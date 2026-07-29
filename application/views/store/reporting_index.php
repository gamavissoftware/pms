<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Reporting Index</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        
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
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
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
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}
</style>
    </head>
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
						
                           
                            <h4 class="page-title">REPORTING INDEX DASHBOARD</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <?php 
                            $i=1;
                            $q = $this->db->select('report_type')->from('reporting_index')->group_by('report_type')->order_by('id','asc')->get();
                            foreach($q->result() as $row){
                            ?>
                            
                            
                            <table class="table table-striped table-bordered manglesh">
                                <thead>
                                    <tr>
                                        <th colspan="11" style="text-align:center"><?php echo $row->report_type;?></th>
                                    </tr>
                                </thead>
                                <thead>
                                <tr>
                                    
									<th>REPORT ID</th>
									<th style="width:20%">REPORT NAME</th>
									<th>PURPOSE OF REPORT</th>
									<th>AUTO TRIGGER</th>
									<th>ACCESS GIVEN TO VIEW</th>
									
                                </tr>
                                </thead>
								

                                <tbody>
                                    <?php 
                                    $j=1;
                                    $q = $this->db->select('report_name,url,report_id,purpose_of_report, moduleid, frequency')->from('reporting_index')->where('report_type',$row->report_type)->get();
                                    foreach($q->result() as $rows){
                                        
                                    ?>
								    <tr>
								         
								        <td><?php echo $rows->report_id;?></td>
								        <td><a href="<?php echo page_url;?><?php echo $rows->url;?>" target="_blank"><?php echo $rows->report_name;?></a></td>
								        <td><?php echo $rows->purpose_of_report;?></td>
										<td><?php echo $rows->frequency;?></td>
								        <td>
										<table class="table table-bordered">
										<thead bgcolor="#38C1A3">
											<tr>
												<td style="color:#fff;">SR NO</td>
												<td style="color:#fff;">NAME</td>
												<td style="color:#fff;">ADD/VIEW</td>
												<td style="color:#fff;">EDIT</td>
												<td style="color:#fff;">MAIL</td>
												<td style="color:#fff;">SMS</td>
												<td style="color:#fff;">WHATSAPP</td>
												<td style="color:#fff;">FREQUENCY</td>
											</tr>
										</thead>
										<tbody>
										<?php 
										$m=1;
										$this->db->distinct();
										$q = $this->db->select('a.madd,a.medit,a.sms, a.email, a.whatsapp, a.frequency,a.role_id,b.first_name, b.last_name')->from('module_capablity a')->join('system_users b','a.role_id=b.user_id','left')->where('a.submoduleid',$rows->moduleid)->where('a.submodule_access','1')->where('b.user_status','1')->order_by('b.first_name','asc')->get();
										
										foreach($q->result() as $userinfo){
											
										if($userinfo->madd=='1')
										{
										$maddflag='1';
										}else
										{
										$maddflag='0';
										}

										if($userinfo->medit=='1')
										{
										$meditflag='1';
										}else
										{
										$meditflag='0';
										}
										
										if($userinfo->sms=='1')
										{
										$smsflag='1';
										}else
										{
										$smsflag='0';
										}
										
										if($userinfo->email=='1')
										{
										$emailflag='1';
										}else
										{
										$emailflag='0';
										}
										
										if($userinfo->whatsapp=='1')
										{
										$whatsappflag='1';
										}else
										{
										$whatsappflag='0';
										}
										
										
										?>
											<tr>
												<td><?php echo $m;?></td>
												<td><?php echo $userinfo->first_name." ".$userinfo->last_name."<br>";?><br>
											
											</td>
												<td><input type="checkbox" id="add<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>" value="1" onchange="addfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');" <?php if($maddflag=='1'){?> checked <?php } ?>>
												<div id="addsuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												<td><input type="checkbox" id="edit<?php echo $rows->moduleid?><?php echo $userinfo->role_id;?>" onchange="editfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');" value="1" <?php if($meditflag=='1'){?> checked <?php } ?>>
												<div id="editsuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												
												<td><input type="checkbox" value="1" id="email<?php echo $rows->moduleid?><?php echo $userinfo->role_id;?>" onchange="emailfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');" <?php if($emailflag=='1'){echo "checked";}?>>
												<div id="emailsuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												<td><input type="checkbox" value="1" id="sms<?php echo $rows->moduleid?><?php echo $userinfo->role_id;?>" onchange="smsfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');" <?php if($smsflag=='1'){echo "checked";}?>>
												<div id="smssuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												<td><input type="checkbox" value="1" id="whatsapp<?php echo $rows->moduleid?><?php echo $userinfo->role_id;?>" onchange="whatsappfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');" <?php if($whatsappflag=='1'){echo "checked";}?>>
												<div id="whatsappsuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												<td><select class="form-control" id="frequency<?php echo $rows->moduleid?><?php echo $userinfo->role_id;?>" onchange="frequencyfunction('<?php echo $rows->moduleid;?>','<?php echo $userinfo->role_id;?>');">
												<option value="">Select Frequency</option>
												<option value="1" <?php if($userinfo->frequency=='1'){echo "selected";}?>>Daily</option>
												<option value="2" <?php if($userinfo->frequency=='2'){echo "selected";}?>>Weekly</option>
												<option value="3" <?php if($userinfo->frequency=='3'){echo "selected";}?>>Monthly</option>
												</select>
												<div id="frequencysuccess<?php echo $rows->moduleid;?><?php echo $userinfo->role_id;?>"></div>
												</td>
												
												<script>
  function showCustomer()
  {
    var userid=$('#username').val();
    //alert(userid);
    //var masdata='';
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Reporting/getdepartment_user/",
    data:"userid="+userid,
    success:function(data){
    //alert(data);
    $('#mesg').html(data);
    }
    });
    
  }

  </script> 

												<script>
												function addfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													if ($('#add'+submoduleid+userid).is(":checked")){
														var addattr = "1";
													}else{
														var addattr = "0";
													}
													
												
													
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_access/",
													data:"addattr="+addattr+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
														//alert(data);
													$('#addsuccess'+submoduleid+userid).html(data);
													}
													});
												}
												</script>
												
												<script>
												function editfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													if ($('#edit'+submoduleid+userid).is(":checked")){
														var editattr = "1";
													}else{
														var editattr = "0";
													}
													
												
													
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_edit_access/",
													data:"editattr="+editattr+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
													$('#editsuccess'+submoduleid+userid).html(data);
													}
													});
												}
												
												function smsfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													if ($('#sms'+submoduleid+userid).is(":checked")){
														var smsattr = "1";
													}else{
														var smsattr = "0";
													}
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_sms_access/",
													data:"smsattr="+smsattr+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
													$('#smssuccess'+submoduleid+userid).html(data);
													}
													});
												}
												function emailfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													if ($('#email'+submoduleid+userid).is(":checked")){
														var emailattr = "1";
													}else{
														var emailattr = "0";
													}
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_email_access/",
													data:"emailattr="+emailattr+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
													$('#emailsuccess'+submoduleid+userid).html(data);
													}
													});
												}
												function whatsappfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													if ($('#whatsapp'+submoduleid+userid).is(":checked")){
														var whatsappattr = "1";
													}else{
														var whatsappattr = "0";
													}
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_whatsapp_access/",
													data:"whatsappattr="+whatsappattr+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
													$('#whatsappsuccess'+submoduleid+userid).html(data);
													}
													});
												}
												function frequencyfunction(i,j){
													var submoduleid = i;
													var userid = j; 
													var frequency = $("#frequency"+submoduleid+userid).val();
													$.ajax({
													type:"post",
													url:"<?php echo page_url;?>Reporting/update_frequency_access/",
													data:"frequency="+frequency+"&moduleid="+submoduleid+"&userid="+userid,
													success:function(data){
													$('#frequencysuccess'+submoduleid+userid).html(data);
													}
													});
												}
												</script>
												
											</tr>
										<?php $m++;}?>
										
										</tbody>
										</table>
											<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter<?php echo $rows->moduleid;?>">
        <i class="fa fa-plus" aria-hidden="true"></i>
      </button>

<form action="<?php echo page_url;?>Reporting/setpermission/<?php echo $rows->moduleid;?>" method="post">
    <div class="modal fade" id="exampleModalCenter<?php echo $rows->moduleid;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $rows->report_name;?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
                </div>
                <div class="modal-body">


                   
                        <select name="username" id="username" onchange="showCustomer(this.value)" class="form-control">
                    <option value="">Select </option>
                    <?php 
                    $res=$this->db->select('department_id,department,status')->from('departments')->where('status',1)->get();
                    if($res->num_rows() > 0)
                    {
                        foreach($res->result() as $rows)
                        {
                    
                    ?>
                    <option value="<?php echo $rows->department_id; ?>"><?php echo $rows->department; ?></option>
                    <?php }} ?>
                    </select>
                    
                    <br>
                    <div id="mesg"></div>
                    <input class="btn btn-primary" type="submit" value="Submit">

                </div>
               
            </div>
        </div>
    </div>
    </form>
										</td>
								        
								       
								       
								    </tr>
								    <?php $j++;}?>
                                </tbody>
                            </table>
                            <?php $i++; }?>
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

		<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>



</body>
</html>