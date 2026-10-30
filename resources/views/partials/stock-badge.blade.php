@if ($stock <= 0)
    <span class="badge out">Out of stock</span>
@elseif ($stock <= config('inventory.low_stock_threshold'))
    <span class="badge low">Low</span>
@else
    <span class="badge ok">In stock</span>
@endif
