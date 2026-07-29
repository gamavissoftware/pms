<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fetch_dynamic_data extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$this->load->model('User_model','user');
		$this->load->model('Fms_model','Fms_model');
		$this->load->model('Store_model','Store_model');
		$this->load->model('Dashboard_model','reportingdata');
		$user_id =$this->session->userdata['logged_in']['user_id'];
		if(empty($user_id))
		 {
		 redirect(site_url(),'refresh');
		 }
	
	}
	
	public function getdynamic_data(){
		$id = $this->uri->segment(3);
		$q = $this->db->select('report_count')->from('administrator_dashboard')->where('report_id',$id)->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
	}
	
	public function plannedvsactual_data(){
		
		
                                $f=array('1,4,5,8,9');
                                $as=array();
                                    $query = $this->db->select('a.production_flow_id,a.fms_flow,a.flow_id as recordid')->from('fms_flow a')->where('a.status','1')->where_in('a.production_flow_id',$f,false)->get();
	                                	$res = $query->result();
	                                	foreach($res as $row){
	                                	 
	                                	$pendcounts= $this->Fms_model->dashboardplannedtoactual($row->production_flow_id,$row->recordid);
	                                	
	                                	$as+=[$row->recordid=>$pendcounts];
	                                	}
	                                	
	                                		arsort($as);
	                                
	                       
                                foreach($as as $x=>$x_value)
                                {
                                    
                                $resytsyyud=$this->db->select('fms_flow,production_flow_id')->from('fms_flow')->where('flow_id',$x)->get();
                                foreach($resytsyyud->result() as $resytsyyud1);
                                $productionfloname=$this->Fms_model->productionname($resytsyyud1->production_flow_id);
                                if($productionfloname<>'')
                                {
                                   $prods="-".$productionfloname;
                                }else
                                {
                                    $prods='';
                                }
                                
                                echo '<li class="list-group-item">
                                    <a href="'.page_url.'FMS/pendingorder/'.$x.'/0" class="user-list-item" target="_blank">
                                        <div class="avatar text-center">
                                       '.$x_value.'
                                        </div>
                                        <div class="user-desc">
                                           <span>'.$resytsyyud1->fms_flow.' "'.$prods.'"</span>
                                        </div>
                                    </a>
                                </li>';
								}
                              $query = $this->db->select('a.id as recordid, a.dashboard_title')->from('dynamic_forms a')->where('a.form_running_status','0')->where('a.show_in_plan_actual','0')->order_by('a.dashboard_title','asc')->get();
                                $res = $query->result();
                                $i=1;
                                foreach($res as $row)
                                { 	
                                    
                                $todaysdate = date('Y-m-d');
                                $time = "23:59:59";
                                $finaldate = $todaysdate." ".$time;
                                $starttime= date('Y-m-d')." 00:00:00";
                                $q = $this->db->select('id, planned_date')->from('dynamic_form_data')->where('form_id',$row->recordid)->where('work_status','0')->get();
                                //->where('planned_date<=',$finaldate)
                                $totalpending = count($q->result());
                                   
                                   if($row->recordid=='58')
                                   {
                                      	$storeocpendcounts= $this->Store_model->getqcrejectedcount();
                                      	$totalpending=$totalpending+$storeocpendcounts;
                                   }
                                
                                echo '<li class="list-group-item">
                                    <a href="'.page_url.'Form/data_report/'.$row->recordid .'/0" class="user-list-item" target="_blank">
                                        <div class="avatar text-center">
                                       '.$totalpending.'
                                        </div>
                                        <div class="user-desc">
                                           <span>'.$row->dashboard_title.'</span>
                                        </div>
                                    </a>
                                </li>';
                                
                                
                                
                               
                                }
                               
		
		
	}
	
function completestockvalue(){
	$restyui=$this->Fms_model->totalstockvalue();
                        $rt=count($restyui);
                        $p=1;
                        foreach($restyui as $totval)
                        {
                            if($p==$rt)
                            {
                        $hsou1=explode('-',$totval);
                            }
                        
                        $p++;
                            
                        }
						
						echo round($hsou1[1]); exit;
	
}

function totalstockvaluedata(){
	$restyui=$this->Fms_model->totalstockvalue();
	$rt=count($restyui);
	
                                $r=1;
                                 foreach($restyui as $restyui1)
                                {
                                    if($r<>$rt)
                                    {
                                    $hsou=explode('-',$restyui1);
                                    $val=round($hsou[0]);
                                    $label=$hsou[1];
                                    }else
                                    {
                                     $val='';
                                    $label='';
                                    }
                                    
                                    if(trim($label)=='WIP COST')
                                    {
                                        $a=page_url.'Reporting/machinecostprice';
                                    }else if(trim($label)=='FINISHED GOODS')
                                    {
                                        $a=page_url.'Reporting/finishedvalue';
                                    }else
                                    {
                                        $a='#';
                                    }
                                
                               echo '<li class="list-group-item">
                                    <a href="'.$a.'" class="user-list-item" style="text-decoration:none;color:none;">
                                        <div class="avatar text-center" style="width: 188px;">
                                          '.$label.'
                                        </div>
                                        <div class="user-desc">
                                           <span>'.$val.'</span>
                                        </div>
                                    </a>
                                </li>';
                                
                              $r++;  }
                                
	
}

public function notification_data(){

                                $i=1;
                                $q = $this->db->select('event_date, news_events, image')->from('presto_news_events')->order_by('event_date','desc')->get();
                                $count = count($q->result());
                                foreach($q->result() as $row){
                                
                                     echo '<li class="list-group-item">';
                                  
                                  		if($row->image<>''){
                                        echo '<div class=" col-md-4 avatar text-center" style="float:left">';
                                           	if($row->image<>''){
                                           		$d=explode('.',$row->image);
                                        if(count($d)>0)
                                        {
                                        	if($d[1]=='pdf' || $d[1]=='PDF')
                                        	{
                                        echo '<a href="'.eventimgpath.''.$row->image.'" target="_blank"><img src="'.page_url1.'pdf.png" style="width:30px"></a>';
                                        	}else
                                        	{
												echo '<img src="'.eventimgpath.''.$row->image.'" style="width:30px">';
					}
					}
		    
		    }else{
		   //echo '<i class="zmdi zmdi-circle text-primary"></i>';
		     }

		 	}
                                        echo '</div>
                                        <div class="user-desc">
                                           <span class="">'.$row->news_events.'</span>
                                        </div>
                                   
                                </li>';
}

				  $query = $this->db->select('first_name, last_name,profile_image')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where("MONTH(date_of_birth) = MONTH(NOW()) AND DAY(date_of_birth) = DAY(NOW())")->get();
				  if($query->num_rows()>0){
				      foreach($query->result() as $birthday){
				 
                                echo '<li class="list-group-item">
                                    <a href="#" class="user-list-item">
                                        <div class="avatar text-center">
<i class="fa fa-birthday-cake" aria-hidden="true" style="color:pink"></i>

                                        </div>
                                        <div class="user-desc">
                                            
                                            <span class="desc">Happy Birthday '.$birthday->first_name.' '.$birthday->last_name.'</span>
                                        </div>
                                    </a>
                                </li>';
}}
                             
                           
}

public function totalorders(){
		$userinfo = "0";
		$lcountall=$this->Fms_model->nondispatchedorders($userinfo);
		echo $lcountall; exit;
}

public function geteadynamic_data(){
		$id = $this->uri->segment(3);
		$q = $this->db->select('report_count')->from('ea_dashboard')->where('report_id',$id)->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
	}
	
	public function getsalesreportingcount() {
		$id = $this->uri->segment(3);
		$q = $this->db->select('report_count')->from('sales_reporting_dashboard')->where('report_id',$id)->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
	}
	
	public function getservicereportingcount() {
		$id = $this->uri->segment(3);
		$q = $this->db->select('report_count')->from('service_reporting_dashboard')->where('report_id',$id)->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
	}
	
	public function getproductionreportcount() {
		$id = $this->uri->segment(3);
		$q = $this->db->select('report_count')
		              ->from('production_reporting_dashboard')
		              ->where('report_id',$id)
		              ->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
	}
	
    public function	getstorereportcount() {
        $id = $this->uri->segment(3);
		$q = $this->db->select('report_count')
		              ->from('store_reporting_dashboard')
		              ->where('report_id',$id)
		              ->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
    }
    
    public function	getpurchasereportcount() {
        $id = $this->uri->segment(3);
		$q = $this->db->select('report_count')
		              ->from('purchase_reporting_dashboard')
		              ->where('report_id',$id)
		              ->get();
		foreach($q->result() as $row);
		echo $row->report_count; exit;
    }

    function user_wise_approval()
    {
		$app=array();
		$row112=$this->db->select('approval_id')->from('approvals_permission')->where('user_id',$_SESSION['logged_in']['user_id'])->get();
		if($row112->num_rows()>0)
		{
		foreach($row112->result() as $rowss)
		{
		$app[]=$rowss->approval_id;
		}

		}


		$a1=0;
		$a2=0;
		$a3=0;
		$a4=0;
		$a5=0;
		$a6=0;
		$a7=0;
		$a8=0;
		$a9=0;
			
	if(in_array(1,$app))
	{						
	$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('status',0)->get();

	$a1=$resty->num_rows();
	}
// END SPEC FILE 

		if(in_array(2,$app))
		{
		$sql1 = $this->db->select('a.id')
		->from('customer_quotation_detail a')
		->join('customer_quotation b', 'b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id')
		->join('customer_detail d', 'd.id=b.customer_id')
		->join('presto_instruments e', 'e.id=a.product_id')
		->where('a.flag', 0)
		->get();
		$a2=$sql1->num_rows();
		}

		// END QUOTATION


		if(in_array(3,$app))
		{
		$this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,a.added_on')->from('customer_detail a');
		$this->db->join('store_rack_location c','a.company_id=c.id');
		$sql2=$this->db->where('a.payment_term_approval','0')->get();
		$a31=$sql2->num_rows();

		$this->db->select('a.id')->from('payment_change_req a');
		$sql21=$this->db->where('a.status','0')->get();
		$a32=$sql21->num_rows();
		$a3=$a31+$a32;
		}

	 // END PAYMENT TERM APPROVAL

		if(in_array(4,$app))
		{
		$a4=$this->convence_for_approval();
		}



		if(in_array(5,$app))
		{
		$a5=$this->payment_closure_approval();
		}

		if(in_array(6,$app))
		{
		$a6 = $this->reportingdata->orders_on_hold_count_user();
		}



		if(in_array(7,$app))
		{
		$a7 = $this->reportingdata->density_approval();
		}

		if(in_array(8,$app))
		{
		$a8 = $this->equivalent_approval();

		}

		if(in_array(9,$app))
		{
		$a9 = $this->reportingdata->pendingtrailforapproval();
		}



		// END CONVENCE;
		$res=$a1+$a2+$a3+$a4+$a5+$a6+$a7+$a8+$a9;


		echo $res;


    }

    function convence_for_approval()
	{
		
			
		$scheduler_data = array();
		$scheduler_data[]=0;


		for ($l = -6; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-t');

			
		$q=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('convence',1)->where('user_status',1)->get();
		if($q->num_rows()>0)
		{
		foreach($q->result() as $q1)
		{

		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$q1->user_id)->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{ $scheduler_data[] =1;
		}
		}
		}
		}
	}
		return array_sum($scheduler_data);
	}
		
			function payment_closure_approval()
			{

				$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->join('system_users i','i.user_id=a.adjustmentBy')
						  ->where('a.billing', 1)
						  ->where('a.payment', 0)
						  ->where('a.adjustment',1)
						  ->where('a.adjustment_approval',0)

						  ->order_by('a.id','DESC')
				 		  ->get();

				 		  return $query->num_rows();

			}



			 function approval_pending_from_admin()
    {
		$app=array();
		$row112=$this->db->select('approval_id')->from('approvals_permission')->where('user_id',$_SESSION['logged_in']['user_id'])->get();
		if($row112->num_rows()>0)
		{
		foreach($row112->result() as $rowss)
		{
		$app[]=$rowss->approval_id;
		}

		}


		$a1=0;
		$a2=0;
		$a3=0;
		$a4=0;
		$a5=0;
		$a6=0;
		$a7=0;
		$a8=0;
			
							
	$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('product_competitor_files a')->join('system_users b','a.addedby=b.user_id')->where('a.addedby',$_SESSION['logged_in']['user_id'])->where('status',0)->get();

	$a1=$resty->num_rows();
	
// END SPEC FILE 

		
		$sql1 = $this->db->select('a.id')
		->from('customer_quotation_detail a')
		->join('customer_quotation b', 'b.id=a.quotation_id')
		->join('store_rack_location c', 'c.id=b.company_id')
		->join('customer_detail d', 'd.id=b.customer_id')
		->join('presto_instruments e', 'e.id=a.product_id')
		->where('b.added_by',$_SESSION['logged_in']['user_id'])
		->where('a.flag', 0)
		->get();
		$a2=$sql1->num_rows();
		

		// END QUOTATION


		
		$this->db->select('a.id,a.customer_name,a.company_name,a.payment_type,a.credit_days,c.companyname,a.added_on')->from('customer_detail a');
		$this->db->join('store_rack_location c','a.company_id=c.id');
		$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		$sql2=$this->db->where('a.payment_term_approval','0')->get();
		$a31=$sql2->num_rows();

		$this->db->select('a.id')->from('payment_change_req a');
		$this->db->where('a.addedBy',$_SESSION['logged_in']['user_id']);
		$sql21=$this->db->where('a.status','0')->get();
		$a32=$sql21->num_rows();
		$a3=$a31+$a32;
		

	 // END PAYMENT TERM APPROVAL

		
		$a4=$this->convence_for_approval_user();
		



		
		$a5=$this->payment_closure_approval_user();
		

		
		$a6 = $this->reportingdata->orders_on_hold_count_user();
		



		$a7 = $this->reportingdata->density_approval_user();

				


		
		// END CONVENCE;
		$res=$a1+$a2+$a3+$a4+$a5+$a6+$a7;


		echo $res;


    }


     function convence_for_approval_user()
	{
		
			
		$scheduler_data = array();
		$scheduler_data[]=0;


		for ($l = -3; $l <= 0; $l++){

			$m1=date('Y-m', strtotime("$l month"));

			$start_date=date($m1.'-01');
			$end_date=date($m1.'-t');

			

		$query = $this->db->select('a.*,b.first_name,b.last_name,b.convence_type')->from('employee_convence a')->join('system_users b','a.user_id=b.user_id')->where('a.convence_date>=',$start_date)->where('a.convence_date<=',$end_date)->where('send_for_approval',1)->where('approved',0)->where('a.user_id',$_SESSION['logged_in']['user_id'])->group_by('a.user_id')->get();
		if($query->num_rows()>0)
		{
		$res = $query->result();
		$i=1;		
		foreach($res as $row)
		{ $scheduler_data[] =1;
		}
		}
	}
		return array_sum($scheduler_data);
	}
	

	function payment_closure_approval_user()
			{

				$query = $this->db->select('a.id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->join('system_users i','i.user_id=a.adjustmentBy')
						  ->where('a.billing', 1)
						  ->where('a.payment', 0)
						  ->where('a.adjustment',1)
						  ->where('a.adjustment_approval',0)
						  ->where('a.agent',$_SESSION['logged_in']['user_id'])

						  ->order_by('a.id','DESC')
				 		  ->get();

				 		  return $query->num_rows();

			}

			function equivalent_approval()
			{

		$resty=$this->db->select('a.*,b.first_name,b.last_name')->from('equivalent_chart
 a')->join('system_users b','a.addedby=b.user_id','left')->where('a.approved',0)->where('client_product!=','NA')->where('client_product!=','')->get();

		return $resty->num_rows();
			}

		function getmonthlysale()
		{
		$monthlyclosedwonorder=$this->reportingdata->monthlyclosedwonorder();

		$mdata=explode('|',$monthlyclosedwonorder);
		$html='';
		$html.='
		<div class="border-left yellow">
		<p>Month Sales-&nbsp;&nbsp;<span id="monthsalecount">'.$mdata[1].'  Order(s)</span>&nbsp;
		<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
		<span class="tooltiptext">Monthly sale statistics along with previous month comparision. Arrow up means more conversions and down arrow means low conversion (in %) than the last month.</span>
		</div>
		</p>
		<h2 id="monthlysale" class="customfont">'.$mdata[0]." ".$mdata[2].'</h2>
		</div>

		';

		echo $html; 
		}

function getyearlysale()
{
    $monthlyclosedwonorder=$this->reportingdata->yearlyclosedwonorder();

    $mdata=explode('|',$monthlyclosedwonorder);
    $html='';
    $html.='
                                <div class="border-left yellow">
                                   <p>Year Sales-&nbsp;&nbsp;<span id="yearsalecount">'.$mdata[1].'</span>
                                <div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <span class="tooltiptext">Financial Year sale statistics along with previous FY comparision. Arrow up means more conversions and down arrow means low conversion (in %) than the last FY.</span>
                                    </div>
                                    </p>
                                    <h2 id="yearsale" class="customfont">'.$mdata[0]." ".$mdata[2].'</h2>
                                </div>
                    
                    ';

                    echo $html; 


                  
}



	///////////////////////// BASIC MACHINE DF FORM ////////////////////////////


	public function basic_machine_df_project_form_add(){

		// $this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		$norml_id=$this->uri->segment(3);
		$po_id = $this->uri->segment(4);
		$lead_id = $this->uri->segment(5);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/basic_machine_df');
		}else{

			date_default_timezone_set("Asia/Kolkata");

			$data =array(
				'po_id'=>$po_id,
				// 'lead_id'=>$lead_id,
				// 'design_form_name'=>$this->input->post('design_form_name'),
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				// 'iom_no'=>$this->input->post('iom_no'),
				// 'invoice_no'=>$this->input->post('invoice_no'),
				// 'invoice_date'=>$this->input->post('invoice_date'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);



			$this->db->insert('basic_machine_df_design_form_table', $data);

			$latest_id =$this->db->insert_id();

			$data1 = array(

				'record_id'=>$latest_id,
				'ce_complied'=>$this->input->post('ce_complied'),
				'ce_complied_remarks'=>$this->input->post('ce_complied_remarks'),
				'date_of_po'=>$this->input->post('date_of_po'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'penalty_clause'=>$this->input->post('penalty_clause'),
				'penalty_clause_remarks'=>$this->input->post('penalty_clause_remarks'),
				 'dispatch_date'=>date('Y-m-d',strtotime($this->input->post('dispatch_date'))),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				 'trial_date'=>date('Y-m-d',strtotime($this->input->post('trial_date'))),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				'machine_mode_no'=>$this->input->post('machine_mode_no'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				'machine_type'=>$this->input->post('machine_type'),
				'machine_type_remarks'=>$this->input->post('machine_type_remarks'),
				'machine_orientation'=>$this->input->post('machine_orientation'),
				'machine_orientation_remarks'=>$this->input->post('machine_orientation_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine', $data1);


  		$pouch_widths = $this->input->post('pouch_width');
        $pouch_lengths = $this->input->post('pouch_length');
        $pouch_heights = $this->input->post('pouch_height');
		

		  if (!empty($pouch_widths)) {
            for ($i = 0; $i < count($pouch_widths); $i++) {
                $data2 = array(
                    'record_id' => $latest_id,
                    'pouch_width' => $pouch_widths[$i],
                    'pouch_length' => $pouch_lengths[$i],
                    'pouch_height' => $pouch_heights[$i],
                    'pouch_size_remarks' => $this->input->post('pouch_size_remarks'),
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $this->session->userdata['logged_in']['user_id']
                );
                $this->db->insert('basic_machine_df_form_machine_specification_size_qnty', $data2);
            }
        }

		$quantity_packed = $this->input->post('quantity_packed');
		$quantity_units = $this->input->post('quantity_packed_unit');

		if (!empty($quantity_packed)) {
            for ($i = 0; $i < count($quantity_packed); $i++) {
                $dataaa = array(
                    'record_id' => $latest_id,
					 'quantity_packed' => $quantity_packed[$i],
                    'quantity_packed_unit' => $quantity_units[$i],
                    'quantity_packed_remarks' => $this->input->post('quantity_packed_remarks'),
                    'added_on' => date("Y-m-d h:i:s"),
                    'added_by' => $this->session->userdata['logged_in']['user_id']
                );
                $this->db->insert('basic_machine_df_form_machine_spec_qty', $dataaa);
            }
        }


			$data3 = array(
				'record_id'=>$latest_id,
				'tracks'=>$this->input->post('tracks'),
				'product_packed'=>$this->input->post('product_packed'),
				'powder_option'=>$this->input->post('powder_option'),
				'liquid_option'=>$this->input->post('liquid_option'),
				'non_viscous_option'=>$this->input->post('non_viscous_option'),
				'viscous_option'=>$this->input->post('viscous_option'),
				'piston_filler_option'=>$this->input->post('piston_filler_option'),
				'follow_meter_option'=>$this->input->post('follow_meter_option'),
				'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				'cup_filler_option'=>$this->input->post('cup_filler_option'),
				'free_flow_option'=>$this->input->post('free_flow_option'),
				'weigher_system_option'=>$this->input->post('weigher_system_option'),
				'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
				'profile_of_sealing'=>$this->input->post('profile_of_sealing'),
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				'density'=>$this->input->post('density'),
				'viscosity'=>$this->input->post('viscosity'),
				'product_specification_remarks'=>$this->input->post('product_specification_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_machine_specification', $data3);

			$data4 = array(
				'record_id'=>$latest_id,
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'cladding_provision'=>$this->input->post('cladding_provision'),
				'cladding_provision_remarks'=>$this->input->post('cladding_provision_remarks'),
				'embossing'=>$this->input->post('embossing'),
				'embossing_option'=>$this->input->post('embossing_option'),
				'linear_option'=>$this->input->post('linear_option'),
				'rotary_option'=>$this->input->post('rotary_option'),
				'provision_remarks'=>$this->input->post('provision_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),	
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine1', $data4);


			$data5 = array(
				'record_id'=>$latest_id,
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'laminate_pulling'=>$this->input->post('laminate_pulling'),
				'laminate_pulling_remarks'=>$this->input->post('laminate_pulling_remarks'),
				'up_down'=>$this->input->post('up_down'),
				'up_down_remarks'=>$this->input->post('up_down_remarks'),
				'embossing_coding'=>$this->input->post('embossing_coding'),
				'embossing_coding_remarks'=>$this->input->post('embossing_coding_remarks'),
				'cooling_station'=>$this->input->post('cooling_station'),
				'cooling_station_remarks'=>$this->input->post('cooling_station_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'perforation_blade'=>$this->input->post('perforation_blade'),
				'perforation_blade_remarks'=>$this->input->post('perforation_blade_remarks'),
				'priston_drive'=>$this->input->post('priston_drive'),
				'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				
				//'individual_option'=>$this->input->post('individual_option'),
				//'rotary_option1'=>$this->input->post('rotary_option1'),
			
				'shutt_off_nozzle'=>$this->input->post('shutt_off_nozzle'),
				'shutt_off_nozzle_remarks'=>$this->input->post('shutt_off_nozzle_remarks'),
				'filling_plate_drive'=>$this->input->post('filling_plate_drive'),
				'filling_plate_drive_remarks'=>$this->input->post('filling_plate_drive_remarks'),
				'individual_weight'=>$this->input->post('individual_weight'),
				'individual_weight_remarks'=>$this->input->post('individual_weight_remarks'),
				'overall_weight_adjust'=>$this->input->post('overall_weight_adjust'),
				'overall_weight_adjust_remarks'=>$this->input->post('overall_weight_adjust_remarks'),
				'vertical_sealer_width'=>$this->input->post('vertical_sealer_width'),
				'horizontal_sealer_width'=>$this->input->post('horizontal_sealer_width'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine2', $data5);


			$data6 =array(
				'record_id'=>$latest_id,
				'traverse_drive'=>$this->input->post('traverse_drive'),
				'yes_traverse_drive'=>$this->input->post('yes_traverse_drive'),
				'traverse_drive_remarks'=>$this->input->post('traverse_drive_remarks'),
				'printer_yes_no'=>$this->input->post('printer_yes_no'),
				'printer'=>$this->input->post('printer'),
				'inkjet_option'=>$this->input->post('inkjet_option'),
				'tto_option'=>$this->input->post('tto_option'),
				'thermal_inkjet_option'=>$this->input->post('thermal_inkjet_option'),
				'printer_remarks'=>$this->input->post('printer_remarks'),
				'case_packer_drive'=>$this->input->post('case_packer_drive'),
				'case_packer_drive_remarks'=>$this->input->post('case_packer_drive_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				'batch_cut_format'=>$this->input->post('batch_cut_format'),
				'string_option'=>$this->input->post('string_option'),
				'batch_cut_format_remarks'=>$this->input->post('batch_cut_format_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine3', $data6);

			$data7 =array(
				'record_id'=>$latest_id,
				// 'horizontal_sealer1'=>$this->input->post('horizontal_sealer1'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				// 'vertical_sealer1'=>$this->input->post('vertical_sealer1'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'rotary_valve_coating'=>$this->input->post('rotary_valve_coating'),
				'rotary_valve_coating_remarks'=>$this->input->post('rotary_valve_coating_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'reel_shaft_type'=>$this->input->post('reel_shaft_type'),
				'reel_shaft_type_remarks'=>$this->input->post('reel_shaft_type_remarks'),
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_control_system'=>$this->input->post('heater_control_system'),
				'heater_control_system_remarks'=>$this->input->post('heater_control_system_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'supply_voltage'=>$this->input->post('supply_voltage'),
				'supply_voltage_remarks'=>$this->input->post('supply_voltage_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'hmi_size'=>$this->input->post('hmi_size'),
				'hmi_size_remarks'=>$this->input->post('hmi_size_remarks'),
				'cip_system'=>$this->input->post('cip_system'),
				'cip_system_option'=>$this->input->post('cip_system_option'),
				'cip_system_option_remarks'=>$this->input->post('cip_system_option_remarks'),
				'tool_kit'=>$this->input->post('tool_kit'),
				'tool_kit_remarks'=>$this->input->post('tool_kit_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine4', $data7);

			  

			$data8 =array(
				'record_id'=>$latest_id,
				'secondary_pack'=>$this->input->post('secondary_pack'),
				//'yes_secondary_pack'=>implode(', ', $yes_secondary_pack),
				'case_packer'=>$this->input->post('case_packer'),
				'secondary_pack_remarks'=>$this->input->post('secondary_pack_remarks'),
				'ladder_platform'=>$this->input->post('ladder_platform'),
				'ladder_platform_remarks'=>$this->input->post('ladder_platform_remarks'),
				'machine_guarding'=>$this->input->post('machine_guarding'),
				'aluminium_option'=>$this->input->post('aluminium_option'),
				'ss_304_option'=>$this->input->post('ss_304_option'),
				'machine_guarding_remarks'=>$this->input->post('machine_guarding_remarks'),
				//'trial_comments'=>$this->input->post('trial_comments'),
				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->insert('basic_machine_df_form_multi_track_machine5', $data8);


			 $yes_secondary_pack = $this->input->post('yes_secondary_pack');

			  $qty = $this->input->post('yes_secondary_pack_qty');

			 if(!empty($yes_secondary_pack)){

				for($i = 0; $i<count($yes_secondary_pack); $i++){
$pack =array(
'record_id'=>$latest_id,
	'yes_secondary_pack'=>$yes_secondary_pack[$i],
	'yes_secondary_pack_qty'=>$qty[$i],
	'added_on' => date("Y-m-d h:i:s"),
'added_by' => $this->session->userdata['logged_in']['user_id']
);


$this->db->insert('basci_machine_df_form_multi_track_machine_pack', $pack);
				}

			 }

		// 	$part_desc = $this->input->post('special_notes_list');
		// 	$part_qty = $this->input->post('special_notes_qty');

		// 	  if (!empty($part_desc)) {
        //     for ($i = 0; $i < count($part_desc); $i++) {
        //         $data9 = array(
        //             'record_id' => $latest_id,
        //             'special_notes_list' => $part_desc[$i], 
        //             'special_notes_qty' => $part_qty[$i],
        //             'added_on' => date("Y-m-d h:i:s"),
        //             'added_by' => $this->session->userdata['logged_in']['user_id']
        //         );
        //         $this->db->insert('df_form_multi_track_machine6', $data9);
        //     }
        // }

		$user_id=$_SESSION['logged_in']['user_id'];
			$data34 = array('df_id'=>0,
			'taskid'=>114,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);

			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('task_department_wise_scheduling',$data34);

			/*Select PO No*/
			$pono = $this->getpono($this->uri->segment(3));
			/*Select PO No*/

			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
			$data = array('df_id'=>0,
				'taskid'=>86,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$pono,
				'task_status'=>0,
				'remarks'=>'',
				'assigned_user'=>$user_id,
				'userid'=>$user_id,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));

			$this->db->insert('task_department_wise_scheduling',$data);


			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully added.</div>');
		redirect(page_url.'Formats/basic_machine_df_form/'.$po_id);
		}

	}


	//************************************************************************************* */

	public function basic_machine_df_project_form_update(){

		// $this->form_validation->set_rules('design_form_name', 'Design Form', 'required|trim');
		$this->form_validation->set_rules('design_form_date', 'Design Form Date', 'required|trim');
		// $norml_id=$this->uri->segment(3);
		$po_id = $this->uri->segment(3);
		$record_id = $this->uri->segment(4);
			if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('dashboard/basic_machine_df');
		}else{

			date_default_timezone_set("Asia/Kolkata");

			$data =array(
				'po_id'=>$po_id,
				// 'lead_id'=>$lead_id,
				// 'design_form_name'=>$this->input->post('design_form_name'),
				'design_form_date'=>$this->input->post('design_form_date'),
				'reference_no'=>$this->input->post('reference_no'),
				'ref_df_date'=>$this->input->post('ref_df_date'),
				// 'iom_no'=>$this->input->post('iom_no'),
				// 'invoice_no'=>$this->input->post('invoice_no'),
				// 'invoice_date'=>$this->input->post('invoice_date'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);


			$this->db->where('id', $record_id);
			$this->db->update('basic_machine_df_design_form_table', $data);

			
			$dataqty =array(

				'quantity_packed_remarks'=>$this->input->post('quantity_packed_remarks')

			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_machine_spec_qty', $dataqty);


			$datapouch =array(

				'pouch_size_remarks'=>$this->input->post('pouch_size_remarks')

			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_machine_specification_size_qnty', $datapouch);


			$data1 = array(

				
				'ce_complied'=>$this->input->post('ce_complied'),
				'ce_complied_remarks'=>$this->input->post('ce_complied_remarks'),
				'date_of_po'=>$this->input->post('date_of_po'),
				'date_of_po_remarks'=>$this->input->post('date_of_po_remarks'),
				'penalty_clause'=>$this->input->post('penalty_clause'),
				'penalty_clause_remarks'=>$this->input->post('penalty_clause_remarks'),
				 'dispatch_date'=>date('Y-m-d',strtotime($this->input->post('dispatch_date'))),
				'dispatch_date_remarks'=>$this->input->post('dispatch_date_remarks'),
				 'trial_date'=>date('Y-m-d',strtotime($this->input->post('trial_date'))),
				'trial_date_remarks'=>$this->input->post('trial_date_remarks'),
				'machine_mode_no'=>$this->input->post('machine_mode_no'),
				'machine_mode_no_remarks'=>$this->input->post('machine_mode_no_remarks'),
				'machine_type'=>$this->input->post('machine_type'),
				'machine_type_remarks'=>$this->input->post('machine_type_remarks'),
				'machine_orientation'=>$this->input->post('machine_orientation'),
				'machine_orientation_remarks'=>$this->input->post('machine_orientation_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine', $data1);


			$iddd=	$this->input->post('multi_pouch_id');


			if(is_array($iddd)){
				for($y=0; $y<count($iddd); $y++){
				$miidd =$iddd[$y]; 
				$pouch_widths = $this->input->post('pouch_width'.$miidd);
				$pouch_lengths = $this->input->post('pouch_length'.$miidd);
				$pouch_heights = $this->input->post('pouch_height'.$miidd);
			
				$daaa =array(
			
					'pouch_width' => $pouch_widths,
			      'pouch_length' => $pouch_lengths,
			         'pouch_height' => $pouch_heights,
			
				);
			
				$this->db->where('id',$miidd);
				$this->db->update('basic_machine_df_form_machine_specification_size_qnty', $daaa);
			}
			}
			

			if($this->input->post('addmorepack')==1){

				 $pouch_widths = $this->input->post('pouch_width');
         $pouch_lengths = $this->input->post('pouch_length');
         $pouch_heights = $this->input->post('pouch_height');
	
				for($i = 0; $i<count($pouch_widths); $i++){
				$pack =array(
				'record_id'=>$record_id,
				'pouch_width' => $pouch_widths[$i],
				           'pouch_length' => $pouch_lengths[$i],
				             'pouch_height' => $pouch_heights[$i],
				            'pouch_size_remarks' => $this->input->post('pouch_size_remarks'),
				'added_on' => date("Y-m-d h:i:s"),
				'added_by' => $this->session->userdata['logged_in']['user_id']
				);
	
	
				$this->db->insert('basic_machine_df_form_machine_specification_size_qnty', $pack);
				}
	
				}


			

// if ($iii<>'') {
// 	for($y=0; $y<count($iii); $y++){
// 		// $md =$iii[$y]; 
//         $quantity_packed = $this->input->post('quantity_packed' . [$y]);
//         $quantity_units = $this->input->post('quantity_packed_unit' . [$y]);

//         $daata = array(
//             'quantity_packed' => $quantity_packed,
//             'quantity_packed_unit' => $quantity_units
//         );

//         $this->db->where('id', $iii[$y]);
//         $this->db->update('basic_machine_df_form_machine_spec_qty', $daata);
//     }
// }


/////////////////////////

$iii = $this->input->post('multi_qty_id');

if(is_array($iii)){
	for($y=0; $y<count($iii); $y++){
	$miitt =$iii[$y]; 
	$quantity_packed = $this->input->post('quantity_packed'.$miitt);
	$quantity_units = $this->input->post('quantity_packed_unit'.$miitt);


	$daata =array(

		'quantity_packed' => $quantity_packed,
		'quantity_packed_unit' => $quantity_units

	);

	$this->db->where('id', $miitt);
	$this->db->update('basic_machine_df_form_machine_spec_qty', $daata);
}
}



///////////////////////////




				if($this->input->post('addmoreqty')==1){

					$quantity_packed = $this->input->post('quantity_packed');
					$quantity_units = $this->input->post('quantity_packed_unit');
	   
	   
	   
				   for($i = 0; $i<count($quantity_packed); $i++){
				   $mulqty =array(
					'record_id'=>$record_id,
				   'quantity_packed' => $quantity_packed[$i],
							  'quantity_packed_unit' => $quantity_units[$i],
							
							   'quantity_packed_remarks' => $this->input->post('pouch_size_remarks'),
				   'added_on' => date("Y-m-d h:i:s"),
				   'added_by' => $this->session->userdata['logged_in']['user_id']
				   );
	   
	   
				   $this->db->insert('basic_machine_df_form_machine_spec_qty', $mulqty);
				   }
	   
	   
				   }

  		// $pouch_widths = $this->input->post('pouch_width');
        // $pouch_lengths = $this->input->post('pouch_length');
        // $pouch_heights = $this->input->post('pouch_height');
		// $quantity_packed = $this->input->post('quantity_packed');
        // $quantity_units = $this->input->post('quantity_packed_unit');

		//   if (!empty($pouch_widths)) {
        //     for ($i = 0; $i < count($pouch_widths); $i++) {
        //         $data2 = array(
        //             'record_id' => $latest_id,
        //             'pouch_width' => $pouch_widths[$i],
        //             'pouch_length' => $pouch_lengths[$i],
        //             'pouch_height' => $pouch_heights[$i],
        //             'pouch_size_remarks' => $this->input->post('pouch_size_remarks'),
		// 			 'quantity_packed' => $quantity_packed[$i],
        //             'quantity_packed_unit' => $quantity_units[$i],
        //             'quantity_packed_remarks' => $this->input->post('quantity_packed_remarks'),
        //             'added_on' => date("Y-m-d h:i:s"),
        //             'added_by' => $this->session->userdata['logged_in']['user_id']
        //         );
        //         $this->db->insert('basic_machine_df_form_machine_specification_size_qnty', $data2);
        //     }
        // }


			$data3 = array(
				
				'tracks'=>$this->input->post('tracks'),
				'product_packed'=>$this->input->post('product_packed'),
				'powder_option'=>$this->input->post('powder_option'),
				'liquid_option'=>$this->input->post('liquid_option'),
				'non_viscous_option'=>$this->input->post('non_viscous_option'),
				'viscous_option'=>$this->input->post('viscous_option'),
				'piston_filler_option'=>$this->input->post('piston_filler_option'),
				'follow_meter_option'=>$this->input->post('follow_meter_option'),
				'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				'cup_filler_option'=>$this->input->post('cup_filler_option'),
				'free_flow_option'=>$this->input->post('free_flow_option'),
				'weigher_system_option'=>$this->input->post('weigher_system_option'),
				'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
				'profile_of_sealing'=>$this->input->post('profile_of_sealing'),
				'tracks_remarks'=>$this->input->post('tracks_remarks'),
				'product_packed_remarks'=>$this->input->post('product_packed_remarks'),
				'filling_unit_remarks'=>$this->input->post('filling_unit_remarks'),
				'density'=>$this->input->post('density'),
				'viscosity'=>$this->input->post('viscosity'),
				'product_specification_remarks'=>$this->input->post('product_specification_remarks'),
				'profle_sealing_remarks'=>$this->input->post('profle_sealing_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_machine_specification', $data3);

			$data4 = array(
				
				'notching_option'=>$this->input->post('notching_option'),
				'notching_option_remarks'=>$this->input->post('notching_option_remarks'),
				'hooper_details'=>$this->input->post('hooper_details'),
				'openable_option'=>$this->input->post('openable_option'),
				'closed_option'=>$this->input->post('closed_option'),
				'pressurised_option'=>$this->input->post('pressurised_option'),
				'closed_pressurised_option'=>$this->input->post('closed_pressurised_option'),
				'non_pressurised_option'=>$this->input->post('non_pressurised_option'),
				'non_closed_pressurised_option'=>$this->input->post('non_closed_pressurised_option'),
				'hooper_details_remarks'=>$this->input->post('hooper_details_remarks'),
				'cladding_provision'=>$this->input->post('cladding_provision'),
				'cladding_provision_remarks'=>$this->input->post('cladding_provision_remarks'),
				'embossing'=>$this->input->post('embossing'),
				'embossing_option'=>$this->input->post('embossing_option'),
				'linear_option'=>$this->input->post('linear_option'),
				'rotary_option'=>$this->input->post('rotary_option'),
				'provision_remarks'=>$this->input->post('provision_remarks'),
				'web_aligner'=>$this->input->post('web_aligner'),
				'web_aligner_option'=>$this->input->post('web_aligner_option'),
				'web_aligner_remarks'=>$this->input->post('web_aligner_remarks'),	
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine1', $data4);


			$data5 = array(
				
				'center_slitting'=>$this->input->post('center_slitting'),
				'center_slitting_remarks'=>$this->input->post('center_slitting_remarks'),
				'vertical_slitting'=>$this->input->post('vertical_slitting'),
				'vertical_slitting_remarks'=>$this->input->post('vertical_slitting_remarks'),
				'vertical_sealer'=>$this->input->post('vertical_sealer'),
				'vertical_sealer_remarks'=>$this->input->post('vertical_sealer_remarks'),
				'laminate_pulling'=>$this->input->post('laminate_pulling'),
				'laminate_pulling_remarks'=>$this->input->post('laminate_pulling_remarks'),
				'up_down'=>$this->input->post('up_down'),
				'up_down_remarks'=>$this->input->post('up_down_remarks'),
				'embossing_coding'=>$this->input->post('embossing_coding'),
				'embossing_coding_remarks'=>$this->input->post('embossing_coding_remarks'),
				'cooling_station'=>$this->input->post('cooling_station'),
				'cooling_station_remarks'=>$this->input->post('cooling_station_remarks'),
				'horizontal_sealer'=>$this->input->post('horizontal_sealer'),
				'horizontal_sealer_remarks'=>$this->input->post('horizontal_sealer_remarks'),
				'perforation_blade'=>$this->input->post('perforation_blade'),
				'perforation_blade_remarks'=>$this->input->post('perforation_blade_remarks'),
				'priston_drive'=>$this->input->post('priston_drive'),
				'priston_drive_remarks'=>$this->input->post('priston_drive_remarks'),
				
				//'individual_option'=>$this->input->post('individual_option'),
				//'rotary_option1'=>$this->input->post('rotary_option1'),
			
				'shutt_off_nozzle'=>$this->input->post('shutt_off_nozzle'),
				'shutt_off_nozzle_remarks'=>$this->input->post('shutt_off_nozzle_remarks'),
				'filling_plate_drive'=>$this->input->post('filling_plate_drive'),
				'filling_plate_drive_remarks'=>$this->input->post('filling_plate_drive_remarks'),
				'individual_weight'=>$this->input->post('individual_weight'),
				'individual_weight_remarks'=>$this->input->post('individual_weight_remarks'),
				'overall_weight_adjust'=>$this->input->post('overall_weight_adjust'),
				'overall_weight_adjust_remarks'=>$this->input->post('overall_weight_adjust_remarks'),
				'vertical_sealer_width'=>$this->input->post('vertical_sealer_width'),
				'horizontal_sealer_width'=>$this->input->post('horizontal_sealer_width'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine2', $data5);


			$data6 =array(
				
				'traverse_drive'=>$this->input->post('traverse_drive'),
				'yes_traverse_drive'=>$this->input->post('yes_traverse_drive'),
				'traverse_drive_remarks'=>$this->input->post('traverse_drive_remarks'),
				'printer_yes_no'=>$this->input->post('printer_yes_no'),
				'printer'=>$this->input->post('printer'),
				'inkjet_option'=>$this->input->post('inkjet_option'),
				'tto_option'=>$this->input->post('tto_option'),
				'thermal_inkjet_option'=>$this->input->post('thermal_inkjet_option'),
				'printer_remarks'=>$this->input->post('printer_remarks'),
				'case_packer_drive'=>$this->input->post('case_packer_drive'),
				'case_packer_drive_remarks'=>$this->input->post('case_packer_drive_remarks'),
				'nozzle_funnel'=>$this->input->post('nozzle_funnel'),
				'powder_option1'=>$this->input->post('powder_option1'),
				'liquid_option1'=>$this->input->post('liquid_option1'),
				'liquid_shut_option1'=>$this->input->post('liquid_shut_option1'),
				'nozzle_funnel_remarks'=>$this->input->post('nozzle_funnel_remarks'),
				'hose_pipe'=>$this->input->post('hose_pipe'),
				'hose_pipe_remarks'=>$this->input->post('hose_pipe_remarks'),
				'batch_cut_format'=>$this->input->post('batch_cut_format'),
				'string_option'=>$this->input->post('string_option'),
				'batch_cut_format_remarks'=>$this->input->post('batch_cut_format_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine3', $data6);

			$data7 =array(
				
				// 'horizontal_sealer1'=>$this->input->post('horizontal_sealer1'),
				'horizontal_sealer1_remarks'=>$this->input->post('horizontal_sealer1_remarks'),
				// 'vertical_sealer1'=>$this->input->post('vertical_sealer1'),
				'vertical_sealer1_remarks'=>$this->input->post('vertical_sealer1_remarks'),
				'rotary_valve_coating'=>$this->input->post('rotary_valve_coating'),
				'rotary_valve_coating_remarks'=>$this->input->post('rotary_valve_coating_remarks'),
				'working_speed'=>$this->input->post('working_speed'),
				'working_speed_remarks'=>$this->input->post('working_speed_remarks'),
				'reel_shaft_type'=>$this->input->post('reel_shaft_type'),
				'reel_shaft_type_remarks'=>$this->input->post('reel_shaft_type_remarks'),
				'reel_core_diameter'=>$this->input->post('reel_core_diameter'),
				'reel_core_diameter_remarks'=>$this->input->post('reel_core_diameter_remarks'),
				'trial_material'=>$this->input->post('trial_material'),
				'trial_material_remarks'=>$this->input->post('trial_material_remarks'),
				'laminate_detail'=>$this->input->post('laminate_detail'),
				'laminate_detail_remarks'=>$this->input->post('laminate_detail_remarks'),
				'heater_control_system'=>$this->input->post('heater_control_system'),
				'heater_control_system_remarks'=>$this->input->post('heater_control_system_remarks'),
				'beacon_light'=>$this->input->post('beacon_light'),
				'beacon_light_remarks'=>$this->input->post('beacon_light_remarks'),
				'hooper_level'=>$this->input->post('hooper_level'),
				'hooper_level_option'=>$this->input->post('hooper_level_option'),
				'hooper_level_remarks'=>$this->input->post('hooper_level_remarks'),
				'safety_relay'=>$this->input->post('safety_relay'),
				'safety_relay_remarks'=>$this->input->post('safety_relay_remarks'),
				'supply_voltage'=>$this->input->post('supply_voltage'),
				'supply_voltage_remarks'=>$this->input->post('supply_voltage_remarks'),
				'plc_maker'=>$this->input->post('plc_maker'),
				'plc_maker_remarks'=>$this->input->post('plc_maker_remarks'),
				'hmi_size'=>$this->input->post('hmi_size'),
				'hmi_size_remarks'=>$this->input->post('hmi_size_remarks'),
				'cip_system'=>$this->input->post('cip_system'),
				'cip_system_option'=>$this->input->post('cip_system_option'),
				'cip_system_option_remarks'=>$this->input->post('cip_system_option_remarks'),
				'tool_kit'=>$this->input->post('tool_kit'),
				'tool_kit_remarks'=>$this->input->post('tool_kit_remarks'),
				'changeover_part'=>$this->input->post('changeover_part'),
				'changeover_part_remarks'=>$this->input->post('changeover_part_remarks'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine4', $data7);

			  

			$data8 =array(
				
				'secondary_pack'=>$this->input->post('secondary_pack'),
				//'yes_secondary_pack'=>implode(', ', $yes_secondary_pack),
				'case_packer'=>$this->input->post('case_packer'),
				'secondary_pack_remarks'=>$this->input->post('secondary_pack_remarks'),
				'ladder_platform'=>$this->input->post('ladder_platform'),
				'ladder_platform_remarks'=>$this->input->post('ladder_platform_remarks'),
				'machine_guarding'=>$this->input->post('machine_guarding'),
				'aluminium_option'=>$this->input->post('aluminium_option'),
				'ss_304_option'=>$this->input->post('ss_304_option'),
				'machine_guarding_remarks'=>$this->input->post('machine_guarding_remarks'),
				//'trial_comments'=>$this->input->post('trial_comments'),
				'special_notes'=>$this->input->post('special_notes'),
				'added_on'=>date("Y-m-d h:i:s"),
				'added_by'=>$this->session->userdata['logged_in']['user_id']
			);

			$this->db->where('record_id', $record_id);
			$this->db->update('basic_machine_df_form_multi_track_machine5', $data8);


			$idd=	$this->input->post('multi_track_machine_id');

for($y=0; $y<count($idd); $y++){
	$miid =$idd[$y]; 
	$yes_secondary_packed = $this->input->post('yes_secondary_pack'.$miid);
	$qtyy = $this->input->post('yes_secondary_pack_qty'.$miid);

	$da =array(

		'yes_secondary_pack'=>$yes_secondary_packed,

		'yes_secondary_pack_qty'=>$qtyy,

	);

	$this->db->where('id',$miid);
	$this->db->update('basci_machine_df_form_multi_track_machine_pack', $da);
}
	 

// Add NEW SECONDARY PACK


			if($this->input->post('add_more_pack')==1){

			$yes_secondary_pack = $this->input->post('yes_secondary_pack_add');

			$qty = $this->input->post('yes_secondary_pack_qty_add');



			for($i = 0; $i<count($yes_secondary_pack); $i++){
			$pack =array(
			'record_id'=>$record_id,
			'yes_secondary_pack'=>$yes_secondary_pack[$i],
			'yes_secondary_pack_qty'=>$qty[$i],
			'added_on' => date("Y-m-d h:i:s"),
			'added_by' => $this->session->userdata['logged_in']['user_id']
			);


			$this->db->insert('basci_machine_df_form_multi_track_machine_pack', $pack);
			}


			}

		$user_id=$_SESSION['logged_in']['user_id'];
			$data34 = array('df_id'=>0,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);

			$this->db->where('po_id',$this->uri->segment(3));
			$this->db->where('taskid',86);

			// $this->db->where('id',$this->uri->segment(3));
			$this->db->update('task_department_wise_scheduling',$data34);

			/*Select PO No*/
			$pono = $this->uri->segment(3);
			/*Select PO No*/

			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$nextWorkingDate = $this->iftomorrowisholiday($tomorrow);
			$data123 = array('df_id'=>0,
				'taskid'=>87,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d',strtotime($nextWorkingDate)),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$pono,
				'task_status'=>0,
				'remarks'=>'', 
				'assigned_user'=>$user_id,
				'userid'=>$user_id,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));

				// echo "<pre>"; print_r($data); exit;

			$this->db->insert('task_department_wise_scheduling',$data123);


			$this->session->set_flashdata('success','<div class="alert alert-success"> Thank You! Data successfully added.</div>');
		redirect(page_url.'Formats/basic_machine_df_form/'.$po_id);
		}

	}


	//************************************************************************************ */


	//////////////////////// BASIC MACHINE DF FORM ////////////////////////////

	function getpono($recordid){
		$q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$recordid)->get();
		foreach($q->result() as $row);
		$pono = $row->po_id;
		return $pono;
	
	}
	
	function iftomorrowisholiday($date){
		while (true) {
			$q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date', $date)->get();
	
			if ($q->num_rows() > 0) {
				// If it's a holiday, move to the next day
				$date = date('Y-m-d', strtotime($date . ' +1 day'));
			} else {
				// Found a working day
				break;
			}
		}
		return $date;
	}


}
