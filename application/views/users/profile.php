<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> IT Assets</title>
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
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <style>
        .label {
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: 700;
            font-size: 10px !important;
        }
    </style>
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="container-fluid">


            <div class="row">
                <div class="col-sm-12">
                    <div class="bg-picture">
                        <div class="profile-info-name">
                            

                            <div class="col-sm-12 card-box" style="margin-top: 40px;">
                                <div class="text-center">
                                    <div class="profile-pic">
                                        <?php $query = $this->db->select('a.*,b.user_role_id, b.user_role,c.business_loc_id, c.company_name, c.address as company_address,d.department_id, d.department')->from('system_users a')->join('user_role b', 'a.user_role_id=b.user_role_id', 'left')->join('business_location c', 'a.business_location=c.business_loc_id', 'left')->join('departments d', 'a.department_id=d.department_id', 'left')->where('a.user_id', $this->uri->segment(3))->get();
                                        foreach ($query->result() as $userinfo)

                                            $profile_image = $userinfo->profile_image;
                                        if ($profile_image) { ?>

                                            <img src="<?php echo user_profile; ?><?php echo $profile_image; ?>" alt="user-img" class="img-thumbnail">
                                        <?php } else { ?>
                                            <img src="http://crm.packingtest.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-thumbnail"> <?php } ?>
                                    </div>
                                </div>
                                <div class="profile-info-detail">
                                    <h3 class="m-t-0 m-b-0" style="text-align:center;"><?php echo $userinfo->first_name . " " . $userinfo->last_name; ?></h3>
                                    <p class="text-muted m-b-20" style="text-align:center;"><i></i> <?php echo $userinfo->user_role; ?></p>

                                </div>
                                <hr style="margin-top: 5px; margin-bottom: 5px;">
                                   <!--------------------------table--------------------------->

                                   <table class="table table-bordered">
                                        <tr>
                                            <td width="15%;">Business Location</td>
                                            <td width="35%;"><strong><?php echo $userinfo->company_name; ?> (<?php echo $userinfo->company_address; ?>)</strong></td>
                                            <td width="15%;">Department</td>
                                            <td width="35%;"><strong><?php echo $userinfo->department; ?></strong></td>
                                        </tr>

                                        <tr>
                                            <td width="15%;">Email ID</td>
                                            <td width="35%;"><strong><?php echo $userinfo->email; ?></strong></td>
                                            <td width="15%;">Contact Number</td>
                                            <td width="35%;"><strong><?php echo $userinfo->contact_number; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">Alternate Number</td>
                                            <td width="35%;"><strong><?php echo $userinfo->alternate_number; ?></strong></td>
                                            <td width="15%;">Father Name</td>
                                            <td width="35%;"><strong><?php echo $userinfo->father_name; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">Mother Name</td>
                                            <td width="35%;"><strong><?php echo $userinfo->mother_name; ?></strong></td>
                                            <td width="15%;">Date of Birth</td>
                                            <td width="35%;"><strong><?php echo $userinfo->date_of_birth; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">Date of Joining</td>
                                            <td width="35%;"><strong><?php echo $userinfo->date_of_joining; ?></strong></td>
                                            <td width="15%;">Qualification</td>
                                            <td width="35%;"><strong><?php echo $userinfo->qualification; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">address</td>
                                            <td width="35%;"><strong><?php echo $userinfo->address; ?></strong></td>
                                            <td width="15%;">PAN Card</td>
                                            <td width="35%;"><strong><?php echo $userinfo->pancard; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">Aadhar Number</td>
                                            <td width="35%;"><strong><?php echo $userinfo->aadhar_number; ?></strong></td>
                                            <td width="15%;">Blood Group</td>
                                            <td width="35%;"><strong><?php echo $userinfo->blood_group; ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="15%;">Spouce Name Card</td>
                                            <td width="35%;"><strong><?php echo $userinfo->spouce_name; ?></strong></td>
                                          
                                        </tr>
                                        
                                      
                                </table>

                                <!--------------------------table--------------------------->
                            </div>
                            

                            
                        </div>

                    </div>
                </div>

            </div>

            <!--/ meta -->






            <!-- end row -->
            <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                        </div>

                        <h4 class="page-title">List All IT Assets Assigned To <?php echo $userinfo->first_name . " " . $userinfo->last_name; ?></h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Business Location </th>
                                    <th>Department</th>
                                    <th>Asset Type</th>
                                    <th>Brand Name</th>
                                    <th>Purchase Date</th>
                                    <th>Action</th>

                                </tr>
                            </thead>

                        </table>
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

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>



    <script>
        $(document).ready(function() {
            $('#example').dataTable({
                "bProcessing": true,
                "pageLength": 500,
                "pagination": true,
                "sAjaxSource": "<?php echo page_url; ?>User/IT_item_list/<?php echo $this->uri->segment(3); ?>",
                "aoColumns": [{
                        mData: 'sr_no'
                    },
                    {
                        mData: 'business_location'
                    },
                    {
                        mData: 'location'
                    },
                    {
                        mData: 'asset_type'
                    },
                    {
                        mData: 'brand_name'
                    },
                    {
                        mData: 'purchase_date'
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
            $('.select2').select2({});
            $('.select3').select2({});
            $('.select4').select2({});
            $("#save").click(function() {
                var business_loc = $("#business_loc").val();
                if (business_loc == '') {
                    $("#error_business_loc").html('Required!');
                }
                var item_name = $("#item_name").val();
                if (item_name == '') {
                    $("#error_item_name").html('Required!');
                }
                var location = $("#location").val();
                if (location == '') {
                    $("#error_location").html('Required!');
                }

                var status = $("#status").val();
                if (status == '') {

                    $("#error_status").html('Required!');
                }


                if (business_loc == '' || item_name == '' || location == '' || status == '') {

                    return false;
                }

            });
        });
    </script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
</body>

</html>