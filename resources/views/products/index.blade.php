@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="card">
        <div class="head">
            <h1>Products</h1>
            <a class="btn secondary" href="{{ route('products.low-stock') }}">View low stock</a>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="row" style="margin-bottom:12px">
            <div>
                <label for="search">Search</label>
                <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name or code">
            </div>
            <div>
                <label for="status">Stock status</label>
                <select id="status" name="status">
                    <option value="">All</option>
                    @foreach (['in_stock' => 'In stock', 'low_stock' => 'Low stock', 'out_of_stock' => 'Out of stock'] as $value => $label)
                        <option value="{{ $value }}" @if (($filters['status'] ?? null) === $value) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:0 0 auto">
                <button class="btn secondary" type="submit">Filter</button>
                <a class="btn secondary" href="{{ route('products.index') }}">Clear</a>
            </div>
        </form>

        @include('partials.products-table')
    </div>
@endsection
