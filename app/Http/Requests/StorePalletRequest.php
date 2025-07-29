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
            'location_code' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'checked_in_at' => 'nullable|date',
            'checked_in_by' => 'nullable|string|max:255',
            'checked_out_at' => 'nullable|date',
            'checked_out_by' => 'nullable|string|max:255',
        ];
    }
}
