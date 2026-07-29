<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parts Controller
 *
 * This controller is an example of how to use the Spare_parts_model.
 */
class Parts extends CI_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        // Load the model so we can use it in our functions
        $this->load->model('Spare_parts_model');
        // We'll also need the URL helper for this example
        $this->load->helper('url');
    }

    /**
     * Index Page for this controller.
     *
     * This function will fetch the transformed data and display it as JSON.
     * You can access this by visiting: http://your-site.com/index.php/parts
     */
    public function index() {
        // Call the model function to get the data
        $transformed_data = $this->Spare_parts_model->get_and_transform_codes();

        // Set the content type header to display JSON correctly
        header('Content-Type: application/json');
        
        // Encode the data as JSON and output it
        echo json_encode($transformed_data, JSON_PRETTY_PRINT);
    }

    /**
     * Example of how to show the data in a simple view.
     *
     * You can access this by visiting: http://your-site.com/index.php/parts/show_list
     */
    public function show_list() {
        // Get the data from the model
        $data['parts_list'] = $this->Spare_parts_model->get_and_transform_codes();

        // You would normally load a view file here, like:
        // $this->load->view('parts_list_view', $data);

        // For this example, we'll just print the data
        echo "<pre>";
        print_r($data['parts_list']);
        echo "</pre>";
    }

    public function run_update() {
        
        // Call the update model function
        $affected_rows = $this->Spare_parts_model->update_codes_to_m11();

        // Show a confirmation message
        echo "<h1>Update Executed</h1>";
        echo "<p><strong>" . $affected_rows . "</strong> rows in your database have been permanently updated.</p>";
        echo "<p>Please check your database to confirm the changes.</p>";
    }
}

/* End of file Parts.php */
/* Location: ./application/controllers/Parts.php */
