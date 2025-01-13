<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
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
                return returnErrorJson('Product Categories not found...!', 400);

            return returnDataJson('categories', $productCategories, 'all product categories returned..');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create(ProductCategoryRequest $request)
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

            return returnSuccessJson('New Product Category created successfully...', 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(ProductCategoryRequest $request, $id)
    {
        try {
            $productCategory = ProductCategory::find($id);

            if (!$productCategory)
                return returnErrorJson('Product Category not found...', 400);

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

            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $productCategory = ProductCategory::find($id);
            if (!$productCategory)
                return returnErrorJson('Product Category not found...', 400);

            $product = $productCategory->products();
            if (isset($product) && $product->count() > 0)
                return returnErrorJson('The Product Category cannot be deleted...', 400);

            $photoPath = $productCategory->photo;
            Storage::disk('images')->delete($photoPath);
            $productCategory->delete();

            return returnSuccessJson('The Product Category deleted successfully...');

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $productCategory = ProductCategory::find($id);

            if (!$productCategory)
                return returnErrorJson('The Product Category not found...!', 400);

            $status = $productCategory->active == 'active' ? 'inactive' : 'active';

            $productCategory->update(['active' => $status]);

            return returnSuccessJson('The Product Category status changed successfully...');

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
