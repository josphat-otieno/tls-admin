<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Add new category
if(isset($_POST['add_category'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    $sql = "INSERT INTO client_categories (name, description) VALUES ('$name', '$description')";
    
    $add_category = $con->query($sql);
    
    if (!$add_category) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: clients.php?status=err&tab=categories");
    } else {
        header("Location: clients.php?status=succ&tab=categories");
    }
}

// Update category
if(isset($_POST['update_category'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    
    $sql = "UPDATE client_categories SET name = '$name', description = '$description' WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: clients.php?status=err&tab=categories");
    } else {
        header("Location: clients.php?status=succ&tab=categories");
    }
}

// Delete category
if(isset($_GET['delete_category'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_category']);
    
    // Check if category has clients
    $check = $con->query("SELECT COUNT(*) as count FROM clients WHERE category_id = $id");
    $row = $check->fetch_assoc();
    
    if($row['count'] > 0) {
        header("Location: clients.php?status=err&msg=category_in_use&tab=categories");
        exit();
    }
    
    $sql = "DELETE FROM client_categories WHERE id = $id";
    
    $delete = $con->query($sql);
    
    if($delete){
        header("Location: clients.php?status=succ&tab=categories");
    } else {
        header("Location: clients.php?status=err&tab=categories");
    }
}

ob_end_flush();
?>
