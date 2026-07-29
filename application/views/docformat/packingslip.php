<!DOCTYPE html>
<html>
<head>
<title>Invoice</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>docscss/style.css">

<style>
p
{
	
	font-weight:bold;color:black;
	
}
</style>
<style type="text/css" media="print">
@page
{
    size: 4in 6in;
}
p 
{
font-size:10pt;
}
.printlogo
    {
        width:30%;
    }
    .printhide
    {
    display:none;
    }
    
    page {
  background: white;
  display: block;
  margin: 0 auto;
  box-shadow: none;
  -webkit-box-shadow: none;
  -moz-box-shadow:none;
  box-sizing:border-box;
  -webkit-box-sizing:border-box;
  -moz-box-sizing:border-box;
  
  position:relative;
  border:none;
}
</style>
<!--<style type="text/css" media="print">

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


</style>-->
</head>

<body>
<?php
$rest=$this->db->select('contact_person,company_name,mobile_number,address,pincode')->from('prestogroup_orders')->where('order_id',$this->uri->segment('3'))->get();
if($rest->num_rows()=='0')
{
	echo "INVALID ACCESS";exit;
}else
{
	foreach($rest->result() as $rest1);
	
	$name=$rest1->contact_person;
	$add=$rest1->address;
	$pincode=$rest1->pincode;
	$phone="";
	$mobile=$rest1->mobile_number;
	
}
?>

<div id="tobeprinted">
    <page>
    	<div class="pageinside-invoice">
			<div class="row">
			
				<div class="col-sm-12">
				<div class="text-center"><img src="https://www.prestogroup.com/images-new/logo-1.png" class="printlogo"></div>
				</div>
				<div class="col-sm-12">
				<div>
				<p>Consignee: <?php echo $name;?></p>
				<p><?php echo $add;?> - <?php echo $pincode;?></p>
				<p></p>
				<p>Ph.- <?php echo $phone;?></p>
				<p>Mobile: <?php echo $mobile;?></p>
				
				</div>
					
					<div>
				<p>Consignor: M/S Presto Stantest Pvt. Ltd.</p>
				<p>I-42A, DLF Industrial Area,</p>
				<p>Phase-1, Delhi Mathura Road,</p>
				<p>Faridabad-121003 Haryana, India</p>
				<p>Ph. No: 0129-4272727</p>
				</div>
				</div>
				
			</div>
        </div>
    </page>
	</div>
	
	<div class="col-md-12" style="margin-top:50px;text-align:center;">
	<span class="btn btn-success printhide" onclick="printDiv();">PRINT PACKING SLIP </span>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo assets_url;?>docscss/printThis.js"></script>
<script>

function printDiv() 
{

  printdataforthis();

}
	
	$( document ).ready(function() {
    window.print();
});
	function printdataforthis()
	{
	
	var css="<?php echo assets_url;?>docscss/style.css";
	var printcss="<?php echo assets_url;?>docscss/print.css";
		var prtContent = document.getElementById("tobeprinted");
var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
WinPrint.document.write('<html><head>');
WinPrint.document.write('<link rel="stylesheet" href="'+css+'">');
//WinPrint.document.write('<link rel="stylesheet" href="'+printcss+'">');
WinPrint.document.write('</head><body>');
WinPrint.document.write(prtContent.innerHTML);
WinPrint.document.write('</body></html>');
WinPrint.document.close();
WinPrint.focus();
WinPrint.print();
WinPrint.close();
//	window.print();
	}
</script>
	</body>
</html>