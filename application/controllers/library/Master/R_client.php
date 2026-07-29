<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class R_client extends CI_Controller {

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

	}
public function index()

	{
		$this->load->view('master/view_add_client');
	}

	public function add_name()
	{


		$user_id =$this->session->userdata['logged_in']['user_id'];
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		$table = "r_client";


			$data = array(
			'name'=>$this->input->post('name'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			//'added_by'=>$user_id
		);

		$result  = $this->master->insert_record($table,$data);
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully added.');
			redirect(page_url.'Master/R_client');

		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/R_client');
		}
		}




	public function view_budget()
	{

		$i=1;
		$this->db->select('*')->from('r_client');
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Master/R_client/update_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Master/R_client/update_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
			$edit = "<a href='".page_url."Master/R_client/edit_budget/".$row->id."'><i class='fa fa-pencil'></i></a>";

			$data[] = array('sr_no'=>$i,
			'name'=>$row->name,
			'status'=>$sta,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);

		echo json_encode($results);
	}

	public function update_status()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(4);
		$sval =  $this->uri->segment(5);
		$field_name = "id";
		$table = "r_client";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			$res = $this->master->update_records($table,$data,$identifier,$field_name);
			$this->session->set_flashdata('message', 'Status successfully updated.');
		redirect(page_url.'Master/R_client');
  }

	public function edit_budget(){
		$this->load->view('master/edit_client');
	}

	public function update_name()
	{


			date_default_timezone_set("Asia/Kolkata");
			$date =  date('Y-m-d H:i:s');
      $table = "r_client";


  			$data = array(
  			'name'=>$this->input->post('name'),
  			'status'=>$this->input->post('status'),
  			'added_on'=>$date,
  			//'added_by'=>$user_id
  		);


			$this->db->where('id',$this->uri->segment(4));
			$result  = $this->db->update($table,$data);
		if($result)
		{
			$this->session->set_flashdata('message','Thank you, record successfully updated.');
			redirect(page_url.'Master/R_client');

		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Master/R_client');;
		}


	}

	}

?>
