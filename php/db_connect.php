<?php
// Database connection parameters
$host = "localhost";    // Database host
$username = "root";     // Database username
$password = "Devil25";  // Database password
$database = "mealplanner"; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 