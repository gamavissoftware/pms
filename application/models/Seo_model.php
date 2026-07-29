<?php
class Seo_model extends CI_Model
{
	public function insert_project($data)
	{
		$query = $this->db->insert('seo_project',$data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	public function uploadFileRecords($data) {
		$this->db->insert('seo_companyDetails',$data);
		return $this->db->affected_rows();
	}

	public function getIndianCities($searchtrm) {
		$ara=array();
		$query = $this->db->select('city_id, city_name')
				 		  ->from('cities')
				 		  ->like('city_name',$searchtrm,'both',false)
				 		  ->get();

		if($query) {
 			 return $query->result();
		} else {
			return $ara;
		}
	}

	public function saveCompanyDetails($data) {
		
		$this->db->insert('company_service_details',$data);
		return $this->db->affected_rows();
	}

	public function seo_saveCities($saveCities) {
		
		$this->db->insert('seo_cities',$saveCities);
		return $this->db->affected_rows();
		
	}

	public function getDetails() {
		// $ara=array();
		$query = $this->db->select('*')
				 		  ->from('seo_project')
				 		  ->order_by('id','asc')
				 		  ->get();

		if($query) {
 			 return $query->result();
		} else {
			return false;
		}
	}

	public function editRecords($projectID) {
		// $ara=array();
		$query = $this->db->select('*')
				 		  ->from('seo_project')
				 		  // ->order_by('id','asc')
				 		  ->join('company_service_details', 'company_service_details.project_id = seo_project.id')
				 		  ->where('seo_project.id',$projectID)
				 		  ->get();

		if($query) {
 			 return $query->result();
		} else {
			return false;
		}
	}

		public function getRows($projectID) {
		// $ara=array();
		$query = $this->db->select('*')
				 		  ->from('company_service_details')
				 		  ->where('project_id',$projectID)
				 		  ->get();

		if($query) {
 			 return $query->result();
		} else {
			return false;
		}
	}

	public function getMatchedCities($projectID) {
		// $ara=array();
		$query = $this->db->select('seo_cities.id as id,cities.city_name as city_name')
				 		  ->from('seo_cities')
				 		  ->join('cities', 'cities.city_id = seo_cities.city_id')
				 		  ->where('seo_cities.project_id',$projectID)
				 		  ->get();

		if($query) {
 			 return $query->result();
		} else {
			return false;
		}
	}

	public function updateProject($updateProject, $projectID) {

		$query = $this->db->where('id', $projectID)
						  ->update('seo_project', $updateProject);
		return $this->db->affected_rows();
	}

	public function updateProduct($updateProduct) {

		$query = $this->db->insert('company_service_details', $updateProduct);
		return $this->db->affected_rows();
	}

	public function seo_updateCities($updateCities) {

		$query = $this->db->insert('seo_cities', $updateCities);
		return $this->db->affected_rows();
	}

	public function delRecord($delID) {
		$this->db->where('id', $delID)
				 ->delete('company_service_details');

		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	public function delCity($delID) {
		$this->db->where('id', $delID)
				 ->delete('seo_cities');

		if ($this->db->affected_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}
}
	?>