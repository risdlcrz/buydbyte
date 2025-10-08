<?php

if (!function_exists('currency')) {
    /**
     * Format a price with the configured currency symbol
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    function currency($amount, $decimals = 2)
    {
        $symbol = config('app.currency.symbol', '₱');
        return $symbol . number_format($amount, $decimals);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get the currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return config('app.currency.symbol', '₱');
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get the currency code
     *
     * @return string
     */
    function currency_code()
    {
        return config('app.currency.code', 'PHP');
    }
}