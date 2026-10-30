<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * List customers, optionally filtered by name or email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->when($request->query('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(max(1, min((int) $request->query('per_page', 15), 100)));

        return CustomerResource::collection($customers);
    }

    /**
     * Create a customer.
     *
     * @param  \App\Http\Requests\Customer\StoreCustomerRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer->loadCount('orders'));
    }

    /**
     * Update a customer.
     *
     * @param  \App\Http\Requests\Customer\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \App\Http\Resources\CustomerResource
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return new CustomerResource($customer);
    }

    /**
     * Delete a customer who has no orders.
     *
     * Customers with orders are kept so order history stays intact.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return response()->json([
                'message' => 'Customer has orders and cannot be deleted.',
            ], 409);
        }

        $customer->delete();

        return response()->json(null, 204);
    }
}
