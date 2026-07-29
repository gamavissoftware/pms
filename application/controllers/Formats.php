<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
class Formats extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
            /*if($session == FALSE)
            {
            redirect(page_url);

            }*/
            //$user_id =$this->session->userdata['logged_in']['user_id'];
            /*if(empty($user_id))
            {
            redirect(site_url(),'refresh');
            }*/
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
        $this->email->set_mailtype("html");
		
	}


    public function department_wise_report(){
       
        $query = "
        SELECT 
            d.department, df.df_no, t.department_id,
            COUNT(t.id) AS total_tasks,
            MAX(
                CASE 
                    WHEN t.end_date < CURDATE() AND t.task_status = 0 THEN DATEDIFF(CURDATE(), t.end_date)
                    ELSE 0
                END
            ) AS max_delay_days
        FROM task_department_wise_scheduling t
        JOIN df_release df ON t.df_id = df.id
        JOIN departments d ON t.department_id = d.department_id
        WHERE df.df_status = 'running'
          AND t.task_status = 0
          AND t.end_date < CURDATE()
           AND t.on_hold = '0'
        GROUP BY t.department_id
        ORDER BY max_delay_days DESC;
    ";

    $query1 =$this->db->query($query);

    
    // if ($query->num_rows() == 0) {
    //     return false;
    // }

   

    $this->load->library('Pdf');
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SHUBHAM PACK');
    $pdf->SetTitle("YOUR DEPARTMENT OVERDUE TASK REPORT");
    $pdf->SetMargins(5, 5, 5);
    $pdf->SetPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();

    $html = '<style>
    table{
        width:100%;
        }

        .table {
            width: 100%;
            font-size:11px;
            padding:5px;
        }


        .table th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
        }

        .table td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table1 {
            font-size:11px;
            width: 100%;
              padding: 5px 10px;
        }

        .table1 th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table1 td {
            border: 1px solid lightgrey;
            background-color: #fff;
            text-align: left;
        }
</style>


<table style="padding:5px;">
    <tr>
        <td style="text-align:center; font-size:18px; background-color:#d0edf7; border:1px solid lightblue;">Department Wise Overdue Task</td>
    </tr>
</table>
';

// echo "<pre>"; print_r($query1->result()); exit;


foreach($query1->result() as $row){

$department =$row->department;

$department_id =$row->department_id;

$query2 = $this->db->select('first_name, last_name, user_id')->from('system_users')->where('department_id', $department_id)->where('user_status', 1)->get();


$depart = ucwords(strtolower($department));

if($depart == 'Ppc'){

    $depart = 'PPC';

} else{

    $depart =$depart;
}

$html .='
<br>
<br>

<table style="padding:5px;" class="table1">
<tr>
<td style="background-color:#049dd4; color:#fff; font-weight:bold; border-bottom:1px solid #000;">Department</td>
<td style="background-color:#049dd4; color:#fff; font-weight:bold; border-bottom:1px solid #000;">Overdue Task</td>
<td style="background-color:#049dd4; color:#fff; font-weight:bold; border-bottom:1px solid #000;">Overdue Task with Remarks</td>
<td style="background-color:#049dd4; color:#fff; font-weight:bold; border-bottom:1px solid #000;">Overdue Task with Helpticket</td>
</tr>
<tr>
<td style="background-color:#b1d7f3; font-weight:bold; font-size:14px; border-left:1px solid #000; border-bottom:1px solid #000;" >'.$depart.'</td>
<td style="background-color:#b1d7f3; text-align:center; font-weight:bold; font-size:14px; border-bottom:1px solid #000;">'.$row->total_tasks.'</td>
<td  style="background-color:#b1d7f3; text-align:center; font-weight:bold; border-bottom:1px solid #000;"></td>
<td  style="background-color:#b1d7f3; text-align:center; font-weight:bold; border-bottom:1px solid #000; border-right:1px solid #000;"></td>
</tr>';


$user_array =array();


foreach($query2->result() as $row1)
{
    $taskprogressid=array();
    $missing_recordids=array();
    	$this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.on_hold',0)->where('task_status',0)->where('end_date<',date('Y-m-d'))->where('b.df_status', 'running');
		
			
				// $this->db->where_in('department_id',$department_id,'false');
			
			// if($_SESSION['logged_in']['adminuser']==3)
			// {
				$this->db->where('assigned_user', $row1->user_id);
			// }

			// $this->db->where('department_id!=','22');
			$q = $this->db->get();
		// if($q->num_rows()>0){
		// 	$count = count($q->result());
		// }else{
        //     $count =0;
        
        // }

        $user_array['name'][] =ucwords(strtolower($row1->first_name.' '.$row1->last_name));
        $user_array['count'][] =$q->num_rows();
        $ldate='';
        $hdate='';
        $remarksAdded=0;
        $helpticket=0;
        if($q->num_rows()>0)
        {
            foreach($q->result() as $rt)
            {
        $taskprogressid[]=$rt->id;
            }

       
        $existing_records = $this->db->select('recordid')
        ->from('task_pending_status')
        ->where_in('recordid', $taskprogressid)
        ->group_by('recordid')
        ->get()
        ->result_array();
        // Extract recordids from the database result
        $existing_ids = array_column($existing_records, 'recordid');
        // Find missing recordids
        $missing_recordids = array_diff($taskprogressid, $existing_ids);

        $remarksAdded=count($taskprogressid)-count($missing_recordids);

        $remarksAdded+=$this->RemarksOnMissingRecords($missing_recordids);

        if(count($existing_ids)>0)
        {
        if(count($missing_recordids)==87)
        {
            $a="added_on";
        }else
        {
            $a="added_on";
        }
        $latestupdate = $this->db->select('max('.$a.') as lastupdated')
        ->from('task_pending_status')
        ->where_in('recordid', $existing_ids,false)
      
        ->get();

        if($latestupdate->num_rows()>0)
        {
            foreach($latestupdate->result() as $rowww);
            $ldate=date('d-M-Y',strtotime($rowww->lastupdated));
        }
        }else
        {

            if(count($missing_recordids)>0)
            {

                    $latestupdate = $this->db->select('max(taskupdatedontime) as lastupdated')
        ->from('task_department_wise_scheduling')
        ->where_in('id', $missing_recordids,false)
      ->get();
        if($latestupdate->num_rows()>0)
        {
            foreach($latestupdate->result() as $rowww);
            $ldate=date('d-M-Y',strtotime($rowww->lastupdated));
        }



            }


        }

        /** HELP TICKET **/

        $existing_records1 = $this->db->select('task_record_id')
        ->from('communication_ticket_system')
        ->where_in('task_record_id', $taskprogressid)
        ->group_by('task_record_id')
        ->get()
        ->result_array();
        // Extract recordids from the database result
        $existing_ids1 = array_column($existing_records1, 'task_record_id');
        // Find missing recordids
        $missing_recordids1 = array_diff($taskprogressid, $existing_ids1);
        $helpticket=count($taskprogressid)-count($missing_recordids1);


         if(count($existing_ids1)>0)
        {
        if(count($missing_recordids1)==87)
        {
            $a="added_on";
        }else
        {
            $a="added_on";
        }
        $latestupdate1 = $this->db->select('max('.$a.') as lastupdated1')
        ->from('communication_ticket_system')
        ->where_in('task_record_id', $existing_ids1,false)
        ->get();

        if($latestupdate1->num_rows()>0)
        {
            foreach($latestupdate1->result() as $rowww1);
            $hdate=date('d-M-Y',strtotime($rowww1->lastupdated1));
        }
        }

   
        }
        
     //   echo "<pre>"; print_r($missing_recordids); 
       
        $user_array['remarks_count'][] =$remarksAdded;
        $user_array['remarks_date'][] =$ldate;
        $user_array['helpticket_count'][] =$helpticket;
        $user_array['helpticket_date'][] =$hdate;

}

 //exit;

$user_arry1 =$this->sortByTaskCount($user_array);
 //echo "<pre>"; print_r($user_array)."<br/><br>"."<pre>"; print_r($user_arry1); exit;

if(count($user_arry1)> 0){
    for($i=0; $i< count($user_arry1['name']); $i++){
        if($user_arry1['remarks_date'][$i]<>'')
        {
            $dt=$user_arry1['remarks_date'][$i];
        }else
        {
            $dt='';
        }

        if($user_arry1['helpticket_date'][$i]<>'')
        {
            $dt1=$user_arry1['helpticket_date'][$i];
        }else
        {
            $dt1='';
        }


      $html .='<tr>
<td>'.$user_arry1['name'][$i].'</td>
<td style="text-align:center;">'.$user_arry1['count'][$i].'</td>
<td>
<table width="100%">
<tr>
<td width="20%">'.$user_arry1['remarks_count'][$i].'</td>
<td width="80%" style="text-align:right;">
 <span style="color:red; font-size:9px; text-align:left;"><i>'.$dt.'</i></span>
</td>
</tr>
</table>

</td>
<td><table width="100%">
<tr>
<td width="20%">'.$user_arry1['helpticket_count'][$i].'</td>
<td width="80%" style="text-align:right;">
 <span style="color:red; font-size:9px; text-align:left;"><i>'.$dt1.'</i></span>
</td>
</tr>
</table>


</td>
</tr>';  
    }
}


$html .='
</table>


';

}



   

    // echo $html; exit;
    $pdf->writeHTML($html, true, false, true, false, '');

    $filelocation = SITE_ROOT . 'image_bank/daily_reports/overduetask/';
    $fileName = "Department-Overdue-Task-list-" . date('Y-m-d') . ".pdf";
      $pdf->Output($filelocation . $fileName, 'F');
    $pdf->Output($filelocation . $fileName, 'I');

    return $filelocation . $fileName;
    }


    function sortByTaskCount($data) {
    // Sort the 'count' array in descending order while maintaining the association with the 'name' array
    array_multisort($data['count'], SORT_DESC, $data['name'],$data['remarks_count'],$data['remarks_date'],$data['helpticket_count'],$data['helpticket_date']);
    
    return $data;
}

function RemarksOnMissingRecords($missing_recordids)
{
if(count($missing_recordids)>0)
{

$this->db->select('id');
$this->db->from('task_department_wise_scheduling');
$this->db->where_in('id', $missing_recordids);
$this->db->group_start();
$this->db->where('REMARKS IS NOT NULL', null, false);
$this->db->where('REMARKS !=', '');
$this->db->group_end();
$query = $this->db->get();
return $query->num_rows();
}else
{
    return 0;
}



}

public function df_form(){
    $this->load->view('formats/rfq/examples/df_form');
}

public function basic_machine_df_form(){
    $this->load->view('formats/rfq/examples/basic_machine_df_form');
}

public function powder_df_form(){
    $this->load->view('formats/rfq/examples/powder_df_form');
}


public function df_form_600(){
    $this->load->view('formats/rfq/examples/df_form_600_pdf');
}

public function previewdfdesignform(){

    $poid = $this->uri->segment(3);
    $leadid = $this->uri->segment(4);
    $date = $this->uri->segment(5);
    $uri5 = base64_decode($date);

    $rest=$this->db->select('id')->from('df_design_form_table')->where('po_id',$poid)->where('email_sent',0)->get();
    if($rest->num_rows()>0)
    {
    //$this->sendnotificationtoalldepartmentheads($poid,$leadid, $uri5,1);
    }


     $rest=$this->db->select('id')->from('df_design_form_600_table')->where('po_id',$poid)->where('email_sent',0)->get();
    if($rest->num_rows()>0)
    {
    //$this->sendnotificationtoalldepartmentheads($poid,$leadid, $uri5,2);
    }

    $this->load->view('master/dfformpreview');
}

public function sendnotificationtoalldepartmentheads($poid, $leadid, $uri5,$flag)
{
   // echo "hi"; exit;
    // Load email library
    $this->load->library('email');


     $restywedew=$this->db->select('df_number')->from('poreceived')->where('id',$poid)->get();
    if($restywedew->num_rows()>0)
    {
        foreach($restywedew->result() as $roereo);
        $df_name=$roereo->df_number;
    }else
    {
        $df_name='';
    }

   // echo $df_name; exit;

$dep=array();
    // GET ALL UNIQUE DEPARTMENT FROM TASK 
    $rest=$this->db->select('department_id')->from('task_management')->group_by('department_id')->get();
    if($rest->num_rows()>0)
    {
        foreach($rest->result() as $row)
        {
            $dep[]=$row->department_id;
        }

    }


    // Get design form details
    if($flag==1)
    {
    $df_query = $this->db->get_where('df_design_form_table', ['po_id' => $poid, 'lead_id' => $leadid]);
    $design_form = $df_query->row();

    if (!$design_form) {
        log_message('error', 'Design form not found for PO ID: ' . $poid . ' and Lead ID: ' . $leadid);
        return;
    }
}else
{
    $df_query = $this->db->get_where('df_design_form_600_table', ['po_id' => $poid, 'lead_id' => $leadid]);
    $design_form = $df_query->row();

    if (!$design_form) {
        log_message('error', 'Design form not found for PO ID: ' . $poid . ' and Lead ID: ' . $leadid);
        return;
    }
}

   // $df_name = $design_form->design_form_name;
    $design_form_date = date('d-m-Y', strtotime($design_form->design_form_date));
    $attachment_path = softwarepath . 'designform/'.$uri5;
//   echo $attachment_path; exit;



    // Fetch all department head emails
    $q = $this->db->select('b.email')
                  ->from('prestogroup_teams a')
                  ->join('system_users b', 'a.team_leader = b.user_id')
                  ->where('user_status', 1)
                  ->where_in('a.department_id',$dep)
                  ->group_by('b.email')
                  ->get();

    foreach ($q->result() as $row) {
       // $to_email = $row->email;
        $to_email = "mangleshup@gmail.com";

        $subject = 'New DF Form Released';

        $message = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px; }
                .container { background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .logo { text-align: center; margin-bottom: 20px; }
                .highlight { color: #007bff; font-weight: bold; }
                .footer { font-size: 12px; color: #777777; text-align: center; margin-top: 30px; }
                .df-details { margin-top: 20px; font-size: 15px; }
                .df-details span { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <img src="https://shubhampack.in/beta1/assets/images/shubhampack.png" alt="Company Logo" width="150">
                </div>
                <p>Dear Department Head,</p>
                <p>We are pleased to inform you that a <span class="highlight">New DF Form</span> has been released.</p>
                <div class="df-details">
                    <p><span>DF No:</span> ' . htmlspecialchars($df_name) . '</p>
                    <p><span>DF Date:</span> ' . htmlspecialchars($design_form_date) . '</p>
                </div>
                <p>Please find the attachment for your reference and further action.</p>

                <p>Please be prepare for the upcoming Assignments related to this DF</p>

                <p>Best regards,<br><strong>Design Team</strong></p>
                <div class="footer">This is an automated message. Please do not reply to this email.</div>
            </div>
        </body>
        </html>';

        $this->email->clear(TRUE);
        $this->email->from('taskmanagement@shubhampack.in', 'New Design Form Release Notification');
        $this->email->to($to_email);
        $this->email->bcc('sdsrbh5@gmail.com, mangleshup@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);

        // if (file_exists($attachment_path    )) {
            $this->email->attach($attachment_path);

        // } else {
        //     log_message('error', 'Attachment not found: ' . $attachment_path);
        // }

        if (!$this->email->send()) {
            log_message('error', 'Email failed to: ' . $to_email . ' - ' . $this->email->print_debugger(['headers']));
        } else {

            log_message('info', 'Notification sent to: ' . $to_email);
        }
    }

    /** UPDATE EMAIL SEND**/
    if($flag==1)
    {
    $up=array('email_sent'=>1,'email_sent_on'=>date('Y-m-d H:i:s'));
    $this->db->where('po_id',$poid);
    $this->db->update('df_design_form_table',$up);
    }else
    {
    $up=array('email_sent'=>1,'email_sent_on'=>date('Y-m-d H:i:s'));
    $this->db->where('po_id',$poid);
    $this->db->update('df_design_form_600_table',$up);
    }
}



function getdfattachment($poid, $leadid,$uri5){
    $q = $this->db->select('design_form_name')->from('df_design_form_table')->where('po_id',$poid)->where('lead_id',$leadid)->get();
    foreach($q->result() as $row);

}


public function performa_invoice(){
    $this->load->view('formats/rfq/examples/performa_invoice');
}

public function export_performa_invoice(){
    $this->load->view('formats/rfq/examples/export_performa_invoice');
}


public function df_form_dompdf()
{
    $po_id   = $this->uri->segment(3);
    $lead_id = $this->uri->segment(4);

    $this->load->model('Salescrm_model','salescrm');

    // ---------- HELPERS ----------
    $safeDate = function($val, $format = 'd-m-Y') {
        if(empty($val) || $val == '0000-00-00' || $val == '0000-00-00 00:00:00') return '';
        $ts = strtotime($val);
        if(!$ts) return '';
        return date($format, $ts);
    };

    $objDefaults = function($obj, $defaults){
        if(empty($obj)) $obj = new stdClass();
        foreach($defaults as $k=>$v){
            if(!isset($obj->$k)) $obj->$k = $v;
        }
        return $obj;
    };

    // ---------- QUOTATION DATA ----------
    $quote_record_id = (int)$this->salescrm->getRecordID($lead_id);

    $ann1 = $this->salescrm->getannexture_1($quote_record_id); // your existing function
    $ann2 = $this->salescrm->getannexture_2($quote_record_id); // your existing function
    $qty_data = $this->salescrm->getQtyPackedData($quote_record_id);

    // Safe parse from annexture_1 array indexes (as per your function)
    $product_to_be_packed = isset($ann1[0])  ? $ann1[0]  : '';
    $horizontal_sealing_width1 = isset($ann1[4]) ? $ann1[4] : '';
    $vertical_sealing_width1   = isset($ann1[5]) ? $ann1[5] : '';
    $typeofsealing             = isset($ann1[7]) ? $ann1[7] : '';
    $power_supply              = isset($ann1[9]) ? $ann1[9] : '';
    $liquidviscositydata       = isset($ann1[10])? $ann1[10]: '';
    $powderdensitydata         = isset($ann1[12])? $ann1[12]: '';
    $batchcut                  = isset($ann1[16])? $ann1[16]: '';

    $liquid_option             = isset($ann1[17])? $ann1[17]: '';
    $powder_option             = isset($ann1[18])? $ann1[18]: '';
    $non_viscous_option        = isset($ann1[19])? $ann1[19]: '';
    $viscous_option            = isset($ann1[20])? $ann1[20]: '';
    $piston_filler_option      = isset($ann1[21])? $ann1[21]: '';
    $follow_meter_option       = isset($ann1[22])? $ann1[22]: '';
    $free_flow_option          = isset($ann1[23])? $ann1[23]: '';
    $weigher_system_option     = isset($ann1[24])? $ann1[24]: '';
    $liner_weigher_option      = isset($ann1[25])? $ann1[25]: '';
    $mult_head_weigher_option  = isset($ann1[26])? $ann1[26]: '';
    $volumetric_cap_option     = isset($ann1[27])? $ann1[27]: '';
    $non_free_flow_option      = isset($ann1[28])? $ann1[28]: '';
    $machine_type              = isset($ann1[29])? $ann1[29]: '';
    $machine_orientation       = isset($ann1[30])? $ann1[30]: '';
    $cup_filler_option         = isset($ann1[31])? $ann1[31]: '';

    // annexture_2 indexes per your function
    $machinemodel = isset($ann2[0]) ? $ann2[0] : '';
    $no_of_track  = isset($ann2[3]) ? $ann2[3] : '';

    // Product Echo + Specification text
    $product_echo = '';
    $pro = '';
    if((string)$product_to_be_packed === '1'){
        $product_echo = 'Liquid / Paste';
        $pro = 'Viscosity:<br>'.nl2br($liquidviscositydata);
    }elseif((string)$product_to_be_packed === '2'){
        $product_echo = 'Powder / Granules';
        $pro = 'Density:<br>'.nl2br($powderdensitydata);
    }

    // ---------- DF MASTER ----------
    $df = $this->db->select('*')
        ->from('df_design_form_table')
        ->where('po_id', $po_id)
        ->where('lead_id', $lead_id)
        ->limit(1)
        ->get()
        ->row();

    $df = $objDefaults($df, [
        'id' => 0,
        'design_form_name' => '',
        'design_form_date' => '',
        'ref_df_date' => '',
        'reference_no' => '',
        'iom_no' => '',
        'invoice_no' => '',
    ]);

    $record_id = (int)$df->id;

    // ---------- PO RECEIVED (IMPORTANT FIX) ----------
    // First try by po_id + lead_id, then fallback lead_id
    $poRow = $this->db->select('podate, df_number')
        ->from('poreceived')
        ->where('lead_id', $lead_id)
        ->where('id', $po_id)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get()
        ->row();

    if(empty($poRow)){
        $poRow = $this->db->select('podate, df_number')
            ->from('poreceived')
            ->where('lead_id', $lead_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();
    }

    $date_of_po = '';
    $df_number_from_po = '';
    if(!empty($poRow)){
        $date_of_po = $safeDate($poRow->podate);
        $df_number_from_po = isset($poRow->df_number) ? $poRow->df_number : '';
    }

    // If DF name is empty in df_design_form_table, fallback to PO df_number
    if(empty($df->design_form_name) && !empty($df_number_from_po)){
        $df->design_form_name = $df_number_from_po;
    }

    // ---------- DF RELATED TABLES ----------
    $multi = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine')->row();
    $spec  = $this->db->where('record_id',$record_id)->get('df_form_machine_specification')->row();
    $m1    = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine1')->row();
    $m2    = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine2')->row();
    $m3    = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine3')->row();
    $m4    = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine4')->row();
    $m5    = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine5')->row();
  
//echo "<pre>"; print_r( $data['special_notes']); exit;
    // Size/Qty remarks
    $sizeRow = $this->db->where('record_id',$record_id)->get('df_form_machine_specification_size_qnty')->row();
    $pouch_size_remarks = !empty($sizeRow) && isset($sizeRow->pouch_size_remarks) ? $sizeRow->pouch_size_remarks : '';
    $quantity_packed_remarks = !empty($sizeRow) && isset($sizeRow->quantity_packed_remarks) ? $sizeRow->quantity_packed_remarks : '';

    // Secondary pack list rows
    $secPackRows = $this->db->where('record_id',$record_id)->get('df_form_multi_track_machine_pack')->result();

    // Resolve web aligner option name (same as your old TCPDF view)
    $web_aligner_option_name = '';
    if(!empty($m1) && !empty($m1->web_aligner_option)){
        $opt = $this->db->select('name')
            ->from('quote_parts_heading_master_options')
            ->where('id', $m1->web_aligner_option)
            ->limit(1)->get()->row();
        if(!empty($opt)) $web_aligner_option_name = $opt->name;
    }

    // ---------- SAFE DEFAULT OBJECTS (NO NOTICE/ERROR) ----------
    $multi = $objDefaults($multi, [
        'ce_complied'=>'','ce_complied_remarks'=>'',
        'date_of_po_remarks'=>'',
        'penalty_clause'=>'','penalty_clause_remarks'=>'',
        'dispatch_date'=>'','dispatch_date_remarks'=>'',
        'trial_date'=>'','trial_date_remarks'=>'',
        'machine_mode_no_remarks'=>'',
        'machine_type_remarks'=>'',
        'machine_orientation'=>'', 'machine_orientation_remarks'=>''
    ]);

    $spec = $objDefaults($spec, [
        'tracks'=>'','tracks_remarks'=>'',
        'product_packed'=>'','product_packed_remarks'=>'',
        'filling_unit_remarks'=>'',
        'product_specification_remarks'=>'',
        'profle_sealing_remarks'=>''
    ]);

    $m1 = $objDefaults($m1, [
        'notching_option'=>'','notching_option_remarks'=>'',
        'hooper_details'=>'','openable_option'=>'','closed_option'=>'','pressurised_option'=>'',
        'closed_pressurised_option'=>'','non_pressurised_option'=>'','non_closed_pressurised_option'=>'',
        'hooper_details_remarks'=>'',
        'cladding_provision'=>'','cladding_provision_remarks'=>'',
        'embossing'=>'','embossing_option'=>'','linear_option'=>'','rotary_option'=>'','provision_remarks'=>'',
        'web_aligner'=>'','web_aligner_remarks'=>''
    ]);

    $m2 = $objDefaults($m2, [
        'center_slitting'=>'','center_slitting_remarks'=>'',
        'vertical_slitting'=>'','vertical_slitting_remarks'=>'',
        'vertical_sealer'=>'','vertical_sealer_remarks'=>'',
        'laminate_pulling'=>'','laminate_pulling_remarks'=>'',
        'up_down'=>'','up_down_remarks'=>'',
        'embossing_coding'=>'','embossing_coding_remarks'=>'',
        'cooling_station'=>'','cooling_station_remarks'=>'',
        'horizontal_sealer'=>'','horizontal_sealer_remarks'=>'',
        'perforation_blade'=>'','perforation_blade_remarks'=>'',
        'priston_drive'=>'','priston_drive_remarks'=>'',
        'shutt_off_nozzle'=>'','shutt_off_nozzle_remarks'=>'',
        'filling_plate_drive'=>'','filling_plate_drive_remarks'=>'',
        'individual_weight'=>'','individual_weight_remarks'=>'',
        'overall_weight_adjust'=>'','overall_weight_adjust_remarks'=>''
    ]);

    $m3 = $objDefaults($m3, [
        'traverse_drive'=>'','yes_traverse_drive'=>'','traverse_drive_remarks'=>'',
        'printer_yes_no'=>'','inkjet_option'=>'','tto_option'=>'','thermal_inkjet_option'=>'','printer_remarks'=>'',
        'case_packer_drive'=>'','case_packer_drive_remarks'=>'',
        'nozzle_funnel'=>'','powder_option1'=>'','liquid_option1'=>'','liquid_shut_option1'=>'','nozzle_funnel_remarks'=>'',
        'hose_pipe'=>'','hose_pipe_remarks'=>'',
        'batch_cut_format_remarks'=>'','string_option'=>''
    ]);

    $m4 = $objDefaults($m4, [
        'horizontal_sealer1'=>'','horizontal_sealer1_remarks'=>'',
        'vertical_sealer1'=>'','vertical_sealer1_remarks'=>'',
        'rotary_valve_coating'=>'','rotary_valve_coating_remarks'=>'',
        'working_speed'=>'','working_speed_remarks'=>'',
        'reel_shaft_type'=>'','reel_shaft_type_remarks'=>'',
        'reel_core_diameter'=>'','reel_core_diameter_remarks'=>'',
        'trial_material'=>'','trial_material_remarks'=>'',
        'laminate_detail'=>'','laminate_detail_remarks'=>'',
        'heater_control_system'=>'','heater_control_system_remarks'=>'',
        'beacon_light'=>'','beacon_light_remarks'=>'',
        'hooper_level'=>'','hooper_level_option'=>'','hooper_level_remarks'=>'',
        'safety_relay'=>'','safety_relay_remarks'=>'',
        'plc_maker'=>'','plc_maker_remarks'=>'',
        'supply_voltage_remarks'=>'',
        'hmi_size'=>'','hmi_size_remarks'=>'',
        'cip_system'=>'','cip_system_option'=>'','cip_system_option_remarks'=>'',
        'tool_kit'=>'','tool_kit_remarks'=>'',
        'changeover_part'=>'','changeover_part_remarks'=>''
    ]);

    $m5 = $objDefaults($m5, [
        'secondary_pack'=>'','secondary_pack_remarks'=>'',
        'case_packer'=>'',
        'ladder_platform'=>'','ladder_platform_remarks'=>'',
        'machine_guarding'=>'','aluminium_option'=>'','ss_304_option'=>'','machine_guarding_remarks'=>'',
        'special_notes'=>''
    ]);

    $cleanSpecialNotes = '';

if(!empty($m5->special_notes))
{
    // 1. Decode entities (like &lt; to <)
    $notes = html_entity_decode(
        $m5->special_notes,
        ENT_QUOTES,
        'UTF-8'
    );

    // 2. Remove all inline CSS/Styles (This prevents Dompdf layout crashes)
    $notes = preg_replace('/ style="[^"]*"/i', '', $notes);

    // 3. Remove specific problematic formatting tags but keep content
    $notes = preg_replace('/<\/?(span|font|strong|b|i|u)[^>]*>/i', '', $notes);

    // 4. STRIP TAGS: Keep only Tables, Lists, and Line breaks
    // Added table, thead, tbody, tr, th, td to the allowed list
    $notes = strip_tags($notes, '<table><thead><tbody><tr><td><th><ul><ol><li><br><p>');

    // 5. Cleanup whitespace and empty tags
    $notes = preg_replace('/<p>\s*<\/p>/i', '', $notes);
    $notes = str_replace('&nbsp;', ' ', $notes);
    
    // Normalize multiple line breaks to a single one
    $notes = preg_replace('/(<br\s*\/?>\s*)+/i', '<br>', $notes);

    $cleanSpecialNotes = $notes;
}


    // ---------- DATA PACK FOR VIEW ----------
    $data = [
        'po_id' => $po_id,
        'lead_id' => $lead_id,

        'df' => $df,
        'date_of_po' => $date_of_po,
        'safeDate' => $safeDate, // pass callable

        'machinemodel' => $machinemodel,
        'no_of_track' => $no_of_track,

        'product_to_be_packed' => $product_to_be_packed,
        'product_echo' => $product_echo,
        'pro' => $pro,
        'special_notes' => $cleanSpecialNotes,
        'qty_data' => $qty_data,

        'horizontal_sealing_width1' => $horizontal_sealing_width1,
        'vertical_sealing_width1' => $vertical_sealing_width1,
        'typeofsealing' => $typeofsealing,
        'batchcut' => $batchcut,

        'liquid_option' => $liquid_option,
        'powder_option' => $powder_option,
        'non_viscous_option' => $non_viscous_option,
        'viscous_option' => $viscous_option,
        'piston_filler_option' => $piston_filler_option,
        'follow_meter_option' => $follow_meter_option,
        'free_flow_option' => $free_flow_option,
        'weigher_system_option' => $weigher_system_option,
        'liner_weigher_option' => $liner_weigher_option,
        'mult_head_weigher_option' => $mult_head_weigher_option,
        'volumetric_cap_option' => $volumetric_cap_option,
        'non_free_flow_option' => $non_free_flow_option,
        'cup_filler_option' => $cup_filler_option,

        'power_supply' => $power_supply,
        'machine_type' => $machine_type,
        'machine_orientation' => $machine_orientation,

        'multi' => $multi,
        'spec'  => $spec,
        'm1'    => $m1,
        'm2'    => $m2,
        'm3'    => $m3,
        'm4'    => $m4,
        'm5'    => $m5,

        'pouch_size_remarks' => $pouch_size_remarks,
        'quantity_packed_remarks' => $quantity_packed_remarks,

        'secPackRows' => $secPackRows,
        'web_aligner_option_name' => $web_aligner_option_name,
    ];

    // ---------- LOAD HTML ----------
    $html = $this->load->view('formats/rfq/examples/df_form_dompdf', $data, true);

    // ---------- DOMPDF ----------
    $this->load->library('pdf'); // if you already have dompdf wrapper, ignore. else keep.
    require_once APPPATH.'third_party/dompdf/autoload.inc.php';

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4','landscape');
    $dompdf->render();

    // ---------- SAVE + STREAM ----------
    $output = $dompdf->output();

    if(!is_dir(FCPATH.'designform')){
        mkdir(FCPATH.'designform', 0755, true);
    }

    $fileNameSafe = 'DF_'.$df->design_form_name.'_'.$safeDate($df->design_form_date, 'Y-m-d').'.pdf';
    $fileNameSafe = preg_replace('/[^A-Za-z0-9_\-\.]/','_', $fileNameSafe);

    $file = FCPATH."designform/".$fileNameSafe;
    file_put_contents($file, $output);

    $dompdf->stream($fileNameSafe, ["Attachment"=>false]);
}

public function df_project_dashboard()
{
    $po_id   = (int)$this->uri->segment(3);
    $lead_id = (int)$this->uri->segment(4);

    $this->load->model('Salescrm_model','salescrm');

    //--------------------------------------------------
    // SAFE DATE HELPER
    //--------------------------------------------------

    $safeDate = function($val,$f='d-m-Y'){
        if(empty($val)) return '';
        $ts = strtotime($val);
        if(!$ts) return '';
        return date($f,$ts);
    };

    //--------------------------------------------------
    // GET QUOTATION SNAPSHOT
    //--------------------------------------------------

    $record_id = (int)$this->salescrm->getRecordID($lead_id);

    $ann1 = $this->salescrm->getannexture_1($record_id);
    $ann2 = $this->salescrm->getannexture_2($record_id);
    $qty  = $this->salescrm->getQtyPackedData($record_id);

    //--------------------------------------------------
    // DF MASTER
    //--------------------------------------------------

    $df = $this->db
        ->where('po_id',$po_id)
        ->where('lead_id',$lead_id)
        ->get('df_design_form_table')
        ->row();

    if(empty($df))
        show_error('DF not created yet');

    $df_id = (int)$df->id;

    //--------------------------------------------------
    // MACHINE SPEC SNAPSHOT
    //--------------------------------------------------

    $spec = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_machine_specification')
        ->row();

    $multi = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine')
        ->row();

    $m1 = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine1')
        ->row();

    $m2 = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine2')
        ->row();

    $m3 = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine3')
        ->row();

    $m4 = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine4')
        ->row();

    $m5 = $this->db
        ->where('record_id',$df_id)
        ->get('df_form_multi_track_machine5')
        ->row();

    //--------------------------------------------------
    // SPECIAL NOTES CLEAN (PDF SAFE)
    //--------------------------------------------------

    $special_notes = '';
    if(!empty($m5->special_notes)){
        $special_notes = html_entity_decode(
            $m5->special_notes,
            ENT_QUOTES,
            'UTF-8'
        );

        $special_notes = preg_replace('/ style="[^"]*"/i', '', $special_notes);
        $special_notes = strip_tags($special_notes,'<ol><ul><li><p><br>');
        $special_notes = preg_replace('/<p>\s*<\/p>/i','',$special_notes);
    }

    //--------------------------------------------------
    // BUILD SNAPSHOT OBJECT
    //--------------------------------------------------

    $dashboard = [
        'df'      => $df,
        'spec'    => $spec,
        'multi'   => $multi,
        'm1'      => $m1,
        'm2'      => $m2,
        'm3'      => $m3,
        'm4'      => $m4,
        'm5'      => $m5,
        'ann1'    => $ann1,
        'ann2'    => $ann2,
        'qty'     => $qty,
        'notes'   => $special_notes
    ];

    //--------------------------------------------------
    // LOAD NEW VIEW
    //--------------------------------------------------

    $this->load->view(
        'formats/df/dashboard_view',
        ['dashboard'=>$dashboard]
    );
}


}