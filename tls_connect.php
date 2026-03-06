<?php
$title = 'TLS Connect';
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
            if (isset($_GET['msg']) && $_GET['msg'] == 'invalid_url') {
                $error_msg = 'Invalid YouTube URL! Please provide a valid YouTube video link.';
            }
            break;
        default:
            $status = '';
    }
}

// Fetch all videos
$sql = "SELECT * FROM tls_videos ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - TLS Connect</title>
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
        .video-card {
            transition: all 0.3s;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .video-thumbnail {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            background: #000;
        }
        .video-thumbnail iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-info {
            padding: 20px;
        }
        .video-title {
            font-size: 16px;
            font-weight: 600;
            color: #313a46;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .video-description {
            font-size: 14px;
            color: #6c757d;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .video-actions {
            display: flex;
            gap: 10px;
        }
        .add-video-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(114, 124, 245, 0.4);
            z-index: 999;
        }
        .add-video-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(114, 124, 245, 0.6);
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
                        <p class="mt-3 text-white">Video has been updated successfully</p>
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

    <!-- Add/Edit Video Modal -->
    <div id="video-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add YouTube Video</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="video-form" action="edit_tls_connect.php" method="POST">
                        <input type="hidden" name="id" id="video-id">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Video Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="Enter video title">
                        </div>

                        <div class="mb-3">
                            <label for="youtube_url" class="form-label">YouTube URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" required 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">Paste the full YouTube video URL</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Brief description of the video (optional)"></textarea>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_video" id="submit-btn" class="btn btn-primary">Add Video</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Video
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Videos Grid -->
                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($video = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card video-card">
                                        <div class="video-thumbnail">
                                            <iframe 
                                                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['video_id']); ?>" 
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                        <div class="video-info">
                                            <h5 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                                            <?php if(!empty($video['description'])): ?>
                                                <p class="video-description"><?php echo htmlspecialchars($video['description']); ?></p>
                                            <?php endif; ?>
                                            <div class="video-actions">
                                                <button class="btn btn-sm btn-soft-primary" onclick='editVideo(<?php echo htmlspecialchars(json_encode($video), ENT_QUOTES, "UTF-8"); ?>)'>
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                <a href="edit_tls_connect.php?delete_video=<?php echo $video['id']; ?>" 
                                                   class="btn btn-sm btn-soft-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this video?')">
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
                                        <i class="mdi mdi-youtube"></i>
                                        <h4>No Videos Yet</h4>
                                        <p class="text-muted">Click the + button to add your first YouTube video</p>
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

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add YouTube Video';
            document.getElementById('video-form').reset();
            document.getElementById('video-id').value = '';
            document.getElementById('submit-btn').name = 'add_video';
            document.getElementById('submit-btn').textContent = 'Add Video';
            $('#video-modal').modal('show');
        }

        function editVideo(video) {
            console.log('Editing video:', video); // Debug log
            document.getElementById('modal-title').textContent = 'Edit YouTube Video';
            document.getElementById('video-id').value = video.id || '';
            document.getElementById('title').value = video.title || '';
            document.getElementById('youtube_url').value = video.youtube_url || '';
            document.getElementById('description').value = video.description || '';
            document.getElementById('submit-btn').name = 'update_video';
            document.getElementById('submit-btn').textContent = 'Update Video';
            $('#video-modal').modal('show');
        }
    </script>

</body>

</html>
