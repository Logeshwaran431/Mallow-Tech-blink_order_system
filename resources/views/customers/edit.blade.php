@extends('layouts.app')

@section('title', 'Edit customer')

@section('content')
    <div class="card" style="max-width:520px">
        <h1 style="margin-bottom:16px">Edit customer</h1>

        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')
            @include('customers._form', ['customer' => $customer])

            <button class="btn" type="submit">Save changes</button>
            <a class="btn secondary" href="{{ route('customers.show', $customer) }}">Cancel</a>
        </form>
    </div>
@endsection
