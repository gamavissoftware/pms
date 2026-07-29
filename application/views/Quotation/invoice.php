<?php 
  
$this->db->select('a.*,b.*,c.*');
$this->db->from('quotation_request a');
$this->db->join('presto_customers b','b.customer_id=a.customer_name');
$this->db->join('standard_subject c','c.id=a.subject');
$this->db->where('a.id',$this->uri->segment(3));

$res=$this->db->get();
//echo "<pre>";print_r($res->result());exit;
foreach($res->result() as $row);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Our Products</title>

       
<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

<style>

table.pretty thead th {

background: <?php echo $LOGO->colorcode;?>;

color:#fff;

font-weight:bold;

text-align:center;

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
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						 
                               
                            </div>
                           
                            <h4 class="page-title"> Performa Invoice</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <!-- <div class="panel-heading">
                                <h4>Invoice</h4>
                            </div> -->
                            <div class="panel-body">
                                <div class="clearfix">
								  
									 <div class="headd">
									  <img src="<?php echo assets_url;?>img/header.jpg" width="100%;">
									</div>
                                  <!--  <div class="pull-left">
                                        <h3 class="logo invoice-logo">Adminto</h3>
                                    </div>
                                    <div class="pull-right">
                                        <h4>Invoice # <br>
                                            <strong>2016-04-23654789</strong>
                                        </h4>
                                    </div> -->
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="pull-left m-t-30">
                                            <address>
                                              <strong><?php echo $row->company_name;?></strong><br>
                                              <?php echo $row->address;?><br>
                                              <strong>Phone :</strong> +91-<?php echo $row->contact_number;?>
                                              </address>
											   
									   <strong>Kind Attn. :</strong> <?php echo $row->customer_name;?><br>
										<strong>Subject :</strong> <?php echo $row->title;?>
                                        </div>
                                        <div class="pull-right m-t-30">
										<p class="m-t-10"><strong>Ref No.: </strong> <?php echo $row->reference_number;?></p>
										   <p><strong>Date:</strong><?php echo $row->quotation_date;?></p>
                                           
                                            
                                        </div>
                                    </div><!-- end col -->
                                </div>
                                <!-- end row -->

                                <div class="m-h-50"></div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table m-t-30">
                                                <thead>
                                                    <tr>
														<th>#</th>
														<th>Quantity</th>
														
														<th>Product</th>
														<th>Rate (<i class="fa fa-inr"></i>)</th>
														<th style="text-align:center;">Amount (<i class="fa fa-inr"></i>)</th>
													</tr>
												</thead>
                                                <tbody>
            <?php 
			$i=1;
			$this->db->select('a.*,b.*,c.*')->from('quotation_product_details a')->join('presto_product_specifications b','a.product_id=b.product_id','left')->join('quotation_product_specifications c','a.product_specification_id=c.id','left')->where('a.quotation_id',$this->uri->segment(3));
			$query =$this->db->get();
			$subtotal=array();
			foreach($query->result() as $rim)
			  {
			  ?> 
												   
												   <tr>
                                                        <td><?php echo $i;?></td>
                                                        <td><?php echo $rim->product_quantity;?></td>
                                                        
                                                        <td><?php echo $rim->product_name;?></td>
                                                        <td><?php echo $rim->price;?></td>
														<?php
														$rowsum=$rim->product_quantity*$rim->price;
														$subtotal[]=$rowsum;
														?>
                                                        <td style="text-align:right;"><?php echo $rowsum;?></td>
                                                    </tr>
                                                    <?php $i++; } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <!--<div class="clearfix m-t-40">
                                            <h5 class="small text-inverse font-600">PAYMENT TERMS AND POLICIES</h5>

                                            <small>
                                                Add : Packing Charges 2.00% on cost)<br>
												Add : Transit Insurance @ % <br>
												Add : IGST @ 18.00%<br>
                                            </small>
                                        </div>-->
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-6 col-md-offset-3">
                                        <p class="text-right"><b>Sub-total: </b><?php echo array_sum($subtotal);?></p>
                                        <?php if($row->gst_supply=='2'){ ?>
										<p class="text-right">IGST: <?php echo $row->gst_rate;?>%</p>
										<?php }else{ ?>
										
										<p class="text-right">SGST: <?php echo $row->gst_rate/2 ;?>%</p>
                                        <p class="text-right">CGST: <?php echo $row->gst_rate/2?>%</p>
										<?php } ?>
                                        <hr>
										<?php
										$subtotalvalue=array_sum($subtotal);
										$gstrate=$row->gst_rate;
										$gst=$subtotalvalue*$gstrate/100;
										$finalamount=$subtotalvalue+$gst;
										?>
                                        <h3 class="text-right"> INR <?php echo $finalamount;?></h3>
                                    </div>
                                </div>
                                <hr>
								 <h5><strong><u>Terms & Conditions</u></strong></h5>
         <small><table class="table table-striped">
              <thead>
                <tr>
                	<td></td>
                  <td></td>
                  <td></td>
                  
                  
                </tr>
              </thead>
              <tbody>
               <?php 
				$i=1;
			$this->db->select('a.*,b.*')->from('quotation_terms a')->join('terms_and_conditions_master b','a.terms_id=b.id','left')->where('a.quotation_id',$this->uri->segment(3));
			$tax=$this->db->get();
				
					foreach($tax->result() as $taxx){
			   ?>

			   <tr>
                <td><?php echo $i;?></td>
                  <td><?php echo $taxx->title;?></td>
                  <td><?php echo $taxx->description;?></td>
                </tr>
					<?php $i++; } ?>
					
              </tbody>
          </table></small>
		  <hr>
        <strong>Bank Details : </strong><br>
		<strong>Bank Name :</strong> ICICI Bank Ltd <br>
		<strong>Bank Account No :</strong> 008305002578<br>
		<strong>Account Name :</strong> Presto Stantest Pvt Ltd <br>
		<strong>Bank Branch Address :</strong> Booth 104-105,<br>
		District Centre,Sector -16, Faridabad <br>
		MICR No - 110229010<br>
		<strong>RTGS/NEFT IFSC :-</strong> ICIC0000083<br><br>
		
		<?php $sig=$this->db->select('*')->from('email_signature')->limit(1)->get();
		foreach($sig->result() as $sign);
		?>
       Regards,<br>
       <strong><?php echo $sign->name;?></strong><br>
       Email : <?php echo $sign->email;?><br>
       Mobile : <?php echo $sign->contact_number;?><br>
       <?php echo $sign->address;?><br>
	   
    
		 <div class="foot">
          <img src="<?php echo assets_url;?>img/footer.jpg" width="100%;" align="bootom">
        </div>
                                <div class="hidden-print">
                                    <div class="pull-right">
                                        <a href="javascript:window.print()" class="btn btn-inverse waves-effect waves-light"><i class="fa fa-print"></i></a>
                                        <a href="#" class="btn btn-primary waves-effect waves-light">Submit</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <!-- end row -->


                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

		
		
    </body>
</html>