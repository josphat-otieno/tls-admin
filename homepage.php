<?php
$title = 'Homepage Slides';
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
                    case 'no_image':
                        $error_msg = 'Please upload an image for the slide!';
                        break;
                    case 'invalid_image':
                        $error_msg = 'Invalid image file! Please upload JPG, PNG, GIF, or WEBP (Max 10MB)';
                        break;
                    case 'file_too_large':
                        $error_msg = 'File is too large! Maximum allowed size is 10MB.';
                        break;
                    case 'no_placeholder':
                        $error_msg = 'Please upload a placeholder image for the video!';
                        break;
                    case 'invalid_placeholder':
                        $error_msg = 'Invalid placeholder image!';
                        break;
                    case 'invalid_youtube':
                        $error_msg = 'Invalid YouTube URL! Please provide a valid YouTube video link.';
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

// Fetch all slides
$sql = "SELECT * FROM homepage_slides ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Homepage Slides</title>
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
        .slide-card {
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .slide-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .slide-preview {
            position: relative;
            height: 200px;
            background: #000;
            overflow: hidden;
        }
        .slide-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .slide-type-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .slide-type-badge.image {
            background: #E62B1E;
            color: white;
        }
        .slide-type-badge.video {
            background: #dc3545;
            color: white;
        }
        .slide-status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .slide-status-badge.active {
            background: #198754;
            color: white;
        }
        .slide-status-badge.inactive {
            background: #6c757d;
            color: white;
        }
        .slide-info {
            padding: 20px;
        }
        .slide-title {
            font-size: 16px;
            font-weight: 600;
            color: #313a46;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .slide-description {
            font-size: 14px;
            color: #6c757d;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .slide-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .image-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .image-upload-area:hover {
            border-color: #E62B1E;
            background: #f0f4ff;
        }
        .image-upload-area.dragover {
            border-color: #E62B1E;
            background: #e8f0fe;
        }
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 48px;
            color: white;
            opacity: 0.9;
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
                        <p class="mt-3 text-white">Slide has been updated successfully</p>
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

    <!-- Add/Edit Slide Modal -->
    <div id="slide-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Homepage Slide</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="slide-form" action="edit_homepage_slide.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="slide-id">
                        
                        <div class="mb-3">
                            <label class="form-label">Slide Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="slide_type" name="slide_type" required onchange="toggleSlideType()">
                                <option value="">Select Type</option>
                                <option value="image">Image Slide</option>
                                <option value="video">YouTube Video Slide</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter slide title (optional)">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Brief description (optional)"></textarea>
                        </div>

                        <!-- Image Slide Section -->
                        <div id="image-section" class="form-section">
                            <div class="mb-3">
                                <label class="form-label">Slide Image <span class="text-danger">*</span></label>
                                <div class="image-upload-area" id="image-upload-area" onclick="document.getElementById('slide-image-input').click()">
                                    <i class="mdi mdi-cloud-upload" style="font-size: 48px; color: #E62B1E;"></i>
                                    <p class="mb-0 mt-2">Click to upload or drag and drop</p>
                                    <small class="text-muted">JPG, PNG, GIF or WEBP (Max 5MB)</small>
                                </div>
                                <input type="file" id="slide-image-input" name="slide_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'image-preview')">
                                <div id="image-preview-container" style="display: none;">
                                    <img id="image-preview" class="image-preview" src="" alt="Preview">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage('slide-image')">Remove Image</button>
                                </div>
                            </div>
                        </div>

                        <!-- Video Slide Section -->
                        <div id="video-section" class="form-section">
                            <div class="mb-3">
                                <label for="youtube_url" class="form-label">YouTube URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                       placeholder="https://www.youtube.com/watch?v=...">
                                <small class="text-muted">Paste the full YouTube video URL</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Placeholder Image <span class="text-danger">*</span></label>
                                <small class="d-block text-muted mb-2">This image will be shown while the video loads</small>
                                <div class="image-upload-area" id="placeholder-upload-area" onclick="document.getElementById('placeholder-image-input').click()">
                                    <i class="mdi mdi-image" style="font-size: 48px; color: #E62B1E;"></i>
                                    <p class="mb-0 mt-2">Click to upload placeholder image</p>
                                    <small class="text-muted">JPG, PNG, GIF or WEBP (Max 5MB)</small>
                                </div>
                                <input type="file" id="placeholder-image-input" name="placeholder_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'placeholder-preview')">
                                <div id="placeholder-preview-container" style="display: none;">
                                    <img id="placeholder-preview" class="image-preview" src="" alt="Preview">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage('placeholder-image')">Remove Image</button>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_slide" id="submit-btn" class="btn btn-primary">Add Slide</button>
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
                    <h4 class="modal-title">Crop Image</h4>
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
                                <div class="page-title-right">
                                    <button class="btn btn-primary" onclick="openAddModal()">
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Slide
                                    </button>
                                </div>
                                <h4 class="page-title">Homepage Slides</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Slides Grid -->
                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($slide = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card slide-card">
                                        <div class="slide-preview">
                                            <?php if($slide['slide_type'] == 'image'): ?>
                                                <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="Slide">
                                                <span class="slide-type-badge image"><i class="mdi mdi-image"></i> Image</span>
                                            <?php else: ?>
                                                <img src="<?php echo htmlspecialchars($slide['placeholder_image']); ?>" alt="Video Placeholder">
                                                <i class="mdi mdi-play-circle play-icon"></i>
                                                <span class="slide-type-badge video"><i class="mdi mdi-youtube"></i> Video</span>
                                            <?php endif; ?>
                                            <span class="slide-status-badge <?php echo $slide['is_active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $slide['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                        <div class="slide-info">
                                            <?php if(!empty($slide['title'])): ?>
                                                <h5 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h5>
                                            <?php endif; ?>
                                            <?php if(!empty($slide['description'])): ?>
                                                <p class="slide-description"><?php echo htmlspecialchars($slide['description']); ?></p>
                                            <?php endif; ?>
                                            <div class="slide-actions">
                                                <button class="btn btn-sm btn-soft-primary" onclick='editSlide(<?php echo htmlspecialchars(json_encode($slide), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <a href="edit_homepage_slide.php?toggle_slide=<?php echo $slide['id']; ?>" 
                                                   class="btn btn-sm btn-soft-<?php echo $slide['is_active'] ? 'warning' : 'success'; ?>">
                                                    <i class="mdi mdi-<?php echo $slide['is_active'] ? 'eye-off' : 'eye'; ?>"></i> 
                                                    <?php echo $slide['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </a>
                                                <a href="edit_homepage_slide.php?delete_slide=<?php echo $slide['id']; ?>" 
                                                   class="btn btn-sm btn-soft-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this slide?')">
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
                                        <i class="mdi mdi-view-carousel"></i>
                                        <h4>No Slides Yet</h4>
                                        <p class="text-muted">Click the "Add Slide" button to create your first homepage slide</p>
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
        let currentImageType = null; // 'slide-image' or 'placeholder-image'
        let currentFile = null;

        $(document).ready(function() {
            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        function toggleSlideType() {
            const slideType = document.getElementById('slide_type').value;
            const imageSection = document.getElementById('image-section');
            const videoSection = document.getElementById('video-section');
            
            imageSection.classList.remove('active');
            videoSection.classList.remove('active');
            
            if (slideType === 'image') {
                imageSection.classList.add('active');
            } else if (slideType === 'video') {
                videoSection.classList.add('active');
            }
        }

        // Helper function to check for special characters
        function hasSpecialChars(filename) {
            return /[^a-zA-Z0-9 ._\-()]/.test(filename);
        }

        // Helper to suggest a safe filename
        function getSafeSuggestion(filename) {
            const parts = filename.split(".");
            const ext = parts.length > 1 ? "." + parts.pop() : "";
            let base = parts.join(".");
            base = base.replace(/[’'“”"–—]/g, "").replace(/[^a-zA-Z0-9 ._\-()]+/g, "_").replace(/_+/g, "_").replace(/^_+|_+$/g, "");
            return (base || "file") + (ext ? "." + ext : "");
        }

        // Helper to show modal warning
        function showFilenameWarning(filename) {
            const currentEl = document.getElementById('warn-filename-current');
            const suggestedEl = document.getElementById('warn-filename-suggested');
            if (currentEl) currentEl.textContent = filename;
            if (suggestedEl) suggestedEl.textContent = getSafeSuggestion(filename);
            
            const modalEl = document.getElementById('filename-warning-modal');
            try {
                if (window.jQuery && jQuery.fn.modal) {
                    $(modalEl).modal('show');
                } else if (window.bootstrap && bootstrap.Modal) {
                    const myModal = new bootstrap.Modal(modalEl);
                    myModal.show();
                } else {
                    alert("Filename contains special characters: " + filename + "\nSuggested safe name: " + getSafeSuggestion(filename));
                }
            } catch (err) {
                alert("Filename contains special characters: " + filename + "\nSuggested safe name: " + getSafeSuggestion(filename));
            }
        }

        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                if (hasSpecialChars(file.name)) {
                    showFilenameWarning(file.name);
                    input.value = '';
                    return;
                }
                currentFile = file;
                currentImageType = previewId === 'image-preview' ? 'slide-image' : 'placeholder-image';
                
                // Use ObjectURL instead of DataURL to handle large images more efficiently
                const objectUrl = URL.createObjectURL(file);
                document.getElementById('crop-image').src = objectUrl;
                $('#cropper-modal').modal('show');
                
                // Initialize cropper after modal is shown
                $('#cropper-modal').one('shown.bs.modal', function () {
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    const image = document.getElementById('crop-image');
                    cropper = new Cropper(image, {
                        // aspectRatio: 16 / 9, // Removed to allow independent edge movement
                        viewMode: 0,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                        zoomable: true,
                        movable: true,
                        rotatable: true,
                        scalable: true,
                        ready: function() {
                            // URL.revokeObjectURL(objectUrl);
                        }
                    });
                });
                
                // Clean up object URL when modal is hidden
                $('#cropper-modal').one('hidden.bs.modal', function() {
                    URL.revokeObjectURL(objectUrl);
                });
            }
        }

        function cropAndSave() {
            if (!cropper) return;
            
            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 1920,
                height: 1080,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            if (!canvas) {
                alert("Could not process image. The dimensions might be too large for your browser's memory.");
                return;
            }
            
            // Convert canvas to blob
            canvas.toBlob(function(blob) {
                // Create a new File object from the blob
                const fileName = currentFile.name;
                const croppedFile = new File([blob], fileName, {
                    type: currentFile.type,
                    lastModified: Date.now()
                });
                
                // Create a DataTransfer to set the file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                
                // Update the appropriate file input
                if (currentImageType === 'slide-image') {
                    document.getElementById('slide-image-input').files = dataTransfer.files;
                    showCroppedPreview(canvas.toDataURL(), 'image-preview');
                } else {
                    document.getElementById('placeholder-image-input').files = dataTransfer.files;
                    showCroppedPreview(canvas.toDataURL(), 'placeholder-preview');
                }
                
                // Close cropper modal
                $('#cropper-modal').modal('hide');
                
                // Destroy cropper instance
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }, currentFile.type);
        }

        function showCroppedPreview(dataUrl, previewId) {
            document.getElementById(previewId).src = dataUrl;
            document.getElementById(previewId + '-container').style.display = 'block';
            document.getElementById(previewId.replace('-preview', '-upload-area')).style.display = 'none';
        }

        function removeImage(type) {
            if (type === 'slide-image') {
                document.getElementById('slide-image-input').value = '';
                document.getElementById('image-preview-container').style.display = 'none';
                document.getElementById('image-upload-area').style.display = 'block';
            } else if (type === 'placeholder-image') {
                document.getElementById('placeholder-image-input').value = '';
                document.getElementById('placeholder-preview-container').style.display = 'none';
                document.getElementById('placeholder-upload-area').style.display = 'block';
            }
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Homepage Slide';
            document.getElementById('slide-form').reset();
            document.getElementById('slide-id').value = '';
            document.getElementById('submit-btn').name = 'add_slide';
            document.getElementById('submit-btn').textContent = 'Add Slide';
            
            // Reset sections
            document.getElementById('image-section').classList.remove('active');
            document.getElementById('video-section').classList.remove('active');
            removeImage('slide-image');
            removeImage('placeholder-image');
            
            $('#slide-modal').modal('show');
        }

        function editSlide(slide) {
            document.getElementById('modal-title').textContent = 'Edit Homepage Slide';
            document.getElementById('slide-id').value = slide.id;
            document.getElementById('title').value = slide.title;
            document.getElementById('description').value = slide.description || '';
            document.getElementById('slide_type').value = slide.slide_type;
            
            toggleSlideType();
            
            if (slide.slide_type === 'image' && slide.image_path) {
                document.getElementById('image-preview').src = slide.image_path;
                document.getElementById('image-preview-container').style.display = 'block';
                document.getElementById('image-upload-area').style.display = 'none';
            } else if (slide.slide_type === 'video') {
                document.getElementById('youtube_url').value = slide.youtube_url || '';
                if (slide.placeholder_image) {
                    document.getElementById('placeholder-preview').src = slide.placeholder_image;
                    document.getElementById('placeholder-preview-container').style.display = 'block';
                    document.getElementById('placeholder-upload-area').style.display = 'none';
                }
            }
            
            document.getElementById('submit-btn').name = 'update_slide';
            document.getElementById('submit-btn').textContent = 'Update Slide';
            $('#slide-modal').modal('show');
        }

        // Clean up cropper when modal is hidden
        $('#cropper-modal').on('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Block submission if filename has special chars
        document.getElementById("slide-form").addEventListener("submit", function (e) {
            const slideInput = document.getElementById("slide-image-input");
            const slideFile = slideInput?.files?.[0];
            if (slideFile && hasSpecialChars(slideFile.name)) {
                e.preventDefault();
                showFilenameWarning(slideFile.name);
                return;
            }

            const placeholderInput = document.getElementById("placeholder-image-input");
            const placeholderFile = placeholderInput?.files?.[0];
            if (placeholderFile && hasSpecialChars(placeholderFile.name)) {
                e.preventDefault();
                showFilenameWarning(placeholderFile.name);
                return;
            }
        });
    </script>

</body>

</html>
