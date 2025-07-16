<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Shipment;

class ShipmentInvoiceExport implements FromArray, WithHeadings, WithEvents
{
    protected $shipment;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment->load([
            'vehicle',
            'shippingRequest',
            'shipmentItems.crate',
            'shipmentItems.pallet',
            'shipmentItems.loadedBy',
        ]);
    }

    public function array(): array
    {
        $rows = [];
        // Các dòng detail
        $detailRows = [
            ['Mã vận chuyển', $this->shipment->shipment_code],
            ['Trạng thái', $this->shipment->status instanceof \App\Enums\ShipmentStatus ? $this->shipment->status->getLabel() : $this->shipment->status],
            ['Xe', $this->shipment->vehicle->vehicle_code ?? ''],
            ['Biển số', $this->shipment->vehicle->license_plate ?? ''],
            ['Tài xế', $this->shipment->vehicle->driver_name ?? ''],
            ['SĐT tài xế', $this->shipment->vehicle->driver_phone ?? ''],
            ['Tổng số thùng', $this->shipment->total_crates],
            ['Tổng số pallet', $this->shipment->total_pallets],
            ['Tổng số lượng', $this->shipment->total_pieces],
            ['Tổng khối lượng', $this->shipment->total_weight],
            ['Ghi chú', $this->shipment->notes ?? ''],
            ['Ngày tạo', $this->shipment->created_at],
            ['Ngày cập nhật', $this->shipment->updated_at],
        ];
        foreach ($detailRows as $row) {
            $rows[] = $row;
        }
        $emptyRows = 3;
        for ($i = 0; $i < $emptyRows; $i++) {
            $rows[] = array_fill(0, 7, null);
        }
        $headerRowIndex = count($rows) + 1; // Excel bắt đầu từ 1
        $rows[] = [
            'STT', 'Thùng hàng', 'Pallet', 'Số lượng', 'Ghi chú', 'Người xếp', 'Thời gian xếp'
        ];
        foreach ($this->shipment->shipmentItems as $i => $item) {
            $rows[] = [
                $i + 1,
                $item->crate->crate_id ?? '',
                $item->pallet->pallet_id ?? '',
                $item->quantity ?? '',
                $item->notes ?? '',
                $item->loadedBy->name ?? '',
                $item->loaded_at ?? '',
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Định nghĩa lại $detailRows để đếm số dòng detail động
                $detailRows = [
                    ['Mã vận chuyển', $this->shipment->shipment_code],
                    ['Trạng thái', $this->shipment->status instanceof \App\Enums\ShipmentStatus ? $this->shipment->status->getLabel() : $this->shipment->status],
                    ['Xe', $this->shipment->vehicle->vehicle_code ?? ''],
                    ['Biển số', $this->shipment->vehicle->license_plate ?? ''],
                    ['Tài xế', $this->shipment->vehicle->driver_name ?? ''],
                    ['SĐT tài xế', $this->shipment->vehicle->driver_phone ?? ''],
                    ['Tổng số thùng', $this->shipment->total_crates],
                    ['Tổng số pallet', $this->shipment->total_pallets],
                    ['Tổng số lượng', $this->shipment->total_pieces],
                    ['Tổng khối lượng', $this->shipment->total_weight],
                    ['Ghi chú', $this->shipment->notes ?? ''],
                    ['Ngày tạo', $this->shipment->created_at],
                    ['Ngày cập nhật', $this->shipment->updated_at],
                ];
                $detailRowsCount = count($detailRows);
                $emptyRows = 3;
                $headerRowIndex = count($detailRows) + $emptyRows + 1;
                $itemCount = $this->shipment->shipmentItems->count();
                $startRow = $headerRowIndex;
                $endRow = $startRow + $itemCount;
                // Vẽ border cho bảng danh sách (in đậm, rõ nét)
                $cellRange = "A{$startRow}:G{$endRow}";
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Đặt màu nền header danh sách (blue-500), chữ trắng, in đậm
                $headerRange = "A{$startRow}:G{$startRow}";
                $event->sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3B82F6'], // blue-500
                    ],
                ]);
                // In đậm label detail (cột A)
                $event->sheet->getStyle("A1:A{$detailRowsCount}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
                // Đặt lại width cột như bạn đã chỉnh
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);
                // Thiết lập cỡ chữ 16 cho toàn bộ sheet
                $event->sheet->getDelegate()->getStyle(
                    'A1:G' . $event->sheet->getDelegate()->getHighestRow()
                )->getFont()->setSize(16);
            },
        ];
    }
} 