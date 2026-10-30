<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\LowStockProductRequest;
use App\Http\Requests\Product\RestockProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\InventoryService;

class ProductController extends Controller
{
    /**
     * List products, filtered by name/code search and stock status.
     *
     * @param  \App\Http\Requests\Product\IndexProductRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexProductRequest $request)
    {
        $filters = $request->validated();

        $products = Product::query()
            ->search($filters['search'] ?? null)
            ->stockStatus($filters['status'] ?? null, (int) config('inventory.low_stock_threshold'))
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);

        return ProductResource::collection($products);
    }

    /**
     * List products at or below the low-stock threshold, lowest stock first.
     *
     * @param  \App\Http\Requests\Product\LowStockProductRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function lowStock(LowStockProductRequest $request)
    {
        $threshold = $request->threshold();

        $products = Product::lowStock($threshold)
            ->orderBy('stock_on_hand')
            ->orderBy('name')
            ->paginate($request->validated()['per_page'] ?? 15);

        return ProductResource::collection($products)
            ->additional(['meta' => ['threshold' => $threshold]]);
    }

    /**
     * Show a single product.
     *
     * @param  \App\Models\Product  $product
     * @return \App\Http\Resources\ProductResource
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update a product's details (not its stock).
     *
     * @param  \App\Http\Requests\Product\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \App\Http\Resources\ProductResource
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    /**
     * Add stock to a product.
     *
     * @param  \App\Http\Requests\Product\RestockProductRequest  $request
     * @param  \App\Models\Product  $product
     * @param  \App\Services\InventoryService  $inventory
     * @return \App\Http\Resources\ProductResource
     */
    public function restock(RestockProductRequest $request, Product $product, InventoryService $inventory)
    {
        return new ProductResource(
            $inventory->restock($product, (int) $request->validated()['quantity'])
        );
    }
}
