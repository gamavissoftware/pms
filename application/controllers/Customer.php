<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$this->load->model('Dashboard_model','dashboardmodel');
		$this->load->model('Salecrm_model','salescrm');
		

	// if (!$this->session->userdata('logged_in'))
 //        { 
 //            $this->session->set_flashdata('message','Session Logged Out. Login to continue');
 //            redirect(page_url);
 //        }

		// $config = array();  
		// $config['protocol'] = 'smtp';  
		// $config['smtp_host'] = 'mail.sunderindoil.com';  
		// $config['smtp_user'] = 'info@sunderindoil.com';  
		// $config['smtp_pass'] = 'Manglesh@sd5';   
		// $config['smtp_port'] = 587;   
		// $config['newline'] = "\r\n";
  //       $this->email->initialize($config);  
  //       $this->load->library('email', $config);

		
	}
	public function quotation_customer_dashboard()
	{
		$this->load->view('customer/quotation_customer_dashboard.php');
	}

	public function customer_view()
	{
		$this->load->view('customer/customer.php');
	}

	public function mergecustomer()
	{
		$this->load->view('customer/mergecustomer');
	}
	
	
	/********Add detail*********/
	public function add_customer()
	{
		
		$user_id=$_SESSION['logged_in']['user_id'];
	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$state=$this->input->post('state');
		$city=$this->input->post('cityname');
	
		$table = "customer_detail";

		$rt=$this->db->select('id')->from('customer_detail')->where('customer_ref_no',$this->input->post('customer_code'))->get();
		if($rt->num_rows()==0)
		{

		$cust_name = $this->input->post('contact_person');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$pincode = $this->input->post('pincode');
		$alias = $this->input->post('alias');

		if($this->input->post('tds_appl')==1)
		{
			$td=1;
			$tdp=$this->input->post('tds_per');
		}else
		{
			$td=0;
			$tdp=0;
		}

			$data = array(
			'title'=>$this->input->post('title'),
			'company_name'=>$this->input->post('companyname'),
			'company_id'=>$this->input->post('company'),
			'customer_ref_no'=>$this->input->post('customer_code'),
			'email'=> $email_id,
			'customer_name'=>$cust_name,
			'customer_alias'=>$alias,
			'email'=>$email,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'address'=>$this->input->post('address'),
			'state'=>$state,
			'city'=>$city,
			'pincode'=>$pincode,
			'bill_address'=>$this->input->post('address'),
			'bill_state'=>$state,
			'bill_city'=>$city,
			'bill_pincode'=>$pincode,
			'bill_email'=>$email,
			'gst'=>$this->input->post('gst'),
			'country'=>'101',
			'status'=>$this->input->post('status'),
			'order_max_limit'=>$this->input->post('order_max_limit'),
			'payment_type'=>$this->input->post('payment_type'),
			'credit_days'=>$this->input->post('paymentterms'),
			'msme_number'=>$this->input->post('msme_number'),
			'added_on'=>$date,
			'tds_appl'=>$td,
			'tds_per'=>$tdp,
			'added_by'=>$user_id);
			

		
		$last_lead_id  = $this->db->insert($table,$data);
			
		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Customer/customer_view');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Duplicate Customer Code.</div>');
			redirect(page_url.'Customer/customer_view');
		}
			

		
	}
	public function customer_list()
	{
		$uri=$this->uri->segment(3);
		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name, c.country as selectedcountry')->from('customer_detail a');
		$this->db->join('leads c','a.id=c.company_name','left');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->where('a.country',101);
		if($uri!='')
		  {
		  $this->db->where('a.company_id',$uri);	
		  }
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();	
		//echo "<pre>"; print_r($res); exit;								
		$i=1;
		foreach($res as $row)
		{

			

			$edit = "<a href='".page_url."Customer/edit_customer/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			$q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->where('state_id',$row->state)->get();

			if($q->num_rows() > 0) {
            	foreach($q->result() as $state);
            	$state_name = $state->state_name;
			} else {
				$state_name = '';
			}
			

	

			if($row->status==1)
			{
				$sta="<a href='".page_url."Customer/mark_active_inactive/".$row->id."/0/".$uri."'><span class='btn btn-success'>Active</span></a>";
			}else
			{
				$sta="<a href='".page_url."Customer/mark_active_inactive/".$row->id."/1/".$uri."'><span class='btn btn-danger'>Inactive</span></a>";
			}
			if($row->exhibitiion_email_sent==1){
				$emaildelivery = "Delivered";
			}else{
				$emaildelivery = "Not Delivered";
			}
			
			
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'status'=>$sta,
			'code'=>$row->customer_ref_no,
			'company_name'=>$row->company_name,
			'gst'=>$row->gst,
			'address'=>$row->address,
			'contactperson'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'credit_limit'=>$row->credit_limit,
			'msme_number'=>$row->msme_number,
			'country'=>$row->country_name,
			'state'=>$state_name,
			'city'=>$row->city,
			'pincode'=>$row->pincode,
			'edit'=>$edit,
			'emaildelivery'=>$emaildelivery
			);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	

	public function update_customer_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "customer_detail";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Status successfully updated.</div>');
			redirect('Leads');
		}

	public function edit_customer(){
		$this->load->view('customer/edit_customer');
	}	
	
	public function update_customer_information()
	{
		$uri=$this->uri->segment(3);
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_detail";
		
		$user_id=$_SESSION['logged_in']['user_id'];
	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$state=$this->input->post('state');
		$city=$this->input->post('cityname');
	

		if($this->input->post('tds_appl')==1)
		{
			$td=1;
			$tdp=$this->input->post('tds_per');
		}else
		{
			$td=0;
			$tdp=0;
		}


		$table = "customer_detail";
		$cust_name = $this->input->post('contact_person');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$ctype = $this->input->post('ctype');
		$data = array(
			'title'=>$this->input->post('title'),
			'customer_type'=>$this->input->post('ctype'),
			'company_name'=>$this->input->post('companyname'),
			'company_id'=>$this->input->post('company'),
			'email'=> $email,
			'customer_name'=>$cust_name,
			'customer_alias'=>$this->input->post('alias'),
			'customer_ref_no'=>$this->input->post('customer_code'),
			'email'=>$email,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'address'=>$this->input->post('address'),
			'state'=>$state,
			'city'=>$city,
			'bill_address'=>$this->input->post('address'),
			'bill_state'=>$state,
			'bill_city'=>$city,
			'bill_pincode'=>$this->input->post('pincode'),

			'tds_appl'=>$td,
			'tds_per'=>$tdp,
			'customer_type'=>$ctype,
			'gst'=>$this->input->post('gst'),
			'country'=>'101',
			'msme_number'=>$this->input->post('msme_number'),
			'order_max_limit'=>$this->input->post('order_max_limit'),
			'payment_type'=>$this->input->post('payment_type'),
			'credit_days'=>$this->input->post('paymentterms'),
			'pincode'=>$this->input->post('pincode'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'gst_verified'=>$this->input->post('gst_verified'),
			'added_by'=>$user_id);
				
			// echo "<pre>";print_r($data);exit;
				
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update($table,$data);	

		if($result)
			
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Customer/customer_view');
						
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
		redirect(page_url.'Customer/customer_view');
		}
			
	
	}

	public function quotation()
	{
		$this->load->view('customer/create_qoute');
	}
	public function getcustomerdata()
	{
		$htm='';
		$htm.='<option value="" >Select</option>';
		$cid=$this->input->post('custid');
		if($cid<>'')
		{
		$res=$this->db->select('id,company_name')->from('customer_detail')->where('company_id',$cid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row)
			{
				$htm.='<option value='.$row->id.'>'.$row->company_name.'</option>';
			}
		}
	}else
	{

		$res=$this->db->select('id,company_name')->from('customer_detail')->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row)
			{
				$htm.='<option value='.$row->id.'>'.$row->company_name.'</option>';
			}
		}
	}



		echo $htm; 
	}
	public function getproductdata()
	{

		$html = '';
		$company_location = $this->input->post('proid');

		$query = $this->db->select('b.id, b.instruments_name,b.pack_size')
		 				  ->from('company_products a')
		 				  ->join('presto_instruments b', 'b.id=a.product_id')
						  ->where('a.company_id', $company_location)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name."-".$row->pack_size."</option>";
				}
			} 
				
		echo $html;

	}

	public function getCompanyProduct()
	{
		$html = '';
		$company_location = $this->input->post('proid');

		$query = $this->db->select('b.id, b.instruments_name,b.pack_size')
		 				  ->from('company_products a')
		 				  ->join('presto_instruments b', 'b.id=a.product_id')
						  ->where('a.company_id', $company_location)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name."-".$row->pack_size."</option>";
				}
			} 
				
		echo $html; 
	}

	public function getproductpricedata()
	{
		// $htm='';
		// $htm.='<option value="" >select</option>';
		$cid=$this->input->post('proid');
		$res=$this->db->select('a.id,a.discount_price,a.mvalue,a.unit, b.id as unit_id')
					  ->from('presto_instruments a')
					  ->join('units b', 'b.shortname=a.unit', 'left')
					  ->where('a.id',$cid)
					  ->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			echo $row->mvalue.'|'.$row->unit.'|'.$row->unit_id.'|'.$row->discount_price; 
		}
		 
	}
	public function getactualprice()
	{
		$cid=$this->input->post('proid');
		$res=$this->db->select('a.id,a.discount_price,a.mvalue,a.unit, b.id as unit_id')
					  ->from('presto_instruments a')
					  ->join('units b', 'b.shortname=a.unit', 'left')
					  ->where('a.id',$cid)
					  ->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			echo $row->mvalue; 
		}
		 
	}
	public function getactualdiscountedprice()
	{
		$cid=$this->input->post('proid');
		$res=$this->db->select('a.id,a.discount_price,a.mvalue,a.unit, b.id as unit_id')
					  ->from('presto_instruments a')
					  ->join('units b', 'b.shortname=a.unit', 'left')
					  ->where('a.id',$cid)
					  ->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			echo $row->discount_price; 
		}
		 
	}
	public function getactualpriceunit()
	{
		$cid=$this->input->post('proid');
		$res=$this->db->select('a.id,a.discount_price,a.mvalue,a.unit, b.id as unit_id')
					  ->from('presto_instruments a')
					  ->join('units b', 'b.shortname=a.unit', 'left')
					  ->where('a.id',$cid)
					  ->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			echo $row->unit; 
		}
		 
	}

	function setdistributorterms(){
		$dis = $this->input->post('selectdistributor');
		$q = $this->db->select('id, firm_name')->from('distributor')->where('id',$dis)->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row);
			echo "Product Distribution will be done through ".$row->firm_name; exit;
		}
	}
	public function getdiscountpricedata()
	{
		// $htm='';
		// $htm.='<option value="" >select</option>';
		$cid=$this->input->post('proid');
		$res=$this->db->select('id,discount_price')->from('presto_instruments')->where('id',$cid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			echo $row->discount_price; 
		}
		 
	}

	public function addquotation()
	{

		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_quotation";

		$query = $this->db->select('id')
						  ->from('customer_quotation')
						  ->order_by('id','desc')
						  ->limit(1)
						  ->get();
						  
		if($query->num_rows() > 0) {
			foreach($query->result() as $last_id);
				$lastid = $last_id->id;
				$uniqueno = str_pad($last_id->id+1, 3, '0', STR_PAD_LEFT);
		} else {
				$lastid = 0;
				$uniqueno = '001';
		}

		$unique_no = str_pad($uniqueno+1, 3, '0', STR_PAD_LEFT);

		$data = array(
			'company_id'=>$this->input->post('company'),
			'customer_id'=>$this->input->post('customer'),
			'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
			'ref_id'=>$unique_no,
			'check_terms'=>$this->input->post('chk_tnc'),
			'general_terms'=>$this->input->post('drums_tnc'),
			'bulk_terms'=>$this->input->post('bulk_tnc'),
			'added_on'=>$date,
			'added_by'=>$user_id
			);
		$result  = $this->db->insert($table,$data);
		$insert_id = $this->db->insert_id();
		if($result>0)
		{
			$productname = $this->input->post('product');
			$comp_product = $this->input->post('comp_product');
            $qty = $this->input->post('qty');
            $packsize=$this->input->post('pack_size');
            $listprice = $this->input->post('listprice');
	         $discount = $this->input->post('discountprice');
	         $discountpricehide = $this->input->post('discountpricehide');
	         $netprice = $this->input->post('netprice');
       		for($i=0 ;$i<count($productname);$i++){
       			  
       		if($listprice[$i] < $discountpricehide[$i]) {
       			$flag = 0;
       		} else {
       			$flag = 1;
       		}

       		$cp=$this->salescrm->getcurrentcp($productname[$i]);
 			$data = array(
			'quotation_id'=>$insert_id,
			'competitor_product'=>$comp_product[$i],
			'product_id'=>$productname[$i],
			'qty'=>$qty[$i],
			'pack_size'=>$packsize[$i],
			'list_price'=>$listprice[$i],
			'discount_price'=>$discount[$i],
			'net_price'=>$netprice[$i],
			'flag' => $flag,
			'msp'=>$discountpricehide[$i],
			'cp'=>$cp,
			'new_batch_code'=>1,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id
			);
            $this->db->insert('customer_quotation_detail',$data);
	    	}
		}

		$darray = array(
					'email' => $this->input->post('cust_email'),
					'contact_no' => $this->input->post('cust_mobile_no')
					);
						
			$this->db->where('id', $this->input->post('customer'))
					 ->update('customer_detail', $darray);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			// redirect(page_url.'Customer/discount_approval/'.$insert_id);
			redirect(page_url.'Customer/quotation_preview/'.$insert_id);
	}
		public function quotation_view()
		{
			$this->load->view('customer/quotation_preview');
		}

		function discount_approval() {
			$this->load->view('customer/discount_approval');
		}

		function discount_approval_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price,a.added_on')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
					
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";
					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->added_on)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		$sql = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price,a.added_on')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('store_rack_location c', 'c.id=b.hpcl_company')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->list_price != '' && $row->list_price > 0) {
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_lead_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";

					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->added_on)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
					}
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function save_discount_remarks() {
		$flag=$this->uri->segment(3);
		$flag1=$this->uri->segment(4);
		$detail_id = $this->input->post('detail_id');
		$discount_approval = $this->input->post('discount_approval');
		$remarks = $this->input->post('remarks');
 
		$data = array(
					  'flag' => $discount_approval,
					  'remarks' => $remarks,
					  'approve_reject_on' => date('Y-m-d H:i:s'),
					  'approve_reject_by' => $this->session->userdata['logged_in']['user_id']
					 );

		$this->db->where('id', $detail_id)
				 ->update('customer_quotation_detail', $data);

		if($discount_approval == 1) {
			$msg = 'Discount has been successfully approved.';
		} else {
			$msg = 'Discount has been successfully rejected.';
		}

		if($flag=='')
		{
		$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Customer/discount_approval');
		}else
		{
			if($flag==1)
			{
			$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Leads/common_approval');
		}else if($flag==3)
		{
		$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Leads/order_discount_approval');
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Leads/quotation_discount_approval');
		}
		}
	}

	function save_lead_discount_remarks() {


		$detail_id = $this->input->post('lead_detail_id');
		//echo $detail_id; exit;
		$discount_approval = $this->input->post('lead_discount_approval');
		$remarks = $this->input->post('lead_remarks');
 
		$data = array(
					  'flag' => $discount_approval,
					  'remarks' => $remarks,
					  'approve_reject_on' => date('Y-m-d H:i:s'),
					  'approve_reject_by' => $this->session->userdata['logged_in']['user_id']
					 );

		$this->db->where('id', $detail_id)
				 ->update('lead_products', $data);


		if($discount_approval == 1) {
			$msg = 'Discount has been successfully approved.';
			$s="APPROVED 👍 ";
		} else {
			$msg = 'Discount has been successfully rejected.';
			$s="REJECTED 👎";
		}

		$restey=$this->db->select('b.price,b.remarks,d.first_name,d.last_name,d.contact_number,a.customer_name,a.company_name,c.instruments_name')->from('leads a')->join('lead_products b','a.id=b.lead_id')->join('presto_instruments c','c.id=b.product_id')->join('system_users d','a.added_by=d.user_id')->where('b.id',$detail_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$contact_no=$row->contact_number;
			$message="Hello ".$row->first_name." ".$row->last_name.", \n\n";
			$message.="Your Following Discount Approval has  been *".$s."* \n\n";
			$message.="*Company: ".$row->company_name."*\n";
			$message.="*Product: ".$row->instruments_name."*\n";
			$message.="*Price Offered: ".$row->price."*\n";
			$message.="*Status: ".$s."*\n";
			$message.="*Remarks: ".TRIM($remarks)."* \n\n";
			$message.="Share Quote with customer once all approvals are made \n";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => $contact_no.',8447031736',
			//'receiverMobileNo' => '8447031736',
			'username' => '',
			'password' => '',
			'message' => str_replace("&nbsp;", " ", strip_tags($message))		
			);

			// echo "<pre>";print_r($post);exit;

			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);



		}


		$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Leads/common_approval');
	}

	function edit_quote()
	{
		$this->load->view('customer/edit_quote');
	}

	public function getcustomerdataedit()
	{
		$htm='';
		$htm.='<option value="" >Select</option>';
		$cid=$this->input->post('custid');
		$selectedcompany=$this->input->post('selectedcompany');
		$res=$this->db->select('id,company_name')->from('customer_detail')->where('company_id',$cid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row)
			{
				if($row->id==$selectedcompany)
				{
					$a="selected";
				}else
				{
					$a='';
				}
				$htm.='<option value="'.$row->id.'" '.$a.'>'.$row->company_name.'</option>';
			}
		}
		echo $htm; 
	}
	public function updatequotation()
	{
		$uid=$this->uri->segment('3');
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_quotation";
		$upd_id=$this->input->post('editid');
		$flag_upd=$this->input->post('flag');
		for($j=0 ;$j<count($upd_id);$j++)
		{
			$comp_product_old = $this->input->post('comp_producedit'.$upd_id[$j]);
			$productname_old = $this->input->post('productedit'.$upd_id[$j]);
            //$packsize_old = $this->input->post('pack_sizeedit'.$upd_id[$j]);
            $qty_old = $this->input->post('qtyedit'.$upd_id[$j]);

            $listprice_old = $this->input->post('listpriceedit'.$upd_id[$j]);
	         $discount_old = $this->input->post('discountpriceedit'.$upd_id[$j]);
	         $discountpricehide_old = $this->input->post('discountpricehideedit'.$upd_id[$j]);
	         $netprice_old = $this->input->post('netpriceedit'.$upd_id[$j]);

	        if($this->input->post('chk_order_punch') == 0) {

		         if($listprice_old < $discountpricehide_old) {
	       			$flag = 0;
	       		} else {
	       			$flag = 1;
	       		}
	        } else {
	        	$flag = $flag_upd[$j];
	        }

	        // echo $flag;exit;
       		$data = array(
			'product_id'=>$productname_old,
			'qty'=>$qty_old,
			'list_price'=>$listprice_old,
			'competitor_product'=>$comp_product_old,
			'discount_price'=>$discount_old,
			'net_price'=>$netprice_old,
			'flag' => $flag
			);
       		$this->db->where('id',$upd_id[$j]);
            $this->db->update('customer_quotation_detail',$data);
	    	

		}
		
		if($uid<>'')
		{

			$data1 = array(
			'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
			'check_terms'=>$this->input->post('chk_tnc'),
			'general_terms'=>$this->input->post('drums_tnc'),
			'bulk_terms'=>$this->input->post('bulk_tnc')
			);
			$this->db->where('id',$uid);
			$this->db->update('customer_quotation',$data1);

			$comp_product = $this->input->post('comp_product');
			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            //$pack_size = $this->input->post('pack_size');
            $listprice = $this->input->post('listprice');
	         $discount = $this->input->post('discountprice');
	         $discountpricehide = $this->input->post('discountpricehide');
	         $netprice = $this->input->post('netprice');
       		for($i=0 ;$i<count($productname);$i++){
       			  
       		if($listprice[$i] < $discountpricehide[$i]) {
       			$flag = 0;
       		} else {
       			$flag = 1;
       		}

       		$cp=$this->salescrm->getcurrentcp($productname[$i]);
 			$data = array(
			'quotation_id'=>$uid,
			'competitor_product'=>$comp_product[$i],
			'product_id'=>$productname[$i],
			'qty'=>$qty[$i],
			'list_price'=>$listprice[$i],
			'discount_price'=>$discount[$i],
			'net_price'=>$netprice[$i],
			'flag' => $flag,
			'msp'=>$discountpricehide[$i],
			'cp'=>$cp,
			'new_batch_code'=>1,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id
			);
			if($productname[$i] !='')
			{
            $this->db->insert('customer_quotation_detail',$data);
       		}
	    	}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			// redirect(page_url.'Customer/discount_approval/'.$insert_id);
		if($this->uri->segment(4) == 1) {
			redirect(page_url.'Customer/rejected_quotations');
		} else {
			redirect(page_url.'Customer/quotation_dashboard');
		}
	}

	public function updateleadquotation()
	{
		$uid=$this->uri->segment(3);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "leads";
		$upd_id=$this->input->post('editid');
		$flag_upd=$this->input->post('flag');
		for($j=0 ;$j<count($upd_id);$j++)
		{
			$productname_old = $this->input->post('productedit'.$upd_id[$j]);
            $qty_old = $this->input->post('qtyedit'.$upd_id[$j]);
            $listprice_old = $this->input->post('listpriceedit'.$upd_id[$j]);
	         $discount_old = $this->input->post('discountpriceedit'.$upd_id[$j]);
	         $discountpricehide_old = $this->input->post('discountpricehideedit'.$upd_id[$j]);
	         $netprice_old = $this->input->post('netpriceedit'.$upd_id[$j]);

		         if($listprice_old < $discountpricehide_old) {
	       			$flag = 0;
	       		} else {
	       			$flag = 1;
	       		}


	        // echo $flag;exit;
       		$data = array(
			'product_id'=>$productname_old,
			'qty'=>$qty_old,
			'price'=>$listprice_old,
			'percent_amt'=>$discount_old,
			'net_price'=>$netprice_old,
			'flag' => $flag
			);
       		$this->db->where('id',$upd_id[$j]);
            $this->db->update('lead_products',$data);
	    	

		}
		
		if($uid<>'')
		{

			$data1 = array(
			'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
			'check_terms'=>$this->input->post('chk_tnc'),
			'general_terms'=>$this->input->post('drums_tnc'),
			'bulk_terms'=>$this->input->post('bulk_tnc')
			);
			$this->db->where('id',$uid);
			$this->db->update('leads',$data1);

			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            $listprice = $this->input->post('listprice');
	         $discount = $this->input->post('discountprice');
	         $discountpricehide = $this->input->post('discountpricehide');
	         $netprice = $this->input->post('netprice');
       		for($i=0 ;$i<count($productname);$i++){
       			  
       		if($listprice[$i] < $discountpricehide[$i]) {
       			$flag = 0;
       		} else {
       			$flag = 1;
       		}

 			$data = array(
			'lead_id'=>$uid,
			'product_id'=>$productname[$i],
			'qty'=>$qty[$i],
			'price'=>$listprice[$i],
			'percent_amt'=>$discount[$i],
			'net_price'=>$netprice[$i],
			'flag' => $flag,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id
			);
			if($productname[$i] !='')
			{
            $this->db->insert('lead_products',$data);
       		}
	    	}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Customer/rejected_quotations');
	}

	public function deletequotationpoduct()
	{
		$id=$this->uri->segment(3);
		$uri=$this->uri->segment(4);
		$this->db->where('id', $id);
    	$this->db->delete('customer_quotation_detail');
    	$this->session->set_flashdata('message','<div class="alert alert-danger">Product successfully deleted.</div>');
			// redirect(page_url.'Customer/discount_approval/'.$insert_id);
			redirect(page_url.'Customer/edit_quote/'.$uri);
	}
	public function send_mail_to_customer()
	{
		$uri=$this->uri->segment(3);
if($uri<>'')
{
$res=$this->db->select('*')->from('customer_quotation')->where('id',$uri)->get();
if($res->num_rows() >0)
{
    foreach($res->result() as $com)
    {
    //echo "<pre>"; print_r($com); exit;
        $companyid=$com->company_id;
        $customerid=$com->customer_id;
    }
    $cu=$this->db->select('customer_name,city,state,contact_no,address,company_name,email')->from('customer_detail')->where('id',$customerid)->get();
    if($cu->num_rows() >0)
    {
        foreach($cu->result() as $cdetail);
        $customer_name=$cdetail->customer_name;
        $company_name=$cdetail->company_name;
        $city=$cdetail->city;
        $contact_no=$cdetail->contact_no;
        $address=$cdetail->address;
        $email = $cdetail->email;
    }
}
else
{
  $companyid='';
  $customerid=''; 
  $company_name='';
  $city='';
  $contact_no='';
  $address=''; 
  $email = '';
}
}else{
    redirect(page_url.'Customer/quotation/'.$uri);
}
		$subjectname='Quotation of Industrial Lubricants.';
		$Message ='<table style="width: 100%; font-size:14px; font-family: monospace;">
        <tr>
           
            <td width="100%" style=" padding:5px; box-shadow: 1px 1px 10px lightgray; background-color:white;">
                <table style="width: 100%; font-size:14px;">
                    <tr>
                        
                        <td width="100%">
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="50%">To</td>
                                    <td width="50%" style="text-align:right;">Date:'. date('d-m-Y').'</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="40%">'.$customer_name.'<br>
                                        M/s '.$company_name.'
                                        <br>
                                        '.$address.'.
                                    </td>
                                    <td width="30%"></td>
                                    <td width="30%"></td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="80%" style="text-align:center;">Sub: Quotation of Industrial Lubricants.
                                    </td>
                                    <td width="10%"></td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        Sir,<br>
                                        In reference to our meeting discussion held regarding Industrial Oil supply to
                                        your respective business units therefore, we hereby offer you Quotation for the
                                        products as required by yourself.
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <table style="width: 100%; font-size:14px;" border="1">
                                <tr>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Product Name with
                                        HSN Code</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Pack size</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">List price per
                                        ltr</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Discount Per Ltr
                                    </th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Net price per ltr
                                        (GST Extra) </th>
                                </tr>';
                                
                                $pro=$this->db->select('*')->from('customer_quotation_detail')->where('quotation_id',$uri)->get();
                                if($pro->num_rows() >0)
                                {
                                    foreach($pro->result() as $prodetail)
                                    {

                                        $ins=$this->db->select('id,instruments_name')->from('presto_instruments')->where('id',$prodetail->product_id)->get();
                                        foreach($ins->result() as $instruments);

                                $Message.='
                                <tr>
                                    <td style="padding: 5px; text-align:center;">'.$instruments->instruments_name.'</td>
                                    <td style="padding: 5px; text-align:center;">'.$prodetail->qty.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $prodetail->list_price.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $prodetail->discount_price.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $prodetail->net_price.'</td>
                                </tr>';
                             } }
                           $Message.='</table>';
                            
                            $term_d=$this->db->select('general_terms')->from('customer_quotation')->where('id',$uri)->get();
                            if($term_d->num_rows() >0)
                            {
                                foreach($term_d->result() as $term_drums)
                                {
                            
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.
                               $term_drums->general_terms;
                            $Message.='</table>';
                        	}}
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <i>Please feel free to contact us incase of any further queries. We shall be
                                            more than happy to assist/resolve all your queries.</i>
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td style="color: red;">
                                        Please Note: - We are the only authorized C&F Agents for Industrial lubricants
                                        for <b>M/s HINDUSTAN PETROLEUM CORPORATION LIMITED</b> in Faridabad district and
                                        that
                                        We/HPCL does not take any responsibility for any unauthorized product supplied
                                        by unauthorized/illegitimate supplier.
                                    </td>
                                </tr>
                            </table>';

                            $com=$this->db->select('*')->from('store_rack_location')->where('id',$companyid)->get();
                            if($com->num_rows() >0)
                            {
                                foreach($com->result() as $company);
                           
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
                                        $company->contact_person.'<br>'.'
                                        M/S CFA// '.$company->companyname.'<br>'.
                                         $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
                                        $company->address.'<br>
                                        Emails : '. $company->email_id.'<br>
                                        Office Landline No. '.$company->landline_number.'<br>
                                        MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
                                        Please locate us on HPCL Website:'.$company->locate_us.'


                                    </td>
                                </tr>
                            </table>';
                        } 
                            $term_d=$this->db->select('bulk_terms')->from('customer_quotation')->where('id',$uri)->get();
                            if($term_d->num_rows() >0)
                            {
                                foreach($term_d->result() as $term_drums)
                                {
                          
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.$term_drums->bulk_terms;
                              
                            $Message.='</table>';
                        	}}

                               $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td>
                                            <i>Please feel free to contact us incase of any further queries. We shall be
                                                more than happy to assist/resolve all your queries. </i>
                                        </td>
                                    </tr>
                                </table>
                                <table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td style="color: red;">
                                            We are the only authorized C&F Agents for industrial lubricants for HPCL in
                                            Faridabad district and that HPCL does not take any responsibility for any
                                            unauthorized product supplied by unauthorized/illegitimate supplier.
                                        </td>
                                    </tr>
                                </table>';
                              
                           
                            $com=$this->db->select('*')->from('store_rack_location')->where('id',$companyid)->get();
                            // if($com->num_rows() >0)
                            // {
                                foreach($com->result() as $company);
                           
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
                                        $company->contact_person.'<br>'.'
                                        M/S CFA//'.$company->companyname.'<br>'.
                                         $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
                                        $company->address.'<br>
                                        Emails : '. $company->email_id.'<br>
                                        Office Landline No. '.$company->landline_number.'<br>
                                        MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
                                        Please locate us on HPCL Website:'.$company->locate_us.'


                                    </td>
                                </tr>
                            </table>';
                        // } 
                       $Message.='</td>
                        
                    </tr>
                </table>
            </td>
           
        </tr>
    </table>';


		
		$this->email->set_mailtype("html");
		$this->email->to($email);
		$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com,webdevelopment1@gamavis.com');
		//$this->email->from($company->email);
		$this->email->from('info@sunderindoil.com');
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$result11=$this->email->send();
		//$this->email->print_debugger(); exit;
		$data=array(
			'quotation_id'=>$uri,
			'mail_sent_on'=>date('Y-m-d H:i:s'),
			'mail_sent_by'=>$_SESSION['logged_in']['user_id']
		);
		$this->db->insert('customer_quotation_mail_history',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">mail send.</div><br/>');
			redirect(page_url.'Customer/quotation_dashboard/');
	}

	function quotation_dashboard()
	{
		$this->load->view('customer/quotation_list1');
	}

	function quotation_list()
	{	
		$user_role =$this->session->userdata['logged_in']['role'];
		$companyid=$this->uri->segment(3);
		$start_date=base64_decode($this->uri->segment(4));
		$end_date=base64_decode($this->uri->segment(5));
		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id', 'left');
		$this->db->join('customer_detail c','a.customer_id=c.id', 'left');
		if($user_role != 1) {
			$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		}
		if($companyid !='')
		{
			$this->db->where('a.customer_id',$companyid);
			$this->db->where('a.added_on BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html=$this->getproducts_detail($row->id);
			$j=1;


			if($row->lead_id==0)
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/quotation.php?quotation_id=".$row->id."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";
			}else
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$row->lead_id."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";

			}
			
			$query = $this->db->select('id')
					 		  ->from('order_punch')
					 		  ->where('quotation_id', $row->id)
					 		  ->get();

			if($query->num_rows() == 0) {


				$checkIfProductIsApproved = $this->master->checkIfProductIsApproved($row->id);
				if ($checkIfProductIsApproved == 0)
				{
				$generate_order = "<a href='".page_url."Leads/generate_order/".$row->id."' class='btn btn-success btn-xs'>Generate Order</a>";
			}else
			{
				$generate_order='<span style="color: red; font-weight: bold;font-size: 16px; text-align:center;">One or more products are not Approved/Rejected. Hence, Quotation cannot be sent</span>';
			}

				$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";

				$lead_data[] = array('sr_no'=>$i."<BR/>".$row->id,
				'create_date'=>date('d-m-Y', strtotime($row->added_on)),
				'company'=>$row->companyname,
				'company_name'=>$row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->address,
				'products'=>$html,	
				'quotation' => $view,
				'edit' => $edit,
				'generate_order' => $generate_order
				);
			$i++;
			}
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function quotation_history_list()
	{	
		$user_role =$this->session->userdata['logged_in']['role'];
		$companyid=$this->uri->segment(3);
		$start_date=base64_decode($this->uri->segment(4));
		$end_date=base64_decode($this->uri->segment(5));
		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id', 'left');
		$this->db->join('customer_detail c','a.customer_id=c.id', 'left');
		if($user_role != 1) {
			$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		}
		if($companyid !='')
		{
			$this->db->where('a.customer_id',$companyid);
			$this->db->where('a.added_on BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html=$this->getproducts_detail($row->id);
			$view = "<a href='".page_url."Customer/quotation_preview/".$row->id."' class='btn btn-success btn-xs'>View Quotation</a>";

			$query = $this->db->select('id')
					 		  ->from('order_punch')
					 		  ->where('quotation_id', $row->id)
					 		  ->get();

			if($query->num_rows() > 0) {
				$generate_order = '<strong style="color:green">Order Generated</strong>';


				//$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";

				$edit='CANNOT BE EDITED AS ORDER HAS BEEN GENERATED';

				$lead_data[] = array('sr_no'=>$i,
				'create_date'=>date('d-m-Y', strtotime($row->added_on)),
				'company'=>$row->companyname,
				'company_name'=>$row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->address,
				'products'=>$html,	
				'quotation' => $view,
				'edit' => $edit,
				'generate_order' => $generate_order
				);
				$i++;
			}
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function discount_approval_history() {
			$this->load->view('customer/discount_approval_history');
		}

		function discount_approval_history_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, a.flag, a.remarks, a.approve_reject_on, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price, f.first_name, f.last_name')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
						->where('a.flag !=', 0)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->flag == 1) {
						$flag = "APPROVED";
					} else if($row->flag == 2) {
						$flag = "REJECTED";
					} else {
						$flag = "";
					}

					if($row->approve_reject_on != '0000-00-00 00:00:00') {
						$approve_reject_on = date('d-m-Y H:i:s', strtotime($row->approve_reject_on));
					} else {
						$approve_reject_on = '';
					}


					$lead_data[] = array(
									'sr_no'=>$i,
									'company_name' => $row->companyname,
									'cust_company_name' => $row->company_name,
									'customer_name' => $row->customer_name,
									'product_name' => $row->instruments_name,
									'qty' => $row->qty,
									'allowed_price' => $row->discount_price,
									'list_price' => $row->list_price,
									'action' => $flag,
									'remarks' => '<strong style="color:red;">'.$row->remarks.'</strong>',
									'approve_reject_on' => $approve_reject_on,
									'approve_reject_by' => $row->first_name." ".$row->last_name
										);
					$i++;
				}
			}


			$sql = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, a.flag, a.remarks, a.approve_reject_on, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price, f.first_name, f.last_name')
							->from('lead_products a')
							->join('leads b', 'b.id=a.lead_id')
							->join('store_rack_location c', 'c.id=b.hpcl_company')
							->join('presto_instruments e', 'e.id=a.product_id')
							->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
							->where('a.flag !=', 0)
							->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->flag == 1) {
						$flag = "APPROVED";
					} else if($row->flag == 2) {
						$flag = "REJECTED";
					} else {
						$flag = "";
					}

					if($row->approve_reject_on != '0000-00-00 00:00:00') {
						$approve_reject_on = date('d-m-Y H:i:s', strtotime($row->approve_reject_on));
					} else {
						$approve_reject_on = '';
					}


					$lead_data[] = array(
									'sr_no'=>$i,
									'company_name' => $row->companyname,
									'cust_company_name' => $row->company_name,
									'customer_name' => $row->customer_name,
									'product_name' => $row->instruments_name,
									'qty' => $row->qty,
									'allowed_price' => $row->discount_price,
									'list_price' => $row->list_price,
									'action' => $flag,
									'remarks' => '<strong style="color:red;">'.$row->remarks.'</strong>',
									'approve_reject_on' => $approve_reject_on,
									'approve_reject_by' => $row->first_name." ".$row->last_name
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function checkIfDuplicateNoExists() {
		$company = $this->input->post('company');
		$mobile = $this->input->post('mobile');

		$sql = $this->db->select('id')
						->from('customer_detail')
						->where('company_id', $company)
						->where('contact_no', $mobile)
						->where('contact_no !=', '')
						->get();

		if($sql->num_rows() > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}

	function quotations_expiring_today() {
		$this->load->view('customer/quotations_expiring_today');
	}

	function quotations_expiring_today_list() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id');
		$this->db->join('customer_detail c','a.customer_id=c.id');
		$this->db->where('a.validity_date', $today_date);
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html=$this->getproducts_detail($row->id);
			$j=1;


			$extend_by_week = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 1)' style='margin-top: 20px;'>EXTEND BY WEEK</a>";
			$extend_by_half_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 2)' style='margin-top: 20px;'>EXTEND BY 15 DAYS</a>";
			$extend_by_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 3)' style='margin-top: 20px;'>EXTEND BY MONTH</a>";
			$extend_by_year = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 4)' style='margin-top: 20px;'>EXTEND BY YEAR</a>";


			

if($row->lead_id==0){
    $view = "<a href='".page_url."Customer/quotation_view/".$row->id."' class='btn btn-success btn-xs'>view Quotation</a>";
}else{
		$view = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$row->lead_id."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";
}



			$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";
			$notify_customers = "<input type='checkbox' name='send_notification[]' value='".$row->id."'><br>";

			$query1 = $this->db->select('id')
							   ->from('customer_notification_history')
							   ->where('quotation_id', $row->id)
							   ->get();

			if($query1->num_rows() > 0) {
				$notify_customers .= "<a href='javascript:;' class='btn btn-success btn-xs' onclick='getNotificationHistory(".$row->id.")'>View Notification History</a>";
			}

			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->added_on)),
			'company'=>$row->companyname,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'city'=>$row->city,
			'address'=>$row->address,
			'products'=>$html,	
			'quotation' => $view,
			'extend_by' => $extend_by_week.'<br>'.$extend_by_half_month.'<br>'.$extend_by_month.'<br>'.$extend_by_year,
			'notify_customers' => $notify_customers
			);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


		function quotations_expiring_today_user() {
		$this->load->view('customer/quotations_expiring_today_user');
	}

	function quotations_expiring_today_user_list() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id');
		$this->db->join('customer_detail c','a.customer_id=c.id');
		$this->db->where('a.validity_date', $today_date);
		$this->db->where('a.added_by', $_SESSION['logged_in']['user_id']);
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;
			$html.='<table class="table table-bordered"><thead><tr><th>sr_no</th><th>Product Name</th><th>Per pcak qty</th><th>List price</th><th>discount per liter</th><th>Net Price per ltr</th></tr></thead><tbody>';
			$res=$this->db->select('a.*,b.instruments_name,')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$row->id)->get();
			if($res->num_rows()>0)
			{
				foreach($res->result() as $product){
					$html.='<tr><td>'.$j.'</td>';
					$html.='<td>'.$product->instruments_name.'</td>';
					$html.='<td>'.$product->qty.'</td>';
					$html.='<td>'.$product->list_price.'</td>';
					$html.='<td>'.$product->discount_price.'</td>';
					$html.='<td>'.$product->net_price.'</td></tr>';

					$j++;
				}
				$html.='</tbody></table>';
			}

			$extend_by_week = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 1)' style='margin-top: 20px;'>EXTEND BY WEEK</a>";
			$extend_by_half_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 2)' style='margin-top: 20px;'>EXTEND BY 15 DAYS</a>";
			$extend_by_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 3)' style='margin-top: 20px;'>EXTEND BY MONTH</a>";
			$extend_by_year = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 4)' style='margin-top: 20px;'>EXTEND BY YEAR</a>";


			$view = "<a href='".page_url."Customer/quotation_view/".$row->id."' class='btn btn-success btn-xs'>view Quotation</a>";
			$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->added_on)),
			'company'=>$row->companyname,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'city'=>$row->city,
			'address'=>$row->address,
			'products'=>$html,	
			'quotation' => $view,
			'extend_by' => $extend_by_week.'<br>'.$extend_by_half_month.'<br>'.$extend_by_month.'<br>'.$extend_by_year
			);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function quotations_expiration_ext_user() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id');
		$this->db->join('customer_detail c','a.customer_id=c.id');
		$this->db->where('a.added_by', $_SESSION['logged_in']['user_id']);
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;
			$sql1=$this->db->select('increased_validity_by, old_validity_date, new_validity_date')
						  ->from('customer_quotation_validity')
						  ->where('quotation_id',$row->id)
						  ->get();

			if($sql1->num_rows()>0)
			{
			$html.='<table class="table table-bordered">
						<thead>
							<tr>
								<th>Sr No</th>
								<th>Increased Validity By</th>
								<th>Last Validity Date</th>
								<th>Increased Validity Date</th>
							</tr>
						</thead>
						<tbody>';

				foreach($sql1->result() as $row1) {

					if($row1->increased_validity_by == 1) {
						$increased_validity_by = 'A WEEK';
					} else if($row1->increased_validity_by == 2) {
						$increased_validity_by = '15 DAYS';
					} else if($row1->increased_validity_by == 3) {
						$increased_validity_by = 'A MONTH';
					} else if($row1->increased_validity_by == 4) {
						$increased_validity_by = '1 TIME';
					}

					$html.='<tr><td>'.$j.'</td>';
					$html.='<td>'.$increased_validity_by.'</td>';
					$html.='<td>'.date('d-m-Y', strtotime($row1->old_validity_date)).'</td>';
					$html.='<td>'.date('d-m-Y', strtotime($row1->new_validity_date)).'</td>';

					$j++;
				}
				$html.='</tbody></table>';
			}

			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->added_on)),
			'company'=>$row->companyname,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'city'=>$row->city,
			'address'=>$row->address,
			'products'=>$html
			);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

		function quotations_expiration_ext() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id');
		$this->db->join('customer_detail c','a.customer_id=c.id');
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;
			$sql1=$this->db->select('a.*, b.first_name, b.last_name')
						  ->from('customer_quotation_validity a')
						  ->join('system_users b', 'b.user_id=a.updated_by')
						  ->where('a.quotation_id',$row->id)
						  ->get();

			if($sql1->num_rows()>0)
			{
			$html.='<table class="table table-bordered">
						<thead>
							<tr>
								<th>Sr No</th>
								<th>Increased Validity By</th>
								<th>Last Validity Date</th>
								<th>Increased Validity Date</th>
								<th>Validity Extended On</th>
								<th>Validity Extended By</th>
							</tr>
						</thead>
						<tbody>';

				foreach($sql1->result() as $row1) {

					if($row1->increased_validity_by == 1) {
						$increased_validity_by = 'A WEEK';
					} else if($row1->increased_validity_by == 2) {
						$increased_validity_by = '15 DAYS';
					} else if($row1->increased_validity_by == 3) {
						$increased_validity_by = 'A MONTH';
					} else if($row1->increased_validity_by == 4) {
						$increased_validity_by = '1 TIME';
					}

					$html.='<tr><td>'.$j.'</td>';
					$html.='<td>'.$increased_validity_by.'</td>';
					$html.='<td>'.date('d-m-Y', strtotime($row1->old_validity_date)).'</td>';
					$html.='<td>'.date('d-m-Y', strtotime($row1->new_validity_date)).'</td>';
					$html.='<td>'.date('d-m-Y', strtotime($row1->updated_on)).'</td>';
					$html.='<td>'.$row1->first_name." ".$row1->last_name.'</td>';

					$j++;
				}
				$html.='</tbody></table>';
			}

			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->added_on)),
			'company'=>$row->companyname,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'city'=>$row->city,
			'address'=>$row->address,
			'products'=>$html
			);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function increase_validity() {
		$id = $this->uri->segment(3);
		$validity = $this->uri->segment(4);
		$today_date = date('Y-m-d H:i:s');

		$increased_date = '';

		$sql = $this->db->select('validity_date')
						->from('customer_quotation')
						->where('id', $id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
				$validity_date = $row->validity_date;

		  if($validity == 1) {
		    $increased_date = date('Y-m-d', strtotime($validity_date. ' + 7 days'));
		  } else if($validity == 2) {
		    $increased_date = date('Y-m-d', strtotime($validity_date. ' + 15 days'));
		  } else if ($validity == 3) {
		    $increased_date = date('Y-m-d', strtotime($validity_date. ' + 30 days'));
		  } else if ($validity == 4) {
		    $increased_date = date('Y-m-d', strtotime($validity_date. ' + 1 year'));
		  }
		}

		$data_log = array(
						 'quotation_id' => $id,
						 'increased_validity_by' => $validity,
						 'old_validity_date' => $validity_date,
						 'new_validity_date' => $increased_date,
						 'updated_on' => $today_date,
						 'updated_by' => $_SESSION['logged_in']['user_id']
						 );

		$this->db->insert('customer_quotation_validity', $data_log);

		$data = array(
					'validity_date' => $increased_date
					 );

		$this->db->where('id', $id)
				 ->update('customer_quotation', $data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Validity successfully increased.</div>');
		redirect(page_url.'Customer/quotations_expiring_today');
	}

		function rejected_quotations() {
			$this->load->view('customer/rejected_quotations');
		}

		function rejected_quotations_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, a.discount_price, a.net_price,a.flag, a.remarks, a.approve_reject_on, b.id, c.companyname, d.customer_name, d.company_name, e.instruments_name, f.first_name, f.last_name')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
						->where('a.flag', 2)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->flag == 1) {
						$flag = "APPROVED";
					} else if($row->flag == 2) {
						$flag = "REJECTED";
					} else {
						$flag = "";
					}

					$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."/1'><i class='fa fa-edit'></i></a>";

					if($row->approve_reject_on != '0000-00-00 00:00:00') {
						$approve_reject_on = date('d-m-Y H:i:s', strtotime($row->approve_reject_on));
					} else {
						$approve_reject_on = '';
					}

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'list_price' => $row->list_price,
										'discount_price' => $row->discount_price,
										'net_price' => $row->net_price,
										'action' => $flag,
										'remarks' => '<strong style="color:red;">'.$row->remarks.'</strong>',
										'edit' => $edit,
										'approve_reject_on' => $approve_reject_on,
										'approve_reject_by' => $row->first_name." ".$row->last_name
										);
					$i++;
				}
			}

			$sql1 = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, a.flag, a.remarks, a.approve_reject_on, b.id, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price, f.first_name, f.last_name')
							->from('lead_products a')
							->join('leads b', 'b.id=a.lead_id')
							->join('store_rack_location c', 'c.id=b.hpcl_company')
							->join('presto_instruments e', 'e.id=a.product_id')
							->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
							->where('a.flag', 2)
							->get();


						if($sql1->num_rows() > 0) {
				$i=1;
				foreach($sql1->result() as $row1) {
					if($row1->flag == 1) {
						$flag = "APPROVED";
					} else if($row1->flag == 2) {
						$flag = "REJECTED";
					} else {
						$flag = "";
					}

					$edit_lead = "<a href='".page_url."Customer/edit_lead_quote/".$row1->id."/1'><i class='fa fa-edit'></i></a>";

					if($row1->approve_reject_on != '0000-00-00 00:00:00') {
						$approve_reject_on = date('d-m-Y H:i:s', strtotime($row1->approve_reject_on));
					} else {
						$approve_reject_on = '';
					}

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row1->companyname,
										'cust_company_name' => $row1->company_name,
										'customer_name' => $row1->customer_name,
										'product_name' => $row1->instruments_name,
										'qty' => $row1->qty,
										'list_price' => $row1->list_price,
										'discount_price' => $row1->discount_price,
										'action' => $flag,
										'remarks' => '<strong style="color:red;">'.$row1->remarks.'</strong>',
										'edit' => $edit_lead,
										'approve_reject_on' => $approve_reject_on,
										'approve_reject_by' => $row1->first_name." ".$row1->last_name
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function generate_order() {
		$this->load->view('customer/generate_order');
	}

	function save_order_details() {

			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}
		$pic = $_FILES['upload_file']['name'];

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = '';
			 }

			$sql = $this->db->select('generated_order_id')
							->from('order_punch')
							->where('added_on>=',$start_date)
							->where('added_on<=',$end_date)
							->order_by('id', 'DESC')
							->limit(1)
							->get();

				if ($sql->num_rows() > 0) {
					foreach ($sql->result() as $row);	
						$generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
				} else {
						$generated_order_id = '001';
				}

				if($this->input->post('check_freight') == 1) {
					$freight = 1;
				} else {
					$freight = 0;
				}

		$data = array(
					 'quotation_id' => $this->uri->segment(3),
					 'source'=>$this->input->post('source'),
					 'agent'=>$this->input->post('agent'),
					 'invoice_no'=>$this->input->post('invoice_no'),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'upload_po' => $newname,
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'hpcl_billing_company'=>$this->input->post('hpcl_company'),
					 'freight' => $freight,
					 'freight_amount' => $this->input->post('freight_amt'),
					 'generated_order_id' => $generated_order_id,
					 'added_on' => date('Y-m-d H:i:s'),
					 'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		// echo "<pre>";print_r($data);exit;

		$this->db->insert('order_punch', $data);
		$order_id = $this->db->insert_id();

		$sql1 = $this->db->select('company_id, customer_id')
						 ->from('customer_quotation')
						 ->where('id', $this->uri->segment(3))
						 ->get();

		if($sql1->num_rows() > 0) {
			foreach ($sql1->result() as $row1);
				$company_id = $row1->company_id;
				$customer_id = $row1->customer_id;
		} else { 
				$company_id = '';
				$customer_id = '';
		}

		$sql3 = $this->db->select('address, contact_no, payment_type, credit_days')
						 ->from('customer_detail')
						 ->where('id', $customer_id)
						 ->get();

		if($sql3->num_rows() > 0) {
			foreach($sql3->result() as $row4);
				if($row4->address == '' || $row4->contact_no == '') {
					$data4 = array(
								   'customer_name' => $this->input->post('billing_name'),
								   'address' => $this->input->post('billing_address'),
								   'contact_no' => $this->input->post('billing_mobile_no'),
								   'state' => $this->input->post('billing_state'),
								   'email' => $this->input->post('billing_email'),
								   'city' => $this->input->post('billing_city'),
								   'pincode' => $this->input->post('billing_pincode')
								  );

					$this->db->where('id', $customer_id)
							 ->update('customer_detail', $data4);
				}

				if($row4->payment_type == 0) {
					$data5 = array(
									'payment_type' => $this->input->post('payment_type'),
					 				'credit_days' => $this->input->post('paymentterms')
								  );

					$this->db->where('id', $customer_id)
							 ->update('customer_detail', $data5);
				}
		}
			$datas = array(
						 'create_date' => date('Y-m-d'),
						 'order_id' => $order_id,
						 'company' => $company_id,
						 'customer' => $customer_id,
						 // 'total_amount' => $this->input->post('total_amount'),
						 'bank_name' => '',
						 'total_collection' => 0
						 );
	
			$this->db->insert('customer_cheque_collection', $datas);
			$collection_id = $this->db->insert_id();

			$datas1 = array(
						 'collection_id' => $collection_id,
						 'cheque_amt' => 0,
						 'cheque_bank_name' => ''
						 );

			$this->db->insert('customer_cheque_collection_details', $datas1);

			if($this->input->post('check_billing') == 1) {
				$check_billing = 1;
			} else {
				$check_billing = 0;
			}

		$data_m = array(
					 'order_id' => $order_id,
					 'ship_to' => $this->input->post('ship_to'),
					 'shipping_name' => $this->input->post('shipping_name'),
					 'shipping_address' => $this->input->post('shipping_address'),
					 'shipping_state' => $this->input->post('shipping_state'),
					 'shipping_city' => $this->input->post('shipping_city'),
					 'shipping_pincode' => $this->input->post('shipping_pincode'),
					 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
					 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
					 'shipping_email' => $this->input->post('shipping_email'),
					 'same_shipping_billing' => $check_billing,
					 'billing_name' => $this->input->post('billing_name'),
					 'billing_address' => $this->input->post('billing_address'),
					 'billing_state' => $this->input->post('billing_state'),
					 'billing_city' => $this->input->post('billing_city'),
					 'billing_pincode' => $this->input->post('billing_pincode'),
					 'billing_phone_no' => $this->input->post('billing_phone_no'),
					 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
					 'billing_email' => $this->input->post('billing_email')
					 );

		$this->db->insert('order_punch_mailing_details', $data_m);

		$data_t = array(
					 'order_id' => $order_id,
					 'msme_no' => $this->input->post('msme_no'),
					 'pan_no' => $this->input->post('pan_no'),
					 'registration_type' => $this->input->post('registration_type'),
					 'gst_no' => $this->input->post('gst_no')
					 );

		$this->db->insert('order_punch_tax_details', $data_t);

		$data_p = array(
					 'order_id' => $order_id,
					 'reference' => $this->input->post('reference'),
					 'note' => $this->input->post('note')
					 );

		$this->db->insert('order_punch_payment_details', $data_p);

	

		$upd_id=$this->input->post('editid');
		
		for($j=0 ;$j<count($upd_id);$j++) {
            $qty_old = $this->input->post('qtyedit'.$upd_id[$j]);
            $unit_old = $this->input->post('unit_id_edit'.$upd_id[$j]);
            $agreed_price_edit = $this->input->post('agreed_price_edit'.$upd_id[$j]);
            $batch_code_edit = $this->input->post('batch_code_edit'.$upd_id[$j]);


       		$data1 = array(
					'qty' => $qty_old,
					'pack_size' => $unit_old,
					'agreed_price' => $agreed_price_edit,
					'batch_code' => $batch_code_edit,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $this->session->userdata['logged_in']['user_id']
				);

       		$this->db->where('id',$upd_id[$j])
            		 ->update('customer_quotation_detail',$data1);
		}

			$competitor_product = $this->input->post('comp_product');
			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            $unit = $this->input->post('pack_size');
            $listprice = $this->input->post('listprice');
	        $discount = $this->input->post('discountprice');
	        $discountpricehide = $this->input->post('discountpricehide');
	        $netprice = $this->input->post('netprice');
	        $batch_code = $this->input->post('batch_code');

	       if($this->input->post('add_product') == 1) {
	       		for($i=0 ;$i<count($productname);$i++) {
	       			if($productname[$i] !='') {  

			 			$data2 = array(
							'quotation_id' => $this->uri->segment(3),
							'competitor_product' => $competitor_product[$i],
							'product_id' => $productname[$i],
							'qty' => $qty[$i],
							'pack_size' => $unit[$i],
							'list_price' => $listprice[$i],
							'agreed_price' => $listprice[$i],
							'discount_price' => $discount[$i],
							'net_price'=> $netprice[$i],
							'batch_code' => $batch_code[$i],
							'new_batch_code'=>1,
							'flag' => 1,
							'added_on' => date('Y-m-d H:i:s'),
							'added_by' => $this->session->userdata['logged_in']['user_id']
						);

							// echo "<pre>";print_r($data2);exit;
			            	$this->db->insert('customer_quotation_detail',$data2);
		       		}
		    	}
	    	}

				$resteye= $this->db->select('email,gst,pan,ship_address,ship_pincode,ship_email,ship_state,ship_city,bill_address,bill_state,bill_city,bill_pincode,bill_email,msme_number')->from('customer_detail')->where('id',$customer_id)->get();
				if($resteye->num_rows()>0)
				{
					foreach($resteye->result() as $curow);
					if($curow->email=='' || $curow->email==0 || $curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->gst=='' || $curow->gst==0)
					{
						$darray=array('gst'=>$this->input->post('gst_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->pan=='' || $curow->pan==0)
					{
						$darray=array('pan'=>$this->input->post('pan_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_address=='' || $curow->ship_address==0)
					{
						$darray=array('ship_address'=>$this->input->post('shipping_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_pincode=='' || $curow->ship_pincode==0)
					{
						$darray=array('ship_pincode'=>$this->input->post('shipping_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_email=='' || $curow->ship_email==0)
					{
						$darray=array('ship_email'=>$this->input->post('shipping_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_state=='' || $curow->ship_state==0)
					{
						$darray=array('ship_state'=>$this->input->post('shipping_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_city=='' || $curow->ship_city==0)
					{
						$darray=array('ship_city'=>$this->input->post('shipping_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->bill_address=='' || $curow->bill_address==0)
					{
						$darray=array('bill_address'=>$this->input->post('billing_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_state=='' || $curow->bill_state==0)
					{
						$darray=array('bill_state'=>$this->input->post('billing_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_city=='' || $curow->bill_city==0)
					{
						$darray=array('bill_city'=>$this->input->post('billing_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_pincode=='' || $curow->bill_pincode==0)
					{
						$darray=array('bill_pincode'=>$this->input->post('billing_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('bill_email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->msme_number=='' || $curow->msme_number==0)
					{
						$darray=array('msme_number'=>$this->input->post('msme_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}
				}

		$data_upd = array(
						 'order_punch' => 1
						 );

		$this->db->where('id', $this->uri->segment(3))
				 ->update('customer_quotation', $data_upd);



		
		$ptype=$this->input->post('payment_type');
		if($ptype==4)
		{

				$credit_days = $this->input->post('paymentterms');
				$expected_pdc_date = date('Y-m-d', strtotime('+'.$credit_days.' days'));
				$original_cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
				$pdcrecv = $this->input->post('pdcrecv');

				if($pdcrecv==1)
				{
					$hold_due_to_pdc=0;
					if(strtotime($original_cheque_date)>strtotime($expected_pdc_date))
					{
						$hold_due_to_pdc=1;
					}

					$recieved=1;

					$cheque_no=$this->input->post('cheque_no');

				}else
				{
					$recieved=0;
					$hold_due_to_pdc=0;
					$cheque_no='';
				}

				$data_chq = array(
							'order_id' => $order_id,
							'customer_id' => $cust_id,
							'expected_pdc_date' => $original_cheque_date,
							'cheque_no'=>$cheque_no,
							'received' => $recieved,
							'deposited' => 0,
							'order_punch_date' =>  date('Y-m-d H:i:s'),
							'hold_due_to_pdc'=>$hold_due_to_pdc
						);

				$this->db->insert('customer_cheque_details', $data_chq);
		
		}


		if($ptype==6)
		{
			$cheque_no=$this->input->post('cheque_no');
			$cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
			$order_amount = $this->salescrm->getOrderAmountWithGST($quotation_id, $this->input->post('gst_no'), $seller_gst);

			$data2 = array(
						   'customer_id' => $cust_id,
						   'type' => 2,
						   'bills' => "'$order_id'",
						   'addedOn' => date('Y-m-d H:i:s'),
						   'addedBy' => $this->session->userdata['logged_in']['user_id']
						  );

			$this->db->insert('customer_payments', $data2);
			$payment_id = $this->db->insert_id();

			$data3 = array(
						   'payment_id' => $payment_id,
						   'payment_type' => 1,
						   'cheque_no' => $cheque_no,
						   'cheque_date' => $cheque_date,
						   'amount' => $order_amount
						  );

			$this->db->insert('customer_payment_particulars', $data3);

			$data_chq = array(
					'order_id' => $order_id,
					'customer_id' => $cust_id,
					'expected_pdc_date' => $cheque_date,
					'cheque_no'=>$cheque_no,
					'received' => 1,
					'deposited' => 0,
					'order_punch_date' =>  date('Y-m-d H:i:s'),
					'hold_due_to_pdc' => 0
					);

			
				$this->db->insert('customer_cheque_details', $data_chq);
		
		}

				$data_upd = array(
				'order_punch' => 1
				);

				$this->db->where('id', $this->uri->segment(3))
				->update('customer_quotation', $data_upd);



		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order successfully created.</div>');
		redirect(page_url.'Leads/pending_for_order_punching');
	}

	function all_orders() {
		$this->load->view('customer/all_orders');
	}

	function all_orders_list() {

		$start_date = $this->uri->segment(3)." 00:00:00";
		$end_date = $this->uri->segment(4)." 00:00:00";
		$company = $this->uri->segment(5);
		$customer = $this->uri->segment(6);
		$product = $this->uri->segment(7);



		$lead_data = array();
		 $this->db->select('a.cancelled,a.billing,a.send_to_tally,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id');
						 	if($start_date != '' && $end_date != '') {
							$this->db->where('a.added_on>=', $start_date);
							$this->db->where('a.added_on<=', $end_date);
							}
							if($company<>'ALL' && $company<>'')
							{
							$this->db->where('a.hpcl_billing_company',$company);
							}

							if($customer<>'ALL' && $customer<>'')
							{
							$this->db->where('b.customer_id',$customer);
							}
						 	$query=$this->db->order_by('a.added_on','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

					// if($row->payment_type == 1) {
					// 	$payment_type = 'Cheque';
					// 	$th_fields = "<th>Cheque No</th>";
					// 	$td_fields = "<td>".$row->cheque_no."</td>";
					// } else if($row->payment_type == 2) {
					// 	$payment_type = 'Cash';
					// 	$th_fields = "";
					// 	$td_fields = "";
					// } else if($row->payment_type == 3) {
					// 	$payment_type = 'NEFT';
					// 	$th_fields = "<th>UTR No</th>";
					// 	$td_fields = "<td>".$row->utr_no."</td>";
					// }  else if($row->payment_type == 4) {
					// 	$payment_type = 'PDC';
					// 	$th_fields = "<th>Cheque No</th><th>PDC Date</th>";
					// 	$td_fields = "<td>".$row->cheque_no."</td><td>".date('d-m-Y', strtotime($row->pdc_date))."</td>";

					// }

					// $html=$this->getproducts_detail($row->quotation_id);
					$html=$this->getproducts_detailNew($row->quotation_id,$product);
					$h=explode('~',$html);

					$shipstate=$this->getstate($row->shipping_state);
					$billstate=$this->getstate($row->billing_state);
					$j=1;

					$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


					$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

					
					if($row->send_to_tally==0)
					{

					$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
					if($_SESSION['logged_in']['role']==1)
					{
					$edit.=" | "."<a href='javascript:;' onclick='delete_order(".$row->id.", ".$row->quotation_id.")'><i class='fa fa-trash'></i></a>";
					}else
					{
						
					}

					}else
					{
						$edit='<strong style="color:red;font-weight:bold;">Billing has been done. Cannot be removed or edited</strong>';
					}
					$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
					$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";
					$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

					if($row->payment_type == 2) {
						$payment_type = 'Cash';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.= '';
					} else if($row->payment_type == 3) {
						$payment_type = 'Online';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms .= '';
					} else if($row->payment_type == 4) {
						$payment_type = 'PDC';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
						$payment_type = 'CREDIT';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
						$payment_type = 'ADVANCE';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						
					} else {
						$payment_type = '';
						$payment_terms="";
						$payment_terms .= '';
					}

					$quotation = "<a href='".page_url."Customer/quotation_preview/".$row->quotation_id."' class='btn btn-success btn-xs' target='_blank'>Preview Quotation</a>";
					
					if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
					if($h[1]>0)
			{

					if($row->billing==1)
					{
						$s="<span class='btn btn-xs btn-success'>Billed</span>";
					}else if($row->cancelled==1)
					{
						$s="<span class='btn btn-xs btn-warning'>Cancelled</span>";
					}else
					{
						$s="<span class='btn btn-xs btn-danger'>Billing Pending</span>";
					}
					$lead_data[] = array('sr_no'=>$i,
						'source'=>$row->lead_source, 
						'status'=>$s,
						'agent'=>$row->first_name." ".$row->last_name,
										 'company_name'=>$row->companyname,
										 'cust_company_name'=>$row->company_name,
										 'customer_name'=>$row->customer_name,
										 'taxdetail'=>$taxdetail,
										 'products'=>$h[0],
										 'shipaddress' =>$shipdetail,
								 		 'billingaddress' =>$billdetail,
								 		 'payment_terms' =>$payment_terms,
								 		 'po_details' =>$po_details,
										 'edit' => $edit,
										 'order_details' => $order_details,
										 'payment_collection' => $payment_collection,
										 'quotation' => $quotation
										);
					$i++;
				}
				}
			}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function all_orders_user() {
		$this->load->view('customer/all_orders_user');
	}

	function all_orders_user_list() {
		$lead_data = array();
		$query = $this->db->select('a.send_to_tally,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, b.id as quotation_id, c.companyname, d.customer_name')
						  ->from('order_punch a')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('b.order_punch', 1)
						  ->where('b.added_by', $this->session->userdata['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;

			if($row->payment_type == 1) {
				$payment_type = 'Cheque';
				$th_fields = "<th>Cheque No</th>";
				$td_fields = "<td>".$row->cheque_no."</td>";
			} else if($row->payment_type == 2) {
				$payment_type = 'Cash';
				$th_fields = "";
				$td_fields = "";
			} else if($row->payment_type == 3) {
				$payment_type = 'NEFT';
				$th_fields = "<th>UTR No</th>";
				$td_fields = "<td>".$row->utr_no."</td>";
			}  else if($row->payment_type == 4) {
				$payment_type = 'PDC';
				$th_fields = "<th>Cheque No</th><th>PDC Date</th>";
				$td_fields = "<td>".$row->cheque_no."</td><td>".date('d-m-Y', strtotime($row->pdc_date))."</td>";

			}   else {
				$payment_type = '';
				$th_fields = '';
				$td_fields = '';

			}


			$html.='<table class="table table-bordered">
						<thead>
							<tr>
								<th>Payment Type</th>
								'.$th_fields.'
							</tr>
						</thead>
					<tbody>';
			$html.='<tr>
						<td>'.$payment_type.'</td>'.$td_fields.
						'</tr>';
			$html.='</tbody></table>';

			// $res=$this->db->select('a.*,b.instruments_name,')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$row->id)->get();
			// if($res->num_rows()>0)
			// {
			// 	foreach($res->result() as $product){
			// 		$html.='<tr><td>'.$j.'</td>';
			// 		$html.='<td>'.$product->instruments_name.'</td>';
			// 		$html.='<td>'.$product->qty.'</td>';
			// 		$html.='<td>'.$product->list_price.'</td>';
			// 		$html.='<td>'.$product->discount_price.'</td>';
			// 		$html.='<td>'.$product->net_price.'</td></tr>';

			// 		$j++;
			// 	}
			// 	$html.='</tbody></table>';
			// }

			if($row->send_to_tally==0)
					{
					$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>"." | "."<a href='javascript:;' onclick='delete_order(".$row->id.", ".$row->quotation_id.")'><i class='fa fa-trash'></i></a>";
					}else
					{
						$edit='<strong style="color:red;font-weight:bold;">Billing has been done. Cannot be removed or edited</strong>';
					}
			//$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

			$lead_data[] = array('sr_no'=>$i,
			'company_name'=>$row->companyname,
			'customer_name'=>$row->customer_name,
			'products'=>$html,
			'edit' => $edit
			);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function edit_order() {
		$this->load->view('customer/edit_order');
	}


	function edit_order_from_cancell() {
		$this->load->view('customer/edit_order_cancelled');
	}


	function view_audit() {
		$this->load->view('customer/view_audit');
	}


	// 	public function add_direct_order() {
	// 	$user_id =$this->session->userdata['logged_in']['user_id'];	
	// 	date_default_timezone_set("Asia/Kolkata");
	// 	$date =  date('Y-m-d H:i:s'); 

	// 	$company_id = $this->input->post('company');
	// 	$unique_no=$this->getinvoice_no($company_id);
		
		
	// 	$customer_id = $this->input->post('customer');

	// 	$data = array(
	// 		'company_id' => $company_id,
	// 		'customer_id' => $customer_id,
	// 		'validity_date' => date('Y-m-d', strtotime('+10 days')),
	// 		'ref_id' => $unique_no,
	// 		'check_terms' => 2,
	// 		'general_terms' => $this->input->post('drums_tnc'),
	// 		'bulk_terms' => $this->input->post('bulk_tnc'),
	// 		'added_on' => $date,
	// 		'added_by' => $user_id
	// 		);
	// 	$result  = $this->db->insert('customer_quotation',$data);
	// 	$insert_id = $this->db->insert_id();

	// 	if($result > 0) {
	// 		$productname = $this->input->post('product');
	// 		$comp_product = $this->input->post('comp_product');
 //            $qty = $this->input->post('qty');
 //            $packsize = $this->input->post('pack_size');
 //            $listprice = $this->input->post('listprice');
 //            $batch_code = $this->input->post('batch_code');
	//         $discount = $this->input->post('discountprice');
	//         $discountpricehide = $this->input->post('discountpricehide');
	//         $netprice = $this->input->post('netprice');

 //       		for($i=0 ;$i<count($productname);$i++){
       			  
	//  			$data1 = array(
	// 					'quotation_id' => $insert_id,
	// 					'competitor_product' => $comp_product[$i],
	// 					'product_id' => $productname[$i],
	// 					'qty' => $qty[$i],
	// 					'pack_size' => $packsize[$i],
	// 					'list_price' => $listprice[$i],
	// 					'agreed_price' => $listprice[$i],
	// 					'batch_code' => $batch_code[$i],
	// 					'discount_price' => $discount[$i],
	// 					'net_price' => $netprice[$i],
	// 					'flag' => 1,
	// 					'added_on' => date('Y-m-d H:i:s'),
	// 					'added_by' => $user_id
	// 			);
	//             $this->db->insert('customer_quotation_detail',$data1);
	//     	}
	// 	}

	// 	 // echo "<pre>";print_r($data);exit;
	// 		$financial=$this->salescrm->get_finacial_year_range();
	// 		if(count($financial)>0)
	// 		{
	// 			$start_date=$financial['start_date']." 00:00:00";
	// 			$end_date=$financial['end_date']." 23:59:59";
	// 		}else
	// 		{
	// 			$start_date=date('Y-04-01')." 00:00:00";
	// 			$end_date=date('Y-m-d')." 23:59:59";
	// 		}
			
	// 		$pic = $_FILES['upload_file']['name'];

	// 		  if($pic <> '') {
	// 			$files = explode('.', $pic);
	// 			$ext = end($files);
	// 			$newname = time().'.'.$ext;
	// 			move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
	// 		 } else {
	// 		 	$newname = '';
	// 		 }

	// 		$sql = $this->db->select('generated_order_id')
	// 						->from('order_punch')
	// 						->where('added_on>=',$start_date)
	// 						->where('added_on<=',$end_date)
	// 						->order_by('id', 'DESC')
	// 						->limit(1)
	// 						->get();

	// 			if ($sql->num_rows() > 0) {
	// 				foreach ($sql->result() as $row);	
	// 					$generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
	// 			} else {
	// 					$generated_order_id = '001';
	// 			}

	// 			if($this->input->post('check_freight') == 1) {
	// 				$freight = 1;
	// 			} else {
	// 				$freight = 0;
	// 			}

	// 	$data2 = array(
	// 				 'quotation_id' => $insert_id,
	// 				 'source'=>$this->input->post('source'),
	// 				 'agent'=>$this->input->post('agent'),
	// 				 'invoice_no' => $this->input->post('invoice_no'),
	// 				 'direct_order' => 1,
	// 				 'upload_po' => $newname,
	// 				 'po_no' => $this->input->post('po_no'),
	// 				 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
	// 				 'payment_type' => $this->input->post('payment_type'),
	// 				 'credit_days' => $this->input->post('paymentterms'),
	// 				 'hpcl_billing_company' => $this->input->post('company'),
	// 				 'freight' => $freight,
	// 				 'freight_amount' => $this->input->post('freight_amt'),
	// 				 'generated_order_id' => $generated_order_id,
	// 				 'added_on' => date('Y-m-d H:i:s'),
	// 				 'added_by' => $this->session->userdata['logged_in']['user_id']
	// 				 );

	// 	// echo "<pre>";print_r($data);exit;

	// 	$this->db->insert('order_punch', $data2);
	// 	$order_id = $this->db->insert_id();


	// 	$sql3 = $this->db->select('address, contact_no')
	// 					 ->from('customer_detail')
	// 					 ->where('id', $customer_id)
	// 					 ->get();

	// 	// if($sql3->num_rows() > 0) {
	// 	// 	foreach($sql3->result() as $row4);
	// 	// 		if($row4->address == '' || $row4->contact_no == '') {
	// 	// 			$data3 = array(
								  
	// 	// 						   'address' => $this->input->post('billing_address'),
	// 	// 						   'contact_no' => $this->input->post('billing_mobile_no'),
	// 	// 						   'state' => $this->input->post('billing_state'),
	// 	// 						   'email' => $this->input->post('billing_email'),
	// 	// 						   'city' => $this->input->post('billing_city'),
	// 	// 						   'pincode' => $this->input->post('billing_pincode')
	// 	// 						  );

	// 	// 			$this->db->where('id', $customer_id)
	// 	// 					 ->update('customer_detail', $data3);
	// 	// 		}
	// 	// }

	// 		$data4 = array(
	// 					 'create_date' => date('Y-m-d'),
	// 					 'order_id' => $order_id,
	// 					 'company' => $company_id,
	// 					 'customer' => $customer_id,
	// 					 // 'total_amount' => $this->input->post('total_amount'),
	// 					 'bank_name' => '',
	// 					 'total_collection' => 0
	// 					 );
	
	// 		$this->db->insert('customer_cheque_collection', $data4);
	// 		$collection_id = $this->db->insert_id();

	// 		$datas5 = array(
	// 					 'collection_id' => $collection_id,
	// 					 'cheque_amt' => 0,
	// 					 'cheque_bank_name' => ''
	// 					 );

	// 		$this->db->insert('customer_cheque_collection_details', $datas5);

	// 		if($this->input->post('check_billing') == 1) {
	// 			$check_billing = 1;
	// 		} else {
	// 			$check_billing = 0;
	// 		}

	// 	$data_m = array(
	// 				 'order_id' => $order_id,
	// 				 'ship_to' => $this->input->post('ship_to'),
	// 				 'shipping_address' => $this->input->post('shipping_address'),
	// 				 'shipping_state' => $this->input->post('shipping_state'),
	// 				 'shipping_city' => $this->input->post('shipping_city'),
	// 				 'shipping_pincode' => $this->input->post('shipping_pincode'),
	// 				 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
	// 				 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
	// 				 'shipping_email' => $this->input->post('shipping_email'),
	// 				 'same_shipping_billing' => $check_billing,
	// 				 'bill_to' => $this->input->post('bill_to'),
	// 				 'billing_name' => $this->input->post('billing_name'),
	// 				 'billing_address' => $this->input->post('billing_address'),
	// 				 'billing_state' => $this->input->post('billing_state'),
	// 				 'billing_city' => $this->input->post('billing_city'),
	// 				 'billing_pincode' => $this->input->post('billing_pincode'),
	// 				 'billing_phone_no' => $this->input->post('billing_phone_no'),
	// 				 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
	// 				 'billing_email' => $this->input->post('billing_email')
	// 				 );

	// 	$this->db->insert('order_punch_mailing_details', $data_m);

	// 	$data_t = array(
	// 				 'order_id' => $order_id,
	// 				 'msme_no' => $this->input->post('msme_no'),
	// 				 'pan_no' => $this->input->post('pan_no'),
	// 				 'registration_type' => $this->input->post('registration_type'),
	// 				 'gst_no' => $this->input->post('gst_no')
	// 				 );

	// 	$this->db->insert('order_punch_tax_details', $data_t);

	// 	$data_p = array(
	// 				 'order_id' => $order_id,
	// 				 'reference' => $this->input->post('reference'),
	// 				 'note' => $this->input->post('note')
	// 				 );

	// 	$this->db->insert('order_punch_payment_details', $data_p);

	// 	$data_upd = array(
	// 					 'order_punch' => 1
	// 					 );

	// 	$this->db->where('id', $insert_id)
	// 			 ->update('customer_quotation', $data_upd);


	// 			 /** CHECK IF CUSTOMER DETAIL IS EMPTY THEN UPDATE THE DATA **/

	// 			$resteye= $this->db->select('email,gst,pan,ship_address,ship_pincode,ship_email,ship_state,ship_city,bill_address,bill_state,bill_city,bill_pincode,bill_email,msme_number')->from('customer_detail')->where('id',$customer_id)->get();
	// 			if($resteye->num_rows()>0)
	// 			{
	// 				foreach($resteye->result() as $curow);
	// 				if($curow->email=='' || $curow->email==0 || $curow->bill_email=='' || $curow->bill_email==0)
	// 				{
	// 					$darray=array('email'=>$this->input->post('billing_email'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->gst=='' || $curow->gst==0)
	// 				{
	// 					$darray=array('gst'=>$this->input->post('gst_no'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}


	// 				if($curow->pan=='' || $curow->pan==0)
	// 				{
	// 					$darray=array('pan'=>$this->input->post('pan_no'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->ship_address=='' || $curow->ship_address==0)
	// 				{
	// 					$darray=array('ship_address'=>$this->input->post('shipping_address'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->ship_pincode=='' || $curow->ship_pincode==0)
	// 				{
	// 					$darray=array('ship_pincode'=>$this->input->post('shipping_pincode'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->ship_email=='' || $curow->ship_email==0)
	// 				{
	// 					$darray=array('ship_email'=>$this->input->post('shipping_email'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->ship_state=='' || $curow->ship_state==0)
	// 				{
	// 					$darray=array('ship_state'=>$this->input->post('shipping_state'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->ship_city=='' || $curow->ship_city==0)
	// 				{
	// 					$darray=array('ship_city'=>$this->input->post('shipping_city'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}


	// 				if($curow->bill_address=='' || $curow->bill_address==0)
	// 				{
	// 					$darray=array('bill_address'=>$this->input->post('billing_address'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->bill_state=='' || $curow->bill_state==0)
	// 				{
	// 					$darray=array('bill_state'=>$this->input->post('billing_state'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->bill_city=='' || $curow->bill_city==0)
	// 				{
	// 					$darray=array('bill_city'=>$this->input->post('billing_city'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->bill_pincode=='' || $curow->bill_pincode==0)
	// 				{
	// 					$darray=array('bill_pincode'=>$this->input->post('billing_pincode'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->bill_email=='' || $curow->bill_email==0)
	// 				{
	// 					$darray=array('bill_email'=>$this->input->post('billing_email'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}

	// 				if($curow->msme_number=='' || $curow->msme_number==0)
	// 				{
	// 					$darray=array('msme_number'=>$this->input->post('msme_no'));
	// 					$this->db->where('id',$this->input->post('customer'));
	// 					$this->db->update('customer_detail',$darray);
	// 				}
	// 			}

	// 			 /** END **/

	// 	$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order Successfully Generated.</div>');
	// 		// redirect(page_url.'Customer/discount_approval/'.$insert_id);
	// 		redirect(page_url.'Customer/direct_order_form');
	// }

	function update_order_details() {
		$pic = $_FILES['upload_file']['name'];
		$user_id = $this->session->userdata['logged_in']['user_id'];	
		$add_new = $this->input->post('add_new');
		$upd_id = $this->input->post('edit_product_id');
		$qty_edit = $this->input->post('qty_edit');
        $listprice_edit = $this->input->post('listprice_edit');
        $discountpricehide_edit = $this->input->post('discountpricehideedit');
        $batch_code_edit = $this->input->post('batch_code_edit');

        $proceed_flag=array();
        $proceed_flag[]=0;

		if($add_new == 1) {
		$productname_for_check = $this->input->post('product');
		$qty_for_check = $this->input->post('qty');
		$for_new_product=$this->salescrm->checkforavailable_Company_QTY($productname_for_check,$qty_for_check,$this->input->post('company'));
		$new_data=explode('~',$for_new_product);
		$proceed_flag[]=$new_data[0];
		}

	if(array_sum($proceed_flag)==0)
			{

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = $this->input->post('old_upload_file');
			 }

			// echo $newname;exit;

			$data1 = array(
						'customer_id' => $this->input->post('customer'),
						'company_id'=>$this->input->post('company')
						);

			// echo "<pre>";print_r($data1);exit;

		   $this->db->where('id', $this->uri->segment(4))
					->update('customer_quotation',$data1);


       		for($i=0 ;$i<count($upd_id);$i++){
	
       			if($discountpricehide_edit[$i]>$listprice_edit[$i])
       			{
       				$flag=0;
       			}else
       			{
       				$flag=1;
       			}

			       			  
	 			$data2 = array(
							'qty' => $qty_edit[$i],
							'agreed_price' => $listprice_edit[$i],
							 'batch_code' => $batch_code_edit[$i],
							'flag'=>$flag
							);

	            $this->db->where('id', $upd_id[$i])
						 ->update('customer_quotation_detail',$data2);
	    	}

			if($add_new == 1) {
				$productname = $this->input->post('product');
				$comp_product = $this->input->post('comp_product');
	            $qty = $this->input->post('qty');
	            $packsize = $this->input->post('pack_size');
	            $listprice = $this->input->post('listprice');
	            $batch_code = $this->input->post('batch_code');
		        $discount = $this->input->post('discountprice');
		        $discountpricehide = $this->input->post('discountpricehide');
		        $netprice = $this->input->post('netprice');

	       		for($j=0 ;$j<count($productname);$j++){
	       			  
	       			  if($discountpricehide[$j]>$listprice[$j])
	       			  {
	       			  	$flag=0;
	       			  }else
	       			  {
	       			  	$flag=1;
	       			  }
		 			$data3 = array(
							'quotation_id' => $this->uri->segment(4),
							'competitor_product' => $comp_product[$j],
							'product_id' => $productname[$j],
							'qty' => $qty[$j],
							'pack_size' => $packsize[$j],
							'list_price' => $listprice[$j],
							'agreed_price' => $listprice[$j],
							'batch_code' => $batch_code[$j],
							'discount_price' => $discount[$j],
							'net_price' => $netprice[$j],
							'flag' => $flag,
							'new_batch_code'=>1,
							'added_on' => date('Y-m-d H:i:s'),
							'added_by' => $user_id
					);
		            $this->db->insert('customer_quotation_detail', $data3);
		    	}
			}



			$old_company_hidden = $this->input->post('old_company_hidden');
			$company_id = $this->input->post('company');
			// if($old_company_hidden!=$company_id)
			// {
			// $unique_no=$this->getinvoice_no_new($this->input->post('company'));

			// $dd=array('invoice_no'=>$unique_no);
			// $this->db->where('id', $this->uri->segment(3))
			// ->update('order_punch', $dd);

			// }



		$data = array(
				'hpcl_billing_company'=>$this->input->post('company'),
					 'source'=>$this->input->post('source'),
					 'agent'=>$this->input->post('agent'),
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'upload_po' => $newname
					 );

		$this->db->where('id', $this->uri->segment(3))
				 ->update('order_punch', $data);

		if($this->input->post('check_billing') == 1) {
			$check_billing = 1;
		} else {
			$check_billing = 0;
		}

		$data_m = array(
					 'ship_to' => $this->input->post('ship_to'),
					 'shipping_name' => $this->input->post('shipping_name'),
					 'shipping_address' => $this->input->post('shipping_address'),
					 'shipping_state' => $this->input->post('shipping_state'),
					 'shipping_city' => $this->input->post('shipping_city'),
					 'shipping_pincode' => $this->input->post('shipping_pincode'),
					 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
					 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
					 'shipping_email' => $this->input->post('shipping_email'),
					 'same_shipping_billing' => $check_billing,
					 'bill_to' => $this->input->post('bill_to'),
					 'billing_name' => $this->input->post('billing_name'),
					 'billing_address' => $this->input->post('billing_address'),
					 'billing_state' => $this->input->post('billing_state'),
					 'billing_city' => $this->input->post('billing_city'),
					 'billing_pincode' => $this->input->post('billing_pincode'),
					 'billing_phone_no' => $this->input->post('billing_phone_no'),
					 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
					 'billing_email' => $this->input->post('billing_email')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_mailing_details', $data_m);

		$data_t = array(
					 'msme_no' => $this->input->post('msme_no'),
					 'pan_no' => $this->input->post('pan_no'),
					 'registration_type' => $this->input->post('registration_type'),
					 'gst_no' => $this->input->post('gst_no')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_tax_details', $data_t);

		$data_p = array(
					 'reference' => $this->input->post('reference'),
					 'note' => $this->input->post('note')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_payment_details', $data_p);


		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order successfully updated.</div>');
		redirect(page_url.'Billing/order_pending_for_billing_so');
	}else
	{
		echo "<strong style='color:red;font-weight:bold;'>REQUIRED STOCK IS NOT AVAIABLE FOR SOME ITEMS. DATA CANNOT BE EDITED</strong>"; exit;
	}

	}

		function rejected_quotations_user() {
			$this->load->view('customer/rejected_quotations_user');
		}

		function rejected_quotations_user_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, a.discount_price, a.net_price,a.flag, a.remarks,b.id, c.companyname, d.customer_name, e.instruments_name')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('b.added_by', $_SESSION['logged_in']['user_id'])
						->where('a.flag', 2)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->flag == 1) {
						$flag = "APPROVED";
					} else if($row->flag == 2) {
						$flag = "REJECTED";
					} else {
						$flag = "";
					}

					$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."/1'><i class='fa fa-edit'></i></a>";

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->companyname,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'list_price' => $row->list_price,
										'discount_price' => $row->discount_price,
										'net_price' => $row->net_price,
										'action' => $flag,
										'remarks' => $row->remarks,
										'edit' => $edit
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

		function pending_for_ppc_clearance() {
			$this->load->view('customer/pending_for_ppc_clearance');
		}

		function pending_for_ppc_clearance_list() {
		$cur_date = date('Y-m-d');

		$lead_data = array();
		$sql = $this->db->select('a.id, a.cheque_no, a.pdc_date, c.companyname, d.customer_name, e.id as collection_id')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id', 'left')
						->join('customer_detail d', 'd.id=b.customer_id', 'left')
						->join('customer_cheque_collection e', 'e.order_id=a.id')
						->where('a.checked', 0)
						->where('a.payment_type', 4)
						->where('a.pdc_date <=', $cur_date)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$checked = "<input type='checkbox' onchange='mark_checked(".$row->id.")'>";

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date <=', $cur_date)
									  ->get();

					if($query->num_rows() > 0) {

						$lead_data[] = array(
											'sr_no'=>$i,
											'company_name' => $row->companyname,
											'customer_name' => $row->customer_name,
											'cheque_no' => $row->cheque_no,
											'pdc_date' => date('d-m-Y', strtotime($row->pdc_date)),
											'deposited' => $checked
											);
					}
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function cheque_deposited() {
		$order_id = $this->input->post('id');
		$data = array('checked' => 1);

		$this->db->where('id', $order_id)
				 ->update('order_punch', $data);

	}

		function pending_for_ppc_clearance_user() {
			$this->load->view('customer/pending_for_ppc_clearance_user');
		}

		function pending_for_ppc_clearance_user_list() {
		$cur_date = date('Y-m-d');
		$user_id=$_SESSION['logged_in']['user_id'];
		$lead_data = array();
		$sql = $this->db->select('a.id, a.cheque_no, a.pdc_date, c.companyname, d.customer_name, e.id as collection_id')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id', 'left')
						->join('customer_detail d', 'd.id=b.customer_id', 'left')
						->join('customer_cheque_collection e', 'e.order_id=a.id')
						->where('a.checked', 0)
						->where('a.payment_type', 4)
						->where('a.pdc_date <=', $cur_date)
						->where('a.added_by', $user_id)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$checked = "<input type='checkbox' onchange='mark_checked(".$row->id.")'>";

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date <=', $cur_date)
									  ->get();

					if($query->num_rows() > 0) {

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->companyname,
										'customer_name' => $row->customer_name,
										'cheque_no' => $row->cheque_no,
										'pdc_date' => date('d-m-Y', strtotime($row->pdc_date)),
										'deposited' => $checked
										);
					}
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}


		function pending_for_ppc_clearance_today() {
			$this->load->view('customer/pending_for_ppc_clearance_today');
		}

		function pending_for_ppc_clearance_today_list() {
		$cur_date = date('Y-m-d');

		$lead_data = array();
		$sql = $this->db->select('a.id, a.cheque_no, a.pdc_date, c.companyname, d.customer_name, e.id as collection_id')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id', 'left')
						->join('customer_detail d', 'd.id=b.customer_id', 'left')
						->join('customer_cheque_collection e', 'e.order_id=a.id')
						->where('a.checked', 0)
						->where('a.payment_type', 4)
						->where('a.pdc_date', $cur_date)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$checked = "<input type='checkbox' onchange='mark_checked(".$row->id.")'>";

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date', $cur_date)
									  ->get();

					if($query->num_rows() > 0) {

						$lead_data[] = array(
											'sr_no'=>$i,
											'company_name' => $row->companyname,
											'customer_name' => $row->customer_name,
											'cheque_no' => $row->cheque_no,
											'pdc_date' => date('d-m-Y', strtotime($row->pdc_date)),
											'deposited' => $checked
											);
					}
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}


		function pending_for_ppc_clearance_today_user() {
			$this->load->view('customer/pending_for_ppc_clearance_today_user');
		}

		function pending_for_ppc_clearance_today_user_list() {
		$cur_date = date('Y-m-d');
		$user_id=$_SESSION['logged_in']['user_id'];
		$lead_data = array();
		$sql = $this->db->select('a.id, a.cheque_no, a.pdc_date, c.companyname, d.customer_name, e.id as collection_id')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id', 'left')
						->join('customer_detail d', 'd.id=b.customer_id', 'left')
						->join('customer_cheque_collection e', 'e.order_id=a.id')
						->where('a.checked', 0)
						->where('a.payment_type', 4)
						->where('a.pdc_date', $cur_date)
						->where('a.added_by', $user_id)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$checked = "<input type='checkbox' onchange='mark_checked(".$row->id.")'>";

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date', $cur_date)
									  ->get();

					if($query->num_rows() > 0) {

						$lead_data[] = array(
											'sr_no'=>$i,
											'company_name' => $row->companyname,
											'customer_name' => $row->customer_name,
											'cheque_no' => $row->cheque_no,
											'pdc_date' => date('d-m-Y', strtotime($row->pdc_date)),
											'deposited' => $checked
											);
					}
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function send_notifications() {
	$notification_id = $this->input->post('send_notification');
	
	$sql = $this->db->select('category, send_email, send_whatsapp, email_subject, email_template')
					->from('departmentwise_email_template')
					->where('category', 1)
					->get();

		if($sql->num_rows() > 0) {
			for($i = 0; $i < count($notification_id); $i++) {
				if($notification_id[$i] != '') {
					$sql1 = $this->db->select('a.ref_id, b.customer_name, b.email as customer_email, b.contact_no, c.companyname, c.smtp, c.port, c.email, c.password')
									 ->from('customer_quotation a')
									 ->join('customer_detail b', 'b.id=a.customer_id')
									 ->join('store_rack_location c', 'c.id=a.company_id')
									 ->where('a.id', $notification_id[$i])
									 ->get();

					$quotation_detail = '';

					if($sql1->num_rows() > 0) {
						foreach($sql1->result() as $row1) {
							$customer_name = $row1->customer_name;
							$company_name = $row1->companyname;
							$customer_email = $row1->customer_email;
							$contact_no = $row1->contact_no;
							$quotation_no = $row1->ref_id;

							$quotation_detail.='<table cellpadding="0" cellspacing="0" width="80%" align="left" border="1" style="font-size:14px">
										<thead>
											<tr style="background-color:#666699; color:#fff;">
												<th>Sr No</th>
												<th>Product Name</th>
												<th>Per pack qty</th>
												<th>Per pack list price</th>
											</tr>
										</thead>
										<tbody>';
							$res=$this->db->select('a.*,b.instruments_name')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$notification_id[$i])->get();
							if($res->num_rows()>0)
							{
								$j = 1;
								foreach($res->result() as $product){
									$quotation_detail.='<tr><td>'.$j.'</td>';
									$quotation_detail.='<td>'.$product->instruments_name.'</td>';
									$quotation_detail.='<td>'.$product->qty.'</td>';
									$quotation_detail.='<td>'.$product->list_price.'</td>';

									$j++;
								}
								$quotation_detail.='</tbody></table>';
							}


							
							

							$message = '';

							// $msg_body .= 'Dear '.$row1->customer_name.','."\n";

							foreach($sql->result() as $row);


							// $msg_body .= $message;

							if($row->send_email == 1) {
						
							$find = array('customer_name', 'company_name', 'quotation_no', 'quotation_detail');
							$replace = array($customer_name, $company_name, $quotation_no, $quotation_detail);
							$message = str_replace($find, $replace, $row->email_template);

								
								$this->email->set_mailtype("html");
								$this->email->to($customer_email);
								// $this->email->to('sdsrbh5@gmail.com');
								// $this->email->bcc('webdevelopment1@gamavis.com');
								//$this->email->from($row1->email);
								$this->email->from('info@sunderindoil.com');
			    				$this->email->subject($row->email_subject);
			    				$this->email->message($message);
			    				$result11=$this->email->send();
		    				}

		    				if($row->send_whatsapp == 1) {
		    					/***WHATSAPP INTEGRATION***/
		    				$quotation_detail = page_url.'Customer/quotation_view/'.$notification_id[$i].'/1';

							$find = array('customer_name', 'company_name', 'quotation_no', 'quotation_detail');
							$replace = array($customer_name, $company_name, $quotation_no, $quotation_detail);
							$message = str_replace($find, $replace, $row->email_template);
							// echo $message;exit;

								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_POST, 1);
								$post = array(
								'receiverMobileNo' => $contact_no,
								// 'receiverMobileNo' => '8447031736',
								'username' => '',
								'password' => '',
								'message' => str_replace("&nbsp;", " ", strip_tags($message))		
								);

								// echo "<pre>";print_r($post);exit;

								curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
								$result = curl_exec($ch);
								//echo $result; exit;
								if (curl_errno($ch)) {
								echo 'Error:' . curl_error($ch);
								}
								curl_close($ch);
								/** end **/
		    				}


		    				$data = array(
		    							  'quotation_id' => $notification_id[$i],
		    							  'sent_on' => date('Y-m-d H:i:s'),
		    							  'sent_by' => $_SESSION['logged_in']['user_id']
		    							 );

		    				$this->db->insert('customer_notification_history', $data);
						}
					}

				}
			}
		}			
	$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, notifications successfully sent.</div>');
	redirect(page_url.'Customer/quotations_expiring_today');
	}

	function payment_collection_history() {
		$this->load->view('customer/payment_collection_history');
	}

	function getNotificationHistory() {
		$html = '';
		$quotation_id = $this->input->post('quotation_id');

		$sql = $this->db->select('a.sent_on, b.first_name, b.last_name')
						->from('customer_notification_history a')
						->join('system_users b', 'b.user_id=a.sent_by')
						->where('quotation_id', $quotation_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
				$html .= '<tr>
							<td style="font-size:15px">'.date('d-m-Y H:i:s', strtotime($row->sent_on)).'</td>
							<td style="font-size:15px">'.$row->first_name." ".$row->last_name.'</td>
						  </tr>';
			}
		}

		echo $html;
	}

		function payment_collection_history_list() {
		$lead_data = array();
		$order_id = $this->uri->segment(3);
		$query = $this->db->select('a.id, a.total_amount, a.bank_name, a.total_collection, c.companyname, d.company_name')
						  ->from('customer_cheque_collection a')
						  ->join('store_rack_location c', 'c.id=a.company')
						  ->join('customer_detail d', 'd.id=a.customer')
						  ->where('a.order_id', $order_id)
						  ->order_by('a.id','DESC')
				 		  ->get();



			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

				   $query1 = $this->db->select('cheque_amt, cheque_date, cheque_no, cheque_bank_name')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->id)
							 		  ->get();

					if($query1->num_rows() > 0) {
						foreach($query1->result() as $row1) {

						$lead_data[] = array('sr_no' => $i,
											 'customer_name' => $row->company_name,
											 'cheque_amt' => $row1->cheque_amt,
											 'cheque_date' => date('d-m-Y', strtotime($row1->cheque_date)),
											 'cheque_no' => $row1->cheque_no,
											 'cheque_bank_name' => $row1->cheque_bank_name,
											 'total_amount' => $row->total_amount,
											 'total_collection' => $row->total_collection,
											 'bank_name' => $row->bank_name
											);
						$i++;
						}
					}
				}
			}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function getproducts_detail($quotation)
	{
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		</tr>
		</thead>
		<tbody>';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';

			$j++;
			}
			$html.='</tbody></table>';
			}

			return $html;
	}

		function getstate($shipstate)
	{
		$sname='';
		$resteu=$this->db->select('state_name')->from('states')->where('state_id',$shipstate)->get();
		if($resteu->num_rows()>0)
		{
		foreach($resteu->result() as $product)

		$sname=$product->state_name;

		}

		return $sname;



	}

	// JSON list of states for a country (SAP-matched where SAP has states for it); used by the country/state dropdowns on customer forms.
	public function states_by_country()
	{
		$this->load->library('Sap_service');
		$rows = $this->sap_service->states_for_country($this->input->get_post('country_id'));
		$this->output->set_content_type('application/json')->set_output(json_encode($rows));
	}

	// Reads optional POST country/state and returns the customer_detail columns for them.
	// Returns false when a country is posted without a state although SAP has states defined for that country.
	private function _customer_country_state_data($country_key='country', $state_key='state')
	{
		$country = (int) $this->input->post($country_key);
		$state = (int) $this->input->post($state_key);
		if ($country > 0 && $state <= 0) {
			$this->load->library('Sap_service');
			if (count($this->sap_service->sap_states_for_country($country)) > 0) {
				return false;
			}
		}
		return array('country' => $country, 'state' => $state, 'bill_state' => $state, 'ship_state' => $state);
	}

	public function quotation_preview() {
		$id=$this->uri->segment(3);
		header('location:'.site_http_root.'poformat/tcpdf/examples/quotation.php?quotation_id='.$id);
	}

	function preview_pdf_quote() {
		// echo 'hi';exit;
		$this->load->view('customer/pdf_preview');
	}

	function sendclientintimation() {
		$id=$this->uri->segment(3);
		$whatsapp=$this->input->post('whatsapp');
		$email=$this->input->post('email');
			$profile=$this->input->post('profile');
		
		if($email==1 && $email<>'')
		{
			$aemail=$this->input->post('aemail');
				if($aemail<>'')
				{
			$this->send_mail_to_lead_with_pdf_new($id,$aemail,$profile);
				}
		}

		if($whatsapp==1 && $whatsapp<>'')
		{

			$amobile=$this->input->post('amobile');
				if($amobile<>'')
				{
			$this->whatsapp_quote_with_pdf_new($id,$amobile,$profile);
			}
		}

			$this->session->set_flashdata('message','<div class="alert alert-success">Intimation send.</div><br/>');
			redirect(page_url.'Customer/preview_pdf_quote/'.$id);

	}

			public function send_mail_to_lead_with_pdf($id) {
		$uri=$id;

		if ($uri <> '') {
			$sql = $this->db->select('a.id, a.company_id, a.check_terms, a.general_terms, a.bulk_terms, b.company_name, b.customer_name, b.contact_no, b.email, b.address, b.city, b.alt_contact,c.email as salesemail,c.first_name,c.last_name,c.contact_number')
			                ->from('customer_quotation a')
			                ->join('customer_detail b', 'b.id=a.customer_id', 'left')
			                ->join('system_users c', 'c.user_id=a.added_by', 'left')
			                ->where('a.id',$id)
			                ->get();
		    // $sql = $this->db->select('contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, general_terms, bulk_terms, alt_contact_no, hpcl_company')
		    //                 ->from('leads')
		    //                 ->where('id', $uri)
		    //                 ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);

		        	$unique_id='QUOTE'.$row->id;
		            // $contact_person = $row->contact_person;
		            $alt_contact_no = $row->alt_contact;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $city = $row->city;
		            $contact_no = $row->contact_no;
		            $address = $row->address;
		            $general_terms = $row->general_terms;
		            $bulk_terms = $row->bulk_terms;
		            $email_id = $row->email;
		            $hpcl_company = $row->company_id;
		            $salesemail=$row->salesemail;
		            $first_name=$row->first_name;
		            $last_name=$row->last_name;
		            $salescontact=$row->contact_number;
		        } else {
		            // $contact_person = '';
		            $alt_contact_no = '';
		            $unique_id='';
		            $customer_name = '';
		            $company_name = '';
		            $city = '';
		            $contact_no = '';
		            $address = '';
		            $general_terms = '';
		            $bulk_terms = '';
		            $email_id = '';
		            $hpcl_company = 0;
		            $salesemail='';
		            $salescontact='';
		        }
		} else {
		    redirect(page_url);
		}

		$compemail='';
		$compname='';
		$swe=$this->db->select('email_id,companyname')->from('store_rack_location')->where('id',$hpcl_company)->get();
		if($swe->num_rows()>0)
		{
		foreach($swe->result() as $compemail111);
		$compemail=$compemail111->email_id;
		$compname=$compemail111->companyname;

		}
		$subjectname='Quotation of Industrial Lubricants.';

		$Message = "Dear ".$customer_name." Ji, <br><br>";
	    $Message .= "In reference to our meeting discussion held regarding Industrial Oil supply to
	                    your respective business units therefore, we hereby offer you attached Quotation for the
	                    products as required by yourself."."<br><br>";
	    $Message .= "Regards"."<br>";
	    $Message .= $first_name.' '.$last_name."<br>";
	    $Message .= $salescontact."<br>";
	    $Message .= $compemail."<br>";
	    $Message .= $compname."<br>";

		$file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";


		// $Message ='<table style="width: 100%; font-size:14px; font-family: monospace;">
  //       <tr>
  //           <td width="20%"></td>
  //           <td width="60%" style=" padding:5px; box-shadow: 1px 1px 10px lightgray; background-color:white;">
  //               <table style="width: 100%; font-size:14px;">
  //                   <tr>
  //                       <td width="10%"></td>
  //                       <td width="80%">
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="50%">To</td>
  //                                   <td width="50%" style="text-align:right;">Date:'. date('d-m-Y').'</td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="40%">'.$customer_name.'<br>
  //                                       M/s '.$company_name.'
  //                                       <br>
  //                                       '.$address.'.
  //                                   </td>
  //                                   <td width="30%"></td>
  //                                   <td width="30%"></td>
  //                               </tr>
  //                           </table>
  //                           <br>
  //                           <br>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="10%"></td>
  //                                   <td width="80%" style="text-align:center;">Sub: Quotation of Industrial Lubricants.
  //                                   </td>
  //                                   <td width="10%"></td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       Sir,<br>
  //                                       In reference to our meeting discussion held regarding Industrial Oil supply to
  //                                       your respective business units therefore, we hereby offer you Quotation for the
  //                                       products as required by yourself.
  //                                   </td>
  //                               </tr>
  //                           </table>
  //                           <br>
  //                           <table style="width: 100%; font-size:14px;" border="1">
  //                               <tr>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Product Name with
  //                                       HSN Code</th>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Pack size</th>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Offered Price
  //                                       </th>
  //                               </tr>';

  //                               $sql2 = $this->db->select('a.qty, a.list_price, b.instruments_name')
  //                                                ->from('customer_quotation_detail a')
  //                                                ->join('presto_instruments b', 'b.id=a.product_id')
  //                                                ->where('quotation_id', $uri)
  //                                                ->get();
  //                               if ($sql2->num_rows() > 0) {
  //                                   foreach ($sql2->result() as $row2) {

  //                               $Message.='
  //                               <tr>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->instruments_name.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->qty.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'. $row2->list_price.'</td>
  //                               </tr>';
  //                            } }
  //                          $Message.='</table>';
                           
                            
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.
  //                              $general_terms;
  //                           $Message.='</table>';
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <i>Please feel free to contact us incase of any further queries. We shall be
  //                                           more than happy to assist/resolve all your queries.</i>
  //                                   </td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td style="color: red;">
  //                                       Please Note: - We are the only authorized C&F Agents for Industrial lubricants
  //                                       for <b>M/s HINDUSTAN PETROLEUM CORPORATION LIMITED</b> in Faridabad district and
  //                                       that
  //                                       We/HPCL does not take any responsibility for any unauthorized product supplied
  //                                       by unauthorized/illegitimate supplier.
  //                                   </td>
  //                               </tr>
  //                           </table>';

  //                           $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
  //                           if($com->num_rows() >0)
  //                           {
  //                               foreach($com->result() as $company);
                           
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
  //                                       $company->contact_person.'<br>'.'
  //                                       M/S CFA// '.$company->companyname.'<br>'.
  //                                        $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
  //                                       $company->address.'<br>
  //                                       Emails : '. $company->email_id.'<br>
  //                                       Office Landline No. '.$company->landline_number.'<br>
  //                                       MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
  //                                       Please locate us on HPCL Website:'.$company->locate_us.'


  //                                   </td>
  //                               </tr>
  //                           </table>';
  //                       } 
                          
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.$bulk_terms;
                              
  //                           $Message.='</table>';

  //                              $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                                   <tr>
  //                                       <td>
  //                                           <i>Please feel free to contact us incase of any further queries. We shall be
  //                                               more than happy to assist/resolve all your queries. </i>
  //                                       </td>
  //                                   </tr>
  //                               </table>
  //                               <table style="width: 100%; font-size:14px; padding: 5px;">
  //                                   <tr>
  //                                       <td style="color: red;">
  //                                           We are the only authorized C&F Agents for industrial lubricants for HPCL in
  //                                           Faridabad district and that HPCL does not take any responsibility for any
  //                                           unauthorized product supplied by unauthorized/illegitimate supplier.
  //                                       </td>
  //                                   </tr>
  //                               </table>';
                              
                           
  //                           $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
  //                           // if($com->num_rows() >0)
  //                           // {
  //                               foreach($com->result() as $company);
                           
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
  //                                       $company->contact_person.'<br>'.'
  //                                       M/S CFA//'.$company->companyname.'<br>'.
  //                                        $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
  //                                       $company->address.'<br>
  //                                       Emails : '. $company->email_id.'<br>
  //                                       Office Landline No. '.$company->landline_number.'<br>
  //                                       MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
  //                                       Please locate us on HPCL Website:'.$company->locate_us.'


  //                                   </td>
  //                               </tr>
  //                           </table>';
  //                       // } 
  //                      $Message.='</td>
  //                       <td width="10%"></td>
  //                   </tr>
  //               </table>
  //           </td>
  //           <td width="20%"></td>
  //       </tr>
  //   </table>';
 	// 	// echo $Message; exit;


	$com=$this->db->select('smtp,email,password')->from('store_rack_location')->where('id',$hpcl_company)->get();
if($com->num_rows() >0){
foreach($com->result() as $company);


     $config['protocol'] = 'ssmtp';  
		$config['smtp_host'] = $company->smtp;  
		$config['smtp_user'] = $company->email;  
		$config['smtp_pass'] = $company->password;   
		$config['smtp_port'] = 465;   
		// $config['smtp_crypto'] = 'ssl';
		$config['newline'] = "\r\n";
		$config['starttls'] = TRUE;
		$config['charset'] = 'iso-8859-1';
		$config['mailtype'] = 'html';

        $this->email->initialize($config);  
        $this->load->library('email', $config);
        $this->email->set_header('Header1', 'Value1');
		$this->email->set_mailtype("html");

		$this->email->to($email_id);
		//$this->email->to('sdsrbh5@gmail.com,saurabh@gamavis.com');
		//$this->email->to('sdsrbh5@gmail.com,saurabh@gamavis.com');
		//$this->email->from('info@sunderindoil.com');
		//$this->email->from('lubesmaster@gmail.com');
		if($hpcl_company==3)
		{
			$this->email->cc('faridabadcfa@gmail.com,faridabad@hpclcfa.com');
		}
		$this->email->bcc('sdsrbh5@gmail.com,'.$salesemail);
		$this->email->from($company->email);
		
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$this->email->attach($file);
		$result11=$this->email->send();
		
			$data=array(
					'quotation_id'=>$id,
					'type'=>1,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id']
					);
					$this->db->insert('customer_quotation_mail_history',$data);

	}

		
	}


	function whatsapp_quote_with_pdf($id)
	{
	

		 // $sql = $this->db->select('unique_id,contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, general_terms, bulk_terms, alt_contact_no, hpcl_company')
		 //                    ->from('leads')
		 //                    ->where('id', $id)
		 //                    ->get();

		    $sql = $this->db->select('a.id, a.company_id, b.company_name, b.customer_name, b.contact_no, b.email,c.contact_number as salescontact,c.first_name,c.last_name')
			                ->from('customer_quotation a')
			                ->join('customer_detail b', 'b.id=a.customer_id', 'left')
			                ->join('system_users c','a.added_by=c.user_id')
			                ->where('a.id',$id)
			                ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);
		            // $contact_person = $row->contact_person;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $contact_no = $row->contact_no;
		            $unique_id='QUOTE'.$row->id;
		            $salescontact=$row->salescontact;
		            /** SEND WHATSAPP **/
					$smsmessage="Hello ".$customer_name." ji,\n\n";
					$smsmessage.="Please find the quotation attachment for your requirement\n\n";
					$smsmessage.="Regards\n";
					$smsmessage.=ucwords(strtolower($row->first_name))." ".ucwords(strtolower($row->last_name))."\n";
					$smsmessage.=$salescontact;

					$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => '91'.$contact_no.",".$salescontact,
					// 'receiverMobileNo' => '918447031736',
					// 'receiverMobileNo' => '919560814669',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags(ucwords(strtolower($smsmessage))),
					'filePathUrl' => $file);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					// PDF 
					
					// $file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";
					//$file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";
// ECHO $file; exit;
				

					// $ch = curl_init();
					// curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					// curl_setopt($ch, CURLOPT_POST, 1);
					// $post = array(
					// //'receiverMobileNo' => '91'.$contact,
					// // 'receiverMobileNo' => '918447031736',
					// 'receiverMobileNo' => '91'.$contact_no.",".$salescontact,
					// 'username' => whatsappuser,
					// 'password' => whatsapppass,
					// 'filePathUrl' => $file,
					// 'message'=>'');
					// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					// $result = curl_exec($ch);
					

					// if (curl_errno($ch)) {
					// echo 'Error:' . curl_error($ch);
					// }
					// curl_close($ch);
		            /** END **/
		            
					
					$data=array(
					'quotation_id'=>$id,
					'type'=>2,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id']
					);
					$this->db->insert('customer_quotation_mail_history',$data);

		        } else {
		            // $contact_person = '';
		            $customer_name = '';
		            $company_name = '';
		            $contact_no = '';		         
		        }

	}

	function edit_lead_quote() {
		$this->load->view('customer/edit_lead_quote');
	}


	 function get_finacial_year_range() {
    $year = date('Y');
    $month = date('m');
    if($month<4){
        $year = $year-1;
    }
    $start_date = date('Y-m-d',strtotime(($year).'-04-01'));
    $end_date = date('Y-m-d',strtotime(($year+1).'-03-31'));
    $response = array('start_date' => $start_date, 'end_date' => $end_date);
    return $response;
}

	
	function direct_order_form() {
		$this->load->view('customer/direct_order_form');
	}

	function getunit() {
		$unit = '';
		$product = $this->input->post('proid');

		$sql = $this->db->select('a.unit, b.id,a.pack_size')
						->from('presto_instruments a')
						->join('units b', 'b.shortname=a.unit', 'left')
						->where('a.id', $product)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
				$unit = $row->unit.'|'.$row->id.'|'.$row->pack_size;
		}

		echo $unit;
	}

	function getCustomerDetails_frommaster() {
		  $res = '';
		  $customer = $this->input->post('custid');

		  $query=$this->db->select('company_brand,customer_alias,payment_type,customer_ref_no,bill_address,bill_state,bill_city, email, bill_pincode,bill_email,msme_number,gst,pan,ship_address,ship_state,ship_city,ship_pincode,ship_email,title, company_name, state,contact_no,credit_days')
		  				 ->from('customer_detail')
		  				 ->where('id', $customer)
		  				 ->get();

		  	if ($query->num_rows() > 0) {
		  		foreach ($query->result() as $row);
		  		$bname=$this->salescrm->getBrandName($row->company_brand);
		  		//echo $bname; exit;
				$res = $row->bill_address.'|'.$row->bill_state.'|'.$row->bill_city.'|'.$row->bill_pincode.'|'.$row->bill_email.'|'.$row->msme_number.'|'.$row->gst.'|'.$row->pan.'|'.$row->ship_address.'|'.$row->ship_state.'|'.$row->ship_city.'|'.$row->ship_pincode.'|'.$row->ship_email.'|'.$row->company_name.'|'.$row->contact_no.'|'.$row->customer_ref_no.'|'.$row->payment_type.'|'.$row->credit_days.'|'.$row->customer_alias.'|'.$bname.'|'.$row->email;
			} 

			echo $res;
	}

	public function add_direct_order() {
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 

		$productname_for_check = $this->input->post('product');
		$qty_for_check=$this->input->post('qty');
		$for_new_product=$this->salescrm->checkforavailable_Company_QTY($productname_for_check,$qty_for_check,$this->input->post('company'));
			$new_data=explode('~',$for_new_product);
			$proceed_flag=$new_data[0];
			$prd_not_available=$new_data[1];
			//echo $proceed_flag; exit;
		if($proceed_flag==0)
		{

		$company_id = $this->input->post('company');
		$unique_no=$this->getinvoice_no_new($company_id);
		$sales_order_no=$this->getsales_no_new();

		//echo $unique_no; exit;
		$seller_gst=$this->salescrm->getSellerGst($company_id);
		$customer_id = $this->input->post('customer');



		$data = array(
			'company_id' => $company_id,
			'customer_id' => $customer_id,
			'validity_date' => date('Y-m-d', strtotime('+10 days')),
			'ref_id' => $unique_no,
			'check_terms' => 2,
			'general_terms' => $this->input->post('drums_tnc'),
			'bulk_terms' => $this->input->post('bulk_tnc'),
			'added_on' => $date,
			'added_by' => $user_id
			);
		$result  = $this->db->insert('customer_quotation',$data);
		$insert_id = $this->db->insert_id();

		if($result > 0) {
			$productname = $this->input->post('product');
			$comp_product = $this->input->post('comp_product');
            $qty = $this->input->post('qty');
            $packsize = $this->input->post('pack_size');
            $pack_size_text = $this->input->post('pack_size_text');
            $listprice = $this->input->post('listprice');
             $batch_code = $this->input->post('batch_code');
	        $discount = $this->input->post('discountprice');
	        $discountpricehide = $this->input->post('discountpricehide');
	        $netprice = $this->input->post('netprice');
	        $rebrand_used = $this->input->post('rebrand_used');
	        $rebrand_prd = $this->input->post('rebrand_prd');
	        


       		for($i=0 ;$i<count($productname);$i++){
       			  
       			  if($listprice[$i] < $discountpricehide[$i]) {
	       			$flag = 0;
	       		} else {
	       			$flag = 1;
	       		}

	       		$cp=$this->salescrm->getcurrentcp($productname[$i]);
	       		if($pack_size_text[$i]=="DRUM")
	       		{
	       			$bcode=$batch_code[$i];
	       		}else{
	       			$bcode='';
	       		}


	       		if($rebrand_used[$i]==1)
	       		{
	       			$rb_prd=$rebrand_prd[$i];
	       			$used=1;
	       		}else
	       		{
	       			$rb_prd=0;
	       			$used=0;
	       		}
	 			$data1 = array(
						'quotation_id' => $insert_id,
						'competitor_product' => $comp_product[$i],
						'product_id' => $productname[$i],
						'qty' => $qty[$i],
						'pack_size' => $packsize[$i],
						'list_price' => $listprice[$i],
						'agreed_price' => $listprice[$i],
						'batch_code' => $bcode,
						'discount_price' => $discount[$i],
						'net_price' => $netprice[$i],
						'flag' => $flag,
						'msp'=>$discountpricehide[$i],
						'cp'=>$cp,
						'added_on' => date('Y-m-d H:i:s'),
						'new_batch_code'=>1,
						'rebrand'=>$used,
						'rebrand_product_id'=>$rb_prd,
						'added_by' => $user_id
				);
	            $this->db->insert('customer_quotation_detail',$data1);
	    	}
		}

		 // echo "<pre>";print_r($data);exit;
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
				$start_date=$financial['start_date']." 00:00:00";
				$end_date=$financial['end_date']." 23:59:59";
			}else
			{
				$start_date=date('Y-04-01')." 00:00:00";
				$end_date=date('Y-m-d')." 23:59:59";
			}
			
			$pic = $_FILES['upload_file']['name'];

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = '';
			 }

			$sql = $this->db->select('generated_order_id')
							->from('order_punch')
							->where('added_on>=',$start_date)
							->where('added_on<=',$end_date)
							->order_by('id', 'DESC')
							->limit(1)
							->get();

				if ($sql->num_rows() > 0) {
					foreach ($sql->result() as $row);	
						$generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
				} else {
						$generated_order_id = '001';
				}

				if($this->input->post('check_freight') == 1) {
					$freight = 1;
				} else {
					$freight = 0;
				}


		$data2 = array(
					 'quotation_id' => $insert_id,
					 'source'=>$this->input->post('source'),
					 'agent'=>$this->input->post('agent'),
					 // 'invoice_no' => 0,
					'sales_order_no'=>$sales_order_no,
					'salesorderstart'=>1,
					'sales_order_addedOn'=>date('Y-m-d H:i:s'),
					'sales_order_By'=>$_SESSION['logged_in']['user_id'],
					 'direct_order' => 1,
					 'upload_po' => $newname,
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'hpcl_billing_company' => $this->input->post('company'),
					 'freight' => $freight,
					 'freight_amount' => $this->input->post('freight_amt'),
					 'generated_order_id' => $generated_order_id,
					 'added_on' => date('Y-m-d H:i:s'),
					 'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		// echo "<pre>";print_r($data);exit;

		$this->db->insert('order_punch', $data2);
		$order_id = $this->db->insert_id();


		$sql3 = $this->db->select('address, contact_no')
						 ->from('customer_detail')
						 ->where('id', $customer_id)
						 ->get();

		// if($sql3->num_rows() > 0) {
		// 	foreach($sql3->result() as $row4);
		// 		if($row4->address == '' || $row4->contact_no == '') {
		// 			$data3 = array(
								  
		// 						   'address' => $this->input->post('billing_address'),
		// 						   'contact_no' => $this->input->post('billing_mobile_no'),
		// 						   'state' => $this->input->post('billing_state'),
		// 						   'email' => $this->input->post('billing_email'),
		// 						   'city' => $this->input->post('billing_city'),
		// 						   'pincode' => $this->input->post('billing_pincode')
		// 						  );

		// 			$this->db->where('id', $customer_id)
		// 					 ->update('customer_detail', $data3);
		// 		}
		// }

			$data4 = array(
						 'create_date' => date('Y-m-d'),
						 'order_id' => $order_id,
						 'company' => $company_id,
						 'customer' => $customer_id,
						 // 'total_amount' => $this->input->post('total_amount'),
						 'bank_name' => '',
						 'total_collection' => 0
						 );
	
			$this->db->insert('customer_cheque_collection', $data4);
			$collection_id = $this->db->insert_id();

			$datas5 = array(
						 'collection_id' => $collection_id,
						 'cheque_amt' => 0,
						 'cheque_bank_name' => ''
						 );

			$this->db->insert('customer_cheque_collection_details', $datas5);

			if($this->input->post('check_billing') == 1) {
				$check_billing = 1;
			} else {
				$check_billing = 0;
			}

			// if($this->input->post('ttype')==1)
			// {
			// 	$transport_type=$this->input->post('ttype');
			// 	$vehicle_no=$this->input->post('self_vehicle_no');
			// 	$transporter_id=0;
			// 	$transporter_mobile=0;
			// 	$transporter_address='';
			// 	$trans_rate_type=0;
			// 	$trans_rate=0;
				
			// }else if($this->input->post('ttype')==2)
			// { 
			// 	$transport_type=2;
			// 	$vehicle_no=$this->input->post('vehicle_no');
			// 	$sql = $this->db->select('id')
			// 	->from('transporter_details')
			// 	->where('id', $this->input->post('transporter_name'))
			// 	->get();
			// 	if($sql->num_rows() == 0) {
			// 	$datas = array(
			// 	'name' => $this->input->post('transporter_name'),
			// 	'mobile_no' => $this->input->post('tmobile'),
			// 	'address' => $this->input->post('taddress')
			// 	);
			// 	$this->db->insert('transporter_details', $datas);
			// 	$transporter_id = $this->db->insert_id();
			// 	}else
			// 	{
			// 	$transporter_id=$this->input->post('transporter_name');
			// 	}

			// 	$transporter_mobile=$this->input->post('tmobile_no');
			// 	$transporter_address=$this->input->post('taddress');
			// 	$trans_rate_type=$this->input->post('rate_type');
			// 	$trans_rate=$this->input->post('transport_rate');

	

			// }

		$data_m = array(
					 'order_id' => $order_id,
					 'ship_to' => $this->input->post('ship_to'),
					 'shipping_address' => $this->input->post('shipping_address'),
					 'shipping_state' => $this->input->post('shipping_state'),
					 'shipping_city' => $this->input->post('shipping_city'),
					 'shipping_pincode' => $this->input->post('shipping_pincode'),
					 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
					 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
					 'shipping_email' => $this->input->post('shipping_email'),
					 'same_shipping_billing' => $check_billing,
					 'bill_to' => $this->input->post('bill_to'),
					 'billing_name' => $this->input->post('billing_name'),
					 'billing_address' => $this->input->post('billing_address'),
					 'billing_state' => $this->input->post('billing_state'),
					 'billing_city' => $this->input->post('billing_city'),
					 'billing_pincode' => $this->input->post('billing_pincode'),
					 'billing_phone_no' => $this->input->post('billing_phone_no'),
					 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
					 'billing_email' => $this->input->post('billing_email')
					 // 'transport_type'=>$transport_type,
					 // 'transporter_id'=>$transporter_id,
					 // 'transporter_mobile'=>$transporter_mobile,
					 // 'transporter_address'=>$transporter_address,
					 // 'rate_type'=>$trans_rate_type,
					 // 'rate'=>$trans_rate,
					 // 'vehicle_no'=>$vehicle_no,
					 // 'vehicle_type' => $this->input->post('vehicle_type'),
					 // 'destination' => $this->input->post('destination')
					 );

		$this->db->insert('order_punch_mailing_details', $data_m);

		$data_t = array(
					 'order_id' => $order_id,
					 'msme_no' => $this->input->post('msme_no'),
					 'pan_no' => $this->input->post('pan_no'),
					 'registration_type' => $this->input->post('registration_type'),
					 'gst_no' => $this->input->post('gst_no')
					 );

		$this->db->insert('order_punch_tax_details', $data_t);

		$data_p = array(
					 'order_id' => $order_id,
					 'reference' => $this->input->post('reference'),
					 'note' => $this->input->post('note')
					 );

		$this->db->insert('order_punch_payment_details', $data_p);

		$data_upd = array(
						 'order_punch' => 1
						 );

		$this->db->where('id', $insert_id)
				 ->update('customer_quotation', $data_upd);


				 /** CHECK IF CUSTOMER DETAIL IS EMPTY THEN UPDATE THE DATA **/

				$resteye= $this->db->select('email,gst,pan,ship_address,ship_pincode,ship_email,ship_state,ship_city,bill_address,bill_state,bill_city,bill_pincode,bill_email,msme_number')->from('customer_detail')->where('id',$customer_id)->get();
				if($resteye->num_rows()>0)
				{
					foreach($resteye->result() as $curow);
					if($curow->email=='' || $curow->email==0 || $curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->gst=='' || $curow->gst==0)
					{
						$darray=array('gst'=>$this->input->post('gst_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->pan=='' || $curow->pan==0)
					{
						$darray=array('pan'=>$this->input->post('pan_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_address=='' || $curow->ship_address==0)
					{
						$darray=array('ship_address'=>$this->input->post('shipping_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_pincode=='' || $curow->ship_pincode==0)
					{
						$darray=array('ship_pincode'=>$this->input->post('shipping_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_email=='' || $curow->ship_email==0)
					{
						$darray=array('ship_email'=>$this->input->post('shipping_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_state=='' || $curow->ship_state==0)
					{
						$darray=array('ship_state'=>$this->input->post('shipping_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_city=='' || $curow->ship_city==0)
					{
						$darray=array('ship_city'=>$this->input->post('shipping_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->bill_address=='' || $curow->bill_address==0)
					{
						$darray=array('bill_address'=>$this->input->post('billing_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_state=='' || $curow->bill_state==0)
					{
						$darray=array('bill_state'=>$this->input->post('billing_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_city=='' || $curow->bill_city==0)
					{
						$darray=array('bill_city'=>$this->input->post('billing_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_pincode=='' || $curow->bill_pincode==0)
					{
						$darray=array('bill_pincode'=>$this->input->post('billing_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('bill_email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->msme_number=='' || $curow->msme_number==0)
					{
						$darray=array('msme_number'=>$this->input->post('msme_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->payment_type==0 || $curow->payment_type=='')
					{
						$darray=array('payment_type' => $this->input->post('payment_type'),'credit_days' => $this->input->post('paymentterms'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}
				}

							$darray1=array('customer_ref_no'=>$this->input->post('customer_codes'));
							$this->db->where('id',$this->input->post('customer'));
							$this->db->update('customer_detail',$darray1);

				 /** END **/
 

		$ptype=$this->input->post('payment_type');
		if($ptype==4)
		{

				$credit_days = $this->input->post('paymentterms');
				$expected_pdc_date = date('Y-m-d', strtotime('+'.$credit_days.' days'));
				$original_cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
				$pdcrecv = $this->input->post('pdcrecv');

				if($pdcrecv==1)
				{
					$hold_due_to_pdc=0;
					if(strtotime($original_cheque_date)>strtotime($expected_pdc_date))
					{
						$hold_due_to_pdc=1;
					}

					$recieved=1;

					$cheque_no=$this->input->post('cheque_no');

				}else
				{
					$recieved=0;
					$hold_due_to_pdc=0;
					$cheque_no='';
				}

					$data_chq = array(
					'order_id' => $order_id,
					'customer_id' => $customer_id,
					'expected_pdc_date' => $original_cheque_date,
					'cheque_no'=>$cheque_no,
					'received' => $recieved,
					'deposited' => 0,
					'order_punch_date' =>  date('Y-m-d H:i:s'),
					'hold_due_to_pdc'=>$hold_due_to_pdc
					);

			
				$this->db->insert('customer_cheque_details', $data_chq);

				// echo "<pre>";print_r($data_chq);exit;
		
		}

		if($ptype==6)
		{
			$cheque_no=$this->input->post('cheque_no');
			$cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
			$order_amount = $this->salescrm->getOrderAmountWithGST($insert_id, $this->input->post('gst_no'), $seller_gst);

			$data2 = array(
						   'customer_id' => $customer_id,
						   'type' => 2,
						   'bills' => "'$order_id'",
						   'addedOn' => date('Y-m-d H:i:s'),
						   'addedBy' => $this->session->userdata['logged_in']['user_id']
						  );

			$this->db->insert('customer_payments', $data2);
			$payment_id = $this->db->insert_id();

			$data3 = array(
						   'payment_id' => $payment_id,
						   'payment_type' => 1,
						   'cheque_no' => $cheque_no,
						   'cheque_date' => $cheque_date,
						   'amount' => $order_amount
						  );

			$this->db->insert('customer_payment_particulars', $data3);

			$data_chq = array(
					'order_id' => $order_id,
					'customer_id' => $customer_id,
					'expected_pdc_date' => $cheque_date,
					'cheque_no'=>$cheque_no,
					'received' => 1,
					'deposited' => 0,
					'order_punch_date' =>  date('Y-m-d H:i:s'),
					'hold_due_to_pdc' => 0
					);

			
				$this->db->insert('customer_cheque_details', $data_chq);
		
		}

		$mul_company = $this->input->post('mul_company');

		for($k=0; $k<count($mul_company);$k++) {
			if($mul_company[$k] != '') {
				$checkIfCustomerCompanyExists = $this->salescrm->checkIfCustomerCompanyExists($customer_id, $mul_company[$k]);

				// echo $checkIfCustomerCompanyExists;exit;

				if($checkIfCustomerCompanyExists == 0) {
					$getCustomerAllDetails = $this->salescrm->getCustomerAllDetails($customer_id);

					if($getCustomerAllDetails != '') {
					   foreach($getCustomerAllDetails as $row9);

							$data9 = array(
								   'company_id' => $mul_company[$k],
								   'customer_ref_no' => $row9->customer_ref_no,
								   'title' => $row9->title,
								   'customer_name' => $row9->customer_name,
								   'email' => $row9->email,
								   'contact_no' => $row9->contact_no,
								   'country' => $row9->country,
								   'state' => $row9->state,
								   'city' => $row9->city,
								   'gst' => $row9->gst,
								   'pan' => $row9->pan,
								   'address' => $row9->address,
								   'status' => $row9->status,
								   'company_name' => $row9->company_name,
								   'alt_contact' => $row9->alt_contact
								  );

							$this->db->insert('customer_detail', $data9);

					}
				}
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order Successfully Generated.</div>');
			// redirect(page_url.'Customer/discount_approval/'.$insert_id);
			redirect(page_url.'Customer/direct_order_form');

		}else
		{

			$ty='';
			//echo $prd_not_available1; exit;
			$product_names=$this->salescrm->get_prd_not_availableNew($prd_not_available);

			if(count($product_names)>0)
			{
				foreach($product_names as $prd_names1)
				{
				$ty.=$prd_names1."<br/><br/>";
				}
			}

			echo "<strong style='color:red;font-weight:bold;'>REQUIRED STOCK IS NOT AVAIABLE FOR FOLLOWING ITEMS<BR/><br/>".$ty."<br/><br/>RECREATE THE ORDER WITH PROPER STOCK</strong>"; exit;

		}

		
	}


		function getinvoice_no()
		{
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

			$compid=$this->input->post('compid');

			$rest=$this->db->select('invoice_starts_from')->from('store_rack_location')->where('id',$compid)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				$invoice_starts=$row->invoice_starts_from;
			}else
			{
				$invoice_starts=0;
			}




			$sql = $this->db->select('invoice_no')
			->from('order_punch')
			->where('hpcl_billing_company',$compid)
			->where('added_on>=',$start_date)
			->where('added_on<=',$end_date)	
			->where('invoice_no!=',0)
			->order_by('invoice_no', 'DESC')
			->limit(1)
			->get();



            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->invoice_no+1;

		}else
		{
			$lastorderid=$invoice_starts;
		}

		return $lastorderid;
	}


	public function add_customer_vis_ajax()
	{
		
		$user_id=$_SESSION['logged_in']['user_id'];
	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$state=$this->input->post('state');
		$city=$this->input->post('cityname');
	
		$table = "customer_detail";
		$cust_name = $this->input->post('contact_person');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$pincode = $this->input->post('pincode');
		// $ctype = $this->input->post('ctype');

			$data = array(
			'title'=>$this->input->post('title'),
			'company_name'=>$this->input->post('hpclcompany'),
			'customer_ref_no'=>$this->input->post('customer_code'),
			'company_id'=>$this->input->post('client_company'),
			'email'=> $email,
			'customer_name'=>$cust_name,
			'order_max_limit'=>$this->input->post('order_max_limit'),
			'email'=>$email,
			'contact_no'=>$mobile,
			'address'=>$this->input->post('address'),
			'state'=>$state,
			'city'=>$city,
			'pincode'=>$pincode,
			'bill_address'=>$this->input->post('address'),
			'bill_state'=>$state,
			'bill_city'=>$city,
			'bill_pincode'=>$pincode,
			'bill_email'=>$email,
			// 'customer_type'=>$ctype,
			'gst'=>$this->input->post('gst'),
			'country'=>'101',
			'status'=>1,
			'msme_number'=>$this->input->post('msme_number'),
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			
			$this->db->insert($table,$data);
			$lid=$this->db->insert_id();
			if($this->db->affected_rows()>0)
			{
				echo $lid;
			}else
			{
				echo false;
			}
			
	}

	function delete_product() {
		$id = $this->input->post('id');

		$this->db->where('id', $id)
				 ->delete('customer_quotation_detail');

		if($this->db->affected_rows() > 0) {
			echo 1;
		} else {
			echo 0;
		}
	}

	function get_tnc() {
		$term1 = '';
		$term2 = '';
		$company = $this->input->post('company');

		$sql = $this->db->select('term_for, term_conditions')
						->from('customer_quotation_terms_condition')
						// ->where('term_for', $term_for)
						->where('company_id', $company)
						->where('status', 1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
			
				if($row->term_for == 1) {
					$term1 = $row->term_conditions;
				} 

				if($row->term_for == 2) {
					$term2 = $row->term_conditions;
				}
			}
		}

		echo $term1.'|'.$term2;
	}

	function delete_order() {
		$order_id = $this->uri->segment(3);
		$quotation_id = $this->uri->segment(4);

		   $this->db->where('id', $order_id)
					->delete('order_punch');

		   $this->db->where('order_id', $order_id)
					->delete('order_punch_mailing_details');

		    $this->db->where('order_id', $order_id)
					 ->delete('order_punch_payment_details');

			$this->db->where('order_id', $order_id)
					 ->delete('order_punch_tax_details');

		$data_upd = array(
						 'order_punch' => 0
						 );

		$this->db->where('id', $quotation_id)
				 ->update('customer_quotation', $data_upd);

		$this->session->set_flashdata('message','<div class="alert alert-success">Order Successfully Deleted.</div>');
		redirect(page_url.'Customer/all_orders');

	}

		function pending_for_pdc_recd() {
			$this->load->view('customer/pending_for_pdc_recd');
		}

		function pending_for_pdc_recd_list() {
		$cur_date = date('Y-m-d');

		$lead_data = array();
		$sql = $this->db->select('a.id,b.quotation_id,b.invoice_no,a.id, a.expected_pdc_date, d.company_name,e.gst,f.gst_no,b.payment_type,b.credit_days,d.customer_name,h.first_name,h.last_name,i.shipping_mobile_no')
						->from('customer_cheque_details a')
						->join('order_punch b', 'b.id=a.order_id')
						->join('order_punch_tax_details f', 'f.order_id=b.id')
						->join('order_punch_mailing_details i', 'i.order_id=b.id')
						->join('customer_quotation c', 'c.id=b.quotation_id')
						->join('customer_detail d', 'd.id=c.customer_id')
						->join('store_rack_location e', 'e.id=b.hpcl_billing_company')
						->join('system_users h','h.user_id=b.agent')
						->where('a.received', 0)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

							if($row->payment_type == 2) {
							$payment_type = 'Cash';
							$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
							$payment_terms.= '';
							} else if($row->payment_type == 3) {
							$payment_type = 'Online';
							$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
							$payment_terms .= '';
							} else if($row->payment_type == 4) {
							$payment_type = 'PDC';
							$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
							$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
							}else if($row->payment_type == 5) {
							$payment_type = 'CREDIT';
							$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
							$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
							}else if($row->payment_type == 6) {
							$payment_type = 'ADVANCE';
							$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';

							} else {
							$payment_type = '';
							$payment_terms="";
							$payment_terms .= '';
							}

					
						$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$action="<a href='javascript:;' class='btn btn-danger' onclick='add_pdc_details(".$row->id.");'>PDC Recieved</a>";
						$lead_data[] = array(
											'sr_no'=>$i,
											'invoice_no'=>$row->invoice_no,
											'company_name' => $row->company_name,
											'customer_name' => $row->customer_name."<br/>".$row->shipping_mobile_no,
											'amount' => $order_value,
											'payment_term' =>$payment_terms,
											'sales_agent' =>$row->first_name." ".$row->last_name,
											'action'=>$action
											);

					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

		function pending_for_pdc_deposited() {
			$this->load->view('customer/pending_for_pdc_deposited');
		}

		function pending_for_pdc_deposited_list() {
		$cur_date = date('Y-m-d');

		$lead_data = array();
		$sql = $this->db->select('a.id, a.expected_pdc_date, d.company_name,b.id as order_punch_id')
						->from('customer_cheque_details a')
						->join('order_punch b', 'b.id=a.order_id')
						->join('customer_quotation c', 'c.id=b.quotation_id')
						->join('customer_detail d', 'd.id=c.customer_id')
						->where('a.deposited', 0)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$deposited = "<input type='checkbox' onchange='mark_deposited(".$row->id.",".$row->order_punch_id.")'>";

						$lead_data[] = array(
											'sr_no'=>$i,
											'company_name' => $row->company_name,
											// 'customer_name' => $row->customer_name,
											// 'cheque_no' => $row->cheque_no,
											'pdc_date' => date('d-m-Y', strtotime($row->expected_pdc_date)),
											'deposited' => $deposited
											);

					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function update_pdc_deposited() {
		$evidence = $_FILES['upload_evidence']['name'];

		if($evidence<>'') {
			$files = explode('.',$evidence);
			$ext = end($files);
			$newname = time().'.'.$ext;
			move_uploaded_file($_FILES["upload_evidence"]["tmp_name"],assets_upload.'pdc_dep_evidence/'.$newname);
		}else {
			$newname = '';
		}

		$data = array(
					 'deposited'=>1,
					  'dep_evidence' => $newname,
					  'dep_remarks' => $this->input->post('remarks')
					 );

		$this->db->where('id', $this->input->post('detail_id'))
				 ->update('customer_cheque_details', $data);



		$this->session->set_flashdata('message','<div class="alert alert-info">Cheque Successfully Deposited</div>');
		redirect(page_url.'Customer/pending_for_pdc_deposited');
	}

		function pdc_deposited_history() {
			$this->load->view('customer/pdc_deposited_history');
		}

		function pdc_deposited_history_list() {
		$cur_date = date('Y-m-d');

		$lead_data = array();
		$sql = $this->db->select('a.id, a.expected_pdc_date, a.dep_remarks, a.dep_evidence, d.company_name')
						->from('customer_cheque_details a')
						->join('order_punch b', 'b.id=a.order_id')
						->join('customer_quotation c', 'c.id=b.quotation_id')
						->join('customer_detail d', 'd.id=c.customer_id')
						->where('a.deposited', 1)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

					$evidence = '<a href="'.assets_url.'pdc_dep_evidence/'.$row->dep_evidence.'" download>Download</a>';

						$lead_data[] = array(
											'sr_no'=>$i,
											'company_name' => $row->company_name,
											// 'customer_name' => $row->customer_name,
											// 'cheque_no' => $row->cheque_no,
											'pdc_date' => date('d-m-Y', strtotime($row->expected_pdc_date)),
											'evidence' => $evidence,
											'remarks' => $row->dep_remarks
											);

					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}

	function payment_overdue()
	{
		$this->load->view('customer/payment_overdue');
	}


	function all_payment_overdue_list() {

		if (!$this->dashboardmodel->payment_overdue_module_available()) {
			$results = array(
				"sEcho" => 1,
				"iTotalRecords" => 0,
				"iTotalDisplayRecords" => 0,
				"aaData" => array(),
				"data" => array()
			);

			header('Content-Type: application/json');
			echo json_encode($results);
			return;
		}

		$user_id=$this->uri->segment(3);
		$company=$this->uri->segment(4);
		$overdue_days_type=$this->uri->segment(5);
		if($overdue_days_type<>'ALL' && $overdue_days_type<>'')
		{
			if($overdue_days_type==1)
			{
				$l="1";
				$up="29";
			}else if($overdue_days_type==2)
			{
				$l="30";
				$up="59";
			}else if($overdue_days_type==3)
			{
				$l="60";
				$up="89";
			}else
			{
				$l="89";
				$up="100000";
			}
		}else
		{
			$l=0;
			$up=0;
		}
		$currentday=date('Y-m-d');
		$lead_data = array();
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.cancelled',0);

						  if($user_id<>'' && $user_id<>'NA')
						  {
						  	$this->db->where('a.agent',$user_id);
						  }
						  if($company<>'' && $company<>'ALL')
						  {
						  	$this->db->where('a.hpcl_billing_company',$company);
						  }
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

					

					$html=$this->getproducts_detail($row->quotation_id);

					
					$j=1;

					
					if($row->payment_type == 2) {
						$payment_type = 'Cash';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.= '';
					} else if($row->payment_type == 3) {
						$payment_type = 'Online';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms .= '';
					} else if($row->payment_type == 4) {
						$payment_type = 'PDC';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
						$payment_type = 'CREDIT';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
						$payment_type = 'ADVANCE';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						
					} else {
						$payment_type = '';
						$payment_terms="";
						$payment_terms .= '';
					}

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{
						
							$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));
				
							$exceed_days = floor($diff / (60 * 60 * 24));
						
					
						$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$get_username = $this->salescrm->getusername($row->unfollow_added_by);

						$partial=$this->customer_previous_payment($row->id);
	
						$payment_due=$order_value-$partial;

						if($row->unfollow_customer == 0) {
							$do_not_followup = "<input type='checkbox' name='chk_unfollow' class='chk_unfollow".$row->id."' value='".$row->customer_id."' onchange='check_followup(".$row->id.")'>";
						} else {
							$do_not_followup = "ORDER UNFOLLOWED ON: <strong style='color:red;'>".date('d-m-Y H:i:s', strtotime($row->unfollow_added_on))."</strong><br>ORDER UNFOLLOWED BY: <strong style='color:red;'>".$get_username."</strong>";
						}



						if($l!=0 && $up!=0)
						{
						 
						if($exceed_days>=$l && $exceed_days<=$up)
						{

						$payment_close="<span id='c".$row->id."'><input type='checkbox' id='payment_close".$row->id."' value='".$row->id."' onchange='close_payment(".$row->id.")'></span>";
					$lead_data[] = array('sr_no'=>$i."<BR/>".$currentday."<BR/>".$expected_payment_days,

						                 'agent'=>$row->first_name." ".$row->last_name,
										 'company_name'=>$row->companyname,
										 'invoice'=>$row->invoice_no,
										 'cust_company_name'=>"<strong>".$row->company_name."</strong>",
										 'customer_name'=>$row->customer_name,
										 'products'=>$html,
										 'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
										 'partial_payment'=>$partial,
										 'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
										 'payment_close'=>'',
										 'payment_terms' =>$payment_terms,
								 		 'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
								 		 'payment_date' =>date('d-M-Y',strtotime($expected_payment_days)),
								 		 'exceeded_by' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>+".$exceed_days." Days</strong>",
								 		 'do_not_followup' => $do_not_followup
										);
					$i++;
				}
				}else
				{

					$payment_close="<span id='c".$row->id."'><input type='checkbox' id='payment_close".$row->id."' value='".$row->id."' onchange='close_payment(".$row->id.")'></span>";
					$lead_data[] = array('sr_no'=>$i,

						                 'agent'=>$row->first_name." ".$row->last_name,
										 'company_name'=>$row->companyname,
										 'invoice'=>$row->invoice_no,
										 'cust_company_name'=>"<strong>".$row->company_name."</strong>",
										 'customer_name'=>$row->customer_name,
										 'products'=>$html,
										 'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
										 'partial_payment'=>$partial,
										 'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
										 'payment_close'=>$payment_close,
										 'payment_terms' =>$payment_terms,

								 		 'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
								 		 'payment_date' =>date('d-M-Y',strtotime($expected_payment_days)),
								 		 'exceeded_by' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>+".$exceed_days." Days</strong>",
								 		 'do_not_followup' => $do_not_followup
										);
					$i++;


				}
				}
				}
			}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function customer_previous_payment($order_id)
	{
		$recvd=array();
		$recvd[]=0;
		$Resteuy=$this->db->select('order_amount,recieved_amount')->from('customer_order_to_payments')->where('order_id',$order_id)->get();
		if($Resteuy->num_rows()>0){
			foreach($Resteuy->result() as $row)
			{
				$recvd[]=$row->recieved_amount;
			}
		}

		return array_sum($recvd);
	}

	function upcoming_customer_payments()
	{
		$this->load->view('customer/upcoming_customer_payments');
	}

	function all_payment_upcoming_list() {

		$user_id=$this->uri->segment(3);
		$currentday=date('Y-m-d');

		$lead_data = array();
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.cancelled',0)
						  // ->where('a.invoice_no',848)
						  ->where('a.send_to_tally',1);


						  if($user_id<>'')
						  {
						  	$this->db->where('a.agent',$user_id);
						  }
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

					

					$html=$this->getproducts_detail($row->quotation_id);

					
					$j=1;

					
					if($row->payment_type == 2) {
						$payment_type = 'Cash';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.= '';
					} else if($row->payment_type == 3) {
						$payment_type = 'Online';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms .= '';
					} else if($row->payment_type == 4) {
						$payment_type = 'PDC';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
						$payment_type = 'CREDIT';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
						$payment_type = 'ADVANCE';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						
					} else {
						$payment_type = '';
						$payment_terms="";
						$payment_terms .= '';
					}

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}



					$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

					//echo $expected_payment_days."<br/>".$currentday; exit;
					
					$diff = strtotime($expected_payment_days) - strtotime($currentday);

					$days=round($diff / (60 * 60 * 24));
					
				 if($days<11 && strtotime($expected_payment_days)>=strtotime($currentday))
				{
						$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);

						$partial=$this->customer_previous_payment($row->id);
	
						$payment_due=$order_value-$partial;

					$lead_data[] = array('sr_no'=>$i,

						                 'agent'=>$row->first_name." ".$row->last_name,
										 'company_name'=>$row->companyname,
										 'invoice'=>$row->invoice_no,
										 'cust_company_name'=>"<strong>".$row->company_name."</strong>",
										 'customer_name'=>$row->customer_name,
										 'products'=>$html,
										 'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
										 'partial_payment'=>$partial,
										 'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
										 'payment_terms' =>$payment_terms,
								 		 'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
								 		 'payment_date' =>date('d-M-Y',strtotime($expected_payment_days))
								 		
										);
					$i++;
				
			}
				}
			}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function add_cheque_details()
	{
		$record_id=$this->input->post('record_id');
		$cheque_no=$this->input->post('cheque_no');
		$cheque_date=$this->input->post('cheque_date');
		$cheque_amt=$this->input->post('cheque_amount');
		if($record_id>0)
		{
			$rest=$this->db->select('a.order_id,a.customer_id,b.credit_days,a.order_punch_date,b.quotation_id,c.gst_no,d.gst')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->join('order_punch_tax_details c','c.order_id=b.id')->join('store_rack_location d', 'd.id=b.hpcl_billing_company')->where('a.id',$record_id)->get();

			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$order_id=$row->order_id;


				$credit_days = $row->credit_days;
				$expected_pdc_date = date('Y-m-d', strtotime($row->order_punch_date. '+'.$credit_days.' days'));

				$original_cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));

				if(strtotime($original_cheque_date)>strtotime($expected_pdc_date))
					{
						$hold_due_to_pdc=1;
					}else
					{
						$hold_due_to_pdc=0;
					}


				$updata=array('expected_pdc_date'=>date('Y-m-d',strtotime($cheque_date)),'cheque_no'=>$cheque_no,'cheque_amount'=>$cheque_amt,'received'=>1,'recieved_On'=>date('Y-m-d H:i:s'));

				$this->db->where('id',$record_id);
				$this->db->update('customer_cheque_details',$updata);


				/** UPDATE PAYMENTS DETAILS **/
				$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
				$pay_data=array('customer_id'=>$row->customer_id,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('customer_payments',$pay_data);
				$lid=$this->db->insert_id();
				$data = array(
				'payment_id' => $lid,
				'payment_type' => 1,
				'cheque_no' => $cheque_no,
				'cheque_date' => date('Y-m-d',strtotime($cheque_date)),
				'amount' => $cheque_amt
				);
				$this->db->insert('customer_payment_particulars', $data);
				/** END **/

				if($order_value==$cheque_amt)
				{
					$update_payment=array('payment'=>1,'payment_id'=>$lid);
					$this->db->where('id',$row->order_id);
					$this->db->update('order_punch',$update_payment);

					$pending_order_amount=$this->get_balance($row->order_id);

					$orderdata=array('order_id'=>$row->order_id,'payment_id'=>$lid,'order_amount'=>$order_value,'recieved_amount'=>$cheque_amt,'balance'=>0,'pending_order_amount'=>$pending_order_amount);
					$this->db->insert('customer_order_to_payments',$orderdata);

					$leftbalance=0;


				}else if($order_value>$cheque_amt)
				{
					$pending_order_amount=$this->get_balance($row->order_id);
					$balance=$order_value-$cheque_amt;
					$orderdata=array('order_id'=>$row->order_id,'payment_id'=>$lid,'order_amount'=>$order_value,'recieved_amount'=>$cheque_amt,'balance'=>$balance,'pending_order_amount'=>$pending_order_amount);
					$this->db->insert('customer_order_to_payments',$orderdata);
					$leftbalance=0;

				}else
				{
					// order value less than cheque_amt
					$update_payment=array('payment'=>1,'payment_id'=>$lid);
					$this->db->where('id',$row->order_id);
					$this->db->update('order_punch',$update_payment);
						$pending_order_amount=$this->get_balance($row->order_id);
					$balance=$order_value-$cheque_amt;
					$orderdata=array('order_id'=>$row->order_id,'payment_id'=>$lid,'order_amount'=>$order_value,'recieved_amount'=>$cheque_amt,'balance'=>0,'pending_order_amount'=>$pending_order_amount);
					$this->db->insert('customer_order_to_payments',$orderdata);

					$leftbalance=$cheque_amt-$order_value;

				}



				$recd_payment=$leftbalance;
				
				if($recd_payment>0)
				{
				$sql = $this->db->select('a.id, a.invoice_no, b.id as quotation_id, c.gst_no as buyer_gst, e.gst as seller_gst')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('order_punch_tax_details c', 'c.order_id=a.id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('store_rack_location e', 'e.id=a.hpcl_billing_company')
						->where('b.order_punch', 1)
						->where('a.payment', 0)
						->where('a.billing', 1)
						->where('b.customer_id', $row->customer_id)
						->order_by('a.id')
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				
				foreach ($sql->result() as $row) {
					$order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$prev_payment=$this->customer_previous_payment($row->id);
					$order_amount=$order_amount-$prev_payment;

					if($order_amount>0) 
					{
					if($recd_payment > $order_amount) {

						
						$datas = array(
									   'payment' => 1,
									   'payment_id'=>$lid
									  );

						$this->db->where('id', $row->id)
								 ->update('order_punch', $datas);

						$sql2 = $this->db->select('a.id, b.payment_type, b.cheque_date')
										->from('order_punch a')
										->join('customer_payment_particulars b', 'b.payment_id=a.payment_id')
										->where('a.payment_id', $lid)
										->get();

						if($sql2->num_rows() > 0) {
							foreach($sql2->result() as $row2) {

								if($row2->payment_type == 1) {
									$data2 = array(
										   'expected_pdc_date' => $row2->cheque_date,
										   'cheque_no' => $cheque_no,
										   'received' => 1
										  );
									
								$this->db->where('order_id', $row2->id)
										 ->update('customer_cheque_details', $data2);
								}

							}
						}

						$pending_order_amount=$this->get_balance($row->id);

						$orderdata=array('order_id'=>$row->id,'payment_id'=>$lid,'order_amount'=>$order_amount,'recieved_amount'=>$order_amount,'balance'=>0,'pending_order_amount'=>$pending_order_amount);
						$this->db->insert('customer_order_to_payments',$orderdata);

						$recd_payment=$recd_payment-$order_amount;
				
						

					} else if($recd_payment < $order_amount) {


						$pending_order_amount=$this->get_balance($row->id);

						$balance=$order_amount-$recd_payment;

						$orderdata=array('order_id'=>$row->id,'payment_id'=>$lid,'order_amount'=>$order_amount,'recieved_amount'=>$recd_payment,'balance'=>$balance,'pending_order_amount'=>$pending_order_amount);
						$this->db->insert('customer_order_to_payments',$orderdata);
						break;

					} else if($recd_payment == $order_amount) {
					
						$datas = array(
									   'payment' => 1
									  );

						$this->db->where('id', $row->id)
								 ->update('order_punch', $datas);

								 $pending_order_amount=$this->get_balance($row->id);

								 $orderdata=array('order_id'=>$row->id,'payment_id'=>$lid,'order_amount'=>$order_amount,'recieved_amount'=>$recd_payment,'pending_order_amount'=>$pending_order_amount);
								 $this->db->insert('customer_order_to_payments',$orderdata);

								 break;
					}

				}
				$i++;
			}
			}


			}



			}

			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Customer/pending_for_pdc_recd');


		}else
		{

			$this->session->set_flashdata('message','<div class="alert alert-danger">Record Cannot be updated. Try again</div>');
			redirect(page_url.'Customer/pending_for_pdc_recd');

		}
	}


	function get_balance($order_id)
	{
		$balance=0;
		$Resteuy=$this->db->select('balance')->from('customer_order_to_payments')->where('order_id',$order_id)->order_by('id','DESC')->limit(1)->get();
		if($Resteuy->num_rows()>0){
			foreach($Resteuy->result() as $row);
				$prev_balance=$row->recieved_amount;
			
		}

		return $balance;

	}


	function get_Order_Amount_by_id()
	{
		$order_amount=0;
		$record_id=$this->uri->segment(3);
		$rest=$this->db->select('order_id')->from('customer_cheque_details')->where('id',$record_id)->get();
		if($rest->num_rows()>0)
		{

			foreach($rest->result() as $row);

			$order_id=$row->order_id;


			$query = $this->db->select('b.id as quotation_id,e.gst_no,c.gst')
			->from('order_punch a')
			->join('order_punch_tax_details e','a.id=e.order_id')
			->join('customer_quotation b', 'b.id=a.quotation_id')
			->join('store_rack_location c', 'c.id=a.hpcl_billing_company')	  
			->where('a.id', $order_id)
			->get();
			if($query->num_rows()>0)
			{

				foreach($query->result() as $rows);
				$order_amount=$this->salescrm->getOrderAmountWithGST($rows->quotation_id,$rows->gst_no,$rows->gst);

			}

		}


				echo $order_amount;

	}
	

	function getOrderAmountWithGST($quotation, $buyer_gst, $seller_gst)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

			$total_gst = 0;

		    if($buyer_gst<>'' && $seller_gst <>'') {
		        $bscode = substr($buyer_gst,0,2);
		        $sscode = substr($seller_gst,0,2);

		        if($bscode == $sscode) {
            		$gst = ($total_price*9)/100;
            		$total_gst = $gst + $gst;
		        } else {
		        	$gst = ($total_price*18)/100;
		        	$total_gst = $gst;
        		}
        	} else {
		        $igst=0;
		        $cgst=0;
		    }

		    $final_amt = $total_price + $total_gst;

		    return $final_amt;
	}


	function payment_term_approval()
	{

		
		$lead_data = array();
		$this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,d.first_name,d.last_name,a.added_on')->from('customer_detail a');
		$this->db->join('store_rack_location c','a.company_id=c.id');
		$this->db->join('system_users d','d.user_id=a.added_by','left');
		$this->db->where('a.payment_term_approval','0');
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			
			$edit = "<a href='javascript:;' class='btn btn-warning' 
			onclick='approve_terms(".$row->id.");'>Approve/Change</a>";



			if($row->payment_type == 2) {
				$payment_type = 'Payment Type: <strong>'.'Cash'.'</strong>';
				$credit_days = '';
			} else if($row->payment_type == 3) {
				$payment_type = 'Payment Type: <strong>'.'Online'.'</strong>';	
				$credit_days = '';
			} else if($row->payment_type == 4) {
				$payment_type = 'Payment Type: <strong>'.'PDC'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			} else if($row->payment_type == 5) {
				$payment_type = 'Payment Type: <strong>'.'Credit'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			}else if($row->payment_type == 6) {
				$payment_type = 'Payment Type: <strong>'.'Advance'.'</strong>';	
				$credit_days = '';
			} else {
				$payment_type = '';
				$credit_days = '';
			}

		

			$lead_data[] = array('sr_no'=>$i,
			'company_name'=>$row->companyname,
			'cust_company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'previous'=>'',
			'payment_term'=>$payment_type."<br/>".$credit_days,			
			'addedOn'=>date('d-M-Y',strtotime($row->added_on)),
			'addedBy'=>$row->first_name." ".$row->last_name,
			'action'=>$edit
			);		

			$i++;
		}




			$j=$i;

			$rt=$this->db->select('a.id,a.customer_id,b.customer_name,a.payment_type,a.credit_days,a.addedOn,a.addedBy,b.company_name,c.companyname,b.payment_type as current_type,b.credit_days as current_days,d.first_name,d.last_name')->from('payment_change_req a')->join('customer_detail b','a.customer_id=b.id')->join('store_rack_location c','b.company_id=c.id')->join('system_users d','a.addedBy=d.user_id')->where('a.status',0)->get();
			if($rt->num_rows()>0)
			{
				foreach($rt->result() as $rtt)
				{

					$edit = "<a href='javascript:;' class='btn btn-warning' 
					onclick='approve_terms_request(".$rtt->id.");'>Approve/Reject</a>";

					

					if($rtt->payment_type==2)
					{
					$payment="Cash";
					}else if($rtt->payment_type==3)
					{
					$payment="Online";
					}else if($rtt->payment_type==4)
					{
					$payment="PDC";
					}else if($rtt->payment_type==5)
					{
					$payment="Credit-".$rtt->credit_days." Days";
					}else if($rtt->payment_type==6)
					{
					$payment="Advance-".$rtt->credit_days." Days";
					}else
					{
					$payment='';

					}


					if($rtt->current_type==2)
					{
					$payment1="Cash";
					}else if($rtt->current_type==3)
					{
					$payment1="Online";
					}else if($rtt->current_type==4)
					{
					$payment1="PDC";
					}else if($rtt->current_type==5)
					{
					$payment1="Credit-".$rtt->current_days." Days";
					}else if($rtt->current_type==6)
					{
					$payment1="Advance-".$rtt->current_days." Days";
					}else
					{
					$payment1='';

					}


			$lead_data[] = array('sr_no'=>$j,
			'company_name'=>$rtt->companyname,
			'cust_company_name'=>$rtt->company_name,
			'customer_name'=>$rtt->customer_name,
			'previous'=>$payment1,
			'payment_term'=>$payment,			
			'addedOn'=>date('d-M-Y',strtotime($rtt->addedOn)),
			'addedBy'=>$rtt->first_name." ".$rtt->last_name,
			'action'=>$edit
			);
			$j++;
			}
			}

		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);

	}

	function get_payment_term()
	{
		$pt='';
		$cd='';
		$cname='';
		$id=$this->uri->segment(3);
		$restey=$this->db->select('company_name,payment_type,credit_days')->from('customer_detail')->where('id',$id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			echo $row->payment_type.'|'.$row->credit_days.'|'.$row->company_name;
		}else
		{
			echo $pt='';
		}
	}

	function approve_customer_payment_terms()
	{
		$custid=$this->input->post('payment_app_id');
		$payment_term=$this->input->post('payment_term');
		$credit_terms=$this->input->post('credit_terms');

		$data=array('payment_type'=>$payment_term,'credit_days'=>$credit_terms,'payment_term_approval'=>1,'payment_approved_On'=>date('Y-m-d H:i:s'),'payment_approved_By'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$custid);
		$this->db->update('customer_detail',$data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/common_approval');
	}

	function approve_customer_payment_terms_mainpage()
	{
		$custid=$this->input->post('payment_app_id');
		$payment_term=$this->input->post('payment_term');
		$credit_terms=$this->input->post('credit_terms');

		$data=array('payment_type'=>$payment_term,'credit_days'=>$credit_terms,'payment_term_approval'=>1,'payment_approved_On'=>date('Y-m-d H:i:s'),'payment_approved_By'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$custid);
		$this->db->update('customer_detail',$data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Customer/customerpendingforpaymenttermsapproval');
	}
	

	function get_requested_payment_term($id)
	{

		$pt='';
		$cd='';
		$cname='';
		$id=$this->uri->segment(3);
		$restey=$this->db->select('a.payment_type,a.credit_days,b.company_name')->from('payment_change_req a')->join('customer_detail b','a.customer_id=b.id')->where('a.id',$id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			echo $row->payment_type.'|'.$row->credit_days.'|'.$row->company_name;
		}else
		{
			echo $pt='';
		}

	}


	function approve_reject_payment_terms_change_req()
	{
		$id=$this->input->post('payment_app_id_r');
		$payment_term=$this->input->post('payment_term_r');
		$credit_terms=$this->input->post('credit_terms_r');
		$decision=$this->input->post('decision');
		$rej_remarks=$this->input->post('rej_remarks');

		$rt=$this->db->select('customer_id')->from('payment_change_req')->where('id',$id)->get();
		if($rt->num_rows()>0)
		{
			foreach($rt->result() as $rtt);

			$custid=$rtt->customer_id;
			if($decision==1)
			{

			$data=array('payment_type'=>$payment_term,'credit_days'=>$credit_terms,'payment_term_approval'=>1,'payment_approved_On'=>date('Y-m-d H:i:s'),'payment_approved_By'=>$_SESSION['logged_in']['user_id']);
			$this->db->where('id',$custid);
			$this->db->update('customer_detail',$data);

			}


			$updata=array('status'=>$decision,'approvedBy'=>$_SESSION['logged_in']['user_id'],'approvedOn'=>date('Y-m-d H:i:s'),'reject_remarks'=>$rej_remarks);

			$this->db->where('id',$id);
			$this->db->update('payment_change_req',$updata);
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/common_approval');
	}


	function approve_adjustments()
	{
		$order_id=$this->input->post('adjust_order_id');

		$decision_p=$this->input->post('decision_p');
		if($decision_p==2)
		{
		$rej_remarks_p=$this->input->post('rej_remarks_p');
		$payment=0;
		}else
		{
			$rej_remarks_p='';
			$payment=1;
		}


		$updata=array('payment'=>$payment,'adjustment_approval'=>$decision_p,'adjustment_approvalOn'=>date('Y-m-d H:i:s'),'adjustment_approval_remarks'=>$rej_remarks_p,'adjustment_approvalBy'=>$_SESSION['logged_in']['user_id']);

			$this->db->where('id',$order_id);
			$this->db->update('order_punch',$updata);

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/common_approval');

	}



	function discount_approval_list_user() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
						->where('b.added_by',$_SESSION['logged_in']['user_id'])
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";
					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		$sql = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('store_rack_location c', 'c.id=b.hpcl_company')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('b.added_by',$_SESSION['logged_in']['user_id'])
						->where('a.flag', 0)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->list_price != '' && $row->list_price > 0) {
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_lead_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
					}
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}


	function payment_term_approval_user()
	{

		
		$lead_data = array();
		$this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,d.first_name,d.last_name,a.added_on')->from('customer_detail a');
		$this->db->join('store_rack_location c','a.company_id=c.id');
		$this->db->join('system_users d','d.user_id=a.added_by','left');
		$this->db->where('a.payment_term_approval','0');
		$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			
			$edit = "<a href='javascript:;' class='btn btn-warning' 
			onclick='approve_terms(".$row->id.");'>Approve/Change</a>";



			if($row->payment_type == 2) {
				$payment_type = 'Payment Type: <strong>'.'Cash'.'</strong>';
				$credit_days = '';
			} else if($row->payment_type == 3) {
				$payment_type = 'Payment Type: <strong>'.'Online'.'</strong>';	
				$credit_days = '';
			} else if($row->payment_type == 4) {
				$payment_type = 'Payment Type: <strong>'.'PDC'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			} else if($row->payment_type == 5) {
				$payment_type = 'Payment Type: <strong>'.'Credit'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			}else if($row->payment_type == 6) {
				$payment_type = 'Payment Type: <strong>'.'Advance'.'</strong>';	
				$credit_days = '';
			} else {
				$payment_type = '';
				$credit_days = '';
			}

		

			$lead_data[] = array('sr_no'=>$i,
			'company_name'=>$row->companyname,
			'cust_company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'previous'=>'',
			'payment_term'=>$payment_type."<br/>".$credit_days,			
			'addedOn'=>date('d-M-Y',strtotime($row->added_on)),
			'addedBy'=>$row->first_name." ".$row->last_name,
			'action'=>$edit
			);



			

			$rt=$this->db->select('a.id,a.customer_id,b.customer_name,a.payment_type,a.credit_days,a.addedOn,a.addedBy,b.company_name,c.companyname,b.payment_type as current_type,b.credit_days as current_days,d.first_name,d.last_name')->from('payment_change_req a')->join('customer_detail b','a.customer_id=b.id')->join('store_rack_location c','b.company_id=c.id')->join('system_users d','a.addedBy=d.user_id')->where('a.status',0)->where('a.addedBy',$_SESSION['logged_in']['user_id'])->get();
			if($rt->num_rows()>0)
			{
				foreach($rt->result() as $rtt)
				{

					$edit = "<a href='javascript:;' class='btn btn-warning' 
					onclick='approve_terms_request(".$rtt->id.");'>Approve/Reject</a>";

					

					if($rtt->payment_type==2)
					{
					$payment="Cash";
					}else if($rtt->payment_type==3)
					{
					$payment="Online";
					}else if($rtt->payment_type==4)
					{
					$payment="PDC";
					}else if($rtt->payment_type==5)
					{
					$payment="Credit-".$rtt->credit_days." Days";
					}else if($rtt->payment_type==6)
					{
					$payment="Advance-".$rtt->credit_days." Days";
					}else
					{
					$payment='';

					}


					if($rtt->current_type==2)
					{
					$payment1="Cash";
					}else if($rtt->current_type==3)
					{
					$payment1="Online";
					}else if($rtt->current_type==4)
					{
					$payment1="PDC";
					}else if($rtt->current_type==5)
					{
					$payment1="Credit-".$rtt->current_days." Days";
					}else if($rtt->current_type==6)
					{
					$payment1="Advance-".$rtt->current_days." Days";
					}else
					{
					$payment1='';

					}


			$lead_data[] = array('sr_no'=>$i+1,
			'company_name'=>$rtt->companyname,
			'cust_company_name'=>$rtt->company_name,
			'customer_name'=>$rtt->customer_name,
			'previous'=>$payment1,
			'payment_term'=>$payment,			
			'addedOn'=>date('d-M-Y',strtotime($rtt->addedOn)),
			'addedBy'=>$rtt->first_name." ".$rtt->last_name,
			'action'=>$edit
			);
			}
			}

			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);

	}

	function unfollow_customer() {
		$user_id=$_SESSION['logged_in']['user_id'];
	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$customer_id = $this->input->post('customer_id');

		$sql = $this->db->select('b.id')
						->from('customer_quotation a')
						->join('order_punch b', 'b.quotation_id=a.id')
						->where('a.customer_id', $customer_id)
						->where('b.payment', 0)
						->get();

		if($sql->num_rows() > 0) {
			
			foreach ($sql->result() as $row) {
				$data = array(
							 'unfollow_customer' => 1,
							 'unfollow_added_on' => date('Y-m-d H:i:s'),
							 'unfollow_added_by' => $user_id
							 );

				$this->db->where('id', $row->id)
						 ->update('order_punch', $data);
			}
		}
	}

	 function get_customer_by_company(){
        if(isset($_GET['searchTerm']))
    {
    $searchtrm= $_GET['searchTerm'];
    }else
    {
      $searchtrm='';
    }
   // $compid = isset($_GET['compid']) ? $_GET['compid'] :'';
   

      
    $this->db->select('a.customer_alias,a.id,a.company_name, customer_name')
                ->from('customer_detail a')
                //->where('a.company_id', $compid)
                ->where('a.status','1');
                // ->join('fg_location_wise_stock b','a.id = b.fg_id','left')
    $this->db->like('a.company_name',$searchtrm,'both',false);
    $query =$this->db->get();


    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){

    	if($customer->customer_alias<>'')
    	{
    		$a="-".$customer->customer_alias;
    	}else
    	{
    		$a="";
    	}
    	
    $json[] = array('id'=>$customer->id, 'text'=>$customer->company_name.$a." ".$customer->customer_name);

    }
    }else{

    $json[] = array('id'=>"", 'text'=>"No Data Available");

    }

    echo json_encode($json);
  }
  function direct_customer(){
    $this->load->view('customer/direct_customer');
  }
  public function direct_customer_list()
  {
    $uri=$this->uri->segment(3);
    $lead_data = array();
    $this->db->select('a.*,b.state_name')->from('hpcl_direct_customer a');
    $this->db->join('states b','a.state=b.state_id','left');
  
    $query = $this->db->get();
    $res = $query->result();                  
    $i=1;
    foreach($res as $row)
    {
      $collection = $this->salescrm->get_customer_payment_collection($row->id);
      $customer_debit_credit = $this->get_customer_debit_credit($row->id);
      $customer_tq = $this->get_customer_tq($row->id);
      $edit = "<a href='".page_url."Customer/edit_direct_customer/".$row->id."'><i class='fa fa-pencil' title='Edit Customer'></i></a>";
      
      $payment = '<button class="btn btn-success btn-xs" onclick="payment('.$row->id.')" >Add Payment</button><br/><br/><button class="btn btn-warning btn-xs" onclick="tq_payment('.$row->id.')" >Add TQ Payment</button>';
      if($collection != ''){
        $debit_credit = '<button class="btn btn-success btn-xs" onclick="add_debit_credit('.$row->id.')" >Add Debit Credit</button>';
      }else{
        $debit_credit = '';
      }

      $lead_data[] = array('sr_no'=>$i,
        'customer_name'=>$row->customer_name,
      'customer_code'=>$row->customer_code,
      'tds'=>$row->tds,
      'address'=>$row->address,
      'gst'=>$row->gst,
      'tcs'=>$row->tcs,
      'state'=>$row->state_name,
      'tq'=>$customer_tq,
      'collection' => $collection,
      'customer_debit_credit' =>  $customer_debit_credit,
      'edit'=>$edit,
      'payment'=>$payment,
      'debit_credit' => $debit_credit
      );
      $i++;
    }
    //echo "<pre>"; print_r($compititor_data); exit;
      $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }
  

  function edit_direct_customer(){
    $this->load->view('customer/edit_direct_customer');
  }

  function update_direct_customer(){
    // print_r($this->input->post());

    if($this->input->post('payment_req') == 1) {
    	$payment_req = 1;
    } else {
    	$payment_req = 0;
    }

      $data=array(
        'customer_name'=>$this->input->post('customer_name'),
        'customer_code'=>$this->input->post('customer_code'),
        'tds'=>$this->input->post('tds'),
        'gst'=>$this->input->post('gst'),
        'tcs'=>$this->input->post('tcs'),
        'address'=>$this->input->post('address'),
        'state'=>$this->input->post('state'),
        'payment_req'=>$payment_req
       );

      $this->db->where('id',$this->input->post('direct_customer_id'));
      $this->db->update('hpcl_direct_customer',$data);

      redirect(page_url.'Customer/direct_customer');

  }
    function get_direct_customer(){
    $id = $this->input->post('id');
    $qry = $this->db->select('*')
                ->from('hpcl_direct_customer')
                ->where('id',$id)
                ->get();

    if($qry->num_rows() > 0){
      foreach($qry->result() as $row);
      echo $row->id. "|".$row->customer_name;
    }
  }

    function get_customer_debit_credit($id){
       $html = '';
            $sql = $this->db->select('credit_debit, credit_debit_for, credit_debit_amount')
                            ->from('customer_collection_credit_debit')
                            ->where('customer_id', $id)
                            ->get();

            if($sql->num_rows() > 0) {
                $html .= '<table class="table table-bordered" style="width:100%">
                                <tr style="background-color:#DADADA;">
                                    <th style="text-align:center; width:250px;">REASON</th>
                                    <th style="text-align:center;">CREDIT</th> 
                                    <th style="text-align:center;">DEBIT</th> 
                                </tr>';
                foreach ($sql->result() as $rows) {
                        $credit_amount = '';
                        $debit_amount = '';

                        if($rows->credit_debit == 1) {
                            $credit_amount = '<strong style="color:green;">+'.$rows->credit_debit_amount.'</strong>';
                            $debit_amount = '';
                        } else if($rows->credit_debit == 2) {
                            $credit_amount = '';
                            $debit_amount = '<strong style="color:red;">-'.$rows->credit_debit_amount.'</strong>';
                        }

                    $html .= '<tr>
                                <td style="text-align:center; width:250px;">'.$rows->credit_debit_for.'</td>
                                <td style="text-align:center;">'.$credit_amount.'</td> 
                                <td style="text-align:center;">'.$debit_amount.'</td> 
                                </tr>';
                }

                 $html .= '</table>';
            }

        return $html;
  }
  
  function unfollow_customer_for_payment()
  {
    $id = $this->uri->segment(3);
    $this->load->view('customer/unfollow_customer');
  }

  function unfollow_customer_list() {

    $user_id=$this->uri->segment(3);
    $currentday=date('Y-m-d');
    $lead_data = array();
     $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
              ->from('order_punch a')
              ->join('order_punch_mailing_details f','a.id=f.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b', 'b.id=a.quotation_id')
              ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
              ->join('customer_detail d', 'd.id=b.customer_id')
              ->where('a.payment_type',5)
              ->where('a.payment',0)
              ->where('a.unfollow_customer',1)
              ->where('a.send_to_tally',1);

              if($user_id<>'')
              {
                $this->db->where('a.agent',$user_id);
              }
             $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();


      $i=1;
      if($query->num_rows() > 0) {
        foreach($query->result() as $row) {
          $html='';
          $j=1;

          

          $html=$this->getproducts_detail($row->quotation_id);

          
          $j=1;

          
          if($row->payment_type == 2) {
            $payment_type = 'Cash';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.= '';
          } else if($row->payment_type == 3) {
            $payment_type = 'Online';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms .= '';
          } else if($row->payment_type == 4) {
            $payment_type = 'PDC';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 5) {
            $payment_type = 'CREDIT';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 6) {
            $payment_type = 'ADVANCE';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            
          } else {
            $payment_type = '';
            $payment_terms="";
            $payment_terms .= '';
          }

            if($row->credit_days!='')
            {
            $creditdays=$row->credit_days;
            }else
            {
            $creditdays=0;
            }

            $expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


            if(strtotime($currentday)>strtotime($expected_payment_days))
            {
            
            $diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            
          
            $order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
            $get_username = $this->salescrm->getusername($row->unfollow_added_by);

            $partial=$this->customer_previous_payment($row->id);
  
            $payment_due=$order_value-$partial;

            if($row->unfollow_customer == 0) {
              $do_not_followup = "<input type='checkbox' name='chk_unfollow' class='chk_unfollow".$row->id."' value='".$row->customer_id."' onchange='check_followup(".$row->id.")'>";
            } else {
              $do_not_followup = "ORDER UNFOLLOWED ON: <strong style='color:red;'>".date('d-m-Y H:i:s', strtotime($row->unfollow_added_on))."</strong><br>ORDER UNFOLLOWED BY: <strong style='color:red;'>".$get_username."</strong>";
            }

            $action = "<a href='javascript:;' class='btn btn-warning' onclick='revert_unfollow(".$row->id.");'>Revert</a>";

          $lead_data[] = array('sr_no'=>$i,

                      'agent'=>$row->first_name." ".$row->last_name,
                     'company_name'=>$row->companyname,
                     'invoice'=>$row->invoice_no,
                     'cust_company_name'=>"<strong>".$row->company_name."</strong>",
                     'customer_name'=>$row->customer_name,
                     'products'=>$html,
                     'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
                     'partial_payment'=>$partial,
                     'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
                     'payment_terms' =>$payment_terms,
                     'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
                     'payment_date' =>date('d-M-Y',strtotime($expected_payment_days)),
                     'exceeded_by' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>+".$exceed_days." Days</strong>",
                     'do_not_followup' => $do_not_followup,
                     'action' => $action
                    );
          $i++;
        }
        }
      }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }

  function unfollow_customer_for_user()
  {
    $id = $this->uri->segment(3);
    $this->load->view('customer/unfollow_customer_for_user');
  }


  function unfollow_customer_list_for_user() {

    $user_id=$this->uri->segment(3);
    $currentday=date('Y-m-d');
    $lead_data = array();
     $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
              ->from('order_punch a')
              ->join('order_punch_mailing_details f','a.id=f.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b', 'b.id=a.quotation_id')
              ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
              ->join('customer_detail d', 'd.id=b.customer_id')
              ->where('a.payment_type',5)
              ->where('a.payment',0)
              ->where('a.unfollow_customer',1)
              ->where('a.send_to_tally',1)
              ->where('a.agent',$_SESSION['logged_in']['user_id']);

              // if($user_id<>'')
              // {
              //   $this->db->where('a.agent',$user_id);
              // }
             $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();


      $i=1;
      if($query->num_rows() > 0) {
        foreach($query->result() as $row) {
          $html='';
          $j=1;

          

          $html=$this->getproducts_detail($row->quotation_id);

          
          $j=1;

          
          if($row->payment_type == 2) {
            $payment_type = 'Cash';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.= '';
          } else if($row->payment_type == 3) {
            $payment_type = 'Online';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms .= '';
          } else if($row->payment_type == 4) {
            $payment_type = 'PDC';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 5) {
            $payment_type = 'CREDIT';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 6) {
            $payment_type = 'ADVANCE';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            
          } else {
            $payment_type = '';
            $payment_terms="";
            $payment_terms .= '';
          }

            if($row->credit_days!='')
            {
            $creditdays=$row->credit_days;
            }else
            {
            $creditdays=0;
            }

            $expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


            if(strtotime($currentday)>strtotime($expected_payment_days))
            {
            
            $diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            
          
            $order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
            $get_username = $this->salescrm->getusername($row->unfollow_added_by);

            $partial=$this->customer_previous_payment($row->id);
  
            $payment_due=$order_value-$partial;

            if($row->unfollow_customer == 0) {
              $do_not_followup = "<input type='checkbox' name='chk_unfollow' class='chk_unfollow".$row->id."' value='".$row->customer_id."' onchange='check_followup(".$row->id.")'>";
            } else {
              $do_not_followup = "ORDER UNFOLLOWED ON: <strong style='color:red;'>".date('d-m-Y H:i:s', strtotime($row->unfollow_added_on))."</strong><br>ORDER UNFOLLOWED BY: <strong style='color:red;'>".$get_username."</strong>";
            }


            $action = "<a href='javascript:;' class='btn btn-warning' onclick='revert_unfollow(".$row->id." );'>Revert</a>";
            
          $lead_data[] = array('sr_no'=>$i,

                             'agent'=>$row->first_name." ".$row->last_name,
                     'company_name'=>$row->companyname,
                     'invoice'=>$row->invoice_no,
                     'cust_company_name'=>"<strong>".$row->company_name."</strong>",
                     'customer_name'=>$row->customer_name,
                     'products'=>$html,
                     'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
                     'partial_payment'=>$partial,
                     'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
                     'payment_terms' =>$payment_terms,
                     'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
                     'payment_date' =>date('d-M-Y',strtotime($expected_payment_days)),
                     'exceeded_by' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>+".$exceed_days." Days</strong>",
                     'do_not_followup' => $do_not_followup,
                     'action' => $action
                    );
          $i++;
        }
        }
      }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }
  

  function revert_unfollow_customer() {
    $user_id=$_SESSION['logged_in']['user_id'];
  
    date_default_timezone_set("Asia/Kolkata");
    $date =  date('Y-m-d H:i:s'); 
    $customer_id = $this->input->post('customer_id');

        $data = array(
               'unfollow_customer' => 0,
               'unfollow_added_on' => date('Y-m-d H:i:s'),
               'unfollow_added_by' => $user_id
               );

        $this->db->where('id', $customer_id)
             ->update('order_punch', $data);
      
  }

  function getHpclCompanies() {
    $searchtrm= $_GET['q'];

    $sql = $this->db->select('id, companyname')
    				->from('store_rack_location')
    				->where('status',1)
    				->like('companyname',$searchtrm,'both',false)
    				->get();


    if($sql->num_rows()>0) {
    	foreach($sql->result() as $row) {
    		$json[] = array('id'=>$row->id, 'text'=>$row->companyname);
    	}
    } else {
    		$json[] = array('id'=>"", 'text'=>"No Data Available");
    }

    echo json_encode($json);
  }

function customer_assignment(){

$customerid = $this->input->post('customer_id');
$userid = $this->input->post('employee_id');
$data = array('assigned_to'=>$userid);
$this->db->where('id',$customerid);
$this->db->update('customer_detail',$data);
echo "Customer Successfully assigned."; exit;

}

function customer_assignment_for_trail(){

$customerid = $this->input->post('customer_id');
$userid = $this->input->post('employee_id');
$data = array('assign_customer_for_trail'=>$userid);
$this->db->where('id',$customerid);
$this->db->update('customer_detail',$data);
echo "Customer Successfully assigned."; exit;

}

function customer_assignment_for_trail_for_new(){

$customerid = $this->input->post('customer_id');
$userid = $this->input->post('employee_id');
$data = array('assignedperson'=>$userid);
$this->db->where('id',$customerid);
$this->db->update('trial_to_be_sent',$data);
echo "Customer Successfully assigned."; exit;

}

function customer_assignment_for_trail_for_newone(){

$trailorderid = $this->input->post('trailorderid');
//echo $trailorderid; exit;
$approvalstatus = $this->input->post('approvalstatus');
$assigntousers = $this->input->post('assigntousers');
$approvalremarks = $this->input->post('approvalremarks');
//echo $approvalremarks; exit;

if($approvalstatus==1){
	$data= array('assignedperson'=>$assigntousers,
	'approved'=>$approvalstatus,
	'approval_addedby'=>$_SESSION['logged_in']['user_id'],
	'approval_addedon'=>date('Y-m-d H:i:s'));
	$this->db->where('id',$trailorderid);
	$this->db->update('trial_to_be_sent',$data);
}else{
	$data= array('approvalremarks'=>$approvalremarks,
	'approved'=>$approvalstatus,
	'assignedperson'=>0,
	'approval_addedby'=>$_SESSION['logged_in']['user_id'],
	'approval_addedon'=>date('Y-m-d H:i:s'));
	$this->db->where('id',$trailorderid);
	$this->db->update('trial_to_be_sent',$data);
}
$this->session->set_flashdata('message','<div class="alert alert-info">Thank You! Record successfully updated.</div>');
redirect(page_url.'Leads/common_approval');


}

public function getproductbatchcode()
	{
		$htm=''; 
		$htm.='<option value="" >Select Batch No.</option>';
		$cid=$this->input->post('proid');
		
		$res = $this->db->select('b.id, b.batch_no')->from('inventory_details a')->join('inventory_batch_no b','a.id=b.inv_detail_id')->where('a.product',$cid)->group_by('b.batch_no')->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row)
			{
			$htm.="<option value='".$row->id."'>".$row->batch_no."</option>";
			}
		}
		$htm.="";
		echo $htm; exit;
		 
	}

	function getassigned_user()
	{
		$html='';
		$id=$this->input->post('custid');
		$rest=$this->db->select('a.assigned_to,b.user_id,b.first_name,b.last_name')->from('customer_detail a')->join('system_users b','a.assigned_to=b.user_id')->where('id',$id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $res);
			$html.='<option value="'.$res->user_id.'">'.$res->first_name." ".$res->last_name.'</option>';
		}

		echo $html;
	}

	function agent_customers()
	{
		$this->load->view('customer/agent_customers');
	}


	public function customer_list_agent()
	{
		$agent_id=$this->uri->segment(3);
		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,c.companyname, d.credit_period as credit')->from('customer_detail a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('store_rack_location c','a.company_id=c.id','left');
		$this->db->join('credit_period d','d.id=a.credit_period','left');
		$this->db->where('a.status','1');
		if($agent_id!='')
		  {
		  $this->db->where('a.assigned_to',$agent_id);	
		  }
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			if($row->customer_distributor==1)
			{
				$types="Distributor";
			}else
			{
				$types="Direct Customer";
			}
			$edit = "<a href='".page_url."Customer/edit_customer/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			$q = $this->db->select('state_id, state_name')->from('states')->where('country_id','101')->where('state_id',$row->state)->get();

			if($q->num_rows() > 0) {
            	foreach($q->result() as $state);
            	$state_name = $state->state_name;
			} else {
				$state_name = '';
			}
			

			if($_SESSION['logged_in']['role']==1) {
				//$edit .= " | <a href='javascript:;' onclick='deleteLead(".$row->id.")'><i class='fa fa-trash' title='Delete Lead'></i></a>";
			}

			if($row->payment_type == 2) {
				$payment_type = '<strong>'.'Cash'.'</strong>';
				$credit_days = '';
			} else if($row->payment_type == 3) {
				$payment_type = '<strong>'.'Online'.'</strong>';	
				$credit_days = '';
			} else if($row->payment_type == 4) {
				$payment_type = '<strong>'.'PDC'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			} else if($row->payment_type == 5) {
				$payment_type = '<strong>'.'Credit'.'</strong>';	
				$credit_days = 'Credit Days: <strong>'.$row->credit_days.'</strong>';
			} else {
				$payment_type = '';
				$credit_days = '';
			}


			if($row->tds_appl==1)
			{
				$td="Yes-".$row->tds_per."%";
			}else
			{
				$td="No";
			}
			
			if($row->customer_type==1)
			{
				$type="Dormant";

			}else if($row->customer_type==2)
			{
				$type="No Followup";
			}else
			{
				$type="";
			}

			if($row->status==1)
			{
				$sta="<span class='btn btn-success'>Active</span>";
			}else
			{
				$sta="<span class='btn btn-danger'>Inactive</span>";
			}

			$q1 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',6)->where('user_status',1)->or_where('user_id',25)->where('user_status',1)->get();

			//$q2 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$row->assigned_to)->get();
			
				//foreach($q2->result() as $rows2);
					//$assignedpersonid = $rows2->user_id;
				
			$assigncustomer = "<select name='employee_name".$i."' id='employee_name".$i."' class='form-control' onChange='assign_to(".$row->id.",".$i.")'>";
				$assigncustomer.="<option value=''>Select User to Assign</option>";

				foreach($q1->result() as $rowss){
					if($row->assigned_to==$rowss->user_id){
						$selected = "selected";
					}else{
						$selected = "";
					}
			$assigncustomer.="<option value='".$rowss->user_id."' ".$selected.">".$rowss->first_name." " .$rowss->last_name."</option>";
				}

			$assigncustomer.="</select><div style='color:red; font-weight:bold;' id='successmessage".$row->id."'></div>";

$q11 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',7)->where('user_status',1)->where('user_status',1)->get();

			$trailassign = "<select name='employeename".$i."' id='employeename".$i."' class='form-control' onChange='assign_to_trail(".$row->id.",".$i.")'>";
				$trailassign.="<option value=''>Select User to Assign</option>";

				foreach($q11->result() as $rowssss){
					if($row->assign_customer_for_trail==$rowssss->user_id){
						$selected = "selected";
					}else{
						$selected = "";
					}
			$trailassign.="<option value='".$rowssss->user_id."' ".$selected.">".$rowssss->first_name." " .$rowssss->last_name."</option>";  
				}

			$trailassign.="</select><div style='color:red; font-weight:bold;' id='datasuccess".$row->id."'></div>";

			$uname=$this->salescrm->getusername($agent_id);
			//$remarks = substr($row->remarks,0,50);

			$html='';
			$d=$this->db->select('a.product_id,b.instruments_name')->from('customer_product_used a')->join('presto_instruments b','a.product_id=b.id')->where('a.customer_id',$row->id)->get();
			if($d->num_rows()>0)
			{
				foreach($d->result() as $dd)
				{
				$html.='<div style="width:150px;">'.$dd->instruments_name.'</p>';
				}

			}

			$html.='<div style="width:150px;"><a href="'.page_url.'Customer/add_product_for_customer/'.$row->id.'" class="btn btn-danger">Add Product</a></p>';
			$lead_data[] = array('sr_no'=>$i,
			'product_data_bank'=>"<strong style='font-weight:bold;'>".$types."<strong>",
			'status'=>$sta,
			'assigncustomer'=>$assigncustomer,
			'trailassign'=>$trailassign,
			'company'=>$row->companyname,
			'agent'=>$uname,
			'company_name'=>$row->company_name,
			'gst'=>$row->gst,
			'address'=>$row->address,
			'contactperson'=>$row->customer_name,
			'email'=>$row->email,
			'mobile'=>$row->contact_no,
			'order_max_limit'=>$row->order_max_limit,
			'credit_period'=> $payment_type.'<br>'.$credit_days,
			'credit_limit'=>$row->credit_limit,
			'msme_number'=>$row->msme_number,
			'country'=>$row->country_name,
			'state'=>$state_name,
			'city'=>$row->city,
			'pincode'=>$row->pincode,
			'tds'=>$td,
			'type'=>"<strong style='color:red;font-weight:bold;'>".$type."</strong>"
			);
			$i++;
		}
		//echo "<pre>"; print_r($compititor_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function getallcustomer_product()
	{
		$r=$this->db->select('id')->from('customer_detail')->where('status',1)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $rr)
			{

				$rt=$this->db->select('a.id,b.product_id')->from('customer_quotation a')->join('customer_quotation_detail b','a.id=b.quotation_id')->where('a.customer_id',$rr->id)->get();
				if($rt->num_rows()>0)
				{
					foreach($rt->result() as $row)
					{
						$dup=$this->checkduplicate($row->product_id,$rr->id);
						if($dup==0)
						{

							$data=array('customer_id'=>$rr->id,'product_id'=>$row->product_id,'addedOn'=>date('Y-m-d'));
							$this->db->insert('customer_product_used',$data);
						}

					}


				}


			}
		}
	}


	function checkduplicate($prd,$cust)
	{

		$rest=$this->db->select('id')->from('customer_product_used')->where('customer_id',$cust)->where('product_id',$prd)->get();
		return $rest->num_rows();

	}

	function add_product_for_customer()
	{
		$this->load->view('customer/add_product_for_customer');
	}

	function getproduct()
	{

	
    $searchtrm= $_GET['q'];
   
     

      
	$this->db->select('a.id,a.instruments_name,a.model_number,a.pack_size')
	->from('presto_instruments a')
	->like('a.instruments_name',$searchtrm,'both',false)
	->where('a.status','1');

	$query =$this->db->get();


    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){

    $json[] = array('id'=>$customer->id, 'text'=>$customer->instruments_name."-".$customer->model_number."-".$customer->pack_size);

    }
    }else{

    $json[] = array('id'=>"", 'text'=>"No Data Available");

    }

    echo json_encode($json);
	}

	function update_product_add_customer()
	{
			$cust=$this->uri->segment(3);
			$product=$this->input->post('product');
			if(count($product)>0)
			{

			for($i=0;$i<count($product);$i++)
			{


			$dup=$this->checkduplicate($product[$i],$cust);
			if($dup==0)
			{

			$data=array('customer_id'=>$cust,'product_id'=>$product[$i],'addedOn'=>date('Y-m-d'));
			$this->db->insert('customer_product_used',$data);
			}

			}

			}

		$this->session->set_flashdata('message','<div class="alert alert-info">Product Added.</div>');
		redirect(page_url.'Customer/agent_customers/'.$_SESSION['logged_in']['user_id']);
	}

	function delete_mapped_product($rowid)
	{
		$this->db->where('id',$rowid);
		$this->db->delete('customer_product_used');

		$this->session->set_flashdata('message','<div class="alert alert-info">Product Added.</div>');
		redirect(page_url.'Customer/agent_customers/'.$_SESSION['logged_in']['user_id']);

	}


	function add_existing_customer_order()
	{
		$proceed_flag=array();
		$proceed_flag[]=0;
		$quotation_id=$this->uri->segment(3);
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$company_id = $this->input->post('company');

		$edit_id=$this->input->post('editid');
			for($i=0;$i<count($edit_id);$i++)
			{
				$edid=$edit_id[$i];
				$product = $this->input->post('productedit'.$edid);
				$qty = $this->input->post('qtyedit'.$edid);
				$for_new_product=$this->salescrm->checkforavailable_Company_QTY_single($product,$qty,$company_id);
				$new_data=explode('~',$for_new_product);
				$proceed_flag[]=$new_data[0];
			}

			//echo "<pre>"; print_r($proceed_flag); exit;
			if(array_sum($proceed_flag)==0)
			{	

			$unique_no=$this->getinvoice_no_new($company_id);
			$seller_gst=$this->salescrm->getSellerGst($company_id);
			$customer_id = $this->input->post('customer');
			$insert_id = $quotation_id;


		/** QUOTEE UPDATE **/
			$edit_id=$this->input->post('editid');
			for($i=0;$i<count($edit_id);$i++)
			{
			$edid=$edit_id[$i];

			$qty = $this->input->post('qtyedit'.$edid);
			$discount = $this->input->post('agreed_price_edit'.$edid);
			$product = $this->input->post('productedit'.$edid);
			$flag = 1;
			$data1 = array(
			'qty' => $qty,
			'net_price'=>$discount,
			'agreed_price' => $discount,
			'flag' => $flag,
			);
			//echo "<pre>"; print_r($data1); exit;
			$this->db->where('id',$edid);
			$this->db->update('customer_quotation_detail',$data1);

			/** update databank **/
			$dup=$this->checkduplicate($product,$customer_id);
			if($dup==0)
			{
			$data_for_prd=array('customer_id'=>$customer_id,'product_id'=>$product,'addedOn'=>date('Y-m-d'));
			$this->db->insert('customer_product_used',$data_for_prd);
			}


			/** end **/
			}

			/** end **/
		
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
				$start_date=$financial['start_date']." 00:00:00";
				$end_date=$financial['end_date']." 23:59:59";
			}else
			{
				$start_date=date('Y-04-01')." 00:00:00";
				$end_date=date('Y-m-d')." 23:59:59";
			}
			
			$pic = $_FILES['upload_file']['name'];

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = '';
			 }

			$sql = $this->db->select('generated_order_id')
							->from('order_punch')
							->where('added_on>=',$start_date)
							->where('added_on<=',$end_date)
							->order_by('id', 'DESC')
							->limit(1)
							->get();

				if ($sql->num_rows() > 0) {
					foreach ($sql->result() as $row);	
						$generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
				} else {
						$generated_order_id = '001';
				}

				if($this->input->post('check_freight') == 1) {
					$freight = 1;
				} else {
					$freight = 0;
				}

				$sales_order_no=$this->getsales_no_new();

		$data2 = array(
					 'quotation_id' => $insert_id,
					 'source'=>$this->input->post('source'),
					 'agent'=>$this->input->post('agent'),
					 'invoice_no' =>0,
					 'sales_order_no'=>$sales_order_no,
					 'salesorderstart'=>1,
					'sales_order_addedOn'=>date('Y-m-d H:i:s'),
					 'direct_order' => 1,
					 'upload_po' => $newname,
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'hpcl_billing_company' => $this->input->post('company'),
					 'freight' => $freight,
					 'freight_amount' => $this->input->post('freight_amt'),
					 'generated_order_id' => $generated_order_id,
					 'added_on' => date('Y-m-d H:i:s'),
					 'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		// echo "<pre>";print_r($data);exit;

		$this->db->insert('order_punch', $data2);
		$order_id = $this->db->insert_id();


		$sql3 = $this->db->select('address, contact_no')
						 ->from('customer_detail')
						 ->where('id', $customer_id)
						 ->get();

		// if($sql3->num_rows() > 0) {
		// 	foreach($sql3->result() as $row4);
		// 		if($row4->address == '' || $row4->contact_no == '') {
		// 			$data3 = array(
								  
		// 						   'address' => $this->input->post('billing_address'),
		// 						   'contact_no' => $this->input->post('billing_mobile_no'),
		// 						   'state' => $this->input->post('billing_state'),
		// 						   'email' => $this->input->post('billing_email'),
		// 						   'city' => $this->input->post('billing_city'),
		// 						   'pincode' => $this->input->post('billing_pincode')
		// 						  );

		// 			$this->db->where('id', $customer_id)
		// 					 ->update('customer_detail', $data3);
		// 		}
		// }

			$data4 = array(
						 'create_date' => date('Y-m-d'),
						 'order_id' => $order_id,
						 'company' => $company_id,
						 'customer' => $customer_id,
						 // 'total_amount' => $this->input->post('total_amount'),
						 'bank_name' => '',
						 'total_collection' => 0
						 );
	
			$this->db->insert('customer_cheque_collection', $data4);
			$collection_id = $this->db->insert_id();

			$datas5 = array(
						 'collection_id' => $collection_id,
						 'cheque_amt' => 0,
						 'cheque_bank_name' => ''
						 );

			$this->db->insert('customer_cheque_collection_details', $datas5);

			if($this->input->post('check_billing') == 1) {
				$check_billing = 1;
			} else {
				$check_billing = 0;
			}

			// if($this->input->post('ttype')==1)
			// {
			// 	$transport_type=$this->input->post('ttype');
			// 	$vehicle_no=$this->input->post('self_vehicle_no');
			// 	$transporter_id=0;
			// 	$transporter_mobile=0;
			// 	$transporter_address='';
			// 	$trans_rate_type=0;
			// 	$trans_rate=0;
				
			// }else if($this->input->post('ttype')==2)
			// { 
			// 	$transport_type=2;
			// 	$vehicle_no=$this->input->post('vehicle_no');
			// 	$sql = $this->db->select('id')
			// 	->from('transporter_details')
			// 	->where('id', $this->input->post('transporter_name'))
			// 	->get();
			// 	if($sql->num_rows() == 0) {
			// 	$datas = array(
			// 	'name' => $this->input->post('transporter_name'),
			// 	'mobile_no' => $this->input->post('tmobile'),
			// 	'address' => $this->input->post('taddress')
			// 	);
			// 	$this->db->insert('transporter_details', $datas);
			// 	$transporter_id = $this->db->insert_id();
			// 	}else
			// 	{
			// 	$transporter_id=$this->input->post('transporter_name');
			// 	}

			// 	$transporter_mobile=$this->input->post('tmobile_no');
			// 	$transporter_address=$this->input->post('taddress');
			// 	$trans_rate_type=$this->input->post('rate_type');
			// 	$trans_rate=$this->input->post('transport_rate');

	

			// }

		$data_m = array(
					 'order_id' => $order_id,
					 'ship_to' => $this->input->post('ship_to'),
					 'shipping_address' => $this->input->post('shipping_address'),
					 'shipping_state' => $this->input->post('shipping_state'),
					 'shipping_city' => $this->input->post('shipping_city'),
					 'shipping_pincode' => $this->input->post('shipping_pincode'),
					 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
					 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
					 'shipping_email' => $this->input->post('shipping_email'),
					 'same_shipping_billing' => $check_billing,
					 'bill_to' => $this->input->post('bill_to'),
					 'billing_name' => $this->input->post('billing_name'),
					 'billing_address' => $this->input->post('billing_address'),
					 'billing_state' => $this->input->post('billing_state'),
					 'billing_city' => $this->input->post('billing_city'),
					 'billing_pincode' => $this->input->post('billing_pincode'),
					 'billing_phone_no' => $this->input->post('billing_phone_no'),
					 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
					 'billing_email' => $this->input->post('billing_email')
					 // 'transport_type'=>$transport_type,
					 // 'transporter_id'=>$transporter_id,
					 // 'transporter_mobile'=>$transporter_mobile,
					 // 'transporter_address'=>$transporter_address,
					 // 'rate_type'=>$trans_rate_type,
					 // 'rate'=>$trans_rate,
					 // 'vehicle_no'=>$vehicle_no,
					 // 'vehicle_type' => $this->input->post('vehicle_type'),
					 // 'destination' => $this->input->post('destination')
					 );

		$this->db->insert('order_punch_mailing_details', $data_m);

		$data_t = array(
					 'order_id' => $order_id,
					 'msme_no' => $this->input->post('msme_no'),
					 'pan_no' => $this->input->post('pan_no'),
					 'registration_type' => $this->input->post('registration_type'),
					 'gst_no' => $this->input->post('gst_no')
					 );

		$this->db->insert('order_punch_tax_details', $data_t);

		$data_p = array(
					 'order_id' => $order_id,
					 'reference' => $this->input->post('reference'),
					 'note' => $this->input->post('note')
					 );

		$this->db->insert('order_punch_payment_details', $data_p);

		$data_upd = array(
						 'order_punch' => 1
						 );

		$this->db->where('id', $insert_id)
				 ->update('customer_quotation', $data_upd);


				 /** CHECK IF CUSTOMER DETAIL IS EMPTY THEN UPDATE THE DATA **/

				$resteye= $this->db->select('email,gst,pan,ship_address,ship_pincode,ship_email,ship_state,ship_city,bill_address,bill_state,bill_city,bill_pincode,bill_email,msme_number')->from('customer_detail')->where('id',$customer_id)->get();
				if($resteye->num_rows()>0)
				{
					foreach($resteye->result() as $curow);
					if($curow->email=='' || $curow->email==0 || $curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->gst=='' || $curow->gst==0)
					{
						$darray=array('gst'=>$this->input->post('gst_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->pan=='' || $curow->pan==0)
					{
						$darray=array('pan'=>$this->input->post('pan_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_address=='' || $curow->ship_address==0)
					{
						$darray=array('ship_address'=>$this->input->post('shipping_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_pincode=='' || $curow->ship_pincode==0)
					{
						$darray=array('ship_pincode'=>$this->input->post('shipping_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_email=='' || $curow->ship_email==0)
					{
						$darray=array('ship_email'=>$this->input->post('shipping_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_state=='' || $curow->ship_state==0)
					{
						$darray=array('ship_state'=>$this->input->post('shipping_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->ship_city=='' || $curow->ship_city==0)
					{
						$darray=array('ship_city'=>$this->input->post('shipping_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}


					if($curow->bill_address=='' || $curow->bill_address==0)
					{
						$darray=array('bill_address'=>$this->input->post('billing_address'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_state=='' || $curow->bill_state==0)
					{
						$darray=array('bill_state'=>$this->input->post('billing_state'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_city=='' || $curow->bill_city==0)
					{
						$darray=array('bill_city'=>$this->input->post('billing_city'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_pincode=='' || $curow->bill_pincode==0)
					{
						$darray=array('bill_pincode'=>$this->input->post('billing_pincode'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->bill_email=='' || $curow->bill_email==0)
					{
						$darray=array('bill_email'=>$this->input->post('billing_email'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->msme_number=='' || $curow->msme_number==0)
					{
						$darray=array('msme_number'=>$this->input->post('msme_no'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}

					if($curow->payment_type==0 || $curow->payment_type=='')
					{
						$darray=array('payment_type' => $this->input->post('payment_type'),'credit_days' => $this->input->post('paymentterms'));
						$this->db->where('id',$this->input->post('customer'));
						$this->db->update('customer_detail',$darray);
					}
				}

							$darray1=array('customer_ref_no'=>$this->input->post('customer_codes'));
							$this->db->where('id',$this->input->post('customer'));
							$this->db->update('customer_detail',$darray1);

				 /** END **/
 

		$ptype=$this->input->post('payment_type');
		if($ptype==4)
		{

				$credit_days = $this->input->post('paymentterms');
				$expected_pdc_date = date('Y-m-d', strtotime('+'.$credit_days.' days'));
				$original_cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
				$pdcrecv = $this->input->post('pdcrecv');

				if($pdcrecv==1)
				{
					$hold_due_to_pdc=0;
					if(strtotime($original_cheque_date)>strtotime($expected_pdc_date))
					{
						$hold_due_to_pdc=1;
					}

					$recieved=1;

					$cheque_no=$this->input->post('cheque_no');

				}else
				{
					$recieved=0;
					$hold_due_to_pdc=0;
					$cheque_no='';
				}

					$data_chq = array(
					'order_id' => $order_id,
					'customer_id' => $customer_id,
					'expected_pdc_date' => $original_cheque_date,
					'cheque_no'=>$cheque_no,
					'received' => $recieved,
					'deposited' => 0,
					'order_punch_date' =>  date('Y-m-d H:i:s'),
					'hold_due_to_pdc'=>$hold_due_to_pdc
					);

			
				$this->db->insert('customer_cheque_details', $data_chq);

				// echo "<pre>";print_r($data_chq);exit;
		
		}

		if($ptype==6)
		{
			$cheque_no=$this->input->post('cheque_no');
			$cheque_date=date('Y-m-d', strtotime($this->input->post('cheque_date')));
			$order_amount = $this->salescrm->getOrderAmountWithGST($insert_id, $this->input->post('gst_no'), $seller_gst);

			$data2 = array(
						   'customer_id' => $customer_id,
						   'type' => 2,
						   'bills' => "'$order_id'",
						   'addedOn' => date('Y-m-d H:i:s'),
						   'addedBy' => $this->session->userdata['logged_in']['user_id']
						  );

			$this->db->insert('customer_payments', $data2);
			$payment_id = $this->db->insert_id();

			$data3 = array(
						   'payment_id' => $payment_id,
						   'payment_type' => 1,
						   'cheque_no' => $cheque_no,
						   'cheque_date' => $cheque_date,
						   'amount' => $order_amount
						  );

			$this->db->insert('customer_payment_particulars', $data3);

			$data_chq = array(
					'order_id' => $order_id,
					'customer_id' => $customer_id,
					'expected_pdc_date' => $cheque_date,
					'cheque_no'=>$cheque_no,
					'received' => 1,
					'deposited' => 0,
					'order_punch_date' =>  date('Y-m-d H:i:s'),
					'hold_due_to_pdc' => 0
					);

			
				$this->db->insert('customer_cheque_details', $data_chq);
		
		}

		$mul_company = $this->input->post('mul_company');

		for($k=0; $k<count($mul_company);$k++) {
			if($mul_company[$k] != '') {
				$checkIfCustomerCompanyExists = $this->salescrm->checkIfCustomerCompanyExists($customer_id, $mul_company[$k]);

				// echo $checkIfCustomerCompanyExists;exit;

				if($checkIfCustomerCompanyExists == 0) {
					$getCustomerAllDetails = $this->salescrm->getCustomerAllDetails($customer_id);

					if($getCustomerAllDetails != '') {
					   foreach($getCustomerAllDetails as $row9);

							$data9 = array(
								   'company_id' => $mul_company[$k],
								   'customer_ref_no' => $row9->customer_ref_no,
								   'title' => $row9->title,
								   'customer_name' => $row9->customer_name,
								   'email' => $row9->email,
								   'contact_no' => $row9->contact_no,
								   'country' => $row9->country,
								   'state' => $row9->state,
								   'city' => $row9->city,
								   'gst' => $row9->gst,
								   'pan' => $row9->pan,
								   'address' => $row9->address,
								   'status' => $row9->status,
								   'company_name' => $row9->company_name,
								   'alt_contact' => $row9->alt_contact
								  );

							$this->db->insert('customer_detail', $data9);

					}
				}
			}
		}


		$data_upd = array(
						 'order_punch' => 1
						 );

		$this->db->where('id', $insert_id)
				 ->update('customer_quotation', $data_upd);



		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order Successfully Generated.</div>');
			// redirect(page_url.'Customer/discount_approval/'.$insert_id);
			redirect(page_url.'Customer/quotation_dashboard');
		}else
		{
			echo "<strong style='color:red;font-weight:bold;'>REQUIRED STOCK IS NOT AVAIABLE FOR SOME ITEMS<BR/>TRY AGAIN</strong>"; exit;

		}
	}


	function running_customer_quotation_dashboard()
	{
		$this->load->view('customer/running_customer_quotes');
	}


	function quotation_running_report()
	{	
		$user_role =$this->session->userdata['logged_in']['role'];
		
		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user_id=$this->uri->segment(5);
		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('leads d','a.lead_id=d.id');
		$this->db->join('store_rack_location b','a.company_id=b.id', 'left');
		$this->db->join('customer_detail c','a.customer_id=c.id', 'left');
		$this->db->where('d.create_date>=',date('Y-m-d', strtotime($start_date)));
		$this->db->where('d.create_date<=',date('Y-m-d', strtotime($end_date)));
		$this->db->where('a.lead_id',0);
		if($user_id !='' && $user_id<>'ALL')
		{
			$this->db->where('d.added_by',$user_id);
			
		}
		$this->db->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html=$this->getproducts_detail($row->id);
			$j=1;


			if($row->lead_id==0)
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/quotation.php?quotation_id=".$row->id."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";
			}else
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$row->lead_id."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";

			}
			
				$query = $this->db->select('id')
				->from('order_punch')
				->where('quotation_id', $row->id)
				->get();

				if($query->num_rows() == 0) {
				$checkIfProductIsApproved = $this->master->checkIfProductIsApproved($row->id);
				if ($checkIfProductIsApproved == 0)
				{
				$generate_order = "<a  class='btn btn-danger btn-xs'>ORDER NOT GENERATED YET</a>";
				}else
				{
				$generate_order='<span style="color: red; font-weight: bold;font-size: 16px; text-align:center;">One or more products are not Approved/Rejected. Hence, Quotation cannot be sent</span>';
				}
				}else
				{
					$generate_order = "<a  class='btn btn-success btn-xs'>ORDER GENERATED</a>";
				}

				$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";

				$agent_name=$this->salescrm->getusername($row->added_by);
				$lead_data[] = array('sr_no'=>$i,
				'salesagent'=>$agent_name,
				'create_date'=>date('d-m-Y', strtotime($row->added_on)),
				'company'=>$row->companyname,
				'company_name'=>$row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->address,
				'products'=>$html,	
				'quotation' => $view,
				'edit' => $edit,
				'generate_order' => $generate_order
				);
			$i++;
			}
		
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function filter_running_quote()
	{
		$firstdate=date('Y-m-d',strtotime($this->input->post('firstdate')));
		$lastdate=date('Y-m-d',strtotime($this->input->post('lastdate')));
		$user=$this->input->post('user');

		redirect(page_url.'Customer/running_customer_quotation_dashboard/'.$firstdate.'/'.$lastdate.'/'.$user);
	}



	function getcustomerprevious_quote_details()
	{
		$html = "<table class='table table-bordered'><thead><tr><th colspan='2' style='text-align:center'>SHOWING LAST 2 QUOTATION/ORDERS</th></tr><tr><th>Quotation ID</th><th>Quotation Date</th><th>Products</th></tr></thead><tbody>";

		$custid=$this->input->post('custid');
		$q = $this->db->select('id, added_on, lead_id, order_punch')->from('customer_quotation')->where('customer_id',$custid)->order_by('id','DESC')->limit(2)->get();
		foreach($q->result() as $row){
			$quotationid = "";
			if($row->lead_id==0){

			$quotationid = "<a href='".page_url."Customer/preview_pdf_quote/".$row->id."' target='_blank'><span class='btn btn-success btn-xs'>View Quotation</span></a>";
			}else{
				$quotationid = "<a href='".page_url."Leads/preview_pdf_quote/".$row->lead_id."' target='_blank'><span class='btn btn-success btn-xs'>View Quotation</span></a>";
			}
			$orderpunch = "";
			$addedondate = $row->added_on;
			if($row->order_punch==1){
				$orderpunch = "<span style='color:green; font-weight:bold'>Order Generated</span>";
			}else{
				$orderpunch = "<span style='color:red; font-weight:bold'>Order Not Generated</span>";
			}
			$html.= "<tr>
				<td>".$quotationid ."</td>
				<td>".date('d-m-Y',strtotime($addedondate))."<br><br>".$orderpunch."</td>
				<td>
					<table class='table table-bordered'>
						<thead>
							<tr>
								<th>SR NO</th>
								<th>Product Name</th>
								<th>Quoted Price</th>
								<th>Agreed Price</th>
							</tr>

						</thead>
						<tbody>";

						$i=1;
						$q1= $this->db->select('b.instruments_name, a.list_price, a.agreed_price')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.quotation_id',$row->id)->get();
						foreach($q1->result() as $rows){

						$html.="<tr>
						<td>".$i."</td>
						<td>".$rows->instruments_name."</td>
						<td>".$rows->list_price."</td>
						<td>".$rows->agreed_price."</td>
						</tr>";
						$i++;
						}

						$html.="</tbody>
					</table>
				</td>
			</tr>";

			

		}


echo $html; exit;
		

	}

function getproductunitdata(){
	$productid = $this->input->post('productid');
	$q=$this->db->select('unit')->from('presto_instruments')->where('id',$productid)->get();
	foreach($q->result() as $row);
	echo $row->unit; exit;
}

function mark_active_inactive()
{
	$id=$this->uri->segment(3);
	$status=$this->uri->segment(4);
	$uri=$this->uri->segment(5);

	$d=array('status'=>$status);
	$this->db->where('id',$id);
	$this->db->update('customer_detail',$d);

$this->session->set_flashdata('message','<div class="alert alert-info">Status Changed.</div>');
	redirect(page_url."Customer/customer_view/".$uri);

}

function filter_overdue_payments()
{
	$seg=$this->uri->segment(3);
	$comp=$this->input->post('company');
	$overdue=$this->input->post('overdue');
	if($seg!='')
    {
      $u=$seg;
    }else
    {
      $u="NA";
    }


    redirect(page_url."Customer/payment_overdue/".$u.'/'.$comp.'/'.$overdue);



}

function customer_not_taken_product()
{
	$this->load->view('approval/customer_not_taken_product');
}

function customer_not_taking_product_list()
{


	$uri=$this->uri->segment(3);
		$lead_data = array();
		$this->db->select('a.*')->from('customer_detail a');
		$this->db->where('a.customer_type',0);
		if($this->uri->segment(3)<>'')
		{
			$this->db->where('a.assigned_to',$this->uri->segment(3));
		}
		$this->db->order_by('a.company_name','ASC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			$curr=date('Y-m-01')." 00:00:00";
			$curr1=date('Y-m-t')." 23:59:59";

			$purr=date('Y-m-01',strtotime('-1 month'))." 00:00:00";
			$purr1=date('Y-m-t',strtotime('-1 month'))." 00:00:00";
			
			$prev_month=$this->this_monthorderedOn($purr,$purr1,$row->id);
			$thismonth=$this->this_monthorderedOn($curr,$curr1,$row->id);

			if($prev_month==1 && $thismonth==0)
			{
			$ldata=$this->getlastdata($row->id,date('Y-m-01'));
			$rty=explode('|',$ldata);
			 $last=$this->last_order_and_product_using($row->id);
			 $username=$this->salescrm->getusername($row->assigned_to);
			$df=explode('~',$last);
			$lead_data[] = array('sr_no'=>$i,
			'customer'=>$row->company_name,
			'lastorder'=>$df[0],
			'productusing'=>$df[1],
			'assignedto'=>$username,
			'lastaction'=>$rty[0]."<br/>".$rty[1],
			'remarks'=>"<div class='col-md-12'><textarea id='remarks".$row->id."' style='resize:none' class='form-control' onblur='save_data_remarks(".$row->id.");'>".$rty[0]."</textarea></div>"
			);
			$i++;
			}
		}
		
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($lead_data),
		"iTotalDisplayRecords" => count($lead_data),
		"aaData"=>$lead_data);
			
		echo json_encode($results);
}

function this_monthorderedOn($curr,$curr1,$customer_id)
{

	$data=array();
	$sql31 = $this->db->select('a.id,c.invoice_no,d.customer_name,c.send_to_tally_On,d.company_name')
	->from('customer_quotation_detail a')
	->join('customer_quotation b', 'b.id=a.quotation_id')
	->join('order_punch c', 'c.quotation_id=b.id')
	->join('customer_detail d', 'd.id=b.customer_id')
	->where('b.customer_id', $customer_id)
	->where('c.send_to_tally',1)
	->where('c.cancelled',0)
	->where('c.added_on>=',$curr)
	->where('c.added_on<=',$curr1)
	->order_by('c.id','DESC')
	->get();
	return $sql31->num_rows();

 
}

function last_order_and_product_using($id)
{
	$d='';
	$sql31 = $this->db->select('c.added_on')
	->from('customer_quotation_detail a')
	->join('customer_quotation b', 'b.id=a.quotation_id')
	->join('order_punch c', 'c.quotation_id=b.id')
	->join('customer_detail d', 'd.id=b.customer_id')
	->where('b.customer_id', $id)
	->where('c.send_to_tally',1)
	->where('c.cancelled',0)
	->order_by('c.id','DESC')
	->get();
	if($sql31->num_rows()>0)
	{
		foreach($sql31->result() as $sqlnu);
		$d=date('d-m-Y',strtotime($sqlnu->added_on));
	}

	$productusing=$this->productused($id);

	return $d."~".$productusing;

}

function productused($id)
{
	$da=array();
		$sql31 = $this->db->select('b.instruments_name')
	->from('customer_quotation_detail a')
	->join('customer_quotation c', 'c.id=a.quotation_id')
	->join('presto_instruments b', 'b.id=a.product_id')
	->where('c.customer_id',$id)
	->group_by('a.product_id')->get();
	if($sql31->num_rows()>0)
	{
		foreach($sql31->result() as $sql31n)
		{
			$da[]=$sql31n->instruments_name;
		}
	}

	if(count($da)>0)
	{
		$prd=implode(',',$da);
	}else
	{
		$prd='-';
	}

return $prd;
}



function get_customer_tq($id){
       $html = '';
            $sql = $this->db->select('*')
                            ->from('customer_tq')
                            ->where('customer_id', $id)
                            ->get();

            if($sql->num_rows() > 0) {
                $html .= '<table class="table table-bordered" style="width:100%">
                                <tr style="background-color:#DADADA;">
                                    <th style="text-align:center; width:250px;">TQ Date</th>
                                    <th style="text-align:center; width:250px;">TQ REF</th>
                                    <th style="text-align:center;">AMOUNT</th> 
                                  
                                </tr>';
                foreach ($sql->result() as $rows) {
                      
                     

                    $html .= '<tr>
                               
                                <td style="text-align:center;">'.date('d-M-Y',strtotime($rows->tq_date)).'</td> 
                                <td style="text-align:center;">'.$rows->tq_ref.'</td> 
                                <td style="text-align:center;">'.$rows->tq_amount.'</td> 
                                </tr>';
                }

                 $html .= '</table>';
            }

        return $html;
  }


  function getinvoice_no_new($company_id)
		{
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

			$compid=$company_id;
			$rest=$this->db->select('invoice_starts_from')->from('store_rack_location')->where('id',$compid)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				$invoice_starts=$row->invoice_starts_from;
			}else
			{
				$invoice_starts=0;
			}



			 $sql = $this->db->select('invoice_no')
                        ->from('order_punch')
                        ->where('hpcl_billing_company',$compid)
                        ->where('added_on>=',$start_date)
                        ->where('added_on<=',$end_date)	
                        ->order_by('invoice_no', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->invoice_no+1;

		}else
		{
			$lastorderid=$invoice_starts;
		}

		return $lastorderid;
	}

	function save_followup_remarks()
	{
		$rmk=$this->input->post('rmk');
		$customer=$this->input->post('customer');
		$m=date('Y-m-01');
		$d=array('customer_id'=>$customer,'remarks'=>$rmk,'period'=>date('Y-m-01'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('customer_followup_remarks',$d);
		
	}

	function getlastdata($customer,$period)
	{
		$rmk='';
		$ad='';
		$d=$this->db->select('remarks,addedOn')->from('customer_followup_remarks')->where('period',$period)->where('customer_id',$customer)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $ddd);
			$rmk=$ddd->remarks;
			$ad=date('d-m-Y',strtotime($ddd->addedOn));

		}

		return $rmk."|".$ad;

	}



		public function send_mail_to_lead_with_pdf_new($id,$cust_email,$profile) {
		$uri=$id;

		if ($uri <> '') {
			$sql = $this->db->select('a.id, a.company_id, a.check_terms, a.general_terms, a.bulk_terms, b.company_name, b.customer_name, b.contact_no, b.email, b.address, b.city, b.alt_contact,c.email as salesemail,c.first_name,c.last_name,c.contact_number')
			                ->from('customer_quotation a')
			                ->join('customer_detail b', 'b.id=a.customer_id', 'left')
			                ->join('system_users c', 'c.user_id=a.added_by', 'left')
			                ->where('a.id',$id)
			                ->get();
		    // $sql = $this->db->select('contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, general_terms, bulk_terms, alt_contact_no, hpcl_company')
		    //                 ->from('leads')
		    //                 ->where('id', $uri)
		    //                 ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);

		        	$unique_id='QUOTE'.$row->id;
		            // $contact_person = $row->contact_person;
		            $alt_contact_no = $row->alt_contact;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $city = $row->city;
		            $contact_no = $row->contact_no;
		            $address = $row->address;
		            $general_terms = $row->general_terms;
		            $bulk_terms = $row->bulk_terms;
		            $email_id = $row->email;
		            $hpcl_company = $row->company_id;
		            $salesemail=$row->salesemail;
		            $first_name=$row->first_name;
		            $last_name=$row->last_name;
		            $salescontact=$row->contact_number;
		        } else {
		            // $contact_person = '';
		            $alt_contact_no = '';
		            $unique_id='';
		            $customer_name = '';
		            $company_name = '';
		            $city = '';
		            $contact_no = '';
		            $address = '';
		            $general_terms = '';
		            $bulk_terms = '';
		            $email_id = '';
		            $hpcl_company = 0;
		            $salesemail='';
		            $salescontact='';
		        }
		} else {
		    redirect(page_url);
		}

		$compemail='';
		$compname='';
		$swe=$this->db->select('email_id,companyname')->from('store_rack_location')->where('id',$hpcl_company)->get();
		if($swe->num_rows()>0)
		{
		foreach($swe->result() as $compemail111);
		$compemail=$compemail111->email_id;
		$compname=$compemail111->companyname;

		}
		$subjectname='Quotation of Industrial Lubricants.';

		$Message = "Dear ".$customer_name." Ji, <br><br>";
	    $Message .= "In reference to our meeting discussion held regarding Industrial Oil supply to
	                    your respective business units therefore, we hereby offer you attached Quotation for the
	                    products as required by yourself."."<br><br>";
	    $Message .= "Regards"."<br>";
	    $Message .= $first_name.' '.$last_name."<br>";
	    $Message .= $salescontact."<br>";
	    $Message .= $compemail."<br>";
	    $Message .= $compname."<br>";

		$file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";


		// $Message ='<table style="width: 100%; font-size:14px; font-family: monospace;">
  //       <tr>
  //           <td width="20%"></td>
  //           <td width="60%" style=" padding:5px; box-shadow: 1px 1px 10px lightgray; background-color:white;">
  //               <table style="width: 100%; font-size:14px;">
  //                   <tr>
  //                       <td width="10%"></td>
  //                       <td width="80%">
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="50%">To</td>
  //                                   <td width="50%" style="text-align:right;">Date:'. date('d-m-Y').'</td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="40%">'.$customer_name.'<br>
  //                                       M/s '.$company_name.'
  //                                       <br>
  //                                       '.$address.'.
  //                                   </td>
  //                                   <td width="30%"></td>
  //                                   <td width="30%"></td>
  //                               </tr>
  //                           </table>
  //                           <br>
  //                           <br>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td width="10%"></td>
  //                                   <td width="80%" style="text-align:center;">Sub: Quotation of Industrial Lubricants.
  //                                   </td>
  //                                   <td width="10%"></td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       Sir,<br>
  //                                       In reference to our meeting discussion held regarding Industrial Oil supply to
  //                                       your respective business units therefore, we hereby offer you Quotation for the
  //                                       products as required by yourself.
  //                                   </td>
  //                               </tr>
  //                           </table>
  //                           <br>
  //                           <table style="width: 100%; font-size:14px;" border="1">
  //                               <tr>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Product Name with
  //                                       HSN Code</th>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Pack size</th>
  //                                   <th style="padding: 5px; background-color: lightgray;" width="20%">Offered Price
  //                                       </th>
  //                               </tr>';

  //                               $sql2 = $this->db->select('a.qty, a.list_price, b.instruments_name')
  //                                                ->from('customer_quotation_detail a')
  //                                                ->join('presto_instruments b', 'b.id=a.product_id')
  //                                                ->where('quotation_id', $uri)
  //                                                ->get();
  //                               if ($sql2->num_rows() > 0) {
  //                                   foreach ($sql2->result() as $row2) {

  //                               $Message.='
  //                               <tr>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->instruments_name.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->qty.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'. $row2->list_price.'</td>
  //                               </tr>';
  //                            } }
  //                          $Message.='</table>';
                           
                            
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.
  //                              $general_terms;
  //                           $Message.='</table>';
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <i>Please feel free to contact us incase of any further queries. We shall be
  //                                           more than happy to assist/resolve all your queries.</i>
  //                                   </td>
  //                               </tr>
  //                           </table>
  //                           <table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td style="color: red;">
  //                                       Please Note: - We are the only authorized C&F Agents for Industrial lubricants
  //                                       for <b>M/s HINDUSTAN PETROLEUM CORPORATION LIMITED</b> in Faridabad district and
  //                                       that
  //                                       We/HPCL does not take any responsibility for any unauthorized product supplied
  //                                       by unauthorized/illegitimate supplier.
  //                                   </td>
  //                               </tr>
  //                           </table>';

  //                           $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
  //                           if($com->num_rows() >0)
  //                           {
  //                               foreach($com->result() as $company);
                           
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
  //                                       $company->contact_person.'<br>'.'
  //                                       M/S CFA// '.$company->companyname.'<br>'.
  //                                        $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
  //                                       $company->address.'<br>
  //                                       Emails : '. $company->email_id.'<br>
  //                                       Office Landline No. '.$company->landline_number.'<br>
  //                                       MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
  //                                       Please locate us on HPCL Website:'.$company->locate_us.'


  //                                   </td>
  //                               </tr>
  //                           </table>';
  //                       } 
                          
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.$bulk_terms;
                              
  //                           $Message.='</table>';

  //                              $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                                   <tr>
  //                                       <td>
  //                                           <i>Please feel free to contact us incase of any further queries. We shall be
  //                                               more than happy to assist/resolve all your queries. </i>
  //                                       </td>
  //                                   </tr>
  //                               </table>
  //                               <table style="width: 100%; font-size:14px; padding: 5px;">
  //                                   <tr>
  //                                       <td style="color: red;">
  //                                           We are the only authorized C&F Agents for industrial lubricants for HPCL in
  //                                           Faridabad district and that HPCL does not take any responsibility for any
  //                                           unauthorized product supplied by unauthorized/illegitimate supplier.
  //                                       </td>
  //                                   </tr>
  //                               </table>';
                              
                           
  //                           $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
  //                           // if($com->num_rows() >0)
  //                           // {
  //                               foreach($com->result() as $company);
                           
  //                           $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">
  //                               <tr>
  //                                   <td>
  //                                       <img src="'.sfdocument.'hp.jpg"><b>with regards</b><br>';
  //                                       $company->contact_person.'<br>'.'
  //                                       M/S CFA//'.$company->companyname.'<br>'.
  //                                        $Message.='AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>'.
  //                                       $company->address.'<br>
  //                                       Emails : '. $company->email_id.'<br>
  //                                       Office Landline No. '.$company->landline_number.'<br>
  //                                       MOBILE '. $company->mobile.',#'. $company->alt_mobile.'<br> Please view us on GoogleMap:- '.$company->googlemap.'<br>
  //                                       Please locate us on HPCL Website:'.$company->locate_us.'


  //                                   </td>
  //                               </tr>
  //                           </table>';
  //                       // } 
  //                      $Message.='</td>
  //                       <td width="10%"></td>
  //                   </tr>
  //               </table>
  //           </td>
  //           <td width="20%"></td>
  //       </tr>
  //   </table>';
 	// 	// echo $Message; exit;


	$com=$this->db->select('smtp,email,password,profile')->from('store_rack_location')->where('id',$hpcl_company)->get();
if($com->num_rows() >0){
foreach($com->result() as $company);


	$profile_file=$company->profile;
     $config['protocol'] = 'ssmtp';  
		$config['smtp_host'] = $company->smtp;  
		$config['smtp_user'] = $company->email;  
		$config['smtp_pass'] = $company->password;   
		$config['smtp_port'] = 465;   
		// $config['smtp_crypto'] = 'ssl';
		$config['newline'] = "\r\n";
		$config['starttls'] = TRUE;
		$config['charset'] = 'iso-8859-1';
		$config['mailtype'] = 'html';

        $this->email->initialize($config);  
        $this->load->library('email', $config);
        $this->email->set_header('Header1', 'Value1');
		$this->email->set_mailtype("html");

		$this->email->to($cust_email);
		
		//$this->email->to('sdsrbh5@gmail.com,saurabh@gamavis.com');
		//$this->email->to('sdsrbh5@gmail.com,saurabh@gamavis.com');
		//$this->email->from('info@sunderindoil.com');
		//$this->email->from('lubesmaster@gmail.com');
		if($hpcl_company==3)
		{
			$this->email->cc('faridabadcfa@gmail.com,faridabad@hpclcfa.com');
		}
		$this->email->bcc('sdsrbh5@gmail.com,'.$salesemail);
		$this->email->from($company->email);
		
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$this->email->attach($file);
		if($profile==1 && $profile_file<>'')
		{
			if(file_exists(UPLOADPATH.'profile/'.$profile_file))
			{
				$pfile=site_http_root."image_bank/profile/".$profile_file;
				$this->email->attach($pfile, 'attachment', 'Company_Profile.pdf');
			}

		}
		$result11=$this->email->send();
		 //echo $this->email->print_debugger(); exit;
		
			$data=array(
					'quotation_id'=>$id,
					'type'=>1,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id'],
					'send_to'=>$cust_email
					);
					$this->db->insert('customer_quotation_mail_history',$data);

	}

		
	}


	function whatsapp_quote_with_pdf_new($id,$cust_mobile,$profile)
	{
	

		 // $sql = $this->db->select('unique_id,contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, general_terms, bulk_terms, alt_contact_no, hpcl_company')
		 //                    ->from('leads')
		 //                    ->where('id', $id)
		 //                    ->get();

		    $sql = $this->db->select('a.id, a.company_id, b.company_name, b.customer_name, b.contact_no, b.email,c.contact_number as salescontact,c.first_name,c.last_name')
			                ->from('customer_quotation a')
			                ->join('customer_detail b', 'b.id=a.customer_id', 'left')
			                ->join('system_users c','a.added_by=c.user_id')
			                ->where('a.id',$id)
			                ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);
		            // $contact_person = $row->contact_person;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $contact_no = $row->contact_no;
		            $unique_id='QUOTE'.$row->id;
		            $salescontact=$row->salescontact;
		             $hpcl_company=$row->company_id;



					$swe=$this->db->select('profile')->from('store_rack_location')->where('id',$hpcl_company)->get();
					if($swe->num_rows()>0)
					{
					foreach($swe->result() as $compemail111);
					$pff=$compemail111->profile;
					}else
					{
						$pff='';
					}

		            /** SEND WHATSAPP **/
					$smsmessage="Hello ".$customer_name." ji,\n\n";
					$smsmessage.="Please find the quotation attachment for your requirement\n\n";
					$smsmessage.="Regards\n";
					$smsmessage.=ucwords(strtolower($row->first_name))." ".ucwords(strtolower($row->last_name))."\n";
					$smsmessage.=$salescontact;

					$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => $cust_mobile.",".$salescontact,
					// 'receiverMobileNo' => '918447031736',
					// 'receiverMobileNo' => '919560814669',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags(ucwords(strtolower($smsmessage))),
					'filePathUrl' => $file);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);


					
					if($profile==1 && $pff<>'')
						{
						if(file_exists(UPLOADPATH.'profile/'.$pff))
						{
						$pfile=site_http_root."image_bank/profile/".$pff;
					

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						$post = array(
						'receiverMobileNo' => $cust_mobile.",".$salescontact,
						// 'receiverMobileNo' => '918447031736',
						// 'receiverMobileNo' => '919560814669',
						'username' => whatsappuser,
						'password' => whatsapppass,
						'message'=>strip_tags(ucwords(strtolower($smsmessage))),
						'filePathUrl' => $pfile);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						$result = curl_exec($ch);
						if (curl_errno($ch)) {
						echo 'Error:' . curl_error($ch);
						}
						curl_close($ch);




						}

						}

					// PDF 
					
					// $file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";
					//$file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";
// ECHO $file; exit;
				

					// $ch = curl_init();
					// curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					// curl_setopt($ch, CURLOPT_POST, 1);
					// $post = array(
					// //'receiverMobileNo' => '91'.$contact,
					// // 'receiverMobileNo' => '918447031736',
					// 'receiverMobileNo' => '91'.$contact_no.",".$salescontact,
					// 'username' => whatsappuser,
					// 'password' => whatsapppass,
					// 'filePathUrl' => $file,
					// 'message'=>'');
					// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					// $result = curl_exec($ch);
					

					// if (curl_errno($ch)) {
					// echo 'Error:' . curl_error($ch);
					// }
					// curl_close($ch);
		            /** END **/
		            
					
					$data=array(
					'quotation_id'=>$id,
					'type'=>2,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id'],
					'send_to'=>$cust_mobile
					);
					$this->db->insert('customer_quotation_mail_history',$data);

		        } else {
		            // $contact_person = '';
		            $customer_name = '';
		            $company_name = '';
		            $contact_no = '';		         
		        }

	}



	function add_direct_customer(){
    // print_r($this->input->post());

 

      $data=array(
        'customer_name'=>$this->input->post('customer_name'),
        'customer_code'=>$this->input->post('customer_code'),
        'tds'=>$this->input->post('tds'),
        'gst'=>$this->input->post('gst'),
        'tcs'=>$this->input->post('tcs'),
        'address'=>$this->input->post('address'),
        'state'=>$this->input->post('state')
       );

      $this->db->insert('hpcl_direct_customer',$data);
      $this->session->set_flashdata('message', '<div class="alert alert-danger" style="color:#fff;">Record Added.</div>');
      redirect(page_url.'Customer/direct_customer');

  }


  public function getproductdataNew()
	{
		$html = '';
		$query = $this->db->select('b.id, b.instruments_name,b.pack_size')
		 				  ->from('presto_instruments b')
						  ->where('b.status',1)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name." ".$row->pack_size."</option>";
				}
			} 
				
		echo $html;

	}



	public function getcustomerdataCompanyWise()
	{
		$htm='';
		$htm.='<option value="" >Select</option>';
		$cid=$this->input->post('comp');
		if($cid<>'')
		{
		$res=$this->db->select('id,company_name')->from('customer_detail')->where('company_id',$cid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row)
			{
				$htm.='<option value='.$row->id.'>'.$row->company_name.'</option>';
			}
		}
	}



		echo $htm; 
	}

	function getsales_no_new()
	{
		 $sql = $this->db->select('sales_order_no')
                        ->from('order_punch')
                        ->where('salesorderstart',1)
                        ->order_by('id', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->sales_order_no+1;

		}else
		{
			$lastorderid=1;
		}

	return $lastorderid;
	}

	function add_opening_balance()
	{
		$op_money=$this->input->post('op_money');
		$id=$this->input->post('id');
		$d=array('opening_balance'=>$op_money);
		$this->db->where('id',$id);
		$this->db->update('customer_detail',$d);


		return true;



	}


	 function get_customer_by_companyNew(){
     $html='<option value="ALL">ALL</option>';
    $company=$this->input->post('company');     
    $this->db->select('a.customer_alias,a.id,a.company_name')
                ->from('customer_detail a')
                ->where('a.company_id', $company)
                ->where('a.status','1');
    $query =$this->db->get();

    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){


    	if($customer->customer_alias<>'')
    	{
    		$a="-".$customer->customer_alias;
    	}else
    	{
    		$a="";
    	}

    	$html.='<option value="'.$customer->id.'">'.$customer->company_name.$a.'</option>';
   

    }
    }

    	echo $html;
  }


   function get_customer_by_companyNew_selected(){

   	  $company=$this->input->post('company');     
    $customer=$this->input->post('customer');
   	if($customer=='ALL')
   	{
   		$cf="selected";
   	}else
   	{
   		$cf='';
   	}

     $html='<option value="ALL" '.$cf.'>ALL</option>';
       
    $this->db->select('a.customer_alias,a.id,a.company_name')
                ->from('customer_detail a')
                ->where('a.company_id', $company)
                ->where('a.status','1');
    $query =$this->db->get();

    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){


    	if($customer->customer_alias<>'')
    	{
    		$a="-".$customer->customer_alias;
    	}else
    	{
    		$a="";
    	}

    	if($customer==$customer->id)
    	{
    		$ab="selected";
    	}else{
    		$ab="";
    	}
    	$html.='<option value="'.$customer->id.'" '.$ab.'>'.$customer->company_name.$a.'</option>';
   

    }
    }

    	echo $html;
  }


  function getSalesOrderDetails_frommaster() {
		  $res = '';
		  $customer = $this->input->post('custid');

		  $query=$this->db->select('customer_alias,payment_type,customer_ref_no,bill_address,bill_state,bill_city,bill_pincode,bill_email,msme_number,gst,pan,ship_address,ship_state,ship_city,ship_pincode,ship_email,title, company_name, state,contact_no,credit_days')
		  				 ->from('customer_detail')
		  				 ->where('id', $customer)
		  				 ->get();

		  	if ($query->num_rows() > 0) {
		  		foreach ($query->result() as $row)
				$res = $row->bill_address.'|'.$row->bill_state.'|'.$row->bill_city.'|'.$row->bill_pincode.'|'.$row->bill_email.'|'.$row->msme_number.'|'.$row->gst.'|'.$row->pan.'|'.$row->ship_address.'|'.$row->ship_state.'|'.$row->ship_city.'|'.$row->ship_pincode.'|'.$row->ship_email.'|'.$row->company_name.'|'.$row->contact_no.'|'.$row->customer_ref_no.'|'.$row->payment_type.'|'.$row->credit_days.'|'.$row->customer_alias;
			} 

			echo $res;
	}

function customerledgerreport()
{
$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('sunderindoil');
$pdf->SetTitle("Customer Ledger");
$pdf->SetSubject('Customer Ledger');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);


$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://crm.sunderindoil.com/assets/images/logo_mitr.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';
$q = $this->db->select('a.company_name, a.customer_name, a.contact_no, b.companyname, a.company_id')->from('customer_detail a')->join(' store_rack_location b','a.company_id=b.id')->where('a.id',$this->uri->segment(3))->get();
foreach($q->result() as $companydetail);

$name = strtoupper($companydetail->company_name)." (".strtoupper($companydetail->companyname).")";

$html.='<table width="100%" style="padding:3px;">
<tr>
<td>
<h3 style="text-align:center;">'.$name.'</h3>
</td>
</tr>
</table><br/><br/>';
$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td colspan="5" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">LEDGER</td>
</tr>
<tr>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Sr No.</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Bill Date</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Particulars</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Order Amount</b> </td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Payment Amount</b> </td>

</tr>';
$totalorderamount = array();
$totalorderamount[] = 0;
$totalpaymentamount = array();
$totalpaymentamount[] = 0;


$data = array();
		$this->db->select('a.sales_order_no,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id,j.first_name as createdf,j.last_name as createdl, c.gst as seller_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users j','j.user_id=a.added_by')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.billing', 1)
						  ->where('a.hpcl_billing_company',$companydetail->company_id);
							$this->db->where('b.customer_id',$this->uri->segment(3))
							->where('a.send_to_tally_On>=',$this->uri->segment(4))
							->where('a.send_to_tally_On<=',$this->uri->segment(5));
							$query = $this->db->where('a.cancelled', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row){

			$debit = "";
			$credit = "";
			$paymentfrom = 1;
			$orderamount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->gst_no, $row->seller_gst);

			$data[] = array('srno'=>$i,
				'sales_order_no'=>$row->sales_order_no,
				'billing_date'=>$row->send_to_tally_On,
				'billing_company'=>$row->companyname,
				'invoice_no'=>$row->invoice_no,
				'orderamount'=>$orderamount,
				'debit'=>$debit,
				'particulars'=>"Order Booked - ".$row->invoice_no,
				'credit'=>$credit,
				'paymentfrom'=>$paymentfrom);
		
		$i++;}


//echo "<pre>"; print_r($data); exit;


$j=1;
$this->db->select('a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name, d.companyname')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id')->join('store_rack_location d','b.hpcl_billing_company=d.id','left')->where('c.id',$this->uri->segment(3))->where('b.hpcl_billing_company',$companydetail->company_id);;
$this->db->order_by('a.payment_date','DESC');

$restey=$this->db->get();
if($restey->num_rows()>0){

foreach($restey->result() as $row){
if($row->bills<>'')
{
$invoice=$this->get_invoice_no($row->bills);
}else
{
$invoice='';
}

$debit = "";
$credit = "";
$paymentfrom = 2;
$cno = "";
$cdate = "";
if($row->payment_type==1)
    		{
    			$PT="<strong>Cheque</strong>";
    			$cno="<strong>".$row->cheque_no."</strong>";
    			$cdate="<strong>".date('d-m-Y',strtotime($row->cheque_date))."</strong>";

    		}else if($row->payment_type==2)
    		{
    			$PT="<strong>Cash</strong>";
    		}else if($row->payment_type==3)
    		{
    			$PT="<strong>NEFT</strong>";
    			$cno="<strong>".$row->neft_trans_no."</strong>";
    		}else
    		{
    			$PT='';
    		}




	$data[] = array('srno'=>$j,
				'sales_order_no'=>$invoice,
				'billing_date'=>$row->payment_date,
				'billing_company'=>$row->companyname,
				'invoice_no'=>$invoice,
				'orderamount'=>$row->amount,
				'debit'=>$debit,
				'particulars'=>"Payment Received Via - ".$PT." ".$cno." - ".$cdate,
				'credit'=>$credit,
				'paymentfrom'=>$paymentfrom);
	$j++;
}



}

$key = array_column($data, 'billing_date');
array_multisort($key, SORT_ASC, $data);
$i=1;
foreach($data as $rowdata){
//echo "<pre>"; print_r($rowdata); exit;

if($rowdata['paymentfrom']==1){
	$ordeamount = $rowdata['orderamount'];
	$totalorderamount[] = $rowdata['orderamount'];
	$paymentamount  = 0;
}else{
	$ordeamount = 0;
	$paymentamount = $rowdata['orderamount'];
	$totalpaymentamount[] = $rowdata['orderamount'];
}
$html.='
	<tr>
		<td style="text-align:center;">'.$i.'</td>
		<td style="text-align:center;">'.date('d-M-Y',strtotime($rowdata['billing_date'])).'</td>
		<td style="text-align:center;">'.$rowdata['particulars'].'</td>
		<td style="text-align:center;">'.$ordeamount .'</td>
		<td style="text-align:center;">'.$paymentamount .'</td>
	


	</tr>
';

$i++;
}

$tbill= array_sum($totalorderamount);
$tpay = array_sum($totalpaymentamount);
$totalourstanding = $tbill-$tpay;

$html.='<tr>
<td colspan="3" style="text-align:center;"></td>
<td style="text-align:center">'.array_sum($totalorderamount).'</td>
<td style="text-align:center">'.$tpay.'</td>
</tr>';

$html.='<tr>
<td colspan="5" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">Total Outstanding:  '.round($totalourstanding,2).'</td>
</tr>';

$html.='</tbody></table>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
//$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=ucwords(strtolower($name))."_Customer_Ledger_".date('d_M_Y_H_A').".pdf"; //Linux

// $pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');
 
		
}


function get_invoice_no($order_ids)
	{
		$d1=array();
		$resteyu=$this->db->select('invoice_no')->from('order_punch')->where_in('id',$order_ids,false)->get();
		if($resteyu->num_rows()>0)
		{
			foreach($resteyu->result() as $d)
			{
				$d1[]=$d->invoice_no;
			}

		}
		if(count($d1)>0)
		{
			return implode(',',$d1);
		}else
		{
			return null;
		}
	}

	function check_for_rebrand()
	{
		$html='';
		$prd=$this->input->post('prd');
		$rtr=$this->db->select('rebrand')->from('presto_instruments')->where('rebrand',1)->where('id',$prd)->get();
		if($rtr->num_rows()>0)
		{
			$rt=$this->db->select('a.rebrand_product_id,b.instruments_name')->from('instrument_rebrand a')->join('presto_instruments b','a.rebrand_product_id=b.id')->where('a.product_id',$prd)->get();
			if($rt->num_rows()>0)
			{
			$html.='<option value="">Select Product</option>';
			foreach($rt->result() as $rtt)
			{
			$html.='<option value="'.$rtt->rebrand_product_id.'">'.$rtt->instruments_name.'</option>';
			}

			}

		}

		echo $html;
	}



	 function get_customer_by_company_for_payment(){
      

      	 $html='<option value="ALL">ALL</option>';
    $company=$this->input->post('company');     
 $selected=$this->input->post('selected');     
    $this->db->select('a.customer_alias,a.id,a.company_name')
                ->from('customer_detail a')
                ->where('a.company_id', $company)
                ->where('a.status','1');
    $query =$this->db->get();

    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){


    	if($customer->customer_alias<>'')
    	{
    		$a="-".$customer->customer_alias;
    	}else
    	{
    		$a="";
    	}

    	if($selected<>'' && $customer->id==$selected)
					{	
					$bb="selected";
					}else
					{
					$bb="";
					}


    	$html.='<option value="'.$customer->id.'" '.$bb.'>'.$customer->company_name.$a.'</option>';
   

    }
    }

    	echo $html;



			}


			function markcustomerpaymentdone()
			{
				$orderid=$this->input->post('orderid');

				$datas = array(
				'payment' => 1,
				'adjustment'=>1,
				'adjustment_type'=>1
				);

				$this->db->where('id', $orderid)
				->update('order_punch', $datas);
				echo $this->db->affected_rows();

			}

	public function mergewithothercompany(){

		 $this->form_validation->set_rules('mergewithcompany', 'Merge With Company', 'required|trim');
		$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
		$userid = $_SESSION['logged_in']['user_id'];
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('users/mergecustomer');
		}else
		{

			$datas = array('customer_id'=>$this->uri->segment(3),
				'merge_with_customer'=>$this->input->post('mergewithcompany'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$userid);

			$this->db->insert('customer_merge_request_record',$datas);

			/** Update is Customer Quotation**/
			$data = array('customer_id'=>$this->input->post('mergewithcompany'));
			$this->db->where('customer_id',$this->uri->segment(3));
			$this->db->update('customer_quotation',$data );

			/** Update is Customer Payment**/
			$data1 = array('customer_id'=>$this->input->post('mergewithcompany'));
			$this->db->where('customer_id',$this->uri->segment(3));
			$this->db->update('customer_payments',$data1 );

			/** Update is Customer Trail to be sent**/
			$data2 = array('customer_id'=>$this->input->post('mergewithcompany'));
			$this->db->where('customer_id',$this->uri->segment(3));
			$this->db->update('trial_to_be_sent',$data2 );


			$this->db->where('id',$this->uri->segment(3));
			$this->db->delete('customer_detail');

			 $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;"><span style="color:red; float-left:20px;">Thank You! Record successfully updated.</span></div><br/>');
			 redirect(page_url.'Customer/customer_view');


		}
	}

	function ledger_report()
	{
		$this->load->view('customer/ledger_report');
	}

	function get_ledger()
	{
		$customer=$this->input->post('customer');
		$stdate=date('Y-m-d',strtotime($this->input->post('stdate')));
		$etdate=date('Y-m-d',strtotime($this->input->post('etdate')));
		

		redirect(page_url.'Customer/customerledgerreport/'.$customer.'/'.$stdate.'/'.$etdate);
	}


	 function get_customer_by_company_for_cheque_bounce(){
      
	 $html.='<option value="">Select</option>';
    $company=$this->input->post('company');     
 $selected=$this->input->post('selected');     
    $this->db->select('a.customer_alias,a.id,a.company_name')
                ->from('customer_detail a')
                ->where('a.company_id', $company)
                ->where('a.status','1');
    $query =$this->db->get();

    if($query->num_rows()>0)
    {
    foreach($query->result() as $customer){


    	if($customer->customer_alias<>'')
    	{
    		$a="-".$customer->customer_alias;
    	}else
    	{
    		$a="";
    	}

    


    	$html.='<option value="'.$customer->id.'">'.$customer->company_name.$a.'</option>';
   

    }
    }

    	echo $html;



	}


	function discount_approval_list_only_quote() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price,a.added_on,b.id as quoteid')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
					
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

					$ret=$this->db->select('id')->from('order_punch')->where('quotation_id',$row->quoteid)->get();
					if($ret->num_rows()==0)
					{
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";
					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->added_on)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
				}
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		$sql = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price,a.added_on')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('store_rack_location c', 'c.id=b.hpcl_company')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {
					if($row->list_price != '' && $row->list_price > 0) {
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_lead_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";

					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->added_on)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
					}
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}



	function discount_approval_list_only_order() {
		$lead_data = array();
		$sql = $this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price,a.added_on,b.id as quoteid')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
					
						->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

					$ret=$this->db->select('id')->from('order_punch')->where('quotation_id',$row->quoteid)->get();
					if($ret->num_rows()>0)
					{
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";
					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->added_on)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'allowed_price' => $row->discount_price,
										'list_price' => $row->list_price,
										'action' => $action
										);
					$i++;
				}
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}


function getproducts_detailNew($quotation,$productss)
	{
		$a=array();
		$a[]=0;
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		<th>Batch Code</th>
		</tr>
		</thead>
		<tbody>';
			$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation);

			

			$res=$this->db->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			if($productss<>'' && $productss<>'ALL')
			{
				
				if($product->product_id==$productss)
				{
				$a[]=1;
				}
			}else
			{
				$a[]=1;
			}

			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';

			if($product->new_batch_code==0)
			{
			$html.='<td>'.$product->batch_code.'</td>';
			}else
			{
			$batch_no=$this->getbatch_no($product->batch_code);
			$html.='<td>'.$batch_no.'</td>';
			}

			$j++;
			}
			$html.='</tbody></table>';
			}



			return $html."~".array_sum($a);
	}

	function getbatch_no($id)
	{
		$batch='';
		$restey=$this->db->select('batch_no')->from('inventory_batch_no')->where('id',$id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$batch=$row->batch_no;
		}

		return $batch;

	}


	function direct_customer_collection()
	{
		$this->load->view('customer/direct_customer_collection');
	}

	function direct_customer_collection_list()
	{

		 $uri=$this->uri->segment(3);
		 $sdate=$this->uri->segment(4);
		 $edate=$this->uri->segment(5);
    $lead_data = array();
   $this->db->select('a.*,b.balance,c.customer_name')
                  ->from('customer_collection_reference a')
                  ->join('customer_collection_reference_balance b','a.id=b.collection_id')
                  ->join('hpcl_direct_customer c','a.customer_id=c.id')
                  ->where('customer_id',$uri);
                  if($sdate<>'' && $edate<>'')
                  {
                  	$this->db->where('a.collection_date>=',$sdate);
                  	$this->db->where('a.collection_date<=',$edate);
                  }
                $query=  $this->db->order_by('a.collection_date','DESC')
                  ->get();
   
   	if($query->num_rows()>0)
   	{                
    $i=1;
    foreach($query->result() as $row)
    {
      $collection = $this->salescrm->get_customer_payment_collection($uri);
   

            if($i==1)
            {
            $del = "<a href='javascript:;' class='btn btn-warning  btn-xs' onclick='deletecollection_id(".$row->id.",0);'>Remove Payment Adjusted</a><br/><br/><br/><a href='javascript:;' class='btn btn-warning btn-xs' onclick='deletecollection_id(".$row->id.",1);'>Remove Payment Adjusted & Wallet Record</a>"; 
            } else
            {
                $del='';
            } 

      $customer_debit_credit = $this->get_customer_debit_credit($uri,$row->id);
      $lead_data[] = array('sr_no'=>$i,
        'customer_name'=>$row->customer_name,
      'date'=>date('d-M-Y',strtotime($row->collection_date)),
      'collection_id'=>$row->collection_id,
      'amount'=>$row->collection_amount,
      'balance'=>$row->balance,
      'remarks'=>$row->balance,
      'credit_debit_note'=>$customer_debit_credit,
      'action'=>$del
      );
      $i++;
    }
}
    //echo "<pre>"; print_r($compititor_data); exit;
      $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);

	}

	function filter_wallet()
	{
		$customer=$this->input->post('customer');
		$sdate=$this->input->post('sdate');
		$edate=$this->input->post('edate');
		redirect(page_url.'Customer/direct_customer_collection/'.$customer.'/'.$sdate.'/'.$edate);
	}


	function delete_collection_id()
	{
		$id=$this->uri->segment(3);
		$flag=$this->uri->segment(4);
		$customer_id=$this->uri->segment(5);
		$sdate=$this->uri->segment(6);
		$edate=$this->uri->segment(7);



		/** GET ORIGINAL BALANCE **/
	 $restey=$this->db->select('collection_amount')->from('customer_collection_reference')->where('id',$id)->get();
            if($restey->num_rows()>0)
            {
                foreach($restey->result() as $restey1);
                $coll_amount=$restey1->collection_amount;
            }else
            {
                $coll_amount=0;
            }

            /** END **/

            /** GET ALL INVENTORY ID SETLLED BY THIS COLLECTION **/
			$inventory=array();
			$resty=$this->db->select('inventory_id')->from('customer_inventory_payment_details')->where('collection_id',$id)->get();
			if($resty->num_rows()>0)
			{
				foreach($resty->result() as $row)
				{
					$inventory[]=$row->inventory_id;
				}
			}


			/** END **/

		

			/** UN ADJUST THOSE INVENTORY ID **/
			if(count($inventory)>0)
			{
			foreach($inventory as $inv)
			{
					$data2 = array(
					'payment_date' => '0000-00-00',
					'payment' => 0,
					'paymentBy'=>0
					);
					$this->db->where('id',$inv);
					$this->db->update('type_2_3_invoice_particular',$data2);

					/** DELETE THE INVENTORY PAYMENT DETAILS **/
				
					$this->db->where('collection_id',$id);
					$this->db->delete('customer_collection_reference');
					/** END **/

					$this->db->where('collection_id',$id);
					$this->db->where('inventory_id',$inv);
					$this->db->delete('customer_inventory_payment_details');

			}
			}

			/** END **/


        /** UPDATE COLLECTION BALANCE **/
        $data3=array('balance'=>$coll_amount,'active'=>1);
         $this->db->where('collection_id',$id);
         $this->db->update(' customer_collection_reference_balance',$data3);
        /** end **/

         /** DELETE ALL COLLECTION IF YES **/
        if($flag==1)
        {
        
            $this->db->where('id',$id);
            $this->db->delete('customer_collection_reference');

            $this->db->where('collection_id',$id);
            $this->db->delete(' customer_collection_reference_balance');

            $this->db->where('collection_id',$id);
            $this->db->delete('customer_collection_credit_debit');

        }
        /** END **/

         if($flag==1)
        {
        $this->session->set_flashdata('message','<div class="alert alert-success">Payment Unadjusted & Wallet Deleted Successfully.</div><br/>');
       redirect(page_url.'Customer/direct_customer_collection/'.$customer_id.'/'.$sdate.'/'.$edate);
        }else
        {
          $this->session->set_flashdata('message','<div class="alert alert-success">Payment Unadjusted & Wallet Restored Successfully.</div><br/>');
        redirect(page_url.'Customer/direct_customer_collection/'.$customer_id.'/'.$sdate.'/'.$edate);
        }


  
        
	

	}
	

function customerpendingforpaymenttermsapproval() {
			$this->load->view('leads/customerpendingforpaymenttermsapproval');
		}


		function all_orders_list_user() {

		$start_date = $this->uri->segment(3)." 00:00:00";
		$end_date = $this->uri->segment(4)." 00:00:00";
		$company = $this->uri->segment(5);
		$customer = $this->uri->segment(6);
		$product = $this->uri->segment(7);



		$lead_data = array();
		 $this->db->select('a.cancelled,a.billing,a.send_to_tally,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id');
						 	if($this->uri->segment(3) != '' && $this->uri->segment(4) != '') {
							$this->db->where('a.added_on>=', $start_date);
							$this->db->where('a.added_on<=', $end_date);
							}
							if($company<>'ALL' && $company<>'')
							{
							$this->db->where('a.hpcl_billing_company',$company);
							}

							if($customer<>'ALL' && $customer<>'')
							{
							$this->db->where('b.customer_id',$customer);
							}
							 $this->db->where('b.added_by', $this->session->userdata['logged_in']['user_id']);
						 	$query=$this->db->order_by('a.added_on','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

					// if($row->payment_type == 1) {
					// 	$payment_type = 'Cheque';
					// 	$th_fields = "<th>Cheque No</th>";
					// 	$td_fields = "<td>".$row->cheque_no."</td>";
					// } else if($row->payment_type == 2) {
					// 	$payment_type = 'Cash';
					// 	$th_fields = "";
					// 	$td_fields = "";
					// } else if($row->payment_type == 3) {
					// 	$payment_type = 'NEFT';
					// 	$th_fields = "<th>UTR No</th>";
					// 	$td_fields = "<td>".$row->utr_no."</td>";
					// }  else if($row->payment_type == 4) {
					// 	$payment_type = 'PDC';
					// 	$th_fields = "<th>Cheque No</th><th>PDC Date</th>";
					// 	$td_fields = "<td>".$row->cheque_no."</td><td>".date('d-m-Y', strtotime($row->pdc_date))."</td>";

					// }

					// $html=$this->getproducts_detail($row->quotation_id);
					$html=$this->getproducts_detailNew($row->quotation_id,$product);
					$h=explode('~',$html);

					$shipstate=$this->getstate($row->shipping_state);
					$billstate=$this->getstate($row->billing_state);
					$j=1;

					$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


					$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

					
					if($row->send_to_tally==0)
					{

					$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
					if($_SESSION['logged_in']['role']==1)
					{
					$edit.=" | "."<a href='javascript:;' onclick='delete_order(".$row->id.", ".$row->quotation_id.")'><i class='fa fa-trash'></i></a>";
					}else
					{
						
					}

					}else
					{
						$edit='<strong style="color:red;font-weight:bold;">Billing has been done. Cannot be removed or edited</strong>';
					}
					$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
					$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";
					$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

					if($row->payment_type == 2) {
						$payment_type = 'Cash';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.= '';
					} else if($row->payment_type == 3) {
						$payment_type = 'Online';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms .= '';
					} else if($row->payment_type == 4) {
						$payment_type = 'PDC';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
						$payment_type = 'CREDIT';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
						$payment_type = 'ADVANCE';
						$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
						
					} else {
						$payment_type = '';
						$payment_terms="";
						$payment_terms .= '';
					}

					$quotation = "<a href='".page_url."Customer/quotation_preview/".$row->quotation_id."' class='btn btn-success btn-xs' target='_blank'>Preview Quotation</a>";
					
					if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
					if($h[1]>0)
			{

					if($row->billing==1)
					{
						$s="<span class='btn btn-xs btn-success'>Billed</span>";
					}else if($row->cancelled==1)
					{
						$s="<span class='btn btn-xs btn-warning'>Cancelled</span>";
					}else
					{
						$s="<span class='btn btn-xs btn-danger'>Billing Pending</span>";
					}
					$lead_data[] = array('sr_no'=>$i,
						'source'=>$row->lead_source, 
						'status'=>$s,
						'agent'=>$row->first_name." ".$row->last_name,
										 'company_name'=>$row->companyname,
										 'cust_company_name'=>$row->company_name,
										 'customer_name'=>$row->customer_name,
										 'taxdetail'=>$taxdetail,
										 'products'=>$h[0],
										 'shipaddress' =>$shipdetail,
								 		 'billingaddress' =>$billdetail,
								 		 'payment_terms' =>$payment_terms,
								 		 'po_details' =>$po_details,
										 'edit' => $edit,
										 'order_details' => $order_details,
										 'payment_collection' => $payment_collection,
										 'quotation' => $quotation
										);
					$i++;
				}
				}
			}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function add_new_ajax_customer()
	{
		if(!is_numeric($this->input->post('brand'))){
		$resty=$this->db->select('id')->from('company_brand')->where('LOWER(name)',strtolower($this->input->post('brand')))->get();
		if($resty->num_rows()==0)
		{
			$dr=array('name'=>$this->input->post('brand'));
			$this->db->insert('company_brand',$dr);
			$brand_id=$this->db->insert_id();
		}else
		{
			foreach($resty->result() as $roww);
			$brand_id=$roww->id;
		}
	}else{
		$brand_id = $this->input->post('brand');
	}

		$company=$this->input->post('company');
		$brand=$brand_id;
		$email=$this->input->post('email');
		$address=$this->input->post('address');
		$contactpersonname = $this->input->post('contactpersonname');
		$personcontactno = trim($this->input->post('personcontactno'));
		$acontactno = $this->input->post('acontactno');
		$geo = $this->_customer_country_state_data();
		if($geo===false){ echo "0~State is required"; exit; }
		if($personcontactno<>''){
		// $a = $this->db->select('contact_no')->from('customer_detail')->where('contact_no',$personcontactno)->get();
		// if($a->num_rows()>0){
		// 	echo "Record Already Exist."; exit;
		// }else{
				$dr=array('company_brand'=>$brand,'company_name'=>$company,'email'=>$email,'bill_email'=>$email,'address'=>$address,'bill_address'=>$address,'status'=>1,'customer_name'=>$contactpersonname,'contact_no'=>$personcontactno,'alt_contact'=>$acontactno);
		$dr = array_merge($dr, $geo);


		$this->db->insert('customer_detail',$dr);
		$id=$this->db->insert_id();
		$this->sync_customer_to_sap('marketing', $id);
		echo $this->db->affected_rows()."~".$id;
		
	}
	
	}


function add_new_ajax_customer_with_multiple_Record()
{
    $user_id = $_SESSION['logged_in']['user_id'];
    
    if (!is_numeric($this->input->post('brand'))) {
        $resty = $this->db->select('id')->from('company_brand')->where('LOWER(name)', strtolower($this->input->post('brand')))->get();
        if ($resty->num_rows() == 0) {
            $dr = array('name' => $this->input->post('brand'));
            $this->db->insert('company_brand', $dr);
            $brand_id = $this->db->insert_id();
        } else {
            foreach ($resty->result() as $roww);
            $brand_id = $roww->id;
        }
    } else {
        $brand_id = $this->input->post('brand');
    }

    $company = $this->input->post('company');
    $email = $this->input->post('email');
    $address = $this->input->post('address');
    $geo = $this->_customer_country_state_data();
    if ($geo === false) {
        echo "0~State is required";
        exit;
    }

    // Decode JSON contacts
    $contacts = json_decode($this->input->post('contacts'), true);


    if (!empty($contacts)) {
        // Extract the first contact for main customer detail
        $first_contact = $contacts[0];
        $first_contact_no = trim($first_contact['personcontactno']);

        // Start a transaction to ensure atomicity
        $this->db->trans_start();

        // Check if the first contact number already exists in customer_detail
        $a = $this->db->select('contact_no')->from('customer_detail')->where('contact_no', $first_contact_no)->get();
        if ($a->num_rows() > 0) {
            echo "Record Already Exist.";
            $this->db->trans_rollback();  // Rollback transaction
            exit;
        } else {
            // Insert main customer details using the first contact information
            $dr = array(
                'company_brand' => $brand_id,
                'company_name' => $company,
                'email' => $email,
                'bill_email' => $email,
                'address' => $address,
                'bill_address' => $address,
                'status' => 1,
                'customer_name' => $first_contact['contactpersonname'],
                'contact_no' => $first_contact_no,
                'designation' => $first_contact['designation'],  // Add designation
                'branchlocation' => $first_contact['branchlocation'],  // Add branch location
                'added_on' => date('Y-m-d H:i:s'),
                'added_by' => $user_id
            );
            $dr = array_merge($dr, $geo);

            $this->db->insert('customer_detail', $dr);
            $customer_id = $this->db->insert_id();

            if ($customer_id) {
                // Insert each contact (including the first) into `company_multiple_contacts`
                foreach ($contacts as $contact) {
                    $contact_data = array(
                        'customer_id' => $customer_id,
                        'contactpersonname' => $contact['contactpersonname'],
                        'personcontactno' => $contact['personcontactno'],
                        'personemailid' => $contact['personemailid'],
                        'designation' => $contact['designation'],
                        'branchlocation' => $contact['branchlocation'],
                        'added_on' => date('Y-m-d H:i:s'),
                        'added_by' => $user_id
                    );

                    // Insert each contact row individually
                    if (!$this->db->insert('company_multiple_contacts', $contact_data)) {
                        log_message('error', 'Failed to insert contact record: ' . json_encode($contact_data));
                    }
                }
            } else {
                log_message('error', 'Failed to insert customer record.');
                $this->db->trans_rollback();
                return;
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                // Transaction failed; rollback
                echo "An error occurred while saving data.";
                $this->db->trans_rollback();
            } else {
                $this->sync_customer_to_sap('marketing', $customer_id);
                echo "Data successfully saved.";
            }
        }
    } else {
        echo "No contact data provided.";
    }
}




	function addnewcustomer(){
		$this->load->view('customer/add_new_customer');
	}

	function feednewcustomerdata()
	{

		 $user_id = $_SESSION['logged_in']['user_id'];
		if(!is_numeric($this->input->post('new_company_brand'))){

		
		$resty=$this->db->select('id')->from('company_brand')->where('LOWER(name)',strtolower($this->input->post('new_company_brand')))->get();
		if($resty->num_rows()==0)
		{
			$dr=array('name'=>$this->input->post('new_company_brand'));
			$this->db->insert('company_brand',$dr);
			$brand_id=$this->db->insert_id();
		}else
		{
			foreach($resty->result() as $roww);
			$brand_id=$roww->id;
		}
	}else{
		$brand_id = $this->input->post('new_company_brand');
	}

		$company=$this->input->post('new_companyname');
		$brand=$brand_id;
		$email=$this->input->post('new_email');
		$address=$this->input->post('new_address');
		$contactpersonname = $this->input->post('contactpersonname');
		$personcontactno = trim($this->input->post('personcontactno'));
		$acontactno = $this->input->post('acontactno');
		$geo = $this->_customer_country_state_data();
		if($geo===false){
			$this->session->set_flashdata('message','<div class="alert alert-danger">State is required.</div>');
			redirect(page_url.'Customer/addnewcustomer');
		}
		if($personcontactno<>''){
		//$a = $this->db->select('contact_no')->from('customer_detail')->where('contact_no',$personcontactno)->get();
	//	if($a->num_rows()>0){
	//		echo "Record Already Exist."; exit;
	//	}else{
				$dr=array('company_brand'=>$brand,
					'company_name'=>$company,
					'email'=>$email,
					'bill_email'=>$email,
					'address'=>$address,
					'bill_address'=>$address,
					'status'=>1,
					'customer_name'=>$contactpersonname,
					'contact_no'=>$personcontactno,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'banglore_exhibition'=>1,
					'alt_contact'=>$acontactno);
				$dr = array_merge($dr, $geo);

				//echo "<pre>"; print_r($dr); exit;
		$this->db->insert('customer_detail',$dr);
		$id=$this->db->insert_id();
		$this->sync_customer_to_sap('marketing', $id);
		

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Customer/addnewcustomer');

		///}
	}
	
	}

	private function sync_customer_to_sap($source, $customer_id)
	{
		$customer_id = (int) $customer_id;
		if ($customer_id <= 0) {
			return;
		}

		$this->load->library('Sap_service');
		$result = $source === 'spares'
			? $this->sap_service->sync_spares_customer($customer_id)
			: $this->sap_service->sync_marketing_customer($customer_id);

		if (empty($result['success']) && empty($result['skipped'])) {
			log_message('error', 'SAP customer sync failed for ' . $source . ' customer ' . $customer_id . ': ' . (isset($result['message']) ? $result['message'] : 'Unknown error'));
		}
	}
	
	public function viewyourcustomers()
	{
		$this->load->view('customer/viewyourcustomers');
	}

	public function viewyourcustomerslist()
{
    $user_id = $_SESSION['logged_in']['user_id'];
    $uri = $this->uri->segment(3);

    // NEW: Country filter
    $country_id = $this->input->get('country_id', true); // ex: 101

    $lead_data = array();

    $this->db->select('
            a.*,
            b.country_id,
            b.country_name,
            c.country as selectedcountry,
            c.postal_address,
            a.id as leadid,
            c.welcome_email_status,
            s.title, s.first_name, s.last_name,
            a.exhibitiion_email_sent
        ')
        ->from('customer_detail a')
        ->join('leads c', 'c.id = a.company_name', 'left')
        ->join('countries b', 'c.country = b.country_id', 'left')
        ->join('system_users s', 'c.added_by=s.user_id', 'left');

    // Role-based access
    if ($_SESSION['logged_in']['role'] != 12 && $_SESSION['logged_in']['role'] != 41) {
        $this->db->where('a.added_by', $user_id);
    }

    // NEW: apply country filter if selected
    if (!empty($country_id)) {
        $this->db->where('c.country', (int)$country_id);
    }

    $this->db->order_by('a.added_on', 'DESC');
    $query = $this->db->get();
    $res = $query->result();

    $i = 1;
    foreach ($res as $row) {

        $edit = "<a href='".page_url."Customer/editcustomerinformation/".$row->id."' class='btn btn-xs btn-default' style='margin-top:6px;'>
                    <i class='fa fa-pencil' title='Edit Customer'></i> Edit
                 </a>";

        // NOTE: You currently hard-coded country_id 101 for state lookup.
        // Keeping it as-is (but ideally use $row->selectedcountry for correct country states).
        $q = $this->db->select('state_id, state_name')
                      ->from('states')
                      ->where('country_id', '101')
                      ->where('state_id', $row->state)
                      ->get();
        $state_name = $q->num_rows() > 0 ? $q->row()->state_name : '';

        $sta = ($row->status == 1)
            ? "<a href='".page_url."Customer/mark_active_inactive/".$row->id."/0/".$uri."' class='btn btn-success btn-xs'>Active</a>"
            : "<a href='".page_url."Customer/mark_active_inactive/".$row->id."/1/".$uri."' class='btn btn-danger btn-xs'>Inactive</a>";

        $emaildelivery = ($row->exhibitiion_email_sent == 1) ? "Delivered" : "Not Delivered";

        // Additional contacts
        $contacts_query = $this->db->select('*')
                                   ->from('company_multiple_contacts')
                                   ->where('customer_id', $row->id)
                                   ->get();
        $additional_contacts = $contacts_query->result();

        if ($contacts_query->num_rows() > 0) {
            $contacts_table = "<div class='table-responsive'>
                <table class='table table-bordered table-condensed' style='margin-bottom:0;'>
                    <thead>
                        <tr style='background-color:#f7f7f7;'>
                            <th style='text-align:center;font-weight:700;'>Contact Person</th>
                            <th style='text-align:center;font-weight:700;'>Contact No</th>
                            <th style='text-align:center;font-weight:700;'>Email ID</th>
                            <th style='text-align:center;font-weight:700;'>Designation</th>
                            <th style='text-align:center;font-weight:700;'>Branch Location</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($additional_contacts as $contact) {
                $contacts_table .= "<tr>
                    <td style='text-align:center;'>{$contact->contactpersonname}</td>
                    <td style='text-align:center;'>{$contact->personcontactno}</td>
                    <td style='text-align:center;'>{$contact->personemailid}</td>
                    <td style='text-align:center;'>{$contact->designation}</td>
                    <td style='text-align:center;'>{$contact->branchlocation}</td>
                </tr>";
            }
            $contacts_table .= "</tbody></table></div>";
        } else {
            $contacts_table = "<span class='text-muted'>—</span>";
        }

        // Primary contact (UI improved)
        $primarycontactinfo = "<div class='table-responsive'>
            <table class='table table-bordered table-condensed' style='margin-bottom:0;'>
                <thead>
                    <tr style='background-color:#f7f7f7;'>
                        <th style='text-align:center;font-weight:700;'>Person Name</th>
                        <th style='text-align:center;font-weight:700;'>Contact No</th>
                        <th style='text-align:center;font-weight:700;'>Email ID</th>";

        if ($row->designation != '') {
            $primarycontactinfo .= "<th style='text-align:center;font-weight:700;'>Designation</th>";
        }
        if ($row->branchlocation != '') {
            $primarycontactinfo .= "<th style='text-align:center;font-weight:700;'>Branch Location</th>";
        }

        $primarycontactinfo .= "</tr></thead><tbody>";

        $primarycontactinfo .= "<tr>
            <td style='text-align:center;'>{$row->customer_name}</td>
            <td style='text-align:center;'>{$row->contact_no}</td>
            <td style='text-align:center;'>{$row->email}</td>";

        if ($row->designation != '') {
            $primarycontactinfo .= "<td style='text-align:center;'>{$row->designation}</td>";
        }
        if ($row->branchlocation != '') {
            $primarycontactinfo .= "<td style='text-align:center;'>{$row->branchlocation}</td>";
        }

        $primarycontactinfo .= "</tr></tbody></table></div>";

        // Intro email button
        if ((int)$row->exhibitiion_email_sent === 0) {
            $sendwelcome = '<a href="'.page_url.'Master/User_management/sendintroemailtocustomers/'.$row->leadid.'" class="btn btn-primary btn-xs" style="margin-top:6px;">
                                Send Intro Email <i class="fa fa-envelope"></i>
                            </a>';
        } else {
            $sendwelcome = "<span class='label label-success' style='display:inline-block;margin-top:6px;'>Intro Email Sent</span>";
        }

//'company_name' => "<div style='font-weight:700;'>" . $row->company_name . "</div>
                               //<div style='margin-top:6px;'>" . $sendwelcome . "</div>",
        $lead_data[] = array(
            'sr_no' => $i . "<div style='margin-top:6px;'>" . $edit . "</div>",
            'status' => $sta,
            'code' => $row->customer_ref_no,
            'company_name' => $row->company_name,
            'primarycontact' => $primarycontactinfo,
            'additional_contacts' => $contacts_table,
            'gst' => $row->gst,
            'address' => $row->address,
            'country' => $row->country_name ? $row->country_name : '',
            'state' => $state_name,
            'city' => $row->city,
            'pincode' => $row->pincode,
            'email'  => $row->email,
    		'mobile' => $row->contact_no,
            'customer_added_on' => date('d-m-Y', strtotime($row->added_on)),
            'addedbyperson' => ucwords(strtolower($row->title . " " . $row->first_name . " " . $row->last_name)),
            'emaildelivery' => $emaildelivery
        );
        $i++;
    }

    echo json_encode(array(
        "sEcho" => 1,
        "iTotalRecords" => count($lead_data),
        "iTotalDisplayRecords" => count($lead_data),
        "aaData" => $lead_data
    ));
}


function addcustomerinformation(){
	$this->load->view('customer/addcustomerinformation');
}

public function addcustomerinformationindb(){

	$user_id=$_SESSION['logged_in']['user_id'];

	$geo = $this->_customer_country_state_data();
	if($geo===false){
		$this->session->set_flashdata('message','<div class="alert alert-danger">State is required.</div>');
		redirect(page_url.'Customer/addcustomerinformation');
	}

	$data=array(

		'company_name'=>$this->input->post('new_companyname'),
		'company_brand'=>$this->input->post('company_brand'),
		'email'=>$this->input->post('new_email'),
		'customer_name'=>$this->input->post('contactpersonname'),
		'contact_no'=>$this->input->post('personcontactno'),
		'alt_contact'=>$this->input->post('acontactno'),
		'address'=>$this->input->post('new_address'),
		'added_on'=>date('Y-m-d H:i:s'),
		'added_by'=>$user_id
		);
	$data = array_merge($data, $geo);

	$this->db->insert('customer_detail',$data);
	$customer_id = $this->db->insert_id();
	$this->sync_customer_to_sap('marketing', $customer_id);

	$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
redirect(page_url.'Customer/viewyourcustomers');
}


function editcustomerinformation(){
	$this->load->view('customer/editcustomerinformation');
}

public function updatecustomerinformationindb(){

$data = array('company_name'=>$this->input->post('new_companyname'),
'email'=>$this->input->post('new_email'),
'customer_name'=>$this->input->post('contactpersonname'),
'contact_no'=>$this->input->post('personcontactno'),
'alt_contact'=>$this->input->post('acontactno'),
'address'=>$this->input->post('new_address'),
'gst'=>$this->input->post('gstn'),
'updated_on'=>date('Y-m-d H:i:s'),
'updated_by'=>$user_id);

if($this->input->post('country')!==null){
	$geo = $this->_customer_country_state_data();
	if($geo===false){
		$this->session->set_flashdata('message','<div class="alert alert-danger">State is required.</div>');
		redirect(page_url.'Customer/editcustomerinformation/'.$this->uri->segment(3));
	}
	$data = array_merge($data, $geo);
}

$this->db->where('id',$this->uri->segment(3));
$this->db->update('customer_detail',$data);

$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
redirect(page_url.'Customer/viewyourcustomers');
}


}
	
