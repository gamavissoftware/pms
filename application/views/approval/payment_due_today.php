<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$payment_due=$CI->Salescrm_model->today_due_payment($this->uri->segment(3));

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?></title>

        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
         <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<style>
            table.pretty thead th {
                text-align: center;
                background:<?php echo $LOGO->colorcode;?>;
                color:#fff;
                font-size:12px;
            }
            table.pretty td {
                text-align: center;
                font-size:12px;
            }
            </style>
    </head>


    <body>


        <!-- Navigation Bar-->
        <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                            <div class="btn-group pull-right">
                              <a href="<?php echo page_url; ?>Approval/history_payments"><button type="submit" class="btn btn-success" name="action_button">History</button></a>
                             </div>
                            <h4 class="page-title text-center">PENDING PAYMENTS FOR TODAY</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                
                 <form method="post" action="<?php echo page_url;?>Approval/filter_payment_due_today">
                            <div class="card-box col-md-12">
                                <div class="">
                                   
                                   <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Filter by Vendor/Party</label>
                                            <select class="form-control" name="vendor" required>
                                                <option value="ALL" <?php if($this->uri->segment(3)=='' || $this->uri->segment(3)=='ALL'){?> selected <?php } ?>>ALL</option>
                                                <?php $res=$this->db->select('id,name')->from('vendors')->get();
                                                if($res->num_rows()>0)
                                                 {
                                                        foreach($res->result() as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($this->uri->segment(3)==$row1->id){?> selected <?php } ?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                  
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4 card-box">
                            <h4 class="text-center">Payment Due</h4>
                            <p style="color:red;font-weight: bold;font-size: 22px;text-align: center;"><?php echo $payment_due;?></p>
                        </div>
                    </div>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                               <tr>
   <th>Sr No.</th>
                                    <th>Purchase Date</th>
                                    <th>Bill No.</th>
                                    <th>Vendor</th>
                                    <th>Payment Term/Credit Day</th>                       
                                    <th>Product Detail</th>   
                                    <th style="width:100px;">Due Date</th>                      
                                    <th>Basic Amount</th>
                                    <th>GST</th>
                                   <th>Grand Amount</th>
                                    <th>Previous Payment</th>
                                    <th>Final Payable Amount</th>
                                    <th>Action</th>
                                 
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->


                 <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="loginForm" method="post" action="<?php echo page_url;?>Inventory/update_inventory_payment" enctype="multipart/form-data">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Update Payment Details</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Approve/Reject</label>
                                            <span id="error_to_date" style="color:red;">*</span>
                                            <input type="hidden" name="detail_id" id="detail_id">
                                            <select class="form-control" id="discount_approval" name="discount_approval" required="" onchange="check_for_remarks()">
                                                <option value="">SELECT</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Reject</option>
                                            </select>
                                        </div>
                                    </div> -->
                          <div class="col-md-4">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Payment Date</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="pur_paymentOn" id="datepicker" class="form-control mand" autocomplete="nope" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Payment</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="payment" id="payment" class="form-control mand" autocomplete="nope" value="" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Payment Done</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="payment_done" id="payment_done" class="form-control mand allow_decimal" autocomplete="nope" value="" required  onkeyup="allow_decimal('payment_done');">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Collection Reference </label>
                              <span style="color:red;">*</span>
                              <input type="text" name="collection_reference" id="collection_reference" class="form-control mand" autocomplete="nope" value="" required>
                            </div>
                        </div>
                         <div class="col-md-4">
                                <label>Payment Type<span style="color: red;">*</span></label>
                                <span class="input-icon icon-right" style="margin-bottom:10px">
                                
                                <select class="form-control mand" name="payment_type" id="payment_type" required onchange="check_payment_type();">
                                  <option value="">SELECT PAYMENT TYPE</option>
                                  <option value="1">NEFT/IMPS</option>
                                  <option value="2">Cheque</option>
                                  <option value="3">Cash</option>
                                </select>
                                </span><br/>
                                
                              </div>


                                <div class="col-md-4 utr_details" style="display:none;">
                                      <label>UTR No</label><span style="color: red">*</span>
                                      <input type="text" name="utr_no" id="utr_no" class="form-control" autocomplete="off">
                                </div>
                                 <div class="col-md-4 utr_details" style="display:none;">
                                        <label>Upload Evidence </label><span style="color: red">*</span>
                                        <span class="input-icon icon-right">
                                          <input type="file" name="utr_evidence" id="upload_utr_file" class="form-control" autocomplete="nope">
                                        </span>
                                </div>

                               <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque No. <span style="color: red">*</span></label>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                  <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque Date <span style="color: red">*</span></label>
                                        <input type="date"  name="cheque_date" id="cheque_date" class="form-control">
                                    </div>
                                </div>

                                  <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Upload Cheque <span style="color: red">*</span></label>
                                        <input type="file"  name="evidence" id="cheque_pic" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-4 cash_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Upload Evidence <span style="color: red">*</span></label>
                                        <input type="file"  name="cash_evidence" id="cash_pic" class="form-control">
                                    </div>
                                </div>



                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Remarks</label>
                                            <span class="remove_mand" style="color:red;">*</span>
                                            <textarea class="form-control" name="remarks" id="remarks"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="inventory_id" id="inventory_id">
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
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
       
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
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
        <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/payment_due_today_list/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
                
                { mData: 'sr_no' } ,
                { mData: 'purchase_date' },
                { mData: 'bill_no' },
                { mData: 'party' },
                { mData: 'credit_days' },
                { mData: 'product_detail' },
                { mData: 'due_date' },
                { mData: 'basic_amount' },
                { mData: 'gst' },
                { mData: 'grand_amount' },
                { mData: 'previous_payment' },
                { mData: 'final_payment' },
                { mData: 'action' }

                 
                
                
                
        ]
});  
 
});
$('#datepicker').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'dd-mm-yyyy',
      
   });

      function check_payment_type() {
          var payment_type=$("#payment_type").val();
            // alert(payment_type)
              $(".utr_details").css('display','none');
              $(".cheque_details").css('display','none');
              $(".cash_details").css('display','none');
              $("#cheque_no").removeClass('mand');
              $("#cheque_date").removeClass('mand');
              $("#cheque_no").attr('required',false);
              $("#cheque_date").attr('required',false);
              $("#cheque_date").removeClass('mand');
              $("#cheque_pic").attr('required',false);
              $("#cheque_date").removeClass('mand');
              $("#cheque_pic").attr('required',false);

          if(payment_type==1)
          {
              $(".utr_details").css('display','');
              $("#utr_no").addClass('mand');
              $("#utr_no").attr('required',true);
              $("#upload_utr_file").addClass('mand');
              $("#upload_utr_file").attr('required',true);

          }
          if(payment_type==2)
          {
              $(".cheque_details").css('display','');
              $("#cheque_no").addClass('mand');
              $("#cheque_date").addClass('mand');
              $("#cheque_no").attr('required',true);
              $("#cheque_date").attr('required',true);
              $("#cheque_pic").addClass('mand');
              $("#cheque_pic").attr('required',true);

          }

          if(payment_type==3)
          {
              $(".cash_details").css('display','');
              $("#cash_pic").addClass('mand');
              $("#cash_pic").attr('required',true);

          }
      }


</script>
        
        
<script language="javascript" type="text/javascript">   


function update_payment(id,amt)
{

$("#myModal").modal('show');
$("#inventory_id").val(id);
$("#payment").val(amt);

}
      function allow_decimal(data) {
      
          var self = $("#" + data);
          self.val(self.val().replace(/[^0-9\.]/g, ''));
          if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
              evt.preventDefault();
          }
      
      
      }
</script>
    </body>
</html>