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
        Schema::create('images', function (Blueprint $table) {
            $table->id(); // Primary key

            // Core image file information
            $table->string('path'); // The path/URL to the main/original image file
            $table->string('filename')->nullable(); // Original filename (optional)
            $table->string('mime_type')->nullable(); // e.g., 'image/jpeg'
            $table->string('disk')->default('public');
            $table->unsignedBigInteger('size')->nullable(); // File size in bytes
            
            // Metadata and purpose
            $table->string('alt_text')->nullable(); // Alternative text for accessibility
            $table->string('purpose')->nullable()->index(); // e.g., 'thumbnail', 'preview', 'gallery', 'profile_picture'
                                                              // Indexed for faster lookup by purpose
            // Polymorphic relationship fields
            // This will add 'imageable_id' (unsignedBigInteger) and 'imageable_type' (string)
            $table->morphs('imageable'); // Links the image to its parent model (e.g., Product, User)
                                         // Automatically adds an index on both columns

            // Store different resolution/conversion paths and metadata as JSON
            $table->json('conversions')->nullable(); // Stores array/object of variant paths, sizes, etc.

            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // For soft deleting images, consistent with your Product model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
