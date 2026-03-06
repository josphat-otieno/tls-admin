<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';
mysqli_set_charset($con, "utf8mb4");

// Create uploads directory if it doesn't exist
$thumbnail_upload_dir = 'uploads/publication_thumbnails/';
if (!file_exists($thumbnail_upload_dir)) {
    mkdir($thumbnail_upload_dir, 0777, true);
}

// Function to handle thumbnail upload
function handleThumbnailUpload($file) {
    global $thumbnail_upload_dir;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['error' => 'file_too_large'];
            case UPLOAD_ERR_PARTIAL:
                return ['error' => 'upload_failed'];
            default:
                return ['error' => 'upload_failed'];
        }
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = @getimagesize($file['tmp_name']);
    if (!$file_info || !in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_image'];
    }

    // Validate file size (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = str_replace('image/', '', $file['type']);
    }
    $filename = uniqid('pub_thumb_') . '.' . $extension;
    $filepath = $thumbnail_upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

// Function to delete old thumbnail
function deleteThumbnail($thumbnail_path) {
    if ($thumbnail_path && file_exists($thumbnail_path)) {
        unlink($thumbnail_path);
    }
}

// Function to handle document upload
function handleDocumentUpload($file) {
    $upload_dir = 'uploads/publications/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    $allowed_types = ['pdf', 'doc', 'docx'];
    if (!in_array($extension, $allowed_types)) {
        return ['success' => false, 'error' => 'invalid_type'];
    }
    
    // Validate file size (10MB max)
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'file_too_large'];
    }
    
    // Generate unique filename
    $filename = uniqid('pub_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $document_type = ($extension === 'pdf') ? 'pdf' : 'word';
        return [
            'success' => true,
            'filepath' => $filepath,
            'document_type' => $document_type,
            'file_size' => $file['size'],
            'original_filename' => $file['name']
        ];
    }
    
    return ['success' => false, 'error' => 'upload_failed'];
}

// Function to delete document file
function deleteDocument($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}



// Add new publication
if(isset($_POST['add_publication'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $publication_type = mysqli_real_escape_string($con, $_POST['publication_type']);
    $thumbnail = null;
    
    // Handle thumbnail upload
    if (isset($_FILES['thumbnail'])) {
        $thumb_result = handleThumbnailUpload($_FILES['thumbnail']);
        if (isset($thumb_result['error'])) {
            header("Location: publications.php?status=err&msg=" . $thumb_result['error']);
            exit();
        }
        $thumbnail = $thumb_result['path'] ?? null;
    }
    
    if ($publication_type === 'document') {
        // Handle document upload
        if (!isset($_FILES['document']) || $_FILES['document']['error'] !== 0) {
            if ($thumbnail) deleteThumbnail($thumbnail);
            header("Location: publications.php?status=err&msg=no_document");
            exit();
        }
        
        $upload_result = handleDocumentUpload($_FILES['document']);
        
        if (!$upload_result['success']) {
            if ($thumbnail) deleteThumbnail($thumbnail);
            header("Location: publications.php?status=err&msg=" . $upload_result['error']);
            exit();
        }
        
        $document_path = mysqli_real_escape_string($con, $upload_result['filepath']);
        $document_type = mysqli_real_escape_string($con, $upload_result['document_type']);
        $file_size = (int)$upload_result['file_size'];
        $original_filename = mysqli_real_escape_string($con, $upload_result['original_filename']);
        
        $thumbnail_value = $thumbnail ? "'$thumbnail'" : "NULL";
        $category_value = !empty($category) ? "'$category'" : "NULL";
        $sql = "INSERT INTO publications (title, description, category, thumbnail, publication_type, document_path, document_type, file_size, original_filename) 
                VALUES ('$title', '$description', $category_value, $thumbnail_value, 'document', '$document_path', '$document_type', $file_size, '$original_filename')";
        
    } elseif ($publication_type === 'link') {
        // Handle external link
        $external_url = mysqli_real_escape_string($con, $_POST['external_url']);
        
        if (empty($external_url)) {
            if ($thumbnail) deleteThumbnail($thumbnail);
            header("Location: publications.php?status=err&msg=no_url");
            exit();
        }
        
        // Validate URL format
        if (!filter_var($external_url, FILTER_VALIDATE_URL)) {
            if ($thumbnail) deleteThumbnail($thumbnail);
            header("Location: publications.php?status=err&msg=invalid_url");
            exit();
        }
        
        $thumbnail_value = $thumbnail ? "'$thumbnail'" : "NULL";
        $category_value = !empty($category) ? "'$category'" : "NULL";
        $sql = "INSERT INTO publications (title, description, category, thumbnail, publication_type, external_url) 
                VALUES ('$title', '$description', $category_value, $thumbnail_value, 'link', '$external_url')";
    }
    
    if($con->query($sql)){
        header("Location: publications.php?status=succ");
        exit();
    } else {
        // Delete uploaded files if database insert fails
        if ($publication_type === 'document' && isset($upload_result['filepath'])) {
            deleteDocument($upload_result['filepath']);
        }
        if ($thumbnail) deleteThumbnail($thumbnail);
        header("Location: publications.php?status=err");
        exit();
    }
}

// Update publication
if(isset($_POST['update_publication'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $publication_type = mysqli_real_escape_string($con, $_POST['publication_type']);
    $old_thumbnail = mysqli_real_escape_string($con, $_POST['old_thumbnail']);
    
    // Get old publication data
    $old_sql = "SELECT * FROM publications WHERE id = '$id'";
    $old_result = mysqli_query($con, $old_sql);
    $old_pub = mysqli_fetch_assoc($old_result);
    
    $thumbnail = $old_thumbnail; // Keep old thumbnail by default
    
    // Handle new thumbnail upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
        $thumb_result = handleThumbnailUpload($_FILES['thumbnail']);
        if (isset($thumb_result['error'])) {
            header("Location: publications.php?status=err&msg=" . $thumb_result['error']);
            exit();
        }
        // Delete old thumbnail if new one uploaded successfully
        if ($old_thumbnail) {
            deleteThumbnail($old_thumbnail);
        }
        $thumbnail = $thumb_result['path'] ?? $old_thumbnail;
    }
    
    if ($publication_type === 'document') {
        $document_path = $old_pub['document_path'];
        $document_type = $old_pub['document_type'];
        $file_size = $old_pub['file_size'];
        $original_filename = $old_pub['original_filename'];
        
        // Check if new document uploaded
        if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
            $upload_result = handleDocumentUpload($_FILES['document']);
            
            if (!$upload_result['success']) {
                header("Location: publications.php?status=err&msg=" . $upload_result['error']);
                exit();
            }
            
            // Delete old document if exists
            if (!empty($old_pub['document_path'])) {
                deleteDocument($old_pub['document_path']);
            }
            
            $document_path = mysqli_real_escape_string($con, $upload_result['filepath']);
            $document_type = mysqli_real_escape_string($con, $upload_result['document_type']);
            $file_size = (int)$upload_result['file_size'];
            $original_filename = mysqli_real_escape_string($con, $upload_result['original_filename']);
        }
        
        $thumbnail_value = $thumbnail ? "'$thumbnail'" : "NULL";
        $category_value = !empty($category) ? "'$category'" : "NULL";
        $sql = "UPDATE publications SET 
                title = '$title',
                description = '$description',
                category = $category_value,
                thumbnail = $thumbnail_value,
                publication_type = 'document',
                document_path = '$document_path',
                document_type = '$document_type',
                file_size = $file_size,
                original_filename = '$original_filename',
                external_url = NULL
                WHERE id = '$id'";
        
    } elseif ($publication_type === 'link') {
        $external_url = mysqli_real_escape_string($con, $_POST['external_url']);
        
        if (empty($external_url)) {
            header("Location: publications.php?status=err&msg=no_url");
            exit();
        }
        
        if (!filter_var($external_url, FILTER_VALIDATE_URL)) {
            header("Location: publications.php?status=err&msg=invalid_url");
            exit();
        }
        
        // Delete old document file if switching from document to link
        if ($old_pub['publication_type'] === 'document' && !empty($old_pub['document_path'])) {
            deleteDocument($old_pub['document_path']);
        }
        
        $thumbnail_value = $thumbnail ? "'$thumbnail'" : "NULL";
        $category_value = !empty($category) ? "'$category'" : "NULL";
        $sql = "UPDATE publications SET 
                title = '$title',
                description = '$description',
                category = $category_value,
                thumbnail = $thumbnail_value,
                publication_type = 'link',
                external_url = '$external_url',
                document_path = NULL,
                document_type = NULL,
                file_size = 0,
                original_filename = NULL
                WHERE id = '$id'";
    }
    
    if($con->query($sql)){
        header("Location: publications.php?status=succ");
        exit();
    } else {
        header("Location: publications.php?status=err");
        exit();
    }
}

// Toggle active status
if(isset($_GET['toggle_publication'])){
    $id = mysqli_real_escape_string($con, $_GET['toggle_publication']);
    
    $sql = "UPDATE publications SET is_active = NOT is_active WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: publications.php?status=succ");
        exit();
    } else {
        header("Location: publications.php?status=err");
        exit();
    }
}

// Delete publication
if(isset($_GET['delete_publication'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_publication']);
    
    // Get publication data to delete files
    $get_sql = "SELECT * FROM publications WHERE id = '$id'";
    $result = $con->query($get_sql);
    if ($result && $row = $result->fetch_assoc()) {
        // Delete document file if it's a document type
        if ($row['publication_type'] === 'document') {
            deleteDocument($row['document_path']);
        }
        // Delete thumbnail if exists
        if (!empty($row['thumbnail'])) {
            deleteThumbnail($row['thumbnail']);
        }
    }
    
    $sql = "DELETE FROM publications WHERE id = '$id'";
    
    if($con->query($sql)){
        header("Location: publications.php?status=succ");
        exit();
    } else {
        header("Location: publications.php?status=err");
        exit();
    }
}

// Track download/access
if(isset($_GET['download'])){
    $id = mysqli_real_escape_string($con, $_GET['download']);
    
    // Get publication
    $sql = "SELECT * FROM publications WHERE id = '$id'";
    $result = mysqli_query($con, $sql);
    
    if($pub = mysqli_fetch_assoc($result)){
        // Increment download counter
        $update_sql = "UPDATE publications SET downloads = downloads + 1 WHERE id = '$id'";
        $con->query($update_sql);
        
        if ($pub['publication_type'] === 'document') {
            // Serve file for download
            $filepath = $pub['document_path'];
            if(file_exists($filepath)){
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $pub['original_filename'] . '"');
                header('Content-Length: ' . filesize($filepath));
                readfile($filepath);
                exit();
            }
        } elseif ($pub['publication_type'] === 'link') {
            // Redirect to external URL
            header('Location: ' . $pub['external_url']);
            exit();
        }
    }
    
    header("Location: publications.php?status=err&msg=file_not_found");
    exit();
}

ob_end_flush();
?>
