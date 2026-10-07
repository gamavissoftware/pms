<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_amount_in_words')) {
    /**
     * Converts a number to words (Supports international USD/EUR and Indian INR).
     *
     * @param float $number The number to convert.
     * @param string $currency 'INR', 'USD', or 'EUR'.
     * @return string The number in words.
     */
    function get_amount_in_words(float $number, $currency = 'INR')
    {
        $number = round($number, 2);
        $no = floor($number);
        $decimal = round(($number - $no) * 100);
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );

        $currency = strtoupper(trim((string) $currency));

        if (in_array($currency, array('USD', 'EUR'), true)) {
            // --- International Numbering System (Millions/Billions) ---
            $levels = array('', 'Thousand', 'Million', 'Billion', 'Trillion');
            
            // Handle zero case
            if ($no == 0) {
                $MainCurrency = "Zero";
            } else {
                while ($i < $digits_length) {
                    $divider = 1000;
                    $current_chunk = floor($no % $divider);
                    $no = floor($no / $divider);
                    
                    if ($current_chunk) {
                        $chunk_str = array();
                        
                        // Handle Hundreds in the chunk
                        if (floor($current_chunk / 100)) {
                            $chunk_str[] = $words[floor($current_chunk / 100)] . ' Hundred';
                        }
                        
                        // Handle Tens and Ones in the chunk
                        $remainder = $current_chunk % 100;
                        if ($remainder) {
                            if ($remainder < 21) {
                                $chunk_str[] = $words[$remainder];
                            } else {
                                $chunk_str[] = $words[floor($remainder / 10) * 10] . ' ' . $words[$remainder % 10];
                            }
                        }
                        
                        $level_name = $levels[count($str)];
                        $str[] = trim(implode(' ', $chunk_str) . ' ' . $level_name);
                    } else {
                        $str[] = null;
                    }
                    $i += 3;
                }
                $MainCurrency = implode(' ', array_reverse(array_filter($str)));
            }

            $SubCurrency = ($decimal > 0) ? "and " . ($decimal < 21 ? $words[(int)$decimal] : $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10]) . ' Cents' : '';
            
            $currency_name = $currency === 'EUR' ? 'Euros' : 'US Dollars';

            return trim($MainCurrency) . ' ' . $currency_name . ' ' . trim($SubCurrency) . " Only";

        } else {
            // --- Indian Numbering System (Lakh/Crore) ---
            $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $current_number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                
                if ($current_number) {
                    $counter = count($str);
                    $unit = ($current_number < 21) ? $words[$current_number] : $words[floor($current_number / 10) * 10] . ' ' . $words[$current_number % 10];
                    $str[] = trim($unit . ' ' . $digits[$counter]);
                } else {
                    $str[] = null;
                }
            }
            
            $MainCurrency = implode(' ', array_reverse(array_filter($str)));
            $SubCurrency = ($decimal > 0) ? "and " . ($decimal < 21 ? $words[(int)$decimal] : $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
            
            return trim($MainCurrency) . ' Rupees ' . trim($SubCurrency) . " Only";
        }
    }
}
