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

            return returnDataJson('cart', $cart, __('messages.Shopping Cart'));

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
                return returnErrorJson(__('messages.Item cannot be added to cart'), 400);

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

            return returnSuccessJson(__('messages.Item added to cart'));

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
                return returnErrorJson(__('messages.Item cannot be updated...!'), 400);

            DB::beginTransaction();
            $cartItem->product()->increment('amount', $cartItem->quantity);

            if ($request->quantity > $cartItem->product->amount)
                return returnErrorJson(__('messages.Item cannot be updated...!'), 400);

            $updated = $cartItem->update([
                'quantity' => $request->quantity
            ]);

            $cartItem->product()->decrement('amount', $request->quantity);
            DB::commit();

            $message = ($updated) ? __('messages.The Item updated successfully...')
                : __('messages.No modifications have been made...');

            return returnSuccessJson($message);

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

            if (!$cart)
                return returnErrorJson(__('messages.Cart Item cannot deleted...!'), 400);

            $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

            if (!$cartItem)
                return returnErrorJson(__('messages.Cart Item cannot deleted...!'), 400);

            DB::beginTransaction();

            $cartItem->product()->increment('amount', $cartItem->quantity);

            $cartItem->delete();

            DB::commit();

            return returnSuccessJson(__('messages.Item removed from cart'));

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }
}
