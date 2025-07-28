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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->morphs('productable');
            $table->unsignedBigInteger('sequence_number')->nullable();

            $table->string('title');
            $table->string('slug')->unique(); 
            $table->text('description')->nullable();

            $table->decimal('price', 15, 2); 
            $table->string('currency', 3)->default('IDR');

            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable(); // When the product was made public

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at for soft deletion
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};