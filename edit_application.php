<?php
ob_start();
error_reporting(E_ALL);
include 'dbConnect.php';

// ── UPDATE STATUS ────────────────────────────────────────────────────────────
if (isset($_POST['update_status'])) {
    $id     = (int)$_POST['id'];
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $sql = "UPDATE job_applications SET status='$status' WHERE id=$id";

    if ($con->query($sql)) {
        header("Location: applications.php?status=succ");
    } else {
        header("Location: applications.php?status=err");
    }
    exit();
}

// ── DELETE APPLICATION ───────────────────────────────────────────────────────
if (isset($_GET['delete_app'])) {
    $id = (int)$_GET['delete_app'];
    
    // First get CV path to delete file
    $res = $con->query("SELECT cv_path FROM job_applications WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $file = $row['cv_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    if ($con->query("DELETE FROM job_applications WHERE id = $id")) {
        header("Location: applications.php?status=succ");
    } else {
        header("Location: applications.php?status=err");
    }
    exit();
}

ob_end_flush();
?>
