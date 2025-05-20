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

// Get user's saved meal plans
$plans_sql = "SELECT * FROM meal_plans WHERE user_id = $user_id ORDER BY created_at DESC";
$plans_result = $conn->query($plans_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Saved Meal Plans - MealPlanner</title>
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
                    <li class="active"><a href="saved_plans.php">Saved Plans</a></li>
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
            <h1 class="section-title">My Saved Meal Plans</h1>

            <?php if(isset($_SESSION['plan_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['plan_message']; 
                        unset($_SESSION['plan_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($plans_result->num_rows > 0): ?>
                <div class="plans-filter">
                    <div class="filter-options">
                        <button class="filter-btn active" data-filter="all">All Plans</button>
                        <button class="filter-btn" data-filter="favorites">Favorites</button>
                        <button class="filter-btn" data-filter="recent">Recently Added</button>
                    </div>
                    <div class="sort-options">
                        <label for="sort-select">Sort by:</label>
                        <select id="sort-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name">Name</option>
                            <option value="calories">Calories</option>
                        </select>
                    </div>
                </div>
                
                <div class="saved-plans">
                    <?php while ($plan = $plans_result->fetch_assoc()): ?>
                                                <?php                         $plan_data = json_decode($plan['plan_data'], true);                        $days = count($plan_data);                        $meals_per_day = isset($plan_data[0]['meals']) ? count($plan_data[0]['meals']) : 0;                                                // Get a sample image from the plan's first day                        $sample_image = '../img/recipe-placeholder.jpg';                        if (isset($plan_data[0]['meals'][0]['image_url']) && !empty($plan_data[0]['meals'][0]['image_url'])) {                            $sample_image = '../' . $plan_data[0]['meals'][0]['image_url'];                        }                                                // Check if this plan is a favorite                        $is_favorite = isset($plan['is_favorite']) && $plan['is_favorite'] == 1;                        ?>                        <div class="plan-card <?php echo $is_favorite ? 'is-favorite' : ''; ?>">
                            <div class="plan-image">
                                <img src="<?php echo $sample_image; ?>" alt="<?php echo htmlspecialchars($plan['plan_name']); ?>">
                            </div>
                                                        <div class="plan-header">                                <h3>                                    <?php if ($is_favorite): ?>                                        <i class="fas fa-star favorite-icon"></i>                                    <?php endif; ?>                                    <?php echo htmlspecialchars($plan['plan_name']); ?>                                </h3>                                <span class="plan-date"><?php echo date('M d, Y', strtotime($plan['created_at'])); ?></span>                            </div>
                            <div class="plan-stats">
                                <div class="stat">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?php echo $days; ?> <?php echo $days > 1 ? 'days' : 'day'; ?></span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-utensils"></i>
                                    <span><?php echo $meals_per_day; ?> meals/day</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-fire"></i>
                                    <span>~<?php echo isset($plan_data[0]['totals']['calories']) ? $plan_data[0]['totals']['calories'] : '0'; ?> cal/day</span>
                                </div>
                            </div>
                            <div class="plan-actions">
                                <a href="view_plan.php?id=<?php echo $plan['id']; ?>" class="btn btn-primary">View Plan</a>
                                <button class="btn btn-secondary btn-sm" data-plan-id="<?php echo $plan['id']; ?>" onclick="deletePlan(this)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No Saved Meal Plans</h3>
                    <p>You haven't created any meal plans yet. Create your first personalized meal plan now!</p>
                    <a href="index.php" class="btn btn-primary">Create Meal Plan</a>
                </div>
            <?php endif; ?>
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
        function deletePlan(button) {
            if (confirm('Are you sure you want to delete this meal plan?')) {
                const planId = button.getAttribute('data-plan-id');
                window.location.href = 'delete_plan.php?id=' + planId;
            }
        }

        // Plan filtering and sorting
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const sortSelect = document.getElementById('sort-select');
            const planCards = document.querySelectorAll('.plan-card');
            
            // Filter functionality
            filterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active button
                    filterButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    // Show/hide cards based on filter
                    planCards.forEach(card => {
                        card.style.display = 'block'; // Reset display
                        
                        if (filter === 'favorites' && !card.classList.contains('is-favorite')) {
                            card.style.display = 'none';
                        } else if (filter === 'recent') {
                            // Get the date of the plan 
                            const dateText = card.querySelector('.plan-date').textContent;
                            const planDate = new Date(dateText);
                            const oneWeekAgo = new Date();
                            oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
                            
                            if (planDate < oneWeekAgo) {
                                card.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
            // Sort functionality
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                const plansContainer = document.querySelector('.saved-plans');
                const plans = Array.from(planCards);
                
                plans.sort((a, b) => {
                    if (sortValue === 'newest') {
                        const dateA = new Date(a.querySelector('.plan-date').textContent);
                        const dateB = new Date(b.querySelector('.plan-date').textContent);
                        return dateB - dateA;
                    } else if (sortValue === 'oldest') {
                        const dateA = new Date(a.querySelector('.plan-date').textContent);
                        const dateB = new Date(b.querySelector('.plan-date').textContent);
                        return dateA - dateB;
                    } else if (sortValue === 'name') {
                        const nameA = a.querySelector('h3').textContent.toLowerCase();
                        const nameB = b.querySelector('h3').textContent.toLowerCase();
                        return nameA.localeCompare(nameB);
                    } else if (sortValue === 'calories') {
                        const calA = parseInt(a.querySelector('.stat:nth-child(3) span').textContent);
                        const calB = parseInt(b.querySelector('.stat:nth-child(3) span').textContent);
                        return calB - calA;
                    }
                    return 0;
                });
                
                // Re-append sorted plans
                plans.forEach(plan => {
                    plansContainer.appendChild(plan);
                });
            });
        });
    </script>
</body>
</html> 