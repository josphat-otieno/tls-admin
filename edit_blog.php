<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

// Function to handle blog image upload
function handleBlogImageUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['error' => 'file_too_large'];
            case UPLOAD_ERR_PARTIAL:
                return ['error' => 'upload_failed'];
            case UPLOAD_ERR_NO_FILE:
                return ['error' => 'no_file'];
            default:
                return ['error' => 'upload_failed'];
        }
    }

    $upload_dir = 'uploads/blog_images/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = @getimagesize($file['tmp_name']);
    if (!$file_info || !in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }
    
    // Validate file size (10MB max)
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = str_replace('image/', '', $file['type']);
    }
    $filename = uniqid('blog_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete old blog image
function deleteOldBlogImage($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}

// Function to extract YouTube video ID and create embed URL
function getYouTubeEmbedUrl($url) {
    $video_id = '';
    
    // Handle different YouTube URL formats
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    }
    
    if ($video_id) {
        return "https://www.youtube.com/embed/" . $video_id;
    }
    
    return '';
}

// Add new blog
if(isset($_POST['add_blog'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $media_type = mysqli_real_escape_string($con, $_POST['media_type']);

    $media_url = null;
    
    // Handle media based on type
    if ($media_type === 'image' && isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === 0) {
        $upload_result = handleBlogImageUpload($_FILES['blog_image']);
        if (isset($upload_result['error'])) {
            header("Location: blogs.php?status=err&msg=" . $upload_result['error']);
            exit();
        }
        $media_url = $upload_result['path'];
    } elseif ($media_type === 'video' && !empty($_POST['youtube_url'])) {
        $youtube_url = $_POST['youtube_url'];
        $embed_url = getYouTubeEmbedUrl($youtube_url);
        if (empty($embed_url)) {
            header("Location: blogs.php?status=err&msg=invalid_youtube");
            exit();
        }
        $media_url = $embed_url;
    }
    
    $media_url_value = $media_url ? "'" . mysqli_real_escape_string($con, $media_url) . "'" : "NULL";
    $media_type_value = $media_type ? "'" . $media_type . "'" : "NULL";
    
    $sql = "INSERT INTO blogs (title, content, media_type, media_url) 
            VALUES ('$title', '$content', $media_type_value, $media_url_value)";
    
    $add_blog = $con->query($sql);
    
    if (!$add_blog) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: blogs.php?status=err");
    } else {
        header("Location: blogs.php?status=succ");
    }
    exit();
}

// Update blog
if(isset($_POST['update_blog'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $media_type = mysqli_real_escape_string($con, $_POST['media_type']);

    $old_media_url = $_POST['old_media_url'] ?? '';
    $media_url = $old_media_url;
    
    // Handle media based on type
    if ($media_type === 'image') {
        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === 0) {
            // New image uploaded
            $upload_result = handleBlogImageUpload($_FILES['blog_image']);
            if (isset($upload_result['error'])) {
                header("Location: blogs.php?status=err&msg=" . $upload_result['error']);
                exit();
            }
            // Delete old image if it exists
            if (!empty($old_media_url)) {
                deleteOldBlogImage($old_media_url);
            }
            $media_url = $upload_result['path'];
        }
        // If no new image uploaded, keep the old one
    } elseif ($media_type === 'video' && !empty($_POST['youtube_url'])) {
        $youtube_url = $_POST['youtube_url'];
        $embed_url = getYouTubeEmbedUrl($youtube_url);
        if (empty($embed_url)) {
            header("Location: blogs.php?status=err&msg=invalid_youtube");
            exit();
        }
        // Delete old image if switching from image to video
        if (!empty($old_media_url) && strpos($old_media_url, 'uploads/') === 0) {
            deleteOldBlogImage($old_media_url);
        }
        $media_url = $embed_url;
    } elseif (empty($media_type)) {
        // No media selected, delete old media if it was an image
        if (!empty($old_media_url) && strpos($old_media_url, 'uploads/') === 0) {
            deleteOldBlogImage($old_media_url);
        }
        $media_url = null;
    }
    
    $media_url_value = $media_url ? "'" . mysqli_real_escape_string($con, $media_url) . "'" : "NULL";
    $media_type_value = $media_type ? "'" . $media_type . "'" : "NULL";
    
    $sql = "UPDATE blogs SET 
            title = '$title',
            content = '$content',
            media_type = $media_type_value,
            media_url = $media_url_value
            WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: blogs.php?status=err");
    } else {
        header("Location: blogs.php?status=succ");
    }
    exit();
}

// Delete blog
if(isset($_GET['delete_blog'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_blog']);
    
    // Get blog data to delete associated image
    $get_sql = "SELECT media_url, media_type FROM blogs WHERE id = $id";
    $result = $con->query($get_sql);
    if ($result && $row = $result->fetch_assoc()) {
        // Delete image file if it exists
        if ($row['media_type'] === 'image' && !empty($row['media_url'])) {
            deleteOldBlogImage($row['media_url']);
        }
    }
    
    $sql = "DELETE FROM blogs WHERE id = $id";
    
    $delete = $con->query($sql);
    
    if($delete){
        header("Location: blogs.php?status=succ");
    } else {
        header("Location: blogs.php?status=err");
    }
    exit();
}

ob_end_flush();
?>
