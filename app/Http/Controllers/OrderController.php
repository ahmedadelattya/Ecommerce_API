<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function myOrders()
{
    $orders = Auth::user()->orders()->with('orderItems.product')->get();

    return OrderResource::collection($orders);
}
public function cancelOrder($orderId)
{
    $order = Auth::user()->orders()->where('status', 'pending')->findOrFail($orderId);


    // Loop through the order items and restore the product stock
    foreach ($order->orderItems as $item) {
        $product = $item->product;
        $product->increment('stock', $item->quantity);
    }

    // Mark the order as canceled
    $order->update(['status' => 'canceled']);

    return response()->json(['message' => 'Order canceled successfully.']);
}

    public function updateStatus(Request $request, $id)
    {
        // Validate the status input
        $request->validate([
            'status' => 'required|in:pending,processing,completed',
        ]);

        // Find the order
        $order = Order::findOrFail($id);

        // Update the order status
        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Order status updated successfully!',
            'order' => $order,
        ], 200);
    }
}
