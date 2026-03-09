<?php
// api/get_services.php
include_once 'api_helper.php';

$query = "SELECT * FROM services ORDER BY id ASC";
$result = mysqli_query($con, $query);
$services = [];
while ($row = mysqli_fetch_assoc($result)) {
    $services[] = $row;
}

response('success', 'Services fetched', $services);
?>
