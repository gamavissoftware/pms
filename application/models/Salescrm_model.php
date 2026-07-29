<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Salescrm_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->model('Dashboard_model','dashboardmodel');
	}

	function getLeadStatus($lead_id) {
		 $query = $this->db->select('b.added_on,b.added_by,a.lead_id as lead_id, a.lead_name as lead_name, b.remarks')
						   ->from('lead_stage a')
						   ->join('progress_remarks b', 'a.lead_id=b.lead_status')
						   ->where('b.lead_id', $lead_id)
						   ->order_by('b.id', 'DESC')
						   ->limit(1)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} else {
				$res = '';

			}

			return $res;

	}

	function checkMeetingStatus($lead_id) {
		$data=array();
		 $query = $this->db->select('lead_status')
						   ->from('progress_remarks')						   
						   ->where('lead_id', $lead_id)
						   ->order_by('id', 'DESC')
						   ->limit(3)
						   ->get();

			if($query->num_rows() > 0) {
				foreach ($query->result() as $querys) {
					$data[]=$querys->lead_status;
				}
			} 

			return $data;

	}
	
		function getLeadType($lead_stage_id) {
		$res = '';
		$query = $this->db->select('lead_type')
						   ->from('lead_types')						   
						   ->where('lead_stage_id', $lead_stage_id)
						   ->get();

			if($query->num_rows() > 0) {
				
				$res = $query->result();
				
			} 

			return $res;
	}

	function getLeadTypeName($id) {
		$query = $this->db->select('lead_type')
						   ->from('lead_types')						   
						   ->where('id', $id)
						   ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			} 

			
	}
	
	function checkLeadAssignment($lead_id) {
		$query = $this->db->select('lead_id')
						   ->from('lead_assigned_to_team_member')						   
						   ->where('lead_id', $lead_id)
						   ->get();

			if($query->num_rows() > 0) {
				return 1;
			} else {
				return 0;
			}
	}

	function getLeadDetails($lead_id) {
		 $res = '';
		 $query = $this->db->select('customer_name, company_name, mobile_no, email_id, patient_type_id, website,contact_no, title, postal_address, city')
						   ->from('leads')
						   ->where('id', $lead_id)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} 

		return $res;
	}

	function getClientType() {
		 $query = $this->db->select('patient_id, patient_type')
						   ->from('patient_type')
						   ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			} 
	}

	function getPISDetails($pis_id) {
		 $query = $this->db->select('a.lead_id, a.project_name, a.website, a.website_name, a.delivery_address,a.delivery_city,a.delivery_state, a.billing_address,a.billing_state,a.billing_city, b.company_name, b.customer_name, b.mobile_no, b.email_id, b.website, b.patient_type_id, c.id, c.design_req_chk, c.design_req, c.input_type, c.ppt_status, c.cad_data, c.format_type, c.cad_drawing, c.prototype, c.mockup, c.batch_prod_chk, c.batch_prod, c.mould_facility_chk, c.mould_facility, c.shipment_type, c.delivery_type, c.customer_reason, customer_request_chk, toolmaker_type')
						   ->from('bom_pi_sales_client_info a')
						   ->join('leads b', 'a.lead_id=b.id')
						   ->join('bom_pi_sales_project_info c', 'c.client_info_id=a.id')
						   ->where('a.id', $pis_id)
						   ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			} 
	}
	

	function getCustReq($uri) {
		$result = array();
		 $query = $this->db->select('id')
						   ->from('bom_pi_sales_project_info')
						   ->where('client_info_id', $uri)
						   ->get();

			if($query->num_rows() > 0) {
				foreach ($query->result() as $row);
					   $sql = $this->db->select('id, customer_request')
									   ->from('bom_pi_sales_cust_req')
									   ->where('project_id', $row->id)
									   ->get();

						if ($sql->num_rows() > 0) {
							$result = $sql->result();
						}

			}

			return $result;
	}

	function getProvinceCity($uri) {
		$query = $this->db->select('lead_id')
						   ->from('bom_pi_sales_client_info')
						   ->where('id', $uri)
						   ->get();

			if($query->num_rows() > 0) {
				foreach ($query->result() as $row);
					   $sql = $this->db->select('a.id as id, b.id as province_id, b.name as province, c.id as city_id, c.name as city')
									   ->from('bom_cities a')
									   ->join('china_provinces b', 'a.province_id=b.id')
									   ->join('china_city c', 'a.city_id=c.id')
									   ->where('a.lead_id', $row->lead_id)
									   ->get();

						if ($sql->num_rows() > 0) {
							return $sql->result();
						}

			}
	}

	
	function getClientTypebyid($id) {
		 $query = $this->db->select('patient_type')
						   ->from('patient_type')
			 			   ->where('patient_id',$id)
						   ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $que);
				
				return $que->patient_type;
			} else
			{
				return '';
			}
	}


	function getPreviousSchedule($lead_id) {
		$query = $this->db->select('a.meeting_date, a.meeting_time, b.reminder_date, b.reminder_time')
						   ->from('lead_meeting_schedule a')
						   ->join('lead_internal_meeting_reminder b', 'b.lead_id=a.lead_id')
			 			   ->where('a.lead_id',$lead_id)
			 			   ->order_by('a.id', 'DESC')
			 			   ->limit(1)
						   ->get();

		if($query->num_rows() > 0) {
			return $query->result();
		}
	}

	function getMeetingParticipants($lead_id) {
		$query = $this->db->select('b.user_id, b.first_name, b.last_name')
						   ->from('lead_meeting_schedule_details a')
						   ->join('system_users b', 'b.user_id=a.user_id')
			 			   ->where('a.lead_id',$lead_id)
			 			   // ->order_by('a.id', 'DESC')
			 			   // ->limit(1)
						   ->get();

		if($query->num_rows() > 0) {
			return $query->result();
		}
	}

	function getTeams() {
		$query = $this->db->select('team_id, team_name')
						   ->from('prestogroup_teams')
			 			   ->where('status', 1)
						   ->get();

		if($query->num_rows() > 0) {
			return $query->result();
		}
	}



function getusername($user_id)
	{
		$resteye=$this->db->select('title, first_name,last_name')->from('system_users')->where('user_id',$user_id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);

			$user=ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));
		}else
		{
			$user='';
		}

		return $user;

	}


	function getcountry($id)
	{
		$resteye=$this->db->select('country_name')->from('countries')->where('country_id',$id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);

			$user=$row->country_name;
		}else
		{
			$user='';
		}

		return $user;

	}


function getstate($id)
	{
		$resteye=$this->db->select('state_name')->from('states')->where('state_id',$id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);

			$user=$row->state_name;
		}else
		{
			$user='';
		}

		return $user;

	}


	function getleadassigned($leadid)
	{
		$rwow=$this->db->select('member_id')->from('lead_assigned_to_team_member')->where('lead_id',$leadid)->get();
		if($rwow->num_rows()>0)
		{
			foreach($rwow->result() as $row);

			return $row->member_id;

		}else
		{
			return "";
		}
	}


function getLeadStagename($lead_id) {
		 $query = $this->db->select('a.lead_name')
						   ->from('lead_type a')
						
						   ->where('a.lead_id', $lead_id)
						
						   ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row);
				$res = $row->lead_name;
			} else {
				$res = '';

			}

			return $res; 

	}


	function getLeadStatusbyidandremarks($lead_id) {
		 $query = $this->db->select('b.lead_status,b.remarks')
						   ->from('lead_type a')
						   ->join('progress_remarks b', 'a.lead_id=b.lead_status')
						   ->where('b.lead_id', $lead_id)
						   ->order_by('b.id', 'DESC')
						   ->limit(1)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} else {
				$res = '';

			}

			return $res;

	}


	function getleadsourcebyid($id)
	{

		 $query = $this->db->select('lead_source')
						   ->from('lead_source')
						   ->where('source_id', $id)

						   ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $lead);
				return $lead->lead_source;
			} else {
				return '';

			}
	}

	function getSelectedTeam($lead_id) {
		$res = '';
		$query = $this->db->select('a.team_id')
						  ->from('prestogroup_teams a')
						  ->join('lead_assigned_to_team b', 'b.team_id=a.team_id')
						  ->where('b.lead_id',$lead_id)
						  ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			} 

			return $res;
								
	}

	function getCustName($lead_id) {
		$res = '';
		$query = $this->db->select('customer_name')
						  ->from('leads')
						  ->where('id', $lead_id)
						  ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			} 

			return $res;
	}

	function getTeamMembers() {
		$res = '';
		$sql = $this->db->select('a.user_id, a.first_name, a.last_name')
						->from('system_users a')
						->join('lead_assigned_to_team_member b', 'b.member_id=a.user_id')
						->group_by('b.member_id')
						->get();

		if ($sql->num_rows() > 0) {
				$res = $sql->result();
			} 

			return $res;
	}

	function checkTeam($lead_id) {
		$res = '';
		$query = $this->db->select('a.team_id')
						  ->from('lead_assigned_to_team a')
						  ->join('leads b', 'b.id=a.lead_id')
						  ->where('b.id', $lead_id)
						  ->get();

			if ($query->num_rows() > 0) {
				 foreach($query->result() as $row);
				 $res = $row->team_id;
			} 

			return $res;
	}


	function getuseremail($user_id)
	{
		$resteye=$this->db->select('email')->from('system_users')->where('user_id',$user_id)->get();
		if($resteye->num_rows()>0)
		{
			foreach($resteye->result() as $row);

			$user=$row->email;
		}else
		{
			$user='';
		}

		return $user;

	}

	function getQuotations($lead_id) {
		$res = '';
		$query = $this->db->select('a.id, a.lead_id, a.option_id, a.supplier_id, a.supplier_price, a.final_price, b.company_legal_representative_name')
						  ->from('client_quoted_price a')
						  ->join('supplier_legal_representative b', 'b.supplier_id=a.supplier_id')
						  ->where('a.lead_id', $lead_id)
						  ->get();

			if ($query->num_rows() > 0) {
				  $res = $query->result();
					
			} 

			return $res;
	}

	function getCustomerDetails($lead_id) {
		  $res = '';
		  $query = $this->db->select('a.title, a.customer_name, a.company_name, a.unique_id, a.create_date, a.email_id, a.country_code, a.contact_no, a.city, a.email, a.contact_person, a.postal_address, a.state, b.country_id, b.country_name, c.state_name, d.patient_type')
		                    ->from('leads a')
		                    ->join('countries b','a.country=b.country_id','left')
		                    ->join('states c','a.state=c.state_id','left')
		                    ->join('patient_type d', 'a.patient_type_id=d.patient_id','left')
		                    ->where('a.id',$lead_id)
		                    ->get();

		  if ($query->num_rows() > 0) {
				  $res = $query->result();
					
			} 

			return $res;
	}

	function getProposalStatus($lead_id) {
		 $query = $this->db->select('b.meeting_status, b.added_on, c.first_name, c.last_name, a.lead_name')
						   ->from('lead_type a')
						   ->join('proposal_meeting_details b', 'a.lead_id=b.meeting_status', 'left')
						   ->join('system_users c', 'c.user_id=b.added_by', 'left')
						   ->where('b.lead_id', $lead_id)
						   ->order_by('b.id', 'DESC')
						   ->limit(1)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} else {
				$res = '';

			}

			return $res;

	}

	function getPreviousDates($lead_id) {
		$res = '';
		$query = $this->db->select('a.meeting_date, a.meeting_time, a.reminder_date, a.reminder_time')
						   ->from('proposal_meeting_details a')
						   // ->join('client_quoted_price b', 'a.quotation=b.id','left')
			 			   ->where('a.lead_id',$lead_id)
			 			   // ->where('a.meeting_status','15')
			 			   ->order_by('a.id', 'DESC')
			 			   ->limit(1)
						   ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			}

		return $res;
	}

	function getParticipants($lead_id) {
		$query = $this->db->select('b.user_id, b.first_name, b.last_name')
						   ->from('proposal_meeting_participants a')
						   ->join('system_users b', 'b.user_id=a.user_id')
			 			   ->where('a.lead_id',$lead_id)
						   ->get();

		if($query->num_rows() > 0) {
			return $query->result();
		}
	}

	function getNegotiationStatus($lead_id) {
		 $query = $this->db->select('b.meeting_status, a.lead_name')
						   ->from('lead_type a')
						   ->join('negotiation_meeting_details b', 'a.lead_id=b.meeting_status')
						   ->where('b.lead_id', $lead_id)
						   ->order_by('b.id', 'DESC')
						   ->limit(1)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} else {
				$res = '';

			}

			return $res;

	}

	function getNegotiationDates($lead_id) {
		$res = '';
		$query = $this->db->select('meeting_date, meeting_time, reminder_date, reminder_time')
						   ->from('negotiation_meeting_details')
			 			   ->where('lead_id',$lead_id)
			 			   ->order_by('id', 'DESC')
			 			   ->limit(1)
						   ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			}

		return $res;
	}

	function getNegParticipants($lead_id) {
		$query = $this->db->select('b.user_id, b.first_name, b.last_name')
						   ->from('negotiation_meeting_participants a')
						   ->join('system_users b', 'b.user_id=a.user_id')
			 			   ->where('a.lead_id',$lead_id)
						   ->get();

		if($query->num_rows() > 0) {
			return $query->result();
		}
	}

	function getNegotiationData($lead_id) {
	$res = '';
	$query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.lead_name, d.lead_name as order_status')
                      ->from('negotiation_meeting_details a')
                      ->join('system_users b','a.added_by=b.user_id','left')
                      ->join('lead_type c','a.meeting_status=c.lead_id','left')
                      ->join('lead_type d','a.order_status=d.lead_id','left')
                      ->join('client_quoted_price e','a.order_status=e.lead_id','left')
                      ->where('a.lead_id',$lead_id)
                      ->order_by('a.id','desc')
                      ->get();

          if($query->num_rows() > 0) {
          	$res = $query->result();
		}
			return $res;

    }

    function getUsernames($lead_id) {
    	$res = '';
		$query = $this->db->select('b.first_name, b.last_name')
	                      ->from('negotiation_meeting_participants a')
	                      ->join('system_users b','a.user_id=b.user_id','left')
	                      ->where('a.lead_id',$lead_id)
	                      ->order_by('a.id','desc')
	                      ->get();

	          if($query->num_rows() > 0) {
	          	$res = $query->result();
			}
				return $res;
    }

    function getProposalData($lead_id) {
	$res = '';
	$query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.lead_name, d.lead_name as order_status')
                      ->from('proposal_meeting_details a')
                      ->join('system_users b','a.added_by=b.user_id','left')
                      ->join('lead_type c','a.meeting_status=c.lead_id','left')
                      ->join('lead_type d','a.order_status=d.lead_id','left')
                      ->join('client_quoted_price e','a.order_status=e.lead_id','left')
                      ->where('a.lead_id',$lead_id)
                      ->order_by('a.id','desc')
                      ->get();

          if($query->num_rows() > 0) {
          	$res = $query->result();
		}
			return $res;

    }

      function getProUsernames($lead_id) {
    	$res = '';
		$query = $this->db->select('b.first_name, b.last_name')
	                      ->from('proposal_meeting_participants a')
	                      ->join('system_users b','a.user_id=b.user_id','left')
	                      ->where('a.lead_id',$lead_id)
	                      ->order_by('a.id','desc')
	                      ->get();

	          if($query->num_rows() > 0) {
	          	$res = $query->result();
			}
				return $res;
    }

    		function getSelectedQuotation($quotation) {
				$ht='';
				$query=$this->db->select('lead_id, option_id, supplier_id, supplier_price, final_price')
							    ->from('client_quoted_price')
							    ->where('id',$quotation)
							    ->get();
				if($query->num_rows()>0) {
						$ht.="<table class='table table-bordered'>
						<thead>
						<tr>
						<th style='padding:0px;'>Supplier Price</th>
						<th style='padding:0px;'>Final Price</th>
						<th style='padding:0px;'>Quotations</th>
						</tr>
						</thead>
						<tbody>";
					foreach($query->result() as $rows) {

						$ht.="<tr>
						<td style='padding:0px;'>".$rows->supplier_price."</td>
						<td style='padding:0px;'>".$rows->final_price."</td>
						<td style='padding:0px;'><a href='".page_url1."pdf/rfq/examples/quotation.php?lead_id=".$rows->lead_id."&option=".$rows->option_id."&suplier=".$rows->supplier_id."' target='_blank'>View Quotation</a></td>
						</tr>";
					}

					$ht.="</tbody> </table>";

				}else
				{
					$ht.="NOT SOURCED YET";
				}


		//echo $ht; exit;
				return $ht;

			}

		function getBop($order_punch_id) {
			$res = '';
			$query = $this->db->select('b.itemname, b.qty')
						      ->from('order_won a')
						      ->join('bom_bopparts_required b', 'b.lead_id=a.lead_id')
						      ->where('a.id',$order_punch_id)
						      ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			}

			return $res;
		}

		function getQuotationDetails($lead_id) {
			$res = '';
			$query = $this->db->select('a.quotation, b.supplier_id, b.final_price, c.company_name')
						      ->from('proposal_meeting_details a')
						      ->join('client_quoted_price b', 'b.id=a.quotation', 'left')
						      ->join('suppliers c', 'c.supplier_id=b.supplier_id', 'left')
						      ->where('a.lead_id',$lead_id)
						      ->order_by('a.id', 'desc')
						      ->limit(1)
						      ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			}

			return $res;
		}

		function getQuotationID($lead_id) {
		$res = '';
		$query = $this->db->select('a.quotation, b.supplier_id')
					      ->from('proposal_meeting_details a')
					      ->join('client_quoted_price b', 'b.id=a.quotation', 'left')
					      ->where('a.lead_id',$lead_id)
					      ->order_by('a.id', 'desc')
					      ->limit(1)
					      ->get();

			if ($query->num_rows() > 0) {
				$res = $query->result();
			}

		return $res;
	}

	function getClientPrice($order_won_id) {
		$res = '';
		$query = $this->db->select('b.supplier_price')
					      ->from('order_won a')
					      ->join('client_quoted_price b', 'b.id=a.final_price')
					      ->where('a.id',$order_won_id)
					      ->get();

			if ($query->num_rows() > 0) {
				 foreach($query->result() as $row);
				 $res = $row->supplier_price;
			}

			return $res;
	}

	function getStatusName($status) {
		$res = '';
		$query = $this->db->select('lead_name')
					      ->from('lead_type')
					      ->where('lead_id',$status)
					      ->get();

		if ($query->num_rows() > 0) {
				 foreach($query->result() as $row);
				 $res = $row->lead_name;
			}

			return $res;
	}

	   function getPisStage($lead_id) {
	   	$stage = 'QUOTATION PIS';
	   	$sql = $this->db->select('a.id')
	   					->from('client_quoted_price a')
	   					->join('order_won b', 'b.lead_id=a.lead_id')
	   					->where('a.lead_id', $lead_id)
	   					->where('b.approved_by_technical', 0)
	   					->get();

	   		if ($sql->num_rows() > 0) {
	   			$stage = 'QUOTATION PIS';
	   		} else {
	   			  $query = $this->db->select('id')
				   					->from('order_won')
				   					->where('lead_id', $lead_id)
				   					->where('approved_by_technical', 1)
				   					->where('approved_by_sales', 0)
				   					->get();

			   	if ($query->num_rows() > 0) {
			   		$stage = 'SALES PIS';
			   	} else {
			   		 $query1 = $this->db->select('id')
					   					->from('order_won')
					   					->where('lead_id', $lead_id)
					   					->where('approved_by_sales', 1)
					   					->get();

					  if ($query1->num_rows() > 0) {
			   				$stage = 'TOOLING PIS';
			   			}
			   	}
	   		}

	   		return $stage;
	   }

	   function getHjigServices() {
	   	$res = '';
	   	$sql = $this->db->select('id, service_name')
	   					->from('services')
	   					->where('status', 1)
	   					->get();

	   	if ($sql->num_rows() > 0) {
	   		$res = $sql->result();
	   	}

	   	return $res;
	   } 

	   function getSelectedHjigServices($lead_id) {
		   	$res = '';
		   	$sql = $this->db->select('a.id as ser_id, b.id, b.service_name')
		   					->from('bom_pi_services_info a')
		   					->join('services b', 'b.id=a.hjig_services')
		   					->where('a.lead_id', $lead_id)
		   					->get();

		   	if ($sql->num_rows() > 0) {
		   		$res = $sql->result();
		   	}

		   	return $res;
	   }

	   function getServiceAmt($lead_id) {
	   		
		   	$sql = $this->db->select('service_amt')
		   					->from('bom_pi_services_info')
		   					->where('lead_id', $lead_id)
		   					->get();

		   	return $sql->num_rows();
	   }

	   function getProducts($lead_id) {
	   	// echo $lead_id;exit;
	   	$instruments = array();
		$sql = $this->db->select('a.id as leadproduct,a.competitor_product, a.product_id, a.qty, a.price, a.percent_amt, a.net_price, a.flag, b.id, b.mvalue, b.instruments_name, b.pack_size as instrument_pack_size,a.packsize')
						->from('lead_products a')
	   					->join('presto_instruments b', 'b.id=a.product_id', 'left')
	   					->where('a.lead_id', $lead_id)
	   					->get();

	   		// if ($sql->num_rows() > 0) {
		   	// 	foreach($sql->result() as $rows) {
		   	// 		$instruments[] = $rows->instruments_name;
		   	// 	}
		   	// }

		   	// return implode(',', $instruments);
		   	   return $sql->result();
	   }

	   function getSettingsDetail() {
	   	$quotation_name = '';
	   	$pi_name = '';
	   	$sql = $this->db->select('quotation_name, pi_name')
		   				->from('setting_master')
		   				->get();

		 if ($sql->num_rows() > 0) {
		 	foreach($sql->result() as $row);
		 		$quotation_name = $row->quotation_name;
		 		$pi_name = $row->pi_name;
		 }

		 return $quotation_name.'|'.$pi_name;
	   }
	   
	   
	   function getquoteddata($id)
	   {
	       $data=array();
	       $lp=$this->db->select('*')->from('lead_products')->where('id',$id)->get();
	       if($lp->num_rows()>0)
	       {
	           foreach($lp->result() as $row);
	           $data['qty']=$row->qty;
	           $data['price']=$row->price;
	           $data['discount_type']=$row->discount_type;
	           $data['percent_amt']=$row->percent_amt;
	           
	       }
	       
	       return $data;
	   }
	   
	   
	   function getquotedpackdata($id)
	   {
	       $data=array();
	       $lp=$this->db->select('*')->from('lead_product_details')->where('lead_id',$id)->get();
	       if($lp->num_rows()>0)
	       {
	           foreach($lp->result() as $row);
	           $data['packing_type']=$row->packing_type;
	           $data['packing_price']=$row->packing_price;
	           $data['freight_actual']=$row->freight_actual;
	           $data['freight_charges']=$row->freight_charges;
	           
	       }
	       
	       return $data;
	   }
	   
	   function getmasterterms()
	   {
	       $row=$this->db->select('terms_conditions')->from('terms_and_conditions_master')->get();
	       if($row->num_rows()>0)
	       {
	           foreach($row->result() as $rows);
	           
	           return $rows->terms_conditions;
	       }else
	       {
	           return '';
	       }
	   }
	   
	   function getlateststatus($leadid)
	   {
	       $r=$this->db->select('b.lead_name')->from('progress_remarks a')->join('lead_stage b','a.lead_status=b.lead_id')->where('a.lead_id',$leadid)->order_by('a.id','DESC')->limit(1)->get();
	       if($r->num_rows()>0)
	       {
	           foreach($r->result() as $rr);
	           
	           return $rr->lead_name;
	       }else
	       {
	           return '';
	       }
	       
	       
	   }
	   
	   function checkifavailable($leadid,$status)
	   {
	      $r= $this->db->select('id')->from('progress_remarks')->where('lead_id',$leadid)->where('lead_status',$status)->get();
	      
	      return $r->num_rows();
	       
	   }
	   
	   
// 	   	function getsettings()
// {
// 	$setarray=array();
// 				$sql = $this->db->select('*')
// 				->from('setting_master')
// 				->get();

// 				if($sql->num_rows() > 0) {
// 				foreach ($sql->result() as $row);
// 				$setarray['port'] = $row->port;
// 				$setarray['password'] = $row->password;
// 				$setarray['email_smtp'] = $row->email;
// 				$setarray['email_outgoing']=$row->outgoing;
// 				$setarray['email_pass']=$row->pass;
// 				$setarray['email_ccmailid']=$row->cc_email;
// 				$setarray['whatsappuser']=$row->user_name;
// 				$setarray['whatsapppassword']=$row->password;
// 				$setarray['quotefile']=$row->quotation_name;
// 				$setarray['pifile']=$row->pi_name;
// 				} 

// 				return $setarray;


// }


	function getorder_details($leadid)
	{
	    $html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
        <th style="width:80px;">Price</th>
        <th style="width:80px;">Discount</th>
        <th style="width:80px;">Final Price</th>
        </tr>
        </thead><tbody>';
    
    $row=$this->db->select('b.instruments_name,a.product_id,a.qty,a.price,a.discount_type,a.percent_amt')->from('lead_products a')->join('presto_instruments b','a.product_id=b.id')->where('a.lead_id',$leadid)->get();
    $ar=array();
    $ar[]=0;
    if($row->num_rows()>0)
    {
        $i=1;
        foreach($row->result() as $rows)
        {
            $totalprice=$rows->price;
            if($rows->discount_type==0)
            {
             $discount=  ($rows->price*$rows->percent_amt)/100;
             $dtype=$rows->percent_amt." %";
                
            }else if($rows->discount_type==1)
            {
                $discount= $rows->percent_amt;
                $dtype=$rows->percent_amt;
                
            }else
            {
                $discount=0;
                $dtype="--";
            }
            
            
            $final=$totalprice-$discount;
      $html.='<tr>
        <td>'.$i.'</td>
        <td>'.$rows->instruments_name.'</td>
        <td>'.$rows->qty.'</td>
         <td>'.$rows->price.'</td>
          <td>'.$dtype.'</td>
          <td>'.$final.'</td>
      </tr>';
      $ar[]=$final;
       $i++;
       }
       
         $html.='<tr>
        <th colspan="5" style="text-align:right;background-color:#e8e8e8;font-weight:bold;">Total</td>
        <th style="text-align:center;background-color:#e8e8e8;font-weight:bold;">'.array_sum($ar).'</td>
     
      </tr>';
      
    }else
    {
           $html.='<tr>
        <td colspan="6">No Data Available</td>
      
      </tr>';
    }
    
     $html.='</tbody>
  </table>';
  
  return $html; 
	    
	    
	}
	
	
    function calculateFiscalYearForDate($month)
    {
    if($month > 4)
    {
    $y = date('Y');
    $pt = date('Y', strtotime('+1 year'));
    $fy = $y."-04-01".":".$pt."-03-31";
    }
    else
    {
    $y = date('Y', strtotime('-1 year'));
    $pt = date('Y');
    $fy = $y."-04-01".":".$pt."-03-31";
    }
    return $fy;
    }



    function getLeadStageRelation($lead_stage_id) {

    	$res = '';
    	$sql = $this->db->select('b.lead_id, b.lead_name')
    					->from('lead_stage_relation a')
    					->join('lead_stage b', 'b.lead_id=a.stage_relation')
    					->where('lead_stage_id', $lead_stage_id)
    					->get();

    	if ($sql->num_rows() > 0) {
    		$res = $sql->result();
    	}

    	return $res;
    }

	function getLeadStageDetails($lead_stage_id) {
	$res = '';
	
	$sql = $this->db->select('quotation_step, quotation_revised_step, pi_step, pi_revised_step, conversion_step,lead_name,reason')
					->from('lead_stage')
					->where('lead_id', $lead_stage_id)
					->get();

	if ($sql->num_rows() > 0) {
		 $res = $sql->result();
	}

		return $res;
	}

	function getCompareHandleLeadTable($user) {
		if($user<>'NA') {
            $users=base64_decode($user);
        }

        $this->db->select('b.user_id,b.first_name,b.last_name')
                 ->from('system_users b')->where('b.business_location',$_SESSION['logged_in']['business_location']);
        
         if($user<>'NA') {
              $this->db->where_in('b.user_id',$users,false);
          }

          	$query = $this->db->get();

          	$res = '';
          if($query->num_rows() >0) {
          		$res = $query->result();
           }

           return $res;
	}

	function getCompareHandleLeadData($user_id, $stdate, $enddate) {
		$this->db->select('a.lead_id')
      		     ->from('lead_assigned_to_team_member a')
      		     ->where('a.member_id',$user_id);

      	if($stdate <> '') {
      		$this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
      	}

      	 $q = $this->db->get();

      	 return $q->num_rows();

	}



	function getConvertedLeadsByMember($user) {
		if($user<>'NA') {
          $users=base64_decode($user);
          }
		$this->db->select('b.user_id,b.first_name,b.last_name')
				 ->from('system_users b')

				 ->where('b.business_location',$_SESSION['logged_in']['business_location']);
            if($user<>'NA') {
                  $this->db->where_in('b.user_id',$users,false);
              }
          
          $query = $this->db->get();

          $res = '';
          if($query->num_rows() >0) {
          		$res = $query->result();
           }

           return $res;
	}

	function getConvertedLeadsByMemberData($user_id, $stdate, $enddate, $conversion_lead_stage) {
		 $this->db->select('a.lead_id')
		 		  ->from('lead_assigned_to_team_member a')
		 		  ->join('progress_remarks b','a.lead_id=b.lead_id')
		 		  ->where('a.member_id',$user_id)
		 		  ->where('b.lead_status', $conversion_lead_stage);

          if($stdate<>'') {
          $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
          }
          
          $q=$this->db->get();

          return $q->num_rows();
	}


	function getDiscountByMember($user) {
		if($user<>'NA') {
        	$users=base64_decode($user);
        }

        $this->db->select('b.user_id,b.first_name,b.last_name')
        		 ->from('system_users b')
        		 ->where('b.business_location',$_SESSION['logged_in']['business_location']);

        if($user <> 'NA') {
            $this->db->where_in('b.user_id',$users,false);
        }
       
        $query = $this->db->get();

          $res = '';
	      if($query->num_rows() > 0) {
	      		$res = $query->result();
	       }

	       return $res;
	}

	function getDiscountByMemberData($user_id, $stdate, $enddate, $conversion_lead_stage) {
		$income=array();
		$income[]=0;

		if($stdate <> '' && $enddate <> '') {
	        $sdf="AND c.added_on BETWEEN '".$stdate. "' and '". $enddate."' GROUP BY id";
	     } else {
	        $sdf = "";
	     }
		$sql1 = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($conversion_lead_stage) AND c.member_id=$user_id $sdf");
		$totall = '';
		if($sql1->num_rows() > 0) {
            foreach($sql1->result() as $row1) {
            	$sql2 = $this->db->query("SELECT qty, price, discount_type, percent_amt FROM lead_products WHERE lead_id=$row1->id");
            	$total_discount = array();
	            $total_discount[] = 0;
	            
	            if($sql2->num_rows() >0) {
	            	foreach($sql2->result() as $row2) {
	            		 $total_price = $row2->price * $row2->qty;

	                        if ($row2->discount_type == 0) {
	                            $total_discount[] = $total_price - ($total_price * $row2->percent_amt)/100;
	                        } else if($row2->discount_type == 1) {
	                            $total_discount[] = $total_price - $row2->percent_amt;
	                        } else {
	                            $total_discount[] = 0;
	                        }
	            	}
	            }
	            $totall = array_sum($total_discount);
            }
        } else{
	            $totall = "0.00";
              }

           $total_amt = str_replace(",", "", $totall);
           return $total_amt;
	}

	function getCompareLeadByTeam($user, $stdate, $enddate) {
		$output = array();
		$res = '';
             if($user<>'NA') {
	            $users=base64_decode($user);
	         }
            
            $this->db->select('a.id,b.team_id,b.team_name')
            		 ->from('leads a')
            		 ->join('lead_assigned_to_team c','a.id=c.lead_id')
            		 ->join('prestogroup_teams b','c.lead_id=b.team_id');

             if($user<>'NA') {
                $this->db->where_in('b.team_id',$users,false);
            }

            if($stdate<>'') {
            	$this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }

            $query = $this->db->get();
            if($query->num_rows() >0) {          
            	$res = $query->result();
        }

        return $res;

	}

	function getCompareLeadByTeamData($team_id) {
		$q=$this->db->select('COUNT(a.team_id) as asd')
					->from('lead_assigned_to_team a')
					->where('a.team_id', $team_id)
					->get();

		return $q->result();
	}

	function getConvertedLeadByTeam($user, $stdate, $enddate) {
		$res = '';
		if($user<>'NA') {
       	 $users=base64_decode($user);
        }

        $this->db->select('a.id,b.team_id,b.team_name')
        		 ->from('leads a')
        		 ->join('lead_assigned_to_team c','a.id=c.lead_id')
        		 ->join('prestogroup_teams b','c.lead_id=b.team_id');

            if($user<>'NA') {
                $this->db->where_in('b.team_id',$users,false);
            }

            if($stdate<>'') {
            	$this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
            	$res = $query->result();
            }

           return $res;
	}


	function getConvertedLeadByTeamData($team_id, $lead_stage) {
		$q = $this->db->query("SELECT COUNT(b.id) as asd FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($lead_stage) AND c.team_id=$team_id");

		return $q->result();
	}

	function getDiscountByTeam($user, $stdate, $enddate) {
		$res = '';
		if($user<>'NA') {
        	$users = base64_decode($user);
        }

        $this->db->select('a.id,b.team_id,b.team_name')
        		 ->from('leads a')
        		 ->join('lead_assigned_to_team c','a.id=c.lead_id')
        		 ->join('prestogroup_teams b','c.lead_id=b.team_id');

         if($user<>'NA') {
            $this->db->where_in('b.team_id',$users,false);
          }

        if($stdate<>'') {
        	$this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
        }

        $query = $this->db->get();

         if ($query->num_rows() > 0) {
            	$res = $query->result();
            }

           return $res;

	}

	function getDiscountByTeamData($team_id, $lead_stage) {
		$income=array();
	    $income[]=0;
	    $sql1 = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($lead_stage) AND c.team_id=$team_id");

	      $totall = '';
	      $total_discount = array();
	      $total_discount[] = 0;

	      if($sql1->num_rows() >0) {
            foreach($sql1->result() as $row1) {
               $sql2 = $this->db->query("SELECT qty, price, discount_type, percent_amt FROM lead_products WHERE lead_id=$row1->id");

               if($sql2->num_rows() >0) {
                    foreach($sql2->result() as $row2) {
                        $total_price = $row2->price * $row2->qty;

                        if ($row2->discount_type == 0) {
                            $total_discount[] = $total_price - ($total_price * $row2->percent_amt)/100;
                        } else if($row2->discount_type == 1) {
                            $total_discount[] = $total_price - $row2->percent_amt;
                        } else {
                            $total_discount[] = 0;
                        }
               		 }
            	}

            	$totall = array_sum($total_discount);
           }
       }

       return $totall;
	}

	function getDeadEndLeadStage() {
		$lead_stages = array();
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('dead_end', 1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$lead_stages[] = $row->lead_id;
			}
		} else {
			$lead_stages[] = 0;
		}

		return $lead_stages;
	}

	function getPiSentLeadStage() {
		$lead_stage = '';
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('pi_step', 1)
						->where('company_id',$_SESSION['logged_in']['business_location'])
						->get();

		if ($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
			$lead_stage = $row->lead_id;
		}

		return $lead_stage;
	}


	function getCompareHandleLeadDatamulti($user_id, $stdate, $enddate) {
		$this->db->select('a.lead_id')
      		     ->from('assigned_users_for_lead a')
      		     ->where('a.user_id',$user_id);

      	if($stdate <> '') {
      		$this->db->where('a.addedOn >=', $stdate)->where('a.addedOn <=',$enddate); 
      	}

      	$this->db->group_by('a.lead_id');
      	 $q = $this->db->get();

      	 return $q->num_rows();

	}


		function getConvertedLeadsByMemberDatamulti($user_id, $stdate, $enddate, $conversion_lead_stage) {
		 $this->db->select('a.lead_id')
		 		  ->from('assigned_users_for_lead a')
		 		  ->join('progress_remarks b','a.lead_id=b.lead_id')
		 		  ->where('a.user_id',$user_id)
		 		  ->where('b.lead_status', $conversion_lead_stage);

          if($stdate<>'') {
          $this->db->where('a.addedOn >=', $stdate)->where('a.addedOn <=',$enddate); 
          }
          
          $this->db->group_by('a.lead_id');
          $q=$this->db->get();

          return $q->num_rows();
	}


	function getDiscountByMemberDatamulti($user_id, $stdate, $enddate, $conversion_lead_stage) {
		$income=array();
		$income[]=0;

		if($conversion_lead_stage == '') {
			$conversion_lead_stage = 0;
		}

		if($stdate <> '' && $enddate <> '') {
	        $sdf="AND c.addedOn BETWEEN '".$stdate. "' and '". $enddate."' GROUP BY id";
	     } else {
	        $sdf = "";
	     }
		$sql1 = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN assigned_users_for_lead c ON a.lead_id=c.lead_id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ($conversion_lead_stage) AND c.user_id=$user_id $sdf");
		$totall = '';
		if($sql1->num_rows() > 0) {
            foreach($sql1->result() as $row1) {
            	$sql2 = $this->db->query("SELECT qty, price, discount_type, percent_amt FROM lead_products WHERE lead_id=$row1->id");
            	$total_discount = array();
	            $total_discount[] = 0;
	            
	            if($sql2->num_rows() >0) {
	            	foreach($sql2->result() as $row2) {
	            		 $total_price = $row2->price * $row2->qty;

	                        if ($row2->discount_type == 0) {
	                            $total_discount[] = $total_price - ($total_price * $row2->percent_amt)/100;
	                        } else if($row2->discount_type == 1) {
	                            $total_discount[] = $total_price - $row2->percent_amt;
	                        } else {
	                            $total_discount[] = 0;
	                        }
	            	}
	            }
	            $totall = array_sum($total_discount);
            }
        } else{
	            $totall = "0.00";
              }

           $total_amt = str_replace(",", "", $totall);
           return $total_amt;
	}


	function checkforquotationoraheadstep($leadstage)
	{
		$sort=0;
		$restey=$this->db->select('sort_order')->from('lead_stage')->where('quotation_step',1)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$sort=$row->sort_order;
		}


			$r1=$this->db->select('lead_id')->from('lead_stage')->where('lead_id',$leadstage)->where('sort_order>=',$sort)->get();
			return $r1->num_rows();


	}


	function getLeadStatusonly($lead_id) {
		 $query = $this->db->select('a.lead_id as lead_id, a.lead_name as lead_name, b.remarks')
						   ->from('lead_stage a')
						   ->join('progress_remarks b', 'a.lead_id=b.lead_status')
						   ->where('b.lead_id', $lead_id)
						   ->order_by('b.id', 'DESC')
						   ->limit(1)
						   ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row);
				return $row->lead_name;
			} else {
				return '';

			}

	}

	function checkRolePermission($user_id, $submodule_id) {
		$res = '';
		$sql = $this->db->select('madd, medit, mremove')
						->from('module_capablity')
						->where('role_id', $user_id)
						->where('submoduleid', $submodule_id)
						->get();

		if ($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}


	function getDeadEnd_closeEndLeadStage() {
		$lead_stages = array();
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('dead_end', 1)
						->or_where('conversion_step',1)
						->where('company_id',$_SESSION['logged_in']['business_location'])
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$lead_stages[] = $row->lead_id;
			}
		} else {
			$lead_stages[] = 0;
		}

		return $lead_stages;
	}

	function checkQuotationSent() {
		$lead_stages = array();
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('quotation_step', 1)
						->or_where('quotation_revised_step',1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$lead_stages[] = $row->lead_id;
			}
		} else {
			$lead_stages[] = 0;
		}

		return $lead_stages;
	}

	function getOnlyQuotationSentID() {
		$lead_stages = '';
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('quotation_step', 1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
				$lead_stages = $row->lead_id;
		}

		return $lead_stages;
	}

	function checkIfFollowupDate() {
		$lead_stages = array();
		$sql = $this->db->select('lead_id')
						->from('lead_stage')
						->where('followup_date', 1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$lead_stages[] = $row->lead_id;
			}
		} else {
			$lead_stages[] = 0;
		}

		return $lead_stages;
	}

	function getLeadProducts($lead_id) {
		$res = '';
		$sql = $this->db->select('a.id, a.competitor_product, a.qty, a.price, a.net_price, a.percent_amt, a.product_id, b.instruments_name, b.unit, b.discount_price,b.mvalue, a.packsize, a.flag, c.id as unit_id, c.shortname')
						->from('lead_products a')
						->join('presto_instruments b', 'b.id=a.product_id','left')
						->join('units c', 'c.shortname=b.unit', 'left')
						->where('a.lead_id', $lead_id)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getCreditPeriod() {
		$res = '';
		$sql = $this->db->select('id, credit_period')
						->from('credit_period')
						->where('status', 1)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getQuotationInfo($order_id) {
		$res = '';
		
		$sql1 = $this->db->select('a.id as order_id, a.credit_days, b.validity_date, b.company_id, b.customer_id, c.id, c.company_name, c.customer_name, d.companyname')
		                 ->from('order_punch a')
		                 ->join('customer_quotation b', 'b.id=a.quotation_id')
		                 ->join('customer_detail c', 'c.id=b.customer_id', 'left')
		                 ->join('store_rack_location d', 'd.id=b.company_id', 'left')
		                 ->where('a.id', $order_id)
		                 ->get();


		if($sql1->num_rows() > 0) {
			$res = $sql1->result();
		}

		return $res;
	}

	function getQuotationProducts($quotation_id) {
		$res = '';
		$sql = $this->db->select('b.unit,a.id, a.qty, a.list_price, a.discount_price, a.net_price, a.agreed_price,a.competitor_product, a.product_id, a.batch_code, a.pack_size, b.instruments_name, b.unit, b.pack_size as instrument_pack_size,c.id as unit_id, c.shortname,b.discount_price as max_allowed_price')
						->from('customer_quotation_detail a')
						->join('presto_instruments b', 'b.id=a.product_id')
						->join('units c', 'c.id=a.pack_size', 'left')
						->where('a.quotation_id', $quotation_id)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getOrderDetails($order_id) {
		$res = '';
		$sql = $this->db->select('k.companyname as billingcompany,a.sales_order_no,f.first_name,f.last_name,e.lead_source,b.bill_to,b.ship_to,a.invoice_no, a.po_no, a.po_date,a.added_on,a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.hpcl_billing_company, a.source, a.agent, b.ship_to, b.shipping_name, b.shipping_address, b.shipping_city, b.shipping_pincode, b.shipping_phone_no, b.shipping_mobile_no, b.shipping_email, b.same_shipping_billing, b.shipping_state, b.billing_name, b.billing_address, b.billing_city, b.billing_pincode, b.billing_phone_no, b.billing_mobile_no, b.billing_email, b.billing_state, c.credit_terms, c.extra_days, c.max_credit_limit, c.reference, c.note, d.pan_no, d.registration_type, d.gst_no, d.msme_no')
						->from('order_punch a')
						->join('order_punch_mailing_details b', 'b.order_id=a.id')
						->join('order_punch_payment_details c', 'c.order_id=a.id')
						->join('system_users f','f.user_id=a.agent','left')
						->join('lead_source e','e.source_id=a.source','left')
						->join('order_punch_tax_details d', 'd.order_id=a.id')
						->join('store_rack_location k', 'k.id=a.hpcl_billing_company')
						->where('a.id', $order_id)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	// function getRefID() {
	// 	$query = $this->db->select('id, ref_id')
	// 					  ->from('customer_quotation')
	// 					  ->order_by('id','desc')
	// 					  ->limit(1)
	// 					  ->get();
						  
	// 	if($query->num_rows() > 0) {
	// 		foreach($query->result() as $last_id);
	// 			$lastid = $last_id->id;
	// 			$uniqueno = $last_id->ref_id;
	// 	} else {
	// 			$lastid = 0;
	// 			$uniqueno = 0;
	// 	}

	// 	$unique_no = str_pad($uniqueno+1, 3, '0', STR_PAD_LEFT);
	// 	return 'QUOTE'.$unique_no;
	// }

	function getAllStates()
	{
		$res = '';
		$sql = $this->db->select('state_id, state_name')
						->from('states')
						->where('country_id', 101)
					    ->get();

		if($sql->num_rows()>0) {
			$res = $sql->result();
		}

		return $res;

	}

	function getPaymentDetails($order_id) {
		$res = '';
		$sql = $this->db->select('payment_type, total_amount, cheque_no, pdc_date, utr_no, upload_po, credit_days, po_no, po_date')
						->from('order_punch')
						->where('id', $order_id)
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}


	function getLeadAttachment($lead_id)
	{
		$audio='';

		$row=$this->db->select('audio')->from('leads')->where('id',$lead_id)->where('audio!=','')->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $roww);
			$audiolink=site_http_root.'audio_files/'.$roww->audio;
			$audio.='<a href="'.$audiolink.'" download>Download Audio Recording</a><br/>';
		}

	

	 $ret=$this->db->select('attachment')->from('lead_based_attachment')->where('lead_id',$lead_id)->get();
	 if($ret->num_rows()>0)
	 {
	 	$i=1;
	 	foreach($ret->result() as $row)
	 	{
	 		$audio.='<a href="'.site_http_root.'image_bank/lead_based_attachment/'.$row->attachment.'">Download File '.$i.'</a><br/>';
	 		$i++;
	 	}

	 }

		return $audio;

	}


	function getCustomerDetails_frommaster($quotation_id) {
		  $res = '';

		  $query=$this->db->select('b.pan,b.gst,b.assigned_to,b.address,a.company_id, a.customer_id,b.title,b.customer_name,b.company_name,b.state,c.state_name,b.contact_no,b.email,b.city,b.pincode, b.msme_number, b.payment_type, b.credit_days')
		  				 ->from('customer_quotation a')
		  				 ->join('customer_detail b','a.customer_id=b.id', 'left')
		  				 ->join('states c','c.state_id=b.state','left')
		  				 ->where('a.id',$quotation_id)
		  				 ->get();

		  if ($query->num_rows() > 0) {
				  $res = $query->result();
					
			} 

			return $res;
	}

		function getlastremarks($quotation_id)
		{
			$data='';
			$sql = $this->db->select('lead_id')
							->from('customer_quotation')
							->where('id', $quotation_id)
							->where('lead_id !=', 0)
							->get();

			if($sql->num_rows() > 0) {
				foreach($sql->result() as $row);
				$getConversionLeadStage=$this->dashboardmodel->getConversionLeadStage();
				$query = $this->db->select('remarks')
				 				  ->from('progress_remarks')						   
								  ->where('lead_id', $row->lead_id)
								  ->where('lead_status',$getConversionLeadStage)
								  ->order_by('id', 'DESC')
								  ->limit(1)
								  ->get();

					if($query->num_rows() > 0) {
						foreach ($query->result() as $querys) {
							$data=$querys->remarks;
						}
					} 

			}

							return $data;
		}


		function getpaymentdetail($quotation_id)
		{
			$data=array();
			$qyer=$this->db->select('monthly_consumption,payment_type,credit_days')
						   ->from('customer_quotation')
						   ->where('id',$quotation_id)
						   ->get();
			
			if($qyer->num_rows()>0) {
				foreach($qyer->result() as $row);
					$data[]=$row->monthly_consumption;
					$data[]=$row->payment_type;
					$data[]=$row->credit_days;
			}

			return $data;

		}

	function getAllProducts() {
		$res = '';
		$sql = $this->db->select('id, instruments_name,pack_size, distributorprice')
				 	    ->from('presto_instruments')
					    ->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function get_usernames($user_id) {
		$res = '';
		$users = array();
		$usernames = array();

	   $this->db->select('user_id,first_name, last_name')
				->from('system_users');

				if($user_id != '') {
				$this->db->where_in('user_id', $user_id, false);
				}
				$this->db->group_by('user_id');
				$sql = $this->db->get();

			if($sql->num_rows() > 0) {
				foreach($sql->result() as $row) {
					$usernames[] = $row->first_name.' '.$row->last_name;
				}
			}

		$res = implode (', ', $usernames);
	
		return $res;
	}

	function get_lead_stages($lead_stage_id) {
		$res = '';
		$users = array();
		$lead_stages = array();
 
				   $this->db->select('lead_name')
							->from('lead_stage');

							if($lead_stage_id != '') {
							$this->db->where_in('lead_id', $lead_stage_id, false);
							}
							$sql = $this->db->get();

			if($sql->num_rows() > 0) {
				foreach($sql->result() as $row) {
					$lead_stages[] = $row->lead_name;
				}
			}

		$res = implode (',', $lead_stages);
		// echo $res;exit;
		return $res;
	}

	function get_products($product_id) {
		$res = '';
		$users = array();
		$products = array();

				   $this->db->select('instruments_name')
							->from('presto_instruments');

							if($product_id != '') {
							$this->db->where_in('id', $product_id, false);
							}
							$sql = $this->db->get();

			if($sql->num_rows() > 0) {
				foreach($sql->result() as $row) {
					$products[] = $row->instruments_name;
				}
			}

		$res = implode (',', $products);
		// echo $res;exit;
		return $res;
	}

	function getfilterleadsourceapp($lead_source)
	{
		$d='';
		$lead_sources=array();
		$row=$this->db->select('lead_source')->from('lead_source')->where_in('source_id',$lead_source,false)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows)
			{
				$lead_sources[]=$rows->lead_source;
			}

		}

		if(count($lead_sources)>0)
		{
			$d=implode(", ",$lead_sources);
		}

		return $d; 

	}

	function get_reason($reason)
	{
		$d='';
		$reasons=array();
		$row=$this->db->select('reason')->from('leads_unqualified_reason')->where_in('reason_id',$reason,false)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows)
			{
				$reasons[]=$rows->reason;
			}

		}

		if(count($reasons)>0)
		{
			$d=implode(", ",$reasons);
		}

		return $d; 

	}

				function getHpclLocations() {
				$res = '';
				$sql = $this->db->select('id, name')
				->from('hpcl_location')
				->get();

				if($sql->num_rows() > 0) {
				$res = $sql->result();
				}

				return $res;
				}

	function getApprovalProductDetails($approval_id) {
		$ht='';
		$query=$this->db->select('a.invoice,a.billing_date,a.annexture,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI</th>
				<th style='padding:10px;'>Minimum Qty</th>
				<th style='padding:10px;'>Annexure</th>
	

				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				if($rows->annexture<>'')
				{
					$anex="<a href='".site_http_root."type_one_annexure/".$rows->annexure_upload."' download>Download</a>";
				}else
				{
					$anex='';
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$rows->unit."</td>
						<td style='padding:10px;'>".$rows->moq." ".$rows->unit."</td>
						<td style='padding:10px;'>".$rows->annexture."<br/>".$anex."</td>
					
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht;
	}

	function getEditApproval($approval_id) {
		$res = '';
		$query = $this->db->select('c.first_name,c.last_name,a.added_on,a.added_by,a.auto_gen_code,a.id, a.current_date, a.hpcl_location, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,a.combination')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location','left')
						  ->join('system_users c','a.added_by=c.user_id','left')
						  ->where('a.id', $approval_id)
		 				  ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}



	function getEditProductApprovalOLdd($approval_id) {
		$res = '';
		$query=$this->db->select('a.moq,a.id, a.approved_price, a.product_id, a.pack_size, a.price_validity, a.credit_vli, a.moq')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}


function getEditProductApproval($approval_id) {
    $res = '';
    $query=$this->db->select('a.moq,a.id, a.approved_price, a.product_id, a.pack_size,a.validity_from,a.validity_to, a.price_validity, a.credit_vli, a.moq, a.location,a.annexture, a.annexure_upload')
              ->from('approval_product_details a')
              ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
              ->where('a.approval_id',$approval_id)
              ->get();

     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }


	function getSampleRequiredLeadStage() {
			$res = '';
			$sql = $this->db->select('lead_id')
							->from('lead_stage')
							->where('sample', 1)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row1);
				$res = $row1->lead_id;
			}

			return $res;
		}

	function getTrialRequiredLeadStage()
	{
		$res = '';
			$sql = $this->db->select('lead_id')
							->from('lead_stage')
							->where('trail', 1)
							->get();

			if ($sql->num_rows() > 0) {
				foreach ($sql->result() as $row1);
				$res = $row1->lead_id;
			}

			return $res;

	}


	function getProductsTabular($lead_id) {

		$ht='';
			$ht.="<table class='table table-bordered' style='width:300px'>
						<thead>
						<tr>
						<th style='padding:2px;width:210px;'>Sr. No.</th>
						<th style='padding:2px;width:450px;'>Our Product</th>
						<th style='padding:2px;width:350px;'>Qty/Unit</th>
						</tr>
						</thead>
						<tbody>";

	   	$instruments = array();
		$sql = $this->db->select('a.id as leadproduct,a.competitor_product, a.product_id, a.qty, a.price, a.percent_amt, a.net_price, a.flag, b.id, b.mvalue, b.instruments_name,a.packsize, c.shortname')
						->from('lead_products a')
	   					->join('presto_instruments b', 'b.id=a.product_id', 'left')
	   					->join('units c', 'c.id=a.packsize', 'left')
	   					->where('a.lead_id', $lead_id)
	   					->get();

	   					if($sql->num_rows()>0)
	   					{
	   						$i=1;
	   						foreach($sql->result() as $row)
	   						{
													if($row->packsize==1)
													{
													$pack="Drum";
													}else if($row->packsize==2)
													{
													$pack="Bucket";
													}else if($row->packsize==3)
													{
													$pack="Bulk";
													}else if($row->packsize==4)
													{
													$pack="Cans";
													}else if($row->packsize==5)
													{
													$pack="Kgs";
													}else
													{
													$pack='';
													}

												$ht.="<tr>
												<td style='padding:2px;'>".$i."</td>
												<td style='padding:2px;'>".ucwords(strtolower($row->instruments_name))."</td>
												
												<td style='padding:2px;'>".ucwords(strtolower($row->qty." ".$row->shortname))."</td>
												</tr>";
								$i++;
								}

				

	   					}else
	   					{
	   						$ht.="<tr>
												<td style='padding:2px;' colspan='3'>No Products Found</td>
											
												</tr>";

	   					}

	   						$ht.="</tbody> </table>";


	   						return $ht;

	   	
	   }

   function getAllVendors() {
   		$res = '';
   		$sql = $this->db->select('id, name')
   						->from('vendors')
   						->get();

   		if($sql->num_rows() > 0) {
   			$res = $sql->result();
   		}

   		return $res;
   }


    function getEditInventoryDetails($inventory_id) {
   		$res = '';
   		$sql = $this->db->select('id, product, qty, pack_size, rate')
   						->from('inventory_details')
   						->where('inventory_id', $inventory_id)
   						->get();

   		if($sql->num_rows() > 0) {
   			$res = $sql->result();
   		}

   		return $res;
   }

     function getAllUnits() {
   		$res = '';
   		$sql = $this->db->select('id, shortname')
   						->from('units')
   						->get();

   		if($sql->num_rows() > 0) {
   			$res = $sql->result();
   		}

   		return $res;
   }

   function getEditInventoryEntry($id) {
   		$res = '';
   		$sql = $this->db->select('id, currentdate, bill_no, party')
   						->from('purchase_entry')
   						->where('id', $id)
   						->get();

   		if($sql->num_rows() > 0) {
   			$res = $sql->result();
   		}

   		return $res;
   }

     function getEditInventoryEntryDetails($approval_detail_id) {
   		$res = '';
   		$sql = $this->db->select('a.id, approval_detail_id, a.qty, a.pack_size, b.product_id, b.approved_price')
   						->from('purchase_entry_details a')
   						->join('approval_product_details b', 'b.id=a.approval_detail_id')
   						->where('b.approval_id', $approval_detail_id)
   						->get();

   		if($sql->num_rows() > 0) {
   			$res = $sql->result();
   		}

   		return $res;
   }

    function getProductName($product_id) {
		$res = '';
		$sql = $this->db->select('instruments_name')
				 	    ->from('presto_instruments')
				 	    ->where('id', $product_id)
				 	    ->where('status',1)
					    ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
				$res = $row->instruments_name;
		}

		return $res;
	}

	function getAllApprovalProdIDs($approval_id) {
		$res = array();
		$sql = $this->db->select('id')
						->from('approval_product_details')
						->where('approval_id', $approval_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
				$res[] = $row->id;
		}

		return $res;
	}

	function getLastInsertedCode() {
		$unique_no = '001';
		$sql = $this->db->select('auto_gen_code')
						->from('approval_form')
						->order_by('id', 'desc')
						->limit(1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$code = $row->auto_gen_code;
			$unique_no = str_pad($code+1, 3, '0', STR_PAD_LEFT);
		}

		return $unique_no;
	}

	function chkIfProductExists($approval_id, $product) {
		$sql = $this->db->select('id')
					    ->from('approval_product_details')
			   			->where('product_id',$product)
			   			->where('approval_id',$approval_id)
			   			->get();

		return $sql->num_rows();
	}

	function getLastInsertedTypeTwoCode() {
		$unique_no = '001';
		$sql = $this->db->select('auto_gen_code')
						->from('type_two_approval')
						->order_by('id', 'desc')
						->limit(1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$code = $row->auto_gen_code;
			$unique_no = str_pad($code+1, 3, '0', STR_PAD_LEFT);
		}

		return $unique_no;
	}

	function getTypeTwoApprovalProductDetails($approval_id) {
		$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity, a.credit_vli, a.moq, b.instruments_name')
					    ->from('type_two_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI Per Ltr</th>
				<th style='padding:10px;'>Minimum Qty</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {
				$shortname=$this->getUnitName($rows->pack_size);

					if($rows->price_validity<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}


				$ht.="<tr>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$shortname."</td>
						<td style='padding:10px;color:".$color."'>".date('d-m-Y',strtotime($rows->price_validity))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$shortname."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}
//echo $ht; exit;
		return $ht;
	}
	
	function chkIfTypeTwoProductExists($approval_id, $product) {
		$sql = $this->db->select('id')
					    ->from('type_two_product_details')
			   			->where('product_id',$product)
			   			->where('approval_id',$approval_id)
			   			->get();

		return $sql->num_rows();
	}

	function getEditTypeTwoApproval($approval_id) {
		$res = '';
		$query = $this->db->select('a.addedOn,c.first_name,c.last_name,a.id, a.current_date, a.hpcl_location, a.transportation, a.transportation_rate, b.name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('system_users c','a.addedBy=c.user_id','left')
						  ->where('a.id', $approval_id)
		 				  ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}

	function getEditTypeTwoProductApproval($approval_id) {
		$res = '';
		$query=$this->db->select('a.pack_size,a.id, a.approved_price, a.product_id, a.price_validity, a.credit_vli, a.moq')
					    ->from('type_two_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}

	function getAllTransporters() {
		$res = '';
		$sql = $this->db->select('id, name')
						->from('transporter_details')
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getUnitName($unit_id) {
   		$shortname = '';
   		$sql = $this->db->select('shortname')
   						->from('units')
   						->where('id', $unit_id)
   						->get();

   		if($sql->num_rows() > 0) {
   			foreach($sql->result() as $row);
   				$shortname = $row->shortname;
   		}

   		return $shortname;
   }


   function getInventory_details($approvalid)
   {
   	$data=array();
   $reset=	$this->db->select('a.currentdate,a.bill_no,a.party,a.added_by,b.name')->from('purchase_entry a')->join('vendors b','a.party=b.id')->where('a.approval_id',$approvalid)->get();
   if($reset->num_rows()>0)
   {
   		foreach($reset->result() as $row)
   		{
   			$data['currentdate']=date('d-M-Y',strtotime($row->currentdate));
   			$data['bill_no']=$row->bill_no;
   			$data['partyname']=$row->name;
   			$data['added_by']=$row->added_by;

   		}
   }

   return $data;

   }



 function getInventory_product_details($approval_detail_lid)
   {
   	$data='';

			$query=$this->db->select('a.qty')
			->from('purchase_entry_details a')
			->where('a.approval_detail_id',$approval_detail_lid)
			->get();
   if($query->num_rows()>0)
   {
   		foreach($query->result() as $row);
   			$data=$row->qty;
   			
   }

   return $data;

   }


   function getVendors() {
		$res = '';
		$sql = $this->db->select('id, name')
						->from('vendors')
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function months_purchase_type1($start,$end,$location,$party,$product)
	{
		$credit_note_sum=array();
		$credit_note_sum[]=0;
		$start_date=date('Y-m-d',strtotime($start));
		$end_date=date('Y-m-d',strtotime($end));

		$this->db->select('a.id')
						  ->from('purchase_entry a')
						  ->join('vendors b', 'b.id=a.party')
						  ->join('approval_form c', 'c.id=a.approval_id')
						  ->join('hpcl_location d', 'd.id=c.hpcl_location');

			if($start <> '' && $end <> '' && $start <> 'ALL' && $end <> 'ALL') {
				 $this->db->where('a.currentdate >=', $start_date);
				 $this->db->where('a.currentdate <=', $end_date);
			}

			if($location<>'' && $location<>'ALL')
			{
				$this->db->where('c.hpcl_location',$location);
			}

			if($party<>'' && $party<>'ALL')
			{
				$this->db->where('a.party',$party);
			}

    	$query =  $this->db->get();
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				 $this->db->select('a.qty, b.approved_price')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->where('a.entry_id', $row->id);
										if($product<>'' && $product<>'ALL')
									{
										$this->db->where('b.product_id',$product);
									}
								$sql =	$this->db->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {
							$credit_note_sum[] = $rows->qty * $rows->approved_price;

						}
					}
			}

		}

			return array_sum($credit_note_sum);

	}


	function IND_money_format($number){
        $decimal = (string)($number - floor($number));
        $money = floor($number);
        $length = strlen($money);
        $delimiter = '';
        $money = strrev($money);

        for($i=0;$i<$length;$i++){
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
                $delimiter .=',';
            }
            $delimiter .=$money[$i];
        }

        $result = strrev($delimiter);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);

        if( $decimal != '0'){
            $result = $result.$decimal;
        }

        return $result;
    }

    function get_location_name($loc)
    {
    	$loca='';
    	$rest=$this->db->select('name')->from('hpcl_location')->where('id',$loc)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$loca=$row->name;
    	}

    	return $loca;
    }

       function get_customers_name($customer)
    {
    	$loca='';
    	$rest=$this->db->select('company_name')->from('customer_detail')->where('id',$customer)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$cust=$row->company_name;
    	}

    	return $cust;
    }


     function get_party_name($loc)
    {
    	$loca='';
    	$rest=$this->db->select('name')->from('vendors')->where('id',$loc)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$loca=$row->name;
    	}

    	return $loca;
    }


     function get_product_name($loc)
    {
    	$loca='';
    	$rest=$this->db->select('instruments_name,pack_size')->from('presto_instruments')->where('id',$loc)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$loca=$row->instruments_name."-".$row->pack_size;
    	}

    	return $loca;
    }

  function get_company_name($comp)
    {
      $company='';
      $rest=$this->db->select('id,companyname')->from('store_rack_location')->where('id',$comp)->get();
      if($rest->num_rows()>0)
      {
        foreach($rest->result() as $row);
        $company=$row->companyname;
      }

      return $company;
    }




    function getApprovalProductDetails_late_payemnt($approval_id) {
		$ht='';
		$amount=array();
		$amount[]=0;
		$query=$this->db->select('c.qty,a.pack_size,a.approved_price, a.price_validity, a.credit_vli, a.moq, b.instruments_name')
					    ->from('approval_product_details a')
					    ->join('purchase_entry_details c','c.approval_detail_id=a.id')
					    ->join('presto_instruments b', 'b.id=a.product_id')
					    ->where('a.approval_id',$approval_id)
					    ->group_by('c.approval_detail_id')
					    ->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;'>Qty Purchased</th>
				<th style='padding:10px;'>Total Amount</th>
				</tr>
				</thead>
				<tbody>";
				
			foreach($query->result() as $rows) {

				$packsize=$this->getUnitName($rows->pack_size);
				$totalamount=$rows->qty*$rows->approved_price;



				$ht.="<tr>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$packsize."</td>
					
						<td style='padding:10px;'>".$rows->qty." ".$packsize."</td>
						<td style='padding:10px;color:red;font-weight:bold;'>₹ ".$totalamount."</td>
						
					</tr>";
					$amount[]=$totalamount;
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($amount);
	}

	function today_due_payment($vendor)
	{
		$data=array();
		$data[]=0;
		$i=1;
		$party=$vendor;
		//$product=$this->uri->segment(6);
		         $this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.gst')
		         		  ->from('inventory a')
						  ->join('hpcl_location d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}

						$this->db->where_in('a.payment_type','5,6',false);
						$this->db->where('a.pur_payment',0);


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				

						
						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {
				$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);

				if($row->payment_type==2)
						{
							$payment="Cash";
							$cr_days='';

						}else if($row->payment_type==3)
						{
								$payment="Online";
								$cr_days='';

						}else if($row->payment_type==4)
						{

							$payment="PDC";
							$cr_days='';
						}else if($row->payment_type==5)
						{
							$payment="Credit";
							$cr_days=$row->credit_days;
						}else  
						{
							$payment="Advance";
							$cr_days=$row->credit_days;
						}


						if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
							 $gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;

            $prev_payment=$this->check_for_previous_payment($row->id);
							$data[] = $grand_amt-$prev_payment;

						$i++;
					}
					
						
					}

				}

		return array_sum($data);
	}


	function today_due_payment_upcoming($vendor)
	{
		$total=array();
		$total[]=0;
		$current_date=date('Y-m-d');
		$this->db->select('a.id,a.purchase_entry_on,a.credit_period')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($vendor<>'' && $vendor<>'ALL')
						  {
						  	$this->db->where('c.party',$vendor);
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date < $due_date) {
					$total[]=$prd_detail[1];
					}

			}

		}



		return array_sum($total);
	}




	function today_due_payment_late($vendor)
	{
		$total=array();
		$total[]=0;
		$current_date=date('Y-m-d');
		$this->db->select('a.id,a.purchase_entry_on,a.credit_period')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($vendor<>'' && $vendor<>'ALL')
						  {
						  	$this->db->where('c.party',$vendor);
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date < $due_date) {
					$total[]=$prd_detail[1];
					}

			}

		}



		return array_sum($total);
	}



function today_due_payment_late_with_interest($vendor)
	{
		$total=array();
		$total[]=0;
		$current_date=date('Y-m-d');
		$this->db->select('a.id,a.purchase_entry_on,a.credit_period')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($vendor<>'' && $vendor<>'ALL')
						  {
						  	$this->db->where('c.party',$vendor);
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date < $due_date) {
					$getinterest=$prd_detail[1]*0.1375;
					$total[]=$getinterest;
					}

			}

		}



		return array_sum($total);
	}


function get_months_claim_amount($start_date,$end_date,$vendor,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		         $this->db->select('a.id, a.bill_no, b.name as party, c.auto_gen_code, d.name')
						  ->from('purchase_entry a')
						  ->join('vendors b', 'b.id=a.party')
						  ->join('approval_form c', 'c.id=a.approval_id')
						  ->join('hpcl_location d', 'd.id=c.hpcl_location');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.currentdate >=', $start_date);
				 $this->db->where('a.currentdate <=', $end_date);
			}

			if($vendor <> '' && $vendor <> 'ALL') {
				$this->db->where('a.party', $vendor);
			}

			if($location <> '' && $location <> 'ALL') {
				$this->db->where('c.hpcl_location', $location);
			}
	   $query =  $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$sql = $this->db->select('a.qty, b.credit_vli')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->join('presto_instruments c', 'c.id=b.product_id')
									->join('units d', 'd.id=a.pack_size')
									->where('a.entry_id', $row->id)
									->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum[] = $rows->qty * $rows->credit_vli;
						
			

						$i++;
						}
					}
				
				}
			}
			
			return array_sum($credit_note_sum);
	

}


function getTypeTwoTransportationDetails($id,$transportation_rate)
{
	 $html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
        <th style="width:80px;">Transporation Rate</th>
        <th style="width:80px;">Claim Amount</th>
        </tr>
        </thead><tbody>';

$d=array();
	$restey=$this->db->select('a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_two_product_details a')->join('item_delivery_details b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$transportation_rate;
			$d[]=$tamount;
			 $html.='<tr>
        <td style="padding:10px;">'.$i.'</td>
        <td style="padding:10px;">'.$rows->instruments_name.'</td>
        <td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
         <td style="padding:10px;font-weight:bold;">'.$transportation_rate.' / '.$packsize.'</td>
          <td style="padding:10px;color:green;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      </tr>';

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($d);


}

function TransporterDetails($id)
{
	$html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        
        <th style="width:100px;">Transporter Name</th>
         <th style="width:30px;">Mobile</th>
        <th style="width:80px;">Vehicle No.</th>
        <th style="width:80px;">Vehicle Type</th>
        <th style="width:80px;">Transported Rate</th>
        </tr>
        </thead><tbody>';

	$restey=$this->db->select('a.transporter_id,a.vehicle_no,a.vehicle_type,b.mobile_no,b.name,a.transport_rate')->from('item_delivery a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $rows);

		$html.='<tr>

		<td style="padding:10px;">'.$rows->name.'</td>
		<td style="padding:10px;">'.$rows->mobile_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_type.'</td>
		<td style="padding:10px;font-weight:bold;">₹'.$rows->transport_rate.'</td>
		</tr>';



	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';
	}

	return $html;
}



 function get_transporter_name($loc)
    {
    	$loca='';
    	$rest=$this->db->select('name')->from('transporter_details')->where('id',$loc)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$loca=$row->name;
    	}

    	return $loca;
    }



    function get_months_transport_claim_amount_type2($start_date,$end_date,$transporter,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		$this->db->select('a.id,a.transportation_rate')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) { 
				$transportation_rate = $row->transportation_rate;
				$getApprovalProductDetails = $this->getTypeTwoTransportationDetails($row->id,$transportation_rate);

				$prdeetail=explode('|',$getApprovalProductDetails);
				$credit_note_sum[]=$prdeetail[1];
			}

		}


			
			return array_sum($credit_note_sum);
	

}


function getTypeTwoCredit_VLI_Details($id,$product)
{

	 $html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
        <th style="width:80px;">Credit/Vli</th>
        <th style="width:80px;">Claim Amount</th>
        </tr>
        </thead><tbody>';

$d=array();
$d[]=0;
	$this->db->select('a.credit_vli,a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_two_product_details a')->join('item_delivery_details b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id);
	if($product!='' && $product<>'ALL') 
	{
		$this->db->where('a.product_id',$product);
	}
	$restey=$this->db->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$rows->credit_vli;
			$d[]=$tamount;
			 $html.='<tr>
        <td style="padding:10px;">'.$i.'</td>
        <td style="padding:10px;">'.$rows->instruments_name.'</td>
        <td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
         <td style="padding:10px;font-weight:bold;">'.$rows->credit_vli.' / '.$packsize.'</td>
          <td style="padding:10px;color:green;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      </tr>';

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($d);


}



  function get_months_credit_claim_amount_type2Oldd($start_date,$end_date,$location,$product)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		$this->db->select('a.id')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.addedOn >=', $start_date);
				 $this->db->where('a.addedOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}



		$query = $this->db->get();
		$v=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) { 
				$getApprovalProductDetails = $this->getTypeTwoCredit_VLI_Details($row->id,$product);
				$prdeetail=explode('|',$getApprovalProductDetails);

				if($product<>'' && $product<>'ALL')
				{
				$v=$this->chkIfTypeTwoProductExists($row->id,$product);
				}

				if($v>0)
				{
				$credit_note_sum[]=$prdeetail[1];
				}	
			}

		}


			
			return array_sum($credit_note_sum);
	

}


function getTypeTwoTransportationDetails_without_amt($id,$transportation_rate)
{
	 $html='';
	 $t=array();
	 $t[]=0;
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
          <th style="width:30px;">Transporter Payment</th>
        
        </tr>
        </thead><tbody>';

$d=array();
	$restey=$this->db->select('a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_two_product_details a')->join('item_delivery_details b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$transportation_rate;

			$html.='<tr>
			<td style="padding:10px;">'.$i.'</td>
			<td style="padding:10px;">'.$rows->instruments_name.'</td>
			<td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
			<td style="padding:10px;color:red;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      
          
      </tr>';

      $t[]=$tamount;

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($t);


}



function TransporterDetails_with_rate($id)
{
	$html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        
        <th style="width:100px;">Transporter Name</th>
         <th style="width:30px;">Mobile</th>
        <th style="width:80px;">Vehicle No.</th>
        <th style="width:80px;">Vehicle Type</th>
        <th style="width:80px;">Transported Rate</th>
        </tr>
        </thead><tbody>';
$trate=0;
	$restey=$this->db->select('a.transporter_id,a.vehicle_no,a.vehicle_type,b.mobile_no,b.name,a.transport_rate')->from('item_delivery a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $rows);
$trate=$rows->transport_rate;
		$html.='<tr>

		<td style="padding:10px;">'.$rows->name.'</td>
		<td style="padding:10px;">'.$rows->mobile_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_type.'</td>
		<td style="padding:10px;font-weight:bold;">₹'.$rows->transport_rate.'</td>
		</tr>';



	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';
	}

	return $html."|".$trate;
}

   function get_pending_transporter_payment_type2($transporter,$location)
{

		$credit_note_sum = array();
		$credit_note_sum[] =0;
		

				$hpcl_location = $location;
				$transport_type = $transporter;

						$query=$this->db->select('a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
						->from('approval_product_details_type_two a')
						->join('approval_form_type_two d','a.approval_id=d.id')
						->join('presto_instruments b', 'b.id=a.product_id' ,'left')
						->join('hpcl_location c', 'c.id=a.location')
						->join('hpcl_direct_customer e', 'e.id=d.customer_name')
						->where('a.delivered',1)
						->where('a.vehicle_type',2)
						->where('a.transporter_payment',0);

					    if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    {
					    	$this->db->where('a.location',$hpcl_location);
					    }
					   
					    if($transport_type<>'ALL' && $transport_type<>'')
					    {
					    	$this->db->where('a.transporter_id',$transport_type);
					    }

					  $query=$this->db->get();

		if($query->num_rows()>0) {

				
			foreach($query->result() as $rows)
			{
					
					if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
					{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
					}else
					{
					$p="";
					$color="green;font-weight:bold;";
					}

					if($rows->transport_type==1)
					{	

					$transport="EXMI";
					$trate='-';

					}else
					{
					$transport="Delivered";
					$trate=$rows->transport_rate."/".$rows->unit;
					}

						 
					$delivery_detail=floatval($rows->deliveredQty)." ".$rows->unit."<br/><br/>Delivered On-".date('d-M-Y',strtotime($rows->deliveredOn));

					$tfrom='';
					$vtype='';
					$vno='';
					$rtype='';
					$t_type_rate='';
					$amount=0;
					$tds=$this->get_trasnporter_tds($rows->transporter_id);
					if($rows->transporter_from==1)
					{
						$tfrom="OUR";
					if($rows->vehicle_type==1)
					{
						$vtype="Our Vehicle";
						$vno=$rows->vehicle_no;
					}else if($rows->vehicle_type==2)
					{
						$vtype="Hired Vehicle";
						$vno='';
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate*$rows->deliveredQty;

							$amount=$t_type_rate;
							if($tds>0)
							{
								$tds_amount=$amount*($tds/100);
							}else
							{
								$tds_amount=0;
							}

							$amount=$t_type_rate-$tds_amount;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
							$amount=$rows->transporter_fixed_rate;
							if($tds>0)
							{
								$tds_amount=$amount*($tds/100);
							}else
							{
								$tds_amount=0;
							}

							$amount=$amount-$tds_amount;
							
						}else{ }
					}else
					{

					}

					}else if($rows->transporter_from==2)
					{
						$tfrom="CUSTOMER";
					}else
					{
						$tfrom="";
					}


						$credit_note_sum[] =$amount; 
			}
						

			}
				
			
		return array_sum($credit_note_sum);
	
	}
			
			


 function get_done_transporter_payment_type2($start_date,$end_date,$transporter,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		  $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',1);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.paidOn >=', $start_date);
				 $this->db->where('c.paidOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeTwoTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);
					
					$credit_note_sum[]=$pdetails[1];
			}

		}


			
			return array_sum($credit_note_sum);
	


}

	function getLastInsertedTypeThreeCode() {
		$unique_no = '001';
		$sql = $this->db->select('auto_gen_code')
						->from('type_three_approval')
						->order_by('id', 'desc')
						->limit(1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$code = $row->auto_gen_code;
			$unique_no = str_pad($code+1, 3, '0', STR_PAD_LEFT);
		}

		return $unique_no;
	}


	function getTypeThreeApprovalProductDetails($approval_id) {
		$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity, a.credit_vli, a.moq, b.instruments_name')
					    ->from('type_three_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI Per Ltr</th>
				<th style='padding:10px;'>Minimum Qty</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {
				$shortname=$this->getUnitName($rows->pack_size);

					if($rows->price_validity<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}


				$ht.="<tr>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$shortname."</td>
						<td style='padding:10px;color:".$color."'>".date('d-m-Y',strtotime($rows->price_validity))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$shortname."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}
//echo $ht; exit;
		return $ht;
	}


	function chkIfTypeThreeProductExists($approval_id, $product) {
		$sql = $this->db->select('id')
					    ->from('type_three_product_details')
			   			->where('product_id',$product)
			   			->where('approval_id',$approval_id)
			   			->get();

		return $sql->num_rows();
	}

	function getEditTypeThreeApproval($approval_id) {
		$res = '';
		$query = $this->db->select('a.addedOn,c.first_name,c.last_name,a.id, a.current_date, a.hpcl_location, a.transportation, a.transportation_rate, b.name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('system_users c','a.addedBy=c.user_id','left')
						  ->where('a.id', $approval_id)
		 				  ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}


	function getEditTypeThreeProductApproval($approval_id) {
		$res = '';
		$query=$this->db->select('a.pack_size,a.id, a.approved_price, a.product_id, a.price_validity, a.credit_vli, a.moq')
					    ->from('type_three_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		 if($query->num_rows() > 0) {
		 	$res = $query->result();
		 }

		 return $res;
	}


	function getTypeThreeTransportationDetails($id,$transportation_rate)
{
	 $html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
        <th style="width:80px;">Transporation Rate</th>
        <th style="width:80px;">Claim Amount</th>
        </tr>
        </thead><tbody>';

$d=array();
	$restey=$this->db->select('a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_three_product_details a')->join('item_delivery_details_type_3 b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$transportation_rate;
			$d[]=$tamount;
			 $html.='<tr>
        <td style="padding:10px;">'.$i.'</td>
        <td style="padding:10px;">'.$rows->instruments_name.'</td>
        <td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
         <td style="padding:10px;font-weight:bold;">'.$transportation_rate.' / '.$packsize.'</td>
          <td style="padding:10px;color:green;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      </tr>';

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($d);


}


 function get_months_transport_claim_amount_type3($start_date,$end_date,$transporter,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		$this->db->select('a.id,a.transportation_rate')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('a.transport_owned_hired',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) { 
				$transportation_rate = $row->transportation_rate;
				$getApprovalProductDetails = $this->getTypeThreeTransportationDetails($row->id,$transportation_rate);

				$prdeetail=explode('|',$getApprovalProductDetails);
				$credit_note_sum[]=$prdeetail[1];
			}

		}


			
			return array_sum($credit_note_sum);
	

}


function TransporterDetails_type3($id)
{
	$html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        
        <th style="width:100px;">Transporter Name</th>
         <th style="width:30px;">Mobile</th>
        <th style="width:80px;">Vehicle No.</th>
        <th style="width:80px;">Vehicle Type</th>
        <th style="width:80px;">Transported Rate</th>
        </tr>
        </thead><tbody>';

	$restey=$this->db->select('a.transporter_id,a.vehicle_no,a.vehicle_type,b.mobile_no,b.name,a.transport_rate')->from('item_delivery_type_3 a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $rows);

		$html.='<tr>

		<td style="padding:10px;">'.$rows->name.'</td>
		<td style="padding:10px;">'.$rows->mobile_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_type.'</td>
		<td style="padding:10px;font-weight:bold;">₹'.$rows->transport_rate.'</td>
		</tr>';



	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';
	}

	return $html;
}


function getTypeThreeCredit_VLI_Details($id,$product)
{

	 $html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
        <th style="width:80px;">Credit/Vli</th>
        <th style="width:80px;">Claim Amount</th>
        </tr>
        </thead><tbody>';

$d=array();
$d[]=0;
	$this->db->select('a.credit_vli,a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_three_product_details a')->join('item_delivery_details_type_3 b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id);
	if($product!='' && $product<>'ALL') 
	{
		$this->db->where('a.product_id',$product);
	}
	$restey=$this->db->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$rows->credit_vli;
			$d[]=$tamount;
			 $html.='<tr>
        <td style="padding:10px;">'.$i.'</td>
        <td style="padding:10px;">'.$rows->instruments_name.'</td>
        <td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
         <td style="padding:10px;font-weight:bold;">'.$rows->credit_vli.' / '.$packsize.'</td>
          <td style="padding:10px;color:green;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      </tr>';

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($d);


}


function get_months_credit_claim_amount_type3($start_date,$end_date,$transporter,$location,$product)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		$this->db->select('a.id')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->where('a.item_delivered',1);
						 

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.addedOn >=', $start_date);
				 $this->db->where('a.addedOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}



		$query = $this->db->get();
		$v=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) { 
				$getApprovalProductDetails = $this->getTypeThreeCredit_VLI_Details($row->id,$product);
				$prdeetail=explode('|',$getApprovalProductDetails);

				if($product<>'' && $product<>'ALL')
				{

				$v=$this->chkIfTypeThreeProductExists($row->id,$product);
				}


				if($v>0)
				{
				$credit_note_sum[]=$prdeetail[1];
				}	
			}

		}

		
			
			return array_sum($credit_note_sum);
	

}



function TransporterDetails_with_rate_type_3($id)
{
	$html='';
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        
        <th style="width:100px;">Transporter Name</th>
         <th style="width:30px;">Mobile</th>
        <th style="width:80px;">Vehicle No.</th>
        <th style="width:80px;">Vehicle Type</th>
        <th style="width:80px;">Transported Rate</th>
        </tr>
        </thead><tbody>';
$trate=0;
	$restey=$this->db->select('a.transporter_id,a.vehicle_no,a.vehicle_type,b.mobile_no,b.name,a.transport_rate')->from('item_delivery_type_3 a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $rows);
$trate=$rows->transport_rate;
		$html.='<tr>

		<td style="padding:10px;">'.$rows->name.'</td>
		<td style="padding:10px;">'.$rows->mobile_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_no.'</td>
		<td style="padding:10px;">'.$rows->vehicle_type.'</td>
		<td style="padding:10px;font-weight:bold;">₹'.$rows->transport_rate.'</td>
		</tr>';



	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';
	}

	return $html."|".$trate;
}



function getTypeThreeTransportationDetails_without_amt($id,$transportation_rate)
{
	 $html='';
	 $t=array();
	 $t[]=0;
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
          <th style="width:30px;">Transporter Payment</th>
        
        </tr>
        </thead><tbody>';

$d=array();
	$restey=$this->db->select('a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_three_product_details a')->join('item_delivery_details_type_3 b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$transportation_rate;

			$html.='<tr>
			<td style="padding:10px;">'.$i.'</td>
			<td style="padding:10px;">'.$rows->instruments_name.'</td>
			<td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
			<td style="padding:10px;color:red;font-weight:bold;font-size:18px;">₹'.$tamount.'</td>
      
          
      </tr>';

      $t[]=$tamount;

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($t);


}


 function get_pending_transporter_payment_type3($start_date,$end_date,$transporter,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		  $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',0);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate_type_3($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);
					
					$credit_note_sum[]=$pdetails[1];
			}

		}


			
			return array_sum($credit_note_sum);
	


}



 function get_done_transporter_payment_type3($start_date,$end_date,$transporter,$location)
{


		$credit_note_sum = array();
		$credit_note_sum[] =0;
		$i=1;
		     
		      		  $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',1)
						  ->where('a.transport_owned_hired',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.paidOn >=', $start_date);
				 $this->db->where('c.paidOn <=', $end_date);
			}

			if($location != '' && $location != 'ALL') {
				 $this->db->where('a.hpcl_location', $location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate_type_3($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);
					
					$credit_note_sum[]=$pdetails[1];
			}

		}


			
			return array_sum($credit_note_sum);
	


}



function getTypeTwoApprovalDeliveryDetails($approval_id,$product) {
		$ht='';
		$this->db->select('a.pack_size,a.qty, c.instruments_name,b.approved_price,b.credit_vli,b.moq,b.price_validity')
		->from('item_delivery_details a')
					    ->join('type_two_product_details b','a.approval_detail_id=b.id')
					    ->join('presto_instruments c', 'c.id=b.product_id')
					    ->where('b.approval_id',$approval_id);
					    if($product<>'' && $product<>'ALL')
					    {
					    	$this->db->where('b.product_id',$product);
					    }

					    $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>QTY Delivered</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI</th>
				<th style='padding:10px;'>Minimum QTY</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {
				$shortname=$this->getUnitName($rows->pack_size);
				$ht.="<tr>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'><strong style='font-weight:bold;'>".$rows->qty."/".$shortname."</strong></td>
						<td style='padding:10px;'>".$rows->approved_price."/".$shortname."</td>
						<td style='padding:10px;'>".date('d-M-Y',strtotime($rows->price_validity))."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$shortname."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}
//echo $ht; exit;
		return $ht;
	}


function getTypeThreeApprovalDeliveryDetails($approval_id,$product) {
		$ht='';
		$this->db->select('a.pack_size,a.qty, c.instruments_name,b.approved_price,b.credit_vli,b.moq,b.price_validity')
		->from('item_delivery_details_type_3 a')
					    ->join('type_three_product_details b','a.approval_detail_id=b.id')
					    ->join('presto_instruments c', 'c.id=b.product_id')
					    ->where('b.approval_id',$approval_id);
					    if($product<>'' && $product<>'ALL')
					    {
					    	$this->db->where('b.product_id',$product);
					    }

					    $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>QTY Delivered</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI</th>
				<th style='padding:10px;'>Minimum QTY</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {
				$shortname=$this->getUnitName($rows->pack_size);
				$ht.="<tr>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'><strong style='font-weight:bold;'>".$rows->qty."/".$shortname."</strong></td>
						<td style='padding:10px;'>".$rows->approved_price."/".$shortname."</td>
						<td style='padding:10px;'>".date('d-M-Y',strtotime($rows->price_validity))."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$shortname."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}
//echo $ht; exit;
		return $ht;
	}


	function getTypeThreeTransportationDetails_without_amt_n_payment($id,$transportation_rate)
{
	 $html='';
	 $t=array();
	 $t[]=0;
        $html.='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Transported Qty</th>
         
        
        </tr>
        </thead><tbody>';

$d=array();
	$restey=$this->db->select('a.product_id,a.approved_price,b.pack_size,b.qty,d.instruments_name')->from('type_three_product_details a')->join('item_delivery_details_type_3 b','a.id=b.approval_detail_id')->join('presto_instruments d','a.product_id=d.id')->where('a.approval_id',$id)->get();
	if($restey->num_rows()>0)
	{
		$i=1;
		foreach($restey->result() as $rows)
		{
			$packsize=$this->getUnitName($rows->pack_size);
			$tamount=$rows->qty*$transportation_rate;

			$html.='<tr>
			<td style="padding:10px;">'.$i.'</td>
			<td style="padding:10px;">'.$rows->instruments_name.'</td>
			<td style="padding:10px;">'.$rows->qty.' '.$packsize.'</td>
			
      
          
      </tr>';

      $t[]=$tamount;

			$i++;
		}
	}else
	{
		$html.='<tr>
        <td style="padding:10px;" colspan="5">No Data Available</td>
   
      </tr>';

	}


	return $html."|".array_sum($t);


}


function total_visits($start_date,$end_date,$user)
{
	$this->db->select('id')->from('daily_visits')->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)));
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		$res=$this->db->get();

		$res1=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',$start_date." 00:00:00")->where('added_on<=',$end_date." 23:59:59")->where('added_by',$user)->get();
		


		return $res->num_rows()+$res1->num_rows();



}

function total_visits_scheduled($start_date,$end_date,$user)
{
	$this->db->select('id')->from('daily_visits')->where('converted',0)->where('followup_date>=',date('Y-m-d',strtotime($start_date)))->where('followup_date<=',date('Y-m-d',strtotime($end_date)));
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		$res=$this->db->get();
		return $res->num_rows();

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


function getApprovalProductDetails_combination($approval_id) {
	
	$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {

			$combdata=$this->getcombinationData($approval_id);
			if(count($combdata)>0)
			{
				$type=$combdata['type'];
				if($type==1)
				{
					$type_name="Combined MOQ";
					$moq_span=$query->num_rows();

				}else if($type==2)
				{
					$type_name="Combined VLI";
					$vli_span=$query->num_rows();
				}else if($type==3)
				{
					$type_name="Combined MOQ & VLI";
					$moq_span=$query->num_rows();
					$vli_span=$query->num_rows();
				}else
				{
					$type_name="";
					$moq_span=0;
					$vli_span=0;
				}
				$moq=$combdata['moq'];
				$vli=$combdata['vli'];
			}else
			{
				$type_name='';
				$moq=0;
				$vli=0;
				$moq_span=0;
				$vli_span=0;
			}

				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;' >Credit/VLI</th>
				<th style='padding:10px;'>Minimum Qty</th>
				</tr>
				</thead>
				<tbody>";
				$t=0;
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>";
						if($vli_span>0)
						{
							if($t==0)
							{
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$vli."/".$rows->unit."</td>";
							}
						}else
						{
								$ht.="<td style='padding:10px;'>".$vli."/".$rows->unit."</td>";
						}


						if($moq_span>0)
						{
						if($t==0)
						{
						$ht.="<td style='padding:10px;' rowspan='".$moq_span."'>".$moq." ".$rows->unit."</td>";
						}
						}else
						{
						$ht.="<td style='padding:10px;'>".$rows->moq." ".$unit."</td>";
						}


						
					$ht.="</tr>";
			$t++; 
				}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht;


	}

	function getcombinationData($approval_id)
	{
		$data=array();
		$restey=$this->db->select('moq,vli,type')->from('approval_combination_type_1')->where('approval_id',$approval_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$data['type']=$row->type;
			$data['moq']=$row->moq;
			$data['vli']=$row->vli;

		}

		return $data;

	}


	function months_purchase_generic($start,$end,$location,$product,$company)
	{
		$credit_note_sum=array();
		$credit_note_sum[]=0;
		$start_date=date('Y-m-d',strtotime($start));
		$end_date=date('Y-m-d',strtotime($end));

	 $this->db->select('a.gst,a.id, a.bill_no,a.currentdate,d.name as party')
						  ->from('inventory a')
						  ->join('hpcl_location d', 'd.id=a.party','left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.currentdate >=', $start_date);
				 $this->db->where('a.currentdate <=', $end_date);
			}

      if($location<>'' && $location<>'ALL')
      {
        $this->db->where('a.party',$location);
      }

      if($company<>'' && $company<>'ALL')
      {
        $this->db->where('a.hpcl_billing_company',$company);
      }

    	$query =  $this->db->get();
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				 $this->db->select('a.qty, a.rate')
									->from('inventory_details a')
									->where('a.inventory_id', $row->id);
										if($product<>'' && $product<>'ALL')
									{
										$this->db->where('a.product',$product);
									}
								$sql =	$this->db->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {
							$gst=$row->gst/100;
							$total=$rows->qty * $rows->rate;
							$gst_amount=$total*$gst;
							$grand_amount=$total+$gst_amount;
							$credit_note_sum[] =$grand_amount;

						}
					}
			}

		}

			return array_sum($credit_note_sum);

	}

	 function getTotalUserConveyance($user_id, $start_date, $end_date) {
		$finaltotal = array();
		$finaltotal[] = 0;

			 $this->db->select('a.*, b.first_name, b.last_name')
					  ->from('conveyance_voucher_view a')
					  ->join('system_users_view b','a.added_by=b.user_id','left');
			if($user_id != '') {
			 $this->db->where('a.added_by', $user_id);
			}

			if($start_date != '') {
			 $this->db->where('a.travel_date >=', $start_date);
			}


			if($end_date != '') {
			 $this->db->where('a.travel_date <=', $end_date);
			}
			
			$query = $this->db->order_by('a.travel_date','DESC')
	    					  ->where('a.hod_status','1')
	    					  ->where('a.account_status','1')
	    					  ->get();


		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

			$query1 = $this->db->select('a.amount, a.attachment, b.options, a.to_location, a.from_location')->from('local_conveyance_expense a')->join('conveyance_type_options b','a.expense_type=b.id','left')->where('a.conveyance_id',$row->id)->get();

				if($query1->num_rows() > 0) { 
					foreach($query1->result() as $record){
						
						$finaltotal[] = $record->amount;

						// $finaltotal[]  = array_sum($amount);
						
					} 
				} else {
					$md = "OWN";
					$parkingbills = "";
					$totalamount = $row->amount+$row->parking_charges;
					$finaltotal[] = $totalamount;
				}

			}
		}

		return array_sum($finaltotal);
	}

	function getAllUsers() {
		$res = '';
		$sql = $this->db->select('user_id, first_name, last_name')
						->from('system_users')
						->get();

		if ($sql->num_rows() > 0) {
				$res = $sql->result();
		} 

		return $res;
	}

	function getLeadInfo($lead_id) {
		 $res = '';
		 $query = $this->db->select('a.company_name, c.instruments_name, e.first_name, e.last_name, e.contact_number')
						   ->from('leads a')
						   ->join('trial_to_be_sent b', 'b.lead_id=a.id')
						   ->join('lead_products f','f.id=b.lead_product_id')
						   ->join('presto_instruments c','f.product_id=c.id')
						   ->join('lead_assigned_to_team_member d','d.lead_id=a.id')
						   ->join('system_users e','e.user_id=d.member_id')
						   ->where('a.id', $lead_id)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} 

		return $res;
	}

	function getTrialSentID($lead_id)
	{
		$res = '';
		$sql = $this->db->select('id')
						   ->from('trial_to_be_sent')
						   ->where('lead_id', $lead_id)
						   ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$res = $row->id;
		} 

		return $res;

	}

	function getOrderAmountWithGST($quotation, $buyer_gst, $seller_gst)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

			$total_gst = 0;

		    if($buyer_gst<>'' && $seller_gst <>'') {
		        $bscode = substr($buyer_gst,0,2);
		        $sscode = substr($seller_gst,0,2);

		        if($bscode == $sscode) {
            		$gst = ($total_price*9)/100;
            		$total_gst = $gst + $gst;
		        } else {
		        	$gst = ($total_price*18)/100;
		        	$total_gst = $gst;
        		}
        	} else {
		        $igst=0;
		        $cgst=0;
		    }

		    $final_amt = $total_price + $total_gst;

		    return $final_amt;
	}

	function getCustomerdetail($customerid)
	{
		$data=array();
		$r=$this->db->select('customer_name,company_name, order_max_limit,address')->from('customer_detail')->where('id',$customerid)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $row);
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
			$data[]=$row->order_max_limit;
			$data[]=$row->address;
		}

		return  $data;

	}

	function check_remove_hold($order_id) {
		$sql = $this->db->select('id')
						->from('order_remove_hold')
						->where('order_id', $order_id)
						->get();

		return $sql->num_rows();
	}

	function getHpclCompanies() {
		$res = '';
		$sql = $this->db->select('id, companyname')
						->from('store_rack_location')
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getHpclCompanyName($company_id) {
		$res = '';
		$sql = $this->db->select('companyname')
						->from('store_rack_location')
						->where('id', $company_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$res = $row->companyname;
			}
		}

		return $res;
	}

	function getOrderTotalQty($quotation)
	{
		
		  $shortname = '';
		  $total_qty=0;
		  $sql = $this->db->select('qty, pack_size')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();
				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row){
						$shortname = $this->getUnitName($row->pack_size);
						$total_qty += $row->qty;
						}
				}
		
			

			if($total_qty == 0) {
		    	$total = '';
			} else {
				$total = $total_qty.' '.$shortname;
			}

			return $total;

	}

	function getOrderRate($quotation)
	{
		
		  $price = '';
		  $shortname = '';
		  
		  $sql = $this->db->select('agreed_price, pack_size')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows() > 1) {
				$price = 0;
				$shortname = '';
			} else {
				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row);
						$shortname = $this->getUnitName($row->pack_size);
						$price = $row->agreed_price;
				}
			}
			

			if($price == 0) {
		    	$total = '';
			} else {
				$total = $price.'/'.$shortname;
			}

			return $total;
	}

	function getOrderAmountWithoutGST($quotation)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

		    return $total_price;
	}

	function getOrderGST($quotation, $buyer_gst, $seller_gst)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

			$total_gst = 0;

		    if($buyer_gst<>'' && $seller_gst <>'') {
		        $bscode = substr($buyer_gst,0,2);
		        $sscode = substr($seller_gst,0,2);
		        if($bscode == $sscode) {
	        		$cgst = ($total_price*9)/100;
	        		$sgst = ($total_price*9)/100;
	        		$igst = 0;
	        	} else {
	        		$cgst=0;
		        	$sgst=0;
	        		$igst = ($total_price*18)/100;
	        	}
        	} else {
        		$igst = 0;
		        $cgst=0;
		        $sgst=0;
		    }

		    return $cgst.'|'.$sgst.'|'.$igst;
	}

	function getunpaid_order_amount($customer_id)
	{
		$payment_due_sum=array();
		$query = $this->db->select('a.id,a.quotation_id,e.gst_no,c.gst')
		->from('order_punch a')
		->join('order_punch_mailing_details d','a.id=d.order_id')
		->join('order_punch_tax_details f','a.id=f.order_id')
		->join('lead_source g','g.source_id=a.source','left')
		->join('system_users h','h.user_id=a.agent')
		->join('order_punch_tax_details e','a.id=e.order_id')
		->join('customer_quotation b','b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id', 'left')
		->where('a.payment', 0)
		->where('b.customer_id',$customer_id)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$order_value=$this->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
				$partial=$this->customer_previous_payment($row->id);
				$payment_due=$order_value-$partial;
				$payment_due_sum[]=$payment_due;

			}
		}
		

		return array_sum($payment_due_sum);
	}

	function customer_previous_payment($order_id)
	{
		$recvd=array();
		$recvd[]=0;
		$Resteuy=$this->db->select('order_amount,recieved_amount')->from('customer_order_to_payments')->where('order_id',$order_id)->get();
		if($Resteuy->num_rows()>0){
			foreach($Resteuy->result() as $row)
			{
				$recvd[]=$row->recieved_amount;
			}
		}

		return array_sum($recvd);
	}

	function gettdsinput($startdate,$enddate,$user,$company)
	{

		$sdate=date('Y-m-d',strtotime($startdate))." 00:00:00";
		$edate=date('Y-m-d',strtotime($enddate))." 23:59:59";
		$user=$user;
		$company=$company;

			$lead_data = array();
			$lead_data[]=0;

			$this->db->select('a.customer_id,c.order_id,f.hpcl_billing_company,f.invoice_no,f.quotation_id as orderpunchquote,g.gst_no as buyer_gst,h.gst as seller_gst')->from('customer_order_to_payments c')->join('customer_payments a','a.id=c.payment_id')->join('customer_detail e','a.customer_id=e.id')->join('order_punch f','c.order_id=f.id')->join('order_punch_tax_details g','f.id=g.order_id') ->join('store_rack_location h', 'h.id=f.hpcl_billing_company','left')->where('a.addedOn>=',$sdate)->where('a.addedOn<=',$edate)->where('f.payment',1)->where('e.tds_appl',1)->group_by('order_id');

				if($user<>'ALL' && $user<>'')
				{
				$this->db->where('a.customer_id',$user);
				}

				$this->db->where('f.hpcl_billing_company',$company);

				$query=$this->db->get();
	
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;

		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;


				$data=$this->getCustomerdetail_for_order($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
					$tds_appl=$data[3];
					$tds_per=$data[4];
				}else
				{
					$customer_name='';
					$companyname='';
					$order_max_limit='';
					$tds_appl=0;
					$tds_per=0;

				}
			
		
			
			$j=1;
			
			

		

					$this_order_amount = $this->getOrderAmountWithGST($row->orderpunchquote, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->orderpunchquote, $row->buyer_gst, $row->seller_gst);
					$gst_amount = $this->getOrderGSTAmount($row->orderpunchquote, $row->buyer_gst, $row->seller_gst);
					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
											
					}else
					{
						
						$getorderamountaftertds=0;
					}
	
			$lead_data[] = $getorderamountaftertds;

			 			 


			$i++;
		}
		}

		return array_sum($lead_data);

	}


	function getCustomerdetail_for_order($customerid)
	{
		$data=array();
		$r=$this->db->select('customer_name,company_name, order_max_limit,tds_appl,tds_per')->from('customer_detail')->where('id',$customerid)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $row);
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
			$data[]=$row->order_max_limit;
			$data[]=$row->tds_appl;
			$data[]=$row->tds_per;
		}

		return  $data;

	}

	function getOrderGSTAmount($quotation, $buyer_gst, $seller_gst)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->agreed_price * $row->qty;
				}
			
			}

			$total_price = array_sum($price);

			$total_gst = 0;

		    if($buyer_gst<>'' && $seller_gst <>'') {
		        $bscode = substr($buyer_gst,0,2);
		        $sscode = substr($seller_gst,0,2);

		        if($bscode == $sscode) {
            		$gst = ($total_price*9)/100;
            		$total_gst = $gst + $gst;
		        } else {
		        	$gst = ($total_price*18)/100;
		        	$total_gst = $gst;
        		}
        	} else {
		        $igst=0;
		        $cgst=0;
		    }

		    $final_amt = $total_gst;

		    return $final_amt;
	}

	function getSellerGst($company_id) {
		$res = '';
		$sql = $this->db->select('gst')
						->from('store_rack_location')
						->where('id', $company_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$res = $row->gst;
			}
		}

		return $res;
	}


	function chkIfLocationExists($approval_id, $location) {
		$sql = $this->db->select('id')
					    ->from('approval_product_details')
			   			->where('location',$location)
			   			->where('approval_id',$approval_id)
			   			->get();

		return $sql->num_rows();
	}



	function getClaimgenerated_product_wise_single($approval_id,$vli,$moq,$location,$valid_from,$valid_to,$product,$start_date,$end_date,$approval_pack_size,$product_density,$product_pack_type)
	{

		$claim=0;
		$error='';
		$qty=array();
		$unitshortname=$this->getUnitName($approval_pack_size);
			$res=$this->db->select('a.inventory_id,a.qty,a.pack_size')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','c.id=b.party')->where('product',$product)->where('b.currentdate>=',$valid_from)->where('b.currentdate<=',$valid_to)->where('c.hpcl_location',$location)->get();
			if($res->num_rows()>0)
			{
				foreach($res->result() as $row)
				{
					$qty[]=$row->qty;
				}


			}
				
			
				if($moq>0)
				{
					if($product_pack_type=="BULK" && $approval_pack_size==5)
					{
					$converted_moq=$moq/$product_density;

					}else
					{
					$converted_moq=$moq;
					}
				}else
				{
					$converted_moq=0;
				}


				if(count($qty)>0)
				{
				$purchased_qty=array_sum($qty);
				}else
				{
				$purchased_qty=0;
				}

				if($purchased_qty>=$converted_moq)
				{

					$claim=$purchased_qty*$vli;

				}else
				{
					$diff=$converted_moq-$purchased_qty;
					$error="<strong style='color:red;font-weight:bold;'>You are ".$diff." ".$unitshortname." short of MOQ target. So no claim applicable for now.</strong>";
				}


				return $claim."|".$purchased_qty."|".$error; 
				

	}



	function getClaimgenerated_product_wise_combination($approval_id,$vli,$moq,$location,$valid_from,$valid_to,$product,$start_date,$end_date,$approval_pack_size,$product_density,$product_pack_type)
	{

		$claim=0;
		$error='';
	$qty=array();
	$rest=	$this->db->select('product,combination_id')->from('approval_combination_detail_type_1')->where('approval_id',$approval_id)->get();
	if($rest->num_rows()>0)
	{

		foreach($rest->result() as $rowss)
		{
			$combination_id=$rowss->combination_id;
		$product=$rowss->product;
		$unitshortname=$this->getUnitName($approval_pack_size);
			$res=$this->db->select('a.inventory_id,a.qty,a.pack_size')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','c.id=b.party')->where('product',$product)->where('b.currentdate>=',$valid_from)->where('b.currentdate<=',$valid_to)->where('c.hpcl_location',$location)->get();
			if($res->num_rows()>0)
			{
				foreach($res->result() as $row)
				{
					$qty[]=$row->qty;
				}


			}
		}

	}
				
			$combon=$this->getcombination_moq_vli($combination_id);
			$combodata=explode('|',$combon);
			$moq=$combodata[0];
			$vli=$combodata[1];
			
				if($moq>0)
				{
					if($product_pack_type=="BULK" && $approval_pack_size==5)
					{
					$converted_moq=$moq/$product_density;

					}else
					{
					$converted_moq=$moq;
					}
				}else
				{
					$converted_moq=0;
				}


				if(count($qty)>0)
				{
				$purchased_qty=array_sum($qty);
				}else
				{
				$purchased_qty=0;
				}

				if($purchased_qty>=$converted_moq)
				{

					$claim=$purchased_qty*$vli;

				}else
				{
					$diff=$converted_moq-$purchased_qty;
					$error="<strong style='color:red;font-weight:bold;'>You are ".$diff." ".$unitshortname." short of MOQ target. So no claim applicable for now.</strong>";
				}


				return $claim."|".$purchased_qty."|".$error; 
				

	}

	function getcombination_moq_vli($comboid)
	{
		$moq=0;
		$vli=0;
		$restey=$this->db->select('moq,vli')->from('approval_combination_type_1')->where('id',$comboid)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $rrrow);
			$moq=$rrrow->moq;
			$vli=$rrrow->vli;

		}

		return $moq."|".$vli;

	}



	function getApprovalProductDetails_for_claim($approval_id,$start_date,$end_date) {
		$ht='';
		// $query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
		// 			    ->from('approval_product_details a')
		// 			    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
		// 			   	->join('hpcl_location c', 'c.id=a.location')
		// 			    ->where('a.approval_id',$approval_id)
		// 			    ->get();

//->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)
					$totalclaim=array();
					$totalclaim[]=0;
					$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');
				$query=$this->db->where('a.approval_id',$approval_id)->get();
			if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;width:200px;'>Location</th>
				<th style='padding:10px;width:200px;'>Product Name</th>
				<th style='padding:10px;width:200px;'>Approved Price</th>
				<th style='padding:10px;width:200px;'>Price Validity</th>
				<th style='padding:10px;width:200px;'>Credit/VLI</th>
				<th style='padding:10px;width:200px;'>Minimum Qty</th>
				<th style='padding:10px;width:200px;'>Purchased Qty</th>
				<th style='padding:10px;width:200px;'>Claim Generated</th>
				<th style='padding:10px;width:200px;'>Error/Warning(s)</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

	
					$vli=$rows->credit_vli;
					$moq=$rows->moq;

					$claim=$this->salescrm->getClaimgenerated_product_wise_single($rows->approval_id,$vli,$moq,$rows->location,$rows->validity_from,$rows->validity_to,$rows->product_id,$start_date,$end_date,$rows->pack_size,$rows->density,$rows->pack_type);
					$c=explode("|",$claim);
					$claim_amount=$c[0];
					$purchase_qty=$c[1];
					$error_message=$c[2];

				$totalclaim[]=$claim_amount;

			
				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->shortname."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$rows->shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$rows->shortname."</td>
						<td style='padding:10px;'>".$purchase_qty." ".$rows->shortname."</td>
						<td style='padding:10px;'><strong style='color:red;font-weight:bold;font-size:18px;'>".$claim_amount."</strong></td>
						<td style='padding:10px;'>".$error_message."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($totalclaim);
	}


	function getApprovalProductDetails_combination_for_claim($approval_id,$start_date,$end_date) {
	
	//->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)
	$ht='';
	$total_claim_amount=array();
	$total_claim_amount[]=0;
			$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');
				$query=$this->db->where('a.approval_id',$approval_id)->get();

		if($query->num_rows()>0) {

			$combdata=$this->getcombinationData($approval_id);
			if(count($combdata)>0)
			{
				$type=$combdata['type'];
				if($type==1)
				{
					$type_name="Combined MOQ";
					$moq_span=$query->num_rows();

				}else if($type==2)
				{
					$type_name="Combined VLI";
					$vli_span=$query->num_rows();
				}else if($type==3)
				{
					$type_name="Combined MOQ & VLI";
					$moq_span=$query->num_rows();
					$vli_span=$query->num_rows();
				}else
				{
					$type_name="";
					$moq_span=0;
					$vli_span=0;
				}
				$moq=$combdata['moq'];
				$vli=$combdata['vli'];
			}else
			{
				$type_name='';
				$moq=0;
				$vli=0;
				$moq_span=0;
				$vli_span=0;
			}

				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;width:200px;'>Location</th>
				<th style='padding:10px;width:200px;'>Product Name</th>
				<th style='padding:10px;width:200px;'>Approved Price</th>
				<th style='padding:10px;width:200px;'>Price Validity</th>
				<th style='padding:10px;width:200px;' >Credit/VLI</th>
				<th style='padding:10px;width:200px;'>Minimum Qty</th>
				<th style='padding:10px;width:200px;'>Purchased Qty</th>
				<th style='padding:10px;width:200px;'>Claim Generated</th>
				<th style='padding:10px;width:200px;'>Error/Warning(s)</th>
				</tr>
				</thead>
				<tbody>";
				$t=0;
			foreach($query->result() as $rows) {


				$claim=$this->getClaimgenerated_product_wise_combination($rows->approval_id,$vli,$moq,$rows->location,$rows->validity_from,$rows->validity_to,$rows->product_id,$start_date,$end_date,$rows->pack_size,$rows->density,$rows->pack_type);
				$c=explode("|",$claim);
				$claim_amount=$c[0];
				$purchase_qty=$c[1];
				$error_message=$c[2];

				if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->shortname."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>";
						if($vli_span>0)
						{
							if($t==0)
							{
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$vli."/".$rows->shortname."</td>";
							}
						}else
						{
								$ht.="<td style='padding:10px;'>".$vli."/".$rows->shortname."</td>";
						}


						if($moq_span>0)
						{
						if($t==0)
						{
						$ht.="<td style='padding:10px;' rowspan='".$moq_span."'>".$moq." ".$rows->shortname."</td>";
						}
						}else
						{
						$ht.="<td style='padding:10px;'>".$rows->moq." ".$rows->shortname."</td>";
						}

						if($t==0)
						{
						$total_claim_amount[]=$claim_amount;
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$purchase_qty." ".$rows->shortname."</td>";
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'><strong style='color:red;font-weight:bold;font-size:18px;'>".$claim_amount."</strong></td>";
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$error_message."</td>";
						}


						
					$ht.="</tr>";
			$t++; 
				}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($total_claim_amount);


	}



	function this_month_claim_list() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$claim_amount=0;
		$purchase_qty=0;
		$error_message='';


		$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');

		$rows=$this->db->get();

		if($rows->num_rows()>0)
		{
			foreach($rows->result() as $row)
			{
				if($row->combination==0)
				{
					$app_type="Per Product Approval";
					$vli=$row->credit_vli;
					$moq=$row->moq;

					$claim=$this->salescrm->getClaimgenerated_product_wise_single($row->approval_id,$vli,$moq,$row->location,$row->validity_from,$row->validity_to,$row->product_id,$start_date,$end_date,$row->pack_size,$row->density,$row->pack_type);
					$c=explode("|",$claim);
					$claim_amount=$c[0];
					$purchase_qty=$c[1];
					$error_message=$c[2];

				}else
				{
					$vli=0;
					$moq=0;
					$app_type="Combination Approval";
					$r=$this->db->select('moq,vli')->from('approval_combination_type_1')->where('approval_id',$row->approval_id)->get();
					if($r->num_rows()>0)
					{
						foreach($r->result() as $rr);
						$vli=$rr->vli;
						$moq=$rr->moq;

					}

					$claim=$this->salescrm->getClaimgenerated_product_wise_combination($row->approval_id,$vli,$moq,$row->location,$row->validity_from,$row->validity_to,$row->product_id,$start_date,$end_date,$row->pack_size,$row->density,$row->pack_type);
					$c=explode("|",$claim);
					$claim_amount=$c[0];
					$purchase_qty=$c[1];
					$error_message=$c[2];
				}


		$data[] = array(
							'sr_no'=>$i,
							'approvalid'=>"TYPE-1".$row->auto_gen_code."<br/><strong style='color:red;'>".$app_type."</strong>",
							'location'=>$row->name,
							'product'=>$row->instruments_name,
							'validty'=>"<strong style=color:red;font-weight:bold;>".date('d-M-Y',strtotime($row->validity_from))."-".date('d-M-Y',strtotime($row->validity_to))."</strong>",
							'approved_price'=>$row->approved_price."/".$row->shortname,
							'creditnote'=>$vli."/".$row->shortname,
							'moq'=>floatval($moq)." ".$row->shortname,
							'purchase_qty'=>$purchase_qty." ".$row->shortname,
							'claim' =>"<strong style='color:red;font-weight:bold;font-size:19px;'>".$claim_amount."</strong>",
							'error'=>$error_message
						);
			}
		}
					
					
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}



	function this_month_claim_list_consolidated_amount($s,$e,$loc) {

		$data=array();
		$data[]=0;
		$interest_array=array();
		$interest_array[]=0;
		$start_date = date('Y-m-d', strtotime($s));
		$end_date = date('Y-m-d', strtotime($e));
		$hpcl_location =$loc;
			$reste=$this->db->select('e.hpcl_location,d.interest,d.payment_type,d.pur_payment,d.bill_no,e.name as vendor_name,d.gst as gstrate,d.currentdate,a.id, a.product,a.pack_size,a.qty,a.original_qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','a.inventory_id=d.id')->join('vendors e','d.party=e.id')->where('d.currentdate>=',$start_date)->where('d.currentdate<=',$end_date)->where('a.payment',1)->where('d.hpcl_billing_company',3)->get();
	if($reste->num_rows()>0)
	{
		$i=1;
		foreach($reste->result() as $row)
		{

			if($row->pur_payment==1)
				{
					$pstatus="FULLY PAID";
				}else
				{
					$pstatus='';
				}


				/** GST CALCULATION **/
				$total=$row->rate*$row->qty;
				$gstrate=$row->gstrate/100;
				$total_gst=$total*$gstrate;
				$grandtotal=$total+$total_gst;
				/** END **/

				if($row->payment_type==6)
				{
					$due_date=date('d-m-Y', strtotime($row->currentdate));
				}else if($row->payment_type==5 || $row->payment_type==4 )
				{
					$due_date=date('d-m-Y', strtotime($row->currentdate." +".$row->credit_days." Days"));
				}else
				{
					$due_date=date('d-m-Y', strtotime($row->currentdate));
				}

				$collection_details=$this->getcollection_details($row->id,$due_date,$row->interest,$row->rate,$row->product,$row->currentdate,$row->hpcl_location);

				$collections=explode('|',$collection_details);
				$interest_array[]=$collections[1];
				$approvals_data=$this->getApprovalForPurchase($row->product,$row->currentdate,$row->hpcl_location);
				$appdata=explode('|',$approvals_data);
				$app_id=$appdata[0];
				$app_rate=$appdata[1];
				$type=$appdata[2];
				$moq=$appdata[3];
				$credit=$appdata[4];
				/** NEW **/
				$credit=$row->rate-$app_rate+$credit;
				
				$validity_from=$appdata[5];
				$validity_to=$appdata[6];
			
				$errors='';
				if($type==0)
				{
					/** CHECK FOR MOQ **/
					if($moq>0)
					{
					$moq_fullfilled=$this->checkMOQFullfilled($validity_from,$validity_to,$moq,$row->product,$row->hpcl_location);
					$moq_full=explode("|",$moq_fullfilled);
				//	echo "<pre>"; print_r($moq_full); exit;
					if($moq_full[0]==1)
					{
					$claim=$row->qty*$credit;
				
					if($credit>0)
					{

					$final_claim=$claim;
					$without_intrest_claim=$final_claim;
					//echo $final_claim; exit;
					
					}else
					{
							$final_claim=0;
							$without_intrest_claim=$final_claim;
					}
					}else
					{
						$claim=0;
						$final_claim=0;
						$without_intrest_claim=$final_claim;
						$left=$moq-$moq_full[1];
						$errors="<strong style='color:red;font-weight:bold;'>".$left." LTR is short as per the MOQ to claim the Credit Note.</strong>";
					}


					}else
					{
					$claim=$row->qty*$credit;
					if($credit>0)
					{
					$final_claim=$claim;
					$without_intrest_claim=$final_claim;
					}else{
					$final_claim=0;
					$without_intrest_claim=$final_claim;
					}
					}

				}else
				{
					/** FOR COMBINATION **/
					
					// get combined partner 
					$combined_prd=$this->getcombinedProductDetails($row->product,$app_id);
					$comb_moq=$combined_prd[0];
					$comb_vli=$combined_prd[1];
					$credit=$comb_vli;
					$moq_criteria=$this->CheckCombinedMoqFullfilment($comb_moq,$comb_vli,$app_id,$row->product,$validity_from,$validity_to,$row->hpcl_location);
					$moqcriteria=explode("|",$moq_criteria);
					if($moqcriteria[0]==1)
					{
						//$pur_qty=$moqcriteria[1];
						$pur_qty=$row->qty;
					}else
					{
						$left=$comb_moq-$moqcriteria[1];
					
						$pur_qty=0;
						$errors="<strong style='color:red;font-weight:bold;'>Short QTY for products<br/><br/>". $moqcriteria[2]."<br/><br/>Qty Short-".$left." LTR</strong>";
					}

					$claim=$pur_qty*$comb_vli;
					if($claim>0)
					{
					$final_claim=$claim-$collections[1];
					$without_intrest_claim=$claim;
					}else{
						$final_claim=0;
						$without_intrest_claim=0;
					}	

				}


						$data[] =$without_intrest_claim;

		}

	}


//echo "<pre>"; print_r($data); exit;
	return array_sum($data)."|".array_sum($interest_array);
	}

	function this_month_claim_list_consolidated_amountOlddd($s,$e,$loc) {

		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($s));
		$end_date = date('Y-m-d', strtotime($e));
		$hpcl_location =$loc;
		

		         $this->db->select('a.combination,a.purchase_entry,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period')
						  ->from('approval_form a');
						//  ->join('hpcl_location b', 'b.id=a.hpcl_location');
						  // ->where('a.purchase_entry', 0);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			// if($hpcl_location != '' && $hpcl_location != 'ALL') {
			// 	 $this->db->where('a.hpcl_location', $hpcl_location);
			// }

		$this->db->order_by('a.current_date','DESC');
		$query = $this->db->get();

		$grandtotal_claim=array();
		$grandtotal_claim[]=0;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				
					if($row->combination==0)
						{
					$getApprovalProductDetails = $this->getApprovalProductDetails_for_claim($row->id,$start_date,$end_date);
					$cl_detail=explode('|',$getApprovalProductDetails);
						$prd_table=$cl_detail[0];
						$totalclaim=$cl_detail[1];
						}else
						{
					$getApprovalProductDetails=$this->getApprovalProductDetails_combination_for_claim($row->id,$start_date,$end_date);

						$cl_detail=explode('|',$getApprovalProductDetails);
						$prd_table=$cl_detail[0];
						$totalclaim=$cl_detail[1];
						}

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->chkIfProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }

			
						
					if($hpcl_location != '' && $hpcl_location != 'ALL') {
						$chkIflocationExists = $this->chkIfLocationExists($row->id, $hpcl_location);
					} else {
						$chkIflocationExists = 1;
					}


$chkIfProductExists=1;

					if($chkIfProductExists > 0 && $chkIflocationExists>0) {

						if($row->combination==0)
						{
							$app_type="Per Product Approval";
						}else
						{
							$app_type="Combination Approval";
						}
						
						$grandtotal_claim[]=$totalclaim;
					// $data[] = array(
					// 		'sr_no'=>$i,

					// 		'approvalid'=>date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-1'.$row->auto_gen_code."<br/><strong style='color:red;'>".$app_type."</strong>",
					// 		'product_details'=>$prd_table,
					// 		'total_claim'=>"<strong style='font-weight:bold;color:red;font-size:20px;'>".$totalclaim."</strong>",
					// 		// 'payment_terms'=>$payment_terms,
					// 		// 'credit_period'=>$days,
					// 		// 'edit'=>$edit,
					// 		// 'purchase_entry'=>$pdetail,
					// 		// 'csra_export' => $csra_export,
					// 		// 'credit_note_claim_amount'=>''
					// 	);
					$i++;
					}
				
				}
			}
			
			return array_sum($grandtotal_claim);
	}

	function get_purchase_details($id)
	{
		// <th style="width:80px;">Lot No.</th>
       // <th style="width:100px;">Batch No.</th>
       
        //<th style="width:80px;">Manufacturing Date.</th>
		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
        <th style="width:80px;">Rate</th>
       
        <th style="width:80px;">Total Price</th>
        </tr>
        </thead><tbody>';

        $tot=array();
        $tot[]=0;
		$reste=$this->db->select('a.id, a.product,a.pack_size,a.qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->where('a.inventory_id',$id)->get();
		if($reste->num_rows()>0)
		{
			$i=1;
			foreach($reste->result() as $rows)
			{

				$batch_code = array();
				$report_file = '';

				$sql = $this->db->select('batch_no, report_file')
								->from('inventory_batch_no')
								->where('inv_detail_id', $rows->id)
								->get();


				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row) {
						$batch_code[] = $row->batch_no;

						if($row->report_file <> '') {
							$report_file .= '<a href="'.assets_url.'test_report/'.$row->report_file.'" download>Download</a><br>';
						} else {
							$report_file .= '';
						}
					}
				}

				// echo "<pre>";print_r($batch_code);exit;

				$total=$rows->qty*$rows->rate;
					$html.='<tr>
					<td>'.$i.'</td>
					<td>'.$rows->instruments_name.'</td>
					<td>'.$rows->qty.' '.$rows->shortname.'</td>
					<td>'.$rows->rate.'/'.$rows->shortname.'</td>
					
					<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$total.'</strong></td>
					</tr>';

					$tot[]=$total;
			$i++;
		}


				//<td>'.$rows->lot_no.'</td>
				//<td>'.implode(',<br>', $batch_code).'</td>

				//<td>'.date('d-M-Y',strtotime($rows->manufacturing_date)).'</td>
		}else
		{
			$html.='<tr>
					<td colspan="8">No Product Available</td>
					</tr>';
		}

		$html.='</tbody>
  </table>';

  				return $html."|".array_sum($tot);

	}


	function total_due_payment($vendor,$start_date,$end_date)
	{
		$data=array();
		$data[]=0;
		$i=1;
		$party=$vendor;
		//$product=$this->uri->segment(6);
		         $this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.gst')
		         		  ->from('inventory a')
						  ->join('vendors d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}
						$this->db->where('a.currentdate>=',date('Y-m-d',strtotime($start_date)));
						$this->db->where('a.currentdate<=',date('Y-m-d',strtotime($end_date)));
						$this->db->where_in('a.payment_type','5,6',false);
						$this->db->where_in('d.hpcl',1);
						$this->db->where('a.pur_payment',0);


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				

						
						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {
				$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);

				if($row->payment_type==2)
						{
							$payment="Cash";
							$cr_days='';

						}else if($row->payment_type==3)
						{
								$payment="Online";
								$cr_days='';

						}else if($row->payment_type==4)
						{

							$payment="PDC";
							$cr_days='';
						}else if($row->payment_type==5)
						{
							$payment="Credit";
							$cr_days=$row->credit_days;
						}else  
						{
							$payment="Advance";
							$cr_days=$row->credit_days;
						}


						// if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
						$gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;
            $prev_payment=$this->check_for_previous_payment($row->id);
						$data[] = $grand_amt-$prev_payment;
						$i++;
					//}
					
						
					}

				}

		return array_sum($data);
	}

	function get_gst_slab()
	{
		$gst=0;
	$r=$this->db->select('gst_per')->from('gst_slab')->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $rr);
			$gst=$rr->gst_per;
		}

		return $gst;
	}


	function get_interest_slab()
	{
		$gst=0;
	$r=$this->db->select('interest_per')->from('payment_interest')->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $rr);
			$gst=$rr->interest_per;
		}

		return $gst;
	}



	function payment_late_with_interestOlddd($vendor,$s,$e)
	{

		$party=$vendor;
		$data = array();
		$data[]=0;
		$data = array();

		$i=1;
		         $this->db->select('a.gst,a.interest,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.pur_paymentOn')
		         		  ->from('inventory a')
						  ->join('hpcl_location d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}

							if($s<>'' && $e<>'')
						{
							$this->db->where('a.pur_paymentOn>=',$s." 00:00:00");
							$this->db->where('a.pur_paymentOn<=',$e." 23:59:59");
						}
						$this->db->where_in('a.payment_type','5,6',false);
						$this->db->where('a.pur_payment',1);


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);
					$action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';

						if($row->payment_type==2)
						{
							$payment="Cash";
							$cr_days='';

						}else if($row->payment_type==3)
						{
								$payment="Online";
								$cr_days='';

						}else if($row->payment_type==4)
						{

							$payment="PDC";
							$cr_days='';
						}else if($row->payment_type==5)
						{
							$payment="Credit";
							$cr_days=$row->credit_days;
						}else  
						{
							$payment="Advance";
							$cr_days=$row->credit_days;
						}



						$diff = abs(strtotime($row->pur_paymentOn) - strtotime(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))));
						$days=round($diff / (60 * 60 * 24));

					
						
						if(date('Y-m-d',strtotime($row->pur_paymentOn))>date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {

							$gst_slab=$row->gst/100;
							$gst_amount=$pdetails[1]*$gst_slab;
							$total=$pdetails[1]+$gst_amount;

							/** CACULATE INTEREST **/
							$interest=$row->interest;
							$interest=$interest/100;
							$calculate_int=($total*$interest)/365;
							$calculate_int=round($calculate_int*$days,2);
							/** END **/
							$data[] = $calculate_int;
					
					}
					//}
						
					}
				
				
			}


			return array_sum($data);

		
		
	}




	function payment_late_with_interest($vendor,$s,$e)
	{

		$data = array();
$data[]=0;
		$i=1;
		$party=$vendor;
		$start_date=date('Y-m-d',strtotime($s));
		$end_date=date('Y-m-d',strtotime($e));
		$data = array();
		$i=1;
		         $this->db->select('a.gst,a.interest,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,b.pur_paymentOn')->from('inventory_payment_details b')
		         		  ->join('inventory a','a.id=b.inventory_id')
						  ->join('hpcl_location d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}
						$this->db->where_in('a.payment_type','5,6',false);
						//	$this->db->where('a.pur_payment',1);

						if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
						{
							$this->db->where('b.pur_paymentOn>=',$start_date);
							$this->db->where('b.pur_paymentOn<=',$end_date);
						}


						$this->db->group_by('a.id');
		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);
					//echo "<pre>"; print_r($pdetails); exit; 
					$action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';

						if($row->payment_type==2)
						{
							$payment="Cash";
							$cr_days='';

						}else if($row->payment_type==3)
						{
								$payment="Online";
								$cr_days='';

						}else if($row->payment_type==4)
						{

							$payment="PDC";
							$cr_days='';
						}else if($row->payment_type==5)
						{
							$payment="Credit";
							$cr_days=$row->credit_days;
						}else  
						{
							$payment="Advance";
							$cr_days=$row->credit_days;
						}

			

						$late_pay=$this->check_for_any_payment_late(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days")),$row->id);
					
						if($late_pay>0)
						{
							
							$gst_slab=$row->gst/100;
							$gst_amount=$pdetails[1]*$gst_slab;
							$total=$pdetails[1]+$gst_amount;
					

								$exceeded_payments=$this->check_for_date_exceeded_payments_for_interest(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days")),$row->id,$row->gst,$row->interest);
							$pdata=	explode("|",$exceeded_payments);
					

							$data[] = $pdata[1];											

		

						$i++;
					}
							
					}
				
				
			}

		


					return array_sum($data);
		

		
		
	}




				function check_for_date_exceeded_payments_for_interest($payment_date,$inventory_id,$gst,$interest_per)
			{

$total_data=array();
$total_data[]=0;
							$html='<table class="table table-bordered">
					<thead>
					<tr>
					<th style="width:20px;">Payment Date</th>
					<th style="width:100px;">Collection ID</th>
					<th style="width:30px;">Collection Amount</th>
					<th style="width:80px;">Payment Type</th>
					<th style="width:80px;">Overdue Days</th>
					<th style="width:80px;">Interest</th>
					
					</tr>
					</thead><tbody>';

					$reste=$this->db->select('a.*,b.collection_id as collection_no,c.first_name,c.last_name')->from('inventory_payment_details a')->join('sunder_collection_reference b','a.collection_id=b.id')->join('system_users c','a.addedBy=c.user_id')->where('a.inventory_id',$inventory_id)->get();
					if($reste->num_rows()>0)
					{
						$i=1;
						foreach($reste->result() as $row)
						{
							if($row->payment_type==1)
							{
								$t="NEFT/IMPS";
								$cheque_no='';
								$cheque_date='';
							}else if($row->payment_type==2)
							{
								$t="Cheque";
								$cheque_no=$row->cheque_no;
								$cheque_date=date('d-M-Y',strtotime($row->cheque_date));
							}else
							{
								$t="Cash";
								$cheque_no='';
								$cheque_date='';
							}
 

 								$diff = abs(strtotime($row->pur_paymentOn) - strtotime(date('Y-m-d',strtotime($payment_date))));
								$days=round($diff / (60 * 60 * 24));

								$gst_slab=$gst/100;
								$gst_amount=$row->collection_amount*$gst_slab;
								$total=$row->collection_amount+$gst_amount;
								$total=$row->collection_amount;
								/** CACULATE INTEREST **/
								$interest=$interest_per;
								$interest=$interest/100;
								$calculate_int=($total*$interest)/365;
								$calculate_int=round($calculate_int*$days,2);
								/** END **/

								$total_data[]=$calculate_int;

								$html.='<tr>
								<td><strong style="color:red;font-weight:bold;font-size: 18px;">'.date('d-m-Y',strtotime($row->pur_paymentOn)).'</strong></td>
								<td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
								<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
								<td>'.$t.'</td>
								<td>'.$days.'</td>
								<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$calculate_int.'</strong></td>
							
								</tr>';
							$i++;

						//	}
					}
					}else
					{

								$html.='<tr>
								<td colspan="6"></td>
								</tr>';

					}



					return $html."|".array_sum($total_data);

			}


	function check_for_any_payment_late($final_payment_date,$inventory_id)
			{
					$restey=$this->db->select('id')->from('inventory_payment_details')->where('pur_paymentOn>',$final_payment_date)->where('inventory_id',$inventory_id)->get();

					return $restey->num_rows();

			}

		function getApprovalProductDetails_for_claim_for_excel($approval_id,$start_date,$end_date) {
		$ht='';
		// $query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
		// 			    ->from('approval_product_details a')
		// 			    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
		// 			   	->join('hpcl_location c', 'c.id=a.location')
		// 			    ->where('a.approval_id',$approval_id)
		// 			    ->get();

//->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)
					$totalclaim=array();
					$totalclaim[]=0;
					$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');
				$query=$this->db->where('a.approval_id',$approval_id)->get();
			if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;width:200px;'>Location</th>
				<th style='padding:10px;width:200px;'>Product Name</th>
				<th style='padding:10px;width:200px;'>Approved Price</th>
				<th style='padding:10px;width:200px;'>Price Validity</th>
				<th style='padding:10px;width:200px;'>Credit/VLI</th>
				<th style='padding:10px;width:200px;'>Minimum Qty</th>
				<th style='padding:10px;width:200px;'>Purchased Qty</th>
				<th style='padding:10px;width:200px;'>Claim Generated</th>
				<th style='padding:10px;width:200px;'>Error/Warning(s)</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

	
					$vli=$rows->credit_vli;
					$moq=$rows->moq;

					$claim=$this->salescrm->getClaimgenerated_product_wise_single($rows->approval_id,$vli,$moq,$rows->location,$rows->validity_from,$rows->validity_to,$rows->product_id,$start_date,$end_date,$rows->pack_size,$rows->density,$rows->pack_type);
					$c=explode("|",$claim);
					$claim_amount=$c[0];
					$purchase_qty=$c[1];
					$error_message=$c[2];

				$totalclaim[]=$claim_amount;
				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->shortname."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$rows->shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$rows->shortname."</td>
						<td style='padding:10px;'>".$purchase_qty." ".$rows->shortname."</td>
						<td style='padding:10px;'><strong style='color:red;font-weight:bold;font-size:18px;'>".$claim_amount."</strong></td>
						<td style='padding:10px;'>".$error_message."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($totalclaim);
	}


		function getLastInsertedCode_TYPE_2() {
		$unique_no = '001';
		$sql = $this->db->select('auto_gen_code')
						->from('type_two_approval')
						->order_by('id', 'desc')
						->limit(1)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row);
			$code = $row->auto_gen_code;
			$unique_no = str_pad($code+1, 3, '0', STR_PAD_LEFT);
		}

		return $unique_no;
	}



		function getApprovalProductDetails_type_two($approval_id,$hpcl_location,$product,$transport_type) {
		$ht='';
		$query=$this->db->select('a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate')
					    ->from('approval_product_details_type_two a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id);

					    if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    {
					    	$this->db->where('a.location',$hpcl_location);
					    }
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }

					    if($transport_type<>'ALL' && $transport_type<>'')
					    {
					    	$this->db->where('a.transport_type',$transport_type);
					    }

					  $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;'>Credit Days</th>
				<th style='padding:10px;'>CFA Commision</th>
				<th style='padding:10px;'>Transport Type</th>
				<th style='padding:10px;'>Transport Rate</th>
				<th style='padding:10px;'>Annexture Details</th>
		
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				if($rows->transport_type==1)
				{	
					
					$transport="EXMI";
				
					$trate=$rows->transport_rate."/".$rows->unit;

				}else
				{
					$transport="Delivered";
						$trate='-';
					
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_days." days</td>
						<td style='padding:10px;'>".$rows->commision."/".$rows->unit."</td>
						<td style='padding:10px;'>".$transport."</td>
						<td style='padding:10px;'>".$trate."</td>
						<td style='padding:10px;'>".$rows->annexure."<br/><a href='".page_url1."/type_two_annexure/".$rows->annexure_upload."'>Download</a></td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NA";
		}


//echo $ht; exit;
		return $ht;
	}



  function get_months_credit_claim_amount_type2($start_date,$end_date,$location,$product)
{

$credit_note_sum = array();
$credit_note_sum[] =0;

$data = array();
		$i=1;
		$start_date = $start_date;
		$end_date =$end_date;
		$hpcl_location =$location;
		$product = $product;
$query=$this->db->select('a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('a.delivered',1)
					    ->where('a.payment',1);

					    if($start_date<>'' && $end_date<>'')
					    {
					    	$this->db->where('d.current_date >=',$start_date);
					    	$this->db->where('d.current_date <=',$end_date);
					    }

					    if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    {
					    	$this->db->where('a.location',$hpcl_location);
					    }
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }
					   

					  $query=$this->db->get();

		if($query->num_rows()>0) {

				
			foreach($query->result() as $rows)
			{
					
				

					if($rows->transport_type==1)
					{	

					$transport="EXMI";
					$trate='-';
					$transport_commission=0;

					}else
					{
					$transport="Delivered";
					$trate=$rows->transport_rate;
					$transport_commission=$trate*$rows->deliveredQty;
					}

		 
			$cfa_commission=$rows->deliveredQty*$rows->commision;
			$total=$cfa_commission;
			$credit_note_sum[] =$total;
			}
						

			}
				



return array_sum($credit_note_sum);
	

}


  function get_months_credit_transportation_claim_amount_type2($start_date,$end_date,$location,$product)
{

$credit_note_sum = array();
$credit_note_sum[] =0;

$data = array();
		$i=1;
		$start_date = $start_date;
		$end_date =$end_date;
		$hpcl_location =$location;
		$product = $product;
$query=$this->db->select('a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('a.delivered',1)
					    ->where('a.payment',1);

					    if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    {
					    	$this->db->where('a.location',$hpcl_location);
					    }
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }
					   

					  $query=$this->db->get();

		if($query->num_rows()>0) {

				
			foreach($query->result() as $rows)
			{
					
				

					if($rows->transport_type==1)
					{	

					$transport="EXMI";
					$trate='-';
					$transport_commission=0;

					}else
					{
					$transport="Delivered";
					$trate=$rows->transport_rate;
					$transport_commission=$trate*$rows->deliveredQty;
					}

		 
			$cfa_commission=$rows->deliveredQty*$rows->commision;
			$total=$transport_commission;
			$credit_note_sum[] =$total;
			}
						

			}
				



return array_sum($credit_note_sum);
	

}

function get_payment_interest_type_2($start_date,$end_date)
{
		$data = array();
		$data[]=0;
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));


$query=$this->db->select('a.gst,a.interest_charges,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment,a.paymentOn,a.paymentBy,a.payment_evidence')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('a.delivered',1)
					    ->where('a.payment',1)
						->where('a.paymentOn>DATE_ADD(a.deliveredOn, INTERVAL a.credit_days DAY)')
						->where('a.paymentOn>=',$start_date)
						->where('a.paymentOn<=',$end_date);
						 $query=$this->db->get();

		if($query->num_rows()>0) {

				
			foreach($query->result() as $rows)
			{
							
					$amount=$rows->approved_price*$rows->deliveredQty;
					$duedate=date('Y-m-d',strtotime($rows->deliveredOn ."+".$rows->credit_days." Days"));
					$paymentOn=date('Y-m-d',strtotime($rows->paymentOn));
					$datediff = strtotime($paymentOn) - strtotime($duedate);
					$days=round($datediff / (60 * 60 * 24));
					$days=round($datediff / (60 * 60 * 24));
					$gst_slab=$rows->gst/100;
						$gst_amount=$amount*$gst_slab;
						$total=$amount+$gst_amount;
						/** CACULATE INTEREST **/
						$interest=$rows->interest_charges;
						$interest=$interest/100;
						$calculate_int=($total*$interest)/365;
						$calculate_int=round($calculate_int*$days,2);
						/** END **/
						$data[] = round($calculate_int,1);
	
					
			}
						

			}
				
			

			return array_sum($data);
	
		
}


function get_trasnporter_tds($tid){

			$rr='';
			$reoe=$this->db->select('tds')->from('transporter_details')->where('id',$tid)->get();
			if($reoe->num_rows()>0)
			{
				foreach($reoe->result() as $rowss);
				$rr=$rowss->tds;
			}

			return $rr;
		}


		function transportation_based_approval_ProductDetails($approval_id,$hpcl_location,$product) {
		$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,a.transport_type,a.transport_rate,a.deliveredQty')
					    ->from('transportation_based_aprroval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					  
					    ->where('a.approval_id',$approval_id);

					 
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }

					 
					  $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>

				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Qty</th>
				</tr>
				</thead>
				<tbody>";
				$qty=array();
				$qty[]=0;
			foreach($query->result() as $rows) {

				 $unit=$this->getUnitName($rows->pack_size);

			
			$qty[]=$rows->deliveredQty; 

				$ht.="<tr>
		
						<td style='padding:10px;'>".$rows->instruments_name."</td>
								<td style='padding:10px;'>".floatval($rows->deliveredQty)." ".$unit."</td>
						
					</tr>";
			}


			$ht.="<tr>
		
						<td style='padding:10px;'>Total QTY</td>
								<td style='padding:10px;'>".array_sum($qty)." ".$unit."</td>
						
					</tr>";


			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NA";
		}


//echo $ht; exit;
		return $ht;
	}



		function transportation_based_approval_Productclaim($approval_id,$hpcl_location,$product,$rate) {
		$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,a.transport_type,a.transport_rate,a.deliveredQty')
					    ->from('transportation_based_aprroval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					  
					    ->where('a.approval_id',$approval_id);

					 
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }

					 
					  $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>

				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Qty</th>
				</tr>
				</thead>
				<tbody>";
				$qty=array();
				$qty[]=0;
			foreach($query->result() as $rows) {

				 $unit=$this->getUnitName($rows->pack_size);

			

				$ht.="<tr>
		
						<td style='padding:10px;'>".$rows->instruments_name."</td>
								<td style='padding:10px;'>".$rows->deliveredQty." ".$unit."</td>
						
					</tr>";
					$qty[]=$rows->deliveredQty;
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NA";
		}


//echo $ht; exit;
		return array_sum($qty)*$rate;
	}


	function get_transportation_based_all_claim($start_date,$end_date,$location,$product)
	{
		$data = array();
		$claim_data = array();
		$claim_data[]=0;
		$i=1;
		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));
		$hpcl_location = $location;
		$product = $product;


		         $this->db->select('c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location c','a.hpcl_location=c.id')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location<>'' && $hpcl_location<>'ALL')
			{
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

		

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
													
		

					$getApprovalProductDetails = $this->transportation_based_approval_ProductDetails($row->id,$hpcl_location,$product);
				
 					if($getApprovalProductDetails!="NA") {
						
						if($row->tapproval_type==1)
						{
							$tap="Per Ltr";
						}else
						{
							$tap="Fixed Amount";
						}

						$tapp_rate=$row->tapproval_rate;

						if($row->transport_done_by==1)
						{
							$transport="Our Vehicle";
							$transporter_name=$row->vehicle_no;
							$transporter_type='';
							$transported_fixed_rate='';
						}else
						{
							$transport="Hired Vehicle";
							$transporter_name=$this->get_trasnporter_name($row->transporter_id);
							if($row->transporter_rate_type==1)
							{
								$transporter_type="Per Ltr";
								$transported_fixed_rate=$row->transporter_rate_fixed;
							}else
							{
								$transporter_type="Fixed Rate";
								$transported_fixed_rate=$row->transporter_rate_fixed;

							}
							
							
							

						}

					if($row->tapproval_type==1)
					{
					$claim = $this->transportation_based_approval_Productclaim($row->id,$hpcl_location,$product,$tapp_rate);
					}else
					{
						$claim=$tapp_rate;
					}

					$claim_data[] = $claim;
					$i++;
					}
				
				}
			}
			

			return array_sum($claim_data);


	}

	function get_trasnporter_name($tid){

			$rr='';
			$reoe=$this->db->select('name')->from('transporter_details')->where('id',$tid)->get();
			if($reoe->num_rows()>0)
			{
				foreach($reoe->result() as $rowss);
				$rr=$rowss->name;
			}

			return $rr;
		}

		function get_all_transporter_pending_payment($transpoter)
		{

						$data = array();
						$i=1;
		        $this->db->select('c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location c','a.hpcl_location=c.id')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');
							$this->db->where('a.payment',0);
							$this->db->where('a.transport_done_by',2);
							if($transpoter<>'ALL' && $transpoter<>'')
							{
							$this->db->where('a.transporter_id',$transpoter);
							}

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
										
					$getApprovalProductDetails = $this->transportation_based_approval_ProductDetails($row->id,"ALL","ALL");
				
 					if($getApprovalProductDetails!="NA") {
						
						if($row->tapproval_type==1)
						{
							$tap="Per Ltr";
						}else
						{
							$tap="Fixed Amount";
						}

						$tapp_rate=$row->tapproval_rate;

						$tds=0;
						if($row->transport_done_by==1)
						{
							$transport="Our Vehicle";
							$transporter_name=$row->vehicle_no;
							$transporter_type='';
							$transported_fixed_rate='';
						}else
						{
							$transport="Hired Vehicle";
							$transporter_name=$this->get_trasnporter_name($row->transporter_id);
							$tds=$this->get_trasnporter_tds($row->transporter_id);
							if($row->transporter_rate_type==1)
							{
								$transporter_type="Per Ltr";
								$transported_fixed_rate=$row->transporter_rate_fixed;

							}else
							{
								$transporter_type="Fixed Rate";
								$transported_fixed_rate=$row->transporter_rate_fixed;

							}
							
							
							

						}

					if($row->transporter_rate_type==1)
					{

					$claim = $this->transportation_based_approval_Productclaim($row->id,"ALL","ALL",$transported_fixed_rate);
					}else
					{
						$claim=$transported_fixed_rate;
					}

					$tds_amount=0;
					if($tds>0)
					{
						$td=$tds/100;
						$tds_amount=$claim*$td;
						$final_claim=$claim-$tds_amount;
					}else
					{
						$final_claim=$claim;	
					}
		
					$data[] = $final_claim;
	
					}
				
				}
			}


			return array_sum($data);
			
		
		}

		function getTransportationBasedApproval($id) {
			$res = '';
			$sql = $this->db->select('b.customer_name, b.customer_code, b.tds')
							->from('transportation_based_approval a')
						    ->join('hpcl_direct_customer b','b.id=a.customer_name')
						    ->where('a.id', $id)
						    ->get();

			if($sql->num_rows() > 0) {
				$res = $sql->result();
			}

			return $res;
		}

	function get_transportation_based_approval_details($start_date,$end_date) {
		$res = '';
		$i=1;
		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));


		         $this->db->select('id')
						  ->from('transportation_based_approval');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('current_date >=', $start_date);
				 $this->db->where('current_date <=', $end_date);
			}

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			$res = $query->result();
		}

		return $res;
	}


 function getEditCombinationProduct($approval_id) {
    $res = '';
    $query = $this->db->select('a.*,b.*,b.id as comb_detail_id,c.instruments_name')
                  ->from('approval_combination_type_1 a')
                  ->join('approval_combination_detail_type_1 b','b.combination_id = a.id','left')
                  ->join('presto_instruments c', 'c.id=b.product' ,'left')
                  ->where('a.approval_id',$approval_id)
                  ->get();

     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }



 function getClaimgenerated_product_wise_combination_product_wise($approval_id,$vli,$moq,$location,$valid_from,$valid_to,$product,$start_date,$end_date,$approval_pack_size,$product_density,$product_pack_type)
  {

    $claim=0;
    $error='';
  $qty=array();
  $rest=  $this->db->select('product,combination_id')->from('approval_combination_detail_type_1')->where('approval_id',$approval_id)->where('product',$product)->get();
  if($rest->num_rows()>0)
  {
// print_r($rest->result());exit;
    foreach($rest->result() as $rowss)
    {
      $combination_id=$rowss->combination_id;
    $product=$rowss->product;
    $unitshortname=$this->getUnitName($approval_pack_size);
      $res=$this->db->select('a.inventory_id,a.qty,a.pack_size')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','c.id=b.party')->where('product',$product)->where('b.currentdate>=',$start_date)->where('b.currentdate<=',$end_date)->where('c.hpcl_location',$location)->get();
      if($res->num_rows()>0)
      {
        foreach($res->result() as $row)
        {
          $qty[]=$row->qty;
        }


      }
    }

  }
  // print_r($qty);exit;
        
     $combon=$this->getcombination_moq_vli($combination_id);
      $combodata=explode('|',$combon);
      $moq=$combodata[0];
      $vli=$combodata[1];
      
        if($moq>0)
        {
          if($product_pack_type=="BULK" && $approval_pack_size==5)
          {
          $converted_moq=$moq/$product_density;

          }else
          {
          $converted_moq=$moq;
          }
        }else
        {
          $converted_moq=0;
        }

// echo $converted_moq ; exit();
        if(count($qty)>0)
        {
        $purchased_qty=array_sum($qty);
        }else
        {
        $purchased_qty=0;
        }

          $claim=$purchased_qty*$vli;

    


        return $claim."|".$purchased_qty; 
      
     


        // if(count($qty)>0)
        // {
        // $purchased_qty=array_sum($qty);
        // }else
        // {
        // $purchased_qty=0;
        // }

        


        // return $purchased_qty;
        

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


  function getApprovalProductDetails_for_claim_for_export($approval_id,$start_date,$end_date) {
		$ht='';
		// $query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
		// 			    ->from('approval_product_details a')
		// 			    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
		// 			   	->join('hpcl_location c', 'c.id=a.location')
		// 			    ->where('a.approval_id',$approval_id)
		// 			    ->get();

//->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)
					$totalclaim=array();
					$totalclaim[]=0;
					$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');
				$query=$this->db->where('a.approval_id',$approval_id)->get();
			if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;width:200px;'>Location</th>
				<th style='padding:10px;width:200px;'>Product Name</th>
				<th style='padding:10px;width:200px;'>Approved Price</th>
				<th style='padding:10px;width:200px;'>Price Validity</th>
				<th style='padding:10px;width:200px;'>Credit/VLI</th>
				<th style='padding:10px;width:200px;'>Minimum Qty</th>
				<th style='padding:10px;width:200px;'>Purchased Qty</th>
				<th style='padding:10px;width:200px;'>Claim Generated</th>
				<th style='padding:10px;width:200px;'>Error/Warning(s)</th>
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

	
					$vli=$rows->credit_vli;
					$moq=$rows->moq;

					$claim=$this->salescrm->getClaimgenerated_product_wise_single($rows->approval_id,$vli,$moq,$rows->location,$rows->validity_from,$rows->validity_to,$rows->product_id,$start_date,$end_date,$rows->pack_size,$rows->density,$rows->pack_type);
					$c=explode("|",$claim);
					$claim_amount=$c[0];
					$purchase_qty=$c[1];
					$error_message=$c[2];

				$totalclaim[]=$claim_amount;

			
				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->shortname."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$rows->shortname."</td>
						<td style='padding:10px;'>".$rows->moq." ".$rows->shortname."</td>
						<td style='padding:10px;'>".$purchase_qty." ".$rows->shortname."</td>
						<td style='padding:10px;'><strong style='color:red;font-weight:bold;font-size:18px;'>".$claim_amount."</strong></td>
						<td style='padding:10px;'>".$error_message."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($totalclaim);
	}


		function getApprovalProductDetails_combination_for_claim_for_export($approval_id,$start_date,$end_date) {
	
	//->where('a.validity_from>=',$start_date)->where('a.validity_from<=',$end_date)
	$ht='';
	$total_claim_amount=array();
	$total_claim_amount[]=0;
			$this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->join('hpcl_location c','c.id=a.location')->join('presto_instruments d','d.id=a.product_id')->join('units e','a.pack_size=e.id');
				$query=$this->db->where('a.approval_id',$approval_id)->get();

		if($query->num_rows()>0) {

			$combdata=$this->getcombinationData($approval_id);
			if(count($combdata)>0)
			{
				$type=$combdata['type'];
				if($type==1)
				{
					$type_name="Combined MOQ";
					$moq_span=$query->num_rows();

				}else if($type==2)
				{
					$type_name="Combined VLI";
					$vli_span=$query->num_rows();
				}else if($type==3)
				{
					$type_name="Combined MOQ & VLI";
					$moq_span=$query->num_rows();
					$vli_span=$query->num_rows();
				}else
				{
					$type_name="";
					$moq_span=0;
					$vli_span=0;
				}
				$moq=$combdata['moq'];
				$vli=$combdata['vli'];
			}else
			{
				$type_name='';
				$moq=0;
				$vli=0;
				$moq_span=0;
				$vli_span=0;
			}

				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;width:200px;'>Location</th>
				<th style='padding:10px;width:200px;'>Product Name</th>
				<th style='padding:10px;width:200px;'>Approved Price</th>
				<th style='padding:10px;width:200px;'>Price Validity</th>
				<th style='padding:10px;width:200px;' >Credit/VLI</th>
				<th style='padding:10px;width:200px;'>Minimum Qty</th>
				<th style='padding:10px;width:200px;'>Purchased Qty</th>
				<th style='padding:10px;width:200px;'>Claim Generated</th>
				<th style='padding:10px;width:200px;'>Error/Warning(s)</th>
				</tr>
				</thead>
				<tbody>";
				$t=0;
			foreach($query->result() as $rows) {


				$claim=$this->getClaimgenerated_product_wise_combination($rows->approval_id,$vli,$moq,$rows->location,$rows->validity_from,$rows->validity_to,$rows->product_id,$start_date,$end_date,$rows->pack_size,$rows->density,$rows->pack_type);
				$c=explode("|",$claim);
				$claim_amount=$c[0];
				$purchase_qty=$c[1];
				$error_message=$c[2];

				if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->shortname."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>";
						if($vli_span>0)
						{
							if($t==0)
							{
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$vli."/".$rows->shortname."</td>";
							}
						}else
						{
								$ht.="<td style='padding:10px;'>".$vli."/".$rows->shortname."</td>";
						}


						if($moq_span>0)
						{
						if($t==0)
						{
						$ht.="<td style='padding:10px;' rowspan='".$moq_span."'>".$moq." ".$rows->shortname."</td>";
						}
						}else
						{
						$ht.="<td style='padding:10px;'>".$rows->moq." ".$rows->shortname."</td>";
						}

						if($t==0)
						{
						$total_claim_amount[]=$claim_amount;
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$purchase_qty." ".$rows->shortname."</td>";
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'><strong style='color:red;font-weight:bold;font-size:18px;'>".$claim_amount."</strong></td>";
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$error_message."</td>";
						}


						
					$ht.="</tr>";
			$t++; 
				}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht."|".array_sum($total_claim_amount);


	}


	function getEditApprovalTypeTwo($approval_id) {
    $res = '';
    $query = $this->db->select('a.invoice_date,a.invoice,a.annexture,a.annexture_name,a.type,a.current_date,a.customer_name,a.customer_tds,a.customer_tcs, c.first_name,c.last_name,h.customer_code')
              ->from('approval_form_type_two a')
              ->join('system_users c','a.added_by=c.user_id','left')
              ->join('hpcl_direct_customer h','h.id=a.customer_name')
              ->where('a.id', $approval_id)
              ->get();
// echo $this->db->last_query();
// exit();
     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }


 function getEditProductApprovalTypeTwo($approval_id) {
    $res = '';
    $query=$this->db->select('a.id,a.location,a.product_id,a.pack_size,a.validity_from,a.validity_to,a.approved_price,a.credit_days,a.commision,a.transport_type,a.transport_rate,a.annexure,a.annexure_upload')
              ->from('approval_product_details_type_two a')
              ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
              ->where('a.approval_id',$approval_id)
              ->get();

     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }

   function getEditApprovalTranpotation($approval_id) {
    $res = '';
    $query = $this->db->select('a.type,a.current_date,a.customer_name,a.customer_tds,a.hpcl_location,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed, c.first_name,c.last_name,h.customer_code')
              ->from('transportation_based_approval a')
              ->join('system_users c','a.added_by=c.user_id','left')
              ->join('hpcl_direct_customer h','h.id=a.customer_name')
              ->where('a.id', $approval_id)
              ->get();
// echo $this->db->last_query();
// exit();
     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }
  function getEditTransportationAprrovalProduct($approval_id) {
    $res = '';
    $query=$this->db->select('a.id,a.location,a.product_id,a.pack_size,a.deliveredQty,a.annexure,a.annexure_upload')
              ->from('transportation_based_aprroval_product_details a')
              ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
              ->where('a.approval_id',$approval_id)
              ->get();

     if($query->num_rows() > 0) {
      $res = $query->result();
     }

     return $res;
  }

  function check_for_previous_payment($inventory_id)
			{
				$d=array();
				$d[]=0;
				$restey=$this->db->select('collection_amount')->from('inventory_payment_details')->where('inventory_id',$inventory_id)->get();
				if($restey->num_rows()>0)
				{
					foreach($restey->result() as $row)
					{
						$d[]=$row->collection_amount;
					}

				}

				return array_sum($d);

			}

	function getHighestCustomerCode() {
		$res = 0;
		$sql = $this->db->select('MAX(customer_ref_no) as customer_code')
						->from('customer_detail')
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
			$res = $row->customer_code + 1;
		}

		return $res;
	}
   function get_customer_payment_collection($customer_id){
    $ht='';
        $query=$this->db->select('a.*,b.balance')
                  ->from('customer_collection_reference a')
                  ->join('customer_collection_reference_balance b','a.id=b.collection_id')
                  ->where('customer_id',$customer_id)
                  ->order_by('a.collection_date','DESC')->limit(4)
                  ->get();
        if($query->num_rows()>0) {
            $ht.="<table class='table table-bordered'>
            <thead>
            <tr>
            <th style='padding:10px;width:210px;'>Collection Date</th>
            <th style='padding:10px;'>Collection Id</th>
            <th style='padding:10px;'>Collection Amount</th>
            <th style='padding:10px;'>Balance</th>
            <th style='padding:10px;'>Remarks</th>
            <th style='padding:10px;'>Credit/Debit Note</th>
            </tr>
            </thead>
            <tbody>";
          foreach($query->result() as $rows) {
             $customer_debit_credit = $this->get_customer_debit_credit($customer_id,$rows->id);
            $ht.="<tr>
            <td style='padding:10px;width:210px;'>".date('d-M-Y',strtotime($rows->collection_date))."</td>
            <td style='padding:10px;'>".$rows->collection_id."</td>
            <td style='padding:10px;'>".$rows->collection_amount."</td>
            <td style='padding:10px;'>".$rows->balance."</td>
            <td style='padding:10px;'>".$rows->remarks."</td>
            <td style='padding:10px;'>".$customer_debit_credit."</td>
            
            </tr>";
          }

          $ht.="</tbody> </table>";

           if($query->num_rows()>0) {
           	$ht.="<br/><br/><span class='text-center'><a href='".page_url."Customer/direct_customer_collection/".$customer_id."' class='btn btn-success btn-sm text-center'>View All</a></span>";
           }

        }


    //echo $ht; exit;
        return $ht;
   }
   function getDirectCustomer($id){
    $res = '';
     $sql = $this->db->select('a.*,b.state_name')
              ->from('hpcl_direct_customer a')
              ->join('states b','a.state=b.state_id','left')
              ->where('a.id',$id)
              ->get();

      if($sql->num_rows() > 0) {
        $res = $sql->result();
      }

      return $res;
   }

        function getProductsByLocation($location_id) {
      $res = '';
      $sql = $this->db->select('b.id,b.instruments_name,b.pack_size')
              ->from('company_products a')
              ->join('presto_instruments b','a.product_id= b.id')
              ->where('a.company_id', $location_id)
              ->get();

      if($sql->num_rows() > 0) {
        $res = $sql->result();
      }

      return $res;
   }

   function getCustomerQuotationID($lead_id) {
   	    $res = '';
   		$query =  $this->db->select('id')
						   ->from('customer_quotation')
						   ->where('lead_id',$lead_id)
						   ->get();
						  
		if($query->num_rows() > 0) {
			foreach ($query->result() as $row);
			$res = $row->id;
		}

		return $res;
   }

   function checkIfQuotationExists($lead_id) {
   		$lead_status = $this->dashboardmodel->getConversionLeadStage();

   		$query =  $this->db->select('id')
						   ->from('progress_remarks')
						   ->where('lead_id',$lead_id)
						   ->where('lead_status',$lead_status)
						   ->get();

		return $query->num_rows();
						  
   }

       function get_customer_debit_credit($id, $collection_id){
       $html = '';
            $sql = $this->db->select('credit_debit, credit_debit_for, credit_debit_amount')
                            ->from('customer_collection_credit_debit')
                            ->where('customer_id', $id)
                            ->where('collection_id', $collection_id)
                            ->get();

            if($sql->num_rows() > 0) {
                $html .= '<table class="table table-bordered" style="width:100%">
                                <tr style="background-color:#DADADA;">
                                    <th style="text-align:center; width:250px;">REASON</th>
                                    <th style="text-align:center;">CREDIT</th> 
                                    <th style="text-align:center;">DEBIT</th> 
                                </tr>';
                foreach ($sql->result() as $rows) {
                        $credit_amount = '';
                        $debit_amount = '';

                        if($rows->credit_debit == 1) {
                            $credit_amount = '<strong style="color:green;">+'.$rows->credit_debit_amount.'</strong>';
                            $debit_amount = '';
                        } else if($rows->credit_debit == 2) {
                            $credit_amount = '';
                            $debit_amount = '<strong style="color:red;">-'.$rows->credit_debit_amount.'</strong>';
                        }

                    $html .= '<tr>
                                <td style="text-align:center; width:250px;">'.$rows->credit_debit_for.'</td>
                                <td style="text-align:center;">'.$credit_amount.'</td> 
                                <td style="text-align:center;">'.$debit_amount.'</td> 
                                </tr>';
                }

                 $html .= '</table>';
            }

        return $html;
  }

function get_conveyance_voucher($id)
  {
    $res = '';
    $resteyu=$this->db->select('id,convence_date,start_read,end_read,user_id')->from('employee_convence')->where('id',$id)->get();
    if($resteyu->num_rows()>0)
    {
      foreach($resteyu->result() as $row);
      $res= $row;
    }
    return $res;
  }

  function get_product_approval_details($product,$start_date, $end_date){
    $res ='';
    $invres='';
    $query = $this->db->select('b.combination')
                          ->from('approval_product_details a')
                          ->join('approval_form b', 'b.id=a.approval_id')
                          ->where('a.product_id',$product)
                          ->where('b.current_date >=', $start_date)
                          ->where('b.current_date <=', $end_date)
                          ->get();

                          // ->order_by('a.approved_price','ASC')
                          // ->limit(1)

      if($query->num_rows() > 0){
       foreach($query->result() as $rows){
        if($rows->combination == 0){
        $qry2 = $this->db->select('b.combination, a.approved_price, a.credit_vli, a.annexture, a.annexure_upload')
                          ->from('approval_product_details a')
                          ->join('approval_form b', 'b.id=a.approval_id')
                          ->where('a.product_id',$product)
                          ->where('b.current_date >=', $start_date)
                          ->where('b.current_date <=', $end_date)
                          ->where('b.combination',0)
                          ->order_by('a.approved_price','ASC')
                          ->limit(1)
                          ->get();
            if($qry2->num_rows() > 0){
              $res = $qry2->result();
            }


        }else{

        }
       }
      }


      return $res;
  }
  function get_sales_user(){
    $user_array = array();
    $user_array2 = array();
    $query = $this->db->select('a.id,h.user_id,h.first_name,h.last_name')
              ->from('order_punch a')
              ->join('order_punch_mailing_details f','a.id=f.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b', 'b.id=a.quotation_id')
              ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
              ->join('customer_detail d', 'd.id=b.customer_id')
              ->where('a.payment_type',5)
              ->where('a.payment',0)
              ->where('a.unfollow_customer',1)
              ->where('a.send_to_tally',1)
              ->group_by('a.agent')
              ->get();

     // $query = $this->db->select('user_id,first_name ,last_name')
     //                      ->from('system_users')
     //                      ->where('department_id',6)
     //                      ->where('user_status', 1)
     //                      ->get();

      if($query->num_rows() > 0){
        foreach($query->result() as $row){
          $query2 = $this->db->select('a.id')
              ->from('order_punch a')
              ->join('order_punch_mailing_details f','a.id=f.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b', 'b.id=a.quotation_id')
              ->join('lead_source g','g.source_id=a.source','left')
              ->join('system_users h','h.user_id=a.agent')
              ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
              ->join('customer_detail d', 'd.id=b.customer_id')
              ->where('a.payment_type',5)
              ->where('a.payment',0)
              ->where('a.unfollow_customer',1)
              ->where('a.send_to_tally',1)
              ->where('a.agent',$row->user_id)
              ->get();

          $user_array['user_id'] = $row->user_id;
          $user_array['name'] = $row->first_name." ".$row->last_name;
            $user_array['unfollow_sales'] = $query2->num_rows(); 
            $user_array2[] = $user_array;

        }
      }

      return $user_array2;
  }

  function checkIfCustomerCompanyExists($customer_id, $multiple_company) {
  	$sql = $this->db->select('id')
  					->from('customer_detail')
  					->where('customer_id', $customer_id)
  					->where('company_id', $multiple_company)
  					->get();

  	return $sql->num_rows();
  }

  function getCustomerAllDetails($customer_id) {
  	$res = '';

  	$sql = $this->db->select('customer_ref_no, title, customer_name, email, contact_no, country, state, city, gst, pan, address, status, company_name, alt_contact')
  					->from('customer_detail')
  					->where('id', $customer_id)
  					->get();

  	if($sql->num_rows() > 0) {
  		$res = $sql->result();
  	}

  	return $res;
  }


  function get_purchase_details_with_gst($id)
	{
		// <th style="width:80px;">Lot No.</th>
       // <th style="width:100px;">Batch No.</th>
       
        //<th style="width:80px;">Manufacturing Date.</th>
		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
        <th style="width:80px;">Rate</th>
         <th style="width:80px;">Total Price With GST</th>

        </tr>
        </thead><tbody>';

        $tot=array();
        $tot[]=0;
		$reste=$this->db->select('d.gst,a.id, a.product,a.pack_size,a.qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','d.id=a.inventory_id')->where('a.inventory_id',$id)->get();
		if($reste->num_rows()>0)
		{
			$i=1;
			foreach($reste->result() as $rows)
			{

				$batch_code = array();
				$report_file = '';

				$sql = $this->db->select('batch_no, report_file')
								->from('inventory_batch_no')
								->where('inv_detail_id', $rows->id)
								->get();


				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row) {
						$batch_code[] = $row->batch_no;

						if($row->report_file <> '') {
							$report_file .= '<a href="'.assets_url.'test_report/'.$row->report_file.'" download>Download</a><br>';
						} else {
							$report_file .= '';
						}
					}
				}

				// echo "<pre>";print_r($batch_code);exit;

				$total=$rows->qty*$rows->rate;
				$gst=$rows->gst/100;
				$gst_amt=$total*$gst;
				$t_amt=$total+$gst_amt;
					$html.='<tr>
					<td>'.$i.'</td>
					<td>'.strtoupper($rows->instruments_name).'</td>
					<td>'.$rows->qty.' '.$rows->shortname.'</td>
					<td>'.$rows->rate.'/'.$rows->shortname.'</td>
					
					<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$t_amt.'</strong></td>
					</tr>';

					$tot[]=$total;
			$i++;
		}


				//<td>'.$rows->lot_no.'</td>
				//<td>'.implode(',<br>', $batch_code).'</td>

				//<td>'.date('d-M-Y',strtotime($rows->manufacturing_date)).'</td>
		}else
		{
			$html.='<tr>
					<td colspan="8">No Product Available</td>
					</tr>';
		}

		$html.='</tbody>
  </table>';

  				return $html."|".array_sum($tot);

	}

	 function getcollection_details($id,$due_date,$interest,$billing_price,$product,$purchase_date,$hpcl_location)
      {

      	$interest_charge=array();
      	$interest_charge[]=0;
				$table='<table class="table table-bordered">
				<thead>
				<tr>
				<th>Collection ID</th>
				<th>Collection Amount</th>
				<th>Payment Due Date</th>
				<th>Collection Date</th>
				<th>Days Difference</th>
				<th>Interest Rate (in %)</th>
				<th>Billing Price</th>
				<th>Approved Price</th>
				<th>Diff</th>
				<th>Vli</th>
				<th>Total</th>
				<th>Interest</th>
			
				</tr>
				</thead>
				<tbody>';

      	$resty=$this->db->select('a.*,b.collection_id as collection_ref')->from('inventory_payment_details_product_wise a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.inventory_details_id',$id)->order_by('id','ASC')->get();
      	if($resty->num_rows()>0)
      	{
      		$i=1;
      		foreach($resty->result() as $row)
      		{
      			$due=date('Y-m-d',strtotime($due_date));
      			$pay_date=date('Y-m-d',strtotime($row->payment_date));
						$start = strtotime($due);
						$end = strtotime($pay_date);
						$days_between = ($end - $start) / 86400;

						$approval_data=$this->getApprovalForPurchase($product,$purchase_date,$hpcl_location);
						$appdata=explode('|',$approval_data);
						$app_id=$appdata[0];
						$app_rate=$appdata[1];
							$vli=$appdata[4];

						$diff=$billing_price-$app_rate;
						$rate_diff=$billing_price-$app_rate+$vli;

						$total_interest=0;
						if($days_between>0)
						{
						/** CACULATE INTEREST **/
						$interest_rate = $interest;
						$interest_rate = $interest_rate / 100;
						$calculate_int = ($row->amount * $interest_rate) / 365;
						$calculate_int = round($calculate_int * $days_between, 2);

						$total_interest = $total_interest + $calculate_int;
						}else
						{
							$total_interest=0;
						}

						/** END **/
						$table.=' <tr>
					
						<td>'.$row->collection_ref.$row->id.'</td>
						<td><strong style="color:red;font-weight:bold;font-size:16px;">₹'.floatval($row->amount).'</strong></td>
						<td>'.date('d-M-Y',strtotime($due_date)).'</td>
						<td>'.date('d-M-Y',strtotime($row->payment_date)).'</td>
						<td>'.$days_between.' Days</td>
						<td>'.$interest.'</td>
						<td>'.$billing_price.'</td>
						<td>'.$app_rate.'</td>
						<td>'.$diff.'</td>
						<td>'.$vli.'</td>
						<td>'.$rate_diff.'</td>
						<td><strong style="color:red;font-weight:bold;font-size:16px;">₹'.$total_interest.'</strong></td>
					
						</tr>';

						$interest_charge[]=$total_interest;
      		$i++;
      		}

      	}

      	return $table."|".array_sum($interest_charge)."|".$resty->num_rows();

      }


       function getApprovalForPurchase($product,$purchase_date,$hpcl_location)
      {
      	$app_price=0;
      	$app_id=0;
				$type=0;
				$moq=0;
				$credit=0;
				$validity_from=0;
				$validity_to=0;
				$annexture='';
				$annexture_upload='';
      				
      
      	$restey=$this->db->select('a.annexture,a.annexure_upload,a.validity_from,a.validity_to,a.approved_price,a.credit_vli,a.approval_id,b.combination,a.moq')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->where('a.location',$hpcl_location)->where('a.product_id',$product)->where('a.validity_from<=',date('Y-m-d',strtotime($purchase_date)))->where('a.validity_to>=',date('Y-m-d',strtotime($purchase_date)))->order_by('a.approved_price','ASC')->limit(1)->get();
      		if($restey->num_rows()>0)
      		{
      			foreach($restey->result() as $rowss)
      			{
      				$app_price=$rowss->approved_price;
      				$app_id=$rowss->approval_id;
      				$type=$rowss->combination;
      				$moq=$rowss->moq;
      				$credit=$rowss->credit_vli;
      				$validity_from=$rowss->validity_from;
      				$validity_to=$rowss->validity_to;
      				$annexture=$rowss->annexture;
      				$annexture_upload=$rowss->annexure_upload;
      				
      			}
      		}

      		return $app_id."|".$app_price."|".$type."|".$moq."|".$credit."|".$validity_from."|".$validity_to."|".$annexture."|".$annexture_upload;

      }


      function check_for_type_of_approval($appid)
      {
      	$type=0;
      	$restyyu=$this->db->select('combination')->from('approval_form')->where('id',$appid)->get();
      	if($restyyu->num_rows()>0)
      	{
      		foreach($restyyu->result() as $row);
      		$type=$row->combination;

      	}

      	return $type;

      }

      function checkMOQFullfilled($from,$to,$moq,$product,$location)
      {
      	$yes=0;
      	$pqty=0;
      	$rest=$this->db->select('sum(a.qty) as purchased_qty')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','b.party=c.id')->where('b.currentdate>=',$from)->where('b.currentdate<=',$to)->where('a.product',$product)->where('c.hpcl_location',$location)->get();
      	if($rest->num_rows()>0)
      	{
      		foreach($rest->result() as $rowss);
      		if($rowss->purchased_qty>=$moq)
      		{
      			$yes=1;
      			
      		}else
      		{
      			$yes=0;
      		}

      		if($rowss->purchased_qty<>'')
      		{
      		$pqty=$rowss->purchased_qty;
      		}else
      		{
      			$pqty=0;
      		}

      	}

      	return $yes."|".$pqty;

      }

      function getcombinedProductDetails($product_id,$app_id)
      {
      	$data=array();
      	$reste=$this->db->select('moq,vli')->from('approval_combination_type_1')->where('approval_id',$app_id)->get();
      	if($reste->num_rows()>0)
      	{
      		foreach($reste->result() as $row);
      		$data[]=$row->moq;
      		$data[]=$row->vli;
      	}


      	return $data;
      }

      function CheckCombinedMoqFullfilment($comb_moq,$comb_vli,$app_id,$product,$from,$to,$location)
      {
      	$qty_purchased=array();
      	$qty_purchased[]=0;
      	$product_name=array();
      	$rest1=$this->db->select('a.product,b.instruments_name')->from('approval_combination_detail_type_1 a')->join('presto_instruments b','a.product=b.id')->where('a.approval_id',$app_id)->get();
      	if($rest1->num_rows()>0)
      	{
      		foreach($rest1->result() as $row)
      		{
						$rest=$this->db->select('sum(a.qty) as purchased_qty')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','b.party=c.id')->where('b.currentdate>=',$from)->where('b.currentdate<=',$to)->where('a.product',$row->product)->where('c.hpcl_location',$location)->get();
						if($rest->num_rows()>0)
						{
						foreach($rest->result() as $rowss);
						if($rowss->purchased_qty=='')
						{
							$qty=0;

						}else{ 

							$qty=$rowss->purchased_qty; 
						}
						$qty_purchased[]=$qty;
					}

					$product_name[]=$row->instruments_name;

      	}

      }


      $total_qty_purchased=array_sum($qty_purchased);

      if($total_qty_purchased>=$comb_moq)
      {
      	$yes=1;
      }else
      {
      	$yes=0;
      }

      if(count($product_name)>0)
      {
      	$prdname=implode('<br/><br/>',$product_name);
      }else{
      	$prdname='';
      }


      return $yes."|".$total_qty_purchased."|".$prdname;

}

function getOrderWitqty($quotation)
	{
		
		  $price = array();
		  $sql = $this->db->select('agreed_price, qty')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows()>0) {
				foreach($sql->result() as $row) {
					$price[] = $row->qty;
				}
			
			}

			$total_price = array_sum($price);

		    return $total_price;
	}

	function getHpclCompanyCode($company_id) {
		$res = '';
		$sql = $this->db->select('address')
						->from('hpcl_location')
						->where('id', $company_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$res = $row->address;
			}
		}

		return $res;
	}

	function getHpclLocationsName($id) {
		$res = array();
		$sql = $this->db->select('a.name,b.name as vendor_name,b.address,b.state,c.state_name,b.gst')
						->from('hpcl_location a')
						->join('vendors b','a.id=b.hpcl_location')
						->join('states c','b.state=c.state_name')
						->where('a.id',$id)->get();
		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row)
			{
				$res[]=$row->name;
				$res[]=$row->vendor_name;
				$res[]=$row->address;
				$res[]=$row->state_name;
				$res[]=$row->gst;
			}
		}

		return $res;
	}


	function getRackLocationViaID($id) {
    $res = array();
    $query = $this->db->select('companyname,rack_location,address,pincode,gst,contact_person,email_id,state_id')
              ->from('store_rack_location')
              ->where('id',$id)
              ->get();

    	if($query->num_rows() > 0) {
			foreach($query->result() as $row)
			{
				$state_name=$this->getstate($row->state_id);
				$res[]=$row->companyname;
				$res[]=$row->rack_location;
				$res[]=$row->address;
				$res[]=$row->pincode;
				$res[]=$row->gst;
				$res[]=$row->contact_person;
				$res[]=$row->email_id;
				$res[]=$state_name;
			}
		}

    return $res;
  }


    function get_collection_ref($start,$end)
  {
  	$collection_ref=array();
  	$restey=$this->db->select('collection_date,collection_id,collection_amount,remarks')->from('sunder_collection_reference')->where('addedOn>=',date('Y-m-d',strtotime($start)))->where('addedOn<=',date('Y-m-d',strtotime($end)))->order_by('addedOn','ASC')->get();
  	if($restey->num_rows()>0)
  	{
  		foreach($restey->result() as $rowss)
  		{
  			$collection_ref[]=array('trns_date'=>date('Y-m-d',strtotime($rowss->collection_date)),'amount'=>$rowss->collection_amount,'description'=>'Payment Done Collection Reference '.$rowss->collection_id,'remarks'=>$rowss->remarks,'type'=>1);
  		}

  	}

  	$credit_ref=array();
  	$restey=$this->db->select('a.added_on,b.collection_id,a.credit_debit,credit_debit_for,credit_debit_amount')->from(' sunder_collection_credit_debit a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.added_on>=',date('Y-m-d',strtotime($start)))->where('a.added_on<=',date('Y-m-d',strtotime($end)))->where('a.credit_debit',1)->order_by('a.added_on','ASC')->get();
  	if($restey->num_rows()>0)
  	{
  		foreach($restey->result() as $rowss)
  		{
  			$credit_ref[]=array('trns_date'=>date('Y-m-d',strtotime($rowss->added_on)),'amount'=>$rowss->credit_debit_amount,'description'=>'Amount Credited Due to '.$rowss->credit_debit_for." in collection id".$rowss->collection_id,'remarks'=>'','type'=>1);
  		}

  	}


  	$debit_ref=array();
  	$restey=$this->db->select('a.added_on,b.collection_id,a.credit_debit,credit_debit_for,credit_debit_amount')->from(' sunder_collection_credit_debit a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.added_on>=',date('Y-m-d',strtotime($start)))->where('a.added_on<=',date('Y-m-d',strtotime($end)))->where('a.credit_debit',2)->order_by('a.added_on','ASC')->get();
  	if($restey->num_rows()>0)
  	{
  		foreach($restey->result() as $rowss)
  		{
  			$debit_ref[]=array('trns_date'=>date('Y-m-d',strtotime($rowss->added_on)),'amount'=>$rowss->credit_debit_amount,'description'=>'Debit Note Due to '.$rowss->credit_debit_for." in collection id".$rowss->collection_id,'remarks'=>'','type'=>2);
  		}

  	}

  	$payment_ref=array();
  	$restey=$this->db->select('a.*,b.collection_id')->from('inventory_payment_details a')->join('sunder_collection_reference b','a.collection_id=b.id')->join('inventory c','a.inventory_id=c.id')->where('a.pur_paymentOn>=',date('Y-m-d',strtotime($start)))->where('a.pur_paymentOn<=',date('Y-m-d',strtotime($end)))->order_by('a.pur_paymentOn','ASC')->get();
  	if($restey->num_rows()>0)
  	{
  		foreach($restey->result() as $rowss)
  		{
  			$payment_ref[]=array('trns_date'=>date('Y-m-d',strtotime($rowss->pur_paymentOn)),'amount'=>$rowss->collection_amount,'description'=>'Payment Made Against Purchase Bill '.$rowss->credit_debit_for." in collection id".$rowss->collection_id,'remarks'=>'','type'=>2);
  		}

  	}

  	echo "<pre>"; print_r($collection_ref)."<br/>"."<pre>"; print_r($credit_ref)."<br/>".print_r($debit_ref); exit;





  }

  function check_status($type,$lead_id,$month)
  {
  	$lead_ids=array();
  	if($type==0)
  	{
  		$start_date=date('Y-m-01',strtotime($month))." 00:00:00";
  		$end_date=date('Y-m-t',strtotime($month))." 23:59:59";

  	}else
  	{
  		$date = date('Y-m',strtotime($month));
  		$start_datess=date('Y-m',strtotime($date));
  		$start_date=date('Y-m-01',strtotime($start_datess))." 00:00:00";
  		$end_date=date('Y-m-t',strtotime($start_date))." 23:59:59";  		
  	}


  	if($lead_id<>1)
  	{
  	$restyu=$this->db->select('a.lead_id')->from('progress_remarks a')
  					->join('leads b','a.lead_id=b.id')
  					->where('a.lead_status',$lead_id)
  					->where('a.added_on>=',$start_date)
  					->where('a.added_on<=',$end_date)
  					->group_by('a.lead_id')->get();
  	}else
  	{
  		$start_date=date('Y-m-d',strtotime($start_date));
  		$end_date=date('Y-m-d',strtotime($end_date));
  		$restyu=$this->db->select('a.lead_id')->from('progress_remarks a')
  					->join('leads b','a.lead_id=b.id')
  					->where('a.lead_status',$lead_id)
  					->where('b.create_date>=',$start_date)
  					->where('b.create_date<=',$end_date)
  					->group_by('lead_id')
  					->get();
  	}
  	if($restyu->num_rows()>0)
  	{
  		foreach($restyu->result() as $row)
  		{
  			$lead_ids[]=$row->lead_id;
  		}

  	}

  	return $lead_ids;

  }

function total_visits_old_customer($start_date,$end_date,$user)
{

	$startdate1 = $start_date." 00:00:00";
		$end_date1 = $end_date." 23:59:59";
	$this->db->select('id')->from('oldcustomer_visit')->where('added_on BETWEEN "'.$startdate1. '" and "'.$end_date1.'"');
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		$res=$this->db->get();
		return $res->num_rows();

}

function total_visits_old_customer_userwise($start_date,$end_date,$user)
{

	$startdate1 = $start_date." 00:00:00";
		$end_date1 = $end_date." 23:59:59";
	$this->db->select('id')->from('oldcustomer_visit')->where('added_on BETWEEN "'.$startdate1. '" and "'.$end_date1.'"');
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		$res=$this->db->get();
		return $res->num_rows();

}

function order_punch_details($order_id)
{
	$d=array();
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,h.contact_number,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst,i.company_name')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('customer_detail i','i.id=b.customer_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.id',$order_id)
				 		  ->get();
							if($query->num_rows()>0)
							{

							$res = $query->result();
							foreach($res as $row);

							$d[]=$row->company_name;
							$d[]=$row->first_name." ".$row->last_name;
							$d[]=$row->contact_number;
							$d[]=$row->invoice_no;


							}

							return $d;

}

function gettrialproductInfo($trail_id)
{
$prd='';
$re=$this->db->select('a.lead_product_id,c.instruments_name')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','b.product_id=c.id')->where('a.id',$trail_id)->get();
if($re->num_rows()>0)
{
	foreach($re->result() as $row);
	$prd=$row->instruments_name;

}
return $prd;
}

function getdirectcustomer_name($id)
{
	$name='';
	$sql = $this->db->select('a.customer_name')
	->from('hpcl_direct_customer a')
	->where('a.id',$id)
	->get();
	if($sql->num_rows() > 0) {
	foreach($sql->result() as $re);
	$name = $re->customer_name; 
	}

	return $name;

}

function previous_paid_type_2_3($product_invoice_id,$customer)
{
	$paid=0;
	$dt=$this->db->select('sum(collection_amount) as collected')->from('customer_inventory_payment_details')->where('inventory_id',$product_invoice_id)->get();

	//->where('customer_id',$customer)
	
	if($dt->num_rows()>0)
	{
		foreach($dt->result() as $dtt);
		if($dtt->collected<>'')
		{
			$paid=$dtt->collected;
		}else
		{
			$paid=0;
		}
	}

	return $paid;

}

function check_for_applicable_approvals($product_id,$startdate,$location,$moq,$customer,$type)
{
	$commission=0;
	$transport=0;
	$approved_price=0;
	$annexture_name='';
	$annexture_upload='';
	$credit_days=0;

		$ht="<table class='table table-bordered'>
			<thead>
			<tr>
	
			<th style='padding:10px;'>Approved Price</th>
			<th style='padding:10px;width:150px;'>Price Validity</th>
			<th style='padding:10px;'>Credit Days</th>
			<th style='padding:10px;'>CFA Commision</th>
			<th style='padding:10px;'>Transport Type</th>
			<th style='padding:10px;'>Transport Rate</th>
			<th style='padding:10px;'>Annexture Details</th>
			</tr>
			</thead>
			<tbody>";

	$q=$this->db->select('a.pack_size,a.id,a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision,a.transport_type,a.transport_rate')->from('approval_product_details_type_two a')->join('approval_form_type_two b','a.approval_id=b.id')->where('a.product_id',$product_id)->where('a.location',$location)->where('b.customer_name',$customer)->where('b.type',$type)->order_by('a.approved_price','DESC')->limit(1)->get();
	if($q->num_rows()>0)
	{		
			foreach($q->result() as $rows);

			$start_from=$rows->validity_from;
			$start_end=$rows->validity_to;

			$range=$this->createDateRangeArray($start_from,$start_end);
			if(in_array($startdate, $range))
			{

			if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

					$unit=$this->getUnitName($rows->pack_size);

				if($rows->transport_type==1)
				{	
					
					$transport="EXMI";
				
					$trate=$rows->transport_rate."/".$unit;
					$transport=$rows->transport_rate;

				}else
				{
					$transport="Delivered";
						$trate='-';
						$transport=$rows->transport_rate;
					
				}

					$commission=$rows->commision;

					$approved_price=$rows->approved_price;
					$annexture_name=$rows->annexure;
					$annexture_upload=$rows->annexure_upload;
					$credit_days=$rows->credit_days;

					$ht.="<tr>
					<td style='padding:10px;'>".$rows->approved_price."/".$unit."</td>
					<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
					<td style='padding:10px;'>".$rows->credit_days." days</td>
					<td style='padding:10px;'>".$rows->commision."/".$unit."</td>
					<td style='padding:10px;'>".$transport."</td>
					<td style='padding:10px;'>".$trate."</td>
					<td style='padding:10px;'>".$rows->annexure."<br/><a href='".page_url1."/type_two_annexure/".$rows->annexure_upload."'>Download</a></td>
					</tr>";
			}else
			{

				$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
			}
		}else
		{
				$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
		}



			return $ht."|".$commission."|".$transport."|".$approved_price."|".$annexture_name."|".$annexture_upload."|".$credit_days;


}


function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange = [];

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}


function get_months_credit_claim_amount_type2_new($start_date,$end_date,$location,$product)
{

$commission=array();
$commission[]=0;
$transport[]=array();
$transport[]=0;
$start_date =$start_date;
$end_date =$end_date;

$query=$this->db->select('f.volume,a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
->from('type_2_3_invoice_particular d')
->join('type_2_3_invoice a','d.invoice_id=a.id')
->join('presto_instruments f','f.id=d.product_id')
->join('hpcl_location c', 'c.id=a.shipping_from')
->join('hpcl_direct_customer e', 'e.id=a.customer')
->where('a.invoice_date>=',$start_date)
->where('a.invoice_date<=',$end_date);
$this->db->order_by('a.invoice_date','ASC');
$query=$this->db->get();
 if ($query->num_rows() > 0) {
  foreach ($query->result() as $rows) {

$SSDATE=$rows->invoice_date;
  	 $claim_am=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$SSDATE);
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];

			$collection_data=array();
			$collection_data[]=0;
			$indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
			$payment_rows = $indata->num_rows();
			// echo $payment_rows; 
			// exit;
			$collection_array = array();
			$collection_amt = array();
			if ($indata->num_rows() > 0) {
			$l = 0;
			foreach ($indata->result() as $invdata) {

				 if ($payment_rows > 1) {
				 if($l==0)
				 {
			$com=$rows->pqty * $this_invoice_comm;
			$tra= $rows->pqty * $this_invoice_trans;
			$commission[]=$com+$tra;
			$transport[]=$tra;
				}
			}else
			{
			$com=$rows->pqty * $this_invoice_comm;
			$tra= $rows->pqty * $this_invoice_trans;
			$commission[]=$com+$tra;
			}


		$l++;
			}

			}

  }

}


//echo "<pre>"; print_r($commission); exit;
return array_sum($commission);


}


function check_status_via_user($type,$lead_id,$month,$user,$from_month)
  {
  	
  	$lead_ids=array();
  	//$date = date('Y-m-d',strtotime($month));
  		$start_datess=date('Y-m-d',strtotime($from_month));
  		$start_date=date('Y-m-d',strtotime($start_datess))." 00:00:00";
  		$end_date=date('Y-m-d',strtotime($month))." 23:59:59";  		
  		$start_date=date('Y-m-d',strtotime($start_date))." 00:00:00";
			$end_date=date('Y-m-d',strtotime($end_date))." 23:59:59";
			$date_selected="";
			/** GET ALL LEADS **/
			//$getall_assigned_leads=$this->getall_assigned_leads($start_date,$end_date,$user);
			$getall_assigned_leads=$this->getall_leads_worked($start_date,$end_date,$user);
			//echo "<pre>"; print_r($getall_assigned_leads); exit;
			/** END **/


  	
  		$stagecheck="AND a.lead_status IN(".$lead_id.")";
			if($user<>'ALL')
			{
			$membercheck="AND c.member_id=".$user;
			}else{
				$membercheck='';
			}
			if($lead_id==3)
			{
				$a="a.id";
			}else
			{
				$a="a.id";
			}

			if($getall_assigned_leads<>''){
				$f="AND b.id IN (".$getall_assigned_leads.")";
			}else{
				$f='';
			}

			$restyu=$this->db->query("SELECT ".$a." FROM progress_remarks a JOIN leads b ON a.lead_id=b.id JOIN lead_assigned_to_team_member c ON b.id=c.lead_id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)  $date_selected $stagecheck $membercheck $f GROUP BY b.id ORDER BY b.id DESC");

   				
  
  	if($restyu->num_rows()>0)
  	{
  		foreach($restyu->result() as $row)
  		{
  			$lead_ids[]=$row->id;
  		}
  	}

  

  	return $lead_ids;

  }


function overall_check_status_via_user($type,$lead_id,$startdate,$enddate, $user)
  {
  	$lead_ids=array();
  	$start_date=date('Y-m-d',strtotime($startdate))." 00:00:00";
  	$end_date=date('Y-m-d',strtotime($enddate))." 23:59:59";

  	if($lead_id<>1)
  	{
  	$this->db->select('a.lead_id')->from('progress_remarks a')
  					->join('leads b','a.lead_id=b.id')
  					->where('a.lead_status',$lead_id)
  					->where('a.added_on>=',$start_date)
  					->where('a.added_on<=',$end_date);
  					if($user<>'ALL')
  					{
  						$this->db->where('b.added_by',$user);
  					}
  					$restyu=$this->db->group_by('a.lead_id')->get();
  	}else
  	{
  		$start_date=date('Y-m-d',strtotime($start_date));
  		$end_date=date('Y-m-d',strtotime($end_date));
  		$restyu=$this->db->select('a.lead_id')->from('progress_remarks a')
  					->join('leads b','a.lead_id=b.id')
  					->where('a.lead_status',$lead_id)
  					->where('b.create_date>=',$start_date)
  					->where('b.create_date<=',$end_date);
  					if($user<>'ALL')
  					{
  						$this->db->where('b.added_by',$user);
  					}
  					$restyu=$this->db->group_by('a.lead_id')->get();
  				
  	}
  	if($restyu->num_rows()>0)
  	{
  		foreach($restyu->result() as $row)
  		{
  			$lead_ids[]=$row->lead_id;
  		}

  	}

  	return $lead_ids;

  }

  	function visit_for_new_customers($month,$user)
	{
			$data=array();
			$data[]=0;
		  $start_date=date('Y-m-01',strtotime($month));
  		$end_date=date('Y-m-t',strtotime($month));

		 $this->db->select('id')->from('daily_visits')->where('create_date>=',$start_date)->where('create_date<=',$end_date)->where('converted',0);
		 if($user<>'ALL')
		 {
		 	$this->db->where('added_by',$user);
		 }

		$res=$this->db->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row)
			{
				$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->id)->get();
				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->id)->get();
				if($rty->num_rows()==0 && $rty1->num_rows()==0)
				{
				$data[]=1;
				}
			}

		}
		
		return array_sum($data);
	}

	function totalvisit_for_new_customers($month,$user)
	{
		  $start_date=date('Y-m-01',strtotime($month));
  		$end_date=date('Y-m-t',strtotime($month));

		 $this->db->select('id')->from('daily_visits');
		 if($user<>'ALL')
		 {
		 	$this->db->where('added_by',$user);
		 }

		$res=$this->db->get();
		return $res->num_rows();
	}




	function visit_for_old_customers($month,$user)
	{
		 	$start_date=date('Y-m-01',strtotime($month));
  		$end_date=date('Y-m-t',strtotime($month));

			$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',$start_date." 00:00:00")->where('added_on<=',$end_date." 23:59:59");
			if($user<>'ALL')
			{
			$this->db->where('added_by',$user);
			}

		$res=$this->db->get();

		return $res->num_rows();
	}

	function totalvisit_for_old_customers($month,$user)
	{
		 	$start_date=date('Y-m-01',strtotime($month));
  		$end_date=date('Y-m-t',strtotime($month));

			$this->db->select('id')->from('oldcustomer_visit');
			if($user<>'ALL')
			{
			$this->db->where('added_by',$user);
			}

		$res=$this->db->get();

		return $res->num_rows();
	}


function total_visits_new($start_date,$end_date,$user)
{
	$this->db->select('id')->from('daily_visits')->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)));
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}
		$res=$this->db->get();
		return $res->num_rows();
}

function running_quote_send($start_date,$end_date,$user)
{
		$this->db->select('a.id')->from('customer_quotation a')->join('leads b','a.lead_id=b.id')->where('b.create_date>=',$start_date)->where('b.create_date<=',$end_date)->where('a.lead_id',0);
		if($user<>'ALL')
		{
		$this->db->where('b.added_by',$user);
		}
		$resty=$this->db->get();
		return $resty->num_rows();
}

 function get_pending_transporter_payment_type2_new($party,$location)
{

	$data = array();
	$data[]=0;
	$i=1;
	$this->db->select('a.*,c.name as location_name,e.customer_name')
	->from('type_2_3_invoice a')
	->join('hpcl_location c', 'c.id=a.shipping_from')
	->join('hpcl_direct_customer e', 'e.id=a.customer')
	->where('a.transporter_payment',0)
	->where('a.transport_type',2);

	if($party<>'' && $party<>'ALL')
	{
	$this->db->where('a.transporter_id',$party);
	}

	if($location<>'' && $location<>'ALL')
	{
	$this->db->where('shipping_from',$location);
	}
	$this->db->order_by('a.invoice_date','ASC');
	$query=$this->db->get();
	if($query->num_rows()>0) {

			foreach($query->result() as $rows)
			{
					
				
					$prd=$this->get_type_2_3_qty_detail($rows->id);
					$prd_detail=explode('|',$prd);
					$qty=$prd_detail[1];

					$trans=$this->get_type_2_3_transporter_detail_new($rows->id);
					$trans_detail=explode('|',$trans);
					$gstamt=0;
					if($trans_detail[1]==1)
						{

							$rtype="Per Ltr";
							$t_type_rate=$trans_detail[2]*$qty;

							$amount=$t_type_rate;
							$base_amount=$amount;
							if($trans_detail[3]>0)
							{
								$tds_amount=$base_amount*($trans_detail[3]/100);
							}else
							{
								$tds_amount=0;
							}


							$amount=$base_amount-$tds_amount;

							if($trans_detail[4]<>'')
							{
								$ggst=18/100;
								$gstamt=$amount*$ggst;
							}

							$finalbase=$amount+$gstamt;


						}else if($trans_detail[1]==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$trans_detail[2];
							$base_amount=$trans_detail[2];
							$amount=$trans_detail[2];
							if($trans_detail[3]>0)
							{
								$tds_amount=$base_amount*($trans_detail[3]/100);
							}else
							{
								$tds_amount=0;
							}

							$amount=$base_amount-$tds_amount;

							$finalbase=$amount+$gstamt;

						}else{ 	

							$amount=0;
							$base_amount=0;
							$tds_amount=0;
							$finalbase=0;

						}
			
						$data[] = $finalbase;

						$i++;
			}
						

			}
				
			
	
	
			
		return  array_sum($data);


		

	
	}

		function get_type_2_3_invoice_particular($invoice_id)
	{

		    //<th style="width:80px;">Manufacturing Date.</th>
		  $html='<table class="table table-bordered" style="width:100%">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:400px;">Product</th>
         <th style="width:130px;">Qty</th>
        <th style="width:180px;">Rate</th>
        <th style="width:180px;">Total Price</th>
        </tr>
        </thead><tbody>'; 

				$tot=array();
				$tot[]=0;
				$reste=$this->db->select('a.price,a.product_id,a.qty,a.price,a.unit,c.shortname,b.instruments_name')->from('type_2_3_invoice_particular a')->join('type_2_3_invoice d','a.invoice_id=d.id')->join('presto_instruments b','a.product_id=b.id')->join('units c','a.unit=c.id')->where('a.invoice_id',$invoice_id)->get();
				if($reste->num_rows()>0)
				{
				$i=1;
				foreach($reste->result() as $rows)
				{
					$total=$rows->qty*$rows->price;
					 $html.='<tr>';
					 $html.='<td>'.$i.'</td>';
					 $html.='<td>'.$rows->instruments_name.'</td>';
					 $html.='<td>'.floatval($rows->qty).' '.$rows->shortname.'</td>';
					 $html.='<td>'.$rows->price.'</td>';
					 $html.='<td>'.$total.'</td>';
					 $html.='</tr>';
					 $tot[]=$total;
				$i++;
				}
				}else
				{
					 $html.='<tr>';
					 $html.='<td colspan="5">No Data Available</td>';
					 $html.='</tr>';

				}

				return $html."|".array_sum($tot);

	}

	function getalltransportation_bills($start_date,$end_date)
	{

				$data = array();
				$start_date = date('Y-m-d', strtotime($start_date));
				$end_date = date('Y-m-d', strtotime($end_date));

				$this->db->select('c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
				->from('transportation_based_approval a')
				->join('hpcl_location c','a.hpcl_location=c.id')
				->join('hpcl_direct_customer b','a.customer_name=b.id');

				if($start_date <> '' && $end_date <> '') {
				$this->db->where('a.current_date >=', $start_date);
				$this->db->where('a.current_date <=', $end_date);
				}




				$query = $this->db->get();

				if($query->num_rows() > 0) {
					$i=1;
				foreach($query->result() as $row) {

				$getApprovalProductDetails = $this->salescrm->transportation_based_approval_ProductDetails($row->id,'ALL','ALL');
				if($getApprovalProductDetails!="NA") {
				$generate_invoice = "<a href='".page_url1."taxinvoice/tcpdf/examples/invoice.php?approval_id=".$row->id."' class='btn btn-success btn-xs'>Invoice</a>";
				$data[] =$row->id;								
				$i++;
				}

				}
				}
		

				return $data;

	}


	function getcurrent_status($ss)
	{
		$s=0;
		$resty=$this->db->select('current_status')->from('type_reports')->where('period',$ss)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $rowss);
			$s=$rowss->current_status;
		}

		return $s;

	}

	function running_customer_orders($start_date,$end_date,$user)
{
		$this->db->select('id')->from('order_punch')->where('send_to_tally_On>=',$start_date)->where('send_to_tally_On<=',$end_date)->where('send_to_tally',1)->where('direct_order',1);
		if($user<>'ALL')
		{
		$this->db->where('agent',$user);
		}
		$resty=$this->db->get();
		return $resty->num_rows();
}

function get_sales_numbers($startdate,$enddate,$user)
	{

		$sale_price=array();
		$sale_price[]=0;

		 $this->db->select('a.quotation_id as orderpunchquote')
              ->from('order_punch a')
              ->join('order_punch_mailing_details d','a.id=d.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b','b.id=a.quotation_id')
              ->join('store_rack_location c', 'c.id=b.company_id', 'left')
              ->join('system_users t', 't.user_id=a.agent', 'left')
              ->where('a.send_to_tally', 1)
              ->where('a.direct_order', 1)
              ->where('a.send_to_tally_On>=',$startdate)
              ->where('a.send_to_tally_On<=',$enddate);
              if($user<>'' && $user<>'ALL')
              {
                $this->db->where('a.agent',$user);
              }

             $query =$this->db->order_by('a.id','DESC')->get();
    $i=1;
    if($query->num_rows() > 0) {
    foreach($query->result() as $row)
    {
			$sale_price[]=$this->getproducts_detail($row->orderpunchquote);
		}
		}

		return array_sum($sale_price);
	}


function getproducts_detail($quotation)
	{
			$price=array();
			$price[]=0;
			$res=$this->db->select('a.agreed_price,a.qty')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			
			$price[]=round($product->agreed_price*$product->qty);

			$j++;
			}
		
			}

			return array_sum($price);
	}


	function get_profit_numbers($startdate,$enddate,$user)
	{

		$sale_price=array();
		$sale_price[]=0;

		 $this->db->select('a.quotation_id as orderpunchquote')
              ->from('order_punch a')
              ->join('order_punch_mailing_details d','a.id=d.order_id')
              ->join('order_punch_tax_details e','a.id=e.order_id')
              ->join('customer_quotation b','b.id=a.quotation_id')
              ->join('store_rack_location c', 'c.id=b.company_id', 'left')
              ->join('system_users t', 't.user_id=a.agent', 'left')
              ->where('a.send_to_tally', 1)
              ->where('a.cancelled', 0)
        
              ->where('a.send_to_tally_On>=',$startdate)
              ->where('a.send_to_tally_On<=',$enddate);
              if($user<>'' && $user<>'ALL')
              {
                $this->db->where('a.agent',$user);
              }

             $query =$this->db->order_by('a.id','DESC')->get();
    $i=1;
    if($query->num_rows() > 0) {
    foreach($query->result() as $row)
    {
			$sale_price[]=$this->getproducts_detail_for_profit($row->orderpunchquote,$startdate);
		}
		}

		//echo "<pre>"; print_r($sale_price); exit;
		return array_sum($sale_price);
	}


	function getproducts_detail_for_profit($quotation,$month)
	{

			$price=array();
			$price[]=0;
			$res=$this->db->select('a.agreed_price,a.qty,a.product_id,a.cp')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			
			//$cp=$this->getproduct_cp_for($product->product_id,$month,$product->agreed_price);
			$cp=$product->cp;
			$given_price=round($product->agreed_price*$product->qty);
			$cprice=round($cp*$product->qty);
			if($cprice>0)
			{
			$profit=$given_price-$cprice;
			}else
			{
				$profit=0;
			}
			$price[]=$profit;


			$j++;
			}
		
			}
			

			return array_sum($price);
	}

	function getproduct_cp_for($productid,$month,$order_price)
	{
		$startdate=date('Y-m-01',strtotime($month));
		$enddate=date('Y-m-t',strtotime($month));
		$resty=$this->db->select('costprice')->from('presto_instruments_margin_sheet')->where('product_id',$productid)->where('addedOn>=',$startdate." 00:00:00")->where('addedOn<=',$enddate." 23:59:59")->order_by('id','DESC')->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row);
			$mprice=$row->costprice;
		}else
		{
			$mprice=$this->getcurrent_allowed_price($productid);
			//$mprice=$order_price;
		}

		return $mprice;
	}





	function getproducts_detail_for_order_amount_cp_profit($quotation,$month)
	{

			$price=array();
			$price[]=0;
			$order_amount=array();
			$order_amount[]=0;
			$cp=array();
			$cp[]=0;
			$res=$this->db->select('a.agreed_price,a.qty,a.product_id,a.cp')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			
			//$cp1=$this->getproduct_cp_for($product->product_id,$month,$product->agreed_price);
			
			$cp1=$product->cp;
			$given_price=round($product->agreed_price*$product->qty);
			$cprice=round($cp1*$product->qty);
			if($cprice>0)
			{
			$profit=$given_price-$cprice;
			}else
			{
				$profit=0;
			}
			$price[]=$profit;
			$cp[]=$cprice;
			$order_amount[]=$given_price;

			$j++;
			}
		
			}

		

			return array_sum($order_amount)."|".array_sum($cp)."|".array_sum($price);
	}

	function getcurrent_allowed_price($productid)
	{
		$d=0;
		$restey=$this->db->select('discount_price')->from('presto_instruments')->where('id',$productid)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$d=$row->discount_price;
		}

		return $d;

	}


	function getApprovalProductDetails_type_two_trial($approval_id,$hpcl_location,$product,$transport_type,$customer_id) {
		$ht='';
		$query=$this->db->select('a.product_id,a.approved_price,a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate')
					    ->from('approval_product_details_type_two a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id);

					    if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    {
					    	$this->db->where('a.location',$hpcl_location);
					    }
					     if($product<>'ALL' && $product<>'')
					    {
					    	$this->db->where('a.product_id',$product);
					    }

					    if($transport_type<>'ALL' && $transport_type<>'')
					    {
					    	$this->db->where('a.transport_type',$transport_type);
					    }

					  $query=$this->db->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;'>Credit Days</th>
				<th style='padding:10px;'>CFA Commision</th>
				<th style='padding:10px;'>Transport Type</th>
				<th style='padding:10px;'>Transport Rate</th>
				<th style='padding:10px;'>Annexture Details</th>
				<th style='padding:10px;width:20%;'>Errors/Warning</th>
		
				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				if($rows->transport_type==1)
				{	
					
					$transport="EXMI";
				
					$trate=$rows->transport_rate."/".$rows->unit;

				}else
				{
					$transport="Delivered";
						$trate='-';
					
				}

				$in_uploaded=$this->check_for_invoice_added($customer_id,$rows->product_id,$rows->location,$rows->approved_price,$rows->validity_from,$rows->validity_to);

				$error_data=explode('|',$in_uploaded);
				//echo "<pre>"; print_r($error_data); exit;
				if(count($error_data)>0)
				{
					$error_msgg=$error_data[0];
					if($error_data[1]==0)
					{
						$st="background-color:green;color:white !important;";
					}else
					{
						$st="background-color:red;color:white !important;";
					}
				}else
				{
					$error_msgg='';
					$st='';
				}


				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_days." days</td>
						<td style='padding:10px;'>".$rows->commision."/".$rows->unit."</td>
						<td style='padding:10px;'>".$transport."</td>
						<td style='padding:10px;'>".$trate."</td>
						<td style='padding:10px;'>".$rows->annexure."<br/><a href='".page_url1."/type_two_annexure/".$rows->annexure_upload."'>Download</a></td>
						<td style='padding:10px;".$st."'>".$error_data[0]."</td>
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NA";
		}


//echo $ht; exit;
		return $ht;
	}


		function check_for_invoice_added($customer_id,$productid,$location,$apporved_price,$valid_from,$validity_to)
		{
			$errors=array();
			$invoice_attached=array();
			$error_flag=array();
			$error_flag[]=0;
			$rest=$this->db->select('a.payment,a.price,a.invoice_id,b.shipping_from,b.invoice_no,b.invoice_date')->from('type_2_3_invoice_particular a')->join('type_2_3_invoice b','b.id=a.invoice_id')->where('b.customer',$customer_id)->where('a.product_id',$productid)->where('b.invoice_date>=',$valid_from)->where('b.invoice_date<=',$validity_to)->get(); 
			//->where('b.invoice_date>=',$valid_from)->where('b.invoice_id<=',$validity_to)
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				{
					$invoice_attached[]=$row->invoice_no;
					if($row->shipping_from!=$location)
					{
						$errors[]="INVOICE NO: ".$row->invoice_no." LOCATION DIFFERENCE";
						$error_flag[]=11;
					}

					if(strtotime($row->invoice_date)>=strtotime($valid_from) && strtotime($row->invoice_date)<=strtotime($validity_to))
					{

					}else
					{
						$errors[]="INVOICE NO: ".$row->invoice_no." PRODUCT VALIDITY EXPIRED";
							$error_flag[]=12;
					}

					if($apporved_price!=$row->price)
					{
						$errors[]="INVOICE NO: ".$row->invoice_no." APPROVAL-BILLING PRICE DIFFERENCE";
							$error_flag[]=13;
					}

					if($row->payment==0)
					{
						$errors[]="INVOICE NO: ".$row->invoice_no." PAYMENT PENDING";
						$error_flag[]=1;
					}

				}




			}else
			{
				$errors[]="INVOICE MISSING!!";
					$error_flag[]=15;
			}


		
			
			$error_msg='';
			if(count($errors)>0)
			{
				$t=1;
				foreach($errors as $d)
				{
				$error_msg.="<strong style='font-weight;bold;'>".$d."</strong><br/><br/>";
				$t++;
				}
			}else
			{
				
				if(count($invoice_attached)>0)
			{
				$t=1;
				$error_msg.="<strong style='font-weight;bold;'>NO ERRORS<br/>INVOICES APPLICABLE</strong><br/><br/>";
				foreach($invoice_attached as $d)
				{
					$error_msg.="<strong style='font-weight;bold;'>".$d."</strong><br/>";
				$t++;
				}
			}

			}

			return $error_msg."|".array_sum($error_flag);

		}



function check_for_applicable_approvals_new($product_id,$startdate,$location,$moq,$customer,$type,$start_date,$end_date)
{
	$commission=0;
	$transport=0;
	$approved_price=0;
	$annexture_name='';
	$annexture_upload='';
	$credit_days=0;

		$ht="<table class='table table-bordered'>
			<thead>
			<tr>
	
			<th style='padding:10px;'>Approved Price</th>
			<th style='padding:10px;width:150px;'>Price Validity</th>
			<th style='padding:10px;'>Credit Days</th>
			<th style='padding:10px;'>CFA Commision</th>
			<th style='padding:10px;'>Transport Type</th>
			<th style='padding:10px;'>Transport Rate</th>
			<th style='padding:10px;'>Annexture Details</th>
			</tr>
			</thead>
			<tbody>";

if($customer==16)
{
$a="a.pack_size";
//echo $product_id."<br/>".$startdate.'-'.$location.'-'.$moq.'-'.$customer.'-'.$type.'-'.$start_date.'-'.$end_date; exit;
}else
{
$a="a.pack_size";
}
	$q=$this->db->select($a.',b.id as approval_id,a.id,a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision,a.transport_type,a.transport_rate')->from('approval_product_details_type_two a')->join('approval_form_type_two b','a.approval_id=b.id')->where('a.product_id',$product_id)->where('a.location',$location)->where('b.customer_name',$customer)->where('b.type',$type)->where('validity_from>=',$start_date)->where('validity_from<=',$end_date)->order_by('a.approved_price','DESC')->limit(1)->get();
	if($q->num_rows()>0)
	{		
			foreach($q->result() as $rows);

			$start_from=$rows->validity_from;
			$start_end=$rows->validity_to;

			$range=$this->createDateRangeArray($start_from,$start_end);
			if(in_array($startdate, $range))
			{

			if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

					$unit=$this->getUnitName($rows->pack_size);

				if($rows->transport_type==1)
				{	
					
					$transport="EXMI";
				
					$trate=$rows->transport_rate."/".$unit;
					$transport=$rows->transport_rate;

				}else
				{
					$transport="Delivered";
						$trate='-';
						$transport=$rows->transport_rate;
					
				}

					$commission=$rows->commision;

					$approved_price=$rows->approved_price;
					$annexture_name=$rows->annexure;
					$annexture_upload=$rows->annexure_upload;
					$credit_days=$rows->credit_days;

					$ht.="<tr>
					<td style='padding:10px;'>".$rows->approved_price."/".$unit."</td>
					<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
					<td style='padding:10px;'>".$rows->credit_days." days</td>
					<td style='padding:10px;'>".$rows->commision."/".$unit."</td>
					<td style='padding:10px;'>".$transport."</td>
					<td style='padding:10px;'>".$trate."</td>
					<td style='padding:10px;'>".$rows->annexure."<br/><a href='".page_url1."/type_two_annexure/".$rows->annexure_upload."'>Download</a></td>
					</tr>";
			}else
			{

				$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
			}
		}else
		{
				$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
		}



			return $ht."|".$commission."|".$transport."|".$approved_price."|".$annexture_name."|".$annexture_upload."|".$credit_days;


}


public function delegatedtaskreport($startdate, $enddate, $user){

	$this->db->select('id')->from('delegation_task')->where('delegated_date>=',$startdate)->where('delegated_date<=',$enddate);
	if($user<>'ALL'){
		$this->db->where('delegate_to',$user);
	}
	$q = $this->db->get();
	return count($q->result());


}

public function conveyancereporting($startdate, $enddate, $user){

	$this->db->select('id')->from('employee_convence')->where('convence_date>=',$startdate)->where('convence_date<=',$enddate);
	if($user<>'ALL'){
		$this->db->where('user_id',$user);
	}
	$q = $this->db->get();
	return count($q->result());


}

function totalnumberofoverdues($startdate, $enddate, $user){
$this->db->select('a.id')
->from('order_punch a')
->where('a.payment_type',5)
->where('a.payment',0)
->where('a.send_to_tally',1)
->where('a.cancelled',0);

if($user<>'ALL' && $user<>'')
{
$this->db->where('a.agent',$user);
}
$query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
->order_by('a.id','DESC')
->get();
return count($query->result());
}

function all_payment_overdue_list($userid) {

		$user_id=$this->uri->segment(3);
		$company=$this->uri->segment(4);
		$overdue_days_type='ALL';
		if($overdue_days_type<>'ALL' && $overdue_days_type<>'')
		{
			if($overdue_days_type==1)
			{
				$l="1";
				$up="29";
			}else if($overdue_days_type==2)
			{
				$l="30";
				$up="59";
			}else if($overdue_days_type==3)
			{
				$l="60";
				$up="89";
			}else
			{
				$l="89";
				$up="100000";
			}
		}else
		{
			$l=0;
			$up=0;
		}
		$currentday=date('Y-m-d');
		$grandtotalval = array();
		$lead_data = array();
		 $this->db->select('a.invoice_no,a.send_to_tally_On,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, a.unfollow_customer, a.unfollow_added_on, a.unfollow_added_by, b.id as quotation_id, b.customer_id, c.companyname, c.gst,d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('a.payment_type',5)
						  ->where('a.payment',0)
						  ->where('a.send_to_tally',1)
						  ->where('a.cancelled',0);

						  if($userid<>'ALL' && $userid<>'')
						  {
						  	$this->db->where('a.agent',$userid);
						  }
						  
						 $query =$this->db->where('a.send_to_tally_On !=','0000-00-00')
						  ->order_by('a.id','DESC')
				 		  ->get();


			$i=1;
			if($query->num_rows() > 0) {
				foreach($query->result() as $row) {
					$html='';
					$j=1;

					

					$html=$this->getproducts_detail($row->quotation_id);

					
					$j=1;

					
					if($row->payment_type == 2) {
					$payment_type = 'Cash';
					$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
					$payment_terms.= '';
					} else if($row->payment_type == 3) {
					$payment_type = 'Online';
					$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
					$payment_terms .= '';
					} else if($row->payment_type == 4) {
					$payment_type = 'PDC';
					$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
					$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
					$payment_type = 'CREDIT';
					$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
					$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
					$payment_type = 'ADVANCE';
					$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';

					} else {
					$payment_type = '';
					$payment_terms="";
					$payment_terms .= '';
					}

						if($row->credit_days!='')
						{
						$creditdays=$row->credit_days;
						}else
						{
						$creditdays=0;
						}

						$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$creditdays.' days'));


						if(strtotime($currentday)>strtotime($expected_payment_days))
						{
						
						$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$exceed_days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						
					
						$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
						$get_username = $this->salescrm->getusername($row->unfollow_added_by);

						$partial=$this->customer_previous_payment($row->id);
	
						$payment_due=$order_value-$partial;

						if($row->unfollow_customer == 0) {
							$do_not_followup = "<input type='checkbox' name='chk_unfollow' class='chk_unfollow".$row->id."' value='".$row->customer_id."' onchange='check_followup(".$row->id.")'>";
						} else {
							$do_not_followup = "ORDER UNFOLLOWED ON: <strong style='color:red;'>".date('d-m-Y H:i:s', strtotime($row->unfollow_added_on))."</strong><br>ORDER UNFOLLOWED BY: <strong style='color:red;'>".$get_username."</strong>";
						}



						if($l!=0 && $up!=0)
						{
						 
						if($exceed_days>=$l && $exceed_days<=$up)
						{

							$grandtotalval[] = $order_value;

				
				}
				}else
				{

				$grandtotalval[] = $order_value;
			


				}
				}
				}
			}
		return $grandtotalval;
	}



function get_type_2_3_qty_detail($invoice_id)
	{

		    //<th style="width:80px;">Manufacturing Date.</th>
		  $html='<table class="table table-bordered" style="width:100%">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:400px;">Product</th>
         <th style="width:130px;">Qty</th>
        <th style="width:180px;">Rate</th>
        <th style="width:180px;">Total Price</th>
        </tr>
        </thead><tbody>'; 

				$tot=array();
				$tot[]=0;
				$reste=$this->db->select('a.price,a.product_id,a.qty,a.price,a.unit,c.shortname,b.instruments_name')->from('type_2_3_invoice_particular a')->join('type_2_3_invoice d','a.invoice_id=d.id')->join('presto_instruments b','a.product_id=b.id')->join('units c','a.unit=c.id')->where('a.invoice_id',$invoice_id)->get();
				if($reste->num_rows()>0)
				{
				$i=1;
				foreach($reste->result() as $rows)
				{
					$total=$rows->qty*$rows->price;
					 $html.='<tr>';
					 $html.='<td>'.$i.'</td>';
					 $html.='<td>'.$rows->instruments_name.'</td>';
					 $html.='<td>'.floatval($rows->qty).' '.$rows->shortname.'</td>';
					 $html.='<td>'.$rows->price.'</td>';
					 $html.='<td>'.$total.'</td>';
					 $html.='</tr>';
					 $tot[]=$rows->qty;
				$i++;
				}
				}else
				{
					 $html.='<tr>';
					 $html.='<td colspan="5">No Data Available</td>';
					 $html.='</tr>';

				}

				return $html."|".array_sum($tot);

	}


	function get_type_2_3_transporter_detail_new($invoice_id)
	{

		  $html='<table class="table table-bordered" style="width:100%">
        <thead>
        <tr>
        <th style="width:400px;">Transport By</th>
         <th style="width:130px;">Transport Type</th>
        <th style="width:180px;">Transporter</th>
        <th style="width:180px;">Vehicle No.</th>
        <th style="width:180px;">Rate Type</th>
        <th style="width:180px;">Rate</th>
        </tr>
        </thead>
        <tbody>'; 

				$tot=array();
				$tot[]=0;
				$reste=$this->db->select('a.transportation,a.transport_type,a.vehicle_no,a.transporter_id,a.rate_type,a.transport_rate')->from('type_2_3_invoice a')->where('a.id',$invoice_id)->get();
				if($reste->num_rows()>0)
				{
				$i=1;
				foreach($reste->result() as $row)
				{
					$tt='';
					$transporter='';
					$rtype='';
					$ttype='';
					$tds=0;
					$gst='';
					if($row->transportation==1)
					{
						$tt="Our";


						if($row->transport_type==1)
						{
							$ttype="Our";
							$transporter='';
						}else
						{
								$ttype="Hired";
								$transporter=$this->getTransporterDetailsName($row->transporter_id);
								$tds=$this->getTransportertds($row->transporter_id);
								$gst=$this->getTransportergst($row->transporter_id);
								if($row->rate_type==1)
								{
								$rtype="Per LTR";
								}else
								{
								$rtype="Fixed";
								}
						}


					}else
					{
						$tt="Customer";
						$ttype='';
						$transporter='';
					}

					$rate_type=$row->rate_type;
					$trate=$row->transport_rate;
				
					 $html.='<tr>';
					 $html.='<td>'.$tt.'</td>';
					 $html.='<td>'.$ttype.'</td>';
					 $html.='<td>'.$transporter.'</td>';
					 $html.='<td>'.$row->vehicle_no.'</td>';
					 $html.='<td>'.$rtype.'</td>';
					 $html.='<td>'.$row->transport_rate.'</td>';
					 $html.='</tr>';

				$i++;
				}
				}else
				{
					$rate_type=0;
					$trate=0;
					 $html.='<tr>';
					 $html.='<td colspan="5">No Data Available</td>';
					 $html.='</tr>';

				}

				return $html."|".$rate_type."|".$trate."|".$tds."|".$gst;

	}

	function getTransportertds($tid)
	{
			$name=0;
		$sql = $this->db->select('tds')
						->from('transporter_details')
						->where('id', $tid)
						->get();
						if($sql->num_rows()>0)
						{
							foreach($sql->result() as $rowww);
							$name=$rowww->tds;
						}

						return $name;

	}


	function getTransportergst($tid)
	{
			$name=0;
		$sql = $this->db->select('tds,gst')
						->from('transporter_details')
						->where('id', $tid)
						->get();
						if($sql->num_rows()>0)
						{
							foreach($sql->result() as $rowww);
							$name=$rowww->gst;
						}

						return $name;

	}

	function getTransporterDetailsName($transporter_id)
	{
		$name='';
		$sql = $this->db->select('name')
						->from('transporter_details')
						->where('id', $transporter_id)
						->get();
						if($sql->num_rows()>0)
						{
							foreach($sql->result() as $rowww);
							$name=$rowww->name;
						}

						return $name;

	}

	function check_for_status($id,$period)
	{
		$r=$this->db->select('id')->from('type_report_final')->where('period',$period)->where('type',$id)->get();
		return $r->num_rows();

	}


		function getcurrent_statusNew($ss,$type)
	{
		$s=0;
		$resty=$this->db->select('current_status')->from('type_report_final')->where('period',$ss)->where('type',$type)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $rowss);
			$s=$rowss->current_status;
		}

		return $s;

	}

	function getcurrent_Remarks($period,$id)
	{
		$msg='';
		$resty=$this->db->select('remarks')->from('type_reports_progress')->where('period',$period)->where('type',$id)->order_by('id','DESC')->limit(1)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $rowss);
			$msg=$rowss->remarks;
		}

		return $msg;


	}


	function check_for_applicable_approvals_new_one($product_id,$startdate,$location,$moq,$customer,$type,$start_date,$end_date,$invoice_date)
{
	$commission=0;
	$transport=0;
	$approved_price=0;
	$annexture_name='';
	$annexture_upload='';
	$credit_days=0;

		$ht="<table class='table table-bordered'>
			<thead>
			<tr>
	
			<th style='padding:10px;'>Approved Price</th>
			<th style='padding:10px;width:150px;'>Price Validity</th>
			<th style='padding:10px;'>Credit Days</th>
			<th style='padding:10px;'>CFA Commision</th>
			<th style='padding:10px;'>Transport Type</th>
			<th style='padding:10px;'>Transport Rate</th>
			<th style='padding:10px;'>Annexture Details</th>
			</tr>
			</thead>
			<tbody>";
if($customer==16)
{
$a="a.pack_size";
//echo $product_id."<br/>".$startdate.'-'.$location.'-'.$moq.'-'.$customer.'-'.$type.'-'.$start_date.'-'.$end_date; exit;
}else
{
$a="a.pack_size";
}


$apprid=0;
$q1=$this->db->select($a.',b.id as approval_id,a.id,a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision,a.transport_type,a.transport_rate')->from('approval_product_details_type_two a')->join('approval_form_type_two b','a.approval_id=b.id')->where('a.product_id',$product_id)->where('a.location',$location)->where('b.customer_name',$customer)->where('b.type',$type)->where('validity_from>=',$start_date)->where('validity_from<=',$end_date)->order_by('a.approved_price','DESC')->get();
if($q1->num_rows()>0)
{
	foreach($q1->result() as $q11)
	{
		$start_from1=$q11->validity_from;
		$start_end1=$q11->validity_to;
		if (strtotime($invoice_date) >= strtotime($start_from1) && strtotime($invoice_date) <= strtotime($start_end1)) {
			$apprid=$q11->approval_id;
		}
		
	}
}


if($apprid>0)
{
$q=$this->db->select($a.',b.id as approval_id,a.id,a.annexure,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision,a.transport_type,a.transport_rate')->from('approval_product_details_type_two a')->join('approval_form_type_two b','a.approval_id=b.id')->where('a.product_id',$product_id)->where('a.location',$location)->where('b.customer_name',$customer)->where('b.type',$type)->where('approval_id',$apprid)->get();
	if($q->num_rows()>0)
	{		
			foreach($q->result() as $rows);

			$start_from=$rows->validity_from;
			$start_end=$rows->validity_to;

			// $range=$this->createDateRangeArray($start_from,$start_end);
			// //echo "<pre>"; print_r($range); exit;
			// if(in_array($startdate, $range))
			// {

			if($rows->validity_from>date('Y-m-d') && $rows->validity_to<date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

					$unit=$this->getUnitName($rows->pack_size);

				if($rows->transport_type==1)
				{	
					
					$transport="EXMI";
				
					$trate=$rows->transport_rate."/".$unit;
					$transport=$rows->transport_rate;

				}else
				{
					$transport="Delivered";
						$trate='-';
						$transport=$rows->transport_rate;
					
				}

					$commission=$rows->commision;

					$approved_price=$rows->approved_price;
					$annexture_name=$rows->annexure;
					$annexture_upload=$rows->annexure_upload;
					$credit_days=$rows->credit_days;

					$ht.="<tr>
					<td style='padding:10px;'>".$rows->approved_price."/".$unit."</td>
					<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
					<td style='padding:10px;'>".$rows->credit_days." days</td>
					<td style='padding:10px;'>".$rows->commision."/".$unit."</td>
					<td style='padding:10px;'>".$transport."</td>
					<td style='padding:10px;'>".$trate."</td>
					<td style='padding:10px;'>".$rows->annexure."<br/><a href='".page_url1."/type_two_annexure/".$rows->annexure_upload."'>Download</a></td>
					</tr>";
			// }else
			// {

			// 	$ht.="<tr>
			// 				<td colspan='7'>No Approval Available</td>
			// 				</tr>";
			// }
		}else
		{
				$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
		}
	}else
	{
			$ht.="<tr>
							<td colspan='7'>No Approval Available</td>
							</tr>";
	}



			return $ht."|".$commission."|".$transport."|".$approved_price."|".$annexture_name."|".$annexture_upload."|".$credit_days;


}



	function getApprovalProductDetails_for_trail($approval_id) {
		$ht='';
		$query=$this->db->select('a.id as approval_particular_id,b.id as product_id,a.invoice,a.billing_date,a.annexture,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {
				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;'>Credit/VLI</th>
				<th style='padding:10px;'>Minimum Qty</th>
				<th style='padding:10px;'>Annexure</th>
				<th style='padding:10px;'>ERRORS</th>
	

				</tr>
				</thead>
				<tbody>";
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				if($rows->annexture<>'')
				{
					$anex="<a href='".site_http_root."type_one_annexure/".$rows->annexure_upload."' download>Download</a>";
				}else
				{
					$anex='';
				}


				$errors=$this->checkfor_errors_single($approval_id,$rows->approval_particular_id,$rows->product_id,$rows->moq,$rows->validity_from,$rows->validity_to,$rows->location);
				$er=explode('~',$errors);
				if($er[1]>0)
				{
					$background="background-color:red;font-weight:bold;color:white;";
				}else
				{
					$background="background-color:green;font-weight:bold;color:white;";
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
						<td style='padding:10px;'>".$rows->credit_vli."/".$rows->unit."</td>
						<td style='padding:10px;'>".$rows->moq." ".$rows->unit."</td>
						<td style='padding:10px;'>".$rows->annexture."<br/>".$anex."</td>
						<td style='padding:10px;".$background."'>".$er[0]."</td>
					
					</tr>";
			}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht;
	}



	function getApprovalProductDetails_combination_for_trial($approval_id) {
	
	$ht='';
		$query=$this->db->select('a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {

			$combdata=$this->getcombinationData($approval_id);
			if(count($combdata)>0)
			{
				$type=$combdata['type'];
				if($type==1)
				{
					$type_name="Combined MOQ";
					$moq_span=$query->num_rows();

				}else if($type==2)
				{
					$type_name="Combined VLI";
					$vli_span=$query->num_rows();
				}else if($type==3)
				{
					$type_name="Combined MOQ & VLI";
					$moq_span=$query->num_rows();
					$vli_span=$query->num_rows();
				}else
				{
					$type_name="";
					$moq_span=0;
					$vli_span=0;
				}
				$moq=$combdata['moq'];
				$vli=$combdata['vli'];
			}else
			{
				$type_name='';
				$moq=0;
				$vli=0;
				$moq_span=0;
				$vli_span=0;
			}

				$ht.="<table class='table table-bordered'>
				<thead>
				<tr>
				<th style='padding:10px;'>Location</th>
				<th style='padding:10px;'>Product Name</th>
				<th style='padding:10px;'>Approved Price</th>
				<th style='padding:10px;width:150px;'>Price Validity</th>
				<th style='padding:10px;' >Credit/VLI</th>
				<th style='padding:10px;'>Minimum Qty</th>
				</tr>
				</thead>
				<tbody>";
				$t=0;
			foreach($query->result() as $rows) {

				// $packsize=$this->getUnitName($rows->pack_size);

				if($rows->validity_from>=date('Y-m-d') && $rows->validity_to<=date('Y-m-d'))
				{
					$p="<strong style='color:red;font-weight:bold;'>Validity Expired</strong>";
					$color="red;font-weight:bold;";
				}else
				{
					$p="";
					$color="green;font-weight:bold;";
				}

				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>";
						if($vli_span>0)
						{
							if($t==0)
							{
						$ht.="<td style='padding:10px;' rowspan='".$vli_span."'>".$vli."/".$rows->unit."</td>";
							}
						}else
						{
								$ht.="<td style='padding:10px;'>".$vli."/".$rows->unit."</td>";
						}


						if($moq_span>0)
						{
						if($t==0)
						{
						$ht.="<td style='padding:10px;' rowspan='".$moq_span."'>".$moq." ".$rows->unit."</td>";
						}
						}else
						{
						$ht.="<td style='padding:10px;'>".$rows->moq." ".$unit."</td>";
						}


						
					$ht.="</tr>";
			$t++; 
				}

			$ht.="</tbody> </table>";

		}else
		{
			$ht.="NO PRODUCT FOUND";
		}


//echo $ht; exit;
		return $ht;


	}

	function checkfor_errors_single($apporval_id,$particular_id,$product_id,$moq,$valid_from,$valid_to,$location)
	{
		$a=array();
		$a[]=0;
		$qtys=array();
		$qtys[]=array();
		$errors='';
		$restey=$this->db->select('b.party,a.qty,a.id,b.bill_no,b.currentdate,a.payment,b.pur_payment')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('vendors c','c.id=b.party')->where('a.product',$product_id)->where('b.hpcl_billing_company',3)->where('c.hpcl',1)->where('b.currentdate>=',$valid_from)->where('b.currentdate<=',$valid_to)->get();
		if($restey->num_rows()>0)
		{
			$i=1;
			foreach($restey->result() as $row)
			{
				$in_location=$this->get_hpcl_location_via_party($row->party);
				$in_billno=$row->bill_no;
				$in_current_date=$row->currentdate;

				$qtys[]=$row->qty;

				if($i==0)
				{
					$errors.="<hr><br/><br/>";
				}


				

				if($in_location!=$location)
				{
					// $avail=$this->check_forbilling_here($location,$product_id,$valid_from,$valid_to,$in_location);

					// if($avail==0)
					// {
					$a[]=1;
					$errors.="BILLING & APPROVAL LOCATION MIS MATCH<BR/><BR/>";
					//}
				}

				if(strtotime($in_current_date)>=strtotime($valid_from) &&  strtotime($in_current_date)<=strtotime($valid_to))
				{

				}else
				{
					$a[]=1;
					$errors.="VALIDITY AND INVOICE DATE MISMATCH<BR/>BR/>";
				}

				if($row->payment==0)
				{


					$a[]=1;
					$errors.=$row->bill_no."<br/>";
					$errors.="PAYMENT PENDING<BR/><BR/>";
				}


			$i++;
		}

		}else
		{
			$a[]=1;
			$errors.="NO PURCHASE RECORDED<BR/>";
		}

		if($moq>0)
		{
			if($moq>array_sum($qtys))
			{
				$DI=$moq-array_sum($qtys);
			$a[]=1;
			$errors.="MOQ NOT REACHED STILL NEED ".$DI." LTRS<BR/>";
			}
		}

		if(array_sum($a)>0)
		{
			$final_errors=$errors;
		}else
		{
			$final_errors="NO ERROS FOUND";
		}

		return $final_errors."~".array_sum($a);

	}

	function get_hpcl_location_via_party($party)
	{
		$loc=0;
		$r=$this->db->select('hpcl_location')->from('vendors')->where('id',$party)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $rr);
			$loc=$rr->hpcl_location;
		}

		return $loc;

	}



function getApprovalProductDetails_data($approval_id) {
		$data=array();
		$query=$this->db->select('a.invoice,a.billing_date,a.annexture,a.annexure_upload,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,a.location,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {
				
			foreach($query->result() as $rows) {

		
				if($rows->annexture<>'')
				{
					$anex="<a href='".site_http_root."type_one_annexure/".$rows->annexure_upload."' download>Download</a>";
				}else
				{
					$anex='';
				}


				$data[]=array('location'=>$rows->name,'instruments'=>$rows->instruments_name,'approved_price'=>$rows->approved_price."/".$rows->unit,'validity'=>date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to)),'credit_vli'=>$rows->credit_vli."/".$rows->unit,'moq'=>$rows->moq." ".$rows->unit,'annex'=>site_http_root."type_one_annexure/".$rows->annexure_upload,'combinedwith'=>'');
				// $ht.="<tr>
				// 		<td style='padding:10px;'>".$rows->name."</td>
				// 		<td style='padding:10px;'>".$rows->instruments_name."</td>
				// 		<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
				// 		<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>
				// 		<td style='padding:10px;'>".$rows->credit_vli."/".$rows->unit."</td>
				// 		<td style='padding:10px;'>".$rows->moq." ".$rows->unit."</td>
				// 		<td style='padding:10px;'>".$rows->annexture."<br/>".$anex."</td>
					
				// 	</tr>";
			}

			// $ht.="</tbody> </table>";

		}

//echo "<pre>"; print_r($data); exit;
		return $data;
	}


	function getApprovalProductDetails_combination_data($approval_id) {
	
	$data=array();
		$query=$this->db->select('a.product_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_vli, a.moq, b.instruments_name,b.unit,c.name')
					    ->from('approval_product_details a')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					    ->join('hpcl_location c', 'c.id=a.location')
					    ->where('a.approval_id',$approval_id)
					    ->get();

		if($query->num_rows()>0) {

			$combdata=$this->getcombinationData($approval_id);
		
			if(count($combdata)>0)
			{
				$type=$combdata['type'];
				if($type==1)
				{
					$type_name="Combined MOQ";
					$moq_span=$query->num_rows();

				}else if($type==2)
				{
					$type_name="Combined VLI";
					$vli_span=$query->num_rows();
				}else if($type==3)
				{
					$type_name="Combined MOQ & VLI";
					$moq_span=$query->num_rows();
					$vli_span=$query->num_rows();
				}else
				{
					$type_name="";
					$moq_span=0;
					$vli_span=0;
				}
				$moq=$combdata['moq'];
				$vli=$combdata['vli'];
			}else
			{
				$type_name='';
				$moq=0;
				$vli=0;
				$moq_span=0;
				$vli_span=0;
			}

			
				$t=0;
			foreach($query->result() as $rows) {

				

					$combdata=$this->getcombinationData_product($approval_id,$rows->product_id);

				$data[]=array('location'=>$rows->name,'instruments'=>$rows->instruments_name,'approved_price'=>$rows->approved_price."/".$rows->unit,'validity'=>date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to)),'credit_vli'=>$rows->credit_vli."/".$rows->unit,'moq'=>$rows->moq." ".$rows->unit,'annex'=>site_http_root."type_one_annexure/".$rows->annexure_upload,'combinedwith'=>$combdata);


				$ht.="<tr>
						<td style='padding:10px;'>".$rows->name."</td>
						<td style='padding:10px;'>".$rows->instruments_name."</td>
						<td style='padding:10px;'>".$rows->approved_price."/".$rows->unit."</td>
						<td style='padding:10px;color:".$color."'>From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p."</td>";
								
			$t++; 
				}


		}


//echo $ht; exit;
		return $ht;


	}



	function getcombinationData_product($approval_id,$product)
	{
		$data=array();
		$restey=$this->db->select('b.instruments_name')->from('approval_combination_detail_type_1 a')->join('presto_instruments b','a.product=b.id')->where('a.approval_id',$approval_id)->where('a.product !=',$product)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$data[]=$row->instruments_name;

		}

		if(count($data)>0)
		{
			$d=implode(',',$data);
		}else
		{
			$d='';
		}

		return $d;

	}

	function total_visits_old_customer_new($start_date,$end_date,$user,$customer)
{

	$startdate1 = $start_date." 00:00:00";
		$end_date1 = $end_date." 23:59:59";
	$this->db->select('id')->from('oldcustomer_visit')->where('added_on BETWEEN "'.$startdate1. '" and "'.$end_date1.'"');
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		if($customer<>'ALL' && $customer<>'')
		{
			$this->db->where('customer_id',$customer);
		}

		$res=$this->db->get();
		return $res->num_rows();

}

function check_forbilling_here($location,$product_id,$valid_from,$valid_to,$other_location)
{
		 $this->db->select('a.combination1,a.purchase_entry,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period')
						  ->from('approval_form a')
						   ->where('a.hpcl_location', $location)
							->where('a.current_date >=', $valid_from)
							->where('a.current_date <=', $valid_to)
							->or_where('a.hpcl_location', $other_location)
							->where('a.current_date >=', $valid_from)
							->where('a.current_date <=', $valid_to);
			

		$query = $this->db->get();

		return $query->num_rows();

}


function total_visits_new_customer($start_date,$end_date,$user)
{
	$this->db->select('id')->from('daily_visits')->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)))->where('converted',0);
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}

		$res=$this->db->get();

		// $res1=$this->db->select('id')->from('oldcustomer_visit')->where('added_on>=',$start_date." 00:00:00")->where('added_on<=',$end_date." 23:59:59")->where('added_by',$user)->get();
		


		return $res->num_rows();

}

function get_stock_availability($product_id,$company)
{
		$stock=0;
		$rest=$this->db->select('stock')->from('company_wise_inventory')->where('company_id',$company)->where('itemid',$product_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$stock=$row->stock;
		}

		return $stock;

}


function checkforavailable_Company_QTY($product_name,$qty_for_check,$company)
{
	$not_available=array();
	$not_available[]=0;
	$product_na=array();
	$prd_data='';
	for($i=0;$i<count($product_name);$i++)
	{
		$available=$this->get_stock_availability($product_name[$i],$company);
		$req=$qty_for_check[$i];
		if($req>$available)
		{
			$not_available[]=1;
			$product_na[]=$product_name[$i];
		}
	}

	if(count($product_na)>0)
	{
		$prd_data=implode(',',$product_na);
	}


	return array_sum($not_available)."~".$prd_data;

}

function checkforavailable_Company_QTY_oldproduct($product_name,$company)
{

	$not_available=array();
	$not_available[]=0;
	$product_na=array();
	$prd_data='';
	if($product_name!='')
	{
	
	for($i=0;$i<count($product_name);$i++)
	{
		$prdid=$product_name[$i];
		$req=$this->input->post('qtyedit1'.$prdid);
		$product_id=$this->input->post('productedit1'.$prdid);
		$available=$this->get_stock_availability($product_id,$company);
		if($req>$available)
		{
			$not_available[]=1;
			$product_na[]=$product_id;
		}

	}

		if(count($product_na)>0)
	{
		$prd_data=implode(',',$product_na);
	}

	}

	return array_sum($not_available)."~".$prd_data;

}

function get_prd_not_available($product1,$product2)
{
	$prd1=array();
	$prd2=array();
	$newarr=array();
	$final_data='';
	if($product1!='')
	{
		$products=explode(',',$product1);
		foreach($products as $prd11)
		{
			$prd1[]=$this->getProductName($prd11);
		}
	}



if($product2!='')
	{
		$products1=explode(',',$product2);
		foreach($products1 as $prd12)
		{
			//echo $prd12; exit;
			$prd2[]=$this->getProductName($prd12);
		}
	}

//echo "<pre>"; print_r($prd1)."<br/>"."<pre>"; print_r($prd2);exit;
   $newarr=array_merge($prd1,$prd2);
 



	return $newarr;

}


function get_prd_not_availableNew($product1)
{
	$prd1=array();
	$newarr=array();
	$final_data='';
	if($product1!='')
	{
		$products=explode(',',$product1);
		foreach($products as $prd11)
		{
			$prd1[]=$this->getProductName($prd11);
		}
	}



	return $prd1;

}


function checkforavailable_Company_QTY_single($product_name,$qty_for_check,$company)
{

	$not_available=0;
	$available=$this->get_stock_availability($product_name,$company);
		$req=$qty_for_check;
		if($req>$available)
		{
			$not_available=1;
		}
	

	return $not_available."~";

}

function check_for_not_send_to_tally(){

$today=date('Y-m-d');
	$rest=$this->db->query("SELECT id FROM order_punch WHERE DATE(added_on)<'$today' AND send_so_to_billing=1 AND send_to_tally=0 AND cancelled=0 AND billed_by=0");
	return $rest->num_rows(); 

}

function update_order_date($order_id)
{

	$d=array('added_on'=>date('Y-m-d H:i:s'),'sales_order_addedOn'=>date('Y-m-d H:i:s'));
	$this->db->where('id',$order_id);
	$this->db->update('order_punch',$d);

}

function months_purchase_generic_based_filter($start,$end,$location,$product,$company,$alltime)
	{
		$credit_note_sum=array();
		$credit_note_sum[]=0;
		$start_date=date('Y-m-d',strtotime($start));
		$end_date=date('Y-m-d',strtotime($end));

	 $this->db->select('a.gst,a.id, a.bill_no,a.currentdate')
						  ->from('inventory a');		
				if($alltime==0 || $alltime=='')
				{
				if($start_date <> '' && $end_date <> '' ) {
				$this->db->where('a.currentdate >=', $start_date);
				$this->db->where('a.currentdate <=', $end_date);
				}
				}

      if($location<>'' && $location<>'ALL')
      {
        $this->db->where('a.party',$location);
      }

      if($company<>'' && $company<>'ALL')
      {
        $this->db->where('a.hpcl_billing_company',$company);
      }

    	$query =  $this->db->get();
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				 $this->db->select('a.qty, a.rate')
									->from('inventory_details a')
									->where('a.inventory_id', $row->id);
										if($product<>'' && $product<>'ALL')
									{
										$this->db->where('a.product',$product);
									}
								$sql =	$this->db->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {
							$gst=$row->gst/100;
							$total=$rows->qty * $rows->rate;
							$gst_amount=$total*$gst;
							$grand_amount=$total+$gst_amount;
							$credit_note_sum[] =$grand_amount;

						}
					}
			}

		}



			return array_sum($credit_note_sum);

	}

	function getcurrentcp($product)
	{
		$cp=0;
		$rt=$this->db->select('costprice')->from('presto_instruments_cp')->where('product_id',$product)->order_by('actual_addedOn','DESC')->limit(1)->get();
		if($rt->num_rows()>0)
		{

			foreach($rt->result() as $row);
			$cp=$row->costprice;

		}

		return $cp;

	}


	function getOrderTotalQtyonlynumber($quotation)
	{
		
		  $shortname = '';
		  $sql = $this->db->select('qty, pack_size')
						  ->from('customer_quotation_detail')
						  ->where('quotation_id',$quotation)
						  ->get();

			if($sql->num_rows() > 1) {
				$total_qty = 0;
				$shortname = '';
			} else {
				if($sql->num_rows() > 0) {
					foreach($sql->result() as $row);
						$shortname = $this->getUnitName($row->pack_size);
						$total_qty = $row->qty;
				}
			}
			

			if($total_qty == 0) {
		    	$total = '';
			} else {
				$total = $total_qty;
			}

			return $total;

	}


	function payment_history($customerid)
	{



		$html.'<table width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Sr No.</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Bill Date</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Billing Company</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Sales Order No</b></td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Invoice No</b> </td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Order Amount</b> </td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Debit</b> </td>
<td style="background-color:lightgrey;text-align:center; font-size:14px;"><b>Credit</b> </td>

</tr>';
		$lead_data=array();
		 $this->db->select('a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id')->where('c.id',$customerid);
			$this->db->order_by('a.payment_date','DESC');
		$restey=$this->db->get();
    if($restey->num_rows()>0)
    {
    	$i=1;
    	foreach($restey->result() as $row)
    	{
    		$invoice='';
    		$cno='';
    		$cdate='';

    		if($row->billtype==1)
    		{
    			$pay_type="<span class='btn btn-xs btn-warning'>FIFO</span>";
    			$edit=$row->payment_id;
    		}else
    		{
    			
    			$pay_type="<span class='btn btn-xs btn-success'>AGAINST BILL</span>";
    			if($row->bills<>'')
    			{
    				$invoice=$this->get_invoice_no($row->bills);
    			}else
    			{
    				$invoice='';
    			}
    			$edit="<a href='javascript:;' onclick='delete_payment_data(".$row->payment_id.",".$row->customerpart.");'><i class='fa fa-trash'></i></a>|".$row->payment_id;
    		}

    		if($row->payment_type==1)
    		{
    			$PT="<strong>Cheque</strong>";
    			$cno="<strong>".$row->cheque_no."</strong>";
    			$cdate="<strong>".date('d-m-Y',strtotime($row->cheque_date))."</strong>";

    		}else if($row->payment_type==2)
    		{
    			$PT="<strong>Cash</strong>";
    		}else if($row->payment_type==3)
    		{
    			$PT="<strong>NEFT</strong>";
    			$cno="<strong>".$row->neft_trans_no."</strong>";
    		}else
    		{
    			$PT='';
    		}


    		$name=$this->getusername($row->addedBy);
    		$invoice_no=$this->get_invoice($row->id);

    		$lead_data[] = array('sr_no'=>$i,
				'payment_date'=>date('d-M-Y',strtotime($row->payment_date)),
				'customer'=>$row->company_name,
				'payment_mode'=>$PT,
				'cheque_no'=>$cno."<br/>".$cdate,
				'amount'=>$row->amount,
				'bill_type'=>$pay_type,
				'payment_settled'=>$invoice,
				'addedOn'=>date('d-M-Y',strtotime($row->addedOn)),
				'addedby'=>$name,
				'editpaymenthistorydata'=>$edit
				);

			 			 
		$i++;
		}
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);

	}


	function empty_barrel_report_list() {
			$data = array();
			$data[] = 0;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$party=$this->uri->segment(5);
    $product=$this->uri->segment(6);
    $company=$this->uri->segment(7);
    $alltime=$this->uri->segment(8);
		$data = array();
		$i=1;
				$this->db->select('e.instruments_name,c.product, c.density, c.qty,c.rate,a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname, e.volume, c.secondproduct')
				->from('inventory_details c')
				->join('inventory a','c,inventory_id=a.id')
				->join('vendors d', 'd.id=a.party','left')
				->join('store_rack_location s', 's.id=a.hpcl_billing_company','left')
				->join('presto_instruments e','c.product=e.id');
				$this->db->where('a.currentdate >', '2023-07-31');
            	if($alltime==0 || $alltime=='')
				{
				if($start_date <> '' && $end_date <> '' ) {
				$this->db->where('a.currentdate >=', $start_date);
				$this->db->where('a.currentdate <=', $end_date);
				}
				}

      if($party<>'' && $party<>'ALL')
      {
        $this->db->where('a.party',$party);
      }

      if($company<>'' && $company<>'ALL')
      {
        $this->db->where('a.hpcl_billing_company',$company);
      }

       if($product<>'' && $product<>'ALL')
      {
        $this->db->where('c.product',$product);
      }
      $this->db->where('c.density>',0);
      $this->db->where('c.bulkproducttype',2);

	  $query =  $this->db->order_by('a.currentdate')->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$volumeval  = 0;
				$secondproductname = "";
			$qq = $this->db->select('volume, instruments_name')->from('presto_instruments')->where('id',$row->secondproduct)->get();	
			if($qq->num_rows()>0){
				foreach($qq->result() as $seconditemval);

					$volumeval = $seconditemval->volume;
					$secondproductname = $seconditemval->instruments_name;
			}	

			  $pdetails=$row->qty*$row->rate;
              $ggst=$row->gst/100;
              $gst_amount=$pdetails*$ggst;
              $grandtotal=$pdetails+$gst_amount;


              $density = $row->density;
              $mass = $row->qty*$density;
              
              if($volumeval ==0){
				$volume = 0;
              }else{
              	$volume = $row->qty/$volumeval;
              }
              




              $data[] = ceil($volume);

						$i++;
				
					}
				
				
			}

			
			
		return array_sum($data);
	}



	function empty_barrel_purchase_report_list() {
			$data = array();
			$data[] = 0;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$party=$this->uri->segment(5);
    $product=$this->uri->segment(6);
    $company=$this->uri->segment(7);
    $alltime=$this->uri->segment(8);
		$data = array();
		$i=1;
				$this->db->select('e.instruments_name,c.product, c.density, c.qty,c.rate,a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname, e.volume, c.secondproduct')
				->from('inventory_details c')
				->join('inventory a','c,inventory_id=a.id')
				->join('vendors d', 'd.id=a.party','left')
				->join('store_rack_location s', 's.id=a.hpcl_billing_company','left')
				->join('presto_instruments e','c.product=e.id');
				$this->db->where('a.currentdate >', '2023-07-31');
				$this->db->where('c.product',348);
        //     	if($alltime==0 || $alltime=='')
				// {
				// if($start_date <> '' && $end_date <> '' ) {
				// $this->db->where('a.currentdate >=', $start_date);
				// $this->db->where('a.currentdate <=', $end_date);
				// }
				// }

      // if($party<>'' && $party<>'ALL')
      // {
      //   $this->db->where('a.party',$party);
      // }

      // if($company<>'' && $company<>'ALL')
      // {
      //   $this->db->where('a.hpcl_billing_company',$company);
      // }

      //  if($product<>'' && $product<>'ALL')
      // {
      //   $this->db->where('c.product',$product);
      // }
      // $this->db->where('c.density>',0);
      // $this->db->where('c.bulkproducttype',2);

	  $query =  $this->db->order_by('a.currentdate')->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$volumeval  = 0;
				$secondproductname = "";
		
	 $data[] = ceil($row->qty);

						$i++;
				
					}
				
				
			}

			
			
		return array_sum($data);
	}


	function total_visits_new_no_lead($start_date,$end_date,$user)
{
	$data=array();
	$data[]=0;
	$this->db->select('id')->from('daily_visits')->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)));
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
		}
		$res=$this->db->get();
		if($res->num_rows()>0){
		foreach($res->result() as $row)
		{

			$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->id)->get();
			if($rty->num_rows()==0)
			{
				$data[]=1;
			}

		}
		}

		return array_sum($data);
}


	function get_invoice($id)
	{
		$inv=array();
		$resteyu=$this->db->select('invoice_no')->from('order_punch')->where('payment_id',$id)->get();
		if($resteyu->num_rows()>0)
		{
			foreach($resteyu->result() as $restete)
			{
				$inv[]=$restete->invoice_no;
			}
		}

		return implode(',',$inv);
	}

	function getbankaccount($bank1)
	{
		$bank='';
		$rty=$this->db->select('id,bank_name,account')->from('store_rack_location_account')->where('id',$bank1)->get();
		if($rty->num_rows()>0)
		{
		foreach($rty->result() as $row);
		$bank=$row->bank_name."<br/>".$row->account;

		}

		return $bank;

	}

	function get_invoice_no($order_ids)
	{
		$d1=array();
		$resteyu=$this->db->select('invoice_no')->from('order_punch')->where_in('id',$order_ids,false)->get();
		if($resteyu->num_rows()>0)
		{
			foreach($resteyu->result() as $d)
			{
				$d1[]=$d->invoice_no;
			}

		}
		if(count($d1)>0)
		{
			return implode(',',$d1);
		}else
		{
			return null;
		}
	} 


	function get_invoice_data_array($id)
	{
		$inv=array();
		$resteyu=$this->db->select('id')->from('order_punch')->where('payment_id',$id)->get();
		if($resteyu->num_rows()>0)
		{
			foreach($resteyu->result() as $restete)
			{
				$inv[]=$restete->id;
			}
		}

		return $inv;
	}

	function get_target($start,$end,$user)
	{
		$target=0;

		if($user<>'ALL' && $user<>'')
		{
			$rest=$this->db->select('amount,previous')->from('sales_agent_target')->where('period',$start)->where('agent',$user)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$target=$row->amount+$row->previous;
			}

		}

		return $target;


	}


function total_visits_only_new($start_date,$end_date,$user)
{
	$data=array();
	$data[]=0;
	$this->db->select('id')->from('daily_visits')->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)));
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
			
		}
		$this->db->where('converted',0);
		$res=$this->db->get();
		if($res->num_rows()>0){
		foreach($res->result() as $row)
		{

				$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->id)->get();
				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->id)->get();
				if($rty->num_rows()==0 && $rty1->num_rows()==0)
				{
				$data[]=1;
				}
		}

		}

		return array_sum($data);
}


function incomplete_visit($month,$user)
	{
			$data=array();
			$data[]=0;
		  $start_date=date('Y-m-01',strtotime($month));
  		$end_date=date('Y-m-t',strtotime($month));

//->where('create_date>=',$start_date)->where('create_date<=',$end_date)
		 $this->db->select('id')->from('daily_visits')->where('converted',0);
		 if($user<>'ALL')
		 {
		 	$this->db->where('added_by',$user);
		 }

		$res=$this->db->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row)
			{
				$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->id)->get();
				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->id)->get();
				if($rty->num_rows()==0 && $rty1->num_rows()>0)
				{
				$data[]=1;
				}
			}

		}
		
		return array_sum($data);
	}


function incomplete_visits_only_new($start_date,$end_date,$user)
{
	$data=array();
	$data[]=0;
	//->where('create_date>=',date('Y-m-d',strtotime($start_date)))->where('create_date<=',date('Y-m-d',strtotime($end_date)))
	$this->db->select('id')->from('daily_visits');
	if($user<>'ALL' && $user<>'')
		{
			$this->db->where('added_by',$user);
			
		}
		$this->db->where('converted',0);
		$res=$this->db->get();
		if($res->num_rows()>0){
		foreach($res->result() as $row)
		{

				$rty1=$this->db->select('id')->from('incomplete_visit_data')->where('visit_id',$row->id)->get();
				$rty=$this->db->select('id')->from('leads')->where('visit_id',$row->id)->get();
				if($rty->num_rows()==0 && $rty1->num_rows()>0)
				{
				$data[]=1;
				}
		}

		}

		return array_sum($data);
}

function get_visit_details($id)
{
	$ht='';

	$a=array();
	$resteu=$this->db->select('a.remarks,a.added_on,a.added_by,b.first_name,b.last_name')->from('daily_visits a')->join('system_users b','a.added_by=b.user_id')->where('a.id',$id)->get();
	if($resteu->num_rows()>0)
	{
		foreach($resteu->result() as $row);
		$a[]=1;
	$ht.="<table class='table table-bordered' style='width:700px'>
						<thead>
						<tr>
						<th style='padding:0px;width:100px;'></th>
						<th style='padding:0px;width:200px;'>Remarks</th>
						<th style='padding:0px;width:200px;'>Added On</th>
						<th style='padding:0px;width:300px;'>Added By</th>
						</tr>
						</thead>
						<tbody>";

					$ht.="<tr>
					<td style='padding:0px;'>Visit 1</td>
					<td style='padding:0px;'>".$row->remarks."</td>
					<td style='padding:0px;'>".date('d-M-Y H:i',strtotime($row->added_on))."</td>
					<td style='padding:0px;'>".$row->first_name." ".$row->last_name."</td>
					</tr>";

					$rty1=$this->db->select('a.id,a.remarks,a.addedOn,a.addedBy,b.first_name,b.last_name')->from('incomplete_visit_data a')->join('system_users b','a.addedBy=b.user_id')->where('a.visit_id',$id)->get();
			if($rty1->num_rows()>0)
				{
					$y=2;
					foreach($rty1->result() as $row)
					{
						$a[]=1;
						$ht.="<tr>
						<td style='padding:0px;'>Visit ".$y."</td>
						<td style='padding:0px;'>".$row->remarks."</td>
							<td style='padding:0px;'>".date('d-M-Y H:i',strtotime($row->addedOn))."</td>
					<td style='padding:0px;'>".$row->first_name." ".$row->last_name."</td>
						</tr>";
					$y++;
					}

				}

	}else
	{
		$ht.="<tr>
						<td style='padding:0px;' colspan='2'>No Data Available</td>
					</tr>";
	}


						$ht.="</tbody> </table>";

						return $ht.'~'.array_sum($a);


}

function transfer_history($id,$userid)
{
	$d='';
	$restey=$this->db->select('a.addedOn,b.first_name,b.last_name')->from('visit_transfer a')->join('system_users b','a.from_user=b.user_id')->where('a.to_user',$userid)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$d.="<strong style='color:red;font-weight:bold;'>Transfered On ".date('d-M-Y H:i',strtotime($row->addedOn))." from ".$row->first_name." ".$row->last_name."</strong><br/><br/>";
		}
	}

	return $d;
}


function get_months_credit_claim_amount_type2_new_without_payment($start_date,$end_date,$location,$product)
{

$commission=array();
$commission[]=0;
$transport[]=array();
$transport[]=0;
$start_date =$start_date;
$end_date =$end_date;

$query=$this->db->select('f.volume,a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
->from('type_2_3_invoice_particular d')
->join('type_2_3_invoice a','d.invoice_id=a.id')
->join('presto_instruments f','f.id=d.product_id')
->join('hpcl_location c', 'c.id=a.shipping_from')
->join('hpcl_direct_customer e', 'e.id=a.customer')
->where('a.invoice_date>=',$start_date)
->where('a.invoice_date<=',$end_date);
$this->db->order_by('a.invoice_date','ASC');
$query=$this->db->get();
 if ($query->num_rows() > 0) {
  foreach ($query->result() as $rows) {

$SSDATE=$rows->invoice_date;
  	 $claim_am=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$SSDATE);
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];

			$collection_data=array();
			$collection_data[]=0;
			// $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
			// $payment_rows = $indata->num_rows();
			// echo $payment_rows; 
			// exit;
			$collection_array = array();
			$collection_amt = array();
			// if ($indata->num_rows() > 0) {
			$l = 0;
			// foreach ($indata->result() as $invdata) {

			$payment_rows=1;
				 if ($payment_rows > 1) {
				 if($l==0)
				 {
			$com=$rows->pqty * $this_invoice_comm;
			$tra= $rows->pqty * $this_invoice_trans;
			$commission[]=$com+$tra;
			$transport[]=$tra;
				}
			}else
			{
			$com=$rows->pqty * $this_invoice_comm;
			$tra= $rows->pqty * $this_invoice_trans;
			$commission[]=$com+$tra;
			}


		$l++;
	

			}

  }


//echo "<pre>"; print_r($commission); exit;
return array_sum($commission);


}

function getdistributorName($id)
{
	$this->db->select('firm_name')
	->from('distributor');
	$query = $this->db->get();
	if($query->num_rows()>0)
	{
	$res = $query->result();
	foreach($res as $patient_type);
	return $patient_type->firm_name;
	}else
	{
		return null;
	}
}


	function wonordergetProductsTabular($lead_id) {

		$ht='';
			$ht.="<table class='table table-bordered' style='width:300px'>
						<thead>
						<tr>
						<th style='padding:2px;width:210px;'>Sr. No.</th>
						<th style='padding:2px;width:450px;'>Our Product</th>
						<th style='padding:2px;width:350px;'>Qty/Unit</th>
						<th style='padding:2px;width:350px;'>Final Amount (INR)</th>
						</tr>
						</thead>
						<tbody>";

					$qqty[] = 0;
					$finalttl[] = 0;
	   	$instruments = array();
		$sql = $this->db->select('a.id as leadproduct,a.competitor_product, a.product_id, a.qty, a.price, a.percent_amt, a.net_price, a.flag, b.id, b.mvalue, b.instruments_name,a.packsize, b.unit as unitname')
						->from('lead_products a')
	   					->join('presto_instruments b', 'b.id=a.product_id', 'left')
	   					->join('units c', 'c.id=b.unit', 'left')
	   					->where('a.lead_id', $lead_id)
	   					->get();

	   					if($sql->num_rows()>0)
	   					{
	   						$i=1;
	   						foreach($sql->result() as $row)
	   						{
													if($row->packsize==1)
													{
													$pack="Drum";
													}else if($row->packsize==2)
													{
													$pack="Bucket";
													}else if($row->packsize==3)
													{
													$pack="Bulk";
													}else if($row->packsize==4)
													{
													$pack="Cans";
													}else if($row->packsize==5)
													{
													$pack="Kgs";
													}else
													{
													$pack='';
													}
													$qqty[] = $row->qty;
													$finalttl[] = $row->net_price;

												$ht.="<tr>
												<td style='padding:2px;'>".$i."</td>
												<td style='padding:2px;'>".ucwords(strtolower($row->instruments_name))."</td>
												<td style='padding:2px;'>".ucwords(strtolower($row->qty." ".$row->unitname))."</td>
												<td style='padding:2px;'>".round($row->net_price)."</td>
												</tr>";
								$i++;
								}

					$ht.="<tr>
					<td colspan='3'>Total Order Qty: ".array_sum($qqty)."</td>
					<td>".array_sum($finalttl)."</td></tr>";

	   					}else
	   					{
	   						$ht.="<tr>
												<td style='padding:2px;' colspan='4'>No Products Found</td>
											
												</tr>";

	   					}

	   						$ht.="</tbody> </table>";


	   						return $ht;

	   	
	   }

	      function getall_leads_worked($start,$end,$user)
  {

  	$d=array();
  	$resdata='';
  	$this->db->select('a.lead_id')->from('progress_remarks a')->join('lead_assigned_to_team_member b','a.lead_id=b.lead_id')->where('a.added_on>=',$start)->where('a.added_on<=',$end);
			if($user<>'' && $user<>'ALL' && $user<>'NA')
			{
			$this->db->where_in('b.member_id',$user,false);
			}
	 		$resteu=$this->db->group_by('a.lead_id')->get();
	 		if($resteu->num_rows()>0)
	 		{
	 			foreach($resteu->result() as $row)
	 			{
	 				$d[]=$row->lead_id;
	 			}
	 		}


	 		if(count($d)>0)
	 		{
	 			$resdata = "'" . implode ( "', '", $d ) . "'";
	 		}

	 		return $resdata;


  }

  function getOppNo($op_type,$mach_type)
  {
			$financial=$this->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}


  
  	$resty=$this->db->select('unique_no')->from('leads')->where('added_on>=',$start_date)->where('added_on<=',$end_date)->order_by('unique_no','DESC')->limit(1)->get();
  	if($resty->num_rows()>0)
  	{
  		foreach($resty->result() as $row);
  		$u_no=$row->unique_no+1;
  		$un_no1=str_pad($u_no, 4, '0', STR_PAD_LEFT);
  	}else
  	{
  		$u_no=1;
  		$un_no1=str_pad(1, 4, '0', STR_PAD_LEFT);;
  	}

  	$fy_year=$this->getFinancialYear(date('Y-m-d'));

  	if($op_type==1)
  	{
  		$C="DOM";
  	}else
  	{
  		$C="EXP";
  	}

  	if($mach_type==1)
  	{
  		$D="L";
  	}else if($mach_type==2)
  	{
  			$D="P";
  	}else if($mach_type==3)
  	{
  		$D="C";
  	}else{
  		$D="";
  	}

  	$oppno="SPM/".$C."/".$D."/".$un_no1."/".$fy_year;

  	echo $oppno."~".$u_no;

  }

   function runtimegetOppNo($op_type,$mach_type)
  {
			$financial=$this->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}


  
  	$resty=$this->db->select('unique_no')->from('leads')->where('added_on>=',$start_date)->where('added_on<=',$end_date)->order_by('unique_no','DESC')->limit(1)->get();
  	if($resty->num_rows()>0)
  	{
  		foreach($resty->result() as $row);
  		$u_no=$row->unique_no+1;
  		$un_no1=str_pad($u_no, 4, '0', STR_PAD_LEFT);
  	}else
  	{
  		$u_no=1;
  		$un_no1=str_pad(1, 4, '0', STR_PAD_LEFT);;
  	}

  	$fy_year=$this->getFinancialYear(date('Y-m-d'));

  	if($op_type==1)
  	{
  		$C="DOM";
  	}else
  	{
  		$C="EXP";
  	}

  	if($mach_type==1)
  	{
  		$D="L";
  	}else if($mach_type==2)
  	{
  			$D="P";
  	}else{
  		$D="C";
  	}

  	$oppno="SPM/".$C."/".$D."/".$un_no1."/".$fy_year;

  	return $oppno."~".$u_no;

  }




  function getFinancialYear($inputDate, $format = "y") {
    $date = date_create($inputDate);
    if (date_format($date, "m") >= 4) { // On or After April (FY is current year - next year)
        $financial_year = date_format($date, $format) . '-' . (date_format($date, $format) + 1);
    } else { // On or Before March (FY is previous year - current year)
        $financial_year = (date_format($date, $format) - 1) . '-' . date_format($date, $format);
    }

    return $financial_year;
}

function getbrandName($id)
{
	$name='';
	$ret=$this->db->select('name')->from('company_brand')->where('id',$id)->get();
	if($ret->num_rows()>0)
	{
		foreach($ret->result() as $ro);
		$name=$ro->name;
	}

	return $name;

}


function getLeadDetailsNew($lead_id) {
		 $res = '';
		 $query = $this->db->select('a.unique_id,b.id as customer_id,a.customer_name,a.mobile_no,a.email_id,a. patient_type_id, a.website,a.contact_no,a.title,a.postal_address, a.city,b.company_name,machine_type,a.country')
						   ->from('leads a')
						   ->join('customer_detail b','a.company_name=b.id')
						   ->where('a.id', $lead_id)
						   ->get();

			if($query->num_rows() > 0) {
				$res = $query->result();
			} 

		return $res;
	}

	function getLeadStagenames($lead_id) {
		 $query = $this->db->select('a.lead_name')
						   ->from('lead_stage a')
						
						   ->where('a.lead_id', $lead_id)
						
						   ->get();

			if($query->num_rows() > 0) {
				foreach($query->result() as $row);
				$res = $row->lead_name;
			} else {
				$res = '';

			}

			return $res; 

	}

	function getquoteBasicData($record_id)
	{
		$data=array();
		$rest=$this->db->select('a.mach_model_no,a.cantilever,a.ref_no,a.quotation_date,a.customer_id,a.currency,a.country,a.machine_name,a.machine_model_no,a.lead_id,a.product_id, b.country_name,a.special_notes,gst_actual,validity')->from('quotation_customer_data a')->join('countries b','a.country=b.country_id','left')->where('a.id',$record_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$data[]=$row->ref_no;
				$data[]=$row->quotation_date;
				$data[]=$row->customer_id;
				$data[]=$row->currency;
				$data[]=$row->country;
				$data[]=$row->machine_name;
				$data[]=$row->machine_model_no;
				$data[]=$row->lead_id;
				$data[]=$row->product_id;
				$data[] = $row->country_name;
				$data[] = $row->cantilever;
				$data[] = $row->mach_model_no;
				$data[] = $row->special_notes;
				$data[] = $row->gst_actual;
				$data[] = $row->validity;
			}
		}

		return $data;

	}

	function getUserDetails($lead_id)
	{
		$data=array();
		$d=$this->db->select('b.user_id,b.first_name,b.last_name,b.contact_number,b.email')->from('leads a')->join('system_users b','a.added_by=b.user_id')->where('a.id',$lead_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);
			$data[]=$row->first_name." ".$row->last_name;
			$data[]=$row->contact_number;
			$data[]=$row->email;
			$data[]=$row->user_id;
		}

		return $data;
	}

	function getannexture_1($record_id)
	{

		$data=array();
		$d=$this->db->select('machine_type,machine_orientation,product_to_be_packed,product_name,pouch_size_type,qty_to_be_packed,horizontal_sealing_width,vertical_sealing_width,perforation_pitch,perforationstyle,batchcut,typeofsealing,plc_make,power_supply, liquidviscositydata, liquidconductivitydata, powderdensitydata, powderdfrdata, powdermoisturecontentdata,liquid_option,powder_option,non_viscous_option,viscous_option,piston_filler_option,follow_meter_option,free_flow_option,weigher_system_option,liner_weigher_option,mult_head_weigher_option,volumetric_cap_option,non_free_flow_option,cup_filler_option')->from('quotation_annexture_1')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);
			$data[]=$row->product_to_be_packed;
			$data[]=$row->product_name;
			$data[]=$row->pouch_size_type;
			$data[]=$row->qty_to_be_packed;
			$data[]=$row->horizontal_sealing_width;
			$data[]=$row->vertical_sealing_width;
			$data[]=$row->perforation_pitch;
			$data[]=$row->typeofsealing;
			$data[]=$row->plc_make;
			$data[]=$row->power_supply;
			$data[]=$row->liquidviscositydata;
			$data[]=$row->liquidconductivitydata;
			$data[]=$row->powderdensitydata;
			$data[]=$row->powderdfrdata;
			$data[]=$row->powdermoisturecontentdata;
			$data[]=$row->perforationstyle;
			$data[]=$row->batchcut;
			$data[]=$row->liquid_option;
			$data[]=$row->powder_option;
			$data[]=$row->non_viscous_option;
			$data[]=$row->viscous_option;
			$data[]=$row->piston_filler_option;
			$data[]=$row->follow_meter_option;
			$data[]=$row->free_flow_option;
			$data[]=$row->weigher_system_option;
			$data[]=$row->liner_weigher_option;
			$data[]=$row->mult_head_weigher_option;
			$data[]=$row->volumetric_cap_option;
			$data[]=$row->non_free_flow_option;
			$data[]=$row->machine_type;
			$data[]=$row->machine_orientation;
			$data[]=$row->cup_filler_option;
		}

		return $data;

	}

	function quotation_cum_tech_spec($record_id)
	{

		$data=array();
		$d=$this->db->select('model')->from('quotation_cum_tech_spec')->where('record_id',$record_id)->where('flag',1)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);
			$data[]=$row->model;
		
		}

		return $data;

	}

		function quotation_cum_tech_spec_dynamic($record_id)
	{

		$data=array();
		$d=$this->db->select('model,id')->from('quotation_cum_tech_spec')->where('record_id',$record_id)->where('flag',0)->get();
		if($d->num_rows()>0)
		{
			return $d->result();
			
		
		}

		

	}


	function getannexture_2($record_id)
	{

		$data=array();
		$d=$this->db->select('filling_accuracy,machinemodel,sealingstyle,speed,actual_speed,no_of_track,leminate_specification,product_to_be_packed,filling_capacity,pouch_size,sealing_drives,perforation_and_cutting,laminate_draw,laminate_tracking,electrical_spec,layout_dimensions,machine_weight, gross_weight,compressed_air, laminatereeldia, laminatereelcoredia, compressedairbar')->from('quotation_annexure_2')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);
			$data[]=$row->machinemodel;
			$data[]=$row->sealingstyle;
			$data[]=$row->speed;
			$data[]=$row->no_of_track;
			$data[]=$row->leminate_specification;
			$data[]=$row->product_to_be_packed;
			$data[]=$row->filling_capacity;
			$data[]=$row->pouch_size;
			$data[]=$row->sealing_drives;
			$data[]=$row->perforation_and_cutting;
			$data[]=$row->laminate_draw;
			$data[]=$row->laminate_tracking;
			$data[]=$row->electrical_spec;
			$data[]=$row->layout_dimensions;
			$data[]=$row->machine_weight;
			$data[]=$row->compressed_air;
			$data[]=$row->actual_speed;
			$data[]=$row->laminatereeldia;
			$data[]=$row->laminatereelcoredia;
			$data[]=$row->gross_weight;
			$data[]=$row->compressedairbar;
			$data[]=$row->filling_accuracy;
		}

		return $data;

	}

	function getPriceData($record_id,$flag){

		$data=array();
		$d=$this->db->select('description,qty,price')->from('quotation_annexture_4')->where('record_id',$record_id)->where('flag',$flag)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);
			$data[]=$row->description;
			$data[]=$row->qty;
			$data[]=$row->price;
		}

		return $data;

	}

	function getOptionalData($record_id)
	{
			$data=array();
		$d=$this->db->select('id,description,value,price')->from('quotation_optional')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row)
			{
			$data[]=array('description'=>$row->description,'value'=>$row->value,'price'=>$row->price,'id'=>$row->id);
			}
		}

		return $data;
	}

	function getotherinformation($record_id)
	{
		$data=array();
		$d=$this->db->select('terms_value,delivery_value,pocket_expense,support_statement_value')->from('quotation_other_information')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
		foreach($d->result() as $row);
		$data[]=$row->terms_value;
		$data[]=$row->delivery_value;
		$data[]=$row->pocket_expense;
		$data[]=$row->support_statement_value;
		}

		return $data;

	}

	

	function getQuoteData($record_id)
	{

$basicData=$this->getquoteBasicData($record_id);
if(count($basicData)>0)
{
	$ref_no=$basicData[0];
	$quotation_date=$basicData[1];
	$customer_id=$basicData[2];
	$currency=$basicData[3];
	$country=$basicData[4];
	$machine_name=$basicData[5];
	$machine_model_no=$basicData[6];
	$lead_id=$basicData[7];
	$product_id=$basicData[8];
	$countryname=$basicData[9];
	$special_notes=$basicData[12];
	$gst_actual=$basicData[13];
	$validity=$basicData[14];
	$user_detail=$this->getUserDetails($lead_id);
	$export_domestic=$this->CheckexportDomestic($lead_id);
	$merchantexport = $this->checkmerchantexport($lead_id);
	//echo $merchantexport; exit;
	if(count($user_detail)>0)
	{
		$agent=$user_detail[0];
		$contact_no=$user_detail[1];
		$email=$user_detail[2];
		$agent_id=$user_detail[3];
		
	}else
	{
		$agent='';
		$contact_no='';
		$email='';
		$agent_id=0;
	}

	$customer_detail=$this->getCustomerdetail($customer_id);
	//echo "<pre>"; print_r($customer_detail); exit;
	if(count($customer_detail)>0)
	{
		$customerName=$customer_detail[1];
		$customercontactperson = $customer_detail[0];
	}else{
		$customerName='';
		$customercontactperson = '';
	}
}else
{
	$ref_no='';
	$quotation_date='';
	$customer_id='';
	$currency='';
	$country='';
	$machine_name='';
	$machine_model_no='';
	$customerName='';
	$countryname = '';
}

$annexture_1=$this->getannexture_1($record_id);
// echo "<pre>"; print_r($annexture_1); exit;
if(count($annexture_1)>0)
{
	$product_to_be_packed=$annexture_1[0];
	if($product_to_be_packed==1)
	{
	$product_to_be_packed='Liquid/ Paste';
	}else if($product_to_be_packed==2)
	{
	$product_to_be_packed='Powder/ Granules';
	}else if($product_to_be_packed==3)
	{
	$product_to_be_packed='Granules';
	}else if($product_to_be_packed==4)
	{
	$product_to_be_packed='Pouches';
	}else if($product_to_be_packed==5)
	{
	$product_to_be_packed='Cartons';
	}else
	{
	$product_to_be_packed='';
	}
	//echo $product_to_be_packed; exit;
	$product_name=$annexture_1[1];
	$pouch_size_type=$annexture_1[2];
	$qty_to_be_packed=$annexture_1[3];
	$horizontal_sealing_width=$annexture_1[4];
	$vertical_sealing_width=$annexture_1[5];
	$perforation_pitch=$annexture_1[6];
	$typeofsealing=$annexture_1[7];
	$plc_make=$annexture_1[8];
	$power_supply=$annexture_1[9];
	$liquidviscositydata=$annexture_1[10];
	$liquidconductivitydata=$annexture_1[11];
	$powderdensitydata=$annexture_1[12];
	$powderdfrdata=$annexture_1[13];
	$powdermoisturecontentdata=$annexture_1[14];
	$perforationstyle=$annexture_1[15];
	$batchcut=$annexture_1[16];
	$liquid_option=$annexture_1[17];
	$powder_option=$annexture_1[18];
	$non_viscous_option=$annexture_1[19];
	$viscous_option=$annexture_1[20];
	$piston_filler_option=$annexture_1[21];
	$follow_meter_option=$annexture_1[22];
	$free_flow_option=$annexture_1[23];
	$weigher_system_option=$annexture_1[24];
	$liner_weigher_option=$annexture_1[25];
	$mult_head_weigher_option=$annexture_1[26];
	$volumetric_cap_option=$annexture_1[27];
	$non_free_flow_option=$annexture_1[28];
	$machtype=$annexture_1[29];
	$orientation=$annexture_1[30];

}else
{

	$product_to_be_packed='';
	$product_name='';
	$pouch_size_type='';
	$qty_to_be_packed='';
	$horizontal_sealing_width='';
	$vertical_sealing_width='';
	$perforation_pitch='';
	$typeofsealing='';
	$plc_make='';
	$power_supply='';
	$liquidviscositydata='';
	$liquidconductivitydata='';
	$powderdensitydata='';
	$powderdfrdata='';
	$powdermoisturecontentdata='';
	$batchcut= '';
	$perforationstyle='';
	$liquid_option='';
	$powder_option='';
	$non_viscous_option='';
	$viscous_option='';
	$piston_filler_option='';
	$follow_meter_option='';
	$free_flow_option='';
	$weigher_system_option='';
	$liner_weigher_option='';
	$mult_head_weigher_option='';
	$volumetric_cap_option='';
	$non_free_flow_option='';
	$machtype='';
	$orientation='';
}


$quotation_cum=$this->quotation_cum_tech_spec($record_id);
if(count($quotation_cum)>0)
{
	$model=$quotation_cum[0];
	$no_of_axis_in_machine="";
	$axis_detail="";

}else
{
	$model='';
	$no_of_axis_in_machine='';
	$axis_detail='';
}

$axis_data='';
$axis_sum=array();
$axis_sum[]=0;
$axis_details=$this->get_axis_detailsNew($record_id);
if(count($axis_details)>0)
{
	for($o=0;$o<count($axis_details['description']);$o++)
	{

		$axis_data.='<li>'.$axis_details['description'][$o].'-'.$axis_details['axiscount'][$o].'</li>';
		$axis_sum[]=$axis_details['axiscount'][$o];
	}
}

if(array_sum($axis_sum)>0)
{
$axis_data_new='<b>No. of Axis in Machine: <strong>'.array_sum($axis_sum).'</strong></b>';
$axis_data_new.='<ul style="padding:0px;">'.$axis_data.'</ul>';
}else
{
	$axis_data_new='';
}


$quotation_cum_tech_specdynamic=$this->quotation_cum_tech_spec_dynamic($record_id);
//echo "<pre>"; print_r($quotation_cum_tech_specdynamic); exit;
$getannexture_2=$this->getannexture_2($record_id);
// echo "<pre>"; print_r($getannexture_2); exit;
if(count($getannexture_2)>0)
{
$machinemodel=$getannexture_2[0];
$sealingstyle=$getannexture_2[1];
$speed=$getannexture_2[2];
$no_of_track=$getannexture_2[3];
$leminate_specification=$getannexture_2[4];
$product_to_be_packed1=$getannexture_2[5];
$filling_capacity=$getannexture_2[6];
$pouch_size=$getannexture_2[7];
$sealing_drives=$getannexture_2[8];
$perforation_and_cutting=$getannexture_2[9];
$laminate_draw=$getannexture_2[10];
$laminate_tracking=$getannexture_2[11];
$electrical_spec=$getannexture_2[12];
$layout_dimensions=$getannexture_2[13];
$filling_accuracy=$getannexture_2[21];
$r=explode('<br>',$layout_dimensions);
if(count($r)>0)
{
	$lay="L-".$r[0]."<br/>";
	$lay.="W-".$r[1]."<br/>";
	$lay.="H-".$r[2];
} else
{
	$lay=$layout_dimensions;
}

$machine_weight=$getannexture_2[14];
$compressed_air=$getannexture_2[15];
$actual_speed=$getannexture_2[16];
$laminatereeldia=$getannexture_2[17];
$laminatereelcoredia=$getannexture_2[18];
$gross_weight=$getannexture_2[19];
$compressedairbar=$getannexture_2[20];
}else
{
$lay='';
$machinemodel='';
$sealingstyle='';
$speed='';
$no_of_track='';
$leminate_specification='';
$product_to_be_packed='';
$filling_capacity='';
$pouch_size='';
$sealing_drives='';
$perforation_and_cutting='';
$laminate_draw='';;
$laminate_tracking='';
$electrical_spec='';
$layout_dimensions='';
$machine_weight='';
$compressed_air='';
$actual_speed = '';
$laminatereeldia='';
$laminatereelcoredia='';
$gross_weight = '';
$compressedairbar = '';
$filling_accuracy='';
}

$layoutimage=$this->get_quotation_layout_img($record_id);
$fillingimage=$this->get_quotation_machine_filling_image($record_id);
$kldimage=$this->get_quotation_machine_kld_image($record_id);
$qty_data=$this->getQtyPackedData($record_id);
// echo "<pre>"; print_r($qty_data); exit;
if(count($qty_data)>0)
{
	if($machine_name=='VFFS COLLAR TYPE TWIN HEAD MACHINE' || $machine_name=='VFFS COLLAR TYPE MACHINE' || $machine_name=='HFFS SINGLE TRACK MACHINE' || $machine_name=='HFFS MULTI TRACK MACHINE' || $machine_name=='HFFS PICK FILL SEAL'){
	// echo "<pre>"; print_r($qty_data); exit;
	$pouch_size_type='<table border="1" ruled="all" style=" padding:3px;"><tbody>';
	$pouch_size_type.='<tr>
	<th style="text-align:center;font-size:11px;">Qty</th>
	<th style="text-align:center;width:35%;font-size:12px;">Size (mm)</th>';
	$pouch_size_type.='<th style="text-align:center;font-size:11px;width:20%">Gusset</th>
	<th style="text-align:center;width:20%;font-size:11px;" >Punch Hole</th>';

	$pouch_size_type.='</tr>';

	for($r=0;$r<count($qty_data['qty']);$r++)
	{

			$pouch_size_type.='<tr>
			<td style="text-align:center;">'.$qty_data['qty'][$r].' '.$qty_data['unit'][$r].'</td>
			<td style="text-align:center;">'.$qty_data['length'][$r].'x'.$qty_data['width'][$r].'x'.$qty_data['height'][$r].'</td>';
				
			$pouch_size_type.='<td style="text-align:center;">'.$qty_data['gusset'][$r].'</td>
			<td style="text-align:center;">'.$qty_data['punchhole'][$r].'</td>';
			
			$pouch_size_type.='</tr>';


	}


	$pouch_size_type.='</tbody></table>';
}else{
	$pouch_size_type='<table border="1" ruled="all" style=" padding:3px;"><tbody>';
	$pouch_size_type.='<tr>
	<th style="text-align:center;font-size:11px;">Qty</th>
	<th style="text-align:center;font-size:12px;">Size (LxWxH) (mm)</th>';


	$pouch_size_type.='</tr>';
// echo "<pre>"; print_r($qty_data); exit;
	for($r=0;$r<count($qty_data['qty']);$r++)
	{

			$pouch_size_type.='<tr>
			<td style="text-align:center;">'.$qty_data['qty'][$r].' '.$qty_data['unit'][$r].'</td>
			<td style="text-align:center;">'.$qty_data['length'][$r].'x'.$qty_data['width'][$r].'x'.$qty_data['height'][$r].'</td>';
				
		$pouch_size_type.='</tr>';


	}


	$pouch_size_type.='</tbody></table>';
}

}else
{
	$pouch_size_type=$pouch_size_type;
}
$q444 = $this->db->select('version, last_revision_date')->from('quotation_customer_data')->where('id',$record_id)->get();
if($q444->num_rows()>0){
	foreach($q444->result() as $versiondatainfo);
	if($versiondatainfo->version>0){
		$versionnameinfo2 = str_pad($versiondatainfo->version, 2, '0', STR_PAD_LEFT);
		$versionnameinfo = "Version - ".$versionnameinfo2;
	}else{
		$versionnameinfo = '';
	}
	if($versiondatainfo->last_revision_date<>'0000-00-00'){
		$last_revision_date = $versiondatainfo->last_revision_date;
	}else{
		$last_revision_date = $quotation_date;
	}
	
}else{
	$versionnameinfo = '';
}


$options = [
    'powder_option' => $powder_option,
    'liquid_option' => $liquid_option,
    'non_viscous_option' => $non_viscous_option,
    'viscous_option' => $viscous_option,
    'piston_filler_option' => $piston_filler_option,
    'follow_meter_option' => $follow_meter_option,
    'free_flow_option' => $free_flow_option,
    'weigher_system_option' => $weigher_system_option,
    'liner_weigher_option' => $liner_weigher_option,
    'mult_head_weigher_option' => $mult_head_weigher_option,
    'volumetric_cap_option' => $volumetric_cap_option,
    'non_free_flow_option' => $non_free_flow_option
];

$i33=0;
$output = '';
foreach ($options as $key => $value) {
    if (!empty($value)) {
    	if($i33<>0)
    	{

        $output .= ' --> ' . $value;
    	}else
    	{
    		 $output .= $value;
    	}

    	$i33++;
    }

    
}

//echo $versionnameinfo; exit;
$html='
<table width="100%" ruled="all" style=" padding:3px;">
<tr>
<td style="text-align:left;"><strong>Ref. no. '.$ref_no.'</strong></td>
<td style="text-align:right;"><strong>Date: '.date('d M Y',strtotime($last_revision_date)).'</strong>
<br>
<strong>'.$versionnameinfo.'</strong>
</td>
</tr>
</table>
<table style="padding-top:120px">
<tr>
<td>
<h2 style="text-align:center;">TECHNO COMMERCIAL <br>QUOTE FOR<br>
'.strtoupper($machine_name).'<br/>MODEL '.strtoupper($machine_model_no).'</h2>
</td></tr>
</table>
<table style="padding-top:20px">
<tr>
<td><i style="text-align:center; font-size:18px;">“Global Standards Unmatched Performance”</i></td>
</tr>
</table>

<table style="padding-top:120px">
<tr style="padding-left:300px">
<td style="text-align:center;">Specially prepared for<br>
<h2 style="font-size:22px;"><b>'.strtoupper($customerName).'</b></h2>
<h4 style="font-size:18px;"><b>'.strtoupper($customercontactperson).'</b></h4><br>
<h5 style="font-size:18px;">'.strtoupper($countryname).'</h5>
</td>

</tr>
</table>

<table style="padding-top:80px">
<tr>
<td>
<b>Submitted by:</b><br>
'.ucwords(strtolower($agent)).'<br>
Marketing<br>
Email: '.ucwords(strtolower($email)).'<br>
Cell: +91-'.ucwords(strtolower($contact_no)).'<br>
</td>
</tr>
</table>

<br pagebreak="true">

<table>
<tr>
<td><h2 style="text-align:center;">Index</h2></td></tr>
</table>
<table>
<tr>
	<td><h2 style="text-align:center;"></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table><table>
<tr>
	<td><h2 style="text-align:center;"></h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table><table>
<tr>
	<td><h2 style="text-align:center;"></h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>
<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-I</u></td>
<td style="text-align:center; width:50%">Project Data</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-II</u></td>
<td style="text-align:center; width:50%">Technical Specifications</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-III</u></td>
<td style="text-align:center; width:50%">Exclusions</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-IV</u></td>
<td style="text-align:center; width:50%">Price Schedule & Optional Items</td>
</tr>
</table>

<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-V</u></td>
<td style="text-align:center; width:50%">Commercial terms and conditions</td>
</tr>
</table>';

if($layoutimage<>'')
{
$html.='<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-VI</u></td>
<td style="text-align:center; width:50%">Machine Layout Image<br></td>
</tr>
</table>';
}

$optional=$this->getConsumableData($record_id);
if(count($optional)>0)
{
$html.='<table style="padding-top:30px; padding-bottom:30px;">
<tr>
<td style="text-align:center; width:50%"><u>Annexure-VII</u></td>
<td style="text-align:center; width:50%">1 Year Consumable Spares<br></td>
</tr>
</table>';
}
$html.='<br pagebreak="true">

<table>
<h2 style="text-align:center">Annexure-I<br>Project Data</h2>
</table>

<table border="1" style="padding:5px 5px 5px 5px;">
<tr style="padding:120px;">
	<td width="4%">1</td>
	<td width="48%">Product to be Packed</td>
	<td width="48%">'.$product_to_be_packed.' - '.$product_name.'</td>
</tr>

<tr style="padding:120px;">
	<td width="4%">2</td>
	<td width="48%">Filling System</td>
	<td width="48%">'.$output.'</td>
</tr>

<tr style="padding:120px;">
	<td width="4%">3</td>
	<td width="48%">Machine Type</td>
	<td width="48%">'.$machtype.'</td>
</tr>



<tr>
	<td width="4%">4</td>
	<td width="48%">Qty to be Packed & Pouch Size</td>
	<td width="48%">'.$pouch_size_type.'</td>
</tr>';


// <tr>
// 	<td width="4%">4</td>
// 	<td width="48%">Quantity to be packed</td>
// 	<td width="48%">'.$qty_to_be_packed.'</td>
// </tr>
$html.='<tr>
	<td width="4%">5</td>
<td>Sealing Style </td>
<td>'.$this->textformating($sealingstyle).'</td>
</tr>';

if($vertical_sealing_width<>'NA' && $vertical_sealing_width<>'' )
{
	$vsw=$vertical_sealing_width." mm";
}else
{
	$vsw="NA";
}



if($perforation_pitch<>'NA' && $perforation_pitch<>'')
{
	$pp=$perforation_pitch." mm";
}else
{
	$pp="NA";
}

$html.='<tr>
	<td width="4%">6</td>
	<td width="48%">Horizontal Sealing Width</td>
	<td width="48%">'.$horizontal_sealing_width.'mm</td>
</tr>
<tr>
	<td width="4%" >7</td>
	<td width="48%">Vertical Sealing Width</td>
	<td width="48%">'.$vsw.'</td>
</tr>

<tr>
	<td width="4%">8</td>
	<td width="48%" >Perforation Pitch</td>
	<td width="48%">'.$pp.'</td>
</tr>

<tr>
	<td width="4%">9</td>
	<td width="48%" >Perforation Style</td>
	<td width="48%">'.$perforationstyle.'</td>
</tr>

<tr>
	<td width="4%">10</td>
	<td width="48%" >Batch Cut</td>
	<td width="48%">'.$batchcut.'</td>
</tr>

<tr>
	<td width="4%" ><br/>11</td>
	<td width="48%">Type of Sealing</td>
	<td width="48%">'.$typeofsealing.'</td>
</tr>

<tr>
	<td width="4%" >12</td>
	<td width="48%">Power Supply</td>
	<td width="48%">'.$power_supply.'</td>
</tr>';
if($powderdensitydata<>''){
$html.='<tr>
	<td width="4%">13</td>
	<td width="48%">Product viscosity /Powder Bulk Density</td>
	<td width="48%">'.$powderdensitydata.'</td>
</tr>';
}

if($liquidviscositydata<>''){
$html.='<tr>
	<td width="4%">13</td>
	<td width="48%">Liquid Viscosity</td>
	<td width="48%">'.$liquidviscositydata.'</td>
</tr>';
}

if($liquidconductivitydata<>''){
$html.='<tr>
	<td width="4%">14</td>
	<td width="48%">Liquid Conductivity</td>
	<td width="48%">'.$liquidconductivitydata.'</td>
</tr>';
}

if($powderdfrdata<>''){
$html.='<tr>
	<td width="4%">14</td>
	<td width="48%">Powder DFR</td>
	<td width="48%">'.$powderdfrdata.'</td>
</tr>';
}
$powdermoisturecontentdata = htmlspecialchars($powdermoisturecontentdata);
if($powdermoisturecontentdata<>''){

	if (is_numeric($powdermoisturecontentdata)) {
    $powdermoisturecontentdataval =  $powdermoisturecontentdata . '%';
} else {
    $powdermoisturecontentdataval =  $powdermoisturecontentdata; // Display without '%'
}

$html.='<tr>
	<td width="4%">15</td>
	<td width="48%">Moisture Content</td>
	<td width="48%">'.$powdermoisturecontentdataval.'</td>
</tr>';
}


$html.='</table>
<br pagebreak="true">

<table>
<tr style="text-align:center;">
<td><h2 style="text-align:center"><u><br/>Quotation Cum Technical Specification Of The Machine</u></h2></td>
</tr>
</table><br/><br/>

<table border="1" style="padding:5px 5px 5px 5px">
<tr>
<th style="width:10%; text-align:center; font-weight:bold; padding:10%;">Sr No.</th>
<th style="width:90%; text-align:center; font-weight:bold; padding:10%;">Description</th>
</tr>

<tr>
<td style="width:10%; text-align:center; padding:10%;">01</td>
<td style="width:90%; padding-left:10%;"><b>'.strtoupper($machine_name).' MODEL '.strtoupper($model).'</b>';
if($quotation_cum_tech_specdynamic<>''){
	//echo "<pre>"; print_r($quotation_cum_tech_specdynamic); exit;
foreach($quotation_cum_tech_specdynamic as $techdata);
$html.=$techdata->model;
}
$html.='
</td>
</tr>';

if(array_sum($axis_sum)>0)
{
$html.='<tr nobr="true">
<td style="width:10%; text-align:center; padding:10%;">02</td>
<td>'.$axis_data_new.'</td></tr>';
}


$html.='</table>';

if($fillingimage<>'')
{
	if(file_exists(UPLOADPATH.'opportunitydocs/'.$fillingimage))
	{
	
			$imgssss=page_url1.'image_bank/opportunitydocs/'.$fillingimage;
			//echo $img; exit;
			$html.='<br pagebreak="true">';
			$html.='<table>
			<tr>
			<td><h2 style="text-align:center"><u>MACHINE FILLING SYSTEM</h2></u></td>
			</tr>

			<tr>
			<td style="width:20%"></td>
			<td style="width:60%"><img src="'.$imgssss.'"></td>
			<td style="width:20%"></td>
			</tr>';

			$html.='</table>';




	}
}


if($kldimage<>'')
{
	if(file_exists(UPLOADPATH.'opportunitydocs/'.$kldimage))
	{
	
			$imgssss=page_url1.'image_bank/opportunitydocs/'.$kldimage;
			//echo $img; exit;
			$html.='<br pagebreak="true">';
			$html.='<table>
			<tr>
			<td><h2 style="text-align:center"><u>KEY LINE DIAGRAM</h2></u></td>
			</tr>

			<tr>
			<td style="width:20%"></td>
			<td style="width:60%"><img src="'.$imgssss.'"></td>
			<td style="width:20%"></td>
			</tr>';

			$html.='</table>';




	}
}

$bdata=$this->getBrandAutomationData($record_id);
if(count($bdata)>0)
{
$html.='<br pagebreak="true">

<table>
<tr>
	<td><h2 style="text-align:center;">Electrical & Automation Parts Brand/Make</h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>
<table>
<tr>
	<td><h2 style="text-align:center;"></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table><table>
<tr>
	<td><h2 style="text-align:center;"></h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table><table>
<tr>
	<td><h2 style="text-align:center;"></h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>
<table border="1" style="padding:5px 5px 5px 5px;">
<tbody style="padding-left:30px">';
$html.='<tr>
	<th style="text-align:center;font-weight:bold;">Sr. No.</th>
	<th style="text-align:center;font-weight:bold;">Description</th>
	<th style="text-align:center;font-weight:bold;">Brands/Make</th>
</tr>';

for($r=0;$r<count($bdata['head_id']);$r++)
{
	$rt=$r+1;
$html.='<tr>
<td style="text-align:center">'.$rt.'</td>
<td style="text-align:center">'.$bdata['head_data'][$r].'</td>
<td style="text-align:center">'.$bdata['head_value'][$r].'</td>
</tr>';
}
$html.='</tbody>
</table>';
}

$html.='<br pagebreak="true">

<table>
<tr>
	<td><h2 style="text-align:center;">Annexure-II<br>
	Technical Specifications</h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>

<table border="1" style="padding:5px 5px 2px 5px;">
<tbody style="padding-left:30px">
<tr>
<td>Machine Model </td>
<td><b>'.strtoupper($machinemodel).'</b></td>
</tr>

<tr>
<td>No. of Tracks</td>
<td>'.$no_of_track.'</td>
</tr>

<tr>
<td>Filling Accuracy </td>
<td>'.$this->textformating($filling_accuracy).'</td>
</tr>

<tr>
<td>Filling Capacity </td>
<td>'.$this->textformating($filling_capacity).'</td>
</tr>


<tr>
<td>Design Speed- '.$this->closeUnclosedHtmlTags($speed).'</td>
<td>Actual Speed-'.$this->closeUnclosedHtmlTags($actual_speed).'</td>
</tr>



<tr>
<td>Laminate specification (in mm) </td>
<td>Width-'.$leminate_specification.'
Max. Reel Dia-'.$laminatereeldia.'
Reel Core Dia-'.trim($laminatereelcoredia).'</td></tr>';
// $pouchsize = $pouch_size;
// $delimiter = 'X'; // The value to split the string by
// $newDelimiter = ', '; // The value to join the array elements with

// // Split the string into an array using the delimiter X
// $array = explode($delimiter, $pouchsize);

// // Join the array elements into a single string using the new delimiter
// $implodedString = implode($newDelimiter, $array);
// if(count($array[2])==0){
// 	$pouchsizeval  = $pouchsize;
// }else{
// 	$pouchsizeval = $array[0]." X ".$array[1];
// }
$html.='
<tr>
<td>Electrical Spec. </td>
<td>'.$electrical_spec.'</td></tr>

<tr>
<td>Machine Layout Dimensions (in mm)</td>
<td>'.$lay.'</td>
</tr>

<tr>
<td>Machine Weight (in kg)</td>
<td>Net Weight -'.$machine_weight.'<br>
Gross Weight -'.$gross_weight.'</td>
</tr>

<tr>
<td style="padding: 8px;">Compressed Air </td>
<td style="padding: 8px;">Operating Pressure - '.$compressed_air.' CFM<br>
Consumption - '.$compressedairbar.' BAR</td>
</tr>
</tbody>
</table>

<br pagebreak="true">
<table style="padding:10px 10px 10px 40px">
	<tr>
		<td style="text-align:center"><h2><u>Annexure-III<br>
Exclusion<br>
(To be provided by customer)</u></h2></td>
	</tr>
</table>


<table style="padding:10px 10px 10px 10px">
<tr>
<td width="10%">1.0</td>
<td width="90%">Foundation and any civil building work</td>
</tr>
<tr>
<td width="10%">2.0</td>
<td width="90%">Dismantling of existing equipment, if any.</td>
</tr>
<tr style="padding:40px">
<td width="10%">3.0 </td>
<td width="90%">Compressed air piping including compressor</td>
</tr>
<tr style="padding:40px">
<td width="10%">4.0 </td>
<td width="90%">Incomer cable and power supply up to Shubham Pack control panel.</td>
</tr>
<tr style="padding:40px">
<td width="10%">5.0 </td>
<td width="90%">Voltage stabilizer of suitable capacity in case voltage and frequency variation is more
than 10% & 3% respectively at site</td>
</tr>
<tr style="padding:40px">
<td width="10%">6.0</td>
<td width="90%">Bulk material/Laminate during Factory acceptance test (FAT)</td>
</tr>
<tr style="padding:40px">
<td width="10%">7.0 </td>
<td width="90%">Special tools and tackles like cranes, lifts etc., at the time of installation.</td>
</tr>
<tr style="padding:40px">
<td width="10%">8.0</td>
<td width="90%">Skilled and unskilled man power at site along with qualified supervisor
to assist installation.</td>
</tr>
</table>';

$html.='<br pagebreak="true">
<table>
<tr>
<td>
<h2 style="text-align:center;">Annexure-IV<br>
Price Schedule</h2>
</td>
</tr>
<tr>
<td><h3 style="text-align:center;">Line items below will be added as per the requirement</h3></td>
</tr>
</table><br/><br/>
';

if($currency==1){
	$curr = "USD";
}else if($currency==2){
	$curr = "INR";
}else{
	$curr = "EURO";
}

$html.='
<table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px">
<thead>
<tr>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold; text-align:center;"><br/>S.no.<br/></th>
<th style="border: 1px solid black; padding: 14px; width:30%;font-weight:bold;"><br/>Item description<br/></th>
<th style="border: 1px solid black; padding: 14px; width:15%;font-weight:bold;"><br/>HSN Code<br/></th>
<th style="border: 1px solid black; padding: 14px; width:15%;font-weight:bold;"><br/>Unit Price ('.$curr.')<br/></th>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold; text-align:center;"><br/>Qty<br/></th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;"><br/>Total Price ('.$curr.')<br/></th>
</tr>
</thead>
<tbody>';

$price_data=array();
$price_data[]=0;
$dprice=$this->getPriceDataByCategory($record_id,0);
if(count($dprice)>0)
{
	$i=1;
foreach($dprice as $dprice1)
{

	$desc=$dprice1['description'];
	$qty=$dprice1['qty'];
	$price=$dprice1['price'];
	$hsn=$dprice1['hsn'];
	$unit=$dprice1['unit'];
	$additionalinformationwiththemachine=$dprice1['additionalinformationwiththemachine'];
	$total_price=$qty*$price;
if($price<>'')
{
	$price_data[]=$total_price;
	$price=floatval($price);
$formatted_price = $this->formatIndianNumber($price);
$formatted_total_price = $this->formatIndianNumber($total_price);
}else
{
	$formatted_price='';
	$formatted_total_price='';
}
//style="background-color:lightyellow"
$html.='<tr>
<td style="border: 1px solid black; padding: 40px; width:10%; text-align:center;"></td>
		<td colspan="4" style="border: 1px solid black; padding: 40px;width:90%"><br/><u><strong>'.strtoupper($machine_name).' MODEL '.strtoupper($machine_model_no).'</strong></u><br/></td>
	</tr>';

$html.='<tr>
		<td style="border: 1px solid black; padding: 40px; width:10%; text-align:center;"><br/>'.$i.'<br/></td>
		<td style="border: 1px solid black; padding: 40px; width:30%"><br/>Price of design, manufacturing, supply of machine as per technical specifications at Annexure-II <strong>'.$this->formattedText($desc).'</strong>
		'.$additionalinformationwiththemachine.'
		</td>
		<td style="border: 1px solid black; padding: 40px; width:15%; text-align:center;">'.$hsn.'</td>
			<td style="border: 1px solid black; padding: 40px; width:15%;text-align:right;"><br/>'.$formatted_price.'<br/></td>
		<td style="border: 1px solid black; padding: 40px; width:10%;text-align:center;"><br/>'.floatval($qty).' '.$unit.'<br/></td>
	
		<td style="border: 1px solid black; padding: 40px; width:20%;text-align:right;"><br/><strong>'.$formatted_total_price.'</strong><br/></td>
	</tr>';

$i++;
}
}



$dprice=$this->getPriceDataByCategory($record_id,1);
if(count($dprice)>0)
{
	$i=$i;
foreach($dprice as $dprice1)
{

	$desc=$dprice1['description'];
	$qty=$dprice1['qty'];
	$price=$dprice1['price'];
		$hsn=$dprice1['hsn'];
	$unit=$dprice1['unit'];
	$total_price=$qty*$price;
if($price<>'')
{
	$price_data[]=$total_price;
	$price=floatval($price);
$formatted_price = $this->formatIndianNumber($price);
$formatted_total_price = $this->formatIndianNumber($total_price);
}else
{
	$formatted_price='';
	$formatted_total_price='';
}

$instruments=$this->getProductName($desc);

$html.='<tr>
		<td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>'.$i.'<br/></td>
		<td style="border: 1px solid black; padding: 14px; width:30%"><br/>'.$this->formattedText($instruments).'<br/></td>
		<td style="border: 1px solid black; padding: 40px; width:15%; text-align:center;">'.$hsn.'</td>
			<td style="border: 1px solid black; padding: 14px; width:15%;text-align:right;"><br/>'.$formatted_price.'<br/></td>
		<td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>'.floatval($qty).' '.$unit.'<br/></td>
	
		<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><br/><strong>'.$formatted_total_price.'</strong><br/></td>
	</tr>';

$i++;
}
}



$conprice=array();
$conprice[]=0;
$optional=$this->getConsumableData($record_id);
//echo "<pre>"; print_r($optional); exit;
if(count($optional)>0)
{

foreach($optional as $option)
{
$price=$option['price'];
$qty=$option['value'];
$media=$option['media'];
$conprice[]=$price*$qty;
} }
if(count($optional)>0)
{

	$i=$i;
	$price_data[]=array_sum($conprice);
	$formatted_price_total=$this->formatIndianNumber(array_sum($conprice));
	$ref_file='';
	if($media!='')
	{
		$ref_file="<br/><a href='".page_url1."image_bank/opportunitydocs/sparemedia/".$media."' target='_blank'>Click here to view Reference File</a>";
	}
$html.='<tr>
<td style="border: 1px solid black; padding: 14px; width:10%; text-align:center;"><br/>'.$i.'<br/></td>
<td style="border: 1px solid black; padding: 14px; width:45%"><br/>1 Year Consumable Spares'.$ref_file.'</td>
<td style="border: 1px solid black; padding: 14px; width:15%;text-align:right;"><br/>'.$this->formatIndianNumber($price).'<br/></td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:center;"><br/>'.$qty.' Set<br/></td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong><br/>'.$formatted_price_total.'<br/></strong></td>
</tr>';
$i++;
}
//echo "<pre>"; print_r($price_data); exit;
$exworkprice=$this->formatIndianNumber(array_sum($price_data));

$basiccostsum=array_sum($price_data);
//style="background-color:#78cae3"
$html.='<tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><b>Basic Cost</b> </td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$exworkprice.'</strong></td>
	
</tr>';
$dscvalafterdiscount = 0;
$q = $this->db->select('discount_type, discountvalue')->from('quotation_discount_data')->where('record_id',$record_id)->get();
if($q->num_rows()>0){
	foreach($q->result() as $discountdata);
	if($discountdata->discount_type==1){
		$pecentdesc = '';
		$pecentdesc = "(".$discountdata->discountvalue." %)";
		$dscvalafterdiscount  = $basiccostsum*$discountdata->discountvalue/100;

	}else{
		$pecentdesc = '';
		//$disval = $discountdata->discountvalue;
		$dscvalafterdiscount = $discountdata->discountvalue;
	}
$dscfinalcost = $basiccostsum-$dscvalafterdiscount;


$html.='<tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><b>Special Discount Given '.$pecentdesc.'</b> </td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>-'.$this->formatIndianNumber($dscvalafterdiscount).'</strong></td>

</tr>';
$html.='<tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><b>Cost of Machine after Special Discount Given</b> </td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$this->formatIndianNumber($dscfinalcost).'</strong></td>

</tr>';
$basiccostsum=$dscfinalcost;

}


$ftype='';
$othercharge=$this->quotation_freight_packing_forwarding($record_id);
//echo "<pre>"; print_r($othercharge); exit;

if(count($othercharge)>0)
{
	
$i=$i;

if($othercharge['packing_charges']>0)
{
	$packing_charges=round($basiccostsum*($othercharge['packing_charges']/100),2);
	$per=$othercharge['packing_charges'];
	$price_data[]=$packing_charges;
$html.='<tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Packing Charges @'.floatval($per).'%</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$this->formatIndianNumber($packing_charges).'</strong>
</td>
</tr>';
$i++;
}
//echo "<pre>"; print_r($othercharge); exit;

if($othercharge['forwarding_charges']>0)
{
	$i=$i;
	$packing_charges=round($basiccostsum*($othercharge['forwarding_charges']/100),2);

	$per=$othercharge['forwarding_charges'];
	$price_data[]=$packing_charges;
$html.='<tr nobr="true">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Forwarding Charges @'.floatval($per).'%</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$this->formatIndianNumber($packing_charges).'</strong></td>
</tr>';
$i++;
}


if($othercharge['insurance']>0)
{
	$i=$i;
	$packing_charges=round($basiccostsum*($othercharge['insurance']/100),2);
	$per=$othercharge['insurance'];
	$price_data[]=$packing_charges;
$html.='<tr nobr="true">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Insurance Charges @'.floatval($per).'%</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$this->formatIndianNumber($packing_charges).'</strong>
</td>
</tr>';
$i++;
}

if($othercharge['installation']>0)
{
	$i=$i;
	$installation_charges=$othercharge['installation'];
	$price_data[]=$installation_charges;
$html.='<tr nobr="true">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Installation & Commissioning Charges</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><strong>'.$this->formatIndianNumber($installation_charges).'</strong>
</td>
</tr>';
$i++;
}


$ftypedata=$othercharge['freight_type'];

if($othercharge['freight']==1 || $othercharge['freight']==2)
{
	$i=$i;

	if($othercharge['freight']==1)
	{
		$ftype="Extra at Actuals";
	}else
	{
		$ftype="In Customer Scope";
	}

if($ftypedata=="FOB" || $ftypedata=='CFR' || $ftypedata=='CIF')
{
$port="Port-".$othercharge['port_name'];
}else
{
	$port='';
}

//if($ftypedata==)
$html.='<tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Freight</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;">'.$ftype.'
</td>
</tr>';
$i++;
}else
{
	$i=$i;

	if($ftypedata=="FOB" || $ftypedata=='CFR' || $ftypedata=='CIF')
{
$port="Port-".$othercharge['port_name'];
}else
{
	$port='';
}

	if($othercharge['freight']==3)
	{
		$ftype="Additional";
	}else
	{
		$ftype="Inland Transporation";
	}

$price_data[]=floatval($othercharge['freight_charges']);
$html.='<tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$i.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">Freight-'.$ftype.'</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;text-align:right;"><strong>'.$this->formatIndianNumber(floatval($othercharge['freight_charges'])).'</strong>
</td>
</tr>';
$i++;
}




}
$calculateGST = 0;

// if($curr=="INR"){
//echo $export_domestic; exit;
if($export_domestic==1){
	$mval = array_sum($price_data)-$dscvalafterdiscount;
	if($merchantexport==1){
		$gstvalueinper = 0.1;
	}else{
		$gstvalueinper = 18;
	}
	
	$mm = $i;
if($gst_actual==0)
{
	$calculateGST = $mval*$gstvalueinper/100;
$html.='<tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$mm.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">GST ('.$gstvalueinper.'%)</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;text-align:right;"><strong>'.$this->formatIndianNumber(floatval($calculateGST)).'</strong>
</td>
</tr>';
}else
{
	$html.='<tr style="">
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;">'.$mm.'</td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%">GST</td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;text-align:right;"><strong>As Actual</strong>
</td>
</tr>';
}

}

if($curr=="INR")
{
$words=ucwords(strtolower($this->getCurrencyCode(round(array_sum($price_data)-$dscvalafterdiscount+$calculateGST))));
}else
{

	$words=ucwords(strtolower(str_replace('-','',$this->getUSCodeInWords(round(array_sum($price_data)-$dscvalafterdiscount+$calculateGST)))));
}
//style="background-color:#78cae3"
$html.='<tr>
<td style="border: 1px solid black; padding: 8px; width:10%; text-align:center;"></td>
<td colspan="3" style="border: 1px solid black; padding: 8px; width:70%"><br/><strong>'.$ftypedata.', Cost of machine<br/>'.$curr." ".$words.' Only.</strong><br/></td>
<td style="border: 1px solid black; padding: 8px; width:20%;text-align:right;"><br/><strong>'.$this->formatIndianNumber(round(array_sum($price_data)-$dscvalafterdiscount+$calculateGST)).' </strong><br/></td>
</tr>';  
  
$html.='</tbody> 
</table>';

$optional=$this->getOptionalData($record_id);
if(count($optional)>0)
{
$html.='<br pagebreak="true"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px">
<thead>
<tr style="background-color:#78cae3">
<th colspan="5" style="text-align:center;"><strong>Optional Accessories</strong></th>
</tr>
<tr>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold;">S.no.</th>
<th style="border: 1px solid black; padding: 14px; width:40%;font-weight:bold;">Item description</th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;">Unit Price ('.$curr.')</th>
<th style="border: 1px solid black; padding: 14px; width:10%;font-weight:bold;">Qty</th>
<th style="border: 1px solid black; padding: 14px; width:20%;font-weight:bold;">Total Price ('.$curr.')</th>
</tr>
</thead>
<tbody>

';

$i=1;
foreach($optional as $option)
{
$price=$option['price'];
if($price<>'')
{
$price_data[]=$option['value']*$price;
$price=floatval($price);
$formatted_price = $this->formatIndianNumber($price);
$formatted_price_total = $this->formatIndianNumber($option['value']*$price);
}else
{
$formatted_price='';
$formatted_price_total='';
}

$optional_name=$this->getProductName($option['description']);
$html.='<tr>
<td style="border: 1px solid black; padding: 14px; width:10%">'.$i.'</td>
<td style="border: 1px solid black; padding: 14px; width:40%">'.ucwords(strtolower($optional_name)).'
</td>
<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;">'.$formatted_price.'</td>
<td style="border: 1px solid black; padding: 14px; width:10%;text-align:right;">'.$option['value'].'</td>

<td style="border: 1px solid black; padding: 14px; width:20%;text-align:right;"><strong>'.$formatted_price_total.'</strong></td>
</tr>

';


$i++;
} 

$html.='</table>';
}


$odata=$this->getotherinformation($record_id);
	if(count($odata)>0)
	{
	$terms_value=$odata[0];
	
	$terms_value=$this->getpaymentterms($terms_value);
	// echo $terms_value; exit;
	$delivery_value=$odata[1];
	$pocket_expense=$odata[2];
	$support_statement_value=$odata[3];
	}else
	{
	$terms_value='';
	$delivery_value='';
	$pocket_expense='';
	$support_statement_value='';
	}
//echo $port."<br/>".$ftypedata; exit;
$html.='<br pagebreak="true"><table style="padding:0px 5px 0px 0px">
<tr>
<td><h2 style="text-align:center"><u>Annexure-V<br>
Commercial Terms and Conditions</u></h2></td>
</tr>
<tr>
<td>
<h4><u>PRICE BASIS</u></h4>
</td></tr>
<tr>
<td>All prices are on '.$ftypedata.' '.$port.', Basis unless otherwise specified.</td>
</tr>
<tr>
<td></td>
</tr>
<tr><td><h4><u>TERMS OF PAYMENT</u></h4></td></tr>
<tr>
<td>'.$terms_value.'
</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td>Supplier’s Bankers details:</td>
</tr>
<tr>
<td>Axis Bank Ltd.<br>
SCO-40, Sec-7 Market<br>
Ballabgarh, Faridabad 121004<br>
Haryana, India<br>
A/c holder name- Shubham Flexible Packaging Machines Pvt. Ltd.<br>
A/c no.- 920030068344715<br>
Swift code- AXISINBB039<br></td>
</tr>';

if($agent_id!=118)
{
$html.='<tr>
<td>The Letter of Credit must permit <b>partial shipment</b> and transshipment and should be valid for
negotiation for a period of 21 days beyond the last permissible date of shipment.</td></tr>
<tr>
<td><h4><strong>The Letter of Credit must accept Combined Transport Bill of Lading issued by the Shipping
Company in New Delhi as a negotiable document.</strong></h4><br></td>
</tr>';
}

$customTerms=$this->getCustomTerms($record_id);
if(count($customTerms)>0)
{
if($country==101 && $customTerms[5]==1 && $customTerms[6]<>'')
{
$html.='<tr>
<td><h4><strong><u>LATE DELIVERY CLAUSE: -</u></strong></h4></td>
</tr>
<tr>
<td>'.$customTerms[6].'</td>
</tr><tr>
<td></td>
</tr>';
}


if($country==101 && $customTerms[0]==1 && $customTerms[1]<>'')
{
$html.='<tr>
<td><h4><strong><u>LIQUIDATED DAMAGES CLAUSE: -</u></strong></h4></td>
</tr>
<tr>
<td>'.$customTerms[1].'</td>
</tr><tr>
<td></td>
</tr>';
}


}


$pck="In strong seaworthy wooden boxes.";
if(count($customTerms)>0)
{
if($country==101 && $customTerms[2]<>'')
{
$pck=$customTerms[2];
}
}
$html.='<tr>
<td><h4><strong><u>PACKING: -</u></strong></h4></td>
</tr>

<tr>
<td>'.$pck.'</td>
</tr>
<tr>
<td></td>
</tr>';


$pck="Buyer’s responsibility to take suitable insurance for goods from seller’s warehouse in Ballabhgarh to
the port of discharge covering all risks including erection, installation and commissioning for 110 %
of CIF value. Documentary evidence of this insurance to be given to us at least 30 days before
shipment. Insurance will be applicable in case of terms other than Ex-works.";
if(count($customTerms)>0)
{
if($country==101 && $customTerms[3]<>'')
{
$pck=$customTerms[3];
}
}


$html.='<tr>
<td>
<h4><strong><u>INSURANCE: -</u></strong></h4>
</td>
</tr>
<tr>
<td>
'.$pck.'
</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><h4><strong><u>FREIGHT:-</u></strong></h4></td>
</tr>
<tr>
<td>'.$ftype.'</td></tr>
<tr>
<td></td>
</tr>
<tr>
<td><h4><strong><u>DELIVERY: -</u></strong></h4></td>
</tr>
<tr>
<td>'.$delivery_value.'</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><h4><strong><u>INSTALLATION / START- UP AND TRAINING</u></strong></h4></td>
</tr>';


$default='<tr>
<td>
<p>To be done by factory trained engineers of Shubham pack or its authorized sub suppliers. The
installation cost is indicated separately in the price schedule at Annexure-IV.</p>
<p>Standard tool kit will be provided along with machine <a href="'.page_url.'User/freesparelist" target="_blank">Click here to view standard toolkit</a></p>
</td>
</tr>
<tr>
<td><strong>Buyer has to provide Hotel, Food, local conveyance and medical expenses for all service
engineers at customer site.</strong></td>
</tr>

<tr>
<td>Service /installation charges are extra as per number of days required. Buyer has to bear expenses
for extra to and fro air fares, if any, stay in hotel, food, local transport and medical expenses for
deputing engineers and also reimburse out of pocket expenses <strong>@ '.$curr.' '.$pocket_expense.'</strong> per day per including the travel time and intervening holidays.</td>
</tr>

<tr>
<td>Local Labor, Power and other connected items including lifting, tackle, foundation and masonry work
during installation shall have to be provided by the Buyer.<br>
Buyer is advised to unload the machine from the truck/container and place it at designated place. It
is also advisable that inlet of bulk feed, air connection, power supply etc. should be made ready before
arrival of Installation engineer. However, all such connections as well as electrical power ON should
be done in presence of installation engineer only.</td>
</tr>';

if(count($customTerms)>0)
{
if($country==101 && $customTerms[4]<>'')
{
$default='<tr>
					<td>'.$customTerms[4].'</td>
					</tr>';
}
}


$html.=$default;


$html.='<tr>
<td></td>
</tr>


<tr>
<td><h4><strong><u>WARRANTY</u></strong></h4></td>
</tr>
<tr>
<td>
<p>We warranty for a period of 12 months from the date of erection of products at Buyer’s site,
all products and parts thereof, when properly installed, adjusted, operated and maintained
as per our proposal and / or the applicable technical manuals. This will however not
include components made of rubber, plastic and electrical equipment and other parts /
components subject to normal wear and tear.</p>
<p>Warranty does not cover consumables. These parts are considered as consumables and are required
to be paid for upon replacement.</p>
<p>Customer must buy critical and consumable spares for 24 months in order to avail comprehensive
warranty of 24 months.</p>
<p>Warranty does not cover damage of part due to poor preventive maintenance OR parts that are subject
to damage due to voltage fluctuation or improper voltage at buyer site.<br></p>
<p>Warranty does not apply to any equipment which has been improperly installed, adjusted, operated,
maintained, repaired or altered by unauthorized persons. We reserve the right to inspect any claimed
defect prior to replacement.</p>
<p>We will replace/repair, any defective material or workmanship, provided that we are given
written notice of the claimed defects above. Unless caused by us, equipment damaged by
overloading, exposure to corrosive or abrasive substance of abnormal dampness or other
misuse, neglect or accident, shall not be subject to the warranty set forth above.</p>
<p>The Warranty as referred to above clause will cease to operate if:<br>
a. The buyer, within the Warranty period, sells or otherwise parts with possession of the products.
 <p style="text-align:center">AND / OR</p>
b. Any local mechanic or electrician tampers with the Products without Shubham’ written
permission</p>

</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td>
<h4><strong><u>LIABILITY</u></strong></h4>
</td>
</tr>
<tr>
<td>
<p>Liability of Shubham Flexible Packaging Machines Pvt. Ltd is limited to Warranty performance of
equipment and does not cover any aspect related to conversion of material. Shubham Flexible
Packaging Machines Pvt. Ltd undertakes no responsibility on performance of laminate/film converted
on the equipment. Customer must conduct trials at his own risk and cost, to evaluate and achieve
satisfactory results before commencing large-scale production. Machine speed and performance are
indicative and may vary with different substrates and raw material and may not be accurate. Liability of Shubham Flexible Packaging Machines Pvt. Ltd is only limited to replacement of defective parts and
does not cover any incidental or consequential loss to customer. </p>
</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><h4><strong><u>CANCELLATION</u></strong></h4>
</td>
</tr>
<tr>
<td>
<p>In the event of a request to stop work or to cancel any part of the order should be mutually discussed
between Supplier and Buyer.</p>
</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td><h4><strong><u>FORCE MAJEURE:</u></strong></h4></td></tr>
<tr>
<td>
<p>We will not be responsible for any delay in delivery or for non-delivery of our products by reasons of
Force Majeure, such as acts of God, war, riots, civil disturbances, acts of authorities, strikes, lockouts
or other labour difficulties or any other circumstances beyond our control which might affect us or
our suppliers and hinder, impede or prevent deliveries. We will send the notice of force majeure to
customer in writing.</p>
</td>
</tr>
<tr>
<td></td>
</tr>

<tr>
<td><h4><strong><u>APPROVAL</u></strong></h4></td>
</tr>
<tr>
<td><p>Shubham Flexible Packaging Machines Pvt. Ltd will fully assemble the equipment prior to shipping
and a representative of the customer must approve the Equipment in writing prior to shipment. Any
modification or change suggested at this point will invalidate the delivery date clause and reasonable
time will be allowed to incorporate the modification.</p></td>
</tr>
<tr>
<td></td>
</tr>

<tr>
<td><h4><strong><u>ARBITRATION</u></strong></h4></td>
</tr>
<tr>
<td><p>Any dispute or differences whatsoever arising between the parties out of or relating to the construction,
meaning and operation or effect of this contract or the breach thereof shall be settled by arbitration in
accordance with the Rules of Arbitration of the Indian Council of Arbitration and the Award made in
pursuance thereof shall be binding on the parties.</p></td>
</tr>
<tr>
<td></td>
</tr>


<tr>
<td><h4><strong><u>VALIDITY</u></strong></h4></td>
</tr>
<tr>
<td><p>This Contract and prices are valid for '.$validity.' days and supersedes all previous contracts. The terms and
conditions mentioned above shall supersede any terms and conditions agreed to earlier Performa
invoice if any.</p></td>
</tr>
<tr>
<td></td>
</tr>


<tr>
<td><h4><strong><u>GENERAL</u></strong></h4></td>
</tr>
<tr>
<td><p>Continuous improvement is standard policy at Shubham. Accordingly, all specification and features
are subject to change without any prior notice.</p></td>
</tr>
<tr>
<td></td>
</tr>


</table>';

if($special_notes!='')
{
$html .= '<br/>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <td style="background-color:#f2f2f2; font-weight:bold;">
            Special Note
        </td>
    </tr>
    <tr>
        <td>'.$special_notes.'</td>
    </tr>
</table>';
}


if($layoutimage<>'')
{

	if(file_exists(UPLOADPATH.'opportunitydocs/layoutimg/'.$layoutimage))
	{
	
			$imgssss=page_url1.'image_bank/opportunitydocs/layoutimg/'.$layoutimage;
			//echo $img; exit;
			$html.='<br pagebreak="true">';
			$html.='<table>
			<tr>
			<td><h2 style="text-align:center"><u>Annexure-VI</h2></u></td>
			</tr>

			<tr>
			<td style="text-align:center"><img src="'.$imgssss.'" style="width:500px;"></td>
			</tr>';

			$html.='</table>';




	}
}

$optional=$this->getConsumableData($record_id);
if(count($optional)>0)
{
$html.='<br pagebreak="true"><br/><br/>';

$html.='<table style="width:100%; border-collapse: collapse; padding:5px 5px 5px 5px;" border="1">
<tr>
<th>
<h2 style="text-align:center;padding:">1 Year Consumable Spares</h2>
</th>
</tr>';

$optional=$this->getConsumableData($record_id);
if(count($optional)>0)
{
// 	$t=1;
// foreach($optional as $option)
// {
// $name=$this->formattedText($this->getconsumableSpare($option['description']));
// $qty=$option['value'];

$html.='<tr>
<td style="border: 1px solid black; padding: 8px;text-align:center;"><a href="">Click here to view the Consumable Spare</a></td>
</tr>';
// $t++;
// }
 }
$html.='</table>';



}
	
// $html.='</tbody>
// </table>';
//echo $html; exit;
return $html;
	}

function get_axis_details($record_id)
{
	$data=array();
	$rest=$this->db->select('id,description,axiscou')->from('quotation_no_of_axis_in_machine')->where('record_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
		$data[]=$row->description;
	
		}
	}

	return $data;
}


function get_axis_detailsNew($record_id)
{
	$data=array();
	$rest=$this->db->select('id,description,axiscount')->from('quotation_no_of_axis_in_machine')->where('record_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
		$data['description'][]=$row->description;
		$data['axiscount'][]=$row->axiscount;
		$data['id'][]=$row->id;
	
		}
	}

	return $data;
}


function get_axis_detailswithid($record_id)
{
	$data=array();
	$rest=$this->db->select('id,description')->from('quotation_no_of_axis_in_machine')->where('record_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
		$data[]=array('description'=>$row->description,'id'=>$row->id);	
		}
	}

	return $data;
}



function getPriceDataByCategory($record_id,$flag){

		$data=array();
		$d=$this->db->select('id,description,qty,price,hsn,unit,additionalinformationwiththemachine')->from('quotation_annexture_4')->where('record_id',$record_id)->where('category_type',$flag)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row)
			{
				$data[]=array('description'=>$row->description,'qty'=>$row->qty,'price'=>$row->price,'id'=>$row->id,'additionalinformationwiththemachine'=>$row->additionalinformationwiththemachine,'hsn'=>$row->hsn,'unit'=>$row->unit);
			}
		}

		return $data;

	}

	function formatIndianNumber($number) {
    // Splitting the number into integer and decimal parts
    $parts = explode('.', $number);
    
    // Formatting the integer part
    $formatted_integer = number_format($parts[0]);
    
    // Combining the formatted integer part and the decimal part (if present)
    if (isset($parts[1])) {
        $formatted_number = $formatted_integer . '.' . $parts[1];
    } else {
        $formatted_number = $formatted_integer;
    }
    
    return $formatted_number;
}


function formattedText($text)
{
// 	//$text = "hello, this is a sentence. and another one. and one more.";

// // Split the text into an array of sentences
// $sentences = preg_split('/(?<=[.?!])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

// // Capitalize the first letter of each sentence
// foreach ($sentences as &$sentence) {
//     $sentence = ucfirst(strtolower($sentence));
// }

// // Join the sentences back into a single string
// $formatted_text = implode(' ', $sentences);

//return  ucfirst(strtolower($text));
return  $text;
}

function getRecordID($lead_id)
{
	$id=0;
	$rest=$this->db->select('id')->from('quotation_customer_data')->where('lead_id',$lead_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$id=$row->id;
	}
return $id;
}


	function checkforquotationoraheadstepNew($leadstage)
	{
		$sort=0;
		$restey=$this->db->select('lead_id as id')->from('lead_stage')->where('quotation_step',1)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$sort=$row->id;
		}


			$r1=$this->db->select('id')->from('progress_remarks')->where('lead_id',$leadstage)->where('lead_status',$sort)->get();
			if($r1->num_rows()>0)
			{
				return true;
			}else
			{
				return false;
			}


	}

		function checkforquotationoraheadsteptogetquotationvisible($leadstage)
	{
		$sort = array();
		$sort[]=0;
		$restey=$this->db->select('lead_id as id')->from('lead_stage')->where('quotation_step',1)->or_where('quote_visible',1)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row){
				$sort[]=$row->id;
			}
			
		}
		if(count($sort)>0){
			$result = "'" . implode ( "', '", $sort ) . "'";
		}else{
			$result  = "'1000'";
		}


			$r1=$this->db->select('id')->from('progress_remarks')->where('lead_id',$leadstage)->where_in('lead_status',$result,false)->get();
			if($r1->num_rows()>0)
			{
				return true;
			}else
			{
				return false;
			}


	}


	function get_quotation_layout_img($record_id)
	{
		$layout='';
		$rest=$this->db->select('image')->from('quotation_layout_img')->where('record_id',$record_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			$layout=$row->image;
		}

		return $layout;

	}

	function get_quotation_machine_filling_image($record_id)
	{
		$layout='';
		$rest=$this->db->select('image')->from('quotation_machine_filling_image')->where('record_id',$record_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			$layout=$row->image;
		}

		return $layout;
	}

	function get_quotation_machine_kld_image($record_id)
	{
		$layout='';
		$rest=$this->db->select('image')->from('quotation_kld_image')->where('record_id',$record_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			$layout=$row->image;
		}

		return $layout;
	}


	function getConsumableData($record_id)
	{
			$data=array();
		$d=$this->db->select('id,spare_id,qty,price,media')->from('quotation_consumable_spares')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row)
			{
			$data[]=array('description'=>$row->spare_id,'value'=>$row->qty,'price'=>$row->price,'id'=>$row->id,'media'=>$row->media);
		}
		}

		return $data;
	}

	function quotation_freight_packing_forwarding($record_id){
		$data=array();
		$d=$this->db->select('freight,freight_type,freight_charges,packing_charges,forwarding_charges,insurance, port,installation_charges')->from('quotation_freight_packing_forwarding')->where('record_id',$record_id)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $row);

			$port_name=$this->getPortName($row->port);
			$data=array('freight'=>$row->freight,'freight_type'=>$row->freight_type,'freight_charges'=>$row->freight_charges,'packing_charges'=>$row->packing_charges,'forwarding_charges'=>$row->forwarding_charges,'insurance'=>$row->insurance, 'port'=>$row->port,'port_name'=>ucwords(strtolower($port_name)),'installation'=>$row->installation_charges);
		}

		return $data;
	}

	function getpaymentterms($id)
	{
		$pay='';
		$resty=$this->db->select('payment_terms')->from('payment_terms')->where('id',$id)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row);
			$pay=$row->payment_terms;
		}

		return $pay;
	}


function getCurrencyCode($grandtot)
{
	/* CURRENCY CODE **/
	$number = floatval($grandtot);
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
		0 => '', 1 => 'one', 2 => 'two',
		3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
		7 => 'seven', 8 => 'eight', 9 => 'nine',
		10 => 'ten', 11 => 'eleven', 12 => 'twelve',
		13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
		16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
		19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
		40 => 'forty', 50 => 'fifty', 60 => 'sixty',
		70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
	);
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

	while ($i < $digits_length) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
		} else $str[] = null;
	}

	$rupees = implode('', array_reverse($str));
	$paise = '';

	if ($decimal) {
		$paise = 'and ';
		$decimal_length = strlen($decimal);

		if ($decimal_length == 2) {
			if ($decimal >= 20) {
				$dc = $decimal % 10;
				$td = $decimal - $dc;
				$ps = ($dc == 0) ? '' : '-' . $words[$dc];

				$paise .= $words[$td] . $ps;
			} else {
				$paise .= $words[$decimal];
			}
		} else {
			$paise .= $words[$decimal % 10];
		}

		$paise .= ' paise';
	}

	return $rupees;
	$words = $rupees . 'rupees ' . $paise;

	/* END **/
}

function getconsumableSpare($product_id)
{

$res = '';
$sql = $this->db->select('spare_name')
->from('consumablespares')
->where('id', $product_id)
->get();

if($sql->num_rows() > 0) {
foreach($sql->result() as $row);
$res = $row->spare_name;
}

return $res;

}

function closeUnclosedHtmlTags($html) {
    // Suppress warnings and load HTML
    libxml_use_internal_errors(true);
    
    // Use DOMDocument to handle HTML
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    // Save and return the fixed HTML
    $fixedHtml = $dom->saveHTML();
    
    // Remove the XML declaration added for encoding
    $fixedHtml = preg_replace('/^<\?xml.*?>/', '', $fixedHtml);

    return $fixedHtml;
}

function textformating($string){
return ucwords(strtolower($string));
}

function getopportunitygeneraterefno($record_id)
{
	$ref_no='';
	$rest=$this->db->select('ref_no')->from('quotation_customer_data')->where('id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$ref_no=$row->ref_no;
	}

	return $ref_no;

}


function getopportunityversionfno($record_id)
{
	$ref_no='';
	$rest=$this->db->select('version')->from('quotation_customer_data')->where('id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$ref_no=$row->version;
	}

	return $ref_no;
}

function getUSCodeInWords($amount)
{
	   $parts = explode('.', $amount);
    $dollars = $parts[0];
    $cents = isset($parts[1]) ? $parts[1] : '00';

    // Array of words for numbers 0 to 19
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen'
    );

    // Array of words for tens multiples (20, 30, ..., 90)
    $tens = array(
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );

    // Array of scale numbers (thousand, million, billion, etc.)
    $scale = array(
        '', 'Thousand', 'Million', 'Billion', 'Trillion', 'Quadrillion', 'Quintillion'
    );

    // If the number is 0, return "Zero Dollars"
    if ($dollars == '0' && $cents == '00') {
        return $words[0] . ' ';
    }

    // Initialize result string for dollars
    $dollars_in_words = '';

    // Process dollars part
    $groups = array_chunk(str_split(strrev($dollars)), 3);
    foreach ($groups as $key => $group) {
        $group_number = (int) strrev(implode('', $group));
        if ($group_number != 0) {
            $hundreds = floor($group_number / 100);
            $tens_ones = $group_number % 100;

            $current_group_words = '';
            if ($hundreds > 0) {
                $current_group_words .= $words[$hundreds] . ' Hundred ';
            }
            if ($tens_ones < 20) {
                $current_group_words .= $words[$tens_ones];
            } else {
                $tens_digit = floor($tens_ones / 10);
                $ones_digit = $tens_ones % 10;
                $current_group_words .= $tens[$tens_digit];
                if ($ones_digit > 0) {
                    $current_group_words .= '-' . $words[$ones_digit];
                }
            }
            $dollars_in_words = trim($current_group_words) . ' ' . $scale[$key] . ' ' . $dollars_in_words;
        }
    }

    // Add "Dollars" to the end
    $dollars_in_words .= ' ';

    // Initialize result string for cents
    $cents_in_words = '';
    if ($cents != '00') {
        $cents_number = (int) $cents;
        if ($cents_number < 20) {
            $cents_in_words .= $words[$cents_number];
        } else {
            $tens_digit = floor($cents_number / 10);
            $ones_digit = $cents_number % 10;
            $cents_in_words .= $tens[$tens_digit];
            if ($ones_digit > 0) {
                $cents_in_words .= '-' . $words[$ones_digit];
            }
        }
        $cents_in_words .= ' ';
    }

    // Combine dollars and cents into final string
    $final_words = trim($dollars_in_words);
    if (!empty($cents_in_words)) {
        $final_words .= ' And ' . $cents_in_words;
    }

    return $final_words;
}



function getUSCodeInWordsOldd($amount)
{
	   $parts = explode('.', $amount);
    $dollars = $parts[0];
    $cents = isset($parts[1]) ? $parts[1] : '00';

    // Array of words for numbers 0 to 19
    $words = array(
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen'
    );

    // Array of words for tens multiples (20, 30, ..., 90)
    $tens = array(
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );

    // Array of scale numbers (thousand, million, billion, etc.)
    $scale = array(
        '', 'Thousand', 'Million', 'Billion', 'Trillion', 'Quadrillion', 'Quintillion'
    );

    // If the number is 0, return "Zero Dollars"
    if ($dollars == '0' && $cents == '00') {
        return $words[0] . ' Dollars';
    }

    // Initialize result string for dollars
    $dollars_in_words = '';

    // Process dollars part
    $groups = array_chunk(str_split(strrev($dollars)), 3);
    foreach ($groups as $key => $group) {
        $group_number = (int) strrev(implode('', $group));
        if ($group_number != 0) {
            $hundreds = floor($group_number / 100);
            $tens_ones = $group_number % 100;

            $current_group_words = '';
            if ($hundreds > 0) {
                $current_group_words .= $words[$hundreds] . ' Hundred ';
            }
            if ($tens_ones < 20) {
                $current_group_words .= $words[$tens_ones];
            } else {
                $tens_digit = floor($tens_ones / 10);
                $ones_digit = $tens_ones % 10;
                $current_group_words .= $tens[$tens_digit];
                if ($ones_digit > 0) {
                    $current_group_words .= '-' . $words[$ones_digit];
                }
            }
            $dollars_in_words = trim($current_group_words) . ' ' . $scale[$key] . ' ' . $dollars_in_words;
        }
    }

    // Add "Dollars" to the end
    $dollars_in_words .= ' Dollars';

    // Initialize result string for cents
    $cents_in_words = '';
    if ($cents != '00') {
        $cents_number = (int) $cents;
        if ($cents_number < 20) {
            $cents_in_words .= $words[$cents_number];
        } else {
            $tens_digit = floor($cents_number / 10);
            $ones_digit = $cents_number % 10;
            $cents_in_words .= $tens[$tens_digit];
            if ($ones_digit > 0) {
                $cents_in_words .= '-' . $words[$ones_digit];
            }
        }
        $cents_in_words .= ' Cents';
    }

    // Combine dollars and cents into final string
    $final_words = trim($dollars_in_words);
    if (!empty($cents_in_words)) {
        $final_words .= ' And ' . $cents_in_words;
    }

    return $final_words;
}

function getQtyPackedData($record_id)
{
	$data=array();
	$rest=$this->db->select('*')->from('quotation_pouch_size')->where('record_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
			$data['qty'][]=$row->packing_qty;
			$data['unit'][]=$row->unit;
			$data['length'][]=$row->length;
			$data['width'][]=$row->width;
			$data['height'][]=$row->height;
			$data['gusset'][]=$row->gusset;
			$data['punchhole'][]=$row->punchhole;
			$data['id'][]=$row->id;
		}
	}

	return $data;

}

function getBrandAutomationData($record_id)
{
	$data=array();
	$rest=$this->db->select('a.id,a.head_id,a.value_id,b.name as head,c.name as head_value')->from('quotation_brand_data a')->join('quote_parts_heading_master b','a.head_id=b.id')->join('quote_parts_heading_master_options c','a.value_id=c.id')->where('record_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row)
		{
			$data['id'][]=$row->id;
			$data['head_id'][]=$row->head_id;
			$data['head_data'][]=$row->head;
			$data['value_id'][]=$row->value_id;
			$data['head_value'][]=$row->head_value;
			
		}
	}

	return $data;
}

function getleadidfromsalescrm($record_id)
{
	$lead_id=0;
	$resty=$this->db->select('lead_id')->from('quotation_customer_data')->where('id',$record_id)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $row);
		$lead_id=$row->lead_id;
	}

	return $lead_id;
}

function getPortName($ports)
{
	$pname='';
	$resty=$this->db->select('name')->from('ports')->where('id',$ports)->get();
	if($resty->num_rows()>0)
	{
	foreach($resty->result() as $row);
	$pname=$row->name;
	}

	return $pname;

}

function getcurrent_statusLead($ss)
	{
		$s=0;
		$resty=$this->db->select('lead_status')->from('progress_remarks')->where('lead_id',$ss)->order_by('id','DESC')->limit(1)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $rowss);
			$s=$rowss->lead_status;
		}

		return $s;

	}

	function CheckexportDomestic($lead_id)
	{
		$patient=2;
		$rest=$this->db->select('patient_type_id')->from('leads')->where('id',$lead_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rreow);
			$patient=$rreow->patient_type_id;
		}

		return $patient;
	}

		function checkmerchantexport($lead_id)
	{
		$merchantexport=0;
		$rest=$this->db->select('merchantexport')->from('leads')->where('id',$lead_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rreow);
			$merchantexport=$rreow->merchantexport;
		}

		return $merchantexport;
	}


	function getporeceivedData($po_id,$lead_id)
	{
		$data=array();
		$rest=$this->db->select('pono,podate,customer_currency,prof_inv_no,payment_term')->from('poreceived')->where('id',$po_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$data[]=$row->pono;
			$data[]=$row->podate;
			$data[]=$row->customer_currency;
			$data[]=$row->prof_inv_no;
			$data[]=$row->payment_term;
		}

		return $data;

	}



function convertNumberToWordsUSD($number) {
    $number = number_format((float)$number, 2, '.', '');
    list($dollars, $cents) = explode('.', $number);

    $words = ucwords($this->convertToWords($dollars)) . ' dollars';
    if ((int)$cents > 0) {
        $words .= ' and ' . ucwords($this->convertToWords($cents)) . ' cents';
    }
    return $words . ' only';
}

function convertToWords($num) {
    $ones = array(
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen',
        'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );

    $tens = array(
        '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
    );

    $num = (int)$num;

    if ($num < 20) return $ones[$num];
    if ($num < 100) {
        return $tens[intval($num / 10)] . (($num % 10 > 0) ? ' ' . $ones[$num % 10] : '');
    }
    if ($num < 1000) {
        return $ones[intval($num / 100)] . ' hundred' . (($num % 100 > 0) ? ' ' . $this->convertToWords($num % 100) : '');
    }
    if ($num < 1000000) {
        return $this->convertToWords(intval($num / 1000)) . ' thousand' . (($num % 1000 > 0) ? ' ' . $this->convertToWords($num % 1000) : '');
    }
    if ($num < 1000000000) {
        return $this->convertToWords(intval($num / 1000000)) . ' million' . (($num % 1000000 > 0) ? ' ' . $this->convertToWords($num % 1000000) : '');
    }

    return $this->convertToWords(intval($num / 1000000000)) . ' billion' . (($num % 1000000000 > 0) ? ' ' . $this->convertToWords($num % 1000000000) : '');
}


function getAllRefNo($lead_id)
{
	$this->db->select('a.id,a.ref_no,a.lead_id,machine_model_no,u.first_name,u.last_name')->from('quotation_customer_data a')->join('leads b','a.lead_id=b.id')->join('system_users u','u.user_id=b.added_by','left');
	// if($_SESSION['logged_in']['user_id']!=161 && $_SESSION['logged_in']['user_id']!=61 && $_SESSION['logged_in']['user_id']!=149 && $_SESSION['logged_in']['user_id']!=162)
	// {
	// 	$this->db->where('b.added_by',$_SESSION['logged_in']['user_id']);
	// }
	$this->db->where('b.id!=',$lead_id);
	$rest=$this->db->get();
	return $rest->result();
}

function checkIfQuoteCreated($lead_id)
{
	$rest=$this->db->select('id')->from('quotation_customer_data')->where('lead_id',$lead_id)->get();
	return $rest->num_rows();

}


function checkOwnervsViewer($lead_id)
{
	if($_SESSION['logged_in']['user_id']!=161 && $_SESSION['logged_in']['user_id']!=61 && $_SESSION['logged_in']['user_id']!=149 && $_SESSION['logged_in']['user_id']!=162 && $_SESSION['logged_in']['user_id']!=139)
	{
	$rest=$this->db->select('id')->from('leads')->where('id',$lead_id)->where('added_by',$_SESSION['logged_in']['user_id'])->get();
	return $rest->num_rows();
	}else
	{
		return 1;
	}

}


function showMsg($content)
{
    echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Message</title>
<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f0f2f5;
        font-family: "Segoe UI", Arial, sans-serif;
    }

    .msg-box {
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-left: 10px solid #00b35c;
        max-width: 500px;
        text-align: center;
        animation: popIn 0.25s ease-out;
        font-weight: 700;
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }

    .msg-title {
        font-size: 26px;
        margin-bottom: 12px;
        color: #111;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .msg-text {
        font-size: 19px;
        color: #222;
        line-height: 1.5;
        font-weight: 600;
    }
</style>
</head>
<body>

<div class="msg-box">
    <div class="msg-title">Notice</div>
    <div class="msg-text">'. $content .'</div>
</div>

</body>
</html>';

    exit;
}


function getCustomTerms($record_id)
{
	$data=array();
	$rest=$this->db->select('*')->from('quotation_custom_terms')->where('quotation_id',$record_id)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $row);
		$data[]=$row->liquidated_applicable;
		$data[]=$row->liquidated_text;
		$data[]=$row->packing_charges;
		$data[]=$row->insurance;
		$data[]=$row->installation;
		$data[]=$row->late_delivery_applicable;
		$data[]=$row->late_delivery_text;
	}

	return $data;

}

function getannexture_1_for_dompdf($record_id)
{
    $data = array();

    $row = $this->db
        ->select('machine_type,machine_orientation,product_to_be_packed,product_name,
        pouch_size_type,qty_to_be_packed,horizontal_sealing_width,
        vertical_sealing_width,perforation_pitch,perforationstyle,
        batchcut,typeofsealing,plc_make,power_supply,
        liquidviscositydata,liquidconductivitydata,
        powderdensitydata,powderdfrdata,powdermoisturecontentdata,
        liquid_option,powder_option,non_viscous_option,
        viscous_option,piston_filler_option,follow_meter_option,
        free_flow_option,weigher_system_option,liner_weigher_option,
        mult_head_weigher_option,volumetric_cap_option,
        non_free_flow_option,cup_filler_option')
        ->from('quotation_annexture_1')
        ->where('record_id',$record_id)
        ->get()
        ->row();

    if($row)
    {
        $data[] = $row->product_to_be_packed ?? '';
        $data[] = $row->product_name ?? '';
        $data[] = $row->pouch_size_type ?? '';
        $data[] = $row->qty_to_be_packed ?? '';
        $data[] = $row->horizontal_sealing_width ?? '';
        $data[] = $row->vertical_sealing_width ?? '';
        $data[] = $row->perforation_pitch ?? '';
        $data[] = $row->typeofsealing ?? '';
        $data[] = $row->plc_make ?? '';
        $data[] = $row->power_supply ?? '';
        $data[] = $row->liquidviscositydata ?? '';
        $data[] = $row->liquidconductivitydata ?? '';
        $data[] = $row->powderdensitydata ?? '';
        $data[] = $row->powderdfrdata ?? '';
        $data[] = $row->powdermoisturecontentdata ?? '';
        $data[] = $row->perforationstyle ?? '';
        $data[] = $row->batchcut ?? '';
        $data[] = $row->liquid_option ?? '';
        $data[] = $row->powder_option ?? '';
        $data[] = $row->non_viscous_option ?? '';
        $data[] = $row->viscous_option ?? '';
        $data[] = $row->piston_filler_option ?? '';
        $data[] = $row->follow_meter_option ?? '';
        $data[] = $row->free_flow_option ?? '';
        $data[] = $row->weigher_system_option ?? '';
        $data[] = $row->liner_weigher_option ?? '';
        $data[] = $row->mult_head_weigher_option ?? '';
        $data[] = $row->volumetric_cap_option ?? '';
        $data[] = $row->non_free_flow_option ?? '';
        $data[] = $row->machine_type ?? '';
        $data[] = $row->machine_orientation ?? '';
        $data[] = $row->cup_filler_option ?? '';
    }

    return $data;
}

function getannexture_2_for_dompdf($record_id)
{
    $data = array();

    $row = $this->db
        ->select('filling_accuracy,machinemodel,sealingstyle,speed,
        actual_speed,no_of_track,leminate_specification,
        product_to_be_packed,filling_capacity,pouch_size,
        sealing_drives,perforation_and_cutting,laminate_draw,
        laminate_tracking,electrical_spec,layout_dimensions,
        machine_weight,gross_weight,compressed_air,
        laminatereeldia,laminatereelcoredia,compressedairbar')
        ->from('quotation_annexure_2')
        ->where('record_id',$record_id)
        ->get()
        ->row();

    if($row)
    {
        $data[] = $row->machinemodel ?? '';
        $data[] = $row->sealingstyle ?? '';
        $data[] = $row->speed ?? '';
        $data[] = $row->no_of_track ?? '';
        $data[] = $row->leminate_specification ?? '';
        $data[] = $row->product_to_be_packed ?? '';
        $data[] = $row->filling_capacity ?? '';
        $data[] = $row->pouch_size ?? '';
        $data[] = $row->sealing_drives ?? '';
        $data[] = $row->perforation_and_cutting ?? '';
        $data[] = $row->laminate_draw ?? '';
        $data[] = $row->laminate_tracking ?? '';
        $data[] = $row->electrical_spec ?? '';
        $data[] = $row->layout_dimensions ?? '';
        $data[] = $row->machine_weight ?? '';
        $data[] = $row->compressed_air ?? '';
        $data[] = $row->actual_speed ?? '';
        $data[] = $row->laminatereeldia ?? '';
        $data[] = $row->laminatereelcoredia ?? '';
        $data[] = $row->gross_weight ?? '';
        $data[] = $row->compressedairbar ?? '';
        $data[] = $row->filling_accuracy ?? '';
    }

    return $data;
}



}