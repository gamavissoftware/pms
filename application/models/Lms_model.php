<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lms_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
	}

	function checkifuserhasgivenanyanswer($user_id,$department_id)
	{
		$rws=$this->db->select('id')->from('lms_user_result')->where('user_id',$user_id)->where('department_id',$department_id)->where('fresult!=','FAIL')->get();
		return $rws->num_rows();
		
	}
	
	function getdaywhoseanswerisgiven($user_id,$department_id)
	{
		$given=array();
		$rws2=$this->db->select('test_day')->from('lms_result')->where('userid',$user_id)->where('department_id',$department_id)->where('status',1)->order_by('test_day','ASC')->get();
			if($rws2->num_rows() >0){
				foreach($rws2->result() as $study)
				{
					$given[]=$study->test_day;
				} 
				
				$this->gettotalnoofdays($given,$user_id,$department_id);
			}else
			{
				
				return 0;
			}

		
				
	
		
	}
	
	function gettotalnoofdays($excludedays,$user_id,$department_id)
	{
		
		$result = "'" . implode ( "', '", $excludedays ) . "'";
		
		$Reste=$this->db->select('s_day')->from('lms_study')->where('department_id',$department_id)->where_not_in('s_day',$result,false)->order_by('s_day','ASC')->limit(1)->get();
		if($Reste->num_rows()>0)
		{
			foreach($Reste->result() as $Reste1);
			
			return $Reste1->s_day;
			
		}else{
			
			return 0;
		}
		
		
	}
	
	

}
	
	