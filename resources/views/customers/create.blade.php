@extends('layouts.app')

@section('title', 'New customer')

@section('content')
    <div class="card" style="max-width:520px">
        <h1 style="margin-bottom:16px">New customer</h1>

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            @include('customers._form', ['customer' => null])

            <button class="btn" type="submit">Create customer</button>
            <a class="btn secondary" href="{{ route('customers.index') }}">Cancel</a>
        </form>
    </div>
@endsection
