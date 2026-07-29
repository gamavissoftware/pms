<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_report extends CI_Controller {
  
  public function __construct()
  {
    parent::__construct();

    $this->load->model('Daily_report_model');    
  }
  
  public function report(){
    $this->load->view('Daily_report/daily_report');
        
  }

public function todays_absent(){
    $this->load->view('Daily_report/todays_absent');
}

public function todays_absent_list() {
    $leave_data = array();
    $current_date =$this->uri->segment(3); 

    // $query = $this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')
    //                   ->from('leave_application a')
    //                   ->join('system_users b','a.employee_id=b.user_id','left')
    //                   ->join('departments c','b.department_id=c.department_id','left')
    //                   ->join('user_role d','b.user_role_id=d.user_role_id','left')
    //                   ->where('a.from_loc',$current_date)
    //                   ->get();

    $query =$this->db->select('a.id,h.first_name,h.last_name, h.contact_number, c.department, d.user_role')
                      ->from('mark_your_attendance a')
                      ->join('system_users h','h.user_id=a.employee_id') 
                      ->join('departments c','h.department_id=c.department_id','left')
                      ->join('user_role d','h.user_role_id=d.user_role_id','left')
                      ->where('a.attendance_date',$current_date)
                      ->where('a.absent_status','1')
                      ->where_not_in('a.employee_id','1')
                      ->get();
    $res = $query->result();
    $i=1;
    if($query->num_rows() > 0) {
      foreach($res as $row) {

          date_default_timezone_set("Asia/Kolkata");
          $added_time = date('d-m-Y h:i A',strtotime($row->added_on));
          $leave_data[] = array(
                              'sr_no'=>$i,
                              'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
                              // 'timestamp'=>$added_time,
                              // 'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
                              // 'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
                              // 'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
                              // 'reason'=>strtoupper($row->reason),
                              // 'total_days'=>strtoupper($row->total_days),
                              'department'=>strtoupper($row->department),
                              'user_role'=>strtoupper($row->user_role),
                              'phone_no'=>strtoupper($row->contact_number)
                              // 'leave_type'=>$taskupdation,
                              // 'leave_for'=>$leavefor,
                              // 'hodstatus'=>$hodstatus
                            );
      $i++;
    }
  }
    
  $results = array(
  "sEcho" => 1,
  "iTotalRecords" => count($leave_data),
  "iTotalDisplayRecords" => count($leave_data),
  "aaData"=>$leave_data);
  echo json_encode($results);
}

public function todays_early_going(){
    $this->load->view('Daily_report/todays_early_going');
}

public function todays_early_going_list() {
    $leave_data = array();
    $current_date =$this->uri->segment(3); 

    // $query = $this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')
    //                   ->from('leave_application a')
    //                   ->join('system_users b','a.employee_id=b.user_id','left')
    //                   ->join('departments c','b.department_id=c.department_id','left')
    //                   ->join('user_role d','b.user_role_id=d.user_role_id','left')
    //                   ->where('a.from_loc',$current_date)
    //                   ->get();

    $query =$this->db->select('b.first_name, b.last_name, b.contact_number, c.department, d.user_role')
                      ->from('mark_your_attendance a')
                      ->join('system_users b','b.user_id=a.employee_id','left')
                      ->join('departments c','b.department_id=c.department_id','left')
                      ->join('user_role d','b.user_role_id=d.user_role_id','left')
                      ->where('a.attendance_date',$current_date)
                      ->where('a.absent_status','0')
                      ->where_not_in('a.employee_id','1')
                      ->where('a.evening_time <','19:00:00')
                      ->get();
    $res = $query->result();
    $i=1;
    if($query->num_rows() > 0) {
      foreach($res as $row) {

          date_default_timezone_set("Asia/Kolkata");

          $leave_data[] = array(
                              'sr_no'=>$i,
                              'employee_name'=>strtoupper($row->first_name." ".$row->last_name),
                              // 'timestamp'=>$added_time,
                              // 'leave_date'=>date('d-m-Y',strtotime($row->leave_date)),
                              // 'from_loc'=>date('d-m-Y',strtotime($row->from_loc)),
                              // 'to_loc'=>date('d-m-Y',strtotime($row->to_loc)),
                              // 'reason'=>strtoupper($row->reason),
                              // 'total_days'=>strtoupper($row->total_days),
                              'department'=>strtoupper($row->department),
                              'user_role'=>strtoupper($row->user_role),
                              'phone_no'=>strtoupper($row->contact_number)
                              // 'leave_type'=>$taskupdation,
                              // 'leave_for'=>$leavefor,
                              // 'hodstatus'=>$hodstatus
                            );
      $i++;
    }
  }
    
  $results = array(
  "sEcho" => 1,
  "iTotalRecords" => count($leave_data),
  "iTotalDisplayRecords" => count($leave_data),
  "aaData"=>$leave_data);
  echo json_encode($results);
}
  
  
  
  public function monthly_report() {
    $this->load->view('Daily_report/monthly_report');
  }


function total_dispatched() {
  $this->load->view('Daily_report/total_dispatched');
}

  function total_dispatched_list() {
    $lead_data = array();
    $current_date =$this->uri->segment(3); 

    $query = $this->db->select('t.first_name,t.last_name,a.credit_days,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
      b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.dispatch, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
              ->from('order_punch a')
              ->join('order_punch_mailing_details d','a.id=d.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b','b.id=a.quotation_id')
              ->join('store_rack_location c', 'c.id=b.company_id', 'left')
              ->join('system_users t', 't.user_id=a.agent', 'left')
              ->where('a.billing', 1)
              ->where('a.dispatch', 1)
              ->where('CAST(a.dispatched_on AS DATE)=',$current_date)
              ->order_by('a.id','DESC')
              ->get();

    $res = $query->result();
    $i=1;
    if($query->num_rows() > 0) {
    foreach($res as $row)
    {
      $customer_name='';
      $companyname='';
      $data=$this->getCustomerdetail($row->customer_id);
        if(count($data)>0)
        {
          $customer_name=$data[0];
          $companyname=$data[1];
        }

      
      $shipstate=$this->getstate($row->shipping_state);
      $billstate=$this->getstate($row->billing_state);
      $j=1;

      $shipdetail=$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


      $billdetail=$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

      $html=$this->getproducts_detail($row->orderpunchquote);
      $edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
      $order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
      $payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

      $taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

      if($row->payment_type == 2) {
        $payment_type = 'Cash';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.= '';
      } else if($row->payment_type == 3) {
        $payment_type = 'Online';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms .= '';
      } else if($row->payment_type == 4) {
        $payment_type = 'PDC';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 5) {
        $payment_type = 'CREDIT';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 6) {
        $payment_type = 'ADVANCE';
        $payment_terms="";
        $payment_terms.="";
      } else {
        $payment_type = '';
        $payment_terms='';
        $payment_terms .= '';
      }

      if($row->send_to_tally == 1) {
        $send_to_tally = "<span><strong style='color:green'>Sent To Tally</strong></span>";
      } else {
        $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
      }

      if($row->billing == 1) {
        $billing = "<span><strong style='color:green'>Billed</strong></span>";
      } else {
        $billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
      }

      $eway_bill = "<a href='javascript:;' class='btn btn-success btn-xs'>Generate Eway Bill</a>";
      
      if($row->dispatch == 1) {
        $dispatch = "<span><strong style='color:green'>Order Dispatched</strong></span>";
      } else {
        $dispatch = "<span id='dispatched".$row->id."'><input type='checkbox' name='dispatch' id='dispatch".$row->id."' value='".$row->id."' onchange='dispatch(".$row->id.")'></span>";
      }


      $billcompany=$this->getbilling_company($row->hpcl_billing_company);

      $order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";

    $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
      
      $lead_data[] = array('sr_no'=>$i,
        'lead_manager'=>$row->first_name." ".$row->last_name,
        'billing_company'=>$billcompany,
                 'company_name'=>$companyname,
                 'customer_name'=>$customer_name,
                 'taxdetail'=>$taxdetail,
                 'products'=>$html,
                 'shipaddress' =>$shipdetail,
                 'billingaddress' =>$billdetail,
                 'paymentterm' =>$payment_terms,
                 'sendtotally' => $send_to_tally,
                 'billed' => $billing,
                 'order_details' => $order_details,
                 'dispatch' => $dispatch,
                 'tax_invoice' => $tax_invoice
                 // 'payment_collection' => $payment_collection
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

    function getCustomerdetail($customerid)
  {
    $data=array();
    $r=$this->db->select('customer_name,company_name, order_max_limit,tds_appl,tds_per')->from('customer_detail')->where('id',$customerid)->get();
    if($r->num_rows()>0)
    {
      foreach($r->result() as $row);
      $data[]=$row->customer_name;
      $data[]=$row->company_name;
      $data[]=$row->order_max_limit;
      $data[]=$row->tds_appl;
      $data[]=$row->tds_per;
    }

    return  $data;

  }

    function getstate($shipstate)
  {
    $sname='';
    $resteu=$this->db->select('state_name')->from('states')->where('state_id',$shipstate)->get();
    if($resteu->num_rows()>0)
    {
    foreach($resteu->result() as $product)

    $sname=$product->state_name;

    }

    return $sname;



  }

    function getproducts_detail($quotation)
  {
    $html='<table class="table table-bordered">
    <thead>
    <tr>
    <th>Sr. No.</th>
    <th>Product</th>
    <th>Qty</th>
    <th>Agreed Price</th>
    <th>Batch Code</th>
    </tr>
    </thead>
    <tbody>';
      $res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
      if($res->num_rows()>0)
      {
        $j=1;
      foreach($res->result() as $product){



      $html.='<tr><td>'.$j.'</td>';
      $html.='<td>'.$product->instruments_name.'</td>';
      $html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
      $html.='<td>'.$product->agreed_price.'</td>';
      $html.='<td>'.$product->batch_code.'</td>';

      $j++;
      }
      $html.='</tbody></table>';
      }

      return $html;
  }

    function getbilling_company($company)
  {
    $sname='';
    $resteu=$this->db->select('companyname')->from('store_rack_location')->where('id',$company)->get();
    if($resteu->num_rows()>0)
    {
    foreach($resteu->result() as $product)

    $sname=$product->companyname;

    }

    return $sname;

  }

  function new_party_product_supplied() {
    $this->load->view('Daily_report/new_party_product_supplied');
  }

  function new_party_product_supplied_list() {
    $lead_data = array();
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    // $query = $this->db->select('a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
    //   b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
    //           ->from('order_punch a')
    //           ->join('order_punch_mailing_details d','a.id=d.order_id')
    //           ->join('order_punch_tax_details e','a.id=e.order_id')
    //           ->join('lead_source g','g.source_id=a.source','left')
    //           ->join('system_users h','h.user_id=a.agent')
    //           ->join('customer_quotation b','b.id=a.quotation_id')
    //           ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
    //           ->where('a.billing', 1)
    //           ->where('a.cancelled', 0)
    //           ->order_by('a.id','DESC')
    //           ->get();

    $sql = $this->db->select('a.billed_On, a.send_to_tally_On, a.added_on, a.invoice_no, a.po_no, a.po_date, a.credit_days, a.hpcl_billing_company, a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, b.lead_id, b.company_id, a.quotation_id as orderpunchquote, b.customer_id, c.ship_to, c.bill_to, c.shipping_phone_no, c.shipping_mobile_no, c.shipping_email, c.billing_name, c.billing_address, c.billing_state, c.billing_city, c.billing_pincode, c.billing_phone_no, c.billing_mobile_no, c.billing_email, c.shipping_name, c.shipping_address, c.shipping_state, c.shipping_city, c.shipping_pincode, d.pan_no, d.gst_no, d.msme_no, e.lead_source, f.first_name, f.last_name, g.companyname')
                    ->from('order_punch a')
                    ->join('customer_quotation b', 'b.id=a.quotation_id')
                    ->join('order_punch_mailing_details c', 'c.order_id=a.id')
                 	->join('order_punch_tax_details d','d.order_id=a.id')
                 	->join('lead_source e','e.source_id=a.source','left')
                 	->join('system_users f','f.user_id=a.agent','left')
                 	->join('store_rack_location g', 'g.id=a.hpcl_billing_company', 'left')
                    ->where('a.send_to_tally',1)
                    ->where('a.send_to_tally_On', $start_date)
                    ->get();

    if($sql->num_rows() > 0) {
    $i=1;
      foreach($sql->result() as $row) {

       

    // $res = $query->result();
    // foreach($res as $row)
    // {
      $customer_name='';
      $companyname='';
      $data=$this->getCustomerdetail($row->customer_id);
        if(count($data)>0)
        {
          $customer_name=$data[0];
          $companyname=$data[1];
        }

      
      $shipstate=$this->getstate($row->shipping_state);
      $billstate=$this->getstate($row->billing_state);
      $j=1;

    $shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


      $billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

      $html=$this->getproducts_detail($row->orderpunchquote);
      $edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
      $order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
      $payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

      $taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

      if($row->payment_type == 2) {
        $payment_type = 'Cash';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.= '';
      } else if($row->payment_type == 3) {
        $payment_type = 'Online';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms .= '';
      } else if($row->payment_type == 4) {
        $payment_type = 'PDC';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 5) {
        $payment_type = 'CREDIT';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 6) {
        $payment_type = 'ADVANCE';
        $payment_terms="";
        $payment_terms.="";
      } else {
        $payment_type = '';
        $payment_terms='';
        $payment_terms .= '';
      }

      if($row->send_to_tally == 1) {
        $send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
      } else {
        $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
      }

      if($row->billing == 1) {
        $billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
      } else {
        $billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
      }

        if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
            $po_date = date('d-m-Y', strtotime($row->po_date));
          } else {
            $po_date = '';
          }


          $po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


      $billcompany=$this->getbilling_company($row->hpcl_billing_company);

      $order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
    $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
        $cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";

      $batch_details=$this->get_batch_code_files($row->orderpunchquote);
            $query = $this->db->select('id')
                          ->from('customer_quotation')
                          ->where('customer_id', $row->customer_id)
                          ->where('id !=', $row->quotation_id)
                          ->get();

        if($query->num_rows() == 0) {
      $lead_data[] = array('sr_no'=>$i,
        'source'=>$row->lead_source, 
            'agent'=>$row->first_name." ".$row->last_name,
            'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
        'billing_company'=>$billcompany,
                 'company_name'=>$companyname,
                 'customer_name'=>$customer_name,
                 'taxdetail'=>$taxdetail,
                 'products'=>$html,
                 'shipaddress' =>$shipdetail,
                 'billingaddress' =>$billdetail,
                 'paymentterm' =>$payment_terms,
                 'sendtotally' => $send_to_tally,
                  'po_details' =>$po_details,
                 'billed' => $billing,
                 'order_details' => $order_details,
                 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
                 'cancel'=>$cancell

                 // 'payment_collection' => $payment_collection
                );

       


      $i++;
    }
     }
      }
    // }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }

  	function get_batch_code_files($quoteid)
	{
			$html='';
			$c='';
			$html1='';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quoteid)->get();
			if($res->num_rows()>0)
			{
			$j=1;
				foreach($res->result() as $product){
					if($product->batch_code<>'')
					{
						
						$rt=$this->db->select('test_report,report_file')->from('inventory_batch_no')->where('batch_no',$product->batch_code)->get();
						if($rt->num_rows()>0)
						{
							foreach($rt->result() as $rtow);
							if($rtow->test_report==1 && $rtow->report_file<>'')
							{
							if(file_exists(assets_upload."test_report/".$rtow->report_file))
							{
								$c="<a href='".page_url1."assets/test_report/".$rtow->report_file."'>".$product->batch_code."</a><br/>";
							}else
							{
								$c=$product->batch_code."<br/>";

							}

							}else
							{
								$c=$product->batch_code."<br/>";
							}

						}else{
							$c=$product->batch_code."<br/>";
						}
					}


					$html.=$c;

				}

			}else
			{
				$html='';
			}

			return $html;
	}

	function total_customers_lost() {
		$this->load->view('Daily_report/total_customers_lost');
	}

	function total_customers_lost_list() {
		$getAllPreviousMonthCustomers = $this->Daily_report_model->getAllPreviousMonthCustomers();
		$getAllCurrentMonthCustomers = $this->Daily_report_model->getAllCurrentMonthCustomers();
   // echo "<pre>";print_r($getAllPreviousMonthCustomers)."<br/>".$getAllCurrentMonthCustomers;exit;
		$lost_customer_id = array_diff($getAllPreviousMonthCustomers, $getAllCurrentMonthCustomers);
    //echo "<pre>";print_r($lost_customer_id); exit;
		 


    $lead_data = array();
$i=1;
    foreach($lost_customer_id as $rows1) {

    $sql = $this->db->select('a.billed_On, a.send_to_tally_On, a.added_on, a.invoice_no, a.po_no, a.po_date, a.credit_days, a.hpcl_billing_company, a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, b.lead_id, b.company_id, a.quotation_id as orderpunchquote, b.customer_id, c.ship_to, c.bill_to, c.shipping_phone_no, c.shipping_mobile_no, c.shipping_email, c.billing_name, c.billing_address, c.billing_state, c.billing_city, c.billing_pincode, c.billing_phone_no, c.billing_mobile_no, c.billing_email, c.shipping_name, c.shipping_address, c.shipping_state, c.shipping_city, c.shipping_pincode, d.pan_no, d.gst_no, d.msme_no, e.lead_source, f.first_name, f.last_name, g.companyname')
                    ->from('order_punch a')
                    ->join('customer_quotation b', 'b.id=a.quotation_id')
                    ->join('order_punch_mailing_details c', 'c.order_id=a.id')
                 	->join('order_punch_tax_details d','d.order_id=a.id')
                 	->join('lead_source e','e.source_id=a.source','left')
                 	->join('system_users f','f.user_id=a.agent','left')
                 	->join('store_rack_location g', 'g.id=a.hpcl_billing_company', 'left')
                    ->where('a.send_to_tally',1)
                    ->where('a.cancelled',0)
                    ->where('b.customer_id', $rows1)
                    ->order_by('a.id', 'DESC')
                    ->limit(1)
                    ->get();

    if($sql->num_rows() > 0) {
    
      foreach($sql->result() as $row) {

       

    // $res = $query->result();
    // foreach($res as $row)
    // {
      $customer_name='';
      $companyname='';
      $data=$this->getCustomerdetail($row->customer_id);
        if(count($data)>0)
        {
          $customer_name=$data[0];
          $companyname=$data[1];
        }

      
      $shipstate=$this->getstate($row->shipping_state);
      $billstate=$this->getstate($row->billing_state);
      $j=1;

    $shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


      $billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

      $html=$this->getproducts_detail($row->orderpunchquote);
      $edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
      $order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
      $payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

      $taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

      if($row->payment_type == 2) {
        $payment_type = 'Cash';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.= '';
      } else if($row->payment_type == 3) {
        $payment_type = 'Online';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms .= '';
      } else if($row->payment_type == 4) {
        $payment_type = 'PDC';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 5) {
        $payment_type = 'CREDIT';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 6) {
        $payment_type = 'ADVANCE';
        $payment_terms="";
        $payment_terms.="";
      } else {
        $payment_type = '';
        $payment_terms='';
        $payment_terms .= '';
      }

      if($row->send_to_tally == 1) {
        $send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
      } else {
        $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
      }

      if($row->billing == 1) {
        $billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
      } else {
        $billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
      }

        if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
            $po_date = date('d-m-Y', strtotime($row->po_date));
          } else {
            $po_date = '';
          }


          $po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


      $billcompany=$this->getbilling_company($row->hpcl_billing_company);

      $order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
    $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
        $cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";

      $batch_details=$this->get_batch_code_files($row->orderpunchquote);

      $lead_data[] = array('sr_no'=>$i,
        'source'=>$row->lead_source, 
            'agent'=>$row->first_name." ".$row->last_name,
            'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
        'billing_company'=>$billcompany,
                 'company_name'=>$companyname,
                 'customer_name'=>$customer_name,
                 'taxdetail'=>$taxdetail,
                 'products'=>$html,
                 'shipaddress' =>$shipdetail,
                 'billingaddress' =>$billdetail,
                 'paymentterm' =>$payment_terms,
                 'sendtotally' => $send_to_tally,
                  'po_details' =>$po_details,
                 'billed' => $billing,
                 'order_details' => $order_details,
                 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
                 'cancel'=>$cancell

                 // 'payment_collection' => $payment_collection
                );

       


      $i++;
     }
      }
     }
    // }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }

  	function total_customers_gain() {
		$this->load->view('Daily_report/total_customers_gain');
	}

	function total_customers_gain_list() {
		$getAllPreviousMonthCustomers = $this->Daily_report_model->getAllPreviousMonthCustomers();
		$getAllCurrentMonthCustomers = $this->Daily_report_model->getAllCurrentMonthCustomers();

		$gained_customer_id = array_diff($getAllCurrentMonthCustomers, $getAllPreviousMonthCustomers);
		// echo "<pre>";print_r($lost_customer_id);exit;


    $lead_data = array();
$i=1;
    foreach($gained_customer_id as $rows1) {

    $sql = $this->db->select('a.billed_On, a.send_to_tally_On, a.added_on, a.invoice_no, a.po_no, a.po_date, a.credit_days, a.hpcl_billing_company, a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, b.lead_id, b.company_id, a.quotation_id as orderpunchquote, b.customer_id, c.ship_to, c.bill_to, c.shipping_phone_no, c.shipping_mobile_no, c.shipping_email, c.billing_name, c.billing_address, c.billing_state, c.billing_city, c.billing_pincode, c.billing_phone_no, c.billing_mobile_no, c.billing_email, c.shipping_name, c.shipping_address, c.shipping_state, c.shipping_city, c.shipping_pincode, d.pan_no, d.gst_no, d.msme_no, e.lead_source, f.first_name, f.last_name, g.companyname')
                    ->from('order_punch a')
                    ->join('customer_quotation b', 'b.id=a.quotation_id')
                    ->join('order_punch_mailing_details c', 'c.order_id=a.id')
                 	->join('order_punch_tax_details d','d.order_id=a.id')
                 	->join('lead_source e','e.source_id=a.source','left')
                 	->join('system_users f','f.user_id=a.agent','left')
                 	->join('store_rack_location g', 'g.id=a.hpcl_billing_company', 'left')
                    ->where('a.send_to_tally',1)
                    ->where('a.send_to_tally_On >=', $this->uri->segment(3))
                    ->where('a.send_to_tally_On <=', $this->uri->segment(4))
                    ->where('b.customer_id', $rows1)
                    ->order_by('a.id', 'DESC')
                    ->limit(1)
                    ->get();

    if($sql->num_rows() > 0) {
    
      foreach($sql->result() as $row) {

       

    // $res = $query->result();
    // foreach($res as $row)
    // {
      $customer_name='';
      $companyname='';
      $data=$this->getCustomerdetail($row->customer_id);
        if(count($data)>0)
        {
          $customer_name=$data[0];
          $companyname=$data[1];
        }

      
      $shipstate=$this->getstate($row->shipping_state);
      $billstate=$this->getstate($row->billing_state);
      $j=1;

    $shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


      $billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

      $html=$this->getproducts_detail($row->orderpunchquote);
      $edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
      $order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
      $payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

      $taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

      if($row->payment_type == 2) {
        $payment_type = 'Cash';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.= '';
      } else if($row->payment_type == 3) {
        $payment_type = 'Online';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms .= '';
      } else if($row->payment_type == 4) {
        $payment_type = 'PDC';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 5) {
        $payment_type = 'CREDIT';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 6) {
        $payment_type = 'ADVANCE';
        $payment_terms="";
        $payment_terms.="";
      } else {
        $payment_type = '';
        $payment_terms='';
        $payment_terms .= '';
      }

      if($row->send_to_tally == 1) {
        $send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
      } else {
        $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
      }

      if($row->billing == 1) {
        $billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
      } else {
        $billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
      }

        if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
            $po_date = date('d-m-Y', strtotime($row->po_date));
          } else {
            $po_date = '';
          }


          $po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


      $billcompany=$this->getbilling_company($row->hpcl_billing_company);

      $order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
    $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
        $cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";

      $batch_details=$this->get_batch_code_files($row->orderpunchquote);

      $lead_data[] = array('sr_no'=>$i,
        'source'=>$row->lead_source, 
            'agent'=>$row->first_name." ".$row->last_name,
            'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
        'billing_company'=>$billcompany,
                 'company_name'=>$companyname,
                 'customer_name'=>$customer_name,
                 'taxdetail'=>$taxdetail,
                 'products'=>$html,
                 'shipaddress' =>$shipdetail,
                 'billingaddress' =>$billdetail,
                 'paymentterm' =>$payment_terms,
                 'sendtotally' => $send_to_tally,
                  'po_details' =>$po_details,
                 'billed' => $billing,
                 'order_details' => $order_details,
                 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
                 'cancel'=>$cancell

                 // 'payment_collection' => $payment_collection
                );

       


      $i++;
     }
      }
     }
    // }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }

    function customer_payment_stuck()
  {
    $this->load->view('Daily_report/customer_payment_stuck');
  }


  function customer_payment_stuck_list() {

    $user_id=$this->uri->segment(3);
    $currentday=date('Y-m-d');
    $lead_data = array();
     $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
              ->from('order_punch a')
              ->join('order_punch_mailing_details f','a.id=f.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b', 'b.id=a.quotation_id')
              ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
              ->join('customer_detail d', 'd.id=b.customer_id')
              ->where('a.payment_type',5)
              ->where('a.payment',0)
              ->where('a.cancelled',0)
              ->where('a.send_to_tally',1);

              if($user_id<>'')
              {
                $this->db->where('a.agent',$user_id);
              }
             $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();


      $i=1;
      if($query->num_rows() > 0) {
        foreach($query->result() as $row) {
          $html='';
          $j=1;

          

          $html=$this->getproducts_detail($row->quotation_id);

          
          $j=1;

          
          if($row->payment_type == 2) {
            $payment_type = 'Cash';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.= '';
          } else if($row->payment_type == 3) {
            $payment_type = 'Online';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms .= '';
          } else if($row->payment_type == 4) {
            $payment_type = 'PDC';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 5) {
            $payment_type = 'CREDIT';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
          }else if($row->payment_type == 6) {
            $payment_type = 'ADVANCE';
            $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
            
          } else {
            $payment_type = '';
            $payment_terms="";
            $payment_terms .= '';
          }

            if($row->credit_days!='')
            {
            $creditdays=$row->credit_days;
            }else
            {
            $creditdays=0;
            }

            $expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


            if(strtotime($currentday)>strtotime($expected_payment_days))
            {
            
           $diff = abs(strtotime($currentday) - strtotime($expected_payment_days));
        
              $exceed_days = floor($diff / (60 * 60 * 24));

            
          
            $order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
            $get_username = $this->salescrm->getusername($row->unfollow_added_by);

            $partial=$this->customer_previous_payment($row->id);
  
            $payment_due=$order_value-$partial;

            if($row->unfollow_customer == 0) {
              $do_not_followup = "<input type='checkbox' name='chk_unfollow' class='chk_unfollow".$row->id."' value='".$row->customer_id."' onchange='check_followup(".$row->id.")'>";
            } else {
              $do_not_followup = "ORDER UNFOLLOWED ON: <strong style='color:red;'>".date('d-m-Y H:i:s', strtotime($row->unfollow_added_on))."</strong><br>ORDER UNFOLLOWED BY: <strong style='color:red;'>".$get_username."</strong>";
            }

            if($exceed_days > 100) {

          $lead_data[] = array('sr_no'=>$i,

                             'agent'=>$row->first_name." ".$row->last_name,
                     'company_name'=>$row->companyname,
                     'invoice'=>$row->invoice_no,
                     'cust_company_name'=>"<strong>".$row->company_name."</strong>",
                     'customer_name'=>$row->customer_name,
                     'products'=>$html,
                     'order_value'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$order_value."</strong>",
                     'partial_payment'=>$partial,
                     'payment_due'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$payment_due."<strong>",
                     'payment_terms' =>$payment_terms,
                     'billing_date' =>date('d-M-Y',strtotime($row->send_to_tally_On)),
                     'payment_date' =>date('d-M-Y',strtotime($expected_payment_days)),
                     'exceeded_by' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>+".$exceed_days." Days</strong>",
                     'do_not_followup' => $do_not_followup
                    );
          $i++;
          }
        }
        }
      }
    $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }


  function customer_previous_payment($order_id)
  {
    $recvd=array();
    $recvd[]=0;
    $Resteuy=$this->db->select('order_amount,recieved_amount')->from('customer_order_to_payments')->where('order_id',$order_id)->get();
    if($Resteuy->num_rows()>0){
      foreach($Resteuy->result() as $row)
      {
        $recvd[]=$row->recieved_amount;
      }
    }

    return array_sum($recvd);
  }

   function stock_not_sold()
  {
    $this->load->view('Daily_report/stock_not_sold');
  }

  public function stock_not_sold_list()
  {
    $scheduler_data = array();
    // $uri=$this->uri->segment(3);
    // $query = $this->db->select('a.pack_size,a.hsncode,a.density,a.spec_file,msds_file,a.id, a.instruments_name, a.model_number, a.mvalue, a.image, a.status, a.discount_price,a.unit, trial_reading,a.stock')
    //          ->from('presto_instruments a')
    //            ->get();

    $start_date=date('Y-m-d',strtotime('- 100 Days'));
    $end_date=date('Y-m-d');
    $res = array();
    $res1 = array();

    $sql = $this->db->select('a.send_to_tally_On, b.product_id')
                    ->from('order_punch a')
                    ->join('customer_quotation_detail b', 'b.quotation_id=a.quotation_id')
                    ->where('a.send_to_tally',1)
                    ->where('a.send_to_tally_On !=','0000-00-00')
                    ->where('a.send_to_tally_On >=', $start_date)
                    ->where('a.send_to_tally_On <=', $end_date)
                    ->get();

    if($sql->num_rows() > 0) {
      foreach($sql->result() as $row1) {
          $res[] = $row1->product_id;
      }
    }

    $customer_products = array_unique($res);

    $query = $this->db->select('pack_size, hsncode, density, spec_file,msds_file, id,  instruments_name,  model_number,  mvalue,  image,  status,  discount_price, unit, trial_reading, stock')
                      ->from('presto_instruments')
                      ->where('status',1)
                      ->where_not_in('id', $customer_products)
                      ->get();

    // if($sql1->num_rows() > 0) {
    //   foreach($sql1->result() as $row1) {
    //     $res1[] = $row1->id;
    //   }
    // }
              

    $i=1;
    if($query->num_rows() > 0) {
      foreach($query->result() as $row) {
        date_default_timezone_set("Asia/Kolkata");
      
      if($row->status == 1) {
        $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
      } else {
        $sta =  "<a href='".page_url."FMS/update_instruments_status/".$row->id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
      }

      if($row->image <> '' || $row->image <> 0) {
        $image="<img src='".instrumentimg.$row->image."' style='width:100px'>";
      } else {
        $image="Image not found";
      }
      
      $getSelectedSubParts = $this->master->getSelectedSubParts($row->id);
      //$getSelectedLocations = $this->master->getSelectedLocations($row->id);
      
      $edit = "<a href='".page_url."FMS/edit_instruments/".$row->id."'><i class='fa fa-pencil'></i></a>";
      $getProductWiseCompanies = $this->master->getProductWiseCompanies($row->id);
      $sub_parts =  "<p>".$getSelectedSubParts."</p><br><a href='".page_url."Master/Finished_goods/fin_good_sub_parts/".$row->id."'>SUB PARTS</a>";
      $bom =  "<a href='".page_url."Store/bom/".$row->id."'><span class='btn btn-warning btn-xs'>BOM</span></a>";

      if($row->trial_reading == 1) {
        $trial_reading = 'APPLICABLE';
      } else {
        $trial_reading = 'NOT APPLICABLE';
      }



            if($row->spec_file<>'')
            {
              $spec="<a href='".page_url1."image_bank/instrumentimg/".$row->spec_file."' download>Download</a>";
            }else
            {
              $spec="Not Available";
            }

            if($row->msds_file<>'')
            {
              $msds="<a href='".page_url1."image_bank/instrumentimg/".$row->msds_file."' download>Download</a>";
            }else
            {
              $msds="Not Available";
            }

          $details=$this->Daily_report_model->lastSoldOn($row->id);
          if(count($details)>0)
          {
            $customer_name=$details[0];
            $invoice=$details[1];
            $odate=$details[2];
            $d="<strong style='color:red;font-weight:bold;'>".$customer_name."<br/>Invoice ".$invoice."<br/>Billed On ".$odate;
        
          }else
          {
            $d='-';
          }
            
          $scheduler_data[] = array(
          'sr_no' => $i,
          'instruments_name' => strtoupper($row->instruments_name),
          'model_number' => $row->model_number,
          'last_sold' =>$d,
          'unit' => $row->unit,
          'hsn'=>$row->hsncode,
          'pack_size'=>$row->pack_size,
          'density'=>$row->density,
          'mvalue' => $row->mvalue,
          'discount_price' => $row->discount_price,
          'image' => $image,
          'locations' => $getProductWiseCompanies,       
          'stock' => floatval($row->stock),
          'bom' => $bom,
          'specfile'=>$spec,
          'msds'=>$msds,
          'status' => $sta,
          'trial_reading' => $trial_reading,
          'edit' => $edit
          );
            $i++;
    }
  }
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($scheduler_data),
    "iTotalDisplayRecords" => count($scheduler_data),
    "aaData"=>$scheduler_data);
    echo json_encode($results);
}


function total_billed() {
  $this->load->view('Daily_report/total_billed');
}


 function total_billed_list() {
    $lead_data = array();
    $start_date =$this->uri->segment(3); 
    $end_date =$this->uri->segment(4); 
    $user =$this->uri->segment(5); 

     $this->db->select('t.first_name,t.last_name,a.credit_days,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
      b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.dispatch, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
              ->from('order_punch a')
              ->join('order_punch_mailing_details d','a.id=d.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b','b.id=a.quotation_id')
              ->join('store_rack_location c', 'c.id=b.company_id', 'left')
              ->join('system_users t', 't.user_id=a.agent', 'left')
              ->where('a.send_to_tally', 1)
              ->where('a.direct_order', 1)
              ->where('a.send_to_tally_On>=',$start_date)
              ->where('a.send_to_tally_On<=',$end_date);
              if($user<>'' && $user<>'ALL')
              {
                $this->db->where('a.agent',$user);
              }

             $query =$this->db->order_by('a.id','DESC')->get();

    $res = $query->result();
    $i=1;
    if($query->num_rows() > 0) {
    foreach($res as $row)
    {
      $customer_name='';
      $companyname='';
      $data=$this->getCustomerdetail($row->customer_id);
        if(count($data)>0)
        {
          $customer_name=$data[0];
          $companyname=$data[1];
        }

      
      $shipstate=$this->getstate($row->shipping_state);
      $billstate=$this->getstate($row->billing_state);
      $j=1;

      $shipdetail=$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


      $billdetail=$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

      $html=$this->getproducts_detail($row->orderpunchquote);
      $edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
      $order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
      $payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

      $taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

      if($row->payment_type == 2) {
        $payment_type = 'Cash';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.= '';
      } else if($row->payment_type == 3) {
        $payment_type = 'Online';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms .= '';
      } else if($row->payment_type == 4) {
        $payment_type = 'PDC';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 5) {
        $payment_type = 'CREDIT';
        $payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
        $payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
      } else if($row->payment_type == 6) {
        $payment_type = 'ADVANCE';
        $payment_terms="";
        $payment_terms.="";
      } else {
        $payment_type = '';
        $payment_terms='';
        $payment_terms .= '';
      }

      if($row->send_to_tally == 1) {
        $send_to_tally = "<span><strong style='color:green'>Sent To Tally</strong></span>";
      } else {
        $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
      }

      if($row->billing == 1) {
        $billing = "<span><strong style='color:green'>Billed</strong></span>";
      } else {
        $billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
      }

      $eway_bill = "<a href='javascript:;' class='btn btn-success btn-xs'>Generate Eway Bill</a>";
      
      if($row->dispatch == 1) {
        $dispatch = "<span><strong style='color:green'>Order Dispatched</strong></span>";
      } else {
        $dispatch = "<span id='dispatched".$row->id."'><input type='checkbox' name='dispatch' id='dispatch".$row->id."' value='".$row->id."' onchange='dispatch(".$row->id.")'></span>";
      }


      $billcompany=$this->getbilling_company($row->hpcl_billing_company);

      $order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";

    $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
      
      $lead_data[] = array('sr_no'=>$i,
        'lead_manager'=>$row->first_name." ".$row->last_name,
        'billing_company'=>$billcompany,
                 'company_name'=>$companyname,
                 'customer_name'=>$customer_name,
                 'taxdetail'=>$taxdetail,
                 'products'=>$html,
                 'shipaddress' =>$shipdetail,
                 'billingaddress' =>$billdetail,
                 'paymentterm' =>$payment_terms,
                 'sendtotally' => $send_to_tally,
                 'billed' => $billing,
                 'order_details' => $order_details,
                 'dispatch' => $dispatch,
                 'tax_invoice' => $tax_invoice
                 // 'payment_collection' => $payment_collection
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

  function filtermonthreport()
  {
    $stdate=date('Y-m-d',strtotime($this->input->post('stdate')));
    $endate=date('Y-m-d',strtotime($this->input->post('endate')));
    

    redirect(page_url.'Daily_report/monthly_report/'.$stdate.'/'.$endate);
  }

  function filter_daily_report()
  {
    $stdate=date('Y-m-d',strtotime($this->input->post('sdate')));
      redirect(page_url.'Daily_report/report/'.$stdate);
  }

}