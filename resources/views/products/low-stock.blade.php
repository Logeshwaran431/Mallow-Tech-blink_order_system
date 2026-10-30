@extends('layouts.app')

@section('title', 'Low stock')

@section('content')
    <div class="card">
        <div class="head">
            <h1>Low stock <span class="muted">(≤ {{ $threshold }})</span></h1>
            <form method="GET" action="{{ route('products.low-stock') }}" class="row" style="flex:0 1 380px">
                <div>
                    <label for="threshold">Threshold</label>
                    <input id="threshold" type="number" name="threshold" min="0" value="{{ $threshold }}">
                </div>
                <div style="flex:0 0 auto">
                    <button class="btn" type="submit">Apply</button>
                    <a class="btn secondary" href="{{ route('products.low-stock') }}">Reset</a>
                </div>
            </form>
        </div>

        <p class="muted" style="margin-top:-6px">Enter a quantity and press Add to restock. Restocked products drop off this list once they are above the threshold.</p>

        @include('partials.products-table')
    </div>
@endsection
