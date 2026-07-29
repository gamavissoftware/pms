<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$collection_ref=$CI->Salescrm_model->get_collection_ref($this->uri->segment(3),$this->uri->segment(4));
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
   
     
    <style>
        table.manglesh thead th {


            background: red;


            color: #fff;


            font-weight: bold;


        }





        #pageloader {


            background: rgba(255, 255, 255, 0.8);


            display: none;


            height: 100%;


            position: fixed;


            width: 100%;


            z-index: 9999;


        }


        #pageloader img {


            left: 50%;


            margin-left: -32px;


            margin-top: -32px;


            position: absolute;


            top: 50%;


        }

        .ledger_table {
            width: 100%;
            border: 1px solid lightgray;
            font-family: 'Montserrat', sans-serif;
            margin-top: 20px;
        }

        .ledger_table th {
            padding: 5px;
            color: black;
            background-color: whitesmoke;
            font-weight: 600;
        }

        .ledger_table td {
            padding: 5px;
            color: black;
        }
    </style>


</head>

<body>
    <header id="topnav">
        
<style>

.help_ticket{
    color: white;
    border: 1px solid white;
    text-align: center;
    margin-top: 10px;
    padding: 5px;
    border-radius: 5px;
    width: 100%;
}

 .badge1 {

        position:relative;

    }

    .badge1[data-badge]:after {

        content:attr(data-badge);

        position:absolute;

        top:-10px;

        right:-10px;

        font-size:12px;

        font-weight:bold;

        background:black;

        color:white;

        width:18px;height:18px;

        text-align:center;

        border-radius:50%;

        box-shadow:0 0 1px #333;

    }

    .quotecss{

        color: #fff;

    text-align: center;

    padding-top: 26px;

    font-size: 13px;

        font-weight:bold;

    }

    #topnav .topbar-main {

  /* background-color: #2986CE; */

  /* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */
     background-color: #353d4a;
    height: 54px;
    box-shadow: 1px 1px 5px #353d4a;
    position:fixed;
    width:100%;
    z-index:50;

}

.usernamecss {

    margin-top:14px;

  }

@media only screen and (max-width: 600px) {

  .usernamecss {

    margin-top:0px;

  }

}



.secondul

{

    max-height:450px;

    overflow-y:auto;

}

.secondulss{

    max-height:200px;

    overflow-y:auto;

}

 </style>

</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


   <div class="wrapper ">
        <div class="container-fluid">
            <div class="dashboard-header">
                <h1>Sunder Industrial Oil HPCL Ledger Report</h1>
                <hr>
            </div>
           
            <div class="row mt-4">
                
                <!-- <div class="col-sm-1"></div> -->
                <div class="col-sm-12">
                    <table class="ledger_table" align="center" border="1" rule="all">
                        <tbody><tr>
                            <th width="5%">Date</th>
                          <!--   <th width="15%">Journal Entry #</th> -->
                            <th width="35%">Description</th>
                            <th width="15%">Debit</th>
                            <th width="15%">Credit</th>
                            <th width="15%">Balance</th>
                        </tr>

                                                <tr>
                            <td></td>
                            <td>Project Awarded</td>
                            <td>0</td>
                            <td>1000.00</td>
                            <td>1000.00</td>
                        </tr>
                    
                                       
                                            <tr>
                            <th colspan="4" style="text-align:center;">Total Pending Payment</th>
                            <th>-5400</th>
                        </tr>
                    </tbody></table>
                </div>
                <!-- <div class="col-sm-1"></div> -->
            </div>

             <?php $this->load->view('common/footer'); ?>
        </div>
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