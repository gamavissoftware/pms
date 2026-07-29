<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>MATERIAL ISSUED BUT NOT ACCEPTED BY STORE</title>

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
				<div class="col-md-6 page-title-box">


				<h4 class="page-title">MATERIAL RETURN REQUEST</h4><BR/>
				<?php echo $this->session->flashdata('message');?>
				</div>


				</div>
				</div>
				<!-- end page title end breadcrumb -->

				 <form method="post" action="<?php echo page_url;?>Store/updateReturnStock">
				  <input type="submit" class="btn btn-success pull-left" name="Submit" value="Submit">
				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                           
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   
                                    <th>Sr No.</th>
									<th>ACCEPT</th>
									<th>ROLLBACK</th>
									<th>ITEM NAME</th>
                                    <th>NAME</th>
                                    <th>JOBCARD NO.</th>
									<th>QTY</th>
									<th>STORE ACCEPTED ON</th>
							
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                          
                            


                    </div>
                    </div>
                </div>
                </form>
          
		  <!-- Footer -->
		  
		  				<div class="row">
				<div class="col-sm-12">
				<div class="col-md-6 page-title-box">


				<h4 class="page-title">MATERIAL RETURN REQUEST HISTORY</h4>
				</div>


				</div>
				</div>
				
				
				<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example1" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                   
                                    <th>Sr No.</th>
                                    <th>ITEM NAME</th>
                                    <th>NAME</th>
                                    <th>JOBCARD NO.</th>
                                    <th>QTY</th>
                                    <th>STORE UPDATED ON</th>
                                     <th>ACTION TAKEN</th>
							
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>



 </div>
                    </div>
                </div>
          
          
				<!-- end page title end breadcrumb -->

		  
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
$( document ).ready(function() {
 
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Store/getReturnStock",
 "aoColumns": [
				
				{ mData: 'sr_no' },
				{ mData: 'accept' },
				 { mData: 'rollback' },
				{ mData: 'itemname' },
				{ mData: 'name'},
				{ mData: 'jobcard' },
				{ mData: 'qty'},
				{ mData: 'store_accepted'}	
                ]
        });   
		
		
		$('#example1').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Store/getReturnStockHistory",
 "aoColumns": [
				
				{ mData: 'sr_no' },
                { mData: 'itemname' },
                { mData: 'name'},
                { mData: 'jobcard' },
                { mData: 'qty'},
                { mData: 'store_accepted'},
                { mData: 'action'}
	
                ]
        });   
});


</script>

<script>
function checkallitem()
{
	if($('.selectall').is(":checked"))
	{
		$(".checkitems").prop('checked', true);
	}else
	{
		$(".checkitems").attr('checked',false);
	}
	
	
}
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function acceptitems(id)
{
/**	if($('#accept'+id).is(":checked"))
	{ **/
		$("#myModal").modal('show');
		$("#issueid").val(id);
		 $(".blockeddata").html(''); 
		$.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/getblockedlistforthisitem",
        data:"id="+id,
        success:function(data){
            if(data!='')
            {
               $(".blockeddata").html(data); 
                
            }
            
        }
        
        });
        
        
        
        
        	$.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/getthisitemdetails",
        data:"id="+id,
        success:function(data){
            if(data!='')
            {
               var s=data.split('|');
               $("#iname").text(s[0]);
               $("#jobcards").text(s[2]);
               $("#qtyy").text(s[1]);
                
            }
            
        }
        
        });
        
        
		
	/** $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c"+id).css('display','none');
			$("#ce"+id).css('display','');
		}
         
        }
        }); **/
		
/**	}else{
		
		
		$("#myModal").modal('hide');
	} **/
	
}



function acceptnonjobcarditems(id)
{
    
    $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c"+id).css('display','none');
			$("#ce"+id).css('display','');
		}
         
        }
        });
    
}
function acceptitems1(id)
{

	if($('#accept1'+id).is(":checked"))
	{
		
	 $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Store/acceptissueditems1",
        data:"id="+id,
        success:function(data){
        if(data==1)
		{
			
			$("#c1"+id).css('display','none');
			$("#ce1"+id).css('display','');
		}
         
        }
        });
		
	}else{
		
		
	}
	
}


function rollbackRecord(id) {

    if (confirm('Are you sure you want to rollback this record?')) {
        
		
		document.location="<?php echo page_url;?>Store/rollbackRecord/"+id;
		return true;
    }
	
}
</script>

</body>
</html>
