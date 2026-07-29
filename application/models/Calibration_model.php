<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Calibration_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}
	

	function getmachinedetails_internal($quoteid,$rowtype,$state) {
		
		$html='';
	$Restyuu=$this->db->select('machineid,amc,visit,total')->from('calibrationmachinehistory')->where('quoteid',$quoteid)->get();
	
	$ast='border-right:1px solid #000;';
		
		if($Restyuu->num_rows()>0)
		{
		    	 if($rowtype=='1')
		    {
		    

			$html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">AMC Charge</th>
			</tr>
			</thead>
			<tbody>';
		    }else if($rowtype=='2')
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Calibration Charge</th>
			</tr> 
			</thead>
			<tbody>';
		        
		    }else
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Price</th>
			</tr>
			</thead>
			<tbody>';
		        
		    }

	
				$totalarr=array();
				$totalarr[]=0;
				$amcarr=array();
				$amcarr[]=0;
				$j=1;			
				foreach($Restyuu->result() as $Restyuu11)
				{
			    	$insname=$this->getinstrumentname($Restyuu11->machineid);
			    if($rowtype=='1')
			    {
			
				$html.="<tr>
				<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;'>".$insname."</td>
				<td style='text-align:left;".$ast."'>".floatval($Restyuu11->amc)."</td>
				</tr>";
				$amcarr[]=$Restyuu11->amc;
			    }else if($rowtype=='2')
			    {
			       	$html.="<tr>
			       	<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;'>".$insname."</td>
				<td style='text-align:left;".$ast."'>".floatval($Restyuu11->amc)."</td>
				
				</tr>"; 
			       $amcarr[]=$Restyuu11->amc;
			        
			    }else
			    {
			        
			        	$html.="<tr>
			        	<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;".$ast."'>".$insname."</td>
				<td style='text-align:left;".$ast."'></td>
				
				</tr>";
			        
			    }
				
				$totalarr[]=$Restyuu11->total;
				
			$j++;
			}
			
			
		 if($rowtype=='1')
			    {
					
               $html.= "<tr>
               <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total AMC Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
	 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Visit Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($tot)."</td>
		</tr>";
		$cgst = (floatval($tot)*9)/100;
		$sgst = (floatval($tot)*9)/100;
		$igst = (floatval($tot)*18)/100;
		if ($state == 11) {
		$grandTotal = floatval($tot) + floatval($cgst) + floatval($sgst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;;font-size:10px;'>Add CGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
		</tr>";
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
		</tr>";
		} else {
		$grandTotal = floatval($tot) + floatval($igst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges (18%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
		</tr>";	
		}

		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
		</tr>";
		
		$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";
		
			    }else if($rowtype=='2')
			    {
                   		
               $html.= "<tr>
                <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total Calibration Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
	 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Visit Cost Charges</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($tot)."</td>
		</tr>";
		$cgst = (floatval($tot)*9)/100;
		$sgst = (floatval($tot)*9)/100;
		$igst = (floatval($tot)*18)/100;
		if ($state == 11) {
		$grandTotal = floatval($tot) + floatval($cgst) + floatval($sgst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add CGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
		</tr>";
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
		</tr>";
		} else {
		$grandTotal = floatval($tot) + floatval($igst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges(18%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
		</tr>";	
		}

		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
		</tr>";
		
		
		$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";   
			    }else
			    {
					$vcharge=$Restyuu11->visit;
			         $html.= "<tr style='background-color:#fff'>
                 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
				 <td></td>
                    <td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>Total - ".floatval($vcharge)."</td>
                 
                    </tr>"; 
			        
			    	$cgst = (floatval($vcharge)*9)/100;
					$sgst = (floatval($vcharge)*9)/100;
					$igst = (floatval($vcharge)*18)/100;
			    	if ($state == 11) {
			    		$grandTotal = floatval($vcharge) + floatval($cgst) + floatval($sgst);
			    		$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add CGST Charges(9%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
								</tr>";
								$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
								</tr>";
			    	} else {
			    		$grandTotal = floatval($vcharge) + floatval($igst);
			    		$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges(18%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
								</tr>";
			    	}

			    	$html.="<tr style='background-color:#fff'>
		 					<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
							<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
							<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
							</tr>";
							
							$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";
			        
			    }
			    
			    $extradata=$this->getheadfootimage();
		$html.="<tr>
		<td colspan='6' style='border:1px solid #333;padding:4px 6px;font-size:12px;font-style:italics;'>".$extradata[0]."</i></td>
		</tr>";
		
		$html.="</tbody></table>";
			
		}
		
	
		
		return $html;

	
	}
  
	
	function getmachinedetails($quoteid,$rowtype,$state)
	{
		
		$html='';
	$Restyuu=$this->db->select('machineid,amc,visit,total')->from('calibrationmachinehistory')->where('quoteid',$quoteid)->get();
	
	$ast='border-right:1px solid #000;';
		
		if($Restyuu->num_rows()>0)
		{
		    	 if($rowtype=='1')
		    {
		    

			$html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">AMC Charge</th>
			</tr>
			</thead>
			<tbody>';
		    }else if($rowtype=='2')
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Calibration Charge</th>
			</tr> 
			</thead>
			<tbody>';
		        
		    }else
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:left;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Product</th>
			<th style="text-align:left;border-top:1px solid;'.$ast.'">Price</th>
			</tr>
			</thead>
			<tbody>';
		        
		    }

	
				$totalarr=array();
				$totalarr[]=0;
				$amcarr=array();
				$amcarr[]=0;
				$j=1;			
				foreach($Restyuu->result() as $Restyuu11)
				{
					$vcharge=$Restyuu11->visit*1;
					$totalProduct = $Restyuu->num_rows();
					$charges = floatval($vcharge)/$totalProduct;
					$productCharge = $charges+$Restyuu11->amc;

			    	$insname=$this->getinstrumentname($Restyuu11->machineid);
			    if($rowtype=='1')
			    {
			
				$html.="<tr>
				<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;'>".$insname."</td>
				<td style='text-align:left;".$ast."'>".floatval($productCharge)."</td>
				</tr>";
				$amcarr[]=$Restyuu11->amc;
			    }else if($rowtype=='2')
			    {
			       	$html.="<tr>
			       	<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;'>".$insname."</td>
				<td style='text-align:left;".$ast."'>".floatval($productCharge)."</td>
				
				</tr>"; 
			       $amcarr[]=$Restyuu11->amc;
			        
			    }else
			    {
			        
			        	$html.="<tr>
			        	<td style='text-align:left;'>".$j."</td>
				<td style='text-align:left;".$ast."'>".$insname."</td>
				<td style='text-align:left;".$ast."'></td>
				
				</tr>";
			        
			    }
				
				$totalarr[]=$Restyuu11->total;
				
			$j++;
			}
			
			
		 if($rowtype=='1')
			    {
					
  //              $html.= "<tr>
  //              <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		// <td style='text-align:left;font-weight:bold;font-size:10px;'>Total AMC Cost</td>
		// <td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval(array_sum($amcarr))."</td>
		
		
		// </tr>";
		
		
		$vcharge=$Restyuu11->visit*1;
		
	// $html.="<tr>
	//  <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
	// 	<td style='text-align:left;font-weight:bold;font-size:10px;'>Visit Charges Cost</td>
	// 	<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($vcharge)."</td>
	// </tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($tot)."</td>
		</tr>";
		$cgst = (floatval($tot)*9)/100;
		$sgst = (floatval($tot)*9)/100;
		$igst = (floatval($tot)*18)/100;
		if ($state == 11) {
		$grandTotal = floatval($tot) + floatval($cgst) + floatval($sgst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;;font-size:10px;'>Add CGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
		</tr>";
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
		</tr>";
		} else {
		$grandTotal = floatval($tot) + floatval($igst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges (18%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
		</tr>";	
		}

		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
		</tr>";
		
		$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";
		
			    }else if($rowtype=='2')
			    {
                   		
  //              $html.= "<tr>
  //               <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		// <td style='text-align:left;font-weight:bold;font-size:10px;'>Total Calibration Cost</td>
		// <td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval(array_sum($amcarr))."</td>
		
		
		// </tr>";
		
		$vcharge=$Restyuu11->visit*1;
		
	// $html.="<tr>
	//  <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
	// 	<td style='text-align:left;font-weight:bold;font-size:10px;'>Visit Cost Charges</td>
	// 	<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($vcharge)."</td>
	// </tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($tot)."</td>
		</tr>";
		$cgst = (floatval($tot)*9)/100;
		$sgst = (floatval($tot)*9)/100;
		$igst = (floatval($tot)*18)/100;
		if ($state == 11) {
		$grandTotal = floatval($tot) + floatval($cgst) + floatval($sgst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add CGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
		</tr>";
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
		</tr>";
		} else {
		$grandTotal = floatval($tot) + floatval($igst);
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges(18%)</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
		</tr>";	
		}

		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:left;font-weight:bold;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
		</tr>";
		
		
		$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";   
			    }else
			    {
					$vcharge=$Restyuu11->visit;
			         $html.= "<tr style='background-color:#fff'>
                 <td style='text-align:left;font-weight:bold;font-size:10px;'></td>
				 <td></td>
                    <td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>Total - ".floatval($vcharge)."</td>
                 
                    </tr>"; 
			        
			    	$cgst = (floatval($vcharge)*9)/100;
					$sgst = (floatval($vcharge)*9)/100;
					$igst = (floatval($vcharge)*18)/100;
			    	if ($state == 11) {
			    		$grandTotal = floatval($vcharge) + floatval($cgst) + floatval($sgst);
			    		$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add CGST Charges(9%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($cgst)."</td>
								</tr>";
								$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add SGST Charges(9%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($sgst)."</td>
								</tr>";
			    	} else {
			    		$grandTotal = floatval($vcharge) + floatval($igst);
			    		$html.="<tr style='background-color:#fff'>
		 						<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
								<td style='text-align:left;font-weight:bold;font-size:10px;'>Add IGST Charges(18%)</td>
								<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($igst)."</td>
								</tr>";
			    	}

			    	$html.="<tr style='background-color:#fff'>
		 					<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
							<td style='text-align:left;font-weight:bold;font-size:10px;'>Grand Total Charges Cost</td>
							<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'>".floatval($grandTotal)."</td>
							</tr>";
							
							$amtwords=ucwords($this->getIndianCurrency(floatval($grandTotal)));
		
		$html.="<tr style='background-color:#fff'>
		<td style='text-align:left;font-weight:bold;font-size:10px;'></td>
		<td style='text-align:left;font-weight:bold;font-size:10px;'>Total In Words- ".$amtwords."</td>
		<td style='text-align:left;font-weight:bold;".$ast.";font-size:10px;'></td>
		</tr>";
			        
			    }
			    
			    $extradata=$this->getheadfootimage();
		$html.="<tr>
		<td colspan='6' style='border:1px solid #333;padding:4px 6px;font-size:12px;font-style:italics;'>".$extradata[0]."</i></td>
		</tr>";
		
		$html.="</tbody></table>";
			
		}
		
	
		
		return $html;
		
	}
	
	
	
	function getinstrumentname($inst)
	{
		$restsyt=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$inst)->get();
		
		if($restsyt->num_rows()>0)
		{

			foreach($restsyt->result() as $restsyt1);

			$instt=$restsyt1->instruments_name;

			return $instt;
			
		}else{
			
			return 0;
		}
		
		
	}
	
	function getheadfootimage()
	{
	$extradata=array();
	$restsyt=$this->db->select('script')->from('calibration_script')->where('company_id','1')->order_by('type','ASC')->get();
		
		if($restsyt->num_rows()>0)
		{

			foreach($restsyt->result() as $restsyt1)
			{

			$extradata[]=$restsyt1->script;
			
			}

			return $extradata;
			
		}
	}
	
	function getstates($st)
	{
		$resty=$this->db->select('state')->from('calibrationstate')->where('id',$st)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $restt);
			
			return $restt->state;
			
		}else{
			
			return '';
		}
		
	}


function getIndianCurrency($number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');

    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }

    $rupees = implode('', array_reverse($str));
    $paise = '';

    if ($decimal) {
        $paise = 'and ';
        $decimal_length = strlen($decimal);

        if ($decimal_length == 2) {
            if ($decimal >= 20) {
                $dc = $decimal % 10;
                $td = $decimal - $dc;
                $ps = ($dc == 0) ? '' : '-' . $words[$dc];

                $paise .= $words[$td] . $ps;
            } else {
                $paise .= $words[$decimal];
            }
        } else {
            $paise .= $words[$decimal % 10];
        }

        $paise .= ' paise';
    }

    return ($rupees ? $rupees . 'rupees ' : '') . $paise ;
}

	function editQuoteHistory($id) {
		$query = $this->db->select('id,company,contact_number,email,person_name,address')
				 ->from('calibrationquotehistory')
				 ->where('id',$id)
				 ->get();

				 if($query->num_rows() > 0) {
				 	return $query->result();
				 }
	}

	function updateQuote($data, $id) {
	$query = $this->db->where('id',$id)
			 		  ->update('calibrationquotehistory',$data);	
			return $this->db->affected_rows();
	}

	function getCalibrationStates() {

		$a=$this->input->get('q');
	$query = $this->db->select('id,state')
					  ->from('calibrationstate')
					  ->like('state',$a,'both')
					  ->get();

		 if($query->num_rows() > 0) {
		 	return $query->result();
		 }
	}

	function getCalibrationZones() {
		$query = $this->db->select('id,zone')
					  ->from('saleszone')
					  ->get();

		 if($query->num_rows() > 0) {
		 	return $query->result();
		 }
		}



		function saveCity($saveCity) {
			$this->db->insert('calibrationcity', $saveCity);
			return $this->db->insert_id();
		}

		function saveUpDownDistance($saveUpDown) {
		
			$this->db->insert('calibrationvisitdistance',$saveUpDown);
			return $this->db->affected_rows();
		}

		function checkIfCityExists($modalCity) {
			$query = $this->db->select('city')
					  		  ->from('calibrationcity')
					  		  ->where('city', $modalCity)
					  		  ->get();
			 
			return $query->num_rows();
		}
		
		function getSelectedState($stateID) {
			$query = $this->db->select('state')
					  		  ->from('calibrationstate')
					  		  ->where('id', $stateID)
					  		  ->get();
			 
			if($query->num_rows() > 0) {
			 	return $query->result();
			 }
		}
	
}
