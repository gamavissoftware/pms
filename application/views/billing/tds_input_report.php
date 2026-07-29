<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model');
$sname='';
if($this->uri->segment(6)<>'')
{

$resteu=$this->db->select('companyname')->from('store_rack_location')->where('id',$this->uri->segment(6))->get();
if($resteu->num_rows()>0)
{
foreach($resteu->result() as $product)

$sname=$product->companyname;

}
}

$tds_amount=$CI->Salescrm_model->gettdsinput($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$this->uri->segment(6));
          
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
      

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
                        <h4 class="assign_lead">TDS INPUT REPORT FOR <?php echo $sname;?> FROM <?php echo date('d-M-Y',strtotime($this->uri->segment(3)));?> to <?php echo date('d-M-Y',strtotime($this->uri->segment(4)));?></h4>
                        <div class="underline"></div>
                        <form method="post" action="<?php echo page_url; ?>Billing/filter_tds_input" onsubmit="return validate();">
                          
                            <div class="row" style="margin-top: 20px;">
                          
                          <div class="col-md-4"></div>
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
                                            <input type="date" id="to_date" name="to_date" class="form-control mand" required="" value="<?php echo $this->uri->segment(3);?>">
                                        </div>
                                    </div>
                                    <input type="hidden" name="user" value="<?php echo $this->uri->segment(5);?>">

                                    <input type="hidden" name="company" value="<?php echo $this->uri->segment(6);?>">                                   

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

            <div class="row">
                <div class="col-sm-12">
                    <div class="col-md-4 pull-right">
                    <div class="card-assign" style="margin-top:20px;">
                        <h4 style="text-align: center;">Total TDS INPUT</h4>
                        <p style="text-align: center;font-size:20px;color:red;font-weight:bold">₹<?php echo $tds_amount;?></p>
                    </div>

                    </div>
                </div>
            </div>


            <?php
            if($this->uri->segment(3)<>'' && $this->uri->segment(4)<>'')
            {
                // $get_usernames="All";
                // $get_lead_stages="All";
                // $get_products="All";
                // $get_source="All";
                // $get_reason="All";
                // $get_qty="All";
                // $get_pack_size="All";

                // if($this->uri->segment(5)<>'' && $this->uri->segment(5)<>'NA'){


                //     $get_usernames = $CI->Salescrm_model->get_usernames(base64_decode($this->uri->segment(5)));
                    
                // }

                //  if($this->uri->segment(6)<>'' && $this->uri->segment(6)<>'NA'){

                //  $get_lead_stages = $CI->Salescrm_model->get_lead_stages(base64_decode($this->uri->segment(6)));
                // }

                //  if($this->uri->segment(7)<>'' && $this->uri->segment(7)<>'NA'){
                //     $get_products = $CI->Salescrm_model->get_products(base64_decode($this->uri->segment(7)));
                // }

                //  if($this->uri->segment(8)<>'' && $this->uri->segment(8)<>'NA'){

                //      $get_source = $CI->Salescrm_model->getfilterleadsourceapp(base64_decode($this->uri->segment(8)));
                // }

                // if($this->uri->segment(9)<>'' && $this->uri->segment(9)<>'NA'){

                //      $get_reason = $CI->Salescrm_model->get_reason(base64_decode($this->uri->segment(9)));
                // }

                // if($this->uri->segment(10)<>'' && $this->uri->segment(10)<>'NA'){

                //      $get_qty = base64_decode($this->uri->segment(10));
                // }

             


// <th style="border: 1px solid black;text-align:center; width:200px;">Pack Size</th>
                $filtercriteria='';
            ?>
           <!--  <td style="border: 1px solid black;text-align:center;color:black;">'.$product_pack_size1.'</td> -->
            <div class="row" style="margin-top:10px;">
                <div class="col-md-2"></div>
                <div class="col-sm-8">
                   <?php echo $filtercriteria;?>
                </div>
                 <div class="col-md-2"></div>
            </div>
              <?php } ?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                       
                            <table id="example" class="table table-striped table-bordered pretty">
                                <thead>
                                    <tr>
                                    <th>Sr No.</th>
                                    <th>Billing Company</th>
                                    <th>Lead Manager</th> 
                                    <th>Company Name</th>
                                    <th>Customer Name</th>
                                    <th>Invoice No.</th>
                                    <th>Product(s)</th> 
                                    <th>Basic Amount</th>
                                    <th>GST ORDER Amount</th>
                                    <th>TOTAL Amount</th>
                            
                                    <th>TDS %</th>                                  
                                    <th>TDS Input</th>
                                    </tr>
                                </thead>

                            </table>

                      
                    </div>
                </div>
            </div>

    
            <!-- end row -->
          


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

  
     <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            
        
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "deferRender": true,
                 dom: 'lBfrtip',
        buttons: [
         {
                extend: 'excelHtml5',
                title: 'Lead Data Dump'
            }

        
        ],
          pageLength:50,

            "sAjaxSource": "<?php echo page_url; ?>Billing/tds_input_report_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
                "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'billing_company'},
                        { mData: 'lead_manager' },
                        { mData: 'company_name' },
                        { mData: 'customer_name' },
                        { mData: 'invoice_no' },
                        { mData: 'product' },
                        { mData: 'basic_order_amount' },
                        { mData: 'gst_order_amount' },
                        { mData: 'order_amount' },
                        { mData: 'tds_per' },
                        { mData: 'tds_input' }
                    

                ]
            });


           
        });



function validate()
{

    var isValid=0;
    $(".mand").each(function() {
    var element = $(this).val();
    if (element=="") {

    isValid=1;
    }
    });


    if(isValid==0)
    {
    return true;
    }else
    {
    alert('Fields marked with (*) are mandatory');
    return false;
    }
    
}

$( document ).ready(function() {

$('.select8').select2({});
});
    </script>


</body>

</html>