<?php
$uri=$this->uri->segment(3);
$accountsflag=$this->uri->segment(4);
if($uri=='')
{
	echo "Invalid Access";
}else
{
	
			$rest=$this->db->select('a.*,a.itemid as mrnitemid,a.gateentryno as mrngatentry')->from('mrn a')->where('a.mrndone','1')->where('a.gateentryno',$uri)->order_by('a.addedOn','DESC')->group_by('a.id')->get();
		
				


}
$CI =& get_instance();
$CI->load->model('Store_model');
?>
<!DOCTYPE html>
<html>
<head>
<title>MATERIAL RECIEPT NOTE</title>
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
    <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT</span>    
        
    </div>
    
</div>

<?php

if($rest->num_rows()>0)
{

	foreach($rest->result() as $rest111);

$podata=$this->db->select('b.*,b.source as mainsource,b.qty as porequiredqty')->from('purchase_order b')->where('b.id',$rest111->poid)->get();
if($podata->num_rows()>0)
{
    foreach($podata->result() as $porow);
    
    
}else
{
    echo "PO NOT FOUND"; exit;
}

	/** GET VENDOR **/
	$ven=$this->db->select('*')->from('vendors')->where('id',$porow->vendor)->get();
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
		$vgst=$ven1->gst;
		$vendorgst=$ven1->gst;
	
	//	$gstpercentage = $ven1->gst_percentage;
		$vaddress=$ven1->address;
		$payterm=$ven1->payment_terms;
		$frieght=$ven1->deliveryby;
		
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
		
	}
	/** END **/
?>

  <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="<?php echo assets_url;?>images/logo_mitr.png" alt="" height="50"></h3>
					<p class="address uppercase">PLOT NO- 61, SECTOR-6 <br>FARIDABAD HARYANA-121006<br>PHONES: +91-8130192001, +91-7428912669<!--<strong>GSTN:  07ACLFS3577R1Z2</strong>--></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">Material Receipt Report<div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
					<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tbody><tr>
											<th>TO.</th>
											<td style="text-align:left">: <?php echo $vname;?></td>
										</tr>
										
									      <tr>
											<th style="text-align:left">ADDRESS</th>
											<td style="text-align:left">: <?php echo $vaddress;?></td>
																						</tr>
																						
																						
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
											<td style="text-align:left">: <?php echo $vgst;?></td>
										</tr>
										
										
										
									
										
									</tbody></table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tbody><tr>
											<th style="text-align:left">DATE</th>
											<td style="text-align:left">: <?php echo date('d-M-Y',strtotime($rest111->addedOn));?></td>
										</tr>
											<tr>
											<th>GATE ENTRY NO</th>
											<td style="text-align:left">: <?php echo $this->uri->segment(3);?></td>
										</tr>
										
									
										
									</tbody></table>
								</td>
							</tr>
						</tbody>
					</table>

			
											<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
							<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
							<th style="text-align:center;border-top:1px solid;">Item Name</th>
							<th style="text-align:center;border-top:1px solid;">Item Code</th>
							<th style="text-align:center;border-top:1px solid;">Specification</th>
							<th style="text-align:center;border-top:1px solid;">PO No.</th>
							<th class="hideprice" style="text-align:center;border-top:1px solid;">Bill No.</th>
							<th class="" style="text-align:center;border-top:1px solid;">Bill Qty</th>
							<th class="" style="text-align:center;border-top:1px solid;">Recvd Qty</th>
							<?php
							if($accountsflag==1)
							{
							?>
							<th class="" style="text-align:center;border-top:1px solid;">PO Price</th>
							<th class="" style="text-align:center;border-top:1px solid;">Total</th>
							<?php

							}
							?>
					
							</tr>
							
						</thead>
						<tbody>

									<?php
									$t=1;
									$totarray[]=0;
									foreach($rest->result() as $rest111)
									{
										if($porow->potype=='0')
										{
										
											$idetail=$CI->Store_model->getmachineotherdetails($rest111->mrnitemid);

											if(count($idetail)>0)
											{
												$name=$idetail['name'];
												$fincode=$idetail['fincode'];
												$specialization=$idetail['specialization'];
												$size=$idetail['size'];
												$hsn=$idetail['hsn'];
												$unit=$idetail['unit'];
												$conunit=$idetail['conunit'];
												$conweight=$idetail['conweight'];
												if($conunit>0)
												{
												$convertedunit=$CI->Store_model->getunit($conunit);
												$finalreq=$rest111->reqty*$conweight." ".$convertedunit;
												$finalrecv=$rest111->recqty*$conweight." ".$convertedunit;
												$finalrecvqty=$rest111->recqty*$conweight;
												}else
												{
													$convertedunit=$CI->Store_model->getunit($unit);
													$finalreq=$rest111->reqty." ".$convertedunit;
													$finalrecv=$rest111->recqty." ".$convertedunit;
													$finalrecvqty=$rest111->recqty;
												}
											}else{  


												$name='';
												$fincode='';
												$specialization='';
												$size='';
												$hsn='';
												$unit='';
												$conunit=0;
												$conweight='';
												$convertedunit=$CI->Store_model->getunit($unit);
												$finalreq=$rest111->reqty." ".$convertedunit;
												$finalrecv=$rest111->recqty." ".$convertedunit;
												$finalrecvqty=$rest111->recqty;
												

											}

										}else
										{
											$idetail=$CI->Store_model->getgeneralitemdetails($rest111->mrnitemid);

										if(count($idetail)>0)
										{
											$name=$idetail['name'];
											$fincode='';
											$specialization='';
											$size='';
											$hsn='';
											$unit='';


										}else{  

											$name='';
											$fincode='';
											$specialization='';
											$size='';
											$hsn='';
											$unit='';

										}


										}

										if($unit<>'')
										{
										$restyu=$this->db->select('shortname')->from('units')->where('id',$unit)->get();
										if($restyu->num_rows()>0)
										{
										foreach($restyu->result() as $restyu1);
										$unival=strtoupper($restyu1->shortname);
										}else{

										$unival='';							
										}
										}else
										{
										$unival='';

										}
										
										/** GET PO PRICE DETAILS **/
										$popurchaseprice=0;
										$resteure=$this->db->select('price')->from('purchase_order')->where('id',$rest111->poid)->get();
										if($resteure->num_rows()>0)
										{
										foreach($resteure->result() as $resteure1);
										$popurchaseprice=$resteure1->price;
										}
										/** END **/
										?>
									<tr style="text-align:center">
									<td><?php echo $t;?></td>
									<td style="text-align:center"><?php echo $name;?></td>
									<td style="text-align:center"><?php echo $fincode;?></td>
									<td style="text-align:center"><?php echo $specialization;?></td>
									<td style="text-align:center"><?php echo $rest111->pono;?></td>
									<td><?php echo $rest111->billno;?></td>
									<!--<td class=""><span style="color:red"><?php echo $rest111->reqty;?> <?php echo $unival;?></span></td> 
									<td class=""><span style="color:red"><?php echo $rest111->recqty;?> <?php echo $unival;?></span></td>-->
									<td class=""><span style="color:red"><?php echo $finalreq;?></span></td> 
									<td class=""><span style="color:red"><?php echo $finalrecv;?></span></td>
									<?php
									if($accountsflag==1)
									{
										$tot=$finalrecvqty*floatval($popurchaseprice);

										$totarray[]=$tot;
									?>
									<td class=""><span style="color:red"><?php echo floatval($popurchaseprice);?></span></td> 
									<td class=""><span style="color:red"><?php echo $tot ;?></span></td> 
									<?php

									}
									?>
									</tr>
									<?php
								$t++;
									}

									if($accountsflag=='1')
									{
									?>

									<tr style="text-align:center">
									<td colspan="8">
									<td class="">Grand Total</span></td> 
									<td class=""><span style="color:red"><?php echo array_sum($totarray);?></span></td> 
									</tr>


									<?php
								}
								?>
					</tbody>
					</table>
				
				</div>
		<?php
		$mendonebyname=$CI->Store_model->getsusername($rest111->mrndoneBy);
		?>
				<div class="col-sm-6">
    				    <p>&nbsp;&nbsp;MRN By: <strong><?php echo $mendonebyname;?></strong></p>
    				    					
				    <p>&nbsp;&nbsp;</p><br>
				    <p>&nbsp;&nbsp;Authorised Signatory:</p>
				    
				    
				</div>
				
				
			
        </div>
    
	

	

</div></page>
<?php
}else
{
?>

<h3>No Data Found</h3>
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
