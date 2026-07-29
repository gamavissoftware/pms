<?php
$CIA =& get_instance();
$CIA->load->model('Task_model');

// --- ALL PHP LOGIC MOVED TO THE TOP ---

// 1. Initialize variables
$grand_total_loss = 0;
$table_rows_html = '';
$m = 1;

// 2. Get Financial Year Filter
$selected_fy = $this->input->post('financial_year');
$fy_start_date = '';
$fy_end_date = '';

if ($selected_fy) {
    $years = explode('-', $selected_fy);
    $fy_start_date = $years[0] . '-04-01'; //e.g., 2024-04-01
    $fy_end_date = $years[1] . '-03-31'; //e.g., 2025-03-31
}

// 3. Build Main Query (with new FY filter)
$this->db->select('id, df_no, added_on, df_upload');
$this->db->from('df_release');
$this->db->where('df_status', 1);

// --- Apply FY filter if set ---
if ($fy_start_date && $fy_end_date) {
    // Filter is on 'added_on' (DF Release Date)
    $this->db->where('added_on >=', $fy_start_date);
    $this->db->where('added_on <=', $fy_end_date);
}

$this->db->order_by('id', 'desc');
$q = $this->db->get();

if ($q->num_rows() > 0) {
    foreach ($q->result() as $rows) {
        // --- Get Project Start Date (from the earliest task) ---
        $q_start = $this->db->select_min('start_date', 'stdate')
                            ->from('task_department_wise_scheduling')
                            ->where('df_id', $rows->id)
                            ->get();
        $startdate_raw = $q_start->row()->stdate ?? '';
        
        // --- Get Planned Closer Date (from Dispatch Task ID 103) ---
        $q_end = $this->db->select('end_date')
                          ->from('task_department_wise_scheduling')
                          ->where('df_id', $rows->id)
                          ->where('taskid', 103) // Specifically getting the dispatch task
                          ->get();
        $planneddate_raw = $q_end->row()->end_date ?? '';

        // Process dates
        $planneddate = ($planneddate_raw && strtotime($planneddate_raw)) ? date('Y-m-d', strtotime($planneddate_raw)) : '';
        $startdate = ($startdate_raw && strtotime($startdate_raw)) ? date('Y-m-d', strtotime($startdate_raw)) : '';
        $planned_display = ($planneddate) ? date('d-m-Y', strtotime($planneddate)) : '--';

        // --- Get PO Details and Order Value (Optimized Query) ---
        $q5 = $this->db->select('a.podate, a.po_attachment, a.order_value, b.title, b.first_name, b.last_name')
                        ->from('poreceived a')
                        ->join('system_users b', 'a.added_by = b.user_id', 'left')
                        ->where('a.df_id', $rows->id)
                        ->get();
        
        // Initialize variables
        $podate = $dfowner = $po_attachment = '';
        $order_value = 0;

        if ($q5->num_rows() > 0) {
            $row5 = $q5->row();
            $podate = ($row5->podate && strtotime($row5->podate)) ? date('d-m-Y', strtotime($row5->podate)) : '';
            $dfowner = $row5->title . " " . $row5->first_name . " " . $row5->last_name;
            $po_attachment = '<a href="' . sfdocument . 'Taskdocument/' . $row5->po_attachment . '" download><span class="btn btn-warning btn-xs">Click to download PO</span></a>';
            $order_value = (float)$row5->order_value; // Get order value directly
        }

        // --- Get Actual Completion Date ---
        $q2 = $this->db->select_max('task_completed_on', 'latest_completion')
                        ->from('task_department_wise_scheduling')
                        ->where('df_id', $rows->id)
                        ->where('task_status', 1)
                        ->get();

        $latest_completion_raw = $q2->row()->latest_completion ?? '';
        $latest_completion = ($latest_completion_raw && strtotime($latest_completion_raw)) ? date('Y-m-d', strtotime($latest_completion_raw)) : '';
        $actual_display = ($latest_completion) ? date('d-m-Y', strtotime($latest_completion)) : '--';

        // --- Calculate Delays, Loss, and Total Completion Time ---
        $df_delay_days = 0;
        $loss_display = "N/A";
        $row_color = "";

        if ($planneddate && $latest_completion) {
            if ($latest_completion > $planneddate) {
                // --- PROJECT IS DELAYED ---
                $df_delay_days = $CIA->Task_model->getDays($planneddate, $latest_completion, 1);
                $row_color = 'style="background-color: #ffe6e6;"'; // Light red for delayed

                // --- Loss Calculation (Opportunity Cost @ 8%) ---
                if ($order_value > 0) {
                    if (!defined('OPPORTUNITY_COST_RATE')) {
                        define('OPPORTUNITY_COST_RATE', 0.08); // 8% annual rate
                    }
                    $daily_rate = (OPPORTUNITY_COST_RATE / 365);
                    $loss_amount = round(($order_value * $daily_rate) * $df_delay_days);

                    if ($loss_amount > 0) {
                        $grand_total_loss += $loss_amount; // Add to grand total
                        $loss_display = '<span style="color:red;"><strong><i class="fa fa-inr"></i> '
                                      . $CIA->Task_model->formatIndianCurrency($loss_amount)
                                      . '</strong></span>';
                    }
                }

            } elseif ($latest_completion < $planneddate) {
                // --- PROJECT IS EARLY ---
                $row_color = 'style="background-color: #e6ffe6;"'; // Light green for early
            }
        }

        // --- Total Days to Completion ---
        $total_days_to_completion = ($startdate && $latest_completion)
            ? $CIA->Task_model->getDays($startdate, $latest_completion, 1)
            : 0;

        // --- Build HTML row and append to variable ---
        $table_rows_html .= '<tr ' . $row_color . '>';
        $table_rows_html .= '<td>' . $m . '</td>';
        $table_rows_html .= '<td>' . strtoupper($rows->df_no) . '</td>';
        $table_rows_html .= '<td>
                                <a href="' . sfdocument . 'Taskdocument/dfattachment/' . $rows->df_upload . '" download>
                                    <span class="btn btn-primary btn-xs">DOWNLOAD DF</span>
                                </a><br><br>
                                ' . $po_attachment . '
                            </td>';
        $table_rows_html .= '<td>' . $podate . '</td>';
        $table_rows_html .= '<td>' . $dfowner . ' <br>
                                <a href="' . page_url . 'Task/viewdfmeetingmom/' . $rows->id . '">
                                    <span class="btn btn-success btn-xs">DF MEETING MOM</span>
                                </a>
                            </td>';
        $table_rows_html .= '<td>' . date('d-m-Y', strtotime($rows->added_on)) . '</td>';
        $table_rows_html .= '<td>' . $planned_display . '</td>';
        $table_rows_html .= '<td>' . $actual_display . '</td>';
        $table_rows_html .= '<td><strong>' . $total_days_to_completion . ' DAYS</strong></td>';
        $table_rows_html .= '<td>100%</td>';
        $table_rows_html .= '<td><strong style="color:red; font-weight:bold;">' . $df_delay_days . ' DAYS</strong></td>';
        $table_rows_html .= '<td>' . $loss_display . '</td>';
        $table_rows_html .= '<td>
                                <a href="' . page_url . 'Task/dfgantchartNew/' . $rows->id . '" target="_blank">
                                    <span class="btn btn-warning btn-xs">GANTT CHART</span>
                                </a>
                            </td>';
        $table_rows_html .= '</tr>';
        
        $m++;
    }
}
?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle; ?>Closed DF</title>
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <?PHP
        // This query is just for the color, it's fine
        $q_logo = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
        $LOGO = $q_logo->row();
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
    <header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
    </header>
    <div class="wrapper">
      <div class="container-fluid">

                <div class="row" style="margin-top:20px;">
                    <div class="col-lg-12">
                        <div class="card-box" style="padding: 15px 20px;">
                            <form method="post" action="<?php echo page_url; ?>Df_reports/closed_df_report" class="form-inline">
                                <div class="form-group m-r-10">
                                    <label for="financial_year" class="m-r-10">Financial Year:</label>
                                    <select name="financial_year" id="financial_year" class="form-control">
                                        <option value="">All Years</option>
                                        <?php 
                                        // Generate FY list
                                        $current_year = date('Y');
                                        for ($i = $current_year + 1; $i >= 2023; $i--) {
                                            $fy_text = ($i-1) . '-' . $i;
                                            $selected = ($selected_fy == $fy_text) ? 'selected' : '';
                                            echo "<option value='{$fy_text}' {$selected}>{$fy_text}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search"></i> Filter</button>
                                <a href="<?php echo page_url; ?>Df_reports/closed_df_report" class="btn btn-default waves-effect waves-light m-l-5"><i class="fa fa-refresh"></i> Reset</a>
                            </form>
                        </div>
                    </div>
                </div>

        <div class="row">
          <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
            <div class="page-title-box">
               <div class="btn-group pull-right">
                                <div style="text-align: right; border: 2px solid #a94442; padding: 5px 10px; border-radius: 8px;">
                                    <h5 style="margin: 0; color: #555;">Consolidated Loss:</h5>
                                    <h3 style="margin: 0; color: #a94442; font-weight: 700;">
                                        <i class="fa fa-inr"></i> <?php echo $CIA->Task_model->formatIndianCurrency($grand_total_loss); ?>
                                    </h3>
                                </div>
                            </div>
              <h4 class="text-center" style="padding:10px; 10px; 10px; 10px;">Closed DF</h4><hr>
            </div>
          </div>
        </div>
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
       
                <div class="row card-box">
                    <div class="table-responsive">
                        <table id="example5" class="table manglesh table-bordered">
                          <thead>
                          <tr>
                            <th>S. NO.</th>
                            <th>DF No.</th>
                            <th>DOWNLOAD</th>
                            <th>PO DATE</th>
                            <th>MARKETING PERSON</th>
                            <th>DF RELEASE DATE</th>
                            <th>SYSTEM PLANNED DATE OF CLOSER</th>
                            <th>ACTUAL COMPLETION DATE</th>
                            <th>TOTAL DAYS TO COMPLETION</th>
                            <th>DF STATUS</th>
                            <th>DF DELAYED</th>
                            <th>LOSS AGAINST DF</th>
                            <th>VIEW GANTT CHART</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                            // --- This is where the pre-built HTML rows are printed ---
                            echo $table_rows_html;
                        ?>
                        </tbody>
                        </table>
                    </div>
                </div>

        <?php $this->load->view('common/footer');?>
        </div> </div>
    
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

    <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script>
$( document ).ready(function() {
    $('#example5').DataTable({ // Use capital "D"
        "bProcessing": true,
        fixedHeader: true,
        "pagination":true,
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Closed DF Export'
            }
        ]
    }); 
});
</script>
</body>
</html>