<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

use App\Models\Product;
use App\Enums\Product\ProductType;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Authorize the 'viewAny' action for the Product model.
        // This will check the 'viewAny' method in ProductPolicy.
        $this->authorize('viewAny', Product::class);

        // Get the authenticated user
        $user = $request->user();

        // Query products based on user role
        if ($user && in_array($user->role, ['superadmin', 'admin'])) {
            $products = Product::withTrashed()->latest()->get(); // Use latest() for ordering
        } else {
            $products = Product::published()->latest()->get();
        }

        // Pass products and the current user's ID to the view
        return view('products.index', [
            'products' => $products,
            'currentUserId' => $user ? $user->id : null, // Pass user ID for comparison in view
        ]);
    }

    /**
     * Display the form for creating a new product.
     * Only accessible by 'superadmin' and 'admin' roles.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function createForm()
    {
        // Authorize the 'create' action for the Product model.
        // This will check the 'create' method in ProductPolicy.
        $this->authorize('create', Product::class);

        // Get all cases from the ProductType enum to populate a dropdown
        // This will return an array like: ['MEMBERSHIP' => 'Membership / Premium Access']
        $productTypes = ProductType::toSelectArray();

        // Pass the product types to the view
        return view('products.create', compact('productTypes'));
    }


    /**
     * Store a newly created product in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request The incoming validated HTTP request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        try {
            // Dapatkan data yang sudah divalidasi dari Form Request
            $validatedData = $request->all();

            $product = Product::storeRecord(
                $validatedData, // Teruskan data yang sudah divalidasi
                $request->user(), // Teruskan objek User
                $validatedData['productable'] 
            );

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['productable_type' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat membuat produk: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the form for editing an existing product.
     * Only accessible by 'superadmin' and 'admin' roles.
     *
     * @param  \App\Models\Product  $product  The product instance resolved via route model binding.
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function editForm(Product $product)
    {
        // Authorize the 'update' action for the specific product instance.
        // This will check the 'update' method in ProductPolicy.
        $this->authorize('update', $product);

        // Get all cases from the ProductType enum (useful if you want to display or re-select type)
        $productTypes = collect(ProductType::cases())->mapWithKeys(function ($type) {
            return [$type->value => $type->label()];
        })->toArray();

        // Pass the existing product data and product types to the view
        return view('products.edit', compact('product', 'productTypes'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request The incoming validated HTTP request.
     * @param  \App\Models\Product  $product  The product instance resolved via route model binding.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            // Dapatkan data yang sudah divalidasi dari Form Request
            $validatedData = $request->all();

            // --- Logika untuk mendapatkan atau mengupdate instance productable (jika diperlukan) ---
            $productable = null; // Default null jika tidak ada update productable
            // Contoh: Jika Anda ingin mengupdate Membership terkait
            // if ($product->productable_type === ProductType::MEMBERSHIP && $product->productable) {
            //     $membership = $product->productable; // Dapatkan instance Membership terkait
            //     $membership->update([
            //         'name' => $validatedData['title'] . ' Membership Updated',
            //         // ... update data membership lainnya
            //     ]);
            //     $productable = $membership; // Teruskan objek Membership yang sudah diupdate
            // }
            // --- Akhir logika productable ---

            $updatedProduct = Product::updateRecord(
                $validatedData, // Teruskan data yang sudah divalidasi
                $product, // Teruskan objek Product yang akan diupdate
                $productable // Teruskan objek productable yang sudah diupdate (jika ada)
            );

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['error' => 'Produk tidak ditemukan.']);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['productable_type' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage()]);
        }
    }
}