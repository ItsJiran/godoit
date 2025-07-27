<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response; // Import Response for more detailed messages

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can perform any action (e.g., for super-admins).
     * This method runs BEFORE any other policy method.
     */
    public function before(User $user, string $ability)
    {
        // Assuming your User model has a 'role' attribute or a method like 'isSuperAdmin()'
        if ($user->role === 'superadmin') {
            return true; // Superadmin can do anything
        }
        // If you have a package like Spatie/Laravel-Permission, you might use:
        // if ($user->hasRole('superadmin')) { return true; }
    }

    /**
     * Determine whether the user can view any products.
     * Kita izinkan semua user yang login untuk melihat daftar produk.
     * Filtering "published only" atau "all" akan dilakukan di controller.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true; // Semua user yang login boleh melihat daftar produk
    }

    /**
     * Determine whether the user can view the model.
     * Anda bisa menambahkan logika di sini jika ada produk yang hanya bisa dilihat oleh pemiliknya, dll.
     * Untuk skenario ini, kita akan fokus pada filtering di index method controller.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Product $product): Response|bool
    {
        // Contoh: Izinkan melihat jika published, atau jika user adalah pemilik, atau jika admin/superadmin
        if ($product->status === \App\Enums\ProductStatus::Published->value) {
            return true;
        }

        if ($user->id === $product->user_id) {
            return true;
        }

        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only allow 'superadmin' or 'admin' to create products
        // Assuming your User model has a 'role' attribute
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }

        return Response::deny('You do not have permission to create products.');
        // Or simply: return false;
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Product $product): Response|bool
    {
        // Only allow 'superadmin' or 'admin' to update products
        if (in_array($user->role, ['superadmin', 'admin'])) {
            // Optional: Add more granular checks, e.g., if the user owns the product
            // if ($user->id === $product->user_id) {
            //     return true;
            // }
            return true;
        }

        return Response::deny('You do not have permission to update this product.');
        // Or simply: return false;
    }

    // Add other policy methods (viewAny, view, delete, restore, forceDelete) as needed
}