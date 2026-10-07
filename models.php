<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
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
	   $data = array('report_count'=>$report);
		$this->db->where('report_id','2');
		$this->db->update('administrator_dashboard',$data);
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
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','18')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','5');
		$this->db->update('administrator_dashboard',$data);
	}
	function service_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','17')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','6');
		$this->db->update('administrator_dashboard',$data);
	}
	function it_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','2')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','7');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function sales_helpticket(){
		 $query = $this->db->select('id')->from('sales_support')->where('task_status','1')->get();
         $report =  count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','8');
		$this->db->update('administrator_dashboard',$data);
	}
	function production_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','51')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','9');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function dispatch_helpticket(){
		$query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','52')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','10');
		$this->db->update('administrator_dashboard',$data);
	}
	
	function ea_gm(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','1')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','11');
		$this->db->update('administrator_dashboard',$data);
	}
	function ea_vm(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','55')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','12');
		$this->db->update('administrator_dashboard',$data);
	}
	function purchase_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','53')->where('work_status','0')->get();
         $report= count($query->result());
		$data = array('report_count'=>$report);
		$this->db->where('report_id','13');
		$this->db->update('administrator_dashboard',$data);
	}
	function hr_helpticket(){
		 $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','56')->where('work_status','0')->get();
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
	
	function getDashboardModules() {
	    $query = $this->db->select('a.id as moduleid, a.module_name, b.id as submoduleid, b.sub_module_name')
	                      ->from('dashboard_modules a')
	                      ->join('dashboard_sub_modules b', 'a.id=b.module_id')
	                      ->get();
        if($query->num_rows() > 0) {
            return $query->result();
        }
	}
	
	function saveDashboardAccess($data) {
	   //echo "<pre>";print_r($data);exit;
	    $this->db->insert('dashboard_access', $data);
	    return $this->affected_rows();
	}
	

	
}
