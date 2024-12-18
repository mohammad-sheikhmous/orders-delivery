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
                ->select('id', 'name', 'slug', 'photo')->get();

            if (!$productCategories)
                return response()->json([
                    'status' => false,
                    'status code' => 204,
                    'message' => 'product categories not found...!',
                ], 204);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all categories returned...',
                'productCategories' => $productCategories,
            ]);

        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400));
        }
    }
}
