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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade'); 
            $table->decimal('amount', 15, 2); 
            $table->string('type'); 
            $table->string('purpose'); 
            $table->nullableMorphs('sourceable');
            $table->text('description')->nullable(); 
            $table->string('status')->default('pending'); 
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};