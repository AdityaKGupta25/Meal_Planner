// JavaScript

// Mobile Menu Toggle
document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});

// Tab Switching
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        document.getElementById('day-' + this.getAttribute('data-day')).classList.add('active');
    });
});

// Range Slider for Calories
const caloriesSlider = document.getElementById('calories-slider');
const caloriesInput = document.getElementById('calories');

caloriesSlider.addEventListener('input', function() {
    caloriesInput.value = this.value;
});

caloriesInput.addEventListener('input', function() {
    caloriesSlider.value = this.value;
});

// Range Slider for Macros
const proteinSlider = document.getElementById('protein-slider');
const carbsSlider = document.getElementById('carbs-slider');
const fatSlider = document.getElementById('fat-slider');

const proteinValue = document.getElementById('protein-value');
const carbsValue = document.getElementById('carbs-value');
const fatValue = document.getElementById('fat-value');

proteinSlider.addEventListener('input', updateMacros);
carbsSlider.addEventListener('input', updateMacros);
fatSlider.addEventListener('input', updateMacros);

function updateMacros() {
    let protein = parseInt(proteinSlider.value);
    let carbs = parseInt(carbsSlider.value);
    let fat = parseInt(fatSlider.value);
    
    // Ensure total equals 100%
    let total = protein + carbs + fat;
    
    if (total !== 100) {
        // Adjust based on which slider was last moved
        if (this === proteinSlider) {
            // Adjust carbs and fat equally
            let remainder = 100 - protein;
            let ratio = remainder / (carbs + fat);
            
            carbs = Math.round(carbs * ratio);
            fat = 100 - protein - carbs;
            
            carbsSlider.value = carbs;
            fatSlider.value = fat;
        } else if (this === carbsSlider) {
            // Adjust protein and fat equally
            let remainder = 100 - carbs;
            let ratio = remainder / (protein + fat);
            
            protein = Math.round(protein * ratio);
            fat = 100 - protein - carbs;
            
            proteinSlider.value = protein;
            fatSlider.value = fat;
        } else {
            // Adjust protein and carbs equally
            let remainder = 100 - fat;
            let ratio = remainder / (protein + carbs);
            
            protein = Math.round(protein * ratio);
            carbs = 100 - protein - fat;
            
            proteinSlider.value = protein;
            carbsSlider.value = carbs;
        }
    }
    
    // Update displayed values
    proteinValue.textContent = proteinSlider.value;
    carbsValue.textContent = carbsSlider.value;
    fatValue.textContent = fatSlider.value;
}

// Main Plan Container
const mealPlanContainer = document.getElementById('meal-plan');
// Current plan ID
let currentPlanId = null;

// Load existing meal plan on page load
document.addEventListener('DOMContentLoaded', function() {
    checkExistingPlan();
});

// Check if user has an existing meal plan
async function checkExistingPlan() {
    try {
        const response = await fetch('../php/get_meal_plan.php');
        const data = await response.json();
        
        if (data.success && data.plan) {
            currentPlanId = data.plan.id;
            displayMealPlan(data.plan.plan_data);
        }
    } catch (error) {
        console.error('Error checking for existing plan:', error);
    }
}

// Generate Plan Button
document.getElementById('generate-plan-btn').addEventListener('click', function() {
    generateMealPlan();
});

// Generate a new meal plan
async function generateMealPlan() {
    // Show loading state
    mealPlanContainer.innerHTML = '<div class="loading">Generating your personalized meal plan...</div>';
    mealPlanContainer.scrollIntoView({ behavior: 'smooth' });
    
    // Get preferences from the form
    const preferences = {
        calories: parseInt(document.getElementById('calories').value),
        meals_per_day: parseInt(document.getElementById('meals-per-day').value),
        days: parseInt(document.getElementById('plan-days').value),
        diet_type: document.getElementById('diet-type').value,
        exclusions: getSelectedExclusions(),
        macros: {
            protein: parseInt(document.getElementById('protein-slider').value),
            carbs: parseInt(document.getElementById('carbs-slider').value),
            fat: parseInt(document.getElementById('fat-slider').value)
        }
    };
    
    try {
        const response = await fetch('../php/generate_meal_plan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(preferences)
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentPlanId = data.plan_id;
            displayMealPlan(data.plan);
        } else {
            mealPlanContainer.innerHTML = `<div class="error">${data.error}</div>`;
        }
    } catch (error) {
        console.error('Error generating meal plan:', error);
        mealPlanContainer.innerHTML = '<div class="error">Failed to generate meal plan. Please try again.</div>';
    }
}

// Display the meal plan
function displayMealPlan(planData) {
    // Update section title with the number of days
    const dayCount = planData.length;
    document.querySelector('#meal-plan h2.form-subtitle').textContent = 
        `Your ${dayCount}-Day Meal Plan`;
    
    // Clear previous tab buttons
    const tabsContainer = document.getElementById('day-tabs');
    tabsContainer.innerHTML = '';
    
    // Clear previous tab content
    const tabContentContainer = document.getElementById('meal-plan');
    // Preserve the title
    const title = tabContentContainer.querySelector('h2.form-subtitle');
    tabContentContainer.innerHTML = '';
    tabContentContainer.appendChild(title);
    tabContentContainer.appendChild(tabsContainer);
    
    // Generate tab buttons and content for each day
    planData.forEach((day, index) => {
        // Create tab button
        const tabButton = document.createElement('button');
        tabButton.className = index === 0 ? 'tab-btn active' : 'tab-btn';
        tabButton.setAttribute('data-day', day.day);
        tabButton.textContent = day.day_name;
        tabsContainer.appendChild(tabButton);
        
        // Create tab content
        const tabContent = document.createElement('div');
        tabContent.className = index === 0 ? 'tab-content active' : 'tab-content';
        tabContent.id = `day-${day.day}`;
        
        // Day header
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        dayHeader.innerHTML = `
            <h3 class="day-title">${day.day_name}</h3>
            <div class="day-info">
                <span class="nutrition-badge">${day.totals.calories} cal</span>
                <span class="nutrition-badge">${day.totals.protein}g protein</span>
                <span class="nutrition-badge">${day.totals.carbs}g carbs</span>
                <span class="nutrition-badge">${day.totals.fat}g fat</span>
            </div>
        `;
        tabContent.appendChild(dayHeader);
        
        // Macro chart (simplified)
        const chartContainer = document.createElement('div');
        chartContainer.className = 'nutrition-chart';
        chartContainer.innerHTML = `
            <div class="macro-chart">
                <div class="macro-bar">
                    <div class="macro-segment protein" style="width: ${Math.round((day.totals.protein * 4 / day.totals.calories) * 100)}%">P</div>
                    <div class="macro-segment carbs" style="width: ${Math.round((day.totals.carbs * 4 / day.totals.calories) * 100)}%">C</div>
                    <div class="macro-segment fat" style="width: ${Math.round((day.totals.fat * 9 / day.totals.calories) * 100)}%">F</div>
                </div>
            </div>
        `;
        tabContent.appendChild(chartContainer);
        
        // Meals for the day
        const mealsContainer = document.createElement('div');
        mealsContainer.className = 'meals-container';
        
        day.meals.forEach((meal, mealIndex) => {
            const mealCard = document.createElement('div');
            mealCard.className = 'meal-card';
            mealCard.innerHTML = `
                <div class="meal-header">
                    <span class="meal-number">Meal ${mealIndex + 1}</span>
                    <div class="meal-actions">
                        <button class="replace-meal-btn" data-day-index="${index}" data-meal-index="${mealIndex}">
                            <i class="fas fa-sync-alt"></i> Replace
                        </button>
                    </div>
                </div>
                <div class="meal-content">
                    <img src="../${meal.image_url}" alt="${meal.name}" class="meal-image">
                    <div class="meal-details">
                        <h4 class="meal-title">${meal.name}</h4>
                        <p class="meal-description">${meal.description}</p>
                        <div class="meal-nutrition">
                            <span class="nutrition-item">
                                <i class="fas fa-fire"></i> ${meal.calories} kcal
                            </span>
                            <span class="nutrition-item">
                                <i class="fas fa-drumstick-bite"></i> ${meal.protein}g protein
                            </span>
                            <span class="nutrition-item">
                                <i class="fas fa-bread-slice"></i> ${meal.carbs}g carbs
                            </span>
                            <span class="nutrition-item">
                                <i class="fas fa-cheese"></i> ${meal.fat}g fat
                            </span>
                        </div>
                        <div class="meal-meta">
                            <span><i class="far fa-clock"></i> Prep: ${meal.prep_time} min</span>
                            <span><i class="fas fa-utensils"></i> Cook: ${meal.cook_time} min</span>
                            <span class="diet-type">${meal.dietary_type}</span>
                        </div>
                    </div>
                </div>
            `;
            mealsContainer.appendChild(mealCard);
        });
        
        tabContent.appendChild(mealsContainer);
        tabContentContainer.appendChild(tabContent);
    });
    
    // Reinitialize tab switching event listeners
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById('day-' + this.getAttribute('data-day')).classList.add('active');
        });
    });
    
    // Add event listeners for meal replacement
    document.querySelectorAll('.replace-meal-btn').forEach(button => {
        button.addEventListener('click', function() {
            const dayIndex = this.getAttribute('data-day-index');
            const mealIndex = this.getAttribute('data-meal-index');
            openReplaceMealDialog(dayIndex, mealIndex);
        });
    });
}

// Get selected food exclusions
function getSelectedExclusions() {
    const checkboxes = document.querySelectorAll('input[name="exclusions"]:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

// Open dialog to replace a meal
async function openReplaceMealDialog(dayIndex, mealIndex) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('replace-meal-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'replace-meal-modal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h3>Replace Meal</h3>
                <div class="filter-options">
                    <select id="filter-diet-type">
                        <option value="any">Any Diet Type</option>
                        <option value="vegetarian">Vegetarian</option>
                        <option value="vegan">Vegan</option>
                        <option value="paleo">Paleo</option>
                        <option value="keto">Keto</option>
                    </select>
                    <input type="text" id="recipe-search" placeholder="Search recipes...">
                </div>
                <div class="recipes-container"></div>
                <div class="modal-loading">Loading recipes...</div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Modal close button
        modal.querySelector('.close-modal').addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Filter and search event listeners
        const dietTypeFilter = document.getElementById('filter-diet-type');
        const searchInput = document.getElementById('recipe-search');
        
        dietTypeFilter.addEventListener('change', function() {
            loadReplacementRecipes(dayIndex, mealIndex);
        });
        
        searchInput.addEventListener('input', function() {
            loadReplacementRecipes(dayIndex, mealIndex);
        });
    }
    
    // Show the modal
    modal.style.display = 'block';
    
    // Store the day and meal indices as data attributes
    modal.setAttribute('data-day-index', dayIndex);
    modal.setAttribute('data-meal-index', mealIndex);
    
    // Load recipes
    loadReplacementRecipes(dayIndex, mealIndex);
}

// Load replacement recipes
async function loadReplacementRecipes(dayIndex, mealIndex) {
    const modal = document.getElementById('replace-meal-modal');
    const recipesContainer = modal.querySelector('.recipes-container');
    const loadingIndicator = modal.querySelector('.modal-loading');
    
    recipesContainer.innerHTML = '';
    loadingIndicator.style.display = 'block';
    
    // Get filter values
    const dietType = document.getElementById('filter-diet-type').value;
    const searchTerm = document.getElementById('recipe-search').value;
    
    // Build query URL with filters
    let url = '../php/get_recipes.php';
    const params = new URLSearchParams();
    if (dietType && dietType !== 'any') {
        params.append('diet_type', dietType);
    }
    if (searchTerm) {
        params.append('search', searchTerm);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        loadingIndicator.style.display = 'none';
        
        if (data.success && data.recipes.length > 0) {
            data.recipes.forEach(recipe => {
                const recipeCard = document.createElement('div');
                recipeCard.className = 'recipe-card';
                recipeCard.innerHTML = `
                    <div class="recipe-card-content">
                        <img src="../${recipe.image_url}" alt="${recipe.name}" class="recipe-image">
                        <div class="recipe-details">
                            <h4 class="recipe-title">${recipe.name}</h4>
                            <div class="recipe-nutrition">
                                <span>${recipe.calories} cal</span>
                                <span>${recipe.protein}g protein</span>
                                <span>${recipe.carbs}g carbs</span>
                                <span>${recipe.fat}g fat</span>
                            </div>
                            <button class="select-recipe-btn" data-recipe-id="${recipe.id}">Select</button>
                        </div>
                    </div>
                `;
                recipesContainer.appendChild(recipeCard);
                
                // Add event listener to select button
                recipeCard.querySelector('.select-recipe-btn').addEventListener('click', function() {
                    replaceMeal(dayIndex, mealIndex, recipe.id);
                });
            });
        } else {
            recipesContainer.innerHTML = '<div class="no-recipes">No recipes found matching your criteria.</div>';
        }
    } catch (error) {
        console.error('Error loading recipes:', error);
        loadingIndicator.style.display = 'none';
        recipesContainer.innerHTML = '<div class="error">Failed to load recipes. Please try again.</div>';
    }
}

// Replace a meal in the plan
async function replaceMeal(dayIndex, mealIndex, recipeId) {
    const modal = document.getElementById('replace-meal-modal');
    const loadingIndicator = modal.querySelector('.modal-loading');
    
    // Show loading
    loadingIndicator.innerHTML = 'Replacing meal...';
    loadingIndicator.style.display = 'block';
    
    try {
        const response = await fetch('../php/replace_meal.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                plan_id: currentPlanId,
                day_index: dayIndex,
                meal_index: mealIndex,
                new_recipe_id: recipeId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            modal.style.display = 'none';
            
            // Update the displayed meal plan
            displayMealPlan(data.updated_plan);
        } else {
            loadingIndicator.innerHTML = `Error: ${data.error}`;
        }
    } catch (error) {
        console.error('Error replacing meal:', error);
        loadingIndicator.innerHTML = 'Failed to replace meal. Please try again.';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sliders
    initializeSliders();

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

    // Recipe swap modal
    const swapBtns = document.querySelectorAll('.recipe-swap');
    const modal = document.getElementById('recipe-swap-modal');
    const closeModal = document.querySelector('.close-modal');
    
    swapBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'block';
            // Store reference to which meal is being swapped
            modal.dataset.mealCard = this.closest('.meal-card').id;
        });
    });
    
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Generate Meal Plan button
    const generateBtn = document.getElementById('generate-plan-btn');
    generateBtn.addEventListener('click', generateMealPlan);

    // Save Meal Plan button
    const saveBtn = document.getElementById('save-plan-btn');
    saveBtn.addEventListener('click', saveMealPlan);

    // Export to PDF button
    const exportBtn = document.getElementById('export-plan-btn');
    exportBtn.addEventListener('click', exportToPDF);

    // Generate Grocery List button
    const groceryBtn = document.getElementById('export-groceries-btn');
    groceryBtn.addEventListener('click', generateGroceryList);
});

function initializeSliders() {
    // Calories slider
    const caloriesSlider = document.getElementById('calories-slider');
    const caloriesInput = document.getElementById('calories');
    
    if (caloriesSlider && caloriesInput) {
        caloriesSlider.addEventListener('input', function() {
            caloriesInput.value = this.value;
        });
        
        caloriesInput.addEventListener('input', function() {
            caloriesSlider.value = this.value;
        });
    }
    
    // Macros sliders
    const proteinSlider = document.getElementById('protein-slider');
    const carbsSlider = document.getElementById('carbs-slider');
    const fatSlider = document.getElementById('fat-slider');
    
    const proteinValue = document.getElementById('protein-value');
    const carbsValue = document.getElementById('carbs-value');
    const fatValue = document.getElementById('fat-value');
    
    if (proteinSlider && carbsSlider && fatSlider) {
        // Update displayed values when sliders change
        proteinSlider.addEventListener('input', updateMacroValues);
        carbsSlider.addEventListener('input', updateMacroValues);
        fatSlider.addEventListener('input', updateMacroValues);
        
        // Initial update
        updateMacroValues();
    }
    
    function updateMacroValues() {
        // Get values
        let protein = parseInt(proteinSlider.value);
        let carbs = parseInt(carbsSlider.value);
        let fat = parseInt(fatSlider.value);
        
        // Adjust to ensure they sum to 100%
        const total = protein + carbs + fat;
        if (total !== 100) {
            // Adjust the last moved slider
            const lastMoved = document.activeElement;
            
            if (lastMoved === proteinSlider) {
                // Adjust carbs and fat proportionally
                const nonProtein = 100 - protein;
                const carbsRatio = carbs / (carbs + fat);
                carbs = Math.round(nonProtein * carbsRatio);
                fat = 100 - protein - carbs;
                
                carbsSlider.value = carbs;
                fatSlider.value = fat;
            } else if (lastMoved === carbsSlider) {
                // Adjust protein and fat proportionally
                const nonCarbs = 100 - carbs;
                const proteinRatio = protein / (protein + fat);
                protein = Math.round(nonCarbs * proteinRatio);
                fat = 100 - protein - carbs;
                
                proteinSlider.value = protein;
                fatSlider.value = fat;
            } else {
                // Adjust protein and carbs proportionally
                const nonFat = 100 - fat;
                const proteinRatio = protein / (protein + carbs);
                protein = Math.round(nonFat * proteinRatio);
                carbs = 100 - protein - fat;
                
                proteinSlider.value = protein;
                carbsSlider.value = carbs;
            }
        }
        
        // Update display values
        proteinValue.textContent = proteinSlider.value;
        carbsValue.textContent = carbsSlider.value;
        fatValue.textContent = fatSlider.value;
    }
}

function generateMealPlan() {
    // Show loading state
    document.getElementById('generate-plan-btn').classList.add('loading');
    document.getElementById('generate-plan-btn').textContent = 'Generating...';
    
    // Get form values
    const calories = document.getElementById('calories').value;
    const mealsPerDay = document.getElementById('meals-per-day').value;
    const planDays = document.getElementById('plan-days').value;
    const dietType = document.getElementById('diet-type').value;
    
    // Get macros
    const protein = document.getElementById('protein-slider').value;
    const carbs = document.getElementById('carbs-slider').value;
    const fat = document.getElementById('fat-slider').value;
    
    // Get exclusions
    const exclusions = [];
    document.querySelectorAll('input[name="exclusions"]:checked').forEach(checkbox => {
        exclusions.push(checkbox.value);
    });
    
    // Prepare data for API request
    const requestData = {
        calories: calories,
        meals_per_day: mealsPerDay,
        days: planDays,
        diet_type: dietType,
        macros: {
            protein: protein,
            carbs: carbs,
            fat: fat
        },
        exclusions: exclusions
    };
    
    // Make API request to generate plan
    fetch('../php/generate_meal_plan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI with the generated meal plan
            updateMealPlanUI(data.plan);
            // Show the meal plan section
            document.getElementById('meal-plan').style.display = 'block';
            // Scroll to meal plan
            document.getElementById('meal-plan').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Error generating meal plan: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating your meal plan.');
    })
    .finally(() => {
        // Reset button state
        document.getElementById('generate-plan-btn').classList.remove('loading');
        document.getElementById('generate-plan-btn').textContent = 'Generate Meal Plan';
    });
}

function updateMealPlanUI(planData) {
    // Update day tabs
    const dayTabs = document.getElementById('day-tabs');
    dayTabs.innerHTML = '';
    
    const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    for (let i = 0; i < planData.length; i++) {
        const dayName = i < dayNames.length ? dayNames[i] : `Day ${i + 1}`;
        const activeClass = i === 0 ? 'active' : '';
        
        dayTabs.innerHTML += `
            <button class="tab-btn ${activeClass}" data-day="${i + 1}">${dayName}</button>
        `;
    }
    
    // Create content for each day
    const mealPlanElement = document.getElementById('meal-plan');
    
    // Clear existing content (except tabs)
    const tabContents = mealPlanElement.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.remove();
    });
    
    // Add content for each day
    for (let i = 0; i < planData.length; i++) {
        const day = planData[i];
        const dayName = i < dayNames.length ? dayNames[i] : `Day ${i + 1}`;
        const activeClass = i === 0 ? 'active' : '';
        
        const dayContent = document.createElement('div');
        dayContent.className = `tab-content ${activeClass}`;
        dayContent.id = `day-${i + 1}`;
        
        // Day header with nutrition info
        dayContent.innerHTML = `
            <div class="meal-day">
                <div class="day-header">
                    <h3 class="day-title">${dayName}</h3>
                    <div class="day-info">
                        <span class="nutrition-badge">${day.totals.calories} cal</span>
                        <span class="nutrition-badge">${day.totals.protein}g protein</span>
                        <span class="nutrition-badge">${day.totals.carbs}g carbs</span>
                        <span class="nutrition-badge">${day.totals.fat}g fat</span>
                    </div>
                </div>
                
                <div class="nutrition-chart">
                    <div class="macro-chart">
                        <div class="macro-bar protein" style="width: ${Math.round((day.totals.protein * 4 / day.totals.calories) * 100)}%;">
                            ${Math.round((day.totals.protein * 4 / day.totals.calories) * 100)}% Protein
                        </div>
                        <div class="macro-bar carbs" style="width: ${Math.round((day.totals.carbs * 4 / day.totals.calories) * 100)}%;">
                            ${Math.round((day.totals.carbs * 4 / day.totals.calories) * 100)}% Carbs
                        </div>
                        <div class="macro-bar fat" style="width: ${Math.round((day.totals.fat * 9 / day.totals.calories) * 100)}%;">
                            ${Math.round((day.totals.fat * 9 / day.totals.calories) * 100)}% Fat
                        </div>
                    </div>
                </div>
                
                <div class="meal-list">
                    <!-- Meals will be added here -->
                </div>
            </div>
        `;
        
        // Add meals
        const mealList = dayContent.querySelector('.meal-list');
        const mealNames = ['Breakfast', 'Lunch', 'Dinner', 'Snack 1', 'Snack 2', 'Snack 3'];
        
        day.meals.forEach((meal, mealIndex) => {
            const mealName = mealIndex < mealNames.length ? mealNames[mealIndex] : `Meal ${mealIndex + 1}`;
            
            const mealCard = document.createElement('div');
            mealCard.className = 'meal-card';
            mealCard.id = `day-${i + 1}-meal-${mealIndex}`;
            
            mealCard.innerHTML = `
                <h4 class="meal-title">${mealName}</h4>
                <div class="recipe-card">
                    <img src="${meal.image_url || '../img/recipe-placeholder.jpg'}" alt="${meal.name}" class="recipe-image">
                    <div class="recipe-content">
                        <h5 class="recipe-title">${meal.name}</h5>
                        <div class="recipe-info">
                            <span>${meal.prep_time + meal.cook_time} mins</span>
                            <span>${meal.calories} cal</span>
                            <span>${meal.protein}g protein</span>
                        </div>
                        <button class="btn btn-secondary btn-sm recipe-swap">Swap Recipe</button>
                    </div>
                </div>
            `;
            
            mealList.appendChild(mealCard);
        });
        
        mealPlanElement.appendChild(dayContent);
    }
    
    // Reattach event listeners for tabs and swap buttons
    attachEventListeners();
}

function attachEventListeners() {
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
    
    // Recipe swap buttons
    const swapBtns = document.querySelectorAll('.recipe-swap');
    const modal = document.getElementById('recipe-swap-modal');
    
    swapBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'block';
            // Store reference to which meal is being swapped
            modal.dataset.mealCard = this.closest('.meal-card').id;
        });
    });
}

function saveMealPlan() {
    // Show loading state
    const saveBtn = document.getElementById('save-plan-btn');
    saveBtn.classList.add('loading');
    saveBtn.textContent = 'Saving...';
    
    // Get plan data from the UI
    const planData = [];
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabContents.forEach(dayContent => {
        // Get day information
        const dayHeader = dayContent.querySelector('.day-header');
        const dayTitle = dayHeader.querySelector('.day-title').textContent;
        
        // Get nutrition totals
        const nutritionBadges = dayHeader.querySelectorAll('.nutrition-badge');
        const calories = parseInt(nutritionBadges[0].textContent);
        const protein = parseInt(nutritionBadges[1].textContent);
        const carbs = parseInt(nutritionBadges[2].textContent);
        const fat = parseInt(nutritionBadges[3].textContent);
        
        // Get meals
        const mealCards = dayContent.querySelectorAll('.meal-card');
        const meals = [];
        
        mealCards.forEach(card => {
            const title = card.querySelector('.recipe-title').textContent;
            const infoSpans = card.querySelectorAll('.recipe-info span');
            
            // Extract time, calories, protein
            const timeStr = infoSpans[0].textContent;
            const caloriesStr = infoSpans[1].textContent;
            const proteinStr = infoSpans[2].textContent;
            
            const time = parseInt(timeStr);
            const mealCalories = parseInt(caloriesStr);
            const mealProtein = parseInt(proteinStr);
            
            // Get image URL
            const imgSrc = card.querySelector('.recipe-image').getAttribute('src');
            
            meals.push({
                id: card.id,
                name: title,
                calories: mealCalories,
                protein: mealProtein,
                carbs: Math.round(mealCalories * 0.5 / 4), // Estimated
                fat: Math.round(mealCalories * 0.3 / 9),  // Estimated
                image_url: imgSrc,
                prep_time: Math.round(time * 0.4),  // Estimated
                cook_time: Math.round(time * 0.6)   // Estimated
            });
        });
        
        planData.push({
            day: dayTitle,
            totals: {
                calories: calories,
                protein: protein,
                carbs: carbs,
                fat: fat
            },
            meals: meals
        });
    });
    
    // Prepare data for API request
    const requestData = {
        plan_name: 'My Meal Plan - ' + new Date().toLocaleDateString(),
        plan_data: planData
    };
    
    // Make API request to save plan
    fetch('save_plan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            
            // Redirect to view the saved plan
            if (data.plan_id) {
                window.location.href = `view_plan.php?id=${data.plan_id}`;
            }
        } else {
            alert('Error saving meal plan: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving your meal plan.');
    })
    .finally(() => {
        // Reset button state
        saveBtn.classList.remove('loading');
        saveBtn.textContent = 'Save Meal Plan';
    });
}

function exportToPDF() {
    alert('Export to PDF functionality will be implemented soon!');
    // In a real implementation, this would use a library like jsPDF or make a server request
}

function generateGroceryList() {
    alert('Generate Grocery List functionality will be implemented soon!');
    // In a real implementation, this would extract ingredients from each recipe and categorize them
}
