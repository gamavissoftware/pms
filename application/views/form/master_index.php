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



        <title><?php echo sitetitle; ?> Master Index</title>



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

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">

		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.jqueryui.min.css">

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

	position: relative;

				background: <?php echo $LOGO->colorcode;?>;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}

			table.taskdetail thead th {

				background: yellow;

				color:#000;

				font-weight:bold;

			}

			

		table.sales thead th {

	position: relative;

				background: red;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}	



			

</style>

<script type="text/javascript">

    window.onload = function(){

        location.href=document.getElementById("selectbox").value;

    }       

</script>



<script type="text/javascript">

    window.onload = function(){

        location.href=document.getElementById("selectbox").value;

    }  

	function onuserchange(id){

	var userid = $('#checklist_user'+id).val();

		if(userid!==''){

		url = "https://prestomitr.com/Checklist/view_user_checklist/"+userid;

		window.open(url, '_blank');

		}

	}

	

	function onref_search(id){

		var ref_id = $('#reference'+id).val();

		var sub_ref_id = $('#subref'+id).val();

		if(sub_ref_id!==''){

		url = "https://prestomitr.com/Form/fetch_ref_doc/"+ref_id+"/"+sub_ref_id;

		 window.open(url, '_blank');

		}

	}

	

	

		function onref_search1(id){

		var ref_id = $('#reference'+id).val();

		var sub_ref_id = $('#subref'+id).val();

		var subsubref=$("#subsubref"+id).val();

		if(sub_ref_id!='' && subsubref!=''){

		url = "https://prestomitr.com/Form/fetch_ref_doc/"+ref_id+"/"+sub_ref_id+"/"+subsubref;

		 window.open(url, '_blank');

		}

	}

	

	function onemail_search(id){

		var emailsubject = $('#emailsubject'+id).val();

		if(emailsubject!==''){

		url = "https://prestomitr.com/Master/User_management/view_email_template/"+emailsubject;

		 window.open(url, '_blank');

		}

	}

	

	function checkforsubsubreference(id)

	{

	    

	    var subref=$("#subref"+id).val();

	    if(subref!='')

	    {

        $.ajax({

        type:"post",

        url:"<?php echo page_url;?>Form/get_sub_sub_ref",

        data:"subref="+subref,

        success:function(data){

            if($.trim(data)!='NA')

            {

                $(".subsubrefifavailable"+id).css('display','');

        $("#subsubref"+id).html(data);

                

            }else

            {

               $(".subsubrefifavailable"+id).css('display','none');

               onref_search(id); 

            }

          

        }

        });

	    }

	    

	    

	}

</script>



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

                <div class="row">

                    <div class="col-sm-12">

                        <div class="row">

						 <div class="col-md-12"><h4 class="page-title text-center">MASTER INDEX</h4></div>

                        </div>

                    </div>

					

                </div>



		<div class="row">

		

                    <div class="col-sm-12">

                        <div class="card-box table-responsive" id="target">

                           <table class="table table-bordered manglesh scroll display">

						   <?php

							$k=1; 

							$query = $this->db->select('department,department_id, reference,show_in_master_index')->from('departments')->where('status','1')->where('show_in_master_index','1')->order_by('department','asc')->get();

							foreach($query->result() as $department){

							    $dpt= strtoupper($department->department);

							    if($dpt=='HR & ADMIN'){

							        $departmentmodel="HRADMIN";

							    }else{

								$departmentmodel = preg_replace('/\s+/', '_', $department->department);

							    }

							?>

							<thead>

							

								<tr class="text-center">

								<th class="text-center" colspan="4"><?php echo strtoupper($department->department);?> <span style="margin-left:20px"></span><i class="fa fa-folder-open-o" aria-hidden="true" title="view_reference" data-toggle="modal" data-target="#<?php echo $departmentmodel;?><?php echo $department->department_id;?>"></i><span style="margin-left:20px"></span><i class="fa fa-check-square-o" aria-hidden="true" title="Checklist" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>"></i><span style="margin-left:20px"></span><i class="fa fa-envelope-square" aria-hidden="true" title="Email Template" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>emailtemplate"></i></th>

								</tr>

								

								

								<div id="<?php echo $departmentmodel;?>emailtemplate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

								<form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> EMAIL TEMPLATE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										

										<div class="col-md-12">

										<label>EMAIL SUBJECT</label>

										<select class="form-control" name="emailsubject<?php echo $department->department_id;?>" id="emailsubject<?php echo $department->department_id;?>"  onChange="onemail_search(<?php echo $department->department_id;?>);">

										<option value="">--Select Subject--</option>

										<?php 

										$q =$this->db->select('*')->from('departmentwise_email_template')->where('department_id',$department->department_id)->get();

										foreach($q->result() as $email){

										?>

										<option value="<?php echo $email->id;?>"><?php echo $email->email_subject;?></option>

										<?php }?>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

								<div id="<?php echo $departmentmodel;?><?php echo $department->department_id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> REFERENCE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<div class="col-md-6">

										<label>VIEW REFERENCE</label>

										<select class="form-control" id="reference<?php echo $department->department_id;?>" name="reference<?php echo $department->department_id;?>" onChange="get_sub_reference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										<?php $query = $this->db->select('ref_id, reference_title')->from('department_reference')->where('department_id',$department->department_id)->get();

										foreach($query->result() as $ref){?>

										<option value="<?php echo $ref->ref_id;?>"><?php echo $ref->reference_title;?></option>

										<?php }?>

										

										</select>

										

										<script type="text/javascript">

											

													function get_sub_reference(id){

													var ref=$("#reference"+id).val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Form/get_sub_ref",

													data:"ref="+ref,

													success:function(data){

													$("#subref"+id).html(data);

													}

													});

													

													}

													

											

												</script>

										</div>

										<div class="col-md-6">

										<label>VIEW SUB REFERENCE</label>

										<select class="form-control" name="subref<?php echo $department->department_id;?>" id="subref<?php echo $department->department_id;?>"  onChange="checkforsubsubreference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

										

										<div class="col-md-6 subsubrefifavailable<?php echo $department->department_id;?>" style="display:none;">

										<label>VIEW SUB SUB REFERENCE</label>

										<select class="form-control" name="subsubref<?php echo $department->department_id;?>" id="subsubref<?php echo $department->department_id;?>"  onChange="onref_search1(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							

							<div id="<?php echo $departmentmodel;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> USER CHECKLIST</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

										<div class="col-md-12">

										<label>VIEW USER CHECKLIST</label>

										<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<select class="form-control" name="checklist_user<?php echo $department->department_id;?>" id="checklist_user<?php echo $department->department_id;?>" onChange="onuserchange(<?php echo $department->department_id;?>);">

											<option value="">--SELECT USER--</option>

										<?php 

										$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',$department->department_id)->where('user_status','1')->where('hide_profile','0')->get();

										foreach($query->result() as $users){

										?>

										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>

										<?php }?>

									</select></div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							</thead>

							<tbody>

								<tr style="background-color:#1E98B1; color:#fff;">

									<td>SR NO</td>

									<td>FORM</td>

									<td class="text-center">SPEED FMS (PENDING)</td>

									<td>HISTORY</td>

								</tr>

								<?php 

								$i=1;

								$query = $this->db->select('id,department_id, form_title, description, status, dashboard_title, form_video_link, dashboard_video_link')->from('dynamic_forms')->where('department_id',$department->department_id)->where('status','1')->where('form_running_status','0')->get();

									$dyrow=$query->num_rows();

									

								foreach($query->result() as $row){

								?>

								<tr>

									<td><?php echo $i;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><span style="margin-left:20px"></span> 

									<?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','11')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?><a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px" ></i></a>&nbsp; &nbsp; &nbsp;<a href="<?php echo page_url;?>Form/edit_form/<?php echo $row->id;?>"><i class="fa fa-pencil-square-o pull-right" style="margin-left: 1.3em !important;" title="EDIT FORM"></i></a> &nbsp; &nbsp; &nbsp;  <a href="<?php echo page_url;?>Form/set_permission_of_access/<?php echo $row->id;?>" target="_blank"><i class="fa fa-eye pull-right" style="    margin-left: 1.3em !important;" title="SET LABEL PERMISSION"></i></a><?php }?></td>

								

									<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#246c7b; color:#fff;">SPEED FMS</a> <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px"></i></a></td>

										<td style="color:#000;"><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								</tr>

								<?php $i++;}?>

								

								<?php

								if($dyrow>0)

								{

								$t=$dyrow+1;

								}else

								{

								    $t=1;

								    

								}

								$delegationavail=$this->db->select('a.id,a.video,a.dashboard_video_link,b.first_name,b.last_name,b.user_id')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id')->where('b.department_id',$department->department_id)->get();

								$delegationcount = $delegationavail->num_rows();

								if($delegationavail->num_rows()>0)

								{

									

									foreach($delegationavail->result() as $delegations)

									{

										

								?>

								<tr>

								<td><?php echo $t;?></td>

								<td><a href="<?php echo page_url;?>Delegation/delegation_task/<?php echo $delegations->user_id;?>/1" target="_blank">DELEGATION FORM FOR <?php echo strtoupper($delegations->first_name.' '.$delegations->last_name);?></a><a href="<?php echo $delegations->video;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

									<td class="text-center"><a href="<?php echo page_url;?>Delegation/delegation_dashboard/<?php echo $delegations->user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#246c7b; color:#fff;">SPEED FMS</a> <a href="<?php echo $delegations->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

								<td><a href="<?php echo page_url;?>Delegation/delegated_task_history/<?php echo $delegations->user_id;?>" target="_blank">DELEGATION HISTORY</a></td>

								</tr>

								<?php

								$t++;

								}

								}

								?>

								

								<?php

								if($delegationcount>0)

								{

								$s=$t+1;

								}else

								{

								    $s=$t;

								    

								}

								

								$q1=$this->db->select('form_title, form_url, dashboard_title, form_vide_link, dashboard_video_link, dashboard_url, history_url')->from('standalone_dashboard_access a')->where('department_id',$department->department_id)->get();

								$q1count = $q1->num_rows();

							

								if($q1->num_rows()>0)

								{

								    /**if($delegationcount!='0'){

									$s=$delegationcount+2;

									}else if($dyrow=='0'){

									    $s=1;

									}else{

									    $s=$dyrow+1;

									} **/

									foreach($q1->result() as $standalonedata)

									{

										

								?>

								<tr>

								<td><?php echo $s;?></td>

								<td>

								    <?php if($standalonedata->form_url){?>

								    <a href="<?php echo page_url;?><?php echo $standalonedata->form_url;?>" target="_blank"><?php echo $standalonedata->form_title;?></a><?php }else{?><?php echo $standalonedata->form_title;?><?php }?> <span class="pull-right"><a href="<?php echo $standalonedata->form_vide_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td class="text-center">

								    <a href="<?php echo page_url;?><?php echo $standalonedata->dashboard_url;?>" target="_blank" class="btn btn-xs" style="background-color:#246c7b; color:#fff;">SPEED FMS</a>

								    <span class="pull-right"><a href="<?php echo $standalonedata->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span>

								   </td>

								<td><?php if($standalonedata->history_url==''){?><?php echo $standalonedata->dashboard_title;?><?php }else{?><a href="<?php echo $standalonedata->history_url;?>" target="_blank"><?php echo $standalonedata->dashboard_title;?></a><?php }?></td>

							

								

								</tr>

								<?php

								$s++;

								}

								}

								?>

								

								<?php 

								if($q1count>0)

								{

							     $r=$s+1;

								}else

								{

								$r=$s;

								}

								$query = $this->db->select('a.form_id, a.user_id, b.user_id, b.department_id, c.id,c.form_title, c.description, c.status, c.dashboard_title, c.form_video_link, c.dashboard_video_link')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id')->join('dynamic_forms c','a.form_id=c.id')->where('b.department_id',$department->department_id)->where('c.status','1')->where('c.form_running_status','0')->group_by('a.form_id')->get();

									$dyrow=$query->num_rows();

								if($query->num_rows()>0){	

								  

								foreach($query->result() as $row){

								     

									

									

								?>

								<tr>

									<td><?php echo $s++;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><br>

									<?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px"></i></a>&nbsp; &nbsp; &nbsp;<a href="<?php echo page_url;?>Form/edit_form/<?php echo $row->id;?>"><i class="fa fa-pencil-square-o pull-right" style="margin-left: 1.3em !important;"></i></a><?php }?></td>

										<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#246c7b; color:#fff;">SPEED FMS</a>  <span class="pull-right"><a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								

								</tr>

								<?php $r++; }}?>

							</tbody>

							<?php $k++;}?>

						   </table>

                        </div>

                    </div>

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

	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>	

	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>	

	<script src="https://cdn.datatables.net/1.10.20/js/dataTables.jqueryui.min.js"></script>	

	<script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>	

		

 <script>

 $( document ).ready(function() {

	$('.select3').select2({ });

$('#example').dataTable({

 "bProcessing": false,

 "searching": false,

  fixedHeader: true,

 "bPaginate": false,

 "bLengthChange": false,

 "pagination":true

 });   

});



</script>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

</body>

</html>