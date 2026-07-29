<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Open_leads extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
	}

	public function index()
	{
		
		echo "Invalid URL.";exit;
	}

	public function open_lead() {
		$this->load->view('leads/open_leads_type');
		//$this->load->view('leads/add_open_leads');
	}

	function lead_form()
	{
		$this->load->view('leads/add_open_leads');
	}

	function visit_form()
	{
		$this->load->view('leads/visit_form');
	}

	function add_open_leads() {
		
		$user_id = $this->uri->segment(3);
		$user_name=$this->getusername($user_id);
		date_default_timezone_set("Asia/Kolkata");
		$visit=$this->input->post('visit');
		if($visit=='')
		{
			$visit=0;
		}else
		{
			$visit=$visit;
		}
		$date =  date('Y-m-d H:i:s'); 
		$cust_name = $this->input->post('cust_name');
		$cust_gstn = $this->input->post('company_gstn');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile_no');
		$message1 = $this->input->post('message1');
		$country_code = $this->input->post('country_code');
		$lead_source = $this->input->post('lead_source');
		$email_id = $this->input->post('email_id');

		$query = $this->db->select('id, unique_no')->from('leads')->order_by('id','desc')->limit(1)->get();
		if($query->num_rows() > 0) {
		foreach($query->result() as $last_id);
		$lastid = $last_id->id;
		$uniqueno = $last_id->unique_no;
		} else {
		$lastid = '';
		$uniqueno = 0;
		}

		if($lead_source == 3) {
			$referral_name = $this->input->post('referral_name');
		} else {
			$referral_name = '';
		}

			
			$sql = $this->db->select('keyword')
							->from('lead_source')
							->where('source_id', $this->input->post('lead_source'))
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row);	
					$unique_no = str_pad($uniqueno+1, 3, '0', STR_PAD_LEFT);
					$uniqueid = $row->keyword.$unique_no;
			} else {
					$uniqueid = '';
					$unique_no = '001';
			}
//$uniqueid = "MM".$lastid.$countryname;
				$state=$this->input->post('state');
				$city=$this->input->post('city');
				$clientlocation=0;

			$audiodata=$this->input->post('audiofile');
			
            $data = array(
			'title' => $this->input->post('title'),
			'company_name' => $this->input->post('company_name'),
			'customer_gstn' => $this->input->post('company_gstn'),
			'email_id' => $email_id,
			'postal_address' => $this->input->post('postal_address'),
			'create_date' => date('Y-m-d',strtotime($this->input->post('create_date'))),
			'lead_source_id' => $lead_source,
			'referral_name' => $referral_name,
			'patient_type_id' => $this->input->post('patient_type'),
			'other_business' => $this->input->post('other_business'),
			'customer_name'=>$cust_name,
			// 'website'=>$this->input->post('website'),
			'email'=>$email,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'alt_contact_no'=>$this->input->post('alt_contact_no'),
			'country_code'=>91,
			'city'=>$city,
			'country'=>101,
			'remarks'=>$this->input->post('spacification'),
			'message'=>$message1,
			// 'status'=>$this->input->post('status'),
			'unique_id'=>$uniqueid,
			'unique_no' => $unique_no,
			'added_on'=>$date,
			'client_location'=>$clientlocation,
			'hpcl_company'=>$this->input->post('company_location'),
			'added_by'=>$user_id,
			'audio'=>$audiodata,
			'visit_id'=>$visit
				);

			// echo "<pre>";print_r($data);exit;
			
			$this->db->insert('leads',$data);
			$last_lead_id = $this->db->insert_id();

			$products = $this->input->post('products');

			$competitor_product = $this->input->post('competitor_product');
			$qty = $this->input->post('qty');
			$pack_size = $this->input->post('pack_size');
			 
			for($k=0; $k < count($products); $k++) {
				
			 	if($products[$k] != '') {
					$data_prod = array(
									  'lead_id' => $last_lead_id,
									  'product_id' => $products[$k],
									  'qty'=>$qty[$k],
									  'packsize'=>$pack_size[$k],
									  'competitor_product' => $competitor_product[$k]
									  );

					$this->db->insert('lead_products',$data_prod);


					/** UPDATE THE OUR PRODUCT SPEC AND MSDS FILE **/
					if($_FILES['o_spec_file']['name'][$k]<>'')
					{

						$o_spec=$_FILES['o_spec_file']['name'][$k];
						$exty=explode('.',$o_spec);
						$ex=end($exty);
						$spec_name=time().$k.'12113.'.$ex;
						move_uploaded_file($_FILES["o_spec_file"]["tmp_name"][$k],SITE_ROOT.'/image_bank/instrumentimg/'.$spec_name);
						$attadata=array('type'=>1,'product_id'=>$products[$k],'file_type'=>1,'spec_file'=>$spec_name,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
						$this->db->insert('product_competitor_files',$attadata);
					}


					if($_FILES['o_msds_file']['name'][$k]<>'')
					{

						$o_spec=$_FILES['o_msds_file']['name'][$k];
						$exty=explode('.',$o_spec);
						$ex=end($exty);
						$spec_name=time().$k.'1211311.'.$ex;
						move_uploaded_file($_FILES["o_msds_file"]["tmp_name"][$k],SITE_ROOT.'/image_bank/instrumentimg/'.$spec_name);
						$attadata=array('type'=>1,'product_id'=>$products[$k],'file_type'=>2,'spec_file'=>$spec_name,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
						$this->db->insert('product_competitor_files',$attadata);
					}


					/** END **/
				}


				/** ADD MASTER DATA FOR EQUIVALENT PRODUCT **/
				$our_prd_name=$this->get_our_product_name($products[$k]);
				if($our_prd_name<>'' && $competitor_product[$k]<>'')
				{
				$equivalent=$this->checkfor_duplicate_equivalent($our_prd_name,$competitor_product[$k]);

					if($equivalent==0)
						{

							$eqdata=array('client_product'=>$competitor_product[$k],'our_product'=>$our_prd_name,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
							$this->db->insert('equivalent_chart',$eqdata);

						}	

				}
				/** END **/


				/** UPDATE COMPETITOR FILES **/

				if($_FILES['c_spec_file']['name'][$k]<>'')
					{

						$o_spec=$_FILES['c_spec_file']['name'][$k];
						$exty=explode('.',$o_spec);
						$ex=end($exty);
						$spec_name=time().$k.'121131.'.$ex;
						move_uploaded_file($_FILES["c_spec_file"]["tmp_name"][$k],SITE_ROOT.'/competitor_files/'.$spec_name);
						$attadata=array('type'=>2,'competitor_name'=>$competitor_product[$k],'file_type'=>1,'spec_file'=>$spec_name,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
						$this->db->insert('product_competitor_files',$attadata);
					}


					if($_FILES['c_msds_file']['name'][$k]<>'')
					{
						$o_spec=$_FILES['c_msds_file']['name'][$k];
						$exty=explode('.',$o_spec);
						$ex=end($exty);
						$spec_name=time().$k.'12113112.'.$ex;
						move_uploaded_file($_FILES["c_msds_file"]["tmp_name"][$k],SITE_ROOT.'/competitor_files/'.$spec_name);
						$attadata=array('type'=>2,'competitor_name'=>$competitor_product[$k],'file_type'=>2,'spec_file'=>$spec_name,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
						$this->db->insert('product_competitor_files',$attadata);
					}


			}


			if(count($_FILES['attach']['name'])>0)
			{
				for($t=0;$t<count($_FILES['attach']['name']);$t++)
				{
					if($_FILES['attach']['name'][$t]<>'')
					{

						$file=$_FILES['attach']['name'][$t];
						$exty=explode('.',$file);
						$ex=end($exty);
						$newname=time().$t.'.'.$ex;
						move_uploaded_file($_FILES["attach"]["tmp_name"][$t],SITE_ROOT.'/image_bank/lead_based_attachment/'.$newname);

						$attadata=array('lead_id'=>$last_lead_id,'attachment'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
						$this->db->insert('lead_based_attachment',$attadata);


					}

				}

			}
				
			$initalstep=$this->getintialstep();
			$datap = array(
							'lead_id' => $last_lead_id,
							'lead_status' => $initalstep,
							'next_follow_date' => '0000-00-00'
							);

			$this->db->insert('progress_remarks',$datap);

		 	 $datat = array('lead_id' => $last_lead_id,
							'team_id' => 0,
							'member_id' => $user_id,
							'added_by' => $user_id,
							'added_on' => date('Y-m-d H:i:s')
							);
			$this->db->insert('lead_assigned_to_team_member',$datat);

			/** SEND NOTIFICATION **/
			if($audiodata<>'')
			{
				$send_audio=$this->input->post('send_audio');
				if($send_audio<>'')
				{
					$contact_number=$this->getuser_contact($send_audio);
					if(count($contact_number)>0)
					{
						$contact=$contact_number[0];
						$emp_name=$contact_number[1];

						$audiofilelink=site_http_root.'audio_files/'.$audiodata;

						$this->sendwhatsapp_for_audio($audiofilelink,$this->input->post('company_name'),$cust_name,$message1,$contact,$emp_name,$user_name);


					}



				}

			}

			if($visit!=0)
			{
				$dd=array('converted'=>1);
				$this->db->where('id',$visit);
				$this->db->update('daily_visits',$dd);

			}
			/** END **/

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Open_leads/open_lead/'.base64_encode($user_id));
	}

		function getintialstep()
	{
		$initiallead=0;
		$row=$this->db->select('lead_id')->from('lead_stage')->order_by('sort_order','ASC')->limit(1)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rowss);

			$initiallead=$rowss->lead_id;

		}


		return $initiallead;
	}

	function get_products() {
		$html = '';
		$company_location = $this->input->post('company_location');

		$query = $this->db->select('id, instruments_name')
		 				  ->from('presto_instruments')
						  ->where('company_id', $company_location)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name."</option>";
				}
			} 
				
				echo $html;
	}

	public function cheque_collection() {
		$this->load->view('customer/cheque_collection');
	}

	public function getTotalCustPriceTillDate() {
		$customer_id = $this->input->post('customer_id');
		$order_id = $this->input->post('order_id');

		$sql = $this->db->select('b.id')
		                 ->from('order_punch a')
		                 ->join('customer_quotation b', 'b.id=a.quotation_id')
		                 ->join('customer_detail c', 'c.id=b.customer_id')
		                 ->join('store_rack_location d', 'd.id=b.company_id')
		                 ->where('a.id', $order_id)
		                 ->get();

		if($sql->num_rows() == 0) {
		    $sql = $this->db->select('b.id')
		                     ->from('order_punch a')
		                     ->join('customer_quotation b', 'b.id=a.quotation_id')
		                     ->join('leads c', 'c.id=b.lead_id')
		                     ->where('a.id', $order_id)
		                     ->get();
		}

		if($sql->num_rows() > 0) {
			// echo "<pre>";print_r($sql->result());exit;
			foreach ($sql->result() as $row) {

				$sql1 = $this->db->select('agreed_price')
					  			 ->from('customer_quotation_detail')
								 ->where('quotation_id', $row->id)
								 ->get();

				
				if($sql1->num_rows() > 0) {
					// echo "<pre>";print_r($sql1->result());exit;
					$total_price = array();
					$total_price[] = 0;
					foreach ($sql1->result() as $row1) {
						$total_price[] = $row1->agreed_price;
					}
				}
				echo array_sum($total_price);
			}
		}

	}

	function getlead_based_customer($lead_id)
	{
		$data=array();
		$reste=$this->db->select('customer_name,company_name')->from('leads')->where('id',$lead_id)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row);
			
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
		}

		return $data;

	}

	function getquotation_based_customer($company_id)
	{
		$data=array();
		$restey=$this->db->select('customer_name,company_name')->form('customer_detail')->where('id',$company_id)->get();
		if($restey->num_rows()>0)
		{
		foreach($restey->result() as $restey1);
		$data[]=$row->customer_name;
		$data[]=$row->company_name;
		}
	return $data;

	}

	function save_cheque_collection() {
		$cust = explode('-', $this->input->post('customer'));
		$customer = $cust[0];
		$order_id = $cust[1];

		$data = array(
			'create_date' => date('Y-m-d',strtotime($this->input->post('current_date'))),
			'order_id' => $order_id,
			'company' => $this->input->post('company'),
			'customer' => $customer,
			'total_amount' => $this->input->post('total_amount'),
			'bank_name' => $this->input->post('bank_name'),
			'total_collection' => $this->input->post('total_collection')
			);

		// echo "<pre>";print_r($data);exit;

		$this->db->insert('customer_cheque_collection', $data);
		$last_id = $this->db->insert_id();

		$cheque_amt = $this->input->post('cheque_amt');
		$cheque_date = $this->input->post('cheque_date');
		$cheque_no = $this->input->post('cheque_no');
		$cheque_bank_name = $this->input->post('cheque_bank_name');
			 
			 
			for($i=0; $i < count($cheque_amt); $i++) {
			 	if($cheque_amt[$i] != '') {
					$data1 = array(
								  'collection_id' => $last_id,
								  'cheque_amt' => $cheque_amt[$i],
								  'cheque_date' => date('Y-m-d',strtotime($cheque_date[$i])),
								  'cheque_no' => $cheque_no[$i],
								  'cheque_bank_name' => $cheque_bank_name[$i]
								  );

					$this->db->insert('customer_cheque_collection_details',$data1);
				}
			}

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Record successfully added.</div>');
			redirect(page_url.'Open_leads/cheque_collection');
	}

	function getrecommendations()
	{
		if(isset($_GET['searchTerm']))
		{
		$searchtrm= $_GET['searchTerm'];
		}else
		{
			$searchtrm='';
		}

	    $this->db->select('id,client_product')->from('equivalent_chart')->like('client_product',$searchtrm,'both')->order_by('client_product','ASC')->group_by('client_product')->limit(10);
		$query =$this->db->get();


		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments){

		$json[] = array('id'=>$instruments->client_product, 'text'=>$instruments->client_product);

		}
		}else{

		$json[] = array('id'=>"", 'text'=>"No Data Available");

		}

		echo json_encode($json);
	}

	function getrecommendations_our_product()
	{
		$ourproduct=array();
		$comproduct=$this->input->post('comproduct');
		$restey=$this->db->select('our_product')->from('equivalent_chart')->where('client_product',$comproduct,'both')->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
				$ourproduct[]=$row->our_product;
			}
		}

		if(count($ourproduct)>0)
		{
			$data="Recommended - ". implode(', ',$ourproduct);
		}else
		{
			$data='No Recommendations Found';
		}

		echo $data;
	} 

	function getuser_contact($user_id)
	{
		$contact=array();
		$row=$this->db->select('first_name,last_name,contact_number')->from('system_users')->where('user_id',$user_id)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows);
			$contact[]=$rows->contact_number;
			$contact[]=$rows->first_name.' '.$rows->last_name;

		}

		return $contact;

	}

	function sendwhatsapp_for_audio($audio,$company,$customer,$remarks,$contact,$empname,$user_name)
	{
		//echo $audio; exit;
		$smsmessage="Hello ".$empname.",\n\n";
		$smsmessage.=$user_name." has send you an audio recording for following lead.\n\n";
		$smsmessage.="---------------\n";
		$smsmessage.="*Company Name*-".$company."\n";
		$smsmessage.="*Customer Name*-".$customer."\n";
		$smsmessage.="*Special Remarks*-".$remarks."\n";
		$smsmessage.="---------------";


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '91'.$contact,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'message'=>strip_tags(ucwords(strtolower($smsmessage))));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			//echo $result; exit;
			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

			// AUDIO 
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			$post = array(
			'receiverMobileNo' => '91'.$contact,
			'username' => whatsappuser,
			'password' => whatsapppass,
			'filePathUrl' => $audio,
			'message'=>'');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);

			if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);

	}

	function getusername($username)
	{

		$contact='';
		$row=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$username)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows);			
			$contact=$rows->first_name.' '.$rows->last_name;

		}

		return $contact;

	}

	function preview_pdf_quote()
	{
		// echo 'hieee';exit;
		$this->load->view('leads/pdf_preview');
	}

	function getCompanyProducts() {
		$html = '';
		// $company_location = $this->input->post('company_location');

		// $query = $this->db->select('b.id, b.instruments_name, b.pack_size')
		//  				  ->from('company_products a')
		//  				  ->join('presto_instruments b', 'b.id=a.product_id')
		// 				  ->where('a.company_id', $company_location)
		// 				  ->get();

		// 	if($query->num_rows()>0) {
		// 		$html .= "<option value=''>SELECT PRODUCT</option>";
		// 		foreach($query->result() as $row) {
		// 			$html .= "<option value='".$row->id."'>".$row->instruments_name.'-'.$row->pack_size."</option>";
		// 		}
		// 	} 


		$query = $this->db->select('b.id, b.instruments_name, b.pack_size')
		 				
		 				  ->from('presto_instruments b')
		 				  // ->join('company_products c', 'b.id=c.product_id')
						  ->where('b.status',1)
						  // ->where('c.company_id !=',4)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name.'-'.$row->pack_size."</option>";
				}
			} 
				
		echo $html;
	}


	function add_visits()
	{
	    $user_id = $this->uri->segment(3);
	   
	    $user_name=$this->getusername($user_id);
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$cust_name = $this->input->post('cust_name');
		$cust_gstn = $this->input->post('company_gstn');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile_no');
		$country_code = $this->input->post('country_code');
		$lead_source = $this->input->post('lead_source');
		$email_id = $this->input->post('email_id');
		$state=$this->input->post('state');
		$city=$this->input->post('city');
		$followup_date=$this->input->post('followup_date');
		if($followup_date=='')
		{
			$followup_date="0000-00-00";
		}else
		{
			$followup_date=date('Y-m-d',strtotime($followup_date));
		}

				$data = array(
			'title' => $this->input->post('title'),
			'company_name'=>$this->input->post('company_name'),
			'customer_gstn'=>$cust_gstn,
			'email_id' => $this->input->post('email'),
			'postal_address' => $this->input->post('postal_address'),
			'create_date' => date('Y-m-d',strtotime($this->input->post('create_date'))),
			'patient_type_id' => $this->input->post('patient_type'),
			'customer_name'=>$cust_name,
			'email'=>$email,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'alt_contact_no'=>$this->input->post('alt_contact_no'),
			'country_code'=>91,
			'city'=>$city,
			'country'=>101,
			'remarks'=>$this->input->post('spacification'),
			'added_on'=>$date,
			'added_by'=>$user_id,
			'followup_date'=>$followup_date
				);


			
			$this->db->insert('daily_visits',$data);
			$last_lead_id = $this->db->insert_id();


			if(count($_FILES['attach']['name'])>0)
			{
				for($t=0;$t<count($_FILES['attach']['name']);$t++)
				{
					if($_FILES['attach']['name'][$t]<>'')
					{

						$file=$_FILES['attach']['name'][$t];
						$exty=explode('.',$file);
						$ex=end($exty);
						$newname=time().$t.'.'.$ex;
						move_uploaded_file($_FILES["attach"]["tmp_name"][$t],SITE_ROOT.'/image_bank/lead_based_attachment/'.$newname);

						$attadata=array('lead_id'=>$last_lead_id,'attachment'=>$newname,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id);
						$this->db->insert('visit_based_attachment',$attadata);


					}

				}

			}

				$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
				redirect(page_url.'Open_leads/visit_form/'.base64_encode($user_id));
	    
	}


	function get_visit_details()
	{
		$id=$this->input->post('id');

		$row=$this->db->select('customer_gstn,title,customer_name,email,country_code,contact_no,city,company_name,postal_address,alt_contact,alt_contact_no,email_id,patient_type_id')->from('daily_visits')->where('id',$id)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows);

			$title=$rows->title;
			$customer_name=$rows->customer_name;
			$email=$rows->email;
			$country_code=$rows->country_code;
			$contact_no=$rows->contact_no;
			$city=$rows->city;
			$company_name=$rows->company_name;
			$postal_address=$rows->postal_address;
			$alt_contact=$rows->alt_contact;
			$alt_contact_no=$rows->alt_contact_no;
			$email_id=$rows->email_id;
			$patient_type_id=$rows->patient_type_id;
			$customer_gstn=$rows->customer_gstn;

		}else
		{
			$title='';
			$customer_name='';
			$email='';
			$country_code='';
			$contact_no='';
			$city='';
			$company_name='';
			$postal_address='';
			$alt_contact='';
			$alt_contact_no='';
			$email_id='';
			$patient_type_id=0;
			$patient_type_id='';
		}


		echo $title."|".$customer_name."|".$email."|".$country_code."|".$contact_no."|".$city."|".$company_name."|".$postal_address."|".$alt_contact."|".$alt_contact_no."|".$email_id."|".$patient_type_id.'|'.$customer_gstn;
	}


	function getunit() {
		$unit = '';
		$product = $this->input->post('proid');

		$sql = $this->db->select('b.shortname, b.id')
						->from('presto_instruments a')
						->join('units b', 'b.shortname=a.unit', 'left')
						->where('a.id', $product)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
				$unit = '<option value="'.$row->id.'">'.$row->shortname.'</option>';
		}else
		{
			$unit = '<option value="0">NA</option>';
		}

		echo $unit;
	}

	function get_our_product_name($ourprd)
	{
		$sql=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$ourprd)->get();
		if($sql->num_rows()>0)
		{
			foreach($sql->result() as $sql1);

			return $sql1->instruments_name;
		}else
		{
			return null;
		}

	}

	function checkfor_duplicate_equivalent($our_prd_name,$competitor_product)
	{
		$rest=$this->db->select('id')->from('equivalent_chart')->where('client_product',$competitor_product)->where('our_product',$our_prd_name)->get();

		return $rest->num_rows();

	}


	public function payment_form() {
		$this->load->view('customer/payment_form');
	}

	public function getCustomers() {
			$q = $_GET['q'];
			$company=$_GET['company'];

			$query = $this->db->select('d.id,d.company_name')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('order_punch_tax_details c', 'c.order_id=a.id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->where('b.order_punch', 1)
						->where('a.payment', 0)
						->where('a.billing', 1)
						->where('a.hpcl_billing_company',$company)
						->like('d.company_name', $q, 'both')
						->group_by('d.id')
						->get();

			// $query = $this->db->select('id, company_name')
			// 				  ->from('customer_detail')
			// 				  ->like('company_name', $q, 'both')
			// 				  ->where('company_id',$company)
			// 				  ->get();
						

			if($query->num_rows()>0) {
				foreach($query->result() as $row) {
					$json[] = array('id'=>$row->id, 'text'=>$row->company_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
			echo json_encode($json);
	}

	function getCustomerOrders() {
		$user_id = $this->uri->segment(3);
		$customer_id = $this->uri->segment(4);
		$company_id = $this->uri->segment(5);
		redirect(page_url.'Open_leads/payment_form/'.base64_encode($user_id).'/'.$customer_id.'/'.$company_id);
	}

	function customer_order_list() {
		$lead_data = array();
		 $billing_company = $this->uri->segment(5);
		$customer_id = $this->uri->segment(4);

		$sql = $this->db->select('a.added_on,a.id, a.invoice_no, b.id as quotation_id, c.gst_no as seller_gst, d.gst as buyer_gst,d.tds_appl,tds_per,a.hpcl_billing_company,e.companyname')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('order_punch_tax_details c', 'c.order_id=a.id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('store_rack_location e','e.id=a.hpcl_billing_company')
						->where('b.order_punch', 1)
						->where('a.payment', 0)
						->where('a.billing', 1)
						->where('a.cancelled', 0)
						->where('b.customer_id', $customer_id)
						->where('a.hpcl_billing_company', $billing_company)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach ($sql->result() as $row) {
					$previous_recieved=$this->customer_previous_payment($row->id);
					$order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);


					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					if($row->tds_appl==1)
					{
					$tds_fac=$row->tds_per/100;
					$getorderamountaftertds=$basic_order_amount*$tds_fac;
					//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
					$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
					$order_amount=round($getorderamountaftertds+$gst_amount);
					}else
					{
					$order_amount=round($order_amount);
					}


					$rem_amt = $order_amount - $previous_recieved;

					// if($i==2) {
					// 	echo $previous_recieved;exit;
					// }
					if($order_amount > $rem_amt) {
						$status = '<a href="javascript:;" class="btn btn-success btn-xs">PARTIAL PAYMENT</a>';
					} else {
						$status = '<a href="javascript:;" class="btn btn-warning btn-xs">UNPAID</a>';
					}
					$select_a="<input type='checkbox' class='selectbill' name='bills[]' id='bills".$row->id."' value='".$row->id."'>";
					$lead_data[] = array(
					'sr_no' => $i."<br/>".$select_a,
					'billing_company' => $row->companyname,
					'invoice_no' => $row->invoice_no,
					'invoice_date'=>date('d-M-Y',strtotime($row->added_on)),
					'order_amount' => $order_amount,
					'amount_recd' => $previous_recieved,
					'rem_amt' => $rem_amt,
					'status' => $status
					// 'payment_collection' => $payment_collection
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

	function save_customer_payments() {

		// exit;
		$total_payment=array();
		$total_payment[]=0;
		$user_id = $this->uri->segment(3);
		$customer_id = $this->uri->segment(4);
		$company_id = $this->uri->segment(5);
		$recd_payment = $this->input->post('total_amount');
		$payment_date = $this->input->post('payment_date');
		$payment_type = $this->input->post('payment_type');
		$cheque_no = $this->input->post('cheque_no');
		$cheque_date = $this->input->post('cheque_date');
		$neft_trans_no = $this->input->post('transaction_no');
		$adjust = $this->input->post('adjust');
		$tds_appl = $this->input->post('tds_appl');
		$tds_per = $this->input->post('tds_per');
		
			if($adjust==1)
		{
			$bi='';
		}else
		{
			$bills=$this->input->post('bills');
			$bi = "'" . implode ( "', '", $bills ) . "'";
		}

		$pay_data=array('customer_id'=>$customer_id,'hpcl_billing_company'=>$company_id,'type'=>$adjust,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$user_id,'bills'=>$bi,'bank'=>$this->input->post('bank'));
		$this->db->insert('customer_payments',$pay_data);
		$lid=$this->db->insert_id();
		for($i=0; $i<count($payment_type);$i++) {
			$total_payment[]=$recd_payment[$i];
				$data = array(
							  'payment_id' => $lid,
							  'payment_date'=>date('Y-m-d',strtotime($payment_date[$i])),
							  'payment_type' => $payment_type[$i],
							  'cheque_no' => $cheque_no[$i],
							  'cheque_date' => date('Y-m-d', strtotime($cheque_date[$i])),
							  'neft_trans_no' => $neft_trans_no[$i],
							  'amount' => $recd_payment[$i]
							 );

				$this->db->insert('customer_payment_particulars', $data);

				
			
			}


		/** NOW ADJUST THE PAYMENBT **/

		$recd_payment=array_sum($total_payment);

		//echo $recd_payment; exit;


		if($adjust==1)
		{

		$sql = $this->db->select('a.id, a.invoice_no, b.id as quotation_id, c.gst_no as seller_gst, d.gst as buyer_gst')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('order_punch_tax_details c', 'c.order_id=a.id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->where('b.order_punch', 1)
						->where('a.payment', 0)
						->where('a.billing', 1)
						->where('b.customer_id', $customer_id)
						->order_by('a.id')
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				
				foreach ($sql->result() as $row) {
					$order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);


					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					//echo $order_amount."<br/>".$basic_order_amount."<br/>".$gst_amount; exit;

					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$order_amount=round($getorderamountaftertds+$gst_amount);

					}else
					{
						$order_amount=round($order_amount);
					}

					//echo $order_amount; exit;

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

						$sql2 = $this->db->select('a.id, b.payment_type, b.cheque_date,b.cheque_no')
										->from('order_punch a')
										->join('customer_payment_particulars b', 'b.payment_id=a.payment_id')
										->where('a.payment_id', $lid)
										->get();

						if($sql2->num_rows() > 0) {
							foreach($sql2->result() as $row2) {

								if($row2->payment_type == 1) {
									$data2 = array(
										   'expected_pdc_date' => $row2->cheque_date,
										   'cheque_no' => $row2->cheque_no,
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



			/** END FIFO **/
		}else
		{
			/** AGAINST BILLS **/
			$bills=$this->input->post('bills');
			$all_bills = "'" . implode ( "', '", $bills ) . "'";
		
			$sql = $this->db->select('a.id, a.invoice_no, b.id as quotation_id, c.gst_no as seller_gst, d.gst as buyer_gst')
						->from('order_punch a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('order_punch_tax_details c', 'c.order_id=a.id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->where('b.order_punch', 1)
						->where('a.payment', 0)
						->where('a.billing', 1)
						// ->where('b.customer_id', $customer_id)
						->where_in('a.id',$all_bills,false)
						->order_by('a.id')
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				
				foreach ($sql->result() as $row) {
					$order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);


					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					//echo $order_amount."<br/>".$basic_order_amount."<br/>".$gst_amount; exit;

					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$order_amount=round($getorderamountaftertds+$gst_amount);

					}else
					{
						$order_amount=round($order_amount);
					}

					//echo $order_amount; exit;

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

						$sql2 = $this->db->select('a.id, b.payment_type, b.cheque_date,b.cheque_no')
										->from('order_punch a')
										->join('customer_payment_particulars b', 'b.payment_id=a.payment_id')
										->where('a.payment_id', $lid)
										->get();

						if($sql2->num_rows() > 0) {
							foreach($sql2->result() as $row2) {

								if($row2->payment_type == 1) {
									$data2 = array(
										   'expected_pdc_date' => $row2->cheque_date,
										   'cheque_no' => $row2->cheque_no,
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
									   'payment' => 1,
									   'payment_id'=>$lid
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



			/** END BILLS **/


		}


			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');

			redirect(page_url.'Open_leads/payment_form/'.base64_encode($this->uri->segment(3)).'/'.$this->uri->segment(4));
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

	function get_competitor_files()
	{
		$d='';
		$e='';
		$cprd=$this->input->post('comproduct');
		$rest=$this->db->select('spec_file,msds')->from('equivalent_chart')->where('client_product',$cprd)->where('spec_file!=','')->or_where('msds!=','')->where('client_product',$cprd)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $dd);

			if($dd->spec_file<>'')
			{
				$d='<a href="'.page_url1.'competitor_files/'.$dd->spec_file.'" download style="font-size:11px;">Download Spec File</a>';
			}

			if($dd->msds<>'')
			{
				$e='<a href="'.page_url1.'competitor_files/'.$dd->msds.'" download style="font-size:11px;">Download MSDS File</a>';
			
			}

		}

		echo $d.'|'.$e;
	}

	function get_our_product_files()
	{
		$d='';
		$e='';
		$proid=$this->input->post('proid');
		$rest=$this->db->select('spec_file,msds_file')->from('presto_instruments')->where('id',$proid)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $dd);

			if($dd->spec_file<>'')
			{
				$d='<a href="'.page_url1.'image_bank/instrumentimg/'.$dd->spec_file.'" download style="font-size:11px;">Download Spec File</a>';
			}

			if($dd->msds_file<>'')
			{
				$e='<a href="'.page_url1.'image_bank/instrumentimg/'.$dd->msds_file.'" download style="font-size:11px;">Download MSDS File</a>';
			
			}
		}

		echo $d."~".$e;

	}


	function getOrderAmountWithoutGST($quotation, $buyer_gst, $seller_gst)
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

		    $final_amt = $total_price + $total_gst;

		    return $final_amt;
	}


	function getOrderGSTAmount($quotation, $buyer_gst, $seller_gst)
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

		    $final_amt = $total_gst;

		    return $final_amt;
	}

	function choose_company()
	{
		$this->load->view('customer/payment_form_type');
	}

	function add_oldcustomer_visit()
	{
		
		$user_id = $this->uri->segment(3);
		$customer_type_value = $this->input->post('customer_type_value');
		//echo $customer_type_value; exit;
		if($customer_type_value==2)
		{
		$data = array('customer_id'=>$this->input->post('customer_name'),
		'remarks'=>$this->input->post('remarks'),
		'added_on'=>date('Y-m-d h:i:s'),
		'added_by'=>$user_id);
		$this->db->insert('oldcustomer_visit',$data);
		}else if($customer_type_value==3)
		{
			$visit_customer=$this->input->post('visit_customer');

			$dty=array('visit_id'=>$visit_customer,'remarks'=>$this->input->post('remarks'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$this->uri->segment(3));
			$this->db->insert('incomplete_visit_data',$dty);


		}else
		{
			echo "No Selection Made"; exit;
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
		redirect(page_url.'Open_leads/visit_form/'.base64_encode($user_id));
	       
	}

	function getGSTData()
	{

		$tradeNam='NA';
		$gst=$this->input->post('gst');

		if($gst<>'')
		{



		$d=json_encode(array('AppSCommonSearchTPItem'=>array('GSTIN'=>$gst)));
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://www.ewaybills.com/MVEWBAuthenticate/MVAppSCommonSearchTP');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n\t\"AppSCommonSearchTPItem\": [{\n\t\t\"GSTIN\": \"$gst\"\n\t}]\n} \n");

		$headers = array();
		$headers[] = 'Mvapikey: F5uqpGiExNTNXrB';
		$headers[] = 'Mvsecretkey: bVVKQdPLj+DEDE7vv9XRXg==';
		$headers[] = 'Gstin: 06ACBPN9723G2ZK';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		$st=json_decode($result,true);
		
		if(count($st)>0)
		{
		$status=$st['Status'];
		
		if($status==1)
		{

			if(count($st['lstAppSCommonSearchTPResponse'])>0)
			{

				$tradeNam=$st['lstAppSCommonSearchTPResponse'][0]['tradeNam'];
				if($tradeNam=='')
				{
					$tradeNam=$st['lstAppSCommonSearchTPResponse'][0]['lgnm'];
				}

			}

		}
		

		}
	}


		echo $tradeNam;

	}

	function companybankaccount()
	{
		$html='<option value="ALL">ALL</option>';
		$our_company=$this->input->post('our_company');
		$selected=$this->input->post('selected');
		$rty=$this->db->select('id,bank_name,account')->from('store_rack_location_account')->where('location_id',$our_company)->get();
			if($rty->num_rows()>0)
			{
				foreach($rty->result() as $row)
				{
					if($selected<>'' && $row->id==$selected)
					{	
					$a="selected";
					}else
					{
					$a="";
					}
					
					$html.="<option value='".$row->id."' ".$a.">".$row->account."-".$row->bank_name."</option>";
				}	
			}

			echo $html;	
	}

}