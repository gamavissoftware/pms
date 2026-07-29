<?php $user_id =$this->session->userdata['logged_in']['user_id'];
$CI =& get_instance();
$CI->load->model('Lead_model','leadmodel');
$DI =& get_instance();
$DI->load->model('Salescrm_model','salescrm');
$calculate_fiscal_year_for_date=$CI->leadmodel->get_finacial_year_range();
$st = date('Y-m-d',strtotime($calculate_fiscal_year_for_date['start_date']));
$edate = date('Y-m-d',strtotime($calculate_fiscal_year_for_date['end_date']));
$getHpclCompanies = $DI->salescrm->getHpclCompanies();
?>
<!DOCTYPE html>
<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Sales Report</title>





        <!-- Table Responsive css -->


		<script src="<?php echo assets_url;?>js/angular.min.js"></script>


		 <!-- DataTables -->


        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />


		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />


		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->


        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->


        <!--[if lt IE 9]>


        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>


        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>


        <![endif]-->





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<?PHP 
//$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
//foreach($q->result() as $LOGO);
?>

    <style>


table.manglesh thead th {


				background: red;


				color:#fff;


				font-weight:bold;


			}


			


#pageloader


{


  background: rgba( 255, 255, 255, 0.8 );


  display: none;


  height: 100%;


  position: fixed;


  width: 100%;


  z-index: 9999;


}


#pageloader img


{


  left: 50%;


  margin-left: -32px;


  margin-top: -32px;


  position: absolute;


  top: 50%;


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
  .light-blue{
            border-color: #dbf3fa !important;
        }

  .light-purple{
            border-color: #e9e9ff !important;
        }



</style>


	</head>








    <body>








        <!-- Navigation Bar-->


                <header id="topnav">


          <?php $this->load->view('common/nav-menu');?>


        </header>


        <!-- End Navigation Bar-->

    

<div class="wrapper">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Sales & Product Reports</h1><hr>
</div>

  <div >
        <div class="row">

            <a href="<?php echo page_url;?>Salesreporting/sales_agent_report/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/ALL">
          <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>funnel.png">
              </div>
              <div class="col-sm-10 col-xs-10 ">
                <div class="border-left1 light-blue">
                  <h5>Sales Person Wise Reporting</h5>
                </div>
              </div>
            </div>


          </div>
        </div>
        </a>

      
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

       <a href="<?php echo page_url;?>Sales_stats_reporting/sales_order_stats">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>high_selling.png" style="width:40px">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Sales Report (Yearly/Monthly)</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

       <a href="<?php echo page_url;?>Sales_stats_reporting/most_selling_product/<?php echo $st;?>/<?php echo $edate;?>/ALL">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
               <i class="fa fa-bitbucket-square" style="font-size:40px"></i>
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Product Wise QTY Sales Report (Consolidated)</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

       <a href="<?php echo page_url;?>Sales_stats_reporting/customer_wise_product_consumption">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
               <i class="fa fa-users" style="font-size:40px"></i>
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Product Consumption Stats (Customer Wise)</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>

        <a href="<?php echo page_url;?>Sales_stats_reporting/product_comparision_report">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_convert_agent.png" style="width:40px">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Product Comparison Report</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>


       <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','104')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>

         <a href="<?php echo page_url;?>Sales_stats_reporting/salesperformance/<?php echo date('Y-m-01',strtotime('-1 Months'));?>/<?php echo date('Y-m-01');?>/ALL">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card-body">
            
            <div class="row">
              <div class="col-sm-2 col-xs-2 ">
                <img src="<?php echo dashboard_icon; ?>lead_convert_agent.png" style="width:40px">
              </div>
              <div class="col-sm-10 col-xs-10 ">
              <div class="border-left1 light-non">
              <h5>Sales Agent Performance</h5>
            </div>
              </div>
            </div>
          </div>
        </div>
      </a>
      <?php 
        }
        ?>



   

          
        </div>
        </div>

        <div style="margin-top: 20px; display:none">
        <div class="dashboard-header" >
        <h1>Customer Reports</h1><hr>
        </div>

        <div class="row" style="display:none">
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Sales_stats_reporting/customer_timeline
        " target="_blank"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-user " style="font-size: 39px;"></i>
        </div>
        <p>CUSTOMER TIMELINE</p>
        </div></a>
        </div>

        <?php 
            $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','91')->where('submodule_access','1')->get();
            if($qry->num_rows()>0){
                                    ?>
        <?php if($getHpclCompanies != '') {
                foreach($getHpclCompanies as $row2) {?>
            <div class="col-sm-4 col-md-4 col-lg-2" style="display:none">
            <a href="<?php echo page_url;?>Billing/billing_from_hpclcompany/<?php echo $row2->id;?>/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>" target="_blank"><div class="report-box">
            <div class="text-center" style="margin-bottom: 10px;">
            <i class="fa fa-line-chart" style="font-size: 39px;"></i>
            </div>
            <p><?php echo $row2->companyname;?></p>
            </div></a>
            </div>
        <?php } } }?>



        </div>


         <div style="margin-top: 20px;">
        <div class="dashboard-header" style="display:none">
        <h1>TDS Input Reports</h1><hr>
        </div>
        <div class="row">
        <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','93')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        if($getHpclCompanies != '') {
        foreach($getHpclCompanies as $row2) {?>
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2" style="display:none">
        <a href="<?php echo page_url;?>Billing/tds_input_report/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>/ALL/<?php echo $row2->id;?>" target="_blank"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-files-o" style="font-size: 39px;"></i>
        </div>
        <p><?php echo $row2->companyname;?> TDS INPUT REPORT</p>
        </div></a>
        </div>
        <?php 
        }
        }
        }
        ?>
        </div>
        </div>



        <div style="margin-top: 20px;">
        <div class="dashboard-header">
        <h1>Ledgers Reports</h1><hr>
        </div>
        <div class="row">
        <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','100')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
       <!--  <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Billing/hpcl_ledger/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>" target="_blank"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-files-o" style="font-size: 39px;"></i>
        </div>
        <p>SUNDER IND OIL HPCL LEDGER</p>
        </div></a>
        </div> -->

        <?php 
        }
        ?>
        </div>
        </div>


        <div style="margin-top: 20px;">
        <div class="dashboard-header">
        <h1>CRM Reports</h1><hr>
        </div>
        <div class="row">
        <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','101')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Daily_report/report/<?php echo date('Y-m-d');?>"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-files-o" style="font-size: 39px;"></i>
        </div>
        <p>Daily Reports</p>
        </div></a>
        </div>

        <?php 
        }
        ?>

                <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','102')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Daily_report/monthly_report/<?php echo date('Y-m-01');?>/<?php echo date('Y-m-t');?>"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-files-o" style="font-size: 39px;"></i>
        </div>
        <p>Monthly Reports</p>
        </div></a>
        </div>

        <?php 
        }
        ?>

         <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','103')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Sales_stats_reporting/salesfunnel/<?php echo date('Y-m-01',strtotime('-1 Months'));?>/<?php echo date('Y-m-01');?>"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-list" style="font-size: 39px;"></i>
        </div>
        <p>Sales Progression</p>
        </div></a>
        </div>

        <?php 
        }
        ?>


          <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','104')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Sales_stats_reporting/salesperformance/<?php echo date('Y-m-01',strtotime('-1 Months'));?>/<?php echo date('Y-m-01');?>/ALL"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-users" style="font-size: 39px;"></i>
        </div>
        <p>Sales Agent Performance</p>
        </div></a>
        </div>

        <?php 
        }
        ?>

         <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','107')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Sales_stats_reporting/typewisereport/"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-users" style="font-size: 39px;"></i>
        </div>
        <p>ESTIMATED TYPE REPORTS</p>
        </div></a>
        </div>

        <?php 
        }
        ?>


        <?php 
        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','11')->where('submoduleid','110')->where('submodule_access','1')->get();
        if($qry->num_rows()>0){
        ?>
        <div class="col-sm-4 col-md-4 col-lg-2">
        <a href="<?php echo page_url;?>Sales_stats_reporting/finaltypewisereport/"><div class="report-box">
        <div class="text-center" style="margin-bottom: 10px;">
        <i class="fa fa-users" style="font-size: 39px;"></i>
        </div>
        <p>TYPE REPORTS (FINAL)</p>
        </div></a>
        </div>

        <?php 
        }
        ?>





        
        </div>
        </div>   
    </div>
</div>




                <!-- Footer -->


               <?php $this->load->view('common/footer');?>


                <!-- End Footer -->





            </div> <!-- end container -->


        </div>


        <!-- end wrapper -->








                <!-- jQuery  -->


        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>


        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>js/detect.js"></script>


        <script src="<?php echo assets_url;?>js/fastclick.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>


        <script src="<?php echo assets_url;?>js/waves.js"></script>


        <script src="<?php echo assets_url;?>js/wow.min.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>





        <!-- Datatables-->


        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


		
    <script>
        
        $( document ).ready(function(){
            $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/1',
                type: 'get',
                success: function(data){
                    $('#sales_visit').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/2',
                type: 'get',
                success: function(data){
                    $('#daily_update').attr('data-badge', data);
                }
            });
            
             $.ajax({
                url: '<?php echo page_url;?>Fetch_dynamic_data/getsalesreportingcount/3',
                type: 'get',
                success: function(data){
                    $('#payment_collection').attr('data-badge', data);
                }
            });
        });
 
    </script>

</body>


</html>