
<div class="navbar-custom">
                    <ul class="list-unstyled topnav-menu float-end mb-0">

                        <li class="dropdown notification-list topbar-dropdown">
                            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="assets/images/user.png" alt="user-image" class="rounded-circle">
                                <span class="pro-user-name ms-1">
                                    <?php echo $user ?><i class="mdi mdi-chevron-down"></i> 
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                                <!-- item-->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>
    
                                <!-- item-->
                                <!-- <a href="profile.php" class="dropdown-item notify-item">
                                    <i class="fe-user"></i>
                                    <span>My Account</span>
                                </a> -->
    
                                <!-- item-->
                                <a href="lock-screen.php" class="dropdown-item notify-item">
                                    <i class="fe-lock"></i>
                                    <span>Lock Screen</span>
                                </a>
    
                                <div class="dropdown-divider"></div>
    
                                <!-- item-->
                                <a href="logout.php" class="dropdown-item notify-item">
                                    <i class="fe-log-out"></i>
                                    <span>Logout</span>
                                </a>
    
                            </div>
                        </li>    
                    </ul>
    
                    <!-- LOGO -->
                    <div class="logo-box" >
                        <a href="index.php" class="logo logo-light text-center">
                            <span class="logo-sm">
                                <img src="assets/images/tls_logo.png" alt="" height="50">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/tls_logo.png" alt="" height="50">
                            </span>
                        </a>
                        <a href="index.php" class="logo logo-dark text-center">
                            <span class="logo-sm">
                                <img src="assets/images/tls_logo.png" alt="" height="50">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/tls_logo.png" alt="" height="50">
                            </span>
                        </a>
                    </div>

                    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
                        <li>
                            <button class="button-menu-mobile disable-btn waves-effect">
                                <i class="fe-menu"></i>
                            </button>
                        </li>
    
                        <li>
                            <h4 class="page-title-main"><?php echo $title; ?></h4>
                        </li>
            
                    </ul>

                    <div class="clearfix"></div> 
               
            </div>

<!--end topBar-->