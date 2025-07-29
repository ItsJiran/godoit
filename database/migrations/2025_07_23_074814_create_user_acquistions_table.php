<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MembershipStatus; // Assuming you've created this Enum

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_acquisitions', function (Blueprint $table) {
            $table->id();

            // Link to the user who holds this membership
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // If user is deleted, their acsuqoionst are also removed

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade'); // If product is deleted, their acquistions are also removed

            // Link to the 'membership' product (from your products table)
            // define what cause this acquisitions are created
            $table->nullableMorphs('sourceable');
            $table->text('sourceable_description')->nullable();

            // Audit field: Tracks which admin user manually granted this membership (if applicable)
            $table->foreignId('granted_by_user_id')
                  ->nullable() // Null if granted automatically (e.g., via purchase)
                  ->constrained('users')
                  ->onDelete('set null'); // If the granting admin account is deleted, just set this to null

            // Audit field: An optional reason or note for the manual grant
            $table->text('grant_reason')->nullable();

            // Status of the membership (active, expired, revoked, pending)
            // You should define this in an Enum like App\Enums\MembershipStatus
            $table->enum('status', [
                'active',
                'expired',
                'revoked',
                'pending'
            ])->default('pending')->index();

            $table->timestamp('start_date'); // When the premium access began
            $table->timestamp('end_date')->nullable(); // When the premium access ends (null if permanent/lifetime)

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at for soft deletion

            // Unique constraint to prevent a user from having multiple active memberships
            // for the EXACT SAME product at the same time.
            // Adjust if a user can hold multiple active memberships of the same 'product_id'.
            // For general 'premium' this usually implies one active.

            // Indexes for common queries
            $table->index(['user_id', 'status', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};