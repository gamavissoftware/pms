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

          


            <form action="<?php echo page_url;?>Payment/cheque_bounce_entry/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>" method="post">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-assign" style="margin-top:20px;">
                        <h4 class="assign_lead">Payment Details For Cheque <?php echo base64_decode($this->uri->segment(5));?></h4>
                    </div>
                </div>
            </div>
                     

            <?php if($this->uri->segment(3)!=''){ ?>
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
                                                                       
                                </tr>
                                </thead>
                                <tbody>

                              <?php 
                              $invoices_used=array();
        $type=$this->uri->segment(3);
        $company=$this->uri->segment(4);
        $cheque_customer=$this->uri->segment(5);
         $this->db->select('b.bank,a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id');
            $this->db->where('a.payment_type',1);
            if($type==1)
            {
                $this->db->where('a.cheque_no',base64_decode($cheque_customer));
            }else
            {
                $this->db->where('b.customer_id',$cheque_customer);
            }
            $this->db->order_by('a.payment_date','DESC');


        $restey=$this->db->get();
    if($restey->num_rows()>0)
    {
        $i=1;
        foreach($restey->result() as $row)
        {
            $invoice='';
            $cno='';
            $cdate='';

            if($row->billtype==1)
            {
                $pay_type="<span class='btn btn-xs btn-warning'>FIFO</span>";
                $edit=$row->payment_id;
            }else
            {
                
                $pay_type="<span class='btn btn-xs btn-success'>AGAINST BILL</span>";

                if($row->bills<>'')
                {
                    $invoice=$CI->Salescrm_model->get_invoice_no($row->bills);
                }else
                {
                    $invoice='';
                }
                $edit="<a href='javascript:;' onclick='delete_payment_data(".$row->payment_id.",".$row->customerpart.");'><i class='fa fa-trash'></i></a>|".$row->payment_id;
            }

            if($row->payment_type==1)
            {
                $PT="<strong>Cheque</strong>";
                $cno="<strong>".$row->cheque_no."</strong>";
                $cdate="<strong>".date('d-m-Y',strtotime($row->cheque_date))."</strong>";

            }else if($row->payment_type==2)
            {
                $PT="<strong>Cash</strong>";
            }else if($row->payment_type==3)
            {
                $PT="<strong>NEFT</strong>";
                $cno="<strong>".$row->neft_trans_no."</strong>";
            }else
            {
                $PT='';
            }


            $name=$CI->Salescrm_model->getusername($row->addedBy);
           
            $bank11=$CI->Salescrm_model->getbankaccount($row->bank);
           
            $a=str_replace("'",'',$row->bills);
            $inv=explode(',',$a);
            $invoices_used=$inv;

        ?>
                                    <tr>
                                        <td>

                                            <input type="hidden" name="payment_particular_id[]" value="<?php echo $row->customerpart;?>">
                                            <?php echo $i;?>
                                                

                                            </td>
                                        <td><?php echo date('d-M-Y',strtotime($row->payment_date));?></td>
                                        <td><?php echo $row->company_name;?></td>
                                        <td><?php echo $bank11;?></td>
                                        <td><?php echo $PT;?></td>
                                        <td><?php echo $cno."<br/>".$cdate;?></td>
                                        <td><?php echo $row->amount;?></td>
                                        <td><?php echo $pay_type;?></td>
                                        <td><?php echo $invoice;?></td>
                                        <td><?php echo date('d-M-Y',strtotime($row->addedOn));?></td>
                                        <td><?php echo $name;?></td>
                                      
                                    </tr>

                                <?php }  } ?>
                                </tbody>

                            </table>


                      
                    </div>
                </div>
            </div>
        <?php } ?>


        <div class="col-md-12 card-box">
        <div class="col-md-6">
            <div class="col-md-12">
            <h3>Invoice which will get affected</h3>

            </div>
            <div class="col-md-12">
                 <input type="hidden" name="cheque_no" value="<?php echo base64_decode($this->uri->segment(5));?>">
                <table class="table table-bordered">
                <thead>
                <tr>
                <th>Billing Company</th>
                <th>Customer</th>
                <th>Invoice No</th>
                </tr>
                </thead>
                <tbody>
            <?php 
            //echo "<pre>"; print_r($invoices_used); exit;
            foreach($invoices_used as $invoices_used1)
            {
                $invu=$CI->Salescrm_model->getOrderDetails($invoices_used1);
                if($invu<>'')
                {
                foreach($invu as $row);
            ?>
                <tr>
                <td>

                    <input type="hidden" name="order_id[]" value="<?php echo $invoices_used1;?>">
                   
                    <?php echo $row->billingcompany;?>
                        
                    </td>
                <td><?php echo $row->bill_to;?></td>
                <td><?php echo $row->invoice_no;?></td>
                </tr>
            <?php } } ?>
               
                </tbody>
                </table>

            </div>
        </div>
         <div class="col-md-6">
            <div class="col-md-12" style="margin-top:25px;">
            <div class="form-group">
                <label>Remarks <span style="color:red;">*</span></label>
                <textarea name="rmk" id="rmk" class="form-control" required></textarea>
            </div>
        </div>
         </div>

         <div class="col-md-12" style="margin-top:20px;">
             <div class="col-md-4"></div>
             <div class="col-md-4 text-center">
                 <input type="submit" name="sub" id="sub" class="btn btn-success" value="Submit">
             </div>
         </div>
        </div>
    
            <!-- end row -->
          


        </form>
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
    
    <?php if($this->uri->segment(3)!='' ){ ?>
    <script>
        $(document).ready(function() {
        
        
            $('#example').dataTable({
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
$("#cheque").select2(); 
});

</script>



</body>

</html>