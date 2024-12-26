<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
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
            return returnExceptionJson();
        }
    }

    public function show($id)
    {
        try {
            $product = Product::selectionForShowing()->find($id);

            if (!$product)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'the product not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'the product returned successfully...',
                'product' => $product->makeVisible(['name', 'description']),
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
        }}

    public function create(ProductRequest $request)
    {
        try {
            $photoPath = saveImages('products', $request->photo);

            $request->merge([
                'name' => ['en' => $request->name_en, 'ar' => $request->name_ar]
            ]);

            $request = $request->toArray();
            $request['photo'] = $photoPath;

            Product::create(
                $request
            );

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'New Product created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Product not found...'
                ]);

            if ($request->photo) {
                Storage::disk('images')->delete($product->photo);

                $photoPath = saveImages('products', $request->photo);
            } else {
                $photoPath = $product->photo;
            }
            $request->merge([
                'photo' => $photoPath,
                'name' => ['en' => $request->name_en, 'ar' => $request->name_ar]
            ]);

            $updated = $product->update($request->all());

            $message = ($updated) ? 'The Product updated successfully...'
                : 'No modifications have been made...';

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => $message
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Product not found...',
                ], 400);

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

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Product deleted successfully...',
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Product not found...!',
                ]);

            $status = $product->active == 'active' ? 'inactive' : 'active';

            $product->update(['active' => $status]);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => "The Product status changed successfully...",
            ]);

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
