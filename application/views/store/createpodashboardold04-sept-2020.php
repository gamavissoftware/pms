<?php

if($this->uri->segment(3)=='')
{
	echo "NO CORRESPONDING PR FOUND";exit;
	
}else
{
	
	$rest=$this->db->select('a.masterid,a.prraisereason,a.type,a.sourceid,a.source,a.jobcardid,d.part as machine_part,d.fincode,d.specification,d.current_stock,e.first_name,e.last_name,a.itemid,a.unit,a.qty,e.department_id')->from('purchase_request a')->join('machine_parts_with_picture d','d.id=a.masterid')->join('system_users e','e.user_id=a.addedBy')->where('a.prno',$this->uri->segment(3))->order_by('d.part','ASC')->get();
	if($rest->num_rows()==0)
	{
		echo "PR DETAILS NOT FOUND";
	}
	
}

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

        <title>CREATE PO</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
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
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
}
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}
</style>
    </head>
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
						<?php
						
						if($rest->num_rows()>0)
						{
							foreach($rest->result() as $data);
							
							$jobcard='';
									
									if($data->source==1)
										{
											
											
										$job=$this->db->select('job_card_no,item_id as machineid')->from('order_instruments')->where('id',$data->jobcardid)->get();
										if($job->num_rows()>0)
										{
										foreach($job->result() as $job1);

										$jobcard='Jobcard-'.$job1->job_card_no;
										$macid=$job1->machineid;

										}else{

										$jobcard='';
										$macid=0;
										}
										}else if($data->source==2)
										{
										/** Intend **/



										$jobcard='INDENT- IND'.$data->sourceid;
										$macid=0;


										}else if($data->source==3)
										{
										/** Intend **/



										$jobcard='AUTO PR';
										$macid=0;


										}
							
						}
						
						?>
                           
                            <h4 class="page-title">CREATE PO DASHBOARD- PR NO. <?php echo $this->uri->segment(3);?> | SOURCE- <?php echo $jobcard;?> | INDENTER REF.- <?php echo ucfirst($data->first_name);?> <?php echo ucfirst($data->last_name);?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
	

   
                        <div class="card-box table-responsive">
                           <div class="col-sm-12">
						   <form onsubmit="return validate();" action="<?php echo page_url;?>Store/generatepo" method="post" action="">
						   <input type="hidden" name="pr" value="<?php echo $this->uri->segment('3');?>">
						   	 <input type="hidden" name="source" value="<?php echo $data->source;?>">
							
  <input type="hidden" name="jobcardno" value="<?php echo $data->jobcardid;?>">
  <input type="hidden" name="instrumentid" value="<?php echo $macid;?>">
  <input type="hidden" name="type" value="<?php echo $data->type;?>">
  
				 <table class="table table-bordered potable">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Item</th>
      <th scope="col">Finsys Code</th>
	   <th scope="col">Specification</th>
      <th scope="col">Quantity</th>
      <!--<th scope="col">Category</th>-->
	  <th scope="col">Supplier</th>
	  <th scope="col">Price</th>
	  <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody>
  <?php
						$i=1;
						$cost=array();
						foreach($rest->result() as $data)
						{
							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
							if($restyu->num_rows()>0)
							{
								foreach($restyu->result() as $restyu1);
								$unival=$restyu1->shortname;
							}else{
							
$unival='';							
							}
							
							$suppliers=$CI->Store_model->getallsupplier($data->itemid,$data->masterid);
						
							?>
	<input type="hidden" name="currentstock<?php echo $data->masterid;?>" value="<?php echo $data->current_stock;?>">
  <input type="hidden" name="item[]" class="itemid<?php echo $i;?>" value="<?php echo $data->masterid;?>">
  <input type="hidden" name="unit<?php echo $data->masterid;?>" value="<?php echo $data->unit;?>">


    <tr>
      <th scope="row"><?php echo $i;?></th>
      <td style="width:15%"><?php echo strtoupper($data->machine_part);?></td>
      <td style="width:10%"><?php echo $data->fincode;?></td>
      <td style="width:10%"><?php echo $data->specification;?></td>
      <td style="width:10%"><?php echo floatval($data->qty);?> <?php echo $unival;?>
	  <input type="hidden" class="qtyval<?php echo $i;?>" name="qty<?php echo $data->masterid;?>" id="qty<?php echo $data->masterid;?>" value="<?php echo floatval($data->qty);?>">
	  </td>
	  <td style="width:30%">
	   <?php
	  if(count($suppliers)>0)
	  {
		  $col="12";
	  }else{
		  $col="12";
	  }
	  ?>
	  <div class="col-md-<?php echo $col;?>">
	  <select name="supplier[]" id="supplier<?php echo $data->masterid;?>" class="form-control sup" style="width:60%" onchange="getsupplierprice('<?php echo $data->itemid;?>','<?php echo $data->masterid;?>','<?php echo $i;?>')">
	  <option value="">Select Supplier</option>
	 <?php
	 if(count($suppliers)>0)
	 {
		 foreach($suppliers as $suppliersdata)
		 {
	 ?>
	  
	  <option  value="<?php echo $suppliersdata->id;?>"><?php echo $suppliersdata->name;?></option>
	  <?php
		 }
	 }
	 ?>
	  </select>
	 
	  
	 
	  <?php
	  if(count($suppliers)==0)
	  {
	  ?>
	  <a href="<?php echo page_url;?>Vendor/vendorform/<?php echo $this->uri->segment(3);?>/<?php echo $data->masterid;?>"><span class="btn btn-warning btn-xs">Add Vendors Quotes</span></a>
	  <?php
	  }
	  ?>
	   </div>
	  </td>
	 <!-- <td>@mdo</td>-->
	  <td><input type="text" name="itemprice<?php echo $data->masterid;?>" id="itemprice<?php echo $data->masterid;?>" class="form-control itempaisa<?php echo $i;?>" readonly></td>
	  <td><input type="text" name="totalitemprice<?php echo $data->masterid;?>" id="totalitemprice<?php echo $data->masterid;?>" class="form-control" readonly></td>
    </tr>
	
<?php
$i++;
}
?>						
	<tr>
      <th colspan="7"></th>
  
	  <td><input type="submit" name="save" id="gpo" value="Generate PO" class="btn btn-success pull-right btn-sm"></td>
    </tr>
   
  </tbody>
</table>
		</form>				   
<script>

function getsupplierprice(partid,masterid,rowid)
{
	var suppid=$("#supplier"+masterid).val();
		
	$.ajax({
	type:"post",
	url:"<?php echo page_url;?>Store/getitemprice",
	data:"suppid="+suppid+"&partid="+partid+"&masterid="+masterid,
	success:function(data){
		if(data=="NA")
		{
			alert('Price Not Set');
			$("#totalitemprice"+masterid).val('');
			$("#itemprice"+masterid).val('');
		}else{
			
			var qtyu=$("#qty"+masterid).val();
			var total=qtyu*data;
			$("#totalitemprice"+masterid).val(total);
			$("#itemprice"+masterid).val(data);
			
			
		}
		
		getuniquesupp(suppid,rowid);
	}
	});
		
	
	
	
}


function getuniquesupp(suppid,rowid)
{
	$("#gpo").attr('disabled',true);
	
	/** GET ALL ROW INPUT AND CHANGE THERE NAME **/
	var itemname="item"+suppid+"[]";
	$(".itemid"+rowid).attr('name',itemname);
	/**var qtname="qty"+suppid+"[]";
	$(".qtyval"+rowid).attr('name',qtname);
	var pri="itemprice"+suppid+"[]";
	$(".itempaisa"+rowid).attr('name',pri); **/
	$("#gpo").attr('disabled',false);
	/** END **/
	
	
	
	
	
}

function validate()
{
	
	var isValid;
$(".potable :input").each(function() {
   var element = $(this);
   if (element.val() == "") {
     
	  isValid=1;
   }
});

if(isValid==1)
{
	$("#gpo").attr('disabled',true);
	 alert('All Fields are mandatory');
	return false;
}else{
	
	$("#gpo").attr('disabled',false);
	return true;
	
}
	
}

</script>						   
						   
						   
						   
						   
						   
						   
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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		

	 


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</body>
</html>