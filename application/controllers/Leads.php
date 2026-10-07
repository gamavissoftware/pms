<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends CI_Controller {

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
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$this->load->model('Salescrm_model','salescrm');
		$this->load->model('Dashboard_model','dashboardmodel');


	// 	$setarray=$this->getsettings();
	// 	if(count($setarray)>0)
	// 	{

	// 	$config = array();  
	// 	$config['protocol'] = 'smtp';  
	// 	$config['smtp_host'] = $setarray['email_outgoing'];  
	// 	$config['smtp_user'] = $setarray['email_smtp'];  
	// 	$config['smtp_pass'] = $setarray['email_pass'];   
	// 	$config['smtp_port'] = $setarray['port'];  
	// 	$config['mailtype']  = 'html';
	// 	$config['charset']   = 'iso-8859-1';
	// 	$this->email->initialize($config);  

	// 	$this->email->set_newline("\r\n");  
	// 	$this->load->library('email', $config);
	// }


		$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");


// echo "<pre>";print_r($_SESSION);exit;
	if (!$this->session->userdata('logged_in'))
        { 
            $this->session->set_flashdata('message','Session Logged Out. Login to continue', 'refresh');
            redirect(page_url);
        }
		
	}

	public function index()
	{
		$this->load->view('leads/leads');
	}
	
	
	/********Add detail*********/
	public function add_leads() {
		
		$user_id=$_SESSION['logged_in']['user_id'];	    
	    $send_whatsapp = $this->input->post('send_whatsapp');
	    $appid=$this->input->post('apiid');
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$query = $this->db->select('id, unique_no')->from('leads')->order_by('id','desc')->limit(1)->get();
		if($query->num_rows() > 0) {
		foreach($query->result() as $last_id);
		$lastid = $last_id->id;
		$uniqueno = $last_id->unique_no;
		} else {
		$lastid = '';
		$uniqueno = 0;
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
					
		// echo $unique_no;exit;
		
		$table = "leads";
		$create_date = date('Y-m-d',strtotime($this->input->post('create_date')));
		$gender = $this->input->post('gender');
		$cust_name = $this->input->post('cust_name');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile_no');
		$message1 = $this->input->post('message1');
		$country_code = $this->input->post('country_code');
		$lead_source = $this->input->post('lead_source');
		$email_id = $this->input->post('email_id');

			$data = array(
				'title'=>$this->input->post('title'),
			'company_name'=>$this->input->post('company_name'),
			// 'contact_person'=>$this->input->post('contact_person'),
			'email_id'=> $email_id,
			// 'mobile_no'=>$this->input->post('mobile_no'),
			'postal_address'=>$this->input->post('postal_address'),
			'create_date'=>$create_date,
			'lead_source_id'=>$lead_source,
			'patient_type_id'=>$this->input->post('patient_type'),
			'customer_name'=>$cust_name,
			'website'=>$this->input->post('website'),
			'email'=>$email,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'alt_contact_no'=>$this->input->post('alt_contact_no'),
			'country_code'=>91,
			'city'=>$city,
			'country'=>$this->input->post('country_name'),
			'remarks'=>$this->input->post('spacification'),
			'message'=>$message1,
			// 'status'=>$this->input->post('status'),
			'unique_id'=>$uniqueid,
			'unique_no' => $unique_no,
			'added_on'=>$date,
			'client_location'=>$clientlocation,
			'hpcl_company'=>$this->input->post('company_location'),
			'added_by'=>$user_id,
			'company_id'=>$_SESSION['logged_in']['business_location']);
			
			// echo "<pre>";print_r($data);exit;
		
			$last_lead_id  = $this->master->insert_record($table,$data);
			$products = $this->input->post('products');
			$competitor_product = $this->input->post('competitor_product');
			 
			 
			for($k=0; $k < count($products); $k++) {
			 	if($products[$k] != '') {
					$data_prod = array(
									  'lead_id' => $last_lead_id,
									  'product_id' => $products[$k],
									  'competitor_product' => $competitor_product[$k]
									  );

					$this->db->insert('lead_products',$data_prod);
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
							'member_id' => $_SESSION['logged_in']['user_id'],
							'added_by' => $_SESSION['logged_in']['user_id'],
							'added_on' => date('Y-m-d H:i:s')
							);
			$this->db->insert('lead_assigned_to_team_member',$datat);
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads');
		
	}

	public function Lead_list()
	{

		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','a.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','a.lead_source_id=g.source_id','left');
		//$this->db->where('a.status','1');
		if($_SESSION['logged_in']['role']!=1)
		{
			$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		}else
		{

		}
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{

			$products = $this->salescrm->getProducts($row->id);
				$instruments = array();

				foreach($products as $row1) {
		   			$instruments[] = $row1->instruments_name;
		   		}

		   	$products1 = implode('<br>', $instruments);

			$getLeadStatus = $this->salescrm->getLeadStatus($row->id);
			if(($getLeadStatus) > 0) {
			foreach ($getLeadStatus as $status);
				$currentleadstatus = $status->lead_name;
				$currentleadremarks = $status->remarks;
				$addedOn=$status->added_on;
				$addedBy=$status->added_by;
				$addedBy=$this->salescrm->getusername($addedBy);
				if($addedOn=='0000-00-00 00:00:00')
				{
					$lastup='';
				}else
				{
					$lastup=date('d-m-Y H:i:s',strtotime($addedOn));
				}
			} else {
				$currentleadstatus = '';
				$currentleadremarks = '';
				$addedOn='';
				$addedBy='';
			}
			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";

			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			// if($_SESSION['logged_in']['role']==1) {
			// 	$edit .= " | <a href='javascript:;' onclick='deleteLead(".$row->id.")'><i class='fa fa-trash' title='Delete Lead'></i></a>";
			// }

			// $edit .= " | <a href='".page_url."Leads/getGodModeData/".$row->id."'><i class='fa fa-history' title='God Mode Logs'></i></a>";

			$view = "<a href='".page_url."Leads/view_detail/".$row->id."/".$this->uri->segment(3)."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			// if($mode==1){
			$lead_transfer = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."' class='btn btn-success btn-xs'>Lead Transfer</a>";
			// }else{
			// $lead_transfer = "<a href='".page_url."Assigned_lead/changemultiassignment/".$row->id."' class='btn btn-success btn-xs'>Lead Transfer</a>";	
			// }
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
						
			
		$query = $this->db->select(' team_id,team_name')->from('prestogroup_teams')->where('status','1')->order_by('team_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
		       
				foreach($query->result() as $teamname)
				{
				$team .="<option value='".$teamname->team_id."'>".$teamname->team_name."</option>";
				}
		
			$team .="</select>";

// if($mode==1)
// {

			// $query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
			// 				  ->from('lead_assigned_to_team a')
			// 				  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
			// 				  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
			// 				  ->join('system_users d','d.user_id=c.member_id','left')
			// 				  ->where('a.lead_id',$row->id)
			// 				  ->get();

			$query = $this->db->select('c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team_member c')
							  ->join('system_users d','d.user_id=c.member_id')
							  ->where('c.lead_id',$row->id)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				// $assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			// $assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			// $assignement = "";
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->id)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}



				

$products = $this->salescrm->getProductsTabular($row->id);


			// //$remarks = substr($row->remarks,0,50);
			// $lead_data[] = array('sr_no'=>$i,
			// 'create_date'=>date('d-m-Y', strtotime($row->create_date))."<br>".$row->unique_id,
			// 'patient_type'=>$row->patient_type,
			// 'products'=>$products,
			// 'lead_source'=>$row->lead_source,
			// 'company_name'=>$row->company_name,
			// 'customer_details'=>$row->customer_name."<br>".$row->country_code."-".$row->contact_no."<br>".$row->email_id."<br>".$row->country_name,
			// 'skypeid'=>$skype,
			// 'messangerid'=>$messanger,
			// 'state'=>$row->state,
			// 'city'=>$row->city,
			// 'lead_quality_status'=>$currentleadstatus,
			// 'lead_remarks'=>$row->remarks,
			// 'remarks'=>$currentleadremarks,
			// 'assign'=>$fname." ".$lname,
			// 'edit'=>$edit,
			// 'followup' => $view,
			// 'lead_transfer' => $lead_transfer
			// );


	$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' =>'',
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								
								'lastremarks'=>$currentleadremarks,
								'leadmanager'=>$fname." ".$lname."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$lastup."<br/><br/>".$addedBy,
								'update'=>$view,
								'current_status'=>$currentleadstatus,
								'products' => $products,
								'leadsource'=>$row->lead_source,
								'edit'=>$edit,

								
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
	

	public function update_lead_stage()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "id";
		$table = "leads";
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

	public function edit_leads(){
		$this->load->view('leads/edit_leads');
	}	
	
	public function update_lead_information()
	{
		// $setting=$this->getsettings();
		// if(count($setting)>0)
		// {
		// 	$mode=$setting['mode'];

		// }else
		// {
		// 	echo "Settings Not Found Contact the Account Manager"; exit;
		// }
		
		
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "leads";
		$last_url = $this->input->post('lasturl');
		$create_date = date('Y-m-d',strtotime($this->input->post('create_date')));
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$state = $this->input->post('state');
		$city = $this->input->post('city');
		$clientlocation = 0;

			$data = array(
			'company_name'=>$this->input->post('company_name'),
			'contact_person'=>$this->input->post('contact_person'),
			'email_id'=>$this->input->post('email_id'),
			// 'mobile_no'=>$this->input->post('mobile_no'),
			'postal_address'=>$this->input->post('postal_address'),
			'create_date'=>$create_date,
            'lead_source_id'=>$this->input->post('lead_source'),
			'patient_type_id'=>$this->input->post('patient_type'),
			'customer_name'=>$this->input->post('cust_name'),
			'website'=>$this->input->post('website'),
			'email'=>$this->input->post('email'),
            'country_code'=>$this->input->post('country_code'),
			'contact_no'=>$this->input->post('mobile_no'),
			'alt_contact'=>$this->input->post('alt_contact'),
			'alt_contact_no'=>$this->input->post('alt_contact_no'),
			'country'=>$this->input->post('country_name'),
			'state'=>$state,
			'city'=>$city,
			'client_location'=>$clientlocation,
			'hpcl_company'=>$this->input->post('company_location'),
			'message'=>$this->input->post('message1'),
			'remarks'=>$this->input->post('spacification'),
			// 'status'=>$this->input->post('status'),
			'updatedBy'=>$user_id,
			'updated_on'=>$date
			);
				
			// echo "<pre>";print_r($data);exit;
				
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update($table,$data);	

			 $competitor_product_edit = $this->input->post('competitor_product_edit');
			 $products_edit = $this->input->post('products_edit');
			 $product_id = $this->input->post('product_id');
			 
			for($j=0; $j < count($product_id); $j++) {
			 	if($product_id[$j] != '') {
						$data_prod_edit = array(
										  'product_id' => $products_edit[$j],
										  'competitor_product' => $competitor_product_edit[$j]
										  );

						$this->db->where('id', $product_id[$j])
								 ->update('lead_products',$data_prod_edit);
					}
				}

			 $competitor_product = $this->input->post('competitor_product');
			 $products = $this->input->post('products');
			 	for($k=0; $k < count($products); $k++) {
			 		if($products[$k] != '') {
						$data_prod = array(
										  'lead_id' => $this->uri->segment(3),
										  'product_id' => $products[$k],
										  'competitor_product' => $competitor_product[$k]
										  );

						$this->db->insert('lead_products',$data_prod);
				}
			}
		

			// $datas = array(
			// 				'team_id' => $this->input->post('assign_team'),
			// 				'added_on' => date('Y-m-d H:i:s')
			// 				);

		 // 					$this->db->where('lead_id', $this->uri->segment(3))
		 // 							 ->update('lead_assigned_to_team', $datas);

		 	//  $datat = array(
			// 				'team_id' => 0,
			// 				'member_id' => $user_id,
			// 				'added_on' => date('Y-m-d H:i:s')
			// 				);

			// $this->db->where('lead_id', $this->uri->segment(3))
		 	// 		 ->update('lead_assigned_to_team_member', $datat);


			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Leads');


	
	}
	
	
	public function view_detail(){
		$this->load->view('leads/view_detail');
	}

	public function view_detail_distributor(){
		$this->load->view('leads/view_detail_distributor');
	}
	
	public function update_remarks()
	{
		$uid=$this->uri->segment(3);
		$schedulevisitdate = date('Y-m-d',strtotime($this->input->post('schedulevisitdate')));
		$schedulevisittime = date('H:i:s',strtotime($this->input->post('visittime')));
		$democheduledate = date('Y-m-d',strtotime($this->input->post('democheduledate')));
		//echo $schedulevisitdate."<br>".$schedulevisittime; exit;
		$seller_gst=$this->salescrm->getSellerGst($this->input->post('hpcl_company_hidden'));
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		$upd_id=$this->input->post('editid');
		

			$pic = $_FILES['upload_file']['name'];

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = '';
			 }

		$quotation_status_id = $this->salescrm->checkQuotationSent();

		if(in_array($this->input->post('leadquality'), $quotation_status_id)) {
		
		for($j=0 ;$j<count($upd_id);$j++) {
			$productname_old = $this->input->post('productedit'.$upd_id[$j]);
            $qty_old = $this->input->post('qtyedit'.$upd_id[$j]);
            $unit_old = $this->input->post('unit_id_edit'.$upd_id[$j]);
            $listprice_old = $this->input->post('listpriceedit'.$upd_id[$j]);
	        $discount_old = $this->input->post('discountpriceedit'.$upd_id[$j]);
	        $discountpricehide_old = $this->input->post('discountpricehideedit'.$upd_id[$j]);
	        $netprice_old = $this->input->post('netpriceedit'.$upd_id[$j]);

	         if($listprice_old < $discountpricehide_old) {
       			$flag = 0;
       		} else {
       			$flag = 1;
       		}

       		$cp=$this->salescrm->getcurrentcp($productname_old);
       		$data = array(
			'product_id' => $productname_old,
			'qty' => $qty_old,
			'packsize' => $unit_old,
			'price' => $listprice_old,
			'percent_amt' => $discount_old,
			'net_price' => $netprice_old,
			'flag' => $flag,
			'added_on' => $date,
			'added_by' => $user_id,
			'msp'=>$discountpricehide_old,
			'cp'=>$cp
			);

       		$this->db->where('id',$upd_id[$j]);
            $this->db->update('lead_products',$data);
		}



		
		if($uid <> '') {

			$data1 = array(
				'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
				'check_terms'=>$this->input->post('chk_tnc'),
				'general_terms'=>$this->input->post('drums_tnc'),
				'bulk_terms'=>$this->input->post('bulk_tnc')
			);

			$this->db->where('id',$uid);
			$this->db->update('leads',$data1);

			$competitor_product = $this->input->post('competitor_product');
			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            $unit = $this->input->post('unit_id');
            $listprice = $this->input->post('listprice');
	        $discount = $this->input->post('discountprice');
	        $discountpricehide = $this->input->post('discountpricehide');
	        $netprice = $this->input->post('netprice');

       		for($i=0 ;$i<count($productname);$i++){
       			if($productname[$i] !='') {  
	       		if($listprice[$i] < $discountpricehide[$i]) {
	       			$flag = 0;
	       		} else {
	       			$flag = 1;
	       		}

	       		$cp=$this->salescrm->getcurrentcp($productname[$i]);
	 			$data2 = array(
					'lead_id' => $uid,
					'competitor_product' => $competitor_product[$i],
					'product_id' => $productname[$i],
					'qty' => $qty[$i],
					'packsize' => $unit[$i],
					'price' => $listprice[$i],
					'percent_amt' => $discount[$i],
					'net_price'=> $netprice[$i],
					'flag' => $flag,
					'msp'=>$discountpricehide[$i],
					'cp'=>$cp,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $user_id
				);

					// echo "<pre>";print_r($data2);exit;
	            	$this->db->insert('lead_products',$data2);
	       		}
	    	}
		}

	}

	if($this->input->post('leadquality')==27){
			$demoproductid=$this->input->post('demoproductid');
			for($i=0 ;$i<count($demoproductid);$i++) {
			$productid  = $this->input->post('demoproductid');
            $qty = $this->input->post('orderqty');
            $listprice = $this->input->post('itemprice');
	       	$finalprice = $this->input->post('totalamount');

	        $flag = 0;

       		$data = array(
       		'lead_id'=>$this->uri->segment(3),
			'product_id' => $productid[$i],
			'qty' => $qty[$i],
			'price' => $listprice[$i],
			'net_price' => $finalprice[$i],
			'flag' => $flag,
			'added_on' => date('Y-m-d H:i:s'),
			'added_by' => $user_id);

       		
            $this->db->insert('lead_products',$data);
		}

		}



			$followupdate = $this->input->post('followup_date');
			$followup_date_chk = $this->salescrm->checkIfFollowupDate();

			if(in_array($this->input->post('leadquality'), $followup_date_chk)) {
				if($followupdate == '') {
					$followup_date = '';
				} else {
					$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
				}
			} else {
				$followup_date = '';
			}

			

			if($this->input->post('leadquality')==18){
				$scdate = $schedulevisitdate;
				$sctime = $schedulevisitdate;
			}else{
				$scdate = "";
				$sctime  = "";
			}

			if($this->input->post('leadquality')==20){
				$demoscdate = $democheduledate;
			}else{
				$demoscdate = "";
			}

			$getConversionLeadStage=$this->dashboardmodel->getConversionLeadStage();
			if($getConversionLeadStage <> $this->input->post('leadquality'))
			{
			$data3 = array(
			'lead_id' => $this->uri->segment(3),
			'lead_stage' => $this->input->post('leadquality'),
			'next_follow_date' => $followup_date,
			'remarks' => $this->input->post('remarks'),
			'remark_title' => $this->input->post('remark_title'),
			'nonqualifiedreason' => $this->input->post('nonqualifiedreason'),
			'skip_whatsapp'=>0,
			'demoscheduledate'=>$demoscdate,
			'visitdate'=>$scdate,
			'visittime'=>$sctime,
			'added_on'=>$date,
			'added_by'=>$user_id
			);

			$this->db->insert('progress_remarks',$data3);
			}





		/** LEAD CONVERSION STARTS **/
		$proceed_flag=array();
		$proceed_flag[]=0;
		if($getConversionLeadStage == $this->input->post('leadquality')) {
		
			/** STOCK CHECK VERY IMPORTANT **/
			$productname_for_check = $this->input->post('product1');
			$qty_for_check= $this->input->post('qty1');
			$for_new_product=$this->salescrm->checkforavailable_Company_QTY($productname_for_check,$qty_for_check,$this->input->post('hpcl_company'));
			$new_data=explode('~',$for_new_product);
			$proceed_flag[]=$new_data[0];
			$prd_not_available=$new_data[1];


			$oldproductname_for_check=$this->input->post('editid1');
			$for_old_product=$this->salescrm->checkforavailable_Company_QTY_oldproduct($oldproductname_for_check,$this->input->post('hpcl_company'));
			$old_data=explode('~',$for_old_product);
			//echo "<pre>"; print_r($old_data); exit;
			$proceed_flag[]=$old_data[0];
			$prd_not_available1=$old_data[1];
			//echo $prd_not_available1; exit;
			/** END **/
			if(array_sum($proceed_flag)==0)
			{	

				$data3 = array(
			'lead_id' => $this->uri->segment(3),
			'lead_status' => $this->input->post('leadquality'),
			'next_follow_date' => $followup_date,
			'remarks' => $this->input->post('remarks'),
			'remark_title' => $this->input->post('remark_title'),
			'nonqualifiedreason' => $this->input->post('nonqualifiedreason'),
			'skip_whatsapp'=>0,
			'added_on'=>$date,
			'added_by'=>$user_id
			);

			$this->db->insert('progress_remarks',$data3);


			/** UPDATE PAYMENT TERM & CREDIT PERIOD **/
			$monthly_consumption=$this->input->post('monthly_consumption');		
			$payment_type=$this->input->post('payment_type');
			
			if($payment_type==4 || $payment_type==5)
			{
				$paymentterms=$this->input->post('paymentterms');
			}else
			{
				$paymentterms=0;
			}

			$paydata=array();
			
				 $max_customer_code=$this->salescrm->getHighestCustomerCode();

				 $retlly=$this->db->select('added_by')->from('leads')->where('id',$uid)->get();
				 if($retlly->num_rows()>0)
				 {
				 	foreach($retlly->result() as $retyl);
				 	$assigned_to=$retyl->added_by;
				 }else
				 {
				 	$assigned_to=$_SERVER['logged_in']['user_id'];
				 }
					$data4 = array(
								'company_id' => $this->input->post('hpcl_company_hidden'),
								'customer_ref_no' =>$max_customer_code,
								'gst'=>$this->input->post('gst_no'),
								'title' => $this->input->post('title_bill'),
								'customer_name' => $this->input->post('customer_name_cust'),
								'email' => $this->input->post('billing_email'),
								'contact_no' => $this->input->post('contact_no_cust'),
								'country' => 101,
								'city' => $this->input->post('billing_city'),
								'bill_city' => $this->input->post('billing_city'),
								'bill_state'=>$this->input->post('billing_state'),
								'address' => $this->input->post('billing_address'),
								'bill_address' => $this->input->post('billing_address'),
								'bill_pincode' => $this->input->post('billing_pincode'),
								'company_name' => $this->input->post('company_name_bill'),
								'payment_type'=>$payment_type,
								'credit_days'=>$paymentterms,
								'ship_city' => $this->input->post('shipping_city'),
								'ship_state'=>$this->input->post('shipping_state'),
								'ship_address' => $this->input->post('shipping_address'),
								'ship_pincode' => $this->input->post('shipping_pincode'),
								'status' => 1,
								'payment_term_approval'=>0,
								'added_on'=>date('Y-m-d H:i:s'),
								'added_by'=>$_SESSION['logged_in']['user_id'],
								'assigned_to'=>$assigned_to
								);


					$this->db->insert('customer_detail', $data4);
					$cust_id = $this->db->insert_id();
					$this->sync_customer_to_sap('marketing', $cust_id);



		$sql2 = $this->db->select('validity_date, check_terms, general_terms, bulk_terms')
						 ->from('leads')
						 ->where('id', $this->uri->segment(3))
						 ->get();


				$validity_date = '';
				$check_terms = '';
				$general_terms = '';
				$bulk_terms = '';

			if($sql2->num_rows() > 0) {
				foreach ($sql2->result() as $row2);		
				$validity_date = $row2->validity_date;
				$check_terms = $row2->check_terms;
				$general_terms = $row2->general_terms;
				$bulk_terms = $row2->bulk_terms;
			}

			$data_c = array(
						'lead_id' => $this->uri->segment(3),
						'company_id' => $this->input->post('hpcl_company_hidden'),
						'customer_id' => $cust_id,
						'added_on'=>$date,
						'added_by'=>$user_id,
						'monthly_consumption'=>$monthly_consumption,
						'credit_days'=>$paymentterms,
						'special_remarks'=>$this->input->post('remarks'),
						'payment_type'=>$payment_type,
						'validity_date'=>$validity_date,
						'check_terms'=>$check_terms,
						'general_terms'=>$general_terms,
						'bulk_terms'=>$bulk_terms
					);

			$this->db->insert('customer_quotation',$data_c);
			$quotation_id = $this->db->insert_id();

			$comp_product = $this->input->post('competitor_product1');
			$productname = $this->input->post('product1');
            $qty = $this->input->post('qty1');
            $packsize = $this->input->post('unit_id1');
            $listprice = $this->input->post('listprice1');
	        $agreed_price = $this->input->post('agreed_price1');
	        $discountpricehide_new = $this->input->post('discountpricehide1');

       		for($k=0 ;$k<count($productname);$k++) {
       			if($productname[$k] != '') {

       				if($agreed_price[$k]<$discountpricehide_new[$k])
       				{
       					$flag=1;
       				}else
       				{
       					$flag=0;
       				}

       				$cp=$this->salescrm->getcurrentcp($productname[$k]);
		 			$data1 = array(
							'quotation_id' => $quotation_id,
							'competitor_product' => $comp_product[$k],
							'product_id' => $productname[$k],
							'qty' => $qty[$k],
							'pack_size' => $packsize[$k],
							'list_price' => $listprice[$k],
							'agreed_price' => $agreed_price[$k],
							'flag' => $flag,
							'msp'=>$discountpricehide_new[$k],
							'cp'=>$cp,
							'new_batch_code'=>1,
							'added_on' => date('Y-m-d H:i:s'),
							'added_by' => $user_id
					);


		            $this->db->insert('customer_quotation_detail',$data1);
	        	}
	    	}
			$upd_id=$this->input->post('editid1');
			for($j=0 ;$j<count($upd_id);$j++) {
	            $comp_prod_old = $this->input->post('comp_product_edit1'.$upd_id[$j]);
	            $prod_old = $this->input->post('productedit1'.$upd_id[$j]);
	            $qty_old = $this->input->post('qtyedit1'.$upd_id[$j]);
	            $unit_old = $this->input->post('unit_id_edit1'.$upd_id[$j]);
	            $listprice_old = $this->input->post('listpriceedit1'.$upd_id[$j]);
	            $agreed_price_edit = $this->input->post('agreed_price_edit1'.$upd_id[$j]);
	            $discountpricehideedit = $this->input->post('discountpricehideedit'.$upd_id[$j]);


	            if($listprice_old<$discountpricehideedit)
       				{
       					$flag=0;
       				}else
       				{
       					$flag=1;
       				}

       				$cp=$this->salescrm->getcurrentcp($prod_old);
	       		    $data8 = array(
	       				'quotation_id' => $quotation_id,
						'competitor_product' => $comp_prod_old,
						'product_id' => $prod_old,
						'qty' => $qty_old,
						'pack_size' => $unit_old,
						'list_price' => $listprice_old,
						'agreed_price' => $agreed_price_edit,
						'flag' => $flag,
						'msp'=>$discountpricehideedit,
						'cp'=>$cp,
						'new_batch_code'=>1,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->session->userdata['logged_in']['user_id']
					);

	       		$this->db->insert('customer_quotation_detail',$data8);
			}

	

				if($this->input->post('check_freight') == 1) {
					$freight = 1;
				} else {
					$freight = 0;
				}


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


			$invoice_nnno=$this->getinvoice_no_new($this->input->post('hpcl_company_hidden'));
			$sales_order_no=$this->getsales_no_new();
			$sql = $this->db->select('generated_order_id')
							->from('order_punch')
							->where('added_on>=',$start_date)
							->where('added_on<=',$end_date)
							 ->where('hpcl_billing_company',$this->input->post('hpcl_company_hidden'))
							->order_by('id', 'DESC')
							->limit(1)
							->get();

				if ($sql->num_rows() > 0) {
					foreach ($sql->result() as $row);	
						$generated_order_id = str_pad($row->generated_order_id+1, 3, '0', STR_PAD_LEFT);
				} else {
						$generated_order_id = '001';
				}


			$data2 = array(
					 'quotation_id' => $quotation_id,
					 'source'=> 4,
					 'agent'=>$this->session->userdata['logged_in']['user_id'],
					 'sales_order_no'=>$sales_order_no,
					 'salesorderstart'=>1,
					 //'invoice_no' => $invoice_nnno,
					 'direct_order' => 0,
					 'upload_po' => $newname,
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'hpcl_billing_company' => $this->input->post('hpcl_company_hidden'),
					 'freight' => $freight,
					 'freight_amount' => $this->input->post('freight_amt'),
					 'generated_order_id' => $generated_order_id,
					 'added_on' => date('Y-m-d H:i:s'),
					 'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		// echo "<pre>";print_r($data);exit;

		$this->db->insert('order_punch', $data2);
		$order_id = $this->db->insert_id();


		$data4 = array(
						 'create_date' => date('Y-m-d'),
						 'order_id' => $order_id,
						 'company' => $this->input->post('hpcl_company_hidden'),
						 'customer' => $cust_id,
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
					 'billing_email' => $this->input->post('billing_email'),
					 'vehicle_no' => $this->input->post('vehicle_no'),
					 'vehicle_type' => $this->input->post('vehicle_type'),
					 'destination' => $this->input->post('destination')
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

		$this->db->where('id', $quotation_id)
				 ->update('customer_quotation', $data_upd);

		}else
		{
			$ty='';
			//echo $prd_not_available1; exit;
			$product_names=$this->salescrm->get_prd_not_available($prd_not_available,$prd_not_available1);

			if(count($product_names)>0)
			{
				foreach($product_names as $prd_names1)
				{
				$ty.=$prd_names1."<br/><br/>";
				}
			}
			echo "<strong style='color:red;font-weight:bold;'>REQUIRED STOCK IS NOT AVAIABLE FOR FOLLOWING ITEMS<BR/><br/>".$ty."<br/><br/><a href='".page_url."Leads/view_detail/".$this->uri->segment(3)."/".$this->uri->segment(4)."'>GO BACK</a> AND REMOVE THESE ITEMS TO CREATE ORDER</strong>"; exit;

		}

		}

         /** END LEAD CONVERSION **/



		/** CHECK FOR SAMPLE REQUIRED **/
		$samplestatus=$this->salescrm->getSampleRequiredLeadStage();
		if($this->input->post('leadquality')==$samplestatus)
		{
			$l_product_id=$this->input->post('lead_product_id');
			for($pid=0;$pid<count($l_product_id);$pid++)
			{
				$lead_prd_id=$l_product_id[$pid];
				$sendsample=$this->input->post('sendsample'.$lead_prd_id);

				if($sendsample==1)
				{
					$sampledata=array('lead_id'=>$this->uri->segment(3),'lead_product_id'=>$lead_prd_id,'requestOn'=>date('Y-m-d h:i:s'),'request_remarks'=>$this->input->post('remarks'),'request_by'=>$_SESSION['logged_in']['user_id']);

					$this->db->insert('sample_to_be_sent',$sampledata);

				}
			}
		}

		/** END **/


		/** CHECK FOR TRIAL REQUIRED **/
		$trailstatus=$this->salescrm->getTrialRequiredLeadStage();
		if($this->input->post('leadquality')==$trailstatus)
		{
			$l_product_id=$this->input->post('lead_product_id_trail');
		
			
			for($pid=0;$pid<count($l_product_id);$pid++)
			{
				//$prdfortrail=$this->input->post('prdfortrail'.$l_product_id[$pid]);
				$lead_prd_id=$l_product_id[$pid];
				//echo $lead_prd_id; exit;
				$sendsample=$this->input->post('sendtrail'.$lead_prd_id);

				 if($sendsample==1)
				 {
					$sampledata=array('lead_id'=>$this->uri->segment(3),'lead_product_id'=>$lead_prd_id,'requestOn'=>date('Y-m-d h:i:s'),'request_remarks'=>$this->input->post('remarks'),'request_by'=>$_SESSION['logged_in']['user_id']);

					$this->db->insert('trial_to_be_sent',$sampledata);

				}
			}
		}

		/** END **/
		

		if(in_array($this->input->post('leadquality'), $quotation_status_id)) {
			redirect(page_url.'Leads/lead_quotation_view/'.$uid);

		} else {

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			if($_SESSION['logged_in']['role']==1)
			{
				redirect(page_url.'Leads/lead_stages/'.$this->uri->segment(4));
			}else
			{
				redirect(page_url.'Leads/lead_stages_user/'.$this->uri->segment(4));

			}
			
			
		}

	}

		public function lead_quotation_view() {

			$id=$this->uri->segment(3);
			//echo $id; exit;
			header('location:'.site_http_root.'poformat/tcpdf/examples/hpcl_emailer.php?lead_id='.$id);
			//$this->load->view('leads/lead_quotation_preview');
		}

	// public function update_remarks(){
		
	// 	$user_id =$this->session->userdata['logged_in']['user_id'];	
	// 	$previousstatus=$this->input->post('previous_lead_stage');
	// 	// $quotation_name = $this->getsettings();
	// 	// if(count($quotation_name) >0)
	// 	// {
	// 	// 	$quotefile=$quotation_name['quotefile'];
	// 	// 	$pifile=$quotation_name['pifile'];
	// 	// }else{
	// 	// $quotefile='';
	// 	// 	$pifile='';	
	// 	// }
		
	// 	$check_status = array();
	// 	date_default_timezone_set("Asia/Kolkata");
	// 	$date =  date('Y-m-d H:i:s'); 
	// 	$table = "progress_remarks";
	// 	$followupdate = $this->input->post('followup_date');

	// 	if($followupdate == '') {
	// 		$followup_date = '';
	// 	} else {
	// 		$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
	// 	}
		
 //             $lead_stage = $this->input->post('leadquality');

	// 		// $skip_whatsapp = $this->input->post('skip_whatsapp');

	// 		// if ($skip_whatsapp == '') {
	// 		// 	$skip = 0;
	// 		// } else {

	// 		// 	$skip = 1;
	// 		// }

	// 		$data = array(
	// 					'lead_id'=>$this->uri->segment(3),
	// 					'lead_stage' => $lead_stage,
	// 					'next_follow_date'=>$followup_date,
	// 					'remarks'=>$this->input->post('remarks'),
	// 					'remark_title'=>$this->input->post('remark_title'),
	// 					'nonqualifiedreason'=>$this->input->post('nonqualifiedreason'),
	// 					'skip_whatsapp'=>0,
	// 					'added_on'=>$date,
	// 					'added_by'=>$user_id
	// 					);
	// 		 // echo "<pre>";print_r($data);exit;
	// 		$last_id = $this->master->insert_record($table,$data);

	// 	$pic = $_FILES['upload_file']['name'];

	// 	for($i=0; $i<count($pic); $i++) {
	// 		$picture = $pic[$i];
	// 		  if($picture <> '') {
	// 			$files = explode('.', $picture);
	// 			$ext = end($files);
	// 			$newname = time().$i.'.'.$ext;
	// 			// echo lead_uploads.$newname;exit;
	// 			move_uploaded_file($_FILES['upload_file']["tmp_name"][$i], lead_uploads.$newname);
	// 			$data_p = array(
	// 						 'lead_id' => $this->uri->segment(3),
	// 						 'remarks_id' => $last_id,
	// 						 'lead_stage' => $lead_stage,
	// 						 'upload_file' => $newname
	// 						);

	// 			$this->db->insert('lead_remarks_files', $data_p);
	// 		 }
				
	// 		}

	// 		$checkStatusForEntry = $this->salescrm->getLeadStageDetails($lead_stage);
	// 		$product_id = $this->input->post('product_id');
	// 		$lead_product_id=$this->input->post('lead_product_id');

	// 			if ($checkStatusForEntry != '') {
	// 				foreach ($checkStatusForEntry as $row2);

	// 					if($row2->quotation_step == 1) {
	// 						for($j=0; $j<count($lead_product_id); $j++) {

	// 							  if($lead_product_id[$j] != '') {
								
	// 		                    $qty = $this->input->post('qty'.$lead_product_id[$j]);
	// 		                    $price = $this->input->post('price'.$lead_product_id[$j]);
	// 		                    $discount_type = $this->input->post('discount_type'.$lead_product_id[$j]);
	// 		                    $percent_amt = $this->input->post('percent_amt'.$lead_product_id[$j]);
							
	// 								$datas = array(
	// 											 'qty' => $qty,
	// 											 'price' => $price,
	// 											 'discount_type' => $discount_type,
	// 											 'percent_amt' => $percent_amt
	// 											);

	// 								$this->db->where('id', $lead_product_id[$j])
	// 										 ->update('lead_products', $datas);
	// 							 }
						
	// 						}

	// 						 if($this->input->post('packing_type') == 3) {
	// 	                        $packprice=0.00;
	// 	                    } else {
	// 	                        $packprice=$this->input->post('packing_charges');
	// 	                    }
		                    
	// 	                     if($this->input->post('freight_actual') == 1) {
	// 	                        $fprice=0.00;
	// 	                    } else {
	// 	                        $fprice=$this->input->post('freight_charges');
	// 	                    }		            
		            
	// 						$datas = array(
	// 									 'lead_id' => $this->uri->segment(3),
	// 									 'packing_type' => $this->input->post('packing_type'),
	// 									 'packing_price'=>$packprice,
	// 									 'freight_actual' => $this->input->post('freight_actual'),
	// 									 'freight_charges' => $fprice
	// 									 );

	// 						$this->db->insert('lead_product_details', $datas);
	// 						//$this->send_communication($lead_stage, $this->uri->segment(3));
	// 						redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3).'&leadstatus='.$lead_stage.'&flag=1');
	// 					} else if($row2->quotation_revised_step == 1) {
	// 						$product_id = $this->input->post('product_idedit');
	// 						$lead_product_id=$this->input->post('lead_product_idedit');
					    

	// 						for($j=0; $j<count($lead_product_id); $j++) {

	// 							  if($lead_product_id[$j] != '') {
								
	// 		                    $qty = $this->input->post('qtyedit'.$lead_product_id[$j]);
	// 		                    $price = $this->input->post('priceedit'.$lead_product_id[$j]);
	// 		                    $discount_type = $this->input->post('discount_typeedit'.$lead_product_id[$j]);
	// 		                    $percent_amt = $this->input->post('percent_amtedit'.$lead_product_id[$j]);
							
	// 								$datas = array(
	// 											 'qty' => $qty,
	// 											 'price' => $price,
	// 											 'discount_type' => $discount_type,
	// 											 'percent_amt' => $percent_amt
	// 											);

	// 								$this->db->where('id', $lead_product_id[$j])
	// 										 ->update('lead_products', $datas);
	// 							 }
									
	// 							}


	// 		                    if($this->input->post('packing_typeedit')==3)
	// 		                    {
	// 		                        $packprice=0.00;
	// 		                    }else
	// 		                    {
	// 		                        $packprice=$this->input->post('packing_chargesedit');
	// 		                    }
			            
			                    
	// 		                     if($this->input->post('freight_actualedit')==1)
	// 		                    {
	// 		                        $fprice=0.00;
	// 		                    }else
	// 		                    {
	// 		                        $fprice=$this->input->post('freight_chargesedit');
	// 		                    }
			            
			            
			            
	// 							$datas = array(
										
	// 										 'packing_type' => $this->input->post('packing_typeedit'),
	// 										 'packing_price'=>$packprice,
	// 										 'freight_actual' => $this->input->post('freight_actualedit'),
	// 										 'freight_charges' => $fprice
	// 										 );

								
	// 		                    $this->db->where('lead_id',$this->uri->segment(3));
	// 							$this->db->update('lead_product_details', $datas);

	// 							//$this->send_communication($lead_stage, $this->uri->segment(3));
	// 							redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3).'&leadstatus='.$lead_stage.'&flag=1');
	// 					}  else {

	// 						$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Thank you, record successfully updated.</div>');

	// 						redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
	// 		}
	// 			}

			
		
	
	// }
	public function update_remarksoLD_BEFORE_LEAD_STAGE_DYNAMIC(){
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$quotation_name = $this->getsettings();
		if(count($quotation_name) >0)
		{
			$quotefile=$quotation_name['quotefile'];
			$pifile=$quotation_name['pifile'];
		}else{
		$quotefile='';
			$pifile='';	
		}
		
		$check_status = array();
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "progress_remarks";
		$followupdate = $this->input->post('followup_date');

		if($followupdate == '') {
			$followup_date = '';
		} else {
			$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
		}
		
             $lead_stage = $this->input->post('leadquality');

			$skip_whatsapp = $this->input->post('skip_whatsapp');

			if ($skip_whatsapp == '') {
				$skip = 0;
			} else {

				$skip = 1;
			}

			$data = array(
						'lead_id'=>$this->uri->segment(3),
						'lead_status' => $lead_stage,
						'next_follow_date'=>$followup_date,
						'remarks'=>$this->input->post('remarks'),
						'remark_title'=>$this->input->post('remark_title'),
						'nonqualifiedreason'=>$this->input->post('nonqualifiedreason'),
						'skip_whatsapp'=>$skip,
						'added_on'=>$date,
						'added_by'=>$user_id
						);
			 // echo "<pre>";print_r($data);exit;
			$last_id = $this->master->insert_record($table,$data);

		$pic = $_FILES['upload_file']['name'];

		for($i=0; $i<count($pic); $i++) {
			$picture = $pic[$i];
			  if($picture <> '') {
				$files = explode('.', $picture);
				$ext = end($files);
				$newname = time().$i.'.'.$ext;
				// echo lead_uploads.$newname;exit;
				move_uploaded_file($_FILES['upload_file']["tmp_name"][$i], lead_uploads.$newname);
				$data_p = array(
							 'lead_id' => $this->uri->segment(3),
							 'remarks_id' => $last_id,
							 'lead_stage' => $lead_stage,
							 'upload_file' => $newname
							);

				$this->db->insert('lead_remarks_files', $data_p);
			 }
				
			}

		

		
			if($lead_stage == 8) {
				$product_id = $this->input->post('product_id');
				$lead_product_id=$this->input->post('lead_product_id');
				

				for($j=0; $j<count($lead_product_id); $j++) {

					  if($lead_product_id[$j] != '') {
					
                    $qty = $this->input->post('qty'.$lead_product_id[$j]);
                    $price = $this->input->post('price'.$lead_product_id[$j]);
                    $discount_type = $this->input->post('discount_type'.$lead_product_id[$j]);
                    $percent_amt = $this->input->post('percent_amt'.$lead_product_id[$j]);
				
						$datas = array(
									 'qty' => $qty,
									 'price' => $price,
									 'discount_type' => $discount_type,
									 'percent_amt' => $percent_amt
									);

						$this->db->where('id', $lead_product_id[$j])
								 ->update('lead_products', $datas);
					 }
						
					}


                    if($this->input->post('packing_type')==3)
                    {
                        $packprice=0.00;
                    }else
                    {
                        $packprice=$this->input->post('packing_charges');
                    }
            
                    
                     if($this->input->post('freight_actual')==1)
                    {
                        $fprice=0.00;
                    }else
                    {
                        $fprice=$this->input->post('freight_charges');
                    }
            
            
            
					$datas = array(
								 'lead_id' => $this->uri->segment(3),
								 'packing_type' => $this->input->post('packing_type'),
								 'packing_price'=>$packprice,
								 'freight_actual' => $this->input->post('freight_actual'),
								 'freight_charges' => $fprice
								 );

					$this->db->insert('lead_product_details', $datas);
					
					 $this->send_communication($lead_stage, $this->uri->segment(3));
					redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3));

			} else if($lead_stage==14)
			{
			    
			    $product_id = $this->input->post('product_idedit');
				$lead_product_id=$this->input->post('lead_product_idedit');
		    

				for($j=0; $j<count($lead_product_id); $j++) {

					  if($lead_product_id[$j] != '') {
					
                    $qty = $this->input->post('qtyedit'.$lead_product_id[$j]);
                    $price = $this->input->post('priceedit'.$lead_product_id[$j]);
                    $discount_type = $this->input->post('discount_typeedit'.$lead_product_id[$j]);
                    $percent_amt = $this->input->post('percent_amtedit'.$lead_product_id[$j]);
				
						$datas = array(
									 'qty' => $qty,
									 'price' => $price,
									 'discount_type' => $discount_type,
									 'percent_amt' => $percent_amt
									);

						$this->db->where('id', $lead_product_id[$j])
								 ->update('lead_products', $datas);
					 }
						
					}


                    if($this->input->post('packing_typeedit')==3)
                    {
                        $packprice=0.00;
                    }else
                    {
                        $packprice=$this->input->post('packing_chargesedit');
                    }
            
                    
                     if($this->input->post('freight_actualedit')==1)
                    {
                        $fprice=0.00;
                    }else
                    {
                        $fprice=$this->input->post('freight_chargesedit');
                    }
            
            
            
					$datas = array(
							
								 'packing_type' => $this->input->post('packing_typeedit'),
								 'packing_price'=>$packprice,
								 'freight_actual' => $this->input->post('freight_actualedit'),
								 'freight_charges' => $fprice
								 );

                    $this->db->where('lead_id',$this->uri->segment(3));
					$this->db->update('lead_product_details', $datas);
					
					 $this->send_communication($lead_stage, $this->uri->segment(3));
					redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3));
			    
			    
			    
			 }else if ($lead_stage == 3 || $lead_stage==15) {
			     
			      $product_id = $this->input->post('product_idedit');
				$lead_product_id=$this->input->post('lead_product_idedit');
				

				for($j=0; $j<count($lead_product_id); $j++) {

					  if($lead_product_id[$j] != '') {
					
                    $qty = $this->input->post('qtyedit'.$lead_product_id[$j]);
                    $price = $this->input->post('priceedit'.$lead_product_id[$j]);
                    $discount_type = $this->input->post('discount_typeedit'.$lead_product_id[$j]);
                    $percent_amt = $this->input->post('percent_amtedit'.$lead_product_id[$j]);
				
						$datas = array(
									 'qty' => $qty,
									 'price' => $price,
									 'discount_type' => $discount_type,
									 'percent_amt' => $percent_amt
									);

						$this->db->where('id', $lead_product_id[$j])
								 ->update('lead_products', $datas);
					 }
						
					}


                    if($this->input->post('packing_typeedit')==3)
                    {
                        $packprice=0.00;
                    }else
                    {
                        $packprice=$this->input->post('packing_chargesedit');
                    }
            
                    
                     if($this->input->post('freight_actualedit')==1)
                    {
                        $fprice=0.00;
                    }else
                    {
                        $fprice=$this->input->post('freight_chargesedit');
                    }
            
            
            
					$datas = array(
							
								 'packing_type' => $this->input->post('packing_typeedit'),
								 'packing_price'=>$packprice,
								 'freight_actual' => $this->input->post('freight_actual'),
								 'freight_charges' => $fprice
								 );

                    $this->db->where('lead_id',$this->uri->segment(3));
					$this->db->update('lead_product_details', $datas);
					
					/** UPDATE GST AND TERMS & CONDITIONS **/
					
					$extradata=array('gst'=>$this->input->post('customer_gst'),
					'pi_terms'=>$this->input->post('termscondition'));
					
					$this->db->where('id',$this->uri->segment(3));
					$this->db->update('leads',$extradata);
					
					/** END **/
					
			     $this->send_communication($lead_stage, $this->uri->segment(3));
				redirect(page_url1.'pdf/rfq/examples/'.$pifile.'.php?lead_id='.$this->uri->segment(3));
			}else {

			 $this->send_communication($lead_stage, $this->uri->segment(3));
			$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
			}
			
		
	
	}

	function previewquotation() {
		$this->load->view('leads/lead_quotation_preview');
	}

	function previewpi() {
		$this->load->view('leads/previewpi');
	}

// function send_communication($lead_stage, $lead_id) {

// 	// $setarray=$this->getsettings();
// 	// 	if(count($setarray)>0)
// 	// 	{
// 	// 		$email_ccmailid=$setarray['email_ccmailid'];
// 	// 		$email_smtp=$setarray['email_smtp'];
// 	// 		$whatsappuser=$setarray['whatsappuser'];
// 	// 		$whatsapppassword=$setarray['whatsapppassword'];
// 	// 		$emailer=$setarray['emailer'];
// 	// 		$unsubscribe=$setarray['unsubscribe'];
// 	// 	}else
// 	// 	{
// 	// 		$email_ccmailid='';
// 	// 		$email_smtp='';
// 	// 		$whatsappuser='';
// 	// 		$whatsapppassword='';
// 	// 		$emailer='';
// 	// 		$unsubscribe='';
// 	// 	}
// 		$sql = $this->db->select('unique_id,country_code,title,customer_name, email_id, company_name, contact_no')

// 					    ->from('leads')

// 					    ->where('id',$lead_id)

// 					    ->get();



// 		if ($sql->num_rows() > 0) {

// 				foreach ($sql->result() as $row) {

// 					$query = $this->db->select('a.lead_stage, a.include_pdf, a.include_quotation, a.include_pi, a.immediate, a.whatsapp, a.sms, a.email, b.email_subject, b.email_body')

// 							          ->from('testronix_god_mode_view a')

// 							          ->join('designation_wise_communication_view b', 'b.god_mode_id=a.id')

// 							          ->where('a.company_id',$_SESSION['logged_in']['business_location'])
// 							   		  ->where('a.lead_stage', $lead_stage)

// 							   		  ->limit(1)

// 							   		  ->get();



// 						if ($query->num_rows() > 0) {

// 							$pro = array();

// 							foreach ($query->result() as $rows);

// 								if ($rows->immediate == 1) {


									
// 								   $sql1 = $this->db->select('a.instruments_name, a.pdf')

// 												  	->from('presto_instruments_view a')

// 												  	->join('lead_products b', 'a.id=b.product_id')

// 												  	->where('b.lead_id', $lead_id)

// 												  	->get();



// 										// if ($sql1->num_rows() > 0) {

// 											foreach ($sql1->result() as $row1) {

// 													$pro[] = $row1->instruments_name;

// 												}



// 												if(count($pro) > 0) {



// 												$product_name = implode(',',$pro);	

// 											} else {

// 												$product_name = '';

// 											}

// 										// }



// 										if ($rows->email == 1) {

// 											$find = array('customer_title', 'customer_name', 'company_name', 'email_id', 'contact_number', 'product_name');

// 											$replace = array($row->title, $row->customer_name, $row->company_name, $row->email_id, $row->contact_no, $product_name);

// 											$message = str_replace($find, $replace, $rows->email_body);

// 												//HTML CODE STARTS
// 							$font="'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif";

// if($emailer == 1) {
// $message_body='<!DOCTYPE>
// <html xmlns="" xmlns=""
//     style="box-sizing: border-box; font-family:'.$font.';margin: 0; mso-line-height-rule: exactly; padding: 0">

// <head>
//     <meta name="viewport" content="width=device-width" />
//     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//     <meta name="robots" content="noindex, nofollow" />
//     <title>Ease My Sale</title>
// </head>

// <body align="center"
//     style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; background: #f4f6fc; box-sizing: border-box; font-family: $font; height: 100%; line-height: 1.7; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100% !important"
//     bgcolor="#ffffff">
//     <style type="text/css">
//         img {
//             max-width: 100%;
//             display: block;
//         }

//         body {
//             -webkit-font-smoothing: antialiased;
//             -webkit-text-size-adjust: none;
//             width: 100% !important;
//             height: 100%;
//             line-height: 1.7;
//         }

//         body {
//             background-color: #f4f6fc;
//         }

//         .ExternalClass {
//             width: 100%;
//         }



//         body .footer p {
//             margin: 0 !important;
//         }

//         body {
//             background-color: #f4f6fc;
//         }
//     </style>
//     <table align="center" class="body-wrap"
//         style="background: #f4f6fc; box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100%; word-break: break-word"
//         bgcolor="#f4f6fc">
//         <tr
//             style="box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//             <td align="center"
//                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                 valign="top">
//                 <table
//                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                     <tr
//                         style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                         <td class="container" width="600"
//                             style="box-sizing: border-box; clear: both !important; display: block !important; font-family: '.$font.'; margin: 0 auto; max-width: 600px !important; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                             valign="top">
//                             <div class="content"
//                                 style="box-sizing: border-box; display: block; font-family: '.$font.'; margin: 0 auto; max-width: 600px; mso-line-height-rule: exactly; padding: 0">
//                                 <table class="main" width="100%" cellpadding="0" cellspacing="0"
//                                     style=" box-sizing: border-box; font-family: '.$font.'; margin: 35px 0 0; mso-line-height-rule: exactly; padding: 0">

//                                     <tr
//                                         style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                         <td class="content-wrap"
//                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding:20px; vertical-align: top; border: 3px solid #0293d3; border-radius: 20px; background-color: white;"
//                                             valign="top">
//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">

                                               

//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #ffffff; font-family: '.$font.'; font-size: 14px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top"
//                                                         valign="top" width="50%">
//                                                         <img src="https://easemysale.com/app/assets/images/logo1.png">
//                                                     </td>
//                                                     <td class="content-block" style="box-sizing: border-box; color: #000; font-family: '.$font.' font-size: 14px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly;  text-align: end;
//                                                         line-height: 45px;" valign="top" width="50%">
                                                       
//                                                     </td>
//                                                 </tr>

//                                             </table><br/><br/><br/>
                                            
//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">

                                                
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto;margin-top:30; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; border-bottom: 1px solid lightgray;"
//                                                         valign="top" width="100%">
//                                                         <p>'.$message.'</p>
//                                                     </td>

//                                                 </tr>

//                                             </table>';

//                                           if($unsubscribe == 1) {
//                                          $message_body .= '<table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">

                                    

//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-family: '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="100%">
//                                                         <p><i>If you wish to unsubscribe with this email <a href="www.easemysale.com">Click Here</a>
//                                                             </i>


//                                                         </p>
//                                                     </td>
//                                                 </tr>
//                                             </table>';
//                                         }
//                                            $message_body .= '<table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">



//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-weight: bold; font-family:  '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="50%"><p>www.easemysale.com</p>


//                                                         </p>
//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-weight: bold; text-align: right; font-family: '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="50%">
//                                                         <p>+91 9313 140 140  </p>
//                                                     </td>
//                                                 </tr>



//                                             </table>
//                                         </td>
//                                     </tr>
//                                 </table>
//                             </div>
//                         </td>
//                     </tr>
//                 </table>
//             </td>
//         </tr>
//     </table>
// </body>

// </html>';
// } else if($emailer == 2) {
// 	$message_body = '<!DOCTYPE>
// <html xmlns="" xmlns=""
//     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">

// <head>
//     <meta name="viewport" content="width=device-width" />
//     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//     <meta name="robots" content="noindex, nofollow" />
//     <title>Ease My Sale</title>
// </head>

// <body align="center"
//     style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;  box-sizing: border-box; font-family: '.$font.'; height: 100%; line-height: 1.7; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100% !important"
//     bgcolor="#ffffff">
//     <style type="text/css">
//         img {
//             max-width: 100%;
//             display: block;
//         }

//         body {
//             -webkit-font-smoothing: antialiased;
//             -webkit-text-size-adjust: none;
//             width: 100% !important;
//             height: 100%;
//             line-height: 1.7;
//         }



//         .ExternalClass {
//             width: 100%;
//         }



//         body .footer p {
//             margin: 0 !important;
//         }
//     </style>
//     <table align="center" class="body-wrap"
//         style=" box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100%; word-break: break-word">
//         <tr
//             style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//             <td align="center"
//                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                 valign="top">
//                 <table
//                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                     <tr
//                         style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                         <td class="container" width="600"
//                             style="box-sizing: border-box; clear: both !important; display: block !important; font-family: '.$font.'; margin: 0 auto; max-width: 600px !important; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                             valign="top">
//                             <div class="content"
//                                 style="box-sizing: border-box; display: block; font-family: '.$font.'; margin: 0 auto; max-width: 600px; mso-line-height-rule: exactly; padding: 0">
//                                 <table class="main" width="100%" cellpadding="0" cellspacing="0"
//                                     style=" box-sizing: border-box; font-family: '.$font.'; margin: 35px 0 0; mso-line-height-rule: exactly; padding: 0">
//                                     <tr
//                                         style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                         <td class="content-wrap"
//                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding:20px; vertical-align: top; background-color: whitesmoke;"
//                                             valign="top">
//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="30%">


//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="40%">
//                                                         <img src="https://easemysale.com/app/assets/images/logo1.png">

//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="30%">


//                                                     </td>
//                                                 </tr>
//                                             </table>

//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box;  color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; background-color: white;"
//                                                         valign="top" width="100%">

//                                                         <table width="100%" cellpadding="0" cellspacing="0"
//                                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                             <tr
//                                                                 style="box-sizing: border-box;  margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                                 <td class="content-block"
//                                                                     style="box-sizing: border-box; text-transform:uppercase; color: #c1c1c1; text-align: center; text-transform: uppercase;  font-size: 16px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 5px; font-weight: bold; "
//                                                                     valign="top" width="100%">
//                                                                     <p>THE LATEST NEWS:</p>

//                                                                 </td>

//                                                             </tr>
//                                                             <tr
//                                                                 style="box-sizing: border-box;  margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                                 <td class="content-block"
//                                                                     style="box-sizing: border-box;  color: #000; text-align: center;   font-size: 21px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 5px;  "
//                                                                     valign="top" width="100%">
//                                                                     <p>Lorem ipsum dolor, sit amet</p>

//                                                                 </td>

//                                                             </tr>

//                                                             <tr
//                                                             style="box-sizing: border-box;  margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                             <td class="content-block"
//                                                                 style="box-sizing: border-box;  color: #000; text-align: left;    font-size: 13px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 5px;  "
//                                                                 valign="top" width="100%">
//                                                                 <p>'.$message.'</p>
//                                                             </td>
//                                                         </tr>
//                                                 <tr
//                                                     style="box-sizing: border-box;  margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box;  color: #000; text-align: left;  font-size: 13px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 5px;  "
//                                                         valign="top" width="100%">
//                                                     </td>
//                                                 </tr>
//                                                         </table>

//                                                     </td>

//                                                 </tr>
//                                             </table>';

//                                             if($unsubscribe == 1) {
//                                              $message_body .= '<table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-family: '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="100%">
//                                                         <p><i>If you wish to unsubscribe with this email <a href="www.easemysale.com">Click Here</a>
//                                                             </i>
//                                                         </p>
//                                                     </td>
//                                                 </tr>
//                                             </table>';
//                                         	}

//                                             $message_body .= '<table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">



//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-weight: bold; font-family:  '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="50%"><p>www.easemysale.com</p>


//                                                         </p>
//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; color: #000; font-weight: bold; text-align: right; font-family: '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px;"
//                                                         valign="top" width="50%">
//                                                         <p>+91 9313 140 140  </p>
//                                                     </td>
//                                                 </tr>



//                                             </table>

//                                         </td>
//                                     </tr>
//                                 </table>
//                             </div>
//                         </td>
//                     </tr>
//                 </table>
//             </td>
//         </tr>
//     </table>
// </body>

// </html>';
// } else if($emailer == 3) {
// 	$message_body = '<!DOCTYPE>
// <html xmlns="" xmlns=""
//     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">

// <head>
//     <meta name="viewport" content="width=device-width" />
//     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//     <meta name="robots" content="noindex, nofollow" />
//     <title>Preview</title>
// </head>

// <body align="center"
//     style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none;  box-sizing: border-box; font-family: '.$font.'; height: 100%; line-height: 1.7; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100% !important"
//     bgcolor="#ffffff">
//     <style type="text/css">
//         img {
//             max-width: 100%;
//             display: block;
//         }

//         body {
//             -webkit-font-smoothing: antialiased;
//             -webkit-text-size-adjust: none;
//             width: 100% !important;
//             height: 100%;
//             line-height: 1.7;
//         }



//         .ExternalClass {
//             width: 100%;
//         }



//         body .footer p {
//             margin: 0 !important;
//         }
//     </style>
//     <table align="center" class="body-wrap"
//         style=" box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0; width: 100%; word-break: break-word">
//         <tr
//             style="box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//             <td align="center"
//                 style="box-sizing: border-box; font-family:'.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                 valign="top">
//                 <table
//                     style="box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                     <tr
//                         style="box-sizing: border-box; font-family:'.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                         <td class="container" width="600"
//                             style="box-sizing: border-box; clear: both !important; display: block !important; font-family:'.$font.'; margin: 0 auto; max-width: 600px !important; mso-line-height-rule: exactly; padding: 0; vertical-align: top"
//                             valign="top">
//                             <div class="content"
//                                 style="box-sizing: border-box; display: block; font-family:'.$font.'; margin: 0 auto; max-width: 600px; mso-line-height-rule: exactly; padding: 0">
//                                 <table class="main" width="100%" cellpadding="0" cellspacing="0"
//                                     style=" box-sizing: border-box; font-family: '.$font.'; margin: 35px 0 0; mso-line-height-rule: exactly; padding: 0">
//                                     <tr
//                                         style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                         <td class="content-wrap"
//                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0 auto; mso-line-height-rule: exactly; padding:20px; vertical-align: top; background-color: whitesmoke;"
//                                             valign="top">
//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="30%">
//                                                         <p style="background-color:whitesmoke ;">

//                                                         </p>

//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="40%">
//                                                         <img src="https://easemysale.com/app/assets/images/logo1.png">

//                                                     </td>
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box; text-transform:uppercase; color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; "
//                                                         valign="top" width="30%">

//                                                         <p style="background-color:whitesmoke ;">

//                                                         </p>
//                                                     </td>
//                                                 </tr>
//                                             </table>

//                                             <table width="100%" cellpadding="0" cellspacing="0"
//                                                 style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box;  color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 10px; padding-top: 40px; background-color: white;"
//                                                         valign="top" width="100%">

//                                                         <table width="100%" cellpadding="0" cellspacing="0"
//                                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
                                                           
//                                                             <tr
//                                                                 style="box-sizing: border-box;  margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                                 <td class="content-block"
//                                                                     style="box-sizing: border-box;  color: #000; text-align: left;   font-size: 13px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding: 5px;  "
//                                                                     valign="top" width="100%">
//                                                                     <p>'.$message.'</p>

//                                                                 </td>

//                                                             </tr>
//                                                         </table>

//                                                     </td>

//                                                 </tr>
//                                                 <tr
//                                                     style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                                     <td class="content-block"
//                                                         style="box-sizing: border-box;  color: #000; text-align: center; font-family: '.$font.'; font-size: 18px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top;  "
//                                                         valign="top" width="100%">
//                                                         <img src="https://easemysale.com/app/assets/images/fold1.png">
//                                                     </td>
//                                                 </tr>
//                                             </table>';

//                                             if($unsubscribe == 1) {
//                                            $message_body .= '<table width="100%" cellpadding="0" cellspacing="0"
//                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0; margin-top: 10px;">
//                                             <tr
//                                             style="box-sizing: border-box; font-family: '.$font.'; margin: 0; mso-line-height-rule: exactly; padding: 0">
//                                             <td class="content-block"
//                                                 style="box-sizing: border-box;  color: #000;  font-family: '.$font.'; font-size: 12px; line-height: 170%; margin: 0 auto; mso-line-height-rule: exactly; vertical-align: top; padding:10px; "
//                                                 valign="top" width="100%">
//                                                <i>If you wish to unsubscribe with this email <a href="www.easemysale.com">Click Here</a>
//                                                             </i>
//                                             </td>
//                                             </tr>
//                                         </table>';
//                                     	}

//                                         $message_body .= '</td>
//                                     </tr>

//                                 </table>
//                             </div>
//                         </td>
//                     </tr>
//                 </table>
//             </td>
//         </tr>
//     </table>
// </body>

// </html>


// ';
// }

//  // echo $message_body;exit;

// 							// HTML CODE ENDS 



// 											$this->email->clear(TRUE);

// 											$this->email->set_mailtype("html"); 

// 											$this->email->to($row->email_id);
// 											// $this->email->to('webdevelopment1@gamavis.com');

// 											if($email_ccmailid<>'')
// 											{
// 											$this->email->cc($email_ccmailid);
// 											}	

// 											$this->email->from($email_smtp);

// 											$this->email->subject($rows->email_subject);

// 											$this->email->message($message_body);



// 											if($rows->include_pdf == 1) {

// 												foreach ($sql1->result() as $row1) {

// 												if($row1->pdf<>'')
// 												{
// 													if(file_exists(sfdocumentpath.'instrumentpdf/'.$row1->pdf))
// 													{												$this->email->attach(sfdocumentpath.'instrumentpdf/'.$row1->pdf,$row1->instruments_name);
// 													}
// 												}

// 												}

// 											}



// 											if ($rows->include_quotation == 1) {

// 								    	$quotation=sfdocumentpath.'quotationspdf/'.$row->unique_id.'.pdf';
								    	
												
// 												 if(file_exists($quotation))
//                                         {

//                                             	$this->email->attach($quotation,'Quotation');
//                                         }

// 											}



// 											if ($rows->include_pi == 1) {

//                                             	 if(file_exists(sfdocumentpath.'pipdf/'.$row->unique_id.'_pi.pdf'))
//                                         {
                                            
                                        
// 												$this->email->attach(sfdocumentpath.'pipdf/'.$row->unique_id.'_pi.pdf','Technocommercial_offer');
												
//                                         }

// 											}





// 											$this->email->send();
// 								//	echo $this->email->print_debugger(); exit;


// 											//echo "success";exit;



// 										}



// 											$contact_no = $row->country_code."".$row->contact_no;



// 									if ($rows->whatsapp == 1) {
// 										$message2 = str_replace("&nbsp;", " ", strip_tags($message));
// 										// echo $message2;exit;
// 										if($unsubscribe == 1) {
// 											$message2 .= "\n\n".'_If you wish to unsubscribe with this whatsapp - https://easemysale.com/app/Unsubscribe_';
// 										}

// 										 // echo $message2;exit;
// 							/**WHATSAPP INTEGRATION**/
// 								 $ch = curl_init();
// 								curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
// 								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 								curl_setopt($ch, CURLOPT_POST, 1);
// 								$post = array(
// 								// 'receiverMobileNo' => '919560814669',
// 								// 'receiverMobileNo' => '91'.$contact_number,
// 								'receiverMobileNo' => $contact_no, // Receivers phone
// 								'username' => $whatsappuser,
// 								'password' => $whatsapppassword,
// 								'message'=> $message2// Message
// 							);
// 								//echo "<pre>"; print_r($post); exit;
// 								curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// 								$result = curl_exec($ch);
// 								// echo $result; exit;
// 								if (curl_errno($ch)) {
// 								echo 'Error:' . curl_error($ch);
// 								}
// 								curl_close($ch);
						
						

// 						if($rows->include_pdf == 1) {
// 						foreach ($sql1->result() as $row1) {
// 							$ch = curl_init();
// 		                    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
// 		                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		                    curl_setopt($ch, CURLOPT_POST, 1);
// 		                    $post = array(
// 					                    'filePathUrl' => page_url.'image_bank/instrumentpdf/'.$row1->pdf,
// 					                    'receiverMobileNo' => $contact_no,
// 					                    // 'receiverMobileNo' => '919560814669',
// 					                    'username' => $whatsappuser,
// 										'password' => $whatsapppassword
// 					                    );

// 		                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// 		                    $result = curl_exec($ch);
// 		                    if (curl_errno($ch)) {
// 		                    echo 'Error:' . curl_error($ch);
// 		                    }
// 		                    curl_close($ch); 
// 							}
// 						 }

// 						 if ($rows->include_quotation == 1) {

//                             $qlink=page_url1.'image_bank/quotationspdf/'.$row->unique_id.'.pdf'; 
// 						 	$ch = curl_init();
// 		                    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
// 		                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		                    curl_setopt($ch, CURLOPT_POST, 1);
// 		                    $post = array(
// 		                    'filePathUrl' => $qlink,
// 		                    'receiverMobileNo' => $contact_no,
// 		                    // 'receiverMobileNo' => '919560814669',
// 		                    'username' => $whatsappuser,
// 							'password' => $whatsapppassword
// 		                    );

// 		                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// 		                    $result = curl_exec($ch);
// 		                    if (curl_errno($ch)) {
// 		                    echo 'Error:' . curl_error($ch);
// 		                    }
// 		                    curl_close($ch); 

// 						}

// 						 if ($rows->include_pi == 1) {
						     
// 						     $pilink=page_url1.'image_bank/pipdf/'.$row->unique_id.'_pi.pdf'; 
						     
// 						 	$ch = curl_init();
// 		                    curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
// 		                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		                    curl_setopt($ch, CURLOPT_POST, 1);
// 		                    $post = array(
// 		                    'filePathUrl' =>$pilink,
// 		                    // 'receiverMobileNo' => '919560814669',
// 		                    'receiverMobileNo' => $contact_no,
// 		                    'username' => $whatsappuser,
// 		                    'password' => $whatsapppassword
// 		                    );

// 		                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// 		                    $result = curl_exec($ch);
// 		                    if (curl_errno($ch)) {
// 		                    echo 'Error:' . curl_error($ch);
// 		                    }
// 		                    curl_close($ch);
// 						}

// 					}




// 									}

// 						}

// 				}

// 		}

// 	}
	
	public function update_customer_remarks(){
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('leads/view_detail');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_remarks";
		$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
			$data = array('lead_id'=>$this->uri->segment(3),
			'remarks'=>$this->input->post('remarks'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		
		$query = $this->db->select('id,lead_id')->from('progress_remarks')->where('lead_id',$this->uri->segment(3))->limit(1)->order_by('id','desc')->get();
		$res = $query->result();
		if($res){
		    
		    foreach($res as $followinfo);
		    $data1 = array('no_followup'=>1);
		    $this->db->where('id',$followinfo->id);
		    $this->db->update('progress_remarks',$data1);
		}
		
		
		if($result)
		{
			$this->session->set_flashdata('remarks_message','<div class="alert alert-danger" style="color:#fff;">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
		}
	}
	}
	
	public function customer_remarks_list()
	{
		
		$lead_data = array();
		$this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('customer_remarks a');
		$this->db->join('system_users b','a.added_by=b.user_id','left');
		$this->db->where('lead_id',$this->uri->segment(3));
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edit = "<a href='".page_url."Leads/edit_progress_report/".$row->lead_id."/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$lead_data[] = array('sr_no'=>$i,
			'remarks'=>$row->remarks,
			'added_on'=>$row->added_on,
			'name'=>$row->first_name." ".$row->last_name,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function remarks_list()
	{
		
		$lead_data = array();
		$this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('progress_remarks a');
		$this->db->join('system_users b','a.added_by=b.user_id','left');
		$this->db->where('lead_id',$this->uri->segment(3));
		$this->db->order_by('a.id','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$edit = "<a href='".page_url."Leads/edit_progress_report/".$row->lead_id."/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$lead_data[] = array('sr_no'=>$i,
			'next_follow_date'=>$row->next_follow_date,
			'remarks'=>$row->remarks,
			'added_on'=>$row->added_on,
			'name'=>$row->first_name." ".$row->last_name,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function upcoming_followups(){
		$this->load->view('leads/upcoming_followups');
	}
	
	public function followup_list()
	{
		$this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$this->db->select('a.*,a.id as customerid, b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date,e.lead_id,e.lead_name,e.color, c.remarks as progressremark, s.lead_name as lead_quality_status')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('lead_quality_status s','a.lead_quality_status=s.lead_id','left');
		$this->db->where('c.next_follow_date>=',$date);
		$this->db->group_by('c.lead_id');
		$this->db->order_by('a.create_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$query22 = $this->db->select('a.id,a.lead_id, a.member_id, b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->customerid)->get();
                $userdetail = $query22->result();
                if($query22->num_rows()>0){
					foreach($userdetail as $assigned_person);
					$name = $assigned_person->first_name." ".$assigned_person->last_name;
				}else{
					$name="";
				}
                
                     				
                        				
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$followup_date = $row->next_follow_date;
			
			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->progressremark,0,200);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks,
			'lead_quality'=>$lead_type,
			'lead_quality_status'=>$lead_quality_status,
			'unique_id'=>$row->unique_id,
			'followup'=>$followup_date,
			'assigned_to'=>$name,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function edit_progress_report(){
		$this->load->view('leads/edit_progress_report');
	}
	
	public function update_remarks_detail(){
	   $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('leads/edit_progress_report');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "progress_remarks";
		$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
			$data = array('lead_id'=>$this->uri->segment(4),
			'next_follow_date'=>$followup_date,
			'remarks'=>$this->input->post('remarks'),
			'remark_title'=>$this->input->post('remarks_title'),
			'update_on'=>$date,
			'updated_by'=>$user_id);
			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#fff;">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/view_detail/'.$this->uri->segment(4));
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Leads/view_detail/'.$this->uri->segment(4));
		}
	}	
		
	}
	
	public function lead_assign_to_team(){
		
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $leadid=$this->input->post('pick_items');
  $team_id=$this->input->post('teamleader');
 date_default_timezone_set("Asia/Kolkata");
  $added_time = date('Y-m-d H:i:s');
 for($i=0;$i<count($leadid);$i++)
 {
	$lead_id=$leadid[$i];
	
	$data=array('lead_id'=>$leadid[$i],
	'team_id'=>$team_id[$i],
	'added_by'=>$user_id,
	'added_on'=>$added_time);
	$this->db->insert('lead_assigned_to_team',$data);
}
$this->session->set_flashdata('message','<div class="alert alert-success" style="color:red; float-left:20px;">Thank you, Your record successfully added.</div><br/>');
  redirect(page_url.'Leads/');
	
}

public function filter_by_date_category(){
	
	$patient_type = $this->input->post('patient_type');
	if($patient_type==''){
		$patient_value = "NA";
	}else{
		$patient_value = $patient_type;
	}
	$fromdate = $this->input->post('from_date');
	if($fromdate==''){
		$from_date = "NA";
	}else{
		$from_date = $fromdate;
	}
	$todate = $this->input->post('to_date');
	if($todate==''){
		$to_date = "NA";
	}else{
		$to_date = $todate;
	}
	$data['patient_category'] = $patient_value;
	$data['start_date'] = $from_date;
	$data['end_date'] = $to_date;
	
	$this->load->view('leads/filtered_data',$data);
}

public function filtered_Lead_list()
	{
		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('states c','a.state=c.state_id','left');
		$this->db->join('cities d','a.city=d.city_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','a.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','a.lead_source_id=g.source_id','left');
		$patient_category = $this->uri->segment(3);
		$start_date = $this->uri->segment(4);
		$end_date = $this->uri->segment(5);
		if($patient_category=='NA'){}else{
			$this->db->where('a.patient_type_id',$patient_category);
		}
		if($start_date=='NA' && $end_date=='NA'){}else if($start_date=='NA' && $end_date!='NA'){
		    $this->db->where('a.create_date',$end_date);
		}elseif($start_date!='NA' && $end_date=='NA'){
		   $this->db->where('a.create_date',$start_date);  
		}else{
		$this->db->where("a.create_date BETWEEN '$start_date' AND '$end_date'");
		}
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			
			
		$query = $this->db->select(' team_id,team_name')->from('prestogroup_teams')->where('status','1')->order_by('team_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
				foreach($query->result() as $teamname)
				{
				$team .="<option value='".$teamname->team_id."'>".$teamname->team_name."</option>";
				}
		
			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name')->from('lead_assigned_to_team a')->join('prestogroup_teams b','a.team_id=b.team_id','left')->where('a.lead_id',$row->id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'status'=>$sta,
			'assign'=>$assignement,
			'edit'=>$edit."&nbsp; l &nbsp;  ".$view);
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

public function view_lead_via_team(){
			$this->load->view('leads/view_lead_via_team');
	}
	
		public function view_assigned_lead_report_by_team()
	{
		$lead_data = array();
		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source')->from('lead_assigned_to_team a');
		$this->db->join('leads l','a.lead_id=l.id','left');
		$this->db->join('countries b','l.country=b.country_id','left');
		$this->db->join('states c','l.state=c.state_id','left');
		$this->db->join('cities d','l.city=d.city_id','left');
		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
		$this->db->where('a.team_id',$this->uri->segment(3));
		$this->db->order_by('l.create_date','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$reassign_member = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."'><span class='btn btn-xs btn-danger'>Change Assignment</span></a>";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			
		$query = $this->db->select('a.lead_id, a.team_id, a.member_id, b.lead_id, b.team_id, c.user_id, c.first_name, c.last_name')->from('lead_assigned_to_team_member a')->join('lead_assigned_to_team b','a.lead_id=b.lead_id','left')->join('system_users c','a.member_id=c.user_id','left')->where('a.team_id',$this->uri->segment(3))->where('a.lead_id',$row->id)->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res);
		
	if($res){
	    foreach($res as $assignedinfo)
	    
	    $assignement = $assignedinfo->first_name." ".$assignedinfo->last_name."<br>".$reassign_member;
	}else{
	    	$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
		       
		       $qry = $this->db->select('a.manager_id, a.team_id, b.user_id, b.first_name, b.last_name')->from('manager_teams a')->join('system_users b','a.manager_id=b.user_id','left')->where('a.team_id',$this->uri->segment(3))->get();
		       foreach($qry->result() as $managerinfo);
		       
		      	$team .="<option value='".$managerinfo->user_id."'>".$managerinfo->first_name." ".$managerinfo->last_name."</option>";
		       
				foreach($query->result() as $teammember)
				{
				$team .="<option value='".$teammember->user_id."'>".$teammember->first_name." ".$teammember->last_name."</option>";
				}
		
			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->first_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
	}	
			
	
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'status'=>$sta,
			'assign'=>$assignement,
			'edit'=>$edit."&nbsp; l &nbsp;  ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	
	// public function View_leads_by_team()
	// {
	// 	$lead_data = array();
	// 	$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source, s.lead_name as lead_quality_status')->from('lead_assigned_to_team a');
	// 	$this->db->join('leads l','a.lead_id=l.id','left');
	// 	$this->db->join('countries b','l.country=b.country_id','left');
	// 	$this->db->join('states c','l.state=c.state_id','left');
	// 	$this->db->join('cities d','l.city=d.city_id','left');
	// 	$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
	// 	$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
	// 		$this->db->join('lead_quality_status s','l.lead_quality_status=s.lead_id','left');
	// 	$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
	// 	$this->db->where('a.team_id',$this->uri->segment(3));
	// 	$this->db->where('l.status','1');
	// 	$this->db->order_by('l.create_date','DESC');
	// 	$query = $this->db->get();
	// 	$res = $query->result();									
	// 	$i=1;
	// 	foreach($res as $row)
	// 	{
			
	// 		$lead_quality = $row->lead_name;
	// 		$color_lead = $row->color;
	// 		$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
	// 			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
	// 		$status = $row->status;
	// 		if($status=='1')
	// 		{
	// 			$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
	// 		}else
	// 		{
	// 			$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
	// 		}
	// 		$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
	// 		$reassign_member = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."'><span class='btn btn-xs btn-danger'>Change Assignment</span></a>";
	// 		$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
	// 		$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
	// 		$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
	// 		$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
	// 		$gender = $row->gender; 
	// 		if($gender=='1'){$gnd = "Male";
	// 		$tt = "Mr. ";
	// 		}else{$gnd = "Female";
	// 		$tt = "Ms. ";
	// 		}
			
	// 	$query = $this->db->select('a.lead_id, a.team_id, a.member_id, b.lead_id, b.team_id, c.user_id, c.first_name, c.last_name')->from('lead_assigned_to_team_member a')->join('lead_assigned_to_team b','a.lead_id=b.lead_id','left')->join('system_users c','a.member_id=c.user_id','left')->where('a.team_id',$this->uri->segment(3))->where('a.lead_id',$row->id)->get();
	// 	$res = $query->result();
	// 	//echo "<pre>"; print_r($res);
		
	// if($res){
	//     foreach($res as $assignedinfo)
	    
	//     $assignement = $assignedinfo->first_name." ".$assignedinfo->last_name."<br>".$reassign_member;
	// }else{
	//     	$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();
	// 	$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
	// 	$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
	// 	       $team .= "<option value=''>Select Team</option>";
		       
	// 	       $qry = $this->db->select('a.manager_id, a.team_id, b.user_id, b.first_name, b.last_name')->from('manager_teams a')->join('system_users b','a.manager_id=b.user_id','left')->where('a.team_id',$this->uri->segment(3))->get();
	// 	if($qry->num_rows()>0){
	// 	       foreach($qry->result() as $managerinfo);
		       
	// 	      	$team .="<option value='".$managerinfo->user_id."'>".$managerinfo->first_name." ".$managerinfo->last_name."</option>";
		       
	// 			foreach($query->result() as $teammember)
	// 			{
	// 			$team .="<option value='".$teammember->user_id."'>".$teammember->first_name." ".$teammember->last_name."</option>";
	// 			}
	// 	}
	// 		$team .="</select>";
	// 		$query = $this->db->select('a.id1,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
	// 		$res = $query->result();
	// 		if($res){
	// 			foreach($res as $assign_information)
	// 			{
	// 			$assignement = 	$assign_information->first_name;
	// 			}
				
	// 		}else{
	// 		$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
	// 		}
	// }	
			
	
	// 		//$remarks = substr($row->remarks,0,50);
	// 		$lead_data[] = array('sr_no'=>$i,
	// 		'create_date'=>$row->create_date,
	// 		'patient_type'=>$row->patient_type,
	// 		'lead_source'=>$row->lead_source,
	// 		'customer_name'=>$tt." ".$row->customer_name,
	// 		'gender'=>$gnd,
	// 		'email'=>$row->email,
	// 		'contact_no'=>$row->country_code."".$row->contact_no,
	// 		'skypeid'=>$skype,
	// 		'messangerid'=>$messanger,
	// 		'country'=>$row->country_name,
	// 		'lead_quality'=>$lead_type,
	// 		'lead_quality_status'=>$lead_quality_status,
	// 		'unique_id'=>$row->unique_id,
	// 		'status'=>$sta,
	// 		'assign'=>$assignement,
	// 		'edit'=>$edit."&nbsp; l &nbsp;  ".$view);
	// 		$i++;
	// 	}
	// 		$results = array(
	// 		"sEcho" => 1,
	// 		"iTotalRecords" => count($lead_data),
	// 		"iTotalDisplayRecords" => count($lead_data),
	// 		"aaData"=>$lead_data);
			
	// 	echo json_encode($results);
	// }


	public function View_leads_by_team() {

		$lead_data = array();

		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source, s.lead_name as lead_quality_status')->from('lead_assigned_to_team a');

		$this->db->join('leads l','a.lead_id=l.id','left');

		$this->db->join('countries b','l.country=b.country_id','left');

		$this->db->join('states c','l.state=c.state_id','left');

		$this->db->join('cities d','l.city=d.city_id','left');

		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');

		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');

		$this->db->join('lead_quality_status s','l.lead_quality_status=s.lead_id','left');

		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');

		$this->db->where('a.team_id',$this->uri->segment(3));

		$this->db->where('l.status','1');

		$this->db->order_by('l.create_date','DESC');

		$query = $this->db->get();

		$res = $query->result();                                                                                                                               

    $i=1;

    foreach($res as $row)

    {

                   
$assignement = "<span style='color:red; font-weight:bold'>No Member has been added in this Team yet.</span>";
                    $lead_quality = $row->lead_name;

                    $color_lead = $row->color;

                    $lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";

                                    $lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";

                    $status = $row->status;

                    if($status=='1')

                    {

                                    $sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";

                    }else

                    {

                                    $sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";

                    }

                    $leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";

                    $reassign_member = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."'><span class='btn btn-xs btn-danger'>Change Assignment</span></a>";

                    $edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

                    $view = "<a href='".page_url."Leads/view_detail/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

                    $skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";

                    $messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";

                                               
$query = $this->db->select('a.lead_id, a.team_id, a.member_id, b.lead_id, b.team_id, c.user_id, c.first_name, c.last_name')->from('lead_assigned_to_team_member a')->join('lead_assigned_to_team b','a.lead_id=b.lead_id','left')->join('system_users c','a.member_id=c.user_id','left')->where('a.team_id',$this->uri->segment(3))->where('a.lead_id',$row->id)->get();

                                $res = $query->result();

                               

                               

                if($query->num_rows()>0){

                    foreach($res as $assignedinfo)

                   

                    $assignement = $assignedinfo->first_name." ".$assignedinfo->last_name."<br>".$reassign_member;

                }else{

						//$teammembersid = array();
                                $query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();

					if($query->num_rows()>0){
                                $assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";

                                $team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";

                                       $team .= "<option value=''>Select Team</option>";

                                      

                                       $qry = $this->db->select('a.employee_id')->from('presto_team_members a')->where('a.team_id',$this->uri->segment(3))->get();
											
												
                                                   foreach($qry->result() as $teammembers){

                                                                   $teammembersid[]= $teammembers->employee_id;

                                                   }
												
												
                                                   //echo "<pre>"; print_r( $teammembersid); exit;

                                                   $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where_in('user_id',$teammembersid)->get();

                                                 

                                                if($q->num_rows()>0){

                                                                   foreach($q->result() as $managerinfo){

                                                                   $team .="<option value='".$managerinfo->user_id."'>".$managerinfo->first_name." ".$managerinfo->last_name."</option>";

                                                                   }

                                                }

                               

                                                $team .="</select>";

                                                $query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();

                                                $res = $query->result();

                                                if($res){

                                                                foreach($res as $assign_information)

                                                                {

                                                                $assignement =  $assign_information->first_name;

                                                                }

                                                               

                                                }else{

                                                $assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;

                                                }

                }     
				}

                                               

               

                                                //$remarks = substr($row->remarks,0,50);

                                                $lead_data[] = array('sr_no'=>$i,

                                                'create_date'=>date('d-m-Y', strtotime($row->create_date)),

                                                'patient_type'=>$row->patient_type,

                                                'lead_source'=>$row->lead_source,

                                                'customer_name'=>$row->customer_name,

                                                'email'=>$row->email,

                                                'contact_no'=>$row->country_code."".$row->contact_no,

                                                'skypeid'=>$skype,

                                                'messangerid'=>$messanger,

                                                'country'=>$row->country_name,

                                                'lead_quality'=>$lead_type,

                                                'lead_quality_status'=>$lead_quality_status,

                                                'unique_id'=>$row->unique_id,

                                                'status'=>$sta,

                                                'assign'=>$assignement,

                                                'edit'=>$edit,

                                                'followup' => $view
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

public function mail_history(){
	
	$this->load->view('leads/mail_history');
}
public function mail_history_list(){
	
	$this->db->select('a.*,b.customer_name, c.user_id, c.first_name, c.last_name');
	$this->db->from('mail_data a');
	$this->db->join('leads b','b.id=a.sender_id');
	$this->db->join('system_users c','a.added_by=c.user_id','left');
	$this->db->order_by('a.added_on','desc');
	$mail=$this->db->get();
	$mail_data=array();
	$i=1;
	
	foreach($mail->result() as $mailed){
		$fill="";
	$j=1;
		$fls=$this->db->select('*')->from('mail_files')->where('file_id',$mailed->data_id)->get();
		$res = $fls->result();
		if($res){
		foreach($fls->result() as $fun){
			$fill.="<a href='".mail_file.$fun->files."' download><img src='".mail_file.$fun->files."' height='50px' style='margin:2px'></a>"."<br>";
			
			$j++;
		}
		}
		else{
		    $fill="";
		}
		$remark = "<span class='btn-sm btn-success' data-toggle='modal' onclick='return getid(".$mailed->data_id.")' data-target='#myModal' >Add Remark</span>";
		$viwremark = "<a href='".page_url."Leads/mail_remarks/".$mailed->data_id."'><span class='btn-sm btn-success' >View Remark</span></a>";
		$mail_data[] = array('sr_no'=>$i,
			'Send_to'=>$mailed->customer_name,
			'Sender_email'=>$mailed->sender,
			'subject'=>$mailed->subject,
			'content'=>$mailed->content,
			'file'=>$fill,
			'remark'=>$remark.' '.$viwremark,
			'added_by'=>$mailed->first_name." ".$mailed->last_name,
			'date'=>$mailed->added_on);
			$i++;
	}
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($mail_data),
			"iTotalDisplayRecords" => count($mail_data),
			"aaData"=>$mail_data);
			
		echo json_encode($results);
	
}	

public function customer_mail_history_list(){
	$id = $this->uri->segment(3);
	$this->db->select('a.*,b.customer_name');
	$this->db->from('mail_data a');
	$this->db->join('leads b','b.id=a.sender_id');
	$this->db->where('a.sender_id',$id);
	$this->db->order_by('added_on','desc');
	$mail=$this->db->get();
	$mail_data=array();
	$i=1;
	
	foreach($mail->result() as $mailed){
		$fill="";
	$j=1;
		$fls=$this->db->select('*')->from('mail_files')->where('file_id',$mailed->data_id)->get();
		foreach($fls->result() as $fun){
			$fill.="<a href='".mail_file.$fun->files."' download><img src='".mail_file.$fun->files."' height='50px' style='margin:2px'></a>"."<br>";
			
			$j++;
		}
		$remark = "<span class='btn-sm btn-success' data-toggle='modal' onclick='return getid(".$mailed->data_id.")' data-target='#myModal' >Add Remark</span>";
		$viwremark = "<a href='".page_url."Leads/mail_remarks/".$mailed->data_id."'><span class='btn-sm btn-success' >View Remark</span></a>";
		$mail_data[] = array('sr_no'=>$i,
			'Send_to'=>$mailed->customer_name,
			'Sender_email'=>$mailed->sender,
			'subject'=>$mailed->subject,
			'content'=>$mailed->content,
			'file'=>$fill,
			'remark'=>$remark.' '.$viwremark,
			'date'=>$mailed->added_on);
			$i++;
	}
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($mail_data),
			"iTotalDisplayRecords" => count($mail_data),
			"aaData"=>$mail_data);
			
		echo json_encode($results);
	
}	


public function mail_reply(){
	
	$table="mail_reply";
	date_default_timezone_set("Asia/Kolkata");
	$data=array(
	
	'mail_id'=>$this->input->post('mailid'),
	'remarks'=>$this->input->post('remark'),
	'addedon'=>date('Y-m-d h:i:s'),
	'addedby'=>$this->session->userdata['logged_in']['user_id']
	
	);
	//echo "<pre>";print_r($data);exit;
	$this->db->insert($table,$data);
	redirect(page_url.'Leads/mail_history');
}

public function mail_remarks(){
	
	$this->load->view('leads/mail_remarks');
}
public function mail_reply_data(){
	
	$this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.data_id, c.subject, c.sender');
	$this->db->from('mail_reply a');
	$this->db->join('system_users b','a.addedby=b.user_id','left');
	$this->db->join('mail_data c','a.mail_id=c.data_id','left');
	$this->db->where('a.mail_id',$this->uri->segment(3));
	
	$rpl=$this->db->get();
	$mail_remarks=array();
	$i=1;
	
	foreach($rpl->result() as $reply){
		
		$mail_remarks[] = array('sr_no'=>$i,
			'mail_id'=>$reply->sender,
			'subject'=>$reply->subject,
			'remarks'=>$reply->remarks,
			'addedon'=>$reply->addedon,
			'addedby'=>$reply->first_name." ".$reply->last_name
			);
			$i++;
	}
	
	
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($mail_remarks),
			"iTotalDisplayRecords" => count($mail_remarks),
			"aaData"=>$mail_remarks);
			
		echo json_encode($results);
}	
public function lead_assign_to_team_member(){
		
  $user_id =$this->session->userdata['logged_in']['user_id'];
  $leadid=$this->input->post('pick_items');
  $team_id=$this->input->post('teamleader');
 date_default_timezone_set("Asia/Kolkata");
  $added_time = date('Y-m-d H:i:s');
 for($i=0;$i<count($leadid);$i++)
 {
	$lead_id=$leadid[$i];
	$team = $this->uri->segment(3);
	$data=array('lead_id'=>$leadid[$i],
	'member_id'=>$team_id[$i],
	'team_id'=>$team,
	'added_by'=>$user_id,
	'added_on'=>$added_time);
	$this->db->insert('lead_assigned_to_team_member',$data);

	$datas = array(
				'lead_id' => $leadid[$i],
				'lead_status' => 1,
				'next_follow_date' => '0000-00-00'
				);

	$this->db->insert('progress_remarks',$datas);
}

$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
  redirect(page_url.'Leads/view_lead_via_team/'.$this->uri->segment(3));
	
}
public function filter_teamdata_by_date_category(){
	
	$patient_type = $this->input->post('patient_type');
	if($patient_type==''){
		$patient_value = "NA";
	}else{
		$patient_value = $patient_type;
	}
	$fromdate = $this->input->post('from_date');
	if($fromdate==''){
		$from_date = "NA";
	}else{
		$from_date = $fromdate;
	}
	$todate = $this->input->post('to_date');
	if($todate==''){
		$to_date = "NA";
	}else{
		$to_date = $todate;
	}
	$data['patient_category'] = $patient_value;
	$data['start_date'] = $from_date;
	$data['end_date'] = $to_date;
	
	$this->load->view('leads/filter_lead_data_by_team',$data);
}

public function filtered_Lead_list_by_team()
	{
		$lead_data = array();
		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source')->from('lead_assigned_to_team a');
		$this->db->join('leads l','a.lead_id=l.id','left');
		$this->db->join('countries b','l.country=b.country_id','left');
		$this->db->join('states c','l.state=c.state_id','left');
		$this->db->join('cities d','l.city=d.city_id','left');
		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
		$patient_category = $this->uri->segment(3);
		$start_date = $this->uri->segment(4);
		$end_date = $this->uri->segment(5);
		$team_id = $this->uri->segment(6);
		
		$this->db->where('a.team_id',$team_id);
		
		
		if($start_date=='NA' && $end_date=='NA'){}else if($start_date=='NA' && $end_date!='NA'){
		    $this->db->where('l.create_date',$end_date);
		}elseif($start_date!='NA' && $end_date=='NA'){
		   $this->db->where('l.create_date',$start_date);  
		}else{
		$this->db->where("l.create_date BETWEEN '$start_date' AND '$end_date'");
		}
		
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			
			
		$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(6))->order_by('b.first_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
				foreach($query->result() as $teammember)
				{
				$team .="<option value='".$teammember->user_id."'>".$teammember->first_name."</option>";
				}
		
			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->first_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'status'=>$sta,
			'unique_id'=>$row->unique_id,
			'assign'=>$assignement,
			'edit'=>$edit."&nbsp; l &nbsp;  ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

public function view_your_leads()
	{
		$user_id=$_SESSION['logged_in']['user_id'];

		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source,s.lead_id, s.lead_name as lead_quality_status')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('lead_quality_status s','a.lead_quality_status=s.lead_id','left');
		$this->db->join('patient_type f','a.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','a.lead_source_id=g.source_id','left');
		$this->db->join('lead_assigned_to_team_member h','a.id=h.lead_id');
		$this->db->where('h.member_id',$user_id);
		$this->db->where('a.status','1');
		$this->db->order_by('a.added_on','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{

			$getLeadStatus = $this->salescrm->getLeadStatus($row->id);
			if(($getLeadStatus) > 0) {
			foreach ($getLeadStatus as $status);
				$currentleadstatus = $status->lead_name;
				$currentleadremarks = $status->remarks;
			} else {
				$currentleadstatus = '';
				$currentleadremarks = '';
			}


			$getLeaddata = $this->salescrm->getLeadStatusbyidandremarks($row->id);
			if(($getLeaddata) > 0) {
			foreach ($getLeaddata as $leadstatus);
				$laststatus = $leadstatus->lead_stage;
				$lastrmk=$leadstatus->remarks;
			} else {
				$laststatus = '';
				$lastrmk='';
			}
			

			if($laststatus<>12 && $laststatus<>3)
			{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
						
				
		$query = $this->db->select(' team_id,team_name')->from('prestogroup_teams')->where('status','1')->order_by('team_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
		       
				foreach($query->result() as $teamname)
				{
				$team .="<option value='".$teamname->team_id."'>".$teamname->team_name."</option>";
				}
		
			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->id)
							  ->where('c.member_id',$user_id)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->create_date))."<br>".$row->unique_id,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'company_name'=>$row->company_name,
			'customer_details'=>$row->customer_name."<br>".$row->country_code."-".$row->contact_no."<br>".$row->email_id."<br>".$row->country_name,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'state'=>$row->state,
			'city'=>$row->city,
			'lead_quality_status'=>$currentleadstatus,
			'lead_remarks'=>$row->remarks,
			'remarks'=>$currentleadremarks,
			'assign'=>$assignement."<br>".$fname." ".$lname,
			'followup' => $view
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
	
	
	function leadapi()
	{
	    echo "<pre>"; print_r($_POST);exit;
	    
	}
	
	
	public function view_shared_leads()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];
		//echo $user_id; exit;
		$lead_data = array();
		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source, s.*, l.id as leadinfo_id, l.status as leadstatus,se.lead_id, se.lead_name as lead_quality_status')->from('lead_assigned_to_team_member a');
		$this->db->join('leads l','a.lead_id=l.id','left');
		$this->db->join('countries b','l.country=b.country_id','left');
		$this->db->join('states c','l.state=c.state_id','left');
		$this->db->join('cities d','l.city=d.city_id','left');
		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
			$this->db->join('lead_quality_status se','l.lead_quality_status=se.lead_id','left');
		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
		$this->db->join('customer_share_with s','a.lead_id=s.customer','left');
    	$this->db->where('s.shared_wid',$user_id);
    		//$this->db->where('l.lead_quality',0);
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{

			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$status = $row->leadstatus;
			if($status=='1')
			{
				$sta =  "<span class='btn btn-success btn-xs'>Active</span>";
			}else
			{
				$sta =  "<span class='btn btn-danger btn-xs'>Not Active</span>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->leadinfo_id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->leadinfo_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->leadinfo_id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
			$gender = $row->gender;
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}


		$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
				foreach($query->result() as $teammember)
				{
				$team .="<option value='".$teammember->user_id."'>".$teammember->first_name."</option>";
				}

			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->first_name;
				}

			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->contact_no,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'lead_quality_status'=>$lead_quality_status,
			'unique_id'=>$row->unique_id,
			'status'=>$sta,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		echo json_encode($results);
	}
public function add_social()
	{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];
	    $data = array(
        'customer_id' =>$this->uri->segment(3),
		'whatsapp' =>$this->input->post('whatsapp'),
		'skype' =>$this->input->post('skype'),
		'telegram' =>$this->input->post('telegram'),
		'line_id' =>$this->input->post('line_id'),
		'fb_msg' =>$this->input->post('fb_msg'),
		'google_hangout' =>$this->input->post('google_hangout'),
		'imo' =>$this->input->post('imo'),
		'viber_id' =>$this->input->post('viber_id'),
		'added_by' =>$user_id,
		'added_on' =>$date,
		'wechat' =>$this->input->post('wechat'));

   $this->db->insert('client_social_profiles', $data);
   $this->session->set_flashdata('social_message','<div class="alert alert-info">Thank you, record successfully added.</div>');
   redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
}

public function update_social()
	{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];
	    $data = array(
        'customer_id' =>$this->uri->segment(3),
		'whatsapp' =>$this->input->post('whatsapp'),
		'skype' =>$this->input->post('skype'),
		'telegram' =>$this->input->post('telegram'),
		'line_id' =>$this->input->post('line_id'),
		'fb_msg' =>$this->input->post('fb_msg'),
		'google_hangout' =>$this->input->post('google_hangout'),
		'imo' =>$this->input->post('imo'),
		'viber_id' =>$this->input->post('viber_id'),
		'added_by' =>$user_id,
		'added_on' =>$date,
		'wechat' =>$this->input->post('wechat'));
$this->db->where('id',$this->uri->segment(4));
   $this->db->update('client_social_profiles', $data);
   $this->session->set_flashdata('social_message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
   redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
}	


public function not_active(){
    $this->load->view('leads/not_active_leads');
}
public function Not_active_leads()
	{
		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','a.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','a.lead_source_id=g.source_id','left');
		$this->db->where('a.status','0');
		$this->db->order_by('a.create_date','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
				$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'unique_id'=>$row->unique_id,
			'status'=>$sta,
			'remarks'=>$row->remarks,
			'edit'=>$edit." l ".$view);
			$i++;
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
		public function My_followups(){
		$this->load->view('leads/my_followups');
	}
	
	public function my_followup_list()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];	
		$this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$this->db->select('a.*,b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date,e.lead_id,e.lead_name,e.color, a.id as customer_id')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->where('c.next_follow_date>=',$date);
		$this->db->where('c.added_by',$user_id);
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$followup_date = $row->next_follow_date;
			
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->customer_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customer_id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks."..",
			'lead_quality'=>$lead_type,
			'unique_id'=>$row->unique_id,
			'followup'=>$followup_date,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function customer_call(){
	    
	  // $caller="9873552525";
	   $called = base64_decode($this->uri->segment(3));
	   $user_id =$this->session->userdata['logged_in']['user_id'];
	   $user=$this->uri->segment(4);
	   $lead_id =$this->uri->segment(5);
	   $querty=$this->db->select('*')->from('assign_caller_id')->where('user_id',$user)->get();
	   if($querty->num_rows()>0)
	   {
	   foreach($querty->result() as $callerdata);
	   $callerext=$callerdata->caller_number;
	   $callerpass=$callerdata->password;
	   }else
	   {
	   echo "NO CALLER ID ASSIGNED";exit;
	   }
	   
$curl = curl_init();

//echo  "http://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=c2cliza&pwd=".$callerpass; exit;
$random = mt_rand(10, 1001);
date_default_timezone_set('Asia/Kolkata');
$datatime = date('Ymdhis'); 
$flag = $random.$datatime;

//echo "http://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=techmet&pwd=".$callerpass."/".$flag; exit;
curl_setopt_array($curl, array(
//  CURLOPT_URL => "http://ivr.virtuo.in/c2c.php?user=c2cdemo&pwd=man12man&caller=".$caller."&called=".$called,
CURLOPT_URL => "https://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=techmet&pwd=".$callerpass."&uid=".$flag,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
$success =  json_decode($response,true);
//echo "<pre>"; print_r($response);exit;

    
    $calldate = date('Y-m-d h:i:s');
    $calltime = date('h:i:s');
    $data = array('lead_id'=>$lead_id,
    'called_by'=>$user_id,
    'unique_id'=>$flag,
    'call_date'=>$calldate,
    'start_time'=>'');
    
    $this->db->insert('call_duration_capture',$data);
    
curl_close($curl);

echo "<h1>Thank You, you will be connected with the customer soon..... </h1>";
	}
	
	public function edit_lead_by_member(){
		$this->load->view('leads/edit_lead_by_member');
	}	
	
	public function update_lead_information_by_member()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('leads/edit_lead_by_member');
		}
		else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "leads";
		$create_date = date('Y-m-d',strtotime($this->input->post('create_date')));
			$data = array('gender'=>$this->input->post('gender'),
			'patient_type_id'=>$this->input->post('patient_type'),
			'customer_name'=>$this->input->post('cust_name'),
			'email'=>$this->input->post('email'),
            'country_code'=>$this->input->post('country_code'),
			'contact_no'=>$this->input->post('mobile'),
			'country'=>$this->input->post('country_name'),
			'message'=>$this->input->post('message1'),
			'remarks'=>$this->input->post('spacification'),
			'lead_quality'=>$this->input->post('lead_quality'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);


			$this->db->where('id',$this->uri->segment(3));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Assigned_lead/');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Assigned_lead');
		}
		
	}
	}
	
	
		public function missed_followups(){
		$this->load->view('leads/followup_missed');
	}
	
	public function missed_followup_list()
	{   
	    
	    $this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$query = $this->db->select('a.id, a.lead_id, b.id, b.lead_quality, a.no_followup,a.next_follow_date')->from('progress_remarks a')->join('leads b','a.lead_id=b.id','left')->where('b.lead_quality!=',5)->where('a.no_followup=',0)->where('a.next_follow_date<',date('Y-m-d'))->group_by('a.lead_id')->get();
                                      $res = $query->result();
                                      if($res){
                                          $i=1;
                                          foreach($res as $lead_detail)
                                          {
                                              
                                        $this->db->select('a.id, a.lead_id, a.next_follow_date, l.*,l.id as customerid, b.country_id,b.country_name,e.lead_id,e.lead_name,e.color,s.lead_id, s.lead_name as lead_quality_status')->from('progress_remarks a');
                                        $this->db->join('leads l','a.lead_id=l.id','left');
                                        $this->db->join('countries b','l.country=b.country_id','left');
                                        $this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
                                        $this->db->join('lead_quality_status s','l.lead_quality_status=s.lead_id','left');
                                        $query = $this->db->where('l.status','1')->where('a.lead_id',$lead_detail->lead_id)->where('a.next_follow_date<',date('Y-m-d'))->order_by('a.id','desc')->limit(1)->get();
                                        $result = $query->result(); 
                                       if($result){
                                     foreach($result as $row);
                                    $lead_quality = $row->lead_name;
                        			$color_lead = $row->color;
                        			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
                        			
                        			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
                        			$followup_date = $row->next_follow_date;
                        			
                        			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
                        			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
                        		
                        			$gender = $row->gender; 
                        			if($gender=='1'){$gnd = "Male";
                        			$tt = "Mr. ";
                        			}else{$gnd = "Female";
                        			$tt = "Ms. ";
                        			}
                        			$remarks = substr($row->remarks,0,50);
                        			
                        			$query = $this->db->select('lead_id, next_follow_date')->from('progress_remarks')->where('lead_id',$lead_detail->lead_id)->order_by('next_follow_date','desc')->limit(1)->get();
                        			
                        			$res = $query->result();
                        			foreach($res as $scheduled_date);
                        			if($followup_date<$scheduled_date->next_follow_date){
                        			    $next_schedule_followup = $scheduled_date->next_follow_date;
                        			}else{
                        			    $next_schedule_followup = "Pending";
                        			}
                                       
                        			$query22 = $this->db->select('a.id,a.lead_id, a.member_id, b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$lead_detail->lead_id)->get();
                        				$userdetail = $query22->result();
                        				foreach($userdetail as $assigned_person)
                        			
                        			$lead_data[] = array('sr_no'=>$i,
                        			'create_date'=>$row->create_date,
                        			'customer_name'=>$tt." ".$row->customer_name,
                        			'gender'=>$gnd,
                        			'email'=>$row->email,
                        			'contact_no'=>$row->country_code."".$row->contact_no,
                        			'country'=>$row->country_name,
                        			'remarks'=>$remarks,
                        			'lead_quality'=>$lead_type,
                        			'lead_quality_status'=>$lead_quality_status,
                        			'followup'=>$followup_date,
                        			'scheduled_followup'=>$next_schedule_followup,
                        			'edit'=>$view,
                        			'assignedto'=>$assigned_person->first_name." ".$assigned_person->last_name);
                                     
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
	
	public function user_missed_followups(){
		$this->load->view('leads/user_missed_followup');
	}
	
	public function view_your_missed_followup()
	{
	    $user_id =$this->session->userdata['logged_in']['user_id'];
		$this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$this->db->select('a.*,a.id as customerid, b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date,e.lead_id,e.lead_name,e.color, d.lead_id, d.member_id')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('lead_assigned_to_team_member d','a.id=d.lead_id','left');
		$this->db->where('d.member_id',$user_id);
		$this->db->where('c.next_follow_date<',$date);
		$this->db->where('a.lead_quality!=',5);
		$this->db->where('a.status','1')->group_by('c.lead_id')->order_by('c.next_follow_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$followup_date = $row->next_follow_date;
			
			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->remarks,0,50);
			$query = $this->db->select('lead_id, next_follow_date')->from('progress_remarks')->where('lead_id',$row->lead_id)->order_by('next_follow_date','desc')->limit(1)->get();
                        			
                        			$res = $query->result();
                        			foreach($res as $scheduled_date);
                        			if($followup_date<$scheduled_date->next_follow_date){
                        			    $next_schedule_followup = $scheduled_date->next_follow_date;
                        			}else{
                        			    $next_schedule_followup = "Pending";
                        			}
                        			
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks,
			'lead_quality'=>$lead_type,
			'followup'=>$followup_date,
			'scheduled_followup'=>$next_schedule_followup,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function missed_by_member()
	{   
	    
	    $this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];
		
		 $query = $this->db->select('a.id, a.lead_id,b.id, b.lead_quality, a.no_followup, a.next_follow_date')->from('progress_remarks a')->join('leads b','a.lead_id=b.id','left')->where('b.lead_quality!=',5)->where('a.no_followup=',0)->where('a.next_follow_date<',date('Y-m-d'))->group_by('a.lead_id')->get();
                                      $res = $query->result();
                                      if($res){
                                          $i=1;
                                          foreach($res as $lead_detail)
                                          {
                                              
                                        $this->db->select('a.id, a.lead_id, a.next_follow_date, l.*,l.id as customerid, b.country_id,b.country_name,e.lead_id,e.lead_name,e.color, f.lead_id, f.member_id')->from('progress_remarks a');
                                        $this->db->join('leads l','a.lead_id=l.id','left');
                                        $this->db->join('countries b','l.country=b.country_id','left');
                                        $this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
                                    $this->db->join('lead_assigned_to_team_member f','a.lead_id=f.lead_id','left');
                                        $query = $this->db->where('a.lead_id',$lead_detail->lead_id)->where('f.member_id',$user_id)->where('a.next_follow_date<',date('Y-m-d'))->order_by('a.id','desc')->limit(1)->get();
                                        $result = $query->result(); 
                                       // echo "<pre>"; print_r($result); exit;
                                        if($result){
                                     foreach($result as $row);
                                    
                                    $lead_quality = $row->lead_name;
                        			$color_lead = $row->color;
                        			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
                        			$followup_date = $row->next_follow_date;
                        			
                        			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
                        			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
                        		
                        			$gender = $row->gender; 
                        			if($gender=='1'){$gnd = "Male";
                        			$tt = "Mr. ";
                        			}else{$gnd = "Female";
                        			$tt = "Ms. ";
                        			}
                        			$remarks = substr($row->remarks,0,50);
                        			
                        			$query = $this->db->select('lead_id, next_follow_date')->from('progress_remarks')->where('lead_id',$lead_detail->lead_id)->order_by('next_follow_date','desc')->limit(1)->get();
                        			
                        			$res = $query->result();
                        			foreach($res as $scheduled_date);
                        			if($followup_date<$scheduled_date->next_follow_date){
                        			    $next_schedule_followup = $scheduled_date->next_follow_date;
                        			}else{
                        			    $next_schedule_followup = "Pending";
                        			}
                        			
                        			
                        			$lead_data[] = array('sr_no'=>$i,
                        			'create_date'=>$row->create_date,
                        			'customer_name'=>$tt." ".$row->customer_name,
                        			'gender'=>$gnd,
                        			'email'=>$row->email,
                        			'contact_no'=>$row->country_code."".$row->contact_no,
                        			'country'=>$row->country_name,
                        			'remarks'=>$remarks,
                        			'lead_quality'=>$lead_type,
                        			'followup'=>$followup_date,
                        			'scheduled_followup'=>$next_schedule_followup,
                        			'edit'=>$view);
                                     
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
	
	
public function send_email_to_customer(){
    
    $this->load->view('leads/sending_mail');
}

public function sendmail()
{

	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		
		$this->form_validation->set_rules('sender', 'Sender', 'required|trim');
		$this->form_validation->set_rules('subject', 'Subject', 'required|trim');
		$this->form_validation->set_rules('content', 'Content', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('leads/sending_mail');
		}
		else
		{

		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$data = array(
		'sender_id' =>$this->input->post('sender_id'),
		'sender' =>$this->input->post('sender'),
		'subject' =>$this->input->post('subject'),
		'content' =>$this->input->post('content'),
		'added_on' =>$date,
		'added_by' =>$user_id,

		);
		//echo "<pre>";print_r($data);exit;
		$array_data = array();
		$this->db->insert('mail_data', $data);
		$lid=$this->db->insert_id();
		$imgfile=$_FILES['content_attachment']['name'];
			if($imgfile){
			for($i=0;$i<count($imgfile);$i++)
			{

				$meafile=$imgfile[$i];
				$meafile1=explode('.',$meafile);
				$extend=end($meafile1);
				$newdocument=time().$i.'.'.$extend;
				move_uploaded_file($_FILES['content_attachment']['tmp_name'][$i],$_SERVER['DOCUMENT_ROOT'].'/image_bank/file/'.$newdocument);
				date_default_timezone_set("Asia/Kolkata");
				$date = date('Y-m-d H:i:s');
		  $array_data[] = $_SERVER['DOCUMENT_ROOT'].'/image_bank/file/'.$newdocument;
	      $data1 = array(
				'file_id'=>$lid,
				'files'=>$newdocument,
				'added_on '=>$date);
				$this->db->insert('mail_files',$data1);
		     }
			}

	$sento=$this->input->post('sender');
	$unique_id = $this->input->post('unique_id');
	$content.="With reference to your ID - ".$unique_id."<br><br>";
	$subject=$this->input->post('subject');
    $content.=$this->input->post('content');
	$attach=$_SERVER['DOCUMENT_ROOT'].'/image_bank/file/'. $newdocument;


$query = $this->db->select('user_id, email')->from('system_users')->where('user_id',$user_id)->get();
foreach($query->result() as $useremail);

    $this->email->set_mailtype("html");

	$this->email->to($sento);

	$this->email->bcc('manglesh@gamavis.com');
	$this->email->from('donotreply@hongyijig.com');
	$this->email->subject($subject);
	$this->email->message($content);
	for($i=0;$i<count($array_data);$i++){
	$this->email->attach($array_data[$i]);
	}
	if($this->email->send())
	{
	  $this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#fff">Thank You! Your email successfully sent.</div><br/>');
 redirect(page_url.'Leads/send_email_to_customer/'.$this->uri->segment(3));
	}
	}
}


	public function todays_progress(){
		$this->load->view('leads/todays_progress_report');
	}
	
	public function todays_progress_report()
	{
		$this->db->distinct();
		$date = date('Y-m-d');
		$startdate = date('Y-m-d');
		$enddate = date('Y-m-d');
		$lead_data = array();
		$this->db->select('a.*,a.id as customerid, b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date, c.remarks as progress_remark, e.lead_id,e.lead_name,e.color')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->where("c.added_on BETWEEN '$startdate 00:00:00' AND '$enddate 23:59:59'");
		$this->db->group_by('c.lead_id');
		$this->db->order_by('a.create_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$followup_date = $row->next_follow_date;
			
			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->progress_remark,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks,
			'lead_quality'=>$lead_type,
			'followup'=>$followup_date,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
		public function your_todays_progress(){
		$this->load->view('leads/your_todays_progress_report');
	}
	
	public function your_todays_progress_report()
	{
		$this->db->distinct();
		$date = date('Y-m-d');
		$startdate = date('Y-m-d');
		$enddate = date('Y-m-d');
		$lead_data = array();
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$this->db->select('a.*,a.id as customerid, b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date, c.remarks as progress_remark, e.lead_id,e.lead_name,e.color')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->where("c.added_on BETWEEN '$startdate 00:00:00' AND '$enddate 23:59:59'");
		$this->db->where('c.added_by',$user_id);
		$this->db->group_by('c.lead_id');
		$this->db->order_by('a.create_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$followup_date = $row->next_follow_date;
			
			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->progress_remark,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks,
			'lead_quality'=>$lead_type,
			'followup'=>$followup_date,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function mail_data()
{
	$this->load->view('leads/mail_data');
}

public function mail_list()
{

$lead_data = array();
	$this->db->select('*');
	$this->db->from('mail_data');
	$query=$this->db->get();
    $res = $query->result();
	$i=1;


	foreach($res as $row)
	{
		$rat=$this->db->select('*')->from('files')->where('file_id',$row->data_id)->get();
		$img=array();
		foreach($rat->result() as $result){
		$img[] = array('image'=>$result->files);
		}
	
		$lead_data[] = array('sr_no'=>$i,
		'sender'=>$row->sender,
		'subject'=>$row->subject,
		'content'=>$row->content,
		'files'=>$img,
		'added_on'=>$row->added_on,
		'edit'=>'1');
		$i++;
	}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($lead_data),
		"iTotalDisplayRecords" => count($lead_data),
		"aaData"=>$lead_data);

	echo json_encode($results);
}

public function updatequality(){
    $leadquality = $this->input->post('leadquality');
    $leadqualitystatus = $this->input->post('leadqualitystatus');
    $data = array('lead_quality'=>$leadquality,'lead_quality_status'=>$leadqualitystatus);
    $this->db->where('id',$this->uri->segment(3));
    $res = $this->db->update('leads',$data);
    if($res){
         $this->session->set_flashdata('updatemessage','<div class="alert alert-danger" style="color:#fff">Thank You! Lead Quality Successfully changed.</div><br/>');
 redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
    }
    
}

public function updatetreatment(){
    $treatment_type = $this->input->post('treatment_type');
    $data = array('patient_type_id'=>$treatment_type);
    $this->db->where('id',$this->uri->segment(3));
    $res = $this->db->update('leads',$data);
    if($res){
         $this->session->set_flashdata('updatemessage','<div class="alert alert-danger" style="color:#fff">Thank You! Treatment Type Successfully changed.</div><br/>');
 redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
    }
    
}

public function mailed_history(){
	
	$this->load->view('leads/your_mail_history');
}
public function mailed_history_list(){
$user_id =$this->session->userdata['logged_in']['user_id'];	
	$this->db->select('a.*,b.customer_name');
	$this->db->from('mail_data a');
	$this->db->join('leads b','b.id=a.sender_id');
	$this->db->where('a.added_by',$user_id);
	$mail=$this->db->get();
	$mail_data=array();
	$i=1;
	
	foreach($mail->result() as $mailed){
		$fill="";
	$j=1;
		$fls=$this->db->select('*')->from('mail_files')->where('file_id',$mailed->data_id)->get();
		foreach($fls->result() as $fun){
			$fill.="<a href='".mail_file.$fun->files."' download><img src='".mail_file.$fun->files."' height='50px' style='margin:2px'></a>"."<br>";
			
			$j++;
		}
		
		$viwremark = "<a href='".page_url."Leads/mailed_remarks/".$mailed->data_id."'><span class='btn-sm btn-success' >View Remark</span></a>";
		$mail_data[] = array('sr_no'=>$i,
			'Send_to'=>$mailed->customer_name,
			'subject'=>$mailed->subject,
			'content'=>$mailed->content,
			'file'=>$fill,
			'remark'=>$viwremark,
			'date'=>$mailed->added_on);
			$i++;
	}
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($mail_data),
			"iTotalDisplayRecords" => count($mail_data),
			"aaData"=>$mail_data);
			
		echo json_encode($results);
	
}	
public function mailed_reply(){
	
	$table="mail_reply";
	date_default_timezone_set("Asia/Kolkata");
	$data=array(
	
	'mail_id'=>$this->input->post('mailid'),
	'remarks'=>$this->input->post('remark'),
	'addedon'=>date('Y-m-d h:i:s'),
	'addedby'=>$this->session->userdata['logged_in']['user_id']
	
	);
	//echo "<pre>";print_r($data);exit;
	$this->db->insert($table,$data);
	redirect(page_url.'Leads/mail_history');
}

public function mailed_remarks(){
	
	$this->load->view('leads/your_email_remarks');
}
public function mailed_reply_data(){
	
	$this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.data_id, c.subject, c.sender');
	$this->db->from('mail_reply a');
	$this->db->join('system_users b','a.addedby=b.user_id','left');
	$this->db->join('mail_data c','a.mail_id=c.data_id','left');
	$this->db->where('a.mail_id',$this->uri->segment(3));
	
	$rpl=$this->db->get();
	$mail_remarks=array();
	$i=1;
	
	foreach($rpl->result() as $reply){
		
		$mail_remarks[] = array('sr_no'=>$i,
			'subject'=>$reply->subject,
			'remarks'=>$reply->remarks,
			'addedon'=>$reply->addedon,
			'addedby'=>$reply->first_name." ".$reply->last_name
			);
			$i++;
	}
	
	
	$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($mail_remarks),
			"iTotalDisplayRecords" => count($mail_remarks),
			"aaData"=>$mail_remarks);
			
		echo json_encode($results);
}	


public function call_history_list()
{
date_default_timezone_set('Asia/Kolkata');
$lead_data = array();
	$this->db->select('a.*,b.user_id, b.first_name, b.last_name');
	$this->db->from('call_duration_capture a');
	$this->db->join('system_users b','a.called_by=b.user_id','left');
	$this->db->where('a.lead_id',$this->uri->segment(3));
	$this->db->where_not_in('a.start_time','00:00:00');
	$this->db->order_by('id','desc');
	$query=$this->db->get();
    $res = $query->result();
	$i=1;


	foreach($res as $row)
	{
		

		$lead_data[] = array('sr_no'=>$i,
		'called_by'=>$row->first_name." ".$row->last_name,
		'call_Date'=>$row->call_date,
		'start_time'=>date('h:i:s a', strtotime($row->start_time)),
		'end_time'=>date('h:i:s a', strtotime($row->end_time)));
		$i++;
	}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($lead_data),
		"iTotalDisplayRecords" => count($lead_data),
		"aaData"=>$lead_data);

	echo json_encode($results);
}

public function click_to_call_data(){
    $this->load->view('leads/click_to_call');
}

public function click_call_history()
{
date_default_timezone_set('Asia/Kolkata');
$lead_data = array();
	$this->db->select('a.*,b.*');
	$this->db->from('click_to_call_data a');
	$this->db->join('countries b','a.country_code=b.phonecode','left');
	$this->db->order_by('id','desc');
	$query=$this->db->get();
    $res = $query->result();
	$i=1;


	foreach($res as $row)
	{
	    $delect = "<a href='".page_url."Leads/delete_Calltoaction/".$row->id."'><i class='fa fa-trash' style='font-size:30px;'></i></a>";
$mobile = $row->contact_no;
if($row->country_code=='91'){
    $call = base64_encode($mobile);
}else{
   $call = base64_encode($row->phonecode.$mobile); 
}
$user_id =$this->session->userdata['logged_in']['user_id'];	
$action = "<a href='".page_url."Leads/customer_click_to_call/".$call."/".$user_id."' target='_blank'><span class='btn btn-danger'><i class='fa fa-phone'></i> Call to Customer</span></a>";	
$lead_data[] = array('sr_no'=>$i,
		'customer_name'=>$row->name,
		'country'=>$row->country_name,
		'date'=>date('Y-m-d h:i:s a', strtotime($row->added_on)),
		'contact_number'=>$row->contact_no,'action'=>$action,'delete'=>$delect);
		$i++;
	}
		$results = array(
		"sEcho" => 1,
		"iTotalRecords" => count($lead_data),
		"iTotalDisplayRecords" => count($lead_data),
		"aaData"=>$lead_data);

	echo json_encode($results);
}

public function customer_click_to_call(){
	    
	  // $caller="9873552525";
	   $called = base64_decode($this->uri->segment(3));
	   $user_id =$this->session->userdata['logged_in']['user_id'];
	   $user=$this->uri->segment(4);
	   //$lead_id =$this->uri->segment(5);
	   $querty=$this->db->select('*')->from('assign_caller_id')->where('user_id',$user)->get();
	   if($querty->num_rows()>0)
	   {
	   foreach($querty->result() as $callerdata);
	   $callerext=$callerdata->caller_number;
	   $callerpass=$callerdata->password;
	   }else
	   {
	   echo "NO CALLER ID ASSIGNED";exit;
	   }
	   
$curl = curl_init();

//echo  "http://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=c2cliza&pwd=".$callerpass; exit;
$random = mt_rand(10, 1001);
date_default_timezone_set('Asia/Kolkata');
$datatime = date('Ymdhis'); 
$flag = $random.$datatime;

//echo "http://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=techmet&pwd=".$callerpass."/".$flag; exit;
curl_setopt_array($curl, array(
//  CURLOPT_URL => "http://ivr.virtuo.in/c2c.php?user=c2cdemo&pwd=man12man&caller=".$caller."&called=".$called,
CURLOPT_URL => "http://bst.virtuo.in/c2csip.php?called=".$called."&caller=".$callerext."&user=techmet&pwd=".$callerpass."&uid=".$flag,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
$success =  json_decode($response,true);
//echo "<pre>"; print_r($response);exit;

    
    
curl_close($curl);
echo "<h1>Thank You, you will be connected with the customer soon..... </h1>";
	}
	
public function delete_Calltoaction(){
    $id = $this->uri->segment(3);
   $this -> db -> where('id', $id);
  $this -> db -> delete('click_to_call_data');
    $this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#fff">Thank You! Record Successfully deleted.</div><br/>');
 redirect(page_url.'Leads/click_to_call_data/');
}

public function todays_assigned_lead(){
			$this->load->view('leads/todays_assigned_lead');
	}
	
public function view_todays_assigned_leads()
	{
	    date_default_timezone_set('Asia/Kolkata');
	    $date = date('Y-m-d');
	    //echo $date; exit;
		$lead_data = array();
		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source, s.lead_name as lead_quality_status')->from('lead_assigned_to_team a');
		$this->db->join('leads l','a.lead_id=l.id','left');
		$this->db->join('countries b','l.country=b.country_id','left');
		$this->db->join('states c','l.state=c.state_id','left');
		$this->db->join('cities d','l.city=d.city_id','left');
		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
		$this->db->join('lead_quality_status s','l.lead_quality_status=s.lead_id','left');
		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
		//$this->db->where("a.added_on BETWEEN '$date 00:00:00' AND '$date 23:59:59'");
		$this->db->where('l.lead_quality',0);
		$this->db->order_by('l.create_date','DESC');
		$query = $this->db->get();
		$res = $query->result();									
		$i=1;
		foreach($res as $row)
		{
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
				$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$reassign_member = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."'><span class='btn btn-xs btn-danger'>Change Assignment</span></a>";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			
		$query = $this->db->select('a.lead_id, a.team_id, a.member_id, b.lead_id, b.team_id, c.user_id, c.first_name, c.last_name')->from('lead_assigned_to_team_member a')->join('lead_assigned_to_team b','a.lead_id=b.lead_id','left')->join('system_users c','a.member_id=c.user_id','left')->where('a.team_id',$this->uri->segment(3))->where('a.lead_id',$row->id)->get();
		$res = $query->result();
		//echo "<pre>"; print_r($res);
		
	if($res){
	    foreach($res as $assignedinfo)
	    
	    $assignement = $assignedinfo->first_name." ".$assignedinfo->last_name."<br>".$reassign_member;
	}else{
	    	$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
		       
		       $qry = $this->db->select('a.manager_id, a.team_id, b.user_id, b.first_name, b.last_name')->from('manager_teams a')->join('system_users b','a.manager_id=b.user_id','left')->where('a.team_id',$this->uri->segment(3))->get();
		       foreach($qry->result() as $managerinfo);
		       
		      	$team .="<option value='".$managerinfo->user_id."'>".$managerinfo->first_name." ".$managerinfo->last_name."</option>";
		       
				foreach($query->result() as $teammember)
				{
				$team .="<option value='".$teammember->user_id."'>".$teammember->first_name." ".$teammember->last_name."</option>";
				}
		
			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->first_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
	}	
			
	
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'lead_quality_status'=>$lead_quality_status,
			'unique_id'=>$row->unique_id,
			'status'=>$sta,
			'assign'=>$assignement,
			'edit'=>$edit."&nbsp; l &nbsp;  ".$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function shared_lead_list()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$lead_data = array();
		$this->db->select('a.*,b.user_id, b.first_name,b.last_name')->from('customer_share_with a');
		$this->db->join('system_users b','a.shared_wid=b.user_id','left');
       $this->db->where('a.customer',$this->uri->segment(3));
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{

		
		
			$lead_data[] = array('sr_no'=>$i,
			'name'=>$row->first_name." ".$row->last_name,
			'added_on'=>$row->added_on);
			
			$i++;
		}
		//echo "<pre>"; print_r($lead_data); exit;
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		echo json_encode($results);
	}	
	
	public function view_shared_leads_report()
	{
		$user_id =$this->uri->segment(3);
		$lead_data = array();
		$this->db->select('a.*,l.*,b.country_id,b.country_name,c.state_name,c.state_id,d.city_id,d.city_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source, s.*, l.id as leadinfo_id, l.status as leadstatus')->from('lead_assigned_to_team_member a');
		$this->db->join('leads l','a.lead_id=l.id','left');
		$this->db->join('countries b','l.country=b.country_id','left');
		$this->db->join('states c','l.state=c.state_id','left');
		$this->db->join('cities d','l.city=d.city_id','left');
		$this->db->join('lead_type e','l.lead_quality=e.lead_id','left');
		$this->db->join('patient_type f','l.patient_type_id=f.patient_id','left');
		$this->db->join('lead_source g','l.lead_source_id=g.source_id','left');
		$this->db->join('customer_share_with s','a.lead_id=s.customer','left');
    	$this->db->where('s.shared_wid',$user_id);
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{

			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$status = $row->leadstatus;
			if($status=='1')
			{
				$sta =  "<span class='btn btn-success btn-xs'>Active</span>";
			}else
			{
				$sta =  "<span class='btn btn-danger btn-xs'>Not Active</span>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->leadinfo_id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->leadinfo_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->leadinfo_id."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
			$gender = $row->gender;
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}


		$query = $this->db->select('a.team_id,a.employee_id, a.department_id,b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id')->where('a.team_id',$this->uri->segment(3))->order_by('b.first_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
				foreach($query->result() as $teammember)
				{
				$team .="<option value='".$teammember->user_id."'>".$teammember->first_name."</option>";
				}

			$team .="</select>";
			$query = $this->db->select('a.id,a.lead_id,a.team_id,a.member_id,b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->lead_id)->get();
			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->first_name;
				}

			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			}
			//$remarks = substr($row->remarks,0,50);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'patient_type'=>$row->patient_type,
			'lead_source'=>$row->lead_source,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->contact_no,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'country'=>$row->country_name,
			'lead_quality'=>$lead_type,
			'unique_id'=>$row->unique_id,
			'status'=>$sta,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		echo json_encode($results);
	}
	
	public function todays_followup(){
		$this->load->view('leads/todays_followup');
	}
	
		public function todaysfollowup_list()
	{
		$this->db->distinct();
		$date = date('Y-m-d');
		$lead_data = array();
		$this->db->select('a.*,a.id as customerid, b.country_id,b.country_name,c.id,c.lead_id,c.next_follow_date,e.lead_id,e.lead_name,e.color, c.remarks as progressremark, s.lead_name as lead_quality_status')->from('leads a');
		$this->db->join('countries b','a.country=b.country_id','left');
		$this->db->join('progress_remarks c','a.id=c.lead_id','left');
		$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
		$this->db->join('lead_quality_status s','a.lead_quality_status=s.lead_id','left');
		$this->db->where('c.next_follow_date',$date);
		$this->db->group_by('c.lead_id');
		$this->db->order_by('a.create_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			
				$query22 = $this->db->select('a.id,a.lead_id, a.member_id, b.user_id, b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id','left')->where('a.lead_id',$row->customerid)->get();
                $userdetail = $query22->result();
                if($query22->num_rows()>0){
					foreach($userdetail as $assigned_person);
					$name = $assigned_person->first_name." ".$assigned_person->last_name;
				}else{
					$name="";
				}
                
                     				
                        				
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$followup_date = $row->next_follow_date;
			
			//$edit = "<a href='".page_url."Leads/edit_leads/".$row->customerid."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$view = "<a href='".page_url."Leads/view_detail/".$row->customerid."'><i class='fa fa-eye' title='view detail & Progress Report'></i></a>";
		
			$gender = $row->gender; 
			if($gender=='1'){$gnd = "Male";
			$tt = "Mr. ";
			}else{$gnd = "Female";
			$tt = "Ms. ";
			}
			$remarks = substr($row->progressremark,0,200);
			$lead_data[] = array('sr_no'=>$i,
			'create_date'=>$row->create_date,
			'customer_name'=>$tt." ".$row->customer_name,
			'gender'=>$gnd,
			'email'=>$row->email,
			'contact_no'=>$row->country_code."".$row->contact_no,
			'country'=>$row->country_name,
			'remarks'=>$remarks,
			'lead_quality'=>$lead_type,
			'lead_quality_status'=>$lead_quality_status,
			'unique_id'=>$row->unique_id,
			'followup'=>$followup_date,
			'assigned_to'=>$name,
			'edit'=>$view);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}
	
	public function today_leads(){
		$this->load->view('leads/today_leads');
	}
	
	public function todays_leads_list() {
		$today = date('Y-m-d');
		$query = $this->db->select('a.customer_name, a.email, a.country_code, a.contact_no, a.gender, a.create_date, b.lead_source')
						  ->from('leads a')
						  ->join('lead_source b', 'a.lead_source_id=b.source_id')
						  ->where('a.create_date', $today)
						  ->get();
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
			if($row->gender == 1) {
				$gender = "Male";
				$tt = "Mr. ";
			} else {
				$gender = "Female";
				$tt = "Ms. ";
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'create_date'=> date('d-m-Y', strtotime($row->create_date)),
								'customer_name'=>$tt." ".$row->customer_name,
								'gender'=>$gender,
								'email'=>$row->email,
								'contact_no'=>$row->country_code."".$row->contact_no
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
	
	public function active_leads(){
		$this->load->view('leads/active_leads');
	}
	
	public function active_leads_list() {
		$today = date('Y-m-d');
		$query = $this->db->select('a.customer_name, a.email, a.country_code, a.contact_no, a.gender, a.create_date, b.lead_source')
						  ->from('leads a')
						  ->join('lead_source b', 'a.lead_source_id=b.source_id')
						  ->where('a.status', 1)
						  ->get();
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
			if($row->gender == 1) {
				$gender = "Male";
				$tt = "Mr. ";
			} else {
				$gender = "Female";
				$tt = "Ms. ";
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'create_date'=> date('d-m-Y', strtotime($row->create_date)),
								'customer_name'=>$tt." ".$row->customer_name,
								'gender'=>$gender,
								'email'=>$row->email,
								'contact_no'=>$row->country_code."".$row->contact_no
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

	public function pending_for_bom_sales() {
		$this->load->view('leads/pending_for_bom_sales');
	}

	public function pending_bom_sales_list() {
		$lead_data = array();
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
		{
		
		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

			if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
			$member_id = $this->uri->segment(3);
			$query = $this->db->query("SELECT b.customer_name,c.member_id,b.id,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=0 AND c.member_id='$member_id' $between ORDER BY b.id DESC");
		} else {
			$query = $this->db->query("SELECT b.customer_name,c.member_id,b.id,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=0 $between ORDER BY b.id DESC");
			}
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$query = $this->db->query("SELECT b.customer_name,c.member_id,b.id,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=0 AND c.member_id='$user_id' ORDER BY b.id DESC");
		}
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url."BOM/pis/".$row->id."' class='btn btn-danger btn-xs' target='_blank'>QUOTATION PIS</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";

			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								// 'action' => $edit
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}
	
	public function pending_for_bom_technical() {
		$this->load->view('leads/pending_for_bom_technical');
	}
	
	public function pending_bom_technical_list() {
		$lead_data = array();

		$userid=$_SESSION['logged_in']['user_id'];
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
		{
			$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
			$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

				if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
					$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
				}else{
					$between="";
				}

				if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
				$team_member = $this->uri->segment(3);

				// echo $team_member;exit;
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 AND c.member_id='$team_member' $between ORDER BY b.id DESC");
			} else {
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 $between ORDER BY b.id DESC");
			}
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 AND c.member_id='$userid' ORDER BY b.id DESC");
		}
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url."BOM/bom/".$row->id."' class='btn btn-danger btn-xs' target='_blank'>QUOTATION BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$getProjectName=$this->BOM_model->getProjectName($row->id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								// 'leadsource'=>$leadsource,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'project_name'=>$getProjectName,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

	public function non_qualified_leads() {
		$this->load->view('leads/non_qualified_leads');
	}

	
	public function non_qualified_leads_list() {

		$lead_data = array();
		if($_SESSION['logged_in']['role']==1)
		{
			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.country_code,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('3') AND c.member_id='$team_member' ORDER BY a.added_on DESC");
			} else {
				$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.id as leadid,a.remarks,b.country_code,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('3') ORDER BY a.added_on DESC");
			}
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.country_code,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city,b.contact_person,b.email,b.alt_contact_no FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('3') AND c.member_id='$user_id' ORDER BY a.added_on DESC");
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

	$getleadmanager=$this->salescrm->getusername($row->added_by);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


	/** NON QUALIFIED REASON **/
	if($row->nonqualifiedreason==1)
	{
		$reason="Customer Doesn't have the pocket size";
	}else if($row->nonqualifiedreason==2)
	{
		$reason="Product do not have the pocket size";
	}else if($row->nonqualifiedreason==3)
	{
		$reason="Customer & Product do not have the pocket size";
	}else if($row->nonqualifiedreason==4)
	{
		$reason="Irrelevant";

	}else if($row->nonqualifiedreason==5)
	{
		$reason="Called more than 4 times but not responding";
	}else
	{
		$reason='';
	}
	/** END **/


$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								// 'update'=>$view,
								'reason'=>$reason,
								'leadsource'=>$leadsource
						
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
		public function lead_type_list() {
		$lead_type_list = array();
		$lead_stage = $this->getLeadStageByLeadType($this->uri->segment(3));
		
		$query = $this->db->select('a.id, a.customer_name, a.email, a.contact_no')
						  ->from('leads a')
						  ->where('a.closed', 0)
						  ->get();

		$i=1;
		if($query->num_rows() > 0) {
			
		foreach($query->result() as $row) {

			  $sql = $this->db->select('a.lead_status, a.added_on, b.first_name, b.last_name')
							  ->from('progress_remarks a')
							  ->join('system_users b', 'a.added_by=b.user_id')
							  ->where('a.lead_id', $row->id)
							  ->order_by('a.id', 'DESC')
							  ->limit(1)
							  ->get();

				if($sql->num_rows() > 0) {
					foreach ($sql->result() as $rows);
						if(in_array($rows->lead_status, $lead_stage)) {
						$lead_type_list[] = array(
											'sr_no'=>$i,
											'customer_name' => $row->customer_name,
											'email' => $row->email,
											'contact_no' => $row->contact_no,
											'last_added' => date('d-m-Y H:i:s', strtotime($rows->added_on)),
											'added_by' => $rows->first_name." ".$rows->last_name
											);
						$i++;

						
							}
						
					}
				}
			} 

			$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
	}

	function getLeadStageByLeadType($uri) {
		$lead_stage = array();
		$sql = $this->db->select('lead_stage_id')
						->from('lead_types')
						->where('id', $uri)
						->get();

			if($sql->num_rows() > 0) {
				foreach ($sql->result() as $row) {
				$lead_stage[] = $row->lead_stage_id;
					
				}
			}

			return $lead_stage;
	}

		public function getUsers() {
		   $q = $_GET['q'];
		   $query = $this->db->select('user_id, first_name, last_name')
							 ->from('system_users')
							 ->like('first_name', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $users) {
					$json[] = array('id'=>$users->user_id, 'text'=>$users->first_name." ".$users->last_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
	}

	
			function todaysfollowup()
			{
				$this->load->view('leads/todaysfollowup');
			}
	
		function todaysfollowuplist()
			{
			    
			 //    $company_id=$_SESSION['logged_in']['business_location'];
				// $mode=$this->getsettings();
				// if(count($mode)>0)
				// {
				// 	$mode=$mode['mode'];
				// }else
				// {
				// 	$mode=1;

				// }

				$conversion_lead_stage = $this->getConversionLeadStage();
				$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
				array_push($getDeadEndLeadStage, $conversion_lead_stage);
				$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

				$cur=date('Y-m-d');
			$lead_type_list = array();
			$user_id=$_SESSION['logged_in']['user_id'];
			$contactacess=$this->Lead_model->checkforcontactacess($user_id);
	
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur' GROUP BY a.lead_id");
			
			if($resty->num_rows()>0)
			{
				$i=1;
				foreach ($resty->result() as $row)
				{
					$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					
					

										$a=1;
										$html='';
									

					// if($mode==1)
					// {

								$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
												  ->from('lead_assigned_to_team a')
												  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
												  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
												  ->join('system_users d','d.user_id=c.member_id','left')
												  ->where('a.lead_id',$row->lead_id)
												  ->get();

								$res = $query->result();
								if($res){
									foreach($res as $assign_information)
									{
									$assignement = 	$assign_information->team_name;
									$fname = 	$assign_information->first_name;
									$lname = 	$assign_information->last_name;
									}
									
								}else{
								$assignement = "";
								$fname = '';
								$lname = '';
								}

								$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->lead_id)->get();
								if($restsyt->num_rows()>0)
								{
									foreach($restsyt->result() as $restsyt);

									$fname=$this->salescrm->getusername($restsyt->member_id);
									$lname='';
								}else
								{
									$fname='';
									$lname='';
								}
					// }else
					// {

					// $assignement=$this->Lead_model->getleadmanagerformulti($row->lead_id,$row->client_location);
					// $fname='';
					// $lname='';

					// }


						$view = "<a href='".page_url."Leads/view_detail/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
						$country=$this->salescrm->getcountry($row->country);
					$state=$this->salescrm->getstate($row->state);
				$read='';
				if($contactacess>0){


				$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
				}

					
					$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
					$leadstatus=$this->salescrm->getLeadStatusonly($row->lead_id);
					$lead_type_list[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastleadstatus'=>$leadstatus,
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
								);
					$i++;
					}
			}
			
	$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
			}

			function todays_proposal_list()
			{
				$cur=date('Y-m-d');
			$lead_type_list = array();
			if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
			{

			$resty=$this->db->query("SELECT d.added_on,c.member_id,d.remarks,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city FROM client_quoted_price a JOIN leads b ON a.lead_id=b.id JOIN proposal_meeting_details d ON d.lead_id=a.lead_id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE d.id IN (SELECT MAX(id) FROM proposal_meeting_details GROUP BY lead_id) AND d.meeting_status IN ('16','17') AND d.followup_date='$cur' AND b.proposal_meeting=0 GROUP BY lead_id");
			}else
			{
				$user_id=$_SESSION['logged_in']['user_id'];
				$resty=$this->db->query("SELECT d.added_on,c.member_id,d.remarks,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city FROM client_quoted_price a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id JOIN proposal_meeting_details d ON d.lead_id=a.lead_id WHERE d.id IN (SELECT MAX(id) FROM proposal_meeting_details GROUP BY lead_id) AND d.meeting_status IN ('16','17') AND d.followup_date='$cur' AND b.proposal_meeting=0 AND c.member_id='$user_id' GROUP BY lead_id");
			}
			if($resty->num_rows()>0)
			{
				$i=1;
				foreach ($resty->result() as $row)
				{
					$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					$country=$this->salescrm->getcountry($row->country);
					$state=$this->salescrm->getstate($row->state);
					$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
					$getleadmanager=$this->salescrm->getusername($row->member_id);

						$view = "<a href='".page_url."Proposal/update_progress/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
					$lead_type_list[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
								);
					$i++;
					}
			}
			
	$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
			}

			function todays_negotiation_list()
			{
				$cur=date('Y-m-d');
			$lead_type_list = array();
			if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
			{

			$query=$this->db->query("SELECT a.added_on,a.remarks,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city,c.member_id FROM negotiation_meeting_details a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM negotiation_meeting_details GROUP BY lead_id) AND a.meeting_status IN ('23','24') AND a.followup_date='$cur' AND b.proposal_meeting = 1 AND b.negotiation_meeting =0 GROUP BY lead_id");
			}else
			{
				$user_id=$_SESSION['logged_in']['user_id'];
				$query=$this->db->query("SELECT a.added_on,a.remarks,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city,c.member_id FROM negotiation_meeting_details a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM negotiation_meeting_details GROUP BY lead_id) AND a.meeting_status IN ('23','24') AND a.followup_date='$cur' AND b.proposal_meeting = 1 AND b.negotiation_meeting=0 AND c.member_id='$user_id' GROUP BY lead_id");
			}

			if($query->num_rows()>0)
			{
				$i=1;
				foreach ($query->result() as $row)
				{
					$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					$getleadmanager=$this->salescrm->getusername($row->member_id);
					$country=$this->salescrm->getcountry($row->country);
					$state=$this->salescrm->getstate($row->state);
					$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

						$view = "<a href='".page_url."Proposal/negotiation_progress/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
					$lead_type_list[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
								);
					$i++;
					}
			}
			
	$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
			}


				public function completed_bom_sales_list() {
		$lead_data = array();

		// $query = $this->db->query("SELECT b.id as lead_id,b.customer_name,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.email_id,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=1");
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
		{
		$query = $this->db->query("SELECT c.member_id,b.id as lead_id,b.customer_name,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.email_id,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=1 ORDER BY b.id DESC");
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$query = $this->db->query("SELECT c.member_id,b.id as lead_id,b.customer_name,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.email_id,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=1 AND c.member_id='$user_id' ORDER BY b.id DESC");
		}
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$sql = $this->db->select('id')
							->from('bom_pi_sales_client_info')
							->where('lead_id', $row->lead_id)
							->get();

			foreach ($sql->result() as $rows);

			$pis = "<a href='".page_url."BOM/edit_pis/".$rows->id."/".$row->lead_id."' class='btn btn-danger btn-xs' target='_blank'>EDIT/VIEW PIS</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";

$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								// 'action' => $edit
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}


	public function completed_bom_technical_list() {
		$lead_data = array();

		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
		{
		$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 ORDER BY b.id DESC");
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];

			$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$user_id' ORDER BY b.id DESC");

		}
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			// $pis = "<a href='".page_url."Leads/previewbom/".$row->unique_id.$row->id."/".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";
			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>QUOTATION BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			 $edit = "<a href='".page_url."BOM/edit_bom_testing/".$row->id."'><i class='fa fa-pencil' title='Edit BOM'></i></a>";
			// $edit = '';
			$delete_bom = "<a href='javascript:;' class='btn btn-danger btn-xs' onclick='delete_bom(".$row->id.")'>NULL & VOID</a>";

 //$edit = "<a href='javascript:;'><i class='fa fa-pencil' title='Edit BOM'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$getProjectName = $this->BOM_model->getProjectName($row->id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								// 'leadsource'=>$leadsource,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'project_name' =>  $getProjectName,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis,
								'action' => $edit,
								'delete_bom' => $delete_bom
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

	public function getTeamMembers() {
		$html = '';
		$htmldata = '';
		$assign_team = $this->input->post('assign_team');
		$leadID = $this->input->post('leadID');

	   $sql1 = $this->db->select('member_id')
						->from('lead_assigned_to_team_member')
						->where('team_id', $assign_team)
						->where('lead_id', $leadID)
						->get();



		$sql = $this->db->select('b.user_id, b.first_name, b.last_name')
						->from('presto_team_members a')
						->join('system_users b', 'b.user_id=a.employee_id')
						->where('a.team_id', $assign_team)
						->get();

		$html .= '<option value="">Select Team Member</option>';
		if ($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
				if($sql1->num_rows() > 0) {
					foreach ($sql1->result() as $row1);
					if($row1->member_id == $row->user_id) {
						$selected = 'selected';
					} else {
						$selected = '';
					}
				} else {
						$selected = '';
				}
				$html .= '<option value="'.$row->user_id.'" '.$selected.'>'.$row->first_name." ".$row->last_name.'</option>'; 
			}
		}

		  $query = $this->db->select('b.user_id, b.first_name, b.last_name')
							->from('prestogroup_teams a')
							->join('system_users b', 'b.user_id=a.team_leader')
							->where('a.team_id', $assign_team)
							->get();


				if ($query->num_rows() > 0) {
					foreach ($query->result() as $rows) {
						if($sql1->num_rows() > 0) {
						foreach ($sql1->result() as $row1);
							if($row1->member_id == $rows->user_id) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
						} else {
								$selected = '';
						}
						$html .= '<option value="'.$rows->user_id.'" '.$selected.'>'.$rows->first_name." ".$rows->last_name.'</option>'; 
					}
				}

		echo $html;
	}



	public function new_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('7') AND b.closed=0 AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('7') AND b.closed=0 $between GROUP BY b.id  ORDER BY b.id DESC");
		}
		}else
		{
			
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";

			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('7') AND b.closed=0 AND c.member_id IN ('$user_id',$team_member)  GROUP BY b.id  ORDER BY b.id DESC");
			}
			else
			{

			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('7') AND b.closed=0 AND c.member_id='$user_id'  GROUP BY b.id  ORDER BY b.id DESC");
			}
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	$read='';
		if($contactacess>0){

		
$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	

	function newleads()
	{
		$this->load->view('leads/openleads');
	}


	function qualified_leads_for_intro()
	{
		$this->load->view('leads/pending_for_intro');
	}

	function qualified_leads_for_intro_list()
	{

		$lead_data = array();
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31 )
		{
			$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
			$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

			if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
				$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
			}else{
				$between="";
			}

			if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
			$member_id = $this->uri->segment(3);
			$resty=$this->db->query("SELECT a.lead_status,a.id,a.added_on,a.added_by,b.id as leadid,a.remarks,b.country_code,b.lead_source_id,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2','4','8','9','11') AND b.closed=0 AND b.pis_created='0' AND c.member_id='$member_id' $between ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.lead_status,a.id,a.added_on,a.added_by,b.id as leadid,a.remarks,b.country_code,b.lead_source_id,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2','4','8','9','11') AND b.closed=0 AND b.pis_created='0' $between ORDER BY b.id DESC");
		}
	}else
	{
		$user_id=$_SESSION['logged_in']['user_id'];
		$resty=$this->db->query("SELECT a.lead_status,a.id,a.added_on,a.added_by,b.id as leadid,a.remarks,b.country_code,b.lead_source_id,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2','4','8','9','11') AND b.closed=0 AND b.pis_created='0' AND c.member_id='$user_id' ORDER BY b.id DESC");
	}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

	$getleadmanager=$this->salescrm->getusername($row->added_by);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadstatus=$this->salescrm->getLeadStagename($row->lead_stage);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
					
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								'lastremarks'=>$row->remarks,
								'currentstatus'=>$leadstatus,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'leadsource'=>$leadsource,
								'update'=>$view
						
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

	function change_assigned_lead() {
		$lead_id = $this->uri->segment(3);
		$assigned_team = $this->input->post('assigned_team');
		$assigned_team_member = $this->input->post('assigned_team_member');


		$data = array(
					'team_id' => $assigned_team,
					);

		$this->db->where('lead_id', $lead_id)
				 ->update('lead_assigned_to_team', $data);

		$datas = array(
					'team_id' => $assigned_team,
					'member_id' => $assigned_team_member,
					);

		$this->db->where('lead_id', $lead_id)
				 ->update('lead_assigned_to_team_member', $datas);

		$this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>');
			redirect(page_url.'Leads');

	}



public function overall_non_qualified_leads() {
		$this->load->view('leads/overall_non_qualified_leads');
	}

	
	public function overall_non_qualified_leads_list() {

		$lead_data = array();
		
				$resty=$this->db->query("SELECT a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.id as leadid,a.remarks,b.country_code,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('3') ORDER by a.added_on DESC");
		
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

	$getleadmanager=$this->salescrm->getusername($row->added_by);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

	/** NON QUALIFIED REASON **/
	if($row->nonqualifiedreason==1)
	{
		$reason="Customer Doesn't have the pocket size";
	}else if($row->nonqualifiedreason==2)
	{
		$reason="Product do not have the pocket size";
	}else if($row->nonqualifiedreason==3)
	{
		$reason="Customer & Product do not have the pocket size";
	}else if($row->nonqualifiedreason==4)
	{
		$reason="Irrelevant";

	}else if($row->nonqualifiedreason==5)
	{
		$reason="Called more than 4 times but not responding";
	}else
	{
		$reason='';
	}
	/** END **/

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'reason'=>$reason,
								'leadsource'=>$leadsource
						
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


	function remarketing_leads()
	{
		$this->load->view('leads/remarketing_leads');

	}


public function remarketing_leads_list() {

	$lead_data = array();
		$role=$_SESSION['logged_in']['role'];
		$mode = $this->getsettings();
		if(count($mode)>0){
			$mode = $mode['mode'];
		}else{
			$mode="1";
		}

		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		if(count($getDeadEndLeadStage)>0)
		{
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		}else
		{
			$dead_end_lead_stage='';
		}
 
		$reason=$this->dashboardmodel->getallremarketingreason();
		if(count($reason)>0)
		{
		$rreason = "'" . implode ( "', '", $reason ) . "'";
		}else
		{
		$rreason='';
		}
	
	$company_id=$_SESSION['logged_in']['business_location'];
		if($mode==1)
{
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($dead_end_lead_stage) AND a.nonqualifiedreason IN ($rreason) AND b.company_id='$company_id' GROUP BY b.id ORDER BY b.id DESC");

}else
{
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks AND b.company_id='$company_id' GROUP BY lead_id) AND a.lead_stage IN ($dead_end_lead_stage) AND a.nonqualifiedreason IN ($rreason) GROUP BY b.id ORDER BY b.id DESC");
}
			
		$i=1;
		//echo "<pre>"; print_r($resty->result()); exit;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';


	if($mode==1)
{

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->leadid)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}
}else
{

$assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
$fname='';
$lname='';

}
	
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$lead_stage=$this->salescrm->getlateststatus($row->leadid);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

		$user_id=$_SESSION['logged_in']['user_id'];
		$read='';
		if($contactacess>0){

		
$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}


			$lead_data[] = array(
								'sr_no'=>$i,
								'lead_stage'=>$lead_stage,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	function sales_dashboard() {
		$this->load->view('leads/sales_dashboard');
	}

	public function sales_dashboard_list() {

		$lead_data = array();
		$lead_data1 = array();
		$lead_data2 = array();
		$lead_data3 = array();
		
		$sql=$this->db->select('country_code,contact_no,alt_contact_no,patient_type_id,customer_name,contact_person,email_id,mobile_no,company_name,unique_id,email,create_date,country,state,city')
					  ->from('leads')
					  ->where('bom_created',1)
					  ->where('closed',0)
					  // ->order_by('id', 'DESC')
					  ->get();
		
		
		$i=1;
		if($sql->num_rows() > 0) {
		foreach($sql->result() as $row) {

			$lead_data1[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$row->city,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								'status' => 'BOM CREATED'						
								);
						$i++;

				
				}

			}


		$sql1=$this->db->select('country_code,contact_no,alt_contact_no,patient_type_id,customer_name,contact_person,email_id,mobile_no,company_name,unique_id,email,create_date,country,state,city')
					  ->from('leads')
					  ->where('pis_created',1)
					  ->where('closed',0)
					  // ->order_by('id', 'DESC')
					  ->get();
		
		
		$i=1;
		if($sql1->num_rows() > 0) {
		foreach($sql1->result() as $row1) {

			$lead_data2[] = array(
								'sr_no'=>$i,
								'unique'=>$row1->unique_id.'<br/>'.date('d-m-Y', strtotime($row1->create_date)),
								'company' => $row1->company_name,
								'customer_name' => $row1->customer_name.'<br/><a href="mailto:'.$row1->email_id.'">'.$row1->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row1->country_code.$row1->contact_no.'" target="_blank">'.$row1->contact_no.'</a><br/>'.$row1->city,
								'mobile' => $row1->contact_no,
								'alternatedetail'=>$row1->contact_person."<br/>".$row1->email."<br/>".$row1->alt_contact_no,
								'status' => 'PIS CREATED'						
								);
						$i++;

				
				}

			}

		$sql2=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,b.lead_source_id,b.id as leadid,a.remarks,b.country_code,b.contact_no,b.alt_contact_no,b.patient_type_id,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_status IN ('8','11','9','4','2') ORDER BY a.lead_stage DESC");
		
		
		$i=1;
		if($sql2->num_rows() > 0) {
		foreach($sql2->result() as $row2) {
			$clienttype=$this->salescrm->getClientTypebyid($row2->patient_type_id);
			$getleadmanager=$this->salescrm->getusername($row2->added_by);
			$country=$this->salescrm->getcountry($row2->country);
			$state=$this->salescrm->getstate($row2->state);
			$leadsource=$this->salescrm->getleadsourcebyid($row2->lead_source_id);
			$leadstatus=$this->salescrm->getLeadStagename($row2->lead_stage);
				$lead_data3[] = array(
								'sr_no'=>$i,
								'unique'=>$row2->unique_id.'<br/>'.date('d-m-Y', strtotime($row2->create_date)),
								'company' => $row2->company_name,
								'customer_name' => $row2->customer_name.'<br/><a href="mailto:'.$row2->email_id.'">'.$row2->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row2->country_code.$row2->contact_no.'" target="_blank">'.$row2->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row2->city,
								// 'customer_type' => $clienttype,
								'mobile' => $row2->contact_no,
								'alternatedetail'=>$row2->contact_person."<br/>".$row2->email."<br/>".$row2->alt_contact_no,
								'status' => $leadstatus						
								);
						$i++;
				}

			}

			// $lead_data = array_merge($lead_data1,$lead_data2,$lead_data3);
			$lead_data = $lead_data1+$lead_data2+$lead_data3;

			$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
			
			
		echo json_encode($results);
	}

	function deleteLead() {
		$id = $this->input->post('id');
		//echo $id; exit;

		$this->db->where('id', $id)
				 ->delete('leads');

			if($this->db->affected_rows() > 0) {
				echo 'success';
			} else {
				echo 'error';
			}
	}

	public function delete_lead() {
    $id = $this->input->post('id');
    echo $id; exit;
    if (empty($id)) {
        echo 'ID not found';
        return;
    }

    $this->db->where('id', $id)->delete('leads');

    if ($this->db->affected_rows() > 0) {
        echo 'success';
    } else {
        echo 'error';
    }
}


				function lost_leads()
			{
				$this->load->view('leads/lost_leads');
			}
	
		function lost_leads_list()
			{
			$lead_type_list = array();
			if($_SESSION['logged_in']['role']==1)
			{			
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('12')");
			}else
			{
				$user_id=$_SESSION['logged_in']['user_id'];
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('12') AND c.member_id='$user_id'");
			}
			if($resty->num_rows()>0)
			{
				$i=1;
				foreach ($resty->result() as $row)
				{
					$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					$getleadmanager=$this->salescrm->getusername($row->added_by);
					$country=$this->salescrm->getcountry($row->country);
					$state=$this->salescrm->getstate($row->state);
					$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

						$view = "<a href='".page_url."Leads/view_detail/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
					$lead_type_list[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
								);
					$i++;
					}
			}
			
	$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
			}

function previewbom()
{

$this->load->view('leads/previewbom');
}


public function pending_for_bom_sales_overall() {
		$this->load->view('leads/pending_for_bom_sales_overall');
	}

	public function pending_bom_sales_overall_list() {
		$lead_data = array();

		
		if($this->uri->segment(3) != '') {
			$team_member = $this->uri->segment(3);
			$query = $this->db->query("SELECT b.customer_name,c.member_id,b.id,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=0 AND c.member_id='$team_member'");
		} else {
			$query = $this->db->query("SELECT b.customer_name,c.member_id,b.id,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=0");
			}
	
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url."BOM/pis/".$row->id."' class='btn btn-danger btn-xs' target='_blank'>CREATE PIS</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";

			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								// 'action' => $edit
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

		public function completed_bom_sales_overall_list() {
		$lead_data = array();

		// $query = $this->db->query("SELECT b.id as lead_id,b.customer_name,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.email_id,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=1");
		
		$query = $this->db->query("SELECT c.member_id,b.id as lead_id,b.customer_name,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.email_id,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.pis_created=1");
	
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$sql = $this->db->select('id')
							->from('bom_pi_sales_client_info')
							->where('lead_id', $row->lead_id)
							->get();

			foreach ($sql->result() as $rows);

			$pis = "<a href='".page_url."BOM/edit_pis/".$rows->id."' class='btn btn-danger btn-xs' target='_blank'>EDIT/VIEW PIS</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";

$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								// 'action' => $edit
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

		public function pending_for_bom_overall_technical() {
		$this->load->view('leads/pending_for_bom_overall_technical');
	}
	
	public function pending_bom_technical_overall_list() {
		$lead_data = array();

			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 AND c.member_id='$team_member'");
			} else {
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url."BOM/bom/".$row->id."' class='btn btn-danger btn-xs' target='_blank'>CREATE BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
			$state=$this->salescrm->getstate($row->state);
			$getProjectName=$this->BOM_model->getProjectName($row->id);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'project_name' => $getProjectName,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

		public function completed_bom_technical_overall_list() {
		$lead_data = array();

		if($_SESSION['logged_in']['role']==1)
		{
		$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1");
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];

			$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1");

		}
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			// $pis = "<a href='".page_url."Leads/previewbom/".$row->unique_id.$row->id."/".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);

			$delete_bom = "<a href='javascript:;' class='btn btn-danger btn-xs' onclick='delete_bom(".$row->id.")'>NULL & VOID</a>";
			
			//$edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";

  $edit = "<a href='".page_url."/BOM/edit_bom_testing/".$row->id."'><i class='fa fa-pencil' title='Edit BOM'></i></a>";
			// $edit = '';
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
			$state=$this->salescrm->getstate($row->state);
			$getProjectName=$this->BOM_model->getProjectName($row->id);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$leadsource,
								'project_name'=>$getProjectName,
								'customer_name' =>  $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'rfq' => $pis,
								'action' => $edit,
								'delete_bom' => $delete_bom
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}

	// public function check_mail() {
	// 				$msg_body = 'Check Mail';
	// 				$subjectname = "MEETING INVITE";
	// 				$this->email->set_mailtype("html");
	// 				// $this->email->to($rows->email);
	// 				$this->email->to('manjusha.mishra2494@gmail.com');
	// 				// $this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
	// 				$this->email->from('donotreply@hongyijig.com');
 //    				$this->email->subject($subjectname);
 //    				$this->email->message($msg_body);
 //    				// $this->email->attach($invitelink);
 //    				$result11=$this->email->send();
	// }



	function rfqawaitedleads()
	{
		$this->load->view('leads/rfqawaited');
	}




	public function rfq_awaited_list() {

		$lead_data = array();
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31 )
		{
		
		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);
		
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('13') AND b.closed=0 AND c.member_id='$member_id' $between ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('13') AND b.closed=0 $between ORDER BY b.id DESC");
		}
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('13') AND b.closed=0 AND c.member_id='$user_id' ORDER BY b.id DESC");
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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



function pendingbomforapproval()
{
	$this->load->view('BOM/pending_bom_for_technical_approval');

}
	function pending_bom_for_technical_approval()
	{
		$lead_data = array();

			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 AND c.member_id='$team_member' AND b.bom_technical_approved=0");
			} else {
				$query = $this->db->query("SELECT c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND b.bom_technical_approved=0");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";
$edit="<a href='".page_url."/BOM/edit_bom_testing/".$row->id."'><i class='fa fa-pencil' title='Edit BOM'></i></a>";
	//$edit="<a href='javascript();'><i class='fa fa-pencil' title='Edit BOM'></i></a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'viewbom'=>$pis,
								'edit' =>$edit,
								'approve'=>$approve,
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}




function pending_bom_for_technical_approval_history()
	{
		$lead_data = array();

			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=0 AND c.member_id='$team_member' AND b.bom_technical_approved!=0");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND b.bom_technical_approved!=0");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			// $edit = "<a href='".page_url."RFQ/edit_customer_requirement_info/".$row->id."'><i class='fa fa-pencil' title='Edit RFQ'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

$remarkssssss='';
$addedon='';
$addedby='';
$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
if($rowss->num_rows()>0)
{
	foreach($rowss->result() as $rowsss) ;
	$remarkssssss=$rowsss->remarks;
	$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
	$addedby=$this->salescrm->getusername($rowsss->addedBy);
}
if($row->bom_technical_approved==1)
{
$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

}else if($row->bom_technical_approved==2)
{
$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
}else{ $status='';
}

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'viewbom'=>$pis,
								'approve'=>$status,
								'addedon'=>$addedon.'<br/>'.$addedby,
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);
	}



function pendingbomforrevision_list()
{

	$lead_data = array();

			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.client_approvedOn,b.client_approvedBy,b.bom_technical_approved,b.rejection_remark,b.bom_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member' AND b.bom_approved=2");
			} else {
				$query = $this->db->query("SELECT b.client_approvedOn,b.client_approvedBy,b.bom_technical_approved,b.rejection_remark,b.bom_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND b.bom_approved=2");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			 $edit = "<a href='".page_url."BOM/edit_bom_testing/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			  // $edit = "<a href='javascript:;'><i class='fa fa-pencil'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

$remarkssssss='';
$addedon='';
$addedby='';
// $rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
// if($rowss->num_rows()>0)
// {
// 	foreach($rowss->result() as $rowsss) ;
// 	$remarkssssss=$rowsss->remarks;
// 	$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
// 	$addedby=$this->salescrm->getusername($rowsss->addedBy);
// }
if($row->bom_approved==1)
{
$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$row->rejection_remark;

}else if($row->bom_approved==2)
{
$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$row->rejection_remark;

}else{ $status='';
}


$addedon=date('d-M-Y H:i:s',strtotime($row->client_approvedOn));
$addedby=$this->salescrm->getusername($row->client_approvedBy);
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'viewbom'=>$pis,
								'approve'=>$status,
								'addedon'=>$addedon.'<br/>'.$addedby,
								'edit' => $edit
								);
						$i++;

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}

function pendingbomforrevision()
{
	$this->load->view('BOM/bom_pending_for_revision');
}

function pending_bom_for_missing_data()
{
	$this->load->view('BOM/pending_bom_for_missing_data');
}


function pending_bom_for_missing_data_list()
{

	$lead_data = array();

			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member'");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			 $edit = "<a href='".page_url."BOM/edit_bom_testing/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			 // $edit = "<a href='javascript:;'><i class='fa fa-pencil'></i></a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

$remarkssssss='';
$addedon='';
$addedby='';
$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
if($rowss->num_rows()>0)
{
	foreach($rowss->result() as $rowsss) ;
	$remarkssssss=$rowsss->remarks;
	$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
	$addedby=$this->salescrm->getusername($rowsss->addedBy);
}
if($row->bom_technical_approved==1)
{
$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

}else if($row->bom_technical_approved==2)
{
$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
}else{ $status='';
}

$missingdatacheck=$this->BOM_model->checkformissingdata($row->id);
			if(($missingdatacheck['datamis']>0) || ($missingdatacheck['cadmis']>0) || ($missingdatacheck['datacode']>0))
			{
				$partdata='';
				if($missingdatacheck['datamis']>0)
				{
					$partdata="Part Data Pending";
				}else{ }

				$cadweight='';
				if($missingdatacheck['cadmis']>0)
				{
					$cadweight="CAD Data Pending";
				}else{ }

				$datacode='';
				if($missingdatacheck['datacode']>0)
				{
					$datacode="Part Code Pending";
				}else{ }

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'pendingdata'=>"<strong style='color:red'>".$partdata."<br/>".$cadweight."<br/>".$datacode."</strong>",
								'viewbom'=>$edit,
								'pendingsince'=>$missingdatacheck['bomcreatedon']
								);
						$i++;

			}

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}


function pending_bom_for_missing_caddata()
{
	$this->load->view('BOM/pending_bom_for_missing_cad_data');
}


function pending_bom_for_missing_cad_data_list()
{

	$lead_data = array();
	// echo $this->uri->segment(3);exit;
			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member' ORDER BY b.id DESC");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 ORDER BY b.id DESC");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			//$edit = "<a href='".page_url."BOM/edit_bom1233/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			  $edit = "<a href='".page_url."BOM/caddataupload/".$row->id."' class='btn btn-warning'>Upload Data</a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

$remarkssssss='';
$addedon='';
$addedby='';
$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
if($rowss->num_rows()>0)
{
	foreach($rowss->result() as $rowsss) ;
	$remarkssssss=$rowsss->remarks;
	$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
	$addedby=$this->salescrm->getusername($rowsss->addedBy);
}
if($row->bom_technical_approved==1)
{
$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

}else if($row->bom_technical_approved==2)
{
$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
}else{ $status='';
}

$missingdatacheck=$this->BOM_model->checkformissingcaddata($row->id);
$services=$this->BOM_model->checkservices($row->id);

$resteyy=$this->db->select('design_req_chk')->from('bom_pi_sales_project_info')->where('lead_id',$row->id)->get();
if($resteyy->num_rows()>0)
{
    foreach($resteyy->result() as $rowss);

    $lab = '';
    if($rowss->design_req_chk==1 || (in_array(4, $services)))
    {
        $lab="CAD DATA";
        


			
			if($missingdatacheck>0)
			{

			$addeddate=$this->BOM_model->getaddeddate($row->id);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'pendingdata'=>"<strong style='color:red'>".$lab."</strong>",
								'viewbom'=>$edit,
								'pendingsince'=>$addeddate
								);
						$i++;

			}

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
				}
		    // else
		    // {
		    //      $lab="CAD Data";
		    //     $type="1";
		    // }

		}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}

	function pending_bom_for_missing_pptdata()
	{
		$this->load->view('BOM/pending_bom_for_missing_ppt_data');
	}

	function pending_bom_for_missing_ppt_data_list()
{

	$lead_data = array();
	// echo $this->uri->segment(3);exit;
			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member' ORDER BY b.id DESC");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 ORDER BY b.id DESC");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			//$edit = "<a href='".page_url."BOM/edit_bom1233/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			  $edit = "<a href='".page_url."BOM/caddataupload/".$row->id."' class='btn btn-warning'>Upload Data</a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
			$state=$this->salescrm->getstate($row->state);
			$getProjectName=$this->BOM_model->getProjectName($row->id);

			$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

			$remarkssssss='';
			$addedon='';
			$addedby='';
			$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($rowss->num_rows()>0)
			{
				foreach($rowss->result() as $rowsss) ;
				$remarkssssss=$rowsss->remarks;
				$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
				$addedby=$this->salescrm->getusername($rowsss->addedBy);
			}
			if($row->bom_technical_approved==1)
			{
			$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

			}else if($row->bom_technical_approved==2)
			{
			$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
			}else{ $status='';
			}

			$missingdatacheck=$this->BOM_model->checkformissingcaddata($row->id);
			$services=$this->BOM_model->checkservices($row->id);

			$resteyy=$this->db->select('design_req_chk')
							  ->from('bom_pi_sales_project_info')
							  ->where('lead_id',$row->id)
							  ->get();
			if($resteyy->num_rows()>0)
			{
			    foreach($resteyy->result() as $rowss);

			    $lab = '';
			    if($rowss->design_req_chk==0 || !(in_array(4, $services)))
			    {
			        $lab="PPT";
			        
			    

			
			if($missingdatacheck>0)
			{

			$addeddate=$this->BOM_model->getaddeddate($row->id);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'pendingdata'=>"<strong style='color:red'>".$lab."</strong>",
								'viewbom'=>$edit,
								'pendingsince'=>$addeddate
								);
						$i++;

			}

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
				}
			    // else
			    // {
			    //      $lab="CAD Data";
			    //     $type="1";
			    // }

			}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}

	public function pending_for_sourcing()
	{
		$this->load->view('leads/pending_for_sourcing');
	}
	public function pending_for__business_sourcing()
	{
		$this->load->view('leads/pending_for_business_sourcing');
	}

	function meeting_followup()
	{
		$this->load->view('leads/meeting_followup');
	}

	
	public function meeting_followup_list() {

		$lead_data = array();
		if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['role']==31)
		{
			$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
			$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

			if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
				$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
			}else{
				$between="";
			}

			if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
			$member_id = $this->uri->segment(3);
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14') AND b.closed=0 AND c.member_id='$member_id' $between ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14') AND b.closed=0 $between ORDER BY b.id DESC");
		}
		}else
		{
			$user_id=$_SESSION['logged_in']['user_id'];
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14') AND b.closed=0 AND c.member_id='$user_id' ORDER BY b.id DESC");
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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



function pending_bom_for_missing_cad_data_list_history()
{

	$lead_data = array();
	// echo $this->uri->segment(3);exit;
			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member' ORDER BY b.id DESC");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 ORDER BY b.id DESC");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			//$edit = "<a href='".page_url."BOM/edit_bom1233/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			  $edit = "<a href='".page_url."BOM/caddatauploaded/".$row->id."' class='btn btn-success'>Uploaded CAD Data</a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$getProjectName=$this->BOM_model->getProjectName($row->id);

$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

$remarkssssss='';
$addedon='';
$addedby='';
$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
if($rowss->num_rows()>0)
{
	foreach($rowss->result() as $rowsss) ;
	$remarkssssss=$rowsss->remarks;
	$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
	$addedby=$this->salescrm->getusername($rowsss->addedBy);
}
if($row->bom_technical_approved==1)
{
$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

}else if($row->bom_technical_approved==2)
{
$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
}else{ $status='';
}

$missingdatacheck=$this->BOM_model->checkformissingcaddata($row->id);
$services=$this->BOM_model->checkservices($row->id);

$resteyy=$this->db->select('design_req_chk')->from('bom_pi_sales_project_info')->where('lead_id',$row->id)->get();
if($resteyy->num_rows()>0)
{
    foreach($resteyy->result() as $rowss);

    $lab = '';
    if($rowss->design_req_chk==1 || (in_array(4, $services)))
    {
        $lab="CAD DATA";
        


			
			if($missingdatacheck==0)
			{

			$addeddate=$this->BOM_model->getaddeddate($row->id);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'pendingdata'=>"<strong style='color:red'>".$lab."</strong>",
								'viewbom'=>$edit,
								'pendingsince'=>$addeddate
								);
						$i++;

			}

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
				}
		    // else
		    // {
		    //      $lab="CAD Data";
		    //     $type="1";
		    // }

		}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}


	function pending_bom_for_missing_ppt_data_list_history()
{

	$lead_data = array();
	// echo $this->uri->segment(3);exit;
			if($this->uri->segment(3) != '') {
				$team_member = $this->uri->segment(3);
				// echo $team_member;exit;
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 AND c.member_id='$team_member' ORDER BY b.id DESC");
			} else {
				$query = $this->db->query("SELECT b.bom_technical_approved,c.member_id,b.id,b.customer_name,b.email_id,b.country,b.city,b.state,b.company_name,b.country_code,b.lead_source_id,b.contact_no,b.country_code,b.create_date,b.unique_id, b.patient_type_id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8','11') AND b.bom_created=1 ORDER BY b.id DESC");
			}
		
		$i=1;
		if($query->num_rows() > 0) {
		
		foreach($query->result() as $row) {

			$pis = "<a href='".page_url1."bom_pdf/rfq/examples/client.php?lead_id=".$row->id."' target='_blank' class='btn btn-danger btn-xs' target='_blank'>VIEW BOM</a>";

			$patient_type = $this->salescrm->getClientTypebyid($row->patient_type_id);
			
			//$edit = "<a href='".page_url."BOM/edit_bom1233/".$row->id."/R'><i class='fa fa-pencil'></i></a>";
			  $edit = "<a href='".page_url."BOM/caddatauploaded/".$row->id."' class='btn btn-success'>Uploaded Data</a>";
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			$getleadmanager=$this->salescrm->getusername($row->member_id);
			$country=$this->salescrm->getcountry($row->country);
			$state=$this->salescrm->getstate($row->state);
			$getProjectName=$this->BOM_model->getProjectName($row->id);

			$approve = "<span onclick='showmodal(".$row->id.")' class='btn btn-warning btn-xs'>Approve/Reject</span>";

			$remarkssssss='';
			$addedon='';
			$addedby='';
			$rowss=$this->db->select('remarks,addedOn,addedBy')->from('bom_technical_team_remarks')->where('lead_id',$row->id)->order_by('id','desc')->limit(1)->get();
			if($rowss->num_rows()>0)
			{
				foreach($rowss->result() as $rowsss) ;
				$remarkssssss=$rowsss->remarks;
				$addedon=date('d-M-Y H:i:s',strtotime($rowsss->addedOn));
				$addedby=$this->salescrm->getusername($rowsss->addedBy);
			}
			if($row->bom_technical_approved==1)
			{
			$status="<strong style='color:green'>Approved</strong><br/>Remarks-".$remarkssssss;

			}else if($row->bom_technical_approved==2)
			{
			$status="<strong style='color:red'>Rejected</strong><br/>Remarks-".$remarkssssss;
			}else{ $status='';
			}

			$missingdatacheck=$this->BOM_model->checkformissingcaddata($row->id);
			$services=$this->BOM_model->checkservices($row->id);

			$resteyy=$this->db->select('design_req_chk')
							  ->from('bom_pi_sales_project_info')
							  ->where('lead_id',$row->id)
							  ->get();
			if($resteyy->num_rows()>0)
			{
			    foreach($resteyy->result() as $rowss);

			    $lab = '';
			    if($rowss->design_req_chk==0 || !(in_array(4, $services)))
			    {
			        $lab="PPT";
			        
			    

			
			if($missingdatacheck==0)
			{

			$addeddate=$this->BOM_model->getaddeddate($row->id);

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique_id'=>$row->unique_id."<br/>".date('d-m-Y', strtotime($row->create_date)),
								'leadsource'=>$getProjectName,
								'customer_name' =>  $country."<br>".$state."<br>".$row->city,
								'customer_type' => $patient_type,
								'company'=>$row->company_name,
								'leadmanager' =>$getleadmanager,
								'pendingdata'=>"<strong style='color:red'>".$lab."</strong>",
								'viewbom'=>$edit,
								'pendingsince'=>$addeddate
								);
						$i++;

			}

				$results = array(
							"sEcho" => 1,
							"iTotalRecords" => count($lead_data),
							"iTotalDisplayRecords" => count($lead_data),
							"aaData"=>$lead_data);
				}
				}
			    // else
			    // {
			    //      $lab="CAD Data";
			    //     $type="1";
			    // }

			}
			} else {
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			}
			
		echo json_encode($results);

}

	function get_products() {
		$html = '';
		$company_location = $this->input->post('company_location');

		$query = $this->db->select('b.id, b.instruments_name')
		 				  ->from('company_products a')
		 				  ->join('presto_instruments b', 'b.id=a.product_id')
						  ->where('a.company_id', $company_location)
						  ->get();

			if($query->num_rows()>0) {
				$html .= "<option value=''>SELECT PRODUCT</option>";
				foreach($query->result() as $row) {
					$html .= "<option value='".$row->id."'>".$row->instruments_name."</option>";
				}
			} 
				
		echo $html;
	}



	function wonleads()
	{
		$this->load->view('leads/wonleads');
	}
	public function won_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('6') AND b.closed=0 AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('6') AND b.closed=0 $between  GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('6') AND b.closed=0 AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('6') AND b.closed=0 AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$read='';
			if($contactacess>0){


			$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
			}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,	
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	function deadleads()
	{
		$this->load->view('leads/deadleads');
	}
	public function dead_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2') AND b.closed=0 AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2') AND b.closed=0 $between GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2') AND b.closed=0 AND c.member_id IN ($team_member) GROUP BY b.id  ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('2') AND b.closed=0 AND c.member_id='$user_id' GROUP BY b.id  ORDER BY b.id DESC");
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	function unqualifiedleads()
	{
		$this->load->view('leads/unqualifiedleads');
	}
	public function unqualified_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('11')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('11')  $between GROUP BY b.id  ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('11')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('11')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,		
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	function pisentleads()
	{
		$this->load->view('leads/pisentleads');
	}
	public function pisent_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('3')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('3')  $between GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('3')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('3')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");
			}
			
		}
		
		$i=1; 
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$read='';
			if($contactacess>0){


			$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
			}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	function contactedleads()
	{
		$this->load->view('leads/contactedleads');
	}
	public function contacted_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('10')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('10')  $between GROUP BY b.id  ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('10')  AND c.member_id IN ($team_member) GROUP BY b.id  ORDER BY b.id DESC");
			}
			else
			{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('10')  AND c.member_id='$user_id' GROUP BY b.id  ORDER BY b.id DESC");	
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	function quotationsentleads()
	{
		$this->load->view('leads/quotationsentleads');
	}
	
	
	
	public function quotation_sent_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('8')  $between GROUP BY b.id  ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);
				$team_member="'" . implode ( "', '", $emp ) . "'";

			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8')  AND c.member_id IN($team_member) GROUP BY b.id  ORDER BY b.id DESC");
			}else
			{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('8')  AND c.member_id='$user_id' GROUP BY b.id  ORDER BY b.id DESC");	
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
$read='';
		if($contactacess>0){

		
$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	function attempttocontactleads()
	{
		$this->load->view('leads/attempttocontactleads');
	}
	public function attempt_to_contact_leads_list() {

		$lead_data = array();

		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('9')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('9')  $between GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('9')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}else
			{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('9')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");	
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	function pifollowupleads()
	{
		$this->load->view('leads/pifollowupleads');
	}
	public function pi_followup_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('12')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('12')  $between GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";

			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('12')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('12')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");	
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$read='';
			if($contactacess>0){


			$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
			}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	function totalactiveleads()
	{
		$this->load->view('leads/totalactiveleads');
	}
	public function total_active_leads_list() {

    	$company_id=$_SESSION['logged_in']['business_location'];
		$lead_data = array();
		$role=$_SESSION['logged_in']['role'];
		$mode = $this->getsettings();
		if(count($mode)>0){
			$mode = $mode['mode'];
		}else{
			$mode="1";
		}
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$getDeadEndLeadStage = $this->salescrm->getDeadEnd_closeEndLeadStage();
		if(count($getDeadEndLeadStage)>0)
		{
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		}else
		{
			$dead_end_lead_stage='';
		}

		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==1)
		{
		    

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

	

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);

		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage)  AND c.member_id='$member_id' AND b.company_id='$company_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage  NOT IN ($dead_end_lead_stage)  $between AND b.company_id='$company_id' GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
				//$sd = "'" . implode ( "', '", $username ) . "'";
			//echo	$team_member; exit;
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage)  AND c.member_id IN ('$user_id',$team_member)  AND b.company_id='$company_id' GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				if($mode==1)
				{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage)  AND c.member_id='$user_id' AND b.company_id='$company_id' GROUP BY b.id ORDER BY b.id DESC");	
				}else
				{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage)  AND c.user_id='$user_id' AND c.role_id='$role' AND b.company_id='$company_id' GROUP BY b.id ORDER BY b.id DESC");	

				}
			}
			
		}
		
		$i=1;
		//echo "<pre>"; print_r($resty->result()); exit;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';


	if($mode==1)
{

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->leadid)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}
}else
{

$assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
$fname='';
$lname='';

}
	
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$lead_stage=$this->salescrm->getlateststatus($row->leadid);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

		 
		$user_id=$_SESSION['logged_in']['user_id'];
		$read='';
		if($contactacess>0){

		
$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}


			$lead_data[] = array(
								'sr_no'=>$i,
								'lead_stage'=>$lead_stage,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

// 	function getsettings()
// {
// 	$setarray=array();
// 				$sql = $this->db->select('mode,port,password,email,outgoing,pass,cc_email,user_name,password,quotation_name,pi_name,emailer, unsubscribe')
// 				->from('setting_master_view')
// 				->where('id',$_SESSION['logged_in']['business_location'])
// 				->get();

// 				if($sql->num_rows() > 0) {
// 				foreach ($sql->result() as $row);
// 				$setarray['port'] = $row->port;
// 				$setarray['password'] = $row->password;
// 				$setarray['email_smtp'] = $row->email;
// 				$setarray['email_outgoing']=$row->outgoing;
// 				$setarray['email_pass']=$row->pass;
// 				$setarray['email_ccmailid']=$row->cc_email;
// 				$setarray['whatsappuser']=$row->user_name;
// 				$setarray['whatsapppassword']=$row->password;
// 				$setarray['quotefile']=$row->quotation_name;
// 				$setarray['pifile']=$row->pi_name;
// 				$setarray['emailer']=$row->emailer;
// 				$setarray['unsubscribe']=$row->unsubscribe;
// 				$setarray['mode']=$row->mode;
// 				} 

// 				return $setarray;


// }


function quotationrevisedleads()
	{
		$this->load->view('leads/quotationrevisedleads');
	}
	
	
	
		public function quotation_revised_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);



		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('14')  $between GROUP BY b.id  ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";

			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14')  AND c.member_id IN ($team_member) GROUP BY b.id  ORDER BY b.id DESC");
			}else
			{
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('14')  AND c.member_id='$user_id' GROUP BY b.id  ORDER BY b.id DESC");
			}
			
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$quotation_name = $this->salescrm->getsettings();
		if(count($quotation_name) >0)
		{
			$quotefile=$quotation_name['quotefile'].'.php';
			$pifile=$quotation_name['pifile'].'.php';
		}else{
		$quotefile='';
			$pifile='';	
		}

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
$quotelink=page_url1.'pdf/rfq/examples/'.$quotefile.'?lead_id='.$row->leadid;
$quoteview = "<a href='".$quotelink."' class='btn btn-warning btn-xs' target='_blank'>Preview Quote</a>";

			$read='';
			if($contactacess>0){


			$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
			}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'sendquotation'=>$quoteview,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	
	
		function pisent_revisedleads()
	{
		$this->load->view('leads/pisent_revisedleads');
	}
	
	
		public function pisent_revised_leads_list() {

		$lead_data = array();
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{

		$start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
		$end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="";
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);

		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('15')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		} else {
		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND lead_stage IN ('15')  $between GROUP BY b.id ORDER BY b.id DESC");
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			//echo $userstatus; exit;
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('15')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('15')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");
			}
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('15')  AND c.member_id='$user_id' GROUP BY b.id ORDER BY b.id DESC");
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);

$quotation_name = $this->salescrm->getsettings();
		if(count($quotation_name) >0)
		{
			$quotefile=$quotation_name['quotefile'].'.php';
			$pifile=$quotation_name['pifile'].'.php';
		}else{
		$quotefile='';
			$pifile='';	
		}


$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
$quotelink=page_url1.'pdf/rfq/examples/'.$pifile.'?lead_id='.$row->leadid;
$quoteview = "<a href='".$quotelink."' class='btn btn-warning btn-xs' target='_blank'>Preview PI</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$getleadmanager,
								'pisent'=>$quoteview,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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

	public function lead_form()
	{
		$this->load->view('leads/lead_form');
	}
	
	function monthlysale()
	{
	    $this->load->view('leads/leadsdata');
	}
	
	
	
		function monthlyclosedwonorderdata()
        {
            
			$mode=$this->getsettings();
			if(count($mode)>0)
			{
			$mode=$mode['mode'];
			}else
			{	$mode=1;
			}


            $lead_stage=$this->dashboardmodel->getConversionLeadStage();
            $lead_data=array();
            $start_date=date('Y-m-01')." 00:00:00";
            $end_date=date('Y-m-t')." 23:59:59";
            $user_id=$_SESSION['logged_in']['user_id'];          
            $contactacess=$this->Lead_model->checkforcontactacess($user_id);	
         if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
		{
		if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
		    
            $start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
            $end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="AND (b.added_on BETWEEN '".$start_date."' AND '".$end_date."');";	
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);
			$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		
        }else{
            
		
		 $resty = $this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage') AND a.added_on BETWEEN '".$start_date. "' and '". $end_date."' GROUP BY b.id ");
		}
		
		} else {
               

                $userstatus=$this->Lead_model->teamleadersearch($user_id);
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);
				$team_member="'" . implode ( "', '", $emp ) . "'";

				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage')  AND c.member_id IN ($team_member)  GROUP BY b.id ORDER BY b.id DESC");

            }else
            {
            	if($mode==1)
            	{
            	$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage')  AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");
            	}else
            	{
					$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage')  AND c.user_id=$user_id GROUP BY b.id ORDER BY b.id DESC");


            	}
            }
            
		
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);



	$a=1;
	$html='';
// 	if($mode==2){
// 		$qe=$this->db->select('a.user_id,b.first_name,b.last_name, c.user_role')->from('assigned_users_for_lead a')->join('system_users b','a.user_id=b.user_id','left')->join('user_role c','a.role_id=c.user_role_id','left')->where('a.lead_id',$row->leadid)->group_by('c.user_role')->get();
// 	if($qe->num_rows()>0){	
// $html.="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Role</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Name</th><tbody><tr>";


// if($qe->num_rows()>0)

// {

// foreach($qe->result() as $qer)

// {

// $user =	ucwords(strtolower($qer->first_name." ".$qer->last_name.'<br/>'));
// $html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$a."</td>
// <td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$qer->user_role."</td>
// <td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$user."</td></tr>";	
// $a++;
// }				

// }

// $html.="</table>";
// 	}
// $getleadmanager= $html;
		
		
// 	}else{
		
		

// 	$getleadassignedto=$this->salescrm->getleadassigned($row->lead_id);
// 	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
// 	}

		if($mode==1)
{

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->leadid)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}
			}else
			{

			$assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
			$fname='';
			$lname='';

			}


	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$oddetail=$this->salescrm->getorder_details($row->leadid);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'orderdetail'=>$oddetail,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	
	
	
	function yearlysale()
	{
	    $this->load->view('leads/yearlyleadsdata');
	}
	
	
	
		function yearlyclosedwonorderdata()
        {
            
            $leadstage=$this->dashboardmodel->getConversionLeadStage();
            $lead_data=array();
              $curr_date_month = date('m');
               $user_id=$_SESSION['logged_in']['user_id'];          
            $contactacess=$this->Lead_model->checkforcontactacess($user_id);
            $calculate_fiscal_year_for_date = $this->calculateFiscalYearForDate($curr_date_month);
            $cal=explode(':',$calculate_fiscal_year_for_date);
            if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
			{
            $start_date = date('Y-m-d',strtotime($cal[0]))." 00:00:00";
            $end_date = date('Y-m-d',strtotime($cal[1]))." 23:59:59";
        	$lead_data = array();
			if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
		    
            $start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
            $end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="AND (b.added_on BETWEEN '".$start_date."' AND '".$end_date."');";	
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);


            
		$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		
		
		} else {
                $resty = $this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage') AND a.added_on BETWEEN '".$start_date. "' and '". $end_date."' GROUP BY b.id ");
            
		}
		}else
		{
			 $userstatus=$this->Lead_model->teamleadersearch($user_id);
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);
				$team_member="'" . implode ( "', '", $emp ) . "'";

				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id IN($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id=$user_id  GROUP BY b.id ORDER BY b.id DESC");
			}
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$oddetail=$this->salescrm->getorder_details($row->leadid);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'customer_type'=>$clienttype,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'orderdetail'=>$oddetail,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	
	
		
	function calculateFiscalYearForDate($month)
{
if($month > 4)
{
$y = date('Y');
$pt = date('Y', strtotime('+1 year'));
$fy = $y."-04-01".":".$pt."-03-31";
}
else
{
$y = date('Y', strtotime('-1 year'));
$pt = date('Y');
$fy = $y."-04-01".":".$pt."-03-31";
}
return $fy;
}
	
	
	
	
		function weeklysale()
	{
	    $this->load->view('leads/weeklyleadsdata');
	}
	
	
	
		function weeklyclosedwonorderdata()
        {
        	
        	$mode=$this->getsettings();
		if(count($mode)>0)
		{
			$mode=$mode['mode'];
		}else
		{	$mode=1;
		}


        	$leadstage=$this->dashboardmodel->getConversionLeadStage();
            
        $lead_data=array();
        $monday = strtotime("last monday");
        $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
        $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
        $start_date = date("Y-m-d",$monday);
        $end_date = date("Y-m-d",$sunday);

         $user_id=$_SESSION['logged_in']['user_id'];          
            $contactacess=$this->Lead_model->checkforcontactacess($user_id);
            if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
			{
        	$lead_data = array();
			if($this->uri->segment(4) != '' && $this->uri->segment(5) != ''){
		    
            $start_date=date('Y-m-d', strtotime($this->uri->segment(4)))." 00:00:00";
            $end_date=date('Y-m-d', strtotime($this->uri->segment(5)))." 23:59:59";

			$between="AND (b.added_on BETWEEN '$start_date' AND '$end_date');";	
		}else{
			$between="AND (b.added_on BETWEEN '".$start_date."' AND '".$end_date."');";	
		}

		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);
            
		$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id='$member_id' $between GROUP BY b.id ORDER BY b.id DESC");
		
		
		} else {
                $resty = $this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage') AND a.added_on BETWEEN '".$start_date. "' and '". $end_date."' GROUP BY b.id ");
            
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);
				$team_member="'" . implode ( "', '", $emp ) . "'";

				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				if($mode==1)
				{
				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.member_id = $user_id GROUP BY b.id ORDER BY b.id DESC");
				}else
				{
 				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$leadstage')  AND c.user_id = $user_id GROUP BY b.id ORDER BY b.id DESC");	

				}
			}
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);



	$a=1;
	$html='';
	if($mode=='2'){
		$html='';
		$qe=$this->db->select('a.user_id,b.first_name,b.last_name, c.user_role')->from('assigned_users_for_lead a')->join('system_users b','a.user_id=b.user_id','left')->join('user_role c','a.role_id=c.user_role_id','left')->where('a.lead_id',$row->leadid)->group_by('c.user_role')->get();
	if($qe->num_rows()>0){	
$html.="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Role</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Name</th><tbody><tr>";


if($qe->num_rows()>0)

{

foreach($qe->result() as $qer)

{

$user =	ucwords(strtolower($qer->first_name." ".$qer->last_name.'<br/>'));
$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$a."</td>
<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$qer->user_role."</td>
<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$user."</td></tr>";	
$a++;
}				

}

$html.="</table>";
	}
$getleadmanager= $html;
		
		
	}else{
		
		

	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	}




	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$oddetail=$this->salescrm->getorder_details($row->leadid);

	$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			$read='';
		if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/><a href="mailto:'.$row->email_id.'">'.$row->email_id.'</a><br/><a href="https://api.whatsapp.com/send/?phone='.$row->country_code.$row->contact_no.'" target="_blank">'.$row->contact_no.'</a><br/>'.$country."<br>".$state."<br>".$row->city,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'orderdetail'=>$oddetail,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	
	
	
	function closurepending()
	{
	    $this->load->view('leads/closurepending');
	}
	
	
	
		function pisentclosurepending()
        {

        	$mode=$this->getsettings();
		if(count($mode)>0)
		{
			$mode=$mode['mode'];
		}else
		{	$mode=1;
		}

            
	        $lead_data=array();
        	$lead_data = array();
	        $between='';

			$pi_sent_lead_stage = $this->salescrm->getPiSentLeadStage();	
        	$user_id=$_SESSION['logged_in']['user_id'];          
            $contactacess=$this->Lead_model->checkforcontactacess($user_id);
            if($_SESSION['logged_in']['role']==1 ||$_SESSION['logged_in']['role']==31)
			{
		if($this->uri->segment(3) != '' && $this->uri->segment(3) !=0) {
		$member_id = $this->uri->segment(3);

		
            
		$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($pi_sent_lead_stage)  AND c.member_id='$member_id' GROUP BY b.id ORDER BY b.id DESC");
		
		
		} else {
                $resty = $this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($pi_sent_lead_stage) GROUP BY b.id ");
            
		}
		}else
		{
			$userstatus=$this->Lead_model->teamleadersearch($user_id);
			if($userstatus==1)
			{
				$emp=$this->Lead_model->getteamdetails($user_id);
				$team_member="'" . implode ( "', '", $emp ) . "'";

				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($pi_sent_lead_stage)  AND c.member_id IN ($team_member) GROUP BY b.id ORDER BY b.id DESC");
			}
			else
			{
				if($mode==1)
				{
				$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($pi_sent_lead_stage)  AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");
				}else
				{
					$resty=$this->db->query("SELECT b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ($pi_sent_lead_stage)  AND c.user_id=$user_id GROUP BY b.id ORDER BY b.id DESC");
				}
			}
		}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);


	$a=1;
	$html='';
	if($mode=='2'){
		$html='';
		$qe=$this->db->select('a.user_id,b.first_name,b.last_name, c.user_role')->from('assigned_users_for_lead a')->join('system_users b','a.user_id=b.user_id','left')->join('user_role c','a.role_id=c.user_role_id','left')->where('a.lead_id',$row->leadid)->group_by('c.user_role')->get();
	if($qe->num_rows()>0){	
$html.="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Role</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Name</th><tbody><tr>";


if($qe->num_rows()>0)

{

foreach($qe->result() as $qer)

{

$user =	ucwords(strtolower($qer->first_name." ".$qer->last_name.'<br/>'));
$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$a."</td>
<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$qer->user_role."</td>
<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$user."</td></tr>";	
$a++;
}				

}

$html.="</table>";
	}
$getleadmanager= $html;
		
		
	}else{
		
		

	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
	$getleadmanager=$this->salescrm->getusername($getleadassignedto);
	}




	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	$oddetail=$this->salescrm->getorder_details($row->leadid);

$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		$read='';
		if($contactacess>0){

		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		}
			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'orderdetail'=>$oddetail,
								'leadmanager'=>$getleadmanager,
								'lastupdatedon'=>date('d-m-y',strtotime($row->create_date)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
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
	public function missed_followup_up()
	{
	$this->load->view('leads/missed_follow_up');	
	}
	public function missed_followup_list_app(){

		// $mode=$this->getsettings();
		// if(count($mode)>0)
		// {
		// 	$mode=$mode['mode'];
		// }else
		// {
		// 	$mode=1;
		// }

	// 				$conversion_lead_stage = $this->getConversionLeadStage();
	// 			$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
	// 			array_push($getDeadEndLeadStage, $conversion_lead_stage);
	// 			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

	// 			$cur=date('Y-m-d');
	// 		$lead_type_list = array();
	// 		$user_id=$_SESSION['logged_in']['user_id'];
	// 		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
	
	// 		$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_stage,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur' GROUP BY a.lead_id");
			
	// 		if($resty->num_rows()>0)
	// 		{
	// 			$i=1;
	// 			foreach ($resty->result() as $row)
	// 			{
	// 				$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					
					

	// 									$a=1;
	// 									$html='';
									

	// 				// if($mode==1)
	// 				// {

	// 							$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
	// 											  ->from('lead_assigned_to_team a')
	// 											  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
	// 											  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
	// 											  ->join('system_users d','d.user_id=c.member_id','left')
	// 											  ->where('a.lead_id',$row->lead_id)
	// 											  ->get();

	// 							$res = $query->result();
	// 							if($res){
	// 								foreach($res as $assign_information)
	// 								{
	// 								$assignement = 	$assign_information->team_name;
	// 								$fname = 	$assign_information->first_name;
	// 								$lname = 	$assign_information->last_name;
	// 								}
									
	// 							}else{
	// 							$assignement = "";
	// 							$fname = '';
	// 							$lname = '';
	// 							}

	// 							$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->lead_id)->get();
	// 							if($restsyt->num_rows()>0)
	// 							{
	// 								foreach($restsyt->result() as $restsyt);

	// 								$fname=$this->salescrm->getusername($restsyt->member_id);
	// 								$lname='';
	// 							}else
	// 							{
	// 								$fname='';
	// 								$lname='';
	// 							}
	// 				// }else
	// 				// {

	// 				// $assignement=$this->Lead_model->getleadmanagerformulti($row->lead_id,$row->client_location);
	// 				// $fname='';
	// 				// $lname='';

	// 				// }


	// 					$view = "<a href='".page_url."Leads/view_detail/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	// 					$country=$this->salescrm->getcountry($row->country);
	// 				$state=$this->salescrm->getstate($row->state);
	// 			$read='';
	// 			if($contactacess>0){


	// 			$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
	// 			}

					
	// 				$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
	// 				$leadstatus=$this->salescrm->getLeadStatusonly($row->lead_id);
	// 				$lead_type_list[] = array(
	// 							'sr_no'=>$i,
	// 							'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
	// 							'company' => $row->company_name,
	// 							'customer_name' => $row->customer_name.'<br/>'.$read,
	// 							'customer_type' => $clienttype,
	// 							'location' => $clienttype,
	// 							'email' =>'' ,
	// 							'mobile' => $row->contact_no,
	// 							'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
	// 							'location' => '',
	// 							'lastleadstatus'=>$leadstatus,
	// 							'lastremarks'=>$row->remarks,
	// 							'leadmanager'=>$assignement."<br>".$fname." ".$lname,
	// 							'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
	// 							'update'=>$view,
	// 							'leadsource'=>$leadsource
						
	// 							);
	// 				$i++;
	// 				}
	// 		}
			
	// $results = array(
	// 								"sEcho" => 1,
	// 							    "iTotalRecords" => count($lead_type_list),
	// 								"iTotalDisplayRecords" => count($lead_type_list),
	// 								"aaData"=>$lead_type_list);
			
	// 	echo json_encode($results);

		$i=1;
		$lead_data= array();
       	$user_id=$_SESSION['logged_in']['user_id'];
       	$company_id=$_SESSION['logged_in']['business_location'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		if($dead_end_lead_stage<>'')
		{
		    $dead_end_lead_stage=$dead_end_lead_stage;
		}else
		{
		 $dead_end_lead_stage="'0'";
		}
		

		$q=$this->db->query("SELECT a.lead_status,a.next_follow_date,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city,b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) GROUP BY b.id ORDER BY b.id DESC");
		
		foreach($q->result() as $row){
		
				

				$lastfollowupdate=$row->next_follow_date;
				$status = $row->lead_stage;

				// if($status!=2 && $status!=5 && $status!=6){

				if($lastfollowupdate<date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				//$followup = "<a href='".page_url."Quotation1/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
				$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);


			$a=1;
	$html='';


						$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
										  ->from('lead_assigned_to_team a')
										  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
										  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
										  ->join('system_users d','d.user_id=c.member_id','left')
										  ->where('a.lead_id',$row->leadid)
										  ->get();

						$res = $query->result();
						if($res){
							foreach($res as $assign_information)
							{
							$assignement = 	$assign_information->team_name;
							$fname = 	$assign_information->first_name;
							$lname = 	$assign_information->last_name;
							}
							
						}else{
						$assignement = "";
						$fname = '';
						$lname = '';
						}

						$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
						if($restsyt->num_rows()>0)
						{
							foreach($restsyt->result() as $restsyt);

							$fname=$this->salescrm->getusername($restsyt->member_id);
							$lname='';
						}else
						{
							$fname='';
							$lname='';
						}

			// $assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
			// $fname='';
			// $lname='';
	



				$country=$this->salescrm->getcountry($row->country);
				$state=$this->salescrm->getstate($row->state);
				$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


				$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
				$read='';
				$altrcontact='';
				if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		$altrcontact="'".$row->contact_person.'"<br/>"'.$row->email.'"<br/>"'.$row->alt_contact_no."'";
		}
		$leadstatus=$this->salescrm->getLeadStatusonly($row->leadid);
				$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$altrcontact,
								'lastleadstatus'=>$leadstatus,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-Y',strtotime($row->create_date)),
								'update'=>$view,
								'followupschedule'=>"<strong style='color:red;font-weight:bold;'>".date('d-m-Y',strtotime($row->next_follow_date))."</strong>",
								'leadsource'=>$leadsource
						
								);
						$i++;
				}
				//}

		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			echo json_encode($results);
	}

	public function upcomeingfollow()
	{
		$this->load->view('leads/upcoming_followups_new');
	}
	public function upcomeingfollow_list()
	{

		$i=1;
		$lead_data= array();
       	$user_id=$_SESSION['logged_in']['user_id'];
       	$company_id=$_SESSION['logged_in']['business_location'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		if($dead_end_lead_stage<>'')
		{
		    $dead_end_lead_stage=$dead_end_lead_stage;
		}else
		{
		 $dead_end_lead_stage="'0'";
		}
		

		$q=$this->db->query("SELECT a.lead_status,a.next_follow_date,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city,b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) GROUP BY b.id ORDER BY b.id DESC");
		
		foreach($q->result() as $row){
		
				

				$lastfollowupdate=$row->next_follow_date;
				$status = $row->lead_stage;

				// if($status!=2 && $status!=5 && $status!=6){

				if($lastfollowupdate>date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{

				//$followup = "<a href='".page_url."Quotation1/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
				$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);


			$a=1;
	$html='';


						$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
										  ->from('lead_assigned_to_team a')
										  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
										  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
										  ->join('system_users d','d.user_id=c.member_id','left')
										  ->where('a.lead_id',$row->leadid)
										  ->get();

						$res = $query->result();
						if($res){
							foreach($res as $assign_information)
							{
							$assignement = 	$assign_information->team_name;
							$fname = 	$assign_information->first_name;
							$lname = 	$assign_information->last_name;
							}
							
						}else{
						$assignement = "";
						$fname = '';
						$lname = '';
						}

						$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
						if($restsyt->num_rows()>0)
						{
							foreach($restsyt->result() as $restsyt);

							$fname=$this->salescrm->getusername($restsyt->member_id);
							$lname='';
						}else
						{
							$fname='';
							$lname='';
						}

			// $assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
			// $fname='';
			// $lname='';
	



				$country=$this->salescrm->getcountry($row->country);
				$state=$this->salescrm->getstate($row->state);
				$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


				$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
				$read='';
				$altrcontact='';
				if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		$altrcontact="'".$row->contact_person.'"<br/>"'.$row->email.'"<br/>"'.$row->alt_contact_no."'";
		}
		$leadstatus=$this->salescrm->getLeadStatusonly($row->leadid);
				$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$altrcontact,
								'lastleadstatus'=>$leadstatus,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-Y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'followupschedule'=>"<strong style='color:red;font-weight:bold;'>".date('d-m-Y',strtotime($lastfollowupdate))."</strong>",
								'leadsource'=>$leadsource
						
								);
						$i++;
				}
				//}

		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			echo json_encode($results);
	}


	public function lead_calculator(){

	$this->load->view('dashboard/lead_calculator');

}

	private function can_view_all_stage_leads()
	{
		$role = (int) $_SESSION['logged_in']['role'];
		return in_array($role, array(12, 41), true);
	}

	private function normalize_stage_filter_date($date_value)
	{
		$date_value = trim((string) $date_value);
		if ($date_value === '') {
			return '';
		}

		$formats = array('d-m-Y', 'Y-m-d');
		foreach ($formats as $format) {
			$date = DateTime::createFromFormat($format, $date_value);
			if ($date instanceof DateTime && $date->format($format) === $date_value) {
				return $date->format('Y-m-d');
			}
		}

		return '';
	}

	private function get_stage_filter_values($route_source = '', $route_exhibition = '', $route_manager = '')
	{
		$source_id = trim((string) $this->input->get('source_id', true));
		$exhibition_id = trim((string) $this->input->get('exhibition_id', true));
		$manager_id = trim((string) $this->input->get('manager_id', true));
		$start_date_input = trim((string) $this->input->get('start_date', true));
		$end_date_input = trim((string) $this->input->get('end_date', true));

		if ($source_id === '' && $route_source !== '' && $route_source !== 'ALL') {
			$source_id = $route_source;
		}

		if ($exhibition_id === '' && $route_exhibition !== '' && $route_exhibition !== 'ALL') {
			$exhibition_id = $route_exhibition;
		}

		if ($manager_id === '' && $route_manager !== '' && $route_manager !== 'ALL') {
			$manager_id = $route_manager;
		}

		if (!ctype_digit((string) $source_id)) {
			$source_id = '';
		}

		if (!ctype_digit((string) $exhibition_id)) {
			$exhibition_id = '';
		}

		if (!$this->can_view_all_stage_leads() || !ctype_digit((string) $manager_id)) {
			$manager_id = '';
		}

		$start_date = $this->normalize_stage_filter_date($start_date_input);
		$end_date = $this->normalize_stage_filter_date($end_date_input);

		if ($start_date !== '' && $end_date !== '' && strtotime($start_date) > strtotime($end_date)) {
			$temp = $start_date;
			$start_date = $end_date;
			$end_date = $temp;
		}

		return array(
			'source_id' => $source_id,
			'exhibition_id' => $exhibition_id,
			'manager_id' => $manager_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'start_date_input' => $start_date !== '' ? date('d-m-Y', strtotime($start_date)) : $start_date_input,
			'end_date_input' => $end_date !== '' ? date('d-m-Y', strtotime($end_date)) : $end_date_input,
		);
	}

	private function get_stage_filter_query_string($filters)
	{
		$query = array();

		if (!empty($filters['start_date_input'])) {
			$query['start_date'] = $filters['start_date_input'];
		}

		if (!empty($filters['end_date_input'])) {
			$query['end_date'] = $filters['end_date_input'];
		}

		if (!empty($filters['source_id'])) {
			$query['source_id'] = $filters['source_id'];
		}

		if (!empty($filters['exhibition_id'])) {
			$query['exhibition_id'] = $filters['exhibition_id'];
		}

		if (!empty($filters['manager_id']) && $this->can_view_all_stage_leads()) {
			$query['manager_id'] = $filters['manager_id'];
		}

		return http_build_query($query);
	}

	private function get_stage_filter_options()
	{
		$options = array(
			'lead_sources' => $this->db->select('source_id, lead_source')->from('lead_source')->order_by('lead_source', 'ASC')->get()->result(),
			'exhibitions' => $this->db->select('id, exhibition')->from('exhibition_info')->order_by('exhibition', 'ASC')->get()->result(),
			'managers' => array(),
		);

		if ($this->can_view_all_stage_leads()) {
			$options['managers'] = $this->db
				->select('user_id, title, first_name, last_name')
				->from('system_users')
				->where('user_status', 1)
				->where('department_id', 9)
				->order_by('first_name', 'ASC')
				->get()
				->result();
		}

		return $options;
	}

	private function get_stage_filtered_rows($lead_stage, $filters, $route_manager = '')
	{
		$lead_stage = (int) $lead_stage;
		$current_user_id = (int) $_SESSION['logged_in']['user_id'];
		$effective_manager_id = '';

		if ($this->can_view_all_stage_leads()) {
			if (!empty($filters['manager_id'])) {
				$effective_manager_id = (int) $filters['manager_id'];
			} else if ($route_manager !== '' && $route_manager !== 'ALL' && ctype_digit((string) $route_manager)) {
				$effective_manager_id = (int) $route_manager;
			}
		} else {
			$effective_manager_id = $current_user_id;
		}

		$manager_sql = $effective_manager_id !== '' ? " AND b.added_by=" . $this->db->escape_str($effective_manager_id) : '';
		$source_sql = !empty($filters['source_id']) ? " AND b.lead_source_id=" . $this->db->escape_str($filters['source_id']) : '';
		$exhibition_sql = !empty($filters['exhibition_id']) ? " AND b.exhibition=" . $this->db->escape_str($filters['exhibition_id']) : '';
		$date_sql = '';

		if (!empty($filters['start_date'])) {
			$date_sql .= " AND DATE(a.added_on) >= '" . $this->db->escape_str($filters['start_date']) . "'";
		}

		if (!empty($filters['end_date'])) {
			$date_sql .= " AND DATE(a.added_on) <= '" . $this->db->escape_str($filters['end_date']) . "'";
		}

		$sql = "SELECT
				a.added_on,
				a.next_follow_date,
				b.added_by as leadmanager,
				b.machine_type,
				b.lead_source_id,
				b.company_name as company_reference_id,
				c.company_name as mastercompanyname,
				COALESCE(NULLIF(b.email_id, ''), NULLIF(b.email, ''), NULLIF(c.email, '')) as stage_email,
				ls.lead_source,
				pt.patient_type as clienttype,
				CONCAT_WS(' ', u.title, u.first_name, u.last_name) as leadmanagername
			FROM progress_remarks a
			JOIN leads b ON a.lead_id=b.id
			LEFT JOIN customer_detail c ON c.id=b.company_name
			LEFT JOIN lead_source ls ON ls.source_id=b.lead_source_id
			LEFT JOIN patient_type pt ON pt.patient_id=b.patient_type_id
			LEFT JOIN system_users u ON u.user_id=b.added_by
			WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)
			  AND a.lead_status IN ('" . $this->db->escape_str($lead_stage) . "')
			  AND b.closed=0" . $manager_sql . $source_sql . $exhibition_sql . $date_sql . "
			GROUP BY b.id
			ORDER BY b.id DESC";

		return $this->db->query($sql)->result();
	}

	private function get_stage_dashboard_data($rows, $manager_options = array())
	{
		$today = date('Y-m-d');
		$today_ts = strtotime($today);
		$kpis = array(
			'total_opportunities' => 0,
			'unique_companies' => 0,
			'with_email' => 0,
			'updated_today' => 0,
			'due_today' => 0,
			'overdue_followups' => 0,
			'avg_days_in_stage' => 0,
			'stale_over_7_days' => 0,
			'domestic_count' => 0,
			'export_count' => 0,
			'liquid_count' => 0,
			'powder_count' => 0,
			'custom_count' => 0,
			'last_updated_on' => '',
			'source_summary' => array(),
			'age_summary' => array(
				'0-3 Days' => 0,
				'4-7 Days' => 0,
				'8+ Days' => 0,
			),
			'manager_summary' => array(),
		);

		$source_counts = array();
		$unique_companies = array();
		$manager_map = array();
		$total_days = 0;

		foreach ($manager_options as $manager) {
			$manager_name = trim($manager->title . ' ' . $manager->first_name . ' ' . $manager->last_name);
			$manager_map[$manager->user_id] = array(
				'user_id' => $manager->user_id,
				'name' => ucwords(strtolower($manager_name)),
				'count' => 0,
				'last_updated_on' => '',
			);
		}

		foreach ($rows as $row) {
			$kpis['total_opportunities']++;

			if (!empty($row->company_reference_id)) {
				$unique_companies[$row->company_reference_id] = true;
			}

			if (!empty($row->stage_email)) {
				$kpis['with_email']++;
			}

			$updated_date = !empty($row->added_on) ? date('Y-m-d', strtotime($row->added_on)) : '';
			if ($updated_date === $today) {
				$kpis['updated_today']++;
			}

			if (!empty($row->added_on) && ($kpis['last_updated_on'] === '' || strtotime($row->added_on) > strtotime($kpis['last_updated_on']))) {
				$kpis['last_updated_on'] = $row->added_on;
			}

			if (!empty($row->next_follow_date) && $row->next_follow_date !== '0000-00-00') {
				if ($row->next_follow_date === $today) {
					$kpis['due_today']++;
				} else if ($row->next_follow_date < $today) {
					$kpis['overdue_followups']++;
				}
			}

			if (!empty($row->added_on)) {
				$days_in_stage = max(0, floor((strtotime($today) - strtotime(date('Y-m-d', strtotime($row->added_on)))) / 86400));
				$total_days += $days_in_stage;

				if ($days_in_stage <= 3) {
					$kpis['age_summary']['0-3 Days']++;
				} else if ($days_in_stage <= 7) {
					$kpis['age_summary']['4-7 Days']++;
				} else {
					$kpis['age_summary']['8+ Days']++;
					$kpis['stale_over_7_days']++;
				}
			}

			$client_type = strtolower(trim((string) $row->clienttype));
			if ($client_type !== '') {
				if (strpos($client_type, 'domestic') !== false) {
					$kpis['domestic_count']++;
				} else {
					$kpis['export_count']++;
				}
			}

			if ((int) $row->machine_type === 1) {
				$kpis['liquid_count']++;
			} else if ((int) $row->machine_type === 2) {
				$kpis['powder_count']++;
			} else {
				$kpis['custom_count']++;
			}

			$source_label = trim((string) $row->lead_source) !== '' ? $row->lead_source : 'Unknown';
			if (!isset($source_counts[$source_label])) {
				$source_counts[$source_label] = 0;
			}
			$source_counts[$source_label]++;

			$manager_name = trim((string) $row->leadmanagername) !== '' ? ucwords(strtolower($row->leadmanagername)) : 'Unassigned';
			if (!isset($manager_map[$row->leadmanager])) {
				$manager_map[$row->leadmanager] = array(
					'user_id' => $row->leadmanager,
					'name' => $manager_name,
					'count' => 0,
					'last_updated_on' => '',
				);
			}

			$manager_map[$row->leadmanager]['count']++;
			if (!empty($row->added_on) && ($manager_map[$row->leadmanager]['last_updated_on'] === '' || strtotime($row->added_on) > strtotime($manager_map[$row->leadmanager]['last_updated_on']))) {
				$manager_map[$row->leadmanager]['last_updated_on'] = $row->added_on;
			}
		}

		$kpis['unique_companies'] = count($unique_companies);
		$kpis['avg_days_in_stage'] = $kpis['total_opportunities'] > 0 ? round($total_days / $kpis['total_opportunities'], 1) : 0;

		arsort($source_counts);
		foreach ($source_counts as $label => $count) {
			$kpis['source_summary'][] = array(
				'label' => $label,
				'count' => $count,
			);
		}

		usort($manager_map, function ($left, $right) {
			if ($left['count'] === $right['count']) {
				return strcmp($left['name'], $right['name']);
			}
			return $right['count'] - $left['count'];
		});
		$kpis['manager_summary'] = $manager_map;

		return $kpis;
	}

	public function lead_stages() {
		$lead_stage = (int) $this->uri->segment(3);
		$route_source = (string) $this->uri->segment(4);
		$route_exhibition = (string) $this->uri->segment(5);
		$route_manager = (string) $this->uri->segment(7);
		$filters = $this->get_stage_filter_values($route_source, $route_exhibition, $route_manager);
		$filter_options = $this->get_stage_filter_options();
		$summary_rows = $this->get_stage_filtered_rows($lead_stage, $filters, $route_manager);
		$stage_kpis = $this->get_stage_dashboard_data($summary_rows, $filter_options['managers']);
		$current_url = page_url . 'Leads/lead_stages/' . $lead_stage;
		$route_source_segment = $route_source !== '' ? $route_source : 'ALL';
		$route_exhibition_segment = $route_exhibition !== '' ? $route_exhibition : 'ALL';
		$route_manager_segment = $route_manager !== '' ? $route_manager : 'ALL';
		$ajax_url = page_url . 'Leads/lead_stage_list_pms/' . $lead_stage . '/' . base64_encode($current_url) . '/' . $route_source_segment . '/' . $route_exhibition_segment . '/' . $route_manager_segment;
		$filter_query_string = $this->get_stage_filter_query_string($filters);
		if ($filter_query_string !== '') {
			$ajax_url .= '?' . $filter_query_string;
		}

		$data = array(
			'stage_filters' => $filters,
			'stage_filter_options' => $filter_options,
			'stage_kpis' => $stage_kpis,
			'stage_ajax_url' => $ajax_url,
			'stage_filter_query_string' => $filter_query_string,
			'show_stage_manager_summary' => ($this->can_view_all_stage_leads() || in_array((int) $_SESSION['logged_in']['user_id'], array(139, 161), true)),
		);

		$this->load->view('leads/lead_stages', $data);
	}

	public function lead_stage_list() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();
		$approvalpending = "";
		$demofeedback = "";
		$lead_stage=$this->uri->segment(3);
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		$resty=$this->db->query("SELECT b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no, a.nonqualifiedreason, b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage') AND b.closed=0 GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
	

	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

		if($row->patient_type_id == 7) {
			$business_nature = $clienttype.'<br><br>'.$row->other_business;
		} else {
			$business_nature = $clienttype;
		}

	$a=1;
	$html='';

	

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->leadid)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "";
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}


			$updatedby=$this->salescrm->getusername($row->updatedby);

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


$eda=0;
$view='NA';
	$deadorclose=$this->dashboardmodel->checkfordead_end_or_clousure($lead_stage);

	if($deadorclose<>'')
	{
		$edata=explode('|',$deadorclose);

	}else
	{
		$eda=1;
	}

if($eda!=1)
{
if($edata[0]!=1 && $edata[1]!=1)
{
	if($row->patient_type_id<>1)
	{
	$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}else
	{
		$view = "<a href='".page_url."Leads/view_detail_distributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}



/*Pending for Approval*/
if($this->uri->segment(3)==22){
$approvalpending = "<a href='".page_url."Leads/customizeproductapproval/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>ARROVAL PENDING</a>";	
}else{
$approvalpending = "";
}
/*Pending for Approval*/

/*Pending for Demo Feedback*/
if($this->uri->segment(3)==20){
$demofeedback = "<a href='".page_url."Leads/updatedemofeedback/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Demo Feedback</a>";	
}else{
$demofeedback = "";
}
/*Pending for Demo Feedback*/
}
}else
{

	if($row->patient_type_id<>1)
	{
$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}else
	{
		
		$view = "<a href='".page_url."Leads/view_detail_distributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

	}	


/*Pending for Approval*/
if($this->uri->segment(3)==22){
$approvalpending = "<a href='".page_url."Leads/customizeproductapproval/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>ARROVAL PENDING</a>";	
}else{
$approvalpending = "";
}	

}

/*Pending for Demo Feedback*/
if($this->uri->segment(3)==20){
$view = "<a href='".page_url."Leads/updatedemofeedback/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Demo Feedback</a>";	
}

if($this->uri->segment(3)==25){
	if($row->patient_type_id==1){
	$view = "<a href='".page_url."Leads/markquotationsendbydistributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Quotation Info</a>";	
	}else{
		$view = "<a href='".page_url."Leads/createquotation/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}
	
}
$modifyquotation = "";
if($this->uri->segment(3)==4 || $this->uri->segment(3)==5){
	if($row->patient_type_id==1){
	$modifyquotation = "<a href='".page_url."Leads/markquotationsendbydistributor/".$row->leadid."/".$lead_stage."' class='btn btn-primary btn-xs'>Update Quotation Info</a>";	
	}else{
		$modifyquotation = "<a href='".page_url."Leads/modifyquotation/".$row->leadid."/".$lead_stage."' class='btn btn-primary btn-xs'>Modify Quotation</a>";
	}
	
}

$q = $this->db->select('id')->from('lead_demo_feedback')->where('lead_id',$row->leadid)->get();
if($q->num_rows()>0){
	$feedbackview = '<a href="'.page_url.'Leads/demofeedbackview/'.$row->leadid.'"><span class="btn btn-success btn-xs">View Demo Feedback</span></a>';
}else{
	$feedbackview = "";
}



	$getLeadStageDetails=$this->salescrm->getLeadStageDetails($lead_stage);
	foreach ($getLeadStageDetails as $row1);
	$quotelink='';
	$quotation = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$row->leadid."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";
	//$quotation = "<a href='".page_url."Leads/lead_quotation_view/".$row->leadid."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";




		// $oddetail=$this->salescrm->getorder_details($row->leadid);
		if($row->nonqualifiedreason>0)
		{
			$reason=$this->dashboardmodel->unqualifiedreason($row->nonqualifiedreason);

		}else
		{
			$reason='';
		}

		if($this->uri->segment(3)==27){
		$products = $this->salescrm->wonordergetProductsTabular($row->leadid);
		}else{
			$products = $this->salescrm->getProductsTabular($row->leadid);
		}
				
				
				if($row->lastupdatedOn=='0000-00-00 00:00:00')
				{
					$lastup='';
				}else
				{
					$lastup=date('d-m-Y H:i:s',strtotime($row->lastupdatedOn));
				}

				if($row->distributor>0)
				{
					$distributorName=$this->salescrm->getdistributorName($row->distributor);
				}else{
					$distributorName='';
				}


			if($this->uri->segment(3)=='34'){
			$q = $this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$row->nonqualifiedreason)->get();
			if($q->num_rows()>0){
			foreach($q->result() as $lostresons);
			$leadlostreason = $lostresons->reason;
			}else{
			$leadlostreason = '';
			}
			}else{
			$leadlostreason = '';
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $business_nature,
								'distributor'=>$distributorName,
								'location' => $clienttype,
								'designation'=>$row->designation,
								'email' =>'',
								'feedbackview'=>$feedbackview,
								'demofeedback'=>$demofeedback,
								'pendingapproval'=>$approvalpending,
								'visitdate'=>date('d-m-Y',strtotime($row->visitdate)),
								'visittime'=>date('H:i A',strtotime($row->visittime)),
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'reason'=>"<strong style='font-weight:bold;'>".$reason."</strong>",
								// 'orderdetail'=>$oddetail,
								'lastremarks'=>"<strong style='color:black;'>".$row->remarks."</strong>",
								'leadmanager'=>$assignement."<br>".$fname." ".$lname."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$lastup."<br/><br/>".$updatedby,
								'update'=>$view,
								'leadlostreasonss'=>$leadlostreason,
								'products' => $products,
								'leadsource'=>$leadsource,
								'quotation' => $quotation,
								'modifyquotation'=>$modifyquotation,
								
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

	public function lead_stages_user() {
		$this->load->view('leads/lead_stages_user');
	}

	public function lead_stage_user_list() {

		$lead_data = array();

		$lead_stage=$this->uri->segment(3);
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		//$resty=$this->db->query("SELECT b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member e ON a.lead_id=e.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage IN ('$lead_stage') AND b.closed=0 AND e.member_id='$user_id' GROUP BY b.id  ORDER BY b.id DESC");

		$resty=$this->db->query("SELECT b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member e ON a.lead_id=e.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND e.member_id='$user_id' AND a.lead_stage IN ('$lead_stage') AND b.closed=0 GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);

		if($row->patient_type_id == 7) {
			$business_nature = $clienttype.'<br><br>'.$row->other_business;
		} else {
			$business_nature = $clienttype;
		}

	$a=1;
	$html='';

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->leadid)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "";
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}


			$updatedby=$this->salescrm->getusername($row->updatedby);

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


$eda=0;
$view='NA';
	$deadorclose=$this->dashboardmodel->checkfordead_end_or_clousure($lead_stage);

	if($deadorclose<>'')
	{
		$edata=explode('|',$deadorclose);

	}else
	{
		$eda=1;
	}

if($eda!=1)
{
if($edata[0]!=1 && $edata[1]!=1)
{
	if($row->patient_type_id<>1)
	{
	$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}else
	{
		$view = "<a href='".page_url."Leads/view_detail_distributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}



/*Pending for Approval*/
if($this->uri->segment(3)==22){
$approvalpending = "<a href='".page_url."Leads/customizeproductapproval/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>ARROVAL PENDING</a>";	
}else{
$approvalpending = "";
}
/*Pending for Approval*/

/*Pending for Demo Feedback*/
if($this->uri->segment(3)==20){
$demofeedback = "<a href='".page_url."Leads/updatedemofeedback/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Demo Feedback</a>";	
}else{
$demofeedback = "";
}
/*Pending for Demo Feedback*/
}
}else
{

	if($row->patient_type_id<>1)
	{
$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}else
	{
		
		$view = "<a href='".page_url."Leads/view_detail_distributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

	}	


/*Pending for Approval*/
if($this->uri->segment(3)==22){
$approvalpending = "<a href='".page_url."Leads/customizeproductapproval/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>ARROVAL PENDING</a>";	
}else{
$approvalpending = "";
}	

}

/*Pending for Demo Feedback*/
if($this->uri->segment(3)==20){
$view = "<a href='".page_url."Leads/updatedemofeedback/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Demo Feedback</a>";	
}

if($this->uri->segment(3)==25){
	if($row->patient_type_id==1){
	$view = "<a href='".page_url."Leads/markquotationsendbydistributor/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>Update Quotation Info</a>";	
	}else{
		$view = "<a href='".page_url."Leads/createquotation/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
	}
	
}
$modifyquotation = "";
if($this->uri->segment(3)==4 || $this->uri->segment(3)==5){
	if($row->patient_type_id==1){
	$modifyquotation = "<a href='".page_url."Leads/markquotationsendbydistributor/".$row->leadid."/".$lead_stage."' class='btn btn-primary btn-xs'>Update Quotation Info</a>";	
	}else{
		$modifyquotation = "<a href='".page_url."Leads/modifyquotation/".$row->leadid."/".$lead_stage."' class='btn btn-primary btn-xs'>Modify Quotation</a>";
	}
	
}

	$getLeadStageDetails=$this->salescrm->getLeadStageDetails($lead_stage);
	foreach ($getLeadStageDetails as $row1);
	$quotelink='';
	$quotation = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$row->leadid."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";
	//$quotation = "<a href='".page_url."Leads/lead_quotation_view/".$row->leadid."' class='btn btn-success btn-xs'  target='_blank'>Preview Quotation</a>";




		// $oddetail=$this->salescrm->getorder_details($row->leadid);
		if($row->nonqualifiedreason>0)
		{
			$reason=$this->dashboardmodel->unqualifiedreason($row->nonqualifiedreason);

		}else
		{
			$reason='';
		}

		if($this->uri->segment(3)==27){
		$products = $this->salescrm->wonordergetProductsTabular($row->leadid);
		}else{
			$products = $this->salescrm->getProductsTabular($row->leadid);
		}
				
				
				if($row->lastupdatedOn=='0000-00-00 00:00:00')
				{
					$lastup='';
				}else
				{
					$lastup=date('d-m-Y H:i:s',strtotime($row->lastupdatedOn));
				}

				if($row->distributor>0)
				{
					$distributorName=$this->salescrm->getdistributorName($row->distributor);
				}else{
					$distributorName='';
				}


			$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $business_nature,
								'distributor'=>$distributorName,
								'location' => $clienttype,
								'designation'=>$row->designation,
								'email' =>'',
								'demofeedback'=>$demofeedback,
								'pendingapproval'=>$approvalpending,
								'visitdate'=>date('d-m-Y',strtotime($row->visitdate)),
								'visittime'=>date('H:i A',strtotime($row->visittime)),
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'reason'=>"<strong style='font-weight:bold;'>".$reason."</strong>",
								// 'orderdetail'=>$oddetail,
								'lastremarks'=>"<strong style='color:black;'>".$row->remarks."</strong>",
								'leadmanager'=>$assignement."<br>".$fname." ".$lname."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$lastup."<br/><br/>".$updatedby,
								'update'=>$view,
								'products' => $products,
								'leadsource'=>$leadsource,
								'quotation' => $quotation,
								'modifyquotation'=>$modifyquotation,
								
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

	function getLeadStageDetails() {
		$res = '';
		$lead_stage_id = $this->input->post('lead_stage_id');

			$sql = $this->db->select('quotation_step, quotation_revised_step, pi_step, pi_revised_step,followup_date, reason, conversion_step,sample,trail')
							->from('lead_stage')
							->where('lead_id', $lead_stage_id)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row);
				$res = $row->quotation_step.'|'.$row->quotation_revised_step.'|'.$row->pi_step.'|'.$row->pi_revised_step.'|'.$row->followup_date.'|'.$row->reason.'|'.$row->conversion_step.'|'.$row->sample.'|'.$row->trail;
			}

		echo $res;
	}

	function pdfredirection()
	{
		$lead_stage=$this->uri->segment(3);
		$lead_id=$this->uri->segment(4);
		$unique_id=$this->uri->segment(5);
		$flag=$this->uri->segment(6);

		if($flag == 1) {
			$this->send_communication($lead_stage,$lead_id);
		}

		redirect(page_url.'Leads/previewquotation/'.$unique_id);
	}


	function pipdfredirection()
	{
		$lead_stage=$this->uri->segment(3);
		$lead_id=$this->uri->segment(4);
		$unique_id=$this->uri->segment(5);
		$flag=$this->uri->segment(6);

		if($flag == 1) {
			$this->send_communication($lead_stage,$lead_id);
		}

		redirect(page_url.'Leads/previewpi/'.$unique_id);
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


	// function assignuserbasedonzone($location,$leadid)
	// {

	// 	$userid=0;
	// 	$getAllLeadStages=$this->dashboardmodel->getAllLeadStages();
	// 	if(count($getAllLeadStages)>0) {
	// 	$i=1;
	// 		foreach($getAllLeadStages as $row1) {

	// 			$resty=$this->db->select('user_role,userid')->from('saleszoneusers')->where('user_role',$row1->user_role)->where('zoneid',$location)->get();
	// 			if($resty->num_rows()>0)
	// 			{
	// 				foreach($resty->result() as $rowss)
	// 				$userid=$rowss->userid;
		
	// 			}else
	// 			{
	// 				$userid=0;
	// 			}


	// 			$datas=array('lead_id'=>$leadid,'lead_stage_id'=>$row1->lead_id,'user_id'=>$userid,
	// 			'role_id'=>$row1->user_role,
	// 			'addedOn'=>date('Y-m-d H:i:s'),
	// 			'addedBy'=>$_SESSION['logged_in']['user_id']);
	// 			$this->db->insert('assigned_users_for_lead',$datas);

	// 		}

	// 	}


	// 	return true;

	// }
	
	function change_assigned_lead_multi() {
		$lead_id = $this->uri->segment(3);
		$userrole = $this->input->post('userrole');
		$assuser = $this->input->post('assuser');

		for($i=0; $i<count($userrole); $i++){

			$user = $assuser[$i];
			$userroles = $userrole[$i];



	
			$arow=$this->db->select('id')->from('assigned_users_for_lead')->where('role_id',$userroles)->where('lead_id',$lead_id)->get();
			if($arow->num_rows()>0)
			{
				
			$data = array('user_id'=>$user);
			$this->db->where('lead_id',$lead_id);
			$this->db->where('role_id',$userroles);
			$this->db->update('assigned_users_for_lead',$data);
			}else
			{
				$rowdd=$this->db->select('lead_id')->from('lead_stage')->where('user_role',$userroles)->get();
				if($rowdd->num_rows()>0)
				{
					foreach($rowdd->result() as $newentry)
					{
						$newdata=array('lead_id'=>$lead_id,'lead_stage_id'=>$newentry->lead_id,'user_id'=>$user,'role_id'=>$userroles,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->insert('assigned_users_for_lead',$newdata);



					}
				}
			}
			
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>');
			redirect(page_url.'Leads');

	}


	function getConversionLeadStage() {
			$res = '';
			$sql = $this->db->select('lead_id')
							->from('lead_stage')
							->where('conversion_step', 1)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row1);
				$res = $row1->lead_id;
			}

			return $res;
		}

	function getGodModeData() {
		$this->load->view('leads/god_mode_data');
	}

	function god_mode_list() {
		$lead_data = array();		
		$lead_id = $this->uri->segment(3);
		// echo $lead_id;exit;
		$resty=$this->db->query("SELECT content FROM email_trigger WHERE lead_id=$lead_id");

		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {	
			$obj = json_decode($row->content, true);

			if($obj['email'] == 1) {
				$email = 'YES';
			} else {
				$email = 'NO';
			}

			if($obj['whatsapp'] == 1) {
				$whatsapp = 'YES';
			} else {
				$whatsapp = 'NO';
			}

			if($obj['include_pdf'] == 1) {
				$include_pdf = 'YES';
			} else {
				$include_pdf = 'NO';
			}

			if($obj['include_pi'] == 1) {
				$include_pi = 'YES';
			} else {
				$include_pi = 'NO';
			}

			if($obj['include_quotation'] == 1) {
				$include_quotation = 'YES';
			} else {
				$include_quotation = 'NO';
			}

			$getCustomerDetails = $this->salescrm->getCustomerDetails($lead_id);

			if($getCustomerDetails != '') {
				 foreach ($getCustomerDetails as $rows);
				   $customer_title = $rows->title;
				   $customer_name = $rows->customer_name;
				   $unique_id = $rows->unique_id;
				   $company_name = $rows->company_name;
				   $email_id = $rows->email_id;
				   $contact_no = $rows->contact_no;
			} else {
				$customer_title = '';
				$customer_name = '';
				$unique_id = '';
				$company_name = '';
				$email_id = '';
				$contact_no = '';
			}

			$sql1 = $this->db->select('a.instruments_name, a.pdf')
						     ->from('presto_instruments_view a')
						  	 ->join('lead_products b', 'a.id=b.product_id')
						  	 ->where('b.lead_id', $lead_id)
						  	 ->get();


							foreach ($sql1->result() as $row1) {
								$pro[] = $row1->instruments_name;
							}

							if(count($pro) > 0) {
								$product_name = implode(',',$pro);	

							} else {
								$product_name = '';
							}

			$find = array('customer_title', 'customer_name', 'company_name', 'email_id', 'contact_number', 'product_name');

			$replace = array($customer_title, $customer_name, $company_name, $email_id, $contact_no, $product_name);

			$message = str_replace($find, $replace, $obj['content']);

			$lead_data[] = array(
								'sr_no' => $i,
								'email' => $email,
								'whatsapp' => $whatsapp,
								'include_pdf' => $include_pdf,
								'include_pi' => $include_pi,
								'include_quotation' => $include_quotation,
								'content' => $message,
								'sent_on' => date('d-M-Y H:i:s', strtotime($obj['content_sent_on']))
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

	function unsubscribed_users() {
		$this->load->view('leads/unsubscribed_users');
	}

	function unsubscribed_users_list() {
		$lead_data = array();
		$query = $this->db->select('a.id, a.create_date, a.unique_id, a.customer_name, a.company_name, a.country_code, a.contact_no, a.email_id, b.country_name, f.patient_type, g.lead_source, c.reason')
						  ->from('leads a')
						  ->join('countries b','a.country=b.country_id','left')
						  ->join('patient_type f','a.patient_type_id=f.patient_id','left')
						  ->join('lead_source g','a.lead_source_id=g.source_id','left')
						  ->join('unsubscribers c','a.id=c.lead_id','left')
						  ->where('a.unsubscribe',1)
						  ->order_by('a.added_on','DESC')
						  ->get();
		
		$i=1;
		foreach($query->result() as $row) {
			$products = $this->salescrm->getProducts($row->id);
			$instruments = array();

				foreach($products as $row1) {
		   			$instruments[] = $row1->instruments_name;
		   		}

		   	$products1 = implode(',', $instruments);

		   	if($row->reason == 1) {
		   		$reason = "I'm not interested anymore";
		   	} else if($row->reason == 2) {
		   		$reason = "I receive too many of these emails";
		   	} else if($row->reason == 3) {
		   		$reason = "This content is not relevant to me";
		   	} else if($row->reason == 4) {
		   		$reason = "This is spam/ I never subscribed to this email list";
		   	} else if($row->reason == 5) {
		   		$reason = "My inbox is too full";
		   	} else if($row->reason == 6) {
		   		$reason = "Others";
		   	} else {
		   		$reason = "";
		   	}

			$lead_data[] = array(
							'sr_no' => $i,
							'create_date'=>date('d-m-Y', strtotime($row->create_date))."<br>".$row->unique_id,
							'patient_type'=>$row->patient_type,
							'products'=>$products1,
							'lead_source'=>$row->lead_source,
							'company_name'=>$row->company_name,
							'customer_details'=>$row->customer_name."<br>".$row->country_code."-".$row->contact_no."<br>".$row->email_id."<br>".$row->country_name,
							'reason'=>$reason
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

	function unassigned_leads()
	{
		$this->load->view('leads/unassignedleads');
	}



	public function unassignd_lead_list()
	{
		$lead_data = array();
		$mode=$this->getsettings();
		if(count($mode)>0)
		{
			$mode=$mode['mode'];
		}else
		{	
			$mode=1;
		}




		/** CHECK FOR UNASSIGNED LEADS **/

		$asrow=$this->db->select('a.lead_id')->from('assigned_users_for_lead a')->where('user_id',0)->or_where('user_id','')->group_by('lead_id')->get();
		if($asrow->num_rows()>0)
		{
			foreach($asrow->result() as $asrows)
			{


			$this->db->select('a.*,b.country_id,b.country_name,e.lead_id,e.lead_name,e.color,f.patient_id, f.patient_type,g.source_id, g.lead_source,s.lead_id, s.lead_name as lead_quality_status')->from('leads a');
			$this->db->join('countries b','a.country=b.country_id','left');
			$this->db->join('lead_type e','a.lead_quality=e.lead_id','left');
			$this->db->join('lead_quality_status s','a.lead_quality_status=s.lead_id','left');
			$this->db->join('patient_type f','a.patient_type_id=f.patient_id','left');
			$this->db->join('lead_source g','a.lead_source_id=g.source_id','left');
			//$this->db->where('a.status','1');
			$this->db->where('a.id',$asrows->lead_id);
			$query = $this->db->get();
			$res = $query->result();									
			$i=1;
			if($query->num_rows()>0)
			{
				foreach($res as $row);

				$products = $this->salescrm->getProducts($row->id);
				$instruments = array();

				foreach($products as $row1) {
		   			$instruments[] = $row1->instruments_name;
		   		}

		   	$products1 = implode(',', $instruments);

			$getLeadStatus = $this->salescrm->getLeadStatus($row->id);
			if(($getLeadStatus) > 0) {
			foreach ($getLeadStatus as $status);
				$currentleadstatus = $status->lead_name;
				$currentleadremarks = $status->remarks;
			} else {
				$currentleadstatus = '';
				$currentleadremarks = '';
			}
			
			$lead_quality = $row->lead_name;
			$color_lead = $row->color;
			$lead_type = "<span style='color:".$color_lead."; font-weight:bold;'>".$lead_quality."</span>";
			$lead_quality_status = "<span style='color:".$color_lead."; font-weight:bold;'>".$row->lead_quality_status."</span>";
			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Leads/update_lead_stage/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$leadinfo  = "<input type='hidden' name='leadid[]' value='".$row->id."'";
			$edit = "<a href='".page_url."Leads/edit_leads/".$row->id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			if($_SESSION['logged_in']['role']==1) {
				$edit .= " | <a href='javascript:;' onclick='deleteLead(".$row->id.")'><i class='fa fa-trash' title='Delete Lead'></i></a>";
			}

			$edit .= " | <a href='".page_url."Leads/getGodModeData/".$row->id."'><i class='fa fa-history' title='God Mode Logs'></i></a>";

			$view = "<a href='".page_url."Leads/view_detail/".$row->id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
			if($mode==1){
			$lead_transfer = "<a href='".page_url."Assigned_lead/change_assignment/".$row->id."' class='btn btn-success btn-xs'>Lead Transfer</a>";
			}else{
			$lead_transfer = "<a href='".page_url."Assigned_lead/changemultiassignment/".$row->id."' class='btn btn-success btn-xs'>Lead Transfer</a>";	
			}
			$skype= "<a href='skype:".$row->skypeid."?call'><i class='fa fa-skype'></i>&nbsp; ".$row->skypeid."</a>";
			$messanger = "<a href='https://www.facebook.com/msg/".$row->messangerid."'><img src='https://png.icons8.com/windows/50/000000/facebook-messenger.png' width='20px'>&nbsp;".$row->messangerid."</a>";
						
			
		$query = $this->db->select(' team_id,team_name')->from('prestogroup_teams')->where('status','1')->order_by('team_name','asc')->get();
		$assign = "<input type='checkbox' class='form-control' name='pick_items[]' id='pick_items".$i."' value='".$row->id."' onChange='display_qtybox(".$i.")'>";
		$team = "<select class='form-control' name='teamleader[]' id='teamleader".$i."' style='display:none' disabled>";
		       $team .= "<option value=''>Select Team</option>";
		       
				foreach($query->result() as $teamname)
				{
				$team .="<option value='".$teamname->team_id."'>".$teamname->team_name."</option>";
				}
		
			$team .="</select>";


if($mode==1)
{

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->id)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->id)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}
}else
{



		$a=1;
	$html='';
	$lname='';
	$fname='';
	$assignement='';
// 	if($mode=='2'){
// 		$html='';
// 		$qe=$this->db->select('a.user_id,b.first_name,b.last_name, c.user_role')->from('assigned_users_for_lead a')->join('system_users b','a.user_id=b.user_id','left')->join('user_role c','a.role_id=c.user_role_id','left')->where('a.lead_id',$row->id)->group_by('c.user_role')->get();
// 	if($qe->num_rows()>0){	
// $html.="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Role</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Name</th><tbody><tr>";


// if($qe->num_rows()>0)

// {

// foreach($qe->result() as $qer)

// {

// $user =	ucwords(strtolower($qer->first_name." ".$qer->last_name.'<br/>'));
// $html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$a."</td>
// <td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$qer->user_role."</td>
// <td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$user."</td></tr>";	
// $a++;
// }				

// }

// $html.="</table>";
// 	}
// $fname= $html;
		
		
// 	}else{
		
		

// 	$getleadassignedto=$this->salescrm->getleadassigned($row->leadid);
// 	$fname=$this->salescrm->getusername($getleadassignedto);
// 		}

		if($mode==1)
{

			$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
							  ->from('lead_assigned_to_team a')
							  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
							  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
							  ->join('system_users d','d.user_id=c.member_id','left')
							  ->where('a.lead_id',$row->id)
							  ->get();

			$res = $query->result();
			if($res){
				foreach($res as $assign_information)
				{
				$assignement = 	$assign_information->team_name;
				$fname = 	$assign_information->first_name;
				$lname = 	$assign_information->last_name;
				}
				
			}else{
			$assignement = "<div class='row'><div class='col-md-2'>".$assign."</div><div class='col-md-10'>".$team."</div></div>".$leadinfo;
			$fname = '';
			$lname = '';
			}

			$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->id)->get();
			if($restsyt->num_rows()>0)
			{
				foreach($restsyt->result() as $restsyt);

				$fname=$this->salescrm->getusername($restsyt->member_id);
				$lname='';
			}else
			{
				$fname='';
				$lname='';
			}
			}else
			{

			$assignement=$this->Lead_model->getleadmanagerformulti($row->id,$row->client_location);
			$fname='';
			$lname='';

			}

}


$lead_data[] = array('sr_no'=>$i,
			'create_date'=>date('d-m-Y', strtotime($row->create_date))."<br>".$row->unique_id,
			'patient_type'=>$row->patient_type,
			'products'=>$products1,
			'lead_source'=>$row->lead_source,
			'company_name'=>$row->company_name,
			'customer_details'=>$row->customer_name."<br>".$row->country_code."-".$row->contact_no."<br>".$row->email_id."<br>".$row->country_name,
			'skypeid'=>$skype,
			'messangerid'=>$messanger,
			'state'=>$row->state,
			'city'=>$row->city,
			'lead_quality_status'=>$currentleadstatus,
			'lead_remarks'=>$row->remarks,
			'remarks'=>$currentleadremarks,
			'assign'=>$assignement."<br>".$fname." ".$lname,
			'edit'=>$edit,
			'followup' => $view,
			'lead_transfer' => $lead_transfer
			);
			$i++;
}
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

	function delete_product() {
		$product_id = $this->input->post('product_id');

		$this->db->where('id', $product_id)
				 ->delete('lead_products');

		if ($this->db->affected_rows()) {
			echo 1;
		} else {
			echo 0;
		}
	}

	function delete_lead_product() {
		$product_id = $this->uri->segment(3);
		$lead_id = $this->uri->segment(4);
		$lead_stage = $this->uri->segment(5);

		$this->db->where('id', $product_id)
				 ->delete('lead_products');

		$this->session->set_flashdata('message','<div class="alert alert-success">Product successfully deleted.</div>');
		redirect(page_url.'Leads/view_detail/'.$lead_id.'/'.$lead_stage);
	}

	function lead_discount_approval() {
		$this->load->view('leads/lead_discount_approval');
	}

	function lead_discount_approval_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id, a.qty, a.price, a.percent_amt, a.net_price, b.company_name, b.customer_name, c.instruments_name')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('presto_instruments c', 'c.id=a.product_id')
						->where('a.flag', 0)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->id.")'>Approve/Reject Discount</a>";

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'list_price' => $row->price,
										'discount_price' => $row->percent_amt,
										'net_price' => $row->net_price,
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
			
			echo json_encode($results);
	}

	function save_lead_discount_remarks() {
		$product_id = $this->input->post('product_id');
		$discount_approval = $this->input->post('discount_approval');
		$remarks = $this->input->post('remarks');

		$data = array(
					  'flag' => $discount_approval,
					  'remarks' => $remarks
					 );

		$this->db->where('id', $product_id)
				 ->update('lead_products', $data);

		if($discount_approval == 1) {
			$msg = 'Discount has been successfully approved.';
		} else {
			$msg = 'Discount has been successfully rejected.';
		}
		$this->session->set_flashdata('message','<div class="alert alert-info">'.$msg.'</div>');
		redirect(page_url.'Leads/lead_discount_approval');
	}

		function lead_discount_approval_history() {
			$this->load->view('leads/lead_discount_approval_history');
		}

		function lead_discount_approval_history_list() {
		$lead_data = array();
		$sql = $this->db->select('a.id, a.qty, a.price, a.percent_amt, a.net_price, a.flag, a.remarks, b.company_name, b.customer_name, c.instruments_name')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('presto_instruments c', 'c.id=a.product_id')
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

					$lead_data[] = array(
										'sr_no'=>$i,
										'company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'product_name' => $row->instruments_name,
										'qty' => $row->qty,
										'list_price' => $row->price,
										'discount_price' => $row->percent_amt,
										'net_price' => $row->net_price,
										'action' => $flag,
										'remarks' => $row->remarks
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

	public function send_mail_to_lead() {
		$uri=$this->uri->segment(3);

		if ($uri <> '') {
		    $sql = $this->db->select('contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, general_terms, bulk_terms, alt_contact_no, hpcl_company')
		                    ->from('leads')
		                    ->where('id', $uri)
		                    ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);
		            $contact_person = $row->contact_person;
		            $alt_contact_no = $row->alt_contact_no;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $city = $row->city;
		            $contact_no = $row->contact_no;
		            $address = $row->postal_address;
		            $general_terms = $row->general_terms;
		            $bulk_terms = $row->bulk_terms;
		            $email_id = $row->email_id;
		            $hpcl_company = $row->hpcl_company;
		        } else {
		            $contact_person = '';
		            $alt_contact_no = '';
		            $customer_name = '';
		            $company_name = '';
		            $city = '';
		            $contact_no = '';
		            $address = '';
		            $general_terms = '';
		            $bulk_terms = '';
		            $email_id = '';
		            $hpcl_company = 0;
		        }
		} else {
		    redirect(page_url);
		}
		$subjectname='Quotation of Industrial Lubricants.';
		$Message ='<table style="width: 100%; font-size:14px; font-family: monospace;">
        <tr>
            <td width="20%"></td>
            <td width="60%" style=" padding:5px; box-shadow: 1px 1px 10px lightgray; background-color:white;">
                <table style="width: 100%; font-size:14px;">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%">
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
                                
                                $sql2 = $this->db->select('a.qty, a.price, a.percent_amt, a.net_price, b.instruments_name')
                                                 ->from('lead_products a')
                                                 ->join('presto_instruments b', 'b.id=a.product_id')
                                                 ->where('lead_id', $uri)
                                                 ->get();
                                if ($sql2->num_rows() > 0) {
                                    foreach ($sql2->result() as $row2) {

                                $Message.='
                                <tr>
                                    <td style="padding: 5px; text-align:center;">'.$row2->instruments_name.'</td>
                                    <td style="padding: 5px; text-align:center;">'.$row2->qty.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $row2->price.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $row2->percent_amt.'</td>
                                    <td style="padding: 5px; text-align:center;">'. $row2->net_price.'</td>
                                </tr>';
                             } }
                           $Message.='</table>';
                           
                            
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.
                               $general_terms;
                            $Message.='</table>';
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

                            $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
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
                          
                            $Message.='<table style="width: 100%; font-size:14px; padding: 5px;">'.$bulk_terms;
                              
                            $Message.='</table>';

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
                              
                           
                            $com=$this->db->select('*')->from('store_rack_location')->where('id',$hpcl_company)->get();
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
                        <td width="10%"></td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td>
        </tr>
    </table>';
 		//echo $Message; exit;
    	$config = array(
		'protocol' => 'smtp', 
		'smtp_host' => $company->smtp, 
		'smtp_port' => $company->port, 
		'smtp_user' => $company->email, 
		'smtp_pass' => $company->password, 
		'mailtype' => 'html', 
		'charset' => 'iso-8859-1'
		);
		$this->email->initialize($config);

		$this->email->set_mailtype("html");
		// $this->email->to('faridabad@hpclcfa.com');
		$this->email->to('faridabad@hpclcfa.com');
		$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com,webdevelopment1@gamavis.com');
		$this->email->from($company->email);
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$result11=$this->email->send();
		//$this->email->print_debugger(); exit;
		$data=array(
			'lead_id'=>$uri,
			'mail_sent_on'=>date('Y-m-d H:i:s'),
			'mail_sent_by'=>$_SESSION['logged_in']['user_id']
		);
		$this->db->insert('lead_quotation_mail_history',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">mail send.</div><br/>');
			redirect(page_url.'Leads');
	}


	function pending_for_order_punching() {
		$this->load->view('leads/pending_for_order_punching');
	}

	function order_punching_list() {
		
		$lead_data = array();
		$this->db->select('a.id, a.lead_id, a.monthly_consumption, a.credit_days, a.special_remarks, b.companyname, c.contact_no, c.company_name, c.customer_name, c.email, c.city, c.address')
				 ->from('customer_quotation a')
				 ->join('store_rack_location b','a.company_id=b.id', 'left')
				 ->join('customer_detail c','a.customer_id=c.id', 'left')
				 // ->join('leads b', 'b.id=a.lead_id')
				 ->where('a.order_punch', 0)
				 ->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html=$this->getproducts_detail($row->id);

			$generate_order = "<a href='".page_url."Leads/generate_order/".$row->id."' class='btn btn-success btn-xs'>Generate Order</a>";
			$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";

			$checkIfProductIsApproved = $this->master->checkIfProductIsApproved($row->id);

			if($row->credit_days > 0) {
				$credit_days = $row->credit_days." Days";
			} else {
				$credit_days = '';
			}

			if($checkIfProductIsApproved == 0) {
				$lead_data[] = array('sr_no'=>$i,
				'company_name'=>$row->companyname,
				'cust_company_name' => $row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->address,
				'products'=>$html,
				'paymentterms'=> $credit_days,
				'monthly_consumption'=>$row->monthly_consumption,
				'special_remarks'=>"<strong style='color:red;'>".$row->special_remarks."</strong>",
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

	function generate_orderOLddd() {
		$this->load->view('leads/generate_order');
	}


		function pending_for_order_punching_user() {
		$this->load->view('leads/pending_for_order_punching_user');
	}

	function order_punching_user_list() {

		$lead_data = array();
		$this->db->select('a.id, b.id as leadid, b.company_name,b.contact_no,b.customer_name,b.email_id,b.city,b.postal_address')
				 ->from('customer_quotation a')
				 ->join('leads b', 'b.id=a.lead_id')
				 ->where('a.order_punch', 0)
				 ->where('a.added_by', $_SESSION['logged_in']['user_id'])
				 ->order_by('a.id','DESC');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;
			$html.='<table class="table table-bordered"><thead><tr><th>sr_no</th><th>Product Name</th><th>Per pcak qty</th><th>Per pcak list price</th><th>discount per liter</th><th>Net Price per ltr</th></tr></thead><tbody>';
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

			$generate_order = "<a href='".page_url."Leads/generate_order/".$row->id."' class='btn btn-success btn-xs'>Generate Order</a>";
			$edit = "<a href='".page_url."Customer/edit_quote/".$row->id."' ><i class='fa fa-edit'></i></a>";

			$lead_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'customer_name'=>$row->customer_name,
			'email'=>$row->email_id,
			'mobile'=>$row->contact_no,
			'city'=>$row->city,
			'address'=>$row->postal_address,
			'products'=>$html,
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

		public function upcomeingfollow_user()
	{
		$this->load->view('leads/upcoming_followups_user');
	}
	public function upcomeingfollow_user_list()
	{

		$i=1;
		$lead_data= array();
       	$user_id=$_SESSION['logged_in']['user_id'];
       	$company_id=$_SESSION['logged_in']['business_location'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		if($dead_end_lead_stage<>'')
		{
		    $dead_end_lead_stage=$dead_end_lead_stage;
		}else
		{
		 $dead_end_lead_stage="'0'";
		}
		

		$q=$this->db->query("SELECT a.lead_status,a.next_follow_date,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city,b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");
		
		foreach($q->result() as $row){
		
				

				$lastfollowupdate=$row->next_follow_date;
				$user_filled_followup_date=$lastfollowupdate;
				$status = $row->lead_stage;

				// if($status!=2 && $status!=5 && $status!=6){

				if($lastfollowupdate>date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				//$followup = "<a href='".page_url."Quotation1/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
				$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);


			$a=1;
	$html='';


						$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
										  ->from('lead_assigned_to_team a')
										  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
										  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
										  ->join('system_users d','d.user_id=c.member_id','left')
										  ->where('a.lead_id',$row->leadid)
										  ->get();

						$res = $query->result();
						if($res){
							foreach($res as $assign_information)
							{
							$assignement = 	$assign_information->team_name;
							$fname = 	$assign_information->first_name;
							$lname = 	$assign_information->last_name;
							}
							
						}else{
						$assignement = "";
						$fname = '';
						$lname = '';
						}

						$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
						if($restsyt->num_rows()>0)
						{
							foreach($restsyt->result() as $restsyt);

							$fname=$this->salescrm->getusername($restsyt->member_id);
							$lname='';
						}else
						{
							$fname='';
							$lname='';
						}

			// $assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
			// $fname='';
			// $lname='';
	



				$country=$this->salescrm->getcountry($row->country);
				$state=$this->salescrm->getstate($row->state);
				$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


				$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
				$read='';
				$altrcontact='';
				if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		$altrcontact="'".$row->contact_person.'"<br/>"'.$row->email.'"<br/>"'.$row->alt_contact_no."'";
		}
		$leadstatus=$this->salescrm->getLeadStatusonly($row->leadid);
				$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$altrcontact,
								'lastleadstatus'=>$leadstatus,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-Y',strtotime($row->create_date)),
								'update'=>$view,
								'followupschedule'=>"<strong style='color:red;font-weight:bold;'>".date('d-m-Y',strtotime($user_filled_followup_date))."</strong>",
								'leadsource'=>$leadsource
						
								);
						$i++;
				}
				//}

		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			echo json_encode($results);
	}

		public function missed_followup_up_user()
	{
	$this->load->view('leads/missed_follow_up_user');	
	}
	public function missed_followup_user_list(){

		$i=1;
		$lead_data= array();
       	$user_id=$_SESSION['logged_in']['user_id'];
       	$company_id=$_SESSION['logged_in']['business_location'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);
		$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		if($dead_end_lead_stage<>'')
		{
		    $dead_end_lead_stage=$dead_end_lead_stage;
		}else
		{
		 $dead_end_lead_stage="'0'";
		}
		

		$q=$this->db->query("SELECT a.lead_status,a.next_follow_date,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city,b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");
		
		foreach($q->result() as $row){
		
				

				$lastfollowupdate=$row->next_follow_date;
				$status = $row->lead_stage;

				// if($status!=2 && $status!=5 && $status!=6){

				if($lastfollowupdate<date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				//$followup = "<a href='".page_url."Quotation1/followup/".$row->id."'><span class='btn btn-success btn-xs'>Update Status</span>";
				$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);


			$a=1;
	$html='';


						$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
										  ->from('lead_assigned_to_team a')
										  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
										  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
										  ->join('system_users d','d.user_id=c.member_id','left')
										  ->where('a.lead_id',$row->leadid)
										  ->get();

						$res = $query->result();
						if($res){
							foreach($res as $assign_information)
							{
							$assignement = 	$assign_information->team_name;
							$fname = 	$assign_information->first_name;
							$lname = 	$assign_information->last_name;
							}
							
						}else{
						$assignement = "";
						$fname = '';
						$lname = '';
						}

						$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->leadid)->get();
						if($restsyt->num_rows()>0)
						{
							foreach($restsyt->result() as $restsyt);

							$fname=$this->salescrm->getusername($restsyt->member_id);
							$lname='';
						}else
						{
							$fname='';
							$lname='';
						}

			// $assignement=$this->Lead_model->getleadmanagerformulti($row->leadid,$row->client_location);
			// $fname='';
			// $lname='';
	



				$country=$this->salescrm->getcountry($row->country);
				$state=$this->salescrm->getstate($row->state);
				$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);


				$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
				$read='';
				$altrcontact='';
				if($contactacess>0){


		$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
		$altrcontact="'".$row->contact_person.'"<br/>"'.$row->email.'"<br/>"'.$row->alt_contact_no."'";
		}
		$leadstatus=$this->salescrm->getLeadStatusonly($row->leadid);
				$lead_data[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$altrcontact,
								'lastleadstatus'=>$leadstatus,
								
								'location' => '',
								'lastremarks'=>$row->clientremarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-Y',strtotime($row->create_date)),
								'update'=>$view,
								'followupschedule'=>"<strong style='color:red;font-weight:bold;'>".date('d-m-Y',strtotime($row->next_follow_date))."</strong>",
								'leadsource'=>$leadsource
						
								);
						$i++;
				}
				//}

		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			echo json_encode($results);
	}

		function todaysfollowup_user()
	{
		$this->load->view('leads/todaysfollowup_user');
	}
	
		function todaysfollowuplist_user()
			{
			    
			 //    $company_id=$_SESSION['logged_in']['business_location'];
				// $mode=$this->getsettings();
				// if(count($mode)>0)
				// {
				// 	$mode=$mode['mode'];
				// }else
				// {
				// 	$mode=1;

				// }
				$user_id=$_SESSION['logged_in']['user_id'];
				$conversion_lead_stage = $this->getConversionLeadStage();
				$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
				array_push($getDeadEndLeadStage, $conversion_lead_stage);
				$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

				$cur=date('Y-m-d');
			$lead_type_list = array();
			$user_id=$_SESSION['logged_in']['user_id'];
			$contactacess=$this->Lead_model->checkforcontactacess($user_id);
	
			$resty=$this->db->query("SELECT a.id,a.added_on,a.added_by,a.lead_status,a.remarks,a.remark_title,b.unique_id,b.lead_source_id,b.contact_no,b.email_id,b.contact_person,b.email,b.alt_contact_no,b.customer_name,b.id as lead_id,b.patient_type_id,b.create_date,b.company_name,b.country_code,b.country,b.state,b.city, b.client_location FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_stage NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur' AND c.member_id=$user_id GROUP BY a.lead_id");
			
			if($resty->num_rows()>0)
			{
				$i=1;
				foreach ($resty->result() as $row)
				{
					$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
					
					

										$a=1;
										$html='';
									

					// if($mode==1)
					// {

								$query = $this->db->select('a.id,a.lead_id,a.team_id,b.team_id,b.team_name, c.member_id, d.first_name, d.last_name')
												  ->from('lead_assigned_to_team a')
												  ->join('prestogroup_teams b','a.team_id=b.team_id','left')
												  ->join('lead_assigned_to_team_member c','c.team_id=a.team_id','left')
												  ->join('system_users d','d.user_id=c.member_id','left')
												  ->where('a.lead_id',$row->lead_id)
												  ->get();

								$res = $query->result();
								if($res){
									foreach($res as $assign_information)
									{
									$assignement = 	$assign_information->team_name;
									$fname = 	$assign_information->first_name;
									$lname = 	$assign_information->last_name;
									}
									
								}else{
								$assignement = "";
								$fname = '';
								$lname = '';
								}

								$restsyt=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$row->lead_id)->get();
								if($restsyt->num_rows()>0)
								{
									foreach($restsyt->result() as $restsyt);

									$fname=$this->salescrm->getusername($restsyt->member_id);
									$lname='';
								}else
								{
									$fname='';
									$lname='';
								}
					// }else
					// {

					// $assignement=$this->Lead_model->getleadmanagerformulti($row->lead_id,$row->client_location);
					// $fname='';
					// $lname='';

					// }


						$view = "<a href='".page_url."Leads/view_detail/".$row->lead_id."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
						$country=$this->salescrm->getcountry($row->country);
					$state=$this->salescrm->getstate($row->state);
				$read='';
				if($contactacess>0){


				$read="<a href='mailto:".$row->email_id."'>".$row->email_id."</a><br/><a href='https://api.whatsapp.com/send/?phone=".$row->country_code.$row->contact_no."' target='_blank'>".$row->contact_no."</a><br/>".$country."<br>".$state."<br>".$row->city;
				}

					
					$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
					$leadstatus=$this->salescrm->getLeadStatusonly($row->lead_id);
					$lead_type_list[] = array(
								'sr_no'=>$i,
								'unique'=>$row->unique_id.'<br/>'.date('d-m-Y', strtotime($row->create_date)),
								'company' => $row->company_name,
								'customer_name' => $row->customer_name.'<br/>'.$read,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'' ,
								'mobile' => $row->contact_no,
								'alternatedetail'=>$row->contact_person."<br/>".$row->email."<br/>".$row->alt_contact_no,
								
								'location' => '',
								'lastleadstatus'=>$leadstatus,
								'lastremarks'=>$row->remarks,
								'leadmanager'=>$assignement."<br>".$fname." ".$lname,
								'lastupdatedon'=>date('d-m-y H:i:s',strtotime($row->added_on)),
								'update'=>$view,
								'leadsource'=>$leadsource
						
								);
					$i++;
					}
			}
			
	$results = array(
									"sEcho" => 1,
								    "iTotalRecords" => count($lead_type_list),
									"iTotalDisplayRecords" => count($lead_type_list),
									"aaData"=>$lead_type_list);
			
		echo json_encode($results);
			}



	function lead_quotations_expiring_today() {
		$this->load->view('leads/lead_quotations_expiring_today');
	}

	function lead_quotations_expiring_today_list() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$query = $this->db->select('id, create_date, contact_no,customer_name,email,city,postal_address,company_name')
						  ->from('leads')
						  ->where('validity_date', $today_date)
						  ->order_by('id','DESC')
						  ->get();

		$i=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$html='';
				$j=1;
				$html.='<table class="table table-bordered"><thead><tr><th>Sr No</th><th>Product Name</th><th>Per pack qty</th><th>List price</th></tr></thead><tbody>';
				$sql=$this->db->select('a.*,b.instruments_name,')
							  ->from('lead_products a')
							  ->join('presto_instruments b','a.product_id=b.id')
							  ->where('a.lead_id',$row->id)
							  ->get();

				if($sql->num_rows()>0)
				{
					foreach($sql->result() as $product){
						$html.='<tr><td>'.$j.'</td>';
						$html.='<td>'.$product->instruments_name.'</td>';
						$html.='<td>'.$product->qty.'</td>';
						$html.='<td>'.$product->price.'</td>';

						$j++;
					}
					$html.='</tbody></table>';
				}

				$extend_by_week = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 1)' style='margin-top: 20px;'>EXTEND BY WEEK</a>";
				$extend_by_half_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 2)' style='margin-top: 20px;'>EXTEND BY 15 DAYS</a>";
				$extend_by_month = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 3)' style='margin-top: 20px;'>EXTEND BY MONTH</a>";
				$extend_by_year = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='extend_quotation_validity(".$row->id.", 4)' style='margin-top: 20px;'>EXTEND BY YEAR</a>";


				$view = "<a href='".page_url."Leads/lead_quotation_view/".$row->id."' class='btn btn-success btn-xs' target='_blank'>view Quotation</a>";

				$lead_data[] = array('sr_no'=>$i,
				'create_date'=>date('d-m-Y', strtotime($row->create_date)),
				'company'=>$row->company_name,
				'company_name'=>$row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->postal_address,
				'products'=>$html,	
				'quotation' => $view,
				'extend_by' => $extend_by_week.'<br>'.$extend_by_half_month.'<br>'.$extend_by_month.'<br>'.$extend_by_year
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

		function lead_quotations_expiration_ext() {
		$today_date = date('Y-m-d');

		$lead_data = array();
		$query = $this->db->select('id, create_date, contact_no,customer_name,email,city,postal_address,company_name')
						  ->from('leads')
						  ->order_by('id','DESC')
						  ->get();

		$i=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$html='';
			$j=1;
			$sql1=$this->db->select('increased_validity_by, old_validity_date, new_validity_date')
						  ->from('lead_quotation_validity')
						  ->where('lead_id',$row->id)
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
				'create_date'=>date('d-m-Y', strtotime($row->create_date)),
				'company'=>$row->company_name,
				'company_name'=>$row->company_name,
				'customer_name'=>$row->customer_name,
				'email'=>$row->email,
				'mobile'=>$row->contact_no,
				'city'=>$row->city,
				'address'=>$row->postal_address,
				'products'=>$html
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

	function increase_validity() {
		$id = $this->uri->segment(3);
		$validity = $this->uri->segment(4);
		$today_date = date('Y-m-d H:i:s');

		$increased_date = '';

		$sql = $this->db->select('validity_date')
						->from('leads')
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
						 'lead_id' => $id,
						 'increased_validity_by' => $validity,
						 'old_validity_date' => $validity_date,
						 'new_validity_date' => $increased_date,
						 'updated_on' => $today_date,
						 'updated_by' => $_SESSION['logged_in']['user_id']
						 );

		$this->db->insert('lead_quotation_validity', $data_log);

		$data = array(
					'validity_date' => $increased_date
					 );

		$this->db->where('id', $id)
				 ->update('leads', $data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Validity successfully increased.</div>');
		redirect(page_url.'Leads/lead_quotations_expiring_today');
	}

		function preview_pdf_quote()
		{
			$this->load->view('leads/pdf_preview');
		}


		function sendclientintimation()
		{
			//echo "Mail and whatsapp has been disabled for now."; exit;
			$id=$this->uri->segment(3);
			$whatsapp=$this->input->post('whatsapp');
			$email=$this->input->post('email');
			$profile=$this->input->post('profile');
			
			if($email==1 && $email<>'')
			{
				$aemail=$this->input->post('aemail');
				//echo $aemail; exit;
				$ccemail=$this->input->post('ccemail');
				if($aemail<>'')
				{
				$this->send_mail_to_lead_with_pdf_new($id,$aemail,$profile,$ccemail);
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
				redirect(page_url.'Leads/preview_pdf_quote/'.$id);

		}

		public function send_mail_to_lead_with_pdf($id) {
		$uri=$id;

		if ($uri <> '') {
		    $sql = $this->db->select('b.first_name,b.last_name,b.email as salesemail,b.contact_number as salescontact,a.added_by,a.contact_person,a.company_name,a.customer_name,a.email_id, a.city,a.state,a.contact_no,a.postal_address,a.general_terms,a.bulk_terms,a.alt_contact_no,a. hpcl_company, a.unique_id, c.companyname as hpcl')
		                    ->from('leads a')
		                    ->join('system_users b','a.added_by=b.user_id')
		                    ->join('store_rack_location c', 'c.id=a.hpcl_company', 'left')
		                    ->where('a.id', $uri)
		                    ->get();

		    if ($sql->num_rows() > 0) {
		   
		        foreach ($sql->result() as $row);

		            $first_name = $row->first_name;
		            $last_name = $row->last_name;
		            $contact_person = $row->contact_person;
		            $unique_id=$row->unique_id;
		            $alt_contact_no = $row->alt_contact_no;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $city = $row->city;
		            $contact_no = $row->contact_no;
		            $address = $row->postal_address;
		            $general_terms = $row->general_terms;
		            $bulk_terms = $row->bulk_terms;
		            $email_id = $row->email_id;
		            $hpcl_company = $row->hpcl_company;
		            $salesemail=$row->salesemail;
		            $salescontact=$row->salescontact;
		            $hpcl=$row->hpcl;
		        } else {
		            $first_name = '';
		            $last_name = '';
		            $contact_person = '';
		            $unique_id='';
		            $alt_contact_no = '';
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
		            $hpcl='';
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
		// $smsmessage="Hello ".$customer_name." ji,\n\n";
		// 			$smsmessage.="Please find the quotation attachment for your requirement\n\n";
		$Message = "Dear ".$customer_name." Ji, <br><br>";
	    $Message .= "In reference to our meeting discussion held regarding Industrial Oil supply to
	                    your respective business units therefore, we hereby offer you attached Quotation for the
	                    products as required by yourself."."<br><br>";
	    $Message .= "Regards"."<br>";
	    $Message .= $first_name.' '.$last_name."<br>";
	    $Message .= $salescontact."<br>";
	    $Message .= $compemail."<br>";
	    $Message .= $compname."<br>";

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
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Competitor Product</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Our Equivalent Product</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Qty</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Offered Price</th>
  //                               </tr>';
                                
  //                               $sql2 = $this->db->select('a.competitor_product,a.qty, a.price, a.percent_amt, a.net_price, b.instruments_name, c.shortname')
  //                                                ->from('lead_products a')
  //                                                ->join('presto_instruments b', 'b.id=a.product_id')
  //                                                ->join('units c', 'c.shortname=b.unit')
  //                                                ->where('a.lead_id', $uri)
  //                                                ->get();
  //                               if ($sql2->num_rows() > 0) {
  //                                   foreach ($sql2->result() as $row2) {

  //                               $Message.='
  //                               <tr>
  //                               	<td style="padding: 5px; text-align:center;">'.$row2->competitor_product.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->instruments_name.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->qty.' '.$row2->shortname.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'. $row2->price.'</td>
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
 		// echo $Message; exit;

	$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";

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
		$this->email->attach($file, 'attachment', 'Industrial_Oil_Quotation.pdf');
		$this->load->library('encryption');
		$result11=$this->email->send();
		//echo $this->email->print_debugger(); exit;
		$data=array(
			'lead_id'=>$uri,
			'type'=>1,
			'mail_sent_on'=>date('Y-m-d H:i:s'),
			'mail_sent_by'=>$_SESSION['logged_in']['user_id']
		);
		$this->db->insert('lead_quotation_mail_history',$data);

	}
		
	}


	function whatsapp_quote_with_pdf($id)
	{
	

		 $sql = $this->db->select('b.first_name,b.last_name,b.email as salesemail,b.contact_number as salescontact,a.unique_id,a.contact_person,a.company_name,a.customer_name,a.email_id,a.city, a.state,a.contact_no,a.postal_address,a.general_terms,a.bulk_terms,a.alt_contact_no,a.hpcl_company,b.first_name,b.last_name')
		                    ->from('leads a')
		                    ->join('system_users b','a.added_by=b.user_id')
		                    ->where('id', $id)
		                    ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);
		            $contact_person = $row->contact_person;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $contact_no = $row->contact_no;
		            $unique_id=$row->unique_id;
		            $salescontact=$row->salescontact;

		            /** SEND WHATSAPP **/
					$smsmessage="Hello ".$customer_name." ji,\n\n";
					$smsmessage.="Please find the quotation attachment for your requirement\n\n";
					$smsmessage.="Regards\n";
					$smsmessage.=ucwords(strtolower($row->first_name))." ".ucwords(strtolower($row->last_name))."\n";
					$smsmessage.=$salescontact;

					// echo $smsmessage;exit;
					$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => '91'.$contact_no.",".$salescontact,
					// 'receiverMobileNo' => '918447031736',
					//'receiverMobileNo' => '919560814669',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'filePathUrl' => $file,
					'message'=>strip_tags(ucwords(strtolower($smsmessage))));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					// PDF 

					
					//$file=site_http_root."quotation_pdf/".$unique_id."_".str_replace(" ","_",$company_name).".pdf";
					// echo $file; exit;
				

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
					// // echo $result; exit;

					// if (curl_errno($ch)) {
					// echo 'Error:' . curl_error($ch);
					// }
					// curl_close($ch);
		            /** END **/
		            
					$data=array(
					'lead_id'=>$id,
					'type'=>2,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id']
					);
					$this->db->insert('lead_quotation_mail_history',$data);

		        } else {
		            $contact_person = '';
		            $customer_name = '';
		            $company_name = '';
		            $contact_no = '';		         
		        }

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

	function get_stages()
	{
		if(isset($q)){
		 $q = $_GET['searchTerm'];
		
		$query = $this->db->select('lead_id,lead_name')
				 		  ->from('lead_stage')
				 		  ->like('lead_name', $q, 'both')
						  ->get();
						}else{
							$query = $this->db->select('lead_id,lead_name')
				 		  ->from('lead_stage')
				 		  ->order_by('lead_name','ASC')
						  ->get();
						}

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->lead_id, 'text'=>$row->lead_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		
		echo json_encode($json);

	}

	function getAllProducts() {
		
		if(isset($q)){
		$q = $_GET['q'];

	    $query = $this->db->select('id,instruments_name')
				 		  ->from('presto_instruments')
				 		  ->like('instruments_name', $q, 'both')
						  ->get();
}else{
	 $query = $this->db->select('id,instruments_name')
				 		  ->from('presto_instruments')
				 		  ->order_by('instruments_name','ASC')
						  ->get();
}
		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->id, 'text'=>$row->instruments_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		
		echo json_encode($json);
	}

	function get_lead_source() {
		if(isset($q)){
		$q = $_GET['q'];

	    $query = $this->db->select('source_id,lead_source')
				 		  ->from('lead_source')
				 		  ->like('lead_source', $q, 'both')
						  ->get();
						}else{
						 $query = $this->db->select('source_id,lead_source')
				 		  ->from('lead_source')
				 		  ->order_by('lead_source','ASC')
						  ->get();	
						}

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->source_id, 'text'=>$row->lead_source);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		
		echo json_encode($json);
	}

	function getUnqualifiedReason() {
		
if(isset($q)){
		$q = $_GET['q'];

	    $query = $this->db->select('reason_id, reason')
				 		  ->from('leads_unqualified_reason')
				 		  ->like('reason', $q, 'both')
						  ->get();
						}else{
						  $query = $this->db->select('reason_id, reason')
				 		  ->from('leads_unqualified_reason')
				 		  ->order_by('reason','ASC')
						  ->get();	
						}

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->reason_id, 'text'=>$row->reason);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		
		echo json_encode($json);
	}

	function get_all_qty() {
		$q = $_GET['searchTerm'];

	    $query = $this->db->select('qty')
				 		  ->from('lead_products')
				 		  ->like('qty', $q, 'both')
				 		  ->group_by('qty')
						  ->get();

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->qty, 'text'=>$row->qty);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		
		echo json_encode($json);
	}


	function visits()
	{
		$this->load->view('leads/visit_stage');

	}



	public function visit_list() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();

	
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);
	$close_visit = "<a href='".page_url."Leads/close_visit/".$row->id."' class='btn btn-warning btn-xs'>Close Visit</a>";

	if($row->followup_date<>'0000-00-00')
	{
		$follow=date('d-M-Y',strtotime($row->followup_date));
	}else
	{
		$follow='';
	}

			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),								
								'lastupdatedon'=>$d,
								'next_visit'=>$follow,
								'close_visit'=> $close_visit
								
								
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


	function visit_based_attachment($lead_id)
	{
		$audio='';
		$ret=$this->db->select('attachment')->from('visit_based_attachment')->where('lead_id',$lead_id)->get();
	 if($ret->num_rows()>0)
	 {
	 	$i=1;
	 	foreach($ret->result() as $row)
	 	{
	 		$audio.='<a href="'.site_http_root.'image_bank/lead_based_attachment/'.$row->attachment.'" download
	 		>Download File '.$i.'</a><br/>';
	 		$i++;
	 	}

	 }

	 return $audio;
	}
	

	function user_wise_visit()
	{


		$this->load->view('leads/visit_stage_user_wise');

	}



	public function visit_list_user_wise() {

		$lead_data = array();

	
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 AND b.added_by=$user_id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>date('d-M-Y',strtotime($row->followup_date)),
								
								
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


	function todays_visit()
	{

		$this->load->view('leads/visit_stage_today');
	}


		public function visit_list_today() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);
		$flag=$this->uri->segment(6);
		// 1 for only visit no lead should be created

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.create_date>='$start_date' AND b.create_date<='$end_date'";
		}else
		{
			$chk='';
		}

		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted!=2 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=1;
			if($flag==1)
			{
				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->leadid)->get();
				if($rty->num_rows()>0)
				{
					$show=0;
				}
			}
		
	if($show==1)
	{
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


	if($row->followup_date<>'0000-00-00')
	{
		$follow=date('d-M-Y',strtotime($row->followup_date));
	}else
	{
		$follow='';
	}
			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>$follow,
								
								
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

	function todays_visit_old_customer()
	{

		$this->load->view('leads/old_customer_visit_report');
	}

	function filter_todays_visit()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		$d=$this->uri->segment(3);
		redirect(page_url.'Leads/todays_visit/'.$a.'/'.$b.'/'.$c.'/'.$d);
	}


	function todays_visit_userwise()
	{

		$this->load->view('leads/visit_stage_today_userwise');
	}


		public function todays_visit_user_wise() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.create_date>='$start_date' AND b.create_date<='$end_date'";
		}else
		{
			$chk='';
		}

		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>date('d-M-Y',strtotime($row->followup_date)),
								
								
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

	function filter_todays_visit_userwise()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		redirect(page_url.'Leads/todays_visit_userwise/'.$a.'/'.$b.'/'.$c);
	}

	function todays_visit_userwise_scheduled()
	{
		$this->load->view('leads/visit_stage_today_userwise_scheduled');
	}


	public function todays_visit_user_wise_scheduled() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.followup_date='$start_date' AND b.followup_date='$end_date'";
		}else
		{
			$chk='';
		}

		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>date('d-M-Y',strtotime($row->followup_date)),
								
								
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


function todays_visit_scheduled()
{
	$this->load->view('leads/visit_stage_today_scheduled');
}


public function visit_list_today_scheduled() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.followup_date>='$start_date' AND b.followup_date<='$end_date'";
		}else
		{
			$chk='';
		}

		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


	if($row->followup_date<>'0000-00-00')
	{
		$follow=date('d-M-Y',strtotime($row->followup_date));
	}else
	{
		$follow='';
	}

			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>$follow
								
								
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

	function filter_todays_visit_schedule()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		redirect(page_url.'Leads/todays_visit_scheduled/'.$a.'/'.$b.'/'.$c);
	}

	function getCompanyProducts() {

		if(isset($_GET['searchTerm'])) {
			$searchtrm= $_GET['searchTerm'];
		}else {
			$searchtrm='';
		}

		if(isset($_GET['company'])) {
			$company= $_GET['company'];
		}else {
			$company='';
		}


		  $query = $this->db->select('b.id, b.instruments_name')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->like('b.instruments_name', $searchtrm, 'both')
	                        ->where('c.company_id', $company)
	                        ->get();


		if($query->num_rows()>0) {
			foreach($query->result() as $instruments){
				$json[] = array('id'=>$instruments->id, 'text'=>$instruments->instruments_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		echo json_encode($json);

	}

	function delete_quote_product() {
		$id = $this->uri->segment(3);
		$quotation_id = $this->uri->segment(4);

		$this->db->where('id', $id)
				 ->delete('customer_quotation_detail');

		$this->session->set_flashdata('message','<div class="alert alert-success">Product successfully deleted.</div>');
		redirect(page_url.'Leads/generate_order/'.$quotation_id);
	}

	function close_visit() {
		$visit_id = $this->uri->segment(3);

		$data = array(
					 'converted' => 1,
					 'closed' => 1,
					 'closed_on' => date('Y-m-d H:i:s'),
					 'closed_by' => $_SESSION['logged_in']['user_id']
					 );

		$this->db->where('id', $visit_id)
				 ->update('daily_visits', $data);

		$this->session->set_flashdata('message','<div class="alert alert-success">Visit Closed Successfully.</div>');
		redirect(page_url.'Leads/visits');
	}

	function closed_visits()
	{
		$this->load->view('leads/closed_visits');

	}

	public function closed_visits_list() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();

	
		$user_id=$_SESSION['logged_in']['user_id'];
		$contactacess=$this->Lead_model->checkforcontactacess($user_id);

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by, b.closed_on, b.closed_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 AND b.closed=1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);
	$closed_by = $this->salescrm->getusername($row->closed_by);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);
	$close_visit = "<a href='".page_url."Leads/close_visit/".$row->id."' class='btn btn-warning btn-xs'>Close Visit</a>";


			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),								
								'lastupdatedon'=>$d,
								'next_visit'=>date('d-M-Y',strtotime($row->followup_date)),
								'closed_on'=> date('d-M-Y',strtotime($row->closed_on)),
								'closed_by'=> $closed_by
								
								
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


	function common_approval()
	{
		$this->load->view('leads/common_approval');

	}


	function specfile_approval()
	{

		$lead_data = array();

		$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('status',0)->get();
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			if($row->type==1)
			{
				$t="For Product";
			}else
			{
				$t="For Competitor Product";
			}

			if($row->product_id!=0)
			{
			$prdname=$this->getproduct_name($row->product_id);
			if(count($prdname)>0)
			{
				$prdname=$prdname[0];
				$prdname1='';
					
			}

			if($row->file_type==1)
					{
					$specfile="<a href='".page_url1."image_bank/instrumentimg/".$row->spec_file."' download>Download Spec File</a>";
					$msds_file='';
					}else
					{
						$specfile='';
						$msds_file="<a href='".page_url1."image_bank/instrumentimg/".$row->spec_file."' download>Download MSDS</a>";
					}


			}else
			{

				$prdname1=$row->competitor_name;
				$prdname='';
					if($row->file_type==1)
					{
					$specfile="<a href='".page_url1."competitor_files/".$row->spec_file."' download>Download Spec File</a>";
					$msds_file='';
					}else
					{
						$msds_file="<a href='".page_url1."competitor_files/".$row->spec_file."' download>Download MSDS File</a>";
						$specfile='';
					}
			}



			$approval="<a href='javascript:;' class='btn btn-sm btn-warning' onclick='approve_data(".$row->id.")'>Approve</a>";
			$reject="<a href='javascript:;'   class='btn btn-sm btn-danger' onclick='reject_n_upload(".$row->id.")'>Reject</a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'type' =>$t,
								'product' =>$prdname,
								'competitor_prd' =>$prdname1,
								'specfile' =>$specfile,
								'msdsfile' =>$msds_file,
								'addedon' =>date('d-M-Y',strtotime($row->addedOn)),
								'addedby'=>$row->first_name." ".$row->last_name,
								'approve' =>$approval,								
								'reject'=>$reject
								
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

	function getproduct_name($product_id)
	{
		$d=array();
		$restey=$this->db->select('instruments_name,spec_file,msds_file')->from('presto_instruments')->where('id',$product_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $rowss);
			$d[]=$rowss->instruments_name;
			$d[]=$rowss->spec_file;
			$d[]=$rowss->msds_file;

		}

		return $d;
	}

	function approve_files()
	{
		$id=$this->uri->segment(3);

		$resteye=$this->db->select('spec_file,type,file_type,product_id,competitor_name')->from('product_competitor_files')->where('id',$id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);


			if($row->type==1)
			{

				$d=$this->getproduct_name($row->product_id);
				if(count($d)>0)
				{ 
					$msds=$d[2];
					$spec=$d[1];

				}else{ 

					$msds='';
					$spec=''; 
					}


				if($row->file_type==1)
				{
					$specs=$row->spec_file;
					$msds=$msds;
				}else
				{
					$specs=$spec;
					$msds=$row->spec_file;
				}

				$data=array('spec_file'=>$specs,'msds_file'=>$msds);
				$this->db->where('id',$row->product_id);
				$this->db->update('presto_instruments',$data);
				

			}else
			{

				$d=$this->get_competitor_data($row->competitor_name);

				if(count($d)>0)
				{ 
					$msds=$d[2];
					$spec=$d[1];

				}else{ 

					$msds='';
					$spec=''; 
					}

				if($row->file_type==1)
				{
					$specs=$row->spec_file;
					$msds=$msds;
				}else
				{
						
					$specs=$spec;
					$msds=$row->spec_file;
				}


				$data=array('spec_file'=>$specs,'msds'=>$msds);
				$this->db->where('client_product',$row->competitor_name);
				$this->db->update('equivalent_chart',$data);

			}

			/** UPDATE **/

			$ed=array('status'=>1);
			$this->db->where('id',$id);
			$this->db->update('product_competitor_files',$ed);
		}

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Leads/common_approval');

	}

	function get_competitor_data($prd)
	{
		$d=array();
		$restey=$this->db->select('client_product,spec_file,msds')->from('equivalent_chart')->where('client_product',$prd)->get();
		if($restey->num_rows()>0)
		{	foreach($restey->result() as $rowss);
			$d[]=$rowss->client_product;
			$d[]=$rowss->spec_file;
			$d[]=$rowss->msds;

		}

		return $d;

	}

	function get_files_data()
	{
		$d='';
		$id=$this->uri->segment(3);

		$restey=$this->db->select('file_type')->from('product_competitor_files')->where('id',$id)->get();
		if($restey->num_rows()>0)
		{	foreach($restey->result() as $rowss);
			if($rowss->file_type==1)
			{
				$d="Specification File";
			}else
			{
				$d="MSDS File";
			}

		}

		echo $d;


	}

	function reject_upload()
	{
		$id=$this->input->post('record_id');

		$resteye=$this->db->select('spec_file,type,file_type,product_id,competitor_name')->from('product_competitor_files')->where('id',$id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);


			if($row->type==1)
			{

				$d=$this->getproduct_name($row->product_id);
				if(count($d)>0)
				{ 
					$msds=$d[2];
					$spec=$d[1];

				}else{ 

					$msds='';
					$spec=''; 
					}



			


				


				if($row->file_type==1)
				{
					$specs=$row->spec_file;
					$msds=$msds;

					$photo=$_FILES['upload']['name'];
					if($photo <> '') {
					$image1 = explode('.',$photo);
					$cat_image = end($image1);
					$instrumentimg_spec = time().'.'.$cat_image;
					move_uploaded_file($_FILES["upload"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg_spec);
					} else {
					$instrumentimg_spec = $spec;
					}

					$instrumentimg_msds=$msds;

				}else
				{
					$specs=$spec;
					$msds=$row->spec_file;

					$photo=$_FILES['upload']['name'];
					if($photo <> '') {
					$image1 = explode('.',$photo);
					$cat_image = end($image1);
					$instrumentimg_msds = time().'.'.$cat_image;
					move_uploaded_file($_FILES["upload"]["tmp_name"],UPLOADPATH.'competitor_files/' . $instrumentimg_spec);
					} else {
					$instrumentimg_msds = $msds;
					}

					$instrumentimg_spec=$specs;
				}

				$data=array('spec_file'=>$instrumentimg_spec,'msds_file'=>$instrumentimg_msds);
				$this->db->where('id',$row->product_id);
				$this->db->update('presto_instruments',$data);
				

			}else
			{

				$d=$this->get_competitor_data($row->competitor_name);

				if(count($d)>0)
				{ 
					$msds=$d[2];
					$spec=$d[1];

				}else{ 

					$msds='';
					$spec=''; 
					}

				if($row->file_type==1)
				{
					$specs=$row->spec_file;
					$msds=$msds;

						$photo=$_FILES['upload']['name'];
					if($photo <> '') {
					$image1 = explode('.',$photo);
					$cat_image = end($image1);
					$instrumentimg_spec = time().'.'.$cat_image;
					move_uploaded_file($_FILES["upload"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg_spec);
					} else {
					$instrumentimg_spec = $spec;
					}

					$instrumentimg_msds=$msds;


				}else
				{
						
					$specs=$spec;
					$msds=$row->spec_file;


					$photo=$_FILES['upload']['name'];
					if($photo <> '') {
					$image1 = explode('.',$photo);
					$cat_image = end($image1);
					$instrumentimg_msds = time().'.'.$cat_image;
					move_uploaded_file($_FILES["upload"]["tmp_name"],UPLOADPATH.'competitor_files/' . $instrumentimg_spec);
					} else {
					$instrumentimg_msds = $msds;
					}

					$instrumentimg_spec=$specs;
				}

 


				$data=array('spec_file'=>$instrumentimg_spec,'msds'=>$instrumentimg_msds);
				$this->db->where('client_product',$row->competitor_name);
				$this->db->update('equivalent_chart',$data);

			}

			/** UPDATE **/

			$ed=array('status'=>1);
			$this->db->where('id',$id);
			$this->db->update('product_competitor_files',$ed);


	}


			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/common_approval');
}


	function triggers()
	{
		$this->load->view('leads/trigger_notification');
	}

function common_approval_user()
	{
		$this->load->view('leads/common_approval_user');

	}

		function specfile_approval_user()
	{

		$lead_data = array();

		$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('status',0)->where('a.addedby',$_SESSION['logged_in']['user_id'])->get();
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			if($row->type==1)
			{
				$t="For Product";
			}else
			{
				$t="For Competitor Product";
			}

			if($row->product_id!=0)
			{
			$prdname=$this->getproduct_name($row->product_id);
			if(count($prdname)>0)
			{
				$prdname=$prdname[0];
				$prdname1='';
					
			}

			if($row->file_type==1)
					{
					$specfile="<a href='".page_url1."image_bank/instrumentimg/".$row->spec_file."' download>Download Spec File</a>";
					$msds_file='';
					}else
					{
						$specfile='';
						$msds_file="<a href='".page_url1."image_bank/instrumentimg/".$row->spec_file."' download>Download MSDS</a>";
					}


			}else
			{

				$prdname1=$row->competitor_name;
				$prdname='';
					if($row->file_type==1)
					{
					$specfile="<a href='".page_url1."competitor_files/".$row->spec_file."' download>Download Spec File</a>";
					$msds_file='';
					}else
					{
						$msds_file="<a href='".page_url1."competitor_files/".$row->spec_file."' download>Download MSDS File</a>";
						$specfile='';
					}
			}



			$approval="<a href='javascript:;' class='btn btn-sm btn-warning' onclick='approve_data(".$row->id.")'>Approve</a>";
			$reject="<a href='javascript:;'   class='btn btn-sm btn-danger' onclick='reject_n_upload(".$row->id.")'>Reject</a>";

			$lead_data[] = array(
								'sr_no'=>$i,
								'type' =>$t,
								'product' =>$prdname,
								'competitor_prd' =>$prdname1,
								'specfile' =>$specfile,
								'msdsfile' =>$msds_file,
								'addedon' =>date('d-M-Y',strtotime($row->addedOn)),
								'addedby'=>$row->first_name." ".$row->last_name,
								'approve' =>$approval,								
								'reject'=>$reject
								
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



	function equivalent_file_approval()
	{

		$lead_data = array();

		$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('equivalent_chart
 a')->join('system_users b','a.addedby=b.user_id','left')->where('a.approved',0)->where('a.inactive',0)->where('client_product!=','NA')->where('client_product!=','')->get();
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			$approval="<a href='javascript:;' class='btn btn-sm btn-warning' onclick='approve_data_equ(".$row->id.")'>Approve</a>";
			$reject="<a href='javascript:;'   class='btn btn-sm btn-danger' onclick='reject_n_upload_equ(".$row->id.")'>Reject</a>";

			$check="<input type='checkbox' name='myCheckboxes[]' id='myCheckboxes' class='checkBoxClass' data-toggle='tooltip' data-placement='right' data-original-title='Click here to Select' onChange='getthepreviousstage(".$row->id.");' value='".$row->id."' />";

					$lead_data[] = array(
					'check'=>$check,
					'sr_no'=>$i,
					'competitor_prd' =>$row->client_product,
					'product' =>$row->our_product,
					'addedon' =>date('d-M-Y',strtotime($row->addedOn)),
					'addedby'=>$row->first_name." ".$row->last_name,
					'approve' =>$approval,								
					'reject'=>$reject

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

	function approve_equivalent()
	{
		$id=$this->uri->segment(3);

		$data=array('approved'=>1,'approvedBy'=>$_SESSION['logged_in']['user_id'],'approvedOn'=>date('Y-m-d H:i:s'));
				$this->db->where('id',$id);
				$this->db->update('equivalent_chart',$data);

				$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Leads/common_approval');


	}

	function reject_upload_eu()
	{
		$record_id_eq=$this->input->post('record_id_eq');
		$rack_location=$this->input->post('rack_location');
		$restey=$this->db->select('client_product')->from('equivalent_chart')->where('id',$record_id_eq)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$comp_file=$row->client_product;


		for($i=0;$i<count($rack_location);$i++)
		{
			 $dd=array('client_product'=>$comp_file,
			'our_product'=>$rack_location[$i],
			'addedby'=>$_SESSION['logged_in']['user_id'],
			'addedOn'=>date('Y-m-d H:i:s'),
			'approved'=>1,
			'approvedBy'=>$_SESSION['logged_in']['user_id'],
			'approvedOn'=>date('Y-m-d H:i:s'));

		 $this->db->insert('equivalent_chart',$dd);

		}

		$this->db->where('id',$record_id_eq);
		$this->db->delete('equivalent_chart');

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Leads/common_approval');


		}	

	}

	public function visit_list_today_for_old_customer() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);
		$customer=$this->uri->segment(6);
		$startdate1 = $start_date." 00:00:00";
		$end_date1 = $end_date." 23:59:59";

		$lead_data = array();
		$d=date('Y-m-d');

		$this->db->select('a.remarks, a.added_on, b.company_name, b.customer_name, c.first_name, c.last_name')->from('oldcustomer_visit a')->join('customer_detail b','a.customer_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.added_on BETWEEN "'.$startdate1. '" and "'.$end_date1.'"');
		if($user=='ALL'){
			
		}else{
			$this->db->where('a.added_by',$user);
		}

		if($customer<>'ALL')
		{
			$this->db->where('a.customer_id',$customer);
		}
		$resty= $this->db->get();

		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		


	
			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'remarks' => $row->remarks,
								'addedon'=>$row->added_on,
								'addedby'=>$row->first_name." ".$row->last_name,
								
								
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

function filter_todays_visit_old_customer()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		$d=$this->input->post('customer');
		redirect(page_url.'Leads/todays_visit_old_customer/'.$a.'/'.$b.'/'.$c.'/'.$d);
	}

	function todays_visit_userwise_for_old_customer()
	{

		$this->load->view('leads/visit_stage_today_userwise_old_customer');
	}

		public function todays_visit_userwise_for_old_customer_list() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$_SESSION['logged_in']['user_id'];
		$startdate1 = $start_date." 00:00:00";
		$end_date1 = $end_date." 23:59:59";

		$lead_data = array();
		$d=date('Y-m-d');

		$this->db->select('a.remarks, a.added_on, b.company_name, b.customer_name, c.first_name, c.last_name')->from('oldcustomer_visit a')->join('customer_detail b','a.customer_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.added_on BETWEEN "'.$startdate1. '" and "'.$end_date1.'"');
		$this->db->where('a.added_by',$user);
		$resty= $this->db->get();

		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
		


	
			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'remarks' => $row->remarks,
								'addedon'=>$row->added_on,
								'addedby'=>$row->first_name." ".$row->last_name,
								
								
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

	function filter_todays_old_customer_visit_userwise()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		redirect(page_url.'Leads/todays_visit_userwise_for_old_customer/'.$a.'/'.$b.'/'.$c);
	}

	function reject_hold()
	{
		$hold_order_id=$this->input->post('hold_order_id');
		$reject_hold_rmk=$this->input->post('reject_hold_rmk');

		$d=array('hold_reject'=>1,'hold_reject_by'=>$_SESSION['logged_in']['user_id'],'hold_reject_on'=>date('Y-m-d H:i:s'),'hold_reject_remarks'=>$reject_hold_rmk);

		$this->db->where('id',$hold_order_id);
		$this->db->update('order_punch',$d);

		if($this->db->affected_rows()>0)
		{
			$dt=$this->salescrm->order_punch_details($hold_order_id);

			if(count($dt)>0)
			{
			$message="Hello ".$dt[1]."\n\n";
			$message.="Your following Order Hold Remove Request has been rejected \n\n";
			$message.="Company *".trim($dt[0])."*\n\n";
			$message.="Invoice No. *".trim($dt[3])."*\n\n";
			$message.="Reason. *".trim($reject_hold_rmk)."* \n\n";
			$message.="Thanks";

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				$post = array(
				//'receiverMobileNo' => '8447031736',
				'receiverMobileNo' => $dt[2].',8447031736',
				'username' => whatsappuser,
				'password' => whatsapppass,
				'message'=>strip_tags($message));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				$result = curl_exec($ch);
				//echo $result; exit;
				if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Hold Request Rejected.</div>');
			redirect(page_url.'Leads/common_approval');


	}

	function getProductsData() {

		if(isset($_GET['searchTerm'])) {
			$searchtrm= $_GET['searchTerm'];
		}else {
			$searchtrm='';
		}

		if(isset($_GET['company'])) {
			$company= $_GET['company'];
		}else {
			$company='';
		}


		  $query = $this->db->select('b.id, b.instruments_name')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->like('b.instruments_name', $searchtrm, 'both')
	                    
	                        ->get();


		if($query->num_rows()>0) {
			foreach($query->result() as $instruments){
				$json[] = array('id'=>$instruments->id, 'text'=>$instruments->instruments_name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}

		echo json_encode($json);

	}

	function generate_order() {
		
		$this->load->view('customer/existing_order_form');
	}
	function drum_transportation_cost_data() {
		
		$this->load->view('customer/drum_transportation_cost_data');
	}

	public function drum_transportation_cost(){
		$this->load->view('customer/edit_drum_transportation_cost');
	}

	public function update_drum_transportation_cost(){
	
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		  
		   $data=
			array(
			'type'=>$this->input->post('transportation_type'),
			'cost'=>$this->input->post('cost'));
			$this->db->where('id',$this->uri->segment(3));

			$res = $this->db->update('drum_transportation_cost',$data);
			if($res)
			{
				$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully updated.</span></div><br/>');
				redirect(page_url.'Leads/drum_transportation_cost_data');
				}
			
		
}

function typestatus() {
		
		$this->load->view('customer/type_status');
	}

public function add_typestatus()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('typestatus', 'Type Status', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('customer/type_status');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "typestatus";
			
			$data = array('typestatus'=>$this->input->post('typestatus'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->master->insert_record($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/typestatus');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Leads/typestatus');
		}
		
		
	}
		
	}

function edittypestatus(){
	$this->load->view('customer/edit_typestatus');
}

public function update_typestatus()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('typestatus', 'Type Status', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('customer/edit_typestatus');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "typestatus";
			
		$data = array('typestatus'=>$this->input->post('typestatus'),
		'status'=>$this->input->post('status'),
		'added_on'=>$date,
		'added_by'=>$user_id);
		$this->db->where('id',$this->uri->segment(3));
		
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/typestatus');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Leads/typestatus');
		}
		
		
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



	function getinvoice_no_new($company)
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

			$compid=$company;

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

	function bulk_equivalent_action()
	{
		$items=$this->input->post('items');
		$action_type=$this->input->post('action_type');
		$d=explode(',',$items);
		for($i=0;$i<count($d);$i++)
		{
			$id=$d[$i];
			

			if($action_type==1)
			{

			$dd=array('inactive'=>1,'approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->where('id',$id);
			$this->db->update('equivalent_chart',$dd);

			}else if($action_type==2)
			{

			$dd=array('approved'=>1,'approvedOn'=>date('Y-m-d H:i:s'),'approvedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->where('id',$id);
			$this->db->update('equivalent_chart',$dd);

			}else
			{

			}

		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
		redirect(page_url.'Leads/common_approval');

	}


	public function send_mail_to_lead_with_pdf_new($id,$cust_email,$profile, $ccemail) {
		$uri=$id;

		if ($uri <> '') {
		    $sql = $this->db->select('b.first_name,b.last_name,b.email as salesemail,b.contact_number as salescontact,a.added_by,a.contact_person,a.company_name,a.customer_name,a.email_id, a.city,a.state,a.contact_no,a.postal_address,a.general_terms,a.bulk_terms,a.alt_contact_no,a. hpcl_company, a.unique_id, c.companyname as hpcl')
		                    ->from('leads a')
		                    ->join('system_users b','a.added_by=b.user_id')
		                    ->join('store_rack_location c', 'c.id=a.hpcl_company', 'left')
		                    ->where('a.id', $uri)
		                    ->get();

		    if ($sql->num_rows() > 0) {
		   
		        foreach ($sql->result() as $row);

		            $first_name = $row->first_name;
		            $last_name = $row->last_name;
		            $contact_person = $row->contact_person;
		            $unique_id=$row->unique_id;
		            $alt_contact_no = $row->alt_contact_no;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $city = $row->city;
		            $contact_no = $row->contact_no;
		            $address = $row->postal_address;
		            $general_terms = $row->general_terms;
		            $bulk_terms = $row->bulk_terms;
		            $email_id = $row->email_id;
		            $hpcl_company = $row->hpcl_company;
		            $salesemail=$row->salesemail;
		            $salescontact=$row->salescontact;
		            $hpcl=$row->hpcl;
		        } else {
		            $first_name = '';
		            $last_name = '';
		            $contact_person = '';
		            $unique_id='';
		            $alt_contact_no = '';
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
		            $hpcl='';
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

		$subjectname='Quotation of Products.';
		// $smsmessage="Hello ".$customer_name." ji,\n\n";
		// 			$smsmessage.="Please find the quotation attachment for your requirement\n\n";
		$Message = "Dear ".ucwords(strtolower($customer_name)).", <br><br>";
	    $Message .= "Thank you for taking out time to meet us. In reference to our meeting discussion held
regarding cleaning chemicals supply to your respective business units, we hereby offer you."."<br>Quotation for the products below.<br><br>
Kindly let us know when we can meet and close the contract.<br>";
	    $Message .= "Regards"."<br>";
	    $Message .= $first_name.' '.$last_name."<br>";
	    $Message .= $salescontact."<br>";
	    $Message .= $compemail."<br>";
	    $Message .= ucwords(strtolower($compname))."<br>";

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
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Competitor Product</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Our Equivalent Product</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Qty</th>
	 //                                <th style="padding: 5px; background-color: lightgray;" width="25%">Offered Price</th>
  //                               </tr>';
                                
  //                               $sql2 = $this->db->select('a.competitor_product,a.qty, a.price, a.percent_amt, a.net_price, b.instruments_name, c.shortname')
  //                                                ->from('lead_products a')
  //                                                ->join('presto_instruments b', 'b.id=a.product_id')
  //                                                ->join('units c', 'c.shortname=b.unit')
  //                                                ->where('a.lead_id', $uri)
  //                                                ->get();
  //                               if ($sql2->num_rows() > 0) {
  //                                   foreach ($sql2->result() as $row2) {

  //                               $Message.='
  //                               <tr>
  //                               	<td style="padding: 5px; text-align:center;">'.$row2->competitor_product.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->instruments_name.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'.$row2->qty.' '.$row2->shortname.'</td>
  //                                   <td style="padding: 5px; text-align:center;">'. $row2->price.'</td>
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
 		// echo $Message; exit;

	$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";

$com=$this->db->select('smtp,email,password,profile')->from('store_rack_location')->where('id',$hpcl_company)->get();
if($com->num_rows() >0){
foreach($com->result() as $company);


//echo $Message; exit;
	$profile_file=$company->profile;
      $config['protocol'] = 'ssmtp';  
		$config['smtp_host'] = $company->smtp;  
		$config['smtp_user'] = $company->email;  
		$config['smtp_pass'] = $company->password;   
		$config['smtp_port'] = 465;   
		//$config['smtp_crypto'] = 'ssl';
		$config['newline'] = "\r\n";
		$config['starttls'] = TRUE;
		$config['charset'] = 'iso-8859-1';
		$config['mailtype'] = 'html';

        $this->email->initialize($config);  
        $this->load->library('email', $config);
        $this->email->set_header('Header1', 'Value1');
		$this->email->set_mailtype("html");
		$this->email->to($cust_email);
		if($ccemail<>''){
			$this->email->cc($ccemail);	
		}
		$this->email->bcc('sdsrbh5@gmail.com,'.$salesemail);
		$this->email->from($company->email);
		
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$this->email->attach($file, 'attachment', 'Industrial_Oil_Quotation.pdf');
		if($profile==1 && $profile_file<>'')
		{
			if(file_exists(UPLOADPATH.'profile/'.$profile_file))
			{
				$pfile=site_http_root."image_bank/profile/".$profile_file;
				$this->email->attach($pfile, 'attachment', 'Company_Profile.pdf');
			}

		}
		// $this->load->library('encryption');
		$result11=$this->email->send();
		echo $this->email->print_debugger(); exit;
		$data=array(
			'lead_id'=>$uri,
			'type'=>1,
			'mail_sent_on'=>date('Y-m-d H:i:s'),
			'mail_sent_by'=>$_SESSION['logged_in']['user_id'],
			'send_to'=>$cust_email
		);
		$this->db->insert('lead_quotation_mail_history',$data);

	}
		
	}


	function whatsapp_quote_with_pdf_new($id,$cust_mobile,$profile)
	{
	

		 $sql = $this->db->select('b.first_name,b.last_name,b.email as salesemail,b.contact_number as salescontact,a.unique_id,a.contact_person,a.company_name,a.customer_name,a.email_id,a.city, a.state,a.contact_no,a.postal_address,a.general_terms,a.bulk_terms,a.alt_contact_no,a.hpcl_company,b.first_name,b.last_name')
		                    ->from('leads a')
		                    ->join('system_users b','a.added_by=b.user_id')
		                    ->where('id', $id)
		                    ->get();

		    if ($sql->num_rows() > 0) {
		        foreach ($sql->result() as $row);
		            $contact_person = $row->contact_person;
		            $customer_name = $row->customer_name;
		            $company_name = $row->company_name;
		            $contact_no = $row->contact_no;
		            $unique_id=$row->unique_id;
		            $salescontact=$row->salescontact;
		            $hpcl_company=$row->hpcl_company;

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

					// echo $smsmessage;exit;
					$file=site_http_root."quotation_pdf/".str_replace(" ","%20",$unique_id)."_".str_replace(" ","_",$company_name).".pdf";

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					'receiverMobileNo' => $cust_mobile.",".$salescontact,
					
					'username' => whatsappuser,
					'password' => whatsapppass,
					'filePathUrl' => $file,
					'message'=>strip_tags(ucwords(strtolower($smsmessage))));
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

						'username' => whatsappuser,
						'password' => whatsapppass,
						'filePathUrl' => $pfile
						);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
						$result = curl_exec($ch);
						//echo $result; exit;
						if (curl_errno($ch)) {
						echo 'Error:' . curl_error($ch);
						}
						curl_close($ch);




						}

						}

				
		            
					$data=array(
					'lead_id'=>$id,
					'type'=>2,
					'mail_sent_on'=>date('Y-m-d H:i:s'),
					'mail_sent_by'=>$_SESSION['logged_in']['user_id'],
					'send_to'=>$cust_mobile
					);
					$this->db->insert('lead_quotation_mail_history',$data);

		        } else {
		            $contact_person = '';
		            $customer_name = '';
		            $company_name = '';
		            $contact_no = '';		         
		        }

	}


	function check_stock_availability()
	{
		$company=$this->input->post('compid');
		$qty=$this->input->post('qty');
		$product_id=$this->input->post('product_id');
		$stock_avail=$this->salescrm->get_stock_availability($product_id,$company);
		if($qty>$stock_avail)
		{
			echo 0;
		}else
		{
			echo 1;
		}
	}


	function check_stock_available()
	{
		$company=1;
		$product_id=$this->input->post('product_id');
		$stock_avail=$this->salescrm->get_stock_availability($product_id,$company);
		
		echo $stock_avail;
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

	function new_visit()
	{

		$this->load->view('leads/new_visits');
	}


	public function new_visit_list_today() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);
		$flag=$this->uri->segment(6);
		// 1 for only visit no lead should be created

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.create_date>='$start_date' AND b.create_date<='$end_date'";
		}else
		{
			$chk='';
		}

		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=1;
			if($flag==1)
			{

				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->leadid)->get();
				if($rty->num_rows()>0)
				{
					$show=0;
				}



			}

			$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->leadid)->get();
			if($rty1->num_rows()>0)
				{
					$show=0;
				}
		
	if($show==1)
	{
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


	if($row->followup_date<>'0000-00-00')
	{
		$follow=date('d-M-Y',strtotime($row->followup_date));
	}else
	{
		$follow='';
	}
			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date)),
								'lastupdatedon'=>$d,
								'next_visit'=>$follow,
								
								
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


	function filter_todays_visit_new()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
		$b=date('Y-m-d',strtotime($this->input->post('to_date')));
		$c=$this->input->post('user');
		$d=$this->uri->segment(3);
		redirect(page_url.'Leads/new_visit/'.$a.'/'.$b.'/'.$c.'/'.$d);
	}

	function incomplete_visits(){
		$this->load->view('leads/incomplete_visits');
	}


		public function incomplete_visit_list_today() {

		$start_date=$this->uri->segment(3);
		$end_date=$this->uri->segment(4);
		$user=$this->uri->segment(5);
		$flag=$this->uri->segment(6);
		// 1 for only visit no lead should be created

		if($start_date<>'' && $end_date<>'')
		{
			$chk="AND b.create_date>='$start_date' AND b.create_date<='$end_date'";
		}else
		{
			$chk='';
		}


		$chk='';
		if($user<>'' && $user<>'ALL')
		{
			$chk1="AND b.added_by='$user'";
		}else
		{
			$chk1='';
		}

		$lead_data = array();
		$d=date('Y-m-d');

		$resty=$this->db->query("SELECT b.followup_date,b.id,c.user_id,c.first_name,c.last_name,b.alt_contact,b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.client_location,b.added_by FROM daily_visits b JOIN system_users c ON b.added_by=c.user_id WHERE b.converted=0 $chk $chk1 ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=0;
			

				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->leadid)->get();
				$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->leadid)->get();
			

				if($rty->num_rows()==0 && $rty1->num_rows()>0)
				{
					$show=1;
				}
		
	if($show==1)
	{
	$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
	$a=1;
	$html='';

			

	$country=$this->salescrm->getcountry($row->country);
	$state=$this->salescrm->getstate($row->state);


$eda=0;
$view='NA';
	
	$d=$this->visit_based_attachment($row->id);


	if($row->followup_date<>'0000-00-00')
	{
		$follow=date('d-M-Y',strtotime($row->followup_date));
	}else
	{
		$follow='';
	}

		$vdetail=$this->salescrm->get_visit_details($row->id);
		$v=explode('~',$vdetail);

		$transfer_data=$this->salescrm->transfer_history($row->id,$row->user_id);
			$lead_data[] = array(
								'sr_no'=>$i,
								'company' => $row->company_name,
								'customer_name' => $row->customer_name,
								'customer_type' => $clienttype,
								'location' => $clienttype,
								'email' =>'',
								'mobile' =>$row->contact_person."<br/>".$row->contact_no."<br/>".$row->email_id,
								'alternatedetail'=>$row->alt_contact."<br/>".$row->alt_contact_no,
								'address' =>$row->postal_address."&nbsp;".$row->city,
								'leadmanager'=>$row->first_name." ".$row->last_name."<br/>".date('d-m-y',strtotime($row->create_date))."<br/><br/>".$transfer_data,
								'lastupdatedon'=>$d,
								'next_visit'=>$follow,
								'total_visit'=>"<span style='color:red;font-weight: bold;font-size: 30px;text-align: center;'>".$v[1]."</span>",
								'visit_details'=>$v[0],
								'transfer'=>"<a href='".page_url."Leads/transfer_visit/".$row->id."' class='btn btn-warning btn-xs' target='_blank'>Transfer Visit</a><br/><br/> <a href='javascript:;' onclick='close_visit(".$row->id.")' class='btn btn-warning btn-xs btn-danger'>Close this lead Visit</a> "
								
								
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


	function filter_incomplete_visit()
	{
		
		$c=$this->input->post('user');
		$d=$this->uri->segment(3);
		redirect(page_url.'Leads/incomplete_visits/NA/NA/'.$c.'/'.$d);
	}

	function transfer_visit()
	{
		$this->load->view('leads/transfer_visit');
	}

	function update_visit_transfer()
	{
		$visit_id=$this->uri->segment(3);
		$to=$this->input->post('to');
		$from=$this->input->post('from');
		$ar=array('added_by'=>$to);
		$this->db->where('id',$visit_id);
		$this->db->update('daily_visits',$ar);
		if($this->db->affected_rows()>0)
		{
			$arr=array('visit_id'=>$visit_id,'from_user'=>$from,'to_user'=>$to,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('visit_transfer',$arr);

		}
		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully updated.</div>');
			redirect(page_url.'Leads/transfer_visit/'.$this->uri->segment(3));

	}

	function close_visit_by_admin()
	{
		$flag1=$this->uri->segment(3);
		$flag2=$this->uri->segment(4);
		$flag3=$this->uri->segment(5);
		$id=$this->uri->segment(6);
		$sr=array('converted'=>1,'closed'=>1,'closed_on'=>date('Y-m-d H:i:s'),'closed_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$id);
		$this->db->update('daily_visits',$sr);

		$this->session->set_flashdata('message','<div class="alert alert-info">Visit Closed</div>');
			redirect(page_url.'Leads/incomplete_visits/'.$flag1."/".$flag2."/".$flag3);



	}

	function quotation_discount_approval()
	{
		$this->load->view('leads/quotation_discount_approval');
	}


	function order_discount_approval()
	{
		$this->load->view('leads/order_discount_approval');
	}

	function order_on_hold()
	{
		$this->load->view('leads/order_hold_approval');
	}

	function addnewlead()
	{
		$this->load->view('leads/addnewlead');
	}

	function add_open_leads() {
		
		$user_id = $this->input->post('assigntouser');
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
				$state=$this->input->post('state');
				$city=$this->input->post('city');
				$clientlocation=0;

			$audiodata=$this->input->post('audiofile');
			
			if($this->input->post('patient_type')==1)
			{
				$distributor=$this->input->post('distributor');
			}else
			{
				$distributor=0;
			}
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
			'distributor'=>$distributor,
			'other_business' => $this->input->post('other_business'),
			'customer_name'=>$cust_name,
			'designation'=>$this->input->post('designation'),
			'email'=>$email,
			'state_name'=>$state,
			'contact_no'=>$mobile,
			'alt_contact'=>$this->input->post('alt_contact'),
			'alt_contact_no'=>$this->input->post('alt_contact_no'),
			'country_code'=>91,
			'city'=>$city,
			'country'=>101,
			'remarks'=>$this->input->post('spacification'),
			'message'=>$message1,
			'unique_id'=>$uniqueid,
			'unique_no' => $unique_no,
			'added_on'=>$date,
			'client_location'=>$clientlocation,
			'hpcl_company'=>$this->input->post('company_location'),
			'added_by'=>$user_id,
			'audio'=>$audiodata,
			'visit_id'=>$visit
				);

			//echo "<pre>"; print_r($data); exit;
			
			$this->db->insert('leads',$data);
			$last_lead_id = $this->db->insert_id();

			

			$competitor_product = $this->input->post('competitor_product');
			$qty = $this->input->post('qty');
			$pack_size = $this->input->post('pack_size');
			$products = $this->input->post('products');
			if($products!=''){
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
							'lead_stage' => $initalstep,
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
			redirect(page_url.'Leads/addnewlead/'.base64_encode($user_id));
	}

	public function saleszonewiseuser()
	{	
		$option='';
		$zone = $this->input->post('zone');
		$qe=$this->db->select('a.userid,b.first_name,b.last_name, b.user_id')->from('saleszoneusers a')->join('system_users b','a.userid=b.user_id')->where('a.zoneid',$zone)->get();
		if($qe->num_rows()>0)
		{
			if($qe->num_rows()>1)
			{
				$option.='<option value="">Select</option>';
				$a='';
			}else
			{
				$a="selected";
			}
		foreach($qe->result() as $rows){
			$username = $rows->first_name." ".$rows->last_name;
			$option.='<option value="'.$rows->user_id.'" '.$a.'>'.$username.'</option>';
		}
		}

		echo $option; exit;
		
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

	function updatevisitinfo()
	{
		$this->load->view('leads/updatevisitinfo');
	}

	function updatevisitinfordetail()
	{
		$title = "Customer Visit Update";
		$customerremarks = $this->input->post('problems');
		$nextstage = 21;

		$user_id=$_SESSION['logged_in']['user_id'];
		$leadid = $this->uri->segment(3);
		$currentDate = new DateTime();
		$currentDate->modify('+2 days');
		$nextfollowup =  $currentDate->format('Y-m-d');

		$stageid = $this->uri->segment(4);
		
		$data = array('lead_id'=>$leadid,
			'lead_stage'=>$nextstage,
			'next_follow_date'=>$nextfollowup,
			'remarks'=>$customerremarks,
			'remark_title'=>$title,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id,
			'update_on'=>date('Y-m-d H:i:s'),
			'updated_by'=>$user_id);
		$this->db->insert('progress_remarks',$data);

		$products = $this->input->post('products');
		$pack_size = $this->input->post('pack_size');
		$qty = $this->input->post('qty');
			for($k=0; $k < count($products); $k++) {
				
			 	if($products[$k] != '') {
					$data_prod = array(
									  'lead_id' =>$leadid,
									  'product_id' => $products[$k],
									  'qty'=>$qty[$k],
									  'packsize'=>$pack_size[$k]);

					$this->db->insert('lead_products',$data_prod);
					
					}

				}
			

		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/lead_stages/'.$nextstage);

	}

public function customizeproductapproval(){
	$this->load->view('leads/cutomized_requirement_approval');
}

public function customize_requirement_approval_rejection(){
	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('approval', 'approval', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('leads/cutomized_requirement_approval');
		} else {
			date_default_timezone_set("Asia/Kolkata");
            $added_time = date('Y-m-d H:i:s');
            $approval = $this->input->post('approval');

            if($approval==1){

           $query = $this->db->select('instruments_name')->from('presto_instruments')->where('instruments_name',$this->input->post('product_name'))->get();
		   $res = $query->result();
		   if($res){
			   $this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,This record already exist.</div><br/>');
				redirect(page_url.'Leads/customizeproductapproval/'.$this->uri->segment(3)."/".$this->uri->segment(4));
			   
		   }else{
			$company_id = array();	
			$company_id[] = 1;
			
			if($this->input->post('trial') == 1) {
				$trial_reading = 1;
			} else {
				$trial_reading = 0;
			}


			if($this->input->post('pack_size')=="BULK")
			{

				$density=$this->input->post('prd_density');
			}else
			{
				$density=0;
			}

             $data = array(
                    'instruments_name' => $this->input->post('product_name'),
                    'model_number' => $this->input->post('product_code'),
                    'hsncode' => $this->input->post('hsn'),
                    'unit' => $this->input->post('unit'),
                    'mvalue' => $this->input->post('price'),
                    'discount_price' => $this->input->post('disprice'),
		            'status' =>1,
                    'pack_size'=>$this->input->post('pack_size'),
                    'added_on' => $added_time,
                    'added_by' => $user_id
                	);
			
			$this->db->insert('presto_instruments',$data);
			$last_id = $this->db->insert_id();

			for($i = 0; $i < count($company_id); $i++) {
				if($company_id[$i] != '') {
					$datas = array(
								   'product_id' => $last_id,
								   'company_id' =>1
								  );

					$this->db->insert('company_products', $datas);
				}
			}
			$productname = $this->input->post('product_name');
			$approvedstage = 24;
			$data = array('lead_id'=>$this->uri->segment(3),
            		'lead_stage'=>$approvedstage,
            		'next_follow_date'=>date('Y-m-d'),
            		'remarks'=>'Request Approved! With the name of '.$productname,
            		'remark_title'=>'Request Approved!',
            		'added_on'=>date('Y-m-d H:i:s'),
            		'added_by'=>$user_id,
            		'update_on'=>date('Y-m-d H:i:s'),
            		'updated_by'=>$user_id);
            	$this->db->insert('progress_remarks',$data);



			$this->session->set_flashdata('message','<div class="alert alert-success">Thank You!, Record Successfully updated.</div><br/>');
			redirect(page_url.'Leads/lead_stages/24');
		   }

            }else{
            	$rejectstage = 23;
            	$data = array('lead_id'=>$this->uri->segment(3),
            		'lead_stage'=>$rejectstage,
            		'next_follow_date'=>date('Y-m-d'),
            		'remarks'=>$this->input->post('rejectionremark'),
            		'remark_title'=>'CUSTOMIZATION REJECTED BY MANAGEMENT',
            		'added_on'=>date('Y-m-d H:i:s'),
            		'added_by'=>$user_id,
            		'update_on'=>date('Y-m-d H:i:s'),
            		'updated_by'=>$user_id);
            	$this->db->insert('progress_remarks',$data);
            	$this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
			redirect(page_url.'Leads/lead_stages/23');
            }
			
		}
}

public function updatedemofeedback(){
	$this->load->view('leads/update_demo_feedback');
}

public function demofeedbackdata(){


	$typeoforganization = $this->input->post('typeoforganization');
	$address = $this->input->post('address');
	$area = $this->input->post('area');
	$state = $this->input->post('state');
	$companydemopersonname = $this->input->post('companydemopersonname');
	$demodate = date('Y-m-d',strtotime($this->input->post('demodate')));
	$user_id=$_SESSION['logged_in']['user_id'];	
	$demosignedcopy=$_FILES['demosignedcopy']['name'];
		if($demosignedcopy<>'')
		{
			$image1=explode('.',$demosignedcopy);
			$cat_image=end($image1);
			$demosignedcopy1=time().'.'.$cat_image;
			move_uploaded_file($_FILES["demosignedcopy"]["tmp_name"],UPLOADPATH.'demofiles/demoreport/' . $demosignedcopy1);
		}else
		{
			$demosignedcopy1="";
			}


	$data = array('contact_person'=>$companydemopersonname,
	'postal_address'=>$address,
	'type_of_organization'=>$typeoforganization,
	'state_name'=>$state,
	'area_name'=>$area,
	'demofile'=>$demosignedcopy1,
	'demo_date'=>$demodate);

	$this->db->where('id',$this->uri->segment(3));
	$this->db->update('leads',$data);

	$data2 = array('lead_id'=>$this->uri->segment(3),
	'demo_date'=>$demodate,
	'demofile'=>$demosignedcopy1,
	'added_on'=>date('Y-m-d H:i:s'),
	'added_by'=>$user_id);

	$this->db->insert('lead_demo_basic_detail',$data2);


		$products = $this->input->post('products');
		$demoarea = $this->input->post('demoarea');
		$place = $this->input->post('place');
		$productdilution = $this->input->post('productdilution');
		$customizationrequired = $this->input->post('customizationrequired');
		$remarks = $this->input->post('remarks');
		if($products){
			for($k=0; $k < count($products); $k++) {
				
			 	if($products[$k] != '') {

			 		$q = $this->db->select('mvalue')->from('presto_instruments')->where('id',$products[$k])->get();
			 		if($q->num_rows()>0){
			 			$qq=$this->db->select('id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->where('product_id',$products[$k])->get();
			 			if($qq->num_rows()==0)
			 			{
			 			foreach($q->result() as $existingproductprice);
			 			$leadproductdata = array('lead_id'=>$this->uri->segment(3),
			 		'product_id'=>$products[$k],
			 		'qty'=>1,
			 		'price'=>$existingproductprice->mvalue,
			 		'net_price'=>$existingproductprice->mvalue,
			 		'flag'=>0,
			 		'remarks'=>$remarks[$k],
			 		'added_on'=>date('Y-m-d H:i:s'),
			 		'added_by'=>$user_id);
			 		$this->db->insert('lead_products',$leadproductdata);
			 		}else
			 		{
			 			$leadproductdata=array('remarks'=>$remarks[$k]);
			 			$this->db->where('lead_id',$this->uri->segment(3));
			 			$this->db->where('product_id',$products[$k]);
			 			$this->db->update('lead_products',$leadproductdata);
			 		}
			 		}

			 	

				$data_prod = array(
				'lead_id' =>$this->uri->segment(3),
				'product_id' => $products[$k],
				'demo_area'=>$demoarea[$k],
				'place'=>$place[$k],
				'product_dilution'=>$productdilution[$k],
				'remarks'=>$remarks[$k],
				'customization_required'=>$customizationrequired,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);

				$this->db->insert('lead_demo_feedback',$data_prod);
				$lastinserid = $this->db->insert_id();


				$pic = $_FILES['photo']['name'];
				$pic1 = $_FILES['postphoto']['name'];
				for($i=0; $i<count($pic); $i++) {
					$random1 = rand(10,100);
				$picture = $pic[$i];
				if($picture <> '') {
				$files = explode('.', $picture);
				$ext = end($files);
				$newname = time().$i.$random1.'.'.$ext;
				move_uploaded_file($_FILES['photo']["tmp_name"][$i], UPLOADPATH.'demofiles/'.$newname);
				
				}else{
					$newname = "";
				}

				$random2 = rand(1000,10000);
				$picture1 = $pic1[$i];
				if($picture1 <> '') {
				$files1 = explode('.', $picture1);
				$ext1 = end($files1);
				$newname1 = time().$i.$random2.'.'.$ext1;
				move_uploaded_file($_FILES['postphoto']["tmp_name"][$i], UPLOADPATH.'demofiles/'.$newname1);
				}else{
					$newname1 = "";
				}


				$data_p = array(
				'lead_id' =>$this->uri->segment(3),
				'demo_id' => $lastinserid,
				'product_id'=>$products[$k],
				'added_on' => date('Y-m-d H:i:a'),
				'added_by' => $user_id,
				'picture' => $newname,
				'post_picture' => $newname1,
				);

				$this->db->insert('demo_feedback_photos', $data_p);
				


				}

				$video = $_FILES['videos']['name'];
				for($j=0; $j<count($video); $j++) {
				$random3 = rand(10000,100000);
				$picture1 = $video[$j];
				if($picture1 <> '') {
				$files1 = explode('.', $picture1);
				$ext1 = end($files1);
				$newname2 = time().$j.$random3.'.'.$ext1;
				move_uploaded_file($_FILES['videos']["tmp_name"][$j], UPLOADPATH.'demofiles/videos/'.$newname2);

				
				}else{
				$newname2  = "";	
				}
				$datavideo = array(
				'lead_id' =>$this->uri->segment(3),
				'demo_id' => $lastinserid,
				'product_id'=>$products[$k],
				'added_on' => date('Y-m-d H:i:a'),
				'added_by' => $user_id,
				'video' => $newname2);

				$this->db->insert('demo_feedback_videos', $datavideo);
				}


					
					}

				}
			}
		


		$person_attendent_demo=$this->input->post('person_attendent_demo');
		$person_attendent_designation=$this->input->post('person_attendent_designation');
		//$person_attendent_number=$this->input->post('person_attendent_number');
		if(count($person_attendent_demo)>0)
		{
			for($t=0;$t<count($person_attendent_demo);$t++)
			{
				$name=$person_attendent_demo[$t];
				$designtaion=$person_attendent_designation[$t];
				//$number=$person_attendent_number[$t];


				$datavideo = array(
				'lead_id' =>$this->uri->segment(3),
				'demo_id' => $lastinserid,
				'person_name' => $name,
				//'contact_no' => $number,
				'designation' => $designtaion,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$_SESSION['logged_in']['user_id']);

				$this->db->insert('person_attendent_demo',$datavideo);
			}
		}

		$customer_team_member_name=$this->input->post('customer_team_member_name');
		$customer_team_member_designation=$this->input->post('customer_team_member_designation');
		$customer_team_member_mobile_no=$this->input->post('customer_team_member_mobile_no');
		if(count($customer_team_member_name)>0)
		{
			for($t=0;$t<count($customer_team_member_name);$t++)
			{
				$name=$customer_team_member_name[$t];
				$designtaion=$customer_team_member_designation[$t];
				$number=$customer_team_member_mobile_no[$t];


				$datavideo = array(
				'lead_id' =>$this->uri->segment(3),
				'demo_id' => $lastinserid,
				'person_name' => $name,
				'contact_no' => $number,
				'designation' => $designtaion,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$_SESSION['logged_in']['user_id']);

				$this->db->insert('customer_person_attendent_demo',$datavideo);
			}
		}


	if($customizationrequired==1){
		$leadstage = 22;
$remarktitle = "CUSTOMIZATION IN OUR PRODUCT";
$Date = date('Y-m-d');
$nextfollowupdate =  date('Y-m-d', strtotime($Date. ' + 5 days'));
		$data = array('lead_id'=>$this->uri->segment(3),
			'lead_stage'=>$leadstage,
			'next_follow_date'=>$nextfollowupdate,
			'remarks'=>$customizationrequired,
			'remark_title'=>$remarktitle,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
		$this->db->insert('progress_remarks',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, Your record successfully added.</div><br/>');
			redirect(page_url.'Leads/lead_stages/22');

	}else{

$leadstage = 28;
$remarktitle = "Demo Feedback Updated.";
$rmk = "Demo Feedback has been updated.";
$Date = date('Y-m-d');
$nextfollowupdate =  date('Y-m-d', strtotime($Date. ' + 1 days'));
		$data = array('lead_id'=>$this->uri->segment(3),
			'lead_stage'=>$leadstage,
			'next_follow_date'=>$nextfollowupdate,
			'remarks'=>$rmk,
			'remark_title'=>$remarktitle,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
		$this->db->insert('progress_remarks',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, Your record successfully added.</div><br/>');
			redirect(page_url.'Leads/lead_stages/28');
	}

}

public function markquotationsendbydistributor(){

	$this->load->view('leads/quotation_updates');
}

public function update_about_quotation_sent_by_distributor(){

	$user_id=$_SESSION['logged_in']['user_id'];	
	$leadid = $this->uri->segment(3);
	$last_leadstage = $this->uri->segment(4);
	$leadstage = 26;
	$remarkstitle = "QUOTATION SENT BY DISTRIBUTOR";
	$remarks = $this->input->post('update_about_quotation');
	$Date = date('Y-m-d');
	$nextfollowupdate =  date('Y-m-d', strtotime($Date. ' + 3 days'));
	$data = array('lead_id'=>$this->uri->segment(3),
	'lead_stage'=>$leadstage,
	'next_follow_date'=>$nextfollowupdate,
	'remarks'=>$remarks,
	'remark_title'=>$remarkstitle,
	'added_on'=>date('Y-m-d H:i:s'),
	'added_by'=>$user_id);
	$this->db->insert('progress_remarks',$data);

	


	$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/lead_stages/'.$leadstage);
}


function createquotation(){
	$this->load->view('leads/createquotation');
}


function addquotationdetailtogeneratequotation(){

 	$this->form_validation->set_rules('selectdistributor', 'distributor', 'required|trim');
 	$this->form_validation->set_rules('termsconditions', 'Terms & Conditions', 'required|trim');
	$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
	$user_id = $_SESSION['logged_in']['user_id'];
	if ($this->form_validation->run() == FALSE)
	{
	$this->load->view('leads/createquotation');
	}else
	{



		$distributor = $this->input->post('selectdistributor');
		$termsconditions = $this->input->post('termsconditions');
		$leadid = $this->uri->segment(3);
		$previousstage = $this->uri->segment(4);

		/*Add New Product in Quotation*/
		$editproduct = $this->input->post('editproduct');
		$editfinalprice = $this->input->post('editfinalprice');
		$editunit = $this->input->post('editunit');
		$editqty = $this->input->post('editqty');
		$editofferedprice = $this->input->post('editofferedprice');
		$editdiscount = $this->input->post('editdiscount');
		$editnetprice = $this->input->post('editnetprice');
		$upd_id=$this->input->post('editid');
		$quotation_status_id = $this->salescrm->checkQuotationSent();

		// if(in_array($this->input->post('leadquality'), $quotation_status_id)) {
		if(isset($editproduct)){
		for($j=0 ;$j<count($upd_id);$j++) {
			$qq = $this->db->select('discount_price')->from('presto_instruments')->where('id',$editproduct[$j])->get();
			foreach($qq->result() as $checkpr);


			if($checkpr->discount_price>$editofferedprice[$j]){
				$flag = 0;
			}else{
				$flag = 1;
			}

			
			//$cp=$this->salescrm->getcurrentcp($editproduct[$j]);
       		$data = array(
			'product_id' =>$editproduct[$j],
			'qty' => $editqty[$j],
			'packsize' => $editunit[$j],
			'price' => $editofferedprice[$j],
			'percent_amt' =>$editdiscount[$j],
			'net_price' => $editfinalprice[$j],
			'flag' => $flag,
			'added_on' => date('Y-m-d H:i:s'),
			'added_by' => $user_id,
			'msp'=>0,
			'cp'=>0);
       		//echo "<pre>"; print_r($data); exit;
       		$this->db->where('id',$upd_id[$j]);
            $this->db->update('lead_products',$data);
		}
	}
		
		if($upd_id <> '') {


			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            $unit = $this->input->post('unit');
            $listprice = $this->input->post('listprice');
	        $discount = $this->input->post('discount');
	        $offered_price = $this->input->post('offered_price');
	        $finalprice = $this->input->post('finalprice');

       		for($i=0 ;$i<count($productname);$i++){
       			if($productname[$i] !='') {  
	       		
	       	$qq = $this->db->select('discount_price')->from('presto_instruments')->where('id',$productname[$i])->get();
			foreach($qq->result() as $checkpr);
			//echo $checkpr->discount_price."<br/>".$offered_price[$i]; exit;
			if($checkpr->discount_price>$offered_price[$i]){
				$flag = 0;
			}else{
				$flag = 1;
			}
		

	       		//$cp=$this->salescrm->getcurrentcp($productname[$i]);
	 			$data2 = array(
					'lead_id' =>$leadid,
					'product_id' => $productname[$i],
					'qty' => $qty[$i],
					'packsize' => $unit[$i],
					'price' => $offered_price[$i],
					'percent_amt' => $discount[$i],
					'net_price'=> $finalprice[$i],
					'flag' => $flag,
					'msp'=>$offered_price[$i],
					'cp'=>0,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $user_id
				);
	            	$this->db->insert('lead_products',$data2);
	       		}
	    	}
		}

	//}

	    	/*Add New Product in Quotation*/

	    	$termandconditions = $this->input->post('termsconditions')."<br>".$this->input->post('distributorterms');

			$data1 = array(
				'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
				'general_terms'=>$termandconditions);


			$this->db->where('id',$leadid);
			$this->db->update('leads',$data1);


			/*Modify Existing Product in Quotation*/
			$addate = date('Y-m-d');
			$followup_date = date('Y-m-d', strtotime($addate . " + 3 day"));
			$remarks = "Quotation has been created.";
			$termandconditions = $this->input->post('termsconditions')."<br>".$this->input->post('distributorterms');
			$getConversionLeadStage=$this->dashboardmodel->getConversionLeadStage();
			
			$data3 = array(
			'lead_id' => $this->uri->segment(3),
			'lead_stage' => 4,
			'next_follow_date' => $followup_date,
			'remarks' =>$remarks,
			'remark_title' =>$remarks,
			'skip_whatsapp'=>0,
			'added_on'=>date('Y-m-d H:i:s'),
			'distributor_id'=>$this->input->post('selectdistributor'),
			'terms_conditions'=>$termandconditions,
			'added_by'=>$user_id
			);

			$this->db->insert('progress_remarks',$data3);
			
			$leadstage= $this->input->post('leadquality');

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/lead_stages/'.$leadstage);
	}

}


public function modifyquotation(){
	$this->load->view('leads/modify_quotation');
}

function revisedselectedquotation(){

 	$this->form_validation->set_rules('selectdistributor', 'distributor', 'required|trim');
 	$this->form_validation->set_rules('termsconditions', 'Terms & Conditions', 'required|trim');
	$this->form_validation->set_error_delimiters('<div style="color:green;">', '</div>');
	$user_id = $_SESSION['logged_in']['user_id'];
	if ($this->form_validation->run() == FALSE)
	{
	$this->load->view('leads/createquotation');
	}else
	{


		/*Get previous data and store in history table*/


		$q = $this->db->select('*')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){

				$data = array('lead_id'=>$row->lead_id,
					'competitor_product'=>$row->competitor_product,
					'product_id'=>$row->product_id,
					'qty'=>$row->qty,
					'packsize'=>$row->packsize,
					'price'=>$row->price,
					'discount_type'=>$row->discount_type,
					'percent_amt'=>$row->percent_amt,
					'net_price'=>$row->net_price,
					'flag'=>$row->flag,
					'remarks'=>$row->remarks,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'approve_reject_on'=>$row->approve_reject_on,
					'approve_reject_by'=>$row->approve_reject_by,
					'msp'=>$row->msp,
					'cp'=>$row->cp);

				$this->db->insert('lead_products_history',$data);



			}
		}



		/*Get previous data and store in history table*/

		$distributor = $this->input->post('selectdistributor');
		$termsconditions = $this->input->post('termsconditions');
		$leadid = $this->uri->segment(3);
		$previousstage = $this->uri->segment(4);

		/*Add New Product in Quotation*/
		$editproduct = $this->input->post('editproduct');
		$editfinalprice = $this->input->post('editfinalprice');
		$editunit = $this->input->post('editunit');
		$editqty = $this->input->post('editqty');
		$editofferedprice = $this->input->post('editofferedprice');
		$editdiscount = $this->input->post('editdiscount');
		$editnetprice = $this->input->post('editnetprice');
		$upd_id=$this->input->post('editid');



		$quotation_status_id = $this->salescrm->checkQuotationSent();

		// if(in_array($this->input->post('leadquality'), $quotation_status_id)) {
		if(isset($editproduct)){
		for($j=0 ;$j<count($upd_id);$j++) {

			$qq = $this->db->select('discount_price')->from('presto_instruments')->where('id',$editproduct[$j])->get();
			foreach($qq->result() as $checkpr);
			if($checkpr->discount_price>$editofferedprice[$j]){
				$flag = 0;
			}else{
				$flag = 1;
			}
			//$cp=$this->salescrm->getcurrentcp($editproduct[$j]);
       		$data = array(
			'product_id' =>$editproduct[$j],
			'qty' => $editqty[$j],
			'packsize' => $editunit[$j],
			'price' => $editofferedprice[$j],
			'percent_amt' =>$editdiscount[$j],
			'net_price' => $editfinalprice[$j],
			'flag' => $flag,
			'added_on' => date('Y-m-d H:i:s'),
			'added_by' => $user_id,
			'msp'=>0,
			'cp'=>0);
       		//echo "<pre>"; print_r($data); exit;
       		$this->db->where('id',$upd_id[$j]);
            $this->db->update('lead_products',$data);
		}
	}
		
		if($upd_id <> '') {


			$productname = $this->input->post('product');
            $qty = $this->input->post('qty');
            $unit = $this->input->post('unit');
            $listprice = $this->input->post('listprice');
	        $discount = $this->input->post('discount');
	        $offered_price = $this->input->post('offered_price');
	        $finalprice = $this->input->post('finalprice');

       		for($i=0 ;$i<count($productname);$i++){
       			if($productname[$i] !='') {  
	       		$qq = $this->db->select('discount_price')->from('presto_instruments')->where('id',$productname[$i])->get();
			foreach($qq->result() as $checkpr);
			//echo $checkpr->discount_price."<br/>".$offered_price[$i]; exit;
			if($checkpr->discount_price>$offered_price[$i]){
				$flag = 0;
			}else{
				$flag = 1;
			}


	       		//$cp=$this->salescrm->getcurrentcp($productname[$i]);
	 			$data2 = array(
					'lead_id' =>$leadid,
					'product_id' => $productname[$i],
					'qty' => $qty[$i],
					'packsize' => $unit[$i],
					'price' => $offered_price[$i],
					'percent_amt' => $discount[$i],
					'net_price'=> $finalprice[$i],
					'flag' => $flag,
					'msp'=>$offered_price[$i],
					'cp'=>0,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $user_id
				);
	            	$this->db->insert('lead_products',$data2);
	       		}
	    	}
		}

	//}

			// for($k=0 ;$k<count($editproduct);$k++) {
       		// 	if($editproduct[$k] != '') {

       				
       				
		 	// 		$data1 = array(
			// 				'quotation_id' => $quotation_id,
			// 				'product_id' => $productname[$k],
			// 				'qty' => $qty[$k],
			// 				'pack_size' => $packsize[$k],
			// 				'list_price' => $listprice[$k],
			// 				'agreed_price' => $agreed_price[$k],
			// 				'flag' => $flag,
			// 				'msp'=>$discountpricehide_new[$k],
			// 				'cp'=>0,
			// 				'new_batch_code'=>1,
			// 				'added_on' => date('Y-m-d H:i:s'),
			// 				'added_by' => $user_id
			// 		);


		    //         $this->db->insert('customer_quotation_detail',$data1);
	        // 	}
	    	// }
	    	/*Add New Product in Quotation*/

	    	$termandconditions = $this->input->post('termsconditions')."<br>".$this->input->post('distributorterms');

			$data1 = array(
				'validity_date'=>date('Y-m-d', strtotime($this->input->post('validity_date'))),
				'general_terms'=>$termandconditions);


			$this->db->where('id',$leadid);
			$this->db->update('leads',$data1);


			/*Modify Existing Product in Quotation*/
			$addate = date('Y-m-d');
			$followup_date = date('Y-m-d', strtotime($addate . " + 3 day"));
			$remarks = "Quotation has been revised.";
			$termandconditions = $this->input->post('termsconditions')."<br>".$this->input->post('distributorterms');
			$getConversionLeadStage=$this->dashboardmodel->getConversionLeadStage();
			
			$data3 = array(
			'lead_id' => $this->uri->segment(3),
			'lead_stage' => 5,
			'next_follow_date' => $followup_date,
			'remarks' =>$remarks,
			'remark_title' =>$remarks,
			'skip_whatsapp'=>0,
			'added_on'=>date('Y-m-d H:i:s'),
			'distributor_id'=>$this->input->post('selectdistributor'),
			'terms_conditions'=>$termandconditions,
			'added_by'=>$user_id
			);

			$this->db->insert('progress_remarks',$data3);
			
			$leadstage= 5;

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, record successfully added.</div>');
			redirect(page_url.'Leads/lead_stages/'.$leadstage);
	}

}

public function demofeedbackview(){

	$this->load->view('leads/demo_feedback_view');
}

public function demofeedbackphotosview(){

	$this->load->view('leads/download_prepost_images');
}

public function checkcustomerexistance(){
	$mobile= $this->input->post('mobile_no');
	$q = $this->db->select('id, contact_no, customer_name, company_name')->from('leads')->where('contact_no',$mobile)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);

		$q = $this->db->select('b.first_name, b.last_name')->from('lead_assigned_to_team_member a')->join('system_users b','a.member_id=b.user_id')->where('a.lead_id',$row->id)->get();
		foreach($q->result() as $row1);

		echo "Customer ".$row->customer_name." Company Name ".$row->company_name." is already assigned to ".$row1->first_name." ".$row1->last_name; exit;
	}
}

function edit_demo_feedback(){
	$this->load->view('leads/edit_demo_feedback');
}

function opportunity()
{
	$this->load->view('leads/opportunity_form');
} 
function generateOppNo()
{
	$op_type=$this->input->post('op_type');
  	$mach_type=$this->input->post('mach_type');
	$opnumber=$this->salescrm->getOppNo($op_type,$mach_type);
	echo $opnumber;
}

function runtimegenerateOppNo($op_type,$mach_type)
{
	$opnumber=$this->salescrm->runtimegetOppNo($op_type,$mach_type);
	$dd = explode('~',$opnumber);
	//echo "<pre>"; print_r($dd); exit;
	return $dd;
}

function add_opportunity()
{

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('op_date', 'Opportunity Date', 'required|trim');
		$this->form_validation->set_rules('lsource', 'Opportunity Source', 'required|trim');
		$this->form_validation->set_rules('op_type', 'Opportunity Type', 'required|trim');
		$this->form_validation->set_rules('op_no', 'Opportunity Number', 'required|trim');
		$this->form_validation->set_rules('mach_type', 'Machine Type', 'required|trim');
		$this->form_validation->set_rules('marketing', 'Marketing Person', 'required|trim');
		$this->form_validation->set_rules('customer', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('brand', 'Company Brand', 'required|trim');
		$this->form_validation->set_rules('address', 'Company Address', 'required|trim');
		//$this->form_validation->set_rules('gst', 'Company GST', 'required|trim');
		$this->form_validation->set_rules('product', 'Product', 'required|trim');
		$this->form_validation->set_rules('qty', 'Qty', 'required|trim');
		$this->form_validation->set_rules('customertype', 'Customer Type', 'required|trim');
		$this->form_validation->set_rules('probability', 'Probability of conversion', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('leads/opportunity_form');
		}
		else
		{

			$op_number = $this->runtimegenerateOppNo($this->input->post('op_type'), $this->input->post('mach_type'));
			//echo "<pre>"; print_r($op_number); exit;
			$data=array(
				'lead_source_id'=>$this->input->post('lsource'),
				'patient_type_id'=>$this->input->post('op_type'),
				'create_date'=>date('Y-m-d',strtotime($this->input->post('op_date'))),
				'company_name'=>$this->input->post('customer'),
				'machine_type'=>$this->input->post('mach_type'),
				'unique_id'=>$op_number[0],
				'unique_no'=>$op_number[1],
				'postal_address'=>$this->input->post('address'),
				'gst'=>$this->input->post('gst'),
				'country'=>$this->input->post('country'),
				'customer_gstn'=>$this->input->post('gst'),
				'brand'=>$this->input->post('brand'),
				'added_on'=>date('Y-m-d'),
				'vatno'=>$this->input->post('vatno'),
				'customise_remarks'=>$this->input->post('remarks'),
				'merchantexport'=>$this->input->post('merchantexport'),
				'customer_type'=>$this->input->post('customertype'),
				'probability'=>$this->input->post('probability'),
				'added_by'=>$this->input->post('marketing')
				);

			$this->db->insert('leads',$data);
			$lid=$this->db->insert_id();

			/** LEAD PRODUCTS **/

			if (is_numeric($this->input->post('product')) && !strpos($this->input->post('product'), '.')) {
				$productid = $this->input->post('product');
			}else{
				$datass = array('instruments_name'=>$this->input->post('product'),'type'=>0,'status'=>1);
							$this->db->insert('presto_instruments',$datass);
							$productid = $this->db->insert_id();

			}


			$data_prod = array(
			'lead_id' => $lid,
			'product_id' => $productid,
			'qty' => $this->input->post('qty'),
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$this->input->post('marketing')
			);
			$this->db->insert('lead_products',$data_prod);
			/** END **/

			/** PROGRESS REMARKS **/
			$initalstep=$this->getintialstep();
			$datap = array(
							'lead_id' => $lid,
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$this->input->post('marketing'),
							'lead_status' => $initalstep,
							'next_follow_date' => $date = date('Y-m-d', strtotime("+1 day"))
							);

			$this->db->insert('progress_remarks',$datap);

			/** END **/

			$this->session->set_flashdata('message','<div class="alert alert-success" style="font-size:40px;">Thank you, Opportunity successfully Added.</div>');
			redirect(page_url.'Leads/add_opportunity');


		}

}

public function lead_stages_by_selected_user() {
		$this->load->view('leads/lead_stages_by_selected_user');
	}

public function lead_stages_by_selected_user_list() {
			// echo base64_decode($this->uri->segment(4));exit;
			$lead_data = array();
			$lead_stage=$this->uri->segment(3);
			$selecteduser = $this->uri->segment(4);
			$route_source = $this->uri->segment(5);
			$route_exhibition = $this->uri->segment(6);
			$filters = $this->get_stage_filter_values($route_source, $route_exhibition, $selecteduser);
			$sc = $filters['source_id'];
			$exhi = $filters['exhibition_id'];
			$selecteduser = !empty($filters['manager_id']) ? $filters['manager_id'] : $selecteduser;
			$date_filter_sql = '';
			$user_id=$_SESSION['logged_in']['user_id'];
			if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
				{
					$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				if($selecteduser<>''){
					$chk='AND b.added_by='.$selecteduser;
				}else{
					$chk='';
				}
				
			}
				$sck = ''; 
				$exhibi = '';

				if($sc<>'')
				{
					$sck = " AND b.lead_source_id=".$this->db->escape_str($sc);
				}

				if($exhi<>'')
				{
					$exhibi = " AND b.exhibition=".$this->db->escape_str($exhi);
				}

				if(!empty($filters['start_date']))
				{
					$date_filter_sql .= " AND DATE(a.added_on) >= '".$this->db->escape_str($filters['start_date'])."'";
				}

				if(!empty($filters['end_date']))
				{
					$date_filter_sql .= " AND DATE(a.added_on) <= '".$this->db->escape_str($filters['end_date'])."'";
				}


			$resty=$this->db->query("SELECT b.added_by as leadmanager, b.welcome_email_status, b.customer_gstn, b.vatno, b.customise_remarks, c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, a.nonqualifiedreason, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,c.contact_no, c.customer_name, c.alt_contact, c.email as clientEmail, a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$lead_stage') AND b.closed=0 $chk $sck $exhibi $date_filter_sql GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			if($clienttype=='Domestic'){
				$gstno = "GSTN- ".$row->customer_gstn;
			}else{
				$gstno = "VAT No- ".$row->vatno;
			}
			$emailid = '';
			if (!empty($row->email_id)) {
				$emailid = $row->email_id;
			} else if (!empty($row->email)) {
				$emailid = $row->email;
			} else if (!empty($row->clientEmail)) {
				$emailid = $row->clientEmail;
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else if($row->machine_type==2)
			{	
				$type="Powder";
			}else{
				$type="Customise<hr>";
			}


			$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";


			$username=$this->salescrm->getusername($row->leadmanager);
			$reecord_id=$this->salescrm->getRecordID($row->leadid);
			$url=page_url.'Opportunity/GeneratedQuote/'.$reecord_id;
			if($this->salescrm->checkforquotationoraheadsteptogetquotationvisible($row->leadid))
			{
				if($lead_stage==39){
					$quote="<a href='".page_url."Opportunity/previewquoteandsendforapproval/".$reecord_id."/".$row->leadid."'><span class='btn btn-success btn-xs'>Preview Quotation and Send for Approval</span></a>";
				}else{
					$quote="<a href='".$url."' target='_blank' class='btn btn-danger btn-xs'>View Quotation</a>";
				}
			
			}else
			{
				$quote="NA";
			}
			if($this->uri->segment(3)=='34'){
			$q = $this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$row->nonqualifiedreason)->get();
			if($q->num_rows()>0){
			foreach($q->result() as $lostresons);
			$leadlostreason = $lostresons->reason;
			}else{
			$leadlostreason = '';
			}
			}else{
			$leadlostreason = '';
			}
			

			$products=$this->getProductDetails($row->leadid);
			if(count($products)>0)
			{
				$prd_name=$products[0];
				$prd_qty=$products[1];
			}else
			{
				$prd_name='';
				$prd_qty='';
			}

			if($user_id==139){
			if($this->uri->segment(3)==36){
				$approvereject = '<span class="btn btn-success btn-xs" onclick="approvalwindow('.$row->leadid.');">Approve/Reject</span>';
			}else{
				$approvereject = '';
			}}else{
				$approvereject = "Approval is Pending";
			}
			if($row->welcome_email_status==0){
				$sendwelcome= '<a href="'.page_url.'Master/User_management/sendintroemail/1000/'.$row->leadid.'"><span class="btn btn-primary btn-xs">Send Intro Email <i class="fa fa-envelope"></i></span></a>';
			}else{
				$sendwelcome = "";
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'progress'=>$view."<br><br>".$sendwelcome,
								'quote_step'=>$quote,
								'oppno'=>$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)),
								'opptype'=>$this->TextFormatting($clienttype)."<hr> ".$gstno,
								'source'=>$this->TextFormatting($leadsource),
								'company'=>$this->TextFormatting($row->mastercompanyname),
								'customerdetail'=>$this->TextFormatting($row->customer_name)."<br>".$this->TextFormatting($row->contact_no)."<br>".$this->TextFormatting($row->alt_contact),
								'email'=>$this->TextFormatting($emailid),
								'type'=>$this->TextFormatting($type)."<br>".$this->TextFormatting($row->customise_remarks),
								'address'=>$this->TextFormatting($row->postal_address),
								'product'=>"<strong>".$this->TextFormatting($prd_name)."<br/>".$this->TextFormatting($prd_qty)." Nos</strong>",
								'leadlostreason'=>$this->TextFormatting($leadlostreason),
								'remarks'=>$this->TextFormatting($row->remarks),
								'manager'=>$this->TextFormatting($username),
								'approvereject'=>$approvereject,
								'updatedOn'=>date('d-m-Y H:i A',strtotime($row->added_on))												
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

public function lead_stage_list_pms() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();
		$lead_stage=$this->uri->segment(3);
		$route_source = $this->uri->segment(5);
		$route_exhi = $this->uri->segment(6);
		$route_manager = $this->uri->segment(7);
		if($route_manager === 'ALL')
		{
			$route_manager = '';
		}
		$filters = $this->get_stage_filter_values($route_source, $route_exhi, $route_manager);
		$sc = $filters['source_id'];
		$exhi = $filters['exhibition_id'];
		$selecteduser = $filters['manager_id'] !== '' ? $filters['manager_id'] : $route_manager;
		$date_filter_sql = '';
		$user_id=$_SESSION['logged_in']['user_id'];
		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				if($selecteduser<>''){
					$chk='AND b.added_by='.$selecteduser;
				}else{
					$chk='';
				}
				
			}

			if($sc<>''){
				$sck = " AND b.lead_source_id=".$this->db->escape_str($sc);

			}else{
				$sck = '';
			}
			if($exhi<>''){
				$exhibi = " AND b.exhibition=".$this->db->escape_str($exhi);
			}else{
				$exhibi = '';
			}

			if(!empty($filters['start_date']))
			{
				$date_filter_sql .= " AND DATE(a.added_on) >= '".$this->db->escape_str($filters['start_date'])."'";
			}

			if(!empty($filters['end_date']))
			{
				$date_filter_sql .= " AND DATE(a.added_on) <= '".$this->db->escape_str($filters['end_date'])."'";
			}


		$resty=$this->db->query("SELECT b.added_by as leadmanager, b.welcome_email_status, b.customer_gstn, b.vatno, b.customise_remarks, c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, a.nonqualifiedreason, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,c.contact_no, c.customer_name, c.alt_contact, c.email as clientEmail, a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$lead_stage') AND b.closed=0 $chk $sck $exhibi $date_filter_sql GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			if($clienttype=='Domestic'){
				$gstno = "GSTN- ".$row->customer_gstn;
			}else{
				$gstno = "VAT No- ".$row->vatno;
			}
			if($row->email_id<>'')
			{
				$emailid = $row->email_id;
			}else if($row->email<>'')
			{
				$emailid = $row->email;
			}else if($row->clientEmail<>'')
			{
				$emailid = $row->clientEmail;
			}else
			{
				$emailid = '';
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else if($row->machine_type==2)
			{	
				$type="Powder";
			}else{
				$type="Customise<hr>";
			}


			$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";


			$username=$this->salescrm->getusername($row->leadmanager);
			$reecord_id=$this->salescrm->getRecordID($row->leadid);
			$url=page_url.'Opportunity/GeneratedQuote/'.$reecord_id;
			if($this->salescrm->checkforquotationoraheadsteptogetquotationvisible($row->leadid))
			{
				if($lead_stage==39){
					$quote="<a href='".page_url."Opportunity/previewquoteandsendforapproval/".$reecord_id."/".$row->leadid."'><span class='btn btn-success btn-xs'>Preview Quotation and Send for Approval</span></a>";
				}else{
					$quote="<a href='".$url."' target='_blank' class='btn btn-danger btn-xs'>View Quotation</a>";
				}
			
			}else
			{
				$quote="NA";
			}
			if($this->uri->segment(3)=='34'){
			$q = $this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$row->nonqualifiedreason)->get();
			if($q->num_rows()>0){
			foreach($q->result() as $lostresons);
			$leadlostreason = "<b>".$lostresons->reason."</b>";
			}else{
			$leadlostreason = '';
			}
			}else{
			$leadlostreason = '';
			}
			

			$products=$this->getProductDetails($row->leadid);
			if(count($products)>0)
			{
				$prd_name=$products[0];
				$prd_qty=$products[1];
			}else
			{
				$prd_name='';
				$prd_qty='';
			}

			if($user_id==139){
			if($this->uri->segment(3)==36){
				$approvereject = '<span class="btn btn-success btn-xs" onclick="approvalwindow('.$row->leadid.');">Approve/Reject</span>';
			}else{
				$approvereject = '';
			}}else{
				$approvereject = "Approval is Pending";
			}
			if($row->welcome_email_status==0){
				$sendwelcome= '<a href="'.page_url.'Master/User_management/sendintroemail/1000/'.$row->leadid.'"><span class="btn btn-primary btn-xs">Send Intro Email <i class="fa fa-envelope"></i></span></a>';
			}else{
				$sendwelcome = "";
			}


			$q = $this->db->select('reason')->from('quotation_change_reason')->where('lead_id',$row->leadid)->order_by('id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $changereason);

				$resonremarks = $changereason->reason;

			}else{
				$resonremarks = '';
			}
			$quotation_rejection_comment = '';
			if ((int) $lead_stage === 38) {
				$latest_rejection_request = $this->latest_quotation_rejection_comment_request($row->leadid);
				$request_status = '';
				if ($latest_rejection_request) {
					if ($latest_rejection_request->status === 'approved') {
						$request_status = "<br><span class='label label-success'>Comment approved by Shubham Sir</span>";
					} elseif ($latest_rejection_request->status === 'rejected') {
						$request_status = "<br><span class='label label-danger'>Comment rejected by Shubham Sir</span>";
					} else {
						$request_status = "<br><span class='label label-warning'>Comment pending with Shubham Sir</span>";
					}
				}
				$quotation_rejection_comment = "<br><br><button type='button' class='btn btn-primary btn-xs quote-rejection-comment-btn' data-lead-id='".(int) $row->leadid."' data-opp-no='".htmlspecialchars($row->unique_id, ENT_QUOTES, 'UTF-8')."' data-company='".htmlspecialchars($row->mastercompanyname, ENT_QUOTES, 'UTF-8')."'>Send Comment to Shubham Sir</button>".$request_status;
			}

			$leadstage = array(39, 36); // Define separately, not as a single string

			$q = $this->db->select('id')
			->from('progress_remarks')
			->where('lead_id', $row->leadid)
			->where_in('lead_status', $leadstage)
			->get();

			if ($q->num_rows() > 0) {
			// If lead_stage 39 OR 36 exists for this lead, no delete button
			$delete = '';
			} else {
			// Otherwise, allow delete
			$delete = "<a href='javascript:;' onclick='deleteLead(" . $row->leadid . ")'>
			<i class='fa fa-trash' style='color:red; cursor:pointer;' title='Delete Lead'></i>
           </a>";
}


			

							$lead_data[] = array(
								'sr_no'=>$i."<br><br>".$delete,
								'progress'=>$view."<br><br>".$sendwelcome,
								'quote_step'=>$quote,
								'oppno'=>$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)),
								'opptype'=>$this->TextFormatting($clienttype)."<hr> ".$gstno,
								'source'=>$this->TextFormatting($leadsource),
								'company'=>$this->TextFormatting($row->mastercompanyname),
								'customerdetail'=>$this->TextFormatting($row->customer_name)."<br>".$this->TextFormatting($row->contact_no)."<br>".$this->TextFormatting($row->alt_contact),
								'email'=>$this->TextFormatting($emailid),
								'type'=>$this->TextFormatting($type)."<br>".$this->TextFormatting($row->customise_remarks),
								'address'=>$this->TextFormatting($row->postal_address),
								'product'=>"<strong>".$this->TextFormatting($prd_name)."<br/>".$this->TextFormatting($prd_qty)." Nos</strong>",
								'leadlostreason'=>$this->TextFormatting($leadlostreason)."<br><br>".$this->TextFormatting($row->remarks),
								'remarks'=>$this->TextFormatting($row->remarks).$quotation_rejection_comment,
								'manager'=>$this->TextFormatting($username),
								'approvereject'=>$approvereject,
								'resonremarks'=>$resonremarks,
								'updatedOn'=>date('d-m-Y H:i A',strtotime($row->added_on))												
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

	private function ensure_quotation_rejection_comment_table()
	{
		if ($this->db->table_exists('quotation_rejection_comment_request')) {
			return true;
		}

		return $this->db->query("CREATE TABLE IF NOT EXISTS `quotation_rejection_comment_request` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`lead_id` int(11) NOT NULL,
			`comment` text NOT NULL,
			`status` varchar(20) NOT NULL DEFAULT 'pending',
			`decision_token` varchar(64) NOT NULL,
			`requested_by` int(11) NOT NULL,
			`requested_at` datetime NOT NULL,
			`decided_by` int(11) DEFAULT NULL,
			`decided_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `idx_qrcr_lead` (`lead_id`),
			KEY `idx_qrcr_status` (`status`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
	}

	private function latest_quotation_rejection_comment_request($lead_id)
	{
		if (!$this->db->table_exists('quotation_rejection_comment_request')) {
			return null;
		}

		return $this->db
			->select('*')
			->from('quotation_rejection_comment_request')
			->where('lead_id', (int) $lead_id)
			->order_by('id', 'desc')
			->limit(1)
			->get()
			->row();
	}

	private function json_response($payload, $status_code = 200)
	{
		return $this->output
			->set_status_header($status_code)
			->set_content_type('application/json')
			->set_output(json_encode($payload));
	}

	private function send_quotation_rejection_comment_chat($request_id, $lead, $comment)
	{
		$this->load->helper('chat_access');
		$this->load->model('Chat_model', 'chat');

		$me = chat_identity($this);
		if (!$me) {
			return false;
		}

		$conv_id = $this->chat->ensure_direct($me, 139, 'user');
		if (!$conv_id) {
			return false;
		}

		$request = $this->db->select('decision_token')->from('quotation_rejection_comment_request')->where('id', (int) $request_id)->get()->row();
		if (!$request) {
			return false;
		}

		$approve_url = page_url.'Leads/quotation_rejection_comment_decision/'.(int) $request_id.'/approve/'.$request->decision_token;
		$reject_url = page_url.'Leads/quotation_rejection_comment_decision/'.(int) $request_id.'/reject/'.$request->decision_token;
		$lead_url = page_url.'Leads/view_detail/'.(int) $lead->id.'/38';
		$quote_id = $this->salescrm->getRecordID($lead->id);
		$quote_url = $quote_id ? page_url.'Opportunity/GeneratedQuote/'.$quote_id : '';

		$body = "Quotation rejected comment approval required\n";
		$body .= "Opportunity: ".$lead->unique_id."\n";
		$body .= "Company: ".strip_tags((string) $lead->company_name)."\n";
		$body .= "Comment: ".$comment."\n";
		$body .= "Lead: ".$lead_url."\n";
		if ($quote_url !== '') {
			$body .= "Quotation: ".$quote_url."\n";
		}
		$body .= "\nApprove: ".$approve_url."\n";
		$body .= "Reject: ".$reject_url;

		$msg_id = $this->chat->send_message($conv_id, $me, $body, array('type' => 'text'));
		$this->chat->tag_record($conv_id, $msg_id, $me, 'lead', (int) $lead->id);
		$this->chat->notify_conversation($conv_id, $me, $msg_id, 'message', $me['name'], $body);
		$this->chat->push_event($conv_id, 'message', $me, $msg_id);

		return true;
	}

	public function submit_quotation_rejection_comment()
	{
		$lead_id = (int) $this->input->post('lead_id');
		$comment = trim((string) $this->input->post('comment', true));
		$user_id = (int) $_SESSION['logged_in']['user_id'];

		if ($lead_id <= 0 || $comment === '') {
			return $this->json_response(array('ok' => false, 'message' => 'Please enter a comment.'), 400);
		}

		$lead = $this->db
			->select('leads.id, leads.unique_id, COALESCE(customer_detail.company_name, leads.company_name) AS company_name', false)
			->from('leads')
			->join('customer_detail', 'customer_detail.id = leads.company_name', 'left')
			->where('leads.id', $lead_id)
			->get()
			->row();
		if (!$lead) {
			return $this->json_response(array('ok' => false, 'message' => 'Opportunity not found.'), 404);
		}

		$current_stage = $this->db
			->select('lead_status')
			->from('progress_remarks')
			->where('lead_id', $lead_id)
			->order_by('id', 'desc')
			->limit(1)
			->get()
			->row();
		if (!$current_stage || (int) $current_stage->lead_status !== 38) {
			return $this->json_response(array('ok' => false, 'message' => 'This option is only available for Quotation Rejected opportunities.'), 400);
		}

		if (!$this->ensure_quotation_rejection_comment_table()) {
			return $this->json_response(array('ok' => false, 'message' => 'Could not prepare comment request storage.'), 500);
		}

		$token = bin2hex(function_exists('random_bytes') ? random_bytes(16) : openssl_random_pseudo_bytes(16));
		$data = array(
			'lead_id' => $lead_id,
			'comment' => $comment,
			'status' => 'pending',
			'decision_token' => $token,
			'requested_by' => $user_id,
			'requested_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('quotation_rejection_comment_request', $data);
		$request_id = (int) $this->db->insert_id();

		if (!$this->send_quotation_rejection_comment_chat($request_id, $lead, $comment)) {
			return $this->json_response(array('ok' => false, 'message' => 'Comment saved, but chat notification could not be sent.'), 500);
		}

		return $this->json_response(array('ok' => true, 'message' => 'Comment sent to Shubham Sir for approval.'));
	}

	public function quotation_rejection_comment_decision($request_id = 0, $decision = '', $token = '')
	{
		$user_id = (int) $_SESSION['logged_in']['user_id'];
		if ($user_id !== 139) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Only Shubham Sir can approve or reject this comment.</div>');
			redirect(page_url.'Leads/lead_stages/38');
			return;
		}

		$decision = strtolower(trim((string) $decision));
		if (!in_array($decision, array('approve', 'reject'), true) || !$this->db->table_exists('quotation_rejection_comment_request')) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid comment approval link.</div>');
			redirect(page_url.'Leads/lead_stages/38');
			return;
		}

		$request = $this->db
			->select('*')
			->from('quotation_rejection_comment_request')
			->where('id', (int) $request_id)
			->where('decision_token', $token)
			->get()
			->row();
		if (!$request) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Comment request not found.</div>');
			redirect(page_url.'Leads/lead_stages/38');
			return;
		}

		if ($request->status === 'pending') {
			$this->db->where('id', (int) $request_id)->update('quotation_rejection_comment_request', array(
				'status' => $decision === 'approve' ? 'approved' : 'rejected',
				'decided_by' => $user_id,
				'decided_at' => date('Y-m-d H:i:s')
			));
		}

		$this->session->set_flashdata('message', '<div class="alert alert-success">Comment request has been '.($decision === 'approve' ? 'approved' : 'rejected').'.</div>');
		redirect(page_url.'Leads/lead_stages/38');
	}


	public function update_remarksNew(){
		
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$previousstatus=$this->input->post('previous_lead_stage');
		
		$quotefile='';
			$pifile='';	
		
		
		$check_status = array();
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "progress_remarks";
		$followupdate = $this->input->post('followup_date');

		if($followupdate == '') {
			$followup_date = '';
		} else {
			$followup_date = date('Y-m-d',strtotime($this->input->post('followup_date')));
		}
		
             $lead_stage = $this->input->post('leadquality');

			$skip_whatsapp = $this->input->post('skip_whatsapp');

			if ($skip_whatsapp == '') {
				$skip = 0;
			} else {

				$skip = 1;
			}

			$data = array(
						'lead_id'=>$this->uri->segment(3),
						'lead_status' => $lead_stage,
						'next_follow_date'=>$followup_date,
						'remarks'=>$this->input->post('remarks'),
						'remark_title'=>$this->input->post('remark_title'),
						'nonqualifiedreason'=>$this->input->post('nonqualifiedreason'),
						'skip_whatsapp'=>$skip,
						'added_on'=>$date,
						'added_by'=>$user_id
						);
			 // echo "<pre>";print_r($data);exit;
			$last_id = $this->master->insert_record($table,$data);

		$pic = $_FILES['upload_file']['name'];

		for($i=0; $i<count($pic); $i++) {
			$picture = $pic[$i];
			  if($picture <> '') {
				$files = explode('.', $picture);
				$ext = end($files);
				$newname = time().$i.'.'.$ext;
				// echo lead_uploads.$newname;exit;
				move_uploaded_file($_FILES['upload_file']["tmp_name"][$i], lead_uploads.$newname);
				$data_p = array(
							 'lead_id' => $this->uri->segment(3),
							 'remarks_id' => $last_id,
							 'lead_status' => $lead_stage,
							 'upload_file' => $newname
							);

				$this->db->insert('lead_remarks_files', $data_p);
			 }
				
			}

			$checkStatusForEntry = $this->salescrm->getLeadStageDetails($lead_stage);
			$product_id = $this->input->post('product_id');
			$lead_product_id=$this->input->post('lead_product_id');

				if ($checkStatusForEntry != '') {
					foreach ($checkStatusForEntry as $row2);

						if($row2->quotation_step == 1) {
							for($j=0; $j<count($lead_product_id); $j++) {

								  if($lead_product_id[$j] != '') {
								
			                    $qty = $this->input->post('qty'.$lead_product_id[$j]);
			                    $price = $this->input->post('price'.$lead_product_id[$j]);
			                    $discount_type = $this->input->post('discount_type'.$lead_product_id[$j]);
			                    $percent_amt = $this->input->post('percent_amt'.$lead_product_id[$j]);
							
									$datas = array(
												 'qty' => $qty,
												 'price' => $price,
												 'discount_type' => $discount_type,
												 'percent_amt' => $percent_amt
												);

									$this->db->where('id', $lead_product_id[$j])
											 ->update('lead_products', $datas);
								 }
						
							}

							 if($this->input->post('packing_type') == 3) {
		                        $packprice=0.00;
		                    } else {
		                        $packprice=$this->input->post('packing_charges');
		                    }
		                    
		                     if($this->input->post('freight_actual') == 1) {
		                        $fprice=0.00;
		                    } else {
		                        $fprice=$this->input->post('freight_charges');
		                    }		            
		            
							$datas = array(
										 'lead_id' => $this->uri->segment(3),
										 'packing_type' => $this->input->post('packing_type'),
										 'packing_price'=>$packprice,
										 'freight_actual' => $this->input->post('freight_actual'),
										 'freight_charges' => $fprice
										 );

							$this->db->insert('lead_product_details', $datas);
							//$this->send_communication($lead_stage, $this->uri->segment(3));
							redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3).'&leadstatus='.$lead_stage.'&flag=1');
						} else if($row2->quotation_revised_step == 1) {
							$product_id = $this->input->post('product_idedit');
							$lead_product_id=$this->input->post('lead_product_idedit');
					    

							for($j=0; $j<count($lead_product_id); $j++) {

								  if($lead_product_id[$j] != '') {
								
			                    $qty = $this->input->post('qtyedit'.$lead_product_id[$j]);
			                    $price = $this->input->post('priceedit'.$lead_product_id[$j]);
			                    $discount_type = $this->input->post('discount_typeedit'.$lead_product_id[$j]);
			                    $percent_amt = $this->input->post('percent_amtedit'.$lead_product_id[$j]);
							
									$datas = array(
												 'qty' => $qty,
												 'price' => $price,
												 'discount_type' => $discount_type,
												 'percent_amt' => $percent_amt
												);

									$this->db->where('id', $lead_product_id[$j])
											 ->update('lead_products', $datas);
								 }
									
								}


			                    if($this->input->post('packing_typeedit')==3)
			                    {
			                        $packprice=0.00;
			                    }else
			                    {
			                        $packprice=$this->input->post('packing_chargesedit');
			                    }
			            
			                    
			                     if($this->input->post('freight_actualedit')==1)
			                    {
			                        $fprice=0.00;
			                    }else
			                    {
			                        $fprice=$this->input->post('freight_chargesedit');
			                    }
			            
			            
			            
								$datas = array(
										
											 'packing_type' => $this->input->post('packing_typeedit'),
											 'packing_price'=>$packprice,
											 'freight_actual' => $this->input->post('freight_actualedit'),
											 'freight_charges' => $fprice
											 );

								
			                    $this->db->where('lead_id',$this->uri->segment(3));
								$this->db->update('lead_product_details', $datas);

								//$this->send_communication($lead_stage, $this->uri->segment(3));
								redirect(page_url1.'pdf/rfq/examples/'.$quotefile.'.php?lead_id='.$this->uri->segment(3).'&leadstatus='.$lead_stage.'&flag=1');
						} else if($row2->pi_step == 1 || $row2->pi_revised_step == 1) {
							 $product_id = $this->input->post('product_idedit');
							 $lead_product_id=$this->input->post('lead_product_idedit');
							

							for($j=0; $j<count($lead_product_id); $j++) {

								  if($lead_product_id[$j] != '') {
								
			                    $qty = $this->input->post('qtyedit'.$lead_product_id[$j]);
			                    $price = $this->input->post('priceedit'.$lead_product_id[$j]);
			                    $discount_type = $this->input->post('discount_typeedit'.$lead_product_id[$j]);
			                    $percent_amt = $this->input->post('percent_amtedit'.$lead_product_id[$j]);
							
									$datas = array(
												 'qty' => $qty,
												 'price' => $price,
												 'discount_type' => $discount_type,
												 'percent_amt' => $percent_amt
												);

									$this->db->where('id', $lead_product_id[$j])
											 ->update('lead_products', $datas);
								 }
									
								}


			                    if($this->input->post('packing_typeedit')==3)
			                    {
			                        $packprice=0.00;
			                    }else
			                    {
			                        $packprice=$this->input->post('packing_chargesedit');
			                    }
			            
			                    
			                     if($this->input->post('freight_actualedit')==1)
			                    {
			                        $fprice=0.00;
			                    }else
			                    {
			                        $fprice=$this->input->post('freight_chargesedit');
			                    }
			            
			            
			            
								$datas = array(
										
											 'packing_type' => $this->input->post('packing_typeedit'),
											 'packing_price'=>$packprice,
											 'freight_actual' => $this->input->post('freight_actualedit'),
											 'freight_charges' => $fprice
											 );

			                    $this->db->where('lead_id',$this->uri->segment(3));
								$this->db->update('lead_product_details', $datas);
								
								/** UPDATE GST AND TERMS & CONDITIONS **/
								
								$extradata=array('gst'=>$this->input->post('customer_gst'),
								'pi_terms'=>$this->input->post('termscondition'));
								
								$this->db->where('id',$this->uri->segment(3));
								$this->db->update('leads',$extradata);
								//$this->send_communication($lead_stage, $this->uri->segment(3));
								redirect(page_url1.'pdf/rfq/examples/'.$pifile.'.php?lead_id='.$this->uri->segment(3).'&leadstatus='.$lead_stage.'&flag=1');
			// 		/** END **/
						} else {

						 // $this->send_communication($lead_stage, $this->uri->segment(3));

						if($previousstatus<>'' && $_SESSION['logged_in']['role']<>1)
						{
							$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Thank you, record successfully updated.</div>');

							redirect(page_url.'Leads//lead_stages/'.$previousstatus);

						}else
						{
							$this->session->set_flashdata('message','<div class="alert alert-danger" style="color:#000;">Thank you, record successfully updated.</div>');

							redirect(page_url.'Leads/view_detail/'.$this->uri->segment(3));
						}
			}
				}

			
		
	
	}


function followups()
{
	$this->load->view('leads/followup_page');
}


public function lead_stage_followup_list_pms() {
// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();
		$type=$this->uri->segment(3);
		$user=$this->uri->segment(4);

		if($user=='' || $user=="ALL")
		{
		$user_id=$_SESSION['logged_in']['user_id'];
		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}
		}else
		{
			$user_id=$user;
			$chk=" AND b.added_by=".$user_id;

		}

			$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
			$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
			array_push($getDeadEndLeadStage, $conversion_lead_stage);
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

			// if($type!=1)
			// {

			if($type==1)
			{
				$dr="AND a.next_follow_date='".date('Y-m-d')."'";
			}else if($type==2)
			{
				$dr="AND a.next_follow_date<'".date('Y-m-d')."'";
			}else
			{
				$dr="AND a.next_follow_date>'".date('Y-m-d')."'";
			}
		$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk $dr GROUP BY b.id  ORDER BY b.id DESC");
			// }else
			// {
			// 	$cur=date('Y-m-d');
			// 	$resty=$this->db->query("SELECT a.lead_stage,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.next_follow_date='$cur' AND a.lead_stage NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");
			// }
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=0;
			$lastfollowupdate=$row->next_follow_date;

			//  if($type==2)
			// {
			// 	// missed

			// 	if(strtotime($lastfollowupdate)<strtotime(date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
			// 		$show=1;
			// 	}

			// }else if($type==3)
			// {
			// 	// upcoming
			// 	if(strtotime($lastfollowupdate)>strtotime(date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
			// 		$show = 1;
			// 	}
			// }else if($type==1)
			// {
			// 	// today
			// 	if(strtotime($lastfollowupdate)==strtotime(date('Y-m-d')))
			// 	{
			// 	$show=1;
			// 	}
			// }else
			// {
			// 	$show=0;
			// }

			$show=1;
			if($show==1)
			{
			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else
			{	
				$type="Powder";
			}

			$lead_stage=$row->lead_status;
			//$lead_stage=$this->salescrm->getLeadStagenames($lead_stage);
			$query = $this->db->select('a.lead_name')
						   ->from('lead_stage a')
						
						   ->where('a.lead_id', $row->lead_status)
						
						   ->get();

						   foreach($query->result() as $stagerow);


			$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";
		

			$username=$this->salescrm->getusername($row->leadmanager);
			$lead_data[] = array(
								'sr_no'=>$i,
								'progress'=>$view,
								'status'=>"<strong style='color:red;font-weight:bold;'>".$stagerow->lead_name."</strong><br/><br/><strong>Next Followup</strong><br/>".date('d-M-Y',strtotime($lastfollowupdate)),
								'oppno'=>$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)),
								'opptype'=>$clienttype,
								'source'=>$leadsource,
								'company'=>$row->mastercompanyname,
								'type'=>$type,
								'address'=>$row->postal_address,
								'product'=>'',
								'remarks'=>$row->remarks,
								'manager'=>$username,
								'updatedOn'=>date('d-m-Y H:i A',strtotime($row->added_on))												
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


function quotation()
{

$basicData=$this->salescrm->getquoteBasicData($this->uri->segment(3));
if(count($basicData)>0)
{
	$ref_no=$basicData[0];
	$quotation_date=$basicData[1];
	$customer_id=$basicData[2];
	$currency=$basicData[3];
	$country=$basicData[4];
	$machine_name=$basicData[5];
	$machine_model_no=$basicData[6];
	$lead_id=$basicData[7];
	$product_id=$basicData[8];
	$user_detail=$this->salescrm->getUserDetails($lead_id);
	if(count($user_detail)>0)
	{
		$agent=$user_detail[0];
		$contact_no=$user_detail[1];
		$email=$user_detail[2];
		
	}else
	{
		$agent='';
		$contact_no='';
		$email='';
	}

	$customer_detail=$this->salescrm->getCustomerdetail($customer_id);
	if(count($customer_detail)>0)
	{
		$customerName=$customer_detail[2];
	}else{
		$customerName='';
	}


}else
{
	$ref_no='';
	$quotation_date='';
	$customer_id='';
	$currency='';
	$country='';
	$machine_name='';
	$machine_model_no='';
	$customerName='';
}

$annexture_1=$this->salescrm->getannexture_1($this->uri->segment(3));
if(count($getannexture_1)>0)
{
	$product_to_be_packed=$annexture_1[0];
	$product_name=$annexture_1[1];
	$pouch_size_type=$annexture_1[2];
	$qty_to_be_packed=$annexture_1[3];
	$horizontal_sealing_width=$annexture_1[4];
	$vertical_sealing_width=$annexture_1[5];
	$perforation_pitch=$annexture_1[6];
	$typeofsealing=$annexture_1[7];
	$plc_make=$annexture_1[8];
	$power_supply=$annexture_1[9];
}else
{

	$product_to_be_packed='';
	$product_name='';
	$pouch_size_type='';
	$qty_to_be_packed='';
	$horizontal_sealing_width='';
	$vertical_sealing_width='';
	$perforation_pitch='';
	$typeofsealing='';
	$plc_make='';
	$power_supply='';

}


$quotation_cum=$this->salescrm->quotation_cum_tech_spec($this->uri->segment(3));
if(count($quotation_cum)>0)
{
	$model=$quotation_cum[0];
	$no_of_axis_in_machine=$quotation_cum[1];
	$axis_detail=$quotation_cum[2];

}else
{
	$model='';
	$no_of_axis_in_machine='';
	$axis_detail='';
}

$getannexture_2=$this->salescrm->getannexture_2($this->uri->segment(3));
if(count($getannexture_2)>0)
{
$machinemodel=$getannexture_2[0];
$sealingstyle=$getannexture_2[1];
$speed=$getannexture_2[2];
$no_of_track=$getannexture_2[3];
$leminate_specification=$getannexture_2[4];
$product_to_be_packed=$getannexture_2[5];
$filling_capacity=$getannexture_2[6];
$pouch_size=$getannexture_2[7];
$sealing_drives=$getannexture_2[8];
$perforation_and_cutting=$getannexture_2[9];
$laminate_draw=$getannexture_2[10];
$laminate_tracking=$getannexture_2[11];
$electrical_spec=$getannexture_2[12];
$layout_dimensions=$getannexture_2[13];
$machine_weight=$getannexture_2[14];
$compressed_air=$getannexture_2[15];
}else
{
$machinemodel='';
$sealingstyle='';
$speed='';
$no_of_track='';
$leminate_specification='';
$product_to_be_packed='';
$filling_capacity='';
$pouch_size='';
$sealing_drives='';
$perforation_and_cutting='';
$laminate_draw='';;
$laminate_tracking='';
$electrical_spec='';
$layout_dimensions='';
$machine_weight='';
$compressed_air='';
}
$html='<table width="100%" ruled="all" style=" padding:3px;">
<tr>
<td style="text-align:left">Ref. no. '.$ref_no.'</td>
<td style="text-align:right;">Date: - '.date('d M Y',strtotime($quotation_date)).'</td>
</tr>
</table>
<table style="padding-top:50px">
<tr>
<td>
<h2 style="text-align:center;">Quote for<br>
'.strtoupper($machine_name).' MODEL '.strtoupper($machine_model_no).'</h2>
</td></tr>
</table>
<table style="padding-top:50px">
<tr>
<td><i style="text-align:center; font-size:18px;">“Global Standards Unmatched Performance”</i></td>
</tr>
</table>

<table style="padding-top:150px">
<tr>
<td style="text-align:center;">Specially prepared for<br>
<h2>'.strtoupper($customerName).'</h2>
</td>

</tr>
</table>

<table style="padding-top:200px">
<tr>
<td>
Submitted by:<br>
'.ucwords(strtolower($agent)).'<br>
Projects<br>
Email: '.ucwords(strtolower($email)).'<br>
Cell: +91-'.ucwords(strtolower($contact_no)).'<br>
</td>
</tr>
</table>

<br pagebreak="true">

<table>
<tr>
<td><h2 style="text-align:center;">Index</h2></td></tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-I</u></td>
<td style="text-align:center; width:50%">Project Data</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-II</u></td>
<td style="text-align:center; width:50%">Technical Specifications</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-III</u></td>
<td style="text-align:center; width:50%">Exclusions</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-IV</u></td>
<td style="text-align:center; width:50%">Price Schedule & Optional Items</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-V</u></td>
<td style="text-align:center; width:50%">Commercial terms and conditions</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-VI</u></td>
<td style="text-align:center; width:50%">Attachments<br>
- E-Brochure
 (on demand)</td>
</tr>
</table>
<br pagebreak="true">

<table>
<h2 style="text-align:center">Annexure-I<br>Project Data</h2>
</table>

<table border="1" style="text-align:center;">
<tr style="padding:20px;">
	<td style="padding: 10px;vertical-align: center;" width="4%" height="10%">1</td>
	<td style="padding: 10px;vertical-align: center;" width="32%" height="10%">Product to be Packed</td>
	<td style="padding: 10px;vertical-align: center;"  width="32%" height="10%">Liquid/Powder /Granules</td>
	<td style="padding: 10px;vertical-align: center;"  width="32%" height="10%">'.$product_to_be_packed.'</td>
</tr>

<tr style="padding-top:20px;">
	<td style="padding: 10px;vertical-align: center;" width="4%" height="10%">2</td>
	<td style="padding: 10px;vertical-align: center;"  width="32%" height="10%">Product name</td>
	<td style="padding: 10px;vertical-align: center;"  width="32%" height="10%"></td>
	<td style="padding: 10px;vertical-align: center;"  width="32%" height="10%">'.$product_name.'</td>
</tr>

<tr>
	<td>3</td>
	<td>Pouch Size & Type</td>
	<td>W x L</td>
	<td>'.$pouch_size_type.'</td>
</tr>

<tr>
	<td>4</td>
	<td>Quantity to be packed</td>
	<td>ml / gm</td>
	<td>'.$qty_to_be_packed.'</td>
</tr>

<tr>
	<td>5</td>
	<td>Horizontal Sealing Width</td>
	<td>Mm</td>
	<td>'.$horizontal_sealing_width.'</td>
</tr>
<tr>
	<td>6</td>
	<td>Vertical Sealing Width</td>
	<td>Mm</td>
	<td>'.$vertical_sealing_width.'</td>
</tr>

<tr>
	<td>7</td>
	<td>Perforation Pitch</td>
	<td>Mm</td>
	<td>'.$perforation_pitch.'</td>
</tr>

<tr>
	<td>8</td>
	<td>Type of Sealing</td>
	<td>VLining/Butt/Knurling</td>
	<td>'.$typeofsealing.'</td>
</tr>

<tr>
	<td>9</td>
	<td>PLC Make</td>
	<td>Omron / AB</td>
	<td>'.$plc_make.'</td>
</tr>

<tr>
	<td>10</td>
	<td>Power Supply</td>
	<td>VAC/Ph/Hz</td>
	<td>'.$power_supply.'</td>
</tr>
</table>
<br pagebreak="true">

<table>
<tr style="text-align:center;">
<td>Quotation Cum Technical Specification Of The Machine</td>
</tr>
</table>

<table border="1">
<tr>
<th style="width:20%; text-align:center; font-weight:bold;">Sr No.</th>
<th style="width:80%; text-align:center; font-weight:bold;">Description</th>
</tr>

<tr>
<td style="width:20%; text-align:center;">01</td>
<td style="width:80%"><b>'.$model.'</b>
'.$no_of_axis_in_machine.'
</td>
</tr>
<tr>
<td></td>
<td><strong>'.$axis_detail.'</strong></td></tr>
</table>
<br pagebreak="true">

<table>
<tr>
	<td><h2 style="text-align:center;">Annexure-II<br>
	Technical Specifications</h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>

<table border="1">
<tbody style="padding-left:30px">
<tr>
<td style="width:40%; padding:20px">Machine Model </td>
<td style="width:60%; padding:20px"><b>'.$machinemodel.'</b></td>
</tr>

<tr>
<td>Sealing Style </td>
<td>'.$sealingstyle.'</td>
</tr>

<tr>
<td>Speed</td>
<td>'.$speed.'</td>
</tr>

<tr>
<td>No. of Tracks</td>
<td>'.$no_of_track.'</td>
</tr>

<tr>
<td>Laminate specification </td>
<td>'.$leminate_specification.'</td>
</tr>

<tr>
<td>Product to be packed </td>
<td>'.$product_to_be_packed.'</td>
</tr>

<tr>
<td>Filling capacity </td>
<td>'.$filling_capacity.'</td>
</tr>

<tr>
<td>Pouch Size </td>
<td>'.$pouch_size.'</td>
</tr>

<tr>
<td>Sealing drives</td>
<td>'.$sealing_drives.'</td>
</tr>

<tr>
<td>Perforation and cutting </td>
<td>'.$perforation_and_cutting.'</td>
</tr>

<tr>
<td>Laminate Draw Off system </td>
<td>'.$laminate_draw.'</td>
</tr>

<tr>
<td>Laminate tracking system</td>
<td>'.$laminate_tracking.'</td>
</tr>
<tr>
<td>Electrical Spec. </td>
<td>'.$electrical_spec.'</td></tr>

<tr>
<td>Layout Dimensions </td>
<td>'.$layout_dimensions.'</td>
</tr>

<tr>
<td>Machine Weight </td>
<td>'.$machine_weight.'</td>
</tr>

<tr>
<td style="padding: 8px;">Compressed Air </td>
<td style="padding: 8px;">'.$compressed_air.'</td>
</tr>
</tbody>
</table>
<br pagebreak="true">

<table>
	<tr>
		<td style="text-align:center"><h2><u>Annexure-III<br>
Exclusion<br>
(To be provided by customer</u></h2></td>
	</tr>
</table>


<table>
<tr>
<td style="padding:40px">1.0 Foundation and any civil building work</td>
</tr>
<tr>
<td style="padding:40px">2.0 Dismantling of existing equipment, if any.</td>
</tr>
<tr style="padding:40px">
<td>3.0 Compressed air piping including compressor</td>
</tr>
<tr style="padding:40px">
<td>4.0 Incomer cable and power supply up to Shubham Pack control panel.</td>
</tr>
<tr style="padding:40px">
<td>5.0 Voltage stabilizer of suitable capacity in case voltage and frequency variation is more
than 10% & 3% respectively at site</td>
</tr>
<tr style="padding:40px">
<td>6.0 Bulk material/Laminate during Factory acceptance test (FAT)</td>
</tr>
<tr style="padding:40px">
<td>7.0 Special tools and tackles like cranes, lifts etc., at the time of installation.</td>
</tr>
<tr style="padding:40px">
<td>8.0 Skilled and unskilled man power at site along with qualified supervisor
to assist installation.</td>
</tr>


</table>
<br pagebreak="true">

<table>
<tr>
<h2 style="text-align:center;">Annexure-IV<br>
Price Schedule</h2>
</tr>
<tr>
<td><h3 style="text-align:center;">Line items below will be added as per the requirement</h3></td>
</tr>
</table>

<table style="width:100%; border-collapse: collapse;">
<thead>
<tr>
<th style="border: 1px solid black; padding: 8px; width:10%">S.no.</th>
<th style="border: 1px solid black; padding: 8px; width:60%">Item description</th>
<th style="border: 1px solid black; padding: 8px; width:10%">Qty</th>
<th style="border: 1px solid black; padding: 8px; width:20%">Price </th>
</tr>
</thead>
<tbody>';

$price_data=array();
$price_data[]=0;
$dprice=$this->salescrm->getPriceData($this->uri->segment(3),1);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}

$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Model- '.$desc.' </td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';



	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">A</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Price of design, manufacturing, supply and installation
of machine as per technical specifications at
Annexure-II<br><br>
· Ladder and Platform.<br>
· Guarding with Door switches.</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>';

		$dprice=$this->salescrm->getPriceData($this->uri->segment(3),2);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}


	$html.='<tr>
		
		<td style="border: 1px solid black; padding: 8px; width:10%">B</td>
		
		<td style="border: 1px solid black; padding: 8px; width:60%">'.$desc.'</td>
<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
		
	</tr>';

	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),3);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}


	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">C</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Discharge Conveyor with Rejection system</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';

	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),4);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}



	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">D</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Auto Case Packer with Transfer Conveyor</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>

	<tr style="background-color:#DFD5D9">
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%"><b>Total Cost Basic Machine</b></td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval(array_sum($price_data)).'</td>
	</tr>';


	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),5);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}


	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">E</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Packing Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';


	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),6);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}


	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">F</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Forwarding Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';

		$dprice=$this->salescrm->getPriceData($this->uri->segment(3),6);
if(count($dprice)>0)
{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
}else
{
	$desc='';
	$qty='';
	$price='';
}



	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">G</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Insurance Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';

	

	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),6);
	if(count($dprice)>0)
	{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
	}else
	{
	$desc='';
	$qty='';
	$price='';
	}


	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">H</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Freight until '.$desc.'</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval($price).'</td>
	</tr>';

	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),7);
	if(count($dprice)>0)
	{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
	}else
	{
	$desc='';
	$qty='';
	$price='';
	}


	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">I</td>
		<td style="border: 1px solid black; padding: 8px; width:60%"><b>Total CIF '.$desc.'</b></td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.array_sum($price_data).'</td>
	</tr>';

	$dprice=$this->salescrm->getPriceData($this->uri->segment(3),8);
	if(count($dprice)>0)
	{
	$desc=$dprice[0];
	$qty=$dprice[1];
	$price=$dprice[2];
	$price_data[]=$price;
	}else
	{
	$desc='';
	$qty='';
	$price='';
	}

	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%" rowspan="3">K</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Installation, Commissioning, Training at site by 2<br>
Engineers for 20 days <br><br>
(for details refer terms & conditions attached)
• To & Fro air Fare to be booked by customer<br>Hotels, meals and transfer to be taken care by
customer
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%" rowspan="3">'.floatval($qty).'</td>
		<td style="border: 1px solid black; padding: 8px; width:20%" rowspan="3">'.floatval($price).'</td>
	</tr>

	<tr>
		
		<td style="border: 1px solid black; padding: 8px; width:60%">Lodging, Boarding and Local Conveyance to be borne
by customer. (All Local Taxes must be beard by
Customer)
</td>
		
	</tr>

	<tr>
		
		<td style="border: 1px solid black; padding: 8px; width:60%">(Visa to be arranged by customer)
</td>
		
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%"><strong>Total Cost (CIF Jakarta Port) including Installation & commissioning</strong>
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.floatval(array_sum($price_data)).'</td>
	</tr>

	<tr>
		
		<td colspan="2" style="border: 1px solid black; padding: 8px; width:70%; text-align:center;"><strong>Optional:-</strong>
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>';

$optional=$this->salescrm->getOptionalData($this->uri->segment(3));
	if(count($optional)>0)
	{
		$y=1;
		foreach($optional as $option)
		{

	$html.='<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">'.$y.'</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">'.$option['description'].'
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%">'.$option['value'].'</td>
	</tr>';


		$y++;
		} }
	
$html.='</tbody>
</table>';

$odata=$this->salescrm->getotherinformation($this->uri->segment(3),8);
	if(count($dprice)>0)
	{
	$terms_value=$odata[0];
	$delivery_value=$odata[1];
	$pocket_expense=$odata[2];
	$support_statement_value=$odata[3];
	}else
	{
	$terms_value='';
	$delivery_value='';
	$pocket_expense='';
	$support_statement_value='';
	}


$html.='<table>
<tr>
<td><h2 style="text-align:center"><u>Annexure-V<br>
Commercial Terms and Conditions</u></h2></td>
</tr>
<tr>
<td>
<h4><u>PRICE BASIS</u></h4>
</td></tr>
<tr>
<td>All prices are on CIF Port, Basis unless otherwise specified.<br><br></td>
</tr>
<tr><h4><u>TERMS OF PAYMENT</u></h4></tr>
<tr>
<td>'.$terms_value.'
<br><br>
</td>
</tr>
<tr>
<td>The L/C must be advised and negotiable through Supplier’s Bankers, as per the below details:<br></td>
</tr>
<tr>
<td>Axis Bank Ltd.<br>
SCO-40, Sec-7 Market<br>
Ballabgarh, Faridabad 121004<br>
Haryana, India<br>
A/c holder name- Shubham Flexible Packaging Machines Pvt. Ltd.<br>
A/c no.- 039010200025364<br>
Swift code- AXISINBB039<br></td>
</tr>
<tr>
<td>The Letter of Credit must permit <b>partial shipment</b> and transshipment and should be valid for
negotiation for a period of 21 days beyond the last permissible date of shipment.<br><br></td></tr>
<tr>
<td><h4><strong>The Letter of Credit must accept Combined Transport Bill of Lading issued by the Shipping
Company in New Delhi as a negotiable document.</strong></h4><br><br><br><br></td>
</tr>
<tr>
<td><h4><strong><u>PACKING CHARGES: -</u></strong></h4><br>
In strong seaworthy wooden boxes. 1% of forwarding charges will be applicable in case of terms other
than Ex – Works.<br><br><br><br></td>
</tr>

<tr>
<td>
<h4><strong><u>INSURANCE: -</u></strong></h4><br>
Buyer’s responsibility to take suitable insurance for goods from seller’s warehouse in Ballabhgarh to
the port of discharge covering all risks including erection, installation and commissioning for 110 %
of CIF value. Documentary evidence of this insurance to be given to us at least 30 days before
shipment. Insurance will be applicable in case of terms other than Ex-works.<br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>FREIGHT: -</u></strong></h4><br>
CIF Jakarta Port<br><br></td>
</tr>

<tr>
<td><h4><strong><u>DELIVERY: -</u></strong></h4><br><br>
'.$delivery_value.'</td>
</tr>

<tr>
<td><h4><strong><u>INSTALLATION / START- UP AND TRAINING</u></strong></h4><br><br/>
To be done by factory trained engineers of Shubham pack or its authorized sub suppliers. The
installation cost is indicated separately in the price schedule at Annexure-IV. <br><br></td>
</tr>
<tr>
<td><strong>Buyer has to provide Hotel, Food, local conveyance and medical expenses for all service
engineers at customer site.</strong><br><br></td>
</tr>

<tr>
<td>Service /installation charges are extra as per number of days required. Buyer has to bear expenses
for extra to and fro air fares, if any, stay in hotel, food, local transport and medical expenses for
deputing engineers and also reimburse out of pocket expenses <strong>@ € '.$pocket_expense.'</strong> per day per including the
travel time and intervening holidays. <br><br></td>
</tr>

<tr>
<i>'.$support_statement_value.'<br><br></i>
</tr>

<tr>
<td>Local Labor, Power and other connected items including lifting, tackle, foundation and masonry work
during installation shall have to be provided by the Buyer.<br><br>
Buyer is advised to unload the machine from the truck/container and place it at designated place. It
is also advisable that inlet of bulk feed, air connection, power supply etc. should be made ready before
arrival of Installation engineer. However, all such connections as well as electrical power ON should
be done in presence of installation engineer only. <br><br></td>
</tr>

<tr>
<td><h4><strong><u>WARRANTY</u></strong></h4><br><br>
We warranty for a period of 24 months from the date of erection of products at Buyer’s site,
all products and parts thereof, when properly installed, adjusted, operated and maintained
as per our proposal and / or the applicable technical manuals. This will however not
include components made of rubber, plastic and electrical equipment and other parts /
components subject to normal wear and tear.<br><br>
Warranty does not cover consumables. These parts are considered as consumables and are required
to be paid for upon replacement.<br><br>
<p>Customer must buy critical and consumable spares for 24 months in order to avail comprehensive
warranty of 24 months.<br></p>
<p>Warranty does not cover damage of part due to poor preventive maintenance OR parts that are subject
to damage due to voltage fluctuation or improper voltage at buyer site.<br><br> </p>
<p>Warranty does not apply to any equipment which has been improperly installed, adjusted, operated,
maintained, repaired or altered by unauthorized persons. We reserve the right to inspect any claimed
defect prior to replacement.<br><br></p>
<p>We will replace/repair, any defective material or workmanship, provided that we are given
written notice of the claimed defects above. Unless caused by us, equipment damaged by
overloading, exposure to corrosive or abrasive substance of abnormal dampness or other
misuse, neglect or accident, shall not be subject to the warranty set forth above.<br><br></p>
<p>The Warranty as referred to above clause will cease to operate if:<br>
a. The buyer, within the Warranty period, sells or otherwise parts with possession of the products.<br><br>
 <p style="text-align:center">AND / OR</p><br>
b. Any local mechanic or electrician tampers with the Products without Shubham’ written
permission</p><br><br>

</td>
</tr>
<tr>
<td>
<h4><strong><u>LIABILITY</u></strong></h4><br><br>
<p>Liability of Shubham Flexible Packaging Machines Pvt. Ltd is limited to Warranty performance of
equipment and does not cover any aspect related to conversion of material. Shubham Flexible
Packaging Machines Pvt. Ltd undertakes no responsibility on performance of laminate/film converted
on the equipment. Customer must conduct trials at his own risk and cost, to evaluate and achieve
satisfactory results before commencing large-scale production. Machine speed and performance are
indicative and may vary with different substrates and raw material and may not be accurate. Liability of Shubham Flexible Packaging Machines Pvt. Ltd is only limited to replacement of defective parts and
does not cover any incidental or consequential loss to customer. <br><br></p><br><br>
</td>
</tr>
<tr>
<td><h4><strong><u>CANCELLATION</u></strong></h4><br><br>
<p>In the event of a request to stop work or to cancel any part of the order should be mutually discussed
between Supplier and Buyer.</p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>FORCE MAJEURE:</u></strong></h4><br><br>
<p>We will not be responsible for any delay in delivery or for non-delivery of our products by reasons of
Force Majeure, such as acts of God, war, riots, civil disturbances, acts of authorities, strikes, lockouts
or other labour difficulties or any other circumstances beyond our control which might affect us or
our suppliers and hinder, impede or prevent deliveries. We will send the notice of force majeure to
customer in writing.</p>
<br><br>

</td>
</tr>


</table>

<table>
<tr>
<td>
<h4><strong><u>APPROVAL</u></strong></h4>
<p>Shubham Flexible Packaging Machines Pvt. Ltd will fully assemble the equipment prior to shipping
and a representative of the customer must approve the Equipment in writing prior to shipment. Any
modification or change suggested at this point will invalidate the delivery date clause and reasonable
time will be allowed to incorporate the modification.<br><br></p><br><br>

</td>
</tr>

<tr>
<td><h4><strong><u>ARBITRATION</u></strong></h4><br><br>
<p>Any dispute or differences whatsoever arising between the parties out of or relating to the construction,
meaning and operation or effect of this contract or the breach thereof shall be settled by arbitration in
accordance with the Rules of Arbitration of the Indian Council of Arbitration and the Award made in
pursuance thereof shall be binding on the parties.<br><br></p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>VALIDITY:</u></strong></h4><br><br>
<p>This Contract and prices are valid for 30 days and supersedes all previous contracts. The terms and
conditions mentioned above shall supersede any terms and conditions agreed to earlier Performa
invoice if any.<br><br></p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>GENERAL:</u></strong></h4><br><br>
<p>Continuous improvement is standard policy at Shubham. Accordingly, all specification and features
are subject to change without any prior notice.<br><br></p>
</td>
</tr>
</table>
';

//echo $html; exit;
$this->load->library('Pdf');




$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Shubham Pack');
$pdf->SetTitle("Quotation");
$pdf->SetSubject('Quotation');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 058', PDF_HEADER_STRING, array(0,0,0), array(255,255,255));
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('times', '', 12, '', true);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/customerfile';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL = $filelocation . "/CRM_Daily_Report_".date('d-m-Y') . ".pdf"; //Linux
$pdf->Output($fileNL, 'F');


}

function getcountrytaxinfo(){
	$countryid = $this->input->post('countryid');
	if($countryid<>''){
	$q = $this->db->select('taxtype')->from('countries')->where('country_id',$countryid)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $rows);
		echo $rows->taxtype; exit;
	}
	
	}
	
}

function edit_opportunity(){
	$this->load->view('leads/edit_opportunity');
}

function all_opportunities()
{
$this->load->view('leads/all_opportunities');
}


public function lead_stage_list_pms_all() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();
		$lead_stage=$this->uri->segment(3);
		$user_id=$_SESSION['logged_in']['user_id'];
		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}

		$resty=$this->db->query("SELECT b.added_by as leadmanager,b.customer_gstn, b.vatno, b.customise_remarks, c.company_name as mastercompanyname,b.machine_type,b.distributor, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,c.contact_no, c.customer_name, c.alt_contact  FROM leads b JOIN customer_detail c ON c.id=b.company_name WHERE b.closed=0 $chk GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			if($clienttype=='Domestic'){
				$gstno = "GSTN- ".$row->customer_gstn;
			}else{
				$gstno = "VAT No- ".$row->vatno;
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else if($row->machine_type==2)
			{	
				$type="Powder";
			}else{
				$type="Customise<hr>";
			}


			$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

			/**** REMARKS STATUS **/

			$da=$this->LeadProgressDetails($row->leadid);
			if(count($da)>0)
			{
				$progressremarkid=$da[0];
				$visitdate=$da[1];
				$visittime=$da[2];
				$nonqualifiedreason=$da[3];
				$added_on=$da[4];
				$added_by=$da[5];
				$lead_stage=$da[6];
				$remarks=$da[7];

				$getLeadStageDetails=$this->salescrm->getLeadStageDetails($lead_stage);
				if($getLeadStageDetails<>'')
				{
				foreach ($getLeadStageDetails as $row1);
				$lstatus=$row1->lead_name;
				}else
				{
					$lstatus='';
				}

			}else
			{
				$progressremarkid=0;
				$visitdate='';
				$visittime='';
				$nonqualifiedreason=0;
				$added_on='';
				$added_by='';
				$lead_stage=0;
				$lstatus='';
				$remarks='';	
			}

			$username=$this->salescrm->getusername($row->leadmanager);
			$reecord_id=$this->salescrm->getRecordID($row->leadid);
			$url=page_url.'Opportunity/GeneratedQuote/'.$reecord_id;
			if($this->salescrm->checkforquotationoraheadstepNew($row->leadid)==1)
			{
			$quote="<a href='".$url."' target='_blank'>View Quotation</a>";
			}else
			{
				$quote="NA";
			}
			if($this->uri->segment(3)=='34'){
			$q = $this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$row->nonqualifiedreason)->get();
			if($q->num_rows()>0){
			foreach($q->result() as $lostresons);
			$leadlostreason = $lostresons->reason;
			}else{
			$leadlostreason = '';
			}
			}else{
			$leadlostreason = '';
			}


			$products=$this->getProductDetails($row->leadid);
			if(count($products)>0)
			{
				$prd_name=$products[0];
				$prd_qty=$products[1];
			}else
			{
				$prd_name='';
				$prd_qty='';
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'action'=>'<a href="'.page_url.'Leads/edit_opportunity/'.$row->leadid.'" target="_blank"><i class="fa fa-pencil"></i></a>',
								'progress'=>$view,
								'quote_step'=>$quote,
								'oppno'=>$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)),
								'opptype'=>$clienttype."<hr> ".$gstno,
								'source'=>$leadsource,
								'company'=>$row->mastercompanyname,
								'customerdetail'=>$row->customer_name."<br>".$row->contact_no."<br>".$row->alt_contact,
								'type'=>$type."<br>".$row->customise_remarks,
								'address'=>$row->postal_address,
								'product'=>"<strong>".$prd_name."<br/>".$prd_qty." Nos</strong>",
								'leadlostreason'=>$leadlostreason,
								'lstatus'=>"<strong style='color:red;'>".$lstatus."</strong>",
								'remarks'=>$remarks,
								'manager'=>$username,
								'updatedOn'=>date('d-m-Y H:i A',strtotime($added_on))												
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



function LeadProgressDetails($lead_id)
{
	$d=array();
	$query = $this->db->select('id,visitdate,visittime,nonqualifiedreason,added_on,added_by,lead_status,remarks')->from('progress_remarks')->where('lead_id',$lead_id)->limit(1)->order_by('id','desc')->get();
	if($query->num_rows()>0)
	{
	foreach($query->result() as $row)
	{
		$d[]=$row->id;
		$d[]=$row->visitdate;
		$d[]=$row->visittime;
		$d[]=$row->nonqualifiedreason;
		$d[]=$row->added_on;
		$d[]=$row->added_by;
		$d[]=$row->lead_status;
		$d[]=$row->remarks;
	}
	}

	return $d;
}


function getProductDetails($lead_id)
{
	$data=array();
	$sql = $this->db->select('a.id, a.qty, c.instruments_name,c.id as product_id')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('presto_instruments c', 'c.id=a.product_id')
						->where('a.lead_id', $lead_id)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row);
				$data[]=$row->instruments_name;
				$data[]=$row->qty;
				$data[]=$row->id;
				$data[]=$row->product_id;

			}

			return $data;
}

function getCustomerDetailsByID()
{
	$cus=$this->uri->segment(3);
	$this->db->select('a.customer_alias,a.id,a.company_name,a.company_brand,b.name')
                ->from('customer_detail a')
                ->join('company_brand b','a.company_brand=b.id')
               ->where('a.id',$cus);
    $query =$this->db->get();
    if($query->num_rows()>0)
    {
    	foreach($query->result() as $row);
    	echo "<option value='".$row->id."'>".$row->company_name."</option>"."|<option value='".$row->company_brand."'>".$row->name."</option>";
    }

}


function update_opportunity()
{

		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('op_date', 'Opportunity Date', 'required|trim');
		$this->form_validation->set_rules('lsource', 'Opportunity Source', 'required|trim');
		$this->form_validation->set_rules('op_type', 'Opportunity Type', 'required|trim');
		$this->form_validation->set_rules('op_no', 'Opportunity Number', 'required|trim');
		$this->form_validation->set_rules('mach_type', 'Machine Type', 'required|trim');
		$this->form_validation->set_rules('marketing', 'Marketing Person', 'required|trim');
		$this->form_validation->set_rules('customer', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('brand', 'Company Brand', 'required|trim');
		$this->form_validation->set_rules('address', 'Company Address', 'required|trim');
		//$this->form_validation->set_rules('gst', 'Company GST', 'required|trim');
		$this->form_validation->set_rules('product', 'Product', 'required|trim');
		$this->form_validation->set_rules('qty', 'Qty', 'required|trim');
		$this->form_validation->set_rules('customertype', 'Customer Type', 'required|trim');
		$this->form_validation->set_rules('probability', 'Probability of conversion', 'required|trim');
		$this->form_validation->set_rules('cname', 'Customer Name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('leads/edit_opportunity');
		}
		else
		{

			$op_number = $this->runtimegenerateOppNo($this->input->post('op_type'), $this->input->post('mach_type'));
			//echo "<pre>"; print_r($op_number); exit;

			$d=array('email'=>$this->input->post('customeremailid'),'contact_no'=>$this->input->post('customercontactno'),'customer_name'=>$this->input->post('cname'));
			$this->db->where('id',$this->input->post('customer_id'));
			$this->db->update('customer_detail',$d);

			$data=array(
				'lead_source_id'=>$this->input->post('lsource'),
				'patient_type_id'=>$this->input->post('op_type'),
				'create_date'=>date('Y-m-d',strtotime($this->input->post('op_date'))),
				'company_name'=>$this->input->post('customer'),
				'machine_type'=>$this->input->post('mach_type'),
				//'unique_id'=>$op_number[0],
				'postal_address'=>$this->input->post('address'),
				'gst'=>$this->input->post('gst'),
				'country'=>$this->input->post('country'),
				'customer_gstn'=>$this->input->post('gst'),
				'brand'=>$this->input->post('brand'),
				'added_on'=>date('Y-m-d'),
				'vatno'=>$this->input->post('vatno'),
				'customise_remarks'=>$this->input->post('remarks'),
				'merchantexport'=>$this->input->post('merchantexport'),
				'customer_type'=>$this->input->post('customertype'),
				'probability'=>$this->input->post('probability'),
				'added_by'=>$this->input->post('marketing')
				);

			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('leads',$data);


			/** LEAD PRODUCTS **/
			$data_prod = array(
			'product_id' => $this->input->post('product'),
			'qty' => $this->input->post('qty'),
			'updated_on'=>date('Y-m-d H:i:s'),
			'updated_by'=>$this->input->post('marketing')
			);
			$this->db->where('id',$this->input->post('product_detail_id'));
			$this->db->update('lead_products',$data_prod);
			/** END **/

	

			$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Opportunity Updated.</div>');
			redirect(page_url.'Leads/edit_opportunity/'.$this->uri->segment(3));


		}

}

public function approvalorrejection(){
	$user_id=$_SESSION['logged_in']['user_id'];	
	$id = $this->input->post('leadid');
	$status = $this->input->post('taskstatus');
	$remarks = $this->input->post('taskremarks');
	if($status==1){
		$stage = $this->salescrm->get_quote_approval_stage_after_won($id, 37);
		$remark_title = ($stage == 35) ? 'Revised Quotation Approved - Order Won.' : 'Quotation Approved.';
			$currentDate = new DateTime();
			$currentDate->modify('+2 days');
			$futureDate = $currentDate->format('Y-m-d');
			$data = array('lead_id'=>$id,
				'lead_status'=>$stage,
				'next_follow_date'=>$futureDate,
				'remarks'=>$remarks,
				'remark_title'=>$remark_title,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			
			$this->db->insert('progress_remarks',$data);
			if($stage == 35){
				$this->salescrm->notify_revised_quote_approved_for_order_won($id, $remarks);
			}

			$q = $this->db->select('b.title, b.first_name, b.last_name, contact_number')->from('leads a')->join('system_users b','a.added_by=b.user_id','left')->where('a.id',$id)->get();
			foreach($q->result() as $row);
			$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
			$usercontact = $row->contact_number;
			//$usercontact = "9718991797";
			/*Send WhatsApp Notification*/
			if($stage == 35){
			$message="Dear ".$leadownername.",

Your revised quotation which was earlier marked as Order Won has been approved and transferred to the Order Won stage again.

Regards,
Shubham Pack";
			}else{
			$message="Dear ".$leadownername.",

I am pleased to inform you that your quotation has been approved ✅. Kindly proceed with the necessary processing 📝.

Regards,
Shubham Pack 📦";
			}


/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */




			 $this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Quotation has been Appoved. </div>', 'refresh');
            redirect(page_url."Leads/lead_stages/".$stage);

	}else{
		$stage = 38;
		$remark_title = 'Quotation Rejected.';
			$currentDate = new DateTime();
			$currentDate->modify('+2 days');
			$futureDate = $currentDate->format('Y-m-d');
			$data = array('lead_id'=>$id,
				'lead_status'=>$stage,
				'next_follow_date'=>$futureDate,
				'remarks'=>$remarks,
				'remark_title'=>$remark_title,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			
			$this->db->insert('progress_remarks',$data);

			$q = $this->db->select('b.title, b.first_name, b.last_name, contact_number')->from('leads a')->join('system_users b','a.added_by=b.user_id','left')->where('a.id',$id)->get();
			foreach($q->result() as $row);
			$leadownername = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
			$usercontact = $row->contact_number;
			//$usercontact = "9718991797";
			/*Send WhatsApp Notification*/
			$message="Dear ".$leadownername.",

We regret to inform you that your quotation has been rejected ❌. The remarks provided are as follows: ".$remarks.".

Kindly review the remarks and make the necessary adjustments if needed 📝.

Regards,
Shubham Pack 📦

";



					/*WhatsApp API*/
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					/* end */

			 $this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Quotation marked as Rejected. </div>', 'refresh');
            redirect(page_url."Leads/lead_stages/".$stage);
	}
}


function TextFormatting($str)
{
	return ucwords(strtolower($str));
}

function sourcewiseopportunities()
{
$this->load->view('leads/sourcewiseopportinityfilter.php');
}

public function sourcewiseopportunitieslist() {
		// echo base64_decode($this->uri->segment(4));exit;
		$lead_data = array();
		$lead_stage=$this->uri->segment(3);
		$exhibitionid = $this->uri->segment(4);
		$user_id=$_SESSION['logged_in']['user_id'];
		// if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
		// 	{
		// 		$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
		// 	}else
		// 	{
		// 		$chk=' AND b.lead_source_id='.$lead_stage;
		// 	}

		$chk=' AND b.lead_source_id='.$lead_stage;
		if($exhibitionid<>''){
			$exhi = " AND b.exhibition=".$exhibitionid;
		}else{
			$exhi = '';
		}
		$resty=$this->db->query("SELECT b.added_by as leadmanager,b.customer_gstn, b.vatno, b.customise_remarks, c.company_name as mastercompanyname,b.machine_type,b.distributor, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,b.lead_source_id,b.country_code,b.id as leadid,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,c.contact_no, c.customer_name, c.alt_contact  FROM leads b JOIN customer_detail c ON c.id=b.company_name WHERE b.closed=0 $chk $exhi GROUP BY b.id  ORDER BY b.id DESC");
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {

			$clienttype=$this->salescrm->getClientTypebyid($row->patient_type_id);
			if($clienttype=='Domestic'){
				$gstno = "GSTN- ".$row->customer_gstn;
			}else{
				$gstno = "VAT No- ".$row->vatno;
			}
			$leadsource=$this->salescrm->getleadsourcebyid($row->lead_source_id);
			if($row->machine_type==1)
			{
				$type="Liquid";
			}else if($row->machine_type==2)
			{	
				$type="Powder";
			}else{
				$type="Customise<hr>";
			}


			$view = "<a href='".page_url."Leads/view_detail/".$row->leadid."/".$lead_stage."' class='btn btn-success btn-xs'>UPDATE PROGRESS</a>";

			/**** REMARKS STATUS **/

			$da=$this->LeadProgressDetails($row->leadid);
			if(count($da)>0)
			{
				$progressremarkid=$da[0];
				$visitdate=$da[1];
				$visittime=$da[2];
				$nonqualifiedreason=$da[3];
				$added_on=$da[4];
				$added_by=$da[5];
				$lead_stage=$da[6];
				$remarks=$da[7];

				$getLeadStageDetails=$this->salescrm->getLeadStageDetails($lead_stage);
				if($getLeadStageDetails<>'')
				{
				foreach ($getLeadStageDetails as $row1);
				$lstatus=$row1->lead_name;
				}else
				{
					$lstatus='';
				}

			}else
			{
				$progressremarkid=0;
				$visitdate='';
				$visittime='';
				$nonqualifiedreason=0;
				$added_on='';
				$added_by='';
				$lead_stage=0;
				$lstatus='';
				$remarks='';	
			}

			$username=$this->salescrm->getusername($row->leadmanager);
			$reecord_id=$this->salescrm->getRecordID($row->leadid);
			$url=page_url.'Opportunity/GeneratedQuote/'.$reecord_id;
			if($this->salescrm->checkforquotationoraheadstepNew($row->leadid)==1)
			{
			$quote="<a href='".$url."' target='_blank'>View Quotation</a>";
			}else
			{
				$quote="NA";
			}
			if($this->uri->segment(3)=='34'){
			$q = $this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$row->nonqualifiedreason)->get();
			if($q->num_rows()>0){
			foreach($q->result() as $lostresons);
			$leadlostreason = $lostresons->reason;
			}else{
			$leadlostreason = '';
			}
			}else{
			$leadlostreason = '';
			}


			$products=$this->getProductDetails($row->leadid);
			if(count($products)>0)
			{
				$prd_name=$products[0];
				$prd_qty=$products[1];
			}else
			{
				$prd_name='';
				$prd_qty='';
			}

			$lead_data[] = array(
								'sr_no'=>$i,
								'action'=>'<a href="'.page_url.'Leads/edit_opportunity/'.$row->leadid.'" target="_blank"><i class="fa fa-pencil"></i></a>',
								'progress'=>$view,
								'quote_step'=>$quote,
								'oppno'=>$row->unique_id."<br/>".date('d-m-Y',strtotime($row->create_date)),
								'opptype'=>$clienttype."<hr> ".$gstno,
								'source'=>$leadsource,
								'company'=>$row->mastercompanyname,
								'customerdetail'=>$row->customer_name."<br>".$row->contact_no."<br>".$row->alt_contact,
								'type'=>$type."<br>".$row->customise_remarks,
								'address'=>$row->postal_address,
								'product'=>"<strong>".$prd_name."<br/>".$prd_qty." Nos</strong>",
								'leadlostreason'=>$leadlostreason,
								'lstatus'=>"<strong style='color:red;'>".$lstatus."</strong>",
								'remarks'=>$remarks,
								'manager'=>$username,
								'updatedOn'=>date('d-m-Y H:i A',strtotime($added_on))												
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


public function getCustomerCountry() {
    $customer_id = $this->input->post('customer_id');
    if ($customer_id) {
        $query = $this->db->select('a.country as country_id, b.country_name')
                          ->from('leads a')
                          ->join('countries b', 'a.country = b.country_id', 'left')
                          ->where('a.company_name', $customer_id)
                          ->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            echo json_encode([
                'success' => true,
                'country_id' => $row->country_id,
                'country_name' => $row->country_name
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No country associated with this customer.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid customer ID.']);
    }
}


public function getAllCountries() {
    $query = $this->db->select('country_id, country_name')
                      ->from('countries')
                      ->where('country_status', 1)
                      ->get();
    if ($query->num_rows() > 0) {
        $countries = $query->result();
        echo json_encode(['success' => true, 'countries' => $countries]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No countries available.']);
    }
}


public function sendMissedFollowupReport() {
    // Fetch all active users in the marketing department
    $users_query = $this->db->select('email, user_id')
                            ->from('system_users')
                            ->where('department_id', 9)
                            ->where('user_status', 1) // Active users only
                            ->get();

    if ($users_query->num_rows() > 0) {
        $users = $users_query->result();

        // Iterate through each user to fetch their missed follow-ups
        foreach ($users as $user) {
            $query = $this->db->query("
                SELECT 
                    a.lead_id, 
                    b.create_date,
                    b.company_name, 
                    a.next_follow_date, 
                    a.remarks, 
                    b.added_by as leadmanager, 
                    c.company_name as mastercompanyname,
                    c.customer_name, 
                    c.contact_no
                FROM progress_remarks a
                JOIN leads b ON a.lead_id = b.id
                LEFT JOIN customer_detail c ON c.id = b.company_name
                WHERE a.id IN (
                    SELECT MAX(id) FROM progress_remarks GROUP BY lead_id
                )
                AND a.next_follow_date < '" . date('Y-m-d') . "'
                AND a.next_follow_date <> '0000-00-00'
                AND a.next_follow_date <> '1970-01-01'
                AND a.lead_stage NOT IN (34, 35, 36) -- Exclude specific lead statuses
                AND b.added_by = {$user->user_id} -- Filter by the respective lead manager
                ORDER BY a.next_follow_date ASC
            ");

            if ($query->num_rows() > 0) {
                $data = $query->result();

                // Prepare HTML table
                $html = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">';
                $html .= '<thead style="background-color: #4872b8; color: #ffffff; text-align: left;">
                            <tr>
                                <th>Sr. No</th>
                                <th>Create Date</th>
                                <th>Customer Name</th>
                                <th>Company Name</th>
                                <th>Contact No</th>
                                <th>Next Follow-Up Date</th>
                                <th>Last Discussion with Client</th>
                            </tr>
                          </thead>';
                $html .= '<tbody>';
                $i = 1;
                foreach ($data as $row) {
                	$customername = ucwords(strtolower($row->customer_name));
                	$companyname = ucwords(strtolower($row->mastercompanyname));
                	$remarks = ucwords(strtolower($row->remarks));
                	$leadcreatedate = date('d-m-Y',strtotime($row->create_date));
                    $html .= "<tr>
                                <td>{$i}</td>
                                <td>{$leadcreatedate}</td>
                                <td>{$customername}</td>
                                <td>{$companyname}</td>
                                <td>{$row->contact_no}</td>
                                <td style='color: #ff0000;'>" . date('d-M-Y', strtotime($row->next_follow_date)) . "</td>
                                <td>{$remarks}</td>
                              </tr>";
                    $i++;
                }
                $html .= '</tbody>';
                $html .= '</table>';

                // Send email to the respective lead manager
                $this->load->library('email');
                $this->email->from('taskmanagement@shubhampack.com', 'Missed Follow-Up Report - PMS');
                //$this->email->to($user->email); // Send to the respective lead manager
                $this->email->to('mangleshup@gmail.com'); // Send to the respective lead manager
                //$this->email->cc('shubham@shubhampack.com'); // Send to the respective lead manager
                $this->email->bcc('mangleshup@gmail.com');
                $this->email->subject('🔴 Missed Follow-Up Report');

                // Compose attractive email body with logo
                $email_body = "
                    <div style='font-family: Arial, sans-serif; color: #333333;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack Logo' style='max-width: 150px;'>
                        </div>
                        <p>Dear <strong>{$this->salescrm->getusername($user->user_id)}</strong>,</p>
                        <p>Here is your report of missed follow-ups for your leads:</p>
                        {$html}
                        <p style='margin-top: 20px;'>Please take the necessary actions to ensure timely follow-ups with your leads.</p>
                        <p>Best regards,</p>
                        <p><strong>Shubham Flexible Packaging - PMS</strong></p>
                        <hr style='border: 0; border-top: 1px solid #ddd;'>
                        <p style='font-size: 12px; color: #666;'>This is an automated email. Please do not reply.</p>
                    </div>
                ";

                $this->email->message($email_body);

                if ($this->email->send()) {
                    echo "Email sent successfully to {$user->email}!<br>";
                } else {
                    echo "Failed to send email to {$user->email}. " . $this->email->print_debugger() . "<br>";
                }
            } else {
                echo "No missed follow-ups found for {$user->email}.<br>";
            }
        }
    } else {
        echo "No users found in the marketing department.";
    }
}


public function update_followup_date() {
        // Security check: Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $lead_id = $this->input->post('lead_id');
        $new_date = $this->input->post('new_followup_date');
        
        // Basic validation
        if (empty($lead_id) || empty($new_date)) {
            $response = ['status' => 'error', 'message' => 'Invalid data provided.'];
        } else {
            // Call the model function to perform the update
            $updated = $this->update_latest_followup($lead_id, $new_date);
            
            if ($updated) {
                $response = ['status' => 'success', 'message' => 'Follow-up date updated successfully!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Could not find the record or failed to update.'];
            }
        }
        
        // Send JSON response back to the JavaScript
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
    }


public function update_latest_followup($lead_id, $new_date) {
        $this->db->select('id');
        $this->db->from('progress_remarks');
        $this->db->where('lead_id', $lead_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        
        $last_remark = $query->row();
        //echo "<pre>"; print_r($last_remark); exit;
        // Step 2: If a remark was found, update it.
        if ($last_remark) {
            $remark_id_to_update = $last_remark->id;
            
            // The data to update. Assuming your column is named 'followup_date'.
            $data = [
                'next_follow_date' => $new_date
            ];
            
            //echo "<pre>"; print_r($data); exit;
            $this->db->where('id', $remark_id_to_update);
            $this->db->update('progress_remarks', $data);
            
            // Check if the update was successful
            return $this->db->affected_rows() > 0;
        }

        return false; // No remark found for this lead
    }



}
