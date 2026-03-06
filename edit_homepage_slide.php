<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

// Function to handle slide image upload
function handleSlideImageUpload($file, $type = 'slide') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_codes = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        return ['error' => $error_codes[$file['error']] ?? 'Unknown upload error'];
    }

    $upload_dir = 'uploads/slides/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }
    
    $max_size = 10 * 1024 * 1024; // Increased to 10MB
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid($type . '_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete slide image
function deleteSlideImage($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}

// Function to extract YouTube video ID
function getYouTubeVideoId($url) {
    $video_id = '';
    
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    }
    
    return $video_id;
}

// Add new slide
if(isset($_POST['add_slide'])){
    $slide_type = mysqli_real_escape_string($con, $_POST['slide_type']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    $order_sql = "SELECT MAX(display_order) as max_order FROM homepage_slides";
    $order_result = mysqli_query($con, $order_sql);
    $order_row = mysqli_fetch_assoc($order_result);
    $display_order = ($order_row['max_order'] ?? 0) + 1;
    
    if ($slide_type === 'image') {
        if (!isset($_FILES['slide_image']) || $_FILES['slide_image']['error'] === UPLOAD_ERR_NO_FILE) {
            header("Location: homepage.php?status=err&msg=no_image");
            exit();
        }
        
        $upload_result = handleSlideImageUpload($_FILES['slide_image'], 'slide');
        if (isset($upload_result['success'])) {
            $image_path = $upload_result['path'];
            $sql = "INSERT INTO homepage_slides (slide_type, title, description, image_path, display_order) 
                    VALUES ('$slide_type', '$title', '$description', '$image_path', $display_order)";
        } else {
            $msg = $upload_result['error'];
            header("Location: homepage.php?status=err&msg=$msg");
            exit();
        }
        
    } elseif ($slide_type === 'video') {
        $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
        $video_id = getYouTubeVideoId($youtube_url);
        
        if (empty($video_id)) {
            header("Location: homepage.php?status=err&msg=invalid_youtube");
            exit();
        }
        
        if (!isset($_FILES['placeholder_image']) || $_FILES['placeholder_image']['error'] === UPLOAD_ERR_NO_FILE) {
            header("Location: homepage.php?status=err&msg=no_placeholder");
            exit();
        }
        
        $upload_result = handleSlideImageUpload($_FILES['placeholder_image'], 'placeholder');
        if (isset($upload_result['success'])) {
            $placeholder_image = $upload_result['path'];
            $sql = "INSERT INTO homepage_slides (slide_type, title, description, youtube_url, youtube_video_id, placeholder_image, display_order) 
                    VALUES ('$slide_type', '$title', '$description', '$youtube_url', '$video_id', '$placeholder_image', $display_order)";
        } else {
            $msg = $upload_result['error'];
            header("Location: homepage.php?status=err&msg=$msg");
            exit();
        }
    }
    
    if($con->query($sql)){
        header("Location: homepage.php?status=succ");
    } else {
        header("Location: homepage.php?status=err&msg=db_error");
    }
    exit();
}

// Update slide
if(isset($_POST['update_slide'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $slide_type = mysqli_real_escape_string($con, $_POST['slide_type']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    $old_sql = "SELECT * FROM homepage_slides WHERE id = '$id'";
    $old_result = mysqli_query($con, $old_sql);
    $old_slide = mysqli_fetch_assoc($old_result);
    
    if ($slide_type === 'image') {
        $image_path = $old_slide['image_path'];
        
        if (isset($_FILES['slide_image']) && $_FILES['slide_image']['name'] !== '') {
            $upload_result = handleSlideImageUpload($_FILES['slide_image'], 'slide');
            if (isset($upload_result['success'])) {
                if (!empty($old_slide['image_path'])) {
                    deleteSlideImage($old_slide['image_path']);
                }
                $image_path = $upload_result['path'];
            } else {
                $msg = $upload_result['error'];
                header("Location: homepage.php?status=err&msg=$msg");
                exit();
            }
        }
        
        $sql = "UPDATE homepage_slides SET 
                slide_type = '$slide_type',
                title = '$title',
                description = '$description',
                image_path = '$image_path',
                youtube_url = NULL,
                youtube_video_id = NULL,
                placeholder_image = NULL
                WHERE id = '$id'";
        
        if ($old_slide['slide_type'] === 'video' && !empty($old_slide['placeholder_image'])) {
            deleteSlideImage($old_slide['placeholder_image']);
        }
        
    } elseif ($slide_type === 'video') {
        $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
        $video_id = getYouTubeVideoId($youtube_url);
        
        if (empty($video_id)) {
            header("Location: homepage.php?status=err&msg=invalid_youtube");
            exit();
        }
        
        $placeholder_image = $old_slide['placeholder_image'];
        
        if (isset($_FILES['placeholder_image']) && $_FILES['placeholder_image']['name'] !== '') {
            $upload_result = handleSlideImageUpload($_FILES['placeholder_image'], 'placeholder');
            if (isset($upload_result['success'])) {
                if (!empty($old_slide['placeholder_image'])) {
                    deleteSlideImage($old_slide['placeholder_image']);
                }
                $placeholder_image = $upload_result['path'];
            } else {
                $msg = $upload_result['error'];
                header("Location: homepage.php?status=err&msg=$msg");
                exit();
            }
        }
        
        $sql = "UPDATE homepage_slides SET 
                slide_type = '$slide_type',
                title = '$title',
                description = '$description',
                youtube_url = '$youtube_url',
                youtube_video_id = '$video_id',
                placeholder_image = '$placeholder_image',
                image_path = NULL
                WHERE id = '$id'";
        
        if ($old_slide['slide_type'] === 'image' && !empty($old_slide['image_path'])) {
            deleteSlideImage($old_slide['image_path']);
        }
    }
    
    if($con->query($sql)){
        header("Location: homepage.php?status=succ");
    } else {
        header("Location: homepage.php?status=err&msg=db_error");
    }
    exit();
}

// Toggle slide active status
if(isset($_GET['toggle_slide'])){
    $id = mysqli_real_escape_string($con, $_GET['toggle_slide']);
    
    $sql = "UPDATE homepage_slides SET is_active = NOT is_active WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: homepage.php?status=succ");
    } else {
        header("Location: homepage.php?status=err");
    }
    exit();
}

// Delete slide
if(isset($_GET['delete_slide'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_slide']);
    
    // Get slide data to delete associated images
    $get_sql = "SELECT * FROM homepage_slides WHERE id = $id";
    $result = $con->query($get_sql);
    if ($result && $row = $result->fetch_assoc()) {
        // Delete images
        if (!empty($row['image_path'])) {
            deleteSlideImage($row['image_path']);
        }
        if (!empty($row['placeholder_image'])) {
            deleteSlideImage($row['placeholder_image']);
        }
    }
    
    $sql = "DELETE FROM homepage_slides WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: homepage.php?status=succ");
    } else {
        header("Location: homepage.php?status=err");
    }
    exit();
}

// Update display order
if(isset($_POST['update_order'])){
    $orders = $_POST['order'];
    
    foreach($orders as $id => $order){
        $id = mysqli_real_escape_string($con, $id);
        $order = mysqli_real_escape_string($con, $order);
        $sql = "UPDATE homepage_slides SET display_order = '$order' WHERE id = '$id'";
        $con->query($sql);
    }
    
    header("Location: homepage.php?status=succ");
    exit();
}

ob_end_flush();
?>
