<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> View Item Detail</title>
		<!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

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
<style>
hr{margin-top: 7px;

margin-bottom: 7px;
}
strong{
    color:red;
}
</style>
  <?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

<style>

table.manglesh thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

text-align:center;

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
            <div class="container-fluid" style="background-color:#fff;">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						 
                           </div>
                           
                            <h4 class="page-title">Item Detail</h4><hr>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<?php 
	$this->db->select('a.*,b.location_id, b.installed_location,d.business_loc_id, d.company_name, e.user_id, e.first_name, e.last_name,c.type_id,c.asset_type as asset_name,f.brand_id,f.brand_name,g.ms_office_id,g.ms_office as msoffice, h.ms_office_id, h.ms_office_licence_type as msoffice_licence_type,i.ms_office_id,i.ms_office_licence_type,j.operationg_id, j.operating_system')->from('presto_it_assets a');
	$this->db->join('installed_location b','a.department_id=b.location_id','left');
	$this->db->join('business_location d','a.business_location_id=d.business_loc_id','left');
	$this->db->join('asset_type c','a.asset_type=c.type_id','left');
	$this->db->join('system_users e','e.user_id=a.added_by','left');
	$this->db->join('asset_brands f','a.brand_id=f.brand_id','left');
	$this->db->join('asset_microsift_office g','a.ms_office=g.ms_office_id','left');
	$this->db->join('asset_microsift_office_licence_type h','a.ms_office_license_type=h.ms_office_id','left');
	$this->db->join('asset_microsift_windows_licence_type i','a.window_license_type=i.ms_office_id','left');
	$this->db->join('asset_operating_system j','a.operating_system=j.operationg_id','left');
	$this->db->where('a.asset_id',$this->uri->segment(3));
	$query = $this->db->get();
	foreach($query->result() as $row)
	
	
	
?>
                 <div class="row">
                    <div class="col-sm-12">
					<div class="row">
                        <div class="col-md-2">Business location  :<br> <strong><?php echo $row->company_name;?></strong></div>
                        <div class="col-md-2">Department : <br><strong><?php echo $row->installed_location;?></strong></div>
                        <div class="col-md-2">Antivirus Key :<br> <strong><?php echo $row->antivirus_key;?></strong></div>
                        <div class="col-md-2">Asset Type : <br><strong><?php echo $row->asset_name;?></strong></div>
						<div class="col-md-2">Battery Purchase Date  : <br><strong><?php if($row->battery_purchase_date=='0000-00-00'){}else{ echo date('d-m-Y',strtotime($row->battery_purchase_date));}?></strong></div>
                        <div class="col-md-2">Battery Warranty Expire : <br><strong><?php if($row->battery_warrenty_expire=='0000-00-00'){}else{ echo date('d-m-Y',strtotime($row->battery_warrenty_expire));}?></strong></div>
						</div>
						<hr>
						<div class="row">
                        
                        <div class="col-md-2">Brand : <br><strong><?php echo $row->brand_name;?></strong></div>
                        <div class="col-md-2">Care Pack / AMC Purchase Date : <br><strong><?php if($row->amc_purchase_date=='0000-00-00'){}else{ echo date('d-m-Y',strtotime($row->amc_purchase_date));}?></strong></div>
						<div class="col-md-2">Computer Name : <br><strong><?php echo $row->computer_name;?></strong></div>
                        <div class="col-md-2">Customer Care : <br><strong><?php echo $row->customer_care;?></strong></div>
						<div class="col-md-2">Harddisk Drive : <br><strong><?php echo $row->harddisk_drive;?></strong></div>
                        <div class="col-md-2">IP Address : <br><strong><?php echo $row->harddisk_drive;?></strong></div>
						
						</div><hr>
						<div class="row">
						<div class="col-md-2">Invoice Attached  : <br><strong><?php echo $row->invoice_attached;?></strong></div>
                        <div class="col-md-2">Issue : <br><strong><?php echo $row->issue;?></strong></div>
                        <div class="col-md-2">KeyBoard  : <br><strong><?php echo $row->keyboard;?></strong></div>
                        <div class="col-md-2">License No.  : <br><strong><?php echo $row->license_number;?></strong></div>
                        <div class="col-md-2">Login User  : <br><strong><?php echo $row->login_user;?></strong></div>
                        <div class="col-md-2 ">Login Password  :   <br><strong><?php echo $row->login_password;?></strong></div>
                        </div><hr>
						
                        <div class="row">
                        <div class="col-md-2">Microsoft Office  :  <br><strong><?php echo $row->msoffice;?></strong></div>
                        <div class="col-md-2">Microsoft Office Licence Types  :  <br><strong><?php echo $row->msoffice_licence_type;?></strong></div>
                        <div class="col-md-2">Windows Licence Types  :  <br><strong><?php echo $row->window_license_type;?></strong></div>
                        <div class="col-md-2">Model No.  :  <br><strong><?php echo $row->model_number;?></strong></div>
                        <div class="col-md-2">Monitor / TFT Serial No.  :  <br><strong><?php echo $row->monitor_tft_sr_number;?></strong></div>
                        <div class="col-md-2">Mouse :  <br><strong><?php echo $row->mouse;?></strong></div></div><hr>
						<div class="row">
                        <div class="col-md-2">Operating System :  <br><strong><?php echo $row->operating_system;?></strong></div>
                        <div class="col-md-2">Operating System Type   :  <br><strong><?php echo $row->operating_sys_type;?></strong></div>
                        <div class="col-md-2">Part No.  :  <br><strong><?php echo $row->part_number;?></strong></div>
                        <div class="col-md-2">Processor  :  <br><strong><?php echo $row->processor;?></strong></div>
                        <div class="col-md-2">Purchase Date  :  <br><strong><?php if($row->purchase_date=='0000-00-00'){}else{ echo date('d-m-Y',strtotime($row->purchase_date));}?></strong></div>
                        <div class="col-md-2">RAM   :  <br><strong><?php echo $row->ram;?></strong></div></div><hr>
						<div class="row">
                        <div class="col-md-2">Relationship / Accounts No  :  <br><strong><?php echo $row->relationship_acc_no;?></strong></div>
                        <div class="col-md-2">Renewal to be Done  :  <br><strong><?php echo $row->renewal_to_be_done;?></strong></div>
                        <div class="col-md-2">Screen  :  <br><strong><?php echo $row->screen;?></strong></div>
                        <div class="col-md-2">Serial No  :  <br><strong><?php echo $row->serial_number;?></strong></div>
                        <div class="col-md-2">Service Tag  :  <br><strong><?php echo $row->service_tag;?></strong></div>
                        <div class="col-md-2">Software Key   :  <br><strong><?php echo $row->software_key;?></strong></div></div><hr>
						<div class="row">
                        <div class="col-md-2">Telephone No  :  <br><strong><?php echo $row->telephone_number;?></strong></div>
                        <div class="col-md-2">User Name  :  <br><strong><?php echo $row->user_name;?></strong></div>
                        <div class="col-md-2">Vendor Name  :  <br><strong><?php echo $row->vendor_name;?></strong></div>
                        <div class="col-md-2">Warranty End date   :  <br><strong><?php if($row->warranty_end_date=='0000-00-00'){}else{ echo date('d-m-Y',strtotime($row->warranty_end_date));}?></strong></div>
                        <div class="col-md-4">Website   :  <br><strong><?php echo $row->website;?></strong></div>
                         <div class="col-md-2">Assigned to   :  <br><strong><?php $assign =  $row->assigned_to;
                         $qry = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$assign)->get();
                         foreach($qry->result() as $assignedto)
                         echo $assignedto->first_name." ".$assignedto->last_name;
                         
                         
                         ?></strong></div>
                        <div class="col-md-2">Attached File   :
                            <?php 
                        $attachmentfile = explode('.',$row->attachment_file);
                        $file = end($attachmentfile);
                        if($file=='pdf' || $file=='PDF' || $file=='doc'|| $file=='docx'){
                        ?>
                        <iframe src="<?php echo itassets_path;?><?php echo $row->attachment_file;?>"></iframe>
                        <a href="<?php echo itassets_path;?><?php echo $row->attachment_file;?>" target="_blank"><span class="btn btn-danger">View File </span></a>
                        <?php 
                        }else{?>
                         <br><img src="<?php echo itassets_path;?><?php echo $row->attachment_file;?>" width="100px" height="100px">
                         
														<a href="<?php echo itassets_path;?><?php echo $row->attachment_file;?>" target="_blank"><span class="btn btn-success ">View Attachment</span></a>
													
                        <?php }?></div>
                        </div><hr>
                       <div class="row">
                            <div class="col-md-2">Available in stock  :  <br><?php if($row->available_in_stock=='0'){echo "No";}else{ echo "Yes";}?></div>
                            <div class="col-md-6">Remarks:  <br>
                            <?php echo $row->it_remarks; ?>
                            </div>
                       </div>
                       
						
                    </div>
					
					
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
	var remarks= $("#remarks").val();
if(remarks=='')
{
	$("#error_remarks").html('Required!');
}
if(remarks=='')
{
	
	return false;
}
});
});
</script>
 <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Tech_support/raised_ticket_detail/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'location' },
				{ mData: 'item_name' },
				{ mData: 'remarks' },
				{ mData: 'status' },
				{ mData: 'added_on' },
				{ mData: 'added_by' }
				
				
		]
});   
});

</script>
    </body>
</html>