<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\Order\StoreOrderRequest;

class PlaceOrderRequest extends StoreOrderRequest
{
    /**
     * Prepare the data for validation.
     *
     * The order form always renders several item rows, so rows the user left
     * empty are dropped before the shared order rules are applied.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $items = array_filter((array) $this->input('items', []), function ($item) {
            return ! empty($item['product_id']) || ! empty($item['quantity']);
        });

        $this->merge(['items' => array_values($items)]);
    }
}
