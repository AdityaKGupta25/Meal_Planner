<?php
// Include database connection
require_once 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

// Check if file was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $user_id = $_SESSION['user_id'];
    
    // File properties
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_size = $_FILES['profile_image']['size'];
    $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    
    // Check file size (limit to 2MB)
    if ($file_size > 2097152) {
        $_SESSION['profile_error'] = "File is too large. Maximum size is 2MB.";
        header("Location: ../profile.php");
        exit();
    }
    
    // Check file extension
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['profile_error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        header("Location: ../profile.php");
        exit();
    }
    
    // Create profile images directory if it doesn't exist
    $upload_dir = '../img/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $new_filename = $user_id . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        // Update database with new image filename
        $sql = "UPDATE users SET profile_image = '$new_filename' WHERE id = $user_id";
        
        if ($conn->query($sql) === TRUE) {
            // Get the old profile image
            $get_old_img = "SELECT profile_image FROM users WHERE id = $user_id";
            $result = $conn->query($get_old_img);
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $old_image = $row['profile_image'];
                
                // Delete old profile image if it's not the default
                if ($old_image !== 'default.jpg' && $old_image !== $new_filename) {
                    $old_path = $upload_dir . $old_image;
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }
            
            $_SESSION['profile_message'] = "Profile picture updated successfully.";
        } else {
            $_SESSION['profile_error'] = "Error updating profile image in database: " . $conn->error;
        }
    } else {
        $_SESSION['profile_error'] = "Error uploading image. Please try again.";
    }
} else {
    $_SESSION['profile_error'] = "No image selected or error uploading file.";
}

header("Location: ../profile.php");
exit();
?> 