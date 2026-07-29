<?php
$orderflow=$this->uri->segment(3);
$productionflowid=$this->uri->segment(4);
if($orderflow=='')
{
	echo "Invalid Access to this page";exit;
}
$restyu=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$orderflow)->get();
foreach($restyu->result() as $restyiou);

$CI =& get_instance();
$CI->load->model('Fms_model');
$nextquecount= $CI->Fms_model->nextinqueuecount($orderflow);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo $restyiou->fms_flow;?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
	.backgroundcolor{ background-color:#FCDCB1 !important; }
			.backgroundcolorservice{ background-color:#FEFDCD !important; }
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
						 
                           <div class="col-md-9">
                            <h4 class="page-title"><span style="color:red;"><?php echo $restyiou->fms_flow;?></span> ASSIGNED TO YOU</h4>
							</div>
							
							<div class="col-md-3"><a href="<?php echo page_url;?>Orderstage/nextinqueue/<?php echo $orderflow;?>/<?php echo $productionflowid;?>" target="_blank"><span class="btn btn-warning pull-right badge1" data-badge="<?php echo $nextquecount;?>">Next in Queue</span></a></div>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
									<th>TIMESTAMP</th>
									<th>ORDER TYPE</th>
									<th>MACHINE NAME</th>
                                    <th>I/O & JOB CARD</th>
                                    <th>FILE NO.</th>
                                    <th>FACTORY</th>
                                    <th>PLANNED TIME</th>
									<th>ACTUAL TIME</th>
									<th>TOTAL DAYS</th>
									<th>IN STOCK</th>
									<th>PR NUMBER</th>
									<th>PO DETAILS</th>
									<th>INSTRUMENT NAME</th>
									<th>ACTION</th>
									
                                </tr>
                                </thead>


                                <tbody>
								
                                </tbody>
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
 "sAjaxSource": "<?php echo page_url;?>Orderstage/machining_process3/<?php echo $orderflow;?>/<?php echo $productionflowid;?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						{ mData: 'orderdate'},
						{ mData: 'odtype'},
                        { mData: 'mname' },
                        { mData: 'jobcard' },
                        { mData: 'fileno' },
                        { mData: 'factory' },
			 { mData: 'tat' },
						{ mData: 'actual' },
						{ mData: 'totdays' },
	 { mData: 'stockinfo' },
	 { mData: 'prno' },
	 { mData: 'pono' },
	 { mData: 'material' },
						{ mData: 'action' }
						
                ],"initComplete": function(settings, json) {
    //alert('hi');
			getcolors();
   
  }
        });   
        
        
          $('#example').on('draw.dt', function() {
    // do action here

    getcolors();
});  

  $('#example').on('search.dt', function() {
   
    getcolors();
});  
});

</script>


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function markstagedone(idd,flowstage,orderid,jobcardid)
{
	if(confirm('Confirm you want to mark this stage as done?'))
	{
		document.location="<?php echo page_url;?>Orderstage/markdone/"+idd+"/"+flowstage+"/"+orderid+"/"+jobcardid;
		return true;
		
	}else
	{
		return false;
	}
}
	function getstockdetail(stageid)
{
var stock=$("#stock"+stageid).val();
if(stock==0)
{
$("#prreq"+stageid).css('display','');
$("#prno"+stageid).attr('required',true);
//$("#mtname"+stageid).attr('required',true);

}else{

$("#prreq"+stageid).css('display','none');
$("#prno"+stageid).attr('required',false);
//$("#mtname"+stageid).attr('required',false);

}


}


function getmaterialsforpr(odid,itemid)
{
	$("#materialtable"+odid+" > tbody").html("");
	var stock=$("#stock"+odid).val();
	if(stock=='0')
	{
			$.ajax({
			type:"post",
			url:"<?php echo page_url;?>FMS/partsdetail/"+itemid,
			data:"1",
			success:function(data){
				

$("#materialtable"+odid+" > tbody").append(data);

openqtyboxforalreadychecked();

			}
			});
	}
	
}

function checkifcheckedforpr(id)
{
	if($('#selectcheckbox' + id).is(":checked"))
	{
		//$("#partsqty"+id).attr('required',true);
		var currstock=$("#currentstock"+id).val();
		if(currstock>0)
		{
			$("#prreason"+id).css('display','');
		}else{ 
		
		$("#prreason"+id).css('display','none');

		}
		$("#partsqty"+id).css('display','');
		$("#udata"+id).css('display','');
		
		
	}else
	{
		//$("#partsqty"+id).attr('required',false);
		$("#partsqty"+id).css('display','none');
		$("#udata"+id).css('display','none');
		$("#prreason"+id).css('display','none');
	}
	
	
}

function openqtyboxforalreadychecked()
{
	
	$.each($("input[name='itemselected[]']:checked"), function(){
              var partid=$(this).val();
			  
			  $("#partsqty"+partid).css('display','');
		$("#udata"+partid).css('display','');
			  
            });
	
	
	
}


function searchtable(id) {
  // Declare variables
  var term=$("#searchableee").val();
  var input, filter, table, tr, td, i, txtValue;
 // input = document.getElementById("myInput");
  filter = term.toUpperCase();
  table = document.getElementById("materialtable"+id);
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}



function checkvalidation(id)
{
	
	var stock=$("#stock"+id).val();
	if(stock==0)
	{
		var n=$("input:checkbox[class=partselection]:checked").length;
		if(n==0 || n=='')
		{
			alert('Please select items to proceed');
			return false;
			
		}
	}
	
	var t=checkifqtyisfilled(id);
	var q=checkifreasonisfilled(id);
	if(t==1 || q==1)
	{
	$("#save"+id).val('Save');
	$("#save"+id).attr('disabled',false);
		
		return false;
	}else
	{
	$("#save"+id).val('Loading...');
	$("#save"+id).attr('disabled',true);
		return true;
	}
	
}

function checkifqtyisfilled(id)
{
	$("#save"+id).val('Loading...');
	$("#save"+id).attr('disabled',true);
	var found=0;
	$("input:checkbox[class=partselection]:checked").each(function () {
            var itemid=$(this).val();
			var qty=$("#partsqty"+itemid).val();
			if(qty=='')
			{
				found=1;
				$("#partsqty"+itemid).focus();
				alert('Quantity Required');
			}
        });


return found;

	
}


function checkifreasonisfilled(id)
{
	var found1=0;
	$("input:checkbox[class=partselection]:checked").each(function () {
            var itemid=$(this).val();
			var currstock=$("#currentstock"+itemid).val();
			if(currstock>0)
			{
				var reason=$("#prreason"+itemid).val().trim();
			if(reason=='')
			{
				found1=1;
				$("#prreason"+itemid).focus();
				alert('Reason Required for rasing PR for items having stock');
			}
			
			}
        });


return found1;

	
}


function generatehelpticket(jobcardid)
{
    
    
    $.ajax({
			type:"post",
			url:"<?php echo page_url;?>FMS/genrateproductionhelpticket/"+jobcardid,
			data:"1",
			success:function(data){
				
alert('Help Ticket Raised');

			}
			});
    
    
}


function getcolors()
{

$("#example tr").each(function(){
		
	var currentRow=$(this);
	
	    var col1_value=currentRow.find("td:eq(2)").text();
	   col1_value=col1_value.trim();
		if(col1_value=='SALE' || col1_value=='sale' )
		{
			currentRow.addClass("backgroundcolor");
		} 
		
			if(col1_value=='SERVICE' || col1_value=='service' )
		{
			currentRow.addClass("backgroundcolorservice");
		} 
	

	 
});
}

</script>
</body>
</html>
