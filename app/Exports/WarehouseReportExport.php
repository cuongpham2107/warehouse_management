<?php

namespace App\Exports;

use App\Models\PalletWithInfo;
use App\Enums\PalletStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class WarehouseReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function collection()
    {
        if ($this->data instanceof Collection) {
            return $this->data;
        }
        
        if ($this->data instanceof Builder) {
            return $this->data->get();
        }
        
        return PalletWithInfo::all();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã kế hoạch nhận hàng',
            'Nhà cung cấp',
            'Mã kiện hàng',
            'Tên hàng',
            'Số kiện (PCS)',
            'Trọng lượng (KG)',
            'Kích thước (D x R x C)',
            'Ngày hàng đến',
            'Giờ hạ hàng',
            'Biển số xe nhập',
            'Nhà xe vận chuyển nhập',
            'Tải trọng xe nhập (tấn)',
            'Người nhập kho',
            'Ghi chú nhập kho',
            'Ngày giao hàng',
            'Thời gian đóng hàng',
            'Biển số xe xuất',
            'Nhà xe vận chuyển xuất',
            'Tải trọng xe xuất (tấn)',
            'Khách hàng',
            'Ghi chú xuất kho',
            'Trạng thái Pallet'
        ];
    }

    public function map($row): array
    {
        return [
            $row->pallet_id,
            $row->plan_code,
            $row->receivingPlan?->vendor?->vendor_name ?? '',
            $row->crate?->crate_id ?? '',
            $row->crate_description,
            $row->crate_pcs,
            $row->crate_gross_weight,
            $row->crate_dimensions,
            $row->plan_date ? Carbon::parse($row->plan_date)->format('d/m/Y') : '',
            $row->arrival_date ? Carbon::parse($row->arrival_date)->format('H:i') : '',
            $row->receiving_license_plate,
            $row->receiving_transport_garage,
            $row->receiving_vehicle_capacity,
            $row->checkInBy?->name ?? '',
            $row->receiving_notes,
            $row->requested_date ? Carbon::parse($row->requested_date)->format('d/m/Y') : '',
            $row->lifting_time ? Carbon::parse($row->lifting_time)->format('H:i') : '',
            $row->shipping_license_plate,
            $row->shipping_transport_garage,
            $row->shipping_vehicle_capacity,
            $row->customer_name,
            $row->shipping_notes,
            $this->getPalletStatusLabel($row->pallet_status)
        ];
    }

    public function title(): string
    {
        return 'Báo cáo tổng hợp';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:W' => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // STT
            'B' => 20,  // Mã kế hoạch
            'C' => 25,  // Nhà cung cấp
            'D' => 15,  // Mã kiện hàng
            'E' => 30,  // Tên hàng
            'F' => 12,  // Số kiện
            'G' => 15,  // Trọng lượng
            'H' => 20,  // Kích thước
            'I' => 15,  // Ngày hàng đến
            'J' => 12,  // Giờ hạ hàng
            'K' => 15,  // Biển số xe nhập
            'L' => 25,  // Nhà xe nhập
            'M' => 15,  // Tải trọng nhập
            'N' => 20,  // Người nhập kho
            'O' => 30,  // Ghi chú nhập
            'P' => 15,  // Ngày giao hàng
            'Q' => 15,  // Thời gian đóng hàng
            'R' => 15,  // Biển số xe xuất
            'S' => 25,  // Nhà xe xuất
            'T' => 15,  // Tải trọng xuất
            'U' => 25,  // Khách hàng
            'V' => 30,  // Ghi chú xuất
            'W' => 20,  // Trạng thái
        ];
    }

    private function getPalletStatusLabel($status)
    {
        try {
            return PalletStatus::from($status)->getLabel();
        } catch (\ValueError $e) {
            // Fallback nếu status không hợp lệ
            return $status ?? 'Không xác định';
        }
    }
}
