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

// Check if plan ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['plan_message'] = "Invalid meal plan ID.";
    header("Location: saved_plans.php");
    exit();
}

$plan_id = (int)$_GET['id'];

// Get the requested meal plan and verify ownership
$plan_sql = "SELECT * FROM meal_plans WHERE id = $plan_id AND user_id = $user_id";
$plan_result = $conn->query($plan_sql);

if ($plan_result->num_rows == 0) {
    $_SESSION['plan_message'] = "Meal plan not found or you don't have permission to edit it.";
    header("Location: saved_plans.php");
    exit();
}

$plan = $plan_result->fetch_assoc();
$plan_data = json_decode($plan['plan_data'], true);

// Get day names
$day_names = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$num_days = count($plan_data);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo htmlspecialchars($plan['plan_name']); ?> - MealPlanner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/meal_plan/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="navbar">
                <a href="../index.php" class="logo">Meal<span>Planner</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Create Plan</a></li>
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
            <div class="plan-header-actions">
                <h1 class="section-title">Edit: <?php echo htmlspecialchars($plan['plan_name']); ?></h1>
                <div class="actions">
                    <a href="view_plan.php?id=<?php echo $plan_id; ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Plan</a>
                    <button id="save-changes-btn" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </div>

            <div class="edit-form">
                <div class="form-group">
                    <label for="plan-name">Plan Name</label>
                    <input type="text" id="plan-name" value="<?php echo htmlspecialchars($plan['plan_name']); ?>">
                </div>
            </div>

            <div class="meal-plan edit-mode" id="meal-plan" data-plan-id="<?php echo $plan_id; ?>">
                <!-- Tabs for Days -->
                <div class="meal-tabs" id="day-tabs">
                    <?php for ($i = 0; $i < $num_days; $i++): ?>
                        <button class="tab-btn <?php echo ($i == 0) ? 'active' : ''; ?>" data-day="<?php echo $i + 1; ?>">
                            <?php echo isset($day_names[$i]) ? $day_names[$i] : 'Day ' . ($i + 1); ?>
                        </button>
                    <?php endfor; ?>
                </div>

                <?php for ($day_index = 0; $day_index < $num_days; $day_index++): ?>
                    <?php 
                    $day_data = $plan_data[$day_index];
                    $day_name = isset($day_names[$day_index]) ? $day_names[$day_index] : 'Day ' . ($day_index + 1);
                    ?>
                    <div class="tab-content <?php echo ($day_index == 0) ? 'active' : ''; ?>" id="day-<?php echo $day_index + 1; ?>">
                        <div class="meal-day">
                            <div class="day-header">
                                <h3 class="day-title"><?php echo $day_name; ?></h3>
                                <div class="day-info">
                                    <span class="nutrition-badge"><?php echo $day_data['totals']['calories']; ?> cal</span>
                                    <span class="nutrition-badge"><?php echo $day_data['totals']['protein']; ?>g protein</span>
                                    <span class="nutrition-badge"><?php echo $day_data['totals']['carbs']; ?>g carbs</span>
                                    <span class="nutrition-badge"><?php echo $day_data['totals']['fat']; ?>g fat</span>
                                </div>
                            </div>

                            <div class="nutrition-chart">
                                <div class="macro-chart">
                                    <?php 
                                    $total_cals = $day_data['totals']['calories'];
                                    $protein_cals = $day_data['totals']['protein'] * 4;
                                    $carbs_cals = $day_data['totals']['carbs'] * 4;
                                    $fat_cals = $day_data['totals']['fat'] * 9;
                                    
                                    $protein_pct = round(($protein_cals / $total_cals) * 100);
                                    $carbs_pct = round(($carbs_cals / $total_cals) * 100);
                                    $fat_pct = round(($fat_cals / $total_cals) * 100);
                                    
                                    // Adjust to ensure they sum to 100%
                                    $sum = $protein_pct + $carbs_pct + $fat_pct;
                                    if ($sum != 100) {
                                        $protein_pct += (100 - $sum);
                                    }
                                    ?>
                                    <div class="macro-bar protein" style="width: <?php echo $protein_pct; ?>%;"><?php echo $protein_pct; ?>% Protein</div>
                                    <div class="macro-bar carbs" style="width: <?php echo $carbs_pct; ?>%;"><?php echo $carbs_pct; ?>% Carbs</div>
                                    <div class="macro-bar fat" style="width: <?php echo $fat_pct; ?>%;"><?php echo $fat_pct; ?>% Fat</div>
                                </div>
                            </div>

                            <div class="meal-list">
                                <?php foreach ($day_data['meals'] as $meal_index => $meal): ?>
                                    <div class="meal-card" data-meal-id="<?php echo $meal['id']; ?>" data-day-index="<?php echo $day_index; ?>" data-meal-index="<?php echo $meal_index; ?>">
                                        <h4 class="meal-title">
                                            <?php 
                                            $meal_names = ['Breakfast', 'Lunch', 'Dinner', 'Snack 1', 'Snack 2', 'Snack 3'];
                                            echo isset($meal_names[$meal_index]) ? $meal_names[$meal_index] : 'Meal ' . ($meal_index + 1);
                                            ?>
                                        </h4>
                                        <div class="recipe-card">
                                            <img src="<?php echo !empty($meal['image_url']) ? '../' . $meal['image_url'] : '../img/recipe-placeholder.jpg'; ?>" 
                                                alt="<?php echo htmlspecialchars($meal['name']); ?>" class="recipe-image">
                                            <div class="recipe-content">
                                                <h5 class="recipe-title"><?php echo htmlspecialchars($meal['name']); ?></h5>
                                                <div class="recipe-info">
                                                    <span><?php echo $meal['prep_time'] + $meal['cook_time']; ?> mins</span>
                                                    <span><?php echo $meal['calories']; ?> cal</span>
                                                    <span><?php echo $meal['protein']; ?>g protein</span>
                                                </div>
                                                <div class="edit-controls">
                                                    <button class="btn btn-secondary btn-sm swap-recipe-btn">
                                                        <i class="fas fa-exchange-alt"></i> Swap Meal
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm edit-recipe-btn">
                                                        <i class="fas fa-edit"></i> Edit Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </main>

    <!-- Recipe Swap Modal -->
    <div class="modal" id="swap-recipe-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Replace Recipe</h3>
            <div class="filter-section">
                <input type="text" id="recipe-search" placeholder="Search recipes...">
                <select id="dietary-filter">
                    <option value="">All Dietary Types</option>
                    <option value="vegetarian">Vegetarian</option>
                    <option value="vegan">Vegan</option>
                    <option value="paleo">Paleo</option>
                    <option value="keto">Keto</option>
                    <option value="mediterranean">Mediterranean</option>
                </select>
            </div>
            <div class="recipes-container">
                <!-- Recipe cards will be loaded here -->
                <div class="loading">Loading recipes...</div>
            </div>
        </div>
    </div>

    <!-- Edit Recipe Details Modal -->
    <div class="modal" id="edit-recipe-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Edit Meal Details</h3>
            <form id="edit-recipe-form">
                <div class="form-group">
                    <label for="edit-recipe-calories">Calories:</label>
                    <input type="number" id="edit-recipe-calories" min="0" max="2000">
                </div>
                <div class="form-group">
                    <label for="edit-recipe-protein">Protein (g):</label>
                    <input type="number" id="edit-recipe-protein" min="0" max="200">
                </div>
                <div class="form-group">
                    <label for="edit-recipe-carbs">Carbs (g):</label>
                    <input type="number" id="edit-recipe-carbs" min="0" max="300">
                </div>
                <div class="form-group">
                    <label for="edit-recipe-fat">Fat (g):</label>
                    <input type="number" id="edit-recipe-fat" min="0" max="100">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2023 MealPlanner. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Store current editing state
        const planId = <?php echo $plan_id; ?>;
        let currentEditingMeal = null;
        let planData = <?php echo json_encode($plan_data); ?>;

        // Tab switching functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const dayNum = btn.getAttribute('data-day');
                
                // Remove active class from all buttons and contents
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to selected button and content
                btn.classList.add('active');
                document.getElementById(`day-${dayNum}`).classList.add('active');
            });
        });

        // Swap Recipe functionality
        document.querySelectorAll('.swap-recipe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const mealCard = this.closest('.meal-card');
                currentEditingMeal = {
                    dayIndex: mealCard.getAttribute('data-day-index'),
                    mealIndex: mealCard.getAttribute('data-meal-index')
                };
                
                // Show modal
                document.getElementById('swap-recipe-modal').style.display = 'block';
                // Load recipes
                loadRecipes();
            });
        });

        // Edit Recipe Details functionality
        document.querySelectorAll('.edit-recipe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const mealCard = this.closest('.meal-card');
                const dayIndex = mealCard.getAttribute('data-day-index');
                const mealIndex = mealCard.getAttribute('data-meal-index');
                currentEditingMeal = { dayIndex, mealIndex };
                
                // Get meal data
                const meal = planData[dayIndex].meals[mealIndex];
                
                // Populate form
                document.getElementById('edit-recipe-calories').value = meal.calories;
                document.getElementById('edit-recipe-protein').value = meal.protein;
                document.getElementById('edit-recipe-carbs').value = meal.carbs;
                document.getElementById('edit-recipe-fat').value = meal.fat;
                
                // Show modal
                document.getElementById('edit-recipe-modal').style.display = 'block';
            });
        });

        // Close modals
        document.querySelectorAll('.close-modal').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        // Load recipes for swap modal
        async function loadRecipes() {
            const container = document.querySelector('#swap-recipe-modal .recipes-container');
            const loading = container.querySelector('.loading');
            
            // Get filter values
            const searchQuery = document.getElementById('recipe-search').value;
            const dietaryFilter = document.getElementById('dietary-filter').value;
            
            // Clear previous results
            container.innerHTML = '<div class="loading">Loading recipes...</div>';
            
            try {
                // Build query string
                let url = '../php/get_recipes.php?';
                if (searchQuery) url += `search=${encodeURIComponent(searchQuery)}&`;
                if (dietaryFilter) url += `diet_type=${encodeURIComponent(dietaryFilter)}&`;
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.recipes.length > 0) {
                    // Display recipes
                    container.innerHTML = '';
                    data.recipes.forEach(recipe => {
                        const recipeCard = document.createElement('div');
                        recipeCard.className = 'recipe-card-small';
                        recipeCard.innerHTML = `
                            <img src="../${recipe.image_url}" alt="${recipe.name}">
                            <div class="recipe-info">
                                <h4>${recipe.name}</h4>
                                <div class="recipe-stats">
                                    <span>${recipe.calories} cal</span>
                                    <span>${recipe.protein}g protein</span>
                                </div>
                                <button class="btn btn-sm select-recipe" data-recipe-id="${recipe.id}">Select</button>
                            </div>
                        `;
                        container.appendChild(recipeCard);
                        
                        // Add event listener to select button
                        recipeCard.querySelector('.select-recipe').addEventListener('click', function() {
                            swapRecipe(this.getAttribute('data-recipe-id'));
                        });
                    });
                } else {
                    container.innerHTML = '<div class="no-results">No recipes found matching your criteria</div>';
                }
            } catch (error) {
                console.error('Error loading recipes:', error);
                container.innerHTML = '<div class="error">Error loading recipes. Please try again.</div>';
            }
        }

        // Handle recipe swapping
        async function swapRecipe(recipeId) {
            if (!currentEditingMeal) return;
            
            const modal = document.getElementById('swap-recipe-modal');
            modal.querySelector('.recipes-container').innerHTML = '<div class="loading">Swapping meal...</div>';
            
            try {
                const response = await fetch('../php/replace_meal.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        day_index: currentEditingMeal.dayIndex,
                        meal_index: currentEditingMeal.mealIndex,
                        new_recipe_id: recipeId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update plan data
                    planData = data.updated_plan;
                    
                    // Close modal
                    modal.style.display = 'none';
                    
                    // Refresh page to show changes
                    window.location.reload();
                } else {
                    modal.querySelector('.recipes-container').innerHTML = 
                        `<div class="error">Error: ${data.error}</div>`;
                }
            } catch (error) {
                console.error('Error swapping recipe:', error);
                modal.querySelector('.recipes-container').innerHTML = 
                    '<div class="error">Error swapping recipe. Please try again.</div>';
            }
        }

        // Handle edit recipe form submission
        document.getElementById('edit-recipe-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!currentEditingMeal) return;
            
            const { dayIndex, mealIndex } = currentEditingMeal;
            const meal = planData[dayIndex].meals[mealIndex];
            
            // Get updated values
            const calories = parseInt(document.getElementById('edit-recipe-calories').value);
            const protein = parseInt(document.getElementById('edit-recipe-protein').value);
            const carbs = parseInt(document.getElementById('edit-recipe-carbs').value);
            const fat = parseInt(document.getElementById('edit-recipe-fat').value);
            
            // Update meal
            meal.calories = calories;
            meal.protein = protein;
            meal.carbs = carbs;
            meal.fat = fat;
            
            // Update day totals
            updateDayTotals(dayIndex);
            
            // Close modal
            document.getElementById('edit-recipe-modal').style.display = 'none';
            
            // Update UI
            updateMealUI(dayIndex, mealIndex);
            updateDayTotalsUI(dayIndex);
        });

        // Update day totals after changing a meal
        function updateDayTotals(dayIndex) {
            const day = planData[dayIndex];
            const meals = day.meals;
            
            // Reset totals
            day.totals = {
                calories: 0,
                protein: 0,
                carbs: 0,
                fat: 0
            };
            
            // Sum up all meals
            meals.forEach(meal => {
                day.totals.calories += meal.calories;
                day.totals.protein += meal.protein;
                day.totals.carbs += meal.carbs;
                day.totals.fat += meal.fat;
            });
        }

        // Update meal UI after editing
        function updateMealUI(dayIndex, mealIndex) {
            const meal = planData[dayIndex].meals[mealIndex];
            const dayContent = document.getElementById(`day-${parseInt(dayIndex) + 1}`);
            const mealCards = dayContent.querySelectorAll('.meal-card');
            const mealCard = mealCards[mealIndex];
            
            // Update nutrition info
            const infoSpans = mealCard.querySelectorAll('.recipe-info span');
            infoSpans[1].textContent = `${meal.calories} cal`;
            infoSpans[2].textContent = `${meal.protein}g protein`;
        }

        // Update day totals UI
        function updateDayTotalsUI(dayIndex) {
            const day = planData[dayIndex];
            const dayContent = document.getElementById(`day-${parseInt(dayIndex) + 1}`);
            
            // Update badge values
            const badges = dayContent.querySelectorAll('.day-info .nutrition-badge');
            badges[0].textContent = `${day.totals.calories} cal`;
            badges[1].textContent = `${day.totals.protein}g protein`;
            badges[2].textContent = `${day.totals.carbs}g carbs`;
            badges[3].textContent = `${day.totals.fat}g fat`;
            
            // Update macro chart
            const total_cals = day.totals.calories;
            const protein_cals = day.totals.protein * 4;
            const carbs_cals = day.totals.carbs * 4;
            const fat_cals = day.totals.fat * 9;
            
            const protein_pct = Math.round((protein_cals / total_cals) * 100);
            const carbs_pct = Math.round((carbs_cals / total_cals) * 100);
            const fat_pct = Math.round((fat_cals / total_cals) * 100);
            
            const macroBars = dayContent.querySelectorAll('.macro-bar');
            macroBars[0].style.width = `${protein_pct}%`;
            macroBars[0].textContent = `${protein_pct}% Protein`;
            
            macroBars[1].style.width = `${carbs_pct}%`;
            macroBars[1].textContent = `${carbs_pct}% Carbs`;
            
            macroBars[2].style.width = `${fat_pct}%`;
            macroBars[2].textContent = `${fat_pct}% Fat`;
        }

        // Save all changes
        document.getElementById('save-changes-btn').addEventListener('click', async function() {
            this.textContent = 'Saving...';
            this.disabled = true;
            
            // Get plan name
            const planName = document.getElementById('plan-name').value;
            
            try {
                const response = await fetch('save_plan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        plan_name: planName,
                        plan_data: planData
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Plan saved successfully');
                    window.location.href = `view_plan.php?id=${planId}`;
                } else {
                    alert(`Error saving plan: ${data.error}`);
                }
            } catch (error) {
                console.error('Error saving plan:', error);
                alert('Error saving plan. Please try again.');
            } finally {
                this.textContent = 'Save Changes';
                this.disabled = false;
            }
        });

        // Search and filter functionality for recipes
        document.getElementById('recipe-search').addEventListener('input', loadRecipes);
        document.getElementById('dietary-filter').addEventListener('change', loadRecipes);
    </script>
</body>
</html> 