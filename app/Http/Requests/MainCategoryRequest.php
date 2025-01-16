<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MainCategoryRequest extends FormRequest
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

//    protected function failedValidation(Validator $validator)
//    {
//        //   parent::failedValidation($validator); // TODO: Change the autogenerated stub
//        throw new HttpResponseException(
//            returnErrorJson($validator->errors()->first(), 422, 'errors')
//        );
//    }

    protected function prepareForValidation()
    {
        $this->merge(['active' => ($this->active == 1) ? 'active' : 'inactive']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //'names' => 'required|array|min:1',
            'name_ar' => 'required|string|max:50',
            'name_en' => 'required|string|max:50',
            'photo' => 'required_without:id|image|mimes:jpeg,png,jpg,gif,svg',
            'slug' => 'string|50',
            'active' => 'required|in:inactive,active'
        ];
    }

    public function messages(): array
    {
        return [
            // Name English
            'name_en.required' => __('validation/mainCategories.The English name is required.'),
            'name_en.string' => __('validation/mainCategories.The English name must be a string.'),
            'name_en.max' => __('validation/mainCategories.The English name may not be greater than 50 characters.'),

            // Name Arabic
            'name_ar.required' => __('validation/mainCategories.The Arabic name is required.'),
            'name_ar.string' => __('validation/mainCategories.The Arabic name must be a string.'),
            'name_ar.max' => __('validation/mainCategories.The Arabic name may not be greater than 50 characters.'),

            // Photo
            'photo.required' => __('validation/mainCategories.A photo is required.'),
            'photo.image' => __('validation/mainCategories.The photo must be an image.'),
            'photo.mimes' => __('validation/mainCategories.The photo must be of type: jpeg, png, jpg, gif, svg.'),

            // Slug
            'slug.string' => __('validation/mainCategories.The slug must be a string.'),
            'slug.max' => __('validation/mainCategories.The slug may not be greater than 50 characters.'),

            // Active
            'active.required' => __('validation/mainCategories.The status is required.'),
            'active.in' => __('validation/mainCategories.The status must be either inactive or active.'),
        ];
    }
}
