@extends('layouts.app')

@section('title', 'Edit ' . $product->name)

@section('content')
    <div class="card">
        <div class="head">
            <div>
                <h1>{{ $product->name }}</h1>
                <div class="muted">{{ $product->code }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:28px; font-weight:700">{{ $product->stock_on_hand }}</div>
                <div>@include('partials.stock-badge', ['stock' => $product->stock_on_hand])</div>
            </div>
        </div>
    </div>

    <div class="row" style="align-items:flex-start">
        <div class="card" style="flex:1 1 300px">
            <h2>Add stock</h2>
            <p class="muted" style="margin-top:-6px">The quantity is added to the current stock.</p>

            <form method="POST" action="{{ route('products.restock', $product) }}">
                @csrf
                <div class="field">
                    <label for="quantity">Quantity to add</label>
                    <input id="quantity" type="number" name="quantity" min="1" max="100000" value="{{ old('quantity') }}" required>
                </div>
                <button class="btn" type="submit">Add stock</button>
            </form>
        </div>

        <div class="card" style="flex:2 1 400px">
            <h2>Product details</h2>

            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="field">
                        <label for="name">Name</label>
                        <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
                    </div>
                    <div class="field">
                        <label for="code">Code</label>
                        <input id="code" name="code" value="{{ old('code', $product->code) }}" required maxlength="50">
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="price">Price ({{ config('app.currency_symbol') }})</label>
                        <input id="price" type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
                    </div>
                    <div class="field">
                        <label for="tax_percentage">Tax %</label>
                        <input id="tax_percentage" type="number" name="tax_percentage" step="0.01" min="0" max="100" value="{{ old('tax_percentage', $product->tax_percentage) }}" required>
                    </div>
                </div>

                <p class="muted">Price changes apply to new orders only. Existing orders keep the price they were placed at.</p>

                <button class="btn" type="submit">Save details</button>
                <a class="btn secondary" href="{{ route('products.index') }}">Back to products</a>
            </form>
        </div>
    </div>
@endsection
