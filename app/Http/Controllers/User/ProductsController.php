<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index($product_category_id)
    {
        try {
            $products = Product::where('product_category_id', $product_category_id)
                ->where('active', 0)
                ->where('amount', '>', 0)
                ->select('id', 'name', 'photo', 'description', 'amount', 'price', 'product_category_id')
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if (!isset($products))
                return response()->json([
                    'status' => false,
                    'status code' => 204,
                    'message' => 'products not found...!',
                ], 204);

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

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
