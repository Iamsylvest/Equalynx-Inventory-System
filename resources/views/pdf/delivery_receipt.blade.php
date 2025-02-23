<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        
        /* Remove borders ONLY from the first two tables */
        .no-border, .no-border td, .no-border th {
            border: none !important;
        }

        /* Keep borders for the materials table */
        .bordered-table, .bordered-table th, .bordered-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .bordered-table th {
            background-color: #365486;
            color: white;
        }
        .bordered-table td {
            font-size: 13px;
        }
        
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>

    <!-- Header Section (No Border) -->
    <table class="no-border" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 60%; ">
                <img src="{{ public_path('image/EqualynxLogo.png') }}" style="width: 300px; height: 200px;" />
            </td>
            <td style="width: 40%; text-align: left; vertical-align: middle;">
                <h1 style="font-size: 40px; font-weight: bold;">Delivery Receipt</h1>
            </td>
        </tr>
    </table>

    <!-- Information Section (No Border) -->
    <table class="no-border" style="font-size: 16px; margin-bottom: 20px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($dr->created_at)->format('F j, Y') }}</p>
                <p><strong>Project Name:</strong> {{ $dr->project_name }}</p>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <p><strong>DR#:</strong> {{ $dr->dr_number }}</p>
                <p><strong>Location:</strong> {{ $dr->location }}</p>
            </td>
        </tr>
    </table>

    <!-- Materials Table (With Borders) -->
    <table class="bordered-table">
        <thead>
            <tr>
                <th>Material Name</th>
                <th>Quantity</th>
                <th>Measurement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dr->materials as $material)
            <tr>
                <td>{{ $material->material_name }}</td>
                <td>{{ $material->material_quantity }}</td>
                <td>{{ $material->measurement }} {{ $material->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <p class="footer">Approved By: _____________________</p>
    <p class="footer">Signature over Printed Name</p>

</body>
</html>