<?php 
$CI = &get_instance();
$CI->load->model('Master_model', 'master');

$getunit = $CI->master->getunit();
$getFinGoods = $CI->master->getFinGoods($this->uri->segment(3));
$DI = &get_instance();
$DI->load->model('Salescrm_model', 'salescrm');
$getAllProducts = $DI->salescrm->getAllProducts();

if ($getFinGoods != '') {
	foreach ($getFinGoods as $row1);
	$type = $row1->type;
	$product_name = $row1->instruments_name;
	$product_code = $row1->model_number;
    $hsncode=$row1->hsncode;
	$product_image = $row1->image;
	$product_price = $row1->mvalue;
    $distributorprice = $row1->distributorprice;
    $discount_price = $row1->discount_price;
     $discount_price_distributor = $row1->discount_price_distributor;
    $volume=$row1->volume;
    $unit = $row1->unit;
	$status = $row1->status;
    $trial_reading = $row1->trial_reading;
    $spec_file=$row1->spec_file;
    $msds_file=$row1->msds_file;
    $pack_size=$row1->pack_size;
    $density=$row1->density;
    $rebrand=$row1->rebrand;

} else {
	$type = '';
	$product_name = '';
    $distributorprice = "";
    $discount_price_distributor = "";
    $hsncode='';
    $volume=0;
	$product_code = '';
	$product_image = '';
	$product_price = '';
    $discount_price = '';
    $unit = '';
	$status = '';
    $trial_reading = '';
    $spec_file='';
    $pack_size='';
    $density='';
    $msds_file='';
    $rebrand=0;
}
// $locations = $CI->master->getFinGoodsLocations($this->uri->segment(3));
// $product_companies = $CI->master->getProductCompanies($this->uri->segment(3));
// // echo "<pre>"; print_r($locations);exit;

$rebrand_arr=array();
if($rebrand==1)
{
    $rt=$this->db->select('rebrand_product_id')->from('instrument_rebrand')->where('product_id',$this->uri->segment(3))->get();
    if($rt->num_rows()>0)
    {
        foreach($rt->result() as $rtt)
        {
            $rebrand_arr[]=$rtt->rebrand_product_id;
        }

    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Edit Product </title>

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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style type="text/css">
            .select2-container
        {
            width: 100% !important;
            height: 43px !important;
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           
                            <h4 class="page-title">EDIT PRODUCT</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
<form id="loginForm" method="post" action="<?php echo page_url;?>FMS/update_instruments/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                            
                    <div class="row">
                    	
	                    <!-- <div class="col-md-3">
		                    <div class="form-group">
		                        <label for="field-1" class="control-label">TYPE</label>
								<span id="error_instrument_name" style="color:red;">*</span>
								<select class="form-control" name="type" required="">
									<option value="">SELECT</option>
									<?php if($getFinGoodsType != '') {
											foreach($getFinGoodsType as $row) {?>
												<option value="<?php echo $row->id;?>" <?php if($type == $row->id) { echo 'selected';}?>><?php echo $row->type_name;?></option>
									<?php } 
										} ?>
								</select>
		                    </div>
	                    </div> -->
	                    <div class="col-md-4">
	                        <div class="form-group">
	                            <label for="field-1" class="control-label">PRODUCT NAME</label>
								<span id="error_instrument_name" style="color:red;">*</span>
	                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_name;?>" required>
	                        </div>
	                    </div>												
						<div class="col-md-2">
                            <div class="form-group">
                                <label for="field-1" class="control-label">CODE</label>
								<span id="error_instrument_model" style="color:red;">*</span>
                                <input type="text" class="form-control" id="product_code" style="text-transform: uppercase;" name="product_code" <?php if($_SESSION['logged_in']['role']<>1){?> readonly <?php } ?> value="<?php echo $product_code;?>" required>
                            </div>
                        </div>



            <div class="col-md-2">
            <div class="form-group">
            <label for="field-1" class="control-label">HSN</label>
            <span id="error_instrument_model" style="color:red;">*</span>
            <input type="text" class="form-control" id="product_hsn" style="text-transform: uppercase;" name="product_hsn" value="<?php echo $hsncode;?>" required>
            </div>
            </div>                      

                       

                                    
					
                      

                        


                                    <div class="col-md-2">
                                    <div class="form-group">
                                    <label>PRODUCT MANUAL</label>
                                   
                                    <input type="file" name="msds" id="msds" class="form-control">
                                    <?php if($msds_file<>'')
                                    { ?>
                                          <a href="<?php echo page_url1;?>>image_bank/instrumentimg/<?php echo $msds_file;?>" download>Download</a>
                                           <input type="hidden" name="old_msdsfile" value="<?php echo $msds_file;?>">
                                   <?php } ?>
                                    </div>
                                    </div>

                                  

                                     
                                    
						

                        <div style="clear:both;height: 20px;"></div>
                        <div class="col-md-4"></div>
        				<div class="col-md-4">
							<div class="form-group">
								<label>&nbsp;</label>
								<input type="submit" style="width:100%" id="businessupdate" class="btn btn-success" value="Update">
							</div>
						</div>
						</div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 	</form>

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
$(document).ready(function(){
    $(".select4").select2({ tags:true });
    add_density();
	   $("#businessupdate").attr('disabled',false);
	   $("#businessupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#businessupdate").attr('disabled',true);
     $("#businessupdate").val('Please Wait...');
  });//submit
});//document ready
</script>
    <script >
    function checkamt(discountprice) {
        //alert(discountprice);
    var price=$("#price").val();
    if(parseFloat(discountprice)>parseFloat(price))
    {
        alert('Please enter correct value.');
        $("#disprice").val('');
    }else
    {
        true;
    }
}

function checkamtdist(disprice_distributor) {
            //alert(discountprice);
            var distributorprice = $("#distributorprice").val();
            if (parseFloat(disprice_distributor) > parseFloat(distributorprice)) {
                alert('Please enter correct value.');
                $("#disprice_distributor").val('');
            } else {
                true;
            }
        }
</script>   
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#businessupdate").click(function() {
var country_name = $("#country_name").val();
if(country_name=='')
{
	$("#error_name").html('Required!');
} else {
    $("#error_name").html('');
}
var state = $("#state").val();
if(state=='')
{
	
	$("#error_state").html('Required!');
} else {
    $("#error_state").html('');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#status_error").html('Required!');
} else {
    $("#status_error").html('');
}

var city_name = $("#city_name").val();
if(city_name=='')
{
	
	$("#error_city").html('Required!');
} else {
    $("#error_city").html('');
}

var company_name = $("#company_name").val();
if(company_name=='')
{
	
	$("#error_company").html('Required!');
} else {
    $("#error_company").html('');
}

var address = $("#address").val();
if(address=='')
{
	
	$("#address_error").html('Required!');
} else {
    $("#address_error").html('');
}

var contact_number = $("#contact_number").val();
if(contact_number=='')
{
	
	$("#contact_error").html('Required!');
} else {
    $("#contact_error").html('');
}

if(country_name=='' || state=='' || city_name=='' || company_name=='' || address=='' || contact_number=='' || status=='')
{
	
	return false;
}

});
 $('.select3').select2();

  $('.select44').select2();

});

 function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      
      
      }

      function check_rebrand()
      {
         $("#rebrand_div").css('display','none');
            $("#rebrand_prd").attr('required',false);
        if($('#rebrand').is(":checked"))
        {
            $("#rebrand_div").css('display','');
            $("#rebrand_prd").attr('required',true);
        }else
        {            
            $("#rebrand_div").css('display','none');
            $("#rebrand_prd").attr('required',false);
        }
      }


</script>
    </body>
</html>