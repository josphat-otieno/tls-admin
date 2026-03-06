<?php
ob_start();
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
include 'dbConnect.php';

$upload_dir = 'uploads/client_works/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

function handleMediaUpload($file) {
    global $upload_dir;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        return ['error' => 'upload_failed'];
    }

    $allowed_image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowed_video_types = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
    $allowed_types = array_merge($allowed_image_types, $allowed_video_types);

    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'invalid_file'];
    }

    $max_size = 50 * 1024 * 1024; // 50MB
    if ($file['size'] > $max_size) {
        return ['error' => 'file_too_large'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('cw_') . '.' . $extension;
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }

    return ['error' => 'upload_failed'];
}

function deleteMedia($path) {
    if ($path && file_exists($path)) {
        unlink($path);
    }
}

// When a work is set as featured, unset all others for that client
function setFeatured($con, $client_id, $work_id) {
    $client_id = (int)$client_id;
    $work_id   = (int)$work_id;
    $con->query("UPDATE client_works SET is_featured = 0 WHERE client_id = $client_id");
    $con->query("UPDATE client_works SET is_featured = 1 WHERE id = $work_id AND client_id = $client_id");
}

// ── ADD ──────────────────────────────────────────────────────────────────────
if (isset($_POST['add_work'])) {
    $client_id   = (int)$_POST['client_id'];
    $title       = mysqli_real_escape_string($con, trim($_POST['title']));
    $type        = in_array($_POST['type'], ['banner','video']) ? $_POST['type'] : 'banner';
    $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $video_url   = mysqli_real_escape_string($con, trim($_POST['video_url'] ?? ''));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $media_path = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
        $result = handleMediaUpload($_FILES['media']);
        if (isset($result['error'])) {
            header("Location: clients.php?status=err&msg=" . $result['error'] . "&tab=works&client_id=$client_id");
            exit();
        }
        $media_path = $result['path'];
    }

    $media_val = $media_path ? "'$media_path'" : "NULL";
    $video_val = $video_url   ? "'$video_url'"  : "NULL";

    $sql = "INSERT INTO client_works (client_id, title, type, media_path, video_url, description, is_featured)
            VALUES ($client_id, '$title', '$type', $media_val, $video_val, '$description', $is_featured)";

    if ($con->query($sql)) {
        $new_id = $con->insert_id;
        if ($is_featured) {
            setFeatured($con, $client_id, $new_id);
        }
        header("Location: clients.php?status=succ&tab=works&client_id=$client_id");
    } else {
        header("Location: clients.php?status=err&tab=works&client_id=$client_id");
    }
    exit();
}

// ── UPDATE ───────────────────────────────────────────────────────────────────
if (isset($_POST['update_work'])) {
    $id          = (int)$_POST['id'];
    $client_id   = (int)$_POST['client_id'];
    $title       = mysqli_real_escape_string($con, trim($_POST['title']));
    $type        = in_array($_POST['type'], ['banner','video']) ? $_POST['type'] : 'banner';
    $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $video_url   = mysqli_real_escape_string($con, trim($_POST['video_url'] ?? ''));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $old_media   = mysqli_real_escape_string($con, $_POST['old_media'] ?? '');

    $media_path = $old_media;
    if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
        $result = handleMediaUpload($_FILES['media']);
        if (isset($result['error'])) {
            header("Location: clients.php?status=err&msg=" . $result['error'] . "&tab=works&client_id=$client_id");
            exit();
        }
        deleteMedia($old_media);
        $media_path = $result['path'];
    }

    $media_val = $media_path ? "'$media_path'" : "NULL";
    $video_val = $video_url  ? "'$video_url'"  : "NULL";

    $sql = "UPDATE client_works
            SET title='$title', type='$type', media_path=$media_val, video_url=$video_val,
                description='$description', is_featured=$is_featured
            WHERE id=$id AND client_id=$client_id";

    if ($con->query($sql)) {
        if ($is_featured) {
            setFeatured($con, $client_id, $id);
        }
        header("Location: clients.php?status=succ&tab=works&client_id=$client_id");
    } else {
        header("Location: clients.php?status=err&tab=works&client_id=$client_id");
    }
    exit();
}

// ── DELETE ───────────────────────────────────────────────────────────────────
if (isset($_GET['delete_work'])) {
    $id = (int)$_GET['delete_work'];
    $client_id = (int)($_GET['client_id'] ?? 0);

    $res = $con->query("SELECT media_path FROM client_works WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        deleteMedia($row['media_path']);
    }

    $con->query("DELETE FROM client_works WHERE id = $id");
    header("Location: clients.php?status=succ&tab=works&client_id=$client_id");
    exit();
}

// ── TOGGLE FEATURED ──────────────────────────────────────────────────────────
if (isset($_GET['set_featured'])) {
    $id        = (int)$_GET['set_featured'];
    $client_id = (int)($_GET['client_id'] ?? 0);
    setFeatured($con, $client_id, $id);
    header("Location: clients.php?status=succ&tab=works&client_id=$client_id");
    exit();
}

ob_end_flush();
?>
