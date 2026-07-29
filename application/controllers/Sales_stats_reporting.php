<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_stats_reporting extends CI_Controller {
	
	public function __construct()
	{
		
		parent::__construct();
		$session = $this->session->userdata('logged_in');
		$this->load->model('User_model','user');
		$this->load->model('Salescrm_model','salescrm');
			
		
	}
	
	public function sales_order_stats (){
		$this->load->view('sales_reporting/yearly_sales');
	}

	function get_sales_stats()
	{
		$type=$this->input->post('type');
		if($type==2)
		{
		$startdate=date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate=date('Y-m-d',strtotime($this->input->post('enddate')));

		}else
		{
			$startdate=$this->input->post('startyear');
			$enddate=$this->input->post('endyear');
		}
		redirect(page_url.'Sales_stats_reporting/sales_order_stats/'.$type.'/'.$startdate.'/'.$enddate);

	}


	public function get_sales_data(){
		$type = $this->uri->segment(3);
		$startdate = $this->uri->segment(4);
		$enddate = $this->uri->segment(5);
		
		if($type==1)
		{

			$date1 = explode('-',$startdate);
			$date2 = explode('-',$enddate);

			$startyear=$date1[0];
			$endyear=$date2[1];

			$date1=date($startyear.'-04-01');
			$date2=date($endyear.'-03-31');

			$diff = abs(strtotime($date2)-strtotime($date1));

			$years = floor($diff / (365*60*60*24));

			$curyear=date('Y',strtotime($date1));

			for($i=0;$i<$years+1;$i++)
			{
				

				$last=$curyear+1;

				$monthstart=date('Y-m-d',strtotime($curyear."-04-01"));
				$monthend=date('Y-m-d',strtotime($last."-04-01"));
				
				$salesno=$this->get_sales_numbers($monthstart,$monthend);
				$salesqty=$this->get_sales_qty($monthstart,$monthend);


					$output[] = array(
					'labels'  =>$salesqty,
					'count' => $salesqty,
					'indus'=>$curyear."-".$last,
					'tool'=>'Sales Qty-'.$salesqty." LTR\n Sales Amount-".$salesno
					);


					$curyear=$curyear+1;

			}

		}else
		{


			$start = $month = strtotime($startdate);
			$end = strtotime($enddate);
			while($month <= $end)
			{

				$monthstart=date('Y-m-01',$month);
				$monthend=date('Y-m-t',$month);
				$salesno=$this->get_sales_numbers($monthstart,$monthend);
				$salesqty=$this->get_sales_qty($monthstart,$monthend);

					$output[] = array(
					'labels'  => $salesqty,
					'count' => $salesqty,
					'indus'=>date('F Y', $month),
					'tool'=>'Sales Qty-'.$salesqty." LTR\n Sales Amount-".$salesno
					);



			$month = strtotime("+1 month", $month);
			}




		}


	
		  print json_encode($output);
	}


	function get_sales_numbers($startdate,$enddate)
	{

		$sale_price=array();
		$sale_price[]=0;
		$startdate=$startdate." 00:00:00";
		$enddate=$enddate." 23:59:59";
		
		$query = $this->db->select('a.quotation_id as orderpunchquote')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.billing', 1)
						  ->where('a.cancelled',0)
						  ->where('a.billed_On>=',$startdate)
						  ->where('a.billed_On<=',$enddate)
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
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

	function getdates_sorted()
	{

		$m=$this->input->post('month');

		if (preg_match('/[-]/', $m))
		{
			$d=explode('-',$m);

			$month=date($d[0].'-04-01')."|".date($d[1].'-03-31')."|1";
		}else
		{
			$month=date('Y-m-01',strtotime($this->input->post('month')))."|".date('Y-m-t',strtotime($this->input->post('month')))."|2";
		}

		echo $month; exit;


	}

	function most_selling_product()
	{
		$this->load->view('sales_reporting/most_selling_product');
	}

	function get_product_sales_data()
	{
		
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);
		
		if($prd<>'ALL')
		{
			$prd=base64_decode($prd);
			$prd = "'" . str_replace(",", "','", $prd) . "'";
		}else
		{
			$prd="ALL";
		}
		



		$query = $this->db->select('a.product_id,c.instruments_name,c.pack_size,c.model_number')
						  ->from('customer_quotation_detail a')
						  ->join('order_punch b','b.quotation_id=a.quotation_id')
						  ->join('presto_instruments c','a.product_id=c.id')
						  ->where('b.billing', 1)
						  ->where('b.cancelled',0)
						  ->where('b.billed_On>=',$startdate)
						  ->where('b.billed_On<=',$enddate);
						  if($prd<>'ALL')
						  {
						  	$this->db->where_in('a.product_id',$prd,false);
						  }
						 $query =$this->db->group_by('a.product_id')->get();

				 		 

		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$res=$this->product_sale_stats($row->product_id,$startdate,$enddate);
		
				$output[] = array(
					'labels'  =>round($res),
					'count' =>round($res),
					'indus'=>$row->instruments_name."-".$row->pack_size."|".$row->model_number,
					'id'=>$row->product_id
					);
		}


		array_multisort(array_column($output, 'count'), SORT_DESC, $output);
		}


		
		if(count($output)>0)
		{
			for($i=0;$i<count($output);$i++)
			{
				if($i<5)
				{
			$output1[]=array(
						'labels' =>$output[$i]['labels'],
						'count' =>$output[$i]['count'],
						'indus'=>$output[$i]['indus'],
						'productid'=>$output[$i]['id']);
				}

			}
		}	
		

	
		print json_encode($output1);

	}



function get_product_sales_qty_data()
	{
		
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);
		
		if($prd<>'ALL')
		{
			$prd=base64_decode($prd);
			$prd = "'" . str_replace(",", "','", $prd) . "'";
		}else
		{
			$prd="ALL";
		}
		



		$query = $this->db->select('a.qty,a.product_id,c.instruments_name,c.pack_size,c.model_number,c.unit')
						  ->from('customer_quotation_detail a')
						  ->join('order_punch b','b.quotation_id=a.quotation_id')
						  ->join('presto_instruments c','a.product_id=c.id')
						  ->where('b.billing', 1)
						  ->where('b.cancelled',0)
						  ->where('b.billed_On>=',$startdate)
						  ->where('b.billed_On<=',$enddate);
						  if($prd<>'ALL')
						  {
						  	$this->db->where_in('a.product_id',$prd,false);
						  }
						 $query =$this->db->group_by('a.product_id')->get();

				 		 

		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$res=$this->product_sale_stats_via_qty($row->product_id,$startdate,$enddate);
			$res1=$this->product_sale_stats($row->product_id,$startdate,$enddate);
		
				$output[] = array(
					'labels'  =>round($res),
					'count' =>round($res),
					'indus'=>$row->instruments_name."-".$row->pack_size."|".$row->model_number,
					'id'=>$row->product_id,
					'tool'=>'Qty-'.$res." ".$row->unit."\nAmount-".$res1
					);
		}


		array_multisort(array_column($output, 'count'), SORT_DESC, $output);
		}


		
		if(count($output)>0)
		{
			for($i=0;$i<count($output);$i++)
			{
				if($i<5)
				{
			$output1[]=array(
						'labels' =>$output[$i]['labels'],
						'count' =>$output[$i]['count'],
						'indus'=>$output[$i]['indus'],
						'productid'=>$output[$i]['id'],
						'tool'=>$output[$i]['tool']
							);
				}

			}
		}	
		

	
		print json_encode($output1);

	}


	function product_sale_stats($product_id,$startdate,$enddate)
	{
		$qur=array();
		$qur[]=0;
		$query = $this->db->select('a.agreed_price,a.qty')
						  ->from('customer_quotation_detail a')
						  ->join('order_punch b','b.quotation_id=a.quotation_id')
						  ->join('presto_instruments c','a.product_id=c.id')
						  ->where('b.billing', 1)
						  ->where('b.cancelled',0)
						  ->where('b.billed_On>=',$startdate)
						  ->where('b.billed_On<=',$enddate)
						  ->where('a.product_id',$product_id)
				 		  ->get();

				 		  if($query->num_rows()>0)
				 		  {

				 		  	foreach($query->result() as $queries)
				 		  	{
				 		  		$qur[]=$queries->agreed_price*$queries->qty;


				 		  	}


				 		  }

				 		  return array_sum($qur);

	}
	

	function get_product_sale_stats()
	{

		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		$product=$this->input->post('product');
		
		if(count($product)>1)
		{
		$product = array_diff($product, array("ALL"));
		$prd=base64_encode(implode(",",$product));
		}else if(count($product)==1 && $product[0]<>'ALL')
		{
			$prd=base64_encode(implode(",",$product));
		}else
		{
			$prd="ALL";
		}
		
		redirect(page_url.'Sales_stats_reporting/most_selling_product/'.$startdate.'/'.$enddate.'/'.$prd);
	}
	



	function get_product_sales_data_tabular()
	{
		
		$html='<table class="table table-bordered" style="text-align:center">
		<thead>
		<tr>

		<th scope="col" style="text-align:center">SR NO</th>
		<th scope="col" style="text-align:center">PRODUCT NAME</th>
		<th scope="col" style="text-align:center">SALE QTY</th>
		<th scope="col" style="text-align:center">SALE AMOUNT (in ₹)</th>

		</tr>
		</thead>
		<tbody>';
		
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);
		if($prd<>'ALL')
		{
			$prd=base64_decode($prd);
			$prd = "'" . str_replace(",", "','", $prd) . "'";
		}else
		{
			$prd="ALL";
		}
		

		$query = $this->db->select('a.product_id,c.instruments_name,c.pack_size,c.unit')
						  ->from('customer_quotation_detail a')
						  ->join('order_punch b','b.quotation_id=a.quotation_id')
						  ->join('presto_instruments c','a.product_id=c.id')
						  ->where('b.billing', 1)
						  ->where('b.cancelled',0)
						  ->where('b.billed_On>=',$startdate)
						  ->where('b.billed_On<=',$enddate);
						  if($prd<>'ALL')
						  {
						  	$this->db->where_in('a.product_id',$prd,false);
						  }
						 $query =$this->db->group_by('a.product_id')->get();

				 		 

		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$res=$this->product_sale_stats_via_qty($row->product_id,$startdate,$enddate);
			$res1=$this->product_sale_stats($row->product_id,$startdate,$enddate);
		
				$output[] = array(
					'qty_sale'=>$res,
					'unit'=>$row->unit,
					'count' =>round($res1),
					'indus'=>$row->instruments_name."-".$row->pack_size,
					);
		}


		array_multisort(array_column($output, 'qty_sale'), SORT_DESC, $output);
		}


	
		
		if(count($output)>0)
		{
			for($i=0;$i<count($output);$i++)
			{
				$k=$i+1;
				$html.='<tr>
                          <td>'.$k.'</td>
                          <td>'.$output[$i]['indus'].'</td>
                          <td style="color:red;font-weight:bold;">'.$output[$i]['qty_sale'].' '.$output[$i]['unit'].'</td>
                          <td style="color:red;font-weight:bold;">₹'.$output[$i]['count'].'</td>
                        </tr>';

			}
		}else
		{
			$html.='<tr>
                          <td colspan="3" class="text-align:center">No Data Available</td>
                       
                        </tr>';
		}	
		

	
		echo $html;

	}

		function get_product_and_range()
		{
			$product_id=0;
			$product=$this->input->post('product');
			$ar=explode('|',$product);
			$prd=end($ar);
			$row=$this->db->select('id')->from('presto_instruments')->where('model_number',$prd)->get();
			if($row->num_rows()>0)
			{
				foreach($row->result() as $rows);

				$product_id=$rows->id;

			}

			echo $product_id;

		}

		function product_sales_history()
		{
			$this->load->view('sales_reporting/product_sales_history');
		}



		function get_product_sales_data_range_wise()
	{
		$output=array();
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);
		
		$start = $month = strtotime($this->uri->segment(3));
			$end = strtotime($this->uri->segment(4));

			if($this->uri->segment(3)!='' && $this->uri->segment(4)!='' && $this->uri->segment(5)!='')
			{
			while($month <= $end)
			{
			$saledata=array();
			$saledata[]=0;
			$startdate=date('Y-m-01', $month);
			$enddate=date('Y-m-t',$month);
			$query = $this->db->select('a.quotation_id,a.product_id')
			->from('customer_quotation_detail a')
			->join('order_punch b','b.quotation_id=a.quotation_id')
			->join('presto_instruments c','a.product_id=c.id')
			->where('b.billing', 1)
			->where('b.cancelled',0)
			->where('b.billed_On>=',$startdate)
			->where('b.billed_On<=',$enddate)
			->where('a.product_id',$prd)
			->get();

			$res = $query->result();
		$i=1;
			if($query->num_rows()>0)
			{
			foreach($res as $row)
			{
				$saledata[]=$this->product_sale_data_by_range($row->product_id,$row->quotation_id);
			}

			}


					$output[] = array(
					'labels'  => array_sum($saledata),
					'count' => array_sum($saledata),
					'indus'=>date('F Y', $month),
					);
				$month = strtotime("+1 month", $month);

			}


		
			}

	
		

	
		print json_encode($output);

	}


	function product_sale_data_by_range($prdid,$quotation_id)
	{
		$d=array();
		$d[]=0;
		$restey=$this->db->select('agreed_price,qty')->from('customer_quotation_detail')->where('quotation_id',$quotation_id)->where('product_id',$prdid)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row)
			{
				$d[]=round($row->agreed_price*$row->qty);
			}

		}

		return array_sum($d);

	}

	function get_product_sale_stats_by_range()
	{

		$startdate=$this->input->post('startdate');
		$enddate=$this->input->post('enddate');
		$product=$this->input->post('product');
	
		
		redirect(page_url.'Sales_stats_reporting/product_sales_history/'.$product.'/'.$startdate.'/'.$enddate);
	}

	function get_product_name()
	{
		$prdname='';
		$id=$this->uri->segment(3);
		$rest=$this->db->select('instruments_name,pack_size,model_number')->from('presto_instruments')->where('id',$id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$prdname=$row->instruments_name."-".$row->pack_size."-".$row->model_number;
		}

		echo $prdname;
	}
	

	function customer_timeline()
	{
		$this->load->view('sales_reporting/customer_timeline');
	}

	function getclient()
	{
		if(isset($_GET['searchTerm']))
		{
		$searchtrm= $_GET['searchTerm'];
		}else
		{
		$searchtrm='';
		}

			$rest=$this->db->select('id,company_name')->from('customer_detail')->like('company_name',$searchtrm,'both')->limit(10)->get();
			if($rest->num_rows()>0)
			{ 
				foreach($rest->result() as $row)
				{
					$json[] = array('id'=>$row->id, 'text'=>$row->company_name);
				}

			}else
			{
				$json[] = array('id'=>"", 'text'=>"No Data Available");
			}

	   
	   		echo json_encode($json);
	}

	function fetch_customer_timeline()
	{
		$custid=$this->uri->segment(3);
		redirect(page_url.'Sales_stats_reporting/customer_timeline/'.$custid);
	}

	function getcustomerinfo()
	{
		$html='<table class="table table-bordered" style="background-color:#fff">
    <thead>
         <tr>
        <th colspan="13" style="text-align:center">Customer Details</th>
       
      </tr>
      <tr>  
        <th>Company Name</th>
        <th>Customer Name</th>
        <th>Mobile Number</th>
        <th>Email Address</th>
        <th>Billing Address</th>
        <th>Shipping Address</th>
        <th>GST No.</th>
        <th>PAN No.</th>
        <th>MSME No.</th>
        <th>Order Max Limit</th>
        <th>Payment Term</th>
        <th>Payment Due</th>
      </tr>
    </thead>
    <tbody>';
		$id=$this->uri->segment('3');
		$res=$this->db->select('a.*,b.state_name as bill_state,c.state_name as ship_state')->from('customer_detail a')->join('states b','a.bill_state=b.state_id')->join('states c','a.ship_state=b.state_id')->where('a.id',$id)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);

			if($row->payment_type==2)
			{
				$ptype="Cash";
			}else if($row->payment_type==3)
			{	
				$ptype="Online";

			}else if($row->payment_type==4)
			{
				$ptype="PDC"."<br/>Credit Days: ".$row->credit_days;
			}else if($row->payment_type==5)
			{
				$ptype="Credit"."<br/>Credit Days: ".$row->credit_days;
			}else if($row->payment_type==6)
			{
				$ptype="Advance";
			}else
			{
				$ptype='';
			}

			$paydue=$this->salescrm->getunpaid_order_amount($id);
				$html.='<tr>
				<td><strong style="color:red">'.$row->company_name.'</strong></td>
				<td>'.$row->customer_name.'</td>
				<td>'.$row->contact_no.'</td>
				<td>'.$row->email.'</td>
				<td>'.$row->bill_address.','.$row->bill_state.' '.$row->bill_city.'-'.$row->bill_pincode.'</td>
				<td>'.$row->ship_address.','.$row->ship_state.' '.$row->ship_city.'-'.$row->ship_pincode.'</td>
				<td>'.$row->gst.'</td>
				<td>'.$row->pan.'</td>
				<td>'.$row->msme_number.'</td>
				<td>'.$row->order_max_limit.'</td>
				<td>'.$ptype.'</td>
				<td style="color:red;font-weight:bold;font-size:18px;">'.$paydue.'</td>
				</tr>
				';


		}else
		{
			$html.='<tr>
					<td colspan="12">No Data Available</td>
					</tr>';
		}

			$html.='  </tbody>
			</table>';
		echo $html;
	}

	function getcustomer_quote_info()
	{
		$data=array();
		$id=$this->uri->segment(3);
		$html=' <table class="table table-bordered" style="background-color:#fff">
    <thead>
         <tr>
        <th style="text-align: center;font-size:18px;" colspan="5">Quotations</th>
       
      </tr>
      <tr>
        <th>Quote No.</th>
        <th>Product Details</th>
        <th>Lead Manager/Company</th>
        <th>Quotation Shared On</th>
        
      </tr>
    </thead>
    <tbody>';

   $row=$this->db->select('c.companyname,a.id,a.lead_id,a.company_id,a.added_on,a.added_by,a.special_remarks,b.first_name,b.last_name,a.order_punch')->from('customer_quotation a')->join('system_users b','a.added_by=b.user_id')->join('store_rack_location c','c.id=a.company_id')->where('customer_id',$id)->get();
   if($row->num_rows()>0)
   {
   foreach($row->result() as $rows)
   {
   	$prd=$this->get_quotation_products($rows->id);

   	if($rows->lead_id==0)
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/quotation.php?quotation_id=".$rows->id."'  target='_blank' style='font-weight:bold;'>Preview</a>";
			}else
			{
				$view = "<a href='".site_http_root."poformat/tcpdf/examples/hpcl_emailer.php?lead_id=".$rows->lead_id."'  style='font-weight:bold;'  target='_blank'>Preview</a>";

			}

			if($rows->order_punch==1)
			{
				$o="<a href='javascript:;' class='btn btn-xs btn-success'>Order created</a>";
			}else
			{
				$o="<a href='' class='btn btn-xs btn-warning'>Order not created</a>";
			}
   	$data[]=array('quoteid'=>"QUOTE".$rows->id."<br/>".$view,'product'=>$prd,'lead_manager'=>$rows->first_name." ".$rows->last_name."<br/><br/>".$rows->companyname,"sharedOn"=>date('d-M-Y H:i:s',strtotime($rows->added_on)),'quotation'=>$o);
   }

   array_multisort(array_column($data, 'sharedOn'), SORT_DESC, $data);
   }

if(count($data)>0)	
{
 for($t=0;$t<count($data);$t++)
 {

    $html.= '<tr>
        <td>'.$data[$t]['quoteid'].'</td>
        <td>'.$data[$t]['product'].'</td>
        <td>'.$data[$t]['lead_manager'].'</td>
        <td>'.$data[$t]['sharedOn']."<br/><br/>".$data[$t]['quotation'].'</td>
        
      </tr>';
  }
}else
{
	  $html.= '<tr>
        <td colspan="4">No Data Available</td>
        </tr>';
}



	$html.='</tbody>
	</table>';

	echo $html;


	}

	function get_quotation_products($quoteid)
	{
		$html=' <table class="table table-bordered" style="background-color:#fff">
    <thead>
       
      <tr>
       
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>';

		$rest=$this->db->select('a.product_id,a.qty,a.list_price,b.instruments_name,b.pack_size,unit')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$quoteid)->get();
		if($rest->num_rows()>0)
		{
			$i=1;
			foreach($rest->result() as $row)
			{

					$html.= '<tr>
					
					<td>'.$row->instruments_name."-".$row->pack_size.'</td>
					<td>'.$row->qty.' '.$row->unit.'</td>
					<td>'.$row->list_price.'</td>
				
					</tr>';

			$i++; 
			}


		}else
		{

					$html.= '<tr>
					<td colspan="3">No Data Available</td>
					</tr>';
		}

		$html.='</tbody></table>';
		return $html;

	}

	function getcustomer_order_details()
	{

		$data=array();
		$id=$this->uri->segment(3);
		$html=' <table class="table table-bordered" style="background-color:#fff">
    <thead>
         <tr>
        <th style="text-align: center;font-size:18px" colspan="6">ORDER DETAILS</th>
       
      </tr>
      <tr>
				<th>Order No./Lead Manager</th>
				<th>Billing Company </th>
				<th>Product Details</th>
				<th>Payment Terms/Status</th>
				<th>Created By/Ordered On</th>
				<th>Other Details</th>
				
       
      </tr>
    </thead>
    <tbody>';


$query = $this->db->select('a.generated_order_id,a.added_on as orderedOn,g.lead_source,h.first_name,h.last_name,a.source,a.agent,f.bill_to,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst, d.company_name, d.customer_name,e.pan_no,e.gst_no, e.msme_no,f.ship_to, f.shipping_name,f.shipping_address,f.shipping_state,f.shipping_city,f.shipping_pincode,f.shipping_phone_no,f.shipping_mobile_no,f.shipping_email,f.billing_name,f.billing_address,f.billing_state,f.billing_city,f.billing_pincode,f.billing_phone_no,f.billing_mobile_no,f.billing_email,i.first_name as lfname,i.last_name as llname,a.billing,a.dispatch,a.billed_On,a.dispatched_on,a.invoice_no')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details f','a.id=f.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b', 'b.id=a.quotation_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users i','i.user_id=b.added_by')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
						  ->join('customer_detail d', 'd.id=b.customer_id')
						  ->where('b.order_punch', 1)
						  
						  ->where('a.cancelled', 0)
						  ->where('b.customer_id',$id)
						  ->order_by('a.added_on','DESC')
				 		  ->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

				$prd=$this->getproducts_detail_for_order($row->quotation_id);

				if($row->payment_type == 2) {
						$payment_type = 'Cash';
						$payment_terms="<strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.= '';
					} else if($row->payment_type == 3) {
						$payment_type = 'Online';
						$payment_terms="<strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms .= '';
					} else if($row->payment_type == 4) {
						$payment_type = 'PDC';
						$payment_terms="<strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 5) {
						$payment_type = 'CREDIT';
						$payment_terms="<strong>".$payment_type.'</strong><br/><br/>';
						$payment_terms.="DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
					}else if($row->payment_type == 6) {
						$payment_type = 'ADVANCE';
						$payment_terms="<strong>".$payment_type.'</strong><br/><br/>';
						
					} else {
						$payment_type = '';
						$payment_terms="";
						$payment_terms .= '';
					}
					if($row->billing==1)
					{
							$billed="Billed On <br/>".date('d-m-Y',strtotime($row->billed_On))."<br/><br/>Invoice No: ".$row->invoice_no;
					}else
					{
						$billed="Billing pending";
					}


					if($row->dispatch==1)
					{
							$dispatched="Dispatched On<br/>".date('d-m-Y',strtotime($row->dispatched_on));
					}else
					{
						$dispatched="Dispatched pending";
					}


				$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
				$partial=$this->salescrm->customer_previous_payment($row->id);
				$payment_due=$order_value-$partial;
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs' target='_blank'>Order Details</a>";
				if($payment_due>0)
				{
					$pay="<span style='color:red;font-weight:bold;font-size:15px;'>Due-".$payment_due."</span>";
				}else
				{
					$pay="<span style='color:green'>PAID</span>";
				}
				$html.="<tr>

				<td>".$row->generated_order_id."<br/><br/>".$row->lfname." ".$row->llname."</td>
				<td>".$row->companyname."</td>
				<td>".$prd."</td>
				<td>".$payment_terms."<br/>".$pay."</td>
				<td>".$row->first_name." ".$row->last_name."<br/>".date('d-M-Y H:i',strtotime($row->orderedOn))."</td>
				
				<td>".$billed."<br/><br/>".$dispatched."<br/><br/>".$order_details."</td>
			

				</tr>";

			}

			}else
			{

					$html.="<tr>
    
				<td colspan='6'>No Data Available</td>
			

				</tr>";
			}



	$html.='</tbody>
	</table>';

	echo $html;

	}

	function getproducts_detail_for_order($quotation)
	{
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		
		<th>Product/Batch</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		</tr>
		</thead>
		<tbody>';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,b.pack_size')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			$html.='<tr>';
			$html.='<td>'.$product->instruments_name.'-'.$product->pack_size.'<br/><br/>Batch: '.$product->batch_code.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';
			$j++;
			}
			$html.='</tbody></table>';
			}

			return $html;
	}

	function customer_not_buying()
	{
		$data=array();
		$months=array();
		$id=$this->uri->segment(3);
		$month[]= date('M');
		$month[]= date('M', strtotime('-1 month'));
		$month[]= date('M', strtotime('-2 month'));
		$month[]= date('M', strtotime('-3 month'));

		$products=array();

		$i=0;
		foreach($month as $months)
		{
			
			$start=date('Y-'.$months.'-01')." 00:00:00";
			
			$end=date('Y-'.$months.'-t')." 23:59:59";
			echo $end; exit;
			//echo $start."<br/>".$end; exit;

			$products[]=$start."-".$end; 
			if($i==0)
			{
				$a="b.id";
			}else
			{
					$a="b.id";
			}
					$query = $this->db->select($a.' as quotation_id')
					->from('order_punch a')
					->join('order_punch_mailing_details f','a.id=f.order_id')
					->join('order_punch_tax_details e','a.id=e.order_id')
					->join('customer_quotation b', 'b.id=a.quotation_id')
					->join('lead_source g','g.source_id=a.source','left')
					->join('system_users h','h.user_id=a.agent')
					->join('system_users i','i.user_id=b.added_by')
					->join('store_rack_location c', 'c.id=a.hpcl_billing_company')
					->join('customer_detail d', 'd.id=b.customer_id')
					->where('b.order_punch', 1)
					->where('a.cancelled', 0)
					->where('a.billing', 1)
					->where('b.customer_id',$id)
					->where('a.added_on>=',$start)
					->where('a.added_on>=',$end)
					->order_by('a.added_on','DESC')
					->get();

					if($query->num_rows() > 0) {
					foreach($query->result() as $row) {

						//$products[$months]=array('prd'=>$this->get_quotation_products_monthly($row->quotation_id));

					}

					}

	$i++;
		}



		echo "<pre>"; print_r($products); 

	}


			function get_quotation_products_monthly($quoteid)
	{
		$data=array();
		$rest=$this->db->select('a.product_id')->from('customer_quotation_detail a')->where('a.quotation_id',$quoteid)->get();
		if($rest->num_rows()>0)
		{
			
			foreach($rest->result() as $row)
			{
				$data[]=$row->product_id;
				
			
			}


		}

		
		return $data;

	}

	function get_customer_sale_pattern()
	{
		
	}


	function getcustomer_payment_history()
	{

		$data=array();
		$id=$this->uri->segment(3);
		$html=' <table class="table table-bordered" style="background-color:#fff">
    <thead>
         <tr>
        <th style="text-align: center;font-size:18px" colspan="6">CUSTOMER PAYMENTS</th>
       
      </tr>
      <tr>
		
				<th>Payment Through</th>
				<th>Payment Date</th>
				<th>Amount</th>
				<th>Entered By</th>
				<th>Against Invoice</th>
				
       
      </tr>
    </thead>
    <tbody>';

    $restey=$this->db->select('b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->where('b.customer_id',$id)->get();
    if($restey->num_rows()>0)
    {
    	foreach($restey->result() as $row)
    	{
    		
    		if($row->payment_type==1)
    		{
    			$PT="Cheque <br/>".$row->cheque_no."<br/>".date('Y-m-d',strtotime($row->cheque_date));

    		}else if($row->payment_type==2)
    		{
    			$PT="Cash";
    		}else if($row->payment_type==3)
    		{
    			$PT="NEFT<br/>".$row->neft_trans_no;
    		}else
    		{
    			$PT='';
    		}

    		$name=$this->getusername($row->addedBy);
    		$invoice_no=$this->get_invoice($row->id);

    $html.='<tr>
    				<td>'.$PT.'</td>
    				<td>'.date('d-M-Y',strtotime($row->addedOn)).'</td>
    				<td>'.$row->amount.'</td>
    				<td>'.$name.'</td>
    				<td>'.$invoice_no.'</td>
    			
    			
    				</tr>';
    	}
    }




	$html.='</tbody>
	</table>';

	echo $html;

	}

	function getusername($username)
	{
		$name='';
		$restey=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$username)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$name=$row->first_name." ".$row->last_name;
		}

		return $name;
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


	function product_sale_stats_via_qty($product_id,$startdate,$enddate)
	{
		$qur=array();
		$qur[]=0;
		$query = $this->db->select('a.agreed_price,a.qty')
						  ->from('customer_quotation_detail a')
						  ->join('order_punch b','b.quotation_id=a.quotation_id')
						  ->join('presto_instruments c','a.product_id=c.id')
						  ->where('b.billing', 1)
						  ->where('b.cancelled',0)
						  ->where('b.billed_On>=',$startdate)
						  ->where('b.billed_On<=',$enddate)
						  ->where('a.product_id',$product_id)
				 		  ->get();

				 		  if($query->num_rows()>0)
				 		  {

				 		  	foreach($query->result() as $queries)
				 		  	{
				 		  		$qur[]=$queries->qty;


				 		  	}


				 		  }

				 		  return array_sum($qur);

	}

	function customer_wise_product_consumption()
		{
			$this->load->view('sales_reporting/customer_wise_product_qty');
		}

	function customer_wise_product_consumption_list()
	{
		$output=array();
		$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);

			$startdate = date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$prd=$this->uri->segment(5);
		
			$query = $this->db->select('d.customer_id,e.company_name,c.unit')
			->from('customer_quotation_detail a')
			->join('customer_quotation d','a.quotation_id=d.id')
			->join('order_punch b','b.quotation_id=a.quotation_id')
			->join('presto_instruments c','a.product_id=c.id')
			->join('customer_detail e','e.id=d.customer_id')
			->where('b.billing', 1)
			->where('b.cancelled',0)
			->where('b.billed_On>=',$startdate)
			->where('b.billed_On<=',$enddate)
			->where('a.product_id',$prd)
			->group_by('d.customer_id')
			->get();


		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$res=$this->get_customer_wise_consumption($prd,$startdate,$enddate,$row->customer_id);
			$rest=explode('|',$res);

			$qty_sum=$rest[0];
			$sale_sum=$rest[1];
		
				$output[] = array(
					'labels'  =>round($qty_sum),
					'count' =>round($qty_sum),
					'indus'=>$row->company_name,
					'id'=>$prd,
					'tool'=>'Sale Qty-'.$qty_sum." ".$row->unit."\nAmount-".$sale_sum
					);





		}


		array_multisort(array_column($output, 'count'), SORT_DESC, $output);
		}


		
		$output1=array();
		if(count($output)>0)
		{
			for($i=0;$i<count($output);$i++)
			{
				if($i<5)
				{
			$output1[]=array(
						'labels' =>$output[$i]['labels'],
						'count' =>$output[$i]['count'],
						'indus'=>$output[$i]['indus'],
						'productid'=>$output[$i]['id'],
						'tool'=>$output[$i]['tool']);



		


				}

			}
		}	
		

	
		print json_encode($output1);
			
			
		


	}


	function get_customer_wise_consumption($prd,$startdate,$enddate,$cust)
	{

		$data=array();
		$data1=array();
		$query = $this->db->select('d.customer_id,e.company_name,a.qty,a.agreed_price')
			->from('customer_quotation_detail a')
			->join('customer_quotation d','a.quotation_id=d.id')
			->join('order_punch b','b.quotation_id=a.quotation_id')
			->join('presto_instruments c','a.product_id=c.id')
			->join('customer_detail e','e.id=d.customer_id')
			->where('b.billing', 1)
			->where('b.cancelled',0)
			->where('b.billed_On>=',$startdate)
			->where('b.billed_On<=',$enddate)
			->where('a.product_id',$prd)
			->where('d.customer_id',$cust)
			->get();


		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$data[]=$row->qty;
			$data1[]=$row->qty*$row->agreed_price;
		}
	}

	return array_sum($data)."|".array_sum($data1);
}

function product_sale_by_customer()
{


		$product=$this->input->post('product');
		$startdate=date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate=date('Y-m-d',strtotime($this->input->post('enddate')));

	
		redirect(page_url.'Sales_stats_reporting/customer_wise_product_consumption/'.$product.'/'.$startdate.'/'.$enddate);

}


function get_customer_wise_product_sales_data_tabular()
	{
		
		$html='<table class="table table-bordered" style="text-align:center">
		<thead>
		<tr>

		<th scope="col" style="text-align:center">SR NO</th>
		<th scope="col" style="text-align:center">CUSTOMER NAME</th>
				<th scope="col" style="text-align:center">SALE QTY</th>
		<th scope="col" style="text-align:center">SALE AMOUNT (in ₹)</th>

		</tr>
		</thead>
		<tbody>';
		
		$startdate = date('Y-m-d',strtotime($this->uri->segment(4)))." 00:00:00";
		$enddate = date('Y-m-d',strtotime($this->uri->segment(5)))." 23:59:59";
		$prd=$this->uri->segment(3);
		
		

	$query = $this->db->select('c.instruments_name,d.customer_id,e.company_name,c.unit')
			->from('customer_quotation_detail a')
			->join('customer_quotation d','a.quotation_id=d.id')
			->join('order_punch b','b.quotation_id=a.quotation_id')
			->join('presto_instruments c','a.product_id=c.id')
			->join('customer_detail e','e.id=d.customer_id')
			->where('b.billing', 1)
			->where('b.cancelled',0)
			->where('b.billed_On>=',$startdate)
			->where('b.billed_On<=',$enddate)
			->where('a.product_id',$prd)
			->group_by('d.customer_id')
			->get();


		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{

			$res=$this->get_customer_wise_consumption($prd,$startdate,$enddate,$row->customer_id);
			$rest=explode('|',$res);

			$qty_sum=$rest[0];
			$sale_sum=$rest[1];
		
				$output[] = array(
					'product_name'=>$row->instruments_name,
					'indus'=>$row->company_name,
					'unit'=>$row->unit,
					'sale_qty'=>$qty_sum,
					'sale_amt'=>$sale_sum,
					);
		}


		array_multisort(array_column($output, 'sale_qty'), SORT_DESC, $output);
		}
	
		
		if(count($output)>0)
		{
			for($i=0;$i<count($output);$i++)
			{
				$k=$i+1;
				$html.='<tr>
                          <td>'.$k.'</td>
                          <td>'.$output[$i]['indus'].'</td>
                          <td style="color:red;font-weight:bold;">'.$output[$i]['sale_qty'].' '.$output[$i]['unit'].'</td>
                          <td style="color:red;font-weight:bold;">₹'.$output[$i]['sale_amt'].'</td>
                        </tr>';

			}
		}else
		{
			$html.='<tr>
                          <td colspan="4" class="text-align:center">No Data Available</td>
                       
                        </tr>';
		}	
		

		$html.="</tbody></table>";
	
		echo $html;

	}

	function customer_wise_product_consumption_filter()
		{
		$type=$this->input->post('product');
		$startdate=date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate=date('Y-m-d',strtotime($this->input->post('enddate')));
		redirect(page_url.'Sales_stats_reporting/customer_wise_product_consumption/'.$type.'/'.$startdate.'/'.$enddate);
		}

		function product_comparision_report()
		{
			$this->load->view('sales_reporting/product_comparision');
		}

		function filter_product_comparision()
		{
			$product=$this->input->post('product');
			$startdate1=date('Y-m-d',strtotime($this->input->post('startdate1')));
			$enddate1=date('Y-m-d',strtotime($this->input->post('enddate1')));
			$startdate2=date('Y-m-d',strtotime($this->input->post('startdate2')));
			$enddate2=date('Y-m-d',strtotime($this->input->post('enddate2')));

			redirect(page_url.'Sales_stats_reporting/product_comparision_report/'.$product.'/'.$startdate1.'/'.$enddate1.'/'.$startdate2.'/'.$enddate2);
		}

		function get_customers_added()
		{

			$prd=$this->uri->segment(3);
			$prdname=$this->getproductnames($prd);
			$startdate1=$this->uri->segment(4);
			$enddate1=$this->uri->segment(5);
			$startdate2=$this->uri->segment(6);
			$enddate2=$this->uri->segment(7);

			/** GET FIRST DATA **/

				$customer1=array();
				$query = $this->db->select('d.customer_id,e.company_name,c.unit,c.instruments_name')
				->from('customer_quotation_detail a')
				->join('customer_quotation d','a.quotation_id=d.id')
				->join('order_punch b','b.quotation_id=a.quotation_id')
				->join('presto_instruments c','a.product_id=c.id')
				->join('customer_detail e','e.id=d.customer_id')
				->where('b.billing', 1)
				->where('b.cancelled',0)
				->where('b.billed_On>=',$startdate1)
				->where('b.billed_On<=',$enddate1)
				->where('a.product_id',$prd)
				->group_by('d.customer_id')
				->get();


				$res = $query->result();
				$i=1;
				if($query->num_rows()>0)
				{
				foreach($res as $row)
				{
					$customer1[]=$row->customer_id;
					$product_name=$row->instruments_name;
					$unit=$row->unit;
				}

				}



				$customer2=array();
				$query = $this->db->select('d.customer_id,e.company_name,c.unit,c.instruments_name')
				->from('customer_quotation_detail a')
				->join('customer_quotation d','a.quotation_id=d.id')
				->join('order_punch b','b.quotation_id=a.quotation_id')
				->join('presto_instruments c','a.product_id=c.id')
				->join('customer_detail e','e.id=d.customer_id')
				->where('b.billing', 1)
				->where('b.cancelled',0)
				->where('b.billed_On>=',$startdate2)
				->where('b.billed_On<=',$enddate2)
				->where('a.product_id',$prd)
				->group_by('d.customer_id')
				->get();


				$res = $query->result();
				$i=1;
				if($query->num_rows()>0)
				{
				foreach($res as $row)
				{
					$customer2[]=$row->customer_id;
					$product_name=$row->instruments_name;
					$unit=$row->unit;
				}

				}

				
				$html='';
				$html='<table class="table table-bordered" style="text-align:center">
				<thead>

				<tr>
				<th colspan="5" style="text-align:center">New Customer Added For Product '.$product_name.'<br/> From '.date('d-M-Y',strtotime($startdate1)).'-'.date('d-M-Y',strtotime($enddate2)).'<br/> To '.date('d-M-Y',strtotime($startdate2)).'-'.date('d-M-Y',strtotime($startdate2)).'</th>
				</tr>

				<tr style="position: sticky; top: -12px; background: #e9e8e8;">
				<th scope="col" style="text-align:center">Sr. No.</th>
				<th scope="col" style="text-align:center">Product</th>
				<th scope="col" style="text-align:center">Company</th>
				<th scope="col" style="text-align:center">Qty Purchased</th>
				<th scope="col" style="text-align:center">Purchase Amount</th>
				</tr>
				</thead>
				<tbody>';
				$customeradded = array_diff($customer2, $customer1);
		
				if(count($customeradded)>0)
				{

				

					$one=1;
					$extra_qty=array();
					$extra_qty[]=0;
					$extra_price=array();
					$extra_price[]=0;
					foreach($customeradded as $newcustomers)
					{
							$customer_name=$this->getcustomer_detail($newcustomers);
						  $qty_price=$this->get_product_qty_and_price($prd,$startdate2,$enddate2,$newcustomers);
						  $d=explode('|',$qty_price);
							$html.='<tr>
							<td>'.$one.'</td>
							<td>'.$prdname.'</td>
							<td>'.$customer_name.'</td>
							<td style="color:red;font-weight:bold;">'.$d[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">₹ '.$d[1].'</td>
							</tr>';
							$extra_qty[]=$d[0];
							$extra_price[]=$d[1];
					$one++;
					}
				
					$html.='<tr style="background-color:#585050; position: sticky;
					bottom: -10px; z-index: 4;">
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">'.array_sum($extra_qty).' '.$unit.'</td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">₹ '.array_sum($extra_price).'</td>

					</tr>';

				}else
				{
						$html.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="4" class="text-align:center">No Data Available</td>

						</tr>';
				}	

				$html.="</tbody></table>";


				$html1='';
					$html1='<table class="table table-bordered" style="text-align:center">
				<thead>

				<tr>
				<th colspan="5" style="text-align:center">Customer Reduced For Product '.$product_name.'<br/> From '.date('d-M-Y',strtotime($startdate1)).'-'.date('d-M-Y',strtotime($enddate2)).'<br/> To '.date('d-M-Y',strtotime($startdate2)).'-'.date('d-M-Y',strtotime($startdate2)).'</th>
				</tr>

				<tr style="position: sticky; top: -12px; background: #e9e8e8;">
				<th scope="col" style="text-align:center">Sr. No.</th>
				<th scope="col" style="text-align:center">Product</th>
				<th scope="col" style="text-align:center">Company</th>
					<th scope="col" style="text-align:center">Previous Qty Purchased</th>
				<th scope="col" style="text-align:center">Previous Purchase Amount</th>
				</tr>
				</thead>
				<tbody>';
				$customeradded = array_diff($customer1,$customer2);
				if(count($customeradded)>0)
				{

			

					$one=1;
					$extra_qty=array();
					$extra_qty[]=0;
					$extra_price=array();
					$extra_price[]=0;
					foreach($customeradded as $newcustomers)
					{
							$customer_name=$this->getcustomer_detail($newcustomers);
						  $qty_price=$this->get_product_qty_and_price($prd,$startdate1,$enddate1,$newcustomers);
						  $d=explode('|',$qty_price);
							$html1.='<tr>
							<td>'.$one.'</td>
							<td>'.$prdname.'</td>
							<td>'.$customer_name.'</td>
							<td style="color:red;font-weight:bold;">'.$d[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">₹ '.$d[1].'</td>
							</tr>';
							$extra_qty[]=$d[0];
							$extra_price[]=$d[1];
					$one++;
					}
				
					$html1.='<tr style="background-color:#585050; position: sticky;
					bottom: -10px; z-index: 4;">
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">'.array_sum($extra_qty).' '.$unit.'</td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">₹ '.array_sum($extra_price).'</td>

					</tr>';

				}else
				{
						$html1.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
				}	

				$html1.="</tbody></table>";


				$html2='<table class="table table-bordered" style="text-align:center">
				<thead>

				<tr>
				<th colspan="5" style="text-align:center">Stable Customer For Product '.$product_name.'<br/> From '.date('d-M-Y',strtotime($startdate1)).'-'.date('d-M-Y',strtotime($enddate2)).'<br/> To '.date('d-M-Y',strtotime($startdate2)).'-'.date('d-M-Y',strtotime($startdate2)).'</th>
				</tr>

				<tr style="position: sticky; top: -12px; background: #e9e8e8;">
				<th scope="col" style="text-align:center">Sr No.</th>
				<th scope="col" style="text-align:center">Product</th>
				<th scope="col" style="text-align:center">Company</th>
					<th scope="col" style="text-align:center">Qty Purchased</th>
				<th scope="col" style="text-align:center">Purchase Amount</th>
				</tr>
				</thead>
				<tbody>';
				$customeradded=array_intersect($customer1,$customer2);
				if(count($customeradded)>0)
				{

				

					$one=1;
					$extra_qty=array();
					$extra_qty[]=0;
					$extra_price=array();
					$extra_price[]=0;
					foreach($customeradded as $newcustomers)
					{
							$customer_name=$this->getcustomer_detail($newcustomers);
						  $qty_price=$this->get_product_qty_and_price($prd,$startdate2,$enddate2,$newcustomers);
						  $d=explode('|',$qty_price);
							$html2.='<tr>
							<td>'.$one.'</td>
							<td>'.$prdname.'</td>
							<td>'.$customer_name.'</td>
							<td style="color:red;font-weight:bold;">'.$d[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">₹ '.$d[1].'</td>
							</tr>';
							$extra_qty[]=$d[0];
							$extra_price[]=$d[1];
					$one++;
					}
				
					$html2.='<tr style="background-color:#585050; position: sticky;
					bottom: -10px; z-index: 4;">
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">'.array_sum($extra_qty).' '.$unit.'</td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">₹ '.array_sum($extra_price).'</td>

					</tr>';

				}else
				{
						$html2.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;"> 
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
				}	

				$html2.="</tbody></table>";



				$html3='<table class="table table-bordered" style="text-align:center">
				<thead>

				<tr>
				<th colspan="5" style="text-align:center">Customer With Qty Decreased For Product '.$product_name.'<br/> From '.date('d-M-Y',strtotime($startdate1)).'-'.date('d-M-Y',strtotime($enddate2)).'<br/> To '.date('d-M-Y',strtotime($startdate2)).'-'.date('d-M-Y',strtotime($startdate2)).'</th>
				</tr>

				<tr style="position: sticky; top: -12px; background: #e9e8e8;">
				<th scope="col" style="text-align:center">Sr No.</th>
				<th scope="col" style="text-align:center">Product</th>
				<th scope="col" style="text-align:center">Company</th>
				<th scope="col" style="text-align:center">Current Purchased Qty</th>
				<th scope="col" style="text-align:center">Previous Purchased Qty</th>
				<th scope="col" style="text-align:center">Diff.</th>
		
				</tr>
				</thead>
				<tbody>';
				$customeradded=array_intersect($customer1,$customer2);
				if(count($customeradded)>0)
				{

				

					$one=1;
					$d=0;
					$extra_qty=array();
					$extra_qty[]=0;
					$extra_price=array();
					$extra_price[]=0;
					foreach($customeradded as $newcustomers)
					{
							$customer_name=$this->getcustomer_detail($newcustomers);
						  $qty_price=$this->get_product_qty_and_price($prd,$startdate2,$enddate2,$newcustomers);
						  $qty_price_previous=$this->get_product_qty_and_price($prd,$startdate1,$enddate1,$newcustomers);
						  $d=explode('|',$qty_price);

						  $d1=explode('|',$qty_price_previous);
						   // echo "<pre>"; print_r($d)."<pre>"; print_r($d1); exit;

						  if($d[0]<$d1[0])
						  {
						  	$diff=$d1[0]-$d[1];
							$html3.='<tr>
							<td>'.$one.'</td>
							<td>'.$prdname.'</td>
							<td>'.$customer_name.'</td>
							<td style="color:red;font-weight:bold;">'.$d[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">'.$d1[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">'.$diff.' '.$unit.'</td>
							
							</tr>';
							$extra_qty[]=$diff;
							$d=1;
							}
							
					$one++;
					}
				
					if($d==1)
					{
					$html3.='<tr style="background-color:#585050; position: sticky;
					bottom: -10px; z-index: 4;">
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">'.array_sum($extra_qty).' '.$unit.'</td>
					</tr>';
					}else
					{
						$html3.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
					}

				}else
				{
						$html3.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
				}	

				$html3.="</tbody></table>";
				


				$html4='<table class="table table-bordered" style="text-align:center">
				<thead>

				<tr>
				<th colspan="5" style="text-align:center">Customer With Qty Increased For Product '.$product_name.'<br/> From '.date('d-M-Y',strtotime($startdate1)).'-'.date('d-M-Y',strtotime($enddate2)).'<br/> To '.date('d-M-Y',strtotime($startdate2)).'-'.date('d-M-Y',strtotime($startdate2)).'</th>
				</tr>

				<tr style="position: sticky; top: -12px; background: #e9e8e8;">
				<th scope="col" style="text-align:center">Sr No.</th>
				<th scope="col" style="text-align:center">Product</th>
				<th scope="col" style="text-align:center">Company</th>
				<th scope="col" style="text-align:center">Current Purchased Qty</th>
				<th scope="col" style="text-align:center">Previous Purchased Qty</th>
				<th scope="col" style="text-align:center">Diff.</th>
		
				</tr>
				</thead>
				<tbody>';
				$customeradded=array_intersect($customer1,$customer2);
				if(count($customeradded)>0)
				{

				

					$one=1;
					$d=0;
					$extra_qty=array();
					$extra_qty[]=0;
					$extra_price=array();
					$extra_price[]=0;
					foreach($customeradded as $newcustomers)
					{
							$customer_name=$this->getcustomer_detail($newcustomers);
						  $qty_price=$this->get_product_qty_and_price($prd,$startdate2,$enddate2,$newcustomers);
						  $qty_price_previous=$this->get_product_qty_and_price($prd,$startdate1,$enddate1,$newcustomers);
						  $d=explode('|',$qty_price);

						  $d1=explode('|',$qty_price_previous);
						   // echo "<pre>"; print_r($d)."<pre>"; print_r($d1); exit;

						  if($d[0]>$d1[0])
						  {
						  $diff=$d[1]-$d1[0];
							$html4.='<tr>
							<td>'.$one.'</td>
							<td>'.$prdname.'</td>
							<td>'.$customer_name.'</td>
							<td style="color:red;font-weight:bold;">'.$d[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">'.$d1[0].' '.$unit.'</td>
							<td style="color:red;font-weight:bold;">'.$diff.' '.$unit.'</td>
							
							</tr>';
							$extra_qty[]=$diff;
							$d3=1;
							}
							
					$one++;
					}
				
					if($d3==1)
					{
					$html4.='<tr style="background-color:#585050; position: sticky;
					bottom: -10px; z-index: 4;">
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;"></td>
					<td  style="text-align:center;font-weight:bold;color:#fff;">'.array_sum($extra_qty).' '.$unit.'</td>
					</tr>';
					}else
					{
						$html4.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
					}

				}else
				{
						$html4.='<tr style="background-color:#585050; position: sticky;
						bottom: -10px; z-index: 4; color: white;">
						<td colspan="5" class="text-align:center">No Data Available</td>

						</tr>';
				}	

				$html4.="</tbody></table>";


				echo $html."|".$html1."|".$html2."|".$html3."|".$html4;

		}


		



		function get_product_qty_and_price($prd,$startdate2,$enddate2,$customer)
		{

				$qty=array();
				$qty[]=0;
				$price=array();
				$price[]=0;
				$query = $this->db->select('a.qty,a.agreed_price')
				->from('customer_quotation_detail a')
				->join('customer_quotation d','a.quotation_id=d.id')
				->join('order_punch b','b.quotation_id=a.quotation_id')
				->join('presto_instruments c','a.product_id=c.id')
				->join('customer_detail e','e.id=d.customer_id')
				->where('b.billing', 1)
				->where('b.cancelled',0)
				->where('b.billed_On>=',$startdate2)
				->where('b.billed_On<=',$enddate2)
				->where('a.product_id',$prd)
				->where('d.customer_id',$customer)
				->get();
				if($query->num_rows()>0)
				{

					foreach($query->result() as $row)
					{
						$qty[]=$row->qty;
						$price[]=$row->qty*$row->agreed_price;
					}

				}

				return array_sum($qty)."|".array_sum($price);

		}

		function getcustomer_detail($customer_id)
		{
			$comp='';
			$rest=$this->db->select('company_name')->from('customer_detail')->where('id',$customer_id)->get();
			if($rest->num_rows()>0)
			{ 
				foreach($rest->result() as $row)
				$comp=$row->company_name;

			}

			return $comp;

		}

		function getproduct_timeline()
		{	

			$cusid=$this->uri->segment(3);

			$curr=date('m');

			$product_qty=array();
			$product_qty[]=0;
			for ($i = 1; $i < 6; $i++) {

			$startdate=date('Y-m-01',strtotime("-$i month"))." 00:00:00";
			$enddatedate=date('Y-m-t',strtotime("-$i month"))." 23:59:59";

			$rest=$this->db->select('b.id,b.customer_id,a.product_id,a.qty')->from('customer_quotation_detail a')->join('customer_quotation b','a.quotation_id=b.id')->where('b.customer_id',$cusid)->where('b.added_on>=',$startdate)->where('b.added_on<=',$enddatedate)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				{

				$product_qty[]=$row->product_id;
				$product_qty[]=$row->qty;

				}



			}




			}

		//	$this->db->select('')


		}

		function getproductnames($prd)
		{

			$prdname='';

		$rest=$this->db->select('instruments_name,pack_size,model_number')->from('presto_instruments')->where('id',$prd)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$prdname=$row->instruments_name."-".$row->pack_size."-".$row->model_number;
		}

		return $prdname;

		}


		function get_sales_qty($startdate,$enddate)
	{

		$sale_price=array();
		$sale_price[]=0;
		$startdate=$startdate." 00:00:00";
		$enddate=$enddate." 23:59:59";
		
		$query = $this->db->select('a.quotation_id as orderpunchquote')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.billing', 1)
						  ->where('a.cancelled',0)
						  ->where('a.billed_On>=',$startdate)
						  ->where('a.billed_On<=',$enddate)
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows()>0)
		{
		foreach($res as $row)
		{
			$sale_price[]=$this->getproducts_detail_by_qty($row->orderpunchquote);
		}
		}

		return array_sum($sale_price);
	}


		function getproducts_detail_by_qty($quotation)
	{
			$price=array();
			$price[]=0;
			$res=$this->db->select('a.agreed_price,a.qty')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			
			$price[]=round($product->qty);

			$j++;
			}
		
			}

			return array_sum($price);
	}

	function month_wise_comparision()
	{
		$compare=$this->input->post('compare');

$url=page_url.'Sales_stats_reporting/monthlycomparision/'.$compare[0].'/'.$compare[1];
	
	echo "<script type='text/javascript'>
	window.open('".$url."', '_blank');
	</script>";


		//redirect(page_url.'Sales_stats_reporting/monthlycomparision/'.$compare[0].'/'.$compare[1]);
	}

	function monthlycomparision() 
	{
		$this->load->view('sales_reporting/monthly_comparision_grade_wise');
	}

	function grade_wise_sale()
	{

		$i=1;
		$startmonth_date1=date('Y-m-01',strtotime($this->uri->segment(3)))." 00:00:00";
		$startmonth_date2=date('Y-m-t',strtotime($this->uri->segment(3)))." 23:59:59";
		$endmonth_date1=date('Y-m-01',strtotime($this->uri->segment(4)))." 00:00:00";
		$endmonth_date2=date('Y-m-t',strtotime($this->uri->segment(4)))." 23:59:59";


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_product_in_this_month($startmonth_date1,$startmonth_date2);
		$month2=$this->get_product_in_this_month($endmonth_date1,$endmonth_date2);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty($startmonth_date1,$startmonth_date2,$unique_prd[$i]);
		$sale_qty2=$this->get_sale_qty($endmonth_date1,$endmonth_date2,$unique_prd[$i]);
		$prd=$this->getproduct_detail($unique_prd[$i]);
		if($prd<>'')
		{
			$p=explode('|',$prd);
			$prdn=$p[0];
			$u=$p[1];
		}else
		{
			$prdn='';
			$u='';
		}

		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>"<a href='".page_url."Sales_stats_reporting/customer_comparision/1/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$unique_prd[$i]."'>".$prdn."</a>",
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}

	function get_product_in_this_month($date1,$date2)
	{
		$prd=array();
		 $query = $this->db->select('a.product_id')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->group_by('a.product_id')
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->product_id;
	                      	}

	                      }


	          return $prd;

	}

	function getproduct_detail($prd){

		$pr='';
		$trestey=$this->db->select('instruments_name,model_number,pack_size,unit')->from('presto_instruments')->where('id',$prd)->get();
		if($trestey->num_rows()>0)
		{
			foreach($trestey->result() as $roww);

			$pr=$roww->instruments_name."-".$roww->pack_size."-".$roww->model_number;
			$unit=$roww->unit;
			$pr=$pr."|".$unit;
		}

		return $pr;
	}

	function get_sale_qty($date1,$date2,$prd111)
	{

		$prd=array();
		$prd[]=0;
		 $query = $this->db->select('a.qty')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('a.product_id',$prd111)
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->qty;
	                      	}

	                      }


	          return array_sum($prd);

	}

	function customer_comparision()
	{
		$this->load->view('sales_reporting/monthly_comparision_customer_wise');
	}


	function customer_wise_sale()
	{

		$i=1;
		$startmonth_date1=date('Y-m-01',strtotime($this->uri->segment(4)))." 00:00:00";
		$startmonth_date2=date('Y-m-t',strtotime($this->uri->segment(4)))." 23:59:59";
		$endmonth_date1=date('Y-m-01',strtotime($this->uri->segment(5)))." 00:00:00";
		$endmonth_date2=date('Y-m-t',strtotime($this->uri->segment(5)))." 23:59:59";
		$prd=$this->uri->segment(6);


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_customer_in_this_month($startmonth_date1,$startmonth_date2,$prd);
		$month2=$this->get_customer_in_this_month($endmonth_date1,$endmonth_date2,$prd);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty_customer_wise($startmonth_date1,$startmonth_date2,$unique_prd[$i],$prd);
		$sale_qty2=$this->get_sale_qty_customer_wise($endmonth_date1,$endmonth_date2,$unique_prd[$i],$prd);
		$customer=$this->getcustomer_detail_from_db($unique_prd[$i]);
		$prddata=$this->getproduct_detail($prd);
		if($prddata<>'')
		{

			$u=explode("|",$prddata);
			$u=$u[1];
		}else
		{
			$u='';
		}
		
		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>$customer,
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}


	function get_customer_in_this_month($date1,$date2,$prd111)
	{
		$prd=array();
		 $query = $this->db->select('c.customer_id')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('a.product_id',$prd111)
													->group_by('c.customer_id')
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->customer_id;
	                      	}

	                      }


	          return $prd;

	}

	function getcustomer_detail_from_db($prd){

		$pr='';
		$trestey=$this->db->select('company_name')->from('customer_detail')->where('id',$prd)->get();
		if($trestey->num_rows()>0)
		{
			foreach($trestey->result() as $roww);

			$pr=$roww->company_name;
		
			$pr=$pr;
		}

		return $pr;
	}


	function get_sale_qty_customer_wise($date1,$date2,$prd111,$prd4343)
	{

		$prd=array();
		$prd[]=0;
		 $query = $this->db->select('a.qty')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('c.customer_id',$prd111)
													->where('a.product_id',$prd4343)
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->qty;
	                      	}

	                      }


	          return array_sum($prd);

	}


	function year_wise_comparision()
	{
		$compare=$this->input->post('compare');
		$startyear=explode('|',$compare[0]);
		//echo "<pre>";print_r($startyear); exit;
		$endyear=explode('|',$compare[1]);


$url=page_url.'Sales_stats_reporting/yearlycomparision/'.$startyear[0].'/'.$startyear[1].'/'.$endyear[0].'/'.$endyear[1];
	
	echo "<script type='text/javascript'>
	window.open('".$url."', '_blank');
	</script>";


		//redirect(page_url.'Sales_stats_reporting/monthlycomparision/'.$compare[0].'/'.$compare[1]);
	}

	function yearlycomparision() 
	{
		$this->load->view('sales_reporting/yearly_comparision_grade_wise');
	}


	function grade_wise_sale_yearly()
	{

		$i=1;
		$startmonth_date1=date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$startmonth_date2=date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$endmonth_date1=date('Y-m-d',strtotime($this->uri->segment(5)))." 00:00:00";
		$endmonth_date2=date('Y-m-d',strtotime($this->uri->segment(6)))." 23:59:59";


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_product_in_this_month($startmonth_date1,$startmonth_date2);
		$month2=$this->get_product_in_this_month($endmonth_date1,$endmonth_date2);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty($startmonth_date1,$startmonth_date2,$unique_prd[$i]);
		$sale_qty2=$this->get_sale_qty($endmonth_date1,$endmonth_date2,$unique_prd[$i]);
		$prd=$this->getproduct_detail($unique_prd[$i]);
		if($prd<>'')
		{
			$p=explode('|',$prd);
			$prdn=$p[0];
			$u=$p[1];
		}else
		{
			$prdn='';
			$u='';
		}

		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>"<a href='".page_url."Sales_stats_reporting/customer_comparision_yearly/2/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6)."/".$unique_prd[$i]."'>".$prdn."</a>",
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}


		function customer_comparision_yearly()
	{
		$this->load->view('sales_reporting/yearly_comparision_customer_wise');
	}


	function customer_wise_sale_yearly()
	{

		$i=1;
		$startmonth_date1=date('Y-m-d',strtotime($this->uri->segment(4)))." 00:00:00";
		$startmonth_date2=date('Y-m-d',strtotime($this->uri->segment(5)))." 23:59:59";
		$endmonth_date1=date('Y-m-d',strtotime($this->uri->segment(6)))." 00:00:00";
		$endmonth_date2=date('Y-m-d',strtotime($this->uri->segment(7)))." 23:59:59";
		$prd=$this->uri->segment(8);


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_customer_in_this_month($startmonth_date1,$startmonth_date2,$prd);
		$month2=$this->get_customer_in_this_month($endmonth_date1,$endmonth_date2,$prd);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty_customer_wise($startmonth_date1,$startmonth_date2,$unique_prd[$i],$prd);
		$sale_qty2=$this->get_sale_qty_customer_wise($endmonth_date1,$endmonth_date2,$unique_prd[$i],$prd);
		$customer=$this->getcustomer_detail_from_db($unique_prd[$i]);
		$prddata=$this->getproduct_detail($prd);
		if($prddata<>'')
		{

			$u=explode("|",$prddata);
			$u=$u[1];
		}else
		{
			$u='';
		}
		
		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>$customer,
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}

	function salesfunnel()
	{
		$this->load->view('sales_reporting/lead_funnel');
	}

	function filter_salesfunnel()
	{
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		redirect(page_url.'Sales_stats_reporting/salesfunnel/'.$from_date.'/'.$to_date);
	}

	function salesperformance()
	{
		$this->load->view('sales_reporting/salesperformance');
	}

	function filter_salesperformance()
	{
		// $from_date=$this->input->post('from_date');
		$from_date=$this->input->post('to_date');
		$to_date=$this->input->post('to_date');
		$user=$this->input->post('user');
		redirect(page_url.'Sales_stats_reporting/salesperformance/'.$from_date.'/'.$to_date.'/'.$user);
	}

	function typewisereport()
	{
		$this->load->view('sales_reporting/estimated_type_report');
	}

	function set_status()
	{
		$status=$this->input->post('status');
		$remarks=$this->input->post('remarks');
		$period=$this->input->post('period');
		$resty=$this->db->select('id')->from('type_reports')->where('period',$period)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row);
			$periodid=$row->id;
		}else
		{
			$d=array('period'=>$period,'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('type_reports',$d);
			$periodid=$this->db->insert_id();
		}

			$data=array('period_id'=>$periodid,'period'=>$period,'status'=>$status,'remarks'=>$remarks,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('type_reports_progress',$data);

			/** UPDATE LATEST STATUS **/
			$DDD=array('current_status'=>$status);
			$this->db->where('id',$periodid);
			$this->db->update('type_reports',$DDD);
			/** END **/

			$this->session->set_flashdata('message','<div class="alert alert-success">Record Added.</div>');
			redirect(page_url.'Sales_stats_reporting/typewisereport');


	}	

	function history()
	{
		$this->load->view('sales_reporting/type_history');
	}

	function history_list()
	{
		$data = array();
		$i=1;
		$period =$this->uri->segment(3);
		$type =$this->uri->segment(4);
		$this->db->select('a.*,b.first_name,b.last_name,c.typestatus')
	->from('type_reports_progress a')
	->join('system_users b','a.addedBy=b.user_id')
	->join('typestatus c','a.status=c.id')
	->where('a.period',$period)
	->where('a.type',$type)
	->order_by('a.id','DESC');
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
					$data[] = array(
							'sr_no' => $i,
							'period' => date('M-Y',strtotime($this->uri->segment(3))),
							'status' =>$row->typestatus,
							'remarks' =>$row->remarks,
							'by'=>$row->first_name." ".$row->last_name,
							'on'=>date('d-M-Y H:i',strtotime($row->addedOn)));

					$i++;
				
				
				}
			}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);

	}

	function margin_sheet()
	{
		$this->load->view('sales_reporting/margin_sheet');
	}


		function filter_margin_sheet()
	{
		$from_date=date('Y-m-01',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-t',strtotime($this->input->post('to_date')));
		$user=$this->input->post('user');
		redirect(page_url.'Sales_stats_reporting/margin_sheet/'.$from_date.'/'.$to_date.'/'.$user);
	}

	function set_final_status()
	{
		$status=$this->input->post('status');
		$remarks=$this->input->post('remarks');
		$period=$this->input->post('period');
		$d=array('period'=>$period,'addedOn'=>date('Y-m-d H:i:s'));
		$this->db->insert('type_report_final',$d);




			$this->session->set_flashdata('message','<div class="alert alert-success">Record Added.</div>');
			redirect(page_url.'Sales_stats_reporting/typewisereport');
		
	}

	function set_estimate_status()
	{

		$id=$this->input->post('id');
		$period=$this->input->post('period');

		$reste=$this->db->select('id')->from('type_report_final')->where('period',$period)->where('type',$id)->get();
		if($reste->num_rows()==0)
		{
			$d=array('period'=>$period,'type'=>$id,'current_status'=>1,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('type_report_final',$d);
		}

		$reste=$this->db->select('id')->from('type_reports')->where('period',$period)->where('type',$id)->get();
		if($reste->num_rows()==0)
		{
			/** ADD DATA TO OPEN STATUS **/
		$dd=array('type'=>$id,'period'=>$period,'current_status'=>1,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->insert('type_reports',$dd);
		/** END **/
		}


		return true;


	}

	function revert_data()
	{
		$id=$this->uri->segment(3);
		$period=$this->uri->segment(4);
		$this->db->where('period',$period);
		$this->db->where('type',$id);
		$this->db->delete('type_report_final');

		/** ADD DATA TO OPEN STATUS **/
		$this->db->where('type',$id);
		$this->db->where('period',$period);
		$this->db->delete('type_reports');
		/** END **/

		$this->session->set_flashdata('message','<div class="alert alert-success">Reverted.</div>');
		redirect(page_url.'Sales_stats_reporting/typewisereport/');	
	}

		function finaltypewisereport()
	{
		$this->load->view('sales_reporting/type_report');
	}


	function add_remarks()
{
	
		$period=$this->uri->segment(3);
		$type=$this->uri->segment(4);
		$period_id=$this->uri->segment(5);

		$status=$this->input->post('status'.$period.$type);
		$remarks=$this->input->post('remarks'.$period.$type);


		$data=array('period_id'=>$period_id,'period'=>$period,'status'=>$status,'remarks'=>$remarks,'type'=>$type,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('type_reports_progress',$data);

			/** UPDATE LATEST STATUS **/
			$DDD=array('current_status'=>$status);
			$this->db->where('id',$period_id);
			$this->db->update('type_report_final',$DDD);
			/** END **/

			$price=$this->check_for_approval_non_ajax($status);
			if($price==1)
			{
			$finalcost=$this->input->post('finalcost'.$period.$type);
			$DDD=array('final_price'=>$finalcost);
			$this->db->where('id',$period_id);
			$this->db->update('type_report_final',$DDD);
			}

			$paid=$this->check_for_payment_non_ajax($status);
			if($paid==1)
			{			
				$DDD=array('payment'=>1);
				$this->db->where('id',$period_id);
				$this->db->update('type_report_final',$DDD);
			}




			$this->session->set_flashdata('message','<div class="alert alert-success">Record Added.</div>');
			redirect(page_url.'Sales_stats_reporting/finaltypewisereport/');


}

function check_for_approval()
{
$status=$this->input->post('status');
$restey=$this->db->select('id')->from('typestatus')->where('approved',1)->where('id',$status)->get();
echo $restey->num_rows();

}


function check_for_approval_non_ajax($status)
{

$restey=$this->db->select('id')->from('typestatus')->where('approved',1)->where('id',$status)->get();
return $restey->num_rows();

}

function check_for_payment_non_ajax($status)
{
	$restey=$this->db->select('id')->from('typestatus')->where('final',1)->where('id',$status)->get();
return $restey->num_rows();

}

function paid_type_report()
{
	$this->load->view('sales_reporting/type_report_paid');
}

function customer_qty_comparision()
	{
		$this->load->view('sales_reporting/customer_wise_qty_comparision');
	}



function customer_qty_wise_sale()
	{

		$i=1;
		$startmonth_date1=date('Y-m-01',strtotime($this->uri->segment(4)))." 00:00:00";
		$startmonth_date2=date('Y-m-t',strtotime($this->uri->segment(4)))." 23:59:59";
		$endmonth_date1=date('Y-m-01',strtotime($this->uri->segment(5)))." 00:00:00";
		$endmonth_date2=date('Y-m-t',strtotime($this->uri->segment(5)))." 23:59:59";
		$prd=$this->uri->segment(6);


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_customer_in_this_month_without_product($startmonth_date1,$startmonth_date2);
		$month2=$this->get_customer_in_this_month_without_product($endmonth_date1,$endmonth_date2);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty_customer_wise_overall($startmonth_date1,$startmonth_date2,$unique_prd[$i]);
		$sale_qty2=$this->get_sale_qty_customer_wise_overall($endmonth_date1,$endmonth_date2,$unique_prd[$i]);
		$customer=$this->getcustomer_detail_from_db($unique_prd[$i]);
		$prddata=$this->getproduct_detail($prd);
		// if($prddata<>'')
		// {

		// 	$u=explode("|",$prddata);
		// 	$u=$u[1];
		// }else
		// {
		$u='LTR';
		//}
		
		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>"<a href='".page_url."Sales_stats_reporting/monthlycomparision_customer_wise/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$unique_prd[$i]."' target='_blank'>".$customer."</a>",
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}



function get_customer_in_this_month_without_product($date1,$date2)
	{
		$prd=array();
		 $query = $this->db->select('c.customer_id')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
												
													->group_by('c.customer_id')
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->customer_id;
	                      	}

	                      }


	          return $prd;

	}



function get_sale_qty_customer_wise_overall($date1,$date2,$prd111)
	{

		$prd=array();
		$prd[]=0;
		 $query = $this->db->select('a.qty')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('c.customer_id',$prd111)
										
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->qty;
	                      	}

	                      }


	          return array_sum($prd);

	}

	function filter_customer_qty()
	{
		$a=array();
		$a[]=0;
		$stdate=date('Y-m-d',strtotime($this->uri->segment(3)));
		$etdate=date('Y-m-d',strtotime($this->uri->segment(4)));
		$date1 = new DateTime($stdate);
		$date2 = new DateTime($etdate);
		$interval = $date1->diff($date2);
		$days=$interval->days;

	
		echo $days."|".$stdate."|".$etdate;



	}


	function filter_customer_qty1()
	{

		$stdate=$this->input->post('stdate');
		$etdate=$this->input->post('etdate');

		redirect(page_url.'Sales_stats_reporting/customer_qty_comparision/1/'.date('Y-m-d',strtotime($stdate)).'/'.date('Y-m-d',strtotime($etdate)));
		

	}

	function monthlycomparision_customer_wise() 
	{
		$this->load->view('sales_reporting/monthly_comparision_grade_customer_wise.php');
	}


	function grade_customer_wise_sale()
	{

		$i=1;
		$startmonth_date1=date('Y-m-01',strtotime($this->uri->segment(3)))." 00:00:00";
		$startmonth_date2=date('Y-m-t',strtotime($this->uri->segment(3)))." 23:59:59";
		$endmonth_date1=date('Y-m-01',strtotime($this->uri->segment(4)))." 00:00:00";
		$endmonth_date2=date('Y-m-t',strtotime($this->uri->segment(4)))." 23:59:59";
		$customer_id=$this->uri->segment(5);


		$vendor_data= array();
		$vendor_data1= array();

		$month1=$this->get_product_in_this_month_by_customer($startmonth_date1,$startmonth_date2,$customer_id);
		$month2=$this->get_product_in_this_month_by_customer($endmonth_date1,$endmonth_date2,$customer_id);

		$unique_prd = array_values(array_unique(array_merge($month1,$month2)));
		

	
		for($i=0;$i<count($unique_prd); $i++){
		
		$sale_qty1=$this->get_sale_qty_customer_wise_for_grade($startmonth_date1,$startmonth_date2,$unique_prd[$i],$customer_id);
		$sale_qty2=$this->get_sale_qty_customer_wise_for_grade($endmonth_date1,$endmonth_date2,$unique_prd[$i],$customer_id);
		$prd=$this->getproduct_detail($unique_prd[$i]);
		if($prd<>'')
		{
			$p=explode('|',$prd);
			$prdn=$p[0];
			$u=$p[1];
		}else
		{
			$prdn='';
			$u='';
		}

		if($sale_qty1>$sale_qty2)
		{
			$diff=$sale_qty1-$sale_qty2;
			$sign="-";
			$re="Decrease";
			$color="red";
			$diff_d=0-$diff;
		}else if($sale_qty1<$sale_qty2)
		{
			$diff=$sale_qty2-$sale_qty1;
			$sign="+";
			$re="Increase";
			$color="green";
			$diff_d=$diff;
		}else
		{
			$diff=0;
			$sign="";
			$re="Same";
			$color="orange";
			$diff_d=0;
		}
			$vendor_data1[] = array('sr_no'=>$i+1,
			'item_name'=>$prdn,
			'startmonth'=>$sale_qty1." ".$u,
			'endmonth'=>$sale_qty2." ".$u,
			'diff'=>"<strong style='color:".$color.";font-weight:bold;font-size:17px;'>".$sign."".$diff." ".$u."</strong>",
			'type'=>$re,
			'diff_d'=>$diff_d
			);

			

	}

	$price = array_column($vendor_data1, 'diff_d');
	array_multisort($price, SORT_ASC, $vendor_data1);

	for($i=0;$i<count($vendor_data1);$i++)
	{
			$vendor_data[]=array(
			'sr_no'=>$i+1,
			'item_name'=>$vendor_data1[$i]['item_name'],
			'startmonth'=>$vendor_data1[$i]['startmonth'],
			'endmonth'=>$vendor_data1[$i]['endmonth'],
			'diff'=>$vendor_data1[$i]['diff'],
			'type'=>$vendor_data1[$i]['type'],
			'diff_d'=>$vendor_data1[$i]['diff_d']
		);

	}


			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($vendor_data),
			"iTotalDisplayRecords" => count($vendor_data),
			"aaData"=>$vendor_data);
			
		echo json_encode($results);
	}


	function get_product_in_this_month_by_customer($date1,$date2,$cust)
	{
		$prd=array();
		 $query = $this->db->select('a.product_id')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('c.customer_id',$cust)
													->group_by('a.product_id')
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->product_id;
	                      	}

	                      }


	          return $prd;

	}



function get_sale_qty_customer_wise_for_grade($date1,$date2,$prd111,$cust)
	{

		$prd=array();
		$prd[]=0;
		 $query = $this->db->select('a.qty')
	                        ->from('customer_quotation_detail a')
	                        ->join('presto_instruments b', 'b.id=a.product_id')
	                        ->join('customer_quotation c', 'c.id=a.quotation_id')
	                        ->join('order_punch d', 'd.quotation_id=c.id')
													->where('d.billing', 1)
													->where('d.cancelled',0)
													->where('d.billed_On>=',$date1)
													->where('d.billed_On<=',$date2)
													->where('a.product_id',$prd111)
													->where('c.customer_id',$cust)
	                        ->get();
	                      if($query->num_rows()>0)
	                      {
	                      	foreach($query->result() as $query1)
	                      	{
	                      		$prd[]=$query1->qty;
	                      	}

	                      }


	          return array_sum($prd);

	}

	function save_target()
	{

			$target=$this->input->post('target');
			$agent=$this->input->post('agent');
			$period=$this->input->post('period');
			$previous_bal=$this->input->post('previous_bal');

			$rest=$this->db->select('id')->from('sales_agent_target')->where('agent',$agent)->where('period',$period)->get();
			if($rest->num_rows()==0)
			{
					$data=array('agent'=>$agent,'period'=>$period,'amount'=>$target,'previous'=>$previous_bal,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					$this->db->insert('sales_agent_target',$data);


			}else
			{
				$data=array('amount'=>$target);
				$this->db->where('agent',$agent);
				$this->db->where('period',$period);
				$this->db->update('sales_agent_target',$data);
			}

	}


}