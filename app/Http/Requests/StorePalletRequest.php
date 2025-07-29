<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pallet_id' => 'required|string|max:255',
            'crate_id' => 'required|string|max:255',
        ];
    }
}
