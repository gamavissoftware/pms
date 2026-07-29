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
        $this->db->select("a.*,b.first_name,b.last_name,b.first_name,b.last_name,c.balance,c.active")->from("sunder_collection_reference a")->join('sunder_collection_reference_balance c','a.id=c.collection_id')->join("system_users b",'a.addedBy=b.user_id')->order_by('a.collection_date','DESC');

        $query = $this->db->get();
        $res = $query->result();

      
        foreach ($res as $row) {
        	//$edit = "<a href='".page_url."Master/Interest/edit_interest/".$row->id."'><i class='fa fa-pencil'></i></a>";	
         

             $html='';
            $sql = $this->db->select('a.credit_debit,a.credit_debit_for,a.credit_debit_amount,a.invoice_detail,a.invoice_date,a.payment_date')
                            ->from('sunder_collection_credit_debit a')
                            ->where('a.collection_id', $row->id)
                            ->get();

            if($sql->num_rows() > 0) {
                $html .= '<table class="table table-bordered" style="width:100%">
                                <tr style="background-color:#DADADA;">
                                    <th style="text-align:center; width:250px;">REASON</th>
                                    <th style="text-align:center; width:250px;">INVOICE DETAILS & DATE</th>
                                    <th style="text-align:center; width:250px;">PAYMENT DATE</th>
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
                                <td style="text-align:center; width:250px;">'.$rows->invoice_detail."<br/>".date('d-M-Y',strtotime($rows->invoice_date)).'</td>
                                <td style="text-align:center; width:250px;">'.date('d/M/Y',strtotime($rows->payment_date)).'</td>
                                <td style="text-align:center;">'.$credit_amount.'</td> 
                                <td style="text-align:center;">'.$debit_amount.'</td> 
                                </tr>';
                }

                 $html .= '</table>';
            }



            if($i==1)
            {
            $del = "<a href='javascript:;' class='btn btn-warning  btn-xs' onclick='deletecollection_id(".$row->id.",0);'>Remove Payment Adjusted</a><br/><br/><br/><a href='javascript:;' class='btn btn-warning btn-xs' onclick='deletecollection_id(".$row->id.",1);'>Remove Payment Adjusted & Wallet Record</a>"; 
            } else
            {
                $del='';
            } 
            $micro_plan_data[] = [
                "sr_no" => $i,
                "collection_date" => date('d-m-y',strtotime($row->collection_date)),
                "collection_id" => $row->collection_id,
                "collection_amount" => floatval($row->collection_amount),
                "balance" =>"<strong style='color:green;font-size:16px;'>₹".floatval($row->balance)."</strong>",
                "remarks" => $row->remarks,
                "credit_debit_note" => $html,
                "addedOn" => $row->addedOn,
                "addedBy" => $row->first_name." ".$row->last_name,
                "delete" => $del,
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
    	$table = "sunder_collection_reference";
    	date_default_timezone_set("Asia/Kolkata");
        $date =  date('Y-m-d H:i:s');
            
                $data = [
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
                $this->db->insert('sunder_collection_reference_balance',$data1);



                if($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">ERROR TRY AGAIN</span></div><br/>');

                }else
                {
                $this->db->trans_commit();
                $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Details Added</span></div><br/>');


                }

		       
					
					redirect(page_url.'Wallet');
				
        
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
       
        $collection_id=$this->input->post('collection_id');
        $debit_invoice=$this->input->post('debit_invoice');
        $debit_date=$this->input->post('debit_date');

       for($i=0;$i<count($collection_id);$i++)
       {

                $collectid=$collection_id[$i];
               
                $active=$this->input->post('active'.$collectid);
                $after_pay_balance=$this->input->post('after_pay_balance'.$collectid);
                $balance_used=$this->input->post('balance_used'.$collectid);
                $paid_date=$this->input->post('paid_date'.$collectid);

                $data = array(
                'collection_id' => $collection_id[$i],
                'credit_debit' => $credit_debit,
                'credit_debit_for' => $this->input->post('credit_debit_for'),
                'credit_debit_amount' => $this->input->post('credit_debit_amount'),
                'invoice_detail'=>$debit_invoice,
                'invoice_date'=>date('Y-m-d',strtotime(($debit_date))),
                'remaining_balance' => $after_pay_balance,
                'balance_used'=>$balance_used,
                'payment_date'=>date('Y-m-d',strtotime($paid_date)),
                'active'=>$active,
                'added_on' => date('Y-m-d H:i:s'),
                'added_by' => $this->session->userdata['logged_in']['user_id']
                );

                $this->db->insert('sunder_collection_credit_debit', $data);


                $datas = array(
                'balance' => $after_pay_balance,
                'active'=>$active,
                'last_updatedOn'=>date('Y-m-d H:i:s')
                );
                $this->db->where('id', $collectid)
                ->update('sunder_collection_reference_balance', $datas);

        }


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


    public function add_customer_payment(){
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

  function getCustomerCollection() {
    $res = '';
    $customer_id = $this->input->post('customer_id');
    $query = $this->db->select('id, collection_id')
              ->from('customer_collection_reference')
              ->where('customer_id', $customer_id)
              ->order_by('id', 'DESC')
              ->limit(1)
              ->get();

    if ($query->num_rows() > 0) {
      foreach ($query->result() as $row);

        $sql = $this->db->select('id as balance_id, balance')
                ->from('customer_collection_reference_balance')
                ->where('collection_id', $row->id)
                ->where('active', 1)
                ->get();

        if ($sql->num_rows() > 0) {
          foreach ($sql->result() as $rows);
          $res = $rows->balance.'|'.$row->collection_id.'|'.$row->id.'|'.$rows->balance_id.'|'.$customer_id;
        }
    } 

    echo $res;            
  }

      function save_customer_credit_debit_note() {
        
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
                     'customer_id' => $this->input->post('customer_id'),
                     'collection_id' => $this->input->post('collection_primary_id'),
                     'credit_debit' => $credit_debit,
                     'credit_debit_for' => $this->input->post('credit_debit_for'),
                     'credit_debit_amount' => $this->input->post('credit_debit_amount'),
                     'remaining_balance' => $remaining_balance,
                     'added_on' => date('Y-m-d H:i:s'),
                     'added_by' => $this->session->userdata['logged_in']['user_id']
                     );

        $this->db->insert('customer_collection_credit_debit', $data);

        $datas = array(
                     'balance' => $remaining_balance
                      );

        $this->db->where('id', $balance_id)
                 ->update('customer_collection_reference_balance', $datas);


        if($this->db->trans_status() === FALSE)
        {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">ERROR! PLEASE TRY AGAIN.</span></div><br/>');

        }else
        {
        $this->db->trans_commit();
        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Details Added Successfully</span></div><br/>');


        }

               
                    
          redirect(page_url.'Customer/direct_customer');
    }


    function getcollection_id_to_adjust()
    {

        $html='';
        $a=0;
        $total_amount=$this->input->post('amount');

          $rest=$this->db->select('b.collection_date,b.collection_id,a.balance,b.id')->from('sunder_collection_reference_balance a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.balance>',0)->where('a.active',1)->order_by('b.collection_date','ASC')->get();



                $html.='<table class="table table-bordered">
                <thead>
                <tr>
                <th colspan="4" style="text-align: center;">Available Balance & Collection ID</th>
                </tr>
                <tr>

                <th style="width:100px;">Collection ID</th>
                <th>Balance</th>
                <th>Balance Used</th>
                <th>After Payment Balance</th>
                <th>Payment Date</th>


                </tr>
                </thead>
                <tbody>';

                 $a=0;
                        
                    if($rest->num_rows()>0)
                    {
                    $balance=0;
                    $current_running='';
                    $completed=array();
                    $amt_paid=array();
                    $current_running=0;

                    $to_be_paid=$total_amount;

                    $i=0;
                    foreach($rest->result() as $row)
                    {     
                    $current_running=$current_running;

                    if($to_be_paid>0)
                    {
                    if($row->balance>=$to_be_paid)
                    {

                    $balance=$row->balance-$to_be_paid;
                    $to_be_paid=0;
                    $after_pay_balance=$balance;
                    $balance_used=$row->balance-$balance;


                    }else
                    {

                    $balance=$to_be_paid-$row->balance;
                    $to_be_paid=$balance;
                    $after_pay_balance=0;
                    $balance_used=$row->balance;


                    }

                    $r=$i+1;

                      if($after_pay_balance>0)
                                    { 
                                
                                    $d='<input type="hidden" name="active'.$row->id.'" value="1">';
                                  
                                    } else{
                                   
                                    $d=' <input type="hidden" name="active'.$row->id.'" value="0">';
                                   
                                    }


                    $html.='<tr>';
            
                    $html.='<td>'.$row->collection_id.'<input type="hidden" name="collection_id[]" value="'.$row->id.'"></td>';
                    $html.='<td>'.$row->balance.'<input type="hidden" name="balanceless'.$row->id.'" value="'.$row->id.'"></td>';
                    $html.='<td>'.$balance_used.'<input type="hidden" name="balance_used'.$row->id.'" value="'.$balance_used.'"></td>';
                    $html.='<td>'.$after_pay_balance.$d.'<input type="hidden" name="after_pay_balance'.$row->id.'" value="'.$after_pay_balance.'"></td>';
                    $html.='<td><input type="date" name="paid_date'.$row->id.'" class="form-control"></td>';
                                                    
                    $html.='</tr>';
                    $a=1;



    }
}
}


$html.='</tbody>
</table>';

echo $html."|".$a;
}


function save_customer_tq()
{
     $this->db->trans_begin();
    $customer_id=$this->input->post('customer_id');
    $tq_ref=$this->input->post('tq_ref');
    $tq_amount=$this->input->post('tq_amount');

    $d=array('customer_id'=>$customer_id,'tq_ref'=>$tq_ref,'tq_amount'=>$tq_amount,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'tq_date'=>date('Y-m-d',strtotime($this->input->post('tq_date'))));

    $this->db->insert('customer_tq',$d);

     if($this->db->trans_status() === FALSE)
        {
        $this->db->trans_rollback();
        $this->session->set_flashdata('message','<div class="alert alert-danger"><span style="color:#000; float-left:20px;">ERROR! PLEASE TRY AGAIN.</span></div><br/>');

        }else
        {
        $this->db->trans_commit();
        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">TQ Details Added</span></div><br/>');


        }

        redirect(page_url.'Customer/direct_customer');
    }


    function delete_collection_id()
    {
      
        $inv=array();
        $id=$this->uri->segment(3);
       
        $flag=$this->uri->segment(4); 
        //echo $id ; exit;


        /** GET PAYMENT ID AND INVENTORY ID AND UPDATE THE PAYMENT **/
         $rest=$this->db->select('id,inventory_id')->from('inventory_payment_details')->where('collection_id',$id)->get();
        if($rest->num_rows()>0)
        {
            //echo "<pre>"; print_r($rest->result()); exit;
            foreach($rest->result() as $row)
            {
                $invent_id=$row->inventory_id;
                $inv[]=$row->id;

                $data2 = array(
                'pur_paymentOn' =>'0000-00-00 00:00:00',
                'pur_paymentBy'=>0,
                'pur_payment' => 0
                );
                $this->db->where('id',$invent_id);
                $this->db->update('inventory',$data2);
            }
        }

        /** END **/


        /** LOOP ALL PAYMENT ID AND GET THE INVENTORY DETAIL ID  AND DELTE FROM INVENORY_PAYMENT_DETAIL_PRODUCT_WISE**/
       // echo "<pre>"; print_r($inv); exit;
        $inventory_detail_id=array();
        if(count($inv)>0)
        {
            foreach($inv as $payment_id)
            {
            
            $resteyu=$this->db->select('inventory_details_id')->from('inventory_payment_details_product_wise')->where('payment_id',$payment_id)->where('collection_id',$id)->get();
            if($resteyu->num_rows()>0)
            {
                foreach($resteyu->result() as $rowss)
                {
                    $inventory_detail_id[]=$rowss->inventory_details_id;
                }

        }

            $this->db->where('payment_id',$payment_id);
            $this->db->where('collection_id11',$id);
            $this->db->delete('inventory_payment_details_product_wise');
            }

        }


        /** END **/


        /** NOW MARK PAYMENT NOT DONE IN ALL INVENTORY DETAIL ID **/

        if(count($inventory_detail_id)>0)
        {
            foreach($inventory_detail_id as $inventory_detail_ids)
            {
            $dr=array('payment'=>0,'paymentOn'=>'0000-00-00 00:00:00','paymentBy'=>0);
            $this->db->where('id',$inventory_detail_ids);
            $this->db->update('inventory_details11',$dr);
            }               
        }
        /** END **/


        /** get collection balance and update **/

        $restey=$this->db->select('collection_amount')->from('sunder_collection_reference')->where('id',$id)->get();
            if($restey->num_rows()>0)
            {
                foreach($restey->result() as $restey1);
                $coll_amount=$restey1->collection_amount;
            }else
            {
                $coll_amount=0;
            }


            $col_bal=array('balance'=>$coll_amount,'active'=>1,'last_updatedOn'=>date('Y-m-d H;i:s'));
            $this->db->where('collection_id',$id);
            $this->db->update('sunder_collection_reference_balance',$col_bal);
            /** END **/


        /** DELETE ALL COLLECTION IF YES **/
        if($flag==1)
        {
        
            $this->db->where('id',$id);
            $this->db->delete('sunder_collection_reference');

            $this->db->where('collection_id',$id);
            $this->db->delete('sunder_collection_reference_balance');

            $this->db->where('collection_id',$id);
            $this->db->delete('sunder_collection_credit_debit');

        }
        /** END **/


        if($flag==1)
        {
        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Adjusted & Wallet Deleted</span></div><br/>');

        }else
        {

        $this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Payment Adjusted</span></div><br/>');
        }

        redirect(page_url.'Wallet');


    }
}