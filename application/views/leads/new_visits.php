<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$user_id=$_SESSION['logged_in']['user_id'];
if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
{
$user=$CI->Salescrm_model->getusername($this->uri->segment(5));
}else
{
$user="ALL";
}

$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="2">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">User</th>
             
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$user.'</td>
                </tr>
                </tbody></table>';

                $totalvisit=$CI->Salescrm_model->total_visits_only_new($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5)); 
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
                        <h4 class="page-title text-center">New Customer Visits<h4>
                    </div>
                </div>

                <form action="<?php echo page_url;?>Leads/filter_todays_visit_new/<?php echo $this->uri->segment(6);?>" method="post">
                <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="from_date" class="form-control" value="<?php echo $this->uri->segment(3);?>" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="date" name="to_date" class="form-control" value="<?php echo $this->uri->segment(4);?>" required="">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>User</label>
                                            <select class="form-control" name="user">
                                                <option value="ALL" <?php if($this->uri->segment(3)=="ALL"){?> selected <?php } ?>>ALL</option>
                                                <?php 
                                    $rest=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status',1)->get();
                                    if($rest->num_rows()>0)
                                    {
                                        foreach($rest->result() as $row)
                                        {
                                    ?>
                                    <option value="<?php echo $row->user_id;?>" <?php if($this->uri->segment(3)==$row->user_id){?> selected <?php   } ?>><?php echo $row->first_name;?> <?php echo $row->last_name;?></option>
                                    <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                   
                                  
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>

                        </form>
                <!-- end page title end breadcrumb -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-4 card-box" style="min-height:170px;">
                            <?php echo $filter_criteria;?>
                        </div>

                       <div class="col-md-4 card-box" style="min-height: 170px;">
                        <?php if($user<>'ALL'){?>
                        <h4 class="text-center">Visit's for <?php echo $user;?></h4>
                    <?php }else{?> 
                     <h4 class="text-center">All Visit's</h4>
                    <?php } ?>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;"><?php echo $totalvisit;?></p>
                       

                    </div>
                    </div>
                </div>
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                     <th>Customer Type</th>
                                     <th>Company</th>
                                     <th>Customer Name</th>                                  
                                     <th>Primary Contact</th>
                                     <th>Alternate Contact</th>
                                     <th>Address</th>
                                     <th>Lead Manager</th>
                                     <th>Attachment</th>
                                    <th>Next Visit Scheduled On</th>

                                                  
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!--  <div class="row">
                    <div class="page-title-box col-md-12">
                        <h4 class="page-title text-center">Old Customer Visits<h4>
                    </div>
                </div> -->

                <!--  <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
 <table id="example1" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                     <th>Sr No.</th>
                                     <th>Company</th>
                                     <th>Customer Name</th>                                  
                                     <th>Remarks</th>
                                     <th>Added By </th>
                                    <th>Added On</th>

                                                  
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div> -->
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
"sAjaxSource": "<?php echo page_url;?>Leads/new_visit_list_today/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",

"aoColumns": [
				
                { mData: 'sr_no' } ,
                { mData: 'customer_type' },
                { mData: 'company' },
                { mData: 'customer_name' },                
                { mData: 'mobile' },               
                { mData: 'alternatedetail' },
                { mData: 'address' },
                { mData: 'leadmanager'},
                { mData: 'lastupdatedon'} ,
                 { mData: 'next_visit'},        

               

				
				
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

$('#example1').dataTable({
"bProcessing": true,
"pagination":true,
"pagination":true,
"pageLength": 100,
"stateSave": true,
"sAjaxSource": "<?php echo page_url;?>Leads/visit_list_today_for_old_customer/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

"aoColumns": [
                
                { mData: 'sr_no' } ,
                { mData: 'company' },
                { mData: 'customer_name' },                
                { mData: 'remarks'},
                { mData: 'addedby'} ,
                 { mData: 'addedon'}
                
                
        ]
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