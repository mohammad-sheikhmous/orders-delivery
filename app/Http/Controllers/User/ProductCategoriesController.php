<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductCategoriesController extends Controller
{
    public function index($vendor_id)
    {
        try {
            $productCategories = ProductCategory::where('vendor_id', $vendor_id)
                ->where('active', 1)
                ->select('id', 'name', 'slug', 'photo')->get();

            if ($productCategories->isEmpty())
                return returnErrorJson(__('messages.product categories not found...!'), 400);

            return returnDataJson('productCategories', $productCategories, __('messages.all categories returned...'),);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
