<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo $restyiou->fms_flow;?></title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style type="text/css" media="print">
  @page {  size: A4;
   margin: 0.11mm 0.29mm 0mm 0.06mm; margin-bottom:0mm;}
   

input,
  textarea {
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
  }

   .pageinside{
    padding: 0.5cm;
    border: 5px #405473 double;
    height: 276mm;
}

  .grnhead
{
    display:block;
}
td {font-size:12px;text-align:center;}
th {font-size:13px;text-align:center;color:#F3F3F3;}
.print:last-child {
     page-break-after: auto;
}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    
    padding: 3px;
    text-align:center;
}

.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
   }
   .col-sm-12 {
        width: 100%;
   }
   .col-sm-11 {
        width: 91.66666667%;
   }
   .col-sm-10 {
        width: 83.33333333%;
   }
   .col-sm-9 {
        width: 75%;
   }
   .col-sm-8 {
        width: 66.66666667%;
   }
   .col-sm-7 {
        width: 58.33333333%;
   }
   .col-sm-6 {
        width: 50%;
   }
   .col-sm-5 {
        width: 41.66666667%;
   }
   .col-sm-4 {
        width: 33.33333333%;
   }
   .col-sm-3 {
        width: 25%;
   }
   .col-sm-2 {
        width: 16.66666667%;
   }
   .col-sm-1 {
        width: 8.33333333%;
   }
    html, body {
        height: 99%;    
    }
}

@media print 
{

table { page-break-inside:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-footer-group }

}
</style>
        <script src="http://assets.skexports.in/beta/assets/js/modernizr.min.js"></script>
        
<script>
function deleteiteminpo(challid,pono)
{
	if(confirm('Do you really want to delete the item from PO'))
	{
	document.location="http://assets.skexports.in/beta/Assets_management/deletepoitem/"+challid+"/"+pono;
	}
}
</script>
<style>
body{
	font-family: 'Roboto', sans-serif;
}
</style>

    </head>


    <body>


       <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>

        <div class="wrapper">
            <div class="container" style="padding-top:70px">

                <!-- Page-Title -->
                <div class="row page-title">
                    <div class="col-sm-12" style="padding-top:30px">
                    
                    <div class="col-md-4 page-title-box">
                         <span><a href="javascript:;"  onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</a></span>
                        </div>
                       


 
                    <div class="col-md-4">
                        <div class="page-title-box">
                            <div class="btn-group pull-right">
                              
                            </div>
                                                        <h4 class="page-title text-center">Purchase Requisition</h4>
                        </div>
                        </div>
                        <div class="col-md-4 page-title-box">
                        
                                                <div id="succmsg">
                        <div class="col-md-5">
                        <input type="checkbox" name="approved" id="approved" value='1' onChange="getpoapproved('81922020U','1')"> Mark as Approved
                        </div>
                        </div>
                        <div class="col-md-1"><strong>OR</strong></div>
                        <div class="col-md-6">
                        <select name="approved" id="approvedselect"  onChange="getpoapprovedselect('81922020U','1')" class="form-control">
                       <option value="">--Select PO Status--</option>
                       <option value="1" >Mark as Approved</option>
                       <option value="2" >Mark as Hold</option>
<option value="3" >Mark as Decline</option>
                       </select>
                       </div>
                       </div> 
                                               
                        <div class="hidden-print">
                                    <div class="">
                                        <a href="javascript:;" class="btn btn-inverse waves-effect waves-light" style="position:absolute;top: -37%;
    right: 101px;" onclick="printitem('81922020U','0');"><i class="fa fa-print"></i><br/>
    Print PO</a>
                                        <!--<a href="#" class="btn btn-primary waves-effect waves-light">Save</a>-->
                                        
                                       
                                    </div>
                                    
                                    
                                    <div class="">
                                        <a href="javascript:;" class="btn btn-inverse waves-effect waves-light" style="position:absolute;top: -37%;
    right: -41px;" onclick="printitem1('81922020U','0');"><i class="fa fa-print"></i><br/>
    Print Comp. PO</a>
                                        <!--<a href="#" class="btn btn-primary waves-effect waves-light">Save</a>-->
                                        
                                       
                                    </div>
                                    
                                    
                                    
                                </div>
                        </div>
                        
                                                 <div class="row">
                        
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        
                        
                        
                        
                        </div>
                        </div> 
                       
                                               
                    </div>
                   
                </div>
                <!-- end page title end breadcrumb -->


<page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="http://assets.skexports.in/beta/image_bank/company/logo_unit.png" alt="" height="34">&nbsp; SK EXPORTS</h3>
					<p class="address uppercase">D-5/3, OKHLA
PHASE-2
NEW DELHI-110020<br/><strong>GSTN:  07ACLFS3577R1Z2</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="color:white;font-size: 17px;">Purchase Requisition For General Items <div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>
											<th>To:</th>
											<td style="text-align:left">BATRA STORES</td>
										</tr>
										<tr>
											<th>Address:</th>
											<td style="text-align:left">31/1, GOVIND PURI, KALKAJI, NEAR KALKAJI DEPOT, NEW DELHI-110019</td>
										</tr>
										<tr>
											<th>Phone:</th>
											<td style="text-align:left">Mr. BATRA-7503881573</td>
										</tr>
										
										
										<tr>
											<th>Email:</th>
											<td style="text-align:left"></td>
										</tr>
										
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tr>
											<th style="text-align:left">PR No..</th>
											<td style="text-align:left">: #81922020U</td>
										</tr>
										<tr>
											<th style="text-align:left">Order date</th>
											<td style="text-align:left">: 20-Mar-2020</td>
																						</tr>
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					<table class="table tablenoborder" style="border-top:0;border-bottom:2px solid #333;margin-bottom:0px">
						<tbody>
													<tr>
								<td style="width:100%;font-size: 16px; font-weight: 500;text-align:left;padding-top:0px;padding-bottom:0px; ">
									<u>Delivery at</u><br>
									SK EXPORTS<br>
									<span style="font-size: 14px;">D-5/3, OKHLA
PHASE-2
NEW DELHI-110020</span><br>
									GSTIN: 07ACLFS3577R1Z2								</td>
															</tr>
						</tbody>
					</table>
											<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">Sl. No.</th>
								<th style="text-align:center;border-top:1px solid;">Item</th>
								<th style="text-align:center;border-top:1px solid;">Brand</th>
								<th style="text-align:center;border-top:1px solid;">Qty</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">Curr. Stock</th>
								<th class="" style="text-align:center;border-top:1px solid;">Packing Desc</th>
								<th style="text-align:center;border-top:1px solid;">Sub Unit</th>
								<th class="hideprice" style="text-align:center;border-top:1px solid;">Unit Rate</th>
								<th class="hideprice" style="text-align:center;border-top:1px solid;">Amt.</th>
																								<th class="hideprice" style="text-align:center;border-top:1px solid;">Total Amt.</th>
														<th class="prevpodetail" style="text-align:center;border-top:1px solid;width:100%;">Prev Po Details</th>
																<th class="page-title" style="text-align:center;border-top:1px solid;">Action</th>
							</tr>
							
							
						</thead>
						<tbody>
						
													     <tr style="text-align:center">
                                                        <td>1</td>
                                                        <td style="text-align:center">Clenzo</td>
                                                        <td style="text-align:center">Metropol</td>
                                                                                                                <td style="text-align:center">5 Bottle</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">4</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                                                           <div class="col-md-12" style="color:red">1  Bottle Contains 5&nbsp;Liter </div>
                                                 
                                                       </td>
                                                        <td>Liter</td>
												
                                                        <td class="hideprice">175.00</td>
                                                        
                                                        	<td  class="hideprice">875.00</td>	
																																											                                                                                                                <td  class="hideprice">875.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>10-Feb-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>71370620U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Bulk</strong></div>
      <div class="" style="font-size:12px;"><strong>20 bottle</strong></div> 
      <div class="" style="font-size:12px;"><strong>175.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/62/2888"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2888','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>2</td>
                                                        <td style="text-align:center">Colin</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">5 Liter</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Liter</td>
												
                                                        <td class="hideprice">74.00</td>
                                                        
                                                        	<td  class="hideprice">370.00</td>	
																																											                                                                                                                <td  class="hideprice">370.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>18-Dec-2019</strong></div>
      <div class="" style="font-size:12px;"><strong>12831819U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>5 BOTTLE</strong></div> 
      <div class="" style="font-size:12px;"><strong>74.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/124/2889"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2889','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>3</td>
                                                        <td style="text-align:center">Garbage Bag Big</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">5 Packet</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Packet</td>
												
                                                        <td class="hideprice">65.00</td>
                                                        
                                                        	<td  class="hideprice">325.00</td>	
																																											                                                                                                                <td  class="hideprice">325.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>06-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>9260420U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>10 PACKET</strong></div> 
      <div class="" style="font-size:12px;"><strong>65.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/56/2890"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2890','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>4</td>
                                                        <td style="text-align:center">Hand Gloves (rubber) 10"</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">5 Sets</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Sets</td>
												
                                                        <td class="hideprice">127.00</td>
                                                        
                                                        	<td  class="hideprice">635.00</td>	
																																											                                                                                                                <td  class="hideprice">635.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>01-Aug-2019</strong></div>
      <div class="" style="font-size:12px;"><strong>65600119</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>3 SET</strong></div> 
      <div class="" style="font-size:12px;"><strong>127.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/889/2891"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2891','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>5</td>
                                                        <td style="text-align:center">Hand Wash (pouch)</td>
                                                        <td style="text-align:center">Dettol</td>
                                                                                                                <td style="text-align:center">3 Sets</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                                                           <div class="col-md-12" style="color:red">1  Sets Contains 3&nbsp;Pieces </div>
                                                 
                                                       </td>
                                                        <td>Pieces</td>
												
                                                        <td class="hideprice">130.00</td>
                                                        
                                                        	<td  class="hideprice">390.00</td>	
																																											                                                                                                                <td  class="hideprice">390.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Bulk</strong></div>
      <div class="" style="font-size:12px;"><strong>9 SET</strong></div> 
      <div class="" style="font-size:12px;"><strong>130.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/123/2892"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2892','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>6</td>
                                                        <td style="text-align:center">Handwash Pouch With Bottle</td>
                                                        <td style="text-align:center">Dettol</td>
                                                                                                                <td style="text-align:center">2 Pieces</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Pieces</td>
												
                                                        <td class="hideprice">95.00</td>
                                                        
                                                        	<td  class="hideprice">190.00</td>	
																																											                                                                                                                <td  class="hideprice">190.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>07-Nov-2019</strong></div>
      <div class="" style="font-size:12px;"><strong>50350719</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>2 Pcs</strong></div> 
      <div class="" style="font-size:12px;"><strong>95.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/1137/2893"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2893','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>7</td>
                                                        <td style="text-align:center">Harpic</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">1 Bottle</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Bottle</td>
												
                                                        <td class="hideprice">180.00</td>
                                                        
                                                        	<td  class="hideprice">180.00</td>	
																																											                                                                                                                <td  class="hideprice">180.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>07-Nov-2019</strong></div>
      <div class="" style="font-size:12px;"><strong>50350719</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>1 BOTTLE</strong></div> 
      <div class="" style="font-size:12px;"><strong>180.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/49/2894"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2894','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>8</td>
                                                        <td style="text-align:center">Lizol Lime (5 Pouch - 500 Ml)</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">6 Packet</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Packet</td>
												
                                                        <td class="hideprice">78.00</td>
                                                        
                                                        	<td  class="hideprice">468.00</td>	
																																											                                                                                                                <td  class="hideprice">468.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>5 PACKET</strong></div> 
      <div class="" style="font-size:12px;"><strong>67.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/40/2895"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2895','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>9</td>
                                                        <td style="text-align:center">Room Freshner</td>
                                                        <td style="text-align:center">Godrej</td>
                                                                                                                <td style="text-align:center">2 Pieces</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Pieces</td>
												
                                                        <td class="hideprice">110.00</td>
                                                        
                                                        	<td  class="hideprice">220.00</td>	
																																											                                                                                                                <td  class="hideprice">220.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>2 Pcs</strong></div> 
      <div class="" style="font-size:12px;"><strong>110.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/61/2896"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2896','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>10</td>
                                                        <td style="text-align:center">Sanitory Cube</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">2 Packet</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Packet</td>
												
                                                        <td class="hideprice">75.00</td>
                                                        
                                                        	<td  class="hideprice">150.00</td>	
																																											                                                                                                                <td  class="hideprice">150.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>3 PACKET</strong></div> 
      <div class="" style="font-size:12px;"><strong>75.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/122/2897"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2897','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>11</td>
                                                        <td style="text-align:center">Surf Blue Easy Wash</td>
                                                        <td style="text-align:center">Surf excel</td>
                                                                                                                <td style="text-align:center">2 Kilogram</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Kilogram</td>
												
                                                        <td class="hideprice">100.00</td>
                                                        
                                                        	<td  class="hideprice">200.00</td>	
																																											                                                                                                                <td  class="hideprice">200.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>2 KG</strong></div> 
      <div class="" style="font-size:12px;"><strong>100.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/51/2898"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2898','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>12</td>
                                                        <td style="text-align:center">Tile Cleaning Brush (scotch)</td>
                                                        <td style="text-align:center">N.a.</td>
                                                                                                                <td style="text-align:center">2 Pieces</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Pieces</td>
												
                                                        <td class="hideprice">37.00</td>
                                                        
                                                        	<td  class="hideprice">74.00</td>	
																																											                                                                                                                <td  class="hideprice">74.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>30-Jan-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>46033020U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>5 Pcs</strong></div> 
      <div class="" style="font-size:12px;"><strong>37.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/886/2899"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2899','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														     <tr style="text-align:center">
                                                        <td>13</td>
                                                        <td style="text-align:center">Vimbar Tub</td>
                                                        <td style="text-align:center">Vim</td>
                                                                                                                <td style="text-align:center">2 Pieces</td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red">0</span></td>  
                                                         
                                                    
 
                                                       <td class="">
                                                           
                                                          NA                
                                                       </td>
                                                        <td>Pieces</td>
												
                                                        <td class="hideprice">35.00</td>
                                                        
                                                        	<td  class="hideprice">70.00</td>	
																																											                                                                                                                <td  class="hideprice">70.00</td>

<td class="prevpodetail" style="width:100%;"><div class="row">
    <div class="col-sm-4" style="text-align:left;">
      <div class="" style="font-size:12px;">Date:</div>
      <div class="" style="font-size:12px;">PO:</div>
	  <div class="" style="font-size:12px;">Type:</div>
      <div class="" style="font-size:12px;">Qty:</div> 
      <div class="" style="font-size:12px;">Rate:</div> 
      <div class="" style="font-size:12px;">Sup.:</div>
        
    </div>
	
		 <div class="col-sm-8" style="text-align:left;">
      <div class="" style="font-size:12px;"><strong>02-Mar-2020</strong></div>
      <div class="" style="font-size:12px;"><strong>36302920U</strong></div>
	  <div class="" style="font-size:12px;"><strong>Pcs</strong></div>
      <div class="" style="font-size:12px;"><strong>2 Pcs</strong></div> 
      <div class="" style="font-size:12px;"><strong>35.00</strong></div> 
      <div class="" style="font-size:12px;"><strong>BATRA STORES</strong></div>
        
    </div>
	
	</div>
	
	</td>	   
														
                                                         <td  class="page-title"><a href="http://assets.skexports.in/beta/Assets_management/editpoitem/81922020U/52/2900"><i class="fa fa-pencil" title="Edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onClick="deleteiteminpo('2900','81922020U');"><i class="fa fa-close" title="Delete"></i></a></td>
                                                    </tr>
													
														
							<!--<tr class="totalamtrow">
								<td colspan="5">
									<p style="text-align:right;font-weight:500;margin:0">Total :</p>
								</td>
								<td>800</td>
								<td></td>
								<td></td>
								<td>325642</td>
								<td></td>
								<td>455</td>
								
							</tr>-->
						<!--	<tr>
								<td colspan="9" style="background-color:#ddd;border-top:1px solid #333;border-right:1px solid #333;">
								 									<p style="text-align:center;font-weight:500;font-size:16px;text-align:center;margin:0">Total Amount in Words<br>Four Thousand One Hundred  And Forty Seven  Rupees </p>
								</td>
								<td colspan="7" style="background-color:#ddd;border-top:1px solid #333;">
									<table class="totalamounttbl">
								
										<tr>
											<td>Total Amount.</td>
											<td>: INR 4147.00</td>
										</tr>
										
									</table>
								</td>
							</tr>-->
							
							
						</tbody>
					</table>
				
				</div>
							
			<!--	<div class="col-sm-6">
				    <p style="font-weight:bold;">&nbsp;&nbsp;Special Instructions:</p>
				    <p style="font-weight:bold;">&nbsp;&nbsp;Packing Instructions:</p>
				    <p style="font-weight:bold;">&nbsp;&nbsp;Remarks:</p>
				    
				    
				</div>-->
				<div class="col-sm-6">
    				    <p>&nbsp;&nbsp;Prepared By: <strong>ARVIND TOMAR</strong></p>
    				    					    <p>&nbsp;&nbsp;Approved By: <strong></strong></p>
				    <p>&nbsp;&nbsp;<strong>For SK EXPORTS</strong></p><br/>
				    <p>&nbsp;&nbsp;Authorised Signatory:</p>
				    
				    
				</div>
				<div class="col-sm-5">
				    <table class="table hideprice" border="1" style="text-align:center;margin-bottom:2px;margin-left:65px;">
				        <thead>
				            <tr>
				                <th style="text-align:center">Grand Total</th>
				                <th style="text-align:right;width:60px;">4147.00</th>
				            </tr>
				            
				             <tr>
				                <th colspan="2" style="text-align:center">Four Thousand One Hundred  And Forty Seven  Rupees </th>
				            
				            </tr>
				              
				            
				        </thead>
				        
				        
				    </table>
				</div>
		<!--	<div class="col-sm-1"></div>
			<br/><br/><br/><br/>-->
			<!--<div class="container-fluid">
			    <div class="col-sm-12">
		
			<div class="col-sm-4" style="text-align:left;"></div>
				<div class="col-sm-4" style="text-align:center;"></div>
					<div class="col-sm-4" style="text-align:center;"><p><strong>For SK EXPORTS</strong></p><br/></div>
		
			</div>
			<div class="col-sm-12">
		
			<div class="col-sm-4" style="text-align:left;"><p>Prepared By:<br/> <strong>ARVIND TOMAR</strong></p></div>
							<div class="col-sm-4" style="text-align:left;"><p>Approved By:<br/> <strong></strong></p></div>
					<div class="col-sm-4" style="text-align:center;"><p>Authorized Signatory:</p></div>
		
			</div>
			</div>-->
			
			
        </div>
    </page>
	
	  <!-- Footer -->
                               <!-- End Footer -->
	<!-- jQuery  -->
        <script src="http://assets.skexports.in/beta/assets/js/jquery.min.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/bootstrap.min.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/detect.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/fastclick.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/jquery.blockUI.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/waves.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/jquery.slimscroll.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/jquery.scrollTo.min.js"></script>
        <script src="http://assets.skexports.in/beta/plugins/switchery/switchery.min.js"></script>

        <!-- App js -->
        <script src="http://assets.skexports.in/beta/assets/js/jquery.core.js"></script>
        <script src="http://assets.skexports.in/beta/assets/js/jquery.app.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
function getpoapproved(pono,pobytype)
{
var status=$("#approved").val();

$.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markpoapproved",
	data:"&pono="+pono+"&status="+status+"&indirect="+pobytype,
	success:function(data){
if($.trim(data)=='1')
{
 location.reload();
//$("#succmsg").html('<div style="color:red;font-weight:bold">PO Approved</div>');

/*if(confirm('Do you want to notify supplier about this PO?'))
{
 
 if(confirm('Send PO with price?'))
	{
	document.location="http://assets.skexports.in/beta/Assets_management/mailtosupplier/"+pono+"/Y";
	}else
	{
		document.location="http://assets.skexports.in/beta/Assets_management/mailtosupplier/"+pono+"/N";
	}
	
}else
{
location.reload();
}*/


}else if(data=='2')
{
$("#succmsg").html('<div style="color:red;font-weight:bold">PO on Hold</div>');
}
else if(data=='3')
{
$("#succmsg").html('<div style="color:red;font-weight:bold">PO Declined</div>');
}

	}
	
	
			});

}


function getpoapprovedselect(pono,pobytype)
{
var status=$("#approvedselect").val();

$.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markpoapproved",
	data:"&pono="+pono+"&status="+status+"&indirect="+pobytype,
	success:function(data){
if($.trim(data)=='1')
{
 location.reload();
//$("#succmsg").html('<div style="color:red;font-weight:bold">PO Approved</div>');

/*if(confirm('Do you want to notify supplier about this PO?'))
{
 
 if(confirm('Send PO with price?'))
	{
	document.location="http://assets.skexports.in/beta/Assets_management/mailtosupplier/"+pono+"/Y";
	}else
	{
		document.location="http://assets.skexports.in/beta/Assets_management/mailtosupplier/"+pono+"/N";
	}
	
}else
{
location.reload();
}*/


}else if(data=='2')
{
$("#succmsg").html('<div style="color:red;font-weight:bold">PO on Hold</div>');
}
else if(data=='3')
{
$("#succmsg").html('<div style="color:red;font-weight:bold">PO Declined</div>');
}

	}
	
	
			});

}


function printitem(pono,status)
{
    $(".prevpodetail").hide();

if(status=='1')
{
    if(confirm('Hide Supplier Rates before Printing?'))
{
    $(".hideprice").hide();
	$(".currstock").hide();
	
   
   $.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markprinted",
	data:"&pono="+pono,
	success:function(data){
$("#potypees").html('');
window.print();
}

});
}else
{
	if(confirm('Hide Current Stock'))
	{
	
	
	
    $(".currstock").hide();
	 $(".hideprice").show();
    
   $.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markprinted",
	data:"&pono="+pono,
	success:function(data){
$("#potypees").html('');
window.print();
}

});

}else{
		
		
		
    $(".currstock").show();
	 $(".hideprice").show();
    
   $.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markprinted",
	data:"&pono="+pono,
	success:function(data){

window.print();
}

});
		
	}



}
}else
{
alert('Approve to take a print');
return false;
}

}



function printitem1(pono,status)
{
   
if(status=='1')
{
    $(".hideprice").show();
    $(".currstock").show();
   $(".prevpodetail").css('display','');
   $("#potypees").html('With PO Comparision');
   window.print();

}else
{
alert('Approve to take a print');
return false;
}

}
function printitemold(pono,status)
{
    

if(status=='1')
{
    if(confirm('Hide Supplier Rates before Printing?'))
{
    $(".hideprice").hide();
   
   $.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markprinted",
	data:"&pono="+pono,
	success:function(data){

window.print();
}

});
}else
{
    $(".hideprice").show();
    
   $.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/markprinted",
	data:"&pono="+pono,
	success:function(data){

window.print();
}

});
}
}else
{
alert('Approve to take a print');
return false;
}

}



$( document ).ready(function() {
   // $(".prevpodetail").css('display','');
window.onafterprint = function() {
    console.log('This will be called after the user prints');   
};
});

function submitforapproval(pono)
{
	if(confirm('Submit PR for approval?'))
	{
		
		$.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/submit_for_approval/",
	data:"&pono="+pono,
	success:function(data){
		location.reload();
		}

});


}
	
}

function hiderates()
{
    if($('#makehiderates').is(":checked")){
        $(".hideprice").hide();
    }else
    {
      $(".hideprice").show(); 
    }
    
    
}

function saveinfo()
{
	//alert('hi');
	var savg=$("#savg").val();
	var oavg=$("#oavg").val();
	var stno=$("#stno").val();
	var stname=$("#stname").val();
	var bname=$("#bname").val();
	
	$.ajax({
	type:"post",
	url:"http://assets.skexports.in/beta/Assets_management/poextradata/81922020U",
	data:"&savg="+savg+"&oavg="+oavg+"&stno="+stno+"&stname="+stname+"&bname="+bname,
	success:function(data){
		//alert(data);
		location.reload();
		}
});

}
</script>

                
    </body>
</html>