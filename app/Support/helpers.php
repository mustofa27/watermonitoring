<?php

if (! function_exists('num_id')) {
    function num_id($number, int $decimals = 2): string
    {
        if ($number === null || $number === '') {
            return '0';
        }

        if (is_string($number)) {
            $number = (float) $number;
        }

        $formatted = number_format((float) $number, $decimals, ',', '.');
        
        // Remove trailing zeros after decimal point, but keep at least one if there are decimals
        $formatted = preg_replace('/,?0+$/', '', $formatted);
        
        return $formatted;
    }
}

