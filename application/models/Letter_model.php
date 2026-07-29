<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Letter_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Saves the offer letter data to the database.
     *
     * @param string $recipient_name The name of the recipient for easy identification.
     * @param string $json_data The complete letter data as a JSON string.
     * @return int The ID of the newly inserted letter.
     */
    public function save_letter($recipient_name, $json_data) {
        $data = array(
            'recipient_name' => $recipient_name,
            'letter_data' => $json_data
        );
        
        $this->db->insert('offer_letters', $data);
        
        return $this->db->insert_id();
    }

    /**
     * Fetches a specific offer letter by its ID.
     *
     * @param int $id The ID of the letter to fetch.
     * @return array The letter data row.
     */
    public function get_letter_by_id($id) {
        $query = $this->db->get_where('offer_letters', array('id' => $id));
        return $query->row_array(); // Returns the entire row
    }
}
