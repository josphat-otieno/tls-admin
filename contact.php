<?php
$title = 'Contact Information';
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

// Fetch contact information
$sql = "SELECT * FROM contact_info WHERE id = 1";
$result = mysqli_query($con, $sql);
$contact = mysqli_fetch_assoc($result);

// If no record exists, create one
if (!$contact) {
    mysqli_query($con, "INSERT INTO contact_info (id) VALUES (1)");
    $result = mysqli_query($con, $sql);
    $contact = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Contact Information</title>
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
        .section-card {
            margin-bottom: 30px;
        }
        .section-header {
            background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .section-header h4 {
            margin: 0;
            color: white;
        }
        .social-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .info-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .info-preview h5 {
            color: #E62B1E;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        .info-item i {
            margin-right: 10px;
            color: #E62B1E;
            width: 24px;
        }
        .map-preview {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
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
                        <p class="mt-3 text-white">Contact information has been updated successfully</p>
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

                    <form action="edit_contact.php" method="POST">
                        <div class="row">
                            <!-- Social Media Links Section -->
                            <div class="col-lg-6">
                                <div class="card section-card">
                                    <div class="card-body">
                                        <div class="section-header">
                                            <h4><i class="mdi mdi-share-variant"></i> Social Media Links</h4>
                                        </div>

                                        <div class="mb-3">
                                            <label for="facebook_url" class="form-label">
                                                <i class="mdi mdi-facebook social-icon text-primary"></i>Facebook
                                            </label>
                                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                                   value="<?php echo htmlspecialchars($contact['facebook_url'] ?? ''); ?>"
                                                   placeholder="https://facebook.com/yourpage">
                                        </div>

                                        <div class="mb-3">
                                            <label for="twitter_url" class="form-label">
                                                <i class="mdi mdi-twitter social-icon text-info"></i>X (Twitter)
                                            </label>
                                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                                   value="<?php echo htmlspecialchars($contact['twitter_url'] ?? ''); ?>"
                                                   placeholder="https://x.com/yourhandle">
                                        </div>

                                        <div class="mb-3">
                                            <label for="instagram_url" class="form-label">
                                                <i class="mdi mdi-instagram social-icon text-danger"></i>Instagram
                                            </label>
                                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                                   value="<?php echo htmlspecialchars($contact['instagram_url'] ?? ''); ?>"
                                                   placeholder="https://instagram.com/yourprofile">
                                        </div>

                                        <div class="mb-3">
                                            <label for="linkedin_url" class="form-label">
                                                <i class="mdi mdi-linkedin social-icon text-primary"></i>LinkedIn
                                            </label>
                                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                                   value="<?php echo htmlspecialchars($contact['linkedin_url'] ?? ''); ?>"
                                                   placeholder="https://linkedin.com/company/yourcompany">
                                        </div>

                                        <div class="mb-3">
                                            <label for="youtube_url" class="form-label">
                                                <i class="mdi mdi-youtube social-icon text-danger"></i>YouTube
                                            </label>
                                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                                   value="<?php echo htmlspecialchars($contact['youtube_url'] ?? ''); ?>"
                                                   placeholder="https://youtube.com/@yourchannel">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Details Section -->
                            <div class="col-lg-6">
                                <div class="card section-card">
                                    <div class="card-body">
                                        <div class="section-header">
                                            <h4><i class="mdi mdi-phone"></i> Contact Details</h4>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                <i class="mdi mdi-email"></i> Email Address
                                            </label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>"
                                                   placeholder="info@example.com">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone_number" class="form-label">
                                                        <i class="mdi mdi-phone"></i> Phone Number
                                                    </label>
                                                    <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                                           value="<?php echo htmlspecialchars($contact['phone_number'] ?? ''); ?>"
                                                           placeholder="+254 700 000 000">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="office_number" class="form-label">
                                                        <i class="mdi mdi-phone-classic"></i> Office Name/Number
                                                    </label>
                                                    <input type="text" class="form-control" id="office_number" name="office_number" 
                                                           value="<?php echo htmlspecialchars($contact['office_number'] ?? ''); ?>"
                                                           placeholder="Suite A33">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="building_name" class="form-label">
                                                <i class="mdi mdi-office-building"></i> Building Name
                                            </label>
                                            <input type="text" class="form-control" id="building_name" name="building_name" 
                                                   value="<?php echo htmlspecialchars($contact['building_name'] ?? ''); ?>"
                                                   placeholder="ABC Plaza">
                                        </div>

                                        <div class="mb-3">
                                            <label for="street" class="form-label">
                                                <i class="mdi mdi-road-variant"></i> Street Address
                                            </label>
                                            <input type="text" class="form-control" id="street" name="street" 
                                                   value="<?php echo htmlspecialchars($contact['street'] ?? ''); ?>"
                                                   placeholder="Moi Avenue">
                                        </div>

                                        <div class="mb-3">
                                            <label for="po_box" class="form-label">
                                                <i class="mdi mdi-mailbox"></i> P.O. Box
                                            </label>
                                            <input type="text" class="form-control" id="po_box" name="po_box" 
                                                   value="<?php echo htmlspecialchars($contact['po_box'] ?? ''); ?>"
                                                   placeholder="P.O. Box 12345-00100, Nairobi">
                                        </div>

                                        <div class="mb-3">
                                            <label for="google_map_coordinates" class="form-label">
                                                <i class="mdi mdi-map-marker"></i> Google Maps Embed Code
                                            </label>
                                            <textarea class="form-control" id="google_map_coordinates" name="google_map_coordinates" rows="3"
                                                      placeholder='<iframe src="https://www.google.com/maps/embed?..." width="600" height="450"></iframe>'><?php echo htmlspecialchars($contact['google_map_coordinates'] ?? ''); ?></textarea>
                                            <small class="text-muted d-block mt-2">
                                                <strong>How to get Google Maps embed code:</strong><br>
                                                1. Go to <a href="https://www.google.com/maps" target="_blank">Google Maps</a><br>
                                                2. Search for your location<br>
                                                3. Click the <strong>"Share"</strong> button<br>
                                                4. Select <strong>"Embed a map"</strong> tab<br>
                                                5. Copy the entire <code>&lt;iframe&gt;</code> code<br>
                                                6. Paste it in the field above
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-end mb-4">
                                    <button type="submit" name="update_contact" class="btn btn-success btn-lg">
                                        <i class="mdi mdi-content-save me-1"></i> Save Contact Information
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Preview Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body info-preview">
                                    <h5><i class="mdi mdi-eye"></i> Preview</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Social Media</h6>
                                            <?php if(!empty($contact['facebook_url'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-facebook"></i>
                                                    <a href="<?php echo htmlspecialchars($contact['facebook_url']); ?>" target="_blank">Facebook</a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['twitter_url'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-twitter"></i>
                                                    <a href="<?php echo htmlspecialchars($contact['twitter_url']); ?>" target="_blank">X (Twitter)</a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['instagram_url'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-instagram"></i>
                                                    <a href="<?php echo htmlspecialchars($contact['instagram_url']); ?>" target="_blank">Instagram</a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['linkedin_url'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-linkedin"></i>
                                                    <a href="<?php echo htmlspecialchars($contact['linkedin_url']); ?>" target="_blank">LinkedIn</a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['youtube_url'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-youtube"></i>
                                                    <a href="<?php echo htmlspecialchars($contact['youtube_url']); ?>" target="_blank">YouTube</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-3">Contact Information</h6>
                                            <?php if(!empty($contact['email'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-email"></i>
                                                    <span><?php echo htmlspecialchars($contact['email']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['phone_number'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-phone"></i>
                                                    <span><?php echo htmlspecialchars($contact['phone_number']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['office_number'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-phone-classic"></i>
                                                    <span><?php echo htmlspecialchars($contact['office_number']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['building_name']) || !empty($contact['street'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-map-marker"></i>
                                                    <span>
                                                        <?php echo htmlspecialchars($contact['building_name'] ?? ''); ?>
                                                        <?php if($contact['building_name'] && $contact['street']) echo ', '; ?>
                                                        <?php echo htmlspecialchars($contact['street'] ?? ''); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($contact['po_box'])): ?>
                                                <div class="info-item">
                                                    <i class="mdi mdi-mailbox"></i>
                                                    <span><?php echo htmlspecialchars($contact['po_box']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($contact['google_map_coordinates'])): ?>
                                        <div class="mt-4">
                                            <h6 class="text-muted mb-3">Location Map</h6>
                                            <div class="map-preview">
                                                <?php echo $contact['google_map_coordinates']; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
    </script>

</body>

</html>
