<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Payment Request List</title>
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
                        <h4 class="page-title text-center">PAYMENT REQUEST LIST</h4><hr>
                    </div>
                </div>
            </div>
           <div class="row">
               <div class="col-md-2"></div>
               <div class="col-md-8">
                <form method="post" action="<?php echo page_url;?>Form/filterpaymentdata">
                   <div class="row card-box">
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Start Date</label>
                                <input type="date" class="form-control" name="startdate" id="startdate" value="<?php echo date('Y-m-d',strtotime($this->uri->segment(3)));?>">
                           </div>
                       </div>
                       <div class="col-md-3">
                             <div class="form-group">
                               <label>End Date</label>
                                <input type="date" class="form-control" name="enddate" id="enddate" value="<?php echo date('Y-m-d',strtotime($this->uri->segment(4)));?>">
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
                                   <option value="<?php echo $row->user_id;?>" <?php if($this->uri->segment(5)==$row->user_id){echo "selected";}?>><?php echo strtoupper($row->title." ".$row->first_name." ".$row->last_name);?></option>
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
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Department</th>
                                    <th>User Name</th>
                                    <th>Total Amount</th>
                                    <th>Attachment</th>
                                    <th>Partucular</th>
                                    <th>Added By</th>
                                    <th>Added On</th>
                                    <th>Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $startdate = $this->uri->segment(3)." 00:00:00";
                                    $enddate = $this->uri->segment(4)." 23:59:59";
                                    $i=1;
                                    $this->db->select('a.*, b.department as departmentname, c.title, c.first_name, c.last_name, d.title as ptitle, d.first_name as fname, d.last_name as lname')->from('payment_request a')->join('departments b','a.department=b.department_id','left')->join('system_users c','a.user_id=c.user_id','left')->join('system_users d','a.added_by=d.user_id','left')->order_by('a.id','desc');
                                    $this->db->where('a.added_on BETWEEN "'.$startdate. '" and "'.$enddate.'"');
                                    $this->db->where('a.user_id',$this->uri->segment(5));
                                    $q = $this->db->get();
                                    foreach($q->result() as $row){
                                ?>
                                <tr>
                                    <td><?php echo $i;?></td>
                                    <td><?php echo date('d-m-Y',strtotime($row->date));?></td>
                                    <td><?php echo date('h:i A',strtotime($row->time));?></td>
                                    <td><?php echo ucwords(strtolower($row->departmentname));?></td>
                                    <td><?php echo $row->title." ".$row->first_name." ".$row->last_name;?></td>
                                    <td><?php echo $row->amount;?></td>
                                    <td><?php if($row->attachment){?>
                                            <a href="<?php echo maintenance;?><?php echo $row->attachment;?>" download="download"><span>Download</span></a>
                                        <?php }?></td>
                                    <td><?php echo $row->particular;?></td>
                                    <td><?php echo $row->ptitle." ".$row->fname." ".$row->lname;?></td>
                                    <td><?php echo date('d-m-Y h:i A',strtotime($row->added_on));?></td>
                                        <td>

                                            <?php if($row->payment_status==0 || $row->payment_status==2){
                                                if($row->payment_status==2){
                                                    echo "Pending<br> Last Remark: - <strong>".$row->payment_remarks."</strong><br><br>";
                                                }
                                            ?>
                                             <button 
                                        class="btn btn-success btn-xs update-status-btn" 
                                        data-id="<?php echo $row->id; ?>" 
                                        data-amount="<?php echo $row->amount; ?>" 
                                        data-particular="<?php echo $row->particular; ?>"
                                        >
                                        Update Payment Status
                                        </button>

                                           <?php  }else{
                                                if($row->payment_status==1){
                                                    echo "Paid Last Remark: - <strong>".$row->payment_remarks."</strong><br><br>";
                                                }else if($row->payment_status==2){
                                                    echo "Pending Last Remark: - <strong>".$row->payment_remarks."</strong><br><br>";
                                                }else{
                                                    echo "Rejected Last Remark: - <strong>".$row->payment_remarks."</strong><br><br>";
                                                }
                                            }
                                           ?>
                                       
                                       
                                        </td>
                                </tr>
                                <?php $i++;}?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="updatePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updatePaymentForm">
                <div class="modal-header">
                    <h4 class="modal-title">Update Payment Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="paymentId">
                    <div class="form-group">
                        <label>Total Amount</label>
                        <input type="text" class="form-control" id="totalAmount" readonly>
                    </div>
                    <div class="form-group">
                        <label>Remarks <span style="color:red;">*</span></label>
                        <textarea class="form-control" name="yourremark" id="yourremark" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status <span style="color:red;">*</span></label>
                        <select class="form-control" name="status" id="status">
                            <option value="1">Paid</option>
                            <option value="2">Pending</option>
                            <option value="3">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
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
