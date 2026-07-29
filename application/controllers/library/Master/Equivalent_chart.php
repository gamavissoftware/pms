<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Equivalent_chart extends CI_Controller {

    public function __construct() {
        parent::__construct();

    }

    public function index() {
        $this->load->view('master/equivalent_chart');
    }


    function chart_detailsOLdd() {
        $business_data = array();
        $query = $this->db->select('id,client_product,spec_file,msds')
                          ->from('equivalent_chart')
                          ->group_by('client_product')
                          ->get();


            $i=1;
            if($query->num_rows() > 0) { 
                foreach($query->result() as $row) {

                $d=array();
                $ourprd='';

                $this->gettabular_chart($$row->client_product);
                $restey=$this->db->select('our_product')->from('equivalent_chart')->where('client_product',$row->client_product)->get();
                if($restey->num_rows()>0)
                {

                    foreach($restey->result() as $dnum)
                    {
                        $d[]=$dnum->our_product;
                    }

                    $ourprd=implode(', ',$d);

                }
               
               if($row->spec_file<>'')
               {
                $spec="<a href='".page_url1."competitor_files/".$row->spec_file."' download>Download Spec File</a>";
                }else
                {
                    $spec='';
                }

                 if($row->msds<>'')
               {
                 $msdss="<a href='".page_url1."competitor_files/".$row->msds."' download>Download MSDS File</a>";
               }else
               {
                    $msdss="";
               }




               
                $business_data[] = array(
                            'sr_no' => $i,
                            'competitor' => $row->client_product, 
                            'our_equivalent' =>$ourprd,
                            'spec_file'=>$spec."<br/>".$msdss

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

    function add_data()
    {
        $c_product_name=$this->input->post('c_product_name');
        $rack_location=$this->input->post('rack_location');

        $photo=$_FILES['specfile']['name'];
            
            if($photo <> '') {
                $image1 = explode('.',$photo);
                $cat_image = end($image1);
                $instrumentimg_spec = time().'.'.$cat_image;
                move_uploaded_file($_FILES["specfile"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg_spec);
            } else {
                $instrumentimg_spec = "";
            }


            $photo=$_FILES['msds']['name'];
            
            if($photo <> '') {
                $image1 = explode('.',$photo);
                $cat_image = end($image1);
                $instrumentimg_msds = time().'1.'.$cat_image;
                move_uploaded_file($_FILES["msds"]["tmp_name"],UPLOADPATH.'instrumentimg/' . $instrumentimg_msds);
            } else {
                $instrumentimg_msds = "";
            }   


            for($t=0;$t<count($rack_location);$t++)
            {
                $prd=$this->getproduct_detail($rack_location[$t]);
                $data=array('client_product'=>$c_product_name,'our_product'=>$prd,'spec_file'=>$instrumentimg_spec,'msds'=>$instrumentimg_msds);
                $this->db->insert('equivalent_chart',$data);

            }



                $this->session->set_flashdata('message','<span style="color:red; float-left:20px;">Thank you, Your record successfully added.</span><br/>');
                redirect(page_url.'Master/equivalent_chart');

    }


    function getproduct_detail($id)
    {
        $d='';
        $rest=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$id)->get();

        if($rest->num_rows()>0)
        {
            foreach($rest->result() as $row);

            $d=$row->instruments_name;
        }

        return $d;

    }
  

     function chart_details() {
        $business_data = array();
        $query = $this->db->select('id,our_product,client_product')
                          ->from('equivalent_chart')
                          ->where('client_product!=','')
                          ->order_by('client_product','ASC')
                          ->group_by('client_product')
                          ->get();


            $i=1;
            if($query->num_rows() > 0) { 
                foreach($query->result() as $row) {

                $comp=$this->get_tabular_competitor($row->client_product);
               
                $business_data[] = array(
                            'sr_no' => $i,
                            'competitor' => $row->client_product, 
                            'our_equivalent' =>$comp,
                            'spec_file'=>''

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

    function get_tabular_competitor($client_product)
    {       
        $table='';
        $resty=$this->db->select('our_product')->from('equivalent_chart')->where('client_product',$client_product)->get();
        if($resty->num_rows()>0)
        {
           foreach($resty->result() as $row)
            {
                $table.='<div class="col-md-3">'.$row->our_product.'</div>';
                $this->getmore_table($row->our_product);
            }
    
        }

        return $table;
     
    }

    function getmore_table($our_product)
    {

         $table='';
        $resty=$this->db->select('client_product')->from('equivalent_chart')->where('our_product',$our_product)->get();
        if($resty->num_rows()>0)
        {
            
            foreach($resty->result() as $row)
            {
                $table.='<div class="col-md-3">'.$row->client_product.'</div>';
                //$this->get_tabular_competitor($row->client_product);
            }

            
        }

        return $table;

    }

}
