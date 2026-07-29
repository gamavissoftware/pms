<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Seo_createProject extends CI_Controller
{
	public function __construct()
      {
              parent::__construct();
            // $this->load->library('PHPExcel');
              // $this->load->model('upload_model');
              $this->load->model('Seo_model');
      }
	
	public function index()
	{
		$this->load->view('seo_tool/seo_project');
	}

	public function seo_viewEditProject()
	{
		$this->load->view('seo_tool/seo_viewEditProject');
	}

	public function addProject()
	{
		$this->form_validation->set_rules('company_name','Company Name','required|trim');
		$this->form_validation->set_rules('contact_person','Contact Person','required|trim');
		$this->form_validation->set_rules('phone_number','Phone Number','required|trim|min_length[10]|max_length[10]');
		$this->form_validation->set_rules('admin_email','Admin Email ID','required|trim|valid_email');
		// $this->form_validation->set_rules('logo','Logo','required|trim');

		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('seo_tool/seo_project');
		} else {
			$logo = $_FILES['logo']['name'];

			if ($logo != '')
			{
			$image = explode('.', $logo);
			$ext = end($image);
			$finalLogo = time().'.'.$ext;
			move_uploaded_file($_FILES["logo"]["tmp_name"],SEOUPLOADPATH.$finalLogo);
			} else {
		
			$finalLogo = "";
			}

			$data = array(
					'company_name' => $this->input->post('company_name'),
					'contact_person' => $this->input->post('contact_person'),
					'phone_number' => $this->input->post('phone_number'),
					'company_logo' => $finalLogo,
					'website_link' => $this->input->post('website_link'),
					'admin_email' => $this->input->post('admin_email'),
					'reporting_email' => $this->input->post('reporting_email')
			);

			$result = $this->Seo_model->insert_project($data);

			if ($result > 0) {
			// $this->session->set_flashdata('message','Project Created Successfully');
			redirect('seo_tool/seo_createProject/addCompanyDetails/'.$result);
			} else {
			$this->session->set_flashdata('message','Technical Problem Occurred');
			redirect('seo_tool/seo_createProject','refresh');	
			}
		}
	}

	public function addCompanyDetails() {
		$this->load->view('seo_tool/seo_company_details');
	}

	public function seo_pages() {
		$this->load->view('seo_tool/seo_pages');
	}

	public function getCities() {
		$json=array();
		 $searchtrm= $_GET['q'];
		 $data = $this->Seo_model->getIndianCities($searchtrm);
		
		 	if (count($data)> 0) {
				foreach ($data as $row) {
        			$json[] = array('id'=>$row->city_id, 'text'=>$row->city_name);
        			}
				} else {
					$json[] = array('id'=>"", 'text'=>"No Data Available");
				}
	echo json_encode($json);
	}

		public function insertCompanyDetails() {
		 $projectID = $this->input->post('project_id');
		 $cities = $this->input->post('cities');
		 $competitors = $this->input->post('competitors');
		 $websites = $this->input->post('websites');
		

		  for($i = 0; $i < count($competitors); $i++) {
			$data = array(
					'project_id' => $projectID,
					'competitors' => $competitors[$i],
					'websites' => $websites[$i]
					);
			$results = $this->Seo_model->saveCompanyDetails($data);
			}

			//echo "<pre>";print_r($results);exit;
	
			// if ($results > 0) {
				for ($j = 0; $j < count($cities); $j++) {
					$saveCities = array(
						'project_id' => $projectID,
						'city_id' => $cities[$j]
						);
		 			$result = $this->Seo_model->seo_saveCities($saveCities);
				}
				// echo "<pre>";print_r($result);exit;

		  
			if ($result > 0) {
			$this->session->set_flashdata('message','Project Created Successfully');
			redirect('seo_tool/Seo_createProject/seo_pages/'.$projectID);
				// }
		} else {
		$this->session->set_flashdata('message','Technical Problem Occurred');
		redirect('seo_tool/Seo_createProject/addCompanyDetails','refresh');	
		}
	}

	public function getCompanyDetails() {
		$getResult = $this->Seo_model->getDetails();
		//echo $getResult;exit;

		if (count($getResult) > 0) {
			$i = 1;
			foreach ($getResult as $row) {
				$logo = "<img src='".page_url."assets/images/seo_images/".$row->company_logo."' style='width:80px; height:80px;'>";
				$edit = "<a href='".page_url."seo_tool/Seo_createProject/editProject/".$row->id."'><i class='fa fa-pencil'></i></a>";	
				
			$data[] = array('sr_no' => $i,
							'company_name' => $row->company_name,
							'contact_person' => $row->contact_person,
							'phone_number' => $row->phone_number,
							'company_logo' => $logo,
							'website_link' => $row->website_link,
							'admin_email' => $row->admin_email,
							'reporting_email' => $row->reporting_email,
							'edit' => $edit
						);
				$i++;
				}
		
			$results = array(
						"sEcho" => 1,
						"iTotalRecords" => count($data),
						"iTotalDisplayRecords" => count($data),
						"aaData" => $data
					);
			
		echo json_encode($results);
		}
	}

	public function editProject() {
		$projectID = $this->uri->segment(4);
		$data['editRecord'] = $this->Seo_model->editRecords($projectID);			
		$data['getRows'] = $this->Seo_model->getRows($projectID);			
		$data['getMatchedCities'] = $this->Seo_model->getMatchedCities($projectID);			
		$this->load->view('seo_tool/seo_editProject', $data);

	}

	public function updateSeoTool() {
		$projectID = $this->input->post('project_id');
		$competitors = $this->input->post('competitors');
		$websites = $this->input->post('websites');
		$cities = $this->input->post('cities');
		$old_image = $this->input->post('old_image');

		$logo = $_FILES['logo']['name'];

			if ($logo != '') {
			$image = explode('.', $logo);
			$ext = end($image);
			$finalLogo = time().'.'.$ext;
			move_uploaded_file($_FILES["logo"]["tmp_name"],SEOUPLOADPATH.$finalLogo);
			unlink($_SERVER['DOCUMENT_ROOT'].'/prestolive/assets/images/seo_images/'.$old_image);
			} else {
			$finalLogo = $old_image;
			}
		$updateProject = array(
					'company_name' => $this->input->post('company_name'),
					'contact_person' => $this->input->post('contact_person'),
					'phone_number' => $this->input->post('phone_number'),
					'company_logo' => $finalLogo,
					'website_link' => $this->input->post('website_link'),
					'admin_email' => $this->input->post('admin_email'),
					'reporting_email' => $this->input->post('reporting_email'),
			);

		for($i = 0; $i < count($competitors); $i++) {
		$updateProduct = array(
					'project_id' => $projectID,
					'competitors' => $competitors[$i],
					'websites' => $websites[$i]
				);
			$updateProductData = $this->Seo_model->updateProduct($updateProduct, $projectID);
		}

		for ($j = 0; $j < count($cities); $j++) {
					$updateCities = array(
						'project_id' => $projectID,
						'city_id' => $cities[$j]
						);
		 			$updateCitiesData = $this->Seo_model->seo_updateCities($updateCities);
				}

		$updateProjectData = $this->Seo_model->updateProject($updateProject, $projectID);
		
		if ($updateProjectData > 0) {
			// echo "Updated Successfully";exit;
			// $this->session->set_flashdata('message','Updated Successfully');
			// redirect('seo_tool/Seo_createProject/seo_viewEditProject');
			$this->load->view('seo_tool/seo_viewEditProject');
			
		} else {
			$this->load->view('seo_tool/seo_editProject');
		}
		
	}

	public function delDetails() {
		$delID = $this->input->post('id');
		$result = $this->Seo_model->delRecord($delID);			
		echo $result;

	}

	public function delCity() {
		$delID = $this->input->post('id');
		$result = $this->Seo_model->delCity($delID);			
		echo $result;

	}
}
?>
