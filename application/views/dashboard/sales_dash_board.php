<?php $user_id = $this->session->userdata['logged_in']['user_id']; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Sales Dashboard</title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
     <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?PHP

    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

    foreach ($q->result() as $LOGO);

    ?>
<style type="text/css">
    .tool i {
    position: absolute;
    right: 18px;
    color: orange;
}
</style>
</head>
<body>
<!-- Navigation Bar-->
<header id="topnav">
<?php $this->load->view('common/nav-menu'); ?>
</header>
<!-- End Navigation Bar-->
<div class="wrapper">
<div class="container-fluid">

            <!-- <div class="desc-box">

                <div class="row">

                    <div class="col-sm-2">

                        <img src="<?php echo dashboard_icon; ?>plachold.png" style="width: 100%;">

                    </div>

                    <div class="col-sm-8">

                        <h6>

                            Sales Dashboard</h6>

                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>

                    </div>

                    <div class="col-sm-2">

                        <div class="text-center"><a href="#"><i class="fa fa-video-camera" aria-hidden="true"></i></a>

                        </div>

                    </div>

                </div>

            </div>-->

        </div>

    </div>



    <div class="wrapper">

        <div class="container-fluid">





            <div class="dashboard-header">

                <h1>Sales Dashboard</h1>

                <hr>

            </div>





            <!-- <div id="form" class="tabcontent">

        <h3>Form</h3>

        

    </div> -->





            <div class="row">

                <?php



                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '9')->where('submoduleid', '77')->where('submodule_access', '1')->get();

                if ($qry->num_rows() > 0) {

$role = $_SESSION['logged_in']['role']; 
if($role==10){
    $todaysdate = date('Y-m-d');
    $lastmonthdate = date("Y-m-d", strtotime("-30 day"));
    $user_id = base64_encode($_SESSION['logged_in']['user_id']); 


                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <!-- <a href="<?php echo page_url; ?>Leads"> -->
                        <a href="<?php echo page_url; ?>Search/globalfilter/<?php echo $lastmonthdate;?>/<?php echo $todaysdate;?>/<?php echo $user_id;?>/NA/NA/NA/NA/NA/NA">

                            <div class="report-box">
                                <div class="tool ">
                                    <p data-toggle="tooltip" data-placement="top" title="This section will allow you to add, view or edit the leads information."><i class="fa fa-info-circle" aria-hidden="true"></i></p>
                                </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>add_view_lead.png">

                                </div>

                                <p>View Lead </p>

                            </div>

                        </a>

                    </div>

<?php }else{?>
    <div class="col-sm-4 col-md-4 col-lg-2">

                        <!-- <a href="<?php echo page_url; ?>Leads"> -->
                        <a href="<?php echo page_url; ?>Search/globalfilter">

                            <div class="report-box">
                                <div class="tool ">
                                    <p data-toggle="tooltip" data-placement="top" title="This section will allow you to add, view or edit the leads information."><i class="fa fa-info-circle" aria-hidden="true"></i></p>
                                </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>add_view_lead.png">

                                </div>

                                <p>View Lead </p>

                            </div>

                        </a>

                    </div>
<?php }?>


                <?php } ?>


                <?php 

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid',9)->where('submoduleid',78)->where('submodule_access','1')->get();

                if($qry->num_rows()>0) {   ?>  
                        <div class="col-sm-4 col-md-4 col-lg-2">
                            <a href="<?php echo page_url;?>Leads/lead_discount_approval">
                                <div class="report-box">
                                    <div class="text-center">
                                        <img src="<?php echo dashboard_icon;?>payment_rec.png">
                                    </div>
                                    <p>LEAD QUOTATIONS FOR APPROVAL</p>
                                </div>
                            </a>
                        </div>
                        <?php } ?>

               

                <?php

                // $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '4')->where('submoduleid', '50')->where('submodule_access', '1')->get();
                    // if ($qry->num_rows() > 0) {
                ?>

                <!-- <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Assigned_lead">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>view_your_leads.png">

                                </div>

                                <p>View Your Leads </p>

                            </div>

                        </a>

                    </div> -->

                <?php // } 
                ?>

                  <div class="col-sm-4 col-md-4 col-lg-2">
                   
                        <a href="<?php echo page_url; ?>Leads/addnewlead/<?php echo base64_encode($_SESSION['logged_in']['user_id']);?>">

                            <div class="report-box">
                            <div class="tool ">
                                    <p data-toggle="tooltip" data-placement="top" title="With the help of this section, you will be able to import leads in bulk in the excel format."><i class="fa fa-info-circle" aria-hidden="true"></i></p>
                                </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>add_view_lead.png">

                                </div>

                                <p>Add New Lead</p>

                            </div>

                        </a>

                    </div>

                 <!--   <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>ExcelImport/excel_import">

                            <div class="report-box">
                            <div class="tool ">
                                    <p data-toggle="tooltip" data-placement="top" title="With the help of this section, you will be able to import leads in bulk in the excel format."><i class="fa fa-info-circle" aria-hidden="true"></i></p>
                                </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>import_lead.png">

                                </div>

                                <p>Import Bulk Lead</p>

                            </div>

                        </a>

                    </div> -->

                
               

                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '4')->where('submoduleid', '111')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <!-- <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Leads/remarketing_leads">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>leads_for_remarketing.png">

                                </div>

                                <p> Leads for Remarketing</p>

                            </div>

                        </a>

                    </div> -->

                <?php } ?>

                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '4')->where('submoduleid', '112')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <!-- <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Leads/lost_leads">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>leads_lost.png">

                                </div>

                                <p> Leads Lost</p>

                            </div>

                        </a>

                    </div> -->

                <?php } ?>



                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '4')->where('submoduleid', '71')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <!--  <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Leads/non_qualified_leads">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>non_qualified_leads.png">

                                </div>

                                <p> Non Qualified Leads</p>

                            </div>

                        </a>

                    </div> -->

                <?php } ?>





                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '4')->where('submoduleid', '108')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <!-- <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Leads/overall_non_qualified_leads">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>all_non_qualified_leads.png">

                                </div>

                                <p>ALL Non Qualified Leads </p>

                            </div>

                        </a>

                    </div> -->

                <?php } ?>


                
                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Distributor">
                            <div class="report-box">
                                <div class="text-center">
                                   <i class="fa fa-users" style="font-size:45px; color:#000;"></i>
                                </div>
                                <p>Add Distributor</p>
                            </div>
                        </a>
                    </div>
               

                
                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>/Distributor/distributor_list">
                            <div class="report-box">
                                <div class="text-center">
                                   <i class="fa fa-users" style="font-size:45px; color:#000;"></i>
                                </div>
                                <p>Your Distributors</p>
                            </div>
                        </a>
                    </div>
                




            </div>







            <!-- Footer -->





            <?php $this->load->view('common/footer'); ?>





            <!-- End Footer -->











        </div> <!-- end container -->





    </div>





    <!-- end wrapper -->

















    <!-- jQuery  -->





    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>





    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>





    <script src="<?php echo assets_url; ?>js/detect.js"></script>





    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>





    <script src="<?php echo assets_url; ?>js/waves.js"></script>





    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>











    <!-- Datatables-->





    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>





    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>





    <!-- Datatable init js -->





    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>





    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>





    <!-- App js -->





    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

















</body>





</html>