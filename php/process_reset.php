<?php
// Include database connection
require_once 'db_connect.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $email = $conn->real_escape_string($_POST['email']);
    $token = $conn->real_escape_string($_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Validate inputs
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['reset_error'] = "Both password fields are required";
        header("Location: ../reset_password.php?email=$email&token=$token");
        exit();
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['reset_error'] = "Passwords do not match";
        header("Location: ../reset_password.php?email=$email&token=$token");
        exit();
    }
    
    // Check if password is strong enough
    if (strlen($password) < 8) {
        $_SESSION['reset_error'] = "Password must be at least 8 characters long";
        header("Location: ../reset_password.php?email=$email&token=$token");
        exit();
    }
    
    // Check if token is valid and not expired
    $sql = "SELECT * FROM users WHERE email = '$email' AND reset_token = '$token' AND token_expires > NOW()";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        // Token is valid, update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user password and clear token
        $update_sql = "UPDATE users SET password = '$hashed_password', reset_token = NULL, token_expires = NULL WHERE email = '$email'";
        
        if ($conn->query($update_sql) === TRUE) {
            // Password reset successful
            $_SESSION['login_success'] = "Password reset successful! You can now log in with your new password.";
            header("Location: ../login.php");
            exit();
        } else {
            // Error updating database
            $_SESSION['reset_error'] = "Error: " . $conn->error;
            header("Location: ../reset_password.php?email=$email&token=$token");
            exit();
        }
    } else {
        // Invalid or expired token
        $_SESSION['reset_error'] = "Invalid or expired password reset link";
        header("Location: ../forgotten.php");
        exit();
    }
}
?> 