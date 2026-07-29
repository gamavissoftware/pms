<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getAllUnits = $CI->salescrm->getAllUnits();
$getAllVendors = $CI->salescrm->getAllVendors();
$getAllProducts = $CI->salescrm->getAllProducts();
$getEditApproval = $CI->salescrm->getEditApproval($this->uri->segment(3));

  $current_date = '';
  $hpcl_location = '';
  $payment_terms = '';
  $credit_period = '';
  $transportation = '';
  $transportation_rate = '';

  if($getEditApproval != '') {
    foreach ($getEditApproval as $row);
        $current_date = $row->current_date;
        $hpcl_location = $row->name;
        $approvalid="TYPE-1".$row->auto_gen_code;
        $addedby=$row->first_name." ".$row->last_name;
        $addedON=date('d-M-Y',strtotime($row->added_on));
        if($row->payment_terms == 1) {
          $payment_terms = 'ADVANCE';
          $days='';
        } else if($row->payment_terms == 2) {
          $payment_terms = 'CREDIT PERIOD';
          $days=$row->credit_period." Days";
        } else {
          $payment_terms = '';
          $days='';
        }

        $credit_period = $row->credit_period;
        if($row->transportation == 1) {
          $transportation = 'Included';  
        } else if($row->transportation == 2) {
          $transportation = 'Not Included'; 
        } else {
          $transportation = '';
        }
        $transportation_rate = $row->transportation_rate;
  }else
  {
    echo "Invalid Page"; exit;
  }


$getEditProductApproval = $CI->salescrm->getEditProductApproval($this->uri->segment(3));

$getinventory_details=$CI->salescrm->getInventory_details($this->uri->segment(3));
if(count($getinventory_details)>0)
{
  $purchasedate=$getinventory_details['currentdate'];
  $bill_no=$getinventory_details['bill_no'];
  $partyname=$getinventory_details['partyname'];
  $added_by=$getinventory_details['added_by'];
}else
{
  $purchasedate='';
  $bill_no='';
  $partyname='';
  $added_by='';

}



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

  <!-- DataTables -->

  <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

  <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

  <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">



  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



  <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

  <style>

     .marginbottom {

      margin-bottom: 20px;

    }


.table > thead > tr > th
{
  border:1px solid #000 !important;
}

.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td
{
  border:1px solid #000 !important;
}


    .iii[disabled] {

      pointer-events: none;

      opacity: 0.49;

    }



    .iii i {

      position: absolute;

      top: 50%;

      left: 50%;

      transform: translate(-50%, -50%);

      z-index: 1;

      font-size: 100px;

    }

    .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid lightgray;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 50px;
            padding-bottom: 50px;
            z-index: 100;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            color: #818181;
        }

        .sidenav .closebtn {
            position: absolute;
            top: -15px;
            right: 5px;
            font-size: 36px;
            margin-left: 50px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }

        .todo-box {
            border: 1px solid lightgray;
            border-radius: 5px;
            height: none;
        }

        .all-notes {
            height: 50px;
            padding: 15px;
            border-bottom: 1px solid #cdcdcd;
        }

        .all-notes p {
            font-weight: 600;
        }

        .all-notes i {
            color: #3d48c4;
        }

        .to-list {
            padding: 20px;

        }

        .todo-list {
            margin: 10px 0;
            overflow-y: auto;
            height: 535px;
        }

        .todo-list .todo-item {
            padding: 15px;
            margin: 5px 0;
            border-radius: 0;
            background: #f7f7f7;
        }

          .search-page h3{
font-weight: 600;
        }

        .search-page h3 span{
            background: #fff1ea;
            color: #f9ab00;
            border-radius: 5px;
            padding: 5px;
            font-size: 20px;
        }

        .search-page p{
            color: black;
            /* font-size: 14px; */
            margin: 0;
        }

        .search-page p i{
            margin-right: 10px;
        }

        .search-page h5 {
            font-size: 17px;
            margin: 0px;
            padding: 5px 0px;
            font-weight: 700;
            border-bottom: 1px solid #25272e52;
            margin-bottom: 10px;
        }
        

        .search-page table{
            width: 100%;
          
           
        }

        .search-page table th{
            padding: 5px;
            text-align: center;
            border: 1px solid lightgray;
            color: black;
            background-color: whitesmoke;
        }

        .search-page table td{
            border: 1px solid lightgray;
            padding: 5px;
            text-align: center;
            color: black;
        }
  </style>


</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>


        <div class="wrapper">
          <div class="container">
            <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <h4 class="page-title text-center"><a href="javascript:;" onclick="printme();" class="btn btn-info">Print</a></h4>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                         
                          <h4 class="page-title text-center">Purchase Entry</h4>
                      </div>
                  </div>
              </div>
              <div class="row card-box">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                  <table class="table table-bordered">
                  <thead>
                     <tr style="background-color:#f5f5f5;">
                  <th colspan="6" style="text-align: center;">Approval Overview</th>
                  </tr>
                  <tr style="background-color:#f5f5f5;">
                  <th>Approval ID</th>
                  <th>Approval Date</th>
                  <th>Approved Location</th>
                  <th>Payment Terms</th>
                  <th>Approval Added By</th>
                  <th>Approval Added On</th>

                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                  <td><?php echo $approvalid;?></td>
                  <td><?php echo date('d-m-Y', strtotime($current_date));?></td>
                  <td><?php echo $hpcl_location;?></td>
                  <td><?php echo $payment_terms;?><br/><?php echo $days;?></td>
                  <td><?php echo $addedby;?></td>
                  <td><?php echo $addedON;?></td>
                  </tr>
                  
                  </tbody>
                  </table>
                </div>
                </div>
            
              <div class="row" style="margin-top:10px;">
                <div class="col-sm-12">
                  <div class="card-box">
                    <div class="row">

                      <div class="col-md-4"></div>

                      <div class="col-md-4">
                       <table class="table table-bordered">
                  <thead>
                     <tr style="background-color:#f5f5f5;">
                  <th colspan="6" style="text-align: center;">Party Overview</th>
                  </tr>
                  <tr style="background-color:#f5f5f5;">
                 
                  <th  style="text-align: center;">Purchase Date</th>
                  <th  style="text-align: center;">Party</th>
                  <th  style="text-align: center;">Bill no.</th>
                 

                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                  <td  style="text-align: center;"><?php echo $purchasedate;?></td>
                  <td  style="text-align: center;"><?php echo $partyname;?></td>
                  <td style="text-align: center;"><?php echo $bill_no;?></td>
                 
                  </tr>
                  
                  </tbody>
                  </table>
                </div>
                </div>


                 <div class="row">

                    

                      <div class="col-md-12">
                       <table class="table table-bordered">
                  <thead>
                     <tr style="background-color:#f5f5f5;">
                  <th colspan="6" style="text-align: center;">Purchase Details</th>
                  </tr>
                  <tr style="background-color:#f5f5f5;">
                  <th style="text-align:center;">Product</th>
                  <th  style="text-align:center;">MOQ</th>
                  <th  style="text-align:center;">Qty.</th>
                  <th  style="text-align:center;">Rate.</th>
                  <th  style="text-align:center;">Credit Note./VLI</th>
                  <th  style="text-align:center;">Credit Note Amount</th>
                  </tr>
                  </thead>
                  <tbody>

                      <?php 
                      $total=array();
                      $total[]=0;
                      if($getEditProductApproval != '') {
                                foreach($getEditProductApproval as $row1) {
                                  $getProductName = $CI->salescrm->getProductName($row1->product_id);
                                  $unit_name = $CI->salescrm->getUnitName($row1->pack_size);
                                  $getinventory_product_details=$CI->salescrm->getInventory_product_details($row1->id);

                                  if($row1->moq>0 && $row1->moq<>'')
                                  {
                                    $moq=$row1->moq." ".$unit_name;
                                  }else{
                                     $moq="-";

                                  } 

                                  ?>

                  <tr>
                  <td  style="text-align:center;"><?php echo $getProductName;?></td>
                  <td  style="text-align:center;"><?php echo $moq;?></td>
                  <td style="text-align:center;"><?php echo $getinventory_product_details;?> <?php echo $unit_name;?></td>
                  <td style="text-align:center;"><?php echo $row1->approved_price;?>/<?php echo $unit_name;?></td>
                  <td style="text-align:center;"><?php echo $row1->credit_vli;?>/<?php echo $unit_name;?></td>
                  <?php 
                  $creditamount=$getinventory_product_details*$row1->credit_vli;
                  $total[]=$creditamount;
                  ?>
                  <td style="text-align:center;font-size: 14px;color:red;font-weight:bold;">₹ <?php echo $creditamount;?></td>
                  </tr>
                <?php } } ?>
                  
                  <tr>
                    <td colspan="5" style="text-align:right;font-size: 17px;font-weight: bold;">Total Credit Note</td>
                    <td style="text-align:center;font-size: 14px;color:red;font-weight:bold;">₹ <?php echo array_sum($total);?></td>
                  </tr>
                  </tbody>
                  </table>
                </div>
                </div>


               
          <?php $this->load->view('common/footer'); ?>


        </div>
      </div>



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

      <!-- App js -->

      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

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



      <!-- Datatable init js -->

      <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>



      <!-- App js -->

      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

      <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

      <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

      <script>
        $(document).ready(function() {
            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
             });

            $('#man_date0').datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
             });
        });
      </script>

      <script type="text/javascript">

              function remove_row(id) {
                $(document).on('click', '#delete_btn', function(){
                    $(this).parents("#remove_product"+id).remove();
                });
              }

              function getDatePickers(j) {
                $('#man_date'+j).datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
               });
              }


              function compare_moq_qty(id)
              {

               var qty = parseFloat($("#qty"+id).val());
               var moq = parseFloat($("#moq"+id).val());


               if(qty<moq && moq>0)
               {
                alert('Purchase QTY cannot be less than MOQ');
                $("#qty"+id).val('');
               }

              }

           function validate() {

         $("#saveform").attr('disabled',false);
         $("#saveform").val('Submit');

         $("#loginForm :input").attr('required',false);
         var isValid=0;
          $("#loginForm .mand").each(function() {

                var element = $(this).val();

                if (element=="") {

                    isValid=1;

                }

            });



        if(isValid==0) {

            $("#saveform").attr('disabled',true);

            $("#saveform").val('Please Wait..');

                 return true;

        } else {



             $("#saveform").attr('disabled',false);

             $("#saveform").val('Submit');
             alert('All Fields marked with * are mandatory');
            return false;

        }



    }

    function printme()
    {
       window.print();
     }
      </script>


</body>

</html>