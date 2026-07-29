<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Salescrm_model','salescrm');
	}

	public function index() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/approval_list', $data);
	}

	public function approval() {
		$this->load->view('approval/approval');
	}

	function approval_list() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);

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

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
					$edit = "<a href='".page_url."Approval/edit_approval/".$row->id."' class='btn btn-xs btn-default'>Edit Approval</a>";	
					$item_picked_up = "<input type='checkbox' name='item_picked_up[]' id='item_picked_up".$row->id."' value='".$row->id."' onchange='chk_item_picked_up(".$row->id.")'>";
					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";

					 if($row->purchase_entry==0)
					 {
						$pdetail="No Purchase Entry Has been made";

					$purchase_inventory = "<a href='".page_url."Approval/purchase_inventory/".$row->id."' class='btn btn-info btn-xs'>Purchase Entry</a>";
					 }else
					 {
						$pdetail ="<a href='".page_url."Approval/purchase_details/".$row->id."' target='_blank'>Purchase Details</a>";

						$purchase_inventory='';
					}


					
					if($row->payment_terms == 1) {
						$payment_terms = 'ADVANCE';
						$days='';
					} else if($row->payment_terms == 2) {
						$payment_terms = 'CREDIT PERIOD';
						$days=$row->credit_period." Days";
					} else{
						$payment_terms = '';
						$days='';
					}

					if($row->combination==0)
						{
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails($row->id);
						}else
						{
					$getApprovalProductDetails=$this->salescrm->getApprovalProductDetails_combination($row->id);
						}

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}


					if($hpcl_location != '' && $hpcl_location != 'ALL') {
						$chkIflocationExists = $this->salescrm->chkIfLocationExists($row->id, $hpcl_location);
					} else {
						$chkIflocationExists = 1;
					}



					if($chkIfProductExists > 0 && $chkIflocationExists>0) {

						if($row->combination==0)
						{
							$app_type="Per Product Approval";
						}else
						{
							$app_type="Combination Approval";
						}
						
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-1'.$row->auto_gen_code."<br/><strong style='color:red;'>".$app_type."</strong>",
							'hpcl_location'=>'',
							'product_details'=>$getApprovalProductDetails,
							'payment_terms'=>$payment_terms,
							'credit_period'=>$days,
							'edit'=>$edit,
							'purchase_entry'=>$pdetail,
							'csra_export' => $csra_export,
							'credit_note_claim_amount'=>''
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

	function save_approval() {
		$combination_appr=$this->input->post('combination_approval');

		if($combination_appr=='')
		{
			$combination=0;

		}else
		{
			$combination=1;
		}

		$getLastInsertedCode = $this->salescrm->getLastInsertedCode();

		$data = array(
					  'auto_gen_code' => $getLastInsertedCode,
					  'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'payment_terms' => $this->input->post('payment_terms'),
					  'credit_period' => $this->input->post('credit_period'),
					  'added_on' => date('Y-m-d H:i:s'),
					  'added_by' => $_SESSION['logged_in']['user_id'],
					  'combination'=>$combination,
					  'transportation' => $this->input->post('transportation')
					  // 'transportation_rate' => $this->input->post('rate')
					 );

		$this->db->insert('approval_form', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$unit = $this->input->post('unit');
		// $pack_size = $this->input->post('pack_size');
		$approved_price = $this->input->post('approved_price');
		$validity_from=$this->input->post('valid_from');
		$validity_to=$this->input->post('valid_till');
		$credit_vli = $this->input->post('credit_vli');
		$moq = $this->input->post('moq');
		$hpcl_location = $this->input->post('hpcl_location');
		$historical_moq = $this->input->post('historical_moq');
		$year = $this->input->post('year');
		$increment = $this->input->post('increment');
		$invoice_no = $this->input->post('invoice_no');
		$billing_date = $this->input->post('billing_date');
		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {


				if($historical_moq[$i]==1 && $combination==0)
				{

					$moq_provided=$this->getPreviousPurchase($year[$i],$increment[$i],$product[$i]);
				
				}else
				{
					$moq_provided=$moq[$i];
				}

				$annex_upload = $_FILES['annex_upload']['name'][$i];
				if($annex_upload<>'')
				{
				$tmp_name=explode('.',$annex_upload);
				$extn=end($tmp_name);
				$newname=time().$i.'.'.$extn;
				$uploadFilePath = SITE_ROOT.'type_one_annexure/'.basename($newname);
				move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
				}else
				{
				$newname='';
				}

				$datas = array(
							  'approval_id' => $last_id,
							  'location'=>$hpcl_location[$i],
							  'product_id' => $product[$i],
							  'pack_size'=>$unit[$i],
							  'validity_from'=>date('Y-m-d', strtotime($validity_from[$i])),
							  'validity_to'=>date('Y-m-d', strtotime($validity_to[$i])),
							  'approved_price' => $approved_price[$i],
							  'credit_vli' => $credit_vli[$i],
							  'moq' => $moq_provided,
							  'annexture' => $annexture[$i],
                'annexure_upload'=>$newname
               // 'invoice'=>$invoice_no[$i],
               // 'billing_date'=>date('Y-m-d',strtotime($billing_date[$i]))
					  		  );

				$this->db->insert('approval_product_details', $datas);
			}
		}


		/** INSERT COMBINATION **/
		if($combination==1)
		{

		$combination_type=$this->input->post('combination_type');
		$comb_product=$this->input->post('comb_product');
		if($combination_type==1)
				{
					$moq=$this->input->post('comb_moq');
					$vli=0;
				} else if($combination_type==2)
				{
					$vli=$this->input->post('comb_credit_vli');
					$moq=0;
				}else if($combination_type==3)
				{
					$moq=$this->input->post('comb_moq');
					$vli=$this->input->post('comb_credit_vli');
				}else
				{
					$moq=0;
					$vli=0;
				}

				$app_detail=array('approval_id'=>$last_id,'type'=>$combination_type,'moq'=>$moq,'vli'=>$vli);
				$this->db->insert('approval_combination_type_1',$app_detail);
				$combinationid=$this->db->insert_id();

		if(count($comb_product)>0)
		{
			for($j=0;$j<count($comb_product);$j++)
			{
				
				$ap_detail=array('approval_id'=>$last_id,'combination_id'=>$combinationid,'product'=>$comb_product[$j]);

				$this->db->insert('approval_combination_detail_type_1',$ap_detail);
				
			}
		}



		}

		/** END **/

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval');

	}

	function edit_approval() {
		$this->load->view('approval/edit_approval');
	}

	function delete_approval_product() {
		$id = $this->input->post('id');

		$this->db->where('id', $id)
				 ->delete('approval_product_details');

		if($this->db->affected_rows() > 0) {
			echo 1;
		}
	}


	function update_approval() {
        $combination_appr=$this->input->post('combination_approval');
       // echo $combination_appr; exit;
    

		$data = array(
					  // 'hpcl_location' => $this->input->post('hpcl_location'),
            'combination' => $combination,
            'current_date' => date('Y-m-d',strtotime($this->input->post('current_date'))),
            'payment_terms' => $this->input->post('payment_terms'),
					  'credit_period' => $this->input->post('credit_period'),
            'transportation' => $this->input->post('transportation'),
					 );

		$this->db->where('id', $this->uri->segment(3))
				 ->update('approval_form', $data);

    $product = $this->input->post('product');
    $hpcl_location = $this->input->post('hpcl_location');
		$pack_size = $this->input->post('pack_size');
		$approved_price = $this->input->post('approved_price');
		// $price_validity = $this->input->post('price_validity');
		$credit_vli = $this->input->post('credit_vli');
		$moq = $this->input->post('moq');
    $historical_moq = $this->input->post('historical_moq');
    $year = $this->input->post('year');
    $increment = $this->input->post('increment');
    $validity_from=$this->input->post('valid_from');
    $validity_to=$this->input->post('valid_till');
    $annexture = $this->input->post('annexture');

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {

        if($historical_moq[$i]==1 && $combination==0)
        {

          $moq_provided=$this->getPreviousPurchase($year[$i],$increment[$i],$product[$i]);
        
        }else
        {
          $moq_provided=$moq[$i];
        }

        $annex_upload = $_FILES['annex_upload']['name'][$i];
        if($annex_upload<>'')
        {
        $tmp_name=explode('.',$annex_upload);
        $extn=end($tmp_name);
        $newname=time().$i.'.'.$extn;
        $uploadFilePath = SITE_ROOT.'type_one_annexure/'.basename($newname);
        move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
        }else
        {
        $newname='';
        }

				$datas = array(
							  'approval_id' => $this->uri->segment(3),
                'product_id' => $product[$i],
                'location' => $hpcl_location[$i],
							  'pack_size' => $pack_size[$i],
							  'approved_price' => $approved_price[$i],
                'validity_from'=>date('Y-m-d', strtotime($validity_from[$i])),
                'validity_to'=>date('Y-m-d', strtotime($validity_to[$i])),
							  // 'price_validity' => $price_validity[$i],
							  'credit_vli' => $credit_vli[$i],
							  'moq' => $moq_provided,
                'annexture' => $annexture[$i],
                'annexure_upload'=>$newname
					  		  );



				$this->db->insert('approval_product_details', $datas);
			}
		}

		$approval_detail_id = $this->input->post('approval_detail_id');
		// echo "<pre>";print_r($approval_detail_id);exit;
    $edit_product = $this->input->post('edit_product');
		$edit_hpcl_location = $this->input->post('edit_hpcl_location');
		$edit_pack_size = $this->input->post('edit_pack_size');
    $edit_approved_price = $this->input->post('edit_approved_price');
    $edit_valid_from = $this->input->post('edit_valid_from');
    $edit_valid_till = $this->input->post('edit_valid_till');
		$edit_price_validity = $this->input->post('edit_price_validity');
		$edit_credit_vli = $this->input->post('edit_credit_vli');
		$edit_moq = $this->input->post('edit_moq');
    $edit_annexture = $this->input->post('edit_annexture');
    $edit_historical_moq = $this->input->post('edit_historical_moq');
    $edit_year = $this->input->post('edit_year');
    $edit_increment = $this->input->post('edit_increment');

// echo "<pre>";
// print_r($this->input->post());

		for($j = 0; $j < count($approval_detail_id); $j++) {
			if($approval_detail_id[$j] != '') {

      if($edit_historical_moq[$j]==1 && $combination==0)
        {
          if($edit_year[$j] != '' && $edit_increment[$j] != ''){
            $moq_provided=$this->getPreviousPurchase($edit_year[$j],$edit_increment[$j],$edit_product[$j]);
          }else{
            $moq_provided=$edit_moq[$j];
          }
          
        
        }else
        {
          $moq_provided=$edit_moq[$j];
        }



				$datas = array(
                'product_id' => $edit_product[$j],
							  'location' => $edit_hpcl_location[$j],
							  'pack_size' => $edit_pack_size[$j],
							  'approved_price' => $edit_approved_price[$j],
                'validity_from'=>date('Y-m-d', strtotime($edit_valid_from[$j])),
                'validity_to'=>date('Y-m-d', strtotime($edit_valid_till[$j])),
							  'price_validity' => $edit_price_validity[$j],
							  'credit_vli' => $edit_credit_vli[$j],
							  'moq' => $moq_provided,
                'annexture' => $edit_annexture[$j],
					  		  );

        $edit_annex_upload = $_FILES['edit_annex_upload']['name'][$j];
        if($edit_annex_upload<>'')
        {
        $tmp_name=explode('.',$edit_annex_upload);
        $extn=end($tmp_name);
        $newname=time().$j.'.'.$extn;
        $uploadFilePath = SITE_ROOT.'type_one_annexure/'.basename($newname);
        move_uploaded_file($_FILES['edit_annex_upload']['tmp_name'][$j], $uploadFilePath);

        $datas['annexure_upload'] = $newname;
        }
// print_r($datas);

				$this->db->where('id', $approval_detail_id[$j])
						 ->update('approval_product_details', $datas);
        // echo $this->db->last_query();
			}
		}
// exit();


    /** INSERT COMBINATION **/
    if($combination==1)
    {

    $combination_type=$this->input->post('combination_type');
    $comb_product=$this->input->post('comb_product');
    if($combination_type==1)
        {
          $moq=$this->input->post('comb_moq');
          $vli=0;
        } else if($combination_type==2)
        {
          $vli=$this->input->post('comb_credit_vli');
          $moq=0;
        }else if($combination_type==3)
        {
          $moq=$this->input->post('comb_moq');
          $vli=$this->input->post('comb_credit_vli');
        }else
        {
          $moq=0;
          $vli=0;
        }

        $app_detail=array('type'=>$combination_type,'moq'=>$moq,'vli'=>$vli);
        // $this->db->insert('approval_combination_type_1',$app_detail);
        // $combinationid=$this->db->insert_id();

        $this->db->where('approval_id', $this->uri->segment(3))
             ->update('approval_combination_type_1', $app_detail);

    if(count($comb_product)>0)
    {
      for($j=0;$j<count($comb_product);$j++)
      {
        if($comb_product[$j] != ''){

          $ap_detail=array('approval_id'=>$this->uri->segment(3),'combination_id'=>$this->input->post('combination_id'),'product'=>$comb_product[$j]);

          $this->db->insert('approval_combination_detail_type_1',$ap_detail);
        }
        
        
      }
    }



    }



		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Updated.</div>');
     	redirect(page_url.'Approval');

	}
	function update_approvalOLddd() {
		$data = array(
					  'hpcl_location' => $this->input->post('hpcl_location'),
					  'payment_terms' => $this->input->post('payment_terms'),
					  'credit_period' => $this->input->post('credit_period')
					 );

		$this->db->where('id', $this->uri->segment(3))
				 ->update('approval_form', $data);

		$product = $this->input->post('product');
		$pack_size = $this->input->post('pack_size');
		$approved_price = $this->input->post('approved_price');
		$price_validity = $this->input->post('price_validity');
		$credit_vli = $this->input->post('credit_vli');
		$moq = $this->input->post('moq');

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {
				$datas = array(
							  'approval_id' => $this->uri->segment(3),
							  'product_id' => $product[$i],
							  'pack_size' => $pack_size[$i],
							  'approved_price' => $approved_price[$i],
							  'price_validity' => $price_validity[$i],
							  'credit_vli' => $credit_vli[$i],
							  'moq' => $moq[$i]
					  		  );

				$this->db->insert('approval_product_details', $datas);
			}
		}

		$approval_detail_id = $this->input->post('approval_detail_id');
		// echo "<pre>";print_r($approval_detail_id);exit;
		$edit_product = $this->input->post('edit_product');
		$edit_pack_size = $this->input->post('edit_pack_size');
		$edit_approved_price = $this->input->post('edit_approved_price');
		$edit_price_validity = $this->input->post('edit_price_validity');
		$edit_credit_vli = $this->input->post('edit_credit_vli');
		$edit_moq = $this->input->post('edit_moq');

		for($j = 0; $j < count($approval_detail_id); $j++) {
			if($approval_detail_id[$j] != '') {
				$datas = array(
							  'product_id' => $edit_product[$j],
							  'pack_size' => $edit_pack_size[$j],
							  'approved_price' => $edit_approved_price[$j],
							  'price_validity' => $edit_price_validity[$j],
							  'credit_vli' => $edit_credit_vli[$j],
							  'moq' => $edit_moq[$j]
					  		  );

				$this->db->where('id', $approval_detail_id[$j])
						 ->update('approval_product_details', $datas);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Updated.</div>');
     	redirect(page_url.'Approval');

	}

	function csra_export() {
		$approval_id = $this->uri->segment(3);
		
		$this->load->library("Excel");
		$object = new PHPExcel();
		$object->createSheet(1);
		$object->setActiveSheetIndex(0);
		$table_columns = array("Approved CSRA","Freight Chrg.","Factor Value Numeric","B C","Cur Cod","Effective Date","Expired Date","2nd Item Number","UM","Address Number","Customer Name","Business Unit","PC 1","Product Name","Location Name","REMARKS");
		$column = 0;
		$object->getActiveSheet()->getStyle("A1:P1")->getFont()->setBold(true);
		$object->getActiveSheet()->getStyle('A1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('B1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('C1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('D1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('E1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('F1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('G1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('H1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('I1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('J1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('K1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);

		$object->getActiveSheet()->getStyle('L1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('M1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);

		$object->getActiveSheet()->getStyle('N1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => 'FFEA00')
												        )
												    )
												);

		$object->getActiveSheet()->getStyle('O1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => 'FFEA00')
												        )
												    )
												);
		$object->getActiveSheet()->getStyle('P1')->applyFromArray(
												    array(
												        'fill' => array(
												            'type' => PHPExcel_Style_Fill::FILL_SOLID,
												            'color' => array('rgb' => '00FFFF')
												        )
												    )
												);

		$object->getDefaultStyle()->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('K')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('L')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('M')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('N')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('O')->setWidth(40);
		$object->getActiveSheet()->getColumnDimension('P')->setWidth(40);

		$object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('10')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
		$object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);

		for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
			$object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
		}

		foreach ($table_columns as $field) {
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
			$column++;
		}

		  $sql = $this->db->select('a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name, b.address')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.id', $approval_id)
		 				  ->get();

		 if($sql->num_rows() > 0) {
		 	foreach($sql->result() as $row) {
		 		$excel_row = 2; //now from row 2
        		$i=1;

        		if($row->current_date !='' || $row->current_date != "0000-00-00"){
					$current_date = date('d-m-y', strtotime($row->current_date));
				}else{
					$current_date = '';
				}



				$query1=$this->db->select('hpcl_code')->from('store_rack_location')->where('id',3)->get();
				if($query1->num_rows()>0)
				{
					foreach($query1->result() as $query11);

					$hpcl_code=$query11->hpcl_code;

				}else
				{
					$hpcl_code='';
				}
				$query=$this->db->select('a.approved_price, a.price_validity, a.credit_vli, a.moq, b.instruments_name, b.model_number')
							    ->from('approval_product_details a')
							    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
							    ->where('a.approval_id',$approval_id)
							    ->get();

				if($query->num_rows() > 0) {
		 			foreach($query->result() as $rows) {
		        		$object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $rows->approved_price);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->transportation_rate);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->approved_price);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
				        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, 'INR');
				        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $current_date);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->price_validity);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $rows->model_number);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
				        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->address);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, 'SUNDER INDUSTRIAL OIL');
				        $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row,$hpcl_code);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row,'');
				        $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $rows->instruments_name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $row->name);
				        $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, 'AS PER HQQ APPROVAL');
				        $excel_row++;
				        $i++;

			        }
			    }
		 	}
		 }

		 $fileName = 'CSRA SHEET-'.$current_date.'.xls'; 
		 $savepath=$_SERVER['DOCUMENT_ROOT'].'/csra_sheet/'.$fileName;
         $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
         $object_writer->save($savepath);
         header("Content-type:application/vnd.ms-excel");
         header('Content-Disposition: attachment; filename=' . $fileName);
         readfile( $savepath );
	}

	function chk_item_picked_up() {
		$approval_id = $this->uri->segment(3);

		$data = array(
					  'purchase_entry' => 1,
					  'purchase_entry_on' => date('Y-m-d')
					  );

		$this->db->where('id', $approval_id)
				 ->update('approval_form', $data);

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Updated.</div>');
     	redirect(page_url.'Approval');
	}


	function payment_due_today() {
		$this->load->view('approval/payment_due_today');
	}

	function payment_due_today_list_old() {
		$current_date = date('Y-m-d');
		$data = array();
		$i=1;
		$this->db->select('d.name as vname,c.party,c.bill_no,a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'ALL')
						  {
						  	$this->db->where('c.party',$this->uri->segment(3));
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					

					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date == $due_date) {

						if($row->payment_terms==1)
						{
							$pay="Advance";
							$days='';
						}else
						{
							$pay="Credit Period";
							$days=$row->credit_period." days";
						}
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>$purchase_entry_on,
							'hpcl_location'=>$row->name,
							'vendor'=>$row->vname,
							'bill_no'=>$row->bill_no,
							'product_details'=>$prd_detail[0],
							'payment_terms'=>$pay."<br/>".$days,
							'csra_export' =>"<strong style='color:red;font-weight:bold;font-size:18px;'>₹".$prd_detail[1]."</strong>",
							'credit_period'=>"<strong style='font-weight:bold;'>".date('d-M-Y',strtotime($due_date))."</strong>"
						
							
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

	function upcoming_payments() {
		$this->load->view('approval/upcoming_payments');

	}

	function upcoming_payments_list() {
		$current_date = date('Y-m-d');
		$data = array();
		$i=1;
		$query = $this->db->select('a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.purchase_entry', 1)
		 				  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails($row->id);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->payment_terms.' days'));

					if($due_date > $current_date ) {

					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>date('d-m-Y', strtotime($row->current_date)),
							'hpcl_location'=>$row->name,
							'product_details'=>$getApprovalProductDetails,
							'payment_terms'=>$row->payment_terms,
							'credit_period'=>$row->credit_period,
							'transportation'=>$transportation.'<br>'.$transportation_rate,
							'csra_export' => $csra_export
							
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

	function late_payments() {
		$this->load->view('approval/late_payments');
	}

	function late_payments_list() {
		$current_date = date('Y-m-d');
		$data = array();
		$i=1;
		$query = $this->db->select('a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left')
						  ->where('a.purchase_entry', 1)
		 				  ->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails($row->id);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->payment_terms.' days'));

					if($due_date < $current_date ) {

					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>date('d-m-Y', strtotime($row->current_date)),
							'hpcl_location'=>$row->name,
							'product_details'=>$getApprovalProductDetails,
							'payment_terms'=>$row->payment_terms,
							'credit_period'=>$row->credit_period,
							'transportation'=>$transportation.'<br>'.$transportation_rate,
							'csra_export' => $csra_export
							
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

	function purchase_inventory() {
		$this->load->view('approval/purchase_inventory');
	}

	function edit_inventory_entry() {
		$this->load->view('approval/edit_inventory_entry');
	}

	function this_month_claim() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['party'] = '';
		$this->load->view('approval/this_month_claim', $data);
	}

	function this_month_claim_list_backup() {
		$current_date = date('Y-m-d');
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$vendor = $this->uri->segment(5);
		$location = $this->uri->segment(6);
		$data = array();
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

					$sql = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli, b.moq, c.instruments_name, d.shortname')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->join('presto_instruments c', 'c.id=b.product_id')
									->join('units d', 'd.id=a.pack_size')
									->where('a.entry_id', $row->id)
									->get();

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum = $rows->qty * $rows->credit_vli;
						
						$data[] = array(
								'sr_no' => $i,
								'ref_code' => 'TYPE-1'.$row->auto_gen_code,
								'hpcl_location' => $row->name,
								'bill_no' => $row->bill_no,
								'party' => $row->party,
								'product_name' => $rows->instruments_name,
								'qty' => $rows->qty." ".$rows->shortname,
								'approved_price' => $rows->approved_price." ".$rows->shortname,
								'price_validity' => $rows->price_validity,
								'credit_vli' => $rows->credit_vli."/".$rows->shortname,
								'moq' => $rows->moq." ".$rows->shortname,
								'credit_note_sum' => '<strong style="color:red;font-weight:bold;font-size:18px;">₹'.$credit_note_sum.'</strong>'
								
							);

						$i++;
						}
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

	 function filter_this_month_claim() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/this_month_claim/'.$from_date.'/'.$to_date.'/'.$hpcl_locations);
	}

	function filter_approval_list() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');

	 	$data['start_date'] = $from_date;
		$data['end_date'] = $to_date;
		$data['hpcl_locations'] = $hpcl_locations;
		$data['products'] = $products;

		$this->load->view('approval/approval_list', $data);
	}

	public function type_two_approval() {
		$this->load->view('approval/type_two_approval');
	}


	public function type_two_approval_new() {
		$this->load->view('approval/type_two_approval_new');
	}

	function save_type_two_approval() {
		$getLastInsertedTypeTwoCode = $this->salescrm->getLastInsertedTypeTwoCode();

		$data = array(
					  'auto_gen_code' => $getLastInsertedTypeTwoCode,
					  'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'hpcl_location' => $this->input->post('hpcl_location'),
					  'transportation' => $this->input->post('transportation'),
					  'transportation_rate' => $this->input->post('rate'),
					  'addedOn'=>date('Y-m-d H:i:s'),
					  'addedBy'=>$_SESSION['logged_in']['user_id']
					 );

		$this->db->insert('type_two_approval', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$approved_price = $this->input->post('approved_price');
		$price_validity = $this->input->post('price_validity');
		$credit_vli = $this->input->post('credit_vli');
		$pack_size = $this->input->post('pack_size');
		$moq = $this->input->post('moq');

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {
				$datas = array(
							  'approval_id' => $last_id,
							  'product_id' => $product[$i],
							  'pack_size'=>$pack_size[$i],
							  'approved_price' => $approved_price[$i],
							  'price_validity' => date('Y-m-d', strtotime($price_validity[$i])),
							  'credit_vli' => $credit_vli[$i],
							  'moq' => $moq[$i]
					  		  );

				$this->db->insert('type_two_product_details', $datas);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/type_two_approval');

	}

	public function type_two_approval_list() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_two_approval_list', $data);
	}

	function type_two_approval_listing() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);
		$customer = $this->uri->segment(8);

		         $this->db->select('a.invoice_date,a.invoice,a.annexture_name,a.annexture,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name')
						  ->from('approval_form_type_two a')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($customer<>'' && $customer<>'ALL')
			{
				$this->db->where('b.id',$customer);
			}

		

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				


				

					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_type_two($row->id,$hpcl_location,$product,$transport_type);
 					if($getApprovalProductDetails!="NA") {
						 $edit = "<a href='".page_url."Approval/edit_approval_type_two/".$row->id."/".$start_date."/".$end_date."/".$hpcl_location."/".$product."/".$transport_type."' class='btn btn-xs btn-default'>Edit Approval</a>";
						 $annexture_file=page_url1.'type_two_annexure/'.$row->annexture; 

						 if($row->invoice_date<>'' && $row->invoice_date<>'0000-00-00'){ 

						 	$in_date=date('d-M-Y',strtotime(($row->invoice_date)));

						}else
						 {

						 	$in_date='';

						 }
					$data[] = array(
							'sr_no' => $i,
							'current_date' => $row->type."<br/>".date('d-m-Y', strtotime($row->current_date)),
							'customer_name' =>$row->customer_name,
							'invoice'=>$row->invoice,
							'invoice_date'=>$in_date,
							'annexture'=>'<a href="'.$annexture_file.'">'.$row->annexture_name.'</a>',
							'hpcl_location' => '',
							'product_details' =>$getApprovalProductDetails,
							
							'item_delivered' => '',
							'edit' => $edit
						
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

	function filter_type_two_approvaloLDD() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_two_approval_list/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}

	function item_delivered() {
		$this->load->view('approval/item_delivered');
	}

	function update_item_delivered() {
		$uri = $this->uri->segment(3);
		$approval_detail_id = $this->input->post('approval_detail_id');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');

		for ($i=0; $i < count($approval_detail_id); $i++) { 
				if($approval_detail_id[$i] != '') {
					$data = array(
								'approval_detail_id' => $approval_detail_id[$i],
								'qty' => $qty,
								'pack_size' => $pack_size
								);

					$this->db->insert('type_two_item_delivered', $data);

				}
		}

		if($this->input->post('chkIfTransIncluded') == 1) {
			$datas = array(
						  'name' => $this->input->post('name'),
						  'mobile_no' => $this->input->post('mobile_no'),
						  'address' => $this->input->post('address'),
						  'vehicle_no' => $this->input->post('vehicle_no'),
						  'vehicle_type' => $this->input->post('vehicle_type'),
						  'transport_rate' => $this->input->post('transport_rate')
						  );

			$this->db->insert('type_two_item_delivered', $datas);
		}
	}

	function this_month_purchases() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/this_month_purchases', $data);
	}


	function this_month_purchases_list() {
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$location=$this->uri->segment(5);
		$party=$this->uri->segment(6);
		$product=$this->uri->segment(7);



		$data = array();
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

					$sql = $this->db->select('a.qty, b.approved_price, b.moq,b.price_validity, b.credit_vli, b.moq, c.instruments_name, d.shortname')
									->from('purchase_entry_details a')
									->join('approval_product_details b', 'b.id=a.approval_detail_id')
									->join('presto_instruments c', 'c.id=b.product_id')
									->join('units d', 'd.id=a.pack_size')
									->where('a.entry_id', $row->id);
									if($product<>'' && $product<>'ALL')
									{
										$this->db->where('b.product_id',$product);
									}
								$sql =	$this->db->get();
									

					if($sql->num_rows() > 0) {
						foreach ($sql->result() as $rows) {

							$credit_note_sum = $rows->qty * $rows->approved_price;
						
						$data[] = array(
								'sr_no' => $i,
								'ref_code' => 'TYPE-1'.$row->auto_gen_code,
								'hpcl_location' => $row->name,
								'bill_no' => $row->bill_no,
								'party' => $row->party,
								'product_name' => $rows->instruments_name,
								'qty' => $rows->qty.' '.$rows->shortname,
								'approved_price' => $rows->approved_price,
								'price_validity' => $rows->price_validity,
								'credit_vli' => $rows->credit_vli."/".$rows->shortname,
								'moq' => $rows->moq." ".$rows->shortname,
								'credit_note_sum' => '<strong style="color:red;font-weight:bold;font-size:21px;">₹'.$credit_note_sum.'</strong>'
								
							);

						$i++;
						}
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

	function getTransporterDetails() {
		$res = '';
		$transporter_id = $this->input->post('transporter_id');

		$sql = $this->db->select('mobile_no, address')
						->from('transporter_details')
						->where('id', $transporter_id)
						->get();

		if($sql->num_rows() > 0) {
			foreach ($sql->result() as $row);
			$res = $row->mobile_no.'|'.$row->address;
		}

		echo $res;
	}

	function save_item_delivered() {



		$delivery_date = $this->input->post('delivery_date');
		$transporter = $this->input->post('transporter_name');
		$mobile_no = $this->input->post('mobile_no');
		$address = $this->input->post('address');
		$vehicle_no = $this->input->post('vehicle_no');
		$vehicle_type = $this->input->post('vehicle_type');
		$transport_rate = $this->input->post('transport_rate');
		$approval_detail_id = $this->input->post('approval_detail_id');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');
		$customer_name = $this->input->post('customer_name');
		$customer_mobile = $this->input->post('customer_mobile');
		$customer_address = $this->input->post('customer_address');
		$transporter_id = $transporter;

		$sql = $this->db->select('id')
   						->from('transporter_details')
   						->where('id', $transporter)
   						->get();

   			if($sql->num_rows() == 0) {
   				$datas = array(
   								'name' => $transporter,
   								'mobile_no' => $mobile_no,
   								'address' => $address
   								);

   				$this->db->insert('transporter_details', $datas);
   				$transporter_id = $this->db->insert_id();
   			}

				$data = array(
							  'approval_id' => $this->uri->segment(3),
							  'transporter_id' => $transporter_id,
							  'vehicle_no' => $vehicle_no,
							  'vehicle_type' => $vehicle_type,
							  'transport_rate' => $transport_rate,
							  'customer_name'=>$customer_name,
							  'customer_mobile'=>$customer_mobile,
							  'customer_address'=>$customer_address,
							  'addedOn'=>date('Y-m-d',strtotime($delivery_date)),
							  'addedBy'=>$_SESSION['logged_in']['user_id']
							 );

				$this->db->insert('item_delivery', $data);
				$last_id = $this->db->insert_id();

				for($i=0; $i < count($approval_detail_id); $i++) {
					if($approval_detail_id[$i] != '') {
						$data1 = array(
									  'delivery_id' => $last_id,
									  'approval_detail_id' => $approval_detail_id[$i],
									  'qty' => $qty[$i],
									  'pack_size' => $pack_size[$i]
									  );

						$this->db->insert('item_delivery_details', $data1);
					}
				}
		

				$deldata=array('item_delivered'=>1);
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('type_two_approval',$deldata);


				$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Added</div>');
			redirect(page_url.'Approval/type_two_approval_list/2022-09-01/2022-09-30/ALL/ALL/ALL');
		}

		function purchase_details()
		{
			$this->load->view('approval/purchase_inventory_format');
		}


		function filter_purchase_list()
		{
			$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
			$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
			$hpcl_locations=$this->input->post('hpcl_locations');
			$vendors=$this->input->post('vendors');
			$products=$this->input->post('products');

			

			redirect(page_url.'Approval/this_month_purchases/'.$from_date."/".$to_date."/".$hpcl_locations."/".$vendors."/".$products);
		}

		function filter_payment_due_today()
		{
			$vendor=$this->input->post('vendor');
		
			redirect(page_url.'Approval/payment_due_today/'.$vendor);
		}


		function payment_due_upcoming_list() {
		$current_date = date('Y-m-d');
		$data = array();
		$i=1;
		$this->db->select('d.name as vname,c.party,c.bill_no,a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'ALL')
						  {
						  	$this->db->where('c.party',$this->uri->segment(3));
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					

					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date< $due_date) {

						if($row->payment_terms==1)
						{
							$pay="Advance";
							$days='';
						}else
						{
							$pay="Credit Period";
							$days=$row->credit_period." days";
						}
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>$purchase_entry_on,
							'hpcl_location'=>$row->name,
							'vendor'=>$row->vname,
							'bill_no'=>$row->bill_no,
							'product_details'=>$prd_detail[0],
							'payment_terms'=>$pay."<br/>".$days,
							'csra_export' =>"<strong style='color:red;font-weight:bold;font-size:18px;'>₹".$prd_detail[1]."</strong>",
							'credit_period'=>"<strong style='font-weight:bold;'>".date('d-M-Y',strtotime($due_date))."</strong>"
						
							
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

	function filter_payment_due_upcoming()
		{
			$vendor=$this->input->post('vendor');
			redirect(page_url.'Approval/upcoming_payments/'.$vendor);
		}


		function payment_due_late_listOld() {
		$current_date = date('Y-m-d');
		$data = array();
		$i=1;
		$this->db->select('d.name as vname,c.party,c.bill_no,a.id, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, a.purchase_entry_on, b.name')
						  ->from('approval_form a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('purchase_entry c','a.id=c.approval_id')
						  ->join('vendors d','c.party=d.id')
						  ->where('a.purchase_entry', 1)
						  ->where('a.payment_terms',2);
						  if($this->uri->segment(3)<>'' && $this->uri->segment(3)<>'ALL')
						  {
						  	$this->db->where('c.party',$this->uri->segment(3));
						  }
						 
		 				 $query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {

					

					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_late_payemnt($row->id);
					$prd_detail=explode('|',$getApprovalProductDetails);
					$purchase_entry_on = date('d-m-Y', strtotime($row->purchase_entry_on));
					$due_date = date('Y-m-d', strtotime($purchase_entry_on.' + '.$row->credit_period.' days'));
				
					if($current_date < $due_date) {

						if($row->payment_terms==1)
						{
							$pay="Advance";
							$days='';
						}else
						{
							$pay="Credit Period";
							$days=$row->credit_period." days";
						}

						$interest=($prd_detail[1]*13.75)/100;
						$total=$prd_detail[1]+$interest;
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>$purchase_entry_on,
							'hpcl_location'=>$row->name,
							'vendor'=>$row->vname,
							'bill_no'=>$row->bill_no,
							'product_details'=>$prd_detail[0],
							'payment_terms'=>$pay."<br/>".$days,
							'csra_export' =>"<strong style='color:orange;font-weight:bold;font-size:18px;'>₹".$prd_detail[1]."</strong>",
							'interest'=>"<strong style='color:red;font-weight:bold;font-size:18px;'>".$interest."</strong>",
							'total_amount'=>"<strong style='color:green;font-weight:bold;font-size:18px;'>₹".$total."</strong>",
							'credit_period'=>"<strong style='font-weight:bold;'>".date('d-M-Y',strtotime($due_date))."</strong>"
						
							
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


	function filter_payment_due_late()
		{
			$vendor=$this->input->post('vendor');
			$start_date=date('Y-m-d',strtotime($this->input->post('start_date')));
			$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
			redirect(page_url.'Approval/late_payments/'.$vendor.'/'.$start_date.'/'.$end_date);
		}


		function type_2_transportation_claim()
		{
			$this->load->view('approval/type_2_transport_claim');

		}


		function this_month__transport_claim_list_t2() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeTwoTransportationDetails($row->id,$transportation_rate);

					$pdetails=explode('|',$getApprovalProductDetails);

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					$getTransporterDetails = $this->salescrm->TransporterDetails($row->id);
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$getTransporterDetails,
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name
							
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


	 function filter_this_month_transport_claim_type2() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/type_2_transportation_claim/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}



function this_month_claim_type2() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['party'] = '';
		//$this->load->view('approval/this_month_claim_type_2oLDD', $data);
		$this->load->view('approval/this_month_claim_type_2', $data);
	}


	function this_month__cr_note_claim_list_t2Olddd() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,a.addedOn,d.first_name,d.last_name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=a.addedBy','left')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeTwoCredit_VLI_Details($row->id,$product);

					$pdetails=explode('|',$getApprovalProductDetails);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}
					$getTransporterDetails = $this->salescrm->TransporterDetails($row->id);
					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$getTransporterDetails,
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name
							
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


	 function filter_this_month_claim_type2() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');

	 	redirect(page_url.'Approval/this_month_claim_type2/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products);
	}

	function pending_transporters_payment()
	{
		$this->load->view('approval/type_2_transporter_pending_payment');
	}


	function pending_transporter_payment_list_t2Oldd() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',0);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeTwoTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);

					

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					
					
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
						$payment="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.",".$pdetails[1].");'>Mark as Paid</a>";
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$tdetail[0],
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name,
							'action'=>$payment
							
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


	 function filter_transporter_pending_payment() {

	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/pending_transporters_payment/'.$party.'/'.$hpcl_locations);
	}

	function gettransporter_detail()
	{
		$approvalid=$this->input->post('approvalid');

		$reste=$this->db->select('a.transporter_id,b.name,b.mobile_no')->from('item_delivery a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$approvalid)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row);
			$transporter_name=$row->name;
			$mobile=$row->mobile_no;
			$transporter_id=$row->transporter_id;

		}else
		{
		$transporter_name='';
		$mobile='';
		$transporter_id='';

		}

		echo $transporter_name."|".$mobile."|".$transporter_id;
	}


	function update_type2_transported_payment()
	{

		$transporter_id=$this->input->post('transporter_id');
		$approval_id=$this->input->post('approval_id');
		$payment_mode=$this->input->post('payment_mode');
		$name=$_FILES["image"]["name"];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'transporter_payment/'.basename($newname);
		move_uploaded_file($_FILES['image']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}


		$data=array('transporter_payment'=>1,'payment_mode'=>$payment_mode,'payment_image'=>$newname,'paidBy'=>$_SESSION['logged_in']['user_id'],'paidOn'=>date('Y-m-d H:i:s'));
		$this->db->where('approval_id',$approval_id);
		$this->db->where('transporter_id',$transporter_id);
		$this->db->update('item_delivery',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">Record Successfully Added.</div>');
		redirect(page_url."Approval/pending_transporters_payment/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6));
	}


	function pending_transporters_payment_history()
	{
		$this->load->view('approval/type_2_transporter_pending_payment_history');
	}


	function pending_transporter_payment_list_t2_historyOlddd() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('c.payment_mode,c.payment_image,c.paidBy,c.paidOn,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name,e.first_name as paidfname,e.last_name as paidlname')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy','left')
						  ->join('system_users e','e.user_id=c.paidBy','left')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',1);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeTwoTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);

					

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					
					
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
						$paid_details="Mode:".$row->payment_mode."<br/><br/><a href='".page_url1."transporter_payment/".$row->payment_image."' download>Evidence</a><br/><br/>Paid On: ".date('d-M-Y',strtotime($row->paidOn))."<br/><br/>".$row->paidfname." ".$row->paidlname;
						$payment="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.",".$pdetails[1].");'>Mark as Paid</a>";
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$tdetail[0],
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name,
							'action'=>$paid_details
							
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


	 function filter_transporter_pending_payment_history() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/pending_transporters_payment_history/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}

	function type_three_approval() {
		$this->load->view('approval/type_three_approval');
	}

	function save_type_three_approval() {
		$getLastInsertedTypeThreeCode = $this->salescrm->getLastInsertedTypeThreeCode();

		$data = array(
					  'auto_gen_code' => $getLastInsertedTypeThreeCode,
					  'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'hpcl_location' => $this->input->post('hpcl_location'),
					  'transportation' => $this->input->post('transportation'),
					 
					  'transportation_rate' => $this->input->post('rate'),
					  'addedOn'=>date('Y-m-d H:i:s'),
					  'addedBy'=>$_SESSION['logged_in']['user_id']
					 );

		$this->db->insert('type_three_approval', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$approved_price = $this->input->post('approved_price');
		$price_validity = $this->input->post('price_validity');
		$credit_vli = $this->input->post('credit_vli');
		$pack_size = $this->input->post('pack_size');
		$moq = $this->input->post('moq');

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {
				$datas = array(
							  'approval_id' => $last_id,
							  'product_id' => $product[$i],
							  'pack_size'=>$pack_size[$i],
							  'approved_price' => $approved_price[$i],
							  'price_validity' => date('Y-m-d', strtotime($price_validity[$i])),
							  'credit_vli' => $credit_vli[$i],
							  'moq' => $moq[$i]
					  		  );

				$this->db->insert('type_three_product_details', $datas);
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/type_three_approval');

	}

	function type_three_approval_list()
	{
		$this->load->view('approval/type_three_approval_list');
	}


	function type_three_approval_listing() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate,a.transport_owned_hired, b.name,a.item_delivered')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transport_type<>'ALL' && $transport_type<>'')
			{
				$this->db->where('a.transportation',$transport_type);
			}

			$this->db->order_by('a.id','DESC');
		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
				if($row->item_delivered==0)
				{
					$item_delivered = "<a href='".page_url."Approval/item_delivered_type3/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";
				}else
				{
					$item_delivered = "<strong style='color:red;font-weight:bold;'>Item Delivered to customer</strong>";
				}

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
						$transport_type='';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
						if($row->transport_owned_hired==1)
						{
							$transport_type="Owned";
						}else
						{
							$transport_type="Hired";
						}
					} else{
						$transportation = '';
						$transportation_rate = '';
						$transport_type='';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeThreeApprovalProductDetails($row->id);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeThreeProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}

					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-3'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details' => $getApprovalProductDetails,
							// 'payment_terms'=>$payment_terms,
							'credit_period' => $row->credit_period,
							'transportation' =>"<strong>".$transportation."</strong>",
							'transportation_rate' => $transportation_rate."/"."LTR",
							'item_delivered' => $item_delivered
							// 'edit'=>$edit,
							// 'purchase_entry'=>$purchase_inventory,
							// 'csra_export' => $csra_export
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

	function filter_type_three_approval() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_three_approval_list/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}


function item_delivered_type3() {
		$this->load->view('approval/item_delivered_type_3');
	}


	function save_item_delivered_type_3() {


		$delivery_date = $this->input->post('delivery_date');
		$transport_owned_hired = $this->input->post('transport_owned_hired');
		
		$approval_detail_id = $this->input->post('approval_detail_id');
		$qty = $this->input->post('qty');
		$pack_size = $this->input->post('pack_size');
		$customer_name = $this->input->post('customer_name');
		$customer_mobile = $this->input->post('customer_mobile');
		$customer_address = $this->input->post('customer_address');

		if($transport_owned_hired==2)
		{
		
			$transporter = $this->input->post('transporter_name');
			$mobile_no = $this->input->post('mobile_no');
			$address = $this->input->post('address');
			$vehicle_no = $this->input->post('vehicle_no');
			$vehicle_type = $this->input->post('vehicle_type');
			$transport_rate = $this->input->post('transport_rate');

			$sql = $this->db->select('id')
   						->from('transporter_details')
   						->where('id', $transporter)
   						->get();

   			if($sql->num_rows() == 0) {
   				$datas = array(
   								'name' => $transporter,
   								'mobile_no' => $mobile_no,
   								'address' => $address
   								);

   				$this->db->insert('transporter_details', $datas);
   				$transporter_id = $this->db->insert_id();
   				}else
   				{
   					$transporter_id=$transporter;
   				}

	   		

   		}else
   		{
   			$transporter_id=0;
	   		$transporter ='';
			$mobile_no ='';
			$address ='';
			$vehicle_no ='';
			$vehicle_type ='';
			$transport_rate = '';
   		}

				$data = array(
							  'approval_id' => $this->uri->segment(3),
							  'transporter_id' => $transporter_id,
							  'vehicle_no' => $vehicle_no,
							  'vehicle_type' => $vehicle_type,
							  'transport_rate' => $transport_rate,
							  'customer_name'=>$customer_name,
							  'customer_mobile'=>$customer_mobile,
							  'customer_address'=>$customer_address,
							  'addedOn'=>date('Y-m-d',strtotime($delivery_date)),
							  'addedBy'=>$_SESSION['logged_in']['user_id']
							 );

				$this->db->insert('item_delivery_type_3', $data);
				$last_id = $this->db->insert_id();

				for($i=0; $i < count($approval_detail_id); $i++) {
					if($approval_detail_id[$i] != '') {
						$data1 = array(
									  'delivery_id' => $last_id,
									  'approval_detail_id' => $approval_detail_id[$i],
									  'qty' => $qty[$i],
									  'pack_size' => $pack_size[$i]
									  );

						$this->db->insert('item_delivery_details_type_3', $data1);
					}
				}
		

				$deldata=array('item_delivered'=>1,'transport_owned_hired'=>$transport_owned_hired);
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('type_three_approval',$deldata);


				$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Added</div>');
			redirect(page_url.'Approval/type_three_approval_list/2022-09-01/2022-09-30/ALL/ALL/ALL');
		}


		function type_3_transportation_claim()
		{
			$this->load->view('approval/type_3_transport_claim');

		}


		function this_month__transport_claim_list_t3() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('a.transport_owned_hired',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails($row->id,$transportation_rate);

					$pdetails=explode('|',$getApprovalProductDetails);

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					$getTransporterDetails = $this->salescrm->TransporterDetails_type3($row->id);
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$getTransporterDetails,
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name
							
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


	 function filter_this_month_transport_claim_type3() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/type_3_transportation_claim/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}


	function this_month_claim_type3() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['party'] = '';
		$this->load->view('approval/this_month_claim_type_3', $data);
	}


	function this_month__cr_note_claim_list_t3() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		$product = $this->uri->segment(7);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,a.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=a.addedBy','left')
						  ->where('a.item_delivered',1);
						 

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeThreeCredit_VLI_Details($row->id,$product);

					$pdetails=explode('|',$getApprovalProductDetails);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeThreeProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}
					$getTransporterDetails = $this->salescrm->TransporterDetails_type3($row->id);
					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$getTransporterDetails,
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name
							
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


function filter_this_month_claim_type3() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');

	 	redirect(page_url.'Approval/this_month_claim_type3/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations.'/'.$products);
	}

	function pending_transporters_payment_type_3()
	{
		$this->load->view('approval/type_3_transporter_pending_payment');
	}


	function pending_transporter_payment_list_t3() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('a.transport_owned_hired',2)
						  ->where('c.transporter_payment',0);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate_type_3($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);

					

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					
					
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
						$payment="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.",".$pdetails[1].");'>Mark as Paid</a>";
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$tdetail[0],
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name,
							'action'=>$payment
							
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


	 function filter_transporter_pending_payment_type_3() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/pending_transporters_payment_type_3/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}


	function pending_transporters_payment_history_type_3()
	{
		$this->load->view('approval/type_3_transporter_pending_payment_history');
	}





	function gettransporter_detail_type_3()
	{
		$approvalid=$this->input->post('approvalid');

		$reste=$this->db->select('a.transporter_id,b.name,b.mobile_no')->from('item_delivery_type_3 a')->join('transporter_details b','a.transporter_id=b.id')->where('a.approval_id',$approvalid)->get();
		if($reste->num_rows()>0)
		{
			foreach($reste->result() as $row);
			$transporter_name=$row->name;
			$mobile=$row->mobile_no;
			$transporter_id=$row->transporter_id;

		}else
		{
		$transporter_name='';
		$mobile='';
		$transporter_id='';

		}

		echo $transporter_name."|".$mobile."|".$transporter_id;
	}



	function update_type3_transported_payment()
	{

		$transporter_id=$this->input->post('transporter_id');
		$approval_id=$this->input->post('approval_id');
		$payment_mode=$this->input->post('payment_mode');
		$name=$_FILES["image"]["name"];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'transporter_payment/'.basename($newname);
		move_uploaded_file($_FILES['image']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}


		$data=array('transporter_payment'=>1,'payment_mode'=>$payment_mode,'payment_image'=>$newname,'paidBy'=>$_SESSION['logged_in']['user_id'],'paidOn'=>date('Y-m-d H:i:s'));
		$this->db->where('approval_id',$approval_id);
		$this->db->where('transporter_id',$transporter_id);
		$this->db->update('item_delivery_type_3',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success">Record Successfully Added.</div>');
		redirect(page_url."Approval/pending_transporters_payment_type_3/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/".$this->uri->segment(6));
	}


	function pending_transporter_payment_list_t3_history() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('c.payment_mode,c.payment_image,c.paidBy,c.paidOn,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name,e.first_name as paidfname,e.last_name as paidlname')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy','left')
						  ->join('system_users e','e.user_id=c.paidBy','left')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('c.transporter_payment',1)
						  ->where('a.transport_owned_hired',2);

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate_type_3($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails_without_amt($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);

					

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					
					
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
						$paid_details="Mode:".$row->payment_mode."<br/><br/><a href='".page_url1."transporter_payment/".$row->payment_image."' download>Evidence</a><br/><br/>Paid On: ".date('d-M-Y',strtotime($row->paidOn))."<br/><br/>".$row->paidfname." ".$row->paidlname;
						$payment="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.",".$pdetails[1].");'>Mark as Paid</a>";
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>$tdetail[0],
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name,
							'action'=>$paid_details
							
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



	 function filter_transporter_pending_payment_history_type_3() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/pending_transporters_payment_history_type_3/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}


	public function type_two_approval_list_pending_delivery() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_two_approval_list_pending_delivery', $data);
	}


	function type_two_approval_listing_pending_deliveryoLDD() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,a.item_delivered')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transport_type<>'ALL' && $transport_type<>'')
			{
				$this->db->where('a.transportation',$transport_type);
			}

		$this->db->where('a.item_delivered',0);
		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				if($row->item_delivered==0)
				{
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";
				}else
				{
					$item_delivered="<strong style='color:red;font-weight:bold'>Item Delivered to Customer.</strong>";
				}

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeTwoApprovalProductDetails($row->id);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}

					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details' => $getApprovalProductDetails,
							// 'payment_terms'=>$payment_terms,
							'credit_period' => $row->credit_period,
							'transportation' => $transportation,
							'transportation_rate' => $transportation_rate."/"."LTR",
							'item_delivered' => $item_delivered
							// 'edit'=>$edit,
							// 'purchase_entry'=>$purchase_inventory,
							// 'csra_export' => $csra_export
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


function filter_type_two_approval_pending_delivery() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_two_approval_list_pending_delivery/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}


	function item_delivery_history_type_2()
	{
		$this->load->view('approval/type_two_item_delivery_history');
	}



	function type_two_approval_delivery_listing_history() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,a.item_delivered')
						  ->from('type_two_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transport_type<>'ALL' && $transport_type<>'')
			{
				$this->db->where('a.transportation',$transport_type);
			}

			$this->db->where('a.item_delivered',1);
			$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				if($row->item_delivered==0)
				{
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";
				}else
				{
					$item_delivered="<strong style='color:red;font-weight:bold'>Item Delivered to Customer.</strong>";
				}

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
						$transportation_detail='';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
						$transportation_detail= $this->salescrm->TransporterDetails($row->id);


					} else{
						$transportation = '';
						$transportation_rate = '';
						$transportation_detail='';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeTwoApprovalProductDetails($row->id);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}

					
					if($chkIfProductExists > 0) {

						$getApprovalDeliveryDetails = $this->salescrm->getTypeTwoApprovalDeliveryDetails($row->id,$product);
						
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details' => $getApprovalDeliveryDetails,
							// 'payment_terms'=>$payment_terms,
							'credit_period' => $row->credit_period,
							'transportation' => $transportation,
							'transportation_rate' => $transportation_rate."/"."LTR",
							'transportation_detail'=>$transportation_detail
							// 'edit'=>$edit,
							// 'purchase_entry'=>$purchase_inventory,
							// 'csra_export' => $csra_export
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


	function filter_type_two_delivery_detail() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/item_delivery_history_type_2/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}


	public function type_three_approval_list_pending_delivery() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_three_approval_list_pending_delivery', $data);
	}


	function type_three_approval_listing_pending_delivery() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,a.item_delivered')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transport_type<>'ALL' && $transport_type<>'')
			{
				$this->db->where('a.transportation',$transport_type);
			}

		$this->db->where('a.item_delivered',0);
		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				if($row->item_delivered==0)
				{
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";
				}else
				{
					$item_delivered="<strong style='color:red;font-weight:bold'>Item Delivered to Customer.</strong>";
				}

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeThreeApprovalProductDetails($row->id);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeThreeProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}

					if($chkIfProductExists > 0) {
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details' => $getApprovalProductDetails,
							// 'payment_terms'=>$payment_terms,
							'credit_period' => $row->credit_period,
							'transportation' => $transportation,
							'transportation_rate' => $transportation_rate."/"."LTR",
							'item_delivered' => $item_delivered
							// 'edit'=>$edit,
							// 'purchase_entry'=>$purchase_inventory,
							// 'csra_export' => $csra_export
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


	function filter_type_three_approval_pending_delivery() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_three_approval_list_pending_delivery/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}



	function item_delivery_history_type_3()
	{
		$this->load->view('approval/type_three_item_delivery_history');
	}



	function type_three_approval_delivery_listing_history() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,a.item_delivered,a.transport_owned_hired')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location', 'left');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transport_type<>'ALL' && $transport_type<>'')
			{
				$this->db->where('a.transportation',$transport_type);
			}

			$this->db->where('a.item_delivered',1);
			$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				if($row->item_delivered==0)
				{
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";
				}else
				{
					$item_delivered="<strong style='color:red;font-weight:bold'>Item Delivered to Customer.</strong>";
				}

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
						$transportation_detail='';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
						if($row->transport_owned_hired==2)
						{
						$transportation_detail= $this->salescrm->TransporterDetails($row->id);
						}else
						{
							$transportation_detail="Owned Transport";
						}


					} else{
						$transportation = '';
						$transportation_rate = '';
						$transportation_detail='';
					}

				

					$getApprovalProductDetails = $this->salescrm->getTypeThreeApprovalProductDetails($row->id);

					if($product != '' && $product != 'ALL') {
						$chkIfProductExists = $this->salescrm->chkIfTypeThreeProductExists($row->id, $product);
					} else {
						$chkIfProductExists = 1;
					}

					
					if($chkIfProductExists > 0) {

						$getApprovalDeliveryDetails = $this->salescrm->getTypeThreeApprovalDeliveryDetails($row->id,$product);
						
						
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details' => $getApprovalDeliveryDetails,
							// 'payment_terms'=>$payment_terms,
							'credit_period' => $row->credit_period,
							'transportation' => $transportation,
							'transportation_rate' => $transportation_rate."/"."LTR",
							'transportation_detail'=>$transportation_detail
							// 'edit'=>$edit,
							// 'purchase_entry'=>$purchase_inventory,
							// 'csra_export' => $csra_export
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


	function filter_type_three_delivery_detail() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/item_delivery_history_type_3/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}

	function own_transport_report()
	{
		$this->load->view('approval/own_transport_report');
	}


	function own_transport_list_t3() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(6);
		$transporter = $this->uri->segment(5);
		
	

		         $this->db->select('a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period, a.transportation, a.transportation_rate, b.name,c.transporter_id,c.vehicle_no,c.vehicle_type,c.transport_rate,c.customer_name,c.customer_mobile,c.customer_address,c.addedOn,d.first_name,d.last_name')
						  ->from('type_three_approval a')
						  ->join('hpcl_location b', 'b.id=a.hpcl_location')
						  ->join('item_delivery_type_3 c','c.approval_id=a.id')
						  ->join('system_users d','d.user_id=c.addedBy')
						  ->where('a.item_delivered',1)
						  ->where('a.transportation',2)
						  ->where('a.transport_owned_hired',1);
					

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('c.addedOn >=', $start_date);
				 $this->db->where('c.addedOn <=', $end_date);
			}

			if($hpcl_location != '' && $hpcl_location != 'ALL') {
				 $this->db->where('a.hpcl_location', $hpcl_location);
			}

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('c.transporter_id', $transporter);
			}


		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				
					$item_delivered = "<a href='".page_url."Approval/item_delivered/".$row->id."' class='btn btn-success btn-xs'>Item Delivered</a>";

					if($row->transportation == 1) {
						$transportation = 'Included';
						$transportation_rate = '';
					} else if($row->transportation == 2) {
						$transportation = 'Not Included';
						$transportation_rate = $row->transportation_rate;
					} else{
						$transportation = '';
						$transportation_rate = '';
					}

				

					$getTransporterDetails = $this->salescrm->TransporterDetails_with_rate_type_3($row->id);
					$tdetail=explode('|',$getTransporterDetails);
					$getApprovalProductDetails = $this->salescrm->getTypeThreeTransportationDetails_without_amt_n_payment($row->id,$tdetail[1]);
					$pdetails=explode('|',$getApprovalProductDetails);

					

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfTypeTwoProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }
					
					
					$chkIfProductExists=1;
					if($chkIfProductExists > 0) {
						
						$payment="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.",".$pdetails[1].");'>Mark as Paid</a>";
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-2'.$row->auto_gen_code,
							'hpcl_location' => $row->name,
							'product_details'=>$pdetails[0],
					
							'customer_detail'=>$row->customer_name."<br/><br/>".$row->customer_mobile."<br/>".$row->customer_address,							
							'transportation_rate' =>$transportation_rate."/LTR",
							'transport_detail' =>"<strong style='font-weight:bold;'>".$transportation."<br/>OWNED SUNDAR VEHICLE</strong>",
							'total_claim'=>"<strong style='color:green;font-weight:bold;font-size:26px;'>₹".$pdetails[1]."</strong>",
							'approvalby'=>date('d-M-Y',strtotime($row->addedOn))."<br/><br/>".$row->first_name." ".$row->last_name,
							'action'=>$payment
							
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


	 function filter_own_transport_report() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/own_transport_report/'.$from_date.'/'.$to_date.'/'.$party.'/'.$hpcl_locations);
	}


	function getproducts_byid()
	{
		$html='<option value="">-Select--</option>';
		$prd=$this->input->post('prd');
		$d=explode(',',$prd);
		$result = "'" . implode ( "', '", $d ) . "'";

		$rest=$this->db->select('id,instruments_name')->from('presto_instruments')->where_in('id',$result,false)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $row)
			{
				$html.='<option value="'.$row->id.'">'.$row->instruments_name.'</option>';
			}
		}


		echo $html;
	}

	function getProductUnit()
	{
		$html='';

		$prd=$this->input->post('prd');
		$restey=$this->db->select('a.unit,b.name,b.id,a.pack_size')->from('presto_instruments a')->join('units b','a.unit=b.shortname')->where('a.id',$prd)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $row);

			$html.="<option value='".$row->id."'>".$row->name."</option>";
			
			if($row->pack_size=='BULK')
			{
				$html.="<option value='4'>Kilogram</option>";
			}
		}

		echo $html;
	}


	// function this_month_claim_list() {
	// 	$current_date = date('Y-m-d');
	// 	$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
	// 	$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
	// 	$vendor = $this->uri->segment(5);
	// 	$location = $this->uri->segment(6);
	// 	$data = array();
	// 	$i=1;
	// 	         $this->db->select('a.id, a.bill_no, b.name as party, c.auto_gen_code, d.name')
	// 					  ->from('purchase_entry a')
	// 					  ->join('vendors b', 'b.id=a.party')
	// 					  ->join('approval_form c', 'c.id=a.approval_id')
	// 					  ->join('hpcl_location d', 'd.id=c.hpcl_location');

	// 		if($start_date <> '' && $end_date <> '') {
	// 			 $this->db->where('a.currentdate >=', $start_date);
	// 			 $this->db->where('a.currentdate <=', $end_date);
	// 		}

	// 		if($vendor <> '' && $vendor <> 'ALL') {
	// 			$this->db->where('a.party', $vendor);
	// 		}

	// 		if($location <> '' && $location <> 'ALL') {
	// 			$this->db->where('c.hpcl_location', $location);
	// 		}
	//    $query =  $this->db->get();

	// 	if($query->num_rows() > 0) {
	// 		foreach($query->result() as $row) {

	// 				$sql = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli, b.moq, c.instruments_name, d.shortname')
	// 								->from('purchase_entry_details a')
	// 								->join('approval_product_details b', 'b.id=a.approval_detail_id')
	// 								->join('presto_instruments c', 'c.id=b.product_id')
	// 								->join('units d', 'd.id=a.pack_size')
	// 								->where('a.entry_id', $row->id)
	// 								->get();

	// 				if($sql->num_rows() > 0) {
	// 					foreach ($sql->result() as $rows) {

	// 						$credit_note_sum = $rows->qty * $rows->credit_vli;
						
	// 					$data[] = array(
	// 							'sr_no' => $i,
	// 							'ref_code' => 'TYPE-1'.$row->auto_gen_code,
	// 							'hpcl_location' => $row->name,
	// 							'bill_no' => $row->bill_no,
	// 							'party' => $row->party,
	// 							'product_name' => $rows->instruments_name,
	// 							'qty' => $rows->qty." ".$rows->shortname,
	// 							'approved_price' => $rows->approved_price." ".$rows->shortname,
	// 							'price_validity' => $rows->price_validity,
	// 							'credit_vli' => $rows->credit_vli."/".$rows->shortname,
	// 							'moq' => $rows->moq." ".$rows->shortname,
	// 							'credit_note_sum' => '<strong style="color:red;font-weight:bold;font-size:18px;">₹'.$credit_note_sum.'</strong>'
								
	// 						);

	// 					$i++;
	// 					}
	// 				}
				
	// 			}
	// 		}
	// 		$results = array(
	// 		"sEcho" => 1,
	// 		"iTotalRecords" => count($data),
	// 		"iTotalDisplayRecords" => count($data),
	// 		"aaData"=>$data);
			
	// 	echo json_encode($results);
	// }


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

function this_month_claim_list_consolidated() {

$data=array();
$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);

	$reste=$this->db->select('e.hpcl_location,d.interest,d.payment_type,d.pur_payment,d.bill_no,e.name as vendor_name,d.gst as gstrate,d.currentdate,a.id, a.product,a.pack_size,a.qty,a.original_qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','a.inventory_id=d.id')->join('vendors e','d.party=e.id')->where('e.hpcl',1)->where('d.currentdate>=',$start_date)->where('d.currentdate<=',$end_date)->where('d.hpcl_billing_company',3)->where('a.payment',1)->get();
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

				$collection_details=$this->salescrm->getcollection_details($row->id,$due_date,$row->interest,$row->rate,$row->product,$row->currentdate,$row->hpcl_location);
				$collections=explode('|',$collection_details);


				$approvals_data=$this->salescrm->getApprovalForPurchase($row->product,$row->currentdate,$row->hpcl_location);
				$appdata=explode('|',$approvals_data);
				// if($row->bill_no=="DL0130000583")
				// {
				// 	echo "<pre>"; print_r($appdata); exit;
				// }
			//	echo "<pre>"; print_r($appdata); exit;
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

					$moq_fullfilled=$this->salescrm->checkMOQFullfilled($validity_from,$validity_to,$moq,$row->product,$row->hpcl_location);
					$moq_full=explode("|",$moq_fullfilled);
					if($moq_full[0]==1)
					{
					$claim=$row->qty*$credit;
					if($credit>0)
					{
					$final_claim=$claim-$collections[1];
					}else
					{
							$final_claim=0;
					}
					}else
					{
						$claim=0;
						$final_claim=0;
						$left=$moq-$moq_full[1];
						$errors="<strong style='color:red;font-weight:bold;'>".$left." LTR is short as per the MOQ to claim the Credit Note.</strong>";
					}

					}else
					{
					$claim=$row->qty*$credit;
					if($credit>0)
					{
					$final_claim=$claim-$collections[1];
					}else{
						$final_claim=0;
					}
					}

				}else
				{
					/** FOR COMBINATION **/
					
					// get combined partner 
					$combined_prd=$this->salescrm->getcombinedProductDetails($row->product,$app_id);
					$comb_moq=$combined_prd[0];
					$comb_vli=$combined_prd[1];
					$credit=$comb_vli;
					$moq_criteria=$this->salescrm->CheckCombinedMoqFullfilment($comb_moq,$comb_vli,$app_id,$row->product,$validity_from,$validity_to,$row->hpcl_location);
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
					}else{
						$final_claim=0;
					}	

				}



				
					$data[] = array(
							'sr_no'=>$i."<br/>".$row->id,
							'bill_no'=>$row->bill_no,
							'party'=>$row->vendor_name,
							'purchase_date'=>date('d-m-Y', strtotime($row->currentdate)),
							'product_details'=>$row->instruments_name,
							'qty'=>$row->qty." ".$row->shortname,
							'rate'=>$row->rate,
							'total_rate'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$total."</strong>",
							'gst_rate'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$total_gst."</strong>",
							'with_tax'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$grandtotal."</strong>",
							'due_date'=>$due_date,
							'payment_status'=>$pstatus,
							'collection'=>$collections[0],
							'credit'=>$credit."/LTR",
							'total_interest'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$collections[1]."</strong>",
							'total_claim'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$claim."</strong>",
							'final_claim'=>"<strong style='color:red;font-weight:bold;font-size:16px;'>₹".$final_claim."</strong>",
							'errors'=>$errors,
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

	function this_month_claim_list_consolidatedOldddd() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);

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

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				

				

					
					if($row->payment_terms == 1) {
						$payment_terms = 'ADVANCE';
						$days=$row->credit_period." Days";
					} else if($row->payment_terms == 2) {
						$payment_terms = 'CREDIT PERIOD';
						$days=$row->credit_period." Days";
					} else{
						$payment_terms = '';
						$days='';
					}

					if($row->combination==0)
						{
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_for_claim_for_excel($row->id,$start_date,$end_date);
					$cl_detail=explode('|',$getApprovalProductDetails);
						$prd_table=$cl_detail[0];
						$totalclaim=$cl_detail[1];
						}else
						{	
					$getApprovalProductDetails=$this->salescrm->getApprovalProductDetails_combination_for_claim($row->id,$start_date,$end_date);

						$cl_detail=explode('|',$getApprovalProductDetails);
						$prd_table=$cl_detail[0];
						$totalclaim=$cl_detail[1];
						}

					// if($product != '' && $product != 'ALL') {
					// 	$chkIfProductExists = $this->salescrm->chkIfProductExists($row->id, $product);
					// } else {
					// 	$chkIfProductExists = 1;
					// }


					if($hpcl_location != '' && $hpcl_location != 'ALL') {
						$chkIflocationExists = $this->salescrm->chkIfLocationExists($row->id, $hpcl_location);
					} else {
						$chkIflocationExists = 1;
					}



					if($chkIflocationExists>0) {

						if($row->combination==0)
						{
							$app_type="Per Product Approval";
						}else
						{
							$app_type="Combination Approval";
						}
						
					$data[] = array(
							'sr_no'=>$i,

							'approvalid'=>date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-1'.$row->auto_gen_code."<br/><strong style='color:red;'>".$app_type."</strong>",
							'product_details'=>$prd_table,
							'total_claim'=>"<strong style='font-weight:bold;color:red;font-size:20px;'>".$totalclaim."</strong>",
							// 'payment_terms'=>$payment_terms,
							// 'credit_period'=>$days,
							// 'edit'=>$edit,
							// 'purchase_entry'=>$pdetail,
							// 'csra_export' => $csra_export,
							// 'credit_note_claim_amount'=>''
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


	function payment_due_today_listOLdddddddd() {
		
		$data = array();
		$i=1;
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');

	
		$party=$this->uri->segment(3);
	
		$data = array();
		$i=1;
		         $this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate')
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

						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {



						$action="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.");'>Update Payment Status</a>";
						if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
							$data[] = array(
							'sr_no' => $i,
							'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
							'bill_no' =>$row->bill_no,
							'party'=>$row->party,
							'credit_days'=>$payment."-".$cr_days." Days",
							'product_detail'=>$pdetails[0],
							'total_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
							'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
							'action'=>	$action												

							);

						$i++;
					}
					//}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);



					

			
	}


	function payment_due_today_list() {
		
		$data = array();
		$i=1;
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');
		$party=$this->uri->segment(3);	
		$data = array();
		$i=1;
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

						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {

						$prev_payment=$this->check_for_previous_payment($row->id);
            $gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;
            $final_payable_amount=$grand_amt-$prev_payment;
						$ch = $row->id.",".$grand_amt;
						$action="<a href='".page_url."Approval/pending_payments_type_1/".$row->id."/".base64_encode($final_payable_amount)."/1' class='btn btn-warning'>Update Payment Status</a>";
							if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {

							$data[] = array(
							'sr_no' => $i,
							'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
							'bill_no' =>$row->bill_no,
							'party'=>$row->party,
							'credit_days'=>$payment."-".$cr_days." Days",
							'product_detail'=>$pdetails[0],
							'basic_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
							'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
              'gst'=> $gst,
              'grand_amount' =>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$grand_amt."</strong>",
              'previous_payment'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$prev_payment."</strong>",
              'final_payment'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$final_payable_amount."</strong>",
							'action'=>	$action												

							);

						$i++;
					}
					//}
						
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


function payment_due_list() {
		
		$data = array();
		$i=1;
	
		$party=$this->uri->segment(3);	
		$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));	
		$end_date=date('Y-m-d',strtotime($this->uri->segment(5)));	
		$data = array();
		$i=1;
		         $this->db->select('a.id,a.hpcl_billing_company,a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.gst')
		         		  ->from('inventory a')
						  ->join('vendors d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party); 
						}
						$this->db->where('a.currentdate>=',$start_date);
						$this->db->where('a.currentdate<=',$end_date);
						$this->db->where_in('a.payment_type','5,6',false);
						$this->db->where('a.pur_payment',0);
						$this->db->where('d.hpcl',1);
						$this->db->order_by('a.currentdate','ASC');


		   $query =  $this->db->get();

			if($query->num_rows() > 0) {
				$i=0;
			foreach($query->result() as $row) {

					$purchase_details=$this->get_purchase_details($row->id);
					  $companybilling=$this->salescrm->get_company_name($row->hpcl_billing_company);
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

						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {

						$prev_payment=$this->check_for_previous_payment($row->id);
            $gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;
            $final_payable_amount=$grand_amt-$prev_payment;

                $ch = $row->id.",".$grand_amt;

            if($i==0)
            {
					 $action="<a href='".page_url."Approval/pending_payments_type_1/".$row->id."/".base64_encode($final_payable_amount)."/1/".$party."/".$start_date."/".$end_date."' class='btn btn-warning'>Update Payment Status</a>";
					 }else
					 {
					 	$action='';
					 }
						// if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {

						$data[] = array(
						'sr_no' => $i."<br/>".$row->id,
						'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
						'billing_company'=>$companybilling,
						'bill_no' =>$row->bill_no,
						'party'=>$row->party,
						'credit_days'=>$payment."-".$cr_days." Days",
						'product_detail'=>$pdetails[0],
						'basic_amount'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
						'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
						'gst'=>$gst,
						'grand_amount' =>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$grand_amt."</strong>",
						'previous_payment'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$prev_payment."</strong>",
						'final_payment'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$final_payable_amount."</strong>",
						'action'=>$action												

							);

						$i++;
					//}
					//}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);

			
	}

	function payment_due_listOLdd() {
		
		$data = array();
		$i=1;
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');
		$party=$this->uri->segment(3);	
		$data = array();
		$i=1;
		         $this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate')
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

						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {



						$action="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.");'>Update Payment Status</a>";
						// if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
							$data[] = array(
							'sr_no' => $i,
							'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
							'bill_no' =>$row->bill_no,
							'party'=>$row->party,
							'credit_days'=>$payment."-".$cr_days." Days",
							'product_detail'=>$pdetails[0],
							'total_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
							'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
							'action'=>	$action												

							);

						$i++;
					//}
					//}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);

			
	}


	function filter_payment_due()
		{
			$vendor=$this->input->post('vendor');
			$start_date=date('Y-m-d',strtotime($this->input->post('start_date')));
			$end_date=date('Y-m-d',strtotime($this->input->post('end_date')));
			redirect(page_url.'Approval/upcoming_payments/'.$vendor.'/'.date('Y-m-d',strtotime($start_date)).'/'.date('Y-m-d',strtotime($end_date)));
		}



function payment_due_late_list() {
		//$gst_per=$this->salescrm->get_gst_slab();
		$data = array();
		$i=1;
		$party=$this->uri->segment(3);
		$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		$end_date=date('Y-m-d',strtotime($this->uri->segment(5)));
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
						

							$data[] = array(
							'sr_no' => $i,
							'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
							'bill_no' =>$row->bill_no,
							'party'=>$row->party,
							'credit_days'=>$payment."-".$cr_days." Days",
							'product_detail'=>$pdetails[0],
							'basic_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
							'total_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$total."</strong>",
							'due_date'=>"<strong style='font-size: 15px;color:green;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
							'interest_per'=>"<strong style='font-size: 18px;color:blue;font-weight: bold;'>".$row->interest." %</strong>",
							'payment_details'=>$pdata[0],
							'total_interest'=>"<strong style='font-size: 15px;color:green;font-weight: bold;'>".$pdata[1]."</strong>"
							// 'payment_details'=>"<strong  style='font-size: 15px;color:red;font-weight: bold;'>".$payment_details."</strong>",
							// 'extra_days'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".$days." Days</strong>",												
							// 'interest_rate'=>"<strong  style='font-size: 15px;color:blue;font-weight: bold;'>".$row->interest."%"."</strong>",												
							// 'interest_charges'=>"<strong  style='font-size: 22px;color:red;font-weight: bold;'>₹".$calculate_int."</strong>"											

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


		function payment_due_late_listOLdddd() {
		//$gst_per=$this->salescrm->get_gst_slab();
		$data = array();
		$i=1;
		$party=$this->uri->segment(3);
		$start_date=date('Y-m-d',strtotime($this->uri->segment(4)));
		$end_date=date('Y-m-d',strtotime($this->uri->segment(5)));
		$data = array();
		$i=1;
		         $this->db->select('a.gst,a.interest,a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.pur_paymentOn')
		         		  ->from('inventory a')
						  ->join('hpcl_location d', 'd.id=a.party');
						if($party<>'' && $party<>'ALL')
						{
						$this->db->where('a.party',$party);
						}
						$this->db->where_in('a.payment_type','5,6',false);
						$this->db->where('a.pur_payment',1);

						if($this->uri->segment(4)<>'' && $this->uri->segment(5)<>'')
						{
							$this->db->where('a.pur_paymentOn>=',$start_date." 00:00:00");
							$this->db->where('a.pur_paymentOn<=',$end_date." 23:59:59");
						}


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

						// if($product != '' &&  $product != 'ALL') {
						// $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
						// } else {
						// $chkIfProductExists = 1;
						// }
						
						// if($chkIfProductExists>0)
						// {


						$exceeded_payments=$this->check_for_date_exceeded_payments(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days")),$row->id);

						$diff = abs(strtotime($row->pur_paymentOn) - strtotime(date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))));
						$days=round($diff / (60 * 60 * 24));

						$payment_details="<strong style='color:red;font-weight:bold;'>Payment Done On-".date('d-M-Y',strtotime($row->pur_paymentOn))."</strong>";
						
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

							$data[] = array(
							'sr_no' => $i,
							'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
							'bill_no' =>$row->bill_no,
							'party'=>$row->party,
							'credit_days'=>$payment."-".$cr_days." Days",
							'product_detail'=>$pdetails[0],
							'basic_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
							'total_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$total."</strong>",
							'due_date'=>"<strong style='font-size: 15px;color:green;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
							'payment_details'=>"<strong  style='font-size: 15px;color:red;font-weight: bold;'>".$payment_details."</strong>",
							'extra_days'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".$days." Days</strong>",												
							'interest_rate'=>"<strong  style='font-size: 15px;color:blue;font-weight: bold;'>".$row->interest."%"."</strong>",												
							'interest_charges'=>"<strong  style='font-size: 22px;color:red;font-weight: bold;'>₹".$calculate_int."</strong>"											

							);

						$i++;
					}
					//}
						
					}
				
				
			}

			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);



					

			
	}



	function save_approval_type_two() {
		
		$getLastInsertedCode = $this->salescrm->getLastInsertedCode_TYPE_2();
		$customer_code=$this->input->post('customer_code');
		if(is_numeric($this->input->post('customer_name')))
		{
			$customer=$this->input->post('customer_name');
		}else
		{
			$restey=$this->db->select('id')->from('hpcl_direct_customer')->where('customer_name',$this->input->post('customer_name'))->get();
			if($restey->num_rows()>0)
			{
				foreach($restey->result() as $rr);
				$customer=$rr->id;

			}else
			{
				    if($this->input->post('payment_req') == 1) {
				    	$payment_req = 1;
				    } else {
				    	$payment_req = 0;
				    }
				$ddd=array('customer_name'=>$this->input->post('customer_name'),'customer_code'=>$customer_code,'payment_req'=>$payment_req,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('hpcl_direct_customer',$ddd);
				$customer=$this->db->insert_id();

			}
		}

				// $annex_upload = $_FILES['overall_annexture']['name'];
				// if($annex_upload<>'')
				// {
				// $tmp_name=explode('.',$annex_upload);
				// $extn=end($tmp_name);
				// $newname=time().'.'.$extn;
				// $uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
				// move_uploaded_file($_FILES['overall_annexture']['tmp_name'], $uploadFilePath);
				// }else
				// {
				// $newname='';
				// }

		$data = array(
					  'auto_gen_code' => $getLastInsertedCode,
					  'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'type'=>$this->input->post('type'),
					  'customer_name'=>$customer,
            // 'customer_tds'=>$this->input->post('customer_tds'),
            // 'customer_tcs'=>$this->input->post('customer_tcs'),
					  'added_on' => date('Y-m-d H:i:s'),
					  'added_by' => $_SESSION['logged_in']['user_id']
					  // 'invoice'=>$this->input->post('invoice'),
					  // 'invoice_date'=>date('Y-m-d',strtotime($this->input->post('invoice_date'))),
					  // 'annexture'=>$newname,
					  // 'annexture_name'=>$this->input->post('annexture')

					 );

		$this->db->insert('approval_form_type_two', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$unit = $this->input->post('unit');
		// $pack_size = $this->input->post('pack_size');
		$approved_price = $this->input->post('approved_price');
		$validity_from=$this->input->post('valid_from');
		$validity_to=$this->input->post('valid_till');
		$credit_days = $this->input->post('credit_days');
		$commision = $this->input->post('commision');
		$hpcl_location = $this->input->post('hpcl_location');
		$transportation = $this->input->post('transportation');
		$trate = $this->input->post('trate');
		$remarks=$this->input->post('remarks');
		

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {

				if($transportation[$i]==1)
				{
					$trrate=$trate[$i];
				}else
				{
					$trrate=0;
				}

				$annex_upload = $_FILES['annex_upload']['name'][$i];
				if($annex_upload<>'')
				{
				$tmp_name=explode('.',$annex_upload);
				$extn=end($tmp_name);
				$newname=time().$i.'.'.$extn;
				$uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
				move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
				}else
				{
				$newname='';
				}


				
				$datas = array(
							  'approval_id' => $last_id,
							  'location'=>$hpcl_location[$i],
							  'product_id' => $product[$i],
							  'pack_size'=>$unit[$i],
							  'validity_from'=>date('Y-m-d', strtotime($validity_from[$i])),
							  'validity_to'=>date('Y-m-d', strtotime($validity_to[$i])),
							  'approved_price' => $approved_price[$i],
							  'credit_days' => $credit_days[$i],
							  'commision' => $commision[$i],
							  'transport_type' => $transportation[$i],
							  'transport_rate' => $trrate,
							  'addedOn'=>date('Y-m-d H:i:s'),
							  'addedBy'=>$_SESSION['logged_in']['user_id'],
							  'annexure'=>$remarks[$i],
							  'annexure_upload'=>$newname
							);

				$this->db->insert('approval_product_details_type_two', $datas);
			}
		}



		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/type_two_approval_new');

	}


	function filter_type_two_approval() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');
	 	$customer = $this->input->post('customer');

	 	redirect(page_url.'Approval/type_two_approval_list/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation.'/'.$customer);

	 	
	}

	function type_two_approval_listing_pending_delivery() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

$query=$this->db->select('d.invoice,d.invoice_date,d.type,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('delivered',0);

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

					    // $this->db->where('d.current_date>=',$start_date);
					    // $this->db->where('d.current_date<=',$end_date);

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

					$update="<a href='".page_url."Approval/approval_wise_delivery_detail/".$rows->product_approval_id."' class='btn btn-warning'>Enter Qty & Other Details</a>";
					if($rows->invoice_date<>'' && $rows->invoice_date<>'0000-00-00')
					{
						$inv=date('d-M-Y',strtotime($rows->invoice_date));
					}else
					{
						$inv='';
					}
						
						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'invoice_no'=>$rows->invoice,
						'invoice_date'=>$inv,
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'update' => $update
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


	function approval_wise_delivery_detail()
	{

		$this->load->view('approval/approval_wise_item_delivery');
	}

	function add_item_delivery_detail_type_two()
	{
		$approval_id=$this->uri->segment('3');
		$interest=$this->input->post('interest');
		$gst=$this->input->post('gst');
		$current_date=$this->input->post('current_date');
		$qty=$this->input->post('qty');
		$transportation=$this->input->post('transportation');
		if($transportation==1){
			$trns_type=$this->input->post('trns_type');
			if($trns_type==1)
			{
				$our_vehicle_no=$this->input->post('our_vehicle_no');
			}else if($trns_type==2)
			{
				$transporter_id=$this->input->post('transporter_id');
				$trns_rate_type=$this->input->post('trns_rate_type');
				$trrate=$this->input->post('trrate');
				$our_vehicle_no='';
			}else{
				$transporter_id=0;
				$trns_rate_type=0;
				$trrate=0;
				$our_vehicle_no='';
			}

			/** INSERT **/
			$dd=array('delivered'=>1,'deliveredOn'=>$current_date,'deliveredQty'=>$qty,'transporter_from'=>$transportation,'vehicle_type'=>$trns_type,'transporter_id'=>$transporter_id,'vehicle_no'=>$our_vehicle_no,'transporter_rate_type'=>$trns_rate_type,'transporter_fixed_rate'=>$trrate,'interest_charges'=>$interest,'gst'=>$gst);
				$this->db->where('id',$approval_id);
				$this->db->update('approval_product_details_type_two',$dd);
		}else
		{
			$trns_type=0;

			$dd=array('delivered'=>1,'deliveredOn'=>$current_date,'deliveredQty'=>$qty,'interest_charges'=>$interest,'gst'=>$gst);
			$this->db->where('id',$approval_id);
			$this->db->update('approval_product_details_type_two',$dd);

			/** INSERT **/
		}
		

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
		redirect(page_url.'Approval/type_two_approval_list_pending_delivery/'.date('Y-m-01').'/'.date('Y-m-t').'/ALL/ALL/ALL');

		
	}


	public function type_two_approval_list_pending_delivery_history() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_two_approval_list_pending_delivery_history', $data);
	}


	function type_two_approval_listing_pending_delivery_history() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

$query=$this->db->select('d.type,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('delivered',1);

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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
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

					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;
						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transportation_type' =>$transportation,
						'edit'=>'<a href="'.page_url.'Approval/edit_approval_wise_delivery_detail/'.$rows->product_approval_id.'"><i class="fa fa-pencil"></i></a>'
					
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


		function filter_type_two_approval_history() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_two_approval_list_pending_delivery_history/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
	}


	function this_month__cr_note_claim_list_t2() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

$query=$this->db->select('d.type,a.paymentOn,a.payment_evidence,a.paid_amount,a.collection_reference,a.invoice,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name');

					    $this->db->where('d.current_date>=',$start_date);
					    $this->db->where('d.current_date<=',$end_date);
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
					$trate=$rows->transport_rate;
					$transport_commission=$trate*$rows->deliveredQty;
				
					}else
					{
					$transport="Delivered";
						$trate='-';
					$transport_commission=0;

					
					}

						 
					$delivery_detail=floatval($rows->deliveredQty)." ".$rows->unit."<br/><br/>Delivered On-".date('d-M-Y',strtotime($rows->deliveredOn));

					$tfrom='';
					$vtype='';
					$vno='';
					$rtype='';
					$t_type_rate='';
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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
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

					$cfa_commission=$rows->deliveredQty*$rows->commision;

					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;
					$payment="Payment Done On-".date('d-M-Y',strtotime($rows->paymentOn))."<br/>Payment Evidence-<a href='".page_url1."customer_payment_type/".$rows->payment_evidence."' download>Download</a><br/><strong  style='color:red;font-size:18px;font-weight:bold;'>₹ ".$rows->paid_amount."</strong><br/><br/>Collection Ref- ".$rows->collection_reference."<br/><br/>Invoice-".$rows->invoice;

					    $total=$cfa_commission+$transport_commission;
						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transportation_type' =>$transportation,
						'cfa_comission' =>"<strong style='color:red;font-weight:bold;font-size:22px;'>₹".$cfa_commission."</strong>",
						'transportation_commission' =>"<strong style='color:red;font-weight:bold;font-size:22px;'>₹".$transport_commission."</strong>",
						'total' =>"<strong style='color:red;font-weight:bold;font-size:22px;'>₹".$total."</strong>",
						'payment_details'=>$payment
					
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

	function type_2_customer_payment_pending()
	{
		$this->load->view('approval/pending_customer_payment_type2');
	}



	function type_two_approval_customer_payment_pending() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$type = base64_decode($this->uri->segment(5));
		// $hpcl_location = $this->uri->segment(5);
		// $product = $this->uri->segment(6);
		// $transport_type = $this->uri->segment(7);
		$query=$this->db->select('d.invoice_date,d.invoice,d.type,d.customer_tds,d.customer_tcs,a.gst,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment,e.id as customer_id')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('a.delivered',1)
					    ->where('a.payment',0)
					    ->where('d.current_date>=',$start_date)
					    ->where('d.current_date<=',$end_date);
					    if($type<>'' && $type<>'ALL')
					    {
					   	 $this->db->where('d.type',$type);
					  	}
					    // if($hpcl_location<>'ALL' && $hpcl_location<>'')
					    // {
					    // 	$this->db->where('a.location',$hpcl_location);
					    // }
					    //  if($product<>'ALL' && $product<>'')
					    // {
					    // 	$this->db->where('a.product_id',$product);
					    // }
					    // if($transport_type<>'ALL' && $transport_type<>'')
					    // {
					    // 	$this->db->where('a.transport_type',$transport_type);
					    // }

					  	$this->db->order_by('d.current_date','ASC');
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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
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

					if($rows->invoice_date<>'' && $rows->invoice_date<>'0000-00-00')
					{
						$inv_date=date('d-M-Y',strtotime($rows->invoice_date));
					}else
					{
						$inv_date='';
					}
					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;

					// $payment="<a href='javascript:;' onclick='payment_done(".$rows->product_approval_id.")' class='btn btn-warning'>Update Payment</a>";
          $prev_payment=$this->check_for_customer_previous_payment($rows->product_approval_id);

					$amount=$rows->approved_price*$rows->deliveredQty;
					$gst=($amount*$rows->gst)/100;
					
					$final_base_amount=$amount+$gst;

					/** TDS ON BASIC AMOUNT **/
					if($rows->customer_tds>0)
					{
					$tds=$rows->customer_tds;
					$tds_amount=$amount*($tds/100);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->customer_tcs;
           $tcs_amt = $final_base_amount*$rows->customer_tcs/100;
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

               $payment="<a href='".page_url."Approval/pending_customer_payments_type_2/".$rows->product_approval_id."/".base64_encode($final_payable_amount)."/1/".$rows->customer_id."/".$start_date."/".$end_date."/".base64_encode($type)."' class='btn btn-warning'>Update Payment Status</a>";

						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'invoice' => $rows->invoice,
						'product_name' => $rows->instruments_name,
						'approved_price' => $rows->approved_price."/".$rows->unit,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transportation_type' =>$transportation,
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds."</strong>",
						'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
            'tcs' => "<strong style='color:red;font-size:18px;font-weight:bold;'>" . $tcs . "</strong>",
            'tcs_amt' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $tcs_amt . "</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$amount."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_base_amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst."</strong>",
						'tamount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tinvoice_value."</strong>",
            'prev_payment' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $prev_payment . "</strong>",
            'final_payable_amount' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $final_payable_amount . "</strong>",
						'payment' =>$payment
					
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

	function get_final_date()
	{
		$final_date='';
		$id=$this->uri->segment(3);
		$res=$this->db->select('b.customer_tds,b.customer_tcs,a.gst,a.credit_days,b.current_date,a.deliveredQty,a.approved_price')->from('approval_product_details_type_two a')->join('approval_form_type_two b','a.approval_id=b.id')->where('a.id',$id)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);

			$amount=$row->approved_price*$row->deliveredQty;
			$g=$row->gst/100;
			$gg=$amount*$g;
			

			if($row->customer_tds>0)
			{
			$tds=$amount*($row->customer_tds/100);
			$tds_amount=$amount-$tds;
			$amount=$tds_amount+$gg;
			}else
			{
			$amount=$amount+$gg;
			}

      // if($row->customer_tcs>0){
      //   $tcs_amt = $row->approved_price*$row->deliveredQty*$row->customer_tcs/100;
      //   $amount = $amount + $tcs_amt;
      // }

			$final_date=date('d-M-Y',strtotime($row->current_date ." +".$row->credit_days." Days"));


		}


		echo $final_date."|".$amount;
	}

	function update_payment_customer_type_two()
	{
		$approval_id=$this->input->post('approval_id');
		$payment_date=$this->input->post('payment_date');
		$payable_amount_paid=$this->input->post('payable_amount_paid');
		$reference=$this->input->post('reference');
		$invoiceno=$this->input->post('invoiceno');
		$name=$_FILES['evidance']['name'];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'customer_payment_type/'.basename($newname);
		move_uploaded_file($_FILES['evidance']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}

		$dd=array('payment'=>1,'paymentOn'=>date('Y-m-d H:i:s'),'paymentBy'=>$_SESSION['logged_in']['user_id'],'payment_evidence'=>$newname,'paid_amount'=>$payable_amount_paid,'collection_reference'=>$reference,'invoice'=>$invoiceno);
		$this->db->where('id',$approval_id);
		$this->db->update('approval_product_details_type_two',$dd);

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/type_2_customer_payment_pending');


	}



	function type_two_approval_customer_payment_pending_history() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$type = base64_decode($this->uri->segment(5));
		// $hpcl_location = $this->uri->segment(5);
		// $product = $this->uri->segment(6);
		// $transport_type = $this->uri->segment(7);
		$query=$this->db->select('d.invoice_date,d.invoice,d.type,d.customer_tds,d.customer_tcs,a.gst,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment,e.id as customer_id')
					    ->from('approval_product_details_type_two a')
					    ->join('approval_form_type_two d','a.approval_id=d.id')
					    ->join('presto_instruments b', 'b.id=a.product_id' ,'left')
					   	->join('hpcl_location c', 'c.id=a.location')
					   	->join('hpcl_direct_customer e', 'e.id=d.customer_name')
					    ->where('a.delivered',1)
					    ->where('a.payment',1);
					   //  if($type<>'' && $type<>'ALL')
					   //  {
					   // 	 $this->db->where('d.type',$type);
					  	// }
					   

					  	$this->db->order_by('d.current_date','ASC');
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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
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

					if($rows->invoice_date<>'' && $rows->invoice_date<>'0000-00-00')
					{
						$inv_date=date('d-M-Y',strtotime($rows->invoice_date));
					}else
					{
						$inv_date='';
					}
					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;

					// $payment="<a href='javascript:;' onclick='payment_done(".$rows->product_approval_id.")' class='btn btn-warning'>Update Payment</a>";
          $prev_payment=$this->check_for_customer_previous_payment($rows->product_approval_id);

					$amount=$rows->approved_price*$rows->deliveredQty;
					$gst=($amount*$rows->gst)/100;
					
					$final_base_amount=$amount+$gst;

					/** TDS ON BASIC AMOUNT **/
					if($rows->customer_tds>0)
					{
					$tds=$rows->customer_tds;
					$tds_amount=$amount*($tds/100);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->customer_tcs;
           $tcs_amt = $final_base_amount*$rows->customer_tcs/100;
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

               $payment="<a href='".page_url."Approval/pending_customer_payments_type_2/".$rows->product_approval_id."/".base64_encode($final_payable_amount)."/1/".$rows->customer_id."/".$start_date."/".$end_date."/".base64_encode($type)."' class='btn btn-warning'>Update Payment Status</a>";

               $payment_collection_details=$this->get_customer_payment_details_with_collection_ids_new($rows->product_approval_id,$rows->customer_id);

						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'invoice' => $rows->invoice,
						'product_name' => $rows->instruments_name,
						'approved_price' => $rows->approved_price."/".$rows->unit,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transportation_type' =>$transportation,
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds."</strong>",
						'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
            'tcs' => "<strong style='color:red;font-size:18px;font-weight:bold;'>" . $tcs . "</strong>",
            'tcs_amt' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $tcs_amt . "</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$amount."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_base_amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst."</strong>",
						'tamount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tinvoice_value."</strong>",
            'prev_payment' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $prev_payment . "</strong>",
            'final_payable_amount' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹" . $final_payable_amount . "</strong>",
						'evidence' => $payment_collection_details
					
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


		function pending_transporter_payment_list_t2() {


		      $data = array();
		$i=1;
	
		$hpcl_location = $this->uri->segment(4);
		$transport_type = $this->uri->segment(3);

						$query=$this->db->select('d.type,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
						->from('approval_product_details_type_two a')
						->join('approval_form_type_two d','a.approval_id=d.id')
						->join('presto_instruments b', 'b.id=a.product_id' ,'left')
						->join('hpcl_location c', 'c.id=a.location')
						->join('hpcl_direct_customer e', 'e.id=d.customer_name')
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
					if($rows->transporter_from==1)
					{
						$tfrom="OUR";
					if($rows->vehicle_type==1)
					{
						$vtype="Our Vehicle";
						$vno=$rows->vehicle_no;
						$tds=0;
					}else if($rows->vehicle_type==2)
					{
						$vtype="Hired Vehicle";
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						$tds=$this->get_trasnporter_tds($rows->transporter_id);
						
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate*$rows->deliveredQty;

							$amount=$t_type_rate;
							$base_amount=$amount;
							if($tds>0)
							{
								$tds_amount=$base_amount*($tds/100);
							}else
							{
								$tds_amount=0;
							}


							$amount=$base_amount-$tds_amount;
							
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
							$base_amount=$rows->transporter_fixed_rate;
							$amount=$rows->transporter_fixed_rate;
							if($tds>0)
							{
								$tds_amount=$base_amount*($tds/100);
							}else
							{
								$tds_amount=0;
							}

							$amount=$base_amount-$tds_amount;
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

					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;

				

					$payment="<a href='javascript:;' onclick='payment_done(".$rows->product_approval_id.")' class='btn btn-warning'>Update Payment</a>";

						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'approved_price' => $rows->approved_price."/".$rows->unit,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transporter' =>$vno,
						'faretype' =>$rtype,
						'fare' =>$rows->transporter_fixed_rate,
						'tds' =>$tds,
						'amount' =>"<strong style='color:red;font-size:24px;font-weight:bold;'>₹".$base_amount."</strong>",
						'tds_deduction' =>"<strong style='color:red;font-size:24px;font-weight:bold;'>₹".$tds_amount."</strong>",
						'final_amount' =>"<strong style='color:red;font-size:24px;font-weight:bold;'>₹".$amount."</strong>",
						'payment' =>$payment
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


	function get_transporter_payment_details()
	{
		$final_date='';
		$amount=0;
		$id=$this->uri->segment(3);
		$res=$this->db->select('b.tds,b.name,a.deliveredQty,a.approved_price,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate')->from('approval_product_details_type_two a')->join('transporter_details b','a.transporter_id=b.id')
						->where('a.delivered',1)
						->where('a.vehicle_type',2)
						->where('a.transporter_payment',0)
						->where('a.id',$id)->get();
		if($res->num_rows()>0)
		{
			foreach($res->result() as $row);
			if($row->transporter_rate_type==1)
			{
				$amount=$row->deliveredQty*$row->transporter_fixed_rate;
				if($row->tds>0)
				{
					$tds_amount=$amount*($row->tds/100);
				}else
				{
					$tds_amount=0;
				}

				$amount=$amount-$tds_amount;

			}else{

				$amount=$row->transporter_fixed_rate;
				if($row->tds>0)
				{
					$tds_amount=$amount*($row->tds/100);
				}else
				{
					$tds_amount=0;
				}

				$amount=$amount-$tds_amount;

			}

		

		}


		echo $amount."|".$row->name;
	}

	function update_payment_transporter_type_twoOLddd()
	{
		$approval_id=$this->input->post('approval_id');
		$payment_date=$this->input->post('payment_date');
		$flag1=$this->input->post('flag1');
		$flag2=$this->input->post('flag2');

		$name=$_FILES['evidance']['name'];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'transporter_payment/'.basename($newname);
		move_uploaded_file($_FILES['evidance']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}

		$dd=array('transporter_payment'=>1,'transporter_paymentOn'=>date('Y-m-d H:i:s'),'transporter_paymentEvidence'=>$newname);
		$this->db->where('id',$approval_id);
		$this->db->update('approval_product_details_type_two',$dd);
		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/pending_transporters_payment/'.$flag1.'/'.$flag2);


	}


	function pending_transporter_payment_list_t2_history() {


		      $data = array();
		$i=1;
	
		$hpcl_location = $this->uri->segment(4);
		$transport_type = $this->uri->segment(3);

						$query=$this->db->select('d.type,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment,a.transporter_paymentOn,a.transporter_paymentEvidence')
						->from('approval_product_details_type_two a')
						->join('approval_form_type_two d','a.approval_id=d.id')
						->join('presto_instruments b', 'b.id=a.product_id' ,'left')
						->join('hpcl_location c', 'c.id=a.location')
						->join('hpcl_direct_customer e', 'e.id=d.customer_name')
						->where('a.delivered',1)
						->where('a.vehicle_type',2)
						->where('a.transporter_payment',1);

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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate*$rows->deliveredQty;

							$amount=$t_type_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
							$amount=$rows->transporter_fixed_rate;
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

					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;


					$payment="Payment Done On-".date('d-M-Y',strtotime($rows->transporter_paymentOn))."<br/>Payment Evidence-<a href='".page_url1."transporter_payment/".$rows->transporter_paymentEvidence."' download>Download</a>";

						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'approved_price' => $rows->approved_price."/".$rows->unit,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transporter' =>$vno,
						'faretype' =>$rtype,
						'fare' =>$rows->transporter_fixed_rate,
						'amount' =>"<strong style='color:red;font-size:24px;font-weight:bold;'>₹".$amount."</strong>",
						'payment' =>$payment
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

	 function filter_transporter_pending_payment_done() {

	 	$party = $this->input->post('party');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	redirect(page_url.'Approval/pending_transporters_payment_history/'.$party.'/'.$hpcl_locations);
	}


	function type_2_customer_payment_charges()
	{
		$this->load->view('approval/type_2_customer_payment_charges');
	}


	function type_2_customer_payment_charges_list() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));


$query=$this->db->select('d.type,a.gst,a.interest_charges,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment,a.paymentOn,a.paymentBy,a.payment_evidence')
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
						$vno=$this->get_trasnporter_name($rows->transporter_id);
						if($rows->transporter_rate_type==1)
						{
							$rtype="Per Ltr";
							$t_type_rate=$rows->transporter_fixed_rate;
						}else if($rows->transporter_rate_type==2)
						{
							$rtype="Fixed Amount";
							$t_type_rate=$rows->transporter_fixed_rate."/".$rows->unit;
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

					$transportation="Transport From-".$tfrom."<br/>Transport Type-".$vtype."<br/>Transporter/Vehicle no-".$vno."<br/>Rate Type-".$rtype."<br/>Rate-".$t_type_rate;

						$payment="Payment Done On-".date('d-M-Y',strtotime($rows->paymentOn))."<br/>Payment Evidence-<a href='".page_url1."customer_payment_type/".$rows->payment_evidence."' download>Download</a>";
						$amount=$rows->approved_price*$rows->deliveredQty;

						$duedate=date('Y-m-d',strtotime($rows->deliveredOn ."+".$rows->credit_days." Days"));
						$paymentOn=date('Y-m-d',strtotime($rows->paymentOn));

						
						$datediff = strtotime($paymentOn) - strtotime($duedate);
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


						$data[] = array(
						'sr_no' => $i,
						'current_date' => $rows->type."<br/>".date('d-m-Y', strtotime($rows->current_date)),
						'customer_name' => $rows->customer_name,
						'location' => $rows->name,
						'product_name' => $rows->instruments_name,
						'approved_price' => $rows->approved_price."/".$rows->unit,
						'validity' =>"From: ".date('d-m-Y',strtotime($rows->validity_from))."<br/>To ".date('d-m-Y',strtotime($rows->validity_to))."<br/>".$p,
						'credit_days' =>$rows->credit_days." Days",
						'comission' =>$rows->commision."/".$rows->unit,
						'transport_type' =>"<strong style='color:red;'>".$transport."</strong>",
						'transport_rate' =>"<strong style='color:red;'>".$trate."</strong>",
						'delivered_qty' =>$delivery_detail,
						'transportation_type' =>$transportation,
						'payment' =>$payment,
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst_amount."</strong>",
						'total_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$total."</strong>",
						'paymentOn'=>date('d-M-Y',strtotime($rows->paymentOn)),
						'overdue'=>$days." Day(s)",
						'interest_per'=>"<strong style='color:red;font-size:14px;font-weight:bold;'>".$rows->interest_charges." %</strong>",
						'intrest'=>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".round($calculate_int,1)."</strong>"



					
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

	function filter_late_payment_type_2()
	{
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
     	redirect(page_url.'Approval/type_2_customer_payment_charges/'.$from_date.'/'.$to_date);
	}

	function get_customer_code()
	{
		$code='';
		$tds='';
		$address='';
		$gst='';
		$state='';
		$customer=$this->input->post('customer');
		$tcs='';

		if(is_numeric($customer))
		{
			$ret=$this->db->select('customer_code, tds, address, gst, state,tcs')->from('hpcl_direct_customer')->where('id',$customer)->get();
			if($ret->num_rows()>0)
			{
				foreach($ret->result() as $row);
				$code=$row->customer_code;
				$tds=$row->tds;
				$address=$row->address;
				$gst=$row->gst;
				$state=$row->state;
        $tcs=$row->tcs;
			}

		}

		echo $code."|".$tds."|".$address."|".$gst."|".$state."|".$tcs;

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


		function getPreviousPurchase($year,$increment,$product)
		{
			$pur_qty=0;
			$start_date=date($year.'-m-01');
			$end_date=date($year.'-m-t');

			$restey=$this->db->select('sum(a.qty) as totalqty')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('b.currentdate>=',$start_date)->where('b.currentdate<=',$end_date)->where('a.product',$product)->get();
			if($restey->num_rows()>0)
			{
				foreach($restey->result() as $row);
				$pur_qty=$row->totalqty;
				$p=$increment/100;
				$inc_qty=$pur_qty*$p;
				$pur_qty=$pur_qty+$inc_qty;
			}

			return $pur_qty; 

		}

		function getPreviousPurchase_for_ajax()
		{
			$year=$this->input->post('year');
			$product=$this->input->post('product');
			$increment=$this->input->post('increment');
			$pur_qty=0;
			$start_date=date($year.'-m-01');
			$end_date=date($year.'-m-t');

			$restey=$this->db->select('sum(a.qty) as totalqty')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('b.currentdate>=',$start_date)->where('b.currentdate<=',$end_date)->where('a.product',$product)->get();
			if($restey->num_rows()>0)
			{
				foreach($restey->result() as $row);
				$pur_qty=$row->totalqty;
				$p=$increment/100;
				$inc_qty=$pur_qty*$p;
				$pur_qty=$pur_qty+$inc_qty;
			}

			echo $pur_qty; 

		}

		function transportation_approval()
		{
			$this->load->view('approval/transportation_based_approval');
		}

		function save_transportation_based_approval(){



		$customer_code=$this->input->post('customer_code');
		if(is_numeric($this->input->post('customer_name')))
		{
			$customer=$this->input->post('customer_name');

				$ddd=array(
						   'customer_name'=>$this->input->post('customer_name'),
						   'customer_code'=>$customer_code,
						   'tds'=>$this->input->post('customer_tds'),
						   'address'=>$this->input->post('customer_address'),
						   'gst'=>$this->input->post('customer_gst'),
						   'state'=>$this->input->post('customer_state'),
						   'addedOn'=>date('Y-m-d H:i:s'),
						   'addedBy'=>$_SESSION['logged_in']['user_id']
						   );

				$this->db->where('id', $customer)
						 ->update('hpcl_direct_customer',$ddd);
		}else
		{
			$restey=$this->db->select('id')->from('hpcl_direct_customer')->where('customer_name',$this->input->post('customer_name'))->get();
			if($restey->num_rows()>0)
			{
				foreach($restey->result() as $rr);
				$customer=$rr->id;

			}else
			{
				$ddd=array(
						   'customer_name'=>$this->input->post('customer_name'),
						   'customer_code'=>$customer_code,
						   'tds'=>$this->input->post('customer_tds'),
						   'address'=>$this->input->post('customer_address'),
						   'gst'=>$this->input->post('customer_gst'),
						   'state'=>$this->input->post('customer_state'),
						   'addedOn'=>date('Y-m-d H:i:s'),
						   'addedBy'=>$_SESSION['logged_in']['user_id']
						   );
				$this->db->insert('hpcl_direct_customer',$ddd);
				$customer=$this->db->insert_id();

			}
		}

		if($this->input->post('trns_type')==1)
		{
			$vehicle_no=$this->input->post('our_vehicle_no');
			$transporter_id=0;
			$trns_rate_type=0;
			$trrate=0;

		}else
		{
			$vehicle_no=$this->input->post('trrate_v');
			$transporter_id=$this->input->post('transporter_id');
			$trns_rate_type=$this->input->post('trns_rate_type');
			$trrate=$this->input->post('trrate');
		}

		$data = array(
					  'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
					  'type'=>$this->input->post('type'),
					  'hpcl_location'=>$this->input->post('hpcl_location'),
					  'customer_name'=>$customer,
					  'tapproval_type'=>$this->input->post('ttype'),
					  'tapproval_rate'=>$this->input->post('trate'),
					  'transport_done_by'=>$this->input->post('trns_type'),
					  'transporter_id'=>$transporter_id,
					  'vehicle_no'=>$vehicle_no,
					  'transporter_rate_type'=>$trns_rate_type,
					  'transporter_rate_fixed'=>$trrate,
					  'invoice_no' => $this->input->post('invoice_no'),
					  'supplier_ref' => $this->input->post('supplier_ref'),
					  'other_ref' => $this->input->post('other_ref'),
					  'terms_of_del' => $this->input->post('terms_of_del'),
					  'dispatched_through' => $this->input->post('dispatched_through'),
					  'destination' => $this->input->post('destination'),
					  'particular' => $this->input->post('particular'),
					  'added_on' => date('Y-m-d H:i:s'),
					  'added_by' => $_SESSION['logged_in']['user_id']
					 );

		$this->db->insert('transportation_based_approval', $data);
		$last_id = $this->db->insert_id();

		$product = $this->input->post('product');
		$unit = $this->input->post('unit');
		// $pack_size = $this->input->post('pack_size');
		$qty = $this->input->post('qty');
		$annexure = $this->input->post('annexure');

	

		for($i = 0; $i < count($product); $i++) {
			if($product[$i] != '') {

			
				$annex_upload = $_FILES['annex_upload']['name'][$i];
				if($annex_upload<>'')
				{
				$tmp_name=explode('.',$annex_upload);
				$extn=end($tmp_name);
				$newname=time().$i.'.'.$extn;
				$uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
				move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
				}else
				{
				$newname='';
				}


				$datas = array(
							  'approval_id' => $last_id,
							  'product_id' => $product[$i],
							  'pack_size'=>$unit[$i],
							  'deliveredQty' => $qty[$i],
							  'addedOn'=>date('Y-m-d H:i:s'),
							  'addedBy'=>$_SESSION['logged_in']['user_id'],
							  'annexure'=>$annexure[$i],
							  'annexure_upload'=>$newname
							);

				$this->db->insert('transportation_based_aprroval_product_details', $datas);
			}
		}



		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/transportation_approval');

		}


		public function months_transport_based_claim() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/transportation_based_approval_list', $data);
	}


	function transportation_based_approval_list() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);


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
													
		

					$getApprovalProductDetails = $this->salescrm->transportation_based_approval_ProductDetails($row->id,$hpcl_location,$product);
				
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
							$vehicle_no='';
						}else
						{
							$transport="Hired Vehicle";
							$transporter_name=$this->get_trasnporter_name($row->transporter_id);
							$vehicle_no=$row->vehicle_no;
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
					$claim = $this->salescrm->transportation_based_approval_Productclaim($row->id,$hpcl_location,$product,$tapp_rate);
					}else
					{
						$claim=$tapp_rate;
					}

					$generate_invoice = "<a href='".page_url1."taxinvoice/tcpdf/examples/invoice.php?approval_id=".$row->id."' class='btn btn-success btn-xs'>View Invoice</a>";

					 $edit = "<a href='".page_url."Approval/edit_transportation_based_approval/".$row->id."' class='btn btn-xs btn-default'>Edit Approval</a>";

					$data[] = array(
							'sr_no' => $i, 
							'current_date' => date('d-m-Y', strtotime($row->current_date)),
							'customer_name' =>$row->customer_name,
							'hpcl_location' => $row->locationname,
							'product_details' =>$getApprovalProductDetails,
							'transport_approval'=>"<strong style='color:red;font-weight:bold;'>".$tap." @ ".$tapp_rate."</strong>",
							'transport_details'=>"Type- ".$transport."<br/>Transporter/Vehicle- ".$transporter_name."<br/><strong style='color:red;font-weight:bold;font-size:15px;'>Vehicle No. ".$vehicle_no."<br/><strong style='color:red;font-weight:bold;font-size:15px;'>Rate Type- ".$transporter_type."<br/>Rate- ".$transported_fixed_rate."</strong>",
							'claim' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$claim."</strong>",
							'generate_invoice' => $generate_invoice,
							'edit' => $edit
						 
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


	function filter_transport_based_approval() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 

	 	redirect(page_url.'Approval/months_transport_based_claim/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products);

	 	
	}

	function pending_transporter_payment()
	{
		$this->load->view('approval/transporter_payment_pending');
	}

	function approval_based_transporter_pending_payment()
	{


		$data = array();
		$i=1;
		$transporter = $this->uri->segment(3);
	


		         $this->db->select('c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location c','a.hpcl_location=c.id')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');

			

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('a.transporter_id', $transporter);
			}

			 $this->db->where('a.payment',0);
			 $this->db->where('a.transport_done_by',2);

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
													
		

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


					$payment="<a href='javascript:;' onclick='payment_done(".$row->id.")' class='btn btn-warning'>Update Payment</a>";  
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)),
							'customer_name' =>$row->customer_name,
							'hpcl_location' => $row->locationname,
							'product_details' =>$getApprovalProductDetails,
							'transport_approval'=>"<strong style='color:red;font-weight:bold;'>".$tap." @ ".$tapp_rate."</strong>",
							'transport_details'=>"Type- ".$transport."<br/>Transporter/Vehicle- ".$transporter_name."<br/><strong style='color:red;font-weight:bold;font-size:15px;'>Rate Type- ".$transporter_type."<br/>Rate- ".$transported_fixed_rate."</strong>",
							'claim' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$claim."</strong>",
							'tds' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$tds_amount." @ ".$tds."%</strong>",
							'finalpayment' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$final_claim."</strong>",
							'update_payment' =>$payment
						 
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


	function transporter_payment_evidence()
	{
		$approval_id=$this->input->post('approval_id');
		$payment_date=$this->input->post('payment_date');
		$flag1=$this->input->post('flag1');
		$flag2=$this->input->post('flag2');
		$flag3=$this->input->post('flag3');
		$flag4=$this->input->post('flag4');
		$flag5=$this->input->post('flag5');

		$name=$_FILES['evidance']['name'];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'transporter_payment/'.basename($newname);
		move_uploaded_file($_FILES['evidance']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}

		$dd=array('payment'=>1,'paymentOn'=>date('Y-m-d H:i:s'),'paymentBy'=>$_SESSION['logged_in']['user_id'],'payment_evidence'=>$newname);
		$this->db->where('id',$approval_id);
		$this->db->update('transportation_based_approval',$dd);
		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/pending_transporter_payment/'.$flag1.'/'.$flag2.'/'.$flag2.'/'.$flag3.'/'.$flag4.'/'.$flag5);


	}

	function filter_transport_based_approval_payment_pending() {
	 	$transporter = $this->input->post('transporter');
	  	redirect(page_url.'Approval/pending_transporter_payment/'.$transporter);

	 	
	}


	function pending_transporter_payment_history()
	{
		$this->load->view('approval/transporter_payment_pending_history');
	}

	function approval_based_transporter_pending_payment_history()
	{


		$data = array();
		$i=1;
		$transporter = $this->uri->segment(3);
	


		         $this->db->select('a.paymentOn,a.payment_evidence,c.name as locationname,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.tapproval_type,a.tapproval_rate,a.transport_done_by,a.transporter_id,a.vehicle_no,a.transporter_rate_type,a.transporter_rate_fixed')
						  ->from('transportation_based_approval a')
						  ->join('hpcl_location c','a.hpcl_location=c.id')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');

			

			if($transporter<>'' && $transporter<>'ALL')
			{
				 $this->db->where('a.transporter_id', $transporter);
			}

			 $this->db->where('a.payment',1);
			 $this->db->where('a.transport_done_by',2);

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
													
		

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


					$payment="Payment Done On-".date('d-M-Y',strtotime($row->paymentOn))."<br/>Payment Evidence-<a href='".page_url1."transporter_payment/".$row->payment_evidence."' download>Download</a>";  
					$data[] = array(
							'sr_no' => $i,
							'current_date' => date('d-m-Y', strtotime($row->current_date)),
							'customer_name' =>$row->customer_name,
							'hpcl_location' => $row->locationname,
							'product_details' =>$getApprovalProductDetails,
							'transport_approval'=>"<strong style='color:red;font-weight:bold;'>".$tap." @ ".$tapp_rate."</strong>",
							'transport_details'=>"Type- ".$transport."<br/>Transporter/Vehicle- ".$transporter_name."<br/><strong style='color:red;font-weight:bold;font-size:15px;'>Rate Type- ".$transporter_type."<br/>Rate- ".$transported_fixed_rate."</strong>",
							'claim' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$claim."</strong>",
							'tds' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$tds_amount." @ ".$tds."%</strong>",
							'finalpayment' =>"<strong style='font-size:22px;color:red;font-weight:bold'>₹".$final_claim."</strong>",
							'update_payment' =>$payment
						 
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

	function generate_invoice() {
		$this->load->view('approval/generate_invoice');
	}


	function history_payments() {
    $this->load->view('approval/history_payments');

  }



 function payment_done_list() {
    
    $data = array();
    $i=1;
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d');
    $party=$this->uri->segment(3);  
    $data = array();
    $i=1;
             $this->db->select('a.id,a.hpcl_billing_company,a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.pur_paymentBy,a.cheque_no,a.cheque_date,a.pur_payment_evidance,a.utr_no,a.gst,a.payment_done,a.collection_reference,a.pur_paymentOn')
                  ->from('inventory a')
              ->join('vendors d', 'd.id=a.party');
            if($party<>'' && $party<>'ALL')
            {
            $this->db->where('a.party',$party);
            }

            $this->db->where_in('a.payment_type','5,6,3',false);
            $this->db->where('a.pur_payment',1);
            $this->db->where('d.hpcl',1);


       $query =  $this->db->get();

      if($query->num_rows() > 0) {
      foreach($query->result() as $row) {

          $purchase_details=$this->get_purchase_details($row->id);
          $pdetails=explode('|',$purchase_details);
          // $action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';

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

            // if($product != '' &&  $product != 'ALL') {
            // $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
            // } else {
            // $chkIfProductExists = 1;
            // }
            
            // if($chkIfProductExists>0)
            // {




            // $payment_details = 'Payment:- '.$row->payment_done."<br>Collection Reference:- ".$row->collection_reference."<br>Payment Date:- <br>".date('d-M-Y',strtotime($row->pur_paymentOn))."<br>";


            // $evidence = '';

            //    if($row->pur_paymentBy == 1){
            //     $payment_type = 'NEFT<br>UTR No:- '.$row->utr_no.'<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
            //   }elseif($row->pur_paymentBy == 2){
            //     $payment_type = 'Cheque<br>No:- '.$row->cheque_no.'<br>Date:- '.date('d-M-Y',strtotime($row->cheque_date)) .'<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
            //   }elseif($row->pur_paymentBy == 3){
            //     $payment_type = 'Cash<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
            //   }else{
            //     $payment_type = '';
            //   }



            $payment_details=$this->get_payment_details_with_collection_ids($row->id);
            $gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;





            $action="<a href='javascript:;' class='btn btn-warning btn-xs' onclick='update_payment(".$row->id.",".$grand_amt.");'>Edit Payment Details</a>";

            $companybilling=$this->salescrm->get_company_name($row->hpcl_billing_company);

            // if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
              $data[] = array(
              'sr_no' => $i."<br/>".$row->id,
              'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
              'billing_company'=>$companybilling,
              'bill_no' =>$row->bill_no,
              'party'=>$row->party,
              'credit_days'=>$payment."-".$cr_days." Days",
              'product_detail'=>$pdetails[0],
              'basic_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
              'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
              'gst'=> $gst,
              'grand_amount' =>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$grand_amt."</strong>",
              'action'=>  $payment_details ,
              'edit'=>''                     

              );

            $i++;
          //}
          //}
            
          }
        
        
      }

      $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($data),
      "iTotalDisplayRecords" => count($data),
      "aaData"=>$data);
      
    echo json_encode($results);

      
  }
  function payment_done_listOLddd() {
    
    $data = array();
    $i=1;
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d');
    $party=$this->uri->segment(3);  
    $data = array();
    $i=1;
             $this->db->select('a.id, a.bill_no,a.currentdate,d.name as party,a.credit_days,a.payment_type,a.transport_type,a.transporter,a.vehicle_no,a.vehicle_type,a.transporter_rate,a.pur_paymentBy,a.cheque_no,a.cheque_date,a.pur_payment_evidance,a.utr_no,a.gst')
                  ->from('inventory a')
              ->join('hpcl_location d', 'd.id=a.party');
            if($party<>'' && $party<>'ALL')
            {
            $this->db->where('a.party',$party);
            }

            $this->db->where_in('a.payment_type','5,6',false);
            $this->db->where('a.pur_payment',1);


       $query =  $this->db->get();

      if($query->num_rows() > 0) {
      foreach($query->result() as $row) {

          $purchase_details=$this->get_purchase_details($row->id);
          $pdetails=explode('|',$purchase_details);
          // $action='<a href="'.page_url.'Inventory/edit_inventory/'.$row->id.'"><i class="fa fa-pencil"></i></a>';

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

            // if($product != '' &&  $product != 'ALL') {
            // $chkIfProductExists = $this->chkIfProductExists($row->id, $product);
            // } else {
            // $chkIfProductExists = 1;
            // }
            
            // if($chkIfProductExists>0)
            // {
            $evidence = '';

               if($row->pur_paymentBy == 1){
                $payment_type = 'NEFT<br>UTR No:- '.$row->utr_no.'<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
              }elseif($row->pur_paymentBy == 2){
                $payment_type = 'Cheque<br>No:- '.$row->cheque_no.'<br>Date:- '.$row->cheque_date.'<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
              }elseif($row->pur_paymentBy == 3){
                $payment_type = 'Cash<br><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a>';
              }else{
                $payment_type = '';
              }

            $gst = $pdetails[1]*$row->gst/100; 
            $grand_amt = $pdetails[1]+$gst;



            $action="<a href='javascript:;' class='btn btn-warning' onclick='update_payment(".$row->id.");'>Update Payment Status</a>";

            // if(date('Y-m-d')==date('Y-m-d',strtotime($row->currentdate." +".$cr_days." Days"))) {
              $data[] = array(
              'sr_no' => $i,
              'purchase_date' =>date('d-M-Y',strtotime($row->currentdate)),
              'bill_no' =>$row->bill_no,
              'party'=>$row->party,
              'credit_days'=>$payment."-".$cr_days." Days",
              'product_detail'=>$pdetails[0],
              'basic_amount'=>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$pdetails[1]."</strong>",
              'due_date'=>"<strong style='font-size: 15px;color:red;font-weight: bold;'>".date('d-m-Y',strtotime($row->currentdate." +".$cr_days." Days"))."</strong>",
              'gst'=> $gst,
              'grand_amount' =>"<strong style='font-size: 18px;color:red;font-weight: bold;'>₹".$grand_amt."</strong>",
              'action'=>  $payment_type                       

              );

            $i++;
          //}
          //}
            
          }
        
        
      }

      $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($data),
      "iTotalDisplayRecords" => count($data),
      "aaData"=>$data);
      
    echo json_encode($results);

      
  }


   public function edit_approval_type_two() {
    $this->load->view('approval/edit_approval_type_two');
  }


  public function update_approval_type_two(){
  // echo "<pre>";
  // print_r($this->input->post());

  	// $annex_upload = $_FILES['overall_annexture']['name'];
			// 	if($annex_upload<>'')
			// 	{
			// 	$tmp_name=explode('.',$annex_upload);
			// 	$extn=end($tmp_name);
			// 	$newname=time().'.'.$extn;
			// 	$uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
			// 	move_uploaded_file($_FILES['overall_annexture']['tmp_name'], $uploadFilePath);
			// 	}else
			// 	{
			// 	$newname=$this->input->post('old_annexture');
			// 	}



    $data = array(
            // 'hpcl_location' => $this->input->post('hpcl_location'),
            'type' => $this->input->post('type'),
            'current_date' => date('Y-m-d', strtotime($this->input->post('current_date')) ),
            'customer_name' => $this->input->post('customer_name')
            // 'customer_tds' => $this->input->post('customer_tds'),
            // 'customer_tcs' => $this->input->post('customer_tcs'),
            // 'invoice'=>$this->input->post('invoice'),
            // 'invoice_date'=>date('Y-m-d',strtotime($this->input->post('invoice_date'))),
            // 'annexture_name'=>$this->input->post('annexture'),
            // 'annexture'=>$newname
           );

    $this->db->where('id', $this->uri->segment(3))
         ->update('approval_form_type_two', $data);
         // echo $this->db->last_query();
    $approval_detail_id = $this->input->post('approval_detail_id_type_two');
    $edit_hpcl_location = $this->input->post('edit_hpcl_location');
    $edit_product = $this->input->post('edit_product');
    $edit_unit = $this->input->post('edit_unit');
    // $pack_size = $this->input->post('pack_size');
    $edit_approved_price = $this->input->post('edit_approved_price');
    $edit_valid_from=$this->input->post('edit_valid_from');
    $edit_valid_to=$this->input->post('edit_valid_till');
    $edit_credit_days = $this->input->post('edit_credit_days');
    $edit_commision = $this->input->post('edit_commision');
    $edit_transportation = $this->input->post('edit_transportation');
    $edit_trate = $this->input->post('edit_trate');
    $edit_remarks = $this->input->post('edit_remarks');

    for($j = 0; $j < count($approval_detail_id); $j++) {
      if($approval_detail_id[$j] != '') {

       $datas = array(
                'location'=>$edit_hpcl_location[$j],
                'product_id' => $edit_product[$j],
                'pack_size'=>$edit_unit[$j],
                'validity_from'=>date('Y-m-d', strtotime($edit_valid_from[$j])),
                'validity_to'=>date('Y-m-d', strtotime($edit_valid_to[$j])),
                'approved_price' => $edit_approved_price[$j],
                'credit_days' => $edit_credit_days[$j],
                'commision' => $edit_commision[$j],
                'transport_type' => $edit_transportation[$j],
                'transport_rate' => $edit_trate[$j],
                'annexure' => $edit_remarks[$j],
                // 'addedOn'=>date('Y-m-d H:i:s'),
                // 'addedBy'=>$_SESSION['logged_in']['user_id'],
              );

        $edit_annex_upload = $_FILES['edit_annex_upload']['name'][$j];
        if($edit_annex_upload<>'')
        {
        $tmp_name=explode('.',$edit_annex_upload);
        $extn=end($tmp_name);
        $newname=time().$j.'.'.$extn;
        $uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
        move_uploaded_file($_FILES['edit_annex_upload']['tmp_name'][$j], $uploadFilePath);

        $datas['annexure_upload'] = $newname;
        }

        $this->db->where('id', $approval_detail_id[$j])
             ->update('approval_product_details_type_two', $datas);
       
      }
    }

      $product = $this->input->post('product');
      $unit = $this->input->post('unit');
      // $pack_size = $this->input->post('pack_size');
      $approved_price = $this->input->post('approved_price');
      $validity_from=$this->input->post('valid_from');
      $validity_to=$this->input->post('valid_till');
      $credit_days = $this->input->post('credit_days');
      $commision = $this->input->post('commision');
      $hpcl_location = $this->input->post('hpcl_location');
      $transportation = $this->input->post('transportation');
      $trate = $this->input->post('trate');
      $remarks = $this->input->post('remarks');

      for($i = 0; $i < count($product); $i++) {
        if($product[$i] != '') {

          if($transportation[$i]==1)
          {
            $trrate=$trate[$i];
          }else
          {
            $trrate=0;
          }

          $annex_upload = $_FILES['annex_upload']['name'][$i];
          if($annex_upload<>'')
          {
          $tmp_name=explode('.',$annex_upload);
          $extn=end($tmp_name);
          $newname=time().$i.'.'.$extn;
          $uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
          move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
          }else
          {
          $newname='';
          }


          $datas = array(
                  'approval_id' => $this->uri->segment(3),
                  'location'=>$hpcl_location[$i],
                  'product_id' => $product[$i],
                  'pack_size'=>$unit[$i],
                  'validity_from'=>date('Y-m-d', strtotime($validity_from[$i])),
                  'validity_to'=>date('Y-m-d', strtotime($validity_to[$i])),
                  'approved_price' => $approved_price[$i],
                  'credit_days' => $credit_days[$i],
                  'commision' => $commision[$i],
                  'transport_type' => $transportation[$i],
                  'transport_rate' => $trrate,
                  'addedOn'=>date('Y-m-d H:i:s'),
                  'addedBy'=>$_SESSION['logged_in']['user_id'],
                  'annexure' => $remarks[$i],
                  'annexure_upload'=>$newname
                );

          $this->db->insert('approval_product_details_type_two', $datas);
        }
      }

      $this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Updated.</div>');
      redirect(page_url.'Approval/edit_approval_type_two/'.$this->uri->segment(3)."/".$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7).'/'.$this->uri->segment(8));
    }

      function delete_approval_product_type_two() {
    $id = $this->input->post('id');

    $this->db->where('id', $id)
         ->delete('approval_product_details_type_two');

    if($this->db->affected_rows() > 0) {
      echo 1;
    }
  }



   function edit_transportation_based_approval() {
    $this->load->view('approval/edit_transportation_based_approval');

  }

  public function update_transportation_based_approval(){
  echo "<pre>";
  print_r($this->input->post());
    $customer_code=$this->input->post('customer_code');

    if(is_numeric($this->input->post('customer_name')))
    {
      $customer=$this->input->post('customer_name');
      // echo $customer;
    }else
    {

      $restey=$this->db->select('id')->from('hpcl_direct_customer')->where('customer_name',$this->input->post('customer_name'))->get();
      if($restey->num_rows()>0)
      {
        foreach($restey->result() as $rr);
        $customer=$rr->id;

      }else
      {
        $ddd=array('customer_name'=>$this->input->post('customer_name'),'customer_code'=>$customer_code,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
        $this->db->insert('hpcl_direct_customer',$ddd);
        $customer=$this->db->insert_id();

      }
    }

        if($this->input->post('trns_type')==1)
    {
      $vehicle_no=$this->input->post('our_vehicle_no');
      $transporter_id=0;
      $trns_rate_type=0;
      $trrate=0;

    }else
    {
      $vehicle_no='';
      $transporter_id=$this->input->post('transporter_id');
      $trns_rate_type=$this->input->post('trns_rate_type');
      $trrate=$this->input->post('trrate');
    }

    $data = array(
            'current_date' => date('Y-m-d', strtotime($this->input->post('current_date'))),
            'type'=>$this->input->post('type'),
            'hpcl_location'=>$this->input->post('hpcl_location'),
            'customer_name'=>$customer,
            'tapproval_type'=>$this->input->post('ttype'),
            'tapproval_rate'=>$this->input->post('trate'),
            'transport_done_by'=>$this->input->post('trns_type'),
            'transporter_id'=>$transporter_id,
            'vehicle_no'=>$vehicle_no,
            'transporter_rate_type'=>$trns_rate_type,
            'transporter_rate_fixed'=>$trrate
           );
    $this->db->where('id', $this->uri->segment(3))
         ->update('transportation_based_approval', $data);
echo $this->db->last_query();

    $transpotation_approval_detail_id = $this->input->post('transpotation_approval_detail_id');
    $edit_product = $this->input->post('edit_product');
    $edit_qty = $this->input->post('edit_qty');
    $edit_unit = $this->input->post('edit_unit');
    $edit_remarks = $this->input->post('edit_remarks');

    for($j = 0; $j < count($transpotation_approval_detail_id); $j++) {
      if($transpotation_approval_detail_id[$j] != '') {

        $datas = array(
                'product_id' => $edit_product[$j],
                'deliveredQty'=>$edit_qty[$j],
                'pack_size'=>$edit_unit[$j],
                'annexure' => $edit_remarks[$j],                
              );
         $edit_annex_upload = $_FILES['edit_annex_upload']['name'][$j];
        if($edit_annex_upload<>'')
        {
        $tmp_name=explode('.',$edit_annex_upload);
        $extn=end($tmp_name);
        $newname=time().$j.'.'.$extn;
        $uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
        move_uploaded_file($_FILES['edit_annex_upload']['tmp_name'][$j], $uploadFilePath);

        $datas['annexure_upload'] = $newname;
        }
           $this->db->where('id', $transpotation_approval_detail_id[$j])
             ->update('transportation_based_aprroval_product_details', $datas);
        echo $this->db->last_query();

      }
    }

    $product = $this->input->post('product');
    $unit = $this->input->post('unit');
    $qty = $this->input->post('qty');
    $annexure = $this->input->post('remarks');
    for($i = 0; $i < count($product); $i++) {
      if($product[$i] != '') {

      
        $annex_upload = $_FILES['annex_upload']['name'][$i];
        if($annex_upload<>'')
        {
        $tmp_name=explode('.',$annex_upload);
        $extn=end($tmp_name);
        $newname=time().$i.'.'.$extn;
        $uploadFilePath = SITE_ROOT.'type_two_annexure/'.basename($newname);
        move_uploaded_file($_FILES['annex_upload']['tmp_name'][$i], $uploadFilePath);
        }else
        {
        $newname='';
        }


        $datas = array(
                'approval_id' => $this->uri->segment(3),
                'product_id' => $product[$i],
                'pack_size'=>$unit[$i],
                'deliveredQty' => $qty[$i],
                'addedOn'=>date('Y-m-d H:i:s'),
                'addedBy'=>$_SESSION['logged_in']['user_id'],
                'annexure'=>$annexure[$i],
                'annexure_upload'=>$newname
              );

        $this->db->insert('transportation_based_aprroval_product_details', $datas);
        echo $this->db->last_query();
      }
    }

// exit();

    $this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
      redirect(page_url.'Approval/edit_transportation_based_approval/'.$this->uri->segment(3));

  }

    function delete_transportation_approval_product() {
    $id = $this->input->post('id');

    $this->db->where('id', $id)
         ->delete('transportation_based_aprroval_product_details');

    if($this->db->affected_rows() > 0) {
      echo 1;
    }
  }

  function pending_payments_type_1()
  {
  	$this->load->view('approval/payment_update_type_one');
  }


			function update_inventory_payment_from_collection(){

				$this->db->trans_begin();

				$flag = $this->input->post('flag');
				$inventory_id = $this->input->post('inventory_id');
				$payment = $this->input->post('payment');
				$collection_id = $this->input->post('collection_id');
				$balance_used = $this->input->post('balance_used');
				$final_balance = $this->input->post('final_balance');
				$active = $this->input->post('active');
				$pur_paymentOn = date('Y-m-d',strtotime($this->input->post('pur_paymentOn')));

				$total_payment_done=array();
				$total_payment_done[]=0;

				for($i=0;$i<count($collection_id);$i++)
				{
					$collection_ids=$collection_id[$i];
					$balance_useds=$balance_used[$i];
					$final_balances=$final_balance[$i];
					$actives=$active[$i];
					$total_payment_done[]=$balance_useds;

				if($this->input->post('payment_type') == 1){
				// $data['utr_no'] = $this->input->post('utr_no');
				$picture = $_FILES['utr_evidence']['name'];
				$newname = '';
				if($picture <> '') {
				$files = explode('.', $picture);
				$ext = end($files);
				$newname = time().'.'.$ext;
				// echo $newname;
				// exit();
				$img = move_uploaded_file($_FILES['utr_evidence']["tmp_name"],SITE_ROOT.'evidence/'.$newname);
				

				}
				}elseif($this->input->post('payment_type') == 2){
				// $data['cheque_no'] = $this->input->post('cheque_no');
				// $data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
				$picture = $_FILES['evidence']['name'];
				$newname = '';
				if($picture <> '') {
				$files = explode('.', $picture);
				$ext = end($files);
				$newname = time().'.'.$ext;
				// echo $newname;
				// exit();
				$img = move_uploaded_file($_FILES['evidence']["tmp_name"], SITE_ROOT.'evidence/'.$newname);
			

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
				

				}
				}

				//'payment_type'=>$this->input->post('payment_type'),'pur_payment_evidance'=>$newname,'cheque_date'=>date('Y-m-d', strtotime($this->input->post('cheque_date'))),'cheque_no'=>$this->input->post('cheque_no'),'utr_no'=>$this->input->post('utr_no'),

				$data=array('inventory_id'=>$inventory_id,'pur_paymentOn'=>$pur_paymentOn,'collection_id'=>$collection_ids,'collection_amount'=>$balance_useds,'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				$this->db->insert('inventory_payment_details',$data);
				$lid=$this->db->insert_id();


				/** UPDATE COLLECTION BALANCE **/
				$data3=array('balance'=>$final_balances,'active'=>$actives);
				$this->db->where('collection_id',$collection_ids);
				$this->db->update('sunder_collection_reference_balance',$data3);
				/** end **/


				/** GET ALL INDIVIDUAL INVENTORY DETAILS AND UPDATE **/
				$inventory_detailsid=$this->input->post('inventory_details_id'.$collection_ids);
				$amount_paid=$this->input->post('amount_paid'.$collection_ids);
				$paid_date=$this->input->post('paid_date'.$collection_ids);
				$portal_ref_no=$this->input->post('portal_ref_no'.$collection_ids);
				for($r=0;$r<count($inventory_detailsid);$r++)
				{
					$prd_wise_data=array('payment_id'=>$lid,'collection_id'=>$collection_ids,'inventory_details_id'=>$inventory_detailsid[$r],'amount'=>$amount_paid[$r],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'payment_date'=>date('Y-m-d',strtotime($paid_date[$r])),'portal_ref_no'=>$portal_ref_no[$r]);
					$this->db->insert('inventory_payment_details_product_wise',$prd_wise_data);

					/** CHECK IF ALL PAYMENT IS DONE **/
					$total_amount=$this->get_payment_amount($inventory_detailsid[$r]);
					$payment_made=$this->getpayment_made($inventory_detailsid[$r]);
					//$total_amount==$payment_made || 
					if(trim($payment_made)>=trim($total_amount))
					{
						$ddf=array('payment'=>1,'paymentOn'=>date('Y-m-d H:i:s'),'paymentBy'=>$_SESSION['logged_in']['user_id']);
						$this->db->where('id',$inventory_detailsid[$r]);
						$this->db->update('inventory_details',$ddf);
					}
					/** END **/
				}




				/** END **/

				}

				$total_payment_dones=array_sum($total_payment_done);
				// if($payment==$total_payment_dones)
				if(trim($total_payment_dones)>=trim($payment))
				{
					$data2 = array(
                'pur_paymentOn' => $pur_paymentOn,
                'pur_payment' => 1
              					);

					$this->db->where('id',$this->input->post('inventory_id'));
					$this->db->update('inventory',$data2);
				}


				// if($flag==1)
				// {

				// $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
				// redirect(page_url.'Approval/payment_due_today/ALL');
				// }else
				// {
				// $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
				// redirect(page_url.'Approval/upcoming_payments');
				// }


				if($this->db->trans_status() === FALSE)
				{
				$this->db->trans_rollback();
				if($flag==1)
				{

				$this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
				//redirect(page_url.'Approval/payment_due_today/ALL');
				redirect(page_url.'Approval/upcoming_payments/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
				}else
				{
				$this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
				redirect(page_url.'Approval/upcoming_payments/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
				}

				}else
				{
				$this->db->trans_commit();
				if($flag==1)
				{

				$this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
				//redirect(page_url.'Approval/payment_due_today/ALL');
					redirect(page_url.'Approval/upcoming_payments/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
				}else
				{
				$this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
					redirect(page_url.'Approval/upcoming_payments/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6));
				}


				}



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

			function get_payment_details_with_collection_ids($inventory_id)
			{

					$html='<table class="table table-bordered">
					<thead>
					<tr>
					<th style="width:20px;">Payment Date</th>
					<th style="width:100px;">Collection ID</th>
					<th style="width:30px;">Collection Amount</th>
					<th style="width:80px;">Payment Type</th>
					<th style="width:80px;">Payment Evidence</th>
					<th style="width:80px;">Cheque Date</th>
					<th style="width:80px;">Cheque No.</th>
					<th style="width:80px;">UTR No.</th>
					<th style="width:80px;">Payment Entry By</th>
					<th style="width:80px;">Entry On</th>
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
 
								$html.='<tr>
								<td>'.date('d-m-Y',strtotime($row->pur_paymentOn)).'</td>
								<td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
								<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
								<td>'.$t.'</td>
								<td><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a></td>
								<td>'.$cheque_date.'</td>
								<td>'.$cheque_no.'</td>
								<td>'.$row->utr_no.'</td>
								<td>'.$row->first_name." ".$row->last_name.'</td>
								<td>'.date('d-M-Y',strtotime($row->addedOn)).'</td>
								</tr>';


						$i++;
					}
					}else
					{

								$html.='<tr>
								<td colspan="10"></td>
								</tr>';

					}


					return $html;



			}

			function check_for_date_exceeded_payments($payment_date,$inventory_id)
			{

							$html='<table class="table table-bordered">
					<thead>
					<tr>
					<th style="width:20px;">Payment Date</th>
					<th style="width:100px;">Collection ID</th>
					<th style="width:30px;">Collection Amount</th>
					<th style="width:80px;">Payment Type</th>
					<th style="width:80px;">Payment Evidence</th>
					<th style="width:80px;">Cheque Date</th>
					<th style="width:80px;">Cheque No.</th>
					<th style="width:80px;">UTR No.</th>
					<th style="width:80px;">Payment Entry By</th>
					<th style="width:80px;">Entry On</th>
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
 

 								 if(strtotime($row->pur_paymentOn)>strtotime($payment_date))
 							 {

 							 }
 							
								$html.='<tr>
								<td>'.date('d-m-Y',strtotime($row->pur_paymentOn)).'</td>
								<td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
								<td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
								<td>'.$t.'</td>
								<td><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a></td>
								<td>'.$cheque_date.'</td>
								<td>'.$cheque_no.'</td>
								<td>'.$row->utr_no.'</td>
								<td>'.$row->first_name." ".$row->last_name.'</td>
								<td>'.date('d-M-Y',strtotime($row->addedOn)).'</td>
								</tr>';
							$i++;

						//	}
					}
					}else
					{

								$html.='<tr>
								<td colspan="10"></td>
								</tr>';

					}


					return $html;

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

  function pending_customer_payments_type_2()
  {
    $this->load->view('approval/customer_payment_update_type_two');
  }


      function update_customer_inventory_payment_from_collection(){
        $flag = $this->input->post('flag');
        $customer_id = $this->input->post('customer_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $type = $this->input->post('type');
        $inventory_id = $this->input->post('inventory_id');
        $payment = $this->input->post('payment');
        $collection_id = $this->input->post('collection_id');
        $balance_used = $this->input->post('balance_used');
        $final_balance = $this->input->post('final_balance');
        $active = $this->input->post('active');
       // $pur_paymentOn = date('Y-m-d',strtotime($this->input->post('pur_paymentOn')));
        $payment_date = $this->input->post('payment_date');

        $total_payment_done=array();
        $total_payment_done[]=0;

        for($i=0;$i<count($collection_id);$i++)
        {
          $collection_ids=$collection_id[$i];
          $balance_useds=$balance_used[$i];
          $final_balances=$final_balance[$i];
          $payment_dates=date('Y-m-d',strtotime($payment_date[$i]));
          $actives=$active[$i];
          $total_payment_done[]=$balance_useds;

        if($this->input->post('payment_type') == 1){
        // $data['utr_no'] = $this->input->post('utr_no');
        $picture = $_FILES['utr_evidence']['name'];
        $newname = '';
        if($picture <> '') {
        $files = explode('.', $picture);
        $ext = end($files);
        $newname = time().'.'.$ext;
        // echo $newname;
        // exit();
        $img = move_uploaded_file($_FILES['utr_evidence']["tmp_name"],SITE_ROOT.'evidence/'.$newname);
        

        }
        }elseif($this->input->post('payment_type') == 2){
        // $data['cheque_no'] = $this->input->post('cheque_no');
        // $data['cheque_date'] = date('Y-m-d', strtotime($this->input->post('cheque_date')));
        $picture = $_FILES['evidence']['name'];
        $newname = '';
        if($picture <> '') {
        $files = explode('.', $picture);
        $ext = end($files);
        $newname = time().'.'.$ext;
        // echo $newname;
        // exit();
        $img = move_uploaded_file($_FILES['evidence']["tmp_name"], SITE_ROOT.'evidence/'.$newname);
      

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
        

        }
        }

        $data=array('customer_id'=>$customer_id,'inventory_id'=>$inventory_id,'pur_paymentOn'=>$payment_dates,'collection_id'=>$collection_ids,'collection_amount'=>$balance_useds,'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id'],'pur_paymentOn'=>$payment_dates);
        $this->db->insert('customer_inventory_payment_details',$data);


        /** UPDATE COLLECTION BALANCE **/
        $data3=array('balance'=>$final_balances,'active'=>$actives);
        $this->db->where('collection_id',$collection_ids);
        $this->db->update('customer_collection_reference_balance',$data3);
        /** end **/


        }



        $total_payment_dones=array_sum($total_payment_done);
        if($payment==$total_payment_dones)
        {
          $data2 = array(
                'paymentOn' => $pur_paymentOn,
                'payment' => 1
                        );

          $this->db->where('id',$this->input->post('inventory_id'));
          $this->db->update('approval_product_details_type_two',$data2);
        }
                            

        if($flag==1)
        {

        $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
        redirect(page_url.'Approval/type_2_customer_payment_pending/'.$start_date.'/'.$end_date.'/'.$type);
        }else
        {
          $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
        redirect(page_url.'Approval/type_2_customer_payment_pending/'.$start_date.'/'.$end_date.'/'.$type);
        }

      

    }

    function check_for_customer_previous_payment($inventory_id)
      {
        $d=array();
        $d[]=0;
        $restey=$this->db->select('collection_amount')->from('customer_inventory_payment_details')->where('inventory_id',$inventory_id)->get();
        if($restey->num_rows()>0)
        {
          foreach($restey->result() as $row)
          {
            $d[]=$row->collection_amount;
          }

        }

        return array_sum($d);

      }

          function get_customer_payment_details_with_collection_ids($inventory_id,$customer_id)
      {

          $html='<table class="table table-bordered">
          <thead>
          <tr>
          <th style="width:20px;">Payment Date</th>
          <th style="width:100px;">Collection ID</th>
          <th style="width:30px;">Collection Amount</th>
          <th style="width:80px;">Payment Type</th>
          <th style="width:80px;">Payment Evidence</th>
          <th style="width:80px;">Cheque Date</th>
          <th style="width:80px;">Cheque No.</th>
          <th style="width:80px;">UTR No.</th>
          <th style="width:80px;">Payment Entry By</th>
          <th style="width:80px;">Entry On</th>
          </tr>
          </thead><tbody>';

          $reste=$this->db->select('a.*,b.collection_id as collection_no,c.first_name,c.last_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b','a.collection_id=b.id')->join('system_users c','a.addedBy=c.user_id')->where('a.inventory_id',$inventory_id)->where('a.customer_id',$customer_id)->get();
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
 
                $html.='<tr>
                <td>'.date('d-m-Y',strtotime($row->pur_paymentOn)) .'</td>
                <td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
                <td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
                <td>'.$t.'</td>
                <td><a href="'.site_http_root.'evidence/'.$row->pur_payment_evidance.'" class="btn btn-success btn-xs" download target="_blank" >Download Evidence</a></td>
                <td>'.$cheque_date.'</td>
                <td>'.$cheque_no.'</td>
                <td>'.$row->utr_no.'</td>
                <td>'.$row->first_name." ".$row->last_name.'</td>
                <td>'.date('d-M-Y',strtotime($row->addedOn)).'</td>
                </tr>';


            $i++;
          }
          }else
          {

                $html.='<tr>
                <td colspan="10"></td>
                </tr>';

          }


          return $html;



      }

      function get_payment_amount($in_detail_id)
      {
					$pur_amount=0;
					$ressst=$this->db->select('a.id,a.qty,a.rate,b.gst')->from('inventory_details a')->join('inventory b','a.inventory_id=b.id')->where('a.id',$in_detail_id)->get();
					if($ressst->num_rows()>0)
					{
						foreach($ressst->result() as $invrow);
						$totalval=$invrow->qty*$invrow->rate;
						$gst_v=$invrow->gst/100;
						$gst_val=$totalval*$gst_v;
						$pur_amount=$totalval+$gst_val;
					}

					return $pur_amount;

      }

      function getpayment_made($in_detail_id)
      {
      	$pay_made=0;
      	$resty=$this->db->select('sum(amount) as totalpaid')->from('inventory_payment_details_product_wise')->where('inventory_details_id',$in_detail_id)->get();
      	if($resty->num_rows()>0)
      	{
      		foreach($resty->result() as $row);
      		if($row->totalpaid<>'' && $row->totalpaid<>0)
      		{
      			$pay_made=$row->totalpaid;
      		}
      	}

      	return $pay_made;

      }

     
     	function edit_approval_wise_delivery_detail()
	{

		$this->load->view('approval/edit_approval_wise_item_delivery');
	}



	function update_item_delivery_detail_type_two()
	{
		$approval_id=$this->uri->segment('3');
		// $interest=$this->input->post('interest');
		// $gst=$this->input->post('gst');
		$current_date=$this->input->post('current_date');
		$qty=$this->input->post('qty');
		$transportation=$this->input->post('transportation');
		if($transportation==1){
			$trns_type=$this->input->post('trns_type');
			if($trns_type==1)
			{
				$our_vehicle_no=$this->input->post('our_vehicle_no');
			}else if($trns_type==2)
			{
				$transporter_id=$this->input->post('transporter_id');
				$trns_rate_type=$this->input->post('trns_rate_type');
				$trrate=$this->input->post('trrate');
				$our_vehicle_no='';
			}else{
				$transporter_id=0;
				$trns_rate_type=0;
				$trrate=0;
				$our_vehicle_no='';
			}

			/** INSERT **/
			$dd=array('deliveredOn'=>$current_date,'deliveredQty'=>$qty,'transporter_from'=>$transportation,'vehicle_type'=>$trns_type,'transporter_id'=>$transporter_id,'vehicle_no'=>$our_vehicle_no,'transporter_rate_type'=>$trns_rate_type,'transporter_fixed_rate'=>$trrate);
				$this->db->where('id',$approval_id);
				$this->db->update('approval_product_details_type_two',$dd);
		}else
		{
			$trns_type=0;

			$dd=array('deliveredOn'=>$current_date,'deliveredQty'=>$qty);
			$this->db->where('id',$approval_id);
			$this->db->update('approval_product_details_type_two',$dd);

			/** INSERT **/
		}
		

		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
		redirect(page_url.'Approval/edit_approval_wise_delivery_detail/'.$approval_id);

		
	}

	function filter_type_two_payment() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$type = base64_encode($this->input->post('type'));
	 	redirect(page_url.'Approval/type_2_customer_payment_pending/'.$from_date.'/'.$to_date.'/'.$type);

	 	
	}


	  function get_customer_payment_details_with_collection_ids_new($inventory_id,$customer_id)
      {

          $html='<table class="table table-bordered">
          <thead>
          <tr>
          <th style="width:20px;">Payment Date</th>
          <th style="width:100px;">Collection ID</th>
          <th style="width:30px;">Collection Amount</th>
     
          </tr>
          </thead><tbody>';

          $reste=$this->db->select('a.*,b.collection_id as collection_no,c.first_name,c.last_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b','a.collection_id=b.id')->join('system_users c','a.addedBy=c.user_id')->where('a.inventory_id',$inventory_id)->where('a.customer_id',$customer_id)->get();
          if($reste->num_rows()>0)
          {
            $i=1;
            foreach($reste->result() as $row)
            {
             
                $html.='<tr>
                <td>'.date('d-m-Y',strtotime($row->pur_paymentOn)) .'</td>
                <td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
                <td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
                </tr>';


            $i++;
          }
          }else
          {

                $html.='<tr>
                <td colspan="3"></td>
                </tr>';

          }


          return $html;



      }


      	public function type_two_approval_list_upload_invoice() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_two_invoice_upload', $data);
	}

	function type_2_3_invoice_upload_form()
	{
		$this->load->view('approval/type_2_3_invoice_upload_form');
	}

	function save_invoice_type_2_3()
	{
	

			$attach = $_FILES['attach']['name'];
			if($attach<>'')
			{
			$tmp_name=explode('.',$attach);
			$extn=end($tmp_name);
			$newname=time().'.'.$extn;
			$uploadFilePath = SITE_ROOT.'type_2_3_invoice/'.basename($newname);
			move_uploaded_file($_FILES['attach']['tmp_name'], $uploadFilePath);
			}else
			{
			$newname='';
			}


			$vehicle_no='';
			$transporter_id=0;
			$mobile_no=0;
			$address='';
			$trns_rate_type=0;
			$trrate=0;
			if($this->input->post('transportation')==1)
			{	
						$trns_type=$this->input->post('trns_type');

				if($trns_type==1)
				{
						$vehicle_no=$this->input->post('our_vehicle_no');

				}else
				{
						$vehicle_no=$this->input->post('vehicle_no');

							$sql = $this->db->select('id')
							->from('transporter_details')
							->where('id', $this->input->post('transporter_name'))
							->get();
							if($sql->num_rows() == 0) {
							$datas = array(
							'name' => $this->input->post('transporter_id'),
							'mobile_no' => $this->input->post('mobile_no'),
							'address' => $this->input->post('address')
							);
							$this->db->insert('transporter_details', $datas);
							$transporter_id = $this->db->insert_id();
							}else
							{
							$transporter_id=$this->input->post('transporter_name');
							}


						$transporter_id=$transporter_id;
						$mobile_no=$this->input->post('mobile_no');
						$address=$this->input->post('address');
						$trns_rate_type=$this->input->post('trns_rate_type');
						$trrate=$this->input->post('trrate');
				}

			}


		$data=array('type'=>$this->input->post('type'),
								'invoice_date'=>$this->input->post('invoice_date'),
								'invoice_no'=>$this->input->post('invoice_number'),
								'invoice'=>$newname,
								'shipping_from'=>$this->input->post('hpcl_location'),
								'customer'=>$this->input->post('customer_name'),
								'tds'=>$this->input->post('customer_tds'),
								'tcs'=>$this->input->post('customer_tcs'),
								'transportation'=>$this->input->post('transportation'),
								'transport_type'=>$this->input->post('trns_type'),
								'vehicle_no'=>$vehicle_no,
								'transporter_id'=>$transporter_id,
								'mobile_no'=>$mobile_no,
								'address'=>$address,
								'rate_type'=>$trns_rate_type,
								'transport_rate'=>$trrate,
								'gst'=>$this->input->post('gst'),
								'interest'=>$this->input->post('interest'),
								'addedOn'=>date('Y-m-d H:i:s'),
								'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('type_2_3_invoice',$data);
								$lid=$this->db->insert_id();

						for($i=0;$i<count($this->input->post('product'));$i++)
						{

								$prdid=$_REQUEST['product'][$i];
								$unit=$_REQUEST['unit'][$i];
								$approved_price=$_REQUEST['approved_price'][$i];
								$qty=$_REQUEST['qty'][$i];
								$dd=array('invoice_id'=>$lid,'product_id'=>$prdid,'unit'=>$unit,'price'=>$approved_price,'qty'=>$qty,'addedOn'=>date('Y-m-d H:i:s'),
								'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->insert('type_2_3_invoice_particular',$dd);

						}


						$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
						redirect(page_url.'Approval/type_2_3_invoice_upload_form');
	}


	function type_two_three_invoices_list()
	{


		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$location =$this->uri->segment(5);
		$customer =$this->uri->segment(6);
		$type =$this->uri->segment(7);
		$query=$this->db->select('a.*,c.name as location_name,e.customer_name')
					    ->from('type_2_3_invoice a')
					   	->join('hpcl_location c', 'c.id=a.shipping_from')
					   	->join('hpcl_direct_customer e', 'e.id=a.customer')
					    ->where('a.invoice_date>=',$start_date)
					    ->where('a.invoice_date<=',$end_date);
							if($type<>'' && $type<>'ALL')
							{
								if($type==2)
								{
									$this->db->where('a.type','TYPE II');
								}else 
								{
									$this->db->where('a.type','TYPE III');
								}
							
							}

							if($location<>'' && $location<>'ALL')
							{
									$this->db->where('a.shipping_from',$location);
							}

							if($customer<>'' && $customer<>'ALL')
							{
									$this->db->where('a.customer',$customer);
							}


					  $this->db->order_by('a.invoice_date','ASC');
					  $query=$this->db->get();

					if($query->num_rows()>0) {
		
			foreach($query->result() as $rows)
			{
					
				
					$prd=$this->get_type_2_3_invoice_particular($rows->id);
					$trans=$this->get_type_2_3_transporter_detail($rows->id);
					$prd_detail=explode('|',$prd);

					 $amount=$prd_detail[1];
					 $gst=($amount*$rows->gst)/100;
					 $final_base_amount=$amount+$gst;

					/** TDS ON BASIC AMOUNT **/
					if($rows->tds>0)
					{
					$tds=$rows->tds;
					$tds_amount=$amount*($tds/100);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->tcs;
           $tcs_amt = $final_base_amount*$rows->tcs/100;
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
       			$prev_payment=0;
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

     //           $payment="<a href='".page_url."Approval/pending_customer_payments_type_2/".$rows->product_approval_id."/".base64_encode($final_payable_amount)."/1/".$rows->customer_id."/".$start_date."/".$end_date."/".base64_encode($type)."' class='btn btn-warning'>Update Payment Status</a>";
			
			$action = "<a href='".page_url."Approval/edit_type_two_invoice_upload/".$rows->id."'><i class='fa fa-pencil'></i></a>";	

				$base_amount=$prd_detail[1];

						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$prd_detail[0],
						'transportation_detail' =>$trans,
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tds)." %</strong>",
						'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
            'tcs' => "<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tcs)." %</strong>",
            'tcs_amt' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tcs_amt."</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$prd_detail[1]."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_base_amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst."</strong>",
						'tamount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tinvoice_value."</strong>",
            'prev_payment' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹</strong>",
            'final_payable_amount' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_payable_amount."</strong>",
						'action' =>$action
					
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

	function get_type_2_3_transporter_detail($invoice_id)
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
					 $html.='<tr>';
					 $html.='<td colspan="5">No Data Available</td>';
					 $html.='</tr>';

				}

				return $html;

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

	function filter_type_2_3_invoices()
	{
		$type=$this->input->post('type');
		$from_date=date('Y-m-d',strtotime($this->input->post('from_date')));
		$to_date=date('Y-m-d',strtotime($this->input->post('to_date')));
		$hpcl_locations=$this->input->post('hpcl_locations');
		$customer=$this->input->post('customer');

		redirect(page_url.'Approval/type_two_approval_list_upload_invoice/'.$from_date."/".$to_date."/".$hpcl_locations."/".$customer."/".$type);
	}

	function type_2_customer_payment_pending_new()
	{
		$this->load->view('approval/pending_payment_customer_type2_new');
	}


	function type_two_three_invoices_pending_payment()
	{
		$data = array();
		$i=1;
		$location =$this->uri->segment(3);
		$customer =$this->uri->segment(4);
		$type =$this->uri->segment(5);
		$query=$this->db->select('d.id as product_invoice_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,f.instruments_name,f.unit as productunit')
							->from('type_2_3_invoice_particular d')
					    ->join('type_2_3_invoice a','d.invoice_id=a.id')
					    ->join('presto_instruments f','f.id=d.product_id')
					   	->join('hpcl_location c', 'c.id=a.shipping_from')
					   	->join('hpcl_direct_customer e', 'e.id=a.customer')
					   	->where('d.payment',0);
					    // ->where('a.invoice_date>=',$start_date)
					    // ->where('a.invoice_date<=',$end_date);
							if($type<>'' && $type<>'ALL')
							{
								if($type==2)
								{
									$this->db->where('a.type','TYPE II');
								}else 
								{
									$this->db->where('a.type','TYPE III');
								}
							
							}

							if($location<>'' && $location<>'ALL')
							{
									$this->db->where('a.shipping_from',$location);
							}

							if($customer<>'' && $customer<>'ALL')
							{
									$this->db->where('a.customer',$customer);
							}


					  $this->db->order_by('a.invoice_date','ASC');
					  $query=$this->db->get();

					if($query->num_rows()>0) {
		
			foreach($query->result() as $rows)
			{
					
				
					// $prd=$this->get_type_2_3_invoice_particular($rows->id);
					
					// $prd_detail=explode('|',$prd);

					 $amount=$rows->pqty*$rows->pprrice;
					 $gst=round(($amount*$rows->gst)/100,2);
					 $final_base_amount=round($amount+$gst,2);

					/** TDS ON BASIC AMOUNT **/
					if($rows->tds>0)
					{
					$tds=$rows->tds;
					$tds_amount=round($amount*($tds/100),2);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->tcs;
           $tcs_amt = round($final_base_amount*$rows->tcs/100,2);
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
       			$prev_payment=0;
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

					$base_amount=round($rows->pqty*$rows->pprrice,2);

					$prev_paid=round($this->salescrm->previous_paid_type_2_3($rows->product_invoice_id,$rows->customer),2);

					$balance_payable=round($final_payable_amount-$prev_paid,2);

          $payment="<a href='".page_url."Approval/pending_customer_payments_type_2_new/".$rows->product_invoice_id."/".base64_encode($balance_payable)."/1/".$rows->customer."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."' class='btn btn-warning'>Update Payment Status</a>";

          if($prev_paid>0)
          {
          $close_payment="<a href='javascript:;' class='btn btn-danger' onclick='close_manual_payment(".$rows->product_invoice_id.");'>Close Payment Manually</a>";
        	}else
        	{
        		$close_payment='';
        	}
				



						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$rows->instruments_name,
						'product_qty' =>$rows->pqty." ".$rows->productunit,
						'product_rate' =>$rows->pprrice,
						'total' =>$amount,
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tds)." %</strong>",
						'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
            'tcs' => "<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tcs)." %</strong>",
            'tcs_amt' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tcs_amt."</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$rows->pqty*$rows->pprrice."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_base_amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst."</strong>",
						'tamount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tinvoice_value."</strong>",
            'prev_payment' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹</strong>",
            'final_payable_amount' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_payable_amount."</strong>",
            'alreadypaid' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$prev_paid."</strong>",
            'balancepayable' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$balance_payable."</strong>",
						'action' =>$payment,
						'closepayment'=>$close_payment
					
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

	function filter_type_2_3_pending_payment()
	{
		$type=$this->input->post('type');
		$hpcl_locations=$this->input->post('hpcl_locations');
		$customer=$this->input->post('customer');

		redirect(page_url.'Approval/type_2_customer_payment_pending_new/'.$hpcl_locations."/".$customer."/".$type);
	}

	 function pending_customer_payments_type_2_new()
  {
    $this->load->view('approval/customer_payment_update_type_two_new');
  }


  function update_customer_inventory_payment_from_collection_new(){
        $flag = $this->input->post('flag');
        $customer_id = $this->input->post('customer_id');
        $flag1 = $this->input->post('flag1');
        $flag2 = $this->input->post('flag2');
        $flag3 = $this->input->post('flag3');
        $inventory_id = $this->input->post('inventory_id');
        $payment = $this->input->post('payment');
        $collection_id = $this->input->post('collection_id');
        $balance_used = $this->input->post('balance_used');
        $final_balance = $this->input->post('final_balance');
        $active = $this->input->post('active');
        $payment_date = $this->input->post('payment_date');

        $total_payment_done=array();
        $total_payment_done[]=0;

        for($i=0;$i<count($collection_id);$i++)
        {
          $collection_ids=$collection_id[$i];
          $balance_useds=$balance_used[$i];
          $final_balances=$final_balance[$i];
          $payment_dates=date('Y-m-d',strtotime($payment_date[$i]));
          $actives=$active[$i];
          $total_payment_done[]=$balance_useds;

        $data=array('customer_id'=>$customer_id,'inventory_id'=>$inventory_id,'pur_paymentOn'=>$payment_dates,'collection_id'=>$collection_ids,'collection_amount'=>$balance_useds,'addedOn'=>date('Y-m-d'),'addedBy'=>$_SESSION['logged_in']['user_id'],'pur_paymentOn'=>$payment_dates);
        $this->db->insert('customer_inventory_payment_details',$data);


        /** UPDATE COLLECTION BALANCE **/
        $data3=array('balance'=>$final_balances,'active'=>$actives);
        $this->db->where('collection_id',$collection_ids);
        $this->db->update('customer_collection_reference_balance',$data3);
        /** end **/


        }

        $total_payment_dones=array_sum($total_payment_done);
        if($payment==$total_payment_dones)
        {
          $data2 = array(
                'payment_date' => $pur_paymentOn,
                'payment' => 1,
                'paymentBy'=>$_SESSION['logged_in']['user_id']
                        );

          $this->db->where('id',$this->input->post('inventory_id'));
          $this->db->update('type_2_3_invoice_particular',$data2);
        }
                            

        if($flag==1)
        {
        $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
        redirect(page_url.'Approval/type_2_customer_payment_pending_new/'.$flag1.'/'.$flag2.'/'.$flag3);
        }else
        {
          $this->session->set_flashdata('message','<div class="alert alert-success">Payment Uploaded Successfully.</div><br/>');
        redirect(page_url.'Approval/type_2_customer_payment_pending_new/'.$flag1.'/'.$flag2.'/'.$flag3);
        }

      

    }

    function type_2_3_payment_history()
    {
    	$this->load->view('approval/pending_payment_customer_type2_new_history');
    }



    function type_two_three_invoices_pending_payment_history()
	{
		$data = array();
		$i=1;
		$location =$this->uri->segment(3);
		$customer =$this->uri->segment(4);
		$type =$this->uri->segment(5);
		$query=$this->db->select('d.manual_close,d.manual_closeBy,d.manual_closeOn,d.manual_close_remarks,d.id as product_invoice_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,f.instruments_name,f.unit as productunit')
							->from('type_2_3_invoice_particular d')
					    ->join('type_2_3_invoice a','d.invoice_id=a.id')
					    ->join('presto_instruments f','f.id=d.product_id')
					   	->join('hpcl_location c', 'c.id=a.shipping_from')
					   	->join('hpcl_direct_customer e', 'e.id=a.customer')
					   	->where('d.payment',1);
					    // ->where('a.invoice_date>=',$start_date)
					    // ->where('a.invoice_date<=',$end_date);
							if($type<>'' && $type<>'ALL')
							{
								if($type==2)
								{
									$this->db->where('a.type','TYPE II');
								}else 
								{
									$this->db->where('a.type','TYPE III');
								}
							
							}

							if($location<>'' && $location<>'ALL')
							{
									$this->db->where('a.shipping_from',$location);
							}

							if($customer<>'' && $customer<>'ALL')
							{
									$this->db->where('a.customer',$customer);
							}


					  $this->db->order_by('a.invoice_date','ASC');
					  $query=$this->db->get();

					if($query->num_rows()>0) {
		
			foreach($query->result() as $rows)
			{
					
				
					// $prd=$this->get_type_2_3_invoice_particular($rows->id);
					
					// $prd_detail=explode('|',$prd);

					 $amount=$rows->pqty*$rows->pprrice;
					 $gst=round(($amount*$rows->gst)/100,2);
					 $final_base_amount=round($amount+$gst,2);

					/** TDS ON BASIC AMOUNT **/
					if($rows->tds>0)
					{
					$tds=$rows->tds;
					$tds_amount=round($amount*($tds/100),2);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->tcs;
           $tcs_amt = round($final_base_amount*$rows->tcs/100,2);
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
       			$prev_payment=0;
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

					$base_amount=round($rows->pqty*$rows->pprrice,2);

					$prev_paid=round($this->salescrm->previous_paid_type_2_3($rows->product_invoice_id,$rows->customer),2);

					$balance_payable=round($final_payable_amount-$prev_paid,2);

          $payment="<a href='".page_url."Approval/pending_customer_payments_type_2_new/".$rows->product_invoice_id."/".base64_encode($balance_payable)."/1/".$rows->customer."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."' class='btn btn-warning'>Update Payment Status</a>";
				
					$collection_dettails=$this->get_customer_payment_details_with_collection_ids_new_type_2_3($rows->product_invoice_id,$rows->customer);

					if($rows->manual_close==1)
					{
						$m="Yes<br/>";
						$m.=date('d-M-Y',strtotime($rows->manual_closeOn))."<br/><br/>";
						$m.="<strong>".$rows->manual_close_remarks."</strong><br/><br/>";
						$m.=$this->salescrm->getusername($rows->manual_closeBy);
					}else
					{
						$m='';
					}



						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$rows->instruments_name,
						'product_qty' =>$rows->pqty." ".$rows->productunit,
						'product_rate' =>$rows->pprrice,
						'total' =>$amount,
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tds)." %</strong>",
						'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
            'tcs' => "<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($rows->tcs)." %</strong>",
            'tcs_amt' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tcs_amt."</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$rows->pqty*$rows->pprrice."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_base_amount."</strong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$gst."</strong>",
						'tamount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$tinvoice_value."</strong>",
            'prev_payment' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹</strong>",
            'final_payable_amount' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$final_payable_amount."</strong>",
            'alreadypaid' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$prev_paid."</strong>",
            'balancepayable' => "<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$balance_payable."</strong>",
						'action' =>$collection_dettails,
						'mclose' =>$m
					
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


	 function get_customer_payment_details_with_collection_ids_new_type_2_3($inventory_id,$customer_id)
      {

          $html='<table class="table table-bordered">
          <thead>
          <tr>
          <th style="width:20px;">Payment Date</th>
          <th style="width:100px;">Collection ID</th>
          <th style="width:30px;">Collection Amount</th>
          <th style="width:80px;">Payment Entry By</th>
          <th style="width:80px;">Entry On</th>
          </tr>
          </thead><tbody>';

          $reste=$this->db->select('a.*,b.collection_id as collection_no,c.first_name,c.last_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b','a.collection_id=b.id')->join('system_users c','a.addedBy=c.user_id')->where('a.inventory_id',$inventory_id)->where('a.customer_id',$customer_id)->get();
          if($reste->num_rows()>0)
          {
            $i=1;
            foreach($reste->result() as $row)
            {
            	if($_SESSION['logged_in']['role']==1)
            	{
            		$d='"'.$row->pur_paymentOn.'"';
            		$a="<a href='javascript:;' onclick='edit_record(".$row->id.",".$d.")'><i class='fa fa-pencil'></i></a>";
            	}
              
                $html.='<tr>
                <td>'.date('d-m-Y',strtotime($row->pur_paymentOn)) .'<br/>'.$a.'</td>
                <td><strong style="font-size: 18px;color:red;font-weight: bold;">'.$row->collection_no.'</strong></td>
                <td><strong style="font-size: 15px;color:red;font-weight: bold;">₹'.$row->collection_amount.'</strong></td>
                <td>'.$row->first_name." ".$row->last_name.'</td>
                <td>'.date('d-M-Y',strtotime($row->addedOn)).'</td>
                </tr>';
            $i++;
          }
          }else
          {

                $html.='<tr>
                <td colspan="5"></td>
                </tr>';

          }


          return $html;



      }

      function filter_type_2_3_done_payment()
	{
		$type=$this->input->post('type');
		$hpcl_locations=$this->input->post('hpcl_locations');
		$customer=$this->input->post('customer');

		redirect(page_url.'Approval/type_2_3_payment_history/'.$hpcl_locations."/".$customer."/".$type);
	}



	function this_month__cr_note_claim_list_t2_new() {

		$data = array();
		$total_arr=array();
		$total_arr[]=0;
		$i=1;
		$start_date =$this->uri->segment(3);
		$end_date =$this->uri->segment(4);
		$location =$this->uri->segment(5);
		$product =$this->uri->segment(6);
		$query=$this->db->select('a.type,a.customer,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_invoice_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,f.instruments_name,f.unit as productunit')
							->from('type_2_3_invoice_particular d')
					    ->join('type_2_3_invoice a','d.invoice_id=a.id')
					    ->join('presto_instruments f','f.id=d.product_id')
					   	->join('hpcl_location c', 'c.id=a.shipping_from')
					   	->join('hpcl_direct_customer e', 'e.id=a.customer')
					    ->where('a.invoice_date>=',$start_date)
					    ->where('a.invoice_date<=',$end_date);
					    //->where('d.id',43);
							// if($type<>'' && $type<>'ALL')
							// {
							// 	if($type==2)
							// 	{
							// 		$this->db->where('a.type','TYPE II');
							// 	}else 
							// 	{
							// 		$this->db->where('a.type','TYPE III');
							// 	}
							
							// }

							if($location<>'' && $location<>'ALL')
							{
									$this->db->where('a.shipping_from',$location);
							}

							if($product<>'' && $product<>'ALL')
							{
									$this->db->where('d.product_id',$product);
							}


					  $this->db->order_by('a.invoice_date','ASC');
					  $query=$this->db->get();

					if($query->num_rows()>0) {
		
			foreach($query->result() as $rows)
			{
					
				
					// $prd=$this->get_type_2_3_invoice_particular($rows->id);
					
					// $prd_detail=explode('|',$prd);

					 $amount=$rows->pqty*$rows->pprrice;
					 $total_rate=$rows->pqty*$rows->pprrice;

					 $gst=($amount*$rows->gst)/100;
					 $final_base_amount=$amount+$gst;

					/** TDS ON BASIC AMOUNT **/
					if($rows->tds>0)
					{
					$tds=$rows->tds;
					$tds_amount=$amount*($tds/100);
					//$final_amount=$final_base_amount;
					}else
					{
						$tds="NA";
						$tds_amount=0;
						//$final_amount=$final_base_amount;
					}
					/** END **/

					/** TCS ON INCLUDING TAX VALUE **/
					   $tcs = $rows->tcs;
           $tcs_amt = $final_base_amount*$rows->tcs/100;
              if ($tcs_amt > 0) {
                $final_amount = $final_base_amount + $tcs_amt;
              }
          /** END **/

       
       			$prev_payment=0;
            $tinvoice_value=$final_base_amount-$tds_amount+$tcs_amt;
            $final_payable_amount = $tinvoice_value-$prev_payment;

					$base_amount=$rows->pqty*$rows->pprrice;

					$prev_paid=$this->salescrm->previous_paid_type_2_3($rows->product_invoice_id,$rows->customer);

					$balance_payable=$final_payable_amount-$prev_paid;

          $payment="<a href='".page_url."Approval/pending_customer_payments_type_2_new/".$rows->product_invoice_id."/".base64_encode($balance_payable)."/1/".$rows->customer."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."' class='btn btn-warning'>Update Payment Status</a>";
				
					$collection_dettails=$this->get_customer_payment_details_with_collection_ids_new_type_2_3($rows->product_invoice_id,$rows->customer);


					if($rows->payment==1)
					{
					 $pay="<strong style='color:green;font-size:18px;font-weight:bold;'>Completed</strong>";
					}else
					{
						$pay="<strong style='color:red;font-size:18px;font-weight:bold;'>Pending</strong>";
					}


					if($rows->payment==1)
					{
						$app=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$rows->invoice_date);
						$appro=explode('|',$app);

						$commision=$rows->pqty*$appro[1];
						$transportation=$rows->pqty*$appro[2];
						$total=$commision+$transportation;
						$appro_app=$appro[0];
						$comm=$appro[1];
					}else
					{
						$commision=0;
						$transportation=0;
						$total=0;
						$appro_app="";
						$comm=0;
					}
						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$rows->instruments_name,
						'product_qty' =>$rows->pqty." ".$rows->productunit,
						'product_rate' =>$rows->pprrice,
						'total_r' =>$amount,
					  'balancepayable' =>$pay,
						'approval_applicable' =>$appro_app,
						'commission' =>"<strong style='color:green;font-size:18px;font-weight:bold;'>₹".$commision."</strong>",
						'transportation' =>"<strong style='color:green;font-size:18px;font-weight:bold;'>₹".$transportation."</strong>",
						'total' =>"<strong style='color:green;font-size:18px;font-weight:bold;'>₹".$total."</strong>",
						'error' =>''
					
						);

						//<br/>".$rows->pqty."<br/><br/>".$comm
						$total_arr[]=$total;
						$i++;
			}
						

			}
				
			
			//	echo array_sum($total_arr); exit;
	
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData"=>$data);
			
		echo json_encode($results);
	}
	

	function pending_transporter_payment_list_t2_new() {

		 $data = array();
		$i=1;
		$party = $this->uri->segment(3);
		$location = $this->uri->segment(4);
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
			


				$payment='<a href="javascript:;" class="btn btn-warning" onclick="payment_done('.$rows->id.');">Update Payment</a>';
						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$prd_detail[0],
						'transportation_detail' =>$trans_detail[0],
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($trans_detail[3])." %</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$base_amount."</strong>",
					 'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$gstamt."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$finalbase."</strong>",
						'action' =>$payment
					
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


	public function type_two_approval_list_trial() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_two_approval_list_trial', $data);
	}



function type_two_approval_listing_trial_run() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
		$hpcl_location = $this->uri->segment(5);
		$product = $this->uri->segment(6);
		$transport_type = $this->uri->segment(7);

		         $this->db->select('a.invoice_date,a.invoice,a.annexture_name,a.annexture,a.id,a.type, a.auto_gen_code, a.current_date,b.customer_name,a.customer_name as custid')
						  ->from('approval_form_type_two a')
						  ->join('hpcl_direct_customer b','a.customer_name=b.id');

			if($start_date <> '' && $end_date <> '') {
				 $this->db->where('a.current_date >=', $start_date);
				 $this->db->where('a.current_date <=', $end_date);
			}

		

		$query = $this->db->get();

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
				


				

					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_type_two_trial($row->id,$hpcl_location,$product,$transport_type,$row->custid);
 					if($getApprovalProductDetails!="NA") {
						 $edit = "<a href='".page_url."Approval/edit_approval_type_two/".$row->id."/".$start_date."/".$end_date."/".$hpcl_location."/".$product."/".$transport_type."' class='btn btn-xs btn-default'>Edit Approval</a>";
						 $annexture_file=page_url1.'type_two_annexure/'.$row->annexture; 

						 if($row->invoice_date<>'' && $row->invoice_date<>'0000-00-00'){ 

						 	$in_date=date('d-M-Y',strtotime(($row->invoice_date)));

						}else
						 {

						 	$in_date='';

						 }
					$data[] = array(
							'sr_no' => $i,
							'current_date' => $row->type."<br/>".date('d-m-Y', strtotime($row->current_date)),
							'customer_name' =>$row->customer_name,
							'invoice'=>$row->invoice,
							'invoice_date'=>$in_date,
							'annexture'=>'<a href="'.$annexture_file.'">'.$row->annexture_name.'</a>',
							'hpcl_location' => '',
							'product_details' =>$getApprovalProductDetails,
							
							'item_delivered' => '',
							'edit' => $edit
						
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

	function filter_type_two_approval_trail() {
	 	$from_date = date('Y-m-d',strtotime($this->input->post('from_date')));
	 	$to_date = date('Y-m-d',strtotime($this->input->post('to_date')));
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');
	 	$transportation = $this->input->post('transportation');

	 	redirect(page_url.'Approval/type_two_approval_list_trial/'.$from_date.'/'.$to_date.'/'.$hpcl_locations.'/'.$products.'/'.$transportation);

	 	
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
					$transporter='';
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

				return $html."|".$rate_type."|".$trate."|".$tds."|".$gst."|".$transporter;

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

	

		function get_transporter_payment_detailsNew()
	{

		$invoice_id=$this->uri->segment(3);
		$tname='';

  $data = array();
	$data[]=0;
	$i=1;
	$this->db->select('a.*,c.name as location_name,e.customer_name')
	->from('type_2_3_invoice a')
	->join('hpcl_location c', 'c.id=a.shipping_from')
	->join('hpcl_direct_customer e', 'e.id=a.customer')
	->where('a.transporter_payment',0)
	->where('a.transport_type',2)
	->where('a.id',$invoice_id);
	$this->db->order_by('a.invoice_date','ASC');
	$query=$this->db->get();
	if($query->num_rows()>0) {

			foreach($query->result() as $rows);
								
					$prd=$this->get_type_2_3_qty_detail($rows->id);
					$prd_detail=explode('|',$prd);
					$qty=$prd_detail[1];

					$trans=$this->get_type_2_3_transporter_detail_new($rows->id);
					$trans_detail=explode('|',$trans);
					$tname=$trans_detail[5];
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
							$t_type_rate=$trans_detail[2]."/".$rows->unit;
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

			
						

			}
				
			
	
	
			
		echo  array_sum($data)."|".$tname;

	}


	function update_payment_transporter_type_two()
	{
	
		$invoice_id=$this->input->post('approval_id');
		$payment_date=$this->input->post('payment_date');
		$paid_amount=$this->input->post('paid_amount');
		$flag1=$this->input->post('flag1');
		$flag2=$this->input->post('flag2');

		$name=$_FILES['evidance']['name'];
		if($name<>'')
		{
		$tmp_name=explode('.',$name);
		$extn=end($tmp_name);
		$newname=time().'.'.$extn;
		$uploadFilePath = SITE_ROOT.'transporter_payment/'.basename($newname);
		move_uploaded_file($_FILES['evidance']['tmp_name'], $uploadFilePath);
		}else
		{
			$newname='';
		}

		$dd=array('transporter_payment'=>1,'paidOn'=>date('Y-m-d H:i:s'),'paid_amount'=>$paid_amount,'transporter_paymentEvidence'=>$newname,'paidBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$invoice_id);
		$this->db->update('type_2_3_invoice',$dd);
		$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
     	redirect(page_url.'Approval/pending_transporters_payment/'.$flag1.'/'.$flag2);


	}



function pending_transporter_payment_list_t2_new_history() {

		 $data = array();
		$i=1;
		$party = $this->uri->segment(3);
		$location = $this->uri->segment(4);
		$this->db->select('a.*,c.name as location_name,e.customer_name')
		->from('type_2_3_invoice a')
		->join('hpcl_location c', 'c.id=a.shipping_from')
		->join('hpcl_direct_customer e', 'e.id=a.customer')
		->where('a.transporter_payment',1)
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
							$t_type_rate=$trans_detail[2]."/".$rows->unit;
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
			

					
						$payment='Paid Amount: '.$rows->paid_amount."<br/><br/>"."Paid By: ".$this->salescrm->getusername($rows->paidBy)."<br/><br/>Paid On: ".date('d-M-Y H:i',strtotime($rows->paidOn))."<br/><br/>Evidence: <a href='".page_url1."transporter_payment/".$rows->transporter_paymentEvidence."' download>Click Here</a>";

						$data[] = array(
						'sr_no' => $i,
						'type' => $rows->type,
						'current_date' => date('d-M-Y',strtotime($rows->invoice_date))."<br/>".$rows->invoice_no."<br/><a href='".page_url1."type_2_3_invoice/".$rows->invoice."' download>Download</a>",
						'customer_name' => $rows->customer_name,
						'location' => $rows->location_name,
						'product_name' =>$prd_detail[0],
						'transportation_detail' =>$trans_detail[0],
						'tds' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".floatval($trans_detail[3])." %</strong>",
						'amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>₹".$base_amount."</strong>",
					 'tds_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$tds_amount."</stong>",
						'gst' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$gstamt."</strong>",
						'final_base_amount' =>"<strong style='color:red;font-size:18px;font-weight:bold;'>".$finalbase."</strong>",
						'action' =>$payment
					
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

public function edit_type_two_invoice_upload(){
	$this->load->view('approval/edit_type_2_3_invoice_upload_form');
}

public function deleteproductinfo(){
	$id = $this->uri->segment(3);
	$recordid = $this->uri->segment(4);

	$this->db->where('id',$id);
	$this->db->delete('type_2_3_invoice_particular');

	$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Deleted.</div>');
     	redirect(page_url.'Approval/edit_type_two_invoice_upload/'.$recordid);
}

 function update_save_invoice_type_2_3()
	{
	

			$attach = $_FILES['attach']['name'];
			if($attach<>'')
			{
			$tmp_name=explode('.',$attach);
			$extn=end($tmp_name);
			$newname=time().'.'.$extn;
			$uploadFilePath = SITE_ROOT.'type_2_3_invoice/'.basename($newname);
			move_uploaded_file($_FILES['attach']['tmp_name'], $uploadFilePath);
			}else
			{
			$newname=$this->input->post('oldinvoice');
			}


			$vehicle_no='';
			$transporter_id=0;
			$mobile_no=0;
			$address='';
			$trns_rate_type=0;
			$trrate=0;
			if($this->input->post('transportation')==1)
			{	
						$trns_type=$this->input->post('trns_type');

				if($trns_type==1)
				{
						$vehicle_no=$this->input->post('our_vehicle_no');

				}else
				{
						$vehicle_no=$this->input->post('vehicle_no');
						$transporter_id=$this->input->post('transporter_name');
						$mobile_no=$this->input->post('mobile_no');
						$address=$this->input->post('address');
						$trns_rate_type=$this->input->post('trns_rate_type');
						$trrate=$this->input->post('trrate');
				}

			}


		$data=array('type'=>$this->input->post('type'),
								'invoice_date'=>$this->input->post('invoice_date'),
								'invoice_no'=>$this->input->post('invoice_number'),
								'invoice'=>$newname,
								'shipping_from'=>$this->input->post('hpcl_location'),
								'customer'=>$this->input->post('customer_name'),
								'tds'=>$this->input->post('customer_tds'),
								'tcs'=>$this->input->post('customer_tcs'),
								'transportation'=>$this->input->post('transportation'),
								'transport_type'=>$this->input->post('trns_type'),
								'vehicle_no'=>$vehicle_no,
								'transporter_id'=>$transporter_id,
								'mobile_no'=>$mobile_no,
								'address'=>$address,
								'rate_type'=>$trns_rate_type,
								'transport_rate'=>$trrate,
								'gst'=>$this->input->post('gst'),
								'interest'=>$this->input->post('interest'),
								'addedOn'=>date('Y-m-d H:i:s'),
								'addedBy'=>$_SESSION['logged_in']['user_id']);
								$this->db->where('id',$this->uri->segment(3));
								$this->db->update('type_2_3_invoice',$data);


						for($i=0;$i<count($this->input->post('product'));$i++)
						{
								$recordid = $this->input->post('productrecordid');
								$prdid=$_REQUEST['product'][$i];
								$unit=$_REQUEST['unit'][$i];
								$approved_price=$_REQUEST['approved_price'][$i];
								$qty=$_REQUEST['qty'][$i];
								$dd=array('invoice_id'=>$this->uri->segment(3),'product_id'=>$prdid,'unit'=>$unit,'price'=>$approved_price,'qty'=>$qty,'addedOn'=>date('Y-m-d H:i:s'),
								'addedBy'=>$_SESSION['logged_in']['user_id']);

								if($recordid[$i]<>''){
									$this->db->where('id',$recordid[$i]);
									$this->db->update('type_2_3_invoice_particular',$dd);
								}else{
									if($prdid<>'' && $unit<>'' && $approved_price<>'' && $qty<>'')
									{
									$this->db->insert('type_2_3_invoice_particular',$dd);
									}
								}

								

						}


						$this->session->set_flashdata('message','<div class="alert alert-info">Record Successfully Saved.</div>');
						redirect(page_url.'Approval/edit_type_two_invoice_upload/'.$this->uri->segment(3));
	}



	public function type_1_trail() {
		$data['start_date'] = '';
		$data['end_date'] = '';
		$data['hpcl_locations'] = '';
		$data['products'] = '';
		$this->load->view('approval/type_1_trail_run', $data);
	}


function approval_list_for_trial() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
	

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

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
					$edit = "<a href='".page_url."Approval/edit_approval/".$row->id."' class='btn btn-xs btn-default'>Edit Approval</a>";	
					$item_picked_up = "<input type='checkbox' name='item_picked_up[]' id='item_picked_up".$row->id."' value='".$row->id."' onchange='chk_item_picked_up(".$row->id.")'>";
					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";

					 if($row->purchase_entry==0)
					 {
						$pdetail="No Purchase Entry Has been made";

					$purchase_inventory = "<a href='".page_url."Approval/purchase_inventory/".$row->id."' class='btn btn-info btn-xs'>Purchase Entry</a>";
					 }else
					 {
						$pdetail ="<a href='".page_url."Approval/purchase_details/".$row->id."' target='_blank'>Purchase Details</a>";

						$purchase_inventory='';
					}


					
					if($row->payment_terms == 1) {
						$payment_terms = 'ADVANCE';
						$days='';
					} else if($row->payment_terms == 2) {
						$payment_terms = 'CREDIT PERIOD';
						$days=$row->credit_period." Days";
					} else{
						$payment_terms = '';
						$days='';
					}

					if($row->combination==0)
						{
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_for_trail($row->id);
						}else
						{
					$getApprovalProductDetails=$this->salescrm->getApprovalProductDetails_combination_for_trial($row->id);
						}

					
						


						if($row->combination==0)
						{
							$app_type="Per Product Approval";
						}else
						{
							$app_type="Combination Approval";
						}
						
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>date('d-m-Y', strtotime($row->current_date)).'<br>TYPE-1'.$row->auto_gen_code."<br/><strong style='color:red;'>".$app_type."</strong>",
							'hpcl_location'=>'',
							'product_details'=>$getApprovalProductDetails,
							'payment_terms'=>$payment_terms,
							'credit_period'=>$days,
							'edit'=>$edit,
							'purchase_entry'=>$pdetail,
							'csra_export' => $csra_export,
							'credit_note_claim_amount'=>''
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


		function filter_approval_trial_list() {
	 	$from_date = $this->input->post('from_date');
	 	$to_date = $this->input->post('to_date');
	 	$hpcl_locations = $this->input->post('hpcl_locations');
	 	$products = $this->input->post('products');

	 	$data['start_date'] = $from_date;
		$data['end_date'] = $to_date;
	

		$this->load->view('approval/type_1_trail_run', $data);
	}

	public function approval_summary() {
		$this->load->view('approval/approval_summary_type_1');
	}


function approval_list_summary() {
		$data = array();
		$i=1;
		$start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
		$end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
	
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

		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
														
					$edit = "<a href='".page_url."Approval/edit_approval/".$row->id."' class='btn btn-xs btn-default'>Edit Approval</a>";	
					$item_picked_up = "<input type='checkbox' name='item_picked_up[]' id='item_picked_up".$row->id."' value='".$row->id."' onchange='chk_item_picked_up(".$row->id.")'>";
					$csra_export = "<a href='".page_url."Approval/csra_export/".$row->id."' class='btn btn-success btn-xs'>CSRA SHEET EXPORT</a>";

					 if($row->purchase_entry==0)
					 {
						$pdetail="No Purchase Entry Has been made";

					$purchase_inventory = "<a href='".page_url."Approval/purchase_inventory/".$row->id."' class='btn btn-info btn-xs'>Purchase Entry</a>";
					 }else
					 {
						$pdetail ="<a href='".page_url."Approval/purchase_details/".$row->id."' target='_blank'>Purchase Details</a>";

						$purchase_inventory='';
					}


					
					if($row->payment_terms == 1) {
						$payment_terms = 'ADVANCE';
						$days='';
					} else if($row->payment_terms == 2) {
						$payment_terms = 'CREDIT PERIOD';
						$days=$row->credit_period." Days";
					} else{
						$payment_terms = '';
						$days='';
					}

					if($row->combination==0)
						{
					$getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_data($row->id);
						}else
						{
					$getApprovalProductDetails=$this->salescrm->getApprovalProductDetails_combination_data($row->id);
						}

				


$chkIfProductExists=1;
$chkIflocationExists=1;

					if($chkIfProductExists > 0 && $chkIflocationExists>0) {

						if($row->combination==0)
						{
							$app_type="Per Product Approval";
						}else
						{
							$app_type="Combination Approval";
						}
						
			if(count($getApprovalProductDetails)>0)
			{
				foreach($getApprovalProductDetails as $approval_detail)
				{
					//echo "<pre>"; print_r($approval_detail); exit;
					$data[] = array(
							'sr_no'=>$i,
							'current_date'=>date('d-m-Y', strtotime($row->current_date))."<br/><strong style='color:red;'>".$app_type."</strong>",
							'location'=>$approval_detail['location'],
							'productname'=>$approval_detail['instruments'],
							'approved_price'=>$approval_detail['approved_price'],
							'validity'=>$approval_detail['validity'],
							'credit_vli'=>$approval_detail['credit_vli'],
							'min_qty'=>$approval_detail['moq'],
							'annexture'=>$approval_detail['annex'],
							'combinedwith'=>$approval_detail['combinedwith']
						);
					$i++;
				}
				}
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


function filter_approval_list_summary()
{
	$from=date('Y-m-d',strtotime($this->input->post('from_date')));
	$to=date('Y-m-d',strtotime($this->input->post('to_date')));
	redirect(page_url.'Approval/approval_summary/'.$from.'/'.$to);
}

function close_type_2_manual_payment()
{

	$invoice_particular_id=$this->input->post('invoice_particular_id');
	$flag1=$this->input->post('flag1');
	$flag2=$this->input->post('flag2');
	$flag3=$this->input->post('flag3');
	$rmk=$this->input->post('rmk');
	//echo $invoice_particular_id; exit;
	$d=array('payment'=>1,'paymentBy'=>$_SESSION['logged_in']['user_id'],'manual_close'=>1,'manual_closeBy'=>$_SESSION['logged_in']['user_id'],'manual_closeOn'=>date('Y-m-d H:i:s'),'manual_close_remarks'=>$rmk);
	$this->db->where('id',$invoice_particular_id);
	$this->db->update('type_2_3_invoice_particular',$d);
	$this->session->set_flashdata('message','<div class="alert alert-info">Payment Closed</div>');
	redirect(page_url.'Approval/type_2_customer_payment_pending_new/'.$flag1.'/'.$flag2.'/'.$flag3);


}

function check_payment_dummy()
{
					$total_amount=$this->get_payment_amount(144);
					$payment_made=$this->getpayment_made(144);

					echo $total_amount."<br/>".$payment_made; exit;
}


function check_for_apporval_against_purchase()
{
$purchase_date=$this->input->post('pur_date');
$prd=$this->input->post('prd');
if($prd<>348)
{
	// DO not check empty barrel 
$restey=$this->db->select('a.id')->from('approval_product_details a')->join('approval_form b','a.approval_id=b.id')->where('a.product_id',$prd)->where('a.validity_from<=',date('Y-m-d',strtotime($purchase_date)))->where('a.validity_to>=',date('Y-m-d',strtotime($purchase_date)))->order_by('a.approved_price','ASC')->get();
      		echo $restey->num_rows();
}else{
	echo 1;
}
      		

}

function checkpayment_temp()
{
	$total_amount=$this->get_payment_amount(402);
	$payment_made=$this->getpayment_made(402);

	
	if(trim($payment_made)>=trim($total_amount))
					{
						echo $total_amount."<br/>".$payment_made."<br/>"."hi"; exit;
					}
}


function change_payment_date()
{

	$record_id=$this->input->post('recordid');
	$pay_date=date('Y-m-d',strtotime($this->input->post('pay_date')));
	$dr=array('pur_paymentOn'=>$pay_date);
	$this->db->where('id',$record_id);
	$this->db->update('customer_inventory_payment_details',$dr);
	$this->session->set_flashdata('message','<div class="alert alert-info">Record  Saved.</div>');
  redirect(page_url.'Approval/type_2_3_payment_history/'.	$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7));

}

}

