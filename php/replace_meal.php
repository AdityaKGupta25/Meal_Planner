<?php
session_start();
require_once 'db_connect.php';
require_once 'get_random_image.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized', 'success' => false]);
    exit();
}

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data', 'success' => false]);
    exit();
}

// Extract parameters
$user_id = $_SESSION['user_id'];
$plan_id = isset($data['plan_id']) ? (int)$data['plan_id'] : 0;
$day_index = isset($data['day_index']) ? (int)$data['day_index'] : -1;
$meal_index = isset($data['meal_index']) ? (int)$data['meal_index'] : -1;
$new_recipe_id = isset($data['new_recipe_id']) ? (int)$data['new_recipe_id'] : 0;

// Validate parameters
if ($plan_id <= 0 || $day_index < 0 || $meal_index < 0 || $new_recipe_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters', 'success' => false]);
    exit();
}

// First, get the plan data
$plan_sql = "SELECT * FROM meal_plans WHERE id = ? AND user_id = ?";
$plan_stmt = $conn->prepare($plan_sql);
$plan_stmt->bind_param("ii", $plan_id, $user_id);
$plan_stmt->execute();
$plan_result = $plan_stmt->get_result();

if ($plan_result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Plan not found or you do not have permission', 'success' => false]);
    exit();
}

$plan = $plan_result->fetch_assoc();
$plan_data = json_decode($plan['plan_data'], true);

// Validate day and meal indexes
if (!isset($plan_data[$day_index]) || !isset($plan_data[$day_index]['meals'][$meal_index])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid day or meal index', 'success' => false]);
    exit();
}

// Now get the new recipe data
$recipe_sql = "SELECT * FROM recipes WHERE id = ?";
$recipe_stmt = $conn->prepare($recipe_sql);
$recipe_stmt->bind_param("i", $new_recipe_id);
$recipe_stmt->execute();
$recipe_result = $recipe_stmt->get_result();

if ($recipe_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Recipe not found', 'success' => false]);
    exit();
}

$recipe = $recipe_result->fetch_assoc();

// Get random image for the recipe if it doesn't have one
if (empty($recipe['image_url'])) {
    $recipe['image_url'] = getRandomFoodImage($recipe['name']);
    
    // Optionally save this image URL to the recipe in the database
    $update_image_sql = "UPDATE recipes SET image_url = ? WHERE id = ?";
    $update_image_stmt = $conn->prepare($update_image_sql);
    $update_image_stmt->bind_param("si", $recipe['image_url'], $new_recipe_id);
    $update_image_stmt->execute();
    $update_image_stmt->close();
}

// Update the plan data with the new recipe
$old_meal = $plan_data[$day_index]['meals'][$meal_index];
$plan_data[$day_index]['meals'][$meal_index] = [
    'id' => $recipe['id'],
    'name' => $recipe['name'],
    'description' => $recipe['description'],
    'image_url' => $recipe['image_url'],
    'calories' => $recipe['calories'],
    'protein' => $recipe['protein'],
    'carbs' => $recipe['carbs'],
    'fat' => $recipe['fat'],
    'prep_time' => $recipe['prep_time'],
    'cook_time' => $recipe['cook_time']
];

// Update the day's nutrition totals
$day_totals = &$plan_data[$day_index]['totals'];
$day_totals['calories'] = $day_totals['calories'] - $old_meal['calories'] + $recipe['calories'];
$day_totals['protein'] = $day_totals['protein'] - $old_meal['protein'] + $recipe['protein'];
$day_totals['carbs'] = $day_totals['carbs'] - $old_meal['carbs'] + $recipe['carbs'];
$day_totals['fat'] = $day_totals['fat'] - $old_meal['fat'] + $recipe['fat'];

// Save the updated plan back to the database
$updated_plan_data = json_encode($plan_data);
$update_sql = "UPDATE meal_plans SET plan_data = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("sii", $updated_plan_data, $plan_id, $user_id);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Meal successfully replaced',
        'updated_plan' => $plan_data
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to update meal plan: ' . $update_stmt->error,
        'success' => false
    ]);
}

// Close all statements
$plan_stmt->close();
$recipe_stmt->close();
$update_stmt->close();
$conn->close();
?> 