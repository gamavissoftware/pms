<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$getAllVendors = $CI->Salescrm_model->getAlltransporters();
$getHpclLocations = $CI->Salescrm_model->getHpclLocations();

if($this->uri->segment(4)<>'ALL' && $this->uri->segment(4)<>'')
{
$loc=$CI->Salescrm_model->get_location_name($this->uri->segment(4));
}else
{
$loc="ALL";
}


if($this->uri->segment(3)<>'ALL' && $this->uri->segment(3)<>'')
{
$vendor=$CI->Salescrm_model->get_transporter_name($this->uri->segment(3));
}else
{
$vendor="ALL";
}

$filter_criteria='<table style="border: 1px solid black;" class="table table-bordered">
                <tbody><tr>
                <th style="border: 1px solid black; font-weight:bold;text-align:center;" colspan="3">Report Filter Criteria</th>        
                </tr>
                <tr>
              
                <th style="border: 1px solid black;text-align:center; width:200px;">Transporter</th>
                <th style="border: 1px solid black;text-align:center; width:200px;">Location</th>
                </tr>
                <tr>
              
                 <td style="border: 1px solid black;text-align:center;color:black;">'.$vendor.'</td>
                <td style="border: 1px solid black;text-align:center;color:black;">'.$loc.'</td>
               
                </tr>
                </tbody></table>';




$claim_amount=$CI->Salescrm_model->get_pending_transporter_payment_type2($this->uri->segment(3),$this->uri->segment(4));
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
                <div class="row" style="margin-top:20px;">
                    <div class="col-md-12">
                    
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <h4 class="page-title text-center">TRANSPORTER PAYMENT TYPE II/III HISTORY</h4>
                        </div>
                       
                        
                    </div>
                     <div class="col-md-12 text-center">
                    
                    <?php echo $this->session->flashdata('message');?>
                        
                    </div>
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 card-box ">
                       

                        <form method="post" action="<?php echo page_url;?>Approval/filter_transporter_pending_payment_done">
                            
                                  <div class="col-md-3"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Transporter</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <select class="form-control" name="party">
                                                <option value="ALL">ALL</option>
                                                <?php if($getAllVendors != '') {
                                                        foreach($getAllVendors as $row2) {?>
                                                        <option value="<?php echo $row2->id;?>"<?php if($row2->id == $this->uri->segment(5)) { echo 'selected';};?>><?php echo $row2->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Location</label>
                                            <select class="form-control" name="hpcl_locations">
                                                <option value="ALL">ALL</option>
                                                <?php if($getHpclLocations != '') {
                                                        foreach($getHpclLocations as $row1) {?>
                                                <option value="<?php echo $row1->id;?>" <?php if($row1->id == $this->uri->segment(6)){?> selected <?php } ?>><?php echo $row1->name;?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                             
                        </form>
                    </div>
                </div>
            
                <div class="row">
                    
                    <div class="col-md-4 card-box" style="min-height: 170px;">
                        

                    </div>
                    <div class="col-md-4 card-box" style="min-height: 170px;"><?php echo $filter_criteria;?>
                    </div>

                 
                </div>

                  <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                 <tr>
                                    <th>Sr No.</th>
                                    <th>Type</th>
                                    <th>Invoice No./Date/Attachment</th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th style="width:30%">Product Name</th>
                                    <th>Transport Details</th>
                                    <th>TDS %</th>
                                    <th>Base Amount</th>
                                     <th>TDS Deduction</th>
                                    <th>GST</th>
                                    <th>Final Base Amount</th>
                                   
                    
                                    <th>Payment Details</th>

                                </tr>
                                </thead>
                                
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <!-- end row -->


    <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

   
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

        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script>
$( document ).ready(function() {

    $('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Approval/pending_transporter_payment_list_t2_new_history/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>",
"aoColumns": [
                { mData: 'sr_no' } ,
            { mData: 'type' } ,
            { mData: 'current_date' },
            { mData: 'customer_name' },
            { mData: 'location' },
            { mData: 'product_name' },
            { mData: 'transportation_detail' },
            { mData: 'tds' },
            { mData: 'amount' },
            { mData: 'tds_amount' },
            { mData: 'gst' },
            { mData: 'final_base_amount' },
            { mData: 'action'}

                
                
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
      url: '<?php echo page_url;?>Approval/get_transporter_payment_details/'+id,
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
    </body>
</html>