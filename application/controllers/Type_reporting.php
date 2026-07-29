<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type_reporting extends CI_Controller {
	
	public function __construct()
	{
		
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
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
		
		
	}
	
	public function type1statistics(){
		$this->load->view('type_reporting/type1statistics');
	}
	
	public function productapprovaltrend(){
		$this->load->view('type_reporting/product_approval_trend');
	}
	public function filterproductapprovaltrend(){
		$productvalue = $this->input->post('productname');
		$startdate = date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));
		$data['data'] = array('productvalue'=>$productvalue,
		'startdate'=>$startdate,
		'enddate'=>$enddate);
		
		redirect(page_url.'Type_reporting/productapprovaltrend/'.$productvalue."/".$startdate."/".$enddate);
	}
	public function productpurchasetrend(){
		$this->load->view('type_reporting/productpurchasetrend');
	}
	
	
	public function getdata(){
		$productvalue = $this->uri->segment(3);
		$startdate = $this->uri->segment(4);
		$enddate = $this->uri->segment(5);
		
		$q = $this->db->select('a.current_date,b.approved_price, c.instruments_name')->from('approval_form a')->join('approval_product_details b','a.id=b.approval_id')->join('presto_instruments c','b.product_id=c.id')->where('b.product_id',$productvalue)->where('a.current_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->order_by('a.current_date','ASC')->get();
		
		foreach($q->result() as $row){
			 $output[] = array(
            'labels'  => $row->approved_price,
            'count' => $row->approved_price,
            'indus'=>date('d M Y',strtotime
			($row->current_date))
            );
			
		}
		  print json_encode($output);
	}
	
	public function filterproductpurchasetrend(){
		$startdate = date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));
		$data['data'] = array(
		'startdate'=>$startdate,
		'enddate'=>$enddate);
		
		$this->load->view('type_reporting/productpurchasetrend',$data);
	}
	
	public function getdata1(){
		$startdate = $this->uri->segment(3);
		$enddate = $this->uri->segment(4);
		$q = $this->db->select('')->from('purchase_entry a')->join('approval_product_details b','a.id=b.approval_id')->join('presto_instruments c','b.product_id=c.id')->where('b.product_id',$productvalue)->where('a.current_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->get();
		
		foreach($q->result() as $row){
			 $output[] = array(
            'labels'  => $row->approved_price,
            'count' => $row->approved_price,
            'indus'=>date('d M Y',strtotime
			($row->current_date))
            );
			
		}
		  print json_encode($output);
	}

	function monthly_claim_trend()
	{
		$this->load->view('type_reporting/montly_claim_trend');
	}

	function filter_monthly_claim_type_1()
	{
		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		redirect(page_url.'Type_reporting/monthly_claim_trend/'.$startdate.'/'.$enddate);

	}



	public function getClaimdata_type1(){
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)));
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)));
		
		$date1 = strtotime($startdate);
		$date2 = strtotime($enddate);


		while ($date1 <= $date2) {
			$credit_note_sum=array();
			$credit_note_sum[]=0;
			$start_date=date('Y-m-01',$date1);
			$end_date=date('Y-m-t',$date1);
			
			 $this->db->select('a.id, a.bill_no, b.name as party, c.auto_gen_code, d.name')
						  ->from('purchase_entry a')
						  ->join('vendors b', 'b.id=a.party')
						  ->join('approval_form c', 'c.id=a.approval_id')
						  ->join('hpcl_location d', 'd.id=c.hpcl_location');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.currentdate >=', $start_date);
				 $this->db->where('a.currentdate <=', $end_date);
			}
			 $query =  $this->db->get();	
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$sql = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->join('presto_instruments c', 'c.id=b.product_id')
									->join('units d', 'd.id=a.pack_size')
									->where('a.entry_id', $row->id)
									->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum[] = $rows->qty * $rows->credit_vli;

						}

					}

			}

		}
 
 			$sum=array_sum($credit_note_sum);

		 $output[] = array(
		            'labels'=>$sum,
		            'count' =>$sum,
		            'indus'=>date('M', $date1)
		            );

  					$date1 = strtotime('+1 month', $date1);
					}
		
		
		  print json_encode($output);
	}

	public function typetwostatistics(){
		$this->load->view('type_reporting/typetwostatistics');
	}

	public function typetwoproductapprovaltrend(){
		$this->load->view('type_reporting/typetwoproductapprovaltrend');
	}

	public function filtertypetwoproductapprovaltrend() {
		$productvalue = $this->input->post('productname');
		$startdate = date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));

		$data['data'] = array(
								'productvalue'=>$productvalue,
								'startdate'=>$startdate,
								'enddate'=>$enddate
							 );
		
		redirect(page_url.'Type_reporting/typetwoproductapprovaltrend/'.$productvalue."/".$startdate."/".$enddate);
	}

	public function getdatatypetwo() {
		$productvalue = $this->uri->segment(3);
		$startdate = $this->uri->segment(4);
		$enddate = $this->uri->segment(5);
		$output = array();

		$q = $this->db->select('a.current_date,b.approved_price, c.instruments_name')
					  ->from('type_two_approval a')
					  ->join('type_two_product_details b','a.id=b.approval_id')
					  ->join('presto_instruments c','b.product_id=c.id')
					  ->where('b.product_id',$productvalue)
					  ->where('a.current_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')
					  ->order_by('a.current_date','ASC')
					  ->get();
		
		if($q->num_rows() > 0) {
			foreach($q->result() as $row){
				 $output[] = array(
	            'labels'  => $row->approved_price,
	            'count' => $row->approved_price,
	            'indus'=>date('d M Y',strtotime
				($row->current_date))
	            );
				
			}
		}
		  print json_encode($output);
	}

	function monthlytypetwoclaimtrend()
	{
	  $this->load->view('type_reporting/monthlytypetwoclaimtrend');
	}

	function filtermonthlytypetwoclaimtrend()
	{
		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		redirect(page_url.'Type_reporting/monthlytypetwoclaimtrend/'.$startdate.'/'.$enddate);

	}


	public function getClaimDataTypeTwo(){
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)));
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)));
		
		$date1 = strtotime($startdate);
		$date2 = strtotime($enddate);


		while ($date1 <= $date2) {
			$credit_note_sum=array();
			$credit_note_sum[]=0;
			$start_date=date('Y-m-01',$date1);
			$end_date=date('Y-m-t',$date1);
			
			      $this->db->select('a.id')
						   ->from('type_two_approval a')
						   ->join('hpcl_location b', 'b.id=a.hpcl_location')
						   ->join('item_delivery c','c.approval_id=a.id')
						   ->join('system_users d','d.user_id=a.addedBy','left')
						   ->where('a.item_delivered',1)
						   ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}
			 $query =  $this->db->get();	
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$sql = $this->db->select('b.qty, a.approved_price, a.price_validity, a.credit_vli')
									->from('type_two_product_details a')
									->join('item_delivery_details b', 'a.id=b.approval_detail_id')
									->join('presto_instruments c', 'c.id=a.product_id')
									->where('a.approval_id', $row->id)
									->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum[] = $rows->qty * $rows->credit_vli;

						}

					}

			}

		}
 
 			$sum=array_sum($credit_note_sum);

		 $output[] = array(
		            'labels'=>$sum,
		            'count' =>$sum,
		            'indus'=>date('M', $date1)
		            );

  					$date1 = strtotime('+1 month', $date1);
					}
		
		
		  print json_encode($output);
	}

	function typetwotransporterledger()
	{
	  $this->load->view('type_reporting/typetwotransporterledger');
	}

	function getTransporterLedger() {
		$output = array();
		$transporter = $this->uri->segment(3);
		$start_date = date('Y-m-d',strtotime($this->uri->segment(4)));
		$end_date = date('Y-m-d',strtotime($this->uri->segment(5)));

				 $this->db->select('c.transporter_id, c.transport_rate, d.name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('transporter_details d','d.id=c.transporter_id')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($transporter <> '' && $transporter <> 'ALL') {
				$this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->group_by('c.transporter_id')
						  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$sum=$this->get_type_two_transporter_payment($row->transporter_id, $start_date, $end_date);

				$output[] = array(
			            'labels'=>$sum,
			            'count' =>$sum,
			            'indus'=>$row->name
		            );
			}
		}

		print json_encode($output);

	}

	function get_type_two_transporter_payment($transporter_id, $start_date, $end_date) {
		 $t=array();
	 	 $t[]=0;
			$restey=$this->db->select('b.qty, d.transport_rate')
							 ->from('type_two_product_details a')
							 ->join('type_two_approval c', 'c.id=a.approval_id')
							 ->join('item_delivery_details b','a.id=b.approval_detail_id')
							 ->join('item_delivery d','d.id=b.delivery_id')
							 ->where('d.transporter_id',$transporter_id)
							 ->get();
			
			if($restey->num_rows()>0)
			{
				$i=1;
				foreach($restey->result() as $rows)
				{
					$t[] = $rows->qty * $rows->transport_rate;
				}
			}

			return array_sum($t);
	}

	function filter_type_two_ledger() {
		$transporter = $this->input->post('transporter');
		$startdate = $this->input->post('startdate');
		$enddate = $this->input->post('enddate');
		redirect(page_url.'Type_reporting/typetwotransporterledger/'.$transporter.'/'.$startdate.'/'.$enddate);

	}

	public function typethreestatistics(){
		$this->load->view('type_reporting/typethreestatistics');
	}


	public function typethreeproductapprovaltrend(){
		$this->load->view('type_reporting/typethreeproductapprovaltrend');
	}


public function getdatatypethree() {
		$productvalue = $this->uri->segment(3);
		$startdate = $this->uri->segment(4);
		$enddate = $this->uri->segment(5);
		$output = array();

		$q = $this->db->select('a.current_date,b.approved_price, c.instruments_name')
					  ->from('type_three_approval a')
					  ->join('type_three_product_details b','a.id=b.approval_id')
					  ->join('presto_instruments c','b.product_id=c.id')
					  ->where('b.product_id',$productvalue)
					  ->where('a.current_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')
					  ->order_by('a.current_date','ASC')
					  ->get();
		
		if($q->num_rows() > 0) {
			foreach($q->result() as $row){
				 $output[] = array(
	            'labels'  => $row->approved_price,
	            'count' => $row->approved_price,
	            'indus'=>date('d M Y',strtotime
				($row->current_date))
	            );
				
			}
		}
		  print json_encode($output);
	}


	public function filtertypethreeproductapprovaltrend() {
		$productvalue = $this->input->post('productname');
		$startdate = date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate = date('Y-m-d',strtotime($this->input->post('enddate')));

		$data['data'] = array(
								'productvalue'=>$productvalue,
								'startdate'=>$startdate,
								'enddate'=>$enddate
							 );
		
		redirect(page_url.'Type_reporting/typethreeproductapprovaltrend/'.$productvalue."/".$startdate."/".$enddate);
	}


	function monthlytypethreeclaimtrend()
	{
	  $this->load->view('type_reporting/monthlytypethreeclaimtrend');
	}

	function filtermonthlytypethreeclaimtrend()
	{
		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		redirect(page_url.'Type_reporting/monthlytypethreeclaimtrend/'.$startdate.'/'.$enddate);

	}


	public function getClaimDataTypeThree(){
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)));
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)));
		
		$date1 = strtotime($startdate);
		$date2 = strtotime($enddate);


		while ($date1 <= $date2) {
			$credit_note_sum=array();
			$credit_note_sum[]=0;
			$start_date=date('Y-m-01',$date1);
			$end_date=date('Y-m-t',$date1);
			
			      $this->db->select('a.id')
						   ->from('type_three_approval a')
						   ->join('hpcl_location b', 'b.id=a.hpcl_location')
						   ->join('item_delivery_type_3 c','c.approval_id=a.id')
						   ->join('system_users d','d.user_id=a.addedBy','left')
						   ->where('a.item_delivered',1)
						   ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}
			 $query =  $this->db->get();	
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$sql = $this->db->select('b.qty, a.approved_price, a.price_validity, a.credit_vli')
									->from('type_three_product_details a')
									->join('item_delivery_details_type_3 b', 'a.id=b.approval_detail_id')
									->join('presto_instruments c', 'c.id=a.product_id')
									->where('a.approval_id', $row->id)
									->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum[] = $rows->qty * $rows->credit_vli;

						}

					}

			}

		}
 
 			$sum=array_sum($credit_note_sum);

		 $output[] = array(
		            'labels'=>$sum,
		            'count' =>$sum,
		            'indus'=>date('M', $date1)
		            );

  					$date1 = strtotime('+1 month', $date1);
					}
		
		
		  print json_encode($output);
	}



function typethreetransporterledger()
	{
	  $this->load->view('type_reporting/typethreetransporterledger');
	}

	function getTransporterLedger_type_3() {
		$output = array();
		$transporter = $this->uri->segment(3);
		$start_date = date('Y-m-d',strtotime($this->uri->segment(4)));
		$end_date = date('Y-m-d',strtotime($this->uri->segment(5)));

				 $this->db->select('c.transporter_id, c.transport_rate, d.name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('transporter_details d','d.id=c.transporter_id')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('a.transport_owned_hired',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($transporter <> '' && $transporter <> 'ALL') {
				$this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->group_by('c.transporter_id')
						  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$sum=$this->get_type_three_transporter_payment($row->transporter_id, $start_date, $end_date);

				$output[] = array(
			            'labels'=>$sum,
			            'count' =>$sum,
			            'indus'=>$row->name
		            );
			}
		}

		print json_encode($output);

	}

	function get_type_three_transporter_payment($transporter_id, $start_date, $end_date) {
		 $t=array();
	 	 $t[]=0;
			$restey=$this->db->select('b.qty, d.transport_rate')
							 ->from('type_three_product_details a')
							 ->join('type_three_approval c', 'c.id=a.approval_id')
							 ->join('item_delivery_details_type_3 b','a.id=b.approval_detail_id')
							 ->join('item_delivery_type_3 d','d.id=b.delivery_id')
							 ->where('d.transporter_id',$transporter_id)
							 ->get();
			
			if($restey->num_rows()>0)
			{
				$i=1;
				foreach($restey->result() as $rows)
				{
					$t[] = $rows->qty * $rows->transport_rate;
				}
			}

			return array_sum($t);
	}

	function filter_type_three_ledger() {
		$transporter = $this->input->post('transporter');
		$startdate = $this->input->post('startdate');
		$enddate = $this->input->post('enddate');
		redirect(page_url.'Type_reporting/typethreetransporterledger/'.$transporter.'/'.$startdate.'/'.$enddate);

	}

	function all_type_files() {
		$this->load->view('type_reporting/all_type_files');
	}
}