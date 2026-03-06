<?php
$title = 'Blog Posts';
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

// Fetch all blogs
$sql = "SELECT * FROM blogs ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Blog</title>
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
        .blog-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            height: 100%;
            overflow: hidden;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .blog-card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .blog-card-video {
            height: 200px;
            width: 100%;
            background: #000;
        }

        .blog-card-body {
            padding: 20px;
        }

        .blog-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-preview {
            color: #6c757d;
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }



        .media-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }

        .blog-modal-img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .blog-modal-video {
            width: 100%;
            height: 400px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .blog-content-full {
            line-height: 1.8;
            color: #495057;
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
                        <p class="mt-3 text-white">Blog has been updated successfully</p>
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

    <!-- View Blog Modal -->
    <div id="view-blog-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="view-blog-title"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="view-blog-media"></div>
                    <div id="view-blog-content" class="blog-content-full"></div>
                    <hr>
                    <div class="d-flex justify-content-end align-items-center">
                        <div>
                            <button class="btn btn-primary" onclick="openEditModalFromView()">
                                <i class="mdi mdi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-danger" onclick="deleteBlogFromView()">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Blog Modal -->
    <div id="blog-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Blog Post</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="blog-form" action="edit_blog.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="blog-id">

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="media_type" class="form-label">Media Type</label>
                                    <select class="form-select" id="media_type" name="media_type"
                                        onchange="toggleMediaInput()">
                                        <option value="">None</option>
                                        <option value="image">Image</option>
                                        <option value="video">YouTube Video</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Image Upload Field (hidden by default) -->
                        <div class="mb-3" id="image-upload-field" style="display: none;">
                            <label class="form-label">Upload Image</label>
                            <div class="image-upload-area" id="image-upload-area">
                                <input type="file" id="blog_image" name="blog_image"
                                    accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
                                <i class="mdi mdi-image text-muted" style="font-size: 48px;"></i>
                                <h6 class="mt-2">Click to upload image</h6>
                                <p class="text-muted mb-0 small">JPG, PNG, GIF or WEBP (Max 2MB)</p>
                                <div class="image-preview" id="image-preview">
                                    <img src="" alt="Image preview" id="preview-image">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-image-btn" id="remove-image-btn">
                                    <i class="mdi mdi-delete"></i> Remove Image
                                </button>
                            </div>
                            <input type="hidden" name="old_media_url" id="old-media-url">
                        </div>
                        <!-- YouTube URL Field (hidden by default) -->
                        <div class="mb-3" id="youtube-url-field" style="display: none;">
                            <label for="youtube_url" class="form-label">YouTube URL</label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                                placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">Paste the full YouTube video URL</small>
                        </div>



                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_blog" id="submit-btn" class="btn btn-primary">Add
                                Blog</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Blog Post
                                    </button>
                                </div>
                                <!-- <h4 class="page-title">Blog Posts</h4> -->
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($blog = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card blog-card" onclick='viewBlog(<?php echo htmlspecialchars(json_encode($blog), ENT_QUOTES, "UTF-8"); ?>)'>
                                        <?php if ($blog['media_type'] == 'image' && !empty($blog['media_url'])): ?>
                                            <div style="position: relative;">
                                                <img src="<?php echo htmlspecialchars($blog['media_url']); ?>" class="blog-card-img"
                                                    alt="Blog image">
                                                <span class="media-badge"><i class="mdi mdi-image"></i> Image</span>
                                            </div>
                                        <?php elseif ($blog['media_type'] == 'video' && !empty($blog['media_url'])): ?>
                                            <div style="position: relative;">
                                                <iframe src="<?php echo htmlspecialchars($blog['media_url']); ?>"
                                                    class="blog-card-video" frameborder="0" allowfullscreen></iframe>
                                                <span class="media-badge"><i class="mdi mdi-video"></i> Video</span>
                                            </div>
                                        <?php else: ?>
                                                style="position: relative; background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%); height: 200px; display: flex; align-items: center; justify-content: center;">
                                                <i class="mdi mdi-post text-white" style="font-size: 48px;"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="blog-card-body">
                                            <h5 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                            <p class="blog-preview"><?php echo htmlspecialchars($blog['content']); ?></p>

                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="mdi mdi-post h1 text-muted"></i>
                                        <h4 class="mt-3">No Blog Posts Yet</h4>
                                        <p class="text-muted">Click the "Add Blog Post" button to create your first post.
                                        </p>
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
        let currentBlog = null;

        $(document).ready(function () {
            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        // Image upload handling
        const imageInput = document.getElementById('blog_image');
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
            handleImageFile(this.files[0]);
        });

        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
        });



        function handleImageFile(file) {
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

            // Use the common cropper
            initCropper(file, 'blog_image', 'preview-image');
        }

        function toggleMediaInput() {
            const mediaType = document.getElementById('media_type').value;
            const imageField = document.getElementById('image-upload-field');
            const youtubeField = document.getElementById('youtube-url-field');

            // Hide both fields first
            imageField.style.display = 'none';
            youtubeField.style.display = 'none';

            // Show the appropriate field
            if (mediaType === 'image') {
                imageField.style.display = 'block';
            } else if (mediaType === 'video') {
                youtubeField.style.display = 'block';
            }
        }

        function viewBlog(blog) {
            currentBlog = blog;
            document.getElementById('view-blog-title').textContent = blog.title;
            document.getElementById('view-blog-content').textContent = blog.content;


            // Display media
            let mediaHtml = '';
            if (blog.media_type === 'image' && blog.media_url) {
                mediaHtml = `<img src="${blog.media_url}" class="blog-modal-img" alt="Blog image">`;
            } else if (blog.media_type === 'video' && blog.media_url) {
                mediaHtml = `<iframe src="${blog.media_url}" class="blog-modal-video" frameborder="0" allowfullscreen></iframe>`;
            }
            document.getElementById('view-blog-media').innerHTML = mediaHtml;

            $('#view-blog-modal').modal('show');
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Blog Post';
            document.getElementById('blog-form').reset();
            document.getElementById('blog-id').value = '';
            document.getElementById('submit-btn').name = 'add_blog';
            document.getElementById('submit-btn').textContent = 'Add Blog';
            
            // Hide image preview
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
            
            $('#blog-modal').modal('show');
        }

        function openEditModalFromView() {
            if (!currentBlog) return;

            $('#view-blog-modal').modal('hide');

            setTimeout(() => {
                document.getElementById('modal-title').textContent = 'Edit Blog Post';
                document.getElementById('blog-id').value = currentBlog.id;
                document.getElementById('title').value = currentBlog.title;
                document.getElementById('content').value = currentBlog.content;
                document.getElementById('media_type').value = currentBlog.media_type || '';
                document.getElementById('old-media-url').value = currentBlog.media_url || '';

                // Handle media fields
                if (currentBlog.media_type === 'video') {
                    document.getElementById('youtube_url').value = currentBlog.media_url || '';
                } else if (currentBlog.media_type === 'image' && currentBlog.media_url) {
                    // Show existing image preview
                    previewImage.src = currentBlog.media_url;
                    imagePreview.style.display = 'block';
                    removeImageBtn.style.display = 'inline-block';
                }

                toggleMediaInput(); // Show appropriate field


                document.getElementById('submit-btn').name = 'update_blog';
                document.getElementById('submit-btn').textContent = 'Update Blog';
                $('#blog-modal').modal('show');
            }, 300);
        }

        function deleteBlogFromView() {
            if (!currentBlog) return;

            if (confirm('Are you sure you want to delete this blog post?')) {
                window.location.href = `edit_blog.php?delete_blog=${currentBlog.id}`;
            }
        }

        // Block submission if filename has special chars
        document.getElementById("blog-form").addEventListener("submit", function (e) {
            const input = document.getElementById("blog_image");
            const file = input?.files?.[0];
            if (file && hasSpecialChars(file.name)) {
                e.preventDefault();
                showFilenameWarning(file.name);
            }
        });
    </script>

</body>

</html>