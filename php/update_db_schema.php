<?php
// Include database connection
require_once 'db_connect.php';

// Add is_favorite column to meal_plans table if it doesn't exist
$checkColumn = $conn->query("SHOW COLUMNS FROM meal_plans LIKE 'is_favorite'");

if ($checkColumn->num_rows == 0) {
    $addColumn = $conn->query("ALTER TABLE meal_plans ADD COLUMN is_favorite TINYINT(1) NOT NULL DEFAULT 0");
    
    if ($addColumn) {
        echo "Successfully added is_favorite column to meal_plans table.<br>";
    } else {
        echo "Error adding is_favorite column: " . $conn->error . "<br>";
    }
} else {
    echo "is_favorite column already exists in meal_plans table.<br>";
}

// Make sure image_url is available in recipes table
$checkImgColumn = $conn->query("SHOW COLUMNS FROM recipes LIKE 'image_url'");

if ($checkImgColumn->num_rows == 0) {
    $addImgColumn = $conn->query("ALTER TABLE recipes ADD COLUMN image_url VARCHAR(255) NULL AFTER description");
    
    if ($addImgColumn) {
        echo "Successfully added image_url column to recipes table.<br>";
    } else {
        echo "Error adding image_url column: " . $conn->error . "<br>";
    }
} else {
    echo "image_url column already exists in recipes table.<br>";
}

// Ensure meal_plans table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'meal_plans'");

if ($tableExists->num_rows == 0) {
    $createTable = $conn->query("
        CREATE TABLE meal_plans (
            id INT(11) PRIMARY KEY AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            plan_name VARCHAR(255) NOT NULL,
            plan_data LONGTEXT NOT NULL,
            is_favorite TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    if ($createTable) {
        echo "Successfully created meal_plans table.<br>";
    } else {
        echo "Error creating meal_plans table: " . $conn->error . "<br>";
    }
} else {
    echo "meal_plans table already exists.<br>";
}

// Create an images directory if it doesn't exist
$imgDir = '../img/meals';
if (!file_exists($imgDir)) {
    if (mkdir($imgDir, 0755, true)) {
        echo "Created image directory at $imgDir.<br>";
    } else {
        echo "Failed to create image directory at $imgDir.<br>";
    }
} else {
    echo "Image directory already exists at $imgDir.<br>";
}

echo "Database schema update complete.";
$conn->close();
?> 