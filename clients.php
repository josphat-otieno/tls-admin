<?php
$title = 'Clients Management';
include 'dbConnect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location:login.php");
    exit();
}
$user = $_SESSION['name'];

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'clients';
// Which client to show works for
$selected_client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

// Status handling
$status = '';
$error_msg = '';
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ': $status = 'success'; break;
        case 'err':
            $status = 'error';
            if (isset($_GET['msg'])) {
                switch($_GET['msg']) {
                    case 'invalid_image':   $error_msg = 'Invalid image file!'; break;
                    case 'invalid_file':    $error_msg = 'Invalid file type! Use JPG, PNG, GIF, WEBP, MP4, or WEBM.'; break;
                    case 'file_too_large':  $error_msg = 'File too large! Maximum size is 50MB.'; break;
                    case 'upload_failed':   $error_msg = 'Upload failed! Please try again.'; break;
                    default:                $error_msg = 'Something went wrong. Please try again.'; break;
                }
            }
            break;
    }
}

// Fetch all clients
$clients_result = mysqli_query($con, "SELECT * FROM clients ORDER BY name ASC");

// Fetch client list for works dropdown
$clients_for_works = mysqli_query($con, "SELECT id, name FROM clients ORDER BY name ASC");

// Fetch works for selected client (or all if none selected)
if ($selected_client_id > 0) {
    $works_sql = "SELECT cw.*, c.name as client_name 
                  FROM client_works cw 
                  JOIN clients c ON cw.client_id = c.id 
                  WHERE cw.client_id = $selected_client_id 
                  ORDER BY cw.is_featured DESC, cw.created_at DESC";
} else {
    $works_sql = "SELECT cw.*, c.name as client_name 
                  FROM client_works cw 
                  JOIN clients c ON cw.client_id = c.id 
                  ORDER BY c.name ASC, cw.is_featured DESC, cw.created_at DESC";
}
$works_result = mysqli_query($con, $works_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>TLS CMS Admin - Clients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .client-card { transition: all 0.3s; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .client-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .client-logo { width: 80px; height: 80px; object-fit: contain; border: 1px solid #e3e6f0; border-radius: 8px; padding: 8px; background: #fff; }
        .client-logo-placeholder { width: 80px; height: 80px; background: linear-gradient(135deg, #E62B1E 0%, #8b1a12 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; }
        .logo-upload-area { border: 2px dashed #cbd5e0; border-radius: 8px; padding: 30px; text-align: center; background: #f8f9fa; cursor: pointer; transition: all 0.3s; }
        .logo-upload-area:hover { border-color: #E62B1E; background: #f0f1ff; }
        .logo-upload-area.dragover { border-color: #E62B1E; background: #e8eaff; }
        .logo-preview { max-width: 200px; max-height: 200px; margin: 15px auto; display: none; }
        .logo-preview img { max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .file-info { font-size: 12px; color: #6c757d; margin-top: 10px; }
        .remove-logo-btn { display: none; margin-top: 10px; }
        /* Works styles */
        .work-card { transition: all 0.3s; border-radius: 10px; overflow: hidden; }
        .work-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
        .work-thumbnail { width: 100%; height: 180px; object-fit: cover; background: #f0f1ff; }
        .work-video-thumb { width: 100%; height: 180px; object-fit: cover; }
        .work-video-placeholder { width: 100%; height: 180px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; }
        .featured-badge { position: absolute; top: 10px; left: 10px; z-index: 5; }
        .type-badge { position: absolute; top: 10px; right: 10px; z-index: 5; }
        .work-card-img-wrapper { position: relative; }
        .client-filter-bar { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; }
        #filename-warning-modal { z-index: 1060; }
        .video-url-preview { border-radius: 6px; overflow: hidden; margin-top: 8px; }
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
                        <p class="mt-3 text-white" id="error-message">
                            <?php echo $error_msg ? htmlspecialchars($error_msg) : 'Something went wrong. Please try again'; ?>
                        </p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filename Warning Modal -->
    <div id="filename-warning-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:1060;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title text-white">Invalid Filename</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3"><i class="mdi mdi-alert-circle-outline h1 text-warning"></i></div>
                    <h5>Filename contains special characters!</h5>
                    <p class="text-muted">Characters like apostrophes ('), quotes ("), and special symbols can cause issues with file storage.</p>
                    <div class="alert alert-light border shadow-sm">
                        <div class="mb-2 text-dark"><strong>Current:</strong> <span id="warn-filename-current" class="text-danger"></span></div>
                        <div class="text-dark"><strong>Suggested:</strong> <span id="warn-filename-suggested" class="text-success"></span></div>
                    </div>
                    <p class="text-dark mb-0">Please rename your file and try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning text-white" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ ADD/EDIT CLIENT MODAL ══════════════════════════════════════════════ -->
    <div id="client-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add Client</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="client-form" action="edit_client.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="client-id">
                        <input type="hidden" name="old_logo" id="old-logo">
                        <div class="mb-3">
                            <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter client name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client Logo</label>
                            <div class="logo-upload-area" id="logo-upload-area">
                                <input type="file" name="logo" id="logo-input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;">
                                <i class="mdi mdi-cloud-upload text-muted" style="font-size: 48px;"></i>
                                <h5 class="mt-2">Click to upload or drag and drop</h5>
                                <p class="text-muted mb-0">JPG, PNG, GIF or WEBP (Max 10MB)</p>
                                <div class="logo-preview" id="logo-preview">
                                    <img src="" alt="Logo preview" id="preview-image">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-logo-btn" id="remove-logo-btn">
                                    <i class="mdi mdi-delete"></i> Remove Logo
                                </button>
                            </div>
                            <div class="file-info" id="file-info"></div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_client" id="submit-btn" class="btn btn-primary">Add Client</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ ADD/EDIT WORK MODAL ════════════════════════════════════════════════ -->
    <div id="work-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="work-modal-title">Add Client Work</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="work-form" action="edit_client_work.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="work-id">
                        <input type="hidden" name="old_media" id="old-media">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work-client-id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select class="form-select" id="work-client-id" name="client_id" required>
                                        <option value="">Select Client</option>
                                        <?php
                                        mysqli_data_seek($clients_for_works, 0);
                                        while($cl = mysqli_fetch_assoc($clients_for_works)):
                                        ?>
                                            <option value="<?php echo $cl['id']; ?>"><?php echo htmlspecialchars($cl['name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work-type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="work-type" name="type" onchange="toggleWorkType(this.value)">
                                        <option value="banner">Banner (Image)</option>
                                        <option value="video">Video</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="work-title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="work-title" name="title" required placeholder="e.g. Brand Campaign 2024">
                        </div>

                        <!-- Banner upload -->
                        <div id="banner-upload-section" class="mb-3">
                            <label class="form-label">Banner Image</label>
                            <div class="logo-upload-area" id="work-upload-area">
                                <input type="file" name="media" id="work-media-input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,video/mp4,video/webm" style="display: none;">
                                <i class="mdi mdi-image-area text-muted" style="font-size: 48px;"></i>
                                <h5 class="mt-2">Click to upload banner image</h5>
                                <p class="text-muted mb-0">JPG, PNG, GIF or WEBP (Max 50MB)</p>
                                <div id="work-media-preview" style="display:none; margin-top:12px;">
                                    <img id="work-preview-img" src="" alt="Preview" style="max-width:200px; max-height:150px; border-radius:6px;">
                                </div>
                            </div>
                            <div class="file-info" id="work-file-info"></div>
                        </div>

                        <!-- Video section -->
                        <div id="video-upload-section" class="mb-3" style="display:none;">
                            <label class="form-label">Video</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="video_source" id="video-file-radio" value="file" checked onchange="toggleVideoSource('file')">
                                    <label class="form-check-label" for="video-file-radio">Upload File</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="video_source" id="video-url-radio" value="url" onchange="toggleVideoSource('url')">
                                    <label class="form-check-label" for="video-url-radio">Embed URL (YouTube/Vimeo)</label>
                                </div>
                            </div>
                            <div id="video-file-section">
                                <div class="logo-upload-area" id="work-video-upload-area">
                                    <input type="file" name="media" id="work-video-input" accept="video/mp4,video/webm,video/ogg" style="display: none;">
                                    <i class="mdi mdi-video text-muted" style="font-size: 48px;"></i>
                                    <h5 class="mt-2">Click to upload video</h5>
                                    <p class="text-muted mb-0">MP4, WEBM, OGG (Max 50MB)</p>
                                </div>
                                <div class="file-info" id="work-video-file-info"></div>
                            </div>
                            <div id="video-url-section" style="display:none;">
                                <input type="text" class="form-control" name="video_url" id="work-video-url" placeholder="https://www.youtube.com/embed/...">
                                <small class="text-muted">Use the embed URL (e.g. youtube.com/embed/VIDEO_ID)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="work-description" class="form-label">Description</label>
                            <textarea class="form-control" id="work-description" name="description" rows="2" placeholder="Brief description of this work"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="work-featured" name="is_featured" value="1">
                                <label class="form-check-label" for="work-featured">
                                    <strong>Set as Featured</strong>
                                    <small class="text-muted d-block">This work will be shown automatically on the public site. Only one work per client can be featured.</small>
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_work" id="work-submit-btn" class="btn btn-primary">Add Work</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ VIEW MORE WORKS MODAL ══════════════════════════════════════════════ -->
    <div id="view-more-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="view-more-title">All Works</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="view-more-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">
        <?php include 'topbar.php' ?>
        <?php include 'sidebar.php' ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#clients-tab" data-bs-toggle="tab"
                               class="nav-link <?php echo $active_tab == 'clients' ? 'active' : ''; ?>">
                                <i class="mdi mdi-domain me-1"></i> Clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#works-tab" data-bs-toggle="tab"
                               class="nav-link <?php echo $active_tab == 'works' ? 'active' : ''; ?>">
                                <i class="mdi mdi-image-multiple me-1"></i> Client Works
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- ═══ CLIENTS TAB ═══════════════════════════════════ -->
                        <div class="tab-pane fade <?php echo $active_tab == 'clients' ? 'show active' : ''; ?>" id="clients-tab">
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <div class="text-sm-end">
                                        <button class="btn btn-primary" onclick="openAddModal()">
                                            <i class="mdi mdi-plus-circle me-1"></i> Add Client
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="clients-container">
                                <?php if(mysqli_num_rows($clients_result) > 0): ?>
                                    <?php while($client = mysqli_fetch_assoc($clients_result)): ?>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="card client-card text-center">
                                                <div class="card-body">
                                                    <?php if(!empty($client['logo']) && file_exists($client['logo'])): ?>
                                                        <img src="<?php echo htmlspecialchars($client['logo']); ?>"
                                                             class="client-logo mb-2"
                                                             alt="<?php echo htmlspecialchars($client['name']); ?>">
                                                    <?php else: ?>
                                                        <div class="client-logo-placeholder mb-2 mx-auto">
                                                            <i class="mdi mdi-domain"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <h6 class="mt-1 mb-2"><?php echo htmlspecialchars($client['name']); ?></h6>
                                                    <p class="text-muted mb-2">
                                                        <small><i class="mdi mdi-calendar"></i> <?php echo date('M d, Y', strtotime($client['created_at'])); ?></small>
                                                    </p>
                                                    <?php
                                                        // Count works for this client
                                                        $wc = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as cnt FROM client_works WHERE client_id = " . $client['id']));
                                                        $works_count = $wc['cnt'];
                                                    ?>
                                                    <span class="badge bg-soft-info text-info mb-2"><?php echo $works_count; ?> Work<?php echo $works_count != 1 ? 's' : ''; ?></span>
                                                    <div class="d-flex justify-content-center gap-2 mt-2">
                                                        <button class="btn btn-sm btn-outline-primary"
                                                                onclick='editClient(<?php echo htmlspecialchars(json_encode($client), ENT_QUOTES, "UTF-8"); ?>)'>
                                                            <i class="mdi mdi-pencil"></i> Edit
                                                        </button>
                                                        <a href="edit_client.php?delete_client=<?php echo $client['id']; ?>"
                                                           class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Delete this client and all their works?')">
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
                                            <div class="card-body text-center py-5">
                                                <i class="mdi mdi-domain h1 text-muted"></i>
                                                <h4 class="mt-3">No Clients Yet</h4>
                                                <p class="text-muted">Click "Add Client" to get started.</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ═══ CLIENT WORKS TAB ══════════════════════════════ -->
                        <div class="tab-pane fade <?php echo $active_tab == 'works' ? 'show active' : ''; ?>" id="works-tab">
                            <div class="row mb-3 align-items-center">
                                <div class="col">
                                    <!-- Client filter -->
                                    <div class="client-filter-bar d-flex flex-wrap align-items-center gap-2">
                                        <span class="fw-semibold me-2 text-nowrap">Filter by Client:</span>
                                        <a href="clients.php?tab=works" class="btn btn-sm <?php echo $selected_client_id == 0 ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                            All Clients
                                        </a>
                                        <?php
                                        $clients_filter = mysqli_query($con, "SELECT id, name FROM clients ORDER BY name ASC");
                                        while($cf = mysqli_fetch_assoc($clients_filter)):
                                        ?>
                                            <a href="clients.php?tab=works&client_id=<?php echo $cf['id']; ?>"
                                               class="btn btn-sm <?php echo $selected_client_id == $cf['id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                                <?php echo htmlspecialchars($cf['name']); ?>
                                            </a>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary" onclick="openAddWorkModal()">
                                        <i class="mdi mdi-plus-circle me-1"></i> Add Work
                                    </button>
                                </div>
                            </div>

                            <?php
                            // Group works by client for display
                            $works_by_client = [];
                            if (mysqli_num_rows($works_result) > 0) {
                                while($w = mysqli_fetch_assoc($works_result)) {
                                    $works_by_client[$w['client_id']]['client_name'] = $w['client_name'];
                                    $works_by_client[$w['client_id']]['works'][] = $w;
                                }
                            }
                            ?>

                            <?php if (empty($works_by_client)): ?>
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="mdi mdi-image-multiple h1 text-muted"></i>
                                        <h4 class="mt-3">No Works Found</h4>
                                        <p class="text-muted">Click "Add Work" to add banners or videos for your clients.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($works_by_client as $cid => $group): ?>
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="mdi mdi-domain text-primary me-2"></i>
                                                <?php echo htmlspecialchars($group['client_name']); ?>
                                                <span class="badge bg-soft-primary text-primary ms-2"><?php echo count($group['works']); ?> Work<?php echo count($group['works']) != 1 ? 's' : ''; ?></span>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            $featured_work = null;
                                            $other_works = [];
                                            foreach ($group['works'] as $w) {
                                                if ($w['is_featured']) $featured_work = $w;
                                                else $other_works[] = $w;
                                            }
                                            ?>

                                            <!-- Featured Work -->
                                            <?php if ($featured_work): ?>
                                                <div class="mb-3">
                                                    <span class="badge bg-warning text-dark mb-2"><i class="mdi mdi-star me-1"></i>Featured Work</span>
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-4">
                                                            <?php echo renderWorkCard($featured_work, true); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Other works -->
                                            <?php if (!empty($other_works)): ?>
                                                <div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-muted fw-semibold">Other Works</span>
                                                        <?php if (count($other_works) > 3): ?>
                                                            <button class="btn btn-sm btn-link ms-2 p-0"
                                                                    onclick='openViewMore(<?php echo htmlspecialchars(json_encode($group['client_name']), ENT_QUOTES); ?>, <?php echo htmlspecialchars(json_encode($other_works), ENT_QUOTES); ?>)'>
                                                                View All (<?php echo count($other_works); ?>)
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="row">
                                                        <?php foreach (array_slice($other_works, 0, 3) as $ow): ?>
                                                            <div class="col-md-6 col-lg-4">
                                                                <?php echo renderWorkCard($ow, false); ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if (count($other_works) > 3): ?>
                                                        <div class="text-center mt-2">
                                                            <button class="btn btn-outline-primary btn-sm"
                                                                    onclick='openViewMore(<?php echo htmlspecialchars(json_encode($group['client_name']), ENT_QUOTES); ?>, <?php echo htmlspecialchars(json_encode($other_works), ENT_QUOTES); ?>)'>
                                                                <i class="mdi mdi-eye me-1"></i> View More (<?php echo count($other_works) - 3; ?> more)
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!$featured_work && empty($other_works)): ?>
                                                <p class="text-muted mb-0">No works added yet.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div><!-- /tab-content -->
                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div>
    <!-- END wrapper -->
    <?php include 'common_cropper.php'; ?>

<?php
// Helper to render a work card
function renderWorkCard($work, $is_featured) {
    $id = (int)$work['id'];
    $client_id = (int)$work['client_id'];
    $title = htmlspecialchars($work['title']);
    $type = $work['type'];
    $description = htmlspecialchars($work['description'] ?? '');
    $media_path = $work['media_path'];
    $video_url = $work['video_url'];

    ob_start(); ?>
    <div class="card work-card mb-3">
        <div class="work-card-img-wrapper">
            <?php if ($is_featured): ?>
                <span class="featured-badge badge bg-warning text-dark"><i class="mdi mdi-star"></i> Featured</span>
            <?php endif; ?>
            <span class="type-badge badge <?php echo $type === 'video' ? 'bg-danger' : 'bg-info'; ?>">
                <i class="mdi mdi-<?php echo $type === 'video' ? 'play-circle' : 'image'; ?>"></i>
                <?php echo ucfirst($type); ?>
            </span>

            <?php if ($type === 'banner' && $media_path && file_exists($media_path)): ?>
                <img src="<?php echo htmlspecialchars($media_path); ?>" class="work-thumbnail" alt="<?php echo $title; ?>">
            <?php elseif ($type === 'video' && $video_url): ?>
                <div class="work-video-placeholder">
                    <i class="mdi mdi-play-circle-outline" style="font-size: 48px;"></i>
                    <small class="mt-2">Embedded Video</small>
                </div>
            <?php elseif ($type === 'video' && $media_path && file_exists($media_path)): ?>
                <video class="work-video-thumb" controls>
                    <source src="<?php echo htmlspecialchars($media_path); ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <div class="work-video-placeholder">
                    <i class="mdi mdi-image-off-outline" style="font-size: 48px;"></i>
                    <small class="mt-2">No media</small>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-2">
            <h6 class="mb-1"><?php echo $title; ?></h6>
            <?php if ($description): ?><p class="text-muted mb-1" style="font-size:12px;"><?php echo $description; ?></p><?php endif; ?>
            <div class="d-flex gap-1 mt-2 flex-wrap">
                <button class="btn btn-xs btn-outline-primary"
                        style="font-size:11px; padding:2px 8px;"
                        onclick='editWork(<?php echo htmlspecialchars(json_encode($work), ENT_QUOTES, "UTF-8"); ?>)'>
                    <i class="mdi mdi-pencil"></i> Edit
                </button>
                <?php if (!$is_featured): ?>
                <a href="edit_client_work.php?set_featured=<?php echo $id; ?>&client_id=<?php echo $client_id; ?>"
                   class="btn btn-xs btn-outline-warning"
                   style="font-size:11px; padding:2px 8px;"
                   onclick="return confirm('Set this as the featured work?')">
                    <i class="mdi mdi-star"></i> Feature
                </a>
                <?php endif; ?>
                <a href="edit_client_work.php?delete_work=<?php echo $id; ?>&client_id=<?php echo $client_id; ?>"
                   class="btn btn-xs btn-outline-danger"
                   style="font-size:11px; padding:2px 8px;"
                   onclick="return confirm('Delete this work?')">
                    <i class="mdi mdi-delete"></i>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>

    <!-- Vendor -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        var status = "<?php echo $status; ?>";
        if (status === "success") $('#success-alert-modal').modal('show');
        else if (status === "error") $('#error-alert-modal').modal('show');

        // Activate correct tab
        var activeTab = "<?php echo $active_tab; ?>";
        if (activeTab === 'works') {
            var tabEl = document.querySelector('a[href="#works-tab"]');
            if (tabEl) new bootstrap.Tab(tabEl).show();
        }

        // Select2 for work client dropdown
        $('#work-client-id').select2({ theme: 'bootstrap-5', placeholder: 'Select Client', dropdownParent: $('#work-modal') });

        // Pre-select client if filtered
        var preselectedClient = <?php echo $selected_client_id ?: 'null'; ?>;
        if (preselectedClient) {
            $('#work-client-id').val(preselectedClient).trigger('change');
        }
    });

    // ════ CLIENT FUNCTIONS ══════════════════════════════════════════════

    function openAddModal() {
        document.getElementById('modal-title').textContent = 'Add Client';
        document.getElementById('client-form').reset();
        document.getElementById('client-id').value = '';
        document.getElementById('old-logo').value = '';
        document.getElementById('submit-btn').name = 'add_client';
        document.getElementById('submit-btn').textContent = 'Add Client';
        document.getElementById('logo-preview').style.display = 'none';
        document.getElementById('remove-logo-btn').style.display = 'none';
        document.getElementById('file-info').textContent = '';
        $('#client-modal').modal('show');
    }

    function editClient(client) {
        document.getElementById('modal-title').textContent = 'Edit Client';
        document.getElementById('client-id').value = client.id;
        document.getElementById('name').value = client.name;
        document.getElementById('old-logo').value = client.logo || '';
        document.getElementById('submit-btn').name = 'update_client';
        document.getElementById('submit-btn').textContent = 'Update Client';
        if (client.logo) {
            document.getElementById('preview-image').src = client.logo;
            document.getElementById('logo-preview').style.display = 'block';
            document.getElementById('remove-logo-btn').style.display = 'inline-block';
            document.getElementById('file-info').textContent = 'Current logo (click to change)';
        } else {
            document.getElementById('logo-preview').style.display = 'none';
            document.getElementById('remove-logo-btn').style.display = 'none';
            document.getElementById('file-info').textContent = '';
        }
        $('#client-modal').modal('show');
    }

    // Logo upload area handling
    const logoInput = document.getElementById('logo-input');
    const logoUploadArea = document.getElementById('logo-upload-area');
    const logoPreview = document.getElementById('logo-preview');
    const previewImage = document.getElementById('preview-image');
    const fileInfo = document.getElementById('file-info');
    const removeLogoBtn = document.getElementById('remove-logo-btn');

    logoUploadArea.addEventListener('click', function(e) {
        if (e.target !== removeLogoBtn && !removeLogoBtn.contains(e.target)) logoInput.click();
    });
    logoInput.addEventListener('change', function() { handleLogoFile(this.files[0]); });
    logoUploadArea.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
    logoUploadArea.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('dragover'); });
    logoUploadArea.addEventListener('drop', function(e) {
        e.preventDefault(); this.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) { logoInput.files = e.dataTransfer.files; handleLogoFile(file); }
    });
    removeLogoBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        logoInput.value = '';
        logoPreview.style.display = 'none';
        removeLogoBtn.style.display = 'none';
        fileInfo.textContent = '';
    });

    function handleLogoFile(file) {
        if (!file) return;
        if (hasSpecialChars(file.name)) { showFilenameWarning(file.name); logoInput.value = ''; return; }
        const allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
        if (!allowed.includes(file.type)) { alert('Please upload a valid image file (JPG, PNG, GIF, or WEBP)'); logoInput.value = ''; return; }
        initCropper(file, 'logo-input', 'preview-image');
    }

    document.getElementById('client-form').addEventListener('submit', function(e) {
        const file = document.getElementById('logo-input')?.files?.[0];
        if (file && hasSpecialChars(file.name)) { e.preventDefault(); showFilenameWarning(file.name); }
    });

    // ════ WORK FUNCTIONS ════════════════════════════════════════════════

    function openAddWorkModal() {
        document.getElementById('work-modal-title').textContent = 'Add Client Work';
        document.getElementById('work-form').reset();
        document.getElementById('work-id').value = '';
        document.getElementById('old-media').value = '';
        document.getElementById('work-submit-btn').name = 'add_work';
        document.getElementById('work-submit-btn').textContent = 'Add Work';
        document.getElementById('work-media-preview').style.display = 'none';
        toggleWorkType('banner');
        var preselected = <?php echo $selected_client_id ?: 'null'; ?>;
        if (preselected) $('#work-client-id').val(preselected).trigger('change');
        $('#work-modal').modal('show');
    }

    function editWork(work) {
        document.getElementById('work-modal-title').textContent = 'Edit Work';
        document.getElementById('work-id').value = work.id;
        document.getElementById('work-title').value = work.title;
        document.getElementById('work-description').value = work.description || '';
        document.getElementById('work-featured').checked = work.is_featured == 1;
        document.getElementById('old-media').value = work.media_path || '';
        $('#work-client-id').val(work.client_id).trigger('change');

        var type = work.type || 'banner';
        document.getElementById('work-type').value = type;
        toggleWorkType(type);

        if (type === 'video' && work.video_url) {
            document.querySelector('input[name="video_source"][value="url"]').checked = true;
            toggleVideoSource('url');
            document.getElementById('work-video-url').value = work.video_url;
        }

        // Show existing media preview
        if (work.media_path && type === 'banner') {
            document.getElementById('work-preview-img').src = work.media_path;
            document.getElementById('work-media-preview').style.display = 'block';
        }

        document.getElementById('work-submit-btn').name = 'update_work';
        document.getElementById('work-submit-btn').textContent = 'Update Work';
        $('#work-modal').modal('show');
    }

    function toggleWorkType(type) {
        var bannerInput = document.getElementById('work-media-input');
        var videoInput  = document.getElementById('work-video-input');
        if (type === 'video') {
            document.getElementById('banner-upload-section').style.display = 'none';
            document.getElementById('video-upload-section').style.display = 'block';
            bannerInput.disabled = true;
            bannerInput.value = '';
            videoInput.disabled = false;
        } else {
            document.getElementById('banner-upload-section').style.display = 'block';
            document.getElementById('video-upload-section').style.display = 'none';
            bannerInput.disabled = false;
            videoInput.disabled = true;
            videoInput.value = '';
        }
    }

    function toggleVideoSource(source) {
        var videoInput   = document.getElementById('work-video-input');
        var videoUrlInput = document.getElementById('work-video-url');
        if (source === 'url') {
            document.getElementById('video-file-section').style.display = 'none';
            document.getElementById('video-url-section').style.display = 'block';
            videoInput.disabled = true;
            videoInput.value = '';
            videoUrlInput.disabled = false;
        } else {
            document.getElementById('video-file-section').style.display = 'block';
            document.getElementById('video-url-section').style.display = 'none';
            videoInput.disabled = false;
            videoUrlInput.disabled = true;
            videoUrlInput.value = '';
        }
    }

    // Work media upload area
    const workUploadArea = document.getElementById('work-upload-area');
    const workMediaInput = document.getElementById('work-media-input');
    const workVideoArea  = document.getElementById('work-video-upload-area');
    const workVideoInput = document.getElementById('work-video-input');

    workUploadArea.addEventListener('click', function() { workMediaInput.click(); });
    workMediaInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('work-file-info').textContent = file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('work-preview-img').src = e.target.result;
                    document.getElementById('work-media-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    });

    workVideoArea.addEventListener('click', function() { workVideoInput.click(); });
    workVideoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) document.getElementById('work-video-file-info').textContent = file.name + ' (' + (file.size/1024/1024).toFixed(2) + ' MB)';
    });

    // ════ VIEW MORE ════════════════════════════════════════════════════

    function openViewMore(clientName, works) {
        document.getElementById('view-more-title').textContent = clientName + ' — All Works';
        var container = document.getElementById('view-more-container');
        container.innerHTML = '';
        works.forEach(function(w) {
            var media = '';
            if (w.type === 'banner' && w.media_path) {
                media = '<img src="' + w.media_path + '" style="width:100%;height:160px;object-fit:cover;border-radius:6px;">';
            } else if (w.type === 'video' && w.video_url) {
                media = '<div style="background:#1a1a2e;height:160px;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;flex-direction:column;"><i class="mdi mdi-play-circle-outline" style="font-size:48px;"></i><small>Embedded Video</small></div>';
            } else if (w.type === 'video' && w.media_path) {
                media = '<video style="width:100%;height:160px;border-radius:6px;" controls><source src="' + w.media_path + '" type="video/mp4"></video>';
            } else {
                media = '<div style="background:#e8e8e8;height:160px;border-radius:6px;display:flex;align-items:center;justify-content:center;"><i class="mdi mdi-image-off-outline" style="font-size:48px;color:#aaa;"></i></div>';
            }
            container.innerHTML += '<div class="col-md-4 mb-3"><div class="card work-card h-100">' + media +
                '<div class="card-body p-2"><h6 class="mb-1">' + (w.title||'') + '</h6>' +
                '<p class="text-muted mb-0" style="font-size:12px;">' + (w.description||'') + '</p>' +
                '<div class="d-flex gap-1 mt-2">' +
                '<button class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 8px;" onclick=\'editWork(' + JSON.stringify(w) + '); $("#view-more-modal").modal("hide");\'><i class="mdi mdi-pencil"></i> Edit</button>' +
                '<a href="edit_client_work.php?set_featured=' + w.id + '&client_id=' + w.client_id + '" class="btn btn-xs btn-outline-warning" style="font-size:11px;padding:2px 8px;" onclick="return confirm(\'Set as featured?\')"><i class="mdi mdi-star"></i> Feature</a>' +
                '<a href="edit_client_work.php?delete_work=' + w.id + '&client_id=' + w.client_id + '" class="btn btn-xs btn-outline-danger" style="font-size:11px;padding:2px 8px;" onclick="return confirm(\'Delete?\')"><i class="mdi mdi-delete"></i></a>' +
                '</div></div></div></div>';
        });
        $('#view-more-modal').modal('show');
    }
    </script>
</body>
</html>