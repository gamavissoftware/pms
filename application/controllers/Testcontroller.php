<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testcontroller extends CI_Controller {
	
	public function __construct()
	{
	   // echo phpinfo();exit;
		parent::__construct();
			$session = $this->session->userdata('logged_in');
	
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
	  $config = array();  
        $config['protocol'] = 'smtp';  
        $config['smtp_host'] = 'smtpout.secureserver.net';  
        $config['smtp_user'] = 'mitr@prestomitr.com';  
        $config['smtp_pass'] = 'Presto@123!@#';   
        $config['smtp_port'] = 587;  
        $this->email->initialize($config);  
        $this->email->set_newline("\r\n");  
        $this->load->library('email', $config);
		
	}
	
	function sendmail()
	{
	    
	   $id= $this->uri->segment(3);
	    $SUB = "PATTERN MASTER ASSIGNED TASK REPORT OF MONTH  ".date('F-Y');
$message="HELLO--'.$id.'";


					$this->email->set_mailtype("html");
				   
					$this->email->to("sdsrbh5@gmail.com");
				

					$this->email->from('donotreply@skexports.in');
    				$this->email->subject($SUB);
    			    $this->email->message($message);
    				$result11=$this->email->send();
    				$this->email->print_debugger();
	    
	    
	}
	
	
	
	    
}