<?php
$title = 'Projects Management';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}
$user = $_SESSION['name'];

// Get active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'projects';
// Get status message
$status = '';
$error_msg = '';
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ':
            $status = 'success';
            break;
        case 'err':
            $status = 'error';
            if (isset($_GET['msg'])) {
                switch ($_GET['msg']) {
                    case 'invalid_image':
                        $error_msg = 'Invalid image file! Please upload JPG, PNG, GIF, or WEBP.';
                        break;
                    case 'upload_failed':
                        $error_msg = 'File upload failed! Please try again.';
                        break;
                    default:
                        $error_msg = 'Something went wrong. Please try again.';
                }
            }
            break;
        default:
            $status = '';
    }
}

// Fetch all projects
$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$projects_result = mysqli_query($con, $sql);

// Fetch deliverable types
$types_sql = "SELECT * FROM deliverable_types ORDER BY name ASC";
$types_result = mysqli_query($con, $types_sql);
$deliverable_types = [];
while ($type = mysqli_fetch_assoc($types_result)) {
    $deliverable_types[] = $type;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Projects</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
    <!-- Select2 CSS for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .project-card {
            transition: all 0.2s;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .project-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #dee2e6;
        }

        .project-no-thumbnail {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #adb5bd;
            border-bottom: 1px solid #dee2e6;
        }

        .project-no-thumbnail i {
            font-size: 48px;
        }

        .project-meta {
            font-size: 13px;
            color: #6c757d;
        }

        .project-meta i {
            margin-right: 5px;
        }

        .project-description-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5;
            min-height: 4.5em;
        }

        .image-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-area:hover {
            border-color: #E62B1E;
            background: #f0f1ff;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 10px auto;
            display: none;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
        }

        .remove-image-btn {
            display: none;
            margin-top: 10px;
        }

        /* Deliverables Styles */
        .deliverable-row {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .remove-deliverable {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #ff5b5b;
            cursor: pointer;
            font-size: 18px;
        }

        .existing-deliverable {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .existing-deliverable-info {
            flex-grow: 1;
        }

        .deliverable-badge {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 5px;
        }
    </style>
</head>

<body class="loading" data-layout-color="light" data-layout-mode="default" data-layout-size="fluid"
    data-topbar-color="light" data-leftbar-position="fixed" data-leftbar-color="light" data-leftbar-size='default'
    data-sidebar-user='true'>

    <!-- Success Alert Modal -->
    <div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-success">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-checkmark h1 text-white"></i>
                        <h4 class="mt-2 text-white">Success!</h4>
                        <p class="mt-3 text-white">Operation completed successfully</p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert Modal -->
    <div id="error-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-wrong h1 text-white"></i>
                        <h4 class="mt-2 text-white">Error!</h4>
                        <p class="mt-3 text-white"><?php echo $error_msg ? $error_msg : 'Something went wrong. Please try again'; ?></p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Project Modal -->
    <div id="project-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="project-modal-title">Add Project</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="project-form" action="edit_project.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="project-id">
                        <input type="hidden" name="old_thumbnail" id="old-thumbnail">

                        <div class="mb-3">
                            <label for="title" class="form-label">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="Enter project title">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Project description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" placeholder="Enter client name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Project Image / Thumbnail <small class="text-muted">(Optional)</small></label>
                            <div class="image-upload-area" id="thumbnail-upload-area">
                                <input type="file" name="thumbnail" id="thumbnail-input" accept="image/*" style="display: none;">
                                <i class="mdi mdi-image text-muted" style="font-size: 48px;"></i>
                                <h6 class="mt-2">Click to upload thumbnail</h6>
                                <p class="text-muted mb-0 small">JPG, PNG, GIF or WEBP (Max 2MB)</p>
                                <div class="image-preview" id="thumbnail-preview">
                                    <img src="" alt="Thumbnail preview" id="thumbnail-preview-image">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-image-btn" id="remove-thumbnail-btn">
                                    <i class="mdi mdi-delete"></i> Remove Image
                                </button>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Project Deliverables</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDeliverableRow()">
                                <i class="mdi mdi-plus-circle me-1"></i> Add Deliverable
                            </button>
                        </div>

                        <div id="existing-deliverables-container" class="mb-3" style="display: none;">
                            <label class="form-label">Existing Deliverables</label>
                            <div id="existing-deliverables-list"></div>
                        </div>
                        <input type="hidden" name="deleted_deliverables" id="deleted-deliverables-input">

                        <div id="deliverables-container">
                            <!-- Dynamic rows will be added here -->
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_project" id="project-submit-btn" class="btn btn-primary">Add Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Deliverable Type Modal -->
    <div id="type-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="type-modal-title">Add Deliverable Type</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="type-form" action="edit_deliverable_type.php" method="POST">
                        <input type="hidden" name="id" id="type-id">

                        <div class="mb-3">
                            <label for="type_name" class="form-label">Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="type_name" name="name" required placeholder="e.g., Image, Video, PDF">
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_type" id="type-submit-btn" class="btn btn-primary">Add Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include 'topbar.php' ?>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include 'sidebar.php' ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->

                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-bordered mb-3">
                                        <li class="nav-item">
                                            <a href="#projects-tab" data-bs-toggle="tab" aria-expanded="<?php echo ($active_tab == 'projects') ? 'true' : 'false'; ?>" class="nav-link <?php echo ($active_tab == 'projects') ? 'active' : ''; ?>">
                                                <i class="mdi mdi-briefcase d-md-none d-block me-1"></i>
                                                <span class="d-none d-md-block">Projects</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#types-tab" data-bs-toggle="tab" aria-expanded="<?php echo ($active_tab == 'types') ? 'true' : 'false'; ?>" class="nav-link <?php echo ($active_tab == 'types') ? 'active' : ''; ?>">
                                                <i class="mdi mdi-format-list-bulleted-type d-md-none d-block me-1"></i>
                                                <span class="d-none d-md-block">Deliverable Types</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <!-- Projects Tab -->
                                        <div class="tab-pane <?php echo ($active_tab == 'projects') ? 'show active' : ''; ?>" id="projects-tab">
                                            <div class="row mb-3">
                                                <div class="col-sm-12">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="page-title">Projects</h4>
                                                        <button class="btn btn-primary" onclick="openAddProjectModal()">
                                                            <i class="mdi mdi-plus-circle me-1"></i> Add Project
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="projects-container">
                                                <?php if (mysqli_num_rows($projects_result) > 0): ?>
                                                    <?php mysqli_data_seek($projects_result, 0);
                                                    while ($project = mysqli_fetch_assoc($projects_result)): ?>
                                                        <div class="col-lg-4 project-item">
                                                            <div class="card project-card">
                                                                <?php if (!empty($project['thumbnail'])): ?>
                                                                    <img src="<?php echo htmlspecialchars($project['thumbnail']); ?>" class="project-thumbnail" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                                                <?php else: ?>
                                                                    <div class="project-no-thumbnail">
                                                                        <i class="mdi mdi-briefcase-outline"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="card-body text-dark">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($project['title']); ?></h5>
                                                                        <div class="dropdown">
                                                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="mdi mdi-dots-vertical"></i>
                                                                            </a>
                                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                                <a href="javascript:void(0);" class="dropdown-item" onclick='editProject(<?php echo htmlspecialchars(json_encode($project), ENT_QUOTES, "UTF-8"); ?>)'>
                                                                                    <i class="mdi mdi-pencil me-1"></i>Edit
                                                                                </a>
                                                                                <a href="edit_project.php?delete_project=<?php echo htmlspecialchars($project['id']); ?>"
                                                                                    class="dropdown-item text-danger"
                                                                                    onclick="return confirm('Are you sure you want to delete this project?')">
                                                                                    <i class="mdi mdi-delete me-1"></i>Delete
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <p class="text-muted mb-3 project-description-truncate"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>

                                                                    <div class="project-meta">
                                                                        <?php if (!empty($project['client_name'])): ?>
                                                                            <div class="mb-1">
                                                                                <i class="mdi mdi-domain"></i>
                                                                                <strong>Client:</strong> <?php echo htmlspecialchars($project['client_name']); ?>
                                                                            </div>
                                                                        <?php endif; ?>

                                                                        <?php
                                                                        $p_id = $project['id'];
                                                                        $count_res = $con->query("SELECT COUNT(*) as total FROM project_deliverables WHERE project_id = $p_id");
                                                                        $count_row = $count_res->fetch_assoc();
                                                                        $total_del = $count_row['total'];
                                                                        ?>
                                                                        <div class="mb-1">
                                                                            <i class="mdi mdi-attachment"></i>
                                                                            <strong>Deliverables:</strong> <?php echo $total_del; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-body text-center py-5">
                                                                <i class="mdi mdi-briefcase h1 text-muted"></i>
                                                                <h4 class="mt-3">No Projects Yet</h4>
                                                                <p class="text-muted">Click the "Add Project" button to create your first project.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Deliverable Types Tab -->
                                        <div class="tab-pane <?php echo ($active_tab == 'types') ? 'show active' : ''; ?>" id="types-tab">
                                            <div class="row mb-3">
                                                <div class="col-sm-12">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="page-title">Deliverable Types</h4>
                                                        <button class="btn btn-primary" onclick="openAddTypeModal()">
                                                            <i class="mdi mdi-plus-circle me-1"></i> Add Type
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <?php if (count($deliverable_types) > 0): ?>
                                                    <?php foreach ($deliverable_types as $type): ?>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card h-100 border">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h5 class="mb-0 text-dark"><?php echo htmlspecialchars($type['name']); ?></h5>
                                                                        <div class="dropdown">
                                                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="mdi mdi-dots-vertical"></i>
                                                                            </a>
                                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                                <a href="javascript:void(0);" class="dropdown-item" onclick='editType(<?php echo htmlspecialchars(json_encode($type), ENT_QUOTES, "UTF-8"); ?>)'>
                                                                                    <i class="mdi mdi-pencil me-1"></i>Edit
                                                                                </a>
                                                                                <a href="edit_deliverable_type.php?delete_type=<?php echo $type['id']; ?>" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this type?')">
                                                                                    <i class="mdi mdi-delete me-1"></i>Delete
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="col-12 text-center py-5">
                                                        <i class="mdi mdi-format-list-bulleted-type h1 text-muted"></i>
                                                        <h4 class="mt-3">No Types Yet</h4>
                                                        <p class="text-muted">Click the "Add Type" button to get started.</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div> <!-- end tab-content-->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'footer.php' ?>
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <!-- Select2 JS for searchable dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        // Thumbnail upload handling
        const thumbnailInput = document.getElementById('thumbnail-input');
        const thumbnailUploadArea = document.getElementById('thumbnail-upload-area');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const thumbnailPreviewImage = document.getElementById('thumbnail-preview-image');
        const removeThumbnailBtn = document.getElementById('remove-thumbnail-btn');

        thumbnailUploadArea.addEventListener('click', function(e) {
            if (e.target !== removeThumbnailBtn && !removeThumbnailBtn.contains(e.target)) {
                thumbnailInput.click();
            }
        });

        thumbnailInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbnailPreviewImage.src = e.target.result;
                    thumbnailPreview.style.display = 'block';
                    removeThumbnailBtn.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        });

        removeThumbnailBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            thumbnailInput.value = '';
            thumbnailPreview.style.display = 'none';
            removeThumbnailBtn.style.display = 'none';
        });

        // Project functions
        function openAddProjectModal() {
            document.getElementById('project-modal-title').textContent = 'Add Project';
            document.getElementById('project-form').reset();
            document.getElementById('project-id').value = '';
            document.getElementById('old-thumbnail').value = '';
            document.getElementById('thumbnail-preview').style.display = 'none';
            document.getElementById('remove-thumbnail-btn').style.display = 'none';
            document.getElementById('project-submit-btn').name = 'add_project';
            document.getElementById('project-submit-btn').textContent = 'Add Project';

            // Reset deliverables
            document.getElementById('deliverables-container').innerHTML = '';
            document.getElementById('existing-deliverables-container').style.display = 'none';
            document.getElementById('existing-deliverables-list').innerHTML = '';
            document.getElementById('deleted-deliverables-input').value = '';

            $('#project-modal').modal('show');
        }

        function openAddTypeModal() {
            document.getElementById('type-modal-title').textContent = 'Add Deliverable Type';
            document.getElementById('type-form').reset();
            document.getElementById('type-id').value = '';
            document.getElementById('type-submit-btn').name = 'add_type';
            document.getElementById('type-submit-btn').textContent = 'Add Type';
            $('#type-modal').modal('show');
        }

        function editType(type) {
            document.getElementById('type-modal-title').textContent = 'Edit Deliverable Type';
            document.getElementById('type-id').value = type.id;
            document.getElementById('type_name').value = type.name;
            document.getElementById('type-submit-btn').name = 'update_type';
            document.getElementById('type-submit-btn').textContent = 'Update Type';
            $('#type-modal').modal('show');
        }

        // Maintain active tab in URL
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const tabId = $(e.target).attr('href').replace('#', '');
            const newUrl = window.location.pathname + '?tab=' + (tabId === 'projects-tab' ? 'projects' : 'types');
            window.history.pushState({
                path: newUrl
            }, '', newUrl);
        });

        let deletedDeliverables = [];

        function editProject(project) {
            document.getElementById('project-modal-title').textContent = 'Edit Project';
            document.getElementById('project-id').value = project.id;
            document.getElementById('title').value = project.title;
            document.getElementById('description').value = project.description || '';
            document.getElementById('client_name').value = project.client_name || '';
            document.getElementById('old-thumbnail').value = project.thumbnail || '';

            if (project.thumbnail) {
                thumbnailPreviewImage.src = project.thumbnail;
                thumbnailPreview.style.display = 'block';
                removeThumbnailBtn.style.display = 'inline-block';
            } else {
                thumbnailPreview.style.display = 'none';
                removeThumbnailBtn.style.display = 'none';
            }

            document.getElementById('project-submit-btn').name = 'update_project';
            document.getElementById('project-submit-btn').textContent = 'Update Project';

            // Reset and Load Deliverables
            document.getElementById('deliverables-container').innerHTML = '';
            document.getElementById('deleted-deliverables-input').value = '';
            deletedDeliverables = [];

            fetch(`api/get_projects.php`)
                .then(response => response.json())
                .then(res => {
                    const currentProject = res.data.find(p => p.id == project.id);
                    if (currentProject && currentProject.deliverables && currentProject.deliverables.length > 0) {
                        displayExistingDeliverables(currentProject.deliverables);
                    } else {
                        document.getElementById('existing-deliverables-container').style.display = 'none';
                    }
                });

            $('#project-modal').modal('show');
        }

        function displayExistingDeliverables(deliverables) {
            const container = document.getElementById('existing-deliverables-list');
            const parent = document.getElementById('existing-deliverables-container');
            container.innerHTML = '';
            parent.style.display = 'block';

            deliverables.forEach(d => {
                const div = document.createElement('div');
                div.className = 'existing-deliverable';
                div.innerHTML = `
                    <div class="existing-deliverable-info">
                        <strong>${d.title || 'Untitled'}</strong> 
                        <span class="badge bg-secondary deliverable-badge">${d.type_name}</span>
                        <span class="badge bg-info deliverable-badge">${d.media_type}</span>
                        <br>
                        <small class="text-muted">${d.file_path.split('/').pop()}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="markForDeletion(${d.id}, this)">
                        <i class="mdi mdi-delete"></i>
                    </button>
                `;
                container.appendChild(div);
            });
        }

        function markForDeletion(id, btn) {
            if (confirm('Are you sure you want to delete this deliverable?')) {
                deletedDeliverables.push(id);
                document.getElementById('deleted-deliverables-input').value = deletedDeliverables.join(',');
                btn.closest('.existing-deliverable').style.opacity = '0.5';
                btn.closest('.existing-deliverable').style.pointerEvents = 'none';
                btn.innerHTML = 'Removed';
                btn.className = 'btn btn-sm btn-secondary';
            }
        }

        const deliverableTypes = <?php echo json_encode($deliverable_types); ?>;
        let deliverableIndex = 0;

        function addDeliverableRow() {
            const container = document.getElementById('deliverables-container');
            const div = document.createElement('div');
            div.className = 'deliverable-row';

            let typesOptions = '';
            deliverableTypes.forEach(type => {
                typesOptions += `<option value="${type.id}">${type.name}</option>`;
            });

            div.innerHTML = `
                <span class="remove-deliverable" onclick="this.parentElement.remove()">
                    <i class="mdi mdi-close-circle"></i>
                </span>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Deliverable Title</label>
                        <input type="text" name="deliverable_titles[]" class="form-control form-control-sm" placeholder="Title (e.g. Logo Design)">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Media Type</label>
                        <select name="deliverable_media_types[]" class="form-select form-select-sm">
                            <option value="Image">Image</option>
                            <option value="Video">Video</option>
                            <option value="Audio">Audio</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Category (Type)</label>
                        <select name="deliverable_types[]" class="form-select form-select-sm">
                            ${typesOptions}
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small">File</label>
                        <input type="file" name="deliverable_files[]" class="form-control form-control-sm" required>
                    </div>
                </div>
            `;
            container.appendChild(div);
            deliverableIndex++;
        }
    </script>

</body>

</html>