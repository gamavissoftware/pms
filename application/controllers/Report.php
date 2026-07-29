<?php
// application/controllers/Report.php
// No changes are needed in the controller file. It remains the same.

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('production_model');
        $this->load->helper('url');
    }

    public function index() {
        $this->load->view('report/search_view');
    }


    public function process_search() {
        // Load the form validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('drawing_no', 'Drawing Number', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails (e.g., the field is empty), show the search page again.
            $this->load->view('reports/search_view');
        } else {
            // If validation succeeds, get the drawing number from the form
            $drawing_no = $this->input->post('drawing_no');

            // URL-encode the drawing number to handle special characters (like slashes) safely in the URL.
            $encoded_drawing_no = urlencode($drawing_no);

            // Redirect to the existing journey method with the drawing number.
            redirect(page_url."report/journey/{$encoded_drawing_no}");
        }
    }

    /**
     * Displays the summary report for a given drawing number.
     */
    public function drawing($drawing_no = '') {
        if (empty($drawing_no)) {
            show_404();
        }
        
        $data['summary'] = $this->production_model->get_drawing_summary(urldecode($drawing_no));
        $data['drawing_no'] = urldecode($drawing_no);
        
        $this->load->view('report/drawing_report_view', $data);
    }
    
    /**
     * Displays the detailed journey report for a given drawing number.
     */
  public function journey($drawing_no = '') {
        if (empty($drawing_no)) {
             show_404();
        }

        // Decode the drawing number from the URL to use in the database query.
        $decoded_drawing_no = urldecode($drawing_no);
        $journey_data = $this->production_model->get_drawing_journey($decoded_drawing_no);
        
        $data['journey'] = $journey_data;
        $data['drawing_no'] = $decoded_drawing_no;

        // Initialize variables for summary calculations
        $total_minutes = 0;
        $total_actual_qty = 0;
        $part_name = 'N/A';
        $operation_count = 0;

        if(!empty($journey_data)){
            // Populate summary data from the first record
            $part_name = $journey_data[0]->part_name;
            $operation_count = count($journey_data);

            // Loop through all journey steps to calculate totals
            foreach($journey_data as $entry){
                $total_minutes += (int)$entry->cycle_time;
                $total_actual_qty += (int)$entry->actual_qty;
            }
        }
        
        $data['part_name'] = $part_name;
        $data['total_journey_time'] = $total_minutes;
        $data['total_actual_qty'] = $total_actual_qty;
        $data['operation_count'] = $operation_count;

        // Load the report view with all the calculated data
        $this->load->view('report/drawing_journey_view', $data);
    }
}
?>