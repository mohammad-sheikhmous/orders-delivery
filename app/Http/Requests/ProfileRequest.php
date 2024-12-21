<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'firstName' => ['required', 'string', 'min:2', 'max:15'],
            'lastName' => ['required', 'string', 'min:2', 'max:15'],
            'email' => ['email', 'unique:profiles,email,' . auth()->user()->profile->id],
//            'mobile' => ['required', 'digits:10', 'unique:users,mobile,' . auth()->user()->id, 'starts_with:09'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => ['string', 'max:150']
        ];
    }
}
