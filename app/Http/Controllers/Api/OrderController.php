<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * The order service instance.
     *
     * @var \App\Services\OrderService
     */
    protected $orders;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\OrderService  $orders
     * @return void
     */
    public function __construct(OrderService $orders)
    {
        $this->orders = $orders;
    }

    /**
     * List order history, newest first.
     *
     * Supports filtering by customer_id and by a from/to date range.
     *
     * @param  \App\Http\Requests\Order\IndexOrderRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexOrderRequest $request)
    {
        $orders = Order::query()
            ->with(['customer', 'items.product'])
            ->when($request->validated()['customer_id'] ?? null, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($request->validated()['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->validated()['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest('id')
            ->paginate($request->validated()['per_page'] ?? 15);

        return OrderResource::collection($orders);
    }

    /**
     * Place a new order.
     *
     * @param  \App\Http\Requests\Order\StoreOrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orders->create(
            (int) $request->validated()['customer_id'],
            $request->validated()['items']
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single order with its customer and items.
     *
     * @param  \App\Models\Order  $order
     * @return \App\Http\Resources\OrderResource
     */
    public function show(Order $order)
    {
        return new OrderResource($order->load(['customer', 'items.product']));
    }
}
