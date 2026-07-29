<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Master_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
	}
	
	public function insert_record($table,$data)
	{
		
		 $this->db->insert($table,$data);
		 $result = $this->db->insert_id();
		 return $result;
		
	}
	
	public function varification($table,$varification_field,$varify_data)
	{
		
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($varification_field,$varify_data);
		$query = $this->db->get();
		return $query->result();
		
		
	}
	function update_records($table,$data,$identifier,$field_name)
	{	
		 $this->db->where($field_name,$identifier);
		 $this->db->update($table,$data);
		 return true;
		
		}
	public function select_state($country)
	{
		$this->db->select('*');
		$this->db->distinct();
		$this->db->from('states');
		$this->db->where('country_id',$country);
		$this->db->order_by('state_name','asc');
		$res = $this->db->get();
		return $res->result();
		}
		
	public function select_city($state)
	{
		$this->db->select('*');
		$this->db->distinct();
		$this->db->from('cities');
		$this->db->where('state_id',$state);
		$this->db->order_by('city_name','asc');
		$res = $this->db->get();
		return $res->result();
		}
	public function select_department($business_location)
	{
		$this->db->select('*');
		$this->db->distinct();
		$this->db->from('departments');
		$this->db->where('business_loc_id',$business_location);
		$this->db->where('status','1');
		$this->db->order_by('department','asc');
		$res = $this->db->get();
		return $res->result();
		}
		
	public function select_user_roles($department)
	{
		$this->db->select('*');
		$this->db->distinct();
		$this->db->from('user_role');
		$this->db->where('department_id',$department);
		$this->db->order_by('user_role','asc');
		$res = $this->db->get();
		return $res->result();
		}
		
		
		
		function getallsystemusers()
		{
			$allusers=array();
			$resty=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where_not_in('user_role_id','56,1')->order_by('first_name','ASC')->get();
			if($resty->num_rows()>0)
			{
				foreach($resty->result() as $resty1)
				{
					$allusers[]=$resty1->user_id;
					
				}
				
				return $allusers;
			}else
			{
				
				return $allusers;
				
			}
			
			
		}
		
		
			function getsusername($userid)
	{
		$alluser='';
		$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->order_by('first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				//$alluser[]=$rest1->first_name." ".$rest1->last_name;
				$alluser=strtoupper($rest1->first_name)." ".strtoupper($rest1->last_name);
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}
	
	
	function getmodule($userid)
	{
		$module=array();
		$mod=$this->db->select('moduleid')->from('module_access')->where('role_id',$userid)->where('access','1')->get();
		if($mod->num_rows()>0)
		{
			foreach($mod->result() as $mod1)
			{
				
				$module[]=$mod1->moduleid;
			}
			
			return $module;
		}else{ return $module;  }

		
		
	}
	
	function getmodulename($moduleid)
	{
		$mname='';
		$mname1=$this->db->select('modulename')->from('system_modules')->where('id',$moduleid)->get();
		if($mname1->num_rows()>0)
		{
			foreach($mname1->result() as $mname11);
			$mname=$mname11->modulename;
			return $mname;
						
		}else{
			
			return $mname;
			
		}
		
		
		
	}
	
	function getsubmodule($moduleid,$user)
	{
		$submod=array();
		$resty=$this->db->select('a.submodule,a.id')->from('submodule a')->join('module_capablity b','a.id=b.submoduleid')->where('madd','1')->where('b.role_id',$user)->where('b.moduleid',$moduleid)->or_where('medit','1')->where('b.role_id',$user)->where('b.moduleid',$moduleid)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $resty1)
			{
				$submod[]=$resty1->id;
				
			}
			
			return $submod;
			
		}else
		{
			return $submod;
			
		}
	
	}
	
	function submodulename($submodule)
	{
		$subname='';
		$subm=$this->db->select('submodule')->from('submodule')->where('id',$submodule)->get();
		if($subm->num_rows()>0)
		{
			foreach($subm->result() as $subm1);
			
			$subname=$subm1->submodule;
			return $subname;
		}else{
			return $subname;
		}
		
	}
	
	function getpermission ($userid,$submodule)
	{
		
		$permi='';
		$sep='';
		$permiss=$this->db->select('madd,medit')->from('module_capablity')->where('submoduleid',$submodule)->where('role_id',$userid)->get();
		if($permiss->num_rows()>0)
		{
			
			foreach($permiss->result() as $permiss1);
			
			if($permiss1->madd=='1')
			{
				$permi.="ADD";
				$sep=" |";
			}
			
			if($permiss1->medit=='1')
			{
				
				$permi.=$sep." EDIT";
			}
			
			return $permi;
			
			
			
			
		}
		
		else{
			return $permi;
			
		}
		
	}
		

	function updateInstrument($data, $id) {

	
		$this->db->where('id', $id)
				 ->update('salesforce_order_instruments', $data);
		return $this->db->affected_rows();
	}

	function deleteInstrument($id) {
		$this->db->where('id', $id)
				 ->delete('salesforce_order_instruments');
		return $this->db->affected_rows();
	}
	public function update_po($data, $id) {
			$this->db->where('order_id', $id)
				 	 ->update('prestogroup_orders', $data);
		return $this->db->affected_rows();
	}


	public function getInternalOrderID($order_id) {
	$sql = $this->db->select('internal_order_no')
	->from('prestogroup_orders')
	->where('order_id',$order_id)
	->get();
	if ($sql->num_rows() > 0) {
	foreach($sql->result() as $sqll);
	
	return $sqll->internal_order_no;
	
	}else
	{
	return '';
	}
	}
	
	
	
	public function add_material_request($data) {
		$this->db->insert('service_material_request', $data);
		return $this->db->insert_id();
	}

	public function add_request_items($data) {
		$this->db->insert('service_material_request_items', $data);
		return $this->db->affected_rows();
	}

	public function getMaterialRequestData() {
	$query = $this->db->select('a.id,a.flag, a.service_engineer_name, a.customer_name, a.io_no, b.first_name, b.last_name')
					  ->from('service_material_request a')
					  ->join('system_users b','a.service_engineer_name=b.user_id')
					  ->order_by('a.flag','ASC')
					  ->order_by('a.id','desc')
					  //->group_by('a.id')
					  ->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function getItemDetails($id) {
		$query = $this->db->select('a.quantity, a.remarks, b.part, c.shortname')
						  ->from('service_material_request_items a')
						  ->join('machine_parts_with_picture b','a.item_name=b.id')
						  ->join('units c','c.id=b.unit')
						  ->where('a.service_engineer_id',$id)
						  ->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			}

	}

	public function getMaterialRequest($id) {
		$query = $this->db->select('a.service_engineer_name, a.customer_name, a.io_no, b.first_name, b.last_name')
						  ->from('service_material_request a')
						  ->join('system_users b','a.service_engineer_name=b.user_id')
						  ->where('a.id', $id)
						  //->group_by('a.id')
						  ->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			}
	}

		public function getRequestItems($id) {
		$query = $this->db->select('a.id, a.quantity, a.remarks,b.id as itemid, b.part, c.shortname')
						  ->from('service_material_request_items a')
						  ->join('machine_parts_with_picture b','a.item_name=b.id')
						  ->join('units c','c.id=a.unit')
						  ->where('a.material_request_id', $id)
						  //->group_by('a.id')
						  ->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			}
	}

	public function getCurrentStock($id) {
		$query = $this->db->select('current_stock')
						  ->from('machine_parts_with_picture')
						  ->where('id', $id)
						  ->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			}
	}

	public function challan_no() {
		$query = $this->db->select('id')
						  ->from('service_material_request')
						  ->get();
			return $query->num_rows();
	}

	public function addQty($data, $updateID) {
		$this->db->where('id', $updateID)
				 ->update('service_material_request_items', $data);
			return $this->db->affected_rows();
	}

	public function updateStock($updateData, $id) {
			$this->db->where('id', $id)
					 ->update('machine_parts_with_picture', $updateData);
			return $this->db->affected_rows();
	}
}
