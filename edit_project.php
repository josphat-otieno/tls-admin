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
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
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
    
    $sql = "INSERT INTO projects (title, description, client_name, thumbnail, is_featured) 
            VALUES ('$title', '$description', '$client_name', '$thumbnail_path', '$is_featured')";
    
    $add_project = $con->query($sql);
    
    if (!$add_project) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: projects.php?status=err&tab=projects");
        exit();
    } else {
        $project_id = $con->insert_id;
        
        // Handle deliverables
        if (isset($_POST['deliverable_titles']) && is_array($_POST['deliverable_titles'])) {
            $deliverable_titles = $_POST['deliverable_titles'];
            $deliverable_types = $_POST['deliverable_types'];
            $deliverable_media_types = $_POST['deliverable_media_types'];
            
            $deliverables_dir = "uploads/projects/deliverables/";
            if (!file_exists($deliverables_dir)) {
                mkdir($deliverables_dir, 0777, true);
            }
            
            foreach ($deliverable_titles as $index => $title_raw) {
                $title = mysqli_real_escape_string($con, $title_raw);
                $type_id = mysqli_real_escape_string($con, $deliverable_types[$index]);
                $media_type = mysqli_real_escape_string($con, $deliverable_media_types[$index]);
                
                $is_youtube = ($media_type == 'Video' && !empty($_POST['deliverable_youtube_urls'][$index]));
                
                if ($is_youtube) {
                    $youtube_url = mysqli_real_escape_string($con, $_POST['deliverable_youtube_urls'][$index]);
                    $con->query("INSERT INTO project_deliverables (project_id, type_id, media_type, title, file_path) 
                               VALUES ('$project_id', '$type_id', '$media_type', '$title', '$youtube_url')");
                } else if (isset($_FILES['deliverable_files']['name'][$index]) && $_FILES['deliverable_files']['error'][$index] == 0) {
                    $file_name = time() . '_' . $index . '_' . basename($_FILES["deliverable_files"]["name"][$index]);
                    $target_file = $deliverables_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES["deliverable_files"]["tmp_name"][$index], $target_file)) {
                        $con->query("INSERT INTO project_deliverables (project_id, type_id, media_type, title, file_path) 
                                   VALUES ('$project_id', '$type_id', '$media_type', '$title', '$target_file')");
                    }
                }
            }
        }
        
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
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
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
            thumbnail = '$thumbnail_path',
            is_featured = '$is_featured'
            WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: projects.php?status=err&tab=projects");
        exit();
    } else {
        // Handle new deliverables
        if (isset($_POST['deliverable_titles']) && is_array($_POST['deliverable_titles'])) {
            $deliverable_titles = $_POST['deliverable_titles'];
            $deliverable_types = $_POST['deliverable_types'];
            $deliverable_media_types = $_POST['deliverable_media_types'];
            
            $deliverables_dir = "uploads/projects/deliverables/";
            if (!file_exists($deliverables_dir)) {
                mkdir($deliverables_dir, 0777, true);
            }
            
            foreach ($deliverable_titles as $index => $title_raw) {
                $title = mysqli_real_escape_string($con, $title_raw);
                $type_id = mysqli_real_escape_string($con, $deliverable_types[$index]);
                $media_type = mysqli_real_escape_string($con, $deliverable_media_types[$index]);
                
                $is_youtube = ($media_type == 'Video' && !empty($_POST['deliverable_youtube_urls'][$index]));
                
                if ($is_youtube) {
                    $youtube_url = mysqli_real_escape_string($con, $_POST['deliverable_youtube_urls'][$index]);
                    $con->query("INSERT INTO project_deliverables (project_id, type_id, media_type, title, file_path) 
                               VALUES ('$id', '$type_id', '$media_type', '$title', '$youtube_url')");
                } else if (isset($_FILES['deliverable_files']['name'][$index]) && $_FILES['deliverable_files']['error'][$index] == 0) {
                    $file_name = time() . '_' . $index . '_' . basename($_FILES["deliverable_files"]["name"][$index]);
                    $target_file = $deliverables_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES["deliverable_files"]["tmp_name"][$index], $target_file)) {
                        $con->query("INSERT INTO project_deliverables (project_id, type_id, media_type, title, file_path) 
                                   VALUES ('$id', '$type_id', '$media_type', '$title', '$target_file')");
                    }
                }
            }
        }

        // Handle deleted deliverables
        if (isset($_POST['deleted_deliverables']) && !empty($_POST['deleted_deliverables'])) {
            $deleted_ids = explode(',', $_POST['deleted_deliverables']);
            foreach ($deleted_ids as $del_id) {
                $del_id = (int)$del_id;
                // Get file path to delete
                $res = $con->query("SELECT file_path FROM project_deliverables WHERE id = $del_id AND project_id = $id");
                if ($res && $row = $res->fetch_assoc()) {
                    if (file_exists($row['file_path'])) {
                        unlink($row['file_path']);
                    }
                    $con->query("DELETE FROM project_deliverables WHERE id = $del_id");
                }
            }
        }
        
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

    // Get thumbnail and deliverable files before deleting
    $res = $con->query("SELECT thumbnail FROM projects WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $thumbnail = $row['thumbnail'];
        if (!empty($thumbnail) && file_exists($thumbnail)) {
            unlink($thumbnail);
        }
    }

    $res = $con->query("SELECT file_path FROM project_deliverables WHERE project_id = $id");
    while ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['file_path']) && file_exists($row['file_path'])) {
            unlink($row['file_path']);
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
