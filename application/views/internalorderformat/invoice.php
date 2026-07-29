<?php
$rest=$this->db->select('*')->from('prestogroup_orders')->where('order_id',$this->uri->segment(3))->get();
if($rest->num_rows()==0)
{
	echo "INVALID AUTHORIZATION"; exit;
}else
{
	foreach($rest->result() as $rest1);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>INTERNAL ORDER SLIP</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>docscss/iopackinglsip.css">
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
    <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:right"><img src="<?php echo assets_url;?>images/logo_mitr.png"></th>
							</tr>
						</thead>
					</table>
					<br>
					<table class="table tablenoborder" style="border-bottom:2px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th colspan="2" style="background-color:#000;color:#fff">Internal Order</th>
							</tr>
						</thead>
					</table>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;width:16%;"><b>Ref. No.:</b><?php echo $rest1->internal_order_no;?></td>
								<td style="border:1px solid #333;padding:4px 6px;width:18%;"><b>Date:</b> <?php echo date('Y-m-d',strtotime($rest1->added_on));?></td>
								<td style="width:50px;padding:4px 6px">&nbsp;</td>
								<td style="border:1px solid #333;padding:4px 6px"><b>Delivery Schedule:</b> <?php echo date('Y-m-d',strtotime($rest1->delivery_date));?></td>
							</tr>
							<tr>
								<td colspan="2" style="border:1px solid #333;padding:4px 6px">&nbsp;</td>
								<td style="width:50px">&nbsp;</td>
								<td style="border:1px solid #333;padding:4px 6px"><b>Add.:</b> <?php echo strtoupper($rest1->address);?></td>
							</tr>
						</tbody>
					</table>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px;text-transform:uppercase;font-size:18px"><b>Product Details</b></td>
							</tr>
						</tbody>
					</table>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<th style="border:1px solid #333;padding:4px 6px;width:10%;text-align:center;">S.No.</th>
								<th style="border:1px solid #333;padding:4px 6px;width:60%;text-align:center;">Product Name</th>
								<th style="border:1px solid #333;padding:4px 6px;width:15%;text-align:center;">Jobcard No</th>
								<th style="border:1px solid #333;padding:4px 6px;width:15%;text-align:center;">Quantity</th>
							</tr>
							
							<?php 
							$query = $this->db->select('a.qty,b.instruments_name, a.job_card_no')->from('order_instruments a')->join('presto_instruments b','a.item_id=b.id','left')->where('a.order_id',$this->uri->segment(3))->get();
							if($query->num_rows()>0)
							{
								$t=1;
							foreach($query->result() as $instruments){
							?>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;text-align:center;"><?php echo $t;?></td>
								<td style="border:1px solid #333;padding:4px 6px;text-align:center;"><?php echo $instruments->instruments_name;?></td>
								<td style="border:1px solid #333;padding:4px 6px;text-align:center;"><?php echo $instruments->job_card_no;?></td>
								<td style="border:1px solid #333;padding:4px 6px;text-align:center;"><?php echo $instruments->qty;?></td>
							</tr>
							
							<?php
							$t++;
							}
							}
							?>
						</tbody>
					</table>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px"><b>Remarks:</b></td>
							</tr>
						</tbody>
					</table>
					<br>
					<p>Uploaded Document Name</p>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px"><b>Dealing Manager:</b></td>
								<td style="padding:4px 6px"><b>Authorized By:</b></td>
							</tr>
							<tr>
								<td style="border-bottom:2px solid #333;padding:4px 6px">&nbsp;</td>
								<td style="border-bottom:2px solid #333;padding:4px 6px">&nbsp;</td>
							</tr>
						</tbody>
					</table>
					</div>
					
			</div>
			<div class="col-sm-12 text-center printhide" style="margin-top:30px;">
					<span class="btn btn-success" onclick="printDiv();">Print Me</span>
					</div>
        </div>
		
    </page>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>

function printDiv() 
{

  window.print();

}
	
	$( document ).ready(function() {
		
		window.print();
	});
</script>
	</body>

</html>