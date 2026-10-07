<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>
<!DOCTYPE html>
<html>
    <head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php //echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
<title><?php //echo sitetitle; ?>Task Master Dashboard</title>
<script src="<?php echo assets_url;?>js/angular.min.js"></script>
<link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<?PHP 
//$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
//foreach($q->result() as $LOGO);
?>

    <style>
table.manglesh thead th {
background: red;
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
left: 50%;
margin-left: -32px;
margin-top: -32px;
position: absolute;
top: 50%;
}


</style>


	</head>

    <body>
<header id="topnav">
<?php $this->load->view('common/nav-menu');?>
</header>


        <!-- End Navigation Bar-->

<div class="wrapper ">
<div class="container-fluid ">

</div>
</div>


<div class="wrapper ">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Task Management Dashboard</h1><hr>
</div>

    <div >
    <div class="row">
<?php
$this->load->helper('overtime');
$ot_master_permissions = ot_user_permissions($this->db, (int)$user_id);
$ot_master_user = $this->db->query('SELECT r.isadmin FROM system_users u JOIN user_role r ON r.user_role_id=u.user_role_id AND r.status=1 WHERE u.user_id=? AND u.user_status=1', array((int)$user_id))->row_array();
if (!empty($ot_master_user['isadmin'])) {
    foreach (array('policy'=>array('policy','Overtime Request Limits','fa-clock-o'), 'costs'=>array('cost','Overtime Cost Rates','fa-inr'), 'leaders'=>array('leaders','Overtime Reporting Leaders','fa-users')) as $cap=>$master) {
        if (empty($ot_master_permissions[$cap])) continue;
?>
<div class="col-sm-4 col-md-4 col-lg-2"><a href="<?php echo page_url; ?>Overtime/settings?section=<?php echo $master[0]; ?>"><div class="report-box"><div class="text-center"><i class="fa <?php echo $master[2]; ?>" style="font-size:45px" aria-hidden="true"></i></div><p><?php echo $master[1]; ?></p></div></a></div>
<?php } } ?>


   <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','7')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/taskmanagement">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-tasks" style="font-size:45px"></i>
                                </div>
                                <p>Task Master</p>
                            </div>
                        </a>
                    </div>
     <?php }?>

                  <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','8')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/paymenttermsmanagement">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-file-word-o" style="font-size:45px"></i>
                                </div>
                                <p>Payment Terms</p>
                            </div>
                        </a>
                    </div>
                <?php }?>

                  <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','9')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/poreceived">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-inr" style="font-size:45px;"></i>
                                </div>
                                <p>Add New PO</p>
                            </div>
                        </a>
                    </div>
                <?php }?>

                 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','9')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/legacy_data">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-inr" style="font-size:45px;"></i>
                                </div>
                                <p>Legacy Data</p>
                            </div>
                        </a>
                    </div>
                <?php }?>
                  <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','10')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/receivedpolist">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-list" style="font-size:45px;"></i>
                                </div>
                                <p>Received PO List</p>
                            </div>
                        </a>
                    </div>

                <?php }?>

                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/myreceivedpolist">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-user" style="font-size:45px;"></i>
                                </div>
                                <p>My Received PO List</p>
                            </div>
                        </a>
                    </div>

                <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','51')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/basicmachinelistdata">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-list" style="font-size:45px;"></i>
                                </div>
                                <p>Basic Machine Order List</p>
                            </div>
                        </a>
                    </div>
                <?php }?>

                 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','16')->where('submoduleid','46')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/basicmachinedata">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-list" style="font-size:45px;"></i>
                                </div>
                                <p>Basic Machine List</p>
                            </div>
                        </a>
                    </div>
                <?php }?>
                   <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','11')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/dfrelease">
                            <div class="report-box">
                                <div class="text-center">
                                   <i class="fa fa-clone" style="font-size:45px"></i>
                                </div>
                                <p>Release DF</p>
                            </div>
                        </a>
                    </div>
                <?php }?>

                 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','35')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/dfreleasedashboard">
                            <div class="report-box">
                                <div class="text-center">
                                   <i class="fa fa-list" style="font-size:45px"></i>
                                </div>
                                <p>All DF List</p>
                            </div>
                        </a>
                    </div>
                <?php }?>
                  <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','12')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/dfmeeting">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-group" style="font-size:45px; color:#000"></i>
                                </div>
                                <p>DF Meeting</p>
                            </div>
                        </a>
                    </div>
                <?php }?>

                <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','43')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                     <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Task/sharmajitaskmanagement">
                            <div class="report-box">
                                <div class="text-center">
                                    <i class="fa fa-group" style="font-size:45px; color:#000"></i>
                                </div>
                                <p>MD Sir Task Management</p>
                            </div>
                        </a>
                    </div>
                <?php }?>


                       

 

          
</div>

       
        
    </div>

    


    <script>
        function openPage(pageName, elmnt, color) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].style.backgroundColor = "";
            }
            document.getElementById(pageName).style.display = "block";
            elmnt.style.backgroundColor = color;

            
        }

        document.getElementById("defaultOpen").click();
    </script>



      
    </div>
</div>

<!-- Footer -->


               <?php $this->load->view('common/footer');?>


                <!-- End Footer -->





            </div> <!-- end container -->


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
<script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
</body>
</html>
