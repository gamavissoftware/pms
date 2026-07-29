<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hpcl_location extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('master/hpcl_location');
    }

    function add_location() {    
        date_default_timezone_set("Asia/kolkata");
        $date = date ('Y-m-d H:i:s');
        $table = "hpcl_location";

        $data = array(
            'name'=>$this->input->post('name'),
            'address'=>$this->input->post('address'),
            'status'=>$this->input->post('status'),
            'added_on'=>$date
            );

        $result = $this->db->insert($table,$data);

        if($result>0)
        {
            $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');

            redirect(page_url.'Master/Hpcl_location');
        }else{
            $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error occurred.</div>');
            redirect(page_url.'Master/Hpcl_location');

        }
    }

        public function location_listing(){
            $business_data = array();
                $this->db->select('*')->from('hpcl_location');
                $query= $this->db->get();

            $res=$query->result();
            $i=1;

            foreach($res as $row)
            {

            $status =$row->status;
            if ($status=='1')
            {

                $sta = "<a href='".page_url."Master/Hpcl_location/update_location_status/".$row->id."/".$row->status."'><span class='btn btn-success btm-sm'>Active</span></a>";

            }else{


            $sta = "<a href='".page_url."Master/Hpcl_location/update_location_status/".$row->id."/".$row->status."'><span class='btn btn-danger btm-sm'>Not Active</span></a>";

            }

             $edit = "<a href='".page_url."Master/Hpcl_location/edit_location/".$row->id."'><i class='fa fa-pencil'></i></a>";

            $business_data[] = array(
                        'sr_no'=>$i,
                        'name'=>$row->name, 
                        'address'=>$row->address,          
                        'status'=>$sta,
                        'edit'=>$edit

                    );

            $i++;

            }

                        $results = array(
                        "sEcho" => 1,
                        "iTotalRecords" => count($business_data),
                        "iTotalDisplayRecords" => count($business_data),
                        "aaData"=>$business_data);
                        
                        echo json_encode($results);

        }

 public function update_location_status()
    {

        $identifier = $this->uri->segment(4);
        
        $sval = $this->uri->segment(5);
        
        $field_name = "id";
        
        $table = "hpcl_location";
        
        
        if($sval  == '1')
        {
        
            $status = 0;
        } else
        {
        $status =1;
        
        }
        
        $data = array('status'=>$status);
        
                    $res = $this->db->where('id', $this->uri->segment(4))
                                    ->update('hpcl_location', $data);
        
                    $this->session->set_flashdata('message', 'Status successfully updated.');
                    redirect(page_url.'Master/Hpcl_location');
        
            
            }


            public function update_location_type()
    {
        

 
 $data = array(
     'name'=>$this->input->post('name'),
     'address'=>$this->input->post('address'),
     'status'=>$this->input->post('status'),
     );

     $result = $this->db->where('id', $this->uri->segment(4))
                        ->update('hpcl_location',$data);
  
     if($result)
     {
         $this->session->set_flashdata('messege','<div class="alert-success">Thankyou, record successfully updated.</div><br/>');
         redirect(page_url.'Master/Hpcl_location');
         
     }else
     {
         $this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
         redirect(page_url.'Master/Hpcl_location');
     }


    }
            


    public function edit_location() {
        $this->load->view('master/edit_hpcl_location');
    }
        

    public function hpcl_location() {

        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');
        $table = "hpcl_location";
        $identifier = $this->uri->segment(4);


        $data = array(
            'name'=>$this->input->post('name'),
            'address'=>$this->input->post('address'),
         );

        $result = $this->db->where('id', $this->uri->segment(4))
                           ->update('hpcl_location', $data);

        if($result) {
            $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
            redirect(page_url.'Master/Hpcl_location');          
        } else {
            $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div>');

            redirect(page_url.'Master/Hpcl_location');           

        }
 }  

 function our_vehicles()
 {
    $this->load->view('master/our_vehicle');
 }

 function vehicle_list()
 {
     $business_data = array();
                $this->db->select('*')->from('our_vehicles');
                $query= $this->db->get();

            $res=$query->result();
            $i=1;

            foreach($res as $row)
            {

            $status =$row->status;
    
             $edit = "<a href='".page_url."Master/Hpcl_location/edit_vehicle/".$row->id."'><i class='fa fa-pencil'></i></a>";

            $business_data[] = array(
                        'sr_no'=>$i,
                        'name'=>$row->name, 
                        'type'=>$row->type,          
                        'edit'=>$edit

                    );

            $i++;

            }
                        $results = array(
                        "sEcho" => 1,
                        "iTotalRecords" => count($business_data),
                        "iTotalDisplayRecords" => count($business_data),
                        "aaData"=>$business_data);
                        
                        echo json_encode($results);

 }

    function add_vehicle()
    {
        $name=$this->input->post('name');
        $type=$this->input->post('vtype');
        $status=$this->input->post('status');

        $data=array('name'=>$name,'type'=>$type,'status'=>$status,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
        $this->db->insert('our_vehicles',$data);

        redirect(page_url.'Master/Hpcl_location/our_vehicles/');
        $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');



    }

    function edit_vehicle()
    {
        $this->load->view('master/edit_vehicle');
    }

    function update_vehicle()
    {

        $id=$this->uri->segment(3);
        $name=$this->input->post('name');
        $type=$this->input->post('vtype');


        $data=array('name'=>$name,'type'=>$type,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
        $this->db->where('id',$id);
        $this->db->update('our_vehicles',$data);

         $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
        redirect(page_url.'Master/Hpcl_location/our_vehicles/');
       

    }

}
