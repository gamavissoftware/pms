<?php
$uri=$this->uri->segment(3);
if($uri=='')
{
	echo "Invalid Access";
}else
{
				$rest=$this->db->select('a.sourceid,a.source,a.jobcardid,b.machine_part,d.fincode,e.first_name,e.last_name,a.itemid,a.unit,a.qty,e.department_id')->from('purchase_request a')->join('machine_parts_master b','a.itemid=b.part_id')->join('machine_parts_with_picture d','d.part_id=a.itemid')->join('system_users e','e.user_id=a.addedBy')->where('a.prno',$this->uri->segment(3))->group_by('a.itemid')->get();

}
$CI =& get_instance();
$CI->load->model('Store_model');
?>
<!DOCTYPE html>
<html>
<head>
<title>PURCHASE REQUISITION</title>
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
					

				?>
    <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="<?php echo assets_url;?>logo.png" alt="" height="50"></h3>
					<p class="address uppercase">I-42, DLF INDUSTRIAL Area, PHASE-1
<br/>FARIDABAD HARYANA-121003<br/>PHONES: 0129-4272727 / 0129-4083111<!--<strong>GSTN:  07ACLFS3577R1Z2</strong>--></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">PURCHASE REQUISITION <div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tr>
											<th>PR No.</th>
											<td style="text-align:left">: <?php echo $this->uri->segment(3);?></td>
										</tr>
										
									<tr>
											<th style="text-align:left">Category</th>
											<td style="text-align:left">:</td>
																						</tr>
											
											<?php
											$jobcard='';
									
											if($data->source==1)
											{
												$job=$this->db->select('job_card_no')->from('order_instruments')->where('id',$data->jobcardid)->get();
												if($job->num_rows()>0)
												{
													foreach($job->result() as $job1);
													
													$jobcard='Jobcard-'.$job1->job_card_no;
													
												}else{
													
													$jobcard='';
												}
											}else if($data->source==2)
											{
												/** Intend **/
												
												
													
													$jobcard='INDENT- IND'.$data->sourceid;
													
												

											}												
											?>													<tr>
											<th style="text-align:left">Source</th>
											<td style="text-align:left">: <?php echo $jobcard;?></td>
																						</tr>
										
										
									
										
									</table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tr>
											<th style="text-align:left">Indenter Ref.</th>
											<td style="text-align:left">: <?php echo ucfirst($data->first_name);?> <?php echo ucfirst($data->last_name);?></td>
										</tr>
											<tr>
											<th>Department</th>
											<td style="text-align:left">: <?php echo ucfirst($depart);?></td>
										</tr>
										
										
									</table>
								</td>
							</tr>
						</tbody>
					</table>

					<!---<table class="table tablenoborder" style="border-top:0;border-bottom:2px solid #333;margin-bottom:0px">
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
											<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">Sl. No.</th>
								<th style="text-align:center;border-top:1px solid;">Item Code</th>
								<th style="text-align:center;border-top:1px solid;">Item Name</th>
								<th style="text-align:center;border-top:1px solid;">Delivery Date</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">Unit</th>
								<th class="" style="text-align:center;border-top:1px solid;">Quantity</th>
								<th style="text-align:center;border-top:1px solid;">Exp. Rate</th>
								<!--<th class="hideprice" style="text-align:center;border-top:1px solid;">Total Amt.</th>-->
								<th class="hideprice" style="text-align:center;border-top:1px solid;">Remarks</th>
								
													
																
							</tr>
							
							
						</thead>
						<tbody>
						<?php
						$i=1;
						$cost=array();
						foreach($rest->result() as $data)
						{
							$restyu=$this->db->select('shortname')->from('units')->where('id',$data->unit)->get();
							if($restyu->num_rows()>0)
							{
								foreach($restyu->result() as $restyu1);
								$unival=$restyu1->shortname;
							}else{
							
$unival='';							
							}
							?>
													     <tr style="text-align:center">
                                                        <td>1</td>
                                                        <td style="text-align:center"><?php echo $data->machine_part;?></td>
                                                        <td style="text-align:center"><?php echo $data->fincode;?></td>
                                                                                                                <td style="text-align:center"></td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
                                                     <td class="currstock"><span style="color:red"><?php echo $unival;?></span></td>  
                                                         
                                                    
 
                                                      
                                                        <td><?php echo floatval($data->qty);?> <?php echo $unival;?></td>
												<?php
												$cost[]='175.00';
												?>
                                                        <td class="hideprice">175.00</td>
                                                        
                                                        	
																																											                                                                                                                <td  class="hideprice"></td>

											
                                                      
                                                    </tr>
													
												<?php
											$i++;
											}
												?>						

														  
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
    				    <p>&nbsp;&nbsp;Prepared By: <strong><?php echo ucfirst($data->first_name);?> <?php echo ucfirst($data->last_name);?></strong></p>
    				    					    <p>&nbsp;&nbsp;Approved By: <strong></strong></p>
				    <p>&nbsp;&nbsp;</p><br/>
				    <p>&nbsp;&nbsp;Authorised Signatory:</p>
				    
				    
				</div>
				<div class="col-sm-5">
				    <table class="table hideprice" border="1" style="text-align:center;margin-bottom:2px;margin-left:65px;">
				        <thead>
				            <tr>
				                <th style="text-align:center">Grand Total</th>
								<?php
								if(count($cost)>0)
								{
									$tot=array_sum($cost);
}else{
	$tot=0;
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