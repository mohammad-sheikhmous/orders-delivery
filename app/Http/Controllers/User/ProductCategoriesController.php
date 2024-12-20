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
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'product categories not found...!',
                ], 400);

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
