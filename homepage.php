<?php
// To connect to the database
include 'database.php';
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HearTogether</title>
    <!-- CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        /* Strong Commands */
        :root {
            --text: #F9FAFB;       
            --background: #212121;    
            --primary: #3B82F6;      
            --secondary: #10B981;     
            --accent: #F59E0B;         
            --button: #2563EB;         
        }

        * {
            font-family: inherit;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'nav.php'; ?>

    <h1>HearTogether</h1>
    <!-- Add homepage content here -->
</body>
</html>