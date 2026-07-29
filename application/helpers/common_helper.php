<?php 
if (!function_exists('get_live_inr_to_usd')) {
    function get_live_inr_to_usd() {
        // Using a reliable free API (ExchangeRate-API or similar)
        // For production, you might want to cache this value for 24 hours
        $url = "https://open.er-api.com/v6/latest/INR";
        $response = file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            return $data['rates']['USD'] ?? 0.012; // Fallback to approx rate if API fails
        }
        return 0.012; 
    }
}