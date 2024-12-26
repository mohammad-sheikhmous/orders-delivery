<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingCartRequest;
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
                'message' => __('messages.Shopping Cart'),
                'cart' => $cart
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function store(ShoppingCartRequest $request)
    {
        try {
            $cart = Cart::firstOrCreate(['user_id' => user()->id]);

            $product = Product::find($request->product_id);
            if ($request->quantity > $product->amount)
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => __('messages.Item cannot be added to cart'),
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
                'message' => __('messages.Item added to cart'),
                'cart_item' => $cartItem
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }

    public function update(ShoppingCartRequest $request)
    {
        try {
            $cart = Cart::where('user_id', user()->id)->first();

            //$product = Product::find($request->product_id);

            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!isset($cartItem))
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => __('messages.Item cannot be updated...!'),
                ], 400);

            DB::beginTransaction();
            $cartItem->product()->increment('amount', $cartItem->quantity);

            if ($request->quantity > $cartItem->product->amount)
                return response()->json([
                    'status' => false,
                    'code status' => 400,
                    'message' => __('messages.Item cannot be updated...!'),
                ], 400);

            $updated = $cartItem->update([
                'quantity' => $request->quantity
            ]);

            $cartItem->product()->decrement('amount', $request->quantity);
            DB::commit();

            $message = ($updated) ? __('messages.The Item updated successfully...')
                : __('messages.No modifications have been made...');

            return response()->json([
                'status' => true,
                'code status' => 200,
                'message' => $message,
                'cart_item' => $cartItem
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        try {
            $cart = Cart::where('user_id', user()->id)->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Cart Item cannot deleted...!')
                ], 400);
            }

            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.Cart Item cannot deleted...!')
                ], 400);
            }

            DB::beginTransaction();

            $cartItem->product()->increment('amount', $cartItem->quantity);

            $cartItem->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.Item removed from cart')
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }
}
