<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Google\Service\BigtableAdmin\DataBoostIsolationReadOnly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index()
    {
        try {
            $products = Product::selectionForShowing()
                ->with(['productCategory' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if ($products->isEmpty())
                return to_route('admin.dashboard')->with(['error' => 'products not found...!']);

//            return returnErrorJson('products not found...!', 400);

            return view('admin.products.index', ['products' => $products]);

//            return returnDataJson('products', $products, 'all products returned..');

        } catch (\Exception $exception) {
            return to_route('admin.dashboard')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
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

    public function create()
    {
        $productCategories = ProductCategory::selection()->get();

        return view('admin.products.create', ['productCategories' => $productCategories]);
    }

    public function store(ProductRequest $request)
    {
        try {
            $photoPath = saveImages('products', $request->photo);

            $request = $request->toArray();

            $request['name'] = ['en' => $request['name_en'], 'ar' => $request['name_ar']];
            $request['description'] = ['en' => $request['description_en'], 'ar' => $request['description_ar']];
            $request['photo'] = $photoPath;

//            $request->merge([
//                'name' => ['en' => $request['name_en'], 'ar' => $request['name_ar']],
//                'description' => ['en' => $request['description_en'], 'ar' => $request['description_ar']],
//                'photo' => $photoPath,
//            ]);

            Product::create($request);

            return to_route('admin.products.index')->with(['success' => 'New Product created successfully...']);

//            return returnSuccessJson('New Product created successfully...', 201);

        } catch (\Exception $exception) {
            return to_route('admin.products.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function edit($id)
    {
        try {
            $product = Product::find($id);
            if (!$product)
                return to_route('admin.products.index')->with(['error' => 'المنتج غير موجود ليتم التعديل عليه ']);

//            dd($vendor);
            $productCategories = ProductCategory::where('active', 1)->selection()->get();

            return view('admin.products.edit', ['product' => $product, 'productCategories' => $productCategories]);

        } catch (\Exception $exception) {
            return to_route('admin.products.index')->with(['error' => __('messages.something went wrong...!')]);
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return to_route('admin.products.index')->with(['error' => 'المنتج غير موجود ليتم التعديل عليه ']);

//            return returnErrorJson('Product not found...', 400);

            if ($request->photo) {
                dd($request->photo);
                Storage::disk('images')->delete($product->photo);

                $photoPath = saveImages('products', $request->photo);
            } else {
                $photoPath = $product->photo;
            }

            $name = ['en' => $request['name_en'], 'ar' => $request['name_ar']];
            if ($request->description_ar)
                $description = ['en' => $request['description_en'], 'ar' => $request['description_ar']];
            $request = $request->except('_method', 'name_ar', 'name_en');
            $request['photo'] = $photoPath;
            $request['name'] = $name;

            $updated = $product->update($request);

            $message = ($updated) ? 'The Product updated successfully...'
                : 'No modifications have been made...';

            return to_route('admin.products.index')->with(['success' => $message]);

//            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return to_route('admin.products.index')->with(['error' => __('messages.something went wrong...!')]);

//            return $exception;
//            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return to_route('admin.products.index')->with(['error' => 'المنتج غير موجود ليتم حذفه']);

//            return returnErrorJson('The Product not found...', 400);

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

            return to_route('admin.products.index')->with(['success' => 'The Product deleted successfully...']);

//            return returnSuccessJson('The Product deleted successfully...');

        } catch (\Exception $exception) {
            return to_route('admin.products.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $product = Product::find($id);

            if (!$product)
                return to_route('admin.products.index')->with(['error' => 'The Product not found...!']);

//            return returnErrorJson('The Product not found...!', 400);

            $status = $product->active == 'active' ? 'inactive' : 'active';

            $product->update(['active' => $status]);

            return to_route('admin.products.index')->with(['success' => 'The Product status changed successfully...']);

//            return returnSuccessJson('The Product status changed successfully...');

        } catch (\Exception $ex) {
            return to_route('admin.products.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }
}
