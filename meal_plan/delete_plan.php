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

// Check if plan ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['plan_message'] = "Invalid meal plan ID.";
    header("Location: saved_plans.php");
    exit();
}

$plan_id = (int)$_GET['id'];

// Check if the plan exists and belongs to the user
$check_sql = "SELECT id FROM meal_plans WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $plan_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['plan_message'] = "Meal plan not found or you don't have permission to delete it.";
    header("Location: saved_plans.php");
    exit();
}

// Delete the plan
$delete_sql = "DELETE FROM meal_plans WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $plan_id, $user_id);

if ($delete_stmt->execute()) {
    $_SESSION['plan_message'] = "Meal plan deleted successfully.";
} else {
    $_SESSION['plan_message'] = "Error deleting meal plan: " . $conn->error;
}

// Redirect back to the saved plans page
header("Location: saved_plans.php");
exit();
?> 