<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$id=$this->uri->segment(3);
$rest=$this->db->select('a.*,b.itemid,d.name,d.phone,d.address,b.quantity,b.unit,b.expdate,e.first_name,e.last_name')->from('outwardchallan a')->join('outwardchallan_item b','a.id=b.challanid','left')->join('vendors d','a.supplier=d.id','left')->join('system_users e','a.approvedBy=e.user_id')->where('a.id',$id)->get();
	
?>
<!DOCTYPE html>
<html>
<head>
<title>REJECTION CHALLAN</title>
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
    <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT CHALLAN</span>    
        
    </div>
    
</div>	
<?php
if($rest->num_rows()>0)
{
	foreach($rest->result() as $row);
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
								<th colspan="2" style="font-size: 17px;"> GATE PASS - OUTWARD</th>
							</tr>
						</thead>
						
				
								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tbody><tr>
											<th>To: </th>
											<td style="text-align:left"><?php echo $row->name;?></td>
										</tr>
										<tr>
											<th>Address:</th>
											<td style="text-align:left"><?php echo $row->address;?></td>
										</tr>
										<tr>
											<th>Phone:</th>
											<td style="text-align:left"><?php if($row->phone==0){ }else{ echo $row->phone; };?></td>
										</tr>
										
										
									
										
									</tbody></table>
								</td>
								<td class="w50">
									<table class="tablepodetail w120">
										<tbody>
										
										<tr>
											<th style="text-align:left">Date</th>
											<td style="text-align:left">: <?php echo date('d-m-Y',strtotime($row->challandate));?></td>
												
										</tr>
										
										<tr>
											<th style="text-align:left">Challan No.</th>
											<td style="text-align:left">: <?php echo $row->challanno;?></td>
										</tr>
										
											<tr>
											<th style="text-align:left">PO No.</th>
											<td style="text-align:left">: <?php echo $row->pono;?></td>
										</tr>
										</tbody>
										</table>
								</td>
							</tr>
						</tbody>
					</table>

					<table class="table tablenoborder" style="border-top:0;border-bottom:2px solid #333;margin-bottom:0px">
						<tbody>
						
							<tr>
								<td style="font-size: 12px;text-align:left;padding-top:0px;padding-bottom:0px;">
									<p>Please receive Goods accompanied with this as per details provided below for the purpose of <u>REPAIRING</u> and acknowledge the Receipt</p>
							
									
								</td>
								
								<td style="font-size: 16px;text-align:left;padding-top:2px;padding-bottom:0px">
								
								</td>
							
							</tr>
						</tbody>
					</table>
					
					<div class="col-md-12" style="background-color:#FAC296;padding:2px 0px 2px 0px;font-weight:bold;text-align:center;">Item Details <div class="page-title pull-right"></div></div>
						<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
						<thead>
							
							
							<tr rowspan="2">
								<th style="text-align:center;border-top:1px solid;">OUTWARD ENTRY NO</th>
								<th style="text-align:center;border-top:1px solid;">ITEM</th>
								<th style="text-align:center;border-top:1px solid;">ITEM DESCRIPTION</th>
								<th style="text-align:center;border-top:1px solid;">QUANTITY</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">UNIT</th>
								<th class="" style="text-align:center;border-top:1px solid;">RETURNABLE</th>
								<th style="text-align:center;border-top:1px solid;">DATE OF RETURN</th>
								
							</tr>
							
							
						</thead>
						<tbody>
						
								<?php
								$i=1;
									foreach($rest->result() as $row)
									{
									    
									   $podetail=$CI->Store_model->getpoinfo($row->pono);
									   
									   foreach($podetail as $podetails);
									   
										if($podetails->potype==0)
										{
										$itemname=$CI->Store_model->getmachineitemname($row->itemid);
										$otherdetails=$CI->Store_model->getmachineotherdetails($row->itemid);
										if(count($otherdetails)>0)
										{

										$fincode=$otherdetails['fincode'];
										$specification=$otherdetails['specialization'];
										}else{
										$fincode='';
										$specification='';
										}
										}else{
										$itemname=$CI->Store_model->getgeneralitemname($row->itemid);
										$fincode='';
										$specification='';
										}
										
										$unitname=$CI->Store_model->getunit($podetails->unit);
								?>								
									<tr style="text-align:center">
									<td>1</td>
									<td style="text-align:center"><?php echo $itemname;?></td>
									<td style="text-align:center"><?php echo $specification;?></td>
									<td class="currstock"><?php echo $row->quantity;?></td>
									<td class=""><?php echo $unitname;?></td>
									<td>YES</td>
									<td class=""><?php echo date('d-m-Y',strtotime($row->expdate));?></td>

									</tr>
								<?php
									$i++;
									}
									?>									
						</tbody>
					</table>
				
				</div>
			<div class="col-sm-12">
				    <p>&nbsp;&nbsp;<strong>REMARKS: <?php echo $row->remarks;?></strong></p>
    				    
				</div>
			
				<div class="col-sm-6">
				    <p>&nbsp;&nbsp;Dispatched Through: <strong><?php echo $row->dispatchedby;?></strong></p>
    				 <p>&nbsp;&nbsp;Issued By: <strong><?php echo $row->first_name;?> <?php echo $row->last_name;?></strong></p>
				</div>
			
			
				<div class="col-sm-6">
				    <p>&nbsp;&nbsp;AuthorisedBy: </p>
				    <p>&nbsp;&nbsp;Receivers Signature:</p>
				    
				    
				</div>
		
			
			
        </div>
    
		  <!-- Footer -->
                               <!-- End Footer -->


                
    
</div>


	</page>
	<?php
}
?>
	</body>
<script>
    
    function printDiv() 
{

  window.print();

}
    
</script>
</html>