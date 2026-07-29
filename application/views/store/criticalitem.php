<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>CRITICAL ITEMS BELOW MINIMUM LEVEL</title>

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
<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
				font-size:11px;
			}
			
			.backgroundcolorblack
			{
				background-color: #929292;
				color:#000;
				
			}
			
			.backgroundcolorred
			{
				background-color: #ff8686f7;
				color:#000;
				
			}
			
			
			.backgroundcoloryellow
			{
				background-color: #ffff827d;
				color:#000;
				
			}
			
			
			.backgroundcolorgreen
			{
				background-color: #95FF95;
				color:#000;
				
			}
			
			
			.backgroundcolorblue
			{
				background-color: #87cefa;
				color:#000;
				
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
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        
                            <div class="col-md-4"> <h4 class="page-title">Critical Items Below Minimum Level</h4></div>
                            
                           
                           
                      
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<?php 
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				
				$query = $this->db->select('qty, vendor, price')->from('ims_header_permission')->where('user_id',$user_id)->get();
				if($query->num_rows()>0)
				{
					foreach($query->result() as $row);
					
					$a=1;
				}else
				{
				$a=0;
				}
				
				?>

                 <div class="row">
                    <div class="col-sm-12">
                        
                        <div class="card-box table-responsive">
                           
                            <table id="example" class="table manglesh table-bordered" style="font-size: 14px;">
                                <thead>
                                <tr>
                                    <th>SR NO</th>
									<th>PICTURE</th>
									<th>PART NAME</th>
									 <th>CATEGORY</th>
									 <th>SPECIFICATION</th>
									 <th>MAKE</th>
									 <th>SIZE IN MM</th>
									 <th>MATERIAL </th>
									 <th>RAW/BOP</th>
									 <th>FIN CODE</th>
									 <th>UNIT</th>
									 <th>STOCK</th>
									 <th>MIN STOCK</th>
									 <th>MIN STOCK STATUS</th>
									<?php if($a==1)
									{
									?>
									 <?php if($row->qty=='1'){?>
									 <th>QTY</th>
									 <?php }?>
									 
									 <th>RACK LOCATION</th>
									 <?php if($row->vendor=='1'){?>
									 <th style="width:400px">VENDOR/PRICE</th>
									 <?php }
									 
									 }else
									 {
									 ?>
									 
									  <th>QTY</th>
									 <th>RACK LOCATION</th>
									 <th>VENDOR/PRICE</th>
									 <?php
									 }?>
									
									</tr>
                                </thead>
								<tbody></tbody>
                                
                            </table>
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
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Store/criticallevelitems",
"aoColumns": [
			{ mData: 'sr_no' } ,
				{ mData: 'image' },
				
				{ mData: 'machine_part' },
				{ mData: 'category' },
				{ mData: 'specification' },
				{ mData: 'makes' },
				{ mData: 'size_in_mm' },
				{ mData: 'material' },
				{ mData: 'raw_bop' },
				{ mData: 'fincode' },
				{ mData: 'unit' },
				{ mData: 'stock' },
				{ mData: 'minstock' },
				{ mData: 'minstockstatus' }
				<?php if($a==1)
				{
				?>
				 <?php if($row->qty=='1'){?>
				,{ mData: 'qty' }
				 <?php } ?>

				,{ mData: 'location' }
 <?php if($row->vendor=='1'){?>
				,{ mData: 'vendor' }
 <?php
 }
 }else
 {
 ?>
,{ mData: 'qty' }
,{ mData: 'location' }
,{ mData: 'vendor' }
<?php
}
?>


				
				
				
				
		],"initComplete": function(settings, json) {
  
   getcolors()
  }
  
});   


 $('#example').on('draw.dt', function() {
    // do action here

    //getcolors();
});  

  $('#example').on('search.dt', function() {
   
   // getcolors();
});  

});

</script>
		
		
<script language="javascript" type="text/javascript">   

$(document).ready(function() {
$("#save").click(function() {
var machine_part = $("#machine_part").val();
if(machine_part=='')
{
	$("#error_machine_part").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(machine_part=='' || status=='' )
{
	
	return false;
}

});
});


/**function getcolors()
{

 
$("#example tr").each(function(){
		
	var currentRow=$(this);
	
	    var col1_value=currentRow.find("td:eq(13)").text();
	   col1_value=col1_value.trim();
	   
	  
	if(col1_value=='BLACK')
		{
			currentRow.addClass("backgroundcolorblack");
		}else if(col1_value=='RED')
		{
			currentRow.addClass("backgroundcolorred")
		}else if(col1_value=='YELLOW')
		{
			currentRow.addClass("backgroundcoloryellow")
		}else if(col1_value=='GREEN')
		{
			currentRow.addClass("backgroundcolorgreen")
		}else if(col1_value=='BLUE')
		{
			currentRow.addClass("backgroundcolorblue")
		}			
	

	 
});
}**/

</script>
    </body>
</html>
