<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Leadfunnel_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}
	
	function add_lead_funnel($data) {
	    $this->db->insert('lead_funnel', $data);
	    return $this->db->insert_id();
	}
	
		function add_lead_funnel_fields($data) {
	    $this->db->insert('lead_funnel_fields', $data);
	    return $this->db->affected_rows();
	}
	
}