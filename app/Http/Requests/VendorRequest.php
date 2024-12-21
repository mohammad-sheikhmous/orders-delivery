<?php

namespace App\Http\Requests;

use App\Rules\StrongPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'name' => 'required|string|max:30',
            'mobile' => 'required|digits:10|starts_with:09|unique:vendors,mobile,' . $this->vendor,
            'address' => 'string|max:300',
//            'email'=>'sometimes|nullable|email',
            'email' => 'email|unique:vendors,email,' . $this->id,
            'password' => ['required', 'string', 'confirmed', 'min:8', new StrongPasswordRule()],
            'active' => 'required|in:inactive,active',
            'main_category_id' => 'required|exists:main_categories,id'
        ];
    }
}
