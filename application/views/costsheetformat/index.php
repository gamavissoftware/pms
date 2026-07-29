<?php
$CI =& get_instance();
$CI->load->model('Store_model');

$UI =& get_instance();
$UI->load->model('Fms_model','fmsmodel');
$id=$this->uri->segment(3);
if($id=='')
{
echo "JOBCARD NOT FOUND";exit;
}

/** Get Machine Part **/
$categories=array();

	$machineid=$id;
	$insname=$UI->fmsmodel->getinstrumentname($machineid);
	//echo "<pre>"; print_r($insname);exit;
	if(count($insname)>0)
	{
		$mname=$insname['name'];
	}else{
		
		$mname='';
	}
	$machineparts12=$this->db->select('b.category_id')->from('machine_parts_with_picture b')->join('machine_bom c','b.id=c.partid')->where('c.mid',$machineid)->get();
	if($machineparts12->num_rows()>0)
	{
		foreach($machineparts12->result() as $catuni)
		{
			$categories[]=trim($catuni->category_id);
		}
		
	}
	
	$uniquecat=array_values(array_unique($categories));
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
   
    
</div>
    <page size="A4">
    	<div class="pageinside-invoice">
			<div class="row">
				<div class="col-sm-12">
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:center"><img src="<?php echo assets_url;?>docscss/logo.png">
								
								</td>
								
							</tr>
							
						</thead>
					</table><br/>
					<table style="width:100%">
						<thead>
							<tr>
								<td style="text-align:center;font-weight:bold;font-size:13px;">COST SHEET FOR <?php echo strtoupper($mname);?></td>
							</tr>
							
						</thead>
					</table>
					<br><br>
					
					
					<?php
					
					if(count($uniquecat)>0)
					{
					
					
						
							$greatgrandtotal=array();
						$greatgrandtotal[]=0;
						for($u=0;$u<count($uniquecat);$u++)
						{
                            $categorywisetotal=array();
                            $categorywisetotal[]=0;
                            
                            $category= $CI->Store_model->getcategoryname($uniquecat[$u]);
					
					?>
					
					<table class="table tablenoborder" style="border:2px solid #333;margin-bottom:0">
						<tbody>
							
							<?php
							if($u==0)
							{ ?>
						
							<tr>
								<th style="border-right:1px solid #000;background-color:#A3D0EE;text-transform:uppercase;padding:2px 4px;text-align:center;width:70px;">S.No.</th>
								<th style="border-right:1px solid #000;background-color:#A3D0EE;text-transform:uppercase;padding:2px 4px;text-align:center;width:310px;">Part Name</th>
								<!--<td style="background-color:#a64d79;border-right:1px solid #000;text-transform:uppercase;color:#fff;padding:2px 4px;text-align:center;">Category</td>-->
								<th style="border-right:1px solid #000;background-color:#A3D0EE;text-transform:uppercase;padding:2px 4px;text-align:center;width:150px;">Description</th>
								
								<th style="border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;background-color:#A3D0EE;">FIN CODE</th>
								<th style="border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;background-color:#A3D0EE;">AS PER<BR/> BOM</th>
								<th style="border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;background-color:#A3D0EE;">Unit Price</th>
								<th style="text-transform:uppercase;padding:2px 4px;text-align:center;background-color:#A3D0EE;">Total Price <br/>QTY</th>
							</tr>
							<?php
							}
							?>
							
							<tr class="border">
								<td colspan="10" style="text-transform:uppercase;padding:2px 4px;font-weight:bold;border-right:1px solid #000;text-align:center;"><?php echo $category;?> Item</td>
							
								
							</tr>
							
							<?php
							$machineparts=$this->db->select('b.part as machine_part ,b.specification,b.fincode,b.size_in_mm,c.qty,b.category_id,b.unit,b.id')->from('machine_parts_with_picture b')->join('machine_bom c','b.id=c.partid')->where('c.mid',$machineid)->where('b.category_id',$uniquecat[$u])->order_by('b.part','ASC')->get();
							if($machineparts->num_rows()>0)
							{
								$i=1;
								foreach($machineparts->result() as $machineparts1)
								{
									
									
								
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
									
									$price=$UI->fmsmodel->getbomprice($machineparts1->id);
									?>

							
							<tr>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px;"><?php echo $i;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:310px;"><?php echo $machineparts1->machine_part;?></td>
								<!--<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;"><?php //echo $category;?></td>-->
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:150px;"><?php echo $specssize;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $machineparts1->fincode;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo floatval($machineparts1->qty);?> <?php echo $unit;?></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;padding:2px 4px;text-align:center;width:100px;"><?php echo floatval($price);?></td>
								<?php
								$total= floatval($machineparts1->qty)*floatval($price);
								?>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:100px;"><?php echo $total;?></td>
								<?php
								$categorywisetotal[]=$total;
								?>
							</tr>
							<?php
								$i++;
								}
								?>
								
								<tr>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px; background-color:#A3D0EE;" colspan="5"></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px;background-color:#A3D0EE;"><strong>Total</strong></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px;background-color:#A3D0EE;"><strong><?php echo array_sum($categorywisetotal);?></strong></td>
								<?php
								$greatgrandtotal[]=array_sum($categorywisetotal);
								?>
							</tr>
							
							<?php
							}else{
							?>
							
							<td colspan="11" style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;">NO ITEM Available</td>
							
							
							<?php
							}
							?>
						
				<?php
						}
						?>
						
							<tr>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px; background-color:#A7E8C6" colspan="5"></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px; background-color:#A7E8C6"><strong>Grand Total</strong></td>
								<td style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;width:70px; background-color:#A7E8C6"><strong><?php echo array_sum($greatgrandtotal);?></strong></td>
								
							</tr>
							
							</tbody>
					</table>
						<?php
					}else
					{
					?>
					<table class="table tablenoborder" style="border:2px solid #333;margin-bottom:0">
					<tr>
					<td colspan="11" style="border-right:1px solid #000;text-transform:uppercase;color:#000;padding:2px 4px;text-align:center;">BOM NOT Available</td>
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