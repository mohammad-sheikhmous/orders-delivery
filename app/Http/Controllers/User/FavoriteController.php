<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function show()
    {
        try {
            $favorites = DB::table('favorites')->where('user_id', user()->id)
                ->join('products', 'favorites.product_id', '=', 'products.id')
                ->select('favorites.id', 'product_id', 'name', 'photo', 'description')->get();

            if ($favorites->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Favorites are empty...!'
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'All items in favorites..',
                'favorites' => $favorites
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $user = user();

            // Check if already in favorites
            if ($user->favorites()->where('product_id', $request->product_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => 'Product is already in favorites'
                ], 400);
            }

            $user->favorites()->attach($request->product_id);

            return response()->json([
                'status' => true,
                'code status' => 200,
                'message' => 'Product added to favorites'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $user = user();
            $user->favorites()->detach($request->product_id);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Product removed from favorites'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ]);
        }
    }
}
