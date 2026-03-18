<?php
// api/get_projects.php
include_once 'api_helper.php';

$query = "SELECT * FROM projects ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
$projects = [];
while ($project = mysqli_fetch_assoc($result)) {
    $project_id = $project['id'];
    $deliverables_query = "SELECT d.id, d.project_id, d.type_id, d.media_type, d.title, d.file_path, dt.name as type_name 
                          FROM project_deliverables d 
                          JOIN deliverable_types dt ON d.type_id = dt.id 
                          WHERE d.project_id = $project_id";
    $deliverables_result = mysqli_query($con, $deliverables_query);
    $deliverables = [];
    if ($deliverables_result) {
        while ($d = mysqli_fetch_assoc($deliverables_result)) {
            $deliverables[] = $d;
        }
    }
    $project['deliverables'] = $deliverables;
    $projects[] = $project;
}

response('success', 'Projects fetched', $projects);
?>
