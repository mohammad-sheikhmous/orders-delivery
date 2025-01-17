<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'description' => 'nullable|string',
            'amount' => 'required|integer|min:1',
            'price' => 'required|numeric:10',
            'active' => 'required|in:inactive,active',
            'product_category_id' => 'required|exists:product_categories,id'
        ];
    }
}
