<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$user_id = base64_decode($this->uri->segment(3));
$getLeadProducts=$CI->salescrm->getLeadProducts($this->uri->segment(3));
$basic_terms="Price Basis : Ex - factory Faridabad<br/>Packing and Forwarding : @3% Extra.<br/>Freight : Extra at Actuals<br/>Delivery : 2 Daysfrom the date of written confirmation with Advance Payment.<br/>Payment Terms : 100% advance with PO";

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Modify Quotation </title>
        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
          <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>
            .select2-container {
                    width: 100% !important;
                  }

				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {
				    width: 100% !important;
				}

			   .add_more {
		        margin-top: 33px;
		        }

		        .remove {
		        margin-top: 33px;
		        }

		        .delete_product {
		        margin-top: 33px;
		        }
			</style>
			  <style>
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
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
        <div class="container-fluid">

                <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
	                <div class="page-title-box">
	                    <!-- <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a> -->
					 <div class="btn-group pull-right"></div>
	                   
	                    <h4 class="page-title text-center">REVISED QUOTATION</h4>
	                </div>
                </div>
            </div>
                <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

        <div class="row">
            <div class="col-xs-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">


                <?php 
                    $uri = $this->uri->segment(3);
                  $check = is_numeric($uri);
                
                $qq = $this->db->select('customer_name, id, company_name, contact_person,email_id, contact_no, postal_address, general_terms, distributor')->from('leads')->where('id',$this->uri->segment(3))->get();
                        foreach($qq->result() as $rowss);

                    
                ?>
				<form id="loginForm" method="post" action="<?php echo page_url;?>Leads/revisedselectedquotation/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data" onsubmit="return validate_leads();">
                    <input type="hidden" name="leadquality" value="4" >
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Company Name</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" value="<?php echo $rowss->company_name;?>" placeholder="Company Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                  
                        </div>
                    </div>

                      <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Customer Name</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="customer_name" name="customer_name" value="<?php echo $rowss->customer_name;?>" placeholder="Customer Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                 
                        </div>
                    </div>

                <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Contact No</label>
                            <span style="color:red;">*</span>
                            <input type="text" id="company_name" name="company_name" value="<?php echo $rowss->contact_no;?>" placeholder="Company Name"
                                autocomplete="nope" class="form-control mand" readonly required>
                                
                        </div>
                    </div>

                <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Email ID</label>
                            <input type="text" id="email_id" name="email_id" value="<?php echo $rowss->email_id;?>" placeholder="Email Id"
                                autocomplete="nope" class="form-control mand" readonly required>
                                  
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="field-1" class="control-label">Validity Date</label>
                            <span style="color:red;">*</span>
                            <input type="date" id="validity_date" name="validity_date" value="" placeholder="Validity Date"
                                autocomplete="nope" class="form-control mand" required>
                                  
                        </div>
                    </div>
                
                </div>

                  <?php if($getLeadProducts != '') { 
                          foreach($getLeadProducts as $row5) {
                          $quotedprice = $row5->price;
                          $qty = $row5->qty;
                          $finalprice = $quotedprice*$qty;
                         // echo "<pre>"; print_r($row5); exit;
                           
                    ?>
                <div class="row">
                    <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Product</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="hidden" class="editid1" name="editid[]" value="<?php echo $row5->id;?>">
                              <select class="form-control" name="editproduct[]" id="product">
                                <?php 
                        
                                $sql1 = $this->db->select('id, instruments_name, pack_size')
                                             ->from('presto_instruments')
                                             ->where('id', $row5->product_id)
                                             ->where('status',1)
                                             ->get();

                            if($sql1->num_rows()>0) {
                                foreach($sql1->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?> (<?php echo $row4->pack_size;?>)</option>
                                <?php } } ?>
                              </select>
                             
                          </div>
                      </div>

                        <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-2" class="control-label">List Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="editnetprice[]" id="netpriceeditss<?php echo $row5->id;?>" readonly value="<?php echo $row5->mvalue;?>">
                           <span style="color:red;">Allowed Price: <?php echo $row5->discount_price;?></span>
                           <input type="hidden" id="alloweddiscountprice<?php echo $row5->id;?>" value="<?php echo $row5->discount_price;?>">

                      </div>
                    </div>
                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Discount(%)<?php //echo $row5->unit;?><span></span></label>
                              <input type="text" class="form-control" name="editdiscount[]" id="discountinpercents<?php echo $row5->id;?>" onchange="calculatedisprice(<?php echo $row5->id;?>); checkqtyandupdatetotalprice(<?php echo $row5->id;?>);" value="<?php echo $row5->percent_amt;?>">
                          </div>
                      </div>
                     

                     <div class="col-md-2">
                          <div class="form-group">
                              <label>Offered Price</label>
                              <input type="text" class="form-control" name="editofferedprice[]" id="offered_prices<?php echo $row5->id;?>" value="<?php echo $quotedprice;?>" onblur="updatediscountonfinalprice(<?php echo $row5->id;?>); checkqtyandupdatetotalprice(<?php echo $row5->id;?>);">
                          </div>
                      </div>


                        <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Qty</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control"  name="editqty[]" id="qtys<?php echo $row5->id;?>" value="<?php echo $row5->qty;?>" onkeyup="checkqtyandupdatetotalprice(<?php echo $row5->id;?>);">
                          </div>
                      </div>
                      
                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Unit</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text"  readonly class="form-control"  name="editunit[]" id="unitedit<?php echo $row5->id;?>" value="<?php echo $row5->shortname;?>">
                              <input type="hidden" name="unit_id_edit<?php echo $row5->id;?>" value="<?php echo $row5->unit_id;?>" id="unit_id_edit<?php echo $row5->id;?>">
                          </div>
                      </div>

                      <div class="col-md-2">
                          <div class="form-group">
                              <label>Final Price</label>
                              <input type="text" class="form-control" name="editfinalprice[]" id="finalprices<?php echo $row5->id;?>" value="<?php echo $finalprice;?>" readonly>
                          </div>
                      </div>
                     
                     
                    <div class="col-md-1">
                        <div class="form-group" style="margin-top:25px">
                            <a href="<?php echo page_url; ?>Leads/delete_lead_product/<?php echo $row5->id;?>/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" style="font-size: 27px; color: red;"></i></a>
                        </div>
                    </div>


                </div>

            <?php }
        }?>

            <script>
           function calculatedisprice(i){
            var alloweddiscountprice = parseFloat($("#alloweddiscountprice"+i).val());
            var netprice = parseFloat($("#netpriceeditss"+i).val());
            var discountval  = parseFloat($("#discountinpercents"+i).val());
            if(discountval>0){
                var discountintotal = netprice * (discountval/100);
                var finaltotal = netprice-discountintotal;
                var finaltotal = finaltotal.toFixed(1);

                if(parseFloat(finaltotal)<parseFloat(alloweddiscountprice)){

                if(confirm('Discounted Price should not be less than allowed price.')){
                 $("#offered_prices"+i).val(finaltotal);
                }else{
                $("#offered_prices"+i).val('');
                $("#discountinpercents"+i).val('');
                }

                   
                    
                    
                 }else{
                     $("#offered_prices"+i).val(finaltotal);
                 }
               
            }else{
                $("#offered_prices"+i).val(netprice); 
            }

          }

           function updatediscountonfinalprice(i){
                var alloweddiscountprice = parseFloat($("#alloweddiscountprice"+i).val());
                var finalprices =  parseFloat($("#offered_prices"+i).val());
                if(parseFloat(finalprices)<parseFloat(alloweddiscountprice)){

                    if(confirm('Discounted Price should not be less than allowed price.')){
                    var netpriceeditss = parseFloat($("#netpriceeditss"+i).val());
                var discount = netpriceeditss-finalprices;
                var discountpercent = (discount*100)/netpriceeditss;
               var discountpercent= discountpercent.toFixed(1);
               $("#discountinpercents"+i).val(discountpercent);
                    }else{
                    $("#offered_prices"+i).val('');
                    $("#discountinpercents"+i).val('');
                    }
                }else{
                     var netpriceeditss = parseFloat($("#netpriceeditss"+i).val());
                var discount = netpriceeditss-finalprices;
                var discountpercent = (discount*100)/netpriceeditss;
               var discountpercent= discountpercent.toFixed(1);
               $("#discountinpercents"+i).val(discountpercent);
                }
              


            }

             function checkqtyandupdatetotalprice(i){
                            var qty = parseFloat($("#qtys"+i).val());
                            if(qty>0){
                                var offered_price = parseFloat($("#offered_prices"+i).val());
                                var total = qty*offered_price;
                                var total = total.toFixed(1);
                                $("#finalprices"+i).val(total);
                            }else{
                                 var offered_price = $("#offered_price"+i).val();
                                 var offered_price = offered_price.toFixed(1);
                                $("#finalprices"+i).val(offered_price);
                            }

                          }


                        function calculatedisprice1(i){
                            var masterallowedprice = parseFloat($("#masterallowedprice"+i).val());
                            var netprice = parseFloat($("#netpriceedit"+i).val());
                            var discountval  = parseFloat($("#discountinpercent"+i).val());
                            if(discountval>0){
                                var discountintotal = netprice * (discountval/100);
                                var finaltotal = netprice-discountintotal;
                                var finaltotal = finaltotal.toFixed(1);
                                //alert(finaltotal);
                                if(parseFloat(finaltotal)<parseFloat(masterallowedprice)){
                                    if(confirm('Discounted Price should not be less than allowed price.')){
                                         $("#offered_price"+i).val(finaltotal);
                                    }else{
                                    $("#offered_price"+i).val('');
                                    $("#discountinpercent"+i).val('');
                                    }

                                }else{
                                    $("#offered_price"+i).val(finaltotal);
                                }
                                
                            }else{
                                $("#offered_price"+i).val(netprice); 
                            }

                          }

                function updatediscountonfinalprice1(i){
                var finalprices =  parseFloat($("#offered_price"+i).val());
                var alloweddiscountprice = parseFloat($("#masterallowedprice"+i).val());
                if(parseFloat(finalprices)<parseFloat(alloweddiscountprice)){
                    if(confirm('Discounted Price should not be less than allowed price.')){

                    }else{
                        $("#offered_price"+i).val('');
                    $("#discountinpercent"+i).val('');
                    }
                   
                    
                }else{
                    
                }
               
                var netpriceeditss = parseFloat($("#netpriceedit"+i).val());
                var discount = netpriceeditss-finalprices;
                var discountpercent = (discount*100)/netpriceeditss;
               var discountpercent= discountpercent.toFixed(1);
               $("#discountinpercent"+i).val(discountpercent); 

            }
                      </script>


                    <script type="text/javascript">
                          function checkqtyandupdatetotalprice(i){
                            var qty = parseFloat($("#qtys"+i).val());
                            if(qty>0){
                                var offered_price = parseFloat($("#offered_prices"+i).val());
                                var total = qty*offered_price;
                                $("#finalprices"+i).val(total);
                            }else{
                                 var offered_price = $("#offered_price"+i).val();
                                $("#finalprices"+i).val(offered_price);
                            }

                          }

                          function checkqtyandupdatetotalprice1(i){
                            var qty = parseFloat($("#qty"+i).val());
                            if(qty>0){
                                var offered_price = parseFloat($("#offered_price"+i).val());
                                var total = qty*offered_price;
                                $("#finalprice"+i).val(total);
                            }else{
                                 var offered_price = $("#offered_price"+i).val();
                                $("#finalprice"+i).val(offered_price);
                            }

                          }

                      </script>



        <div class="row">
            <div class="col-md-12">
            <div class="col-md-3">
            <div class="form-group">
            <label>Add More Products</label>
            <input type="checkbox" name="add_product" id="add_product" value="1" onchange="add_product_data();">
            </div>
            </div>
            </div>
        </div>


        <div class="row" style="display:none;" id="shownewproduct">
             <div class="col-md-12" >
                     <input type="hidden" class="editid1" name="editid[]" value="">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-3" class="control-label">Product</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <select name="product[]" id="product1" onchange="getproductactualprice(1); getnewproductactualunit(1); getproductactualallowprice(1);">
                                    <option value="">Select</option>
                                     <?php 
                                $query = $this->db->select('b.id,b.instruments_name,b.pack_size')
                                                  ->from('presto_instruments b')
                                                  ->where('b.status',1)
                                                  ->get();
                            
                            if($query->num_rows()>0) {
                                foreach($query->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?> (<?php echo $row4->pack_size;?>)</option>
                                  <?php } } ?>
                                </select>
                                <span id="recommendation0"></span>
                            </div>
                        </div>
                        <script type="text/javascript">
                            function getproductactualprice(i) {
                                var proid = $("#product"+i).val();
                                $.ajax({
                                type:"post",
                                url:"<?php echo page_url;?>Customer/getactualprice",
                                data:{proid:proid},
                                success:function(data) {

                               $("#netpriceedit"+i).val(data);
                               $("#offered_price"+i).val(data);
                                }
                                });

                            }

                             function getproductactualallowprice(i) {
                                var proid = $("#product"+i).val();
                                $.ajax({
                                type:"post",
                                url:"<?php echo page_url;?>Customer/getactualdiscountedprice",
                                data:{proid:proid},
                                success:function(data) {
                                $("#printproductallowedprice"+i).html(data);
                               $("#masterallowedprice"+i).val(data);
                                }
                                });

                            }

                             function getproductactualunit(i) {
                                var proid = $("#product"+i).val();
                                $.ajax({
                                type:"post",
                                url:"<?php echo page_url;?>Customer/getactualpriceunit",
                                data:{proid:proid},
                                success:function(data) {

                               $("#unitedit"+i).val(data);
                               
                                }
                                });

                            }
                            function getnewproductactualunit(i) {
                                var proid = $("#product"+i).val();
                                $.ajax({
                                type:"post",
                                url:"<?php echo page_url;?>Customer/getactualpriceunit",
                                data:{proid:proid},
                                success:function(data) {
                               $("#newproductunit"+i).val(data);
                               
                                }
                                });

                            }
                        </script>
                         <div class="col-md-2">
                      <div class="form-group">
                          <label for="field-2" class="control-label">List Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="listprice[]" id="netpriceedit1" readonly value="">
                           <input type="hidden" name="masterallowedprice" id="masterallowedprice1" value="">
                          <span style="color:red">Allowed Price: <span id="printproductallowedprice1"></span></span>

                      </div>
                    </div>

                      <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Discount(%)<span></span></label>
                              <input type="text" class="form-control" name="discount[]" id="discountinpercent1" onblur="calculatedisprice1(1); checkqtyandupdatetotalprice1(1); updatediscountonfinalprice1(1);" value="0">
                          </div>
                      </div>

                        <div class="col-md-2">
                          <div class="form-group">
                              <label>Offered Price</label>
                              <input type="text" class="form-control" name="offered_price[]" id="offered_price1" value="" onblur=" updatediscountonfinalprice1(1); checkqtyandupdatetotalprice(1);">
                          </div>
                      </div>


                        <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Qty</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control"  name="qty[]" id="qty1" value="" onkeyup="checkqtyandupdatetotalprice1(1);">
                          </div>
                      </div>



                        <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Unit</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text"  readonly class="form-control"  name="unit[]" id="newproductunit1" value="">
                             
                          </div>
                      </div>

                         <div class="col-md-2">
                          <div class="form-group">
                              <label>Final Price</label>
                              <input type="text" class="form-control" name="finalprice[]" id="finalprice1" value="" readonly>
                          </div>
                      </div>
                      
                        <div class="col-md-1">
                            <div class="form-group" style="margin-top:25px">
                                <a class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                                
                               
                            </div>

                            <div id="dynamictasks"></div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Select Distributor*</label>
                    <select class="form-control" name="selectdistributor" id="selectdistributor" onchange="setdistributorterms();" required>
                        <option value="">Select Distributor</option>
                        <?php 
                            $q = $this->db->select('id, firm_name')->from('distributor')->where('status',1)->get();
                            foreach($q->result() as $dist){
                            ?>
                            <option value="<?php echo $dist->id;?>" <?php if($dist->id==$rowss->distributor){echo "selected";}?>><?php echo $dist->firm_name;?></option>
                    <?php }?>
                    </select>
                </div>

            </div>
            <script type="text/javascript">
                function setdistributorterms(){
                    var selectdistributor = $("#selectdistributor").val();
                    $.ajax({
                                type:"post",
                                url:"<?php echo page_url;?>Customer/setdistributorterms",
                                data:{selectdistributor:selectdistributor},
                                success:function(data) {

                               $("#distributorterms").val(data);
                               
                                }
                                });
                }
            </script>

            <div class="col-md-9">
                <div class="form-group">
                    <label>Terms for Distributor</label>
                    <input type="text" name="distributorterms" id="distributorterms" value="" class="form-control" readonly>
                </div>
            </div>
        <div class="col-md-12">
            <label for="field-2" class="control-label" style="color:#000;">Terms & Conditions <span style="color:red">*</span></label>
            <textarea class="form-control drums" name="termsconditions" id="termsconditions" required><?php echo $rowss->general_terms;?></textarea>
             <script>
            CKEDITOR.replace('termsconditions');
            </script>
       
        </div>
        </div>
        <div class="row" style="padding-top:30px">
        <div class="col-sm-12 ">
                      	<div class="form-group pull-right"><input type="submit" id="saves_form" class="btn btn-info" value="Create Quotation"></div>
                        
                    </div>
                </div>
                </div>
            </form>
         </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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

      

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>
<script type="text/javascript">

 $(document).ready(function() {   
     jQuery('#create_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
     });

 });


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
                $("input[name='qty[]']").attr('required',false);
                $("input[name='listprice[]']").attr('required',false);
            }
        }

   

    function getProductsOfCompany(j) {
        var company_location = $("#company_location").val();

                 $.ajax({
                    type:"post",
                    url:"<?php echo page_url;?>Open_leads/getCompanyProducts",
                    data:{company_location:company_location},

                    success:function(data) {
                        $(".product_name"+j).html(data);
                    }
                });
    }

 
  

</script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script>
try {
  fetch(new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", { method: 'HEAD', mode: 'no-cors' })).then(function(response) {
    return true;
  }).catch(function(e) {
    var carbonScript = document.createElement("script");
    carbonScript.src = "//cdn.carbonads.com/carbon.js?serve=CK7DKKQU&placement=wwwjqueryscriptnet";
    carbonScript.id = "_carbonads_js";
    document.getElementById("carbon-block").appendChild(carbonScript);
  });
} catch (error) {
  console.log(error);
}
</script>

<script type="text/javascript">
             
            $(document).on('click', '.remove', function(){
                $(this).parents(".fieldGroups").remove();
            });

</script>
<script type="text/javascript">
    
$(document).ready(function(){
     getProductsOfCompany(0);
var purl="<?php echo page_url;?>Open_leads/getrecommendations";
$('#competitor_product0').select2({ 
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


$('#product1').select2({ });
$('#selectdistributor').select2({ });
var i = 2;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"> <input type="hidden" class="editid1" name="editid[]" value=""><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control" name="product[]" id="product'+i+'" onchange="getproductactualprice('+i+'); getnewproductactualunit('+i+'); getproductactualallowprice('+i+');"> <option value="">Select</option><?php $sql1= $this->db->select('b.id,b.instruments_name, pack_size')->from('company_products a')->join('presto_instruments b','b.id=a.product_id')->get(); if($sql1->num_rows()>0){ foreach($sql1->result() as $row4){ ?><option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?> (<?php echo $row4->pack_size;?>)</option><?php }} ?></select><span id="recommendation'+i+'"></span></div></div> <div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">List Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="listprice[]" id="netpriceedit'+i+'" readonly value=""><input type="hidden" name="masterallowedprice" id="masterallowedprice'+i+'" value=""> <span style="color:red">Allowed Price: <span id="printproductallowedprice'+i+'"></span></span></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Discount(%)<span></span></label><input type="text" class="form-control" name="discount[]" id="discountinpercent'+i+'" onblur="calculatedisprice1('+i+'); checkqtyandupdatetotalprice1('+i+'); updatediscountonfinalprice1('+i+');" value="0"></div></div><div class="col-md-2"><div class="form-group"><label>Offered Price</label><input type="text" class="form-control" name="offered_price[]" id="offered_price'+i+'" value="" onblur="updatediscountonfinalprice1('+i+'); checkqtyandupdatetotalprice('+i+');"></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control"  name="qty[]" id="qty'+i+'" value="" onkeyup="checkqtyandupdatetotalprice1('+i+');"></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Unit</label><span id="error_rack_location" style="color:red;">*</span><input type="text"  readonly class="form-control"  name="unit[]" id="newproductunit'+i+'" value=""></div></div><div class="col-md-2"><div class="form-group"><label>Final Price</label><input type="text" class="form-control" name="finalprice[]" id="finalprice'+i+'" value="" readonly></div></div><div class="col-md-1" style="margin-top:25px"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-times"></i></button></div></div></div><br/>');

              
                initializeSelect2_product("product"+i);
                i++;
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

    $('#'+selectElementObj).select2({ });

}
      function getourproductname(id)
      {
         $(".comp_files"+id).css('display','none');
         $(".our_files"+id).css('display','none');
        $(".c_specfile_input"+id).css('display','');
        $("#c_spec"+id).html('');
        $(".c_msdsfile_input"+id).css('display','');
          $(".comp_files"+id).css('display','');
        $("#c_msds"+id).html('');
        var comproduct=$("#competitor_product"+id).val();

        if(comproduct!='')
        {

            $(".comp_files"+id).css('display','');

            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/getrecommendations_our_product",
            data:"comproduct="+comproduct,
            success:function(data){
                 $("#recommendation"+id).css('font-size','12px');
            $("#recommendation"+id).css('color','red');
            $("#recommendation"+id).css('font-weight','bold');
            $("#recommendation"+id).html(data);
            }
            });


            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/get_competitor_files",
            data:"comproduct="+comproduct,
            success:function(data){

                var a=data.split('|');

                if(a[0]!='')
                {
                    $(".c_specfile_input"+id).css('display','none');
                    $("#c_spec"+id).html(a[0]);
                }

                if(a[1]!='')
                {
                $(".c_msdsfile_input"+id).css('display','none');
                $("#c_msds"+id).html(a[1]);
                }
               
            }
            });


        }

      }

 
function getprice(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getactualprice",
                    data: "proid=" + proid,
                    success: function(data) {
                       
                        $("#netprice" + i).val(data);
                       
                    }
                });
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

        function allow_decimal(data) {
            var self = $("#" + data);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }


        }

         function geteditprice(i, pid) {
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
                        // $("#listpriceedit" + i).val(arr[0]);
                        $("#netpriceedit" + i).val(arr[0]);
                        }else
                        {
                        // $("#listpriceedit" + i).val('');
                        $("#netpriceedit" + i).val('');
                        }
                        $(".list_price_unit" + i).text(arr[1]);
                        $("#unitedit" + i).val(arr[1]);
                        $("#discountpriceshow" + i).css('display', '');
                        $("#discountpriceshowedit" +pid+i).text('display', '');


                        $("#discountpriceshowedit" +i).text('Allowed Price: '+arr[3]);
                        $("#discountpriceshowedit" +i).css('color', 'red');
                        $("#discountpriceshowedit" +i).css('font-weight', 'bold');

                        $("#unit_id_edit"+i).val(arr[2]);

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

        function getnetamtedit(i) {
            var qty = $('#qtyedit' + i).val();

            var listprice = $('#listpriceedit' + i).val();
            var discountprice = $('#discountpriceedit' + i).val();
            var discountpricehide = $('#discountpricehideedit' + i).val();
        
            if(parseFloat(listprice) > 0) {
                $("#discountpriceedit" + i).attr('readonly', false);
                $('#netpriceedit' + i).val(listprice);
            } else {
                $("#discountpriceedit" + i).attr('readonly', true);
                $('#netpriceedit' + i).val(0);
            }

            if (parseFloat(listprice) < parseFloat(discountpricehide)) {
                if (confirm('Offered Price is less than ' + discountpricehide + '. Are you sure you want to continue with this price?')) {

                    if (parseFloat(listprice) > 0 && parseFloat(discountprice) != '') {
                        var netamt = parseFloat(listprice) - parseFloat(discountprice);
                        $('#netpriceedit' + i).val(netamt);
                    } else {
                        $('#netpriceedit' + i).val(0);
                    }
                } else {
                    
                    $("#discountpriceedit" + i).val('');
                }
            } else {
                var netamt = parseFloat(listprice) - parseFloat(discountprice);
                $('#netpriceedit' + i).val(netamt);
            }

        }

        function getstock(flag)
        {

             $("#saves").attr('disabled',true);
             var product=$("#product"+flag).val();

               $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_available",
                data:"product_id="+product,
                success: function(data) {

                    $("#stock"+flag).val(data);
                    $("#saves").attr('disabled',false);
                    
                }
                });

        }
         function match_stock_availability2(flag)
        {

                var product=$("#product"+flag).val();
                var stock_avail=$("#stock"+flag).val();
                if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
                var qty_edit=$("#qty"+flag).val();
                if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

                if(parseFloat(qty_edit)>parseFloat(stock_avail))
                {
                alert('Available Stock is less than inputted Qty. Please change the Qty');
                $("#qty"+flag).val('');
                }
 
        }

        function CKEditorChange(name) {
          CKEDITOR.replace(name, {
            toolbar: [{
                name: 'clipboard',
                items: ['Undo', 'Redo']
              },
              {
                name: 'styles',
                items: ['Format', 'Font', 'FontSize']
              },
              {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting']
              },
              {
                name: 'colors',
                items: ['TextColor', 'BGColor']
              },
              {
                name: 'align',
                items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
              },
              {
                name: 'links',
                items: ['Link', 'Unlink']
              },
              {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
              },
              {
                name: 'insert',
                items: ['Image', 'Table']
              },
              {
                name: 'tools',
                items: ['Maximize']
              },
              {
                name: 'editing',
                items: ['Scayt']
              }
            ]
          });
        }
         function get_tnc() {
        
        var d="<?php echo $basic_terms;?>";
        CKEDITOR.instances['termsconditions'].setData(d);
                                    
                 
        }
</script>
<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>