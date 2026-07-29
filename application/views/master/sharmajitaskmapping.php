<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Sharmaji Task Mapping</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
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

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						  </div>
                           
                            <h4 class="page-title text-center">ADD TASK IN <?php 
                               $q = $this->db->select('*')->from('mdgantchartmaster')->where('id',$this->uri->segment(3))->get();
                                foreach($q->result() as $row);
                               echo "<strong>".$row->taskname."</strong>";
                        ?> REPORT</h4><hr>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
				<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
				<div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">
							<div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">
									<form method="post" action="<?php echo page_url;?>Task/addtaskmapping/<?php echo $this->uri->segment(3);?>">
										 <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">SELECT TASK AND ADD IN THIS LIST <span style="color:red; ">*</span>
                                                            <?php 
                                                                $q = $this->db->select('b.task_name')->from('sharmajitaskmapping a')->join('task_management b','a.task_id=b.task_id','left')->where('a.report_id',$row->id)->get();
                                                                foreach($q->result() as $row1){
                                                                    echo '<span class="btn btn-primary btn-xs">'.$row1->task_name.'</span><span style="padding-left:10px"></span> ';
                                                                }
                                                            ?>
                                                        </label>
														<span id="error_my_multi_select1" style="color:red;"></span>
												<select multiple="multiple" class="multi-select select2" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
													<?php 
                                                        $q = $this->db->select('task_id, task_name')->from('task_management')->where('department_id',$row->department_id)->get();
                                                        foreach($q->result() as $rows){
                                                    ?>
													
													<option value="<?php echo $rows->task_id;?>"><?php echo $rows->task_name;?></option>
													<?php }?>
												</select>
												    </div>
                                                </div>

                                            <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">SET A TASK AS MASTER<span style="color:red; ">*</span></label>
                                                        <span id="error_my_multi_select1" style="color:red;"></span>
                                                <select class="form-control select2" id="masterid" name="masterid" data-plugin="multiselect" required>
                                                    <?php 
                                                        $q = $this->db->select('task_id, task_name')->from('task_management')->where('department_id',$row->department_id)->get();
                                                        foreach($q->result() as $rows){
                                                    ?>
                                                    
                                                    <option value="<?php echo $rows->task_id;?>" <?php if($row->main_task_id==$rows->task_id){echo "selected";}?>><?php echo $rows->task_name;?></option>
                                                    <?php }?>
                                                </select>
                                                    </div>
                                                </div>
										<div class="col-md-9"></div>
										<div class="col-md-3">
											<div class="form-group pull-right" style="padding-top:24px;">
												<label>&nbsp;</label>
												<input type="submit" class="btn btn-success" value="Update" id="save">
											</div>
										</div>
									</form>
                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
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
		<script>
		$(document).ready(function() {
			
			$('.select2').select2({ });
		});
		</script>
		 <script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
		 
<script language="javascript" type="text/javascript">   
$(document).ready(function() {
$("#save").click(function() {
var my_multi_select1 = $("#my_multi_select1").val();
if(my_multi_select1=='')
{
	$("#error_my_multi_select1").html('Required!');
}


if(category_name=='' || product_name=='')
{
	
	return false;
}

});
});
</script>
   </body>
</html>