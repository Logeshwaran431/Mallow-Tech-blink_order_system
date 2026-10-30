@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="card">
        <div class="head">
            <h1>Orders</h1>
            <a class="btn" href="{{ route('orders.create') }}">Place order</a>
        </div>

        <form method="GET" action="{{ route('orders.index') }}" class="row" style="margin-bottom:12px">
            <div>
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id">
                    <option value="">All customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @if (($filters['customer_id'] ?? null) == $customer->id) selected @endif>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from">From</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div>
                <label for="to">To</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}">
            </div>
            <div style="flex:0 0 auto">
                <button class="btn secondary" type="submit">Filter</button>
                <a class="btn secondary" href="{{ route('orders.index') }}">Clear</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="num">Items</th>
                        <th class="num">Subtotal</th>
                        <th class="num">Tax</th>
                        <th class="num">Grand total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                            <td><a href="{{ route('customers.show', $order->customer) }}">{{ $order->customer->name }}</a></td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="num">{{ $order->items_count }}</td>
                            <td class="num">@money($order->subtotal)</td>
                            <td class="num">@money($order->tax_total)</td>
                            <td class="num">@money($order->grand_total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="muted">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $orders->links('pagination::simple-default') }}</div>
    </div>
@endsection
