<?php
/**
 * Get Random Food Image
 * 
 * This script generates random food images using Unsplash API
 * as a fallback if no image is available
 */

// Function to get a random food image URL
function getRandomFoodImage($query = 'food') {
    // Sanitize the query for URL
    $query = urlencode($query);
    
    // Using Unsplash API (you should use your own API key in production)
    // For this demo, we're using a limited demo key
    $accessKey = 'demo123456789'; // Replace with your actual Unsplash API key
    
    // Create cache directory if it doesn't exist
    $cacheDir = '../img/cache';
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    
    // Try to use cached images first (to avoid API rate limits)
    $cachedImages = glob("$cacheDir/*.jpg");
    if (count($cachedImages) > 0) {
        // Return a random image from cache
        $randomImage = $cachedImages[array_rand($cachedImages)];
        return str_replace('../', '', $randomImage);
    }
    
    // Fallback to predefined food image URLs if API is not available
    $fallbackImages = [
        'img/meals/breakfast-1.jpg',
        'img/meals/lunch-1.jpg',
        'img/meals/dinner-1.jpg',
        'img/meals/snack-1.jpg',
        'img/meals/breakfast-2.jpg',
        'img/meals/lunch-2.jpg',
        'img/meals/dinner-2.jpg',
        'img/meals/snack-2.jpg',
    ];
    
    return $fallbackImages[array_rand($fallbackImages)];
}

// If this file is called directly, return a random image URL
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(['image_url' => getRandomFoodImage($_GET['query'] ?? 'food')]);
}
?> 