<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

use App\Models\Product;
use App\Models\ProductRegular;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductStatus;

use App\Models\Image;
use App\Enums\Image\ImagePurposeType;
use App\Services\Media\ImageUploadService; // Ensure this is correct

use Carbon\Carbon;

class ProductController extends Controller
{


    public function viewProduct(Request $request, $id)
    {
        $user = $request->user();
        $product = Product::where('id',$id)->with(['thumbnail','productable'])->first();

        // Pass products and the current user's ID to the view
        return view('product.index', [
            'product' => $product,
            'currentUserId' => $user ? $user->id : null, // Pass user ID for comparison in view
        ]);
    }


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
        // $this->authorize('viewAny', Product::class);

        // Get the authenticated user
        $user = $request->user();

        $query = $request->input('search');
        if ($query) {
            $products = Product::where('title', 'like', '%' . $query . '%')
                                ->where('creator_id',$request->user()->id)
                                ->with('thumbnail')
                                ->paginate(10);
        } else {
            $products = Product::latest()
                            ->where('creator_id',$request->user()->id)
                            ->with('thumbnail')
                            ->paginate(2);
        }

        // Pass products and the current user's ID to the view
        return view('dashboard.products.index', compact('products', 'query'));
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

    // temporary

    public function saveProduct( Request $request )
    {
        try {

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'timestamp' => 'required|date_format:Y-m-d\TH:i', // Changed from datetime to date_format            
                'gambar' => ['nullable', 'image', 'max:5120'], // Max 5MB
            ]);

            $regular_product = ProductRegular::create([
                'timestamp' => $request->timestamp
            ]);
            
            $sequence_number = Product::determineNextSequenceNumber($regular_product::class);
            $slug = Product::determineNextSequenceSlug($regular_product::class,$sequence_number);

            $product = Product::create([
                'title' => $request->title,
                'creator_id' => $request->user()->id,
                'description' => $request->description,
                'productable_id' => $regular_product->id,
                'productable_type' => $regular_product::class,
                'status' => ProductStatus::PUBLISHED,
                'price' => $request->price,
                'slug' => $slug,
                'sequence_number' => $sequence_number,
            ]);

            if ($request->hasFile('gambar')) {                

                try {                    
                    $newThumbnail = Image::createImageRecord(
                        $request->file('gambar'),
                        $product,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value,
                        $product->title . ' product thumbnail',
                        'public',
                        null,
                        ImagePurposeType::PRODUCT_THUMBNAIL->value
                    );                    
                } catch (\Exception $e) {                                        
                    \Log::error('Failed to upload product thumbnail: ' . $e->getMessage());                    
                    return redirect()->route('admin.product')->with('error', 'Gagal mengunggah foto produk baru. Silakan coba lagi.');
                }
            }

            return redirect()->route('admin.product')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['productable_type' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat membuat produk: ' . $e->getMessage()]);
        }
    }

    public function editProduct($id)
    {
        $product = Product::with(['thumbnail','productable'])->findOrFail($id);
        return view('dashboard.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'gambar' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'description' => 'required|string',
            'timestamp' => 'required|date_format:Y-m-d\TH:i', // Changed from datetime to date_format         
            'price' => 'required|numeric',
        ]);

        if ($request->hasFile('gambar')) {            
            $product->loadMissing('thumbnail');
            
            $oldAvatar = $product->thumbnail;

            try {                
                $newAvatar = Image::createImageRecord(
                    $request->file('gambar'),
                    $product,
                    ImagePurposeType::PRODUCT_THUMBNAIL->value,
                    $product->title . ' product thumbnail',
                    'public',
                    null,
                    ImagePurposeType::PRODUCT_THUMBNAIL->value
                );
                
                if ($oldAvatar) {
                    ImageUploadService::deleteImage($oldAvatar); 
                    $oldAvatar->forceDelete();
                }
            } catch (\Exception $e) {                                
                \Log::error('Failed to upload product thumbnail: ' . $e->getMessage());
                return redirect()->route('admin.product')->with('error', 'Gagal mengunggah foto produk baru. Silakan coba lagi.');
            }
        }

        $product->loadMissing('productable');
        $product->productable->timestamp = $request->timestamp;
        $product->productable->save();

        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->save();

        return redirect()->route('admin.product')->with('success', 'Product berhasil diperbarui.');
    }

    public function deleteProduct($id)
    {
        $kit = Product::findOrFail($id);
        $kit->delete();
        return redirect()->route('admin.product')->with('success', 'Product berhasil dihapus.');
    }

}