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
                ->where('active', 0)
                ->where('amount', '>', 0)
                ->select('id', 'name', 'photo', 'description', 'amount', 'price', 'product_category_id')
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            $products = $products->fresh();
            if (!$products)
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

    public function store()
    {
        try {
            DB::beginTransaction();

            $main_category = MainCategory::create([
                'name'=> 'clothes',
                'photo'=> 'lllllll',
            ]);

            $vendor = Vendor::create([
                'name'=> 'clothes',
                'logo'=> 'lllllll',
                'password'=> '11111111',
                'mobile'=> '0936873488',
                'main_category_id'=> $main_category->id,
            ]);

            $product_category = ProductCategory::create([
                'name'=> 'clothes',
                'photo'=> 'lllllll',
                'vendor_id'=> $vendor->id,
            ]);

            Product::create([
                'name'=> 'clothes',
                'photo'=> 'lllllll',
                'amount'=> 20,
                'price'=> 1000,
                'product_category_id'=> $product_category->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'new product created...'
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

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
