<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesreporting extends CI_Controller { 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dashboard_model','dashboardmodel');
        if ( ! $this->session->userdata('logged_in'))
        { 
            $this->session->set_flashdata('message','Session Logged Out. Login to continue');
            redirect(page_url);
        }
		
	
	

	}
		
		public function piedata() {
            // $form_name = $this->input->post('form_name');
							
            $leave_data = array();

            $user_id =$this->session->userdata['logged_in']['user_id'];	
    
            $this->db->select('a.*,b.first_name, b.last_name, c.department, d.user_role')->from('leave_application a')->join('system_users b','a.employee_id=b.user_id','left')->join('departments c','b.department_id=c.department_id','left')->join('user_role d','b.user_role_id=d.user_role_id','left');
    
            $query = $this->db->order_by('added_on','desc')->get();
    
            $res = $query->result();
    
            $i=1;
    
            foreach($res as $row)
    
            {
              
                $data[] = array(
                    'department'		=>	strtoupper($row->department),
                    'total_days'			=>	strtoupper($row->total_days),
                    'employee_name'    => strtoupper($row->first_name." ".$row->last_name),
                    'color'			=>	'#' . rand(100000, 999999) . ''
                );
    
                
            }
    
    
        //echo json_encode($data);
    

		}

        public function piedataview()
        {
            $this->load->view('master/piechart');
        }

        public function monthwiseleads()
        {
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);
            //query to get data from the table
            $this->db->select('COUNT(id) as Count,MONTHNAME(added_on) as "Mon"')->from('leads')->where('YEAR(added_on)',date('Y'))->group_by('YEAR(added_on)')->group_by('MONTH(added_on)');
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();

            $output = array();
            foreach($query->result() as $row)
            {
            $output[] = array(
            'mon'  => $row->Mon,
            'count' => $row->Count
            );
            }

            //now print the data
            print json_encode($output);
        }
    }
    
     public function yearwiseleads()
        {
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);
            //query to get data from the table
            $this->db->select('COUNT(id) as Count,YEAR(added_on) as "Year"')->from('leads')->where('YEAR(added_on)',date('Y'))->group_by('YEAR(added_on)');
            if($stdate<>'')
            {
            $this->db->where('added_on >=', $stdate)->where('added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();

            //loop through the returned data
            $output = array();
            // foreach ($result as $row) {
            // $data[] = $row;
            // }
            foreach($query->result() as $row)
            {
            $output[] = array(
            'year'  => $row->Year,
            'count' => $row->Count
            );
            }

            //now print the data
            print json_encode($output);
        }
    }



public function industrywiseleads()
      {
        $data=array();

            $data[0][0] = 'Task';
            $data[0][1] = 'Total Lead';
        //query to get data from the table
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);

            
           $query=$this->db->select('patient_id,patient_type')->from('patient_type')->where('company_id',$_SESSION['logged_in']['business_location'])->where('status',1)->get();
            if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
         

                $this->db->select('a.id')->from('leads a')->where('a.patient_type_id',$row->patient_id);
                if($stdate<>'')
                {
                $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
                }

                $this->db->where('a.company_id',$_SESSION['logged_in']['business_location']);
                $res=$this->db->get();
                $leadcount=$res->num_rows();
                if($leadcount>0)
                {
                $data[$i][0] = $row->patient_type;
                $data[$i][1] = $leadcount;
                $i++;
                }
                            
           
        }
           
        }

        echo(json_encode($data));
      }


    public function industrywiseleadsOLdd()
        {

            $data=array();
            $data[0][0] = 'Task';
            $data[0][1] = 'Total Lead';
            //query to get data from the table
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);

            $this->db->select('COUNT(a.id) as Count,a.patient_type_id,b.patient_type')->from('leads a')->join('patient_type b','b.patient_id=a.patient_type_id')->group_by('patient_type_id');
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();
            $industry='';
            $i=1;
            foreach($query->result() as $row)
            {
                if($row->Count>0 && $row->Count<>'')
                {
                $data[$i][0] = $row->patient_type;
                $data[$i][1] = $row->Count;
                $i++;
                }

            
            }

          }

         echo (json_encode($data));
    }
       public function  toptenleads()
       {

        //query to get data from the table
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);

           $sql='';
             if($stdate<>'')
            {
            $sql="where a.added_on BETWEEN '".$stdate."' AND '".$enddate."'";
            }
            $output=array();
            $query = $this->db->query("SELECT a.id,a.customer_name,SUM(b.price) as total from leads a JOIN lead_products b ON a.id=b.lead_id GROUP BY a.id $sql ORDER BY total DESC limit 5");
            if($query->num_rows() >0){
            //execute query
            foreach($query->result() as $row)
            {
                
            $output[] = array(
            'indus'  => $row->customer_name,
            'count' => $row->total
            );
            }

            //now print the data
            print json_encode($output);
        }
       }



public function topenquririesdataOLd()
      {
            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4);
            $this->db->select('a.lead_id,a.product_id,b.instruments_name,b.id')->from('lead_products a')->join('presto_instruments b','a.product_id=b.id')->join('leads c','a.lead_id=c.id')->group_by('a.product_id');
            if($stdate<>'')
            {
            $this->db->where('c.added_on >=', $stdate)->where('c.added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            $industry='';
            $q='';
            foreach($query->result() as $row)
            {
                //$tatalamt=count($row->lead_id);
                $q=$this->db->select('a.lead_id')->from('lead_products a')->where('a.product_id',$row->product_id)->get();
                $tot=$q->num_rows();
                foreach($q->result() as $industry);
                
                //$tatalamt=array_sum($samt);

            $output[] = array(
            'indus'  => $row->instruments_name,
            'count' => $tot
            );
            }

            //now print the data
            print json_encode($output);
        }
      }


        public function topenquririesdata()
        {
       $data=array();
        $prddata=array();
        $data[0][0] = 'Product';
        $data[0][1] = 'Sale';

        $stdate=$this->uri->segment(3);
        $enddate=$this->uri->segment(4);
        $this->db->select('id')->from('leads');
        $this->db->where('company_id',$_SESSION['logged_in']['business_location']);
        if($stdate<>'')
        {
        $this->db->where('added_on >=', $stdate)->where('added_on <=',$enddate); 
        }
        $query=$this->db->get();
        if($query->num_rows()>0)
        {

            foreach($query->result() as $row)
            {
                $resteye=$this->db->select('product_id')->from('lead_products')->where('lead_id',$row->id)->get();
                if($resteye->num_rows()>0)
                {
                    foreach($resteye->result() as $rowsss)
                    {
                        $prddata[]=$rowsss->product_id;

                    }


                }

            }


        }


            if(count(array_count_values($prddata))>0)
            {
                $t=1;
                foreach (array_count_values($prddata) as $key => $value)
                {
                    $instrumentname=$this->getinstrumentname($key);
                    $data[$t][0] = $instrumentname;
                    $data[$t][1] = $value;
                    $t++;
                }

            }

        echo(json_encode($data));

        }

    public function pieleadsbyregiondata()
      {
        $mode=$this->getsettings();
        if(count($mode)>0)
        {
            $mode=$mode['mode'];
        }else
        {
             $mode=1;
        }

        $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
              $data=array();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';
        $stdate=$this->uri->segment(3)." 00:00:00";
        $enddate=$this->uri->segment(4)." 23:59:59";

        $company_id=$_SESSION['logged_in']['business_location'];

        if($conversion_lead_stage<>'')
        {

        if($mode==1)
        {
        if($stdate<>'')
        {
              $sql="AND a.added_on BETWEEN '".$stdate."' AND '".$enddate."'";
        }else
        {
            $sql='';
        }
        $this->db->select('a.state_id,a.state_name')->from('states_view a')->join('leads b','a.state_id=b.state')->where('b.company_id',$_SESSION['logged_in']['business_location'])->group_by('a.state_id');
        $query=$this->db->get();
            if($query->num_rows() >0)
            { 
                $i=1;    
                foreach ($query->result() as $row) {

                $sourceid=$row->state_id;

                $query1=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$conversion_lead_stage') AND b.state='$sourceid' $sql AND b.company_id='$company_id'");

                if($query1->num_rows() >0)
                {
                    $i=1;
                    $leadcount=$query1->num_rows();
                    if($leadcount>0)
                    {
                        $data[$i][0] = $row->state_name;
                        $data[$i][1] = $leadcount;
                    }
                }

                $i++;
                }
               
            }

        }else
        {


                    if($stdate<>'')
                    {
                    $sql="AND a.added_on>='".$stdate."' AND a.added_on<= '".$enddate."'";
                    }else
                    {
                    $sql='';
                    }
                    $this->db->select('a.id as state_id,a.zone as state_name')->from('saleszone a')->join('leads b','a.id=b.client_location')->where('a.company_id',$_SESSION['logged_in']['business_location'])->where('b.company_id',$_SESSION['logged_in']['business_location'])->group_by('a.id');
                    $query=$this->db->get();
                    if($query->num_rows() >0)
                    { 
                    $i=1;    
                    foreach ($query->result() as $row) {

                    $sourceid=$row->state_id;

                    $query1=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$conversion_lead_stage') AND b.client_location='$sourceid' $sql AND b.company_id='$company_id'");

                    if($query1->num_rows() >0)
                    {
                    $i=1;
                    $leadcount=$query1->num_rows();
                    if($leadcount>0)
                    {
                    $data[$i][0] = $row->state_name;
                    $data[$i][1] = $leadcount;
                    }
                    }

                    $i++;
                    }

                    }


        }

    }

             echo(json_encode($data));
      }
      public function pieleadsbyregionwiseenquerydata()
      {

            $mode=$this->getsettings();
            if(count($mode)>0)
            {
                $mode=$mode['mode'];
            }else
            {
                $mode=1;
            }

            $data=array();
            $data[0][0] = 'Task';
            $data[0][1] = 'Total Lead';
        //query to get data from the table
            $stdate=str_replace('%20',' ',$this->uri->segment(3));
            $enddate=str_replace('%20',' ',$this->uri->segment(4));

            if($mode==1)
            {
                $this->db->select('a.state_id,a.state_name,b.id')->from('states a')->join('leads b','a.state_id=b.state')->where('state_status',1)->group_by('b.state');

                $query=$this->db->get();
                if($query->num_rows() >0)
                { 
                $i=1;    
                foreach ($query->result() as $row) {
                $this->db->select('a.id')->from('leads a');
                 $this->db->where('a.company_id',$_SESSION['logged_in']['business_location']);
                if($stdate<>'')
                {
                $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
                }
                $this->db->where('a.state',$row->state_id);




                $q=$this->db->get();
                $leadcount=$q->num_rows();

                if($leadcount>0)
                {
                $data[$i][0] = $row->state_name;
                $data[$i][1] = $leadcount;
                $i++;
                }

                }

                }

            }else
            {



                $this->db->select('a.id as state_id,a.zone as state_name,b.id')->from('saleszone a')->join('leads b','a.id=b.client_location')->where('b.company_id',$_SESSION['logged_in']['business_location'])->group_by('b.client_location');

                $query=$this->db->get();
                if($query->num_rows() >0)
                { 
                $i=1;    
                foreach ($query->result() as $row) {
                $this->db->select('a.id')->from('leads a');
                if($stdate<>'')
                {
                $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
                }
                $this->db->where('a.client_location',$row->state_id);




                $q=$this->db->get();
                $leadcount=$q->num_rows();

                if($leadcount>0)
                {
                $data[$i][0] = $row->state_name;
                $data[$i][1] = $leadcount;
                $i++;
                }

                }

                }


            }

        echo(json_encode($data));
      }

      public function pieleadspersonwisedata()
      {
        $unqualified_lead_stage = $this->dashboardmodel->getUnqualifiedLeadStage();
        $data=array();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';

        $stdate=$this->uri->segment(3);
        $enddate=$this->uri->segment(4);

        if($unqualified_lead_stage<>'')
        {
        $query=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('business_location',$_SESSION['logged_in']['business_location'])->where('user_status',1)->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.added_by',$row->user_id)->where('b.lead_status', $unqualified_lead_stage)->where('a.company_id',$_SESSION['logged_in']['business_location']);
             if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $q=$this->db->get();
            $leadcount=$q->num_rows();
            $name=$row->first_name.' '.$row->last_name;
            if($leadcount>0)
            {
                $data[$i][0] = $name;
                $data[$i][1] = $leadcount;

                $i++;
            }

                     
             
            
        }
          
        }

    }

     echo(json_encode($data));
      }

      public function pieleadsunqulifiedbyregiondata()
      {
        $mode=$this->getsettings();
        if(count($mode)>0)
        {
            $mode=$mode['mode'];
        }else
        {
            $mode=1;
        }

        $data=array();
        $unqualified_lead_stage = $this->dashboardmodel->getUnqualifiedLeadStage();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';

        $stdate=$this->uri->segment(3)." 00:00:00";
        $enddate=$this->uri->segment(4)." 23:59:59";

        if($unqualified_lead_stage<>'')
        {
        if($mode==1)
        {
        $query=$this->db->select('state_id,state_name')->from('states')->where('state_status',1)->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.state',$row->state_id)->where('b.lead_status', $unqualified_lead_stage)->where('a.company_id',$_SESSION['logged_in']['business_location']);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $q=$this->db->get();

            $leadcount=$q->num_rows();
            if($leadcount>0)
            {
            $stname=$row->state_name;
                $data[$i][0] = $stname;
                $data[$i][1] = $leadcount; 
                $i++; 
            }                  
             
            
        }
          
        }

    }else
    {

             $query=$this->db->select('id as state_id,zone as state_name')->from('saleszone')->where('company_id',$_SESSION['logged_in']['business_location'])->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.client_location',$row->state_id)->where('b.lead_status', $unqualified_lead_stage)->where('a.company_id',$_SESSION['logged_in']['business_location']);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $q=$this->db->get();

            $leadcount=$q->num_rows();
            if($leadcount>0)
            {
            $stname=$row->state_name;
                $data[$i][0] = $stname;
                $data[$i][1] = $leadcount; 
                $i++; 
            }                  
             
            
        }
          
        }




    }

}
         echo(json_encode($data));
      }

       public function pieleadsunqulifiedbysourcedata()
      {

         

        $data=array();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';
        $unqualified_lead_stage = $this->dashboardmodel->getUnqualifiedLeadStage();
        $stdate=$this->uri->segment(3)." 00:00:00";
        $enddate=$this->uri->segment(4)." 23:59:59";


if($unqualified_lead_stage<>'')
{
    
        $this->db->select('source_id,lead_source')->from('lead_source')->where('status',1);
       
        $query=$this->db->get();
        if($query->num_rows() >0)
            {              
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('a.lead_source_id',$row->source_id)->where('b.lead_status',$unqualified_lead_stage);
            if($stdate<>'')
            {
            $this->db->where('b.added_on >=', $stdate)->where('b.added_on <=',$enddate); 
            }
            $q=$this->db->get();
            $leadcount=$q->num_rows();
            if($leadcount>0)
            {
            $leadname=$row->lead_source;
                $data[$i][0] = $leadname;
                $data[$i][1] = $leadcount;
           
            $i++;
             }
                
             
           
        }
           
        }
}
        echo(json_encode($data));
      }


       public function pieleadshighsellingdata()
      {

        $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
        $data=array();
        $prddata=array();
        $data[0][0] = 'Product';
        $data[0][1] = 'Sale';

        $stdate=str_replace('%20',' ',$this->uri->segment(3));
        $enddate=str_replace('%20',' ',$this->uri->segment(4));


    if($conversion_lead_stage<>'')
    {

        

        $query=$this->db->query("SELECT a.lead_id FROM progress_remarks a WHERE a.added_on IN (SELECT MIN(added_on) FROM progress_remarks WHERE lead_status='$conversion_lead_stage' GROUP BY a.lead_id) AND a.added_on>='$stdate' AND a.added_on<='$enddate'");
        if($query->num_rows()>0)
        {

            foreach($query->result() as $row)
            {
                $resteye=$this->db->select('product_id')->from('lead_products')->where('lead_id',$row->lead_id)->get();
                if($resteye->num_rows()>0)
                {
                    foreach($resteye->result() as $rowsss)
                    {
                        $prddata[]=$rowsss->product_id;

                    }


                }

            }


        }

    }


   if(count(array_count_values($prddata))>0)
            {
                $prddata1=array_count_values($prddata);
                arsort($prddata1);

               
                $t=1;
                foreach ($prddata1 as $key => $value)
                {
                    if($t<9)
                    {
                    $instrumentname=$this->getinstrumentname($key);
                    $data[$t][0] = $instrumentname;
                    $data[$t][1] = $value;
                    $t++;
                    }
               
                }

            }

        echo(json_encode($data));


    
      }


      public function pieleadquotationtoconversiondata()
      {

        $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();

            $stdate=$this->uri->segment(3);
            $enddate=$this->uri->segment(4); 

            $month=$this->getMonthsInRange($stdate,$enddate);


            $data=array();
            
            $data[0][0] = 'Quotation';
            $data[0][1] = 'Total Lead';

                    $k=0;
            $close=array();
            $close[]=0;
            if($conversion_lead_stage<>'')
            {
            for($i=0;$i<count($month);$i++)
            {

                $monthstartdate=date('Y-'.$month[$i]['month'].'-01')." 00:00:00";
                $monthenddate=date('Y-'.$month[$i]['month'].'-t')." 23:59:59";


                // $this->db->select('id')->from('progress_remarks')->where('lead_status',$conversion_lead_stage);
                // if($stdate<>'')
                // {
                // $this->db->where('added_on >=', $monthstartdate)->where('added_on <=',$monthenddate); 
                // }

                // $query=$this->db->get();

                // if($query->num_rows()>0)
                // {
                    
                // $monthNum = sprintf("%02s", $month[$i]['month']);
                // $monthName = date("F", mktime(null, null, null, $monthNum));
                // if($query->num_rows()>0)
                // {
                
                // $k++;
                // }

                $newstdate=date('Y-m-01',strtotime($monthstartdate))." 00:00:00";
                $newenddate=date('Y-m-t',strtotime($monthenddate))." 23:59:59";

                $query=$this->db->query("SELECT a.lead_id FROM progress_remarks a WHERE a.added_on IN (SELECT MIN(added_on) FROM progress_remarks WHERE lead_status=$conversion_lead_stage GROUP BY lead_id) AND a.added_on>='$newstdate' AND a.added_on<='$newenddate'");

               
                $close[]=$query->num_rows();    

            }

            
            // echo str_replace('%20',' ', $this->uri->segment(3)); exit;

            $data[$k+1][0] = date('d-M-Y',strtotime(str_replace('%20',' ', $this->uri->segment(3))))." to ".date('d-M-Y',strtotime(str_replace('%20',' ', $this->uri->segment(4))));
            $data[$k+1][1] = array_sum($close);
        }else
        {
            $data[0]="NO DATA";
        }
            echo(json_encode($data));
        

      }



      public function pieleadsourcewiseleaddata()
      {
        $data=array();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';
        $stdate=$this->uri->segment(3)." 00:00:00";
        $enddate=$this->uri->segment(4)." 23:59:59";

        $this->db->select('source_id,lead_source')->from('lead_source')->where('status',1);

        $query=$this->db->get();
        if($query->num_rows() >0)
            { 
            $i=1;    
            foreach ($query->result() as $row) {
            $this->db->select('id')
            ->from('leads')
            ->where('lead_source_id',$row->source_id);
            if($stdate<>'')
            {
            $this->db->where('added_on >=', $stdate)->where('added_on <=',$enddate); 
            }
            $q=$this->db->get();
        $leadcount=$q->num_rows();
            $leadcount=$q->num_rows();
            $instname=$row->lead_source;
            if($leadcount>0)
            {
                $data[$i][0] = $instname;
                $data[$i][1] = $leadcount;
               // $data[$i][2] = $lead_source;

                     
             
            $i++;
        }
        }
           
        }

        echo(json_encode($data));
      }

      
      public function pieleadsourcewiseleadconversiondata()
      {
        $company_id=$_SESSION['logged_in']['business_location'];
        $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();

        $data=array();
        $data[0][0] = 'Task';
        $data[0][1] = 'Total Lead';
        $stdate=$this->uri->segment(3)." 00:00:00";
        $enddate=$this->uri->segment(4)." 22:59:59";

        if($stdate<>'')
        {
              $sql="AND a.added_on>= '".$stdate."' AND a.added_on<='".$enddate."'";
        }else
        {
            $sql='';
        }

        if($conversion_lead_stage<>'')
        {
        $this->db->select('source_id,lead_source')->from('lead_source')->where('company_id',$_SESSION['logged_in']['business_location'])->where('status',1);
        $query=$this->db->get();
        if($query->num_rows() >0)
            { 
                $i=1;    
                foreach ($query->result() as $row) {

                $sourceid=$row->source_id;
                $query1=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('$conversion_lead_stage') AND b.lead_source_id='$sourceid' $sql AND b.company_id='$company_id'");

                if($query1->num_rows() >0)
                {
                    $i=1;
                    $leadcount=$query1->num_rows();
                    if($leadcount>0)
                    {
                    $data[$i][0] = $row->lead_source;
                    $data[$i][1] = $leadcount;
                    }
                }

                $i++;
                }
      }
  }
                echo(json_encode($data));


    }


      public function barquotationtoconversion()
      {
       //query to get data from the table
        $dt = strtotime(date('Y-m-01'));
        $output = array();
        $n = 3;
        for ($j = $n; $j >= 0; $j--) {
           $monthdata= date("F Y ", strtotime(" -$j month", $dt));
            $stdate=date("Y-m-01", strtotime(" -$j month", $dt));
            $enddate=date("Y-m-t", strtotime(" -$j month", $dt));
            // echo $monthdata .'<br>'. $stdate .'<br>'. $enddate; exit;

            $query = $this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('b.lead_status',8)->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate)->get();
            //execute query
            $result = $query->num_rows();
            
            
            $q=$this->db->select('a.id')->from('leads a')->join('progress_remarks b','a.id=b.lead_id')->where('b.lead_status',6)->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate)->get();
            $tot=$q->num_rows();
            $output[] = array(
            'months'  => $monthdata,
            'indus'  => $result,
            'count' => $tot
            );
                      
            
        }
        //now print the data 
        print json_encode($output); 
      }

      public function barquotationtoconversionamt()
      {
       //query to get data from the table
        $dt = strtotime(date('Y-m-01'));
        $output = array();
        $n = 3;
        for ($j = $n; $j >= 0; $j--) {
           $monthdata= date("F Y ", strtotime(" -$j month", $dt));
            $stdate=date("Y-m-01", strtotime(" -$j month", $dt));
            $enddate=date("Y-m-t", strtotime(" -$j month", $dt));
            //echo $stdate; echo $enddate; exit;

            $sql="AND a.added_on BETWEEN '".$stdate."' AND '".$enddate."'";

            
            $quotationamt=array();
            $quotationamt[]=0;
             $query = $this->db->query("SELECT a.id,a.customer_name,SUM(b.price) as total from leads a JOIN lead_products b ON a.id=b.lead_id JOIN progress_remarks c ON a.id=c.lead_id WHERE c.lead_status IN ('8') $sql GROUP BY a.id");
             if($query->num_rows() >0)
             {
             foreach($query->result() as $row)
             {
                $quotationamt[]=$row->total;
             }
            }
             $quotationtotal=array_sum($quotationamt);
             $quotationamt=array();

             $conversionamt=array();
            $conversionamt[]=0;
             $query1 = $this->db->query("SELECT a.id,a.customer_name,SUM(b.price) as total1 from leads a JOIN lead_products b ON a.id=b.lead_id JOIN progress_remarks c ON a.id=c.lead_id WHERE c.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND c.lead_status IN ('6') $sql GROUP BY a.id");
             if($query1->num_rows() >0)
             {
             foreach($query1->result() as $row1)
             {
                $conversionamt[]=$row1->total1;
             }
            }
             $conversiontotal=array_sum($conversionamt);
         
            $output[] = array(
            'months'  => $monthdata,
            'indus'  => $quotationtotal,
            'count' => $conversiontotal
            );
                      
            
        }
        //now print the data 
        print json_encode($output); 
      }

    public function filterdata()
    {
        $this->load->view('master/piechartfilterdata');
    }
    public function piechartfilterdataform()
    {
        $chartid=$this->input->post('chartname');
        $startdate=$this->input->post('startdate');
        $enddate=$this->input->post('enddate');
        $stdate=date('Y-m-d',strtotime($startdate));
        $endate=date('Y-m-d',strtotime($enddate));


        redirect(page_url.'Salesreporting/reports/'.$chartid.'/'.$stdate.'/'.$endate);
    }
    public function comparisionfilterdata()
    {
        $this->load->view('master/comparisionfilterdata');
    }
    public function comparfilterdataform()
    {
        $chartname=$this->input->post('chartname');
        $username=$this->input->post('username');
        //$sd=implode(',', $username);
        $sd = "'" . implode ( "', '", $username ) . "'";
        //echo "<pre>"; print_r($username); exit;
        //echo $sd; exit;
        $user=base64_encode($sd);
        $startdate=$this->input->post('startdate');
        $enddate=$this->input->post('enddate');
        $stdate=date('Y-m-d',strtotime($startdate));
        $endate=date('Y-m-d',strtotime($enddate));


            (page_url.'Salesreporting/comparisionfilterdata/'.$chartname.'/'.$user.'/'.$stdate.'/'.$endate);
    }

    public function comparehandlelead()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
               // echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            $this->db->select('b.user_id,b.first_name,b.last_name')->from('system_users b')->where_in('b.user_id',$users,false);
            
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
                $this->db->select('COUNT(a.lead_id) as asd')->from('lead_assigned_to_team_member a')->where('a.member_id',$row->user_id);
                if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
           $q= $this->db->get();
                foreach($q->result() as $row1);
                $name=$row->first_name.' '.$row->last_name;
            $output[] = array(
            'indus'  => $name,
            'count' => $row1->asd,
            'labels'=>$row1->asd
            );
            }

            //now print the data
            print json_encode($output);
        }
      }
      public function comparisionteamfilterdata()
    {
        $this->load->view('master/comparisionteamfilterdata');
    }
     public function compareteamfilterdataform()
    {
        $chartname=$this->input->post('chartname');
        $username=$this->input->post('username');
        //$sd=implode(',', $username);
        $sd = "'" . implode ( "', '", $username ) . "'";
        //echo "<pre>"; print_r($username); exit;
        //echo $sd; exit;
        $user=base64_encode($sd);
        $startdate=$this->input->post('startdate');
        $enddate=$this->input->post('enddate');
        $stdate=date('Y-m-d',strtotime($startdate));
        $endate=date('Y-m-d',strtotime($enddate));


        redirect(page_url.'Salesreporting/comparisionteamfilterdata/'.$chartname.'/'.$user.'/'.$stdate.'/'.$endate);
    }

    public function compareteamhandlelead()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
                //echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            $this->db->select('a.id,b.team_id,b.team_name')->from('leads a')->join('lead_assigned_to_team c','a.id=c.lead_id')->join('prestogroup_teams b','c.lead_id=b.team_id')->where_in('c.team_id',$users,false);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
                $q=$this->db->select('COUNT(a.team_id) as asd')->from('lead_assigned_to_team a')->where('a.team_id',$row->team_id)->get();
                foreach($q->result() as $row1);
                $name=$row->team_name;
            $output[] = array(
            'indus'  => $name,
            'count' => $row1->asd,
            'labels' => $row1->asd
            );
            }

            //now print the data
            print json_encode($output);
        }
      }

      public function conversionbymember()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
               // echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            //$this->db->select('a.id,b.user_id,b.first_name,b.last_name')->from('leads a')->join('lead_assigned_to_team_member c','a.id=c.lead_id')->join('system_users b','c.member_id=b.user_id')->where_in('c.member_id',$users,false);
             $this->db->select('b.user_id,b.first_name,b.last_name')->from('system_users b')->where_in('b.user_id',$users,false);
            
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
                $this->db->select('COUNT(a.lead_id) as asd')->from('lead_assigned_to_team_member a')->join('progress_remarks b','a.lead_id=b.lead_id')->where('a.member_id',$row->user_id)->where('b.lead_status',6);
                //$this->db->select('COUNT(a.lead_id) as asd')->from('progress_remarks a')->where('a.lead_id',$row->id)->where('a.lead_status',6);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
                $q=$this->db->get();
                foreach($q->result() as $row1);
                $name=$row->first_name.' '.$row->last_name;
            $output[] = array(
            'indus'  => $name,
            'count' => $row1->asd,
            'labels'=>$row1->asd
            );
            }

            //now print the data
            print json_encode($output);
        }
      }

      public function conversionbyteam()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
                //echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            $this->db->select('a.id,b.team_id,b.team_name')->from('leads a')->join('lead_assigned_to_team c','a.id=c.lead_id')->join('prestogroup_teams b','c.lead_id=b.team_id')->where_in('c.team_id',$users,false);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
                $q=$this->db->select('COUNT(a.lead_id) as asd')->from('progress_remarks a')->where('a.lead_id',$row->id)->where('a.lead_status',5)->get();
                foreach($q->result() as $row1);
                $name=$row->team_name;
            $output[] = array(
            'indus'  => $name,
            'count' => $row1->asd
            );
            }

            //now print the data
            print json_encode($output);
        }
      }
      public function discountgivenbyteam()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
                //echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            $this->db->select('a.id,b.team_id,b.team_name')->from('leads a')->join('lead_assigned_to_team c','a.id=c.lead_id')->join('prestogroup_teams b','c.lead_id=b.team_id')->where_in('c.team_id',$users,false);
            if($stdate<>'')
            {
            $this->db->where('a.added_on >=', $stdate)->where('a.added_on <=',$enddate); 
            }
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
                $income=array();
                 $income[]=0;

                //$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('6')");
                 $sql = $this->db->query("SELECT * FROM progress_remarks WHERE lead_id = $row->id AND lead_status IN (8,14) AND id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id)");
                if($sql->num_rows() >0){
                foreach($sql->result() as $row1);
                $lead_id=$row1->id;

                $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
                if($lid->num_rows()>0)
                    {
                foreach($lid->result() as $incomes)
                        {
                            
                            $price=$incomes->price;
                            
                            if($incomes->discount_type==0)
                            {
                                /** % **/
                                
                              $discount=  ($price*$incomes->percent_amt)/100;
                                
                            }else if($incomes->discount_type==1)
                            {
                                /** fix **/
                                
                                $discount= $incomes->percent_amt;
                                
                            }else
                            {
                                $discount=0;
                            }
                            
                            
                            $income[]=$price-$discount;
                            
                            
                        }
                    }
                }
                $name=$row->team_name;
            $output[] = array(
            'indus'  => $name,
            'count' => $income
            );
            }

            //now print the data
            print json_encode($output);
        }
      }

      public function discountbymember()
      {
            $user=$this->uri->segment(3);
           // $users=base64_decode($user);
            $users=base64_decode($user);
               // echo $users; exit;
            $stdate=$this->uri->segment(4);
            $enddate=$this->uri->segment(5);
        //query to get data from the table
            //$this->db->select('a.id,b.user_id,b.first_name,b.last_name')->from('leads a')->join('lead_assigned_to_team_member c','a.id=c.lead_id')->join('system_users b','c.member_id=b.user_id')->where_in('c.member_id',$users,false);
            $this->db->select('b.user_id,b.first_name,b.last_name')->from('system_users b')->where_in('b.user_id',$users,false);
           
            $query = $this->db->get();
            if($query->num_rows() >0){
            //execute query
            $result = $query->result();
            $output = array();

            foreach($query->result() as $row)
            {
              $income=array();
                 $income[]=0;

                //$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('6')");
                 //$sql = $this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id WHERE a.id=$row->id AND a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status IN ('8,14')");
                    if($stdate<>'')
                    {
                    $sdf="AND added_on BETWEEN '".$stdate. "' and '". $enddate."' GROUP BY id";
                    }
                 $sql= $this->db->query("SELECT * FROM progress_remarks WHERE lead_status IN (8,14) AND id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) $sdf");
                if($sql->num_rows() >0){
                foreach($sql->result() as $row1);
                $lead_id=$row1->lead_id;

                $lid=$this->db->select('qty,price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$lead_id)->get();
                if($lid->num_rows()>0)
                    {
                foreach($lid->result() as $incomes)
                        {
                            
                            $price=$incomes->price;
                            
                            if($incomes->discount_type==0)
                            {
                                /** % **/
                                
                              $discount=  ($price*$incomes->percent_amt)/100;
                                
                            }else if($incomes->discount_type==1)
                            {
                                /** fix **/
                                
                                $discount= $incomes->percent_amt;
                                
                            }else
                            {
                                $discount=0;
                            }
                            
                            
                            $income[]=$discount;
                            
                            
                        }
                    }
                }
                $name=$row->first_name.' '.$row->last_name;
            $output[] = array(
            'indus'  => $name,
            'count' => array_sum($income)
            );
            }

            //now print the data
            print json_encode($output);
        }
      }


      function reports()
      {
        $this->load->view('master/graphicalreports');
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

        function getcost($leadid)
        {
            $finalprice=array();
            $finalprice[]=0;
            $reste=$this->db->select('price,discount_type,percent_amt')->from('lead_products')->where('lead_id',$leadid)->get();
            if($reste->num_rows()>0)
            {

                foreach($reste->result() as $incomes)
                {
                     if($incomes->discount_type==0)
                            {
                                /** % **/
                                
                              $discount=  ($incomes->price*$incomes->percent_amt)/100;
                                
                            }else if($incomes->discount_type==1)
                            {
                                /** fix **/
                                
                                $discount= $incomes->percent_amt;
                                
                            }else
                            {
                                $discount=0;
                            }

                            $finalprice[]=$incomes->price-$discount;

                }
            }

                $fprice=array_sum($finalprice);

                return $fprice;


        }

        function sumallarrayvalues($arr)
        {

            $acc = array_shift($arr);
            foreach ($arr as $val) {
            foreach ($val as $key => $val) {
            $acc[$key] += $val;
            }
                }

            return $acc;
        }


         public function highsellingproduct()
      {

        $conversion_lead_stage = $this->dashboardmodel->getConversionLeadStage();
        $data=array();
        $prddata=array();
        $data[0][0] = 'Product';
        $data[0][1] = 'Sale';

      
        
        $this->db->select('a.lead_id')->from('progress_remarks a')->join('leads b','a.lead_id=b.id')->where('b.company_id',$_SESSION['logged_in']['business_location'])->where('a.lead_status',$conversion_lead_stage);
       
        $query=$this->db->get();
        if($query->num_rows()>0)
        {

            foreach($query->result() as $row)
            {
                $resteye=$this->db->select('product_id')->from('lead_products')->where('lead_id',$row->lead_id)->get();
                if($resteye->num_rows()>0)
                {
                    foreach($resteye->result() as $rowsss)
                    {
                        $prddata[]=$rowsss->product_id;

                    }


                }

            }


        }

//echo "<pre>"; print_r(array_count_values($prddata)); exit;

            if(count(array_count_values($prddata))>0)
            {
                $t=1;
                foreach (array_count_values($prddata) as $key => $value)
                {
                    $instrumentname=$this->getinstrumentname($key);
                    $data[$t][0] = $instrumentname;
                    $data[$t][1] = $value;
                    $t++;
                }

            }

        echo(json_encode($data));
    
      }


function getsettings()

{

$setarray=array();

$sql = $this->db->select('mode,port,password,email,outgoing,pass,cc_email,user_name,password,quotation_name,pi_name')

->where('id',$_SESSION['logged_in']['business_location'])
->from('setting_master')

->get();



if($sql->num_rows() > 0) {

foreach ($sql->result() as $row);

$setarray['mode'] = $row->mode;

$setarray['port'] = $row->port;

$setarray['password'] = $row->password;

$setarray['email_smtp'] = $row->email;

$setarray['email_outgoing']=$row->outgoing;

$setarray['email_pass']=$row->pass;

$setarray['email_ccmailid']=$row->cc_email;

$setarray['whatsappuser']=$row->user_name;

$setarray['whatsapppassword']=$row->password;

$setarray['quotefile']=$row->quotation_name;

$setarray['pifile']=$row->pi_name;

}



return $setarray;





}

function funnel()
{
    $this->load->view('master/graph/salesfunnel');
}

function stage_wise_leads()
{
    $this->load->view('leads/stage_wise_leads');
}

function salessourcewiselead()
{
    $lr=4;
    $company_id=$_SESSION['logged_in']['business_location'];
    $newallleads="'0'";
    $alltotallead=array();
    $alltotallead[]=0;
    $stdate=date('Y-m-d',strtotime($this->uri->segment('3')));
    $enddate=date('Y-m-d',strtotime($this->uri->segment('4')));
    if($stdate<>'')
    {
        $sql="AND added_on>= '".$stdate." 00:00:00' AND added_on<='".$enddate." 23:59:59'";
    }else
    {
        $sql='';
    }
    $html='';
    $html.='<table><tr>';
    $html.='<td style="font-weight:bold;">SOURCE</td>';
    $sel=$this->db->select('lead_id,lead_name,status')->from('lead_stage')->where('status',1)->where('company_id',$_SESSION['logged_in']['business_location'])->order_by('sort_order','ASC')->get();
    if($sel->num_rows() >0)
    {
        $lr=($sel->num_rows())-1;
        foreach($sel->result() as $lead_stage)
        {
             $html.='<th style="background-color:#F5F5F5;font-weight:bold;" class="display'.$lead_stage->lead_id.'">'.ucwords($lead_stage->lead_name).'<br><!--<input type="checkbox" name="hidde" class="display'.$lead_stage->lead_id.'" onchange="checktdhide('.$lead_stage->lead_id.')">--></th>';
        }
    }
    $html.='<td style="background-color:#F5F5F5;font-weight:bold;" >TOTAL</td>';
    $html.='</tr>';
    //$html.='<tr>';
    $rel=$this->db->select('source_id,lead_source,status')->from('lead_source')->where('company_id',$_SESSION['logged_in']['business_location'])->where('status',1)->get();
    if($rel->num_rows() >0)
    {
        foreach($rel->result() as $lead_source)
        {
            $alllead=array();
            $res=$this->db->query("SELECT id FROM leads WHERE lead_source_id=$lead_source->source_id $sql AND company_id='$company_id'");
            
            if($res->num_rows())
            {
                foreach($res->result() as $leadid)
                {
                    $alllead[]=$leadid->id;
                }
            }

            $newallleads="'" . implode ( "', '", $alllead ) . "'";
             $html.='<tr><td style="background-color:#eee">'.strtoupper($lead_source->lead_source).'</td>';
            
             $totallead=array();
             $totallead[]=0;
            foreach($sel->result() as $lead_stage)
            {
              
                $r=$this->db->query("SELECT b.id FROM progress_remarks a JOIN leads b ON a.lead_id=b.id  WHERE a.id IN (SELECT MAX(id) FROM progress_remarks GROUP BY lead_id) AND a.lead_status=$lead_stage->lead_id AND b.id IN ($newallleads) AND b.company_id='$company_id' GROUP BY b.id");
                $count=$r->num_rows();
              $html.='<td class="display'.$lead_stage->lead_id.'">'.$count.'</td>'; 
              $totallead[]=$count;               
            }
            $tleads=array_sum($totallead);
            $alltotallead[]=$tleads;
            $html.='<td>'.$tleads.'</td>';
             $html.='</tr>';

        }
    
    $finalleads=array_sum($alltotallead);
   
    $html.='<tr><td style="background-color:#eee" ></td><td colspan='.$lr.' style="background-color:#eee" ></td><td  style="background-color:#eee;font-weight:bold;">GRAND TOTAL</td><td style="background-color:#F5F5F5;font-weight:bold;">'.$finalleads.'</td></tr>';
    
    $html.='</table>';

}else
{

$html="<p style='font-size:15px;font-weight:bold;text-align:center;padding-top:150px;'>No Data Available</p>";
   
}

 echo $html; 

}

    function turn_around_time()
    {
        $this->load->view('leads/turn_around_time');
    }
    function turn_around_time_form()
    {
       $stdate=$this->input->post('startdate');
        $enddate=$this->input->post('enddate');

        $sdate=date('Y-m-d',strtotime($stdate));
        $edate=date('Y-m-d',strtotime($enddate)); 

        redirect(page_url.'Salesreporting/turn_around_time/'.$sdate.'/'.$edate);
    }
    function turnaroundtime()
    {

        $company_id=$_SESSION['logged_in']['business_location'];
        $turn_data = array();
        $j=1;
        //$stdate=$this->input->post('startdate');
        //$enddate=$this->input->post('enddate');

        $sdate=$this->uri->segment(3)." 00:00:00";
        $edate=$this->uri->segment(4)." 23:59:59";
        if($sdate)
        {
        $allmonth=array();
        //$result=array();
        $output = [];
        $time   = strtotime($sdate);
        $last   = date('M-Y', strtotime($edate));

        do {
        $month = date('M-Y', $time);
        $total = date('t', $time);
        $fistmonth[]= date('Y-m-01',$time);
        $smonth[]= date('Y-m-t',$time);
        $output[] = $month;

        $time = strtotime('+1 month', $time);
        } while ($month != $last);

      

        for($i=0;$i<count($output);$i++)
        {
            //echo $output[$i]; 
        $leadsupdate=array();
        $leadsupdate[]=0;
        $totalleadupadte=0;
            if($sdate<>'')
            {
            //$sql="WHERE create_date BETWEEN '".$sdate."' AND '".$edate."'";
            $sql="WHERE added_on >= '".$fistmonth[$i]." 00:00:00' AND  added_on<='".$smonth[$i]." 23:59:59'";
            }else
            {
            $sql='';
            }
            $res=$this->db->query("SELECT id,added_on FROM leads $sql");
            $totallead=$res->num_rows();
            if($res->num_rows() >0)
            {
                foreach($res->result() as $leadid)
                {

                    $Q=$this->db->query("SELECT id,added_on FROM progress_remarks WHERE lead_id=$leadid->id LIMIT 1,1");
                    if($Q->num_rows()>0)
                    {
                        foreach($Q->result() as $progress);
                        $leadadd=$leadid->added_on;
                        
                        if($progress->added_on=='0000-00-00 00:00:00')
                        {
                        $activityon=$leadid->added_on;
                        }else
                        {
                        $activityon=$progress->added_on;
                        }

                       
                        $seconds =  strtotime($activityon)-strtotime($leadadd);
                        $hourdiff = $seconds / 60 / 60;
                      
                        $leadsupdate[]=round($hourdiff);
                       

                    }

                }
               
            }


          
            $totalleadupadte=array_sum($leadsupdate);
            if($totallead!=0){
            $average=$totalleadupadte/$totallead;

            $avg=round($average,2);
             }else{
               $avg=0; 
             }
           // echo $totalleadupadte; exit;
            $turn_data[] = array('sr_no'=>$j,
            'months'=>$output[$i],
            'totalleads'=>$totallead,
            'activeleads'=>$totalleadupadte,
            'average'=>$avg);
            $j++;

            }
         }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($turn_data),
            "iTotalDisplayRecords" => count($turn_data),
            "aaData"=>$turn_data);
        echo json_encode($results);
}


function sales_agent_report()
{
    $this->load->view('sales_reporting/sales_performance');

}

function filter_salesperformance()
    {
        $from_date=date('Y-m-d',strtotime($this->input->post('from_date')));;
        $to_date=date('Y-m-d',strtotime($this->input->post('todate')));
       
        $user=$this->input->post('user');
        redirect(page_url.'Salesreporting/sales_agent_report/'.$from_date.'/'.$to_date.'/'.$user);
    }

}