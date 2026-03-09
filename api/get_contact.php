<?php
// api/get_contact.php
include_once 'api_helper.php';

$query = "SELECT * FROM contact_info WHERE id = 1";
$result = mysqli_query($con, $query);
$contact = mysqli_fetch_assoc($result);

response('success', 'Contact info fetched', $contact);
?>
