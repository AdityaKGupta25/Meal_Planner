<?php
// Include database connection
require_once 'db_connect.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required";
        header("Location: ../login.php");
        exit();
    }
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect to profile page
            header("Location: ../profile.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Invalid email or password";
            header("Location: ../login.php");
            exit();
        }
    } else {
        // User does not exist
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: ../login.php");
        exit();
    }
}
?> 