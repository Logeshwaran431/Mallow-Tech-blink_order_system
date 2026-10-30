<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Jobs\SendOrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Number of decimal places used for intermediate money calculations.
     */
    private const SCALE = 6;

    /**
     * Place an order, deduct stock and queue the confirmation email.
     *
     * The products are locked with SELECT ... FOR UPDATE inside a transaction,
     * so two concurrent orders for the same product are serialised and stock
     * can never be oversold.
     *
     * @param  int  $customerId
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     * @return \App\Models\Order
     *
     * @throws \App\Exceptions\InsufficientStockException
     */
    public function create(int $customerId, array $items): Order
    {
        $quantities = $this->quantitiesByProduct($items);

        $order = DB::transaction(function () use ($customerId, $quantities) {
            // Lock in a consistent (id) order to avoid deadlocks between
            // concurrent orders that share products.
            $products = Product::whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $this->ensureStockIsAvailable($products, $quantities);

            $order = Order::create(['customer_id' => $customerId]);

            $subtotal = '0';
            $taxTotal = '0';

            foreach ($quantities as $productId => $quantity) {
                $product = $products[$productId];
                $line = $this->calculateLine($product, $quantity);

                $order->items()->create($line + [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);

                $product->decrement('stock_on_hand', $quantity);

                $subtotal = bcadd($subtotal, $line['subtotal'], 2);
                $taxTotal = bcadd($taxTotal, $line['tax_amount'], 2);
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => bcadd($subtotal, $taxTotal, 2),
            ]);

            return $order;
        });

        // Dispatched after commit so the worker never sees an uncommitted order.
        SendOrderConfirmation::dispatch($order);

        return $order->load(['customer', 'items.product']);
    }

    /**
     * Collapse the requested items into a product id => quantity map.
     *
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     * @return array<int, int>
     */
    private function quantitiesByProduct(array $items): array
    {
        $quantities = [];

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $item['quantity'];
        }

        return $quantities;
    }

    /**
     * Throw if any locked product has less stock than requested.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Product>  $products
     * @param  array<int, int>  $quantities
     * @return void
     *
     * @throws \App\Exceptions\InsufficientStockException
     */
    private function ensureStockIsAvailable($products, array $quantities): void
    {
        $shortages = [];

        foreach ($quantities as $productId => $quantity) {
            $product = $products->get($productId);
            $available = $product ? $product->stock_on_hand : 0;

            if ($available < $quantity) {
                $shortages[] = [
                    'product_id' => $productId,
                    'code' => $product ? $product->code : null,
                    'requested' => $quantity,
                    'available' => $available,
                ];
            }
        }

        if ($shortages) {
            throw new InsufficientStockException($shortages);
        }
    }

    /**
     * Calculate the price snapshot and totals for one order line.
     *
     * Tax is calculated and rounded (half up) per line, and the order totals
     * are the sum of the rounded lines, so they always reconcile.
     *
     * @return array<string, string>
     */
    private function calculateLine(Product $product, int $quantity): array
    {
        $unitPrice = (string) $product->price;
        $taxPercentage = (string) $product->tax_percentage;

        $subtotal = bcmul($unitPrice, (string) $quantity, 2);
        $taxAmount = $this->roundMoney(
            bcdiv(bcmul($subtotal, $taxPercentage, self::SCALE), '100', self::SCALE)
        );

        return [
            'unit_price' => $unitPrice,
            'tax_percentage' => $taxPercentage,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => bcadd($subtotal, $taxAmount, 2),
        ];
    }

    /**
     * Round a non-negative decimal string to two places, half up.
     */
    private function roundMoney(string $amount): string
    {
        return bcadd($amount, '0.005', 2);
    }
}
