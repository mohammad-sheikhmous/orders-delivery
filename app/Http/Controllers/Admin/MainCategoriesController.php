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
                return to_route('admin.main_categories.index')->with(['error' =>'Categories not found...!']);
//                return returnErrorJson('Categories not found...!', 400);

            return view('admin.mainCategories.index', ['main_categories' => $categories]);
            return returnDataJson('categories', $categories, 'all categories returned..');

        } catch (\Exception $exception) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);

            return returnExceptionJson();
        }
    }


    public function create()
    {
        return view('admin.mainCategories.create');
    }

    public function store(MainCategoryRequest $request)
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
            return to_route('admin.main_categories.index')->with(['success' => 'New MainCategory created successfully...']);
            return returnSuccessJson('New MainCategory created successfully...', 201);

        } catch (\Exception $exception) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);

            return returnExceptionJson();
        }
    }


    public function edit($id)
    {
        try {
            $mainCategory = MainCategory::selection()->find($id);

            if (!$mainCategory) {
                return to_route('admin.main_categories.index')->with(['error' => 'لم يتم العثور على هذا القسم لتعديله...!']);
            }

            return view('admin.mainCategories.edit', ['mainCategory' => $mainCategory]);

        } catch (\Exception $exception) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);
        }

    }

    public function update(MainCategoryRequest $request, $id)
    {
        try {
            $category = MainCategory::find($id);

            if (!$category)
                return returnErrorJson('MainCategory not found...', 400);

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

            return to_route('admin.main_categories.index')->with(['success' => $message]);
            return returnSuccessJson($message);

        } catch (\Exception $exception) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);

            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $category = MainCategory::find($id);
            if (!$category)
                return returnErrorJson('MainCategory not found...', 400);

            $vendors = $category->vendors();
            if (isset($vendors) && $vendors->count() > 0)
                return returnErrorJson('The MainCategory cannot be deleted...', 400);

            $photoPath = $category->photo;
            Storage::disk('images')->delete($photoPath);
            $category->delete();

            return to_route('admin.main_categories.index')->with(['success' => 'لقد تم حذف الملف بنجاح...']);
            return returnSuccessJson('The MainCategory deleted successfully...', 400);

        } catch (\Exception $exception) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);

            return returnExceptionJson();
        }
    }

    public function changeStatus($id)
    {
        try {
            $mainCategory = MainCategory::find($id);

            if (!$mainCategory)
                return returnErrorJson('The Main Category not found...!', 400);

            $status = $mainCategory->active == 'active' ? 'inactive' : 'active';

            $mainCategory->update(['active' => $status]);

            return returnSuccessJson('The Main Category status changed successfully...');

        } catch (\Exception $ex) {
            return to_route('admin.main_categories.index')->with(['error' => __('messages.something went wrong...!')]);
            return returnExceptionJson();
        }
    }
}
