<?php 
class Task_reports extends CI_Controller{
function __construct() { 
 parent::__construct();
    $this->load->model('User_model','user');
    $this->load->model('Task_model','task');
    $config = array(
    'protocol' => 'smtp', 
    'smtp_host' => 'smtp.logix.in',
    'smtp_port' => 465,     
    'smtp_user' => 'taskmanagement@shubhampack.com', 
    'smtp_pass' => 'Shubham@199', 
    'mailtype' => 'html', 
    'charset' => 'iso-8859-1',
    'newline'=>"\r\n",
    'starttls'=>TRUE);
    $this->email->initialize($config);
    $this->email->set_mailtype("html");
    //$this->email->set_newline("\r\n");


 } 

public function todayduetasknotification()
{

$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("TODAYS DUE TASK");
$pdf->SetSubject('TODAYS DUE TASK');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://gamavis.com/PMS/assets/images/shubhampack.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';


$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="8" style="background-color:lightgrey; text-align:center; font-size:15px; font-weight:bold;">Todays Due Tasks '.date("d-m-Y").'</th>
</tr>
<tr>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF No</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF Release Date</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Department </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Accountable Person </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Name </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Completion Date </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Last Remarks (If Any) </b></th>

</tr><tbody>';  
$todays = $this->uri->segment(3);
$q = $this->db->select('a.assigned_user')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('b.df_status',0)->group_by('a.assigned_user')->get();
if($q->num_rows()>0){
  foreach($q->result() as $rows){


$q = $this->db->select('a.assigned_user, a.end_date,a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, d.department, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id')->join('departments d','a.department_id=d.department_id')->join('system_users f','a.assigned_user=f.user_id','left')->where('a.task_status',0)->where('a.assigned_user',$rows->assigned_user)->where('a.end_date',date('Y-m-d'))->get();

if($q->num_rows()>0){

$c = 1;

  foreach($q->result() as $row){
    $pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,2);
        if($row->po_id>0){
          $dfno = $row->df_no;
          if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
          {
            $dfreleasedate = date('d-m-Y',strtotime($row->added_on));
          }else{
            $dfreleasedate = "";
          }
          
          
          
        }else{
          $dfno = "NA";
          $dfreleasedate = "NA";
        }
        if($dfreleasedate=='01-01-1970'){
          $dfreleasedate='NA';
        }else{
          $dfreleasedate = $dfreleasedate;
        }
        $enddate = date('d-m-Y',strtotime($row->end_date));
       $pendingsince =  "<strong style='color:green;font-weight:bold;'>".$pendinddays." Days</strong>";

$html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$c.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfreleasedate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->department.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->first_name." ".$row->last_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->task_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$enddate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'. $pendingsince.'</td>
  </tr>';

$c++;}

}
}
}
$html.='</tbody></table><br><br>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Todays_due_task".date('Y-m-d').".pdf"; //Linux

 $pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');
 
    
}


public function overduetasknotification()
{

$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("TODAYS DUE TASK");
$pdf->SetSubject('TODAYS DUE TASK');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://gamavis.com/PMS/assets/images/shubhampack.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';


$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="8" style="background-color:lightgrey; text-align:center; font-size:15px; font-weight:bold;">Todays Due Tasks '.date("d-m-Y").'</th>
</tr>
<tr>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF No</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF Release Date</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Department </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Accountable Person </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Name </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Completion Date </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Last Remarks (If Any) </b></th>

</tr><tbody>';  
$todays = $this->uri->segment(3);
$q = $this->db->select('a.assigned_user')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('b.df_status',0)->group_by('a.assigned_user')->get();
if($q->num_rows()>0){
  foreach($q->result() as $rows){

$q = $this->db->select('a.assigned_user, a.end_date,a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, d.department, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id')->join('departments d','a.department_id=d.department_id')->join('system_users f','a.assigned_user=f.user_id','left')->where('a.task_status',0)->where('a.assigned_user',$rows->assigned_user)->where('a.end_date<',date('Y-m-d'))->get();

if($q->num_rows()>0){

$c = 1;

  foreach($q->result() as $row){
    $pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,2);
        if($row->po_id>0){
          $dfno = $row->df_no;
          if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
          {
            $dfreleasedate = date('d-m-Y',strtotime($row->added_on));
          }else{
            $dfreleasedate = "";
          }
          
          
          
        }else{
          $dfno = "NA";
          $dfreleasedate = "NA";
        }
        if($dfreleasedate=='01-01-1970'){
          $dfreleasedate='NA';
        }else{
          $dfreleasedate = $dfreleasedate;
        }
        $enddate = date('d-m-Y',strtotime($row->end_date));
       $pendingsince =  "<strong style='color:green;font-weight:bold;'>".$pendinddays." Days</strong>";

$html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$c.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfreleasedate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->department.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->first_name." ".$row->last_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->task_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$enddate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'. $pendingsince.'</td>
  </tr>';

$c++;}

}
}
}
$html.='</tbody></table><br><br>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Todays_due_task".date('Y-m-d').".pdf"; //Linux

 $pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');
 
    
}


public function completedtaskontimenotification()
{

$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("TODAYS DUE TASK");
$pdf->SetSubject('TODAYS DUE TASK');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://gamavis.com/PMS/assets/images/shubhampack.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';


$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="8" style="background-color:lightgrey; text-align:center; font-size:15px; font-weight:bold;">Todays Completed Tasks Ontime '.date("d-m-Y").'</th>
</tr>
<tr>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF No</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF Release Date</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Department </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Accountable Person </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Name </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Completion Date </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Last Remarks (If Any) </b></th>

</tr><tbody>';  
$todays = $this->uri->segment(3);
$q = $this->db->select('a.assigned_user')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id')->where('b.df_status',0)->group_by('a.assigned_user')->get();
if($q->num_rows()>0){
  foreach($q->result() as $rows){

$q = $this->db->select('a.assigned_user, a.end_date,a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, d.department, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id')->join('departments d','a.department_id=d.department_id')->join('system_users f','a.assigned_user=f.user_id','left')->where('a.task_status',1)->where('a.assigned_user',$rows->assigned_user)->where('a.end_date',date('Y-m-d'))->or_where('a.end_date<',date('Y-m-d'))->get();

if($q->num_rows()>0){

$c = 1;

  foreach($q->result() as $row){
    $pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,2);
        if($row->po_id>0){
          $dfno = $row->df_no;
          if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
          {
            $dfreleasedate = date('d-m-Y',strtotime($row->added_on));
          }else{
            $dfreleasedate = "";
          }
          
          
          
        }else{
          $dfno = "NA";
          $dfreleasedate = "NA";
        }
        if($dfreleasedate=='01-01-1970'){
          $dfreleasedate='NA';
        }else{
          $dfreleasedate = $dfreleasedate;
        }
        $enddate = date('d-m-Y',strtotime($row->end_date));
       $pendingsince =  "<strong style='color:green;font-weight:bold;'>".$pendinddays." Days</strong>";

$html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$c.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfreleasedate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->department.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->first_name." ".$row->last_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->task_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$enddate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'. $pendingsince.'</td>
  </tr>';

$c++;}

}
}
}
$html.='</tbody></table><br><br>';

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Todays_due_task".date('Y-m-d').".pdf"; //Linux

 $pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');
 
    
}


public function dfwisetaskcompltedreporttilldate()
{

$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Saurabh Dubey');
$pdf->SetTitle("TODAYS DUE TASK");
$pdf->SetSubject('TODAYS DUE TASK');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(5, 2, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://gamavis.com/PMS/assets/images/shubhampack.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';
$todays = $this->uri->segment(3);
$q = $this->db->select('b.id, b.df_no')->from('df_release b')->where('b.df_status',0)->get();
if($q->num_rows()>0){
  foreach($q->result() as $rows){

$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="9" style="background-color:lightgrey; text-align:center; font-size:15px; font-weight:bold;">DF WISE COMPLETED TASKS - DF NO '.$rows->id.' : '.$rows->df_no.'</th>
</tr>
<tr>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Sr No.</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF No</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>DF Release Date</b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Department </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Accountable Person </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Name </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Task Planned Date </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Actual Date </b></th>
<th style="background-color:lightgrey; text-align:center; font-size:12px;"><b>Last Remarks (If Any) </b></th>

</tr><tbody>';  


$q = $this->db->select('a.assigned_user, a.end_date,a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, d.department, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency, a.task_completed_on')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id','left')->join('departments d','a.department_id=d.department_id','left')->join('system_users f','a.assigned_user=f.user_id','left')->where('a.task_status',1)->where('a.df_id',$rows->id)->get();

if($q->num_rows()>0){

$c = 1;

  foreach($q->result() as $row){
    if(date('Y-m-d',strtotime($row->task_completed_on))>$row->end_date){
      $pendinddays=$this->task->getDays($row->end_date,date('Y-m-d',strtotime($row->task_completed_on)),2);
      $b = " Delay"
    }else{
       $pendinddays=$this->task->getDays($row->end_date,date('Y-m-d',strtotime($row->task_completed_on)),2);
       $b = " Before"
    }
    
        if($row->po_id>0){
          $dfno = $row->df_no;
          if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
          {
            $dfreleasedate = date('d-m-Y',strtotime($row->added_on));
          }else{
            $dfreleasedate = "";
          }
          
          
          
        }else{
          $dfno = "NA";
          $dfreleasedate = "NA";
        }
        if($dfreleasedate=='01-01-1970'){
          $dfreleasedate='NA';
        }else{
          $dfreleasedate = $dfreleasedate;
        }
        $enddate = date('d-m-Y',strtotime($row->end_date));
       $pendingsince =  "<strong style='color:green;font-weight:bold;'>".$pendinddays." Days</strong>";
       $actualdate = date('d-m-Y h:i A',strtotime($row->task_completed_on));
      $html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$c.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfno.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfreleasedate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->department.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->first_name." ".$row->last_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row->task_name.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$enddate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$actualdate.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'. $pendingsince.' '.$b.'</td>
  </tr>';

$c++;}


$html.='</tbody></table><br><br>';
}
}
}
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Todays_due_task".date('Y-m-d').".pdf"; //Linux

 $pdf->Output($fileNL, 'F');
$pdf->Output($fileNL, 'I');
 
    
}



}