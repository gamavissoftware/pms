<?php
$uri=$this->uri->segment(3);
if($uri=='')
{
	echo "Invalid Access"; exit;
}else
{
				$rest=$this->db->select('a.*,c.type,c.source,b.vendor,b.unit,b.price,b.addedOn as podate,b.potype')->from('debitnote a')->join('purchase_order b','a.pono=b.pono')->join('purchase_request c','b.prno=c.prno')->where('a.dbno',$this->uri->segment(3))->order_by('a.addedOn','ASC')->get();
				
		

}
$CI =& get_instance();
$CI->load->model('Store_model');
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


<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT DEBIT NOTE</span>    
        
    </div>
    
</div>	
<?php
if($rest->num_rows()>0)
{
	foreach($rest->result() as $data);
	
	$usermade=$CI->Store_model->getsusername($data->addedBy);
	$vendordetail=$CI->Store_model->getvendodetails($data->vendor);
	if(count($vendordetail)>0)
	{
		$vname=$vendordetail['name'];
		if($vendordetail['phone']<>0)
		{
		$vphone=$vendordetail['phone'];
		}else{
			$vphone='';
		}
		$vaddress=$vendordetail['address'];
		$vemail=$vendordetail['email'];
	
		$vgst=$vendordetail['gst'];
		
	}else{
		
		$vname='';
		$vphone='';
		$vaddress='';
		$vemail='';

		$vgst='';
	}
	if($data->potype==0)
	{
		$itemname=$CI->Store_model->getmachineitemname($data->itemid);
		$od=$CI->Store_model->getmachineotherdetails($data->itemid);
		if(count($od)>0)
				{
			
				$fincode=$od['fincode'];
					$specification=$od['specialization'];
				}else{
					$fincode='';
					$specification='';
				}
	}else{
		$itemname=$CI->Store_model->getgeneralitemname($data->itemid);
		$fincode='';
		$specification='';
	}
	
	$unit=$CI->Store_model->getunit($data->unit);

?>			
    <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="<?php echo assets_url;?>logo.png" alt="" height="50"></h3>
					<p class="address uppercase">I-42, DLF INDUSTRIAL Area, PHASE-1
<br/>FARIDABAD HARYANA-121003<br/>PHONES: 0129-4272727 / 0129-4083111<br/><strong>GSTN:  07ACLFS3577R1Z2</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">DEBIT NOTE<div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr><u>DETAILS OF THE PARTY</u></tr>
										<tr>
											
											<td style="text-align:left"><?php echo strtoupper($vname);?></td>
											
										</tr>
										<tr><td><?php echo strtoupper($vaddress);?></td></tr>
										<tr><td><?php echo strtoupper($vphone);?></td></tr>
										<tr><td><?php echo strtoupper($vgst);?></td></tr>
										
										
										
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tr>
											<th style="text-align:left">SERIAL NO.</th>
											<td style="text-align:left">: <?php echo $this->uri->segment(3);?> </td>
										</tr>
											<tr>
											<th>DATE OF ISSUE</th>
											<td style="text-align:left">: <?php echo date('d-m-Y',strtotime($data->addedOn));?> </td>
										</tr>
										
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					
											<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
								<th style="text-align:center;border-top:1px solid;">HSN/SAC CODE</th>
								<th style="text-align:center;border-top:1px solid;">PART NAME</th>
								
								<th style="text-align:center;border-top:1px solid;">DESCRIPTION OF GOODS & SERVICE </th>
								<th class="" style="text-align:center;border-top:1px solid;">QUANTITY</th>
								<th class="" style="text-align:center;border-top:1px solid;">UNIT</th>
								
								<th style="text-align:center;border-top:1px solid;">RATE PR UNIT</th>
								<th style="text-align:center;border-top:1px solid;">TOTAL AMOUNT</th>
								
								<?php
								if($vgst<>'')
								{
									$result = substr($vgst, 0, 2);	
									if($result=='07')
									{
								?>
								<th style="text-align:center;border-top:1px solid;">CGST</th>
								<th style="text-align:center;border-top:1px solid;">SGST</th>
									<?php
									}else{
									?>
									
								<th class="hideprice" style="text-align:center;border-top:1px solid;">IGST</th>
								<?php
									}
								}
								?>
								<th style="text-align:center;border-top:1px solid;">TAXABLE AMOUNT </th>
							
								
													
																
							</tr>
							
							
						</thead>
						<tbody>
						
													     <tr style="text-align:center">
                                                        <td>1</td>
														<td style="text-align:center"><?php echo $fincode;?></td>
														<td style="text-align:center"><?php echo $itemname;?></td>
                                                        <td style="text-align:center"><?php echo $specification;?></td>
                                                        <td><?php echo floatval($data->qty);?></td>
														<td class=""><span style="color:red"><?php echo $unit;?></span></td>  
                                                        <td><?php echo floatval($data->price);?></td>
														<?php
														$tot=floatval($data->qty*$data->price);
														?>
													   <td><?php echo $tot;?></td>
														<?php
														if($vgst<>'')
														{
															if($result=='07')
															{
																$c=10;
															
															$s=18/100;
															
															$tot1=$tot*$s;
															
															$totgst=$tot1;
															$sgst=$tot1/2;
														?>
														<td><?php echo $sgst;?></td>
														<td><?php echo $sgst;?></td>
														<?php
															}else
															{
																$c=9;
																
																$s=18/100;
															
															$tot1=$tot*$s;
															
															$totgst=$tot1;
															$sgst=$tot1;
															?>
														<td><?php echo $sgst;?></td>
														<?php
															}
														
														}else{
															$c=8;
															$totgst=0;
														}
														
														$total=$tot+$totgst;
														$grandtot[]=$total;
														?>
													    <td><?php echo $total;?></td>
                                                        
                                                        	
																																											                                                                                                              

											
                                                      
                                                    </tr>
													
																	

														  
							<tr class="totalamtrow">
							
								<td colspan="<?php echo $c;?>">
									Remarks: <?php echo $data->reason;?>
								</td>
								<?php
								if(count($grandtot)>0)
								{
									$grand=array_sum($grandtot);
								}else{
									$grand=0;
								}
								?>
								<td><p style="text-align:left;font-weight:500;margin:0">Total : <?php echo $grand;?></p></td>
								
								
								
							</tr>
						
							
							
						</tbody>
					</table>
					
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						
						 								
						<tbody>
							<tr>
								<td class="w80" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>Againts Inv. No.: <?php echo $data->pono;?></tr>
										<tr>
											<td>Date of Invoice: <?php echo date('d-m-Y',strtotime($data->podate));?></td>
											
											
										</tr>
										
										
										
									</table>
								</td>
								<td class="w30">
									<table class="tablepodetail w120">
									
											<?php
											$amountinwords = ucwords($CI->Store_model->getIndianCurrency(round($grand)));
											?>
										<tr>
											<td>Total Value (In Words):<strong><?php echo $amountinwords;?></strong></td>
											
										</tr>
										<tr>
									    <td>Total Value (In Figures): <strong><?php echo $grand;?></strong></td>
										
										</tr>
										
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				
				</div>
							
		<div class="col-sm-11">
				    <p style="font-weight:bold;">&nbsp;&nbsp;MADE BY <?php echo $usermade;?> </p>
				    <p style="font-weight:bold;">&nbsp;&nbsp;</p>
					
				</div>
				
				
				
			<div class="col-sm-1"></div>
			<br/><br/><br/><br/>
			
			
        </div>
    </page>
	<?php
}else{
?>
DATA IS MISSING FOR DEBIT NOTE
<?php
}
?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>

function printDiv() 
{

  window.print();

}
	
	$( document ).ready(function() {
		
	//	window.print();
	});
</script>
	</body>

</html>