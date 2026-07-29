<?php $user_id = $this->session->userdata['logged_in']['user_id'];
$DI = &get_instance();
$DI->load->model('Dashboard_model');
// $settingval = $DI->Dashboard_model->getsettings();
// if (count($settingval) > 0) {
//     $mode = $settingval['mode'];
// } else {
//     $mode = 1;
// }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?>
        Sales Master </title>
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



    <!-- <div class="wrapper">

        <div class="container-fluid">

            <div class="desc-box">

                <div class="row">

                    <div class="col-sm-2">

                        <img src="<?php echo dashboard_icon; ?>salestool_icon.jpg" style="width: 100%;">

                    </div>

                    <div class="col-sm-8">

                        <h6>

                            Sales Master </h6>

                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>

                    </div>

                    <div class="col-sm-2">

                    

                    </div>

                </div>

            </div>

        </div>

    </div> -->



    <div class="wrapper">

        <div class="container-fluid">





            <div class="dashboard-header">

                <h1>Sales Master </h1>

                <hr>

            </div>





            <!-- <div id="form" class="tabcontent">

        <h3>Form</h3>

        

    </div> -->





            <div class="row">

                <?php



                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '31')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/Lead_Type/add_lead_source">

                            <div class="report-box">
                                <div class="tool ">
                                    <p data-toggle="tooltip" data-placement="top" title="This section of sales dashboard will allow you to keep a check on the lead source."><i class="fa fa-info-circle" aria-hidden="true"></i></p>

                                </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>source_lead.png">

                                </div>

                                <p> Lead Source</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>



                <?php
                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '32')->where('submodule_access', '1')->get();

                if ($qry->num_rows() > 0) { ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Master/Lead_Type/add_lead_type">
                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can add, edit or view different lead stages of your leads."><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>stage_lead.png">

                                </div>

                                <p> Lead Stage</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

                <?php
                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '32')->where('submodule_access', '1')->get();

                if ($qry->num_rows() > 0) { ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">
                        <a href="<?php echo page_url; ?>Master/Lead_Type/add_lead_type_spares">
                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can add, edit or view different lead stages of your leads."><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <i class="fa fa-cog" aria-hidden="true" style="font-size:40px"></i>

                                </div>

                                <p> Spares Lead Stage</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '76')->where('submodule_access', '1')->get();

                if ($qry->num_rows() > 0) {
                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/Customer_type/add_customer_type">

                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can easily add/edit different customers of different industry type."><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>type_customer.png">

                                </div>

                                <p> Customer Type</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

               



                <!-- <?php



                        $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '14')->where('submodule_access', '1')->get();



                        if ($qry->num_rows() > 0) {



                        ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/Employee_Target/add_employee_logout_time/">

                            <div class="report-box">

                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>holiday.png">

                                </div>

                                <p>Set Logout Time</p>

                            </div>

                        </a>

                    </div>

                <?php } ?> -->





                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '127')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/Supplier/document_standards">

                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title=" You can easily upload or edit standard document files with the help of this section."><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>standard_documents.png">

                                </div>

                                <p>Standard Documents</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

                 <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '37')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/User_management/email_template">

                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="Add Email template to share with the exhibition Customers."><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <i class="fa fa-envelope" style="font-size:40px"></i>

                                </div>

                                <p>Email Body Message for Exhibition</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

                
                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '33')->where('submodule_access', '1')->get();
                if ($qry->num_rows() > 0) {
                ?>
                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>Master/Customer_type/add_reason">

                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can create templates for reasons that you can use for unqualified your leads. "><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>unqualified.png">

                                </div>

                                <p>UNQUALIFIED REASON</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>

                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '34')->where('submodule_access', '1')->get();
                if ($qry->num_rows() > 0) {
                ?>
                    <div class="col-sm-4 col-md-4 col-lg-2">

                        <a href="<?php echo page_url; ?>FMS/instruments">

                            <div class="report-box">
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can create templates for reasons that you can use for unqualified your leads. "><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>technical_support.png">

                                </div>

                                <p>MACHINES</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>



                          <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '80')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) {



                ?>

                    <div class="col-sm-4 col-md-4 col-lg-2"  style="display: none;">

                        <a href="<?php echo page_url; ?>Master/Credit_period">

                            <div class="report-box" >
                            <div class="tool ">
                                        <p data-toggle="tooltip" data-placement="top" title="You can create templates for reasons that you can use for unqualified your leads. "><i class="fa fa-info-circle" aria-hidden="true" ></i></p>

                                    </div>
                                <div class="text-center">

                                    <img src="<?php echo dashboard_icon; ?>unqualified.png">

                                </div>

                                <p>GRACE PERIOD</p>

                            </div>

                        </a>

                    </div>

                <?php } ?>


                       <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '108')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) { ?>
                <div class="col-sm-4 col-md-4 col-lg-2" style="display: none;">
                <a href="<?php echo page_url; ?>Leads/typestatus">
                <div class="report-box">
                <div class="text-center">
                <img src="<?php echo dashboard_icon; ?>stage_lead.png">
                </div>
                <p>TYPE SALE APPROVAL STATUS</p>
                </div>
                </a>
                </div>
                <?php } ?>

                 <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '57')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) { ?>
                <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url; ?>Spares_master">
                <div class="report-box">
                <div class="text-center">
                <img src="<?php echo dashboard_icon; ?>stage_lead.png">
                </div>
                <p>SPARE MASTER MANAGEMENT</p>
                </div>
                </a>
                </div>
                <?php } ?>


                <?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '67')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) { ?>
                <div class="col-sm-4 col-md-4 col-lg-2">
                <a href="<?php echo page_url; ?>Customer_master_control">
                <div class="report-box">
                <div class="text-center">
                <img src="<?php echo dashboard_icon; ?>stage_lead.png">
                </div>
                <p>PMS CUSTOMER MASTER (MARKETING/SPARES)</p>
                </div>
                </a>
                </div>
                <?php } ?>

<?php

                $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where('submoduleid', '68')->where('submodule_access', '1')->get();



                if ($qry->num_rows() > 0) { ?>
                 <div class="col-sm-4 col-md-4 col-lg-2">

                    <a href="<?php echo page_url; ?>Masters/manage_deployment_types">

                        <div class="report-box">
                            <div class="tool ">
                                <p data-toggle="tooltip" data-placement="top" title="Manage deployment type options used in service engineer planning and scheduling."><i class="fa fa-info-circle" aria-hidden="true"></i></p>

                            </div>
                            <div class="text-center">

                                <i class="fa fa-sliders" aria-hidden="true" style="font-size:40px"></i>

                            </div>

                            <p> Deployment Type Master</p>

                        </div>

                    </a>

                </div>
            <?php }?>



            </div>            <!-- Footer -->





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
