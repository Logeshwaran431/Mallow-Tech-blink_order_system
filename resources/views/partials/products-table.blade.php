<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th class="num">Price</th>
                <th class="num">Tax %</th>
                <th class="num">Stock</th>
                <th>Status</th>
                <th>Add stock</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="num">@money($product->price)</td>
                    <td class="num">{{ $product->tax_percentage }}</td>
                    <td class="num">{{ $product->stock_on_hand }}</td>
                    <td>@include('partials.stock-badge', ['stock' => $product->stock_on_hand])</td>
                    <td>
                        <form method="POST" action="{{ route('products.restock', $product) }}" class="inline-form">
                            @csrf
                            <input type="number" name="quantity" min="1" max="100000" placeholder="Qty" required aria-label="Quantity to add for {{ $product->code }}">
                            <button class="btn" type="submit">Add</button>
                        </form>
                    </td>
                    <td><a href="{{ route('products.edit', $product) }}">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">{{ $products->links('pagination::simple-default') }}</div>
