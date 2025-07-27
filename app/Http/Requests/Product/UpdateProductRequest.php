<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ProductStatus; // Import ProductStatus enum
use App\Enums\Product\ProductType; // Import ProductType enum
use Illuminate\Validation\Rules\Enum; // Import Enum rule
use Illuminate\Validation\Rule; // Import Rule for unique validation

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization for updating a product is handled by ProductPolicy's 'update' method.
     * This method in the FormRequest will simply defer to the policy.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Defer authorization to the ProductPolicy
        // This will call the `update` method in App\Policies\ProductPolicy
        // The 'product' parameter is automatically resolved via route model binding
        return $this->user()->can('update', $this->route('product'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the product being updated via route model binding
        $product = $this->route('product');

        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'status' => ['nullable', new Enum(ProductStatus::class)], // Validate against ProductStatus enum
            'published_at' => 'nullable|date',
        ];
    }

    /**
     * Prepare the data for validation.
     * This is useful if you need to modify or add data before validation runs.
     */
    protected function prepareForValidation(): void
    {
        // Ensure productable_type is a fully qualified class name (FQCN) if needed
        $productableType = $this->input('productable_type');
        if ($productableType && ProductType::tryFrom($productableType)) {
            $this->merge([
                'productable_type' => ProductType::from($productableType)->model(),
            ]);
        }
    }
}