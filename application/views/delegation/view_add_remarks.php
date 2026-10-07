<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegation Task Remark</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-header {
            background: linear-gradient(135deg, #4e73df, #224abe);
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin-bottom: 20px;
        }

        .page-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .card-modern {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .table-modern thead {
            background: #4e73df;
            color: #fff;
        }

        .table-modern th {
            border: none !important;
            font-weight: 500;
        }

        .table-modern td {
            vertical-align: middle !important;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: none;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.2);
        }

        .btn-modern {
            background: linear-gradient(135deg, #1cc88a, #17a673);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-modern:hover {
            opacity: 0.9;
        }

        label {
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #e6e6e6;
            /* whatsapp bg */
            border-radius: 10px;
        }

        /* message row */
        .chat-msg {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-end;
        }

        /* LEFT (other user) */
        .chat-left {
            justify-content: flex-start;
        }

        /* RIGHT (me) */
        .chat-right {
            justify-content: flex-end;
        }

        /* profile image */
        .chat-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 8px;
        }

        .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* bubble */
        .chat-bubble {
            min-width: 300px;
            max-width: 70%;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            position: relative;
        }

        /* left bubble */
        .chat-left .chat-bubble {
            background: #fff;
        }

        /* right bubble */
        .chat-right .chat-bubble {
            background: #dcf8c6;
        }

        /* name */
        .chat-name {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        /* time */
        .chat-time {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
            text-align: right;
        }

        /* attachment */
        .chat-attach a {
            font-size: 12px;
            color: #007bff;
        }


        .switch {
            position: relative;
            display: inline-block;
            width: 45px;
            height: 20px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
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

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right" style="margin-top:30px">
                        </div>

                        <h4 class="page-title text-center">DELEGATED TASK CONVERSATION</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card_box table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh">
                            <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>TIMESTAMP</th>
                                    <th>DELEGATED BY</th>
                                    <th>DELEGATED TO</th>
                                    <th>CASE NO</th>
                                    <th>WORK DELEGATED</th>
                                    <th>ATTACHMENT</th>
                                    <th>EMAIL URL</th>
                                    <th>2ND DATE</th>
                                    <th>3RD DATE</th>
                                    <th>WORK DUE DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card_box">
                        <form id="add_task">
                            <input type="hidden" name="task_id" id="task_id" value="<?php echo $this->uri->segment(3); ?>">
                            <div class="form-group">
                                <label for="">Reply / Comment</label>
                                <span style="color: red;">*</span>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Attachment (if Any)</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Send Comment</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card_box">
                        <h4>Complete Conversation</h4>
                        <div class="chat-box" id="remarks_list"></div>
                        <!-- <div id="remarks_list" style="max-height:250px; overflow:auto;"></div> -->
                    </div>
                </div>
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
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({});
            $('.select3').select2({});
            $('.select4').select2({});
            $('#example').dataTable({
                "bProcessing": false,
                fixedHeader: true,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>Delegation/user_wise_by_delegated_task_list/<?php echo $this->uri->segment(3) ?>",
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'timestamp'
                    },
                    {
                        mData: 'delegated_by'
                    },
                     {
                        mData: 'delegated_to'
                    },
                    {
                        mData: 'caseno'
                    },
                    {
                        mData: 'task'
                    },
                    {
                        mData: 'attachment'
                    },
                    {
                        mData: 'email_url'
                    },
                    {
                        mData: 'second_date'
                    },
                    {
                        mData: 'third_date'
                    },
                    {
                        mData: 'delegated_date'
                    }


                ]
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            let task_id = $("#task_id").val();

            // ✅ Page load pe remarks load karo
            if (task_id && task_id != '') {
                loadRemarks(task_id);
            } else {
                console.log("Task ID missing");
            }

            // ✅ Form Submit
            $("#add_task").on("submit", function(e) {
                e.preventDefault();


                let remarks = $("#remarks").val();



                if (remarks.trim() == "") {
                    toastr.error("Please enter remarks");
                    return false;
                }

                let formData = new FormData(this);

                $.ajax({
                    url: "<?php echo page_url ?>Delegation/save_response_ajax",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",

                    success: function(res) {
                        if (res.status == "success") {

                            toastr.success(res.message);

                            $("#add_task")[0].reset();

                            // ✅ Table reload
                            $('#example').DataTable().ajax.reload();

                            // ✅ IMPORTANT: remarks reload karo
                            loadRemarks(task_id);

                        } else {
                            toastr.error(res.message);
                        }
                    },

                    error: function() {
                        toastr.error("Something went wrong!");
                    }
                });
            });

        });


        // ✅ LOAD REMARKS FUNCTION
        function loadRemarks(task_id) {
            $("#remarks_list").html("Loading...");

            $.ajax({
                url: "<?php echo page_url ?>Delegation/get_task_remarks",
                type: "POST",
                data: {
                    task_id: task_id
                },

                success: function(res) {
                    $("#remarks_list").html(res);

                    let box = document.getElementById("remarks_list");
                    box.scrollTop = box.scrollHeight;
                },
                error: function() {
                    $("#remarks_list").html("Error loading remarks");
                }
            });
        }
    </script>

    <script>
function copyUrl(id) {
    let copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999); // mobile support

    document.execCommand("copy");

    toastr.success("URL copied!");
}
</script>


</body>

</html>
