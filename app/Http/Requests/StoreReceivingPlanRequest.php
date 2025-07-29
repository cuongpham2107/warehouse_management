<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivingPlanRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'plan_code' => ['required', 'string', 'unique:receiving_plans,plan_code'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'license_plate' => ['required', 'string', 'max:20'],
            'plan_date' => ['required', 'date'],
            'total_crates' => ['required', 'integer', 'min:1'],
            'total_pcs' => ['nullable', 'integer', 'min:0'],
            'total_weight' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan_code.required' => 'Mã kế hoạch nhận hàng không được để trống',
            'plan_code.unique' => 'Mã kế hoạch nhận hàng đã tồn tại',
            'vendor_id.required' => 'Nhà cung cấp không được để trống',
            'vendor_id.exists' => 'Nhà cung cấp không tồn tại',
            'license_plate.required' => 'Biển số xe không được để trống',
            'license_plate.max' => 'Biển số xe không được vượt quá :max ký tự',
            'plan_date.required' => 'Ngày nhận hàng không được để trống',
            'plan_date.date' => 'Ngày nhận hàng không hợp lệ',
            'total_crates.required' => 'Tổng số thùng không được để trống',
            'total_crates.min' => 'Tổng số thùng phải lớn hơn 0',
            'total_pcs.min' => 'Tổng số lượng không được âm',
            'total_weight.min' => 'Tổng trọng lượng không được âm',
        ];
    }
}
