<?php 
$CI =& get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$interest=$CI->salescrm->get_interest_slab();
$gst_slab=$CI->salescrm->get_gst_slab();
$approval_id=$this->uri->segment(3);

$this->db->select('d.invoice_date,d.invoice,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.model_number,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,d.auto_gen_code')
                        ->from('approval_product_details_type_two a')
                        ->join('approval_form_type_two d','a.approval_id=d.id')
                        ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
                        ->join('hpcl_location c', 'c.id=a.location')
                        ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
                        ->where('a.delivered',0)
                        ->where('a.id',$approval_id);
                    $query=$this->db->get();

    if($query->num_rows()>0) {

        foreach($query->result() as $row);
        $approvalid=$row->auto_gen_code;
        $customer_name=$row->customer_name;
        $invoice_no=$row->invoice;
        $invoice_date=$row->invoice_date;
       
        $instrument_name=$row->instruments_name;
        $instrument_code=$row->model_number;
        $current_date=date('d-M-Y',strtotime($row->current_date));
        $commision=$row->commision;
        $unit=$row->unit;
        
                if($row->transport_type==1)
                {   
                    
                    $transport="EXMI";
                    $trate='-';

                }else
                {
                    $transport="Delivered";
                    $trate="₹".$row->transport_rate."/".$row->unit;
                }


                if($row->validity_from>date('Y-m-d') && $row->validity_to<date('Y-m-d'))
                {
                    $p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
                    $color="red;font-weight:bold;";
                }else
                {
                    $p="";
                    $color="green;font-weight:bold;";
                }





    }else{

        echo "Invalid Link"; exit;
    }

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Details Form</title>

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
        .select2-container
    {
        width: 100% !important;
      

    }
    .select2-container--default .select2-selection--single
    {
        height: 37px !important;
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
                    <h4 class="page-title">Add Item Delivery Details for Commision & Transportation</h4>
                </div>
            </div>
        </div>

            <div class="row">
            <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table class="table table-bordered">
    <thead>
        <tr>
        <th colspan="8" style="text-align:center;">Approval Details</th>
        </tr>
        <tr>
        <th>Approval ID</th>
        <th>Approval Date</th>
        
        <th>Customer Name</th>
        <th>Invoice No.</th>
        <th>Invoice Date.</th>
        <th>Product Name</th>
        <th>CFA Commision</th>
        <th>Transportation Type</th>
        <th>Transportation Rate</th>
        <th>Validity</th>
        </tr>

    </thead>
    <tbody>
<?php 
if($invoice_date<>'' && $invoice_date<>'0000-00-00')
{

$inv=date('Y-m-d',strtotime($invoice_date));
}else
{
$inv='';
}
?>
      <tr>
        <td><?php echo $approvalid;?></td>
        <td><?php echo $current_date;?></td>
        <td><?php echo $customer_name;?></td>
        <td style="color:red;font-weight: bold;font-size: 18px;"><?php echo $invoice_no;?></td>
        <td style="color:red;font-weight: bold;font-size: 18px;"><?php echo $inv;?></td>
        <td style="color:red;font-weight:bold;"><?php echo $instrument_name;?>-<?php echo $instrument_code;?></td>
        <td>₹<?php echo $commision;?>/<?php echo $unit;?></td>
        <td><?php echo $transport;?></td>
        <td><?php echo $trate;?></td>
        <td><?php echo date('d-M-Y',strtotime($row->validity_from));?> to <?php echo date('d-M-Y',strtotime($row->validity_to));?> </td>
      
      </tr>
     
    </tbody>
  </table>
            </div>
            </div>
            </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/add_item_delivery_detail_type_two/<?php echo $approval_id;?>" autocomplete="off" onsubmit="return validate();">
            <input type="hidden" name="interest" value="<?php echo $interest;?>">
            <input type="hidden" name="gst" value="<?php echo $gst_slab;?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                

                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Product<span style="color:red;">*</span></label>
                                        <input type="text" readonly class="form-control mand" name="current_date" style="color:red;font-weight: :bold;font-size:15px;" value="<?php echo $instrument_name;?>-<?php echo $instrument_code;?>" >
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Delivery Date <span style="color:red;">*</span><!-- <span style="color:red;">Delivery date should be equal of in between <?php echo date('d-M-Y',strtotime($row->validity_from));?> to <?php echo date('d-M-Y',strtotime($row->validity_to));?>  </span> --></label>
                                        <input type="date" class="form-control mand" name="current_date" value="<?php echo $inv;?>" required  readonly>
                                    </div>
                                </div>
                              
                             
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                  <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Qty Delivered in <?php echo $unit;?><span style="color:red;">*</span></label>
                                       <input type="text" name="qty" id="qty" class="form-control mand allow_decimal" required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Transportation</label>
                                        <span style="color:red;">*</span>
                                        <select class="form-control mand" name="transportation" id="transportation" required onchange="check_transport();" >
                                            <option value="">Select</option>
                                            <option value="1">Our</option>
                                            <option value="2">Customer</option>
                                           
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    function check_transport()
                                    {

                                         $("#ttype").css('display','none');
                                        $("#trns_type").removeClass('mand');
                                        $("#trns_type").attr('required',false);

                                       var trns=$("#transportation").val();
                                       if(trns==1)
                                       {
                                        $("#ttype").css('display','');
                                        $("#trns_type").addClass('mand');
                                        $("#trns_type").attr('required',true);
                                       }
                                    }
                                </script>

                            </div>

                            <div class="col-md-12" id="ttype" style="display:none;">

                                <div class="col-md-2">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transport Type</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="trns_type" id="trns_type" required onchange="check_transport_type();" >
                                    <option value="">Select</option>
                                    <option value="1">Our Vehicle</option>
                                    <option value="2">Hired Vehicle</option>
                                    </select>
                                </div>
                                </div>
                                <script type="text/javascript">
                                    function check_transport_type()
                                    {   
                                        var trns_type=$("#trns_type").val();
                                        if(trns_type==1)
                                        {

                                        $(".our_vehicle").css('display','');
                                        $(".our_vehicle_no").attr('required',true);
                                        $(".our_vehicle_no").addClass('mand');
                                        $(".hired_vehicle").css('display','none');
                                        $("#transporter_id").attr('required',false);
                                        $("#trns_rate_type").attr('required',false);
                                        $("#trrate").attr('required',false);

                                        $("#transporter_id").removeClass('mand');
                                        $("#trns_rate_type").removeClass('mand');
                                        $("#trrate").removeClass('mand');
                                       
                                        }else if(trns_type==2)
                                        {

                                        $(".our_vehicle").css('display','none');
                                        $(".our_vehicle_no").attr('required',false);
                                        $(".our_vehicle_no").removeClass('mand');
                                         $(".hired_vehicle").css('display','');
                                        $("#transporter_id").attr('required',true);
                                        $("#trns_rate_type").attr('required',true);
                                        $("#trrate").attr('required',true);

                                        $("#transporter_id").addClass('mand');
                                        $("#trns_rate_type").addClass('mand');
                                        $("#trrate").addClass('mand');;

                                        }else
                                        {

                                        $(".our_vehicle").css('display','none');
                                        $(".our_vehicle_no").attr('required',false);
                                        $(".hired_vehicle").css('display','none');
                                        $("#transporter_id").attr('required',false);
                                        $("#trns_rate_type").attr('required',false);
                                        $("#trrate").attr('required',false);

                                        $(".our_vehicle_no").removeClass('mand');
                                        $("#transporter_id").removeClass('mand');
                                        $("#trns_rate_type").removeClass('mand');
                                        $("#trrate").removeClass('mand');

                                       

                                        }

                                    }
                                </script>



                    

                                  <div class="col-md-3 our_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Vehicle No.</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="our_vehicle_no" id="our_vehicle_no">
                                    <option value="">Select</option>
                                    <?php 
                                    $res=$this->db->select('id,name')->from('our_vehicles')->where('status',1)->get();
                                    if($res->num_rows()>0)
                                    {
                                    foreach($res->result() as $rows)
                                    {
                                    ?>
                                    <option value="<?php echo $rows->id;?>"><?php echo $rows->name;?></option>
                                    <?php
                                    }
                                    }
                                    ?>
                                    
                                    </select>
                                </div>
                                </div>

                                <!--- FOR HIRED -->

                                <div class="col-md-3 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Select Transporter</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="transporter_id" id="transporter_id">
                                    <option value="">Select</option>
                                     <?php 
                                    $res=$this->db->select('id,name')->from('transporter_details')->get();
                                    if($res->num_rows()>0)
                                    {
                                    foreach($res->result() as $rows)
                                    {
                                    ?>
                                    <option value="<?php echo $rows->id;?>"><?php echo $rows->name;?></option>
                                    <?php
                                    }
                                    }
                                    ?>
                                    
                                    </select>
                                </div>
                                </div>


                                  <div class="col-md-2 hired_vehicle" style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate Type</label>
                                <span style="color:red;">*</span>
                                    <select class="form-control" name="trns_rate_type" id="trns_rate_type" >
                                    <option value="">Select</option>
                                    <option value="1">Per Ltr</option>
                                    <option value="2">Fixed</option>
                                    </select>
                                </div>
                                </div>

                                <div class="col-md-2 hired_vehicle"  style="display:none;">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Transporter Rate</label>
                                <span style="color:red;">*</span>
                                   <input type="number" step="0.01" name="trrate" class="form-control" id="trrate">
                                </div>
                                </div>



                            </div>

                               
                              

                            </div>

                            <div class="form-group pull-right">
                                    <input type="submit" id="saves" class="btn btn-info" value="Submit">
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
    <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

 


    <script>

        $( document ).ready(function() {
    $("#our_vehicle_no").select2({});
    $("#transporter_id").select2({});
});

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
    </script>

   
    <script type="text/javascript">


   function allow_decimal(data)
{
    var self = $("#"+data);
      self.val(self.val().replace(/[^0-9\.]/g, ''));
   if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
   {
     evt.preventDefault();
   }


}

 </script>
</body>

</html>