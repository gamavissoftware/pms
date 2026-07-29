<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title> <?php echo sitetitle; ?> Cancel Jobcard</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

	<?php
								$id = $this->uri->segment(3);
								$this->db->select('*')->from('prestogroup_orders')->where('order_id',$id);
								$query = $this->db->get();
								$res = $query->result();
								foreach($res as $row);
								
								?>
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                            <h4 class="page-title">CANCELLING JOBCARD FROM ORDER <?php echo $row->internal_order_no;?> - <?php echo $row->company_name;?></h4>
							<?php echo $this->session->flashdata('message'); ?>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->


                <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

							
								
                                   
								   <div class="row">
								   <form method="post" id="form1" action="<?php echo page_url;?>FMS/cancelexistingprder/<?php echo $this->uri->segment('3');?>/<?php echo $this->uri->segment('4');?>" onsubmit="return validate();">
								 
								
									<div class="row" id="jobcarddetail">
									<div class="col-md-4">
									<table class="table table-bordered">
									<thead>
									<th></th>
									<th>Jobcard No.</th>
									<th>Instrument</th>
									</thead>
									<tbody>
									<?php
									$rerstsys=$this->db->select('a.job_card_no,a.id,b.instruments_name,a.item_id,a.order_id')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id')->where('a.order_id',$id)->where('a.id',$this->uri->segment(4))->get();
									if($rerstsys->num_rows()>0)
									{
										foreach($rerstsys->result() as $rerstsys1)
										{
									?>
									<tr>
									<td><input type="radio" checked value="<?php echo $rerstsys1->id;?>" name="jobcard[]" onchange="getionoforsame('<?php echo $rerstsys1->item_id;?>','<?php echo $rerstsys1->order_id;?>');"></td>
									<td><?php echo $rerstsys1->job_card_no;?></td>
									<td><?php echo $rerstsys1->instruments_name;?></td>
									</tr>
									<?php
										}
									}
									?>
									</tbody>
									
									</table>
									</div>
									
										<div class="col-md-5">
										<div class="form-group">
										<label for="field-1" class="control-label">Reason</label>
										<span id="error_order_type" style="color:red;">*</span>
										<textarea name="remarks" class="form-control mand" style="resize:none;"></textarea>


										</div>
										
										</div>
										
									</div>
									
									
									
								
										<div class="col-md-12">
										<input type="submit" name="sub" id="saves" class="btn btn-success pull-right">
										</div>
										</form>
										</div>
												
											
									
									
									
									
							

                                </div>

                            </div>
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
		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

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
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
var order_type = $("#order_type").val();
if(order_type=='')
{
	$("#error_order_type").html('Required!');
}
var person_name = $("#person_name").val();
if(person_name=='')
{
	
	$("#error_person_name").html('Required!');
}

var po_number = $("#po_number").val();
if(po_number=='')
{
	
	$("#error_po_number").html('Required!');
}

var company_name = $("#company_name").val();
if(company_name=='')
{
	
	$("#error_company_name").html('Required!');
}
var address = $("#address").val();
if(address=='')
{
	
	$("#error_address").html('Required!');
}
var email_id = $("#email_id").val();
if(email_id=='')
{
	
	$("#error_email_id").html('Required!');
}
var mobile_number = $("#mobile_number").val();
if(mobile_number=='')
{
	
	$("#error_mobile_number").html('Required!');
}
var auto_generated_io = $("#auto_generated_io").val();
if(auto_generated_io=='')
{
	
	$("#error_internal_order_no").html('Required!');
}
var instruments = $("#instruments").val();
if(instruments=='')
{
	
	$("#error_instruments").html('Required!');
}

var payment_term= $("#payment_term").val();
if(payment_term=='')
{
	
	$("#error_payment_term").html('Required!');
}

var packing_type= $("#packing_type").val();
if(packing_type=='')
{
	
	$("#error_packing_type").html('Required!');
}
var packing_charges= $("#packing_charges").val();
if(packing_charges=='')
{
	
	$("#error_packing_charges").html('Required!');
}
var installation_charges= $("#installation_charges").val();
if(installation_charges=='')
{
	
	$("#error_installation_charges").html('Required!');
}
var freight_type= $("#freight_type").val();
if(freight_type=='')
{
	
	$("#error_freight_type").html('Required!');
}

var status= $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


var contactperson= $("#contactperson").val();
if(contactperson=='')
{
	
	$("#error_contact_person").html('Required!');
}



if(order_type=='' || person_name=='' || po_number=='' || company_name==''|| address==''|| email_id=='' || mobile_number=='' || internal_order_no=='' || instruments=='' || payment_term=='' || installation_charges==''|| packing_type=='' || packing_charges=='' || freight_type=='' || status=='' || contactperson=='')
{
	
	return false;
}

});
});
</script>

<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready

function getform(vaal)
{
	if(vaal=='2')
	{
		$("#forhold").css('display','');
		$("#expo").addClass('mand');
	}else{
		$("#forhold").css('display','none');
		$("#expo").removeClass('mand');
		
	}
	
	
}

</script>
</body>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script>

$( document ).ready(function() {
var purl="<?php echo page_url;?>FMS/getiono";

$('.select2').select2({});

});



</script>
<script>
function validate()
{
	
	
	 $("#saves").attr('disabled',false);
	$("#saves").val('Submit');

var isValid=0;
$("#form1 .mand").each(function() {
var element = $(this).val();
if (element=="") {

isValid=1;
}


});
 
 
 if(isValid==0)
 {
	$("#saves").attr('disabled',true);
	$("#saves").val('Please Wait..');
     return true;
	 
 }else
 {
	 $("#saves").attr('disabled',false);
	$("#saves").val('Submit');
     alert('All Fields are mandatory');
     return false;
 }
  

	
  
}


function checktype(vaal)
{
	if(vaal=='2')
	{
		$("#jobcarddetail").css('display','');	
	}
	else{
		
		$("#jobcarddetail").css('display','none');
	}
	
}


function getionoforsame(itemid,orderid)
{

		$.ajax({
		type:"post",
		url:"<?php echo page_url;?>FMS/getiono",
		data:"itemid="+itemid+"&orderid="+orderid,
		success:function(data){
			
		$("#dcustiono").html(data);
		}
		});

	
}

</script>
</html>