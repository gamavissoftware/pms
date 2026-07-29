<?php
ini_set('memory_limit','2048M');
ini_set('serialize_precision','-1');
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class TallyApi extends CI_Controller {

	function index()
		{
			echo "hi"; exit;
		}
	


	function getcustomerfromrange()
	{
		$this->db->query("SET SQL_BIG_SELECTS=1");
		$response=array();
		$startdate=date('Y-m-d',strtotime($this->input->post('startdate')));
		$enddate=date('Y-m-d',strtotime($this->input->post('enddate')));
		$company=$this->input->post('company');
	

		$this->db->select('a.id as mainorder_id,a.invoice_no,b.bill_to,b.ship_to, a.added_on,a.send_to_tally_On,a.quotation_id,a.generated_order_id,a.hpcl_billing_company,a.payment_type,a.credit_days,a.send_to_tally,b.shipping_address,b.shipping_state,b.shipping_city,b.shipping_pincode,b.shipping_mobile_no,b.shipping_email,b.billing_address,b.billing_state,b.billing_city,b.billing_pincode,b.billing_mobile_no,b.billing_email,c.note,d.gst_no,d.msme_no,a.freight,a.freight_amount')->from('order_punch a')->join('order_punch_mailing_details b','a.id=b.order_id')->join('order_punch_payment_details c','a.id=c.order_id')->join('order_punch_tax_details d','d.order_id=a.id')->where('a.send_to_tally',1)->where('a.send_to_tally_On>=',$startdate)->where('a.send_to_tally<=',$enddate);

			if($company>0 && $company<>'')
			{
				$this->db->where('a.hpcl_billing_company',$company);
			}

			$rest=$this->db->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				{


				$customer_data=$this->getcustomer_detail($row->quotation_id);
				if(count($customer_data)>0)
				{
					$customer_ref_no=$customer_data[0];
					$company_name=$row->bill_to;
					$gst=$customer_data[2];
					$customer_name=$customer_data[3];
					$contact_no=$customer_data[4];
					$email=$customer_data[5];

					if($row->payment_type==2)
					{
						$paymenttype="Cash";

					}else if($row->payment_type==3)
					{
						$paymenttype="Online Transfer";

					}else if($row->payment_type==4){

						$paymenttype="PDC";
					}else if($row->payment_type==5)
					{
						$paymenttype="Credit";
					}else if($row->payment_type==6)
					{
						$paymenttype="Advance";
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
					$client_gst_area=substr($row->gst_no,0,2);

					if($our_gst_area==$client_gst_area)
					{
						$saletype="LOCAL SALE";
					}else
					{
						$saletype="INTERSTATE SALE";
					}
					/** END **/

					$billing_state=$this->getstatename($row->billing_state);
					$shipping_state=$this->getstatename($row->shipping_state);
					$billing_address=$this->RemoveSpecialChar($row->billing_address)."~".$row->billing_city."-".$row->billing_pincode;
					$shipping_address=$this->RemoveSpecialChar($row->shipping_address)."~".$row->shipping_city."-".$row->shipping_pincode;

					if($row->freight==1)
					{
						$freight="Yes";
						$freight_amount=$row->freight_amount;
					}else
					{
						$freight="No";
						$freight_amount=0;
					}


					$dispatchdate=date('d-m-Y', strtotime($row->send_to_tally_On.' +1 day'));
					$orderdate=date('d-m-Y',strtotime($row->send_to_tally_On));
					$total_invoice_amount=$this->get_total_order_value($row->invoice_no,$row->mainorder_id);
					$response[]=
					array(
						'primary_id'=>$row->mainorder_id,
						'total_invoice_value'=>$total_invoice_amount,
						'billing_company'=>$billing_company_name,
						'customer_code'=>$customer_ref_no,
						'customer_name'=>$customer_name,
						'company_name'=>$company_name,
						'contact_no'=>$contact_no,
						'customer_email'=>$email,
						'gst'=>$row->gst_no,
						'gst_type'=>"Regular",
						'billing_address'=>str_replace("\r\n"," ",$billing_address),
						'billing_country'=>"India",
						'billings_state'=>$billing_state,
						'billing_city'=>$row->billing_city,
						'billing_pincode'=>$row->billing_pincode,
						'ship_to'=>$row->ship_to,
						'shipping_address'=>str_replace("\r\n"," ",$shipping_address),
						'shipping_country'=>"India",
						'shipping_state'=>$shipping_state,
						'shipping_city'=>$row->shipping_city,
						'shipping_pincode'=>$row->shipping_pincode,
						'internalorderno'=>$row->invoice_no,
						'freight'=>$freight,
						'freight_amount'=>$freight_amount,
						'dispatch_date'=>$dispatchdate,
						'order_date'=>$orderdate,
						'narration'=>$row->note,
						'saletype'=>$saletype
							);

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


    function getcustomerOrder_details()
    {
    	$response=array();
    	$orderid=$this->input->post('order_id');
    	$primary_id=$this->input->post('primary_id');
    	$rest=$this->db->select('quotation_id')->from('order_punch')->where('invoice_no',$orderid)->where('id',$primary_id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$quotation_id=$row->quotation_id;

    		$rest1=$this->db->select('a.product_id,a.qty,a.pack_size,b.unit,a.agreed_price,b.instruments_name,b.model_number,b.hsncode')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$quotation_id)->get();
    		if($rest1->num_rows()>0)
    		{
    			foreach($rest1->result() as $row1)
    			{

    				$finalprice=round($row1->qty*$row1->agreed_price,2);
    				$finalprice="$finalprice";

    				$response[]=array('name'=>$row1->model_number,'quantity'=>round($row1->qty,2),'saleprice'=>$row1->agreed_price,'finalprice'=>$finalprice,'hsn'=>$row1->hsncode,'unit'=>$row1->unit,'group'=>"Sales Item",'gst'=>"18");

    			}
    		}

    		


    	}


				$result=json_encode($response); 
				echo $result;


    }
  

  	function checkforfreight($orderid,$primary_id)
  	{
  		$fdata='';
  		$rest=$this->db->select('freight,freight_amount')->from('order_punch')->where('invoice_no',$orderid)->where('id',$primary_id)->where('freight',1)->get();
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




    function getOrder_gst_details()
    {

    	$total_base_amount=array();
    	$total_base_amount[]=0;
    	$response=array();
    	$orderid=$this->input->post('order_id');
    	$primary_id=$this->input->post('primary_id');
    	$rest=$this->db->select('quotation_id')->from('order_punch')->where('invoice_no',$orderid)->where('id',$primary_id)->get();
    	if($rest->num_rows()>0)
    	{
    		foreach($rest->result() as $row);
    		$quotation_id=$row->quotation_id;

    		$rest1=$this->db->select('a.product_id,a.qty,a.pack_size,b.unit,a.agreed_price,b.instruments_name,b.model_number,b.hsncode')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$quotation_id)->get();
    		if($rest1->num_rows()>0)
    		{
    			foreach($rest1->result() as $row1)
    			{

    				$finalprice=$row1->qty*$row1->agreed_price;
    				$total_base_amount[]=$finalprice;

    			}
    		}

    		 $fdata=$this->checkforfreight($orderid,$primary_id);
    		 if($fdata!='')
    		 {
    		 	$freightamount=$this->truncate_number($fdata);
				$total_base_amount[]=$freightamount;
    		 }

    		 /** CALCULATE GST **/
    		 $gstslab=18;
    		 $cgst=0;
    		 $sgst=0;
    		 $igst=0;
    		 $totalamount=array_sum($total_base_amount);
    		 $gstdata=$this->getGSTDetails($orderid,$primary_id);
    		 if($gstdata>0)
    		 {
    		 	if($gstdata==1)
    		 	{
    		 		/** LOCAL **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		$cgst=round(($gstamt/2), 2);
    		 		$sgst=round(($gstamt/2), 2);
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
    		 	$totalinvoice_amt=$totalamount+$totalgst;
    		 	$totalgst="$totalgst";

    		  	//echo $totalinvoice_amt; exit;

    		 	$d=$this->is_decimal($totalinvoice_amt);
    		 	if($d==1)
    		 	{
    		 		$r=round($totalinvoice_amt);
 		 			if($r>$totalinvoice_amt)
    		 		{
    		 			$rr=$r-$totalinvoice_amt;

    		 	
    		 		}else
    		 		{

    		 			$rr=round($totalinvoice_amt-$r,2);
    		 			$rr=0-$rr;
    		 		}

    		 		
    		 	}else
    		 	{
    		 		$rr=0;
    		 	}

    		 	// $response[]=array(
    		 	// 	'headname'=>'OUTPUT CGST 9%',
    		 	// 	'headvalue'=>$cgst,
    		 	// 	'headname'=>'OUTPUT SGST 9%',
    		 	// 	'headvalue'=>$sgst,
    		 	// 	'headname'=>'OUTPUT IGST 18%',
    		 	// 	'headvalue'=>$igst,
    		 	// 	'total_gst_value'=>$totalgst);

    		 	$response[]=array(
    		 		'head_cgst'=>'OUTPUT CGST 9%',
    		 		'value_cgst'=>$cgst,
    		 		'head_sgst'=>'OUTPUT SGST 9%',
    		 		'value_sgst'=>$sgst,
    		 		'head_igst'=>'OUTPUT IGST 18%',
    		 		'value_igst'=>$igst,
    		 		'total_gst_value'=>$totalgst,
    		 		'ROUND OFF'=>round($rr,2));
    		 }


    		  $fdata=$this->checkforfreight($orderid,$primary_id);
    		 if($fdata!='')
    		 {
    		 	$freightamount=$this->truncate_number($fdata);
				$freight="$freightamount";
				$response[]=array('head_freight'=>"Freight",'value_freight'=>$freight);
    		 }

    	}


				$result=json_encode($response); 
				echo $result;


    }



    function getGSTDetails($orderid,$primary_id)
    {
    	$saletype=0;
    	$rest=$this->db->select('a.quotation_id,a.hpcl_billing_company,d.gst_no')->from('order_punch a')->join('order_punch_tax_details d','d.order_id=a.id')->where('a.invoice_no',$orderid)->where('a.id',$primary_id)->get();
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
				$client_gst_area=substr($row->gst_no,0,2);

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


    function is_decimal($n) {
    // Note that floor returns a float 
    return is_numeric($n) && floor($n) != $n;
}

function  get_total_order_value($orderid,$primary_id)
{

	$total_base_amount=array();
	$totalinvoice_amt=0;
    	$total_base_amount[]=0;
    	$response=array();
    	$orderid=$orderid;
    	$primary_id=$primary_id;
    	$rest=$this->db->select('quotation_id')->from('order_punch')->where('invoice_no',$orderid)->where('id',$primary_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$quotation_id=$row->quotation_id;

			$rest1=$this->db->select('a.product_id,a.qty,a.pack_size,b.unit,a.agreed_price,b.instruments_name,b.model_number,b.hsncode')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$quotation_id)->get();
			if($rest1->num_rows()>0)
			{
				foreach($rest1->result() as $row1)
				{

				$finalprice=$row1->qty*$row1->agreed_price;
				$total_base_amount[]=$finalprice;

				}
			}


			 $fdata=$this->checkforfreight($orderid,$primary_id);
    		 if($fdata!='')
    		 {
    		 	$freightamount=$this->truncate_number($fdata);
				$total_base_amount[]=$freightamount;
    		 }

					/** CALCULATE GST **/
					$gstslab=18;
					$cgst=0;
					$sgst=0;
					$igst=0;
					$totalamount=array_sum($total_base_amount);
					$gstdata=$this->getGSTDetails($orderid,$primary_id);
					if($gstdata>0)
					{

						if($gstdata==1)
    		 	{
    		 		/** LOCAL **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		$cgst=round(($gstamt/2), 2);
    		 		$sgst=round(($gstamt/2), 2);
    		 		$cgst="$cgst";
    		 		$sgst="$sgst";
    		 	}else
    		 	{
    		 		/** IGST **/
    		 		$gstamt=($totalamount*$gstslab)/100;
    		 		 $igst=round($gstamt,2);
    		 		 $igst=(float) $igst;
    		 		
    		 	}

    		 	
					// if($gstdata==1)
					// {
					// /** LOCAL **/
					// $gstamt=($totalamount*$gstslab)/100;
					// $cgst=$gstamt/2;
					// $sgst=$gstamt/2;
					// $cgst="$cgst";
					// $sgst="$sgst";
					// }else
					// {
					// /** IGST **/
					// $gstamt=($totalamount*$gstslab)/100;
					// $igst=$gstamt;
					// $igst=(float) $igst;
					// }


					$totalgst=$sgst+$cgst+$igst;
					$totalinvoice_amt=$totalamount+$totalgst;
					$d=$this->is_decimal($totalinvoice_amt);
					if($d==1)
    		 		{
    		 			$totalinvoice_amt=round($totalinvoice_amt);

    		 		}else
    		 		{
    		 			$totalinvoice_amt=$totalinvoice_amt;
    		 		}



		}

				
}
			
					return $totalinvoice_amt;		
}

}
