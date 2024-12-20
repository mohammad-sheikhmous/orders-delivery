<?php

namespace App\Http\Controllers\User;

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
            $mainCategories = MainCategory::where('active', 1)->selection()->get();

            if ($mainCategories->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'main categories not found...!',
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'all categories returned..',
                'categories' => $mainCategories,
            ]);

        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400));
        }
    }
}
