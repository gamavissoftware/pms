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
	$query = $this->db->select('a.id,a.flag, a.service_engineer_name, a.customer_name, a.io_no, b.first_name, b.last_name,a.added_on')
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

	public function getItemDetails($id,$rowid) {
		$query = $this->db->select('a.quantity, a.remarks, b.part, c.shortname')
						  ->from('service_material_request_items a')
						  ->join('machine_parts_with_picture b','a.item_name=b.id')
						  ->join('units c','c.id=b.unit')
						  ->where('a.service_engineer_id',$id)
						  ->where('a.material_request_id',$rowid)
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
		$query = $this->db->select('a.id, a.quantity, a.remarks,b.id as itemid, b.part, c.shortname,a.type')
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

	public function updateFlag($updateFlag, $id) {
			$this->db->where('id', $id)
					 ->update('service_material_request_items', $updateFlag);
			return $this->db->affected_rows();
	}

	public function getItemsForIssue() {
	$query = $this->db->select('a.customer_name, a.io_no, b.first_name, b.last_name, c.id, c.quantity, c.unit, c.remarks, c.in_qty, c.issued, d.id as item_id, d.part, u.shortname,a.added_on')
					  ->from('service_material_request a')
					  ->join('system_users b','a.service_engineer_name=b.user_id')
					  ->join('service_material_request_items c','c.material_request_id=a.id')
					  ->join('machine_parts_with_picture d','d.id=c.item_name')
					  ->join('units u','u.id=c.unit')
					  ->where('c.issued', 0)
					  ->where('c.type','1')
					  ->order_by('a.id','desc')
					  //->group_by('a.id')
					  ->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}
	
	
	
	public function getItemsForIssueforservice($loginID) {
	$query = $this->db->select('a.customer_name, a.io_no, b.first_name, b.last_name, c.id, c.quantity, c.unit, c.remarks, c.in_qty, c.issued, d.id as item_id, d.part, u.shortname')
					  ->from('service_material_request a')
					  ->join('system_users b','a.service_engineer_name=b.user_id')
					  ->join('service_material_request_items c','c.material_request_id=a.id')
					  ->join('machine_parts_with_picture d','d.id=c.item_name')
					  ->join('units u','u.id=c.unit')
					  ->where('c.issued', 0)
					  ->where('a.service_engineer_name', $loginID)
					  ->order_by('a.id','desc')
					  //->group_by('a.id')
					  ->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}
	
	
	public function getItemDetails1($rowid,$id) {
		$query = $this->db->select('a.quantity, a.remarks, b.part, c.shortname')
						  ->from('service_material_request_items a')
						  ->join('machine_parts_with_picture b','a.item_name=b.id')
						  ->join('units c','c.id=b.unit')
						  ->where('a.service_engineer_id',$id)
						  ->where('a.material_request_id',$rowid)
						  ->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			}

	}
	
	
	
	public function getMaterialRequestData1($loginID) {
	$query = $this->db->select('a.id,a.flag, a.service_engineer_name, a.customer_name, a.io_no, b.first_name, b.last_name,a.added_on')
					  ->from('service_material_request a')
					  ->join('system_users b','a.service_engineer_name=b.user_id')
					   ->where('a.service_engineer_name', $loginID)
					  ->order_by('a.flag','ASC')
					  ->order_by('a.id','desc')
					  //->group_by('a.id')
					  ->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}	

	public function getAllModules() {
	$query = $this->db->select('id,product_master_name')
					  ->from('product_master')
					  ->get();
		if ($query->num_rows() > 0) {
			return $query->result();
		}
	}

	public function addClientDetails($data) {
			$this->db->insert('new_client', $data);
			return $this->db->insert_id();
	}

	public function addModule($module_data) {
		$this->db->insert('client_modules', $module_data);
		return $this->db->affected_rows();
	}

	public function getClientRecords() {
	   $query = $this->db->select('a.*, c.product_master_name')
						 ->from('new_client a')
						 ->join('client_modules b', 'a.id=b.client_id')
						 ->join('product_master c', 'b.module_id=c.id')
						 ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			}

	}

	/*--------------------------------- MEHTA COSMETICS -----------------------------------------------------*/

	function getFinGoodsDetails($edit_id) {
		$res = '';

		$sql = $this->db->select('id, type_name, status')
						->from('finished_goods_type')
						->where('id', $edit_id)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getFinGoodsType() {
		$res = '';
		$sql = $this->db->select('id, type_name')
						->from('finished_goods_type')
						->where('status', 1)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getFinGoods($edit_id) {
		$res = '';
		$sql = $this->db->select('rebrand,id,volume, hsncode,type, instruments_name, model_number, mvalue, discount_price, image, status,unit,trial_reading,spec_file,msds_file,pack_size,density, distributorprice, discount_price_distributor')
					    ->from('presto_instruments')
					    ->where('id', $edit_id)
					    ->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getSubParts($edit_id) {
		$res = '';
		$sql = $this->db->select('id, category')
						  ->from('presto_machine_part_category')
						  ->where('id', $edit_id)
						  ->get();


		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getSelectedSubParts($id) {
		$res = '';
		$name = array();
		$sql = $this->db->select('c.name')
					  	->from('presto_instruments a')
					  	->join('finished_goods_sub_parts b', 'b.fin_good_id=a.id')
					  	->join('sub_parts c', 'c.id=b.sub_part_id')
					 	->where('a.id', $id)
					    ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$name[] = $row->name;
			}
		}

		$res = implode(',', $name);
		return $res;			
	}

	function checkIfSubPartsSelected($fin_good_id) {
		$res = '';
		$sub_part_id = array();
		// echo "<pre>";print_r($sub_part_id);exit;
		$sql = $this->db->select('sub_part_id')
						->from('finished_goods_sub_parts')
						->where('fin_good_id', $fin_good_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
				$sub_part_id[] = $row->sub_part_id;
			}
		}


		return $sub_part_id;
	}

	function getAllSubParts() {
		$res = '';
		$query = $this->db->select('id, name')
		 				  ->from('sub_parts')
						  ->where('status', 1)
						  ->get();

		if ($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}

	function getEditMachine($edit_id) {
		$res = '';
		$query = $this->db->select('id, machine_name, status')
		 				  ->from('machine')
						  ->where('id', $edit_id)
						  ->get();

		if ($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}

	function getRackLocation() {
		$res = '';
		$query = $this->db->select('id, rack_location,companyname')
		 				  ->from('store_rack_location')
		 				  ->where('status',1)
						  ->get();

		if ($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}
	function getunit(){
	$res = '';
		$query = $this->db->select('id,name,shortname')
		 				  ->from('units')
						  ->get();

		if ($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}
	function getFinGoodsLocations($fin_good_id) {
		$res = '';
		$location = array();
		$query = $this->db->select('company_id')
		 				  ->from('presto_instruments')
		 				  ->where('id', $fin_good_id)
						  ->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$location[] = $row->company_id;
			}
		}

		return $location;
	}

	function getSelectedLocations($id) {
		$res = '';
		$name = array();
		$sql = $this->db->select('b.rack_location')
		 				  ->from('fin_good_locations a')
		 				  ->join('store_rack_location b', 'b.id=a.location')
		 				  ->where('a.fin_good_id', $id)
						  ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$name[] = $row->rack_location;
			}
		}

		$res = implode(',', $name);
		return $res;			
	}

	function getQuotationDetails($quotation_id) {
		$html = '';
		$sql = $this->db->select('a.id, a.qty, a.list_price, a.discount_price, a.net_price, b.instruments_name, b.unit')
						->from('customer_quotation_detail a')
						->join('presto_instruments b', 'b.id=a.product_id')
						->where('a.quotation_id', $quotation_id)
						->where('a.flag', 0)
						->get();

		$i = 1;
		if($sql->num_rows() > 0) {
				$html .= "<table style='width:100%;' class='table table-bordered manglesh' >
				 			<thead align='center'>
								<tr>
									<th style='width:200px'>Product Name</th>
									<th style='width:200px'>Pack Size</th>
									<th style='width:200px'>List Price</th>
									<th style='width:200px'>Discount</th>
									<th style='width:200px'>Net Price</th>
									<th style='width:200px'>Action</th>
								</tr>
							</thead>
							<tbody>";
			foreach($sql->result() as $row) {

				$html .= "<tr>
							<td>".$row->instruments_name."</td>
							<td>".$row->qty."</td>
							<td>".$row->list_price."</td>
							<td>".$row->discount_price."</td>
							<td>".$row->net_price."</td>
							<td><a href='javascript:;' onclick='accept_reject_remarks(".$row->id.")'></a></td>
						  </tr>";
				
				$i++;
			}
				$html .= "</tbody></table>";
		}

		return $html;
	}

	function getAllTnC($term_for) {
		$res = '';
		$sql = $this->db->select('term_conditions')
						->from('customer_quotation_terms_condition')
						->where('term_for', $term_for)
						->where('status', 1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$res = $row->term_conditions;
		}

		return $res;
	}

	function checkIfProductIsApproved($quotation_id) {
		$sql = $this->db->select('flag')
						->from('customer_quotation_detail')
						->where('quotation_id', $quotation_id)
						->where_in('flag', '0, 2', false)
						->get();


		return $sql->num_rows();
	}

	function checkIfLeadProductIsApproved($lead_id) {
		$sql = $this->db->select('flag')
						->from('lead_products')
						->where('lead_id', $lead_id)
						->where_in('flag', '0, 2', false)
						->get();


		return $sql->num_rows();
	}

	function getdiscountpriceeditdata($proid)
	{
		$result = '';
		$res=$this->db->select('id,discount_price')->from('presto_instruments')->where('id',$proid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			$result = $row->discount_price; 
		}

		return $result;
		 
	}

	 function getLeadStageRelation($lead_stage_id) {
    	$res = array();
    	$sql = $this->db->select('stage_relation')
    					->from('lead_stage_relation')
    					->where('lead_stage_id', $lead_stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	foreach($sql->result() as $row) {
	    		$res[] = $row->stage_relation;
	    	}
	    }

	    return $res;
    }

     function getspare_LeadStageRelation($lead_stage_id) {
    	$res = array();
    	$sql = $this->db->select('stage_relation')
    					->from('spares_lead_stage_relation')
    					->where('lead_stage_id', $lead_stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	foreach($sql->result() as $row) {
	    		$res[] = $row->stage_relation;
	    	}
	    }

	    return $res;
    }

     function getLeadStage($stage_id) {
    	$lead_stage = '';
    	$sql = $this->db->select('lead_name')
    					->from('lead_stage')
    					->where('lead_id', $stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	foreach($sql->result() as $row);
	    	$lead_stage = $row->lead_name;
	    }

	    return $lead_stage;
    }

    function getspareLeadStage($stage_id) {
    	$lead_stage = '';
    	$sql = $this->db->select('lead_name')
    					->from('spare_lead_stage')
    					->where('lead_id', $stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	foreach($sql->result() as $row);
	    	$lead_stage = $row->lead_name;
	    }

	    return $lead_stage;
    }

     function getAllLeadStages() {
    	$res = '';
    	$sql = $this->db->select('lead_id, lead_name')
    					->from('lead_stage')
    					->get();

    	if($sql->num_rows() > 0) {
	    	$res = $sql->result();
	    }

	    return $res;
    }

    function getAllspares_LeadStages() {
    	$res = '';
    	$sql = $this->db->select('lead_id, lead_name')
    					->from('spare_lead_stage')
    					->get();

    	if($sql->num_rows() > 0) {
	    	$res = $sql->result();
	    }

	    return $res;
    }

     function getLeadStageDetails($lead_stage_id) {
    	$res = '';
    	$sql = $this->db->select('*')
    					->from('lead_stage')
    					->where('lead_id', $lead_stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	$res = $sql->result();
	    }

	    return $res;
    }

     function getLeadspareStageDetails($lead_stage_id) {
    	$res = '';
    	$sql = $this->db->select('*')
    					->from('spare_lead_stage')
    					->where('lead_id', $lead_stage_id)
    					->get();

    	if($sql->num_rows() > 0) {
	    	$res = $sql->result();
	    }

	    return $res;
    }

    function getProductCompanies($product_id) {
		$res = '';
		$companies = array();
		$query = $this->db->select('company_id')
		 				  ->from('company_products')
		 				  ->where('product_id', $product_id)
						  ->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$companies[] = $row->company_id;
			}
		}

		return $companies;
	}

	function getProductWiseCompanies($product_id) {
		$company_name = array();
		$sql = $this->db->select('b.companyname')
						->from('company_products a')
						->join('store_rack_location b', 'b.id=a.company_id')
						->where('a.product_id', $product_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row) {
				$company_name[] = $row->companyname;
			}
		}

		return implode(', ', $company_name);
	}

	 function getCompanyProducts($company_id) {
		$res = '';
		$products = array();
		$query = $this->db->select('product_id')
		 				  ->from('company_products')
		 				  ->where('company_id', $company_id)
						  ->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$products[] = $row->product_id;
			}
		}

		return $products;
	}

	function getCollectionID() {
		$res = '';
		$query = $this->db->select('id, collection_id')
		 				  ->from('sunder_collection_reference')
		 				  ->order_by('collection_date', 'ASC')
		 				  ->limit(1)
						  ->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row);

				$sql = $this->db->select('id as balance_id, balance')
								->from('sunder_collection_reference_balance')
								->where('collection_id', $row->id)
								->where('active', 1)
								->get();

				if ($sql->num_rows() > 0) {
					foreach ($sql->result() as $rows);
					$res = $rows->balance.'|'.$row->collection_id.'|'.$row->id.'|'.$rows->balance_id;
				}
		}	

		return $res;					  
	}

	function getStateID($state_code) {
		$res = '';
		$sql = $this->db->select('state_id')
		      			->from('states')
						->where('state_code',$state_code)
					 	->get();
		
		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
			$res = $row->state_id;
		}

		return $res;
		
	}


	function get_stock_for_product_static($start_date,$end_date,$company,$product)
	{
		$op=array();
		$op[]=0;
		$avg_rate=array();
		$avg_rate[]=0;
		$fixed_avg_rate=0;
		$taxable=array();
		$taxable[]=0;

		$this->db->select('qty,avg_rate')->from('company_wise_inventory_info')->where('item_id',$product)->where('inventory_particular_id',0)->where('addedOn>=',$start_date." 00:00:00")->where('addedOn<=',$start_date." 23:59:59");
		if($company<>'' && $company<>'ALL')
		{
			$this->db->where('company_id',$company);
		}

		$qyert=$this->db->get();

		if($qyert->num_rows()>0)
		{
				foreach($qyert->result() as $row)
				{			
				$op[]=$row->qty;
				$avg_rate[]=$row->avg_rate;
				$fixed_avg_rate=$row->avg_rate;
				$taxable[]=$row->qty*$row->avg_rate;
				}
		}


		if(count($avg_rate)>0)
		{
			// if($start_date<>'2023-07-12')
			// {
			if(array_sum($op)>0)
			{
			$rate=array_sum($taxable)/array_sum($op);
			return array_sum($op).'~'.$rate;
			}else{
				return array_sum($op)."~0";
			}
		
			// }else{
			// 	return array_sum($op).'~'.$fixed_avg_rate;
			// }
		}else
		{
			return "0~0";
		}
		

	}

	function get_Open_stock_for_product($start_date,$end_date,$company,$product)
	{
		
		$base_date="2023-07-12";

		$getOpenstock=$this->get_stock_for_product_static($base_date,$end_date,$company,$product);
		$ops=explode('~',$getOpenstock);
		$opening_Q=$ops[0];
		$opening_R=$ops[1];

		$balance=0;
		$open_rate=0;

		if(strtotime($start_date)>strtotime($base_date))
		{
			$day_before=date('Y-m-d',strtotime('-1 Day',strtotime($start_date)));

			$inward=$this->get_inward_between_dates($base_date,$day_before,$company,$product);
			$in=explode('~',$inward);
			$inwardQ=$in[0];
			$inwardR=$in[1];
			$outward=$this->get_outward_between_dates($base_date,$day_before,$company,$product);
			$ou=explode('~',$outward);
			$outwardQ=$ou[0];
			$outwardR=$ou[1];
			$balance=$opening_Q+$inwardQ-$outwardQ;

			if($opening_Q>0 || $inwardQ>0)
            {
            	if(($opening_Q+$inwardQ)>0)
            	{
			$open_rate=(($opening_Q*$opening_R)+($inwardQ*$inwardR))/($opening_Q+$inwardQ);
			}else
			{
				$open_rate=0;
			}
			}else
			{
				$open_rate=0;
			}
			//$open_rate=$inwardR;


		}


		return $balance."~".$open_rate;


	}


	function get_inward_between_dates($start_date,$end_date,$company,$product)
	{
		$inward_qty=array();
		$inward_rate=array();
		$inward_value=array();
		
		$this->db->select('a.qty,rate')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('b.currentdate>=',date('Y-m-d',strtotime($start_date)))->where('b.currentdate<=',date('Y-m-d',strtotime($end_date)))->where('a.product',$product);
		if($company<>'ALL' && $company<>'')
		{
			$this->db->where('b.hpcl_billing_company',$company);
		}

		$quer=$this->db->get();

		if($quer->num_rows()>0)
		{
			foreach($quer->result() as $row)
			{
				$inward_qty[]=$row->qty;
				$inward_rate[]=$row->rate;
				$inward_value[]=$row->qty*$row->rate;
			}

		}


		
		if(array_sum($inward_qty)>0)
		{

			$inavg=array_sum($inward_value)/array_sum($inward_qty);


			return array_sum($inward_qty)."~".round($inavg,2);
		}else
		{
			return '0~0';
		}

	}


	function get_inward_between_dates_NEW($start_date,$end_date,$company,$product)
	{
		$inward_qty=array();
		$inward_rate=array();
		$inward_value=array();
		
		$this->db->select('a.qty,rate')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('b.currentdate>=',date('Y-m-d',strtotime($start_date)))->where('b.currentdate<=',date('Y-m-d',strtotime($end_date)))->where('a.product',$product);
		if($company<>'ALL' && $company<>'')
		{
			$this->db->where('b.hpcl_billing_company',$company);
		}

		$quer=$this->db->get();

		if($quer->num_rows()>0)
		{
			foreach($quer->result() as $row)
			{
				$inward_qty[]=$row->qty;
				$inward_rate[]=$row->rate;
				$inward_value[]=$row->qty*$row->rate;
			}

		}


		
		if(array_sum($inward_qty)>0)
		{

			$inavg=array_sum($inward_value)/array_sum($inward_qty);

			
			return array_sum($inward_qty)."~".round($inavg,2);
		}else
		{
			return '0~0';
		}

	}



	function get_outward_between_dates($start_date,$end_date,$company,$product)
	{
		$inward_qty=array();
		$inward_rate=array();
		
		$quer=$this->db->select('a.product_id,a.qty,a.agreed_price')->from('customer_quotation_detail a')->join('customer_quotation b','a.quotation_id=b.id')->join('order_punch c','c.quotation_id=b.id')->where('c.send_to_tally',1)->where('c.send_to_tally_On>=',date('Y-m-d',strtotime($start_date)))->where('c.send_to_tally_On<=',date('Y-m-d',strtotime($end_date)))->where('a.product_id',$product)->where('c.cancelled',0);
		if($company<>'ALL' && $company<>'')
		{
			$this->db->where('c.hpcl_billing_company',$company);
		}

		$quer=$this->db->get();


		if($quer->num_rows()>0)
		{
			foreach($quer->result() as $row)
			{
				$inward_qty[]=$row->qty;
				$inward_rate[]=$row->agreed_price;
			}

		}


		if(count($inward_qty)>0)
		{

			return array_sum($inward_qty)."~".array_sum($inward_rate)/count($inward_rate);
		}else
		{
			return '0~0';
		}

	}

	function getRackLocationByID($comp) {
		$res = '';
		$query = $this->db->select('id, rack_location,companyname')
		 				  ->from('store_rack_location')
		 				  ->where('id',$comp)
						  ->get();

		if ($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}




	// FOR AUTO BULK CP

	
	function get_Open_stock_for_product_AUTO($start_date,$end_date,$company,$product)
	{
		
		$base_date="2023-07-12";

		$getOpenstock=$this->get_stock_for_product_static_AUTO($base_date,$end_date,$company,$product);
		$ops=explode('~',$getOpenstock);
	
		$opening_Q=$ops[0];
		$opening_R=$ops[1];

		$balance=0;
		$open_rate=0;

		if(strtotime($start_date)>strtotime($base_date))
		{
			$day_before=date('Y-m-d',strtotime('-1 Day',strtotime($start_date)));

			$inward=$this->get_inward_between_dates($base_date,$day_before,$company,$product);
			$in=explode('~',$inward);
			$inwardQ=$in[0];
			$inwardR=$in[1];
			$outward=$this->get_outward_between_dates($base_date,$day_before,$company,$product);
			$ou=explode('~',$outward);
			$outwardQ=$ou[0];
			$outwardR=$ou[1];
			$balance=$opening_Q+$inwardQ-$outwardQ;

			if($opening_Q>0 || $inwardQ>0)
            {
            	if(($opening_Q+$inwardQ)>0)
            	{
			$open_rate=(($opening_Q*$opening_R)+($inwardQ*$inwardR))/($opening_Q+$inwardQ);
			}else
			{
				$open_rate=0;
			}
			}else
			{
				$open_rate=0;
			}
			//$open_rate=$inwardR;


		}


		return $balance."~".$open_rate;


	}


	function get_stock_for_product_static_AUTO($start_date,$end_date,$company,$product)
	{
		$op=array();
		$op[]=0;
		$avg_rate=array();
		$avg_rate[]=0;
		$fixed_avg_rate=0;
		$taxable=array();
		$taxable[]=0;

		$this->db->select('qty,avg_rate')->from('company_wise_inventory_info')->where('item_id',$product)->where('inventory_particular_id',0)->where('addedOn>=',$start_date." 00:00:00")->where('addedOn<=',$start_date." 23:59:59");
		if($company<>'' && $company<>'ALL')
		{
			$this->db->where('company_id',$company);
		}

		$qyert=$this->db->get();

		if($qyert->num_rows()>0)
		{
				foreach($qyert->result() as $row)
				{			
				$op[]=$row->qty;
				$avg_rate[]=$row->avg_rate;
				$fixed_avg_rate=$row->avg_rate;
				$taxable[]=$row->qty*$row->avg_rate;
				}
		}

		if(count($avg_rate)>0)
		{
			// if($start_date<>'2023-07-12')
			// {
			
				$rate=array_sum($taxable)/array_sum($op);
				return array_sum($op).'~'.$rate;
			// }else{
			// 	echo "hi"; exit;
			// 	return array_sum($op).'~'.$fixed_avg_rate;
			// }
		}else
		{
			return "0~0";
		}
		

	}


	function checkforcompany($id,$company)
	{
		$art=$this->db->select('id')->from('company_products')->where('product_id',$id)->where('company_id',$company)->get();

		return $art->num_rows();

	}





}
