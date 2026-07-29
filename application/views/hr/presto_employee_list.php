<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title>Prestogroup</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
</head>
</head>

<body>
    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="desc-box">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?php echo dashboard_icon; ?>presto-employee-record.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>Employees Dashboard</h6>
                        <p class="pagedescriptionfont">With the help of this dashboard, you can easily add new users and the details of factory employees.

</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="#">
                                <!-- <i class="fa fa-video-camera" aria-hidden="true"></i>  -->
                                <!-- <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 50px;"> -->
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Navigation Bar-->
    <div class="wrapper">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Presto Employees Dashboard</h4>
                    </div>
                    <div class="btn-group pull-right">
                        <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add New Employee</button>
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
                                    <th>SR NO.</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>EMPLOYEE CODE</th>
                                    <th>REPORTING MANAGER</th>
                                    <th>FACTORY</th>
                                    <th>EMPLOYEE FOR</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url; ?>Hr/add_new_employee/" enctype="multipart/form-data">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Add New Employee</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Employee Name</label>
                                            <span id="error_employee_name" style="color:red;"></span>
                                            <input type="text" class="form-control" name="employee_name" id="employee_name" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Employee Code</label>
                                            <span id="error_employee_code" style="color:red;"></span>
                                            <input type="text" class="form-control" name="employee_code" id="employee_code" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Reporting Manager</label>
                                            <span id="error_hod_name" style="color:red;"></span>
                                            <select class="form-control" name="hod_name" id="hod_name">
                                                <option value="">Select HOD</option>
                                                <?php
                                                $query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('hide_profile', '0')->where('user_status', '1')->get();
                                                foreach ($query->result() as $row) { ?>
                                                    <option value="<?php echo $row->user_id; ?>"><?php echo $row->first_name . " " . $row->last_name; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Factory</label>
                                            <span id="error_factory" style="color:red;"></span>
                                            <select class="form-control" name="factory">
                                                <option value="A WING">A WING</option>
                                                <option value="B WING">B WING</option>
                                                <option value="I-10">I-10</option>
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">EMPLOYEE FOR</label>
                                            <span id="error_factory" style="color:red;"></span>
                                            <select class="form-control" name="employee_for">
                                                <option value="1">PRESTOGROUP EMPLOYEE</option>
                                                <option value="2">TESTRONIX EMPLOYEE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <input type="submit" id="save" class="btn btn-info" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal -->
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
    <!-- <script src="<?php echo assets_url; ?>js/fastclick.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/waves.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/wow.min.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script> -->

    <!-- Datatables-->
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script> --> 
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script> -->
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script> -->
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>
    <script>
        $(document).ready(function() {

            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Hr/presto_employee_record_list/",
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'employee_name'
                    },
                    {
                        mData: 'employee_code'
                    },
                    {
                        mData: 'hod'
                    },
                    {
                        mData: 'factory'
                    },
                    {
                        mData: 'for_emp'
                    },
                    {
                        mData: 'edit'
                    }


                ]
            });
        });
    </script>
    <script>
        function changestatus(i) {
            var status = $('#markas' + i).val();
            var recordid = $('#recordid' + i).val();
            $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Hr/update_leave_status",
                data: "status=" + status + "&recordid=" + recordid,
                success: function(data) {
                    alert(data);

                }
            });
        }
    </script>
    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>

</html>