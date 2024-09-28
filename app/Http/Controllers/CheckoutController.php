<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Handle the checkout process.
     */
    public function checkout(Request $request)
    {
        // Get the authenticated user's cart
        $cart = Auth::user()->cart;

        // Check if the cart is empty
        if ($cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty. Add items to proceed with checkout.',
            ], 400);
        }

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Create a new order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $cart->total,
                'item_count' => $cart->item_count,
                'status' => 'pending' // You can manage different order statuses like pending, completed, etc.
            ]);

            // Loop through the cart items and create corresponding order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->total,
                ]);
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->update(['total' => 0, 'item_count' => 0]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully!',
                'order' => new OrderResource($order),
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process the order. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
