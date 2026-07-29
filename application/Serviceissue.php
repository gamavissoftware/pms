<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Master_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
	}
	
	
	public function getEngineerNames() {
			$q = $_GET['q'];
			$query = $this->db->select('user_id, first_name, last_name')
							 ->from('system_users')
							 ->like('first_name', $q, 'both')
							 ->where('department_id', 5)
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $users) {
					$json[] = array('id'=>$users->user_id, 'text'=>$users->first_name." ".$users->last_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
		}

			public function getItemNames() {
			$q = $_GET['q'];
			$query = $this->db->select('id, part')
							 ->from('machine_parts_with_picture')
							 ->like('part', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $items) {
					$json[] = array('id'=>$items->id, 'text'=>$items->part);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
		}

		public function getItemUnit() {
			$item_id = $this->input->post('item');
			
				$query = $this->db->select('a.unit, b.id, b.shortname, a.current_stock')
								  ->from('machine_parts_with_picture a')
								  ->join('units b','a.unit=b.id')
								  ->where('a.id',$item_id)
								  ->get();
				foreach($query->result() as $row);
					echo $row->id.'|'.$row->shortname.'|'.$row->current_stock;
				}

		public function add_request() {

			$service_engineer_name = $this->input->post('engineer_name');
			$customer_name = $this->input->post('customer_name');
			$io_no = $this->input->post('io_no');
			$item_name = $this->input->post('item_name');
			$hidden_stock = $this->input->post('hidden_stock');
			$quantity = $this->input->post('quantity');
			$unit = $this->input->post('hidden_unit_id');
			$remarks = $this->input->post('remarks');


			$material_request = array(
								'service_engineer_name' => $service_engineer_name,
								'customer_name' => $customer_name,
								'io_no' => $io_no,
								'added_on' => date('Y-m-d H:i:s')
								);

			$result = $this->master->add_material_request($material_request);

			if ($result > 0) {
				for($i = 0; $i < count($item_name); $i++) {
				$data = array(
							'material_request_id' => $result,
							'service_engineer_id' => $service_engineer_name,
							'item_name' => $item_name[$i],
							'current_stock' => $hidden_stock[$i],
							'quantity' => $quantity[$i],
							'unit' => $unit[$i],
							'remarks' => $remarks[$i]
							);
				//echo "pre";print_r($data);exit;

				$results = $this->master->add_request_items($data);
			}




			if ($results > 0) {
				for($j = 0; $j < count($item_name); $j++) {
				$getCurrentStock = $this->master->getCurrentStock($item_name[$j]);
				foreach ($getCurrentStock as $stock);
				$currentStock = $stock->current_stock;
				$stock_left = floatval($currentStock) - floatval($quantity[$j]);
				//echo $stock_left;exit;
				$updateData = array(
								'current_stock' => floatval($stock_left),
								);
				//echo "pre";print_r($updateData);exit;	
				$updateResult = $this->master->updateStock($updateData, $item_name[$j]);
					}

					if ($updateResult) {
						$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Items Inwarded,Stock Updated & Request Opened</span><br/>');
						redirect(page_url.'Master/Units');
					}
			}
		}			


		}

		public function service_request(){
		    $this->load->view('master/service_request_list');
		}

		public function edit_material_request() {
			$id = $this->uri->segment(4);
			$totalRows = $this->master->challan_no();
			$data['challanNo'] = str_pad($id, 3, '0', STR_PAD_LEFT);
			$data['getMaterialRequest'] = $this->master->getMaterialRequest($id);
			$data['getRequestItems'] = $this->master->getRequestItems($id);
			 //echo "<pre>";print_r($data);exit;
		    $this->load->view('master/edit_material_request', $data);
		}

		public function service_request_list()
	{
		$scheduler_data = array();
		$getMaterialData = $this->master->getMaterialRequestData();

		$i=1;
		foreach($getMaterialData as $row)
		{
			
			$html = "<table border='1' style='width:500px;'><tr style='background-color:white'><th style='padding:2px 2px 2px 2px'>ITEM</th><th style='padding:2px 2px 2px 2px'>QUANTITY</th><th style='padding:2px 2px 2px 2px'>UNIT</th><th style='padding:2px 2px 2px 2px'>REMARKS</th></tr>";
			
			$getItems = $this->master->getItemDetails($row->service_engineer_name);
			
			foreach($getItems as $items){
			
				$html.="<tr>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($items->part)."</td>";
			
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($items->quantity)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($items->shortname)."</td>";
				$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($items->remarks)."</td>";
				
				$html.="</tr>";
				
			}
			$html.="</table>";
			
			if($row->flag==0)
			{
			 $checkFlag = "<a href='".page_url."Master/Units/edit_material_request/".$row->id."' target='_blank'><span class='btn btn-xs btn-warning'>OPEN</span></a>";
			}else
			{
				$checkFlag = "<a href='javascript:;'><span class='btn btn-xs btn-success'>CLOSED</span></a>";

			}

			$challan = "<a href='".page_url."Master/Units/serviceitemchallan/".$row->id."' target='_blank'><span class='btn btn-xs btn-warning'>OPEN CHALLAN 00".$row->id."</span></a>";
			
			$scheduler_data[] = array('sr_no'=>$i,
									  'service_engineer_name'=>strtoupper($row->first_name." ".$row->last_name),
									  'customer_name'=>strtoupper($row->customer_name),
									  'io_no'=>strtoupper($row->io_no),
									  'item_name'=>$html,
									  'flag' => $checkFlag,
									  'challan' => $challan
									  );
								$i++;
				}
	$results = array(
	"sEcho" => 1,
	"iTotalRecords" => count($scheduler_data),
	"iTotalDisplayRecords" => count($scheduler_data),
	"aaData"=>$scheduler_data);
	echo json_encode($results);
}

	public function update_stock() {
		$updateID = $this->input->post('update_id');
		$row_id = $this->input->post('row_id');
		$item_name = $this->input->post('item_name');

		$in_qty = $this->input->post('in_qty');
		$reason = $this->input->post('reason');
		$bill_no = $this->input->post('bill_no');

		for ($j=0; $j < count($in_qty); $j++) { 
			$data = array(
					'in_qty' => $in_qty[$j],
					'short_reason' => $reason[$j],
					'bill_no' => $bill_no[$j]
					);
			//echo $row_id[$j];exit;
			$result = $this->master->addQty($data, $row_id[$j]);
		}
					//echo "hi";exit;
		for($i=0; $i<count($in_qty); $i++) {
			$getCurrentStock = $this->master->getCurrentStock($item_name[$i]);
			foreach ($getCurrentStock as $stock);
				$currentStock = $stock->current_stock;
				//echo $currentStock;exit;
				$stock_total = floatval($currentStock) + floatval($in_qty[$i]);
				
				$updateData = array(
								'current_stock' => floatval($stock_total)
								);
			//echo "<pre>";print_r($updateData);exit;	
				$updateResult = $this->master->updateStock($updateData, $item_name[$i]);


				/** UPDATE FLAG **/
				$datsa=array(
							'flag' => 1,
							'updated_on' => date('Y-m-d H:i:s')
							);
				$this->db->where('id',$updateID);
				$this->db->update('service_material_request',$datsa);
				/** END **/
			}

		
		$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-danger">Items Inwarded,Stock Updated & Request Closed</span><br/>');
				
				redirect(page_url.'Master/Units/service_request');
	
		
	}

	function serviceitemchallan()
	{

		$this->load->view('Master/serviceitemchallan');
	}
	
