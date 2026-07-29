<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> All Meeting Scheduled List</title>
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
                        <h4 class="page-title text-center">ALL SCHEDULED MEETING LIST</h4><hr>
                    </div>
                </div>
            </div>
           <div class="row" style="display:none">
               <div class="col-md-2"></div>
               <div class="col-md-8">
                <form method="post" action="<?php echo page_url;?>Form/filterpaymentdata">
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
</form>

               </div>
               <div class="col-md-2"></div>
           </div>
            <!-- End Page Title -->

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>Meeting Date</th>
                <th>Time</th>
                <th>Agenda of Meeting</th>
                <th>Accepted Participants</th>
                <th>Declined Participants (With Remarks)</th>
                <th>Pending Responses</th>
                <th>Added By</th>
                <th>Added On</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sr_no = 1;

            // Fetch meetings and participant information
            $query = $this->db->select('m.id as meeting_id, m.date_of_meeting, m.time_of_meeting, m.agenda, m.added_on, u.first_name as added_by_first_name, u.last_name as added_by_last_name')
                              ->from('df_meeting_notification_alert m')
                              ->join('system_users u', 'm.added_by = u.user_id', 'left')
                              ->order_by('m.added_on','desc')
                              ->get();

            foreach ($query->result() as $meeting) {
                // Fetch participant information for each meeting
                $participant_query = $this->db->select('p.response_status, p.response_reason, su.first_name, su.last_name')
                                              ->from('meeting_participants p')
                                              ->join('system_users su', 'p.participant_id = su.user_id', 'left')
                                              ->where('p.meeting_id', $meeting->meeting_id)
                                              ->get();

                $accepted_participants = [];
                $declined_participants = [];
                $pending_participants = [];

                foreach ($participant_query->result() as $participant) {
                    if ($participant->response_status === 'Available') {
                        $accepted_participants[] = $participant->first_name . ' ' . $participant->last_name;
                    } elseif ($participant->response_status === 'Not Available') {
                        $declined_participants[] = $participant->first_name . ' ' . $participant->last_name . " (Reason: " . $participant->response_reason . ")";
                    } else {
                        $pending_participants[] = $participant->first_name . ' ' . $participant->last_name;
                    }
                }

                $accepted_list = !empty($accepted_participants) ? implode(', ', $accepted_participants) : 'N/A';
                $declined_list = !empty($declined_participants) ? implode(', ', $declined_participants) : 'N/A';
                $pending_list = !empty($pending_participants) ? implode(', ', $pending_participants) : 'N/A';
                ?>
                <tr>
                    <td><?php echo $sr_no++; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($meeting->date_of_meeting)); ?></td>
                    <td><?php echo date('h:i A', strtotime($meeting->time_of_meeting)); ?></td>
                    <td><?php echo $meeting->agenda; ?></td>
                    <td><?php echo $accepted_list; ?></td>
                    <td><?php echo $declined_list; ?></td>
                    <td><?php echo $pending_list; ?></td>
                    <td><?php echo $meeting->added_by_first_name . ' ' . $meeting->added_by_last_name; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($meeting->added_on)); ?><br><?php echo date('h:i A',strtotime($meeting->added_on));?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
                    </div>
                </div>
            </div>

           

<script type="text/javascript">
    $(document).ready(function () {
    // Show modal and pre-fill data
    $('.update-status-btn').on('click', function () {
        const id = $(this).data('id');
        const amount = $(this).data('amount');
        const yourremark = $(this).data('yourremark');

        $('#paymentId').val(id);
        $('#totalAmount').val(amount);
        $('#yourremark').val();
        $('#status').val('');

        $('#updatePaymentModal').modal('show');
    });

    // Submit the form via AJAX
    $('#updatePaymentForm').on('submit', function (e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?php echo page_url; ?>Form/updatePaymentStatus',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
            console.log(response); // Debug the response
            let res = typeof response === 'string' ? JSON.parse(response) : response;

            if (res.status === 'success') {
            alert('Payment status updated successfully!');
            $('#updatePaymentModal').modal('hide');
            location.reload(); // Reload the page to reflect changes
            } else {
            alert('Failed to update payment status.');
            }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
});

</script>

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
