<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$getAllProducts = $CI->Salescrm_model->getAllProducts();
$starting_date=$this->uri->segment(3);
$ending_date=$this->uri->segment(4);
$location=$this->uri->segment(5);
$products=$this->uri->segment(6);
$transport_type=$this->uri->segment(7);
$customer=$this->uri->segment(8);

if($customer<>'ALL' &&  $customer<>'')
{
    $cust=$CI->Salescrm_model->getdirectcustomer_name($customer);
}else
{
    $cust="ALL";
}



if($location<>'ALL' &&  $location<>'')
{
$loc=$CI->Salescrm_model->get_location_name($location);
}else
{
$loc="ALL";
}


if($products<>'ALL' && $products<>'')
{
$product=$CI->Salescrm_model->get_product_name($products);
}else
{
$product="ALL";
}

if($transport_type<>'ALL' && $transport_type<>'')
{
    if($transport_type==1)
    {
        $transport="EXMI";
    }else
    {
        $transport="DELIVERED";
    }

}else
{
    $transport="ALL";
}

$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="5">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Customer</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Transportation Type</th>
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($starting_date)).' to '.date('d-M-Y',strtotime($ending_date)).'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
                <th style="border: 1px solid black;text-align:center; width:200px;">'.$cust.'</th>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$product.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$transport.'</td>

                </tr>
                </tbody></table>';
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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

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

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px">
                    <div class="col-md-12">
                        <?php echo $this->session->flashdata('message');?>
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-center">
                        <h4 class="page-title text-center">TYPE-II/III APPROVAL LIST</h4>
                        
                    </div>
                </div>

                        <div class="row">
                        <form method="post" action="<?php echo page_url;?>Approval/filter_type_two_approval">
                        
                            <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $starting_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $ending_date;?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL"  <?php if($location=='ALL'){?> selected <?php } ?>>ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $location){ echo 'selected';};?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                      <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <select class="form-control" name="customer" id="customers">
                                                <option value="ALL"  <?php if($this->uri->segment(8)=='ALL'){?> selected <?php } ?>>ALL</option>
                                                <?php 
                                                    $sql = $this->db->select('a.id,a.customer_name')
                                                    ->from('hpcl_direct_customer a')
                                                    ->get();
                                                    if($sql->num_rows() > 0) {
                                                    foreach($sql->result() as $row1){
                                                       ?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $customer){ echo 'selected';};?>><?php echo $row1->customer_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Product</label>
                                            <select class="form-control" name="products" id="products">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllProducts != '') {
                                                        foreach($getAllProducts as $row2) {?>
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id == $products) { echo 'selected';};?>><?php echo $row2->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Transportation Type</label>
                                            <select class="form-control" name="transportation" id="transportation">
                                                <option value="ALL">ALL</option>
                                                <option value="1" <?php if($transport_type==1){?> selected <?php } ?>>EXMI</option>
                                                <option value="2" <?php if($transport_type==2){?> selected <?php } ?>>DELIVERED</option>
                                                
                                            </select>
                                        </div>
                                    </div>


                                  <!--   <div class="col-md-3">
                                    <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" name="type" id="type">
                                    <option value="ALL">ALL</option>
                                    <option value="2" <?php //if($transport_type==3){?> selected <?php //} ?>>TYPE II</option>
                                    <option value="3" <?php //if($transport_type==3){?> selected <?php //} ?>>TYPE III</option>

                                    </select>
                                    </div>
                                    </div>   -->                                   

                                     <div class="col-md-4"></div>
                                    <div class="col-md-4 text-center" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 card-box"><?php echo $filter_criteria;?></div>
                        <div class="col-md-4"></div>
                    </div>
            
                <!-- end page title end breadcrumb -->

                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Approval Type/Date/ID</th>
                                    <th>Customer Name</th>
                                    <th>Product Details</th>
                                    <th>Action</th>
                    
                                
                                </tr>
                                </thead>
                                
                            </table>
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
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
$( document ).ready(function() {
$('#products').select2();
$('#customers').select2();
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/type_two_approval_listing/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>/<?php echo $this->uri->segment(8);?>",
"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'current_date' },
                // { mData: 'invoice' },
                // { mData: 'invoice_date' },
                /*{ mData: 'annexture' },*/
                { mData: 'customer_name' },
			
                { mData: 'product_details' },
                { mData: 'edit' }
				
				
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
<script type="text/javascript">
    function chk_item_picked_up(id) {
        window.location.href = "<?php echo page_url;?>Approval/chk_item_picked_up/"+id;
    }
</script>
    </body>
</html>