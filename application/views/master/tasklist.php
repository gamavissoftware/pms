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
        <?php $this->load->view('common/info-section.php');?>
        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">

						 <div class="btn-group pull-right">
                             <div class="form-group">
                            <label>Filter by Department</label>
                            <select class="form-control" id="pageSelect" name="pageSelect" onchange="redirectToPage();">
                                <option value=""></option>
                                <?php 
                                    $q = $this->db->select('b.department_id, b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('b.status',1)->where('b.business_loc_id',2)->group_by('a.department_id')->get();
                                    foreach($q->result() as $row){
                                ?>
                                <option value="<?php echo page_url;?>Task/taskmanagement/<?php echo $row->department_id;?>"><?php echo ucwords(strtolower($row->department));?></option>
                            <?php }?>
                            </select>
                        </div>
                        <script>
                        function redirectToPage() {
                        var selectElement = document.getElementById("pageSelect");
                        var selectedValue = selectElement.value;

                        if (selectedValue) {
                        window.location.href = selectedValue;
                        }
                        }
                        </script>

						<a href="<?php echo page_url;?>Task"><span class="btn btn-success">Add New Task</span></a>
						</div>
			 <h4 class="text-center" >
                <a href="<?php echo page_url;?>Task/graphStructure"><span class="pull-left btn btn-primary btn-xs">Tree View</span></a> Task <?php 
                $q = $this->db->select('department')->from('departments')->where('department_id',$this->uri->segment(3))->get();
                if($q->num_rows()>0){
                    foreach($q->result() as $row);
                    echo "For ".ucwords(strtolower($row->department))." Department";
                }else{
                    echo "Management ";
                }
            ?></h4><hr>

                        </div>

                    </div>


                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
                        	<table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Department</th>
									<th>Task</th>
									<!-- <th>Task Type</th> -->
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

$( document ).ready(function() {

$('#example').dataTable({

"bProcessing": true,
dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Data export'
            }
        ],

	fixedHeader: true,
"pagination":true,

"sAjaxSource": "<?php echo page_url;?>Task/tasklistdata/<?php echo $this->uri->segment(3);?>",
stateSave:true,

"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'department' },
				{ mData: 'task_name' },
				// { mData: 'type' },
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
        var selectedid =  $("#taskid"+taskid).val();
        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Task/updatetasksortorder",
        data:"selectedid="+selectedid+"&taskid="+taskid,
        success:function(data){
        $("#success"+taskid).html(data);
        }
});

    }
</script>

    </body>

</html>

