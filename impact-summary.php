<?php
$title = 'Impact Summary';
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

// Fetch impact summary data
$sql = "SELECT * FROM impact_summary WHERE id = 1";
$result = mysqli_query($con, $sql);
$impact = mysqli_fetch_assoc($result);

// If no record exists, create default
if (!$impact) {
    $create_sql = "INSERT INTO impact_summary (id, total_clients, total_projects, total_countries, people_impacted, sectors_impacted) 
                   VALUES (1, 0, 0, 0, 0, 0)";
    mysqli_query($con, $create_sql);
    $impact = [
        'total_clients' => 0,
        'total_projects' => 0,
        'total_countries' => 0,
        'people_impacted' => 0,
        'sectors_impacted' => 0
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Impact Summary</title>
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
        .stat-card {
            transition: all 0.3s;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-header {
            padding: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .stat-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .stat-icon {
            font-size: 48px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        .stat-value {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 16px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .clients-card .stat-header {
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
        }
        .projects-card .stat-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .countries-card .stat-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .people-card .stat-header {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .sectors-card .stat-header {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .edit-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .form-control-lg {
            font-size: 1.1rem;
            padding: 12px 20px;
        }
        .last-updated {
            text-align: center;
            color: #6c757d;
            margin-top: 20px;
            font-size: 14px;
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
                        <p class="mt-3 text-white">Impact summary has been updated successfully</p>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Impact Summary</h4>
                                <p class="text-muted">Key statistics showcasing our reach and impact</p>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Statistics Cards -->
                    <div class="row">
                        <!-- Total Clients -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card stat-card clients-card">
                                <div class="stat-header">
                                    <div class="stat-icon">
                                        <i class="mdi mdi-domain"></i>
                                    </div>
                                    <div class="stat-value"><?php echo number_format($impact['total_clients']); ?></div>
                                    <div class="stat-label">Clients</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Projects -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card stat-card projects-card">
                                <div class="stat-header">
                                    <div class="stat-icon">
                                        <i class="mdi mdi-briefcase"></i>
                                    </div>
                                    <div class="stat-value"><?php echo number_format($impact['total_projects']); ?></div>
                                    <div class="stat-label">Projects</div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Countries -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card stat-card countries-card">
                                <div class="stat-header">
                                    <div class="stat-icon">
                                        <i class="mdi mdi-earth"></i>
                                    </div>
                                    <div class="stat-value"><?php echo number_format($impact['total_countries']); ?></div>
                                    <div class="stat-label">Countries</div>
                                </div>
                            </div>
                        </div>

                        <!-- People Impacted -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card stat-card people-card">
                                <div class="stat-header">
                                    <div class="stat-icon">
                                        <i class="mdi mdi-account-group"></i>
                                    </div>
                                    <div class="stat-value"><?php echo number_format($impact['people_impacted']); ?></div>
                                    <div class="stat-label">People Impacted</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sectors Impacted -->
                        <div class="col-lg-3 col-md-6">
                            <div class="card stat-card sectors-card">
                                <div class="stat-header">
                                    <div class="stat-icon">
                                        <i class="mdi mdi-factory"></i>
                                    </div>
                                    <div class="stat-value"><?php echo number_format($impact['sectors_impacted']); ?></div>
                                    <div class="stat-label">Sectors Impacted</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="edit-section">
                                <h4 class="mb-4">
                                    <i class="mdi mdi-pencil text-primary"></i> Update Impact Statistics
                                </h4>
                                <form action="edit_impact_summary.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label for="total_clients" class="form-label">
                                                    <i class="mdi mdi-domain text-purple"></i> Total Clients
                                                </label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       id="total_clients" name="total_clients" 
                                                       value="<?php echo $impact['total_clients']; ?>" 
                                                       min="0" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label for="total_projects" class="form-label">
                                                    <i class="mdi mdi-briefcase text-danger"></i> Total Projects
                                                </label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       id="total_projects" name="total_projects" 
                                                       value="<?php echo $impact['total_projects']; ?>" 
                                                       min="0" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label for="total_countries" class="form-label">
                                                    <i class="mdi mdi-earth text-info"></i> Total Countries
                                                </label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       id="total_countries" name="total_countries" 
                                                       value="<?php echo $impact['total_countries']; ?>" 
                                                       min="0" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label for="people_impacted" class="form-label">
                                                    <i class="mdi mdi-account-group text-success"></i> People Impacted
                                                </label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       id="people_impacted" name="people_impacted" 
                                                       value="<?php echo $impact['people_impacted']; ?>" 
                                                       min="0" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="mb-3">
                                                <label for="sectors_impacted" class="form-label">
                                                    <i class="mdi mdi-factory text-warning"></i> Sectors Impacted
                                                </label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       id="sectors_impacted" name="sectors_impacted" 
                                                       value="<?php echo $impact['sectors_impacted']; ?>" 
                                                       min="0" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" name="update_impact" class="btn btn-primary btn-lg">
                                            <i class="mdi mdi-content-save me-1"></i> Update Statistics
                                        </button>
                                    </div>
                                </form>

                                <?php if(isset($impact['updated_at'])): ?>
                                <div class="last-updated">
                                    <i class="mdi mdi-clock-outline"></i> 
                                    Last updated: <?php echo date('F d, Y \a\t h:i A', strtotime($impact['updated_at'])); ?>
                                </div>
                                <?php endif; ?>
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

            // Animate counters on page load
            $('.stat-value').counterUp({
                delay: 10,
                time: 1000
            });
        });
    </script>

</body>

</html>
