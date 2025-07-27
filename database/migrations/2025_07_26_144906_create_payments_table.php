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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            // $table->foreignId('id_customer');
            // $table->foreignId('id_order');
            $table->string('id_customer', 120)->nullable();
            $table->string('id_order', 120);
            $table->string('snap_token', 255)->nullable();
            $table->json('transaction_details');
            $table->json('customer_details');
            $table->json('product_details');
            $table->string('status', 255)->default('pending');
            $table->string('payment_type', 120)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
