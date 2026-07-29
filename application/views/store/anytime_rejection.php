<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?> Anytime Rejection Form</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
			.divheight{
			padding-top:20px;
			}
			
			.select2-container .select2-selection--single {
    height: 39px !important;
}
		</style>
		<script>
		
	$(document).ready(function() {
        $(".decall").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
        {
        evt.preventDefault();
        }
        });
	});
 
		</script>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
				<div class="divheight hidden-xs"></div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						
                            <h4 class="page-title text-center">ANYTIME REJECTION FORM</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->

				<div class="row">
				    <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				</div>
               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
						<form id="loginForm" method="post" action="<?php echo page_url;?>Store/anytime_rejection" enctype="multipart/form-data" onsubmit="return validate();">
						
                             <div class="row">
                                            <?php 
								$first_name =$this->session->userdata['logged_in']['user_name'];	
								$last_name =$this->session->userdata['logged_in']['last_name'];
								?> 
											 <div class="col-md-3">
													<div class="form-group">
														 <label for="field-2" class="control-label">Your Name</label>
														 <span id="error_machine" style="color:red;">*</span>
														 <input type="text" class="form-control" name="yourname" id="yourname" value="<?php echo $first_name;?> <?php echo $last_name;?>" readonly>
													</div>
												</div>
												
											
												<div class="col-md-3">
													<div class="form-group">
														<label>REJECTION TYPE</label>
														<span id="error_rejection_type" style="color:red;">*</span>
													<select class="form-control" name="rejection_type" id="rejection_type" required onchange="getjobcardfield();">
											<option>Select Option</option>
											<option value="PAINT PLATING">PAINT PLATING </option>
											<option value="OTHER (BOP)">OTHER (BOP)</option>
											<option value="JOBCARD">JOBCARD</option>
											

													</select>
													</div>
												</div>
												
                                        
                                        <div class="col-md-6 jobcard" style="display:none;">
                                        <div class="form-group">
                                        <label for="field-2" class="control-label">JOBCARD NO.</label>
                                        <span id="error_item_name" style="color:red;">*</span>
                                        <select name="jobcardno" class="form-control selectjobcard"  id="jobcardno"  style="width:100%" onchange="getjobcardtable();">
                                        <option value=""></option>
                                        </select>
                                        </div>
                                        </div>
                                        <script>
												
												function getjobcardfield()
											{
                                                var type=$("#rejection_type").val();
                                                
                                                if(type=='JOBCARD')
                                                {
                                                
                                        $(".nonjobcard").css('display','none');
                                        $("#item_name").attr('required',false);
                                        
                                        $("#qty").attr('required',false);
                                        $("#reason").attr('required',false);

                                        $(".jobcard").css('display','block');
                                         $(".jobcardno").attr('required',true);
                                         
                                       
                                                
                                                }else
                                                {
                                                
                                        $(".nonjobcard").css('display','block')
                                        $("#item_name").attr('required',true);
                                        $("#qty").attr('required',true);
                                         $(".jobcard").css('display','none');
                                         $(".jobcardno").attr('required',false);
                                        $("#reason").attr('required',true);
                                                
                                                }
											}
												
												
                                            function getjobcardtable()
                                            {
                                            var jbcard=$("#jobcardno").val();
                                            if(jbcard!='')
                                            {
                                                $.ajax({
                                                type:"post",
                                                url:"<?php echo page_url;?>Store/getissuedjobcarditems",
                                                data:"jobcardid="+jbcard,
                                                success:function(data){
                                               
                                                $("#jobcardissued").html(data);
                                                }
                                                });
                                            }
                                            }
											    </script>
                                        
                                        

												
													<div class="col-md-6 nonjobcard" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">REJECTED ITEM NAME</label>
														 <span id="error_item_name" style="color:red;">*</span>
														 <input type="text" class="form-control" name="item_name" id="item_name" value="" required>
													</div>
												</div>
												
												
													<div class="col-md-6 nonjobcard">
													<div class="form-group">
														 <label for="field-2" class="control-label">REASON OF REJECTION</label>
														 <span id="error_reason" style="color:red;">*</span>
														 <input type="text" class="form-control" name="reason" id="reason" value="" required>
													</div>
												</div>
												
													<div class="col-md-3 nonjobcard" style="display:none;">
													<div class="form-group">
														 <label for="field-2" class="control-label">QUANTITY</label>
														 <span id="error_qty" style="color:red;">*</span>
														 <input type="text" class="form-control" name="qty" id="qty" value="" required>
													</div>
												</div>
												
												<div class="col-md-3 nonjobcard" style="display:none;">
												    <div class="form-group">
												        <label>UPLOAD IMAGE (IF ANY)</label>
												        <input type="file" class="form-control" name="photo" id="photo" value="">
												    </div>
												</div>
											
											
											<div class="col-md-12" id="jobcardissued"></div>
											
											<div class="col-md-12" style="margin-top:20px">
											<div class="form-group pull-right">
												<input type="submit" id="save" class="btn btn-info" value="Submit">
											</div>												
											</div>
											
                                        </div>
                             </form>           
										
										<!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script language="javascript" type="text/javascript">   
$(document).ready(function() {
	$('.select2').select2({ });
	$('.select3').select2({ });
	$('.select4').select2({ });
$("#save").click(function() {
	var yourname= $("#yourname").val();
if(yourname=='')
{
	$("#error_yourname").html('Required!');
}
var rejection_type = $("#rejection_type").val();
if(rejection_type=='')
{
	$("#error_rejection_type").html('Required!');
}
var item_name = $("#item_name").val();
if(item_name=='')
{
	$("#error_item_name").html('Required!');
}

var reason = $("#reason").val();
if(reason=='')
{
	
	$("#error_reason").html('Required!');
}
var qty = $("#qty").val();
if(qty=='')
{
	
	$("#error_qty").html('Required!');
}



if(yourname==''|| rejection_type=='' || item_name=='' ||  reason=='' || qty=='')
{
	
	return false;
}

});
});


</script>

 <script> $(document).ready(function() {
$('.datepicker').datepicker({todayHighlight:true});

                $("#datepicker1").datepicker({
					orientation: 'bottom',
					todayHighlight:true
				});
			//	$('.datepicker').datepicker({todayHighlight:true});
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
					
                })

            });
            
            
            
            $(document).ready(function() {
 var purl="<?php echo page_url;?>User/getopenjobcardlist";
 $('.selectjobcard').select2({
 placeholder: 'TYPE TO SELECT JOBCARD',
minmumInputLength:4,
		allowClear: true,

        ajax: {

          url: purl,

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


function openqtybox(blockedid)
{
    if($("#checked"+blockedid).is(":checked"))
    {
    $(".qtyy"+blockedid).css('display','');
  //  $(".qtyy"+blockedid).attr('required',true);
    
    }else
    {
    $(".qtyy"+blockedid).css('display','none');
   // $(".qtyy"+blockedid).attr('required',false);
    }
    
}

    
    
    function checkforqty(itemid)
{
	
	var issueqty=$("#issueqty"+itemid).val();
	var rejqty=$("#rejqty"+itemid).val();
	if(parseFloat(rejqty)>parseFloat(issueqty))
	{
		
		alert('Reject Qty Cannot be greater than Issue Qty');
		$("#rejqty"+itemid).val('');
		$("#rejqty"+itemid).focus();
		return false;
	}
	
	
	
}  



function validate()
{
	var isvalid=true;
	
	
    var type=$("#rejection_type").val();
   
    if(type=='')
    {
        alert('Rejected Type is mandatory');
        isvalid=false;
    }
    
    if(type=='JOBCARD')
    {
        
        	var jobcardid=$("#jobcardno").val();
			if(jobcardid=='')
			{
				 alert('Select Jobcard for Rejection');
			isvalid=false;
			}
			
			
	var favorite = [];
            $.each($("input[name='checkit[]']:checked"), function(){
				var v=$(this).val();
                favorite.push($(this).val());
				
				var qty=$("#rejqty"+v).val();
				if(qty=='')
				{
					alert('Rejected Quantity is mandatory');
					isvalid=false;
				}else if(qty<=0)
				{
					alert('Rejected Quantity cannot be 0');
					isvalid=false;
				}
				
            });
			
			if(favorite.length==0)
			{
            alert('At least one item must be issued');
			isvalid=false;
			}
			
			var isto=$("#reason"+v).val();
			if(isto=='')
			{
				 alert('Reason for Rejection is mandatory');
			isvalid=false;
			}
			
		alert(isvalid);
        
        if(isvalid==false)
        {
        return false;
        }else{
        
        return true;
        }
			
    }
	
	

	
	
}



            </script>
            <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>