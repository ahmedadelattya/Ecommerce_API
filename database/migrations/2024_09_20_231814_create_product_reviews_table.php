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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Reference to the product
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user who wrote the review
            $table->tinyInteger('rating'); // Rating between 1 and 5
            $table->text('comment')->nullable(); // Review comment
            $table->timestamp('reviewed_at')->useCurrent(); // Date of the review
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
