<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'currency' => config('app.currency'),
            'subtotal' => $this->subtotal,
            'subtotal_formatted' => Money::format($this->subtotal),
            'tax_total' => $this->tax_total,
            'tax_total_formatted' => Money::format($this->tax_total),
            'grand_total' => $this->grand_total,
            'grand_total_formatted' => Money::format($this->grand_total),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
