<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_code' => 'required|string|max:255|unique:warehouse_locations',
        ];
    }

    public function messages(): array
    {
        return [
            'location_code.required' => 'Mã vị trí là bắt buộc.',
            'location_code.unique' => 'Mã vị trí này đã tồn tại.',
            'location_code.max' => 'Mã vị trí không được vượt quá 255 ký tự.',
        ];
    }
}
