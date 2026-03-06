<?php
$title = 'Dashboard';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
}
$user = $_SESSION['name'];

// Overview Statistics
$total_clients = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM clients"))['count'];
$active_projects = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM projects"))['count'];
$total_members = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM members"))['count'];
$total_services = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM services"))['count'];
$total_blogs = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM blogs"))['count'];

// Recent Activity
$recent_projects = mysqli_query($con, "SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
$recent_blogs = mysqli_query($con, "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 3");
$recent_clients = mysqli_query($con, "SELECT * FROM clients ORDER BY created_at DESC LIMIT 5");

// Client Analytics
$clients_by_category = mysqli_query($con, "SELECT cc.name, COUNT(c.id) as count FROM client_categories cc LEFT JOIN clients c ON cc.id = c.category_id GROUP BY cc.id, cc.name ORDER BY count DESC");

// Team Overview
$board_members = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM members WHERE member_type = 'board'"))['count'];
$team_members = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM members WHERE member_type = 'team'"))['count'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="TLS CMS Dashboard" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />

    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    <link rel="manifest" href="assets/images/site.webmanifest">
    
    <style>
        .stat-card {
            transition: all 0.3s;
            border-left: 4px solid;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .stat-card.clients {
            border-left-color: #E62B1E;
        }
        .stat-card.projects {
            border-left-color: #198754;
        }
        .stat-card.members {
            border-left-color: #6f42c1;
        }
        .stat-card.services {
            border-left-color: #fd7e14;
        }
        .stat-card.blogs {
            border-left-color: #d63384;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .quick-action-btn {
            padding: 15px;
            border-radius: 8px;
            transition: all 0.2s;
            border: 2px dashed #dee2e6;
            text-align: center;
            cursor: pointer;
        }
        .quick-action-btn:hover {
            border-color: #E62B1E;
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        .activity-item {
            padding: 12px;
            border-left: 3px solid #e9ecef;
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .activity-item:hover {
            border-left-color: #E62B1E;
            background: #f8f9fa;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>

<body class="loading" data-layout-color="light" data-layout-mode="default" data-layout-size="fluid"
    data-topbar-color="light" data-leftbar-position="fixed" data-leftbar-color="light" data-leftbar-size='default'
    data-sidebar-user='true'>

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
                                <!-- <h4 class="page-title">Dashboard</h4> -->
                                <p class="text-muted">Welcome to TLS CMS, <?php echo htmlspecialchars($user); ?>!</p>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Overview Statistics -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card clients">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="stat-icon bg-primary bg-soft text-primary">
                                                <i class="mdi mdi-domain"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="text-muted mb-1">Total Clients</h5>
                                            <h3 class="mb-0"><?php echo $total_clients; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card projects">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="stat-icon bg-success bg-soft text-success">
                                                <i class="mdi mdi-briefcase"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="text-muted mb-1">Active Projects</h5>
                                            <h3 class="mb-0"><?php echo $active_projects; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card members">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="stat-icon bg-purple bg-soft text-purple">
                                                <i class="mdi mdi-account-group"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="text-muted mb-1">Team Members</h5>
                                            <h3 class="mb-0"><?php echo $total_members; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card services">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="stat-icon bg-warning bg-soft text-warning">
                                                <i class="mdi mdi-cog"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="text-muted mb-1">Services</h5>
                                            <h3 class="mb-0"><?php echo $total_services; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card stat-card blogs">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="stat-icon bg-pink bg-soft text-pink">
                                                <i class="mdi mdi-post"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="text-muted mb-1">Blog Posts</h5>
                                            <h3 class="mb-0"><?php echo $total_blogs; ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Quick Actions</h4>
                                    <div class="row">
                                        <div class="col-md-3 col-6 mb-3">
                                            <a href="projects.php" class="text-decoration-none">
                                                <div class="quick-action-btn">
                                                    <i class="mdi mdi-plus-circle text-primary" style="font-size: 32px;"></i>
                                                    <h6 class="mt-2 mb-0">Add Project</h6>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <a href="clients.php" class="text-decoration-none">
                                                <div class="quick-action-btn">
                                                    <i class="mdi mdi-domain-plus text-success" style="font-size: 32px;"></i>
                                                    <h6 class="mt-2 mb-0">Add Client</h6>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <a href="blogs.php" class="text-decoration-none">
                                                <div class="quick-action-btn">
                                                    <i class="mdi mdi-post-outline text-warning" style="font-size: 32px;"></i>
                                                    <h6 class="mt-2 mb-0">Create Blog</h6>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-6 mb-3">
                                            <a href="team-members.php" class="text-decoration-none">
                                                <div class="quick-action-btn">
                                                    <i class="mdi mdi-account-plus text-info" style="font-size: 32px;"></i>
                                                    <h6 class="mt-2 mb-0">Add Member</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Projects -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Recent Projects</h4>
                                    <?php if(mysqli_num_rows($recent_projects) > 0): ?>
                                        <?php while($project = mysqli_fetch_assoc($recent_projects)): ?>
                                            <div class="activity-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($project['title']); ?></h6>
                                                        <p class="text-muted mb-0 small">
                                                            <?php if($project['client_name']): ?>
                                                                <i class="mdi mdi-domain"></i> <?php echo htmlspecialchars($project['client_name']); ?>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-<?php 
                                                        echo $project['status'] == 'in_progress' ? 'primary' : 
                                                            ($project['status'] == 'completed' ? 'success' : 
                                                            ($project['status'] == 'on_hold' ? 'warning' : 'secondary')); 
                                                    ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No recent projects</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Clients -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Recent Clients</h4>
                                    <?php if(mysqli_num_rows($recent_clients) > 0): ?>
                                        <?php while($client = mysqli_fetch_assoc($recent_clients)): ?>
                                            <div class="activity-item">
                                                <div class="d-flex align-items-center">
                                                    <?php if(!empty($client['logo']) && file_exists($client['logo'])): ?>
                                                        <img src="<?php echo htmlspecialchars($client['logo']); ?>" class="me-2" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;" alt="">
                                                    <?php else: ?>
                                                        <div class="me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                            <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($client['name']); ?></h6>
                                                        <small class="text-muted">Added <?php echo date('M d, Y', strtotime($client['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No recent clients</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Row: Clients by Category, Team Overview, Blog Engagement -->
                    <div class="row">
                        <!-- Client Analytics -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Clients by Category</h4>
                                    <div class="chart-container">
                                        <canvas id="clientCategoryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Team Overview -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Team Overview</h4>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="p-3">
                                                <i class="mdi mdi-account-tie text-primary" style="font-size: 48px;"></i>
                                                <h3 class="mt-2 mb-1"><?php echo $board_members; ?></h3>
                                                <p class="text-muted mb-0">Board Members</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3">
                                                <i class="mdi mdi-account-group text-success" style="font-size: 48px;"></i>
                                                <h3 class="mt-2 mb-1"><?php echo $team_members; ?></h3>
                                                <p class="text-muted mb-0">Team Members</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Recent Blog Posts -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Recent Blog Posts</h4>
                                    <div class="row">
                                        <?php if(mysqli_num_rows($recent_blogs) > 0): ?>
                                            <?php while($blog = mysqli_fetch_assoc($recent_blogs)): ?>
                                                <div class="col-md-4">
                                                    <div class="card border">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                                            <p class="card-text text-muted small">
                                                                <?php echo htmlspecialchars(substr($blog['content'], 0, 100)) . '...'; ?>
                                                            </p>

                                                                <small class="text-muted"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <p class="text-muted">No recent blog posts</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <script>
        // Client Category Chart
        var ctx = document.getElementById('clientCategoryChart').getContext('2d');
        var clientCategoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    mysqli_data_seek($clients_by_category, 0);
                    $labels = [];
                    while($cat = mysqli_fetch_assoc($clients_by_category)) {
                        $labels[] = "'" . addslashes($cat['name']) . "'";
                    }
                    echo implode(',', $labels);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                        mysqli_data_seek($clients_by_category, 0);
                        $data = [];
                        while($cat = mysqli_fetch_assoc($clients_by_category)) {
                            $data[] = $cat['count'];
                        }
                        echo implode(',', $data);
                        ?>
                    ],
                    backgroundColor: [
                        '#E62B1E',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#fd7e14',
                        '#20c997',
                        '#d63384'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>

</body>

</html>
