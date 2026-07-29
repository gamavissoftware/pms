<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Help Ticket List</title>
    <meta name="google" content="notranslate">
    <!-- DataTables -->
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    
    <style>
        table.manglesh thead th {
            background-color: #332b24 !important;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        table.manglesh tbody td {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->
    <?php $this->load->view('common/info-section.php'); ?>

    <div class="wrapper">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12">
                    <div class="page-title-box text-center">
                        <h4 class="page-title text-center">MEETING LIST</h4><hr>
                    </div>
                </div>
            </div>
           <div class="row">
               <div class="col-md-2"></div>
               <div class="col-md-8">
                <form method="post" action="<?php echo page_url;?>Maintenance_support/filterlistmeetings">
                   <div class="row card-box">
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Start Date</label>
                                <input type="date" class="form-control" name="startdate" id="startdate" value="<?php echo date('Y-m-d');?>">
                           </div>
                       </div>
                       <div class="col-md-3">
                             <div class="form-group">
                               <label>End Date</label>
                                <input type="date" class="form-control" name="enddate" id="enddate" value="<?php echo date('Y-m-d');?>">
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="form-group">
                               <label>User</label>
                               <select class="form-control select2" name="user_id" id="user_id">
                                   <?php 
                                $this->db->select('user_id, title, first_name, last_name')->from('system_users')->where('user_status',1);
                                    if($_SESSION['logged_in']['role']==12){

                                    }else{
                                        $this->db->where('user_id',$_SESSION['logged_in']['user_id']);
                                    }
                                    $query = $this->db->get();
                                    foreach($query->result() as $row){
                                   ?>
                                   <option value="<?php echo $row->user_id;?>"><?php echo strtoupper($row->title." ".$row->first_name." ".$row->last_name);?></option>
                                   <?php }?>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group" style="margin-top:23px;">
                               <input type="submit" value="Filter" name="" class="btn btn-success">
                           </div>
                       </div>
                   </div>


               </div>
               <div class="col-md-2"></div>
           </div>
            <!-- End Page Title -->

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Meeting Date</th>
                                    <th>Meeting Time</th>
                                    <th>Department</th>
                                    <th>User Name</th>
                                    <th>Total Time Spend</th>
                                    <th>Attachment</th>
                                    <th>Remarks</th>
                                    <th>Meeting Conducted By</th>
                                </tr>
                            </thead>
                            <tbody>
	                                <?php
	                                    $i=1;
	                                 foreach ($tickets as $ticket) : ?>
	                                    <tr>
	                                        <td><?php echo $i; ?></td>
	                                        <td><?php echo date('d-m-Y',strtotime($ticket['meeting_date'])); ?></td>
	                                        <td>
	                                            <?php
	                                                $meetingTimeDisplay = date('h:i A',strtotime($ticket['meeting_time']));
	                                                if (isset($ticket['meeting_end_time']) && $ticket['meeting_end_time'] != '' && $ticket['meeting_end_time'] != '00:00:00') {
	                                                    $meetingTimeDisplay .= ' - ' . date('h:i A',strtotime($ticket['meeting_end_time']));
	                                                }
	                                                echo $meetingTimeDisplay;
	                                            ?>
	                                        </td>
	                                        <td><?php echo $ticket['department']; ?></td>
	                                        <td><?php echo $ticket['mainusertitle']." ".$ticket['first_name']." ".$ticket['last_name']; ?></td>
	                                        <td>
	                                            <?php 
	                                                // Parse the TIME format into hours, minutes, and seconds
                                                list($hours, $minutes, $seconds) = explode(':', $ticket['total_time_spend']);

                                                // Convert the TIME into total seconds (optional, if needed)
                                                $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

                                                // Format it back to HH:MM:SS for display
                                                $formattedTime = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

                                                // Display the formatted time
	                                                echo $formattedTime;

                                                // Uncomment the line below if you also want to display the total seconds (optional)
                                                // echo "<br>Total Time in Seconds: " . "<span style='font-size:20px; color:red; font-weight: bold;'>"$totalSeconds."</span>";
                                            ?>

                                            </td>
                                        <td>
                                            <?php if($ticket['attachment']<>''){?>
                                            <a href="<?php echo maintenance;?><?php echo $ticket['attachment']; ?>" download>Click to Download</a>
                                            <?php }?>
                                        </td>
                                        <td><?php echo $ticket['remarks']; ?></td>
                                        <td><?php echo strtoupper($ticket['title']." ".$ticket['fname']." ".$ticket['lname']); ?></td>
                                    </tr>
                                <?php $i++; 
                                    endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php $this->load->view('common/footer'); ?>
            <!-- End Footer -->
        </div> <!-- end container -->
    </div> <!-- end wrapper -->

    <!-- Scripts -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.min.js"></script>
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
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                responsive: true,
                fixedHeader: true
            });
        });
    </script>
    <script language="javascript" type="text/javascript">   

$(document).ready(function() {
    $('.select2').select2({ });
    $('.select3').select2({ });
    $('.select4').select2({ });
$("#save").click(function() {
    var dfno= $("#dfno").val();
if(dfno=='')
{
    $("#error_dfno").html('Required!');
}
var department = $("#department").val();
if(department=='')
{
    $("#error_department").html('Required!');
}

var user_id = $("#user_id").val();
if(user_id=='')
{
    
    $("#error_user_id").html('Required!');
}

if(dfno==''|| department=='' || user_id=='' )
{
    
    return false;
}

});
});
</script>
 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>
</html>
