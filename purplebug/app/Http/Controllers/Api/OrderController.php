<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Get user's orders
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // Checkout (Create Order)
    public function checkout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $totalAmount = 0;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id'      => $user->id,
                'total_amount' => 0,
                'status'       => 'Pending'
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                if ($product->stocks < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json(['message' => 'Not enough stock for ' . $product->name], 400);
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $product->price
                ]);

                $totalAmount += $product->price * $cartItem->quantity;
                $product->decrement('stocks', $cartItem->quantity);
            }

            $order->update(['total_amount' => $totalAmount]);

            // Log Activity
            activity()
                ->causedBy($user)
                ->withProperties([
                    'order_id'     => $order->id,
                    'total_amount' => $totalAmount,
                    'items_count'  => $cartItems->count()
                ])
                ->log('placed new order');

            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order'   => $order->load('items.product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed'], 500);
        }
    }

    // Admin: Get all orders
    public function adminIndex()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // Admin: Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,For Delivery,Delivered,Canceled'
        ]);

        $oldStatus = $order->status;

        DB::beginTransaction();

        try {
            if ($request->status === 'Canceled' && $oldStatus !== 'Canceled') {
                // Restore stock if changing to Canceled
                foreach ($order->items as $item) {
                    $item->product->increment('stocks', $item->quantity);
                }
            }

            $order->update(['status' => $request->status]);

            activity()
                ->causedBy($request->user())
                ->withProperties(['old_status' => $oldStatus, 'new_status' => $request->status])
                ->log('updated order status');

            DB::commit();

            return response()->json([
                'message' => 'Order status updated successfully',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update status'], 500);
        }
    }

    // Admin: Delete order
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }

    // User: Cancel order
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'Pending') {
            return response()->json(['message' => 'Only pending orders can be cancelled'], 400);
        }

        DB::beginTransaction();

        try {
            // Restore stock for each item
            foreach ($order->items as $item) {
                $item->product->increment('stocks', $item->quantity);
            }

            $order->update(['status' => 'Canceled']);

            activity()
                ->causedBy($request->user())
                ->log('cancelled order #' . $order->id);

            DB::commit();

            return response()->json(['message' => 'Order cancelled successfully. Stock has been restored.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to cancel order'], 500);
        }
    }
}
