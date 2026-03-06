<?php
ob_start();
error_reporting(E_ALL);
include 'dbConnect.php';

// ── ADD ──────────────────────────────────────────────────────────────────────
if (isset($_POST['add_career'])) {
    $title        = mysqli_real_escape_string($con, trim($_POST['title']));
    $location     = mysqli_real_escape_string($con, trim($_POST['location'] ?? ''));
    $job_type     = mysqli_real_escape_string($con, $_POST['job_type']);
    $department   = mysqli_real_escape_string($con, trim($_POST['department'] ?? ''));
    $description  = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $requirements = mysqli_real_escape_string($con, trim($_POST['requirements'] ?? ''));
    $deadline     = !empty($_POST['deadline']) ? "'" . mysqli_real_escape_string($con, $_POST['deadline']) . "'" : "NULL";
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    $sql = "INSERT INTO careers (title, location, job_type, department, description, requirements, deadline, is_active)
            VALUES ('$title','$location','$job_type','$department','$description','$requirements',$deadline,$is_active)";

    if ($con->query($sql)) {
        header("Location: careers.php?status=succ");
    } else {
        header("Location: careers.php?status=err");
    }
    exit();
}

// ── UPDATE ───────────────────────────────────────────────────────────────────
if (isset($_POST['update_career'])) {
    $id           = (int)$_POST['id'];
    $title        = mysqli_real_escape_string($con, trim($_POST['title']));
    $location     = mysqli_real_escape_string($con, trim($_POST['location'] ?? ''));
    $job_type     = mysqli_real_escape_string($con, $_POST['job_type']);
    $department   = mysqli_real_escape_string($con, trim($_POST['department'] ?? ''));
    $description  = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));
    $requirements = mysqli_real_escape_string($con, trim($_POST['requirements'] ?? ''));
    $deadline     = !empty($_POST['deadline']) ? "'" . mysqli_real_escape_string($con, $_POST['deadline']) . "'" : "NULL";
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    $sql = "UPDATE careers SET title='$title', location='$location', job_type='$job_type',
            department='$department', description='$description', requirements='$requirements',
            deadline=$deadline, is_active=$is_active
            WHERE id=$id";

    if ($con->query($sql)) {
        header("Location: careers.php?status=succ");
    } else {
        header("Location: careers.php?status=err");
    }
    exit();
}

// ── DELETE ───────────────────────────────────────────────────────────────────
if (isset($_GET['delete_career'])) {
    $id = (int)$_GET['delete_career'];
    if ($con->query("DELETE FROM careers WHERE id = $id")) {
        header("Location: careers.php?status=succ");
    } else {
        header("Location: careers.php?status=err");
    }
    exit();
}

// ── TOGGLE ACTIVE ────────────────────────────────────────────────────────────
if (isset($_GET['toggle_career'])) {
    $id = (int)$_GET['toggle_career'];
    $res = $con->query("SELECT is_active FROM careers WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $new_val = $row['is_active'] ? 0 : 1;
        $con->query("UPDATE careers SET is_active = $new_val WHERE id = $id");
    }
    header("Location: careers.php?status=succ");
    exit();
}

ob_end_flush();
?>
