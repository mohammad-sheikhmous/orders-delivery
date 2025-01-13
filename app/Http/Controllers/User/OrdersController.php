<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\FcmNotification1;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        try {
            $orders = Order::with('items.product')
                ->where('user_id', auth()->id())
                ->get();

            if ($orders->isEmpty())
                return returnErrorJson(__('messages.orders not found...!'), 400);

            return returnDataJson('orders', $orders, __('messages.All orders for the user...'),);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function create()
    {
        try {
            $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();

            if (!$cart || $cart->items->isEmpty()) {
                return returnErrorJson(__('messages.Order cannot be created...!'), 400);
            }

            $totalPrice = 0;

            foreach ($cart->items as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            DB::beginTransaction();
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => ['en' => 'pending', 'ar' => 'معلق'],
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

            $token = user()->fcmTokens()->latest('updated_at')->pluck('fcm_token')->first();

            if ($token)
                $this->firebaseService->sendNotification($token, __('messages.New Order...'), __('messages.you have created new order'));

            return returnSuccessJson(__('messages.Order created'), 201);

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
        ], $this->messages());

        try {
            $order = Order::where('id', $id)->where('user_id', user()->id)->first();

            if (!$order || $order->status !== __('messages.pending')) {
                return returnErrorJson(__('messages.Order cannot be modified'), 400);
            }

            DB::beginTransaction();
            foreach ($order->items as $item) {
                $item->product->increment('amount', $item->quantity);
            }

            $totalPrice = 0;
            $order->items()->delete();

            foreach ($request->items as $item) {
                //$orderItem = $order->items()->where('product_id', $item['product_id'])->first();

                $product = Product::where('id', $item['product_id'])->first();

                if ($product->amount < $item['quantity'])
                    return returnErrorJson(__('messages.Not enough amount for product:') . ' ' . $product->name, 400);

                $product->decrement('amount', $item['quantity']);

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $totalPrice += $product->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();

            $token = user()->fcmTokens()->latest('updated_at')->pluck('fcm_token')->first();

            if (!$token)
                $this->firebaseService->sendNotification($token, __('messages.Order Updated...'), __('messages.Your Order have been updated'));

            return returnSuccessJson(__('messages.Order modified'));

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::with('items')->where('id', $id)->where('user_id', auth()->id())->first();

            if (!$order || $order->status !== __('messages.pending'))
                return returnErrorJson(__('messages.Order cannot be canceled'), 400);

            foreach ($order->items as $item) {
                $item->product->increment('amount', $item->quantity);
            }

            $order->delete();

            return returnSuccessJson(__('messages.Order canceled successfully...'));

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

