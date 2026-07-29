<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calibration extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		/**$ip = $_SERVER["REMOTE_ADDR"];
		 $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</span><br/>');
            redirect(page_url.'Dashboard');    
            } **/
            
            
            	$this->load->model('Store_model','storemodel');
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'smtpout.secureserver.net';  
		$config['smtp_user'] = 'mitr@prestomitr.com';  
		$config['smtp_pass'] = 'Presto@123!@#';   
		$config['smtp_port'] = 587; 
		$config['mailtype'] = 'html'; 
		$config['charset'] = 'iso-8859-1';  
   
        
        
        $this->email->initialize($config);  
        $this->email->set_newline("\r\n");  
        $this->load->library('email', $config);
	}
	
	function getquote()
	{
		$this->load->view('calibration/index');
	}
	
	function getstateOlddd()
	{
			$searchtrm= $_GET['q'];
			

				$this->db->select('id,state')->from('calibrationstate')->like('state',$searchtrm,'both',false)->order_by('state','ASC');
				$query =$this->db->get();
				if($query->num_rows()>0)
				{
				foreach($query->result() as $instruments){

				$json[] = array('id'=>$instruments->id, 'text'=>$instruments->state);

				}
				}else{

				$json[] = array('id'=>"", 'text'=>"No Data Available");

				}
				
				echo json_encode($json);
		
		
	}
	
	
		function getcity()
	{
			$searchtrm= $_GET['q'];
			

				$this->db->select('id,city')->from('calibrationcity')->like('city',$searchtrm,'both',false)->order_by('city','ASC');
				$query =$this->db->get();
				if($query->num_rows()>0)
				{
				foreach($query->result() as $instruments){

				$json[] = array('id'=>$instruments->id, 'text'=>$instruments->city);

				}
				}else{

				$json[] = array('id'=>"", 'text'=>"No Data Available");

				}
				
				echo json_encode($json);
		
		
	}
	
	
	function getstate()
	{
		$state= $this->uri->segment(3);
		
		$query=$this->db->select('id,stateid')->from('calibrationcity')->where('id',$state)->get();
		
		if($query->num_rows()>0)
		{
		
			foreach($query->result() as $instruments);
			$statename=$this->getstates($instruments->stateid);
			echo "<option value='".$instruments->stateid."' selected>".$statename."</option>";
			

		}
		
	}
	
	
	function generatequote()
	{
		$html='';
		if($_POST)
		{
		$type=$_POST['qtype'];
		$company=$_POST['company'];
		$state=$_POST['state'];
		$city=$_POST['city'];
		$ins=$_POST['ins'];

		if($ins<>'')
		{
		$inst=explode(',',$ins);
		
		
		if($type=='1')
		{
			$html='<div class="col-md-12" style="font-size:14px;text-align:center;margin-bottom:15px;">Showing AMC Charges For Client '.$company.'</div>
			<div class="col-md-12">
			<table style="width:100%">
			<tr style="background-color:#DADADA;">
			<th style="text-align:center; width:250px;">MACHINE</th>
			<th style="text-align:center;">AMC CHARGE</th> 
			</tr>';
			
		}else if($type=='2')
		{
			
			
			$html='<div class="col-md-12" style="font-size:14px;text-align:center;margin-bottom:15px;">Showing Calibration Charges For Client '.$company.'</div>
			<div class="col-md-12">
			<table style="width:100%">
			<tr style="background-color:#DADADA;">
			<th style="text-align:center;width:250px;">MACHINE</th>
			<th style="text-align:center;">CALIBRATION CHARGES</th> 
			
			
			</tr>';
			
			
		}else{
			
		
			$html='<div class="col-md-12" style="font-size:14px;text-align:center;margin-bottom:15px;">Showing Visit Charges For Client '.$company.'</div>
			<div class="col-md-12">
			<table style="width:100%">
			<tr style="background-color:#DADADA;">
			<th style="text-align:center;width:250px;">MACHINE</th>
			</tr>';
			
			
		}
		
		$totalarr=array();
		$totalarr[]=0;
		$amcarr=array();
		$amcarr[]=0;
        $visitarr=array();
        $visitarr[]=0;
		for($i=0;$i<count($inst);$i++)
		{
			
		$listprice=$this->getinstrumentlistprice($inst[$i]);
		$insname=$this->getinstrumentname($inst[$i]);
		
		$visitcharge=$this->getkms($state,$city);
		if(count($visitcharge)>0)
		{
			foreach($visitcharge as $visitcharge1);
			$updowndistance=$visitcharge1->updowndistance;
			$zone=$visitcharge1->zone;
			
		}else{
			$updowndistance='0';
			$zone='0';
			
		}
		
		$visitcharge=$this->getvisitcharge($updowndistance);
		if($type=='1')
		{
			/** AMC CHARGE **/
			$ty="AMC CHARGES";
			$fourper=4/100;
			$listper=$listprice*$fourper;
			$amccharge=$listper+$fourper;
			$totvisitcharge=4*$visitcharge;
		
			/** END **/
			
            /** ROUND TO 10 AT HGHER SIDE **/
            $amccharge=ceil($amccharge / 10) * 10;
            $totvisitcharge=ceil($totvisitcharge / 10) * 10;
            $total=$amccharge+$totvisitcharge;
            /** END **/
			
			
			$html.="<tr>
			<td style='text-align:center;'>".$insname."<input type='hidden' name='qmach[]' value='".$inst[$i]."'><input type='hidden' name='type".$inst[$i]."' value='1'></td>
			<td style='text-align:center;'>".$amccharge."<input type='hidden' name='amccharge".$inst[$i]."' value='".$amccharge."'></td>
			</tr>";
		
			$totalarr[]=$total;
			$amcarr[]=$amccharge;
			$visitarr[]=$totvisitcharge;
		
		
		
		}else if($type=='2')
		{
			/** AMC CHARGE **/
			$ty="CALIBRATION CHARGES";
			$fourper=3/100;
			$listper=$listprice*$fourper;
			$amccharge=$listper+$fourper;
			$totvisitcharge=1*$visitcharge;
			/** END **/
			
			 /** ROUND TO 10 AT HGHER SIDE **/
            $amccharge=ceil($amccharge / 10) * 10;
            $totvisitcharge=ceil($totvisitcharge / 10) * 10;
            $total=$amccharge+$totvisitcharge;
            /** END **/
			
			$html.="<tr>
			<td style='text-align:center;'>".$insname."<input type='hidden' name='qmach[]' value='".$inst[$i]."'><input type='hidden' name='type".$inst[$i]."' value='2'></td>
			<td style='text-align:center;'>".$amccharge."<input type='hidden' name='amccharge".$inst[$i]."' value='".$amccharge."'></td>
			
			</tr>";
				$totalarr[]=$total;
			$amcarr[]=$amccharge;
			$visitarr[]=$totvisitcharge;
		
		
			
		}else
		{
			$totvisitcharge=1*$visitcharge;
			
			 $totvisitcharge=ceil($totvisitcharge / 10) * 10;
        
				
			$html.="<tr>
			<td style='text-align:center;'>".$insname."<input type='hidden' name='qmach[]' value='".$inst[$i]."'><input type='hidden' name='type".$inst[$i]."' value='3'></td>
			
			</tr>";
			$totalarr[]=$totvisitcharge;
			 
		}




		}	


		if($type=='1')
		{
		$html.= "<tr>
		<td style='text-align:center;font-weight:bold;'>Total AMC COST</td>
		<td style='text-align:center;font-weight:bold;'>".array_sum($amcarr)."</td>
		
		
		</tr>
		<tr>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".$totvisitcharge."<input type='hidden' name='visitcharge' value='".$totvisitcharge."'></td>
	
		
		</tr>";
		$tot=array_sum($amcarr)+$totvisitcharge;
		$html.="<tr style='background-color:#66F2AC'>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".$tot."</td>
		</tr>";
		}else if($type=='2')
		{
		$html.= "<tr>
		<td style='text-align:center;font-weight:bold;'>Total CALIBRATION COST</td>
		<td style='text-align:center;font-weight:bold;'>".array_sum($amcarr)."</td>
		
		
		</tr>
		<tr>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".$totvisitcharge."<input type='hidden' name='visitcharge' value='".$totvisitcharge."'></td>
	
		
		</tr>";
		$tot=array_sum($amcarr)+$totvisitcharge;
		$html.="<tr style='background-color:#66F2AC'>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".$tot."</td>
		</tr>";
		}else{

		$html.="<tr style='background-color:#66F2AC'>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGE - ".$totvisitcharge."<input type='hidden' name='visitcharge' value='".$totvisitcharge."'></td>
		</tr>";
		}

		$html.="</table></div>
		<div class='col-md-12 text-center' style='margin-top:20px;'><input type='hidden' name='typess' value='".$type."'><input type='hidden' name='compp' value='".$company."'><input type='hidden' name='state' value='".$state."'><input type='hidden' name='city' value='".$city."'><input type='submit' class='btn btn-warning btn-sm' value='Save'></div>";

		}
		
	}
	
echo $html;exit;
	
	}
	
	function getinstrumentlistprice($inst)
	{
		$restsyt=$this->db->select('mvalue')->from('presto_instruments')->where('id',$inst)->get();
		
		if($restsyt->num_rows()>0)
		{

			foreach($restsyt->result() as $restsyt1);

			$instt=$restsyt1->mvalue;

			return $instt;
			
		}else{
			
			return 0;
		}
		
		
	}
	
	
	
	function getkms($state,$city)
	{
			$restsyt=$this->db->select('updowndistance,zone')->from('calibrationvisitdistance')->where('cityid',$city)->get();

			if($restsyt->num_rows()>0)
			{
			return $restsyt->result();

			}else{

			echo "NO CHARGES FOUND";
			}
		
		
	}
	
	function getvisitcharge($totaldistance)
	{
		
		$restyuui=$this->db->select('charges')->from('calibrationvisitslab')->where('lower<=',$totaldistance)->where('upper>=',$totaldistance)->get();
		if($restyuui->num_rows())
		{
			foreach($restyuui->result() as $restyuui1);
			
			return $restyuui1->charges;
			
		}else
		{
			return 0;
		}
		
		
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
	
	
	function savequote()
	{
		$qmach=$this->input->post('qmach');
		$compp=$this->input->post('compp');
		$state=$this->input->post('state');
		$city=$this->input->post('city');
		$address=$this->input->post('companyaddress');
		$typess=$this->input->post('typess');
		$contact_person = $this->input->post('person_name');
		$email = $this->input->post('email');
		$contact_number = $this->input->post('contact_number');
		$qcoint=$this->db->select('id')->from('calibrationquotehistory')->get();
		$resto="PI/".date('Y')."/".$qcoint->num_rows();
	$ddt=array('company'=>$compp,'type'=>$typess,'state'=>$state,'city'=>$city,'person_name'=>$contact_person,'email'=>$email,'contact_number'=>$contact_number,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'address'=>$address,'pino'=>$resto);
	
		$this->db->insert('calibrationquotehistory',$ddt);
		$lid= $this->db->insert_id();
		
		
		if(count($qmach)>0)
		{
			for($i=0;$i<count($qmach);$i++)
			{
				$insna=$qmach[$i];
				
				$type=$this->input->post('type'.$insna);
				$amccharge=$this->input->post('amccharge'.$insna);
				$visitcharge=$this->input->post('visitcharge');
				$totalcharge='0.00';
				
				if($type=='1')
				{
					$dt=array('quoteid'=>$lid,'machineid'=>$insna,'amc'=>$amccharge,'visit'=>$visitcharge,'total'=>$totalcharge);

					$this->db->insert('calibrationmachinehistory',$dt);
				}else if($type=='2')
				{
					$dt=array('quoteid'=>$lid,'machineid'=>$insna,'amc'=>$amccharge,'visit'=>$visitcharge,'total'=>$totalcharge);

					$this->db->insert('calibrationmachinehistory',$dt);

					
					
				}else
				{
					
					$dt=array('quoteid'=>$lid,'machineid'=>$insna,'amc'=>'0.00','visit'=>$visitcharge,'total'=>$visitcharge);

					$this->db->insert('calibrationmachinehistory',$dt);
					
				}
			}		


				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000">Quote Saved</span></div>');
				redirect(page_url.'Calibration/getquote');
			
			
		}
		
	}
	
	function quotehistory()
	{
		
		$this->load->view('calibration/calibrationhistory');
	}
	
	function history()
	{
	
	$type=$this->uri->segment(3);
	$state=$this->uri->segment(4);
		$scheduler_data = array();
		$this->db->select('*')->from('calibrationquotehistory');
		if($type<>'')
		{
		$this->db->where('type',$type);
		}
		
		if($state<>'')
		{
		$this->db->where('state',$state);
		}
		
		$tresty=$this->db->order_by('addedOn','DESC')->get();
		if($tresty->num_rows()>0)
		{
			
			$i=1;
			foreach($tresty->result() as $row)
			{
				if($row->type=='1')
				{
					$t="AMC";
				}else if($row->type=='2')
				{
					$t="CALIBRATION";
				}else{
					
					$t="VISIT CHARGES";
				}
			
				$state=$this->getstates($row->state);
				$city=$this->getcitys($row->city);
				$viewreport = "<a href='".page_url."Calibration/quotation/".$row->id."'><i class='fa fa-eye'></i></a>";
				$html=$this->getmachinedetails($row->id,$row->type);
				$scheduler_data[] = array('sr_no'=>$i,
				'company'=>$row->company,
				'type'=>$t,
				'contact_person'=>$row->person_name,
				'email'=>$row->email,
				'contact_number'=>$row->contact_number,
				'state'=>$state,
				'city'=>$city,
				'machine'=>$html,
				'addedOn'=>date('d-m-Y H:i:s',strtotime($row->addedOn)),
				'viewreport'=>$viewreport);
			
			$i++;
			}
			
			
			
		}
		
		
		
			$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
		
		
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
	
	
		function getcitys($st)
	{
		$resty=$this->db->select('city')->from('calibrationcity')->where('id',$st)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $restt);
			
			return $restt->city;
			
		}else{
			
			return '';
		}
		
	}
	
	function getmachinedetails($quoteid,$rowtype)
	{
		$html='';
	$Restyuu=$this->db->select('machineid,amc,visit,total')->from('calibrationmachinehistory')->where('quoteid',$quoteid)->get();
		
		if($Restyuu->num_rows()>0)
		{
		    if($rowtype=='1')
		    {
			$html='<table style="width:100%" class="saurabh">
				<tr style="background-color:#DADADA;">
				<th style="text-align:center; width:300px;">MACHINE</th>
				<th style="text-align:center;" width:50px;">AMC CHARGE</th> 
			
				</tr>';
		    }else if($rowtype=='2')
		    {
		        	$html='<table style="width:100%" class="saurabh">
				<tr style="background-color:#DADADA;">
				<th style="text-align:center; width:300px;">MACHINE</th>
				<th style="text-align:center;" width:50px;">CALIBRATION CHARGE</th> 
				
				</tr>';
		        
		    }else
		    {
		        
		        	$html='<table style="width:100%" class="saurabh">
				<tr style="background-color:#C1E6F4;">
				<th style="text-align:center; width:300px;">MACHINE</th>
				
				</tr>';
		        
		    }
				
				$totalarr=array();
				$totalarr[]=0;
				$amcarr=array();
				$amcarr[]=0;
			foreach($Restyuu->result() as $Restyuu11)
			{
			    	$insname=$this->getinstrumentname($Restyuu11->machineid);
			    if($rowtype=='1')
			    {
			
				$html.="<tr>
				<td style='text-align:center;'>".$insname."</td>
				<td style='text-align:center;'>".floatval($Restyuu11->amc)."</td>
				</tr>";
				$amcarr[]=$Restyuu11->amc;
			    }else if($rowtype=='2')
			    {
			       	$html.="<tr>
				<td style='text-align:center;'>".$insname."</td>
				<td style='text-align:center;'>".floatval($Restyuu11->amc)."</td>
				
				</tr>"; 
			       $amcarr[]=$Restyuu11->amc;
			        
			    }else
			    {
			        
			        	$html.="<tr>
				<td style='text-align:center;'>".$insname."</td>
				
				</tr>";
			        
			    }
				
				$totalarr[]=$Restyuu11->total;
				
			}
			
			
		 if($rowtype=='1')
			    {
					
               $html.= "<tr>
		<td style='text-align:center;font-weight:bold;'>Total AMC COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#66F2AC'>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval($tot)."</td>
		</tr>";
		
			    }else if($rowtype=='2')
			    {
                   		
               $html.= "<tr>
		<td style='text-align:center;font-weight:bold;'>Total CALIBRATION COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval(array_sum($amcarr))."</td>
		
		
		</tr>";
		
		$vcharge=$Restyuu11->visit*1;
		
	$html.="<tr>
		<td style='text-align:center;font-weight:bold;'>VISIT CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval($vcharge)."</td>
	</tr>";
		$tot=array_sum($amcarr)+$vcharge;
		$html.="<tr style='background-color:#66F2AC'>
		<td style='text-align:center;font-weight:bold;'>GRAND TOTAL CHARGES COST</td>
		<td style='text-align:center;font-weight:bold;'>".floatval($tot)."</td>
		</tr>";
			    }else
			    {
					$vcharge=$Restyuu11->visit;
			         $html.= "<tr style='background-color:#66F2AC'>
                
                    <td style='text-align:center;font-weight:bold;'>Grand Total - ".floatval($vcharge)."</td>
                 
                    </tr>"; 
			        
			        
			    }
		
		$html.="</table>";
			
		}
		
		
		return $html;
		
	}
	
	public function quotation(){
		$object['controller'] = $this; 
		$this->load->view('calibration/quotation',$object);
	}
	
	
		function sampletestscripts()
{
	$this->load->view('calibration/setscript');
}

function sampletestingscripts()
{
		$scheduler_data=array();
		
		$restyui12=$this->db->select('*')->from('calibration_script')->get();
		if($restyui12->num_rows()>0)
		{
			$i=1;
			foreach($restyui12->result() as $restyui121)
			{
				if($restyui121->type==1)
				{
					$type="Terms & Conditions";
					$script=$restyui121->script;
				}else if($restyui121->type==2)
				{
					$type="Header image";
					$script="<img src='".calibration_image.$restyui121->script."' style='width:100px'>";
				}else{
					$type="Footer Image";
					$script="<img src='".calibration_image.$restyui121->script."' style='width:100px'>";
				}

				$edit="<a href='".page_url."Calibration/editscript/".$restyui121->id."'><span class='btn btn-xs btn-warning'>EDIT</span></a>";
		$scheduler_data[] = array('sr_no'=>$i,
			'type'=>$type,
			'script'=>$script,
			'action'=>$edit);
			$i++;
			}
			
		}
			
			$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
	
}		

function editscript()
{

	
	$this->load->view('calibration/edit_script');
	
}

function updatesamplescript()
{
	
	if($this->input->post('type')=='1')
	{
	$script=$this->input->post('script');
	}else if($this->input->post('type')=='2')
	{
	$himage=$_FILES['headimage']['name'];
	$ex=explode('.',$himage);
	$ext=end($ex);
	$script=time().'.'.$ext;
	//ECHO UPLOADPATH.'calibrationheadfoot/'.$script; exit;
	move_uploaded_file($_FILES['headimage']['tmp_name'],UPLOADPATH.'calibration/'.$script);

	}else
	{

	$himage=$_FILES['footimage']['name'];
	$ex=explode('.',$himage);
	$ext=end($ex);
	$script=time().'.'.$ext;
	move_uploaded_file($_FILES['footimage']['tmp_name'],UPLOADPATH.'calibration/'.$script);

	}

	$data=array('script'=>$script,'updatedOn'=>date('Y-m-d H:i:s'),'updatedBy'=>$_SESSION['logged_in']['user_id']);
	
	$this->db->where('id',$this->uri->segment('3'));
	$this->db->update('calibration_script',$data);
	
	$this->session->set_flashdata('message','Record Updated');
	redirect(page_url.'Calibration/sampletestscripts');
	
}


	function getstatesuggested()
	{

	$q=$_GET['q'];
	$resty=$this->db->select('id,state')->from('calibrationstate')->like('state',$q,'both',false)->get();
	if($resty->num_rows()>0)
	{
	foreach($resty->result() as $instruments){
	$json[] = ['id'=>$instruments->id, 'text'=>$instruments->state];
	}
	
	echo json_encode($json);


	}


	}
	
	function filter()
	{
		$type=$this->input->post('type');
		$state=$this->input->post('state');
		
		redirect(page_url.'Calibration/quotehistory/'.$type.'/'.$state);
	
	}
	
	
	function getstatesuggestedselected()
	{

	$q=$_GET['q'];
	$state=$_GET['state'];
	$resty=$this->db->select('id,state')->from('calibrationstate')->where('id',$state)->get();
	if($resty->num_rows()>0)
	{
	foreach($resty->result() as $instruments){
	$json[] = ['id'=>$instruments->id, 'text'=>$instruments->state];
	}
	echo json_encode($json);
	}


	}
	
	
	
	function savequotationimage()
{
	
	$image = $_POST['image'];
	$pono = $_POST['sampleid'];
	
	$send=explode('/',$pono);
	$pono=end($send);
	
$location = UPLOADPATH."quotationpi/";
$image_parts = explode(";base64,", $image);
$image_base64 = base64_decode($image_parts[1]);
$filename = $pono.'.jpeg';
$file = $location . $filename;
file_put_contents($file, $image_base64);

	
}

function sendmailforpi()
{
$html='';
$rowid=$this->uri->segment(3);

$query=$this->db->select('company,type,pino,email')->from('calibrationquotehistory')->where('id',$rowid)->get();
if($query->num_rows()>0)
{
foreach($query->result() as $queries);

if($queries->type=='1')
{
$ty="AMC";
}else if($queries->type=='2')
{
$ty="Calibration";
}else
{
$ty="Visit";

}

$pon=explode('/',$queries->pino);
$po=end($pon);

		$html.= '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td style="padding: 20px; position: relative;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		</tr>
		<tr>
		<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://www.prestogroup.com/images-new/logo-1.png" width="200px;" alt="" /></td>
		</tr>
		<tr>
		<td height="50"><hr style="width: 100%;" color="#c3c3c3" size="1" />Dear Sir/Maam,<br/><br/>Greeting From PRESTO! <br/><br/>With reference to our discussion, please find the enclosed Proforma Invoice for the desired '.$ty.' Charges.<br/><br/>Kindly send purchase order along with the payment to proceed further.<br/><br/>';

		if($queries->type=='2')
		{
		$html.='<span style="background-color:yellow">*For Calibration, machines should be in working condition.</span>';
		}	

		$html.='</td></tr>
		
		<tr>
		<td align="left" style="background: #fff; padding: 15px 0px;" bgcolor="#120001">Regards<br/>Team Service<br/><a href="mailto:service@presogroup.com">service@presogroup.com</a><br/><a href="tel:91-1294272727">91-129-427-2727</a></td>
		
		</td>
		</tr>


		</table>
		</tr>
		</table>';

		$filepath=UPLOADPATH.'quotationpi/'.$po.'.jpeg';
		$sub=strtoupper($queries->company." ".$ty." PI");
		$subjectname = $sub;
		$this->email->set_mailtype("html");
		$this->email->to($queries->email);
		$this->email->cc('service@prestogroup.com');
		$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
		$this->email->from('mitr@prestomitr.com');
		$this->email->subject($subjectname);
		$this->email->attach($filepath);
		$this->email->message($html);
		$result11=$this->email->send();
		
		  $this->session->set_flashdata('message','<span class="alert alert-danger" style="z-index: 99999999999;color: #fff;background-color:red;">Email Send to Customer.</span><br/>');
redirect(page_url.'Calibration/quotation/'.$rowid);
		
		}

}
}


