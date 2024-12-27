<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmService;
use App\Notifications\FcmNotification1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function show()
    {
        try {
            $favorites = DB::table('favorites')->where('user_id', user()->id)
                ->join('products', 'favorites.product_id', '=', 'products.id')
                ->select('favorites.id', 'product_id', 'name->'.user()->app_lang.' as name',
                    'photo', 'description->'.user()->app_lang.' as description')->get();

            if ($favorites->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Favorites are empty...!')
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.All items in favorites..'),
                'favorites' => $favorites
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function store(Request $request)
    {
        $messages = [
            // Product ID
            'product_id.required' => __('The product ID is required.'),
            'product_id.exists' => __('The selected product ID does not exist.'),
        ];

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ], $messages);

        try {
            $user = user();

            // Check if already in favorites
            if ($user->favorites()->where('product_id', $request->product_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Product is already in favorites')
                ], 400);
            }

            DB::beginTransaction();
            $user->favorites()->attach($request->product_id);

//            $user->notify(new FcmNotification1([
//                'title' => 'New Item in the favorites',
//                'body' => 'new Item added in the favorites..',
//                'data' => ['product_id' => $request->product_id]
//            ]));
//            $fcmService = new FcmService();
//            $fcmService->FCM(
//                user()->fcmTokens, [
//                'title' => 'Favorites',
//                'message' => __('messages.Product added to favorites....'),
//            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.Product added to favorites')
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
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
                'message' => __('messages.Product removed from favorites')
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
