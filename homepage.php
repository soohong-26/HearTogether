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
            --text: #ecf2f4;       
            --background: #0a161a;    
            --primary: #87c9e3;      
            --secondary: #127094;     
            --accent: #29bff9;         
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

    
</body>
</html>