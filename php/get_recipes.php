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

// Get filter parameters
$diet_type = isset($_GET['diet_type']) ? htmlspecialchars($_GET['diet_type']) : null;
$exclusion = isset($_GET['exclusion']) ? htmlspecialchars($_GET['exclusion']) : null;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : null;

// Build query conditions for selecting recipes
$conditions = [];
$params = [];
$types = '';

// Diet type filter
if ($diet_type && $diet_type !== 'any') {
    $conditions[] = "dietary_type = ?";
    $params[] = $diet_type;
    $types .= 's';
}

// Exclusion filter
if ($exclusion) {
    $conditions[] = "NOT JSON_CONTAINS(exclusions, ?, '$')";
    $params[] = json_encode($exclusion);
    $types .= 's';
}

// Search filter
if ($search) {
    $conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

// Build the WHERE clause
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Prepare SQL statement
$sql = "SELECT * FROM recipes $where_clause ORDER BY name ASC";
$stmt = $conn->prepare($sql);

// Bind parameters if there are any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

// Fetch all recipes
$recipes = [];
while ($row = $result->fetch_assoc()) {
    // Parse JSON fields
    $row['ingredients'] = json_decode($row['ingredients'], true);
    $row['exclusions'] = json_decode($row['exclusions'], true);
    $recipes[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($recipes),
    'recipes' => $recipes
]);

$stmt->close();
$conn->close();
?> 