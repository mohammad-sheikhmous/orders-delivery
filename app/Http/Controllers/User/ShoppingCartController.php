<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{
    public function show()
    {
        try {
            $cart = Cart::with('items.product')->firstOrCreate(['user_id' => user()->id]);
//                ->where('user_id', user()->id)
//                ->first();

            return response()->json([
                'status' => true,
                'code status' => 200,
                'message' => 'Shopping Cart',
                'cart' => $cart
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'code status' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $cart = Cart::firstOrCreate(['user_id' => user()->id]);

            $product = Product::find($request->product_id);
            if ($request->quantity > $product->amount)
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => 'Item cannot be added to cart',
                ]);

            DB::beginTransaction();
            $product->amount = $product->amount - $request->quantity;
            $product->save();

            $itemQuantity = 0;

            $item = $cart->items()->where('product_id', $request->product_id)->first();
            if (isset($item))
                $itemQuantity = $item->quantity;

            $cartItem = $cart->items()->updateOrCreate(
                ['product_id' => $request->product_id],
                ['quantity' => $request->quantity + $itemQuantity]
            );
            DB::commit();
//            if a cart item exists with a product_id of "$request->product_id",
//            and a cart_id of "$cart->id" its quantity columns will be updated .
//            if no such cart item exists, a new cart item will be created which has the attributes resulting
//            from merging the first argument array with the second argument array

            return response()->json([
                'status' => true,
                'code status' => 200,
                'message' => 'Item added to cart',
                'cart_item' => $cartItem
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'code status' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            $cart = Cart::where('user_id', user()->id)->first();

            //$product = Product::find($request->product_id);

            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!isset($cartItem))
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => 'Item cannot be updated...!',
                ], 400);

            DB::beginTransaction();
            $cartItem->product()->increment('amount', $cartItem->quantity);

            if ($request->quantity > $cartItem->product()->amount)
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => 'Item cannot be updated...!',
                ], 400);

            $cartItem->update([
                'quantity' => $request->quantity
            ]);

            $cartItem->product()->decrement('amount', $request->quantity);
            DB::commit();

            return response()->json([
                'status' => true,
                'code status' => 200,
                'message' => 'The item updated...',
                'cart_item' => $cartItem
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code status' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            $cart = Cart::where('user_id', user()->id)->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Cart Item cannot deleted...!'
                ], 400);
            }

            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Cart Item cannot deleted...!'
                ], 400);
            }

            DB::beginTransaction();

            $cartItem->product()->increment('amount', $cartItem->quantity);

            $cartItem->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Item removed from cart'
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'code status' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
