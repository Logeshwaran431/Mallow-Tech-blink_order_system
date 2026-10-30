Dear {!! $order->customer->name !!},

Your order has been created successfully. The details are as follows:

Order number : #{{ $order->id }}
Order date   : {{ $order->created_at->format('d M Y, h:i A') }}

Items:
@foreach ($order->items as $item)
  - {!! $item->product->name !!} ({{ $item->product->code }})
    {{ $item->quantity }} x {{ \App\Support\Money::format($item->unit_price) }} + tax {{ \App\Support\Money::format($item->tax_amount) }} ({{ $item->tax_percentage }}%) = {{ \App\Support\Money::format($item->total) }}
@endforeach

Subtotal     : {{ \App\Support\Money::format($order->subtotal) }}
Tax          : {{ \App\Support\Money::format($order->tax_total) }}
Grand total  : {{ \App\Support\Money::format($order->grand_total) }}

Thank you for your order.

Regards,
{!! config('app.name') !!}
