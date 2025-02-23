<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Models\Dr;
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
}