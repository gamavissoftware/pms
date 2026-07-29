<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_live_inr_to_usd')) {
    /**
     * Fetches real-time INR to USD conversion rate
     * Using Open Exchange Rates API (Free Tier)
     */
    function get_live_inr_to_usd() {
        $url = "https://open.er-api.com/v6/latest/INR";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['rates']['USD'])) {
                return (float)$data['rates']['USD'];
            }
        }
        
        // Fallback rate if API fails (approximate value for 2026)
        return 0.012; 
    }
}

if (!function_exists('get_usd_in_words')) {
    /**
     * Simple USD currency formatter for the footer
     */
    function get_usd_in_words($number) {
        return "USD " . number_format($number, 2) . " ONLY";
    }
}