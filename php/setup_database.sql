-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS mealplanner;

-- Use the database
USE mealplanner;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    reset_token VARCHAR(64) DEFAULT NULL,
    token_expires DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create user_preferences table (for future use)
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dietary_preferences JSON,
    allergies JSON,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert a test user (email: test@example.com, password: Test1234) 
-- Only run this if you want a test account
INSERT INTO users (fullname, email, password, created_at) 
VALUES ('Test User', 'test@example.com', '$2y$10$uJY4.4wV9QcQ.c55BpY17.ZIVqFcfVG42BxQXJFN8Iyl8Qkd2.z9W', NOW())
ON DUPLICATE KEY UPDATE id = id; 