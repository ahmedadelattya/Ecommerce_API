<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the authenticated user's cart.
     */
    public function show()
    {
        // Retrieve the authenticated user's cart along with cart items and their associated products
        $cart = Auth::user()->cart()->with('items.product')->first();

        if (!$cart) {
            return response()->json(['message' => 'No cart found.'], 404);
        }

        // Return cart resource
        return new CartResource($cart);
    }

    /**
     * Add a product to the cart or update its quantity if it already exists in the cart.
     */
    public function addProduct(Request $request)
    {
        // Validate request input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the authenticated user's cart
        $cart = Auth::user()->cart;

        // Get product details
        $product = Product::findOrFail($request->product_id);

        // Check if the product already exists in the cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // If product exists, update the quantity and total price
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
                'total' => ($cartItem->quantity + $request->quantity) * $cartItem->price
            ]);
        } else {
            // If product doesn't exist, create a new cart item
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total' => $product->price * $request->quantity
            ]);
        }

        // Update the cart total and item count
        $cart->update([
            'total' => $cart->items->sum('total'),
            'item_count' => $cart->items->sum('quantity')
        ]);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart' => new CartResource($cart)
        ], 200);
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateProduct(Request $request, $productId)
    {
        // Validate request input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the authenticated user's cart
        $cart = Auth::user()->cart;

        // Find the cart item by product ID
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart.'], 404);
        }

        // Update the cart item quantity and total price
        $cartItem->update([
            'quantity' => $request->quantity,
            'total' => $cartItem->price * $request->quantity
        ]);

        // Update the cart total and item count
        $cart->update([
            'total' => $cart->items->sum('total'),
            'item_count' => $cart->items->sum('quantity')
        ]);

        return response()->json([
            'message' => 'Product quantity updated successfully',
            'cart' => new CartResource($cart)
        ], 200);
    }

    /**
     * Remove a product from the cart.
     */
    public function removeProduct($productId)
    {
        // Get the authenticated user's cart
        $cart = Auth::user()->cart;

        // Find the cart item by product ID
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart.'], 404);
        }

        // Remove the cart item
        $cartItem->delete();

        // Update the cart total and item count
        $cart->update([
            'total' => $cart->items->sum('total'),
            'item_count' => $cart->items->sum('quantity')
        ]);

        return response()->json([
            'message' => 'Product removed from cart successfully',
            'cart' => new CartResource($cart)
        ], 200);
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart()
    {
        // Get the authenticated user's cart
        $cart = Auth::user()->cart;

        // Delete all cart items
        $cart->items()->delete();

        // Reset cart totals
        $cart->update([
            'total' => 0,
            'item_count' => 0
        ]);

        return response()->json([
            'message' => 'Cart cleared successfully',
            'cart' => new CartResource($cart)
        ], 200);
    }
}
