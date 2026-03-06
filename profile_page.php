
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="card">
                                <div class="bg-picture card-body">
                                    <div class="d-flex align-items-top">
                                        <img src="assets/images/user.png"
                                                class="flex-shrink-0 rounded-circle avatar-xl img-thumbnail float-start me-3" alt="profile-image">

                                        <div class="flex-grow-1 overflow-hidden">
                                            <h4 class="m-0"><?php echo $user ?></h4>
                                            <p class="text-muted"><?php echo sentenceCase($type); ?></p>

                                            <ul class="social-list list-inline mt-3 mb-0">
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);" class="social-list-item border-purple text-purple"><i
                                                            class="mdi mdi-facebook"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i
                                                            class="mdi mdi-google"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);" class="social-list-item border-info text-info"><i
                                                            class="mdi mdi-twitter"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);" class="social-list-item border-secondary text-secondary"><i
                                                            class="mdi mdi-github"></i></a>
                                                </li>
                                            </ul>
        
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                                <!--/ meta -->

                            </div>
                            <?php if($_SESSION['type']=='admin'):?>
                            <div class="col-sm-4">
                                <div class="card">
                                <div class="card-body">

                                    <h4 class="header-title mt-0 mb-3">My Team Members</h4>

                                    <ul class="list-group mb-0 user-list">
                                    <?php
                       $sql = "SELECT * FROM editors";
                       $data = mysqli_query($con, $sql);
                       if (mysqli_num_rows($data) > 0) {
                        // output data of each row
                            while($row = mysqli_fetch_assoc($data)) {?>
                             <li class="list-group-item">
                                            <a href="#" class="user-list-item">
                                                <div class="user avatar-sm float-start me-2">
                                                    <img src="assets/images/user.png" alt="" class="img-fluid rounded-circle">
                                                </div>
                                                <div class="user-desc">
                                                    <h5 class="name mt-0 mb-1"><?php echo $row['name']; ?></h5>
                                                    <p class="desc text-muted mb-0 font-12"><?php echo sentenceCase($row['type']); ?></p>
                                                </div>
                                            </a>
                                        </li>
                           <?php }
                            
                        }?>                                
                                    </ul>
                                </div>
                                </div>
                            </div>
                        <?php endif ?>
                        </div>    
                    </div> <!-- container -->

                </div> <!-- content -->

         