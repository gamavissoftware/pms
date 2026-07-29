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
		<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
						 <div class="btn-group pull-right">
						  
                               
                            </div>
                           <h4 class="page-title">Issue <span style="color:red"><?php $qu = $this->db->select('stock,instruments_name')->from('presto_instruments')->where('id',$this->uri->segment(3))->get();
						   foreach($qu->result() as $row){
							   echo $row->instruments_name;
						   }?> - Current Stock: <?php echo $row->stock;?> NOS</span></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
                            <?php
                            
                            if($this->uri->segment(4)=='')
                            {
                            ?>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
								<?php
								$id = $this->uri->segment(3);
								
								?>
								
                                   <form method="post" action="<?php echo page_url;?>Store/issue_sold_item/<?php echo $id;?>"  enctype="multipart/form-data" onsubmit="return validate();">
								   
								   
								    <div class="col-md-3" >
													<div class="form-group">
														 <label for="field-2" class="control-label">USER<span style="color:red">*</span></label>
														 <span id="error_user_id" style="color:red;"></span>
														 <select class="form-control select2" class="user_id" name="user" id="user_id">
													<option value="">Select User</option>
													<?php 
													$query = $this->db->select('b.user_id,b.first_name, b.last_name')->from('system_users b')->where('b.user_status','1')->where('b.hide_profile','0')->get();
													foreach($query->result() as $user){
													?>
													<option value="<?php echo $user->user_id;?>"><?php echo $user->first_name." ".$user->last_name;?></option>
													<?php }?>
												</select>
													</div>
												</div>
	

											<div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">ITEM NAME<span style="color:red">*</span></label>
														 <span id="error_user_id" style="color:red;"></span>
														<input type="text" name="itemname" class="form-control" readonly value="<?php echo $row->instruments_name;?>">
													</div>
												</div>	
												
												
													<div class="col-md-2">
													<div class="form-group">
														 <label for="field-2" class="control-label">QTY<span style="color:red">*</span></label>
													
														 <span id="error_qty" style="color:red;"></span>
														<input type="number" min="1" name="qty" max="<?php echo $row->stock;?>" id="qty" class="form-control" value="" onblur="checkqty(<?php echo $row->stock;?>);">
													</div>
												</div>	
												<script>
												function checkqty(stock)
												{
													var qty=$("#qty").val();
													if(qty>stock)
													{
														alert('Cannot issue more than available stock');
														$("#qty").val('');
														
													}
													
												}
												
												</script>
												
												<div class="col-md-4">
												   <div class="form-group">
												       <label>MACHINE SERIAL NUMBER (SEPRATED BY COMMA)</label>
												       <input type ="text" name="machine_serial_number" id="machine_serial_number" class="form-control" value="">
												   </div> 
												</div>
												
										  <div class="col-md-3">
											<div class="form-group">
												<label>Why do you need it?<span style="color:red">*</span>  <span id="error_reason" style="color:red;"></span></label>
												
												<select class="form-control" name="whyneed" id="whyneed" onchange="getcustname(this.value);">
													<option value="">Select Type</option>
													<!--<option value="QC">QC</option>-->
													<option value="DEMO">Demo</option>
													<option value="SHIPMENT">Dispatch</option>
												</select>
											</div>
										  </div>
										  <script>
										      
										     function getcustname(vaal)
										     {
										        if(vaal=='DEMO')
										        {
										         $(".demoyes").css('display','');   
										         $("#custname").attr('required',true);
										         
										  $("#rgpno").attr('required',true);
										        
										            
										        }else
										        {
										            
										             $(".demoyes").css('display','none');   
										              $("#custname").attr('required',false);
										               $("#rgpno").attr('required',false);
										        }
										         
										         
										     }
										      
										  </script>
										  
										  
										    <div class="col-md-3">
											<div class="form-group">
												<label>Returnable?<span style="color:red">*</span> <span id="error_ret" style="color:red;"></span></label>
												<select class="form-control" name="returnable" id="returnable" onchange="checkforreturn(this.value);">
													<option value="">Select</option>
													<option value="1">Yes</option>
													<option value="2">No</option>
													
												</select>
											</div>
										  </div>
										  
										  	<div class="col-md-3 demoyes" style="display:none;">
											<div class="form-group">
											<label>Customer Name<span style="color:red">*</span> <span id="error_custname" style="color:red;"></span></label>
											<input type="text" name="custname" id="custname" value="" class="form-control">
											</div>
											</div>
											
											
											 	<div class="col-md-3 demoyes" style="display:none;">
											<div class="form-group">
											<label>RGP No.<span style="color:red">*</span> <span id="error_rgp" style="color:red;"></span></label>
											<input type="text" name="rgpno" id="rgpno" value="" class="form-control">
											</div>
											</div>
											
											
										  <script>
										  function checkforreturn(vall)
										  {
											  
											  if(vall=='1')
											  {
												$("#returnyes").css('display','');
												$("#returnyes :input").attr('required',true);
												$("#returnno").css('display','none');
												$("#returnno :input").attr('required',false);
												  
											  }else if(vall=='2')
											  {
												  
												$("#returnyes").css('display','none');
												$("#returnyes :input").attr('required',false);
												$("#returnno").css('display','');
												$("#returnno :input").attr('required',true);
												  
											  }else
											  {
												  
												$("#returnyes").css('display','none');
												$("#returnyes :input").attr('required',false);
												$("#returnno").css('display','none');
												$("#returnno :input").attr('required',false);
												  
											  }
											  
											  
										  }
										  </script>
										  
										  
										  
										
											<div class="col-md-3" id="returnyes" style="display:none;">
											<div class="form-group">
											<label>Return Date<span style="color:red">*</span> <span id="error_rdate" style="color:red;"></span></label>
											<input type="date" name="rdate" id="rdate" value="" class="form-control" min="<?php echo date('Y-m-d');?>">
											</div>
											</div>
									
											
											<div class="col-md-3" id="returnno" style="display:none;">
											<div class="form-group">
											<label>Order No.<span style="color:red">*</span> <span id="error_ono" style="color:red;"></span></label>
											<input type="text" name="ono" id="ono" value="" class="form-control">
											</div>
											</div>
											
										   </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												
											<input type="submit" class="btn btn-success" value="Update">
											</div>
										</div>
									
									</form>
                                   

                                </div>

                            </div>
                           <?php
                            }
                            ?>
                            <!-- end row -->
                            
                            
                               <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table manglesh table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Item Name</th>
									 <th>ISSUED TO</th>
									 <th>QTY</th>
									 <th>SERIAL NUMBER</th>
									 <th>ISSUE REASON</th>
									 <th>RETURN TYPE</th>
									 <th>RETURN DATE</th>
									 <th>ORDER NO</th>
									 <th>ISSUED ON</th>
									 <th>ISSUED BY</th>
									 
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
                        
						
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
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
     <script language="javascript" type="text/javascript">   

$(document).ready(function() {
	 $('.select2').select2({});

});


function validate () 
{
	$("#error_user_id").html('');
	$("#error_qty").html('');
	$("#error_reason").html('');
	$("#error_reason").html('');
	$("#error_rdate").html('');
	$("#error_ono").html('');
	$("#error_ret").html('');
	
	var u=0;
	var v=0;
	var user_id = $("#user_id").val();
if(user_id=='')
{
	$("#error_user_id").html('Required!');
}

var qty = $("#qty").val();
if(qty=='')
{
	$("#error_qty").html('Required!');
}

var whyneed = $("#whyneed").val();
if(whyneed=='')
{
	$("#error_reason").html('Required!');
}


var returnable = $("#returnable").val();
if(returnable=='')
{
	$("#error_ret").html('Required!');
}


if(returnable=='1')
{
	var rdate = $("#rdate").val();
if(rdate=='')
{
	 u =1;
	$("#error_rdate").html('Required!');
}
	
}


if(returnable=='2')
{
	var ono = $("#ono").val();
if(ono=='')
{
	 v =1;
	$("#error_ono").html('Required!');
}
	
}
if(user_id=='' || qty=='' || returnable=='' ||  v=='1' || u=='1')
{
	
	return false;
}


}
</script>
<script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pageLength": 100,
"sAjaxSource": "<?php echo page_url;?>Store/issue_item_list/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{mData:'instruments_name'},
				{ mData: 'party_name' },
				{ mData: 'qty' },
				{ mData: 'machine_serial_number' },
				{ mData: 'issuereason' },
				{ mData: 'returntype' },
				{ mData: 'returndate' },
				{ mData: 'ordertype' },	
				{ mData: 'added_on' },	
				{ mData: 'added_by' }
		]
});   
});

</script>
    </body>
</html>