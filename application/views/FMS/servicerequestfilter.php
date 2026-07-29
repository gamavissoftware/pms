<?php
$type=$this->uri->segment('3');
$CI =& get_instance();
$CI->load->model('Fms_model');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>SERVICE REQUEST</title>

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

.backgroundcolordarkgreen
			{
				background-color: #73D673;
				color:#000;
				
			}
			
			.backgroundcoloryellow
			{
				background-color: #F7F6A9;
				color:#000;
				
			}
			
			
			.backgroundcolorcyan
			{
				background-color: #2E95B4;
				color:#000;
				
			}
			
			
			.backgroundcolorgreen
			{
				background-color: #F7AD45;
				color:#000;
				
			}
			
			
			.backgroundcolorskyblue
			{
				background-color: #D0DDFF;
			
				
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
						<div class="col-md-6"><h4 class="page-title">INSTALLATION REQUIRED</h4></div>
                       
                            
                           
                        </div>
                    </div>
                    
                     <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                         <div class="col-md-3"></div>
                     <div class="col-md-6 card-box">
                                
                               <table class="table table-bordered">
                                <thead>
                                <tr>
                                 <th colspan="10" style="text-align:center;">Legend- Color Indicator <span> <a href="<?php echo page_url;?>Reporting/servicerequest"><button class="btn btn-xs pull-right btn-success waves-effect waves-light">Reset Filter</button></a></span></th>
                                
                                </tr>
                                
                                </thead>
                                  <tbody>
                                 <tr>
                              
                                <td style="background-color:#F7F6A9;width:50px"></td>
                                <td  style="width: 117px;">Work In Progress</td>
                                
                               
                                <td style="background-color:#2E95B4;width:50px"></td>
                                <td  style="width: 117px;">Customer End Pending</td>
                                
                                
                                <td style="background-color:#F7AD45;width:50px"></td>
                                <td  style="width: 117px;">Incomplete</td>
                                
                                <td style="background-color:#D0DDFF;width:50px"></td>
                                <td  style="width: 117px;">New Request in Past 24 Hours</td>
                               
                                
                                </tr>
                                <?php
                                $workinprogress=$CI->Fms_model->workinprogresscountforinstallation();
                                $workinprogresscustomer=$CI->Fms_model->workinprogresscustomerinstallation();
                                
                                 $incompleteinstallationall=$CI->Fms_model->incompleteinstallation();
                                 $newinstall=$CI->Fms_model->newinstallation();
                                 
                                  $engineervisitinstallation=$CI->Fms_model->engineervisitinstallation();
                                
                                ?>
                             <tr>
                           
                            <td colspan="2" style="text-align:center"><a href="<?php echo page_url;?>Reporting/servicefilterlist/0">Total - <?php echo $workinprogress;?></a></td>
                             <td colspan="2" style="text-align:center"><a href="<?php echo page_url;?>Reporting/servicefilterlist/2">Total- <?php echo $workinprogresscustomer;?></a></td>
                            <td colspan="2" style="text-align:center" style="text-align:center"><a href="<?php echo page_url;?>Reporting/servicefilterlist/3">Total - <?php echo $incompleteinstallationall;?></a></td>
                            <td colspan="2" style="text-align:center"><a href="<?php echo page_url;?>Reporting/servicefilterlist/4">Total - <?php echo $newinstall;?></a></td>
                            
                           
                             
                                
                                </tbody>
                                </table>
                         
                  
                        
                           
                           
                      
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
                                    <th>Sr No.</th>
                                    <th>REQUEST AGE</th>
									<th>UPDATE</th>
									<th>CURRENT STATUS</th>
									<th>NEXT FOLLOW-UP</th>
									<th>MARKETING PERSON</th>
                                    <th>COMPANY NAME</th>
                                    <th>ADDRESS WITH PINCODE</th>
                                    <th>EMAIL ID</th>
                                    <th>MOBILE NUMBER</th>
									<th>TIMESTAMP</th>
									<th>PLANNED TIME</th>
									<th>ACTUAL TIME</th>
									<th style="width:50%">INSTRUMENTS</th>
                                    <th>ORDER TYPE</th>
                                   	<th>INTERNAL ORDER NUMBER</th>
		                            <th>INSTALLATION CHARGES TYPE</th>
							        <th>REMARKS</th>
								</tr>
                                </thead>


                                <tbody>
								
                                </tbody>
                            </table>
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
		
		<script type="text/javascript">
$(document).ready(function(){
	


 var i=2;
 $('#addmore_btn1').click(function(){
 i++;
 
 $('#dynamictasks1').append('<div id="row'+i+'" class="row"><div class="col-md-9"><div class="form-group"><label for="field-1" class="control-label">INSTRUMENT NAME</label><span id="error_instruments" style="color:red;">*</span><select class="form-control select3'+i+'" style="text-transform: uppercase;" name="instruments[]" id="instruments'+i+'"><option value="">--SELECT INSTRUMENT--</option><?php $query = $this->db->select('id, instruments_name, status')->from(' presto_instruments')->where('status','1')->get();foreach($query->result() as $instruments){?><option value="<?php echo $instruments->id;?>"><?php echo trim(strtoupper($instruments->instruments_name));?></option><?php }?></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">QUANTITY</label><input type="text" class="form-control" id="qty" name="qty[]" placeholder="" required></div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:30px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="'+i+'"><i class="fa fa-close"></i></button></div></div></div><br/>');
 initializeSelect2('select3'+i);
 });
 
 
 
 $(document).on('click', '.btn_remove', function(){
 var button_id = $(this).attr("id");
 $('#row'+button_id+'').remove();
 });
 
});
	  </script>
	  <script>
	  function initializeSelect2(selectElementObj) {
         $('.'+selectElementObj).select2({});
         
      }
	  </script>
		<script>
		// Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
			jQuery('#timepicker4').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });
		</script>
 <script>
$( document ).ready(function() {
$('.select2').select2({ });
$('.select3').select2({ });
$('.select4').select2({ });
 function initializeSelect2() {
    $('.select3').select2({ });
  }
  
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
 "sAjaxSource": "<?php echo page_url;?>Reporting/filterservicerequestlist/<?php echo $type;?>",
 "aoColumns": [
						{ mData: 'sr_no' } ,
						 { mData: 'type' },
						
                        { mData: 'closeorder' },
                        { mData: 'currstatus' },
                       
                        { mData: 'nextfollowup' },
                         { mData: 'marketing_person' },
                        { mData: 'company_name' },
                        { mData: 'address' },
                        { mData: 'email' },
                        { mData: 'mobile_number' },
                        { mData: 'added_on'},
	                     { mData: 'plannedtime' },
	                    { mData: 'actualtime' },
						{ mData: 'itemname' },
                        { mData: 'order_type' },
                       
					
						{ mData: 'internal_order_no' },
						
					
						{ mData: 'installation_charges' },
						
					
						{ mData: 'remarks' }
						
						
                ],"initComplete": function(settings, json) {
  
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


function getcolors()
{

 
$("#example tr").each(function(){
		
	var currentRow=$(this);
	
	    var col1_value=currentRow.find("td:eq(3)").text();
	   col1_value=col1_value.trim();
	   
	  
	if(col1_value=='Task Complete')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcolorgreen");
		}else if(col1_value=='Work In Progress')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcoloryellow")
		}else if(col1_value=='Customer End Pending')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcolorcyan")
		}else if(col1_value=='Incomplete')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcolorgreen")
		}else if(col1_value=='New Request')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcolorskyblue")
		}else if(col1_value=='Engineer Visit Required')
		{
			currentRow.find("td:eq(3)").addClass("backgroundcolorpink")
		}
		
		  var col1_value1=currentRow.find("td:eq(1)").text();
	      col1_value1=col1_value1.trim();
	   
	    
	   if(col1_value1=='NEW')
	   {
	       currentRow.addClass("backgroundcolorskyblue");
	   }
	   
	

	 
});
}



function markstagedone(orderid,compyname,internalorderno)
{
	if(confirm('Confirm you want to close following order? \nCustomer '+compyname+'\n Internal Order No. '+internalorderno))
	{
		document.location="<?php echo page_url;?>Reporting/markservicedone/"+orderid;
		return true;
		
	}else
	{
		return false;
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
</script>
</body>
</html>