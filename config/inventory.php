<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Products whose stock on hand is at or below this value are reported by
    | the low-stock endpoint. It can be overridden per request with the
    | "threshold" query parameter.
    |
    */

    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 10),

];
