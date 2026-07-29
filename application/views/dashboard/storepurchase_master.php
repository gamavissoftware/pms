<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?> Store and Purchase Management</title>





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
<div class="container-fluid">
<div class="desc-box">
<div class="row">
<div class="col-sm-2">
<img src="<?php echo dashboard_icon;?>store_purchase.jpg" style="    width: 100%;">
</div>
<div class="col-sm-8">
<h6>Store and Purchase Management</h6>
<p>This panel is designed in such a way that you can access all store and purchase reports related to products. You will see the different store and management functionalities like PO instruction script, rack location, integrated management system (IMS) that allows you to manage the product inventory, BOM material information, housekeeping item list, stock aging factor list, vendor forms, list of approved vendors, and unit list. All these necessary functionalities will help to manage your store and purchase system with ease.</p>
</div>
<div class="col-sm-2">
<div class="text-center"><a href="#">
<!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
<img src="<?php echo dashboard_icon;?>header_icon.png" style="width: 40%; margin-top: 50px;">
</a>
</div>
</div>
</div>
</div>
</div>
</div>


<div class="wrapper">
    <div class="container-fluid" >
        <div class="dashboard-header">
            <h1>Store and Purchase Management</h1><hr>
        </div>
        <div class="row">		
        <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','286')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>  
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/setpoinstructions">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>po_closed.png">
                        </div>
                            <p>PO Instruction Script</p>
                    </div>
                </a>
            </div>
            <?php }?>
									
									
				    <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','97')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/add_rack_location">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>business_location.png">
                        </div>
                            <p>Rack Location</p>
                    </div>
                </a>
            </div>
            <?php }?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','197')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/machineparts">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>invent.png">
                        </div>
                            <p>IMS</p>
                    </div>
                </a>
            </div>
            <?php }?>
								
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','268')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/itemwisevendor">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>field_boy.png">
                        </div>
                            <p>Green Supplier</p>
                    </div>
                </a>
            </div>
            <?php }?>
								
								
								<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','198')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/bom_materials">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>good_value.png">
                        </div>
                            <p>BOM Material</p>
                    </div>
                </a>
            </div>
            <?php }?>



                                <?php 
                                    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','366')->where('submodule_access','1')->get();
                                    if($qry->num_rows()>0){
                                    ?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Challan/master_nrgp">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>vendor.png">
                        </div>
                            <p>NRGP MASTER</p>
                    </div>
                </a>
            </div>
            <?php }?>
								
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','199')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/add_general_items">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>house.png">
                        </div>
                            <p>House Keeping Items</p>
                    </div>
                </a>
            </div>
            <?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','200')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Store/ageing">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>ageing.png">
                        </div>
                            <p>Ageing Factor</p>
                    </div>
                </a>
            </div>
            <?php
									}
									?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','266')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Vendor/vendorform">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>vendor.png">
                        </div>
                            <p>Vendor Form</p>
                    </div>
                </a>
            </div>
            <?php
									}
									?>
									
									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','267')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Vendor/approved_vendors">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>payment_rec.png">
                        </div>
                            <p>Approved Vendors</p>
                    </div>
                </a>
            </div>
            <?php
									}
									?>
									
										<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','265')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Vendor/pending_for_review">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>complete_mrn.png">
                        </div>
                            <p>Vendor for Approval</p>
                    </div>
                </a>
            </div>
            <?php
									}
									?>

									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','59')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Master/User_management/add_new_vendor">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>delegation.png">
                        </div>
                            <p>Vendor Management</p>
                    </div>
                </a>
            </div>
            <?php }?>

									<?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','149')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>	
            <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url;?>Master/Units">
                    <div class="report-box badge1" >
                        <div class="text-center">
                            <img src="<?php echo dashboard_icon;?>weight.png">
                        </div>
                            <p>Units</p>
                    </div>
                </a>
            </div>
            <?php }?>
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