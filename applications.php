<?php
$title = 'Job Applications';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
    exit();
}
$user = $_SESSION['name'];

$status = '';
$error_msg = '';
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ': $status = 'success'; break;
        case 'err':  $status = 'error'; $error_msg = 'Something went wrong. Please try again.'; break;
    }
}

// Fetch all applications with job details
$query = "SELECT a.*, c.title as job_title 
          FROM job_applications a 
          LEFT JOIN careers c ON a.job_id = c.job_id 
          ORDER BY a.created_at DESC";
$apps_result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Applications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
    <style>
        .app-card { transition: all 0.3s; border-radius: 10px; }
        .app-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .status-badge { font-size: 11px; padding: 4px 10px; border-radius: 12px; font-weight: 600; }
        .job-id-badge { font-family: monospace; letter-spacing: 1px; }
    </style>
</head>
<body class="loading" data-layout-color="light" data-layout-mode="default" data-layout-size="fluid"
    data-topbar-color="light" data-leftbar-position="fixed" data-leftbar-color="light" data-leftbar-size='default'
    data-sidebar-user='true'>

    <!-- Success Alert Modal -->
    <div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-success">
                <div class="modal-body text-center">
                    <i class="dripicons-checkmark h1 text-white"></i>
                    <h4 class="mt-2 text-white">Success!</h4>
                    <p class="mt-3 text-white">Operation completed successfully</p>
                    <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert Modal -->
    <div id="error-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger">
                <div class="modal-body text-center">
                    <i class="dripicons-wrong h1 text-white"></i>
                    <h4 class="mt-2 text-white">Error!</h4>
                    <p class="mt-3 text-white"><?php echo htmlspecialchars($error_msg ?: 'Something went wrong. Please try again.'); ?></p>
                    <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ VIEW APPLICATION MODAL ═══════════════════════════════════════════ -->
    <div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Application Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="app-details-content">
                        <!-- Content loaded via JS -->
                    </div>
                    <hr>
                    <form action="edit_application.php" method="POST">
                        <input type="hidden" name="id" id="status-app-id">
                        <div class="d-flex align-items-center gap-3">
                            <label for="app-status" class="form-label mb-0">Update Status:</label>
                            <select class="form-select w-auto" name="status" id="app-status-select">
                                <option value="Pending">Pending</option>
                                <option value="Reviewed">Reviewed</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ DELETE CONFIRM MODAL ══════════════════════════════════════════════ -->
    <div id="delete-confirm-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="mdi mdi-alert-circle-outline h1 text-danger"></i>
                    <p class="mt-2">Are you sure you want to delete this application? This cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirm-delete-btn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">
        <?php include 'topbar.php' ?>
        <?php include 'sidebar.php' ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Header -->
                    <div class="row mb-3 align-items-center">
                        <div class="col">
                            <h4 class="page-title mb-1"><i class="mdi mdi-file-document-outline me-2 text-primary"></i>Job Applications</h4>
                            <p class="text-muted mb-0">View and manage applications submitted for active job openings.</p>
                        </div>
                    </div>

                    <!-- Summary counts -->
                    <?php
                    $total_apps    = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM job_applications"))['c'];
                    $pending_apps  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM job_applications WHERE status = 'Pending'"))['c'];
                    ?>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card widget-flat text-bg-primary">
                                <div class="card-body">
                                    <h5 class="fw-normal mt-0">Total Applications</h5>
                                    <h2 class="my-2"><?php echo $total_apps; ?></h2>
                                    <p class="mb-0 text-white-50"><i class="mdi mdi-file-document me-1"></i>All submissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card widget-flat text-bg-warning">
                                <div class="card-body">
                                    <h5 class="fw-normal mt-0">Pending Review</h5>
                                    <h2 class="my-2"><?php echo $pending_apps; ?></h2>
                                    <p class="mb-0 text-white-50"><i class="mdi mdi-clock-outline me-1"></i>New applications</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applications list -->
                    <?php if (mysqli_num_rows($apps_result) > 0): ?>
                        <div class="row">
                            <?php while($app = mysqli_fetch_assoc($apps_result)): ?>
                                <?php
                                $status_colors = [
                                    'Pending'  => 'bg-soft-warning text-warning',
                                    'Reviewed' => 'bg-soft-info text-info',
                                    'Accepted' => 'bg-soft-success text-success',
                                    'Rejected' => 'bg-soft-danger text-danger',
                                ];
                                $status_color = $status_colors[$app['status']] ?? 'bg-soft-secondary text-secondary';
                                ?>
                                <div class="col-12 mb-3">
                                    <div class="card app-card border-start border-4 <?php echo str_replace('bg-soft-', 'border-', $status_color); ?>">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($app['name']); ?></h5>
                                                        <span class="badge <?php echo $status_color; ?> status-badge"><?php echo $app['status']; ?></span>
                                                        <span class="text-muted small">ID: <span class="badge bg-light text-dark job-id-badge"><?php echo htmlspecialchars($app['job_id']); ?></span></span>
                                                    </div>
                                                    <div class="text-primary fw-bold mb-2"><?php echo htmlspecialchars($app['job_title'] ?: 'Unknown Position'); ?></div>
                                                    
                                                    <div class="d-flex flex-wrap gap-3 text-muted" style="font-size: 13px;">
                                                        <span><i class="mdi mdi-email-outline me-1"></i><?php echo htmlspecialchars($app['email']); ?></span>
                                                        <span><i class="mdi mdi-phone-outline me-1"></i><?php echo htmlspecialchars($app['phone']); ?></span>
                                                        <span><i class="mdi mdi-calendar-outline me-1"></i>Applied: <?php echo date('M d, Y', strtotime($app['created_at'])); ?></span>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2 align-self-center">
                                                    <button class="btn btn-sm btn-outline-primary" onclick='viewApp(<?php echo htmlspecialchars(json_encode($app), ENT_QUOTES, "UTF-8"); ?>)'>
                                                        <i class="mdi mdi-eye me-1"></i>View
                                                    </button>
                                                    <a href="<?php echo htmlspecialchars($app['cv_path']); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="mdi mdi-file-download me-1"></i>CV
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo $app['id']; ?>)">
                                                        <i class="mdi mdi-delete me-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="mdi mdi-file-document-outline h1 text-muted"></i>
                                <h4 class="mt-3">No Applications Yet</h4>
                                <p class="text-muted">New submissions will appear here once they are submitted through the website.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/app.min.js"></script>

    <script>
    $(document).ready(function() {
        var status = "<?php echo $status; ?>";
        if (status === "success") $('#success-alert-modal').modal('show');
        else if (status === "error")  $('#error-alert-modal').modal('show');
    });

    function viewApp(app) {
        document.getElementById('status-app-id').value = app.id;
        document.getElementById('app-status-select').value = app.status;
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> ${app.name}</p>
                    <p><strong>Email:</strong> ${app.email}</p>
                    <p><strong>Phone:</strong> ${app.phone}</p>
                    <p><strong>Job Title:</strong> ${app.job_title || 'N/A'} (ID: ${app.job_id})</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Portfolio:</strong> ${app.portfolio_link ? `<a href="${app.portfolio_link}" target="_blank">${app.portfolio_link}</a>` : 'Not provided'}</p>
                    <p><strong>Submitted:</strong> ${new Date(app.created_at).toLocaleString()}</p>
                    <p><strong>CV:</strong> <a href="${app.cv_path}" target="_blank" class="btn btn-xs btn-info">View CV</a></p>
                </div>
                <div class="col-12 mt-3">
                    <p><strong>About:</strong></p>
                    <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;">${app.about_self || 'No information provided.'}</div>
                </div>
            </div>
        `;
        document.getElementById('app-details-content').innerHTML = html;
        $('#view-modal').modal('show');
    }

    function confirmDelete(id) {
        document.getElementById('confirm-delete-btn').href = 'edit_application.php?delete_app=' + id;
        $('#delete-confirm-modal').modal('show');
    }
    </script>
</body>
</html>
