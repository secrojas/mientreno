<?php

namespace App\Http\Requests;

use App\Enums\ShoeUsage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreShoeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:60'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'usage' => ['nullable', new Enum(ShoeUsage::class)],
            'purchased_at' => ['nullable', 'date', 'before_or_equal:today'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'initial_km' => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'max_km' => ['required', 'integer', 'min:100', 'max:3000'],
            'is_default' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand.required' => 'Indicá la marca.',
            'model.required' => 'Indicá el modelo.',
            'color.regex' => 'Elegí un color válido.',
            'purchased_at.before_or_equal' => 'La fecha de compra no puede ser futura.',
            'max_km.required' => 'Indicá la vida útil estimada en km.',
            'max_km.min' => 'La vida útil debe ser de al menos 100 km.',
            'max_km.max' => 'La vida útil no puede superar los 3000 km.',
            'photo.image' => 'La foto debe ser una imagen.',
            'photo.max' => 'La foto no puede superar 5MB.',
        ];
    }
}
