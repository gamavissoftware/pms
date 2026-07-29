<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Spare Parts BOM</title>

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
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

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
        <div class="container">

            <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">


                        </div>

                        <h4 class="page-title">SPARE PARTS BOM</h4>
                    </div>
                </div>
            </div>
            <!-- end page title end breadcrumb -->
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

            <div class="row">
                <div class="col-xs-12">
                    <div class="card-box">

                        <div class="row">
                            <div class="col-sm-12 col-xs-12 col-md-12">



                                <form method="post" id="loginForm" action="<?php echo page_url; ?>Master/User_management/add_shortage_spare_parts/<?php echo $this->uri->segment(5)?>">



  <?php 
                                            
                                            $query = $this->db->select('a.id, a.spare_id, a.machine_id, a.quantity, b.code, b.description')->from('bom_spare_parts a')->join('spare_parts b', 'a.spare_id=b.id')->where('a.machine_id', $this->uri->segment(4))->get();

                                             
                                            $i =1;
                                              foreach ($query->result() as $rowss) {
                                            ?>
                                                <div class="row">

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Spare Name</label>
                                                        <span style="color: red;">*</span>
                                                        <input type="text" value="<?php echo $rowss->description.'-'.$rowss->code; ?>" class="form-control" readonly required>
                                                        <input type="hidden" value="<?php echo $rowss->spare_id; ?>" name="shortage_spare_id[]">
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Quantity</label>
                                             <span id="error_quantity" style="color: red;">*</span>
                                            <input type="text" name="quantity[]" value="" id="" class="form-control" required>
                                        </div>
                                    </div>
                                                

                                                </div>

                                                  <?php $i++;
                                            } ?>

                                            <div class="row" style="margin-top: 20px;">

                                    
                                    <div class="col-sm-3">
                                        <input type="submit" id="rolesave" class="btn btn-info" value="Submit">
                                    </div>

                                                </div>
                                </form>


                            </div>

                        </div>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- <script>
        $(document).ready(function() {
            $("#rolesave").attr('disabled', false);
            $("#rolesave").val('Submit');
            $("#loginForm").on("submit", function() {
                
                $("#rolesave").attr('disabled', true);
                $("#rolesave").val('Please Wait...');
            }); 
        }); 
    </script> -->
    <!-- <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            $("#rolesave").click(function() {

                var backcode = $("#backcode").val();

                if (backcode == '')

                {



                    $("#error_backcode").html('Required!');

                }


                var revision = $("#revision").val();

                if (revision == '')

                {



                    $("#error_revision").html('Required!');

                }

                var quantity = $("#quantity").val();

                if (quantity == '')

                {



                    $("#error_quantity").html('Required!');

                }


                if (backcode == '' || quantity == '' || revision == '') {

                    return false;
                }

            });
        });
    </script> -->


   


 <script>

// function getrevisioncode(id){
//      var backCodeId = $('#spare_id' + id).val();

//        if (backCodeId) {

//                 $.ajax({
//                     url: '<?php echo page_url; ?>Master/User_management/get_spare_parts',
//                     method: 'POST',
//                     data: {
//                         backcode_id: backCodeId
//                     },
//                     dataType: 'json',
//                     success: function(response) {

//                         if (response.status == 'success') {

//                             $('#revision' + id).val(response.revision);
//                         } else {

//                             $('#revision' + id).val('');
//                         }
//                     },
//                     error: function() {
//                         alert('Error occurred while fetching data.');
//                     }
//                 });
//             } else {

//                 $('#revision' + id).val('');
//             }
// }
//     </script>



<script>
    $(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        // Loop through all quantity inputs and check their values
        $('input[name="quantity[]"]').each(function() {
            var quantityValue = $(this).val();
            if (quantityValue == 0 || quantityValue == '') {
                // Show error message and prevent form submission
                $('#error_quantity').text('Quantity cannot be 0').css('color', 'red');
                e.preventDefault(); // Prevent form submission
                $(this).focus(); // Focus on the problematic field
                return false; // Stop the loop
            } else {
                // Clear the error message if the input is valid
                $('#error_quantity').text('*');
            }
        });
    });
});
</script>




</body>

</html>