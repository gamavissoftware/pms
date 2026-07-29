<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	
		
		
$this->load->model('User_model','user');
$config = array();  
        $config['protocol'] = 'smtp';  
        $config['smtp_host'] = 'smtp.googlemail.com';  
        $config['smtp_user'] = 'sundarindustrialsoftware@gmail.com';  
        $config['smtp_pass'] = 'SundarIndst@323';   
        $config['smtp_port'] = 465;  
        $config['smtp_auth'] = true;  
        $config['smtp_crypto'] = 'ssl';  
        $this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
        $this->load->library('email', $config); 
$this->load->model('Store_model');
$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		 
		
		
	}

public function vendorform()
	{
		
		$this->load->view('vendor/vendor');
	}
public function add_new_vendor()
	{
	    
	    
	$prno= $this->uri->segment(3);
	  $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('item_name', 'item name', 'required|trim');
	$this->form_validation->set_rules('vendor_name', 'vendor name', 'required|trim');
	
	$this->form_validation->set_rules('phone_number', 'phone_number', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('delivery_days', 'delivery days', 'required|trim');
	$this->form_validation->set_rules('payment_terms', 'payment_terms', 'required|trim');
	$this->form_validation->set_rules('original_price', 'original_price', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('vendor/vendor');
			}else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendor_for_review";
						
		$data = array('item_id'=>$this->input->post('item_id'),
		'vendor_name'=>$this->input->post('vendor_name'),
		'phone_number'=>$this->input->post('phone_number'),
		'address'=>$this->input->post('address'),
		'delivery_days'=>$this->input->post('delivery_days'),
		'payment_terms'=>$this->input->post('payment_terms'),
		'payment_mode'=>$this->input->post('pmode'),
		'original_price'=>$this->input->post('original_price'),
		'discounted_price'=>$this->input->post('discounted_price'),
		'delivery_by'=>$this->input->post('dby'),
		'discount_type'=>$this->input->post('discount_type'),
		'discount_percent'=>$this->input->post('discounted_percentage'),
		'total_value'=>$this->input->post('total_price'),
		'prno'=>$this->input->post('prno'),
		'status'=>'0',
		'added_on'=>$date,
		'added_by'=>$_SESSION['logged_in']['user_id']
		);

//echo "<pre>"; print_r($data);exit;
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Vendor/vendorform/'.$prno.'/'.$this->input->post('item_id'));

		}  
		}  
	    
	    
	    

		
	}
	
	public function pending_for_review()
	{
		$this->load->view('vendor/vendor_for_review');
	}
	public function pending_for_review_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->distinct();
		$this->db->select('id,item_id,status,prno')->from('vendor_for_review')->Where('status','0')->group_by('item_id');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$itemname=$this->getitemnameprwise($row->prno,$row->item_id);
			
			$ac = "1";	
			$rj = "2";
			$itemid = $row->item_id;
			$status = $row->status;
			$accept =  "<a href='".page_url."Vendor/accept_request/".$row->id."/".$ac."'><span class='btn btn-success btn-xs'>Accept</span></a>";
		
				$reject =  "<a href='".page_url."Vendor/accept_request/".$row->id."/".$rj."'><span class='btn btn-danger btn-xs'>Reject</span></a>";
			
		
		    $html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>VENDOR NAME</th><th style='padding:2px 2px 2px 2px'>DELIVERY DAYS</th><th style='padding:2px 2px 2px 2px'>PAYMENT TERMS</th><th style='padding:2px 2px 2px 2px'>PAYMENT MODE</th><th style='padding:2px 2px 2px 2px'>ORIGINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:10%;'>DISCOUNT AMT/%</th><th style='padding:2px 2px 2px 2px;width:15%;'>FINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:15%;'>ACTION</th></tr>";
		    
		    $Q = $this->db->select('id,item_id,vendor_name,payment_mode, delivery_days, payment_terms, original_price,discount_type,discounted_price, discount_percent,total_value')->from('vendor_for_review')->where('item_id',$row->item_id)->get();
		    
		    if(count($Q->result())=='1')
		    {
		        $back="background-color:#10C469;color:white;";
		    }else
		    {
		        $back='';
		    }
		    
		    $pricecomp=array();
            
            if(count($Q->result())>1)
            {
            foreach($Q->result() as $detail){
            $pricecomp[$detail->id]=$detail->total_value;
            }
            asort($pricecomp);
            $key = $value = NULL;
            foreach ($pricecomp as $key => $value) {
            break;
            }
            
            }else
            {
                $value='';
            }
		    
		   
		    
		    $ui=0;
		    foreach($Q->result() as $detail){
		        
		        
            if(count($Q->result())>1)
            {
				if($value==$detail->total_value)
				{
				    $back="background-color:#10C469;color:white;"; 
				}else
				{
				    $back="";
				}
            }
				
		        $acceptvendor =  "<a href='".page_url."Vendor/accept_request/".$detail->id."/".$ac."/".$itemid."'><span class='btn btn-warning btn-xs'>Accept</span></a>";
		        
		        if($detail->discount_type=='2'){
		            $discount = $detail->discount_percent." %";
		        }else if($detail->discount_type=='1'){
		            $discount=$detail->discounted_price;
		        }else{
		            $discount="";
		        }
		        $html.="<tr style='".$back."'>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->vendor_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->delivery_days)." Days</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->payment_terms)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->payment_mode)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->original_price)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($discount)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->total_value)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$acceptvendor."</td>";
				$html.="</tr>";
		   $ui++;
		   }
		
		$html.="</table>";
		
			$vendor_data[] = array('sr_no'=>$i,
			'item_name'=>$itemname,
			'html'=>$html);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function approved_vendors(){
		$this->load->view('vendor/approved_vendors');
	}
	

public function approved_vendor_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('id, item_id, status,prno')->from('vendor_for_review')->Where('status','1');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			
			$itemname=$this->getitemnameprwise($row->prno,$row->item_id);
			
			$Resteye=$this->checkifthismasterisopened($row->id,$row->prno);
			
			
			 $html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>VENDOR NAME</th><th style='padding:2px 2px 2px 2px'>PHONE NUMBER</th><th style='padding:2px 2px 2px 2px'>EMAIL</th><th style='padding:2px 2px 2px 2px'>DELIVERY DAYS</th><th style='padding:2px 2px 2px 2px'>PAYMENT TERMS</th><th style='padding:2px 2px 2px 2px'>PAYMENT MODE</th><th style='padding:2px 2px 2px 2px'>ORIGINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:10%;'>DISCOUNT TYPE</th><th style='padding:2px 2px 2px 2px;width:15%;'>FINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:15%;'>ACTION</th></tr>";
		    
		         $Q = $this->db->select('id,vendor_name, payment_mode,phone_number, email, delivery_days, payment_terms, original_price,discount_type,discounted_price, discount_percent,total_value')->from('vendor_for_review')->where('item_id',$row->item_id)->where('status','1')->get();
		
		    foreach($Q->result() as $detail){
		        
		        $add =  "<a href='".page_url."Master/User_management/add_new_vendor/".$row->id."'><span class='btn btn-success btn-xs'>Add in Master</span></a>";
		        
		        if($detail->discount_type=='1'){
		            $discount = $detail->discount_percent." %";
		        }else if($detail->discount_type=='2'){
		            $discount=$detail->discounted_price;
		        }else{
		            $discount="";
		        }
		        
		        $html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->vendor_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->phone_number)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->email)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->delivery_days)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->payment_terms)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->payment_mode)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->original_price)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($discount)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->total_value)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$add."</td>";
				$html.="</tr>";
		    }
		
		$html.="</table>";
		if($Resteye==0)
		{
			$vendor_data[] = array('sr_no'=>$i,
			'item_name'=>$itemname,
			'html'=>$html,
			'status'=>$add);
			$i++;
		}
		
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function accept_request(){
	    
	    $id = $this->uri->segment(3);
	    $status = $this->uri->segment(4);
	    $itemid = $this->uri->segment(5);
	    $data = array('status'=>$status);
	    $this->db->where('id',$id);
	    $this->db->update('vendor_for_review',$data);
	    
	    $data1 = array('status'=>'2');
	    $this->db->where('item_id',$itemid);
	    $this->db->where('status','0');
	    $this->db->update('vendor_for_review',$data1);
	    
	    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you! Vendor status successfully changed.</div><br/>');
				redirect(page_url.'Vendor/pending_for_review');
	    
	}
	
    public function totalpricecalculator(){
        $original_price = $this->input->post('original_price');
        $discount_percentage = $this->input->post('discounted_percentage');
        
        $TOTAL = $original_price*$discount_percentage/100;
        $grandtotal = $original_price-$TOTAL;
        echo $grandtotal; exit;
    }
    
     public function totalpricecalculator_after_dis(){
        $original_price = $this->input->post('original_price');
        $discounted_price = $this->input->post('discounted_price');
        
        
        $grandtotal = $original_price-$discounted_price;
        echo $grandtotal; exit;
    }
	
	
	function getitemnameprwise($prno,$itemid)
	{
		
	$itemname='';
	$rest=$this->db->select('type')->from('purchase_request')->where('prno',$prno)->get();
	if($rest->num_rows()>0)
	{
	foreach($rest->result() as $restt);

	if($restt->type==0)
	{

	$itemname=$this->Store_model->getmachineitemname($itemid);

	}else if($restt->type==1)
	{

	$itemname=$this->Store_model->getgeneralitemname($itemid);

	}
	}

	return $itemname;
		
		
	}
	
	
	function getpotype($prno)
	{

	$rest=$this->db->select('type')->from('purchase_request')->where('prno',$prno)->get();
	if($rest->num_rows()>0)
	{
	foreach($rest->result() as $restt);

	return $restt->type;
	}else{

	return "NA";
	}

	}
	
	
	function checkifthismasterisopened($quoteid,$prno)
	{
		$prtype=$this->getpotype($prno);
		if($prtype==0)
		{
			$tab1="vendors_price";
		}else{
			$tab1="vendorwise_house_keeping_item_price";
		}
		
		$restyeue=$this->db->select('id')->from($tab1)->where('quoteid',$quoteid)->get();
		return $restyeue->num_rows();
		
		
	}
	
	
	function Ascending($a, $b) {   
    if ($a == $b) {        
        return 0;
    }   
        return ($a < $b) ? -1 : 1; 
}  
	
	
}
