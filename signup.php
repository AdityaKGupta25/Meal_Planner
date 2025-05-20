<?php
// Include error handler
require_once 'php/error_handler.php';

// Start session
session_start();

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="logo">Meal<span>Planner</span></a>
        
        <h3>Create Your Account</h3>
        
        <?php if(isset($_SESSION['signup_errors'])): ?>
            <div class="error-message">
                <?php 
                    foreach($_SESSION['signup_errors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    unset($_SESSION['signup_errors']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="php/signup.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            
            <button type="submit" class="btn">Sign Up</button>
        </form>
        
        <div class="divider">
            <span>OR</span>
        </div>
        
        <button class="btn btn-google">
            <i class="fab fa-google"></i> Sign up with Google
        </button>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Log In</a>
        </div>
        
        <div class="terms">
            By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
        </div>
    </div>
    
    <script src="js/signup.js"></script>
    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return false;
        }
        
        if (password.length < 8) {
            alert('Password must be at least 8 characters long!');
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html> 