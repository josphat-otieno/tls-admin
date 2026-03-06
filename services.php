<?php
$title = 'Our Services';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}
$user = $_SESSION['name'];
 

// Get status message
$status = '';
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ':
            $status = 'success';
            break;
        case 'err':
            $status = 'error';
            break;
        default:
            $status = '';
    }
}

// Fetch all services
$sql = "SELECT * FROM services ORDER BY id ASC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Services</title>
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

    <style>
        .service-card {
            transition: all 0.3s;
            border-left: 4px solid #E62B1E;
            height: 100%;
            overflow: hidden;
        }
        .service-thumbnail {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 2px solid #f1f3fa;
        }
        .service-no-thumbnail {
            width: 100%;
            height: 160px;
            background: #f1f3fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            border-bottom: 2px solid #f1f3fa;
        }
        .service-no-thumbnail i {
            font-size: 48px;
        }
        .service-description-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5;
            max-height: 6em; /* 4 lines * 1.5 line-height */
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-left-color: #0acf97;
        }
        .service-number {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .service-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .service-title {
            font-size: 18px;
            font-weight: 600;
            color: #313a46;
            margin: 0;
        }
        .service-description {
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        .service-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        .thumbnail-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
        .thumbnail-upload-area:hover {
            border-color: #E62B1E;
            background: #f0f1ff;
        }
        .thumbnail-preview {
            max-width: 200px;
            max-height: 150px;
            margin: 10px auto;
            display: none;
        }
        .thumbnail-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
        }
        .remove-thumbnail-btn {
            display: none;
            margin-top: 10px;
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
                        <p class="mt-3 text-white">Service has been updated successfully</p>
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
                        <p class="mt-3 text-white">Something went wrong. Please try again</p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Service Modal -->
    <div id="service-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Service</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="service-form" action="edit_service.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="service-id">
                        <input type="hidden" name="old_thumbnail" id="old-thumbnail">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Strategic Consulting">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Describe what this service offers..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Thumbnail</label>
                            <div class="thumbnail-upload-area" id="thumbnail-upload-area">
                                <input type="file" id="service_thumbnail" name="service_thumbnail" accept="image/*" style="display: none;">
                                <i class="mdi mdi-image text-muted" style="font-size: 48px;"></i>
                                <h6 class="mt-2">Click to upload thumbnail</h6>
                                <p class="text-muted mb-0 small">JPG, PNG, GIF or WEBP (Max 2MB)</p>
                                <div class="thumbnail-preview" id="thumbnail-preview-container">
                                    <img src="" alt="Thumbnail preview" id="preview-thumbnail">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-thumbnail-btn" id="remove-thumbnail-btn">
                                    <i class="mdi mdi-delete"></i> Remove Thumbnail
                                </button>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_service" id="submit-btn" class="btn btn-primary">Add Service</button>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <button class="btn btn-primary" onclick="openAddModal()">
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Service
                                    </button>
                                </div>
                                <!-- <h4 class="page-title">Our Services</h4> -->
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php 
                            $counter = 1;
                            while($service = mysqli_fetch_assoc($result)): 
                            ?>
                                <div class="col-lg-4 mb-4">
                                    <div class="card service-card">
                                        <div class="service-number"><?php echo $counter; ?></div>
                                        <?php if(!empty($service['thumbnail'])): ?>
                                            <img src="<?php echo htmlspecialchars($service['thumbnail']); ?>" class="service-thumbnail" alt="<?php echo htmlspecialchars($service['name']); ?>">
                                        <?php else: ?>
                                            <div class="service-no-thumbnail">
                                                <i class="mdi mdi-briefcase-outline"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column">
                                            <div class="service-header">
                                                <div class="flex-grow-1">
                                                    <h5 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                                                </div>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0);" class="dropdown-item" onclick='editService(<?php echo htmlspecialchars(json_encode($service), ENT_QUOTES, "UTF-8"); ?>)'>
                                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                                        </a>
                                                        <a href="edit_service.php?delete_service=<?php echo $service['id']; ?>" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this service?')">
                                                            <i class="mdi mdi-delete me-1"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <p class="service-description service-description-truncate"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            $counter++;
                            endwhile; 
                            ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="mdi mdi-briefcase-outline h1 text-muted"></i>
                                        <h4 class="mt-3">No Services Yet</h4>
                                        <p class="text-muted">Click the "Add Service" button to get started.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Stats Card -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h4 class="text-white mb-2">
                                                <i class="mdi mdi-briefcase-check me-2"></i>
                                                Total Services Offered
                                            </h4>
                                            <p class="text-white-50 mb-0">We provide comprehensive solutions to meet your business needs</p>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <h1 class="text-white mb-0"><?php echo mysqli_num_rows($result); ?></h1>
                                        </div>
                                    </div>
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
    <?php include 'common_cropper.php'; ?>

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
        const thumbnailInput = document.getElementById('service_thumbnail');
        const thumbnailUploadArea = document.getElementById('thumbnail-upload-area');
        const thumbnailPreviewContainer = document.getElementById('thumbnail-preview-container');
        const previewThumbnail = document.getElementById('preview-thumbnail');
        const removeThumbnailBtn = document.getElementById('remove-thumbnail-btn');

        thumbnailUploadArea.addEventListener('click', function(e) {
            if (e.target !== removeThumbnailBtn && !removeThumbnailBtn.contains(e.target)) {
                thumbnailInput.click();
            }
        });

        thumbnailInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                if (hasSpecialChars(file.name)) {
                    showFilenameWarning(file.name);
                    this.value = '';
                    return;
                }
                
                // Use the common cropper
                initCropper(file, 'service_thumbnail', 'preview-thumbnail', 4/3);
            }
        });

        removeThumbnailBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            thumbnailInput.value = '';
            thumbnailPreviewContainer.style.display = 'none';
            removeThumbnailBtn.style.display = 'none';
            document.getElementById('old-thumbnail').value = '';
        });

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Service';
            document.getElementById('service-form').reset();
            document.getElementById('service-id').value = '';
            document.getElementById('old-thumbnail').value = '';
            thumbnailPreviewContainer.style.display = 'none';
            removeThumbnailBtn.style.display = 'none';
            document.getElementById('submit-btn').name = 'add_service';
            document.getElementById('submit-btn').textContent = 'Add Service';
            $('#service-modal').modal('show');
        }

        function editService(service) {
            document.getElementById('modal-title').textContent = 'Edit Service';
            document.getElementById('service-id').value = service.id;
            document.getElementById('name').value = service.name;
            document.getElementById('description').value = service.description;
            document.getElementById('old-thumbnail').value = service.thumbnail || '';
            
            if (service.thumbnail) {
                previewThumbnail.src = service.thumbnail;
                thumbnailPreviewContainer.style.display = 'block';
                removeThumbnailBtn.style.display = 'inline-block';
            } else {
                thumbnailPreviewContainer.style.display = 'none';
                removeThumbnailBtn.style.display = 'none';
            }

            document.getElementById('submit-btn').name = 'update_service';
            document.getElementById('submit-btn').textContent = 'Update Service';
            $('#service-modal').modal('show');
        }
    </script>

</body>

</html>
