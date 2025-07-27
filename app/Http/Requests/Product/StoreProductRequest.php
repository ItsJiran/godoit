<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Product\ProductStatus;
use App\Enums\Product\ProductType;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule; // Import Rule for conditional validation

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => ['nullable', new Enum(ProductStatus::class)],
            'published_at' => 'nullable|date',
            'productable_id' => ['nullable', 'numeric'],
            'productable_type' => ['required', 'string', new Enum(ProductType::class)],
            'productable_mode' => ['required', 'string', Rule::in(['new', 'existing'])], // Validasi mode
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Dapatkan tipe productable dan mode dari input yang sudah divalidasi oleh rules()
            $productableTypeInput = $this->input('productable_type'); // e.g., 'video'
            $productableMode = $this->input('productable_mode'); // e.g., 'existing' or 'new'

            // Variabel untuk menyimpan FQCN model dan objek productable yang ditemukan
            $productableModelClass = null; // App\Models\Video::class
            $productableInstance = null; // Objek App\Models\Video atau null
            $finalProductableId = null; // ID dari productable yang sudah ada atau null

            // 1. Konversi productable_type dari enum value ke FQCN model
            if ($productableTypeInput && ProductType::tryFrom($productableTypeInput)) {
                $productableTypeEnum = ProductType::from($productableTypeInput);
                $productableModelClass = $productableTypeEnum->model();
            } else {
                // Tambahkan error jika tipe produk tidak valid (seharusnya sudah di rules(), ini sebagai pengaman)
                $validator->errors()->add('productable_type', 'Tipe produk tidak valid.');
                return; // Hentikan validasi lebih lanjut
            }

            // 2. Logika validasi dan pencarian untuk mode 'existing' (sesuai permintaan Anda)
            if ($productableMode == 'existing') {
                $productableIdFromRequest = $this->input('productable_id');

                // Pastikan class model ada sebelum mencoba mencari
                if (class_exists($productableModelClass)) {
                    // Cari model berdasarkan ID dari request (perbaikan sintaks find())
                    $foundProductable = $productableModelClass::find($productableIdFromRequest);

                    if (is_null($foundProductable)) {
                        // Tambahkan error jika produk yang dituju tidak ditemukan
                        $validator->errors()->add($this->input('productable_id'), 'Produk (' . $productableTypeEnum->label() . ') dengan ID tersebut tidak ditemukan.');
                    } else {
                        // Jika ditemukan, simpan objek dan ID-nya
                        $productableInstance = $foundProductable;
                        $finalProductableId = $foundProductable->id;
                    }
                } else {
                    // Error jika mapping model tidak ditemukan (kasus jarang jika enum dan model konsisten)
                    $validator->errors()->add('productable_type', 'Model terkait untuk tipe produk ini tidak ditemukan.');
                }
            }
            // Untuk mode 'new', $productableInstance dan $finalProductableId akan tetap null di sini,
            // dan akan dibuat di controller.

            // 3. Gabungkan data yang sudah ditentukan ke dalam request
            // Ini akan memastikan validated() method mengembalikan nilai yang siap digunakan
            $this->merge([
                'productable_id' => $finalProductableId, // Akan null jika mode 'new' atau tidak ditemukan, atau ID yang valid
                'productable_type' => $productableModelClass, // Selalu FQCN dari model
                'productable' => $productableInstance, 
            ]);
        });
    }
}