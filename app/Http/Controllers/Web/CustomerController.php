<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * List customers, optionally filtered by name or email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Customer::query()
            ->withCount('orders')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    /**
     * Show the form for creating a customer.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a new customer.
     *
     * @param  \App\Http\Requests\Customer\StoreCustomerRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer created.');
    }

    /**
     * Show a customer and their order history.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Customer $customer)
    {
        $orders = $customer->orders()
            ->withCount('items')
            ->latest('id')
            ->paginate(10);

        return view('customers.show', compact('customer', 'orders'));
    }

    /**
     * Show the form for editing a customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update a customer.
     *
     * @param  \App\Http\Requests\Customer\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer updated.');
    }

    /**
     * Delete a customer who has no orders.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->withErrors([
                'customer' => 'Customer has orders and cannot be deleted.',
            ]);
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('status', 'Customer deleted.');
    }
}
