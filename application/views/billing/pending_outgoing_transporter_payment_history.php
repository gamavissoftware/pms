<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?></title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">

        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>

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

				font-size:11px;

				font-weight:bold;

			}

table tbody tr td {

  font-size: 11px;

  color:#000;

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

</style>

    </head>





    <body>





        <!-- Navigation Bar-->

                <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">
                <!-- Page-Title -->

              <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-4"></div>
                     <div class="col-md-4"><h4 class="page-title text-center">Transporter Payments History(Outgoing)</h4></div>
                  
                </div>
              </div>
              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <form action="<?php echo page_url;?>Billing/filter_outgoing_payment_pending_history" name="frm" id="frm" method="post">
              <div class="row">
                <div class="col-md-12 card-box">
                  <div class="row" style="margin-top: 20px;">
                          
                           <div class="col-md-2"></div>
                         
                            

                         

                            <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">From</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <input type="date" id="from_date" name="from_date" class="form-control mand" required="" value="<?php echo $this->uri->segment(3);?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">To</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <input type="date" id="to_date" name="to_date" class="form-control mand" required="" value="<?php echo $this->uri->segment(4);?>">
                                        </div>
                                    </div>

                                      <div class="col-md-2 hiredvehicle">
                             <div class="form-group">
                                            <label for="field-1" class="control-label">Transporter</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                           <select name="transporter" id='transporter' class="form-control">
                                            <option value="ALL">ALL</option>
                                            <?php 
                                            $rest=$this->db->select('id,name')->from('transporter_details')->get();
                                            if($rest->num_rows()>0)
                                            {
                                            foreach($rest->result() as $row)
                                            {
                                            ?>
                                            <option value="<?php echo $row->id;?>" <?php if($this->uri->segment(5)==$row->id){?> selected <?php } ?>><?php echo $row->name;?></option>
                                            <?php } }
                                            ?>

                                           </select>
                                        </div>
                          </div>

                                    <div style="clear:both;height:20px;"></div>
                                      <div class="col-md-3"></div>
                                      <div class="col-md-4 text-center"><input type="submit" name="sub" id="sub" class="btn btn-success" value="Submit"></div>
                                    

                                </div>
                              </form>

                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                          <th>TRANSPORT TYPE</th>
                          <th>TRANSPORTER NAME</th>
                        
                          <th>DISPATCH DATE</th>
                          <th>INVOICE NO.</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>PRODUCTS</th>
                          <th>SHIPPING ADDRESS</th>
                         <th>EWAY BILL</th>
                          <th>TAX INVOICE</th>
                            <th>RATE TYPE</th>
                            <th>RATE</th>
                            <th>VEHICLE DETAILS</th>
                            <th>PAYABLE AMOUNT</th>
                            <th>TDS AMOUNT</th>
                            <th>GST AMOUNT</th>
                            <th>TOTAL PAYABLE AMOUNT</th>
                          <th>PAYMENT DETAILS</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>



              <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Payment Details</h4>
      </div>
      <form action="<?php echo page_url;?>Billing/update_payment_against_order/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="approval_id" id="approval_id">
        <input type="hidden" name="flag1" value="<?php echo $this->uri->segment(3);?>">
        <input type="hidden" name="flag2" value="<?php echo $this->uri->segment(4);?>">
        <input type="hidden" name="flag3" value="<?php echo $this->uri->segment(5);?>">
        <input type="hidden" name="flag4" value="<?php echo $this->uri->segment(6);?>">
        <input type="hidden" name="flag5" value="<?php echo $this->uri->segment(7);?>">
        <input type="hidden" name="recordid" id="recordid" value="">
        <input type="hidden" id="tid" name="" value="">
      <div class="modal-body">
        <div class="row">
            <div class="col-md-4">
            <div class="form-group">
                    <label>Transporter Name</label>
                    <input type="text" name="tname" id="tname" class="form-control" required readonly>
                </div>
            </div>


             <div class="col-md-4">
            <div class="form-group">
                    <label>Payble Amount</label>
                    <input type="text" name="payable_amount" id="payable_amount" class="form-control" required readonly>
                </div>
            </div>

             <div class="col-md-4">
            <div class="form-group">
                    <label>Paid Amount</label>
                    <input type="text" name="paidamount" id="paidamount" class="form-control" onkeyup="checkpaidamount();" required>
                </div>
            </div>


             <div class="col-md-4">
            <div class="form-group">
                
                    <label>Payment Date <span style="color:red">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control" required>
                </div>
            </div>


             <div class="col-md-4">
            <div class="form-group">
                
                    <label>Payment Evidence <span style="color:red">*</span></label>
                    <input type="file" name="evidance" id="evidance" class="form-control" required>
                </div>
            </div>

              <div class="col-md-4">
            <div class="form-group">
                
                    <label>Bill Attachment <span style="color:red">*</span></label>
                    <input type="file" name="billattachment" id="billattachment" class="form-control" required>
                </div>
            </div>
             <div class="col-md-12" style="display: none; " id="remarksdiv">
            <div class="form-group">
                
                    <label>Remarks For Short Payment<span style="color:red">*</span></label>
                   <textarea class="form-control" id="remarks" name="remarks"></textarea>
                </div>
            </div>

        </div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Submit">
      </div>
  </form>
    </div>

  </div>
</div>

		  

               <?php $this->load->view('common/footer');?>

                <!-- End Footer -->



            </div> <!-- end container -->

        </div>

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

        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>

<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

		



	 

 <script>

$( document ).ready(function() {

$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
 dom: 'lBfrtip',
        buttons: [
             'excel'
        ],
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },

"pageLength": 50,
 "sAjaxSource": "<?php echo page_url;?>Billing/transporter_pending_payment_list_history/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'ttype' },
               { mData: 'tname' },
              
               { mData: 'dispatch_date' },
               { mData: 'invoice_no' },
               { mData: 'company_name' },
               { mData: 'customer_name' },
               { mData: 'products' },
               { mData: 'shipaddress' },
               { mData: 'eway_bill' },
               { mData: 'tax_invoice' },
             
                { mData: 'trate' },
               { mData: 'rate' },
               { mData: 'vehicle_details' },
               { mData: 'payableamount' },
               { mData: 'tds' },
               { mData: 'gst' },
               { mData: 'total' },
                 { mData: 'update_payment' }
              ]

                
                



        });

});






$( document ).ready(function() {
    checktransport_type();

});

function payment_done(id, amount, tid)
{
    var totalamount = amount;
    var transpoter_id = tid;
    $.ajax({
      url: '<?php echo page_url;?>Inventory/gettransporterdetail/'+transpoter_id,
      type: 'get',
      success: function(data) {
        var d=data.split('|');
        $("#approval_id").val(id);
        $("#tname").val(d[0]);
        $("#recordid").val(id);
        $("#tid").val(d[1]);
        $("#payable_amount").val(totalamount);
        $("#myModal").modal('show');
      }

});

}

function checkpaidamount(){
    var totalamount = $("#payable_amount").val();
    var paidamount = $("#paidamount").val();
    if(parseFloat(paidamount)!=parseFloat(totalamount)){
          

        $("#remarksdiv").show();
        $("#remarks").attr('required',true);
    }else{
        $("#remarksdiv").hide();
         $("#remarks").attr('required',false);
    }

     if(parseFloat(paidamount)>parseFloat(totalamount)){
            alert('Paid Amount cannot be greater than Payable Amount');
            $("#paidamount").val('');
             $("#remarksdiv").hide();
         $("#remarks").attr('required',false);
          }
    
}
</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>




</body>

</html>
