<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php //echo copyright; 
                                    ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php //echo sitetitle; 
            ?>Ledger Report</title>
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
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
    //$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    //foreach($q->result() as $LOGO);
    ?>

    <style>
        table.manglesh thead th {


            background: red;


            color: #fff;


            font-weight: bold;


        }





        #pageloader {


            background: rgba(255, 255, 255, 0.8);


            display: none;


            height: 100%;


            position: fixed;


            width: 100%;


            z-index: 9999;


        }


        #pageloader img {


            left: 50%;


            margin-left: -32px;


            margin-top: -32px;


            position: absolute;


            top: 50%;


        }

        .ledger_table {
            width: 100%;
            border: 1px solid lightgray;
            font-family: 'Montserrat', sans-serif;
            margin-top: 20px;
        }

        .ledger_table th {
            padding: 5px;
            color: black;
            background-color: whitesmoke;
            font-weight: 600;
        }

        .ledger_table td {
            padding: 5px;
            color: black;
        }
    </style>


</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>


    </header>


    <!-- End Navigation Bar-->

    <div class="wrapper ">
        <div class="container-fluid">
            <div class="dashboard-header">
                <h1>Ledger Report</h1>
                <hr>
            </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <table class="ledger_table" align="center" border="1" rule="all">
                        <tr>
                            <th width="15%">Name</th>
                            <td width="35%">XYZ</td>
                            <th width="15%">Email</th>
                            <td width="35%">info@example.com</td>
                        </tr>
                        <tr>
                            <th width="15%">Name</th>
                            <td width="35%">XYZ</td>
                            <th width="15%">Email</th>
                            <td width="35%">info@example.com</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="row mt-4">
                
                <!-- <div class="col-sm-1"></div> -->
                <div class="col-sm-12">
                    <table class="ledger_table" align="center" border="1" rule="all">
                        <tr>
                            <th width="5%">Date</th>
                            <th width="15%">Journal Entry #</th>
                            <th width="35%">Description</th>
                            <th width="15%">Debit</th>
                            <th width="15%">Credit</th>
                            <th width="15%">Balance</th>
                        </tr>
                        <tr>
                            <td>13-Feb</td>
                            <td>#1</td>
                            <td>Purchase Inventory</td>
                            <td></td>
                            <td></td>
                            <td>80,000</td>
                        </tr>
                        <tr>
                            <td>13-Feb</td>
                            <td>#2</td>
                            <td>Cash received from customer</td>
                            <td>6000</td>
                            <td></td>
                            <td>86,000</td>
                        </tr>
                        <tr>
                            <th colspan="5" style="text-align:center;">Total Value</th>
                            <th>86,000</th>
                        </tr>
                    </table>
                </div>
                <!-- <div class="col-sm-1"></div> -->
            </div>
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




    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>


    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


    <!-- App js -->


    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>


    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>


</html>