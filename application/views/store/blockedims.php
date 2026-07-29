<?php
$CI =& get_instance();
$CI->load->model('Store_model');

$UI =& get_instance();
$UI->load->model('Fms_model');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

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

.numberCirclepurchase {
    border-radius: 50%;
    width: 45px;
    height: 45px;
     padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #F2652F99;
    color: #666;
    text-align: center;
    font-size: 14px;
   
}


.numberCirclestore {
    border-radius: 50%;
    width: 45px;
    height: 45px;
     padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #E6E31DCC;
    color: #666;
    text-align: center;
    font-size: 14px;
    
}

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
                <div class="col-md-12">
                <h4>Blocked Items Detail</h4>
                </div>
                </div>
               
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

<?php 
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				
				$query = $this->db->select('qty, vendor, price')->from('ims_header_permission')->where('user_id',$user_id)->get();
					foreach($query->result() as $row);
				
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
                                <!--<th>MAKE</th>
                                <th>SIZE IN MM</th>-->
                                <th>FIN CODE</th>
                                <th>RACK LOCATION</th>
                                <th>PHYSICAL STOCK</th>
                                <th>BLOCKED DETAILS</th>
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
"sAjaxSource": "<?php echo page_url;?>Store/machine_part_data_with_picture_listforblocked",
"aoColumns": [
  
        { mData: 'sr_no' } ,
        { mData: 'image' },
        { mData: 'machine_part' },
        { mData: 'category' },
        { mData: 'specification' },
        { mData: 'fincode' },
        { mData: 'location' },
        { mData: 'stock' },
        { mData: 'blockeddata' }
 
	]
  
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


function getcolors()
{

 
$("#example tr").each(function(){
		
	var currentRow=$(this);
	
	    var col1_value=currentRow.find("td:eq(13)").text();
	   col1_value=col1_value.trim();
	   
	  
	if(col1_value=='BLACK')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorblack");
		}else if(col1_value=='RED')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorred")
		}else if(col1_value=='YELLOW')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcoloryellow")
		}else if(col1_value=='GREEN')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorgreen")
		}else if(col1_value=='BLUE')
		{
			currentRow.find("td:eq(2)").addClass("backgroundcolorblue")
		}			
	

	 
});
}

</script>
    </body>
</html>