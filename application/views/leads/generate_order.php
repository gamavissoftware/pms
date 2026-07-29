<?php
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$MI = &get_instance();
$MI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Dashboard_model');

$getCustomerDetails = $CI->salescrm->getCustomerDetails_frommaster($this->uri->segment(3));
if($getCustomerDetails != '') {
  foreach ($getCustomerDetails as $row);
  $company_id = $row->company_id;
  $customer_id = $row->customer_id;
  $company_name = $row->company_name;
  $customer_name = $row->customer_name;
  $assigned_to=$row->assigned_to;
  $state = $row->state;
  $city = $row->city;
  $pincode = $row->pincode;
  $email_id = $row->email;
  $contact_no = $row->contact_no;
  $postal_address = $row->address;
  $msme_number = $row->msme_number;
  $payment_type_new = $row->payment_type;
  $credit_days_new = $row->credit_days;
  $pan=$row->pan;
  $gst=$row->gst;
} else {
  $company_id = '';
  $pan='';
  $gst='';
  $customer_id = '';
  $assigned_to=0;
  $company_name = '';
  $customer_name = '';
  $state = '';
  $city = '';
  $pincode = '';
  $email_id = '';
  $contact_no = '';
  $postal_address = '';
  $msme_number = '';
  $payment_type_new = '';
  $credit_days_new = '';
}

$productdetail = $CI->salescrm->getQuotationProducts($this->uri->segment(3));
$getAllStates = $CI->salescrm->getAllStates();
$getspecialremarks=$CI->salescrm->getlastremarks($this->uri->segment(3));
$getpayment_details=$CI->salescrm->getpaymentdetail($this->uri->segment(3));
if(count($getpayment_details)>0)
{
    $monthly=$getpayment_details[0];
    $payment_type=$getpayment_details[1];
    $credit_days=$getpayment_details[2];
}else
{
        $monthly='';
        $payment_type='';
        $credit_days='';

}


$financial=$CI->salescrm->get_finacial_year_range();
if(count($financial)>0)
{
    $start_date=$financial['start_date']." 00:00:00";
    $end_date=$financial['end_date']." 23:59:59";

}else
{
    $start_date=date('Y-04-01')." 00:00:00";
    $end_date=date('Y-m-d')." 23:59:59";
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

      .select2-container {
        height: 44px !important;
        width: 100% !important;
      }

        .select2-container--default .select2-selection--single
        {
            height: 36px !important;
        }
  </style>


</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>

  <!-- End Navigation Bar-->
<!--------------------------------NOTES-------------------------------------->

<div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="todo-box">
                        <div class="all-notes">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><i class="fa fa-list" aria-hidden="true"></i> Add Notes</p>
                                </div>
                            </div>
                        </div>
                        <div class="to-list">
                            <form>
                                <input type="text" class="form-control">
                            </form>

                            <div class="todo-list">
                                <div class="todo-item">

                                    <span>Create theme</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">
                                    <span>Work
                                        on wordpress</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">

                                    <span>Organize office main department</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                              
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
<!--------------------------------NOTES-------------------------------------->

  <div class="wrapper">
    <div class="container">
      <!-- Page-Title -->
      <div class="row">

        <div class="col-sm-12" >
        <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btn-xs" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
         <form method="post" class="card-box" method="post" action="<?php echo page_url; ?>Customer/save_order_details/<?php echo $this->uri->segment(3); ?>" enctype="multipart/form-data" onsubmit="return validate()">
          <div class=" ">
            <div class="profile-info-name">
              <div class="profile-info-detail" style="margin-top:1px">
              	<div class="text-center card-box search-page">
                  <h3 class="m-t-0 m-b-0"><?php echo $company_name; ?></h3>
                  <br />
                  <div class="text-center">

                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page">
                      <h5>Overview</h5>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-industry" aria-hidden="true" style="color:#f9ab00;"></i>Company Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $company_name; ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-user" aria-hidden="true" style="color:#f9ab00;"></i>Customer Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo ucwords($customer_name); ?></p>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-user" aria-hidden="true" style="color:#f9ab00;"></i>Contact Number:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $contact_no; ?></p>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-user" aria-hidden="true" style="color:#f9ab00;"></i>Address:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $postal_address; ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-file" aria-hidden="true" style="color:#f9ab00;"></i>Special Remarks (If any):</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p style="color:red"><?php echo $getspecialremarks;?></p>
                        </div>
                      </div>

                      <hr>
                      <!------------------------------------------>

                      <div class="clearfix"></div>
                    </div>

                  </div>
<!--                   <div class="row">
                  <div class="col-sm-12">
                    <div class="card-box search-page" style="height: 305px; overflow-y: auto;">
                      <h5>Commercial</h5>
                       <table>
                            <tr>
                                <th>S.no</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Offered Price</th>
                                <th>Agreed Price</th>
                            </tr>

                            <?php
                            $i = 1;
                            if($productdetail != '') { 
                                foreach($productdetail as $row2) { ?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $row2->instruments_name;?></td>
                                <td><?php echo $row2->qty;?></td>
                                <td><?php echo $row2->list_price;?></td>
                                <td><input type="hidden" name="quote_detail_id[]" value="<?php echo $row2->id;?>"><input type="text" name="agreed_price[]" class="form-control mand"></td>
                            </tr>
                            <?php  
                            }                                       
                            ?>
                           
                        <?php }else{ ?>

                                <tr>
                                <td colspan="7">No Product Available</td>
                            
                        
                                </tr>

                        <?php } ?>
                  </table>

               
                    </div>

                   

                  </div>
                 
                </div> -->

                        <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Source</label>
                                    <span id="error_rack_location" style="color:red;">*</span>

                                    <select class="form-control mand" name="source" id="source" required>
                                    
                                        <?php $res=$this->db->select('source_id,lead_source')
                                                            ->from('lead_source')->where('source_id',5)
                                                            ->get();
                                            if($res->num_rows() >0) {
                                                foreach($res->result() as $row) {?>
                                                    <option value="<?php echo $row->source_id;?>"><?php echo $row->lead_source;?></option>
                                                <?php }
                                                } ?>
                                    </select>
                                </div>
                            </div>


                              <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">Sales Agent</label>
                                    <span id="error_rack_location" style="color:red;">*</span>

                                    <select class="form-control mand select3" name="agent" id="agent" required>
                                        <?php $res=$this->db->select('user_id,first_name,last_name')
                                                            ->from('system_users')
                                                            ->where('user_id',$assigned_to)
                                                            ->get();
                                            if($res->num_rows() >0) {
                                                foreach($res->result() as $row);?>
                                                    <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name;?>&nbsp;<?php echo $row->last_name;?></option>
                                                <?php }
                                                 ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                            <div class="form-group">
                            <label for="field-2" class="control-label">Monthly Consumption <span id="error_item_name" style="color:red;">*</span></label>

                            <input type="text" class="form-control mand" name="monthly_consumption" id="monthly_consumption" autocomplete="nope">
                            </div>
                            </div>


                            


                            <div style="clear:both;height:10px"></div>

                
                <div class="col-md-12">
                    <h2  class="text-center page-title">Order Details</h2>
                </div>

                <?php if($productdetail != '') { 
                          foreach($productdetail as $row5) {?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Competitor Product</label>
                              <select class="form-control mand" name="comp_product_edit<?php echo $row5->id;?>" id="comp_product_edit<?php echo $row5->id;?>">
                                   <option value="<?php echo $row5->competitor_product;?>"><?php echo $row5->competitor_product;?></option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Product</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="hidden" name="editid[]" value="<?php echo $row5->id;?>">
                              <select class="form-control mand" name="productedit<?php echo $row5->id;?>" id="productedit<?php echo $row5->id;?>" onchange="geteditprice(<?php echo $row5->id;?>,this.value);">
                                <?php 
                        
                                    $sql1= $this->db->select('b.id, b.instruments_name')
				                                    ->from('customer_quotation_detail a')
				                                    ->join('presto_instruments b', 'b.id=a.product_id')
				                                    ->where('b.id', $row5->product_id)
				                                    ->get();

                            
                            if($sql1->num_rows()>0) {
                                foreach($sql1->result() as $row4); ?>
                                  <option value="<?php echo $row4->id;?>" <?php if($row4->id == $row5->product_id) { echo 'selected' ;}?>><?php echo $row4->instruments_name;?></option>
                                <?php } ?>
                              </select>
                              <span id="recommendation_edit<?php echo $row5->id;?>"></span>
                          </div>
                      </div>
                      <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Qty</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control mand"  name="qtyedit<?php echo $row5->id;?>" id="qtyedit<?php echo $row5->id;?>" value="<?php echo $row5->qty;?>" oninput="allow_decimal('qtyedit<?php echo $row5->id;?>');">

                          </div>
                      </div>


                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Unit</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text"  readonly class="form-control mand"  name="unitedit<?php echo $row5->id;?>" id="unitedit<?php echo $row5->id;?>" value="<?php echo $row5->shortname;?>">
                              <input type="hidden" name="unit_id_edit<?php echo $row5->id;?>" value="<?php echo $row5->unit_id;?>">
                          </div>
                      </div>

                      <?php //$discountpricehideedit = $CI->master->getdiscountpriceeditdata($row5->id);?>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit<?php echo $row5->id;?>"><?php echo $row5->shortname;?></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control mand" name="listpriceedit<?php echo $row5->id;?>" id="listpriceedit<?php echo $row5->id;?>" value="<?php echo $row5->list_price;?>" onblur="getnetamtedit(<?php echo $row5->id;?>)" oninput="allow_decimal('listpriceedit<?php echo $row5->id;?>');">
                              <input type="hidden" name="discountpricehideedit<?php echo $row5->id;?>" value="<?php echo $row5->discount_price;?>" id="discountpricehideedit<?php echo $row5->id;?>">
                          </div>
                      </div>

                      <div class="col-md-2">
                      	<div class="form-group">
                      		<label for="field-2" class="control-label">Order Price</label>
                      		<span style="color:red;">*</span>
                      		<input type="text" name="agreed_price_edit<?php echo $row5->id;?>" class="form-control mand" onblur="checkpriceauth();">
                      	</div>
                      </div>

                   

                     <!--  <div class="col-md-2" style="display: none;">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Discount Price Per <?php echo $row5->unit;?><span></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control" name="discountpriceedit<?php echo $row5->id;?>" id="discountpriceedit<?php echo $row5->id;?>" value="0" onblur="getnetamtedit(<?php echo $row5->id;?>);" oninput="allow_decimal('discountpriceedit<?php echo $row5->id;?>');" value="<?php echo $row5->percent_amt;?>">
                          </div>
                      </div>

                   <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Net Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="netpriceedit<?php echo $row5->id;?>" id="netpriceedit<?php echo $row5->id;?>" value="0" readonly value="<?php echo $row5->net_price;?>">
                      </div>
                    </div> -->
                    <div class="col-md-1">
                        <div class="form-group" style="margin-top:25px">
                            <a href="<?php echo page_url; ?>Leads/delete_quote_product/<?php echo $row5->id;?>/<?php echo $this->uri->segment(3);?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" style="font-size: 27px; color: red;"></i></a>
                        </div>
                    </div>
                  </div>
                </div>
                   <?php } }?>


                <div class="col-md-12">
	                <div class="col-md-3">
	                    <div class="form-group">
	                        <label>Add More Products</label>
	                        <input type="checkbox" name="add_product" id="add_product" value="1" onchange="add_product_data();">
	                    </div>
	                </div>
                </div>


                    <div class="col-md-12" style="display:none;" id="shownewproduct">
                      			<div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Competitor Product</label>
                                       <!--  <span id="error_rack_location" style="color:red;">*</span> -->
                                        <select class="form-control" name="comp_product[]" id="comp_product0" onchange="getourproductname(0);"></select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-3" class="control-label">Product</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <select class="form-control products" name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value); getunit(0,this.value)">
                                            <option value="">Select</option></select>
                                         <span id="recommendation0"></span>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Qty</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="qty[]" id="qty0" autocomplete="off" oninput="allow_decimal('qty0')">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Unit</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" name="unit_name[]" id="unit_name0" class="form-control" readonly>
                                        <input type="hidden" name="pack_size[]" id="pack_size0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Order Price/<span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="listprice[]" id="listprice0" value="" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('listprice0');">
                                        <p id="discountpriceshow0" style="display: none;"></p>
                                        <input type="hidden" name="discountpricehide[]" value="" id="discountpricehide0">
                                    </div>
                                </div>
                              
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Allowed Price Per <span class="list_price_unit0"></span></label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="discountprice[]" id="discountprice0" autocomplete="off" value="0" onblur="getnetamt(0)" oninput="allow_decimal('discountprice0');">
                                        

                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">Net Price</label>
                                        <span id="error_rack_location" style="color:red;">*</span>
                                        <input type="text" class="form-control" name="netprice[]" autocomplete="off" id="netprice0" value="0" readonly>

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group" style="margin-top:25px">
                                        <button type="button" class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div id="dynamictasks"></div>
                               
                            </div>


            
                <div class="row">

                       <div class="col-md-12" style="margin-top:20px;">

                        <div class="col-md-3">
                                  <label>Billing Company <span style="color: red;">*</span></label>
                                  <span class="input-icon icon-right" style="margin-bottom:10px">
                                  <select class="form-control mand" name="hpcl_company" id="hpcl_company" required>
                                      <?php          $sql3 = $this->db->select('id, companyname')
                                                                      ->from('store_rack_location')
                                                                      ->where('id', $company_id)
                                                                      ->get();

                                      if($sql3->num_rows() >  0) {
                                      foreach($sql3->result() as $row3);?>   

                                      <option value="<?php echo $row3->id;?>"> <?php echo $row3->companyname;?> </option>    

                                      <?php }?>
                                  </select>
                                  </span>
                                </div>


                         <div class="col-md-3">
                      <label>Invoice No.</label>
                      <input type="text" name="invoice_no" id="invoice_no" class="form-control" autocomplete="nope" readonly value="" required>
                    </div>

                    <div class="col-md-2">
                      <label>PO No.</label>
                      <input type="text" name="po_no" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-2">
                      <label>PO Date</label>
                      <input type="text" name="po_date" id="datepicker" class="form-control" autocomplete="nope">
                    </div>
                     <div class="col-md-2">
                    <label>Upload PO/Evidence</label>
                    <span class="input-icon icon-right">
                      <input type="file" name="upload_file">
                    </span>
                </div>
                  </div>
                </div>
                </div>

              </div>
            </div>






            <!------------------To disable Folloup Use disabled attribute--------------------->

            <div class="iii">

                <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Payment Terms</h4>
                      </div>
                  </div>
                </div>
                <div class="row">

                <div class="col-md-4">
                            <div class="form-group">
                            <label for="field-2" class="control-label">Payment Type <span id="error_item_name" style="color:red;">*</span></label>
                            <select name="payment_type" class="form-control" id="payment_type" onchange="checkpdf();cheque_detail_for_adv();">
                            <option value="">Select</option>
                            <option value="2">Cash</option>
                            <option value="3">Online</option>
                            <option value="4">PDC</option>
                            <option value="5">Credit</option>
                            <option value="6">Advance</option>
                            </select>
                            </div>
                            </div>
                            <script type="text/javascript">
                      function checkpdf()
                      {
                        $("#pcd_details").css('display','none');
                        $(".cheque_details").css('display','none');
                        $("#pdcrecv").removeClass('mand');
                        var payment_type=$("#payment_type").val();
                        if(payment_type==4 || payment_type==5)
                        {
                            $("#paymentterms").addClass('mand');
                            $("#credit_days").css('display','');
                        }else
                        {
                             $("#paymentterms").removeClass('mand');
                             $("#credit_days").css('display','none');
                        }

                        if(payment_type==4)
                        {
                          $("#pcd_details").css('display','');
                          $(".cheque_details").css('display','');
                          $("#pdcrecv").addClass('mand');
                        }

                      }
                  </script>

                   <div class="col-md-4" id="credit_days" style="display:none;">
                    <div class="form-group">
                        <label for="field-2" class="control-label">Payment Terms/Credit Days (In Days) <span id="error_item_name" style="color:red;">*</span></label>
                        <input type="number" class="form-control" name="paymentterms" id="paymentterms" min="0" autocomplete="nope">
                    </div>
                  </div>

                   <div class="col-md-4" id="pcd_details" style="display:none;">
                          <div class="form-group">
                              <label>PDC Recieved?</label>
                              <select name="pdcrecv" id="pdcrecv" class="form-control" onchange="cheque_detail();">
                                  <option value="">Select</option>
                                  <option value="1">Yes</option>
                                  <option value="0">No</option>
                              </select>
                          </div>
                      </div>
                      <script>
                          function cheque_detail()
                          {
                               $(".cheque_details").css('display','none');
                                  $("#cheque_no").removeClass('mand');
                                  $("#cheque_date").removeClass('mand');
                                  $("#cheque_no").attr('required',false);
                                  $("#cheque_date").attr('required',false);

                              var pdcrecv=$("#pdcrecv").val();
                              if(pdcrecv==1)
                              {
                                  // alert('hi');
                                  $(".cheque_details").css('display','');
                                  $("#cheque_no").addClass('mand');
                                  $("#cheque_date").addClass('mand');
                                  $("#cheque_no").attr('required',true);
                                  $("#cheque_date").attr('required',true);

                              }

                          }

                          function cheque_detail_for_adv() {
                              var payment_type=$("#payment_type").val();
                                  $(".cheque_details").css('display','none');
                                  $("#cheque_no").removeClass('mand');
                                  $("#cheque_date").removeClass('mand');
                                  $("#cheque_no").attr('required',false);
                                  $("#cheque_date").attr('required',false);

                              if(payment_type==6)
                              {
                                  $(".cheque_details").css('display','');
                                  $("#cheque_no").addClass('mand');
                                  $("#cheque_date").addClass('mand');
                                  $("#cheque_no").attr('required',true);
                                  $("#cheque_date").attr('required',true);

                              }
                          }
                      </script>
                      <div class="col-md-4 cheque_details" style="display:none;">
                          <div class="form-group">
                              <label>Cheque No. <span style="color: red">*</span></label>
                              <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                          </div>
                      </div>
                       <div class="col-md-4 cheque_details" style="display:none;">
                          <div class="form-group">
                              <label>Cheque Date <span style="color: red">*</span></label>
                              <input type="date"  min="<?php echo date('Y-m-d');?>" name="cheque_date" id="cheque_date" class="form-control">
                          </div>
                      </div>                


                 
               
                </div>

                <br>
                <hr>
                <br>

                 <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Billing Address</h4>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Bill To<span style="color: red;">*</span></label>
                      <input type="text" name="bill_to" id="bill_to" autocomplete="nope" class="form-control mand" required value="<?php echo $company_name; ?>">
                    </div>
                    <div class="col-md-3" style="display:none;">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="billing_name" id="billing_name" autocomplete="nope" class="form-control" value="<?php echo $customer_name;?>">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control mand" name="billing_address" autocomplete="nope" id="billing_address" required><?php echo $postal_address;?></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control mand" name="billing_state" id="billing_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>" <?php if($state == $rows->state_id) { echo 'selected';}?>><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="billing_city" id="billing_city" autocomplete="nope" class="form-control mand" value="<?php echo $city;?>" required>
                    </div>
                    <div style="clear:both;height:5px"></div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="billing_pincode" id="billing_pincode" autocomplete="nope" class="form-control mand" value="<?php echo $pincode;?>" onblur="check_billing_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="billing_phone_no" id="billing_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="billing_mobile_no" id="billing_mobile_no" autocomplete="nope" class="form-control mand" onblur="check_billing_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email</label>
                      <input type="text" name="billing_email" id="billing_email" autocomplete="nope" class="form-control" onblur="check_billing_email()">
                    </div>
                  </div>
                </div>

                   <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <label>Billing Address same as Delivery Address?</label>
                          <input type="checkbox" id="check_billing" name="check_billing" value="1" onchange="check_address()">
                      </div>
                  </div>
                </div>



                <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Shipping Address</h4>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Ship To<span style="color: red;">*</span></label>
                      <input type="text" name="ship_to" id="ship_to" autocomplete="nope" class="form-control" value="<?php echo $company_name; ?>" required>
                    </div>
                    <div class="col-md-3" style="display:none;">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_name" id="shipping_name" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control mand" name="shipping_address" autocomplete="nope" id="shipping_address" required></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control mand" name="shipping_state" id="shipping_state" required>
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>"><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_city" id="shipping_city" autocomplete="nope" class="form-control mand" required>
                    </div>
                    <div style="clear:both;height:5px"></div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_pincode" id="shipping_pincode" autocomplete="nope" class="form-control mand" onblur="check_shipping_pincode()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="shipping_phone_no" id="shipping_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_mobile_no" id="shipping_mobile_no" autocomplete="nope" class="form-control mand" onblur="check_shipping_mobile()" required>
                    </div>
                    <div class="col-md-3">
                      <label>Email</label>
                      <input type="text" name="shipping_email" id="shipping_email" autocomplete="nope" class="form-control" onblur="check_shipping_email()">
                    </div>
                    
                  </div>
                </div>

                <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="col-md-4">
                          <label>Freight Applicable?</label>
                          <input type="checkbox" id="check_freight" name="check_freight" value="1" onchange="checkfreight()">
                      </div>
                      <div class="col-md-4" id="freight" style="display:none;">
                        <label>Freight Amount<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="freight_amt" id="freight_amt" oninput="allow_decimal('freight_amt')">
                      </div>
                  </div>
                </div>

             
               
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Tax Registration Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-3">
                      <label>MSME No.</label>
                      <input type="text" name="msme_no" id="msme_no" class="form-control" value="<?php echo $msme_number;?>" autocomplete="nope">
                    </div>

                    <div class="col-md-3">
                      <label>PAN/IT No.<span style="color: red;">*</span></label>
                      <input type="text" name="pan_no" id="pan_no" class="form-control mand" autocomplete="nope" onblur="check_pan()" required value="<?php echo $pan;?>">
                    </div>
                    <div class="col-md-3">
                      <label>Registration Type<span style="color: red;">*</span></label>
                      <input type="text" name="registration_type" id="registration_type" class="form-control mand" autocomplete="nope" value="REGULAR" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>GSTIN/UIN<span style="color: red;">*</span></label>
                      <input type="text" name="gst_no" id="gst_no" class="form-control mand" autocomplete="nope" onblur="" required="" value="<?php echo $gst;?>">
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Other Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Reference<span style="color: red;"></span></label>
                      <input type="text" name="reference" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-9">
                      <label>Instructions for Billing & Dispatch Department<span style="color: red;"></span></label>
                      <textarea class="form-control" name="note"></textarea>
                    </div>
                  </div>
                </div>

                <div class="p-t-10 pull-right">

                  <input type="submit" class="btn btn-sm btn-primary" style="background-color: #383b43 !important;border: 1px solid #383b43 !important;" id="saves" value="Generate Order">

                </div>
                <div class="marginbottom"></div>
                <div class="clearfix"></div>
            </div>
          </div>
              </form>

          <!-- end row -->





          <!-- Footer -->

          <?php $this->load->view('common/footer'); ?>

          <!-- End Footer -->



        </div>

        <!-- end container -->



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




      <script type="text/javascript">
        $(document).ready(function() {
          $('.select3').select2();
          var company_id = "<?php echo $company_id;?>";
          getinvoice_no(company_id);

          jQuery('#pdc_date').datepicker();

          jQuery('#datepicker').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()

          });



          jQuery('#meeting_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#meeting_time').timepicker({

            defaultTime: false,

            showMeridian: false

          });



          jQuery('#reminder_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#reminder_time').timepicker({



            defaultTime: false,

            showMeridian: false



          });

                    var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"><div class="col-md-2"> <div class="form-group"> <label for="field-3" class="control-label">Competitor Product</label> <select class="form-control" name="comp_product[]" id="comp_product'+i+'" onchange="getourproductname(0);"></select> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span id="error_rack_location" style="color:red;">*</span> <select class="form-control mand products" name="product[]" id="product'+i+'" onchange="getprice('+i+',this.value);getdiscount(0,this.value);  getunit('+i+',this.value)" required> <option value="">Select</option></select> <span id="recommendation'+i+'"></span> </div></div><div class="col-md-1"> <div class="form-group"> <label for="field-2" class="control-label">Qty</label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" class="form-control mand" name="qty[]" id="qty'+i+'" autocomplete="off" required> </div></div><div class="col-md-1"> <div class="form-group"> <label for="field-2" class="control-label">Unit</label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" name="unit_name[]" id="unit_name'+i+'" class="form-control" readonly> <input type="hidden" name="pack_size[]" id="pack_size'+i+'"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Order Price/<span class="list_price_unit'+i+'"></span></label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" class="form-control mand" name="listprice[]" id="listprice'+i+'" value="" onblur="getnetamt(0)" autocomplete="off"> <p id="discountpriceshow0" style="display: none;"></p><input type="hidden" name="discountpricehide[]" value="" id="discountpricehide0"> </div></div><div class="col-md-2" style="display: none;"> <div class="form-group"> <label for="field-2" class="control-label">Allowed Price Per <span class="list_price_unit0"></span></label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" class="form-control" name="discountprice[]" id="discountprice0" autocomplete="off" value="0" onblur="getnetamt(0)"> </div></div><div class="col-md-2" style="display: none;"> <div class="form-group"> <label for="field-2" class="control-label">Net Price</label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" class="form-control" name="netprice[]" autocomplete="off" id="netprice0" value="0" readonly> </div></div><div class="col-md-1" style="margin-top:25px"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-times"></i></button></div></div></div><br/>');

                initializeSelect2("comp_product"+i);
                initializeSelect2_product("product"+i);
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

        var purl="<?php echo page_url;?>Open_leads/getrecommendations";
        $('#comp_product0').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                tags:true,
                ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
                data: function (params) {

                return {
                searchTerm: params.term
                };

                },
                processResults: function (data) {
                return {
                results: data
                };
        },
        cache: true

                }
        });


        var product_url="<?php echo page_url;?>Leads/getProductsData";
        var company_id = "<?php echo $company_id;?>";

             $('#product0').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                    ajax: {
                        url: product_url,
                        dataType: 'json',
                        delay: 250,

                    data: function (params) {

                        return {
                            searchTerm: params.term,
                            company: company_id
                            };

                        },processResults: function (data) {

                            return {
                                results: data
                                };

                            },

                         cache: true

                        }

                 }); 



        });


       function initializeSelect2(selectElementObj) {
   
    var purl="<?php echo page_url;?>Open_leads/getrecommendations";

            $('#'+selectElementObj).select2({ 
            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,
            tags:true,
            ajax: {
            url: purl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
            searchTerm: params.term
            };
            },
            processResults: function (data) {
            return {
            results: data
            };
            },
            cache: true

            }
            }); 
         
      }



       function initializeSelect2_product(selectElementObj) {
            // $('#'+selectElementObj).select2({ });

        var product_url="<?php echo page_url;?>Leads/getProductsData";
        var company_id = "<?php echo $company_id;?>";

             $('#'+selectElementObj).select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                    ajax: {
                        url: product_url,
                        dataType: 'json',
                        delay: 250,

                    data: function (params) {

                        return {
                            searchTerm: params.term,
                            company: company_id
                            };

                        },processResults: function (data) {

                            return {
                                results: data
                                };

                            },

                         cache: true

                        }

                 }); 
         
      }

         function getinvoice_no(compid)
        {

                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/getinvoice_no",
                data: "compid=" + compid,
                success: function(data) {
            
                $("#invoice_no").val(data);
                }
                });

        }
      </script>



      <script type="text/javascript">     


        function validate()
        {

         $("#saves").attr('disabled', false);
          $("#saves").val('Update Followup Remarks');
          var isValid = 0;
          $(".mand").each(function() {
            var element = $(this).val();
            if (element == "") {
              isValid = 1;
            }
          });

          if (isValid == 0)
          {
            $("#saves").attr('disabled', true);
            $("#saves").val('Please Wait..');
            return true;
          } else
          {
            $("#saves").attr('disabled', false);
            $("#saves").val('Update Followup Remarks');
            alert('All fields marked with (*) are mandatory');
            return false;
          }

        }

        function getdiscount(i, pid) {
            var proid = pid;
            $("#discountpriceshow" + i).css('display', 'none');

            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getdiscountpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        $("#discountpricehide"+i).val(data);
                        $("#discountpriceshow" + i).text('Allowed Price: '+data);
                        $("#discountpriceshow" + i).css('color', 'red');
                        $("#discountpriceshow" + i).css('font-weight', 'bold');
                    }
                });
            }
        }

        function getunit(i, product) {
             if (product != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getunit",
                    data: "proid=" + product,
                    success: function(data) {
                        // alert("#pack_size"+i);
                        var arr = data.split('|');
                        $("#unit_name"+i).val(arr[0]);
                        $("#pack_size"+i).val(arr[1]);


                    }
                });
            }
        }



        function removeFollowup(id) {

          if (id == 11 || id == 2 || id == 6 || id == 5) {

            $("#followup").css('display', 'none');

            $("#followup_date").removeClass('mand');

          }

        }



        function show_discount(id) {

          $(".discount_type" + id).css('display', 'none');
          var discount_type = $("#discount_type" + id).val();

          if (discount_type != 3) {
            $(".discount_type" + id).css('display', '');

          }

        }



        function show_discountedit(id) {

          $(".discount_typeedits" + id).css('display', 'none');
          var discount_type = $("#discount_typeedit" + id).val();
          if (discount_type != 3) {

            $(".discount_typeedits" + id).css('display', '');


          }

        }

        function getPaymentInfo() {
          var payment_type = $("#payment_type").val();
          $("#show_cheque_no").css('display', 'none');
          $("#show_utr_no").css('display', 'none');
          $("#show_pdc_date").css('display', 'none');
          $("#cheque_no").removeClass('mand');
          $("#utr_no").removeClass('mand');
          $("#pdc_date").removeClass('mand');

          if (payment_type == 1) {
            $("#show_cheque_no").css('display', '');
            $("#cheque_no").addClass('mand');
          } else if (payment_type == 3) {
            $("#show_utr_no").css('display', '');
            $("#utr_no").addClass('mand');
          } else if (payment_type == 4) {
            $("#show_cheque_no").css('display', '');
            $("#show_pdc_date").css('display', '');
            $("#cheque_no").addClass('mand');
            $("#pdc_date").addClass('mand');
          }
        }

            function check_address() {

              var billing_name = $('#billing_name').val();
              var billing_address = $('#billing_address').val();
              var billing_state = $('#billing_state').val();
              var billing_city = $('#billing_city').val();
              var billing_pincode = $('#billing_pincode').val();
              var billing_contact_person = $('#billing_contact_person').val();
              var billing_phone_no = $('#billing_phone_no').val();
              var billing_mobile_no = $('#billing_mobile_no').val();
              var billing_email = $('#billing_email').val();

              if($('input[name=check_billing]').is(':checked')) {
                  $('#shipping_name').val(billing_name);
                  $('#shipping_address').val(billing_address);
                  $('#shipping_state').val(billing_state);
                  $('#shipping_city').val(billing_city);
                  $('#shipping_pincode').val(billing_pincode);
                  $('#shipping_contact_person').val(billing_contact_person);
                  $('#shipping_phone_no').val(billing_phone_no);
                  $('#shipping_mobile_no').val(billing_mobile_no);
                  $('#shipping_email').val(billing_email);
              } else {
                  $('#shipping_name').val('');
                  $('#shipping_address').val('');
                  $('#shipping_state').val('');
                  $('#shipping_city').val('');
                  $('#shipping_pincode').val('');
                  $('#shipping_contact_person').val('');
                  $('#shipping_phone_no').val('');
                  $('#shipping_mobile_no').val('');
                  $('#shipping_email').val('');
              }
        }



        function allow_decimal(data) {
            var self = $("#" + data);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }

        }

        function validate_edit_batch_code(i) {
           var batch_code = $('#batch_code_edit'+i).val();
           var reggst = /^([a-zA-Z]){1}([0-9]){1}-\s*([0-9]){2}-\s*([a-zA-Z]){1}-\s*([0-9]){3}?$/;

            if(!reggst.test(batch_code) && batch_code!=''){
                    alert('Batch Code Number is not valid.');
                    $('#batch_code_edit'+i).val('');
            }
        }

        function validate_batch_code(i) {
           var batch_code = $('#batch_code'+i).val();
           var reggst = /^([a-zA-Z]){1}([0-9]){1}-\s*([0-9]){2}-\s*([a-zA-Z]){1}-\s*([0-9]){3}?$/;

            if(!reggst.test(batch_code) && batch_code!=''){
                    alert('Batch Code Number is not valid.');
                    $('#batch_code'+i).val('');
            }
        }

        function check_shipping_pincode() {
              var shipping_pincode = $('#shipping_pincode').val();
              var zipRegex = /^\d{6}$/;

              if (!zipRegex.test(shipping_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#shipping_pincode').val('');
              }
        }

        function check_shipping_email() {
          var shipping_email = $('#shipping_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            
            if (!regex.test(shipping_email)) {
              alert('Invalid Email ID!');
              $('#shipping_email').val('');
            }
        }

        function check_shipping_mobile() {
          var shipping_mobile_no = $('#shipping_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if (!regex.test(shipping_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#shipping_mobile_no').val('');
            }
        }

        function check_billing_pincode() {
              var billing_pincode = $('#billing_pincode').val();
              var zipRegex = /^\d{6}$/;

              if (!zipRegex.test(billing_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#billing_pincode').val('');
              }
        }

        function check_billing_email() {
          var billing_email = $('#billing_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            
            if (!regex.test(billing_email)) {
              alert('Invalid Email ID!');
              $('#billing_email').val('');
            }
        }

        function check_billing_mobile() {
          var billing_mobile_no = $('#billing_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if (!regex.test(billing_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#billing_mobile_no').val('');
            }
        }

         function check_pan() {
          var pan_no = $('#pan_no').val();
          var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            
            if (!regex.test(pan_no)) {
              alert('Invalid PAN No!');
              $('#pan_no').val('');
            }
        }

        function validate_gst() {
           var gstinVal = $('#gst_no').val();
           var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([0-9]){1}?$/;

            if(!reggst.test(gstinVal) && gstinVal!=''){
                    alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
                    $('#gst').val('');
            }
        }

      function checkfreight() {
            $('#freight').css('display', 'none');
            $('#freight_amt').attr('required', false);
            
            if($('input[name=check_freight]').is(':checked')) {
              $('#freight').css('display', '');
              $('#freight_amt').attr('required', true);
            }
        }

        function add_product_data()
        {
            if($('#add_product').is(":checked"))
            {
                $("#shownewproduct").css('display','');

                $("#product0").addClass('mand');
                $("#qty0").addClass('mand');
                $("#listprice0").addClass('mand');
                $("#discountprice0").addClass('mand');
                $("#netprice0").addClass('mand');
                $('.remarkbox').css('display','');


            }else
            {
                 $("#shownewproduct").css('display','none');
                $("#product0").removeClass('mand');
                $("#qty0").removeClass('mand');
                $("#listprice0").removeClass('mand');
                $("#discountprice0").removeClass('mand');
                $("#netprice0").removeClass('mand');
            }


        }

                function getprice(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        var arr = data.split('|');
                        if(arr[0]>0)
                        {
                        // $("#listprice" + i).val(arr[0]);
                        $("#netprice" + i).val(arr[0]);
                        }else
                        {
                        // $("#listprice" + i).val('');
                        $("#netprice" + i).val('');
                        }
                        $(".list_price_unit" + i).text(arr[1]);
                        $("#unit" + i).val(arr[1]);
                        $("#unit_id" + i).val(arr[2]);
                        $("#discountpriceshow" + i).css('display', '');

                        if (arr[0] > 0) {
                            $("#discountprice" + i).attr('readonly', false);
                        } else {
                            $("#discountprice" + i).val(0);
                            $("#discountprice" + i).attr('readonly', true);
                        }
                    }
                });
            }
        }

      </script>

<script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "450px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }
    </script>

</body>

</html>