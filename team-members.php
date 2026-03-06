<?php
$title = 'Team Members';
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

// Fetch all team members
$sql = "SELECT * FROM members WHERE member_type = 'team' ORDER BY id ASC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Team Members</title>
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
        .member-card {
            transition: transform 0.2s;
        }
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .social-links a {
            margin-right: 10px;
            font-size: 20px;
        }
        .profile-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f0f0f0;
        }
        .profile-image-placeholder {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            font-weight: bold;
        }
         .profile-info-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5;
            max-height: 3em; /* 2 lines * 1.5 line-height */
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
                        <p class="mt-3 text-white">Team member has been updated successfully</p>
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

    <!-- Add/Edit Member Modal -->
    <div id="member-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Team Member</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="member-form" action="edit_member.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="member_type" value="team">
                        <input type="hidden" name="id" id="member-id">
                        <input type="hidden" name="old_profile_image" id="old-profile-image">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="role" name="role" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
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

                        <div class="mb-3">
                            <label for="profile_info" class="form-label">Profile Information</label>
                            <textarea class="form-control" id="profile_info" name="profile_info" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="linkedin_link" class="form-label">LinkedIn Profile</label>
                                    <input type="url" class="form-control" id="linkedin_link" name="linkedin_link" placeholder="https://linkedin.com/in/username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="x_link" class="form-label">X (Twitter) Profile</label>
                                    <input type="url" class="form-control" id="x_link" name="x_link" placeholder="https://x.com/username">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_member" id="submit-btn" class="btn btn-primary">Add Member</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Team Member
                                    </button>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($member = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card member-card">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <?php if(!empty($member['profile_image']) && file_exists($member['profile_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($member['profile_image']); ?>" class="profile-image" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                                <?php else: ?>
                                                    <div class="profile-image-placeholder mx-auto">
                                                        <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h4 class="mt-0 mb-1"><?php echo htmlspecialchars($member['name']); ?></h4>
                                            <p class="text-muted mb-2"><?php echo htmlspecialchars($member['role']); ?></p>
                                            
                                            <p class="mb-3 profile-info-truncate"><?php echo nl2br(htmlspecialchars($member['profile_info'])); ?></p>

                                            <div class="social-links mb-3">
                                                <?php if(!empty($member['linkedin_link'])): ?>
                                                    <a href="<?php echo htmlspecialchars($member['linkedin_link']); ?>" target="_blank" class="text-primary" title="LinkedIn">
                                                        <i class="mdi mdi-linkedin"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(!empty($member['x_link'])): ?>
                                                    <a href="<?php echo htmlspecialchars($member['x_link']); ?>" target="_blank" class="text-dark" title="X (Twitter)">
                                                        <i class="mdi mdi-twitter"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary" onclick='editMember(<?php echo htmlspecialchars(json_encode($member), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <a href="edit_member.php?delete_member=<?php echo $member['id']; ?>&type=team" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this member?')">
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
                                    <div class="card-body text-center py-5">
                                        <i class="mdi mdi-account-group h1 text-muted"></i>
                                        <h4 class="mt-3">No Team Members Yet</h4>
                                        <p class="text-muted">Click the "Add Team Member" button to get started.</p>
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

            // Use the common cropper with 1:1 aspect ratio for profile photos
            initCropper(file, 'image-input', 'preview-image', 1);
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Team Member';
            document.getElementById('member-form').reset();
            document.getElementById('member-id').value = '';
            document.getElementById('old-profile-image').value = '';
            document.getElementById('submit-btn').name = 'add_member';
            document.getElementById('submit-btn').textContent = 'Add Member';
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
            $('#member-modal').modal('show');
        }

        function editMember(member) {
            document.getElementById('modal-title').textContent = 'Edit Team Member';
            document.getElementById('member-id').value = member.id;
            document.getElementById('name').value = member.name;
            document.getElementById('role').value = member.role;
            document.getElementById('profile_info').value = member.profile_info || '';
            document.getElementById('linkedin_link').value = member.linkedin_link || '';
            document.getElementById('x_link').value = member.x_link || '';
            document.getElementById('old-profile-image').value = member.profile_image || '';
            document.getElementById('submit-btn').name = 'update_member';
            document.getElementById('submit-btn').textContent = 'Update Member';
            
            if (member.profile_image) {
                previewImage.src = member.profile_image;
                imagePreview.style.display = 'block';
                removeImageBtn.style.display = 'inline-block';
            } else {
                imagePreview.style.display = 'none';
                removeImageBtn.style.display = 'none';
            }
            
            $('#member-modal').modal('show');
        }

        // Block submission if filename has special chars
        document.getElementById("member-form").addEventListener("submit", function (e) {
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