<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('stock');
            $table->string('brand')->nullable();
            $table->string('sku')->unique();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('dimension_width', 8, 2)->nullable();
            $table->decimal('dimension_height', 8, 2)->nullable();
            $table->decimal('dimension_depth', 8, 2)->nullable();
            $table->string('warranty_information')->nullable();
            $table->string('shipping_information')->nullable();
            $table->string('availability_status')->nullable();
            $table->string('return_policy')->nullable();
            $table->integer('minimum_order_quantity')->default(1);
            $table->json('tags')->nullable();
            $table->json('meta')->nullable();
            $table->json('images')->nullable();
            $table->string('thumbnail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
