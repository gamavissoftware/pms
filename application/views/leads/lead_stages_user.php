<?php 
$lead_stage = $this->uri->segment(3);
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
                        <div class="page-title-box col-md-1">
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btns" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="page-title-box col-md-12">
                        <h4 class="page-title text-center">Leads at <?php echo ucwords(strtolower($lead_status));?> Stage</h4>
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
                                     <th>Query No/Date</th>
                                      <?php if($this->uri->segment(3)==27){}else{?>
                                     <th>Update Progress</th>
                                 <?php }?>
                                     <?php 
                                        if($this->uri->segment(3)==22){?>
                                            <th>Approval Pending</th>
                                        <?php }?>
                                       
                                        <?php 
                                        if($this->uri->segment(3)=='18'){?>
                                        <th>Visit Date</th>
                                        <th>Visit Time</th>
                                        <?php  }
                                        ?>
                                     <th>Customer Type</th>
                                     <th>Distributor Name</th>
                                     <th>Lead Source</th>
                                     <th>Company</th>
                                     <th>Customer Name</th>                                  
                                     <th>Designation</th>                                  
                                     <th>Products</th>
                                     <th>Primary Contact</th>
                                     <th>Alternate Contact</th>
                                     <th>Address</th>
                                     <?php if($reason>0)
                                     { ?>
                                    <th>Unqualified Reason</th>
                                     <?php } ?>

                                     <th>Lead Remarks</th>
                                    
                                     <th>Lead Manager</th>
                                     <th>Last Updated On</th>
                                      <?php if($this->uri->segment(3)==4 || $this->uri->segment(3)==5){?>
                                       <th>Modify Quotation</th>
                                   <?php }else{}?>

                                 
                                    
                                     <?php if($quotation_step == 1 || $quotation_revised_step == 1 || $getConversionLeadStage == $lead_stage) {?>
                                     <th>Quotation</th>
                                     <?php } ?>                                    
                                </tr>
                                </thead>
                                
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
        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

       

        <script>
$( document ).ready(function() {

$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Leads/lead_stage_user_list/<?php echo $lead_stage;?>/<?php echo base64_encode(current_url());?>",

"aoColumns": [
                
                { mData: 'sr_no' } ,
                { mData: 'unique' }
                <?php if($this->uri->segment(3)==27){}else{?>,
                { mData: 'update'},<?php }?> 
                <?php 
                if($this->uri->segment(3)==22){?>
                    { mData: 'pendingapproval' }, 
                <?php }?>
                
                <?php 
                if($this->uri->segment(3)==18){?>
                { mData: 'visitdate' },
                { mData: 'visittime' },
                <?php }?>
                { mData: 'customer_type' },
                { mData: 'distributor' },
                { mData: 'leadsource' },
                { mData: 'company' },
                { mData: 'customer_name' },                
                { mData: 'designation' },                
                { mData: 'products' }, 
                { mData: 'mobile' },               
                { mData: 'alternatedetail' },
                { mData: 'address' },

                <?php if($reason>0)
                { ?>
                     { mData: 'reason' },

                <?php } ?>
                { mData: 'lastremarks' },
                { mData: 'leadmanager'},
                { mData: 'lastupdatedon'}
                <?php if($this->uri->segment(3)==4 || $this->uri->segment(3)==5){?>
                    ,{ mData: 'modifyquotation'}
                <?php }else{}?>
                
                 <?php if($quotation_step == 1 || $quotation_revised_step == 1 || $getConversionLeadStage == $lead_stage) {?>
                 ,{ mData: 'quotation'}
                <?php } ?>
                
                
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