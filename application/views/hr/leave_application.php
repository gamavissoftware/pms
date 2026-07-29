<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Leave Application</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">

    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        .divheight {
            padding-top: 100px;
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
        <div class="container">

            <!-- Page-Title -->
            <div class="divheight hidden-xs"></div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">

                        <h4 class="page-title text-center">LEAVE APPLICATION FORM</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->


            <div class="row">
                <div class="col-xs-12">
                    <div class="card-box">
                        <form id="loginForm" method="post" action="<?php echo page_url; ?>Hr/leave_application/" enctype="multipart/form-data">
                            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                            <div class="row">
                                <?php
                                $first_name = $this->session->userdata['logged_in']['user_name'];
                                $last_name = $this->session->userdata['logged_in']['last_name'];

                                $department_id = $this->session->userdata['logged_in']['department_id'];
                                $role = $this->session->userdata['logged_in']['role'];

                                ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">User Name</label>
                                        <span id="error_username" style="color:red;">*</span>
                                        <input type="text" id="username" name="username" class="form-control" autocomplete="off" value="<?php echo ucfirst($first_name); ?> <?php echo ucfirst($last_name); ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Department</label>
                                        <span id="error_department" style="color:red;">*</span>
                                        <input type="text" id="department" name="department" class="form-control" autocomplete="off" value="<?php $query = $this->db->select('department')->from('departments')->where('department_id', $department_id)->get();
                                                                                                                                            foreach ($query->result() as $row) {
                                                                                                                                                echo $row->department;
                                                                                                                                            } ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Designation</label>
                                        <span id="error_designation" style="color:red;">*</span>
                                        <input type="text" id="designation" name="designation" class="form-control" autocomplete="off" value="<?php $query = $this->db->select('user_role')->from('user_role')->where('user_role_id', $role)->get();
                                                                                                                                                foreach ($query->result() as $row) {
                                                                                                                                                    echo $row->user_role;
                                                                                                                                                } ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">From</label>
                                        <span id="error_from" style="color:red;">*</span>
                                        <input type="text" id="from" name="from" class="form-control datepicker" autocomplete="off" value="" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">To</label>
                                        <span id="error_to" style="color:red;">*</span>
                                        <input type="text" id="to" name="to" class="form-control datepicker" onChange="gettotaldays();" autocomplete="off" value="" required>
                                    </div>
                                </div>

                                <script>
                                    function gettotaldays() {
                                        var date1 = $("#from").val();
                                        var date2 = $("#to").val();
                                        if (date1 !== '' && date2 !== '') {
                                            $.ajax({
                                                type: "post",
                                                url: "<?php echo page_url; ?>Sales/gettotaldays",
                                                data: "date1=" + date1 + "&date2=" + date2,
                                                success: function(data) {
                                                    $("#totaldays").val(data);
                                                }
                                            });
                                        }

                                    }
                                </script>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Total Days</label>
                                        <span id="error_going_to" style="color:red;">*</span>
                                        <input type="text" id="totaldays" name="totaldays" class="form-control" autocomplete="off" value="" readonly>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Phone No. while on leave</label>

                                        <input type="text" id="phone_number" name="phone_number" class="form-control" autocomplete="off" value="">
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Leave for</label>
                                        <span id="error_leave_for" style="color:red;">*</span>
                                        <select class="form-control" name="leave_for" id="leave_for" onchange="showdetail();" required>
                                            <option value="">Select Option</option>
                                            <option value="1">Full Day</option>
                                            <option value="2">Half Day</option>
                                            <option value="3">Short Leave</option>

                                        </select>
                                    </div>
                                </div>


                                <script>
                                    function showdetail() {
                                        var leave_for = $("#leave_for").val();
                                        if (leave_for == '3') {
                                            $("#fromtimediv").show();
                                            $("#totimediv").show();
                                            $("#from_time").attr("required", true);
                                            $("#to_time").attr("required", true);
                                        } else {
                                            $("#fromtimediv").hide();
                                            $("#totimediv").hide();
                                            $("#from_time").attr("required", false);
                                            $("#to_time").attr("required", false);
                                        }
                                    }
                                </script>
                                <div class="col-md-3" style="display:none" id="fromtimediv">
                                    <div class="form-group">
                                        <label>Duration Start Time </label>

                                        <select class="form-control" name="from_time" id="from_time">
                                            <option value="">Select</option>
                                            <option value="09:00 AM">09:00 AM</option>
                                            <option value="09:30 AM">09:30 AM</option>
                                            <option value="10:00 AM">10:00 AM</option>
                                            <option value="10:30 AM">10:30 AM</option>
                                            <option value="11:00 AM">11:00 AM</option>
                                            <option value="11:30 AM">11:30 AM</option>
                                            <option value="12:00 PM">12:00 PM</option>
                                            <option value="12:30 PM">12:30 PM</option>
                                            <option value="01:00 PM">01:00 PM</option>
                                            <option value="01:30 PM">01:30 PM</option>
                                            <option value="02:00 PM">02:00 PM</option>
                                            <option value="02:30 PM">02:30 PM</option>
                                            <option value="03:00 PM">03:00 PM</option>
                                            <option value="03:30 PM">03:30 PM</option>
                                            <option value="04:00 PM">04:00 PM</option>
                                            <option value="04:30 PM">04:30 PM</option>
                                            <option value="05:00 PM">05:00 PM</option>
                                            <option value="05:30 PM">05:30 PM</option>
                                            <option value="06:00 PM">06:00 PM</option>
                                            <option value="06:30 PM">06:30 PM</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3" style="display:none" id="totimediv">
                                    <div class="form-group">
                                        <label>Duration End Time</label>
                                        <select class="form-control" name="to_time" id="to_time">
                                            <option value="">Select</option>
                                            <option value="09:00 AM">09:00 AM</option>
                                            <option value="09:30 AM">09:30 AM</option>
                                            <option value="10:00 AM">10:00 AM</option>
                                            <option value="10:30 AM">10:30 AM</option>
                                            <option value="11:00 AM">11:00 AM</option>
                                            <option value="11:30 AM">11:30 AM</option>
                                            <option value="12:00 PM">12:00 PM</option>
                                            <option value="12:30 PM">12:30 PM</option>
                                            <option value="01:00 PM">01:00 PM</option>
                                            <option value="01:30 PM">01:30 PM</option>
                                            <option value="02:00 PM">02:00 PM</option>
                                            <option value="02:30 PM">02:30 PM</option>
                                            <option value="03:00 PM">03:00 PM</option>
                                            <option value="03:30 PM">03:30 PM</option>
                                            <option value="04:00 PM">04:00 PM</option>
                                            <option value="04:30 PM">04:30 PM</option>
                                            <option value="05:00 PM">05:00 PM</option>
                                            <option value="05:30 PM">05:30 PM</option>
                                            <option value="06:00 PM">06:00 PM</option>
                                            <option value="06:30 PM">06:30 PM</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Reason</label>
                                        <span id="error_going_to" style="color:red;">*</span>
										<textarea id="reason" name="reason" class="form-control" autocomplete="off"></textarea>
                                        
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group pull-right">
                                        <input type="submit" id="save" class="btn btn-info" value="Submit">
                                    </div>
                                </div>

                            </div>
                        </form>

                        <!-- end row -->
                    </div> <!-- end ard-box -->
                </div><!-- end col-->

            </div>
            <!-- end row -->
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

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            $("#save").click(function() {
                var employee_name = $("#employee_name").val();
                if (employee_name == '') {
                    $("#error_employee_name").html('Required!');
                }
                var material_out_date = $("#material_out_date").val();
                if (material_out_date) {
                    $("#error_material_out_date").html('Required!');
                }
                var out_time = $("#out_time").val();
                if (out_time == '') {
                    $("#error_out_time").html('Required!');
                }

                var item_name = $("#item_name").val();
                if (item_name == '') {

                    $("#error_item_name").html('Required!');
                }
                var to = $("#to").val();
                if (to == '') {

                    $("#error_to").html('Required!');
                }
                var job_card_number = $("#job_card_number").val();
                if (job_card_number == '') {

                    $("#error_job_card_number").html('Required!');
                }
                var weight = $("#weight").val();
                if (weight == '') {

                    $("#error_weight").html('Required!');
                }
                var qty = $("#qty").val();
                if (qty == '') {

                    $("#error_qty").html('Required!');
                }
                var challan_no = $("#challan_no").val();
                if (challan_no == '') {

                    $("#error_challan_no").html('Required!');
                }

                var out_from = $("#out_from").val();
                if (out_from == '') {

                    $("#error_out_from").html('Required!');
                }
                if (employee_name == '' || material_out_date == '' || out_time == '' || item_name == '' || to == '' || job_card_number == '' || weight == '' || qty == '' || challan_no == '' || out_from == '') {

                    return false;
                }

            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            startDate: new Date()
        });
    </script>

</body>

</html>