<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/forgotten.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="logo">Meal<span>Planner</span></a>
        
        <h3>Forgot Password</h3>
        
        <?php if(isset($_SESSION['forgot_error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['forgot_error']; 
                    unset($_SESSION['forgot_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['reset_message'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['reset_message']; 
                    unset($_SESSION['reset_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <p class="form-description">Enter your email address to receive a password reset link.</p>
        
        <form action="php/forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        
        <div class="login-link">
            Remember your password? <a href="login.php">Log In</a>
        </div>
    </div>
    
    <script src="js/forgotten.js"></script>
</body>
</html> 