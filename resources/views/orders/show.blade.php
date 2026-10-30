@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
    <div class="card">
        <div class="head">
            <div>
                <h1>Order #{{ $order->id }}</h1>
                <div class="muted">{{ $order->created_at->format('d M Y H:i') }}</div>
            </div>
            <div>
                <a href="{{ route('customers.show', $order->customer) }}">{{ $order->customer->name }}</a>
                <div class="muted">{{ $order->customer->email }}</div>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="num">Qty</th>
                        <th class="num">Unit price</th>
                        <th class="num">Tax %</th>
                        <th class="num">Subtotal</th>
                        <th class="num">Tax</th>
                        <th class="num">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }} <span class="muted">{{ $item->product->code }}</span></td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">@money($item->unit_price)</td>
                            <td class="num">{{ $item->tax_percentage }}</td>
                            <td class="num">@money($item->subtotal)</td>
                            <td class="num">@money($item->tax_amount)</td>
                            <td class="num">@money($item->total)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals" style="margin-top:16px">
            <div><span>Subtotal</span><span>@money($order->subtotal)</span></div>
            <div><span>Tax</span><span>@money($order->tax_total)</span></div>
            <div class="grand"><span>Grand total</span><span>@money($order->grand_total)</span></div>
        </div>
    </div>
@endsection
