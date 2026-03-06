 <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
                  <!-- Signup modal content -->
                  <div id="signup-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
    
                                                    <div class="modal-body">
                                                        <div class="text-center mt-2 mb-4">
                                                            <div class="auth-logo">
                                                                <div class="logo logo-light">
                                                                    <span class="logo-lg">
                                                                        <img src="assets/images/usalama_logo.png" alt="" height="50">
                                                                    </span>
                                                                </div>
                                            
                                                                <div class="logo logo-dark">
                                                                    <span class="logo-lg">
                                                                        <img src="assets/images/usalama_logo.png" alt="" height="50">
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
    
                                                        <form class="px-3" action="edit_users.php?" method="post" enctype="multipart/form-data">
    
                                                            <div class="mb-3">
                                                                <label for="username" class="form-label">Name</label>
                                                                <input class="form-control" type="text" id="username" name="name" required="" placeholder="Name">
                                                            </div>
    
                                                            <div class="mb-3">
                                                                <label for="emailaddress" class="form-label">Email address</label>
                                                                <input class="form-control" type="email" id="emailaddress" name="email" required="" placeholder="Email">
                                                            </div>
    
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">User Type</label>
                                                                <select name="type" id="type"> 
                                                                <option value="">select</option>
                                                                <option value="admin" selected>Admin</option> 
                                                                <!-- <option value="client">Client</option>  -->
                                                                 </select> 
                                                            </div>
                                                            <div class="mb-3">
                                                        
                                                        <label for="username" class="form-label">Company Name</label>
                                                         <select name="company" id="type" class="form-select">
                                                         <option value="">Select</option> 
                                                         <?php
                                                        $sql = "SELECT * FROM company";
                                                        $data = mysqli_query($con, $sql);
                                                        if (mysqli_num_rows($data) > 0) {
                                                             while($row = mysqli_fetch_assoc($data)) {?>
                                                        <option value="<?php echo $row['id']?>"><?php echo $row['name']?></option> 
                                                       <?php }}
                                                        ?>
                                                         </select> 
                                                    </div>
    
                                                            <div class="mb-3 text-center">
                                                                <button class="btn btn-primary" type="submit" name="add_user">Add User</button>
                                                            </div>
    
                                                        </form>
    
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->
            <div class="content">

<!-- Start Content-->
<div class="container-fluid">


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if($_SESSION['type']=='admin'):?>
                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#signup-modal">Add Users</a>
                                                <!-- item-->
                                                <!-- item-->
                                            </div>
                                        </div>
                    <?php endif ?>
                

                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Editor Type</th>
                            <?php if($_SESSION['type']=='admin'):?>
                            <th>Options</th>
                            <?php endif ?>
                        </tr>
                        </thead>


                        <tbody>
                            <?php
                       $sql = "SELECT * FROM editors";
                       $data = mysqli_query($con, $sql);
                       if (mysqli_num_rows($data) > 0) {
                        // output data of each row
                        while($row = mysqli_fetch_assoc($data)) {
                            echo "<tr>
                            <td>" . $row["name"]. "</td>
                            <td>" . $row["email"]. "</td>
                            <td>" . $row["type"]. "</td>";?>
                            <?php if($_SESSION['type']=='admin'):?>
                            <td> <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editmodal<?php echo $row['id']; ?>">Edit</a>

                            <!-- item-->
                            <a href="" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#danger-alert-modal<?php echo $row['id']; ?>">Delete</a>
                            <!-- item-->
                        </div>
                    </div></td>
                    <?php endif ?>
                </tr>

                    <div id="editmodal<?php echo $row['id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="text-center mt-2 mb-4">
                                    <div class="auth-logo">
                                        <div class="logo logo-light">
                                            <span class="logo-lg">
                                                <img src="assets/images/usalama_logo.png" alt="" height="50">
                                            </span>
                                        </div>

                                        <div class="logo logo-dark">
                                            <span class="logo-lg">
                                                <img src="assets/images/usalama_logo.png" alt="" height="50">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <form class="px-3" action="edit_users.php?id=<?php echo $row['id']; ?>" method="post" enctype="multipart/form-data">

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="username" name="name" required="" value="<?php echo $row['name']; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label">Email address</label>
                                        <input class="form-control" type="email" id="emailaddress" name="email" required="" value="<?php echo $row['email']; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">User Type</label>
                                        <?php
$eid = $row['id'];                                      // Retrieve current value from the database
$sqlc = "SELECT type FROM editors WHERE id = $eid";
$result = mysqli_query($con, $sqlc);
$row2 = mysqli_fetch_assoc($result);
$current_value = $row2['type'];
?>

<select name="type" id="type">
  <option value="admin" <?php if ($current_value == 'admin') echo 'selected'; ?>>Admin</option>
  <option value="client" <?php if ($current_value == 'client') echo 'selected'; ?>>Client</option>
</select>

                                    </div>
                                    <div class="mb-3">

                                    <label for="username" class="form-label">Company Name</label>
<?php
$eid = $row['id'];
$sqlc = "SELECT company_id FROM editors WHERE id = $eid";
$result = mysqli_query($con, $sqlc);
$row1c = mysqli_fetch_assoc($result);
$company_id = $row1c['company_id'];

$current_value = '';
if ($company_id != null) {
    $sqlc = "SELECT name FROM company WHERE id = $company_id";
    $result = mysqli_query($con, $sqlc);
    $row1c = mysqli_fetch_assoc($result);
    $current_value = $row1c['name'];
}
?>
<select name="company" id="type" class="form-select">
    <?php if ($current_value == '' || $company_id == null) { ?>
        <option value="" selected>Select a company</option>
    <?php } else { ?>
        <option value="<?php echo $company_id; ?>" selected><?php echo $current_value; ?></option>
    <?php } ?>
    <?php
    $sql2 = "SELECT * FROM company";
    $data2 = mysqli_query($con, $sql2);
    if (mysqli_num_rows($data2) > 0) {
        while($row2 = mysqli_fetch_assoc($data2)) {?>
            <option value="<?php echo $row2['id']; ?>" <?php if ($current_value == $row2['name']) echo 'selected'; ?>> <?php echo $row2['name']; ?> </option>
    <?php } } ?>
</select>

                                                    </div>

                                    <div class="mb-3 text-center">
                                        <button class="btn btn-primary" type="submit" name="update_user">Edit User</button>
                                    </div>

                                </form>

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <div id="danger-alert-modal<?php echo $row['id']?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content modal-filled bg-danger">
                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <i class="dripicons-wrong h1 text-white"></i>
                                                            <h4 class="mt-2 text-white">Delete</h4>
                                                            <p class="mt-3 text-white">Would You Like to Delete the User<br>
                                                            <b><?php echo $row['name'] ?></b><br>
                                                            of Type<br>
                                                            <b><?php echo $row['type'] ?></b>
                                                        </p>
                                                            <a href="edit_users.php?delete_user=<?php echo  $row['id']?>" type="button" class="btn btn-light my-2">Delete</a>
                                                        </div>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                     
                            <?php   }
                    }?>
                        </tbody>
                    </table>
                </div>
            </div>
           
        </div>
    </div>
    <!-- end row -->
    
</div> <!-- container-fluid -->

</div> <!-- content -->

<!-- Footer Start -->

<!-- end Footer -->

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->