<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Calibration_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}
	
	
	
	function getmachinedetails($quoteid,$rowtype)
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
			<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:center;border-top:1px solid;">Machine</th>
			<th style="text-align:center;border-top:1px solid;'.$ast.'">AMC Charge</th>
			</tr>
			</thead>
			<tbody>';
		    }else if($rowtype=='2')
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:center;border-top:1px solid;">Machine</th>
			<th style="text-align:center;border-top:1px solid;'.$ast.'">Calibration Charge</th>
			</tr>
			</thead>
			<tbody>';
		        
		    }else
		    {
		        $html='<table class="table tablenoborder tabledetails" style="border-top:0;margin-bottom: 5px;" border="1">
			<thead>
			<tr rowspan="2">
			<th style="text-align:center;border-top:1px solid;">Sr. No.</th>
			<th style="text-align:center;border-top:1px solid;'.$ast.'">Machine</th>
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
				<td style='text-align:center;'>".$j."</td>
				<td style='text-align:center;'>".$insname."</td>
				<td style='text-align:center;".$ast."'>".floatval($Restyuu11->amc)."</td>
				</tr>";
				$amcarr[]=$Restyuu11->amc;
			    }else if($rowtype=='2')
			    {
			       	$html.="<tr>
			       	<td style='text-align:center;'>".$j."</td>
				<td style='text-align:center;'>".$insname."</td>
				<td style='text-align:center;".$ast."'>".floatval($Restyuu11->amc)."</td>
				
				</tr>"; 
			       $amcarr[]=$Restyuu11->amc;
			        
			    }else
			    {
			        
			        	$html.="<tr>
			        	<td style='text-align:center;'>".$j."</td>
				<td style='text-align:center;".$ast."'>".$insname."</td>
				
				</tr>";
			        
			    }
				
				$totalarr[]=$Restyuu11->total;
				
			$j++;
			}
			
			
		 if($rowtype=='1')
			    {
					
               $html.= "<tr>
               <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>Total AMC COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
	 <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval($tot)."</td>
		</tr>";
		
			    }else if($rowtype=='2')
			    {
                   		
               $html.= "<tr>
                <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>Total CALIBRATION COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
	 <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#fff'>
		 <td style='text-align:center;font-weight:bold;'></td>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;".$ast."'>".floatval($tot)."</td>
		</tr>";
			    }else
			    {
					$vcharge=$Restyuu11->visit;
			         $html.= "<tr style='background-color:#fff'>
                 <td style='text-align:center;font-weight:bold;'></td>
                    <td style='text-align:center;font-weight:bold;".$ast."'>Grand Total - ".floatval($vcharge)."</td>
                 
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
	$restsyt=$this->db->select('script')->from('calibration_script')->order_by('type','ASC')->get();
		
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

	
}
