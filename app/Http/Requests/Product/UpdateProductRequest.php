<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
     * Stock is deliberately not editable here: overwriting the stock count
     * from a form could undo deductions made by orders placed meanwhile.
     * Stock is increased through the restock endpoint instead.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'code')->ignore($this->route('product')),
            ],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'tax_percentage' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'price.regex' => 'The price may have at most 2 decimal places.',
            'tax_percentage.regex' => 'The tax percentage may have at most 2 decimal places.',
        ];
    }
}
