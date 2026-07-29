<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
         <meta name="author" content="<?php //echo copyright; ?>">
         <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php //echo sitetitle; ?>Purchase Report</title>
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
<h1>Purchase Dashboard</h1><hr>
</div>

  <div >
        <div class="row ">
        
     
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/indent_form">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>indent.png">
                        </div>
                            <p> Create Indent</p>
                    </div>
                </a>
            </div>
       

       
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/pending_indent_dashboard">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>pending_indent.png">
                        </div>
                            <p> Pending Indent(s) </p>
                    </div>
                </a>
            </div>
       

            <!--<div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/masterrequest">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>item_master.png">
                        </div>
                            <p>Item Master Request </p>
                    </div>
                </a>
            </div>-->
     
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/createbulkpo">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>pr_po.png">
                        </div>
                            <p> Pending PR for PO Genaration</p>
                    </div>
                </a>
            </div>
       
      
           <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/pendingpoforapproval_change">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>po_approval.png">
                        </div>
                            <p>  Pending PO for Approval Request</p>
                    </div>
                </a>
            </div>
       
            <!--<div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/pendingpoforpayment">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>pending_payment.png">
                        </div>
                            <p>Pending Payments</p>
                    </div>
                </a>
            </div>
        
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/emailnotifications">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>pending_email.png">
                        </div>
                            <p>Pending Emails</p>
                    </div>
                </a>
            </div>
       
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/followup_dashboard">
                    <div class="report-box badge1" data-badge="0">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>po_follow.png">
                        </div>
                            <p>PO Follow-up </p>
                    </div>
                </a>
            </div>
        
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/po_delayed_report">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>po_delay.png">
                        </div>
                            <p>PO Delayed Report </p>
                    </div>
                </a>
            </div>
        
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/criticalitems">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>critical.png">
                        </div>
                            <p>Critical Items</p>
                    </div>
                </a>
            </div>
       
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/closedpo">
                    <div class="report-box">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>po_closed.png">
                        </div>
                            <p>Closed PO</p>
                    </div>
                </a>
            </div>
        
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Store/field_boy_scheduler_dashboard
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>field_boy.png">
</div>
           <p>Field boy scheduler dashboard</p>
                </div></a>
            </div>-->

            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/rejected_po_report
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>po_reject.png">
</div>
           <p>Rejected PO Report</p>
                </div></a>
            </div>

     <!--<div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/pendingordersreport
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pending_order.png">
</div>
           <p>Pending Order Dashboard</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/indent_vs_pr_report
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>indent_pr.png">
</div>
           <p>Indent VS PR Report</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/repeated_item_report
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase_request.png">
</div>
           <p>Repeated Items Purchase Report</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/filterbyvendor
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>vendorwise.png">
</div>
           <p>Vendorwise Purchase Report</p>
                </div></a>
            </div>
     <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/pendingpoconsolidated
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>consol.png">
</div>
           <p>Consolidated PO</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/freight_approval_dashboard
"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>freight.png">
</div>
           <p> Freight Charges Dashboard</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/pr_vs_po_report
"><div class="report-box badge1" data-badge="0">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pr_po.png">
</div>
           <p>PR vs PO Report</p>
                </div></a>
            </div>

            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Store/anytime_rejection_purchase_dashboard

"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>item.png">
</div>
           <p> Anytime Rejection Purchase Dashboard</p>
                </div></a>
            </div>
            
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Store/closedpo

"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>purchase_request.png">
</div>
           <p>Closed PO Report</p>
                </div></a>
            </div>
          
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Store/min_max

"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>max.png">
</div>
           <p>Update Bulk Min Max (MIS)</p>
                </div></a>
            </div>
           
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/pohistory

"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>anytime_rejection.png">
</div>
           <p>Rejected PO History</p>
                </div></a>
            </div>-->
            
            <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/itemmrn"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pending_order.png">
</div>
           <p>Pending MRN Request(s) </p>
                </div></a>
            </div>


               <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Reporting/gateentry">
                    <div class="report-box badge1" data-badge="0" id="qcreport">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>quality_check.png">
                        </div>
                            <p>QC</p>
                    </div>
                </a>
            </div>

              <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/storereciept">
                    <div class="report-box badge1" data-badge="0" id="storereceipt">
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>receipt.png">
                        </div>
                            <p>STORE RECIEPT</p>
                    </div>
                </a>
            </div>

             <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Store/unacknowledgeditems
"><div class="report-box badge1" data-badge="0" id="materialissuedbutnotupdated">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>update.png">
</div>
           <p>Material Issued not updated for store</p>
                </div></a>
            </div>
            
			<!-- <div class="col-sm-4 col-md-4 col-lg-2">
            <a href="<?php echo page_url;?>Reporting/accountsmrncheque/"><div class="report-box">
<div class="text-center">
                <img src="<?php echo dashboard_icon;?>pending_order.png">
</div>
           <p>Cheque Made </p>
                </div></a>
            </div>-->
									
        </div>

        </div>

    <!-- <div id="person" class="tabcontent">
        <h3>Responsible Person</h3>
       
    </div>



    <script>
        function openPage(pageName, elmnt, color) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].style.backgroundColor = "";
            }
            document.getElementById(pageName).style.display = "block";
            elmnt.style.backgroundColor = color;
        }
        document.getElementById("defaultOpen").click();
    </script> -->

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


		


 


</body>


</html>