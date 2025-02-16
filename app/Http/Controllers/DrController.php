<?php

namespace App\Http\Controllers;

use App\Models\Dr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Models\Material;

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
                'materials.*.measurement_unit' => 'required|string',
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
                    'measurement_unit' => $material['measurement_unit'],
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
    
    public function index() {
        return response()->json(Dr::all());
    }

    public function show($id) {
        $dr = Dr::with('materials')->find($id); 
    
        if (!$dr) {
            return response()->json(['message' => 'DR not found'], 404);
        }
    
        return response()->json([
            'item' => $dr,  
            'materials' => $dr->materials ?? []
        ]);
    }
}

