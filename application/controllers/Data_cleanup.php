<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_cleanup extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('data_cleanup_model');
        $this->load->helper('form');
        $this->load->library('session'); // Ensure session library is loaded
    }

    private function normalize_name($name) {
        $normalized = strtoupper($name);
        $normalized = str_replace(['(', ')'], '', $normalized);
        $normalized = trim($normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return $normalized;
    }

    public function index() {
        $part_names = $this->data_cleanup_model->get_distinct_part_names();
        $groups = [];

        foreach ($part_names as $name) {
            $normalized = $this->normalize_name($name);
            $groups[$normalized][] = $name;
        }

        $data['groups_to_clean'] = array_filter($groups, function($g) {
            return count($g) > 1;
        });

        $this->load->view('data_cleanup_view', $data);
    }

    public function update() {
        $checked_groups = $this->input->post('checked_groups');
        $corrections = $this->input->post('correction');
        $update_count = 0;

        if ($checked_groups && $corrections) {
            $data_to_update = [];
            foreach ($checked_groups as $original_names_json) {
                // Ensure this group has a corresponding correction value
                if (isset($corrections[$original_names_json])) {
                    $original_names = json_decode($original_names_json, true);
                    $new_name = $corrections[$original_names_json];
                    
                    if ($original_names && !empty(trim($new_name))) {
                        $data_to_update[] = [
                            'originals' => $original_names,
                            'new_name' => trim($new_name)
                        ];
                    }
                }
            }

            if (!empty($data_to_update)) {
                if ($this->data_cleanup_model->update_part_names_safely($data_to_update)) {
                    $update_count = count($data_to_update);
                    $this->session->set_flashdata('success_message', "Successfully updated {$update_count} part name group(s).");
                } else {
                    $this->session->set_flashdata('error_message', 'A database error occurred. No changes were made.');
                }
            } else {
                 $this->session->set_flashdata('error_message', 'No valid selections were made for updating.');
            }

        } else {
            $this->session->set_flashdata('error_message', 'You must check the box for each group you want to update.');
        }

        redirect('data_cleanup');
    }
}