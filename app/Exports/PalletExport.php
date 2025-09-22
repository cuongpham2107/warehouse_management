<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PalletExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    public $fileName = 'pallets_export.xlsx';
    protected $records;

    public function __construct(Collection $records)
    {
        // Nạp các quan hệ cần thiết theo cấu trúc thực tế
        $this->records = $records->load([
            'crate.receivingPlan', 
            'shippingRequestItem.shippingRequest',
            'checkedInBy',
            'checkedOutBy'
        ]);
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Mã Pallet',
            'Mã Kiện Hàng',
            'Kế Hoạch Nhập Kho',
            'Vị Trí',
            'Trạng Thái',
            'Thời Gian Nhập Kho',
            'Người Nhập Kho',
            'Thời Gian Xuất Kho',
            'Người Xuất Kho',
            'Mã Yêu Cầu Xuất Kho',
            'Biển Số Xe',
            'Tên Tài Xế',
            'Số ĐT Tài Xế',
            'Số Niêm Phong',
        ];
    }

    public function map($pallet): array
    {
        // Lấy thông tin shipping request nếu có
        $shippingRequest = $pallet->shippingRequestItem?->shippingRequest;
        
        return [
            $pallet->pallet_id,
            $pallet->crate->crate_id ?? '',
            $pallet->crate->receivingPlan->plan_code ?? '',
            $pallet->location_code ?? '',
            $pallet->status instanceof \App\Enums\PalletStatus ? $pallet->status->getLabel() : $pallet->status,
            $pallet->checked_in_at ? $pallet->checked_in_at->format('H:i d/m/Y') : '',
            $pallet->checkedInBy->name ?? '',
            $pallet->checked_out_at ? $pallet->checked_out_at->format('H:i d/m/Y') : '',
            $pallet->checkedOutBy->name ?? '',
            $shippingRequest?->request_code ?? '',
            $shippingRequest?->license_plate ?? '',
            $shippingRequest?->driver_name ?? '',
            $shippingRequest?->driver_phone ?? '',
            $shippingRequest?->seal_number ?? '',
        ];
    }

    public function toResponse($request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download($this, $this->fileName);
    }
} 