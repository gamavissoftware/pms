<?php
if($this->uri->segment(3)=='')
{
	echo "NO CORRESPONDING PR FOUND";exit;
	
}else
{
	
	$rest=$this->db->select('a.source,a.sourceid,a.jobcard as jobcardid,d.item_name as machine_part,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,a.vendor,a.price,a.addedOn as orderdate,a.unit')->from('purchase_order a')->join('house_keeping_items
 d','d.id=a.itemid')->join('system_users e','e.user_id=a.addedBy')->where('a.pono',$this->uri->segment(3))->where('a.completed','0')->where('a.gateentrycomplete','0')->order_by('d.item_name','ASC')->get();
	if($rest->num_rows()==0)
	{
		echo "PR DETAILS NOT FOUND"; exit;
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

        <title>CREATE MRN</title>

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


										}
							
							$vendorname=$CI->Store_model->getvendorname($data->vendor);
							$gnonext=$CI->Store_model->getnextgetentryno();
						}
						
						?>
                           
                            <h4 class="page-title">MRN DASHBOARD- PO NO. <?php echo $this->uri->segment(3);?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
	

   
                        <div class="card-box table-responsive">
                           <div class="col-sm-12">
						   <form onsubmit="return validate();" action="<?php echo page_url;?>Store/creategeneralgateentry" method="post" action="">
						   <input type="hidden" name="po" value="<?php echo $this->uri->segment('3');?>">
						   	 <input type="hidden" name="source" value="<?php echo $data->source;?>">
							
<input type="hidden" name="jobcardno" value="<?php echo $data->jobcardid;?>">
  <input type="hidden" name="instrumentid" value="<?php echo $macid;?>">
  <input type="hidden" name="gateentryno" value="<?php echo $gnonext;?>">
  <input type="hidden"  name="vendor" value="<?php echo $data->vendor;?>">
				 <table class="table table-bordered potable">
  <thead>
    <tr>
	  <th scope="col">#</th>
	<th scope="col">Recv.</th>
    
      <th scope="col">Item</th>
    
	   <th scope="col">Previous Inward</th>
      <th scope="col">Req. Qty</th>
      <!--<th scope="col">Category</th>-->
	  <th scope="col">Recvd. Qty</th>
	 
    </tr>
  </thead>
  <tbody>
  <?php
						$i=1;
						$cost=array();
						foreach($rest->result() as $data)
						{
							
							/** **/
							$prevqty=$CI->Store_model->checkifanypreviousqtyisinwarded($data->itemid,$this->uri->segment(3));
							/** END **/
							
							
							
							
							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
							if($restyu->num_rows()>0)
							{
							foreach($restyu->result() as $restyu1);
							$unival=$restyu1->shortname;
							}else{

							$unival='';							
							}
							
			
						
							?>
		<tr id="rowfor<?php echo $data->itemid;?>">
	
      <th scope="row" style="width:5%"><?php echo $i;?></th>
	  <th scope="row" style="width:5%"><input type="checkbox" name="item[]" class="itemid<?php echo $i;?> allcheck" value="<?php echo $data->itemid;?>" onchange="displayqty(<?php echo $data->itemid;?>,<?php echo $i;?>);"></th>
      <td style="width:15%"><?php echo strtoupper($data->machine_part);?></td>
   
	   <td style="width:10%">
	   <?php
	   if($prevqty<>0)
	  {
		?>
		<?php echo $prevqty;?> <?php echo $unival;?> Already Inwarded;
		
		<?php
	  }else{ echo "NA"; } 
	  ?>
	   
	   </td>
	  <?php
	  if($prevqty<>0)
	  {
	  $reqty=floatval($data->qty)-$prevqty;
	  }else{
		  $reqty=floatval($data->qty);
	  }
	  ?>
      <td style="width:10%"><?php echo $reqty;?> <?php echo $unival;?>
	   <input type="hidden"  name="unit<?php echo $data->itemid;?>" value="<?php echo $data->unit;?>">
	   <input type="hidden"  name="reqqty<?php echo $data->itemid;?>" value="<?php echo $reqty;?>">
	   
	
	  </td>
	  <td style="width:10%"><input type="text" placeholder="Recieved QTY" name="recvqty<?php echo $data->itemid;?>" id="recvqty<?php echo $data->itemid;?><?php echo $i;?>" class="form-control" style="display:none" onkeypress="return isNumberKey(event);" onkeyup="checkmaxqty('<?php echo $data->itemid;?>','<?php echo $i;?>','<?php echo $reqty;?>');"></td>

	
    </tr>
	
<?php
$i++;
}
?>						
	<tr>
      <th colspan="5"></th>
  
	  <td><input type="submit" name="save" id="gpo" value="Inward Items" class="btn btn-success pull-right btn-sm"></td>
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
	$("#gpo").attr('disabled',false);
	
var isValid;
if($("input.allcheck:checkbox:checked").length>0)
{	
$("input.allcheck:checkbox:checked").each(function() {
   var element = $(this).val();
   var dd=$("input[name=recvqty"+element+"]").val();
  if (dd=="") {
     
	  isValid=1;
   }
});

}else{
	
	isValid=1;
}

if(isValid==1)
{
	//$("#gpo").attr('disabled',true);
	 alert('All Fields are mandatory');
	return false;
}else{
	
	//$("#gpo").attr('disabled',false);
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
});//document ready'

function displayqty(itemid,row)
{
	if($('.itemid' + row).is(":checked"))
	{
		$("#recvqty"+itemid+row).css('display','');
		$("#recvqty"+itemid+row).attr('required',true);
		
	}else{
		
			$("#recvqty"+itemid+row).css('display','none');
		$("#recvqty"+itemid+row).attr('required',false);
	}
	
	
}

  function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
	   
	  function checkmaxqty(itemid,rowid,reqqty1)
	   {
		  
		   var recvq=$("#recvqty"+itemid+rowid).val();
		  
	
		   if(parseFloat(recvq)>parseFloat(reqqty1))
		   {
			   alert('Recieved QTY cannot be more than Required Qty');
			  $("#recvqty"+itemid+rowid).val('');
			   $("#recvqty"+itemid+rowid).focus();
			   return false;
			   
			   
		   }
		   
		   
	   }
</script>
</body>
</html>