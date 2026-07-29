<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php //echo copyright; 
                                    ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title>Feedback Form</title>

    <!-- Table Responsive css -->

    <!-- <script src="<?php echo assets_url; ?>js/angular.min.js"></script> -->

    <!-- DataTables -->

    <!-- <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" /> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>


    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />


    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />


    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />


    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />


    <!-- <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" /> -->


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
        label {
            font-size: 16px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .rating-box {
            display: inline-block;
            margin-left: 30px;
        }

        .rating-box .rating-container {
            direction: rtl !important;
        }


        .rating-box .rating-container label {
            display: inline-block;
            /* margin: 15px 0; */
            color: #d4d4d4;
            cursor: pointer;
            font-size: 20px;
            transition: color 0.2s;
        }

        .rating-box .rating-container input {
            display: none;
        }

        .rating-box .rating-container label:hover,
        .rating-box .rating-container label:hover~label,
        .rating-box .rating-container input:checked~label {
            color: gold;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->
<?php
$uri=$this->uri->segment(3); 
$res=$this->db->select('a.visit_date, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')->from('engineer_visit_view a')->join('system_users_view b','a.user_id=b.user_id','left')->join('system_users_view c','a.engineer=c.user_id','left')->where('a.id=',$uri)->get();
if($res->num_rows() >0)
{
    foreach ($res->result() as $key) {
       $name=$key->efname.' '.$key->e_lname;
       $visitdate=date('d-M-Y',strtotime($key->visit_date));
    }
}
else
{
 $name="";
 $visitdate="";   
}
?>



    <div class="wrapper ">
        <div class="container">
            <div class="dashboard-header">
                <h1>Feedback Form | <?php echo $name; ?> | Visit Date <?php echo $visitdate;?></h1>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <div class="card-box">
                        <form action="<?php echo page_url; ?>Sales/visitedfeedback/<?php echo $this->uri->segment(3); ?>" method="POST" >
                            <div class="form-group">
                                <label>Q1. Engineer Behave & Knowledge</label><span style="color:red;">*</span><br>
                                <div class="rating-box">
                                    <div class="rating-container">
                                        <input type="radio" name="engineerbehave" value="5" id="star-5"> <label for="star-5">&#9733;</label>

                                        <input type="radio" name="engineerbehave" value="4" id="star-4"> <label for="star-4">&#9733;</label>

                                        <input type="radio" name="engineerbehave" value="3" id="star-3"> <label for="star-3">&#9733;</label>

                                        <input type="radio" name="engineerbehave" value="2" id="star-2"> <label for="star-2">&#9733;</label>

                                        <input type="radio" name="engineerbehave" value="1" id="star-1"> <label for="star-1">&#9733;</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Q2. Product Performance</label><span style="color:red;">*</span><br>
                                <div class="rating-box">
                                    <div class="rating-container">

                                        <input type="radio" name="productperformance" value="1" id="star-10" required> <label for="star-10">&#9733;</label>

                                        <input type="radio" name="productperformance" value="2" id="star-9" required> <label for="star-9">&#9733;</label>

                                        <input type="radio" name="productperformance" value="3" id="star-8" required> <label for="star-8">&#9733;</label>

                                        <input type="radio" name="productperformance" value="4" id="star-7" required> <label for="star-7">&#9733;</label>

                                        <input type="radio" name="productperformance" value="5" id="star-6" required> <label for="star-6">&#9733;</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Q3. Backend team coordination</label><span style="color:red;">*</span><br>
                                
                                <div class="rating-box">
                                    <div class="rating-container">

                                        <input type="radio" name="coordination" value="1" id="star-11" required> <label for="star-11">&#9733;</label>

                                        <input type="radio" name="coordination" value="2" id="star-12" required> <label for="star-12">&#9733;</label>

                                        <input type="radio" name="coordination" value="3" id="star-13" required> <label for="star-13">&#9733;</label>

                                        <input type="radio" name="coordination" value="4" id="star-14" required> <label for="star-14">&#9733;</label>

                                        <input type="radio" name="coordination" value="5" id="star-15" required> <label for="star-15">&#9733;</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Q4. Would you recommend our Product</label><span style="color:red;">*</span><br>
                                <div style="margin-top: 10px;">
                                <div class="col-sm-12">
                                <div class="col-sm-6 col-xs-6">
                                <label for="chkNo">
                                        <input type="radio" id="chkNo" name="chkproduct" value="0" onclick="ShowHideDiv()" required />
                                        Yes
                                    </label>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                <label for="chkYes">
                                        <input type="radio" id="chkYes" name="chkproduct" value="1" onclick="ShowHideDiv()" required />
                                        No
                                    </label>
                                </div>
                                </div>
                                    
                                    <div id="dvtext" style="display: none">
                                        <textarea rows="5" class="form-control" name="recommendproduct" id="recommendproduct" placeholder="Remark" ></textarea>
                                    </div>
                                </div>

                                <script>
                                    function ShowHideDiv() {
                                        var chkYes = document.getElementById("chkYes");
                                        var dvtext = document.getElementById("dvtext");
                                        dvtext.style.display = chkYes.checked ? "block" : "none";

                                    }
                                </script>
                            </div>

                            <div class="form-group" style="margin-top: 55px;">
                                <label>Q5. Any other Remarks</label>
                                <textarea rows="5" class="form-control" style="margin-top: 10px;"  name="otherremarks"></textarea>
                            </div>

                            <div class="form-group text-center">
                                <input type="submit" name="Submit" value="Submit" class="btn btn-success" style="margin-top: 5px;">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-6"></div>
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

    <!-- 
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script> -->


    <!-- <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script> -->


    <!-- <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>


    <script src="<?php echo assets_url; ?>js/waves.js"></script>


    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>


    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>


    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script> -->





    <!-- Datatables-->
    <!-- <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
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
    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script> -->
    <!-- Datatable init js -->


    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>


    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


    <!-- App js -->


    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>


    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

</body>


</html>