<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); // Using UUID to match your users table
            $table->unsignedBigInteger('product_variant_id');
            $table->timestamps(); // Adds created_at and updated_at columns
            
            // Foreign key constraints
            $table->foreign('user_id')
                  ->references('uuid')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('product_variant_id')
                  ->references('id')
                  ->on('product_variants')
                  ->onDelete('cascade');
            
            // Ensure each user can only have a product in their wishlist once
            $table->unique(['user_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('wishlists');
    }
};