<?php
// api/get_testimonials.php
include_once 'api_helper.php';

$query = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC";
$result = mysqli_query($con, $query);
$testimonials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $testimonials[] = $row;
}

response('success', 'Testimonials fetched', $testimonials);
?>
