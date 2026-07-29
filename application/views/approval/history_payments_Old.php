<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$payment_due=$CI->Salescrm_model->total_due_payment($this->uri->segment(3));

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
                            <!-- <div class="btn-group pull-right">
                              <a href="<?php echo page_url; ?>Approval/history_payments"><button type="submit" class="btn btn-success" name="action_button">History</button></a>
                             </div> -->
                            <h4 class="page-title text-center">PAYMENTS HISTORY</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                
 <!--                 <form method="post" action="<?php echo page_url;?>Approval/filter_payment_due_">
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
                        </form> -->

                  <!--   <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4 card-box">
                            <h4 class="text-center">Payment Due</h4>
                            <p style="color:red;font-weight: bold;font-size: 22px;text-align: center;"><?php echo $payment_due;?></p>
                        </div>
                    </div> -->

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
                                    <th>Total Amount</th>
                                    <th>GST</th>
                                    <th>Grand Amount</th>
                                   
                                    <th>Evidence</th>
                                    <th>Action</th>
                                 
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->




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
"sAjaxSource": "<?php echo page_url;?>Approval/payment_done_list/<?php echo $this->uri->segment(3);?>",
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
                { mData: 'action' },
                { mData: 'edit' }

                 
                
                
                
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


function update_payment(id)
{
$("#myModal").modal('show');
$("#inventory_id").val(id);

}
</script>
    </body>
</html>