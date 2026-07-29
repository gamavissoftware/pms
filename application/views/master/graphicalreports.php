<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="description" content="">

  <meta name="author" content="<?php echo copyright; ?>">
  <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
  <title><?php echo sitetitle; ?>Filter Reports</title>



  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">

  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <?PHP

  $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
  foreach ($q->result() as $LOGO);

  ?>

  <style>
    table.manglesh thead th {

      background: blue;

      color: #fff;

      font-weight: bold;

      text-align: center;

    }

    table.manglesh tbody td {
      text-align: center;
    }
  </style>

</head>

<body>

  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>

  <!-- End Navigation Bar-->



  <?php $this->load->view('common/info-section.php'); ?>

  <div class="wrapper">

    <div class="container">



      <!-- Page-Title -->

      <div class="row" style="margin-top:20px;">

        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

          <div class="page-title-box">

          


            <h4 class="page-title">Filter By</h4>

          </div>

        </div>

      </div>

      <!-- end page title end breadcrumb -->
      <?php
      $uri = $this->uri->segment(3);
      $startdate = $this->uri->segment(4);
      $enddate = $this->uri->segment(5);
      $dateone = date('d-m-Y', strtotime($startdate));
      $datetwo = date('d-m-Y', strtotime($enddate));
      ?>
      <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <div class="row">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
           
            <div class="card-box">
               <form action="<?php echo page_url; ?>Salesreporting/piechartfilterdataform" method="post">
              <div class="row">
               <div class="col-sm-1"></div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="field-1" class="control-label">Leads</label>
                      <span style="color:red;">*</span>
                      <select name="chartname" id="chartname" class="form-control">
                       
                        <?php if ($uri == 1) {?>
                        <option value="1" >Source Wise Leads</option>
                        <?php } ?>
                         <?php if ($uri == 2) {?>
                        <option value="2" <?php if ($uri == 2) echo "selected"; ?>>Source Of Conversion</option>
                      <?php } ?>

                       <?php if ($uri == 3) {?>
                        <option value="3" <?php if ($uri == 3) echo "selected"; ?>>Quotation to Conversion</option>
                        <?php } ?>

                        <?php if ($uri == 4) {?>
                        <option value="4" <?php if ($uri == 4) echo "selected"; ?>>High Selling Product</option>

                        <?php } ?>

                        <?php if ($uri == 5) {?>
                        <option value="5" <?php if ($uri == 5) echo "selected"; ?>>Unqualified Leads by Source</option>

                        <?php } ?>

                        <?php if ($uri == 6) {?>
                        <option value="6" <?php if ($uri == 6) echo "selected"; ?>>Unqualified Leads by Region</option>
                       
                        <?php } ?>

                        <?php if ($uri == 7) {?>
                        <option value="7" <?php if ($uri == 7) echo "selected"; ?>>Unqualified Leads by Person Wise</option>
                       
                       <?php } ?>


                        <?php if ($uri == 8) {?>
                        <option value="8" <?php if ($uri == 8) echo "selected"; ?>>Region Wise Enquiry</option>

                        <?php } ?>

                         <?php if ($uri == 9) {?>

                        <option value="9" <?php if ($uri == 9) echo "selected"; ?>>Region Wise Business</option>

                        <?php } ?>


                         <?php if ($uri == 10) {?>
                        <option value="10" <?php if ($uri == 10) echo "selected"; ?>>Industry Wise Leads</option>

                         <?php } ?>

                          <?php if ($uri == 11) {?>
                        <option value="11" <?php if ($uri == 11) echo "selected"; ?>>Top 5 Customer</option>

                         <?php } ?>
                          <?php if ($uri == 12) {?>

                        <option value="12" <?php if ($uri == 12) echo "selected"; ?>>Top Enquiry Product</option>
                      <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="field-1" class="control-label">START DATE</label>
                      <span style="color:red;">*</span>
                      <input type="text" name="startdate" id="startdate" <?php if ($startdate == '') { ?>value="dd-mm-yyyy" <?php } else { ?> value="<?php echo $dateone; ?>" <?php } ?> class="form-control datepicker">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="field-1" class="control-label">END DATE</label>
                      <span style="color:red;">*</span>
                      <input type="text" name="enddate" id="enddate" <?php if ($enddate == '') { ?>value="dd-mm-yyyy" <?php } else { ?> value="<?php echo $datetwo; ?>" <?php } ?> class="form-control datepicker">
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <button type="submit" style="margin-top: 25px; width:100%;" class="btn btn-info btn-sm">Submit</button>
                    </div>
                  </div>
                  <div class="col-sm-1"></div>
              </div>
              </form>
            </div>
          
          </div>

        </div>

      <div class="row">
        <?php
        if ($uri == 1) {
          $this->load->view('master/graph/source_wise_lead_pie');
        }; ?>
        <?php
        if ($uri == 2) {
          $this->load->view('master/graph/source_wise_lead_conversion_pie');
        }; ?>
        <?php
        if ($uri == 3) {
          $this->load->view('master/graph/qutoation_lead_conversion_pie');
        }; ?>
        <?php
        if ($uri == 4) {
          $this->load->view('master/graph/hotsaleingproduct');
        }; ?>
        <?php
        if ($uri == 5) {
          $this->load->view('master/graph/unqualifiedleads_bysource');
        }; ?>
        <?php
        if ($uri == 6) {
          $this->load->view('master/graph/unqualifiedleads_byregion');
        }; ?>
        <?php
        if ($uri == 7) {
          $this->load->view('master/graph/unqualifiedleads_byperson');
        }; ?>
        <?php
        if ($uri == 8) {
          $this->load->view('master/graph/enquery_by_region');
        }; ?>
        <?php
        if ($uri == 9) {
          $this->load->view('master/graph/enquery_by_business');
        }; ?>
        <?php
        if ($uri == 10) {
          $this->load->view('master/graph/industrywisebargraph');
        }; ?>
        <?php
        if ($uri == 11) {
          $this->load->view('master/graph/toptencustomerbargraph');
        }; ?>
        <?php
        if ($uri == 12) {
          $this->load->view('master/graph/topenquriesproductsbargraph');
        }; ?>
      </div>
      <!-- Footer -->

      <?php $this->load->view('common/footer'); ?>

      <!-- End Footer -->



    </div> <!-- end container -->




  </div>



  <!-- jQuery  -->


  <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

  <script src="<?php echo assets_url; ?>js/detect.js"></script>

  <script src="<?php echo assets_url; ?>js/fastclick.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>

  <script src="<?php echo assets_url; ?>js/waves.js"></script>

  <script src="<?php echo assets_url; ?>js/wow.min.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

  <!-- App js -->

  <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

  <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
  <script>
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy'
    });
  </script>



</body>

</html>