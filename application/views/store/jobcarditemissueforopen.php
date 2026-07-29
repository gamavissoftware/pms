<?php
//4 for userid
//5 for type
if($this->uri->segment(3)=='' || $this->uri->segment(4)=='' || $this->uri->segment(5)=='')
{
	echo "INVALID ACCESS";exit;
	
}else
{
	
	$rest=$this->db->select('a.*,b.job_card_no,b.item_id,c.instruments_name,d.part,d.fincode,d.specification,d.current_stock,d.category_id,f.category')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id','left')->join('presto_instruments c','a.machineid=c.id','left')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$this->uri->segment(3))->get();
	if($rest->num_rows()==0)
	{
		echo "NO JOBCARD FOUND";
	}
	
}

$CI =& get_instance();
$CI->load->model('Store_model');
$personname=$CI->Store_model->checktheissuename($this->uri->segment(4),$this->uri->segment(5));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>ISSUE BLOCKED ITEMS</title>

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
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
			
			.select2-container {
    width: 258px !important;
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
   


    <body>


        <!-- Navigation Bar-->
                <header id="topnav">
          <div class="text-center"><img src="<?php echo assets_url;?>images/logo-1.png"><span class="btn btn-danger" data-toggle="modal" data-target="#returnStock">Return Stock</span>
          	<span class="btn btn-warning" data-toggle="modal" data-target="#issueStock">Issue Stock</span>	<div>
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
						$jbcard=$data->job_card_no;
						$instrid=$data->item_id;
						$instrumentname=$CI->Store_model->getinstrumentname($data->item_id);
					
						}else{

						$jbcard='';
						$instrumentname='';
						$instrid=0;
						}
						
						?>
						<div class="col-md-2" style="padding-top:20px"><u><a href="<?php echo page_url;?>User/issuejobcard" style="font-size:20px;">Back</a></u></div>
                           
                           <div class="col-md-8"> 
						   <h4 class="text-center">ISSUE ITEMS FOR <?php echo $instrumentname;?> JOBCARD - <?php echo $jbcard;?></h4>
						   </div>
						   
						    <div class="col-md-2"> 
						  <span class="btn btn-warning" data-toggle="modal" data-target="#myModal">Add Missing BOM</span>
						   </div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
	

   
                        <div class="card-box table-responsive">
						
						<?php
						$rest123=$this->db->select('d.category_id,f.category,a.issued')->from('blockedstock a')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$this->uri->segment(3))->group_by('d.category_id')->get();
						
							if($rest123->num_rows()>0)
							{
							foreach($rest123->result() as $rest1231)
							{
							?>
                           <div class="col-sm-12">
						   <h4 class="text-center"><strong><?php echo $rest1231->category;?></strong></h4>
						   <form onsubmit="return validate(<?php echo $rest1231->category_id;?>);" action="<?php echo page_url;?>User/issueblockedqty/<?php echo $rest1231->category_id;?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>" method="post" id="frm<?php echo $rest1231->category_id;?>">
						  
						
				 <table class="table table-bordered potable">
  <thead>
    <tr>
	<th scope="col">#</th>
	<th scope="col">Sr. No.</th>
	<th scope="col">Item Name</th>
	<th scope="col">PR No.</th>
	<th scope="col">PO No.</th>
	<th scope="col">Current Stage</th>
	<th scope="col">Expected Delivery On</th>
	<th scope="col">Category</th>
	<!--<th scope="col">Fincode</th>-->
	<th scope="col"  style="width:200px;">Specification</th>
		<th scope="col">Size in mm</th>
	<th scope="col">Curr. Stock</th>
	<th scope="col">Blocked Details</th>
	<th scope="col" style="width:200px;">Req. Quantity</th>
	<th scope="col">Issued Quantity</th>

	<th scope="col" style="width:300px;">Remaining Issue Quantity</th>
	<th scope="col">Issued To</th>
    <th scope="col">Store Accepted</th>
    <th scope="col">Accepted By</th>
  
    </tr>
  </thead>
  <tbody>
				<?php
				$i=1;
					$rest=$this->db->select('a.*,b.job_card_no,c.instruments_name,d.part,d.fincode,d.specification,d.current_stock,d.category_id,d.size_in_mm,d.unit,f.category,a.issued')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id','left')->join('presto_instruments c','a.machineid=c.id','left')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$this->uri->segment(3))->where('d.category_id',$rest1231->category_id)->group_by('d.id')->get();
				if($rest->num_rows()>0)
				{
		
						
				foreach($rest->result() as $data)
				{
					
					$openchekboxflag=0;
				$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
				if($restyu->num_rows()>0)
				{
				foreach($restyu->result() as $restyu1);
				$unival=$restyu1->shortname;	
				}else{

				$unival='';							
				}
				
        
        /** Get Available Stock **/
        
        
       /** $restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$data->itemid)->where('jobcardid !=',$this->uri->segment(3))->where('active','1')->where('issued','0')->get();
        if($restblockedst->num_rows()>0)
        {
        foreach($restblockedst->result() as $restblockedstock);
        if($restblockedstock->blockstock<>'')
        {
        $blockedparts=$restblockedstock->blockstock;
        }else{
        $blockedparts=0;
        }
        }else
        {
            $blockedparts=0;
        } **/
        
       /** if($data->prno=='')
        {
        $restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$data->itemid)->where('jobcardid',$this->uri->segment(3))->where('active','1')->where('issued','0')->get();
        if($restblockedst->num_rows()>0)
        {
        foreach($restblockedst->result() as $restblockedstock);
        if($restblockedstock->blockstock<>'')
        {
        $blockedparts=$restblockedstock->blockstock;
        
        }else{
        $blockedparts=0;
        }
        }else{ $blockedparts=0;  }
            
            $curstock=$blockedparts;
            //$avstock=$data->current_stock;
            
        }else
        {
            $restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$data->itemid)->where('jobcardid !=',$this->uri->segment(3))->where('active','1')->where('issued','0')->get();
        if($restblockedst->num_rows()>0)
        {
        foreach($restblockedst->result() as $restblockedstock);
        if($restblockedstock->blockstock<>'')
        {
        $blockedparts=$restblockedstock->blockstock;
        }else{
        $blockedparts=0;
        }
        }else{ $blockedparts=0;  }
            
        if($data->current_stock==0)
        {
         $curstock=0;
        }else{
        $curstock=$data->current_stock-$blockedparts;
        }
          
      //  $avstock=$data->current_stock;
        } **/
        
		
		 $restblockedst=$this->db->select('sum(stock) as blockstock')->from('blockedstock')->where('itemid',$data->itemid)->where('jobcardid !=',$this->uri->segment(3))->where('active','1')->where('issued','0')->get();
        if($restblockedst->num_rows()>0)
        {
        foreach($restblockedst->result() as $restblockedstock);
        if($restblockedstock->blockstock<>'')
        {
        $blockedparts=$restblockedstock->blockstock;
        $sumblocked=$restblockedstock->blockstock;
        }else{
        $blockedparts=0;
          $sumblocked=0;
        }
        }else{ $blockedparts=0; $sumblocked=0;  }
            

        $blockedparts=0;

        if($data->current_stock==0)
        {
         $curstock=0;
        }else{
        $curstock=$data->current_stock-$blockedparts;
        if($curstock>0)
        {
            $curstock=$curstock;
        }else
        {
            $curstock=0;
        }
        }
        
        /** END **/
        ?>
	
  <input type="hidden" name="blockedstockid<?php echo $rest1231->category_id;?><?php echo $data->itemid;?>"  value="<?php echo $data->id;?>">
  <input type="hidden" name="jobcardsid" value="<?php echo $data->jobcardid;?>">
<?php
if($data->issued=='1')
{
 
	$a="style='background-color:#A8FFFF;'";
	$issuedto=$CI->Store_model->checkwhomitisissued('1',$data->itemid,$data->jobcardid);
		
	$openchekboxflag=1;
}else{
	
	$a="";
	$issuedto='';

}

$alreadyissuedstock=$CI->Store_model->checkifpreviousissueismade($data->itemid,$data->id);

$storeacceptedandissuedby=$CI->Store_model->checkifstorehasaccepted($data->itemid,$data->id);
if(count($storeacceptedandissuedby)>0)
{
    $accepted=$storeacceptedandissuedby['storeaccept'];
    $acceptedbyperson=$storeacceptedandissuedby['accby'];
}else
{
    $accepted='';
    $acceptedbyperson='';
    
}

$requireds=floatval($data->stock)-floatval($alreadyissuedstock);
?>


	<tr <?php echo $a;?>>
		<td scope="row" style="width:3%">
		
		<?php
		if($curstock>0 && $data->issued==0)
		{
		?>
		<input type="checkbox" name="checkit<?php echo $rest1231->category_id;?>[]" id="checkit<?php echo $data->itemid;?>" value="<?php echo $data->itemid;?>" onchange="openqty(<?php echo $data->itemid;?>)">
		<?php
		}else{
		if($data->issued==1)
		{
		?>
		<span style="color:red;">Issued</span>
		<?php
		 }
		 else if($curstock<=0)
		 {
		 ?>
		 <span style="color:red;">Stock Not Available</span>
        <?php
        }
        }
        ?>
		
		</td>
		<td scope="row" style="width:4%"><?php echo $i;?></td>
		<td style="width:15%"><?php echo strtoupper($data->part);?></td>
			<td style="width:5%"><?php echo $data->prno;?></td>
			<td style="width:5%"><?php echo $data->pono;?></td>
			<?php
			if($data->prno<>'')
			{
			$prstage=$CI->Store_model->getprstatus($data->prno);
			$podelidate=$CI->Store_model->checkdeliveryday($data->prno);
			}else{

			$prstage='';
			$podelidate='';
			}
			?>
			<td style="width:5%"><?php echo $prstage;?></td>
			<td style="width:5%"><?php echo $podelidate;?></td>
		<td style="width:10%"><?php echo $data->category;?></td>
		<!--<td style="width:10%"><?php echo $data->fincode;?></td>-->
		<td style="width:10%"><?php echo $data->specification;?></td>
		<td style="width:10%"><?php echo $data->size_in_mm;?></td>
		<td style="width:10%"><?php echo $curstock;?></td>

		<td style="width:10%"><?php echo $sumblocked;?><br/><a href="javascript:;"onclick="raiseht(<?php echo $data->itemid;?>);">Help Ticket?</a></td>
		<!--- CHECK IF ANY PREVIOUS ISSUE OF SAME ITEM IS DONE -->
	
		<td><?php echo floatval($requireds);?> <?php echo $unival;?>
		
		<input type="hidden" name="reqstock<?php echo $rest1231->category_id;?><?php echo $data->itemid;?>" id="reqstock<?php echo $data->itemid;?>" value="<?php echo $requireds;?>">
		</td>
		
		<?php
		/** CHECK IF CURRENT QTY IS LESS THAN REQUIRED **/
		if($curstock<$requireds)
		{
			$eligible=$curstock;
			
		}else{
			
			$eligible=$requireds;
			
		}
		/** END **/
		?>
		<!---END -->
		
		<td style="width:10%"><?php echo floatval($alreadyissuedstock);?> <?php echo $unival;?></td>
		
		<td style="width:10%">
		<div class="col-md-12 hideit<?php echo $data->itemid;?>" style="display:none;"><input type="text" name="issueqty<?php echo $data->itemid;?>" id="issueqty<?php echo $data->itemid;?>" class="form-control" onkeyup="checkforqty(<?php echo $data->itemid;?>);" style="width:100%" value="<?php echo floatval($eligible);?>" readonly></div><div class="col-md-2 hideit<?php echo $data->itemid;?>" style="display:none;"><span style="font-size:13px"><strong><?php echo $unival;?></strong></span></div></td>
		
            <td><?php echo $issuedto;?></td>
            <td><?php echo $accepted;?></td>
            <td><?php echo $acceptedbyperson;?></td>
	
	</tr>
	
<?php
$i++;
}
}
?>						
	<tr>
      <th colspan="9"></th>
	
  
	  <td>  
	  <div class="col-md-12">
	  
	  <select class="form-control" name="issuetos<?php echo $rest1231->category_id;?>" id="issueto<?php echo $rest1231->category_id;?>" style="width:300px">
		<?php
		$userdata=$CI->Store_model->getstoreusersdata($this->uri->segment(4),$this->uri->segment(5));
		if(count($userdata)>0)
		{
		?>
		<option value="<?php echo $userdata['userid'];?>" selected><?php echo $userdata['username'];?></option>
		<?php
		 
		}
		?>
		</select>
		</div>
		<div style="clear:both;height:10px"></div>
		<div class="col-md-12 text-center ">
		<input type="submit" name="save" id="gpo" value="Issue Item" class="btn btn-xs btn-success btn-sm">
		</div>
		</td>
    </tr>
   
  </tbody>
</table>
		
		</form>		

			   
						   
                        </div>
                   
	<?php
							}
	}else{
		
		echo "NO CATGEORY FOUND";
	}
	?>
	
	
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
		

	
<script>
$(document).ready(function(){
	$(".select2").select2({});
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function openqty(itemid)
{
	
	if($('#checkit' +itemid).is(":checked"))
	{
		
	$(".hideit"+itemid).css('display','');
		
	}else{
		
		$(".hideit"+itemid).css('display','none');
	}
	
	
	
}

function validate(cat)
{
	var isvalid=true;
	var favorite = [];
            $.each($("input[name='checkit"+cat+"[]']:checked"), function(){
				var v=$(this).val();
                favorite.push($(this).val());
				
				var qty=$("#issueqty"+v).val();
				if(qty=='')
				{
					alert('Issued Quantity is mandatory');
					isvalid=false;
				}else if(qty==0)
				{
					alert('Issued Quantity cannot be 0');
					isvalid=false;
				}
				
            });
			
			if(favorite.length==0)
			{
            alert('At least one item must be issued');
			isvalid=false;
			}
			
			var isto=$("#issueto"+cat).val();
			if(isto=='')
			{
				 alert('Issue to Person is mandatory');
			isvalid=false;
			}
			
			if(isvalid==false)
			{
				return false;
			}else{
				
				return true;
			}
	
	
	
}

function checkforqty(itemid)
{
	var reqst=$("#reqstock"+itemid).val();
	var issueqty=$("#issueqty"+itemid).val();
	
	if(reqst<issueqty)
	{
		
		alert('Issue Qty Cannot be greater than required qty');
		$("#issueqty"+itemid).val('');
		$("#issueqty"+itemid).focus();
		return false;
	}
	
	
	
}


function raiseht(iddd)
{

$("#myModalhelpticket").modal('show');

}
</script>


<div id="issueStock" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <form id="issueextra" action="<?php echo page_url;?>User/saveIssueStock/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?> " method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ISSUE STOCK</h4>
      </div>
      <div class="modal-body">
       <div class="row">
           
        <div class="col-md-6">
        	<div class="form-group">
				<label for="field-1" class="control-label">Select Item</label>
			        <select class="form-control sele" name="itemsForIssue" id="itemsForIssue" onchange="getUnitsForIssue()">
					</select>
			</div>
        </div> 
        
        <div class="col-md-6">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Job Card No.</label>
        		<input type="text" name="job_card_no" class="form-control" id="job_card_no" value="<?php echo $jbcard;?>" readonly>
        		<input type="hidden" name="job_card_id" value="<?php echo $this->uri->segment(3);?>">
    		</div>
        </div> 

        <div class="col-md-4">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Name</label>
       				 <input type="text" name="username" class="form-control" id="username" value="<?php echo $userdata['username'];?>" readonly>
       				 <input type="hidden" name="user_id" value="<?php echo $userdata['userid'];?>">
       		</div>
        </div>
        <div class="col-md-6">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Quantity</label>
        		<span id="error_username" style="color:red;">*</span><span id="stock" style="color:red;"></span>
        			<input type="hidden" id="hidden_stock" name="hidden_stock">
       				 <input type="number" name="quantityForIssue" class="form-control" id="quantityForIssue" value="" onkeypress="return isNumberKey(event,this)" onblur="checkStock()" step="0.01">
       		</div>
        </div>

        <script>


        	$(document).ready(function() {
  $("#quantityForIssue").on("blur", function() {
    $(this).val(parseFloat($(this).val()).toFixed(2));
  })
});

        </script>
        <div class="col-md-2">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Unit</label>
       				 <input type="text" name="unitForIssue" class="form-control" id="unitForIssue" value="" readonly>
       				 <input type="hidden" name="unitIDForIssue" id="unitIDForIssue" value="">
       		</div>
        </div>
           
       </div>
      </div>
      <div class="modal-footer">
        <input type="submit" name="sub" id="issueextrabutton" class="btn btn-success"></button>
      </div>
    </div>
    </form>

  </div>
</div>


<div id="returnStock" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <form action="<?php echo page_url;?>User/saveReturnStock/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?> " method="post" id="frmm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">RETURN STOCK</h4>
      </div>
      <div class="modal-body">
       <div class="row">
           
        <div class="col-md-6">
        	<div class="form-group">
				<label for="field-1" class="control-label">Select Item</label>
			        <select class="form-control sele" name="items" id="items" onchange="getUnits()">
					</select>
			</div>
        </div> 
        
        <div class="col-md-6">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Job Card No.</label>
        		<input type="text" name="job_card_no" class="form-control" id="job_card_no" value="<?php echo $jbcard;?>" readonly>
        		<input type="hidden" name="job_card_id" value="<?php echo $this->uri->segment(3);?>">
    		</div>
        </div> 

        <div class="col-md-6">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Name</label>
       				 <input type="text" name="name" class="form-control" id="name" value="<?php echo $userdata['username'];?>" readonly>
       				 <input type="hidden" name="user_id" value="<?php echo $userdata['userid'];?>">
       		</div>
        </div>
        <div class="col-md-4">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Quantity</label>
       				 <input type="number"  step="0.01" min="0"  name="quantity" class="form-control issueeejj" id="quantity">
       		</div>
        </div>

        <script>

        	$(document).ready(function() {
  $("#quantity").on("blur", function() {
    $(this).val(parseFloat($(this).val()).toFixed(2));
  })
});

        	function isNumberKey1(evt) {

  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $("#quantity").val().length;
    var index = $("#quantity").val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;
}

        </script>
        <div class="col-md-2">
        	<div class="form-group">
        		<label for="field-1" class="control-label">Unit</label>
       				 <input type="text" name="unit" class="form-control" id="unit" value="" readonly>
       				 <input type="hidden" name="unit_id" id="unit_id" value="">
       		</div>
        </div>
           
       </div>
      </div>
      <div class="modal-footer">
        <input type="submit" id="returnsub" name="sub" class="btn btn-success"></button>
      </div>
    </div>
    </form>

  </div>
</div>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <form action="<?php echo page_url;?>User/genratebommissinghelpticket/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(3);?> " method="post">
        <input type="hidden" name="instid" value="<?php echo $instrid;?>">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">ADD MISSING BOM</h4>
      </div>
      <div class="modal-body">
       <div class="row">
           
        <div class="col-md-6">
        <input type="text" name="itemname" class="form-control" id="itemname" required placeholder="Item Name">
        </div> 
        
        <div class="col-md-6">
        <input type="number" name="qty" class="form-control" id="qty" required placeholder="QUANTITY">
        </div> 
    
           
       </div>
      </div>
      <div class="modal-footer">
        <input type="submit" name="sub" class="btn btn-success"></button>
      </div>
    </div>
    </form>

  </div>
</div>


<!-- HELP TICKET --->
<!-- Modal -->
<div id="myModalhelpticket" class="modal fade" role="dialog">
  <div class="modal-dialog">

<form>
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Raise Helpticket to Surrender for unblocking</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
       <div class="col-md-6 form-group">
        <input type="text" name="itemname" class="form-control" id="itemnameht" required placeholder="Item Name">
        </div> 
        
        <div class="col-md-6 form-group">
        <input type="number" name="qty" class="form-control" id="qty" required placeholder="REQUIRED QTY">
        </div> 
      </div>


<div class="row">
      <div class="col-md-6 form-group">
        <input type="number" name="qty" class="form-control" id="qty" required placeholder="QUANTITY">
        </div> 

    </div>
</div>

<div style="clear:both;height:10px"></div>
      <diV class="row">
        <input type="submit" class="btn btn-success">
      </div>
    </div>
</form>
  </div>
</div>
<script>
$( document ).ready(function() {
	var item_url="<?php echo page_url;?>User/getItems/";

$('.sele').select2({ 
		placeholder: 'TYPE TO SELECT',
		minmumInputLength:4,
		allowClear: true,

        ajax: {

          url: item_url,

          dataType: 'json',

          delay: 250,

          processResults: function (data) {

            return {

              results: data

            };

          },

          cache: true

        }
	});
	
});

	function getUnits() {
		var items = $("#items").val();
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>User/getUnit/",
			data:{items:items},
			success:function(data) {

				$arr = data.split('|');
				//alert($arr);
				$("#unit_id").val($arr[0]);
				$("#unit").val($arr[1]);

				}
			});
	}
	
	
$(document).ready(function(){
$("#returnsub").attr('disabled',false);
$("#issueextrabutton").attr('disabled',false);
$("#returnsub").val('submit');
$("#issueextrabutton").val('Issue');

$("#frmm").on("submit", function(){

// $("#pageloader").fadeIn();

$("#returnsub").attr('disabled',true);

$("#returnsub").val('Please Wait...');

});//submit

$("#issueextra").on("submit", function(){

// $("#pageloader").fadeIn();

$("#issueextrabutton").attr('disabled',true);

$("#issueextrabutton").val('Please Wait...');

});//submit


});//document ready


function getUnitsForIssue() {
		var items = $("#itemsForIssue").val();
		//alert(items);
			$.ajax({
			    type:"post",
			    url:"<?php echo page_url;?>User/getItemUnit",
			    data:{items:items},
			    success:function(data){
			    	//alert(data);
			    var arr = data.split('|');
			    $("#unitIDForIssue").val(arr[0]);
			    $("#unitForIssue").val(arr[1]);
			    $("#stock").text('Current Stock ='+arr[2]);
			    $("#hidden_stock").val(arr[2]);
			    }
			    });
	}

function isNumberKey(evt, element) {

  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;
}

function checkStock() {
    var availableStock = $("#hidden_stock").val();
    var inputQty = $("#quantityForIssue").val();
    if(inputQty!=0)
    {

    if(parseFloat(inputQty) > parseFloat(availableStock)) {
         alert('Input Quantity Exceeds the Available Quantity in the stock!');
        $("#quantityForIssue").val('');  
    }
    
}else
{
 alert('Input Quantity cannot be empty or zero');
        $("#quantityForIssue").val(''); 
}
}
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
</body>
</html>
