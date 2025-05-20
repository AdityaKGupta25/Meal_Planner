<?php
session_start();
require_once '../php/db_connect.php';
require_once '../php/error_handler.php';

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
    
    // Extract parameters
    $plan_name = isset($data['plan_name']) ? $conn->real_escape_string($data['plan_name']) : 'My Meal Plan';
    $plan_data = isset($data['plan_data']) ? json_encode($data['plan_data']) : null;
    $plan_id = isset($data['plan_id']) ? (int)$data['plan_id'] : null;
    
    // Validate parameters
    if (empty($plan_data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required plan data']);
        exit();
    }
    
    // Check if this is an update or a new plan
    if ($plan_id) {
        // Update existing plan
        $stmt = $conn->prepare("
            UPDATE meal_plans 
            SET plan_name = ?, plan_data = ?, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ssii", $plan_name, $plan_data, $plan_id, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Meal plan updated successfully',
                'plan_id' => $plan_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update meal plan: ' . $stmt->error]);
        }
    } else {
        // Insert new plan
        $stmt = $conn->prepare("
            INSERT INTO meal_plans (user_id, plan_name, plan_data, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("iss", $user_id, $plan_name, $plan_data);
        
        if ($stmt->execute()) {
            $new_plan_id = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Meal plan saved successfully',
                'plan_id' => $new_plan_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save meal plan: ' . $stmt->error]);
        }
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method or empty data']);
}

$conn->close();
?> 