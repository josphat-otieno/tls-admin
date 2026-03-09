<?php
// api/get_team.php
include_once 'api_helper.php';

$team_query = "SELECT * FROM members WHERE member_type = 'team' ORDER BY id ASC";
$team_result = mysqli_query($con, $team_query);
$team = [];
while ($row = mysqli_fetch_assoc($team_result)) {
    $team[] = $row;
}

response('success', 'Team members fetched', $team);
?>
