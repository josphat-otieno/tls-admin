<?php
// api/get_clients.php
include_once 'api_helper.php';

$query = "SELECT * FROM clients ORDER BY name ASC";
$result = mysqli_query($con, $query);
$clients = [];
while ($row = mysqli_fetch_assoc($result)) {
    $clients[] = $row;
}

response('success', 'Clients fetched', $clients);
?>
