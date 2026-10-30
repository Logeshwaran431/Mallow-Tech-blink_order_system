<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Orders') · {{ config('app.name') }}</title>
    <style>
        :root { --bg:#fefce8; --card:#fff; --text:#1f2933; --muted:#6b7280; --line:#fde68a; --accent:#facc15; --accent-hover:#eab308; --accent-text:#422006; --link:#a16207; --danger:#dc2626; --warn:#b45309; --ok:#15803d; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background:var(--bg); color:var(--text); font-size:15px; }
        nav { background:var(--accent); padding:0 16px; display:flex; gap:4px; align-items:center; flex-wrap:wrap; border-bottom:2px solid var(--accent-hover); }
        nav .brand { color:var(--accent-text); font-weight:700; margin-right:16px; padding:14px 0; }
        nav a { color:#713f12; text-decoration:none; padding:14px 10px; }
        nav a.active, nav a:hover { color:var(--accent-text); font-weight:600; box-shadow:inset 0 -3px 0 var(--accent-text); }
        main { max-width:1100px; margin:24px auto; padding:0 16px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:8px; padding:20px; margin-bottom:20px; }
        .head { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
        h1 { font-size:22px; margin:0; } h2 { font-size:17px; margin:0 0 12px; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); white-space:nowrap; }
        th { color:var(--muted); font-weight:600; font-size:13px; }
        td.num, th.num { text-align:right; font-variant-numeric: tabular-nums; }
        a { color:var(--link); }
        .btn { display:inline-block; background:var(--accent); color:var(--accent-text); font-weight:600; border:0; border-radius:6px; padding:8px 14px; font-size:14px; text-decoration:none; cursor:pointer; }
        .btn:hover { background:var(--accent-hover); }
        .btn.secondary { background:#fff; color:var(--text); border:1px solid #d1d5db; font-weight:500; }
        .btn.secondary:hover { background:#fef9c3; }
        .btn.danger { background:var(--danger); color:#fff; }
        input:focus, select:focus { outline:2px solid var(--accent); outline-offset:1px; border-color:var(--accent-hover); }
        th { background:#fefce8; }
        label { display:block; font-weight:600; font-size:13px; margin-bottom:4px; }
        input, select { width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; background:#fff; }
        .field { margin-bottom:14px; }
        .row { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
        .row > * { flex:1 1 160px; }
        .alert { padding:12px 14px; border-radius:6px; margin-bottom:16px; }
        .alert.ok { background:#dcfce7; color:var(--ok); }
        .alert.err { background:#fee2e2; color:var(--danger); }
        .alert ul { margin:0; padding-left:18px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; font-weight:600; }
        .badge.out { background:#fee2e2; color:var(--danger); }
        .badge.low { background:#fef3c7; color:var(--warn); }
        .badge.ok { background:#dcfce7; color:var(--ok); }
        .muted { color:var(--muted); }
        .totals { margin-left:auto; max-width:320px; }
        .totals div { display:flex; justify-content:space-between; padding:4px 0; }
        .totals .grand { font-weight:700; border-top:1px solid var(--line); margin-top:6px; padding-top:8px; }
        .pagination { margin-top:14px; }
        .inline-form { display:flex; gap:6px; align-items:center; }
        .inline-form input { width:80px; padding:6px 8px; }
        .inline-form .btn { padding:6px 10px; }
        .pagination nav { background:none; padding:0; }
    </style>
</head>
<body>
    <nav>
        <span class="brand">{{ config('app.name') }}</span>
        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Orders</a>
        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">Products</a>
        <a href="{{ route('products.low-stock') }}" class="{{ request()->routeIs('products.low-stock') ? 'active' : '' }}">Low stock</a>
    </nav>

    <main>
        @if (session('status'))
            <div class="alert ok">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert err">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
