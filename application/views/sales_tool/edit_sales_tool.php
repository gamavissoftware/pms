<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>Edit Sales Tool</title>

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <style>
table.manglesh thead th {
                background: #003366;
                color:#fff;
                font-weight:bold;
            }
            
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}

.addMore {
margin-top: 22px;
}

.removePromotion {
margin-top: 22px;
}
</style>
    </head>


    <body>


        <!-- Navigation Bar-->
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->
        <?php foreach($edit_sales_tool as $row);?>

        <div class="wrapper">
            <div class="container-fluid">
 <div class="card-box">
    <?php echo $this->session->flashdata('message'); ?>
    <h3 style="text-align: left;">Machine Details</h3>
 <form method="post" autocomplete="off" action="<?php echo page_url;?>Sales_tool/update_details" id="frm" enctype="multipart/form-data">
    <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">Machine Name</label>
                     <input type="text" id="machine_name" name="machine_name" value="<?php echo $row->machine_name;?>" class="form-control" readonly>
                </div>
            </div> 
        <div id="suggestion-box"></div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">Price</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="machine_price" name="price" class="form-control" value="<?php echo $row->price;?>" onkeypress="return isNumberKey(event,this)">
            </div>
        </div> 
        
        
         <div class="col-md-3">
            <div class="form-group">
                <label for="field-1" class="control-label">USP</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <textarea class="form-control" name="usp" id="usp" placeholder="" required><?php echo $row->usp;?></textarea>
                <script>CKEDITOR.replace( 'usp' );</script>
            </div>
        </div> 
        
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Max Discount Capping</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="discount" name="discount" class="form-control" value="<?php echo $row->discount;?>" onkeypress="return isNumberKey(event,this)">
            </div>
        </div> 

        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">PDF</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="file" id="pdf_file" name="pdf_file" class="form-control">
            </div>
        </div> 

        <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Video</label>
                <span id="error_datepicker1" style="color:red;"></span>
                <input type="text" id="video" name="video" class="form-control" value="<?php echo $row->video;?>">
            </div>
        </div> 
    </div>
        <hr>
        <h3 style="text-align: left;">Promotion</h3>
        <?php foreach($getEditPromotion as $promotion) {?>
        <div class="row fieldGroup" id="promotion">
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="promotion_name" name="promotion_name[]" value="<?php echo $promotion->promotion_name;?>" class="form-control">
            </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Price</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="promotion_price0" name="promotion_price[]" class="form-control mand" autocomplete="off" value="<?php echo $promotion->promotion_price;?>" onkeypress="return isNumberKey(event,this)" required="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">Description</label>
                    <span id="error_username" style="color:red;">*</span>
                    <textarea class="form-control description" name="description[]" id="description" placeholder="" required><?php echo $promotion->description;?></textarea>
                    <!-- <script>CKEDITOR.replace( 'description' );</script> -->
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-danger removePromotion"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </div>
        </div>
        <?php } ?>
        <div class="row" id="promotion">
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="promotion_name" name="promotion_name[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Price</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="promotion_price0" name="promotion_price[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" required="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Description</label>
                    <span id="error_username" style="color:red;">*</span>
                    <textarea class="form-control description" name="description[]" id="description" placeholder="" required></textarea>
                    <!-- <script>CKEDITOR.replace( 'description' );</script> -->
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-warning addPromotion"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
            </div>
        </div>
        <hr>
        <h3 style="text-align: left;">Client Details</h3>
        <div class="row" id="details">
            <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Client Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="client_name" name="client_name[]" class="form-control">
            </div>
            </div>
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">City</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="city0" name="city[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Industry</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="industry0" name="industry[]" class="form-control mand" autocomplete="off" value="" required="">
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-warning addDetails"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
            </div>
        </div>
        <hr>
           <h3 style="text-align: left;">Miscelleneous Charges</h3>
        <div class="row" id="charges">
         <div class="col-md-2">
            <div class="form-group">
                <label for="field-1" class="control-label">Name</label>
                <span id="error_order_type" style="color:red;">*</span>
                 <input type="text" id="charges_name0" name="charges_name[]" class="form-control">
            </div>
            </div> 
            <div class="col-md-2">
                <div class="form-group fieldGroup">
                    <label for="field-1" class="control-label">Charges</label>
                    <span id="error_username" style="color:red;">*</span>
                    <input type="text" id="charges0" name="charges[]" class="form-control mand" autocomplete="off" value="" required="">
                <span style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn btn-warning addCharges"><span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span></a>
            </div>
        </div>
    <div class="row">
       <div class="col-md-4"> 
            
            <input type="submit" value="Submit" id="sub" class="btn btn-success">
        </div>
    </div>
    </form>
</div>
</div>
                            <!-- /.modal -->


                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        <!-- end wrapper -->


         <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
        <script type="text/javascript">
            var i=1;
            $('.addPromotion').click(function(){
                i++;
                $('#promotion').append('<div style="clear:both;height:5px"></div><div class="row fieldGroup" id="row'+i+'"><div class="col-md-2"><div class="form-group"><label for="field-1" class="control-label">Name</label><span id="error_order_type" style="color:red;">*</span><input type="text" id="promotion_name" name="promotion_name[]" class="form-control"></div></div> <div class="col-md-2"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Price</label><span id="error_username" style="color:red;">*</span><input type="text" id="promotion_price" name="promotion_price[]" class="form-control mand" autocomplete="off" value="" onkeypress="return isNumberKey(event,this)" required=""></div></div><div class="col-md-3"><div class="form-group fieldGroup"><label for="field-1" class="control-label">Description</label><span id="error_username" style="color:red;">*</span><textarea class="form-control description" name="description[]" id="description" placeholder="" required></textarea></div></div><div class="col-md-2"><a href="javascript:void(0)" class="btn btn-danger removePromotion"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span></a> </div></div>');
                });
            
            $(document).on('click', '.removePromotion', function(){
                    $(this).parents(".fieldGroup").remove();
                    });
        </script>
    </body>
</html>