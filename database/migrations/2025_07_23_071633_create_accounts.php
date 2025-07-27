<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key

            // Foreign key to the users table
            $table->foreignId('user_id')
                  ->constrained('users') // Assumes a 'users' table exists with an 'id' column
                  ->onDelete('cascade'); // If the user is deleted, their accounts are also soft-deleted (due to the trait on the model)

            // Unique identifier for the account, potentially for external reference
            $table->string('account_number', 50)->unique()->nullable();

            // Type of account (e.g., 'checking', 'savings', 'wallet', 'credit_card')
            $table->string('account_type', 50)->index();

            // ISO 4217 currency code (e.g., 'USD', 'IDR', 'EUR')
            $table->string('currency', 3)->default('IDR'); 

            // The current balance of the account, using decimal for precision
            $table->decimal('balance', 15, 2)->default(0.00);

            // Version for optimistic locking - increments on every update
            // $table->unsignedBigInteger('version')->default(0); 

            // Status of the account (e.g., 'active', 'inactive', 'suspended', 'closed')
            $table->string('status', 20)->default('active')->index();

            // Standard Laravel timestamps (created_at, updated_at)
            $table->timestamps(); 

            // Soft delete column - Laravel will manage this automatically
            $table->softDeletes(); 

            // Adding indexes for frequently queried columns to improve performance
            $table->index(['user_id', 'currency']);
            // $table->index(['account_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};