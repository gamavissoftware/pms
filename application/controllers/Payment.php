<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {
	
	public function __construct()
	{
parent::__construct();
$this->load->model('User_model','user');
$this->load->model('Salescrm_model','salecrm');
$config = array();  
$config['protocol'] = 'smtp';  
$config['smtp_host'] = 'smtpout.secureserver.net';  
$config['smtp_user'] = 'mitr@prestomitr.com';  
$config['smtp_pass'] = 'Presto@123!@#';   
$config['smtp_port'] = 587;  
$this->email->initialize($config);  
  $user_id =$this->session->userdata['logged_in']['user_id'];
$this->email->set_newline("\r\n");  
$this->load->library('email', $config);
			
	}

public function index()
	{
	$this->load->view('payment/payment');
	}
public function add_payment_info()
	{
	    
	  $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('amount', 'Amount', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('payment/payment');
			}else
		{
		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "payment_reconciliation";
		
		$data = array('company_name'=>$this->input->post('company_name'),
		'bill_date'=>date('Y-m-d'),
		'amount'=>$this->input->post('amount'),
		'payment_type'=>$this->input->post('payment_type'),
		'invoice_number'=>$this->input->post('invoice_no'),
		'remarks'=>$this->input->post('remarks'),
		'added_on'=>$date,
		'added_by'=>$_SESSION['logged_in']['user_id']);
		$result  = $this->db->insert($table,$data);	
		$id= $this->db->insert_id();
		if($result)
		{
		 $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Payment');
		 
		     }
		   }  
	 }  
	    

	
	public function payment_reconciliation_dashboard()
	{
		$this->load->view('payment/payment_list');
	}
	public function payment_list()
	{
		$i=1;
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$vendor_data= array();
		$this->db->distinct();
		$date = date('Y-m-d');
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as firstname, c.last_name as lastname')->from('payment_reconciliation a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.accepted_by=c.user_id','left')->order_by('a.bill_date','desc')->limit(50);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		 $acceptedby = $row->firstname." ".$row->lastname;
		    if($row->accepted_by=='0'){
		    $markasmine = "Pending";
		    }else{
		        $markasmine = $acceptedby;
		    }
		    
		$addedby = $row->first_name." ".$row->last_name;
		$edit = "<a href='".page_url."Reminder/edit_reminder/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			$vendor_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'bill_date'=>date('d-m-Y',strtotime($row->bill_date)),
			'amount'=>$row->amount,
			'payment_type'=>$row->payment_type,
			'invoice_number'=>$row->invoice_number,
			'sonumber'=>$row->sonumber,
			'remarks'=>$row->remarks,
			'action'=>$edit,
			'accepted_by'=>$markasmine,
			'added_by'=>$addedby);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
	public function sales_payment_dashboard(){
	    $this->load->view('payment/sales_payment_list');
	}
	public function sales_payment_list()
	{
		$i=1;
		$enddate = date('Y-m-d',strtotime("-15 days"));
		$startdate = date('Y-m-d');
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$vendor_data= array();
		$this->db->distinct();
		$date = date('Y-m-d');
		
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as firstname, c.last_name as lastname')->from('payment_reconciliation a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.accepted_by=c.user_id','left');
		if($this->uri->segment(3)){
		if($user_id=='34' || $user_id=='87'){
		   $userdetail = array('34','87');
		   $this->db->where_in('accepted_by',$userdetail);
		}else{
			
		     $this->db->where('accepted_by',$user_id); 
		}
		}else{
		  $this->db->where('accepted_by','0');  
		}
		//$this->db->where('bill_date BETWEEN "'.$enddate. '" and "'.$startdate.'"');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		    
		    $acceptedby = $row->firstname." ".$row->lastname;
		    if($row->accepted_by=='0'){
		            $markasmine = '<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal'.$row->id.'">Accept</button>';
		            
		            $markasmine.='<div id="con-close-modal'.$row->id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
					<form id="loginForm" method="post" action="'.page_url.'Payment/update_customer_info/'.$row->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Customer Information</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
											  <div class="col-md-6">
                                             <div class="form-group">
                                             <label>Select Payment Type</label><br>
                                             <select class="form-control" name="payment_type" id="payment_type'.$row->id.'" onchange="getinformation('.$row->id.');">
											 <option value="">Select Option</option>
												            <option value="AGAINST SO">AGAINST SO</option>
												            <option value="SO NOT MADE">SO NOT MADE</option>
												            <option value="BALANCE AGAINST INVOICE">BALANCE AGAINST INVOICE</option>
												            <option value="SUSPENSE">SUSPENSE</option>

												        </select>
                                             </div>
                                             </div>
											 
										<script>  
										function getinformation(i){
										var paymenttype = $("#payment_type"+i).val();
										if (paymenttype == "AGAINST SO")
										{
										$("#hidesonumber"+i).show();
										$("#customername"+i).show();
										$("#invoicenumber"+i).hide();
										$("#so_number"+i).attr("required",true);
										$("#invoice_number"+i).attr("required",false);
										$("#customer_name"+i).attr("required",true);
										}
										
										if (paymenttype == "SO NOT MADE")
										{
										$("#hidesonumber"+i).show();
										$("#customername"+i).show();
										$("#invoicenumber"+i).hide();
										$("#customer_name"+i).attr("required",true);
										$("#invoice_number"+i).attr("required",false);
										}
										
										if (paymenttype == "BALANCE AGAINST INVOICE")
										{
										$("#hidesonumber"+i).hide();
										$("#customername"+i).hide();
										$("#invoicenumber"+i).show();
										$("#invoice_number"+i).attr("required",true);
										$("#customer_name"+i).attr("required",false);
										}
										
										
										if (paymenttype == "SUSPENSE")
										{
										$("#invoicenumber"+i).hide();
										$("#hidesonumber"+i).show();
										$("#customername"+i).show();
										$("#so_number"+i).attr("required",true);
										$("#customer_name"+i).attr("required",true);
										$("#invoice_number"+i).attr("required",false);
										$("#customer_name"+i).attr("required",true);
										}
										
			}
										</script> 
											 <div class="col-md-6" id="hidesonumber'.$row->id.'" style="display:none">
                                             <div class="form-group">
                                             <label>SO Number</label><br>
                                             <input type="text" class="form-control" name="so_number" id="so_number'.$row->id.'" value="" style="width:250px">
                                             </div>
                                             </div>
											 
											  <div class="col-md-6" style="display:none;" id="customername'.$row->id.'">
                                             <div class="form-group">
                                             <label>Customer Name</label><br>
                                             <input type="text" class="form-control" name="customer_name" id="customer_name'.$row->id.'" value="'.$row->company_name.'" style="width:250px">
                                             </div>
                                             </div>
											 
											  <div class="col-md-6" style="display:none;" id="invoicenumber'.$row->id.'">
                                             <div class="form-group">
                                             <label>Invoice Number</label><br>
                                             <input type="text" class="form-control" name="invoice_number" id="invoice_number'.$row->id.'" value="" style="width:250px">
                                             </div>
                                             </div>
											 
											 
                                            
                                             <div class="col-md-12"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
		            
		            
		        
		    }else{
		        $markasmine = $acceptedby;
		    }
		$addedby = $row->first_name." ".$row->last_name;
		$edit = "<a href='".page_url."Reminder/edit_reminder/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			$vendor_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'bill_date'=>date('d-m-Y',strtotime($row->bill_date)),
			'amount'=>$row->amount,
			'payment_type'=>$row->payment_type,
			'invoice_number'=>$row->invoice_number,
			'sonumber'=>$row->sonumber,
			'remarks'=>$row->remarks,
			'action'=>$edit,
			'accepted_by'=>$markasmine,
			'added_by'=>$addedby);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}

    public function acceptit(){
        $id = $this->uri->segment(3);
		$so_number= $this->input->post('so_number');
        date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$data = array('accepted_by'=>$user_id,
		'payment_type'=>$this->input->post('payment_type'),
		'sonumber'=>$so_number);
		$this->db->where('id',$id);
		$this->db->update('payment_reconciliation',$data);
		 $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
		redirect(page_url.'Payment/sales_payment_dashboard');
    }	
    
    public function update_customer_info(){
        $id = $this->uri->segment(3);
		
        date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$so_number= $this->input->post('so_number');
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$data = array('accepted_by'=>$user_id,
		'payment_type'=>$this->input->post('payment_type'),
		'company_name'=>$this->input->post('customer_name'),
		'invoice_number'=>$this->input->post('invoice_number'),
		'sonumber'=>$so_number);
		$this->db->where('id',$id);
		$this->db->update('payment_reconciliation',$data);
		 $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
		redirect(page_url.'Payment/sales_payment_dashboard');
    }
    
    	public function suspense_payment_list()
	{
		$i=1;
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$vendor_data= array();
		$this->db->distinct();
		$date = date('Y-m-d');
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as firstname, c.last_name as lastname')->from('payment_reconciliation a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.accepted_by=c.user_id','left')->where('company_name','')->order_by('a.bill_date','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		 $acceptedby = $row->firstname." ".$row->lastname;
		    if($row->accepted_by=='0'){
		    $markasmine = "Pending";
		    }else{
		        $markasmine = $acceptedby;
		    }
		    
		$addedby = $row->first_name." ".$row->last_name;
		$edit = "<a href='".page_url."Reminder/edit_reminder/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			$vendor_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'bill_date'=>date('d-m-Y',strtotime($row->bill_date)),
			'amount'=>$row->amount,
			'payment_type'=>$row->payment_type,
			'invoice_number'=>$row->invoice_number,
			'remarks'=>$row->remarks,
			'action'=>$edit,
			'accepted_by'=>$markasmine,
			'added_by'=>$addedby);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}
	
public function suspense_dashboard(){
    $this->load->view('payment/suspense_report');
}

public function filter_by_type(){
	    $this->load->view('payment/filter_by_type');
	}
	public function filter_by_type_list()
	{
		$i=1;
		$uri = $this->uri->segment(3);
		if($uri=='1'){
			$type = "AGAINST SO";
		}else if($uri=='2'){
			$type = "SO NOT MADE";
		}else if($uri=='3'){
			$type = "BALANCE AGAINST INVOICE";
		}else if($uri=='4'){
			$type = "SUSPENSE";
		}else{
			$type="";
		}
		$enddate = date('Y-m-d',strtotime("-30 days"));
		$startdate = date('Y-m-d');
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$vendor_data= array();
		$this->db->distinct();
		$date = date('Y-m-d');
		$this->db->select('a.*, b.first_name, b.last_name, c.first_name as firstname, c.last_name as lastname')->from('payment_reconciliation a')->join('system_users b','a.added_by=b.user_id','left')->join('system_users c','a.accepted_by=c.user_id','left');
		$this->db->where('a.payment_type',$type);
		if($this->uri->segment(4)){
		     $this->db->where('accepted_by',$user_id); 
		}else{
		  //$this->db->where('accepted_by','0');  
		}
		//$this->db->where('bill_date BETWEEN "'.$enddate. '" and "'.$startdate.'"');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		    
		    
		    $acceptedby = $row->firstname." ".$row->lastname;
		    if($row->accepted_by=='0'){
		        if($row->payment_type=='SUSPENSE'){
		            $markasmine = '<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Accept</button>';
		            
		            $markasmine.='<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Payment/update_customer_info/'.$row->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Customer Information</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
                                             <div class="col-md-6">
                                             <div class="form-group">
                                             <label>Customer Name</label><br>
                                             <input type="text" class="form-control" name="customer_name" id="customer_name" value="" style="width:250px" required>
                                             </div>
                                             </div>
											 
											 <div class="col-md-6">
                                             <div class="form-group">
                                             <label>SO Number</label><br>
                                             <input type="text" class="form-control" name="so_number" id="so_number" value="" style="width:250px" required>
                                             </div>
                                             </div>
                                            
                                             <div class="col-md-12"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
		            
		            
		        }else{
					$markasmine = '<button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">Accept</button>';
					$markasmine.='<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
 <form id="loginForm" method="post" action="'.page_url.'Payment/acceptit/'.$row->id.'"  enctype="multipart/form-data">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">Update Customer Information</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                             
                                             <div class="col-md-6">
                                             <div class="form-group">
                                             <label>SO Number</label><br>
                                             <input type="text" class="form-control" name="so_number" id="so_number" value="" style="width:250px" required>
                                             </div>
                                             </div>
                                            
                                             <div class="col-md-12"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                                        </div>
                                    </div>
                                </div>
								</form>
                            </div>';
		            
		    
		        }
		    }else{
		        $markasmine = $acceptedby;
		    }
		$addedby = $row->first_name." ".$row->last_name;
		$edit = "<a href='".page_url."Reminder/edit_reminder/".$row->id."'><i class='fa fa-pencil-square-o'></i></a>";
			$vendor_data[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'bill_date'=>date('d-m-Y',strtotime($row->bill_date)),
			'amount'=>$row->amount,
			'payment_type'=>$row->payment_type,
			'invoice_number'=>$row->invoice_number,
			'sonumber'=>$row->sonumber,
			'remarks'=>$row->remarks,
			'action'=>$edit,
			'accepted_by'=>$markasmine,
			'added_by'=>$addedby);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}

	function cheque_bounce()
	{
		$this->load->view('payment/cheque_bounce');
	}

	function filter_cheques()
	{
		$type=$this->input->post('type');		
		if($type==1)
		{	
			$flag1="NA";
			$flag2=base64_encode($this->input->post('cheque'));
		}else
		{
			$flag1=$this->input->post('our_company');
			$flag2=$this->input->post('customer');
		}



		redirect(page_url.'Payment/cheque_bounce/'.$type.'/'.$flag1.'/'.$flag2);
	}


	function payment_history_for_cheque_bounce()
	{ 
		$lead_data=array();
		$type=$this->uri->segment(3);
		$company=$this->uri->segment(4);
		$cheque_customer=$this->uri->segment(5);
		 $this->db->select('b.bank,a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id');
			$this->db->where('a.payment_type',1);
			if($type==1)
			{
				$this->db->where('a.cheque_no',base64_decode($cheque_customer));
			}else
			{
				$this->db->where('b.customer_id',$cheque_customer);
			}
			$this->db->order_by('a.payment_date','DESC');


		$restey=$this->db->get();
    if($restey->num_rows()>0)
    {
    	$i=1;
    	foreach($restey->result() as $row)
    	{
    		$invoice='';
    		$cno='';
    		$cdate='';

    		if($row->billtype==1)
    		{
    			$pay_type="<span class='btn btn-xs btn-warning'>FIFO</span>";
    			$edit=$row->payment_id;
    		}else
    		{
    			
    			$pay_type="<span class='btn btn-xs btn-success'>AGAINST BILL</span>";
    			if($row->bills<>'')
    			{
    				$invoice=$this->get_invoice_no($row->bills);
    			}else
    			{
    				$invoice='';
    			}
    			$edit="<a href='javascript:;' onclick='delete_payment_data(".$row->payment_id.",".$row->customerpart.");'><i class='fa fa-trash'></i></a>|".$row->payment_id;
    		}

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


    		$name=$this->getusername($row->addedBy);
    		$invoice_no=$this->get_invoice($row->id);
    		//."|".$row->customerpart

    		$edit="<a href='".page_url."Payment/check_bounce_remarks/1/NA/".base64_encode($row->cheque_no)."' class='btn btn-warning'>Mark Cheque Bounced</a>";

    			$bank11=$this->getbankaccount($row->bank);
				$lead_data[] = array('sr_no'=>$i."<br/>".$row->customerpart,
				'payment_date'=>date('d-M-Y',strtotime($row->payment_date)),
				'customer'=>$row->company_name,
				'bank_account'=>$bank11,
				'payment_mode'=>$PT,
				'cheque_no'=>$cno."<br/>".$cdate,
				'amount'=>$row->amount,
				'bill_type'=>$pay_type,
				'payment_settled'=>$invoice,
				'addedOn'=>date('d-M-Y',strtotime($row->addedOn)),
				'addedby'=>$name,
				'editpaymenthistorydata'=>$edit
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
	function getusername($username)
	{
		$name='';
		$restey=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$username)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$name=$row->first_name." ".$row->last_name;
		}

		return $name;
	}
	function get_invoice($id)
	{
		$inv=array();
		$resteyu=$this->db->select('invoice_no')->from('order_punch')->where('payment_id',$id)->get();
		if($resteyu->num_rows()>0)
		{
			foreach($resteyu->result() as $restete)
			{
				$inv[]=$restete->invoice_no;
			}
		}

		return implode(',',$inv);
	}

	function getbankaccount($bank1)
	{
		$bank='';
		$rty=$this->db->select('id,bank_name,account')->from('store_rack_location_account')->where('id',$bank1)->get();
		if($rty->num_rows()>0)
		{
		foreach($rty->result() as $row);
		$bank=$row->bank_name."<br/>".$row->account;

		}

		return $bank;

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

	function check_bounce_remarks()
	{
		$this->load->view('payment/add_bounce_remarks');

	}

	function cheque_bounce_entry()
	{
		$cheque_no=$this->input->post('cheque_no');
		$order_id=$this->input->post('order_id');
		$payment_particular_id=$this->input->post('payment_particular_id');
		//echo "<pre>"; print_r($order_id); exit;
		$data=array('cheque_no'=>$cheque_no,'remarks'=>$this->input->post('rmk'),'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('cheque_bounce_history',$data);
		$bounce_id=$this->db->insert_id();

		for($t=0;$t<count($payment_particular_id);$t++)
		{

			$pp_id=$payment_particular_id[$t];

			$rets=$this->db->select('*')->from('customer_payment_particulars')->where('id',$pp_id)->get();
			if($rets->num_rows()>0)
			{
				foreach($rets->result() as $data);

				$newdata=array('original_id'=>$data->id,'bounce_id'=>$bounce_id,'payment_date'=>$data->payment_date,'payment_id'=>$data->payment_id,'payment_type'=>$data->payment_type,'cheque_no'=>$data->cheque_no,'cheque_date'=>$data->cheque_date,'neft_trans_no'=>$data->neft_trans_no,'amount'=>$data->amount);
				$this->db->insert('customer_payment_particulars_bounce',$newdata);

				/** DEL **/
				$this->db->where('id',$data->id);
				$this->db->delete('customer_payment_particulars');
				/** END **/

				$rets1=$this->db->select('*')->from('customer_payments')->where('id',$data->payment_id)->get();
				if($rets1->num_rows()>0)
				{
				foreach($rets1->result() as $data1);
				$newdata1=array(
					'original_id'=>$data1->id,
					'bounce_id'=>$bounce_id,
					'customer_id'=>$data1->customer_id,
					'hpcl_billing_company'=>$data1->hpcl_billing_company,
					'type'=>$data1->type,
					'bills'=>$data1->bills,
					'bank'=>$data1->bank,
					'addedOn'=>$data1->addedOn,
					'addedBy'=>$data1->addedBy
					);

					$this->db->insert('customer_payments_bounce',$newdata1);

					/** DEL **/
					$this->db->where('id',$data1->id);
					$this->db->delete('customer_payments');
					/** END **/


				}

			}




		for($i=0;$i<count($order_id);$i++)
		{


				$rets1=$this->db->select('*')->from('customer_order_to_payments')->where('order_id',$order_id[$i])->get();
				if($rets1->num_rows()>0)
				{
					foreach($rets1->result() as $data1);
					$newdata2=array('original_id'=>$data1->id,
					'bounce_id'=>$bounce_id,
					'order_id'=>$order_id[$i],
					'payment_id'=>$data1->payment_id,
					'order_amount'=>$data1->order_amount,
					'pending_order_amount'=>$data1->pending_order_amount,
					'recieved_amount'=>$data1->recieved_amount,
					'balance'=>$data1->balance);
					$this->db->insert('customer_order_to_payments_bounce',$newdata2);

					/** DEL **/
					$this->db->where('id',$data1->id);
					$this->db->delete('customer_order_to_payments');
					/** END **/

				}	


			$dr=array('payment'=>0,'payment_id'=>0,'adjustment'=>0,'adjustment_type'=>0);
			$this->db->where('id',$order_id[$i]);
			$this->db->update('order_punch',$dr);
		}



	}

	 $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
	redirect(page_url.'Payment/cheque_bounce_history');
}

function cheque_bounce_history()
{
	$this->load->view('payment/cheque_bounce_history');
}


function payment_history_for_cheque_bounce_history()
	{ 
		$lead_data=array();
		$type=$this->uri->segment(3);
		$company=$this->uri->segment(4);
		$cheque_customer=$this->uri->segment(5);

		$restey1=$this->db->select('a.*,b.first_name,b.last_name')->from('cheque_bounce_history a')->join('system_users b','a.addedBy=b.user_id')->get();
		if($restey1->num_rows()>0)
		{
			foreach($restey1->result() as $row1)
			{
		 $this->db->select('b.customer_id,b.bank,a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy')->from('customer_payment_particulars_bounce a')->join('customer_payments_bounce b','a.payment_id=b.original_id');
			$this->db->where('a.bounce_id',$row1->id);
			// if($type==1)
			// {
			// 	$this->db->where('a.cheque_no',base64_decode($cheque_customer));
			// }else
			// {
			// 	$this->db->where('b.customer_id',$cheque_customer);
			// }
			$this->db->order_by('a.payment_date','DESC');


		$restey=$this->db->get();
    if($restey->num_rows()>0)
    {
    	$i=1;
    	foreach($restey->result() as $row)
    	{
    		$invoice='';
    		$cno='';
    		$cdate='';

    	

    		if($row->billtype==1)
    		{
    			$pay_type="<span class='btn btn-xs btn-warning'>FIFO</span>";
    			$edit=$row->payment_id;
    		}else
    		{
    			
    			$pay_type="<span class='btn btn-xs btn-success'>AGAINST BILL</span>";
    			if($row->bills<>'')
    			{
    				$invoice=$this->get_invoice_no($row->bills);
    			}else
    			{
    				$invoice='';
    			}
    			$edit="<a href='javascript:;' onclick='delete_payment_data(".$row->payment_id.",".$row->customerpart.");'><i class='fa fa-trash'></i></a>|".$row->payment_id;
    		}

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


    		$name=$this->getusername($row->addedBy);
    		$invoice_no=$this->get_invoice($row->id);
    		//."|".$row->customerpart

    		$edit="<a href='".page_url."Payment/check_bounce_remarks/1/NA/".base64_encode($row->cheque_no)."' class='btn btn-warning'>Mark Cheque Bounced</a>";
    		$c=$this->salescrm->getCustomerdetail($row->customer_id);
    		if(count($c)>0)
    		{
    			$company_name=$c[1];
    		}else
    		{
    			$company_name='';
    		}

    			$bank11=$this->getbankaccount($row->bank);
				$lead_data[] = array('sr_no'=>$i."<br/>".$row->customerpart,
				'payment_date'=>date('d-M-Y',strtotime($row->payment_date)),
				'customer'=>$company_name,
				'bank_account'=>$bank11,
				'payment_mode'=>$PT,
				'cheque_no'=>$cno."<br/>".$cdate,
				'amount'=>$row->amount,
				'bill_type'=>$pay_type,
				'payment_settled'=>$invoice,
				'addedOn'=>date('d-M-Y',strtotime($row1->addedOn)),
				'addedby'=>$row1->first_name." ".$row1->last_name,
				'remarks'=>$row1->remarks
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

}
