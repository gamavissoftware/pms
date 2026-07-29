<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$getRackLocation = $CI->master->getRackLocation();

$DI = &get_instance();
$DI->load->model('Salescrm_model');
$getAllProducts = $CI->Salescrm_model->getAllProducts();


if($this->uri->segment(5)<>'ALL' &&  $this->uri->segment(5)<>'')
{
$comp=$CI->Salescrm_model->getHpclCompanyName($this->uri->segment(5));
}else
{
$comp="ALL";
}

if($this->uri->segment(6)<>'ALL' &&  $this->uri->segment(6)<>'')
{
$custo1=$CI->Salescrm_model->getCustomerdetail($this->uri->segment(6));
if(count($custo1)>0)
{
  $custo=$custo1[1];
}
}else
{
$custo="ALL";
}



if($this->uri->segment(7)<>'ALL' && $this->uri->segment(7)<>'')
{
$prod=$CI->Salescrm_model->get_product_name($this->uri->segment(7));
}else
{
$prod="ALL";
}




$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Date</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Company</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Customer</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Product</th>
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.date('d-M-Y',strtotime($this->uri->segment(3))).' to '.date('d-M-Y',strtotime($this->uri->segment(4))).'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$comp.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$custo.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$prod.'</td>
              

                </tr>
                </tbody></table>';


?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> ORDERS PENDING FOR BILLING</title>



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
                    <h4 class="page-title text-center">Billing History</h4>
                </div>
              </div>

              <div class="row" style="margin-top:20px;margin-bottom:20px;">
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                  <div class="card-box">
                    <div class="row">
                      <form method="post" action="<?php echo page_url;?>Billing/filter_billing_history">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="field-2" class="control-label">From Date<span style="color: red;">*</span></label>
                            <input class="form-control" type="date" name="from_date" id="from_date" value="<?php echo $this->uri->segment(3);?>" autocomplete="off" required="">
                          </div>
                        </div>    
                        <div class="col-md-2">
                          <label>To Date<span style="color: red;">*</span></label>
                          <input class="form-control" type="date" name="to_date" id="to_date" value="<?php echo $this->uri->segment(4);?>" autocomplete="off" required="">
                        </div>

                         <div class="col-md-2">
                          <label>Billing Company<span style="color: red;">*</span></label>
                          <select class="form-control" name="company" id="company" onchange="get_customer_data();">
                            <option value="ALL" <?php if($this->uri->segment(5)=="ALL"){?> selected <?php } ?>>ALL</option>
                          <?php
                          $uri = $this->uri->segment(5);
                          if ($getRackLocation != '') {
                          foreach ($getRackLocation as $row1) { ?>
                          <option value="<?php echo $row1->id; ?>" <?php if ($row1->id == $uri) {
                          echo "selected";
                          } ?>><?php echo $row1->companyname; ?></option>
                          <?php }
                          } ?>
                          </select>
                         
                        </div>

                         <div class="col-md-3">
                          <label>Customer<span style="color: red;">*</span></label>
                          <select class="form-control select3" name="customer" id="customer">
                          <option value="ALL" <?php if($this->uri->segment(6)=="ALL"){?> selected <?php } ?>>ALL</option>
                          </select>
                        </div>

                         <div class="col-md-3">
                          <label>Product<span style="color: red;">*</span></label>
                           <select class="form-control" name="product" id="product">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllProducts != '') {
                                                        foreach($getAllProducts as $row2) {?>
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id == $this->uri->segment(7)) { echo 'selected';};?>><?php echo $row2->instruments_name;?></option>
                                                <?php } } ?>
                                            </select>
                        </div>

                      </div>

                      <div class="col-md-12">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                          <label></label>
                          <input class="btn btn-success" style="margin-top:24px;width:100%" type="submit" name="submit" value="Filter">
                        </div>
                        
                     
                    </div>
                     </form>
                    </div>
                  </div>
                </div>
              </div>

                <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 card-box"><?php echo $filter_criteria;?></div>
                        <div class="col-md-4"></div>
                    </div>


              <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box table-responsive">
                    <table id="example2" class="table table-striped table-bordered manglesh">
                      <thead>
                        <tr>
                          <th>SR NO.</th>
                            <th>ORDER SOURCE.</th>
                            <th>SALES ORDER DATE/NO.</th>
                            <th>BILL DATE/INVOICE NO.</th>
                          <th>SALES AGENT.</th>
                           <th>BILLING COMPANY</th>
                          <th>CUSTOMER COMPANY NAME</th>
                          <th>CUSTOMER NAME</th>
                          <th>TAX DETAILS</th>
                          <th>PRODUCTS</th>
                          <th>SHIPPING ADDRESS</th>
                          <th>BILLING ADDRESS</th>
                          <th>PAYMENT TERMS</th>
                           <th>PO DETAILS</th>
                          <th>SEND TO TALLY</th>
                          <th>BILLED</th>
                          <th>ORDER DETAILS</th>
                          <th>TAX INVOICE</th>
                          <th>CANCEL ORDER</th>
                          <!-- <th>PAYMENT COLLECTION HISTORY</th> -->
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
		  


               <div id="cancell_order" class="modal fade" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">

                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h5 class="modal-title">Cancell Order</h5>
                    </div>
                    <div class="modal-body">
                      <form method="post" action="<?php echo page_url;?>Billing/cancel_order_revert_stock/1">
                        <input type="hidden" name="cancell_order_id" id="cancell_order_id">
                
                        <div class="row">
                          <div class="col-sm-12">
                            <div  class="form-group">
                             <label>Cancellation Remarks</label>
                             <textarea class="form-control" name="cancell_rmk" id="cancell_rmk"></textarea>
                            </div>
                            
                          </div>
                        </div>
                       
                          <button type="submit" class="btn btn-primary">Submit</button>
                      </form>
                    </div>
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
get_customer_data_selected();
$('#example2').dataTable({
 "bProcessing": false,
 "pagination":true,
 fixedHeader: true,
"bSort": false,
 fixedColumns:   {
  leftColumns: 3
  },
    pageLength:50,

 "sAjaxSource": "<?php echo page_url;?>Billing/billing_history_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>",

 "aoColumns": [

               { mData: 'sr_no' },
               { mData: 'source' },
               { mData: 'so_no' },
               { mData: 'invoice_no' },
               { mData: 'agent' },
               { mData: 'billing_company' },
                { mData: 'company_name' },
                { mData: 'customer_name' },
                 { mData: 'taxdetail' },
                { mData: 'products' },
                { mData: 'shipaddress' },
                { mData: 'billingaddress' },
                { mData: 'paymentterm' },
                  { mData: 'po_details' },
                { mData: 'sendtotally' },
                { mData: 'billed' },
                { mData: 'order_details' },
                { mData: 'tax_invoice' },
                { mData: 'cancel' }
                // { mData: 'payment_collection' }

                ]

                



        });

});



 function cancel_order(id) {
  $('#cancell_order_id').val(id);
  $("#cancell_order").modal('show');
  
  }

 $(document).ready(function() {
            $('.select3').select2();
            $('#product').select2();
});

function get_customer_data() {
            
    var compid=$("#company").val();
    if(compid!="ALL")
    {
    $("#customer").html('');
    $.ajax({
    type:"post",
    url:"<?php echo page_url;?>Customer/get_customer_by_companyNew",
    data:"company="+compid,
    success:function(data){
    $("#customer").html(data);
    }
    });
    }else
    {
      $("#customer").html('<option value="ALL">ALL</option>');
     

    }



        }


        function get_customer_data_selected()
        {

        var compid=$("#company").val();
        var customer=$("#customer").val();
        if(compid!="ALL")
        {
        $("#customer").html('');
        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Customer/get_customer_by_companyNew_selected",
        data:"company="+compid+"&customer="+customer,
        success:function(data){
        $("#customer").html(data);
        }
        });
        }else
        {
        $("#customer").html('<option value="ALL">ALL</option>');


        }


        }

</script>

<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>




</body>

</html>
