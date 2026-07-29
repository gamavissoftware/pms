<?php 
$CI =& get_instance();
$CI->load->model('Store_model');
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
							<h4 class="page-title">ACCEPT/ REJECT AND DEBIT NOTE (<span style="color:red;"><?php echo $this->uri->segment(4);?></span>)</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form method="post" action="<?php echo page_url;?>Store/update_accept_reject_qty_po/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>"  onsubmit="return validateqty()";>
						
						
						<?php 
							$rest=$this->db->select('a.pono,sum(a.recqty) as totalqty, a.id,b.part,a.itemid, c.unit,c.vendor')->from('mrn a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('purchase_order c','a.pono=c.pono','left')->where('a.pono',$this->uri->segment(4))->group_by('a.itemid')->get();
							foreach($rest->result() as $row){
								$unitname=$CI->Store_model->getunit($row->unit);
								
						?>
						<input type="hidden" value="<?php echo $row->id;?>" name="recordid[]">
						<input type="hidden" value="<?php echo $row->itemid;?>" name="itemid[]">
						<input type="hidden" name="supplier" value="<?php echo $row->vendor;?>">
						<input type="hidden" name="unit" value="<?php echo $row->unit;?>">
                            <div class="row">
								   <div class="col-md-12">
								   <div class="col-md-4">
													<div class="form-group">
														 <label for="field-2" class="control-label">ITEM NAME<span style="color:red">*</span></label>
														 <span id="error_item_name" style="color:red;"></span>
														 <input type="text" class="form-control" name="item_name[]" id="item_name"  value="<?php echo $row->part;?>" readonly>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">RECEIVE QTY IN <?php echo strtoupper($unitname);?> <span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="approved_qty" id="rqty"  value="<?php echo floatval($row->totalqty);?>" readonly>
													</div>
												</div>
												
												
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">APPROVED QTY IN <?php echo strtoupper($unitname);?><span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="approved_qty" id="approved_qty"  value="0" required>
													</div>
												</div>
												
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">REJECT QTY IN <?php echo strtoupper($unitname);?> <span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <input type="number" class="form-control" name="reject_qty" id="reject_qty"  value="0"  required>
													</div>
												</div>
												
												
												 <div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">Challan Type?<span style="color:red">*</span></label>
														 <span id="error_qty" style="color:red;"></span>
														 <select class="form-control" name="chtype" id="chtype" >
														 <option value="">Select</option>
														 <option value="1">Rejection Challan</option>
														 <option value="2">Debit Note</option>
														 </select>
													</div>
												</div>
												
												
										
											</div>
											</div>
							<?php }?>
											<div class="row">
											
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update">
											</div>
										</div>
									
									

                                </div></form>
                                   
								   

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
$("#save").click(function() {
var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}

var qty = $("#qty").val();
if(qty=='')
{
	$("#error_qty").html('Required!');
}

var min_qty = $("#min_qty").val();
if(min_qty=='')
{
	$("#error_min_qty").html('Required!');
}
var unit = $("#unit").val();
if(unit=='')
{
	$("#error_unit").html('Required!');
}


var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(item_name=='' || qty=='' || min_qty=='' || unit=='' || status=='' )
{
	
	return false;
}

});
});


function validateqty()
{
	var recvqty=$("#rqty").val();
	
	var appqty=$("#approved_qty").val();

	var rejectqty=$("#reject_qty").val();
	
	var chtype=$("#chtype").val();
	

	if(recvqty=='' || appqty==''  || rejectqty=='')
	{
		
		alert('All fields must be filled');
		return false;
	}else
	{
		if(rejectqty>0 && chtype=='')
		{
			alert('Rejection Challan Type Required')
			return false;
		}else{
		var tot=parseInt(appqty)+parseInt(rejectqty);
		
		if(tot!=recvqty)
		{
			alert('Sum of Approved, Reject must be equal to recieved qty');
			return false;
		}else{
			
			return true;
		}
		
		}
		
	}
	
	
}
</script>

    </body>
</html>