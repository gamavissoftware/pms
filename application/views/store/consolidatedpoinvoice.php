<?php
$uri=$this->uri->segment(3);
$CI =& get_instance();
$CI->load->model('Store_model');
if($uri=='')
{
	echo "Invalid Access"; exit;
}else
{
    
                
				$rest=$this->db->select('a.id,a.prno,a.pono,a.potype,a.approved,a.approvedOn,a.source,a.sourceid,a.jobcard,a.itemid as mainitemid,a.qty,a.vendor,a.price,a.addedOn as orderdate,a.unit,a.discount,a.approvedBy')->from('purchase_order a')->where('a.vendor',$this->uri->segment(3))->where('a.completed','0')->where('a.approved','1')->get();
				



}
$already=0;

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

	

	
	/** GET VENDOR **/
	$ven=$this->db->select('*')->from('vendors')->where('id',$uri)->get();
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
	
	
	//	$gstpercentage = $ven1->gst_percentage;
		$vaddress=$ven1->address;
		$payterm=$ven1->payment_terms;
		$frieght=$ven1->deliveryby;
		
	}else{
		
		$vname='';
		$vphone='';
		$vemail='';
	
		$vaddress='';
		$payterm='';
        $frieght='';
		
	}
	/** END **/
					

				?>
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv(<?php echo $data->approved;?>);">PRINT CONSOLIDATED PO</span>    
        
    </div>
    
</div>				
    <page size="A4">
    	<div class="pageinside-invoice" id="poinvoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><!--<img src="<?php echo assets_url;?>logo.png" alt="" height="50">-->PRESTO STANTEST PVT. LTD.</h3>
					<p class="address uppercase">I-42, DLF INDUSTRIAL Area, PHASE-1
<br/>FARIDABAD HARYANA-121003<br/>PHONES: 0129-4272727 / 0129-4083111<br/>Email: purchase@prestogroup.com<br/><strong>GSTN:  06AAACP9778K1ZR</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">CONSOLIDATED PURCHASE ORDER <div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>
											<th>VENDOR.</th>
											<td style="text-align:left">: <?php echo $vname;?></td>
										</tr>
										
									      <tr>
											<th style="text-align:left">ADDRESS</th>
											<td style="text-align:left">:<?php echo $vaddress;?></td>
																						</tr>
											
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
									
										
											<tr>
											<th style="text-align:left">PHONE</th>
											<td style="text-align:left">: <?php echo $vphone;?></td>
																						</tr>
																							<tr>
											<th style="text-align:left">EMAIL</th>
											<td style="text-align:left">: <?php echo $vemail;?></td>
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
							
							        
							        <th style="text-align:center;border-top:1px solid;">PO Date.</th>
							        <th style="text-align:center;border-top:1px solid;">PO No.</th>
									<th style="text-align:center;border-top:1px solid;">Item Name</th>
									
								<th style="text-align:center;border-top:1px solid;">Specification</th>
								<th style="text-align:center;border-top:1px solid;">Size</th>
								<th class="" style="text-align:center;border-top:1px solid;">Already In</th>
								<th class="" style="text-align:center;border-top:1px solid;">Balance Qty</th>
								<th class="" style="text-align:center;border-top:1px solid;">Unit</th>
								
								<th style="text-align:center;border-top:1px solid;">Rate</th>
								
								<th class="hideprice" style="text-align:center;border-top:1px solid;">Total Amt.</th>
								
									
										
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
						    if($data->potype==0)
						    {
						   $otherdetails=$CI->Store_model->getmachineotherdetails($data->mainitemid);
                            if(count($otherdetails)>0)
                            {
                            
                            $fincode=$otherdetails['fincode'];
                            $specification=$otherdetails['specialization'];
                            $itemname=$otherdetails['name'];
                            $size=$otherdetails['size'];
                            $conunit=$otherdetails['conunit'];
                            $conweight=$otherdetails['conweight'];
                            }else{
                            $fincode='';
                            $specification='';
                            $itemname='';
                            $size='';
                            $conunit='';
                            $conweight='';
                            }

						   
						    }else
						    {
						      $itemname=$CI->Store_model->getgeneralitemname($data->mainitemid);  
						      $specification='';
						    }
						    
							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
							if($restyu->num_rows()>0)
							{
								foreach($restyu->result() as $restyu1);
								$unival=strtoupper($restyu1->shortname);
							}else{
							
$unival='';							
							}
						
						


                                           
						
						/** CHECK IF MRN IS DONE FOR SOME QTY **/
						
						$restysum=$this->db->select('sum(recqty) as alreadyqty')->from('mrn')->where('poid',$data->id)->get();	
						if($restysum->num_rows()>0)
						{
						foreach($restysum->result() as $restysum1);
						$already=$restysum1->alreadyqty;
						}
						
						
						$balanceqty=floatval($data->qty)-$already;


						if($conunit>0)
						{
						$finalqty=$conweight*$data->qty;
						$prevqty="(<span style='color:red'>".floatval($data->qty)." ".$unival."</span>)";
						$already=$already*$conweight;
						$balanceqty=$balanceqty*$conweight;
						$restyu=$this->db->select('shortname')->from('units')->where('id',$conunit)->get();
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
										<tr style="text-align:center">
                                        <td><?php echo $i;?></td>
                                        <td><?php echo date('d-m-Y',strtotime($data->approvedOn));?></td>
                                        <td><?php echo $data->pono;?></td>
													
												
                                                        
                                                        
                                           	<td style="text-align:center"><?php echo $itemname;?></td>
                                                        	
                                                    <td style="text-align:center"><?php echo $specification;?></td>
                                                     <td style="text-align:center"><?php echo $size;?></td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
											

													<td><?php echo floatval($already);?></td>														   
													<td><?php echo floatval($balanceqty);?> <br/> <?php echo $prevqty;?></td>
                                                     <td class=""><span style="color:red"><?php echo $unival;?></span></td>  
                                                         
                                                    
 
                                                      
                                                 
                                                       <td class="hideprice"><?php echo round(floatval($data->price),2);?></td>
                                                     
													   <?php
													   $totwithqty=$data->price*$balanceqty;
													   
													   	$cost[]=$totwithqty;
													   ?>
													   <td class="hideprice"><?php echo round(floatval($totwithqty),2);?></td>
													   
												  
                                                    </tr>
													
												<?php
											$i++;
											}
											
										
										
												?>						

							
						</tbody>
					</table>
				
				</div>
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
 window.print();
  

}
	

</script>
	</body>

</html>
