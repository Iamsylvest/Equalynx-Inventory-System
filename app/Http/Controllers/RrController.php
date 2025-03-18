<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;
use App\Models\Rr;
use Illuminate\Http\Request;
use App\Models\ReturnMaterial;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Events\ActivityLogged;
use App\Events\WarehouseNotification;
use App\Events\AdminNotification;
use App\Events\ProcurementNotification;
use App\Events\ManagerNotification;
use App\Events\highStockUpdated;
use App\Events\BarGraphUpdated;
class RrController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        Log::info('RrController@index invoked');
        try{
            
        $query = Rr::query();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search){
                   $q->where('name', 'like', "%$search%")
                    ->orWhere('rr_number', 'like', "%$search%")
                    ->orWhere('project_name', 'like', "%$search%")
                    ->orWhereHas('approver', function($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
                
        }

        $rrs = $query->with('dr:id,id,dr_number','approver')->paginate(10);
        Log::info('Fetching RRs successful. Total records: ' . $rrs->total());

        return response()->json($rrs);

        }catch(\Exception $e){
            Log::error('Failed to Fetch RRs', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to Fetch Return Receipts'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Received Data:', $request->all());
    
        try {
            // Validate incoming request data
            $request->validate([
                'dr_id' => 'required|integer',  // Ensure DR ID is present
                'name' => 'required|string',
                'project_name' => 'required|string',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'location' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'return_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validate file upload
                'materials.*.material_name' => 'required|string',
                'materials.*.measurement' => 'required|integer|min:1',
                'materials.*.unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid measurement units
                'materials.*.material_quantity' => 'required|integer|min:1',
            ]);
    
            // Generate a unique RR number by getting the latest RR record
            $latestRr = Rr::latest()->first();
            $newRrNumber = $latestRr 
                ? 'RR-' . str_pad($latestRr->id + 1, 6, '0', STR_PAD_LEFT) 
                : 'RR-000001';
    
            // Handle file upload if return_proof is provided
            $returnProofPath = null;
            $returnProofOriginalName = null;  // New field for original filename
            if ($request->hasFile('return_proof')) {
                $file = $request->file('return_proof');
            
                // Store file in the 'public/return_proofs' directory
                $returnProofPath = $file->store('return_proofs', 'public');
            
                // Capture the original file name
                $returnProofOriginalName = $file->getClientOriginalName();
            }
    
            // Create a new RR (Return Receipt) record in the 'rrs' table
            $rr = Rr::create([
                'dr_id' => $request->dr_id,  // Store DR (Delivery Receipt) ID
                'rr_number' => $newRrNumber, // Generated unique RR number
                'name' => $request->name,
                'project_name' => $request->project_name,
                'status' => $request->status,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'remarks' => $request->remarks,
                'return_proof' => $returnProofPath, // Store file path
                'return_proof_original_name' => $returnProofOriginalName, // Store original file name
            ]);
    
            // Log the created RR record for debugging
            Log::info('Created RR:', $rr->toArray());
    
            // Iterate over each material in the request and store in 'return_materials' table
            foreach ($request->materials as $material) {
                Log::info('Inserting Material:', $material); // Log material data for debugging
    
                ReturnMaterial::create([
                    'rr_id' => $rr->id, // Associate with the created RR record
                    'material_name' => $material['material_name'],
                    'measurement' => $material['measurement'], // Material measurement value
                    'unit' => $material['unit'], // Measurement unit (e.g., pcs, m, kg)
                    'material_quantity' => $material['material_quantity'], // Quantity of the material
                ]);
            }
    
        event(new WarehouseNotification([
                'type' => 'rr_created', // ✅ Define the type for handling in the event
                'action' => $rr->rr_number . ' was created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'timestamp' => now()->toDateTimeString(), // ✅ Use consistent format
            ]));

            event(new ManagerNotification([
                'type' => 'rr_created', // ✅ Define the type for handling in the event
                'action' => $rr->rr_number . ' was created by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                'timestamp' => now()->toDateTimeString(), // ✅ Use consistent format
            ]));


        event(new ActivityLogged([
            'action' => $rr->rr_number . ' number ' . ' was created by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                        ' on ' . now()->toDayDateTimeString(),

            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Include role of the performing user
            'timestamp' => now()->toDateTimeString(),
        ]));
                
        // ✅ Write log to a file
        Log::channel('activity')->info(json_encode([
            'action' => $rr->rr_number . ' number ' . ' was created by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Ensure role is logged
            'timestamp' => now()->toDateTimeString(),
        ]));

            // Return a success response with the created RR record and related materials
            return response()->json([
                'success' => true,
                'message' => 'Return Receipt Successfully Created',
                'rr' => $rr->load('materials'), // Load related materials for response
            ], 200);
        } catch (\Exception $e) {
            // Log error message for debugging
            Log::error('Error adding Return Receipt: ' . $e->getMessage());
    
            // Return an error response
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        public function show($id)
        {
            try {
                $rr = Rr::with('materials')->find($id);

                if (!$rr) {
                    return response()->json(['message' => 'Return Receipt Not Found'], 404);
                }
                // Convert return_proof to full URL if it exists
                if ($rr->return_proof) {
                    $rr->return_proof = asset('storage/' . $rr->return_proof);
                }
                // Returning the data as 'item' and 'materials' for consistency
                return response()->json([
                    'item' => $rr,  // Return the RR item
                    'materials' => $rr->materials  // Return related materials if applicable
                ], 200);
            } catch (\Exception $e) {
                Log::error('Error fetching Return Receipt: ' . $e->getMessage());

                return response()->json(['message' => 'Server Error Occurred'], 500);
            }
        }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string',
        'project_name' => 'required|string',
        'status' => 'required|string',
        'remarks' => 'nullable|string',
        'location' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'return_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'materials.*.material_name' => 'required|string',
        'materials.*.measurement' => 'required|integer|min:1',
        'materials.*.unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',
        'materials.*.material_quantity' => 'required|integer|min:1',
    ]);

    try {
        $rr = Rr::findOrFail($id);
        $newStatus = $request->status;


        // Decode materials if they were sent as JSON string (for FormData from Vue)
        $materials = json_decode($request->input('materials'), true);
        if (!is_array($materials)) {
            return response()->json(['error' => 'Invalid materials format'], 400);
        }

        // --- STATUS HANDLING ---
        if ($newStatus === 'approved') {
            if (auth()->check() && auth()->user()->role !== 'manager') {
                return response()->json(['message' => 'You are not authorized to approve this Return Receipt'], 403);
            }

            $originalData = [];

            foreach ($materials as $material) {
                // Find inventory item based on name, measurement, and unit
                $inventoryItem = Inventory::where('material_name', $material['material_name'])
                    ->where('measurement_quantity', $material['measurement'])
                    ->where('measurement_unit', $material['unit'])
                    ->first();

                if (!$inventoryItem) {
                    Log::error("Inventory Item not Found, {$material['material_name']}, Unit: {$material['unit']}");
                    return response()->json([
                        'message' => "Inventory item '{$material['material_name']}' with unit '{$material['unit']}' not found."
                    ], 404);
                }

                
                // ✅ Save the original state of the item before updating
                $original = $inventoryItem->getOriginal();
                $originalData[] = $original;

                // Strictly check the measurement
                if ($inventoryItem->measurement_quantity != $material['measurement']) {
                    Log::warning("Measurement mismatch for {$material['material_name']}. Expected: {$inventoryItem->measurement}, Given: {$material['measurement']}");
                    return response()->json([
                        'message' => "Measurement mismatch for '{$material['material_name']}'. Measurement: {$material['measurement']} {$material['unit']}. Not available/existing in the Inventory Table"
                    ], 422);
                }

                // Ensure stocks is initialized
                if ($inventoryItem->stocks === null) {
                    $inventoryItem->stocks = 0;
                }

                // Add stock
                $inventoryItem->stocks += $material['material_quantity'];
                $inventoryItem->save();

                Log::info("Stock updated for {$inventoryItem->material_name}, New Stock: {$inventoryItem->stocks}");
            }

            // Save approver when approved
            $rr->approved_by = auth()->id();
            $rr->save();  //  This ensures the value is stored in the database


            // ✅ Get the updated inventory data
                $inventoryData = Inventory::all(); 
                $extraInfo = [
                    'updated_by' => auth()->user()->name ?? 'system',
                    'timestamp' => now()->toDateTimeString(),
                    'original_data' => $originalData, // Use collected original data
                ];

                    event(new BarGraphUpdated($inventoryData, $extraInfo));
        }
        

        $this->broadcastHighStock();


        // --- FILE HANDLING ---
        if ($request->hasFile('return_proof')) {
            $file = $request->file('return_proof');
            $returnProofPath = $file->store('return_proofs', 'public');
            $returnProofOriginalName = $file->getClientOriginalName();

            $rr->return_proof = $returnProofPath;
            $rr->return_proof_original_name = $returnProofOriginalName;
        }

        // --- UPDATE RR DETAILS ---
        $rr->update([
            'name' => $request->name,
            'project_name' => $request->project_name,
            'status' => $request->status,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'remarks' => $request->remarks,
        ]);

        // --- MATERIALS HANDLING ---
        $existingMaterialIds = $rr->materials()->pluck('id')->toArray();
        $newMaterialIds = [];

        foreach ($materials as $material) {
            if (!empty($material['id'])) {
                // Update existing material
                $existingMaterial = ReturnMaterial::find($material['id']);
                if ($existingMaterial) {
                    $existingMaterial->update([
                        'material_name' => $material['material_name'],
                        'measurement' => $material['measurement'],
                        'unit' => $material['unit'],
                        'material_quantity' => $material['material_quantity'],
                    ]);
                    $newMaterialIds[] = $existingMaterial->id;
                }
            } else {
                // Create new material
                $newMaterial = ReturnMaterial::create([
                    'rr_id' => $rr->id,
                    'material_name' => $material['material_name'],
                    'measurement' => $material['measurement'],
                    'unit' => $material['unit'],
                    'material_quantity' => $material['material_quantity'],
                ]);
                $newMaterialIds[] = $newMaterial->id;
            }
        }

        // Delete removed materials
        $materialsToDelete = array_diff($existingMaterialIds, $newMaterialIds);
        ReturnMaterial::destroy($materialsToDelete);

       // Fetch the name of the user who approved the DR (if any)
       $rr = Rr::with('approver')->where('id', $id)->first();

        Log::info("RR Data:", ['rr' => $rr]);

      
        if ($rr->status === 'approved' || $rr->status === 'rejected'){
                event(new ActivityLogged([
                    'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                ' on ' . now()->toDayDateTimeString(),

                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Include role of the performing user
                    'timestamp' => now()->toDateTimeString(),
                ]));
                        
                // ✅ Write log to a file
                Log::channel('activity')->info(json_encode([
                    'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Ensure role is logged
                    'timestamp' => now()->toDateTimeString(),
                ]));

                event(new AdminNotification([
                    'type' => 'approved_rr',
                    'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                                'timestamp' => now()->toDateTimeString(),
                ]));

                event(new ProcurementNotification([
                    'type' => 'approved_rr',
                    'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                                'timestamp' => now()->toDateTimeString(),
                ]));

                event(new ManagerNotification([
                    'type' => 'approved_rr',
                    'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                                'timestamp' => now()->toDateTimeString(),
                ]));


    } else {
        event(new ActivityLogged([
            'action' => $rr->rr_number . ' was edited by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                        ' on ' . now()->toDayDateTimeString(),

            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Include role of the performing user
            'timestamp' => now()->toDateTimeString(),
        ]));
                
        // ✅ Write log to a file
        Log::channel('activity')->info(json_encode([
            'action' => $rr->rr_number . ' was edited by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
            'role' => auth()->user()->role, // ✅ Ensure role is logged
            'timestamp' => now()->toDateTimeString(),
        ]));
    }

    if ($rr->status === 'approved' || $rr->status === 'rejected') {
        Log::info('Broadcasting warehouse notification:', [
            'type' => 'approved',
            'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name, 
            'timestamp' => now()->toDateTimeString(),
        ]);
    
        event(new WarehouseNotification([
            'type' => 'approved',
            'action' => $rr->rr_number . ' was ' . $rr->status . ' by ' . 
                        auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name, 
            'timestamp' => now()->toDateTimeString(),
        ]));
    }

        return response()->json([
            'success' => true,
            'message' => 'Return Receipt Successfully Updated',
            'rr' => $rr,
            'approved_by' => $rr->approver
            ? trim("{$rr->approver->first_name} " . ($rr->approver->middle_name ? $rr->approver->middle_name . ' ' : '') . "{$rr->approver->last_name}")
            : 'pending'
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error updating Return Receipt: ' . $e->getMessage());
        return response()->json([
            'error' => 'Server Error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rr = Rr::findOrFail($id);
    
            // Optional: Delete file if there's a proof file (adjust field name if needed)
            if ($rr->return_proof && Storage::exists($rr->return_proof)) {
                Storage::delete($rr->return_proof);
            }
    
            $rr->delete();
    
            Log::info("Return Receipt Deleted Successfully: ID {$id}");

            event(new ActivityLogged([
                'action' => $rr->rr_number . ' number ' . ' was deleted by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                            ' on ' . now()->toDayDateTimeString(),
    
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Include role of the performing user
                'timestamp' => now()->toDateTimeString(),
            ]));
                    
            // ✅ Write log to a file
            Log::channel('activity')->info(json_encode([
                'action' => $rr->rr_number . ' number ' . ' was deleted by ' . 
                            auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                'role' => auth()->user()->role, // ✅ Ensure role is logged
                'timestamp' => now()->toDateTimeString(),
            ]));
    
            return response()->json(['message' => 'Return Receipt Deleted Successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting Return Receipt: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to Delete Return Receipt', 'error' => $e->getMessage()], 500);
        }
    }

    public function broadcastHighStock()
    {
        // Define the high stock threshold
        $highStockThreshold = 50;
    
        // Get all high stock materials
        $highStock = Inventory::where('stocks', '>=', $highStockThreshold)->get();
        $totalMaterials = $highStock->count();
    
        // Prepare additional info for broadcasting
        $extraInfo = [];
        foreach ($highStock as $material) { // Iterate over $highStock, not $totalMaterials
            $extraInfo[] = [
                'type' => 'highStock_materials',
                'action' => $material->material_name . ' ' . $material->stocks . ' ' . $material->measurement_quantity . ' ' . $material->measurement_unit,
                'timestamp' => $material->updated_at->toDateTimeString(),
            ];
        }
                                                                            
        // Broadcasting the event
        broadcast(new highStockUpdated($highStock, $totalMaterials, $extraInfo));
    }

}
