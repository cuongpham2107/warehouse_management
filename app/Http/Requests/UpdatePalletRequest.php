<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pallet_id' => 'sometimes|required|string|max:255',
            'crate_id' => 'sometimes|required|string|max:255',
            'location_code' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|max:50',
            'checked_in_at' => 'nullable|date',
            'checked_in_by' => 'nullable|string|max:255',
            'checked_out_at' => 'nullable|date',
            'checked_out_by' => 'nullable|string|max:255',
        ];
    }
}
