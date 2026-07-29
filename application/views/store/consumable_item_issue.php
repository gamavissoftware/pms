<?php
$CI =& get_instance();
$CI->load->model('Store_model');

$UI =& get_instance();
$UI->load->model('Fms_model');
$userid=$this->uri->segment(3);
$usertype=$this->uri->segment(4);
if($userid=='' || $usertype=='')
{
	echo "INVALID ACCESS";exit;
}

$personname=$CI->Store_model->checktheissuename($this->uri->segment(3),$this->uri->segment(4));
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


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
			#fixedbutton {
			position: fixed;
			bottom: 0px;
			right: 47%;
 
			}
		</style>

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <div class="text-center"><img src="<?php echo assets_url;?>images/logo-1.png"><div>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper" style="margin-left:0px">
			<div class="container-fluid">
			<div class="row">
			
			<div class="col-md-4" style="padding-top:20px"><u><a href="<?php echo page_url;?>User/issuejobcard" style="font-size:20px;">Back</a></u></div>

			<div class="col-md-4"> 
			<h4 class="page-title" style="font-size:16px;">ISSUE CONSUMABLES ITEMS FOR MACHINES TO <?php echo $personname;?></h4>
			</div>
			
			
			<div class="col-md-4"> 
			<h4 class="page-title" style="font-size:16px;"><span class="btn btn-danger" data-toggle="modal" data-target="#myModal">Raise Missing Item Help Ticket</span></h4>
			</div>
			
			</div>
                <!-- Page-Title -->
                
				<div class="row"> 
				<div class="col-md-12">				
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				</div>
				</div>

			

				<form  name="frm" method="post" action="<?php echo page_url;?>User/consumableissue" onsubmit="return validatecheck();">
                 <div class="row">
                    <div class="col-sm-12">
                        
                        <div class="card-box table-responsive">
                           
                            <table id="example" class="table manglesh table-bordered" style="font-size: 14px;">
                                <thead>
                                <tr>
								<th>SR NO</th>
								<th>ISSUE</th>
								<th>ISSUE QTY</th>
								<th>PICTURE</th>
								<th>PART NAME</th>
								<th>CATEGORY</th>
								<th>SPECIFICATION</th>

								<th>FIN CODE</th>
								<th>UNIT</th>

								<th>STOCK</th>
									
									 
									
									
									
                                    
                                </tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
							
							
                        </div>
                    </div>
                </div>
				<input type="hidden" name="usertype" value="<?php echo $usertype;?>">
				<input type="hidden" name="userid" value="<?php echo $userid;?>">
				<div class="row text-center">
				<input type="submit" class="btn btn-warning text-center" id="fixedbutton" style="width:300px" value="Issue Items">
				</div>
				</form>
                <!-- end row -->



                <!-- Footer -->

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

       

        <script>
		
		
		
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"paging":false,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>User/consumable_listforissue",
"aoColumns": [
			{ mData: 'sr_no' } ,
			{ mData: 'issue' },
			{ mData: 'qtybox' },
				{ mData: 'image' },
				{ mData: 'machine_part' },
				{ mData: 'category' },
				{ mData: 'specification' },
			
				{ mData: 'fincode' },
				{ mData: 'unit' },
				
				{ mData: 'stock' }
				
				
				

				
				
		]
  
});   



});


function openqtybox(itemid,i)
{
	if($('#checkitem'+itemid).is(":checked"))
	{
	$("#qtybox"+itemid+i).css('display','block');
	$("#qtybox"+itemid+i).attr('required',false); 
	}else
	{
		$("#qtybox"+itemid+i).css('display','none');
		$("#qtybox"+itemid+i).attr('required',false);
	}
	
}



</script>


<script>
		

		function validatecheck()
		{
				var flag = false;
			var chelen=$('[name="checkitem[]"]:checked').length;
			
			if(chelen>0)
			{
				
			$.each($("input[name='checkitem[]']:checked"), function(){
			var va=$(this).val();
			var qty=$(".qtyb"+va).val();
			if($.trim(qty)=='')
			{
			flag=true;
			}
			
			});
			
			if(flag==1)
			{
			$("#fixedbutton").attr('disabled',false);
			alert('Please Enter Quantity for selcted item(s)');
			return false;
			}else{

			$("#fixedbutton").attr('disabled',true);
			return true;

			}
			
				
			}else{
				
						
				alert('No Item Selected');
				return false;		
			}
			
		}
		
		
		
		function checkforqty(itemid,i)
	{
		var entere=$("#qtybox"+itemid+i).val();
		var current=$("#currentstock"+itemid+i).val();
		
		if(parseFloat(entere)>parseFloat(current))
		{
			alert('Issue Qty cannot be more than Available Stock');
			var entere=$("#qtybox"+itemid+i).val('');
			
		}
	
	}
		
</script>

 
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content--> 
    <form action="<?php echo page_url;?>User/genrateconsumableitemmissinghelpticket/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" name="frm" id="frm" method="post">
    <input type="hidden" name="personname" value="<?php echo $personname;?>">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Raise help ticket for missing item</h4>
      </div>
      <div class="modal-body">
        <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Missing Item Name & Description</label>
        <textarea  name="itemname" id="itemname" value="" class="form-control" required></textarea>
        </div>
        </div>
        
        
        </div>
        </div>
      <div class="modal-footer">
        <input type="submit" name="sub" id="sub" class="btn btn-success">
      </div>
    </div>
    </form>

  </div>
</div>
    </body>
</html>