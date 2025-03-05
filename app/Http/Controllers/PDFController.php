<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Models\Dr;
use App\Models\Rr;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    public function generatePDF($id)
    {
        try {
            // Fetch the DR data along with materials (limit to 50 materials, adjust if needed)
            $dr = Dr::with('materials')->find($id);
     

            // Log the DR data to see if the materials are being loaded
            Log::debug('DR Data:', ['dr' => $dr]); // Check the entire DR data, including materials
            Log::debug('Materials Names:', ['names' => $dr->materials->pluck('material_name')]); // Log the material names

            // Generate HTML content for the PDF using the 'pdf.delivery_receipt' view
            $htmlContent = view('pdf.delivery_receipt', compact('dr'))->render();
            Log::debug('Generated HTML Content:', ['html' => $htmlContent]); // Check if materials are listed in the generated HTML

            // Try generating the PDF
            return PDF::loadHTML($htmlContent)->inline('delivery_receipt.pdf');
        } catch (\Exception $e) {
            // Log the error for debugging if the PDF generation fails
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
    
    public function generatePDFrr($id)
    {
        try {
            // Fetch RR data with materials relationship (you can add a limit if needed)
            $rr = Rr::with('materials')->find($id);
    
            if (!$rr) {
                Log::warning("RR not found with ID: {$id}");
                return response()->json(['error' => 'Return Receipt not found'], 404);
            }
    
            // Log loaded RR data (consider removing in production if it contains sensitive data)
            Log::debug('RR Data:', ['rr' => $rr->toArray()]);
    
            // Optional - Log only the materials' names to check loading
            Log::debug('Materials Names:', $rr->materials->pluck('material_name')->toArray());
    
            // Render the view into HTML
            $htmlContent = view('pdf.return_receipt', compact('rr'))->render();
    
            // Log the HTML content (can be large, so be careful in production)
            Log::debug('Generated HTML Content Length:', ['length' => strlen($htmlContent)]);
    
            // Generate and return the PDF
            return PDF::loadHTML($htmlContent)->inline('return_receipt.pdf');
    
        } catch (\Exception $e) {
            // Log the error message and stack trace
            Log::error('PDF generation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
}