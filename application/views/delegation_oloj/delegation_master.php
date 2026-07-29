<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title>Prestogroup Delegation Master</title>

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
    <!-- End Navigation Bar-->
    <div class="wrapper">
        <div class="container-fluid">
            <div class="desc-box">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?php echo dashboard_icon; ?>add Delegation Master list.jpg" style="    width: 100%;">
                    </div>
                    <div class="col-sm-8">
                        <h6>Delegation Master List</h6>
                        <p class="pagedescriptionfont">Here you will get a list of delegation masters who will assign tasks to different users. The admin members will easily keep a check on the delegated task members that will be given tasks on a daily or weekly basis.
</p>
                    </div>
                    <div class="col-sm-2">
                        <div class="text-center"><a href="https://vimeo.com/prestogroup/review/593691281/a0007f6e39?sort=alphabetical&direction=asc
">
                                <img src="<?php echo dashboard_icon; ?>header_icon.png" style="width: 40%; margin-top: 35px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <div class="container-fluid">

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right" style="margin-top:30px">
                            <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Add Delegation Master</button>

                        </div>

                        <h4 class="page-title">Add Delegation Master list</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <?php echo $this->session->flashdata('message'); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>BUSINESS LOCATION </th>
                                    <th>ASSIGNED TO</th>
                                    <th>WHAT</th>
                                    <th>WHEN</th>
                                    <th>VIDEO</th>
                                    <th>EDIT</th>
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
                <form id="loginForm" method="post" action="<?php echo page_url; ?>Delegation/delegation_master">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Add Delegation Master</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">BUSINESS LOCATION</label>
                                            <span id="error_business_loc" style="color:red;">*</span>
                                            <select class="form-control" id="business_loc" name="business_loc">
                                                <option value="">--SELECT BUSINESS LOCATION--</option>
                                                <?php
                                                $this->db->select('a.company_name, a.business_loc_id,a.state_id, a.city_id,b. 	state_id,b.state_name,c.city_id,c.city_name')->from('business_location a')->join('states b', 'a.state_id=b.state_id', 'left')->join('cities c', 'a.city_id=c.city_id', 'left')->where('business_loc_status', '1');
                                                $this->db->order_by('a.company_name', 'asc');
                                                $query = $this->db->get();
                                                $res = $query->result();
                                                foreach ($res as $row) {
                                                ?>
                                                    <option value="<?php echo $row->business_loc_id; ?>">(<?php echo strtoupper($row->state_name); ?>, <?php echo strtoupper($row->city_name); ?>)</option>
                                                <?php } ?>
                                            </select>
                                            <script type="text/javascript">
                                                $("#business_loc").change(function() {
                                                    var business_loc = $("#business_loc").val();
                                                    $.ajax({
                                                        type: "post",
                                                        url: "<?php echo page_url; ?>Delegation/user_list",
                                                        data: "business_loc=" + business_loc,
                                                        success: function(data) {
                                                            $("#user_id").html(data);
                                                        }
                                                    });

                                                    $.ajax({
                                                        type: "post",
                                                        url: "<?php echo page_url; ?>Delegation/user_list",
                                                        data: "business_loc=" + business_loc,
                                                        success: function(data) {
                                                            $("#reporting_head").html(data);
                                                        }
                                                    });
                                                });
                                            </script>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">WHO</label>
                                            <span id="error_user_id" style="color:red;">*</span>
                                            <select class="form-control select3" id="user_id" name="user_id">
                                                <option value="">--SELECT WHO--</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">WHAT</label>
                                            <span id="error_what" style="color:red;">*</span>
                                            <input class="form-control" type="text" name="what" id="what" value="">

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">WHEN</label>
                                            <span id="error_when" style="color:red;">*</span>
                                            <input class="form-control" type="text" name="when" id="when" value="">

                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Form Video Link</label>

                                            <input class="form-control" type="text" name="form_video_link" id="form_video_link" value="">

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">Dashboard Video Link</label>

                                            <input class="form-control" type="text" name="dashboard_video" id="dashboard_video" value="">

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
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script> -->
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

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script>
        // Time Picker
        jQuery('#timepicker').timepicker({
            defaultTIme: false
        });
        jQuery('#timepicker4').timepicker({
            defaultTIme: false
        });
        jQuery('#timepicker2').timepicker({
            showMeridian: false
        });
        jQuery('#timepicker3').timepicker({
            minuteStep: 15
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({});
            $('.select3').select2({});
            $('.select4').select2({});
            $('#example').dataTable({
                "bProcessing": false,
                "pagination": true,
                fixedHeader: true,
                "sAjaxSource": "<?php echo page_url; ?>Delegation/delegation_list/",
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'business_location'
                    },
                    {
                        mData: 'assigned_to'
                    },
                    {
                        mData: 'what_to_do'
                    },
                    {
                        mData: 'when_to_do'
                    },
                    {
                        mData: 'video'
                    },
                    {
                        mData: 'edit'
                    }

                ]
            });
        });
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            $("#save").click(function() {

                var business_loc = $("#business_loc").val();
                if (business_loc == '') {
                    $("#error_business_loc").html('Required!');
                }
                var user_id = $("#user_id").val();
                if (user_id == '') {
                    $("#error_user_id").html('Required!');
                }


                var what = $("#what").val();
                if (what == '') {

                    $("#error_what").html('Required!');
                }


                var when = $("#when").val();
                if (when == '') {

                    $("#error_when").html('Required!');
                }





                if (business_loc == '' || user_id == '' || what == '' || when == '') {
                    return false;
                }

            });
        });
    </script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>

</html>