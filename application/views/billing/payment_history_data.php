<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model');
$userstatus=$DI->Lead_model->teamleadersearch($_SESSION['logged_in']['user_id']);
$pack = array();           
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?></title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
  <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
      

     <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
       <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
    <?php } ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <?PHP
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    ?>
    <style>

.select2-container .select2-selection--single
{
    height: 32px !important;
}
    .select2-container
    {
        width: 100% !important;
    }
        table.pretty thead th {
            text-align: center;
            background: <?php echo $LOGO->colorcode; ?>;
            color: #fff;
            font-size: 12px;
        }

        table.pretty td {
            text-align: center;
            font-size: 12px;
        }

        .feedback {
            background-color: <?php echo $LOGO->colorcode; ?>;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            border-color: #46b8da;
        }

        #mybutton {
            position: fixed;
            bottom: -4px;
            right: 10px;
        }

        .card-assign {
            border: 1px dashed grey;
            padding: 15px;
        }

        .assign_lead {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }

        .underline {
            height: 2px;
            width: 100px;
            background-color: red;
            margin: auto;
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

          


            <div class="row">
                <div class="col-sm-12">
                    <div class="card-assign" style="margin-top:20px;">
                        <h4 class="assign_lead">Payment History Report</h4>
                        <div class="underline"></div>
                        <form method="post" action="<?php echo page_url; ?>Billing/filter_payment_history_data" onsubmit="return validate();">
                          
                            <div class="row" style="margin-top: 20px;">
                        
                              
                                <?php 
                                    $d1='';
                                    $d2='';
                                    if($this->uri->segment(4)<>'')
                                    {
                                        $d1=date('Y-m-d',strtotime($this->uri->segment(4)));
                                    } 


                                     if($this->uri->segment(5)<>'')
                                    {
                                        $d2=date('Y-m-d',strtotime($this->uri->segment(5)));
                                    } 
                                    ?>
                            <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">From</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <input type="date" id="from_date" name="from_date" class="form-control mand" required="" value="<?php echo $d1;?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">To</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <input type="date" id="to_date" name="to_date" class="form-control mand" required="" value="<?php echo $d2;?>">
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <label for="field-1" class="control-label">Our Firm</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <select id="our_company" name="our_company" class="form-control" required onchange="getbank_account();">
                                                <option value="ALL" <?php if($this->uri->segment(3)=='ALL'){?> selected <?php } ?>>ALL</option>

                                                <?php 
                                                $r=$this->db->select('companyname,id')->from('store_rack_location')->where('status',1)->get();
                                                if($r->num_rows()>0)
                                                {
                                                foreach($r->result() as $row)
                                                {
                                                ?>
                                                <option value="<?php echo $row->id;?>" <?php if($row->id==$this->uri->segment(3)){?> selected <?php } ?>><?php echo $row->companyname;?></option>

                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Bank Account</label>
                                            <span id="error_from_date" style="color:red;">*</span>
                                            <select id="bank" name="bank" class="form-control" required="">
                                                <option value="ALL">ALL</option>


                                            </select>
                                        </div>
                                    </div>


                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Customer</label>
                                            <span id="error_from_date" style="color:red;">*</span>
                                            <select id="customer" name="customer" class="form-control selectcustomer" required="">
                                                <option value="ALL">ALL</option>


                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Payment Mode</label>
                                           <span id="error_from_date" style="color:red;">*</span>
                                            <select id="type" name="type" class="form-control" required="">
                                                <option value="ALL" <?php if($this->uri->segment(6)=='ALL'){?> selected <?php } ?>>ALL</option>
 
                                               
                                                <option value="1" <?php if(1==$this->uri->segment(6)){?> selected <?php } ?>>Cheque</option>

                                                  <option value="2" <?php if(2==$this->uri->segment(6)){?> selected <?php } ?>>Cash</option>

                                                    <option value="3" <?php if(3==$this->uri->segment(6)){?> selected <?php } ?>>Neft</option>

                                                
                                            </select>
                                        </div>
                                    </div>

                                    

                                </div>


                            <div class="row">
                                 <div class="col-sm-12">
                                    <div class="text-center" style="margin-top:30px;">
                                        <input type="submit" class="btn btn-success btn-md" value="Generate Report" style="font-size: 16px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <?php if($this->uri->segment(3)!='' && $this->uri->segment(4)!='' && $this->uri->segment(5)!='' && $this->uri->segment(6)!='' ){ ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                       
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                    <tr>
                                    <th>Sr No.</th>
                                    <th>Payment Date</th>
                                    <th>Customer</th>
                                    <th>Bank Account</th>
                                    <th>Payment Mode</th>
                                    <th>Cheque No./ Date</th>
                                    <th>Payment Amount</th>
                                    <th>Payment Type</th>
                                    <th>Bills Settled</th>
                                    <th>Payment Entered On</th>
                                    <th>Payment Added By</th>
                                    <th>Action</th>
                                   
                                </tr>
                                </thead>

                            </table>


                      
                    </div>
                </div>
            </div>
        <?php } ?>
    
            <!-- end row -->
          


            <!-- Footer -->
            <?php $this->load->view('common/footer'); ?>
            <!-- End Footer -->

        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->


    <!-- jQuery  -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

   
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
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
    <?php } ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>
        <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    
    <?php if($this->uri->segment(3)!='' && $this->uri->segment(4)!='' && $this->uri->segment(5)!='' ){ ?>
    <script>
        $(document).ready(function() {
                getbank_account();
        
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "deferRender": true,
              
                pageLength:50,
                "sAjaxSource": "<?php echo page_url; ?>Billing/payment_history/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>/<?php echo $this->uri->segment(8);?>",
                "aoColumns": [
                    { mData: 'sr_no' } ,
                    { mData: 'payment_date' },
                    { mData: 'customer' },
                    { mData: 'bank_account' },
                    { mData: 'payment_mode' },
                    { mData: 'cheque_no' },
                    { mData: 'amount' },
                    { mData: 'bill_type' },
                    { mData: 'payment_settled' },
                    { mData: 'addedOn' },
                    { mData: 'addedby' },
                    { mData: 'editpaymenthistorydata' }
                ]
            });

        });



</script>
<?php } ?>

<script type="text/javascript">
    function delete_payment_data(paymentid,particular_id)
    {
        if(confirm('Are you sure? All the invoice adjusted will be reverted back for pending payment'))
        {
            document.location="<?php echo page_url;?>Billing/remove_payment/"+paymentid+"/"+particular_id+"/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>";
        }

    }

function getbank_account()
{
var our_company=$("#our_company").val();
if(our_company!='ALL')
{
$.ajax({
type: "post",
url: "<?php echo page_url; ?>Open_leads/companybankaccount",
data: "our_company="+our_company+"&selected=<?php echo $this->uri->segment(7);?>",
success: function(data) {
$("#bank").html(data);
}
});


$.ajax({
type: "post",
url: "<?php echo page_url; ?>Customer/get_customer_by_company_for_payment",
data: "company="+our_company+"&selected=<?php echo $this->uri->segment(8);?>",
success: function(data) {
$("#customer").html(data);
}
});



}else
{
$("#bank").html("<option value='ALL'>ALL</option>");
$("#customer").html("<option value='ALL'>ALL</option>");

}

}

$(document).ready(function() {
$(".selectcustomer").select2(); 
});

</script>



</body>

</html>