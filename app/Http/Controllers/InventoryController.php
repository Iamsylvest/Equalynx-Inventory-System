<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade


class InventoryController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request with additional validation for 'measurement_unit'
        $request->validate([
            'material_name' => 'required|string|max:255',
            'stocks' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:0',
            'measurement_quantity' => 'required|integer|min:0',
            'measurement_unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',  // Valid units
        ]);

        // Try to create the inventory item
        try {
            // Ensure a default value for threshold if needed
            
            $threshold = $request->threshold ?? 0;

            $inventory = Inventory::create([
                'material_name' => $request->material_name,
                'stocks' => $request->stocks,
                'threshold' => $threshold, // Ensure threshold is set
                'measurement_quantity' => intval($request->measurement_quantity),  // Force it to be an integer
                'measurement_unit' => $request->measurement_unit,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inventory item added successfully',
                'inventory' => $inventory,
            ], 201);

        } catch (\Exception $e) {
            // Log the error for better debugging
            Log::error('Error adding inventory: ' . $e->getMessage());

            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
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
                'threshold' => 'required|integer|min:0',
                'measurement_quantity' => 'required|integer|min:0',
                'measurement_unit' => 'required|in:pcs,m,cm,in,kg,g,l,ml',
            ]);
        
            try {
                // Find the inventory item by its ID
                $inventory = Inventory::findOrFail($id);
        
                // Update only the editable fields
                $inventory->update([
                    'stocks' => $request->stocks,
                    'threshold' => $request->threshold,
                    'measurement_quantity' => $request->measurement_quantity,
                    'measurement_unit' => $request->measurement_unit,
                    // 'material_name' remains unchanged
                ]);
        
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
            $inventory = Inventory::find($id);
            if ($inventory) {
                $inventory->delete();
                return response()->json(['message' => 'User deleted successfully.']);
            } else {
                return response()->json(['message' => 'User not found.'], 404);
            }
        }
  
}