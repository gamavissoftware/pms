<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Wallet extends CI_Controller
{
	    public function __construct()
    {
        parent::__construct();

        $this->load->model("User_model", "user");
        $this->load->model("Master_model", "master"); 
    }
    public function index()
    {
        $this->load->view("master/add_payment");
    }

    public function interest_list(){
    	$micro_plan_data = [];
        $i = 1;
        $this->db->select("a.*,b.first_name,b.last_name,b.first_name,b.last_name,c.balance,c.active")->from("sunder_collection_reference a")->join('sunder_collection_reference_balance c','a.id=c.collection_id')->join("system_users b",'a.addedBy=b.user_id');

        $query = $this->db->get();
        $res = $query->result();

        foreach ($res as $row) {
        	$edit = "<a href='".page_url."Master/Interest/edit_interest/".$row->id."'><i class='fa fa-pencil'></i></a>";	

            $html = '';
            $sql = $this->db->select('credit_debit, credit_debit_for, credit_debit_amount')
                            ->from('sunder_collection_credit_debit')
                            ->where('collection_id', $row->id)
                            ->get();

            if($sql->num_rows() > 0) {
                $html .= '<table class="table table-bordered" style="width:100%">
                                <tr style="background-color:#DADADA;">
                                    <th style="text-align:center; width:250px;">REASON</th>
                                    <th style="text-align:center;">CREDIT</th> 
                                    <th style="text-align:center;">DEBIT</th> 
                                </tr>';
                foreach ($sql->result() as $rows) {
                        $credit_amount = '';
                        $debit_amount = '';

                        if($rows->credit_debit == 1) {
                            $credit_amount = '<strong style="color:green;">+'.$rows->credit_debit_amount.'</strong>';
                            $debit_amount = '';
                        } else if($rows->credit_debit == 2) {
                            $credit_amount = '';
                            $debit_amount = '<strong style="color:red;">-'.$rows->credit_debit_amount.'</strong>';
                        }

                    $html .= '<tr>
                                <td style="text-align:center; width:250px;">'.$rows->credit_debit_for.'</td>
                                <td style="text-align:center;">'.$credit_amount.'</td> 
                                <td style="text-align:center;">'.$debit_amount.'</td> 
                                </tr>';
                }

                 $html .= '</table>';
            }



            $micro_plan_data[] = [
                "sr_no" => $i,
                "collection_date" => date('d-m-y',strtotime($row->collection_date)),
                "collection_id" => $row->collection_id,
                "collection_amount" => floatval($row->collection_amount),
                "balance" => floatval($row->balance),
                "remarks" => $row->remarks,
                "credit_debit_note" => $html,
                "addedOn" => $row->addedOn,
                "addedBy" => $row->first_name." ".$row->last_name,
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

      public function add_payment(){
        $this->db->trans_begin();
      $table = "customer_collection_reference";
      date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s');
            
                $data = [
                  
                    "customer_id" => trim($this->input->post("customer_id")),
                    "collection_date" => date('Y-m-d',strtotime($this->input->post("payment_date"))),
                    "collection_id" => trim($this->input->post("collection_id")),
                    "collection_amount" => trim($this->input->post("amount")),
                    "remarks" => trim($this->input->post("remarks")),
                    "addedOn" =>$date,
                    "addedBy"=>$_SESSION['logged_in']['user_id']
                ];

                $result = $this->master->insert_record($table, $data);
                $lid=$this->db->insert_id();

                $data1=array('collection_id'=>$lid,
                             'balance'=>trim($this->input->post("amount")),
                             'active'=>1,
                             'last_updatedOn'=>date('Y-m-d H:i:s'));
                $this->db->insert('customer_collection_reference_balance',$data1);



                if($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">ERROR TRY AGAIN</span></div><br/>');

                }else
                {
                $this->db->trans_commit();
                $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Details Added</span></div><br/>');


                }

           
          
          redirect(page_url.'Customer/direct_customer');
        
        
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

    function save_credit_debit_note() {
        $this->db->trans_begin();
        $credit_debit = $this->input->post('credit_debit');
        $credit_debit_amount = $this->input->post('credit_debit_amount');
        $collection_balance = $this->input->post('collection_balance');
        $balance_id = $this->input->post('balance_id');

        if($credit_debit == 1) {
            $remaining_balance = $collection_balance + $credit_debit_amount;
        } else {
            $remaining_balance = $collection_balance - $credit_debit_amount;
        }

        $data = array(
                     'collection_id' => $this->input->post('collection_primary_id'),
                     'credit_debit' => $credit_debit,
                     'credit_debit_for' => $this->input->post('credit_debit_for'),
                     'credit_debit_amount' => $this->input->post('credit_debit_amount'),
                     'remaining_balance' => $remaining_balance,
                     'added_on' => date('Y-m-d H:i:s'),
                     'added_by' => $this->session->userdata['logged_in']['user_id']
                     );

        $this->db->insert('sunder_collection_credit_debit', $data);

        $datas = array(
                     'balance' => $remaining_balance
                      );

        $this->db->where('id', $balance_id)
                 ->update('sunder_collection_reference_balance', $datas);


                if($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">ERROR! PLEASE TRY AGAIN.</span></div><br/>');

                }else
                {
                $this->db->trans_commit();
                $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Details Added Successfully</span></div><br/>');


                }

               
                    
                    redirect(page_url.'Wallet');
    }
}