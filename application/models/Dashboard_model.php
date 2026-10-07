<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->model('Lead_model');
		$this->load->model('Salescrm_model', 'salescrm');
		$this->load->model('Master_model','master');
		$this->load->model('Task_model','task');
	}

	public function payment_overdue_module_available()
	{
		$requiredTables = array(
			'order_punch',
			'order_punch_mailing_details',
			'order_punch_tax_details',
			'customer_quotation',
			'customer_detail',
			'store_rack_location',
			'system_users'
		);

		foreach ($requiredTables as $tableName) {
			if (!$this->db->table_exists($tableName)) {
				return false;
			}
		}

		return true;
	}
	
	function delegated_task($user_id){
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$q = $this->db->select('id')->from('delegation_task')->where('task_status','0')->where('yourname',$user_id)->get();
        $report = count($q->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','1');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function po_approval(){
		$rest=$this->db->select('id')->from('purchase_order')->where('approved','0')->get();
       $report =  count($rest->result());

       return $report; 
	   // $data = array('report_count'=>$report);
	// 	$this->db->where('report_id','2');
	// 	$this->db->update('administrator_dashboard',$data);
	}
	
	function helptickets(){
		$query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','14')->where('work_status','0')->get();
		$report =  count($query->result());
	   $data = array('report_count'=>$report);
		$this->db->where('report_id','3');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function escalated_ticket(){
		$helticketid = array('18','17','1','55','2','12','14','50','51','52','53','56');
		$addeddate = date('Y-m-d',strtotime("-4 days"));
		$addeddate1 = $addeddate." 00:00:00";
		$q=$this->db->select('a.id')->from('dynamic_form_data a')->where_in('a.form_id',$helticketid,false)->where('a.work_status','0')->Where('a.ea_status','0')->Where('a.gm_status','0')->where('a.added_on<=',$addeddate1)->get();
		$report= count($q->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','4');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function account_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','1')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','5');
		$this->db->update('administrator_dashboard',$data);
	}
	function service_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','8')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','6');
		$this->db->update('administrator_dashboard',$data);
	}
	function it_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','3')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','7');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function sales_helpticket(){
	     $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','5')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','7');
		$this->db->update('administrator_dashboard',$data);
		
		
	}
	function production_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','9')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','9');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function dispatch_helpticket(){
		$query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','10')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','10');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function ea_gm(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','6')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','11');
		$this->db->update('administrator_dashboard',$data);
	}
	function ea_vm(){
		
	}
	function purchase_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','11')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','13');
		$this->db->update('administrator_dashboard',$data);
	}
	function hr_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','4')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','14');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function unclaimed_payment(){
		 $query = $this->db->select('id')->from('payment_reconciliation')->where('accepted_by','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','15');
		$this->db->update('administrator_dashboard',$data);
	}
	function payment_history(){
		 $query = $this->db->select('id')->from('payment_reconciliation')->where('accepted_by!=','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','16');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function daily_activities_report(){
		$query = $this->db->select('id')->from('staff_daily_reporting')->where('reporting_date',date('Y-m-d'))->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','22');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function leave_application(){
		$query = $this->db->select('id')->from('leave_application')->where('from_loc',date('Y-m-d'))->group_by('employee_id')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','23');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function on_leave_today(){
		$query =$this->db->select('a.id')->from('mark_your_attendance a')->where('a.attendance_date',date('Y-m-d'))->where('a.absent_status','1')->where_not_in('a.employee_id','1')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','25');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function rejection_vs_action(){
		$query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','58')->where('work_status','0')->get();
		 $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','21');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function daily_work_report(){
		 $query = $this->db->select('id')->from('staff_daily_reporting')->where('reporting_date',date('Y-m-d'))->get();
		 $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','22');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function ea_delegated_task($user_id){
		
		$user_id =$this->session->userdata['logged_in']['user_id'];
		if($user_id=='119'){
		$userinfo = array('119','66');
		}
		if($user_id=='63'){
		$userinfo = array('63','67');
		}else{
			$userinfo="";
		}
		$q = $this->db->select('id')->from('delegation_task')->where('task_status','0')->where_in('yourname',$userinfo)->get();
        $report = count($q->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','1');
		$this->db->update('ea_dashboard',$data);
	}
	
	function getDashboardModules($addedModules) {
		$business_location = $this->session->userdata['logged_in']['business_location'];
	    $submodulename = array();
	    if(!empty($addedModules)) {
	    foreach($addedModules as $submodule) {
	    	$submodulename[] = $submodule->id;   
		    }
		}
	    
	    
	    
	    $this->db->select('a.id as moduleid, a.module_name, b.id as submoduleid, b.sub_module_name')
	                      ->from('dashboard_modules a')
	                      ->join('dashboard_sub_modules b', 'a.id=b.module_id');
	                      $this->db->where('b.company_id',$business_location);
	                      if(count($submodulename) > 0) {
	                      $this->db->where_not_in('b.id', $submodulename);
	                      }
	                   $query =   $this->db->get();
	               
        if($query->num_rows() > 0) {
            return $query->result();
        }
	    
	    
	}
	
			function saveDashboardAccess($data) {
    	    $this->db->insert('dashboard_access', $data);
    	    return $this->db->affected_rows();
    	    }
	
        	function deleteDashboardAccess($user_id) {
        	    $this->db->where('userid', $user_id)
	                     ->delete('dashboard_access');
        	}
	
		function getDashboardAccessModules($user_id) {
	    $query = $this->db->select('b.id, b.sub_module_name,a.moduleid,a.submoduleid')
	                      ->from('dashboard_access a')
	                      ->join('dashboard_sub_modules b', 'a.submoduleid=b.id')
	                      ->where('a.userid', $user_id)
	                      ->get();
        if($query->num_rows() > 0) {
            return $query->result();
        }
	}

	function checkForDashboardSubmodules($submoduleid) {
	    $query = $this->db->select('id')
	                      ->from('dashboard_access')
	                      ->where('userid', $_SESSION['logged_in']['user_id'])
	                      ->where('submoduleid', $submoduleid)
	                      ->get();
        return $query->num_rows();
        
	}

	function checkfforanymoduleassigned($ids) {
		 $query = $this->db->select('id')
	                      ->from('dashboard_access')
	                      ->where('userid', $_SESSION['logged_in']['user_id'])
	                      ->where_in('submoduleid',$ids,false)
	                      ->get();
        return $query->num_rows();
		
	}

	function discount_approval_count() {
		$data = array();
		$data[] = 0;

		$sql = $this->db->select('a.id, a.list_price')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 0)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
				if($row->list_price != '' && $row->list_price > 0) {
					$data[] = 1;
				}
			}
		}

		$sql1 = $this->db->select('a.id, a.price')
						 ->from('lead_products a')
						 ->join('leads b', 'b.id=a.lead_id')
						 ->join('store_rack_location c', 'c.id=b.hpcl_company')
						 ->join('presto_instruments e', 'e.id=a.product_id')
						 ->where('a.flag', 0)
						 ->get();

		if($sql1->num_rows() > 0) {
			foreach($sql1->result() as $row1) {
				if($row1->price != '' && $row1->price > 0) {
					$data[] = 1;
				}
			}
		}

		return array_sum($data);
	}
	
	function getsinglePIstep() {
		$leadstats="'0'";
		$leadstatss=0;
		$res=$this->db->select('lead_id')->from('lead_stage')->where('pi_step',1)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row)
			{

				$leadstatss=$row->lead_id;
			}

		}


		return $leadstatss;

	}
			
	function getsingleQuotestep() {
		$leadstatss=0;
		$res=$this->db->select('lead_id')->from('lead_stage')->where('quotation_step',1)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row)
			{

				$leadstatss=$row->lead_id;
			}

		}
		return $leadstatss;
	}


	function getproductdetails($leadid) {
			$prd=array();

			$restey=$this->db->select('a.id, a.competitor_product,a.packsize,a.product_id,a.qty,a.price, b.instruments_name,b.mvalue,b.image, c.shortname')
							 ->from('lead_products a')
							 ->join('presto_instruments b','a.product_id=b.id')
							 ->join('units c', 'c.shortname=b.unit', 'left')
							 ->where('lead_id',$leadid)
							 ->get();
			if($restey->num_rows()>0)
			{
				$i=0;
				foreach($restey->result() as $restey1)
				{
					$prd[$i]['id']=$restey1->id;
					$prd[$i]['name']=$restey1->instruments_name;
					$prd[$i]['product_id']=$restey1->product_id;
					$prd[$i]['qty']=$restey1->qty;
					$prd[$i]['value']=$restey1->price;
					$prd[$i]['image']=$restey1->image;
					$prd[$i]['packsize']=$restey1->shortname;
					$prd[$i]['competitor_product']=$restey1->competitor_product;
				$i++;
				}
			}

			return $prd;
		}

		function getAllLeadStages() {
			$sql = $this->db->select('lead_id, lead_name,icon,user_role,step_type, customization_related')
							->from('lead_stage')
							->order_by('sort_order','ASC')
							->get();

			return $sql->result();
		}

		function getAllLeadStagesofleads() {
			$sql = $this->db->select('lead_id, lead_name,icon,user_role,step_type, customization_related')
							->from('lead_stage')
							->where('quotation_related_steps',0)
							->order_by('sort_order','ASC')
							->get();

			return $sql->result();
		}

		function getAllspareLeadStagesofleads() {
			$sql = $this->db->select('lead_id, lead_name,icon,user_role,step_type, customization_related')
							->from('spare_lead_stage')
							->where('quotation_related_steps',0)
							->order_by('sort_order','ASC')
							->get();

			return $sql->result();
		}

		function getAllLeadStagesofquotation() {
			$sql = $this->db->select('lead_id, lead_name,icon,user_role,step_type, customization_related')
							->from('lead_stage')
							->where('quotation_related_steps',1)
							->order_by('sort_order','ASC')
							->get();

			return $sql->result();
		}

		function getAllspareLeadStagesofquotation() {
			$sql = $this->db->select('lead_id, lead_name,icon,user_role,step_type, customization_related')
							->from('spare_lead_stage')
							->where('quotation_related_steps',1)
							->order_by('sort_order','ASC')
							->get();

			return $sql->result();
		}


		function lead_stage_counts($lead_stage_id) {

			$lead_stage = $lead_stage_id;
			if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}
			$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status ='$lead_stage' AND b.closed=0 $chk GROUP BY b.id");

			return $sql->num_rows();
				
		}

		function lead_stage_counts_in_15days($last15daysdate, $lead_stage_id) {

			$lead_stage = $lead_stage_id;
				$enddate = date('Y-m-d')." 23:59:59";
				$startdate = $last15daysdate." 00:00:00";
				
				$q = $this->db->select('a.id')->from('progress_remarks a')->where('lead_status',$lead_stage_id)->where('a.added_on BETWEEN "'.$startdate. '" and "'.$enddate.'"')->get();
			

			return $q->num_rows();
				
		}

		function lead_stage_user_counts($lead_stage_id) {

			$lead_stage = $lead_stage_id;
			$user_id=$_SESSION['logged_in']['user_id'];

			$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  JOIN lead_assigned_to_team_member e ON a.lead_id=e.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status ='$lead_stage' AND b.closed=0 AND e.member_id='$user_id' GROUP BY b.id");

			return $sql->num_rows();
				
		}

		function userwise_lead_stage_counts($user_id,$lead_stage_id) {

			$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member e ON a.lead_id=e.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND e.member_id='$user_id' AND a.lead_status IN ('$lead_stage_id') GROUP BY b.id ");
			return $sql->num_rows();
				
		}

	function total_leads_byuser($userid) {

		$user_id=$_SESSION['logged_in']['user_id'];
		$getDeadEndLeadStage = $this->salescrm->getDeadEnd_closeEndLeadStage();
		if(count($getDeadEndLeadStage)>0)
		{
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";
		}else
		{
			$dead_end_lead_stage='';
		}
		$userstatus=$this->Lead_model->teamleadersearch($user_id);

				$emp=$this->Lead_model->getteamdetails($user_id);

				$team_member="'" . implode ( "', '", $emp ) . "'";
			$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member e ON a.lead_id=e.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND e.member_id IN($team_member) AND a.lead_status NOT IN ($dead_end_lead_stage) GROUP BY b.id");

		return $sql->num_rows();
	}

			function checkfordead_end_or_clousure($leadstageid)
		{

			$row=$this->db->select('dead_end,conversion_step')->from('lead_stage')->where('lead_id',$leadstageid)->get();
			if($row->num_rows()>0)
			{
				foreach($row->result() as $rows);

				return $rows->dead_end."|".$rows->conversion_step;
			}else
			{
				return '';
			}
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

		function unqualifiedreason($reason)
		{
			$lu=$this->db->select('reason')->from('leads_unqualified_reason')->where('reason_id',$reason)->get();
			if($lu->num_rows()>0)
			{
				foreach($lu->result() as $row);

				return $row->reason;
				
			}else
			{
				return null;
			}

		}

		function quotations_expiring_today_count() {
			$today_date = date('Y-m-d');
			$query = $this->db->select('a.id')
					 	      ->from('customer_quotation a')
					 	      ->join('store_rack_location b','a.company_id=b.id', 'left')
					 	      ->join('customer_detail c','a.customer_id=c.id', 'left')
					 	      ->where('a.validity_date', $today_date)
					 	      ->get();

			return $query->num_rows();
		}
	


	function rejected_quotations_count() {
		$data = array();
		$data[] = 0;

		$sql = $this->db->select('a.id')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('a.flag', 2)
						->get();

		if($sql->num_rows() > 0) {
			$data[] = 1;
		}

		$sql1 = $this->db->select('a.id')
						 ->from('lead_products a')
						 ->join('leads b', 'b.id=a.lead_id')
						 ->join('store_rack_location c', 'c.id=b.hpcl_company')
						 ->join('presto_instruments e', 'e.id=a.product_id')
						 ->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
						 ->where('a.flag', 2)
						 ->get();

		if($sql1->num_rows() > 0) {
			$data[] = 1;
		}

		return array_sum($data);
	}

	function pending_for_order_punch_count() {
		$data = array();
		$data[] = 0;
		$query = $this->db->select('a.id')
						 ->from('customer_quotation a')
						 ->join('store_rack_location b','a.company_id=b.id', 'left')
						 ->join('customer_detail c','a.customer_id=c.id', 'left')
						 ->where('a.order_punch', 0)
						 ->order_by('a.id','DESC')
						 ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$checkIfProductIsApproved = $this->master->checkIfProductIsApproved($row->id);

				if($checkIfProductIsApproved == 0) {
			 		$data[] = 1;
			 	}
			}
		}

		return array_sum($data); 
	}

	function all_orders_count() {
		// $query = $this->db->select('a.id')
		// 				  ->from('order_punch a')
		// 				  ->join('customer_quotation b', 'b.id=a.quotation_id')
		// 				  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
		// 				  ->join('customer_detail d', 'd.id=b.customer_id', 'left')
		// 				  ->where('b.order_punch', 1)
		// 				  ->order_by('a.id','DESC')
		// 		 		  ->get();

		$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('b.order_punch', 1)
				 		  ->get();

		return $query->num_rows();
	}

	function quotations_expiring_today_user_count() {
		$today_date = date('Y-m-d');
		$query = $this->db->select('a.id')
				 	      ->from('customer_quotation a')
				 	      ->join('store_rack_location b','a.company_id=b.id', 'left')
				 	      ->join('customer_detail c','a.customer_id=c.id', 'left')
				 	      ->where('a.added_by', $this->session->userdata['logged_in']['user_id'])
				 	      ->where('a.validity_date', $today_date)
				 	      ->get();

			return $query->num_rows();
	}

	function rejected_quotations_user_count() {
		$data = array();
		$data[] = 0;

		$sql = $this->db->select('a.id')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id', 'left')
						->join('customer_detail d', 'd.id=b.customer_id', 'left')
						->join('presto_instruments e', 'e.id=a.product_id')
						->where('b.added_by', $this->session->userdata['logged_in']['user_id'])
						->where('a.flag', 2)
						->get();

		if($sql->num_rows() > 0) {
			$data[] = 1;
		}

		$sql1 = $this->db->select('a.id')
						 ->from('lead_products a')
						 ->join('leads b', 'b.id=a.lead_id')
						 ->join('store_rack_location c', 'c.id=b.hpcl_company')
						 ->join('presto_instruments e', 'e.id=a.product_id')
						 ->join('system_users f', 'f.user_id=a.approve_reject_by', 'left')
						 ->join('lead_assigned_to_team_member g','g.lead_id=b.id')
						 ->where('g.member_id', $this->session->userdata['logged_in']['user_id'])
						 ->where('a.flag', 2)
						 ->get();

		if($sql1->num_rows() > 0) {
			$data[] = 1;
		}

		return array_sum($data);
	}

	function pending_for_order_punch_user_count() {
		$user_id = $this->session->userdata['logged_in']['user_id'];
		$getConversionLeadStage=$this->getConversionLeadStage();

		$sql=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status = $getConversionLeadStage AND b.closed=0 AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");

		// $data = array();
		// $data[] = 0;
		// $query = $this->db->select('a.id')
		// 				 ->from('customer_quotation a')
		// 				 ->join('store_rack_location b','a.company_id=b.id', 'left')
		// 				 ->join('customer_detail c','a.customer_id=c.id', 'left')
		// 				 ->where('a.order_punch', 0)
		// 				 ->order_by('a.id','DESC')
		// 				 ->get();

		// if($query->num_rows() > 0) {
		// 	foreach($query->result() as $row) {
		// 		$checkIfProductIsApproved = $this->master->checkIfProductIsApproved($row->id);

		// 		if($checkIfProductIsApproved == 0) {
		// 	 		$data[] = 1;
		// 	 	}
		// 	}
		// }

		// return array_sum($data); 

		return $sql->num_rows();
	}

	function all_orders_user_count() {
		$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('b.order_punch', 1)
						  ->where('b.added_by', $this->session->userdata['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();

		return $query->num_rows();
	}

	function todays_followup_count() {
		$cur=date('Y-m-d');
		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";


		$query=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur' $chk GROUP BY a.lead_id");

		return $query->num_rows();
	}

	function missed_followup_count() {
		$data = array();
		$data[] = 0;
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}


			$q=$this->db->query("SELECT a.id,a.next_follow_date FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");

		if($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$lastfollowupdate=$row->next_follow_date;
				//echo $lastfollowupdate; exit;

				if(strtotime($lastfollowupdate)<strtotime(date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
					$data[] = 1;
				}
			}
		}


		return array_sum($data);

	}

	function upcoming_followup_count() {
		$data = array();
		$data[] = 0;
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";


			if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}

		$q=$this->db->query("SELECT a.next_follow_date FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id ORDER BY b.id DESC");

		if($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$lastfollowupdate=$row->next_follow_date;

				if(strtotime($lastfollowupdate)>strtotime(date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
					$data[] = 1;
				}
			}
		}

		return array_sum($data);

	}

	function upcoming_followup_user_count() {
		$data = array();
		$data[] = 0;
		$user_id=$_SESSION['logged_in']['user_id'];
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

		$q=$this->db->query("SELECT a.next_follow_date FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");

		if($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$lastfollowupdate=$row->next_follow_date;

				if($lastfollowupdate>date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
					$data[] = 1;
				}
			}
		}

		return array_sum($data);

	}

	function missed_followup_user_count() {
		$data = array();
		$data[] = 0;
		$user_id=$_SESSION['logged_in']['user_id'];
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

		$q=$this->db->query("SELECT a.next_follow_date FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) AND c.member_id=$user_id GROUP BY b.id ORDER BY b.id DESC");

		if($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$lastfollowupdate=$row->next_follow_date;

				if($lastfollowupdate<date('Y-m-d') && ($lastfollowupdate!=date('Y-m-d')) && $lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01') {
					$data[] = 1;
				}
			}
		}

		return array_sum($data);

	}

	function todays_followup_user_count() {
		$cur=date('Y-m-d');
		$user_id=$_SESSION['logged_in']['user_id'];
		$conversion_lead_stage = $this->getConversionLeadStage();
		$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
		array_push($getDeadEndLeadStage, $conversion_lead_stage);
		$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";


		$query=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) AND a.next_follow_date='$cur' AND c.member_id=$user_id GROUP BY a.lead_id");

		return $query->num_rows();
	}

	function pending_for_ppc_clearance_count() {
		$cur_date=date('Y-m-d');
		$data = array();
		$data[] = 0;
		$sql = $this->db->select('a.id, e.id as collection_id')
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

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date <=', $cur_date)
									  ->get();
					
					if($query->num_rows() > 0) {
						$data[] = 1;
					}
				}
			}

			return array_sum($data);
	}

	function pending_for_ppc_clearance_user_count() {
		$user_id=$_SESSION['logged_in']['user_id'];
		$cur_date=date('Y-m-d');
		$data = array();
		$data[] = 0;
		$sql = $this->db->select('a.id, e.id as collection_id')
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

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date <=', $cur_date)
									  ->get();
					
					if($query->num_rows() > 0) {
						$data[] = 1;
					}
				}
			}

			return array_sum($data);
	}

	function pending_for_ppc_clearance_today_count() {
		$cur_date=date('Y-m-d');
		$data = array();
		$data[] = 0;
		$sql = $this->db->select('a.id, e.id as collection_id')
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

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date', $cur_date)
									  ->get();
					
					if($query->num_rows() > 0) {
						$data[] = 1;
					}
				}
			}

			return array_sum($data);
	}

	function pending_for_ppc_clearance_today_user_count() {
		$user_id=$_SESSION['logged_in']['user_id'];
		$cur_date=date('Y-m-d');
		$data = array();
		$data[] = 0;
		$sql = $this->db->select('a.id, e.id as collection_id')
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

					$query = $this->db->select('id')
									  ->from('customer_cheque_collection_details')
									  ->where('collection_id', $row->collection_id)
									  ->where('cheque_date', $cur_date)
									  ->get();
					
					if($query->num_rows() > 0) {
						$data[] = 1;
					}
				}
			}

			return array_sum($data);
	}

	function getUsername($user_id) {
		$name = '';
		$sql = $this->db->select('first_name, last_name')
						->from('system_users')
						->where('user_id', $user_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
				$name = $row->first_name." ".$row->last_name;
		}

		return $name;
	}


	function lead_quotations_expiring_today_count() {
		$today_date = date('Y-m-d');
		$query = $this->db->select('id')
				 	      ->from('leads')
				 	      ->where('validity_date', $today_date)
				 	      ->get();

			return $query->num_rows();
	}

	function pending_order_for_billing()
	{
			$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.send_so_to_billing', 1)
						  ->where('a.billing', 0)
						  ->where('a.cancelled', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();
	return $query->num_rows();
		
	}

	function pending_for_dispatch_count() {
		$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 1)
						  ->where('a.dispatch', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		return $query->num_rows();
	}

	function checkforassignedmember($lead_id)
		{
			$member='';
			$row=$this->db->select('a.member_id,b.first_name,b.last_name')->from('lead_assigned_to_team_member a')->join('system_users_view b','a.member_id=b.user_id')->where('a.lead_id',$lead_id)->order_by('a.id','DESC')->limit(1)->get();

			if($row->num_rows()>0)
			{
				foreach($row->result() as $rows);

				$member=$rows->first_name." ".$rows->last_name;

			}

				return $member;
		}

	function pending_for_approval_count() {
		// $query = $this->db->select('a.id')
		// 				  ->from('approval_form a')
		// 				  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
		// 				  ->where('a.purchase_entry', 0)
		//  				  ->get();

		// return $query->num_rows();

		$query = $this->db->select('a.id')
						  ->from('approval_form a')
						  //->join('hpcl_location b', 'b.id=a.hpcl_location')
						  // ->where('a.purchase_entry', 0)
						  ->where('a.current_date >=', date('Y-m-d', strtotime(date('Y-m-01'))))
						  ->where('a.current_date <=', date('Y-m-d', strtotime(date('Y-m-t'))))
						  ->get();

		return $query->num_rows();
	}

	function payment_due_today_count() {
		
		$data = array();
		$data[]=0;
		$i=1;

		$this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate')
		->from('inventory a')
		->join('hpcl_location d', 'd.id=a.party');


		$this->db->where_in('a.payment_type','5,6',false);
		$this->db->where('a.pur_payment',0);


		$query =  $this->db->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

				if($row->payment_type==2)
				{
				$payment="Cash";
				$cr_days='';

				}else if($row->payment_type==3)
				{
				$payment="Online";
				$cr_days='';

				}else if($row->payment_type==4)
				{

				$payment="PDC";
				$cr_days='';
				}else if($row->payment_type==5)
				{
				$payment="Credit";
				$cr_days=$row->credit_days;
				}else  
				{
				$payment="Advance";
				$cr_days=$row->credit_days;
				}

			if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
		$data[] = 1;
		}
		}
		}

		return array_sum($data);


	}

	function upcoming_payments_count() {
		
		$data = array();
		$data[]=0;
		$i=1;

		$this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate')
		->from('inventory a')
		->join('hpcl_location d', 'd.id=a.party');


		$this->db->where_in('a.payment_type','5,6',false);
		$this->db->where('a.pur_payment',0);


		$query =  $this->db->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {

				if($row->payment_type==2)
				{
				$payment="Cash";
				$cr_days='';

				}else if($row->payment_type==3)
				{
				$payment="Online";
				$cr_days='';

				}else if($row->payment_type==4)
				{

				$payment="PDC";
				$cr_days='';
				}else if($row->payment_type==5)
				{
				$payment="Credit";
				$cr_days=$row->credit_days;
				}else  
				{
				$payment="Advance";
				$cr_days=$row->credit_days;
				}

			// if(date('Y-m-d')<date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
				$data[] = 1;
		//}
		}
		}

		return array_sum($data);

	}

	function late_payments_countOld() {
		$current_date = date('Y-m-d');
		$data = array();
		$data[] = 0;
		$this->db->select('d.name as vname,c.party,c.bill_no,a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'ALL')
						  {
						  	$this->db->where('c.party',$this->uri->segment(3));
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date < $due_date) {
					$data[] = 1;
				}
			}
		}

		return array_sum($data);
	}

	function pending_for_sample_request()
	{
		$r=$this->db->select('id')->from('sample_to_be_sent')->where('status',0)->get();
		return $r->num_rows();
	}


	function pending_for_trial_request()
	{
		$r=$this->db->select('id')->from('trial_to_be_sent')->where('status',0)->where('assignedperson!=',0)->get();
		return $r->num_rows();
	}

	function pending_for_trial_request_for_user($user_id)
	{
		$r=$this->db->select('id')->from('trial_to_be_sent')->where('status',0)->where('assignedperson',$user_id)->get();
		return $r->num_rows();
	}

	function this_month_purchases_count_for_approval() {
		$data = array();
		$data[] = 0;

		  $sql = $this->db->select('a.id')
						  ->from('purchase_entry a')
						  ->join('vendors b', 'b.id=a.party')
						  ->join('approval_form c', 'c.id=a.approval_id')
						  ->join('hpcl_location d', 'd.id=c.hpcl_location')
						  ->where('a.currentdate >=', date('Y-m-01'))
				 		  ->where('a.currentdate <=', date('Y-m-t'))
				 		  ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				  $query = $this->db->select('a.id')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->join('presto_instruments c', 'c.id=b.product_id')
									->join('units d', 'd.id=a.pack_size')
									->where('a.entry_id', $row->id)
									->get();

					if($query->num_rows() > 0) {
						foreach ($query->result() as $rows) {
							$data[] = 1;
						}
					}
			}
		}

		// echo "<pre>";print_r($data);exit;

		return array_sum($data);
	}

	function type_two_approval_count() {
		  $sql = $this->db->select('a.id')
						  ->from('approval_form_type_two a')
						  ->where('a.current_date >=', date('Y-m-01'))
				 		  ->where('a.current_date <=', date('Y-m-t'))
				 		  ->get();

			return $sql->num_rows();
	}


	function type_two_transportation_count() {
		  $sql = $this->db->select('a.id')
						  ->from('type_two_approval a')
						  ->join(' item_delivery b', 'b.approval_id=a.id')
						  ->where('b.addedOn >=', date('Y-m-01')." 00:00:00")
				 		  ->where('b.addedOn <=', date('Y-m-t')." 23:59:59")
				 		  ->where('a.transportation',2)
				 		  ->where('a.item_delivered',1)
				 		  ->get();

			return $sql->num_rows();
	}
  

  	function type_three_approval_count() {
		  $sql = $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.current_date >=', date('Y-m-01'))
				 		  ->where('a.current_date <=', date('Y-m-t'))
				 		  ->get();

			return $sql->num_rows();
	}


	function type_three_transportation_count() {
		  $sql = $this->db->select('a.id')
						  ->from('type_three_approval a')
						  ->join(' item_delivery_type_3 b', 'b.approval_id=a.id')
						  ->where('b.addedOn >=', date('Y-m-01')." 00:00:00")
				 		  ->where('b.addedOn <=', date('Y-m-t')." 23:59:59")
				 		  ->where('a.transportation',2)
				 		  ->where('a.item_delivered',1)
				 		  ->where('a.transport_owned_hired',2)
				 		  ->get();

			return $sql->num_rows();
	}

	function transporter_pending_payment_type_2()
	{
		 // $this->db->select('a.id')
			// 			  ->from('type_two_approval a')
			// 			  ->join('hpcl_location b', 'b.id=a.hpcl_location')
			// 			  ->join('item_delivery c','c.approval_id=a.id')
			// 			  ->join('system_users d','d.user_id=c.addedBy')
			// 			  ->where('a.item_delivered',1)
			// 			  ->where('a.transportation',2)
			// 			  ->where('c.transporter_payment',0);

			// 		$query = $this->db->get();

		$query=$this->db->select('a.id')->from('approval_product_details_type_two a')->where('a.delivered',1)->where('a.vehicle_type',2)->where('a.transporter_payment',0)->get();

					return $query->num_rows();
			
	}


	function transporter_pending_payment_type_3()
	{
		 $this->db->select('a.id')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',0)
						   ->where('a.transport_owned_hired',2);

					$query = $this->db->get();

					return $query->num_rows();
			
	}
  

  function type_two_approval_count_delivery_pending_type_2() {
		  $sql = $this->db->select('a.id')
						  ->from('approval_product_details_type_two a')
						  // ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  // ->where('a.current_date >=', date('Y-m-01'))
				 		 //  ->where('a.current_date <=', date('Y-m-t'))
				 		  ->where('a.delivered',0)
				 		  ->get();

			return $sql->num_rows();
	}


	function type_two_approval_count_delivery_pending_type_3() {
		  $sql = $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.current_date >=', date('Y-m-01'))
				 		  ->where('a.current_date <=', date('Y-m-t'))
				 		  ->where('a.item_delivered',0)
				 		  ->get();

			return $sql->num_rows();
	}

	function visit_counts()
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->get();
		return $res->num_rows();
	}


	function visit_counts_userwise($userid)
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('added_by',$userid)->get();
		return $res->num_rows();
	}


	function visit_counts_today()
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('create_date',date('Y-m-d'))->get();
		return $res->num_rows();
	}

	function visit_counts_userwise_today($userid)
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('added_by',$userid)->where('create_date',date('Y-m-d'))->get();
		return $res->num_rows();
	}

	function visit_schedule_counts_today()
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('followup_date',date('Y-m-d'))->get();
		return $res->num_rows();

	}


	function visit_schedule_counts_today_user_wise($user_id)
	{
		$res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('followup_date',date('Y-m-d'))->where('added_by',$user_id)->get();
		return $res->num_rows();

	}

	function yourtotaldistributor($user_id)
	{
		$res=$this->db->select('id')->from('distributor')->where('added_by',$user_id)->get();
		return $res->num_rows();

	}


	function this_month_purchases_count() {
		$data = array();
		$data[] = 0;

		  $sql = $this->db->select('a.id')
						  ->from('inventory a')
						  ->where('a.currentdate >=', date('Y-m-01'))
				 		  ->where('a.currentdate <=', date('Y-m-t'))
				 		  ->get();

		return $sql->num_rows();
	}

	function pending_for_pdc_recd_count() {
		$sql = $this->db->select('a.id')
						->from('customer_cheque_details a')
						->join('order_punch b', 'b.id=a.order_id')
						->where('a.received', 0)
						->get();

		return $sql->num_rows();
	}

	function pending_for_pdc_dep_count() {
		$sql = $this->db->select('a.id')
						->from('customer_cheque_details a')
						->join('order_punch b', 'b.id=a.order_id')
						->where('a.deposited', 0)
						->get();

		return $sql->num_rows();
	}

	function order_payment_overdue()
	{
		if (!$this->payment_overdue_module_available()) {
			return 0;
		}

		$d=array();
		$d[]=0;
		$currentday=date('Y-m-d');
		$restey=$this->db->select('id,credit_days,send_to_tally_On')->from('order_punch')->where('payment_type',5)->where('payment',0)->where('send_to_tally',1)->where('send_to_tally_On !=','0000-00-00')->where('cancelled',0)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
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
					$d[]=1;
				}
				
			}
		}

		return array_sum($d); 

	}

	function order_payment_overdue_agent_wise($user_id)
	{
		if (!$this->payment_overdue_module_available()) {
			return 0;
		}

		$d=array();
		$d[]=0;
		$currentday=date('Y-m-d');
		$restey=$this->db->select('id,credit_days,send_to_tally_On')->from('order_punch')->where('payment_type',5)->where('payment',0)->where('send_to_tally',1)->where('send_to_tally_On !=','0000-00-00')->where('cancelled',0)->where('agent',$user_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
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
					$d[]=1;
				}
				
			}
		}

		return array_sum($d); 

	}


	function order_payment_upcoming()
	{
		$d=array();
		$d[]=0;
		$currentday=date('Y-m-d');
		$restey=$this->db->select('id,credit_days,send_to_tally_On')->from('order_punch')->where('payment_type',5)->where('payment',0)->where('send_to_tally',1)->where('send_to_tally_On !=','0000-00-00')->where('cancelled',0)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
				if($row->credit_days!='')
				{
					$creditdays=$row->credit_days;
				}else
				{
					$creditdays=0;
				}

				$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


				$diff = strtotime($expected_payment_days) - strtotime($currentday);
					$days=round($diff / (60 * 60 * 24));


				if($days<11  && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					$d[]=1;
				}
				
			}
		}

		return array_sum($d); 

	}


	function order_payment_upcoming_agent_wise($user_id)
	{
		$d=array();
		$d[]=0;
		$currentday=date('Y-m-d');
		$restey=$this->db->select('id,credit_days,send_to_tally_On')->from('order_punch')->where('payment_type',5)->where('payment',0)->where('send_to_tally',1)->where('send_to_tally_On !=','0000-00-00')->where('cancelled',0)->where('agent',$user_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
				if($row->credit_days!='')
				{
					$creditdays=$row->credit_days;
				}else
				{
					$creditdays=0;
				}

				$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

				$diff = abs(strtotime($expected_payment_days) - strtotime($currentday));

				$diff = strtotime($expected_payment_days) - strtotime($currentday);
					$days=round($diff / (60 * 60 * 24));


				if($days<11  && strtotime($expected_payment_days)>=strtotime($currentday))
				{
					$d[]=1;
				}
				
			}
		}

		return array_sum($d); 

	}

	function orders_on_hold_count() {
		$datas = array();
		$datas[] = 0;
		$query = $this->db->select('a.id, b.id as quotation_id, b.customer_id, f.gst_no as buyer_gst,c.gst as seller_gst,a.payment_type')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 0)
						  ->where('a.cancelled', 0)
						  ->where('a.hold_reject', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$customer_name='';
					$companyname='';
					$order_max_limit=0;

					$data=$this->salescrm->getCustomerdetail($row->customer_id);
					if(count($data)>0)
					{
						$customer_name=$data[0];
						$companyname=$data[1];
						$order_max_limit=$data[2];
					}

					$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$order_amount=$this->salescrm->getunpaid_order_amount($row->customer_id);

					$show=1;
					if($order_amount >= $order_max_limit && $order_max_limit > 0) {
						$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
						
						if($check_remove_hold == 0) {
							$show=0;
						}
					}else
					{

						if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}

					}



				/** PREVIOUS PDC **/
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				}else
				{
				$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				}

				}

				/** END **/



				/** CURRENT PDC **/
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
				$show=0;
				}else
				{
				$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

				$show=0;
				}
				}
				}

				/** END **/



				if($show==0)
				{

						$datas[] = 1;
				}
				}
			}


			return array_sum($datas);
	}


function orders_on_hold_user_count() {
		$datas = array();
		$datas[] = 0;
		$query = $this->db->select('a.id, b.id as quotation_id, b.customer_id, f.gst_no as buyer_gst,c.gst as seller_gst,a.payment_type')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 0)
						  ->where('a.cancelled',0)
						  // ->where('a.agent',$user_id)
						  ->order_by('a.id','DESC')
				 		  ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$customer_name='';
					$companyname='';
					$order_max_limit=0;

					$data=$this->salescrm->getCustomerdetail($row->customer_id);
					if(count($data)>0)
					{
						$customer_name=$data[0];
						$companyname=$data[1];
						$order_max_limit=$data[2];
					}

					$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$order_amount=$this->salescrm->getunpaid_order_amount($row->customer_id);

					$h_type=0;
					$show=1;
					if($order_amount >= $order_max_limit && $order_max_limit > 0) {
						$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
						
						if($check_remove_hold == 0) {
							$show=0;
							$h_type=1;
						}
					}else
					{
						if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
					}



				
				/** PREVIOUS PDC **/
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				$h_type=2;
				}else
				{
				$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				$h_type=3;
				}

				}

				/** END **/



				/** CURRENT PDC **/
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
				$show=0;
				$h_type=2;
				}else
				{
				$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

				$show=0;
				$h_type=3;
				}
				}
				}

				/** END **/



				if($show==0)
				{

					if($h_type==2)
					{
						$datas[] = 1;
					}
				}
				}
			}


			return array_sum($datas);
	}

	function orders_on_hold_user_count_OLDD($user_id) {
		$datas = array();
		$datas[] = 0;
		$query = $this->db->select('a.id, b.id as quotation_id, b.customer_id, f.gst_no as buyer_gst,c.gst as seller_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 0)
						  ->where('a.agent', $user_id)
						  ->order_by('a.id','DESC')
				 		  ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$customer_name='';
					$companyname='';
					$order_max_limit=0;

					$data=$this->salescrm->getCustomerdetail($row->customer_id);
					if(count($data)>0)
					{
						$customer_name=$data[0];
						$companyname=$data[1];
						$order_max_limit=$data[2];
					}

					$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);

					$order_amount=$this->salescrm->getunpaid_order_amount($row->customer_id);

					if($order_amount >= $order_max_limit && $order_max_limit > 0) {
						$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 

						if($check_remove_hold == 0) {
							$datas[] = 1;
						}
					}
				}
			}

			return array_sum($datas);
	}
  


  	function check_for_previous_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('a.order_id !=',$order_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


function check_for_current_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('a.order_id',$order_id)->get();
	return $rest->num_rows();
	
	}


	function check_for_previous_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('a.order_id!=',$order_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


	function check_for_current_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('order_id',$order_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}

	function orders_pending_for_payment()
	{
		$data=array();
		$data[]=0;
		$rest=$this->db->select('id')->from('order_punch')->where('payment',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{

				$row=$this->customer_previous_payment($row->id);
				if($row>0)
				{
					$data[]=1;
				}
			}
		}

		return array_sum($data);

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

	function convence_for_approval()
	{
		
			
		$scheduler_data = array();
		$scheduler_data[]=0;


		for ($l = -6; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-31');

			
			$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
			foreach($q->result() as $q1)
			{

		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{ $scheduler_data[] =1;
		}
		}
	}
}
}
		return array_sum($scheduler_data);
	}

	function density_approval()
	{
			$rowss=$this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->get();

			return $rowss->num_rows();
	}


	function orders_on_hold_count_user() {
		$datas = array();
		$datas[] = 0;
		$query = $this->db->select('a.id, b.id as quotation_id, b.customer_id, f.gst_no as buyer_gst,c.gst as seller_gst,a.payment_type')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 0)
						  ->where('a.cancelled', 0)
						  ->where('a.agent',$_SESSION['logged_in']['user_id'])
						  ->where('a.hold_reject',0)
						  ->order_by('a.id','DESC')
				 		  ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$customer_name='';
					$companyname='';
					$order_max_limit=0;

					$data=$this->salescrm->getCustomerdetail($row->customer_id);
					if(count($data)>0)
					{
						$customer_name=$data[0];
						$companyname=$data[1];
						$order_max_limit=$data[2];
					}

					$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$order_amount=$this->salescrm->getunpaid_order_amount($row->customer_id);

					$show=1;
					if($order_amount >= $order_max_limit && $order_max_limit > 0) {
						$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
						
						if($check_remove_hold == 0) {
							$show=0;
						}
					}



				/** PREVIOUS PDC **/
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				}else
				{
				$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{
				$show=0;
				}

				}

				/** END **/



				/** CURRENT PDC **/
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
				$show=0;
				}else
				{
				$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

				$show=0;
				}
				}
				}

				/** END **/



				if($show==0)
				{

						$datas[] = 1;
				}
				}
			}


			return array_sum($datas);
	}


	function density_approval_user()
	{
			$rowss=$this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->where('a.addedBy',$_SESSION['logged_in']['user_id'])->get();

			return $rowss->num_rows();
	}

	function late_payments_count($s,$e)
	{

		$data = array();
		$data[]=0;
		$i=1;
		$party="ALL";
		$start_date=date('Y-m-d',strtotime($s));
		$end_date=date('Y-m-d',strtotime($e));
		$data = array();
		$i=1;
		         $this->db->select('a.gst,a.interest,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,b.pur_paymentOn')->from('inventory_payment_details b')
		         		  ->join('inventory a','a.id=b.inventory_id')
						  ->join('hpcl_location d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}
						$this->db->where_in('a.payment_type','5,6',false);
						//	$this->db->where('a.pur_payment',1);

						if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
						{
							$this->db->where('b.pur_paymentOn>=',$start_date);
							$this->db->where('b.pur_paymentOn<=',$end_date);
						}


						$this->db->group_by('a.id');
		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);
					//echo "<pre>"; print_r($pdetails); exit; 
					$action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';

						if($row->payment_type==2)
						{
							$payment="Cash";
							$cr_days='';

						}else if($row->payment_type==3)
						{
								$payment="Online";
								$cr_days='';

						}else if($row->payment_type==4)
						{

							$payment="PDC";
							$cr_days='';
						}else if($row->payment_type==5)
						{
							$payment="Credit";
							$cr_days=$row->credit_days;
						}else  
						{
							$payment="Advance";
							$cr_days=$row->credit_days;
						}

			

						$late_pay=$this->check_for_any_payment_late(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days")),$row->id);
					
						if($late_pay>0)
						{
							
									$data[] = 1	;										

		

						$i++;
					}
							
					}
				
				
			}

		


					return array_sum($data);
		

		

	

		
	}



		function check_for_any_payment_late($final_payment_date,$inventory_id)
			{
					$restey=$this->db->select('id')->from('inventory_payment_details')->where('pur_paymentOn>',$final_payment_date)->where('inventory_id',$inventory_id)->get();

					return $restey->num_rows();

			}


	function get_purchase_details($id)
	{
		// <th style="width:80px;">Lot No.</th>
       // <th style="width:100px;">Batch No.</th>
       
        //<th style="width:80px;">Manufacturing Date.</th>
		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
        <th style="width:80px;">Rate</th>
       
        <th style="width:80px;">Total Price</th>
        </tr>
        </thead><tbody>';

        $tot=array();
        $tot[]=0;
		$reste=$this->db->select('a.id, a.product,a.pack_size,a.qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->where('a.inventory_id',$id)->get();
		if($reste->num_rows()>0)
		{
			$i=1;
			foreach($reste->result() as $rows)
			{

				$batch_code = array();
				$report_file = '';

				$sql = $this->db->select('batch_no, report_file')
								->from('inventory_batch_no')
								->where('inv_detail_id', $rows->id)
								->get();


				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row) {
						$batch_code[] = $row->batch_no;

						if($row->report_file <> '') {
							$report_file .= '<a href="'.assets_url.'test_report/'.$row->report_file.'" download>Download</a><br>';
						} else {
							$report_file .= '';
						}
					}
				}

				// echo "<pre>";print_r($batch_code);exit;

				$total=$rows->qty*$rows->rate;
					$html.='<tr>
					<td>'.$i.'</td>
					<td>'.$rows->instruments_name.'</td>
					<td>'.$rows->qty.' '.$rows->shortname.'</td>
					<td>'.$rows->rate.'/'.$rows->shortname.'</td>
					
					<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$total.'</strong></td>
					</tr>';

					$tot[]=$total;
			$i++;
		}


				//<td>'.$rows->lot_no.'</td>
				//<td>'.implode(',<br>', $batch_code).'</td>

				//<td>'.date('d-M-Y',strtotime($rows->manufacturing_date)).'</td>
		}else
		{
			$html.='<tr>
					<td colspan="8">No Product Available</td>
					</tr>';
		}

		$html.='</tbody>
  </table>';

  				return $html."|".array_sum($tot);

	}

	function pending_customer_payment()
	{
		$query=$this->db->select('a.id')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('delivered',1)
					    ->where('a.payment',0);

					    // if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    // {
					    // 	$this->db->where('a.location',$hpcl_location);
					    // }
					    //  if($product<>'ALL' && $product<>'')
					    // {
					    // 	$this->db->where('a.product_id',$product);
					    // }
					   

					  $query=$this->db->get();

				return $query->num_rows();
	}


	  function type_two_approval_count_late_payment_type_2() {
	  	$d=array();
	    $sql = $this->db->select('a.id,a.payment,a.paymentOn,a.credit_days,a.deliveredOn')
						  ->from('approval_product_details_type_two a')
				 		  ->where('a.delivered',1)
				 		  ->where('a.payment',1)
				 		  ->where('a.paymentOn>DATE_ADD(a.deliveredOn, INTERVAL a.credit_days DAY)')
				 		  ->where('a.paymentOn>=',date('Y-m-01'))
				 		  ->where('a.paymentOn<=',date('Y-m-t'))
				 		  ->get();
			return $sql->num_rows();

			
	}

	function transportation_approval_count() {
		  $sql = $this->db->select('a.id')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.current_date >=', date('Y-m-01'))
				 		  ->where('a.current_date <=', date('Y-m-t'))
				 		  ->get();

			return $sql->num_rows();
	}


	function transportation_pending_payment_count() {
		  $sql = $this->db->select('a.id')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.payment',0)
						  ->where('a.transport_done_by',2)
				 		  ->get();

			return $sql->num_rows();
	}


  function unfollow_customer_for_payment()
  {
    $res = 0;
    $rest=$this->db->select('id')->from('order_punch')->where('unfollow_customer',1)->get();
    $query2 = $this->db->select('a.id')
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
              ->get();
    return $query2->num_rows();

  }

  function unfollow_customer_for_user()
  {
    $res = 0;
    $rest=$this->db->select('id')->from('order_punch')->where('unfollow_customer',1)->get();
    $query2 = $this->db->select('a.id')
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
              ->where('a.agent',$_SESSION['logged_in']['user_id'])
              ->get();
    return $query2->num_rows();

  }

  function check_for_previous_payment($customer_id,$current_order_id)
  {
  	$stop=array();
  	$invoice=array();
  	$invoices_data=array();
  	$reste=$this->db->select('a.id,a.credit_days,a.send_to_tally_On as added_on,a.invoice_no')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->where('a.payment',0)->where('a.id!=',$current_order_id)->where('b.customer_id',$customer_id)->where('send_to_tally',1)->get();
  	if($reste->num_rows()>0)
  	{
  		foreach($reste->result() as $row)
  		{
  			$credit_days=$row->credit_days;
  			$billed_date=date('Y-m-d',strtotime($row->added_on));
  			$finalpayabledate=date('Y-m-d',strtotime($billed_date. '+'.$credit_days.' Days'));
  			$current_date=date('Y-m-d');
  			if(strtotime($current_date)>strtotime($finalpayabledate))
  			{
  				$stop[]=1;
  				$invoice[]=$row->invoice_no;
  				$invoices_data[]=$row->invoice_no;

  			}


  		}

  	}


  	$in='';
  	$indata='';
  	if(count($invoice)>0)
  	{
  		$in=implode(',',$invoice);
  		$indata=implode(',',$invoices_data);
  	}


  	return array_sum($stop)."|".$in."|".$indata;

  }


function checkfor_hold_release($order_id,$hold_type)
	{
		
		$restey=$this->db->select('id')->from('order_remove_hold')->where('order_id',$order_id)->where('hold_type',$hold_type)->get();
		return $restey->num_rows();


	}

	function visit_counts_old_today()
	{
		$res=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',date('Y-m-d')." 00:00:00")->where('added_on<=',date('Y-m-d')." 23:59:59")->get();
		return $res->num_rows();
	}

	function visit_counts_userwise_old_today()
	{

		$res=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',date('Y-m-d')." 00:00:00")->where('added_on<=',date('Y-m-d')." 23:59:59")->where('added_by',$_SESSION['logged_in']['user_id'])->get();
		return $res->num_rows();
	}


	function pendingtrailforapproval()
	{
			$rowss = $this->db->select('a.id')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.approved',0)->get();

			return $rowss->num_rows();
	}
	function transporter_pending_payment_outgoing()
	{

						$r=$this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.send_to_tally', 1)
						  ->where('transporter_type',2)
						  ->where('transporter_payment',0)
						  ->where('a.send_to_tally_On>=','2023-04-10')
						  ->get();

						  return $r->num_rows();

	}

	function type_2_3_invoice_this_month()
	{

		$query=$this->db->select('a.id')
					    ->from('type_2_3_invoice a')
					   	->join('hpcl_location c', 'c.id=a.shipping_from')
					   	->join('hpcl_direct_customer e', 'e.id=a.customer')
					    ->where('a.invoice_date>=',date('Y-m-01'))
					    ->where('a.invoice_date<=',date('Y-m-t'))
					    ->get();

					    return $query->num_rows();
	}


	function pending_customer_payment_new()
	{
		$query=$this->db->select('a.id')
					    ->from('type_2_3_invoice_particular a')
					    ->where('a.payment',0)->get();

					    return $query->num_rows();


	}

	function old_quote_for_orders_admin()
	{
		$d=array();
		$d[]=0;
		$this->db->select('a.id');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id', 'left');
		$this->db->join('customer_detail c','a.customer_id=c.id', 'left');
		$this->db->order_by('a.id','DESC');
		$rest=$this->db->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
			$query = $this->db->select('id')
					 		  ->from('order_punch')
					 		  ->where('quotation_id', $row->id)
					 		  ->get();

			if($query->num_rows() == 0) {

				$d[]=1;
										}
		}

	}

		return array_sum($d);
	}

	function old_quote_for_orders_user()
	{
		$d=array();
		$d[]=0;
		$this->db->select('a.id');
		$this->db->from('customer_quotation a');
		$this->db->join('store_rack_location b','a.company_id=b.id', 'left');
		$this->db->join('customer_detail c','a.customer_id=c.id', 'left');
		$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		$this->db->order_by('a.id','DESC');
		$rest=$this->db->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
			$query = $this->db->select('id')
					 		  ->from('order_punch')
					 		  ->where('quotation_id', $row->id)
					 		  ->get();

			if($query->num_rows() == 0) {

				$d[]=1;
										}
		}

	}

	return array_sum($d);

	}

	function orders_on_hold_om()
	{


		$lead_data = array();
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.cancelled', 0)
						  ->where('a.billing', 0)
						
						  // ->where('a.agent',$_SESSION['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {
			
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}


			
			

				$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$invoices_list='';
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$invoices_list=$prv_pay[2];
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



		



		if($show==0 && $h_type==4)
		{
			
	
			$lead_data[] = 1;

			
			 


			$i++;
		}
		}
	}
		
		return array_sum($lead_data);

	}

	function getCustomerdetail($customerid)
	{
		$data=array();
		$r=$this->db->select('customer_name,company_name, order_max_limit,tds_appl,tds_per')->from('customer_detail')->where('id',$customerid)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $row);
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
			$data[]=$row->order_max_limit;
			$data[]=$row->tds_appl;
			$data[]=$row->tds_per;
		}

		return  $data;

	}


function getunpaid_order_amount($customer_id)
	{
		$payment_due_sum=array();
		$query = $this->db->select('a.id,a.quotation_id,e.gst_no,c.gst')
		->from('order_punch a')
		->join('order_punch_mailing_details d','a.id=d.order_id')
		->join('order_punch_tax_details f','a.id=f.order_id')
		->join('lead_source g','g.source_id=a.source','left')
		->join('system_users h','h.user_id=a.agent')
		->join('order_punch_tax_details e','a.id=e.order_id')
		->join('customer_quotation b','b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id', 'left')
		->where('a.payment', 0)
		->where('b.customer_id',$customer_id)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
				$partial=$this->customer_previous_payment($row->id);
				$payment_due=$order_value-$partial;
				$payment_due_sum[]=$payment_due;

			}
		}
		

		return array_sum($payment_due_sum);
	}


	function pending_sales_order_for_billing()
	{
			$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.send_so_to_billing', 0)
						  ->where('a.cancelled', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();
	return $query->num_rows();
		
	}


	function transporter_pending_payment_incoming()
	{

						$r=$this->db->select('a.id')
						  ->from('inventory a')
						  ->where('a.transport_type',2)
						  ->where('a.transporter_payment',0)
						  ->get();

						  return $r->num_rows();

	}

	function min_stock_alert()
	{
		$d=$this->db->select('id')->from('company_wise_inventory')->where('stock<min_stock')->where('min_stock>',0)->get();

		return $d->num_rows();
	}

	function quote_discount_count()
	{
		$lead_data=array();
		$lead_data[]=0;
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
					$lead_data[] =1;
					$i++;
				}
				}
			}


			return array_sum($lead_data);

	}


	function order_discount_count()
	{
		$lead_data=array();
		$lead_data[]=0;
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
					
					$lead_data[] =1;
					$i++;
				}
				}
			}


			return array_sum($lead_data);

	}

	public function paymenttermsapproval(){
		$q = $this->db->select('id')->from('customer_detail')->where('payment_term_approval',0)->get();
		$res = count($q->result());
		return $res;
	}

	function getsettings()

{

$setarray=array();

$sql = $this->db->select('city,mode,port,password,email,outgoing,pass,cc_email,user_name,password,quotation_name,pi_name,account_type')

->from('setting_master')

->where('id',$_SESSION['logged_in']['business_location'])

->get();



if($sql->num_rows() > 0) {

foreach ($sql->result() as $row);

$setarray['mode'] = $row->mode;

$setarray['port'] = $row->port;

$setarray['password'] = $row->password;

$setarray['email_smtp'] = $row->email;

$setarray['email_outgoing']=$row->outgoing;

$setarray['email_pass']=$row->pass;

$setarray['email_ccmailid']=$row->cc_email;

$setarray['whatsappuser']=$row->user_name;

$setarray['whatsapppassword']=$row->password;

$setarray['quotefile']=$row->quotation_name;

$setarray['pifile']=$row->pi_name;

$setarray['bcity']=$row->city;

$setarray['account_type']=$row->account_type;



}



return $setarray;





}

	function yearlyclosedwonorder()
	{
		$company_id=$_SESSION['logged_in']['business_location'];
		$lead_stage=$this->getConversionLeadStage();
	    $income=array();
	    $income[]=0;
            $curr_date_month = date('m');
            $calculate_fiscal_year_for_date = $this->calculateFiscalYearForDate($curr_date_month);
            $cal=explode(':',$calculate_fiscal_year_for_date);
            $startdate = date('Y-m-d',strtotime($cal[0]))." 00:00:00";
            $enddate = date('Y-m-d',strtotime($cal[1]))." 23:59:59";
	  
	    	$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$lead_stage') AND a.added_on BETWEEN '".$startdate. "' and '". $enddate."' AND b.company_id='$company_id' GROUP BY b.id ");
	    	
	    
	    	
	    	if($sql->num_rows()>0)
	    	{
	    	    foreach($sql->result() as $row)
	    	    {
	    	        $lead_id=$row->id;
	    	        $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
	    	        if($lid->num_rows()>0)
	    	        {
	    	            foreach($lid->result() as $incomes)
	    	            {
	    	                
	    	                $price=$incomes->price*$incomes->qty;
	    	                
	    	                if($incomes->discount_type==0)
	    	                {
	    	                    /** % **/
	    	                    
	    	                  $discount=  ($price*$incomes->percent_amt)/100;
	    	                    
	    	                }else if($incomes->discount_type==1)
	    	                {
	    	                    /** fix **/
	    	                    
	    	                    $discount= $incomes->percent_amt;
	    	                    
	    	                }else
	    	                {
	    	                    $discount=0;
	    	                }
	    	                
	    	                
	    	                $income[]=$price-$discount;
	    	                
	    	                
	    	            }
	    	            
	    	        }
	    	        
	    	        
	    	        
	    	        
	    	    }
	    	}
	    	
	    	
	    	
	    	$lastmonthdata=$this->previousyearclosedwon();
	    
	    	   $thismonth=array_sum($income);
	    	   if($thismonth>$lastmonthdata)
	    	   {
	    	    
	    	       $diff=$thismonth-$lastmonthdata;
	    	       
	    	       $percentage=($diff*100)/$thismonth." %";
	    	       $sign="<span style='font-size:12px'><i class='fa fa-arrow-up' ></i>&nbsp;".$percentage."</span>";
	    	       
	    	       
	    	   }else if($thismonth==$lastmonthdata)
	    	   {
	    	        $percentage="0 %";
	    	          $sign="<span style='font-size:12px'><i class='fa fa-arrow-up' style='font-size:12px;'></i>&nbsp;".$percentage."</span>";
	    	       
	    	   }else
	    	   {
	    	       $diff=$lastmonthdata-$thismonth;
	    	       
	    	       $percentage=($diff*100)/$thismonth." %";
	    	      $sign="<span style='font-size:12px'><i class='fa fa-arrow-down' style='font-size:12px;'></i>&nbsp;".$percentage."</span>";
	    	       
	    	   }
	    
	    	
	    	return $this->thousandsCurrencyFormat(array_sum($income)).'|'.$sql->num_rows().'|'.$sign;
	    	
	    	
	  
	    	
	    	
	    
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

function previousyearclosedwon()
{
	$company_id=$_SESSION['logged_in']['business_location'];
	$lead_stage=$this->getConversionLeadStage();
     $curr_date_month = date('m');
    
      $calculate_fiscal_year_for_date = $this->calculateFiscalYearForDate($curr_date_month);
            $cal=explode(':',$calculate_fiscal_year_for_date);
            $startdate = date('Y-m-d',strtotime($cal[0]))." 00:00:00";
            $enddate = date('Y-m-d',strtotime($cal[1]))." 23:59:59";
            
   
    $incomedata=array();
	    $incomedata[]=0;
	   
	  
	    	$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$lead_stage') AND a.added_on BETWEEN '".$startdate. "' and '". $enddate."' AND company_id='$company_id' GROUP BY b.id ");
	    	
	    
	    	
	    	if($sql->num_rows()>0)
	    	{
	    	    foreach($sql->result() as $row)
	    	    {
	    	        $lead_id=$row->id;
	    	        $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
	    	        if($lid->num_rows()>0)
	    	        {
	    	            foreach($lid->result() as $incomes)
	    	            {
	    	                
	    	                $price=$incomes->price*$incomes->qty;
	    	                
	    	                if($incomes->discount_type==0)
	    	                {
	    	                    /** % **/
	    	                    
	    	                  $discount=  ($price*$incomes->percent_amt)/100;
	    	                    
	    	                }else if($incomes->discount_type==1)
	    	                {
	    	                    /** fix **/
	    	                    
	    	                    $discount= $incomes->percent_amt;
	    	                    
	    	                }else
	    	                {
	    	                    $discount=0;
	    	                }
	    	                
	    	                
	    	                $incomedata[]=$price-$discount;
	    	                
	    	                
	    	            }
	    	            
	    	        }
	    	        
	    	        
	    	        
	    	        
	    	    }
	    	}
	    	
            return array_sum($incomedata);
	    	
}

function thousandsCurrencyFormat($num) {


 $explrestunits = "" ;
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.

  // if($num>1000) {

  //       $x = round($num);
  //       $x_number_format = number_format($x);
  //       $x_array = explode(',', $x_number_format);
  //       $x_parts = array('k', 'm', 'b', 't');
  //       $x_count_parts = count($x_array) - 1;
  //       $x_display = $x;
  //       $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
  //       $x_display .= $x_parts[$x_count_parts - 1];

  //       return $x_display;

  // }

  //return $num;
}

	function monthlyclosedwonorder()
	{
		$company_id=$_SESSION['logged_in']['business_location'];
		$lead_stage=$this->getConversionLeadStage();
	    $income=array();
	    $income[]=0;
	    $startdate=date('Y-m-01')." 00:00:00";
	    $enddate=date('Y-m-t')." 23:59:59";
	  
	  if($lead_stage<>'')
	  {
	    	$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($lead_stage) AND a.added_on BETWEEN '".$startdate. "' and '". $enddate."' AND b.company_id='$company_id' GROUP BY b.id ");
	    	
	    
	    	
	    	if($sql->num_rows()>0)
	    	{
	    	    foreach($sql->result() as $row)
	    	    {
	    	        $lead_id=$row->id;
	    	        $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
	    	        if($lid->num_rows()>0)
	    	        {
	    	            foreach($lid->result() as $incomes)
	    	            {
	    	                
	    	                $price=$incomes->price*$incomes->qty;
	    	                
	    	                if($incomes->discount_type==0)
	    	                {
	    	                    /** % **/
	    	                    
	    	                  $discount=  ($price*$incomes->percent_amt)/100;
	    	                    
	    	                }else if($incomes->discount_type==1)
	    	                {
	    	                    /** fix **/
	    	                    
	    	                    $discount= $incomes->percent_amt;
	    	                    
	    	                }else
	    	                {
	    	                    $discount=0;
	    	                }
	    	                
	    	                
	    	                $income[]=$price-$discount;
	    	                
	    	                
	    	            }
	    	            
	    	        }
	    	        
	    	        
	    	        
	    	        
	    	    }
	    	}
	    	
	    	
	    	$lastmonthdata=$this->previousmonthclosedwon();
	    
	    	   $thismonth=array_sum($income);
	    	   if($thismonth>$lastmonthdata)
	    	   {
	    	       $diff=$thismonth-$lastmonthdata;
	    	       
	    	       $percentage=($diff*100)/$thismonth." %";
	    	       $sign="<span style='font-size:12px'><i class='fa fa-arrow-up' ></i>&nbsp;".$percentage."</span>";
	    	       
	    	       
	    	   }else if($thismonth==$lastmonthdata)
	    	   {
	    	        $percentage="0 %";
	    	          $sign="<span style='font-size:12px'><i class='fa fa-arrow-up' style='font-size:12px;'></i>&nbsp;".$percentage."</span>";
	    	       
	    	   }else
	    	   {
	    	       $diff=$lastmonthdata-$thismonth;
	    	       if($thismonth>0){
	    	       	$percentage=($diff*100)/$thismonth." %";
	    	       }else{
	    	       	$percentage= "0 %";
	    	       }
	    	       
	    	       
	    	      $sign="<span style='font-size:12px'><i class='fa fa-arrow-down' style='font-size:12px;'></i>&nbsp;".$percentage."</span>";
	    	       
	    	   }
	    
	    	
	    	return $this->thousandsCurrencyFormat(array_sum($income)).'|'.$sql->num_rows().'|'.$sign;

	    }else
	    {
	    	$percentage="0 %";
	    	          $sign="<span style='font-size:12px'><i class='fa fa-arrow-up' style='font-size:12px;'></i>&nbsp;".$percentage."</span>";
	    	return '0|0|'.$sign;	
	    }
	    	
	    
	}	

	function previousmonthclosedwon()
{
    $company_id=$_SESSION['logged_in']['business_location'];
    $lead_stage=$this->getConversionLeadStage();
    $startdate = date('Y-m-01', strtotime('-1 months'));
    $enddate = date('Y-m-t', strtotime('-1 months'));
    
    $incomedata=array();
	    $incomedata[]=0;
	   
	  
	    	$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$lead_stage') AND a.added_on BETWEEN '".$startdate. "' and '". $enddate."' AND b.company_id='$company_id' GROUP BY b.id ");
	    	
	    
	    	
	    	if($sql->num_rows()>0)
	    	{
	    	    foreach($sql->result() as $row)
	    	    {
	    	        $lead_id=$row->id;
	    	        $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
	    	        if($lid->num_rows()>0)
	    	        {
	    	            foreach($lid->result() as $incomes)
	    	            {
	    	                
	    	                $price=$incomes->price*$incomes->qty;
	    	                
	    	                if($incomes->discount_type==0)
	    	                {
	    	                    /** % **/
	    	                    
	    	                  $discount=  ($price*$incomes->percent_amt)/100;
	    	                    
	    	                }else if($incomes->discount_type==1)
	    	                {
	    	                    /** fix **/
	    	                    
	    	                    $discount= $incomes->percent_amt;
	    	                    
	    	                }else
	    	                {
	    	                    $discount=0;
	    	                }
	    	                
	    	                
	    	                $incomedata[]=$price-$discount;
	    	                
	    	                
	    	            }
	    	            
	    	        }
	    	        
	    	        
	    	        
	    	        
	    	    }
	    	}
	    	
            return array_sum($incomedata);
	    	
}

function getUnqualifiedLeadStage() {
			$res = '';
			$sql = $this->db->select('lead_id')
							->from('lead_stage')
							->where('reason', 1)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row1);
				$res = $row1->lead_id;
			}

			return $res;
		}

function pending_indent_for_review()
{
	$scheduler_data = array();
	$scheduler_data[]=0;
		
		$rest=$this->db->select('a.id')->from('intend_request a')->join('system_users e','e.user_id=a.addedBy')->where('a.approvalstatus','0')->where('a.cancel_status','0')->order_by('a.addedOn','DESC')->group_by('a.indendno')->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{			
				$scheduler_data[] =1;
			$i++;
			}
			}

		return array_sum($scheduler_data);	

}

function getsuperadminuserole(){

	$scheduler_data = array();
		
		$rest=$this->db->select('user_role_id')->from('user_role')->where('isadmin',1)->get();
			if($rest->num_rows()>0)
			{
			$i=1;
			foreach($rest->result() as $restyui1)
			{			
				$scheduler_data[] =$restyui1->user_role_id;
			$i++;
			}
			}

		return $scheduler_data;	
}

function paymentcreditedin15days(){
	$date = new DateTime();
	$date->modify('-15 days');
	$createdate =  $date->format('Y-m-d');
	$startdate = $createdate." 00:00:00";
	$enddate = date('Y-m-d')." 23:59:59";
	$q = $this->db->select('amount_received')->from('task_department_wise_scheduling')->where('paymentstage',1)->where('amount_added_in_record_time BETWEEN "'.$startdate. '" and "'.$enddate.'"')->get();
	$res = $q->result();
	return $res;
}

function paymentinnext15days(){
	$date = new DateTime();
	$date->modify('+15 days');
	$createdate =  $date->format('Y-m-d');
	$enddate = $createdate;
	$startdate = date('Y-m-d');
	 $q = $this->db->select('b.id, b.df_no, b.added_on, b.df_upload, b.added_on')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.paymentstage',1)->where('a.end_date BETWEEN "'. date('Y-m-d', strtotime($startdate)). '" and "'. date('Y-m-d', strtotime($enddate)).'"')->where('b.df_status',0)->get();
	$res = $q->result();
	return $res;
}

function formatIndianNumber($number) {
    $number_parts = explode(".", $number);
    $integer_part = $number_parts[0];
    $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

    // Handle negative numbers
    $negative = '';
    if ($integer_part[0] == '-') {
        $negative = '-';
        $integer_part = substr($integer_part, 1);
    }

    // Split the integer part into 3 digits for the last group and 2 digits thereafter
    $lastThree = substr($integer_part, -3);
    $restUnits = substr($integer_part, 0, -3);
    
    if ($restUnits != '') {
        $lastThree = ',' . $lastThree;
    }

    $result = $negative . preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits) . $lastThree . $decimal_part;
    return $result;
}


// In application/models/Dashboard_model.php

private function apply_spares_followup_stage_exclusion($opportunity_alias = 'op')
{
    $latest_stage_name = "(
        SELECT stage.lead_name
        FROM spare_progress_remarks progress
        INNER JOIN spare_lead_stage stage ON stage.lead_id = progress.lead_stage
        WHERE progress.lead_id = {$opportunity_alias}.opportunity_id
        ORDER BY progress.id DESC
        LIMIT 1
    )";

    $this->db->where(
        "LOWER(TRIM({$latest_stage_name})) NOT IN (
            'cancelled quotation', 'order won', 'lead lost',
            'pending for po', 'po created', 'create pi'
        )",
        null,
        false
    );
}

public function SparesFollowupCounts($type)
{
    // --- 1. Use the correct 'opportunities' table for the spares module ---
    $this->db->from('opportunities op');

    // --- 2. We only care about opportunities that are still 'Open' ---
    $this->db->where('op.status', 'Open');
    $this->apply_spares_followup_stage_exclusion('op');
    
    // --- 3. Apply role-based filtering ---
    // If the user is not a manager/admin, only show their own followups.
    $user_role = $_SESSION['logged_in']['role'];
    if ($user_role != 12 && $user_role != 41) {
        $user_id = $_SESSION['logged_in']['user_id'];
        $this->db->where('op.marketing_person_id', $user_id);
    }

    // --- 4. Efficiently find the latest followup date using a subquery ---
    $latest_followup_subquery = "(
        SELECT spr.next_follow_date 
        FROM spare_progress_remarks spr 
        WHERE spr.lead_id = op.opportunity_id 
        ORDER BY spr.id DESC 
        LIMIT 1
    )";

    // --- 5. Apply the date filter directly in the SQL query ---
    switch ($type) {
        case 1: // Today's Followups
            $this->db->where("$latest_followup_subquery = CURDATE()");
            break;
        case 2: // Missed Followups
            $this->db->where("$latest_followup_subquery < CURDATE()");
            // Also ensure the date is valid and not empty
            $this->db->where("$latest_followup_subquery IS NOT NULL");
            $this->db->where("$latest_followup_subquery !=", '0000-00-00');
            break;
        case 3: // Upcoming Followups
            $this->db->where("$latest_followup_subquery > CURDATE()");
            break;
        default:
            return 0; // Return 0 if type is invalid
    }

    // --- 6. Return the final count directly from the database ---
    return $this->db->count_all_results();
}


function FollowupCounts($type)
{
	$lead_data = array();
	$lead_data[]=0;
		$user_id=$_SESSION['logged_in']['user_id'];
		if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41)
			{
				$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}

			$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
			$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
			array_push($getDeadEndLeadStage, $conversion_lead_stage);
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

			if($type!=1)
			{
		$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");
			}else
			{
				$cur=date('Y-m-d');
				$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.next_follow_date='$cur' AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");
			}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=0;
			$lastfollowupdate=$row->next_follow_date;

			 if($type==2)
			{
				// missed

				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				
				if(strtotime($lastfollowupdate)<strtotime(date('Y-m-d'))) {
					$show=1;
				}

				}
			}else if($type==3)
			{
				// upcoming
				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				if(strtotime($lastfollowupdate)>strtotime(date('Y-m-d'))) {
					$show = 1;
				}
				}
			}else if($type==1)
			{
				// today
				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				if(strtotime($lastfollowupdate)==strtotime(date('Y-m-d')))
				{
				$show=1;
				}
				}
			}else
			{
				$show=0;
			}

			if($show==1)
			{
		
		
			$lead_data[]=1;
				

		}
		}
		}
			
			return array_sum($lead_data);

}

function communication_ticket_system_count(){

$department_id = $this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		$alluserofdepartment = []; // Initialize as an empty array

		if ($_SESSION['logged_in']['adminuser'] == 2) {
		// Check if $department_id is an array and has values
		if (is_array($department_id) && !empty($department_id)) {
		$q = $this->db->select('user_id')
		          ->from('system_users')
		          ->where_in('department_id', $department_id)
		          ->get();

		if ($q->num_rows() > 0) {
		foreach ($q->result() as $rowsss) {
		    $alluserofdepartment[] = $rowsss->user_id;
		}
		}
		} else {
		// Handle the case where $department_id is not an array or is empty
		log_message('error', 'Invalid department_id format or no departments assigned.');
		}
		}

$this->db->select('a.id')
    ->from('communication_ticket_system a')
    ->join('df_release b','a.df_id=b.id','left')
    ->where('a.ticket_status',0)
    ->where('a.df_id >', 0)
    ->where('b.df_status',0)
    ->where('b.on_hold',0);

	if($_SESSION['logged_in']['adminuser']==2)
		{
		
		$this->db->where_in('a.added_by',$alluserofdepartment,false);
		}else if($_SESSION['logged_in']['adminuser']==3){
			$userid = $_SESSION['logged_in']['user_id'];
			$this->db->where('a.added_by',$userid);
			
		}
		$q = $this->db->get();
		$count = $q->num_rows();
		return $count;

}

function communication_ticket_for_you(){

$department_id = $this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		$alluserofdepartment = []; // Initialize as an empty array

		if ($_SESSION['logged_in']['adminuser'] == 2) {
		// Check if $department_id is an array and has values
		if (is_array($department_id) && !empty($department_id)) {
		$q = $this->db->select('user_id')
		          ->from('system_users')
		          ->where_in('department_id', $department_id)
		          ->get();

		if ($q->num_rows() > 0) {
		foreach ($q->result() as $rowsss) {
		    $alluserofdepartment[] = $rowsss->user_id;
		}
		}
		} else {
		// Handle the case where $department_id is not an array or is empty
		log_message('error', 'Invalid department_id format or no departments assigned.');
		}
		}

$this->db->select('a.id')
    ->from('communication_ticket_system a')
    ->join('df_release b','a.df_id=b.id','left')
    ->where('a.ticket_status',0)
    ->where('a.df_id >', 0)
    ->where('b.df_status',0)
    ->where('b.on_hold',0);

	if($_SESSION['logged_in']['adminuser']==2)
		{
		
		$this->db->where_in('a.user_id',$alluserofdepartment,false);
		
		}else if($_SESSION['logged_in']['adminuser']==3){
			$userid = $_SESSION['logged_in']['user_id'];
			$this->db->where('a.user_id',$userid);
			
		}
		$q = $this->db->get();
		$count = $q->num_rows();
		return $count;	
}


function FollowupCountsUsers($type,$user_id)
{
	$lead_data = array();
	$lead_data[]=0;
		$user_id=$user_id;
		
			$chk=" AND b.added_by=".$user_id;
			

			$conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
			$getDeadEndLeadStage = $this->salescrm->getDeadEndLeadStage();
			array_push($getDeadEndLeadStage, $conversion_lead_stage);
			$dead_end_lead_stage = "'" . implode ( "', '", $getDeadEndLeadStage ) . "'";

			if($type!=1)
			{
		$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");
			}else
			{
				$cur=date('Y-m-d');
				$resty=$this->db->query("SELECT a.lead_status,a.next_follow_date,b.added_by as leadmanager,c.company_name as mastercompanyname,b.machine_type,b.distributor,a.visitdate, a.visittime, b.alt_contact, b.designation, b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id LEFT JOIN customer_detail c ON c.id=b.company_name WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.next_follow_date='$cur' AND a.lead_status NOT IN ($dead_end_lead_stage) $chk GROUP BY b.id  ORDER BY b.id DESC");
			}
		
		$i=1;
		if($resty->num_rows() > 0) {
		foreach($resty->result() as $row) {
			$show=0;
			$lastfollowupdate=$row->next_follow_date;

			 if($type==2)
			{
				// missed

				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				
				if(strtotime($lastfollowupdate)<strtotime(date('Y-m-d'))) {
					$show=1;
				}

				}
			}else if($type==3)
			{
				// upcoming
				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				if(strtotime($lastfollowupdate)>strtotime(date('Y-m-d'))) {
					$show = 1;
				}
				}
			}else if($type==1)
			{
				// today
				if($lastfollowupdate<>'0000-00-00' && $lastfollowupdate<>'1970-01-01')
				{
				if(strtotime($lastfollowupdate)==strtotime(date('Y-m-d')))
				{
				$show=1;
				}
				}
			}else
			{
				$show=0;
			}

			if($show==1)
			{
		
		
			$lead_data[]=1;
				

		}
		}
		}
		return array_sum($lead_data);

}

// In application/models/Dashboard_model.php

public function SparesFollowupCountsUsers($type, $user_id)
{
    // Use the correct 'opportunities' table for the spares module
    $this->db->from('opportunities op');

    // We only care about opportunities that are still 'Open'
    $this->db->where('op.status', 1);
    $this->apply_spares_followup_stage_exclusion('op');
    
    // Filter by the specific user
    $this->db->where('op.marketing_person_id', $user_id);

    // This subquery efficiently finds the most recent next_follow_date for each opportunity
    $latest_followup_subquery = "(
        SELECT spr.next_follow_date 
        FROM spare_progress_remarks spr 
        WHERE spr.lead_id = op.opportunity_id 
        ORDER BY spr.id DESC 
        LIMIT 1
    )";

    // Apply the correct date filter directly in the SQL query for maximum efficiency
    switch ($type) {
        case 1: // Today's Followups
            $this->db->where("$latest_followup_subquery = CURDATE()");
            break;
        case 2: // Missed Followups
            $this->db->where("$latest_followup_subquery < CURDATE()");
            // Also ensure the date is valid and not empty
            $this->db->where("$latest_followup_subquery IS NOT NULL");
            $this->db->where("$latest_followup_subquery !=", '0000-00-00');
            break;
        case 3: // Upcoming Followups
            $this->db->where("$latest_followup_subquery > CURDATE()");
            break;
    }

    // Return the final count directly from the database
    return $this->db->count_all_results();
}
public function get_active_dfs() {
    $this->load->helper('df_delay');
    $today = date('Y-m-d');
    $dfDelaySql = df_open_overdue_sql($this->db, 't_delay', $today, 'a');
    $dfDelayDaysSql = df_open_overdue_sql($this->db, 't_oldest', $today, 'a');

    $this->db->select('a.id, a.df_no, a.df_description, a.added_on, a.df_upload, a.on_hold, MAX(b.po_attachment) AS po_attachment,
        (SELECT COUNT(*) FROM task_department_wise_scheduling t_open WHERE t_open.df_id=a.id AND t_open.task_status IN (0,2)) AS open_task_count,
        (SELECT COUNT(*) FROM task_department_wise_scheduling t_delay WHERE t_delay.df_id=a.id AND ' . $dfDelaySql . ') AS overdue_task_count,
        (SELECT COUNT(*) FROM task_department_wise_scheduling t_approval WHERE t_approval.df_id=a.id AND t_approval.task_status=2) AS pending_approval_count,
        (SELECT GREATEST(0, DATEDIFF(CURDATE(), MIN(t_oldest.end_date))) FROM task_department_wise_scheduling t_oldest WHERE t_oldest.df_id=a.id AND ' . $dfDelayDaysSql . ') AS max_delay_days,
        (SELECT MIN(t_next.end_date) FROM task_department_wise_scheduling t_next WHERE t_next.df_id=a.id AND t_next.task_status IN (0,2) AND t_next.end_date>=CURDATE()) AS next_due_date', false);
    $this->db->from('df_release a');
    $this->db->join('poreceived b', 'a.id = b.df_id', 'left');

    // Use the same portfolio scope as "All Running DFs": every non-hold running DF.
    // DFs without open tasks remain visible as 0 open / 0 delayed and On Track.
    $this->db->where('a.df_status', 0);
    $this->db->where('IFNULL(a.on_hold, 0) = 0', null, false);

    // Explicit grouping keeps the query compatible with ONLY_FULL_GROUP_BY SQL mode.
    $this->db->group_by(array('a.id', 'a.df_no', 'a.df_description', 'a.added_on', 'a.df_upload', 'a.on_hold'));
    $this->db->order_by('max_delay_days', 'DESC');
    $this->db->order_by('overdue_task_count', 'DESC');
    $this->db->order_by('a.df_no', 'ASC');

    $query = $this->db->get();
    return $query->result_array();
}

     public function get_tasks_by_df($df_id, $departmentid) {

     	$query = $this->db->select('a.id, a.end_date, a.task_completed_on, a.task_status, b.task_name, c.first_name, c.last_name')
                  ->from('task_department_wise_scheduling a')
                  ->join('task_management b', 'a.taskid = b.task_id')
                  ->join('system_users c', 'a.assigned_user = c.user_id')
                  ->where('a.df_id', $df_id)
                  ->where('a.department_id', $departmentid)
                  ->order_by("STR_TO_DATE(a.end_date, '%Y-%m-%d')", 'asc') // Adjust format as per your DB
                  ->get();



        return $query->result();
    }

         public function get_tasks_only_df_wise($df_id) {

		$query = $this->db
			->select('a.id, a.taskid, a.end_date, a.task_completed_on, a.task_status, b.task_name, b.sortorder AS task_order, c.first_name, c.last_name, a.remarks, e.taskupdatedontime, f.department as department_name')
			->from('task_department_wise_scheduling a')
			->join('task_management b', 'a.taskid=b.task_id')
			->join('system_users c', 'a.assigned_user=c.user_id', 'left')
			->join('task_pending_status e', 'a.id=e.recordid', 'left')
			->join('departments f', 'a.department_id=f.department_id', 'left')
			->where('a.df_id', (int) $df_id)
			->order_by('b.sortorder', 'asc')
			->order_by('a.end_date', 'asc')
			->get();



        return $query->result();
    }

     public function get_task_completion_percentage($project_id) {
        $this->db->select('COUNT(*) as total_tasks');
        $this->db->from('task_department_wise_scheduling');
        $this->db->where('df_id', $project_id);
        $total_tasks_query = $this->db->get();
        $total_tasks = $total_tasks_query->row()->total_tasks;

        $this->db->select('COUNT(*) as completed_tasks');
        $this->db->from('task_department_wise_scheduling');
        $this->db->where('df_id', $project_id);
        $this->db->where('task_completed_on !=', '0000-00-00 00:00:00');
        $completed_tasks_query = $this->db->get();
        $completed_tasks = $completed_tasks_query->row()->completed_tasks;

        $completion_percentage = 0;
        if ($total_tasks > 0) {
            $completion_percentage = ($completed_tasks / $total_tasks) * 100;
        }

        return round($completion_percentage)."%";
    }


    public function auto_update_process() {
    // Use a transaction to ensure all updates succeed or none do
    $this->db->trans_start();

    // --- Part 1: Handle the Special Cascade Rule for Task ID 103 ---
    // First, find any active DFs where task 103 was completed but other tasks are still pending.
    $trigger_sql = "
        SELECT 
            t103.df_id, 
            t103.task_completed_on
        FROM 
            task_department_wise_scheduling AS t103
        WHERE 
            t103.taskid = 103
            AND t103.task_status = 1
            AND t103.task_completed_on IS NOT NULL
            AND t103.task_completed_on != '0000-00-00 00:00:00'
            -- Subquery to ensure the DF itself is still active (status 0)
            AND EXISTS (
                SELECT 1 FROM df_release df 
                WHERE df.id = t103.df_id AND df.df_status = 0
            )
            -- Subquery to ensure other pending tasks actually exist to be closed
            AND EXISTS (
                SELECT 1 FROM task_department_wise_scheduling other_tasks
                WHERE other_tasks.df_id = t103.df_id
                AND other_tasks.task_status = 1
                AND (other_tasks.task_completed_on IS NULL OR other_tasks.task_completed_on = '0000-00-00 00:00:00')
            )
    ";
    $triggered_dfs = $this->db->query($trigger_sql)->result();

    // Loop through each triggered DF and cascade the completion
    foreach ($triggered_dfs as $df) {
        $update_data = [
            'task_completed_on' => $df->task_completed_on, // Use the same completion time
            'remarks' => 'Auto-completed due to Task 103 completion.'
        ];

        // Find and update all other pending tasks for this DF
        $this->db->where('df_id', $df->df_id);
        $this->db->where('task_status', 1);
        $this->db->group_start();
            $this->db->where('task_completed_on', '0000-00-00 00:00:00');
            $this->db->or_where('task_completed_on IS NULL');
        $this->db->group_end();

        $this->db->update('task_department_wise_scheduling', $update_data);
    }

    // --- Part 2: Auto-close DFs where ALL tasks are complete ---
    // This runs *after* the cascade logic above.
    
    // Find active DFs (status 0) that have NO pending tasks left.
    $closable_sql = "
        SELECT id FROM df_release 
        WHERE df_status = 0 
        AND NOT EXISTS (
            SELECT 1 FROM task_department_wise_scheduling
            WHERE df_id = df_release.id
            AND task_status = 1
            AND (task_completed_on = '0000-00-00 00:00:00' OR task_completed_on IS NULL)
        )
    ";
    $closable_dfs_result = $this->db->query($closable_sql)->result_array();

    if (!empty($closable_dfs_result)) {
        $df_ids_to_close = array_column($closable_dfs_result, 'id');

        // ⚠️ IMPORTANT: Replace '2' with your actual status ID for a "Closed" DF.
        $this->db->where_in('id', $df_ids_to_close);
        $this->db->update('df_release', [
            'df_status' => 1,
        ]);
    }

    // Complete the transaction
    $this->db->trans_complete();

    // Optional: Log the outcome
    if ($this->db->trans_status() === FALSE) {
        log_message('error', 'DF auto-update transaction failed.');
    } else {
        $closed_count = isset($df_ids_to_close) ? count($df_ids_to_close) : 0;
        log_message('info', 'DF auto-update process ran successfully. DFs auto-closed: ' . $closed_count);
        echo "Process complete. DFs auto-closed: " . $closed_count;
    }
}

	public function get_spares_create_pi_stage_id()
	{
		$stage = $this->db->query(
			"SELECT lead_id
			 FROM spare_lead_stage
			 WHERE lead_id = 15 OR LOWER(TRIM(lead_name)) = ?
			 ORDER BY CASE WHEN lead_id = 15 THEN 0 ELSE 1 END, lead_id ASC
			 LIMIT 1",
			array('create pi')
		)->row();

		return (int) ($stage->lead_id ?? 15);
	}

	public function get_spares_pi_ready_stage_id()
	{
		$stage = $this->db->query(
			"SELECT lead_id
			 FROM spare_lead_stage
			 WHERE lead_id = 8
			    OR LOWER(TRIM(lead_name)) IN (?, ?, ?)
			 ORDER BY CASE WHEN lead_id = 8 THEN 0 ELSE 1 END, lead_id ASC
			 LIMIT 1",
			array(
				'quotation shared & following up',
				'quotation shared & followup',
				'quotation shared & follow-up'
			)
		)->row();

		return (int) ($stage->lead_id ?? 8);
	}

	public function get_spares_create_pi_count($apply_user_filter = true)
	{
		$ready_stage_id = $this->get_spares_pi_ready_stage_id();
		$user_filter_sql = '';

		if (
			$apply_user_filter &&
			$_SESSION['logged_in']['role'] != 12 &&
			$_SESSION['logged_in']['role'] != 41 &&
			$_SESSION['logged_in']['user_id'] != 114 &&
			$_SESSION['logged_in']['user_id'] != 189 &&
			$_SESSION['logged_in']['user_id'] != 111
		) {
			$user_filter_sql = " AND op.added_by = " . (int) $_SESSION['logged_in']['user_id'];
		}

		$pi_filter_sql = '';
		if ($this->db->table_exists('spare_proforma_invoices')) {
			$pi_filter_sql = " AND NOT EXISTS (
				SELECT 1
				FROM spare_proforma_invoices spi
				WHERE spi.opportunity_id = op.opportunity_id
			)";
		}

		$sql = $this->db->query(
			"SELECT op.opportunity_id
			 FROM opportunities op
			 WHERE (
				SELECT spr.lead_stage
				FROM spare_progress_remarks spr
				WHERE spr.lead_id = op.opportunity_id
				ORDER BY spr.id DESC
				LIMIT 1
			 ) = {$ready_stage_id}
			 AND EXISTS (
				SELECT 1
				FROM quotations q
				WHERE q.opportunity_id = op.opportunity_id
			 )
			 {$pi_filter_sql}
			 {$user_filter_sql}
			 GROUP BY op.opportunity_id"
		);

		return $sql->num_rows();
	}

	function spares_lead_stage_counts($lead_stage_id) {

				if ((int) $lead_stage_id === $this->get_spares_create_pi_stage_id()) {
					return $this->get_spares_create_pi_count(true);
				}

				$lead_stage = $lead_stage_id;
				if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41 && $_SESSION['logged_in']['user_id']!=114 && $_SESSION['logged_in']['user_id']!=189 && $_SESSION['logged_in']['user_id']!=111)
				{
					$chk=" AND b.added_by=".$_SESSION['logged_in']['user_id'];
			}else
			{
				$chk='';
			}
			$sql = $this->db->query("SELECT b.opportunity_id FROM spare_progress_remarks a JOIN opportunities b ON a.lead_id=b.opportunity_id WHERE a.id IN (SELECT MAX(id) FROM spare_progress_remarks GROUP BY lead_id) AND a.lead_stage ='$lead_stage' $chk GROUP BY b.opportunity_id");

			return $sql->num_rows();
				
		}


	function spares_lead_stage_counts_app($lead_stage_id) {

				if ((int) $lead_stage_id === $this->get_spares_create_pi_stage_id()) {
					return $this->get_spares_create_pi_count(false);
				}

				$lead_stage = $lead_stage_id;
				
				$sql = $this->db->query("SELECT b.opportunity_id FROM spare_progress_remarks a JOIN opportunities b ON a.lead_id=b.opportunity_id WHERE a.id IN (SELECT MAX(id) FROM spare_progress_remarks GROUP BY lead_id) AND a.lead_stage ='$lead_stage'  GROUP BY b.opportunity_id");

			return $sql->num_rows();
				
		}

// In application/models/Dashboard_model.php

// In application/models/Dashboard_model.php

public function get_followup_opportunities($type, $user_id_filter = NULL)
{
    // Subqueries remain the same
    $latest_stage_subquery = "(SELECT sls.lead_name FROM spare_progress_remarks spr JOIN spare_lead_stage sls ON sls.lead_id = spr.lead_stage WHERE spr.lead_id = op.opportunity_id ORDER BY spr.id DESC LIMIT 1)";
    $latest_quotation_subquery = "(SELECT q.quotation_id FROM quotations q WHERE q.opportunity_id = op.opportunity_id ORDER BY q.quotation_id DESC LIMIT 1)";
    $latest_followup_subquery = "(SELECT spr.next_follow_date FROM spare_progress_remarks spr WHERE spr.lead_id = op.opportunity_id ORDER BY spr.id DESC LIMIT 1)";

    $this->db->select("
        op.opportunity_id, 
        op.op_no, 
        op.op_date, 
        op.status,
        op.probability,
        cm.company_name, 
        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
        ({$latest_stage_subquery}) as current_stage_name,
        ({$latest_quotation_subquery}) as latest_quotation_id,
        ({$latest_followup_subquery}) as next_follow_date
    ");

    $this->db->from('opportunities op');
    $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = op.marketing_person_id', 'left');
    
    $this->db->where('op.status', 'Open');
    $this->apply_spares_followup_stage_exclusion('op');

    // User Filtering remains the same
    if ($user_id_filter) {
        $this->db->where('op.marketing_person_id', $user_id_filter);
    } else {
        $user_role = $_SESSION['logged_in']['role'];
        if ($user_role != 12 && $user_role != 41 && $_SESSION['logged_in']['user_id']!=114 && $_SESSION['logged_in']['user_id']!=189) {
            $user_id = $_SESSION['logged_in']['user_id'];
            $this->db->where('op.marketing_person_id', $user_id);
        }
    }

    // --- FINAL CORRECTION: Combine the entire condition into a single string ---
    // This prevents any part of the subquery or its conditions from being incorrectly escaped.
    switch ($type) {
        case 1: // Today's
            $this->db->where("({$latest_followup_subquery}) = CURDATE()", NULL, FALSE);
            break;
        case 2: // Missed
            $this->db->where("({$latest_followup_subquery}) < CURDATE()", NULL, FALSE);
            $this->db->where("({$latest_followup_subquery}) IS NOT NULL", NULL, FALSE);
            $this->db->where("({$latest_followup_subquery}) != '0000-00-00'", NULL, FALSE);
            break;
        case 3: // Upcoming
            $this->db->where("({$latest_followup_subquery}) > CURDATE()", NULL, FALSE);
            break;
    }

    // Order by clause remains the same
    if ($type == 2) {
        $this->db->order_by('next_follow_date', 'ASC');
    } else {
        $this->db->order_by('next_follow_date', 'ASC');
    }

    return $this->db->get()->result();
}


public function get_spares_sales_by_agent()
{
    // This function calculates the total value of "won" orders for each sales agent.
    $this->db->select("SUM(so.order_value) as total_sales, CONCAT(u.first_name, ' ', u.last_name) as agent_name");
    $this->db->from('spares_orders so');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id');
    $this->db->group_by('so.marketing_person_id');
    $this->db->order_by('total_sales', 'DESC');
    
    return $this->db->get()->result();
}


public function get_spares_top_products_by_value()
{
    // This function finds the top 5 selling products by total value from confirmed orders.
    $this->db->select("SUM(pop.total) as total_product_value, p.code as product_name");
    $this->db->from('po_products pop');
    $this->db->join('spares_orders so', 'so.po_id = pop.po_id'); // Join with spares_orders to count only WON orders
    $this->db->join('spare_parts p', 'p.id = pop.product_id', 'left');
    $this->db->group_by('pop.product_id');
    $this->db->order_by('total_product_value', 'DESC');
    $this->db->limit(5);

    return $this->db->get()->result();
}

public function get_running_orders_count()
{
    $this->db->from('spares_orders so');
    $this->db->where_in('so.status', ['Running', 'In Production', 'Dispatched']);

    // Apply role-based filtering for salespersons
    $user_role = $_SESSION['logged_in']['role'];
    if ($user_role != 12 && $user_role != 41) { // Not an admin/manager
        $user_id = $_SESSION['logged_in']['user_id'];
        $this->db->where('so.marketing_person_id', $user_id);
    }

    return $this->db->count_all_results();
}

public function get_recent_running_orders($limit = 5)
{
    $this->db->select("
        so.order_id,
        so.order_date,
        c.company_name,
        po.po_no
    ");
    $this->db->from('spares_orders so');
    $this->db->join('spares_customers c', 'c.customer_id = so.customer_id', 'left');
    $this->db->join('purchase_orders po', 'po.po_id = so.po_id', 'left');

    $this->db->where_in('so.status', ['Running', 'In Production', 'Dispatched']);

    // Apply role-based filtering for salespersons
    $user_role = $_SESSION['logged_in']['role'];
    if ($user_role != 12 && $user_role != 41 && $_SESSION['logged_in']['user_id']!=114 && $_SESSION['logged_in']['user_id']!=189) { // Not an admin/manager
        $user_id = $_SESSION['logged_in']['user_id'];
        $this->db->where('so.marketing_person_id', $user_id);
    }

    $this->db->order_by('so.order_date', 'DESC');
    $this->db->limit($limit);
    
    return $this->db->get()->result();
}

// In application/models/Dashboard_model.php

public function getAllServiceLeadStages() {
    return $this->db->order_by('sort_order', 'ASC')->get('service_lead_stages')->result();
}

public function getServiceStageCounts($stage_id) {
    // Counts how many leads are currently sitting in this stage
    $this->db->where('current_stage_id', $stage_id);
    $this->db->from('service_opportunities');
    return $this->db->count_all_results();
}

public function getServiceFollowupCounts($type, $user_id = NULL) {
    // 1: Today, 2: Missed, 3: Upcoming
    $today = date('Y-m-d');
    $this->db->from('service_opportunities so');
    $this->db->join(
        'service_progress_history followup_history',
        'followup_history.history_id = (
            SELECT MAX(latest_followup.history_id)
            FROM service_progress_history latest_followup
            WHERE latest_followup.opportunity_id = so.opportunity_id
        )',
        'inner',
        false
    );
    $this->db->join('service_lead_stages current_stage', 'current_stage.stage_id = so.current_stage_id', 'left');
    $this->db->where('followup_history.next_follow_date IS NOT NULL', null, false);
    $this->db->where(
        "LOWER(TRIM(current_stage.stage_name)) NOT IN ('cancelled quotation', 'po received', 'order won', 'create pi')",
        null,
        false
    );

    if ($user_id) {
        $this->db->where('so.marketing_person_id', $user_id);
    }

    if ($type == 1) {
        $this->db->where('followup_history.next_follow_date', $today);
    } elseif ($type == 2) {
        $this->db->where('followup_history.next_follow_date <', $today);
    } elseif ($type == 3) {
        $this->db->where('followup_history.next_follow_date >', $today);
    }

    return $this->db->count_all_results();
}


public function getServiceFollowupCountsNew($type, $user_id = NULL) {
    return $this->getServiceFollowupCounts($type, $user_id);
}


public function get_spare_pipeline_countsNew($role,$user_id,$department_id)
{
 
    /// 🔥 SAME CONDITION AS YOUR FUNCTION
    if ($role != 12 && $role != 41 && $user_id != 114 && $user_id != 189 && $user_id != 111) {
        $chk = " AND b.added_by = $user_id ";
    } else {
        $chk = "";
    }

    $sql = "
        SELECT 
            s.lead_id AS stage_id,
            s.lead_name AS stage_name,
            s.show_in_app AS show_front,
            s.icon,
            COUNT(DISTINCT b.opportunity_id) AS count

        FROM spare_lead_stage s

        LEFT JOIN spare_progress_remarks a 
            ON a.lead_stage = s.lead_id
            AND a.id IN (
                SELECT MAX(id) 
                FROM spare_progress_remarks 
                GROUP BY lead_id
            )

        LEFT JOIN opportunities b 
            ON a.lead_id = b.opportunity_id
            $chk

        WHERE s.quotation_related_steps = 0

        GROUP BY s.lead_id
        ORDER BY s.sort_order ASC
    ";

    return $this->db->query($sql)->result_array();
}

public function get_spare_followup_countsNew($user_role,$user_id,$department_id)
{
 
    /// 🔥 ROLE FILTER
    $user_filter = "";
    if ($user_role != 12 && $user_role != 41) {
        $user_filter = " AND op.marketing_person_id = $user_id ";
    }

    /// 🔥 LATEST FOLLOWUP SUBQUERY
    $latest_followup = "(
        SELECT spr.next_follow_date 
        FROM spare_progress_remarks spr 
        WHERE spr.lead_id = op.opportunity_id 
        ORDER BY spr.id DESC 
        LIMIT 1
    )";

    $latest_stage = "(
        SELECT LOWER(TRIM(sls.lead_name))
        FROM spare_progress_remarks spr_stage
        INNER JOIN spare_lead_stage sls ON sls.lead_id = spr_stage.lead_stage
        WHERE spr_stage.lead_id = op.opportunity_id
        ORDER BY spr_stage.id DESC
        LIMIT 1
    )";

    $sql = "
        SELECT 
            SUM(CASE 
                WHEN $latest_followup = CURDATE() THEN 1 
                ELSE 0 
            END) AS today,

            SUM(CASE 
                WHEN $latest_followup < CURDATE() 
                     AND $latest_followup IS NOT NULL 
                     AND $latest_followup != '0000-00-00'
                THEN 1 
                ELSE 0 
            END) AS missed,

            SUM(CASE 
                WHEN $latest_followup > CURDATE() THEN 1 
                ELSE 0 
            END) AS upcoming

        FROM opportunities op
        WHERE op.status = 'Open'
        AND $latest_stage NOT IN (
            'cancelled quotation', 'order won', 'lead lost',
            'pending for po', 'po created', 'create pi'
        )
        $user_filter
    ";

    return $this->db->query($sql)->row_array();
}

}
