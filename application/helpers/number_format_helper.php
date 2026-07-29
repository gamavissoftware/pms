<?php
if (!function_exists('format_number_indian')) {
    function format_number_indian($number)
    {
        // Check if the number has decimals
        $parts = explode('.', $number);
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? '.' . $parts[1] : '';

        // Format the integer part with Indian numbering system
        $lastThreeDigits = substr($integerPart, -3);
        $remainingDigits = substr($integerPart, 0, -3);

        if ($remainingDigits != '') {
            $formattedNumber = $remainingDigits . ',' . $lastThreeDigits;
        } else {
            $formattedNumber = $lastThreeDigits;
        }

        // Insert commas every two digits from the end
        while (strlen($remainingDigits) > 2) {
            $remainingDigits = substr($remainingDigits, 0, -2);
            $formattedNumber = $remainingDigits . ',' . $formattedNumber;
        }

        return $formattedNumber . $decimalPart;
    }
}
?>
