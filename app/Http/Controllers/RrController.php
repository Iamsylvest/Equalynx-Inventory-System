<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\Rr;
use Illuminate\Http\Request;
use App\Models\ReturnMaterial;
use Illuminate\Support\Facades\Log; // Import Log facade
class RrController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        Log::info('RrController@index invoked');

        $rrs = Rr::with('dr:id,dr_number')->get();    // Fetch all RR records with related DR data
        return response()->json($rrs); // Return JSON response

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
    
            return response()->json(['message' => 'Return Receipt Deleted Successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting Return Receipt: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to Delete Return Receipt', 'error' => $e->getMessage()], 500);
        }
    }
}
