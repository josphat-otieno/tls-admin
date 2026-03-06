<?php
$title = 'Users Management';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}
$user = $_SESSION['name'];

// Get status message
$status = '';
$error_msg = '';
$new_password = '';
$new_user_email = '';

if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ':
            $status = 'success';
            // Check if there's a new password to display
            if (isset($_SESSION['new_user_password'])) {
                $new_password = $_SESSION['new_user_password'];
                $new_user_email = $_SESSION['new_user_email'];
                unset($_SESSION['new_user_password']);
                unset($_SESSION['new_user_email']);
            }
            break;
        case 'err':
            $status = 'error';
            if (isset($_GET['msg']) && $_GET['msg'] == 'user_exists') {
                $error_msg = 'User with this email already exists!';
            }
            break;
        default:
            $status = '';
    }
}

// Fetch all users
$sql = "SELECT * FROM editors ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Users</title>
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
        .user-card {
            transition: all 0.2s;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .user-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .user-type-admin {
            background: #ffc107;
            color: #000;
        }
        .user-type-editor {
            background: #E62B1E;
            color: #fff;
        }
        .user-type-user {
            background: #6c757d;
            color: #fff;
        }
        .password-display {
            background: #f8f9fa;
            border: 2px dashed #E62B1E;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #E62B1E;
            text-align: center;
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

    <!-- Password Display Modal -->
    <?php if($new_password): ?>
    <div id="password-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">
                        <i class="mdi mdi-key-variant"></i> User Credentials
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i> Please save these credentials. They will not be shown again!
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($new_user_email); ?>" id="user-email" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('user-email')">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password:</label>
                        <div class="password-display" id="user-password"><?php echo htmlspecialchars($new_password); ?></div>
                        <button class="btn btn-primary btn-sm mt-2 w-100" onclick="copyToClipboard('user-password')">
                            <i class="mdi mdi-content-copy"></i> Copy Password
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add/Edit User Modal -->
    <div id="user-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="user-form" action="edit_users.php" method="POST">
                        <input type="hidden" name="id" id="user-id">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter full name">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="user@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">User Type</label>
                            <input type="text" class="form-control" id="type" name="type" value="admin" readonly>
                            <small class="text-muted">
                                All users are created with <strong>Admin</strong> (Full access) privileges.
                            </small>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_user" id="submit-btn" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="reset-password-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reset Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reset the password for this user?</p>
                    <p class="text-muted small">A new random password will be generated.</p>
                    <form id="reset-form" action="edit_users.php" method="POST">
                        <input type="hidden" name="id" id="reset-user-id">
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="reset_password" class="btn btn-warning">Reset Password</button>
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
                                        <i class="mdi mdi-plus-circle me-1"></i> Add User
                                    </button>
                                </div>
                                <!-- <h4 class="page-title">Users Management</h4> -->
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($user_data = mysqli_fetch_assoc($result)): ?>
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card user-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <h5 class="mt-0 mb-1">
                                                        <i class="mdi mdi-account-circle text-primary me-1"></i>
                                                        <?php echo htmlspecialchars($user_data['name']); ?>
                                                    </h5>
                                                    <p class="text-muted mb-2">
                                                        <i class="mdi mdi-email"></i> <?php echo htmlspecialchars($user_data['email']); ?>
                                                    </p>
                                                    <span class="user-type-badge user-type-<?php echo $user_data['type']; ?>">
                                                        <?php echo ucfirst($user_data['type']); ?>
                                                    </span>
                                                </div>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0);" class="dropdown-item" onclick='editUser(<?php echo htmlspecialchars(json_encode($user_data), ENT_QUOTES, "UTF-8"); ?>)'>
                                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                                        </a>
                                                        <a href="javascript:void(0);" class="dropdown-item text-warning" onclick="resetPassword(<?php echo $user_data['id']; ?>)">
                                                            <i class="mdi mdi-key-variant me-1"></i>Reset Password
                                                        </a>
                                                        <a href="edit_users.php?delete_user=<?php echo $user_data['id']; ?>" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                            <i class="mdi mdi-delete me-1"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-muted small">
                                                <i class="mdi mdi-calendar"></i> Added <?php echo date('M d, Y', strtotime($user_data['created_at'])); ?>
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
                                        <h4 class="mt-3">No Users Yet</h4>
                                        <p class="text-muted">Click the "Add User" button to create your first user.</p>
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
                <?php if($new_password): ?>
                setTimeout(function() {
                    $('#success-alert-modal').modal('hide');
                    $('#password-modal').modal('show');
                }, 1000);
                <?php endif; ?>
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add User';
            document.getElementById('user-form').reset();
            document.getElementById('user-id').value = '';
            document.getElementById('submit-btn').name = 'add_user';
            document.getElementById('submit-btn').textContent = 'Add User';
            $('#user-modal').modal('show');
        }

        function editUser(user) {
            document.getElementById('modal-title').textContent = 'Edit User';
            document.getElementById('user-id').value = user.id;
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('type').value = user.type;
            document.getElementById('submit-btn').name = 'update_user';
            document.getElementById('submit-btn').textContent = 'Update User';
            $('#user-modal').modal('show');
        }

        function resetPassword(userId) {
            document.getElementById('reset-user-id').value = userId;
            $('#reset-password-modal').modal('show');
        }

        function copyToClipboard(elementId) {
            var element = document.getElementById(elementId);
            var text = element.value || element.textContent;
            
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

</body>

</html>