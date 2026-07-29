<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getAllVendors = $CI->Salescrm_model->getAlltransporters();
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();

if($this->uri->segment(6)<>'ALL' && $this->uri->segment(6)<>'')
{
$loc=$CI->Salescrm_model->get_location_name($this->uri->segment(6));
}else
{
$loc="ALL";
}


if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
{
$vendor=$CI->Salescrm_model->get_transporter_name($this->uri->segment(5));
}else
{
$vendor="ALL";
}

$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Transporter</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                 <td style="border: 1px solid black;text-align:center;color:black;">'.$vendor.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
               
                </tr>
                </tbody></table>';




$claim_amount=$CI->Salescrm_model->get_months_transport_claim_amount_type2($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

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
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
				font-size:12px;
            }
			table.pretty td {
                text-align: center;
                font-size:12px;
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
            <div class="container">
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                    
                            <h4 class="page-title text-center">THIS MONTH'S TRANSPORTATION CLAIM TYPE-II</h4>
                        
                    </div>
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box ">
                       

                        <form method="post" action="<?php echo page_url;?>Approval/filter_this_month_transport_claim_type2">
                            
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $this->uri->segment(3);?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $this->uri->segment(4);?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Transporter</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <select class="form-control" name="party">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllVendors != '') {
                                                        foreach($getAllVendors as $row2) {?>
                                                        <option value="<?php echo $row2->id;?>"<?php if($row2->id == $this->uri->segment(5)) { echo 'selected';};?>><?php echo $row2->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $this->uri->segment(6)){?> selected <?php } ?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                             
                        </form>
                    </div>
                </div>
            
                <div class="row">
                    
                    <div class="col-md-4 card-box" style="min-height: 170px;">
                          <h4 class="text-center">Type II Transport. Claim</h4>
                        <p class="text-center"><a href="<?php echo page_url;?>ExcelImport/type_two_claim_format/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>" class="btn btn-danger btn-sm">Download Type-II Claim Format</a></p>

                    </div>
                    <div class="col-md-4 card-box" style="min-height: 170px;"><?php echo $filter_criteria;?>
                    </div>

                    <div class="col-md-4 card-box" style="min-height: 170px;">
                        <h4 class="text-center">This Month's Transportation Claim Amount</h4>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;">₹<?php echo $claim_amount;?></p>
                       

                    </div>
                </div>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                 <tr>
                                    <th>Sr No.</th>
                                    <th>Approval Date/ID</th>
                                    <th>Approved Location</th> 
                                     <th>Transportation Rate</th>
                                    <th>Product Details</th>                    

                                    <th>Transportation Details</th>
                                    <th>Customer Details</th>
                                    <th>Total Claim</th>
                                     <th>Entry On/By </th>
                                
                                </tr>
                                </thead>
                                
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

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/this_month__transport_claim_list_t2/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
"aoColumns": [
				 { mData: 'sr_no' } ,
                { mData: 'current_date' },
                { mData: 'hpcl_location' },
                 { mData: 'transportation_rate' },
                { mData: 'product_details' },
               
               
                { mData: 'transport_detail' },  
                { mData: 'customer_detail' },  

                { mData: 'total_claim' },
                { mData: 'approvalby' }


               
				
				
		]
        
});  
 
});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
	
	$("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
	
	return false;
}

});
});
</script>
    </body>
</html>