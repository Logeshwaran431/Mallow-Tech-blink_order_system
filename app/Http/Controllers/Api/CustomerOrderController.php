<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    /**
     * List the order history of a single customer, newest first.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Customer $customer)
    {
        $orders = $customer->orders()
            ->with('items.product')
            ->latest('id')
            ->paginate(max(1, min((int) $request->query('per_page', 15), 100)));

        return OrderResource::collection($orders);
    }
}
