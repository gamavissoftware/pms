<?php 
  $CI =& get_instance();
  $CI->load->model('Salescrm_model', 'salescrm');
  $inventory_id=$this->uri->segment(3);
  $payment_pending=base64_decode($this->uri->segment(4));
  if($inventory_id=='' || $payment_pending=='')
  {
    echo "Invalid Link"; exit;
  }
 

     $rest=$this->db->select('b.collection_date,b.collection_id,a.balance,b.id')->from('sunder_collection_reference_balance a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.balance>',0)->where('a.active',1)->order_by('b.collection_date','ASC')->get();
     if($rest->num_rows()==0)
     {
        echo "<strong style='color:red;font-weight:bold;'>NO WALLET BALANCE IS AVAILABLE. ADD BALANCE ENTRY FIRST TO UPDATE PAYMENT</strong>"; exit;
     }

    $gst=0;
    $re= $this->db->select('gst,currentdate')->from('inventory')->where('id',$inventory_id)->get();
    if($re->num_rows()>0)
    {
        foreach($re->result() as $tyy);
        $gst=$tyy->gst;
        $paydate=date('Y-m-d',strtotime($tyy->currentdate));
    }

   $pur_data=$CI->salescrm->get_purchase_details_with_gst($inventory_id);
   $pdetails=explode('|',$pur_data);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> PAYMENT UPDATE</title>

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
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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
                    <h4 class="page-title">UPDATE PAYMENT STATUS</h4>
                </div>
            </div>
        </div>
            <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        <form id="quotation" method="post" action="<?php echo page_url; ?>Approval/update_inventory_payment_from_collection/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>/<?php echo $this->uri->segment(8);?>" autocomplete="off" onsubmit="return validate_form();"  enctype="multipart/form-data">
            <input type="hidden" name="inventory_id" value="<?php echo $this->uri->segment(3);?>">
            <input type="hidden" name="flag" value="<?php echo $this->uri->segment(5);?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box table-responsive">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4" style="display: none;">
                                <div class="form-group">
                                <label for="field-1" class="control-label">Payment Date</label>
                                <span style="color:red;">*</span>
                                <input type="text" name="pur_paymentOn" id="datepicker" class="form-control mand" autocomplete="nope" value="<?php echo $paydate;?>">
                                </div>
                                </div>

                                <div class="col-md-4">
                                <div class="form-group">
                                <label for="field-1" class="control-label">Pending Payment</label>
                                <span style="color:red;">*</span>
                                <input type="text" name="payment" id="payment" class="form-control mand" autocomplete="nope" readonly required style="color:red;font-weight: bold;" value="<?php echo $payment_pending;?>">
                                </div>
                                </div>

                                 <div class="col-md-4">
                                <?php echo $pdetails[0];?>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-9">
                                    <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                    <th colspan="4" style="text-align: center;">Available Balance & Collection ID</th>
                                    </tr>
                                    <tr>
                                   
                                    <th style="width:100px;">Collection ID</th>
                                    <th>Balance</th>
                                    <th>Balance Used</th>
                                    <th>After Payment Balance</th>
                                    <th>PRODUCTS CLEARED</th>
                                  
                                  
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                   

                                    $a=0;
                                
                                    if($rest->num_rows()>0)
                                    {
                                         $balance=0;
                                         $current_running='';
                                         $completed=array();
                                         $amt_paid=array();
                                         $current_running=0;
                                         
                                         $to_be_paid=$payment_pending;
                                         $i=0;
                                        foreach($rest->result() as $row)
                                        {     
                                            $current_running=$current_running;

                                            if($to_be_paid>0)
                                            {
                                            if($row->balance>=$to_be_paid)
                                            {
                                                
                                                $balance=$row->balance-$to_be_paid;
                                                $to_be_paid=0;
                                                $after_pay_balance=$balance;
                                                $balance_used=$row->balance-$balance;
                                               
                                              
                                            }else
                                            {
                                                
                                                $balance=$to_be_paid-$row->balance;
                                                $to_be_paid=$balance;
                                                $after_pay_balance=0;
                                                $balance_used=$row->balance;
                                                

                                            }


                                    ?>   
                                    <tr>
                                  
                                    <td style="color:red;">
                                        <?php echo $row->collection_id;?><br/><br/><?php echo date('d-M-Y',strtotime($row->collection_date));?>
                                        <input type="hidden" name="collection_id[]" value="<?php echo $row->id;?>">
                                    </td>
                                    <td><?php echo $row->balance;?></td>
                                    <td style="color:red;"><?php echo $balance_used;?>
                                    <input type="hidden" name="balance_used[]" value="<?php echo $balance_used;?>">
                                    </td>
                                    <td style="color:red;"><?php echo $after_pay_balance;?>
                                     <input type="hidden" name="final_balance[]" value="<?php echo $after_pay_balance;?>">

                                    <?php if($after_pay_balance>0)
                                    { 
                                    ?>
                                    <input type="hidden" name="active[]" value="1">
                                    <?php 
                                    } else{
                                    ?>
                                     <input type="hidden" name="active[]" value="0">
                                    <?php
                                    }
                                    ?>

                                    </td>

                                    <td>
                                         <table class="table table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                        <th style="width:40%;">Item</th>
                                        <th style="width:20%;">Amount Paid Via <br/>Collection ID</th>
                                        <th style="width:20%;">Payment Date</th>
                                        <th style="width:20%;display: none;">Portal Ref No.</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                    <?php

                                    $payment_balance=$balance_used;
                                    $this->db->select('a.id,a.qty,a.rate,b.instruments_name')->from('inventory_details a')->where('a.inventory_id',$inventory_id)->join('presto_instruments b','a.product=b.id')->where('a.payment',0);
                                    if(count($completed)>0)
                                    {
                                        foreach($completed as $remove)
                                        {
                                            $this->db->where('a.id!=',$remove);
                                        }
                                    }

                                    $ressst=$this->db->get();

                                    if($ressst->num_rows()>0)
                                    {
                                        $ty=0;
                                       $paid=0;
                                        foreach($ressst->result() as $invrow)
                                        {

                                           
                                            $cur=$payment_balance;
                                            $totalval=$invrow->qty*$invrow->rate;
                                            $gst_v=$gst/100;
                                            $gst_val=$totalval*$gst_v;
                                            $totald=$totalval+$gst_val;
                                           


                                            if($payment_balance>0)
                                            {
                                                if($current_running<>$invrow->id)
                                                {
                                                    $totald=$totald;
                                                    $paid=0;
                                                }else
                                                {
                                                $paid=array_sum($amt_paid[$invrow->id]);
                                                $totald=$totald-$paid;
                                                }
                                                //  if($i==2 && $ty==2)
                                                // {
                                                //     echo $totald."<br/>".$paid; exit;
                                                // }
                                            
                                            if($payment_balance>=$totald)
                                            {
                                                $paid_flag=1;
                                                $amount_paid=$totald;
                                                $payment_balance=$payment_balance-$totald;
                                                $amt_paid[$invrow->id][]=$amount_paid;
                                                //$paid=array_sum($amt_paid[$invrow->id]);
                                                $running=$invrow->id;
                                                

                                            }else
                                            {
                                                
                                                $paid_flag=2;
                                                $amount_paid=$payment_balance;
                                                $payment_balance=0;
                                                $amt_paid[$invrow->id][]=$amount_paid;
                                                //$paid=array_sum($amt_paid[$invrow->id]);
                                                 $running=$invrow->id;


                                            }

                                            



                                          
                                                if($paid_flag==1)
                                                {
                                                $completed[]=$invrow->id;
                                                }

                                        

                                    ?>  
                                   
                                       
                                        
                                       
                                     
                                          <tr>
                                        <td style="color: red;font-weight: bold;"><?php echo strtoupper($invrow->instruments_name);?>
                                            <input type="hidden" name="inventory_details_id<?php echo $row->id;?>[]" value="<?php echo $invrow->id;?>">
                                        </td>
                                        <td style="color: red;font-weight: bold;"><?php echo $amount_paid;?><input type="hidden" name="amount_paid<?php echo $row->id;?>[]" value="<?php echo $amount_paid;?>"><input type="hidden" name="paid_flag" value="<?php echo $paid_flag;?>"></td>

                                         <td><input type="date" required name="paid_date<?php echo $row->id;?>[]" class="form-control" value="<?php echo $row->collection_date;?>"></td>
                                         <td style="display: none"><input type="text" name="portal_ref_no<?php echo $row->id;?>[]" required class="form-control" value="<?php echo $row->collection_id;?>" readonly></td>
                                       
                                        </tr>
                                       

                                   <!--  <p><?php //echo $invrow->instruments_name;?><br/><?php //echo $totald;?><br/><?php //echo $amount_paid;?></p>
 -->

                                       <?php 
                                        
                                    $current_running=$invrow->id;
                                    $ty++;
                                    } } } ?>

                                     </tbody>
                                        </table>

                                </td>
                        
                                   
                                
                                    </tr>

                                    


                                    <?php
                                    $i++;
                                    }
                                    } 
                                    }
                                    ?>
                                                             
                                    </tbody>
                                    </table>
                                    </div>

                                </div>

                            </div>

                              <!--   <div class="row">
                                <div class="col-md-12">
                               


                                 <div class="col-md-4">
                                <label>Payment Type<span style="color: red;">*</span></label>
                                <span class="input-icon icon-right" style="margin-bottom:10px">
                                
                                <select class="form-control mand" name="payment_type" id="payment_type" required onchange="check_payment_type();">
                                  <option value="">SELECT PAYMENT TYPE</option>
                                  <option value="1">NEFT/IMPS</option>
                                  <option value="2">Cheque</option>
                                  <option value="3">Cash</option>
                                </select>
                                </span><br/>
                                
                              </div>


                                <div class="col-md-4 utr_details" style="display:none;">
                                      <label>UTR No</label><span style="color: red">*</span>
                                      <input type="text" name="utr_no" id="utr_no" class="form-control" autocomplete="off">
                                </div>
                                 <div class="col-md-4 utr_details" style="display:none;">
                                        <label>Upload Evidence </label><span style="color: red">*</span>
                                        <span class="input-icon icon-right">
                                          <input type="file" name="utr_evidence" id="upload_utr_file" class="form-control" autocomplete="nope">
                                        </span>
                                </div>

                               <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque No. <span style="color: red">*</span></label>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control" autocomplete="off">
                                    </div>
                                </div>
                                  <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Cheque Date <span style="color: red">*</span></label>
                                        <input type="date"  name="cheque_date" id="cheque_date" class="form-control">
                                    </div>
                                </div>

                                  <div class="col-md-4 cheque_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Upload Cheque <span style="color: red">*</span></label>
                                        <input type="file"  name="evidence" id="cheque_pic" class="form-control">
                                    </div>
                                </div>
                                 <div class="col-md-4 cash_details" style="display:none;">
                                    <div class="form-group">
                                        <label>Upload Evidence <span style="color: red">*</span></label>
                                        <input type="file"  name="cash_evidence" id="cash_pic" class="form-control">
                                    </div>
                                </div>

                            </div>
                            </div> -->
                        </div>
                      
                     
                     <?php 
                      $r=$this->db->select('a.id')->from('inventory_details a')->where('a.inventory_id',$inventory_id)->join('presto_instruments b','a.product=b.id')->where('a.payment',0)->get();
                      if($r->num_rows()>0)
                      {
                     ?>
                       
                            <div class="row">
                                 <div class="form-group pull-right">
                                    <input type="submit" id="save" class="btn btn-info" value="Submit">
                                </div>
                            </div>
                    <?php 
                        }else{
                        ?>
                         <div class="row pull-right">
                                <span style="color:red;font-weight: bold;font-size: 20px;">ALL PAYMENTS HAS BEEN CLEARED HENCE CANNOT SUBMIT</span>
                            </div>
                    <?php } ?>
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
  <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
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

    <!-- Datatable init js -->
    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
          $('.select30').select2(); 
            var i = 2;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<hr><div id="row' + i + '" class="row">   <div class="col-md-12"> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Location<span style="color:red;">*</span></label> <select class="form-control mand" name="hpcl_location[]" id="hpcl_location'+i+'"> <option value="">SELECT</option> <?php if($getHpclLocations !='') { foreach($getHpclLocations as $row) {?> <option value=" <?php echo $row->id;?> "> <?php echo $row->name;?> </option> <?php }} ?> </select> </div></div><div class="col-md-3"> <div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span> <select class="form-control mand select33" name="product[]" id="product'+i+'" required  onchange="add_instruments(); checkbulkandunit('+i+');" > <option value="">Select</option> <?php if($getAllProducts !='') { foreach($getAllProducts as $row) { ?> <option value="<?php echo $row->id;?>"><?php echo $row->instruments_name;?></option> <?php }} ?> </select> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Approved Price</label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="approved_price[]" id="approved_price0" autocomplete="off" required> </div></div><div class="col-md-2 col-2"> <div class="form-group"> <label for="field-1" class="control-label">Pack Size</label> <span style="color:red;">*</span> <select class="form-control mand pack_sizeadd'+i+'" name="pack_size[]" onchange="check_unit_add('+i+')" id="pack_sizeadd'+i+'"> <option value="">SELECT</option> <?php if($getAllUnits !='') { foreach($getAllUnits as $row2) { ?> <option value="<?php echo $row2->id;?>"><?php echo $row2->shortname;?></option> <?php }} ?> </select> </div></div> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Valid From<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="date" class="form-control mand" name="valid_from[]" id="valid_from'+i+'" autocomplete="off"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Valid Till<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="date" class="form-control mand" name="valid_till[]" id="valid_till'+i+'" autocomplete="off"> </div></div><!-- <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Price Validity<span class="list_price_unit0"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control mand" name="price_validity[]" id="price_validity0" required autocomplete="off"> </div></div>--> <div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">CREDIT/VLI <span class="chk_unitadd'+i+'"></span></label> <span style="color:red;">*</span> <input type="text" class="form-control common_readonly" name="credit_vli[]" id="credit_vli0" autocomplete="off" onblur="getnetamt(0)"> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Minimum Qty if Any</label> <span style="color:red;">*</span> <input type="text" class="form-control common_readonly" name="moq[]" autocomplete="off" id="moq'+i+'"> </div> <div class="col-md-12"> <div class="form-group"> <label>Historical Data Based MOQ?</label> <input type="checkbox" name="historical_moq[]" id="historical_moq'+i+'" onchange="check_for_historical('+i+');" value="1"> </div></div><div class="col-md-12 historical_div" style="display: none;" id="moqyear'+i+'"> <div class="form-group"> <label>Year <span style="color:red">*</span></label> <select class="form-control yearinput" name="year[]" id="year'+i+'" onchange="get_historical_moq('+i+');"> <option value="">Select</option> <?php for($kl=1;$kl<5;$kl++) {$number=str_pad($kl, 2, '0', STR_PAD_LEFT); $year=date('Y',strtotime("-".$kl." Years")); ?> <option value=" <?php echo $year;?>"> <?php echo $year;?></option> <?php } ?> </select> </div><span style="color:red;font-weight: bold;"></span> </div><div class="col-md-12 increment_div" style="display: none;" id="increment_div'+i+'"> <div class="form-group"> <label>Increase By % <span style="color:red">*</span></label> <input type="text" class="form-control increment_input" name="increment[]" id="increment'+i+'" onblur="get_historical_moq('+i+');"> </div><span style="color:red;font-weight: bold;"></span> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture<span style="color:red;">*</span></label> <input type="text" name="annexture[]" class="form-control" id="annexture'+i+'" > </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Annexture Upload<span style="color:red;">*</span></label> <input type="file" name="annex_upload[]" class="form-control" id="annex_upload'+i+'" > </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
                $('#product'+i).select2();

                // getproductname(i);
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

            var date = new Date();
              var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            jQuery('.datepicker').datepicker({
                startDate: date,
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });


                        var k = 1;
            $('.add_more_product_comb').click(function() {

                $('.prod_comb0').append('<div id="row' + k + '" class="row"><div class="col-md-12"><div class="col-md-3"><div class="form-group"> <label for="field-3" class="control-label">Product</label> <span style="color:red;">*</span><select class="form-control mand  combi_product" name="comb_product[]" id="comb_product'+k+'"></select> </div></div><div class="col-md-1"><div class="form-group pull-left" style="padding-top:23px"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove_prod_comb btn btn-danger btn-xs" id="' + k + '"><i class="fa fa-close"></i></button></div></div></div><br/>');
                    
                    add_instruments_by_id(k);
               
                k++;
            });

            $(document).on('click', '.btn_remove_prod_comb', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });


        });
    </script>


    <script>

        function chk_transportation() {
            $("#chk_transportation").css('display', 'none');
            $("#rate").attr('required', false);

           if($("#transportation").val() == 2) {
                $("#chk_transportation").css('display', '');
                $("#rate").attr('required', true);
           }
           
        }

        function check_unit(i) {
            var pack_size = $('.pack_size'+i+' option:selected').text();
            
            if($('.pack_size'+i).val() != '') {
                $('.chk_unit'+i).text('Per '+pack_size);
            } else {
                $('.chk_unit'+i).text('');
            } 
        }

        function check_unit_add(i) {
            var pack_size = $('.pack_sizeadd'+i+' option:selected').text();
            
            if($('.pack_sizeadd'+i).val() != '') {
                $('.chk_unitadd'+i).text('Per '+pack_size);
            } else {
                $('.chk_unitadd'+i).text('');
            } 
        }

        function delete_approval_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_approval_product",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#remove_product'+id).remove();
                        }
                    }
                    });
            }
        }
        function delete_combination_product(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Approval/delete_combination_product",
                    data:{id: id},
                    success:function(data){
                        if(data == 1) {
                          $('#edit_prod_comb'+id).remove();
                        }
                    }
                    });
            }
        }

         function check_credit_period() {
             $("#chk_credit").css('display', 'none');
             $("#credit_period").attr('required', false);

           if($("#payment_terms").val() == 2) {
                $("#chk_credit").css('display', '');
                $("#credit_period").attr('required', true);
           }
        }


    function checkbulkandunit(flag)
    {
        var prd=$("#product"+flag).val();

              // alert(flag+' '+prd)
         $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getProductUnit",
            data: "prd=" + prd,
            success: function(data) {
              $("#pack_sizeadd"+flag).html(data);
            }
            });

    }

    function check_editbulkandunit(flag)
    {
        var prd=$("#edit_product"+flag).val();

         $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getProductUnit",
            data: "prd=" + prd,
            success: function(data) {
              // alert(data)
              $("#edit_pack_size"+flag).html(data);
            }
            });

    }




function chkIfCombApproved() {
    var combination_approval = $('input[name="combination_approval"]:checked').val(); 
        $('.common_readonly').attr('readonly', false);
        $('.combination_repeat').css('display', 'none');
        $('.combination_head').css('display', 'none');
         $(".combi_product").removeClass('mand');
        $("#combination_type").removeClass('mand');
        $("#com_moq0").removeClass('mand');
        $("#com_credit_vli0").removeClass('mand');
        $('input[name="historical_moq[]"]').attr('disabled',false); 
        $('input[name="edit_historical_moq[]"]').attr('disabled',false); 
        $("#moqyear_comb").css('display','none');
        $("#year_comb").attr('required',false);
        $(".historical_div_comb").css('display','none');
        $("#com_moq0").attr('readonly',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);
        $('.yearinput').attr('required',false); 


    if(combination_approval == 1) {
      // alert("comb")
        $('.common_readonly').attr('readonly', true);
        $('.common_readonly').val(0);
        $('.combination_repeat').css('display', '');
        $('.combination_head').css('display', '');
        $(".combi_product").addClass('mand');
        $("#combination_type").addClass('mand');
        $("#com_moq0").addClass('mand');
        $("#com_credit_vli0").addClass('mand');
        chk_combination_type();
        $('input[name="historical_moq[]"]').attr('disabled',true); 
        $('input[name="edit_historical_moq[]"]').attr('disabled',true); 
        $(".historical_div").css('display','none');
        //alert('hi');
        $('.yearinput').attr('required',false);
        $(".increment_div").css('display','none');
        $(".increment_input").attr('required',false);


    }
}
    </script>

    <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            check_credit_period();
            $("#save").click(function() {
                var vendor_name = $("#vendor_name").val();
                if (vendor_name == '') {
                    $("#error_vendor_name").html('Required!');
                }
                var item_name = $("#item_name").val();
                if (item_name == '') {

                    $("#error_item_name").html('Required!');
                }

                var status = $("#status").val();
                if (status == '') {

                    $("#error_status").html('Required!');
                }


                if (vendor_name == '' || item_name == '' || status == '') {

                    return false;
                }

            });
            var combination_approval = $('input[name="combination_approval"]:checked').val(); 
              if(combination_approval == 1) {
                // alert("pre comb")
                $('.common_readonly').attr('readonly', true);
                $('.common_readonly').val(0);
                $('.combination_repeat').css('display', '');
                $('.combination_head').css('display', '');
                $(".combi_product").addClass('mand');
                $("#combination_type").addClass('mand');
                $("#com_moq0").addClass('mand');
                $("#com_credit_vli0").addClass('mand');
                chk_combination_type();
                $('input[name="historical_moq[]"]').attr('disabled',true); 
                $('input[name="edit_historical_moq[]"]').attr('disabled',true); 
                $(".historical_div").css('display','none');
                //alert('hi');
                $('.yearinput').attr('required',false);
                $(".increment_div").css('display','none');
                $(".increment_input").attr('required',false);


            }
        });

    function chk_combination_type()
    {
        var ctype=$("#combination_type").val();
        if(ctype!='')
        {
            if(ctype==1)
            {
                $("#combmoqfield").css('display','');
                $("#com_moq0").addClass('mand'); 


                $("#combcreditfield").css('display','none');
                $("#com_credit_vli0").removeClass('mand');

            }else if(ctype==2)
            {
                $("#combmoqfield").css('display','none');
                $("#com_moq0").removeClass('mand'); 

                $("#combcreditfield").css('display','');
                $("#com_credit_vli0").addClass('mand');

            }else 
            {

                $("#combmoqfield").css('display','');
                $("#com_moq0").addClass('mand'); 

                $("#combcreditfield").css('display','');
                $("#com_credit_vli0").addClass('mand');

            }

            add_instruments();
        }
    }

    function add_instruments()
    {

         var val = [];

        $('select[name="edit_product[]"]').each(function () {
        var b=$(this).val();
        val.push(b);
        });
        $('select[name="product[]"]').each(function () {
        var a=$(this).val();
        val.push(a);
        });

        var dval=val.join(',');

            $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/getproducts_byid",
            data: "prd=" + dval,
            success: function(data) {
             $(".combi_product").html(data);
              }
            });

            combinations_detail();

    }

     function add_instruments_by_id(flag)
    {
        
         var val = [];
         $('select[name="edit_product[]"]').each(function () {
        var b=$(this).val();
        val.push(b);
        });
        $('select[name="product[]"]').each(function () {
        var a=$(this).val();
        val.push(a);
        });

        var dval=val.join(',');

            $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/getproducts_byid",
            data: "prd=" + dval,
            success: function(data) {
           $("#comb_product"+flag).html(data);
            }
            });
    }

    function combinations_detail(){
      var id = <?php echo $this->uri->segment(3); ?>;
      $.ajax({
            type: "post",
            url: "<?php echo page_url;?>/Approval/combinations_detail",
            data: "id=" + id,
            success: function(data) {
            // alert(data)
            }
            });
    }

    function check_for_historical(id)
    {
      // alert(id);
         $("#moqyear"+id).css('display','none');
         $("#year"+id).attr('required',false);
         $("#increment_div"+id).css('display','none');
         $("#increment"+id).attr('required',false);
         $("#moq"+id).attr('readonly',false);
        if($('#historical_moq' + id).is(":checked"))
        {
            $("#moqyear"+id).css('display','');
            $("#year"+id).attr('required',true);

            $("#increment_div"+id).css('display','');
            $("#increment"+id).attr('required',true);
            $("#moq"+id).attr('readonly',true);
        }

    }
    function check_for_historical_edit(id)
    {
      // alert(id);
         $("#edit_moqyear"+id).css('display','none');
         $("#edit_year"+id).attr('required',false);
         $("#edit_increment_div"+id).css('display','none');
         $("#edit_increment"+id).attr('required',false);
         $("#edit_moq"+id).attr('readonly',false);
        if($('#edit_historical_moq' + id).is(":checked"))
        {
            $("#edit_moqyear"+id).css('display','');
            $("#edit_year"+id).attr('required',true);

            $("#edit_increment_div"+id).css('display','');
            $("#edit_increment"+id).attr('required',true);
            $("#edit_moq"+id).attr('readonly',true);
        }

    }
    

    function get_historical_moq(id)
    {
      // alert(id)
        var prd=$("#product"+id).val();
        var year=$("#year"+id).val();
        var increment=$("#increment"+id).val();
        if(prd!='' && year!='' && increment!='')
        {

             $.ajax({
            type: "post",
            url: "<?php echo page_url;?>Approval/getPreviousPurchase_for_ajax",
            data: "year="+year+"&product="+prd+"&increment="+increment,
            success: function(data) {
            $("#moq"+id).val(data);
            }
            });

        }

    }


function check_payment_type() {
          var payment_type=$("#payment_type").val();
            // alert(payment_type)
              $(".utr_details").css('display','none');
              $(".cheque_details").css('display','none');
              $(".cash_details").css('display','none');
              $("#cheque_no").removeClass('mand');
              $("#cheque_date").removeClass('mand');
              $("#cheque_no").attr('required',false);
              $("#cheque_date").attr('required',false);
              $("#cheque_date").removeClass('mand');
              $("#cheque_pic").attr('required',false);
              $("#cheque_date").removeClass('mand');
              $("#cheque_pic").attr('required',false);

          if(payment_type==1)
          {
              $(".utr_details").css('display','');
              $("#utr_no").addClass('mand');
              $("#utr_no").attr('required',true);
              $("#upload_utr_file").addClass('mand');
              $("#upload_utr_file").attr('required',true);

          }
          if(payment_type==2)
          {
              $(".cheque_details").css('display','');
              $("#cheque_no").addClass('mand');
              $("#cheque_date").addClass('mand');
              $("#cheque_no").attr('required',true);
              $("#cheque_date").attr('required',true);
              $("#cheque_pic").addClass('mand');
              $("#cheque_pic").attr('required',true);

          }

          if(payment_type==3)
          {
              $(".cash_details").css('display','');
              $("#cash_pic").addClass('mand');
              $("#cash_pic").attr('required',true);

          }
      }

      $('#datepicker').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'dd-mm-yyyy',
      
   });
    </script>
</body>

</html>