<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index($product_category_id)
    {
        try {
            $products = Product::where('product_category_id', $product_category_id)
                ->where('active', 1)
                ->where('amount', '>', 0)
                ->selectForindexing()
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if ($products->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'products not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all products returned..',
                'products' => $products,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::selectForShowing()
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->find($id);

            if (!isset($product))
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'the product not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all products returned..',
                'products' => $product,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
