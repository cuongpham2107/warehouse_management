<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pallet_id' => 'required|exists:pallets,id',
            'movement_type' => 'required|string|in:transfer,relocate',
            'from_location_code' => 'required|string|exists:warehouse_locations,location_code',
            'to_location_code' => 'required|string|exists:warehouse_locations,location_code',
            'movement_date' => 'required|date',
            'notes' => 'nullable|string',
            'device_type' => 'required|string|in:scanner,manual',
        ];
    }

    public function messages(): array
    {
        return [
            'pallet_id.required' => 'ID pallet là bắt buộc',
            'pallet_id.exists' => 'Pallet không tồn tại trong hệ thống',
            'movement_type.required' => 'Loại di chuyển là bắt buộc',
            'movement_type.in' => 'Loại di chuyển phải là "Chuyển kho" hoặc "Di chuyển vị trí"',
            'from_location_code.required' => 'Vị trí nguồn là bắt buộc',
            'from_location_code.exists' => 'Vị trí nguồn không tồn tại',
            'to_location_code.required' => 'Vị trí đích là bắt buộc',
            'to_location_code.exists' => 'Vị trí đích không tồn tại',
            'movement_date.required' => 'Ngày di chuyển là bắt buộc',
            'movement_date.date' => 'Ngày di chuyển không hợp lệ',
            'device_type.required' => 'Loại thiết bị là bắt buộc',
            'device_type.in' => 'Loại thiết bị phải là "Máy quét" hoặc "Thủ công"',
        ];
    }
}
