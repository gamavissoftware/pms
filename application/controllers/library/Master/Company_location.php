<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_location extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}

		// $config = array();  
  //       $config['protocol'] = 'smtp';  
  //       $config['smtp_host'] = 'smtp.googlemail.com';  
  //       $config['smtp_user'] = 'leadsgamavis@gmail.com';  
  //       $config['smtp_pass'] = 'Gamavis@i42';   
  //       $config['smtp_port'] = 465;  
  //       $config['smtp_auth'] = true;  
  //       $config['smtp_crypto'] = 'ssl';  
  //       $this->email->initialize($config);  
		// $this->email->set_newline("\r\n");  
  //       $this->load->library('email', $config); 

		$config = array();  
        $config['protocol'] = 'smtp';  
        $config['smtp_host'] = 'smtpout.secureserver.net';  
        $config['smtp_user'] = 'mitr@prestomitr.com';  
        $config['smtp_pass'] = 'Presto@123!@#';   
        $config['smtp_port'] = 465;  
        $config['smtp_auth'] = true;  
        $config['smtp_crypto'] = 'ssl';  
        $this->email->initialize($config);  
		$this->email->set_newline("\r\n");  
        $this->load->library('email', $config);

$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		
	}

	public function index()
	{
		
		$this->load->view('company/company_location');
	}
	
	public function add_company_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('rack_location', 'rack_location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('company/company_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "store_rack_location";
		$query = $this->db->select('rack_location')->from('store_rack_location')->where('rack_location',$this->input->post('rack_location'))->get();
		$res = $query->result();
		
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Master/Company_location/add_company_location');
			
		}else{
	
        $data = array('rack_location'=>$this->input->post('rack_location'),'address'=>$this->input->post('address'),'pincode'=>$this->input->post('pincode'),'gst'=>$this->input->post('gst'),'contact_person'=>$this->input->post('contact_person'),'email_id'=>$this->input->post('email'),'mobile'=>$this->input->post('mobile'),'companyname'=>$this->input->post('company'),'status'=>$this->input->post('status'),'alt_mobile'=>$this->input->post('altmobile'),'landline_number'=>$this->input->post('landline'),'googlemap'=>$this->input->post('googlemap'),'locate_us'=>$this->input->post('locateus'),'bank_details'=>$this->input->post('bank_details'),'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id'],'hpcl_code'=>$this->input->post('hpclcode'));
        $result  = $this->master->insert_record($table,$data);	
        if($result)
        {
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Company_location/add_company_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Company_location/add_company_location');
		}
		}
		
	}
		
	}
	public function campany_location_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('*')->from('store_rack_location');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			
			$edit = "<a href='".page_url."Master/Company_location/edit_company_location/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			if($row->status==1)
			{
				$sta="<a href='".page_url."Master/Company_location/update_company_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-success'>Open</span></a>";
			
			}else{
				
				$sta="<a href='".page_url."Master/Company_location/update_company_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-danger'>Closed</span></a>";
			}
			$s=$this->db->select('state_name')->from('states')->where('state_id',$row->state_id)->get();
			if($s->num_rows() >0)
			{
			foreach($s->result() as $state);
			$c=$this->db->select('city_name')->from('cities')->where('city_id',$row->city_id)->get();
			if($c->num_rows() >0)
			{
			foreach($c->result() as $city);
			$Cit=$city->city_name;
			}
			else
			{
				$Cit='';
			}
			$state=$state->state_name.'<br>'.$Cit;
			}else
			{
				$state='';
			}
			$googlemap="<a href='".$row->googlemap."' target='_blank'>".$row->googlemap."</a>";
			$locate_us="<a href='".$row->locate_us."' target='_blank'>".$row->locate_us."</a>";
			$vendor_data[] = array('sr_no'=>$i,
			'companyname'=>$row->companyname,
			'rack_location'=>$row->rack_location,
			'gst'=>$row->gst,
			'pincode'=>$row->pincode,
			'address'=>$row->address,
			'contactperson'=>$row->contact_person,
			'mobile'=>$row->mobile,
			'email'=>$row->email_id,
			'state'=>$state,
			'alt_mobile'=>$row->alt_mobile,
			'landline_number'=>$row->landline_number,
			'googlemap'=>$googlemap,
			'locate_us'=>$locate_us,
			'status'=>$sta,
			'hpclcode'=>$row->hpcl_code,
			'bank_details'=>$row->bank_details,
			'bank_details_hpcl'=>$row->bank_details_hpcl,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function edit_company_location(){
		$this->load->view('company/edit_company_location');
	}
	
	public function update_company_location()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('rack_location', 'Rack Location', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('company/edit_company_location');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "store_rack_location";
		// $query = $this->db->select('rack_location')->from('store_rack_location')->where('rack_location',$this->input->post('rack_location'))->get();
		// $res = $query->result();
		
		// if($res){
		// 	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
		// 	redirect(page_url.'Store/add_rack_location');
			
		// }else{

			$photo=$_FILES['profile']['name'];
			if($photo <> '') {
			$image1 = explode('.',$photo);
			$cat_image = end($image1);
			$instrumentimg_spec = time().'.'.$cat_image;
			move_uploaded_file($_FILES["profile"]["tmp_name"],UPLOADPATH.'profile/' . $instrumentimg_spec);
			} else {
			$instrumentimg_spec = $spec;
			}


			$data = array(
			'rack_location'=>$this->input->post('rack_location'),
			'address'=>$this->input->post('address'),
			'gst'=>$this->input->post('gst'),
			'contact_person'=>$this->input->post('contact_person'),
			'email_id'=>$this->input->post('email'),
			'pincode'=>$this->input->post('pincode'),
			'mobile'=>$this->input->post('mobile'),
			'status'=>$this->input->post('status'),
			'state_id'=>$this->input->post('state'),
			'city_id'=>$this->input->post('cityname'),
			'companyname'=>$this->input->post('company'),
			'alt_mobile'=>$this->input->post('altmobile'),
			'landline_number'=>$this->input->post('landline'),
			'googlemap'=>$this->input->post('googlemap'),
			'hpcl_code'=>$this->input->post('hpclcode'),
			// 'bank_details'=>$this->input->post('bank_details'),
			'profile'=>$instrumentimg_spec,
			'bank_details_hpcl'=>$this->input->post('bank_details_hpcl'),
			'locate_us'=>$this->input->post('locateus'));
			$this->db->where('id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	

		/** OLD ACCOUNT DETAILS **/
		$edit_id=$this->input->post('edit_id');

		if(count($edit_id)>0)
		{
		for($i=0;$i<count($edit_id);$i++)
		{
			$bank=$this->input->post('bankname_edit'.$edit_id[$i]);
			$account=$this->input->post('account_edit'.$edit_id[$i]);
			$ifsc=$this->input->post('ifsc_edit'.$edit_id[$i]);
			$branch=$this->input->post('branch_edit'.$edit_id[$i]);
			$primary=$this->input->post('primary_edit'.$edit_id[$i]);
			if($edit[$i]==2)
			{
				echo $primary; exit;
			}

			if($primary==1)
			{
			$primary_a=1;
			}else
			{
			$primary_a=0;
			}
		

			$data=array('bank_name'=>$bank,'account'=>$account,'ifsc'=>$ifsc,'branch'=>$branch,'primary_account'=>$primary_a);
			$this->db->where('id',$edit_id[$i]);
			$this->db->update('store_rack_location_account',$data);

			if($primary_a==1)
			{
				$detail=$bank."<br/>ACCOUNT NUMBER:-".$account."<br/>IFSC CODE:-".$ifsc."<br/>BRANCH:-".$branch;
				$dd=array('bank_details'=>$detail);
				$this->db->where('id',$this->uri->segment(4));
				$this->db->update('store_rack_location',$dd);
			}
		
		}

		}
		/** END **/

		/** NEW ACCOUNT DETAILS **/

		$addnaccount=$this->input->post('addnaccount');
		if($addnaccount==1){

		$bankname_new=$this->input->post('bankname');
		$account_new=$this->input->post('account');
		$ifsc_new=$this->input->post('ifsc');
		$branch_new=$this->input->post('branch');
		$primary_new=$this->input->post('primary');

			if(count($bankname_new)>0)
			{
			for($y=0;$y<count($bankname_new);$y++)
			{
				if($primary_new[$y]==1)
				{
					$primary_new_b=1;
				}else
				{
					$primary_new_b=0;
				}


				$dataa=array('location_id'=>$this->uri->segment(4),'bank_name'=>$bankname_new[$y],'account'=>$account_new[$y],'ifsc'=>$ifsc_new[$y],'branch'=>$branch_new[$y],'primary_account'=>$primary_new_b);
				$this->db->insert('store_rack_location_account',$dataa);

				if($primary_new_b==1)
			{
				$detail=$bankname_new[$y]."<br/>ACCOUNT NUMBER:-".$account_new[$y]."<br/>IFSC CODE:-".$ifsc_new[$y]."<br/>BRANCH:-".$branch_new[$y];
				$dd=array('bank_details'=>$detail);
				$this->db->where('id',$this->uri->segment(4));
				$this->db->update('store_rack_location',$dd);
			}


			}
			}


		}


		/** END **/





		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Company_location/add_company_location');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Company_location/add_company_location');
		}
		//}
		
	}
		
	}
	public function	update_company_status()
	{
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "store_rack_location";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect('Master/Company_location/');
	}
	public function getcityname()
	{	
		$city='';
		$stied=$this->input->post('stateid');
		$res=$this->db->select('city_id,city_name')->from('cities')->where('state_id',$stied)->get();
		if($res->num_rows()>0)
		{	
			foreach($res->result() as $cityy)
			{
			$city.='<option value='.$cityy->city_id.'>'.$cityy->city_name.'<option>';
			}
		}

		echo $city; 
	}
	public function quotation_terms()
	{
		$this->load->view('company/quotation_term');
	}
	public function add_terms()
	{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('terms', 'Terms', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('company/quotation_term');
		}
		else
		{
		$for=$this->input->post('termsfor');
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_quotation_terms_condition";
		// $query = $this->db->select('id')->from('customer_quotation_terms_condition')->where('term_for',$for)->get();
		// $res = $query->result();
		
		// if($res){
		// 	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry! This record already exist.</div>');
		// 	redirect(page_url.'Master/Company_location/quotation_terms');
			
		// }else{
			$data = array(
			'company_id'=>$this->input->post('company'),
			'term_for'=>$this->input->post('termsfor'),
			'term_conditions'=>$this->input->post('terms'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'added_by'=>$user_id);
			//$this->db->where('id',$this->uri->segment(4));
		$result  = $this->db->insert($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Company_location/quotation_terms');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Company_location/quotation_terms');
		}
		// }
		
	}
		
	}

	public function terms_list()
	{
		$i=1;
		$vendor_data= array();
		$this->db->select('a.*, b.companyname')
				 ->from('customer_quotation_terms_condition a')
				 ->join('store_rack_location b', 'b.id=a.company_id', 'left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
												
			
			$edit = "<a href='".page_url."Master/Company_location/edit_terms/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			if($row->status==1)
			{
				$sta="<a href='".page_url."Master/Company_location/update_terms_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-success'>Open</span></a>";
			
			}else{
				
				$sta="<a href='".page_url."Master/Company_location/update_terms_status/".$row->id."/".$row->status."'><span class='btn btn-xs btn-danger'>Closed</span></a>";
			}
			if($row->term_for==1)
			{
				$for="Drums";
			}else
			{
				$for="Bulk";
			}
			$vendor_data[] = array('sr_no'=>$i,
			'company'=>$row->companyname,
			'term_for'=>$for,
			'term_conditions'=>$row->term_conditions,			
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	public function	update_terms_status()
	{
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "customer_quotation_terms_condition";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect('Master/Company_location/quotation_terms');
	}

	public function edit_terms()
	{
		$this->load->view('company/edit_quotation_term');
	}
	public function update_quotation_terms()
	{
		$uri=$this->uri->segment(4);
		$for=$this->input->post('termsfor');
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "customer_quotation_terms_condition";		
			$data = array(
			//'term_for'=>$this->input->post('termsfor'),
			'company_id'=>$this->input->post('company'),
			'term_conditions'=>$this->input->post('terms'),
			'status'=>$this->input->post('status'));
			$this->db->where('id',$this->uri->segment(4));
		$result  = $this->db->update($table,$data);	
		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Master/Company_location/quotation_terms');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/Company_location/quotation_terms');
		}
		

	}

	public function send_mail_to_customer()
	{
		$uri=$this->uri->segment(4);
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
    $cu=$this->db->select('customer_name,city,state,contact_no,address,company_name')->from('customer_detail')->where('id',$customerid)->get();
    if($cu->num_rows() >0)
    {
        foreach($cu->result() as $cdetail);
        $customer_name=$cdetail->customer_name;
        $company_name=$cdetail->company_name;
        $city=$cdetail->city;
        $contact_no=$cdetail->contact_no;
        $address=$cdetail->address;
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
}
}else{
    redirect(page_url.'Customer/quotation/'.$uri);
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
                            if($com->num_rows() >0)
                            {
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
                        } 
                       $Message.='</td>
                        <td width="10%"></td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td>
        </tr>
    </table>';
 		//echo $Message; exit;
		$this->email->set_mailtype("html");
		$this->email->to('guptarvind92@gmail.com');
		$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
		$this->email->from('mitr@prestomitr.com');
		$this->email->subject($subjectname);
		$this->email->message($Message);
		$result11=$this->email->send();
		//$this->email->print_debugger(); exit;
		$this->session->set_flashdata('message','<div class="alert alert-success">mail send.</div><br/>');
			redirect(page_url.'Customer/quotation_view/');
	}

	function quotation_view()
	{
		$this->load->view('customer/quotationlist');
	}

	function quotation_list()
	{
		$lead_data = array();
		$this->db->select('a.*, b.companyname,c.contact_no,c.customer_name,c.email,c.city,c.address,c.company_name');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id');
		$this->db->join('customer_detail c','a.customer_id=c.id');
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$html='';
			$j=1;
			$html.='<table class="table table-borderd"><thead><tr><th>sr_no<th><th>Product Name<th><th>Per pcak qty<th><th>Per pcak list price<th><th>discount per liter<th><th>Net Price per ltr</th></tr></thead><tbody>';
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
			$view = "<a href='".page_url."Customer/quotation_view/".$row->id."' class='btn btn-success btn-xs'>view Quotation</a>";
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
			'quotation' => $view
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
	
}
