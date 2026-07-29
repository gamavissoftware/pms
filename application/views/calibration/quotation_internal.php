<?php
$CI =& get_instance();
$CI->load->model('Calibration_model');

$extradata=$CI->Calibration_model->getheadfootimage();
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo sitetitle; ?> CALIBRATION QUOTATION REPORT</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
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


</style>

</head>

<body>
	<?php
	$q = $this->db->select('*')->from('calibrationquotehistory')->where('id',$this->uri->segment(3))->get();
	foreach($q->result() as $row);

	if($row->type=='1')
	{
	$t="AMC";
	}else if($row->type=='2')
	{
	$t="CALIBRATION";
	}else{

	$t="VISIT CHARGES";
	}
	$quoteid = $this->uri->segment(3);

	if($row->type==1)
	{
	$a="AMC";

	}else if($row->type==2)
	{
	$a="CALIBRATION";
	}else
	{

	$a="VISIT";
	}
	?>
<div class="container printhide" style="margin-top:20px;margin-bottom:20px">


 <div class="row text-center" style="margin-bottom:50px">
   <?php echo $this->session->flashdata('message');?>
        
    </div>
    
  
    <div class="row text-center">
      <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT</span>
    <!-- <a href="<?php echo page_url;?>Calibration/Sendmailforpi/<?php echo $row->id;?>"><span class="btn btn-success" style="text-align:center">SEND MAIL</span></a> -->
    <!-- <a href="#myModal" data-toggle="modal" data-target="#myModal"><span class="btn btn-success" style="text-align:center">SEND MAIL</span></a> -->
        
    </div>
    
</div>

    <page size="A4" id="printthis">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
				
					<table style="width:100%;margin-bottom:20px;">
						<thead>
							<tr>
							<td style="text-align:center"><img src="<?php echo calibration_image;?><?php echo $extradata[1];?>" style="width:100%"></td>
								<!--<td style="text-align:center"><img src="<?php echo assets_url;?>logo.png" width="200"></td>-->
							</tr>
						</thead>
					</table>
					
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th style="width:70%;text-align:left;text-transform:uppercase;padding:4px 6px"><?php echo $a;?> CHARGES QUOTES</th>
								<td>REF. NO. - <?php echo $row->pino;?> <br/>DATE: <?php echo date('d-m-Y',strtotime($row->addedOn));?> </td>
								
							</tr>
							
							
						</thead>
					</table>
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<td style="border:1px solid #333;padding:4px 6px;"><?PHP echo $row->company;?><BR/><?php echo $row->address;?><br/><?php echo $row->email;?><br/><?php echo $row->contact_number;?>
								</td>
								<td style="border:1px solid #333;padding:4px 6px;width:33%">Kind Attn.<br/>
<?PHP ECHO $row->person_name;?>								
								</td>
								
							</tr>
						</thead>
					</table>
					<?php
					$data=$CI->Calibration_model->getmachinedetails_internal($this->uri->segment(3),$row->type,$row->state);
					
					?>
					
					
					<?php echo $data;?>
					
					
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px;width:200px">Regards,<br/>
								Team Service<br/>
								service@prestogroup.com<br/>
								+91-129-427-2727<br/>
								
								
								</td>								
								
							</tr>
							
							
						
							
							
								</tr>
								
								
								<tfoot>
								
								
								</tfoot>
							
						</tbody>
					</table>
					
					
		
					
					<table style="width:100%;margin-top:20px;">
					<tr>
							<td style="text-align:center"><img src="<?php echo calibration_image;?><?php echo $extradata[2];?>" style="width:100%"></td>
								<!--<td style="text-align:center"><img src="<?php echo assets_url;?>logo.png" width="200"></td>-->
							</tr>
							
							</table>
				</div>
			</div>
        </div>
  </page>

    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
        	<form action="<?php echo page_url;?>Calibration/Sendmailforpi/<?php echo $row->id;?>" method="post">
        	
        	<div class="form-group">
				<label for="field-1" class="control-label">To</label>
				<span id="error_order_type" style="color:red;">*</span>
				<input type="text" name="to_email" id="to_email" class="form-control" value="<?php echo $row->email;?>" readonly>
			</div>
			<div class="form-group">
				<label for="field-1" class="control-label">Additional To</label>
				<span id="error_order_type" style="color:red;">*(Separated by comma)</span>
				<input type="text" name="additional_to" id="additional_to" class="form-control">
			</div>
			<div class="form-group">
				<label for="field-1" class="control-label">CC</label>
				<span id="error_order_type" style="color:red;">*(Separated by comma)</span>
				<input type="text" name="cc_email" id="cc_email" class="form-control">
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-success" value="Send Mail">
			</div>
		</form>
          <!-- <p>Some text in the modal.</p> -->
        </div>
      <!--   <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div> -->
      </div>
      
    </div>
  </div>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
	<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		 <script type="text/javascript" src="<?php echo assets_url;?>js/html2canvas.js"></script>
<script>
	    function printDiv() 
{

  window.print();

}
</script>

<script>

function getscreenshot(iusere){
var sampletestid="<?php echo $row->pino;?>";
html2canvas(document.getElementById(iusere),{scale:4,letterRendering: 1, allowTaint : true, onrendered : function(canvas) {
var base64URL = canvas.toDataURL('image/jpg').replace('image/jpg', 'image/octet-stream');


// AJAX request
$.ajax({
url: '<?php echo page_url;?>Calibration/savequotationimage',
type: 'post',
data: {image: base64URL,sampleid:sampletestid},
success: function(data){

}
});
}
});


}


            function screenshot(){
            
               var scaleBy = 5;
    var w = 1000;
    var h = 1000;
    var div = document.querySelector('printthis');
    var canvas = document.createElement('canvas');
    canvas.width = w * scaleBy;
    canvas.height = h * scaleBy;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    var context = canvas.getContext('2d');
    context.scale(scaleBy, scaleBy);
    
    
    html2canvas(div, {
        canvas:canvas,
        onrendered: function (canvas) {
            theCanvas = canvas;
            document.body.appendChild(canvas);

            Canvas2Image.saveAsPNG(canvas);
            $(body).append(canvas);
var base64URL = canvas.toDataURL('image/jpg').replace('image/jpg', 'image/octet-stream');


// AJAX request
$.ajax({
url: '<?php echo page_url;?>Calibration/savequotationimage',
type: 'post',
data: {image: base64URL,sampleid:sampletestid},
success: function(data){

}
});
}
});
        }
    });
    
            }
   
	
</script>
<?php //if($sendmail<>'')
//{
?>
<script>
$( document ).ready(function() {

//screenshot();
    getscreenshot('printthis');
});

</script>
<?php
//}
?>


	</body>
</html>
