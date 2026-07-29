<?php

$DI =& get_instance();
$DI->load->model('Dashboard_model', 'dashboardmodel');
$mode=$DI->dashboardmodel->getsettings();
if(count($mode)>0)
{
$mode=$mode['mode'];
}else
{
$mode=1;
}

$getAllLeadStages = $DI->dashboardmodel->getAllFunnelLeadStages();
?>

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
  <link href="<?php echo assets_url; ?>css/funnel.css" rel="stylesheet" type="text/css" />

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

      background: <?php echo $LOGO->colorcode; ?>;

      color: #fff;

      font-weight: bold;

      text-align: center;

    }

    table.manglesh tbody td {
      text-align: center;
    }
  </style>
  <style>

        .box {
        float: left;
        height: 20px;
        width: 20px;
        margin-bottom: 15px;
        border: 1px solid black;
        clear: both;
        }

        .quotation
        {
            background-color:#9cc925;
        }

        @media (max-width:576px){
            .mobile-resp{
                overflow-y: auto;
            }

            .funnel_outer ul {
    margin: 0;
    padding: 0;
    margin-left: -80px;
    margin-right: 81px;
}
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

    <div class="container"><div class="col-md-12"><h2 class="text-center filter">Consolidated Sales Funnel</h2>
    <div class="underline"></div>
  </div></div> <!-- end container -->


    <div class="container">


      <div class="row" style="margin-top:20px;">

        <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

          <div class="page-title-box">

            <!-- <div class="btn-group pull-right">

              <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">ADD NEW DEPARTMENT</button>

                               

                            </div> -->




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
             <!--  <div class="row">
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

        </div> -->

      <div class="row card-box">
        <div class="col-md-4"></div>
        <div class="col-md-4" style="padding:0px;">
<div class="mobile-resp">
        <div class="funnel_outer" style="margin-top: 45px;">
        <ul>

    <?php if(count($getAllLeadStages)>0) {
    $i=1;
    foreach($getAllLeadStages as $row1) {
     $data=$DI->dashboardmodel->getleadsandcount($row1->lead_id);
     if(count($data)>0)
     {

        $count=$data['count'];
        $quoteprice=$data['quotedprice'];
        if($quoteprice>0)
        {
          $quoteprice=$DI->dashboardmodel->moneyFormatIndia($quoteprice);
        }else
        {
          $quoteprice='';
        }

     }
    ?>
    <li class="funnel_step_<?php echo $i;?>"><a href="<?php echo page_url;?>Leads/lead_stages/<?php echo $row1->lead_id;?>" target="_blank"><span><p><?php echo $row1->lead_name;?> - <?php echo $count;?><br><?php echo $quoteprice;?></p></span></a></li>
    <?php $i++;
      } }?>

        </ul>
        </div>
                </div>
        </div>
        
    </div>
    
      </div>


     
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