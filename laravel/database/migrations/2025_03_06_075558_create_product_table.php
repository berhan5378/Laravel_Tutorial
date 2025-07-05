<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->string('category');
            $table->string('badge')->nullable();
            $table->string('type')->nullable();
            $table->float('rating')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('shipping_price', 10, 2)->default(0.00); 
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
