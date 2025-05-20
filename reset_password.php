<?php
session_start();

// Check if email and token are provided
if (!isset($_GET['email']) || !isset($_GET['token'])) {
    header("Location: forgetten.html");
    exit();
}

$email = $_GET['email'];
$token = $_GET['token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="container">
        <a href="index.html" class="logo">Meal<span>Planner</span></a>
        
        <h3>Reset Your Password</h3>
        
        <?php if(isset($_SESSION['reset_error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['reset_error']; 
                    unset($_SESSION['reset_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="php/process_reset.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            
            <button type="submit" class="btn">Reset Password</button>
        </form>
        
        <div class="login-link">
            Remember your password? <a href="login.html">Log In</a>
        </div>
    </div>
    
    <script src="js/reset.js"></script>
</body>
</html> 