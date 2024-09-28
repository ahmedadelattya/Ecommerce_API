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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');     // Links to the carts table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Links to the products table
            $table->integer('quantity')->default(1);                            // Quantity of the product in the cart
            $table->decimal('price', 10, 2);                                   // Price of the product
            $table->decimal('total', 10, 2)->default(0);                      // Total price for the quantity of the product
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
