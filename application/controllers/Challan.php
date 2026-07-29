<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Challan extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
			$session = $this->session->userdata('logged_in');
		if($session == FALSE)
		{
		
		redirect(page_url);
		
		}
		$user_id =$this->session->userdata['logged_in']['user_id'];
	if(empty($user_id))
         {
         redirect(site_url(),'refresh');
         }
		$this->load->model('User_model','user');
		$this->load->model('Master_model','master');
		$config = array();  
		$config['protocol'] = 'smtp';  
		$config['smtp_host'] = 'localhost';  
		$config['smtp_user'] = 'donotreply@packingtest.com';  
		$config['smtp_pass'] = 'Presto@#21';  
		$config['smtp_port'] = 25;  
		$this->email->initialize($config);  

		$this->email->set_newline("\r\n");  
		$this->load->library('email', $config);
		
		
	}
	
	public function inward_challan(){
		$this->load->view('challan/inward_challan');
	}
	
	function generate_inwardchallan()
{
    $supp=$this->input->post('supplier');
    $challan=$this->input->post('challan_no');
    $challandate=date('Y-m-d',strtotime($this->input->post('challan_date')));
    $department=$this->input->post('department');
    $remark=$this->input->post('remarks');
    $dispatch=$this->input->post('dispatch');
    $gatepass=$this->input->post('gatepass');
    $data=array('challanno'=>$challan,'supplier'=>$supp,'challandate'=>$challandate,'department'=>$department,'remarks'=>$remark,'dispatchedby'=>$dispatch,'gatepass'=>$gatepass,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
    
   // echo "<pre>"; print_r($data);exit;
    $this->db->insert('inwardchallan',$data);
    
    $lid=$this->db->insert_id();
    if($lid<>'0')
    {
      $item=$this->input->post('itemname');
      $desc=$this->input->post('item_description');
      $quantity=$this->input->post('qty');
      $unit=$this->input->post('unit');
      $ret=$this->input->post('returnable');
      $pono = $this->input->post('pono');
      $expected_date=$this->input->post('expected_date');
      $packts = $this->input->post('Pckts');
      $billno = $this->input->post('billno');
      
      $billable=$this->input->post('billable');
      for($i=0;$i<count($item);$i++)
      {
          $data1=array('challanid'=>$lid,'itemname'=>$item[$i],'itemdesc'=>$desc[$i],'quantity'=>$quantity[$i],'unit'=>$unit[$i],'returnable'=>$ret[$i],'expdate'=>date('Y-m-d',strtotime($expected_date[$i])),'billable'=>$billable[$i],'pono'=>$pono[$i],'packts'=>$packts[$i],'bill_no'=>$billno[$i],'addedOn'=>date('Y-m-d H:i:s'));
          
        //  echo "<pre>"; print_r($data1);exit;
          $this->db->insert('inwardchallan_item',$data1);
          
      }
        
        
        
        redirect(page_url.'Challan/inward_challan_dashboard');
        
        
    }
    
    
    
}
	
	public function inward_challan_dashboard(){
		$this->load->view('challan/inward_challan_dashboard');
	}
	
	function getinwardchallans()
{
    $currency_data=array();
   	$cha=$this->db->select('a.*,f.first_name,f.last_name')->from('inwardchallan a')->join('system_users f','a.addedby=f.user_id')->order_by('a.id','DESC')->get(); 
   	
   	$i=1;
   	foreach($cha->result() as $challan)
   	{
   	    	$currency_data[] = array('sr_no'=>$i,
			'challan_no'=>$challan->challanno,
			'challan_date'=>date('d-M-Y',strtotime($challan->challandate)),
			'challansuppl'=>$challan->supplier,
			'view'=>"<a href='".page_url."Challan/view_inward_challan/".$challan->id."'>View Challan</a>",
			'gatepassno'=>$challan->gatepass,
			'createdby'=>$challan->first_name.' '.$challan->last_name);
			$i++;
   	}
   	
   	
   		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $results["data"][] = $row ;
}*/
 
echo json_encode($results);
   	
   	
    
}

public function view_inward_challan(){
		$this->load->view('challan/view_inward_challan');
	}
public function outward_challan(){
		$this->load->view('challan/create_challan');
	}
public function outward_challan_dashboard(){
		$this->load->view('challan/outward_challan_dashboard');
	}
public function view_outward_challan(){
		$this->load->view('challan/view_outward_challan');
	}	
	
function generate_outwardchallan()
{
    $supp=$this->input->post('supplier');
    $challan=$this->input->post('challan_no');
    $challandate=date('Y-m-d',strtotime($this->input->post('challan_date')));
    $department=$this->input->post('department');
    $remark=$this->input->post('remarks');
    $dispatch=$this->input->post('dispatch');
    
    $data=array('challanno'=>$challan,'supplier'=>$supp,'challandate'=>$challandate,'department'=>$department,'remarks'=>$remark,'dispatchedby'=>$dispatch,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
    
  
    $this->db->insert('outwardchallan',$data);
    
    $lid=$this->db->insert_id();
    if($lid<>'0')
    {
      $item=$this->input->post('itemname');
      $desc=$this->input->post('item_description');
      $quantity=$this->input->post('qty');
      $unit=$this->input->post('unit');
      $ret=$this->input->post('returnable');
      $expected_date=$this->input->post('expected_date');
      
      $billable=$this->input->post('billable');
      for($i=0;$i<count($item);$i++)
      {
          $data1=array('challanid'=>$lid,'itemid'=>$item[$i],'itemdesc'=>$desc[$i],'quantity'=>$quantity[$i],'unit'=>$unit[$i],'returnable'=>$ret[$i],'expdate'=>date('Y-m-d',strtotime($expected_date[$i])),'billable'=>$billable[$i],'addedOn'=>date('Y-m-d H:i:s'));
          
          $this->db->insert('outwardchallan_item',$data1);
          
         
          
      }
        
        redirect(page_url.'Challan/outward_challan_dashboard');
        
        
    }
    
    
    
}

function getoutwardchallans()
{
    $currency_data=array();
   	$cha=$this->db->select('a.*,f.first_name,f.last_name')->from('outwardchallan a')->join('system_users f','a.addedby=f.user_id')->order_by('a.id','DESC')->get(); 
   	
   	$i=1;
   	foreach($cha->result() as $challan)
   	{
   	    	$currency_data[] = array('sr_no'=>$i,
			'challan_no'=>$challan->challanno,
			'challan_date'=>date('d-M-Y',strtotime($challan->challandate)),
			'challansuppl'=>$challan->supplier,
			'view'=>"<a href='".page_url."Challan/view_outward_challan/".$challan->id."'>View Challan</a>",
			'createdby'=>$challan->first_name.' '.$challan->last_name);
			$i++;
   	}
   	
   	
   		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($currency_data),
	"iTotalDisplayRecords" => count($currency_data),
	"aaData"=>$currency_data);
/*while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $results["data"][] = $row ;
}*/
 
echo json_encode($results);
   	
   	
    
}
public function nrgp_challan(){
		$this->load->view('challan/nrgp_challan_selection');
	}
	
public function nrgp_challan_items(){
		$this->load->view('challan/nrgp_challan');
	}

public function get_item_unit(){
	$source = $this->input->post('source');
	$id = $this->input->post('itemname');
	if($source=='1'){
		$query = $this->db->select('a.unit, b.shortname')->from('machine_parts_with_picture a')->join('units b','a.unit=b.id','left')->where('a.id',$id)->get();
		foreach($query->result() as $row){
			echo $row->shortname; exit;
		}
	}else{
		$query = $this->db->select('a.unit, b.shortname')->from('house_keeping_items a')->join('units b','a.unit=b.id','left')->where('a.id',$id)->get();
		foreach($query->result() as $row){
			echo $row->shortname; exit; 
		}
	}
	
	
}
public function get_item_price(){
	$source = $this->input->post('type');
	$id = $this->input->post('itemname');
	
	if($source=='1' || $source=='2'){
		$query = $this->db->select('mvalue as price')->from('presto_instruments')->where('id',$id)->get();
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row);
			echo $row->price; exit;
			}else{
			echo "0"; exit;
			}
	}else{
		
		/** GET DATA FROM LAST PO **/
		$query = $this->db->select('price')->from('purchase_order')->where('itemid',$id)->where('source','1')->order_by('id','DESC')->limit(1)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $row);
			echo $row->price; exit; 
		}else{
			
			$query =$this->db->select('max(a.price) as maxprice')->from('vendors_price a')->where('id',$id)->get();
			if($query->num_rows()>0)
		{
			foreach($query->result() as $row);
			if($row->maxprice<>NULL)
			{
			echo $row->maxprice; exit; 
			}else{
				echo "0"; exit;
			}
			
		}else{
			
			echo "0"; exit;
		}
			
			
		}
		
		/** GET **/
	}
	
	
}


public function get_supplier_item_list(){
	
	$source = $this->input->post('source');
	$supplier = $this->input->post('supplier');
	echo '<option value="">Select Item</option>';
	if($source=='1'){
		$query = $this->db->select('a.itemid, a.vendorid, b.id, b.part')->from('vendors_price a')->join('machine_parts_with_picture b','a.itemid=b.id','left')->where('a.vendorid',$supplier)->get();
		
		foreach($query->result() as $row){
			echo "<option value='".$row->id."'>".$row->part."</option>";
		}
	}else{
		$query = $this->db->select('a.vendor_id, a.item_id, b.id, b.item_name')->from('vendorwise_house_keeping_item_price a')->join('house_keeping_items b','a.item_id=b.id','left')->where('a.vendor_id',$supplier)->get();
		foreach($query->result() as $row){
			echo "<option value='".$row->id."'>".$row->item_name."</option>";
		}
	}
	
	
}

public function add_nrgp_challan(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('supplier', 'supplier', 'required|trim');
		$this->form_validation->set_rules('challan_no', 'challan_no', 'required|trim');
		$this->form_validation->set_rules('challan_date', 'challan_date', 'required|trim');
		$this->form_validation->set_rules('vehicle_type', 'vehicle_type', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
		    
			$this->load->view('challan/nrgp_challan');
			}else
		{
			date_default_timezone_set("Asia/Kolkata");
           $added_time = date('Y-m-d H:i:s');
		
		   $data=
			array('challan_date'=>date('Y-m-d',strtotime($this->input->post('challan_date'))),
			'challan_no'=>strtoupper($this->input->post('challan_no')),
			'source'=>$this->uri->segment(3),
			'supplier_id'=>$this->input->post('supplier'),
			'vehicle_type'=>$this->input->post('vehicle_type'),
			'added_by'=>$user_id,
			'added_on'=>$added_time);
			
			$res = $this->db->insert('nrgp_challan',$data);
			$last_id = $this->db->insert_id();
			
			if(isset($_REQUEST['itemname'])){	
					$tags1=count($_REQUEST['itemname']);
					if($tags1>0)
					{
					$itemname=$_REQUEST['itemname'];
					$unit = $_REQUEST['unit'];
					$qty=$_REQUEST['qty'];
					$weight_of_qty=$_REQUEST['weight_of_qty'];
					$rate_per_unit=$_REQUEST['rate_per_unit'];
					
					
					for($x=0;$x<$tags1;$x++){
					if($itemname[$x]!='')
						{
							$rate = $qty[$x]*$rate_per_unit[$x];
						$data=array('item_id'=>$itemname[$x],
							'nrgp_id'=>$last_id,
							'sent_qty'=>$qty[$x],
							'unit_id'=>$unit[$x],
							'rate'=>$rate,
							'weight_of_qty'=>$weight_of_qty[$x],
							'added_on'=>$added_time);
							
							$this->db->insert('nrgp_challan_items',$data);
						}
					}
					}
					}
			
			$this->session->set_flashdata('message','<div class="alert alert-success"><span style="color:#000; float-left:20px;">Thank you, Your record successfully added.</span></div><br/>');
				redirect(page_url.'Challan/nrgp_challan_items/'.$this->uri->segment(3));
		  
			}
	}


public function nrgp_challan_dashboard(){
     $ip = $_SERVER["REMOTE_ADDR"];
            $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
            if($query->num_rows()=='0'){
            $this->session->set_flashdata('message','<span class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">Sorry! You can not access this part since your IP Address is not whitelisted. Please contact Administrator.</span><br/>');
            redirect(page_url.'Dashboard');    
            }

else{
		$this->load->view('challan/nrgp_challan_dashboard');
}
	}
	
function get_nrgp_challanlist()
{
    $challan_data=array();
   	$cha=$this->db->select('a.*,f.first_name,f.last_name, c.name')->from('nrgp_challan a')->join('system_users f','a.added_by=f.user_id')->join('vendors c','a.supplier_id=c.id','left')->order_by('a.id','DESC')->get(); 
   	
   	$i=1;
   	foreach($cha->result() as $challan)
   	{
		if($challan->source=='1'){
			$source="Machine Items";
		}else{
			$source = "General Items";
		}
   	    	$challan_data[] = array('sr_no'=>$i,
			'challan_no'=>$challan->challan_no,
			'source'=>$source,
			'vehicle_type'=>$challan->vehicle_type,
			'challan_date'=>date('d-M-Y',strtotime($challan->challan_date)),
			'challansuppl'=>$challan->name,
			'view'=>"<a href='".page_url."Challan/view_nrgp_challan/".$challan->id."' target='_blank'>View Challan</a>",
			'createdby'=>$challan->first_name.' '.$challan->last_name);
			$i++;
   	}
   	
   	
   		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($challan_data),
	"iTotalDisplayRecords" => count($challan_data),
	"aaData"=>$challan_data);

 
echo json_encode($results);
   	
   	
    
}

public function view_nrgp_challan(){
		$this->load->view('challan/view_nrgp_challan');
	}
	
	
	public function rgp_challan(){
		$this->load->view('challan/rgp_challan_selection');
	}
	
	
	public function rgp_challan_dashboard(){
		$this->load->view('challan/rgp_challan_dashboard');
	}
	
	public function rgp_challan_items(){
		$this->load->view('challan/rgp_challan');
	}
	
	public function get_parts_price(){
	
	$id = $this->input->post('itemname');
	$query = $this->db->select('price')->from('vendors_price')->where('itemid',$id)->where('vendorid',$supplier)->get();
	foreach($query->result() as $row)
	{
		echo $row->price; exit;
	}
	
	}
	
	function add_rgp_challan()
	{
	
	$itemname=$this->input->post('itemname');
	$type=$this->input->post('type');
	for($s=0;$s<count($itemname);$s++)
	{
		if($type[$s]=='1' || $type[$s]=='2')
		{
				$stck=$this->instrumentstock($itemname[$s]);
				if($stck==0)
				{
				echo "SOME ITEMS STOCK NOT AVAILABLE. PLEASE TRY AGAIN <a href='".page_url."Challan/rgp_challan_items'>Back</a>";exit;
				}
		}else{
				$stck=$this->bomstock($itemname[$s]);
				if($stck==0)
				{
				echo "SOME ITEMS STOCK NOT AVAILABLE. PLEASE TRY AGAIN <a href='".page_url."Challan/rgp_challan_items'>Back</a>";exit;
				}
			}
	}
	
	$data=array('supplier'=>$this->input->post('user'),'challanno'=>$this->input->post('challan_no'),'challandate'=>date('Y-m-d',strtotime($this->input->post('challan_date'))),'remarks'=>$this->input->post('remarks'),'dispatchedby'=>$this->input->post('dispatch'),'standalone'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'approved'=>'1','approvedOn'=>date('Y-m-d H:i:s'));
		
	$this->db->insert('outwardchallan',$data);
	$lid=$this->db->insert_id();
	
	
	$itemname=$this->input->post('itemname');
	$unit=$this->input->post('unit');
	$qty=$this->input->post('qty');
	$qty=$this->input->post('qty');
	$weight=$this->input->post('weight_of_qty');
	$rate=$this->input->post('rate_per_unit');
	for($i=0;$i<count($type);$i++)
	{
		$udata=array('challanid'=>$lid,'type'=>$type[$i],'itemid'=>$itemname[$i],'quantity'=>$qty[$i],'unit'=>$unit[$i],'price'=>$rate[$i],'weight'=>$weight[$i],'returnable'=>'1','expdate'=>date('Y-m-d',strtotime($this->input->post('dor'))),'billable'=>'0','addedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->insert('outwardchallan_item',$udata);
		
		if($type[$i]=='1' || $type[$i]=='2')
		{
			
			$curstock=$this->instrumentstock($itemname[$i]);
			
			$news=$curstock-$qty[$i];
			$upst=array('stock'=>$news);
			$this->db->where('id',$itemname[$i]);
			$this->db->update('presto_instruments',$upst);
		}else{
			
			$curstock=$this->bomstock($itemname[$i]);
			$news=$curstock-$qty[$i];
			$upst=array('current_stock'=>$news);
			$this->db->where('id',$itemname[$i]);
			$this->db->update('machine_parts_with_picture',$upst);
			
		}
		/** SUBTRACT FROM STOCK **/
		
		
		/** END **/
		
		
	}
		
	
	$this->session->set_flashdata('<div class="alert-success alert"><span style="color:#000; float-left:20px;">Challan Created</span></div>');
	redirect(page_url.'Challan/rgp_challan_dashboard');
		
	}


	function get_rgp_challanlist()
{
 
    $type=$this->uri->segment(3);
   
    $challan_data=array();

   	$this->db->select('a.*,f.first_name,f.last_name,g.first_name as fname,g.last_name as lname')->from('outwardchallan a')->join('system_users f','a.addedBy=f.user_id','left')->join('system_users g','a.supplier=f.user_id','left');
   	
   	if($type<>'')
   	{
   	    $this->db->where('a.open',$type);
   	}
   	$cha=$this->db->group_by('a.id')->order_by('a.id','DESC')->get(); 
   	
   	$i=1;
	$html='';
   	foreach($cha->result() as $challan)
   	{
   $totalchallanprice=array();
		if($challan->standalone=='1')
		{
			$ty="Standalone";
		}else{
			
			$ty="Item Rejection";
		}
		
		
		$rrestyu=$this->db->select('*')->from('outwardchallan_item')->where('challanid',$challan->id)->get();
		if($rrestyu->num_rows()>0)
		{
			
			
			
			$html="<table border='1' style='width:800px;text-align:center;'><tr style='background-color:yellow'><th style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>ITEM</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>UNIT PRICE</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>OUT Qty</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>OUT WEIGHT</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>OUT REMARKS</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>IN QTY</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>IN WEIGHT</th><th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>IN REMARKS</th><tbody><tr>";
			    $totalchallanprice[]=0;
			    foreach($rrestyu->result() as $rrestyu1)
			{
				if($rrestyu1->type=='1' || $rrestyu1->type=='2')
				{
					$ins=$this->getmachinename($rrestyu1->itemid);
				}else{
					
					
					$ins=$this->getbom($rrestyu1->itemid);
				}
					if($challan->open==0)
					{
					$recqty='';
					$recweight='';
					$recremarks='';
					}else{
						$recqty=$rrestyu1->recvqty;
					$recweight=$rrestyu1->recvweight;
					$recremarks=$rrestyu1->recvremarks;

					}
				

					$totalchallanprice[]=$rrestyu1->price;
				
					$html.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'>".$ins."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$rrestyu1->price."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$rrestyu1->quantity."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$rrestyu1->weight." kg</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$challan->remarks."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$recqty."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$recweight."</td><td style='padding:2px 2px 2px 2px; text-align:center;'>".$recremarks."</td></tr>";	
				
			}
			
		}
		
			if($challan->open==0)
			{
				$op="<span class='btn btn-xs btn-warning'>Open</span>";
			
			}else{
				
				$op="<span class='btn btn-xs btn-success'>Closed</span>";
			}
			
			if($challan->open==0)
			{
			   $inw="<a href='".page_url."Challan/inwardrgpitems/".$challan->id."'>Inward items</a>";
			}else{
				$inw="<strong>Items Inwarded On <br/>".date('d-M-Y H:i:s',strtotime($challan->closedOn))."</strong>";
			}
   	    	$challan_data[] = array('sr_no'=>$i,
			'status'=>$op,
			'type'=>$ty,
			'challan_no'=>"<a href='".page_url."Challan/viewrgpchallan/".$challan->id."' target='_blank'>".$challan->challanno."</a>",
			'date'=>date('d-M-Y',strtotime($challan->challandate)),
			'preparedfor'=>$challan->fname." ".$challan->lname,
			'dispatchedby'=>$challan->fname." ".$challan->lname,
			'items'=>$html,
			'challanamt'=>array_sum($totalchallanprice),
			'view'=>$inw,
			'createdby'=>$challan->first_name.' '.$challan->last_name,
			'createdon'=>date('d-M-Y',strtotime($challan->approvedOn)));
			$i++;
   	}
   	
   	
   		$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($challan_data),
	"iTotalDisplayRecords" => count($challan_data),
	"aaData"=>$challan_data);

 
echo json_encode($results);
   	
   	
    
}


function getmachinename($in)
{
	$ins='';
	$query = $this->db->select('instruments_name')->from('presto_instruments')->where('id',$in)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->instruments_name;
			
		}
		
		return $ins;

}


function getbom($id)
{
	$ins1='';
	$query = $this->db->select('part')->from('machine_parts_with_picture')->where('id',$id)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->part;
			
		}
		
		return $ins;

}

function viewrgpchallan()
{
	
	$this->load->view('challan/rgp_challan_format');
	
	
}

function inwardrgpitems()
{
	
$this->load->view('challan/inwardrgpitems');	
}

function inward_rgp_challan()
{
	
	$a[]=0;
	$chalnnitem=$this->input->post('qitem');
	$chalid=$this->uri->segment('3');
	for($i=0;$i<count($chalnnitem);$i++)
	{
		$id=$chalnnitem[$i];
		
		$type=$this->input->post('type'.$id);
		$iname=$this->input->post('itemname'.$id);
		$data=array('recvqty'=>$this->input->post('rqty'.$id),'recvweight'=>$this->input->post('rweight'.$id),'recvremarks'=>$this->input->post('rmk'.$id));
		//echo "<pre>"; print_r($data);exit;
		$this->db->where('id',$id);
		$this->db->where('challanid',$chalid);
		$this->db->update('outwardchallan_item',$data);
		if($this->db->affected_rows()>0)
		{
			$a[]=1;
		}
	
	
		/** ADD STOCK **/
		if($type=='1' || $type=='2')
		{
			
			$curstock=$this->instrumentstock($iname);
			
			$news=$curstock+$this->input->post('rqty'.$id);
			$upst=array('stock'=>$news);
			$this->db->where('id',$iname);
			$this->db->update('presto_instruments',$upst);
		}else{
			
			$curstock=$this->bomstock($iname);
			$news=$curstock+$this->input->post('rqty'.$id);
			$upst=array('current_stock'=>$news);
			$this->db->where('id',$iname);
			$this->db->update('machine_parts_with_picture',$upst);
			
		}
		/** ADD TO STOCK **/
	
	}
	
	if(array_sum($a)==count($chalnnitem))
	{
		$cldata=array('open'=>'1','closedOn'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$chalid);
		$this->db->update('outwardchallan',$cldata);
	}
	
		$this->session->set_flashdata('message','<div class="alert-success alert"><span style="color:#000; float-left:20px;">RGP CHALLAN Inwarded & CLOSED</span></div>');
		redirect(page_url.'Challan/rgp_challan_dashboard');
	
	
}


public function get_item_stock(){
	$source = $this->input->post('type');
	$id = $this->input->post('itemname');
	if($source=='1' || $source=='2'){
		
			$query = $this->db->select('stock')->from('presto_instruments')->where('id',$id)->get();
			if($query->num_rows()>0)
			{
			foreach($query->result() as $instruments);
			$ins=$instruments->stock;
			echo $ins;
			}else{
			echo 0;
			}			
		
	}else{
	$query = $this->db->select('a.current_stock')->from('machine_parts_with_picture a')->where('a.id',$id)->get();
	if($query->num_rows()>0)
			{
		    foreach($query->result() as $row);
			echo $row->current_stock;
			}else{
			echo 0;
			}
	
	}
}


function instrumentstock($id)
{
	
	$query = $this->db->select('stock')->from('presto_instruments')->where('id',$id)->get();
			if($query->num_rows()>0)
			{
			foreach($query->result() as $instruments);
			$ins=$instruments->stock;
			return $ins;
			}else{
			return 0;
			}	
	
	
	
}

function bomstock($id)
{
	
	$query = $this->db->select('a.current_stock')->from('machine_parts_with_picture a')->where('a.id',$id)->get();
	if($query->num_rows()>0)
			{
		    foreach($query->result() as $row);
			return $row->current_stock;
			}else{
			return 0;
			}
	
}

}
