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
                return returnErrorJson(__('messages.main categories not found...!'), 400);

            return returnDataJson('mainCategories', $mainCategories, __('messages.all categories returned..'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
