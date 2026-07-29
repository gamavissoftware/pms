<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$usertype=$this->uri->segment(3);
$issuedto=$this->uri->segment(4);
$jobcardid=$this->uri->segment(5);
$issuedsession=$this->uri->segment(6);
if($usertype=='' || $issuedto=='')
{
	echo "Invalid Access"; exit;
}else
{
	
	
	$this->db->select('a.*')->from('issuestocktousers a');
	
	/**if($jobcardid<>'')
	{
		$this->db->where('a.jobcardid',$jobcardid);
	}**/
	
    $this->db->where('a.issuesession',$issuedsession);
    $this->db->where('issuedto',$issuedto);
    $this->db->where('usertype',$usertype);
	$Restey=$this->db->get();
	$jobcard='';
	if($Restey->num_rows()>0)
	{	
	foreach($Restey->result() as $Restey1);
	if($Restey1->jobcardid!=0)
	{
	$jobcard=$CI->Store_model->getjobcardno($jobcardid);
	}else{
	$jobcard='';
	}
	}
		
		
		if($usertype==1)
		{
		$issueduser=$this->storemodel->getudata($issuedto);

		}else if($usertype==2)
		{
		$issueduser=$this->storemodel->getnoncrmusername($issuedto);

		}else
		{
		$issueduser='';
		}
		

}

?>
<!DOCTYPE html>
<html>
<head>
<title>ISSUE SLIP</title>
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
<page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
			  				<div class="col-sm-12">
					<h3 style="text-align:center;font-weight: 700;margin:8px 0;margin-bottom: 0px;"><img src="<?php echo assets_url;?>images/logo_mitr.png" alt="" height="50"></h3>
				
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="font-size: 17px;">ISSUE SLIP<div id="potypees"></div></th>
							</tr>
						</thead>
						
						 								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
								<table class="tablepodetail w80" style="text-align:left">
										<tbody><tr></tr>
										<tr>
											
										<th style="text-align:left">Issued To</th>
											<td style="text-align:left">: <?php echo $issueduser;?> </td>
											
										</tr>
										<tr><td></td></tr>
										
										
										
										
									</tbody></table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tbody><tr>
											<th style="text-align:left">JOBCARD</th>
											<td style="text-align:left">:<?php echo $jobcard;?></td>
										</tr>
											
										
										
									</tbody></table>
								</td>
							</tr>
						</tbody>
					</table>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;width:30px;">Sr. No.</th>
								<th style="text-align:center;border-top:1px solid;width:150px;">DATE</th>
								<th style="text-align:center;border-top:1px solid;width:200px;">ITEM</th>
								<th style="text-align:center;border-top:1px solid;width:100px;">FINCODE</th>
								<!--<th style="text-align:center;border-top:1px solid;width:300px;">SPECIFICATION</th>-->
								<th style="text-align:center;border-top:1px solid;width:100px;">ISSUED QTY</th>
															
							</tr>
							
							
						</thead>
						<tbody>
						<?php
						if($Restey->num_rows()>0)
						{	
						$s=1;
						foreach($Restey->result() as $Restey1)
						{
							$machinedetails=$CI->Store_model->getmachinewithotherdetails($Restey1->itemid);
							if(count($machinedetails)>0)
							{
							$fincode=$machinedetails['fincode'];
							$specification=$machinedetails['specialization'];
							$part=$machinedetails['name'];
							$rack_location=$machinedetails['rack_location'];
							$unit=$machinedetails['unit'];
							}else{
							$fincode='';
							$specification='';
							$part='';
							$rack_location='';
							$unit='';
							}

							if($unit<>'')
							{
							$unitname=$CI->Store_model->getunit($unit);
							}else
							{
							$unitname='';
							}
				
						
						?>
						<tr style="text-align:center">
						<td><?php echo $s;?></td>
						<td style="text-align:center"><?php echo date('d-M-Y H:i:s',strtotime($Restey1->issuedOn));?></td>
						<td style="text-align:center"><?php echo $part;?></td>
						<td style="text-align:center"><?php echo $fincode;?></td>
						<!--<td style="text-align:center"><?php echo $specification;?></td>-->
						<td style="text-align:center"><?php echo floatval($Restey1->stock);?> <?php echo $unitname;?></td>
						</tr>
						<?php
						$s++;
						}
						}
						?>
													
																	

						
						
							
							
						</tbody>
					</table>
					
				
				</div>
				
					<div class="col-sm-11">
				    <p style="font-weight:bold;">&nbsp;&nbsp;SIGNATURE </p>
				    <p style="font-weight:bold;">&nbsp;&nbsp;</p>
					
				</div>
				
				
			<div class="col-sm-1"></div>
			<br><br><br><br>
			
			
        </div>
    
		
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
	

</div></page>
	
	
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