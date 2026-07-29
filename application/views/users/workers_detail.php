<?php
$a = $this->db->select('a.*,b.rack_location')->from('workers_list a')->join('store_rack_location b', 'a.location=b.id')->where('a.id', $this->uri->segment(3))->get();
if ($a->num_rows() == 0) {
    echo "Invalid Request";
    exit;
} else {
    foreach ($a->result() as $row);

    $dob = date('Y-m-d', strtotime($row->dob));
    if (!empty($dob)) {
        $birthdate = new DateTime($dob);
        $today   = new DateTime('today');
        $age = $birthdate->diff($today)->y;
    } else {
        $age = 0;
    }
}

?>
<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="">

    <meta name="author" content="<?php echo copyright; ?>">



    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">



    <title><?php echo sitetitle; ?> Factory Workers List</title>



    <!-- Table Responsive css -->

    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>

    <!-- DataTables -->

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>



    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/worker_detail.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />



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

    <style>
        table.manglesh thead th {

            background: <?php echo $LOGO->colorcode; ?>;

            color: #fff;

            font-weight: bold;

            text-align: center;

        }

        table.manglesh tbody td {
            text-align: center;
        }
    </style>

</head>





<body>





    <!-- Navigation Bar-->

    <header id="topnav">

        <?php $this->load->view('common/nav-menu'); ?>

    </header>

    <!-- End Navigation Bar-->



    <?php $this->load->view('common/info-section.php'); ?>

    <div class="wrapper">

        <div class="container">



            <!-- Page-Title -->



            <div class="row">
                <div class="col-sm-12">
                    <div class="select-emp">
                        <select name="worker_name" id="worker_name" onchange="getworkerdetails(this.value);">
                            <?php
                            $rooow = $this->db->select('id,name')->from('workers_list')->get();
                            if ($rooow->num_rows() > 0) {
                                foreach ($rooow->result() as $rooww) {
                            ?>
                                    <option value="<?php echo $rooww->id; ?>" <?php if ($rooww->id == $this->uri->segment(3)) { ?> selected <?php } ?>><?php echo $rooww->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="">


                        <div class="row">
<div class="col-sm-12">
    <div class="emp-detail">
    <h6>Worker Details</h6>
                                    <hr>
    </div>
</div>
                            <div class="col-sm-5">
                                <div class="emp-detail">
                                   
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="text-center">
                                                <div class="emp-image">
                                                    <img src="<?php echo sfdocument; ?>worker_docs/passport_image/<?php echo $row->passport_image; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-7 ">
                                            <h5><?php echo $row->name; ?></h5>
                                            <?php
                                            if ($row->status == 1) { ?>
                                                <div class="act-btn">
                                                    <p>ACTIVE</p>
                                                </div>
                                            <?php } else { ?>

                                                <div class="act-btn1">
                                                    <p>INACTIVE</p>
                                                </div>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="emp-detail">
                                  
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p><b>Name</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo $row->name; ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p><b>Salary</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo $row->salary; ?>/-</p>
                                                </div>
                                                </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p><b>DOJ</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo date('d-M-Y', strtotime($row->doj)); ?></p>
                                                </div>
                                                </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p><b>D/O Exit</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p>Currently Working</p>
                                                </div>
                                                <!-- <div class="col-sm-6">
                                            <p><b>Paid till</b></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p>1 June, 2022</p>
                                        </div> -->
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p><b>Mobile No.</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo $row->mobile; ?></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><b>Age</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo $age; ?> Years</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><b>Total Experience</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p>&nbsp;</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><b>Current Location</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><?php echo $row->rack_location; ?></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p><b>Last Paid</b></p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p>&nbsp;</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="row">
                            <div class="col-sm-4 ">
                                <div class="emp-detail">
                                <h6>Salary Increment</h6>
                                <hr>
                                <table>
                                    <tr>
                                        <th>Increment Date</th>
                                        <th>Amount</th>
                                        <th>Salary</th>
                                    </tr>
                                    <?php
                                    $as = $this->db->select('*')->from('worker_account_history')->where('log_type', 1)->where('workerid', $this->uri->segment(3))->get();
                                    if ($as->num_rows() > 0) {
                                        foreach ($as->result() as $data) {
                                    ?>
                                            <tr>
                                                <td><?php echo date('d-M-Y', strtotime($data->addedOn)); ?></td>
                                                <?php
                                                $sal = explode('from', $data->logs);
                                                $ssal = explode('to', $sal[1]);

                                                $new = (float)$ssal[1];
                                                $old = (float)$ssal[0];
                                                $increase = $new - $old;
                                                ?>
                                                <td><?php echo $increase; ?></td>
                                                <td><?php echo $ssal[1]; ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>

                                        <tr>
                                            <td colspan="3">No Records Found</td>

                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            </div>
                            <div class="col-sm-8">
                            <div class="emp-detail">
                                <h6>History of <?php echo $row->name; ?> in Mehta Cosmetics</h6>
                                <hr>
                                <table>
                                    <tr>

                                        <th>Date</th>
                                        <th>Logs</th>
                                        <th>All time Remarks</th>
                                    </tr>
                                    <?php
                                    $as = $this->db->select('*')->from('worker_account_history')->where('log_type', 0)->where('workerid', $this->uri->segment(3))->get();
                                    if ($as->num_rows() > 0) {
                                        foreach ($as->result() as $data) {
                                    ?>
                                            <tr>

                                                <td><?php echo date('d-M-Y', strtotime($data->addedOn)); ?></td>
                                                <td><?php echo $data->logs; ?></td>
                                                <td><?php echo $data->logs; ?></td>
                                            </tr>
                                    <?php }
                                    }else{ ?>


                                        <tr>
                                            <td colspan="3">No Records Found</td>

                                        </tr>
                                        
                                    <?php } ?>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- end page title end breadcrumb -->

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

















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

    <script type="text/javascript">
        function getworkerdetails(id) {
            document.location = "<?php echo page_url; ?>/User/workers_detail/" + id;
            return true;
        }
    </script>



</body>

</html>