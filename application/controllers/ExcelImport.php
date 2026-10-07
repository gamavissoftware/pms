<?php
ini_set('max_execution_time', 1200); // 120 (seconds) = 2 Minutes
defined('BASEPATH') or exit('No direct script access allowed');

class ExcelImport extends CI_Controller
{

	private function sync_customer_to_sap($source, $customer_id)
	{
		$customer_id = (int) $customer_id;
		if ($customer_id <= 0) {
			return;
		}

		$this->load->library('Sap_service');
		$result = $source === 'spares'
			? $this->sap_service->sync_spares_customer($customer_id)
			: $this->sap_service->sync_marketing_customer($customer_id);

		if (empty($result['success']) && empty($result['skipped'])) {
			log_message('error', 'SAP customer sync failed for ' . $source . ' customer ' . $customer_id . ': ' . (isset($result['message']) ? $result['message'] : 'Unknown error'));
		}
	}


  public function __construct()
  {
    parent::__construct();
    //$this->load->model('Query_builder','common_query');
    $this->load->model('Salescrm_model', 'salescrm');

    $userrole = $this->session->userdata['logged_in']['role'];
    if ($userrole == 1) {
    } else {
      $ip = $_SERVER["REMOTE_ADDR"];
      /* $query = $this->db->select('ipaddress')->from('ipblocking')->where('ipaddress',$ip)->get();
     if($query->num_rows()=='0'){
     $this->session->set_flashdata('message','<div class="alert" style="z-index: 99999999999; position:absolute; background-color: red;border-color: rgba(240, 80, 80, 0.3);color: #fff;">We are pleased to inform you that we are enhancing the security of the system so you can only access this application from the office.</div><br/><br><br>');
     redirect(page_url.'User');    

     } */
    }
  }
  public function excel_import()
  {

    $this->load->view('excelimport/excelimport');
  }

  public function uploaddata()
  {
     
  require('library/php-excel-reader/excel_reader2.php');
    require('library/SpreadsheetReader.php');
  
  $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.oasis.opendocument.spreadsheet','text/csv'];
  
  $initial_step=$this->getintialstep();
  //echo $_FILES["creditfile"]["type"]; exit;
  if(in_array($_FILES["creditfile"]["type"],$mimes))
  {


    $source=$this->input->post('leadsoruce');
   $assign=$this->input->post('assign');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $uploadFilePath = exceluploads.basename($_FILES['creditfile']['name']);
    move_uploaded_file($_FILES['creditfile']['tmp_name'], $uploadFilePath);
  $Reader = new SpreadsheetReader($uploadFilePath);
  $totalSheet = count($Reader->sheets());


    /* For Loop for all sheets */
    for($i=0;$i<$totalSheet;$i++){

      $Reader->ChangeSheet($i);
       $counter = array();

       $k=0;
      foreach ($Reader as $key=>$Row)
          {   
                  
          
      //echo "<pre>"; print_r($Row); exit;
      
          
      if($Row[1]<>'' && $Row[4]<>'')
      {
        
          $query = $this->db->select('id, unique_no')->from('leads')->order_by('id','desc')->limit(1)->get();
          if($query->num_rows() > 0) {
          foreach($query->result() as $last_id);
          $lastid = $last_id->id;
          $uniqueno = $last_id->unique_no;
          } else {
          $lastid = '';
          $uniqueno = 0;
          }
         $sql = $this->db->select('keyword')
          ->from('lead_source')
          ->where('source_id', $source)
          ->get();

          if ($sql->num_rows() > 0) {
          foreach ($sql->result() as $row); 
          $unique_no = str_pad($uniqueno+1, 3, '0', STR_PAD_LEFT);
          $uniqueid = $row->keyword.$unique_no;
          } else {
          $uniqueid = '';
          }
        
         
        $createdate=isset($Row[0]) ? $Row[0] : '';
        $name=isset($Row[1]) ? $Row[1] : '';
        $address=isset($Row[2]) ? $Row[2] : '';
        $company=isset($Row[3]) ? $Row[3] : '';
        $mobile=isset($Row[4]) ? $Row[4] : '';
        $email=isset($Row[5]) ? $Row[5] : ''; 
        $state=isset($Row[6]) ? $Row[6] : ''; 
        $city=isset($Row[7]) ? $Row[7] : ''; 
        $title=isset($Row[8]) ? $Row[8] : ''; 
        $country_code="91";
        $country="101";
                
        
        $addedby=$this->session->userdata['logged_in']['user_id'];
        $feededOn=date('Y-m-d H:i:s');

            
        if($k<>0)
        {         
        $data=array('lead_source_id'=>$source,'create_date'=>date('Y-m-d'),'customer_name'=>$name,'email'=>$email,'contact_no'=>$mobile,'country_code'=>"91",'country'=>101,'company_name'=>$company,'postal_address'=>$address,'added_by'=>$addedby,'added_on'=>$feededOn,'company_id'=>$_SESSION['logged_in']['business_location'],'unique_id'=>$uniqueid, 'unique_no' => $unique_no,'patient_type_id'=>$customertype,'state'=>$state,'city'=>$city,'title'=>$title);
          $res=$this->db->insert('leads',$data);
          $lid=$this->db->insert_id();

          /** ADD LEAD PRODUCTS **/

          $data1=array('lead_id'=>$lid,'product_id'=>15,'qty'=>1);
          $this->db->insert('lead_products',$data1);

          $data2=array('lead_id'=>$lid,'product_id'=>19,'qty'=>1);
          $this->db->insert('lead_products',$data2);
          /** END **/

          /** ADD LEAD STAGE **/
          $follow=date('Y-m-d', strtotime("+1 day"));
          $data2=array('lead_id'=>$lid,'lead_status'=>$initial_step,'next_follow_date'=>$follow,'added_on'=>date('Y-m-d H:i:s'),'added_by'=>$_SESSION['logged_in']['user_id']);
          $this->db->insert('progress_remarks',$data2);
          /** END **/

          /** ADD ASSIGN **/
          $getAllLeadStages=$this->dashboardmodel->getAllLeadStages();
          if(count($getAllLeadStages)>0) {
          $i=1;
          foreach($getAllLeadStages as $row1) {

          $datas=array('lead_id'=>$lid,'lead_stage_id'=>$row1->lead_id,'user_id'=>$this->input->post('assign'),
          'role_id'=>$row1->user_role,
          'addedOn'=>date('Y-m-d H:i:s'),
          'addedBy'=>$assign);
          $this->db->insert('assigned_users_for_lead',$datas);
          }
          }

          /** END **/

        }
              
        
          
      }
        
      
      
        
    
              $k++;
          }
      }


      // if(count($counter) >0){
      //    $data['totalneglected'] = array_sum($counter);
      //    }else{
      //    $data['totalneglected'] = '';      
      //    }


      $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
      redirect(page_url.'ExcelImport/excel_import');
      
    }else
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger">Your data is not in correct format. Kindly check and try again</div>');
        redirect(page_url.'ExcelImport/excel_import');
    
    }
  
  // if(!empty($data['totalneglected'])){
  // $this->session->set_flashdata('message1','<div class="alert alert-info">Total  '.$data['totalneglected'].' Entries from the excel already exists in the system, hence are skipped from the import</div>');
  // }

  
  
  }

  public function uploaddataold()
  {

    require('library/php-excel-reader/excel_reader2.php');
    require('library/SpreadsheetReader.php');

    $mimes = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.oasis.opendocument.spreadsheet'];

    if (in_array($_FILES["creditfile"]["type"], $mimes)) {

      $uploadFilePath = exceluploads . $_FILES['creditfile']['name'];
      move_uploaded_file($_FILES['creditfile']['tmp_name'], $uploadFilePath);

      $Reader = new SpreadsheetReader($uploadFilePath);
      $totalSheet = count($Reader->sheets());
      /* For Loop for all sheets */
      $blankentry = array();
      $invalidmobile = array();
      $invalidemail = array();
      $duplicate = array();
      for ($i = 0; $i < $totalSheet; $i++) {

        $Reader->ChangeSheet($i);
        $counter = array();

        $f = 0;
        foreach ($Reader as $key => $Row) {
          if ($f == 0) {
          } else {


            $fg_code = isset($Row[0]) ? $Row[0] : '';
            $subpart = isset($Row[1]) ? $Row[1] : '';
            $raw_material_code = isset($Row[2]) ? $Row[2] : '';
            $qty = isset($Row[3]) ? $Row[3] : '';
            $unit = isset($Row[4]) ? $Row[4] : '';
            $per_carton = isset($Row[5]) ? $Row[5] : '';
            $drawingno = isset($Row[6]) ? $Row[6] : '';
            $added_on = date('Y-m-d H:i:s');

            $sql1 = $this->db->select('id')
              ->from('presto_instruments')
              ->where('model_number', $fg_code)
              ->get();

            if ($sql1->num_rows() > 0) {
              foreach ($sql1->result() as $row1);
              $fg_id = $row1->id;
            } else {
              $fg_id = '';
            }



            $sql2 = $this->db->select('id')
              ->from('machine_parts_with_picture')
              ->where('fincode', $raw_material_code)
              ->get();

            if ($sql2->num_rows() > 0) {
              foreach ($sql2->result() as $row2);
              $raw_material_id = $row2->id;
            } else {
              $raw_material_id = '';
            }

            $sql3 = $this->db->select('id')
              ->from('units')
              ->where('shortname', $unit)
              ->get();

            if ($sql3->num_rows() > 0) {
              foreach ($sql3->result() as $row3);
              $unit_id = $row3->id;
            } else {
              $unit_id = '';
            }



            $data = array(
              'mid' => $fg_id,
              'subpartid' => 0,
              'partid' => $raw_material_id,
              'qty' => trim($qty),
              'unit' => $unit_id,
              'per_carton' => trim($per_carton),
              'drawingno' => 'N/A',
              'addedOn' => $added_on,
              'addedBy' => $_SESSION['logged_in']['user_id']
            );

            // echo "<pre>";print_r($data);exit;
            $this->db->insert('machine_bom', $data);
          }

          $f++;
        }
      }

      $response['msg'] = "Process Completed. Please check Logs for any Errors";


      $this->load->view('excelimport/excelimport', $response);
    } else {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">Your data is not in correct format. Kindly check and try again</div>');
      redirect(page_url . 'ExcelImport/excel_import');
    }
  }

  function phone_validation($phone)
  {
    $isvalid = 0;
    if (preg_match("/^[6-9][0-9]{9}$/", $phone)) {
      $isvalid = 1;
      // $pattern = '~
      // \A    # start of the string
      // # find the largest pattern first in a lookahead
      // # (the idea is to compare the size of trailing digits with the smallest pattern)
      // (?= (\d+) \1+ (\d*) \z )
      // # find the smallest pattern
      // (?<pattern> \d+? ) \3+
      // # that has the same or less trailing digits
      // (?! .+ \2 \z)
      // # capture the eventual trailing digits
      // (?= (?<trailing> \d* ) )
      // ~x';

      // if (preg_match($pattern, $phone)) {
      // $isvalid=0;
      // } else {
      if ($phone == '1234567890' || $phone == '0123456789') {
        $isvalid = 0;
      } else {
        if ($phone == '9876543210' || $phone == '0987654321') {
          $isvalid = 0;
        }
      }
      // }

    } else {
      $isvalid = 0;
    }

    return $isvalid;
  }

  function type_one_claim_formatOldddd()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $vendor = $this->uri->segment(5);
    $location = $this->uri->segment(6);

    $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest 13.75%", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:X1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('B1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('C1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('D1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('E1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('F1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('G1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('H1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('I1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('J1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('K1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

    $object->getActiveSheet()->getStyle('L1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('M1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

    $object->getActiveSheet()->getStyle('N1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

    $object->getActiveSheet()->getStyle('O1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('P1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('Q1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('R1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('S1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('T1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('U1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('V1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    $object->getActiveSheet()->getStyle('W1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

    $object->getActiveSheet()->getStyle('X1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);

    for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }



    $excel_row = 2;
    $i = 1;
    $start_date = $start_date;
    $end_date = $end_date;
    $hpcl_location = '';
    $product = '';

    $this->db->select('a.combination,a.purchase_entry,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period')
      ->from('approval_form a');
    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('a.current_date >=', $start_date);
      $this->db->where('a.current_date <=', $end_date);
    }

    $this->db->order_by('a.current_date', 'DESC');
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
      foreach ($query->result() as $row) {

        $approval_id = $row->id;
        if ($row->payment_terms == 1) {
          $payment_terms = 'ADVANCE';
          $days = $row->credit_period . " Days";
        } else if ($row->payment_terms == 2) {
          $payment_terms = 'CREDIT PERIOD';
          $days = $row->credit_period . " Days";
        } else {
          $payment_terms = '';
          $days = '';
        }


        $customer_code = $this->get_sunder_code();
        if ($row->combination == 0) {
          $getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_for_claim($row->id, $start_date, $end_date);
          $cl_detail = explode('|', $getApprovalProductDetails);
          $prd_table = $cl_detail[0];
          $totalclaim = $cl_detail[1];
          $c = 0;
        } else {
          $getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_combination_for_claim($row->id, $start_date, $end_date);

          $cl_detail = explode('|', $getApprovalProductDetails);
          $prd_table = $cl_detail[0];
          $totalclaim = $cl_detail[1];
          $c = 1;
        }


        if ($c == 0) {
          if ($totalclaim > 0) {

            $this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type,c.address as hpcl_loc_code')->from('approval_product_details a')->join('approval_form b', 'a.approval_id=b.id')->where('a.validity_from>=', $start_date)->where('a.validity_from<=', $end_date)->join('hpcl_location c', 'c.id=a.location')->join('presto_instruments d', 'd.id=a.product_id')->join('units e', 'a.pack_size=e.id');
            $query = $this->db->where('a.approval_id', $approval_id)->get();
            if ($query->num_rows() > 0) {
              foreach ($query->result() as $rows) {

                $vli = $rows->credit_vli;
                $moq = $rows->moq;
                $product = $rows->product_id;
                $location = $rows->location;

                $res = $this->db->select('a.original_qty,a.inventory_id,a.qty,a.pack_size,d.shortname')->from('inventory_details a')->join('inventory b', 'a.inventory_id=b.id')->join('vendors c', 'c.id=b.party')->join('units d', 'a.pack_size=d.id')->where('product', $product)->where('b.currentdate>=', $start_date)->where('b.currentdate<=', $end_date)->where('c.hpcl_location', $location)->get();
                if ($res->num_rows() > 0) {
                  foreach ($res->result() as $purrow);


                  $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->hpcl_loc_code);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_type);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $purrow->qty);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
                  $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
                  $excel_row++;
                }
              }
            }
          }
        } else {

          // $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, 'INR');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row,'');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
          //   $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
          //   $excel_row++;

        }
      }
    }


    $fileName = 'Type-I Claim -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }



  function type_two_claim_format()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $hpcl_location = $this->uri->segment(5);
    $current_date = date('M-Y', strtotime($start_date));

    $this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collected Amount", "Difference (If Any)", "Collection Reference", "Date", "Days", "Interest Rate", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Remarks", "Annexure Link");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);

    $objWorkSheet->getStyle("A1:AC1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle("A1:AC1")->getFont()->setBold(true);
    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AB')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AC')->setWidth(40);

    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $objWorkSheet->getStyle("A1:AC1")->applyFromArray(
      $style_cell

    );

    $object->getActiveSheet()->getStyle('A1:AC1')->getFill()->getStartColor()->setRGB('FFDBE2F1');

    for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
      $objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
    }


    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

     $objWorkSheet->getStyle("A1:AC1")->applyFromArray(
              $styleArray
            );

    $query = $this->db->select('d.customer_tcs,d.annexture_name,d.annexture,d.invoice,a.interest_charges,d.type,a.annexure_upload,a.collection_reference,a.commision,a.gst,a.interest_charges,a.paymentOn,a.paid_amount,c.address,e.customer_code,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,b.pack_size,b.volume,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
      ->from('approval_product_details_type_two a')
      ->join('approval_form_type_two d', 'a.approval_id=d.id')
      ->join('presto_instruments b', 'b.id=a.product_id', 'left')
      ->join('hpcl_location c', 'c.id=a.location')
      ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
      ->where('a.delivered', 1)
      ->where('a.payment', 1);

    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('d.current_date>=', $start_date);
      $this->db->where('d.current_date<=', $end_date);
    }

    $this->db->order_by('d.current_date', 'ASC');
    $query = $this->db->get();
    // print_r($query->result());
    // exit;
    if ($query->num_rows() > 0) {


      $i = 1;

      $excel_row = 2;
      $interest_arr = array();
      $interest_arr[] = 0;

      $claim_arr = array();
      $claim_arr[] = 0;
      foreach ($query->result() as $rows) {

        $collection_data=array();
        $collection_data[]=0;

        $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
        $payment_rows = $indata->num_rows();
        // echo $payment_rows; 
        // exit;
        $collection_array = array();
        $collection_amt = array();
        if ($indata->num_rows() > 0) {
          $l = 0;
          foreach ($indata->result() as $invdata) {
            $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(30);
            $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
              $styleArray
            );

             $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );



            if ($payment_rows > 1) {

               $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,$rows->volume);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->deliveredQty);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->deliveredOn)));
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $rows->credit_days);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->deliveredOn . " +" . $rows->credit_days . " Days")));
              
              $total = $rows->approved_price * $rows->deliveredQty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total = $total + $total_g;
              $tcs_amt=0;
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total*$tcs;
                $total=$total+$tcs_amt;
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $total);
             
             
              $diff = $total - $rows->paid_amount;             

              $now = date('Y-m-d', strtotime($rows->deliveredOn . " +" . $rows->credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


              if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($total * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

          
              
              $collection_data[]=$invdata->collection_amount;
              
              $collection_diff=$total-array_sum($collection_data);
 
            
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $calculate_int);
          
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $rows->approved_price);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->approved_price);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, 0);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $rows->commision);
             
              //echo $excel_row."<br/>".$st; exit;
              
              if ($rows->transport_type == 2) {
                $transport_rate = $rows->transport_rate;
              } else {
                $transport_rate = 0;
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $transport_rate);
              

              $total_com = $rows->deliveredQty * $rows->commision;
              $total_transport = $rows->deliveredQty * $transport_rate;
              $total_claim = $total_com + $total_transport;
              
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $total_claim);
             

             
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $rows->annexture_name);
              if ($rows->annexure_upload <> '') {
                $link = page_url1 . "type_two_annexure/" . $rows->annexture;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $link);

              if($l==0)
              {
              $claim_arr[] = $total_claim;
              $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
              $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
              $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
              $object->getActiveSheet()->mergeCells('AA'.$excel_row. ':AA' . $st);
              }






            } else {

             $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             // $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             // $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
              // $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
              // $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             // $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
              //$object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size);
             // $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $rows->volume);
              //$object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->deliveredQty);
              //$object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice);
             // $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->deliveredOn)));
              // $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $rows->credit_days);
             //  $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->deliveredOn . " +" . $rows->credit_days . " Days")));
             // $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $total = $rows->approved_price * $rows->deliveredQty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total = $total + $total_g;
               $tcs_amt=0;
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total*$tcs;
                $total=$total+$tcs_amt;
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $total);
             // $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
             
              $diff = $total - $rows->paid_amount;             

              $now = date('Y-m-d', strtotime($rows->deliveredOn . " +" . $rows->credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


              if ($days > 0) {
               // echo "hi"; exit;
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($total * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

           
              $collection_diff=$total-$invdata->collection_amount;
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $calculate_int);
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $rows->approved_price);
              //$object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->approved_price);
             // $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, 0);
              // $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $rows->commision);
              //$object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
              if ($rows->transport_type == 2) {
                $transport_rate = $rows->transport_rate;
              } else {
                $transport_rate = 0;
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $transport_rate);
              $total_com = $rows->deliveredQty * $rows->commision;
              $total_transport = $rows->deliveredQty * $transport_rate;
              $total_claim = $total_com + $total_transport;
              $claim_arr[] = $total_claim;
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $total_claim);
              // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $rows->annexture_name);
              if ($rows->annexture <> '') {
                $link = page_url1 . "type_two_annexure/" .$rows->annexture;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $link);

            }




            $excel_row++;
           
            $l++;
          }
           $i++;
        }
      }


     // echo "<pre>"; print_r($claim_arr); exit;
      $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);

      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, array_sum($interest_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, array_sum($claim_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, '');
    

      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );
      }
    $fileName = 'Type-II Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }



  function type_three_claim_format()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $vendor = $this->uri->segment(5);
    $location = $this->uri->segment(6);
    $product = $this->uri->segment(7);

    $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest 13.30%", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Approvals");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:Y1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
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

    for ($i = 'A'; $i <=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
      $object->getActiveSheet()->getColumnDimension($i)->setWidth(40);
      $object->getActiveSheet()->getStyle($i . '1')->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '9fbfdf')
          )
        )
      );
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }

    $this->db->select('a.id, a.addedOn, b.auto_gen_code, c.name')
      ->from('item_delivery_type_3 a')
      ->join('type_three_approval b', 'b.id=a.approval_id')
      ->join('hpcl_location c', 'c.id=b.hpcl_location');

    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('a.addedOn >=', $start_date);
      $this->db->where('a.addedOn <=', $end_date);
    }

    if ($vendor <> '' && $vendor <> 'ALL') {
      $this->db->where('a.transporter_id', $vendor);
    }

    if ($location <> '' && $location <> 'ALL') {
      $this->db->where('b.hpcl_location', $location);
    }

    // if($product <> '' && $product <> 'ALL') {
    //  $this->db->where('e.product_id', $product);
    // }
    $sql =  $this->db->get();

    if ($sql->num_rows() > 0) {
      foreach ($sql->result() as $row) {
        $excel_row = 2; //now from row 2
        $i = 1;

        if ($row->addedOn != '' || $row->addedOn != "0000-00-00") {
          $current_date = date('d-m-y', strtotime($row->addedOn));
        } else {
          $current_date = '';
        }



        $query1 = $this->db->select('hpcl_code')->from('store_rack_location')->where('id', 3)->get();
        if ($query1->num_rows() > 0) {
          foreach ($query1->result() as $query11);

          $hpcl_code = $query11->hpcl_code;
        } else {
          $hpcl_code = '';
        }
        $query = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli, b.moq, c.instruments_name, d.shortname')
          ->from('item_delivery_details_type_3 a')
          ->join('type_three_product_details b', 'b.id=a.approval_detail_id')
          ->join('presto_instruments c', 'c.id=b.product_id')
          ->join('units d', 'd.id=a.pack_size')
          ->where('b.approval_id', $row->id)
          ->get();

        $i = 1;
        if ($query->num_rows() > 0) {
          foreach ($query->result() as $rows) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->auto_gen_code);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'LEO EARTHMOVERS PV. LTD.');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type II');
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, 'INR');
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $hpcl_code);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->instruments_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->qty);
            $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $rows->approved_price);
            $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $rows->approved_price);
            $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $current_date);
            $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
            $excel_row++;
            $i++;
          }
        }
      }
    }

    $fileName = 'Type-III Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }



  function type_three_cr_claim_format()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $vendor = $this->uri->segment(5);
    $location = $this->uri->segment(6);
    $product = $this->uri->segment(7);

    $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest 13.30%", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Approvals");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:Y1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
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

    for ($i = 'A'; $i <=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
      $object->getActiveSheet()->getColumnDimension($i)->setWidth(40);
      $object->getActiveSheet()->getStyle($i . '1')->applyFromArray(
        array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '9fbfdf')
          )
        )
      );
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }

    $this->db->select('b.id, a.addedOn, b.auto_gen_code, c.name,a.customer_name')
      ->from('item_delivery_type_3 a')
      ->join('type_three_approval b', 'b.id=a.approval_id')
      ->join('hpcl_location c', 'c.id=b.hpcl_location');

    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('a.addedOn >=', $start_date);
      $this->db->where('a.addedOn <=', $end_date);
    }

    if ($vendor <> '' && $vendor <> 'ALL') {
      $this->db->where('a.transporter_id', $vendor);
    }

    if ($location <> '' && $location <> 'ALL') {
      $this->db->where('b.hpcl_location', $location);
    }

    // if($product <> '' && $product <> 'ALL') {
    //  $this->db->where('e.product_id', $product);
    // }
    $sql =  $this->db->get();

    if ($sql->num_rows() > 0) {
      foreach ($sql->result() as $row) {
        $excel_row = 2; //now from row 2
        $i = 1;

        if ($row->addedOn != '' || $row->addedOn != "0000-00-00") {
          $current_date = date('d-m-y', strtotime($row->addedOn));
        } else {
          $current_date = '';
        }



        $query1 = $this->db->select('hpcl_code')->from('store_rack_location')->where('id', 3)->get();
        if ($query1->num_rows() > 0) {
          foreach ($query1->result() as $query11);

          $hpcl_code = $query11->hpcl_code;
        } else {
          $hpcl_code = '';
        }
        $query = $this->db->select('a.qty, b.approved_price, b.price_validity, b.credit_vli, b.moq, c.instruments_name, d.shortname')
          ->from('item_delivery_details_type_3 a')
          ->join('type_three_product_details b', 'b.id=a.approval_detail_id')
          ->join('presto_instruments c', 'c.id=b.product_id')
          ->join('units d', 'd.id=a.pack_size')
          ->where('b.approval_id', $row->id)
          ->get();

        $i = 1;
        if ($query->num_rows() > 0) {
          foreach ($query->result() as $rows) {
            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->auto_gen_code);
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->customer_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type III');
            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, 'INR');
            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $hpcl_code);
            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->instruments_name);
            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->qty);
            $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $rows->approved_price);
            $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $rows->approved_price);
            $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $current_date);
            $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
            $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
            $excel_row++;
            $i++;
          }
        }
      }
    }

    $fileName = 'Type-III_CR_NOTE_Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }

  function get_sunder_code()
  {
    $code = '';
    $rste = $this->db->select('hpcl_code')->from('store_rack_location')->where('id', 3)->get();
    if ($rste->num_rows() > 0) {
      foreach ($rste->result() as $cod);
      $code = $cod->hpcl_code;
    }

    return $code;
  }


  function type_one_claim_format()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $location = $this->uri->segment(5);


    $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest %", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals", "Evidence");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1:AA1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
    // $object->getActiveSheet()->getStyle('B1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('C1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('D1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('E1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('F1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('G1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('H1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('I1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('J1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('K1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('L1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('M1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('N1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('O1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('P1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('Q1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('R1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('S1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('T1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('U1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('V1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('W1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('X1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('Y1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );
    // $object->getActiveSheet()->getStyle('Z1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );

    // $object->getActiveSheet()->getStyle('AA1')->applyFromArray(
    //   array(
    //     'fill' => array(
    //       'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //       'color' => array('rgb' => '9fbfdf')
    //     )
    //   )
    // );


    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(80);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('21')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('22')->setRowHeight(20);

    for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }



    $excel_row = 2;
    $i = 1;
    $start_date = $start_date;
    $end_date = $end_date;
    $hpcl_location = '';
    $product = '';
    $total_comision = array();
    $total_comision[] = 0;
    $total_intrest = array();
    $total_intrest[] = 0;

    $this->db->select('a.combination,a.purchase_entry,a.id, a.auto_gen_code, a.current_date, a.payment_terms, a.credit_period')
      ->from('approval_form a');
    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('a.current_date >=', $start_date);
      $this->db->where('a.current_date <=', $end_date);
      // $this->db->where('a.id',3);
    }

    $this->db->order_by('a.current_date', 'DESC');
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
      foreach ($query->result() as $row) {

        $approval_id = $row->id;
        if ($row->payment_terms == 1) {
          $payment_terms = 'ADVANCE';
          $days = $row->credit_period . " Days";
        } else if ($row->payment_terms == 2) {
          $payment_terms = 'CREDIT PERIOD';
          $days = $row->credit_period . " Days";
        } else {
          $payment_terms = '';
          $days = '';
        }


        $customer_code = $this->get_sunder_code();
        if ($row->combination == 0) {
          $getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_for_claim_for_export($row->id, $start_date, $end_date);
          $cl_detail = explode('|', $getApprovalProductDetails);
          $prd_table = $cl_detail[0];
          $totalclaim = $cl_detail[1];
          $c = 0;
        } else {
          $getApprovalProductDetails = $this->salescrm->getApprovalProductDetails_combination_for_claim_for_export($row->id, $start_date, $end_date);

          $cl_detail = explode('|', $getApprovalProductDetails);
          $prd_table = $cl_detail[0];
          $totalclaim = $cl_detail[1];
          $c = 1;
        }

        $total_interest = 0;
        $total_com = 0;



        if ($c == 0) {
          // if ($totalclaim > 0) {

          $this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type,c.address as hpcl_loc_code')->from('approval_product_details a')->join('approval_form b', 'a.approval_id=b.id')->join('hpcl_location c', 'c.id=a.location')->join('presto_instruments d', 'd.id=a.product_id')->join('units e', 'a.pack_size=e.id');
          //$this->db->where('a.id',26);
          $query = $this->db->where('a.approval_id', $approval_id)->get();
          if ($query->num_rows() > 0) {
            foreach ($query->result() as $rows) {

              $vli = $rows->credit_vli;
              $moq = $rows->moq;
              $product = $rows->product_id;
              $location = $rows->location;

              $approved_price = $rows->approved_price;
              $product_pack_type = $rows->pack_type;
              $approval_pack_size = $rows->pack_size;
              $csra = $rows->approved_price;
              $annexture = $rows->annexture;
              if ($rows->annexure_upload != '') {
                $annexure_upload = site_http_root . 'type_one_annexure/' . $rows->annexure_upload;
              } else {
                $annexure_upload = '';
              }

              if ($moq > 0) {
                if ($product_pack_type == "BULK" && $approval_pack_size == 5) {
                  $converted_moq = $moq / $rows->density;
                } else {
                  $converted_moq = $moq;
                }
              } else {
                $converted_moq = 0;
              }


              $res = $this->db->select('a.original_qty,a.inventory_id,a.rate,a.qty,a.pack_size,d.shortname,b.payment_type,b.credit_days,b.pur_paymentOn,b.interest,b.currentdate,b.bill_no,b.gst,b.pur_payment')->from('inventory_details a')->join('inventory b', 'a.inventory_id=b.id')->join('vendors c', 'c.id=b.party')->join('units d', 'a.pack_size=d.id')->where('product', $product)->where('b.currentdate>=', $rows->validity_from)->where('b.currentdate<=', $rows->validity_to)->where('c.hpcl_location', $location)->get();


              if ($res->num_rows() > 0) {
                $qty_array = array();
                $inventry_array = array();
                $qty_array[] = 0;
                foreach ($res->result() as $purconsolidated) {
                  $qty_array[] = $purconsolidated->original_qty;
                  $inventry_array[] = $purconsolidated->inventory_id;
                }
                foreach ($res->result() as $purrow);

                if ($purrow->payment_type == 5) {
                  $due_date = date('d/m/Y', strtotime($purrow->currentdate . ' + ' . $purrow->credit_days . ' days'));
                } else {
                  $due_date = date('d/m/Y', strtotime($purrow->currentdate));
                }



                // echo $days;

                // exit();


                //  echo $purrow->qty; exit;
                if (array_sum($qty_array) >= $converted_moq) {

                  $claim = array_sum($qty_array) * $vli;
                  // $total_comision =  $total_comision + $claim ;
                  // $total_com =  $total_com + $claim ;

                } else {
                  $claim = 0;
                }

                $gst_amount = array_sum($qty_array) * $purrow->rate * $purrow->gst / 100;
                $product_value = array_sum($qty_array) * $purrow->rate + $gst_amount;
                $diff_rate = $purrow->rate - $csra;


                $inventories = "'" . implode("', '", $inventry_array) . "'";

                //  echo "<pre>"; print_r($inventry_array); exit;
                $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('inventory_payment_details a')->join('sunder_collection_reference b', 'b.id=a.collection_id')->where_in('a.inventory_id', $inventories, false)->get();
                $payment_rows = $indata->num_rows();
                if ($indata->num_rows() > 0) {


                  $l = 0;
                  foreach ($indata->result() as $invdata) {



                    $diff = abs(strtotime($invdata->pur_paymentOn) - strtotime(date('Y-m-d', strtotime($purrow->currentdate . " +" . $purrow->credit_days . " Days"))));

                    $days = round($diff / (60 * 60 * 24));
                    $payment_date = date('d/m/Y', strtotime($invdata->pur_paymentOn));

                    if (date('Y-m-d', strtotime($invdata->pur_paymentOn)) > date('Y-m-d', strtotime($purrow->currentdate . " +" . $purrow->credit_days . " Days"))) {
                      $gst_slab = $purrow->gst / 100;
                      $gst_amount = $purrow->qty * $purrow->rate * $gst_slab;
                      $total = $purrow->qty * $purrow->rate + $gst_amount;

                      /** CACULATE INTEREST **/
                      $interest = $purrow->interest;
                      $interest = $interest / 100;
                      $calculate_int = ($invdata->collection_amount * $interest) / 365;
                      $calculate_int = round($calculate_int * $days, 2);

                      $total_interest = $total_interest + $calculate_int;
                      /** END **/
                    } else {
                      $calculate_int = '';
                    }



                    // $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
                    //$object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
                    if ($l == 0 && $payment_rows > 0) {
                      $st = $excel_row + $payment_rows - 1;
                      $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
                      $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
                      //  echo $excel_row."<br/>".$st; exit;
                      $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
                      $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
                      $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->hpcl_loc_code);
                      $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
                      $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_type);
                      $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
                      $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, array_sum($qty_array));
                      $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $purrow->bill_no);
                      $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($purrow->currentdate)));
                      $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $purrow->credit_days);
                      $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);

                      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $due_date);
                      $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $product_value);
                      $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
                    } else {
                      $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
                      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
                      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->hpcl_loc_code);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_type);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
                      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, array_sum($qty_array));
                      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $purrow->bill_no);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($purrow->currentdate)));
                      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $purrow->credit_days);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $due_date);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $product_value);
                    }
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->hpcl_loc_code);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_type);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, array_sum($qty_array));
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $purrow->bill_no);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($purrow->currentdate)));

                    // $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $purrow->credit_days);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $due_date);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $product_value);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $invdata->collection_name);

                    $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $payment_date);

                    $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $days);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $purrow->interest);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $calculate_int);

                    if ($l == 0 && $payment_rows > 0) {
                      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $purrow->rate);
                      $st = $excel_row + $payment_rows - 1;
                      //  echo $excel_row."<br/>".$st; exit;
                      $object->getActiveSheet()->mergeCells('U' . $excel_row . ':U' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $csra);
                      $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $diff_rate);
                      $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $vli);
                      $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim);
                      $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
                      $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annexure_upload);

                      // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);


                    } else {
                      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $purrow->rate);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $csra);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $diff_rate);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $vli);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
                      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annexure_upload);
                    }

                    // $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $purrow->rate);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $csra);

                    // $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $diff_rate);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $vli);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
                    // $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annexure_upload);
                    //                   $url = str_replace('http://', '', $annexure_upload);
                    // $object->getActiveSheet()->getCellByColumnAndRow($excel_row,26)->getHyperlink()->setUrl('http://www.'.$url);

                    $total_intrest[] = $calculate_int;
                    if ($l == 0) {
                      $total_comision[] = $claim;
                      $i++;
                    }
                    $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );

                    $excel_row++;
                    // $i++;
                    $l++;
                  }
                }
              }
            }
          }
          // }

          // echo "<pre>"; print_r($total_comision); exit;
        } else {


          // if ($totalclaim > 0) {
          $this->db->select('a.*,d.instruments_name,d.density,c.name,b.auto_gen_code,b.combination,b.id as approval_id,e.shortname,d.pack_size as pack_type,c.address as hpcl_loc_code')->from('approval_product_details a')->join('approval_form b', 'a.approval_id=b.id')->join('hpcl_location c', 'c.id=a.location')->join('presto_instruments d', 'd.id=a.product_id')->join('units e', 'a.pack_size=e.id');
          $query = $this->db->where('a.approval_id', $approval_id)->get();
          if ($query->num_rows() > 0) {
            $t = 0;
            foreach ($query->result() as $rows2) {
              //print_r($rows2);  
              // $vli = $rows2->credit_vli;
              $moq = $rows2->moq;
              $product = $rows2->product_id;
              // echo $product; exit;
              $location = $rows2->location;
              $row2_id = $rows2->id;
              $csra = $rows2->approved_price;
              $annexture = $rows2->annexture;
              if ($rows2->annexure_upload != '') {
                $annexure_upload = site_http_root . 'type_one_annexure/' . $rows2->annexure_upload;
              } else {
                $annexure_upload = '';
              }

              $combdata = $this->salescrm->getcombinationData($approval_id);
              if (count($combdata) > 0) {
                $type = $combdata['type'];
                if ($type == 1) {
                  $type_name = "Combined MOQ";
                  $moq_span = $query->num_rows();
                } else if ($type == 2) {
                  $type_name = "Combined VLI";
                  $vli_span = $query->num_rows();
                } else if ($type == 3) {
                  $type_name = "Combined MOQ & VLI";
                  $moq_span = $query->num_rows();
                  $vli_span = $query->num_rows();
                } else {
                  $type_name = "";
                  $moq_span = 0;
                  $vli_span = 0;
                }
                $moq = $combdata['moq'];
                $vli = $combdata['vli'];
              } else {
                $type_name = '';
                $moq = 0;
                $vli = 0;
                $moq_span = 0;
                $vli_span = 0;
              }

              $instrument_name = $rows2->instruments_name;

              $res2 = $this->db->select('a.original_qty,a.inventory_id,a.rate,a.qty,a.pack_size,d.shortname,b.payment_type,b.credit_days,b.pur_paymentOn,b.interest,b.currentdate,b.bill_no,b.gst,b.pur_payment')->from('inventory_details a')->join('inventory b', 'a.inventory_id=b.id')->join('vendors c', 'c.id=b.party')->join('units d', 'a.pack_size=d.id')->where('a.product', $product)->where('b.currentdate>=', $rows2->validity_from)->where('b.currentdate<=', $rows2->validity_to)->where('c.hpcl_location', $location)->get();


              if ($res2->num_rows() > 0) {
                $inventry_array2 = array();
                foreach ($res2->result() as $purconsolidated2) {
                  // $qty_array[] = $purconsolidated2->original_qty;
                  $inventry_array2[] = $purconsolidated2->inventory_id;
                }

                foreach ($res2->result() as $purrow2);
                // echo "<pre>";
                // print_r($purrow2);
                //echo "id:".$row2_id;
                if ($purrow2->payment_type == 5) {
                  $due_date = date('d/m/Y', strtotime($purrow1->currentdate . ' + ' . $purrow2->credit_days . ' days'));
                } else {
                  $due_date = date('d/m/Y', strtotime($purrow2->currentdate));
                }

                if ($purrow2->pur_payment == 1) {
                  // $diff = abs(strtotime($purrow2->pur_paymentOn) - strtotime(date('Y-m-d', strtotime($purrow2->currentdate . " +" . $purrow2->credit_days . " Days"))));

                  // $days = round($diff / (60 * 60 * 24));
                  // $payment_date = date('d/m/Y', strtotime($purrow2->pur_paymentOn));

                  // if (date('Y-m-d', strtotime($purrow2->pur_paymentOn)) > date('Y-m-d', strtotime($purrow2->currentdate . " +" . $purrow2->credit_days . " Days"))) {
                  //   $gst_slab = $purrow2->gst / 100;
                  //   $gst_amount = $purrow2->qty * $purrow2->rate * $gst_slab;
                  //   $total = $purrow2->qty * $purrow2->rate + $gst_amount;

                  //   /** CACULATE INTEREST **/
                  //   $interest = $purrow2->interest;
                  //   $interest = $interest / 100;
                  //   $calculate_int = ($total * $interest) / 365;
                  //   $calculate_int = round($calculate_int * $days, 2);

                  //   $total_interest = $total_interest + $calculate_int;
                  //   /** END **/
                  // } else {
                  //   $calculate_int = '';
                  // }

                  $claim = $this->salescrm->getClaimgenerated_product_wise_combination($rows2->approval_id, $vli, $moq, $rows2->location, $rows2->validity_from, $rows2->validity_to, $rows2->product_id, $start_date, $end_date, $rows2->pack_size, $rows2->density, $rows2->pack_type);
                  $c = explode("|", $claim);
                  $claim_amount2 = $c[0];
                  $purchase_qty = $c[1];
                  $error_message = $c[2];
                } else {
                  $payment_date = '';
                  $calculate_int = '';
                  $days = '';
                  $claim_amount2 = 0;
                  $purchase_qty = '';
                }


                $product_pur_qty_claim = $this->salescrm->getClaimgenerated_product_wise_combination_product_wise($rows2->approval_id, $vli, $moq, $rows2->location, $rows2->validity_from, $rows2->validity_to, $rows2->product_id, $start_date, $end_date, $rows2->pack_size, $rows2->density, $rows2->pack_type);
                // print_r($product_pur_qty_claim);exit;

                $cp = explode("|", $product_pur_qty_claim);
                $claim_amt = $cp[0];
                $product_pur_qty = $cp[1];


                $gst_amount = $product_pur_qty * $purrow2->rate * $purrow2->gst / 100;

                $diff_rate = $purrow2->rate - $csra;


                $product_value = $product_pur_qty * $purrow2->rate + $gst_amount;
                // $claim_amt = $product_value * $vli;
                // echo "<pre>"; print_r($inventry_array2); exit;
                $inventories2 = "'" . implode("', '", $inventry_array2) . "'";
                $indata2 = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('inventory_payment_details a')->join('sunder_collection_reference b', 'b.id=a.collection_id')->where_in('a.inventory_id', $inventories2, false)->get();
                $payment_rows2 = $indata2->num_rows();
                // echo $payment_rows2; exit;
                if ($indata2->num_rows() > 0) {
                  $l = 0;
                  $collection_array = array();
                  $collection_amt = array();
                  foreach ($indata2->result() as $invdata2) {
                    $collection_array[] = $invdata2->collection_name;
                    $collection_amt[] = $invdata2->collection_amount;

                     $diff = abs(strtotime($invdata2->pur_paymentOn) - strtotime(date('Y-m-d', strtotime($purrow2->currentdate . " +" . $purrow2->credit_days . " Days"))));

                    $days = round($diff / (60 * 60 * 24));
                    $payment_date = date('d/m/Y', strtotime($invdata2->pur_paymentOn));

                    if (date('Y-m-d', strtotime($purrow2->pur_paymentOn)) > date('Y-m-d', strtotime($purrow2->currentdate . " +" . $purrow2->credit_days . " Days"))) {
                      $gst_slab = $purrow2->gst / 100;
                      $gst_amount = $purrow2->qty * $purrow2->rate * $gst_slab;
                      $total = $purrow2->qty * $purrow2->rate + $gst_amount;

                      /** CACULATE INTEREST **/
                      $interest = $purrow2->interest;
                      $interest = $interest / 100;
                      $calculate_int = ($invdata2->collection_amount * $interest) / 365;
                      $calculate_int = round($calculate_int * $days, 2);

                      $total_interest = $total_interest + $calculate_int;
                      /** END **/
                    } else {
                      $calculate_int = '';
                    }

                  }
                }

                // print_r($collection_array);
                $col = implode(', ', $collection_array);
                // echo $col; exit;

                $st = $excel_row + 1;
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);

                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);

                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');

                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');


                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows2->hpcl_loc_code);

                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $instrument_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows2->pack_type);
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $product_pur_qty);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $purrow2->bill_no);
                $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($purrow2->currentdate)));

                $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $purrow2->credit_days);
                $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $due_date);
                $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $product_value);
                if ($t == 0) {
                  $st = $excel_row + 1;
                  $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($collection_amt));
                  $object->getActiveSheet()->mergeCells('O' . $excel_row . ':O' . $st);
                  $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $col);
                  $object->getActiveSheet()->mergeCells('P' . $excel_row . ':P' . $st);
                }

                $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $payment_date);

                $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $days);
                $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $purrow2->interest);
                $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $calculate_int);
                $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $purrow2->rate);
                $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $csra);

                $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $diff_rate);
                if ($t == 0) {
                  $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $vli);
                  $st = $excel_row + 1;
                  $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
                }
                $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim_amt);
                $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
                $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annexure_upload);

                $total_intrest[] = $calculate_int;
                $total_comision[] = $claim_amt;

                $styleArray = array(
                  'borders' => array(
                    'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                  )
                );

                $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                  $style_cell
                );


                $excel_row++;

                $i++;
                $t++;
                // } }
              }
            }
          }
          // }
        }
      }
    }



    $tot =  array_sum($total_comision);
    $tot_int =  array_sum($total_intrest);


    // $tot =8;
    //print_r($total_comision);
    // echo $tot;
    //exit();

    $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, 'Total');
    $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $tot_int);

    $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $tot);

    $styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
      )
    );

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
      $style_cell
    );



    $fileName = 'Type-I Claim -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }

   function type_one_claim_format_purchase()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $location = $this->uri->segment(5);


    $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest %", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals", "Evidence");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1:AA1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );
   


    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(80);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('21')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('22')->setRowHeight(20);

    for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }



    $excel_row = 2;

     $this->db->select('a.*,b.bill_no,b.bill_date,b.currentdate,b.payment_type,b.credit_days, c.address as hpcl_loc_code, d.instruments_name,d.pack_size as pack_type,d.density, b.gst,b.interest')
              ->from('inventory_details a')
              ->join('inventory b','a.inventory_id=b.id')
              ->join('hpcl_location c', 'c.id=b.party')
              ->join('presto_instruments d', 'd.id=a.product');

    if ($start_date <> '' && $end_date <> '') {
      $this->db->where('b.currentdate>=', $start_date);
      $this->db->where('b.currentdate<=', $end_date);
    }


      $query = $this->db->get();
    if($query->num_rows() > 0){
      $i = 1;
      $customer_code = $this->get_sunder_code();




      foreach($query->result() as $rows){
        // echo "<pre>";
        // print_r($query->result()); exit;
        // $vli = $rows->credit_vli;
        // $moq = $rows->moq;
        $product = $rows->product;
        $location = $rows->hpcl_loc_code;

        $product_pack_type = $rows->pack_type;
        $approval_pack_size = $rows->pack_size;
         // if ($moq > 0) {
         //    if ($product_pack_type == "BULK" && $approval_pack_size == 5) {
         //      $converted_moq = $moq / $rows->density;
         //    } else {
         //      $converted_moq = $moq;
         //    }
         //  } else {
         //    $converted_moq = 0;
         //  }

           if ($rows->payment_type == 5) {
                  $due_date = date('d/m/Y', strtotime($rows->currentdate . ' + ' . $rows->credit_days . ' days'));
                } else {
                  $due_date = date('d/m/Y', strtotime($rows->currentdate));
                }

                $gst_amount = $rows->original_qty * $rows->rate * $rows->gst / 100;
                 $product_value = $rows->original_qty * $rows->rate + $gst_amount;

               $approval = $this->salescrm->get_product_approval_details($rows->product,$start_date, $end_date);

               // echo "<pre>"; print_r($aprrow->result()); exit;

               if($approval){

                foreach($approval  as $aprrow);
                $approved_price = $aprrow->approved_price;
                $diff_rate = $rows->rate - $approved_price;

                   $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('inventory_payment_details a')->join('sunder_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product)->get();
                   $payment_rows = $indata->num_rows();
                   // echo $aprrow->approved_price; exit;
                      // if ($payment_rows > 0) {
                      //   $l = 0;
                      //   foreach ($indata->result() as $invdata) {


            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
         $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->hpcl_loc_code);
    $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
        $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_type);
        $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->original_qty);
        $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->bill_no);
        $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->currentdate)));
        $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $rows->credit_days);
        $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $due_date);
        $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $product_value );
        // $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
        // $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $invdata->collection_name);
        $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $rows->interest);
        $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->rate);
        $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $approved_price);
        $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $diff_rate);
        $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $aprrow->credit_vli);
        $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
        $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $aprrow->annexture);
        $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $aprrow->annexure_upload);

        $i++;
        $excel_row++;
      // }
      // }

      }
    }
            
  }
  
    

   
    $styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
      )
    );

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
      $style_cell
    );



    $fileName = 'Type-I Claim -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }

  function type_one_claim_with_interest()
  {

      $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
     $location = $this->uri->segment(5);
     $flag = $this->uri->segment(6);

     $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest %", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals", "Evidence");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1:AA1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

     $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(80);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('21')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('22')->setRowHeight(20);

      for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }

 $customer_code = $this->get_sunder_code();
 $excel_row = 2;

 $total_claim_amount=array();
 $total_claim_amount[]=0;
 $total_interest_amount=array();
 $total_interest_amount[]=0;
 $total_collection_amount=array();
 $total_collection_amount[]=0;
  $reste=$this->db->select('b.volume,a.product,d.credit_days,e.hpcl_location,d.interest,d.payment_type,d.pur_payment,d.bill_no,e.name as vendor_name,d.gst as gstrate,d.currentdate,a.id, a.product,a.pack_size,a.qty,a.original_qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname,b.pack_size as prd_pack_size')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','a.inventory_id=d.id')->join('vendors e','d.party=e.id')->where('d.currentdate>=',$start_date)->where('d.currentdate<=',$end_date)->where('d.hpcl_billing_company',3)->where('a.payment',1)->get();
  if($reste->num_rows()>0)
  {
   
     $i=1;
    foreach($reste->result() as $row)
    {

      $location_code=$this->salescrm->getHpclCompanyCode($row->hpcl_location);
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

        /** GST CALCULATION **/
        $total=$row->rate*$row->qty;
        $gstrate=$row->gstrate/100;
        $total_gst=$total*$gstrate;
        $grandtotal=$total+$total_gst;
        /** END **/


        $collection_details=$this->salescrm->getcollection_details($row->id,$due_date,$row->interest,$row->rate,$row->product,$row->currentdate,$row->hpcl_location);
        $collections=explode('|',$collection_details);
        $payment_parts=$collections[2];

/** CHECK FOR APPROVAL **/
$approvals_data=$this->salescrm->getApprovalForPurchase($row->product,$row->currentdate,$row->hpcl_location);
$appdata=explode('|',$approvals_data);
$app_id=$appdata[0];
$app_rate=$appdata[1];
$type=$appdata[2];
$moq=$appdata[3];
$credit=$appdata[4];
/** NEW **/
$credit=$row->rate-$app_rate+$credit;
$validity_from=$appdata[5];
$validity_to=$appdata[6];
$annexture=$appdata[7];
$annexure_upload=$appdata[8];
if($annexure_upload!='')
{
  $annex_link=page_url1."type_one_annexure/".$annexure_upload;
}else
{
  $annex_link='';
}
      


        /** GET COLLECTION DATA **/

$interest=$row->interest;
$billing_price=$row->rate;
$product=$row->product;
$purchase_date=$row->currentdate;
$hpcl_location=$row->hpcl_location;

    $resty=$this->db->select('a.*,b.collection_id as collection_ref')->from('inventory_payment_details_product_wise a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.inventory_details_id',$row->id)->order_by('id','ASC')->get();
    if($resty->num_rows()>0)
    {
    $u=0;
    foreach($resty->result() as $collectionrow)
    {

      $due=date('Y-m-d',strtotime($due_date));
            $pay_date=date('Y-m-d',strtotime($collectionrow->payment_date));

            $start = strtotime($due);
            $end = strtotime($pay_date);
            $days_between = ceil($end - $start) / 86400;


            $approval_data=$this->salescrm->getApprovalForPurchase($row->id,$row->currentdate,$row->hpcl_location);
            $appdata=explode('|',$approvals_data);
        $app_id=$appdata[0];
        $app_rate=$appdata[1];

        $type=$appdata[2];
        $moq=$appdata[3];
        $credit=$appdata[4];
        /** NEW **/
        $credit=$row->rate-$app_rate+$credit;
        $validity_from=$appdata[5];
        $validity_to=$appdata[6];

            $rate_diff=$billing_price-$app_rate;

            $total_interest=0;
            if($days_between>0)
            {
            /** CACULATE INTEREST **/
            $interest_rate = $interest;
            $interest_rate = $interest_rate / 100;
            $calculate_int = ($collectionrow->amount * $interest_rate) / 365;
            $calculate_int = round($calculate_int * $days_between, 2);

            $total_interest = $total_interest + $calculate_int;
            }else
            {
              $total_interest=0;
            }


              $errors='';
        if($type==0)
        {
          /** CHECK FOR MOQ **/
          if($moq>0)
          {
          $moq_fullfilled=$this->salescrm->checkMOQFullfilled($validity_from,$validity_to,$moq,$product,$row->hpcl_location);
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



         $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );


if($row->volume<>'' && $row->volume<>0)
                  {
                    $newqty=$row->qty/$row->volume;
                  }else
                  {
                    $newqty=0;
                  }

   $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
   $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
   $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
   $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $location_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, strtoupper($row->instruments_name));
   $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->prd_pack_size."-".$row->volume);
   $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->qty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->bill_no);
   $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y',strtotime($row->currentdate)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $row->credit_days);
   $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/Y',strtotime($due_date)));
   

   $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collectionrow->collection_ref);
   $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, date('d/m/Y',strtotime($collectionrow->payment_date)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row,$days_between);
   $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $interest);
   $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $total_interest);
   $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $billing_price);
   $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $app_rate);
   $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rate_diff);
   $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $credit);
   $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim);
   $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
   $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annex_link);
   if($payment_parts>1)
   {
    if($u==0)
    {
  
   $total_claim_amount[]=$claim;
   
    }

     $total_interest_amount[]=$total_interest;
     $total_collection_amount[]=$collectionrow->amount;
  }else
  {
     $total_interest_amount[]=$total_interest;
   $total_claim_amount[]=$claim;
   $total_collection_amount[]=$collectionrow->amount;
  }
   if($u==0 && $payment_parts>1)
   {
    $st = $excel_row + $payment_parts - 1;
    $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
    $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
    $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
    $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
    $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
    $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
    $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
    $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
    $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
    $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
    $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
    $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
    $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
    $object->getActiveSheet()->mergeCells('U' . $excel_row . ':U' . $st);
    $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
    $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
    $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
    $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
    $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
    $object->getActiveSheet()->mergeCells('AA' . $excel_row . ':AA' . $st);
   }




$excel_row++;
$u++;
}
$i++;
}
}



/** DEBIT NOTED **/
$resuiyuretr=$this->db->select('a.payment_date,a.balance_used,a.collection_id,a.credit_debit_amount,a.invoice_detail,a.invoice_date,a.credit_debit_for,b.collection_id as collection_name')->from('sunder_collection_credit_debit a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.credit_debit',2)->where('invoice_date>=',$start_date." 00:00:00")->where('invoice_date<=',$end_date." 23:59:59")->get();
if($resuiyuretr->num_rows()>0)
{
foreach($resuiyuretr->result() as $debit)
{


 $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );
$total_collection_amount[]=$debit->balance_used;
  // echo "<pre>"; print_r($total_collection_amount); exit;
  $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,$i);
  $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,"19864000");
  $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,"FARIDABAD-CFA-II");
  $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,"Type I");
  $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,"14194");
  $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,$debit->credit_debit_for);
  $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,$debit->invoice_detail);
  $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,date('d/m/Y',strtotime($debit->invoice_date)));
  $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row,$debit->credit_debit_amount);
  $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row,$debit->balance_used);
  $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row,$debit->collection_name);
  $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row,date('d/m/Y',strtotime($debit->payment_date)));
  $i++;
  $excel_row++;
}
}
/** END **/

  $object->getActiveSheet()->getStyle("A".$excel_row.":AA".$excel_row)->getFont()->setBold(true);

 // echo "<pre>"; print_r($total_claim_amount); exit;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($total_collection_amount));
     // $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, array_sum($total_claim_amount));
    
    

  $object->getActiveSheet()->getStyle("A".$excel_row.":AA".$excel_row)->getFont()->setBold(true);

  //echo "<pre>"; print_r($total_collection_amount); exit;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($total_collection_amount));
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, array_sum($total_interest_amount));
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, array_sum($total_claim_amount));
    


}


$styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
      )
    );

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
      $style_cell
    );

    $fileName = 'Type-I Claim -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    if($flag=='')
    {

    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
    
    }


  }



  function type_one_claim_without_interest()
  {

      $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
     $location = $this->uri->segment(5);

     $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals", "Evidence");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1:AA1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

     $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(80);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('21')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('22')->setRowHeight(20);

      for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }

 $customer_code = $this->get_sunder_code();
 $excel_row = 2;

 $total_claim_amount=array();
 $total_claim_amount[]=0;
 $total_interest_amount=array();
 $total_interest_amount[]=0;
 $total_collection_amount=array();
 $total_collection_amount[]=0;
  $reste=$this->db->select('b.volume,a.product,d.credit_days,e.hpcl_location,d.interest,d.payment_type,d.pur_payment,d.bill_no,e.name as vendor_name,d.gst as gstrate,d.currentdate,a.id, a.product,a.pack_size,a.qty,a.original_qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname,b.pack_size as prd_pack_size')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','a.inventory_id=d.id')->join('vendors e','d.party=e.id')->where('d.currentdate>=',$start_date)->where('d.currentdate<=',$end_date)->where('d.hpcl_billing_company',3)->where('a.payment',1)->get();
  if($reste->num_rows()>0)
  {
   
     $i=1;
    foreach($reste->result() as $row)
    {

      $location_code=$this->salescrm->getHpclCompanyCode($row->hpcl_location);
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

        /** GST CALCULATION **/
        $total=$row->rate*$row->qty;
        $gstrate=$row->gstrate/100;
        $total_gst=$total*$gstrate;
        $grandtotal=$total+$total_gst;
        /** END **/


        $collection_details=$this->salescrm->getcollection_details($row->id,$due_date,$row->interest,$row->rate,$row->product,$row->currentdate,$row->hpcl_location);
        $collections=explode('|',$collection_details);
        $payment_parts=$collections[2];

/** CHECK FOR APPROVAL **/
$approvals_data=$this->salescrm->getApprovalForPurchase($row->product,$row->currentdate,$row->hpcl_location);
$appdata=explode('|',$approvals_data);
$app_id=$appdata[0];
$app_rate=$appdata[1];
$type=$appdata[2];
$moq=$appdata[3];
$credit=$appdata[4];
/** NEW **/
$credit=$row->rate-$app_rate+$credit;
$validity_from=$appdata[5];
$validity_to=$appdata[6];
$annexture=$appdata[7];
$annexure_upload=$appdata[8];
if($annexure_upload!='')
{
  $annex_link=page_url1."type_one_annexure/".$annexure_upload;
}else
{
  $annex_link='';
}
      


        /** GET COLLECTION DATA **/

$interest=$row->interest;
$billing_price=$row->rate;
$product=$row->product;
$purchase_date=$row->currentdate;
$hpcl_location=$row->hpcl_location;

    $resty=$this->db->select('a.*,b.collection_id as collection_ref')->from('inventory_payment_details_product_wise a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.inventory_details_id',$row->id)->order_by('id','ASC')->get();
    if($resty->num_rows()>0)
    {
    $u=0;
    foreach($resty->result() as $collectionrow)
    {

      $due=date('Y-m-d',strtotime($due_date));
            $pay_date=date('Y-m-d',strtotime($collectionrow->payment_date));

            $start = strtotime($due);
            $end = strtotime($pay_date);
            $days_between = ceil(abs($end - $start) / 86400);
            if($days_between>0)
            {
              $days_between=0;
            }else
            {
              $days_between=$days_between;
            }


            $approval_data=$this->salescrm->getApprovalForPurchase($row->id,$row->currentdate,$row->hpcl_location);
            $appdata=explode('|',$approvals_data);
        $app_id=$appdata[0];
        $app_rate=$appdata[1];

        $type=$appdata[2];
        $moq=$appdata[3];
        $credit=$appdata[4];
        /** NEW **/
        $credit=$row->rate-$app_rate+$credit;
        $validity_from=$appdata[5];
        $validity_to=$appdata[6];

            $rate_diff=$billing_price-$app_rate;

            $total_interest=0;
            if($days_between>0)
            {
            /** CACULATE INTEREST **/
            $interest_rate = $interest;
            $interest_rate = $interest_rate / 100;
            $calculate_int = ($collectionrow->amount * $interest_rate) / 365;
            $calculate_int = round($calculate_int * $days_between, 2);

            $total_interest = $total_interest + $calculate_int;
            }else
            {
              $total_interest=0;
            }


              $errors='';
        if($type==0)
        {
          /** CHECK FOR MOQ **/
          if($moq>0)
          {
          $moq_fullfilled=$this->salescrm->checkMOQFullfilled($validity_from,$validity_to,$moq,$product,$row->hpcl_location);
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



         $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );


                  if($row->volume<>'' && $row->volume<>0)
                  {
                    $newqty=$row->qty/$row->volume;
                  }else
                  {
                    $newqty=0;
                  }

   $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
   $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
   $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
   $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $location_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, strtoupper($row->instruments_name));
   $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->prd_pack_size."-".$row->volume);
   $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->qty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->bill_no);
   $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y',strtotime($row->currentdate)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $row->credit_days);
   $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/Y',strtotime($due_date)));
   

   $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collectionrow->collection_ref);
   $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, date('d/m/Y',strtotime($collectionrow->payment_date)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row,$days_between);

   $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $billing_price);
   $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $app_rate);
   $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rate_diff);
   $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $credit);
   $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $claim);
   $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $annexture);
   $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $annex_link);
   if($payment_parts>1)
   {

    $total_collection_amount[]=$collectionrow->amount;
    if($u==0)
    {
   $total_interest_amount[]=$total_interest;
   $total_claim_amount[]=$claim;
   
    }
  }else
  {
     $total_interest_amount[]=$total_interest;
   $total_claim_amount[]=$claim;
   $total_collection_amount[]=$collectionrow->amount;
  }
   if($u==0 && $payment_parts>1)
   {
    $st = $excel_row + $payment_parts - 1;
    $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
    $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
    $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
    $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
    $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
    $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
    $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
    $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
    $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
    $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
    $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
    $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
    $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
    $object->getActiveSheet()->mergeCells('U' . $excel_row . ':U' . $st);
    $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
    $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
    $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
    $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
    $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
    $object->getActiveSheet()->mergeCells('AA' . $excel_row . ':AA' . $st);
   }




$excel_row++;
$u++;
}
$i++;
}
}


/** DEBIT NOTED **/
$resuiyuretr=$this->db->select('a.payment_date,a.balance_used,a.collection_id,a.credit_debit_amount,a.invoice_detail,a.invoice_date,a.credit_debit_for,b.collection_id as collection_name')->from('sunder_collection_credit_debit a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.credit_debit',2)->where('invoice_date>=',$start_date." 00:00:00")->where('invoice_date<=',$end_date." 23:59:59")->get();
if($resuiyuretr->num_rows()>0)
{
foreach($resuiyuretr->result() as $debit)
{


 $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );
$total_collection_amount[]=$debit->balance_used;
  // echo "<pre>"; print_r($total_collection_amount); exit;
  $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,$i);
  $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row,"19864000");
  $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row,"FARIDABAD-CFA-II");
  $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row,"Type I");
  $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row,"14194");
  $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row,$debit->credit_debit_for);
  $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row,$debit->invoice_detail);
  $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row,date('d/m/Y',strtotime($debit->invoice_date)));
  $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row,$debit->credit_debit_amount);
  $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row,$debit->balance_used);
  $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row,$debit->collection_name);
  $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row,date('d/m/Y',strtotime($debit->payment_date)));
  $i++;
  $excel_row++;
}
}
/** END **/

  $object->getActiveSheet()->getStyle("A".$excel_row.":AA".$excel_row)->getFont()->setBold(true);

 // echo "<pre>"; print_r($total_claim_amount); exit;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($total_collection_amount));
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, array_sum($total_claim_amount));
    


}


$styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
      )
    );

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
      $style_cell
    );



    $fileName = 'Type-I Claim Without Interest -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);


  }


   function type_two_claim_format_new()
  {
    
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $hpcl_location = $this->uri->segment(5);
    $current_date = date('M-Y', strtotime($start_date));

    $this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collected Amount","TDS","Difference (If Any)", "Collection Reference", "Date", "Days", "Interest Rate", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Remarks", "Annexure Link");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);

    $objWorkSheet->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AB')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AC')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AD')->setWidth(40);

    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
      $style_cell

    );

    $object->getActiveSheet()->getStyle('A1:AD1')->getFill()->getStartColor()->setRGB('FFDBE2F1');

    for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
      $objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
    }


    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

     $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
              $styleArray
            );

    // $query = $this->db->select('d.customer_tcs,d.annexture_name,d.annexture,d.invoice,a.interest_charges,d.type,a.annexure_upload,a.collection_reference,a.commision,a.gst,a.interest_charges,a.paymentOn,a.paid_amount,c.address,e.customer_code,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,b.pack_size,b.volume,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
    //   ->from('approval_product_details_type_two a')
    //   ->join('approval_form_type_two d', 'a.approval_id=d.id')
    //   ->join('presto_instruments b', 'b.id=a.product_id', 'left')
    //   ->join('hpcl_location c', 'c.id=a.location')
    //   ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
    //   ->where('a.delivered', 1)
    //   ->where('a.payment', 1);

    // if ($start_date <> '' && $end_date <> '') {
    //   $this->db->where('d.current_date>=', $start_date);
    //   $this->db->where('d.current_date<=', $end_date);
    // }

    // $this->db->order_by('d.current_date', 'ASC');
    // $query = $this->db->get();
    // print_r($query->result());
    // exit;




      $query=$this->db->select('f.volume,a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
              ->from('type_2_3_invoice_particular d')
              ->join('type_2_3_invoice a','d.invoice_id=a.id')
              ->join('presto_instruments f','f.id=d.product_id')
              ->join('hpcl_location c', 'c.id=a.shipping_from')
              ->join('hpcl_direct_customer e', 'e.id=a.customer')
              ->where('a.invoice_date>=',$start_date)
              ->where('a.invoice_date<=',$end_date);
            $this->db->order_by('a.invoice_date','ASC');
            $query=$this->db->get();




    if ($query->num_rows() > 0) {


      $i = 1;

      $excel_row = 2;
      $interest_arr = array(); 
      $interest_arr[] = 0;

      $claim_arr = array();
      $claim_arr[] = 0;
      foreach ($query->result() as $rows) {

      $SSDATE=$rows->invoice_date;
      $claim_am=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$SSDATE);
      // /echo "<pre>"; print_r($claim_am); exit;
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];
      //echo $approval_based_credit_days; exit;


      $collection_data=array();
      $collection_data[]=0;
        $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
        $payment_rows = $indata->num_rows();
        // echo $payment_rows; 
        // exit;
        $collection_array = array();
        $collection_amt = array();
        if ($indata->num_rows() > 0) {
          $l = 0;
          foreach ($indata->result() as $invdata) {
            $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(30);
            $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
              $styleArray
            );

             $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );



            if ($payment_rows > 1) {

               $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size."-".$rows->volume);
             
             if($rows->volume>0)
             {
              $newqty=$rows->pqty/$rows->volume;
             }else
             {
              $newqty=0;
             }

              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,$newqty);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
              

              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=$total_with_gst-$tds_amount+$tcs_amt;

             $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $total_with_gst);
                        
             // $diff = $tinvoice_value - $rows->paid_amount;             

             //echo $approval_based_credit_days; exit;

              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


              if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

                      
                      // USE TINVOICE VALUE IF NEEDED CHANGE 
              $collection_data[]=$invdata->collection_amount;
              
              $collectionamtdata=$this->getall_collection_amount($rows->product_approval_id);

              $collection_diff=round($tinvoice_value-$collectionamtdata,2);
 
            
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row,$tds_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $calculate_int);
          
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->pprrice);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $approved_price);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, 0);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this_invoice_comm);
             
              //echo $excel_row."<br/>".$st; exit;
              $transport_rate = $this_invoice_trans;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $transport_rate);
              
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $total_claim);
             

             
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $link);

              if($l==0)
              {
              $claim_arr[] = $total_claim;
              $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
              $object->getActiveSheet()->mergeCells('P' . $excel_row . ':P' . $st);
              $object->getActiveSheet()->mergeCells('Q' . $excel_row . ':Q' . $st);
              $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
              $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
              $object->getActiveSheet()->mergeCells('AA'.$excel_row. ':AA' . $st);
              $object->getActiveSheet()->mergeCells('AB'.$excel_row. ':AB' . $st);
              }






            } else {

             $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             // $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             // $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
              // $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
              // $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             // $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
              //$object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size."-".$rows->volume);
             // $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);

              if($rows->volume>0)
             {
              $newqty=$rows->pqty/$rows->volume;
             }else
             {
              $newqty=0;
             }

              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
              //$object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
              //$object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             // $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              // $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
             //  $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
             // $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=$total_with_gst-$tds_amount+$tcs_amt;


              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $total_with_gst);
             // $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
             
              //$diff = $tinvoice_value - $rows->paid_amount;             

            //  echo $approval_based_credit_days; exit;
              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


               if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

           
              $collection_diff=round($tinvoice_value-$invdata->collection_amount,2);
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $tds_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $calculate_int);
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->pprrice);
              //$object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $approved_price);
             // $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, 0);
              // $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this_invoice_comm);
              //$object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
             
                $transport_rate = $this_invoice_trans;
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $transport_rate);
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              $claim_arr[] = $total_claim;
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $total_claim);
              // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);
               $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $link);

            }




            $excel_row++;
           
            $l++;
          }
           $i++;
        }
      }


      //echo "<pre>"; print_r($claim_arr); exit;
      $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);

      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, array_sum($interest_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, array_sum($claim_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, '');
    

      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );
      }
    $fileName = 'Type-II Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }



   function type_two_claim_format_new_original_backup()
  {
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $hpcl_location = $this->uri->segment(5);
    $current_date = date('M-Y', strtotime($start_date));

    $this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collected Amount", "Difference (If Any)", "Collection Reference", "Date", "Days", "Interest Rate", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Remarks", "Annexure Link");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);

    $objWorkSheet->getStyle("A1:AC1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle("A1:AC1")->getFont()->setBold(true);
    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AB')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AC')->setWidth(40);

    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $objWorkSheet->getStyle("A1:AC1")->applyFromArray(
      $style_cell

    );

    $object->getActiveSheet()->getStyle('A1:AC1')->getFill()->getStartColor()->setRGB('FFDBE2F1');

    for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
      $objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
    }


    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

     $objWorkSheet->getStyle("A1:AC1")->applyFromArray(
              $styleArray
            );

    // $query = $this->db->select('d.customer_tcs,d.annexture_name,d.annexture,d.invoice,a.interest_charges,d.type,a.annexure_upload,a.collection_reference,a.commision,a.gst,a.interest_charges,a.paymentOn,a.paid_amount,c.address,e.customer_code,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,b.pack_size,b.volume,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
    //   ->from('approval_product_details_type_two a')
    //   ->join('approval_form_type_two d', 'a.approval_id=d.id')
    //   ->join('presto_instruments b', 'b.id=a.product_id', 'left')
    //   ->join('hpcl_location c', 'c.id=a.location')
    //   ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
    //   ->where('a.delivered', 1)
    //   ->where('a.payment', 1);

    // if ($start_date <> '' && $end_date <> '') {
    //   $this->db->where('d.current_date>=', $start_date);
    //   $this->db->where('d.current_date<=', $end_date);
    // }

    // $this->db->order_by('d.current_date', 'ASC');
    // $query = $this->db->get();
    // print_r($query->result());
    // exit;




      $query=$this->db->select('a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
              ->from('type_2_3_invoice_particular d')
              ->join('type_2_3_invoice a','d.invoice_id=a.id')
              ->join('presto_instruments f','f.id=d.product_id')
              ->join('hpcl_location c', 'c.id=a.shipping_from')
              ->join('hpcl_direct_customer e', 'e.id=a.customer')
              ->where('a.invoice_date>=',$start_date)
              ->where('a.invoice_date<=',$end_date);
            $this->db->order_by('a.invoice_date','ASC');
            $query=$this->db->get();




    if ($query->num_rows() > 0) {


      $i = 1;

      $excel_row = 2;
      $interest_arr = array();
      $interest_arr[] = 0;

      $claim_arr = array();
      $claim_arr[] = 0;
      foreach ($query->result() as $rows) {

      $claim_am=$this->salescrm->check_for_applicable_approvals_new($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date);
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];
      //echo $approval_based_credit_days; exit;


      $collection_data=array();
      $collection_data[]=0;
        $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
        $payment_rows = $indata->num_rows();
        // echo $payment_rows; 
        // exit;
        $collection_array = array();
        $collection_amt = array();
        if ($indata->num_rows() > 0) {
          $l = 0;
          foreach ($indata->result() as $invdata) {
            $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(30);
            $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
              $styleArray
            );

             $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );



            if ($payment_rows > 1) {

               $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,$rows->volume);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
              

              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=$total_with_gst-$tds_amount+$tcs_amt;

             $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $tinvoice_value);
                        
             // $diff = $tinvoice_value - $rows->paid_amount;             

             //echo $approval_based_credit_days; exit;

              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


              if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

                      
              $collection_data[]=$invdata->collection_amount;
              
              $collection_diff=$tinvoice_value-array_sum($collection_data);
 
            
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $calculate_int);
          
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $rows->pprrice);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $approved_price);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, 0);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $this_invoice_comm);
             
              //echo $excel_row."<br/>".$st; exit;
              $transport_rate = $this_invoice_trans;
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $transport_rate);
              
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $total_claim);
             

             
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $link);

              if($l==0)
              {
              $claim_arr[] = $total_claim;
              $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
              $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
              $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
              $object->getActiveSheet()->mergeCells('AA'.$excel_row. ':AA' . $st);
              }






            } else {

             $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             // $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             // $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
              // $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
              // $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             // $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
              //$object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size);
             // $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $rows->volume);
              //$object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
              //$object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             // $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              // $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
             //  $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
             // $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=$total_with_gst-$tds_amount+$tcs_amt;


              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $tinvoice_value);
             // $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
             
              //$diff = $tinvoice_value - $rows->paid_amount;             

            //  echo $approval_based_credit_days; exit;
              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


               if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

           
              $collection_diff=$tinvoice_value-$invdata->collection_amount;
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $invdata->collection_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $calculate_int);
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $rows->pprrice);
              //$object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $approved_price);
             // $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, 0);
              // $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $this_invoice_comm);
              //$object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
             
                $transport_rate = $this_invoice_trans;
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $transport_rate);
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              $claim_arr[] = $total_claim;
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $total_claim);
              // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);
               $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $link);

            }




            $excel_row++;
           
            $l++;
          }
           $i++;
        }
      }


     // echo "<pre>"; print_r($claim_arr); exit;
      $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);

      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, array_sum($interest_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, array_sum($claim_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, '');
    

      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );
      }
    $fileName = 'Type-II Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }

  function getall_collection_amount($inventory_id)
  {
    $d=array();
    $d[]=0;
    $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $inventory_id)->get();
    if($indata->num_rows()>0)
    {
      foreach($indata->result() as $row)
      {
        $d[]=$row->collection_amount;
      }

    }

    return array_sum($d);


  }

  function customer_ledger()
  {
    $this->load->view('approval/type_2_3_customer_ledger');
  }

   public function direct_customer_list()
  {
    $uri=$this->uri->segment(3);
    $lead_data = array();
    $this->db->select('a.*')->from('hpcl_direct_customer a');
    $query = $this->db->get();
    $res = $query->result();                  
    $i=1;
    foreach($res as $row)
    {
     
     $dow="<a href='".page_url."ExcelImport/type_2_3_customer_ledger/".$row->id."'>Download Excel File</a>";
      $lead_data[] = array('sr_no'=>$i,
      'name'=>$row->customer_name,
      'download'=>$dow
      );
      $i++;
    }
    //echo "<pre>"; print_r($compititor_data); exit;
      $results = array(
      "sEcho" => 1,
      "iTotalRecords" => count($lead_data),
      "iTotalDisplayRecords" => count($lead_data),
      "aaData"=>$lead_data);
      
    echo json_encode($results);
  }



   function type_2_3_customer_ledger()
  {
    // $current_date = date('d-m-y');
    // $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    // $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $customerid=$this->uri->segment(3);
    $start_date=$this->getfirstDateforCustomer($customerid);
    $end_date=date('Y-m-d');
    $hpcl_location = $this->uri->segment(5);

    $this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collected Amount","TDS","Difference (If Any)", "Collection Reference", "Date", "Days", "Interest Rate", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Remarks", "Annexure Link");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);

    $objWorkSheet->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AB')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AC')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AD')->setWidth(40);

    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
      $style_cell

    );

    $object->getActiveSheet()->getStyle('A1:AD1')->getFill()->getStartColor()->setRGB('FFDBE2F1');

    for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
      $objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
    }


    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

     $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
              $styleArray
            );

    // $query = $this->db->select('d.customer_tcs,d.annexture_name,d.annexture,d.invoice,a.interest_charges,d.type,a.annexure_upload,a.collection_reference,a.commision,a.gst,a.interest_charges,a.paymentOn,a.paid_amount,c.address,e.customer_code,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,b.pack_size,b.volume,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
    //   ->from('approval_product_details_type_two a')
    //   ->join('approval_form_type_two d', 'a.approval_id=d.id')
    //   ->join('presto_instruments b', 'b.id=a.product_id', 'left')
    //   ->join('hpcl_location c', 'c.id=a.location')
    //   ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
    //   ->where('a.delivered', 1)
    //   ->where('a.payment', 1);

    // if ($start_date <> '' && $end_date <> '') {
    //   $this->db->where('d.current_date>=', $start_date);
    //   $this->db->where('d.current_date<=', $end_date);
    // }

    // $this->db->order_by('d.current_date', 'ASC');
    // $query = $this->db->get();
    // print_r($query->result());
    // exit;




      $query=$this->db->select('f.volume,a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
              ->from('type_2_3_invoice_particular d')
              ->join('type_2_3_invoice a','d.invoice_id=a.id')
              ->join('presto_instruments f','f.id=d.product_id')
              ->join('hpcl_location c', 'c.id=a.shipping_from')
              ->join('hpcl_direct_customer e', 'e.id=a.customer')
              ->where('a.customer',$customerid);
              // ->where('a.invoice_date>=',$start_date)
              // ->where('a.invoice_date<=',$end_date);
            $this->db->order_by('a.invoice_date','ASC');
            $query=$this->db->get();




    if ($query->num_rows() > 0) {


      $i = 1;

      $excel_row = 2;
      $interest_arr = array();
      $interest_arr[] = 0;

      $claim_arr = array();
      $claim_arr[] = 0;
      $diff_arr=array();
      $diff_arr[] = 0;
      $invoice_arr=array();
      $invoice_arr[] = 0;

      $collection_arr=array();
      $collection_arr[] = 0;


      foreach ($query->result() as $rows) {
        $SSDATE=$rows->invoice_date;
      $claim_am=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$SSDATE);
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];
      //echo $approval_based_credit_days; exit;


      $collection_data=array();
      $collection_data[]=0;
        $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
        $payment_rows = $indata->num_rows();
        // echo $payment_rows; 
        // exit;
        $collection_array = array();
        $collection_amt = array();
        if ($indata->num_rows() > 0) {
          $l = 0;
          foreach ($indata->result() as $invdata) {
            $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(30);
            $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
              $styleArray
            );

             $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AD" . $excel_row)->applyFromArray(
          $style_cell
        );



            if ($payment_rows > 1) {

               $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size."-".$rows->volume);
             
             if($rows->volume>0)
             {
              $newqty=$rows->pqty/$rows->volume;
             }else
             {
              $newqty=0;
             }

              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row,$newqty);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
              

              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=round($total_with_gst-$tds_amount+$tcs_amt,2);
             $invoice_arr[]=round($total_with_gst-$tds_amount+$tcs_amt,2);

             $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $tinvoice_value);
                        
             // $diff = $tinvoice_value - $rows->paid_amount;             

             //echo $approval_based_credit_days; exit;

              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


              if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

                      
              $collection_data[]=round($invdata->collection_amount,2);
              $collection_arr[]=round($invdata->collection_amount,2);
              
              $collectionamtdata=round($this->getall_collection_amount($rows->product_approval_id),2);

              $collection_diff=round($tinvoice_value-$collectionamtdata,2);

 
            
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, round($invdata->collection_amount,2));
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row,$tds_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $calculate_int);
          
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->pprrice);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $approved_price);
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, 0);
               
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this_invoice_comm);
             
              //echo $excel_row."<br/>".$st; exit;
              $transport_rate = $this_invoice_trans;
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $transport_rate);
              
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $total_claim);
             

             
              
              $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $link);

              if($l==0)
              {
              $claim_arr[] = $total_claim;
              $diff_arr[]=$collection_diff;
              $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
              $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
              $object->getActiveSheet()->mergeCells('P' . $excel_row . ':P' . $st);
              $object->getActiveSheet()->mergeCells('Q' . $excel_row . ':Q' . $st);
              $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
              $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
              $object->getActiveSheet()->mergeCells('AA'.$excel_row. ':AA' . $st);
              }






            } else {

             $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             // $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             // $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
              // $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
              // $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             // $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
              //$object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size."-".$rows->volume);
             // $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);

              if($rows->volume>0)
             {
              $newqty=$rows->pqty/$rows->volume;
             }else
             {
              $newqty=0;
             }

              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
              //$object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
              //$object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             // $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              // $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
             //  $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
             // $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $total = round($rows->pprrice * $rows->pqty,2);
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=round($total_with_gst-$tds_amount+$tcs_amt,2);
             $invoice_arr[]=round($total_with_gst-$tds_amount+$tcs_amt,2);


              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $tinvoice_value);
             // $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
             
              //$diff = $tinvoice_value - $rows->paid_amount;             

            //  echo $approval_based_credit_days; exit;
              $now = date('Y-m-d', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days"));
              $your_date = date('Y-m-d', strtotime($invdata->pur_paymentOn));


              $datediff = strtotime($your_date) - strtotime($now);
              $days = round($datediff / (60 * 60 * 24));


               if ($days > 0) {
      
                /** CACULATE INTEREST **/
                $interest = $rows->interest_charges;
                $interest = $interest / 100;
                $calculate_int = ($tinvoice_value * $interest) / 365;
                $calculate_int = round($calculate_int * abs($days), 2);
               
                /** END **/
              } else {
                $calculate_int = 0;
              }

           
             $collection_arr[]=round($invdata->collection_amount,2);
              $collection_diff=round($tinvoice_value-$invdata->collection_amount,2);
              $diff_arr[]=$collection_diff;
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, round($invdata->collection_amount,2));
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $tds_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $invdata->collection_name);
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, date('d/m/y', strtotime($invdata->pur_paymentOn)));
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $calculate_int);
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->pprrice);
              //$object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $approved_price);
             // $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, 0);
              // $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this_invoice_comm);
              //$object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
             
                $transport_rate = $this_invoice_trans;
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $transport_rate);
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              $claim_arr[] = $total_claim;
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $total_claim);
              // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);
               $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $link);

            }




            $excel_row++;
           
            $l++;
          }
           $i++;
        }
      }


      /** GET TQ DATA **/
      $i=$i;
      $retq=$this->db->select('*')->from('customer_tq')->where('customer_id',$customerid)->get();
      if($retq->num_rows()>0)
      {
      foreach($retq->result() as $rowtq)
      {
        $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);


         $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row,$i);
      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
    $collection_arr[]=$rowtq->tq_amount;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $rowtq->tq_amount);
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, $rowtq->tq_ref);
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, date('d/mY',strtotime($rowtq->tq_date)));


 $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );

     $excel_row++;
     $i++;

    } 
    }

      /** END **/

     // echo "<pre>"; print_r($claim_arr); exit;
      $diff_data=array_sum($invoice_arr)-array_sum($collection_arr);
      $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);

      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, array_sum($invoice_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($collection_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
      //$object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, array_sum($diff_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $diff_data);
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row,'');
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, array_sum($interest_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, array_sum($claim_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, '');
    

      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );
      }

     // / echo "<pre>"; print_r($collection_arr); exit;
    $fileName = 'Customer_Legder_'.$this->uri->segment(3).'.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/customer_ledger/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }

  function getfirstDateforCustomer($customerid)
  {
    $startdate=date('Y-m-d');
    $retyt=$this->db->select('invoice_date')->from('type_2_3_invoice')->where('customer',$customerid)->order_by('id','ASC')->limit(1)->get();

    if($retyt->num_rows()>0)
    {
      foreach($retyt->result() as $row);
      $startdate=date('Y-m-d',strtotime($row->invoice_date));
    }

    return $startdate;

  }



  function type_one_claim_with_interest_debit_note()
  {

      $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
     $location = $this->uri->segment(5);
     $flag = $this->uri->segment(6);

     $this->load->library("Excel");
    $object = new PHPExcel();
    $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collection", "Collection Reference", "Date", "Days", "Interest %", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm. Per ltr/kg", "TOTAL AMT OF COMM.", "Approvals", "Evidence");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle('A1:AA1')->applyFromArray(
      array(
        'fill' => array(
          'type' => PHPExcel_Style_Fill::FILL_SOLID,
          'color' => array('rgb' => '9fbfdf')
        )
      )
    );

     $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(80);

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
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('21')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('22')->setRowHeight(20);

      for ($i = 'A'; $i !=  $object->getActiveSheet()->getHighestColumn(); $i++) {
      $object->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }

 $customer_code = $this->get_sunder_code();
 $excel_row = 2;

 $total_claim_amount=array();
 $total_claim_amount[]=0;
 $total_interest_amount=array();
 $total_interest_amount[]=0;
 $total_collection_amount=array();
 $total_collection_amount[]=0;
  $reste=$this->db->select('b.volume,a.product,d.credit_days,e.hpcl_location,d.interest,d.payment_type,d.pur_payment,d.bill_no,e.name as vendor_name,d.gst as gstrate,d.currentdate,a.id, a.product,a.pack_size,a.qty,a.original_qty,a.rate,a.lot_no,a.batch_no,a.manufacturing_date,b.instruments_name,c.shortname,b.pack_size as prd_pack_size')->from('inventory_details a')->join('presto_instruments b','a.product=b.id')->join('units c','a.pack_size=c.id')->join('inventory d','a.inventory_id=d.id')->join('vendors e','d.party=e.id')->where('d.currentdate>=',$start_date)->where('d.currentdate<=',$end_date)->where('d.hpcl_billing_company',3)->where('a.payment',1)->get();
  if($reste->num_rows()>0)
  {
   
     $i=1;
    foreach($reste->result() as $row)
    {

      $location_code=$this->salescrm->getHpclCompanyCode($row->hpcl_location);
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

        /** GST CALCULATION **/
        $total=$row->rate*$row->qty;
        $gstrate=$row->gstrate/100;
        $total_gst=$total*$gstrate;
        $grandtotal=$total+$total_gst;
        /** END **/


        $collection_details=$this->salescrm->getcollection_details($row->id,$due_date,$row->interest,$row->rate,$row->product,$row->currentdate,$row->hpcl_location);
        $collections=explode('|',$collection_details);
        $payment_parts=$collections[2];

/** CHECK FOR APPROVAL **/
$approvals_data=$this->salescrm->getApprovalForPurchase($row->product,$row->currentdate,$row->hpcl_location);
$appdata=explode('|',$approvals_data);
$app_id=$appdata[0];
$app_rate=$appdata[1];
$type=$appdata[2];
$moq=$appdata[3];
$credit=$appdata[4];
/** NEW **/
$credit=$row->rate-$app_rate+$credit;

$validity_from=$appdata[5];
$validity_to=$appdata[6];
$annexture=$appdata[7];
$annexure_upload=$appdata[8];
if($annexure_upload!='')
{
  $annex_link=page_url1."type_one_annexure/".$annexure_upload;
}else
{
  $annex_link='';
}
      


        /** GET COLLECTION DATA **/

$interest=$row->interest;
$billing_price=$row->rate;
$product=$row->product;
$purchase_date=$row->currentdate;
$hpcl_location=$row->hpcl_location;

    $resty=$this->db->select('a.*,b.collection_id as collection_ref')->from('inventory_payment_details_product_wise a')->join('sunder_collection_reference b','a.collection_id=b.id')->where('a.inventory_details_id',$row->id)->order_by('id','ASC')->get();
    if($resty->num_rows()>0)
    {
    $u=0;
    foreach($resty->result() as $collectionrow)
    {

      $due=date('Y-m-d',strtotime($due_date));
            $pay_date=date('Y-m-d',strtotime($collectionrow->payment_date));

            $start = strtotime($due);
            $end = strtotime($pay_date);
            $days_between = ceil($end - $start) / 86400;


            $approval_data=$this->salescrm->getApprovalForPurchase($row->id,$row->currentdate,$row->hpcl_location);
            $appdata=explode('|',$approvals_data);
        $app_id=$appdata[0];
        $app_rate=$appdata[1];

        $type=$appdata[2];
        $moq=$appdata[3];
        $credit=$appdata[4];
        /** NEW **/
        $credit=$row->rate-$app_rate+$credit;
        $validity_from=$appdata[5];
        $validity_to=$appdata[6];

            $rate_diff=$billing_price-$app_rate;

            $total_interest=0;
            if($days_between>0)
            {
            /** CACULATE INTEREST **/
            $interest_rate = $interest;
            $interest_rate = $interest_rate / 100;
            $calculate_int = ($collectionrow->amount * $interest_rate) / 365;
            $calculate_int = round($calculate_int * $days_between, 2);

            $total_interest = $total_interest + $calculate_int;
            }else
            {
              $total_interest=0;
            }


              $errors='';
        if($type==0)
        {
          /** CHECK FOR MOQ **/
          if($moq>0)
          {
          $moq_fullfilled=$this->salescrm->checkMOQFullfilled($validity_from,$validity_to,$moq,$product,$row->hpcl_location);
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



         $styleArray = array(
                      'borders' => array(
                        'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                      )
                    );

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
                    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

                    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
                      $style_cell
                    );


if($row->volume<>'' && $row->volume<>0)
                  {
                    $newqty=$row->qty/$row->volume;
                  }else
                  {
                    $newqty=0;
                  }


    if($credit<0)
    {
   $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
   $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $customer_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'FARIDABAD-CFA-II');
   $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Type I');
   $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $location_code);
   $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, strtoupper($row->instruments_name));
   $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->prd_pack_size."-".$row->volume);
   $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->qty);
   $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $row->bill_no);
   $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y',strtotime($row->currentdate)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $row->credit_days);
   $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/Y',strtotime($due_date)));
   

   $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $collectionrow->amount);
   $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $collectionrow->collection_ref);
   $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, date('d/m/Y',strtotime($collectionrow->payment_date)));
   $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row,$days_between);
   $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, $interest);
   $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $total_interest);
   $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $billing_price);
   $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $app_rate);
   $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rate_diff);
   $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $credit);
   $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, $claim);
   $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $annexture);
   $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $annex_link);
   if($payment_parts>1)
   {
    if($u==0)
    {
  
   $total_claim_amount[]=$claim;
   
    }

     $total_interest_amount[]=$total_interest;
     $total_collection_amount[]=$collectionrow->amount;
  }else
  {
     $total_interest_amount[]=$total_interest;
   $total_claim_amount[]=$claim;
   $total_collection_amount[]=$collectionrow->amount;
  }
   if($u==0 && $payment_parts>1)
   {
    $st = $excel_row + $payment_parts - 1;
    $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
    $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
    $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
    $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
    $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
    $object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
    $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);
    $object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
    $object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
    $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
    $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
    $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
    $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
    $object->getActiveSheet()->mergeCells('U' . $excel_row . ':U' . $st);
    $object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
    $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
    $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
    $object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
    $object->getActiveSheet()->mergeCells('Z' . $excel_row . ':Z' . $st);
    $object->getActiveSheet()->mergeCells('AA' . $excel_row . ':AA' . $st);
   }




$excel_row++;
$u++;
}
$i++;
}
}
}



  $object->getActiveSheet()->getStyle("A".$excel_row.":AA".$excel_row)->getFont()->setBold(true);

 // echo "<pre>"; print_r($total_claim_amount); exit;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($total_collection_amount));
     // $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, array_sum($total_claim_amount));
    
    

  $object->getActiveSheet()->getStyle("A".$excel_row.":AA".$excel_row)->getFont()->setBold(true);

  //echo "<pre>"; print_r($total_collection_amount); exit;
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, array_sum($total_collection_amount));
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, array_sum($total_interest_amount));
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, array_sum($total_claim_amount));
    


}


$styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
      )
    );

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray($styleArray);
    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $object->getActiveSheet()->getStyle("A" . $excel_row . ":AA" . $excel_row)->applyFromArray(
      $style_cell
    );

    $fileName = 'Type-I Debit_Note -' . $start_date . '-' . $end_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    if($flag=='')
    {

    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
    
    }


  }
  




 function type_two_claim_format_new_internal_working()
  {
    
    // $current_date = date('d-m-y');
    $start_date = date('Y-m-d', strtotime($this->uri->segment(3)));
    $end_date = date('Y-m-d', strtotime($this->uri->segment(4)));
    $hpcl_location = $this->uri->segment(5);
    $current_date = date('M-Y', strtotime($start_date));

    $this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(1);
    $object->setActiveSheetIndex(0);
    $table_columns = array("S.No.", "Customer Code", "Customer Name", "Type Wise", "Location", "Product", "Pack Size", "Qty", "Qty in Ltrs.", "Invoice No.", "Date", "Credit Period", "Due Date", "Product Value", "Collected Amount","TDS","Difference (If Any)", "Collection Reference", "Date", "Days", "Interest Rate", "Interest", "Billing Price", "CSRA Approved", "Difference", "Comm./Ltr", "Tpt./Ltrs", "Total Claim.", "Remarks", "Annexure Link");
    $column = 0;
    $object->getActiveSheet()->getStyle("A1:AA1")->getFont()->setBold(true);

    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('11')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('12')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('13')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('14')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('15')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('16')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('17')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('18')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('19')->setRowHeight(20);
    $object->getActiveSheet()->getRowDimension('20')->setRowHeight(20);

    $objWorkSheet->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getActiveSheet()->getStyle("A1:AD1")->getFont()->setBold(true);
    $object->getDefaultStyle()->getAlignment()->setWrapText(true);
    $object->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $object->getActiveSheet()->getColumnDimension('D')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('E')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('F')->setWidth(100);
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
    $object->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('R')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('S')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('T')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('U')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('V')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('W')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('X')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Y')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('Z')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AA')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AB')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AC')->setWidth(40);
    $object->getActiveSheet()->getColumnDimension('AD')->setWidth(40);

    $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

    $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
      $style_cell

    );

    $object->getActiveSheet()->getStyle('A1:AD1')->getFill()->getStartColor()->setRGB('FFDBE2F1');

    for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
      $objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
    }


    foreach ($table_columns as $field) {
      $object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
      $column++;
    }


        $styleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
            )
          )
        );

     $objWorkSheet->getStyle("A1:AD1")->applyFromArray(
              $styleArray
            );

    // $query = $this->db->select('d.customer_tcs,d.annexture_name,d.annexture,d.invoice,a.interest_charges,d.type,a.annexure_upload,a.collection_reference,a.commision,a.gst,a.interest_charges,a.paymentOn,a.paid_amount,c.address,e.customer_code,a.id as product_approval_id,a.pack_size,a.approved_price, a.price_validity,a.validity_from,a.validity_to,a.credit_days, a.commision, b.instruments_name,b.unit,b.pack_size,b.volume,a.location,c.name,a.transport_type,a.transport_rate,d.id, d.auto_gen_code,d.current_date,e.customer_name,a.deliveredOn,a.deliveredQty,a.transporter_from,a.vehicle_type,a.vehicle_no,a.transporter_id,a.transporter_rate_type,a.transporter_fixed_rate,a.transporter_payment')
    //   ->from('approval_product_details_type_two a')
    //   ->join('approval_form_type_two d', 'a.approval_id=d.id')
    //   ->join('presto_instruments b', 'b.id=a.product_id', 'left')
    //   ->join('hpcl_location c', 'c.id=a.location')
    //   ->join('hpcl_direct_customer e', 'e.id=d.customer_name')
    //   ->where('a.delivered', 1)
    //   ->where('a.payment', 1);

    // if ($start_date <> '' && $end_date <> '') {
    //   $this->db->where('d.current_date>=', $start_date);
    //   $this->db->where('d.current_date<=', $end_date);
    // }

    // $this->db->order_by('d.current_date', 'ASC');
    // $query = $this->db->get();
    // print_r($query->result());
    // exit;




      $query=$this->db->select('f.volume,a.credit_days,a.type,a.customer,a.invoice_no,a.invoice_date,d.product_id,a.shipping_from,d.payment,d.id as product_approval_id,d.invoice_id,d.price as pprrice,d.qty as pqty,a.*,c.name as location_name,e.customer_name,e.customer_code,f.instruments_name,f.unit as productunit,c.address,f.pack_size,f.volume,a.tcs as customer_tcs,a.interest as interest_charges')
              ->from('type_2_3_invoice_particular d')
              ->join('type_2_3_invoice a','d.invoice_id=a.id')
              ->join('presto_instruments f','f.id=d.product_id')
              ->join('hpcl_location c', 'c.id=a.shipping_from')
              ->join('hpcl_direct_customer e', 'e.id=a.customer')
              ->where('a.invoice_date>=',$start_date)
              ->where('a.invoice_date<=',$end_date);
            $this->db->order_by('a.invoice_date','ASC');
            $query=$this->db->get();




    if ($query->num_rows() > 0) {


      $i = 1;

      $excel_row = 2;
      $interest_arr = array(); 
      $interest_arr[] = 0;

      $claim_arr = array();
      $claim_arr[] = 0;
      foreach ($query->result() as $rows) {

      $SSDATE=$rows->invoice_date;
      $claim_am=$this->salescrm->check_for_applicable_approvals_new_one($rows->product_id,$rows->invoice_date,$rows->shipping_from,0,$rows->customer,$rows->type,$start_date,$end_date,$SSDATE);
      // /echo "<pre>"; print_r($claim_am); exit;
      $claim_amm=explode('|',$claim_am);

      $this_invoice_comm=$claim_amm[1];
      $this_invoice_trans=$claim_amm[2];
      $approved_price=$claim_amm[3];
      $annexture_name=$claim_amm[4];
      $annexture_file=$claim_amm[5];
      $approval_based_credit_days=$claim_amm[6];
      //echo $approval_based_credit_days; exit;


      $collection_data=array();
      $collection_data[]=0;
        // $indata = $this->db->select('a.collection_id,a.collection_amount,a.pur_paymentOn,b.collection_id as collection_name')->from('customer_inventory_payment_details a')->join('customer_collection_reference b', 'b.id=a.collection_id')->where('a.inventory_id', $rows->product_approval_id)->get();
         $payment_rows =1;
        // echo $payment_rows; 
        // exit;
        $collection_array = array();
        // $collection_amt = array();
        // if ($indata->num_rows() > 0) {
          $l = 0;
          // foreach ($indata->result() as $invdata) {
            $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(30);
            $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
              $styleArray
            );

             $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );



            

             $st = $excel_row + $payment_rows - 1;
              $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $i);
             // $object->getActiveSheet()->mergeCells('A' . $excel_row . ':A' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $rows->customer_code);
             // $object->getActiveSheet()->mergeCells('B' . $excel_row . ':B' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $rows->customer_name);
              // $object->getActiveSheet()->mergeCells('C' . $excel_row . ':C' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $rows->type);
              // $object->getActiveSheet()->mergeCells('D' . $excel_row . ':D' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $rows->address);
             // $object->getActiveSheet()->mergeCells('E' . $excel_row . ':E' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $rows->instruments_name);
              //$object->getActiveSheet()->mergeCells('F' . $excel_row . ':F' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $rows->pack_size."-".$rows->volume);
             // $object->getActiveSheet()->mergeCells('G' . $excel_row . ':G' . $st);

              if($rows->volume>0)
             {
              $newqty=$rows->pqty/$rows->volume;
             }else
             {
              $newqty=0;
             }

              $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $newqty);
              //$object->getActiveSheet()->mergeCells('H' . $excel_row . ':H' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $rows->pqty);
              //$object->getActiveSheet()->mergeCells('I' . $excel_row . ':I' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, $rows->invoice_no);
             // $object->getActiveSheet()->mergeCells('J' . $excel_row . ':J' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, date('d/m/Y', strtotime($rows->invoice_date)));
              // $object->getActiveSheet()->mergeCells('K' . $excel_row . ':K' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $approval_based_credit_days);
             //  $object->getActiveSheet()->mergeCells('L' . $excel_row . ':L' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, date('d/m/y', strtotime($rows->invoice_date . " +" . $approval_based_credit_days . " Days")));
             // $object->getActiveSheet()->mergeCells('M' . $excel_row . ':M' . $st);
              $total = $rows->pprrice * $rows->pqty;
              $gst = $rows->gst / 100;
              $total_g = $total * $gst;
              $total_with_gst = $total + $total_g;

              $tcs_amt=0;
              $tsd_amt=0;
              /** TDS ON BASIC AMOUNT **/
              if($rows->tds>0)
              {
              $tds=$rows->tds;
              $tds_amount=$total*($tds/100);
              }else
              {
              $tds="NA";
              $tds_amount=0;
              }

            /** TCS ON INCLUDING TAX VALUE **/
              if($rows->customer_tcs>0)
              {
                $tcs=$rows->customer_tcs/100;
                $tcs_amt=$total_with_gst*$tcs;
              }

             $tinvoice_value=$total_with_gst-$tds_amount+$tcs_amt;


              $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $total_with_gst);
             // $object->getActiveSheet()->mergeCells('N' . $excel_row . ':N' . $st);
             
              //$diff = $tinvoice_value - $rows->paid_amount;             

            //  echo $approval_based_credit_days; exit;
            


             
              $days = 0;
              $calculate_int = 0;
            

           
              $collection_diff=0;
              $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row,0);
              $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, $tds_amount);
              $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, $collection_diff);
              $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row,'');
              $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row,'');
              $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, $days);
              $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, $rows->interest_charges . "%");
              $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, $calculate_int);
              $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, $rows->pprrice);
              //$object->getActiveSheet()->mergeCells('V' . $excel_row . ':V' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, $approved_price);
             // $object->getActiveSheet()->mergeCells('W' . $excel_row . ':W' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, 0);
              // $object->getActiveSheet()->mergeCells('X' . $excel_row . ':X' . $st);
              $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, $this_invoice_comm);
              //$object->getActiveSheet()->mergeCells('Y' . $excel_row . ':Y' . $st);
             
                $transport_rate = $this_invoice_trans;
             
              $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, $transport_rate);
              $total_com = $rows->pqty * $this_invoice_comm;
              $total_transport = $rows->pqty * $this_invoice_trans;
              $total_claim = $total_com + $total_transport;
              $claim_arr[] = $total_claim;
              $interest_arr[] = $calculate_int;
              $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, $total_claim);
              // $object->getActiveSheet()->mergeCells('AA' . $excel_row . 'AA' . $st);
               $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, $annexture_name);
              if ($annexture_file <> '') {
                $link = page_url1 . "type_two_annexure/" . $annexture_file;
              } else {
                $link = '';
              }
              $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, $link);

            




            $excel_row++;
           
            $l++;
            $i++;
          }
           
      

      //echo "<pre>"; print_r($claim_arr); exit;
      $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(40);

      $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(15, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(16, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(17, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(18, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(19, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(20, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(21, $excel_row, array_sum($interest_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(22, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(23, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(24, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(25, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(26, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(27, $excel_row, array_sum($claim_arr));
      $object->getActiveSheet()->setCellValueByColumnAndRow(28, $excel_row, '');
      $object->getActiveSheet()->setCellValueByColumnAndRow(29, $excel_row, '');
    

      $styleArray = array(
        'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
        )
      );

      $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
        $styleArray
      );
      $style_cell = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));

        $objWorkSheet->getStyle("A" . $excel_row . ":AC" . $excel_row)->applyFromArray(
          $style_cell
        );
      }
    $fileName = 'Internal_Type-II Claim -' . $current_date . '.xls';
    $savepath = $_SERVER['DOCUMENT_ROOT'] . '/this_month_claim/' . $fileName;
    $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
    $object_writer->save($savepath);
    header("Content-type:application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename=' . $fileName);
    readfile($savepath);
  }




   public function uploaddataNew()
  {
     
  require('library/php-excel-reader/excel_reader2.php');
    require('library/SpreadsheetReader.php');
  
  $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.oasis.opendocument.spreadsheet','text/csv'];
  

  //echo $_FILES["creditfile"]["type"]; exit;
  if(in_array($_FILES["creditfile"]["type"],$mimes))
  {
  $source=$this->input->post('leadsoruce');
  //$assign=$this->input->post('assign');
  $exhibitionname = $this->input->post('exhibitionname');
  $opportunitytype = $this->input->post('opportunitytype');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $uploadFilePath = exceluploads.basename($_FILES['creditfile']['name']);
    move_uploaded_file($_FILES['creditfile']['tmp_name'], $uploadFilePath);
  $Reader = new SpreadsheetReader($uploadFilePath);
  $totalSheet = count($Reader->sheets());


    /* For Loop for all sheets */
    for($i=0;$i<$totalSheet;$i++){
      $Reader->ChangeSheet($i);
       $counter = array();
       $k=0;
      foreach ($Reader as $key=>$Row)
      {   
      
      if($Row[1]<>'' && $Row[2]<>'' && $Row[3]<>'')
      {

        if($k<>0)
        {      

         
      $designation=isset($Row[1]) ? $Row[1] : '';
      $name=isset($Row[2]) ? $Row[2] : '';
      $companyname=isset($Row[3]) ? $Row[3] : '';
      $brandname=isset($Row[4]) ? $Row[4] : '';
      $country=isset($Row[5]) ? $Row[5] : ''; 
      $jobtitle=isset($Row[6]) ? $Row[6] : '';
      $emailid=isset($Row[7]) ? $Row[7] : '';
      $mobileno=isset($Row[8]) ? $Row[8] : '';       
      $remarks=isset($Row[9]) ? $Row[9] : ''; 
     
      $q = $this->db->select('country_id,country_name')->from('countries')->like('country_name',$country,'both')->get();
      if($q->num_rows()>0){
        foreach($q->result() as $countryinfo);
        $countryid = $countryinfo->country_id;
      }else{
        $countryid= 0;
      }
      $q= $this->db->select('id')->from('customer_detail')->like('company_name',$companyname,'both')->get();
      if($q->num_rows()>0){
        foreach($q->result() as $companyinfo);
        $companyid = $companyinfo->id;
        $customertype = 2;
      }else{
        $data = array('customer_name'=>$name,
          'customer_designation'=>$jobtitle,
          'title'=>$designation,
          'email'=>$emailid,
          'contact_no'=>$mobileno,
          'country'=>$countryid,
          'company_name'=>$companyname,
          'added_on'=>date('Y-m-d'),
          'added_by'=>$_SESSION['logged_in']['user_id']);
        $this->db->insert('customer_detail',$data);
        $companyid = $this->db->insert_id();
        $this->sync_customer_to_sap('marketing', $companyid);
        $customertype = 1;

      }
      $producttype = 1;
      $uniquecode = $this->runtimegenerateOppNo($opportunitytype,$producttype);
     
        $q= $this->db->select('id')->from('company_brand')->like('name',$brandname,'both')->get();
      if($q->num_rows()>0){
        foreach($q->result() as $brandinfo);
        $brandid = $brandinfo->id;
      }else{
        $data22 = array('name'=>$brandname);
        $this->db->insert('company_brand',$data22);
        $brandid = $this->db->insert_id();
      }



        $data=array(
        'lead_source_id'=>$source,
        'patient_type_id'=>$opportunitytype,
        'create_date'=>date('Y-m-d'),
        'company_name'=>$companyid,
        'machine_type'=>1,
        'brand'=> $brandid,
        'unique_id'=>$uniquecode[0],
        'unique_no'=>$uniquecode[1],
        'country'=>$country,
        'added_on'=>date('Y-m-d H:i:s'),
        'customise_remarks'=>$remarks,
        'customer_type'=>$customertype,
        'exhibition'=>$exhibitionname,
        'probability'=>50,
        'added_by'=>$_SESSION['logged_in']['user_id']);
        $this->db->insert('leads',$data);
        $lid = $this->db->insert_id();
        /* LEAD PRODUCTS */
      $data_prod = array(
      'lead_id' => $lid,
      'product_id' =>24,
      'qty' =>1,
      'added_on'=>date('Y-m-d H:i:s'),
      'added_by'=>$_SESSION['logged_in']['user_id']);
      $this->db->insert('lead_products',$data_prod);
      /* END */

      /* PROGRESS REMARKS */
      $initalstep=$this->getintialstep();
      $datap = array(
              'lead_id' => $lid,
              'added_on'=>date('Y-m-d H:i:s'),
              'added_by'=>$_SESSION['logged_in']['user_id'],
              'lead_status' => $initalstep,
              'next_follow_date' =>date('Y-m-d', strtotime("+1 day"))
              );

      $this->db->insert('progress_remarks',$datap);

        }
              
        
          
      }
    
              $k++;
          }
      }


      // if(count($counter) >0){
      //    $data['totalneglected'] = array_sum($counter);
      //    }else{  
      //    $data['totalneglected'] = '';      
      //    }


      $this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
      redirect(page_url.'ExcelImport/excel_import');
      
    }else
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger">Your data is not in correct format. Kindly check and try again</div>');
        redirect(page_url.'ExcelImport/excel_import');
    
    }
  
  // if(!empty($data['totalneglected'])){
  // $this->session->set_flashdata('message1','<div class="alert alert-info">Total  '.$data['totalneglected'].' Entries from the excel already exists in the system, hence are skipped from the import</div>');
  // }

  
  
  }

  function lead_assign_feature($zone)
  {
        /** GET WHOM TO ASSIGN **/
        $userid=0;
        //$zone=3;
        $zone=$zone;
        $rest=$this->db->select('userid')->from('saleszoneusers')->where('zoneid',3)->order_by('id','DESC')->get();
        if($rest->num_rows()>0)
        {
        if($rest->num_rows()==1)
        {
        foreach($rest->result() as $restt);
        $user_id=$restt->userid;
        }else
        {
        $users=array();
       
        foreach($rest->result() as $restt){
        $users[]=$restt->userid;
        }

        if(count($users)>0)
        {
          $users_data = "'" . implode ( "', '", $users ) . "'";
          $TY=$this->db->query("SELECT COUNT(id) as count_rows,member_id FROM lead_assigned_to_team_member where member_id IN(".$users_data.") GROUP BY member_id ORDER BY count_rows ASC");
          if($TY->num_rows()>0)
          {
            foreach($TY->result() as $tyu);
            $userid=$tyu->member_id;

          }
  
        }


        }
        }

        return $userid;

  }

function getintialstep()
  {
    $initiallead=0;
    $row=$this->db->select('lead_id')->from('lead_stage')->order_by('sort_order','ASC')->limit(1)->get();
    if($row->num_rows()>0)
    {
      foreach($row->result() as $rowss);

      $initiallead=$rowss->lead_id;

    }


    return $initiallead;
  }


  public function uploadbomdata()
  {
     
  require('library/php-excel-reader/excel_reader2.php');
    require('library/SpreadsheetReader.php');
  
  $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.oasis.opendocument.spreadsheet','text/csv'];
  $fn = $_FILES["creditfile"]["name"];
  $uploadedfilename = str_replace(' ', '_',$fn);
  
  if(in_array($_FILES["creditfile"]["type"],$mimes))
  {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $uploadFilePath = exceluploads.basename($_FILES['creditfile']['name']);
    move_uploaded_file($_FILES['creditfile']['tmp_name'], $uploadFilePath);
    $Reader = new SpreadsheetReader($uploadFilePath);
    $totalSheet = count($Reader->sheets());


    /* For Loop for all sheets */
    for($i=0;$i<$totalSheet;$i++){

      $Reader->ChangeSheet($i);
       $counter = array();

       $k=0;

      foreach ($Reader as $key=>$Row)
          {   
            
      //echo "<pre>"; print_r($Row); exit;      
      if($Row[1]<>'' && $Row[2]<>'')
      {
        
        $bomcode=isset($Row[0]) ? $Row[0] : '';
        $description=isset($Row[1]) ? $Row[1] : '';
        $qty=isset($Row[2]) ? $Row[2] : '';
        $rev=isset($Row[3]) ? $Row[3] : '';
        $parttype=isset($Row[4]) ? $Row[4] : '';
        $source=isset($Row[5]) ? $Row[5] : ''; 
        
        
        $addedby=$this->session->userdata['logged_in']['user_id'];
        $feededOn=date('Y-m-d H:i:s');
        if($k<>0)
        {         
        
        // Condition 1
        if (substr($bomcode, 0, 3) === "DPL") {
        $parttype = "DPL";
        $bomcode = $bomcode;
        $source = "INHOUSE";
        }

        elseif($source=='BOP-H'){
          $parttype = "PART";
          $bomcode = $bomcode;
           $source = "BOP-H";

        }
        // Condition 2
        elseif ($source === "INHOUSE" && substr($bomcode, 0, 3) === "M11") {
        $parttype = "PART";
        $source = "BOP-M";
        $bomcode = $bomcode;
        }
        // Condition 3
        elseif (($source === "BOP-M" || $source === "INHOUSE" || $source === "In House") && substr($bomcode, 0, 3) !== "M11") {
        if (strpos($bomcode, "M11") !== false) {
        // M11 found in $bomcode
        // Implement your logic here
          $bomcode = "M11".$bomcode;
          $parttype = $parttype;
          $source = $source;
        }
        }
        // Condition 4
        elseif (substr($bomcode, 0, 2) === "A0" || substr($bomcode, 0, 2) === "B0" || substr($bomcode, 0, 2) === "C0") {
        $parttype = "PART";
        $source = "BOP-M";
        $bomcode = "M11".$bomcode;
        }

        elseif (substr($bomcode, 0, 2) === "A1" || substr($bomcode, 0, 2) === "B1" || substr($bomcode, 0, 2) === "C1") {
        $parttype = "PART";
        $source = "BOP-M";
        $bomcode = "M11".$bomcode;
        }
        if (substr($bomcode, 0, 3) === "M11" && $source=='BOP-M' || $parttype=='')  {
        $parttype = "PART";
        $bomcode = $bomcode;
        $source = "BOP-M";
        }

      if (strpos($description, "'") !== false) {
        $description = str_replace("'", "-", $description);
      }else{
        $description = $description;
      }

      // if(substr($bomcode, 0, 3) !== "M11" && substr($bomcode, 0, 3)){

      // }

      if(substr($bomcode, 0, 4) == "S110" || substr($bomcode, 0, 4) == "S111" || substr($bomcode, 0, 4) == "S100"){
         $parttype = "DPL";
        $bomcode = $bomcode;
        $source = "INHOUSE";
      }
      if(substr($bomcode, 0, 3) == "SG-"){
         $parttype = "PART";
        $bomcode = $bomcode;
        $source = "BOP-E";
      }
      
      
        $table = "bomdata_correction";
      

        $data = array('bomcode'=>$bomcode,
          'description'=>$description,
          'qty'=>$qty,
          'rev'=>$rev,
          'parttype'=>$parttype,
          'source'=>$source,
          'added_on'=>date('Y-m-d H:i:s'));
       // echo "<pre>"; print_r($data); exit;
        $this->db->insert($table,$data);

      }
              
        
          
      }
        
       $k++;
          }
      }


  $table_columns = array( 
    0 =>'BackOfficeCode', 
    1=>'Description',
    2=>'QTY',
    3=>'REV',
    4=>'PARTTYPE',
    5=>'SOURCE');


$this->load->library("Excel");
    $object = new PHPExcel();
    $objWorkSheet = $object->createSheet(0); //Setting index when creating
    // $objWorkSheet->setActiveSheetIndex(0);
    $objWorkSheet->setTitle("EXPORT FILE");

    $objWorkSheet->getDefaultStyle()->getAlignment()->setWrapText(true);
    $objWorkSheet->getColumnDimension('A')->setWidth(20);
    $objWorkSheet->getColumnDimension('B')->setWidth(30);
    $objWorkSheet->getColumnDimension('C')->setWidth(20);
    $objWorkSheet->getColumnDimension('D')->setWidth(20);
    $objWorkSheet->getColumnDimension('E')->setWidth(20);
    $objWorkSheet->getColumnDimension('F')->setWidth(20);
    
    $column = 0;

      for ($j = 'A'; $j !=  $objWorkSheet->getHighestColumn(); $j++) {
          //$objWorkSheet->getColumnDimension($j)->setAutoSize(TRUE);
        }
          

    foreach ($table_columns as $field) {
          $objWorkSheet->setCellValueByColumnAndRow($column, 1, $field);
          $objWorkSheet->getRowDimension($column)->setRowHeight(20);

          $styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );

          $objWorkSheet->getStyle("A1:F1")->applyFromArray(
        $styleArray );


          $column++;
        }


        $objWorkSheet->getStyle("A1:F1")->getFont()->setBold(true);
        $objWorkSheet->getStyle('A1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    ),
                                )
                            );
        $objWorkSheet->getStyle('B1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    )
                                )
                            );
        $objWorkSheet->getStyle('C1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    )
                                )
                            );
        $objWorkSheet->getStyle('D1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    )
                                )
                            );
        $objWorkSheet->getStyle('E1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    )
                                )
                            );
        $objWorkSheet->getStyle('F1')->applyFromArray(
                                array(
                                    'fill' => array(
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => '708090')
                                    )
                                )
                            );
   
  

    $excel_row = 2;
$QQ = $this->db->select('*')->from('bomdata_correction')->get();
  if($QQ->num_rows()>0)
  {
  foreach($QQ->result() as $row)
  {
  $objWorkSheet->setCellValueByColumnAndRow(0, $excel_row,$row->bomcode);
  $objWorkSheet->setCellValueByColumnAndRow(1, $excel_row,$row->description);
  $objWorkSheet->setCellValueByColumnAndRow(2, $excel_row,$row->qty);
  $objWorkSheet->setCellValueByColumnAndRow(3, $excel_row,$row->rev);
  $objWorkSheet->setCellValueByColumnAndRow(4, $excel_row,$row->parttype);
  $objWorkSheet->setCellValueByColumnAndRow(5, $excel_row,$row->source);

  $objWorkSheet->getRowDimension($excel_row)->setRowHeight(20);


  $styleArray = array('borders' => 
    array(
  'allborders' => 
  array(
  'style' => PHPExcel_Style_Border::BORDER_THIN
  )
  )
  );

  $objWorkSheet->getStyle("A".$excel_row.":F".$excel_row)->applyFromArray(
  $styleArray );


  $excel_row++;
  }
  }

  $fileName =  $uploadedfilename; 
  $savepath=SITE_ROOT.'exported_files/'.$fileName;
  $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
  $object_writer->save($savepath);
  header("Content-type:application/vnd.ms-excel");
  header('Content-Disposition: attachment; filename=' . $fileName);
  readfile( $savepath );



$this->db->truncate('bomdata_correction');


   

//$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
      //redirect(page_url.'ExcelImport/bomimport');
      
    }else
    {
        $this->session->set_flashdata('message','<div class="alert alert-danger">Your data is not in correct format. Kindly check and try again</div>');
        redirect(page_url.'ExcelImport/bomimport');
    
    }
  
  // if(!empty($data['totalneglected'])){
  // $this->session->set_flashdata('message1','<div class="alert alert-info">Total  '.$data['totalneglected'].' Entries from the excel already exists in the system, hence are skipped from the import</div>');
  // }

  
  
  }

 public function bomimport()
  {

    $this->load->view('excelimport/bomimport');
  }


 

    function clean($string) {


   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function createexhibition(){
  $exhibitionname = $this->input->post('exhibitionname');
  $q = $this->db->select('id, exhibition')->from('exhibition_info')->where('exhibition',$exhibitionname)->get();
  if($q->num_rows()>0){
       $this->session->set_flashdata('message','<div class="alert alert-danger">Sorry! This Exhibition already exist.</div>');
      redirect(page_url.'ExcelImport/excel_import');
  }else{
    $data = array('exhibition'=>$this->input->post('exhibitionname'),
      'country'=>$this->input->post('country'),
      'exhibition_date'=>date('Y-m-d',strtotime($this->input->post('exhibitiondate'))),
      'added_on'=>date('Y-m-d H:i:s'),
      'added_by'=>$_SESSION['logged_in']['user_id']);
    $this->db->insert('exhibition_info',$data);
    $this->session->set_flashdata('message','<div class="alert alert-success">Thank You! Record successfully added.</div>');
      redirect(page_url.'ExcelImport/excel_import');
  }
}

function runtimegenerateOppNo($op_type,$mach_type)
{
  $opnumber=$this->salescrm->runtimegetOppNo($op_type,$mach_type);
  $dd = explode('~',$opnumber);
  return $dd;
}

}
