<?php
include 'dbConnect.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($con, $_GET['status']);

    if (in_array($status, ['new', 'read'])) {
        $sql = "UPDATE enquiries SET status = '$status' WHERE id = $id";
        if (mysqli_query($con, $sql)) {
            header("Location: enquiries.php?status=succ");
        } else {
            header("Location: enquiries.php?status=err");
        }
    } else {
        header("Location: enquiries.php?status=err");
    }
} else {
    header("Location: enquiries.php");
}
?>
