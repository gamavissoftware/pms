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
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
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
		
		if($this->input->post('letter_head')){
        $letterhead = $this->input->post('letter_head');
        }else{
        $letterhead = '';	
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
        $productspecification = $this->input->post('productspecification');
        $productquantity = $this->input->post('productquantity');
        $terms = $this->input->post('terms');
       
 

	        $data = array(
			'letter_head'=>$letterhead,
			'customer_name'=>$customer_name,
			'quotation_date'=>$quotation_date,
			'reference_number'=>$reference_number,
			'subject'=>$subject,
			'greetingtitle'=>$greetingtitle,
			'standardtext'=>$standardtext,
			'address'=>$address,
			'convert_to_performa'=>$convert_to_performa,
			'status'=>1,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);
		//echo $result;exit;	
		if($result)
		{   
           
            for($i=0 ;$i<count($product);$i++){
		    $data = array(
			'quotation_id'=>$result,
			'product_id'=>$product[$i],
			'product_specification_id'=>$productspecification[$i],
			'product_quantity'=>$productquantity[$i]);
            $this->db->insert('quotation_product_details',$data);
	    	}
            foreach($terms as $element){
            $data = array(
			'quotation_id'=>$result,
			'terms_id'=>$element);
            $this->db->insert('quotation_terms',$data);	
            }
          
	      
	       
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Quotation/list_quotations/');
			
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
		$this->db->select(array('q.*','c.customer_name as customername','s.title as title'));
		$this->db->from('quotation_request as q');
		$this->db->join('presto_customers as c','c.customer_id = q.customer_name','left');
		$this->db->join('standard_subject as s','s.id = q.subject','left');
		$query = $this->db->get();
		$res = $query->result_array();
		$i=1;
		foreach($res as $row)
		{
			
			$edit = "<a href='".page_url."Quotation/edit_quotation_detail/".$row['id']."'><i class='fa fa-pencil'></i></a> | <a href='".page_url."Quotation/view_quotation_detail/".$row['id']."'><i class='fa fa-eye'></i></a>";
			
			
           

			$prestogroup_quotations[] = array('sr_no'=>$i,
			'quotation_date'=>$row['quotation_date'],
			'quotation_subject'=>$row['title'],
			'customer_name'=>$row['customername'],
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

		public function view_quotation_detail()
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
	$this->db->select(array('q.*','p.product_name','s.product_specifications'));
	$this->db->from('quotation_product_details as q');
	$this->db->join('presto_product_specifications as p','p.product_id = q.product_id','left');
	$this->db->join('quotation_product_specifications as s','s.id = q.product_specification_id','left');
	$this->db->where('q.quotation_id',$res['id']);
	$query = $this->db->get();
	$productquotations = $query->result_array();
	$data['productquotations'] = $productquotations;
	//echo "<pre>";print_r($data['productquotations']);exit;

	$this->db->select(array('*'));
	$this->db->from('quotation_terms as q');
	$this->db->join('terms_and_conditions_master as p','p.id = q.terms_id','left');
	$this->db->where('quotation_id',$res['id']);
	$query = $this->db->get();
	$termsquotations = $query->result_array();
	$data['termsquotations'] = $termsquotations;
	//echo "<pre>";print_r($data);exit;
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/compititor/product_img/' . $product_image);
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
		$this->db->join('quotation_product_specifications b','b.product_id=a.product_id','left');
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
	//	echo "<pre>"; print_r($prestogroup_products); exit;
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
		$this->form_validation->set_rules('spacification', 'spacification', 'required|trim');
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
			move_uploaded_file($_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/image_bank/compititor/product_img/' . $product_image);
		}else
		{
			$product_image=$this->input->post('old_img');
			}	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "presto_product_specifications";
			$data = array(
			'product_name'=>$this->input->post('product_name'),
			'specifications'=>$this->input->post('spacification'),
			'status'=>$this->input->post('status'),
			'product_image'=>$product_image,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('product_id',$this->uri->segment(3));	
		$result  = $this->db->update($table,$data);	
		if($result)
		{
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
		
	
	
	
	}
	