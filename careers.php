<?php
$title = 'Careers Management';
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

// Fetch all careers
$careers_result = mysqli_query($con, "SELECT * FROM careers ORDER BY is_active DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Careers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
    <style>
        .career-card { transition: all 0.3s; border-radius: 10px; }
        .career-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .career-status-badge { font-size: 11px; padding: 4px 10px; border-radius: 12px; font-weight: 600; }
        .job-type-badge { font-size: 11px; padding: 3px 10px; border-radius: 10px; }
        .deadline-text { font-size: 12px; }
        .deadline-expired { color: #dc3545; }
        .deadline-upcoming { color: #28a745; }
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

    <!-- ══ ADD / EDIT CAREER MODAL ═══════════════════════════════════════════ -->
    <div id="career-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="career-modal-title">Post New Opening</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="career-form" action="edit_career.php" method="POST">
                        <input type="hidden" name="id" id="career-id">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="career-title" class="form-label">Job Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="career-title" name="title" required placeholder="e.g. Senior Brand Designer">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="career-type" class="form-label">Job Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="career-type" name="job_type" required>
                                        <option value="Full-Time">Full-Time</option>
                                        <option value="Part-Time">Part-Time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Remote">Remote</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="career-location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="career-location" name="location" placeholder="e.g. Nairobi, Kenya">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="career-department" class="form-label">Department</label>
                                    <input type="text" class="form-control" id="career-department" name="department" placeholder="e.g. Creative, Strategy">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="career-description" class="form-label">Job Description</label>
                            <textarea class="form-control" id="career-description" name="description" rows="4"
                                      placeholder="Describe the role, responsibilities, and what you are looking for..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="career-requirements" class="form-label">Requirements</label>
                            <textarea class="form-control" id="career-requirements" name="requirements" rows="4"
                                      placeholder="List qualifications, experience, skills required..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="career-deadline" class="form-label">Application Deadline</label>
                                    <input type="date" class="form-control" id="career-deadline" name="deadline">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="mb-3 w-100">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="career-active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="career-active">
                                            <strong>Active / Visible</strong>
                                            <small class="text-muted d-block">Uncheck to hide this opening.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_career" id="career-submit-btn" class="btn btn-primary">
                                <i class="mdi mdi-briefcase-plus me-1"></i> Post Opening
                            </button>
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
                    <p class="mt-2">Are you sure you want to delete this job opening? This cannot be undone.</p>
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
                            <h4 class="page-title mb-1"><i class="mdi mdi-briefcase-search me-2 text-primary"></i>Careers</h4>
                            <p class="text-muted mb-0">Manage current job openings visible on your website.</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" onclick="openAddCareerModal()">
                                <i class="mdi mdi-plus-circle me-1"></i> Post New Opening
                            </button>
                        </div>
                    </div>

                    <!-- Summary counts -->
                    <?php
                    $total    = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM careers"))['c'];
                    $active   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM careers WHERE is_active = 1"))['c'];
                    $inactive = $total - $active;
                    ?>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card widget-flat text-bg-primary">
                                <div class="card-body">
                                    <h5 class="fw-normal mt-0">Total Openings</h5>
                                    <h2 class="my-2"><?php echo $total; ?></h2>
                                    <p class="mb-0 text-white-50"><i class="mdi mdi-briefcase me-1"></i>All job postings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card widget-flat text-bg-success">
                                <div class="card-body">
                                    <h5 class="fw-normal mt-0">Active</h5>
                                    <h2 class="my-2"><?php echo $active; ?></h2>
                                    <p class="mb-0 text-white-50"><i class="mdi mdi-check-circle me-1"></i>Visible on website</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card widget-flat text-bg-secondary">
                                <div class="card-body">
                                    <h5 class="fw-normal mt-0">Inactive</h5>
                                    <h2 class="my-2"><?php echo $inactive; ?></h2>
                                    <p class="mb-0 text-white-50"><i class="mdi mdi-eye-off me-1"></i>Hidden from website</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Careers list -->
                    <?php if (mysqli_num_rows($careers_result) > 0): ?>
                        <?php while($career = mysqli_fetch_assoc($careers_result)): ?>
                            <?php
                            $is_active = $career['is_active'];
                            $deadline  = $career['deadline'];
                            $expired   = $deadline && strtotime($deadline) < strtotime('today');

                            $type_colors = [
                                'Full-Time'  => 'bg-soft-primary text-primary',
                                'Part-Time'  => 'bg-soft-info text-info',
                                'Contract'   => 'bg-soft-warning text-warning',
                                'Internship' => 'bg-soft-success text-success',
                                'Remote'     => 'bg-soft-secondary text-secondary',
                            ];
                            $type_color = $type_colors[$career['job_type']] ?? 'bg-soft-primary text-primary';
                            ?>
                            <div class="card career-card mb-3 <?php echo !$is_active ? 'border-secondary opacity-75' : 'border-success'; ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <h5 class="mb-0"><?php echo htmlspecialchars($career['title']); ?></h5>
                                                <span class="badge bg-soft-dark text-dark job-type-badge" style="font-family:monospace;letter-spacing:1px;" title="Job ID"><?php echo htmlspecialchars($career['job_id']); ?></span>
                                                <span class="badge <?php echo $type_color; ?> job-type-badge"><?php echo htmlspecialchars($career['job_type']); ?></span>
                                                <?php if ($is_active): ?>
                                                    <span class="badge bg-soft-success text-success career-status-badge"><i class="mdi mdi-check-circle me-1"></i>Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-soft-secondary text-secondary career-status-badge"><i class="mdi mdi-eye-off me-1"></i>Inactive</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-flex flex-wrap gap-3 text-muted mt-1" style="font-size: 13px;">
                                                <?php if ($career['location']): ?>
                                                    <span><i class="mdi mdi-map-marker me-1"></i><?php echo htmlspecialchars($career['location']); ?></span>
                                                <?php endif; ?>
                                                <?php if ($career['department']): ?>
                                                    <span><i class="mdi mdi-office-building me-1"></i><?php echo htmlspecialchars($career['department']); ?></span>
                                                <?php endif; ?>
                                                <span><i class="mdi mdi-calendar-plus me-1"></i>Posted: <?php echo date('M d, Y', strtotime($career['created_at'])); ?></span>
                                                <?php if ($deadline): ?>
                                                    <span class="deadline-text <?php echo $expired ? 'deadline-expired' : 'deadline-upcoming'; ?>">
                                                        <i class="mdi mdi-calendar-clock me-1"></i>
                                                        Deadline: <?php echo date('M d, Y', strtotime($deadline)); ?>
                                                        <?php echo $expired ? '<strong>(Expired)</strong>' : ''; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($career['description']): ?>
                                                <p class="text-muted mt-2 mb-0" style="font-size: 13px; line-height: 1.5;">
                                                    <?php echo nl2br(htmlspecialchars(substr($career['description'], 0, 200))); ?>
                                                    <?php echo strlen($career['description']) > 200 ? '...' : ''; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 flex-shrink-0">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    onclick='editCareer(<?php echo htmlspecialchars(json_encode($career), ENT_QUOTES, "UTF-8"); ?>)'>
                                                <i class="mdi mdi-pencil me-1"></i>Edit
                                            </button>
                                            <a href="edit_career.php?toggle_career=<?php echo $career['id']; ?>"
                                               class="btn btn-sm <?php echo $is_active ? 'btn-outline-secondary' : 'btn-outline-success'; ?>">
                                                <i class="mdi mdi-<?php echo $is_active ? 'eye-off' : 'eye'; ?> me-1"></i>
                                                <?php echo $is_active ? 'Deactivate' : 'Activate'; ?>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete(<?php echo $career['id']; ?>)">
                                                <i class="mdi mdi-delete me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="mdi mdi-briefcase-search h1 text-muted"></i>
                                <h4 class="mt-3">No Job Openings Yet</h4>
                                <p class="text-muted">Click "Post New Opening" to add your first career listing.</p>
                                <button class="btn btn-primary" onclick="openAddCareerModal()">
                                    <i class="mdi mdi-plus-circle me-1"></i> Post New Opening
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div><!-- END wrapper -->

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

    function openAddCareerModal() {
        document.getElementById('career-modal-title').textContent = 'Post New Opening';
        document.getElementById('career-form').reset();
        document.getElementById('career-id').value = '';
        document.getElementById('career-active').checked = true;
        document.getElementById('career-submit-btn').name = 'add_career';
        document.getElementById('career-submit-btn').innerHTML = '<i class="mdi mdi-briefcase-plus me-1"></i> Post Opening';
        $('#career-modal').modal('show');
    }

    function editCareer(career) {
        document.getElementById('career-modal-title').textContent = 'Edit Opening';
        document.getElementById('career-id').value = career.id;
        document.getElementById('career-title').value = career.title || '';
        document.getElementById('career-type').value = career.job_type || 'Full-Time';
        document.getElementById('career-location').value = career.location || '';
        document.getElementById('career-department').value = career.department || '';
        document.getElementById('career-description').value = career.description || '';
        document.getElementById('career-requirements').value = career.requirements || '';
        document.getElementById('career-deadline').value = career.deadline || '';
        document.getElementById('career-active').checked = career.is_active == 1;
        document.getElementById('career-submit-btn').name = 'update_career';
        document.getElementById('career-submit-btn').innerHTML = '<i class="mdi mdi-content-save me-1"></i> Update Opening';
        $('#career-modal').modal('show');
    }

    function confirmDelete(id) {
        document.getElementById('confirm-delete-btn').href = 'edit_career.php?delete_career=' + id;
        $('#delete-confirm-modal').modal('show');
    }
    </script>
</body>
</html>
