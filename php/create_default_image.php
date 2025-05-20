<?php
// Create profiles directory if it doesn't exist
$dir = '../img/profiles/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Path to save the default profile image
$default_image_path = $dir . 'default.jpg';

// If the default image already exists, stop execution
if (file_exists($default_image_path)) {
    echo "Default profile image already exists.";
    exit();
}

// Create a blank image with dimensions 200x200
$image = imagecreatetruecolor(200, 200);

// Define colors
$bg_color = imagecolorallocate($image, 74, 108, 247); // #4a6cf7 (blue)
$text_color = imagecolorallocate($image, 255, 255, 255); // White

// Fill the background
imagefill($image, 0, 0, $bg_color);

// Add a circle in the center
$circle_color = imagecolorallocate($image, 255, 255, 255);
imagefilledellipse($image, 100, 100, 120, 120, $circle_color);

// Add a silhouette
$silhouette_color = imagecolorallocate($image, 200, 200, 200);
imagefilledellipse($image, 100, 75, 60, 60, $silhouette_color); // Head
imagefilledrectangle($image, 70, 105, 130, 160, $silhouette_color); // Body

// Save the image as JPG
imagejpeg($image, $default_image_path, 100);

// Free up memory
imagedestroy($image);

echo "Default profile image created successfully.";
?> 