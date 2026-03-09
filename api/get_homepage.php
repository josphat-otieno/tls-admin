<?php
// api/get_homepage.php
include_once 'api_helper.php';

$slides_query = "SELECT * FROM homepage_slides WHERE is_active = 1 ORDER BY display_order ASC";
$slides_result = mysqli_query($con, $slides_query);
$slides = [];
while ($row = mysqli_fetch_assoc($slides_result)) {
    $slides[] = $row;
}

$impact_query = "SELECT * FROM impact_summary WHERE id = 1";
$impact_result = mysqli_query($con, $impact_query);
$impact = mysqli_fetch_assoc($impact_result);

response('success', 'Homepage content fetched', [
    'slides' => $slides,
    'impact_summary' => $impact
]);
?>
