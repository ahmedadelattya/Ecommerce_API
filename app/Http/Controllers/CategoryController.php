<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')
            ->paginate(10);
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
          // Validate the request data
          $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Create the new category
            $category = Category::create($validatedData);
            // Generate the slug from the category name
            $category->slug = Str::slug($category->name);
            $category->save();

            // Commit the transaction
            DB::commit();

            // Return the created category as a resource
            return response()->json([
                'message' => 'Category created successfully',
                'category' => new CategoryResource($category)
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Failed to create the category: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        
        $category = Category::with('products')->where('slug', $slug)->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Return the category resource
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        // Begin a transaction
        DB::beginTransaction();

        try {
            // Find the category by its slug
            $category = Category::where('slug', $slug)->firstOrFail();

            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            ]);

            // Update the category with the validated data
            $category->name = $validatedData['name'];

            // Automatically generate a new slug from the new name
            $category->slug = Str::slug($validatedData['name']); // or any slug generation method you use

            // Save the updated category
            $category->save();

            // Commit the transaction
            DB::commit();

            // Return the updated category as a resource
            return response()->json([
                'message' => 'Category updated successfully',
                'category' => new CategoryResource($category)
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response       
            return response()->json([
                'message' => 'Failed to update the category: ',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        // Begin a transaction
        DB::beginTransaction();

        try {
            // Find the category by its slug
            $category = Category::where('slug', $slug)->firstOrFail();


            // handle the case where the category has associated products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'Category cannot be deleted because it has associated products.',
                ], 400);
            }

            // Delete the category
            $category->delete();

            // Commit the transaction
            DB::commit();

            // Return a success response
            return response()->json([
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Failed to delete the category.',
            ], 500);
        }
    }
}
