<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$samplereqid=$this->uri->segment(3);
if($samplereqid=='')
{
	echo "INVALID ACCESS TO THE PAGE";
}ELSE
{
	
	$sampledata=$this->db->select('a.*,b.instruments_name,c.addedOn as resultdate,c.*')->from('sampletestrequest a')->join('presto_instruments b','a.instrument=b.id')->join('sampletestresults c','a.id=c.samplereqid')->where('a.id',$samplereqid)->get();
	if($sampledata->num_rows()==0)
	{
		
		echo "REQUEST NOT FOUND";exit;
	}else
	{
		foreach($sampledata->result() as $sampledatas);
	}
	
	
	$day=date('d',strtotime($sampledatas->resultdate));
	$month=date('m',strtotime($sampledatas->resultdate));
	$year=date('Y',strtotime($sampledatas->resultdate));
	$times=date('g:i A',strtotime($sampledatas->resultdate));
	
	$op=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$sampledatas->addedBY)->get();
	if($op->num_rows()>0)
	{
		foreach($op->result() as $op1);
		$opname=strtoupper($op1->first_name." ".$op1->last_name);
		
	}else{
		
		$opname='';
	}
}


?>
<!DOCTYPE html>
<html>
<head>
<title>SAMPLE TEST REPORT</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link href="<?php echo assets_url;?>fonts/calibri.ttf" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/sampletest.css">

</head>

<body>
    <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
				
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:center"><img src="<?php echo assets_url;?>logo.png" width="200"></td>
							</tr>
						</thead>
					</table>
					
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<th style="width:50%;text-align:left;text-transform:uppercase;padding:4px 6px">SAMPLE TEST REPORT</th>
								<td>DATE: <?php echo $day;?> /  <?php echo $month;?> / <?php echo $year;?> </td>
								<td>TIME: <?php echo $times;?></td>
							</tr>
							<tr>
								<td colspan="3" style="border:1px solid #333;padding:4px 6px">MACHINE NAME: <?php echo strtoupper($sampledatas->instruments_name);?></td>
							</tr>
							<tr>
								<td colspan="3" style="border:1px solid #333;padding:4px 6px">COMPANY NAME: <?php echo strtoupper($sampledatas->companyname);?></td>
							</tr>
						</thead>
					</table>
					<table class="table tablenoborder" style="border:1px solid #333;margin-bottom:0">
						<thead>
							<tr class="border">
								<td style="border:1px solid #333;padding:4px 6px;">Address:<br/> <?php echo strtoupper($sampledatas->address);?></td>
								<td style="border:1px solid #333;padding:4px 6px;width:33%">Video:<br/> <a href="<?php echo $sampledatas->testvideo;?>"target="_blank" ><?php echo $sampledatas->testvideo;?></a></td>
								<td style="border:1px solid #333;padding:4px 6px;width:33%;text-align:center;">
								    <?php
								    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/sampletesting/'.$sampledatas->testimage)) {
								    ?>
								    <img src="<?php echo page_url;?>upload/sampletesting/<?php echo $sampledatas->testimage;?>" style="width:40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								    <?php
								    }
								    ?>
								    
								     <?php
								    if($sampledatas->testimage1<>'')
								    {
								    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/sampletesting/'.$sampledatas->testimage1)) {
								    ?>
								    <img src="<?php echo page_url;?>upload/sampletesting/<?php echo $sampledatas->testimage1;?>" style="width:40%">
								    <?php
								    }
								    }
								    ?>
								    
								    </td>
							</tr>
						</thead>
					</table>
					<!---<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;width:114px;">SAMPLE ID</td>
								<td style="border:1px solid #333;padding:4px 6px;width:262px;"><?php echo strtoupper($sampledatas->sampletestid);?></td>
								<td style="border:1px solid #333;padding:4px 6px;width: 252px;">OPERATOR NAME</td>
								<td style="border:1px solid #333;padding:4px 6px;width:127px;"><?php echo $opname;?></td>
							</tr>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;">LOT NO.</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->lotno);?></td>
								<td style="border:1px solid #333;padding:4px 6px;">SAMPLE LENGTH</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->samplelength);?></td>
							</tr>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;">BATCH NO.</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->batchno);?></td>
								<td style="	border:1px solid #333;padding:4px 6px;">SAMPLE WIDTH</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->samplewidth);?></td>
							</tr>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;">PRODUCT NAME</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->productname);?></td>
								<td style="border:1px solid #333;padding:4px 6px;">SAMPLE THICKNESS</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->thickness);?></td>
							</tr>
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;">TEST SPEED</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->testspeed);?></td>
								<td style="border:1px solid #333;padding:4px 6px;">SHAPE/GSM</td>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->shapegsm);?></td>
							</tr>
						</tbody>
					</table>
					-->
					
					
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;">SAMPLE ID</td>
								<td colspan="2" style="width:6cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->sampletestid);?></td>
								<td colspan="2" style="width:7cm;border:1px solid #333;padding:4px 6px;">OPERATOR NAME</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;"><?php echo $opname;?></td>
							</tr>
							<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;">LOT NO.</td>
								<td colspan="2" style="width:6cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->lotno);?></td>
								<td colspan="2" style="width:7cm;border:1px solid #333;padding:4px 6px;">SAMPLE LENGTH</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->samplelength);?></td>
							</tr>
							<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;">BATCH NO.</td>
								<td colspan="2" style="width:6cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->batchno);?></td>
								<td colspan="2" style="width:7cm;border:1px solid #333;padding:4px 6px;">SAMPLE WIDTH</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->samplewidth);?></td>
							</tr>
							<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;">PRODUCT NAME</td>
								<td colspan="2" style="width:6cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->productname);?></td>
								<td colspan="2" style="width:7cm;border:1px solid #333;padding:4px 6px;">SAMPLE THICKNESS</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->thickness);?></td>
							</tr>
							
								<tr>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;">TEST SPEED</td>
								<td colspan="2" style="width:6cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->testspeed);?></td>
								<td colspan="2" style="width:7cm;border:1px solid #333;padding:4px 6px;">SHAPE/GSM</td>
								<td style="width:4cm;border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sampledatas->shapegsm);?></td>
							</tr>
							
							<tr>
								<th style="border:1px solid #333;padding:4px 6px;background-color:#efefef;">SAMPLE</th>
								<?php
								$rowcount=array();
								$sample=$this->db->select('id,sample')->from('sampletobetested')->where('samplereqid',$samplereqid)->get();
								foreach($sample->result() as $sample1)
								{
								$testtime=$this->db->select('count(id) as rowtimes')->from('sampletobetestedresults')->where('samplereqid',$samplereqid)->where('sampleid',$sample1->id)->get();
								foreach($testtime->result() as $testtime1);
								$rowcount[]=$testtime1->rowtimes;
								}
								
								$maxrow=max($rowcount);
								
								
								$maxrow="5";
								$widthtah='85/'.$maxrow;
							for($i=0;$i<$maxrow;$i++)
							{
								if($i==0)
								{
									$w="200px";
								}else if($i==1)
								{
									$w="224px";
								}else if($i==4)
								{
									$w="203px";
								}else
								{
									$w="200px";
								}
							
								?>

								<th style="border:1px solid #333;padding:4px 6px;background-color:#efefef">NO.<?php echo $i+1;?></th>
								<?php
							}
							?>
							
							</tr>
							
							<?php
								
								foreach($sample->result() as $sample1)
								{
									
								?>
								
							<tr>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($sample1->sample);?></td>
								
							<?php
								for($i=0;$i<$maxrow;$i++)
								{
									$kl=$i;
								$rt=$this->db->select('resultdata,sampleunit')->from('sampletobetestedresults')->where('sampleid',$sample1->id)->where('samplereqid',$samplereqid)->limit(1,$kl)->get();
								if($rt->num_rows()>0)
								{
									foreach($rt->result() as $rta);
									
									$rsultdata=$rta->resultdata;
									$unit= $CI->Store_model->getunit($rta->sampleunit);
									
								}else{
									
									$rsultdata='--';
									$unit	='';
								}
								
								?>
								<td style="border:1px solid #333;padding:4px 6px;"><?php echo strtoupper($rsultdata);?>&nbsp;<?php echo $unit;?></td>
								<?php
								
								
								}
								?>
								
								
							</tr>
							<?php
								}
								?>
							
							
							
							<tr style="display:none">
								<td style="border:1px solid #333;padding:4px 6px;">RESULT PASS/FAIL</td>
								<?php
							for($i=0;$i<$maxrow;$i++)
							{
							?>
								<td style="border:1px solid #333;padding:4px 6px;"></td>
								<?php
							}
							?>
								
								
							</tr>
							
								<tr>
								<td colspan="<?php echo $maxrow+1;?>" style="border:1px solid #333;padding:4px 6px;">Testing Instructions : <?php echo $sampledatas->instruction;?></td>
							</tr>
							
							<tr>
								<td colspan="<?php echo $maxrow+1;?>" style="border:1px solid #333;padding:4px 6px;">QC Remarks : <?php echo $sampledatas->remarks;?></td>
							</tr>
							
								<tr>
								<td colspan="<?php echo $maxrow+1;?>" style="border:1px solid #333;padding:4px 6px;font-size:10px;font-style:italics;">Disclaimer : <i><?php echo $sampledatas->disclaimer;?></i></td>
							</tr>
							
						</tbody>
					</table>
					<br>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="padding:4px 6px;width:200px"><b>CHECKED BY:</b></td>								
								<td style="padding:4px 6px;text-align:right"><b>www.prestogroup.com</b></td>
							</tr>
							<tr>
								<td style="border-bottom:2px solid #333;padding:4px 6px">&nbsp;</td>
								<td style="padding:4px 6px;text-align:right"><img src="<?php echo assets_url;?>qualitystamp.jpg" width="70"></td>
								
							</tr>
							
						
							<tr>
								<td style="padding:4px 6px"><b>info@prestogroup.com</b></td>
								<td style="padding:4px 6px;text-align:right">&nbsp;</td>
								
							</tr>
							
								</tr>
							
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </page>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	</body>
</html>