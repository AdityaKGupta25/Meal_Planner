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
    
    // Sanitize inputs
    $sex = isset($data['sex']) ? htmlspecialchars($data['sex']) : null;
    $height_ft = isset($data['height_ft']) ? (int)$data['height_ft'] : null;
    $height_in = isset($data['height_in']) ? (int)$data['height_in'] : null;
    $weight = isset($data['weight']) ? (int)$data['weight'] : null;
    $age = isset($data['age']) ? (int)$data['age'] : null;
    $bodyfat = isset($data['bodyfat']) ? htmlspecialchars($data['bodyfat']) : null;
    $activity_level = isset($data['activity_level']) ? htmlspecialchars($data['activity_level']) : null;
    $diet_type = isset($data['diet_type']) ? htmlspecialchars($data['diet_type']) : null;
    
    // Convert arrays to JSON strings
    $dietary_preferences = isset($data['dietary_preferences']) ? json_encode($data['dietary_preferences']) : null;
    $allergies = isset($data['allergies']) ? json_encode($data['allergies']) : null;
    
    // Prepare SQL statement using prepared statements
    $stmt = $conn->prepare("
        INSERT INTO user_preferences 
            (user_id, sex, height_ft, height_in, weight, age, bodyfat, activity_level, diet_type, dietary_preferences, allergies, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
            sex = VALUES(sex),
            height_ft = VALUES(height_ft),
            height_in = VALUES(height_in),
            weight = VALUES(weight),
            age = VALUES(age),
            bodyfat = VALUES(bodyfat),
            activity_level = VALUES(activity_level),
            diet_type = VALUES(diet_type),
            dietary_preferences = VALUES(dietary_preferences),
            allergies = VALUES(allergies),
            updated_at = NOW()
    ");
    
    // Bind parameters
    $stmt->bind_param(
        "isiiisssss",
        $user_id,
        $sex,
        $height_ft,
        $height_in,
        $weight,
        $age,
        $bodyfat,
        $activity_level,
        $diet_type,
        $dietary_preferences,
        $allergies
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Preferences saved successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method or empty data']);
}

$conn->close();
?> 