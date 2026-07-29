<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporter_details extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('master/transporter');
    }

    function add_transporters() {    
        date_default_timezone_set("Asia/kolkata");
        $date = date ('Y-m-d H:i:s');
        $table = "transporter_details";

        if($this->input->post('gst_appl')==1)
        {
            $gstn=$this->input->post('gst');
        }else
        {
            $gstn=''; 
        }

        if($this->input->post('tds')==0)
        {
            $pic = $_FILES['tds_dec']['name'];

            if($pic <> '') {
            $files = explode('.', $pic);
            $ext = end($files);
            $newname = time().'.'.$ext;
            move_uploaded_file($_FILES['tds_dec']["tmp_name"], UPLOADPATH.'tds_cert/'.$newname);
            } else {
            $newname = '';
            }

        }else
        {
            $newname='';
        }

        if($this->input->post('gst_appl')==1)
        {
            $gstapp=1;
        }else
        {
             $gstapp=0;
        }
            $data = array(
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'gst_appl'=>$gstapp,
                'gst'=>$gstn,
                'tds'=>$this->input->post('tds'),
                'tds_cert'=>$newname,
                'pan'=>$this->input->post('pan'),
                'mobile_no' => $this->input->post('mobile_no'),
                'added_on' => $date,
                'added_by' => $_SESSION['logged_in']['user_id']
                );

            $this->db->insert('transporter_details', $data);

            $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
            redirect(page_url.'Master/Transporter_details');

    }

    function transporters_listing() {
        $business_data = array();
        $query = $this->db->select('*')
                          ->from('transporter_details')
                          ->get();


            $i=1;
            if($query->num_rows() > 0) { 
                foreach($query->result() as $row) {

                 $edit = "<a href='".page_url."Master/Transporter_details/edit_transporters/".$row->id."'><i class='fa fa-pencil'></i></a>";

                 if($row->gst_appl==1)
                 {
                    $gt="Yes";
                    $gt.="<br>".$row->gst;
                 }else
                 {
                    $gt="No";
                 }

                 if($row->tds==0)
                 {
                 if($row->tds_cert<>''){

                    $tdcert="<a href='".page_url1."image_bank/tds_cert/".$row->tds_cert."' download>Download</a>";
                 }else
                 {
                    $tdcert='';
                 }
                }else
                {
                     $tdcert='';
                }
                $business_data[] = array(
                            'sr_no' => $i,
                            'name' => $row->name, 
                            'mobile_no' => $row->mobile_no, 
                            'address' => $row->address,
                            'gst' => $gt,
                            'tds' => $row->tds,
                            'pan'=>$row->pan,
                            'tds_cert'=>$tdcert,
                            'edit'=>$edit

                        );

                $i++;

                }
            }

            $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($business_data),
            "iTotalDisplayRecords" => count($business_data),
            "aaData"=>$business_data);
            
            echo json_encode($results);

    }

    public function edit_transporters() {
        $this->load->view('master/edit_transporters');
    }


    public function update_transporter() {


         if($this->input->post('gst_appl')==1)
        {
            $gstn=$this->input->post('gst');
            $appl=1;
        }else
        {
            $gstn=''; 
            $appl=0;
        }


        if($this->input->post('tds')==0)
        {
            $pic = $_FILES['tds_dec']['name'];

            if($pic <> '') {
            $files = explode('.', $pic);
            $ext = end($files);
            $newname = time().'.'.$ext;
            move_uploaded_file($_FILES['tds_dec']["tmp_name"], UPLOADPATH.'tds_cert/'.$newname);
            } else {
            $newname = $this->input->post('old_image');
            }

        }else
        {
            $newname='';
        }


        $data = array(
                'name' => $this->input->post('name'),
                'address' => $this->input->post('address'),
                'mobile_no' => $this->input->post('mobile_no'),
                'pan'=>$this->input->post('pan'),
                'gst_appl'=>$appl,
                'gst'=>$gstn,
                'tds' => $this->input->post('tds'),
                'tds_cert'=>$newname
                );

               $this->db->where('id', $this->uri->segment(4))
                        ->update('transporter_details',$data);

         $this->session->set_flashdata('messege','<div class="alert-success">Thankyou, record successfully updated.</div><br/>');
         redirect(page_url.'Master/Transporter_details');

    }

    function checkIfMobileNoExists() {
    	$mobile_no = $this->input->post('mobile_no');

    	$sql = $this->db->select('id')
    					->from('transporter_details')
    					->where('mobile_no', $mobile_no)
    					->get();

    	echo $sql->num_rows();
    }
 

}
