<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Zend\Barcode\Barcode;

class BarcodeController extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function generate() {
        // Generate barcode
        $barcodeOptions = ['text' => '123456789']; // Change the text as needed
        $rendererOptions = [];

        $imageResource = Barcode::factory('code128', 'image', $barcodeOptions, $rendererOptions)->draw();

        // Output the image
        header('Content-type: image/png');
        imagepng($imageResource);
        imagedestroy($imageResource);
    }

    public function scan() {
        $this->load->view('scan_barcode');
    }

    public function update_record() {
        // Get the scanned barcode from the form
        $barcode = $this->input->post('barcode');

        // Load database
        $this->load->database();

        // Search for the record associated with the scanned barcode
        $query = $this->db->get_where('products', array('barcode' => $barcode));

        if ($query->num_rows() > 0) {
            // Update the record
            $row = $query->row();
            $id = $row->id;
            
            // Perform the update here, for example:
            // $this->db->where('id', $id);
            // $this->db->update('products', array('name' => 'New Product Name'));

            echo "Record updated successfully.";
        } else {
            echo "No record found for the scanned barcode.";
        }
    }

}