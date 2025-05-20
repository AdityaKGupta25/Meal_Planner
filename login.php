<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="logo">Meal<span>Planner</span></a>
        
        <h3>Welcome Back</h3>
        
        <?php if(isset($_SESSION['login_error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['login_error']; 
                    unset($_SESSION['login_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['login_success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['login_success']; 
                    unset($_SESSION['login_success']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="php/login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="forgot-password">
                <a href="forgotten.php">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn">Log In</button>
        </form>
        
        <div class="divider">
            <span>OR</span>
        </div>
        
        <button class="btn btn-google">
            <i class="fab fa-google"></i> Log in with Google
        </button>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>
    
    <script src="js/login.js"></script>
</body>
</html> 