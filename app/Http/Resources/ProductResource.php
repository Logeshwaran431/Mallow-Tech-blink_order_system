<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'currency' => config('app.currency'),
            'price' => $this->price,
            'price_formatted' => Money::format($this->price),
            'tax_percentage' => $this->tax_percentage,
            'stock_on_hand' => $this->stock_on_hand,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
