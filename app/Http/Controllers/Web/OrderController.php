<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\Web\PlaceOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
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
     * Show order history, newest first.
     *
     * @param  \App\Http\Requests\Order\IndexOrderRequest  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(IndexOrderRequest $request)
    {
        $filters = $request->validated();

        $orders = Order::query()
            ->with('customer')
            ->withCount('items')
            ->when($filters['customer_id'] ?? null, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($filters['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get(['id', 'name']);

        return view('orders.index', compact('orders', 'customers', 'filters'));
    }

    /**
     * Show the form for placing an order.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'email']);
        $products = Product::orderBy('name')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Place an order.
     *
     * Insufficient stock is handled by the exception, which sends the user
     * back to the form with their input and an error per product.
     *
     * @param  \App\Http\Requests\Web\PlaceOrderRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PlaceOrderRequest $request)
    {
        $order = $this->orders->create(
            (int) $request->validated()['customer_id'],
            $request->validated()['items']
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('status', "Order #{$order->id} placed.");
    }

    /**
     * Show a single order with its customer and items.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);

        return view('orders.show', compact('order'));
    }
}
