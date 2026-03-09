<?php
// api/get_projects.php
include_once 'api_helper.php';

$query = "SELECT * FROM projects ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$projects = [];
while ($row = mysqli_fetch_assoc($result)) {
    $projects[] = $row;
}

response('success', 'Projects fetched', $projects);
?>
