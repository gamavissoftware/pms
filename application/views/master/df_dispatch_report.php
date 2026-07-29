<?php 
$CIA =& get_instance();
$CIA->load->model('Dashboard_model');

$raw_start_date = $this->uri->segment(3);
$raw_end_date = $this->uri->segment(4);

$report_start_date = (strtotime((string) $raw_start_date) !== false) ? date('Y-m-d', strtotime($raw_start_date)) : date('Y-m-01');
$report_end_date = (strtotime((string) $raw_end_date) !== false) ? date('Y-m-d', strtotime($raw_end_date)) : date('Y-m-t');

if ($report_start_date > $report_end_date) {
    $swap_date = $report_start_date;
    $report_start_date = $report_end_date;
    $report_end_date = $swap_date;
}

$report_start_display = date('d-m-Y', strtotime($report_start_date));
$report_end_display = date('d-m-Y', strtotime($report_end_date));
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> DF Dispatch Report Between <?php echo $report_start_display;?> to <?php echo $report_end_display;?></title>
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
        <link href="<?php echo assets_url;?>css/our.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
            <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
    <style>
    table.manglesh thead th {
    background: <?php echo $LOGO->colorcode;?>;
    color:#fff;
    font-weight:bold;
    text-align:center;
    }
    table.manglesh tbody td {
    text-align:center;
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
                        <div class="page-title-box">
                         <div class="btn-group pull-right"></div>
                           <h4 class="text-center" style="padding:10px; 10px; 10px; 10px;">DF DISPATCH REPORT BETWEEN <?php echo $report_start_display;?> to <?php echo $report_end_display;?></h4>
                        </div>
                    </div>

                </div>
                <form method="post" action="<?php echo page_url;?>Dashboard/filterdfdispatchviamonth/">
                <div class="row card-box">
                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="startdate" id="startdate" value="<?php echo $report_start_date;?>">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="enddate" id="enddate" value="<?php echo $report_end_date;?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="margin-top:23px;">
                            <input type="submit" class="btn btn-success" name="" value="Search">
                        </div>
                        
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </form>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
                            <table id="example5" class="table manglesh table-striped table-bordered">
                            <thead>
                            <tr>
                            <th>S. NO.</th>
                            <th>DF NO.</th>
                            <th>PRIORITY</th>
                            <th>MODEL</th>
                            <th>AUTOMATION</th>
                            <th>DF DATE</th>
                            <th>BOM DATE</th>
                            <th>LOADING</th>
                            <th>COMPLETION STATUS</th>
                            <th>COMPLETION DATE</th>
                            <th>DISPATCH DATE</th>
                            <th>DESIGN</th>
                            <th>MARKETING</th>
                            

                            </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i=1;
                                $qq = $this->db->distinct()
                                    ->select('a.id, a.priority_marked, a.df_no, a.df_description, a.added_on, c.title, c.first_name, c.last_name, b.end_date as dispatch_end_date, b.task_status as dispatch_task_status, b.task_completed_on as dispatch_completed_on')
                                    ->from('df_release a')
                                    ->join('task_department_wise_scheduling b', 'a.id=b.df_id AND b.taskid=103', 'inner')
                                    ->join('system_users c', 'a.added_by=c.user_id', 'left')
                                    ->where('b.end_date >=', $report_start_date)
                                    ->where('b.end_date <=', $report_end_date)
                                    ->order_by('b.end_date', 'asc')
                                    ->order_by('a.id', 'desc')
                                    ->get();
                                foreach($qq->result() as $row){

                                ?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row->df_no;?></td>
                                <td><?php 
                                if($row->priority_marked==0){?>
                                    <span class="btn btn-xs btn-warning" onclick="showpriority(<?php echo $row->id;?>);">Set Priority</span>
                               <?php  }else{
                                    echo $row->priority_marked;
                                }
                            ?></td>
                                <td><?php echo $row->df_description;?></td>
                                <td><?php 
                                    $lead_row = $this->db->select('lead_id')
                                        ->from('poreceived')
                                        ->where('df_id', $row->id)
                                        ->order_by('id', 'desc')
                                        ->limit(1)
                                        ->get()
                                        ->row();
                                    $lead_id = !empty($lead_row->lead_id) ? $lead_row->lead_id : 0;

                                    $q = $this->db->select('plc_make')
                                        ->from('quotation_customer_data a')
                                        ->join('quotation_annexture_1 b','a.id=b.record_id','left')
                                        ->where('a.lead_id', $lead_id)
                                        ->limit(1)
                                        ->get();
                                    if($q->num_rows()>0){
                                        foreach($q->result() as $ro);
                                        echo $ro->plc_make;
                                    }else{
                                        echo "N/A";
                                    }
                            ?></td>
                                <td><?php echo date('d-m-Y',strtotime($row->added_on));?></td>
                                <td><?php 
                                    $q2 = $this->db->select('task_completed_on')->from('task_department_wise_scheduling')->where('taskid',88)->where('df_id',$row->id)->where('task_status',1)->get();
                                    if($q2->num_rows()>0){
                                        foreach($q2->result() as $doneinfo);
                                        echo date('d-m-Y',strtotime($doneinfo->task_completed_on));
                                    }else{
                                        echo "NOT RELEASED YET";
                                    }

                            ?></td>
                                <td><?php 
                                    $q2 = $this->db->select('task_completed_on')->from('task_department_wise_scheduling')->where('taskid',26)->where('df_id',$row->id)->where('task_status',1)->get();
                                    if($q2->num_rows()>0){
                                        foreach($q2->result() as $doneinfo);
                                        echo date('d-m-Y',strtotime($doneinfo->task_completed_on));
                                    }else{
                                        echo "NOT RELEASED YET";
                                    }

                            ?></td>
                                <td><?php 
                                    $donepercent = $CIA->Dashboard_model->get_task_completion_percentage($row->id);
                                    echo $donepercent;
                            ?></td>
                                <td><?php 
                                    $q2 = $this->db->select_max('task_completed_on', 'latest_completion')->from('task_department_wise_scheduling')->where('df_id', $row->id)->where('task_status', 1)->where('task_completed_on !=', '0000-00-00 00:00:00')->get();
                                    if($q2->num_rows()>0){
                                        foreach($q2->result() as $doneinfo);
                                        if(!empty($doneinfo->latest_completion) && $doneinfo->latest_completion != '0000-00-00 00:00:00'){
                                            echo date('d-m-Y',strtotime($doneinfo->latest_completion));
                                        }else{
                                            echo "NOT COMPLETED YET";
                                        }
                                    }else{
                                        echo "NOT COMPLETED YET";
                                    }

                            ?></td>
                                <td><?php 
                                    if($row->dispatch_task_status == 1 && !empty($row->dispatch_completed_on) && $row->dispatch_completed_on != '0000-00-00 00:00:00'){
                                        echo date('d-m-Y', strtotime($row->dispatch_completed_on));
                                    }else{
                                        echo date('d-m-Y', strtotime($row->dispatch_end_date));
                                    }
                                ?></td>
                                <td>
                                    <?php 
                                        $q = $this->db->select('b.first_name, b.last_name')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->where('a.df_id',$row->id)->where('a.department_id',11)->limit(1)->get();
                                        if($q->num_rows()>0){
                                            foreach($q->result() as $row1){
                                                echo ucwords(strtolower($row1->first_name." ".$row1->last_name));
                                            }
                                        }else{
                                            echo "NOT ASSIGNED YET";
                                        }
                                    ?>
                                </td>
                                <td><?php echo ucwords(strtolower(trim($row->title." ".$row->first_name." ".$row->last_name)));?></td>
                            </tr>

                        <?php $i++; }?>
                        <?php if($i===1){ ?>
                            <tr>
                                <td colspan="13">No DF dispatch records found for the selected date range.</td>
                            </tr>
                        <?php } ?>
                            </tbody>
                            </table>

                        </div>

                    </div>

                </div>

<div id="updateprogress" class="modal fade" role="dialog">
<form id="updateprogressform" method="post" action="<?php echo page_url;?>Dashboard/setdfpriority/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>"  enctype="multipart/form-data">
<div id="pageloader1">
<img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
</div>
<div class="modal-dialog">
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title" style="font-weight: bold; text-align:center;">Set Priority</h4>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-12">
<input type="hidden" id="taskkiid" value="" name="taskkiid">

<div class="form-group">
<label>Priority <span style="color:red" id="error_taskstatus">*</span></label>
<input type="number" name="prioritymarked" id="prioritymarked" value="" class="form-control" required>
</div>
</div>

</div>





<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<input type="submit" style="width: 100%;" name="" value="Submit" class="btn btn-success">
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
 

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
<script type="text/javascript">
    function showpriority(i){
       $("#updateprogress").modal('show');
$("#taskkiid").val(i);
    }
</script>
<script>
$(document).ready(function(){
$("#updateprogressform").on("submit", function(){
$("#pageloader1").fadeIn();
});//submit
});//document ready
</script>

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(document).ready(function(){
       $("#teamupdate").attr('disabled',false);
       $("#teamupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#teamupdate").attr('disabled',true);
     $("#teamupdate").val('Please Wait...');
  });//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#savedata").click(function() {
var uploaddf = $("#uploaddf").val();
if(uploaddf=='')
{
    $("#error_uploaddf").html('Required!');
    $("#uploaddf").css("border", "1px solid red");
}

var dfno = $("#dfno").val();
if(dfno=='')
{
    $("#error_dfno").html('Required!');
    $("#dfno").css("border", "1px solid red");
}

if(uploaddf=='' || dfno=='')
{
    
    return false;
}

});
});
</script>    
        <script>

$( document ).ready(function() {

$('#example5').dataTable({

"bProcessing": true,

    fixedHeader: true,

"pagination":true,
pageLength:1000,
"dom": 'Bfrtip',
              "buttons": [
                    'excelHtml5',
                ]

});   

});



</script>

</body>
</html>
