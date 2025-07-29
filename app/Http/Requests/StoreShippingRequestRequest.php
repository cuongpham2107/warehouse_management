<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_code' => ['required', 'string', 'max:255', 'unique:shipping_requests'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_contact' => ['required', 'string', 'max:255'],
            'delivery_address' => ['required', 'string'],
            'requested_date' => ['required', 'date'],
            'departure_time' => ['required', 'date'],
            'license_plate' => ['required', 'string', 'max:255'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['required', 'string', 'max:255'],
            'seal_number' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'request_code.required' => 'Mã yêu cầu vận chuyển là bắt buộc',
            'request_code.unique' => 'Mã yêu cầu vận chuyển đã tồn tại',
            'customer_name.required' => 'Tên khách hàng là bắt buộc',
            'customer_contact.required' => 'Thông tin liên hệ khách hàng là bắt buộc',
            'delivery_address.required' => 'Địa chỉ giao hàng là bắt buộc',
            'requested_date.required' => 'Ngày yêu cầu là bắt buộc',
            'requested_date.date' => 'Ngày yêu cầu không hợp lệ',
            'departure_time.required' => 'Thời gian khởi hành là bắt buộc',
            'departure_time.date' => 'Thời gian khởi hành không hợp lệ',
            'license_plate.required' => 'Biển số xe là bắt buộc',
            'driver_name.required' => 'Tên tài xế là bắt buộc',
            'driver_phone.required' => 'Số điện thoại tài xế là bắt buộc',
            'priority.required' => 'Mức độ ưu tiên là bắt buộc',
            'priority.min' => 'Mức độ ưu tiên phải từ 1 đến 5',
            'priority.max' => 'Mức độ ưu tiên phải từ 1 đến 5',
            'items.required' => 'Danh sách sản phẩm là bắt buộc',
            'items.min' => 'Phải có ít nhất một sản phẩm',
            'items.*.product_id.required' => 'ID sản phẩm là bắt buộc',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại',
            'items.*.quantity.required' => 'Số lượng là bắt buộc',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
        ];
    }
}
