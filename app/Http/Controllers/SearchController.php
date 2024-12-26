<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchByVendor(Request $request)
    {
        //the validation has to be out the try and catch handling
        $request->validate([
            'main_category_id' => ['required', 'exists:main_categories,id']
        ]);
        try {
            if (!isset($request->search_text))
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Nothing to search for...!')
                ], 400);

            if (user()->tokenCan('admin'))
                $searchResult = Vendor::where('main_category_id', $request->main_category_id)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();
            else
                $searchResult = Vendor::where('main_category_id', $request->main_category_id)
                    ->where('active', 1)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();

            if ($searchResult->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.No results for ').$request->search_text.__('messages., Try a new Search')
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.Search result...'),
                'result' => $searchResult,
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function searchByProduct(Request $request)
    {
        $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id']
        ]);

        try {
            if (!isset($request->search_text))
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Nothing to search for...!')
                ], 400);

            if (user()->tokenCan('admin'))
                $searchResult = Product::where('product_category_id', $request->product_category_id)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();
            else
                $searchResult = Product::where('product_category_id', $request->product_category_id)
                    ->where('active', 1)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();

            if ($searchResult->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.No results for ').$request->search_text.__('messages., Try a new Search')
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.Search result...'),
                'result' => $searchResult,
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function searchInGeneral(Request $request)
    {
        try {
            if (!isset($request->search_text))
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Nothing to search for...!')
                ], 400);

            if (user()->tokenCan('admin'))
                $searchResult1 = Vendor::where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();
            else
                $searchResult1 = Vendor::where('active', 1)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();

            if (user()->tokenCan('admin'))
                $searchResult2 = Product::where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();
            else
                $searchResult2 = Product::where('active', 1)
                    ->where('name', 'like', '%' . $request->search_text . '%')
                    ->selectionForIndexing()->get();

            $searchResult = collect($searchResult1)->merge($searchResult2);

            if ($searchResult->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.No results for ').$request->search_text.__('messages., Try a new Search')
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.Search result...'),
                'result' => $searchResult,
            ]);

        } catch (\Exception $exception) {
            return $exception;
            return returnExceptionJson();
        }
    }
}
