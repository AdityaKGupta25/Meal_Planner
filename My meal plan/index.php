<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once '../php/db_connect.php';

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealPlanner - Meal Planning</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/meal_plan/style.css?v=<?php echo time(); ?>">
    <script src="../js/meal_plan/script.js" defer></script>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="navbar">
                <a href="../index.php" class="logo">Meal<span>Planner</span></a>
                <ul class="nav-links">
                    <li class="profile-nav-item">
                        <a href="../profile.php" class="profile-link">My Profile</a>
                    </li>
                    <li><a href="../php/logout.php" class="profile-link">Logout</a></li>
                </ul>
                <div class="mobile-menu-toggle">☰</div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <h1 class="section-title">Create Your Meal Plan</h1>

            <!-- Preferences Form -->
            <div class="form-card">
                <h2 class="form-subtitle">Your Preferences</h2>
                <form id="meal-preferences-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories">Daily Calories:</label>
                            <input type="number" id="calories" name="calories" value="2000" min="1200" max="4000">
                            <div class="range-slider">
                                <input type="range" id="calories-slider" min="1200" max="4000" value="2000" step="50">
                                <div class="range-values">
                                    <span>1200</span>
                                    <span>4000</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meals-per-day">Meals Per Day:</label>
                            <select id="meals-per-day" name="meals-per-day">
                                <option value="3">3 meals</option>
                                <option value="4">4 meals</option>
                                <option value="5">5 meals</option>
                                <option value="6">6 meals</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="plan-days">Plan For:</label>
                            <select id="plan-days" name="plan-days">
                                <option value="1">1 day</option>
                                <option value="3">3 days</option>
                                <option value="7" selected>1 week</option>
                                <option value="14">2 weeks</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Diet Type:</label>
                            <select id="diet-type" name="diet-type">
                                <option value="any">Any</option>
                                <option value="vegetarian">Vegetarian</option>
                                <option value="vegan">Vegan</option>
                                <option value="paleo">Paleo</option>
                                <option value="keto">Keto</option>
                                <option value="mediterranean">Mediterranean</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Macro Distribution:</label>
                            <div id="macro-sliders">
                                <div>
                                    <label>Protein: <span id="protein-value">30</span>%</label>
                                    <div class="range-slider">
                                        <input type="range" id="protein-slider" min="10" max="60" value="30" step="5">
                                    </div>
                                </div>
                                <div>
                                    <label>Carbs: <span id="carbs-value">40</span>%</label>
                                    <div class="range-slider">
                                        <input type="range" id="carbs-slider" min="10" max="60" value="40" step="5">
                                    </div>
                                </div>
                                <div>
                                    <label>Fat: <span id="fat-value">30</span>%</label>
                                    <div class="range-slider">
                                        <input type="range" id="fat-slider" min="10" max="60" value="30" step="5">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Food Exclusions:</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-dairy" name="exclusions" value="dairy">
                                    <label for="no-dairy">Dairy</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-gluten" name="exclusions" value="gluten">
                                    <label for="no-gluten">Gluten</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-nuts" name="exclusions" value="nuts">
                                    <label for="no-nuts">Nuts</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-shellfish" name="exclusions" value="shellfish">
                                    <label for="no-shellfish">Shellfish</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-soy" name="exclusions" value="soy">
                                    <label for="no-soy">Soy</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="no-eggs" name="exclusions" value="eggs">
                                    <label for="no-eggs">Eggs</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-action">
                        <button type="button" id="generate-plan-btn" class="btn btn-primary">Generate Meal Plan</button>
                    </div>
                </form>
            </div>

            <!-- Generated Meal Plan -->
            <div class="meal-plan" id="meal-plan">
                <h2 class="form-subtitle">Your 7-Day Meal Plan</h2>

                <!-- Tabs for Days -->
                <div class="meal-tabs" id="day-tabs">
                    <button class="tab-btn active" data-day="1">Monday</button>
                    <button class="tab-btn" data-day="2">Tuesday</button>
                    <button class="tab-btn" data-day="3">Wednesday</button>
                    <button class="tab-btn" data-day="4">Thursday</button>
                    <button class="tab-btn" data-day="5">Friday</button>
                    <button class="tab-btn" data-day="6">Saturday</button>
                    <button class="tab-btn" data-day="7">Sunday</button>
                </div>

                <!-- Day 1 (Monday) -->
                <div class="tab-content active" id="day-1">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Monday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">150g protein</span>
                                <span class="nutrition-badge">200g carbs</span>
                                <span class="nutrition-badge">67g fat</span>
                            </div>
                        </div>

                        <div class="nutrition-chart">
                            <div class="macro-chart">
                                <div class="protein" style="width: 30%"></div>
                                <div class="carbs" style="width: 40%"></div>
                                <div class="fat" style="width: 30%"></div>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <div class="legend-color legend-protein"></div>
                                    <span>Protein</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color legend-carbs"></div>
                                    <span>Carbs</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color legend-fat"></div>
                                    <span>Fat</span>
                                </div>
                            </div>
                        </div>

                        <div class="meal-cards">
                            <!-- Breakfast -->
                            <div class="meal-card">
                                <img src="../img/meal_plan/Greek Yogurt Parfait.jpg" alt="Greek Yogurt Parfait" class="meal-image">
                                <div class="meal-content">
                                    <span class="meal-type">Breakfast</span>
                                    <h3 class="meal-title">Greek Yogurt Parfait</h3>
                                    <div class="meal-nutrition">
                                        <span>450 cal</span>
                                        <span>30g protein</span>
                                        <span>45g carbs</span>
                                        <span>15g fat</span>
                                    </div>
                                    <p>Greek yogurt with berries, honey, and granola.</p>
                                    <div class="meal-actions">
                                        <button class="btn btn-secondary btn-small">View Recipe</button>
                                        <button class="btn btn-secondary btn-small">Replace</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Lunch -->
                            <div class="meal-card">
                                <img src="../img/meal_plan/grilled chicken salad.jpg" alt="Grilled Chicken Salad" class="meal-image">
                                <div class="meal-content">
                                    <span class="meal-type">Lunch</span>
                                    <h3 class="meal-title">Grilled Chicken Salad</h3>
                                    <div class="meal-nutrition">
                                        <span>550 cal</span>
                                        <span>40g protein</span>
                                        <span>35g carbs</span>
                                        <span>25g fat</span>
                                    </div>
                                    <p>Grilled chicken breast with mixed greens, avocado, and vinaigrette.</p>
                                    <div class="meal-actions">
                                        <button class="btn btn-secondary btn-small">View Recipe</button>
                                        <button class="btn btn-secondary btn-small">Replace</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dinner -->
                            <div class="meal-card">
                                <img src="../img/meal_plan/Salmon with quinoa.jpeg" alt="Salmon with Quinoa" class="meal-image">
                                <div class="meal-content">
                                    <span class="meal-type">Dinner</span>
                                    <h3 class="meal-title">Baked Salmon with Quinoa</h3>
                                    <div class="meal-nutrition">
                                        <span>650 cal</span>
                                        <span>45g protein</span>
                                        <span>60g carbs</span>
                                        <span>22g fat</span>
                                    </div>
                                    <p>Oven-baked salmon with quinoa and roasted vegetables.</p>
                                    <div class="meal-actions">
                                        <button class="btn btn-secondary btn-small">View Recipe</button>
                                        <button class="btn btn-secondary btn-small">Replace</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Snack -->
                            <div class="meal-card">
                                <img src="../img/meal_plan/protein smoothie.jpg" alt="Protein Smoothie" class="meal-image">
                                <div class="meal-content">
                                    <span class="meal-type">Snack</span>
                                    <h3 class="meal-title">Protein Smoothie</h3>
                                    <div class="meal-nutrition">
                                        <span>350 cal</span>
                                        <span>35g protein</span>
                                        <span>30g carbs</span>
                                        <span>5g fat</span>
                                    </div>
                                    <p>Whey protein with banana, spinach, and almond milk.</p>
                                    <div class="meal-actions">
                                        <button class="btn btn-secondary btn-small">View Recipe</button>
                                        <button class="btn btn-secondary btn-small">Replace</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Days 2-7 (placeholders) -->
                <div class="tab-content" id="day-2">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Tuesday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">155g protein</span>
                                <span class="nutrition-badge">190g carbs</span>
                                <span class="nutrition-badge">70g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Tuesday meals will appear here</p>
                    </div>
                </div>

                <div class="tab-content" id="day-3">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Wednesday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">145g protein</span>
                                <span class="nutrition-badge">210g carbs</span>
                                <span class="nutrition-badge">65g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Wednesday meals will appear here</p>
                    </div>
                </div>

                <div class="tab-content" id="day-4">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Thursday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">150g protein</span>
                                <span class="nutrition-badge">205g carbs</span>
                                <span class="nutrition-badge">66g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Thursday meals will appear here</p>
                    </div>
                </div>

                <div class="tab-content" id="day-5">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Friday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">148g protein</span>
                                <span class="nutrition-badge">198g carbs</span>
                                <span class="nutrition-badge">68g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Friday meals will appear here</p>
                    </div>
                </div>

                <div class="tab-content" id="day-6">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Saturday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">152g protein</span>
                                <span class="nutrition-badge">196g carbs</span>
                                <span class="nutrition-badge">70g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Saturday meals will appear here</p>
                    </div>
                </div>

                <div class="tab-content" id="day-7">
                    <div class="meal-day">
                        <div class="day-header">
                            <h3 class="day-title">Sunday</h3>
                            <div class="day-info">
                                <span class="nutrition-badge">2000 cal</span>
                                <span class="nutrition-badge">149g protein</span>
                                <span class="nutrition-badge">202g carbs</span>
                                <span class="nutrition-badge">69g fat</span>
                            </div>
                        </div>
                        <p style="text-align: center; padding: 40px;">Sunday meals will appear here</p>
                    </div>
                </div>
            </div>

            <!-- Download/Share Buttons -->
            <div style="text-align: center; margin-top: 40px;">
                <button class="btn btn-primary">Save Meal Plan</button>
                <button class="btn btn-secondary" style="margin-left: 15px;">Share</button>
                <button class="btn btn-secondary" style="margin-left: 15px;">Print</button>
            </div>
        </div>
    </main>

    <!-- Footer -->
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
                    <li style="margin-bottom: 10px;"><a href="../index.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Home</a></li>
                        <li style="margin-bottom: 10px;"><a href="../recipes.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Recipes</a></li>
                        <li style="margin-bottom: 10px;"><a href="../about.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">About</a></li>
                        <li style="margin-bottom: 10px;"><a href="../contact.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column" style="padding: 0 15px;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 20px; position: relative; padding-bottom: 10px; color: white;">Support</h3>
                    <ul class="footer-links" style="list-style: none;">
                        <li style="margin-bottom: 10px;"><a href="../faq.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">FAQs</a></li>
                        <li style="margin-bottom: 10px;"><a href="../contact.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Contact Us</a></li>
                        <li style="margin-bottom: 10px;"><a href="../privacy.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Privacy Policy</a></li>
                        <li style="margin-bottom: 10px;"><a href="../terms.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Terms of Service</a></li>
                        <li style="margin-bottom: 10px;"><a href="../help.php" style="color: #ccc; text-decoration: none; transition: color 0.3s ease;">Help Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom" style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: #ccc; font-size: 0.9rem; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <p>&copy; 2025 MealPlanner. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>