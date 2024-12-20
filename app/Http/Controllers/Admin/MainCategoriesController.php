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
            if (user()->tokenCan('admin'))
                $categories = MainCategory::selection()->all();
            else
                $categories = MainCategory::where('active', 1)->selection()->all();

            return response()->json([
                'message' => 'all categories returned..',
                'categories' => $categories,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function store(MainCategoryRequest $request)
    {
        try {
            $photo = $request->photo;

            $photoPath = saveImages('categories', $photo);

            MainCategory::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
            ]);

            return response()->json([
                'message' => 'New MainCategory created successfully...'
            ], 201);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(MainCategoryRequest $request)
    {
        try {
            $category_id = $request->id;
            $category = MainCategory::find($category_id);
            if (!$category)
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'MainCategory not found...'
                ]);

            if($request->photo){
                $photoPath = saveImages('categories', $request->photo);
            }else {
                $photoPath = $category->photo;
            }

            $category->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'photo' => $photoPath,
                'active' => $request->active,
            ]);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The MainCategory updated successfully...'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy()
    {
        try {
            $category = MainCategory::find(request()->id);
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
                'message' => 'The MainCategory deleted successfully...',
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
