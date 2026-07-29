<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $getAllUnits = $CI->salescrm->getAllUnits();
  $getAllTransporters = $CI->salescrm->getAllTransporters();
  $getEditTypeTwoApproval = $CI->salescrm->getEditTypeTwoApproval($this->uri->segment(3));


  $current_date = '';
  $hpcl_location = '';
  $transportation = '';
  $transportation_rate = '';

  if($getEditTypeTwoApproval != '') {
    foreach ($getEditTypeTwoApproval as $row);
        $current_date = $row->current_date;
        $hpcl_location = $row->name;
        if($row->transportation == 1) {
          $transportation = 'Included';  
          $a = 0;
        } else if($row->transportation == 2) {
          $transportation = 'Not Included'; 
          $a = 1;
        } else {
          $transportation = '';
          $a = 0;
        }
        $transportation_rate = floatval($row->transportation_rate);
        $addedBy=$row->first_name." ".$row->last_name;
        $addedOn=$row->addedOn;
    

  }


$getEditTypeTwoProductApproval = $CI->salescrm->getEditTypeTwoProductApproval($this->uri->segment(3));
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Approval Form</title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.18.0/ckeditor.js" integrity="sha512-woYV6V3QV/oH8txWu19WqPPEtGu+dXM87N9YXP6ocsbCAH1Au9WDZ15cnk62n6/tVOmOo0rIYwx05raKdA4qyQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<style type="text/css">
    .search-page h5 {
    font-size: 17px;
    margin: 0px;
    padding: 5px 0px;
    font-weight: 700;
    border-bottom: 1px solid #25272e52;
    margin-bottom: 10px;
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
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                <div class="page-title-box">
                    <h4 class="page-title text-center">ITEM TRANSPORTATION DETAIL</h4>
                </div>
            </div>
        </div>

        <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                  <table class="table table-bordered">
                  <thead>
                     <tr style="background-color:#f5f5f5;">
                  <th colspan="7" style="text-align: center;">Approval Overview</th>
                

                  </tr>
                  <tr style="background-color:#f5f5f5;">
                  <th>Approval ID</th>
                  <th>Approval Date</th>
                  <th>Approved Location</th>
                  <th>Transportation Type</th>
                    <th>Transportation Rate</th>
                 
                  <th>Approval Added On</th>
                   <th>Approval Added By</th>

                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                  <td>TYPE-1011</td>
                  <td><?php echo date('d-m-Y', strtotime($current_date));?></td>
                  <td><?php echo $hpcl_location;?></td>
                  <td><?php echo $transportation;?></td>
                  <td>₹<?php echo $transportation_rate;?>/LTR</td>
                  <td><?php echo date('d-M-Y',strtotime($addedOn));?></td>
                  <td><?php echo $addedBy;?></td>
                  </tr>
                  
                  </tbody>
                  </table>
                </div>
                </div>
       
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <div class="row" style="margin-top:20px;">
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/save_item_delivered/<?php echo $this->uri->segment(3);?>" autocomplete="off" onsubmit="return validate();">
            
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                             <div class="col-md-12 text-center" style="border-bottom:1px dotted #000;"><h4 class="page-title">Product Detail</h4></div>
                        </div>

                          <div class="row">
                         <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Date</label>
                                        <span style="color:red;">*</span>
                                        <input type="date" name="delivery_date" required class="form-control mand"> 
                                    </div>
                                </div>
                            </div>

            <?php if($getEditTypeTwoProductApproval != '') {
                $i=0;
                    foreach($getEditTypeTwoProductApproval as $row1) {
                        $getProductName = $CI->salescrm->getProductName($row1->product_id); ?>
                        <div class="row">
                           
                

                            <div class="col-md-12" style="margin-top:10px;">
                                
                                   


                                <div style="clear:both;height:10px;"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span style="color:red;">*</span>
                                        <input type="hidden" name="approval_detail_id[]" class="inventory" value="<?php echo $row1->id;?>">
                                        <input type="text" name="product[]" class="form-control" value="<?php echo $getProductName;?>" readonly> 
                                    </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Qty</label>
                                    <span style="color:red;">*</span>
                                    <input type="text" name="qty[]" id="qty<?php echo $i;?>" class="form-control qtydata<?php echo $i;?> mand" autocomplete="nope" onkeyup="allow_decimal('qtydata<?php echo $i;?>');" required onblur="compare_moq_qty(<?php echo $i;?>);">
                                  </div>
                                </div>
                                <div class="col-md-2 col-2">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">Pack Size</label>
                                    <span style="color:red;">*</span>
                                    <select class="form-control" name="pack_size[]">
                                    
                                      <?php if($getAllUnits != '') {
                                              foreach($getAllUnits as $row2) {
                                                if($row1->pack_size==$row2->id){?>
                                      <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option>
                                      <?php } }  } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Approved Price</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_approved_price[]" id="approved_price0" autocomplete="off" value="<?php echo $row1->approved_price;?>" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_price_validity[]" id="price_validity0" required autocomplete="off" value="<?php echo date('d-m-Y',strtotime($row1->price_validity));?>" oninput="allow_decimal('price_validity0');" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">CREDIT/VLI Per Ltr<span class="list_price_unit0"></span></label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_credit_vli[]" id="credit_vli0" autocomplete="off" value="<?php echo $row1->credit_vli;?>" oninput="allow_decimal('credit_vli0');" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Minimum Qty if Any</label>
                                        <span style="color:red;">*</span>
                                        <input type="text" class="form-control" name="edit_moq[]" autocomplete="off" id="moq<?php echo $i;?>" value="<?php echo $row1->moq;?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $i++; } } ?>
                          <div class="row">
                             <div class="col-md-12 text-center" style="border-bottom:1px dotted #000;"><h4 class="page-title">Transportation Detail</h4></div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation</label>
                                        <input type="text" class="form-control" value="<?php echo $transportation;?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transportation Allowance/Per Ltr</label>
                                        <input type="text" class="form-control" value="<?php echo $transportation_rate;?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($a == 1) {?>
                        <input type="hidden" name="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transporter Name<span style="color:red;">*</span></label>
                                        <select class="form-control select3 mand" name="transporter_name" id="transporter_name" onchange="getTransporterDetails()" required="">
                                            <option value="">SELECT</option>
                                            <?php if($getAllTransporters != '') {
                                                    foreach($getAllTransporters as $row2) {?>
                                                <option value="<?php echo $row2->id;?>"><?php echo $row2->name;?></option>
                                            <?php } } ?>
                                        </select>
                                        <!-- <input type="text" class="form-control" name="name" required=""> -->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Mobile No.<span style="color:red;">*</span></label>
                                        <input type="number" maxlength="10" class="form-control mand" name="mobile_no" id="mobile_no"  value="" required  data-mask="(999) 999-9999">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Address<span style="color:red;">*</span></label>
                                        <textarea class="form-control" name="address" id="address" required=""></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Vehicle No.<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control mand" name="vehicle_no" id="vehicle_no" required="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Vehicle Type<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control mand" name="vehicle_type" id="vehicle_type" required="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transporter Rate/LTR<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control trate" name="transport_rate" required=""  onkeyup="allow_decimal('trate');">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>


                          <div class="row">
                             <div class="col-md-12 text-center" style="border-bottom:1px dotted #000;"><h4 class="page-title">Customer Detail</h4></div>
                        </div>

                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                     <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Name<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control mand" name="customer_name" required="">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                     <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Mobile<span style="color:red;">*</span></label>
                                       <input type="number" maxlength="10" class="form-control mand" name="customer_mobile" id="customer_mobile" value="" required  data-mask="(999) 999-9999">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                     <div class="form-group">
                                        <label for="field-2" class="control-label">Customer Address<span style="color:red;"></span></label>
                                        <input type="text" class="form-control" name="customer_address">
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="row" style="margin-top:10px;">
                            <div class="col-sm-12">                        
                            
                            <div class="form-group text-center">
                            <input type="submit" id="saves" class="btn btn-success" value="Submit">
                        
                            </div>
                            </div>

                            </div>


                    </div>
                </div>
            </div>
           

            </form>

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
    <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $('.select3').select2({ tags: true});

    function getTransporterDetails() {
        var transporter_id = $("#transporter_name").val();
        // alert(transporter_id);

        $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Approval/getTransporterDetails",
                data:{transporter_id: transporter_id},
                success:function(data) {
                     var arr = data.split('|');
                    var mobile_no = arr[0];
                    var address = arr[1];

                    $("#mobile_no").val(mobile_no);
                    $("#address").val(address);


                }
            });
    }

       function allow_decimal(data)
{
    var self = $("."+data);
      self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }


}


function validate() {
            $("#saves").attr('disabled',false);
            $("#saves").val('Submit');

            var isValid=0;

            $(".mand").each(function() {
                var element = $(this).val();

                    if (element=="") {
                        isValid=1;
                    }
            });

            if(isValid==0) {
                $("#saves").attr('disabled',true);
                $("#saves").val('Please Wait..');
                return true;             
             } else {
                $("#saves").attr('disabled',false);
                $("#saves").val('Submit');
                alert('All Fields Marked as (*) are mandatory');
                return false;
             }   

        }

         function compare_moq_qty(id)
              {

               var qty = parseFloat($("#qty"+id).val());
               var moq = parseFloat($("#moq"+id).val());


               if(qty<moq && moq>0)
               {
                alert('QTY cannot be less than MOQ');
                $("#qty"+id).val('');
               }

              }

</script>

</body>

</html>