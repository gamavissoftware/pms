<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$getAllProducts = $CI->salescrm->getAllProducts();
$getAllTransporters = $CI->salescrm->getAllTransporters();
  $getRackLocation = $CI->salescrm->getRackLocation();
$id=$this->uri->segment(3);
$restey=$this->db->select('a.interest,a.payment_type,a.currentdate,a.bill_no,a.party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.hpcl_billing_company')->from('inventory a')->where('id',$id)->get();
if($restey->num_rows()>0)
{
  foreach($restey->result() as $row);
  $pdate=date('d-m-Y',strtotime($row->currentdate));
  $billno=$row->bill_no;
  $hpcl_billing_company=$row->hpcl_billing_company;
  $party=$row->party;
  $payment_type=$row->payment_type;
  $credit_days=$row->credit_days;
  $transport_type=$row->transport_type;
  $transporter=$row->transporter;
  $vehicle_no=$row->vehicle_no;
  $vehicle_type=$row->vehicle_type;
  $transporter_rate=$row->transporter_rate;
   $interest=$row->interest;

}else
{
echo "Invalid Link"; exit;
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

    .select2-container
{
  width: 100% !important;
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
                          <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
                          <h4 class="page-title text-center">EDIT PURCHASE ENTRY</h4>
                      </div>
                  </div>
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12"><?php echo $this->session->flashdata('message');?></div>
              </div>
              <form method="post" action="<?php echo page_url;?>Inventory/update_inventory/<?php echo $id;?>" onsubmit="return validate();">
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box">
                    <div class="row">
                      <div class="col-sm-12 col-xs-12 col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Purchase Date</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="current_date" <?php if($_SESSION['logged_in']['role']==1){?> id="datepicker"  <?php }else{?> readonly <?php } ?> class="form-control mand" autocomplete="nope" value="<?php echo $pdate;?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Bill No</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="bill_no" class="form-control mand" autocomplete="nope" value="<?php echo $billno;?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Company</label>
                      <span id="error_vendor_name" style="color:red;">*</span>
                      <select class="form-control" name="pur_company" id="pur_company" required onchange="getParty()">
                        <option value="">Select Company</option>
                        <?php if ($getRackLocation != '') {
                          foreach ($getRackLocation as $row1) { ?>
                        <option value="<?php echo $row1->id; ?>"  <?php if($hpcl_billing_company==$row1->id){?> selected <?php } ?>><?php echo $row1->companyname; ?></option>
                        <?php }
                          } ?>
                      </select>
                      
                    </div>
                  </div>
                        <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Party</label>
                              <span style="color:red;">*</span>
                              <select name="party" class="form-control mand" id="pur_party" required>
                                <option value="">Select Party</option>
                                <?php
                                //->where('id',$party) 
                               $reste=$this->db->select('id,name')->from('vendors')->where('status',1)->get();
                               if($reste->num_rows()>0)
                               {
                                foreach($reste->result() as $row)
                                {
                               ?>
                               <option value="<?php echo $row->id;?>" <?php if($party==$row->id){?> selected <?php } ?>><?php echo $row->name;?></option>
                                <?php 
                                }
                                }
                                ?>
                              </select>
                            </div>
                        </div>


                          <div class="col-md-3">
                      <label>Payment Type<span style="color: red;">*</span></label>
                      <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select class="form-control mand" name="payment_type" id="payment_type" required="" onchange="checkpdf()">
                          <option value="">SELECT PAYMENT TYPE</option>
                          <option value="2" <?php if($payment_type==2){?> selected <?php } ?>>Cash</option>
                          <option value="3"  <?php  if($payment_type==3){?> selected <?php } ?>>Online</option>
                          <option value="4"  <?php  if($payment_type==4){?> selected <?php } ?>>PDC</option>
                          <option value="5"  <?php  if($payment_type==5){?> selected <?php } ?>>Credit</option>
                          <option value="6"  <?php  if($payment_type==6){?> selected <?php } ?>>Advance</option>
                        </select>
                      </span>
                    </div>
                    <script type="text/javascript">
                      $(document).ready(function() {
                      
                          var payment_type = $("#payment_type").val();
                          if (payment_type == 4 || payment_type == 5) {
                              $("#credit_days").addClass('mand');
                              $("#paymentterms").css('display', '');
                          } else {
                              $("#credit_days").removeClass('mand');
                              $("#paymentterms").css('display', 'none');
                          }
                          checkpdf();
                      });
                      
                      
                      function checkpdf() {
                          var payment_type = $("#payment_type").val();
                          if (payment_type == 4 || payment_type == 5) {
                              $("#credit_days").addClass('mand');
                              $("#paymentterms").css('display', '');
                          } else {
                              $("#credit_days").removeClass('mand');
                              $("#paymentterms").css('display', 'none');
                          }
                      
                      }
                    </script>

                          <div class="col-md-3" style="display:none">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Credit Days</label>
                              <span style="color:red;">*</span>
                              <input type="number" name="credit_days" class="form-control" autocomplete="nope" value="<?php echo $credit_days;?>">
                            </div>
                        </div>


                         <div class="col-md-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Interest %</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="interest"  id="interest" class="form-control mand " autocomplete="nope" value="<?php echo $interest;?>" oninput="allow_decimal('interest')" >
                            </div>
                        </div>

                      </div>
                    </div>


                    <?php 
                    $rest=$this->db->select('a.id,a.product,a.qty,a.pack_size,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.name,b.shortname')->from('inventory_details a')->join('units b','a.pack_size=b.id')->where('inventory_id',$id)->get();
                    if($rest->num_rows()>0)
                    {
                      foreach($rest->result() as $prow)
                      {
                    ?>
                    <hr>
                    <input type="hidden" name="detail_id[]" value="<?php echo $prow->id;?>">
                    <div class="row">
                      <div class="col-sm-12 col-xs-12 col-md-12">
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Product</label>
                              <span style="color:red;">*</span>
                              <select name="edit_product<?php echo $prow->id;?>" id="edit_product<?php echo $prow->id;?>" class="form-control mand">
                              
                                <?php if($getAllProducts != '') {
                                        foreach($getAllProducts as $row) { if($row->id==$prow->product){ ?>
                                            <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option>
                                      <?php  } } 
                                           } ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Qty</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="edit_qty<?php echo $prow->id;?>" class="form-control mand allow_decimal" autocomplete="nope" step="any" value="<?php echo $prow->qty;?>" id="edit_qty<?php echo $prow->id;?>" readonly>
                            </div>
                          </div>
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Unit</label>
                              <span style="color:red;">*</span>
                              <select name="edit_pack_size<?php echo $prow->id;?>" id="edit_unit<?php echo $prow->id;?>" class="form-control mand" autocomplete="nope">
                                <option value="<?php echo $prow->pack_size;?>"><?php echo $prow->name;?></option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-2 col-2">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Rate/<span><?php echo $prow->name;?></span></label>
                              <span style="color:red;">*</span>
                              <input type="text" name="edit_rate<?php echo $prow->id;?>"  class="form-control mand allow_decimal" autocomplete="nope" step="any" value="<?php echo $prow->rate;?>" id="edit_rate<?php echo $prow->id;?>" onkeyup="calculatetaxablevalue_edit(<?php echo $prow->id;?>);" onkeyup="allow_decimal();">
                            </div>
                          </div>

                          <?php 
                          $taxvalue=$prow->rate*$prow->qty;
                          ?>
                            <div class="col-md-3 col-3">
                            <div class="form-group">
                            <label for="field-1" class="control-label">Taxable Value</label>
                            <span style="color:red;">*</span>
                            <input type="text" name="taxablevalue_edit<?php echo $prow->id;?>" onkeyup="calculateratebytaxableqty_edit(<?php echo $prow->id;?>);" id="taxablevalue_edit<?php echo $prow->id;?>" value="<?php echo $taxvalue;?>" class="form-control mand allow_decimal" autocomplete="nope" step="any">
                            </div>
                            </div>


                           <div class="col-md-3 col-3"  style="display:none;">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Lot No</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="edit_lot_no<?php echo $prow->id;?>" class="form-control" autocomplete="nope" value="<?php echo $prow->lot_no;?>">
                            </div>
                          </div>
                          <?php 
                          $restey=$this->db->select('id,batch_no')->from('inventory_batch_no')->where('inv_detail_id',$prow->id)->get();
                          ?>
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Batch No  <span style="color:red;">*</span></label>
                              <textarea name="edit_batch_no<?php echo $prow->id;?>" id="edit_batch_no<?php echo $prow->id;?>" class="form-control" autocomplete="nope" onblur="check_batch_no_edit(<?php echo $prow->id;?>);"></textarea>
                              <span id="batch_error_edit<?php echo $prow->id;?>" style="color:red;font-weight: bold;font-weight: bold;"></span>
                               <?php 
                              if($restey->num_rows()>0)
                              {
                                foreach($restey->result() as $row)
                                {
                              ?>
                              <div class="col-md-3" style="margin-bottom:20px;">
                                <span class="btn btn-default"><strong><?php echo $row->batch_no;?></strong>&nbsp;<a href='javascript:;' onclick="change_batch_no('<?php echo $row->id;?>','<?php echo $row->batch_no;?>');"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick="delete_batch(<?php echo $row->id;?>);"><i class="fa fa-trash"></i></a></span>
                              </div>
                              <div style="clear:both;height:1px;"></div>
                            <?php 
                              }
                              } 
                              ?>
                            </div>
                            
                             
                          
                          </div>
                          <div class="col-md-2 col-2" style="display:none">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Manufacturing Date</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="edit_manufacturing_date<?php echo $prow->id;?>" class="form-control manufdate" id="man_date<?php echo $prow->id;?>" autocomplete="nope" value="<?php echo date('d-m-Y',strtotime($prow->manufacturing_date));?>">
                            </div>
                          </div>

                          <?php 

                          $reoq=$this->db->select('qty,balance_left')->from('company_wise_inventory_info')->where('inventory_particular_id',$prow->id)->get();

                          if($reoq->num_rows()>0)
                          {
                            foreach($reoq->result() as $reqq);
                            if($reqq->qty==$reqq->balance_left)
                            {
                          ?>
                          <div class="col-md-1 col-1">
                          <a href="javascript:void(0)" class="btn btn-danger btn-xs"
                          style="margin-top: 32px;" onclick="delete_item(<?php echo $prow->id;?>,<?php echo $this->uri->segment(3);?>);"><i class="fa fa-close" aria-hidden="true"></i></a>
                          </div>
                          <?php 
                          }else
                          {
                          ?>
                           <div class="col-md-2 col-sm-2" style="margin-top:20px;">
                          <p style="color:red;font-weight:bold;">Cannot be Deleted this purchase has been used for billing</p>
                          </div>

                          <?php } }else{?>
                              <div class="col-md-1 col-1">
                          <a href="javascript:void(0)" class="btn btn-danger btn-xs"
                          style="margin-top: 32px;" onclick="delete_item(<?php echo $prow->id;?>,<?php echo $this->uri->segment(3);?>);"><i class="fa fa-close" aria-hidden="true"></i></a>
                          </div>                         

                          <?php } ?>


                        </div>
                    </div>
                    <?php 
                      }
                     }
                     ?>

                       <script type="text/javascript">
                        function calculatetaxablevalue_edit(i){
                            var rate = $("#edit_rate"+i).val();
                            var qty = $("#edit_qty"+i).val();
                            var taxablevalue = rate*qty;
                            var taxablevalue=taxablevalue.toFixed(2);
                            $("#taxablevalue_edit"+i).val(taxablevalue);


                        }

                        function calculateratebytaxableqty_edit(i){
                          var taxablevalue = $("#taxablevalue_edit"+i).val();
                            var qty = $("#edit_qty"+i).val();
                            var ratevalue = taxablevalue/qty;
                             var ratevalue=ratevalue.toFixed(2);
              
                            $("#edit_rate"+i).val(ratevalue);
                        }
                    </script>

                    <hr>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-4">
                          <input type="checkbox" name="add_more" id="add_more" onchange="add_more_check()">&nbsp;Add More
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="row productss" style="display:none">
                      <div class="col-sm-12 col-xs-12 col-md-12">
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Product</label>
                              <span style="color:red;">*</span>
                              <select name="product[]" id="product0" class="form-control" onchange="getunit(0);">
                                <option value="">SELECT</option>
                                <?php if($getAllProducts != '') {
                                        foreach($getAllProducts as $row) { ?>
                                            <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?>-<?php echo $row->pack_size;?></option>
                                      <?php  }
                                           } ?>
                              </select>
                              <input type="hidden" name="bulk[]" id="bulk0" value="0">
                            </div>
                          </div>
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Qty</label>
                              <span style="color:red;">*</span>
                              <input type="number" name="qty[]" id="qty0" class="form-control" autocomplete="nope" step="any">
                            </div>
                          </div>
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Unit</label>
                              <span style="color:red;">*</span>
                              <select name="pack_size[]" id="unit0" class="form-control" autocomplete="nope" onchange="checkforkgs(0);">

                              </select>
                            </div>
                          </div>


                        <div class="col-md-3 col-3" id="dens0" style="display:none;">
                        <div class="form-group">
                        <label for="field-1" class="control-label">Density</label>
                        <span style="color:red;">*</span>
                        <input type="number" name="density[]" id="density0" class="form-control" autocomplete="nope" step="any">
                        </div>
                        </div>

                          <div class="col-md-2 col-2">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Rate/<span id="unitrate0"></span></label>
                              <span style="color:red;">*</span>
                              <input type="number" name="rate[]" id="rate0" onkeyup="calculatetaxablevalue(0);"  class="form-control" autocomplete="nope" step="any">
                            </div>
                          </div>

                          <div class="col-md-2 col-2">
                          <div class="form-group">
                          <label for="field-1" class="control-label">Taxable Value</label>
                          <span style="color:red;">*</span>
                          <input type="number" name="taxablevalue[]" onkeyup="calculateratebytaxableqty(0);" id="taxablevalue0" class="form-control" autocomplete="nope" step="any">
                          </div>
                          </div>


                          <div class="col-md-3 col-3" id="bulktobulkorbulktodrum0" style="display:none;">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Select Bulk Type</label>
                        <span style="color:red;">*</span>
                        <select class="form-control" name="bulktype[]" id="bulktype0" onchange="checkbulktype(0);" value="">
                          <option value="1"> Bulk to Bulk </option>
                          <option value="2"> Bulk to Drum </option>

                        </select>
                      </div>
                    </div>

                   <div class="col-md-3" id="selectsecondproduct0" style="display: none;">
                      <div class="form-group">
                        <label for="field-1" class="control-label">Select Product</label>
                        <select name="secondproduct[]" id="product00" >
                          <option value="">SELECT</option>
                          <?php if ($getAllProducts != '') {
                            foreach ($getAllProducts as $row) { ?>
                          <option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option>
                          <?php  }
                            } ?>
                        </select>
                        </div>
                    </div>  





                           <div class="col-md-3 col-3" style="display:none">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Lot No</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="lot_no[]" id="lot_no0" class="form-control" autocomplete="nope">
                            </div>
                          </div>
                          <div class="col-md-3 col-3">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Batch No</label>
                              <span style="color:red;">*(Batch No. to be separated by comma (,))</span>
                              <!-- <input type="text" name="batch_no[]" id="batch_no0" class="form-control" autocomplete="nope"> -->
                              <textarea name="batch_no[]" id="batch_no0" class="form-control" onblur="check_batch_no(0);"></textarea>
                              <span id="batch_error0" style="color:red;font-weight: bold;font-weight: bold;"></span>
                            </div>
                          </div>
                          <div class="col-md-3 col-3" style="display:none">
                            <div class="form-group">
                              <label for="field-1" class="control-label">Manufacturing Date</label>
                              <span style="color:red;">*</span>
                              <input type="text" name="manufacturing_date[]" class="form-control" id="man_date0" autocomplete="nope">
                            </div>
                          </div>
                        <div class="col-md-1 col-1">
                          <a href="javascript:void(0)" class="btn btn-warning btn-xs add_more"
                        style="margin-top: 32px;"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        </div>
                      </div>
                    </div>

                     <hr>
                    <div class="row">
                       <div class="col-md-12"><h4 class="page-title text-center">TRANSPORTATION DETAILS</h4></div>
                      <div class="col-md-12">
                        <div class="col-md-2">
                          <select name="ttype" id="ttype" class="form-control mand" onchange="chk_owned()">
                            <option value="">Select Type</option>
                            <option value="1" <?php if($transport_type==1){?> selected <?php } ?>>Self Owned</option>
                            <option value="2" <?php if($transport_type==2){?> selected <?php } ?>>Hired</option>
                          </select>
                        </div>
                      </div>
                    </div>


                     <div class="row" id="external_transporter" style="display: none;margin-top:20px;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transporter Name<span style="color:red;">*</span></label>
                                        <select class="form-control select2" name="transporter_name" id="transporter_name" onchange="getTransporterDetails()" >
                                            <option value="">SELECT</option>
                                            <?php if($getAllTransporters != '') {
                                                    foreach($getAllTransporters as $row2) {
                                                      ?>
                                                <option value="<?php echo $row2->id;?>" <?php if($row2->id==$transporter){ ?> selected <?php } ?>><?php echo $row2->name;?></option>
                                            <?php } } ?>
                                        </select>
                                        <!-- <input type="text" class="form-control" name="name" required=""> -->
                                    </div>
                                </div>
                             
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Vehicle No.<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="vehicle_no" id="vehicle_no" value="<?php echo $vehicle_no;?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Vehicle Type<span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="vehicle_type" id="vehicle_type"  value="<?php echo $vehicle_type;?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Transporter Rate/LTR<span style="color:red;">*</span></label>
                                        <input type="number" step="any" class="form-control" id="transport_rate" name="transport_rate"  value="<?php echo $transporter_rate;?>">
                                    </div>
                                </div>
                            </div>
                        </div>


                    <div class="row" style="margin-top:20px;">
                      <div class="col-md-12 text-center">
                         <input type="submit" id="saves" class="btn btn-success" value="Submit">
                      </div>
                    </div>
                   
                  </div>
                </div>
              </div>
              </form>

              <script type="text/javascript">
              function calculatetaxablevalue(i){
              var rate = $("#rate"+i).val();
              var qty = $("#qty"+i).val();
              var taxablevalue = rate*qty;
              var taxablevalue=taxablevalue.toFixed(2);
              $("#taxablevalue"+i).val(taxablevalue);


              }

              function calculateratebytaxableqty(i){
              var taxablevalue = $("#taxablevalue"+i).val();
              var qty = $("#qty"+i).val();
              var ratevalue = taxablevalue/qty;
              var ratevalue=ratevalue.toFixed(2);
              $("#rate"+i).val(ratevalue);
              }
              </script>


          <?php $this->load->view('common/footer'); ?>


        </div>
      </div>


       <!-- Modal -->
  <div class="modal fade" id="batchchange" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <form action="<?php echo page_url;?>Inventory/change_batch_no" method="post">
        <input type="hidden" name="batch_code_id" id="batch_code_id" value="">
        <input type="hidden" name="edit_id" id="edit_id" value="">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Change Batch Code</h4>
        </div>
        <div class="modal-body">
          <p><input type="text" name="batch_code_change" id="batch_code_change" value="" required class="form-control"></p>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-success" value="Submit">
        </div>
      </div>
      
    </div>
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

$(".allow_decimal").on("input", function(evt) {
var self = $(this);
self.val(self.val().replace(/[^0-9\.]/g, ''));
if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
{
evt.preventDefault();
}
});


          chk_owned();

           $("#transporter_name").select2({ 
        
    });

            $("#product0").select2({ 
        
    });
            $("#product00").select2({ 
        
    });

            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy',
                endDate: '+0d'
             });

            $('#man_date0').datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
             });


             $('.manufdate').datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
             });
        });
      </script>

      <script type="text/javascript">
                      var j=1;

                  $('.add_more').click(function(){
                  $('.productss').append('<div class="row fieldGroups"><div class="col-md-12"><div class="col-md-3 col-3"><div class="form-group"> <label for="field-1" class="control-label">Product</label> <span style="color:red;">*</span><select name="product[]" id="product'+j+'" class="form-control mand" onchange="getunit('+j+');"><option value="">SELECT</option>  <?php if($getAllProducts != '') { foreach($getAllProducts as $row) { ?><option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?>-<?php echo $row->pack_size;?></option><?php  } } ?></select></div></div><input type="hidden" name="bulk[]" id="bulk'+j+'" value="0"><div class="col-md-3 col-3"> <div class="form-group"> <label for="field-1" class="control-label">Qty</label> <span style="color:red;">*</span> <input type="number" step="any" name="qty[]" class="form-control mand" autocomplete="nope"> </div></div><div class="col-md-3 col-3"><div class="form-group"> <label for="field-1" class="control-label">Unit</label> <span style="color:red;">*</span><select name="pack_size[]" id="unit'+j+'" class="form-control mand" autocomplete="nope" onchange="checkforkgs('+j+');"></select></div></div><div class="col-md-3 col-3" id="dens'+j+'" style="display:none;"><div class="form-group"><label for="field-1" class="control-label">Density</label><span style="color:red;">*</span><input type="number" name="density[]" id="density'+j+'" class="form-control" autocomplete="nope" step="any"></div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Rate/<span id="unitrate'+j+'"></span></label> <span style="color:red;">*</span><input type="number" step="any" name="rate[]" class="form-control mand" autocomplete="nope"> </div></div><div class="col-md-2 col-3"><div class="form-group"><label for="field-1" class="control-label">Taxable Value</label><span style="color:red;">*</span><input type="number" name="taxablevalue[]" onkeyup="calculateratebytaxableqty('+j+');" id="taxablevalue'+j+'" class="form-control mand" autocomplete="nope" step="any"></div></div><div class="col-md-3 col-3" id="bulktobulkorbulktodrum'+j+'" style="display:none;"><div class="form-group"><label for="field-1" class="control-label">Select Bulk Type</label><span style="color:red;">*</span><select class="form-control" name="bulktype[]" id="bulktype'+j+'" onchange="checkbulktype('+j+');" value=""><option value="1"> Bulk to Bulk </option><option value="2"> Bulk to Drum </option></select></div></div><div class="col-md-3" id="selectsecondproduct'+j+'" style="display: none;"><div class="form-group"><label for="field-1" class="control-label">Select Product</label><select name="secondproduct[]" id="product'+j+'1" class="form-control" ><option value="">SELECT</option><?php if ($getAllProducts != '') { foreach ($getAllProducts as $row) { ?><option value="<?php echo $row->id; ?>"><?php echo $row->instruments_name; ?>-<?php echo $row->pack_size; ?></option><?php  } } ?></select></div></div><div class="col-md-3 col-3" style="display:none"><div class="form-group"> <label for="field-1" class="control-label">Lot No</label> <span style="color:red;">*</span> <input type="text" name="lot_no[]" class="form-control" autocomplete="nope"> </div></div><div class="col-md-3 col-3"><div class="form-group"> <label for="field-1" class="control-label">Batch No</label> <span style="color:red;">*(Batch No. to be separated by comma (,))</span><textarea name="batch_no[]" id="batch_no'+j+'" class="form-control" onblur="check_batch_no('+j+');"></textarea><span id="batch_error'+j+'" style="color:red;font-weight: bold;font-weight: bold;"></span></div></div><div class="col-md-3 col-3" style="display:none"> <div class="form-group"> <label for="field-1" class="control-label">Manufacturing Date</label> <span style="color:red;">*</span> <input type="text" name="manufacturing_date[]" class="form-control" id="man_date'+j+'" autocomplete="nope"> </div></div><div class="col-md-1 col-1"> <a href="javascript:void(0)" class="btn btn-danger btn-xs remove" style="margin-top: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></a></div></div></div>');
                    getDatePickers(j);
                    initialize_data("product"+j);
                    initialize_data("product0"+j);
                  j++;

                  });

                  $(document).on('click', '.remove', function(){
                      $(this).parents(".fieldGroups").remove();
                  });

              function getDatePickers(j) {
                $('#man_date'+j).datepicker({
                  autoclose: true,
                  todayHighlight: true,
                  format: 'dd-mm-yyyy'
               });
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

        function getunit(flag) {

      var purdate=$("#datepicker").val();
      var pur_company=$("#pur_company").val();
     

      $("#bulktobulkorbulktodrum"+flag).css('display','none');
      $("#bulktype"+flag).attr('required',false);
      $("#bulktype"+flag).removeClass('mand');
          $("#dens" + flag).css('display', 'none');
          $("#density" + flag).removeClass('mand');
          $("#bulk" + flag).val(0);

          /** batch no **/
          $("#batch_no"+flag).attr('readonly',false);
          $("#batch_no"+flag).attr('required',true);
          $("#batch_no"+flag).text('');
          $("#batch_no"+flag).addClass('mand');
          $("#bmand"+flag).text('*');
          /** END**/

          var product = $("#product" + flag).val();
          if(product!='')
          {
          if(purdate!='' && pur_company!='')
          {


            //   $.ajax({
            // type: "post",
            // url: "<?php echo page_url; ?>Inventory/get_product_unit",
            // data: "prd=" + product,
            // success: function(data) {
            // var d = data.split('|');
            // $("#unit" + flag).html(d[0]);
            // $("#rate" + flag).text(d[1]);
            // if (d[2] == 4) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            // }
            // });
           


            var hpcl=$("#hpcl_vendor").val();

            if(pur_company==3 && hpcl==1)
            {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Approval/check_for_apporval_against_purchase",
            data: "prd="+product+"&pur_date="+purdate,
            success: function(data) {
            if(data>0)
            {

               $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/get_product_unit",
            data: "prd=" + product,
            success: function(data) {

            var d = data.split('|');
          
            $("#unit" + flag).html(d[0]);
            $("#rate" + flag).text(d[1]);
            
            if(d[3]=='BULK' || d[3]=='BUCKET'){
              $("#batch_no"+flag).attr('readonly',true);
              $("#batch_no"+flag).attr('required',false);
              $("#batch_no"+flag).text('Not Required');
              $("#batch_no"+flag).removeClass('mand');
              $("#bmand"+flag).text('');

            }else
            {
               $("#batch_no"+flag).attr('readonly',false);
              $("#batch_no"+flag).attr('required',true);
               $("#batch_no"+flag).text('');
               $("#batch_no"+flag).addClass('mand');
               $("#bmand"+flag).text('*');
            }

            // if (d[2] == 4) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            }
            });
            }else
            {
              alert('Product Cannot be Selected. Since No Approval has been found for this product among this purchase date');
              $("#product"+flag).val(null).trigger('change'); 
            }

      
            }
            });
          }else
          {

              $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/get_product_unit",
            data: "prd=" + product,
            success: function(data) {
            var d = data.split('|');
             
            $("#unit" + flag).html(d[0]);
            $("#rate" + flag).text(d[1]);

            if(d[3]=='BULK' || $d[3]=='BUCKET'){
              $("#batch_no"+flag).attr('readonly',true);
              $("#batch_no"+flag).attr('required',false);
              $("#batch_no"+flag).text('Not Required');
              $("#batch_no"+flag).removeClass('mand');
              $("#bmand"+flag).text('');

            }else
            {
               $("#batch_no"+flag).attr('readonly',false);
              $("#batch_no"+flag).attr('required',true);
               $("#batch_no"+flag).text('');
               $("#batch_no"+flag).addClass('mand');
               $("#bmand"+flag).text('*');
            }
            // if (d[2] == 5) {
            // $("#dens" + flag).css('display', '');
            // $("#density" + flag).addClass('mand');
            // }
            }
            });

          }




          }else
          {

          alert('Choose Purchase Date/Company First');
           $("#product"+flag).val(null).trigger('change'); 
          }

        }


        
      checkforbulkitem(flag)
      
      
      }

      function checkforbulkitem(flag)
      {

         $("#bulk" + flag).val(0);
        var product=$("#product"+flag).val();
          $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Inventory/check_for_bulk",
              data: "prd=" + product,
              success: function(data) {
                //alert(data);

                  if (data > 0) {
                   // alert(flag); 
                      $("#bulk" + flag).val(1);
                      // $("#unit" + flag).append("<option value='4'>Kilogram</option>");
                      // $("#bulktobulkorbulktodrum"+flag).css('display','');
                      // $("#bulktype"+flag).attr('required',true);
                      // $("#bulktype"+flag).addClass('mand');

                  }
            
      
              }
          });

      }


          function checkbulktype(i){
          var bulktype = $("#bulktype"+i).val();
          if(bulktype==2){
            $("#selectsecondproduct"+i).css('display','');
                      $("#product0"+i).attr('required',true);
                      $("#product0"+i).addClass('mand');

                        $("#batch_no"+i).attr('readonly',false);
                        $("#batch_no"+i).addClass('mand');
                        $("#batch_no"+i).attr('required',true);
                        $("#batch_no"+i).html('');

                    }else{
                      $("#selectsecondproduct"+i).css('display','none');
                      $("#product0"+i).attr('required',false);
                      $("#product0"+i).removeClass('mand');

                        $("#batch_no"+i).attr('readonly',true);
                        $("#batch_no"+i).removeClass('mand');
                        $("#batch_no"+i).attr('required',false);
                        $("#batch_no"+i).html('');

                    }
      }
      

         function validate() {
      $("#saves").attr('disabled',false);
      $("#saves").val('Submit');

      var isValid=0;

      $(".mand").each(function() {
        var element = $(this).val();

          if (element=="") {

            console.log($(this));
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

    function add_more_check()
    {
        if($('#add_more').is(":checked"))
        {
          $(".productss").css('display','');

           $("#product0").addClass('mand');
           $("#qty0").addClass('mand');
           $("#unit0").addClass('mand');
           $("#rate0").addClass('mand');
           // $("#lot_no0").addClass('mand');
           //$("#batch_no0").addClass('mand');
           //$("#man_date0").addClass('mand');
        }else
        {
           $(".productss").css('display','none');
           $("#product0").removeClass('mand');
           $("#qty0").removeClass('mand');
           $("#unit0").removeClass('mand');
           $("#rate0").removeClass('mand');
           // $("#lot_no0").removeClass('mand');
           $("#batch_no0").removeClass('mand');
           // $("#man_date0").removeClass('mand');
        }
    }


    function delete_item(id,invid)
    {
      if(confirm('Do you really want to delete?'))
      {
          document.location="<?php echo page_url;?>Inventory/delete_items/"+id+"/"+invid;
      }

    }

      </script>

       <script>
                            function chk_owned()
                            {
                                $("#external_transporter").css('display','none');
                                $("#transporter_name").removeClass('mand');
                                // $("#mobile_no").removeClass('mand');
                                $("#vehicle_no").removeClass('mand');
                                $("#vehicle_type").removeClass('mand');
                                $("#transport_rate").removeClass('mand');

                                var type=$("#ttype").val();

                                if(type!='')
                                {
                                    if(type==1)
                                    {
                                        $("#external_transporter").css('display','none');
                                        $("#transporter_name").removeClass('mand');
                                        // $("#mobile_no").removeClass('mand');
                                        $("#vehicle_no").removeClass('mand');
                                        $("#vehicle_type").removeClass('mand');
                                        $("#transport_rate").removeClass('mand');
                                    }else
                                    {
                                         $("#external_transporter").css('display','');
                                      
                                         $("#transporter_name").addClass('mand');
                                         // $("#mobile_no").addClass('mand');
                                         $("#vehicle_no").addClass('mand');
                                         $("#vehicle_type").addClass('mand');
                                         $("#transport_rate").addClass('mand');
                                         
                                    }

                                }
                            }

                            function getTransporterDetails() {
        var transporter_id = $("#transporter_name").val();
        // alert(transporter_id);

        $.ajax({
                type:"post",
                url:"<?php echo page_url;?>Approval/getTransporterDetails",
                data:{transporter_id: transporter_id},
                success:function(data) {
                     var arr = data.split('|');
                    var mobile_no = arr[0];
                    var address = arr[1];

                    $("#mobile_no").val(mobile_no);
                    $("#address").val(address);


                }
            });
    }


      function getParty(){
        var pur_company = $('#pur_company').val();
        // alert(pur_company);
        $.ajax({
              type: "post",
              url: "<?php echo page_url; ?>Inventory/get_company_party",
              data: "company=" + pur_company,
              success: function(data) {                  
                  $("#pur_party").html(data);                  
              }     
      
          });
      }

      function initialize_data(selectobj)
      {
        $("#"+selectobj).select2({ 

        });
      }

        function checkforkgs(flag) {

        $("#bulktobulkorbulktodrum"+flag).css('display','none');
        $("#bulktype"+flag).attr('required',false);
        $("#bulktype"+flag).removeClass('mand');

        $("#rate" + flag).text('');
          var u = $("#unit" + flag).val();
          var u11 = $("#unit" + flag).text();
           var ifbulk = $("#bulk" + flag).val();
          if (u == 5 && ifbulk==1) {
         
              $("#dens" + flag).css('display', '');
              $("#density" + flag).addClass('mand');
              $("#rate"+flag).text('Kg');

            $("#bulktobulkorbulktodrum"+flag).css('display','');
            $("#bulktype"+flag).attr('required',true);
            $("#bulktype"+flag).addClass('mand');

          } else {
              $("#dens" + flag).css('display', 'none');
              $("#density" + flag).removeClass('mand');
              $("#rate"+flag).text(u11);


          }
      
      }

      function delete_batch(id)
      {
        if(confirm('do you really want to delete?'))
        {
          document.location="<?php echo page_url;?>Inventory/delete_batch/"+id+"/<?php echo $this->uri->segment(3);?>";
        }

      }

      function change_batch_no(id,batch)
      {
       
        $("#batch_code_change").val(batch);
        $("#batch_code_id").val(id);
        $("#edit_id").val("<?php echo $this->uri->segment(3);?>");
        $("#batchchange").modal('show');

      }



      function check_batch_no(flag)
      {
        $("#batch_error"+flag).html('');
        var batch_no=$("#batch_no"+flag).val();
        var product=$("#product"+flag).val();
        if(batch_no!='' && product!='')
        {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/check_batch_no",
            data: "batch_no="+batch_no+"&product="+product,
            success: function(data) {
              if(data!='')
              {
                $("#batch_no"+flag).val('');
                $("#batch_error"+flag).html(data);
              }
          
            }
            });

        }

      }


      function check_batch_no_edit(flag)
      {
        $("#batch_error_edit"+flag).html('');
        var batch_no=$("#edit_batch_no"+flag).val();
        var product=$("#edit_product"+flag).val();
        if(batch_no!='' && product!='')
        {
            $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Inventory/check_batch_no",
            data: "batch_no="+batch_no+"&product="+product,
            success: function(data) {
              if(data!='')
              {
                $("#edit_batch_no"+flag).val('');
                $("#batch_error_edit"+flag).html(data);
              }
          
            }
            });

        }

      }
   </script>

</body>

</html>