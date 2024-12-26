<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainCategoriesController extends Controller
{
    public function index()
    {
        try {
            $categories = MainCategory::selection()->get();

            if ($categories->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Categories not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all categories returned..',
                'categories' => $categories,
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create(MainCategoryRequest $request)
    {
        try {
            $photoPath = saveImages('categories', $request->photo);

            MainCategory::create([
                'name' => [
                    'en' => $request->name_en,
                    'ar' => $request->name_ar,
                ],
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
            ]);

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'New MainCategory created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(MainCategoryRequest $request, $id)
    {
        try {
            $category = MainCategory::find($id);

            if (!$category)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'MainCategory not found...'
                ]);

            if ($request->photo) {
                if (isset($category->photo))
                    Storage::disk('images')->delete($category->photo);

                $photoPath = saveImages('categories', $request->photo);
            } else {
                $photoPath = $category->photo;
            }

            $updated = $category->update([
                'name' => [
                    'en' => $request->name_en,
                    'ar' => $request->name_ar,
                ],
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
            ]);

            $message = ($updated) ? 'The MainCategory updated successfully...'
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
            $category = MainCategory::find($id);
            if (!$category)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'MainCategory not found...',
                ]);

            $vendors = $category->vendors();
            if (isset($vendors) && $vendors->count() > 0)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The MainCategory cannot be deleted...',
                ], 400);

            $photoPath = $category->photo;
            Storage::disk('images')->delete($photoPath);
            $category->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The MainCategory deleted successfully...',
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $mainCategory = MainCategory::find($id);

            if (!$mainCategory)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'The Main Category not found...!',
                ]);

            $status = $mainCategory->active == 'active' ? 'inactive' : 'active';

            $mainCategory->update(['active' => $status]);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => "The Main Category status changed successfully...",
            ]);

        } catch (\Exception $ex) {
            return returnExceptionJson();
        }
    }
}
