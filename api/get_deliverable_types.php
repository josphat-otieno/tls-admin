<?php
// api/get_deliverable_types.php
include_once 'api_helper.php';

$query = "SELECT * FROM deliverable_types ORDER BY name ASC";
$result = mysqli_query($con, $query);
$types = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $types[] = $row;
    }
}

response('success', 'Deliverable types fetched', $types);
?>
