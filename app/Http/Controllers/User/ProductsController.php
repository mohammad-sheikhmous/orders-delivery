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
            $userId = auth()->id(); // Get the logged-in user's ID.

            // Fetch all products and join with the favorites for the current user.
            $products = Product::where('product_category_id', $product_category_id)
                ->where('active', 1)
                ->where('amount', '>', 0)
                ->leftJoin('favorites', function ($join) use ($userId) {
                    $join->on('products.id', '=', 'favorites.product_id')
                        ->where('favorites.user_id', '=', $userId);
                })
                ->select('products.id', 'name', 'photo', 'product_category_id', DB::raw('CASE WHEN favorites.id IS NOT NULL THEN true ELSE false END as is_favorite'))
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get()->makeHidden('translated_description');
//            'products.*'

            if ($products->isEmpty())
                return returnErrorJson(__('messages.products not found...!'), 400);

            return returnDataJson('products', $products, __('messages.all products returned..'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function show($id)
    {
        try {
            $product = Product::selectionForShowing()
                ->where('active', 1)
                ->where('amount', '>', 0)
                ->find($id);

            if (!isset($product))
                return returnErrorJson(__('messages.the product not found...!'), 400);

            $product->productCategory = $product->productCategory()->value('name');
            $product->vendor = $product->productCategory()->getRelation('vendor')->value('name');
            $product->mainCategory = $product->productCategory()->getRelation('vendor')->getRelation('mainCategory')->value('name');

            return returnDataJson('products', $product, __('messages.the product returned successfully for ID: '));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
