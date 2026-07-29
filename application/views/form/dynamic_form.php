<?php 

$user_id =$this->session->userdata['logged_in']['user_id'];

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> <?php $query = $this->db->select('a.tatappl,a.id, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();

							foreach($query->result() as $row){

							echo $row->form_title;

							}?></title>



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

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

		<link href="<?php echo assets_url;?>plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">



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

			}

			

#pageloader

{

  background: rgba( 255, 255, 255, 0.8 );

  display: none;

  height: 100%;

  position: fixed;

  width: 100%;

  z-index: 9999;

}

#pageloader img

{

  left: 30%;

  margin-left: -10px;

  margin-top: -10px;

  position: absolute;

  top: 30%;

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

                <div class="row">

                    <div class="col-sm-12">

                        <div class="page-title-box">

                            <div class="btn-group pull-right">

                              

                            </div>

                           

                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->





                <div class="row card-box">

			<div class="col-md-2 hidden-xs">

                      <?php 

                      $query = $this->db->select('form_video_link')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();

                      foreach($query->result() as $row);

                      ?>

                      <a href="<?php echo $row->form_video_link; ?>" target="_blank"><span class="btn btn-success btn-xs">Click here to view Video <i class="fa fa-video-camera" style="font-size:15px"></i></span></a>

                  </div>

                  <div style="clear:both;"></div>

                  <div class="col-md-2 hidden-xs"></div>

                    <div class="col-xs-12 col-md-8">

                        <div>

 <p class="page-title text-center"><?php $query = $this->db->select('a.tatappl,a.id, a.form_title, a.description, a.department_id, b.department_id, b.department')->from('dynamic_forms a')->join('departments b','a.department_id=b.department_id','left')->where('a.id',$this->uri->segment(3))->get();

							foreach($query->result() as $row){

								echo "<span style='font-size:18px'>".strtoupper($row->form_title)."</span>";

								echo "<span style='font-size:14px'>  (".strtoupper($row->description).")</span>";

							}?></p><hr>

                            <div class="row">

							<div class="col-md-2"></div>

                                <div class="col-sm-12 col-xs-12 col-md-8" style="border:1px solid #E8E7E6; padding:20px 20px 20px 20px">

								<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

								<form method="post" id="loginForm" action="<?php echo page_url;?>Form/getdata/<?php echo $this->uri->segment(3);?>" enctype="multipart/form-data">

								      <?php

								    $tatavail=$row->tatappl;

								    ?>

								    <input type="hidden" name="tatappl" value="<?php echo $row->tatappl;?>">

								    <div id="pageloader">

                                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />

                                    </div>	

									

												<?php 

												$query = $this->db->select('field_id, field_design, field_type, form_id, form_label, field_required')->from(' master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();

												foreach($query->result() as $rowdata){

												?>

												<div class="row">

												<div class="col-md-12">

													<div class="form-group">

												

														 <label for="field-2" class="control-label"><?php echo strtoupper($rowdata->form_label);?></label> <?php if($rowdata->field_type=='4' || $rowdata->field_type=='9'){echo "<br>";}?>

														 

														<?php echo $rowdata->field_design;?>

														<input type="hidden" name="formfield[]" value="<?php 

														$name1= preg_replace('/\s+/', '', $rowdata->form_label);

														$name = strtolower($name1);

														echo $name;?>"> 

							

							                            <input type="hidden" name="field_type[]" value="<?php echo $rowdata->field_type;?>">

														

													</div>

												</div>

												

												</div>

												<?php }?>

												

												

											

											<div class="col-md-9"></div>

										<div class="col-md-3">

											<div class="form-group pull-right" style="padding-top:24px;">

												<label>&nbsp;</label>

												<input type="submit" id="save" class="btn btn-success" value="Submit">

											</div>

										</div>

									

									</form>

                                   



                                </div>

								<div class="col-md-2"></div>

                            </div>

                            <!-- end row -->

                        </div> <!-- end ard-box -->

                    </div><!-- end col-->

                  

                </div>

                <!-- end row -->

				

				

			

                        <div class="row card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered manglesh">

                                <thead>

                                <tr>

								<?php 

								$Q2 = $this->db->select('form_type')->from('dynamic_forms')->where('id',$this->uri->segment(3))->get();

								foreach($Q2->result() as $forminfo);

								

									$que=$this->db->select('form_id, form_label, field_type')->from('master_dynamic_fields')->where('form_id',$this->uri->segment(3))->get();

									

									if($que->num_rows()>0){

										foreach($que->result() as $row){

											?>

											<th><?php echo strtoupper($row->form_label);?></th>

											<?php

											}

											?>

											<th>RESPONSE ID</th>

											<th>TIMESTAMP</th>

											<th>PLANNED DATE</th>

											<th>WORK STATUS</th>

											<th>REMARKS</th>

											<?php

																						

									}

								?>

                                   

									

                                </tr>

                                </thead>

                                <tbody>

								<?php 

								$k=1;

								$lastdate = date('Y-m-d', strtotime('-15 days'));

								$lastdateinfo = $lastdate." 00:00:00";

								$today = date('Y-m-d');

								$todaysdate = $today." 23:59:59";

								$user_id =$this->session->userdata['logged_in']['user_id'];

									$q=$this->db->select('a.responseid,a.completely_done,a.id,a.form_id,a.work_status, a.formdata,b.mark_as_done, b.record_id, a.planned_date, a.added_on')->from('dynamic_form_data a')->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')->where('a.form_id',$this->uri->segment(3))->where('a.added_by',$user_id)->where('a.added_on BETWEEN "'.$lastdateinfo. '" and "'.$todaysdate.'"')->get();

									if($q->num_rows()>0){

										foreach($q->result() as $row){

								?>

								<tr>

									<?php 

									

										$json_data = json_decode($row->formdata,true);

										foreach($que->result() as $rowdata)

										{

											$name1= preg_replace('/\s+/', '', $rowdata->form_label);

											$name = strtolower($name1);

											

											$fieldtype = $rowdata->field_type;

											if($fieldtype=='5'){

											    

												if($json_data[$name]!==''){

												    

											$finalvalue=	"<a href='".dynamicformdata.strtolower($json_data[$name])."' download>CLICK HERE TO DOWNLOAD</a>";

												}else{

													$finalvalue=""; }	

											}else{

											$finalvalue = strtoupper($json_data[$name]);	

											}

											

											

									?>

									

									<td><?php echo $finalvalue;?></td>

										<?php }?>

										<td><?php echo $row->responseid;?></td>

										<td><?php  if($row->added_on=='0000-00-00 00:00:00'){}else{

											echo $row->added_on;

										};?></td>

										<td><?php  if($row->planned_date=='0000-00-00 00:00:00'){}else{

											echo $row->planned_date;

										};?></td>

										<td><?php if($row->work_status=='1'){

										echo "Marked as Done";}?></td>

										<td><?php 

									$qqq = $this->db->select('remarks')->from('dynamic_form_data_remarks')->where('data_id',$row->id)->get();

										    if($qqq->num_rows()>0){

										        foreach($qqq->result() as $remarksinfo){

										            echo $remarksinfo->remarks;

										        }

										    }

										?></td>

										

										<?php 

										}?>

										

								</tr>

									<?php $k++;}?>

                                </tbody>

                            </table>

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

		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>



        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>



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

<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>

$( document ).ready(function() {

$('#example').dataTable({

 "bProcessing": false,

 "pagination":true

 });   

});



</script>

<script>

$(document).ready(function(){

  $("#loginForm").on("submit", function(){

    $("#pageloader").fadeIn();

  });//submit

});//document ready

</script>

<script> $(document).ready(function() {

				$("#datepicker1").datepicker();

                $("#datepicker1btn").click(function(event) {

                    event.preventDefault();

                    $("#datepicker1").focus();

                })



            });</script>

			</body>

</html>