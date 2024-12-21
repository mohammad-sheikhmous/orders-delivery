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
                'vendors' => $products,
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
                'vendor' => $product,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function create(ProductRequest $request)
    {
        try {
            $photoPath = saveImages('products', $request->photo);

            $request->merge(['photo' => $photoPath]);

            Product::create($request->all());

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'New Product created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
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
            $request->merge(['photo' => $photoPath]);

            $updated = $product->update($request->all());

            $message = ($updated) ? 'The Product updated successfully...'
                : 'No modifications have been made...';

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => $message
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
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
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
