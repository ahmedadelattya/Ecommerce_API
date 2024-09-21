<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by tag if provided
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('id', $request->input('tag_id'));
            });
        }

        // Get all products with pagination
        $products = $query->paginate(10);

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'tags'])->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
