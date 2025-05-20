<?php
session_start();
// Include database connection
require_once 'php/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealPlanner - Your Personal Meal Planning Assistant</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <a href="index.php" class="logo">Meal<span>Planner</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="recipes.php">Recipes</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="meal_plan/">Meal Plans</a></li>
                        <li class="profile-nav-item">
                            <a href="profile.php" class="profile-link">
                                My Profile
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="signup.php" class="signup-btn">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu-toggle">☰</div>
            </div>
        </div>
    </header>

    <section class="meal">
        <div class="container">
            <div class="meal-content">
                <h1 class="meal-title">Plan Your Meals, Simplify Your Life</h1>
                <p class="meal-subtitle">Create personalized meal plans, discover new recipes, and generate grocery lists with a few clicks. Start your journey to healthier eating today.</p>
                <div class="meal-buttons">
                    <a href="#" onclick="window.location.replace('signup.php'); return false;" class="btn btn-primary">Get Started</a>
                    <button class="btn btn-secondary">Learn More</button>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose MealPlanner</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📅</div>
                    <h3 class="feature-title">Easy Planning</h3>
                    <p class="feature-description">Plan your meals for days, weeks, or even months ahead with our intuitive drag-and-drop interface.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🥗</div>
                    <h3 class="feature-title">Personalized Options</h3>
                    <p class="feature-description">Set your dietary preferences and restrictions to get customized meal recommendations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛒</div>
                    <h3 class="feature-title">Automatic Grocery Lists</h3>
                    <p class="feature-description">Generate shopping lists based on your meal plan with a single click, organized by category.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⏱️</div>
                    <h3 class="feature-title">Time-Saving</h3>
                    <p class="feature-description">Save hours each week on meal planning and grocery shopping with our efficient tools.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3 class="feature-title">Budget-Friendly</h3>
                    <p class="feature-description">Reduce food waste and save money by planning meals and using ingredients efficiently.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Mobile-Friendly</h3>
                    <p class="feature-description">Access your meal plans and grocery lists on any device, anywhere, anytime.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Create Your Profile</h3>
                    <img src="img/profile setup.webp" alt="Profile Creation" class="step-image">
                    <p>Set your dietary preferences, restrictions, and health goals to get personalized recommendations.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Plan Your Meals</h3>
                    <img src="img/diet plan.jpg" alt="Meal Planning" class="step-image">
                    <p>Browse recipes, drag them to your weekly calendar, or let MealPlanner suggest plans for you.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Shop & Cook</h3>
                    <img src="img/shop and cook.jpg" alt="Shopping and Cooking" class="step-image">
                    <p>Get your grocery list organized by store sections, then follow easy recipes to prepare delicious meals.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Users Say</h2>
            <div class="testimonials-slider">
                <div class="testimonial">
                    <p class="testimonial-text">"MealPlanner has completely transformed how I plan meals for my family. I save so much time and money each week, and we're eating healthier than ever!"</p>
                    <h4 class="testimonial-author">Ramesh</h4>
                    <p class="testimonial-role">Family of 4</p>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-recipes">
        <div class="container">
            <h2 class="section-title">Featured Recipes</h2>
            <div class="recipes-grid">
                <div class="recipe-card">
                    <img src="img/mediterranean-quinoa-salad-4.jpg" alt="Quinoa Salad" class="recipe-image">
                    <div class="recipe-content">
                        <h3 class="recipe-title">Mediterranean Quinoa Salad</h3>
                        <div class="recipe-info">
                            <span>20 mins</span>
                            <span>350 calories</span>
                            <span>Vegetarian</span>
                        </div>
                        <p class="recipe-description">A refreshing quinoa salad with cucumber, tomatoes, feta cheese, and a lemon herb dressing.</p>
                        <a href="#" class="btn btn-secondary">View Recipe</a>
                    </div>
                </div>
                <div class="recipe-card">
                    <img src="img/Edamame, Corn, and Black Bean Salad.webp" alt="Salad" class="recipe-image">
                    <div class="recipe-content">
                        <h3 class="recipe-title">Edamame, Corn, and Black Bean Salad</h3>
                        <div class="recipe-info">
                            <span>10 mins</span>
                            <span>300 calories</span>
                            <span>Good Protein</span>
                        </div>
                        <p class="recipe-description">A light lime vinaigrette adds a splash of acidity to this colorful, lettuce-free salad recipe.</p>
                        <a href="#" class="btn btn-secondary">View Recipe</a>
                    </div>
                </div>
                <div class="recipe-card">
                    <img src="img/Vegetable Curry.jpg" alt="Vegetable Curry" class="recipe-image">
                    <div class="recipe-content">
                        <h3 class="recipe-title">Creamy Vegetable Curry</h3>
                        <div class="recipe-info">
                            <span>40 mins</span>
                            <span>380 calories</span>
                            <span>Vegan</span>
                        </div>
                        <p class="recipe-description">A hearty vegetable curry with coconut milk, aromatic spices, and seasonal vegetables.</p>
                        <a href="#" class="btn btn-secondary">View Recipe</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="cta">
        <div class="container">
            <h2 class="cta-title">Ready to Simplify Your Meal Planning?</h2>
            <p class="cta-subtitle">Join thousands of users who are saving time, reducing food waste, and enjoying healthier meals with MealPlanner.</p>
            <a href="#" onclick="window.location.replace('signup.php'); return false;" class="btn cta-btn">Sign Up for Free</a>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>MealPlanner</h3>
                    <p>Your personal assistant for meal planning, recipe discovery, and grocery shopping.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Recipes</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Help Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 MealPlanner. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>