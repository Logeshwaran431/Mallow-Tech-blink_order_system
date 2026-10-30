@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="card">
        <div class="head">
            <h1>Customers</h1>
            <a class="btn" href="{{ route('customers.create') }}">New customer</a>
        </div>

        <form method="GET" action="{{ route('customers.index') }}" class="row" style="margin-bottom:12px">
            <div>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search name or email">
            </div>
            <div style="flex:0 0 auto">
                <button class="btn secondary" type="submit">Search</button>
                <a class="btn secondary" href="{{ route('customers.index') }}">Clear</a>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="num">Orders</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                            <td>{{ $customer->email }}</td>
                            <td class="num">{{ $customer->orders_count }}</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $customers->links('pagination::simple-default') }}</div>
    </div>
@endsection
