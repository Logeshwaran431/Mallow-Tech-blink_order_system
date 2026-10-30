<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Add stock to a product.
     *
     * The increment is a single atomic UPDATE (stock_on_hand = stock_on_hand + n),
     * so it is safe to run while orders are deducting stock concurrently.
     *
     * @param  \App\Models\Product  $product
     * @param  int  $quantity
     * @return \App\Models\Product
     */
    public function restock(Product $product, int $quantity): Product
    {
        $product->increment('stock_on_hand', $quantity);
        $product->refresh();

        Log::info('Product restocked.', [
            'product_id' => $product->id,
            'code' => $product->code,
            'added' => $quantity,
            'stock_on_hand' => $product->stock_on_hand,
        ]);

        return $product;
    }
}
