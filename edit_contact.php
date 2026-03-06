<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbConnect.php';

// Update contact information
if(isset($_POST['update_contact'])){
    // Social Media Links
    $facebook_url = mysqli_real_escape_string($con, $_POST['facebook_url']);
    $twitter_url = mysqli_real_escape_string($con, $_POST['twitter_url']);
    $instagram_url = mysqli_real_escape_string($con, $_POST['instagram_url']);
    $linkedin_url = mysqli_real_escape_string($con, $_POST['linkedin_url']);
    $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
    
    // Contact Details
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone_number = mysqli_real_escape_string($con, $_POST['phone_number']);
    $office_number = mysqli_real_escape_string($con, $_POST['office_number']);
    $building_name = mysqli_real_escape_string($con, $_POST['building_name']);
    $street = mysqli_real_escape_string($con, $_POST['street']);
    $po_box = mysqli_real_escape_string($con, $_POST['po_box']);
    $google_map_coordinates = mysqli_real_escape_string($con, $_POST['google_map_coordinates']);
    
    // Update the single record (id = 1)
    $sql = "UPDATE contact_info SET 
            facebook_url = " . ($facebook_url ? "'$facebook_url'" : "NULL") . ",
            twitter_url = " . ($twitter_url ? "'$twitter_url'" : "NULL") . ",
            instagram_url = " . ($instagram_url ? "'$instagram_url'" : "NULL") . ",
            linkedin_url = " . ($linkedin_url ? "'$linkedin_url'" : "NULL") . ",
            youtube_url = " . ($youtube_url ? "'$youtube_url'" : "NULL") . ",
            email = " . ($email ? "'$email'" : "NULL") . ",
            phone_number = " . ($phone_number ? "'$phone_number'" : "NULL") . ",
            office_number = " . ($office_number ? "'$office_number'" : "NULL") . ",
            building_name = " . ($building_name ? "'$building_name'" : "NULL") . ",
            street = " . ($street ? "'$street'" : "NULL") . ",
            po_box = " . ($po_box ? "'$po_box'" : "NULL") . ",
            google_map_coordinates = " . ($google_map_coordinates ? "'$google_map_coordinates'" : "NULL") . "
            WHERE id = 1";
    
    if($con->query($sql)){
        header("Location: contact.php?status=succ");
    } else {
        header("Location: contact.php?status=err");
    }
    exit();
}

ob_end_flush();
?>
