<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ProductRequest - validácia produktu (serverová strana)
 */
class ProductRequest extends FormRequest
{
    /**
     * Určuje, či je používateľ autorizovaný
     */
    public function authorize(): bool
    {
        return true; // V produkčnom prostredí by sa overovalo či je admin
    }

    /**
     * Validačné pravidlá
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:200',
            'base_price' => 'required|numeric|min:0.01|max:99999.99',
            'brand' => 'nullable|string|max:200',
            'sku_model' => 'nullable|string|max:84',
            'gender' => 'nullable|in:men,women,unisex',
            'description' => 'nullable|string|max:5000',
            'image_urls' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Vlastné chybové správy (v slovenčine)
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Názov produktu je povinný',
            'name.min' => 'Názov musí mať aspoň 2 znaky',
            'name.max' => 'Názov je príliš dlhý (max. 200 znakov)',
            'base_price.required' => 'Cena je povinná',
            'base_price.numeric' => 'Cena musí byť číslo',
            'base_price.min' => 'Cena musí byť kladná',
            'base_price.max' => 'Cena je príliš vysoká',
            'brand.max' => 'Značka je príliš dlhá (max. 200 znakov)',
            'sku_model.max' => 'SKU je príliš dlhé (max. 84 znakov)',
            'gender.in' => 'Neplatná hodnota pohlavia',
            'description.max' => 'Popis je príliš dlhý',
            'images.*.image' => 'Súbor musí byť obrázok',
            'images.*.mimes' => 'Povolené formáty: jpeg, png, jpg, gif, webp',
            'images.*.max' => 'Obrázok je príliš veľký (max. 5MB)',
        ];
    }

    /**
     * Príprava dát pred validáciou
     */
    protected function prepareForValidation(): void
    {
        // Konvertuje cenu z reťazca na číslo
        if ($this->has('base_price')) {
            $this->merge([
                'base_price' => floatval(str_replace(',', '.', $this->base_price))
            ]);
        }
    }
}

