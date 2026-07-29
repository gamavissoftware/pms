<?php
ini_set('memory_limit','2048M');
ini_set('serialize_precision','-1');
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class PurchaseApi extends CI_Controller {

	function index()
		{
			//echo "hi"; exit;
		}
	

	function getpurchasefromrange()
	{

		$response=array();
		$startdate=date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate=date('Y-m-d',strtotime($this->input->post('enddate')));
		$company=$this->input->post('company');

			$this->db->select('id,currentdate,party,bill_no,payment_type,credit_days,transport_type,hpcl_billing_company')->from('inventory')->where('send_to_tally',1)->where('send_to_tally_On>=',$startdate)->where('send_to_tally<=',$enddate)->where('billed',0);
				if($company>0 && $company<>'')
				{
				$this->db->where('hpcl_billing_company',$company);
				}

				$rest=$this->db->get();
				if($rest->num_rows()>0)
				{
				foreach($rest->result() as $row)
				{

				$inventory_id=$row->id;

				$customer_data=$this->getvendor_detail($row->party);
				if(count($customer_data)>0)
				{
					$customer_ref_no=$customer_data[0];
					$company_name=$customer_data[1];
					$gst=$customer_data[2];
					$address=$customer_data[3];
					$contact_no=$customer_data[4];
					$email=$customer_data[5];
					$payment_term=$customer_data[6];
					$payment_mode=$customer_data[7];
					$contact_person=$customer_data[8];
					$pan=$customer_data[9];

					if($payment_term==2)
					{
						$paymenttype="Cash";
						$credit_day='';

					}else if($payment_term==3)
					{
						$paymenttype="Online Transfer";
						$credit_day='';

					}else if($payment_term==4){

						$paymenttype="PDC";
						$credit_day=$row->credit_days. "Days";
					}else if($payment_term==5)
					{
						$paymenttype="Credit";
						$credit_day=$row->credit_days. "Days";
					}else if($payment_term==6)
					{
						$paymenttype="Advance";
						$credit_day='';
					}else
					{
						$paymenttype='';
						$credit_day='';
					}


					$bill_company_detail=$this->getbilling_company_detail($row->hpcl_billing_company);
					if(count($bill_company_detail)>0)
					{
						$our_gst=$bill_company_detail[0];
						$billing_company_name=$bill_company_detail[1];
					}else
					{
						$our_gst=00;
						$billing_company_name='';
					}


					/** CHECK FOR SALES LEDGER TYPE **/
					$our_gst_area=substr($our_gst,0,2);
					$client_gst_area=substr($gst,0,2);
					if($our_gst_area==$client_gst_area)
					{
						$saletype="LOCAL PURCHASE";
					}else
					{
						$saletype="INTERSTATE PURCHASE";
					}
					/** END **/
		
					$orderdate=date('d-m-Y',strtotime($row->currentdate));
					$note='';

					$approved=$this->check_for_density_approved($inventory_id);

					if($approved==0)
					{
						$pur_value=$this->gettotal_purchase_value($inventory_id);
					$response[]=
					array(
						'our_company'=>$billing_company_name,
						'vendor_code'=>$customer_ref_no,
						'vendor_name'=>$contact_person,
						'vendor_company_name'=>$company_name,
						'vendor_contact_no'=>$contact_no,
						'vendor_email'=>$email,
						'vendot_gst'=>$gst,
						'vendor_bill_no'=>$row->bill_no,
						'purchase_order_no'=>$row->id,
						'vendor_payment_term'=>$paymenttype." ".$credit_day,
						'purchase_date'=>$orderdate,
						'narration'=>$note,
						'saletype'=>$saletype,
						'purchasevalue'=>$pur_value
							);
					}

				}

			}



			}

					$result=json_encode($response); 
					echo $result;


	}
								


	function getbilling_company_detail($companyid)
	{
		$data=array();
		$rest=$this->db->select('companyname,gst')->from('store_rack_location')->where('id',$companyid)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);

			$data[]=$row->gst;
			$data[]=$row->companyname;
		}

		return $data;

	}


	function getstatename($state)
	{
		$states='';
		$rest=$this->db->select('state_name')->from('states')->where('state_id',$state)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);

			$states=$row->state_name;
		}

		return $states;

	}

	function RemoveSpecialChar($str) { 
      
    // Using str_replace() function  
    // to replace the word  
    $res = str_replace( array( '\'', '"' , ';', '<', '>','-','<br>','\r','\t','\n'), ' ', $str); 
      
    // Returning the result  
    return $res; 
    } 


    function getcustomer_detail($quotation_id)
    {
    	$customer=array();
    	$rest=$this->db->select('a.customer_id,b.customer_ref_no,b.company_name,b.gst,b.customer_name,b.contact_no,b.email')->from('customer_quotation a')->join('customer_detail b','a.customer_id=b.id')->where('a.id',$quotation_id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$customer[]=$row->customer_ref_no;
    		$customer[]=$row->company_name;
    		$customer[]=$row->gst;
    		$customer[]=$row->customer_name;
    		$customer[]=$row->contact_no;
    		$customer[]=$row->email;
		}

    	return $customer;


    }


   
  

  	function checkforfreight($orderid)
  	{
  		$fdata='';
  		$rest=$this->db->select('freight,freight_amount')->from('order_punch')->where('invoice_no',$orderid)->where('freight',1)->get();
  		if($rest->num_rows()>0)
  		{
  			foreach($rest->result() as $row);

  			$fdata=$row->freight_amount;
  		}


  		return $fdata;
  	}


  	function truncate_number( $number, $precision = 2) {
    // Zero causes issues, and no need to truncate
    if ( 0 == (int)$number ) {
        return $number;
    }
    // Are we negative?
    $negative = $number / abs($number);
    // Cast the number to a positive to solve rounding
    $number = abs($number);
    // Calculate precision number for dividing / multiplying
    $precision = pow(10, $precision);
    // Run the math, re-applying the negative value to ensure returns correctly negative / positive
    return floor( $number * $precision ) / $precision * $negative;
}




  

   

    function getvendor_detail($id)
    {
    	$customer=array();
    	$restey=$this->db->select('name,code,phone,email,gst,address,payment_terms,payment_mode,contactperson,pan')->from('vendors')->where('id',$id)->get();
    	if($restey->num_rows()>0)
    	{
    		foreach($restey->result() as $row);

    		$customer[]=$row->code;
    		$customer[]=$row->name;
    		$customer[]=$row->gst;
    		$customer[]=$row->address;
    		$customer[]=$row->phone;
    		$customer[]=$row->email;  
    		$customer[]=$row->payment_terms;  
    		$customer[]=$row->payment_mode;  
    		$customer[]=$row->contactperson;   			
    		$customer[]=$row->pan;   			
    	}

    	return $customer;

    }


    function getvendor_purchase_details()
    {

    	$purchase_id=$this->input->post('purchase_id');

    	$response=array();
    	
   		$rest1=$this->db->select('a.bulkproducttype,a.secondproduct,a.product,a.qty,a.pack_size,b.unit,a.rate,b.instruments_name,b.model_number,b.hsncode')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->where('a.inventory_id',$purchase_id)->get();
    		if($rest1->num_rows()>0)
    		{
    			foreach($rest1->result() as $row1)
    			{
					$model=trim($row1->model_number);
					if($row1->bulkproducttype==2)
					{
						$model=trim($this->get_model_number($row1->secondproduct));
					}
					$finalprice=round($row1->qty*$row1->rate,2);
					$finalprice="$finalprice";
					$response[]=array('name'=>$model,'quantity'=>$row1->qty,'purchaseprice'=>$row1->rate,'finalprice'=>$finalprice,'hsn'=>$row1->hsncode,'unit'=>$row1->unit,'group'=>"Sales Item",'gst'=>"18");

    			}
    		}
	
				$result=json_encode($response); 
				echo $result;


    }


     function getPurchase_gst_details()
    {

    	$total_base_amount=array();
    	$total_base_amount[]=0;
    	$response=array();
    	$purchase_id=$this->input->post('purchase_id');
    	
 
		$rest1=$this->db->select('a.product,a.qty,a.pack_size,b.unit,a.rate,b.instruments_name,b.model_number,b.hsncode')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->where('a.inventory_id',$purchase_id)->get();
    		if($rest1->num_rows()>0)
    		{
    			foreach($rest1->result() as $row1)
    			{

    				$finalprice=$row1->qty*$row1->rate;
    				$total_base_amount[]=$finalprice;

    			}
    		}

    // 		 $fdata=$this->checkforfreight($orderid);
    // 		 if($fdata!='')
    // 		 {
    // 		 	$freightamount=$this->truncate_number($fdata);
				// $total_base_amount[]=$freightamount;
    // 		 }

    		 /** CALCULATE GST **/
    		 $gstslab=18;
    		 $cgst=0;
    		 $sgst=0;
    		 $igst=0;
    		 $totalamount=array_sum($total_base_amount);
    		 $gstdata=$this->getVendorGSTDetails($purchase_id);
    		 if($gstdata>0)
    		 {
    		 	if($gstdata==1)
    		 	{
    		 		/** LOCAL **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		$cgst=round($gstamt/2,2);
    		 		$sgst=round($gstamt/2,2);
    		 		$cgst="$cgst";
    		 		$sgst="$sgst";
    		 	}else
    		 	{
    		 		/** IGST **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		 $igst=round($gstamt,2);
    		 		 $igst=(float) $igst;
    		 		
    		 	}


    		 	$totalgst=$sgst+$cgst+$igst;
    		 	$totalgst="$totalgst";
    		 

    		 	$response[]=array(
    		 		'head_cgst'=>'INPUT CGST @9%',
    		 		'value_cgst'=>$cgst,
    		 		'head_sgst'=>'INPUT SGST @9%',
    		 		'value_sgst'=>$sgst,
    		 		'head_igst'=>'INPUT IGST 18 %',
    		 		'value_igst'=>$igst,
    		 		'total_gst_value'=>$totalgst);
    		 }


    // 		  $fdata=$this->checkforfreight($orderid);
    // 		 if($fdata!='')
    // 		 {
    // 		 	$freightamount=$this->truncate_number($fdata);
				// $freight="$freightamount";
				// $response[]=array('head_freight'=>"Freight",'value_freight'=>$freight);
    // 		 }

    	


				$result=json_encode($response); 
				echo $result;


    }


     function getVendorGSTDetails($purchase_id)
    {
    	$saletype=0;
    	$rest=$this->db->select('a.id,d.gst,a.hpcl_billing_company')->from('inventory a')->join('vendors d','d.id=a.party')->where('a.id',$purchase_id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
				$bill_company_detail=$this->getbilling_company_detail($row->hpcl_billing_company);
				if(count($bill_company_detail)>0)
				{
				$our_gst=$bill_company_detail[0];
				}else
				{
				$our_gst=00;
				}

				
				/** CHECK FOR SALES LEDGER TYPE **/
				$our_gst_area=substr($our_gst,0,2);
				$client_gst_area=substr($row->gst,0,2);
				if($our_gst_area==$client_gst_area)
				{
				$saletype="1";
				}else
				{
				$saletype="2";
				}

    	}


    			return $saletype;

    }


    function check_for_density_approved($invid)
    {
    	$restey=$this->db->select('id')->from('inventory_details')->where('density_approved',0)->where('inventory_id',$invid)->get();

    	return $restey->num_rows();

    }

    function gettotal_purchase_value($purchase_id)
    {

    	$total_base_amount=array();
    	$total_base_amount[]=0;
    	$totalgst=0;
    	$rest1=$this->db->select('a.product,a.qty,a.pack_size,b.unit,a.rate,b.instruments_name,b.model_number,b.hsncode')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->where('a.inventory_id',$purchase_id)->get();
    		if($rest1->num_rows()>0)
    		{
    			foreach($rest1->result() as $row1)
    			{

    				$finalprice=$row1->qty*$row1->rate;
    				$total_base_amount[]=$finalprice;

    			}
    		}


    		 /** CALCULATE GST **/
    		 $gstslab=18;
    		 $cgst=0;
    		 $sgst=0;
    		 $igst=0;
    		 $totalamount=array_sum($total_base_amount);
    		 $gstdata=$this->getVendorGSTDetails($purchase_id);
    		 if($gstdata>0)
    		 {
    		 	if($gstdata==1)
    		 	{
    		 		/** LOCAL **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		$cgst=round($gstamt/2,2);
    		 		$sgst=round($gstamt/2,2);
    		 		
    		 	}else
    		 	{
    		 		/** IGST **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		$igst=round($gstamt,2);
    		 	
    		 		
    		 	}


    		 	$totalgst=$sgst+$cgst+$igst;

	 	 }

	 
    return round($totalamount+$totalgst,2);
					
}

function get_model_number($prdid)
{
	$model='';
	$prst=$this->db->select('model_number')->from('presto_instruments')->where('id',$prdid)->get();
	if($prst->num_rows()>0)
	{
		foreach($prst->result() as $rows);
		$model=$rows->model_number;
	}

	return $model; 

}


}