<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hpcl_location extends CI_Controller {
    
    
    public function __construct()
    {
        //echo "hi";exit;
        parent::__construct();
   $this->load->model('Hpcl_model','master');
        
    }

public function index()
{
$this->load->view('Master/hpcl_location');
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

$result = $this->master->insert_record($table,$data);

if($result>0)
{

    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');

    redirect(page_url.'Masters/Hpcl_location');
}else{

}
    $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div>');

            redirect(page_url.'Masters/Hpcl_location');


    }

public function location_listing()
{

$bussiness_data = array();
$this->db->select('*')->from('hpcl_location');
$query= $this->db->get();

$res=$query->result();
$i=1;

foreach($res as $row)
{

$status =$row->status;
if ($status=='1')
{

    $sta = "<a href='".page_url."Masters/Hpcl_location/update_location_status/".$row->id."/".$row->status."'><span class='btn btn-success btm-sm'>Active</span></a>";

}else{


$sta = "<a href='".page_url."Masters/Hpcl_location/update_location_status/".$row->id."/".$row->status."'><span class='btn btn-danger btm-sm'>Not Active</span></a>";

}

 $edit = "<a href='".page_url."Masters/Hpcl_location/edit_location/".$row->id."'><i class='fa fa-pencil'></i></a>";

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
        
                    $res = $this->master->update_records($table,$data,$identifier,$field_name);
        
                    $this->session->set_flashdata('message', 'Status successfully updated.');
                    redirect(page_url.'Masters/Hpcl_location');
        
            
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
         redirect(page_url.'Masters/Hpcl_location');
         
     }else
     {
         $this->session->set_flashdata('message','<div class="alert alert-danger">Sorry,technical error accure.</div><br/>');
         redirect(page_url.'Masters/Hpcl_location');
     }


    }
            


            public function edit_location()

            {
        
                $this->load->view('Master/edit_hpcl_location');
            }
        


    public function hpcl_location()

 {

date_default_timezone_set("Asia/Kolkata");
$date = date('Y-m-d H:i:s');
$table = "hpcl_location";
$identifier = $this->uri->segment(4);
$field_name = "id";


$data = array(
    'name'=>$this->input->post('name'),
    'address'=>$this->input->post('address'),

 );
 $result = $this->master->update_records($table,$data,$identifier,$field_name);

 if($result)

        {

            $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');

            redirect(page_url.'Masters/Hpcl_location');

            

        }else

        {

            $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div>');

            redirect(page_url.'Masters/Hpcl_location'); 

            

        }
 }  

}
