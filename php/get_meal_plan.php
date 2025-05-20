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

// Optional parameter: specific plan ID
$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : null;

// Prepare SQL statement to get the most recent meal plan or a specific one
if ($plan_id) {
    $stmt = $conn->prepare("
        SELECT * FROM meal_plans 
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $plan_id, $user_id);
} else {
    $stmt = $conn->prepare("
        SELECT * FROM meal_plans 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $plan = $result->fetch_assoc();
    
    // Decode the JSON plan data
    $plan['plan_data'] = json_decode($plan['plan_data'], true);
    
    echo json_encode([
        'success' => true,
        'plan' => $plan
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No meal plan found for this user'
    ]);
}

$stmt->close();
$conn->close();
?> 