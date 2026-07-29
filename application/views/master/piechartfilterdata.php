<?php

$DI =& get_instance();
$DI->load->model('Dashboard_model', 'dashboardmodel');
// $mode=$DI->dashboardmodel->getsettings();
// if(count($mode)>0)
// {
// $mode=$mode['mode'];
// }else
// {
// $mode=1;
// }
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

 .border-left1{
            border-left: 3px solid;
            height: none;
            height: 40px;
            padding: 10px;
        }

        .border-left1 p{
            font-size:14px;
            line-height: 24px;
            color: black !important;
        }

        .border-left1 h2{
            font-weight: 600;
            margin: 0px;
        }

        .border-left1 h5{
            font-weight: 600;
            margin: 0px;
            font-size: 12px;
        }
.card-body{

    padding: 1.25rem;

    height: 95px;
    background-color: white !important;

}
  .light-blue{
            border-color: #dbf3fa !important;
        }

  .light-purple{
            border-color: #e9e9ff !important;
        }
        
        .light-green{
            border-color: #e1f2e1 !important;
        }

        .light-red{
            border-color: #fbe9ef !important;
        }

        .light-orange{
            border-color: #fff1ea !important;
        }

        .light-purple{
            border-color: #e9e9ff !important;
        }

        .light-yellow{
         border-color: #fff8dc !important;   
        }

        .light-grey{
            border-color: #d3d3d3;
        }

        .light-second-purple{
            border-color: #cb7cc070;
        }

        .light-light-blue{
            border-color: #1077da59;
        }

        .light-light-red{
            border-color: rgb(212, 162, 162);
        }

        .light-light-green{
            border-color: #7697004d;
        }

        .light-non{
            border-color: #08b2bb4a;
        }

        .light-agent{
            border-color: #ff020433;
        }

        .light-convert{
            border-color: #8cecff45;
        }

        .light-dis{
            border-color: #f7ff99;
        }

        .light-team{
            border-color: #3c49c64f;
        }

        .light-team-con{
            border-color: rgba(152,180,51,.2)!important;
        }

        .light-dis-team{
            border-color: hsla(0,73%,73%,.2)!important;
        }

    table.manglesh thead th {

      background: <?php echo $LOGO->colorcode; ?>;

      color: #fff;

      font-weight: bold;

      text-align: center;

    }

    table.manglesh tbody td {
      text-align: center;
    }
    a {
            color: black;
        }

        .customfont {
            font-size: 25px;
        }

        .card-box h4 {
            margin-top: 0;
            margin-bottom: 0;
            background: whitesmoke;
            padding: 5px;
            border-radius: 5px;
        }

        .underline-top {
            margin-top: 80px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #eee;
        }

        @media (max-width:576px) {
            .underline-top {
                margin-top: 15px;
            }
        }

        .underline-top1 {
            margin-top: 90px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #eee;
        }

        @media (max-width:576px) {
            .underline-top1 {
                margin-top: 120px;
            }
        }

        .card-box {
            padding: 0px;
            /* box-shadow: 1px 1px 10px lightgray; */
            /* box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%); */
            border: 1px solid lightgrey;
            -webkit-border-radius: 20px;
            border-radius: 5px;
            -moz-border-radius: 20px;
            background-clip: padding-box;
            margin-bottom: 7px;
            background-color: white !important;
            height: 100%;
            padding: 10px;
            margin-top: 15px;
        }

        .total-lead {
            background-color: #dbf3fa;
            height: 40px !important;
            width: 40px !important;
            border-radius: 50%;
        }

        .total-lead i {
            color: #4ac2e4;
            font-size: 25px;
            line-height: 40px;
        }

        .count-total p b {
            font-size: 20px;
        }

        .count-total p {
            margin-bottom: 0px !important;
            color: black;
            font-size: 12px;
        }

        .won-lead {
            background-color: rgba(152, 180, 51, .2) !important;
            height: 40px !important;
            width: 40px !important;
            border-radius: 50%;
        }

        .won-lead i {
            color: #98b433;
            font-size: 25px;
            line-height: 41px;
        }

        .dead-lead {
            background-color: hsla(0, 73%, 73%, .2) !important;
            height: 40px !important;
            width: 40px !important;
            border-radius: 50%;
        }

        .dead-lead i {
            color: #ec8686;
            font-size: 25px;
            line-height: 41px;
        }

        .un-lead {
            background-color: rgb(212 162 162 / 52%);
            height: 40px !important;
            width: 40px !important;
            border-radius: 50%;
        }

        .un-lead i {
            color: rgb(212 108 108);
            font-size: 25px;
            line-height: 40px;
        }

        .pi-lead {
            background-color: #d3d3d3;
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .pi-lead i {
            color: #000000;
            font-size: 20px;
            line-height: 40px;
        }

        .contact-lead {
            background-color: #fff8dc;
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .contact-lead i {
            color: #ffd012;
            font-size: 25px;
            line-height: 44px;
        }

        .open-lead {
            background-color: #d3d3d3;
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .open-lead i {
            color: #000000;
            font-size: 20px;
            line-height: 38px;
        }

        .follow-lead {
            background-color: #1077da2b;
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .follow-lead i {
            color: #1077da;
            font-size: 25px;
            line-height: 40px;
        }

        hr {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        @media (min-width: 992px) {
            .col-md-2 {
                width: 20% !important;
            }
        }

        @media (min-width: 992px) {
            .col-md-offset-1 {
                margin-left: 0% !important;
            }
        }

        /*-------------------------------------------------------------------------*/
        .tooltip {
            position: absolute;
            display: inline-block;
            right: 17px;
            top: 15px;
            z-index: 10 !important;
            /* border-bottom: 1px dotted black; */
            opacity: 1 !important;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 400px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            top: -30px;
            left: 160%;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 100%;
            margin-top: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent black transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
        }

        .tooltip1 {
            position: absolute;
            display: inline-block;
            right: 17px;
            top: 15px;
            /* border-bottom: 1px dotted black; */
            opacity: 1 !important;
        }

        @media (max-width:768px) {
            .tooltip .tooltiptext {
                display: none;
            }

            .tooltip {
                display: none;
            }

            .tooltip1 {
                display: none;
            }
        }

        .tooltip1 .tooltiptext {
            visibility: hidden;
            width: 400px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            top: -30px;
            right: 100%;
        }

        .tooltip1 .tooltiptext::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 100%;
            margin-top: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent black transparent transparent;
        }

        .tooltip1:hover .tooltiptext {
            visibility: visible;
        }

        .numberCircleproduction {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            padding: 9px 0px 0px 0px;
            background: #fff;
            border: 2px solid #8B97BD;
            color: #666;
            text-align: center;
            font-size: 14px;
            margin: auto;
        }

#shortcut p {
    text-align: center;
    font-size: 11px !important;
}
.card-body {
    border: 1px solid lightgrey;
    -webkit-border-radius: 20px;
    border-radius: 5px;
    -moz-border-radius: 20px;
    background-clip: padding-box;
    margin-bottom: 7px;
    background-color: white !important;
    height: 100%;
    padding: 16px;
    margin-top: 15px;
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

    <div class="container"><div class="col-md-12"><h2 class="text-center filter">Sales Analytics</h2>
    <div class="underline"></div>
  </div></div> <!-- end container -->


    <div class="container">
      <div class="row">
        <a href="<?php echo page_url;?>Salesreporting/reports/1/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
          <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>funnel.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
                <div class="border-left1 light-blue">
                  <h5>Source Wise Leads</h5>
                </div>
              </div>
            </div>


          </div>
        </div>
        </a>
        
     
        
        <a href="<?php echo page_url;?>Salesreporting/reports/3/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
          <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>quote_conver.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
                <div class="border-left1 light-red">
                  <h5>Quotation to Conversion</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>


       
      
      <a href="<?php echo page_url;?>Salesreporting/reports/5/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">

            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>unqual_lead.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
                <div class="border-left1 light-purple">
                  <h5>Unqualified Leads by Source</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </a>

    
   
 

    <!--   <a href="<?php echo page_url;?>Salesreporting/reports/11/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>top_customers.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-light-green">
              <h5>Top 5 Customer</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a> -->

   

  

 <a href="<?php echo page_url;?>Salesreporting/turn_around_time/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>trunaroundtime.png" style="width:40px">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Turn Around Time</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>
      </div>
      <div class="dashed"></div>
    </div>
    <div class="container"><div class="col-md-12"><h2 class="text-center filter">Userwise Comparison Analytics</h2>
    <div class="underline"></div>
  </div></div>
    <div class="container">
      <div class="row">
        <a href="<?php echo page_url;?>Piechart/comparisionfilterdata/1/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_handle_agent.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-agent">
              <h5>Leads Handled by Agents</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="<?php echo page_url;?>Piechart/comparisionfilterdata/2/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_convert_agent.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-convert">
              <h5>Leads Converted by Agents</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="<?php echo page_url;?>Piechart/comparisionfilterdata/3/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>dis_by_agent.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-dis">
              <h5>Discounts Given By Agents</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>
      </div>

     
      <div class="dashed"></div>

      </div>


      
       <div class="container"><div class="col-md-12"><h2 class="text-center filter">Teamwise Comparison Analytics</h2>
      <div class="underline"></div>
      </div></div>
      <div class="container">
      <div class="row">
        <a href="<?php echo page_url;?>Piechart/comparisionteamfilterdata/1/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_handle_team.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-team">
              <h5>Leads Handled by Teams</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="<?php echo page_url;?>Piechart/comparisionteamfilterdata/2/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_convert_team.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-team-con">
              <h5>Leads Converted by Teams</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="<?php echo page_url;?>Piechart/comparisionteamfilterdata/3/NA/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>dis_by_team.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-dis-team">
              <h5>Discounts Given By Teams</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>
      </div>
    </div>
  
 

  <div class="container">
<div class="dashed"></div>
    <div class="row">
                    <!-- <a href="<?php echo page_url; ?>Leads/order_management">
                        <div class="col-md-2 col-md-offset-1">
                            <div class="card-body" style="height:110px;" id="order_manage">
                            </div>
                        </div>
                    </a>
 -->

                    <a href="<?php echo page_url; ?>Leads/monthlysale">
                        <div class="col-md-2 col-md-offset-1">
                            <div class="card-body" style="height:110px;" id="monthsale">
                            </div>
                        </div>
                    </a>


                    <a href="<?php echo page_url; ?>Leads/yearlysale">
                        <div class="col-md-2">
                            <div class="card-body" style="height:110px;" id="yearlysale">

                            </div>
                        </div>
                    </a>
                    <a href="<?php echo page_url; ?>Leads/weeklysale">
                        <div class="col-md-2">
                            <div class="card-body" style="height:110px;" id="weeklywonorder">

                            </div>
                    </a>
                </div>
                <a href="<?php echo page_url; ?>Leads/closurepending">
                    <div class="col-md-2">
                        <div class="card-body" style="height:110px;" id="closurepending">

                        </div>
                    </div>
                </a>
                <a href="javascript:;">
                    <div class="col-md-2">
                        <div class="card-body" style="height:110px;" id="averageday">

                        </div>
                    </div>
                </a>

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
 $(document).ready(function() { 
$.ajax({
                url: '<?php echo page_url; ?>Fetch_dynamic_data/getmonthlysale',
                type: 'get',
                success: function(data) {
                    $("#monthsale").html(data);
                    // ddata = data.split('|');
                    // $('#monthlysale').html(ddata[0] + " " + ddata[2]);
                    // $("#monthsalecount").html(ddata[1] + " Order(s)");
                }
            });
            $.ajax({
                url: '<?php echo page_url; ?>Fetch_dynamic_data/getyearlysale',
                type: 'get',
                success: function(data) {
                    $("#yearlysale").html(data);
                    // ddata = data.split('|');
                    // $('#monthlysale').html(ddata[0] + " " + ddata[2]);
                    // $("#monthsalecount").html(ddata[1] + " Order(s)");
                }
            });
            $.ajax({
                url: '<?php echo page_url; ?>Fetch_dynamic_data/getweeklysale',
                type: 'get',
                success: function(data) {
                    $("#weeklywonorder").html(data);

                }
            });
            $.ajax({
                url: '<?php echo page_url; ?>Fetch_dynamic_data/closurepending',
                type: 'get',
                success: function(data) {
                    $("#closurepending").html(data);

                }
            });
            $.ajax({
                url: '<?php echo page_url; ?>Fetch_dynamic_data/averagedays',
                type: 'get',
                success: function(data) {
                    $("#averageday").html(data);
                }
            });

});
  </script>



</body>

</html>