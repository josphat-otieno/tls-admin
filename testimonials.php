<?php
$title = 'Testimonials';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}
$user = $_SESSION['name'];

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
                switch($_GET['msg']) {
                    case 'invalid_image': $error_msg = 'Invalid image file! Please upload JPG, PNG, GIF, or WEBP.'; break;
                    case 'file_too_large': $error_msg = 'File too large! Maximum size is 10MB.'; break;
                    case 'upload_failed': $error_msg = 'Upload failed! Please try again.'; break;
                    default: $error_msg = 'Something went wrong. Please try again.'; break;
                }
            }
            break;
        default:
            $status = '';
    }
}

// Fetch all testimonials
$sql = "SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Testimonials</title>
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
        .testimonial-card {
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            background: white;
        }
        .testimonial-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .testimonial-header {
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
            padding: 20px;
            color: white;
            position: relative;
        }
        .quote-icon {
            font-size: 48px;
            opacity: 0.3;
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .testimonial-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .testimonial-role {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 3px;
        }
        .testimonial-company {
            font-size: 13px;
            opacity: 0.8;
        }
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-badge.active {
            background: #198754;
            color: white;
        }
        .status-badge.inactive {
            background: #6c757d;
            color: white;
        }
        .testimonial-body {
            padding: 20px;
        }
        .testimonial-text {
            font-size: 15px;
            line-height: 1.6;
            color: #495057;
            font-style: italic;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .testimonial-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding-top: 15px;
            border-top: 1px solid #e3e6f0;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 20px;
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
            max-width: 150px;
            max-height: 150px;
            margin: 10px auto;
            display: none;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
        }
        .remove-image-btn {
            display: none;
            margin-top: 10px;
        }
        #filename-warning-modal {
            z-index: 1060;
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
                        <p class="mt-3 text-white">Testimonial has been updated successfully</p>
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

    <!-- Filename Warning Modal -->
    <div id="filename-warning-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title text-white">Invalid Filename</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="mdi mdi-alert-circle-outline h1 text-warning"></i>
                    </div>
                    <h5>Filename contains special characters!</h5>
                    <p class="text-muted">Characters like apostrophes ('), quotes ("), and special symbols can cause issues with file storage.</p>
                    
                    <div class="alert alert-light border shadow-sm">
                        <div class="mb-2 text-bold text-dark"><strong>Current:</strong> <span id="warn-filename-current" class="text-danger"></span></div>
                        <div class="text-bold text-dark"><strong>Suggested:</strong> <span id="warn-filename-suggested" class="text-success"></span></div>
                    </div>
                    
                    <p class="text-bold text-dark mb-0">Please rename your file and try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning text-white" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Testimonial Modal -->
    <div id="testimonial-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Testimonial</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="testimonial-form" action="edit_testimonial.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="testimonial-id">
                        <input type="hidden" name="old_profile_image" id="old-profile-image">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter person's name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role/Position <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="role" name="role" required placeholder="e.g., CEO, Manager">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="company" class="form-label">Company <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company" name="company" required placeholder="Enter company name">
                        </div>

                        <div class="mb-3">
                            <label for="testimony" class="form-label">Testimonial <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="testimony" name="testimony" rows="5" required 
                                      placeholder="Enter the testimonial text..."></textarea>
                            <small class="text-muted">Share what the client said about your services</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image <small class="text-muted">(Optional)</small></label>
                            <div class="image-upload-area" id="image-upload-area">
                                <input type="file" name="profile_image" id="image-input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
                                <i class="mdi mdi-account-circle text-muted" style="font-size: 48px;"></i>
                                <h6 class="mt-2">Click to upload profile image</h6>
                                <p class="text-muted mb-0 small">JPG, PNG, GIF or WEBP (Max 2MB)</p>
                                <div class="image-preview" id="image-preview">
                                    <img src="" alt="Profile preview" id="preview-image">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-image-btn" id="remove-image-btn">
                                    <i class="mdi mdi-delete"></i> Remove Image
                                </button>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_testimonial" id="submit-btn" class="btn btn-primary">Add Testimonial</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Testimonial
                                    </button>
                                </div>
                                <h4 class="page-title">Testimonials</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Testimonials Grid -->
                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($testimonial = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-6">
                                    <div class="card testimonial-card">
                                        <div class="testimonial-header">
                                            <span class="status-badge <?php echo $testimonial['is_active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                            <i class="mdi mdi-format-quote-close quote-icon"></i>
                                            <div class="d-flex align-items-center mb-2" style="position: relative; z-index: 1;">
                                                <?php if(!empty($testimonial['profile_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($testimonial['profile_image']); ?>" alt="Profile" 
                                                         class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="testimonial-name"><?php echo htmlspecialchars($testimonial['name']); ?></div>
                                                    <div class="testimonial-role"><?php echo htmlspecialchars($testimonial['role']); ?></div>
                                                </div>
                                            </div>
                                            <div class="testimonial-company">
                                                <i class="mdi mdi-domain"></i> <?php echo htmlspecialchars($testimonial['company']); ?>
                                            </div>
                                        </div>
                                        <div class="testimonial-body">
                                            <p class="testimonial-text">"<?php echo nl2br(htmlspecialchars($testimonial['testimony'])); ?>"</p>
                                            <div class="testimonial-actions">
                                                <button class="btn btn-sm btn-soft-primary" onclick='editTestimonial(<?php echo htmlspecialchars(json_encode($testimonial), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <a href="edit_testimonial.php?toggle_testimonial=<?php echo $testimonial['id']; ?>" 
                                                   class="btn btn-sm btn-soft-<?php echo $testimonial['is_active'] ? 'warning' : 'success'; ?>">
                                                    <i class="mdi mdi-<?php echo $testimonial['is_active'] ? 'eye-off' : 'eye'; ?>"></i> 
                                                    <?php echo $testimonial['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </a>
                                                <a href="edit_testimonial.php?delete_testimonial=<?php echo $testimonial['id']; ?>" 
                                                   class="btn btn-sm btn-soft-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body empty-state">
                                        <i class="mdi mdi-comment-quote"></i>
                                        <h4>No Testimonials Yet</h4>
                                        <p class="text-muted">Click the "Add Testimonial" button to add your first client testimonial</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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

        // Image upload handling
        const imageInput = document.getElementById('image-input');
        const imageUploadArea = document.getElementById('image-upload-area');
        const imagePreview = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');
        const removeImageBtn = document.getElementById('remove-image-btn');

        imageUploadArea.addEventListener('click', function(e) {
            if (e.target !== removeImageBtn && !removeImageBtn.contains(e.target)) {
                imageInput.click();
            }
        });

        imageInput.addEventListener('change', function(e) {
            handleFile(this.files[0]);
        });

        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
        });



        function handleFile(file) {
            if (!file) return;

            if (hasSpecialChars(file.name)) {
                showFilenameWarning(file.name);
                imageInput.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPG, PNG, GIF, or WEBP)');
                imageInput.value = '';
                return;
            }

            // Use the common cropper with 1:1 aspect ratio for client photos
            initCropper(file, 'image-input', 'preview-image', 1);
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Testimonial';
            document.getElementById('testimonial-form').reset();
            document.getElementById('testimonial-id').value = '';
            document.getElementById('old-profile-image').value = '';
            document.getElementById('submit-btn').name = 'add_testimonial';
            document.getElementById('submit-btn').textContent = 'Add Testimonial';
            
            // Hide image preview
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
            
            $('#testimonial-modal').modal('show');
        }

        function editTestimonial(testimonial) {
            document.getElementById('modal-title').textContent = 'Edit Testimonial';
            document.getElementById('testimonial-id').value = testimonial.id;
            document.getElementById('old-profile-image').value = testimonial.profile_image || '';
            document.getElementById('name').value = testimonial.name;
            document.getElementById('role').value = testimonial.role;
            document.getElementById('company').value = testimonial.company;
            document.getElementById('testimony').value = testimonial.testimony;
            
            // Show image preview if exists
            if (testimonial.profile_image) {
                previewImage.src = testimonial.profile_image;
                imagePreview.style.display = 'block';
                removeImageBtn.style.display = 'inline-block';
            } else {
                imagePreview.style.display = 'none';
                removeImageBtn.style.display = 'none';
            }
            
            document.getElementById('submit-btn').name = 'update_testimonial';
            document.getElementById('submit-btn').textContent = 'Update Testimonial';
            $('#testimonial-modal').modal('show');
        }

        // Block submission if filename has special chars
        document.getElementById("testimonial-form").addEventListener("submit", function (e) {
            const input = document.getElementById("image-input");
            const file = input?.files?.[0];
            if (file && hasSpecialChars(file.name)) {
                e.preventDefault();
                showFilenameWarning(file.name);
            }
        });
    </script>

</body>

</html>
