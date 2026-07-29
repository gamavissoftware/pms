<?php
defined('whatsappuser') OR define('whatsappuser','sundarindustrial');
defined('whatsapppass') OR define('whatsapppass','HPCLsundar@42I');
ini_set('serialize_precision','-1');
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Salescrm_model','salescrm');
		//$this->db->query('SET SQL_BIG_SELECTS=1'); 
				
	}


	public function order_pending_for_billing() 
	{
		$this->load->view('billing/pending_billing');
	}


	function all_pending_billing() {

//		$this->output->clear_path_cache(page_url.'/Billing/all_pending_billing');

		$lead_data = array();
		$query = $this->db->select('i.first_name as salesorderfname,i.last_name as salesorderlname,a.sales_order_no,a.sales_order_addedOn,a.sales_order_By,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,h.contact_number,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users i','i.user_id=a.sales_order_By','left')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.send_so_to_billing',1)
						  ->where('a.billing', 0)
						  ->where('a.cancelled', 0)
						  // ->where('a.invoice_no',1282)
						  ->order_by('a.id','DESC')
				 		  ->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 

			
				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}
			

			$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

				$show=1;
				$reason="";
				$hold_type='';
				$h_type=0;



				if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}

						}

					}

				}

				
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="PREVIOUS INVOICE PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}

				}

				
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="INVOICE PDC NOT RECIEVED";
						$h_type=2;
						$uphold=$this->checkfor_hold_release($row->id,$h_type);
						if($uphold>0)
						{
						$show=1;
						}

				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS SET";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}
				}

				}



				/** CHECK FOR DISCOUNT PENDING FOR APPORVAL **/

				$d_app=$this->checkProductDiscountPending($row->quotation_id);
				if($d_app>0)
					{
					$show=0;
					$reason="<strong style='color:red'>Pending/Rejected Discount Approval</strong>";
					$hold_type="PRODUCT(s) PENDING FOR DISCOUNT APPROVAL";
					$h_type=5;
					}

				/** END **/


				/** CHECK FOR PAYMENT TERMS NOT APPROVED **/
				$p_app=$this->checkCustomerPaymentTermsApprovalPending($row->customer_id);

				if($p_app>0)
					{
					$show=0;
					$reason="<strong style='color:red'>Customer Payment Terms Not Approved</strong>";
					$hold_type="CUSTOMER PAYMENT TERM NOT APPROVED";
					$h_type=6;
					}

				

				/** END **/

				// else if($row->payment_type==5)
				// {

				// 	//echo "hi"; exit;


				// }


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				// if($row->hpcl_billing_company==3)
				// {
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once invoice is created and billing is done</strong>";
					}
				//}	
			}





			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			// if($row->send_to_tally == 1) {
			// 	$send_to_tally = "<span><strong style='color:green'>Invoice Created On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			// } else {
			// 	$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			// }

			if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Invoice Created On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.",".$row->hpcl_billing_company.")'>Create Invoice</a></span>";
				}
			}

			if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					// $billing = "<span><strong style='color:green'>Billed</strong><br>
					// 			<label>Bill No</label>
					// 			<input type='text' value='".$row->po_no."'></span>";
				} else {

					if($row->send_to_tally==1)
					{
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
					}else
					{
						$billing='<strong style="color:red;font-weight:bold;">CREATE INVOICE FIRST TO MARK AS BILLED</strong>';
					}
				}
			}

			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}



			if($show==0)
			{

				$art=$this->db->select('id')->from('hold_notification')->where('order_id',$row->id)->get();
				if($art->num_rows()==0)
				{
					$msg="Hello ".$row->first_name." ".$row->last_name.",\n\n";
					$msg.="An Order for you customer *".strtoupper($companyname)." bill no ".$row->invoice_no."* is on hold due to following reason \n\n";
					$msg.="*".$reason."*"."\n\n";
					$msg.="Contact Admin for further steps"."\n\n";
					//echo $msg; exit;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '8447031736',
					'receiverMobileNo' => $row->contact_number.',8447031736',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($msg));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
						}
					curl_close($ch);


					$msg1="Hello Sir,\n\n";
					$msg1.="An Order for you customer *".strtoupper($companyname)." bill no ".$row->invoice_no."* is on hold due to following reason \n\n";
					$msg1.="*".$reason."*"."\n\n";
					$msg1.="Please take neccesary action"."\n\n";
					//echo $msg; exit;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '8447031736',
					'receiverMobileNo' => '9891941007,8447031736',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($msg1));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					$d=array('order_id'=>$row->id,'addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('hold_notification',$d);

					}else
					{
						

					}
					
			}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			if($row->send_to_tally==1){
			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			$batch_file_detail=$this->get_batch_code_files_with_validation($row->orderpunchquote);
			$bfd=explode('~',$batch_file_detail);
			if($bfd[0]>0)
			{
			if($_SESSION['logged_in']['role']==1)
			{
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}else
			{
				$tax_invoice="<strong style='color:red;fonr-weight:bold;'>Following Test Reports are not uploaded, Invoice cannot be generated<br/>".$bfd[1]."</strong>";
			}
			}else
			{
				$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}


		}else{
			$tax_invoice="<strong style='color:red;font-weight:bold;'>Invoice is activated once order is sent to tally.</strong>";
			$batch_details="";
		}

			$cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";


			if($_SESSION['logged_in']['role']==1)
			{
			$customer_edit="<a href='".page_url."Customer/edit_customer/".$row->customer_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			}else
			{
				$customer_edit='NA';
			}

			if($row->send_to_tally==0)
			{
			$rollback="<a href='javascript:;' class='btn btn-danger btn-xs' onclick='rollback_order(".$row->id.");'>Rollback Order</a>";
			}else
			{
				$rollback="NA";
			}
			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$lead_data[] = array('sr_no'=>$i,
				'edit_customer'=>$customer_edit,
				'source'=>$row->lead_source, 
				'sales_order'=>$row->sales_order_no."<br/>".date('d-M-Y H:i:s',strtotime($row->sales_order_addedOn))."<br/>".$row->salesorderfname." ".$row->salesorderlname, 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								  'order_details' => $order_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
								 'cancell' => $cancell,
								 'rollback' => $rollback
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function getlead_based_customer($lead_id)
	{
		$data=array();
		$reste=$this->db->select('customer_name,company_name')->from('leads')->where('id',$lead_id)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row);
			
			$data[]=$row->customer_name;
			$data[]=$row->company_name;
		}

		return $data;

	}

	function getquotation_based_customer($company_id)
	{
		$data=array();
		$restey=$this->db->select('customer_name,company_name')->from('customer_detail')->where('id',$company_id)->get();
		if($restey->num_rows()>0)
		{
		foreach($restey->result() as $restey1);
		$data[]=$restey1->customer_name;
		$data[]=$restey1->company_name;
		}
	return $data;

	}

	function getproducts_detail($quotation)
	{
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		<th>Batch Code</th>
		</tr>
		</thead>
		<tbody>';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){



			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';

			if($product->new_batch_code==0)
			{
			$html.='<td>'.$product->batch_code.'</td>';
			}else
			{
			$batch_no=$this->getbatch_no($product->batch_code);
			$html.='<td>'.$batch_no.'</td>';
			}


			
			$j++;
			}
			$html.='</tbody></table>';
			}

			return $html;
	}


	function getProductName() {
		$stock_ok=array();
		$stock_ok[]=0;
		$quotation_id = $this->input->post('quote_id');
		$company = $this->input->post('company');
		$html = '';
		$invoice_no=$this->getinvoice_no_new($company);
		$previous=$this->get_previous_two_invoices($company,$quotation_id);
		$html.="<div class='col-md-12'>
		<div class='col-md-12'>
			<div class='form-group'>
			<label>Probable Invoice No.</label><br/>
			<span>Previous Invoices Detail<br/>".$previous."</span><br/>
			<input type='text' id='in' value='".$invoice_no."' class='form-control' readonly>
		
			</div>
			</div>
			
		</div><br/>";




		$sql = $this->db->select('a.qty,a.id, a.product_id, a.batch_code, b.instruments_name,b.unit')
					    ->from('customer_quotation_detail a')
					    ->join('presto_instruments b','a.product_id=b.id')
					    ->where('a.quotation_id',$quotation_id)
					    ->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				$stock_avail=$this->check_stock_availability($row->product_id,$company);
				if($stock_avail<$row->qty)
				{
					$stock_ok[]=1;
				}
				$html .= '<div class="col-sm-4">
                              <div class="form-group">
                                <label>Product Name</label>
                                  <input type="hidden" name="detail_id[]" value="'.$row->id.'">
                                  <input type="hidden" name="product_id[]" value="'.$row->product_id.'">
                                  <input type="text" name="product_name[]" id="product_name'.$row->id.'" class="form-control" value="'.$row->instruments_name.'" readonly>
                              </div>
                            </div>

                            <div class="col-sm-2">
                              <div class="form-group">
                                <label>Avail. Stock</label>
                                  <input type="text" class="form-control" value="'.$stock_avail.'" readonly>
                              </div>
                            </div>

                            <div class="col-sm-2">
                              <div class="form-group">
                                <label>QTY</label>
                                  <input type="text" name="prd_qty[]" id="prd_qty'.$row->id.'" class="form-control" value="'.$row->qty.'" readonly>'.strtoupper($row->unit).'
                              </div>
                            </div>
                            
                            </div><div style="clear:both;height:5px;"></div>';
			}
		}

		echo $html."~".array_sum($stock_ok);
	}

	function getstate($shipstate)
	{
		$sname='';
		$resteu=$this->db->select('state_name')->from('states')->where('state_id',$shipstate)->get();
		if($resteu->num_rows()>0)
		{
		foreach($resteu->result() as $product)

		$sname=$product->state_name;

		}

		return $sname;



	}

	function getbilling_company($company)
	{
		$sname='';
		$resteu=$this->db->select('companyname')->from('store_rack_location')->where('id',$company)->get();
		if($resteu->num_rows()>0)
		{
		foreach($resteu->result() as $product)

		$sname=$product->companyname;

		}

		return $sname;

	}

	// function send_to_tally() {
	// 	$id = $this->input->post('id');

	// 	$data = array(
	// 				  'send_to_tally' => 1,
	// 				  'send_to_tally_On' => date('Y-m-d'),
	// 				  'send_by' => $this->session->userdata['logged_in']['user_id']
	// 				  );

	// 	$this->db->where('id', $id)
	// 			 ->update('order_punch', $data);


	// 	if($this->db->affected_rows() > 0) {
	// 		echo '<strong style="color:green">Sent To Tally</strong>';
	// 	} 
	// }

	function send_to_tally() {
	

		$this->db->trans_start();
		$detail_id = $this->input->post('detail_id');
		$order_id = $this->input->post('order_id');
		$quotation_id = $this->input->post('quotation_id');
		$billing_company = $this->input->post('billing_company');
			$invoice_nno=$this->getinvoice_no_new($billing_company);
		/** SUBTRACT STOCK **/
		$prd_id=$this->input->post('product_id');
		$prd_qty=$this->input->post('prd_qty');
		$stock_check=$this->salescrm->checkforavailable_Company_QTY($prd_id,$prd_qty,$billing_company);
		$new_data=explode('~',$stock_check);
		$proceed_flag=$new_data[0];
		// $prd_not_available=$new_data[1];
		if($proceed_flag==0)
		{

			/** GENERATE INVOICE NO **/
			$invoice_nno=$this->getinvoice_no_new($billing_company);
			$ddf=array('invoice_no'=>$invoice_nno);
			$this->db->where('id',$order_id);
			$this->db->update('order_punch',$ddf);
			/** END **/

		for($l=0;$l<count($prd_id);$l++)
		{
			$product=$prd_id[$l];
			$product_qty=$prd_qty[$l];
			$stockdata=$this->check_stock_availability($product,$billing_company);
			$new_stock=$stockdata-$product_qty;
			$d=array('stock'=>$new_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('company_id',$billing_company);
			$this->db->where('itemid',$product);
			$this->db->update('company_wise_inventory',$d);

			/** CHECK FOR PURCHASE AND MARK IT NULL **/
			$amount_to_be_paid=$product_qty;
			$tyu=$this->db->select('id,qty,balance_left')->from('company_wise_inventory_info')->where('company_id',$billing_company)->where('item_id',$product)->where('exhausted',0)->where('balance_left>',0)->get();
			if($tyu->num_rows()>0)
			{
				foreach($tyu->result() as $exhaust_stock)
				{

					if($amount_to_be_paid>0)
					{
						$balance_left_stock=$exhaust_stock->balance_left;
						$amount_to_be_paid=$amount_to_be_paid;

						if($balance_left_stock>=$amount_to_be_paid)
						{
							$bleft=$balance_left_stock-$amount_to_be_paid;
							$amount_to_be_paid=0;
							
						}else
						{
							$amount_to_be_paid=$amount_to_be_paid-$balance_left_stock;
							$bleft=0;
						}

						

						$ddf=array('balance_left'=>$bleft,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
						$this->db->where('id',$exhaust_stock->id);
						$this->db->update('company_wise_inventory_info',$ddf);

						$nrow=$this->db->select('id')->from('company_wise_inventory_info')->where('id',$exhaust_stock->id)->where('balance_left>0')->get();
						if($nrow->num_rows()==0)
						{
						$ddf1=array('exhausted'=>1);
						$this->db->where('id',$exhaust_stock->id);
						$this->db->update('company_wise_inventory_info',$ddf1);
						}else
						{
						$ddf1=array('exhausted'=>0);
						$this->db->where('id',$exhaust_stock->id);
						$this->db->update('company_wise_inventory_info',$ddf1);
						}



					}



				}
			}


		}


		for($i=0;$i<count($detail_id);$i++) {
			if($detail_id[$i] != '') {

				// $batch_code = $this->input->post('batch_code'.$detail_id[$i]);
				// $batch_code_new = implode(',', $batch_code);

				// $datas = array(
				// 			   'batch_code' => $batch_code_new,
				// 			   'new_batch_code'=>1
				// 			  );

				// // echo '<pre>';print_r($datas);exit;
				// $this->db->where('id', $detail_id[$i])
				// 		 ->update('customer_quotation_detail', $datas);

			}
		}


		if($this->input->post('transporter_type') == 1) {
			$vehicle_no = $this->input->post('vehicle_no');
			$tname=0;
			$tmobile_no=0;
			$taddress='';
			$rate_type=0;
			$transport_rate=0;

		} else if($this->input->post('transporter_type') == 2) {
			$vehicle_no = $this->input->post('vehicle_no1');

			$sql = $this->db->select('id')
				->from('transporter_details')
				->where('id', $this->input->post('transporter_name'))
				->get();
				if($sql->num_rows() == 0) {
				$datas = array(
				'name' => $this->input->post('transporter_name'),
				'mobile_no' => $this->input->post('tmobile_no'),
				'address' => $this->input->post('taddress')
				);
				$this->db->insert('transporter_details', $datas);
				$tname = $this->db->insert_id();
				}else
				{
				$tname=$this->input->post('transporter_name');
				}
			
			$tmobile_no=$this->input->post('tmobile_no');
			$taddress=$this->input->post('taddress');
			$rate_type=$this->input->post('rate_type');
			$transport_rate=$this->input->post('transport_rate');

		} else {
			$vehicle_no = '';
			$tname=0;
			$tmobile_no=0;
			$taddress='';
			$rate_type=0;
			$transport_rate=0;
		}

		$data = array(
					  'transporter_type' => $this->input->post('transporter_type'),
					  'transporter_name' =>$tname,
					  'transporter_mobile'=>$tmobile_no,
					  'transporter_address'=>$taddress,
					  'rate_type'=>$rate_type,
					  'rate'=>$transport_rate,
					  'transporter_id' => $this->input->post('transporter_id'),
					  'distance' => $this->input->post('distance_in_km'),
					  'vehicle_no' => $vehicle_no,
					  'send_to_tally' => 1,
					  'billing'=>1,
					  'billed_On'=>date('Y-m-d H:i:s'),
					  'billed_by'=>$_SESSION['logged_in']['user_id'],
					  'send_to_tally_On' => date('Y-m-d'),
					  'send_by' => $this->session->userdata['logged_in']['user_id']
					  );

		$this->db->where('id', $order_id)
				 ->update('order_punch', $data);

		$data1 = array(
					  'vehicle_no' => $this->input->post('vehicle_no'),
					  'vehicle_type' => $this->input->post('vehicle_type'),
					  'destination' => $this->input->post('destination')
					  );

		$this->db->where('order_id', $order_id)
				 ->update('order_punch_mailing_details', $data1);



			if ($this->db->trans_status() === FALSE)
			{
			$this->db->trans_rollback();
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Failed To Process the Transaction.</div>');
		redirect(page_url.'Billing/order_pending_for_billing');
			}
			else
			{
			$this->db->trans_commit();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Successfully Sent To Tally</div>');
			redirect(page_url.'Billing/order_pending_for_billing');
			}


	
	}else
	{
		echo "Some Items Stock is not Available than required. Please go back and check"; exit;
	}
	}

	function billing() {
		$id = $this->input->post('id');

		$po_no = '';

		$sql = $this->db->select('po_no')
						->from('order_punch')
						->where('id', $id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
			$po_no = $row->po_no;
		}

		$data = array(
					  'billing' => 1,
					  'billed_On' => date('Y-m-d H:i:s'),
					  'billed_by' => $this->session->userdata['logged_in']['user_id']
					  );

		$this->db->where('id', $id)
				 ->update('order_punch', $data);


		if($this->db->affected_rows() > 0) {
			echo '<strong style="color:green">Billed</strong>';
		} 
	}


	function billing_history() {
		$this->load->view('billing/billing_history');
	}

	function billing_history_list() {
		$start_date = $this->uri->segment(3);
		$end_date = $this->uri->segment(4);
		$company = $this->uri->segment(5);
		$customer = $this->uri->segment(6);
		$product = $this->uri->segment(7);
		$lead_data = array();
		$this->db->select('a.sales_order_no,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id,j.first_name as createdf,j.last_name as createdl')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users j','j.user_id=a.added_by')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.billing', 1);
		if($start_date != '' && $end_date != '') {
				 $this->db->where('a.send_to_tally_On >=', $start_date);
				 $this->db->where('a.send_to_tally_On <=', $end_date);
		}
		if($company<>'ALL' && $company<>'')
		{
			$this->db->where('a.hpcl_billing_company',$company);
		}

		if($customer<>'ALL' && $customer<>'')
		{
			$this->db->where('b.customer_id',$customer);
		}

		$query = $this->db->where('a.cancelled', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detailNew($row->orderpunchquote,$product);
			$h=explode('~',$html);
			// echo $h[1]; exit;


			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";

			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			$batch_file_detail=$this->get_batch_code_files_with_validation($row->orderpunchquote);
			$bfd=explode('~',$batch_file_detail);
			if($bfd[0]>0)
			{
			if($_SESSION['logged_in']['role']==1)
			{
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}else
			{
				$tax_invoice="<strong style='color:red;fonr-weight:bold;'>Following Test Reports are not uploaded, Invoice cannot be generated<br/>".$bfd[1]."</strong>";
			}
			}else
			{
				$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}



		// $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";

		if($_SESSION['logged_in']['role']==1)
			{
				$cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";
			}else
			{
				$cancell="<strong style='color:red;'>Available To Admin Only</strong>";
			}

			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			if($h[1]>0)
			{
			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
						'so_no'=>"<strong style='color:red;font-weight:bold;'>".$row->sales_order_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on))."<br/><br/>".$row->createdf." ".$row->createdf,
						'agent'=>$row->first_name." ".$row->last_name,
						'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->send_to_tally_On)),
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$h[0],
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'sendtotally' => $send_to_tally,
								  'po_details' =>$po_details,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
								 'cancel'=>$cancell

								 // 'payment_collection' => $payment_collection
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

	function getCustomerdetail($customerid)
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


	function pending_for_dispatch() {
		$this->load->view('billing/pending_for_dispatch');
	}

	function pending_for_dispatch_list() {
		$lead_data = array();
		$query = $this->db->select('a.credit_days,a.distance,a.transporter_id,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.dispatch, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id,t.first_name,t.last_name')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->join('system_users t', 't.user_id=a.agent', 'left')
						  ->where('a.billing', 1)
						  ->where('a.dispatch', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail=$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail=$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->orderpunchquote."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally</strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}


			if($row->distance==0)
			{
			$eway_bill = "<a href='javascript:;'  onclick='open_modal(".$row->id.")' class='btn btn-warning btn-xs'>Generate E-way Bill</a>";
			}else
			{
				$eway_bill="<a href='".page_url."Billing/ewaybill/".$row->id."/".$row->orderpunchquote."'  class='btn btn-success btn-xs'>Download E-way Bill</a>";
			}
			
			if($row->dispatch == 1) {
				$dispatch = "<span><strong style='color:green'>Order Dispatched</strong></span>";
			} else {
				$dispatch = "<span id='dispatched".$row->id."'><input type='checkbox' name='dispatch' id='dispatch".$row->id."' value='".$row->id."' onchange='dispatch(".$row->id.")'></span>";
			}


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";

			
			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			$batch_file_detail=$this->get_batch_code_files_with_validation($row->orderpunchquote);
			$bfd=explode('~',$batch_file_detail);
			if($bfd[0]>0)
			{
			if($_SESSION['logged_in']['role']==1)
			{
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}else
			{
				$tax_invoice="<strong style='color:red;fonr-weight:bold;'>Following Test Reports are not uploaded, Invoice cannot be generated<br/>".$bfd[1]."</strong>";
			}
			}else
			{
				$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}

		// $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
			$lead_data[] = array('sr_no'=>$i,
				'billing_company'=>$billcompany,
				'lead_manager'=>$row->first_name." ".$row->last_name,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'eway_bill' => $eway_bill,
								 'dispatch' => $dispatch,
								 'tax_invoice' => $tax_invoice
								 // 'payment_collection' => $payment_collection
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function dispatch_history() {
		$this->load->view('billing/dispatch_history');
	}

	function dispatch_history_list() {
		$lead_data = array();
		$query = $this->db->select('t.first_name,t.last_name,a.credit_days,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.dispatch, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->join('system_users t', 't.user_id=a.agent', 'left')
						  ->where('a.billing', 1)
						  ->where('a.dispatch', 1)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail=$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail=$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally</strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

			$eway_bill = "<a href='javascript:;' class='btn btn-success btn-xs'>Generate Eway Bill</a>";
			
			if($row->dispatch == 1) {
				$dispatch = "<span><strong style='color:green'>Order Dispatched</strong></span>";
			} else {
				$dispatch = "<span id='dispatched".$row->id."'><input type='checkbox' name='dispatch' id='dispatch".$row->id."' value='".$row->id."' onchange='dispatch(".$row->id.")'></span>";
			}


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";


			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			$batch_file_detail=$this->get_batch_code_files_with_validation($row->orderpunchquote);
			$bfd=explode('~',$batch_file_detail);
			if($bfd[0]>0)
			{
			if($_SESSION['logged_in']['role']==1)
			{
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}else
			{
				$tax_invoice="<strong style='color:red;fonr-weight:bold;'>Following Test Reports are not uploaded, Invoice cannot be generated<br/>".$bfd[1]."</strong>";
			}
			}else
			{
				$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			}

		// $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
			
			$lead_data[] = array('sr_no'=>$i,
				'lead_manager'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'dispatch' => $dispatch,
								 'tax_invoice' => $tax_invoice
								 // 'payment_collection' => $payment_collection
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function dispatch() {
		$id = $this->input->post('id');

		$data = array(
					  'dispatch' => 1,
					  'dispatched_on' => date('Y-m-d H:i:s'),
					  'dispatched_by' => $this->session->userdata['logged_in']['user_id']
					  );

		$this->db->where('id', $id)
				 ->update('order_punch', $data);

		$this->send_dispatch_intimation($id);


		if($this->db->affected_rows() > 0) {
			echo '<strong style="color:green">Order Dispatched</strong>';
		} 
	}


	function send_dispatch_intimation($id)
	{
		
		$html='';
		$sql=$this->db->select('a.invoice_no,d.first_name,d.last_name,d.contact_number as agent_contact,a.agent,a.hpcl_billing_company,a.generated_order_id,a.quotation_id,b.customer_id,c.title,c.contact_no,c.email,c.company_name,c.customer_name')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->join('customer_detail c','b.customer_id=c.id')->join('system_users d','a.agent=d.user_id')->where('a.id',$id)->get();
		if($sql->num_rows()>0)
		{
			foreach($sql->result() as $row);
			$order_details=$this->getcustomer_order_detail($row->quotation_id);
			// echo $order_details;exit;
			$arr = explode('|', $order_details);

			if($arr[1] != '') {
				$attach_line = 'Please find the attached Batch Test Report.';
			} else {
				$attach_line = '';
			}

			$agent_email=$this->getuser_details($row->agent);
			$html.="Dear ".$row->title." ".ucwords(strtolower($row->customer_name)).",<br/><br/>";
			$html.='We are pleased to inform you that your order no. '.$row->generated_order_id.' have been dispatched as per the details below. The Order will reach you in 4-6 hours<br/><br/>';
			$html.='<strong>Product & Quantity Details</strong><br/>';
			$html.=$arr[0]."<br/>".$attach_line;
			$html.="<br/>Regards,";
			$html.="<br/>Team Dispatch<br/><br/>";
			$com=$this->db->select('*')->from('store_rack_location')->where('id',$row->hpcl_billing_company)->get();
			if($com->num_rows() >0)
			{
				foreach($com->result() as $company);

				$html.='<table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        M/S CFA// '.$company->companyname.'<br>'.
                                        $company->address.'<br>
                                        Emails : '. $company->email_id.'<br>
                                        MOBILE '. $company->mobile.'
                                    </td>
                                </tr>
                            </table>';

                            // echo $html;exit;

                            $subjectname = "Order Dispatched -".$row->company_name." | ".$company->companyname;
					     	$config['protocol'] = 'ssmtp';  
							$config['smtp_host'] = $company->smtp;  
							$config['smtp_user'] = $company->email;  
							$config['smtp_pass'] = $company->password;   
							$config['smtp_port'] = 465;   
							// $config['smtp_crypto'] = 'ssl';
							$config['newline'] = "\r\n";
							$config['starttls'] = TRUE;
							$config['charset'] = 'iso-8859-1';
							$config['mailtype'] = 'html';

					  //       $this->email->initialize($config);  
					  //       $this->load->library('email', $config);
					  //       $this->email->set_header('Header1', 'Value1');
							// 	$this->email->set_mailtype("html");

							// $this->email->to($row->email);
							// if($agent_email<>'')
							// {
							// $this->email->cc($agent_email,'sdsrbh5@gmail.com, webdevelopment1@gamavis.com');
							// }
							// $this->email->from($company->email);
							// $this->email->subject($subjectname);
							// $this->email->message($html);
							// if($arr[1] != '') {
							// 	$this->email->attach($arr[1]);
							// }
							// $result11=$this->email->send();
							// $this->email->print_debugger(); exit;


							/** SEND WHATSAPP **/
							/*Whatsapp Notification*/
							$item_details=$this->getcustomer_order_detail_whatsapp($row->quotation_id);
							$smsmessage="Dear ".$row->first_name." ".$row->last_name.",\n\n";
							$smsmessage.="Your Order ".$row->invoice_no." for ".$row->company_name." has been dispatched.\n\n";
							if($item_details<>'')
							{
							$smsmessage.="Order Details are as follows\n\n";
							$smsmessage.="----------------------------\n";
							$smsmessage.=$item_details;
							$smsmessage.="----------------------------\n";
							}
						
						//echo $smsmessage; exit;
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_POST, 1);
							$post = array(
							'receiverMobileNo' => '91'.$row->agent_contact,"918447031736",
							//'receiverMobileNo' => '918447031736',
							'username' => whatsappuser,
							'password' => whatsapppass,
							'message'=>strip_tags($smsmessage));
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$result = curl_exec($ch);
							//echo $result; exit;
							if (curl_errno($ch)) {
							echo 'Error:' . curl_error($ch);
							}
							curl_close($ch);
						/*Whatsapp Notification*/

							/** END **/


			}

			


		}



	}

	function getcustomer_order_detail($quoteid)
	{
		$table='';
		$filepath = '';
		$sql=$this->db->select('a.product_id,a.qty,a.batch_code, b.instruments_name, b.pack_size, c.shortname')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c','c.id=a.pack_size', 'left')->where('a.quotation_id',$quoteid)->get();
		if($sql->num_rows()>0)
		{
			$table.='<table style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">';
		foreach($sql->result() as $row)
		{

			$batch_code = explode(',', $row->batch_code);

			for($i=0; $i<count($batch_code);$i++) {
				$sql1 = $this->db->select('report_file')
								 ->from('inventory_batch_no')
								 ->where('batch_no', $batch_code[$i])
								 ->get();

					if($sql1->num_rows() > 0) {
						foreach ($sql1->result() as $rows);

						if($rows->report_file != '') {
							$filepath = assets_upload.'test_report/'.$rows->report_file;
						}
					}
			}

		$table.='<tr style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">
		<td style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">'.$row->instruments_name.' - '.$row->pack_size.'</td>
		<td style="padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;">'.$row->qty.' '.$row->shortname.'</td>
		</tr>';
		}

		$table.='</table>';
		}

		return $table.'|'.$filepath;

	}

	function einvoice()
	{

		$order_id=$this->uri->segment(3);
		$quote_id=$this->uri->segment(4);

		$order_data=$this->get_order_details($order_id);
	
		if(count($order_data)>0)
		{

		

		$docdetails=array("Typ"=>"INV","No"=>$order_data['No'],"Dt"=>$order_data['DT']);

		$buyer_Pin=(int) $order_data['Pin'];
		$sellerDtl=array("Gstin"=>$order_data['sellergst'],"LglNm"=>$order_data['LglNm'],"TrdNm"=>$order_data['TrdNm'],"Addr1"=>$order_data['Addr1'],"Addr2"=>$order_data['Addr2'],"Loc"=>$order_data['Loc'],"Pin"=>$buyer_Pin,"Stcd"=>$order_data['Stcd'],"Ph"=>$order_data['Ph'],"Em"=>$order_data['Em']);

		$buyer_Pin=(int) $order_data['buyer_Pin'];

		if($order_data['buyer_Em']=='')
		{
			$buyeremail=null;
		}else{
			$buyeremail=$order_data['buyer_Em'];
		}

		if($order_data['buyer_Ph']=='')
		{
			$buyerphone=null;
		}else
		{
			$buyerphone=$order_data['buyer_Ph'];
		}
		$BuyerDtls=array("Gstin"=>$order_data['buyer_gst'],"LglNm"=>$order_data['buyer_LglNm'],"TrdNm"=>$order_data['buyer_TrdNm'],"Pos"=>$order_data['buyer_Pos'],"Addr1"=>$order_data['buyer_Addr1'],"Addr2"=>$order_data['buyer_Addr2'],"Loc"=>$order_data['buyer_Loc'],"Pin"=>$buyer_Pin,"Stcd"=>$order_data['buyer_Stcd'],"Ph"=>$buyerphone,"Em"=>$buyeremail);

		

		if(count($order_data['prdDetails'])>0)
		{
			$grandtotal_w_gst=array();
			$grandtotal_w_gst[]=0;
			$grandtotal=array();
			$grandtotal[]=0;
			$cgstsum=array();
			$cgstsum[]=0;
			$igstsum=array();
			$igstsum[]=0;
			for($y=0;$y<count($order_data['prdDetails']);$y++)
			{
				
				$slno=(int) $order_data['prdDetails'][$y]['SlNo'];

				$slno="$slno";
			
		$ItemList[]=array("SlNo"=>$slno,"PrdDesc"=>$order_data['prdDetails'][$y]['PrdDesc'],"IsServc"=>$order_data['prdDetails'][$y]['IsServc'],"HsnCd"=>$order_data['prdDetails'][$y]['HsnCd'],"Qty"=>$order_data['prdDetails'][$y]['Qty'],"Unit"=>$order_data['prdDetails'][$y]['Unit'],"UnitPrice"=>$order_data['prdDetails'][$y]['UnitPrice'],"TotAmt"=>$order_data['prdDetails'][$y]['TotAmt'],"Discount"=>$order_data['prdDetails'][$y]['Discount'],"PreTaxVal"=>$order_data['prdDetails'][$y]['PreTaxVal'],"AssAmt"=>$order_data['prdDetails'][$y]['AssAmt'],"GstRt"=>$order_data['prdDetails'][$y]['GstRt'],"IgstAmt"=>$order_data['prdDetails'][$y]['IgstAmt'],"CgstAmt"=>$order_data['prdDetails'][$y]['CgstAmt'],"SgstAmt"=>$order_data['prdDetails'][$y]['SgstAmt'],"CesRt"=>$order_data['prdDetails'][$y]['CesRt'],"CesAmt"=>$order_data['prdDetails'][$y]['CesAmt'],"CesNonAdvlAmt"=>$order_data['prdDetails'][$y]['CesNonAdvlAmt'],"StateCesRt"=>$order_data['prdDetails'][$y]['StateCesRt'],"StateCesAmt"=>$order_data['prdDetails'][$y]['StateCesAmt'],"StateCesNonAdvlAmt"=>$order_data['prdDetails'][$y]['StateCesNonAdvlAmt'],"OthChrg"=>$order_data['prdDetails'][$y]['OthChrg'],"TotItemVal"=>$order_data['prdDetails'][$y]['TotItemVal']);

			$grandtotal_w_gst[]=$order_data['prdDetails'][$y]['PreTaxVal'];
			$grandtotal[]=$order_data['prdDetails'][$y]['TotItemVal'];
			$cgstsum[]=$order_data['prdDetails'][$y]['SgstAmt'];
			$igstsum[]=$order_data['prdDetails'][$y]['IgstAmt'];
			}
			



			 $ValDtls=array("AssVal"=>array_sum($grandtotal_w_gst),"IgstVal"=>array_sum($igstsum),"CgstVal"=>array_sum($cgstsum),"SgstVal"=>array_sum($cgstsum),"CesVal"=>0,"StCesVal"=>0,"Discount"=>0,"OthChrg"=>0,"RndOffAmt"=>0,"TotInvVal"=>round(array_sum($grandtotal),2));

		}else
		{
			$ValDtls=array();
			$ItemList=array();
		}

		
		if(array_sum($igstsum)>0)
		{
			$igstonintra="Y";
		}else
		{
			$igstonintra="N";
		}
		
		$trans_Detail=array("TaxSch"=>"GST","SupTyp"=>"B2B","IgstOnIntra"=>$igstonintra,"RegRev"=>null,"EcmGstin"=>null);

		$finalarray[]=array("Version"=>"1.1","TranDtls"=>$trans_Detail,"DocDtls"=>$docdetails,"SellerDtls"=>$sellerDtl,"BuyerDtls"=>$BuyerDtls,"ValDtls"=>$ValDtls,"ItemList"=>$ItemList);

		$encoded=json_encode($finalarray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$path=SITE_ROOT."einvoices/myfile_".$order_id.".json";
		$fp = fopen($path, 'w');
		fwrite($fp, $encoded);   // here it will print the array pretty
		fclose($fp);

		$downladpath=page_url1."eninvoices/myfile_".$order_id.".json";
		$filename="myfile_".$order_id.".json";
		header('Content-disposition: attachment; filename='.$filename);
		header('Content-type: application/json');

		echo ($encoded);

	
		}else
		{
			echo "Invalid Link"; exit;
		}

	}



	function get_order_details($order_id)
	{
		$data=array();
		$query = $this->db->select('a.transporter_name,a.transporter_id,a.distance,a.vehicle_no,a.hpcl_billing_company,d.ship_to,d.bill_to,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname as legalname,c.email as selleremail,c.mobile as sellermobile,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no as buyergst, e.msme_no,b.customer_id,c.gst as sellergst,c.rack_location as sellerlocation,c.address as selleraddress,c.pincode as sellerpincode,f.state_name as sellerstate,f.state_code as seller_state_code,g.city_name as sellercity,h.company_name as buyercompany,i.state_name as buyerbillingstate,i.state_code as buyer_state_code,a.invoice_no,a.send_to_tally_On')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('customer_detail h','h.id=b.customer_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->join('states f','c.state_id=f.state_id','left')
						
						  ->join('cities g','c.city_id=g.city_id','left')
						  ->join('states i','d.billing_state=i.state_id','left')
						  ->where('a.id', $order_id)
						  ->order_by('a.id','DESC')
				 		  ->get();

		
						$i=1;
						if($query->num_rows()>0)
						{
						$res = $query->result();
						//echo "<pre>"; print_r($res); exit;
						foreach($res as $row);

						$data['No']=trim($row->invoice_no);
						$data['DT']=date('d/m/Y',strtotime($row->send_to_tally_On));

						// done order details
						
						$data['sellergst']=trim($row->sellergst);
						$data['LglNm']=trim($row->legalname);
						$data['TrdNm']=trim($row->legalname);
						$data['Addr1']=trim($row->sellerlocation);
						$data['Addr2']=trim($row->selleraddress);
						$data['Loc']=trim($row->sellerlocation);
						$data['Pin']=trim($row->sellerpincode);
						$data['Stcd']=trim($row->seller_state_code);
						$data['Ph']=trim($row->sellermobile);
						$data['Em']=trim($row->selleremail);

						// SELLER DONE 

						$data['buyer_gst']=trim($row->buyergst);
						$data['buyer_LglNm']=trim($row->bill_to);
						$data['buyer_TrdNm']=trim($row->bill_to);
						$data['buyer_Pos']=trim(substr($row->buyergst,0,2));
						$data['buyer_Addr1']=trim($row->billing_address);
						$data['buyer_Addr2']=trim($row->buyerbillingstate);
						$data['buyer_Loc']=trim($row->billing_city);
						$data['buyer_Pin']=trim($row->billing_pincode);
						$data['buyer_Stcd']=trim($row->buyer_state_code);
						$data['buyer_Ph']=trim($row->billing_mobile_no);
						$data['buyer_Em']=trim($row->billing_email);

						//TRANSPORT DETAILS
						$data['trn_distance']=trim($row->distance);
						$data['trn_name']=trim($row->transporter_name);
						$data['trn_id']=trim($row->transporter_id);
						$data['trn_vehicle_no']=trim($row->vehicle_no);

						//GET PRODUCT VALUE DETAILS 
						$valuedetails=$this->getvaluedetails($row->quotation_id,$row->buyergst,$row->sellergst);

						$data['prdDetails']=$valuedetails;

						

						}

				//echo "<pre>"; print_r($data); exit;

						return $data; 

	}


		function getvaluedetails($quotation_id,$buyergst,$sellergst)
		{

			$d=array();
			

			$res=$this->db->select('a.rebrand_product_id,a.product_id,a.qty,a.agreed_price,a.pack_size,b.instruments_name,b.hsncode,b.unit')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->where('a.quotation_id',$quotation_id)->get();
			if($res->num_rows()>0)
			{
				$i=0;
				foreach($res->result() as $row)
				{
					if($buyergst<>'' && $sellergst <>'')
					{
						$bscode=substr($buyergst,0,2);
						$sscode=substr($sellergst,0,2);
						if($bscode==$sscode)
						{
							$gst=($row->qty*$row->agreed_price)*0.18;
							$cgst=round(($gst/2),2);
							$igst=0;
						}else
						{
							$gst=($row->qty*$row->agreed_price)*0.18;
							$igst=round($gst,2);
							$cgst=0;
						}

					}else
					{
						$igst=0;
						$cgst=0;
					}

								
					$resteuny=$this->db->select('name,shortname')->from('units')->where('shortname',$row->unit)->get();
					if($resteuny->num_rows()>0)
					{
					foreach($resteuny->result() as $rowinvoice);
					$unit=$rowinvoice->name;
					$shortname=$rowinvoice->shortname;
					}else
					{
					$unit='';
					$shortname='';
					}	

					
					
					$convertedqty=$row->qty;
					$unit=$shortname;
			

					$sr=$i+1;
					$sr_no="$sr";
					$total=round(($row->qty*$row->agreed_price)+$igst+$cgst+$cgst,2);

					if($row->rebrand_product_id>0)
					{
						$prdddd=$this->getproduct_name($row->rebrand_product_id);
					}else
					{
						$prdddd=$row->instruments_name;
					}
					$d[$i]['SlNo']=$sr_no;
					$d[$i]['PrdDesc']=$prdddd;
					$d[$i]['IsServc']="N";
					$d[$i]['HsnCd']=$row->hsncode;
					$d[$i]['Qty']=(float)$convertedqty;
					$d[$i]['Unit']=$unit;
					$d[$i]['UnitPrice']=(float)$row->agreed_price;
					$d[$i]['TotAmt']=(float)round($row->qty*$row->agreed_price,2);
					$d[$i]['Discount']=0;
					$d[$i]['PreTaxVal']=(float)round($row->qty*$row->agreed_price,2);
					$d[$i]['AssAmt']=(float)round($row->qty*$row->agreed_price,2);
					$d[$i]['GstRt']=18;
					$d[$i]['IgstAmt']=(float)$igst;
					$d[$i]['CgstAmt']=(float)$cgst;
					$d[$i]['SgstAmt']=(float)$cgst;
					$d[$i]['CesRt']=0;
					$d[$i]['CesAmt']=0;
					$d[$i]['CesNonAdvlAmt']=0;
					$d[$i]['StateCesRt']=0;
					$d[$i]['StateCesAmt']=0;
					$d[$i]['StateCesNonAdvlAmt']=0;
					$d[$i]['OthChrg']=0;
					$d[$i]['TotItemVal']=(float) $total;

				

				$i++;
				}


			}

			


			return $d;

		}

		function getinvoice_no()
		{
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

			$compid=$this->input->post('compid');
	//		echo $compid; exit;

			// $rest=$this->db->select('invoice_starts_from')->from('store_rack_location')->where('id',$compid)->get();
			// if($rest->num_rows()>0)
			// {
			// 	foreach($rest->result() as $row)
			// 	$invoice_starts=$row->invoice_starts_from;
			// }else
			// {
			// 	$invoice_starts=0;
			// }



			 $sql = $this->db->select('invoice_no')
                        ->from('order_punch')
                        ->where('hpcl_billing_company',$compid)
                        ->where('added_on>=',$start_date)
                        ->where('added_on<=',$end_date)	
                       // ->where_not_in('id','5,139,328',false)
                        ->order_by('invoice_no', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->invoice_no+1;

		}else
		{
			$lastorderid=1;
		}

		echo $lastorderid;
	}

	function getorder_details()
	{
		$vno='';
		$id=$this->input->post('id');
		$res=$this->db->select('vehicle_no')->from('order_punch_mailing_details')->where('order_id',$id)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $rowss);
			$vno=$rowss->vehicle_no;
		}

			echo $vno;
	}


	function add_transport_detail()
	{
		$order_id=$this->input->post('order_id');

		if($order_id>0 && $order_id<>'')
		{

			$data=array('transporter_name'=>$this->input->post('tname'),'transporter_id'=>$this->input->post('tid'),'distance'=>$this->input->post('distance'),'vehicle_no'=>$this->input->post('vehicle_no'));

			$this->db->where('id',$order_id);
			$this->db->update('order_punch',$data);


			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Record Updated</div>');
			redirect(page_url.'Billing/pending_for_dispatch');

		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Details not updated. Please try again</div>');
			redirect(page_url.'Billing/pending_for_dispatch');
		}
	}



	function ewaybill()
	{

		$order_id=$this->uri->segment(3);
		$quote_id=$this->uri->segment(4);

		$order_data=$this->get_order_details($order_id);
	
		if(count($order_data)>0)
		{

		
			$sel_gst=$order_data['sellergst'];
		$docdetails=array("Typ"=>"INV","No"=>$order_data['No'],"Dt"=>$order_data['DT']);

		$buyer_Pin=(int) $order_data['Pin'];
		$sellerDtl=array("Gstin"=>$order_data['sellergst'],"LglNm"=>$order_data['LglNm'],"TrdNm"=>$order_data['TrdNm'],"Addr1"=>$order_data['Addr1'],"Addr2"=>$order_data['Addr2'],"Loc"=>$order_data['Loc'],"Pin"=>$buyer_Pin,"Stcd"=>$order_data['Stcd'],"Ph"=>$order_data['Ph'],"Em"=>$order_data['Em']);

		$buyer_Pin=(int) $order_data['buyer_Pin'];

		if($order_data['buyer_Em']=='')
		{
			$buyeremail=null;
		}else{
			$buyeremail=$order_data['buyer_Em'];
		}

		if($order_data['buyer_Ph']=='')
		{
			$buyerphone=null;
		}else
		{
			$buyerphone=$order_data['buyer_Ph'];
		}
		$BuyerDtls=array("Gstin"=>$order_data['buyer_gst'],"LglNm"=>$order_data['buyer_LglNm'],"TrdNm"=>$order_data['buyer_TrdNm'],"Pos"=>$order_data['buyer_Pos'],"Addr1"=>$order_data['buyer_Addr1'],"Addr2"=>$order_data['buyer_Addr2'],"Loc"=>$order_data['buyer_Loc'],"Pin"=>$buyer_Pin,"Stcd"=>$order_data['buyer_Stcd'],"Ph"=>$buyerphone,"Em"=>$buyeremail);

		

		if(count($order_data['prdDetails'])>0)
		{
			$grandtotal_w_gst=array();
			$grandtotal_w_gst[]=0;
			$grandtotal=array();
			$grandtotal[]=0;
			$cgstsum=array();
			$cgstsum[]=0;
			$igstsum=array();
			$igstsum[]=0;
			$hsncode='';
			for($y=0;$y<count($order_data['prdDetails']);$y++)
			{
				
				$slno=(int) $order_data['prdDetails'][$y]['SlNo'];

				$slno="$slno";
			
		// $ItemList[]=array("SlNo"=>$slno,"PrdDesc"=>$order_data['prdDetails'][$y]['PrdDesc'],"IsServc"=>$order_data['prdDetails'][$y]['IsServc'],"HsnCd"=>$order_data['prdDetails'][$y]['HsnCd'],"Qty"=>$order_data['prdDetails'][$y]['Qty'],"Unit"=>$order_data['prdDetails'][$y]['Unit'],"UnitPrice"=>$order_data['prdDetails'][$y]['UnitPrice'],"TotAmt"=>$order_data['prdDetails'][$y]['TotAmt'],"Discount"=>$order_data['prdDetails'][$y]['Discount'],"PreTaxVal"=>$order_data['prdDetails'][$y]['PreTaxVal'],"AssAmt"=>$order_data['prdDetails'][$y]['AssAmt'],"GstRt"=>$order_data['prdDetails'][$y]['GstRt'],"IgstAmt"=>$order_data['prdDetails'][$y]['IgstAmt'],"CgstAmt"=>$order_data['prdDetails'][$y]['CgstAmt'],"SgstAmt"=>$order_data['prdDetails'][$y]['SgstAmt'],"CesRt"=>$order_data['prdDetails'][$y]['CesRt'],"CesAmt"=>$order_data['prdDetails'][$y]['CesAmt'],"CesNonAdvlAmt"=>$order_data['prdDetails'][$y]['CesNonAdvlAmt'],"StateCesRt"=>$order_data['prdDetails'][$y]['StateCesRt'],"StateCesAmt"=>$order_data['prdDetails'][$y]['StateCesAmt'],"StateCesNonAdvlAmt"=>$order_data['prdDetails'][$y]['StateCesNonAdvlAmt'],"OthChrg"=>$order_data['prdDetails'][$y]['OthChrg'],"TotItemVal"=>$order_data['prdDetails'][$y]['TotItemVal']);

					if($order_data['prdDetails'][$y]['SgstAmt']>0){
					$srate=9;
					$crate=9;
					$irate=0;
					}else if($order_data['prdDetails'][$y]['IgstAmt']>0)
					{
					$srate=0;
					$crate=0;
					$irate=9;
					}else
					{
					$srate=0;
					$crate=0;
					$irate=0;
					}
$ItemList[]=array("itemNo" =>$slno, 
                  "productName" => $order_data['prdDetails'][$y]['PrdDesc'], 
                  "productDesc" => $order_data['prdDetails'][$y]['PrdDesc'], 
                  "hsnCode" => $order_data['prdDetails'][$y]['HsnCd'], 
                  "quantity" =>$order_data['prdDetails'][$y]['Qty'], 
                  "qtyUnit" => $order_data['prdDetails'][$y]['Unit'], 
                  "taxableAmount" => $order_data['prdDetails'][$y]['PreTaxVal'], 
                  "sgstRate" => $srate, 
                  "cgstRate" => $crate, 
                  "igstRate" => $irate, 
                  "cessRate" => 0, 
                  "cessNonAdvol" => 0);


			$grandtotal_w_gst[]=$order_data['prdDetails'][$y]['PreTaxVal'];
			$grandtotal[]=$order_data['prdDetails'][$y]['TotItemVal'];
			$cgstsum[]=$order_data['prdDetails'][$y]['SgstAmt'];
			$igstsum[]=$order_data['prdDetails'][$y]['IgstAmt'];
			$hsncode=$order_data['prdDetails'][$y]['HsnCd'];
			}
			



			 $ValDtls=array("AssVal"=>array_sum($grandtotal_w_gst),"IgstVal"=>array_sum($igstsum),"CgstVal"=>array_sum($cgstsum),"SgstVal"=>array_sum($cgstsum),"CesVal"=>0,"StCesVal"=>0,"Discount"=>0,"OthChrg"=>0,"RndOffAmt"=>0,"TotInvVal"=>round(array_sum($grandtotal),2));

		}else
		{
			$ValDtls=array();
			$ItemList=array();
		}

		
		if(array_sum($igstsum)>0)
		{
			$igstonintra="Y";
		}else
		{
			$igstonintra="N";
		}
		

		$sellerDtl=array("Gstin"=>$order_data['sellergst'],"LglNm"=>$order_data['LglNm'],"TrdNm"=>$order_data['TrdNm'],"Addr1"=>$order_data['Addr1'],"Addr2"=>$order_data['Addr2'],"Loc"=>$order_data['Loc'],"Pin"=>$buyer_Pin,"Stcd"=>$order_data['Stcd'],"Ph"=>$order_data['Ph'],"Em"=>$order_data['Em']);


		$seller_Pin=(int) $order_data['Pin'];
		$buyer_Pincode=(int) $order_data['buyer_Pin'];



			$trn_distance=(int) $order_data['trn_distance'];
		$trans_Detail[]=array("userGstin"=>$order_data['sellergst'],"supplyType"=>"O","subSupplyType"=>1,"docType"=>"INV","docNo"=>$order_data['No'],"docDate"=>$order_data['DT'],"transType"=>1,"fromGstin"=>$order_data['sellergst'],"fromTrdName"=>$order_data['TrdNm'],"fromAddr1"=>$order_data['Addr1'],"fromAddr2"=>$order_data['Addr2'],"fromPlace"=>$order_data['Loc'],"fromPincode"=>$seller_Pin,"fromStateCode"=>$order_data['Stcd'],"actualFromStateCode"=>$order_data['Stcd'],"toGstin"=>$order_data['buyer_gst'],"toTrdName"=>$order_data['buyer_TrdNm'],"toAddr1"=>$order_data['buyer_Addr1'],"toAddr2"=>$order_data['buyer_Addr2'],"toPlace"=>$order_data['buyer_Loc'],"toPincode"=>$buyer_Pincode,"toStateCode"=>$order_data['buyer_Stcd'],"actualToStateCode"=>$order_data['buyer_Stcd'],"totalValue"=>array_sum($grandtotal_w_gst),"cgstValue"=>array_sum($cgstsum),"sgstValue"=>array_sum($cgstsum),"igstValue"=>array_sum($igstsum),"cessValue"=>0,"TotNonAdvolVal"=>0,"OthValue"=>0,"totInvValue"=>round(array_sum($grandtotal),2),"transMode"=>1,"transDistance"=>$trn_distance,"transporterName"=>$order_data['trn_name'],"transporterId"=>$order_data['trn_id'],"transDocNo"=>'',"transDocDate"=>'',"vehicleNo"=>$order_data['trn_vehicle_no'],"vehicleType"=>"R","mainHsnCode"=>$hsncode,"itemList"=>$ItemList);



		$finalarray=array("version"=>"1.0.0219","billLists"=>$trans_Detail);
		$encoded=json_encode($finalarray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		//echo $encoded; exit;
		$filename=$sel_gst."_".str_replace('/','',$order_data['DT']).'_'.rand(1000,9999);
		$path=SITE_ROOT."ewaybill/".$filename.".json";
		$fp = fopen($path, 'w');
		fwrite($fp, $encoded);   // here it will print the array pretty
		fclose($fp);

		$downladpath=page_url1."ewaybill/".$filename.".json";
		$filename=$filename.".json";
		header('Content-disposition: attachment; filename='.$filename);
		header('Content-type: application/json');

		echo ($encoded);

	
		}else
		{
			echo "Invalid Link"; exit;
		}

	}

	function getTransportDetails() {
		$order_id = $this->input->post('order_id');
		$transporter_name = '';
		$transporter_id = '';
		$distance = '';
		$vehicle_no = '';
		$vehicle_type = '';
		$destination = '';

		$sql = $this->db->select('a.transporter_name, a.transporter_id, a.distance, a.vehicle_no, b.vehicle_type, b.destination')
						->from('order_punch a')
						->from('order_punch_mailing_details b', 'b.order_id=a.id')
						->where('a.id', $order_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
				$transporter_name = $row->transporter_name;
				$transporter_id = $row->transporter_id;
				$distance = $row->distance;
				$vehicle_no = $row->vehicle_no;
				$vehicle_type = $row->vehicle_type;
				$destination = $row->destination;
		}

		echo $transporter_name.'|'.$transporter_id.'|'.$distance.'|'.$vehicle_no.'|'.$vehicle_type.'|'.$destination;
	}

	public function getBatchCodes() {
			$q = $_GET['q'];
			$query = $this->db->select('id, batch_no')
							  ->from('inventory_batch_no')
							  ->like('batch_no', $q, 'both')
							  ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $row) {
					$json[] = array('id'=>$row->batch_no, 'text'=>$row->batch_no);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
			echo json_encode($json);
	}

	function order_on_hold() {
		$this->load->view('billing/order_on_hold');
	}

		function orders_on_hold_list() {
		$lead_data = array();
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.cancelled', 0)
						  ->where('a.billing', 0)
						  ->where('a.hold_reject',0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}


			
			

				$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

		

			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$invoice_table='';
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

						$invoice_table=$prv_pay[3];

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			if($_SESSION['logged_in']['role']==1)
			{
			$remove_hold="<a href='javascript:;' class='btn btn-success btn-xs' onclick='remove_hold(".$row->id.",".$h_type.")'>Remove Order On Hold</a>";
			}else
			{
				$remove_hold='';
			}


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);


			if($_SESSION['logged_in']['role']==1)
			{
				$reject_hold="<a href='javascript:;' class='btn btn-xs btn-danger' onclick='reject_hold(".$row->id.");'>Reject Hold Request.</a>";
			}else
			{
				$reject_hold='';
			}

		
		if($show==0)
		{


			$previoushold = "<a href='".page_url."Billing/hold_history/".$row->customer_id."' target='_blank'>Hold Release History</a>";
			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
				'hold_type'=>"<strong style='color:red;font-weight:bold;'>".$hold_type."</strong><br></br>".$invoice_table."<br/>".$previoushold,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'remove_hold' => $remove_hold."<br/>".$billing."<br/><br/>".$reject_hold
								 // 'payment_collection' => $payment_collection
								);

			
			 


			$i++;
		}
		}
	}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function orders_on_hold_user() {
		$this->load->view('billing/orders_on_hold_user');
	}

		function orders_on_hold_user_list() {

			$lead_data = array();
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.cancelled', 0)
						  ->where('a.billing', 0)
						  ->where('a.agent',$_SESSION['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {

		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}


			
			

				$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

		

			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			
			$remove_hold="<a href='javascript:;' class='btn btn-success btn-xs' onclick='remove_hold(".$row->id.",".$h_type.")'>Remove Order On Hold</a>";
			


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);


		
		if($show==0 && $h_type==2)
		{

$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
				'hold_type'=>"<strong style='color:red;font-weight:bold;'>".$hold_type."</strong>",
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'remove_hold' => $remove_hold."<br/>".$billing,
								 'tax_invoice'=>$tax_invoice
								 // 'payment_collection' => $payment_collection
								);

			
			 


			$i++;
		}
		}
	}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);

	}

	function remove_hold_remarks() {
		$flag=$this->uri->segment(3);
		if($flag<>'')
		{
			$f=$flag;
		}else
		{
			$f=0;
		}
		$order_id = $this->input->post('order_id');
		
		
		$hold_type = $this->input->post('hold_type');
		$remove_hold_remarks = $this->input->post('remove_hold_remarks');


		$pic = $_FILES['Evidence']['name'];

		if($pic <> '') {
		$files = explode('.', $pic);
		$ext = end($files);
		$newname = time().'.'.$ext;
		move_uploaded_file($_FILES['Evidence']["tmp_name"], UPLOADPATH.'removehold/'.$newname);
		} else {
			$newname = '';
		}
		$data = array(
					  'order_id' => $order_id,
					  'hold_type'=>$hold_type,
					  'remarks' => $remove_hold_remarks,
					  'evidencedata' => $newname,
					  'added_on' => date('Y-m-d H:i:s'),
					  'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		 $this->db->insert('order_remove_hold', $data);
		$this->salescrm->update_order_date($order_id);

		$this->send_notification_order_creater($order_id);

		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Removed from Hold.</div>');
		if($f==0)
		{
		redirect(page_url.'Billing/order_on_hold');
		}else if($f==1)
		{
			redirect(page_url.'Leads/common_approval');
		}else
		{
			redirect(page_url.'Leads/order_on_hold');
		}
	}

	public function billing_from_hpclcompany()
	{
		$this->load->view('billing/billing_from_hpclcompany');
	}


	function billing_from_hpclcompany_list() {
		$lead_data = array();
		$hpcl_company = $this->uri->segment(3);
		$start_date =  date('Y-m-d', strtotime($this->uri->segment(4)));
		$end_date =  date('Y-m-d', strtotime($this->uri->segment(5)));

		$this->db->select('a.vehicle_no,i.company_name,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no, d.billing_mobile_no, d.billing_email, d.vehicle_type, d.destination, b.lead_id, b.company_id, a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('customer_detail i','i.id=b.customer_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 1)
						  // ->where('invoice_no','1226')
						  ->where('a.cancelled', 0);

			if($hpcl_company != 'ALL') {
				$this->db->where('a.hpcl_billing_company', $hpcl_company);
			}

        $query = $this->db->where('a.send_to_tally_On >=', $start_date)
						  ->where('a.send_to_tally_On <=', $end_date)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$i=1;
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
		
				$order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_gst = $this->salescrm->getOrderGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount_wo_gst = $this->salescrm->getOrderAmountWithoutGST($row->quotation_id);

				$arr = explode('|', $order_gst);

				$total_qty = $this->salescrm->getOrderTotalQty($row->quotation_id);
				$rate = $this->salescrm->getOrderRate($row->quotation_id);

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			}  else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($arr[2] == 0) {
				$sale_type = 'LOCAL';
			} else {
				$sale_type = 'INTERSTATE';
			}

			$lead_data[] = array('sr_no'=>$i,
								'billed_date' => date('d-m-Y', strtotime($row->send_to_tally_On)), 
								'company_name' => $row->company_name,
								'voucher_type' => 'Sales',
								'invoice_no' => "<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong>",
								'gst_no' => $row->buyer_gst,
								'pan_no' => $row->pan_no,
								'order_no' => "<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong>",
								'paymentterm' =>$payment_terms,
								'other_references' => '',
								'terms_of_delivery' => '',
								'delivery_note' => '',
								'dispatch_doc_no' => '',
								'dispatch_through' => $row->vehicle_no,
								'destination' => $row->destination,
								'total_qty' => '<strong>'.$total_qty.'</strong>',
								'alt_units' => '',
								//'order_rate' => $rate,
								'order_value' => '<strong>'.$order_amount_wo_gst.'</strong>',
								'gross_total' => '<strong>'.round($order_amount).'</strong>',
								'sale_type' => $sale_type,
								'local_sale' => $order_amount_wo_gst,
								'cgst' => $arr[0],
								'sgst' => $arr[1],
								'igst' => $arr[2]
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

	function filter_hpcl_billing() {
		$user_id = $this->uri->segment(3);
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		redirect(page_url.'Billing/billing_from_hpclcompany/'.$user_id.'/'.$from_date.'/'.$to_date);
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
		->where('a.cancelled', 0)
		->where('b.customer_id',$customer_id)
		->order_by('a.id','DESC')
		->get();

		if($query->num_rows()>0)
		{
			//echo "<pre>"; print_r($query->result()); exit;
			foreach($query->result() as $row)
			{
				$order_value=$this->salescrm->getOrderAmountWithGST($row->quotation_id,$row->gst_no,$row->gst);
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

	function check_for_previous_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('a.order_id !=',$order_id)->where('b.cancelled',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


function check_for_current_pdc($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.received',0)->where('b.cancelled',0)->where('a.order_id',$order_id)->get();
	return $rest->num_rows();
	
	}


	function check_for_previous_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('a.order_id!=',$order_id)->where('b.cancelled',0)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}


	function check_for_current_pdc_hold_due_to_date($customer_id,$order_id)
	{
		$pdc='';
		$pdc1=array();
		$rest=$this->db->select('a.id,a.order_id,b.invoice_no')->from('customer_cheque_details a')->join('order_punch b','a.order_id=b.id')->where('a.customer_id',$customer_id)->where('a.hold_due_to_pdc',1)->where('b.cancelled',0)->where('order_id',$order_id)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$pdc1[]=$row->invoice_no;
			}

			$pdc=implode(',',$pdc1);

		}


		return $pdc;
	}

	function checkfor_hold_release($order_id,$hold_type)
	{
		
		$restey=$this->db->select('id')->from('order_remove_hold')->where('order_id',$order_id)->where('hold_type',$hold_type)->get();
		return $restey->num_rows();


	}

	function order_pending_for_complete_payment()
	{
		$this->load->view('billing/order_pending_for_complete_payment');
	}


		function pending_order_payment_adjustment() {
		$lead_data = array();
		$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.billing', 1)
						  ->where('a.payment', 0)

						  ->order_by('a.id','DESC')
				 		  ->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
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
			$html=$this->getproducts_detail($row->orderpunchquote);
			
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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		
			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";

			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$row111=$this->customer_payments_against_order($row->id);
				if($row111>0)
				{

					$this_order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$post_tds=$order_amount=$getorderamountaftertds+$gst_amount;
					}else
					{
						$post_tds=$this_order_amount;
					}
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			$partial=$this->customer_previous_payment($row->id);

			$due=$post_tds-$partial;


			$adjust="<a href='javascript:;' onclick='adjust_payment(".$row->id.",".$due.");' class='btn btn-warning'>Adjust Payment</a>";
			$lead_data[] = array('sr_no'=>$i,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'paymentterm' =>$payment_terms,
								 'basic_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$basic_order_amount."</strong>",
								 'gst_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$gst_amount."</strong>",
								 'total_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$this_order_amount."</strong>",
								 'order_amount_after_tds' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$post_tds."</strong>",
								 'payment_recvd' =>"<strong style='color:green;font-weight:bold;font-size:16px;'>".$partial."</strong>",
								 'payment_due' =>"<strong style='color:red;font-weight:bold;font-size:16px;'>".$due."</strong>",
								 'adjustment' => $adjust
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



		function customer_payments_against_order($order_id)
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


	function getOrderAmountWithoutGST($quotation, $buyer_gst, $seller_gst)
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

		    $final_amt = $total_price + $total_gst;

		    return $final_amt;
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

	function adjust_order_payment()
	{
		$order_id=$this->input->post('order_id');
		$adjust=$this->input->post('adjust');
		$remarks=$this->input->post('remarks');
		if($adjust==2)
		{

					$photo=$_FILES['debit_note']['name'];
					if($photo <> '') {
					$image1 = explode('.',$photo);
					$cat_image = end($image1);
					$instrumentimg_spec = time().'.'.$cat_image;
					move_uploaded_file($_FILES["debit_note"]["tmp_name"],UPLOADPATH.'debit_note/'.$instrumentimg_spec);
					} else {
					$instrumentimg_spec = "";
					}
			
				$approved=0;
				$approvedOn='0000-00-00 00:00:00';
				$approvedBy=0;

				$data=array('adjustment'=>1,'adjustment_type'=>$adjust,'debit_note'=>$instrumentimg_spec,'adjustment_remarks'=>$remarks,'payment'=>1,'adjustmentOn'=>date('Y-m-d H:i:s'),'adjustmentBy'=>$_SESSION['logged_in']['user_id'],'adjustment_approval'=>$approved,'adjustment_approvalOn'=>$approvedOn,'adjustment_approvalBy'=>$approvedBy);
				$this->db->where('id',$order_id);
				$this->db->update('order_punch',$data);

		}else
		{

			if($adjust==1)
			{
				$approved=1;
				$approvedOn=date('Y-m-d H:i:s');
				$approvedBy=$_SESSION['logged_in']['user_id'];
			}else
			{
				$approved=0;
				$approvedOn='0000-00-00 00:00:00';
				$approvedBy=0;
			}

				$data=array('adjustment'=>1,'adjustment_type'=>$adjust,'adjustment_remarks'=>$remarks,'payment'=>1,'adjustmentOn'=>date('Y-m-d H:i:s'),'adjustmentBy'=>$_SESSION['logged_in']['user_id'],'adjustment_approval'=>$approved,'adjustment_approvalOn'=>$approvedOn,'adjustment_approvalBy'=>$approvedBy);
				$this->db->where('id',$order_id);
				$this->db->update('order_punch',$data);

		}


		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Payment Adjusted Updated</div>');
			redirect(page_url.'Billing/order_pending_for_complete_payment');


	}

		function order_pending_for_complete_payment_history()
	{
		$this->load->view('billing/order_pending_for_complete_payment_history');
	}



	function pending_order_payment_adjustment_history() {
		$lead_data = array();
		$query = $this->db->select('a.adjustment_approval_remarks,a.adjustment_approval,a.adjustment_approvalOn,a.adjustment_approvalBy,a.adjustment_type,a.debit_note,a.adjustment_remarks,a.adjustmentBy,a.adjustmentOn,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst,i.first_name as adjustf,i.last_name as adjustl,j.first_name as adjustappf,j.last_name as adjustappl')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->join('system_users i','i.user_id=a.adjustmentBy')
						  ->join('system_users j','j.user_id=a.adjustment_approvalBy')
						  ->where('a.billing', 1)
						  ->where('a.adjustment', 1)
						  ->where('a.adjustment_approval>', 0)

						  ->order_by('a.id','DESC')
				 		  ->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
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
			$html=$this->getproducts_detail($row->orderpunchquote);
			
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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		
			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";

			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$row111=$this->customer_payments_against_order($row->id);
				if($row111>0)
				{

					$this_order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$post_tds=$order_amount=$getorderamountaftertds+$gst_amount;
					}else
					{
						$post_tds=$this_order_amount;
					}
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			$partial=$this->customer_previous_payment($row->id);

			$due=$post_tds-$partial;

		

			if($row->adjustment_type==1)
			{
				$adjust_ty="Round Off";
				$db="Remarks- ".$row->adjustment_remarks;
			}else if($row->adjustment_type==2)
			{
				$adjust_ty="Debit Note.";
				$db='<a href="'.page_url1.'image_bank/debit_note/'.$row->debit_note.'" download>Download DB</a><br/>';
				$db.="Remarks- ".$row->adjustment_remarks;
			}else
			{
				$adjust_ty="Bad Debt";
				$db="Remarks- ".$row->adjustment_remarks;
			}

			if($row->adjustment_approval==1)
			{
				$app="<strong style='color:green;font-weight:bold;font-size:14px;'>Approved</strong>";
				$rej='';
				$by=$row->adjustappf." ".$row->adjustappl;
			}else 
			{
					$app="<strong style='color:red;font-weight:bold;font-size:14px;'>Rejected</strong>";
					$rej=$row->adjustment_approval_remarks;
				$by=$row->adjustappf." ".$row->adjustappl;
			}

			$adjustment_details="<strong style='color:red;font-weight:bold;color:red;'>".$adjust_ty."<br/><br/>".$db."<br/><br/>".$row->adjustf." ".$row->adjustl."<br/><br/>".date('d-M-y',strtotime($row->adjustmentOn))."</strong>";
			$lead_data[] = array('sr_no'=>$i,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'paymentterm' =>$payment_terms,
								 'basic_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$basic_order_amount."</strong>",
								 'gst_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$gst_amount."</strong>",
								 'total_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$this_order_amount."</strong>",
								 'order_amount_after_tds' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$post_tds."</strong>",
								 'payment_recvd' =>"<strong style='color:green;font-weight:bold;font-size:16px;'>".$partial."</strong>",
								 'payment_due' =>"<strong style='color:red;font-weight:bold;font-size:16px;'>".$due."</strong>",
								 'adjustment' => $adjustment_details,
								 'approval_details'=>$app."<br/>".$rej."<br/>".$by,
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


	function tds_input_report()
	{

		$this->load->view('billing/tds_input_report');
	}

	function tds_input_report_list()
	{
		$sdate=date('Y-m-d',strtotime($this->uri->segment(3)))." 00:00:00";
		$edate=date('Y-m-d',strtotime($this->uri->segment(4)))." 23:59:59";
		$user=$this->uri->segment(5);
		$company=$this->uri->segment(6);

			$lead_data = array();

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


				$data=$this->getCustomerdetail($row->customer_id);
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
			
			

			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
				$html=$this->getproducts_detail($row->orderpunchquote);

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
	
			$lead_data[] = array('sr_no'=>$i,
									'billing_company'=>$billcompany,
									'lead_manager'=>'',
									'company_name'=>$companyname,
									'customer_name'=>$customer_name,
									'invoice_no'=>$row->invoice_no,
									'product'=>$html,
									'basic_order_amount' =>$basic_order_amount,
									'gst_order_amount' =>$gst_amount,
									'order_amount' =>$this_order_amount,
									'tds_per' =>$tds_per."%",
									'tds_input' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$getorderamountaftertds."</strong>"
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

	function filter_tds_input()
	{
		$a=date('Y-m-d',strtotime($this->input->post('from_date')));
			$b=date('Y-m-d',strtotime($this->input->post('to_date')));
			$c=$this->input->post('user');
			$d=$this->input->post('company');

		redirect(page_url.'Billing/tds_input_report/'.$a."/".$b."/".$c."/".$d);
	}

 
				
	function cancel_order()
	{
		$flag=$this->uri->segment(3);
		$order_id=$this->input->post('cancell_order_id');
		$cancell_rmk=$this->input->post('cancell_rmk');

		$data=array('cancelled'=>1,'cancelledOn'=>date('Y-m-d H:i:s'),'cancel_reason'=>$cancell_rmk,'cancelled_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$order_id);
		$this->db->update('order_punch',$data);

if($flag==1)
{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Cancelled</div>');
		redirect(page_url.'Billing/billing_history');

}else
{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Cancelled</div>');
		redirect(page_url.'Billing/order_pending_for_billing_so');
}
	}

	function cancelled_history()
	{
		$this->load->view('billing/cancelled_history');
	}

	function cancelled_history_list() {
		$lead_data = array();
		$query = $this->db->select('a.sales_order_no,a.cancelled,a.cancelledOn,cancelled_by,cancel_reason,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id,i.first_name as cfname,i.last_name as clname,j.first_name as createdf,j.last_name as createdl')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users j','j.user_id=a.added_by')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->join('system_users i', 'i.user_id=a.cancelled_by')
						  ->where('a.cancelled', 1)
						  ->order_by('a.id','DESC')
				 		  ->get();
 
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";

			if($_SESSION['logged_in']['role']==1)
			{
				if($row->invoice_no<>'' && $row->invoice_no>0 && $row->send_to_tally==1)
				{
				$edit="<a href='".page_url."Customer/edit_order_from_cancell/".$row->id."/".$row->orderpunchquote."' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
				}else
				{
					$edit="";
				}
			}else
			{
				$edit='';
			}
			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
						'agent'=>$row->first_name." ".$row->last_name,
						'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->sales_order_no."</strong><br/>".$row->invoice_no."<br/>".date('d-M-Y',strtotime($row->added_on))."<br/><br/>".$row->createdf." ".$row->createdl."<br/><br/>".$edit,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'cancel_reason'=>"<strong style='color:red;font-weight:bold;'>".$row->cancel_reason."</strong>",
								 'cancel_by'=>$row->cfname." ".$row->clname,
								 'cancel_on'=>date('d-M-Y',strtotime($row->cancelledOn))
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function vehicle_sale_register()
	{
		$this->load->view('billing/vehicle_sale_register');
	}


	function vehicle_register() {

		$vehicle=$this->uri->segment(3);
		$type=$this->uri->segment(6);

		if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
		{
			$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		$end_date=date('Y-m-d',strtotime($this->uri->segment(5)));
		}else
		{
			$start_date='';
			$end_date='';
		}
		
		$lead_data = array();
	$this->db->select('a.transporter_type,a.transporter_name,a.transporter_address,a.rate_type,a.rate,a.transporter_id,a.distance,a.vehicle_no,a.dispatched_on,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.send_to_tally', 1);
						  // ->where('a.dispatch', 1);

						  if($type<>'ALL')
						  {
						  	if($type==1)
							  	{
							  		$this->db->where('transporter_type',1);
										if($vehicle<>'' && $vehicle<>'ALL')
										{
										$this->db->where('a.vehicle_no',$vehicle);
										}
							  	}else
							  	{
							  		$this->db->where('transporter_type',2);
							  		if($vehicle<>'' && $vehicle<>'ALL')
										{
											$this->db->where('a.transporter_name',$vehicle);
										}

							  	}
							}

						  if($start_date<>'' && $end_date<>'')
						  {
						  	$this->db->where('a.send_to_tally_On>=',$start_date);
						  	$this->db->where('a.send_to_tally_On<=',$end_date);
						  }
							$query = $this->db->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			//$html=$this->getproducts_detail($row->orderpunchquote);
			$html=$this->getproducts_detail_with_qty($row->orderpunchquote);
			$h = explode('|',$html);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			if($row->distance==0)
			{
			$eway_bill = "<a href='javascript:;'  onclick='open_modal(".$row->id.")' class='btn btn-warning btn-xs'>Generate E-way Bill</a>";
			}else
			{
				$eway_bill="<a href='".page_url."Billing/ewaybill/".$row->id."/".$row->orderpunchquote."'  class='btn btn-success btn-xs'>Download E-way Bill</a>";
			}
			

			if($row->transporter_type==1)
			{
				$ttype="Owned";
				$tname='';
			}else if($row->transporter_type==2)
			{
				$ttype="Hired";
				$tname=$this->salescrm->get_transporter_name($row->transporter_name);
			
			}else
			{
				$ttype="";
				$tname='';
			}

			if($row->rate_type==1)
			{
				$rtype="Per LTR";
				$totalamount= $h[1]*$row->rate;

			}else if($row->rate_type==2)
			{
				$rtype="Fixed";
				$totalamount= $row->rate;

			}else
			{
				$rtype="";
				$totalamount=0;
			}
			$lead_data[] = array('sr_no'=>$i,
				'dispatch_date'=>date('d-M-Y',strtotime($row->dispatched_on)), 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong>",
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$h[0],
								 'shipaddress' =>$shipdetail,
								 'vehicle_details' =>"<strong style='color:red;font-weight:bold;'>".$row->vehicle_no."</strong>",
								 'eway_bill' => $eway_bill,
								 'tax_invoice' => $tax_invoice,
								 'ttype'=>$ttype,
								 'tname'=>$tname,
								 'trate'=>$rtype,
								 'rate'=>$row->rate,
								 'totalqty'=>$h[1]." ltr",
								 'totalamount'=>"<strong style='color:red;font-weight:bold; font-size:16px;'><i class='fa fa-inr' aria-hidden='true'></i>".$totalamount."</strong>"
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function filter_vehicle_sale_report()
	{
		$type=$this->input->post('type');
		if($type=="ALL")
		{
			$vehicle="ALL";
		}else if($type==1)
		{
			$vehicle=base64_encode($this->input->post('vehicle'));

		}else if($type==2)
		{
				$vehicle=$this->input->post('transporter');
		}else
		{
			$vehicle="ALL";
		}

		
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		redirect(page_url.'Billing/vehicle_sale_register/'.$vehicle.'/'.$from_date.'/'.$to_date.'/'.$type);
	}

	function getVehicles() {
		$q = $_GET['q'];
		$sql = $this->db->select('id, name')
						->from('our_vehicles')
						->get();

		if($sql->num_rows()>0) {
			foreach($sql->result() as $row) {
				$json[] = array('id'=>$row->name, 'text'=>$row->name);
				}
		} else {
			$json[] = array('id'=>"", 'text'=>"No Data Available");
		}
				
		echo json_encode($json);
	}

	function get_customer_detail_with_payment()
	{
		
		$customer='';
		$payment='';
		$id=$this->input->post('customer');
		$row=$this->db->select('company_name,payment_type,credit_days')->from('customer_detail')->where('id',$id)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $rows);

			$customer=$rows->company_name;
			if($rows->payment_type==2)
			{
				$payment="Cash";
			}else if($rows->payment_type==3)
			{
				$payment="Online";
			}else if($rows->payment_type==4)
			{
				$payment="PDC";
			}else if($rows->payment_type==5)
			{
				$payment="Credit-".$rows->credit_days." Days";
			}else if($rows->payment_type==6)
			{
				$payment="Advance-".$rows->credit_days." Days";
			}else
			{
				$payment='';

			}
		}

		echo $payment."|".$customer;
	}

	function raise_payment_change_request()
	{
		$customer=$this->input->post('customer');
		$type=$this->input->post('type');
		$credit_days=$this->input->post('credit_days');

		$data=array('customer_id'=>$customer,'payment_type'=>$type,'credit_days'=>$credit_days,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);

		$this->db->insert('payment_change_req',$data);

		echo true;


	}



		function pending_order_payment_adjustment_for_approval() {
		$lead_data = array();
		$query = $this->db->select('a.adjustment,a.adjustment_type,a.debit_note,a.adjustment_remarks,a.adjustmentBy,a.adjustmentOn,a.adjustment_approval,a.adjustment_approvalOn,a.adjustment_approvalBy,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst,i.first_name as adjustf,i.last_name as adjustl')
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
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
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
			$html=$this->getproducts_detail($row->orderpunchquote);
			
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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		
			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$row111=$this->customer_payments_against_order($row->id);
				if($row111>0)
				{

					$this_order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$post_tds=$order_amount=$getorderamountaftertds+$gst_amount;
					}else
					{
						$post_tds=$this_order_amount;
					}
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			$partial=$this->customer_previous_payment($row->id);

			$due=$post_tds-$partial;

			if($row->adjustment_type==1)
			{
				$adjust_ty="Round Off";
				$db="Remarks- ".$row->adjustment_remarks;
			}else if($row->adjustment_type==2)
			{
				$adjust_ty="Debit Note.";
				$db='<a href="'.page_url1.'image_bank/debit_note/'.$row->debit_note.'" download>Download DB</a><br/>';
				$db.="Remarks- ".$row->adjustment_remarks;
			}else
			{
				$adjust_ty="Bad Debt";
				$db="Remarks- ".$row->adjustment_remarks;
			}

			$adjustment_details="<strong style='color:red;font-weight:bold;color:red;'>".$adjust_ty."<br/><br/>".$db."<br/><br/>".$row->adjustf." ".$row->adjustl."<br/><br/>".date('d-M-y',strtotime($row->adjustmentOn))."</strong>";


			$action="<a href='javascript:;' class='btn btn-warning' onclick='payment_adjustment_decision(".$row->id.",".$due.")'>Approve/Reject</a>";
			$lead_data[] = array('sr_no'=>$i,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'paymentterm' =>$payment_terms,
								 'basic_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$basic_order_amount."</strong>",
								 'gst_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$gst_amount."</strong>",
								 'total_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$this_order_amount."</strong>",
								 'order_amount_after_tds' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$post_tds."</strong>",
								 'payment_recvd' =>"<strong style='color:green;font-weight:bold;font-size:16px;'>".$partial."</strong>",
								 'payment_due' =>"<strong style='color:red;font-weight:bold;font-size:16px;'>".$due."</strong>",
								 'adjustment' => $adjustment_details,
								 'approval_send' => date('d-M-Y H:i',strtotime($row->adjustmentOn)),
								 'action'=>$action
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

	function getcustomerdata()
	{
		$customer='';
		$invo='';
		$order_id=$this->uri->segment(3);
		$r=$this->db->select('a.invoice_no,c.company_name')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->join('customer_detail c','b.customer_id=c.id')->where('a.id',$order_id)->get();
		if($r->num_rows()>0)
		{
			foreach($r->result() as $ro);

			$customer=$ro->company_name;
			$invo=$ro->invoice_no;
		}

		echo $customer."|".$invo;
	}

	function payment_history_data()
	{
		$this->load->view('billing/payment_history_data');
	}

	function payment_history()
	{

		$lead_data=array();
		$companyname=$this->uri->segment(3);
		$start_date=$this->uri->segment(4);
		$enddate=$this->uri->segment(5);
		$type=$this->uri->segment(6);
		$bank=$this->uri->segment(7);
		$customer=$this->uri->segment(8);
		 $this->db->select('b.bank,a.payment_date,a.id as customerpart,b.type as billtype,b.bills,b.id,a.payment_id,a.payment_type,a.cheque_no,a.cheque_date,a.neft_trans_no,a.amount,b.addedOn,b.addedBy,c.company_name')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->join('customer_detail c','b.customer_id=c.id');
			if($companyname<>'' && $companyname<>'ALL')
			{
				$this->db->where('b.hpcl_billing_company',$companyname);
			}

			if($bank<>'' && $bank<>'ALL')
			{
				$this->db->where('b.bank',$bank);
			}

			if($customer<>'' && $customer<>'ALL')
			{
				$this->db->where('b.customer_id',$customer);
			}


			if($type<>'' && $type<>'ALL')
			{
				$this->db->where('a.payment_type',$type);
			}

			$this->db->where('a.payment_date>=',date('Y-m-d',strtotime($start_date)));
			$this->db->where('a.payment_date<=',date('Y-m-d',strtotime($enddate)));
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
    		//."|".$row->customerpart

    			$bank11=$this->getbankaccount($row->bank);
				$lead_data[] = array('sr_no'=>$i."<br/>".$row->bank,
				'payment_date'=>date('d-M-Y',strtotime($row->payment_date)),
				'customer'=>$row->company_name,
				'bank_account'=>$bank11,
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

	function filter_payment_history_data()
	{
		$our_company=$this->input->post('our_company');
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$type=$this->input->post('type');
		$bank=$this->input->post('bank');
		$customer=$this->input->post('customer');
		redirect(page_url.'Billing/payment_history_data/'.$our_company.'/'.$from_date.'/'.$to_date.'/'.$type.'/'.$bank.'/'.$customer);
	}

	function purchase_density_approval()
	{
			$lead_data=array();
		$rowss=$this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->get();
if($rowss->num_rows()>0)
{
	$i=1;
	foreach($rowss->result() as $row)
	{
	
	$action="<a href='javascript:;' class='btn btn-warning' onclick='approve_density(".$row->id.")'>Approve/Reject</a>";

		$lead_data[] = array('sr_no'=>$i,
								 'purchase_date'=>date('d-M-Y',strtotime($row->currentdate)),
								 'party'=>$row->vname,

								 'product'=>$row->instruments_name,
								 'qty' =>$row->original_qty." KG",
								 'density' =>"<strong style='color:red;font-weight:bold;'>".$row->density."</strong>",
								 'converted' =>$row->qty." LTR",
								 'approve' =>$action
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


	function get_inventory_details()
	{
		$product='';
		$pur_qty='';
		$density='';
		$converted='';
		$prdid=0;
		$id=$this->uri->segment(3);
		$rowss=$this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.id',$id)->get();
			if($rowss->num_rows()>0)
			{
				foreach($rowss->result() as $row);
				$product=$row->instruments_name;
				$pur_qty=$row->original_qty;
				$density=$row->density;
				$converted=$row->qty;
				$prdid=$row->product;

			}

			echo $product."|".$pur_qty."|".$density."|".$converted."|".$prdid;
	}

	function approve_density()
	{
		$inventory_detail_id=$this->input->post('inventory_detail_id');
		$prd_name_id=$this->input->post('prd_name_id');
		$original_density=$this->input->post('original_density');
		$density=$this->input->post('density');
		$pur_qty=$this->input->post('pur_qty');
		$con_qty=$this->input->post('con_qty');
		$a=0;
		// if($density!=$original_density)
		// {
		// 			$des=$density;
		// 			$mass=$pur_qty;
		// 			if($des>0)
		// 			{
		// 			$prd_qty=$mass/$des;
		// 			}else
		// 			{
		// 				$prd_qty=$con_qty;
		// 			}

		// 		$data=array('density'=>$des,'qty'=>$prd_qty);
		// 		$this->db->where('id',$inventory_detail_id);
		// 		$this->db->update('inventory_details',$data);

		// 		$a=1;
		// }

			$data1=array('density_approved'=>1,'density_approvedOn'=>date('Y-m-d H:i:s'),'density_approvedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->where('id',$inventory_detail_id);
				$this->db->update('inventory_details',$data1);


				//$current_stock=$this->getcurrent_stock($prd_name_id);
				// /** REDUCE INCREASE STOCK **/
				// if($a==1)
				// {

				// 	/** NEW CINVERSION **/
				// 	$des=$density;
				// 	$mass=$pur_qty;
				// 	if($des>0)
				// 	{
				// 	$prd_qty_new=$mass/$des;
				// 	}else
				// 	{
				// 		$prd_qty_new=$con_qty;
				// 	}


				// 	/** OLD CONVERSION **/

				// 	$des1=$original_density;
				// 	$mass=$pur_qty;
				// 	if($des1>0)
				// 	{
				// 	$prd_qty_old=$mass/$des1;
				// 	}else
				// 	{
				// 		$prd_qty_old=$con_qty;
				// 	}

				// 	/** END **/

				// 	if($prd_qty_old>$prd_qty_new)
				// 	{
				// 		$diff=$prd_qty_old-$prd_qty_new;

				// 		/** MINUS **/
				// 		$current_stock=$current_stock-$diff;
				// 		if($current_stock<0)
				// 		{
				// 			$current_stock=0;
				// 		}


				// 	}else if($prd_qty_old<$prd_qty_new)
				// 	{
				// 			$diff=$prd_qty_new-$prd_qty_old;
				// 				/** PLUS **/
				// 		$current_stock=$current_stock+$diff;
				// 		if($current_stock<0)
				// 		{
				// 			$current_stock=0;
				// 		}

				// 	}else
				// 	{

				// 	}

				// 	/** update stock **/
				// 	$cudata=array('stock'=>$current_stock);
				// 	$this->db->where('id',$prd_name_id);
				// 	$this->db->update('presto_instruments',$cudata);

				// }
				// /** END **/


				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Record Approved</div>');
				redirect(page_url.'Leads/common_approval');

	}


	function getcurrent_stock($prdid)
	{
		$st=0;
		$rest=$this->db->select('stock')->from('presto_instruments')->where('id',$prdid)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row);
			$st=$row->stock;

		}

		return $st;
	}



		function pending_order_payment_adjustment_for_approval_user() {
		$lead_data = array();
		$query = $this->db->select('a.adjustment,a.adjustment_type,a.debit_note,a.adjustment_remarks,a.adjustmentBy,a.adjustmentOn,a.adjustment_approval,a.adjustment_approvalOn,a.adjustment_approvalBy,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst,i.first_name as adjustf,i.last_name as adjustl')
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
						  ->where('a.adjustmentBy',$_SESSION['logged_in']['user_id'])

						  ->order_by('a.id','DESC')
				 		  ->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
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
			$html=$this->getproducts_detail($row->orderpunchquote);
			
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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		
			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
		$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$row111=$this->customer_payments_against_order($row->id);
				if($row111>0)
				{

					$this_order_amount = $this->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$basic_order_amount = $this->getOrderAmountWithoutGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					$gst_amount = $this->getOrderGSTAmount($row->quotation_id, $row->buyer_gst, $row->seller_gst);
					if($tds_appl==1)
					{
						$tds_fac=$tds_per/100;
						$getorderamountaftertds=$basic_order_amount*$tds_fac;
						//echo $basic_order_amount."<br/>".$getorderamountaftertds; exit;
						$getorderamountaftertds=$basic_order_amount-$getorderamountaftertds;
						$post_tds=$order_amount=$getorderamountaftertds+$gst_amount;
					}else
					{
						$post_tds=$this_order_amount;
					}
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			$partial=$this->customer_previous_payment($row->id);

			$due=$post_tds-$partial;

			if($row->adjustment_type==1)
			{
				$adjust_ty="Round Off";
				$db="Remarks- ".$row->adjustment_remarks;
			}else if($row->adjustment_type==2)
			{
				$adjust_ty="Debit Note.";
				$db='<a href="'.page_url1.'image_bank/debit_note/'.$row->debit_note.'" download>Download DB</a><br/>';
				$db.="Remarks- ".$row->adjustment_remarks;
			}else
			{
				$adjust_ty="Bad Debt";
				$db="Remarks- ".$row->adjustment_remarks;
			}

			$adjustment_details="<strong style='color:red;font-weight:bold;color:red;'>".$adjust_ty."<br/><br/>".$db."<br/><br/>".$row->adjustf." ".$row->adjustl."<br/><br/>".date('d-M-y',strtotime($row->adjustmentOn))."</strong>";


			$action="<a href='javascript:;' class='btn btn-warning' onclick='payment_adjustment_decision(".$row->id.",".$due.")'>Approve/Reject</a>";
			$lead_data[] = array('sr_no'=>$i,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'paymentterm' =>$payment_terms,
								 'basic_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$basic_order_amount."</strong>",
								 'gst_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$gst_amount."</strong>",
								 'total_order_amount' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$this_order_amount."</strong>",
								 'order_amount_after_tds' =>"<strong style='color:blue;font-weight:bold;font-size:16px;'>".$post_tds."</strong>",
								 'payment_recvd' =>"<strong style='color:green;font-weight:bold;font-size:16px;'>".$partial."</strong>",
								 'payment_due' =>"<strong style='color:red;font-weight:bold;font-size:16px;'>".$due."</strong>",
								 'adjustment' => $adjustment_details,
								 'approval_send' => date('d-M-Y H:i',strtotime($row->adjustmentOn)),
								 'action'=>$action
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




		function orders_on_hold_list_user() {
		$lead_data = array();

			$query = $this->db->select('a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.cancelled', 0)
						  ->where('a.billing', 0)
						  ->where('a.agent',$_SESSION['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();



		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}


			
			

				$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

		

			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			if($_SESSION['logged_in']['role']==1)
			{
			$remove_hold="<a href='javascript:;' class='btn btn-success btn-xs' onclick='remove_hold(".$row->id.",".$h_type.")'>Remove Order On Hold</a>";
			}else
			{
				$remove_hold='';
			}


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);


		
		if($show==0)
		{

			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
				'hold_type'=>"<strong style='color:red;font-weight:bold;'>".$hold_type."</strong>",
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'remove_hold' => $remove_hold."<br/>".$billing
								 // 'payment_collection' => $payment_collection
								);

			
			 


			$i++;
		}
		}
	}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}



	function purchase_density_approval_user()
	{
			$lead_data=array();
		$rowss=$this->db->select('a.id,a.original_qty,a.inventory_id,a.product,a.qty,a.density,b.currentdate,a.pack_size,a.rate,a.density_approved,b.party,d.name as vname,c.instruments_name,e.shortname')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->join('presto_instruments c','a.product=c.id')->join('vendors d','b.party=d.id')->join('units e','a.pack_size=e.id')->where('a.density_approved',0)->where('a.addedBy',$_SESSION['logged_in']['user_id'])->get();
if($rowss->num_rows()>0)
{
	$i=1;
	foreach($rowss->result() as $row)
	{
	
	$action="<a href='javascript:;' class='btn btn-warning' onclick='approve_density(".$row->id.")'>Approve/Reject</a>";

		$lead_data[] = array('sr_no'=>$i,
								 'purchase_date'=>date('d-M-Y',strtotime($row->currentdate)),
								 'party'=>$row->vname,

								 'product'=>$row->instruments_name,
								 'qty' =>$row->original_qty." KG",
								 'density' =>"<strong style='color:red;font-weight:bold;'>".$row->density."</strong>",
								 'converted' =>$row->qty." LTR",
								 'approve' =>$action
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


	function getuser_details($user_id)
	{
		$email='';
		$restey=$this->db->select('email')->from('system_users')->where('user_id',$user_id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $rowss);
			$email=$rowss->email;
		}

		return $email;
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

	function get_batch_code_files($quoteid)
	{
		//echo $quoteid; exit;
			$html='';
			$c='';
			$html1='';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quoteid)->get();
			if($res->num_rows()>0)
			{
			$j=1;
				foreach($res->result() as $product){
					if($product->batch_code<>'')
					{
						if($quoteid==1215)
						{
							$tete="test_report";
						}else
						{
							$tete="test_report";
						}
						

						if($product->new_batch_code==1)
						{
							$batch_no=$this->getbatch_no($product->batch_code);
						$rt=$this->db->select($tete.',report_file')->from('inventory_batch_no')->where('batch_no',$product->batch_code)->get();
						if($rt->num_rows()>0)
						{
							foreach($rt->result() as $rtow);
							if($rtow->test_report==1 && $rtow->report_file<>'')
							{
							if(file_exists(assets_upload."test_report/".$rtow->report_file))
							{
								$c="<a href='".page_url1."assets/test_report/".$rtow->report_file."'>".$batch_no."</a><br/>";
							}else
							{
								$c=$batch_no."<br/>";

							}

							}else
							{
								$c=$batch_no."<br/>";
							}

						}else{
							$c=$batch_no."<br/>";
						}

					}else
					{

						$rt=$this->db->select($tete.',report_file,batch_no')->from('inventory_batch_no')->where('id',$product->batch_code)->get();
						if($rt->num_rows()>0)
						{
							foreach($rt->result() as $rtow);
							if($rtow->test_report==1 && $rtow->report_file<>'')
							{
							if(file_exists(assets_upload."test_report/".$rtow->report_file))
							{
								$c="<a href='".page_url1."assets/test_report/".$rtow->report_file."'>".$product->batch_code."</a><br/>";
							}else
							{
								$c=$rtow->batch_no."<br/>";

							}

							}else
							{
								$c=$rtow->batch_no."<br/>";
							}

						}else{
							$c="Batch Not Found<br/>";
						}

					}



					}


					$html.=$c;

				}

			}else
			{
				$html='';
			}

			return $html;
	}


	function getcustomer_order_detail_whatsapp($quoteid)
	{
		$table='';
		$filepath = '';
		$sql=$this->db->select('a.product_id,a.qty,a.batch_code, b.instruments_name, b.pack_size, c.shortname')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c','c.id=a.pack_size', 'left')->where('a.quotation_id',$quoteid)->get();
		if($sql->num_rows()>0)
		{
			$table.='';
		foreach($sql->result() as $row)
		{
	
		$table.="*".$row->instruments_name."&nbsp;&nbsp;&nbsp;&nbsp;".$row->qty." ".$row->shortname."*"."\n\n";
		}

		}

		return $table;

	}

    function unfollow_customer()
  {
    $this->load->view('billing/unfollow_customer');
  }



  function hpcl_ledger()
  {
  	$this->load->view('billing/hpcl_ledger');
  }


  function orders_on_hold_list_history() {

  	//$this->db->query("SET SQL_BIG_SELECTS=1");
  	$billing='';
		$lead_data = array();
		 $this->db->select('x.hold_type,x.remarks as hold_release_remarks, x.evidencedata, x.added_on as releasedate,x.added_by as releasedby,y.first_name as freleasedby,y.last_name as lreleasedby,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
									->from('order_remove_hold x')
									->join('order_punch a','x.order_id=a.id')
									->join('order_punch_mailing_details d','a.id=d.order_id')
									->join('order_punch_tax_details f','a.id=f.order_id')
								
									->join('system_users h','h.user_id=a.agent')
									->join('system_users y','x.added_by=y.user_id')
									->join('order_punch_tax_details e','a.id=e.order_id')
									->join('customer_quotation b','b.id=a.quotation_id')
									->join('store_rack_location c', 'c.id=b.company_id', 'left');
									if($this->uri->segment(3)<>''){
										$this->db->where('b.customer_id',$this->uri->segment(3));
									}

									$query = $this->db->order_by('a.id','DESC')->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}


			
			

				$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
				$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

		

			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
						}

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			if($_SESSION['logged_in']['role']==1)
			{
			$remove_hold="<a href='javascript:;' class='btn btn-success btn-xs' onclick='remove_hold(".$row->id.",".$h_type.")'>Remove Order On Hold</a>";
			}else
			{
				$remove_hold='';
			}


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			if($row->hold_type==1)
			{
				$ho="CREDIT LIMIT WAS EXCEEDED";
			}else if($row->hold_type==2)
			{
				$ho="PDC WAS NOT RECIEVED";
			}else if($row->hold_type==2)
			{
				$ho="PDC DATE EXCEEDED THE SET LIMIT";
			}else 
			{
				$ho="PREVIOUS INVOICE CREDIT DAYS WAS OVER AND PAYMENT WAS PENDING";
			}

			if($row->evidencedata<>''){
			$evidencedata = "<a href='".UPLOADPATH.$row->evidencedata."' download>Download Evidence</a>";
		}else{
			$evidencedata="";
		}
			$lead_data[] = array('sr_no'=>$i,
				'source'=>'', 
				'hold_type'=>"<strong style='color:red;font-weight:bold;'>".$ho."</strong>",
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'hold_remove_reason' =>"<strong style='color:red;font-weight:bold;'>".$row->hold_release_remarks."</strong><br><br>".$evidencedata,
								 'hold_remove_by_on' =>date('d-M-Y H:i',strtotime($row->releasedate))."<br/><br/>".$row->freleasedby." ".$row->lreleasedby
					
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

	function hold_history() {
		$this->load->view('billing/orders_on_hold_history');
	}

	function checkProductDiscountPending($quoteid)
	{
		$rt=$this->db->select('id')->from('customer_quotation_detail')->where('flag',0)->where('quotation_id',$quoteid)->or_where('flag',2)->where('quotation_id',$quoteid)->get();

		return $rt->num_rows();
	}

	function transporter_pending_payment_outgoing()
	{
		$this->load->view('billing/pending_outgoing_transporter_payment');
	}


	function transporter_pending_payment_list() {

		// $vehicle=$this->uri->segment(3);
		// $type=$this->uri->segment(6);
		// if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
		// {
		// 	$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		// $end_date=date('Y-m-d',strtotime($this->uri->segment(5)));
		// }else
		// {
		// 	$start_date='';
		// 	$end_date='';
		// }

		 $start_date=date('Y-m-d',strtotime($this->uri->segment(3)));
		 $end_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		 $vehicle=$this->uri->segment(5);
		
		$lead_data = array();
	$this->db->select('a.transporter_type,a.transporter_name,a.transporter_address,a.rate_type,a.rate,a.transporter_id,a.distance,a.vehicle_no,a.dispatched_on,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.send_to_tally', 1)
						  ->where('a.transporter_type', 2)
						  ->where('a.transporter_payment', 0)
						 	->where('a.send_to_tally_On>=','2023-04-10');

						 //  if($type<>'ALL')
						 //  {
						 //  	if($type==1)
							//   	{
							// 			if($vehicle<>'' && $vehicle<>'ALL')
							// 			{
							// 			$this->db->where('a.vehicle_no',$vehicle);
							// 			}
							//   	}else
							//   	{
							//   		if($vehicle<>'' && $vehicle<>'ALL')
							// 			{
							// 				$this->db->where('a.transporter_name',$vehicle);
							// 			}

							//   	}
							// }

						  if($start_date<>'' && $end_date<>'')
						  {
						  	$this->db->where('a.send_to_tally_On>=',$start_date);
						  	$this->db->where('a.send_to_tally_On<=',$end_date);
						  }

						if($vehicle<>'' && $vehicle<>'ALL')
						{
							$this->db->where('a.transporter_name',$vehicle);
						}


							$query = $this->db->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a>";
			if($row->distance==0)
			{
			$eway_bill = "<a href='javascript:;'  onclick='open_modal(".$row->id.")' class='btn btn-warning btn-xs'>Generate E-way Bill</a>";
			}else
			{
				$eway_bill="<a href='".page_url."Billing/ewaybill/".$row->id."/".$row->orderpunchquote."'  class='btn btn-success btn-xs'>Download E-way Bill</a>";
			}
			

			if($row->transporter_type==1)
			{
				$ttype="Owned";
				$tname='';
				$trt_rate=0;
				$tds=0;
				$gst=0;
				$gst_appl=0;
			}else if($row->transporter_type==2)
			{
				$ttype="Hired";
				// $tname=$this->salescrm->get_transporter_name($row->transporter_name);

				$transport_details=$this->transportation_details_Incoming($row->transporter_name,0,0,0);
				$trt=explode('~',$transport_details);
				$tname=$trt[0];
				$trt_rate=$trt[1];
				$tds=$trt[2];
				$gst=$trt[3];
				$gst_appl=$trt[4];

			
			}else
			{
				$ttype="";
				$tname='';
				$trt_rate=0;
				$tds=0;
				$gst=0;
				$gst_appl=0;
			}

			if($row->rate_type==1)
			{
				$rtype="Per LTR";
				$total_qty=$this->salescrm->getOrderTotalQtyonlynumber($row->quotation_id);
				
				$payable_amount=$row->rate*$total_qty;
			}else if($row->rate_type==2)
			{
				$rtype="Fixed";
				$payable_amount=$row->rate;
			}else
			{
				$rtype="";
			}


			$total=round($payable_amount);
			if($tds>0 && $tds<>'')
			{
			$tds_total=$total*($tds/100);
			}else{
			$tds_total=0;
			}

			if($gst_appl>0)
			{
			$gst_total=$total*0.18;
			}else{
			$gst_total=0;
			}

			$total=round($total-$tds_total+$gst_total);



			$payment="<a href='javascript:;' onclick='payment_done(".$row->id.",".$total.",".$row->transporter_name.")' class='btn btn-warning'>Update Payment</a>";  


			$lead_data[] = array('sr_no'=>$i,
				'dispatch_date'=>date('d-M-Y',strtotime($row->dispatched_on)), 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong>",
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'shipaddress'=>$shipdetail,
								 'vehicle_details'=>"<strong style='color:red;font-weight:bold;'>".$row->vehicle_no."</strong>",
								 'payableamount'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$payable_amount."</strong>",
								 'tds'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$tds_total."</strong>",
								 'gst'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$gst_total."</strong>",
								 'total'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$total."</strong>",
								 'eway_bill' => $eway_bill,
								 'tax_invoice' => $tax_invoice,
								 'ttype'=>$ttype,
								 'tname'=>$tname,
								 'trate'=>$rtype,
								 'rate'=>$row->rate,
								 'update_payment'=>$payment
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}



function orders_on_hold_reject_list_history() {
  
  $lead_data = array();
		$this->db->select('a.hold_reject_remarks,k.first_name as r_fname,k.last_name as r_lname,a.hold_reject_on,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,h.contact_number,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users k','k.user_id=a.hold_reject_by')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.hold_reject', 1);
						  if($this->uri->segment(3)<>''){
						  	$this->db->where('b.customer_id',$this->uri->segment(3));
						  }
						  // ->where('a.invoice_no',1282)
						  $query =  $this->db->order_by('a.id','DESC')->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 

			
				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}
			

			$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

				$show=1;
				$reason="";
				$hold_type='';
				$h_type=0;



				if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}

						}

					}

				}

				
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="PREVIOUS INVOICE PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}

				}

				
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="INVOICE PDC NOT RECIEVED";
						$h_type=2;
						$uphold=$this->checkfor_hold_release($row->id,$h_type);
						if($uphold>0)
						{
						$show=1;
						}

				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS SET";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}
				}

				}



				/** CHECK FOR DISCOUNT PENDING FOR APPORVAL **/

				$d_app=$this->checkProductDiscountPending($row->quotation_id);
				if($d_app>0)
					{
					$show=0;
					$reason="<strong style='color:red'>Pending/Rejected Discount Approval</strong>";
					$hold_type="PRODUCT(s) PENDING FOR DISCOUNT APPROVAL";
					$h_type=5;

					}
				// else if($row->payment_type==5)
				// {

				// 	//echo "hi"; exit;


				// }


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}


			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			// if($row->send_to_tally == 1) {
			// 	$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			// } else {
			// 	$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			// }

			if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}

			if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					// $billing = "<span><strong style='color:green'>Billed</strong><br>
					// 			<label>Bill No</label>
					// 			<input type='text' value='".$row->po_no."'></span>";
				} else {

					if($row->send_to_tally==1)
					{
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
					}else
					{
						$billing='<strong style="color:red;font-weight:bold;">SEND TO TALLY FIRST TO MARK AS BILLED</strong>';
					}
				}
			}

			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}



		

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
			$batch_details=$this->get_batch_code_files($row->orderpunchquote);

			$cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";


			$customer_edit="<a href='".page_url."Customer/edit_customer/".$row->customer_id."'><i class='fa fa-pencil' title='Edit Lead'></i></a>";
			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$lead_data[] = array('sr_no'=>$i,
				
				'rejected'=>"<strong style='color:red;font-weight:bold;'>".$row->hold_reject_remarks."</strong>", 
				'rejectedon'=>date('d-M-Y',strtotime($row->hold_reject_on)), 
				'rejectedby'=>$row->r_fname." ".$row->r_lname, 
				'source'=>$row->lead_source, 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'order_details' => $order_details,
								 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
								 'cancell' => $cancell
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function getproducts_detail_with_qty($quotation)
	{
		$qtysum = array();
		$qtysum[] =0;
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		<th>Batch Code</th>
		</tr>
		</thead>
		<tbody>';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
				$qtysum[] = $product->qty;


			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';
			$html.='<td>'.$product->batch_code.'</td>';

			$j++;
			}
			$html.='</tbody></table>';
			}

			return $html.'|'.array_sum($qtysum);
	}



	function sales_margin_list() {
		$start_date = $this->uri->segment(3);
		$end_date = $this->uri->segment(4);
		$user_id = $this->uri->segment(5);
		$lead_data = array();

		$this->db->select('a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.send_to_tally', 1);
		if($start_date != '' && $end_date != '') {
				 $this->db->where('a.send_to_tally_On >=', $start_date);
				 $this->db->where('a.send_to_tally_On <=', $end_date);
		}
			if($user_id<>'ALL')
			{
			$this->db->where('a.agent',$user_id);
			}
		$query = $this->db->where('a.cancelled', 0)
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail_with_cp($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
		$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Tax Invoice</a>";
				$cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";

			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
			$d=$this->salescrm->getproducts_detail_for_order_amount_cp_profit($row->orderpunchquote,date('Y-m-d',strtotime($start_date)));
			$dd=explode('|',$d);

			

			$lead_data[] = array('sr_no'=>$i,
				'order_amount'=>"<strong style='color:blue;font-weight:bold;font-size:14px;'>₹".$dd[0]."</strong>",
				'cp'=>"<strong style='color:orange;font-weight:bold;font-size:14px;'>₹".$dd[1]."</strong>",
				'profit'=>"<strong style='color:red;font-weight:bold;font-size:14px;'>₹".$dd[2]."</strong>",
				'source'=>$row->lead_source, 
						'agent'=>$row->first_name." ".$row->last_name,
						'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'sendtotally' => $send_to_tally,
								  'po_details' =>$po_details,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
								 'cancel'=>$cancell

								 // 'payment_collection' => $payment_collection
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}

	function checkCustomerPaymentTermsApprovalPending($customer_id)
	{
		$d=$this->db->select('id')->from('customer_detail')->where('id',$customer_id)->where('payment_term_approval',0)->get();

		return $d->num_rows();

	}

	public function editpaymenthistory(){
		$this->load->view('billing/editpaymenthistory');
	}

	function remove_payment()
	{
		$payment_id=$this->uri->segment(3);
		$payment_particular_id=$this->uri->segment(4);
		$flag1=$this->uri->segment(5);
		$flag2=$this->uri->segment(6);
		$flag3=$this->uri->segment(7);
		$flag4=$this->uri->segment(8);

		/** START **/
		$restet=$this->db->select('a.id,b.bills')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->where('a.id',$payment_particular_id)->where('a.payment_id',$payment_id)->get();
		if($restet->num_rows()>0)
		{
			$b=array();
			foreach($restet->result() as $row);
			if($row->bills<>'')
			{
				$b=array_map('trim', explode(',',str_replace("'","",$row->bills)));
			}

		for($i=0;$i<count($b);$i++)
		{
			$order_id=$b[$i];
			$dd=array('payment'=>0,'payment_id'=>0);
			$this->db->where('id',$order_id);
			$this->db->update('order_punch',$dd);


			$this->db->where('order_id',$order_id);
			$this->db->delete('customer_cheque_details');
		}


		/** REMOVE CUSTOMER ORDER TO PAYMENT  AND CHEQUE DETAILS **/
		$this->db->where('payment_id',$payment_id);
		$this->db->delete('customer_order_to_payments');


		/** REMOVE PAYMENT PARTICULARS **/

		$this->db->where('id',$payment_particular_id);
		$this->db->delete('customer_payment_particulars');

		/** CHECK IF PAYMENT IF HAS ANY OTHER PARTICLAUR **/

		$resuu=$this->db->select('id')->from('customer_payment_particulars')->where('id!=',$payment_particular_id)->where('payment_id',$payment_id)->get();
		if($resuu->num_rows()==0)
		{
			$this->db->where('id',$payment_id);
			$this->db->delete('customer_payments');
		}


		}
		/** END **/

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Record Deleted</div>');
		redirect(page_url.'Billing/payment_history_data/'.$flag1.'/'.$flag2.'/'.$flag3.'/'.$flag4);
	}


	function orders_on_hold_omparkash_list() {
			$lead_data = array();
		$query = $this->db->select('a.sales_order_no,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.cancelled', 0)
						  ->where('a.billing', 0)
						
						  // ->where('a.agent',$_SESSION['logged_in']['user_id'])
						  ->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		if($query->num_rows() > 0) {
			
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}
	

			$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

		

			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

		

			$show=1;
			$reason="";
			$hold_type="";
			$h_type=0;
			$invoices_list='';
			$invoice_table='';
			$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 
			if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$invoices_list=$prv_pay[2];
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
							$invoice_table=$prv_pay[3];
						}

					}
				}


				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="HOLD DUE PREVIOUS ORDER PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
						$show=1;
					}
					}

				}



				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC NOT RECIEVED";
						$h_type=2;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
						$hold_type="HOLD DUE CURRENT ORDER PDC DATE EXCEEDS PAYMENT TERMS";
						$h_type=3;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}
					}

				}

				}





		if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.")'>Send To Tally</a></span>";
				}
			}


		if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					
				} else {
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
				}
			}


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}



			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			
			$remove_hold="<a href='javascript:;' class='btn btn-success btn-xs' onclick='remove_hold(".$row->id.",".$h_type.")'>Remove Order On Hold</a>";
			


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$grace_days=$this->get_grace_period();


		if($show==0 && $h_type==4)
		{
			$open_feature=array();			 
			$grace_feature=array();			 
			if($invoices_list<>'')
			{
			$invoice=explode(',',$invoices_list);
			if(count($invoice)>0)
			{

			$re='';
			$noopen=array();
			foreach($invoice as $invoices)
			{
				//$d=$this->check_for_invoice_hold_open_or_partial($invoices,$row->customer_id);
				$d=$this->check_for_invoice_hold_open_or_partialNew($invoices,$row->customer_id,$grace_days,$row->sales_order_no);
				//echo $d; exit;
				$e=$this->check_for_invoice_exceed_more_than_grace_days($invoices,$row->customer_id,$grace_days);

				$open_feature[]=$d;
				$grace_feature[]=$e;

				if($d==1)
				{
					$noopen[]=$invoices;
				}
			}
			}
			}

			 // echo "<pre>"; print_r($open_feature)."<br/><br/>";
			 // echo "<pre>"; print_r($grace_feature); exit;
			if(array_sum($open_feature)==0)
			{
				if(array_sum($grace_feature)==0)
				{

				$open_hold='<a href="javascript:;" onclick="remove_hold('.$row->id.','.$h_type.')" class="btn btn-warning">Open Hold</a>';
				}else
				{
					$open_hold='<strong style="color:red;font-weight:bold;">Hold Cannot be Opened By You Since one of the invoice has exceed the Grace Period of '.$grace_days.' Days</strong><br/><br/>';
				}
			}else
			{
				$open_hold='<a href="javascript:;" onclick="remove_hold('.$row->id.','.$h_type.')" class="btn btn-warning">Open Hold</a>';

				// $open_hold='<strong style="color:red;font-weight:bold;">Hold Cannot be Opened By You</strong><br/><br/>';
				// if(count($noopen)>0)
				// {
				// 	$no=implode(',',$noopen);
				// 	$open_hold.="<strong style='color:red;font-weight:bold;'>".$no." invoice(s) previously opened by you but payment pending</strong>";
				// }
			}

		// $tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=2' class='btn btn-success btn-xs'>View Duplicate Tax Invoice</a><br/><br/><a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=3' class='btn btn-success btn-xs'>View Triplicate Tax Invoice</a>";
			$lead_data[] = array('sr_no'=>$i,
				'source'=>$row->lead_source, 
				'hold_type'=>"<strong style='color:red;font-weight:bold;'>".$hold_type."</strong><br/>".$invoice_table,
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on)),
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'remove_hold' => $open_hold
								 // 'payment_collection' => $payment_collection
								);

			
			 


			$i++;
		}
		}
	}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);

	}

	function orders_on_hold_om() {
		$this->load->view('billing/orders_on_hold_omparkash');
	}

	function check_for_invoice_hold_open_or_partial($invoices,$customer_id)
	{
				
			$ewdwe=$this->db->select('a.id')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->join('order_remove_hold c','a.id=c.order_id')->where('b.customer_id',$customer_id)->where('a.invoice_no',trim($invoices))->where('c.hold_type',4)->where('c.added_by',27)->get();
			// if($invoices==1388)
			// {
			// 	echo  $ewdwe->num_rows(); exit;
			// }

			return $ewdwe->num_rows();
			
				
				
		
		
	}

	function check_for_invoice_hold_open_or_partialNew($invoices,$customer_id,$grace_days,$sales_order_no)
	{
		$currentday=date('Y-m-d');
			$d=0;
			$ewdwe=$this->db->select('a.id,a.credit_days,a.send_to_tally_On')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->where('b.customer_id',$customer_id)->where('a.invoice_no',trim($invoices))->where('sales_order_no!=',$sales_order_no)->where('a.payment',0)->get();
			if($ewdwe->num_rows()>0)
			{
				foreach($ewdwe->result() as $row)
				{	

					$expected_payment_days=date('Y-m-d',strtotime($row->send_to_tally_On. ' + '.$row->credit_days.' days'));
					$expected_payment_days=date('Y-m-d',strtotime($expected_payment_days. "+".$grace_days." days"));
					if(strtotime($currentday)>strtotime($expected_payment_days))
					{

					$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));
					$exceed_days = floor($diff / (60 * 60 * 24));
					$a=$this->salescrm->check_remove_hold($row->id);
					if($a==1)
					{
						$d=1;
					}

					}


				}

			}
			// if($invoices==1388)
			// {
			// 	echo  $ewdwe->num_rows(); exit;
			// }

			return $d;
			
				
				
		
		
	}


	function get_customer_bills_payment($customerid)
	{
		//$this->db->select('bills')->from('customer_payments')->where('')

	}


	function remove_hold_remarks_om() {
		
		$order_id = $this->input->post('order_id');
		$hold_type = $this->input->post('hold_type');
		$remove_hold_remarks = $this->input->post('remove_hold_remarks');
		$data = array(
					  'order_id' => $order_id,
					  'hold_type'=>$hold_type,
					  'remarks' => $remove_hold_remarks,
					  'added_on' => date('Y-m-d H:i:s'),
					  'added_by' => $this->session->userdata['logged_in']['user_id']
					 );

		$this->db->insert('order_remove_hold', $data);
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Removed from Hold.</div>');
		redirect(page_url.'Billing/orders_on_hold_om');
	
	}

	function check_for_invoice_exceed_more_than_grace_days($invoices,$customer_id,$grace_days)
	{
			$d=0;
			$currentday=date('Y-m-d');

			$ewde=$this->db->select('a.id,a.send_to_tally_On,a.credit_days')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->where('b.customer_id',$customer_id)->where('a.invoice_no',trim($invoices))->get();

			if($ewde->num_rows()>0)
			{
			foreach($ewde->result() as $row);

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
			$exceed_days = floor($diff / (60 * 60 * 24));

			if($exceed_days>$grace_days)
			{

				$d=1;
			}

			}

			}
			
				
				return $d;
				
		
		
	}

	function get_grace_period()
	{
		$g=0;
		$c=$this->db->select('credit_period')->from('credit_period')->get();
		if($c->num_rows()>0)
		{
			foreach($c->result() as $cc);
			$g=$cc->credit_period;
		}

		return $g;
	}

function check_for_previous_payment($customer_id,$current_order_id)
  {
  	$stop=array();
  	$invoice=array();
  	$invoices_data=array();
  	$order_punch_id=array();
  	$order_quote_id=array();
  	$buyer_gst=array();
  	$seller_gst=array();
  	$invoice_date=array();
  	$send_to_tally=array();
  	$credit_days_data=array();
  	$reste=$this->db->select('a.send_to_tally_On,a.credit_days,a.added_on,a.quotation_id,a.id,a.credit_days,a.send_to_tally_On as added_on,a.invoice_no,f.gst_no as buyer_gst,c.gst as seller_gst')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->join('order_punch_tax_details f','a.id=f.order_id')->join('store_rack_location c', 'c.id=b.company_id', 'left')->where('a.payment',0)->where('a.id!=',$current_order_id)->where('b.customer_id',$customer_id)->where('send_to_tally',1)->where('a.cancelled',0)->get();
  	if($reste->num_rows()>0)
  	{
  		foreach($reste->result() as $row)
  		{
  			$credit_days=$row->credit_days;
  			$billed_date=date('Y-m-d',strtotime($row->added_on));
  			$finalpayabledate=date('Y-m-d',strtotime($billed_date. '+'.$credit_days.' Days'));
  			$current_date=date('Y-m-d');
  			if(strtotime($current_date)>strtotime($finalpayabledate))
  			{
  				$stop[]=1;
  				$invoice[]="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' target='_blank'><u>".$row->invoice_no."</u></a>";
  				$invoices_data[]=$row->invoice_no;
  				$order_punch_id[]=$row->id;
  				$order_quote_id[]=$row->quotation_id;
  				$buyer_gst[]=$row->buyer_gst;
  				$seller_gst[]=$row->seller_gst;
  				$invoice_date[]=date('d-M-Y',strtotime($row->added_on));
  				$send_to_tally[]=$row->send_to_tally_On;
  				$credit_days_data[]=$row->credit_days;

  			}


  		}

  	}


  	$in='';
  	$indata='';
  	if(count($invoice)>0)
  	{
  		$in=implode(',',$invoice);
  		$indata=implode(',',$invoices_data);
  	}

  	/** GET TABLE DATA **/

	$html='';
  	if(count($order_punch_id)>0)
  	{
  		$html.='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Invoice No.</th>
		<th>Invoice Date.</th>
		<th>Exceed Days</th>
		<th>Pending Amount</th>
		
		</tr>
		</thead>
		<tbody>';

		$currentday=date('Y-m-d');
		for($r=0;$r<count($order_punch_id);$r++)
		{
			$order_value=$this->salescrm->getOrderAmountWithGST($order_quote_id[$r],$buyer_gst[$r],$seller_gst[$r]);
			$partial=$this->customer_previous_payment($order_punch_id[$r]);

			$expected_payment_days=date('Y-m-d',strtotime($send_to_tally[$r]. ' + '.$credit_days_data[$r].' days'));
			if(strtotime($currentday)>strtotime($expected_payment_days))
						{
							$diff = abs(strtotime($currentday) - strtotime($expected_payment_days));
				
							$exceed_days = floor($diff / (60 * 60 * 24));
						}else
						{
							$exceed_days=0;
						}

			$final=$order_value-$partial;
			$html.='<tr><td>'.$invoices_data[$r].'</td>';
			$html.='<td>'.$invoice_date[$r].'</td>';
			$html.='<td>'.$exceed_days.'</td>';
			$html.='<td>'.$final.'</td>
			</tr>';
		}

		$html.='</tbody></table>';


  	}


  	return array_sum($stop)."|".$in."|".$indata."|".$html;

  }

  function check_stock_availability($product_id,$company)
  {

		$stock_avail=$this->salescrm->get_stock_availability($product_id,$company);
		
		return $stock_avail;

  }


  function getsalesorder_no()
		{
		

			 $sql = $this->db->select('sales_order_no')
                        ->from('order_punch')
                        ->where('salesorderstart',1)
                        ->order_by('invoice_no', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->sales_order_no+1;

		}else
		{
			$lastorderid=1;
		}

		echo $lastorderid;
	}

	public function order_pending_for_billing_so() 
	{
		$this->load->view('billing/pending_billing_from_so');
	}


	function all_pending_billing_so() {

//		$this->output->clear_path_cache(page_url.'/Billing/all_pending_billing');

		$lead_data = array();
		$query = $this->db->select('a.sales_order_no,a.send_to_tally_On,a.added_on,a.invoice_no,d.bill_to,g.lead_source,h.first_name,h.last_name,h.contact_number,a.hpcl_billing_company,d.ship_to, d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.credit_days, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, a.po_no, a.po_date, b.id as quotation_id, c.companyname,c.gst as seller_gst, a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, f.gst_no as buyer_gst, j.first_name as createdf,j.last_name as createdl,j.contact_number as creater_no')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details f','a.id=f.order_id')
						   ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('system_users j','j.user_id=a.added_by')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=b.company_id', 'left')
						  ->where('a.send_so_to_billing', 0)
						  ->where('a.cancelled', 0)
						  // ->where('a.invoice_no',1282)
						  ->order_by('a.id','DESC')
				 		  ->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$order_max_limit=0;

				$check_remove_hold = $this->salescrm->check_remove_hold($row->id); 

			
				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				}
			

			$this_order_amount = $this->salescrm->getOrderAmountWithGST($row->quotation_id, $row->buyer_gst, $row->seller_gst);
			$order_amount=$this->getunpaid_order_amount($row->customer_id);
			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

			$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;

 
			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";

				$show=1;
				$reason="";
				$hold_type='';
				$h_type=0;



				if($order_amount >= $order_max_limit && $order_max_limit > 0) 
				{
				if($check_remove_hold == 0) 
				{
					$show=0;
					$reason="<strong style='color:red'>Max Limit Reached. Order On Hold<br/><br/>Max Limit-".$order_max_limit."<br/><br/>Payment Due-".$order_amount."<br/><br/>This Order Amount-".$this_order_amount."</strong>";
					$hold_type="HOLD DUE TO MAX LIMIT REACHED";
					$h_type=1;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

				}
				}else
				{
					if($row->payment_type==5)
					{

						$prev_payment_check=$this->check_for_previous_payment($row->customer_id,$row->id);
						$prv_pay=explode('|',$prev_payment_check);
						if($prv_pay[0]>0)
						{
							$show=0;
							$reason="<strong style='color:red'>PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS</strong>";
							$hold_type="PREVIOUS INVOICE ".$prv_pay[1]." PAYMENT PENDING AND EXCEEDING CREDIT DAYS";
							$h_type=4;
							$uphold=$this->checkfor_hold_release($row->id,$h_type);
							if($uphold>0)
							{
							$show=1;
							}

						}

					}

				}

				
				$prev_pdc=$this->check_for_previous_pdc($row->customer_id,$row->id);
				if($prev_pdc<>'')
				{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." is not yet recieved</strong>";
					$hold_type="PREVIOUS INVOICE PDC NOT RECIEVED";
					$h_type=2;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}
				}else
				{
					$prev_pdc=$this->check_for_previous_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>Previous Invoices ".$prev_pdc." PDC Date exceeds the payment term set</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}

				}

				
				if($row->payment_type==4)
				{
				$order_pdc=$this->check_for_current_pdc($row->customer_id,$row->id);
				if($order_pdc>0)
				{
						$show=0;
						$reason="<strong style='color:red'>Current Invoice PDC is not yet recieved.</strong>";
						$hold_type="INVOICE PDC NOT RECIEVED";
						$h_type=2;
						$uphold=$this->checkfor_hold_release($row->id,$h_type);
						if($uphold>0)
						{
						$show=1;
						}

				}else
				{
					$prev_pdc=$this->check_for_current_pdc_hold_due_to_date($row->customer_id,$row->id);
					if($prev_pdc<>'')
					{

					$show=0;
					$reason="<strong style='color:red'>This Invoice ".$prev_pdc." PDC Date exceeds the payment term set.</strong>";
					$hold_type="INVOICE PDC DATE EXCEEDS PAYMENT TERMS SET";
					$h_type=3;
					$uphold=$this->checkfor_hold_release($row->id,$h_type);
					if($uphold>0)
					{
					$show=1;
					}

					}
				}

				}



				/** CHECK FOR DISCOUNT PENDING FOR APPORVAL **/

				$d_app=$this->checkProductDiscountPending($row->quotation_id);
				if($d_app>0)
					{
					$show=0;
					$reason="<strong style='color:red'>Pending/Rejected Discount Approval</strong>";
					$hold_type="PRODUCT(s) PENDING FOR DISCOUNT APPROVAL";
					$h_type=5;
					}

				/** END **/


				/** CHECK FOR PAYMENT TERMS NOT APPROVED **/
				$p_app=$this->checkCustomerPaymentTermsApprovalPending($row->customer_id);

				if($p_app>0)
					{
					$show=0;
					$reason="<strong style='color:red'>Customer Payment Terms Not Approved</strong>";
					$hold_type="CUSTOMER PAYMENT TERM NOT APPROVED";
					$h_type=6;
					}

				

				/** END **/

				// else if($row->payment_type==5)
				// {

				// 	//echo "hi"; exit;


				// }


			if($show==0) {
				
					$order_details = $reason;
				
			} else {
				$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";


				if($row->hpcl_billing_company==3)
				{
					if($row->send_to_tally == 1) {
					$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
					}else
					{
						$order_details.="<br></br><strong style='color:red;font-weight:bold;'>E-Invoice will be activated once data is send to tally and billing is done</strong>";
					}
				}	
			}





			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			// if($row->send_to_tally == 1) {
			// 	$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			// } else {
			// 	$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			// }

			if($show==0) {
			
					$send_to_tally = $reason;
				
			} else {
				if($row->send_to_tally == 1) {
					$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
				} else {
					$send_to_tally = "<span id='sent_to_tally".$row->id."'><a href='javascript:;' class='btn btn-success btn-xs' onclick='send_to_tally(".$row->id.",".$row->orderpunchquote.",".$row->hpcl_billing_company.")'>Send To Tally</a></span>";
				}
			}

			if($show==0) {
				
				$billing = $reason;
				
			} else {

				if($row->billing == 1) {
					$billing='';
					// $billing = "<span><strong style='color:green'>Billed</strong><br>
					// 			<label>Bill No</label>
					// 			<input type='text' value='".$row->po_no."'></span>";
				} else {

					if($row->send_to_tally==1)
					{
					$billing = "<span id='billed".$row->id."'>
					
					<input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
					}else
					{
						$billing='<strong style="color:red;font-weight:bold;">SEND TO TALLY FIRST TO MARK AS BILLED</strong>';
					}
				}
			}

			
			if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}



			if($show==0)
			{

				$art=$this->db->select('id')->from('hold_notification')->where('order_id',$row->id)->get();
				if($art->num_rows()==0)
				{
					$msg="Hello ".$row->first_name." ".$row->last_name.",\n\n";
					$msg.="An Order for you customer *".strtoupper($companyname)." Sales Order No. ".$row->sales_order_no."* is on hold due to following reason \n\n";
					$msg.="*".$reason."*"."\n\n";
					$msg.="Contact Admin for further steps"."\n\n";
					//echo $msg; exit;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '8447031736',
					'receiverMobileNo' => $row->contact_number.','.$row->creater_no.',8447031736',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($msg));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
						}
					curl_close($ch);


					$msg1="Hello Sir,\n\n";
					$msg1.="An Order for you customer *".strtoupper($companyname)."  Sales Order No. ".$row->sales_order_no."* is on hold due to following reason \n\n";
					$msg1.="*".$reason."*"."\n\n";
					$msg1.="Please take neccesary action"."\n\n";
					//echo $msg; exit;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '8447031736',
					'receiverMobileNo' => '9891941007,8447031736',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($msg1));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);

					$d=array('order_id'=>$row->id,'addedOn'=>date('Y-m-d H:i:s'));
						$this->db->insert('hold_notification',$d);

					}else
					{
						

					}
					
			}

			$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";

			if($row->send_to_tally==1){
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a>";
			$batch_details=$this->get_batch_code_files($row->orderpunchquote);
		}else{
			$tax_invoice="<strong style='color:red;font-weight:bold;'>Invoice is activated once order is sent to tally.</strong>";
			$batch_details="";
		}


			
			$cancell = "<span id='cancell_order".$row->id."'><a href='javascript:;' class='btn btn-warning btn-xs' onclick='cancel_order(".$row->id.")'>Cancel Order</a></span>";
			


			$customer_edit="<a href='".page_url."Customer/edit_customer/".$row->customer_id."' class='btn btn-warning btn-xs'><i class='fa fa-pencil' title='Edit Lead' target='_blank'></i></a>";

			$order_edit="<a href='".page_url."Customer/edit_order/".$row->id."/".$row->orderpunchquote."' class='btn btn-warning btn-xs' target='_blank'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

			if($show==0) {
				$sendtobilling=$reason;
				}else
				{
			$sendtobilling="<a href='javascript:;' class='btn btn-warning btn-xs' onclick='send_order_to_billing(".$row->id.",".$row->hpcl_billing_company.")'>Send to billing</a>";
			}
						$billcompany=$this->getbilling_company($row->hpcl_billing_company);
			$lead_data[] = array('sr_no'=>$i,
				'edit_customer'=>$customer_edit,
				'edit_order'=>$order_edit,
				'source'=>$row->lead_source, 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->sales_order_no."</strong><br/>".date('d-M-Y',strtotime($row->added_on))."<br/>".$row->createdl." ".$row->createdf,
						'agent'=>$row->first_name." ".$row->last_name,
				'billing_company'=>$billcompany,
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'taxdetail'=>$taxdetail,
								 'products'=>$html,
								 'shipaddress' =>$shipdetail,
								 'billingaddress' =>$billdetail,
								 'paymentterm' =>$payment_terms,
								 'po_details' =>$po_details,
								 'sendtotally' => $send_to_tally,
								 'billed' => $billing,
								 'order_details' => $order_details,
								 'tax_invoice' => $tax_invoice."<br/>".$batch_details,
								 'sendtobilling' => $sendtobilling,
								 'cancell' => $cancell
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function send_to_billing()
	{
		$id=$this->uri->segment(3);
		$company=$this->uri->segment(4);

		$r=$this->db->select('id')->from('order_punch')->where('cancelled',1)->where('id',$id)->get();
		if($r->num_rows()==0)
		{
		$d=array('send_so_to_billing'=>1,'sales_order_addedOn'=>date('Y-m-d H:i:s'),'sales_order_By'=>$_SESSION['logged_in']['user_id']);

		$this->db->where('id',$id);
		$this->db->update('order_punch',$d);
		}else
		{
			echo "SO HAS BEEN CANCELLED CANNOT BE MOVED TO BILLING"; EXIT;
		}

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Sent For Billing.</div>');
		redirect(page_url.'Billing/order_pending_for_billing_so');

	}



	function getinvoice_no_new($company)
		{
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

			$compid=$company;

			$rest=$this->db->select('invoice_starts_from')->from('store_rack_location')->where('id',$compid)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				$invoice_starts=$row->invoice_starts_from;
			}else
			{
				$invoice_starts=0;
			}



			 $sql = $this->db->select('invoice_no')
                        ->from('order_punch')
                        ->where('hpcl_billing_company',$compid)
                        ->where('added_on>=',$start_date)
                        ->where('added_on<=',$end_date)	
                        ->where('invoice_no!=',0)
                        ->order_by('invoice_no', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->invoice_no+1;

		}else
		{
			$lastorderid=$invoice_starts;
		}

		return $lastorderid;
	}


	function rollback_to_sales_order()
	{
		$id=$this->uri->segment(3);

		$d=array('send_so_to_billing'=>0);

		$this->db->where('id',$id);
		$this->db->update('order_punch',$d);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Roll Backed to Sales Order.</div>');
		redirect(page_url.'Billing/order_pending_for_billing');


	}

	function filter_billing_history()
	{
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$company=$this->input->post('company');
		if($company=='')
		{
			$company="ALL";
		}
		$customer=$this->input->post('customer');
		if($customer=='')
		{
			$customer="ALL";
		}
		$product=$this->input->post('product');
		if($product=='')
		{
			$product="ALL";
		}

		redirect(page_url.'Billing/billing_history/'.$from_date.'/'.$to_date.'/'.$company.'/'.$customer.'/'.$product);
	}


	function getproducts_detailNew($quotation,$productss)
	{
		$a=array();
		$a[]=0;
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Agreed Price</th>
		<th>Batch Code</th>
		</tr>
		</thead>
		<tbody>';
			$this->db->select('a.*,b.instruments_name,c.shortname,batch_code')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation);

			

			$res=$this->db->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){
			if($productss<>'' && $productss<>'ALL')
			{
				
				if($product->product_id==$productss)
				{
				$a[]=1;
				}
			}else
			{
				$a[]=1;
			}

			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';

			if($product->new_batch_code==0)
			{
			$html.='<td>'.$product->batch_code.'</td>';
			}else
			{
			$batch_no=$this->getbatch_no($product->batch_code);
			$html.='<td>'.$batch_no.'</td>';
			}

			$j++;
			}
			$html.='</tbody></table>';
			}



			return $html."~".array_sum($a);
	}


	function getproducts_detail_with_cp($quotation)
	{
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th>Sr. No.</th>
		<th>Product</th>
		<th>Qty</th>
		<th>Cost Price</th>
		<th>Agreed Price</th>
		<th>Diff</th>
		<th>Batch Code</th>
		</tr>
		</thead>
		<tbody>';
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code,a.cp')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quotation)->get();
			if($res->num_rows()>0)
			{
				$j=1;
			foreach($res->result() as $product){



			$html.='<tr><td>'.$j.'</td>';
			$html.='<td>'.$product->instruments_name.'</td>';
			$html.='<td>'.$product->qty.' '.$product->shortname.'</td>';
			$html.='<td>'.$product->cp.'</td>';
			$html.='<td>'.$product->agreed_price.'</td>';
			if($product->cp>0)
			{
				$diff=$product->agreed_price-$product->cp;
				
			}else
			{
				$diff=0;
			}
			$html.='<td>'.$diff.'</td>';
			$html.='<td>'.$product->batch_code.'</td>';

			$j++;
			}
			$html.='</tbody></table>';
			}

			return $html;
	}

	// function default_date_in_payment_date()
	// {
	// 	$restey=$this->db->select('a.id,b.addedOn')->from('customer_payment_particulars a')->join('customer_payments b','a.payment_id=b.id')->get();

	// 	if($restey->num_rows()>0)
	// 	{
	// 		foreach($restey->result() as $rows)
	// 		{
	// 			$id=$rows->id;
	// 			$ar=array('payment_date'=>date('Y-m-d',strtotime($rows->addedOn)));
	// 			$this->db->where('id',$id);
	// 			$this->db->update('customer_payment_particulars',$ar);

	// 		}
	// 	}
	// }


	function transporter_pending_payment_incoming()
	{
		$this->load->view('billing/pending_incoming_transporter_payment');
	}


	function transporter_pending_payment_incoming_filter()
{
	$company=$this->input->post('company');
		
		
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$transporter=$this->input->post('transporter');
		redirect(page_url.'Billing/transporter_pending_payment_incoming/'.$from_date.'/'.$to_date.'/'.$transporter.'/'.$company);

}


function transporter_pending_payment_incoming_history()
	{
		$this->load->view('billing/pending_incoming_transporter_payment_history');
	}
	function transporter_pending_payment_incoming_filter_history()
{
	$company=$this->input->post('company');
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$transporter=$this->input->post('transporter');
		redirect(page_url.'Billing/transporter_pending_payment_incoming_history/'.$from_date.'/'.$to_date.'/'.$transporter.'/'.$company);

}	


			
	function cancel_order_revert_stock()
	{
		$flag=$this->uri->segment(3);
		$order_id=$this->input->post('cancell_order_id');
		$cancell_rmk=$this->input->post('cancell_rmk');
		

		/** GET BILLING CONMPANY AND  PRODUCTS AND REVERT STOCK **/
		$rt=$this->db->select('hpcl_billing_company,quotation_id')->from('order_punch')->where('id',$order_id)->get();
		if($rt->num_rows()>0)
		{
			foreach($rt->result() as $row);
			$company=$row->hpcl_billing_company;
			$quoteid=$row->quotation_id;

			$yt=$this->db->select('product_id,qty')->from('customer_quotation_detail')->where('quotation_id',$quoteid)->get();
			if($yt->num_rows()>0)
			{
				foreach($yt->result() as $tyrow)
				{
					$prdid=$tyrow->product_id;
					$qty=$tyrow->qty;

					
					
					$rtyu=$this->db->select('id,qty,exhausted,balance_left')->from('company_wise_inventory_info')->where('qty>=',$qty)->where('item_id',$prdid)->where('company_id',$company)->where('balance_left!=qty')->order_by('id','DESC')->limit(1)->get();
					if($rtyu->num_rows()>0)
					{
						foreach($rtyu->result() as $rr)
						$new_balance=$rr->balance_left+$qty;
						$dd=array('exhausted'=>0,'balance_left'=>$new_balance);
						$this->db->where('id',$rr->id);
						$this->db->update('company_wise_inventory_info',$dd);


						$srt=$this->db->select('id,stock')->from('company_wise_inventory')->where('itemid',$prdid)->where('company_id',$company)->get();
						if($srt->num_rows()>0)
						{
							foreach($srt->result() as $srtt);
							$st=$srtt->stock;
							$stid=$srtt->id;
							$new_stock=$st+$qty;
							$ddddddd=array('stock'=>$new_stock);
							$this->db->where('id',$stid);
							$this->db->update('company_wise_inventory',$ddddddd);

						}
					}


				}
			}

		}

		
		/** END **/

		if($rtyu->num_rows()>0)
		{
		$data=array('cancelled'=>1,'cancelledOn'=>date('Y-m-d H:i:s'),'cancel_reason'=>$cancell_rmk,'cancelled_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$order_id);
		$this->db->update('order_punch',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Order Cancelled</div>');
		redirect(page_url.'Billing/billing_history');
		}else{

			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Bill Cannot be cancelled</div>');
		redirect(page_url.'Billing/billing_history');
		}

	}


	function get_batch_code_files_with_validation($quoteid)
	{
			$html='';
			$c='';
			$html1='';
			$d=array();
			$prd_b_code=array();
			$d[]=0;
			$res=$this->db->select('a.*,b.instruments_name,c.shortname,batch_code,b.pack_size')->from('customer_quotation_detail a')->join('presto_instruments b','a.product_id=b.id')->join('units c', 'c.id=a.pack_size', 'left')->where('a.quotation_id',$quoteid)->get();
			if($res->num_rows()>0)
			{
			$j=1;
				foreach($res->result() as $product){
					if($product->pack_size=="DRUM")
					{
					if($product->batch_code<>'')
					{
						if($quoteid==1215)
						{
							$tete="test_report";
						}else
						{
							$tete="test_report";
						}
						
						if($product->new_batch_code==0)
						{
						$rt=$this->db->select($tete.',report_file')->from('inventory_batch_no')->where('batch_no',$product->batch_code)->where('report_file!=','')->get();
						if($rt->num_rows()>0)
						{
							foreach($rt->result() as $rtow);
							if($rtow->test_report==1 && $rtow->report_file<>'')
							{
							if(file_exists(assets_upload."test_report/".$rtow->report_file))
							{
								$c="<a href='".page_url1."assets/test_report/".$rtow->report_file."'>".$product->batch_code."</a><br/>";
							}else
							{
								$c=$product->batch_code."<br/>";
								$d[]=1;
								$prd_b_code[]=$product->batch_code;

							}

							}else
							{
								$c=$product->batch_code."<br/>";
								$d[]=1;
								$prd_b_code[]="<a href='".page_url."Inventory/pending_test_report' target='_blank'>".$product->batch_code."</a>";
							}

						}else{
							$c=$product->batch_code."<br/>";
							$d[]=1;
							$prd_b_code[]="<a href='".page_url."Inventory/pending_test_report' target='_blank'>".$product->batch_code."</a>";
						}
				}else{


					$rt=$this->db->select($tete.',report_file,batch_no')->from('inventory_batch_no')->where('id',$product->batch_code)->where('report_file!=','')->get();
						if($rt->num_rows()>0)
						{
							foreach($rt->result() as $rtow);
							if($rtow->test_report==1 && $rtow->report_file<>'')
							{
							if(file_exists(assets_upload."test_report/".$rtow->report_file))
							{
								$c="<a href='".page_url1."assets/test_report/".$rtow->report_file."'>".$rtow->batch_no."</a><br/>";
							}else
							{
								$c=$rtow->batch_no."<br/>";
								$d[]=1;
								$prd_b_code[]=$rtow->batch_no;

							}

							}else
							{
								$c=$rtow->batch_no."<br/>";
								$d[]=1;
								$prd_b_code[]="<a href='".page_url."Inventory/pending_test_report' target='_blank'>".$rtow->batch_no."</a>";
							}

						}else{
							$c="Batch not found<br/>";
							$d[]=1;
							$prd_b_code[]="<a href='".page_url."Inventory/pending_test_report' target='_blank'>Batch not found</a>";
						}






				}
					


				}
				}

					$html.=$c;

				}

			}else
			{
				$html='';
			}
			if(count($prd_b_code)>0)
			{
				$b_code=implode(',',$prd_b_code);
			}else
			{
				$b_code='';
			}

			return array_sum($d).'~'.$b_code;
	}


	function send_notification_order_creater($order_id)
	{

			$query = $this->db->select('a.sales_order_no,a.billed_On,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,a.hpcl_billing_company,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, ,b.customer_id,j.first_name as createdf,j.last_name as createdl,j.contact_number as createrno')
			->from('order_punch a')
			->join('system_users j','j.user_id=a.added_by')
			->join('customer_quotation b','b.id=a.quotation_id')
			->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
			->where('a.id', $order_id)
			->get();

			if($query->num_rows()>0)
			{
				foreach($query->result() as $row);

				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
					$order_max_limit=$data[2];
				


				$msg="Hello ".$row->createdf." ".$row->createdl.",\n\n";
					$msg.="Hold on the Order your created for customer *".strtoupper($companyname)." Sales Order No. ".$row->sales_order_no."* has been released \n\n";
					$msg.="Please do the invoicing"."\n\n";
					//echo $msg; exit;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '8447031736',
					'receiverMobileNo' =>$row->createrno.',8447031736',
					'username' => whatsappuser,
					'password' => whatsapppass,
					'message'=>strip_tags($msg));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
					//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
						}
					curl_close($ch);
				}



			}



	}



	function getinvoice_no_new_testing($company=3)
		{
			$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

			$compid=$company;

			$rest=$this->db->select('invoice_starts_from')->from('store_rack_location')->where('id',$compid)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row)
				$invoice_starts=$row->invoice_starts_from;
			}else
			{
				$invoice_starts=0;
			}



			 $sql = $this->db->select('invoice_no')
                        ->from('order_punch')
                        ->where('hpcl_billing_company',$compid)
                        ->where('added_on>=',$start_date)
                        ->where('added_on<=',$end_date)	
                        ->where('invoice_no!=',0)
                        ->order_by('invoice_no', 'DESC')
                        ->limit(1)
                        ->get();

            if ($sql->num_rows() > 0) {

            	foreach($sql->result() as $rows);

            	$lastorderid=$rows->invoice_no+1;

		}else
		{
			$lastorderid=$invoice_starts;
		}

		echo  $lastorderid; exit;
	}

	function get_previous_two_invoices($company,$quotation_id)
	{
		$financial=$this->salescrm->get_finacial_year_range();
			if(count($financial)>0)
			{
			$start_date=$financial['start_date']." 00:00:00";
			$end_date=$financial['end_date']." 23:59:59";

			}else
			{
			$start_date=date('Y-04-01')." 00:00:00";
			$end_date=date('Y-m-d')." 23:59:59";
			}

		$prev='';
		$reste=$this->db->select('a.invoice_no,b.customer_id')->from('order_punch a')->join('customer_quotation b','a.quotation_id=b.id')->where('a.hpcl_billing_company',$company)->where('a.quotation_id!=',$quotation_id)->where('a.invoice_no!=',0)->where('a.send_so_to_billing',1)->where('a.added_on>=',$start_date)->where('a.added_on<=',$end_date)->order_by('a.invoice_no','DESC')->limit(2)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row)
			{
				$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}else{
					$customer_name='';
					$companyname='';
				}

				$prev.="<strong style='color:red;font-weight:bold;'>".$companyname."-".$row->invoice_no."</strong><br/>";
			}


		}

		return $prev;

	}

	function getbatch_no($id)
	{
		$batch='';
		$restey=$this->db->select('batch_no')->from('inventory_batch_no')->where('id',$id)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$batch=$row->batch_no;
		}

		return $batch;

	}

	function getproduct_name($id){
		$ins='';
		$rrtyu=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$id)->get();
		if($rrtyu->num_rows()>0)
		{
			foreach($rrtyu->result() as $row);
			$ins=$row->instruments_name;

		}

		return $ins;
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


	function filter_order_history()
	{
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$company=$this->input->post('company');
		if($company=='')
		{
			$company="ALL";
		}
		$customer=$this->input->post('customer');
		if($customer=='')
		{
			$customer="ALL";
		}
		$product=$this->input->post('product');
		if($product=='')
		{
			$product="ALL";
		}

		redirect(page_url.'Customer/all_orders/'.$from_date.'/'.$to_date.'/'.$company.'/'.$customer.'/'.$product);
	}



	function transportation_details_Incoming($tid,$vehicle_no,$vehicle_type,$trate)
	{
		$res=$this->db->select('id,name,mobile_no,gst,tds,gst_appl')->from('transporter_details')->where('id',$tid)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);
			$name=$row->name;
			$mobile=$row->mobile_no;
			$tds=$row->tds;
			$gst=$row->gst;
			$gst_appl=$row->gst_appl;
		}else
		{
			$name='';
			$mobile='';
			$tds=0;
			$gst=0;	
			$gst_appl=0;	
		}

		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Transporter Name</th>
        <th style="width:100px;">Transporter Mobile</th>
         <th style="width:30px;">Vehicle Type</th>
        <th style="width:80px;">Vehicle No.</th>
        <th style="width:80px;">Transporter Rate/Ltr</th>
        </tr>
        </thead><tbody>';

        $html.='<tr>
        		<td>'.$name.'</td>
        		<td>'.$mobile.'</td>
        		<td>'.$vehicle_type.'</td>
        		<td>'.$vehicle_no.'</td>
        		<td ><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$trate.'/LTR</strong></td>
        		</tr>';

        		$html.="</tbody></table>";

        		return $name."~".$trate."~".$tds."~".$gst.'~'.$gst_appl;


	}


	function update_payment_against_order(){
		$recordid = $this->input->post('recordid');
		$evidance = $_FILES['evidance']['name'];

		if($evidance<>'')
		{
			$ar=explode('.',$evidance);
			$ext=end($ar);
			$evidancefile=time()."000".'.'.$ext;
			move_uploaded_file($_FILES['evidance']['tmp_name'], UPLOADPATH."transpoterpayment/evidence/".$evidancefile);
		} else {
			$evidancefile="";
		}

		$billattachment = $_FILES['billattachment']['name'];

		if($billattachment<>'')
		{
			$ar1=explode('.',$billattachment);
			$ext1=end($ar1);
			$bill=time().'.'.$ext1;
			move_uploaded_file($_FILES['billattachment']['tmp_name'], UPLOADPATH."transpoterpayment/".$bill);
		} else {
			$bill="";
		}


		$data = array('transporter_payment_amount'=>$this->input->post('paidamount'),
			'transporter_payment_evidence'=>$evidancefile,
			'transporter_payment_bill'=>$bill,
			'transporter_payment'=>1,
			'transporter_payment_date'=>date('Y-m-d',strtotime($this->input->post('payment_date'))),
			'transporter_payment_remarks'=>$this->input->post('remarks'));

		//echo "<pre>"; print_r($data); exit;
		$this->db->where('id',$recordid);
		$this->db->update('order_punch',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you! Payment Successfully updated.</div>');
		redirect(page_url.'Billing/transporter_pending_payment_outgoing/'.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6));

		
	}

	function transporter_pending_payment_outgoing_history()
	{

		$this->load->view('billing/pending_outgoing_transporter_payment_history');
	}


	function transporter_pending_payment_list_history() {

		// $vehicle=$this->uri->segment(3);
		// $type=$this->uri->segment(6);
		// if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
		// {
		// 	$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		// $end_date=date('Y-m-d',strtotime($this->uri->segment(5)));
		// }else
		// {
		// 	$start_date='';
		// 	$end_date='';
		// }
		
		 $start_date=date('Y-m-d',strtotime($this->uri->segment(3)));
		 $end_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		 $vehicle=$this->uri->segment(5);
		 
		$lead_data = array();
	$this->db->select('a.transporter_type,a.transporter_name,a.transporter_address,a.rate_type,a.rate,a.transporter_id,a.distance,a.vehicle_no,a.dispatched_on,a.billed_On,d.ship_to,d.bill_to,a.send_to_tally_On,a.added_on,a.invoice_no,a.po_no, a.po_date,a.credit_days,g.lead_source,h.first_name,h.last_name,a.hpcl_billing_company,d.shipping_name,d.shipping_address,d.shipping_state,d.shipping_city,d.shipping_pincode,d.shipping_phone_no,d.shipping_mobile_no,d.shipping_email,d.billing_name,d.billing_address,d.billing_state,d.billing_city,d.billing_pincode,d.billing_phone_no,d.billing_mobile_no,d.billing_email,
			b.lead_id,b.company_id,a.id, a.payment_type, a.cheque_no, a.pdc_date, a.utr_no, a.upload_po, a.send_to_tally, a.billing, b.id as quotation_id, c.companyname,a.quotation_id as orderpunchquote, e.pan_no, e.gst_no, e.msme_no,b.customer_id, a.transporter_payment_amount, a.transporter_payment_evidence, a.transporter_payment_bill, a.transporter_payment_remarks, transporter_payment_date')
						  ->from('order_punch a')
						  ->join('order_punch_mailing_details d','a.id=d.order_id')
						  ->join('order_punch_tax_details e','a.id=e.order_id')
						  ->join('lead_source g','g.source_id=a.source','left')
						  ->join('system_users h','h.user_id=a.agent')
						  ->join('customer_quotation b','b.id=a.quotation_id')
						  ->join('store_rack_location c', 'c.id=a.hpcl_billing_company', 'left')
						  ->where('a.send_to_tally', 1)
						  ->where('a.transporter_type', 2)
						  ->where('a.transporter_payment', 1)
						 	->where('a.send_to_tally_On>=','2023-04-10');

						 	 if($start_date<>'' && $end_date<>'')
						  {
						  	$this->db->where('a.send_to_tally_On>=',$start_date);
						  	$this->db->where('a.send_to_tally_On<=',$end_date);
						  }

						if($vehicle<>'' && $vehicle<>'ALL')
						{
							$this->db->where('a.transporter_name',$vehicle);
						}



						 //  if($type<>'ALL')
						 //  {
						 //  	if($type==1)
							//   	{
							// 			if($vehicle<>'' && $vehicle<>'ALL')
							// 			{
							// 			$this->db->where('a.vehicle_no',$vehicle);
							// 			}
							//   	}else
							//   	{
							//   		if($vehicle<>'' && $vehicle<>'ALL')
							// 			{
							// 				$this->db->where('a.transporter_name',$vehicle);
							// 			}

							//   	}
							// }

						  // if($start_date<>'' && $end_date<>'')
						  // {
						  // 	$this->db->where('a.send_to_tally_On>=',$start_date);
						  // 	$this->db->where('a.send_to_tally_On<=',$end_date);
						  // }
							$query = $this->db->order_by('a.id','DESC')
				 		  ->get();

		$res = $query->result();
		$i=1;
		foreach($res as $row)
		{
			$customer_name='';
			$companyname='';
			$data=$this->getCustomerdetail($row->customer_id);
				if(count($data)>0)
				{
					$customer_name=$data[0];
					$companyname=$data[1];
				}

			
			$shipstate=$this->getstate($row->shipping_state);
			$billstate=$this->getstate($row->billing_state);
			$j=1;

		$shipdetail="<strong>Ship To :</strong>".$row->ship_to."<br/><br/>".$row->shipping_name."<br/>".$row->shipping_address.",".$row->shipping_city.",".$shipstate."-".$row->shipping_pincode."<br/>".$row->shipping_phone_no."<br/>".$row->shipping_mobile_no."<br/>".$row->shipping_email;


			$billdetail="<strong>Bill To :</strong>".$row->bill_to."<br/><br/>".$row->billing_name."<br/>".$row->billing_address.",".$row->billing_city.",".$billstate."-".$row->billing_pincode."<br/>".$row->billing_phone_no."<br/>".$row->billing_mobile_no."<br/>".$row->billing_email;

			$html=$this->getproducts_detail($row->orderpunchquote);
			$edit = "<a href='".page_url."Customer/edit_order/".$row->id."/".$row->quotation_id."' ><i class='fa fa-edit'></i></a>";
			$order_details = "<a href='".page_url."Customer/view_audit/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Order Details</a>";
			$payment_collection = "<a href='".page_url."Customer/payment_collection_history/".$row->id."/".$row->quotation_id."' class='btn btn-success btn-xs'>Payment Collection History</a>";

			$taxdetail="PAN NO.<br/><strong>".$row->pan_no.'</strong><br/><br/>'.'GST NO.<br/><strong>'.$row->gst_no.'<br/><br/></strong>'.'MSME NO.<br/><strong>'.$row->msme_no.'</strong>';

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
			} else if($row->payment_type == 5) {
				$payment_type = 'CREDIT';
				$payment_terms="PAYMENT TYPE<br/><strong>".$payment_type.'</strong><br/><br/>';
				$payment_terms.="CREDIT DAYS<br/><strong>".$row->credit_days.'</strong><br/><br/>';
			} else if($row->payment_type == 6) {
				$payment_type = 'ADVANCE';
				$payment_terms="";
				$payment_terms.="";
			} else {
				$payment_type = '';
				$payment_terms='';
				$payment_terms .= '';
			}

			if($row->send_to_tally == 1) {
				$send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
			} else {
				$send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
			}

			if($row->billing == 1) {
				$billing = "<span><strong style='color:green'>Billed On ".date('d-M-Y',strtotime($row->billed_On))."</strong></span>";
			} else {
				$billing = "<span id='billed".$row->id."'><input type='checkbox' name='billing' id='billing".$row->id."' value='".$row->id."' onchange='billing(".$row->id.")'></span>";
			}

				if($row->po_date != '1970-01-01' && $row->po_date != '0000-00-00') {
						$po_date = date('d-m-Y', strtotime($row->po_date));
					} else {
						$po_date = '';
					}


					$po_details="PO No.<br/><strong>".$row->po_no."</strong><br/><br/>PO Date<br/><strong>".$po_date."</strong>";


			$billcompany=$this->getbilling_company($row->hpcl_billing_company);

			$order_details.="<br/><br/><a href='".page_url."Billing/einvoice/".$row->id."/".$row->quotation_id."' class='btn btn-warning btn-xs'>E-Invoice</a>";
			$tax_invoice="<a href='".page_url1."taxinvoice/tcpdf/examples/bill.php?order_id=".$row->id."&flag=1' class='btn btn-success btn-xs'>View Original Tax Invoice</a>";
			if($row->distance==0)
			{
			$eway_bill = "<a href='javascript:;'  onclick='open_modal(".$row->id.")' class='btn btn-warning btn-xs'>Generate E-way Bill</a>";
			}else
			{
				$eway_bill="<a href='".page_url."Billing/ewaybill/".$row->id."/".$row->orderpunchquote."'  class='btn btn-success btn-xs'>Download E-way Bill</a>";
			}
			

			if($row->transporter_type==1)
			{
				$ttype="Owned";
				$tname='';
				$trt_rate=0;
				$tds=0;
				$gst=0;
				$gst_appl=0;
			}else if($row->transporter_type==2)
			{
				$ttype="Hired";
				// $tname=$this->salescrm->get_transporter_name($row->transporter_name);

				$transport_details=$this->transportation_details_Incoming($row->transporter_name,0,0,0);
				$trt=explode('~',$transport_details);
				$tname=$trt[0];
				$trt_rate=$trt[1];
				$tds=$trt[2];
				$gst=$trt[3];
				$gst_appl=$trt[4];

			
			}else
			{
				$ttype="";
				$tname='';
				$trt_rate=0;
				$tds=0;
				$gst=0;
				$gst_appl=0;
			}

			if($row->rate_type==1)
			{
				$rtype="Per LTR";
				$total_qty=$this->salescrm->getOrderTotalQtyonlynumber($row->quotation_id);
				
				$payable_amount=$row->rate*$total_qty;
			}else if($row->rate_type==2)
			{
				$rtype="Fixed";
				$payable_amount=$row->rate;
			}else
			{
				$rtype="";
			}


			$total=round($payable_amount);
			if($tds>0 && $tds<>'')
			{
			$tds_total=$total*($tds/100);
			}else{
			$tds_total=0;
			}

			if($gst_appl>0)
			{
			$gst_total=$total*0.18;
			}else{
			$gst_total=0;
			}

			$total=round($total-$tds_total+$gst_total);



			$payment="<a href='javascript:;' onclick='payment_done(".$row->id.",".$total.",".$row->transporter_name.")' class='btn btn-warning'>Update Payment</a>";  

			$paid_date="Date- ".date('d-M-Y',strtotime($row->transporter_payment_date));
			$paid_amount="AMT- ".$row->transporter_payment_amount;
			$paid_remarks="Remarks- ".$row->transporter_payment_remarks;
			$transporter_payment_evidence = '<a href="'.UPLOADPATH.'transpoterpayment/evidence/'.$row->transporter_payment_evidence.'" download>Evidence Download</a>';
			$transporter_payment_bill = '<a href="'.UPLOADPATH.'transpoterpayment/'.$row->transporter_payment_bill.'" download>Bill Download</a>';




			$lead_data[] = array('sr_no'=>$i,
				'dispatch_date'=>date('d-M-Y',strtotime($row->dispatched_on)), 
				'invoice_no'=>"<strong style='color:red;font-weight:bold;'>".$row->invoice_no."</strong>",
								 'company_name'=>$companyname,
								 'customer_name'=>$customer_name,
								 'products'=>$html,
								 'shipaddress'=>$shipdetail,
								 'vehicle_details'=>"<strong style='color:red;font-weight:bold;'>".$row->vehicle_no."</strong>",
								 'payableamount'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$payable_amount."</strong>",
								 'tds'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$tds_total."</strong>",
								 'gst'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$gst_total."</strong>",
								 'total'=>"<strong style='color:red;font-weight:bold;font-size:17px;'>₹".$total."</strong>",
								 'eway_bill' => $eway_bill,
								 'tax_invoice' => $tax_invoice,
								 'ttype'=>$ttype,
								 'tname'=>$tname,
								 'trate'=>$rtype,
								 'rate'=>$row->rate,
								 'update_payment'=>$paid_date."<br/><br/>".$paid_amount."<br/><br/>".$paid_remarks."<br/><br/>".$transporter_payment_evidence."<br/><br/>".$transporter_payment_bill
								);

			 


			$i++;
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
		echo json_encode($results);
	}


	function filter_outgoing_payment_pending()
	{
		
		
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$transporter=$this->input->post('transporter');

		redirect(page_url.'Billing/transporter_pending_payment_outgoing/'.$from_date.'/'.$to_date.'/'.$transporter);
	}

		function filter_outgoing_payment_pending_history()
	{
		
		
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$transporter=$this->input->post('transporter');

		redirect(page_url.'Billing/transporter_pending_payment_outgoing_history/'.$from_date.'/'.$to_date.'/'.$transporter);
	}

	function rebrand_product_history()
	{
		$this->load->view('billing/rebrand_product_history');
	}

	function rebrand_details_history() {

		$start_date = $this->uri->segment(3);
		$end_date = $this->uri->segment(4);
		$company = $this->uri->segment(5);
		$customer = $this->uri->segment(6);
		$product = $this->uri->segment(7);

		$lead_data = array();
		$this->db->select('a.id as detail_id, a.qty, a.list_price, c.companyname, d.customer_name, d.company_name, e.instruments_name, e.discount_price,a.added_on,b.id as quoteid,g.instruments_name as rebrand_instruments_name,f.invoice_no,f.send_to_tally_On,e.unit')
						->from('customer_quotation_detail a')
						->join('customer_quotation b', 'b.id=a.quotation_id')
						->join('store_rack_location c', 'c.id=b.company_id')
						->join('customer_detail d', 'd.id=b.customer_id')
						->join('presto_instruments e', 'e.id=a.product_id')
						->join('order_punch f','f.quotation_id=b.id')
						->join('presto_instruments g', 'g.id=a.rebrand_product_id')
					
						->where('a.rebrand',1)
						->where('a.rebrand_product_id!=',0);

						if($start_date != '' && $end_date != '') {
						$this->db->where('f.send_to_tally_On >=', $start_date);
						$this->db->where('f.send_to_tally_On <=', $end_date);
						}
						if($company<>'ALL' && $company<>'')
						{
						$this->db->where('f.hpcl_billing_company',$company);
						}

						if($customer<>'ALL' && $customer<>'')
						{
						$this->db->where('b.customer_id',$customer);
						}

						if($product<>'ALL' && $product<>'')
						{
						$this->db->where('a.product_id',$product);
						}

						$sql = $this->db->order_by('f.send_to_tally_On','DESC')->get();


			if($sql->num_rows() > 0) {
				$i=1;
				foreach($sql->result() as $row) {

				
					$action = "<a href='javascript:;' class='btn btn-success btn-xs' onclick='accept_reject_remarks(".$row->detail_id.")'>Approve/Reject Discount</a>";
					$lead_data[] = array(
										'sr_no'=>$i,
										'date'=>date('d-M-Y',strtotime($row->send_to_tally_On)),
										'company_name' => $row->companyname,
										'cust_company_name' => $row->company_name,
										'customer_name' => $row->customer_name,
										'invoice_no' => $row->invoice_no,
										'product_name' => $row->instruments_name,
										'rebrand_product_name' => $row->rebrand_instruments_name,
										'qty' => $row->qty." ".$row->unit
										);
					$i++;
				}
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);

		

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($lead_data),
			"iTotalDisplayRecords" => count($lead_data),
			"aaData"=>$lead_data);
			
			echo json_encode($results);
	}


	function filter_rebrand_history()
	{
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$company=$this->input->post('company');
		if($company=='')
		{
			$company="ALL";
		}
		$customer=$this->input->post('customer');
		if($customer=='')
		{
			$customer="ALL";
		}
		$product=$this->input->post('product');
		if($product=='')
		{
			$product="ALL";
		}

		redirect(page_url.'Billing/rebrand_product_history/'.$from_date.'/'.$to_date.'/'.$company.'/'.$customer.'/'.$product);
	}



	function update_order_details_from_cancelled() {
		$pic = $_FILES['upload_file']['name'];
		$user_id = $this->session->userdata['logged_in']['user_id'];	
		$add_new = $this->input->post('add_new');
		$upd_id = $this->input->post('edit_product_id');
		$product_master_id_existing = $this->input->post('product_master_id_old');
		$qty_edit = $this->input->post('qty_edit');
        $listprice_edit = $this->input->post('listprice_edit');
        $discountpricehide_edit = $this->input->post('discountpricehideedit');
        $batch_code_edit = $this->input->post('batch_code_edit');
        $proceed_flag=array();
        $proceed_flag[]=0;
		if($add_new == 1) {
		$productname_for_check = $this->input->post('product');
		$qty_for_check = $this->input->post('qty');
		$for_new_product=$this->salescrm->checkforavailable_Company_QTY($productname_for_check,$qty_for_check,$this->input->post('company'));
		$new_data=explode('~',$for_new_product);
		$proceed_flag[]=$new_data[0];
		}

	if(array_sum($proceed_flag)==0)
			{

			  if($pic <> '') {
				$files = explode('.', $pic);
				$ext = end($files);
				$newname = time().'.'.$ext;
				move_uploaded_file($_FILES['upload_file']["tmp_name"], UPLOADPATH.'order_punch_po/'.$newname);
			 } else {
			 	$newname = $this->input->post('old_upload_file');
			 }

			// echo $newname;exit;

			$data1 = array(
						'customer_id' => $this->input->post('customer'),
						'company_id'=>$this->input->post('company')
						);

			// echo "<pre>";print_r($data1);exit;

		   $this->db->where('id', $this->uri->segment(4))
					->update('customer_quotation',$data1);


       		for($i=0 ;$i<count($upd_id);$i++){
	
       			if($discountpricehide_edit[$i]>$listprice_edit[$i])
       			{
       				$flag=0;
       			}else
       			{
       				$flag=1;
       			}

			       			  
	 			$data2 = array(
							'qty' => $qty_edit[$i],
							'agreed_price' => $listprice_edit[$i],
							 'batch_code' => $batch_code_edit[$i],
							'flag'=>$flag
							);

	            $this->db->where('id', $upd_id[$i])
						 ->update('customer_quotation_detail',$data2);

				$this->update_stocks($product_master_id_existing[$i],$this->input->post('company'),$qty_edit[$i]);
				
   	}

			// if($add_new == 1) {
			// 	$productname = $this->input->post('product');
			// 	$comp_product = $this->input->post('comp_product');
	        //     $qty = $this->input->post('qty');
	        //     $packsize = $this->input->post('pack_size');
	        //     $listprice = $this->input->post('listprice');
	        //     $batch_code = $this->input->post('batch_code');
		    //     $discount = $this->input->post('discountprice');
		    //     $discountpricehide = $this->input->post('discountpricehide');
		    //     $netprice = $this->input->post('netprice');

	       	// 	for($j=0 ;$j<count($productname);$j++){
	       			  
	       	// 		  if($discountpricehide[$j]>$listprice[$j])
	       	// 		  {
	       	// 		  	$flag=0;
	       	// 		  }else
	       	// 		  {
	       	// 		  	$flag=1;
	       	// 		  }
		 	// 		$data3 = array(
			// 				'quotation_id' => $this->uri->segment(4),
			// 				'competitor_product' => $comp_product[$j],
			// 				'product_id' => $productname[$j],
			// 				'qty' => $qty[$j],
			// 				'pack_size' => $packsize[$j],
			// 				'list_price' => $listprice[$j],
			// 				'agreed_price' => $listprice[$j],
			// 				'batch_code' => $batch_code[$j],
			// 				'discount_price' => $discount[$j],
			// 				'net_price' => $netprice[$j],
			// 				'flag' => $flag,
			// 				'new_batch_code'=>1,
			// 				'added_on' => date('Y-m-d H:i:s'),
			// 				'added_by' => $user_id
			// 		);
		    //         $this->db->insert('customer_quotation_detail', $data3);

		    //         $this->update_stocks($productname[$j],$this->input->post('company'),$qty[$j]);

		    // 	}
			// }



			$old_company_hidden = $this->input->post('old_company_hidden');
			$company_id = $this->input->post('company');
			// if($old_company_hidden!=$company_id)
			// {
			// $unique_no=$this->getinvoice_no_new($this->input->post('company'));

			// $dd=array('invoice_no'=>$unique_no);
			// $this->db->where('id', $this->uri->segment(3))
			// ->update('order_punch', $dd);

			// }



		$data = array(
				'hpcl_billing_company'=>$this->input->post('company'),
					 'source'=>$this->input->post('source'),
					 'agent'=>$this->input->post('agent'),
					 'po_no' => $this->input->post('po_no'),
					 'po_date' => date('Y-m-d', strtotime($this->input->post('po_date'))),
					 'payment_type' => $this->input->post('payment_type'),
					 'credit_days' => $this->input->post('paymentterms'),
					 'upload_po' => $newname,
					 'redited_from_cancel'=>1,
					 'cancelled'=>0,
					 'cancelledOn'=>'0000-00-00 00:00:00',
					 'cancel_reason'=>'',
					 'cancelled_by'=>0
					 );

		$this->db->where('id', $this->uri->segment(3))
				 ->update('order_punch', $data);

		if($this->input->post('check_billing') == 1) {
			$check_billing = 1;
		} else {
			$check_billing = 0;
		}
		$data_m = array(
					 'ship_to' => $this->input->post('ship_to'),
					 'shipping_name' => $this->input->post('shipping_name'),
					 'shipping_address' => $this->input->post('shipping_address'),
					 'shipping_state' => $this->input->post('shipping_state'),
					 'shipping_city' => $this->input->post('shipping_city'),
					 'shipping_pincode' => $this->input->post('shipping_pincode'),
					 'shipping_phone_no' => $this->input->post('shipping_phone_no'),
					 'shipping_mobile_no' => $this->input->post('shipping_mobile_no'),
					 'shipping_email' => $this->input->post('shipping_email'),
					 'same_shipping_billing' => $check_billing,
					 'bill_to' => $this->input->post('bill_to'),
					 'billing_name' => $this->input->post('billing_name'),
					 'billing_address' => $this->input->post('billing_address'),
					 'billing_state' => $this->input->post('billing_state'),
					 'billing_city' => $this->input->post('billing_city'),
					 'billing_pincode' => $this->input->post('billing_pincode'),
					 'billing_phone_no' => $this->input->post('billing_phone_no'),
					 'billing_mobile_no' => $this->input->post('billing_mobile_no'),
					 'billing_email' => $this->input->post('billing_email')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_mailing_details', $data_m);

		$data_t = array(
					 'msme_no' => $this->input->post('msme_no'),
					 'pan_no' => $this->input->post('pan_no'),
					 'registration_type' => $this->input->post('registration_type'),
					 'gst_no' => $this->input->post('gst_no')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_tax_details', $data_t);

		$data_p = array(
					 'reference' => $this->input->post('reference'),
					 'note' => $this->input->post('note')
					 );

		$this->db->where('order_id', $this->uri->segment(3))
				 ->update('order_punch_payment_details', $data_p);


		$this->session->set_flashdata('message','<div class="alert alert-info">Thank you, Order successfully updated and moved to billing history</div>');
		redirect(page_url.'Billing/cancelled_history');
	}else
	{
		echo "<strong style='color:red;font-weight:bold;'>REQUIRED STOCK IS NOT AVAIABLE FOR SOME ITEMS. DATA CANNOT BE EDITED</strong>"; exit;
	}

	}

	function update_stocks($product,$billing_company,$product_qty)
	{
		$product=$product;
		$product_qty=$product_qty;
		$stockdata=$this->check_stock_availability($product,$billing_company);
		$new_stock=$stockdata-$product_qty;
		$d=array('stock'=>$new_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
		$this->db->where('company_id',$billing_company);
		$this->db->where('itemid',$product);
		$this->db->update('company_wise_inventory',$d);
		/** CHECK FOR PURCHASE AND MARK IT NULL **/
		$amount_to_be_paid=$product_qty;
		$tyu=$this->db->select('id,qty,balance_left')->from('company_wise_inventory_info')->where('company_id',$billing_company)->where('item_id',$product)->where('exhausted',0)->where('balance_left>',0)->get();
		if($tyu->num_rows()>0)
		{
		foreach($tyu->result() as $exhaust_stock)
		{

		if($amount_to_be_paid>0)
		{
		$balance_left_stock=$exhaust_stock->balance_left;
		$amount_to_be_paid=$amount_to_be_paid;

		if($balance_left_stock>=$amount_to_be_paid)
		{
		$bleft=$balance_left_stock-$amount_to_be_paid;
		$amount_to_be_paid=0;

		}else
		{
		$amount_to_be_paid=$amount_to_be_paid-$balance_left_stock;
		$bleft=0;
		}

		$ddf=array('balance_left'=>$bleft,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
		$this->db->where('id',$exhaust_stock->id);
		$this->db->update('company_wise_inventory_info',$ddf);

		$nrow=$this->db->select('id')->from('company_wise_inventory_info')->where('id',$exhaust_stock->id)->where('balance_left>0')->get();
		if($nrow->num_rows()==0)
		{
		$ddf1=array('exhausted'=>1);
		$this->db->where('id',$exhaust_stock->id);
		$this->db->update('company_wise_inventory_info',$ddf1);
		}else
		{
		$ddf1=array('exhausted'=>0);
		$this->db->where('id',$exhaust_stock->id);
		$this->db->update('company_wise_inventory_info',$ddf1);
		}
		}
		}
		}

	}
}