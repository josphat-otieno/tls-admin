<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Update impact summary
if(isset($_POST['update_impact'])){
    $total_clients = (int)mysqli_real_escape_string($con, $_POST['total_clients']);
    $total_projects = (int)mysqli_real_escape_string($con, $_POST['total_projects']);
    $total_countries = (int)mysqli_real_escape_string($con, $_POST['total_countries']);
    $people_impacted = (int)mysqli_real_escape_string($con, $_POST['people_impacted']);
    $sectors_impacted = (int)mysqli_real_escape_string($con, $_POST['sectors_impacted']);
    
    // Update the single record (id = 1)
    $sql = "UPDATE impact_summary SET 
            total_clients = $total_clients,
            total_projects = $total_projects,
            total_countries = $total_countries,
            people_impacted = $people_impacted,
            sectors_impacted = $sectors_impacted
            WHERE id = 1";
    
    if($con->query($sql)){
        header("Location: impact-summary.php?status=succ");
        exit();
    } else {
        header("Location: impact-summary.php?status=err");
        exit();
    }
}

ob_end_flush();
?>
