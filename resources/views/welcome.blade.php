<!-- resources/views/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
     <!-- ✅ Add Google Fonts (Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <title>Equalynx App</title>
    @vite('resources/js/app.js')  <!-- Include Vite build -->
    @vite('resources/css/app.css') <!-- Include your CSS -->
</head>
<body>
    <div id="app">
       
    </div> <!-- Vue app will mount here -->
</body>
</html>
