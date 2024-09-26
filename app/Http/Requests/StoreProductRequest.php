<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_name' => 'required|exists:categories,name',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:1',
            'brand' => 'nullable|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'weight' => 'nullable|numeric',
            'dimension_width' => 'nullable|numeric',
            'dimension_height' => 'nullable|numeric',
            'dimension_depth' => 'nullable|numeric',
            'warranty_information' => 'nullable|string',
            'shipping_information' => 'nullable|string',
            'availability_status' => 'required|in:in-stock,out-stock',
            'return_policy' => 'nullable|string',
            'minimum_order_quantity' => 'nullable|integer|min:1',
            'barcode' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,name',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|max:2048',
            'thumbnail' => 'required|image|max:2048',
        ];
    }
}
