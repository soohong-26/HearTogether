<?php
    session_start();

    // Database connection parameters
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'heartogether';
 
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        // Log the error to a file
        error_log('Database connection failed: ' . $conn->connect_error);
        die('Connection failed: ' . $conn->connect_error);  // Displaying the error message
    }
?>
 