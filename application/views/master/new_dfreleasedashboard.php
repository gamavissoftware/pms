<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');

// --- SECURITY CONFIGURATION ---
// Define who can see the Management Report Filter
$allowed_report_users = [61, 62, 162, 161, 139, 189, 114];
$current_user_id = $this->session->userdata['logged_in']['user_id'];
// ------------------------------
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?> DF List with Delay info</title>
        
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

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        
        <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

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
                vertical-align: top;
            }
            /* Ticket Badge Style */
            .ticket-badge {
                background-color: #f0ad4e;
                color: #fff;
                padding: 3px 8px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: bold;
                cursor: pointer;
            }
            .ticket-badge:hover {
                background-color: #ec971f;
            }
        </style>
    </head>

    <body>

        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <div class="wrapper">
            <div class="container-fluid">

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                           <div class="btn-group pull-right"></div>
                           <?php if($this->uri->segment(3)<>''){?>
                                <h4 class="text-center" style="padding:10px; 10px; 10px; 10px; color:Red;">DFs LIST RUNNING DELAY</h4>
                           <?php }else{?>
                                <h4 class="text-center" style="padding:10px; 10px; 10px; 10px;">All Running DF</h4>
                           <?php }?>
                        </div>
                    </div>
                </div>
                <?php if(in_array($current_user_id, $allowed_report_users)): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box filter-card" style="background-color: #f9f9f9; border: 1px solid #e3e3e3;">
                            <h4 class="m-t-0 header-title"><b><i class="fa fa-file-text-o"></i> Management Report Filter</b></h4>
                            
                            <form method="post" action="<?php echo page_url; ?>Dashboard/management_report" class="form-inline" target="_blank">
                                <div class="form-group m-r-10">
                                    <label for="start_date" class="m-r-10">From:</label>
                                    <input type="text" class="form-control datepicker" name="start_date" id="start_date" placeholder="Start Date" required>
                                </div>
                                <div class="form-group m-r-10">
                                    <label for="end_date" class="m-r-10">To:</label>
                                    <input type="text" class="form-control datepicker" name="end_date" id="end_date" placeholder="End Date" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> Generate Report</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example5" class="table manglesh table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>S. NO.</th>
                                        <th>DF NO.</th>
                                        <th>DOWNLOAD</th>
                                        <th>PO DATE</th>
                                        <th>MARKETING PERSON</th>
                                        <th>DF RELEASE DATE</th>
                                        <th>DF START DATE</th>
                                        <th>START DATE</th>
                                        <th>END DATE</th>
                                      <th>BOUGHT OUT ITEMS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // --- START OF OPTIMIZED QUERY ---
                                    $m = 1;
                                    $reportid = $this->uri->segment(3);
                                    
                                    $this->db->select(
                                        "df.id, df.df_no, df.added_on, df.df_upload, df.on_hold, df.machine_id,
                                        po.podate, po.po_attachment, po.lead_id, po.id as po_id,
                                        po.df_number as po_df_number, 
                                        CONCAT(u.title, ' ', u.first_name, ' ', u.last_name) as dfowner,
                                        u.user_id as owner_id,
                                        
                                        MIN(tasks.start_date) as df_start_date,
                                        MAX(tasks.end_date) as projected_completion_date,
                                        
                                        COUNT(DISTINCT tasks.id) as total_tasks,
                                        SUM(CASE WHEN tasks.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,
                                        
                                        (SELECT COUNT(DISTINCT id) 
                                         FROM communication_ticket_system 
                                         WHERE df_id = df.id AND ticket_status = 0) as open_ticket_count,

                                        GROUP_CONCAT(
                                            CASE 
                                                WHEN tasks.task_status = 1 
                                                THEN CONCAT(tasks.task_completed_on, '|', tasks.end_date) 
                                                ELSE NULL 
                                            END
                                        SEPARATOR ';') as completed_tasks_data,
                                        
                                        MAX(CASE WHEN tasks.task_status = 1 THEN tasks.task_completed_on ELSE NULL END) as max_completion_date",
                                        false
                                    );
                                    $this->db->from('df_release df');
                                    $this->db->join('poreceived po', 'po.df_id = df.id', 'left');
                                    $this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
                                    $this->db->join('task_department_wise_scheduling tasks', 'tasks.df_id = df.id', 'left');
                                    
                                    $this->db->where('df.df_status', 0);
                                    $this->db->where('df.on_hold', 0);
                                    $this->db->group_by('df.id');
                                    
                                    $query = $this->db->get();

                                    if ($query->num_rows() > 0) {
                                        foreach ($query->result() as $rows) {
                                            
                                            // Fallback for DF Number if blank in df_release
                                            $display_df_no = $rows->df_no;
                                            if(empty($display_df_no) || $display_df_no == 0) {
                                                 $display_df_no = $rows->po_df_number;
                                            }

                                            // Calculations
                                            $podate = ($rows->podate && $rows->podate != '0000-00-00') ? date('d-m-Y', strtotime($rows->podate)) : "";
                                            
                                            $po_attachment = '';
                                            if ($_SESSION['logged_in']['user_id'] == $rows->owner_id && $rows->po_attachment) {
                                                $po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$rows->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
                                            }

                                            $gg = $this->db->select('id')->from('df_design_form_table')->where('lead_id', $rows->lead_id)->get();
                                            $edit = ($gg->num_rows() > 0) ? "<a href='".page_url."Dashboard/edit_df_project_form/".$rows->po_id."/".$rows->lead_id."'><i class='fa fa-pencil'></i></a>" : '';

                                            $completion_percentage = ($rows->total_tasks > 0) ? round($rows->completed_tasks * 100 / $rows->total_tasks) : 0;

                                            $max_delay_days = 0;
                                            $delaycount = 0;
                                            
                                            if (!empty($rows->completed_tasks_data)) {
                                                $completed_tasks_list = explode(';', $rows->completed_tasks_data);
                                                foreach ($completed_tasks_list as $task_data) {
                                                    list($completed_on, $end_date) = explode('|', $task_data);
                                                    if($completed_on && $completed_on != '0000-00-00') {
                                                        $completed_date_obj = date('Y-m-d', strtotime($completed_on));
                                                        if ($completed_date_obj > $end_date) {
                                                            $delaycount++;
                                                            $delay_days = $CIA->Task_model->getDays($end_date, $completed_date_obj, 1);
                                                            if ($delay_days > $max_delay_days) {
                                                                $max_delay_days = $delay_days;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            $delay_percentage = ($rows->completed_tasks > 0) ? round($delaycount * 100 / $rows->completed_tasks) : 0;

                                            if ($completion_percentage == 100 && $rows->max_completion_date && $rows->max_completion_date != '0000-00-00') {
                                                $actual_completion_date = $rows->max_completion_date;
                                            } else {
                                                $temp_date = date('Y-m-d', strtotime($rows->projected_completion_date . ' +' . $max_delay_days . ' days'));
                                                $actual_completion_date = $CIA->Task_model->SKIPsingle_holidays($temp_date);
                                            }

                                            // Display Dates
                                            $df_start_display = ($rows->df_start_date && $rows->df_start_date != '0000-00-00') ? date('d-m-Y', strtotime($rows->df_start_date)) : '--';
                                            $projected_display = ($rows->projected_completion_date && $rows->projected_completion_date != '0000-00-00') ? date('d-m-Y', strtotime($rows->projected_completion_date)) : '--';
                                            $actual_display = ($actual_completion_date && $actual_completion_date != '0000-00-00') ? date('d-m-Y', strtotime($actual_completion_date)) : '--';

                                            $df_delay_days = ($rows->projected_completion_date && $actual_completion_date)
                                                ? $CIA->Task_model->getDays($rows->projected_completion_date, $actual_completion_date, 1) : 0;

                                            $show = 1;
                                            if ($reportid != '') {
                                                $show = ($delay_percentage > 0) ? 1 : 0;
                                            }

                                            if ($show == 1) {
                                        ?>
                                        <tr>
                                            <td><?php echo $m; ?></td>
                                            <td><?php echo strtoupper($display_df_no); ?></td>
                                            <td>
                                                <a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $rows->df_upload;?>" download>
                                                    <span class="btn btn-primary btn-xs">Download DF</span>
                                                </a><br/><?php echo $edit;?>
                                            </td>
                                            <td>
                                                <?php echo $podate; ?>
                                                <?php echo $po_attachment;?>
                                            </td>
                                            <td><?php echo strtoupper($rows->dfowner); ?></td>
                                            <td><?php echo date('d-m-Y', strtotime($rows->added_on)); ?></td>
                                            <td><?php echo $df_start_display; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                              
    <?php
    // Lead ID se quotation_customer_data ka record nikalo
    $quotation = $this->db
        ->select('id')
        ->from('quotation_customer_data')
        ->where('lead_id', $rows->lead_id)
        ->get()
        ->row();

    if (!empty($quotation)) {

        // Brand data + heading + option name join
        $botout_data = $this->db
            ->select('
                qbd.id,
                qphm.name as description,
                qphmo.name as brand_name
            ')
            ->from('quotation_brand_data qbd')
            ->join('quote_parts_heading_master qphm', 'qphm.id = qbd.head_id', 'left')
            ->join('quote_parts_heading_master_options qphmo', 'qphmo.id = qbd.value_id', 'left')
            ->where('qbd.record_id', $quotation->id)
            ->get();

        if ($botout_data->num_rows() > 0) {
    ?>

    <table class="table table-bordered table-striped table-sm" style="margin-bottom:0px;">
        <thead style="background:#f1f1f1;">
            <tr>
                <th style="width:60px;">S.NO.</th>
                <th>DESCRIPTION</th>
                <th>BRANDS / MAKE</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
             <?php
        $b = 1;
        foreach ($botout_data->result() as $botout) {

            // checkbox checked hai ya nahi
            $checkedData = $this->db
                ->select('id, checked_on')
                ->from('quotation_brand_action')
                ->where('quotation_brand_id', $botout->id)
                ->get();

            $isChecked = ($checkedData->num_rows() > 0) ? 'checked' : '';

            
        ?>
                <tr>
                    <td><?php echo $b; ?></td>
                    <td><?php echo strtoupper($botout->description); ?></td>
                    <td><?php echo strtoupper($botout->brand_name); ?></td>
                   <td>

<?php if($checkedData->num_rows() > 0): 

    $checkedRow = $checkedData->row();
?>

    <span class="label label-success" style="font-size:10px;">
        Purchased
    </span>

    <br>

    <small style="color:#666;">
        <?php echo date('d-m-Y h:i A', strtotime($checkedRow->checked_on)); ?>
    </small>

<?php else: ?>

    <div id="purchase_area_<?php echo $botout->id; ?>">

        <input 
            type="checkbox"
            class="brand_checkbox"
            data-id="<?php echo $botout->id; ?>"
        >

    </div>

<?php endif; ?>

</td>
                </tr>
            <?php
                $b++;
            }
            ?>
        </tbody>
    </table>

    <?php
        } else {
            echo '<span class="text-danger">No Botout Data Found</span>';
        }

    } else {
        echo '<span class="text-danger">No Quotation Found</span>';
    }
    ?>

                                            </td>
                                        </tr>
                                    <?php 
                                            $m++;
                                            } // end show
                                        } // end foreach
                                    } // end if
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php $this->load->view('common/footer');?>
                </div> </div>
        <div class="modal fade" id="ticket-modal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="ticketModalLabel">Open Tickets for DF: [DF_NO]</h4>
                    </div>
                    <div class="modal-body">
                        <div id="ticket-modal-loader" style="text-align: center; padding: 30px;">
                            <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                            <p>Loading tickets...</p>
                        </div>
                        <div id="ticket-modal-content" style="max-height: 60vh; overflow-y: auto;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
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

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Datepickers
                $('.datepicker').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'dd-mm-yyyy'
                });

                // Initialize DataTables
                $('#example5').dataTable({
                    "bProcessing": true,
                    "fixedHeader": true,
                    "pagination": true,
                    "ordering": false // Disable ordering for complex calculated columns
                });

                // Ticket Modal Logic
                $('.wrapper').on('click', '.btn-view-tickets', function() {
                    var $modal = $('#ticket-modal');
                    var $loader = $('#ticket-modal-loader');
                    var $content = $('#ticket-modal-content');
                    
                    var dfId = $(this).data('df-id');
                    var dfNo = $(this).data('df-no');
                    
                    $modal.find('#ticketModalLabel').text('Open Tickets for DF: ' + dfNo);
                    $content.empty(); 
                    $loader.show();   
                    $modal.modal('show');

                    $.ajax({
                        url: '<?php echo page_url; ?>Task/ajax_get_df_tickets/' + dfId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            $loader.hide();
                            if (response.success && response.tickets.length > 0) {
                                var html = '';
                                $.each(response.tickets, function(index, ticket) {
                                    html += '<div style="border-bottom:1px solid #eee; padding:10px 0;">';
                                    html += '  <div style="color:#337ab7; font-weight:bold;">Task: ' + (ticket.task_name || 'N/A') + '</div>';
                                    html += '  <div style="font-size:12px; color:#777;"><strong>Ticket #:</strong> ' + ticket.help_ticket_no + '</div>';
                                    html += '  <div style="font-size:12px; color:#777;"><strong>By:</strong> ' + ticket.added_by_name + ' | <strong>On:</strong> ' + ticket.added_date + '</div>';
                                    html += '  <div style="margin-top:5px; padding-left:10px; border-left:3px solid #f0ad4e;">' + ticket.remarks + '</div>';
                                    html += '</div>';
                                });
                                $content.html(html);
                            } else {
                                $content.html('<div class="alert alert-info">No open tickets found for this DF.</div>');
                            }
                        },
                        error: function(xhr) {
                            $loader.hide();
                            $content.html('<div class="alert alert-danger">Error loading ticket details.</div>');
                        }
                    });
                });
            });

            function saveMachine(dfid) {
                var machineId = document.getElementById("select_machine"+dfid).value;
                if (machineId != "") {
                    $.ajax({
                        url: '<?php echo page_url;?>Task/save_machine',
                        type: 'POST',
                        data: { machine_id: machineId, dfid: dfid },
                        success: function(response) {},
                        error: function(xhr, status, error) {
                            alert('Error saving the machine');
                        }
                    });
                } else {
                    alert("Please select a machine.");
                }
            }
        </script>

        <script>
$(document).on('change', '.brand_checkbox', function () {

    var checkbox = $(this);
    var quotation_brand_id = checkbox.data('id');

    // CHECK
    if (checkbox.is(':checked')) {

        $.ajax({
            url: "<?php echo page_url?>Task/save_brand_action",
            type: "POST",
            data: {
                quotation_brand_id: quotation_brand_id
            },
           success: function (response) {

    var currentDate = new Date();

    var day = ("0" + currentDate.getDate()).slice(-2);
    var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
    var year = currentDate.getFullYear();

    var hours = currentDate.getHours();
    var minutes = ("0" + currentDate.getMinutes()).slice(-2);

    var ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;

    var formattedDate =
        day + '-' + month + '-' + year + ' ' +
        hours + ':' + minutes + ' ' + ampm;

    $('#purchase_area_' + quotation_brand_id).html(
        '<span class="label label-success" style="font-size:10px;">Purchased</span><br>' +
        '<small style="color:#666;">' + formattedDate + '</small>'
    );

}
        });

    } else {

        // UNCHECK CONFIRM
        if(confirm('Are you sure you want to uncheck this item?')) {

            $.ajax({
                url: "<?php echo page_url?>Task/delete_brand_action",
                type: "POST",
                data: {
                    quotation_brand_id: quotation_brand_id
                },
                success: function (response) {

                }
            });

        } else {

            checkbox.prop('checked', true);

        }

    }

});
</script>
    </body>
</html>