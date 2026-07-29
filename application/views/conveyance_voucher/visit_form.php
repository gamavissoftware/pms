<?php
$serviceid=$this->uri->segment(4);

 $cname='';
        $contactperson='';
        $mobno='';
        $emalid='';
        $rmk='';
        $request=0;
$type=$this->uri->segment(3);
if($type<>'' && $type<>'NA')
{
    $roww=$this->db->select('company_name,contact_person,mobile_number')->from('prestogroup_orders')->where('order_id',$type)->get();
    if($roww->num_rows()>0)
    {
        foreach($roww->result() as $rowww);
        $cname=$rowww->company_name;
        $contactperson=$rowww->contact_person;
        $mobno=$rowww->mobile_number;
           $request=$type;
           $emalid='';
        
        $rowwww=$this->db->select('remarks')->from('service_request_followup')->where('record_id',$type)->where('status','5')->get();
        if($rowwww->num_rows()>0)
        {
        foreach($rowwww->result() as $rowww1);
        
        $rmk=$rowww1->remarks;
     }
    }
    
}else{


$roww=$this->db->select('b.company_name,a.customer_name,a.mobile_number,a.email_id')->from('service_detail a')->join('nrgp_master b','a.company_name=b.id')->where('sr_id',$serviceid)->get();
    if($roww->num_rows()>0)
    {

        foreach($roww->result() as $rowww);
        $cname=$rowww->company_name;
        $contactperson=$rowww->customer_name;
        $mobno=$rowww->mobile_number;
        $emalid=$rowww->email_id;
       

    }



}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Prestogroup</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">Visit Form</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Sales/visit_form/" enctype="multipart/form-data">
								  <input type="hidden" name="odid" value="<?php echo $request;?>">
								   <input type="hidden" name="serviceid" value="<?php echo $serviceid;?>">
										  <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Your Name</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $first_name;?> <?php echo $last_name;?>" readonly>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Company Name</label>
														 <span id="error_company_name" style="color:red;">*</span>
														<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $cname;?>" required>
													</div>
												</div>
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Contact Person</label>
														 <span id="error_contact_person" style="color:red;">*</span>
														<input type="text" class="form-control" name="contact_person" id="contact_person" value="<?php echo $contactperson;?>" required>
													</div>
												</div>
												
												 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Contact Number</label>
														 <span id="error_contact_number" style="color:red;">*</span>
														<input type="text" class="form-control" name="contact_number" id="contact_number" value="<?php echo $mobno;?>" required>
													</div>
												</div>
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Sales Force No</label>
														 <span id="error_sales_force" style="color:red;">*</span>
														 <input type="text" class="form-control" name="sales_force" id="sales_force" value="" required>
													</div>
												</div>
												
													
												
												<div class="col-md-2">
													<div class="form-group">
														<label>Warranty Status</label>
														<span id="error_warrenty_status" style="color:red;">*</span>
														<select class="form-control" name="warrenty_status" id="warrenty_status" required>
														    <option value="">Select Option</option>
														    <option value="1">Under Warranty</option>
														    <option value="2">Warranty Expired</option>
														</select>
													
													</div>
												</div>
												
												 <script>
												$(function() {
												   
												
												$('#chargabledetail').hide(); 
												$('#pricedetail').hide(); 
												$('#billdetail').hide(); 
												$("#payment_collect").hide();
													$('#warrenty_status').change(function(){
												if($('#warrenty_status').val() == '2') {
											   $('#pricedetail').show(); 
												$('#chargabledetail').show(); 
												$('#billdetail').show(); 
												$("#payment_collect").show();
											
												} else {
											
												$('#chargabledetail').hide(); 
												$('#pricedetail').hide(); 
												$('#billdetail').hide(); 
												$("#payment_collect").hide();
												
												} 
												});
												});
											</script>
											
												<div class="col-md-2" id="chargabledetail" style="display:none">
											    <div class="form-group">
											        <label>Chargable <span id="error_bill_number" style="color:red;">*</span></label>
											       <select class="form-control" name="chargable" id="chargable">
											           
											           <option value="1">Yes</option>
											           <option value="2">No</option>
											       </select>
											    </div>
											</div>
											
											 <script>
												$(function() {
												   
												$('#pricedetail').hide(); 
												$('#billdetail').hide(); 
												$("#payment_collect").hide();
													$('#chargable').change(function(){
												if($('#chargable').val() == '1') {
												$('#pricedetail').show(); 
												$('#billdetail').show(); 
												$("#payment_collect").show();
												
												$("#charges").attr("required", true);
												$("#bill_number").attr("required", true);
												} else {
												$('#pricedetail').hide(); 
												$('#billdetail').hide(); 
												$("#payment_collect").hide();
												$("#charges").attr("required", false);
												
												} 
												});
												});
											</script>
											
											<div class="col-md-2" id="pricedetail" style="display:none">
											    <div class="form-group">
											        <label>Charges <span id="error_bill_number" style="color:red;">*</span></label>
											        <input type="number" class="form-control" name="charges" id="charges" value="" step="0.01">
											    </div>
											</div>
											
												<div class="col-md-2" id="billdetail" style="display:none">
											    <div class="form-group">
											        <label>Bill Number <span id="error_bill_number" style="color:red;">*</span></label>
											        <input type="text" class="form-control" name="bill_number" id="bill_number" value="">
											    </div>
											</div>
											
												<div class="col-md-2" id="payment_collect" style="display:none">
											    <div class="form-group">
											        <label>Payment to Collect <span id="error_bill_number" style="color:red;">*</span></label>
											       <select class="form-control" name="payment_to_collect" id="payment_to_collect">
											           <option value="1">Yes</option>
											           <option value="0">No</option>
											       </select>
											    </div>
											</div>
												
												<div class="col-md-7">
													<div class="form-group">
														<label>Nature of Complaints</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
														<input type="text" class="form-control" name="nature_of_complaints" id="nature_of_complaints" value="<?php echo $rmk;?>">
													</div>
												</div>
												
													<div class="col-md-3">
													<div class="form-group">
														<label>Engineer</label>
														<span id="error_nature_of_complaints" style="color:red;">*</span>
													<select class="form-control select2" name="engineer" id="engineer">
													    <option>Select Engineer</option>
													    <?php 
													    $query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status','1')->where('department_id','5')->where('hide_profile','0')->order_by('first_name','asc')->get();
													    foreach($query->result() as $row){
													    ?>
													    <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
													    <?php }?>
													</select>
													</div>
												</div>
											 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Date of Visit</label>
														 <span id="error_sales_force" style="color:red;">*</span>
														 <input type="date" class="form-control" name="visit_date" id="visit_date" value="<?php echo date('m-d-Y');?>" required>
													</div>
												</div>	
                                            </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Save">
											</div>
										</div>
									
									</form>
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

     <script language="javascript" type="text/javascript">   

$(document).ready(function() {
    $('.select2').select2({ });
$("#save").click(function() {
var company_name = $("#company_name").val();
if(company_name=='')
{
	$("#error_company_name").html('Required!');
}
var sales_force = $("#sales_force").val();
if(sales_force=='')
{
	$("#error_sales_force").html('Required!');
}
var warrenty_status = $("#warrenty_status").val();
if(warrenty_status=='')
{
	$("#error_warrenty_status").html('Required!');
}
var nature_of_complaints = $("#nature_of_complaints").val();
if(nature_of_complaints=='')
{
	$("#error_nature_of_complaints").html('Required!');
}
var service_visit_report = $("#service_visit_report").val();
if(service_visit_report=='')
{
	$("#error_service_visit_report").html('Required!');
}
var cases = $("#cases").val();
if(cases=='')
{
	$("#error_case").html('Required!');
}


if(company_name=='' || sales_force=='' || warrenty_status=='' || nature_of_complaints=='' ||service_visit_report=='' || cases=='')
{
	
	return false;
}

});
});
</script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>