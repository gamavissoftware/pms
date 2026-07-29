
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Daily_report_model extends CI_Model {
  public function __construct() {
    parent::__construct();
    $this->load->model('Dashboard_model','dashboardmodel');
    $this->load->model('Salescrm_model');
  }

  function visit_counts_userwise_today($create_date)
  {
    // $res=$this->db->select('id')->from('daily_visits')->where('converted',0)->where('added_by',$userid)->where('create_date',date('Y-m-d'))->get();

    $res=$this->db->select('id')->from('daily_visits')->where('create_date',$create_date)->where('converted',0)->get();

    $res1=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',date('Y-m-d')." 00:00:00")->where('added_on<=',date('Y-m-d')." 23:59:59")->get();
    return $res->num_rows()+$res1->num_rows();
  }

  function visit_agent_list($create_date){
    $user_array = array();
    $user_array2 = array();
    $us1=array();
    $dus1=array();
    $query = $this->db->select('a.id,h.user_id,h.first_name,h.last_name')
                      ->from('daily_visits a')             
                      ->join('system_users h','h.user_id=a.added_by') 
                      ->where('create_date',$create_date)
                      ->group_by('a.added_by')
                      ->get();      


      if($query->num_rows() > 0){

        foreach($query->result() as $row){
        $us1[]=$row->user_id;
         
          }
       
      }


        $res=$this->db->select('added_by')->from('oldcustomer_visit')->where('added_on>=',$create_date." 00:00:00")->where('added_on<=',$create_date." 23:59:59")->get();
        if($res->num_rows()>0)
        {

        foreach($res->result() as $row){
        $us1[]=$row->added_by;
         
          }

        }

        $dus1=array_values(array_unique($us1));


        for($t=0;$t<count($dus1);$t++)
        {
          $query11 =  $query = $this->db->select('a.id')
              ->from('daily_visits a') 
              ->where('a.added_by',$dus1[$t])
              ->where('create_date',$create_date)
              ->get();  

            $query12=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',$create_date." 00:00:00")->where('added_on<=',$create_date." 23:59:59")->where('added_by',$dus1[$t])->get();

            $ret=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$dus1[$t])->get();
            if($ret->num_rows()>0){
              foreach($ret->result() as $rett);
              $fname=$rett->first_name;
              $lname=$rett->last_name;
            }else{ $fname=''; $lname=''; }

            $user_array['name'] = $fname." ".$lname;
            $user_array['visit_count'] = $query11->num_rows()+$query12->num_rows(); 
            $user_array2[] = $user_array;


        }



       
       // foreach($query->result() as $row){
          //   $query2 =  $query = $this->db->select('a.id')
          //     ->from('daily_visits a') 
          //     ->where('a.added_by',$row->user_id)
          //     ->where('create_date',$create_date)
          //     ->get();    

          //   // $user_array['user_id'] = $row->user_id;
          // $user_array['name'] = $row->first_name." ".$row->last_name;
          //   $user_array['visit_count'] = $query2->num_rows(); 
          //   $user_array2[] = $user_array;
      return $user_array2;
  }

  function on_leave_today($date){
    $leave_today = [];
    $query =$this->db->select('a.id,h.first_name,h.last_name')
                      ->from('mark_your_attendance a')
                      ->join('system_users h','h.user_id=a.employee_id') 
                      ->where('a.attendance_date',$date )
                      ->where('a.absent_status','1')
                      ->where_not_in('a.employee_id','1')
                      ->get();
         
      if($query->num_rows() > 0){
            $leave_today = $query->result();
           }

      return $leave_today;
  }

  function early_going($date){
    $query =$this->db->select('a.id')
                      ->from('mark_your_attendance a')
                      ->where('a.attendance_date',$date )
                      ->where('a.absent_status','0')
                      ->where_not_in('a.employee_id','1')
                      ->where('evening_time <','19:00:00')
                      ->get();
         $report_early_going= count($query->result());

      return $report_early_going;
  }
  function early_going_user($date){

    $early_going_user = [];
    $ignore_users = array(1,25);

    $query =$this->db->select('a.id,h.first_name,h.last_name,a.evening_time')
                      ->from('mark_your_attendance a')
                      ->join('system_users h','h.user_id=a.employee_id') 
                      ->where('a.attendance_date',$date)
                      ->where('a.absent_status','0')
                      ->where_not_in('a.employee_id', $ignore_users)
                      ->where('a.evening_time <','18:00:00')
                      ->where('a.evening_time!=','00:00:00')
                      ->get();

           if($query->num_rows() > 0){
            $early_going_user = $query->result();
           }

      return $early_going_user;
  }

  function no_punch_out($date){

    $early_going_user = [];
    $ignore_users = array(1,25);

    $query =$this->db->select('a.id,h.first_name,h.last_name,a.evening_time')
                      ->from('mark_your_attendance a')
                      ->join('system_users h','h.user_id=a.employee_id') 
                      ->where('a.attendance_date',$date)
                      ->where('a.absent_status','0')
                      ->where_not_in('a.employee_id', $ignore_users)
                      ->where('a.evening_time','00:00:00')
                      ->get();

           if($query->num_rows() > 0){
            $early_going_user = $query->result();
           }

      return $early_going_user;
  }


  function total_dispatch_today($date){
    $total = [];
    $total_qty = [];
    $user_array2 = array();
    $query = $this->db->select('a.id,a.quotation_id')
              ->from('order_punch a')              
              ->where('a.dispatch',1)
              ->where('CAST(a.dispatched_on AS DATE)=',$date)
              ->get();
    $c = $query->num_rows();
    if($c > 0){
      foreach($query->result() as $order){
        $orderamt = $this->Salescrm_model->getOrderAmountWithoutGST($order->quotation_id);
        $orderqty = $this->Salescrm_model->getOrderWitqty($order->quotation_id);
        $total[] = $orderamt;
        $total_qty[] = $orderqty;
      }
    }


    return $c."|".array_sum($total)."|".array_sum($total_qty);

  }

  function pending_order_for_billing()
  {
     $total = [];
      $query = $this->db->select('a.id,a.quotation_id')
              ->from('order_punch a')
              ->join('order_punch_mailing_details d','a.id=d.order_id')
              ->join('order_punch_tax_details f','a.id=f.order_id')
               ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b','b.id=a.quotation_id')
              ->join('store_rack_location c', 'c.id=b.company_id', 'left')
              ->where('a.billing', 0)
              ->where('a.cancelled', 0)
              ->order_by('a.id','DESC')
              ->get();
        $c = $query->num_rows();
          
          if($c > 0){
            foreach($query->result() as $order){
              $orderamt = $this->Salescrm_model->getOrderAmountWithoutGST($order->quotation_id);
              
              $total[] = $orderamt;
            }
          }


          return $c." : ".array_sum($total);
  }


  function all_payment_overdue() {

 
    $currentday=date('Y-m-d');
    $lead_data = array();
     $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
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
              // ->group_by('company_name');

             $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();


      $i=0;
      if($query->num_rows() > 0) {
        foreach($query->result() as $row) {
          
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
            
          $i++;
        }
        }
      }
    
    return $i;

    
  }



  function all_payment_overdue_amount() {

   
    $currentday=date('Y-m-d');
    $lead_data = array();
    $payment_overdue_amount = array();
     $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
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

             $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();


      $i=1;
      if($query->num_rows() > 0) {
        foreach($query->result() as $row) {

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

            
            $order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);

          $partial=$this->customer_previous_payment($row->id);
          $payment_overdue_amount[] = $order_value-$partial;

          $i++;
        }
        }
      }
   
      
    return array_sum($payment_overdue_amount);
  }

function sampleCollected($date){
  $res= '';
  $query = $this->db->select('lead_id')
              ->from('lead_stage')              
              ->where('sample',1)
              ->get();
    if($query->num_rows() > 0){
      $sample_lead_id = $query->row()->lead_id;
     
      $query2 = $this->db->select('id')
              ->from('progress_remarks')              
              ->where('lead_status',$sample_lead_id )
              ->where('CAST(added_on AS DATE)=',$date)
              ->group_by('lead_id')
              ->get();
        $res =  $query2->num_rows();
    }
    return $res;
 }

 function getTrialsStartedToday($current_date) {
    $date = date('Y-m-d', strtotime($current_date));
    $query = $this->db->select('a.id, e.first_name, e.last_name, c.instruments_name, a.lead_id, a.lead_product_id, a.status, a.requestOn, a.request_remarks, a.request_by, c.trial_reading, d.customer_name, d.contact_no, d.city, d.company_name, d.postal_address,d.alt_contact,d.alt_contact_no,d.title')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.status',0)->where('a.requestOn', $date)->get();

    return $query->num_rows();
 }

 function getTrialSuccessfulLeadStage() {
  $res = 0;
  $sql = $this->db->select('lead_id')
                  ->from('lead_stage')
                  ->where('trial_successful', 1)
                  ->get();

  if($sql->num_rows() > 0) {
    foreach($sql->result() as $row);
    $res = $row->lead_id;
  }

  return $res;
 }

 function getTrialsApprovedToday($current_date) {
    $start_date = date('Y-m-d', strtotime($current_date)).' 00:00:00';
    $end_date = date('Y-m-d', strtotime($current_date)).' 23:59:59';
    $lead_stage = $this->getTrialSuccessfulLeadStage();

    $resty=$this->db->query("SELECT b.alt_contact,b.alt_contact_no,b.postal_address,a.nonqualifiedreason,a.id,a.added_on,a.added_by,b.lead_source_id,b.country_code,b.id as leadid,a.remarks,b.contact_no,b.alt_contact_no,b.patient_type_id,b.remarks as clientremarks,b.customer_name,b.contact_person,b.email_id,b.mobile_no,b.company_name,b.unique_id,b.email,b.create_date,b.country,b.state,b.city, b.other_business, b.client_location,a.added_on as lastupdatedOn,a.added_by as updatedby  FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($lead_stage) AND a.added_on>='$start_date' AND a.added_on<='$end_date' AND b.closed=0 GROUP BY b.id  ORDER BY b.id DESC");

    return $resty->num_rows();
 }

 function getBulkPurchaseDensity() {
   $res = array();
   $sql = $this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->get();

   if($sql->num_rows() > 0) {
      $res = $sql->result();
   }

   return $res;
 }

 function getNewPartyProductSupplied($current_date) {
    $data = array();
    $start_date = date('Y-m-d', strtotime($current_date)).' 00:00:00';
    $end_date = date('Y-m-d', strtotime($current_date)).' 23:59:59';

    $sql = $this->db->select('a.quotation_id, b.customer_id')
                    ->from('order_punch a')
                    ->join('customer_quotation b', 'b.id=a.quotation_id')
                    ->where('a.send_to_tally',1)
                    ->where('a.send_to_tally_On >=', $start_date)
                    ->where('a.send_to_tally_On <=', $end_date)
                    ->get();

    if($sql->num_rows() > 0) {
      foreach($sql->result() as $row) {
        $sql1 = $this->db->select('id')
                         ->from('customer_quotation')
                         ->where('customer_id', $row->customer_id)
                         ->where('id !=', $row->quotation_id)
                         ->get();

        if($sql1->num_rows() == 0) {
          $data[] = 1;
        }
      }
    }

    return array_sum($data);
 }

 function getPaymentStatusChange($current_date) {
    $start_date = date('Y-m-d', strtotime($current_date)).' 00:00:00';
    $end_date = date('Y-m-d', strtotime($current_date)).' 23:59:59';

    $sql = $this->db->select('a.id')
                    ->from('customer_payment_particulars a')
                    ->join('customer_payments b','a.payment_id=b.id')
                    ->join('customer_detail c','b.customer_id=c.id')
                    ->where('addedOn >=', $start_date)
                    ->where('addedOn <=', $end_date)
                    ->get();

    return $sql->num_rows();
 }

 function getApprovalPendingFromMgt() {
  $discount_approval = array();
  $payment_approval = array();
  $scheduler_data = array();
  $scheduler_data[]=0;


      $sql1 = $this->db->select('a.*,b.first_name,b.last_name')->from('equivalent_chart
 a')->join('system_users b','a.addedby=b.user_id','left')->where('a.approved',0)->where('client_product!=','NA')->where('client_product!=','')->get();

      $equivalent_chart_approval = $sql1->num_rows();

      $sql2 = $this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('status',0)->get();

      $specfile_approval = $sql2->num_rows();

      $sql31 = $this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price')
                        ->from('customer_quotation_detail a')
                        ->join('customer_quotation b', 'b.id=a.quotation_id')
                        ->join('store_rack_location c', 'c.id=b.company_id')
                        ->join('customer_detail d', 'd.id=b.customer_id')
                        ->join('presto_instruments e', 'e.id=a.product_id')
                        ->where('a.flag', 0)
                        ->get();

      $discount_approval[] = $sql31->num_rows();

      $sql32 = $this->db->select('a.id as detail_id, a.qty, a.price as list_price, b.customer_name, b.company_name, c.companyname, e.instruments_name, e.discount_price')
                        ->from('lead_products a')
                        ->join('leads b', 'b.id=a.lead_id')
                        ->join('store_rack_location c', 'c.id=b.hpcl_company')
                        ->join('presto_instruments e', 'e.id=a.product_id')
                        ->where('a.flag', 0)
                        ->get();

      if($sql32->num_rows() > 0) {
        foreach($sql32->result() as $row) {
          if($row->list_price != '' && $row->list_price > 0) {
              $discount_approval[] = 1;
          }
        }
      }

      $total_discount_approval = array_sum($discount_approval);

      $this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,d.first_name,d.last_name,a.added_on')->from('customer_detail a');
      $this->db->join('store_rack_location c','a.company_id=c.id');
      $this->db->join('system_users d','d.user_id=a.added_by','left');
      $this->db->where('a.payment_term_approval','0');
      $this->db->order_by('a.added_on','DESC');
      $sql41 = $this->db->get();

      $payment_approval[] = $sql41->num_rows();

      $sql42=$this->db->select('a.id,a.customer_id,b.customer_name,a.payment_type,a.credit_days,a.addedOn,a.addedBy,b.company_name,c.companyname,b.payment_type as current_type,b.credit_days as current_days,d.first_name,d.last_name')->from('payment_change_req a')->join('customer_detail b','a.customer_id=b.id')->join('store_rack_location c','b.company_id=c.id')->join('system_users d','a.addedBy=d.user_id')->where('a.status',0)->get();

      $payment_approval[] = $sql42->num_rows();

      $total_payment_approval = array_sum($payment_approval);

      for ($l = -6; $l <= 0; $l++) {
        $m1=date('Y-m', strtotime("$l month"));
        $start_date=date($m1.'-01');
        $end_date=date($m1.'-t');

        $q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
    
        if($q->num_rows()>0) {
          foreach($q->result() as $q1) {
            $query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();

            if($query->num_rows()>0) {
              $res = $query->result();
              $i=1;   
              
              foreach($res as $row) { 
                $scheduler_data[] =1;
              }
            }
          }
        }
      }

      $total_conveyance_approval = array_sum($scheduler_data);

       $sql5 = $this->db->select('a.id')
                        ->from('order_punch a')
                        ->join('order_punch_mailing_details d','a.id=d.order_id')
                        ->join('order_punch_tax_details f','a.id=f.order_id')
                        ->join('lead_source g','g.source_id=a.source','left')
                        ->join('system_users h','h.user_id=a.agent')
                        ->join('order_punch_tax_details e','a.id=e.order_id')
                        ->join('customer_quotation b','b.id=a.quotation_id')
                        ->join('store_rack_location c', 'c.id=b.company_id', 'left')
                        ->join('system_users i','i.user_id=a.adjustmentBy')
                        ->where('a.billing', 1)
                        ->where('a.payment', 0)
                        ->where('a.adjustment',1)
                        ->where('a.adjustment_approval',0)
                        ->order_by('a.id','DESC')
                        ->get();

      $total_adjustment_approval = $sql5->num_rows();

       $sql6 = $this->db->select('a.id')
                        ->from('order_punch a')
                        ->join('order_punch_mailing_details d','a.id=d.order_id')
                        ->join('order_punch_tax_details f','a.id=f.order_id')
                        ->join('lead_source g','g.source_id=a.source','left')
                        ->join('system_users h','h.user_id=a.agent')
                        ->join('order_punch_tax_details e','a.id=e.order_id')
                        ->join('customer_quotation b','b.id=a.quotation_id')
                        ->join('store_rack_location c', 'c.id=b.company_id', 'left')
                        ->where('a.cancelled', 0)
                        ->where('a.billing', 0)
                        ->order_by('a.id','DESC')
                        ->get();

      $total_orders_on_hold = $sql6->num_rows();

      $sql7 = $this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->get();

      $total_density_approval = $sql7->num_rows();

      $rowss = $this->db->select('a.id')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->join('leads d','a.lead_id=d.id')->join('system_users e','e.user_id=a.request_by')->where('a.approved',0)->get();
      $totaltrail = $rowss->num_rows();

      return $equivalent_chart_approval.'|'.$specfile_approval.'|'.$total_discount_approval.'|'.$total_payment_approval.'|'.$total_conveyance_approval.'|'.$total_adjustment_approval.'|'.$total_orders_on_hold.'|'.$total_density_approval.'|'.$totaltrail;
    }

    function getPDCToBeCollected() {
      $sql = $this->db->select('a.id,b.quotation_id,b.invoice_no,a.id, a.expected_pdc_date, d.company_name,e.gst,f.gst_no,b.payment_type,b.credit_days,d.customer_name,h.first_name,h.last_name,i.shipping_mobile_no')
            ->from('customer_cheque_details a')
            ->join('order_punch b', 'b.id=a.order_id')
            ->join('order_punch_tax_details f', 'f.order_id=b.id')
            ->join('order_punch_mailing_details i', 'i.order_id=b.id')
            ->join('customer_quotation c', 'c.id=b.quotation_id')
            ->join('customer_detail d', 'd.id=c.customer_id')
            ->join('store_rack_location e', 'e.id=b.hpcl_billing_company')
            ->join('system_users h','h.user_id=b.agent')
            ->where('a.received', 0)
            ->get();

        return $sql->num_rows();
    }

    function getAllSalesUsers() {
       $res = '';
       $sql = $this->db->select('user_id, first_name, last_name')
                       ->from('system_users')
                       ->where('marketing_person', 1)
                       ->get();

        if($sql->num_rows() > 0) {
          $res = $sql->result();
        }

        return $res;
    }

    function getQuotationSentLeadStage() {
      $res = '';
      $sql = $this->db->select('lead_id')
                      ->from('lead_stage')
                      ->where('quotation_step', 1)
                      ->get();

       if($sql->num_rows() > 0) {
          foreach($sql->result() as $row);
          $res = $row->lead_id;
        }

        return $res;     
    }

    function getQuotationRevisedLeadStage() {
      $res = '';
      $sql = $this->db->select('lead_id')
                      ->from('lead_stage')
                      ->where('quotation_revised_step', 1)
                      ->get();

       if($sql->num_rows() > 0) {
          foreach($sql->result() as $row);
          $res = $row->lead_id;
        }

        return $res;     
    }

    function getLeadConversionLeadStage() {
      $res = '';
      $sql = $this->db->select('lead_id')
                      ->from('lead_stage')
                      ->where('conversion_step', 1)
                      ->get();

       if($sql->num_rows() > 0) {
          foreach($sql->result() as $row);
          $res = $row->lead_id;
        }

        return $res;     
    }

    function getTodaysQuotationSent($current_date, $user_id, $lead_status) {
        $start_date = date('Y-m-d', strtotime($current_date)).' 00:00:00';
        $end_date = date('Y-m-d', strtotime($current_date)).' 23:59:59';

        $sql = $this->db->select('a.lead_id')
                        ->from('progress_remarks a')
                        ->join('leads b', 'b.id=a.lead_id')
                        ->where('a.added_on >=', $start_date)
                        ->where('a.added_on <=', $end_date)
                        ->where('a.lead_status', $lead_status)
                        ->where('b.added_by', $user_id)
                        ->group_by('a.lead_id')
                        ->get();

        return $sql->num_rows();
    }

    function getMonthQuotationSent($start_date, $end_date, $user_id, $lead_status) {
        $startdate = date('Y-m-d', strtotime($start_date)).' 00:00:00';
        $enddate = date('Y-m-d', strtotime($end_date)).' 23:59:59';

        $sql = $this->db->select('a.lead_id')
                        ->from('progress_remarks a')
                        ->join('leads b', 'b.id=a.lead_id')
                        ->where('a.added_on >=', $startdate)
                        ->where('a.added_on <=', $enddate)
                        ->where('a.lead_status', $lead_status)
                        ->where('b.added_by', $user_id)
                        ->group_by('a.lead_id')
                        ->get();

        return $sql->num_rows();
    }

     function total_monthly_sales($start_date, $end_date) {
        $total = [];
        $total_qty = [];
        $user_array2 = array();
        $query = $this->db->select('id,quotation_id')
                          ->from('order_punch')              
                          ->where('billing',1)
                          ->where('cancelled',0)
                          ->where('CAST(billed_On AS DATE) >=',$start_date)
                          ->where('CAST(billed_On AS DATE) <=',$end_date)
                          ->get();
        $c = $query->num_rows();
        if($c > 0){
          foreach($query->result() as $order){
            $orderamt = $this->Salescrm_model->getOrderAmountWithoutGST($order->quotation_id);
            $orderqty = $this->Salescrm_model->getOrderWitqty($order->quotation_id);
            $total[] = $orderamt;
            $total_qty[] = $orderqty;
          }
        }


        return $c."|".array_sum($total)."|".array_sum($total_qty);

     }

     function getPreviousYearCurrentSales($start_date, $end_date) {
        $salesno=$this->get_sales_numbers($start_date, $end_date);
        $salesqty=$this->get_sales_qty($start_date, $end_date);

        return $salesno."|".$salesqty;
     }

    function get_sales_numbers($startdate,$enddate) {

      $sale_price=array();
      $sale_price[]=0;
      $startdate=$startdate." 00:00:00";
      $enddate=$enddate." 23:59:59";
      
      $query = $this->db->select('a.quotation_id as orderpunchquote')
                ->from('order_punch a')
                ->join('order_punch_mailing_details d','a.id=d.order_id')
                ->join('order_punch_tax_details e','a.id=e.order_id')
                ->join('lead_source g','g.source_id=a.source','left')
                ->join('system_users h','h.user_id=a.agent')
                ->join('customer_quotation b','b.id=a.quotation_id')
                ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
                ->where('a.billing', 1)
                ->where('a.cancelled',0)
                ->where('a.billed_On>=',$startdate)
                ->where('a.billed_On<=',$enddate)
                ->get();

      $res = $query->result();
      $i=1;
      if($query->num_rows()>0)
      {
      foreach($res as $row)
      {
        $sale_price[]=$this->getproducts_detail($row->orderpunchquote);
      }
      }

      return array_sum($sale_price);
  }

  function get_sales_qty($startdate,$enddate) {

      $sale_price=array();
      $sale_price[]=0;
      $startdate=$startdate." 00:00:00";
      $enddate=$enddate." 23:59:59";
      
      $query = $this->db->select('a.quotation_id as orderpunchquote')
                ->from('order_punch a')
                ->join('order_punch_mailing_details d','a.id=d.order_id')
                ->join('order_punch_tax_details e','a.id=e.order_id')
                ->join('lead_source g','g.source_id=a.source','left')
                ->join('system_users h','h.user_id=a.agent')
                ->join('customer_quotation b','b.id=a.quotation_id')
                ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
                ->where('a.billing', 1)
                ->where('a.cancelled',0)
                ->where('a.billed_On>=',$startdate)
                ->where('a.billed_On<=',$enddate)
                ->get();

      $res = $query->result();
      $i=1;
      if($query->num_rows()>0)
      {
      foreach($res as $row)
      {
        $sale_price[]=$this->getproducts_detail_by_qty($row->orderpunchquote);
      }
      }

      return array_sum($sale_price);
  }

  function getproducts_detail($quotation)
  {
      $price=array();
      $price[]=0;
      $res=$this->db->select('a.agreed_price,a.qty')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
      if($res->num_rows()>0)
      {
        $j=1;
      foreach($res->result() as $product){
      
      $price[]=round($product->agreed_price*$product->qty);

      $j++;
      }
    
      }

      return array_sum($price);
  }

      function getproducts_detail_by_qty($quotation)
  {
      $price=array();
      $price[]=0;
      $res=$this->db->select('a.agreed_price,a.qty')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
      if($res->num_rows()>0)
      {
        $j=1;
      foreach($res->result() as $product){
      
      $price[]=round($product->qty);

      $j++;
      }
    
      }

      return array_sum($price);
  }

  function getAllPreviousMonthCustomers() {
    $d=date('Y-m');
    $dold=date('Y-m',strtotime('-1 Month',strtotime($d)));
      $start_month = date('Y-m-01',strtotime($dold)).' 00:00:00';
      $end_month = date('Y-m-t',strtotime($start_month)).' 23:59:59';
      
      $res = array();
      $query = $this->db->select('b.customer_id')
                        ->from('order_punch a')
                        ->join('customer_quotation b','b.id=a.quotation_id')
                        ->where('a.billing', 1)
                       ->where('a.cancelled', 0)
                        ->where('a.billed_On >=', $start_month)
                        ->where('a.billed_On <=', $end_month)
                        ->group_by('b.customer_id')
                        ->get();

      if($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
          $res[] = $row->customer_id;
        }
      }

      return $res;
  }

  function getAllCurrentMonthCustomers() {
     $start_month = date('Y-m-01').' 00:00:00';
     $end_month = date('Y-m-t').' 23:59:59';

      $res = array();
      $query = $this->db->select('b.customer_id')
                        ->from('order_punch a')
                        ->join('customer_quotation b','b.id=a.quotation_id')
                        ->where('a.billing', 1)
                        ->where('a.cancelled', 0)
                        ->where('a.billed_On >=', $start_month)
                        ->where('a.billed_On <=', $end_month)
                        ->group_by('b.customer_id')
                        ->get();

      if($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
          $res[] = $row->customer_id;
        }
      }

      return $res;
  }

  function getAllUsers() {
    $res = '';
    $sql = $this->db->select('user_id, first_name, last_name')
                    ->from('system_users')
                    ->where('user_status', 1)
                    ->where('user_role_id !=', 1)
                    ->get();


    if($sql->num_rows() > 0) {
          $res = $sql->result();
    }

      return $res;
  }

  function getPaymentOverdue() {
   $currentday=date('Y-m-d');
   $data = array();

   $sql = $this->db->select('a.invoice_no, a.send_to_tally_On, g.lead_source, h.first_name, h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
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
              ->where('a.send_to_tally',1)
              ->where('a.send_to_tally_On !=','0000-00-00')
              ->order_by('a.id','DESC')
              ->get();

       if($sql->num_rows() > 0) {
          foreach($sql->result() as $row) {

            if($row->credit_days!='') {
              $creditdays=$row->credit_days;
            } else {
              $creditdays=0;
            }

            $expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));

           $diff = abs(strtotime($currentday) - strtotime($expected_payment_days));
        
              $exceed_days = floor($diff / (60 * 60 * 24));

            if($exceed_days > 100) {
              $data[] = 1;
            }
          }
        }

        return array_sum($data);
  }

  function getStockNotSold() {
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
      foreach($sql->result() as $row) {
          $res[] = $row->product_id;
      }
    }

    $customer_products = array_unique($res);

    $sql1 = $this->db->select('id')
                     ->from('presto_instruments')
                     ->where('status',1)
                     ->where_not_in('id', $customer_products)
                     ->get();

    if($sql1->num_rows() > 0) {
      foreach($sql1->result() as $row1) {
        $res1[] = $row1->id;
      }
    }

    return count($res1);

  }

  function getTodaysVisit($today,$user_id)
  {
    $r=$this->db->select('id')->from('daily_visits')->where('create_date',$today)->where('added_by',$user_id)->get();
    return $r->num_rows();


  }

  // function getHPCLLocation($location_id) {
  //   $res = '';
  //   $query = $this->db->select('name')
  //                     ->from('hpcl_location')
  //                     ->where('id', $location_id)
  //                     ->get();
          
  //   if($query->num_rows() > 0) {
  //     foreach($query->result() as $row);
  //     $res = $row->name;
  //   }

  //   return $res;

  // }

  // function getApprovalDetails($start_date, $end_date) {
  //       $res = '';
  //       $sql = $this->db->select('combination, purchase_entry, id, auto_gen_code, current_date, payment_terms,  credit_period')
  //                       ->from('approval_form')
  //                       ->where('current_date >=', $start_date)
  //                       ->where('current_date <=', $end_date)
  //                       ->get();

  //       if($sql->num_rows() > 0) {
  //         $res = $sql->result();
  //       }

  //      return $res; 
  // }

  // function getApprovalProductDetails($approval_id, $location_id) {
  //   $res = '';
  //   $sql = $this->db->select('a.approval_id, a.credit_vli, a.moq, a.validity_from, a.validity_to, a.product_id, a.pack_size, a.location, c.density, c.pack_size as pack_type')
  //                   ->from('approval_product_details a')
  //                   ->join('approval_form b', 'b.id=a.approval_id')
  //                   ->join('presto_instruments c', 'c.id=a.product_id')
  //                   ->join('hpcl_location d', 'd.id=a.location')
  //                   ->where('a.approval_id', $approval_id)
  //                   ->where('a.location', $location_id)
  //                   ->get();

  //     if($sql->num_rows() > 0) {
  //         $res = $sql->result();
  //       }

  //      return $res; 
  // }

  // function getInventoryDetails($valid_from, $valid_to, $product, $location) {
  //     $res = '';
  //     $sql = $this->db->select('a.inventory_id,a.qty,a.pack_size')
  //                     ->from('inventory_details a')
  //                     ->join('inventory b', 'a.inventory_id=b.id')
  //                     ->join('vendors c', 'c.id=b.party')
  //                     ->where('b.currentdate >=', $valid_from)
  //                     ->where('b.currentdate <=', $valid_to)
  //                     ->where('a.product', $product)
  //                     ->where('c.hpcl_location', $location)
  //                     ->get();

  //     if($sql->num_rows() > 0) {
  //       $res = $sql->result();
  //     }

  //     return $res; 
  // }

  // function getApprovalFormProductDetails($approval_id) {
  //   $res = '';
  //   $sql = $this->db->select('a.approval_id, a.credit_vli, a.moq, a.validity_from, a.validity_to, a.product_id, a.pack_size, a.location, c.density, c.pack_size as pack_type')
  //                   ->from('approval_product_details a')
  //                   ->join('approval_form b', 'b.id=a.approval_id')
  //                   ->join('presto_instruments c', 'c.id=a.product_id')
  //                   ->join('hpcl_location d', 'd.id=a.location')
  //                   ->where('a.approval_id', $approval_id)
  //                   ->get();

  //     if($sql->num_rows() > 0) {
  //         $res = $sql->result();
  //       }

  //      return $res; 
  // }

  // function getApprovalCombinationType1($approval_id) {
  //     $res = '';
  //     $query = $this->db->select('moq, vli, type')
  //                       ->from('approval_combination_type_1')
  //                       ->where('approval_id', $approval_id)
  //                       ->get();

  //     if($query->num_rows() > 0) {
  //       $res = $query->result();
  //     }

  //    return $res; 
  // }

  function getApprovalFormProductDetails($location_id) {
    $res = '';
    $sql = $this->db->select('c.name, c.address, c.gst, c.state')
                    ->from('hpcl_location b')
                    ->join('vendors c', 'c.hpcl_location=b.id')
                    ->where('b.id', $location_id)
                    ->get();

      if($sql->num_rows() > 0) {
          $res = $sql->result();
        }

       return $res; 
  }

    function getStateNameCode($state_id) {
      $res = '';
      $sql = $this->db->select('state_name,state_code')
                      ->from('states')
                      ->where('state_id', $state_id)
                      ->get();

      if($sql->num_rows() > 0) {
          foreach($sql->result() as $row);
          $res = $row->state_name.'|'.$row->state_code;
      }

       return $res; 
  }

  function getStoreRackLocation($store_rack_location_id) {
      $res = '';
      $sql = $this->db->select('companyname as sunder_company_name, address as sunder_company_address,pincode as sunder_pincode,state_id as sunder_state,city_id as sundercity,gst as sunder_gst,email_id as sunder_email, bank_details')
                      ->from('store_rack_location')
                      ->where('id', $store_rack_location_id)
                      ->get();

      if($sql->num_rows() > 0) {
          $res = $sql->result();
      }

       return $res; 
  }

  function getCityName($city_id) {
      $res = '';
      $sql = $this->db->select('city_name')
                      ->from('cities')
                      ->where('city_id', $city_id)
                      ->get();

      if($sql->num_rows() > 0) {
          foreach($sql->result() as $row);
          $res = $row->city_name;
      }

       return $res; 
  }

  function getCurrencyCode($grandtot)
{
    /* CURRENCY CODE **/
    $number = floatval($grandtot);
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }

    $rupees = implode('', array_reverse($str));
    $paise = '';

    if ($decimal) {
        $paise = 'and ';
        $decimal_length = strlen($decimal);

        if ($decimal_length == 2) {
            if ($decimal >= 20) {
                $dc = $decimal % 10;
                $td = $decimal - $dc;
                $ps = ($dc == 0) ? '' : '-' . $words[$dc];

                $paise .= $words[$td] . $ps;
            } else {
                $paise .= $words[$decimal];
            }
        } else {
            $paise .= $words[$decimal % 10];
        }

        $paise .= ' paise';
    }

    return $rupees;
    $words = $rupees . 'rupees ' . $paise;

    /* END **/
}

  function getType1TransportationProductDetails($approval_id) {
    $res = '';
    $sql = $this->db->select('a.deliveredQty, b.instruments_name, b.hsncode, c.shortname')
                    ->from('transportation_based_aprroval_product_details a')
                    ->join('presto_instruments b', 'b.id=a.product_id')
                    ->join('units c', 'c.id=a.pack_size', 'left')
                    ->where('a.approval_id', $approval_id)
                    ->get();

    if($sql->num_rows() > 0) {
      $res = $sql->result();
    }

    return $res;
  }

  function lastSoldOn($prd_id)
  {
     $data=array();
     $sql31 = $this->db->select('a.id,c.invoice_no,d.customer_name,c.send_to_tally_On,d.company_name')
                        ->from('customer_quotation_detail a')
                        ->join('customer_quotation b', 'b.id=a.quotation_id')
                        ->join('order_punch c', 'c.quotation_id=b.id')
                        ->join('customer_detail d', 'd.id=b.customer_id')
                        ->where('a.product_id', $prd_id)
                        ->where('c.send_to_tally',1)
                        ->where('c.cancelled',0)
                        ->order_by('c.id','DESC')
                        ->limit(1)
                        ->get();
                        if($sql31->num_rows()>0){
                          foreach($sql31->result() as $sql32);
                          $data[]=$sql32->company_name;
                          $data[]=$sql32->invoice_no;
                          $data[]=date('d-M-Y',strtotime($sql32->send_to_tally_On));
                        }

                        return $data;

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

  function getusername($user_id)
  {
    $resteye=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$user_id)->get();
    if($resteye->num_rows()>0)
    {
      foreach($resteye->result() as $row);

      $user=$row->first_name." ".$row->last_name;
    }else
    {
      $user='';
    }

    return $user;

  }

}
