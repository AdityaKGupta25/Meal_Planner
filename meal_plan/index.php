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
                    <li class="active"><a href="index.php">Create Plan</a></li>
                    <li><a href="saved_plans.php">Saved Plans</a></li>
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
                                <div class="macro-bar protein" style="width: 30%;">30% Protein</div>
                                <div class="macro-bar carbs" style="width: 40%;">40% Carbs</div>
                                <div class="macro-bar fat" style="width: 30%;">30% Fat</div>
                            </div>
                        </div>

                        <div class="meal-list">
                            <!-- Meals will be dynamically generated here -->
                            <div class="meal-card">
                                <h4 class="meal-title">Breakfast</h4>
                                <div class="recipe-card">
                                    <img src="../img/greek-yogurt-parfait.jpg" alt="Greek Yogurt Parfait" class="recipe-image">
                                    <div class="recipe-content">
                                        <h5 class="recipe-title">Greek Yogurt Parfait</h5>
                                        <div class="recipe-info">
                                            <span>5 mins</span>
                                            <span>350 cal</span>
                                            <span>25g protein</span>
                                        </div>
                                        <button class="btn btn-secondary btn-sm recipe-swap">Swap Recipe</button>
                                    </div>
                                </div>
                            </div>

                            <div class="meal-card">
                                <h4 class="meal-title">Lunch</h4>
                                <div class="recipe-card">
                                    <img src="../img/mediterranean-quinoa-salad-4.jpg" alt="Mediterranean Quinoa Salad" class="recipe-image">
                                    <div class="recipe-content">
                                        <h5 class="recipe-title">Mediterranean Quinoa Salad</h5>
                                        <div class="recipe-info">
                                            <span>20 mins</span>
                                            <span>450 cal</span>
                                            <span>15g protein</span>
                                        </div>
                                        <button class="btn btn-secondary btn-sm recipe-swap">Swap Recipe</button>
                                    </div>
                                </div>
                            </div>

                            <div class="meal-card">
                                <h4 class="meal-title">Dinner</h4>
                                <div class="recipe-card">
                                    <img src="../img/grilled-salmon.jpg" alt="Grilled Salmon with Vegetables" class="recipe-image">
                                    <div class="recipe-content">
                                        <h5 class="recipe-title">Grilled Salmon with Vegetables</h5>
                                        <div class="recipe-info">
                                            <span>30 mins</span>
                                            <span>580 cal</span>
                                            <span>42g protein</span>
                                        </div>
                                        <button class="btn btn-secondary btn-sm recipe-swap">Swap Recipe</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other days will be similar but hidden initially -->
                <div class="tab-content" id="day-2">
                    <!-- Tuesday content will be dynamically loaded -->
                </div>
                <!-- More day tabs... -->
            </div>

            <!-- Recipe Swap Modal -->
            <div class="modal" id="recipe-swap-modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h3>Replace Recipe</h3>
                    <div class="recipe-search">
                        <input type="text" id="recipe-search-input" placeholder="Search recipes...">
                        <button id="recipe-search-btn" class="btn">Search</button>
                    </div>
                    <div class="recipe-filters">
                        <select id="recipe-filter-diet">
                            <option value="">Any Diet</option>
                            <option value="vegetarian">Vegetarian</option>
                            <option value="vegan">Vegan</option>
                            <option value="keto">Keto</option>
                        </select>
                        <select id="recipe-filter-time">
                            <option value="">Any Time</option>
                            <option value="15">Under 15 min</option>
                            <option value="30">Under 30 min</option>
                            <option value="60">Under 60 min</option>
                        </select>
                    </div>
                    <div class="recipe-results">
                        <!-- Recipe results will be dynamically loaded here -->
                    </div>
                </div>
            </div>

            <!-- Save and Export Buttons -->
            <div class="action-buttons">
                <button id="save-plan-btn" class="btn btn-primary">Save Meal Plan</button>
                <button id="export-plan-btn" class="btn btn-secondary">Export to PDF</button>
                <button id="export-groceries-btn" class="btn btn-secondary">Generate Grocery List</button>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2023 MealPlanner. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html> 