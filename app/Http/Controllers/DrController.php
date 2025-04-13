<?php

namespace App\Http\Controllers;

use App\Models\TransactionLog;
use App\Models\Dr;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Models\Material;
use App\Events\ActivityLogged;
use App\Events\AdminNotification;
use App\Events\ProcurementNotification;
use App\Events\ManagerNotification;
use App\Events\LowstockUpdated;


class DrController extends Controller

{
    public function destroyTransactionlog($id)
    {
        try {
            // Find the log by ID
            $log = TransactionLog::findOrFail($id);
            
            // Delete the log
            $log->delete();
            
            return response()->json(['message' => 'Log deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting transaction log: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete log'], 500);
        }
    }


    public function logTransaction($dr)
        {
            TransactionLog::create([
                'action' => 'New ' . $dr->dr_number . ' was created by ' . auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . ' for project ' . $dr->project_name . ' at ' . $dr->location . ' on ' . now()->toDayDateTimeString(),
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role,
                'timestamp' => now()->toDateTimeString(),
                'transaction_details' => json_encode([
                    'delivery_receipt' => [
                        'dr_number' => $dr->dr_number,
                        'status' => $dr->status,
                        'remarks' => $dr->remarks,
                        'location' => $dr->location,
                    ],
                    'materials' => $dr->materials->map(function ($material) {
                        return [
                            'material_name' => $material->material_name,
                            'measurement' => $material->measurement,
                            'unit' => $material->unit,
                            'quantity' => $material->material_quantity,
                        ];
                    }),
                ]),
            ]);
        }
        
        public function logEditTransaction($oldDr, $newDr)
        {
            $user = auth()->user();
            $fullName = trim("{$user->first_name} {$user->middle_name} {$user->last_name}");
        
            $changes = [];
        
            // Compare top-level DR fields
            $fieldsToCheck = ['name', 'project_name', 'status', 'remarks', 'location'];
            foreach ($fieldsToCheck as $field) {
                if ($oldDr->$field !== $newDr->$field) {
                    $changes[] = "$field changed from '{$oldDr->$field}' to '{$newDr->$field}'";
                }
            }
        
            // Load materials for comparison
            $oldMaterials = $oldDr->materials->keyBy('id');
            $newMaterials = $newDr->materials->keyBy('id');
        
            // Detect added or modified materials
            foreach ($newMaterials as $id => $material) {
                if (!isset($oldMaterials[$id])) {
                    $changes[] = "added new material '{$material->material_name}' with quantity {$material->material_quantity}";
                    continue;
                }
        
                $oldMaterial = $oldMaterials[$id];
                if ($oldMaterial->material_name !== $material->material_name) {
                    $changes[] = "material name changed from '{$oldMaterial->material_name}' to '{$material->material_name}'";
                }
                if ($oldMaterial->measurement !== $material->measurement) {
                    $changes[] = "measurement of '{$material->material_name}' changed from '{$oldMaterial->measurement}' to '{$material->measurement}'";
                }
                if ($oldMaterial->unit !== $material->unit) {
                    $changes[] = "unit of '{$material->material_name}' changed from '{$oldMaterial->unit}' to '{$material->unit}'";
                }
                if ($oldMaterial->material_quantity !== $material->material_quantity) {
                    $changes[] = "quantity of '{$material->material_name}' changed from {$oldMaterial->material_quantity} to {$material->material_quantity}";
                }
            }
        
            // Detect removed materials
            foreach ($oldMaterials as $id => $material) {
                if (!isset($newMaterials[$id])) {
                    $changes[] = "removed material '{$material->material_name}'";
                }
            }
        
            // Generate action description
            $action = $changes
                ? "{$newDr->dr_number} was edited by $fullName — " . implode(', ', $changes)
                : "{$newDr->dr_number} was edited by $fullName but no major changes were made";
        
            // Save to transaction log
            TransactionLog::create([
                'action' => $action,
                'performed_by' => $fullName,
                'role' => $user->role,
                'timestamp' => now()->toDateTimeString(),
                'transaction_details' => json_encode([
                    'delivery_receipt' => [
                        'dr_number' => $newDr->dr_number,
                        'status' => $newDr->status,
                        'remarks' => $newDr->remarks,
                        'location' => $newDr->location,
                    ],
                    'materials' => $newDr->materials->map(function ($m) {
                        return [
                            'material_name' => $m->material_name,
                            'measurement' => $m->measurement,
                            'unit' => $m->unit,
                            'quantity' => $m->material_quantity,
                        ];
                    }),
                ]),
            ]);
        }

        public function getTransactionLogs(Request $request)
        {
            try {
                $logs = TransactionLog::latest()->paginate(10);
                return response()->json($logs);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

    public function store(Request $request) {
        try {
            // Validate input
            $request->validate([
                'name' => 'required|string',
                'project_name' => 'required|string',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'location' => 'required|string',
                'materials' => 'required|array|min:1',
                'materials.*.material_name' => 'required|string',
                'materials.*.measurement' => 'required|integer|min:1', // Changed measurement_unit to measurement (integer)
                'materials.*.unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
                'materials.*.material_quantity' => 'required|integer|min:1',
            ]);
    
          
            // Get the latest DR record, including soft-deleted ones
            $latestDr = Dr::withTrashed()->latest('id')->first();  
          // Generate a new DR number based on the latest ID or start from DR-000001 if none exists
            $newDrNumber = $latestDr ? 'DR-' . str_pad($latestDr->id + 1, 6, '0', STR_PAD_LEFT) : 'DR-000001';
    
            // Create DR record
            $dr = Dr::create([
                'dr_number' => $newDrNumber,
                'name' => $request->name,
                'project_name' => $request->project_name,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'location' => $request->location,
            ]);
    
                    // Insert materials linked to this DR
            foreach ($request->materials as $material) {
                Material::create([
                    'dr_id' => $dr->id,
                    'material_name' => $material['material_name'],
                    'measurement' => $material['measurement'], // Updated field name
                    'unit' => $material['unit'], // New field
                    'material_quantity' => $material['material_quantity'],
                ]);
            }

                // ✅ Call logTransaction to save to transaction_logs table
            $this->logTransaction($dr);

            event(new ActivityLogged([
                'action' => 'New ' . $dr->dr_number . ' was created by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                            ' on ' . now()->toDayDateTimeString(),
    
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => 'New ' . $dr->dr_number . ' was created by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));

            event(new AdminNotification([
                'type' => 'new_dr',
                'action' => 'New ' . $dr->dr_number . ' was created by ' .   auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name
                . ' ' . ' status ' . $dr->status . '.',
                'timestamp' => now()->toDateTimeLocalString(),
            ]));

            event(new ManagerNotification([
                'type' => 'new_dr',
                'action' => 'New ' . $dr->dr_number . ' was created by ' .   auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name
                . ' ' . ' status ' . $dr->status . '.',
                'timestamp' => now()->toDateTimeLocalString(),
            ]));


    
                
            return response()->json([
                'success' => true,
                'message' => 'Delivery Receipt successfully created.',
                'dr' => $dr->load('materials'), // Load related materials
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Error adding delivery receipt: ' . $e->getMessage());
    
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function index(Request $request)
    {
        $query = Dr::query();

        Log::info('DrController@index invoked');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
    
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                 ->orWhere('dr_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhereHas('approver', function($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                          ->orWhere('middle_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                });
                  
            });
        }
    
        $drs = $query->with('approver')->paginate(10);
    
        return response()->json($drs);
    }

    public function show($id) {
        $dr = Dr::withTrashed()->with('materials')->find($id); 
    
        if (!$dr) {
            return response()->json(['message' => 'DR not found'], 404);
        }
    
        return response()->json([
            'item' => $dr,  // This includes the materials already
            'materials' => $dr->materials ?? [],
        ]);
    }

    public function destroy($id) {
        try {
              // Find the inventory item or fail
            $dr = Dr::findorfail($id);

            // Delete the inventory item
            $dr -> delete();

            Log::info("Delivery Receipt Archived Successfully: ID {$id}");


            event(new ActivityLogged([
                'action' => $dr->dr_number. ' number ' . ' was archived by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                            ' on ' . now()->toDayDateTimeString(),
    
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => $dr->dr_number. ' number ' . ' was archived by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));

            return response()->json([ 'message' => 'Delivery Receipt Archived Successfully']);
        } catch (\Exception $e) {
              // Log the error for debugging
                Log::error('Error Deleting Delivery Receipt' . $e ->getMessage());
                 // Return a 500 error if something went wrong
                 return response()->json(['message' => 'Failed to Archived Delivery Receipt', 'ERROR' => $e->getMessage()], 500);
        }
    }

    public function archived(Request $request){
        Log::info('DrController@index invoked');
 
    
         $query = Dr::onlyTrashed()->with('approver');

         if ($request->has('status')) {
             $query->where('status', $request->input('status'));
         }
     
         if ($request->has('created_at')) {
             $query->whereDate('created_at', $request->input('created_at'));
         }
     
         if ($request->has('search')) {
             $search = $request->input('search');
             $query->where(function($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                  ->orWhere('dr_number', 'like', "%{$search}%")
                   ->orWhere('project_name', 'like', "%{$search}%")
                   ->orWhereHas('approver', function($query) use ($search) {
                     $query->where('first_name', 'like', "%{$search}%")
                           ->orWhere('middle_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                 });
                   
             });
         }
        // ✅ Use pagination for better performance
         $archivedDrs = $query->paginate(10);
    
         return response()->json($archivedDrs);
    }
    
    public function restore($id) {
        $dr = Dr::onlyTrashed()->findOrFail($id); // find the archived dr
        $dr->restore(); // restore the dr
    
        Log::info("Delivery Receipt Restored Successfully: ID {$id}");
    
        // Fire the event before returning the response
        event(new ActivityLogged([
            'action' => $dr->dr_number . ' number ' . ' was Restored by ' . 
                       auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                       ' on ' . now()->toDayDateTimeString(),
            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, 
            'timestamp' => now()->toDateTimeString(),
        ]));
    
        return response()->json(['message' => 'Delivery receipt restored successfully']);
    }
    

       
   public function forceDelete($id){
        try{
            $dr = Dr::withTrashed()->findOrFail($id);
            $dr->forceDelete();



            event(new ActivityLogged([
                'action' => $dr->dr_number. ' number ' . ' was deleted permanently by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                            ' on ' . now()->toDayDateTimeString(),
    
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));

            return response()->json(['message' => 'Delivery Receipt deleted permanently']);

        } catch(\Exception $e){
            return response()->json(['message' => 'Failed to Delete Delivery Reciept', 'error' =>$e->getMessage()], 500);
        }

}





    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'project_name' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'location' => 'required|string',
            'materials' => 'required|array|min:1',
            'materials.*.material_name' => 'required|string',
            'materials.*.measurement' => 'required|integer|min:1', // Changed measurement_unit to measurement (integer)
            'materials.*.unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
            'materials.*.material_quantity' => 'required|integer|min:1',
        ]);
      
        try {
            // Find the DR item or fail
            $dr = Dr::findOrFail($id);
    
            // Check if the status is changing and if it is 'approved'
            $newStatus = $request->status;

                    // If status is 'approved', perform stock deductions
            if ($newStatus === 'approved') {
                if (auth()->check() && auth()->user()->role !== 'manager') {
                    return response()->json(['message' => 'You are not authorized to approve this delivery receipt'], 403);
                }
              

                    
                foreach ($request->materials as $material) {
                    // Find the inventory item based on name, unit, and measurement quantity
                    $inventoryItem = Inventory::where('material_name', $material['material_name'])
                                                ->where('measurement_quantity', $material['measurement']) // Include this!
                                                ->where('measurement_unit', $material['unit'])
                                                ->first();

                    // If no matching material with the same name & unit exists, return error
                    if (!$inventoryItem) {
                        Log::error("Inventory item not found: {$material['material_name']}, Unit: {$material['unit']}");
                        return response()->json([
                            'message' => "Inventory item '{$material['material_name']}' with unit '{$material['unit']}' not found."
                        ], 404);
                    }

                    // ✅ Strictly enforce that the measurement quantity must match exactly
                    if ($inventoryItem->measurement_quantity != $material['measurement']) {
                        Log::warning("Measurement mismatch for {$material['material_name']}. Expected: {$inventoryItem->measurement_quantity}, Given: {$material['measurement']}");
                        return response()->json([
                            'message' => "Measurement mismatch for '{$material['material_name']}'. Measurement: {$material['measurement']} {$material['unit']}. Not available/existing in the Inventory Table "
                        ], 422);
                    }

                    // Ensure stock is set before checking availability
                    if ($inventoryItem->stocks === null) {
                        $inventoryItem->stocks = 0; // Prevent null values from causing issues
                    }

                    // Check if there's enough stock to deduct
                    if ($inventoryItem->stocks < $material['material_quantity']) {
                        Log::warning("Not enough stock for {$material['material_name']}. Available: {$inventoryItem->stocks}, Required: {$material['material_quantity']}");
                        return response()->json([
                            'message' => "Not enough stock for '{$material['material_name']}'. Available: {$inventoryItem->stocks}, Required: {$material['material_quantity']}."
                        ], 400);
                    }

                    // Deduct stock and save
                    $inventoryItem->stocks -= $material['material_quantity'];
                    $inventoryItem->save();

                    Log::info("Stock updated for {$inventoryItem->material_name}, New Stock: {$inventoryItem->stocks}");
                }
            }

            // calll the function to braodcast low stock materials
            $this->broadcastLowStock();

            $originalDr = Dr::with('materials')->find($dr->id); // get full original data with materials
            // Update the DR item status
            $dr->approved_by = auth()->id();  // Save user ID
            $dr->status = $newStatus;
            $dr->save();
    
            // Update other DR fields
            $dr->update([
                'name' => $request->name,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
                'location' => $request->location,
            ]);
    
            // Get the current materials in the database for this DR
            $existingMaterials = $dr->materials()->pluck('id')->toArray();
    
            // Get the updated materials from the request
            $updatedMaterials = collect($request->materials)->pluck('id')->toArray();
    
            // 🗑 Find materials that were removed
            $toDelete = array_diff($existingMaterials, $updatedMaterials);
    
            // Delete removed materials from the database
            if (!empty($toDelete)) {
                Material::whereIn('id', $toDelete)->delete();
            }
    
            // Update or create the materials
            foreach ($request->materials as $material) {
                Material::updateOrCreate(
                    ['id' => $material['id'] ?? null], // Check if the material has an ID (existing or new)
                    [
                        'dr_id' => $dr->id,
                        'material_name' => $material['material_name'],
                        'measurement' => $material['measurement'],
                        'unit' => $material['unit'],
                        'material_quantity' => $material['material_quantity'],
                    ]
                );
            }

            // Fetch the name of the user who approved the DR (if any)
            $dr = Dr::with('approver')->where('id', $id)->first();


          // ✅ Call logTransaction to save to transaction_logs table
            $this->logEditTransaction($dr, $originalDr);


                if ($dr->status === 'approved' ||  $dr->status === 'rejected') {
                    event(new ActivityLogged([
                        'action' => $dr->dr_number. ' number ' . ' was ' . $dr->status . ' by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                    ' on ' . now()->toDayDateTimeString(),
            
                        'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'role' => auth()->user()->role, // ✅ Include role of the performing user
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                            
                    // ✅ Write log to a file
                    Log::channel('activity')->info(json_encode([
                        'action' => $dr->dr_number. ' number ' . ' was ' . $dr->status . ' by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'role' => auth()->user()->role, // ✅ Ensure role is logged
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                          // NOTIFY ADMIN ACCOUNT
                    event(new AdminNotification([
                        'type' => 'approved_dr',
                        'action' => $dr->dr_number .  ' was ' . $dr->status . ' by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . '.',
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                         // NOTIFY PROCUREMENT ACCOUNT
                    event(new ProcurementNotification([
                        'type' => 'approved_dr',
                        'action' => $dr->dr_number .  ' was ' . $dr->status . ' by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . '.',
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                         // NOTIFY MANAGER ACCOUNT
                    event(new ManagerNotification([
                        'type' => 'approved_dr',
                        'action' => $dr->dr_number .  ' was ' . $dr->status . ' by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . '.',
                        'timestamp' => now()->toDateTimeString(),
                    ]));
        
                } else {
                    event(new ActivityLogged([
                        'action' => $dr->dr_number. ' number ' . ' was edited by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                    ' on ' . now()->toDayDateTimeString(),
            
                        'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'role' => auth()->user()->role, // ✅ Include role of the performing user
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                            
                    // ✅ Write log to a file
                    Log::channel('activity')->info(json_encode([
                        'action' => $dr->dr_number. ' number ' . ' was edited by ' . 
                                    auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                        'role' => auth()->user()->role, // ✅ Ensure role is logged
                        'timestamp' => now()->toDateTimeString(),
                    ]));
                }



            Log::info("DR Data:", ['dr' => $dr]);
            return response()->json([
                'success' => true,
                'message' => 'Delivery Receipt updated successfully',
                'dr' => $dr,
                'approved_by' => $dr->approver 
                ? trim("{$dr->approver->first_name} {$dr->approver->middle_name} {$dr->approver->last_name}") 
                : 'pending'
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Error updating Delivery Receipt: ' . $e->getMessage());
    
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    

    public function broadcastLowStock(){
        // Get all materials with low stock based on their threshold
        $lowStockMaterials = Inventory::whereRaw('stocks <= threshold')->get();
        $totalLowStock = $lowStockMaterials->count();
    
        $extraInfo = [];
        foreach ($lowStockMaterials as $material) {
            $extraInfo[] = [
                'type' => 'lowStock_materials',
                'action' => $material->material_name . ' ' . $material->stocks . ' ' . $material->unit,
                'timestamp' => $material->updated_at->toDateTimeString(),
            ];
        }
    
        broadcast(new LowstockUpdated($totalLowStock, $lowStockMaterials, $extraInfo));
    }
  
}

