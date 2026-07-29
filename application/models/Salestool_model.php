<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Salestool_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}

	public function add_machine_details($data) {
		$this->db->insert('newsalestool_machines', $data);
		return $this->db->insert_id();
	}

	public function add_promotion($data) {
		$this->db->insert('newsalestool_promotion', $data);
		return $this->db->affected_rows();
	}

	public function add_city($data) {
		$this->db->insert('newsalestool_clients', $data);
		return $this->db->affected_rows();
	}

	public function add_charges($data) {
		$this->db->insert('newsalestool_misc', $data);
		return $this->db->affected_rows();
	}

	public function getSalesToolDetails($machine_id) {
		$query = $this->db->select('machine_name, price, usp, discount, pdf_file, video')
						 ->from('newsalestool_machines')
						 ->where('id', $machine_id)
						 ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getSalesToolMisc($machine_id) {
	   $query = $this->db->select('charges_name, charges')
						 ->from('newsalestool_misc')
						 ->where('machine_id', $machine_id)
						 ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getSalesToolClients($machine_id) {
	   $query = $this->db->select('client_name, city, industry')
						 ->from('newsalestool_clients')
						 ->where('machine_id', $machine_id)
						 ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getMachineDetails($machine_id) {
		$query = $this->db->select('instruments_name, mvalue')
						 ->from('presto_instruments')
						 ->where('id', $machine_id)
						 ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getEditMachines($id) {
		$query = $this->db->select('machine_name, price, usp, discount, video, pdf_file')
						  ->from('newsalestool_machines')
						  ->where('id', $id)
						  ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getEditPromotion($id) {
		$query = $this->db->select('promotion_name, promotion_price, description')
						  ->from('newsalestool_promotion')
						  ->where('machine_id', $id)
						  ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getEditClients($id) {
		$query = $this->db->select('client_name, city, industry')
						  ->from('newsalestool_clients')
						  ->where('machine_id', $id)
						  ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

	public function getEditMisc($id) {
		$query = $this->db->select('charges_name, charges')
						  ->from('newsalestool_misc')
						  ->where('machine_id', $id)
						  ->get();

			if($query->num_rows()) {
				return $query->result();
			}
	}

}