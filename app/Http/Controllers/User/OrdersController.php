<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\FcmService;
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

            if ($orders->isEmpty())
                return response()->json([
                    'status' => false,
                    'status code' => 400,
                    'message' => __('messages.orders not found...!'),
                ], 400);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => __('messages.All orders for the user...'),
                'orders' => $orders
            ]);
        } catch (\Exception $exception) {
            return returnExceptionJson();
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
                    'message' => __('messages.Order cannot be created...!')
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

            $fcmService = new FcmService();
            $fcmService->FCM(
                user()->fcmTokens,[
                'title'=>'New Order',
                'message'=>__('messages.new order created...'),
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
                'message' => __('messages.Order created'),
                'order' => $order
            ], 201);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ],$this->messages());

        try {
            $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$order || $order->status !== 'pending') {
                return response()->json([
                    'message' => __('messages.Order cannot be modified'),
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
                        'message' => __('messages.Not enough amount for product:').' '.$product->name,
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
                'message' => __('messages.Order modified'),
                'order' => $order->fresh()
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$order || $order->status !== 'pending') {
                return response()->json([
                    'message' => __('messages.Order cannot be canceled')
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
                'message' => __('messages.Order canceled successfully...')
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function messages(): array
    {
        return [
            // Items array
            'items.required' => __('validation/orders.The items field is required.'),
            'items.array' => __('validation/orders.The items must be an array.'),

            // Product ID
            'items.*.product_id.required' => __('validation/orders.Each item must have a product ID.'),
            'items.*.product_id.exists' => __('validation/orders.The selected product ID does not exist.'),

            // Quantity
            'items.*.quantity.required' => __('validation/orders.Each item must have a quantity.'),
            'items.*.quantity.integer' => __('validation/orders.The quantity for each item must be an integer.'),
            'items.*.quantity.min' => __('validation/orders.The quantity for each item must be at least 1.'),
        ];
    }

}

