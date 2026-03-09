<?php
// api/get_careers.php
include_once 'api_helper.php';

$query = "SELECT * FROM careers WHERE is_active = 1 ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$careers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $careers[] = $row;
}

response('success', 'Careers fetched', $careers);
?>
