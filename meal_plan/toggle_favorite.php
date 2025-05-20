<?php
session_start();
require_once '../php/db_connect.php';

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

// Extract and validate parameters
$user_id = $_SESSION['user_id'];
$plan_id = isset($data['plan_id']) ? (int)$data['plan_id'] : 0;
$is_favorite = isset($data['is_favorite']) ? (bool)$data['is_favorite'] : false;

if ($plan_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid plan ID', 'success' => false]);
    exit();
}

// First, verify the user owns this plan
$check_sql = "SELECT id FROM meal_plans WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $plan_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not have permission to modify this plan', 'success' => false]);
    $check_stmt->close();
    exit();
}
$check_stmt->close();

// Update the favorite status
$update_sql = "UPDATE meal_plans SET is_favorite = ? WHERE id = ? AND user_id = ?";
$update_stmt = $conn->prepare($update_sql);
$favorite_int = $is_favorite ? 1 : 0;
$update_stmt->bind_param('iii', $favorite_int, $plan_id, $user_id);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => $is_favorite ? 'Plan added to favorites' : 'Plan removed from favorites',
        'is_favorite' => $is_favorite
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to update favorite status: ' . $update_stmt->error,
        'success' => false
    ]);
}

$update_stmt->close();
$conn->close();
?> 