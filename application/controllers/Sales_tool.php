<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_tool extends CI_Controller {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->model('Salestool_model','salestool');
	}
	
	
	function index() {
		$this->load->view('sales_tool/sales_tool');
		
	}

	public function add_details() {
		$promotion_name = $this->input->post('promotion_name');
		$promotion_price = $this->input->post('promotion_price');
		$description = $this->input->post('description');
		$client_name = $this->input->post('client_name');
		$city = $this->input->post('city');
		$industry = $this->input->post('industry');
		$charges_name = $this->input->post('charges_name');
		$charges = $this->input->post('charges');

			$pdf_file = $_FILES['pdf_file']['name'];
		        if($pdf_file <> '')
		        {
		            $file = explode('.',$pdf_file);
		            $ext = end($file);
		            $newname = time().'.'.$ext;
		            //echo UPLOADPATH.'blog/' . $newname;exit;
		            move_uploaded_file($_FILES["pdf_file"]["tmp_name"],UPLOADPATH.'sales_tool/' . $newname);
		         } else {
		            $newname='';
		        }


			$machine_details = array(
								'machine_name' => $this->input->post('machine_name'),
								'price' => $this->input->post('price'),
								'usp' => $this->input->post('usp'),
								'discount' => $this->input->post('discount'),
								'video'=>$this->input->post('video'),
								'pdf_file' => $newname
								);
			//echo "<pre>";print_r($machine_details);

			$last_insert_id = $this->salestool->add_machine_details($machine_details);

				for($i = 0; $i < count($promotion_name); $i++) {
				$data = array(
							'machine_id' => $last_insert_id,
							'promotion_name' => $promotion_name[$i],
							'promotion_price' => $promotion_price[$i],
							'description' => $description[$i]
							);

				//echo "<pre>";print_r($data);
				$resultPromotion = $this->salestool->add_promotion($data);
			}

				for($j = 0; $j < count($city); $j++) {
				$datas = array(
							'machine_id' => $last_insert_id,
							'client_name' => $client_name[$j],
							'city' => $city[$j],
							'industry' => $industry[$j]
							);

				//echo "<pre>";print_r($datas);
				$resultCity = $this->salestool->add_city($datas);
				}

				for($k = 0; $k < count($charges_name); $k++) {
				$datass = array(
							'machine_id' => $last_insert_id,
							'charges_name' => $charges_name[$k],
							'charges' => $charges[$k]
							);
				//echo "<pre>";print_r($datass);exit;

				$resultCharges = $this->salestool->add_charges($datass);
			}


						$this->session->set_flashdata('message','<span style="color:black; float-left:20px;" class="alert alert-success">Details successfully added.</span><br/>');
						redirect(page_url.'Sales_tool');		


		}

		public function salestool_details() {
			$this->load->view('sales_tool/sales_tool_details');
		}

			public function getMachineName() {
			$q = $_GET['q'];
			$query = $this->db->select('id, machine_name')
							 ->from('newsalestool_machines')
							 ->like('machine_name', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $machines) {
					$json[] = array('id'=>$machines->id, 'text'=>$machines->machine_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
		}

		public function getAllDetails() {
			$machine_id = $this->input->post('machine_id');
			$result = $this->salestool->getSalesToolDetails($machine_id);

				foreach ($result as $row);
					echo $row->machine_name.'|'.$row->price.'|'.strip_tags($row->usp).'|'.$row->discount.'|'.$row->pdf_file.'|'.$row->video;
		}

		public function getAllMisc() {
			$html = '';
			$machine_id = $this->input->post('machine_id');
			$rowid = $this->input->post('rowid');
			// $result = $this->salestool->getSalesToolMisc($machine_id);

				// foreach ($result as $row) {
				// 	$html .= '<div class="col-md-2">
				// 		            <div class="form-group">
				// 		                <label for="field-1" class="control-label">'.$row->charges_name.'</label>
				// 		                 <input type="text" id="misc_charges" name="charges_name[]" class="form-control misc_charges" value="'.$row->charges.'" onkeyup="calculateTotal()" readonly>
				// 		            </div>
				// 		          </div>';
				// }
				   $query = $this->db->select('charges_name, charges')
									 ->from('newsalestool_misc')
									 ->where('machine_id', $machine_id)
									 ->get();

						if($query->num_rows() > 0) {
							foreach ($query->result() as $row) {
									$html .= '<tr>
												<td>'.$row->charges_name.'</td>
												<td class="misss'.$rowid.'">'.$row->charges.'</td>
											  </tr>';
									}
								}

						

				echo $html;
		}

		public function getAllClients() {
			$html = '';
			$machine_id = $this->input->post('machine_id');
			$query = $this->db->select('client_name, city, industry')
							  ->from('newsalestool_clients')
							  ->where('machine_id', $machine_id)
							  ->get();

			if($query->num_rows() > 0) {
			$i = 1;
				foreach ($query->result() as $row) {
					$html .= '<tr>
								<td>'.$row->client_name.'</td>
								<td>'.$row->city.'</td>
								<td>'.$row->industry.'</td>
							  </tr>';
					$i++;
				}
			}

				echo $html;
		}

		public function getAllPromotions() {
			$html = '';
			$machine_id = $this->input->post('machine_id');
			$rowid = $this->input->post('rowid');

			$query = $this->db->select('promotion_name, promotion_price, description')
						      ->from('newsalestool_promotion')
						      ->where('machine_id', $machine_id)
						      ->get();
				
				if($query->num_rows() > 0) {
			$i = 1;
				foreach ($query->result() as $row) {
					$html .= '<tr>
								<td><input type="checkbox" class="checkpromotion'.$rowid.'" name="checkpromotion" value="'.$row->promotion_price.'" onchange="calculateTotal('.$rowid.')"></td>
								<td>'.$row->promotion_name.'</td>
								<td>'.$row->promotion_price.'</td>
								<td>'.$row->description.'</td>
							  </tr>';
					$i++;
				}

				}



				echo $html;
		}

		public function getSummary() {
			$html = '';
			$misc_th = '';
			$misc_td = '';
			
			$machine_id = $this->input->post('machine_id');
			$result = $this->salestool->getSalesToolMisc($machine_id);
			$a=count($result);
			foreach ($result as $rows) {

				$misc_th .= '<th style="text-align: center;">'.$rows->charges_name.'</th>';
				$misc_td .= '<td style="text-align: center;" id="misc">'.$rows->charges.'</td>';
			}
			$query = $this->db->select('promotion_name, promotion_price, description')
						      ->from('newsalestool_promotion')
						      ->where('machine_id', $machine_id)
						      ->get();
				
				if($query->num_rows() > 0) {
			//$i = 1;
			     	$html .=	'<thead>
                                <tr>
                                    <th style="text-align: center;width:10%">MACHINE NAME</th>
                                    <th style="text-align: center;">PRICE</th>
                                    <th style="text-align: center;">DISCOUNT %</th>
                                    <th style="text-align: center;">DISCOUNTED PRICE</th>
                                    <th style="text-align: center;">FREIGHT</th>
                                    <th style="text-align: center;">PACKING %</th>
                                    <th style="text-align: center;">PACKING PRICE</th>
                                    '.$misc_th.'
                                    <th style="text-align: center;">TOTAL PRICE</th>
                                    <th style="text-align: center;">MINIMUM PRICE</th>
                                    <th style="text-align: center;width:10%">PDF</th>
                                    <th style="text-align: center;width:10%">VIDEO</th>
                                    <th style="text-align: center;width:15%">USP</th>
                                
                                </tr>
                                </thead>';
				foreach ($query->result() as $row);
					$html .= '<tbody>
					<tr>
                                  <td style="text-align: center;" id="machine"></td>
                                  <td style="text-align: center;" id="price"></td>
                                  <td style="text-align: center;" id="disc"></td>
                                  <td style="text-align: center;" id="disc_price"></td>
                                  <td style="text-align: center;" id="freight_td"></td>
                                  <td style="text-align: center;" id="pack"></td>
                                  <td style="text-align: center;" id="pack_price"></td>
                                  '.$misc_td.'
                                  <td style="text-align: center;" id="total"></td>
                                  <td style="text-align: center;" id="minimum"></td>
                                  <td style="text-align: center;"><a href="" target="_blank" id="pdf_file">Click Here</a></td>
                                  <td style="text-align: center;"><a href="" target="_blank" id="video_link">Click Here</a></td>
                                  <td style="text-align: center;" id="usp_td"></td>
                                  </tr>';

                                  $colsp=$a+10;
                                 $html.='<tr>
                                  <td style="text-align: center;" id="finalresult" colspan="'.$colsp.'"></td>
                                
                                  </tr>
                                </tbody>';
					//$i++;
					
				}

				echo $html;
		}

		public function searchMachine() {
	   	   $q = $_GET['q'];
		   $query = $this->db->select('id, instruments_name')
							 ->from('presto_instruments')
							 ->like('instruments_name', $q, 'both')
							 ->get();

			if($query->num_rows()>0) {
				foreach($query->result() as $machines) {
					$json[] = array('id'=>$machines->id, 'text'=>$machines->instruments_name);
					}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
				
				echo json_encode($json);
	   
	}

	public function getNamePrice() {
			$machine_id = $this->input->post('machineid');
			$result = $this->salestool->getMachineDetails($machine_id);

				foreach ($result as $row);
					echo $row->instruments_name.'|'.$row->mvalue;
		}

		public function sales_tool_list() {
		$this->load->view('sales_tool/sales_tool_list');
		}

		public function sales_tool_report() {
			 $data=array();
       $category = '';
        $query = $this->db->select('id, machine_name, price')
                          ->from('newsalestool_machines')
                          ->get();

        
        if($query->num_rows()>0) {
            $i=1;
            foreach($query->result() as $row)
            {
               

             $edit = "<a href='".page_url."Sales_tool/edit_sales_tool/".$row->id."'><i class='fa fa-pencil'></i></a>";
            

        $data[] = array(
                'sr_no' => $i,
                'machine_name' => $row->machine_name,
                'machine_price' => $row->price,
                'action' => $edit
                );
            
            $i++;
            }           
            
        
        $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData"=>$data);
    echo json_encode($results);
    
    }else
    {
        $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData"=>$data);
    echo json_encode($results);
        
    }
		}

	public function edit_sales_tool() {
        $id = $this->uri->segment(3);
        $data['edit_sales_tool'] = $this->salestool->getEditMachines($id);
        $data['getEditPromotion'] = $this->salestool->getEditPromotion($id);
        $data['getEditClients'] = $this->salestool->getEditClients($id);
        $data['getEditMisc'] = $this->salestool->getEditMisc($id);
        $this->load->view('sales_tool/edit_sales_tool', $data);
    }


}