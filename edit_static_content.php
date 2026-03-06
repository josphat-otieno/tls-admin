<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Update static content
if(isset($_POST['update_content'])){
    $page_name = mysqli_real_escape_string($con, $_POST['page_name']);
    
    // Strip HTML tags but preserve line breaks
    $content = $_POST['content'];
    // Convert <br>, <br/>, <br />, </p>, </div> to newlines
    $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
    $content = preg_replace('/<\/p>/i', "\n\n", $content);
    $content = preg_replace('/<\/div>/i', "\n", $content);
    // Strip all remaining HTML tags
    $content = strip_tags($content);
    // Clean up multiple newlines
    $content = preg_replace('/\n{3,}/', "\n\n", $content);
    // Trim whitespace
    $content = trim($content);
    // Escape for database
    $content = mysqli_real_escape_string($con, $content);
    
    // Check if content exists
    $check_sql = "SELECT * FROM static_content WHERE page_name = '$page_name'";
    $check_result = mysqli_query($con, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0){
        // Update existing content
        $sql = "UPDATE static_content SET content = '$content' WHERE page_name = '$page_name'";
    } else {
        // Insert new content
        $sql = "INSERT INTO static_content (page_name, content) VALUES ('$page_name', '$content')";
    }
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: " . $_POST['redirect_page'] . "?status=err");
    } else {
        header("Location: " . $_POST['redirect_page'] . "?status=succ");
    }
}

ob_end_flush();
?>
