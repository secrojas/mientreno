<?php

namespace App\Http\Requests;

class UpdateShoeRequest extends StoreShoeRequest
{
    public function authorize(): bool
    {
        return $this->route('shoe')->user_id === $this->user()->id;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_photo' => ['sometimes', 'boolean'],
        ]);
    }
}
