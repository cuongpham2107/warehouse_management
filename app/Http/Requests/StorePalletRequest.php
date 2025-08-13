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
            /**
             * Pallet Code
             */
            'pallet_code' => 'required|string|max:255',
            /**
             * Crate Code
             */
            'crate_code' => 'required|string|max:255',
        ];
    }
}
