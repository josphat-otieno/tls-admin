<?php
ob_start();
include 'dbConnect.php';

// Function to extract YouTube video ID from URL
function getYouTubeVideoId($url) {
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
    
    return $video_id;
}

// Add new video
if(isset($_POST['add_video'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    // Extract video ID
    $video_id = getYouTubeVideoId($youtube_url);
    
    if(empty($video_id)){
        header("Location: ek_connect.php?status=err&msg=invalid_url");
        exit();
    }
    
    // Get next display order
    $order_sql = "SELECT MAX(display_order) as max_order FROM ek_connect_videos";
    $order_result = mysqli_query($con, $order_sql);
    $order_row = mysqli_fetch_assoc($order_result);
    $display_order = ($order_row['max_order'] ?? 0) + 1;
    
    $sql = "INSERT INTO ek_connect_videos (title, youtube_url, video_id, description, display_order) 
            VALUES ('$title', '$youtube_url', '$video_id', '$description', $display_order)";
    
    if($con->query($sql)){
        header("Location: ek_connect.php?status=succ");
    } else {
        header("Location: ek_connect.php?status=err");
    }
    exit();
}

// Update video
if(isset($_POST['update_video'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    // Extract video ID
    $video_id = getYouTubeVideoId($youtube_url);
    
    if(empty($video_id)){
        header("Location: ek_connect.php?status=err&msg=invalid_url");
        exit();
    }
    
    $sql = "UPDATE ek_connect_videos SET 
            title = '$title',
            youtube_url = '$youtube_url',
            video_id = '$video_id',
            description = '$description'
            WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: ek_connect.php?status=succ");
    } else {
        header("Location: ek_connect.php?status=err");
    }
    exit();
}

// Delete video
if(isset($_GET['delete_video'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_video']);
    
    $sql = "DELETE FROM ek_connect_videos WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: ek_connect.php?status=succ");
    } else {
        header("Location: ek_connect.php?status=err");
    }
    exit();
}

ob_end_flush();
?>
