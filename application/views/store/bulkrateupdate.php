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
        	<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<style>
	
  #mybutton {
  position: fixed;
  bottom: -4px;
  right: 10px;
}
		</style>

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
				font-size:11px;
			}
</style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
<?php
	$rytyu=$this->db->select('a.id')->from('machine_parts_with_picture  a')->where('a.up','0')->get();
										$qyew = $rytyu->num_rows();
?>

        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						<h4 class="page-title">SET RATE FOR ITEMS - REMAINING ITEMS <span style="color:red;"><?php echo $qyew;?></span></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<?php 
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				
				$query = $this->db->select('qty, vendor, price')->from('ims_header_permission')->where('user_id',$user_id)->get();
					foreach($query->result() as $row);
				
				?>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
						<form method="post" action="<?php echo page_url;?>Store/bulkupdate" class="frm">
                            <table class="table manglesh table-striped table-bordered">
                                <thead>
                                <tr>
									
									<th>CHECK</th>
									<th>PART NAME</th>
									<th>SPECIFICATION</th>
									<th>FIN CODE</th>
									<th style="width:60%">VENDOR RATE</th>
								 </tr>
                                </thead>
								<tbody>
								
								<?php
								
									$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture')->from('machine_parts_with_picture  a')->where('a.up','0');
										$query = $this->db->get();
                                        $res = $query->result();
                                        $i=0;
                                        $query12 = $this->db->select('id,name')->from('vendors')->where('status','1')->get();
                                        foreach($res as $row){
								?>
								<tr>
								
								<td><input type="checkbox" name="checkit[]" id="che<?php echo $i;?>" value="<?php echo $row->id;?>" onchange="checkthis(<?php echo $i;?>)";></td>
								<td><?php echo $row->part;?></td>
								<td><?php echo $row->specification;?></td>
								<td><?php echo $row->fincode;?></td>
								<td>
								<div class="vend<?php echo $i;?>" style="display:none">
								<div class="col-md-4">
								<div class="form-group">
								<label for="field-2" class="control-label">VENDOR </label>
								<span id="error_item_name" style="color:red;"></span>
								<select class="form-control select2" name="vendor<?php echo $row->id;?>" id="vendor<?php echo $row->id;?>"  style="font-size:13px;"
								><option value="" disabled  selected>--SELECT VENDOR--</option>
								<?php  foreach($query12->result() as $vendor){?>
								<option value="<?php echo $vendor->id;?>"><?php echo $vendor->name;?></option>
								<?php }?>
								</select>
								</div>
								
								
								</div>
							
								
								<div class="col-md-3">
													<div class="form-group">
														<label>LIST PRICE</label>
														<input type="text" name="lprice<?php echo $row->id;?>" id="lprice<?php echo $i;?>" value="" step="0.2" class="form-control" onkeyup="getdiscountpricefornew(<?php echo $i;?>);" >
													</div>
												</div>
								
								
								<div class="col-md-2">
													<div class="form-group">
														<label>DISCOUNT</label>
														<input type="text" name="discount<?php echo $row->id;?>" id="discount<?php echo $i;?>" value="" step="0.2" class="form-control" onkeyup="getdiscountpricefornew(<?php echo $i;?>);">
													</div>
												</div>
												
												<div class="col-md-2">
													<div class="form-group">
														<label>D. PRICE</label>
														<input type="text" name="price<?php echo $row->id;?>" id="price<?php echo $i;?>" value="" step="0.2" class="form-control" readonly >
													</div>
												</div>
								
								
								
								</div>
								
								
								</td>
								
									
								</tr>
					<?php
					$i++;
					}
					?>
								
								</tbody>
                                
                            </table>
                       
					   
					   <input type="submit"  name="sub" value="Update" class="btn btn-success" id="mybutton">
					   </form>
					   
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
  <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       <script>
	   
	   function checkthis(id)
	   {
		  if($('#che'+ id).is(":checked"))
		  {
			  
			  $(".vend"+id).css('display','block');
			  $(".vend"+id+" :input").attr('required',true);
		  }else
		  {
			  
			    
			  $(".vend"+id).css('display','none');
			  $(".vend"+id+" :input").attr('required',false);
			
			  
		  }
		   
		     $(".select2-offscreen").attr('required',false);
		   
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
	
	$(document).ready(function() {
	$('.select2').select2({ });
	
	});
	
	   </script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>		
		

 </body>
</html>