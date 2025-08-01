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
        Schema::create('landing_section', function (Blueprint $table) {
            $table->id();
            $table->string('landing_type')->default('homepage');
            $table->unsignedInteger('index');
            $table->string('type');
            $table->jsonb('meta_content');            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_section');
    }
};
