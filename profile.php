<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'php/db_connect.php';

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <a href="index.php" class="logo">Meal<span>Planner</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li class="profile-nav-item">
                        <a href="profile.php" class="profile-link">My Profile</a>
                    </li>
                    <li><a href="php/logout.php" class="profile-link">Logout</a></li>
                </ul>
                <div class="mobile-menu-toggle">☰</div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>My Profile</h1>
            
            <?php if(isset($_SESSION['profile_message'])): ?>
                <div class="message success">
                    <?php 
                        echo $_SESSION['profile_message']; 
                        unset($_SESSION['profile_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['profile_error'])): ?>
                <div class="message error">
                    <?php 
                        echo $_SESSION['profile_error']; 
                        unset($_SESSION['profile_error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-image">
                        <?php
                        $profile_img = 'img/profiles/' . ($user['profile_image'] ? $user['profile_image'] : 'default.jpg');
                        if (!file_exists($profile_img)) {
                            $profile_img = 'img/profiles/default.jpg';
                        }
                        ?>
                        <img src="<?php echo $profile_img; ?>" alt="Profile Picture">
                        
                        <form action="php/upload_image.php" method="POST" enctype="multipart/form-data" id="imageForm">
                            <div class="image-upload">
                                <label for="profile_image" class="profile-link">
                                    <i class="fas fa-camera"></i> Change Photo
                                </label>
                                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                            </div>
                        </form>
                    </div>
                    
                    <div class="sidebar-links">
                        <a href="#" class="active profile-link"><i class="fas fa-user"></i> Profile</a>
                        <a href="meal_plan/" class="profile-link"><i class="fas fa-utensils"></i> My Meal Plans</a>
                        <a href="#" class="profile-link"><i class="fas fa-cog"></i> Settings</a>
                    </div>
                </div>
                
                <div class="profile-content">
                    <h2>Account Information</h2>
                    
                    <div class="profile-info">
                        <div class="info-group">
                            <label>Full Name</label>
                            <p><?php echo htmlspecialchars($user['fullname']); ?></p>
                        </div>
                        
                        <div class="info-group">
                            <label>Email</label>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        
                        <div class="info-group">
                            <label>Member Since</label>
                            <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <a href="#" class="profile-link">Edit Profile</a>
                    
                    <hr>
                    
                    <h2>Personal Information</h2>
                    
                    <div class="profile-info">
                        <div class="info-group">
                            <label>Sex <i class="fas fa-question-circle help-icon" title="Select the option that best aligns with your biological sex for metabolism calculations"></i></label>
                            <div class="option-buttons">
                                <label class="option-label">
                                    <input type="radio" name="sex" value="Male" class="option-radio" checked>
                                    <span class="option-button active">Male</span>
                                </label>
                                <label class="option-label">
                                    <input type="radio" name="sex" value="Female" class="option-radio">
                                    <span class="option-button">Female</span>
                                </label>
                                <label class="option-label">
                                    <input type="radio" name="sex" value="Non-Binary" class="option-radio">
                                    <span class="option-button">Non-Binary</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label>Height</label>
                            <div class="measurement-inputs">
                                <input type="number" value="5" min="1" max="9" class="measurement-input"> <span>ft</span>
                                <input type="number" value="9" min="0" max="11" class="measurement-input"> <span>in</span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label>Weight</label>
                            <div class="measurement-inputs">
                                <input type="number" value="144" min="1" class="measurement-input"> <span>lbs</span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label>Age</label>
                            <div class="measurement-inputs">
                                <input type="number" value="21" min="1" class="measurement-input"> <span>years</span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label>Bodyfat <i class="fas fa-question-circle help-icon" title="Low: Athletic/Visible abs (10-14% male, 15-19% female). Medium: Average (15-24% male, 20-29% female). High: Above average (25%+ male, 30%+ female)"></i></label>
                            <div class="option-buttons">
                                <label class="option-label">
                                    <input type="radio" name="bodyfat" value="Low" class="option-radio">
                                    <span class="option-button">Low</span>
                                </label>
                                <label class="option-label">
                                    <input type="radio" name="bodyfat" value="Medium" class="option-radio" checked>
                                    <span class="option-button active">Medium</span>
                                </label>
                                <label class="option-label">
                                    <input type="radio" name="bodyfat" value="High" class="option-radio">
                                    <span class="option-button">High</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label>Activity Level</label>
                            <div class="dropdown-container">
                                <select class="activity-dropdown">
                                    <option selected>Desk job, light exercise</option>
                                    <option>Moderate exercise (3-5 days/week)</option>
                                    <option>Active job or daily exercise</option>
                                    <option>Very active (hard exercise 6-7 days/week)</option>
                                    <option>Professional athlete</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="profile-link">Save Changes</a>
                    
                    <hr>
                    
                    <h2>My Meal Plan Summary</h2>
                    <p class="empty-state">You haven't created any meal plans yet. <a href="meal_plan/" class="profile-link">Get started</a> with your first plan!</p>
                </div>
            </div>
        </div>
    </main>
    
    <footer style="background-color: #333; color: white; padding: 60px 0 20px; width: 100%; margin-top: 50px; position: relative; left: 0; right: 0;">
        <div class="container" style="display: block; width: 90%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="footer-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 50px;">
                <div class="footer-column" style="padding: 0 15px;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; color: white;">MealPlanner</h3>
                    <p style="margin-bottom: 20px; line-height: 1.6; color: #ccc;">Your personal assistant for meal planning, recipe discovery, and grocery shopping.</p>
                </div>
                <div class="footer-column" style="padding: 0 15px;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; color: white;">Quick Links</h3>
                    <ul class="footer-links" style="list-style: none;">
                        <li style="margin-bottom: 10px;"><a href="index.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Home</a></li>
                        <li style="margin-bottom: 10px;"><a href="recipes.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Recipes</a></li>
                        <li style="margin-bottom: 10px;"><a href="meal_plan/" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Meal Plans</a></li>
                        <li style="margin-bottom: 10px;"><a href="about.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">About</a></li>
                        <li style="margin-bottom: 10px;"><a href="contact.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column" style="padding: 0 15px;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; color: white;">Support</h3>
                    <ul class="footer-links" style="list-style: none;">
                        <li style="margin-bottom: 10px;"><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">FAQs</a></li>
                        <li style="margin-bottom: 10px;"><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Contact Us</a></li>
                        <li style="margin-bottom: 10px;"><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Privacy Policy</a></li>
                        <li style="margin-bottom: 10px;"><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Terms of Service</a></li>
                        <li style="margin-bottom: 10px;"><a href="#" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Help Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom" style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: #ccc; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <p>&copy; 2025 MealPlanner. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="js/profile.js"></script>
</body>
</html> 