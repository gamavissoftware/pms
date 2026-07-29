<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> IT Assets</title>
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
<style>
	.label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
    font-size: 10px !important;
}
table.pretty th {
				font-size:12px;
				
			}
table.pretty tbody td {
				font-size:12px;
				
			}
</style>
 <?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

<style>

table.pretty thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

text-align:center;

}

</style>
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
					<?php
					 $forminput = (object)$inputdata;
					if($forminput->asset_type){?>
					 <a href="<?php echo page_url;?>IT_Assets/assets_by_type/<?php echo $forminput->asset_type;?>"><div class="col-lg-2 col-md-2">
					<div style="background-color:#2167a1; color:#fff; text-align:center;"> Asset Type</div>
					<div class="card-box widget-user" style="min-height: 85px;">
						<div class="text-center">
							<h4><span style="color:red; font-weight:bold;"><?php $query = $this->db->select('asset_id, asset_type')->from('presto_it_assets')->where('asset_type',$forminput->asset_type)->get();
							$res = $query->result();
							echo count($res);
							?></span>
							<?php $query = $this->db->select('type_id, asset_type')->from('asset_type')->where('type_id',$forminput->asset_type)->get();
							foreach($query->result() as $asset_name){
								echo $asset_name->asset_type;
							}?></h4>
						</div>
					</div>
					</div></a><?php }?>
					
					<?php if($forminput->location){?>
					 <a href="<?php echo page_url;?>IT_Assets/assets_by_department/<?php echo $forminput->location;?>"><div class="col-lg-2 col-md-2">
					 <div style="background-color:#2167a1; color:#fff; text-align:center;">Department</div>
					<div class="card-box widget-user" style="min-height: 85px;">
						<div class="text-center">
							<h4><span style="color:red; font-weight:bold;"><?php $query = $this->db->select('asset_id, department_id')->from('presto_it_assets')->where('department_id',$forminput->location)->get();
							$res = $query->result();
							echo count($res);
							?></span>
							<?php $query = $this->db->select('location_id, installed_location')->from('installed_location')->where('location_id',$forminput->location)->get();
							foreach($query->result() as $department){
								echo $department->installed_location;
							}?>
							
							</h4>
						</div>
					</div>
					</div></a><?php }?>
					
					<?php if($forminput->window_license_type){?>
					 <a href="<?php echo page_url;?>IT_Assets/assets_by_window_license/<?php echo $forminput->window_license_type;?>"><div class="col-lg-2 col-md-2">
					 <div style="background-color:#2167a1; color:#fff; text-align:center;">Windows License</div>
					<div class="card-box widget-user" style="min-height: 85px;">
						<div class="text-center">
							<h4><span style="color:red; font-weight:bold;"><?php $query = $this->db->select('asset_id, window_license_type')->from('presto_it_assets')->where('window_license_type',$forminput->window_license_type)->get();
							$res = $query->result();
							echo count($res);
							?></span>
							<?php $query = $this->db->select('ms_office_id, ms_office_licence_type')->from('asset_microsift_windows_licence_type')->where('ms_office_id',$forminput->window_license_type)->get();
							foreach($query->result() as $window_license){
								echo $window_license->ms_office_licence_type;
							}?>
							
							</h4>
						</div>
					</div>
					</div></a><?php }?>
					
					<?php if($forminput->ms_office_license_type){?>
					 <a href="<?php echo page_url;?>IT_Assets/assets_by_ms_office_license/<?php echo $forminput->ms_office_license_type;?>"><div class="col-lg-2 col-md-2">
					 <div style="background-color:#2167a1; color:#fff; text-align:center;">Microsoft Office License</div>
					<div class="card-box widget-user" style="min-height: 85px;">
						<div class="text-center">
							<h4><span style="color:red; font-weight:bold;"><?php $query = $this->db->select('asset_id, ms_office_license_type')->from('presto_it_assets')->where('ms_office_license_type',$forminput->ms_office_license_type)->get();
							$res = $query->result();
							echo count($res);
							?></span>
							<?php $query = $this->db->select('ms_office_id, ms_office_licence_type')->from('asset_microsift_office_licence_type')->where('ms_office_id',$forminput->ms_office_license_type)->get();
							foreach($query->result() as $office_license){
								echo $office_license->ms_office_licence_type;
							}?>
							
							</h4>
						</div>
					</div>
					</div></a><?php }?>
					
					<?php if($forminput->available_status){?>
					 <a href="javascript:void(0);"><div class="col-lg-2 col-md-2">
					 <div style="background-color:#2167a1; color:#fff; text-align:center;">Available in stock</div>
					<div class="card-box widget-user" style="min-height: 85px;">
						<div class="text-center">
							<h4><span style="color:red; font-weight:bold;"><?php $query = $this->db->select('asset_id, available_in_stock')->from('presto_it_assets')->where('available_in_stock',$forminput->available_status)->get();
							$res = $query->result();
							echo count($res);
							?></span>
							<?php if($forminput->available_status=='1'){
								echo "In Stock";
							}else{
								echo "Not Available";
							}?>
							
							</h4>
						</div>
					</div>
					</div></a><?php }?>
					
					
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                    <th>Action</th>
                                    <th>User Name</th>
									<th>Assigned To</th>
									<th>IT Asset Name</th>
									<th>Asset Type</th>
									<th>Location </th>
									<th>Department</th>
									<th>Microsoft Office</th>
									<th>Microsoft Office Licence Types</th>
									<th>Operating System</th>
							    	<th>Microsoft Windows Licence Types</th>
							    	<th>Anti Virus</th>
									<th>Brand Name</th>
									<th>Model No.</th>
									<th>Serial No.</th>
									<th>Issue</th>
									<th>Warranty Expire Date</th>
								
									
                                </tr>
                                </thead>
								<tbody>
								<?php 
								$i=1;
								foreach($resultdata as $resultdata1){
									$itassets = (object)$resultdata1;
									$edit = "<a href='".page_url."IT_Assets/edit_item/".$itassets->asset_id."'><i class='fa fa-pencil'></i></a>";
									$view = "<a href='".page_url."IT_Assets/view_item_detail/".$itassets->asset_id."'><span class='btn btn-warning btn-xs'>View Detail</span></a>";	
			
								?>
									<tr>
										<td><?php echo $i;?></td>
											<td><?php echo $edit." &nbsp; ".$view;?></td>
											<td><?php echo $itassets->user_name;?></td>
										<td><?php echo $itassets->assigned_person_fname." ".$itassets->assigned_person_lname;?></td>
										
										<td><?php echo $itassets->it_asset_name;?></td>
										<td><?php echo $itassets->asset_name;?></td>
											<td><?php echo $itassets->company_name."<br>".$itassets->city_name;?></td>
										<td><?php echo $itassets->installed_location;?></td>
											<td><?php echo $itassets->ms_office;?></td>	
											<td><?php echo $itassets->ms_office_licence_type;?></td>
												<td><?php echo $itassets->operating_system;?></td>
													<td><?php echo $itassets->ms_office_window_licence_type;?></td>
										<td><?php echo $itassets->antivirus_key;?></td>
										<td><?php echo $itassets->brand_name;?></td>
										<td><?php echo $itassets->model_number;?></td>
										<td><?php echo $itassets->serial_number;?></td>
									
										<td><?php echo $itassets->issue;?></td>
										<td><?php echo $itassets->warranty_end_date;?></td>
										
									
									</tr>
								<?php $i++;}?>
								</tbody>
                                
                            </table>
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

       

<script language="javascript" type="text/javascript">   

$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });

});


</script>
 <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pageLength": 500,
dom: 'Bfrtip',
        buttons: [
            'excel'
        ],
"pagination":true
});   
});

</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>