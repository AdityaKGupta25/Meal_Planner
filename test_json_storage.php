<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>JSON Storage Test</h1>";

// Include JSON database connection
require_once 'php/db_connect_json.php';
require_once 'php/meal_plan_generator.php';

// Check if data directory exists
$data_dir = __DIR__ . '/data';

echo "<h2>1. Storage System Test</h2>";
if (file_exists($data_dir)) {
    echo "<p style='color:green'>✅ Data directory exists at $data_dir</p>";
    
    // Check for JSON files
    $recipes_file = $data_dir . '/recipes.json';
    $users_file = $data_dir . '/users.json';
    $plans_file = $data_dir . '/meal_plans.json';
    
    if (file_exists($recipes_file)) {
        echo "<p style='color:green'>✅ Recipes file exists</p>";
        
        $recipes = json_decode(file_get_contents($recipes_file), true);
        echo "<p>Found " . count($recipes) . " recipes in the database.</p>";
        
        // Show sample recipes
        if (!empty($recipes)) {
            echo "<h3>Sample Recipes:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Name</th><th>Meal Type</th><th>Calories</th></tr>";
            
            $count = 0;
            foreach ($recipes as $recipe) {
                if ($count++ >= 5) break;
                echo "<tr>";
                echo "<td>" . $recipe['id'] . "</td>";
                echo "<td>" . $recipe['name'] . "</td>";
                echo "<td>" . $recipe['meal_type'] . "</td>";
                echo "<td>" . $recipe['calories'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "<p style='color:red'>❌ Recipes file does not exist</p>";
    }
    
    if (file_exists($users_file)) {
        echo "<p style='color:green'>✅ Users file exists</p>";
    } else {
        echo "<p style='color:red'>❌ Users file does not exist</p>";
    }
    
    if (file_exists($plans_file)) {
        echo "<p style='color:green'>✅ Meal plans file exists</p>";
    } else {
        echo "<p style='color:red'>❌ Meal plans file does not exist</p>";
    }
} else {
    echo "<p style='color:red'>❌ Data directory does not exist</p>";
}

// Test database wrapper functions
echo "<h2>2. Database Wrapper Functions Test</h2>";

// Test SELECT query
$result = db_query("SELECT * FROM recipes LIMIT 5");
if ($result) {
    echo "<p style='color:green'>✅ SELECT query works</p>";
    
    $rows = db_fetch_all($result);
    echo "<p>Fetched " . count($rows) . " recipes</p>";
} else {
    echo "<p style='color:red'>❌ SELECT query failed</p>";
}

// Test COUNT query
$result = db_query("SELECT COUNT(*) as count FROM recipes");
if ($result) {
    $row = db_fetch_assoc($result);
    if (isset($row['count'])) {
        echo "<p style='color:green'>✅ COUNT query works. Found {$row['count']} recipes</p>";
    } else {
        echo "<p style='color:red'>❌ COUNT query returned invalid data</p>";
    }
} else {
    echo "<p style='color:red'>❌ COUNT query failed</p>";
}

// Try to generate a simple meal plan
echo "<h2>3. Meal Plan Generation Test</h2>";
try {
    $preferences = [
        'days' => 1,
        'calories_per_day' => 2000,
        'meal_types' => ['breakfast', 'lunch', 'dinner']
    ];
    
    echo "<p>Attempting to generate a 1-day meal plan...</p>";
    
    $plan = generateMealPlan($preferences);
    
    if ($plan && !empty($plan)) {
        echo "<p style='color:green'>✅ Successfully generated a meal plan!</p>";
        
        echo "<h3>Meal Plan Details:</h3>";
        echo "<pre>";
        print_r($plan);
        echo "</pre>";
    } else {
        echo "<p style='color:red'>❌ Failed to generate a meal plan. The function returned empty.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error generating meal plan: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></p>";
}

// Final instructions
echo "<h2>Next Steps</h2>";
echo "<p>If all tests passed, you're ready to use the meal planner with JSON storage.</p>";
echo "<ol>";
echo "<li><a href='meal_plan/update_for_json.php'>Update Meal Plan Interface for JSON Storage</a></li>";
echo "<li><a href='meal_plan/index.php'>Go to Meal Plan Generator</a></li>";
echo "</ol>";
?>