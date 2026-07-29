<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	
		
		
$this->load->model('User_model','user');
$config = array();  
$config['protocol'] = 'smtp';  
$config['smtp_host'] = 'smtpout.secureserver.net';  
$config['smtp_user'] = 'mitr@prestomitr.com';  
$config['smtp_pass'] = 'Presto@123!@#';   
$config['smtp_port'] = 587;  
$this->email->initialize($config);  
  
$this->email->set_newline("\r\n");  
$this->load->library('email', $config);
		
	}

public function index()
	{
		$this->load->view('vendor/vendor');
	}
public function add_new_vendor()
	{
	    
	    
	  $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('item_name', 'item name', 'required|trim');
	$this->form_validation->set_rules('vendor_name', 'vendor name', 'required|trim');
		$this->form_validation->set_rules('email', 'email', 'required|trim');
	$this->form_validation->set_rules('phone_number', 'phone_number', 'required|trim');
	$this->form_validation->set_rules('address', 'address', 'required|trim');
	$this->form_validation->set_rules('delivery_days', 'delivery days', 'required|trim');
	$this->form_validation->set_rules('payment_terms', 'payment_terms', 'required|trim');
	$this->form_validation->set_rules('original_price', 'original_price', 'required|trim');
	$this->form_validation->set_rules('discounted_price', 'discounted_price', 'required|trim');
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('vendor/vendor');
			}else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "vendor_for_review";
						
		$data = array('item_id'=>$this->input->post('item_name'),
		'vendor_name'=>$this->input->post('vendor_name'),
		'phone_number'=>$this->input->post('phone_number'),
		'email'=>$this->input->post('email'),
		'address'=>$this->input->post('address'),
		'delivery_days'=>$this->input->post('delivery_days'),
		'payment_terms'=>$this->input->post('payment_terms'),
		'original_price'=>$this->input->post('original_price'),
		'discounted_price'=>$this->input->post('discounted_price'),
		'discount_type'=>$this->input->post('discount_type'),
		'discount_percent'=>$this->input->post('discounted_percentage'),
		'total_value'=>$this->input->post('total_price'),
		'status'=>'0',
		'added_on'=>$date,
		'added_by'=>$_SESSION['logged_in']['user_id']
		);

		$result  = $this->db->insert($table,$data);	
		if($result)
		{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Vendor');

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
		$this->db->select('id,item_id,status')->from('vendor_for_review')->Where('status','0')->group_by('item_id');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			$ac = "1";	
			$rj = "2";
			$itemid = $row->item_id;
			$status = $row->status;
			$accept =  "<a href='".page_url."Vendor/accept_request/".$row->id."/".$ac."'><span class='btn btn-success btn-xs'>Accept</span></a>";
		
				$reject =  "<a href='".page_url."Vendor/accept_request/".$row->id."/".$rj."'><span class='btn btn-danger btn-xs'>Reject</span></a>";
			
		
		    $html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>VENDOR NAME</th><th style='padding:2px 2px 2px 2px'>DELIVERY DAYS</th><th style='padding:2px 2px 2px 2px'>PAYMENT TERMS</th><th style='padding:2px 2px 2px 2px'>ORIGINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:10%;'>DISCOUNT TYPE</th><th style='padding:2px 2px 2px 2px;width:15%;'>FINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:15%;'>ACTION</th></tr>";
		    
		    $Q = $this->db->select('id,vendor_name, delivery_days, payment_terms, original_price,discount_type,discounted_price, discount_percent,total_value')->from('vendor_for_review')->where('item_id',$row->item_id)->get();
		
		    foreach($Q->result() as $detail){
		        
		        $acceptvendor =  "<a href='".page_url."Vendor/accept_request/".$detail->id."/".$ac."/".$itemid."'><span class='btn btn-success btn-xs'>Accept</span></a>";
		        
		        if($detail->discount_type=='1'){
		            $discount = $detail->discount_percent." %";
		        }else if($detail->discount_type=='2'){
		            $discount=$detail->discounted_price;
		        }else{
		            $discount="";
		        }
		        $html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->vendor_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->delivery_days)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->payment_terms)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->original_price)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($discount)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->total_value)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$acceptvendor."</td>";
				$html.="</tr>";
		    }
		
		$html.="</table>";
		
			$vendor_data[] = array('sr_no'=>$i,
			'item_name'=>$row->item_id,
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
		$this->db->select('id, item_id, status')->from('vendor_for_review')->Where('status','1');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
			
			
			
			
			
			 $html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>VENDOR NAME</th><th style='padding:2px 2px 2px 2px'>PHONE NUMBER</th><th style='padding:2px 2px 2px 2px'>EMAIL</th><th style='padding:2px 2px 2px 2px'>DELIVERY DAYS</th><th style='padding:2px 2px 2px 2px'>PAYMENT TERMS</th><th style='padding:2px 2px 2px 2px'>ORIGINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:10%;'>DISCOUNT TYPE</th><th style='padding:2px 2px 2px 2px;width:15%;'>FINAL PRICE</th><th style='padding:2px 2px 2px 2px;width:15%;'>ACTION</th></tr>";
		    
		         $Q = $this->db->select('id,vendor_name, phone_number, email, delivery_days, payment_terms, original_price,discount_type,discounted_price, discount_percent,total_value')->from('vendor_for_review')->where('item_id',$row->item_id)->get();
		
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
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->original_price)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($discount)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->total_value)."</td>";
			    $html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".$add."</td>";
				$html.="</tr>";
		    }
		
		$html.="</table>";
		
			$vendor_data[] = array('sr_no'=>$i,
			'item_name'=>$row->item_id,
			'html'=>$html,
			'status'=>$add);
			$i++;
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
	
}
