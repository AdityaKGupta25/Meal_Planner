<?php
// Database connection parameters
$host = "localhost";    // Database host
$username = "root";     // Database username
$password = "Devil25";  // Database password

// Create connection without database name for creating database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Setup</h2>";

// Read SQL file
$sql_file = file_get_contents("php/setup_database.sql");
$sql_commands = explode(';', $sql_file);

// Execute each SQL command
$error = false;
foreach ($sql_commands as $sql) {
    $sql = trim($sql);
    if (!empty($sql)) {
        if ($conn->query($sql) === TRUE) {
            // Success
        } else {
            echo "<p style='color: red;'>Error executing SQL: " . $conn->error . "</p>";
            echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
            $error = true;
        }
    }
}

if (!$error) {
    echo "<p style='color: green;'>Database setup completed successfully!</p>";
    echo "<p>You can now <a href='signup.php'>sign up</a> or <a href='login.php'>log in</a> using the provided test account:</p>";
    echo "<ul>";
    echo "<li>Email: test@example.com</li>";
    echo "<li>Password: Test1234</li>";
    echo "</ul>";
    echo "<p>Or you can try the <a href='forgotten.php'>forgot password</a> functionality.</p>";
}

// Close connection
$conn->close();
?> 