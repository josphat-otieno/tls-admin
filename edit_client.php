<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/client_logos/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Function to handle file upload
function handleLogoUpload($file) {
    global $upload_dir;
    
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
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = str_replace('image/', '', $file['type']);
    }
    $filename = uniqid('client_logo_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['error' => 'upload_failed'];
}

function deleteOldLogo($logo_path) {
    if ($logo_path && file_exists($logo_path)) {
        unlink($logo_path);
    }
}

// Add new client
if(isset($_POST['add_client'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $logo = null;
    
    if (isset($_FILES['logo'])) {
        $upload_result = handleLogoUpload($_FILES['logo']);
        if (isset($upload_result['error'])) {
            header("Location: clients.php?status=err&msg=" . $upload_result['error'] . "&tab=clients");
            exit();
        }
        $logo = $upload_result['path'] ?? null;
    }
    
    $logo_value = $logo ? "'$logo'" : "NULL";
    $sql = "INSERT INTO clients (name, logo) VALUES ('$name', $logo_value)";
    
    $add_client = $con->query($sql);
    
    if (!$add_client) {
        header("Location: clients.php?status=err&tab=clients");
    } else {
        header("Location: clients.php?status=succ&tab=clients");
    }
    exit();
}

// Update client
if(isset($_POST['update_client'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $old_logo = mysqli_real_escape_string($con, $_POST['old_logo']);
    
    $logo = $old_logo;
    
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handleLogoUpload($_FILES['logo']);
        if (isset($upload_result['error'])) {
            header("Location: clients.php?status=err&msg=" . $upload_result['error'] . "&tab=clients");
            exit();
        }
        if ($old_logo) {
            deleteOldLogo($old_logo);
        }
        $logo = $upload_result['path'] ?? $old_logo;
    }
    
    $logo_value = $logo ? "'$logo'" : "NULL";
    $sql = "UPDATE clients SET name = '$name', logo = $logo_value WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        header("Location: clients.php?status=err&tab=clients");
    } else {
        header("Location: clients.php?status=succ&tab=clients");
    }
    exit();
}

// Delete client
if(isset($_GET['delete_client'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_client']);
    
    // Get logo path and delete associated works media
    $result = $con->query("SELECT logo FROM clients WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        deleteOldLogo($row['logo']);
    }
    
    // Delete associated works files
    $works = $con->query("SELECT media_path FROM client_works WHERE client_id = $id");
    if ($works) {
        while ($work = $works->fetch_assoc()) {
            if ($work['media_path'] && file_exists($work['media_path'])) {
                unlink($work['media_path']);
            }
        }
    }
    
    $sql = "DELETE FROM clients WHERE id = $id";
    $delete = $con->query($sql);
    
    if($delete){
        header("Location: clients.php?status=succ&tab=clients");
    } else {
        header("Location: clients.php?status=err&tab=clients");
    }
    exit();
}

ob_end_flush();
?>
