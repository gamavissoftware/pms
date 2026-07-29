<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finished_goods extends CI_Controller {
	
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

		$this->load->model('Master_model','master');
		
	}

	public function index() {
		$this->load->view('master/finished_goods_type');
	}

	function add_finished_goods_type() {

			$data = array(  
						'type_name' => $this->input->post('type_name'),
						'status' => $this->input->post('status'),
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->session->userdata['logged_in']['user_id']
						);
			
			$this->db->insert('finished_goods_type', $data);	

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Master/Finished_goods');
		
	}

	function finished_goods_type_listing() {
		$i=1;
		$lead_data = array();
		$query = $this->db->select('id, type_name, status')
						  ->from('finished_goods_type')
						  ->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
												
			if($row->status == 1)
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_type_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_type_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
			$edit = "<a href='".page_url."Master/Finished_goods/edit_finished_goods_type/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$lead_data[] = array(
							'sr_no' => $i,
							'type_name' => $row->type_name,
							'status' => $status,
							'edit' => $edit
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

	function update_type_status() {
		$id = $this->uri->segment(4);
		$sta = $this->uri->segment(5);

		if($sta == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		
		$data = array(
					'status' => $status
					);

			   $this->db->where('id', $id)
						->update('finished_goods_type', $data);
		
		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Status successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods');
	}


	public function edit_finished_goods_type()
	{
		$this->load->view('master/edit_finished_goods_type');
		
	}
	
	public function update_finished_goods_type()
	{
		$date =  date('Y-m-d H:i:s'); 	
		$edit_id = $this->uri->segment(4);	

		$data = array(
					'type_name' => $this->input->post('type_name'),
					'status' => $this->input->post('status')
					);

		$this->db->where('id', $edit_id)
				 ->update('finished_goods_type', $data);

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods');
			
	}

		function add_finished_goods() {

			$photo=$_FILES['picture']['name'];
			
			if($photo <> '') {
				$image1 = explode('.',$photo);
				$cat_image = end($image1);
				$instrumentimg = time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/mehtacosmetics/image_bank/instrumentimg/' . $instrumentimg);
			} else {
				$instrumentimg = "";
			}

			$data = array(  
						'type' => $this->input->post('type'),
						'product_name' => $this->input->post('product_name'),
						'product_code' => $this->input->post('product_code'),
						'product_image' => $instrumentimg,
						'product_price' => $this->input->post('price'),
						'status' => $this->input->post('status'),
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->session->userdata['logged_in']['user_id']
						);
			
			$this->db->insert('finished_goods', $data);	

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'FMS/instruments');
		
	}

	function finished_goods_listing() {
		$i=1;
		$lead_data = array();
		$query = $this->db->select('a.id, a.product_name, a.product_code, a.product_image, a.product_price, a.status, b.type_name')
						  ->from('finished_goods a')
						  ->join('finished_goods_type b', 'b.id=a.type')
						  ->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
												
			if($row->status == 1)
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_goods_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_goods_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
			$edit = "<a href='".page_url."Master/Finished_goods/edit_finished_goods/".$row->id."'><i class='fa fa-pencil'></i></a>";

			if($row->product_image <> '' && $row->product_image <> 0) {
				$image="<img src='".instrumentimg.$row->product_image."' style='width:100px'>";
			} else {
				$image="Image not found";
			}

			$sub_parts =  "<a href='".page_url."Master/Finished_goods/fin_good_sub_parts/".$row->id."'><span class='btn btn-warning btn-xs'>SUB PARTS</span></a>";
			$bom =  "<a href='".page_url."Master/Finished_goods/update_goods_status/'><span class='btn btn-warning btn-xs'>BOM</span></a>";

			$lead_data[] = array(
							'sr_no' => $i,
							'type' => $row->type_name,
							'product_name' => $row->product_name,
							'product_code' => $row->product_code,
							'product_price' => $row->product_price,
							'product_image' => $image,
							'sub_part' => $sub_parts,
							'bom' => $bom,
							'status' => $status,
							'edit' => $edit
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

	function update_goods_status() {
		$id = $this->uri->segment(4);
		$sta = $this->uri->segment(5);

		if($sta == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		
		$data = array(
					'status' => $status
					);

			   $this->db->where('id', $id)
						->update('finished_goods', $data);
		
		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Status successfully updated.</div>'));
		redirect(page_url.'FMS/instruments');
	}


	public function edit_finished_goods()
	{
		$this->load->view('master/edit_finished_goods');
		
	}
	
	public function update_finished_goods()
	{
		$date =  date('Y-m-d H:i:s'); 	
		$edit_id = $this->uri->segment(4);	

		$photo=$_FILES['picture']['name'];

			if($photo <> '') {
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$instrumentimg=time().'.'.$cat_image;
				move_uploaded_file($_FILES["picture"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/mehtacosmetics/image_bank/instrumentimg/' . $instrumentimg);
			} else {
				$instrumentimg=$this->input->post('oldimg');
			}  

		$data = array(
					'type' => $this->input->post('type'),
					'product_name' => $this->input->post('product_name'),
					'product_code' => $this->input->post('product_code'),
					'product_price' => $this->input->post('product_price'),
					'product_image' => $instrumentimg,
					'status' => $this->input->post('status')
					);

		$this->db->where('id', $edit_id)
				 ->update('finished_goods', $data);

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>'));
		redirect(page_url.'FMS/instruments');
			
	}

	function sub_parts() {
		$this->load->view('master/sub_parts');
	}

	function add_sub_parts() {
		$data = array(
					'category' => $this->input->post('name'),
					// 'status' => $this->input->post('status')
					);

		$this->db->insert('presto_machine_part_category', $data);

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully added.</div>'));
		redirect(page_url.'Master/Finished_goods/sub_parts');
	}

	function sub_parts_list() {
		$i=1;
		$lead_data = array();
		$query = $this->db->select('id, category')
						  ->from('presto_machine_part_category')
						  ->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
				
			$edit = "<a href='".page_url."Master/Finished_goods/edit_sub_part/".$row->id."'><i class='fa fa-pencil'></i></a>";

			$lead_data[] = array(
							'sr_no' => $i,
							'name' => $row->category,
							'edit' => $edit
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


	function update_sub_part_status() {
		$id = $this->uri->segment(4);
		$sta = $this->uri->segment(5);

		if($sta == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		
		$data = array(
					'status' => $status
					);

			   $this->db->where('id', $id)
						->update('sub_parts', $data);
		
		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Status successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods/sub_parts');
	}

	public function edit_sub_part()
	{
		$this->load->view('master/edit_sub_part');
		
	}
	
	public function update_sub_parts()
	{
		$date =  date('Y-m-d H:i:s'); 	
		$edit_id = $this->uri->segment(4);	

		$data = array(
					'category' => $this->input->post('name')
					);

		$this->db->where('id', $edit_id)
				 ->update('presto_machine_part_category', $data);

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods/sub_parts');
			
	}

	function fin_good_sub_parts() {
		$this->load->view('master/fin_good_sub_parts');
	}

	function getSubParts() {
	   $q = $_GET['q'];
	   $query = $this->db->select('id, name')
						 ->from('sub_parts')
						 ->where('status', 1)
						 ->get();

		if($query->num_rows()>0) {
			foreach($query->result() as $row) {
				$json[] = array('id'=>$row->id, 'text'=>$row->name);
			}
		} else {
				$json[] = array('id'=>"", 'text'=>"No Data Available");
		}
		
		echo json_encode($json);
	}

	function add_fin_goods_sub_parts() {
		$sub_part_id = $this->input->post('sub_part_id');

		// echo "<pre>";print_r($sub_part_id);exit;

		$sql = $this->db->select('id')
						->from('finished_goods_sub_parts')
						->where('fin_good_id', $this->uri->segment(4))
						->get();

		if($sql->num_rows() > 0) {

			$this->db->where('fin_good_id', $this->uri->segment(4))
					 ->delete('finished_goods_sub_parts');
					 
			for($i = 0; $i < count($sub_part_id); $i++) {

				$data = array(
							'fin_good_id' => $this->uri->segment(4),
							'sub_part_id' => $sub_part_id[$i]
							);

				$this->db->insert('finished_goods_sub_parts', $data);
			}
		} else {
			for($i = 0; $i < count($sub_part_id); $i++) {
				$data = array(
							'fin_good_id' => $this->uri->segment(4),
							'sub_part_id' => $sub_part_id[$i]
							);

				$this->db->insert('finished_goods_sub_parts', $data);
			}
		}

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>'));
		redirect(page_url.'FMS/instruments');
	}

	function machine() {
		$this->load->view('master/machine');
	}

	function add_machine() {
		$data = array(  
						'machine_name' => $this->input->post('machine_name'),
						'status' => $this->input->post('status'),
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->session->userdata['logged_in']['user_id']
						);
			
		$this->db->insert('machine', $data);	

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url.'Master/Finished_goods/machine');
	}


	function machine_listing() {
		$i=1;
		$lead_data = array();
		$query = $this->db->select('id, machine_name, status')
						  ->from('machine')
						  ->get();

		if($query->num_rows() > 0) {
		foreach($query->result() as $row) {
												
			if($row->status == 1)
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_machine_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$status =  "<a href='".page_url."Master/Finished_goods/update_machine_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}
				
			$edit = "<a href='".page_url."Master/Finished_goods/edit_machine/".$row->id."'><i class='fa fa-pencil'></i></a>";


			$lead_data[] = array(
							'sr_no' => $i,
							'machine_name' => $row->machine_name,
							'status' => $status,
							'edit' => $edit
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


	function update_machine_status() {
		$id = $this->uri->segment(4);
		$sta = $this->uri->segment(5);

		if($sta == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		
		$data = array(
					'status' => $status
					);

			   $this->db->where('id', $id)
						->update('machine', $data);
		
		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Status successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods/machine');
	}


	public function edit_machine()
	{
		$this->load->view('master/edit_machine');
		
	}
	
	public function update_machine()
	{
		$date =  date('Y-m-d H:i:s'); 	
		$edit_id = $this->uri->segment(4);	

		$data = array(
					'machine_name' => $this->input->post('machine_name'),
					'status' => $this->input->post('status')
					);

		$this->db->where('id', $edit_id)
				 ->update('machine', $data);

		$this->session->set_flashdata('message', $this->session->set_flashdata('message','<div class="alert alert-info">Record successfully updated.</div>'));
		redirect(page_url.'Master/Finished_goods/machine');
			
	}

}