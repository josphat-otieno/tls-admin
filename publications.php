<?php
$title = 'Publications';
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
                    case 'invalid_image':
                        $error_msg = 'Invalid image file! Please upload JPG, PNG, GIF, or WEBP.';
                        break;
                    case 'no_document':
                        $error_msg = 'Please upload a document!';
                        break;
                    case 'no_url':
                        $error_msg = 'Please provide a URL!';
                        break;
                    case 'invalid_url':
                        $error_msg = 'Invalid URL format!';
                        break;
                    case 'invalid_type':
                        $error_msg = 'Invalid file type! Please upload PDF or Word documents only.';
                        break;
                    case 'file_too_large':
                        $error_msg = 'File too large! Maximum size is 10MB.';
                        break;
                    case 'upload_failed':
                        $error_msg = 'File upload failed! Please try again.';
                        break;
                    case 'file_not_found':
                        $error_msg = 'File not found!';
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

// Fetch all publications
$sql = "SELECT * FROM publications ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Publications</title>
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
        .publication-card {
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .publication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .publication-icon {
            width: 100%;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
            position: relative;
        }
        .publication-icon i {
            font-size: 64px;
            color: white;
        }
        .publication-icon.pdf {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .publication-icon.word {
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
        }
        .publication-icon.link {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
        }
        .doc-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(255,255,255,0.9);
        }
        .doc-type-badge.pdf {
            color: #dc3545;
        }
        .doc-type-badge.word {
            color: #E62B1E;
        }
        .doc-type-badge.link {
            color: #198754;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
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
        .publication-info {
            padding: 20px;
        }
        .publication-title {
            font-size: 16px;
            font-weight: 600;
            color: #313a46;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .publication-meta {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .publication-meta i {
            margin-right: 5px;
        }
        .publication-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        .file-upload-area:hover {
            border-color: #E62B1E;
            background: #f0f4ff;
        }
        .file-info {
            margin-top: 10px;
            font-size: 14px;
            color: #6c757d;
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
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
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
        .publication-icon.has-thumbnail {
            background: none;
            padding: 0;
        }
        .publication-icon.has-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .category-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: #e3f2fd;
            color: #1976d2;
            margin-bottom: 8px;
        }
        #filename-warning-modal {
            z-index: 1060; /* Higher than standard modal (1055) */
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
                        <p class="mt-3 text-white">Publication has been updated successfully</p>
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
                    
                    <p class=" text-bold text-dark mb-0">Please rename your file and try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning text-white" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Publication Modal -->
    <div id="publication-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Publication</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="publication-form" action="edit_publication.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="publication-id">
                        <input type="hidden" name="old_thumbnail" id="old-thumbnail">
                        <input type="hidden" name="old_document_path" id="old-document-path">
                        <input type="hidden" name="old_document_type" id="old-document-type">
                        <input type="hidden" name="old_file_size" id="old-file-size">
                        <input type="hidden" name="old_original_filename" id="old-original-filename">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="Enter publication title">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the publication"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category <small class="text-muted">(Optional)</small></label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="TLS in the news">TLS in the news</option>
                                <option value="Peer reviewed articles">Peer reviewed articles</option>
                                <option value="Reports">Reports</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Publication Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="publication_type" name="publication_type" required onchange="togglePublicationType()">
                                <option value="">Select Type</option>
                                <option value="document">Upload Document (PDF/Word)</option>
                                <option value="link">External Link/URL</option>
                            </select>
                        </div>

                        <!-- Document Section -->
                        <div id="document-section" class="form-section">
                            <div class="mb-3">
                                <label class="form-label">Document <span class="text-danger">*</span></label>
                                <div class="file-upload-area" id="file-upload-area" onclick="document.getElementById('document-input').click()">
                                    <i class="mdi mdi-file-document" style="font-size: 48px; color: #E62B1E;"></i>
                                    <p class="mb-0 mt-2">Click to upload document</p>
                                    <small class="text-muted">PDF, DOC, or DOCX (Max 10MB)</small>
                                </div>
                                <input type="file" id="document-input" name="document" accept=".pdf,.doc,.docx" style="display: none;" onchange="showFileName(this)">
                                <div id="file-info" class="file-info"></div>
                            </div>
                        </div>

                        <!-- Link Section -->
                        <div id="link-section" class="form-section">
                            <div class="mb-3">
                                <label for="external_url" class="form-label">External URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="external_url" name="external_url" 
                                       placeholder="https://example.com/document.pdf">
                                <small class="text-muted">Enter the full URL to the document or resource</small>
                            </div>
                        </div>

                        <!-- Thumbnail Section -->
                        <div class="mb-3">
                            <label class="form-label">Thumbnail Image <small class="text-muted">(Optional)</small></label>
                            <div class="image-upload-area" id="thumbnail-upload-area">
                                <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
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

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_publication" id="submit-btn" class="btn btn-primary">Add Publication</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Publication
                                    </button>
                                </div>
                                <h4 class="page-title">Publications</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Publications Grid -->
                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($pub = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card publication-card" onclick="window.open('edit_publication.php?download=<?php echo $pub['id']; ?>', '_blank')">
                                        <div class="publication-icon <?php echo !empty($pub['thumbnail']) ? 'has-thumbnail' : ($pub['publication_type'] == 'link' ? 'link' : $pub['document_type']); ?>">
                                            <?php if(!empty($pub['thumbnail'])): ?>
                                                <img src="<?php echo htmlspecialchars($pub['thumbnail']); ?>" alt="<?php echo htmlspecialchars($pub['title']); ?>">
                                            <?php elseif($pub['publication_type'] == 'link'): ?>
                                                <i class="mdi mdi-link-variant"></i>
                                            <?php else: ?>
                                                <i class="mdi mdi-<?php echo $pub['document_type'] == 'pdf' ? 'file-pdf-box' : 'file-word-box'; ?>"></i>
                                            <?php endif; ?>
                                            <span class="doc-type-badge <?php echo $pub['publication_type'] == 'link' ? 'link' : $pub['document_type']; ?>">
                                                <?php echo $pub['publication_type'] == 'link' ? 'LINK' : strtoupper($pub['document_type']); ?>
                                            </span>
                                            <span class="status-badge <?php echo $pub['is_active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $pub['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                        <div class="publication-info" onclick="event.stopPropagation()">
                                            <?php if(!empty($pub['category'])): ?>
                                                <span class="category-badge"><?php echo htmlspecialchars($pub['category']); ?></span>
                                            <?php endif; ?>
                                            <h5 class="publication-title"><?php echo htmlspecialchars($pub['title']); ?></h5>
                                            <?php if(!empty($pub['description'])): ?>
                                                <p class="text-muted mb-2" style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo htmlspecialchars($pub['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <div class="publication-meta">
                                                <?php if($pub['publication_type'] == 'document'): ?>
                                                    <div><i class="mdi mdi-file-outline"></i><?php echo formatFileSize($pub['file_size']); ?></div>
                                                <?php else: ?>
                                                    <div><i class="mdi mdi-link"></i>External Link</div>
                                                <?php endif; ?>
                                                <div><i class="mdi mdi-eye"></i><?php echo $pub['downloads']; ?> views</div>
                                                <div><i class="mdi mdi-calendar"></i><?php echo date('M d, Y', strtotime($pub['created_at'])); ?></div>
                                            </div>
                                            <div class="publication-actions">
                                                <button class="btn btn-sm btn-soft-success" onclick="window.open('edit_publication.php?download=<?php echo $pub['id']; ?>', '_blank')">
                                                    <i class="mdi mdi-<?php echo $pub['publication_type'] == 'link' ? 'open-in-new' : 'download'; ?>"></i> 
                                                    <?php echo $pub['publication_type'] == 'link' ? 'Open' : 'Download'; ?>
                                                </button>
                                                <button class="btn btn-sm btn-soft-primary" onclick='editPublication(<?php echo htmlspecialchars(json_encode($pub, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)'>
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <a href="edit_publication.php?toggle_publication=<?php echo $pub['id']; ?>" 
                                                   class="btn btn-sm btn-soft-<?php echo $pub['is_active'] ? 'warning' : 'success'; ?>">
                                                    <i class="mdi mdi-<?php echo $pub['is_active'] ? 'eye-off' : 'eye'; ?>"></i>
                                                </a>
                                                <a href="edit_publication.php?delete_publication=<?php echo $pub['id']; ?>" 
                                                   class="btn btn-sm btn-soft-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this publication?')">
                                                    <i class="mdi mdi-delete"></i>
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
                                        <i class="mdi mdi-file-document-multiple"></i>
                                        <h4>No Publications Yet</h4>
                                        <p class="text-muted">Click the "Add Publication" button to add your first publication</p>
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

        // Helper function to escape HTML entities
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

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
            handleThumbnailFile(this.files[0]);
        });

        removeThumbnailBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            thumbnailInput.value = '';
            thumbnailPreview.style.display = 'none';
            removeThumbnailBtn.style.display = 'none';
        });



        function handleThumbnailFile(file) {
            if (!file) return;

            if (hasSpecialChars(file.name)) {
                showFilenameWarning(file.name);
                thumbnailInput.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPG, PNG, GIF, or WEBP)');
                thumbnailInput.value = '';
                return;
            }

            // Use the common cropper with free aspect ratio for book covers/thumbnails
            initCropper(file, 'thumbnail-input', 'thumbnail-preview-image');
        }

        function togglePublicationType() {
            const publicationType = document.getElementById('publication_type').value;
            const documentSection = document.getElementById('document-section');
            const linkSection = document.getElementById('link-section');
            
            documentSection.classList.remove('active');
            linkSection.classList.remove('active');
            
            if (publicationType === 'document') {
                documentSection.classList.add('active');
                document.getElementById('document-input').required = true;
                document.getElementById('external_url').required = false;
            } else if (publicationType === 'link') {
                linkSection.classList.add('active');
                document.getElementById('document-input').required = false;
                document.getElementById('external_url').required = true;
            }
        }

        function showFileName(input) {
            const file = input.files[0];
            if (file) {
                if (hasSpecialChars(file.name)) {
                    showFilenameWarning(file.name);
                    input.value = '';
                    document.getElementById('file-info').innerHTML = '';
                    return;
                }

                const sizeKB = (file.size / 1024).toFixed(2);
                const sizeMB = (file.size / 1048576).toFixed(2);
                const sizeText = file.size > 1048576 ? sizeMB + ' MB' : sizeKB + ' KB';
                
                document.getElementById('file-info').innerHTML = 
                    '<i class="mdi mdi-file-check text-success"></i> ' + 
                    '<strong>' + file.name + '</strong> (' + sizeText + ')';
            }
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Publication';
            document.getElementById('publication-form').reset();
            document.getElementById('publication-id').value = '';
            document.getElementById('old-thumbnail').value = '';
            document.getElementById('submit-btn').name = 'add_publication';
            document.getElementById('submit-btn').textContent = 'Add Publication';
            document.getElementById('file-info').innerHTML = '';
            
            // Reset sections
            document.getElementById('document-section').classList.remove('active');
            document.getElementById('link-section').classList.remove('active');
            
            // Hide thumbnail preview
            thumbnailPreview.style.display = 'none';
            removeThumbnailBtn.style.display = 'none';
            
            $('#publication-modal').modal('show');
        }

        function editPublication(pub) {
            document.getElementById('modal-title').textContent = 'Edit Publication';
            document.getElementById('publication-id').value = pub.id;
            document.getElementById('old-thumbnail').value = pub.thumbnail || '';
            document.getElementById('title').value = pub.title;
            document.getElementById('description').value = pub.description || '';
            document.getElementById('category').value = pub.category || '';
            document.getElementById('publication_type').value = pub.publication_type;
            document.getElementById('submit-btn').name = 'update_publication';
            document.getElementById('submit-btn').textContent = 'Update Publication';
            
            // Populate old document data for updates
            document.getElementById('old-document-path').value = pub.document_path || '';
            document.getElementById('old-document-type').value = pub.document_type || '';
            document.getElementById('old-file-size').value = pub.file_size || '';
            document.getElementById('old-original-filename').value = pub.original_filename || '';
            
            togglePublicationType();
            
            if (pub.publication_type === 'document') {
                document.getElementById('document-input').required = false;
                document.getElementById('file-info').innerHTML = 
                    '<i class="mdi mdi-file text-info"></i> ' +
                    '<strong>Current:</strong> ' + escapeHtml(pub.original_filename) + 
                    '<br><small class="text-muted">Leave empty to keep current document</small>';
            } else if (pub.publication_type === 'link') {
                document.getElementById('external_url').value = pub.external_url || '';
            }
            
            // Show thumbnail preview if exists
            if (pub.thumbnail) {
                thumbnailPreviewImage.src = pub.thumbnail;
                thumbnailPreview.style.display = 'block';
                removeThumbnailBtn.style.display = 'inline-block';
            } else {
                thumbnailPreview.style.display = 'none';
                removeThumbnailBtn.style.display = 'none';
            }
            
            $('#publication-modal').modal('show');
        }

        // Block submission if filename has special chars (backup safety check)
        document.getElementById("publication-form").addEventListener("submit", function (e) {
            // Check Document
            const docInput = document.getElementById("document-input");
            const docFile = docInput?.files?.[0];
            if (docFile && hasSpecialChars(docFile.name)) {
                e.preventDefault();
                showFilenameWarning(docFile.name);
                return;
            }

            // Check Thumbnail
            const thumbInputInternal = document.getElementById("thumbnail-input");
            const thumbFile = thumbInputInternal?.files?.[0];
            if (thumbFile && hasSpecialChars(thumbFile.name)) {
                e.preventDefault();
                showFilenameWarning(thumbFile.name);
                return;
            }
        });
    </script>


</body>

</html>
