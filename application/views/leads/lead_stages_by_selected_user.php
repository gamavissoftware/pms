<?php 
$lead_stage = $this->uri->segment(3);
$current_url = page_url.'Leads/lead_stages/'.$lead_stage;
// echo $current_url;exit;
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$getLeadStageDetails = $CI->Salescrm_model->getLeadStageDetails($lead_stage);
foreach ($getLeadStageDetails as $row);
$quotation_step = $row->quotation_step;
$quotation_revised_step = $row->quotation_revised_step;
$pi_step = $row->pi_step;
$pi_revised_step = $row->pi_revised_step;
$lead_status=$row->lead_name;
$reason=$row->reason;
$quotestep=$CI->Salescrm_model->checkforquotationoraheadstep($lead_stage);
$getConversionLeadStage=$CI->Dashboard_model->getConversionLeadStage();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

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
        <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
				font-size:12px;
            }
			table.pretty td {
                text-align: center;
                font-size:12px;
            }

            .btns {
                margin-top: 20px;
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
                        <div class="row">
                                <div class="col-md-2 pull-left">
                                      <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btns" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                                </div>
                                 <div class="col-md-2 pull-right">
                            <!-- <a href="<?php echo page_url;?>ExcelImport/excel_import"><span class="btn btn-primary">Import Leads Via Excel</span></a> -->
                        </div>
                        </div>
                       
                       
                    </div>
                </div>

                <div class="row card-box">
                    <div class="page-title-box col-md-12">
                        <h4 class="page-title text-center">Opportunities at <?php echo ucwords(strtolower($lead_status));?> Stage of  <span style="font-weight:bold; font-size:23px;"><?php $selecteduser = $this->uri->segment(4);
                    $q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$selecteduser)->get();
                    foreach($q->result() as $userinfo);
                    echo ucwords(strtolower($userinfo->title." ".$userinfo->first_name." ".$userinfo->last_name));
                   ?></span></h4>
                    </div>
                </div>

                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>

                                    
                                     <th>Sr No.</th>
                                     <?php $q = $this->db->select('lead_id')->from('lead_stage')->where('lead_id',$this->uri->segment(3))->where('dead_end',1)->get();
                                     if($q->num_rows()>0){?>
                                        <th>Lost Reason</th>
                                     <?php }else{
                                        if($this->uri->segment(3)==35){}else{
                                        ?>
                                     <th>Update</th>
                                 <?php } }?>
                                     <?php
                                     if($quotestep==1 || $this->uri->segment(3)==36 || $this->uri->segment(3)==37 || $this->uri->segment(3)==38 || $this->uri->segment(3)==39)
                                     {
                                    ?>
                                     <th>Preview Quotation</th>
                                    <?php } ?>

                                     <th>Opp. No/Date</th>
                                     <th>Opp Type</th>
                                     <th>Lead Source</th>
                                     <th>Company Name</th> 
                                     <th>Contact Detail</th> 
                                     <th>Email ID</th>
                                     <th>Machine Type</th>
                                     <th>Machine</th>
                                     <th>Address</th>
                                     <th>Remarks</th>
                                     <th>Manager</th>
                                     <th>Last Updated On</th>                                    
                                                                      
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->

<div id="updateprogress" class="modal fade" role="dialog">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Leads/approvalorrejection"  enctype="multipart/form-data">
<div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="font-weight: bold; text-align:center;">Approve or Reject Quotation</h4>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-12">
<input type="hidden" id="leadid" value="" name="leadid">

<div class="form-group">
<label>Change Stage <span style="color:red" id="error_taskstatus">*</span></label>
<select name="taskstatus" id="taskstatus" onchange="checkifrejected();" class="form-control" required>
    <option value="">Select Option</option>
<option value="1">Approve</option>
<option value="0">Reject</option>

</select>
</div>
</div>
</div>

<div class="row"  id="remarkbox" style="display:none;">

<div class="col-md-12">
<div class="form-group">
<label>Remarks <span style="color:red" id="error_taskremarks"></span></label>
<textarea name="taskremarks" id="taskremarks" class="form-control"></textarea>
</div>
</div>
</div>



<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" onclick="taskupdationvalidation();" value="Submit" class="btn btn-success">
</div>
</div>

</div>
<!-- <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div> -->
</div>

</div>
</form>
</div>

<script type="text/javascript">
function approvalwindow(id){
$("#updateprogress").modal('show');
$("#leadid").val(id);

}
</script>

<script type="text/javascript">
    function checkifrejected() {
       var taskstatus = $("#taskstatus").val();
       if(taskstatus==0){
        $("#remarkbox").css('display','block');
       // $("#taskremarks").attr('Required','true');
       }else{
        $("#remarkbox").attr('display','none');
        //$("#taskremarks").attr('Required','false');
       }
    }
</script>

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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

       

        <script>
$( document ).ready(function() {

$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data export'
            }
        ],
"stateSave": true,
"sAjaxSource": <?php echo json_encode(page_url . 'Leads/lead_stages_by_selected_user_list/' . $lead_stage . '/' . $this->uri->segment(4) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')); ?>,

"aoColumns": [
				{ mData: 'sr_no' } 
                <?php $q = $this->db->select('lead_id')->from('lead_stage')->where('lead_id',$this->uri->segment(3))->where('dead_end',1)->get();
                                     if($q->num_rows()>0){?>
                                         ,{ mData: 'leadlostreason' } 
                                     <?php }else if($this->uri->segment(3)==36){?>
                                        ,{ mData: 'approvereject' } 

                                    <?php  }else{ 
                                        if($this->uri->segment(3)==35){}else{?>
                ,{ mData: 'progress' } 
            <?php } }?>
                <?php
                if($quotestep==1 || $this->uri->segment(3)==36 || $this->uri->segment(3)==37 || $this->uri->segment(3)==38 || $this->uri->segment(3)==39)
                {
                ?>
                ,{ mData: 'quote_step' } 
                <?php } ?>
                ,{ mData: 'oppno' },
                { mData: 'opptype' },
                { mData: 'source' },
                { mData: 'company' },
                { mData: 'customerdetail' },
                { mData: 'email' },
                { mData: 'type' },
                { mData: 'product' },
                { mData: 'address' },
                { mData: 'remarks' },
                { mData: 'manager' },
                { mData: 'updatedOn' }

				
				
		]
});  

        $(document).ready(function() {
             $('#start').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });
             $('#end').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });
        }); 

});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
	
	$("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
	
	return false;
}

});
});
</script>
<script>
$(document).ready(function(){
$("#updateprogressform").on("submit", function(){
$("#pageloader1").fadeIn();
});//submit
});//document ready
</script>

<script type="text/javascript">
    function getMemberList() {
        var team_members=$("#team_members").val();
        var startdate=$("#start").val();
        var enddate=$("#end").val();
        location.href = '<?php echo page_url;?>Leads/newleads/'+team_members+'/'+startdate+'/'+enddate;
            
    }
</script>
    </body>
</html>
