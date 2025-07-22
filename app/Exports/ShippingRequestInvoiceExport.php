<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Shipment;
use App\Models\ShippingRequest;

class ShippingRequestInvoiceExport implements FromArray, WithHeadings, WithEvents
{
    protected $shippingRequest;

    public function __construct(ShippingRequest $shippingRequest)
    {
        $this->shippingRequest = $shippingRequest->load([
            'creator',
            'items.crate',
            'items.crate.pallet'
        ]);
    }

    public function array(): array
    {
        $rows = [];
        // Các dòng detail
        $detailRows = [
            ['', 'Mã yêu cầu', $this->shippingRequest->request_code],
            ['', 'Tên khách hàng', $this->shippingRequest->customer_name],
            ['', 'SĐT khách hàng', $this->shippingRequest->customer_contact],
            ['', 'Địa chỉ giao hàng', $this->shippingRequest->delivery_address],
            ['', 'Ngày yêu cầu', $this->shippingRequest->requested_date],
            ['', 'Biển số xe', $this->shippingRequest->license_plate],
            ['', 'Tài xế', $this->shippingRequest->driver_name],
            ['', 'SĐT tài xế', $this->shippingRequest->driver_phone],
            ['', 'Số seal', $this->shippingRequest->seal_number],
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
            'STT', 'Crate_ID', 'Pallet_ID', 'Số lượng', 'Trọng lượng', 'Ghi chú'
        ];
        foreach ($this->shippingRequest->items as $i => $item) {
            $crate = $item->crate;
            $pallet = $crate ? $crate->pallet : null;
            $rows[] = [
                $i + 1,
                $crate ? $crate->crate_id : '',
                $pallet ? $pallet->pallet_id : '',
                $crate->pieces ?? '',
                $crate->gross_weight ?? '',
                $item->notes ?? '',
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
                    ['', 'Mã yêu cầu', $this->shippingRequest->request_code],
                    ['', 'Tên khách hàng', $this->shippingRequest->customer_name],
                    ['', 'SĐT khách hàng', $this->shippingRequest->customer_contact],
                    ['', 'Địa chỉ giao hàng', $this->shippingRequest->delivery_address],
                    ['', 'Ngày yêu cầu', $this->shippingRequest->requested_date],
                    ['', 'Biển số xe', $this->shippingRequest->license_plate],
                    ['', 'Tài xế', $this->shippingRequest->driver_name],
                    ['', 'SĐT tài xế', $this->shippingRequest->driver_phone],
                    ['', 'Số seal', $this->shippingRequest->seal_number],
                ];
                $detailRowsCount = count($detailRows);
                $emptyRows = 3;
                $headerRowIndex = count($detailRows) + $emptyRows + 1;
                $itemCount = $this->shippingRequest->items->count();
                $startRow = $headerRowIndex;
                $endRow = $startRow + $itemCount;
                // Vẽ border cho bảng danh sách (in đậm, rõ nét)
                $cellRange = "A{$startRow}:F{$endRow}";
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Đặt màu nền header danh sách (blue-500), chữ trắng, in đậm
                $headerRange = "A{$startRow}:F{$startRow}";
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
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                // $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);

                // Thiết lập cỡ chữ 16 cho toàn bộ sheet
                $event->sheet->getDelegate()->getStyle(
                    'A1:F' . $event->sheet->getDelegate()->getHighestRow()
                )->getFont()->setSize(16);
            },
        ];
    }
} 