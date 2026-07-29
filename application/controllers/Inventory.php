<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Salescrm_model','salecrm');
		$this->load->model('Master_model','mastermodel');
	}

	public function index()
	{
		$this->load->view('inventory/inventory');
	}

	function add_inventory() {
	
		$this->db->trans_begin();
		$gst_per=$this->salescrm->get_gst_slab();
		$interest_per=$this->salescrm->get_interest_slab();
		if($this->input->post('ttype')==2)
		{
			$transporter = $this->input->post('transporter_name');
			$mobile_no = $this->input->post('mobile_no');
			$address = $this->input->post('address');
			$vehicle_no = $this->input->post('vehicle_no');
			$vehicle_type = $this->input->post('vehicle_type');
			$transport_rate = $this->input->post('transport_rate');
			$transporter_id=$transporter;

			// $sql = $this->db->select('id')
   			// 			->from('transporter_details')
   			// 			->where('id', $transporter)
   			// 			->get();

   			// if($sql->num_rows() == 0) {
   			// 	// $datas = array(
   			// 	// 				'name' => $transporter,
   			// 	// 				'mobile_no' => $mobile_no,
   			// 	// 				'address' => $address
   			// 	// 				);

   			// 	// $this->db->insert('transporter_details', $datas);
   			// 	// $transporter_id = $this->db->insert_id();
   			// 	}else
   			// 	{
   					
   				// }


		}else
		{
			$vehicle_no='';
			$vehicle_type='';
			$transport_rate=0;
			$transporter_id=0;
		}


		$data = array(
					  'hpcl_billing_company' => $this->input->post('pur_company'),
            'party' => $this->input->post('party'),
					  'currentdate' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'bill_no' => $this->input->post('bill_no'),
					  'payment_type' => $this->input->post('payment_type'),
					  'credit_days'=>$this->input->post('credit_days'),
					  'transport_type'=>$this->input->post('ttype'),
					  'transporter'=>$transporter_id,
					  'vehicle_no'=>$vehicle_no,
					  'vehicle_type'=>$vehicle_type,
					  'transporter_rate'=>$transport_rate,
					  'gst'=>$gst_per,
					  'interest'=>$interest_per
					 );

		$this->db->insert('inventory', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$secondproduct = $this->input->post('secondproduct');
		$bulkitemtype = $this->input->post('bulktype');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');
		$rate = $this->input->post('rate');
		$lot_no = $this->input->post('lot_no');
		$batch_no = $this->input->post('batch_no');
		$manufacturing_date = $this->input->post('manufacturing_date');
		$bulk = $this->input->post('bulk');
		$density = $this->input->post('density');

		for($i = 0; $i < count($product); $i++) {

			if($product[$i] != '') {
				
				$rates=$rate[$i];
				$TTrate=$this->gettransport_drum_rate();
				if(count($TTrate)>0)
				{
				$transportCOST=$TTrate[0];
				$drumCOST=$TTrate[1];
				}else
				{
				$transportCOST=0;
				$drumCOST=0;
				}

				if($bulk[$i]==1 && $pack_size[$i]==5)
				{
					
					$des=$density[$i];
					$mass=$qty[$i];
					$density_app=0;
					if($des>0)
					{
					$prd_qty=$mass/$des;
					}else
					{
						$prd_qty=0;
					}

					$rates=round($rates*$des,4);
						
				}else
				{
					$prd_qty=$qty[$i];
					$des=0;
					$mass=0;
					$density_app=1;
				}

				if($bulk[$i]==1 && $bulkitemtype[$i]==2)
				{
					$scond_product = $secondproduct[$i];
					$bulktype = $bulkitemtype[$i];
					$changed_product=1;
					$primary_product=$secondproduct[$i];
					$secondry_product=$product[$i];

				}else{

					$scond_product = $product[$i];
					$bulktype = 0;
					$changed_product=0;
					$primary_product=$product[$i];
					$secondry_product=0;
				}

				$datas = array(
							  'inventory_id' => $last_id,
							  'product' => $primary_product,
							  'qty' => $prd_qty,
							  'pack_size' => $pack_size[$i],
							  'rate' => $rates,
							  'density'=>$des,
							  'addedOn'=>date('Y-m-d H:i:s'),
							  'addedBy'=>$_SESSION['logged_in']['user_id'],
							  'density_approved'=>$density_app,
							  'original_qty'=>$qty[$i],
							  'bulkproducttype'=>$bulktype,
							  'secondproduct'=>$secondry_product,
							  'changed_product'=>$changed_product
							 );

				$this->db->insert('inventory_details', $datas);
				$last_detail_id = $this->db->insert_id();


					

/** NO BULK **/
				if($bulk[$i]==0)
				{
					$drumCOST=0;
					$curr_stock=$this->getcurrent_stock($product[$i]);
					/** ADD STOCK **/
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$product[$i]);
				$this->db->update('presto_instruments',$stdata);
				/** END **/

				/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($product[$i],$this->input->post('pur_company'));
				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$product[$i],'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}
					
					
				}else{

					
					/** BULK WITH DRUM **/
					if($bulktype==2){
				$drumCOST=$drumCOST;
				$curr_stock=$this->getcurrent_stock($scond_product);
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$scond_product);
				 $this->db->update('presto_instruments',$stdata);


					/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($scond_product,$this->input->post('pur_company'));
				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$scond_product,'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}



				/** MINUS BARRELS **/
				$volume=$this->getvolume($scond_product);
				if($volume>0)
				{
				$bare_prd_qty=$prd_qty/$volume;
				}else
				{
				$bare_prd_qty=$prd_qty;
				}

				$barrel_stock=$this->getcurrent_stock(348);
				$new_stock=$barrel_stock-$bare_prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',348);
				$this->db->update('presto_instruments',$stdata);
				/** END **/

					}else{

					$drumCOST=0;
				/** ADD STOCK **/
				$curr_stock=$this->getcurrent_stock($product[$i]);
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$product[$i]);
			 	$this->db->update('presto_instruments',$stdata);
				/** END **/


					/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($product[$i],$this->input->post('pur_company'));

				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$product[$i],'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}

				

					}
				}
				

				if($this->input->post('chk_report') == 1) 
				{
					$test_report=0;
				}else
				{
					$test_report=1;
				}
				$batch_no_new = explode(',', $batch_no[$i]);

				for($j=0; $j<count($batch_no_new); $j++) {
					if($batch_no_new[$j] != '' && $batch_no_new[$j]<>'Not Required') {
						$datas1 = array(
										'inventory_id' => $last_id,
										'inv_detail_id' => $last_detail_id,
										'batch_no' => $batch_no_new[$j],
										'test_report' => $test_report
										);

						$this->db->insert('inventory_batch_no', $datas1);
					}
				}


				/** ENTER STOCK INFO DETAILS **/
				
				if($bulk[$i]==1 && $bulktype==2)
				{
				$prdused = $secondproduct[$i];

				}else{
				$prdused = $product[$i];
				}

				$stdata=array('inventory_particular_id'=>$last_detail_id,'item_id'=>$prdused,'qty'=>$prd_qty,'company_id'=>$this->input->post('pur_company'),'balance_left'=>$prd_qty,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
				 $this->db->insert('company_wise_inventory_info',$stdata);

				/** END **/



				/*** CP FOR PRODUCT **/

			$st=date('Y-m-d');
			$en=date('Y-m-d');
			$com=$this->input->post('pur_company');
			$getOpenstock = $this->mastermodel->get_Open_stock_for_product($st,$en,$com,$prdused);
			if($com==3)
			{
			$vli = $this->check_for_approval_CRNOTE($prdused,date('Y-m-d', strtotime($this->input->post('current_date'))));
			}else
			{
				$vli=0;
			}


			$ops=explode('~',$getOpenstock);

			$opening_Q=$ops[0];
			$opening_R=$ops[1];
			$opening_V=$opening_Q*$opening_R;


			$instock=$this->mastermodel->get_inward_between_dates($st,$en,$com,$prdused);
			$opin=explode('~',$instock);
			$opin_Q=$opin[0];
			$opin_R=$opin[1];
			$opin_V=$opin_Q*$opin_R;

			

			if(($opening_Q+$opin_Q)>0)
			{
			$msp=($opening_V+$opin_V)/($opening_Q+$opin_Q);
			}else
			{
				$msp=($opening_V+$opin_V);
			}

			if($this->input->post('ttype')==2)
			{
			$transportCOST=$transportCOST;
			}else{
			$transportCOST=0;
			}
			$msp=$msp+$drumCOST+$transportCOST-$vli;
			$msp=round($msp);



			/** ADD REFRENCE **/
			$DRT=array('product_id'=>$prdused,'billing_price'=>$msp,'vli'=>$vli,'drum'=>$drumCOST,'transport'=>$transportCOST,'costprice'=>$msp,'addedOn'=>date('Y-m-d', strtotime($this->input->post('current_date'))),'actual_addedOn'=>date('Y-m-d H:i:s'),'inventory_detail_id'=>$last_detail_id);
			$this->db->insert('presto_instruments_cp',$DRT);
			/** END **/

			/** ADD MARGIN **/

			$current_margin=$this->getcurrent_margin($prdused);
			if(count($current_margin)>0)
			{
				if($current_margin[0]==1)
				{

				$mmmvalue=$current_margin[1]/100;
				$margin=$msp+($msp*$mmmvalue);

				$mtype=$current_margin[0];
				$mValues=$current_margin[1];

				}else
				{
					$mmmvalue=$current_margin[1];
					$margin=$msp+$mmmvalue;
					$mtype=$current_margin[0];
					$mValues=$current_margin[1];
				}

			}else
			{
				$mmmvalue=15/100;
				$margin=$msp+($msp*$mmmvalue);
				$mtype=1;
				$mValues=15;
			}


			$drytey=array('product_id'=>$prdused,'margintype'=>$mtype,'marginvalue'=>$mValues,'costprice'=>$msp,'msp'=>$margin,'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('presto_instruments_margin_sheet',$drytey);

			/** UPDATE ALLOWED PRICE **/
			$DT=array('discount_price'=>$margin);
			$this->db->where('id',$prdused);
			$this->db->update('presto_instruments',$DT);

			


			




			}

		}

		if($this->input->post('chk_report') == 1) {
						$html = '';
						$sql1 = $this->db->select('batch_no')
										 ->from('inventory_batch_no')
										 ->where('test_report', 0)
										 ->get();

						if($sql1->num_rows() > 0) {
							$html .= 'Please provide test report for the following batch codes:-'.'<br><br>';
							foreach ($sql1->result() as $row1) {
								$html .= $row1->batch_no.'<br>';
							}

							// echo $html;exit;
		    //             $subjectname = "Test Report Required";

				  //    	$config['protocol'] = 'ssmtp';  
						// $config['smtp_host'] = 'ssl://ssmtp.googlemail.com';  
						// $config['smtp_user'] = 'faridabadcfa2@gmail.com';  
						// $config['smtp_pass'] = 'faridabad@123**';   
						// $config['smtp_port'] = 465;   
						// // $config['smtp_crypto'] = 'ssl';
						// $config['newline'] = "\r\n";
						// $config['starttls'] = TRUE;
						// $config['charset'] = 'iso-8859-1';
						// $config['mailtype'] = 'html';

				  //       $this->email->initialize($config);  
				  //       $this->load->library('email', $config);
				  //       $this->email->set_header('Header1', 'Value1');
						// $this->email->set_mailtype("html");
						// $this->email->to('webdevelopment1@gamavis.com');
						// $this->email->from('faridabadcfa2@gmail.com');
						// $this->email->subject($subjectname);
						// $this->email->message(strip_tags($html));
						// $result11=$this->email->send();
					}
		}


		if ($this->db->trans_status() === FALSE)
			{
			$this->db->trans_rollback();
				$this->session->set_flashdata('message','<div class="alert alert-danger">Issue in Adding Inventory. Please try again later.</div><br/>');
			redirect(page_url.'Inventory');
			}
			else
			{
			$this->db->trans_commit();
			$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
			redirect(page_url.'Inventory');
			}
	

		

	}

	function add_purchase_entry() {
		
		$approval_id = $this->uri->segment(3);

		$data = array(
					  'approval_id' => $approval_id,
					  'currentdate' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'bill_no' => $this->input->post('bill_no'),
					  'party' => $this->input->post('vendor'),
					  'added_by' => $_SESSION['logged_in']['user_id'] 
					 );

		$this->db->insert('purchase_entry', $data);
		$last_id = $this->db->insert_id();

		$approval_detail_id = $this->input->post('approval_detail_id');
		$product_id = $this->input->post('product');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');

		for($i = 0; $i < count($approval_detail_id); $i++) {
			if($approval_detail_id[$i] != '') {
				$datas = array(
							  'entry_id' => $last_id,
							  'approval_detail_id' => $approval_detail_id[$i],
							  'qty' => $qty[$i],
							  'pack_size' => $pack_size[$i]
							 );

				$this->db->insert('purchase_entry_details', $datas);

			}

		}

			$data1 = array(
					  'purchase_entry' => 1,
					  'purchase_entry_on' => date('Y-m-d')
					  );

			$this->db->where('id', $approval_id)
					 ->update('approval_form', $data1);

		$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
	  	redirect(page_url.'Approval');

	}


	function update_purchase_entry() {
		$entry_id = $this->uri->segment(3);
		$approval_id = $this->uri->segment(4);


		$getAllApprovalProdIDs = $this->salescrm->getAllApprovalProdIDs($approval_id);

		$data = array(
					  'currentdate' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'bill_no' => $this->input->post('bill_no'),
					  'party' => $this->input->post('vendor')
					 );

		$this->db->where('id', $entry_id)
				 ->update('purchase_entry', $data);

		$inventory_entry_id = $this->input->post('inventory_entry_id');
		$approval_detail_id = $this->input->post('approval_detail_id');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');

		for($i = 0; $i < count($inventory_entry_id); $i++) {
			if($inventory_entry_id[$i] != '') {

				if (in_array($approval_detail_id[$i], $getAllApprovalProdIDs)) {
					$datas = array(
								  'qty' => $qty[$i],
								  'pack_size' => $pack_size[$i]
								 );

					$this->db->where('id', $inventory_entry_id[$i])
							 ->update('purchase_entry_details', $datas);
				} else {
					$datas = array(
								  'approval_detail_id' => $approval_detail_id[$i],
								  'qty' => $qty[$i],
								  'pack_size' => $pack_size[$i]
								 );

					$this->db->insert('purchase_entry_details', $datas);
				}

			}

		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
	  	redirect(page_url.'Approval');

	}

	function get_product_unit()
	{
		$htm='<option value="">Select</option>';
		$name='';
		$prd=$this->input->post('prd');
		$restey=$this->db->select('a.unit,b.name,b.id,a.pack_size')->from('presto_instruments a')->join('units b','a.unit=b.shortname')->where('a.id',$prd)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);


			$htm.="<option value='".$row->id."'>".$row->name."</option>";
			if($row->pack_size=="BULK")
			{
				$htm.="<option value='5'>Kilogram</option>";
			}

			$name=$row->name;
		}

		echo $htm.'|'.$name.'|'.$row->id.'|'.$row->pack_size;

	}


	function this_month_purchases() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('inventory/this_month_purchases', $data);
	}

	function this_month_purchases_list() {
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$party=$this->uri->segment(5);
    $product=$this->uri->segment(6);
    $company=$this->uri->segment(7);
    $alltime=$this->uri->segment(8);
		$data = array();
		$i=1;
		         $this->db->select('a.payment_type,a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname')
						  ->from('inventory a')
              ->join('vendors d', 'd.id=a.party','left')
              ->join('store_rack_location s', 's.id=a.hpcl_billing_company','left');
            

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


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {


				if($row->payment_type==2)
				{
					$pay="Cash";
				}else if($row->payment_type==3)
				{
					$pay="Online";
				}else if($row->payment_type==4)
				{
					$pay="PDC";
				}else if($row->payment_type==5)
				{
					$pay="Credit";
				}else if($row->payment_type==6)
				{
					$pay="Advance";
				}else
				{
					$pay="";
				}
					$purchase_details=$this->get_purchase_details($row->id);
					$pdetails=explode('|',$purchase_details);
					

					if($row->transport_type==2)
					{
						$transport_details=$this->transportation_details($row->transporter,$row->vehicle_no,$row->vehicle_type,$row->transporter_rate);
					}else
					{
						$transport_details='';
					}

						if($product != '' &&  $product != 'ALL') {
						$chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						} else {
						$chkIfProductExists = 1;
						}
						
						if($chkIfProductExists>0)
						{
              if($row->send_to_tally == 1) {
                $send_to_tally = "<span><strong style='color:green'>Sent To Tally On ".date('d-M-Y',strtotime($row->send_to_tally_On))." </strong></span>";
              } else {
                $send_to_tally = "<span id='sent_to_tally".$row->id."'><input type='checkbox' name='send_to_tally' id='send_to_tally".$row->id."' value='".$row->id."' onchange='send_to_tally(".$row->id.")'></span>";
              }


              if($row->billed == 1) {
                $billed = "<span><strong style='color:green'>Imported In Tally Marked On ".date('d-M-Y',strtotime($row->billed_On))." </strong></span>";
              } else {
                $billed = "<span id='imported_to_tally".$row->id."'><input type='checkbox' name='billed' id='billed".$row->id."' value='".$row->id."' onchange='imported_to_tally(".$row->id.")'></span>";
              }


              $ggst=$row->gst/100;
              $gst_amount=$pdetails[1]*$ggst;
              $grandtotal=$pdetails[1]+$gst_amount;

							$earlier = new DateTime(date('Y-m-d',strtotime($row->currentdate)));
							$later = new DateTime(date('Y-m-d'));
							$abs_diff = $later->diff($earlier)->format("%a");
							if($_SESSION['logged_in']['role']!=1)
							{
								if($abs_diff<8)
								{
									  $action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';
								}else
								{
									  $action='<strong style="color:red;font-weight:bold;">Edit available for last 7 days purchase</strong>';
								}
							}else
							{
								  $action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';
							}

							if($pdetails[2]==0 && $_SESSION['logged_in']['user_id'])
							{
							$del="<a href='javascript:;' onclick='del_inv_data(".$row->id.");'><i class='fa fa-trash'></i></a>";
							}else{
								$del='';
							}

            
						$data[] = array(
								'sr_no' => $i."<br/>".$del,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'payment'=>$pay,
								'credit_days'=>$row->credit_days." Days",
								'product_detail'=>$pdetails[0],
								'transport_detail'=>$transport_details,
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
								'gst_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_amount."</strong>",
								'grand_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$grandtotal."</strong>",
                'sendtotally' => $send_to_tally,
                'billed' => $billed,
								'action'=>	$action												
								
							);

						$i++;
					}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}


	function get_purchase_details($id)
	{
		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
        <th style="width:80px;">Rate</th>
        <th style="width:80px;">Lot No.</th>
        <th style="width:100px;">Batch No.</th>
        <th style="width:100px;">Test Report</th>
        <th style="width:80px;">Manufacturing Date.</th>
        <th style="width:80px;">Tranfer Type</th>
        <th style="width:80px;">Tranfer To </th>
        <th style="width:80px;">Total Price</th>
        </tr>
        </thead><tbody>';

        $tot=array();
        $tot[]=0;
		$reste=$this->db->select('a.changed_product,a.id, a.product,a.pack_size,a.qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname, a.bulkproducttype, d.instruments_name as second_instruments_name')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('presto_instruments d','a.secondproduct=d.id','left')->where('a.inventory_id',$id)->get();
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

$c='';
if($rows->bulkproducttype==1){
$bulktype="Bulk to Bulk";
$main_product=$rows->instruments_name;
$prd='';
}else if($rows->bulkproducttype==2){
$bulktype="Bulk to Drum";
if($rows->changed_product==1)
{
$main_product=$rows->second_instruments_name;
$prd=$rows->instruments_name;
$c="Changed Product";
}else
{
$main_product=$rows->instruments_name;
$prd=$rows->second_instruments_name;
$c="";
}

}else{
	$bulktype = "NA";
	$main_product=$rows->instruments_name;
	$prd='';
	$c="";
}

// if($rows->second_instruments_name){
// $prd = $rows->second_instruments_name;
// }else{
// 	$prd = "NA";
// }
				$total=$rows->qty*$rows->rate;
				//.$rows->shortname
					$html.='<tr>
					<td>'.$i.'</td>
					<td>'.$main_product.'<br/><br/>(<strong style="color:red;font-weight:bold;">'.$c.'</strong>)</td>
					<td>'.$rows->qty.' LTR</td>
					<td>'.$rows->rate.'/LTR</td>
					<td>'.$rows->lot_no.'</td>
					<td>'.implode(',<br>', $batch_code).'</td>
					<td>'.$report_file.'</td>
					<td>'.date('d-M-Y',strtotime($rows->manufacturing_date)).'</td>
					<td>'.$bulktype.'</td>
					<td>'.$prd.'</td>
					<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$total.'</strong></td>
					</tr>';

					$tot[]=$total;
			$i++;
		}

		}else
		{
			$html.='<tr>
					<td colspan="8">No Product Available</td>
					</tr>';
		}

		$html.='</tbody>
  </table>';

  				return $html."|".array_sum($tot).'|'.$reste->num_rows();

	}

		function filter_purchase_list()
		{
			$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
			$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
			$hpcl_locations=$this->input->post('hpcl_locations');
      $products=$this->input->post('products');
      $company=$this->input->post('company');
      $alltime=$this->input->post('alltime');
      if($alltime==1)
      {
      	$alltime=1;
      }else
      {
      	$alltime=0;
      }
			

			redirect(page_url.'Inventory/this_month_purchases/'.$from_date."/".$to_date."/".$hpcl_locations."/".$products."/".$company.'/'.$alltime);
		}
	

	function chkIfProductExists($id,$product)
	{
		$this->db->select('a.id')->from('inventory_details a')->where('a.inventory_id',$id);
		if($product<>'' && $product<>'ALL')
		{
		$this->db->where('a.product',$product);
		}

		$restey=$this->db->get();

		return $restey->num_rows();

	}

	function edit_inventory()
	{
		$this->load->view('inventory/edit_inventory');
	}



function update_inventory() {

	$this->db->trans_start();

		$id=$this->uri->segment(3);

		$gst_per=$this->salescrm->get_gst_slab();
		$interest_per=$this->salescrm->get_interest_slab();
		if($this->input->post('ttype')==2)
		{
			$transporter = $this->input->post('transporter_name');
			$vehicle_no = $this->input->post('vehicle_no');
			$vehicle_type = $this->input->post('vehicle_type');
			$transport_rate = $this->input->post('transport_rate');
   		

		}else
		{
			$vehicle_no='';
			$vehicle_type='';
			$transport_rate=0;
			$transporter_id=0;
			$transporter=0;
		}


		$data = array(
				
					  'currentdate' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'bill_no' => $this->input->post('bill_no'),
					  'credit_days'=>$this->input->post('credit_days'),
            		  'hpcl_billing_company' => $this->input->post('pur_company'),
            		  'payment_type' => $this->input->post('payment_type'),
					  'credit_days'=>$this->input->post('credit_days'),
            		  'party' => $this->input->post('party'),
					  'transport_type'=>$this->input->post('ttype'),
					  'transporter'=>$transporter_id,
					  'vehicle_no'=>$vehicle_no,
					  'vehicle_type'=>$vehicle_type,
					  'transporter_rate'=>$transport_rate,
					  'interest'=>$this->input->post('interest')
					 );

		$this->db->where('id',$id);
		$this->db->update('inventory', $data);
		$last_id = $id;

		/** EDIT EXISTING **/
		// if(isset($this->input->post('detail_id')))
		// {
		$detail_id=$this->input->post('detail_id');
		if(count($detail_id)>0)
		{
			for($r=0;$r<count($detail_id);$r++)
			{
				$detailid=$detail_id[$r];

				$eqty=$this->input->post('edit_qty'.$detailid);
				$eunit=$this->input->post('edit_pack_size'.$detailid);
				$erate=$this->input->post('edit_rate'.$detailid);
				$elot=$this->input->post('edit_lot_no'.$detailid);
				$ebatch=$this->input->post('edit_batch_no'.$detailid);
				$emanufac=$this->input->post('edit_manufacturing_date'.$detailid);

				$dedata=array('qty'=>$eqty,'pack_size'=>$eunit,'rate'=>$erate,'lot_no'=>$elot,'batch_no'=>$ebatch,'manufacturing_date'=>date('Y-m-d',strtotime($emanufac)));
				$this->db->where('id',$detailid);
				$this->db->update('inventory_details',$dedata);



				/**** BATCH NO ***/
				$batch_no_exi = explode(',',$ebatch);
				
				for($j=0; $j<count($batch_no_exi); $j++) {
				if($batch_no_exi[$j] != '' &&  $batch_no_exi[$j] != 'Not Required') {
				$datas1 = array(
				'inventory_id' => $id,
				'inv_detail_id' => $detailid,
				'batch_no' => $batch_no_exi[$j],
				'test_report' => 0
				);

				$this->db->insert('inventory_batch_no', $datas1);

				}
				}
				/** BATCH NO. **/




			}
		}
		//}
		/** END **/




		$product = $this->input->post('product');
		$secondproduct = $this->input->post('secondproduct');
		$bulkitemtype = $this->input->post('bulktype');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');
		$rate = $this->input->post('rate');
		$lot_no = $this->input->post('lot_no');
		$batch_no = $this->input->post('batch_no');
		$manufacturing_date = $this->input->post('manufacturing_date');
		$bulk = $this->input->post('bulk');
		$density = $this->input->post('density');

		for($i = 0; $i < count($product); $i++) {

			if($product[$i] != '') {
				
				$rates=$rate[$i];
				$TTrate=$this->gettransport_drum_rate();
				if(count($TTrate)>0)
				{
				$transportCOST=$TTrate[0];
				$drumCOST=$TTrate[1];
				}else
				{
				$transportCOST=0;
				$drumCOST=0;
				}

				if($bulk[$i]==1 && $pack_size[$i]==5)
				{
					
					$des=$density[$i];
					$mass=$qty[$i];
					$density_app=0;
					if($des>0)
					{
					$prd_qty=$mass/$des;
					}else
					{
						$prd_qty=0;
					}

					$rates=round($rates*$des,4);
						
				}else
				{
					$prd_qty=$qty[$i];
					$des=0;
					$mass=0;
					$density_app=1;
				}

				if($bulk[$i]==1 && $bulkitemtype[$i]==2)
				{
					$scond_product = $secondproduct[$i];
					$bulktype = $bulkitemtype[$i];
					$changed_product=1;
					$primary_product=$secondproduct[$i];
					$secondry_product=$product[$i];

				}else{

					$scond_product = $product[$i];
					$bulktype = 0;
					$changed_product=0;
					$primary_product=$product[$i];
					$secondry_product=0;
				}

				$datas = array(
							  'inventory_id' => $last_id,
							  'product' => $primary_product,
							  'qty' => $prd_qty,
							  'pack_size' => $pack_size[$i],
							  'rate' => $rates,
							  'density'=>$des,
							  'addedOn'=>date('Y-m-d H:i:s'),
							  'addedBy'=>$_SESSION['logged_in']['user_id'],
							  'density_approved'=>$density_app,
							  'original_qty'=>$qty[$i],
							  'bulkproducttype'=>$bulktype,
							  'secondproduct'=>$secondry_product,
							  'changed_product'=>$changed_product
							 );

				$this->db->insert('inventory_details', $datas);
				$last_detail_id = $this->db->insert_id();


					

/** NO BULK **/
				if($bulk[$i]==0)
				{
					$drumCOST=0;
					$curr_stock=$this->getcurrent_stock($product[$i]);
					/** ADD STOCK **/
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$product[$i]);
				$this->db->update('presto_instruments',$stdata);
				/** END **/

				/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($product[$i],$this->input->post('pur_company'));
				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$product[$i],'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}
					
					
				}else{

					
					/** BULK WITH DRUM **/
					if($bulktype==2){
				$drumCOST=$drumCOST;
				$curr_stock=$this->getcurrent_stock($scond_product);
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$scond_product);
				 $this->db->update('presto_instruments',$stdata);


					/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($scond_product,$this->input->post('pur_company'));
				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$scond_product,'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}



				/** MINUS BARRELS **/
				$volume=$this->getvolume($scond_product);
				if($volume>0)
				{
				$bare_prd_qty=$prd_qty/$volume;
				}else
				{
				$bare_prd_qty=$prd_qty;
				}

				$barrel_stock=$this->getcurrent_stock(348);
				$new_stock=$barrel_stock-$bare_prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',348);
				$this->db->update('presto_instruments',$stdata);
				/** END **/

					}else{

					$drumCOST=0;
				/** ADD STOCK **/
				$curr_stock=$this->getcurrent_stock($product[$i]);
				$new_stock=$curr_stock+$prd_qty;
				$stdata=array('stock'=>$new_stock);
				$this->db->where('id',$product[$i]);
			 	$this->db->update('presto_instruments',$stdata);
				/** END **/


					/** COMPANY WISE STOCK **/
				$exist=$this->checkforcompanystock($product[$i],$this->input->post('pur_company'));

				if(count($exist)>0)
				{
					
					$updated_stock=$exist[1]+$prd_qty;
					$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
					$this->db->where('id',$exist[0]);
					 $this->db->update('company_wise_inventory',$stdata);

				}else
				{
					$updated_stock=$prd_qty;
					$stdata=array('company_id'=>$this->input->post('pur_company'),'itemid'=>$product[$i],'stock'=>$updated_stock,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
					 $this->db->insert('company_wise_inventory',$stdata);
				}

				

					}
				}
				

				if($this->input->post('chk_report') == 1) 
				{
					$test_report=0;
				}else
				{
					$test_report=1;
				}
				$batch_no_new = explode(',', $batch_no[$i]);

				for($j=0; $j<count($batch_no_new); $j++) {
					if($batch_no_new[$j] != '' && $batch_no_new[$j]<>'Not Required') {
						$datas1 = array(
										'inventory_id' => $last_id,
										'inv_detail_id' => $last_detail_id,
										'batch_no' => $batch_no_new[$j],
										'test_report' => $test_report
										);

						$this->db->insert('inventory_batch_no', $datas1);
					}
				}


				/** ENTER STOCK INFO DETAILS **/
				
				if($bulk[$i]==1 && $bulktype==2)
				{
				$prdused = $secondproduct[$i];

				}else{
				$prdused = $product[$i];
				}

				$stdata=array('inventory_particular_id'=>$last_detail_id,'item_id'=>$prdused,'qty'=>$prd_qty,'company_id'=>$this->input->post('pur_company'),'balance_left'=>$prd_qty,'addedBy'=>$_SESSION['logged_in']['user_id'],'addedOn'=>date('Y-m-d H:i:s'));
				 $this->db->insert('company_wise_inventory_info',$stdata);

				/** END **/



				/*** CP FOR PRODUCT **/

			$st=date('Y-m-d');
			$en=date('Y-m-d');
			$com=$this->input->post('pur_company');
			$getOpenstock = $this->mastermodel->get_Open_stock_for_product($st,$en,$com,$prdused);
			if($com==3)
			{
			$vli = $this->check_for_approval_CRNOTE($prdused,date('Y-m-d', strtotime($this->input->post('current_date'))));
			}else
			{
				$vli=0;
			}


			$ops=explode('~',$getOpenstock);

			$opening_Q=$ops[0];
			$opening_R=$ops[1];
			$opening_V=$opening_Q*$opening_R;


			$instock=$this->mastermodel->get_inward_between_dates($st,$en,$com,$prdused);
			$opin=explode('~',$instock);
			$opin_Q=$opin[0];
			$opin_R=$opin[1];
			$opin_V=$opin_Q*$opin_R;

			

			if(($opening_Q+$opin_Q)>0)
			{
			$msp=($opening_V+$opin_V)/($opening_Q+$opin_Q);
			}else
			{
				$msp=($opening_V+$opin_V);
			}

			if($this->input->post('ttype')==2)
			{
			$transportCOST=$transportCOST;
			}else{
			$transportCOST=0;
			}
			$msp=$msp+$drumCOST+$transportCOST-$vli;
			$msp=round($msp);



			/** ADD REFRENCE **/
			$DRT=array('product_id'=>$prdused,'billing_price'=>$msp,'vli'=>$vli,'drum'=>$drumCOST,'transport'=>$transportCOST,'costprice'=>$msp,'addedOn'=>date('Y-m-d', strtotime($this->input->post('current_date'))),'actual_addedOn'=>date('Y-m-d H:i:s'),'inventory_detail_id'=>$last_detail_id);
			$this->db->insert('presto_instruments_cp',$DRT);
			/** END **/

			/** ADD MARGIN **/

			$current_margin=$this->getcurrent_margin($prdused);
			if(count($current_margin)>0)
			{
				if($current_margin[0]==1)
				{

				$mmmvalue=$current_margin[1]/100;
				$margin=$msp+($msp*$mmmvalue);

				$mtype=$current_margin[0];
				$mValues=$current_margin[1];

				}else
				{
					$mmmvalue=$current_margin[1];
					$margin=$msp+$mmmvalue;
					$mtype=$current_margin[0];
					$mValues=$current_margin[1];
				}

			}else
			{
				$mmmvalue=15/100;
				$margin=$msp+($msp*$mmmvalue);
				$mtype=1;
				$mValues=15;
			}


			$drytey=array('product_id'=>$prdused,'margintype'=>$mtype,'marginvalue'=>$mValues,'costprice'=>$msp,'msp'=>$margin,'addedOn'=>date('Y-m-d H:i:s'));
			$this->db->insert('presto_instruments_margin_sheet',$drytey);

			/** UPDATE ALLOWED PRICE **/
			$DT=array('discount_price'=>$margin);
			$this->db->where('id',$prdused);
			$this->db->update('presto_instruments',$DT);

			


			




			}

		}










if ($this->db->trans_status() === FALSE)
			{
			$this->db->trans_rollback();
						$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
	  	redirect(page_url.'Inventory/edit_inventory/'.$id);
			}
			else
			{
			$this->db->trans_commit();
					$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
	  	redirect(page_url.'Inventory/edit_inventory/'.$id);
			}

	



	}
	function update_inventoryOldd() {

		$id=$this->uri->segment(3);

		if($this->input->post('ttype')==2)
		{
			$transporter = $this->input->post('transporter_name');
			$vehicle_no = $this->input->post('vehicle_no');
			$vehicle_type = $this->input->post('vehicle_type');
			$transport_rate = $this->input->post('transport_rate');
   		

		}else
		{
			$vehicle_no='';
			$vehicle_type='';
			$transport_rate=0;
			$transporter_id=0;
			$transporter=0;
		}


		$data = array(
				
					  'currentdate' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'bill_no' => $this->input->post('bill_no'),
					  'credit_days'=>$this->input->post('credit_days'),
					  'party' => $this->input->post('party'),
					  'transport_type'=>$this->input->post('ttype'),
					  'transporter'=>$transporter_id,
					  'vehicle_no'=>$vehicle_no,
					  'vehicle_type'=>$vehicle_type,
					  'transporter_rate'=>$transport_rate
					 );

		$this->db->where('id',$id);
		$this->db->update('inventory', $data);
		$last_id = $id;

		/** EDIT EXISTING **/
		$detail_id=$this->input->post('detail_id');
		if(count($detail_id)>0)
		{
			for($r=0;$r<count($detail_id);$r++)
			{
				$detailid=$detail_id[$r];

				$eqty=$this->input->post('edit_qty'.$detailid);
				$eunit=$this->input->post('edit_pack_size'.$detailid);
				$erate=$this->input->post('edit_rate'.$detailid);
				$elot=$this->input->post('edit_lot_no'.$detailid);
				$ebatch=$this->input->post('edit_batch_no'.$detailid);
				$emanufac=$this->input->post('edit_manufacturing_date'.$detailid);

				$dedata=array('qty'=>$eqty,'pack_size'=>$eunit,'rate'=>$erate,'lot_no'=>$elot,'batch_no'=>$ebatch,'manufacturing_date'=>date('Y-m-d',strtotime($emanufac)));

				$this->db->where('id',$detailid);
				$this->db->update('inventory_details',$dedata);


			}
		}
		/** END **/









/** ADD NEW **/
		$product = $this->input->post('product');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');
		$rate = $this->input->post('rate');
		$lot_no = $this->input->post('lot_no');
		$batch_no = $this->input->post('batch_no');
		$manufacturing_date = $this->input->post('manufacturing_date');
		if(count($product)>0)
		{
		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {
				$datas = array(
							  'inventory_id' => $last_id,
							  'product' => $product[$i],
							  'qty' => $qty[$i],
							  'pack_size' => $pack_size[$i],
							  'rate' => $rate[$i],
							  'lot_no' => $lot_no[$i],
							  'batch_no' => $batch_no[$i],
							  'manufacturing_date' => date('Y-m-d', strtotime($manufacturing_date[$i])),
							  'addedOn'=>date('Y-m-d H:i:s'),
							  'addedBy'=>$_SESSION['logged_in']['user_id']
							 );

				$this->db->insert('inventory_details', $datas);

			}

		}
	}


	

		$this->session->set_flashdata('message','<div class="alert alert-success">Inventory Successfully added.</div><br/>');
	  	redirect(page_url.'Inventory/edit_inventory/'.$id);

	}

	function delete_items()
	{
		$this->db->trans_start();
		$id=$this->uri->segment(3);
		$invid=$this->uri->segment(4);
		$this->db->where('id',$id);
		$this->db->delete('inventory_details');

		/** CHECK FOR INVENTORY INFO **/
			$reoq=$this->db->select('qty,company_id,item_id')->from('company_wise_inventory_info')->where('inventory_particular_id',$id)->get();
			if($reoq->num_rows()>0)
			{
				foreach($reoq->result() as $reqq);
				$qty=$reqq->qty;
				$company_id=$reqq->company_id;
				$item_id=$reqq->item_id;
				$exist=$this->checkforcompanystock($item_id,$company_id);
				$updated_stock=$exist[1]-$qty;
				$stdata=array('stock'=>$updated_stock,'updatedBy'=>$_SESSION['logged_in']['user_id'],'updatedOn'=>date('Y-m-d H:i:s'));
				$this->db->where('id',$exist[0]);
				$this->db->update('company_wise_inventory',$stdata);

				$this->db->where('inventory_particular_id',$id);
				$this->db->delete('company_wise_inventory_info');
			}
		/** END **/

		if ($this->db->trans_status() === FALSE)
		{

		$this->db->trans_rollback();
		$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Failed To Process the Transaction.</div>');
		redirect(page_url.'Inventory/edit_inventory/'.$invid);

		}else
		{
		$this->db->trans_commit();
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Item Deleted.</div>');
		redirect(page_url.'Inventory/edit_inventory/'.$invid);
		}



	}

	function transportation_details($tid,$vehicle_no,$vehicle_type,$trate)
	{
		$res=$this->db->select('name,mobile_no')->from('transporter_details')->where('id',$tid)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);
			$name=$row->name;
			$mobile=$row->mobile_no;
		}else
		{
			$name='';
			$mobile='';
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

        		return $html;


	}

	function pending_test_report() {
		$this->load->view('inventory/pending_test_report');
	}

	function inventory_batch_code_list() {
		$query = $this->db->select('id, batch_no,inv_detail_id')
						  ->from('inventory_batch_no')
						  ->where('test_report', 0)
						  ->get();

			$i=1;
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$product_name=$this->get_product_name_test_report($row->inv_detail_id);

						$test_report='<a href="javascript:;" class="btn btn-success btn-xs" onclick="showmodal('.$row->id.')">Upload Test Report</a>';
						$idate=$this->get_inventory_date($row->inv_detail_id);
						
						$data[] = array(
								'sr_no' => $i,
								'purchase_date'=>$idate,
								'batch_code' => $row->batch_no,
								'product_name' =>$product_name,
								'test_report'=>	$test_report												
								
							);

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

	function save_report() {
		$batch_id = $this->input->post('batch_id');
		$test_report = $_FILES['upload_file']['name'];

		if($test_report<>'')
		{
			$ar=explode('.',$test_report);
			$ext=end($ar);
			$newname=time().'.'.$ext;
			move_uploaded_file($_FILES['upload_file']['tmp_name'], assets_upload."test_report/".$newname);
		} else {
			$newname="";
		}

		$data = array(
					  'test_report' => 1,
					  'report_file' => $newname
					 );

		// echo "<pre>";print_r($data);exit;

		$this->db->where('id', $batch_id)
				 ->update('inventory_batch_no', $data);


		$this->session->set_flashdata('message','<div class="alert alert-success">Test Report Uploaded Successfully.</div><br/>');
	  	redirect(page_url.'Inventory/pending_test_report');
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

	function check_for_bulk()
	{
		$prd=$this->input->post('prd');

		$rows=$this->db->select('id')->from('presto_instruments')->where('id',$prd)->where('pack_size','BULK')->get();
		echo $rows->num_rows();
	}

	 function update_inventory_payment(){
    // echo "<pre>";
    // print_r($this->input->post());

	 	
    $inventory_id = $this->input->post('inventory_id');
    $data = array(
                'pur_paymentOn' => date('Y-m-d', strtotime($this->input->post('pur_paymentOn'))),
                'remarks' => $this->input->post('remarks'),
                'pur_paymentBy' => $this->input->post('payment_type'),  
                'payment' => $this->input->post('payment'),  
                'payment_done' => $this->input->post('payment_done'),  
                'collection_reference' => $this->input->post('collection_reference'),     
                'pur_payment' => 1,
                
               );

        if($this->input->post('payment_type') == 1){
          $data['utr_no'] = $this->input->post('utr_no');
          $picture = $_FILES['utr_evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['utr_evidence']["tmp_name"],SITE_ROOT.'evidence/'.$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }elseif($this->input->post('payment_type') == 2){
          $data['cheque_no'] = $this->input->post('cheque_no');
          $data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
          $picture = $_FILES['evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['evidence']["tmp_name"], SITE_ROOT.'evidence/'.$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }elseif($this->input->post('payment_type') == 3){
          
          $picture = $_FILES['cash_evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['cash_evidence']["tmp_name"], SITE_ROOT.'evidence/'.$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }

        $this->db->where('id',$this->input->post('inventory_id'));
        $this->db->update('inventory',$data);
        // echo $this->db->last_query();
        $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
      redirect(page_url.'Approval/upcoming_payments');

  }

   function get_company_party()
  {
    $htm='';
    $name='';
    $company=$this->input->post('company');
    $restey=$this->db->select('a.*')->from('vendors a')->where('a.company',$company)->get();
    if($restey->num_rows()>0)
    {

      $htm.="<option value='ALL'>ALL</option>";
      foreach($restey->result() as $row)

      $htm.="<option value='".$row->id."'>".$row->name."</option>";
    }

    echo $htm;

  }

   function get_company_partyNew()
  {
    $htm='';
    $name='';
    $company=$this->input->post('company');
    $restey=$this->db->select('a.*')->from('vendors a')->where('a.company',$company)->get();
    if($restey->num_rows()>0)
    {

      $htm.="<option value=''>Select</option>";
      foreach($restey->result() as $row)

      $htm.="<option value='".$row->id."'>".$row->name."</option>";
    }

    echo $htm;

  }

  function get_product_name_test_report($inv_detail_id)
  {
  	$a='';
  	$reste=$this->db->select('c.instruments_name')->from('inventory_batch_no a')->join('inventory_details b','a.inv_detail_id=b.id')->join('presto_instruments c','b.product=c.id')->where('a.inv_detail_id',$inv_detail_id)->get();
  	if($reste->num_rows()>0)
  	{
  		foreach($reste->result() as $roww);
  		$a=$roww->instruments_name;

  	}

  	return $a;

  }


  function uploaded_test_report() {
		$this->load->view('inventory/uploaded_test_report');
	}

	function inventory_batch_code_list_done() {
		$query = $this->db->select('report_file,id, batch_no,inv_detail_id')
						  ->from('inventory_batch_no')
						  ->where('test_report', 1)
						  ->get();

			$i=1;
			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$product_name=$this->get_product_name_test_report($row->inv_detail_id);

				if($row->report_file<>'')
				{
					$test_report='<a href="'.page_url1.'assets/test_report/'.$row->report_file.'" Download>'.page_url1.'assets/test_report/'.$row->report_file.'</a>';
				}else
				{
					$test_report="-";
				}

				$idate=$this->get_inventory_date($row->inv_detail_id);

				if($_SESSION['logged_in']['role']==1 || $_SESSION['logged_in']['department_id']==4)
				{
					$aa="<a href='javascript:;' onclick='reopen_test_report(".$row->id.");'><u>Reopen</u></a>";
				}else
				{
					$aa='';
				}
				

						$data[] = array(
								'sr_no' => $i,
								'purchase_date'=>$idate,
								'batch_code' => $row->batch_no,
								'product_name' =>$product_name,
								'test_report'=>	$test_report,
								'reopen'=>$aa												
								
							);

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


	 function getpayment_byid(){
    $id = $this->input->post('id');

    $qry = $this->db->select('id,DATE(pur_paymentOn)
         AS  pur_paymentOn,payment, payment_done,collection_reference,pur_paymentBy,remarks,utr_no,pur_payment_evidance,cheque_no,cheque_date')
              ->from('inventory')
              ->where('id',$id)
              ->get();
      if($qry->num_rows() > 0){
      echo json_encode($qry->row());
      }

  }

   function update_inventory_payment_details(){
    // echo "<pre>";
    // print_r($this->input->post());
    $inventory_id = $this->input->post('inventory_id');
    $data = array(
                'pur_paymentOn' => date('Y-m-d', strtotime($this->input->post('pur_paymentOn'))),
                'remarks' => $this->input->post('remarks'),
                'pur_paymentBy' => $this->input->post('payment_type'),  
                'payment' => $this->input->post('payment'),  
                'payment_done' => $this->input->post('payment_done'),  
                'collection_reference' => $this->input->post('collection_reference')
               );

        if($this->input->post('payment_type') == 1){
          $data['utr_no'] = $this->input->post('utr_no');
          $picture = $_FILES['utr_evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['utr_evidence']["tmp_name"], SITE_ROOT."evidence/".$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }elseif($this->input->post('payment_type') == 2){
          $data['cheque_no'] = $this->input->post('cheque_no');
          $data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
          $picture = $_FILES['evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['evidence']["tmp_name"], SITE_ROOT."evidence/".$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }elseif($this->input->post('payment_type') == 3){
          
          $picture = $_FILES['cash_evidence']['name'];
          $newname = '';
          if($picture <> '') {
            $files = explode('.', $picture);
            $ext = end($files);
            $newname = time().'.'.$ext;
             // echo $newname;
             // exit();
            $img = move_uploaded_file($_FILES['cash_evidence']["tmp_name"], SITE_ROOT."evidence/".$newname);
            $data['pur_payment_evidance'] = $newname;

         }
        }

        $this->db->where('id',$this->input->post('inventory_id'));
        $this->db->update('inventory',$data);
        // echo $this->db->last_query(); exit();
        $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
      redirect(page_url.'Approval/history_payments');

  }

    function send_to_tally() {
       $id = $this->input->post('id');

       $data = array(
               'send_to_tally' => 1,
               'send_to_tally_On' => date('Y-m-d'),
               'send_by' => $this->session->userdata['logged_in']['user_id']
               );

       $this->db->where('id', $id)
            ->update('inventory', $data);


       if($this->db->affected_rows() > 0) {
         echo '<strong style="color:green">Sent To Tally</strong>';
       } 
  }

  function getvolume($prdid)
  {
  	$vol=0;
  	$restey=$this->db->select('a.volume')->from('presto_instruments a')->where('a.id',$prdid)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);
			$vol=$row->volume;
		}

		return $vol;


  }

  function checkforcompanystock($product,$company)
  {
			$d=array();
			$rest=$this->db->select('id,stock')->from('company_wise_inventory')->where('company_id',$company)->where('itemid',$product)->get();
			if($rest->num_rows()>0)
			{
			foreach($rest->result() as $row);
			$d[]=$row->id;
			$d[]=$row->stock;
			}

			return $d; 
  }

  function get_company_partySelected()
  {
    $htm='';
    $name='';
    $company=$this->input->post('company');
    $selectedParty=$this->input->post('selectedParty');
    $restey=$this->db->select('a.*')->from('vendors a')->where('a.company',$company)->get();
    if($restey->num_rows()>0)
    {

      $htm.="<option value='ALL'>ALL</option>";
      foreach($restey->result() as $row)
      {
      	if($row->id==$selectedParty)
      	{
      		$a="selected";
      	}else
      	{
      		$a="";
      	}
      $htm.="<option value='".$row->id."' ".$a.">".$row->name."</option>";
    	}
    }

    echo $htm;

  }


   function imported_to_tally() {
       $id = $this->input->post('id');

       $data = array(
               'billed' => 1,
               'billed_On' => date('Y-m-d'),
               'billed_By' => $this->session->userdata['logged_in']['user_id']
               );

       $this->db->where('id', $id)
            ->update('inventory', $data);


       if($this->db->affected_rows() > 0) {
         echo '<strong style="color:green">Imported To Tally Marked</strong>';
       } 
  }

  function gettransport_drum_rate()
	{
		$d=array();
		$res=$this->db->select('cost')->from('drum_transportation_cost')->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $ress)
			{
			$d[]=$ress->cost;
		
			}
		}

		return $d;
	}


	function check_for_approval_CRNOTE($prd,$pur_date)
	{
			$vli=0;

			$restey=$this->db->select('a.credit_vli')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->where('a.product_id',$prd)->where('a.validity_from<=',date('Y-m-d',strtotime($pur_date)))->where('a.validity_to>=',date('Y-m-d',strtotime($pur_date)))->order_by('a.approved_price','ASC')->get();
			if($restey->num_rows()>0)
			{
			foreach($restey->result() as $rr);
			$vli=$rr->credit_vli;
			}

			return $vli;

	}

	function getcurrent_margin($prd)
	{
		$dt=array();
		$rt=$this->db->select('margintype,marginvalue')->from('presto_instruments_margin_sheet')->where('product_id',$prd)->order_by('id','DESC')->get();
		if($rt->num_rows()>0)
		{
			foreach($rt->result() as $row);
			$dt[]=$row->margintype;
			$dt[]=$row->marginvalue;
		}else
		{
			$dt[]=1;
			$dt[]=15;
		}

		return $dt;

	}



function NEWCPCODE_ORIGINAL(){



$this->db->select('a.pack_size,a.hsncode,a.id, a.instruments_name,a.unit,a.stock')
                ->from('presto_instruments a')
               ->where('id',92);

                $query =$this->db->get();


              $i=1;
              if($query->num_rows() > 0) {
              foreach($query->result() as $row) {
            
            
        

                 $getOpenstock = $this->mastermodel->get_Open_stock_for_product_AUTO($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);


                $ops=explode('~',$getOpenstock);

                $opening_Q=$ops[0];
                $opening_R=$ops[1];
                $opening_V=$opening_Q*$opening_R;
             
            


                $instock=$this->mastermodel->get_inward_between_dates($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);
                $opin=explode('~',$instock);
                $opin_Q=$opin[0];
                $opin_R=$opin[1];
                $opin_V=$opin_Q*$opin_R;


              $outstock=$this->mastermodel->get_outward_between_dates($this->uri->segment(3),$this->uri->segment(4),$this->uri->segment(5),$row->id);

              $opout=explode('~',$outstock);
              $opout_Q=$opout[0];
              $opout_R=$opout[1];
              $opout_V=$opout_Q*$opout_R;
              $closing=$opening_Q+$opin_Q-$opout_Q;
              if($opening_Q>0 || $opin_Q>0)
              {
              $closing_R=(($opening_Q*$opening_R)+($opin_Q*$opin_R))/($opening_Q+$opin_Q);
              //echo $closing_R; exit;
              }else
              {
                $closing_R=0;
              }

              $closing_V=$closing*$closing_R;

              $msp=($opening_V+$opin_V)/($opening_Q+$opin_Q);
              echo $msp; exit;;

          }

      }






    


}

	function NewCPCODE()
	{

		$TTrate=$this->gettransport_drum_rate();
		if(count($TTrate)>0)
		{
		$transportCOST=$TTrate[0];
		$drumCOST=$TTrate[1];
		}else
		{
		$transportCOST=0;
		$drumCOST=0;
		}

		$rest=$this->db->select('id,pack_size,volume')->from('presto_instruments')->where('status',1)->get();
	if($rest->num_rows()>0)
	{
		foreach($rest->result() as $rowss)
		{
			$prdused=$rowss->id;
		    /*** CP FOR PRODUCT **/

			$st='2023-09-19';
			$en=date('Y-m-d');
			$com="ALL";
			$getOpenstock = $this->mastermodel->get_Open_stock_for_product($st,$en,$com,$prdused);
			// if($com==3)
			// {
			$vli = $this->check_for_approval_CRNOTE($prdused,date('Y-m-d'));
			// }else
			// {
			// 	$vli=0;
			// }

			if($rowss->pack_size=="BULK")
			{
				$drumCOST=$drumCOST;
			}else
			{
				$drumCOST=0;
			}


			$ops=explode('~',$getOpenstock);
			$opening_Q=$ops[0];
			$opening_R=$ops[1];
			$opening_V=$opening_Q*$opening_R;
			$instock=$this->mastermodel->get_inward_between_dates($st,$en,$com,$prdused);

			$opin=explode('~',$instock);
			
			$opin_Q=$opin[0];
			$opin_R=$opin[1];
			$opin_V=$opin_Q*$opin_R;

			
		

			if(($opening_Q+$opin_Q)>0 && ($opening_V+$opin_V)>0)
			{
			$msp=($opening_V+$opin_V)/($opening_Q+$opin_Q);
			}else
			{
				$msp=0;
			}



			if($msp>0)
			{
			$msp=$msp+$drumCOST+$transportCOST-$vli;
			$msp=round($msp);
			}else
			{
				$msp=0;
			}

			
			


			/** ADD REFRENCE **/
			$DRT=array('product_id'=>$prdused,'billing_price'=>$msp,'vli'=>$vli,'drum'=>$drumCOST,'transport'=>$transportCOST,'costprice'=>$msp,'addedOn'=>date('Y-m-d', strtotime($this->input->post('current_date'))),'actual_addedOn'=>date('Y-m-d H:i:s'),'inventory_detail_id'=>0,'opening_volume'=>$opening_Q,'opening_rate'=>$opening_R,'inward_volume'=>$opin_Q,'inward_rate'=>$opin_R);
			$this->db->insert('presto_instruments_cp',$DRT);
			/** END **/

			/** ADD MARGIN **/
			if($msp>0)
			{
			

				$current_margin=$this->getcurrent_margin($prdused);
				$mmmvalue=15/100;
				$margin=$msp+($msp*$mmmvalue);
				$mtype=1;
				$mValues=15;

				$drytey=array('product_id'=>$prdused,'margintype'=>$mtype,'marginvalue'=>$mValues,'costprice'=>$msp,'msp'=>$margin,'addedOn'=>date('Y-m-d H:i:s'));
				$this->db->insert('presto_instruments_margin_sheet',$drytey);

				/** UPDATE ALLOWED PRICE **/
				$DT=array('discount_price'=>$margin,'costprice'=>$msp);
				$this->db->where('id',$prdused);
				$this->db->update('presto_instruments',$DT);
			}
		}
	}

			

	}


	function transporter_pending_payment_list_incoming() {

$lead_data = array();
$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
$party=$this->uri->segment(5);
$company=$this->uri->segment(6);
		$data = array();
		$i=1;
		         $this->db->select('a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname')
						  ->from('inventory a')
              ->join('vendors d', 'd.id=a.party','left')
              ->join('store_rack_location s', 's.id=a.hpcl_billing_company','left');
            

				if($start_date <> '' && $end_date <> '' ) {
				$this->db->where('a.currentdate >=', $start_date);
				$this->db->where('a.currentdate <=', $end_date);
				}
			
				$this->db->where('a.transport_type',2);
				$this->db->where('a.transporter_payment',0);

      if($party<>'' && $party<>'ALL')
      {
        $this->db->where('a.transporter',$party);
      }

      if($company<>'' && $company<>'ALL')
      {
        $this->db->where('a.hpcl_billing_company',$company);
      }


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_detailsfortransport($row->id);
					$pdetails=explode('|',$purchase_details);
					

					if($row->transport_type==2)
					{
						$transport_details=$this->transportation_details_Incoming($row->transporter,$row->vehicle_no,$row->vehicle_type,$row->transporter_rate);
						$trt=explode('~',$transport_details);
						$trt_detail=$trt[0];
						$trt_rate=$trt[1];
						$tds=$trt[2];
						$gst=$trt[3];
						$gst_appl=$trt[4];
					}else
					{
						$transport_details='';
						$trt_detail='';
						$trt_rate=0;
						$tds=0;
						$gst='';
						$gst_appl=0;
					}

						
						$chkIfProductExists = 1;						
						if($chkIfProductExists>0)
						{
             
             	
             				$total=round($trt_rate*$pdetails[1]);
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
						
						$payment="<a href='javascript:;' onclick='payment_done(".$row->id.",".$total.",".$row->transporter.")' class='btn btn-warning'>Update Payment</a>";  
            
						$data[] = array(
								'sr_no' => $i,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                				'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'gstno'=>$row->gst,
								'credit_days'=>$row->credit_days." Days",
								'product_detail'=>$pdetails[0],
								'transport_detail'=>$trt_detail,
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".$pdetails[1]." LTR</strong>",
								'total'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$total."</strong>",
								'tds'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$tds_total."</strong>",
								'gst'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_total."</strong>",
								'payable'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$total."</strong>",
								'payment'=>$payment
							);

						$i++;
					}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}

		function get_purchase_detailsfortransport($id)
	{
		  $html='<table class="table table-bordered">
        <thead>
        <tr>
        <th style="width:20px;">Sr no.</th>
        <th style="width:100px;">Product</th>
         <th style="width:30px;">Qty</th>
       
        </tr>
        </thead><tbody>';

        $tot=array();
        $tot[]=0;
		$reste=$this->db->select('a.id, a.product,a.pack_size,a.qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname, a.bulkproducttype, d.instruments_name as second_instruments_name')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('presto_instruments d','a.secondproduct=d.id','left')->where('a.inventory_id',$id)->get();
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

if($rows->bulkproducttype==1){
$bulktype="Bulk to Bulk";
}else if($rows->bulkproducttype==2){
$bulktype="Bulk to Drum";
}else{
	$bulktype = "NA";
}

if($rows->second_instruments_name){
$prd = $rows->second_instruments_name;
}else{
	$prd = "NA";
}
				$total=$rows->qty*$rows->rate;
					$html.='<tr>
					<td>'.$i.'</td>
					<td>'.$rows->instruments_name.'</td>
					<td>'.$rows->qty.' '.$rows->shortname.'</td>
					</tr>';

					$tot[]=$rows->qty;
			$i++;
		}

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

        		return $html."~".$trate."~".$tds."~".$gst.'~'.$gst_appl;


	}


function transporter_pending_payment_incoming_filter()
{
	$company=$this->input->post('company');
		
		
		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$transporter=$this->input->post('transporter');
		redirect(page_url.'Billing/transporter_pending_payment_incoming/'.$from_date.'/'.$to_date.'/'.$transporter.'/'.$company);

}


function get_transporter_payment_details_for_approval_based_transportation()
	{
		$final_date='';
		$final_claim=0;
		$transporter_name='';
		$amount=0;
		$id=$this->uri->segment(3);
		 $this->db->select('c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location c','a.hpcl_location=c.id')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');
						  $this->db->where('a.payment',0);
						  $this->db->where('a.transport_done_by',2);
						  $this->db->where('a.id',$id);
						  $res=$this->db->get();

		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);
			

			$getApprovalProductDetails = $this->salescrm->transportation_based_approval_ProductDetails($row->id,"ALL","ALL");
				
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

					$claim = $this->salescrm->transportation_based_approval_Productclaim($row->id,"ALL","ALL",$transported_fixed_rate);
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


}

			
		

		}


		echo $final_claim."|".$transporter_name;
	}


	function gettransporterdetail(){
		$id = $this->uri->segment(3);
		$qq = $this->db->select('id, name')->from('transporter_details')->where('id',$id)->get();
		foreach($qq->result() as $row);
		echo $row->name."|".$row->id;
	}


	function update_payment_against_transporter_inventory(){
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
		$this->db->update('inventory',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you! Payment Successfully updated.</div>');
		redirect(page_url.'Billing/transporter_pending_payment_incoming/'.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6));

		
	}

		function transporter_pending_payment_list_incoming_history() {

$lead_data = array();
$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
$party=$this->uri->segment(5);
$company=$this->uri->segment(6);
		$data = array();
		$i=1;
		         $this->db->select('a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname, a.transporter_payment_amount, a.transporter_payment_evidence, a.transporter_payment_bill, a.transporter_payment_remarks, transporter_payment_date')
						  ->from('inventory a')
              ->join('vendors d', 'd.id=a.party','left')
              ->join('store_rack_location s', 's.id=a.hpcl_billing_company','left');
            

				if($start_date <> '' && $end_date <> '' ) {
				$this->db->where('a.currentdate >=', $start_date);
				$this->db->where('a.currentdate <=', $end_date);
				}
			
				$this->db->where('a.transport_type',2);
				$this->db->where('a.transporter_payment',1);

      if($party<>'' && $party<>'ALL')
      {
        $this->db->where('a.transporter',$party);
      }

      if($company<>'' && $company<>'ALL')
      {
        $this->db->where('a.hpcl_billing_company',$company);
      }


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_detailsfortransport($row->id);
					$pdetails=explode('|',$purchase_details);
					

					if($row->transport_type==2)
					{
						$transport_details=$this->transportation_details_Incoming($row->transporter,$row->vehicle_no,$row->vehicle_type,$row->transporter_rate);
						$trt=explode('~',$transport_details);
						$trt_detail=$trt[0];
						$trt_rate=$trt[1];
						$tds=$trt[2];
						$gst=$trt[3];
						$gst_appl=$trt[4];
					}else
					{
						$transport_details='';
						$trt_detail='';
						$trt_rate=0;
						$tds=0;
						$gst='';
						$gst_appl=0;
					}

						
						$chkIfProductExists = 1;						
						if($chkIfProductExists>0)
						{
             
             	
             				$total=round($trt_rate*$pdetails[1]);
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
						
						$payment="<a href='javascript:;' onclick='payment_done(".$row->id.",".$total.",".$row->transporter.")' class='btn btn-warning'>Update Payment</a>";  
            			$transporter_payment_evidence = '<a href="'.UPLOADPATH.'transpoterpayment/evidence/'.$row->transporter_payment_evidence.'" download>Evidence Download</a>';
            			$transporter_payment_bill = '<a href="'.UPLOADPATH.'transpoterpayment/'.$row->transporter_payment_bill.'" download>Bill Download</a>';
						$data[] = array(
								'sr_no' => $i,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                				'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'gstno'=>$row->gst,
								'credit_days'=>$row->credit_days." Days",
								'product_detail'=>$pdetails[0],
								'transport_detail'=>$trt_detail,
								'transporter_payment_date'=>$row->transporter_payment_date,
								'transporter_payment_evidence'=>$transporter_payment_evidence,
								'transporter_payment_bill'=>$transporter_payment_bill,
								'transporter_payment_remarks'=>$row->transporter_payment_remarks,
								'transporter_payment_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$row->transporter_payment_amount."</strong>",
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".$pdetails[1]." LTR</strong>",
								'total'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$total."</strong>",
								'tds'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$tds_total."</strong>",
								'gst'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_total."</strong>",
								'payable'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$total."</strong>",
								'payment'=>$payment
							);

						$i++;
					}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}

	public function inventory_summary()
	{
		$this->load->view('inventory/inventory_summary');
	}




	function this_month_purchases_summary() {
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$party=$this->uri->segment(5);
    $product=$this->uri->segment(6);
    $company=$this->uri->segment(7);
    $alltime=$this->uri->segment(8);
		$data = array();
		$i=1;
				$this->db->select('c.secondproduct,c.changed_product,e.instruments_name,c.product,c.qty,c.rate,a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname')
				->from('inventory_details c')
				->join('inventory a','c,inventory_id=a.id')
				->join('vendors d', 'd.id=a.party','left')
				->join('store_rack_location s', 's.id=a.hpcl_billing_company','left')
				->join('presto_instruments e','c.product=e.id');
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


		   $query =  $this->db->order_by('a.currentdate')->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					

			  $pdetails=$row->qty*$row->rate;
              $ggst=$row->gst/100;
              $gst_amount=$pdetails*$ggst;
              $grandtotal=$pdetails+$gst_amount;

              if($row->changed_product==1)
              {
              	$ch="<strong style='color:red;font-weight:bold;'>(Changed Product)</strong>";
              	$prd=$this->getproductName($row->secondproduct);
              }else
              {
              	$ch="";
              	$prd="";
              }

							

            
						$data[] = array(
								'sr_no' => $i,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                				'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'credit_days'=>$row->credit_days." Days",
								'product'=>$row->instruments_name."<br/><strong>".$ch."</strong>",
								'actual_product'=>$prd,
								'qty'=>$row->qty,
								'rate'=>$row->rate,
													
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails."</strong>",
								'gst_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_amount."</strong>",
								'grand_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$grandtotal."</strong>"										
								
							);

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


		function filter_purchase_summary()
		{
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$hpcl_locations=$this->input->post('hpcl_locations');
		$products=$this->input->post('products');
		$company=$this->input->post('company');
		$alltime=$this->input->post('alltime');
		if($alltime==1)
		{
		$alltime=1;
		}else
		{
		$alltime=0;
		}


		redirect(page_url.'Inventory/inventory_summary/'.$from_date."/".$to_date."/".$hpcl_locations."/".$products."/".$company.'/'.$alltime);
		}

	public function inventory_summary_with_dencity_report()
	{
		$this->load->view('inventory/inventory_summary_density_report');
	}


		function this_month_purchases_summary_with_density() {
			$data = array();
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$party=$this->uri->segment(5);
    $product=$this->uri->segment(6);
    $company=$this->uri->segment(7);
    $alltime=$this->uri->segment(8);
		$data = array();
		$i=1;
				$this->db->select('e.instruments_name,c.product, c.density, c.qty,c.rate,a.billed,a.billed_On,a.billed_By,a.gst,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.send_to_tally,a.send_to_tally_On,s.companyname')
				->from('inventory_details c')
				->join('inventory a','c,inventory_id=a.id')
				->join('vendors d', 'd.id=a.party','left')
				->join('store_rack_location s', 's.id=a.hpcl_billing_company','left')
				->join('presto_instruments e','c.product=e.id');
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

		   $query =  $this->db->order_by('a.currentdate')->get();

			if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					

			  $pdetails=$row->qty*$row->rate;
              $ggst=$row->gst/100;
              $gst_amount=$pdetails*$ggst;
              $grandtotal=$pdetails+$gst_amount;


              $density = $row->density;
              $mass = $row->qty*$density;
              				

            
						$data[] = array(
								'sr_no' => $i,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                				'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'credit_days'=>$row->credit_days." Days",
								'product'=>$row->instruments_name,
								'qty'=>$row->qty,
								'rate'=>$row->rate,
								'mass'=>ceil($mass),
								'density'=> $density,					
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails."</strong>",
								'gst_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_amount."</strong>",
								'grand_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$grandtotal."</strong>"										
								
							);

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

		function filter_purchase_summary_with_density()
		{
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$hpcl_locations=$this->input->post('hpcl_locations');
		$products=$this->input->post('products');
		$company=$this->input->post('company');
		$alltime=$this->input->post('alltime');
		if($alltime==1)
		{
		$alltime=1;
		}else
		{
		$alltime=0;
		}


		redirect(page_url.'Inventory/inventory_summary_with_dencity_report/'.$from_date."/".$to_date."/".$hpcl_locations."/".$products."/".$company.'/'.$alltime);
		}


		public function empty_barrel_report()
		{
		$this->load->view('inventory/empty_barrels_report');
		}


		function empty_barrel_report_list() {
			$data = array();
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
              




              $data[] = array(
								'sr_no' => $i,
								'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
                				'company_name' =>$row->companyname,
								'bill_no' =>$row->bill_no,
								'party'=>$row->party,
								'credit_days'=>$row->credit_days." Days",
								'product'=>$row->instruments_name,
								'qty'=>$row->qty,
								'rate'=>$row->rate,
								'mass'=>ceil($mass),
								'density'=> $density,
								'barrel'=>"<strong style='font-size:18px; color:red; font-weight:bold'>".ceil($volume)."</strong>",
								'bulktodrum'=>$secondproductname,
								'volume'=>$volumeval,					
								'total_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails."</strong>",
								'gst_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$gst_amount."</strong>",
								'grand_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$grandtotal."</strong>"										
								
							);

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

	function get_part_detail()
	{
		$flag=0;
		$party=$this->input->post('party');
		$d=$this->db->select('hpcl')->from('vendors')->where('id',$party)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $dd);
			$flag=$dd->hpcl;
		}

		echo $flag;

	}

	function get_inventory_date($invid)
	{
		$date='';
		$d=$this->db->select('b.currentdate')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('a.id',$invid)->get();
		if($d->num_rows()>0)
		{
			foreach($d->result() as $ddd);
			$date=date('d-M-Y',strtotime($ddd->currentdate));
		}

		return $date;

	}

	function reopen_batch()
	{
		$id=$this->uri->segment(3);
		$d=array('test_report'=>0);
		$this->db->where('id',$id);
		$this->db->update('inventory_batch_no',$d);

		$this->session->set_flashdata('message','<div class="alert alert-success">Batch No. Reopened.</div><br/>');
		redirect(page_url.'Inventory/uploaded_test_report');
	}

	function delete_batch()
	{
		$id=$this->uri->segment(3);
		$edit_id=$this->uri->segment(4);
		$this->db->where('id',$id);
		$this->db->delete('inventory_batch_no');

		$this->session->set_flashdata('message','<div class="alert alert-success">Batch No. Deleted</div><br/>');
		redirect(page_url.'Inventory/edit_inventory/'.$edit_id);
	}

	function delete_inventory(){

		$id=$this->uri->segment(3);
		$flag1=$this->uri->segment(4);
		$flag2=$this->uri->segment(5);
		$flag3=$this->uri->segment(6);
		$this->db->where('id',$id);
		$this->db->delete('inventory');
		$this->session->set_flashdata('message','<div class="alert alert-success">Record Deleted</div><br/>');
		redirect(page_url.'Inventory/this_month_purchases/'.$flag1.'/'.$flag2.'/'.$flag3);


	}

	function change_batch_no()
	{
		$batch_code_id=$this->input->post('batch_code_id');
		$batch_code_change=$this->input->post('batch_code_change');
		$edit_id=$this->input->post('edit_id');
		$data=array('batch_no'=>$batch_code_change);
		$this->db->where('id',$batch_code_id);
		$this->db->update('inventory_batch_no',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success">Batch No Changed</div><br/>');
		redirect(page_url.'Inventory/edit_inventory/'.$edit_id);

	}


function getproductName($id)
{
	$prd='';
	$r=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$id)->get();
	if($r->num_rows()>0)
	{
		foreach($r->result() as $row);
		$prd=$row->instruments_name;
	}

	return $prd;

}

function check_batch_no(){

	$a=array();
	$d='';
	$batch_no=$this->input->post('batch_no');
	$product=$this->input->post('product');
	if($batch_no!='' && $product!='')
	{
		$rt=explode(',',$batch_no);
		if(count($rt)>0)
		{
			foreach($rt as $rtt)
			{
				$rytyu=$this->db->select('a.inv_detail_id,a.batch_no,c.instruments_name')->from('inventory_batch_no a')->join('inventory_details b','a.inv_detail_id=b.id')->join('presto_instruments c','b.product=c.id')->where('a.batch_no',trim($rtt))->where('b.product!=',$product)->get();
				if($rytyu->num_rows()>0)
				{
					foreach($rytyu->result() as $rows)
					{
						$a[]=$rows->batch_no." Cannot be Used<br/>Previously used for- ".$rows->instruments_name;
					}
				}


			}

		}
	}

	if(count($a)>0)
	{
		$d=implode('<br/><br/>',$a);
	}

	echo $d;

}




}


