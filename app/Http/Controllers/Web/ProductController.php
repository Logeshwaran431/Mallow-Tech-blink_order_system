<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\LowStockProductRequest;
use App\Http\Requests\Product\RestockProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\InventoryService;

class ProductController extends Controller
{
    /**
     * Show the product catalogue, filtered by search and stock status.
     *
     * @param  \App\Http\Requests\Product\IndexProductRequest  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexProductRequest $request)
    {
        $filters = $request->validated();

        $products = Product::query()
            ->search($filters['search'] ?? null)
            ->stockStatus($filters['status'] ?? null, (int) config('inventory.low_stock_threshold'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products', 'filters'));
    }

    /**
     * Show products at or below the low-stock threshold.
     *
     * @param  \App\Http\Requests\Product\LowStockProductRequest  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function lowStock(LowStockProductRequest $request)
    {
        $threshold = $request->threshold();

        $products = Product::lowStock($threshold)
            ->orderBy('stock_on_hand')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.low-stock', compact('products', 'threshold'));
    }

    /**
     * Show the form for editing a product and adding stock.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update a product's details (not its stock).
     *
     * @param  \App\Http\Requests\Product\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.edit', $product)
            ->with('status', 'Product updated.');
    }

    /**
     * Add stock to a product and return to the previous page.
     *
     * @param  \App\Http\Requests\Product\RestockProductRequest  $request
     * @param  \App\Models\Product  $product
     * @param  \App\Services\InventoryService  $inventory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restock(RestockProductRequest $request, Product $product, InventoryService $inventory)
    {
        $quantity = (int) $request->validated()['quantity'];
        $product = $inventory->restock($product, $quantity);

        return back()->with(
            'status',
            "Added {$quantity} to {$product->name}. Stock is now {$product->stock_on_hand}."
        );
    }
}
