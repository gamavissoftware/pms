<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
	$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		

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
	
	public function index(){
		$this->load->view('audit_report/dashboard');
	}
	public function Add_quotation()
	{
		
	$this->load->view('Quotation/add_quotation');
		
	}
	public function quatation_template()
	{
		
	$this->load->view('Quotation/quotation_view');
		
	}

	public function addquotation()
	{  
	 	$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "quotation_request";
		$customer_name = $this->input->post('customer_name');
        $quotation_date = date('Y-m-d');
		$query = $this->db->select('id')->from('quotation_request')->get();
		   $res = count($query->result());
		   if($res<=0){
			   $caseno = "TI-001";
		   }else{
			   $caseno= "TI-00".$res;
		   }
		$reference_number = $caseno;
        $title = $this->input->post('title');
        $company_name = $this->input->post('company_name');
        $customer_name = $this->input->post('customer_name');
        $contact_number = $this->input->post('contact_number');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $product = $this->input->post('product');
        $productquantity = $this->input->post('productquantity');
        
	        $data = array(
			'customer_name'=>$customer_name,
			'company_name'=>$company_name,
			'quotation_date'=>$quotation_date,
			'reference_number'=>$reference_number,
			'state_id'=>$this->input->post('state'),
			'status'=>1,
			'contact_number'=>$contact_number,
			'email'=>$email,
			'address'=>$address,
			'title'=>$title,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);
		if($result)
		{   
           
            for($i=0 ;$i<count($product);$i++){
		    $data = array(
			'quotation_id'=>$result,
			'product_id'=>$product[$i],
			'product_quantity'=>$productquantity[$i]);
            $this->db->insert('quotation_product_details',$data);
	    	}
             
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/view_quotation_detail/'.$result);
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/list_quotations/');
		}
	   
		
	}


public function updatequotation()
	{  
		$quotationid = $this->uri->segment(3);
	 	$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "quotation_request";
		if($this->input->post('letter_head')){
        $letterhead = $this->input->post('letter_head');
        }else{
        $letterhead = '';	
        }
		$gst_applicable = $this->input->post('gst');
		if($gst_applicable == '1'){
        $gst_percent = $this->input->post('gst_percent');
        $gst_state = $this->input->post('gst_state');
        }else{
        $gst_percent = '';	
        $gst_state = '';	
        }
        $customer_name = $this->input->post('customer_name');
        $quotation_date = $this->input->post('quotation_date');
        $reference_number = $this->input->post('reference_number');
        $subject = $this->input->post('subject');
        $greetingtitle = $this->input->post('greetingtitle');
        $standardtext = $this->input->post('standardtext');
        $address = $this->input->post('address');
        if($this->input->post('convert_to_performa')){
        $convert_to_performa = $this->input->post('convert_to_performa');
        }else{
        $convert_to_performa = '';	
        }
        $product = $this->input->post('product');
        $productsempty = in_array("", $product, true);
        $productspecification = $this->input->post('productspecification');
        $productquantity = $this->input->post('productquantity');
        $terms = $this->input->post('terms');
        $termsempty = in_array("", $terms, true);
       
 

	        $data = array(
			'letter_head'=>$letterhead,
			'customer_name'=>$customer_name,
			'quotation_date'=>$quotation_date,
			'reference_number'=>$reference_number,
			'subject'=>$subject,
			'greetingtitle'=>$greetingtitle,
			'standardtext'=>$standardtext,
			'gst_applicable'=>$gst_applicable,
			'gst_rate'=>$gst_percent,
			'gst_supply'=>$gst_state,
			'address'=>$address,
			'convert_to_performa'=>$convert_to_performa,
			'status'=>1,
			'added_by'=>$user_id);
			$this->db->where('id',$quotationid);
		    $this->db->update('quotation_request',$data);	
		
            if($productsempty != 1){
            for($i=0 ;$i<count($product);$i++){
		    $data = array(
			'quotation_id'=>$quotationid,
			'product_id'=>$product[$i],
			'product_specification_id'=>$productspecification[$i],
			'product_quantity'=>$productquantity[$i]);
            $this->db->insert('quotation_product_details',$data);
	    	}}
	    	if($termsempty != 1){
            foreach($terms as $element){
            $data = array(
			'quotation_id'=>$quotationid,
			'terms_id'=>$element);
            $this->db->insert('quotation_terms',$data);	
            }
            }
          
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Quotation/list_quotations/');
			
		
	   
		
	}


	public function list_quotations()
	{
		
	
	$this->load->view('Quotation/list_quotations');
			
	
		
	}

	public function quotation_list()
	{
		$prestogroup_quotations = array();
		$this->db->select('*');
		$this->db->from('quotation_request');
		$this->db->where('status','1');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edt = "<a href='".page_url."Quotation/edit_quotation_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$edit = " <a href='".page_url."Quotation/view_quotation_detail/".$row->id."'><span class='btn btn-warning btn-xs'>View Quotation</span></a>";
			$iv = "<a href='".page_url."Quotation/performa_invoice/".$row->id."'><i class='fa fa-info'></i></a>";
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-warning btn-xs'>update status</span>";
			$q = $this->db->select('followup_date, status')->from('quotation_followup')->where('quotation_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				if($rows->status=='1'){
					$sta = "ON HOLD";
				}else if($rows->status=='2'){
					$sta = "DEAD";
				}else if($rows->status=='3'){
					$sta = "PI SENT";
				}elseif($rows->status=='4'){
					$sta = "HOT LEAD";
				}else if($rows->status=='5'){
					$sta = "ORDER LOST";
				}else{
					$sta = "";
				}
				$followupinfo = $followup."<br>"."".$sta."<br> ".date('d-m-Y',strtotime($rows->followup_date));
			}else{
				$followupinfo=$followup;
			}
			
			if($row->pisent_status=='1'){
				$btncolor = "success";
			}else{
				$btncolor = "warning";
			}
			if($row->pisent=='1'){
				$viewpi = "<a href='".page_url."Quotation/view_pi/".$row->id."'><span class='btn btn-warning btn-xs'>View PI</span></a>";
				$sendpi = "<a href='".page_url."Quotation/send_pi/".$row->id."'><span class='btn btn-".$btncolor." btn-xs'>Send Email</span></a>";
				$view_pi_history= "<a href='".page_url."Quotation/view_pi_history/".$row->id."'><span class='btn btn-danger btn-xs'>View History</span></a>";
			}else{
				$viewpi="";
				$sendpi ="";
				$view_pi_history = "";
			}
			if($row->quotation_status=='1'){
				$btncolor = "success";
			}else{
				$btncolor = "warning";
			}
			$sendemail = "<a href='".page_url."Quotation/send_quotation/".$row->id."'><span class='btn btn-".$btncolor." btn-xs'>Send Email</span></a>";
			
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'followup'=>$followupinfo,
			'address'=>$row->address,
			'viewpi'=>$viewpi,
			'sendemail'=>$sendemail,
			'edit'=>$edit,
			'sendpi'=>$sendpi,
			'view_pi_history'=>$view_pi_history);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}

		public function view_quotation_detail()
	{
	$this->load->view('Quotation/view_quotation');
			
	
		
	}

	public function edit_quotation_detail()
	{
	$quotation_id = $this->uri->segment(3);
	$this->db->select(array('q.*','c.customer_name as customername','s.title as title','s.message as message'));
	$this->db->from('quotation_request as q');
	$this->db->join('presto_customers as c','c.customer_id = q.customer_name','left');
	$this->db->join('standard_subject as s','s.id = q.subject','left');
	$this->db->where('q.id',$quotation_id);
	$query = $this->db->get();
	$res = $query->row_array();
	$data['quotation_details'] = $res;
	//echo "<pre>";print_r($data['quotation_details']);exit;
	$this->db->select(array('q.*','p.product_name','s.product_specifications'));
	$this->db->from('quotation_product_details as q');
	$this->db->join('presto_product_specifications as p','p.product_id = q.product_id','left');
	$this->db->join('quotation_product_specifications as s','s.id = q.product_specification_id','left');
	$this->db->where('q.quotation_id',$res['id']);
	$query = $this->db->get();
	$productquotations = $query->result_array();
	$data['productquotations'] = $productquotations;
	//echo "<pre>";print_r($data['productquotations']);exit;

	$this->db->select(array('q.*','p.title','p.description'));
	$this->db->from('quotation_terms as q');
	$this->db->join('terms_and_conditions_master as p','p.id = q.terms_id','left');
	$this->db->where('quotation_id',$res['id']);
	$query = $this->db->get();
	$termsquotations = $query->result_array();
	$data['termsquotations'] = $termsquotations;
	//echo "<pre>";print_r($data);exit;
	$this->load->view('Quotation/edit_quotations',$data);
			
	
		
	}

	

	
	
	public function our_products()
	{
		
		
			$this->load->view('Quotation/products');
		
		
	}

	public function add_products()
	{
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$photo=$_FILES['photo']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$product_image=time().'.'.$cat_image;
			move_uploaded_file($_FILES["photo"]["tmp_name"],UPLOADPATH.'compititor/product_img/' . $product_image);
		}else
		{
			$product_image="";
			}	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_product_specifications";
			$data = array(
			'product_name'=>$this->input->post('product_name'),
			'status'=>$this->input->post('status'),
			'product_image'=>$product_image,
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{   $productspecification = $this->input->post('specification');
		$price=$this->input->post('price');
	//	echo "<pre>"; print_r($price);exit;
	        for($i=0;$i<count($productspecification); $i++){
            if(!empty($productspecification[$i])&& !empty($price[$i])){
            $data = array('product_id'=>$result,
            	'product_specifications'=>$productspecification[$i],
            	'price'=>$price[$i]
            
            );	
            $this->db->insert('quotation_product_specifications',$data);
	    	$this->db->insert_id();

            }
	        }
	       
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/our_products/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/our_products/');
		}
		
	
		
	}
	public function products_list()
	{
	    $prestogroup_products = array();
		$this->db->select('a.*,b.*');
		$this->db->from('presto_product_specifications a');
		$this->db->join('quotation_product_specifications b','b.product_id=a.product_id');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Quotation/products_status/".$row->product_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Quotation/products_status/".$row->product_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Quotation/edit_products_detail/".$row->product_id."'><i class='fa fa-pencil'></i></a>";
			$img = "<img src='".product_path.$row->product_image."' width='100px'>";
           

			$prestogroup_products[] = array('sr_no'=>$i,
			'product_name'=>$row->product_name,
			'product_image'=>$img,
			'specifications'=>$row->product_specifications,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_products),
			"iTotalDisplayRecords" => count($prestogroup_products),
			"aaData"=>$prestogroup_products);
			
		echo json_encode($results);
	}
	
	public function products_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$compititor_id =  $this->uri->segment(5);
		$field_name = "product_id";
		$table = "presto_product_specifications";
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
			redirect(page_url.'Quotation/our_products/');
		}

	public function edit_products_detail(){
		$this->load->view('Quotation/edit_products');
	}	
	
	public function update_products_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/edit_products');
		}
		else
		{
		$photo=$_FILES['photo']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$product_image=time().'.'.$cat_image;
			move_uploaded_file($_FILES["photo"]["tmp_name"],UPLOADPATH.'compititor/product_img/' . $product_image);
		}else
		{
			$product_image=$this->input->post('old_img');
			}	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_product_specifications";
			$data = array(
			'product_name'=>$this->input->post('product_name'),
			'status'=>$this->input->post('status'),
			'product_image'=>$product_image,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('product_id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{	
			$productspecification = $this->input->post('specification');
			$price=$this->input->post('price');
			$rec_id=$this->input->post('record_id');
			//	echo "<pre>"; print_r($price);exit;
	        for($i=0;$i<count($productspecification); $i++){
            if(!empty($productspecification[$i])&& !empty($price[$i])){
            $data = array('product_id'=>$result,
            	'product_specifications'=>$productspecification[$i],
            	'price'=>$price[$i]);
if($rec_id[$i]==''){
	$this->db->insert('quotation_product_specifications',$data);
}else{				
			$this->db->where('id',$rec_id[$i]);
            $this->db->update('quotation_product_specifications',$data);
}
			}
	        }
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/our_products/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/our_products/');
		}
		
	}
		
	}
	
	public function customers()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('number', 'Number', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/customer');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_customers";
			$data = array(
			'customer_name'=>$this->input->post('name'),
			'contact_number'=>$this->input->post('number'),
			'email'=>$this->input->post('email'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/customers/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/customers/');
		}
		
	}
		
	}
	public function customer_list()
	{
	    $prestogroup_customers = array();
		$this->db->select('*')->from('presto_customers');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Quotation/customer_status/".$row->customer_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Quotation/customer_status/".$row->customer_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Quotation/edit_customer_detail/".$row->customer_id."'><i class='fa fa-pencil'></i></a>";
			$prestogroup_customers[] = array('sr_no'=>$i,
			'customer_name'=>$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'company_name'=>$row->company_name,
			'address'=>$row->address,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_customers),
			"iTotalDisplayRecords" => count($prestogroup_customers),
			"aaData"=>$prestogroup_customers);
			
		echo json_encode($results);
	}
	
	public function customer_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "customer_id";
		$table = "presto_customers";
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
			redirect(page_url.'Quotation/customers/');
		}

	public function edit_customer_detail(){
		$this->load->view('Quotation/edit_customer');
	}	
	
	public function update_customer_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('number', 'Number', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		$this->form_validation->set_rules('company_name', 'company_name', 'required|trim');
		$this->form_validation->set_rules('address', 'Address', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/edit_customer');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_customers";
			$data = array(
			'customer_name'=>$this->input->post('name'),
			'contact_number'=>$this->input->post('number'),
			'email'=>$this->input->post('email'),
			'company_name'=>$this->input->post('company_name'),
			'address'=>$this->input->post('address'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('customer_id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/customers/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/customers/');
		}
		
	}
		
	}
	public function standard_subject()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		$this->form_validation->set_rules('body_message', 'Message', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/standard_subject');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "standard_subject";
			$data = array(
			'title'=>$this->input->post('title'),
			'message'=>$this->input->post('body_message'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/standard_subject/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/standard_subject/');
		}
		
	}
		
	}
	public function standard_subject_list()
	{
	    $standard_message = array();
		$this->db->select('*')->from('standard_subject');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Quotation/standard_subject_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Quotation/standard_subject_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Quotation/edit_standard_subject_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$standard_message[] = array('sr_no'=>$i,
			'title'=>$row->title,
			'message'=>$row->message,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($standard_message),
			"iTotalDisplayRecords" => count($standard_message),
			"aaData"=>$standard_message);
			
		echo json_encode($results);
	}
	
	public function standard_subject_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "standard_subject";
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
			redirect(page_url.'Quotation/standard_subject/');
		}

	public function edit_standard_subject_detail(){
		$this->load->view('Quotation/edit_standard_message');
	}	
	
	public function update_standard_subject_detail()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('title', 'Title', 'required|trim');
		$this->form_validation->set_rules('body_message', 'Message', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/edit_standard_message');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "standard_subject";
			$data = array(
			'title'=>$this->input->post('title'),
			'message'=>$this->input->post('body_message'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/standard_subject/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/standard_subject/');
		}
		
	}
		
	}
	
	
	

	
	
	public function add_clients()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('number', 'number', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('Quotation/client_vertical');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "email_signature";
			$data = array(
			'name'=>$this->input->post('name'),
			'contact_number'=>$this->input->post('number'),
			'email'=>$this->input->post('email'),
			'address'=>$this->input->post('address'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/email_signature/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Quotation/email_signature/');
		}
		
	}
		
	}



	public function standardtext()
	{
	   
	   $subject = $this->input->post('subject');
	   $this->db->select('*')->from('standard_subject')->where('id',$subject);                              
       $query = $this->db->get();
       $res = $query->row_array();
       echo '<input type="text" name="standardtext" id="standardtext" class="form-control" value="'.$res['message'].'">';
			
	}

	public function productspecifications()
	{
	   
	   echo "<option value=''>--Select Product Specifications--</option>";
	$productspecification = $this->input->post('product');
	$productspecificationid = $this->input->post('specification');

	 $this->db->select('*')->from('quotation_product_specifications')->where('product_id',$productspecification);                              
       $query = $this->db->get();
       $res = $query->result_array();
			foreach($res as $element)
			{ if($element['id'] == $productspecificationid){
				$selected = 'selected';
			}else{
				$selected = '';
			}
				echo "<option value=".$element['id']." ".$selected.">".$element['product_specifications']."</option>";
				}
			
	}

		public function deletequotationproduct()
	{     $json = array();
	      $productid = $this->input->post('productid');
	      $this->db->where('id', $productid);
          $this->db->delete('quotation_product_details'); 
          $json['success'] = TRUE;
          echo json_encode($json);
	}

		public function deleteterms()
	{     $json = array();
	      $termsid = $this->input->post('termsid');
	      $this->db->where('id', $termsid);
          $this->db->delete('quotation_terms'); 
          $json['success'] = TRUE;
          echo json_encode($json);
	}

		

public function email_signature()
    {
        
        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('number', 'number', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        $this->form_validation->set_rules('status', 'Status', 'required|trim');
        $user_id =$this->session->userdata['logged_in']['user_id'];        
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('Quotation/email-signature');
        }
        else
        {
        
        date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s'); 
        $table = "email_signature";
            $data = array(
            'name'=>$this->input->post('name'),
            'contact_number'=>$this->input->post('number'),
            'email'=>$this->input->post('email'),
            'address'=>$this->input->post('address'),
            'status'=>$this->input->post('status'),
            'added_on'=>$date,
            'added_by'=>$user_id);
            
        $result  = $this->master->insert_record($table,$data);    
        if($result)
        {
            $this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
            redirect(page_url.'Quotation/email_signature/');
            
        }else
        {
            $this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
            redirect(page_url.'Quotation/email_signature/');
        }
        
    }
        
    }
    public function signature_list()
    {
        $email_signature = array();
        $this->db->select('*')->from('email_signature');
        $query = $this->db->get();
        $res = $query->result();
        $i=1;
        foreach($res as $row)
        {
            $status = $row->status;
            if($status=='1')
            {
                $sta =  "<a href='".page_url."Quotation/signature_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
            }else
            {
                $sta =  "<a href='".page_url."Quotation/signature_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
            }
            $edit = "<a href='".page_url."Quotation/edit_email_signature_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
            $email_signature[] = array('sr_no'=>$i,
            'name'=>$row->name,
            'contact_number'=>$row->contact_number,
            'email'=>$row->email,
            'address'=>$row->address,
            'status'=>$sta,
            'edit'=>$edit);
            $i++;
        }
            $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($email_signature),
            "iTotalDisplayRecords" => count($email_signature),
            "aaData"=>$email_signature);
            
        echo json_encode($results);
    }
    
    public function signature_status()
    {
        /*************Dynamic information****************/
        $identifier =  $this->uri->segment(3);
        $sval =  $this->uri->segment(4);
        $field_name = "id";
        $table = "email_signature";
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
            redirect(page_url.'Quotation/email_signature/');
        }

    public function edit_email_signature_detail(){
        $this->load->view('Quotation/edit_email_signature');
    }    
    
    public function update_email_signature_detail()
    {
        
        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('number', 'number', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        $this->form_validation->set_rules('status', 'Status', 'required|trim');
        $user_id =$this->session->userdata['logged_in']['user_id'];        
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('Quotation/edit_email_signature');
        }
        else
        {
        
        date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s'); 
        $table = "email_signature";
            $data = array(
            'name'=>$this->input->post('name'),
            'contact_number'=>$this->input->post('number'),
            'email'=>$this->input->post('email'),
            'address'=>$this->input->post('address'),
            'status'=>$this->input->post('status'),
            'added_on'=>$date,
            'added_by'=>$user_id);
        $this->db->where('id',$this->uri->segment(3));    
        $result  = $this->db->update($table,$data);    
        if($result)
        {
            $this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
            redirect(page_url.'Quotation/email_signature/');
            
        }else
        {
            $this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
            redirect(page_url.'Quotation/email_signature/');
        }
        
    }
        
    }
		
	
	public function performa_invoice(){
        $this->load->view('Quotation/invoice');
    } 
	
	public function followup(){
        $this->load->view('Quotation/followup');
    } 
	
	public function quotation_followup()
    {
        
        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('followup_date', 'Followup Date', 'required|trim');
        $this->form_validation->set_rules('status', 'status', 'required|trim');
        $user_id =$this->session->userdata['logged_in']['user_id'];        
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('Quotation/followup');
        }
        else
        {
        
        date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s'); 
        $table = "quotation_followup";
		    $data = array(
            'followup_date'=>date('Y-m-d',strtotime($this->input->post('followup_date'))),
            'quotation_id'=>$this->uri->segment(3),
            'status'=>$this->input->post('status'),
			'remarks'=>$this->input->post('remarks'),
            'added_on'=>$date,
            'added_by'=>$user_id);
            
        $result  = $this->master->insert_record($table,$data);    
        if($result)
        {
			if($this->input->post('status')=='3'){
				$data = array('terms_conditions'=>$this->input->post('terms_conditions'),
				'pisent'=>'1',
				'packing_charges'=>$this->input->post('packing_charges'),
				'packingcharges_type'=>$this->input->post('packingtype'),
				'freight_charges'=>$this->input->post('freight_charges'));
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('quotation_request',$data);
			}
			$products= $_REQUEST['products_name'];
			$qty= $_REQUEST['qty'];
			$discount_type= $_REQUEST['discount_type'];
			$discount = $_REQUEST['discount'];
			$recordid = $_REQUEST['recordid'];
			for($i=0 ;$i<count($products);$i++){
		    $data = array(
			'product_quantity'=>$qty[$i],
			'discount_type'=>$discount_type[$i],
			'discount'=>$discount[$i]);
			$this->db->where('id',$recordid[$i]);
            $this->db->update('quotation_product_details',$data);
	    	}
			
			$product= $_REQUEST['product'];
			if(isset($_REQUEST['product'])){
			$tags1=count($_REQUEST['product']);
			if($tags1>0)
			{
			for($i=0;$i<count($product);$i++){
			$productdata= $_REQUEST['product'];	
			$productquantity= $_REQUEST['productquantity'];
			if($productdata[$i]!==''){
		    $data1 = array(
			'product_id'=>$productdata[$i],
			'quotation_id'=>$this->uri->segment(3),
			'product_quantity'=>$productquantity[$i]);
			$this->db->insert('quotation_product_details',$data1);
			}
			}
	    	}
			}
			if($this->input->post('status')=='6' || $this->input->post('status')=='2' || $this->input->post('status')=='5'){
				$data = array('status'=>'0',
				'orderstatus'=>$this->input->post('status'),
				'orderlost_reason'=>$this->input->post('orderlostreason'));				
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('quotation_request',$data);
					
			}
			if($this->input->post('status')=='6'){
				
				
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
            redirect(page_url.'FMS/testronix_order/'.$this->uri->segment(3));	
			}else{
			 $this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
            redirect(page_url.'Quotation/list_quotations');	
			}
           
            
        }else
        {
            $this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
            redirect(page_url.'Quotation/list_quotations');
        }
        
    }
        
    }
	public function hot_leads(){
		$this->load->view('Quotation/hot_leads');
	}
	public function view_pi(){
		$this->load->view('Quotation/view_pi');
	}
	
	public function hot_leads_list()
	{
		$prestogroup_quotations = array();
		$q = $this->db->select('quotation_id')->from('quotation_followup')->where('status',)->get();
		$this->db->select('*');
		$this->db->from('quotation_request');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edt = "<a href='".page_url."Quotation/edit_quotation_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$edit = " <a href='".page_url."Quotation/view_quotation_detail/".$row->id."'><span class='btn btn-success btn-xs'>View Quotation</span></a>";
			$iv = "<a href='".page_url."Quotation/performa_invoice/".$row->id."'><i class='fa fa-info'></i></a>";
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
			$q = $this->db->select('followup_date, status')->from('quotation_followup')->where('quotation_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				if($rows->status=='1'){
					$sta = "ON HOLD";
				}else if($rows->status=='2'){
					$sta = "DEAD";
				}else if($rows->status=='3'){
					$sta = "PI SENT";
				}elseif($rows->status=='4'){
					$sta = "HOT LEAD";
				}else if($rows->status=='5'){
					$sta = "ORDER LOST";
				}else{
					$sta = "";
				}
				$followupinfo = $followup."<br>"."Current Status: ".$sta."<br>Date:- ".date('d-m-Y',strtotime($rows->followup_date));
			}else{
				$followupinfo=$followup;
			}
			
			if($row->pisent=='1'){
				$viewpi = "<a href='".page_url."Quotation/view_pi/".$row->id."'><span class='btn btn-warning btn-xs'>View PI</span></a>";
			}else{
				$viewpi="";
			}
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'followup'=>$followupinfo,
			'address'=>$row->address,
			'viewpi'=>$viewpi,
			'edit'=>$edit);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	function send_quotation(){
		
		$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','6')->get();
		foreach($q->result() as $headerimg);
				
		$this->uri->segment(3);
		
		
		$q = $this->db->select('quotation_date, terms_conditions, email, title, customer_name, address, reference_number, quotation_status')->from('quotation_request')->where('id',$this->uri->segment(3))->get();
		foreach($q->result() as $row);
		
			if($row->quotation_status=='' || $row->quotation_status=='NULL'){
				$user_id =$this->session->userdata['logged_in']['user_id'];	
				$nextdate = date('Y-m-d', strtotime(' +1 day'));
				date_default_timezone_set("Asia/Kolkata");
				$date =  date('Y-m-d H:i:s');
				$data = array('quotation_id'=>$this->uri->segment(3),
				'followup_date'=>$nextdate,
				'status'=>'4',
				'added_on'=>$date,
				'added_by'=>$user_id);
				$this->db->insert('quotation_followup',$data);
			}
			$data = array('quotation_status'=>'1');
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('quotation_request',$data);
		
		
		$message = '<div style="width:900px; padding:10px 10px 10px 10px">
			<div><div><img src="'.quotationimg.$headerimg->script.'" style="float:right; width:200px;"></div><br/><br/>
			<div style="padding-top:30px"></div>
				<span style="font-family:calibri;">Dear '.$row->title.' '.$row->customer_name.', <span style="float:right; font-family:calibri;">Date: '.date('d-m-Y').'</span></span><br/> 
				<span style="font-family:calibri;">'.$row->address.'<span style="float:right; font-family:calibri;">Reference No: '.$row->reference_number.'</span> </span><br/><br/>
<p style="font-family:calibri;">A warm Namaste from Testronix Instruments !!!<br/><br/>
We thank you very much for your most valuable enquiry and appreciate the interest & confidence shown on Testronix Instruments . </br>

We are pleased to submit our offer for the Testing Instruments as per the details given. <br/>
Testronix Instruments is manufacturers of Lab testing Products for Paper &Packaging, Paint & Plating , PET Bottles & Preform and Plastic etc. <br/><br/>
We are dedicated to provide<br/>
</p>
</p>
<ul style="font-family:calibri;">
<li>High Quality Products</li>
<li>Good Application Engineering to suit your requirement</li>
<li>Timely delivery </li>
<li>Smooth Execution</li>
<li>Excellent after sales support </li>
</ul>
<p style="font-family:calibri;">With over 25 years vast knowledge and experience in these instruments and industry, we will be able to address various applications that you require. </p>
<p style="font-family:calibri;">Please find attached our Techno Commercial proposal. Thanking you and looking forward to work with you .<br/>
<div style="padding-top:70px"></div>
Thanks & Regards, 
</p>
<p>
<p style="font-family:calibri;">
Sales Cell: +91-9971040808 <br/>
Email: info@testronixinstruments.com<br/>
<b>Testronix Instruments</b><br>
Address : I-10A, DLF Industrial Area, Phase-1,<br>
Faridabad 121003, Haryana, India
</p></div>
<div style="padding-top:100px"></div>
<hr style="color:#f4f1f15e">
<div style="padding-top:100px"></div>
<div><div><img src="'.quotationimg.$headerimg->script.'" style="float:right; width:200px;">
 
</div><br/><br/>
<span style="float:right; font-family:calibri; padding-top:20px">Date: '.date('d-m-Y').'<br/>
Reference No: '.$row->reference_number.'</span></span><br/> 
				
<div style="padding-top:100px"></div>
<table cellpadding="0" cellspacing="0" width="100%" align="left" border="1" style="font-size:14px; font-family:calibri;"> 
                <thead>    
                <tr style="background-color:#D9D9D9">
								<th>S. NO.</th>
								<th>PRODUCT DESCRIPTION </th>
								<th>UNIT PRICE</th>
							</tr>
				</thead>
				<tbody>';
				$i=1;
					$q = $this->db->select('a.quotation_id, a.product_quantity, b.instruments_name,b.mvalue,b.model_number, a.product_quantity, b.image, a.discount, a.discount_type')->from('quotation_product_details a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.quotation_id',$this->uri->segment(3))->get();
					//echo "<pre>"; print_r($q->result()); exit;
					foreach($q->result() as $prdrows){
					$message.='<tr>
						<td style="text-align:center;">'.$i.'</td>
						<td style="text-align:center;">'.$prdrows->instruments_name.' '.$prdrows->model_number.'</td>
						<td style="text-align:center;">'.$prdrows->product_quantity*$prdrows->mvalue;
								$total[] = $prdrows->product_quantity*$prdrows->mvalue.'</td>';
								
								$prdprice = $prdrows->product_quantity*$prdrows->mvalue;
								$discountprice= "0";
								if($prdrows->discount_type!==''){
									
									if($prdrows->discount_type=='0'){
										$discount= $prdrows->discount;
										$disprice = $prdprice*$discount/100;
										$discountprice = $prdprice-$disprice;
									}else{
										$discount= $prdrows->discount;
										$discountprice = $prdprice-$discount;
									}
								}
								
							$discounttotal[] = $discountprice;
								
						$message.='</tr>';
							$i++;}
				$message.='</tbody></table>
				<div style="padding-top:150px"></div>
				<p style="font-bold; font-family:calibri;"><u>Terms & Conditions:</u></p>';
				
				if($row->terms_conditions==''){
									$message.='<ul style="font-family:calibri;">'; 
									$k=1;
									$q = $this->db->select('title, description')->from('terms_and_conditions_master')->where('status','1')->where('term_conditions_for','1')->get();
									foreach($q->result() as $termconditions){
									
										$message.='<li>'.$termconditions->title.' : '.$termconditions->description.'</li>';
									$k++;}
									$message.='</ul>';
									}else{
									$message.='<p style="font-family:calibri;">'.$row->terms_conditions.'</p>';
									 }
			$message.='</div>
			
		<div>';
		$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','5')->get();
				foreach($q->result() as $footer);
		$message.='<div style="float:left; padding-top:50px"><p style="font-family:calibri;">
Sales Cell: +91-9971040808 <br/>
Email: info@testronixinstruments.com<br/>
<b>Testronix Instruments</b><br>
Address : I-10A, DLF Industrial Area, Phase-1,<br>
Faridabad 121003, Haryana, India
</p></div><div style="padding-top:100px"></div>';

		//echo $message; exit;
		
		$subjectname = "Offer for Lab Testing Instruments";
					$this->email->set_mailtype("html");
					$this->email->to($row->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($message);
    				$result11=$this->email->send();
					$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Email successfully sent.</span></div><br/>');
redirect(page_url.'Quotation/list_quotations');
		
	}
	
	public function todays_followup(){
		$this->load->view('Quotation/todays_followup');
	}
	
	public function followup_list(){
		$i=1;
		$prestogroup_quotations= array();
		$q = $this->db->select('a.id, a.followup_date, b.address, b.company_name, b.email, b.contact_number, b.title, b.customer_name, b.quotation_date, b.reference_number, b.id')->from('quotation_followup a')->join('quotation_request b','b.id=a.quotation_id','left')->where('a.followup_date',date('Y-m-d'))->get();
		foreach($q->result() as $row){
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'update_followup'=>$followup,
			'followup'=>date('d-m-Y',strtotime($row->followup_date)),
			'address'=>$row->address);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	function send_pi(){
		
		$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','6')->get();
		foreach($q->result() as $headerimg);
				
		$this->uri->segment(3);
		$q = $this->db->select('quotation_date, terms_conditions, email, title, customer_name, address, reference_number, packingcharges_type, packing_charges, freight_charges,pisent_status, state_id')->from('quotation_request')->where('id',$this->uri->segment(3))->get();
		foreach($q->result() as $row);
		
		$data = array('pisent_status'=>'1');
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('quotation_request',$data);
		$query = $this->db->select('pi_id')->from('pi_history')->get();
		   $res = count($query->result());
		   if($res<=0){
			   $pino = "TESTRONIX-1";
		   }else{
			   $pino= "TESTRONIX-".$res;
		   }
		   $user_id =$this->session->userdata['logged_in']['user_id']; 
		date_default_timezone_set("Asia/Kolkata");
		$data = array('quotation_id'=>$this->uri->segment(3),
		'pi_date'=>date('Y-m-d'),
		'pi_number'=>$pino,
		'terms_conditions'=>$row->terms_conditions,
		'pi_send_on'=>date('Y-m-d H:i:s'),
		'pi_sent_by'=>$user_id);
		$this->db->insert('pi_history',$data);
		$last_id = $this->db->insert_id();
		$q2 = $this->db->select('product_id, product_quantity, discount_type, discount')->from('quotation_product_details')->where('quotation_id',$this->uri->segment(3))->get();
		foreach($q2->result() as $prdinfo){
			$data = array('quotation_id'=>$this->uri->segment(3),
			'pi_id'=>$last_id,
			'product_id'=>$prdinfo->product_id,
			'qty'=>$prdinfo->product_quantity,
			'discount_type'=>$prdinfo->discount_type,
			'discount_value'=>$prdinfo->discount);
			$this->db->insert('pi_product_detail',$data);
			
		}
		
		
		$message = '<div style="width:900px; padding:10px 10px 10px 10px">
			<div><div><img src="'.quotationimg.$headerimg->script.'" style="float:right; width:200px;"></div><br/><br/>
			<div style="padding-top:30px"></div>
				<span style="font-family:calibri;">Dear '.$row->title.' '.$row->customer_name.', <span style="float:right; font-family:calibri;">Date: '.date('d-m-Y').'</span></span><br/> 
				<span style="font-family:calibri;">'.$row->address.'<span style="float:right; font-family:calibri;">Reference No: '.$row->reference_number.'</span> </span><br/><br/>
<p style="font-family:calibri;">A warm Namaste from Testronix Instruments !!!<br/><br/>
We thank you very much for your most valuable enquiry and appreciate the interest & confidence shown on Testronix Instruments . </br>

We are pleased to submit our offer for the Testing Instruments as per the details given. <br/>
Testronix Instruments is manufacturers of Lab testing Products for Paper &Packaging, Paint & Plating , PET Bottles & Preform and Plastic etc. <br/><br/>
We are dedicated to provide<br/>
</p>
</p>
<ul style="font-family:calibri;">
<li>High Quality Products</li>
<li>Good Application Engineering to suit your requirement</li>
<li>Timely delivery </li>
<li>Smooth Execution</li>
<li>Excellent after sales support </li>
</ul>
<p style="font-family:calibri;">With over 25 years vast knowledge and experience in these instruments and industry, we will be able to address various applications that you require. </p>
<p style="font-family:calibri;">Please find attached our Techno Commercial proposal. Thanking you and looking forward to work with you .<br/>
<div style="padding-top:70px"></div>
Thanks & Regards, 
</p>
<p>
<p style="font-family:calibri;">
Sales Cell: +91-9971040808 <br/>
Email: info@testronixinstruments.com<br/>
<b>Testronix Instruments</b><br>
Address : I-10A, DLF Industrial Area, Phase-1,<br>
Faridabad 121003, Haryana, India
</p></div>
<div style="padding-top:100px"></div>
<hr style="color:#f4f1f15e">
<div style="padding-top:100px"></div>
<div><div><img src="'.quotationimg.$headerimg->script.'" style="float:right; width:200px;">
 
</div><br/><br/>
<span style="float:right; font-family:calibri; padding-top:20px">Date: '.date('d-m-Y').'<br/>
Reference No: '.$row->reference_number.'</span></span><br/> 
				
<div style="padding-top:100px"></div>
<table cellpadding="0" cellspacing="0" width="100%" align="left" border="1" style="font-size:14px; font-family:calibri;"> 
                <thead>    
                <tr style="background-color:#D9D9D9">
								<th>S. NO.</th>
								<th>PRODUCT DESCRIPTION </th>
								<th>RATE</th>
								<th>AMOUNT</th>
							</tr>
				</thead>
				<tbody>';
				$i=1;
					$q = $this->db->select('a.quotation_id, a.product_quantity, b.instruments_name,b.mvalue,b.model_number, a.product_quantity, b.image, a.discount, a.discount_type')->from('quotation_product_details a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.quotation_id',$this->uri->segment(3))->get();
					foreach($q->result() as $prdrows){
						$prdprice = $prdrows->product_quantity*$prdrows->mvalue;
								$discountprice= $prdprice;
								if($prdrows->discount_type!=''){
									
									if($prdrows->discount_type='0'){
										$discount= $prdrows->discount;
										$disprice = $prdprice*$discount/100;
										$discountprice = $prdprice-$disprice;
									}else{
										$discount= $prdrows->discount;
										$discountprice = $prdprice-$discount;
									}
								}
							$discounttotal[] = $discountprice;
							$total[] = $prdrows->product_quantity*$prdrows->mvalue;
							
					$message.='<tr>
						<td style="text-align:center;">'.$i.'</td>
						<td style="text-align:center;">'.strtoupper($prdrows->instruments_name).' '.$prdrows->model_number.'</td>
						<td style="text-align:center;">'.$prdrows->product_quantity*$prdrows->mvalue.'</td>
						<td style="text-align:center;">'.$discountprice.'</td>';
					$message.='</tr>';
							$i++;}
							$finaltotal = array_sum($discounttotal);
							if($row->packingcharges_type=='1'){
								$type = "(%)";
								$packingcharges = $finaltotal*$row->packing_charges/100;
							}else if($row->packingcharges_type=='1'){
								$packingcharges=$row->packing_charges;
								$type = "";
							}else{
							$packingcharges="";	
							$type = "";
							}
							$igst="";
							if($row->state_id=='13'){
							$cgstamount = $finaltotal*0.09;
							$sgstamount = $finaltotal*0.09;
							$gstcharges = $cgstamount+$sgstamount;
							}else{
								$igst = $finaltotal*0.18;
								$gstcharges = $igst;
							}
							$greatgrand = $finaltotal+$packingcharges+$gstcharges;
					$message.='<tr>
					<td colspan="2" style="text-align:center;">Total </td>
						<td style="text-align:center;">'.array_sum($total).'</td>
						<td style="text-align:center;">'.$finaltotal.'</td>
					</tr>';
					$message.='<tr>
						<td  colspan="3" style="text-align:center;">@ Packing Charges '.$type.'</td>
						<td style="text-align:center;">'.$packingcharges.'</td>
						
					</tr>';
					if($row->state_id=='13'){
						$cgstamount = $finaltotal*0.09;
						$sgstamount = $finaltotal*0.09;
						$message.='<tr>
							<td colspan="3" style="text-align:center;">CGST:</td>
							<td style="text-align:center;">'.$cgstamount.'</td>
						</tr>';
						$message.='<tr>
							<td colspan="3" style="text-align:center;">SGST:</td>
							<td style="text-align:center;">'.$sgstamount.'</td>
						</tr>';
					}
					else{
						
						$message.='<tr>
							<td colspan="3" style="text-align:center;">CGST:</td>
							<td style="text-align:center;">'.$igst.'</td>
						</tr>';
					}
					
					
					
				$message.='<tr>
					<td colspan="3" style="text-align:center;">Grand Total:</td>
					<td style="text-align:center; font-size:15px; font-weight:bold;">'.$greatgrand.'</td>
				</tr>';
					
				$message.='</tbody></table>
				<div style="padding-top:150px"></div>
				<p style="font-bold; font-family:calibri;"><u>Terms & Conditions:</u></p>';
				
				if($row->terms_conditions==''){
				$message.='<ul style="font-family:calibri;">'; 
				$k=1;
				$q = $this->db->select('title, description')->from('terms_and_conditions_master')->where('status','1')->where('term_conditions_for','1')->get();
				foreach($q->result() as $termconditions){
				
					$message.='<li>'.$termconditions->title.' : '.$termconditions->description.'</li>';
				$k++;}
				$message.='</ul>';
				}else{
				$message.='<p style="font-family:calibri;">'.$row->terms_conditions.'</p>';
									 }
				$message.='</div>
			
			<div>';
			//echo $message; exit;
				$q = $this->db->select('script')->from('calibration_script')->where('company_id','2')->where('id','5')->get();
				foreach($q->result() as $footer);
				$message.='<div style="float:left; padding-top:50px"><p style="font-family:calibri;">
				Sales Cell: +91-9971040808 <br/>
				Email: info@testronixinstruments.com<br/>
				<b>Testronix Instruments</b><br>
				Address : I-10A, DLF Industrial Area, Phase-1,<br>
				Faridabad 121003, Haryana, India
				</p></div><div style="padding-top:100px"></div>';

					$subjectname = "Offer for Lab Testing Instruments";
					$this->email->set_mailtype("html");
					$this->email->to($row->email);
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject($subjectname);
    				$this->email->message($message);
    				$result11=$this->email->send();
					$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Email successfully sent.</span></div><br/>');
					redirect(page_url.'Quotation/list_quotations');
		
	}
	
	public function missed_followup(){
		$this->load->view('Quotation/missed_followup');
	}
	
	public function missed_followup_list(){
		$i=1;
		$prestogroup_quotations= array();
		$q = $this->db->select('a.id, a.followup_date, b.address, b.company_name, b.email, b.contact_number, b.title, b.customer_name, b.quotation_date, b.reference_number, b.id')->from('quotation_followup a')->join('quotation_request b','b.id=a.quotation_id','left')->where('a.followup_date<',date('Y-m-d'))->group_by('a.quotation_id')->get();
		foreach($q->result() as $row){
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'update_followup'=>$followup,
			'followup'=>date('d-m-Y',strtotime($row->followup_date)),
			'address'=>$row->address);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	public function upcoming_followup(){
		$this->load->view('Quotation/upcoming_followup');
	}
	
	public function upcoming_followup_list(){
		$i=1;
		$prestogroup_quotations= array();
		$q = $this->db->select('a.id, a.followup_date, b.address, b.company_name, b.email, b.contact_number, b.title, b.customer_name, b.quotation_date, b.reference_number, b.id')->from('quotation_followup a')->join('quotation_request b','b.id=a.quotation_id','left')->where('a.followup_date>',date('Y-m-d'))->group_by('a.quotation_id')->get();
		foreach($q->result() as $row){
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'update_followup'=>$followup,
			'followup'=>date('d-m-Y',strtotime($row->followup_date)),
			'address'=>$row->address);
			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	function lost_orders(){
		$this->load->view('Quotation/order_lost');
	}
	
	public function lost_orders_list()
	{
		$prestogroup_quotations = array();
		$this->db->select('*');
		$this->db->from('quotation_request');
		$this->db->where('status','0');
		$this->db->where('orderstatus','5');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edt = "<a href='".page_url."Quotation/edit_quotation_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$edit = " <a href='".page_url."Quotation/view_quotation_detail/".$row->id."'><span class='btn btn-success btn-xs'>View Quotation</span></a>";
			$iv = "<a href='".page_url."Quotation/performa_invoice/".$row->id."'><i class='fa fa-info'></i></a>";
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>update status</span>";
			$q = $this->db->select('followup_date, status')->from('quotation_followup')->where('quotation_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				if($rows->status=='1'){
					$sta = "ON HOLD";
				}else if($rows->status=='2'){
					$sta = "DEAD";
				}else if($rows->status=='3'){
					$sta = "PI SENT";
				}elseif($rows->status=='4'){
					$sta = "HOT LEAD";
				}else if($rows->status=='5'){
					$sta = "ORDER LOST";
				}else{
					$sta = "";
				}
				$followupinfo = $followup."<br>"."".$sta."<br> ".date('d-m-Y',strtotime($rows->followup_date));
			}else{
				$followupinfo=$followup;
			}
			
			if($row->pisent=='1'){
				$viewpi = "<a href='".page_url."Quotation/view_pi/".$row->id."'><span class='btn btn-warning btn-xs'>View PI</span></a>";
				$sendpi = "<a href='".page_url."Quotation/send_pi/".$row->id."'><span class='btn btn-success btn-xs'>Send Email</span></a>";
			}else{
				$viewpi="";
				$sendpi ="";
			}
			
			
			$sendemail = "<a href='".page_url."Quotation/send_quotation/".$row->id."'><span class='btn btn-success btn-xs'>Send Email</span></a>";
			
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'followup'=>$followupinfo,
			'address'=>$row->address,
			'viewpi'=>$viewpi,
			'sendemail'=>$sendemail,
			'edit'=>$edit,
			'lostreason'=>$row->orderlost_reason,
			'sendpi'=>$sendpi);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	function closed_one(){
		$this->load->view('Quotation/closed_one');
	}
	
	public function closed_one_list()
	{
		$prestogroup_quotations = array();
		$this->db->select('*');
		$this->db->from('quotation_request');
		$this->db->where('status','0');
		$this->db->where('orderstatus','6');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edt = "<a href='".page_url."Quotation/edit_quotation_detail/".$row->id."'><i class='fa fa-pencil'></i></a>";
			$edit = " <a href='".page_url."Quotation/view_quotation_detail/".$row->id."'><span class='btn btn-success btn-xs'>View Quotation</span></a>";
			$iv = "<a href='".page_url."Quotation/performa_invoice/".$row->id."'><i class='fa fa-info'></i></a>";
			$followup = "<a href='".page_url."Quotation/followup/".$row->id."'><span class='btn btn-success btn-xs'>update status</span>";
			$q = $this->db->select('followup_date, status')->from('quotation_followup')->where('quotation_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				if($rows->status=='1'){
					$sta = "ON HOLD";
				}else if($rows->status=='2'){
					$sta = "DEAD";
				}else if($rows->status=='3'){
					$sta = "PI SENT";
				}elseif($rows->status=='4'){
					$sta = "HOT LEAD";
				}else if($rows->status=='5'){
					$sta = "ORDER LOST";
				}else{
					$sta = "";
				}
				$followupinfo = $followup."<br>"."".$sta."<br> ".date('d-m-Y',strtotime($rows->followup_date));
			}else{
				$followupinfo=$followup;
			}
			
			if($row->pisent=='1'){
				$viewpi = "<a href='".page_url."Quotation/view_pi/".$row->id."'><span class='btn btn-warning btn-xs'>View PI</span></a>";
				$sendpi = "<a href='".page_url."Quotation/send_pi/".$row->id."'><span class='btn btn-success btn-xs'>Send Email</span></a>";
			}else{
				$viewpi="";
				$sendpi ="";
			}
			
			$sendemail = "<a href='".page_url."Quotation/send_quotation/".$row->id."'><span class='btn btn-success btn-xs'>Send Email</span></a>";
			
			$make_order = "<a href='".page_url."FMS/testronix_order/".$row->id."'><span class='btn btn-success btn-xs'>Create Order</span></a>";
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'reference_number'=>$row->reference_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'followup'=>$followupinfo,
			'address'=>$row->address,
			'viewpi'=>$viewpi,
			'sendemail'=>$sendemail,
			'edit'=>$edit,
			'make_order'=>$make_order,
			'sendpi'=>$sendpi);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	public function view_pi_history(){
		$this->load->view('Quotation/pi_history');
	}
	
	public function pi_history_list()
	{
		
		$prestogroup_quotations = array();
		$this->db->select('a.*, b.quotation_date, b.reference_number, b.address, b.company_name,b.email, b.contact_number, b.title, b.customer_name');
		$this->db->from('pi_history a');
		$this->db->join('quotation_request b','a.quotation_id=b.id','left');
		$this->db->where('a.quotation_id',$this->uri->segment(3));
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$q = $this->db->select('followup_date, status')->from('quotation_followup')->where('quotation_id',$this->uri->segment(3))->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rows);
				if($rows->status=='1'){
					$sta = "ON HOLD";
				}else if($rows->status=='2'){
					$sta = "DEAD";
				}else if($rows->status=='3'){
					$sta = "PI SENT";
				}elseif($rows->status=='4'){
					$sta = "HOT LEAD";
				}else if($rows->status=='5'){
					$sta = "ORDER LOST";
				}else{
					$sta = "";
				}
				
			}
			$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px'>SR No.</th><th style='padding:2px 2px 2px 2px'>PRODUCT NAME</th><th style='padding:2px 2px 2px 2px'>QTY</th> <th style='padding:2px 2px 2px 2px'>PRICE</th> <th style='padding:2px 2px 2px 2px'>DISCOUNT TYPE</th> <th style='padding:2px 2px 2px 2px'>AMOUNT</th></tr>";
			$m=1;
			$query = $this->db->select('a.product_id, a.qty, a.discount_type, a.discount_value, a.discount_value, b.instruments_name, b.mvalue')->from('pi_product_detail a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.pi_id',$row->pi_id)->get();
			foreach($query->result() as $record){
				if($record->discount_type=='0'){
					$discounttype = $record->discount_value."%";
					$productamount = $record->mvalue*$record->discount_value/100;
					$fvalue = $record->mvalue-$productamount;
				}else if($record->discount_type=='1'){
					$discounttype = "Fixed: ".$record->discount_value;
					$productamount = $record->mvalue-$record->discount_value;
					$fvalue = $productamount;
				}else{
					$productamount  = $record->mvalue;
					$fvalue  = $record->mvalue;
					$discounttype ="";
				}
				$productvalue[] = $fvalue;
				
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($m)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->instruments_name)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->qty)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($record->mvalue)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($discounttype)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center'>".strtoupper($fvalue)."</td>";
				
				$html.="</tr>";
				$m++;
			}
		
			$finalamount = array_sum($productvalue);
			//$trv_amount =  array_sum($travelamt);
			
			$html.="<tr>
				<td colspan='5'></td>
				<td> <strong style='color:red;'>TOTAL INR-".$finalamount."</strong><td>
			</tr></table>";
			
			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>date('d-m-Y',strtotime($row->quotation_date)),
			'pi_date'=>date('d-m-Y',strtotime($row->pi_date)),
			'pi_number'=>$row->reference_number,
			'reference_number'=>$row->pi_number,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->title." ".$row->customer_name,
			'contact_number'=>$row->contact_number,
			'email'=>$row->email,
			'address'=>$row->address,
			'product_detail'=>$html);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($prestogroup_quotations),
			"iTotalDisplayRecords" => count($prestogroup_quotations),
			"aaData"=>$prestogroup_quotations);
			echo json_encode($results);
	}
	
	public function communication_master() {
		$this->load->view('Quotation/communication_master');
		}

		public function add_communication() {
			$designation = $this->input->post('designation');
			$email_body = $this->input->post('email_body');

		$data = array(
					'lead_stage' => $this->input->post('lead_stage'),
					'days' => $this->input->post('days'),
					'include_pdf' => $this->input->post('include_pdf'),
					'include_quotation' => $this->input->post('include_quotation'),
					'include_pi' => $this->input->post('include_pi'),
					'status' => $this->input->post('status')
					);

		$this->db->insert('testronix_god_mode', $data);
		$last_id = $this->db->insert_id();

		for ($i=0; $i < count($email_body); $i++) { 
		$datas = array(
					'god_mode_id' => $last_id,
					'designation' => $designation[$i],
					'email_body' => $email_body[$i]
					);

		$this->db->insert('designation_wise_communication', $datas);

		}


		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('message', '<div class="alert alert-success">Record Saved Successfully.</div><br/>');
			redirect(page_url.'Quotation/communication_master');
		}
	}

	public function communication_master_list() {
		$this->load->view('Quotation/communication_master_list');
	}

	public function communication_master_listing() {
		$data = array();
		$include_pdf = '';
		$include_quotation = '';
		$include_pi = '';
		$query = $this->db->select('*')
						  ->from('testronix_god_mode')
						  ->get();
		$result = $query->result();
		$i=1;
		foreach($result as $row) {
			$edit = "<a href='".page_url."Quotation/edit_communication/".$row->id."'><i class='fa fa-pencil'></i></a>";
			
			if ($row->lead_stage == 0) {
				$lead_stage = 'QUOATTION SEND';
			} 
			else if ($row->lead_stage == 1) {
				$lead_stage = 'ON HOLD';
			} else if ($row->lead_stage == 2) {
				$lead_stage = 'DEAD';
			} else if ($row->lead_stage == 3) {
				$lead_stage = 'PI SENT';
			} else if ($row->lead_stage == 4) {
				$lead_stage = 'HOT LEAD';
			} else if ($row->lead_stage == 5) {
				$lead_stage = 'ORDER LOST';
			} else {
				$lead_stage = 'ORDER CLOSED WON';
			}

			if ($row->include_pdf == 1) {
				$include_pdf = 'YES';
			}

			if ($row->include_quotation == 2) {
				$include_quotation = 'YES';
			}

			if ($row->include_pi == 3) {
				$include_pi = 'YES';
			}

			if($row->status == 1) {
				$status = 'ACTIVE';
			} else if($row->status == 2) {
				$status = 'INACTIVE';
			} else {
				$status = '';
			}
			
			$data[] = array('sr_no'=>$i,
			'lead_stage'=>$lead_stage,
			'days'=>$row->days,
			'include_pdf'=>$include_pdf,
			'include_quotation'=>$include_quotation,
			'include_pi'=>$include_pi,
			'edit' => $edit,
			'status' => $status
			);
			 $i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			echo json_encode($results);
	}

	public function edit_communication() {
		$query = $this->db->select('*')
						  ->from('testronix_god_mode')
						  ->where('id', $this->uri->segment(3))
						  ->get();
		$data['getEditCom'] = $query->result();

		  $sql = $this->db->select('*')
						  ->from('designation_wise_communication')
						  ->where('god_mode_id', $this->uri->segment(3))
						  ->get();
		$data['getEditDesignation'] = $sql->result();
		$this->load->view('Quotation/edit_communication', $data);
	}

	public function update_communication() {
		$uri = $this->uri->segment(3);
		$designation = $this->input->post('designation');
		$designation_id = $this->input->post('designation_id');
		$email_body = $this->input->post('email_body');
		$designation1 = $this->input->post('designation1');
		$email_body1 = $this->input->post('email_body1');
		
		$data = array(
					'lead_stage' => $this->input->post('lead_stage'),
					'days' => $this->input->post('days'),
					'include_pdf' => $this->input->post('include_pdf'),
					'include_quotation' => $this->input->post('include_quotation'),
					'include_pi' => $this->input->post('include_pi'),
					'status' => $this->input->post('status')
					);

		$this->db->where('id', $uri)
				 ->update('testronix_god_mode', $data);


		for ($i=0; $i < count($designation) ; $i++) { 
		$datas = array(
					'email_body' => $email_body[$i],
					'designation' => $designation[$i]
					);

		$this->db->where('id', $designation_id[$i])
				 ->update('designation_wise_communication', $datas);
		}

		if(count($email_body1) > 0) {
		for ($j=0; $j < count($email_body1) ; $j++) { 
			if($email_body1[$j] != '' && $designation1[$j] != '') { 
		$datass = array(
					'god_mode_id' => $uri,
					'email_body' => $email_body1[$j],
					'designation' => $designation1[$j]
					);

		$this->db->insert('designation_wise_communication', $datass);
			}
		}
	}


		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('message','<div class="alert alert-success">Record Updated Successfully.</div><br/>');
			redirect(page_url.'Quotation/communication_master_list');
		}
	}
	}
	