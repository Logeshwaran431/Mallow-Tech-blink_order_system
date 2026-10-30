<?php

namespace App\Support;

class Money
{
    /**
     * Format an amount with the currency symbol and Indian digit grouping.
     *
     * Works on the decimal string directly, so no float rounding is involved:
     * "123456.7" becomes "₹1,23,456.70".
     *
     * @param  string|int|float|null  $amount
     * @return string
     */
    public static function format($amount): string
    {
        $amount = (string) ($amount ?? '0');
        $negative = strpos($amount, '-') === 0;
        $amount = bcadd(ltrim($amount, '-'), '0', 2);

        [$whole, $fraction] = explode('.', $amount);

        // Last three digits, then groups of two: 12,34,567.
        if (strlen($whole) > 3) {
            $lastThree = substr($whole, -3);
            $rest = substr($whole, 0, -3);
            $whole = strrev(implode(',', str_split(strrev($rest), 2))).','.$lastThree;
        }

        return ($negative ? '-' : '').config('app.currency_symbol').$whole.'.'.$fraction;
    }
}
