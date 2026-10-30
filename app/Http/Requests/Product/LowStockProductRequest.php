<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class LowStockProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'threshold' => ['nullable', 'integer', 'min:0'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get the stock level at or below which a product counts as low.
     *
     * Falls back to the configured threshold when the field is empty.
     */
    public function threshold(): int
    {
        return $this->filled('threshold')
            ? (int) $this->input('threshold')
            : (int) config('inventory.low_stock_threshold');
    }
}
