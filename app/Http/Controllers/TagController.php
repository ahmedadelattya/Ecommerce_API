<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Create the new tag
            $tag = Tag::create($validatedData);

            // Commit the transaction
            DB::commit();

            // Return the created tag as a resource
            return response()->json([
                'message' => 'Tag created successfully',
                'tag' => new TagResource($tag)
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Failed to create the tag: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the tag by its ID
            $tag = Tag::findOrFail($id);

            // Return the tag as a resource
            return new TagResource($tag);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'tag not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the tag by its ID
        $tag = Tag::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ]);

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Update the tag with the validated data
            $tag->update($validatedData);

            // Commit the transaction
            DB::commit();

            // Return the updated tag as a resource
            return response()->json([
                'message' => 'Tag updated successfully',
                'tag' => new TagResource($tag)
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Failed to update the tag: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Begin a transaction
        DB::beginTransaction();

        try {
            // Find the tag by its ID
            $tag = Tag::findOrFail($id);

            // Check if the tag is attached to any products
            if ($tag->products()->exists()) {
                return response()->json([
                    'message' => 'Tag cannot be deleted because it is associated with products.'
                ], 400);
            }

            // Delete the tag
            $tag->delete();

            // Commit the transaction
            DB::commit();

            // Return a success response
            return response()->json([
                'message' => 'Tag deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            // Return an error response
            return response()->json([
                'message' => 'Failed to delete the tag: ' . $e->getMessage(),
            ], 500);
        }
    }
}
