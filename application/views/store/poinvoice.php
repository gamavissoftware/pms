<?php
$uri=$this->uri->segment(3);
if($uri=='')
{
	echo "Invalid Access"; exit;
}else
{
				$rest=$this->db->select('d.conversion_unit,d.conversion_weight,d.size_in_mm,a.remarks,a.prno,a.potype,a.approved,d.gst,a.source,a.sourceid,a.jobcard,d.part as machine_part,d.fincode,d.specification,d.hsn,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,a.vendor,a.price,a.addedOn as orderdate,a.unit,a.discount,a.approvedBy')->from('purchase_order a')->join('machine_parts_with_picture d','d.id=a.itemid')->join('system_users e','e.user_id=a.addedBy')->where('a.pono',$this->uri->segment(3))->order_by('d.part','ASC')->get();
				


}
$CI =& get_instance();
$CI->load->model('Store_model');

$instruction=$CI->Store_model->getpoinstruction();
?>
<!DOCTYPE html>
<html>
<head>
<title>PURCHASE ORDER</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/style.css">
<style type="text/css" media="print">
  @page {  size: A4;
   margin: 0mm 0mm 0mm 0mm; margin-bottom:0mm;}
   

.printhide
{
	display:none;
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
        height: auto;    
    }
}


</style>
</head>

<body>

<?php
if($rest->num_rows()>0)
{
	foreach($rest->result() as $data);
	/** GET Department **/
	$dep=$this->db->select('department')->from('departments')->where('department_id',$data->department_id)->get();
	if($dep->num_rows()>0)
	{
		foreach($dep->result() as $depp)
	$depart=$depp->department;
		
	}else{
		$depart='';
	}
	/** END **/
	
	
	/** Get Approved By Name **/
	$dep=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$data->approvedBy)->get();
	if($dep->num_rows()>0)
	{
		foreach($dep->result() as $depp)
	$appby=$depp->first_name." ".$depp->last_name;
		
	}else{
		$appby='';
	}
	/** END **/
	
	/** GET VENDOR **/
	$ven=$this->db->select('*')->from('vendors')->where('id',$data->vendor)->get();
	if($ven->num_rows()>0)
	{
		foreach($ven->result() as $ven1);
		$vname=$ven1->name;
		if($ven1->phone<>0)
		{
		$vphone=$ven1->phone;
		}else{
			
			$vphone='';
		}
		$vemail=$ven1->email;
		$vgst=$data->gst;
		$vendorgst=$ven1->gst;
	
	//	$gstpercentage = $ven1->gst_percentage;
		$vaddress=$ven1->address;
		$payterm=$ven1->payment_terms;
		$frieght=$ven1->deliveryby;

		$frieghtpercentage=$ven1->freightpercentage;

		$packpercentage=$ven1->packingpercentage;
		
	}else{
		
		$vname='';
		$vphone='';
		$vemail='';
		$vgst='';
		$vaddress='';
		$payterm='';
		$vendorgst='';
	//	$gstpercentage="";
        $frieght='';
        $frieghtpercentage='';
		$packpercentage='';
		
	}
	/** END **/
					

				?>
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv(<?php echo $data->approved;?>);">PRINT PO</span>    
        
    </div>
    
</div>				
    <page size="A4">
    	<div class="pageinside-invoice" id="poinvoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><!--<img src="<?php echo assets_url;?>logo.png" alt="" height="50">-->DYNACHEM DEEP INDIA PRIVATE LIMITED.</h3>
					<p class="address uppercase">PLOT NO- 61, SECTOR-6 
<br/>FARIDABAD HARYANA-121006<br/>PHONES: +91-8130192001, +91-7428912669<br/>Email: info@dynachemdeepindia.com<br/><strong>GSTN:  06AAHCD2410M1ZT</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">PURCHASE ORDER <div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>
											<th>TO.</th>
											<td style="text-align:left">: <?php echo $vname;?></td>
										</tr>
										
									      <tr>
											<th style="text-align:left">ADDRESS</th>
											<td style="text-align:left">:<?php echo $vaddress;?></td>
																						</tr>
																						
											<?php
											$jobcard='';
									
									
									       
											if($data->source==1)
											{
												
												$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$data->jobcard)->get();
												if($job->num_rows()>0)
												{
													foreach($job->result() as $job1);
													
													$jobcard='Jobcard-';
													
												}else{
													
													$jobcard='';
												}
											}else if($data->source==2)
											{
												/** Intend **/
												
												$indno=$CI->Store_model->getindentno($data->prno);
													
													$jobcard='INDENT- IND'.$indno;
													
												

											}												
											?>											
											<tr>
											<th style="text-align:left">PHONE</th>
											<td style="text-align:left">: <?php echo $vphone;?></td>
																						</tr>
																							<tr>
											<th style="text-align:left">EMAIL</th>
											<td style="text-align:left">: <?php echo $vemail;?></td>
																						</tr>
																							<tr>
											<th>GSTN</th>
											<td style="text-align:left">: <?php echo $vendorgst;?></td>
										</tr>
										
										
										
									
										
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tr>
											<th style="text-align:left">PO NO.</th>
											<td style="text-align:left">: <?php echo $this->uri->segment(3);?></td>
										</tr>
											<tr>
											<th>ORDER DATE</th>
											<td style="text-align:left">: <?php echo date('d-m-Y',strtotime($data->orderdate));?></td>
										</tr>
										
										<tr>
											<th>SOURCE</th>
											<td style="text-align:left">: <?php echo $jobcard;?></td>
										</tr>
										
									
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					<!--<table class="table tablenoborder" style="border-top:0;border-bottom:2px solid #333;margin-bottom:0px">
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
					</table>-->
											<div class="col-md-12" style="background-color:#457ee95c;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid; padding:3px;">Sr. No.</th>
							
								<th style="text-align:center;border-top:1px solid; padding:3px;">Item Code</th>
									<th style="text-align:center;border-top:1px solid; padding:3px;">HSN</th>
								
									<th style="text-align:center;border-top:1px solid; padding:3px;">Name</th>
									
								<th style="text-align:center;border-top:1px solid; padding:3px;">Specs./Size</th>
								<th class="" style="text-align:center;border-top:1px solid; padding:3px;">Qty</th>
								<th class="" style="text-align:center;border-top:1px solid; padding:3px;">Unit</th>
								
								
									<th style="text-align:center;border-top:1px solid; padding:3px;">List Price</th>
									<th style="text-align:center;border-top:1px solid; padding:3px;">Discount</th>
									<th style="text-align:center;border-top:1px solid; padding:3px;">Rate</th>
								<th class="hideprice" style="text-align:center;border-top:1px solid; padding:3px;">Total Amt.</th>

								<?php
								if($vendorgst!='')
								{
								?>
								<th class="hideprice" style="text-align:center;border-top:1px solid; padding:3px;">GST %</th>
								<?php
								}
								?>
								
							<!-- type of GST-->
								<?php
if($vendorgst<>''){								
									$statecode = substr($vendorgst,0,2);
									if($statecode=='06'){
									
								?>
								<th style="text-align:center;border-top:1px solid; padding:3px;">CGST</th>
								<th style="text-align:center;border-top:1px solid; padding:3px;">SGST</th>
									<?php }else{?>
									<th style="text-align:center;border-top:1px solid; padding:3px;">IGST</th>
									<?php }?>
		<?php } ?>					
								
													
																
							</tr>
							
							
						</thead>
						<tbody>
						<?php
						$i=1;
						$cost=array();
						$totalinarray=array();
						$rmk='';
						foreach($rest->result() as $data)
						{
						    $rmk.=$data->machine_part." - ".$data->remarks.'<br/>';
						    
							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
							if($restyu->num_rows()>0)
							{
								foreach($restyu->result() as $restyu1);
								$unival=strtoupper($restyu1->shortname);
							}else{
							
$unival='';							
							}
							$gstpercentage=$data->gst;
							?>
													     <tr style="text-align:center">
                                                        <td style="padding:3px;"><?php echo $i;?></td>
													
														<td style="text-align:center; padding:3px;"><?php echo $data->fincode;?></td>
														
															<td style="text-align:center; padding:3px;"><?php echo $data->hsn;?></td>
                                                        
                                                         
                                           	<td style="text-align:center; padding:3px;"><?php echo $data->machine_part;?></td>
                                                        	
                                                    <td style="text-align:center; padding:3px;"><?php echo $data->specification;?><?php if($data->size_in_mm<>''){?>/<?php echo $data->size_in_mm;?><?php } ?></td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                   <?php
                                                   if($data->conversion_unit>0)
                                                   {
                                                   	$finalqty=$data->conversion_weight*$data->qty;
                                                   	$prevqty="<span style='color:red'>".floatval($data->qty)." ".$unival."</span>";


													$restyu=$this->db->select('shortname')->from('units')->where('id',$data->conversion_unit)->get();
													if($restyu->num_rows()>0)
													{
													foreach($restyu->result() as $restyu1);
													$unival=strtoupper($restyu1->shortname);
													}else{

													$unival='';							
													}


                                                   }else
                                                   {
                                                   	$finalqty=$data->qty;
                                                   	$prevqty='';
                                                   }
                                                   ?>
																											   
													<td style="width:100px; padding:3px;"><?php echo floatval($finalqty);?> <br/><?php echo $prevqty;?></td>
                                                     <td class="" style="padding:3px;"><?php echo $unival;?></td>  
                                                         
                                                    
 
                                                      <?php
                                                      $listp=$CI->Store_model->getlistprice($data->itemid,$data->vendor);
                                                       $listp=$CI->Store_model->getlistprice($data->itemid,$data->vendor);
                                                      if($data->discount>0)
                                                      {
                                                      	$dis=$data->discount;
                                                      }else
                                                      {
                                                      	if(floatval($listp)>round(floatval($data->price),2))
                                                      	{
                                                      			$dis=floatval($listp)-round(floatval($data->price),2);
                                                      	}else
                                                      	{
                                                      		$dis=$data->discount;
                                                      	}

                                                      }
                                                      ?>
                                                 
                                                      <td class="hideprice" style="padding:3px;"><?php echo floatval($listp);?></td>
                                                      <td class="hideprice" style="padding:3px;"><?php echo $dis;?></td>
                                                       <td class="hideprice" style="padding:3px;"><?php echo round(floatval($data->price),2);?></td>
													   <?php
													   $totwithqty=$data->price*$finalqty;
													   
													   	$cost[]=$totwithqty;
													   ?>
													   <td class="hideprice" style="padding:3px;"><?php echo round(floatval($totwithqty),2);?></td>
													   <?php
													   if($vendorgst<>'')
													   {
													   ?>
													   <td class="hideprice" style="padding:3px;"><?php echo floatval($gstpercentage);?> %</td>
													   <?php
													   }
													   ?>
                                                        
												<?php
												$colps=0;
												if($vendorgst<>''){								
												$statecode = substr($vendorgst,0,2);
												if($statecode=='06'){
												$tamount = round(floatval($totwithqty),2);
												$cgstsgst = $gstpercentage/2;
												$gstamount = $tamount*$cgstsgst/100;
												$gstftotal[] = $gstamount;
												$finalgstamount = $tamount*$gstpercentage/100;
												$totalinarray[] = $tamount*$gstpercentage/100;
												$colps=2;
												?>
												<td style="text-align:center; padding:3px;"><?php echo $gstamount;?></td>
												<td style="text-align:center; padding:3px;" ><?php echo $gstamount;?></td>
												<?php }else{
												$tamount = round(floatval($totwithqty),2);
												$finalgstamount = $tamount*$gstpercentage/100;
												$totalinarray[] = $tamount*$gstpercentage/100;
												$gstftotal[] = $finalgstamount;
												$colps=1;
												?>
									<td style="text-align:center; padding:3px;"><?php echo $finalgstamount;?></td>
									<?php }?>
		<?php } ?>	    							                                                                                                              

											
                                                      
                                                    </tr>
													
											<?php
											$i++;
											} ?>
							
											<?php
											$colps=0;
											if($vendorgst<>''){								
											$statecode = substr($vendorgst,0,2);
											if($statecode=='06'){

											$colps=2;
											}else
											{
											$colps=1;
											}

											}


											if($vendorgst<>'')
											{				
											$colspannnnn=8+$colps;
											$colspannnnn1=8+$colps;
											}else
											{
											$colspannnnn=8;
											$colspannnnn1=8;
											}
											?>		

										<?php
											$frightamt=0;
											if($frieghtpercentage<>'')
											{
											if(count($cost)>0)
											{
											$totpr=array_sum($cost);
											$fright=$frieghtpercentage/100;
											$frightamt=$totpr*$fright;

											}else{
											$totpr=0;
											$frightamt=0;
											}


										?>

										<tr class="totalamtrow" style=" padding:3px;">
										<td colspan="10" style="text-align:center; padding:3px;">Add Freight Charges @<?php echo $frieghtpercentage;?>%</td>

										<td><p style="text-align:right;font-weight:500;margin:0"><?php echo $frightamt;?></p></td>
									
										<td></td>
										<td></td>
										</tr>
										<?php
										}
										?>



										<?php
										$packamt=0;
											if($packpercentage<>'')
											{
											if(count($cost)>0)
											{
											$totpr=array_sum($cost);
											$pack=$packpercentage/100;
											$packamt=$totpr*$pack;

											}else{
											$totpr=0;
											$packamt=0;
											}
										?>

										<tr class="totalamtrow" style="text-align:center; padding:3px;">
										<td colspan="10" style="text-align:center; padding:3px;">Add Packing Charges @<?php echo $packpercentage;?>%</td>

										<td><p style="text-align:right;font-weight:500;margin:0"><?php echo $packamt;?></p></td>
									
										<td></td>
										<td></td>
										</tr>
										<?php
										}
										?>


											<?php
											$coslsl=9;

											?>			  
							<tr class="totalamtrow">
								<td colspan="<?php echo $coslsl;?>" style="text-align:center; padding:3px;">
									Payment Term: <?php echo $payterm;?><br/>
									Freight Terms: <?php echo $frieght;?>
									
								</td>
								
								<td><p style="text-align:right;font-weight:500;margin:0">Total :</p></td>
								<?php
								if(count($cost)>0)
								{
									$totpr=array_sum($cost)+$packamt+$frightamt;
								}else{
									$totpr=0;
								}
								?>
								<td style="text-align:center; padding:3px;" ><?php echo $totpr;?></td>
								<td style="text-align:center; padding:3px;" ></td>
							<?php
							$colps=0;
								 //echo "<pre>"; print_r($gstftotal); exit;
								if($vendorgst<>''){								
									$statecode = substr($vendorgst,0,2);
									if($statecode=='06'){
									
									$colps=2;
								?>
								<td style="text-align:center; padding:3px;"><?php echo array_sum($gstftotal);?></td>
								<td style="text-align:center; padding:3px;"><?php echo array_sum($gstftotal);?></td>
									<?php }else{
										$colps=1;
										?>
									<td style="text-align:center; padding:3px;"><?php echo array_sum($gstftotal);?></td>
									<?php }?>
		<?php } ?>	    	    	
								
							</tr>
							
							<?php
							$colspannnnn=12+$colps;
							?>
							<tr>
								<td colspan="<?php echo $colspannnnn;?>" style="border:1px solid #333;padding:4px 6px;font-size:10px;font-style:italics;">Remarks: <?php echo $rmk;?> 
</i></td>
							</tr>
							
							<tr>
								<td colspan="<?php echo $colspannnnn;?>" style="border:1px solid #333;padding:4px 6px;font-size:10px;font-style:italics;">Instructions : <i><?php echo $instruction;?>
</i></td>
							</tr>
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
							
		<div class="col-sm-6">
				    <p style="font-weight:bold;">&nbsp;&nbsp;Raised by: <?php echo ucfirst($data->first_name);?> <?php echo ucfirst($data->last_name);?></p>
				      <p style="font-weight:bold;">&nbsp;&nbsp;Approved by: <?php echo ucwords($appby);?></p>
				    <p style="font-weight:bold;">&nbsp;&nbsp;FOR DYNACHEM DEEP INDIA PRIVATE LIMITED.</p>
					<p style="font-weight:bold;">&nbsp;&nbsp;</p>
					
				
				    <p style="font-weight:bold;">&nbsp;&nbsp;Authorized Signatory:</p>
				    
				    
				</div>
				
				
				<div class="col-sm-5">
				    <table class="table hideprice" border="1" style="text-align:center;margin-bottom:2px;margin-left:65px;">
				        <thead>
				            <tr>
				                <th style="text-align:center">Grand Total</th>
								<?php
								if($vendorgst<>'')
								{
								if(count($cost)>0 && count($totalinarray)>0)
								{
									$tot=array_sum($cost)+array_sum($totalinarray)+$packamt+$frightamt;
}else{
	$tot=0;
}

}else{
	
	$tot=$totpr;
}
$amountinwords = ucwords($CI->Store_model->getIndianCurrency(round($tot)));

								?>
				                <th style="text-align:right;width:60px;"><?php echo number_format($tot,2);?></th>
				            </tr>
				            
				             <tr>
				                <th colspan="2" style="text-align:center"><?php echo $amountinwords;?></th>
				            
				            </tr>
				              
				            
				        </thead>
				        
				        
				    </table>
				</div>
			<div class="col-sm-1"></div>
			<br/><br/><br/><br/>
			
			
        </div>
    </page>
	
	<?php
	}
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	 <script type="text/javascript" src="<?php echo assets_url;?>js/html2canvas.js"></script>
<script>

function printDiv(status) 
{
if(status!=1)
	{
		alert('Po cannot be printed since it is unapproved');
		return false;
	}else{

  window.print();
  
	}

}
	
	$( document ).ready(function() {
		
	

var pono="<?php echo $this->uri->segment(3);?>";
html2canvas(document.getElementById('poinvoice')).then(function(canvas) {
var base64URL = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');


// AJAX request
$.ajax({
url: '<?php echo page_url;?>Store/savepoimage',
type: 'post',
data: {image: base64URL,ponumber:pono},
success: function(data){
}
});
});



		
	//	window.print();
	});
</script>
	</body>

</html>
