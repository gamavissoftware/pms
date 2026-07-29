<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Notification_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
	}
	
	function getSalesVisitReportCount() {
      $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as fname, c.last_name as lname')
                        ->from('visit_schedule a')
                        ->join('system_users b','a.employee_id=b.user_id','left')
                        ->join('system_users c','a.added_by=c.user_id','left')
                        ->order_by('a.visit_date','desc')
                        ->get();
    
        return $query->num_rows();
    }
    
    function getDailyUpdateReportCount() {
     $query = $this->db->select('a.*, b.first_name, b.last_name')
                       ->from('field_sales_daily_update a')
                       ->join('system_users b','a.employee_id=b.user_id','left')
                       ->order_by('a.id','desc')
                       ->get();
    
        return $query->num_rows();
    }
    
    function getPaymentCollectionReportCount() {
     $paymentcol = array();
     $paymentcol[] = 0;
     
     $query = $this->db->select('*')
                       ->from('payment_collected')
                       ->order_by('id','ASC')
                       ->get();
                       
        if($query->num_rows() > 0) {
             foreach($query->result() as $row) {
                $sql = $this->db->select('first_name,last_name')
                                ->from('system_users')
                                ->where('user_id',$row->addedBy)
                                ->get();
                                
                    if($sql->num_rows() > 0) {
                        $paymentcol[] = 1;
                    }
             }
        }
    
        return array_sum($paymentcol);
    }
    
       function getHodDashboardReportCount() {
         
       $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
                         ->from('engineer_visit a')
                         ->join('system_users b','a.user_id=b.user_id','left')
                         ->join('system_users c','a.engineer=c.user_id','left')
                         ->where('a.final_status','0')
                         ->order_by('a.id','desc')
                         ->get();
                           
        
            return $query->num_rows();
    }
    
      function getVisitHistoryReportCount() {
         
          $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
                            ->from('engineer_visit a')
                            ->join('system_users b','a.user_id=b.user_id','left')
                            ->join('system_users c','a.engineer=c.user_id','left')
                            ->where('a.final_status','1')
                            ->order_by('a.id','desc')
                            ->get();
        
            return $query->num_rows();
    }
    
        function getViewDashboardReportCount() {
         $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
                          ->from('engineer_visit a')
                          ->join('system_users b','a.user_id=b.user_id','left')
                          ->join('system_users c','a.engineer=c.user_id','left')
        		          ->where('a.case_status','0')
                          ->order_by('a.id','desc')
                          ->get();
        
            return $query->num_rows();
    }
    
    function getFocDashboardReportCount() {$team="";
        $query = $this->db->select('team_id')
         	              ->from('prestogroup_teams')
         	              ->get();         	      
     	  if($query->num_rows() > 0) {        
         	  foreach($query->result() as $teamdetail);
         	  $sql = $this->db->select('team_id, employee_id')
         	                    ->from('presto_team_members')
         	                    ->where('team_id',$teamdetail->team_id)
         	                    ->get();
         	    
         	  if($sql->num_rows() > 0) {                
             	  foreach($sql->result() as $teaminfo) {
                        $teammembers[] = $teaminfo->employee_id;
                    }
         	  }
     	  } else {
		    $team='NA';
		    }if($team!=='NA') { $query =$this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
            	            ->from('engineer_visit a')
            	            ->join('system_users b','a.user_id=b.user_id','left')
            	            ->join('system_users c','a.engineer=c.user_id','left')
            	            ->where('a.warrenty_status','Warranty Expired')
            	            ->where('a.chargable','2')
                            ->where_in('a.engineer',$team,false)
                            ->order_by('a.id','desc')
                            ->get();
		}
        
            return $query->num_rows();
    }
    
    function getPaymentFollowupReportCount() {
     $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
                        ->from('engineer_visit a')
                        ->join('system_users b','a.user_id=b.user_id','left')
                        ->join('system_users c','a.engineer=c.user_id','left')
                        ->where('a.next_status','Payment follow-up')
                        ->where('a.final_status','1')
		                ->order_by('a.id','desc')
		                ->get();
        
            return $query->num_rows();
    }
    
    function getTechnicalSupportReportCount() {
    $query = $this->db->select('a.*, b.first_name, b.last_name, c.first_name as efname, c.last_name as e_lname')
                      ->from('engineer_visit a')
                      ->join('system_users b','a.user_id=b.user_id','left')
                      ->join('system_users c','a.engineer=c.user_id','left')
                      ->where('next_status','Phone Call')
                      ->where('final_status','0')
                      ->order_by('a.id','desc')
                      ->get();

        
            return $query->num_rows();
    }
    
    function getQcRepairReportCount() {
      $techsupport=array();
      $techsupport[]=0;
      $sql = $this->db->select('*')
                      ->from('service_repair_request')
                      ->order_by('qc','ASC')
                      ->get();
                      
        if($sql->num_rows() > 0) {
            foreach($sql->result() as $row) {
                
			$query = $this->db->select('first_name,last_name')
			                  ->from('system_users')
			                  ->where('user_id',$row->addedBy)
			                  ->get();
			                  
			    if($query->num_rows()>0) {
			        $techsupport[]=1;
			    }	              
    			
            }
        }
        
        return array_sum($techsupport);
    }
    
    function getServiceRepairReportCount() {
      $repaircount=array();
      $repaircount[]=0;
      $sql = $this->db->select('*')
                      ->from('service_repair_request')
                      ->where('flag', 0)
                      ->order_by('qc','ASC')
                      ->get();

            if($sql->num_rows()>0) {

				foreach($sql->result() as $row) {
				    $query = $this->db->select('a.insname,a.id')
				                      ->from('service_repair_instruments a')
				                      ->where('a.serviceid',$row->id)
				                      ->get();
			    if($query->num_rows()>0) {
    				foreach($query->result() as $rows) {
    				    $sql1=$this->db->select('image,remarks')
    				                   ->from('qc_service_repair_request')
    				                   ->where('repairid',$rows->id)
    				                   ->where('instrumentdetailid',$rows->id)
    				                   ->get();
    				    if($sql1->num_rows()>0)
    				    {
    				        $repaircount[] = 1;
    				    }
    				}
                 }
		    } 
		
        }
        
        return array_sum($repaircount);
    }
    
    function getPendingOrderReportCount() {
      $total=array();
      $total[]=0;
      $query = $this->db->select('a.order_id')
                        ->from('order_instruments a')
                        ->join('prestogroup_orders b','a.order_id=b.order_id')
                        ->join('order_planning d','d.order_id=b.order_id')
                        ->join('system_users c','b.marketing_person=c.user_id')
                        ->where('a.complete','0')
                        ->where('factory !=','2')
                        ->group_by('a.order_id')
                        ->get();
                        
            if($query->num_rows()>0) {
            
            foreach($query->result() as $order1) {
                $inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')
                               ->from('presto_instruments a')
                               ->join('order_instruments b','a.id=b.item_id')
                               ->join('order_planning c','b.id=c.jobcard_id')
                               ->where('b.complete','0')
                               ->where('b.order_id',$order1->order_id)
                               ->get();
                               
                    if($inst->num_rows()>0) {
                       $total[]=1; 
                    }
            }
        }
        
        return array_sum($total);
    }
    
    function getSalesPendingOrderReportCount() {
        $total = array();
        $total[] = 0; 
        	$order=$this->db->select('a.order_id')
        	                ->from('order_instruments a')
        	                ->join('prestogroup_orders b','a.order_id=b.order_id')
        	                ->join('order_planning d','d.order_id=b.order_id')
        	                ->join('system_users c','b.marketing_person=c.user_id')
        	                ->where('a.complete','0')
        	                ->where('factory !=','2')
        	                ->where('b.marketing_person',$_SESSION['logged_in']['user_id'])
        	                ->group_by('a.order_id')
        	                ->order_by('b.added_on','ASC')
        	                ->get();
        	
        	if($order->num_rows()>0) {
			foreach($order->result() as $order1) {
			    $inst=$this->db->select('c.factory,b.id,a.instruments_name,b.job_card_no')
			                   ->from('presto_instruments a')
			                   ->join('order_instruments b','a.id=b.item_id')
			                   ->join('order_planning c','b.id=c.jobcard_id')
			                   ->where('b.complete','0')
			                   ->where('b.order_id',$order1->order_id)
			                   ->get();
			                   
			         if($inst->num_rows()>0) {
			             $total[] = 1;
			         }
			}
		}
		
		return array_sum($total); 
    }
    
    function getMachinePackingReportCount() {
        $pack = $this->db->select('b.factory,b.id as planid,a.id,a.job_card_no,b.plannedOn,c.instruments_name')
                         ->from('order_instruments a')
                         ->join('order_planning b','a.id=b.jobcard_id')
                         ->join('presto_instruments c','a.item_id=c.id')
                         ->where('a.complete','1')
                         ->where('a.packed','0')
                         ->get();
                         
        return $pack->num_rows(); 
    }
    
     function getServiceMachinePackingReportCount() {
        $pack=$this->db->select('b.factory,b.id as planid,a.id,a.job_card_no,b.plannedOn,c.instruments_name')
                        ->from('order_instruments a')
                        ->join('order_planning b','a.id=b.jobcard_id')
                        ->join('presto_instruments c','a.item_id=c.id')
                        ->join('prestogroup_orders d','a.order_id=d.order_id')
                        ->where('a.complete','1')
                        ->where('a.packed','0')
                        ->where('d.order_type','SERVICE')
                        ->get();
                         
        return $pack->num_rows(); 
    }
    
    function getReadyDispatchReportCount() {
      $query = $this->db->select('a.order_id')
                        ->from('prestogroup_orders a')
                        ->join('system_users b','a.marketing_person=b.user_id','left')
                        ->where('a.order_status','1')
                        ->where('a.closeorder','0')
                        ->where('a.movetodispatch','0')
                        ->order_by('a.order_id','desc')
                        ->get();
		return $query->num_rows(); 
    }
    
    function getReorderReportCount() {
        $query = $this->db->select('id')
                          ->from('presto_instruments')
                          ->where('stock<minstock')
                          ->where('minstock !=','0')
                          ->where_in('type','0','1',false)
                          ->order_by('instruments_name','ASC')
                          ->get();
                          
            return $query->num_rows(); 
    }
    
    function getDispatchTomReportCount() {
      $query = $this->db->select('a.order_id')
                        ->from('prestogroup_orders a')
                        ->join('system_users b','a.marketing_person=b.user_id','left')
                        ->where('a.order_status','1')
                        ->where('a.movetodispatch','1')
                       
                        ->get();
                        
       $sql = $this->db->select('a.id')
                        ->from('dynamic_form_data a')
                        ->join('dynamic_form_data_mark_done b','a.id=b.record_id','left')
                        ->where('a.form_id','6')
                        ->where('a.work_status','0')
                        ->order_by('a.id','desc')
                        ->get();
                        
        return $query->num_rows() + $sql->num_rows();
    }
    
    function getReadyBillingReportCount() {
      $query = $this->db->select('a.order_id')
                        ->from('prestogroup_orders a')
                        ->join('system_users b','a.marketing_person=b.user_id','left')
                        ->where('a.order_status','1')
                        ->where('a.closeorder','0')
                        ->where('a.movetodispatch','1')
                        ->where('a.totalpacket !=','')
                        ->get();
                        
        return $query->num_rows();
    }
    
    function getSalesDispatchReportCount() {
     $query = $this->db->select('a.order_id')
                       ->from('prestogroup_orders a')
                       ->join('system_users b','a.marketing_person=b.user_id','left')
                       ->where('a.order_status','1')
                       ->where('a.movetodispatch','1')
                       ->get();
                       
        return $query->num_rows();
    }
    
    function getServiceRequestReportCount() {
         $total = array();
         $total[] = 0;
         $query = $this->db->select('a.order_id')
                           ->from('prestogroup_orders a')
                           ->join('system_users b','a.marketing_person=b.user_id','left')
                           ->where('a.order_status','1')
                           ->where('a.closeorder','0')
                           ->where('a.installation_charges','1')
                           ->get();
              if($query->num_rows()>0){             
            foreach($query->result() as $row) {
                $completedjobcard=$this->db->select('id')
                                           ->from('order_instruments')
                                           ->where('order_id',$row->order_id)
                                           ->where('complete','1')
                                           ->where('packed','1')
                                           ->get();
			    $completedjobcards=$completedjobcard->num_rows();
			    $query1 = $this->db->select('a.finalpacked')
			                       ->from('order_instruments a')
			                       ->join('presto_instruments b','a.item_id=b.id','left')
			                       ->where('a.order_id',$row->order_id)
			                       ->get();
			                       
			     $alljobcard=$query1->num_rows();
			     
			     if($completedjobcards==$alljobcard) {
			         	$finalpacked=array();
			            foreach($query1->result() as $instruments) {
			                if($instruments->finalpacked=='1') {
				                $finalpacked[]=1;
					       } else {
				                $finalpacked[]=0;
					       }
			            }
			            
			            if(!in_array("0", $finalpacked)) {
			               $total[] = 1; 
			            }
			     }
                
            }
			  }               
        return array_sum($total);
    }
    
    function getLotOrderReportCount() {
        $type = "'0','1'";
        $query = $this->db->select('id')
                          ->from('presto_instruments')
                          ->where_in('type',$type,false)
                          ->where('status','1')
                          ->get();
                          
          return $query->num_rows();  
    }
    
    function getStartEndReportCount() {
        $query=$this->db->select('a.id')
                        ->from('order_instruments a')
                        ->join('prestogroup_orders b','a.order_id=b.order_id')
                        ->join('presto_instruments c','a.item_id=c.id')
                        ->join('system_users d','b.marketing_person=d.user_id')
                        ->join('order_planning e','a.id=e.jobcard_id')
                        ->where('a.complete','1')
                        ->where('a.packed','1')
                        ->where('b.order_status','1')
                        ->where('b.closeorder','0')
                        ->where_in('e.factory','1','4','5',false)
                        ->get();
                        
        return $query->num_rows();  
    }
    
    function getMrnReportCount() {
        $query = $this->db->select('a.id')
                          ->from('purchase_order a')
                          ->where('a.approved','1')
                          ->where('a.gateentrycomplete','0')
                          ->where('a.completed','0')
                          ->get();
        return $query->num_rows();
    }
    
    function getQcReportCount() {
        $query = $this->db->select('a.id')
                          ->from('mrn a')
                          ->join('purchase_order b','a.poid=b.id')
                          ->where('b.approved','1')
                          ->where('a.qcstatus','0')
                          ->group_by('a.id')
                          ->get();
                          
            return $query->num_rows();
        
    }
    
    
    function getStoreReceiptReportCount() {
        $query = $this->db->select('a.id')
                          ->from('mrn_history a')
                          ->join('mrn c','a.record_id=c.id')
                          ->where('a.storereciept','0')
                          ->group_by('a.id')
                          ->get();
                          
        return $query->num_rows(); 
    }
    
    
    function getTodaysIssuedReportCount() {
        $firstdate=date('Y-m-d')." 00:00:00";
    	$lastdate=date('Y-m-d')." 23:59:59";
    	$query = $this->db->select('a.id')
    	                  ->from('issuestocktousers a')
    	                  ->where('a.issuedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"')
    	                  ->get();
    	                  
    	   return $query->num_rows();
    }
    
    function getAuditMaterialReportCount() {
       $query = $this->db->select('a.id')
                         ->from('issuestocktousers a')
                         ->where('a.storeaccept','0')
                         ->get();
                         
        return $query->num_rows();
    }
    
     function getMaterialIssuedReportCount() {
       $query = $this->db->select('a.id')
                         ->from('issuestocktousers a')
                         ->where('a.storeaccept','0')
                         ->get();
                         
        return $query->num_rows();
    }
    
      function getQcRejectedReportCount() {
       $query = $this->db->select('a.id')
                         ->from('item_rejection_request
 a')
                         ->join('mrn_history b','a.mrnhistoryid=b.id','left')
                         ->order_by('a.approved','ASC')
                         ->order_by('a.addedOn','DESC')
                         ->get();
                         
        return $query->num_rows();
    }
    
    function getIssueInwardReportCount() {
    
        $query = $this->db->select('a.id')
    					  ->from('service_material_request a')
    					  ->join('system_users b','a.service_engineer_name=b.user_id')
    					  ->get();
    					  
    	return $query->num_rows();
    }
    
    function getRgpChallanReportCount() {
       $query = $this->db->select('a.id')
                         ->from('outwardchallan a')
                         ->join('system_users f','a.addedBy=f.user_id','left')
                         ->join('system_users g','a.supplier=f.user_id','left')
                         ->group_by('a.id')
                         ->get();
                         
          return $query->num_rows();
    }
    
    function getAnytimeStoreReportCount() {
        $query = $this->db->select('a.id')
                      ->from('anytime_rejection a')
                      ->join('system_users b','a.yourname=b.user_id','left')
                      ->where('store_status','0')
                      ->get();
                      
            return $query->num_rows();
    }
    
    function getAnytimeRgpReportCount() {
        $query = $this->db->select('a.id')
                         ->from('anytime_rejection a')
                         ->join('system_users b','a.yourname=b.user_id','left')
                         ->join('vendors c','a.vendor_id=c.id','left')
                         ->where('a.purchase_status','1')
                         ->where('a.store_status','1')
                         ->where('a.rgp_status','0')
                         ->get();
          return $query->num_rows();  
    }
    
    function getAnytimeGateReportCount() {
    $query = $this->db->select('a.id')
                      ->from('anytime_rejection a')
                      ->join('system_users b','a.yourname=b.user_id','left')
                      ->join('vendors c','a.vendor_id=c.id','left')
                      ->where('a.purchase_status','1')
                      ->where('a.store_status','1')
                      ->where('a.rgp_status','1')
                      ->where('a.gate_entry_status','0')
                      ->get();
        
        return $query->num_rows();              
        
    }
    
    function getAnytimeQcReportCount() {
    
    $query = $this->db->select('a.id')
                      ->from('anytime_rejection a')
                      ->join('system_users b','a.yourname=b.user_id','left')
                      ->join('vendors c','a.vendor_id=c.id','left')
                      ->where('a.purchase_status','1')
                      ->where('a.store_status','1')
                      ->where('a.rgp_status','1')
                      ->where('a.gate_entry_status','1')
                      ->where('a.qc_status','0')
                      ->get();
                      
        return $query->num_rows();
    
    }
    
    function getPendingIndentReportCount() {
        $query = $this->db->select('a.id')
                          ->from('intend_request a')
                          ->join('system_users e','e.user_id=a.addedBy')
                          ->where('a.approvalstatus','0')
                          ->where('a.cancel_status','0')
                          ->group_by('a.indendno')
                          ->get();
                          
        return $query->num_rows();
    }
    
    function getPrPoReportCount() {
        $query = $this->db->select('a.id')
                          ->from('purchase_request a')
                          ->join('machine_parts_with_picture d','d.id=a.masterid')
                          ->join('system_users e','e.user_id=a.addedBy')
                          ->where('a.approvalstatus','0')
                          ->where('a.closed','0')
                          ->limit(50)
                          ->get();
                          
        return $query->num_rows();
    }
    
    function getPendingPoApprovalReportCount() {
        $query = $this->db->select('a.id')
						  ->from('purchase_order a')
						  ->join('system_users e','e.user_id=a.addedBy','left')
						  ->join('purchase_request h','h.prno=a.prno','left')
						  ->join('vendors v','a.vendor=v.id','left')
						  ->where('a.approved','0')
						  ->group_by('a.id')
						  ->get();
						  
		return $query->num_rows();	
    }
    
    function getPendingPaymentReportCount() {
      $total=array();
      $total[]=0;
    $query = $this->db->select('a.pono')
                      ->from('purchase_order a')
                      ->join('vendors b','a.vendor=b.id')
                      ->where('a.completed','1')
                      ->where('a.payment','0')
                      ->group_by('a.pono')
                      ->get();
                      
        if($query->num_rows()>0) {
            foreach($query->result() as $Restey1) {
                $allmrn=$this->checkifallmrnaredone($Restey1->pono);
                if(count($allmrn)>0) {
                    $restur=$this->checkmrndata($Restey1->pono,$allmrn);
                    if($restur==true) {
                        $total[]=1;
                    }
                }
            }

    }
                      
        return array_sum($total);
    }
    
    function getPendingEmailReportCount() {
       $query = $this->db->select('a.vendor')
                         ->from('purchase_order a')
                         ->where('a.approved','1')
                         ->where('a.emailnotified','0')
                         ->where('a.completed','0')
                         ->group_by('a.vendor')
                         ->get(); 
                         
            return $query->num_rows();	
    }
    
    function getPoFollowupReportCount() {
        $query = $this->db->select('a.id')
						  ->from('vendor_followup a')
						  ->join('purchase_order b','a.po_no=b.pono')
						  ->join('vendors c','b.vendor=c.id')
						  ->where('a.followup_status!=','1')
						  ->where('a.followup_close','0')
						  ->where('b.approved','1')
						  ->group_by('b.vendor')
						  ->get();
						  
		 return $query->num_rows();	
    }
    
    function getPendingOrderPurReportCount() {
        $query = $this->db->select('a.id')
                          ->from('purchase_order a')
                          ->join('system_users e','e.user_id=a.addedBy','left')
                          ->join('vendors v','a.vendor=v.id','left')
                          ->where('a.approved','1')
                          ->where('a.completed','0')
                          ->get();
                          
        return $query->num_rows();
    }
    
    function getIndentPrReportCount() {
        $query = $this->db->select('a.id')
                          ->from('intend_request a')
                          ->join('purchase_request b','b.sourceid=a.indendno','left')
                          ->join('system_users c','a.addedBy=c.user_id','left')
                          ->where('b.source','2')
                          ->where('a.approvalstatus','0')
                          ->group_by('a.indendno')
                          ->get();
                          
        return $query->num_rows();
    }
    
    function getPrVsPoReportCount() {
        $query = $this->db->select('a.id')
            			  ->from('purchase_request a')
            			  ->join('machine_parts_with_picture d','d.id=a.masterid')
            			  ->join('system_users e','e.user_id=a.addedBy')
            			  ->join('units u','u.id=a.unit')
            			  ->where('a.approvalstatus','0')
            			  ->where('a.closed','0')
                          ->get();
                          
        return $query->num_rows(); 
    }
    
    function checkifallmrnaredone($pono) {
	
    $items=array();
    $Restyr=$this->db->select('itemid')->from('purchase_order')->where('pono',$pono)->where('approved','1')->where('gateentrycomplete','1')->get();
    if($Restyr->num_rows()>0)
    {
        foreach($Restyr->result() as $restyrur)
        {
            $items[]=$restyrur->itemid;
        }
         
    }
    
    return $items;
}

function checkmrndata($pono,$allmrn)
{
    $mdata=array();
    $itemss = "'" . implode ( "', '", $allmrn ) . "'";
   
    $Resteyu=$this->db->select('id')->from('mrn')->where('pono',$pono)->where_in('itemid',$itemss,false)->group_by('itemid',$itemss)->get();
    if(count($allmrn)==$Resteyu->num_rows())
    {
    return true;
    
    }else
    {
    return false;
    }
     
 }
	
}