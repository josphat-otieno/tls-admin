<?php
// api/get_blogs.php
include_once 'api_helper.php';

$query = "SELECT * FROM blogs ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$blogs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $blogs[] = $row;
}

response('success', 'Blogs fetched', $blogs);
?>
