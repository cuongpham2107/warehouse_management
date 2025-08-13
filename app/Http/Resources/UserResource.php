<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * ID người dùng
             */
            'id' => $this->id,
            /**
             * Tên người dùng
             * @example Nguyễn Văn A
             */
            'name' => $this->name,
            /**
             * Mã nhân viên
             */
            'asgl_id' => $this->asgl_id,
            /**
             * Ngày tạo
             * @example 2023-03-15 10:00
             */
            'created_at' => $this->created_at,
        ];
    }
}
