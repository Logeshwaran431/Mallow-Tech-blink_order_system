@extends('layouts.app')

@section('title', 'Place order')

@section('content')
    @php
        $rows = old('items', [[], [], []]);
    @endphp

    <div class="card">
        <h1 style="margin-bottom:16px">Place order</h1>

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf

            <div class="field" style="max-width:420px">
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id" required>
                    <option value="">Select a customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            @if (old('customer_id', request('customer_id')) == $customer->id) selected @endif>
                            {{ $customer->name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <h2>Items</h2>
            <p class="muted" style="margin-top:-6px">Prices and tax are taken from the product at the time the order is placed. Empty rows are ignored.</p>

            <div id="items">
                @foreach ($rows as $i => $row)
                    <div class="row item-row field">
                        <div style="flex:3 1 280px">
                            <label>Product</label>
                            <select name="items[{{ $i }}][product_id]">
                                <option value="">Select a product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @if (($row['product_id'] ?? null) == $product->id) selected @endif
                                        @if ($product->stock_on_hand <= 0) disabled @endif>
                                        {{ $product->name }} · {{ $product->code }} · @money($product->price) · {{ $product->stock_on_hand }} in stock
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1 1 100px">
                            <label>Quantity</label>
                            <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $row['quantity'] ?? '' }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="btn secondary" type="button" id="add-item">Add another product</button>

            <div style="margin-top:20px">
                <button class="btn" type="submit">Place order</button>
                <a class="btn secondary" href="{{ route('orders.index') }}">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('add-item').addEventListener('click', function () {
            var container = document.getElementById('items');
            var rows = container.querySelectorAll('.item-row');
            var clone = rows[rows.length - 1].cloneNode(true);
            var index = rows.length;

            clone.querySelectorAll('select, input').forEach(function (el) {
                el.name = el.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                el.value = '';
            });

            container.appendChild(clone);
        });
    </script>
@endsection
