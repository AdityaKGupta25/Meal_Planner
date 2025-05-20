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
    $_SESSION['plan_message'] = "Meal plan not found or you don't have permission to view it.";
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
    <title><?php echo htmlspecialchars($plan['plan_name']); ?> - MealPlanner</title>
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
                <h1 class="section-title"><?php echo htmlspecialchars($plan['plan_name']); ?></h1>
                <div class="actions">
                    <a href="saved_plans.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Plans</a>
                    <button id="print-plan" class="btn btn-secondary"><i class="fas fa-print"></i> Print</button>
                    <button id="download-plan" class="btn btn-secondary"><i class="fas fa-download"></i> Download</button>
                    <button id="favorite-plan" class="btn btn-secondary <?php echo $plan['is_favorite'] ? 'favorite-active' : ''; ?>">
                        <i class="<?php echo $plan['is_favorite'] ? 'fas' : 'far'; ?> fa-star"></i> 
                        <?php echo $plan['is_favorite'] ? 'Favorited' : 'Favorite'; ?>
                    </button>
                    <a href="edit_plan.php?id=<?php echo $plan_id; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Plan</a>
                </div>
            </div>

            <div class="meal-plan view-mode" id="meal-plan">
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
                                    <div class="meal-card">
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
                                                <p class="recipe-description">
                                                    <?php echo !empty($meal['description']) ? htmlspecialchars($meal['description']) : 'No description available.'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Grocery List Section -->
            <div class="grocery-list-section">
                <h2 class="form-subtitle">Grocery List</h2>
                <button id="generate-grocery-list" class="btn btn-primary">Generate Grocery List</button>
                <div id="grocery-list-content" class="hidden">
                    <!-- Grocery list will be dynamically generated here -->
                </div>
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

    <script>
        // Tab switching functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const dayNum = btn.getAttribute('data-day');
                
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to selected button and content
                btn.classList.add('active');
                document.getElementById(`day-${dayNum}`).classList.add('active');
            });
        });

        // Print functionality
        document.getElementById('print-plan').addEventListener('click', () => {
            window.print();
        });

        // Download functionality
        document.getElementById('download-plan').addEventListener('click', async () => {
            try {
                const response = await fetch('download_plan.php?id=<?php echo $plan_id; ?>');
                if (!response.ok) throw new Error('Failed to download plan');
                
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = '<?php echo htmlspecialchars($plan['plan_name']); ?>.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
            } catch (error) {
                console.error('Error downloading plan:', error);
                alert('Failed to download plan. Please try again.');
            }
        });

        // Favorite plan functionality
        document.getElementById('favorite-plan').addEventListener('click', async function() {
            try {
                const isFavorite = this.classList.contains('favorite-active');
                const response = await fetch('toggle_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: <?php echo $plan_id; ?>,
                        is_favorite: !isFavorite
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Toggle favorite UI
                    this.classList.toggle('favorite-active');
                    const icon = this.querySelector('i');
                    if (this.classList.contains('favorite-active')) {
                        icon.className = 'fas fa-star';
                        this.innerHTML = '<i class="fas fa-star"></i> Favorited';
                    } else {
                        icon.className = 'far fa-star';
                        this.innerHTML = '<i class="far fa-star"></i> Favorite';
                    }
                } else {
                    alert('Failed to update favorite status: ' + data.error);
                }
            } catch (error) {
                console.error('Error updating favorite status:', error);
                alert('Failed to update favorite status. Please try again.');
            }
        });

        // Generate grocery list
        document.getElementById('generate-grocery-list').addEventListener('click', function() {
            const groceryListContent = document.getElementById('grocery-list-content');
            groceryListContent.classList.toggle('hidden');
            
            if (!groceryListContent.classList.contains('hidden') && groceryListContent.innerHTML.trim() === '') {
                // Generate grocery list content
                const planData = <?php echo json_encode($plan_data); ?>;
                const groceryList = generateGroceryList(planData);
                groceryListContent.innerHTML = groceryList;
            }
        });

        function generateGroceryList(planData) {
            // This is a simplified version - in a real app, you would extract ingredients from recipes
            let html = '<div class="grocery-categories">';
            
            const categories = {
                'Proteins': ['Chicken', 'Beef', 'Fish', 'Eggs', 'Tofu'],
                'Grains': ['Rice', 'Pasta', 'Bread', 'Quinoa'],
                'Vegetables': ['Lettuce', 'Tomatoes', 'Onions', 'Bell Peppers', 'Carrots'],
                'Fruits': ['Apples', 'Bananas', 'Berries', 'Citrus'],
                'Dairy': ['Milk', 'Yogurt', 'Cheese'],
                'Pantry': ['Olive Oil', 'Vinegar', 'Spices', 'Canned Goods']
            };
            
            // Generate a simple list based on categories
            for (const [category, items] of Object.entries(categories)) {
                html += `<div class="grocery-category">
                    <h3>${category}</h3>
                    <ul>`;
                    
                for (const item of items) {
                    html += `<li><label><input type="checkbox"> ${item}</label></li>`;
                }
                
                html += `</ul></div>`;
            }
            
            html += `</div>
                <button class="btn btn-secondary" onclick="window.print()">Print Grocery List</button>`;
            
            return html;
        }
    </script>
</body>
</html>