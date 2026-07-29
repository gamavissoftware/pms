<?php
$CI =& get_instance();
$CI->load->model('Store_model');
$id=$this->uri->segment(3);
if($id<>'')
{
	/** Get Machine ID **/
$odata=$this->db->select('c.psize,a.order_id,a.item_id,a.job_card_no,b.order_id as orderno,c.instruments_name,c.model_number,b.added_on,b.internal_order_no,a.mserialno,a.extraserialno,d.fileno')->from('order_instruments a')->join('prestogroup_orders b','a.order_id=b.order_id')->join('order_planning d','a.id=d.jobcard_id')->join('presto_instruments c','a.item_id=c.id')->where('a.id',$id)->get();
if($odata->num_rows()==0)
{
	echo "JOBCARD INVALID OR CORRUPTED";exit;
}

	/** END **/
	
}else
{
	echo "JOBCARD NOT FOUND";exit;
}

/** Get Machine Part **/
$categories=array();
if($odata->num_rows()>0)
{
	foreach($odata->result() as $odata1);
	$machineid=$odata1->item_id;
	$machineparts12=$this->db->select('b.category_id')->from('machine_parts_with_picture b')->join('machine_bom c','b.id=c.partid')->where('c.mid',$machineid)->get();
	if($machineparts12->num_rows()>0)
	{
		foreach($machineparts12->result() as $catuni)
		{
			$categories[]=trim($catuni->category_id);
		}
		
	}
	
	$uniquecat=array_values(array_unique($categories));

}else{  echo "JOBCARD NOT FOUND";exit; }
/** END **/

?>
<!DOCTYPE html>
<html>
<head>
<title>JOBCARD</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url;?>css/invoice/jobcardcss.css">
<style>
table td{
	
	word-break: break-all;
}

</style>
<style type="text/css" media="print">
  @page {  size: A4;
   margin: 5mm 0mm 0mm 0mm; margin-bottom:0mm;}
   

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
					<table style="width:100%">
						<thead>
							<tr>
							    		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
								<td style="text-align:center"><img src="<?php echo companylogo;?><?php echo $LOGO->logo;?>" width="100px">
								
								</td>
								
							</tr>
							
						</thead>
					</table><br/>
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:center;font-weight:bold;font-size:16px;">JOBCARD</td>
							</tr>
							
						</thead>
					</table>
					<br><br>
					<?php
					if($odata->num_rows()>0)
					{
						foreach($odata->result() as $odata1);
						$machinename=$odata1->instruments_name;
						$orderdate=date('d-M-Y h:i:s',strtotime($odata1->added_on));
						$orderno=$odata1->internal_order_no;
						$machineno=date('m-y');
						$psize=$odata1->psize;
						$mserialno=$odata1->mserialno;
						$exserialno=$odata1->extraserialno;
						$jobcardno=$odata1->job_card_no;
						$fileno=$odata1->fileno;
						
	
					}
					?>
					<table style="margin-bottom:0;width:100%;">
						<tbody>
							<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold;width:14%;">TimeStamp</td>
								<td style="border:2px solid #333;padding:2px 4px;width: 32%;"><?php echo date('d-M-Y');?></td>
								<td style="width:50px;padding:2px 4px">&nbsp;</td>
								<td style="width:50px;padding:2px 4px">&nbsp;</td>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold;width:11%;">Plate Size</td>
								<td style="border:2px solid #333;padding:2px 4px;width: 27%;"><?php echo $psize;?> </td>
								<td style="width:100px;padding:2px 4px">&nbsp;</td>
							</tr>
							<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">Date</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo date('d-M-Y H:i:s');?></td>
								<td style="width:50px;padding:2px 4px;"></td>
								<td style="width:50px;padding:2px 4px">&nbsp;</td>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">Factory</td>
								<td style="border:2px solid #333;padding:2px 4px;">I-42, Mathura Rd, Block C, DLF Industrial Area, Sector 32, Faridabad, Haryana 121003</td>
								<td style="width:100px;padding:2px 4px">&nbsp;</td>
							</tr>
							
								<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">JOBCARD NO.</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo $jobcardno;?></td>
								<td colspan="5" style="width:50px;padding:2px 4px">&nbsp;</td>
							</tr>
							<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">ITEM NAME</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo $machinename;?></td>
								<td colspan="5" style="width:50px;padding:2px 4px">&nbsp;</td>
							</tr>
							<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">ORDER NO.:#</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo $orderno;?></td>
								<!--<td style="width:50px;padding:2px 4px;background-color:red;color:#fff">CHECK</td>-->
								<td colspan="4" style="width:50px;padding:2px 4px">&nbsp;</td>
							</tr>
							
							<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">MACHINE NO.:#</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo $mserialno;?><br/><?php echo $exserialno;?></td>
								<!--<td style="width:50px;padding:2px 4px;background-color:red;color:#fff">CHECK</td>-->
								<td colspan="4" style="width:50px;padding:2px 4px">&nbsp;</td>
							</tr>
							
								<tr>
								<td style="border:2px solid #333;padding:2px 4px;text-transform:uppercase;font-weight:bold">FILE NO.:#</td>
								<td style="border:2px solid #333;padding:2px 4px"><?php echo $fileno;?></td>
								<!--<td style="width:50px;padding:2px 4px;background-color:red;color:#fff">CHECK</td>-->
								<td colspan="4" style="width:50px;padding:2px 4px">&nbsp;</td>
							</tr>
							
						</tbody>
					</table>
					<br>
					<?php
					
					if(count($uniquecat)>0)
					{
	
						for($u=0;$u<count($uniquecat);$u++)
						{
							$category= $CI->Store_model->getcategoryname($uniquecat[$u]);
					
					?>
					
					<table class="table tablenoborder" style="border:2px solid #333;margin-bottom:0">
						<tbody>
							
							<?php
							if($u==0)
							{ ?>
						
							<tr>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:70px;">S.No.</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:310px;">Part Name</td>
								<!--<td style="background-color:#a64d79;border-right:1px solid #000;text-transform:uppercase;color:#fff;padding:2px 4px;text-align:center;">Category</td>-->
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:150px;">Description</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;">Material</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;">ROW<br/>BOP</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:150px;">LOCATION</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;">FIN CODE</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;">AS PER<BR/> BOM</td>
								<td style="background-color:#CFE1F5;border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;">ISSUED</td>
								<td style="background-color:#CFE1F5;text-transform:uppercase;padding:2px 4px;text-align:center;">BALANCE <br/>QTY</td>
							</tr>
							<?php
							}
							?>
							
							<tr class="border">
								<td colspan="7" style="text-transform:uppercase;padding:2px 4px;font-weight:bold;border-right:1px solid #000;text-align:center;"><?php echo $category;?> Item</td>
								<td style="text-transform:uppercase;padding:2px 4px;font-weight:bold;border-right:1px solid #000">&nbsp;</td>
								<td colspan="3" style="text-transform:uppercase;padding:2px 4px;font-weight:bold;text-align:center;">Quality</td>
							</tr>
							
							<?php
							$machineparts=$this->db->select('b.id,b.part as machine_part ,b.specification,b.size_in_mm,b.material,b.raw_bop,b.fincode,c.qty,b.location_id,b.category_id,b.unit')->from('machine_parts_with_picture b')->join('machine_bom c','b.id=c.partid')->where('c.mid',$machineid)->where('b.category_id',$uniquecat[$u])->order_by('b.part','ASC')->get();
							if($machineparts->num_rows()>0)
							{
								$i=1;
								foreach($machineparts->result() as $machineparts1)
								{
									
									
									$location= $CI->Store_model->getlocationname($machineparts1->location_id);
									$unit= $CI->Store_model->getunit($machineparts1->unit);
									if($machineparts1->specification<>'')
									{
										$ms=$machineparts1->specification;
										
										}else{
										
										$ms="";
										
									}
									if($machineparts1->size_in_mm<>'')
									{
										$size=$machineparts1->size_in_mm;
										$sep="/";
									}else{
										
										$size="";
										$sep="";
									}
									$specssize=$ms.$sep.$size;
									
									$alreadyissuedqty=$CI->Store_model->checkifanyitemisissued($id,$machineparts1->id,'1','1');
									
									if($alreadyissuedqty>0)
									{
										$balance=floatval($machineparts1->qty)-$alreadyissuedqty;
									}else
									{
										$balance=floatval($machineparts1->qty);
									}
							?>

							
							<tr>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px;"><?php echo $i;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:310px;"><?php echo $machineparts1->machine_part;?></td>
								<!--<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;"><?php //echo $category;?></td>-->
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:150px;"><?php echo $specssize;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $machineparts1->material;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $machineparts1->raw_bop;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:150px;"><?php echo $location;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $machineparts1->fincode;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo floatval($machineparts1->qty);?> <?php echo $unit;?></td>
							<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $alreadyissuedqty;?> <?php echo $unit;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $balance;?> <?php echo $unit;?></td>
							</tr>
							<?php
								$i++;
								}
							}else{
							?>
							
							<td colspan="11" style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;">NO ITEM Available</td>
							
							
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
						}
					}else
					{
					?>
					<table class="table tablenoborder" style="border:2px solid #333;margin-bottom:0">
					<tr>
					<td colspan="11" style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;">NO ITEM Available</td>
					</tr>
					</table>
					<?php
					}
					?>
				
				
				</div>
			</div>
        </div>
    </page>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>
	    function printDiv() 
{

  window.print();

}
	    
	</script>
	</body>
</html>
</html>