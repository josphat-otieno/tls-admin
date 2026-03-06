<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Generate random password string
function generatePassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%';
    $charactersLength = strlen($characters);
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $charactersLength - 1)];
    }
    return $password;
}

// Add new user
if(isset($_POST['add_user'])){
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $type = 'admin';
    
    // Check if user already exists
    $check_sql = "SELECT email FROM editors WHERE email = '$email'";
    $check_result = mysqli_query($con, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        header("Location: users.php?status=err&msg=user_exists");
        exit();
    }
    
    // Generate random password
    $password = generatePassword();
    
    $sql = "INSERT INTO editors (name, email, password, type) VALUES ('$name', '$email', '$password', '$type')";
    
    $add_user = $con->query($sql);
    
    if (!$add_user) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: users.php?status=err");
    } else {
        // Store password in session to display to admin
        session_start();
        $_SESSION['new_user_password'] = $password;
        $_SESSION['new_user_email'] = $email;
        header("Location: users.php?status=succ&action=added");
    }
}

// Update user
if(isset($_POST['update_user'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    // $type = mysqli_real_escape_string($con, $_POST['type']);
    
    $sql = "UPDATE editors SET name = '$name', email = '$email', type = 'admin' WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: users.php?status=err");
    } else {
        header("Location: users.php?status=succ&action=updated");
    }
}

// Reset password
if(isset($_POST['reset_password'])){
    $id = mysqli_real_escape_string($con, $_POST['id']);
    
    // Generate new random password
    $new_password = generatePassword();
    
    $sql = "UPDATE editors SET password = '$new_password' WHERE id = '$id'";
    
    $update = $con->query($sql);
    
    if (!$update) {
        echo "Error: " . $sql . "<br>" . $con->error;
        header("Location: users.php?status=err");
    } else {
        // Get user email
        $user_result = mysqli_query($con, "SELECT email FROM editors WHERE id = '$id'");
        $user_data = mysqli_fetch_assoc($user_result);
        
        // Store password in session to display to admin
        session_start();
        $_SESSION['new_user_password'] = $new_password;
        $_SESSION['new_user_email'] = $user_data['email'];
        header("Location: users.php?status=succ&action=reset");
    }
}

// Delete user
if(isset($_GET['delete_user'])){
    $id = mysqli_real_escape_string($con, $_GET['delete_user']);
    
    $sql = "DELETE FROM editors WHERE id = $id";
    
    $delete = $con->query($sql);
    
    if($delete){
        header("Location: users.php?status=succ&action=deleted");
    } else {
        header("Location: users.php?status=err");
    }
}

ob_end_flush();
?>
