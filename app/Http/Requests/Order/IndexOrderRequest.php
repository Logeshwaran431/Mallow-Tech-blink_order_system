<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
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
     * Every filter is nullable because a submitted filter form sends empty
     * fields, which must mean "no filter" rather than a validation error.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'customer_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'to' => array_filter([
                'nullable',
                'date',
                $this->filled('from') ? 'after_or_equal:from' : null,
            ]),
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'customer_id' => 'customer',
            'from' => 'from date',
            'to' => 'to date',
        ];
    }
}
