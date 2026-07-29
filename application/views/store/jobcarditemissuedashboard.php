<?php

if($this->uri->segment(3)=='')
{
	echo "INVALID ACCESS";exit;
	
}else
{
	
	$rest=$this->db->select('a.*,b.job_card_no,c.instruments_name,d.part,d.fincode,d.specification,d.current_stock,d.category_id,f.category')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id','left')->join('presto_instruments c','a.machineid=c.id','left')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$this->uri->segment(3))->get();
	if($rest->num_rows()==0)
	{
		echo "NO JOBCARD FOUND";
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
							
						
								
							
						}
						
						?>
                           
                            <h4 class="page-title">ISSUE ITEMS FOR JOBCARD - <?php echo $data->job_card_no;?></h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
	

   
                        <div class="card-box table-responsive">
						
						<?php
						$rest123=$this->db->select('d.category_id,f.category')->from('blockedstock a')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.active','1')->where('a.issued','0')->where('a.jobcardid',$this->uri->segment(3))->group_by('d.category_id')->get();
						
						if($rest123->num_rows()>0)
						{
							foreach($rest123->result() as $rest1231)
							{
								
								
						?>
                           <div class="col-sm-12">
						   <h4 class="text-center"><strong><?php echo $rest1231->category;?></strong></h4>
						   <form onsubmit="return validate(<?php echo $rest1231->category_id;?>);" action="<?php echo page_url;?>Store/issueblockedqty/<?php echo $rest1231->category_id;?>" method="post" id="frm<?php echo $rest1231->category_id;?>">
						
				 <table class="table table-bordered potable">
  <thead>
    <tr>
	<th scope="col">#</th>
      <th scope="col">Sr. No.</th>
      <th scope="col">Item Name</th>
	   <th scope="col">PR No.</th>
	     <th scope="col">PO No.</th>
	  <th scope="col">Category</th>
      <th scope="col">Fincode</th>
	   <th scope="col">Specification</th>
	   <th scope="col">Curr. Stock</th>
      <th scope="col">Req. Quantity</th>
	  
	  <th scope="col">Issue Quantity</th>
  
    </tr>
  </thead>
  <tbody>
				<?php
				$i=1;
					$rest=$this->db->select('a.*,b.job_card_no,c.instruments_name,d.part,d.fincode,d.specification,d.current_stock,d.category_id,d.unit,f.category')->from('blockedstock a')->join('order_instruments b','a.jobcardid=b.id','left')->join('presto_instruments c','a.machineid=c.id','left')->join('machine_parts_with_picture d','a.itemid=d.id','left')->join('presto_machine_part_category f','d.category_id=f.id','left')->where('a.jobcardid',$this->uri->segment(3))->where('d.category_id',$rest1231->category_id)->where('a.active','1')->where('a.issued','0')->get();
				if($rest->num_rows()>0)
				{
		
						
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
        
        if($data->prno=='')
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
            $avstock=$data->current_stock;
            
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
          
        $avstock=$data->current_stock;
        }
        
        
        /** END **/
        

		
				?>
	
  <input type="hidden" name="blockedstockid<?php echo $rest1231->category_id;?><?php echo $data->itemid;?>"  value="<?php echo $data->id;?>">
  <input type="hidden" name="jobcardsid" value="<?php echo $data->jobcardid;?>">

	<tr>
		<th scope="row" style="width:3%">
		
		<?php
		if($curstock>0)
		{
		?>
		<input type="checkbox" name="checkit<?php echo $rest1231->category_id;?>[]" id="checkit<?php echo $data->itemid;?>" value="<?php echo $data->itemid;?>" onchange="openqty(<?php echo $data->itemid;?>)">
		<?php
		}else{
		?>
		<span style="color:red;">Stock Not Available</span>
		<?php
		}
		?>
		
		</th>
		<th scope="row" style="width:4%"><?php echo $i;?></th>
		<td style="width:15%"><?php echo strtoupper($data->part);?></td>
			<td style="width:5%"><?php echo $data->prno;?></td>
			<td style="width:5%"><?php echo $data->pono;?></td>
		<td style="width:10%"><?php echo $data->category;?></td>
		<td style="width:10%"><?php echo $data->fincode;?></td>
		<td style="width:10%"><?php echo $data->specification;?></td>
		<td style="width:10%"><?php echo $avstock;?></td>
		
		<!--- CHECK IF ANY PREVIOUS ISSUE OF SAME ITEM IS DONE -->
		<?php
		$alreadyissuedstock=$CI->Store_model->checkifpreviousissueismade($data->itemid,$data->id);
		
		
		$requireds=floatval($data->stock)-floatval($alreadyissuedstock);
		
		?>
		<td style="width:10%"><?php echo floatval($requireds);?> <?php echo $unival;?>
		
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
		
		<td style="width:10%">
		<div class="col-md-10 hideit<?php echo $data->itemid;?>" style="display:none;"><input type="text" name="issueqty<?php echo $data->itemid;?>" id="issueqty<?php echo $data->itemid;?>" class="form-control" onkeyup="checkforqty(<?php echo $data->itemid;?>);" style="width:100%" value="<?php echo floatval($eligible);?>" readonly></div><div class="col-md-2 hideit<?php echo $data->itemid;?>" style="display:none;"><span style="font-size:13px"><strong><?php echo $unival;?></strong></span></div></td>
	
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
	  <select class="form-control select2" name="issuetos<?php echo $rest1231->category_id;?>" id="issueto<?php echo $rest1231->category_id;?>">
		<option value="">Issue to</option>
		<?php
		$userdata=$CI->Store_model->getusersdata();
			
		if(count($userdata)>0)
		{
		 foreach($userdata as $userdata1)
		 {
			// echo "<pre>"; print_r($userdata1);exit;
			 
		?>
		<option value="<?php echo $userdata1->user_id;?>"><?php echo $userdata1->first_name." ".$userdata1->last_name;?></option>
		<?php
		 }
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
		

	 


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
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
</script>
</body>
</html>