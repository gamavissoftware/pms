        <?php
        $CI = &get_instance();
        $CI->load->model('Task_model');
        $df_id = $this->uri->segment(3);
        $department = $this->uri->segment(4);
        $date_range = $CI->Task_model->getmaintaskenddate($df_id);

        $tasks = $CI->Task_model->getDFTaskScheduled($df_id, $department);
        //echo "<pre>"; print_r($tasks); exit;
        if (count($date_range) > 0) {
            if($CI->Task_model->checkdfclosed($df_id)>0)
            {
                $etdate=date('Y-m-d');
            }else
            {
                $etdate=$date_range[1];
            }
            $stdate = $date_range[0];
            //$etdate = $date_range[1];
            $Dates = $CI->Task_model->getMondays($stdate, $etdate);
            $days_count = $CI->Task_model->getDaysCountForEachMonthFromArray($Dates);
        } else {
            echo "Date Not Found";
            exit;
        }

        $df_data = $CI->Task_model->getallrunningdfByID($df_id);
        $department_name = $CI->Task_model->textFormatting($CI->Task_model->getDepartmentBYID($department));
        // $work_done_per = $CI->Task_model->workCompleted($df_id, $department);
        // $work_delayed_per = $CI->Task_model->workDelayed($df_id, $department);
        $planned_end_date = $CI->Task_model->getPlannedEndDate($df_id, $department);
        $estimated_end_date = $CI->Task_model->GetEstimatedWithDelay($df_id, $department);
        $ActualDelay = $CI->Task_model->getActualDelay($df_id, $department);

        if (count($df_data) > 0) {
            $runningdfno = strtoupper($df_data[1]);
            $companyname = $CI->Task_model->textFormatting($df_data[3]);
            $pono = $CI->Task_model->textFormatting($df_data[4]);
            $podate = $CI->Task_model->textFormatting($df_data[5]);
            $df_addedOn = date('d-m-Y', strtotime($df_data[7]));
            $marketing = $CI->Task_model->textFormatting($df_data[8]);
        }
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <meta name="author" content="GAMAVIS SOFTECH PRIVATE LIMITED">
            <title>DF WISE GANT CHART - SHUBHAM PACK</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js" integrity="sha512-7rusk8kGPFynZWu26OKbTeI+QPoYchtxsmPeBqkHIEXJxeun4yJ4ISYe7C6sz9wdxeE1Gk3VxsIWgCZTc+vX3g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <style>
                .holiday {
                    background-color: #ffc0cb !important;
                }

                .completed {
                    background-color: #00FF00;
                }

                .completed1 {
                    background-color: #73d673 !important;
                    background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
                    background-size: 23px 23px;
                }

                .earlycomplete {
                    background-color: #a2c1e0;
                    opacity: 0.50 !important;
                }

                .earlycomplete1 {
                    background-color: #a2c1e0 !important;
                    background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
                    background-size: 23px 23px;
                }

                .org {
                    background-color: #B7833A;
                }

                .act {
                    background-color: #70596ed1;
                    background-image: repeating-linear-gradient(135deg, #70596E, #70596E 0.5px, transparent 0.5px, transparent 16px);
                    background-size: 23px 23px;
                }

                .org1 {
                    background-color: #b7833a9c;
                    background-image: repeating-linear-gradient(135deg, #B7833A, #B7833A 0.5px, transparent 0.5px, transparent 16px);
                    background-size: 23px 23px;
                }

                .beyondcomplete {
                    background-color: #FAA0A0;
                }

                .beyondcomplete1 {
                    background-color: #f29b9b !important;
                    background-size: 23px 23px;
                }

                .progress {
                    margin: 0;
                    height: 45px;
                    background-color: #ebeff2;
                    border-radius: 20px;
                }

                .progress1 {}

                h1 {
                    position: relative;
                    color: #000;
                    display: inline-block;
                    margin: 10px 0;
                    text-shadow: 0 0 2px black;
                    text-align: center;
                    font-weight: 600;
                }

                h1:before {
                    content: "";
                    position: absolute;
                    background: #ffd69d;
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: -1;
                }

                h3,
                h4 {
                    position: relative;
                    color: #000;
                    display: inline-block;
                    margin: auto;
                    text-shadow: 0 0 2px black;
                    text-align: center !important;
                    font-weight: 300;
                }

                .table-scroll {
                    position: relative;
                    width: 100%;
                    z-index: 1;
                    margin: auto;
                    overflow: auto;
                    height: 350px;
                }

                .table-scroll table {
                    width: 100%;
                    margin: auto;
                    border-collapse: separate;
                    border-spacing: 0;
                }

                .table-wrap {
                    position: relative;
                }

                .table-scroll th,
                .table-scroll td {
                    font-size: 10px;
                    text-align: center;
                }

                .table-scroll thead th {
                    position: -webkit-sticky;
                    position: sticky;
                    top: 0;
                    text-align: center;
                    font-size: 10px;
                    box-shadow: rgb(204 219 232 / 0%) 3px 3px 6px 0px inset, rgb(255 255 255) -3px -3px 6px 1px inset;
                }

                .table-scroll th,
                thead {
                    word-wrap: break-word !important;
                }

                .table-scroll tfoot,
                .table-scroll tfoot th,
                .table-scroll tfoot td {
                    position: -webkit-sticky;
                    position: sticky;
                    bottom: 0;
                    z-index: 4;
                }

                th:nth-child(1) {
                    position: -webkit-sticky;
                    position: sticky;
                    left: 0;
                    z-index: 2;
                }

                thead th:nth-child(1),
                tfoot th:first-child {
                    z-index: 5;
                }

                .packing {
                    height: 700px;
                    overflow-y: auto;
                    margin-top: 25px;
                    border: 1px solid black;
                    border-radius: 3px;
                }

                .right div {
                    writing-mode: vertical-rl;
                    font-size: 12px;
                    font-weight: 600;
                }

                .light_grey {
                    background-color: #efefef;
                    vertical-align: top;
                    transform: rotate(180deg);
                }

                .nxt {
                    vertical-align: top;
                    transform: rotate(180deg);
                }

                .lighter_grey {
                    background-color: #dddddd;
                }

                .light_blue {
                    background-color: #c5dcf4;
                }

                .light_red {
                    background-color: #eed2db;
                }

                .light_green {
                    background-color: #01fe4b;
                }

                .lighter_green {
                    background-color: #01fe4b;
                    box-shadow: inset 0 0 15px 0 #c7c7c7;
                }

                .red {
                    background-color: red;
                    color: white;
                    text-align: center;
                    height: 20px;
                }

.red svg{
    width: 10px;
    fill: #fff;
}

.red i{
    font-size: 14px;
    color: #fff;
}

                .yellow {
                    background-color: yellow;
                    color: black;
                    text-align: center;

                    height: 20px;
                }

                .green {
                    background-color: green;
                    color: white;
                    text-align: center;
                    height: 20px;
                }

                .green svg{
                    width: 10px;
                    fill: #fff;
                }

                .black {
                    background-color: black;
                    color: white;
                    text-align: center;
                }

                .orange {
                    background-color: orange;
                    color: black;
                    text-align: center;
                }

                .blue {
                    background-color: #afcbe3;
                    color: white;
                    text-align: center;
                    height: 20px;
                }

                .blue svg{
                    width: 10px;
                    fill: #fff;
                }

                .font_size {
                    font-size: 14px !important;
                }

                .na {
                    color: red;
                }

                .supplier_planned {
                    background-color: #ebeff2;
                    border-color: #ebeff2;
                    border: none;
                }

                .supplier_not_started {
                    border: none;
                    background-color: #DAF7A6;
                }

                .supplier_neutral {
                    background-color: #fff;
                    border-color: #000;
                }

                .tooltip-container {
                    position: relative;
                    display: inline-block;
                    cursor: pointer;
                    padding: 1px;
                }

                .tooltip {
                    z-index: 9999;
                    visibility: hidden;
                    width: 313px;
                    max-width: 400px;
                    background-color: #fff;
                    border: 1px solid #ccc;
                    padding: 10px;
                    position: absolute;
                    z-index: 1001;
                    bottom: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    transition: opacity 0.3s;
                    opacity: 0;
                    overflow-x: auto;
                }

                .tooltip-container:hover .tooltip {
                    visibility: visible;
                    opacity: 1;
                }

                .tooltip table {
                    border-collapse: collapse;
                    width: 100%;
                }

                .tooltip th,
                .tooltip td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }

                .tooltip th {
                    background-color: #f2f2f2;
                }

                .modal-table {
                    width: 100%;
                    margin-bottom: 1rem;
                    color: #212529;
                    border-collapse: collapse;
                }

                .modal-table th,
                .modal-table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid #dee2e6;
                }

                .modal-table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid #dee2e6;
                }

                .modal-table tbody+tbody {
                    border-top: 2px solid #dee2e6;
                }

                .modal-table th {
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                }

                .modal-table td {
                    border: 1px solid #dee2e6;
                }

                .modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .modal-title {
                    margin: 0;
                }
            </style>
        </head>

        <body style="text-transform: uppercase;">
            <div class="wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12" style="margin-top:20px;">
                            <select id="switch" onchange="switchData();">
                                <option value="2">Day Wise</option>
                                <option value="1">Week Wise</option>
                                <option value="3">Department Wise</option>
                                <option value="4" selected>Department & Week Wise</option>
                            </select>

                        </div>
                        <div class="col-sm-12">
                            <div class="text-center">
                                <h1>Progress Gant Chart</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:20px;">
                        <div class="col">
                            <table border="1" class="table table-bordered" style="padding:10px;background-color: #efefef;">
                                <tr style="font-size:12px;">
                                    <th>DF NO<br /><span style="color:brown;"><?php echo $runningdfno; ?><span></th>
                                    <th>Company Name<br /><span style="color:brown;"><?php echo $companyname; ?><span></th>
                                    <th>DF Release Date<br /><span style="color:brown;"><?php echo $df_addedOn; ?></span></th>
                                    <th>Marketing Person<br /><span style="color:brown;"><?php echo $marketing; ?></span></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 card-box">
                        <div class="packing table-scroll">
                            <table id="main-table" class="main-table left" border="1">
                                <thead>
                                    <tr>
                                        <th style="background-color:white;padding: 0; vertical-align: bottom;" rowspan="3">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td style="text-align: center; border-bottom: 1px solid lightgray; font-size: 20px;">
                                                            Indicators
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="blue" width="23%"></td>
                                                        <td class="" width="10%">Planned</td>
                                                        <!-- <td class="white" width="15%"></td>
        <td class="" width="10%">Late Start</td> -->
                                                        <td class="green" width="23%"></td>
                                                        <td class="" width="10%">Actual</td>
                                                        <td class="red" width="23%"></td>
                                                        <td class="" width="10%">Delay</td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table style="width: 100%;" border="1">
                                                <tbody>
                                                    <tr>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 15px;">#</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 70px;">Department</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 170px;">Task</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 25px;">PLN <br>ACT</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 100px;">Start Date</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width: 100px;">End Date</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width:50px;">Delay<br>(in Days)</div>
                                                        </th>
                                                        <th scope="col" class="lighter_grey">
                                                            <div style="width:50px;">% Work<br> Completed</div>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php
                                            if (count($days_count) > 0) {
                                                // echo "<pre>"; print_r($days_count); exit;
                                                foreach ($days_count as $month => $days) {
                                            ?>
                                        <th class="text-center lighter_grey" colspan="<?php echo $days; ?>"><?php echo $month; ?></th>
                                <?php }
                                            } ?>
                                </th>
                                    </tr>
                                    <?php
                                    if (count($Dates) > 0) {
                                        foreach ($Dates as $date) {
                                            $week = $CI->Task_model->getWeekNumberFromDate($date);
                                    ?>
                                            <th class="text-center lighter_grey" style="z-index: 4"><?php echo $week; ?></th>
                                    <?php }
                                    } ?>
                                    </tr>
                                    <?php
                                    if (count($Dates) > 0) {
                                        foreach ($Dates as $date) { ?>
                                            <td scope="col" class="right light_grey ">
                                                <div style="width: 30px;"><?php echo date('d M Y', strtotime($date)); ?></div>
                                            </td>
                                    <?php }
                                    } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $alreadythere = array();
                                    $i = 1;
                                    $ty = 0;
                                    $q = $this->db->select('a.id, a.department_id, a.taskname, a.main_task_id, b.department')->from('mdgantchartmaster a')
                                        ->join('departments b', 'a.department_id=b.department_id', 'left')
                                        //->where('a.id',4)
                                        ->order_by('a.sortorder', 'asc')
                                        //->group_by('b.department_id')
                                        ->get();
                                    //echo "<pre>"; print_r($q->result()); exit;
                                    foreach ($q->result() as $row) {

                                        $tasksid = array();
                                        $css_occur = array();

                                    ?>
                                        <tr>
                                            <th style="background-color: white; padding: 0px; border: none;" rowspan="2">
                                                <table border="1">
                                                    <tbody>

                                                        <tr>
                                                            <td rowspan="2" style="height: 40px;">
                                                                <div style="width: 15px;"><?php echo $i; ?></div>
                                                            </td>

                                                            <td rowspan="2" style="text-align: left;">
                                                                <div style="width: 70px; text-align: left;"><?php echo ucfirst(strtolower($row->department)); ?> <?php //echo $row->id;
                                                                                                                                                                    ?></div>
                                                            </td>
                                                            <td rowspan="2" style="height: 40px;">
                                                                <div style="width: 170px; text-align: left !important;">
                                                                    <?php echo ucwords(strtolower($row->taskname)); ?>
                                                                    <div class="tooltip-container">
                                                                        <?php $dfidd = $this->uri->segment(3); ?>
                                                                        <span style="font-size:10px" onclick="open_popup('<?php echo $dfidd; ?>','<?php echo $row->id; ?>','<?php echo ucwords(strtolower($row->department)); ?>');"><i class="fa fa-cog fa-spin"></i></span> &nbsp; <span style="font-size:10px" onclick="open_ticket_popup('<?php echo $dfidd; ?>','<?php echo ucwords(strtolower($row->department)); ?>');"><i class="fa fa-ticket"></i></span>
                                                                        <!-- <div class="tooltip">
        <ul> -->
                                                                        <?php
                                                                        // $q = $this->db->select('b.task_name')->from('sharmajitaskmapping a')->join('task_management b','a.task_id=b.task_id','left')->where('a.report_id',$row->id)->get();
                                                                        // foreach($q->result() as $recod){
                                                                        ?>
                                                                        <!-- <li><?php //echo ucwords(strtolower($recod->task_name)); 
                                                                                    ?></li> -->
                                                                        <?php //} 
                                                                        ?>
                                                                        <!--  </ul>
        </div> -->

                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="">
                                                                <div style="width: 25px;text-align: LEFT;font-size: 10px;">PLN</div>
                                                            </td>
                                                            <?php
                                                            $q2 = $this->db->select('id, task_id')->from('sharmajitaskmapping')->where('report_id', $row->id)->get();
                                                            if ($q2->num_rows() > 0) {
                                                                foreach ($q2->result() as $row1) {
                                                                    $tasksid[] = $row1->task_id;
                                                                }

                                                                $q3 = $this->db->select('MIN(start_date) as min_start_date, MAX(end_date) as max_end_date')
                                                                    ->from('task_department_wise_scheduling')->where_in('taskid', $tasksid, false)->where('df_id', $this->uri->segment(3))->where('department_id', $row->department_id)->get();

                                                                if ($q3->num_rows() > 0) {
                                                                    $row3 = $q3->row();
                                                                    $min_start_date = $row3->min_start_date;
                                                                    $max_end_date = $row3->max_end_date;
                                                                    //echo $min_start_date."<br/>".$max_end_date; exit;
                                                                    $q4 = $this->db->select('end_date')
                                                                        ->from('task_department_wise_scheduling')->where('taskid', $row->main_task_id)->where('df_id', $this->uri->segment(3))
                                                                        ->where('department_id', $row->department_id)->get();

                                                                    if ($q4->num_rows() > 0) {
                                                                        $main_task_end_date = $q4->row()->end_date;
                                                                        $edt = ($main_task_end_date == $max_end_date) ? $main_task_end_date : $max_end_date;

                                                                        $q5 = $this->db->select('task_completed_on')
                                                                            ->from('task_department_wise_scheduling')
                                                                            ->where('taskid', $row->main_task_id)
                                                                            ->where('df_id', $this->uri->segment(3))
                                                                            ->get();

                                                                        if ($q5->num_rows() > 0) {
                                                                            $task_completed_on = $q5->row()->task_completed_on;
                                                                            //echo $task_completed_on; exit;
                                                                            if ($task_completed_on <> '0000-00-00 00:00:00') {
                                                                                $completed_date = new DateTime($task_completed_on);
                                                                                $max_end_date_obj = new DateTime($max_end_date);
                                                                                $delaydays = ($completed_date > $max_end_date_obj) ? $completed_date->diff($max_end_date_obj)->days . " Days" : "";
                                                                            } else {
                                                                                $completed_date = new DateTime(date('Y-m-d'));
                                                                                $max_end_date_obj = new DateTime($max_end_date);
                                                                                $delaydays = ($completed_date > $max_end_date_obj) ? $completed_date->diff($max_end_date_obj)->days . " Days" : "";
                                                                            }
                                                                        } else {
                                                                            $today = new DateTime();
                                                                            $max_end_date_obj = new DateTime($max_end_date);
                                                                            $delaydays = ($today > $max_end_date_obj) ? $today->diff($max_end_date_obj)->days . " Days (Pending)" : "Pending";
                                                                        }
                                                            ?>
                                                                        <td>
                                                                            <div style="width: 100px;"><?php echo date('d-M-Y', strtotime($min_start_date)); ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <div style="width: 100px;"><?php echo date('d-M-Y', strtotime($edt)); ?></div>
                                                                        </td>
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <td>
                                                                            <div style="width: 100px;">No end date found</div>
                                                                        </td>
                                                                        <td>
                                                                            <div style="width: 100px;">No end date found</div>
                                                                        </td>
                                                                    <?php
                                                                    }
                                                                } else {
                                                                    ?>
                                                                    <td>
                                                                        <div style="width: 100px;">No dates found</div>
                                                                    </td>
                                                                    <td>
                                                                        <div style="width: 100px;">No dates found</div>
                                                                    </td>
                                                                <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <td>
                                                                    <div style="width: 100px;">No tasks found</div>
                                                                </td>
                                                                <td>
                                                                    <div style="width: 100px;">No tasks found</div>
                                                                </td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td rowspan="2">
                                                                <div style="width:50px;"><?php echo $delaydays; ?></div>
                                                            </td>
                                                            <td rowspan="2">
                                                                <div style="width: 50px;">
                                                                    <?php
                                                                    $department_id = $row->department_id;
                                                                    $df_id = $this->uri->segment(3);
                                                                    $percentage_work_done = $CI->Task_model->delaypercentagecount($row->id, $df_id, $tasksid);
                                                                    echo number_format($percentage_work_done, 2) . "%<br>";
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="" style="text-align: LEFT;font-size: 10px;">ACT</td>
                                                            <td colspan="2">
                                                                <?php
                                                                if (number_format($percentage_work_done, 2) == '100.00') {
                                                                    $q = $this->db->select('task_completed_on')->from('task_department_wise_scheduling')->where('taskid', $row->main_task_id)->where('df_id', $this->uri->segment(3))->where('task_status', 1)->get();
                                                                    if ($q->num_rows() > 0) {
                                                                        foreach ($q->result() as $complete);
                                                                        echo date('d-M-Y', strtotime($complete->task_completed_on));
                                                                    }
                                                                }
                                                                ?>

                                                                <?php //echo $row->main_task_id; ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </th>
                                            <?php
                                            if (count($Dates) > 0) {
                                                foreach ($Dates as $date) {
                                                    // echo $min_start_date."<br/>".$edt; exit;
                                                    $plannedWeeks = $CI->Task_model->get_week_numbers_between_datesWithYearNew($min_start_date, $edt);
                                                    //echo "<pre>"; print_r($plannedWeeks); exit;
                                                    $currentDateweek = $CI->Task_model->getWeekNumberFromDate($date);

                                                    $currentDateweek = $this->task->getYearForWeek($date) . $currentDateweek;

                                                    if (in_array($currentDateweek, $plannedWeeks)) {
                                                        $css = "blue";
                                                    } else {
                                                        $css = "";
                                                    }
                                                    //$currentDateweek = $CI->Task_model->getWeekNumberFromDate($date);
                                            ?>
                                                    <td class="<?php echo $css; ?>" style="color:black;"><?php //echo $currentDateweek;
                                                                                                            ?></td>
                                            <?php }
                                            } ?>
                                        </tr>




                                        <?php
                                        if (count($Dates) > 0) {
                                            foreach ($Dates as $date) {
                                                $css = "white";
                                                $CurrentWeekNo = $CI->Task_model->getWeekNumberFromDateWithYear($date);
                                                $tdetail = $CI->Task_model->getDoneTaskMaxStart_EndDate($tasksid, $df_id);

                                                if (count($tdetail) > 0) {
                                                    $min_start = $tdetail[0];
                                                    $max_end = $tdetail[1];
                                                    //echo $min_start."<br/>".$max_end; exit;
                                                    $ActualWeeks = $CI->Task_model->get_week_numbers_between_datesWithYearNew($min_start, $max_end);
                                                    $currentDateweek = $CI->Task_model->getWeekNumberFromDateWithYear($date);

                                                    if (in_array($currentDateweek, $ActualWeeks)) {
                                                        //$css="green";
                                                    }

                                                    /** CHECK IF WORK IS STARTED LATE AND MARK YELLOW **/
                                                    $mystartweek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start);
                                                    $systemstartweek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);

                                                    if ($mystartweek > $systemstartweek) {
                                                        $extraweektostart = range($systemstartweek, $mystartweek);

                                                        if (in_array($currentDateweek, $extraweektostart) && $currentDateweek <> $mystartweek) {
                                                            $css = "white";
                                                        }
                                                    }
                                                    /** END **/


                                                    $alldone = $CI->Task_model->checkifalltasksaredone($tasksid, $df_id);
                                                    $CurrentWeekNo = $CI->Task_model->getWeekNumberFromDateWithYear($date);

                                                    // echo $alldone; exit;
                                                    if ($alldone == 1) {
                                                        $completedDate = date('Y-m-d', strtotime($CI->Task_model->getTaskCompletedDate($row->main_task_id, $df_id)));
                                                        // echo $completedDate; exit;
                                                        $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                                        $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);

                                                        $ActualendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($completedDate);

                                                        //echo $plannedendWeek."<br/>".$ActualendWeek; exit;
                                                        if ($ActualendWeek > $plannedendWeek) {

                                                            $weekinbetween = range($plannedendWeek, $ActualendWeek);
                                                            //echo "<pre>"; print_r($weekinbetween); exit;
                                                            //echo $CurrentWeekNo; exit;
                                                            if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo <> $plannedendWeek) {
                                                                $css = "red";
                                                            }




                                                            if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                                $css = "green";
                                                            }
                                                            //echo "hi"; exit; 

                                                        } else if ($ActualendWeek == $plannedendWeek) {

                                                            if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                                $css = "green";
                                                            }
                                                        } else {

                                                            $plstartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                                            $plendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);
                                                            //echo $plstartWeek."<br/>".$plendWeek; exit;
                                                            if ($CurrentWeekNo >= $plstartWeek && $CurrentWeekNo <= $plendWeek) {
                                                                $css = "green";
                                                            }

                                                            if (in_array($CurrentWeekNo, $ActualWeeks) && $CurrentWeekNo > $plendWeek) {
                                                                $css = "";
                                                            }
                                                        }

                                                        if ($css == "green") {
                                                            if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                                //$css="";
                                                            } else {
                                                                $css = "";
                                                            }
                                                        }
                                                    } else {



                                                        $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                                        $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);
                                                        // echo $plannedendWeek; exit;
                                                        $todayweekno = $CI->Task_model->getWeekNumberFromDateWithYear(date('Y-m-d'));

                                                        if ($todayweekno > $plannedendWeek) {
                                                            $completeendweek = $plannedendWeek;
                                                            $weekinbetween = range($completeendweek, $todayweekno);
                                                            //echo "<pre>"; print_r($weekinbetween); exit;
                                                            if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo > $plannedendWeek) {
                                                                $css = "red";
                                                            } else {
                                                                if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                                    $css = "green";
                                                                }
                                                            }
                                                        } else {
                                                            if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $todayweekno) {
                                                                $css = "green";
                                                            }
                                                        }



                                                        // task not completed


                                                    }
                                                } else {
                                                    // echo "hi"; exit;
                                                    // task not completed not started but delayed from todays date
                                                    //echo "hi"; exit;
                                                    $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                                    //echo $edt; exit;
                                                    $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);

                                                    $todayweekno = $CI->Task_model->getWeekNumberFromDateWithYear(date('Y-m-d'));
                                                    //echo $todayweekno."<br/>".$plannedendWeek; exit;

                                                    if ($todayweekno > $plannedendWeek) {

                                                        $completeendweek = $plannedendWeek;
                                                        $weekinbetween = range($completeendweek, $todayweekno);
                                                        //echo "<pre>"; print_r($weekinbetween); exit;
                                                        if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo > $plannedendWeek) {
                                                            $css = "red";
                                                        } else {
                                                            if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                                $css = "green";
                                                            }
                                                        }
                                                    } else {
                                                        //echo "hi"; exit;
                                                        $CurrentWeekNo = $CI->Task_model->getWeekNumberFromDateWithYear($date);
                                                        //echo $CurrentWeekNo; exit;
                                                        // if($CurrentWeekNo==202438)
                                                        // {
                                                        //echo intval($CurrentWeekNo)."|".intval($plannedStartWeek)."|".intval($todayweekno); exit;
                                                        // }
                                                        if (intval($CurrentWeekNo) >= intval($plannedStartWeek) && intval($CurrentWeekNo) <= intval($todayweekno)) {
                                                            //$css = "green";
                                                            $css = "green";
                                                            $a = 1;
                                                        } else {
                                                            $css = "";
                                                            $a = 0;
                                                        }
                                                    }
                                                }

                                                // echo $css."|".$a; exit;

                                                if ($css == "red") {
                                                    // $icon='<span style="color:white;font-weight:bold;" onclick="open_ticket_popup('.$dfidd.','.$row->department_id.');"><i class="fa fa-ticket"></i></span>';
                                                    $this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b', 'a.department_id=b.department_id')->join('task_management c', 'a.task_id=c.task_id')->join('system_users d', 'a.added_by=d.user_id', 'left')->join('system_users e', 'a.user_id=e.user_id')->join('df_release f', 'a.df_id=f.id', 'left');
                                                    if ($dfidd <> 'ALL') {
                                                        $this->db->where('a.df_id', $dfidd);
                                                    }
                                                    if ($row->department_id <> '') {
                                                        $this->db->where('a.department_id', $row->department_id);
                                                    }

                                                    $rest = $this->db->get();

                                                    if ($rest->num_rows() > 0) {
                                                        if (!in_array('red', $css_occur)) {
                                                            $icon = '<a href="javascript:;" onclick="open_ticket_popup(' . $dfidd . ',' . $row->department_id . ');"><i class="fa fa-ticket" aria-hidden="true"></i></a>';
                                                            $css_occur[] = "red";
                                                        } else {
                                                            $icon = '';
                                                        }
                                                    } else {
                                                        $icon = '';
                                                    }
                                                } else {
                                                    $icon = "";
                                                }
                                        ?>
                                                <td class="<?php echo $css; ?>" style="color:black;"><?php echo $icon; ?></td>
                                        <?php }
                                        } ?>
                                        </tr>
                                    <?php $alreadythere[] = $row->id;
                                        $i++;
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h3 id="exampleModalLabel text-center"><span id="stagrmk"></span></h3>
                        </div>
                        <div class="modal-body" id="modalbody"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modal -->
            <div class="modal fade" id="exampleModalTicket" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h3 id="exampleModalLabel text-center"><span id="stagrmkTicket">Department Raised Tickets</span></h3>
                        </div>
                        <div class="modal-body" id="modalbodyTicket" style="overflow-x:scroll;"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>



            <script type="text/javascript">
                function open_popup(dfid, taskid, departmentid) {
                    $("#exampleModal").modal('show');
                    var departmentname = departmentid + ' Progress Report';
                    $.ajax({
                        type: "post",
                        url: "<?php echo page_url; ?>Task/getreportoftasks/",
                        data: {
                            dfid: dfid,
                            taskid: taskid,
                            departmentid: departmentid
                        },
                        success: function(data) {
                            backdrop: false,
                            $("#modalbody").html(data);
                            $("#stagrmk").html(departmentname);
                        }
                    });
                }


                function open_ticket_popup(dfid, departmentid) {
                    $("#exampleModalTicket").modal('show');
                    var departmentname = departmentid + ' Raised Tickets';
                    $.ajax({
                        type: "post",
                        url: "<?php echo page_url; ?>Task/getTicktsDepartmentWise/",
                        data: {
                            dfid: dfid,
                            departmentid: departmentid
                        },
                        success: function(data) {
                            backdrop: false,
                            $("#modalbodyTicket").html(data);
                            ///$("#stagrmkTicket").html(departmentname);
                        }
                    });
                }



                function filter_Gant() {
                    var filter = $('#filter').val();
                    document.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartSharmaji/<?php echo $this->uri->segment(3); ?>/" + filter;
                }

                function switchData() {
                    var swi = $("#switch").val();
                    if (swi == 1) {
                        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartSharmaji/<?php echo $this->uri->segment(3); ?>/";
                    } else if (swi == 2) {
                        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartNew/<?php echo $this->uri->segment(3); ?>/";
                    } else if (swi == 3) {
                        window.location = "https://pms.shubhampack.in/index.php/Task/dfgantchartDepartmentwise/<?php echo $this->uri->segment(3); ?>";
                    } else {
                        window.location = "https://pms.shubhampack.in/index.php/Task/finalgantchartwithdetails/<?php echo $this->uri->segment(3); ?>";
                    }
                }
            </script>
        </body>

        </html>