<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lead_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->model('Dashboard_model');
		
	}
	
	function add_lead_funnel($data) {
	    $this->db->insert('lead_funnel', $data);
	    return $this->db->insert_id();
	}
	
		function add_lead_funnel_fields($data) {
	    $this->db->insert('lead_funnel_fields', $data);
	    return $this->db->affected_rows();
	}

	function teamleadersearch($userid)
	{
		$res=$this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$userid)->get();
		if($res->num_rows() >0)
		{
			$userstatus='1';
		}else
		{
			$userstatus='0';
		}

		return $userstatus;
	}
	
	public function getteamdetails($userid)
	{
		$emp=array();
				$team=$this->db->select('employee_id')->from('prestogroup_teams a')->join('presto_team_members b','a.team_id=b.team_id')->where('a.team_leader',$userid)->get();
				if($team->num_rows() >0){
				foreach($team->result() as $teamrow)
				{
				$emp[]=$teamrow->employee_id;	
				}
			}
				$emp[]=$userid;

				return $emp;
	}
	function checkforcontactacess($user_id)
	{
		$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','9')->where('submoduleid','148')->where('submodule_access','1')->get();
		return $qry->num_rows();
	}

	public function searchProducts($keyword) {

$mode=$this->Dashboard_model->getsettings();
if(count($mode)>0)
{
$mode=$mode['mode'];

}else{
$mode=1;
}

if($_SESSION['logged_in']['role']==1)
{
		$sql = $this->db->select('id,customer_name,contact_no,unique_id,company_name')

				 		->from('leads')
				 		->where('company_id',$_SESSION['logged_in']['business_location'])
				 		->like('customer_name', $keyword,'both')
				 		->or_like('contact_no', $keyword,'both')
				 		->or_like('unique_id', $keyword,'both')
				 		->or_like('company_name',$keyword,'both')
						->group_by('id')

				 		->get();

}else
{
			$userstatus=$this->teamleadersearch($_SESSION['logged_in']['user_id']);
			//echo $userstatus; exit;
			if($userstatus==1)
			{



				$emp=$this->getteamdetails($_SESSION['logged_in']['user_id']);

				$team_member="'" . implode ( "', '", $emp ) . "'";


				$sql = $this->db->select('a.id,a.customer_name,a.contact_no,a.unique_id,a.company_name')

				->from('leads a')
				->join('lead_assigned_to_team_member b','a.id=b.lead_id')
				->where_in('b.member_id',$team_member,false)
				->where('a.company_id',$_SESSION['logged_in']['business_location'])
				->like('a.customer_name', $keyword)
				->or_like('a.contact_no', $keyword)
				->or_like('a.unique_id', $keyword)
				->or_like('a.company_name', $keyword)
				->where('status','1')
				->group_by('a.id')
				->get();






			}else
			{

				if($mode==1)
				{
					$sql = $this->db->select('a.id,a.customer_name,a.contact_no,a.unique_id,a.company_name')
					->from('leads a')
					->join('lead_assigned_to_team_member b','a.id=b.lead_id')
					->where('b.member_id',$_SESSION['logged_in']['user_id'])
					->like('a.customer_name', $keyword)
					->or_like('a.contact_no', $keyword)
					->or_like('a.unique_id', $keyword)
					->or_like('a.company_name', $keyword)
					->where('status','1')
					->group_by('a.id')
					->get();
				}else
				{
					$sql = $this->db->select('a.id,a.customer_name,a.contact_no,a.unique_id,a.company_name')
					->from('leads a')
					->join('assigned_users_for_lead b','a.id=b.lead_id')
					->where('b.user_id',$_SESSION['logged_in']['user_id'])
					->like('a.customer_name', $keyword)
					->or_like('a.contact_no', $keyword)
					->or_like('a.unique_id', $keyword)
					->or_like('a.company_name', $keyword)
					->where('status','1')
					->group_by('a.id')
					->get();
				} 

			}

}

				


				return $sql->result();

}






	function getinstrumentname($ins)

        {

            $name='';

            $req=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$ins)->get();

            if($req->num_rows()>0)

            {

                foreach($req->result() as $row);

                $name=$row->instruments_name;

 

            }

 

            return $name;

        }

 

        function getMonthsInRange($startDate, $endDate) {

        $months = array();

        while (strtotime($startDate) <= strtotime($endDate)) {

        $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );

        $startDate = date('01 M Y', strtotime($startDate.

        '+ 1 month')); // Set date to 1 so that new month is returned as the month changes.

        }

 

        return $months;

        }

        function getleadmanagerformulti($leadid,$zoneid)
        {
			$as=1;
			$html='';
			$lname='';
			$fname='';
			$assignement='';

			$a=$this->db->select('c.user_role,c.user_role_id')->from('lead_stage a')->join('user_role c','a.user_role=c.user_role_id')->where('a.company_id',$_SESSION['logged_in']['business_location'])->group_by('a.user_role')->get();
			if($a->num_rows()>0)
			{
				$html.="<table border='1' style='width:300px'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sr No</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Role</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>User Name</th><tbody><tr>";

				foreach($a->result() as $roww)
				{
					$urole=$roww->user_role;
					$qe=$this->db->select('a.user_id,b.first_name,b.last_name')->from('assigned_users_for_lead a')->join('system_users b','a.user_id=b.user_id','left')->where('a.lead_id',$leadid)->where('business_location',$_SESSION['logged_in']['business_location'])->where('a.role_id',$roww->user_role_id)->get();
					if($qe->num_rows()>0)
					{
						foreach($qe->result() as $qes);

						$name=$qes->first_name." ".$qes->last_name;
					}else
					{
						$name="";
					}

					/** TABLE STARTS HERE **/

				$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>".$as."</td>
				<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$urole."</td>
				<td style='padding:2px 2px 2px 2px; text-align:center;width:40%;'>".$name."</td></tr>";	






				$as++; }

			}else
			{

			$html.="<tr><td colspan='2' style='padding:2px 2px 2px 2px; text-align:center;width:20%;'>No Data Available</td>
			</tr>";	


			}
				

				return $html;
				

        }

         function get_finacial_year_range() {
    $year = date('Y');
    $month = date('m');
    if($month<4){
        $year = $year-1;
    }
    $start_date = date('Y-m-d',strtotime(($year).'-04-01'));
    $end_date = date('Y-m-d',strtotime(($year+1).'-03-31'));
    $response = array('start_date' => $start_date, 'end_date' => $end_date);
    return $response;
}


function getpaymentdue($custid)
{

}

function getLeadDetailsNew($lead_id) {
		 $res = '';
		 $query = $this->db->select('a.create_date,a.unique_id,b.id as customer_id,b.customer_name,a.mobile_no,a.email_id,a. patient_type_id, a.website,a.contact_no,a.title,a.postal_address, a.city,b.company_name,machine_type,a.country,a.lead_source_id,a.patient_type_id,a.machine_type,a.added_by,a.remarks,a.customise_remarks,b.email,b.contact_no as customer_contact,a.merchantexport,a.probability,a.customer_type')
						   ->from('leads a')
						   ->join('customer_detail b','a.company_name=b.id')
						   ->where('a.id', $lead_id)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} 

		return $res;
	}


	function getProductDetails($lead_id)
{
	$data=array();
	$sql = $this->db->select('a.id, a.qty, c.instruments_name,c.id as product_id')
						->from('lead_products a')
						->join('leads b', 'b.id=a.lead_id')
						->join('presto_instruments c', 'c.id=a.product_id')
						->where('a.lead_id', $lead_id)
						->get();

			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row);
				$data[]=$row->instruments_name;
				$data[]=$row->qty;
				$data[]=$row->id;
				$data[]=$row->product_id;

			}

			return $data;
}

}