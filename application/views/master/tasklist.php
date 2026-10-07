<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Task Management</title>
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
            body.task-management-page {
                background: #f4f7fb;
                color: #1f2937;
            }
            .task-management-page .wrapper {
                padding-top: 22px;
            }
            .task-page-header {
                align-items: center;
                background: #ffffff;
                border: 1px solid #e5ebf2;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
                display: flex;
                gap: 18px;
                justify-content: space-between;
                margin-bottom: 16px;
                padding: 16px 18px;
            }
            .task-page-title {
                margin: 0;
            }
            .task-page-title h4 {
                color: #0f172a;
                font-size: 22px;
                font-weight: 700;
                line-height: 1.2;
                margin: 0 0 5px;
            }
            .task-page-title span {
                color: #64748b;
                display: block;
                font-size: 13px;
                font-weight: 600;
            }
            .task-page-actions {
                align-items: flex-end;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
            }
            .task-page-actions .form-group {
                margin-bottom: 0;
                min-width: 230px;
            }
            .task-page-actions label {
                color: #64748b;
                display: block;
                font-size: 11px;
                font-weight: 700;
                margin-bottom: 4px;
                text-transform: uppercase;
            }
            .task-page-actions .form-control {
                border-color: #d8e0ea;
                border-radius: 6px;
                box-shadow: none;
                height: 36px;
            }
            .task-page-actions .btn {
                border-radius: 6px;
                font-weight: 700;
                height: 36px;
                line-height: 22px;
            }
            .task-table-card {
                background: #ffffff;
                border: 1px solid #e5ebf2;
                border-radius: 8px;
                box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
                overflow-x: auto;
                padding: 0;
            }
            .task-table-card .dataTables_wrapper {
                padding: 14px;
            }
            .task-table-card .dataTables_length,
            .task-table-card .dt-buttons,
            .task-table-card .dataTables_filter {
                margin-bottom: 12px;
            }
            .task-table-card .dataTables_length label,
            .task-table-card .dataTables_filter label {
                color: #475569;
                font-weight: 600;
            }
            .task-table-card .dataTables_filter input,
            .task-table-card .dataTables_length select {
                border: 1px solid #d8e0ea;
                border-radius: 6px;
                box-shadow: none;
                margin-left: 6px;
                min-height: 34px;
                padding: 6px 10px;
            }
            .task-table-card .dt-buttons .btn {
                background: #0f766e;
                border: 0;
                border-radius: 6px;
                color: #ffffff;
                font-weight: 700;
                padding: 7px 14px;
            }
            table.manglesh {
                border: 0;
                min-width: 1520px;
                table-layout: fixed;
                width: 100% !important;
            }
            table.manglesh thead th {
                background: #0f6c94;
                border-color: #0d5e80 !important;
                color: #ffffff;
                font-size: 12px;
                font-weight: 700;
                padding: 11px 10px !important;
                text-align: center;
                vertical-align: middle;
                white-space: nowrap;
            }
            table.manglesh tbody td {
                border-color: #e8eef5 !important;
                color: #243244;
                font-size: 13px;
                line-height: 1.35;
                padding: 10px 10px !important;
                text-align: center;
                vertical-align: middle !important;
                word-break: normal;
            }
            table.manglesh tbody tr:nth-child(odd) td {
                background: #fbfdff;
            }
            table.manglesh tbody tr:hover td {
                background: #eef8fb;
            }
            table.manglesh th:nth-child(1),
            table.manglesh td:nth-child(1) {
                width: 76px;
            }
            table.manglesh th:nth-child(2),
            table.manglesh td:nth-child(2) {
                width: 130px;
            }
            table.manglesh th:nth-child(3),
            table.manglesh td:nth-child(3) {
                width: 210px;
            }
            table.manglesh th:nth-child(4),
            table.manglesh td:nth-child(4) {
                width: 340px;
            }
            table.manglesh th:nth-child(5),
            table.manglesh td:nth-child(5) {
                width: 70px;
            }
            table.manglesh th:nth-child(6),
            table.manglesh td:nth-child(6) {
                width: 190px;
            }
            table.manglesh th:nth-child(7),
            table.manglesh td:nth-child(7) {
                width: 88px;
            }
            table.manglesh th:nth-child(8),
            table.manglesh td:nth-child(8) {
                width: 300px;
            }
            table.manglesh th:nth-child(9),
            table.manglesh td:nth-child(9) {
                width: 170px;
            }
            table.manglesh th:nth-child(10),
            table.manglesh td:nth-child(10),
            table.manglesh th:nth-child(11),
            table.manglesh td:nth-child(11) {
                width: 110px;
            }
            .task-index {
                align-items: center;
                display: flex;
                gap: 7px;
                justify-content: center;
            }
            .task-index-number {
                background: #e8f2f7;
                border: 1px solid #d4e6ef;
                border-radius: 999px;
                color: #0f5f7f;
                display: inline-block;
                font-weight: 800;
                min-width: 34px;
                padding: 5px 8px;
            }
            .task-edit-btn {
                align-items: center;
                background: #ffffff;
                border: 1px solid #d8e0ea;
                border-radius: 6px;
                color: #0f6c94;
                display: inline-flex;
                height: 30px;
                justify-content: center;
                width: 30px;
            }
            .task-edit-btn:hover,
            .task-edit-btn:focus {
                background: #0f6c94;
                border-color: #0f6c94;
                color: #ffffff;
            }
            .task-department,
            .task-person,
            .task-name-cell {
                display: block;
                font-weight: 700;
            }
            .task-name-cell {
                color: #111827;
            }
            .task-department {
                color: #334155;
            }
            .task-person {
                color: #475569;
            }
            .task-tat {
                background: #fff7ed;
                border: 1px solid #fed7aa;
                border-radius: 999px;
                color: #9a3412;
                display: inline-block;
                font-weight: 800;
                min-width: 34px;
                padding: 5px 8px;
            }
            .task-start-from strong,
            .task-start-from span {
                display: block;
            }
            .task-start-from strong {
                color: #1f2937;
                font-weight: 700;
            }
            .task-start-from span {
                color: #64748b;
                font-size: 12px;
                margin-top: 2px;
            }
            .task-hierarchy-box {
                align-items: center;
                display: grid;
                gap: 7px;
                grid-template-columns: 112px minmax(0, 1fr);
                min-width: 0;
            }
            .task-hierarchy-box select {
                border: 1px solid #d8e0ea;
                border-radius: 6px;
                box-shadow: none;
                color: #334155;
                font-size: 13px;
                height: 34px;
                margin-bottom: 0;
                max-width: 100%;
                padding: 6px 8px;
            }
            .task-hierarchy-status {
                display: block;
                font-size: 12px;
                font-weight: 700;
                grid-column: 1 / -1;
                min-height: 16px;
                text-align: left;
            }
            .task-hierarchy-status.success {
                color: #16803c;
            }
            .task-hierarchy-status.error {
                color: #d14343;
            }
            .task-message-panel {
                text-align: left;
            }
            .task-message-scroll {
                border: 1px solid #e4ebf2;
                border-radius: 6px;
                max-height: 92px;
                overflow: auto;
            }
            .task-message-table {
                border-collapse: collapse;
                margin: 0;
                width: 100%;
            }
            .task-message-table th {
                background: #edf7fb;
                color: #0f5f7f;
                font-size: 11px;
                padding: 6px 7px;
                text-align: left;
                text-transform: uppercase;
            }
            .task-message-table td {
                background: #ffffff !important;
                border-top: 1px solid #edf2f7;
                color: #334155;
                font-size: 12px !important;
                padding: 7px !important;
                text-align: left !important;
                vertical-align: top !important;
            }
            .task-message-count {
                color: #64748b;
                display: block;
                font-size: 11px;
                font-weight: 700;
                margin-top: 5px;
                text-align: right;
                text-transform: uppercase;
            }
            .task-empty-message {
                color: #94a3b8 !important;
                font-style: italic;
                text-align: center !important;
            }
            .task-badge {
                border-radius: 999px;
                display: inline-block;
                font-size: 11px;
                font-weight: 800;
                padding: 5px 8px;
                text-transform: uppercase;
            }
            .task-badge-final {
                background: #eef2ff;
                border: 1px solid #c7d2fe;
                color: #3730a3;
            }
            .task-badge-visible {
                background: #ecfdf5;
                border: 1px solid #bbf7d0;
                color: #047857;
            }
	            .task-muted {
	                color: #94a3b8;
	                font-weight: 700;
	            }
	            .task-page-loader {
	                align-items: center;
	                background: rgba(248, 250, 252, 0.86);
	                display: none;
	                height: 100%;
	                justify-content: center;
	                left: 0;
	                position: fixed;
	                top: 0;
	                width: 100%;
	                z-index: 9999;
	            }
	            .task-loader-card {
	                align-items: center;
	                background: #ffffff;
	                border: 1px solid #e2e8f0;
	                border-radius: 8px;
	                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
	                display: flex;
	                gap: 13px;
	                min-width: 260px;
	                padding: 16px 18px;
	            }
	            .task-loader-spinner {
	                animation: taskSpin 0.8s linear infinite;
	                border: 3px solid #dbeafe;
	                border-radius: 50%;
	                border-top-color: #0f6c94;
	                height: 30px;
	                width: 30px;
	            }
	            .task-loader-text {
	                color: #0f172a;
	                font-weight: 800;
	            }
	            @keyframes taskSpin {
	                to { transform: rotate(360deg); }
	            }
	            @media (max-width: 991px) {
	                .task-page-header {
	                    align-items: stretch;
                    flex-direction: column;
                }
                .task-page-actions {
                    justify-content: flex-start;
                }
                .task-page-actions .form-group {
                    min-width: 100%;
                }
            }
		</style>

    </head>





	    <body class="task-management-page">
	    <div id="taskPageLoader" class="task-page-loader">
	        <div class="task-loader-card">
	            <div class="task-loader-spinner"></div>
	            <div class="task-loader-text" id="taskLoaderText">Loading task list...</div>
	        </div>
	    </div>
<!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <?php $this->load->view('common/info-section.php');?>
        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="task-page-header">
                            <div class="task-page-title">
                                <h4>Task Management</h4>
                                <span><?php 
                                    $q = $this->db->select('department')->from('departments')->where('department_id',$this->uri->segment(3))->get();
                                    if($q->num_rows()>0){
                                        foreach($q->result() as $row);
                                        echo "Department: ".ucwords(strtolower($row->department));
                                    }else{
                                        echo "All departments";
                                    }
                                ?></span>
                            </div>
                            <div class="task-page-actions">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control" id="pageSelect" name="pageSelect" onchange="redirectToPage();">
                                        <option value="<?php echo page_url;?>Task/taskmanagement">All Departments</option>
                                <?php 
                                    $q = $this->db->select('b.department_id, b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('b.status',1)->where('b.business_loc_id',2)->group_by('a.department_id')->get();
                                    foreach($q->result() as $row){
                                ?>
                                        <option value="<?php echo page_url;?>Task/taskmanagement/<?php echo $row->department_id;?>" <?php if((int)$this->uri->segment(3)===(int)$row->department_id){echo "selected";}?>><?php echo ucwords(strtolower($row->department));?></option>
                            <?php }?>
                                    </select>
                                </div>
                                <a href="<?php echo page_url;?>Task/graphStructure" class="btn btn-primary"><i class="fa fa-sitemap"></i> Tree View</a>
                                <a href="<?php echo page_url;?>Task" class="btn btn-success"><i class="fa fa-plus"></i> Add New Task</a>
                            </div>
                        </div>
                        <script>
	                            function redirectToPage() {
	                                var selectElement = document.getElementById("pageSelect");
	                                var selectedValue = selectElement.value;

	                                if (selectedValue) {
	                                    showTaskPageLoader("Loading selected department...");
	                                    window.location.href = selectedValue;
	                                }
	                            }
                        </script>

                    </div>


                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive task-table-card">
                        	<table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Department</th>
									<th>Task</th>
									<th>Main / Sub Task</th>
                                   <th>TAT</th>
                                   <!-- <th>Task Frequency</th> -->
									<th>TAT Start From</th>
									<th>Sort Order</th>
                                    <th>Message</th>
                                    <th>Responsible Person</th>
                                   <th>Final Step</th>
                                    <th>Visible for MD Sir</th>
									<!--<th>Status</th>-->
									
                                </tr>
                                </thead>
                            </table>

                        </div>

                    </div>

                </div>


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



<script>

$(document).ready(function(){

$("#depsave").attr('disabled',false);

$("#depsave").val('submit');

$("#loginForm").on("submit", function(){

// $("#pageloader").fadeIn();

$("#depsave").attr('disabled',true);

$("#depsave").val('Please Wait...');

});//submit

});//document ready

</script>  
<script>

	function showTaskPageLoader(message) {
		jQuery("#taskLoaderText").text(message || "Loading task list...");
		jQuery("#taskPageLoader").fadeIn(120);
	}

	function hideTaskPageLoader() {
		jQuery("#taskPageLoader").fadeOut(120);
	}

	$( document ).ready(function() {

	showTaskPageLoader("Loading task list...");

	$('#example').dataTable({

"bProcessing": true,
"bAutoWidth": false,
dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data export'
            }
        ],

fixedHeader: true,
"pagination":true,
"iDisplayLength":25,
"aLengthMenu":[[25,50,100,-1],[25,50,100,"All"]],

	"sAjaxSource": "<?php echo page_url;?>Task/tasklistdata/<?php echo $this->uri->segment(3);?>",
	stateSave:false,
	"fnInitComplete": function() {
		hideTaskPageLoader();
	},
	"fnDrawCallback": function() {
		hideTaskPageLoader();
	},

	"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'department' },
				{ mData: 'task_name' },
				{ mData: 'type' },
				{ mData: 'tat' },
				// { mData: 'task_frequency' },
				{ mData: 'taskname' },
				{ mData: 'sortorder' },
                { mData: 'message' },
                { mData: 'responsibleperson' },
				{ mData: 'fstep' },
				 { mData: 'visibleformd' }
				

		]

});   

});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#depsave").click(function() {

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
    function updateorders(i) {
        var taskid = i;
        var selectedid =  jQuery("#taskid"+taskid).val();
        jQuery.ajax({
        type:"post",
        url:"<?php echo page_url;?>Task/updatetasksortorder",
        data:"selectedid="+selectedid+"&taskid="+taskid,
        success:function(data){
        jQuery("#success"+taskid).html(data);
        }
});

    }
</script>
<script type="text/javascript">
    function updateTaskHierarchy(taskid) {
        var taskType = jQuery("#task_type_" + taskid).val();
        var startFrom = jQuery("#startfrom_" + taskid).val();
        var statusBox = jQuery("#hierarchy_status_" + taskid);

        statusBox.removeClass("success error").html("Saving...");

        jQuery.ajax({
            type: "post",
            url: "<?php echo page_url;?>Task/update_task_hierarchy",
            dataType: "json",
            data: {
                taskid: taskid,
                task_type: taskType,
                startfrom: startFrom
            },
            success: function(response) {
                if (response && response.success) {
                    statusBox.removeClass("error").addClass("success").html(response.message);
                } else {
                    statusBox.removeClass("success").addClass("error").html(response && response.message ? response.message : "Unable to update.");
                }
            },
            error: function() {
                statusBox.removeClass("success").addClass("error").html("Unable to update.");
            }
        });
    }
</script>

    </body>

</html>
