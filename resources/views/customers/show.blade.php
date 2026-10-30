@extends('layouts.app')

@section('title', $customer->name)

@section('content')
    <div class="card">
        <div class="head">
            <div>
                <h1>{{ $customer->name }}</h1>
                <div class="muted">{{ $customer->email }}</div>
            </div>
            <div class="row" style="flex:0 0 auto">
                <a class="btn" href="{{ route('orders.create', ['customer_id' => $customer->id]) }}">Place order</a>
                <a class="btn secondary" href="{{ route('customers.edit', $customer) }}">Edit</a>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                      onsubmit="return confirm('Delete this customer?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Order history</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
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
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="num">{{ $order->items_count }}</td>
                            <td class="num">@money($order->subtotal)</td>
                            <td class="num">@money($order->tax_total)</td>
                            <td class="num">@money($order->grand_total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $orders->links('pagination::simple-default') }}</div>
    </div>
@endsection
