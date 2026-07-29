<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Interest extends CI_Controller
{
	    public function __construct()
    {
        parent::__construct();

        $this->load->model("User_model", "user");
        $this->load->model("Master_model", "master"); 
    }
    public function index()
    {
        $this->load->view("master/interest");
    }

    public function interest_list(){
    	$micro_plan_data = [];
        $i = 1;
        $this->db->select("*")->from("payment_interest");

        $query = $this->db->get();
        $res = $query->result();

        foreach ($res as $row) {
        	$edit = "<a href='".page_url."Master/Interest/edit_interest/".$row->id."'><i class='fa fa-pencil'></i></a>";	


            $micro_plan_data[] = [
                "sr_no" => $i,
                "interest_per" => $row->interest_per,
                "edit" => $edit,
            ];
            $i++;
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($micro_plan_data),
            "iTotalDisplayRecords" => count($micro_plan_data),
            "aaData" => $micro_plan_data,
        ];

        echo json_encode($results);
    }

    public function add_interest(){
    	$table = "payment_interest";
    	date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s');
            $query = $this->db->select("id")
                ->from($table)
                ->where("interest_per",$this->input->post("interest_per"))
                ->get();

            $res = $query->result();
            // echo "<pre>";
            // echo $this->input->post('stage_name_level');
            // print_r($res); exit;
            if ($res) {
                $this->session->set_flashdata(
                    "message",
                    '<div class="alert alert-danger">Sorry! This record already exist.</div>'
                );
                redirect(page_url . "Master/Interest");
            } else {
                $data = [
                    "interest_per" => trim($this->input->post("interest_per")),
                    "addedOn" => $date 
                ];

                $result = $this->master->insert_record($table, $data);

		        if($result)
				{
					$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
					redirect(page_url.'Master/Interest');
					
				}else
				{
					$this->session->set_flashdata('message','<div class="alert alert-info">Sorry,technical error accure.</div><br/>');
					redirect(page_url.'Master/Interest');
				}
            }
    }

    public function edit_interest(){
    	$this->load->view('master/edit_interest');
    }

    public function update_interest(){
    	$table = "payment_interest";
    	date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s');
 
        $data = [
                    "interest_per" => trim($this->input->post("interest_per")),
                    "addedOn" => $date 
                ];

        $this->db->where('id',$this->uri->segment(4));
        $result  = $this->db->update($table,$data); 
        if($result)
        {
            $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully updated.</div>');
            redirect(page_url.'Master/Interest');
            
        }else
        {
            $this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
            redirect(page_url.'Master/Interest');
        }

    }
}