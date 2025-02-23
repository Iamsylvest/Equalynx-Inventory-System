<?php

namespace App\Http\Controllers;

use App\Models\Dr;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Models\Material;
use App\Models\User;

class DrController extends Controller
{
    public function store(Request $request) {
        try {
            // Validate input
            $request->validate([
                'name' => 'required|string',
                'project_name' => 'required|string',
                'status' => 'required|string',
                'remarks' => 'nullable|string',
                'location' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'materials' => 'required|array|min:1',
                'materials.*.material_name' => 'required|string',
                'materials.*.measurement' => 'required|integer|min:1', // Changed measurement_unit to measurement (integer)
                'materials.*.unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
                'materials.*.material_quantity' => 'required|integer|min:1',
            ]);
    
            // Generate Unique DR Number
            $latestDr = Dr::latest()->first();
            $newDrNumber = $latestDr ? 'DR-' . str_pad($latestDr->id + 1, 6, '0', STR_PAD_LEFT) : 'DR-000001';
    
            // Create DR record
            $dr = Dr::create([
                'dr_number' => $newDrNumber,
                'name' => $request->name,
                'project_name' => $request->project_name,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }
    
        $drs = $query->paginate(10);
    
        return response()->json($drs);
    }

    public function show($id) {
        $dr = Dr::with('materials')->find($id); 
    
        if (!$dr) {
            return response()->json(['message' => 'DR not found'], 404);
        }
    
        return response()->json([
            'item' => $dr,  // This includes the materials already
        ]);
    }

    public function destroy($id) {
        try {
              // Find the inventory item or fail
            $dr = Dr::findorfail($id);

            // Delete the inventory item
            $dr -> delete();

            Log::info("Delivery Receipt Deleted Successfully: ID {$id}");

            return response()->json([ 'message' => 'Delivery Receipt Deleted Successfully']);
        } catch (\Exception $e) {
              // Log the error for debugging
                Log::error('Error Deleting Delivery Receipt' . $e ->getMessage());
                 // Return a 500 error if something went wrong
                 return response()->json(['message' => 'Failed to Delete Delivery Receipt', 'ERROR' => $e->getMessage()], 500);
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
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
                    $inventoryItem = Inventory::where('material_name', $material['material_name'])
                        ->where('measurement_unit', $material['unit'])
                        ->where('measurement_quantity', $material['measurement'])
                        ->first();
    
                    if (!$inventoryItem) {
                        Log::error("Inventory item not found: {$material['material_name']}, Measurement: {$material['measurement']}");
                        return response()->json(['message' => "Inventory item {$material['material_name']} not found"], 404);
                    }
    
                    if ($inventoryItem->stocks === null) {
                        $inventoryItem->stocks = 0; // Set stock to 0 if it's null
                    }
    
                    // Check if there is enough stock to deduct
                    if ($inventoryItem->stocks >= $material['material_quantity']) {
                        $inventoryItem->stocks -= $material['material_quantity'];
                        $inventoryItem->save();
                        Log::info("Stock updated for {$inventoryItem->material_name}, New Stock: {$inventoryItem->stocks}");
                    } else {
                        Log::warning("Not enough stock for {$material['material_name']}. Available: {$inventoryItem->stocks}, Required: {$material['material_quantity']}");
                        return response()->json(['message' => "Not enough stock for {$material['material_name']}"], 400);
                    }    
                }
            }
    
            // Update the DR item status
            $dr->status = $newStatus;
            $dr->save();
    
            // Update other DR fields
            $dr->update([
                'name' => $request->name,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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
            $approver = $dr->approved_by ? User::find($dr->approved_by) : null;
            $approvedName = $approver ? $approver->name : 'Unknown'; // Default to 'Unknown' if no approver found

            return response()->json([
                'success' => true,
                'message' => 'Delivery Receipt updated successfully',
                'dr' => $dr,
                'appover_name' => $approvedName, // Add the approver's name to the response
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Error updating Delivery Receipt: ' . $e->getMessage());
    
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}

