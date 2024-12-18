<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::with('items.product')
                ->where('user_id', auth()->id())
                ->get();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'All orders for the user...',
                'orders' => $orders
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function create()
    {
        try {
            $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => 'Order cannot be created...!'
                ], 400);
            }

            $totalPrice = 0;

            foreach ($cart->items as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            DB::beginTransaction();
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Clear the cart
            $cart->items()->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 201,
                'message' => 'Order created',
                'order' => $order
            ], 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$order || $order->status !== 'pending') {
                return response()->json([
                    'message' => 'Order cannot be modified'
                ], 400);
            }

            DB::beginTransaction();
            foreach ($order->items as $item) {
                $item->product->increment('amount', $item->quantity);
            }

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $orderItem = $order->items()->where('product_id', $item['product_id'])->first();

                $product = $orderItem->product;

                if ($product->amount < $item['quantity']) {
                    return response()->json([
                        'message' => "Not enough amount for product: {$product->name}",
                    ], 400);
                }

                $product->decrement('amount', $item['quantity']);

                $orderItem->update([
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $totalPrice += $product->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Order modified',
//                'order' => $order
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$order || $order->status !== 'pending') {
                return response()->json([
                    'message' => 'Order cannot be canceled'
                ], 400);
            }

            foreach ($order->items as $item) {
                $item->product->increment('amount', $item->quantity);
            }

            $order->items()->delete();
            $order->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Order canceled successfully...'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
