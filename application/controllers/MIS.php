<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MIS extends CI_Controller {
	
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
		$this->load->model('MIS_model');
		$config = array();  
		$config = Array( 'protocol' => 'smtp', 
			'smtp_host' => 'ssl://smtp.googlemail.com', 
			'smtp_port' => 465, 
			'smtp_user' => 'response@hongyijig.com', 
			'smtp_pass' => 'hongyijig123', 
			'mailtype' => 'html', 
			'charset' => 'iso-8859-1' );

		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		
		
	}

	function index()
	{
		$this->load->view('MIS/mis_user_wise');
	}

	function gettaskdata()
	{
		$user_id=$this->input->post('user_id');
		$sdate=$this->input->post('sdate');
		$edate=$this->input->post('edate');
		$flag=$this->input->post('flag');
		$dfid = $this->input->post('dfid');
		if($flag==1)
		{
		$data=$this->MIS_model->allnotdonetaskdetail($sdate,$edate,$user_id,$dfid);
		}else if($flag==2)
		{
			$data=$this->MIS_model->alldelayednotdonetaskdetail($user_id, $sdate,$edate,$dfid);
		}else if($flag==4)
		{
			$data=$this->MIS_model->alldonenotclosedTicket($user_id, $sdate,$edate,$dfid);
		}else
		{
			$data=$this->MIS_model->allnotclosedTicket($user_id, $sdate,$edate,$dfid);
		}
		echo $data;
	}


	function filter_mis(){
		$startdate = date('Y-m-d',strtotime($this->input->post('start_date')));
		$enddate = date('Y-m-d',strtotime($this->input->post('end_date')));
		$userid = $this->input->post('user');
		$dfid = $this->input->post('filterbydf');
		$department = $this->input->post('department');
		redirect(page_url."MIS/index/".$dfid."/".$startdate."/".$enddate."/".$userid."/".$department);

	}


	function getmissedfollowups()
	{
		$user_id=$this->input->post('user_id');
		$data=$this->MIS_model->getallmissedfollowup($user_id);
		echo $data;
	}

	function getUsers()
	{
		$html='<option value="ALL">ALL</option>';
			$department=$this->input->post('department');

			 $this->db->select('b.user_id,b.first_name,b.last_name, c.department')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->where('b.business_location',2)->where('b.user_role_id!=',1)->where('b.user_status',1)->group_by('a.assigned_user');
			if($department<>'ALL' && $department<>'')
				{
					$this->db->where('b.department_id',$department);
				}

			$rtr =$this->db->get();
			
			if($rtr->num_rows()>0)
			{
			foreach($rtr->result() as $rtr1)
			{ 
				$html.='<option  value="'.$rtr1->user_id.'">'.$rtr1->first_name.'&nbsp;'.$rtr1->last_name.'</option>';
			}
			}

			echo $html; 
	}



		function get_delegation_data()
	{
		$user_id=$this->input->post('user_id');
		$sdate=$this->input->post('sdate');
		$edate=$this->input->post('edate');
		$flag=$this->input->post('flag');
		if($flag==1)
		{
		$data=$this->MIS_model->all_task_delegated_not_done_details($sdate,$edate,$user_id);
		}else
		{
			$data=$this->MIS_model->all_task_delegated_delayed($sdate,$edate,$user_id);
		}
		echo $data;
	}


	
	function get_mom_data()
	{
		$user_id=$this->input->post('user_id');
		$sdate=$this->input->post('sdate');
		$edate=$this->input->post('edate');
		$flag=$this->input->post('flag');
		if($flag==1)
		{
		$data=$this->MIS_model->all_MOM_not_done_details($sdate,$edate,$user_id);
		}else
		{
			$data=$this->MIS_model->all_MOM_done_n_delayed_details($sdate,$edate,$user_id);
		}

		echo $data;
	}


	function save_remark()
{
    $id = $this->input->post('id');
    $remark = $this->input->post('remark');

    $data = array(
        'remarks' => $remark,
        'taskupdatedontime' => date('Y-m-d H:i:s')
    );

    $this->db->where('id', $id);
    $update = $this->db->update('task_department_wise_scheduling', $data);

    if ($update) {
        echo json_encode([
            'status' => 'success',
            'remark' => $remark,
            'id' => $id
        ]);
    } else {
        echo json_encode([
            'status' => 'error'
        ]);
    }
}


}