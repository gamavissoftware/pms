<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$id=$this->uri->segment(3);
$rest=$this->db->select('a.*,f.first_name,f.last_name,g.first_name as fname,g.last_name as lname')->from('outwardchallan a')->join('system_users f','a.addedBy=f.user_id','left')->join('system_users g','a.approvedBy=f.user_id','left')->where('a.id',$id)->group_by('a.id')->get(); 
	
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo sitetitle; ?> REJECTION CHALLAN</title>
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
					<p class="address uppercase">I-42, DLF INDUSTRIAL AREA, PHASE-1
<br/>FARIDABAD HARYANA-121003<br/>PHONES: 0129-4272727 / 0129-4083111<br/><strong>GSTN:  07ACLFS3577R1Z2</strong></p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="color:white;font-size: 17px;color:black;">RETURNABLE GATE PASS </th>
							</tr>
						</thead>
						
				
								
						<tbody>
							<tr>
								<td class="w50" style="border-right:1px solid #333">
									<table class="tablepodetail w80" style="text-align:left">
										<tbody><tr>
											<th>VENDOR: </th>
											<td style="text-align:left">PRESTO STANTEST PVT. LTD.</td>
										</tr>
										<tr>
											<th>Address:</th>
											<td style="text-align:left">I-10A, PHASE -1 <BR/>DLF INDUSTRIAL AREA, PHASE-1<BR/>HARYANA 121003</td>
										</tr>
										<tr>
											<th>Phone:</th>
											<td style="text-align:left">PHONES: 0129-4272727 / 0129-4083111</td>
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
								<th style="text-align:center;border-top:1px solid;">ENTRY NO</th>
								<th style="text-align:center;border-top:1px solid;">DATE OF RETURN</th>
								<th style="text-align:center;border-top:1px solid;">ITEM</th>
							
								<th style="text-align:center;border-top:1px solid;">QUANTITY</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">UNIT</th>
										<th class="currstock" style="text-align:center;border-top:1px solid;">WEIGHT</th>
								<th class="currstock" style="text-align:center;border-top:1px solid;">PRICE</th>
						
								<th class="currstock" style="text-align:center;border-top:1px solid;">TOTAL</th>
							
								
								
							</tr>
							
							
						</thead>
						<tbody>
						
								<?php
								$i=1;
							
									foreach($rest->result() as $row);
									
								$rrestyu=$this->db->select('*')->from('outwardchallan_item')->where('challanid',$row->id)->get();
								if($rrestyu->num_rows()>0)
								{
									foreach($rrestyu->result() as $itemdetail)
									{
											if($itemdetail->type=='1' || $itemdetail->type=='2')
											{
											$ins=$CI->Store_model->getmachinename($itemdetail->itemid);
											}else{
											$ins=$CI->Store_model->getbom($itemdetail->itemid);
											}
										
								?>								
									<tr style="text-align:center">
									<td><?php echo $i;?></td>
									<td class=""><?php echo date('d-m-Y',strtotime($itemdetail->expdate));?></td>
									<td style="text-align:center"><?php echo $ins;?></td>
									
									<td style="text-align:center"><?php echo $itemdetail->quantity;?></td>
									<td class="currstock"><?php echo $itemdetail->unit;?></td>
									<td><?php echo $itemdetail->weight;?></td>
									<td class=""><?php echo $itemdetail->price;?></td>
									<td class=""><?php echo $itemdetail->quantity*$itemdetail->price;?></td>
									
									

									</tr>
								<?php
									$i++;
									}
								}
									?>									
						</tbody>
					</table>
				
				</div>
			<div class="col-sm-12">
				    <p>&nbsp;&nbsp;<strong>REMARKS: <?php echo $row->remarks;?></strong></p>
    				    
				</div>
			
				<div class="col-sm-6">
				    <p>&nbsp;&nbsp;Dispatched Through: <strong><?php echo $row->fname." ".$row->lname;?></strong></p>
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
