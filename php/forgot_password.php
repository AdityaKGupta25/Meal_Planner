<?php
// Include database connection
require_once 'db_connect.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $email = $conn->real_escape_string($_POST['email']);
    
    // Validate inputs
    if (empty($email)) {
        $_SESSION['forgot_error'] = "Email is required";
        header("Location: ../forgotten.php");
        exit();
    }
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        // User exists, generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // Store token in database
        $update_sql = "UPDATE users SET reset_token = '$token', token_expires = '$expires' WHERE email = '$email'";
        
        if ($conn->query($update_sql) === TRUE) {
            // For this example, we'll just show the token
            // In a real application, you would send this in an email
            $_SESSION['reset_message'] = "Password reset link has been sent to your email. 
                                         For this demo, here's your reset token: $token";
            header("Location: ../reset_password.php?email=$email&token=$token");
            exit();
        } else {
            // Error updating database
            $_SESSION['forgot_error'] = "Error: " . $conn->error;
            header("Location: ../forgotten.php");
            exit();
        }
    } else {
        // For security reasons, don't reveal that the email doesn't exist
        $_SESSION['reset_message'] = "If your email is registered, you will receive a password reset link.";
        header("Location: ../forgotten.php");
        exit();
    }
}
?> 