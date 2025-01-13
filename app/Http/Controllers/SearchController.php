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
                return returnErrorJson(__('messages.Nothing to search for...!'), 400);

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
                return returnErrorJson(__('messages.No results for ') . $request->search_text . __('messages., Try a new Search'), 400);

            return returnDataJson('result', $searchResult, __('messages.Search result...'));

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
                return returnErrorJson(__('messages.Nothing to search for...!'), 400);

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
                return returnErrorJson(__('messages.No results for ') . $request->search_text . __('messages., Try a new Search'), 400);

            return returnDataJson('result', $searchResult, __('messages.Search result...'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function searchInGeneral(Request $request)
    {
        try {
            if (!isset($request->search_text))
                return returnErrorJson(__('messages.Nothing to search for...!'), 400);

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

            $searchResult = [
                'vendors' => $searchResult1,
                'products' => $searchResult2
            ];

            if (empty($searchResult))
                return returnErrorJson(__('messages.No results for ') . $request->search_text . __('messages., Try a new Search'), 400);

            return returnDataJson('result', $searchResult, __('messages.Search result...'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
