<?php
$title = 'What We Do';
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

// Fetch content from database
$page_name = 'what_we_do';
$sql = "SELECT * FROM static_content WHERE page_name = '$page_name'";
$result = mysqli_query($con, $sql);
$content_data = mysqli_fetch_assoc($result);
$content = $content_data ? $content_data['content'] : '<p>No content available. Please add content.</p>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - What We Do</title>
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
        .content-display {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 300px;
        }
        .edit-form {
            display: none;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .action-buttons {
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
                        <p class="mt-3 text-white">Content has been updated successfully</p>
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
                    <!-- <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">What We Do</h4>
                            </div>
                        </div>
                    </div> -->
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button id="edit-btn" class="btn btn-primary" onclick="toggleEdit()">
                                    <i class="mdi mdi-pencil"></i> Edit Content
                                </button>
                                <button id="cancel-btn" class="btn btn-secondary" onclick="toggleEdit()" style="display: none;">
                                    <i class="mdi mdi-close"></i> Cancel
                                </button>
                            </div>

                            <!-- Content Display -->
                            <div id="content-display" class="content-display">
                               <?php echo nl2br(htmlspecialchars($content)); ?>
                            </div>

                            <!-- Edit Form -->
                            <div id="edit-form" class="edit-form">
                                <form action="edit_static_content.php" method="POST">
                                    <input type="hidden" name="page_name" value="what_we_do">
                                    <input type="hidden" name="redirect_page" value="what-we-do.php">
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea id="content" name="content" class="form-control" rows="10"><?php echo htmlspecialchars($content); ?></textarea>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" name="update_content" class="btn btn-success">
                                            <i class="mdi mdi-content-save"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
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

    <!-- App js-->
    <script src="assets/js/app.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show status modals
            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        function toggleEdit() {
            var displayDiv = document.getElementById('content-display');
            var editDiv = document.getElementById('edit-form');
            var editBtn = document.getElementById('edit-btn');
            var cancelBtn = document.getElementById('cancel-btn');

            if (displayDiv.style.display === 'none') {
                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';
                editBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'none';
            } else {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
                editBtn.style.display = 'none';
                cancelBtn.style.display = 'inline-block';
            }
        }
    </script>

</body>

</html>