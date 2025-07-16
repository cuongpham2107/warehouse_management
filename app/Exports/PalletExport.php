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
        // Nạp các quan hệ cần thiết
        $this->records = $records->load(['crate', 'location', 'shipmentItems.shipment.vehicle']);
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Mã Pallet',
            'Mã Thùng Hàng',
            'Vị Trí',
            'Trạng Thái',
            'Thời Gian Nhập Kho',
            'Thời Gian Xuất Kho',
            'Mã Lô Vận Chuyển',
            'Biển Số Xe',
            'Tên Tài Xế',
            'Số ĐT Tài Xế',
        ];
    }

    public function map($pallet): array
    {
        // Lấy thông tin shipment đầu tiên nếu có
        $shipmentItem = $pallet->shipmentItems->first();
        $shipment = $shipmentItem?->shipment;
        $vehicle = $shipment?->vehicle;
        return [
            $pallet->pallet_id,
            $pallet->crate->crate_id ?? '',
            $pallet->location->location_code ?? '',
            $pallet->status instanceof \App\Enums\PalletStatus ? $pallet->status->getLabel() : $pallet->status,
            $pallet->checked_in_at,
            $pallet->checked_out_at,
            $shipment?->shipment_code ?? '',
            $vehicle?->license_plate ?? '',
            $vehicle?->driver_name ?? '',
            $vehicle?->driver_phone ?? '',
        ];
    }

    public function toResponse($request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download($this, $this->fileName);
    }
} 