<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatch extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        // $session = $this->session->userdata('logged_in');
        // echo "<pre>"; print_r($session); exit;


        if(!empty($this->session->userdata['logged_in']['smtpemailid'])){
            $smtpemail  = $this->session->userdata['logged_in']['smtpemailid'];
        }else{
            $smtpemail = '';
        }
        if(!empty($this->session->userdata['logged_in']['smtppassword'])){
            $smtppassword  = $this->session->userdata['logged_in']['smtppassword'];
        }else{
            $smtppassword='';
        }
        
        if(!empty($this->session->userdata['logged_in']['smtpemailid']) && !empty($this->session->userdata['logged_in']['smtppassword'])){
        $config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => $smtpemail,
        'smtp_pass' => $smtppassword,
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
        }


	}

	function index()
	{
		//$this->load->view('dispatch/generate_qr');
       // $this->load->view('dispatch/newqr');
         $this->load->view('dispatch/qr_with_logo');
	}

	 public function generate()
    {
        $data[] = array();
        $this->load->view('qrcode/generate', $data);
    }
    public function qrcodeGenerator()
    {
        $qrtext = $this->input->post('qrcode_text');    
        if(isset($qrtext))
        {
        
            $SERVERFILEPATH = FCPATH."data/qrcode/";
            $text = $qrtext;
            $text1= substr($text, 0,9);    
            $folder = $SERVERFILEPATH;
            $file_name1 = $text1."-Qrcode" . rand(2,200) . ".png";
            $file_name = $folder.$file_name1;
            QRcode::png($text,$file_name); 
            echo"<center><img src=".base_url().'data/qrcode/'.$file_name1."></center";
        }
        else
        {
            echo 'No Text Entered';
        }  
    }



      function sendintroemailtocustomer(){
     // echo $this->session->userdata['logged_in']['smtppassword']; exit;
    ini_set('memory_limit', '6144M');
    $file = UPLOADPATH."reference/Shubham-Pack-Catalog.pdf";
    if(!empty($this->session->userdata['logged_in']['smtpemailid'])){
            $smtpemail  = $this->session->userdata['logged_in']['smtpemailid'];
        }else{
            $smtpemail = '';
        }
        if(!empty($this->session->userdata['logged_in']['smtppassword'])){
            $smtppassword  = $this->session->userdata['logged_in']['smtppassword'];
        }else{
            $smtppassword='';
        }
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('title, first_name, last_name, contact_number, smtp_email, smtp_password, email')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $userinfo);
    if($userinfo->smtp_email<>''){
    $contact_number = $userinfo->contact_number;
    $usersignature = ucwords(strtolower($userinfo->title." ".$userinfo->first_name." ".$userinfo->last_name))."<br>"."Marketing<br>"."+91-".$contact_number."<br>".$smtpemail;
    $exhibition = $this->input->post('Exhibition');
    $subject = $this->input->post('subject');
    $message = $this->input->post('email_template')."<br>".$usersignature;
    $q = $this->db->select('b.email')->from('leads a')->join('customer_detail b','a.company_name=b.id','left')->where('a.id',$this->uri->segment(4))->where('a.added_by',$user_id)->get();
    if($q->num_rows()>0){

        $markcctoshubhamsir = 'shubham@shubhampack.com,'.$smtpemail;
        foreach ($q->result() as $leaddata);
            $this->email->clear(TRUE);
            $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            //$this->email->from('shivom@shubhampack.com', 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            //$this->email->to('mangleshup@gmail.com,sdsrbh5@gmail.com');
            $this->email->to($leaddata->email);
            if($smtpemail=='export@shubhampack.com'){
                $this->email->cc('projects1@shubhampack.com, shubham@shubhampack.com');
            }else{
                 $this->email->cc($markcctoshubhamsir);
            }
            
            $this->email->bcc('mangleshup@gmail.com,saurabh@gamavis.com');
            $this->email->subject($subject);
            $this->email->message($message);
            $this->email->attach($file);
            $customeremail = $leaddata->email;
            $this->email->send();
           //echo $this->email->print_debugger(); exit;
                
       // $data3 = array('welcome_email_status'=>1);
       // $leadid = $this->uri->segment(4);
       // $this->db->where('id',$leadid);
       // $this->db->update('leads',$data3);

       $this->session->set_flashdata('message','<div class="alert alert-danger alert-success">Thank You! Welcome Email successfully sent. You also marked in CC. </div>');
            redirect(page_url.'Leads/lead_stages/1');

    }
    }else{
        echo "<h5>Please Share your email SMPT setup with Gamavis Team to activate email functionality in your account.</h5>"; exit;
    }
    //echo $this->email->print_debugger(); exit;


  }


  function sendexhibitionintroemail(){
     // echo $this->session->userdata['logged_in']['smtppassword']; exit;
    ini_set('memory_limit', '6144M');
    $file = UPLOADPATH."reference/Shubham-Pack-Catalog.pdf";
    if(!empty($this->session->userdata['logged_in']['smtpemailid'])){
            $smtpemail  = $this->session->userdata['logged_in']['smtpemailid'];
        }else{
            $smtpemail = '';
        }
        if(!empty($this->session->userdata['logged_in']['smtppassword'])){
            $smtppassword  = $this->session->userdata['logged_in']['smtppassword'];
        }else{
            $smtppassword='';
        }
    $user_id =$this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('title, first_name, last_name, contact_number, smtp_email, smtp_password, email')->from('system_users')->where('user_id',$user_id)->get();
    foreach($q->result() as $userinfo);
    if($userinfo->smtp_email<>''){
    $contact_number = $userinfo->contact_number;
    $usersignature = ucwords(strtolower($userinfo->title." ".$userinfo->first_name." ".$userinfo->last_name))."<br>"."Marketing<br>"."+91-".$contact_number."<br>".$smtpemail;
    $exhibition = $this->input->post('Exhibition');
    $subject = $this->input->post('subject');
    $message = $this->input->post('email_template')."<br>".$usersignature;
    $q = $this->db->select('email')->from('customer_detail')->where('id',$this->uri->segment(3))->get();
    //echo "<pre>"; print_r($q->result()); exit;
    if($q->num_rows()>0){


        foreach ($q->result() as $leaddata);
            $this->email->clear(TRUE);
            $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            //$this->email->from('shivom@shubhampack.com', 'Shubham Flexible Packaging Machines Pvt. Ltd.');
            //$this->email->to('mangleshup@gmail.com');
            $this->email->to($leaddata->email);
            if($smtpemail=='export@shubhampack.com'){
                $this->email->cc('projects1@shubhampack.com, shubham@shubhampack.com, virendra@shubhampack.com');
            }else{
                 $this->email->cc('virendra@shubhampack.com, shubham@shubhampack.com');
            }
            
            //$this->email->bcc('mangleshup@gmail.com,saurabh@gamavis.com');
            $this->email->subject($subject);
            $this->email->message($message);
            $this->email->attach($file);
            $customeremail = $leaddata->email;
            $this->email->send();
           //echo $this->email->print_debugger(); exit;
                
       $data3 = array('exhibitiion_email_sent'=>1);
       $id = $this->uri->segment(3);
       $this->db->where('id',$id);
       $this->db->update('customer_detail',$data3);

       $this->session->set_flashdata('message','<div class="alert alert-danger alert-success">Thank You! Welcome Email successfully sent. You also marked in CC. </div>');
            redirect(page_url.'Customer/viewyourcustomers/');

    }
    }else{
        echo "<h5>Please Share your email SMPT setup with Gamavis Team to activate email functionality in your account.</h5>"; exit;
    }
    //echo $this->email->print_debugger(); exit;


  }



   function posendintroemailtocustomer() {
    ini_set('memory_limit', '6144M');

     $smtpemail = 'info@shubhampack.com';
    $smtppass = 'kbitoslhbmbpbazk';

    $config = array(
        'protocol'  => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => $smtpemail,
        'smtp_pass' => $smtppass,
        'mailtype'  => 'html',
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
    );
    $this->email->initialize($config);
 $this->load->library('email', $config);

    $user_id = $this->session->userdata['logged_in']['user_id'];
    $q = $this->db->select('title, first_name, last_name, contact_number')->from('system_users')->where('user_id', $user_id)->get();
    $userinfo = $q->row();

    $subject = "Potential Delivery Delays Due to Regional Tensions - Shubham Flexible Packaging Machines Pvt. Ltd.";

    $message = '
    <html>
  <head>
    <style>
      body {
        font-family: Arial, sans-serif;
        color: #333333;
        background-color: #f4f4f4;
        padding: 20px;
      }
      .container {
        max-width: 700px;
        margin: auto;
        background-color: #ffffff;
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
      }
      .logo {
        max-width: 180px;
        margin-bottom: 20px;
      }
      .title {
        font-size: 22px;
        font-weight: bold;
        color: #004080;
        margin-bottom: 15px;
      }
      p {
        line-height: 1.6;
        margin: 10px 0;
      }
      .signature {
        margin-top: 30px;
      }
      .footer {
        font-size: 12px;
        color: #888888;
        margin-top: 40px;
        border-top: 1px solid #e0e0e0;
        padding-top: 10px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      
      <p>Dear Valued Client,</p>

      <p>Greetings from <strong>Shubham Pack</strong>.</p>

      <p>We hope this message finds you well.</p> 

      <p>We wish to inform you that due to the recent escalation of tensions between India and Pakistan, there is a possibility of delays in our daily operations. This may impact the delivery of ongoing machinery and spare part orders.</p>

      <p>These developments may affect transportation routes, border procedures, and customs clearances. Please be assured that we are closely monitoring the situation and taking all necessary steps to minimize any disruption. However, some delays may still occur due to factors beyond our immediate control.</p>

      <p>We sincerely appreciate your understanding and continued support during this time. Should you have any urgent concerns, feel free to reach out to us. We will keep you updated as the situation evolves.</p>

      <p>Thank you for your trust and cooperation.</p>

      <div class="signature">
        <strong>Shubham Sharma</strong><br>
        Executive Director – Business Development<br>
        <a href="mailto:shubham@shubhampack.com">shubham@shubhampack.com</a><br>
        <a href="https://www.shubhampack.com" target="_blank">www.shubhampack.com</a>
      </div>

      <div class="footer">
        © Shubham Flexible Packaging Machines Pvt. Ltd.
      </div>
    </div>
  </body>
</html>

    ';

    //echo $message; exit;

    // Load email library once with config
   // $this->load->library('email', $config);

    $e_mail = $this->db->select('a.id, b.df_id, d.email')
        ->from('df_release a')
        ->join('poreceived b', 'a.id = b.df_id', 'left')
        ->join('leads c', 'c.id = b.lead_id', 'left')
        ->join('customer_detail d', 'c.company_name = d.id')
        ->where('a.exclude_df_for_email', 0)
        ->where('d.email !=', '')
        ->get();

        //echo "<pre>"; print_r($e_mail->result()); exit;

    if ($e_mail->num_rows() > 0) {
        //echo "text"; exit;
        foreach ($e_mail->result() as $rowss) {
            $recipient = trim($rowss->email);
            //echo $recipient; exit;
            if (!empty($recipient)) {
                $this->email->clear(TRUE);
                $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
                //$this->email->to('mangleshup@gmail.com');
               $this->email->to($recipient);
            $this->email->cc('shubham@shubhampack.com');
                $this->email->bcc('mangleshup@gmail.com,saurabh@gamavis.com');
                $this->email->subject($subject);
                $this->email->message($message);
                $this->email->send();
               //echo  $this->email->print_debugger(); exit;
            }
        }
    }

    $this->session->set_flashdata('message', '<div class="alert alert-success">Thank You! Email successfully sent. You also marked in BCC.</div>');
    redirect(page_url . 'Leads/lead_stages/1');
}


function textemailsend(){
     $smtpemail = 'info@shubhampack.com';
    $smtppass = 'kbitoslhbmbpbazk';

    $config = array(
        'protocol'  => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => $smtpemail,
        'smtp_pass' => $smtppass,
        'mailtype'  => 'html',
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
    );
    $this->email->initialize($config);
 $this->load->library('email', $config);
$subject = "text";
 $message = '
    <html>
    <head>
      <style>
        body { font-family: Arial, sans-serif; color: #333333; background-color: #f9f9f9; padding: 20px; }
        .container { width: 90%; max-width: 700px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 200px; }
        .title { font-size: 20px; font-weight: bold; color: #004080; margin-top: 20px; }
        .signature { margin-top: 30px; }
        .footer { font-size: 12px; color: #888888; margin-top: 40px; text-align: center; }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="header">
          <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Shubham Pack Logo" class="logo">
        </div>

        <p class="title">Subject: Potential Delivery Delays Due to Regional Tensions</p>

        <p>Dear Valued Client,</p>

        <p>Greetings from <strong>Shubham Pack</strong>.</p>

        <p>We hope this message finds you well. We wish to inform you that due to the recent escalation of tensions between India and Pakistan, there is a possibility of delays in our daily operations. This may impact the delivery of ongoing machinery and spare part orders.</p>

        <p>These developments may affect transportation routes, border procedures, and customs clearances. Please be assured that we are closely monitoring the situation and taking all necessary steps to minimize any disruption. However, some delays may still occur due to factors beyond our immediate control.</p>

        <p>We sincerely appreciate your understanding and continued support during this time. Should you have any urgent concerns, feel free to reach out to us. We will keep you updated as the situation evolves.</p>

        <p>Thank you for your trust and cooperation.</p>

        <div class="signature">
          <strong>Shubham Sharma</strong><br>
          Executive Director – Business Development<br>
          <a href="mailto:info@shubhampack.com">info@shubhampack.com</a><br>
          <a href="https://www.shubhampack.com" target="_blank">www.shubhampack.com</a>
        </div>

        <div class="footer">
          © Shubham Flexible Packaging Machines Pvt. Ltd.
        </div>
      </div>
    </body>
    </html>
    ';
      //$this->email->clear(TRUE);
                $this->email->from($smtpemail, 'Shubham Flexible Packaging Machines Pvt. Ltd.');
                $this->email->to('mangleshup@gmail.com');
                //$this->email->to($recipient);
                $this->email->bcc('mangleshup@gmail.com,saurabh@gamavis.com');
                $this->email->subject($subject);
                $this->email->message($message);
                $this->email->send();
                echo  $this->email->print_debugger(); exit;

}


}