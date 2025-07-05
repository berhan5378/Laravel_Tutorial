<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); 
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending');
            $table->string('country');
            $table->string('zip_code');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                  ->references('uuid')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
