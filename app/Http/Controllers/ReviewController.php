<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
    * Store a new review for a product.
    */
   public function store(Request $request, Product $product)
   {
       // Validate review data
       $request->validate([
           'rating' => 'required|integer|min:1|max:5',
           'comment' => 'nullable|string',
       ]);

       // Create the review for the product
       $review = $product->reviews()->create([
           'user_id' => $request->user()->id,
           'rating' => $request->rating,
           'comment' => $request->comment,
       ]);

       return response()->json([
           'message' => 'Review added successfully!',
           'review' => $review,
       ], 201);
   }

     /**
     * Remove a review.
     */
    public function destroy($id)
    {
        // Find the review
        $review = Review::findOrFail($id);

        // Delete the review
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully!',
        ], 200);
    }
}
