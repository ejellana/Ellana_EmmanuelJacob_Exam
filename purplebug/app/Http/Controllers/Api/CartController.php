<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'       => $item->product->id,
                    'name'     => $item->product->name,
                    'price'    => $item->product->price,
                    'image'    => $item->product->image,
                    'quantity' => $item->quantity,
                    'stocks'   => $item->product->stocks,
                ];
            });

        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $userId  = $request->user()->id;

        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Increase quantity
            $newQuantity = $cartItem->quantity + 1;

            if ($newQuantity > $product->stocks) {
                return response()->json(['message' => 'Not enough stock'], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Add new item
            if ($product->stocks < 1) {
                return response()->json(['message' => 'Out of stock'], 400);
            }

            CartItem::create([
                'user_id'    => $userId,
                'product_id' => $product->id,
                'quantity'   => 1
            ]);
        }

        return response()->json(['message' => 'Product added to cart']);
    }

    public function update(Request $request, $productId)
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $product = Product::findOrFail($productId);

        if ($request->quantity > $product->stocks) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Quantity updated']);
    }

    public function remove(Request $request, $productId)
    {
        CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Item removed']);
    }

    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cart cleared']);
    }
}
