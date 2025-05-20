<?php
// Include database connection
require_once 'db_connect.php';

// If connection is successful (no error was thrown), display success message
echo "<h2>Database Connection Test</h2>";
echo "<p style='color: green;'>Database connection successful!</p>";

// Display PHP and MySQL information
echo "<h3>Server Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>MySQL Version: " . $conn->server_info . "</p>";

// Close the connection
$conn->close();
?> 