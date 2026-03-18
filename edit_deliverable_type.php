<?php
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}

// Add new type
if(isset($_POST['add_type'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    
    $sql = "INSERT INTO deliverable_types (name) VALUES ('$name')";
    if ($con->query($sql)) {
        header("Location: projects.php?status=succ&tab=types");
    } else {
        header("Location: projects.php?status=err&tab=types");
    }
    exit();
}

// Update type
if(isset($_POST['update_type'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    
    $sql = "UPDATE deliverable_types SET name = '$name' WHERE id = '$id'";
    if ($con->query($sql)) {
        header("Location: projects.php?status=succ&tab=types");
    } else {
        header("Location: projects.php?status=err&tab=types");
    }
    exit();
}

// Delete type
if (isset($_GET['delete_type'])) {
    $id = (int)$_GET['delete_type'];
    
    $sql = "DELETE FROM deliverable_types WHERE id = $id";
    if ($con->query($sql)) {
        header("Location: projects.php?status=succ&tab=types");
    } else {
        header("Location: projects.php?status=err&tab=types");
    }
    exit();
}
?>
