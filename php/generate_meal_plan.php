<?php
session_start();
require_once 'db_connect.php';
require_once 'error_handler.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

// Validate and sanitize input
$user_id = $_SESSION['user_id'];

// Check if request is POST and has JSON content
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(file_get_contents('php://input'))) {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit();
    }
    
    // Extract and sanitize meal plan preferences
    $calories = isset($data['calories']) ? (int)$data['calories'] : 2000;
    $meals_per_day = isset($data['meals_per_day']) ? (int)$data['meals_per_day'] : 3;
    $days = isset($data['days']) ? (int)$data['days'] : 7;
    $diet_type = isset($data['diet_type']) ? htmlspecialchars($data['diet_type']) : 'any';
    $exclusions = isset($data['exclusions']) ? $data['exclusions'] : [];
    
    // Macros distribution
    $protein_pct = isset($data['macros']['protein']) ? (int)$data['macros']['protein'] : 30;
    $carbs_pct = isset($data['macros']['carbs']) ? (int)$data['macros']['carbs'] : 40;
    $fat_pct = isset($data['macros']['fat']) ? (int)$data['macros']['fat'] : 30;
    
    // Validate macros add up to 100%
    if ($protein_pct + $carbs_pct + $fat_pct != 100) {
        http_response_code(400);
        echo json_encode(['error' => 'Macros must add up to 100%']);
        exit();
    }
    
    // Build query conditions for selecting appropriate recipes
    $conditions = [];
    $params = [];
    $types = '';
    
    // Diet type filter (if not 'any')
    if ($diet_type !== 'any') {
        $conditions[] = "dietary_type = ?";
        $params[] = $diet_type;
        $types .= 's';
    }
    
    // Build exclusions filter using JSON_CONTAINS
    if (!empty($exclusions)) {
        foreach ($exclusions as $exclusion) {
            $conditions[] = "JSON_CONTAINS(exclusions, ?, '$')";
            $params[] = json_encode($exclusion);
            $types .= 's';
        }
    }
    
    // Build the WHERE clause
    $where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
    
    // Prepare SQL statement to get recipes
    $sql = "SELECT * FROM recipes $where_clause";
    $stmt = $conn->prepare($sql);
    
    // Bind parameters if there are any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all matching recipes
    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    
    $stmt->close();
    
    // Check if we have enough recipes
    if (count($recipes) < $meals_per_day) {
        http_response_code(400);
        echo json_encode(['error' => 'Not enough recipes match your criteria. Please adjust your preferences.']);
        exit();
    }
    
    // Generate the meal plan
    $meal_plan = [];
    $day_names = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    // Calculate calories per meal
    $calories_per_meal = $calories / $meals_per_day;
    
    // Generate plans for each day
    for ($day = 0; $day < $days; $day++) {
        $day_index = $day % 7; // Loop back to Monday if days > 7
        $day_name = $day_names[$day_index];
        
        // Shuffle recipes to get random selections each time
        shuffle($recipes);
        
        $day_meals = [];
        $day_total_calories = 0;
        $day_total_protein = 0;
        $day_total_carbs = 0;
        $day_total_fat = 0;
        
        // Select meals for this day
        for ($meal = 0; $meal < $meals_per_day; $meal++) {
            // Get the next recipe
            $recipe_index = $meal % count($recipes);
            $recipe = $recipes[$recipe_index];
            
            // Add to day meals
            $day_meals[] = [
                'id' => $recipe['id'],
                'name' => $recipe['name'],
                'description' => $recipe['description'],
                'image_url' => $recipe['image_url'],
                'calories' => $recipe['calories'],
                'protein' => $recipe['protein'],
                'carbs' => $recipe['carbs'],
                'fat' => $recipe['fat'],
                'prep_time' => $recipe['prep_time'],
                'cook_time' => $recipe['cook_time'],
                'dietary_type' => $recipe['dietary_type']
            ];
            
            // Add to day totals
            $day_total_calories += $recipe['calories'];
            $day_total_protein += $recipe['protein'];
            $day_total_carbs += $recipe['carbs'];
            $day_total_fat += $recipe['fat'];
        }
        
        // Add day to meal plan
        $meal_plan[] = [
            'day' => $day + 1,
            'day_name' => $day_name,
            'meals' => $day_meals,
            'totals' => [
                'calories' => $day_total_calories,
                'protein' => $day_total_protein,
                'carbs' => $day_total_carbs,
                'fat' => $day_total_fat
            ]
        ];
    }
    
    // Convert the meal plan to JSON
    $plan_json = json_encode($meal_plan);
    
    // Save the meal plan to the database
    $stmt = $conn->prepare("
        INSERT INTO meal_plans 
            (user_id, plan_data, calories, days, meals_per_day, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("isiii", $user_id, $plan_json, $calories, $days, $meals_per_day);
    
    if ($stmt->execute()) {
        // Return the generated meal plan and its ID
        $plan_id = $stmt->insert_id;
        echo json_encode([
            'success' => true,
            'plan_id' => $plan_id,
            'plan' => $meal_plan
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save meal plan: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method or empty data']);
}

$conn->close();
?> 