
  <!-- Start Content-->
  <div class="container-fluid">
    <div class="row" style="width:100%">
      <div class="col-md-6">
        <div class="card" style="height: 90%;">
          <div class="card-body">
              
            <h4 class="header-title mt-0 mb-4">Team Members</h4>
            <div class="widget-chart-1">
              <div class="widget-chart-box-1 float-start" dir="ltr">
                <input data-plugin="knob" data-width="70" data-height="70" data-fgcolor="#f05050" data-bgcolor="#F9B9B9" value="100" data-skin="tron" data-angleoffset="180" data-readonly="true" data-thickness=".15">
              </div>
              <div class="widget-detail-1 text-end">
                <h2 class="fw-normal pt-2 mb-1"><?php echo $total_count; ?></h2>
                <p class="text-muted mb-1" style="font-size: 12px;"><?php echo $total_amount; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div><!-- end col -->

      <div class="col-md-6">
        <div class="card" style="height: 90%;">
          <div class="card-body">
            <h4 class="header-title mt-0 mb-3">Total Clients</h4>
            <div class="widget-box-2">
              <div class="widget-detail-2 text-end">
                <span class="badge bg-success rounded-pill float-start mt-3"><?php echo $percentage1a; ?>%</span>
                <h2 class="fw-normal mb-1"><?php echo $approved_count; ?></h2>
                <p class="text-muted mb-1" style="font-size: 12px;"><?php echo $total_approved_amount; ?></p>
              </div>
              <div class="progress progress-bar-alt-success progress-sm">
                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?php echo $percentage1a ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentage1a?>%;">
                  <!-- <span class="visually-hidden">77% Complete</span> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- end col -->

      <div class="col-md-6">
        <div class="card" style="height: 90%;">
          <div class="card-body">
            <h4 class="header-title mt-0 mb-4">Active Blogs</h4>
            <div class="widget-chart-1">
              <div class="widget-chart-box-1 float-start" dir="ltr">
                <input data-plugin="knob" data-width="70" data-height="70" data-fgcolor="#ffbd4a" data-bgcolor="#FFE6BA" value="<?php echo $percentage1b; ?>" data-skin="tron" data-angleoffset="180" data-readonly="true" data-thickness=".15">
              </div>
              <div class="widget-detail-1 text-end">
                <h2 class="fw-normal pt-2 mb-1"><?php echo $pending_count; ?></h2>
                <p class="text-muted mb-1" style="font-size: 12px;"><?php echo $total_pending_amount; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div><!-- end col -->
      <div class="col-md-6">
        <div class="card" style="height: 90%;">
          <div class="card-body">
              
            <h4 class="header-title mt-0 mb-3">Scanned Visits</h4>
            <div class="widget-box-2">
              <div class="widget-detail-2 text-end">
                <span class="badge bg-purple rounded-pill float-start mt-3"><?php echo $percentageqc; ?>%</span>
                <h2 class="fw-normal mb-1"><?php echo $disbursed_count; ?></h2>
                <p class="text-muted mb-1" style="font-size: 12px;"><?php echo $total_disbursed_amount; ?></p>
              </div>
              <div class="progress progress-bar-alt-purple progress-sm">
                <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="<?php echo $percentageqc; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentageqc?>%;">
                  <!-- <span class="visually-hidden">77% Complete</span> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- end col -->
      <!-- <div class="col-md-6">
        <div class="card" style="height: 90%;">
          <div class="card-body">
              
            <h4 class="header-title mt-0 mb-3">Repaid Loans</h4>
            <div class="widget-box-2">
              <div class="widget-detail-2 text-end">
                <span class="badge bg-pink rounded-pill float-start mt-3"><?php echo $percentage1; ?>%</span>
                <h2 class="fw-normal mb-1"><?php echo $paid_count; ?></h2>
                <p class="text-muted mb-1" style="font-size: 12px;"><?php echo $total_paid_amount; ?></p>
              </div>
              <div class="progress progress-bar-alt-pink progress-sm">
                <div class="progress-bar bg-pink" role="progressbar" aria-valuenow="<?php echo $percentage1; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percentage1?>%;">
                  <span class="visually-hidden">77% Complete</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    
    
    
    
    <!-- end row -->
    
    </div><!-- end row -->
  </div><!-- container-fluid -->