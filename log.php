<?php
session_start();
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $sql = "SELECT * FROM editors WHERE email='$email' AND password='$password'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user = $row['name'];
        $email = $row['email'];
        $type = $row['type'];
        $user_id = $row['id'];
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $user;
        $_SESSION['email'] = $email;
        $_SESSION['type'] = $type;
        $_SESSION['logged_in'] = "true";
        
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
}
?>
