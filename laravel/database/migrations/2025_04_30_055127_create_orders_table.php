<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->unsignedInteger('shipping_address_id');
            $table->decimal('total_amount', 10, 2); // for money values
            $table->enum('status', ['pending', 'paid', 'unpaid'])->default('pending');
            $table->timestamps();

             // Add foreign key constraint
             $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
             $table->foreign('shipping_address_id')->references('id')->on('shipments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
