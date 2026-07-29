<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Autoscript extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
			$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            }
	}
	
	function totalstockvalue()
	{
		
		$html="*Weekly Stock Value Report*\n\n";
		$totalsum=array();
		
		/** GET IMS STOCK DETAILS **/
		$restyuiu=$this->db->select('id,category')->from('presto_machine_part_category')->where('cattype','1')->get();
		if($restyuiu->num_rows()>0)
		{
			foreach($restyuiu->result() as $restyuiu1)
			{
				$catwisecost=array();
				$restyui=$this->db->select('a.id,a.current_stock')->from('machine_parts_with_picture a')->join('vendors_price b','a.id=b.itemid')->where('a.category_id',$restyuiu1->id)->get();
				if($restyui->num_rows()>0)
				{
					foreach($restyui->result() as $restyui11)
					{
						$vprice=$this->getvendorlowerprice($restyui11->id);
						
						$stock=$restyui11->current_stock;
						
						$total=$vprice*$stock;
						
						$catwisecost[]=$total;
						
						
					}
					
					if(count($catwisecost)>0)
					{
					$html.=$restyuiu1->category."- ".round(array_sum($catwisecost),2)."\n\n";
					$totalsum[]=array_sum($catwisecost);
					}else
					{
						$html.=$restyuiu1->category."- 0"."\n\n";
						$totalsum[]=0;
					}
					
				}else
					{
						$html.=$restyuiu1->category."- 0"."\n\n";
						$totalsum[]=0;
					}
			
			
			}
			
			
			
		}
		
		/** END **/
		
		/** IMPORTED STOCK **/
		$imprice=array();
		$imported=$this->db->select('stock,mvalue')->from('presto_instruments')->where('type','1')->where('stock !=','0')->where('mvalue>','0')->get();
		if($imported->num_rows()>0)
		{
			foreach($imported->result() as $imported1)
			{
				$imprice[]=$imported1->stock*$imported1->mvalue;
				
				
			}
			if(count($imprice)>0)
			{
				$impr=array_sum($imprice);
				$totalsum[]=array_sum($imprice);
			}else{
				
				$impr=0;
				$totalsum[]=0;
			}
			
			$html.="IMPORTED STOCK- ".round($impr,2)."\n\n";
		}else{
			
			$html.="IMPORTED STOCK-0\n\n";
			$totalsum[]=0;
		}
		
		/** END **/
		
		$wipcost=array();
	$restyu=$this->productionflowlongreport();
	if($restyu==0)
	{
				$restqwee=$this->db->select('flow_id,fms_flow,stockvalue')->from('fms_flow')->where_in('production_flow_id',$restyu,false)->order_by('production_flow_id')->get();
				if($restqwee->num_rows()>0)
				{
				
				foreach($restqwee->result() as $restqwee1)
				{

				/** GET ALL INSTRUMENTS ON THIS STAGE **/
				$details=$this->onstagejobcard($restqwee1->flow_id,$restqwee1->stockvalue);

				/** END **/
				$wipcost[]=$details['stagetotal'];

				}
			}
	}
	
		if(count($wipcost)>0)
		{
		$html.="WIP COST - ".round(array_sum($wipcost),2)."\n\n";
		$totalsum[]=array_sum($wipcost);

		}else{

		$html.="WIP COST - 0\n\n";
		$totalsum[]=0;
		}
	
	if(count($totalsum)>0)
	{
		$tot=array_sum($totalsum);
	}else{
		
		$tot=0;
	}
	
	
		$html.="*_Total Stock Value - ".round($tot,2)."_*";
		

		$phones=array('919818777007','918447031736');
		
		foreach($phones as $phoneee)
		{
		/***WHATSAPP INTEGRATION***/
			$data = [
			'phone' => $phoneee, // Receivers phone
			'body' => $html, // Message
			];
			$json = json_encode($data); // Encode data to JSON
			// URL for request POST /message
			$url = 'https://eu17.chat-api.com/instance88514//message?token=lwpwzff7ubbp6dc6';
			// Make a POST request
			$options = stream_context_create(['http' => [
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			'content' => $json
			]
			]);
			// Send a request
			$result = file_get_contents($url, false, $options);
			echo $result;
			
		}
			
		
	
	}
	
	
	function getvendorlowerprice($itemid)
	{
	
		$price=0;
		$resul=$this->db->select('min(price) as minrate')->from('vendors_price')->where('itemid',$itemid)->get();
		if($resul->num_rows()>0)
		{
			
			foreach($resul->result() as $resul1);
			
			$price=$resul1->minrate;
		}
		
		return $price;
		
		
		
	}
	
	
	
	function productionflowlongreport()
{	
	$prd=array();
	$restyu=$this->db->select('id')->from('production_flow')->where('longreport','1')->get();
	if($restyu->num_rows()>0)
	{
		foreach($restyu->result() as $restyu1)
		{
			
			$prd[]=$restyu1->id;
		}
		
		$result = "'" . implode ( "', '", $prd ) . "'";
		return $result;
	}else{
		
		return 0;
	}
	
	
}


function onstagejobcard($flowid,$stockvalue)
{
	/** GET MACHINE CP **/
	$cp=0;
	$restyuiioo=$this->db->select('cp')->from('machinecp')->get();
	if($restyuiioo->num_rows()>0)
	{
		foreach($restyuiioo->result() as $restyuiioo1);
		$cp=$restyuiioo1->cp;
	}
	/** END **/

	$data=$this->db->select('a.jobcardid,b.job_card_no,c.instruments_name,c.mvalue')->from('order_stage a')->join('order_instruments b','a.jobcardid=b.id')->join('presto_instruments c','b.item_id=c.id')->where('a.flowstage',$flowid)->where('a.userstatus','0')->get();
	$total=array();
	if($data->num_rows()>0)
	{
		
		foreach($data->result() as $datas)
		{
			
			$machinewisevalue=$this->getmachinevalue($cp,$stockvalue,$datas->mvalue,$datas->jobcardid);
			$total[]=$machinewisevalue;
			
			
		}
		
	}
	
	$overallstagetot=$this->giveoveralldata($total);
	return array('stagetotal'=>$overallstagetot);
}



function giveoveralldata($total)
{
	if(count($total)>0)
	{
		
		return array_sum($total);
		
	}else{
		
		return 0;
		
	}
}


function getmachinevalue($cp,$stockvalue,$mvalue,$jobcard)
{
	
	$getproductioncost=($cp*$mvalue)/100;
	$onlyprdcost=$getproductioncost;
	
	$mval=($onlyprdcost*$stockvalue)/100;
	
	return $mval;
	
}


public function update_checklist_status(){
    
    //$yesterday = date('Y-m-d',strtotime("-1 days"));
    $date = date('Y-m-d');
					$qry1 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$date)->get();
					if($qry1->num_rows()>0){
						$status = "1";
					}else{
						$status= "0";
					}
					
					
					$qry = $this->db->select('a.task_id, b.tat_id, a.dateforemail, b.task_id, b.status')->from('compliance_set_date a')->join('compliance_task_report b','a.task_id=b.task_id','left')->where('b.status','1')->where('a.dateforemail<=',$date)->get();
					if($qry->num_rows()>0){
					
					
					foreach($qry->result() as $row){
						
					$data = array('task_id'=>$row->task_id,
					'status'=>$status,
					'task_date'=>date('Y-m-d'));
					$this->db->insert('checklist_done_notdone',$data);
					
					$today = date('Y-m-d');
					$data1 = array('dateforemail'=>$today);
					$this->db->where('task_id',$row->task_id);
					$this->db->update('compliance_set_date',$data1);
					
	
	$tatid = $row->tat_id;
	$todaysdate = date('Y-m-d');
	$nextdate=date('Y-m-d');
	$query1 = $this->db->select('id, turnaroundtime')->from('compliance_tat')->where('id',$row->tat_id)->get();
	foreach($query1->result() as $row1)
	{
		$todaysdate = date('Y-m-d');
		$nextdate="";
		if($row1->id=='1'){
			/** For 1 daily**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate); // Y-m-d
			$date->add(new DateInterval('P1D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='2'){
			/** For Weekly**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P7D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='4'){
				/** For 1 daily**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate); // Y-m-d
			$date->add(new DateInterval('P14D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='5'){
			/** For 3 months**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P90D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='6'){
			/** For 3 months**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P365D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='3'){
			/** For Per Monthly**/
			$todaysdate = date('Y-m-d');
			$date = new DateTime($todaysdate);
			$date->add(new DateInterval('P30D')); 
			$nextdate = $date->format('Y-m-d');
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				$nextdate = date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			/* CHECK HOLIDAY*/
		}elseif($row1->id=='7'){
		    /*TWICE IN A MONTH*/
		   $todaysdate = date('Y-m-d');
			$scheduleddate = date('d');
			$month = date('m');
			$year = date('Y');
			$nextmonth = date('m', strtotime($todaysdate. ' + 1 month'));
			if($nextmonth=='12'){
				$y = date('Y', strtotime($todaysdate. ' + 1 year'));
			}else{
				$y=$year;
			}
			if($tatinfo->first_date==$scheduleddate){
				$nextdate = $year."-".$month."-".$tatinfo->last_date;
			}else{
				$nextdate = $y."-".$nextmonth."-".$tatinfo->first_date;
			}
			
			
			/* CHECK HOLIDAY*/
			$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
			if($query->num_rows()>0){
				date('Y-m-d', strtotime("+1 day", strtotime($nextdate)));
			}else{
				$nextdate = $nextdate;
			}
			
			/* CHECK HOLIDAY*/ 
		}
	
	$query = $this->db->select('*')->from('compliance_set_date')->where('task_id',$row->task_id)->get();
	$res = $query->result();
	if($query->num_rows()>0){
		foreach($res as $updatedata)
		$data = array('dateforemail'=>$nextdate);
		$this->db->where('id',$updatedata->id);
		$this->db->update('compliance_set_date',$data);
		
	}else{
		
		$data = array('dateforemail'=>$nextdate,'task_id'=>$taskid);
		$this->db->insert('compliance_set_date',$data);
	}
	
	}
		}
			}
}
	
}