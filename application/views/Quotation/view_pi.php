<!DOCTYPE html>
<html>
<head>
<title>VIEW PI REPORT</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link href="<?php echo assets_url;?>fonts/calibri.ttf" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/sampletest.css">
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

table, th, td {
  border: 1px solid grey !important;
  border-collapse: collapse;
}


</style>

</head>

<body>
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">
    <div class="row text-center">
    <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT</span>  
        
    </div>
    
</div>
    <page size="A4" id="printthis">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
				<?php 
				$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','4')->get();
				foreach($q->result() as $headerimg);?>
					<table style="width:100%; color:grey">
						<thead>
							<tr>
				<td style="width:100%"><img src="<?php echo calibration_image;?><?php echo $headerimg->script;?>" width="100%"></td>							
							</tr>
						</thead>
					</table>
					<?PHP 
					$q = $this->db->select('*')->from('quotation_request')->where('id',$this->uri->segment(3))->get();
					foreach($q->result() as $row);
					?>
					<table class="table" style="border:1px solid grey;margin-bottom:0 !important;">
						<thead>
							<tr class="border">
								<th style="width:70%;text-align:left;text-transform:uppercase;padding:4px 6px">PROFORMA INVOICE</th>
								<td style="text-align:right;">DATE:  <?php echo date('d-m-Y',strtotime($row->quotation_date));?></td>
							</tr>
							
							
						</thead>
					</table>
					<table class="table tablenoborder" style="border:1px solid grey;">
						<thead>
							<tr class="border">
								<td style="padding:4px 6px; width: 26.5cm;;">
								<?php echo $row->company_name;?><br/>
								Kind Attention : <?php echo $row->customer_name;?><br>
									<?php echo $row->contact_number;?><br/>
									<?php echo $row->email;?><br/>
									<?php echo $row->address;?>
								</td>
								
								<td style="padding:4px 6px; width:7cm; text-align:right;">
									Reference No. <br/><?php echo $row->reference_number;?>
								</td>
							</tr>
						</thead>
					</table>
					<table style="margin-bottom:0;width:100%;">
					
						<tbody>
							<tr>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">SR NO.</td>
								<td style="width:15cm;border:1px solid grey;padding:4px 6px; text-align:center;">PRODUCT DETAIL</td>
								<td style="width:5cm;border:1px solid grey;padding:4px 6px; text-align:center;">PRODUCT</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">QTY</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">RATE</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">AMOUNT</td>
								
							</tr>
							<?php 
							$i=1;
							$q = $this->db->select('a.quotation_id, a.product_quantity, b.instruments_name,b.mvalue,b.model_number, a.product_quantity, b.image, a.discount, a.discount_type')->from('quotation_product_details a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.quotation_id',$this->uri->segment(3))->get();
							foreach($q->result() as $prdrows){
							?>
							<tr>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php echo $i;?></td>
								<td style="width:15cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php echo strtoupper($prdrows->instruments_name);?> <?php echo strtoupper($prdrows->model_number);?></td>
								<td style="width:5cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php if($prdrows->image==''){}else{?><img src="<?php echo instrumentimg;?><?php echo $prdrows->image;?>" width="140px"><?php }?></td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php echo $prdrows->product_quantity;?></td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php echo $prdrows->product_quantity*$prdrows->mvalue;
								$total[] = $prdrows->product_quantity*$prdrows->mvalue;
								?></td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								$prdprice = $prdrows->product_quantity*$prdrows->mvalue;
								$discountprice= $prdprice;
								if($prdrows->discount_type!==''){
									
									if($prdrows->discount_type=='0'){
										$discount= $prdrows->discount;
										$disprice = $prdprice*$discount/100;
										$discountprice = $prdprice-$disprice;
									}else{
										$discount= $prdrows->discount;
										$discountprice = $prdprice-$discount;
									}
								}
								
							$discounttotal[] = $discountprice;
							echo $discountprice;
							
							?></td>
							</tr>
							<?php $i++;}?>
							
							<?php if($row->state_id=='13'){?>
							<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">CGST</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								$discounttotal =  array_sum($discounttotal);
								$producttotal = array_sum($total);
								$final = $discounttotal;
								$cgstamount = $final*0.09;
								echo $cgstamount;
								
								?></td>
							</tr>
							<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">SGST</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								$sgstamount = $final*0.09;
								echo $sgstamount;
								
								$finalgst = $sgstamount;
								
								?></td>
							</tr>
							
							<?php }else{?>
							<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">IGST</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php $discounttotal =  array_sum($discounttotal);
								$producttotal = array_sum($total);
								$final = $discounttotal;
								$gstamount = $final*0.18;
								echo $gstamount;								
								?></td>
							</tr>
							
							<?php }?>
							<!--<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">GST TOTAL</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								
								//echo $sgstamount+$cgstamount;
								$gstfinaltotal  = $sgstamount+$cgstamount;
								
								?></td>
							</tr>-->
							<?php 
								
								
								$gstfinaltotal  = $sgstamount+$cgstamount;
								$prdprice = $discountprice;
								$finaltotal = $prdprice;
								
								?>
								<?php 
							if($row->packingcharges_type=='1'){
								$type = "(%)";
								$packingcharges = $finaltotal*$row->packing_charges/100;
							}else if($row->packingcharges_type=='1'){
								$packingcharges=$row->packing_charges;
								$type = "";
							}else{
							$packingcharges="0";	
							$type = "";
							}
							?>
							<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">@ Packing Charges <?php echo $type?></td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								echo $packingcharges;
								?></td>
							</tr>
							<tr>
								<td colspan="3" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"></td>
								<td colspan="2" style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;">Grand Total</td>
								<td style="width:2cm;border:1px solid grey;padding:4px 6px; text-align:center;"><?php 
								
								 $finaltotal=$gstfinaltotal+$discountprice+$packingcharges;
								 echo $finaltotal;
								?></td>
							</tr>
							
							
							
							<tr>
						
								<td colspan="6" style="width:7cm;border:1px solid grey;padding:4px 6px;">
									<p style="font-weight:bold;">TERMS & CONDITIONS</p>
									<?php if($row->terms_conditions==''){?>
									<ul><?php 
									$k=1;
									$q = $this->db->select('title, description')->from('terms_and_conditions_master')->where('status','1')->where('term_conditions_for','1')->get();
									foreach($q->result() as $termconditions){
									?>
										<li><?php echo $termconditions->title;?> : <?php echo $termconditions->description;?></li>
									<?php $k++;}?>
									</ul>
									<?php }else{?>
									<?php echo $row->terms_conditions;?>
									<?php }?>
									<hr>
								<span style="font-size:14px; font-weight:bold;">Bank Detail:</span>
								<p>A/C 184205001070<br/>
NAME- TESTRONIX INSTRUMENTS <br/>
IFSC CODE- ICIC0001842 <br/>
BRANCH ADDRESS- CHARMWOOD FARIDABAD</p>
<span style="font-size:14px; font-weight:bold;">GST Detail:</span>
<p>GSTIN- 06AAQFT9879C1ZG<br>
FOR TESTRONIX INSTRUMENTS</p>
<img src="https://prestomitr.com/assets/checkerssign.png" width="20%">

								
								</td>
							</tr>
							</tbody>
							</table>
					
				
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							
							<tr>
								<td style="border-bottom:2px solid grey;padding:4px 6px"><img src="" width="170px"></td>
								<td style="padding:4px 6px;text-align:right"><img src="" width="70"></td>
								
							</tr>
							<?php 
				$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','5')->get();
				foreach($q->result() as $footer);?>
						
							<tr>
								<td style="width:100%"><img src="<?php echo calibration_image;?><?php echo $footer->script;?>" width="100%"></td>
							</tr>
							
								</tr>
							
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </page>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		 <script type="text/javascript" src="<?php echo assets_url;?>js/html2canvas.js"></script>
<script>
	    function printDiv() 
{

  window.print();

}
</script>

<script>

function getscreenshot(userid,salesid){

var sampletestid="<?php echo $sampledatas->sampletestid;?>";
var sampleid="<?php echo $samplereqid;?>";
html2canvas(document.getElementById(userid)).then(function(canvas) {
var base64URL = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');


// AJAX request
$.ajax({
url: '<?php echo page_url;?>Sampletesting/sendonwhatsapp',
type: 'post',
data: {image: base64URL,userids:salesid,samplefilename:sampletestid,sampleid:sampleid},
success: function(data){
	alert(data);
}
});
});


}


</script>
<?php //if($sendmail<>'')
//{
?>
<script>
$( document ).ready(function() {
    getscreenshot('printthis'<?php //echo $salesid;?>');
});
</script>
<?php
//}
?>


	</body>
</html>
