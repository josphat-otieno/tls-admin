<?php
$title = 'Background Images';
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
            if (!empty($_GET['msg'])) {
                switch($_GET['msg']) {
                    case 'invalid_image':
                        $error_msg = 'Invalid image file! Please upload JPG, PNG, GIF, or WEBP (Max 10MB)';
                        break;
                    case 'file_too_large':
                        $error_msg = 'File is too large! Maximum allowed size is 10MB.';
                        break;
                    case 'upload_failed':
                        $error_msg = 'Failed to upload image. Please check your server permissions.';
                        break;
                    default:
                        $error_msg = htmlspecialchars($_GET['msg']);
                }
            } else {
                $error_msg = 'Something went wrong. Please try again.';
            }
            break;
        default:
            $status = '';
    }
}

// Fetch all background images
$sql = "SELECT * FROM page_backgrounds ORDER BY page_name ASC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Background Images</title>
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
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

    <style>
        .page-card {
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            background: #fff;
        }
        .page-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .page-preview {
            position: relative;
            height: 180px;
            background: #f8f9fa;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .no-image-placeholder {
            text-align: center;
            color: #adb5bd;
        }
        .no-image-placeholder i {
            font-size: 48px;
        }
        .page-info {
            padding: 20px;
        }
        .page-title {
            font-size: 18px;
            font-weight: 600;
            color: #313a46;
            margin-bottom: 15px;
        }
        .image-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .image-upload-area:hover {
            border-color: #E62B1E;
            background: #f0f4ff;
        }
        .image-preview-modal {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            margin-top: 10px;
        }
        /* Cropper Modal Styles */
        .cropper-container {
            max-height: 400px;
            margin: 20px 0;
        }
        #crop-image {
            max-width: 100%;
        }
        .crop-controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        #filename-warning-modal {
            z-index: 1070; /* Higher than cropper modal */
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
                        <p class="mt-3 text-white">Background image has been updated successfully</p>
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

    <!-- Edit Background Modal -->
    <div id="edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Update Background Image</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bg-form" action="edit_background_images.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="bg-id">
                        <input type="hidden" name="page_name" id="bg-page-name">
                        
                        <div class="mb-3 text-center">
                            <h5 id="display-page-name" class="text-primary"></h5>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image <span class="text-danger">*</span></label>
                            <div class="image-upload-area" id="image-upload-area" onclick="document.getElementById('bg-image-input').click()">
                                <i class="mdi mdi-cloud-upload" style="font-size: 48px; color: #667eea;"></i>
                                <p class="mb-0 mt-2">Click to upload or drag and drop</p>
                                <small class="text-muted">JPG, PNG, GIF or WEBP (Max 5MB)</small>
                            </div>
                            <input type="file" id="bg-image-input" name="background_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <div id="image-preview-container" style="display: none;" class="text-center">
                                <img id="image-preview" class="image-preview-modal" src="" alt="Preview">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">Remove Image</button>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_background" id="submit-btn" class="btn btn-primary">Update Background</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Cropper Modal -->
    <div id="cropper-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Crop Background Image</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img id="crop-image" src="" alt="Image to crop">
                    </div>
                    <div class="crop-controls">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.rotate(-90)">
                            <i class="mdi mdi-rotate-left"></i> Rotate Left
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.rotate(90)">
                            <i class="mdi mdi-rotate-right"></i> Rotate Right
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.scaleX(-cropper.getData().scaleX || -1)">
                            <i class="mdi mdi-flip-horizontal"></i> Flip H
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.scaleY(-cropper.getData().scaleY || -1)">
                            <i class="mdi mdi-flip-vertical"></i> Flip V
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.zoom(0.1)">
                            <i class="mdi mdi-magnify-plus"></i> Zoom In
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.zoom(-0.1)">
                            <i class="mdi mdi-magnify-minus"></i> Zoom Out
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="cropper.reset()">
                            <i class="mdi mdi-refresh"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="cropAndSave()">
                        <i class="mdi mdi-crop"></i> Crop & Use Image
                    </button>
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
                                <h4 class="page-title">Background Images Management</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php if($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($bg = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card page-card">
                                        <div class="page-preview">
                                            <?php if(!empty($bg['image_path'])): ?>
                                                <img src="<?php echo htmlspecialchars($bg['image_path']); ?>" alt="<?php echo htmlspecialchars($bg['page_name']); ?>">
                                            <?php else: ?>
                                                <div class="no-image-placeholder">
                                                    <i class="mdi mdi-image-off-outline"></i>
                                                    <p>No background image set</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="page-info">
                                            <h5 class="page-title"><?php echo htmlspecialchars($bg['page_name']); ?></h5>
                                            <div class="d-grid">
                                                <button class="btn btn-primary btn-sm" onclick='openEditModal(<?php echo htmlspecialchars(json_encode($bg), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="mdi mdi-pencil me-1"></i> Update Image
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center">
                                <div class="alert alert-info">
                                    No pages found. Please ensure the database is initialized.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <?php include 'footer.php' ?>
        </div>
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

    <!-- Cropper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <script>
        let cropper = null;
        let currentFile = null;

        $(document).ready(function() {
            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        function hasSpecialChars(filename) {
            return /[^a-zA-Z0-9 ._\-()]/.test(filename);
        }

        function getSafeSuggestion(filename) {
            const parts = filename.split(".");
            const ext = parts.length > 1 ? "." + parts.pop() : "";
            let base = parts.join(".");
            base = base.replace(/[’'“”"–—]/g, "").replace(/[^a-zA-Z0-9 ._\-()]+/g, "_").replace(/_+/g, "_").replace(/^_+|_+$/g, "");
            return (base || "file") + (ext ? "." + ext : "");
        }

        function showFilenameWarning(filename) {
            document.getElementById('warn-filename-current').textContent = filename;
            document.getElementById('warn-filename-suggested').textContent = getSafeSuggestion(filename);
            $('#filename-warning-modal').modal('show');
        }

        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                if (hasSpecialChars(file.name)) {
                    showFilenameWarning(file.name);
                    input.value = '';
                    return;
                }
                currentFile = file;
                
                // Use ObjectURL instead of DataURL to handle large images more efficiently
                const objectUrl = URL.createObjectURL(file);
                document.getElementById('crop-image').src = objectUrl;
                $('#cropper-modal').modal('show');
                
                $('#cropper-modal').one('shown.bs.modal', function () {
                    if (cropper) cropper.destroy();
                    const image = document.getElementById('crop-image');
                    cropper = new Cropper(image, {
                        // aspectRatio: 16 / 7, // Removed to allow independent edge movement
                        viewMode: 0,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        zoomable: true,
                        movable: true,
                        rotatable: true,
                        scalable: true,
                        ready: function() {
                            // Revoke object URL after cropper is ready to free memory
                            // URL.revokeObjectURL(objectUrl);
                        }
                    });
                });
                
                // Also clean up object URL when modal is hidden
                $('#cropper-modal').one('hidden.bs.modal', function() {
                    URL.revokeObjectURL(objectUrl);
                });
            }
        }

        function cropAndSave() {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 1920,
                height: 840,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            if (!canvas) {
                alert("Could not process image. The dimensions might be too large for your browser's memory.");
                return;
            }
            
            canvas.toBlob(function(blob) {
                const croppedFile = new File([blob], currentFile.name, {
                    type: currentFile.type,
                    lastModified: Date.now()
                });
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                document.getElementById('bg-image-input').files = dataTransfer.files;
                
                document.getElementById('image-preview').src = canvas.toDataURL();
                document.getElementById('image-preview-container').style.display = 'block';
                document.getElementById('image-upload-area').style.display = 'none';
                
                $('#cropper-modal').modal('hide');
            }, currentFile.type);
        }

        function removeImage() {
            document.getElementById('bg-image-input').value = '';
            document.getElementById('image-preview-container').style.display = 'none';
            document.getElementById('image-upload-area').style.display = 'block';
        }

        function openEditModal(bg) {
            document.getElementById('bg-id').value = bg.id;
            document.getElementById('bg-page-name').value = bg.page_name;
            document.getElementById('display-page-name').textContent = bg.page_name;
            
            removeImage();
            if (bg.image_path) {
                document.getElementById('image-preview').src = bg.image_path;
                document.getElementById('image-preview-container').style.display = 'block';
                document.getElementById('image-upload-area').style.display = 'none';
            }
            
            $('#edit-modal').modal('show');
        }

        $('#cropper-modal').on('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        document.getElementById("bg-form").addEventListener("submit", function (e) {
            const input = document.getElementById("bg-image-input");
            const file = input.files[0];
            const hasExisting = document.getElementById('image-preview').src && !document.getElementById('image-preview').src.startsWith('data:');
            
            if (!file && !hasExisting) {
                e.preventDefault();
                alert("Please select an image");
                return;
            }
        });
    </script>
</body>
</html>
