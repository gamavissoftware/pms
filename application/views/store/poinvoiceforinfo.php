<?php
$uri=$this->uri->segment(3);
if($uri=='')
{
	echo "Invalid Access"; exit;
}else
{
				$rest=$this->db->select('a.id as poitemid,a.remarks,a.prno,a.potype,a.approved,d.gst,a.source,a.sourceid,a.jobcard,d.part as machine_part,d.fincode,d.specification,d.hsn,e.first_name,e.last_name,a.itemid,a.qty,e.department_id,a.vendor,a.price,a.addedOn as orderdate,a.unit,a.discount,a.approvedBy')->from('purchase_order a')->join('machine_parts_with_picture d','d.id=a.itemid')->join('system_users e','e.user_id=a.addedBy')->where('a.pono',$this->uri->segment(3))->order_by('d.part','ASC')->get();
				


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
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
 
    
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
													
													$jobcard='Jobcard-'.$job1->job_card_no;
													
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

							<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details </div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
							
								<th style="text-align:center;border-top:1px solid;">Item Code</th>
									<th style="text-align:center;border-top:1px solid;">HSN</th>
								
									<th style="text-align:center;border-top:1px solid;">Item Name</th>
									
								<th style="text-align:center;border-top:1px solid;">Specification</th>
								<th class="" style="text-align:center;border-top:1px solid;">Quantity</th>
								<th class="" style="text-align:center;border-top:1px solid;">Unit</th>
								
								
								<th class="hideprice" style="text-align:center;border-top:1px solid;">Approval Status</th>
								
									<th class="hideprice" style="text-align:center;border-top:1px solid;">Delivery Date</th>
						
																
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
                                                        <td><?php echo $i;?></td>
													
														<td style="text-align:center"><?php echo $data->fincode;?></td>
														
															<td style="text-align:center"><?php echo $data->hsn;?></td>
                                                        
                                                        
                                           	<td style="text-align:center"><?php echo $data->machine_part;?></td>
                                                        	
                                                    <td style="text-align:center"><?php echo $data->specification;?></td>
                                                        
                                                        
                                                          
                                                                                                               <!-- <td class="currstock"><span style="color:red"></span></td>-->
																											   
													<td><?php echo floatval($data->qty);?></td>
                                                     <td class=""><span style="color:red"><?php echo $unival;?></span></td>  
                                                         
                                                    
							<?php
							
							$appdetail=$CI->Store_model->getapprovaldateanddeliverytime($data->poitemid); 
							
							if(count($appdetail)>0)
							{
							?>
                                                 
                                                      <td class="hideprice"><?php echo $appdetail['approvalstatus'];?> <br/><?php echo $appdetail['approvedon'];?></td>
                                                     <td class="hideprice"><?php echo $appdetail['deliverydate'];?></td>
                                                     
                                                      </tr>

							<?php
							}else
							{ ?>
							 <td class="hideprice"></td>
                                                       <td class="hideprice"></td>
                                                      </tr>
							<?php
							}
							$i++;
							}


							?>						

						</tbody>
					</table>
				
				</div>
							
		<div class="col-sm-6">
				    <p style="font-weight:bold;">&nbsp;&nbsp;Raised by: <?php echo ucfirst($data->first_name);?> <?php echo ucfirst($data->last_name);?></p>
				  
					
				
				    <p style="font-weight:bold;">&nbsp;&nbsp;Authorized Signatory:</p>
				    
				    
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

	</body>

</html>
