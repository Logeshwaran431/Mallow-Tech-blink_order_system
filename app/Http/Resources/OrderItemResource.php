<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'name' => $this->product->name,
                    'code' => $this->product->code,
                ];
            }),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'unit_price_formatted' => Money::format($this->unit_price),
            'tax_percentage' => $this->tax_percentage,
            'subtotal' => $this->subtotal,
            'subtotal_formatted' => Money::format($this->subtotal),
            'tax_amount' => $this->tax_amount,
            'tax_amount_formatted' => Money::format($this->tax_amount),
            'total' => $this->total,
            'total_formatted' => Money::format($this->total),
        ];
    }
}
