<?php
$CI =& get_instance();
$CI->load->model('Store_model','storemodel');

$curstock='';
$partname='';
$uname='';
if($this->uri->segment(3)<>'')
{
$Resteyre=$this->db->select('current_stock,part,unit')->from('machine_parts_with_picture')->where('id',$this->uri->segment(3))->get();
if($Resteyre->num_rows()>0)
{
	foreach($Resteyre->result() as $Resteyre1);
	$curstock=$Resteyre1->current_stock;
	$partname=$Resteyre1->part;
	
	$uname=$CI->storemodel->getunit($Resteyre1->unit);
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

        <title>ADD IMS STOCK</title>

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
				<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

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
                           
                            <h4 class="page-title">Adding Stock for <?php echo $partname;?> Curent Stock- <?php echo $curstock;?> <?php echo strtolower($uname);?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

 <form method="post" action="<?php echo page_url;?>Store/updatedexistingstock/<?php echo $this->uri->segment(3);?>"  enctype="multipart/form-data">
 <input type="hidden" name="curstock" value="<?php echo $curstock;?>">
 
				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
							<div class="col-sm-12 col-xs-12 col-md-12">
							<div class="col-md-3">
							<div class="form-group">
							<label for="field-2" class="control-label">Stock in <?php echo $uname;?><span id="error_machine_part" style="color:red;font-size:10px;">* (Please not this stock will get added to current stock)</span></label>
							<input type="text" name="stock" id="stock" class="form-control allow_decimal" value="<?php echo set_value('stock');?>" required>
							<span style="color:red;"><?php echo form_error('stock');?></span>
							</div>
							</div>

							</div>
							<script>
								$(".allow_decimal").on("input", function(evt) {
								var self = $(this);
								self.val(self.val().replace(/[^0-9\.]/g, ''));
								if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
								{
								evt.preventDefault();
								}
								});
//*
							</script>
											
											<div class="row">
											<div class="col-md-12">
											
											<div class="col-md-9"></div>
											<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
											<label>&nbsp;</label>
											<input type="submit" class="btn btn-success" value="Save">
											</div>
											</div>
											
											</div>
											</div>
											
											

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->
					</form>
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
	$(".select2").select2({});
$("#save").click(function() {
var machine = $("#machine").val();
if(machine=='')
{
	$("#error_machine").html('Required!');
}
var machine_part = $("#machine_part").val();
if(machine_part=='')
{
	$("#error_machine_part").html('Required!');
}
var rack_location = $("#rack_location").val();
if(rack_location=='')
{
	$("#error_rack_location").html('Required!');
}

if(machine=='' || machine_part=='' || rack_location=='')
{
	
	return false;
}

});
});
</script>

<script type="text/javascript">
$(document).ready(function(){
var i=1;
 $('#addmore_btn1').click(function(){

 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-12"><div class="col-md-4"><div class="form-group"><label for="field-2" class="control-label">VENDOR </label><span id="error_item_name" style="color:red;"></span><select class="form-control select2'+i+'" name="vendor[]" id="vendor"  style="font-size:13px;"><option value="">--SELECT VENDOR--</option><?php $query = $this->db->select('id,name')->from('vendors')->where('status','1')->get(); foreach($query->result() as $vendor){?><option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label>LIST PRICE</label><input type="number" name="lprice[]" id="lprice'+i+'" value="" step="0.2" class="form-control" onkeyup="getdiscountpricefornew(0);" ></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNT</label><input type="number" name="discount[]" id="discount'+i+'" value="" step="0.2" class="form-control" onkeyup="getdiscountpricefornew('+i+');"></div></div><div class="col-md-2"><div class="form-group"><label>DISCOUNTED PRICE</label><input type="number" name="price[]" id="price'+i+'" value="" step="0.2" class="form-control" readonly required></div></div><div class="col-md-1"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><br/><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div></div><br/>');
 initializeSelect2('select2'+i);
  i++;
 });
 
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});


 function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }


  function getdiscountpricefornew(id)
{
	var lprice=$("#lprice"+id).val();
	var exdis=$("#discount"+id).val();

//alert(lprice);
	
	if((lprice!='' || lprice!=0))
	{
		
		
		if(exdis!=0)
		{
			var d=exdis/100;
			
			var lp=lprice*d;
			
			var fin=lprice-lp;
			
			$("#price"+id).val(fin.toFixed(2));
		}else{
			
			$("#price"+id).val(lprice);
		}
		
		
	}else{
		
		alert('List Price is required');
		$("#price"+id).val('');
	}
	
	
	
}

	  </script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    </body>
</html>