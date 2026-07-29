<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();
$getAllProducts = $CI->Salescrm_model->getAllProducts();
$transporter=$this->uri->segment(3);
if($transporter=='ALL')
{
$transporter_name="ALL";
}else
{
$transporter_name=$CI->Salescrm_model->get_transporter_name($transporter);
}


$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                </tr>
                <tr>
                <th style="border: 1px solid black;text-align:center; width:300px;">Transporter</th>
               
              
                </tr>
                <tr>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$transporter_name.'</td>
                   

                </tr>
                </tbody></table>';

                $getAllclaim = $CI->Salescrm_model->get_all_transporter_pending_payment($transporter);
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
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

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
                <div class="row" style="margin-top:20px">
                    <div class="col-md-12">
                        <?php echo $this->session->flashdata('message');?>
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-4 col-xs-4 col-md-4 col-lg-4 text-center"></div>
                    <div class="col-sm-4 col-xs-4 col-md-4 col-lg-4 text-center">
                        <h4 class="page-title text-center">TRANSPORTER PENDING PAYMENTS</h4>
                        
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo page_url;?>Approval/pending_transporter_payment_history" class="pull-right"><span class="btn btn-warning">History</span></a>
                    </div>
                </div>

                        <div class="row">
                        <form method="post" action="<?php echo page_url;?>Approval/filter_transport_based_approval_payment_pending">
                        

                        


                            <div class="card-box col-md-12">

 
                               <div class="col-md-4"></div>
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label>Transporter <span style="color:red;"></span></label>
                                <select name="transporter" id="transporter" class="form-control">
                                <option value="">Select</option>
                                <?php 
                                $resty=$this->db->select('id,name')->from('transporter_details')->get();
                                if($resty->num_rows()>0)
                                {
                                foreach($resty->result() as $rowww)
                                {
                                ?>
                                <option value="<?php echo $rowww->id;?>"><?php echo $rowww->name;?></option>
                                <?php 
                                }
                                }
                                ?>
                                </select>
                                </div>
                                <div class="col-md-12 text-center" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                            </div>

                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 card-box"><?php echo $filter_criteria;?></div>
                        <div class="col-md-4 card-box" style="min-height: 144px;">
                            <h4 class="text-center">Transportation Claim</h4>
                        <p style="color:red;font-weight: bold;font-size: 30px;text-align: center;">₹<?php echo $getAllclaim;?></p>
                        </div>
                    </div>
            
                <!-- end page title end breadcrumb -->

                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                <tr>
                                    <th>Sr No.</th>
									<th>Approval Date</th>
                                    <th>Location From</th>
                                    <th>Customer Name</th>
                                    <th>Product Details</th>
                                    <th>Transport Approval Details</th>
                                    <th>Transporter Details</th>
                                    <th>Amount</th>
                                    <th>TDS</th>
                                    <th>Final Payable Amount</th>
                                    <th>Update Payment</th>
                    
                                
                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>


                 <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Payment Details</h4>
      </div>
      <form action="<?php echo page_url;?>Approval/transporter_payment_evidence" method="post" enctype="multipart/form-data">
        <input type="hidden" name="approval_id" id="approval_id">
        <input type="hidden" name="flag1" value="<?php echo $this->uri->segment(3);?>">
        <input type="hidden" name="flag2" value="<?php echo $this->uri->segment(4);?>">
        <input type="hidden" name="flag3" value="<?php echo $this->uri->segment(5);?>">
        <input type="hidden" name="flag4" value="<?php echo $this->uri->segment(6);?>">
        <input type="hidden" name="flag5" value="<?php echo $this->uri->segment(7);?>">
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
        </div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Submit">
      </div>
  </form>
    </div>

  </div>
</div>



                <!-- Footer -->
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
        <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

       

        <script>
$( document ).ready(function() {
$('#products').select2();
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/approval_based_transporter_pending_payment/<?php echo $this->uri->segment(3);?>",
"aoColumns": [

				{ mData: 'sr_no' } ,
				{ mData: 'current_date' },
                { mData: 'hpcl_location' },
                { mData: 'customer_name' },
			    { mData: 'product_details' },
                { mData: 'transport_approval' },
                { mData: 'transport_details' },
                { mData: 'claim' },
                { mData: 'tds' },
                { mData: 'finalpayment' },
                { mData: 'update_payment' }
				
				
		]
});  


});

</script>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
var business_loc = $("#business_loc").val();
if(business_loc=='')
{
	$("#error_business_loc").html('Required!');
}
var department_name = $("#department_name").val();
if(department_name=='')
{
	
	$("#error_department_name").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
	
	$("#error_status").html('Required!');
}


if(business_loc=='' || department_name==''|| status=='' )
{
	
	return false;
}

});
});


function payment_done(id)
{
    $.ajax({
      url: '<?php echo page_url;?>Approval/get_transporter_payment_details_for_approval_based_transportation/'+id,
      type: 'get',
      success: function(data) {
        var d=data.split('|');
        $("#approval_id").val(id);
        $("#tname").val(d[1]);
        $("#payable_amount").val(d[0]);
      $("#myModal").modal('show');
      }

});

}
</script>
<script type="text/javascript">
    function chk_item_picked_up(id) {
        window.location.href = "<?php echo page_url;?>Approval/chk_item_picked_up/"+id;
    }
</script>
    </body>
</html>