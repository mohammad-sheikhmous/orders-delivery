<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductCategoriesController extends Controller
{
    public function index()
    {
        try {
            $productCategories = ProductCategory::selection()
                ->with(['vendor' => function ($q) {
                    $q->select('id', 'name');
                }])->get();

            if ($productCategories->isEmpty())
                return to_route('admin.dashboard')->with(['error' => 'Product Categories not found...!']);

//                return returnErrorJson('Product Categories not found...!', 400);

            return view('admin.productCategories.index', ['productCategories' => $productCategories]);

//            return returnDataJson('categories', $productCategories, 'all product categories returned..');

        } catch (\Exception $exception) {
            return to_route('admin.dashboard')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function create()
    {
        $vendors = Vendor::selectionForIndexing()->get();

        return view('admin.productCategories.create', ['vendors' => $vendors]);
    }

    public function store(ProductCategoryRequest $request)
    {
        try {
            $photoPath = saveImages('product_categories', $request->photo);

            ProductCategory::create([
                'name' => [
                    'en' => $request->name_en,
                    'ar' => $request->name_ar,
                ],
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
                'vendor_id' => $request->vendor_id
            ]);

            return to_route('admin.product_categories.index')->with(['success' => 'New Product Category created successfully...']);

//            return returnSuccessJson('New Product Category created successfully...', 201);

        } catch (\Exception $exception) {
            return to_route('admin.dashboard')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function edit($id)
    {
        try {
            $productCategory = ProductCategory::find($id);
            if (!$productCategory)
                return to_route('admin.product_categories.index')->with(['error' => 'القسم غير موجود ليتم التعديل عليه ']);

//            dd($vendor);
            $vendors = Vendor::where('active', 1)->selectionForIndexing()->get();

            return view('admin.productCategories.edit', ['productCategory' => $productCategory, 'vendors' => $vendors]);

        } catch (\Exception $exception) {
            return to_route('admin.product_categories.index')->with(['error' => __('messages.something went wrong...!')]);
        }
    }


    public function update(ProductCategoryRequest $request, $id)
    {
        try {
            $productCategory = ProductCategory::find($id);

            if (!$productCategory)
                return to_route('admin.productCategories.index')->with(['error' => 'Product Category not found...']);

//                return returnErrorJson('Product Category not found...', 400);

            if ($request->photo) {
                if (isset($productCategory->photo))
                    Storage::disk('images')->delete($productCategory->photo);

                $photoPath = saveImages('product_categories', $request->photo);
            } else {
                $photoPath = $productCategory->photo;
            }

            $updated = $productCategory->update([
                'name' => [
                    'en' => $request->name_en,
                    'ar' => $request->name_ar,
                ],
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
                'vendor_id' => $request->vendor_id
            ]);

            $message = ($updated) ? 'The MainCategory updated successfully...'
                : 'No modifications have been made...';

            return to_route('admin.product_categories.index')->with(['success' => $message]);

//            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return to_route('admin.productCategories.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $productCategory = ProductCategory::find($id);
            if (!$productCategory)
                return to_route('admin.productCategories.index')->with(['error' => 'Product Category not found...']);

//            return returnErrorJson('Product Category not found...', 400);

            $product = $productCategory->products();
            if (isset($product) && $product->count() > 0)
                return to_route('admin.product_categories.index')->with(['error' => 'The Product Category cannot be deleted...']);

//                return returnErrorJson('The Product Category cannot be deleted...', 400);

            $photoPath = $productCategory->photo;
            Storage::disk('images')->delete($photoPath);
            $productCategory->delete();

            return to_route('admin.product_categories.index')->with(['success' => 'The Product Category deleted successfully...']);

//            return returnSuccessJson('The Product Category deleted successfully...');

        } catch (\Exception $exception) {
            return to_route('admin.product_categories.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $productCategory = ProductCategory::find($id);

            if (!$productCategory)
                return to_route('admin.product_categories.index')->with(['error' => 'The Product Category not found...!']);

//            return returnErrorJson('The Product Category not found...!', 400);

            $status = $productCategory->active == 'active' ? 'inactive' : 'active';

            $productCategory->update(['active' => $status]);

            return to_route('admin.product_categories.index')->with(['success' => 'The Product Category status changed successfully...']);

//            return returnSuccessJson('The Product Category status changed successfully...');

        } catch (\Exception $ex) {
            return to_route('admin.product_categories.index')->with(['error' => __('messages.something went wrong...!')]);

//            return returnExceptionJson();
        }
    }
}
