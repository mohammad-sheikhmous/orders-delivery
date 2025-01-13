<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Google\Service\BigtableAdmin\DataBoostIsolationReadOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Product::selectionForIndexing()
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if ($products->isEmpty())
                return returnErrorJson('products not found...!', 400);

            return returnDataJson('products', $products, 'all products returned..');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function show($id)
    {
        try {
            $product = Product::selectionForShowing()->find($id);

            if (!$product)
                return returnErrorJson('the product not found...!', 400);

            return returnDataJson('product', $product->makeVisible(['name', 'description']), 'the product returned successfully...');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create(ProductRequest $request)
    {
        try {
            $photoPath = saveImages('products', $request->photo);

            $request->merge([
                'name' => ['en' => $request['name_en'], 'ar' => $request['name_ar']],
                'description' => ['en' => $request['description_en'], 'ar' => $request['description_ar']],
                'photo' => $photoPath,
            ]);

            Product::create($request->except('name_ar', 'name_en', 'description_ar', 'description_en'));

            return returnSuccessJson('New Product created successfully...', 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return returnErrorJson('Product not found...', 400);

            if ($request->photo) {
                Storage::disk('images')->delete($product->photo);

                $photoPath = saveImages('products', $request->photo);
            } else {
                $photoPath = $product->photo;
            }

            $name = ['en' => $request['name_en'], 'ar' => $request['name_ar']];
            $request = $request->except('_method', 'name_ar', 'name_en');
            $request['photo'] = $photoPath;
            $request['name'] = $name;

            $updated = $product->update($request);

            $message = ($updated) ? 'The Product updated successfully...'
                : 'No modifications have been made...';

            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return $exception;
            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return returnErrorJson('The Product not found...', 400);

//            $products = $vendor->products();
//            if (isset($products) && $products->count() > 0)
//                return response()->json([
//                    'status' => false,
//                    'status code' => 400,
//                    'message' => 'The Vendor cannot be deleted...',
//                ], 400);

            $photoPath = $product->photo;
            Storage::disk('images')->delete($photoPath);
            $product->delete();

            return returnSuccessJson('The Product deleted successfully...');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return returnErrorJson('The Product not found...!', 400);

            $status = $product->active == 'active' ? 'inactive' : 'active';

            $product->update(['active' => $status]);

            return returnSuccessJson('The Product status changed successfully...');

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
