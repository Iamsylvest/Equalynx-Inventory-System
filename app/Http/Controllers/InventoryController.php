<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Events\ActivityLogged;
use App\Events\WarehouseNotification;

class InventoryController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request with additional validation for 'measurement_unit'
        $request->validate([
            'material_name' => 'required|string|max:255',
            'stocks' => 'required|integer|min:0',
            'measurement_quantity' => 'required|integer|min:0',
            'measurement_unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
        ]);
    
        try {
            // Check if material already exists with the same name and measurement unit
            $inventory = Inventory::where('material_name', $request->material_name)
                                  ->where('measurement_unit', $request->measurement_unit)
                                  ->first();
    
            if ($inventory) {
                // If the material exists, check the measurement_quantity
                if ($inventory->measurement_quantity == $request->measurement_quantity) {
                    // If the material quantity is the same, suggest editing it
                    return response()->json([
                        'success' => false,
                        'message' => 'This material already exists with the same quantity. Please edit the existing material.',
                    ], 400);
                } else {
                    // If the material quantity is different, allow adding the new material
                    $inventory = Inventory::create([
                        'material_name' => $request->material_name,
                        'stocks' => $request->stocks,
                        'measurement_quantity' => $request->measurement_quantity,
                        'measurement_unit' => $request->measurement_unit,
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'New inventory added successfully.',
                        'inventory' => $inventory,
                    ], 201);
                }
            } else {
                // If material doesn't exist, create a new inventory record
                $inventory = Inventory::create([
                    'material_name' => $request->material_name,
                    'stocks' => $request->stocks,
                    'measurement_quantity' => $request->measurement_quantity,
                    'measurement_unit' => $request->measurement_unit,
                ]);


                
            
                event(new ActivityLogged([
                    'action' => $inventory->stocks . $inventory->material_name . '" was added by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                ' on ' . now()->toDayDateTimeString(),
                
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Include role of the performing user
                    'timestamp' => now()->toDateTimeString(),
                ]));
                
                    
                Log::channel('activity')->info(json_encode([
                    'action' => $inventory->stocks . ' ' . $inventory->material_name . '" was added by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Ensure role is logged
                    'timestamp' => now()->toDateTimeString(),
                ]));


                if ($inventory->stocks >= 50) {
                    event(new WarehouseNotification([
                        'type' => 'high_stock', // ✅ Define the type for handling in the event
                        'action' => $inventory->material_name . ' is overstocked (' . $inventory->stocks . ' remaining).',
                        'timestamp' => now()->toDateTimeString(), // ✅ Use consistent format
                    ]));
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Inventory added successfully.',
                    'inventory' => $inventory,
                ], 201);
            }
    
        } catch (\Exception $e) {
            Log::error('Error adding/updating inventory: ' . $e->getMessage());
    
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function checkMaterial(Request $request)
    {
        // Validate the incoming request to ensure necessary data
        $request->validate([
            'material_name' => 'required|string|max:255',
            'measurement_quantity' => 'required|integer|min:0',
            'measurement_unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
        ]);
    
        // Check if material already exists with the same name, quantity, and measurement unit
        $inventory = Inventory::where('material_name', $request->material_name)
                              ->where('measurement_quantity', $request->measurement_quantity)
                              ->where('measurement_unit', $request->measurement_unit)
                              ->exists();
    
        return response()->json(['exists' => $inventory]);
    }




    public function index(Request $request)
    {
        $query = Inventory::query();

         // Search filter for material_name
        if ($request->has('search') && $request->search !== "") {
            $query->where('material_name', 'like', '%' . $request->search . '%');
        }
        
        // Handle stock level filter
        if ($request->has('stocks') && $request->stocks !== "") {
            $stockLevel = $request->stocks;
            if ($stockLevel === "Low") {
                $query->whereRaw("stocks <= 20");
            } elseif ($stockLevel === "Normal") {
                $query->whereRaw("stocks > 20 AND stocks <= 40");
            } elseif ($stockLevel === "High") {
                $query->whereRaw("stocks > 40");
            }
        }
    
        if ($request->has('created_at') && $request->created_at !== "") {
            $query->whereDate('created_at', '=', $request->created_at);
        }
        
        if ($request->has('updated_at') && $request->updated_at !== "") {
            $query->whereDate('updated_at', '=', $request->updated_at);
        }
    
        // Paginate results
        $materials = $query->paginate(10);
    
        // Log the final SQL query (for debugging)
        Log::info('SQL Query:', ['query' => $query->toSql()]);
    
        // Return the response with pagination data
        return response()->json([
            'total' => $materials->total(),
            'data' => $materials->items(),
            'current_page' => $materials->currentPage(),
            'last_page' => $materials->lastPage(),
            'per_page' => $materials->perPage(),
        ]);
    }


    public function update(Request $request, $id)
        {
            // Validate incoming request for only editable fields
            $request->validate([
                'stocks' => 'required|integer|min:0',
                'measurement_quantity' => 'required|integer|min:0',
                'measurement_unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',
            ]);
        
            try {
                // Find the inventory item by its ID
                $inventory = Inventory::findOrFail($id);
        
                // Update only the editable fields
                $inventory->update([
                    'stocks' => $request->stocks,
                    'measurement_quantity' => $request->measurement_quantity,
                    'measurement_unit' => $request->measurement_unit,
                    // 'material_name' remains unchanged
                ]);

                event(new ActivityLogged([
                    'action' => 'Material ' . $inventory->material_name . ' was edited by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                ' on ' . now()->toDayDateTimeString(),
                
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Include role of the performing user
                    'timestamp' => now()->toDateTimeString(),
                ]));
                
                    
                Log::channel('activity')->info(json_encode([
                    'action' => 'Material ' .  $inventory->material_name . ' was edited by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Ensure role is logged
                    'timestamp' => now()->toDateTimeString(),
                ]));
                

                
                if ($inventory->stocks <= 30 ) {
                    event(new WarehouseNotification([
                        'type' => 'low_stock',
                        'action' => $inventory->material_name . ' is low stocked (' . $inventory->stocks . ' remaining).', 'timestamp' => now()->toDayDateTimeString(),
                    ]));
                    } elseif ($inventory->stocks >= 50){
                        event(new WarehouseNotification([
                            'type' => 'high_stock', // ✅ Define the type for handling in the event
                            'action' => $inventory->material_name . ' is overstocked (' . $inventory->stocks . ' remaining).',
                            'timestamp' => now()->toDateTimeString(), // ✅ Use consistent format
                        ]));
                    }

                return response()->json([
                    'success' => true,
                    'message' => 'Inventory item updated successfully',
                    'inventory' => $inventory,
                ], 200);
        
            } catch (\Exception $e) {
                // Log the error for better debugging
                Log::error('Error updating inventory: ' . $e->getMessage());
        
                return response()->json([
                    'error' => 'Server Error',
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        public function destroy($id)
        {
            try {
                // Find the inventory item or fail
                $inventory = Inventory::findOrFail($id);
                
                // Delete the inventory item
                $inventory->delete();
        
                // Log the deletion action
                Log::info("Material deleted successfully: ID {$id}");

                event(new ActivityLogged([
                    'action' => 'Material ' . $inventory->material_name . ' was deleted by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name . 
                                ' on ' . now()->toDayDateTimeString(),
                
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Include role of the performing user
                    'timestamp' => now()->toDateTimeString(),
                ]));
                
                    
                Log::channel('activity')->info(json_encode([
                    'action' => 'Material ' .  $inventory->material_name . ' was deleted by ' . 
                                auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'performed_by' => auth()->user()->first_name . ' ' . (auth()->user()->middle_name ?? '') . ' ' . auth()->user()->last_name,
                    'role' => auth()->user()->role, // ✅ Ensure role is logged
                    'timestamp' => now()->toDateTimeString(),
                ]));
        
                return response()->json(['message' => 'Material deleted successfully']);
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Error deleting material: ' . $e->getMessage());
        
                // Return a 500 error if something went wrong
                return response()->json(['message' => 'Failed to delete material', 'error' => $e->getMessage()], 500);
            }
        }
}