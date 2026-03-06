<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Add new project
if(isset($_POST['add_project'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $client_name = mysqli_real_escape_string($con, $_POST['client_name']);
    
    $thumbnail_path = "";
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $target_dir = "uploads/projects/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES["thumbnail"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["thumbnail"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
                $thumbnail_path = $target_file;
            } else {
                header("Location: projects.php?status=err&tab=projects&msg=upload_failed");
                exit();
            }
        } else {
            header("Location: projects.php?status=err&tab=projects&msg=invalid_image");
            exit();
        }
    }
    
    $sql = "INSERT INTO projects (title, description, client_name, thumbnail) 
            VALUES ('$title', '$description', '$client_name', '$thumbnail_path')";
    
    $add_project = $con->query($sql);
    
    if (!$add_project) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: projects.php?status=err&tab=projects");
        exit();
    } else {
        header("Location: projects.php?status=succ&tab=projects");
        exit();
    }
}

// Update project
if(isset($_POST['update_project'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $client_name = mysqli_real_escape_string($con, $_POST['client_name']);
    $old_thumbnail = $_POST['old_thumbnail'];
    
    $thumbnail_path = $old_thumbnail;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $target_dir = "uploads/projects/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES["thumbnail"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES["thumbnail"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
                $thumbnail_path = $target_file;
                // Delete old thumbnail if exists
                if (!empty($old_thumbnail) && file_exists($old_thumbnail)) {
                    unlink($old_thumbnail);
                }
            } else {
                header("Location: projects.php?status=err&tab=projects&msg=upload_failed");
                exit();
            }
        } else {
            header("Location: projects.php?status=err&tab=projects&msg=invalid_image");
            exit();
        }
    } elseif (empty($_FILES['thumbnail']['name']) && empty($old_thumbnail)) {
         // Case where user removes the image without uploading a new one
         $thumbnail_path = "";
         if (!empty($old_thumbnail) && file_exists($old_thumbnail)) {
            unlink($old_thumbnail);
        }
    }
    
    $sql = "UPDATE projects SET 
            title = '$title',
            description = '$description',
            client_name = '$client_name',
            thumbnail = '$thumbnail_path'
            WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: projects.php?status=err&tab=projects");
        exit();
    } else {
        header("Location: projects.php?status=succ&tab=projects");
        exit();
    }
}

// Delete project
if (isset($_GET['delete_project'])) {
    $id = filter_var($_GET['delete_project'], FILTER_VALIDATE_INT);

    if ($id === false || $id <= 0) {
        header("Location: projects.php?status=err&tab=projects&msg=invalid_id");
        exit();
    }

    // Get thumbnail path before deleting
    $res = $con->query("SELECT thumbnail FROM projects WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $thumbnail = $row['thumbnail'];
        if (!empty($thumbnail) && file_exists($thumbnail)) {
            unlink($thumbnail);
        }
    }

    $stmt = $con->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: projects.php?status=succ&tab=projects");
        exit();
    } else {
        $stmt->close();
        header("Location: projects.php?status=err&tab=projects");
        exit();
    }
}
ob_end_flush();
?>
