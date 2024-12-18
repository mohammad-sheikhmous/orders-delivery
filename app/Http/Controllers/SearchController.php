<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchByVendor(Request $request)
    {
        if (!isset($request->searchedThing))
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'Nothing to search for...!'
            ]);

        $searchResult = Vendor::where('category_id', $request->category_id)
            ->where('name', 'like', '%' . $request->searchedThing . '%')
            ->get();

        if (!$searchResult)
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'No results for "' . $request->searchedThing . '". Try a new Search '
            ]);

        return response()->json([
            'status' => true,
            'status code' => 200,
            'message' => 'Search result...',
            'result' => $searchResult,
        ]);
    }

    public function searchByProduct(Request $request)
    {
        if (!isset($request->searchedThing))
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'Nothing to search for...!'
            ], 204);

        $searchResult = Product::where('product_category_id', $request->product_category_id)
            ->where('name', 'like', '%' . $request->searchedThing . '%')
            ->get();

        if (!$searchResult)
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'No results for "' . $request->searchedThing . '". Try a new Search '
            ], 204);

        return response()->json([
            'status' => true,
            'status code' => 200,
            'message' => 'Search result...',
            'result' => $searchResult,
        ]);
    }

    public function searchInGeneral(Request $request)
    {
        if (!isset($request->searchedThing))
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'Nothing to search for...!'
            ]);

        $searchResult1 = Vendor::where('name', 'like', '%' . $request->searchedThing . '%')
            ->get();
        $searchResult2 = Product::where('name', 'like', '%' . $request->searchedThing . '%')
            ->get();
        $searchResult = collect($searchResult1)->merge($searchResult2);

        if (!$searchResult)
            return response()->json([
                'status' => false,
                'status code' => 204,
                'message' => 'No results for "' . $request->searchedThing . '". Try a new Search '
            ]);

        return response()->json([
            'status' => true,
            'status code' => 200,
            'message' => 'Search result...',
            'result' => $searchResult,
        ]);
    }
}
