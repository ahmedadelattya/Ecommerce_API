<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get query parameters for filtering, sorting, and pagination
        $categorySlug = $request->query('category');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $perPage = $request->query('per_page', 10);

        // Fetch products with optional filtering
        $products = Product::query()
            ->when($categorySlug, function ($query) use ($categorySlug) {
                return $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->when($minPrice, function ($query) use ($minPrice) {
                return $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($query) use ($maxPrice) {
                return $query->where('price', '<=', $maxPrice);
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        // Return paginated and filtered products
        return ProductResource::collection($products)
            ->additional([
                'total' => $products->total(),
                'skip' => $products->currentPage() * $products->perPage() - $products->perPage(),
                'limit' => $products->perPage(),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // Begin a transaction
        DB::beginTransaction();

        try {

            // The validated data is automatically available from the request
            $validatedData = $request->validated();
            $category = Category::where('name', $validatedData['category_name'])->first();
            $validatedData['category_id'] = $category->id;

            // Create the product
            $product = Product::create($validatedData);

            // Attach tags to the product
            if (!empty($validatedData['tags'])) {
                $tagIds = Tag::whereIn('name', $validatedData['tags'])->pluck('id');
                $product->tags()->attach($tagIds);
            }

            // Handle the uploaded thumbnail image
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('product-thumbnails', 'products_images');
                $product->images()->create(['url' => $thumbnailPath, 'is_thumbnail' => 1]);
            }



            // Handle images if provided
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product-images', 'products_images');
                    $product->images()->create(['url' => $imagePath, 'is_thumbnail' => 0]);
                }
            }

            // Commit the transaction if everything is okay
            DB::commit();

            // Return a success response
            return response()->json(['message' => 'Product created successfully', 'product' => new ProductResource($product)], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create the product.',
                'error'=>$e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the product by its ID
            $product = Product::findOrFail($id);

            // Return the product wrapped in a resource
            return response()->json(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            // Return a custom error message if the product is not found
            return response()->json([
                'message' => 'Product not found.',
                'error'=>$e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        // Begin a transaction
        DB::beginTransaction();
    
        try {
            // Find the product
            $product = Product::findOrFail($id);
    
            // Update product details
            $validatedData = $request->validated();
            
            $category = Category::where('name', $validatedData['category_name'])->first();
            $validatedData['category_id'] = $category->id;
    
            // Update the product
            $product->update($validatedData);
    
            // Update tags if provided
            if (!empty($validatedData['tags'])) {
                $tagIds = Tag::whereIn('name', $validatedData['tags'])->pluck('id');
                $product->tags()->sync($tagIds);
            }
    
           // Handle the uploaded thumbnail image if provided
        if ($request->hasFile('thumbnail')) {
            // Delete the old thumbnail file from storage
            $oldThumbnail = $product->images()->where('is_thumbnail', 1)->first();
            if ($oldThumbnail) {
                FacadesStorage::disk('products_images')->delete($oldThumbnail->url);
                $oldThumbnail->delete(); // Delete the thumbnail record from DB
            }

            // Upload new thumbnail and save it
            $thumbnailPath = $request->file('thumbnail')->store('product-thumbnails', 'products_images');
            $product->images()->create(['url' => $thumbnailPath, 'is_thumbnail' => 1]);
        }

        // Handle image deletion if specified
        if ($request->has('delete_images')) {
            $imagesToDelete = $request->input('delete_images'); // Array of image IDs to delete
            foreach ($imagesToDelete as $imageId) {
                $image = $product->images()->where('id', $imageId)->first();
                if ($image) {
                    FacadesStorage::disk('products_images')->delete($image->url); // Delete from storage
                    $image->delete(); // Delete from DB
                }
            }
        }

        // Handle new images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product-images', 'products_images');
                $product->images()->create(['url' => $imagePath, 'is_thumbnail' => 0]);
            }
        }
    
            // Commit the transaction
            DB::commit();
    
            // Return a success response
            return response()->json(['message' => 'Product updated successfully', 'product' => new ProductResource($product)], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if there is any error
            DB::rollBack();
    
            // Return an error response
            return response()->json([
                'message' => 'Failed to update the product.',
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
        // Find the product
        $product = Product::findOrFail($id);

        // Detach all tags associated with the product
        $product->tags()->detach();

        // Delete all associated images
        foreach ($product->images as $image) {
            // Delete the image from the storage
            if (FacadesStorage::disk('products_images')->exists($image->url)) {
                FacadesStorage::disk('products_images')->delete($image->url);
            }
            // Delete the image record from the database
            $image->delete();
        }

        // Delete the product
        $product->delete();

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json(['message' => 'Product deleted successfully.'], 200);
    } catch (ModelNotFoundException $e) {
        // Rollback the transaction if the product is not found
        DB::rollBack();

        return response()->json(['message' => 'Product not found.'], 404);
    } catch (\Exception $e) {
        // Rollback the transaction if there's any other error
        DB::rollBack();

        return response()->json(['message' => 'Failed to delete the product.'], 500);
    }
    }
}
