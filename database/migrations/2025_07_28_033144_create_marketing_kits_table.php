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
        Schema::create('marketing_kits', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('gambar'); // path gambar yang diupload
            $table->text('konten'); // isi konten pakai CKEditor (text saja)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_kits');
    }
};
