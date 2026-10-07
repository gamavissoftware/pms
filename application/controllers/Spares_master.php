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
            array('add_form', 'edit_form', 'save', 'delete', 'upload_stock'),
            'This EA profile can review spare parts masters but cannot change them.'
        );
    }

    /**
     * Main listing page for Spare Parts
     * URL: Spares_master/index or Spares_master
     */
    public function index() {
        // DataTables fetches line items via AJAX; summary cards are rendered with the page.
        $data['inventory_summary'] = $this->Spare_parts_model->get_inventory_summary();
        $data['recent_stock_uploads'] = $this->Spare_parts_model->get_recent_stock_uploads(5);
        $data['inventory_columns_available'] = $this->Spare_parts_model->inventory_columns_available();
        $this->load->view('spares_master/list', $data);
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

            $available_qty = isset($part->available_qty) ? (float) $part->available_qty : 0;
            if ($available_qty <= 0) {
                $stock_class = 'stock-zero';
                $stock_label = 'No Stock';
            } elseif ($available_qty <= 5) {
                $stock_class = 'stock-low';
                $stock_label = 'Low';
            } else {
                $stock_class = 'stock-ok';
                $stock_label = 'Available';
            }
            $row[] = '<div class="stock-pill ' . $stock_class . '"><span class="stock-qty">' . number_format($available_qty, 3) . '</span><span class="stock-label">' . $stock_label . '</span></div>';

            if (!empty($part->stock_updated_on)) {
                $row[] = date('d M, Y h:i A', strtotime($part->stock_updated_on));
            } else {
                $row[] = '<span class="text-muted">Not updated</span>';
            }
            
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
        $data['inventory_columns_available'] = $this->Spare_parts_model->inventory_columns_available();
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
        $data['inventory_columns_available'] = $this->Spare_parts_model->inventory_columns_available();
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
        if ($this->Spare_parts_model->inventory_columns_available()) {
            $this->form_validation->set_rules('available_qty', 'Available Qty', 'trim|numeric');
        }

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
                'status' => $this->input->post('status'),
                'revision' => $this->input->post('revision') ? $this->input->post('revision') : 0, // Assuming revision can be set
                // 'added_on' is handled by the model or database
                // 'added_by' should be set to the logged-in user's ID
                'added_by' => $this->current_user_id()
            );

            if ($this->Spare_parts_model->inventory_columns_available()) {
                $posted_qty = $this->input->post('available_qty');
                $data['available_qty'] = ($posted_qty === '' || $posted_qty === null) ? 0 : (float) $posted_qty;
                $data['stock_updated_on'] = date('Y-m-d H:i:s');
                $data['stock_updated_by'] = $this->current_user_id();
            }

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

    /**
     * Downloads a simple CSV format that can also be opened in Excel.
     */
    public function download_stock_format() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=spares-stock-upload-format.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Code', 'Available Qty', 'Description'));
        fputcsv($output, array('SPARE-CODE-001', '10', 'Optional description for reference'));
        fclose($output);
        exit;
    }

    /**
     * Imports available stock quantity from Finsys export.
     */
    public function upload_stock() {
        $result = array(
            'total_rows' => 0,
            'updated_rows' => 0,
            'skipped_rows' => 0,
            'failed_rows' => 0,
            'messages' => array()
        );

        if (!$this->Spare_parts_model->inventory_columns_available()) {
            $this->session->set_flashdata('error', 'Inventory fields are not available. Please run Database/spares_master_inventory_001.sql first.');
            redirect(page_url.'spares_master');
        }

        $staged = $this->stage_stock_upload($result);
        if (!$staged['ok']) {
            $this->session->set_flashdata('error', implode('<br>', $result['messages']));
            redirect(page_url.'spares_master');
        }

        $parse = $this->parse_stock_sheet($staged['path'], $staged['name']);
        if (!$parse['ok']) {
            $this->session->set_flashdata('error', $parse['message']);
            redirect(page_url.'spares_master');
        }

        $seen_codes = array();
        $user_id = $this->current_user_id();

        foreach ($parse['rows'] as $item) {
            $result['total_rows']++;
            $code_key = strtolower(trim($item['code']));

            if ($code_key === '' || $item['qty'] === '') {
                $result['skipped_rows']++;
                continue;
            }

            if (isset($seen_codes[$code_key])) {
                $result['failed_rows']++;
                $result['messages'][] = 'Row ' . $item['row_no'] . ': duplicate code ' . html_escape($item['code']) . ' skipped.';
                continue;
            }
            $seen_codes[$code_key] = true;

            if (!is_numeric($item['qty']) || (float) $item['qty'] < 0) {
                $result['failed_rows']++;
                $result['messages'][] = 'Row ' . $item['row_no'] . ': invalid quantity for code ' . html_escape($item['code']) . '.';
                continue;
            }

            $update = $this->Spare_parts_model->update_stock_by_code($item['code'], (float) $item['qty'], $user_id);
            if ($update['ok']) {
                $result['updated_rows']++;
            } else {
                $result['failed_rows']++;
                $result['messages'][] = 'Row ' . $item['row_no'] . ': ' . html_escape($item['code']) . ' - ' . html_escape($update['message']);
            }
        }

        $this->Spare_parts_model->log_stock_upload(array(
            'file_name' => $staged['name'],
            'stored_file' => $staged['stored_file'],
            'total_rows' => $result['total_rows'],
            'updated_rows' => $result['updated_rows'],
            'skipped_rows' => $result['skipped_rows'],
            'failed_rows' => $result['failed_rows'],
            'remarks' => implode("\n", array_slice($result['messages'], 0, 25)),
            'uploaded_by' => $user_id,
            'uploaded_on' => date('Y-m-d H:i:s')
        ));

        $summary = 'Stock upload completed. Updated: ' . $result['updated_rows']
            . ', Failed: ' . $result['failed_rows']
            . ', Skipped: ' . $result['skipped_rows'] . '.';

        if ($result['failed_rows'] > 0) {
            $this->session->set_flashdata('stock_import_report', $result);
            $this->session->set_flashdata('error', $summary . ' Please review the import report below.');
        } else {
            $this->session->set_flashdata('success', $summary);
        }

        redirect(page_url.'spares_master');
    }

    private function current_user_id() {
        $logged_in = $this->session->userdata('logged_in');
        if (is_array($logged_in) && isset($logged_in['user_id'])) {
            return (int) $logged_in['user_id'];
        }

        return null;
    }

    private function stage_stock_upload(&$result) {
        $blank = array('ok' => false, 'path' => '', 'name' => '', 'stored_file' => '');

        if (empty($_FILES['stock_file']['name'])) {
            $result['messages'][] = 'Please choose a Finsys stock file to upload.';
            return $blank;
        }

        if (!empty($_FILES['stock_file']['error'])) {
            $result['messages'][] = 'The file did not upload correctly. Error code: ' . (int) $_FILES['stock_file']['error'];
            return $blank;
        }

        $name = $_FILES['stock_file']['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, array('xlsx', 'xls', 'csv'), true)) {
            $result['messages'][] = 'Only .xlsx, .xls and .csv files are allowed.';
            return $blank;
        }

        if ($ext === 'xls' && PHP_VERSION_ID >= 80000) {
            $result['messages'][] = 'Old .xls files cannot be read safely on this server. Save the Finsys export as .xlsx or .csv and upload again.';
            return $blank;
        }

        $dir = FCPATH . 'exceluploads/spares_stock';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            $result['messages'][] = 'Upload folder could not be created: exceluploads/spares_stock.';
            return $blank;
        }

        $stored_file = 'spares_stock_' . date('Ymd_His') . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $path = $dir . '/' . $stored_file;

        if (!@move_uploaded_file($_FILES['stock_file']['tmp_name'], $path)) {
            $result['messages'][] = 'The uploaded file could not be saved on the server.';
            return $blank;
        }

        return array('ok' => true, 'path' => $path, 'name' => $name, 'stored_file' => $stored_file);
    }

    private function parse_stock_sheet($path, $original_name) {
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            return $this->parse_stock_csv($path);
        }

        $this->load->library('Excel');
        $readers = array('xlsx' => 'Excel2007', 'xls' => 'Excel5');

        if (!isset($readers[$ext])) {
            return array('ok' => false, 'message' => 'Unsupported stock file format.');
        }

        try {
            $reader = PHPExcel_IOFactory::createReader($readers[$ext]);
            $reader->setReadDataOnly(true);
            $book = $reader->load($path);
            $grid = $book->getSheet(0)->toArray(null, true, false, false);
        } catch (Exception $e) {
            return array('ok' => false, 'message' => 'The stock file could not be read. Save it as .xlsx or .csv and try again. ' . $e->getMessage());
        }

        return $this->parse_stock_grid($grid);
    }

    private function parse_stock_csv($path) {
        $grid = array();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return array('ok' => false, 'message' => 'CSV file could not be opened.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            $grid[] = $row;
        }
        fclose($handle);

        return $this->parse_stock_grid($grid);
    }

    private function parse_stock_grid($grid) {
        $found = $this->find_stock_header($grid);
        if (!$found) {
            return array('ok' => false, 'message' => 'No stock heading row found. The file must contain part code and quantity columns.');
        }

        $header_row = $found['row'];
        $columns = $found['columns'];
        $rows = array();

        for ($i = $header_row + 1; $i < count($grid); $i++) {
            $raw = $grid[$i];
            $code = isset($raw[$columns['code']]) ? trim((string) $raw[$columns['code']]) : '';
            $qty = isset($raw[$columns['qty']]) ? $this->clean_quantity($raw[$columns['qty']]) : '';

            if ($code === '' && $qty === '') {
                continue;
            }

            $rows[] = array(
                'row_no' => $i + 1,
                'code' => $code,
                'qty' => $qty
            );
        }

        if (empty($rows)) {
            return array('ok' => false, 'message' => 'The stock sheet has headings but no stock rows.');
        }

        return array('ok' => true, 'rows' => $rows);
    }

    private function find_stock_header($grid) {
        for ($i = 0; $i < count($grid) && $i < 15; $i++) {
            $columns = array();
            foreach ($grid[$i] as $index => $heading) {
                $key = $this->normalise_heading($heading);
                if ($key === '') {
                    continue;
                }
                if ($this->is_code_heading($key)) {
                    $columns['code'] = $index;
                }
                if ($this->is_qty_heading($key)) {
                    $columns['qty'] = $index;
                }
            }

            if (isset($columns['code']) && isset($columns['qty'])) {
                return array('row' => $i, 'columns' => $columns);
            }
        }

        return null;
    }

    private function normalise_heading($value) {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', trim((string) $value)));
    }

    private function is_code_heading($key) {
        return in_array($key, array(
            'code', 'partcode', 'sparecode', 'itemcode', 'materialcode',
            'erpcode', 'itemno', 'partno', 'finsyscode', 'sku'
        ), true);
    }

    private function is_qty_heading($key) {
        return in_array($key, array(
            'qty', 'quantity', 'availableqty', 'availablequantity', 'stock',
            'closingstock', 'balqty', 'balanceqty', 'currentstock', 'physicalstock',
            'stockqty', 'closingqty'
        ), true);
    }

    private function clean_quantity($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(',', '', $value);
        $value = preg_replace('/[^0-9.\-]/', '', $value);
        return $value;
    }
}
