<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
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
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::with(['category', 'tags'])->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
