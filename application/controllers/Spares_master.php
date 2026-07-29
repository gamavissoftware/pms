<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spares_master extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect(page_url);
        }

        // Load the Spare_parts_model. 
        // Make sure you've already created the Spare_parts_model.php file from our previous conversation.
        $this->load->model('Spare_parts_model');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('Master_profile_guard');
        $this->master_profile_guard->block_methods(
            array('add_form', 'edit_form', 'save', 'delete'),
            'This EA profile can review spare parts masters but cannot change them.'
        );
    }

    /**
     * Main listing page for Spare Parts
     * URL: Spares_master/index or Spares_master
     */
    public function index() {
        // Just load the view. DataTables will fetch the data via AJAX.
        $this->load->view('spares_master/list');
    }

    /**
     * AJAX endpoint for DataTables to fetch parts.
     * This is called by the JavaScript in the list.php view.
     */
    public function get_parts_list_ajax() {
        $is_master_read_only = $this->master_profile_guard->is_master_read_only();
        $list = $this->Spare_parts_model->get_parts_list_serverside();
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $part) {
            $no++;
            $row = array();
            
            $row[] = $part->id;
            $row[] = '<strong>' . html_escape($part->code) . '</strong>';
            $row[] = html_escape($part->description);
            $row[] = '₹' . number_format($part->price, 2);
            
            // Status Badge
            if ($part->status == 1) {
                $row[] = '<span class="badge badge-success">Active</span>';
            } else {
                $row[] = '<span class="badge badge-danger">Inactive</span>';
            }
            
            $row[] = date('d M, Y', strtotime($part->added_on));

            // Action Buttons
            if ($is_master_read_only) {
                $row[] = '<span class="text-muted">View only</span>';
            } else {
                $edit_url = page_url.'spares_master/edit_form/' . $part->id;
                $row[] = '<div class="action-icons">
                              <a href="' . $edit_url . '" title="Edit" class="text-warning"><i class="fa fa-pencil"></i></a>
                          </div>';
            }

            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->Spare_parts_model->count_all_parts(),
            "recordsFiltered" => $this->Spare_parts_model->count_filtered_parts(),
            "data" => $data,
        );

        // Output to JSON format
        echo json_encode($output);
    }

    /**
     * Displays the "Add New Spare Part" form.
     * URL: Spares_master/add_form
     */
    public function add_form() {
        $data['part'] = null; // No data, it's a new part
        $data['form_action'] = page_url.'spares_master/save';
        $data['page_title'] = 'Add New Spare Part';
        $this->load->view('spares_master/form', $data);
    }

    /**
     * Displays the "Edit Spare Part" form.
     * URL: Spares_master/edit_form/[ID]
     */
    public function edit_form($id = 0) {
        if (empty($id)) {
            redirect('spares_master');
        }

        $data['part'] = $this->Spare_parts_model->get_part_by_id($id);
        
        if (empty($data['part'])) {
             $this->session->set_flashdata('error', 'Spare part not found.');
             redirect('spares_master');
        }

        $data['form_action'] = page_url.'spares_master/save/' . $id;
        $data['page_title'] = 'Edit Spare Part';
        $this->load->view('spares_master/form', $data);
    }

    /**
     * Handles both saving a new part and updating an existing one.
     * URL: Spares_master/save (for new)
     * URL: Spares_master/save/[ID] (for update)
     */
    public function save($id = 0) {
        
        // 1. Set validation rules
        $this->form_validation->set_rules('code', 'Part Code', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('price', 'Price', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed, show the form again with errors
            $this->session->set_flashdata('error', validation_errors());
            if ($id == 0) {
                $this->add_form();
            } else {
                $this->edit_form($id);
            }
        } else {
            // 2. Prepare data
            $data = array(
                'code' => $this->input->post('code'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'status' =>1,
                'revision' => $this->input->post('revision') ? $this->input->post('revision') : 0, // Assuming revision can be set
                // 'added_on' is handled by the model or database
                // 'added_by' should be set to the logged-in user's ID
                'added_by' => 1 // !! IMPORTANT: Replace '1' with your session's user ID, e.g., $this->session->userdata('user_id')
            );

            //echo "<pre>"; print_r($data); exit;

            // 3. Insert or Update
            if ($id == 0) {
                // This is a new part
                if ($this->Spare_parts_model->add_part($data)) {
                    $this->session->set_flashdata('success', 'Spare part added successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Error adding spare part.');
                }
            } else {
                // This is an update
                if ($this->Spare_parts_model->update_part($id, $data)) {
                    $this->session->set_flashdata('success', 'Spare part updated successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Error updating spare part.');
                }
            }
            
            redirect(page_url.'spares_master');
        }
    }

    /**
     * Deletes (soft deletes) a spare part.
     * URL: Spares_master/delete/[ID]
     */
    public function delete($id = 0) {
        if (empty($id)) {
            redirect('spares_master');
        }

        if ($this->Spare_parts_model->delete_part($id)) {
            $this->session->set_flashdata('success', 'Spare part has been set to Inactive.');
        } else {
            $this->session->set_flashdata('error', 'Error deleting spare part.');
        }
        redirect('spares_master');
    }
}
