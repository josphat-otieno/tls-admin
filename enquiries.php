<?php
$title = 'Enquiry Messages';
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

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM enquiries WHERE id = $delete_id";
    if (mysqli_query($con, $sql)) {
        header("Location: enquiries.php?status=succ");
    } else {
        header("Location: enquiries.php?status=err");
    }
    exit;
}

// Fetch all enquiries
$sql = "SELECT * FROM enquiries ORDER BY created_at DESC";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Enquiry Messages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Enquiry Messages from TLS Website" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    
    <!-- DataTables css -->
    <link href="assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />

    <style>
        .message-truncate {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .unread-row {
            font-weight: 600;
            background-color: rgba(230, 43, 30, 0.03) !important;
        }
        .status-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            font-weight: 700;
        }
        .badge-new {
            background-color: #E62B1E;
            color: white;
        }
        .badge-read {
            background-color: #e2e8f0;
            color: #475569;
        }
        .modal-label {
            font-weight: 600;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .modal-value {
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 20px;
            word-break: break-word;
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
                        <p class="mt-3 text-white">Something went wrong. Please try again</p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Enquiry Modal -->
    <div id="view-enquiry-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Enquiry Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="modal-label">Date Received</div>
                            <div class="modal-value" id="view-date"></div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="modal-label">Status</div>
                            <div id="view-status-badge"></div>
                        </div>
                    </div>
                    
                    <div class="modal-label">Email Address</div>
                    <div class="modal-value"><a href="" id="view-email-link"><span id="view-email"></span></a></div>
                    
                    <div class="modal-label">Phone Number</div>
                    <div class="modal-value" id="view-phone"></div>
                    
                    <div class="modal-label">Message</div>
                    <div class="modal-value p-3 bg-light rounded" id="view-message" style="white-space: pre-wrap;"></div>
                    
                    <div class="text-end mt-4">
                        <a href="" id="toggle-status-btn" class="btn btn-outline-primary me-2">Mark as Read</a>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Enquiry Messages</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="enquiries-table" class="table table-hover dt-responsive nowrap w-100">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Sender Info</th>
                                                    <th>Message</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                        <tr class="<?php echo $row['status'] == 'new' ? 'unread-row' : ''; ?>">
                                                            <td>
                                                                <div class="text-dark"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                                                <small class="text-muted"><?php echo date('H:i', strtotime($row['created_at'])); ?></small>
                                                            </td>
                                                            <td>
                                                                <div class="text-dark fw-bold"><?php echo htmlspecialchars($row['email']); ?></div>
                                                                <div class="text-muted small"><?php echo htmlspecialchars($row['phone_number']); ?></div>
                                                            </td>
                                                            <td>
                                                                <div class="message-truncate text-muted">
                                                                    <?php echo htmlspecialchars($row['message']); ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <?php if ($row['status'] == 'new'): ?>
                                                                    <span class="status-badge badge-new">New</span>
                                                                <?php else: ?>
                                                                    <span class="status-badge badge-read">Read</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button class="btn btn-primary btn-sm rounded-pill px-3 me-2" 
                                                                            onclick='viewEnquiry(<?php echo json_encode($row); ?>)'>
                                                                        <i class="mdi mdi-eye me-1"></i> View
                                                                    </button>
                                                                    <a href="enquiries.php?delete_id=<?php echo $row['id']; ?>" 
                                                                       class="text-danger p-1" 
                                                                       onclick="return confirm('Are you sure you want to delete this message?')"
                                                                       title="Delete Message">
                                                                        <i class="mdi mdi-delete-variant h4 mb-0"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- DataTables js -->
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#enquiries-table').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 10,
                "language": {
                    "paginate": {
                        "previous": "<i class='mdi mdi-chevron-left'>",
                        "next": "<i class='mdi mdi-chevron-right'>"
                    }
                },
                "drawCallback": function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });

            var status = "<?php echo $status; ?>";
            if (status == "success") {
                $('#success-alert-modal').modal('show');
            } else if (status == "error") {
                $('#error-alert-modal').modal('show');
            }
        });

        function viewEnquiry(enquiry) {
            $('#view-date').text(new Date(enquiry.created_at).toLocaleString());
            $('#view-email').text(enquiry.email);
            $('#view-email-link').attr('href', 'mailto:' + enquiry.email);
            $('#view-phone').text(enquiry.phone_number);
            $('#view-message').text(enquiry.message);
            
            const badge = $('#view-status-badge');
            const toggleBtn = $('#toggle-status-btn');
            
            if (enquiry.status === 'new') {
                badge.html('<span class="status-badge badge-new">New</span>');
                toggleBtn.text('Mark as Read').attr('href', 'update_enquiry_status.php?id=' + enquiry.id + '&status=read');
                toggleBtn.removeClass('btn-outline-warning').addClass('btn-outline-primary');
            } else {
                badge.html('<span class="status-badge badge-read">Read</span>');
                toggleBtn.text('Mark as New').attr('href', 'update_enquiry_status.php?id=' + enquiry.id + '&status=new');
                toggleBtn.removeClass('btn-outline-primary').addClass('btn-outline-warning');
            }
            
            $('#view-enquiry-modal').modal('show');
        }
    </script>

</body>

</html>
